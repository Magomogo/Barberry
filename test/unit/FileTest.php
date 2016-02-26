<?php
/**
 * @author lion
 */

namespace Barberry;

use Barberry\Storage\File;

class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testWriteException()
    {
        $this->setExpectedException('Barberry\Storage\WriteException');

        $file = new File('/');
        $file->save('123');
    }
}