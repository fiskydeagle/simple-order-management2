<?php
// api/src/OpenApi/JwtDecorator.php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;

final class JwtDecorator implements OpenApiFactoryInterface
{
    private $decorated;
    public function __construct(
        OpenApiFactoryInterface $decorated
    )
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['Token'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);
        $schemas['Credentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    //'example' => 'testUser',
                    'example' => 'fiskydeagle',
                ],
                'password' => [
                    'type' => 'string',
                    //'example' => 'apassword',
                    'example' => '121001Fisky',
                ],
            ],
        ]);

        $pathItem = new Model\PathItem(
            'JWT Token',
            '',
            '',
            null,
            null,
            new Model\Operation(
             'postCredentialsItem',
            ['Token'],
            [
                '200' => [
                    'description' => 'Get JWT token',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Token',
                            ],
                        ],
                    ],
                ],
            ],
            'Get JWT token to login.',
            'Get JWT token to login.',
            null,
            [],
            new Model\RequestBody(
             'Generate new JWT Token',
                    new \ArrayObject([
                    'application/json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/Credentials',
                        ],
                    ],
        ]),
        ),
        ),
        );
        $openApi->getPaths()->addPath('/authentication_token', $pathItem);

        return $openApi;
    }
}
