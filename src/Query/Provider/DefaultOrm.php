<?php

namespace ZF\Doctrine\QueryBuilder\Query\Provider;

use ZF\Apigility\Doctrine\Server\Query\Provider\AbstractQueryProvider;
use ZF\Apigility\Doctrine\Server\Query\Provider\QueryProviderInterface;
use ZF\Doctrine\QueryBuilder\Filter\Service\ORMFilterManager;
use ZF\Doctrine\QueryBuilder\OrderBy\Service\ORMOrderByManager;
use ZF\Rest\ResourceEvent;

/**
 * Class FetchAllOrm
 *
 * @package ZF\Apigility\Doctrine\Server\Query\Provider
 */
class DefaultOrm extends AbstractQueryProvider implements QueryProviderInterface
{
    /**
     * @var ORMFilterManager
     */
    private $filterManager;
    /**
     * @var ORMOrderByManager
     */
    private $orderByManager;
    /**
     * @var array
     */
    private $options;

    public function __construct(ORMFilterManager $filterManager, ORMOrderByManager $orderByManager, array $options = [])
    {
        $this->filterManager = $filterManager;
        $this->orderByManager = $orderByManager;
        $this->options = $options;
    }

    /**
     * @param ResourceEvent $event
     * @param string        $entityClass
     * @param array         $parameters
     *
     * @return mixed This will return an ORM or ODM Query\Builder
     */
    public function createQuery(ResourceEvent $event, $entityClass, $parameters)
    {
        $request = $event->getRequest()->getQuery()->toArray();
        $queryBuilder = $this->getObjectManager()->createQueryBuilder();
        $queryBuilder->select('row')
            ->from($entityClass, 'row');

        if (isset($request[$this->getFilterKey()])) {
            $metadata = $this->getObjectManager()->getClassMetadata($entityClass);
            $this->filterManager->filter(
                $queryBuilder,
                $metadata,
                $request[$this->getFilterKey()]
            );
        }

        if (isset($request[$this->getOrderByKey()])) {
            $metadata = $this->getObjectManager()->getClassMetadata($entityClass);
            $this->orderByManager->orderBy(
                $queryBuilder,
                $metadata,
                $request[$this->getOrderByKey()]
            );
        }

        return $queryBuilder;
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
