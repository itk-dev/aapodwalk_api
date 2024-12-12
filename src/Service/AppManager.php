<?php

namespace App\Service;

use App\Service\AppManager\App;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AppManager
{
    /**
     * @var App[]
     */
    private array $apps;

    public function __construct(
        private readonly array $options,
    ) {
        $this->resolveOptions();
        $this->apps = array_map(fn (array $app) => new App($app), $options['apps']);
    }

    /**
     * @return App[]|array
     */
    public function getApps(): array
    {
        return $this->apps;
    }

    public function getApp(string|int $id): ?App
    {
        foreach ($this->getApps() as $app) {
            if ($app->getId() === $id) {
                return $app;
            }
        }

        return null;
    }

    private function resolveOptions(): void
    {
        (new OptionsResolver())
            ->setRequired('apps')
            ->setAllowedTypes('apps', 'array')
            ->setDefault('apps', function (OptionsResolver $resolver): void {
                $resolver
                    ->setPrototype(true)
                    ->setRequired('id')
                    ->setAllowedTypes('id', 'string')
                    ->setRequired('name')
                    ->setAllowedTypes('name', 'string')
                    ->setRequired('url')
                    ->setAllowedTypes('url', 'string')
                    ->setRequired('paths')
                    ->setAllowedTypes('paths', 'array')
                    ->setDefault('paths', function (OptionsResolver $resolver): void {
                        $resolver
                            ->setRequired('route')
                            ->setAllowedTypes('route', 'string')
                            ->setRequired('tag')
                            ->setAllowedTypes('route', 'string');
                    });
            })
            ->resolve($this->options);
    }
}
