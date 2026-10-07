<?php

namespace App\Services;

use App\Mail\StudentAccountCredentials;
use App\Models\Import;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentImportService
{
    /**
     * Import students from an uploaded Excel/CSV master list.
     *
     * Expected columns (header row required, order does not matter):
     * Student Number | Full Name | Email | Mobile Number | Program | Year Level | Program Type | Block (optional)
     */
    public function import(
        string $filePath,
        int $importedById,
        array $mapping,
        ?string $originalFileName = null
    ): array {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        // First row is the header
        array_shift($rows);

        $successCount   = 0;
        $failedCount    = 0;
        $duplicateCount = 0;
        $archivedCount  = 0;
        $totalCount     = 0;

        foreach ($rows as $row) {
            // Skip fully blank rows
            if (
                collect($row)
                    ->filter(fn ($v) => trim((string) $v) !== '')
                    ->isEmpty()
            ) {
                continue;
            }

            $totalCount++;

            $studentNumber = trim((string) ($row[$mapping['student_number']] ?? ''));
            $fullName      = trim((string) ($row[$mapping['full_name']] ?? ''));
            $email         = trim((string) ($row[$mapping['email']] ?? ''));
            $mobileNumber  = $this->normalizeMobile((string) ($row[$mapping['mobile_number']] ?? ''));
            $program       = trim((string) ($row[$mapping['program']] ?? ''));
            $yearLevel     = trim((string) ($row[$mapping['year_level']] ?? ''));
            $programType   = trim((string) ($row[$mapping['program_type']] ?? ''));

            // Block is optional: only read it if the column was mapped
            $block = null;
            if (isset($mapping['block']) && $mapping['block'] !== '') {
                $block = strtoupper(trim((string) ($row[$mapping['block']] ?? '')));
                $block = $block !== '' ? $block : null;
            }

            // Normalize so filters/grouping don't split BSED / BSEd / bsed
            $program     = strtoupper($program);
            $programType = ucwords(strtolower($programType)); // "field study" -> "Field Study"

            if ($studentNumber === '' || $fullName === '' || $email === '') {
                $failedCount++;
                continue;
            }

            // Archived record (same student number or email): never recreate or overwrite it.
            // The admin must restore it from Student Records instead.
            $archivedMatch = Student::onlyArchived()
                ->where(function ($q) use ($studentNumber, $email) {
                    $q->where('student_number', $studentNumber)
                        ->orWhere('email', $email)
                        ->orWhereHas('user', fn ($u) => $u->where('email', $email));
                })
                ->exists();

            if ($archivedMatch) {
                $archivedCount++;
                continue;
            }

            // Existing student: skip, but backfill block if it is currently empty
            $existing = Student::where('student_number', $studentNumber)->first();

            if ($existing) {
                if ($block && blank($existing->block)) {
                    $existing->update(['block' => $block]);
                }

                $duplicateCount++;
                continue;
            }

            [$firstName, $middleName, $lastName] = $this->parseFullName($fullName);
            $password = $this->generatePassword($lastName, $studentNumber);

            // Students imported as "Internship" skip the Field Study stage, so
            // their Internship is unlocked for initial requirements review.
            // Everyone else stays locked until Field Study is completed.
            $internshipStatus = $programType === 'Internship'
                ? 'pending_review'
                : 'locked';

            try {
                DB::transaction(function () use (
                    $studentNumber, $firstName, $middleName, $lastName,
                    $email, $mobileNumber, $program, $programType, $yearLevel,
                    $block, $password, $internshipStatus
                ) {
                    $user = User::create([
                        'name'                 => trim("{$firstName} {$lastName}"),
                        'first_name'           => $firstName,
                        'middle_name'          => $middleName,
                        'last_name'            => $lastName,
                        'email'                => $email,
                        'password'             => Hash::make($password),
                        'role'                 => 'student',
                        'status'               => true,
                        'mobile_number'        => $mobileNumber,
                        'must_change_password' => false,
                    ]);

                    $student = Student::create([
                        'user_id'             => $user->id,
                        'student_number'      => $studentNumber,
                        'reference_number'    => $studentNumber,
                        'email'               => $email,
                        'mobile_number'       => $mobileNumber,
                        'first_name'          => $firstName,
                        'middle_name'         => $middleName,
                        'last_name'           => $lastName,
                        'program'             => $program,
                        'program_type'        => $programType,
                        'year_level'          => $yearLevel,
                        'block'               => $block,
                        'field_study_hours'   => 0,
                        'internship_hours'    => 0,
                        'is_eligible'         => false,
                        'internship_status'   => $internshipStatus,
                        'status'              => 'inactive',
                        'is_imported'         => true,
                        'is_late_enrollee'    => false,
                        'registration_status' => 'accepted',
                    ]);

                    // Send credentials. A mail failure doesn't roll back account creation.
                    try {
                        Mail::to($email)->queue(new StudentAccountCredentials($student, $password));
                    } catch (\Throwable $mailException) {
                        Log::warning("StudentImport: failed to email credentials to {$email}: {$mailException->getMessage()}");
                    }
                });

                $successCount++;
            } catch (\Throwable $e) {
                Log::error("StudentImport: failed to import row for student_number {$studentNumber}: {$e->getMessage()}");
                $failedCount++;
            }
        }

        Import::create([
            'file_name'          => $originalFileName ?? basename($filePath),
            'imported_by'        => $importedById,
            'total_records'      => $totalCount,
            'successful_records' => $successCount,
            'failed_records'     => $failedCount,
            'duplicate_records'  => $duplicateCount,
        ]);

        return [
            'total'     => $totalCount,
            'success'   => $successCount,
            'failed'    => $failedCount,
            'duplicate' => $duplicateCount,
            'archived'  => $archivedCount,
        ];
    }

    /**
     * Parse "LASTNAME, FIRSTNAME MIDDLE" into [first, middle, last].
     */
    private function parseFullName(string $fullName): array
    {
        if (! str_contains($fullName, ',')) {
            // Fallback: no comma, treat whole string as first name
            return [ucwords(strtolower($fullName)), '', ''];
        }

        [$lastPart, $firstPart] = array_map('trim', explode(',', $fullName, 2));

        $lastName = ucwords(strtolower($lastPart));

        $firstTokens = preg_split('/\s+/', trim($firstPart));
        $firstName   = ucwords(strtolower(array_shift($firstTokens) ?? ''));
        $middleName  = ucwords(strtolower(implode(' ', $firstTokens)));

        return [$firstName, $middleName, $lastName];
    }

    /**
     * Restore the leading zero Excel drops from PH mobile numbers.
     * 9176433012 -> 09176433012, +639176433012 -> 09176433012
     */
    private function normalizeMobile(string $mobile): string
    {
        $digits = preg_replace('/\D+/', '', $mobile);

        if ($digits === '') {
            return '';
        }

        if (strlen($digits) === 12 && str_starts_with($digits, '63')) {
            return '0' . substr($digits, 2);
        }

        if (strlen($digits) === 10 && str_starts_with($digits, '9')) {
            return '0' . $digits;
        }

        return $digits;
    }

    /**
     * Password = uppercase last name + last 4 digits of student number.
     * e.g. Costales / 20231168 -> COSTALES1168
     */
    private function generatePassword(string $lastName, string $studentNumber): string
    {
        $lastFour = substr($studentNumber, -4);

        return strtoupper($lastName) . $lastFour;
    }
}