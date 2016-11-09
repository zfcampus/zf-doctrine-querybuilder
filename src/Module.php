<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014-2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder;

use Zend\ModuleManager\Feature\DependencyIndicatorInterface;
use Zend\ModuleManager\Listener\ServiceListener;
use Zend\ModuleManager\ModuleManager;

class Module implements DependencyIndicatorInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function init(ModuleManager $moduleManager)
    {
        $serviceManager  = $moduleManager->getEvent()->getParam('ServiceManager');
        /** @var ServiceListener $serviceListener */
        $serviceListener = $serviceManager->get('ServiceListener');

        $serviceListener->addServiceManager(
            'ZfDoctrineQueryBuilderFilterManagerOrm',
            'zf-doctrine-querybuilder-filter-orm',
            Filter\FilterInterface::class,
            'getDoctrineQueryBuilderFilterOrmConfig'
        );

        $serviceListener->addServiceManager(
            'ZfDoctrineQueryBuilderFilterManagerOdm',
            'zf-doctrine-querybuilder-filter-odm',
            Filter\FilterInterface::class,
            'getDoctrineQueryBuilderFilterOdmConfig'
        );

        $serviceListener->addServiceManager(
            'ZfDoctrineQueryBuilderOrderByManagerOrm',
            'zf-doctrine-querybuilder-orderby-orm',
            OrderBy\OrderByInterface::class,
            'getDoctrineQueryBuilderOrderByOrmConfig'
        );
        $serviceListener->addServiceManager(
            'ZfDoctrineQueryBuilderOrderByManagerOdm',
            'zf-doctrine-querybuilder-orderby-odm',
            OrderBy\OrderByInterface::class,
            'getDoctrineQueryBuilderOrderByOdmConfig'
        );
    }

    /**
     * Expected to return an array of modules on which the current one depends on
     *
     * @return array
     */
    public function getModuleDependencies()
    {
        return ['DoctrineModule'];
    }
}
