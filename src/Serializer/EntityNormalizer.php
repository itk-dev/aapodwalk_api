<?php

namespace App\Serializer;

use App\Entity\PointOfInterest;
use App\Entity\Route;
use App\Service\MediaProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Exception\VichUploaderExceptionInterface;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * @see https://api-platform.com/docs/core/file-upload/#resolving-the-file-url
 */
final class EntityNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.jsonld.normalizer.item')]
        readonly private NormalizerInterface $normalizer,
        readonly private StorageInterface $storage,
        readonly private MediaProcessorInterface $mediaProcessor,
    ) {
    }

    public function normalize(
        mixed $data,
        ?string $format = null,
        array $context = [],
    ): array|string|int|float|bool|\ArrayObject|null {
        $this->setIsProcessed($data, $context);

        if (is_object($data)) {
            $this->processImages($data);
            $this->processMedia($data);
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
        if ($this->isProcessed($data, $context)) {
            return false;
        }

        return $data instanceof Route || $data instanceof PointOfInterest;
    }

    private function isProcessed(mixed $data, array $context): bool
    {
        return isset($context[$this->getIsProcessedKey($data)]);
    }

    private function setIsProcessed(mixed $data, array &$context): void
    {
        if ($key = $this->getIsProcessedKey($data)) {
            $context[$key] = true;
        }
    }

    private function getIsProcessedKey(mixed $data): ?string
    {
        if ($data instanceof Route || $data instanceof PointOfInterest) {
            return sprintf('%s||%s||%s', static::class, $data::class, $data->getId() ?? '');
        }

        return null;
    }

    private function processImages(object $data): void
    {
        // TODO: Find these field names using reflection or some such magic …
        $fileFieldNames = ['imageFile'];

        foreach ($fileFieldNames as $fileFieldName) {
            if (!property_exists($data, $fileFieldName)) {
                continue;
            }

            $reflectionProperty = new \ReflectionProperty($data, $fileFieldName);
            foreach ($reflectionProperty->getAttributes() as $attribute) {
                if (UploadableField::class === $attribute->getName()) {
                    $uploadableField = new UploadableField(...$attribute->getArguments());
                    if ($uploadableField->getFileNameProperty()) {
                        $urlFieldName = $uploadableField->getFileNameProperty().'Url';
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
    }

    private function processMedia(object $data): void
    {
        if (!$data instanceof PointOfInterest) {
            return;
        }

        // TODO: Find these field names using reflection or some such magic …
        $mediaUrlNames = ['mediaUrl'];

        foreach ($mediaUrlNames as $mediaUrlName) {
            if (!property_exists($data, $mediaUrlName)) {
                continue;
            }

            $reflectionProperty = new \ReflectionProperty($data, $mediaUrlName);
            if ($url = $reflectionProperty->getValue($data)) {
                $embedCodeName = preg_replace('/Url$/', 'EmbedCode', $mediaUrlName);
                if (property_exists($data, $embedCodeName)) {
                    try {
                        $data->{$embedCodeName} = $this->mediaProcessor->getEmbedCode($data, $mediaUrlName);
                    } catch (VichUploaderExceptionInterface $exception) {
                    }
                }
            }
        }
    }
}
