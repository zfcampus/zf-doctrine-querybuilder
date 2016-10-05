<?php

namespace ZF\Doctrine\QueryBuilder\Query\Provider;

use ZF\Apigility\Doctrine\Server\Query\Provider\AbstractQueryProvider;
use ZF\Apigility\Doctrine\Server\Query\Provider\QueryProviderInterface;
use ZF\Apigility\Doctrine\Server\Paginator\Adapter\DoctrineOdmAdapter;
use ZF\Doctrine\QueryBuilder\Filter\Service\ODMFilterManager;
use ZF\Doctrine\QueryBuilder\OrderBy\Service\ODMOrderByManager;
use ZF\Rest\ResourceEvent;

class DefaultOdm extends AbstractQueryProvider implements QueryProviderInterface
{
    /**
     * @var ODMFilterManager
     */
    private $filterManager;
    /**
     * @var ODMOrderByManager
     */
    private $orderByManager;
    /**
     * @var array
     */
    private $options;

    public function __construct(ODMFilterManager $filterManager, ODMOrderByManager $orderByManager, array $options = [])
    {
        $this->filterManager = $filterManager;
        $this->orderByManager = $orderByManager;
        $this->options = $options;
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
            $this->filterManager->filter(
                $queryBuilder,
                $metadata,
                $request[$this->getFilterKey()]
            );
        }

        if (isset($request[$this->getOrderByKey()])) {
            $metadata = $this->getObjectManager()->getMetadataFactory()->getMetadataFor($entityClass);
            $this->orderByManager->orderBy(
                $queryBuilder,
                $metadata,
                $request[$this->getOrderByKey()]
            );
        }

        return $queryBuilder;
    }

    /**
     * @param   $queryBuilder
     *
     * @return DoctrineOdmAdapter
     */
    public function getPaginatedQuery($queryBuilder)
    {
        $adapter = new DoctrineOdmAdapter($queryBuilder);

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
        $queryBuilder->find($entityClass);
        $count = $queryBuilder->getQuery()->execute()->count();

        return $count;
    }

    /**
     * @return string
     */
    protected function getFilterKey()
    {
        if (isset($this->options['filter_key'])) {
            $filterKey = $this->options['filter_key'];
        } else {
            $filterKey = 'filter';
        }

        return $filterKey;
    }

    /**
     * @return string
     */
    protected function getOrderByKey()
    {
        if (isset($this->options['order_by_key'])) {
            $orderByKey = $this->options['order_by_key'];
        } else {
            $orderByKey = 'order-by';
        }

        return $orderByKey;
    }
}
