<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

return [
    'modules' => [
        'DoctrineModule',
        'DoctrineORMModule',
        'DoctrineMongoODMModule',
        'Db',
        'DbMongo',
        'ZF\Doctrine\QueryBuilder',
    ],
    'module_listener_options' => [
        'config_glob_paths' => [
            __DIR__ . '/local.php',
        ],
        'module_paths' => [
            __DIR__ . '/../vendor',
            'DbMongo' => __DIR__ . '/../assets/module/DbMongo',
            'Db' => __DIR__ . '/../assets/module/Db',
            'ZF\Doctrine\QueryBuilder' => __DIR__ . '/../../',
        ],
    ],
];
