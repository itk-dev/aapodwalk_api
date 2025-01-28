<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\AppExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_media_embed_code', [AppExtensionRuntime::class, 'getMediaEmbedCode']),
            new TwigFunction('get_media_templates', [AppExtensionRuntime::class, 'getMediaTemplates']),
            new TwigFunction('format_value_with_unit', [AppExtensionRuntime::class, 'formatValueWithUnit']),
        ];
    }
}
