<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Import;
use App\Services\ExcelMappingService;
use App\Services\StudentImportService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentImportController extends Controller
{
    public function index()
    {
        $imports = Import::latest()->paginate(10);

        $totalRecords = Import::sum('total_records');
        $totalSuccessful = Import::sum('successful_records');
        $totalFailed = Import::sum('failed_records');

        return view(
            'coordinator.students.import',
            compact(
                'imports',
                'totalRecords',
                'totalSuccessful',
                'totalFailed'
            )
        );
    }

    /**
     * Upload file and display mapping screen.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'mimes:xlsx,xls,csv',
                'max:5120',
            ],
        ]);

        $uploadedFile = $request->file('file');

        $mappingService = new ExcelMappingService();

        $headers = $mappingService->getHeaders(
            $uploadedFile->getRealPath()
        );

        $previewRows = $mappingService->getPreviewRows(
            $uploadedFile->getRealPath()
        );

        // Proper total row count
        $extension = strtolower(
            $uploadedFile->getClientOriginalExtension()
        );

        if ($extension === 'csv') {
            $totalRows = max(
                count(file($uploadedFile->getRealPath())) - 1,
                0
            );
        } else {
            $spreadsheet = IOFactory::load(
                $uploadedFile->getRealPath()
            );

            $sheet = $spreadsheet->getActiveSheet();

            $totalRows = max(
                $sheet->getHighestRow() - 1,
                0
            );
        }

        $filePath = $uploadedFile->store('imports');

        $originalFileName = $uploadedFile->getClientOriginalName();

        return view(
            'coordinator.students.mapping',
            [
                'headers' => $headers,
                'previewRows' => $previewRows,
                'filePath' => $filePath,
                'totalRows' => $totalRows,
                'originalFileName' => $originalFileName, // Correctly bound clean filename key
            ]
        );
    }

    /**
     * Process import after coordinator maps columns.
     */
    public function process(Request $request)
    {
        $request->validate([
            'file_path' => 'required',
            'original_file_name' => 'required',
            'mapping' => 'required|array',
        ]);

        $fullPath = storage_path(
            'app/private/' . $request->file_path
        );

        $service = new StudentImportService();

        $result = $service->import(
            $fullPath,
            auth()->id(),
            $request->mapping,
            $request->original_file_name
        );

        if ($result['success'] > 0) {
            $message = "{$result['success']} students imported successfully.";
        } elseif (
            $result['duplicate'] > 0 &&
            $result['failed'] == 0
        ) {
            $message = "No new students were imported. {$result['duplicate']} duplicate records were skipped.";
        } else {
            $message = "Import completed with errors.";
        }

        return redirect()
            ->route('coordinator.students.import')
            ->with('success', $message)
            ->with('importSummary', $result);
    }

    /**
     * Download CSV template.
     */
    public function downloadTemplate()
    {
        $fileName = 'student_master_list_template.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $columns = [
            'Student Number',
            'Full Name',
            'Email',
            'Mobile Number',
            'Program',
            'Year Level',
            'Program Type',
        ];

        $sampleRows = [
            [
                '20231168',
                'COSTALES, HARVEY M',
                'harvey@gmail.com',
                '09123456789',
                'BPED',
                '3',
                'Field Study',
            ],
            [
                '20231169',
                'DELA CRUZ, JUAN P',
                'juan@gmail.com',
                '09123456788',
                'BPED',
                '3',
                'Internship',
            ],
        ];

        $callback = function () use (
            $columns,
            $sampleRows
        ) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, $columns);

            foreach ($sampleRows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        };

        return response()->stream(
            $callback,
            200,
            $headers
        );
    }
}