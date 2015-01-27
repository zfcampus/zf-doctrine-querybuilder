<?php

namespace ZF\Doctrine\QueryBuilder\Query\Provider;

use ZF\Apigility\Doctrine\Server\Query\Provider\DefaultOdm as ZFApigilityDefaultOdm;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\ResourceEvent;

class DefaultOdm extends ZFApigilityDefaultOdm implements ServiceLocatorAwareInterface
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
     * {@inheritDoc}
     */
    public function createQuery(ResourceEvent $event, $entityClass, $parameters)
    {
        $queryBuilder = parent::createQuery($event, $entityClass, $parameters);
        if ($queryBuilder instanceof ApiProblem) {
            return $queryBuilder;
        }

        $request = $event->getRequest()->getQuery()->toArray();

        if (isset($request['filter'])) {
            $metadata = $this->getObjectManager()->getMetadataFactory()->getAllMetadata();
            $filterManager = $this->
                getServiceLocator()->
                getServiceLocator()->
                get('ZfDoctrineQueryBuilderFilterManagerOrm');

            $filterManager->filter(
                $queryBuilder,
                $metadata[0],
                $request['filter']
            );
        }

        if (isset($request['order-by'])) {
            $metadata = $this->getObjectManager()->getMetadataFactory()->getAllMetadata();
            $orderByManager = $this->
                getServiceLocator()->
                getServiceLocator()->
                get('ZfDoctrineQueryBuilderOrderByManagerOrm');

            $orderByManager->orderBy(
                $queryBuilder,
                $metadata[0],
                $request['order-by']
            );
        }

        return $queryBuilder;
    }
}
