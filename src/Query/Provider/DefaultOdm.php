<?php

namespace ZF\Doctrine\QueryBuilder\Query\Provider;

use ZF\Apigility\Doctrine\Server\Query\Provider\DefaultOdm as DefaultOdmBase;
use ZF\Rest\ResourceEvent;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class DefaultOdm extends DefaultOdmBase
{
    use ServiceLocatorAwareTrait;
    
    /**
     * {@inheritDoc}
     */
    public function createQuery(ResourceEvent $event, $entityClass, $parameters)
    {
        $request = $this->getServiceLocator()->getServiceLocator()
                        ->get('Application')->getRequest()->getQuery()->toArray();

        $queryBuilder = $this->getObjectManager()->createQueryBuilder();
        $queryBuilder->find($entityClass);

        if (isset($request['filter'])) {
            $metadata = $this->getObjectManager()->getMetadataFactory()->getAllMetadata();
            $filterManager = $this->getServiceLocator()->getServiceLocator()
                                  ->get('ZfDoctrineQueryBuilderFilterManagerOrm');
            $filterManager->filter(
                $queryBuilder,
                $metadata[0],
                $request['filter']
            );
        }

        if (isset($request['order-by'])) {
            $metadata = $this->getObjectManager()->getMetadataFactory()->getAllMetadata();
            $orderByManager = $this->getServiceLocator()->getServiceLocator()
                                   ->get('ZfDoctrineQueryBuilderOrderByManagerOrm');
            $orderByManager->orderBy(
                $queryBuilder,
                $metadata[0],
                $request['order-by']
            );
        }

        return $queryBuilder;
    }
}
