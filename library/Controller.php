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

    /**
     * @param Storage_Interface $storage
     */
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

    /**
     * @return Response
     * @throws Controller_NullPostException
     */
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

    /**
     * @return Response
     * @throws Controller_NotFoundException
     */
    public function GET() {
        try {
            $bin = $this->storage->getById($this->docId);
        } catch (Storage_NotFoundException $e) {
            throw new Controller_NotFoundException;
        }

        $directionFactory = new Direction_Factory($bin, $this->outputContentType);

        try {
            return self::response(
                $this->outputContentType,
                $directionFactory->direction()->convert($bin)
            );
        } catch (Plugin_NotAvailableException $e) {
            throw new Controller_NotFoundException;
        }
    }

    /**
     * @TODO implement
     * @return Response
     * @throws Controller_NotFoundException
     */
    public function DELETE() {
        return self::response(ContentType::json(), '{}');
    }

    public function __call($name, $args) {
        throw new Controller_NotFoundException;
    }

//--------------------------------------------------------------------------------------------------

    private static function response($contentType, $body) {
        return new Response($contentType, $body);
    }

}
