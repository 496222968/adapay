<?php

namespace Dwc\AdaPay;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for adaPay.',
                    'source' => __DIR__ . '/../publish/adapay.php',
                    'destination' => BASE_PATH . '/config/autoload/adapay.php',
                ],
            ],
        ];
    }
}