<?php

namespace App\Serializer;

use App\Entity\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * @see https://api-platform.com/docs/core/file-upload/#resolving-the-file-url
 */
final class FileNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'FILE_NORMALIZER_ALREADY_CALLED';

    public function __construct(
        readonly private StorageInterface $storage,
        readonly private EntityManagerInterface $entityManager
    ) {
    }

    public function normalize($object, string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $context[self::ALREADY_CALLED] = true;

        if (is_object($object)) {
            $metadata = $this->entityManager->getClassMetadata($object::class);
            foreach ($metadata->getFieldNames() as $fieldName) {
                $fileFieldName = $fieldName.'File';
                $urlFieldName = $fieldName.'Url';
                if (property_exists($object, $fileFieldName)
                 && property_exists($object, $urlFieldName)) {
                    $object->{$urlFieldName} = $this->storage->resolveUri($object, $fileFieldName);
                }
            }
        }

        return $this->normalizer->normalize($object, $format, $context);
    }

    // TODO: Implementing this creates an infinite loop.
    public function hest_getSupportedTypes(?string $format)
    {
        return [
            Route::class => true,
        ];
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof Route;
    }
}
