<?php

namespace App\Service;

use App\Entity\PointOfInterest;

interface MediaProcessorInterface
{
    /**
     * Get embed code.
     */
    public function getEmbedCode(PointOfInterest $entity, string $property = 'mediaUrl'): string;

    /**
     * Get all templates.
     */
    public function getTemplates(): array;

    /**
     * Get template by URL.
     */
    public function getTemplateByUrl(string $url): ?array;
}
