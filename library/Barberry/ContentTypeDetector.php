<?php

namespace Barberry;

class ContentTypeDetector
{
    public const SAMPLE_SIZE = 65536;

    public function detect(string $content): ContentType
    {
        return $this->isUtf16Csv($content) ? ContentType::csv() : ContentType::bin();
    }

    public function detectFile(string $path): ContentType
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return ContentType::bin();
        }

        try {
            $sample = fread($handle, self::SAMPLE_SIZE);
        } finally {
            fclose($handle);
        }

        return $sample !== false ? $this->detect($sample) : ContentType::bin();
    }

    private function isUtf16Csv(string $content): bool
    {
        $encoding = $this->detectUtf16Encoding($content);
        if ($encoding === null) {
            return false;
        }

        $text = iconv($encoding, 'UTF-8//IGNORE', $content);
        if ($text === false || strpos($text, "\0") !== false) {
            return false;
        }

        return $this->hasCsvStructure($text);
    }

    private function detectUtf16Encoding(string $content): ?string
    {
        if (substr($content, 0, 2) === "\xFF\xFE") {
            return 'UTF-16LE';
        }

        if (substr($content, 0, 2) === "\xFE\xFF") {
            return 'UTF-16BE';
        }

        if (strlen($content) < 16) {
            return null;
        }

        $evenNuls = 0;
        $oddNuls = 0;
        $pairs = intdiv(strlen($content), 2);
        for ($index = 0; $index < $pairs * 2; $index += 2) {
            $evenNuls += $content[$index] === "\0" ? 1 : 0;
            $oddNuls += $content[$index + 1] === "\0" ? 1 : 0;
        }

        if ($oddNuls / $pairs > 0.3 && $evenNuls / $pairs < 0.05) {
            return 'UTF-16LE';
        }

        if ($evenNuls / $pairs > 0.3 && $oddNuls / $pairs < 0.05) {
            return 'UTF-16BE';
        }

        return null;
    }

    private function hasCsvStructure(string $text): bool
    {
        $rows = preg_split('/\r\n|\r|\n/', $text);
        $rows = array_values(array_filter($rows, static function (string $row): bool {
            return trim($row) !== '';
        }));

        if (count($rows) < 2) {
            return false;
        }

        foreach ([';', ',', "\t"] as $delimiter) {
            $fieldCount = null;
            $validRows = 0;
            foreach (array_slice($rows, 0, 10) as $row) {
                $fields = str_getcsv($row, $delimiter, '"', '');
                if (count($fields) < 2) {
                    break;
                }

                if ($fieldCount === null) {
                    $fieldCount = count($fields);
                }

                if ($fieldCount !== count($fields)) {
                    break;
                }

                $validRows++;
            }

            if ($validRows >= 2) {
                return true;
            }
        }

        return false;
    }
}
