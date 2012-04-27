<?php

class Direction_Composer {

    private $directionDirectory;

    public function __construct($directionDirectory) {
        $this->directionDirectory = $directionDirectory;
    }

    public function writeClassDeclaration(ContentType $source,
                                          ContentType $destination,
                                          $newConverterPhp,
                                          $newCommandPhp = null) {
        file_put_contents(
            $this->directionDirectory . self::directionName($source, $destination) . '.php',
            self::classCode(
                self::directionName($source, $destination), $newConverterPhp, $newCommandPhp
            )
        );
    }

//--------------------------------------------------------------------------------------------------

    private static function directionName(ContentType $source, ContentType $destination) {
        return ucfirst($source->standartExtention())
                . 'To' . ucfirst($destination->standartExtention());
    }

    private static function classCode($className, $newConverterPhp, $newCommandPhp = null) {
        $converterInitialization = '$this->converter = ' . rtrim($newConverterPhp, ';') . ';';
        $commandInitialization = null;
        if (!is_null($newCommandPhp)) {
            $newCommandPhp = rtrim($newCommandPhp, ';') . ';';
            $commandInitialization = <<<PHP
\$this->command = $newCommandPhp
        \$this->command->configure(\$commandString);
        if (!\$this->command->conforms(\$commandString)) {
            throw new Plugin_AmbiguousCommandException(\$commandString);
        }
PHP;
        }

        return <<<PHP
<?php
class {$className}Direction extends Direction_Abstract {
    protected function init(\$commandString = null) {
        $converterInitialization
        $commandInitialization
    }
}
PHP;
    }
}
