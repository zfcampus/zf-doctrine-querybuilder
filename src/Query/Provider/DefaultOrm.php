<?php

namespace ZF\Doctrine\QueryBuilder\Query\Provider;

use ZF\Apigility\Doctrine\Server\Query\Provider\AbstractQueryProvider;
use ZF\Apigility\Doctrine\Server\Query\Provider\QueryProviderInterface;
use ZF\Apigility\Doctrine\Server\Paginator\Adapter\DoctrineOrmAdapter;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\Rest\ResourceEvent;

/**
 * Class FetchAllOrm
 *
 * @package ZF\Apigility\Doctrine\Server\Query\Provider
 */
class DefaultOrm extends AbstractQueryProvider implements ObjectManagerAwareInterface, QueryProviderInterface, ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator = null;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
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
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Set the object manager
     *
     * @param ObjectManager $objectManager
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get the object manager
     *
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * @param string $entityClass
     * @param array  $parameters
     *
     * @return mixed This will return an ORM or ODM Query\Builder
     */
    public function createQuery(ResourceEvent $event, $entityClass, $parameters)
    {
        $request = $this->getServiceLocator()->getServiceLocator()->get('Application')->getRequest()->getQuery()->toArray();

        $queryBuilder = $this->getObjectManager()->createQueryBuilder();
        $queryBuilder->select('row')
            ->from($entityClass, 'row');

        if (isset($request['filter'])) {
            $metadata = $this->getObjectManager()->getMetadataFactory()->getAllMetadata();
            $filterManager = $this->getServiceLocator()->getServiceLocator()->get('ZfDoctrineQueryBuilderFilterManagerOrm');
            $filterManager->filter(
                $queryBuilder,
                $metadata[0],
                $request['filter']
            );
        }

        if (isset($request['order-by'])) {
            $metadata = $this->getObjectManager()->getMetadataFactory()->getAllMetadata();
            $orderByManager = $this->getServiceLocator()->getServiceLocator()->get('ZfDoctrineQueryBuilderOrderByManagerOrm');
            $orderByManager->orderBy(
                $queryBuilder,
                $metadata[0],
                $request['order-by']
            );
        }

        return $queryBuilder;
    }

    /**
     * @param   $queryBuilder
     *
     * @return AdapterInterface
     */
    public function getPaginatedQuery($queryBuilder)
    {
        $adapter = new DoctrineOrmAdapter($queryBuilder->getQuery(), false);

        return $adapter;
    }

    /**
     * @param   $entityClass
     *
     * @return int
     */
    public function getCollectionTotal($entityClass)
    {
        $queryBuilder = $this->getObjectManager()->createQueryBuilder();
        $cmf = $this->getObjectManager()->getMetadataFactory();
        $entityMetaData = $cmf->getMetadataFor($entityClass);

        $identifier = $entityMetaData->getIdentifier();
        $queryBuilder->select('count(row.' . $identifier[0] . ')')
            ->from($entityClass, 'row');

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
