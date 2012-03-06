<?php

class Storage_FileTest extends PHPUnit_Framework_TestCase {

    private $var_path;

    protected function setUp() {
        $this->var_path = dirname(dirname(dirname(dirname(__FILE__)))).'/var/';
    }

    public function testIsDirectoryVarExistAndWriteable() {
        $this->assertTrue(is_dir($this->var_path));
        $this->assertEquals('0755', substr(decoct(fileperms($this->var_path) ), 1));
    }

    public function testIsFileSavedInFileSystem() {
        $id = $this->storage()->save(Test_Data::gif1x1());
        $expectedPath = $this->var_path.$id;

        $this->assertEquals(file_get_contents($expectedPath), Test_Data::gif1x1());

        # clean
        unlink($expectedPath);
    }

    public function testIsFileReturnById() {
        $id = $this->storage()->save(Test_Data::gif1x1());
        $expectedPath = $this->var_path.$id;

        $this->assertEquals($this->storage()->getById($id), Test_Data::gif1x1());

        # clean
        unlink($expectedPath);
    }

    public function testIsFileDeletedById() {
        $id = $this->storage()->save(Test_Data::gif1x1());
        $expectedPath = $this->var_path.$id;

        $this->storage()->delete($id);

        $this->assertFalse(file_exists($expectedPath));
    }

//--------------------------------------------------------------------------------------------------

    private function storage() {
        return new Storage_File($this->var_path);
    }
}