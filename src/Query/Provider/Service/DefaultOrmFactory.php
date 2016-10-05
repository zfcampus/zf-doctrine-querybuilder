<?php

namespace ZF\Doctrine\QueryBuilder\Query\Provider\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\Doctrine\QueryBuilder\Filter\Service\ORMFilterManager;
use ZF\Doctrine\QueryBuilder\OrderBy\Service\ORMOrderByManager;
use ZF\Doctrine\QueryBuilder\Query\Provider\DefaultOrm;

class DefaultOrmFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param                    $requestedName
     * @param array|null         $options
     *
     * @return DefaultOrm
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (!method_exists($container, 'configure')) {
            $container = $container->getServiceLocator();
        }
        $filterManager = $container->get(ORMFilterManager::class);
        $orderByManager = $container->get(ORMOrderByManager::class);
        $config = $container->get('Config');
        $options = [];
        if (isset($config['zf-doctrine-querybuilder-options'])
            && is_array($config['zf-doctrine-querybuilder-options'])
        ) {
            $options = $config['zf-doctrine-querybuilder-options'];
        }
        return new DefaultOrm($filterManager, $orderByManager, $options);
    }
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, DefaultOrm::class);
    }
}