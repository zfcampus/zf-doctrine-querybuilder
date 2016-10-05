<?php

namespace ZF\Doctrine\QueryBuilder\Query\Provider\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\Doctrine\QueryBuilder\Filter\Service\ODMFilterManager;
use ZF\Doctrine\QueryBuilder\OrderBy\Service\ODMOrderByManager;
use ZF\Doctrine\QueryBuilder\Query\Provider\DefaultOdm;

class DefaultOdmFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param                    $requestedName
     * @param array|null         $options
     *
     * @return DefaultOdm
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (!method_exists($container, 'configure')) {
            $container = $container->getServiceLocator();
        }
        $filterManager = $container->get(ODMFilterManager::class);
        $orderByManager = $container->get(ODMOrderByManager::class);
        $config = $container->get('Config');
        $options = [];
        if (isset($config['zf-doctrine-querybuilder-options'])
            && is_array($config['zf-doctrine-querybuilder-options'])
        ) {
            $options = $config['zf-doctrine-querybuilder-options'];
        }
        return new DefaultOdm($filterManager, $orderByManager, $options);
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
        return $this($serviceLocator, DefaultOdm::class);
    }
}