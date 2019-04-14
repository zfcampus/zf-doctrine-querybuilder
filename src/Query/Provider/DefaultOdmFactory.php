<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder\Query\Provider;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractPluginManager;

class DefaultOdmFactory
{
    /**
     * Create and return DefaultOdm instance.
     *
     * @param ContainerInterface $container
     * @return DefaultOdm
     */
    public function __invoke(ContainerInterface $container)
    {
        if ($container instanceof AbstractPluginManager) {
            $container = $container->getServiceLocator() ?: $container;
        }

        $provider = new DefaultOdm();
        $provider->setServiceLocator($container);

        return $provider;
    }
}
