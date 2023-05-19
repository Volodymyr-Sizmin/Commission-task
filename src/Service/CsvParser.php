<?php
declare(strict_types = 1);

namespace PayX\CommissionTask\Service;

use Exception;

class CsvParser
{
    private string $filename;

    public function __construct(string $filename)
    {
        $this->filename = __DIR__ . '/' .$filename;
    }

    public function parseFile(): array
    {
        $csvFile = fopen($this->filename, 'r');
        if (!$csvFile) {
            throw new Exception("Failed to open input file.");
        }

        $data = [];
        while (($row = fgetcsv($csvFile)) !== false) {
            $data[] = $row;
        }

        fclose($csvFile);

        return $data;
    }
}
