<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelMappingService
{
    /**
     * Get all headers from the uploaded file.
     */
    public function getHeaders(UploadedFile $file): array
    {
        $spreadsheet = IOFactory::load($file->getRealPath());

        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, false);

        return array_map(
            fn ($header) => trim((string) $header),
            $rows[0] ?? []
        );
    }

    /**
     * Get preview rows.
     */
    public function getPreviewRows(
        UploadedFile $file,
        int $limit = 5
    ): array {
        $spreadsheet = IOFactory::load($file->getRealPath());

        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, false);

        array_shift($rows);

        return array_slice($rows, 0, $limit);
    }
}