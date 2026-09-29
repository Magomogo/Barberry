<?php

namespace Barberry;

use PHPUnit\Framework\TestCase;

class ContentTypeDetectorTest extends TestCase
{
    public function testRecognizesUtf16LeCsvWithoutBom(): void
    {
        $content = iconv('UTF-8', 'UTF-16LE', "article;quantity\nTomato;2\nGarlic;4\n");

        self::assertSame('text/csv', (string) (new ContentTypeDetector())->detect($content));
    }

    public function testRecognizesUtf16LeCsvFromFileWithoutBom(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'barberry-csv-');
        file_put_contents($path, iconv('UTF-8', 'UTF-16LE', "article;quantity\nTomato;2\nGarlic;4\n"));

        try {
            self::assertSame('text/csv', (string) (new ContentTypeDetector())->detectFile($path));
        } finally {
            unlink($path);
        }
    }

    public function testKeepsUnrecognizedBinaryContentGeneric(): void
    {
        self::assertSame('application/octet-stream', (string) (new ContentTypeDetector())->detect("\x00\xFF\x01\xFE"));
    }

}
