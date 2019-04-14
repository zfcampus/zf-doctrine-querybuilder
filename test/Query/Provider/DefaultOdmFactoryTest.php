<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZFTest\Doctrine\QueryBuilder\Query\Provider;

use PHPUnit\Framework\TestCase;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\Doctrine\QueryBuilder\Query\Provider\DefaultOdm;
use ZF\Doctrine\QueryBuilder\Query\Provider\DefaultOdmFactory;

class DefaultOdmFactoryTest extends TestCase
{
    public function testInvokableFactoryReturnsDefaultOdmQueryProvider()
    {
        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class)->reveal();

        $factory = new DefaultOdmFactory();
        $provider = $factory($serviceLocator);

        $this->assertInstanceOf(DefaultOdm::class, $provider);
        $this->assertAttributeSame($serviceLocator, 'serviceLocator', $provider);
    }

    public function testInvokableFactoryReturnsDefaultOdmQueryProviderWhenCreatedViaAbstractPluginManager()
    {
        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class)->reveal();
        $abstractPluginManager = $this->prophesize(AbstractPluginManager::class);
        $abstractPluginManager->getServiceLocator()->willReturn($serviceLocator);

        $factory = new DefaultOdmFactory();
        $provider = $factory($abstractPluginManager->reveal());

        $this->assertInstanceOf(DefaultOdm::class, $provider);
        $this->assertAttributeSame($serviceLocator, 'serviceLocator', $provider);
    }
}
