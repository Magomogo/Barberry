<?php

namespace Barberry\Filter;

use Barberry\PostedFile;
use Barberry\PostedFile\Collection;
use GuzzleHttp\Psr7\UploadedFile;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\TestCase;

class FilterCompositeTest extends TestCase
{

    public function testImplementsFilterInterface(): void
    {
        self::assertInstanceOf(FilterInterface::class, $this->c());
    }

    public function testCallsAssignedFilter(): void
    {
        $files = new Collection();
        $files['file'] = self::postedFile();

        $filterMock = $this->filterMock($files, array('vars'));
        $this->c($filterMock)->filter($files, array('vars'));
    }

    public function testCallsAllFilters(): void
    {
        $vars = ['vars'];

        $files = new Collection();
        $files['file'] = self::postedFile();

        $filter1 = $this->filterMock($files, $vars);
        $filter2 = $this->filterMock(
            $files, $vars,
            function ($files, $vars) {
                $files['file'] = self::postedFile();
            }
        );
        $filter3 = $this->filterMock($files, $vars);

        $this->c($filter1, $filter2, $filter3)->filter($files, $vars);
        $this->assertEquals('test_name.txt', $files['file']->uploadedFile->getClientFilename());
    }

    private function c()
    {
        $args = func_get_args();

        if (!isset($args[0])) {
            $args[0] = $this->createMock('Barberry\\Filter\\FilterInterface');
        }
        $rc = new \ReflectionClass('Barberry\\Filter\\FilterComposite');
        return $rc->newInstanceArgs($args);
    }

    private function filterMock(Collection $expectedFiles, array $expectedVars, $callback = null)
    {
        $f = $this->createMock('Barberry\\Filter\\FilterInterface');
        $w = $f->expects($this->once())->method('filter')->with($expectedFiles, $expectedVars);

        if (is_callable($callback)) {
            $w->willReturnCallback($callback);
        }

        return $f;
    }

    private static function postedFile(): PostedFile
    {
        return new PostedFile(
            new UploadedFile(Utils::streamFor('some text'), 10, UPLOAD_ERR_OK, 'test_name.txt'),
            '/tmp/asD6yhq'
        );
    }
}
