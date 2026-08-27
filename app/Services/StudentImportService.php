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
use App\Services\ExcelMappingService;
class StudentImportService
{
    /**
     * Import students from an uploaded Excel/CSV master list.
     *
     * Expected columns (in order, header row required):
     * Student Number | Full Name | Email | Mobile Number | Program | Year Level | Program Type
     */
  public function import(
    string $filePath,
    int $importedById,
    array $mapping,
    ?string $originalFileName = null
): array
{
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, false);

    // First row is the header
    array_shift($rows);

    $successCount = 0;
    $failedCount = 0;
    $duplicateCount = 0;
    $totalCount = 0;

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
        $mobileNumber  = trim((string) ($row[$mapping['mobile_number']] ?? ''));
        $program       = trim((string) ($row[$mapping['program']] ?? ''));
        $yearLevel     = trim((string) ($row[$mapping['year_level']] ?? ''));
        $programType   = trim((string) ($row[$mapping['program_type']] ?? ''));

            if ($studentNumber === '' || $fullName === '' || $email === '') {
                $failedCount++;
                continue;
            }

            // Skip duplicates (existing student_number)
            if (Student::where('student_number', $studentNumber)->exists()) {
                $duplicateCount++;
                continue;
            }

            [$firstName, $middleName, $lastName] = $this->parseFullName($fullName);
            $password = $this->generatePassword($lastName, $studentNumber);

            try {
                DB::transaction(function () use (
                    $studentNumber, $firstName, $middleName, $lastName,
                    $email, $mobileNumber, $program, $programType, $yearLevel,
                    $password
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
                    'user_id' => $user->id,
                    'student_number' => $studentNumber,
                    'reference_number' => $studentNumber,
                    'email' => $email,
                    'mobile_number' => $mobileNumber,
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'program' => $program,
                    'program_type' => $programType,
                    'year_level' => $yearLevel,
                    'field_study_hours' => 0,
                    'internship_hours' => 0,
                    'is_eligible' => false,
                    'status' => 'inactive',
                    'is_imported' => true,
                    'is_late_enrollee' => false,
                    'registration_status' => 'accepted',
]);

                    // Send credentials — failure to email doesn't roll back account creation
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

        // Persist the import record. The `imports` table only tracks
        // total/successful/failed, so duplicates are folded into failed
        // for storage (total = successful + failed) while still being
        // reported separately in the returned summary below.
Import::create([
    'file_name' => $originalFileName ?? basename($filePath),
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
        ];
    }

    /**
     * Parse "LASTNAME, FIRSTNAME MIDDLE" into [first, middle, last].
     */
    private function parseFullName(string $fullName): array
    {
        if (! str_contains($fullName, ',')) {
            // Fallback: no comma — treat whole string as first name
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
     * Password = uppercase last name + last 4 digits of student number.
     * e.g. Costales / 20231168 → COSTALES1168
     */
    private function generatePassword(string $lastName, string $studentNumber): string
    {
        $lastFour = substr($studentNumber, -4);

        return strtoupper($lastName) . $lastFour;
    }
}