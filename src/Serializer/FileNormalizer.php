<?php

namespace App\Serializer;

use App\Entity\PointOfInterest;
use App\Entity\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Exception\VichUploaderExceptionInterface;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * @see https://api-platform.com/docs/core/file-upload/#resolving-the-file-url
 */
final class FileNormalizer implements NormalizerInterface
{
    private const ALREADY_CALLED = 'MEDIA_OBJECT_NORMALIZER_ALREADY_CALLED';

    public function __construct(
        #[Autowire(service: 'api_platform.jsonld.normalizer.item')]
        readonly private NormalizerInterface $normalizer,
        readonly private StorageInterface $storage,
    ) {
    }

    public function normalize(
        mixed $data,
        ?string $format = null,
        array $context = [],
    ): array|string|int|float|bool|\ArrayObject|null {
        $context[self::ALREADY_CALLED] = true;

        if (is_object($data)) {
            // TODO: Find these field names using reflection or some such magic …
            $fileFieldNames = ['imageFile', 'podcastFile'];

            foreach ($fileFieldNames as $fileFieldName) {
                if (!property_exists($data, $fileFieldName)) {
                    continue;
                }

                $reflectionProperty = new \ReflectionProperty($data, $fileFieldName);
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
                        if (property_exists($data, $urlFieldName)) {
                            try {
                                $data->{$urlFieldName} = $this->storage->resolveUri($data, $fileFieldName);
                            } catch (VichUploaderExceptionInterface $exception) {
                            }
                        }
                    }
                }
            }
        }

        return $this->normalizer->normalize($data, $format, $context);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            PointOfInterest::class => true,
            Route::class => true,
        ];
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof Route || $data instanceof PointOfInterest;
    }
}
