<?php
namespace Barberry;
use Barberry\Response;
use Barberry\Storage;
use Barberry\Direction;

class Controller implements Controller\ControllerInterface {
    /**
     * @var Direction\Factory
     */
    private $directionFactory;

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
     * @param Direction\Factory $directionFactory
     */
    public function __construct(Request $request, Storage\StorageInterface $storage, Direction\Factory $directionFactory) {
        $this->request = $request;
        $this->storage = $storage;
        $this->directionFactory = $directionFactory;
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
            $contentType = ContentType::byFilename($this->request->tmpName);
        } catch (ContentType\Exception $e) {
            throw new Controller\NotImplementedException($e->getMessage());
        }

        return self::response(
            ContentType::json(),
            json_encode(
                array(
                    'id' => $this->storage->save($this->request->bin),
                    'contentType' => (string) $contentType,
                    'ext' => $contentType->standardExtension(),
                    'length' => strlen($this->request->bin),
                    'filename' => $this->request->postedFilename,
                    'md5' => $this->request->postedMd5
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

        try {
            return self::response(
                $this->request->contentType,
                $this->directionFactory->direction(
                    ContentType::byString($bin),
                    $this->request->contentType,
                    $this->request->commandString
                )->convert($bin)
            );
        } catch (Plugin\NotAvailableException $e) {
            throw new Controller\NotFoundException;
        } catch (Exception\AmbiguousPluginCommand $e) {
            throw new Controller\NotFoundException;
        } catch (Exception\ConversionNotPossible $e) {
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
