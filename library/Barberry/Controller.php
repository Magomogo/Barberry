<?php
namespace Barberry;
use Barberry\Response;
use Barberry\Storage;

class Controller implements Controller\ControllerInterface {
    /**
     * @var Storage\StorageInterface
     */
    private $storage;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request $request
     * @param Storage\StorageInterface $storage
     */
    public function __construct(Request $request, Storage\StorageInterface $storage) {
        $this->request = $request;
        $this->storage = $storage;
    }

    /**
     * @return Response
     * @throws Controller\NullPostException
     * @throws Controller\NotImplementedException
     */
    public function POST() {
        if (!strlen($this->request->bin)) {
            throw new Controller\NullPostException;
        }

        try {
            $contentType = ContentType::byString($this->request->bin);
        } catch (ContentType\Exception $e) {
            throw new Controller\NotImplementedException($e->getMessage());
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
     * @throws Controller\NotFoundException
     */
    public function GET() {
        try {
            $bin = $this->storage->getById($this->request->id);
        } catch (Storage\NotFoundException $e) {
            throw new Controller\NotFoundException;
        }

        if (is_null($this->request->contentType)) {
            $this->request->defineContentType(ContentType::byString($bin));
        }

        $directionFactory = new Direction\Factory($bin, $this->request->contentType);

        try {
            return self::response(
                $this->request->contentType,
                $directionFactory->direction($this->request->commandString)->convert($bin)
            );
        } catch (Plugin\NotAvailableException $e) {
            throw new Controller\NotFoundException;
        } catch (Exception\AmbiguousPluginCommand $e) {
            throw new Controller\NotFoundException;
        }
    }

    /**
     * @return Response
     * @throws Controller\NotFoundException
     */
    public function DELETE() {
        try {
            $this->storage->delete($this->request->id);
        } catch (Storage\NotFoundException $e) {
            throw new Controller\NotFoundException;
        }
        return self::response(ContentType::json(), '{}');
    }

    public function __call($name, $args) {
        throw new Controller\NotFoundException;
    }

//--------------------------------------------------------------------------------------------------

    private static function response($contentType, $body, $code = 200) {
        return new Response($contentType, $body, $code);
    }

}
