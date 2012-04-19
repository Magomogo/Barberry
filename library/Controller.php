<?php

class Controller implements Controller_Interface {
    /**
     * @var Storage_Interface
     */
    private $storage;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request $request
     * @param Storage_Interface $storage
     */
    public function __construct(Request $request, Storage_Interface $storage) {
        $this->request = $request;
        $this->storage = $storage;
    }

    /**
     * @return Response
     * @throws Controller_NullPostException
     */
    public function POST() {
        if (!strlen($this->request->bin)) {
            throw new Controller_NullPostException;
        }

        try {
            $contentType = ContentType::byString($this->request->bin);
        } catch (ContentType_Exception $e) {
            throw new Controller_NotImplementedException($e->getMessage());
        }

        return self::response(
            ContentType::json(),
            json_encode(
                array(
                    'id' => $this->storage->save($this->request->bin),
                    'contentType' => strval($contentType),
                    'ext' => $contentType->standartExtention(),
                    'length' => strlen($this->request->bin),
                    'filename' => $this->request->postedFilename
                )
            ),
            201
        );
    }

    /**
     * @return Response
     * @throws Controller_NotFoundException
     */
    public function GET() {
        try {
            $bin = $this->storage->getById($this->request->id);
        } catch (Storage_NotFoundException $e) {
            throw new Controller_NotFoundException;
        }

        $directionFactory = new Direction_Factory($bin, $this->request->contentType);

        try {
            return self::response(
                $this->request->contentType,
                $directionFactory->direction($this->request->commandString)->convert($bin)
            );
        } catch (Plugin_NotAvailableException $e) {
            throw new Controller_NotFoundException;
        }
    }

    /**
     * @return Response
     * @throws Controller_NotFoundException
     */
    public function DELETE() {
        try {
            $this->storage->delete($this->request->id);
        } catch (Storage_NotFoundException $e) {
            throw new Controller_NotFoundException;
        }
        return self::response(ContentType::json(), '{}');
    }

    public function __call($name, $args) {
        throw new Controller_NotFoundException;
    }

//--------------------------------------------------------------------------------------------------

    private static function response($contentType, $body, $code = 200) {
        return new Response($contentType, $body, $code);
    }

}
