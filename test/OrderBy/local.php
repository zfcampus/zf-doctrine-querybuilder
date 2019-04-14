<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

use Doctrine\DBAL\Driver\PDOSqlite\Driver;
use Zend\ServiceManager\Factory\InvokableFactory;
use ZF\Doctrine\QueryBuilder\OrderBy;

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'configuration' => 'orm_default',
                'eventmanager'  => 'orm_default',
                'driverClass'   => Driver::class,
                'params' => [
                    'memory' => true,
                ],
            ],
            'odm_default' => [
                'server' => 'localhost',
                'port' => '27017',
                'user' => '',
                'password' => '',
                'dbname' => 'zf_doctrine_querybuilder_filter_test',
            ],
        ],
    ],
    'zf-doctrine-querybuilder-orderby-orm' => [
        'aliases' => [
            'field' => OrderBy\ORM\Field::class,
        ],
        'factories' => [
            OrderBy\ORM\Field::class => InvokableFactory::class,
        ],
    ],
    'zf-doctrine-querybuilder-orderby-odm' => [
        'aliases' => [
            'field' => OrderBy\ODM\Field::class,
        ],
        'factories' => [
            OrderBy\ODM\Field::class => InvokableFactory::class,
        ],
    ],
];
