<?php

return array(
    'modules' => array(
        'Zend\Router',
        'DoctrineModule',
        'DoctrineORMModule',
        'DoctrineMongoODMModule',
        'Db',
        'DbMongo',
        'ZF\Doctrine\QueryBuilder',
    ),
    'module_listener_options' => array(
        'config_glob_paths' => array(
            __DIR__ . '/local.php',
        ),
        'module_paths' => array(
            __DIR__ . '/../vendor',
            'DbMongo' => __DIR__ . '/module/DbMongo',
            'Db' => __DIR__ . '/module/Db',
            'ZF\Doctrine\QueryBuilder' => __DIR__ . '/../../',
        ),
    ),
);
