<?php
namespace App\Tests\Http;

use App\Http\RequestMapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class RequestMapperTest extends KernelTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|SerializerInterface
     */
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    protected function tearDown(): void
    {
        unset($this->serializer);
    }

    public function testMapToObjectFromRequestData(): void
    {
        $requestData = [
            'test' => 'test'
        ];

        $request = $this->createMock(Request::class);
        $request->request = $this->createMock(ParameterBag::class);

        $request->request
            ->expects(static::once())
            ->method('all')
            ->willReturn($requestData);

        $object = new class {
            public ?string $test;
        };
        $objectClass = get_class($object);

        $context = $expectedContext = ['example' => 'test'];
        $expectedContext['object_to_populate'] = $object;
        $expectedContext['allow_extra_attributes'] = false;

        $this->serializer
            ->expects(static::once())
            ->method('deserialize')
            ->with($requestData, $objectClass, 'array', $expectedContext);

        $mapper = new RequestMapper($this->serializer);
        $mapper->mapToObject($request, $object, $context, RequestMapper::SOURCE_POST);
    }

    public function testMapToObjectFromRequestContent(): void
    {
        $data = [
            'test' => 'test'
        ];
        $dataJson = json_encode($data);
        $format = 'json';

        $request = $this->createMock(Request::class);
        $request
            ->expects(static::once())
            ->method('getContent')
            ->willReturn($dataJson);

        $request
            ->expects(static::once())
            ->method('getRequestFormat')
            ->willReturn($format);

        $object = new class {
            public ?string $test;
        };
        $objectClass = get_class($object);

        $context = $expectedContext = ['example' => 'test'];
        $expectedContext['object_to_populate'] = $object;
        $expectedContext['allow_extra_attributes'] = false;

        $this->serializer
            ->expects(static::once())
            ->method('deserialize')
            ->with($dataJson, $objectClass, $format, $expectedContext);

        $mapper = new RequestMapper($this->serializer);
        $mapper->mapToObject($request, $object, $context, RequestMapper::SOURCE_BODY);
    }

    public function testSerializationErrorTranslatesToBadRequestHttpException(): void
    {
        $request = $this->createMock(Request::class);
        $request
            ->method('getRequestFormat')
            ->willReturn('json');

        $this->serializer
            ->expects(static::once())
            ->method('deserialize')
            ->willThrowException(new class extends \Exception implements ExceptionInterface {});

        $this->expectException(BadRequestHttpException::class);

        $mapper = new RequestMapper($this->serializer);
        $mapper->mapToObject($request, new class {});
    }
}
