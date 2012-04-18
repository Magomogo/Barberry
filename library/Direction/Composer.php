<?php

class Direction_Composer {

    private $directionDirectory;

    public function __construct($directionDirectory) {
        $this->directionDirectory = $directionDirectory;
    }

    public function writeClassDeclaration(ContentType $source, ContentType $destination, $phpCode) {
        file_put_contents(
            $this->directionDirectory . self::directionName($source, $destination) . '.php',
            self::classCode(self::directionName($source, $destination), $phpCode)
        );
    }

//--------------------------------------------------------------------------------------------------

    private static function directionName(ContentType $source, ContentType $destination) {
        return ucfirst($source->standartExtention())
                . 'To' . ucfirst($destination->standartExtention());
    }

    private static function classCode($className, $converterInstantiatingPhp) {
        return <<<PHP
<?php
class {$className}Direction extends Direction_Abstract {
    public function __construct(Plugin_Interface_Command \$command = null) {
        \$this->converter = $converterInstantiatingPhp
    }
}
PHP;
    }
}
