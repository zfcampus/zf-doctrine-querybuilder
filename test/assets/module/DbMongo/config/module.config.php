<?php

namespace DbMongo;

use Doctrine\ODM\MongoDB\Mapping\Driver\YamlDriver;

return [
    'doctrine' => [
        'driver' => [
            'odm_driver' => [
                'class' => YamlDriver::class,
                'paths' => [__DIR__ . '/yml'],
            ],
            'odm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Document' => 'odm_driver',
                ],
            ],
        ],
    ],
];
