<?php

namespace Barberry;

use Barberry\Plugin\NullPlugin;
use Symfony\Component\HttpFoundation;
use Barberry\Storage;
use Barberry\Direction;

class Controller implements Controller\ControllerInterface
{
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
    public function __construct(Request $request, Storage\StorageInterface $storage, Direction\Factory $directionFactory)
    {
        $this->request = $request;
        $this->storage = $storage;
        $this->directionFactory = $directionFactory;
    }

    /**
     * @return HttpFoundation\Response
     * @throws Controller\NullPostException
     * @throws Controller\NotImplementedException
     */
    public function post(): HttpFoundation\Response
    {
        if (
            is_null($this->request->postedFile) ||
            $this->request->postedFile->uploadedFile->getStream()->getSize() === 0
        ) {
            throw new Controller\NullPostException;
        }

        try {
            $contentType = ContentType::byFilename($this->request->postedFile->tmpName);
        } catch (ContentType\Exception $e) {
            throw new Controller\NotImplementedException($e->getMessage());
        }

        $uploadedFile = $this->request->postedFile->uploadedFile;
        $fileMd5 = md5_file($this->request->postedFile->tmpName);

        return self::jsonResponse(
            [
                'id' => $this->storage->save($uploadedFile),
                'contentType' => (string) $contentType,
                'ext' => $contentType->standardExtension(),
                'length' => $uploadedFile->getSize(),
                'filename' => $uploadedFile->getClientFilename(),
                'md5' => $fileMd5
            ],
            HttpFoundation\Response::HTTP_CREATED
        );
    }

    /**
     * @return HttpFoundation\Response
     * @throws Controller\NotFoundException
     * @throws ContentType\Exception
     */
    public function get(): HttpFoundation\Response
    {
        try {
            $stream = $this->storage->getById($this->request->id);
        } catch (Storage\NotFoundException $e) {
            throw new Controller\NotFoundException;
        }

        $contentType = $this->storage->getContentTypeById($this->request->id);

        if (is_null($this->request->contentType)) {
            $this->request->defineContentType($contentType);
        }

        try {
            $direction = $this->directionFactory->direction(
                $contentType,
                $this->request->contentType,
                $this->request->commandString
            );

            if ($direction instanceof NullPlugin) {
                return (new HttpFoundation\StreamedResponse(
                    static function() use ($stream) {
                        $stream->rewind();
                        while (!$stream->eof()) {
                            echo $stream->read(4096);
                        }
                        $stream->close();
                    },
                    HttpFoundation\Response::HTTP_OK,
                    [
                        'Content-type' => (string) $this->request->contentType
                    ]
                ))->setProtocolVersion('1.1');
            }

            return (new HttpFoundation\Response(
                $direction->convert($stream->getContents()),
                HttpFoundation\Response::HTTP_OK,
                [
                    'Content-type' => (string) $this->request->contentType
                ]
            ))->setProtocolVersion('1.1');
        } catch (Plugin\NotAvailableException $e) {
            throw new Controller\NotFoundException;
        } catch (Exception\AmbiguousPluginCommand $e) {
            throw new Controller\NotFoundException;
        } catch (Exception\ConversionNotPossible $e) {
            throw new Controller\NotFoundException;
        }
    }

    /**
     * @return HttpFoundation\Response
     * @throws Controller\NotFoundException
     */
    public function delete(): HttpFoundation\Response
    {
        try {
            $this->storage->delete($this->request->id);
        } catch (Storage\NotFoundException $e) {
            throw new Controller\NotFoundException;
        }
        return self::jsonResponse([]);
    }

    public function __call($name, $args)
    {
        throw new Controller\NotFoundException;
    }

    /**
     * @param $data
     * @param int $status
     * @return HttpFoundation\Response
     */
    private static function jsonResponse($data, int $status = HttpFoundation\Response::HTTP_OK): HttpFoundation\Response
    {
        return (new HttpFoundation\JsonResponse($data,$status))->setProtocolVersion('1.1');
    }
}
