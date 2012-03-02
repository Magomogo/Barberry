<?php

class Controller {

    /**
     * @var null|string
     */
    private $entityId;

    /**
     * @var null|ContentType
     */
    private $outputContentType;

    /**
     * @var Storage_Interface
     */
    private $storage;

    public function __construct(Storage_Interface $storage,
                                $entityId = null,
                                ContentType $outputContentType = null) {
        $this->entityId = $entityId;
        $this->storage = $storage;
        $this->outputContentType = $outputContentType;
    }

    public function POST() {
        return self::response(
            ContentType::createByExtention('json'),
            json_encode(
                array(
                    'id' => $this->storage->save('')
                )
            )
        );
    }

    public function GET() {
        $bin = $this->storage->getById($this->entityId);
        $direction = new ConvertDirection($bin, $this->outputContentType);

        return self::response(
            $this->outputContentType,
            $direction->initConverter(new Converter_Factory())->convert($bin)
        );
    }

    public function DELETE() {
        return self::response(
            ContentType::createByExtention('json'),
            '{}'
        );
    }

//--------------------------------------------------------------------------------------------------

    private static function response($contentType, $body) {
        return new Response($contentType, $body);
    }
}
