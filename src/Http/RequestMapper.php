<?php
namespace App\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class RequestMapper
{
    public const SOURCE_BODY = 'body';
    public const SOURCE_POST = 'post';

    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function mapToObject(Request $request, object $data, array $context = [], string $source = self::SOURCE_BODY): void
    {
        switch ($source) {
            case self::SOURCE_POST:
                $rawData = $request->request->all();
                $format = 'array';
                break;
            case self::SOURCE_BODY:
            default:
                $rawData = $request->getContent();
                $format = $request->getRequestFormat();
                break;
        }

        $context = array_merge_recursive(
            [
                'allow_extra_attributes' => false,
            ],
            $context
        );

        $context['object_to_populate'] = $data;

        try {
            $this->serializer->deserialize(
                $rawData,
                get_class($data),
                $format,
                $context
            );
        } catch (ExceptionInterface $e) {
            throw new BadRequestHttpException('Deserialization error', $e);
        }


    }
}
