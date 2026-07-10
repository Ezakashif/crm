<?php

namespace App\Services\Csv;

use Illuminate\Http\UploadedFile;
use RuntimeException;

class CsvReader
{
    public const MAX_ROWS = 500;

    /**
     * @return array{headers: list<string>, rows: list<array{row: int, data: array<string, string>}>}
     */
    public function read(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            throw new RuntimeException('Unable to open the uploaded CSV file.');
        }

        try {
            $headerRow = fgetcsv($handle);

            if ($headerRow === false || $headerRow === [null] || $headerRow === []) {
                throw new RuntimeException('The CSV file is empty or missing a header row.');
            }

            $headers = array_map(fn ($header) => $this->normalizeHeader((string) $header), $headerRow);

            if (in_array('', $headers, true) || count($headers) !== count(array_unique($headers))) {
                throw new RuntimeException('The CSV header row is invalid or contains duplicate columns.');
            }

            $rows = [];
            $rowNumber = 1;

            while (($data = fgetcsv($handle)) !== false) {
                $rowNumber++;

                if ($this->rowIsEmpty($data)) {
                    continue;
                }

                if (count($rows) >= self::MAX_ROWS) {
                    throw new RuntimeException('CSV imports are limited to '.self::MAX_ROWS.' data rows.');
                }

                $assoc = [];
                foreach ($headers as $index => $header) {
                    $assoc[$header] = isset($data[$index]) ? trim((string) $data[$index]) : '';
                }

                $rows[] = [
                    'row' => $rowNumber,
                    'data' => $assoc,
                ];
            }

            return [
                'headers' => $headers,
                'rows' => $rows,
            ];
        } finally {
            fclose($handle);
        }
    }

    protected function normalizeHeader(string $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/', '', $header) ?? $header;
        $header = strtolower(trim($header));
        $header = str_replace([' ', '-'], '_', $header);

        return $header;
    }

    /**
     * @param  list<string|null>|false  $data
     */
    protected function rowIsEmpty(array|false $data): bool
    {
        if ($data === false) {
            return true;
        }

        foreach ($data as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }
}
