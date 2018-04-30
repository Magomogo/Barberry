<?php
namespace Barberry\Filter;
use Barberry\PostedFile\Collection;

class FilterCompositeTest extends \PHPUnit_Framework_TestCase {

    public function testImplementsFilterInterface() {
        $this->assertInstanceOf('Barberry\\Filter\\FilterInterface', $this->c());
    }

    public function testCallsAssignedFilter() {
        $files = new Collection();
        $files['file'] = new \Barberry\PostedFile('test', 'test.txt');

        $filterMock = $this->filterMock($files, array('vars'));
        $this->c($filterMock)->filter($files, array('vars'));
    }

    public function testCallsAllFilters() {
        $vars = array('vars');

        $files = new Collection();
        $files['file'] = new \Barberry\PostedFile('test', 'test.txt');

        $filter1 = $this->filterMock($files, $vars);
        $filter2 = $this->filterMock(
            $files, $vars,
            function ($files, $vars) {
                $files['file'] = new \Barberry\PostedFile('dsdgdfg', 'test_name.txt');
            }
        );
        $filter3 = $this->filterMock($files, $vars);

        $this->c($filter1, $filter2, $filter3)->filter($files, $vars);
        $this->assertEquals('test_name.txt', $files['file']->filename);
    }

    private function c() {
        $args = func_get_args();

        if (!isset($args[0])) {
            $args[0] = $this->createMock('Barberry\\Filter\\FilterInterface');
        }
        $rc = new \ReflectionClass('Barberry\\Filter\\FilterComposite');
        return $rc->newInstanceArgs($args);
    }

    private function filterMock(Collection $expectedFiles, array $expectedVars, $callback = null) {
        $f = $this->createMock('Barberry\\Filter\\FilterInterface');
        $w = $f->expects($this->once())->method('filter')->with($expectedFiles, $expectedVars);

        if (is_callable($callback)) {
            $w->will($this->returnCallback($callback));
        }

        return $f;
    }

}
