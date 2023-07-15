<?php

namespace App\Serializer;

use App\Entity\PointOfInterest;
use App\Entity\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Exception\VichUploaderExceptionInterface;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * @see https://api-platform.com/docs/core/file-upload/#resolving-the-file-url
 */
final class FileNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = __FILE__;

    public function __construct(
        readonly private StorageInterface $storage,
    ) {
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;

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
                        $uploadableField = new UploadableField(...$attribute->getArguments());
                        if ($uploadableField->getFileNameProperty()) {
                            $urlFieldName = $uploadableField->getFileNameProperty().'Url';
                            if (property_exists($object, $urlFieldName)) {
                                try {
                                    $object->{$urlFieldName} = $this->storage->resolveUri($object, $fileFieldName);
                                } catch (VichUploaderExceptionInterface $exception) {
                                }
                            }
                        }
                        break;
                    }
                }
            }
        }

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof PointOfInterest || $data instanceof Route;
    }

    // TODO: Implementing getSupportedTypes (which should be done since
    // symfony/serializer 6.3) results in an infinite loop.
    //
    // The example
    // https://api-platform.com/docs/core/file-upload/#resolving-the-file-url
    // does not do this and in general the normalizers in API Platform do not de
    // this (cf. https://github.com/api-platform/api-platform/issues/2475).
    //
    // public function getSupportedTypes(?string $format)
    // {
    //     return [
    //         PointOfInterest::class => true,
    //         Route::class => true,
    //     ];
    // }
}
