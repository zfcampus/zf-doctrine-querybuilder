<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2013-2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder;

return [
    'service_manager' => [
        'aliases' => [
            'ZfDoctrineQueryBuilderFilterManagerOrm' => Filter\Service\ORMFilterManager::class,
            'ZfDoctrineQueryBuilderFilterManagerOdm' => Filter\Service\ODMFilterManager::class,
            'ZfDoctrineQueryBuilderOrderByManagerOrm' => OrderBy\Service\ORMOrderByManager::class,
            'ZfDoctrineQueryBuilderOrderByManagerOdm' => OrderBy\Service\ODMOrderByManager::class,
        ],
        'factories' => [
            Filter\Service\ORMFilterManager::class => Filter\Service\ORMFilterManagerFactory::class,
            Filter\Service\ODMFilterManager::class => Filter\Service\ODMFilterManagerFactory::class,
            OrderBy\Service\ORMOrderByManager::class => OrderBy\Service\ORMOrderByManagerFactory::class,
            OrderBy\Service\ODMOrderByManager::class => OrderBy\Service\ODMOrderByManagerFactory::class,
        ],
    ],
];
