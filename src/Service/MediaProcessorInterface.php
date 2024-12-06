<?php

namespace App\Service;

use App\Entity\PointOfInterest;

interface MediaProcessorInterface
{
    /**
     * Get embed code.
     */
    public function getEmbedCode(PointOfInterest $point): string;
}
