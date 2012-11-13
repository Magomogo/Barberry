<?php
namespace Barberry\Filter;

class FilterCompositeTest extends \PHPUnit_Framework_TestCase {

    public function testImplementsFilterInterface() {
        $this->assertInstanceOf('Barberry\\Filter\\FilterInterface', $this->c());
    }

    public function testCallsAssignedFilter() {
        $filterMock = $this->filterMock(array('vars'), array('files'));
        $this->c($filterMock)->filter(array('vars'), array('files'));
    }

    public function testReturnsFilteredFileFromAssignedFilter() {
        $file = new \Barberry\PostedFile('bin', 'test.txt');

        $filterMock = $this->filterMock(array('vars'), array('files'), $file);
        $this->assertEquals($file, $this->c($filterMock)->filter(array('vars'), array('files')));
    }

    public function testCallsFiltersUntilReturnFilteredFile() {
        $vars = array('vars');
        $files = array('files');

        $filter1 = $this->filterMock($vars, $files);
        $filter2 = $this->filterMock($vars, $files);
        $filter3 = $this->filterMock($vars, $files, new \Barberry\PostedFile('binary', 'test_name.txt'));
        $filter4 = $this->getMock('Barberry\\Filter\\FilterInterface');
        $filter4->expects($this->never())->method('filter');

        $this->assertEquals(
            'test_name.txt',
            $this->c($filter1, $filter2, $filter3, $filter4)->filter($vars, $files)->filename
        );
    }

    private function c() {
        $args = func_get_args();

        if (!isset($args[0])) {
            $args[0] = $this->getMock('Barberry\\Filter\\FilterInterface');
        }
        $rc = new \ReflectionClass('Barberry\\Filter\\FilterComposite');
        return $rc->newInstanceArgs($args);
    }

    private function filterMock(array $expectedVars, array $expectedFiles, \Barberry\PostedFile $return = null) {
        $f = $this->getMock('Barberry\\Filter\\FilterInterface');
        $f->expects($this->once())
            ->method('filter')
            ->with($expectedVars, $expectedFiles)
            ->will($this->returnValue($return));

        return $f;
    }

}
