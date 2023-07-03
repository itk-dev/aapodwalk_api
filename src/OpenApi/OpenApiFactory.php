<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;

class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(
        private readonly OpenApiFactoryInterface $decorated,
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $securitySchemes = $openApi->getComponents()->getSecuritySchemes();
        if (is_null($securitySchemes)) {
            $securitySchemes = [];
        }
        $securitySchemes['access_token'] = new \ArrayObject([
            'type' => 'http',
            'scheme' => 'bearer',
        ]);

        return $openApi;
    }
}
