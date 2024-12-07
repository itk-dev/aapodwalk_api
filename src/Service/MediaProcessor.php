<?php

namespace App\Service;

use App\Entity\PointOfInterest;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Translation\LocaleSwitcher;
use Twig\Environment;

final class MediaProcessor implements MediaProcessorInterface
{
    private readonly array $options;

    public function __construct(
        private readonly LocaleSwitcher $localeSwitcher,
        private readonly PropertyAccessorInterface $propertyAccessor,
        private readonly Environment $twig,
        array $options,
    ) {
        $this->options = $this->processOptions($options);
    }

    public function getEmbedCode(PointOfInterest $entity, string $property = 'mediaUrl'): string
    {
        $url = $this->propertyAccessor->getValue($entity, $property);
        if ($url) {
            $processedUrl = $url;
            $isAudioProperty = preg_replace('/Url$/', 'IsAudio', $property);
            if ($this->propertyAccessor->isReadable($entity, $isAudioProperty)
                && $this->propertyAccessor->getValue($entity, $isAudioProperty)) {
                $processedUrl .= (str_contains($url, '?') ? '&' : '?').http_build_query(['is_audio' => true]);
            }
            foreach ($this->getTemplates() as $template) {
                try {
                    if (preg_match($template['pattern'], $processedUrl, $matches)) {
                        $twig = $this->twig->createTemplate($template['template']);
                        $context = $matches + [
                            'url' => $url,
                        ];
                        if ($entity instanceof \JsonSerializable) {
                            $context += $entity->jsonSerialize();
                        }

                        return $twig->render($context);
                    }
                } catch (\Throwable $exception) {
                    throw $exception;
                }
            }
        }

        throw new \RuntimeException(sprintf('Could not process media url %s', $url ?? 'null'));
    }

    public function getTemplates(): array
    {
        return $this->options['templates'];
    }

    private function processOptions(array $options): array
    {
        $locale = $this->localeSwitcher->getLocale();
        $getLocalizedString = function (Options $options, array|string $value) use ($locale): string {
            if (is_array($value)) {
                // Use the value for the current locale if defined; otherwise use the first value.
                $value = (string) ($value[$locale] ?? reset($value));
            }

            return $value;
        };

        return (new OptionsResolver())
            ->setRequired('templates')
            ->setAllowedTypes('templates', 'array')
            ->setDefault('templates', function (OptionsResolver $resolver) use ($getLocalizedString): void {
                $resolver
                    ->setPrototype(true)
                    ->setRequired('name')
                    ->setAllowedTypes('name', 'string')
                    ->setRequired('help')
                    ->setAllowedTypes('help', ['string', 'array'])
                    ->setNormalizer('help', $getLocalizedString(...))
                    ->setRequired('pattern')
                    ->setAllowedTypes('pattern', 'string')
                    ->setRequired('template')
                    ->setAllowedTypes('template', 'string');
            })
            ->resolve($options);
    }
}
