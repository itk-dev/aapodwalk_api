<?php

namespace App\Service\AppManager;

final class App
{
    public function __construct(
        private readonly array $options,
    ) {
    }

    public function getId()
    {
        return $this->options['id'];
    }

    public function getName(): string
    {
        return $this->options['name'];
    }

    public function getUrl(): string
    {
        return $this->options['url'];
    }

    public function getPath(string $type, array $params = [], bool $absolute = true): string
    {
        /** @var ?string $path */
        $path = $this->options['paths'][$type];
        if (!$path) {
            throw new \RuntimeException(sprintf('Path %s not found', $type));
        }

        // Replace `{key}` with `$params[key]` in path.
        $path = preg_replace_callback(
            '#\{(?P<key>[^}]+)\}#',
            fn (array $matches) => $params[$matches['key']] ?? $matches[0],
            $path
        );

        return $absolute ? self::buildUrl($this, $path) : $path;
    }

    public static function buildUrl(App $app, string $path): string
    {
        // @todo Improve this to handle paths correctly.
        return $app->getUrl().$path;
    }
}
