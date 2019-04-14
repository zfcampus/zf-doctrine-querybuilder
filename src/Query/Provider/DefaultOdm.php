<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder\Query\Provider;

use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\Apigility\Doctrine\Server\Paginator\Adapter\DoctrineOdmAdapter;
use ZF\Apigility\Doctrine\Server\Query\Provider\AbstractQueryProvider;
use ZF\Apigility\Doctrine\Server\Query\Provider\QueryProviderInterface;
use ZF\Doctrine\QueryBuilder\Filter\Service\ODMFilterManager;
use ZF\Doctrine\QueryBuilder\OrderBy\Service\ODMOrderByManager;
use ZF\Rest\ResourceEvent;

class DefaultOdm extends AbstractQueryProvider implements QueryProviderInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return self
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * {@inheritDoc}
     */
    public function createQuery(ResourceEvent $event, $entityClass, $parameters)
    {
        $request = $event->getRequest()->getQuery()->toArray();

        $queryBuilder = $this->getObjectManager()->createQueryBuilder();
        $queryBuilder->find($entityClass);

        if (isset($request[$this->getFilterKey()])) {
            $metadata = $this->getObjectManager()->getMetadataFactory()->getMetadataFor($entityClass);
            $this->getFilterManager()->filter(
                $queryBuilder,
                $metadata,
                $request[$this->getFilterKey()]
            );
        }

        if (isset($request[$this->getOrderByKey()])) {
            $metadata = $this->getObjectManager()->getMetadataFactory()->getMetadataFor($entityClass);
            $this->getOrderByManager()->orderBy(
                $queryBuilder,
                $metadata,
                $request[$this->getOrderByKey()]
            );
        }

        return $queryBuilder;
    }

    /**
     * @param $queryBuilder
     * @return DoctrineOdmAdapter
     */
    public function getPaginatedQuery($queryBuilder)
    {
        $adapter = new DoctrineOdmAdapter($queryBuilder);

        return $adapter;
    }

    /**
     * @param $entityClass
     * @return int
     */
    public function getCollectionTotal($entityClass)
    {
        $queryBuilder = $this->getObjectManager()->createQueryBuilder();
        $queryBuilder->find($entityClass);
        $count = $queryBuilder->getQuery()->execute()->count();

        return $count;
    }

    /**
     * @return string
     */
    protected function getFilterKey()
    {
        $config = $this->getConfig();
        if (isset($config['filter_key'])) {
            return $config['filter_key'];
        }

        return 'filter';
    }

    /**
     * @return string
     */
    protected function getOrderByKey()
    {
        $config = $this->getConfig();
        if (isset($config['order_by_key'])) {
            return $config['order_by_key'];
        }

        return 'order-by';
    }

    /**
     * @return array
     */
    protected function getConfig()
    {
        $config = $this->getServiceLocator()->get('config');
        if (isset($config['zf-doctrine-querybuilder-options'])) {
            return $config['zf-doctrine-querybuilder-options'];
        }

        return [];
    }

    /**
     * @return ODMFilterManager
     */
    protected function getFilterManager()
    {
        return $this->getServiceLocator()->get(ODMFilterManager::class);
    }

    /**
     * @return ODMOrderByManager
     */
    protected function getOrderByManager()
    {
        return $this->getServiceLocator()->get(ODMOrderByManager::class);
    }
}
