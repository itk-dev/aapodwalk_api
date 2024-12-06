<?php

namespace App\Service;

use App\Entity\PointOfInterest;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Twig\Environment;

final class MediaProcessor implements MediaProcessorInterface
{
    private readonly array $options;

    public function __construct(
        private readonly PropertyAccessorInterface $propertyAccessor,
        private readonly Environment $twig,
        array $options,
    ) {
        $this->options = $options;
    }

    public function getEmbedCode(PointOfInterest $entity, string $property = 'mediaUrl'): string
    {
        $url = $this->propertyAccessor->getValue($entity, $property);
        if ($url) {
            $processedUrl = $url;
            if ($entity->isAudio()) {
                $processedUrl .= (str_contains($url, '?') ? '&' : '?').http_build_query(['is_audio' => true]);
            }
            foreach ($this->options['templates'] as $template) {
                try {
                    if (preg_match($template['pattern'], $processedUrl, $matches)) {
                        $twig = $this->twig->createTemplate($template['template']);
                        $context = $matches + [
                            'title' => $entity->getName(),
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
