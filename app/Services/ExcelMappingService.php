<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelMappingService
{
    public function getHeaders(string $filePath): array
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($extension === 'csv') {
            $file = fopen($filePath, 'r');

            $headers = fgetcsv($file);

            fclose($file);

            return $headers ?: [];
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        return $sheet->rangeToArray(
            'A1:' . $sheet->getHighestColumn() . '1',
            null,
            true,
            true,
            false
        )[0];
    }

    public function getPreviewRows(
        string $filePath,
        int $rows = 5
    ): array {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($extension === 'csv') {
            $file = fopen($filePath, 'r');

            $preview = [];

            fgetcsv($file); // skip header

            while (($data = fgetcsv($file)) !== false) {
                $preview[] = $data;

                if (count($preview) >= $rows) {
                    break;
                }
                
            }

            fclose($file);

            return $preview;
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        return $sheet->rangeToArray(
            'A2:' . $sheet->getHighestColumn() . ($rows + 1),
            null,
            true,
            true,
            false
        );
    }
    
}