<?php

namespace App\Twig\Runtime;

use App\Entity\PointOfInterest;
use App\Service\MediaProcessorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class AppExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly MediaProcessorInterface $mediaProcessor,
    ) {
    }

    public function getMediaEmbedCode(mixed $value): ?string
    {
        try {
            if ($value instanceof PointOfInterest) {
                return $this->mediaProcessor->getEmbedCode($value);
            }
        } catch (\Throwable $exception) {
            // Ignore all errors.
        }

        return null;
    }

    public function getMediaTemplates(): array
    {
        return $this->mediaProcessor->getTemplates();
    }
}
