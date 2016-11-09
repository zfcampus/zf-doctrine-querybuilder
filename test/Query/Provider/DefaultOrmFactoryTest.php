<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZFTest\Doctrine\QueryBuilder\Query\Provider;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\Doctrine\QueryBuilder\Query\Provider\DefaultOrm;
use ZF\Doctrine\QueryBuilder\Query\Provider\DefaultOrmFactory;

class DefaultOrmFactoryTest extends TestCase
{
    public function testInvokableFactoryReturnsDefaultOdmQueryProvider()
    {
        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class)->reveal();

        $factory = new DefaultOrmFactory();
        $provider = $factory($serviceLocator);

        $this->assertInstanceOf(DefaultOrm::class, $provider);
        $this->assertAttributeSame($serviceLocator, 'serviceLocator', $provider);
    }
}
