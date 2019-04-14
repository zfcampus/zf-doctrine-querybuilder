<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZFTest\Doctrine\QueryBuilder\Hydrator\Strategy;

use PHPUnit\Framework\TestCase;
use stdClass;
use Zend\ServiceManager\ServiceManager;
use ZF\Doctrine\QueryBuilder\Hydrator\Strategy\CollectionLink;

class CollectionLinkTest extends TestCase
{
    private $mockValue;

    /**
     * @var CollectionLink
     */
    private $hydrator;

    protected function setUp()
    {
        $this->mockValue = $this->getMockBuilder(stdClass::class)
            ->setMethods(['getTypeClass', 'getMapping', 'getOwner'])
            ->getMock();

        $this->mockValue->expects($this->any())
            ->method('getTypeClass')
            ->will($this->returnCallback(function () {
                $mockTypeClass = new stdClass();
                $mockTypeClass->name = 'MockValue';

                return $mockTypeClass;
            }));

        $this->mockValue->expects($this->any())
                ->method('getOwner')
                ->will($this->returnCallback(function () {
                    $mockOwner = $this->getMockBuilder(stdClass::class)
                        ->setMethods(['getId'])
                        ->getMock();

                    $mockOwner->expects($this->any())
                        ->method('getId')
                        ->will($this->returnValue(123));

                    return $mockOwner;
                }));


        $config = [
            'zf-hal' => [
                'metadata_map' => [
                    $this->mockValue->getTypeClass()->name => [
                        'route_name' => 'my-route',
                        'zf-doctrine-querybuilder-options' => [
                            'filter_key' => 'my-filter-key',
                        ],
                    ],
                ],
            ],
        ];

        $mock = $this->prophesize(ServiceManager::class);
        $mock->get('config')->willReturn($config);

        $this->hydrator = new CollectionLink();
        $this->hydrator->setServiceManager($mock->reveal());
    }

    public function mappingDataProvider()
    {
        return [
            // OneToMany relation
            [
                [
                    'fieldName' => 'posts',
                    'mappedBy' => 'blog',
                    'targetEntity' => 'Application\\Model\\Post',
                    'cascade' => [],
                    'orphanRemoval' => false,
                    'fetch' => 2,
                    'type' => 4,
                    'inversedBy' => null,
                    'isOwningSide' => false,
                    'sourceEntity' => 'Application\\Model\\Blog',
                    'isCascadeRemove' => false,
                    'isCascadePersist' => false,
                    'isCascadeRefresh' => false,
                    'isCascadeMerge' => false,
                    'isCascadeDetach' => false,
                ],
                [
                    'query' => [
                        'my-filter-key' => [
                            [
                                'field' => 'blog',
                                'type' => 'eq',
                                'value' => 123,
                            ],
                        ],
                    ],
                ],
            ],
            // ManyToMany relation
            [
                [
                    'fieldName' => 'posts',
                    'joinTable' =>
                    [
                        'name' => 'tag_post',
                        'joinColumns' => [
                            [
                                'name' => 'tag_id',
                                'referencedColumnName' => 'id',
                                'onDelete' => 'CASCADE',
                            ],
                        ],
                        'inverseJoinColumns' => [
                            [
                                'name' => 'post_id',
                                'referencedColumnName' => 'id',
                                'onDelete' => 'CASCADE',
                            ],
                        ],
                    ],
                    'targetEntity' => 'Application\\Model\\Post',
                    'mappedBy' => null,
                    'inversedBy' => 'tags',
                    'cascade' => [],
                    'orphanRemoval' => false,
                    'fetch' => 2,
                    'type' => 8,
                    'isOwningSide' => true,
                    'sourceEntity' => 'Application\\Model\\Tag',
                    'isCascadeRemove' => false,
                    'isCascadePersist' => false,
                    'isCascadeRefresh' => false,
                    'isCascadeMerge' => false,
                    'isCascadeDetach' => false,
                    'joinTableColumns' => [
                        'tag_id',
                        'post_id',
                    ],
                    'isOnDeleteCascade' => true,
                    'relationToSourceKeyColumns' => [
                        'tag_id' => 'id',
                    ],
                    'relationToTargetKeyColumns' => [
                        'post_id' => 'id',
                    ],
                ],
                [
                    'query' => [
                        'my-filter-key' => [
                            [
                                'field' => 'tags',
                                'type' => 'ismemberof',
                                'value' => 123,
                            ],
                        ],
                    ],
                ],
            ],
            // ManyToMany inversed relation
            [
                [
                    'fieldName' => 'tags',
                    'joinTable' => [],
                    'targetEntity' => 'Application\\Model\\Tag',
                    'mappedBy' => 'posts',
                    'inversedBy' => null,
                    'cascade' => [],
                    'orphanRemoval' => false,
                    'fetch' => 2,
                    'type' => 8,
                    'isOwningSide' => false,
                    'sourceEntity' => 'Application\\Model\\Post',
                    'isCascadeRemove' => false,
                    'isCascadePersist' => false,
                    'isCascadeRefresh' => false,
                    'isCascadeMerge' => false,
                    'isCascadeDetach' => false,
                ],
                [
                    'query' => [
                        'my-filter-key' => [
                            [
                                'field' => 'posts',
                                'type' => 'ismemberof',
                                'value' => 123,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider mappingDataProvider
     * @param array $mapping
     * @param array $expected
     */
    public function testStuff(array $mapping, array $expected)
    {
        $this->mockValue->expects($this->any())
                ->method('getMapping')
                ->will($this->returnValue($mapping));

        $actual = $this->hydrator->extract($this->mockValue);
        $this->assertEquals('my-route', $actual->getRoute());
        $this->assertEquals(['id' => null], $actual->getRouteParams());
        $this->assertEquals($expected, $actual->getRouteOptions());
    }
}
