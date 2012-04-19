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

        $contentType = ContentType::byString($this->request->bin);

        return self::response(
            ContentType::json(),
            json_encode(
                array(
                    'id' => $this->storage->save($this->request->bin),
                    'contentType' => strval($contentType),
                    'ext' => $contentType->standartExtention(),
                    'length' => strlen($this->request->bin),
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
