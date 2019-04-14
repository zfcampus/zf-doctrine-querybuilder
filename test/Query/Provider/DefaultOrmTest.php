<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZFTest\Doctrine\QueryBuilder\Query\Provider;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager as ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ProphecyInterface;
use Zend\Http\Request;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Parameters;
use ZF\Apigility\Doctrine\Server\Paginator\Adapter\DoctrineOrmAdapter;
use ZF\Doctrine\QueryBuilder\Filter\Service\ORMFilterManager;
use ZF\Doctrine\QueryBuilder\OrderBy\Service\ORMOrderByManager;
use ZF\Doctrine\QueryBuilder\Query\Provider\DefaultOrm;
use ZF\Rest\ResourceEvent;

class DefaultOrmTest extends TestCase
{
    /**
     * @var DefaultOrm|ProphecyInterface
     */
    protected $provider;

    /**
     * @var QueryBuilder|ProphecyInterface
     */
    protected $queryBuilder;

    /**
     * @var ObjectManager|ProphecyInterface
     */
    protected $objectManager;

    /**
     * @var ServiceLocatorInterface|ProphecyInterface
     */
    protected $serviceLocator;

    protected function setUp()
    {
        $this->queryBuilder = $this->prophesize(QueryBuilder::class);
        $this->queryBuilder->select(Argument::any())->willReturn($this->queryBuilder->reveal());
        $this->queryBuilder->from(Argument::any(), Argument::any())->willReturn($this->queryBuilder->reveal());

        $this->objectManager = $this->prophesize(ObjectManager::class);
        $this->objectManager->createQueryBuilder()->willReturn($this->queryBuilder->reveal());

        $this->serviceLocator = $this->prophesize(ServiceLocatorInterface::class);

        $this->provider = new DefaultOrm();
        $this->provider->setServiceLocator($this->serviceLocator->reveal());
        $this->provider->setObjectManager($this->objectManager->reveal());
    }

    public function testCreateQueryWithoutParams()
    {
        $resourceEvent = $this->getResourceEvent();

        $result = $this->provider->createQuery($resourceEvent->reveal(), 'foo.entity.class', []);

        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testCreateQueryWithFilterParameter()
    {
        $entityClass = 'foo.entity.class';

        $metadata = $this->prophesize(ClassMetadata::class)->reveal();
        $this->objectManager->getClassMetadata($entityClass)->willReturn($metadata);

        $filterManager = $this->prophesize(ORMFilterManager::class);
        $filterManager->filter($this->queryBuilder->reveal(), $metadata, 'foo-filter')->shouldBeCalledTimes(1);

        $this->serviceLocator->get('config')->willReturn([]);
        $this->serviceLocator->has(ORMFilterManager::class)->willReturn(true);
        $this->serviceLocator->get(ORMFilterManager::class)->willReturn($filterManager->reveal());

        $resourceEvent = $this->getResourceEvent(['filter' => 'foo-filter']);

        $result = $this->provider->createQuery($resourceEvent->reveal(), $entityClass, []);

        $this->assertInstanceOf(QueryBuilder::class, $result);
        $this->assertSame($this->queryBuilder->reveal(), $result);
    }

    public function testCreateQueryWithRenamedFilterParameter()
    {
        $entityClass = 'foo.entity.class';

        $metadata = $this->prophesize(ClassMetadata::class)->reveal();
        $this->objectManager->getClassMetadata($entityClass)->willReturn($metadata);

        $filterManager = $this->prophesize(ORMFilterManager::class);
        $filterManager->filter($this->queryBuilder->reveal(), $metadata, 'foo-filter-renamed')->shouldBeCalledTimes(1);

        $this->serviceLocator->get('config')->willReturn([
            'zf-doctrine-querybuilder-options' => [
                'filter_key' => 'renamed-filter-param',
            ],
        ]);
        $this->serviceLocator->has(ORMFilterManager::class)->willReturn(true);
        $this->serviceLocator->get(ORMFilterManager::class)->willReturn($filterManager->reveal());

        $resourceEvent = $this->getResourceEvent(['renamed-filter-param' => 'foo-filter-renamed']);

        $result = $this->provider->createQuery($resourceEvent->reveal(), $entityClass, []);

        $this->assertInstanceOf(QueryBuilder::class, $result);
        $this->assertSame($this->queryBuilder->reveal(), $result);
    }

    public function testCreateQueryWithOrderByParameter()
    {
        $entityClass = 'foo.entity.class';

        $metadata = $this->prophesize(ClassMetadata::class)->reveal();
        $this->objectManager->getClassMetadata($entityClass)->willReturn($metadata);

        $orderByManager = $this->prophesize(ORMOrderByManager::class);
        $orderByManager->orderBy($this->queryBuilder->reveal(), $metadata, 'foo-order-by')->shouldBeCalledTimes(1);

        $this->serviceLocator->get('config')->willReturn([]);
        $this->serviceLocator->has(ORMOrderByManager::class)->willReturn(true);
        $this->serviceLocator->get(ORMOrderByManager::class)->willReturn($orderByManager->reveal());

        $resourceEvent = $this->getResourceEvent(['order-by' => 'foo-order-by']);

        $result = $this->provider->createQuery($resourceEvent->reveal(), $entityClass, []);

        $this->assertInstanceOf(QueryBuilder::class, $result);
        $this->assertSame($this->queryBuilder->reveal(), $result);
    }

    public function testCreateQueryWithRenamedOrderByParameter()
    {
        $entityClass = 'foo.entity.class';

        $metadata = $this->prophesize(ClassMetadata::class)->reveal();
        $this->objectManager->getClassMetadata($entityClass)->willReturn($metadata);

        $orderByManager = $this->prophesize(ORMOrderByManager::class);
        $orderByManager->orderBy($this->queryBuilder->reveal(), $metadata, 'FooOrderBy')->shouldBeCalledTimes(1);

        $this->serviceLocator->get('config')->willReturn([
            'zf-doctrine-querybuilder-options' => [
                'order_by_key' => 'renamed-order-by-param',
            ],
        ]);
        $this->serviceLocator->has(ORMOrderByManager::class)->willReturn(true);
        $this->serviceLocator->get(ORMOrderByManager::class)->willReturn($orderByManager->reveal());

        $resourceEvent = $this->getResourceEvent(['renamed-order-by-param' => 'FooOrderBy']);

        $result = $this->provider->createQuery($resourceEvent->reveal(), $entityClass, []);

        $this->assertInstanceOf(QueryBuilder::class, $result);
        $this->assertSame($this->queryBuilder->reveal(), $result);
    }

    public function testCreateQueryWithFilterAndOrderByParameters()
    {
        $entityClass = 'foo.entity.class';

        $metadata = $this->prophesize(ClassMetadata::class)->reveal();
        $this->objectManager->getClassMetadata($entityClass)->willReturn($metadata);

        $filterManager = $this->prophesize(ORMFilterManager::class);
        $filterManager->filter($this->queryBuilder->reveal(), $metadata, 'foo-filter')->shouldBeCalledTimes(1);

        $orderByManager = $this->prophesize(ORMOrderByManager::class);
        $orderByManager->orderBy($this->queryBuilder->reveal(), $metadata, 'foo-order-by')->shouldBeCalledTimes(1);

        $this->serviceLocator->get('config')->willReturn([]);
        $this->serviceLocator->has(ORMOrderByManager::class)->willReturn(true);
        $this->serviceLocator->get(ORMOrderByManager::class)->willReturn($orderByManager->reveal());
        $this->serviceLocator->has(ORMFilterManager::class)->willReturn(true);
        $this->serviceLocator->get(ORMFilterManager::class)->willReturn($filterManager->reveal());

        $resourceEvent = $this->getResourceEvent([
            'filter' => 'foo-filter',
            'order-by' => 'foo-order-by',
        ]);

        $result = $this->provider->createQuery($resourceEvent->reveal(), $entityClass, []);

        $this->assertInstanceOf(QueryBuilder::class, $result);
        $this->assertSame($this->queryBuilder->reveal(), $result);
    }

    public function testGetPaginatedQuery()
    {
        $query = $this->prophesize(AbstractQuery::class);
        $this->queryBuilder->getQuery()->willReturn($query->reveal());

        $adapter = $this->provider->getPaginatedQuery($this->queryBuilder->reveal());

        $this->assertInstanceOf(DoctrineOrmAdapter::class, $adapter);
    }

    public function testGetCollectionTotal()
    {
        $entityClass = 'foo.entity.class';

        $metadata = $this->prophesize(ClassMetadata::class)->reveal();
        $metadataFactory = $this->prophesize(ClassMetadataFactory::class);
        $metadataFactory->getMetadataFor($entityClass)->willReturn($metadata);

        $this->objectManager->getMetadataFactory()->willReturn($metadataFactory->reveal());

        $query = $this->prophesize(AbstractQuery::class);
        $query->getSingleScalarResult()->willReturn(783);
        $this->queryBuilder->getQuery()->willReturn($query->reveal());

        $count = $this->provider->getCollectionTotal($entityClass);

        $this->assertEquals(783, $count);
    }

    /**
     * @param array|null $params
     * @return ResourceEvent|ProphecyInterface
     */
    protected function getResourceEvent(array $params = null)
    {
        $request = $this->prophesize(Request::class);
        $request->getQuery()->willReturn(new Parameters($params));

        $resourceEvent = $this->prophesize(ResourceEvent::class);
        $resourceEvent->getRequest()->willReturn($request->reveal());

        return $resourceEvent;
    }
}
