<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder\Query\Provider;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractPluginManager;

class DefaultOrmFactory
{
    /**
     * Create and return DefaultOrm instance.
     *
     * @param ContainerInterface $container
     * @return DefaultOrm
     */
    public function __invoke(ContainerInterface $container)
    {
        if ($container instanceof AbstractPluginManager) {
            $container = $container->getServiceLocator() ?: $container;
        }

        $provider = new DefaultOrm();
        $provider->setServiceLocator($container);

        return $provider;
    }
}
