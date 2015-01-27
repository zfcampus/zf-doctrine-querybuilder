<?php

namespace ZF\Doctrine\QueryBuilder\Query\Provider;

use ZF\Apigility\Doctrine\Server\Query\Provider\DefaultOrm as DefaultOrmBase;
use ZF\Rest\ResourceEvent;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class FetchAllOrm
 *
 * @package ZF\Apigility\Doctrine\Server\Query\Provider
 */
class DefaultOrm extends DefaultOrmBase implements ServiceLocatorAwareInterface
{  
    use ServiceLocatorAwareTrait;

    /**
     * @param string $entityClass
     * @param array  $parameters
     *
     * @return mixed This will return an ORM or ODM Query\Builder
     */
    public function createQuery(ResourceEvent $event, $entityClass, $parameters)
    {
        $request = $this->getServiceLocator()->getServiceLocator()
                        ->get('Application')->getRequest()->getQuery()->toArray();

        $queryBuilder = $this->getObjectManager()->createQueryBuilder();
        $queryBuilder->select('row')
            ->from($entityClass, 'row');

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
