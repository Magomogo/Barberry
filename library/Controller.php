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
        $this->outputContentType = $outputContentType ?: ContentType::createByExtention('json');
    }

    public function POST() {
        return $this->response(
            json_encode(
                array(
                    'id' => $this->storage->save('')
                )
            )
        );
    }

    public function GET() {
        $bin = $this->storage->getById($this->entityId);
/*        $detector = new ConvertDirection(new ConverterFactory(), $bin, $this->outputContentType);
        $converter = $detector->createConverter();*/

        return $this->response(''/*$converter->convert($bin)*/);
    }

    public function DELETE() {

        return $this->response('{}');
    }

//--------------------------------------------------------------------------------------------------

    private function response($body = '') {
        return new Response($this->outputContentType, $body);
    }
}
