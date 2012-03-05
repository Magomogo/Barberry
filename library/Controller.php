<?php

class Controller implements Controller_Interface {

    /**
     * @var null|string
     */
    private $docId;

    /**
     * @var null|ContentType
     */
    private $outputContentType;

    /**
     * @var string
     */
    private $postedFile;

    /**
     * @var Storage_Interface
     */
    private $storage;

    public function __construct(Storage_Interface $storage) {
        $this->storage = $storage;
    }

    /**
     * @param $docId
     * @param ContentType|null $outputContentType
     * @param null|string $bin
     * @return Controller
     */
    public function requestDispatched($docId, ContentType $outputContentType = null, $bin = null) {
        $this->docId = $docId;
        $this->outputContentType = $outputContentType;
        $this->postedFile = $bin;
        return $this;
    }

    public function POST() {
        if (!strlen($this->postedFile)) {
            throw new Controller_NullPostException;
        }

        return self::response(
            ContentType::json(),
            json_encode(
                array(
                    'id' => $this->storage->save($this->postedFile)
                )
            )
        );
    }

    public function GET() {
        $bin = $this->storage->getById($this->docId);
        $direction = new ConvertDirection($bin, $this->outputContentType);

        return self::response(
            $this->outputContentType,
            $direction->initConverter(new Converter_Factory())->convert($bin)
        );
    }

    /**
     * @TODO implement
     * @return Response
     */
    public function DELETE() {
        return self::response(ContentType::json(), '{}');
    }

//--------------------------------------------------------------------------------------------------

    private static function response($contentType, $body) {
        return new Response($contentType, $body);
    }

}
