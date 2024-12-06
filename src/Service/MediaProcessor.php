<?php

namespace App\Service;

use App\Entity\PointOfInterest;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

final class MediaProcessor implements MediaProcessorInterface
{
    private readonly array $options;

    public function __construct(
        private readonly Environment $twig,
        array $options,
    ) {
        $this->options = $options;
    }

    public function getEmbedCode(PointOfInterest $point): string
    {
        $url = $point->getMediaUrl();
        if ($url) {
            $processedUrl = $url;
            if ($point->isAudio()) {
                $processedUrl .= (str_contains($url, '?') ? '&' : '?') . http_build_query(['is_audio' => true]);
            }
            foreach ($this->options['templates'] as $template) {
                try {
                    if (preg_match($template['pattern'], $processedUrl, $matches)) {
                        $twig = $this->twig->createTemplate($template['template']);
                        $context = $matches + [
                                'title' => $point->getName(),
                                'url' => $url,
                            ];

                        return $twig->render($context);
                    }
                } catch (\Throwable $exception) {
                    throw $exception;
                }
            }
        }

        throw new \RuntimeException(sprintf('Could not process media url %s', $url ?? 'null'));
    }

    private function processOptions(array $options): array
    {
        return (new OptionsResolver())
            ->resolve($options);
    }
}
