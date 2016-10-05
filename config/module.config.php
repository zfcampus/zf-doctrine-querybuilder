<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2013 Zend Technologies USA Inc. (http://www.zend.com)
 */

use ZF\Doctrine\QueryBuilder\Filter\Service as FilterService;
use ZF\Doctrine\QueryBuilder\OrderBy\Service as OrderService;

return [
    'service_manager' => [
        'factories' => [
            FilterService\ORMFilterManager::class => FilterService\ORMFilterManagerFactory::class,
            FilterService\ODMFilterManager::class => FilterService\ODMFilterManagerFactory::class,
            OrderService\ORMOrderByManager::class => OrderService\ORMOrderByManagerFactory::class,
            OrderService\ODMOrderByManager::class => OrderService\ODMOrderByManagerFactory::class,
        ],
        'aliases' => [
            'ZfDoctrineQueryBuilderFilterManagerOrm' => FilterService\ORMFilterManager::class,
            'ZfDoctrineQueryBuilderFilterManagerOdm' => FilterService\ODMFilterManager::class,
            'ZfDoctrineQueryBuilderOrderByManagerOrm' => OrderService\ORMOrderByManager::class,
            'ZfDoctrineQueryBuilderOrderByManagerOdm' => OrderService\ODMOrderByManager::class,
        ],
    ],
];
