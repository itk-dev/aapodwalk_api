<?php

namespace App\Serializer;

use App\Entity\PointOfInterest;
use App\Entity\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Vich\UploaderBundle\Exception\VichUploaderExceptionInterface;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * @see https://api-platform.com/docs/core/file-upload/#resolving-the-file-url
 */
final class FileNormalizer implements NormalizerInterface
{
    public function __construct(
        readonly private ObjectNormalizer $normalizer,
        readonly private StorageInterface $storage,
        readonly private EntityManagerInterface $entityManager
    ) {
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        if (is_object($object)) {
            // TODO: Find these field names using reflection or some such magic …
            $fileFieldNames = ['imageFile', 'podcastFile'];

            foreach ($fileFieldNames as $fileFieldName) {
                if (!property_exists($object, $fileFieldName)) {
                    continue;
                }

                $reflectionProperty = new \ReflectionProperty($object, $fileFieldName);
                foreach ($reflectionProperty->getAttributes() as $attribute) {
                    if (UploadableField::class === $attribute->getName()) {
                        $mapping = null;
                        $fileNameProperty = null;
                        foreach ($attribute->getArguments() as $name => $value) {
                            if ('mapping' === $name) {
                                $mapping = $value;
                            } elseif ('fileNameProperty' === $name) {
                                $fileNameProperty = $value;
                            }
                        }
                    }

                    if (isset($mapping, $fileNameProperty)) {
                        $urlFieldName = $fileNameProperty.'Url';
                        if (property_exists($object, $urlFieldName)) {
                            try {
                                $object->{$urlFieldName} = $this->storage->resolveUri($object, $fileFieldName);
                            } catch (VichUploaderExceptionInterface $exception) {
                            }
                        }
                    }
                }
            }
        }

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function getSupportedTypes(?string $format)
    {
        return [
            PointOfInterest::class => true,
            Route::class => true,
        ];
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Route || $data instanceof PointOfInterest;
    }
}
