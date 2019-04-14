<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder\Hydrator\Strategy;

use DoctrineModule\Stdlib\Hydrator\Strategy\AbstractCollectionStrategy;
use Zend\Filter\FilterChain;
use Zend\Hydrator\Strategy\StrategyInterface;
use Zend\ServiceManager\ServiceManager;
use ZF\Hal\Link\Link;

/**
 * A field-specific hydrator for collections.
 *
 * This version is for use with zend-hyrator versions 1 and 2, and will be
 * aliased to CollectionLink in those versions.
 *
 * @returns Link
 */
class CollectionLinkHydratorV2 extends AbstractCollectionStrategy implements StrategyInterface
{
    protected $serviceManager;

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }

    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    public function extract($value)
    {
        $config = $this->getServiceManager()->get('config');
        if (! method_exists($value, 'getTypeClass')
            || ! isset($config['zf-hal']['metadata_map'][$value->getTypeClass()->name])
        ) {
            return;
        }

        $config = $config['zf-hal']['metadata_map'][$value->getTypeClass()->name];
        $mapping = $value->getMapping();

        $filter = new FilterChain();
        $filter->attachByName('WordCamelCaseToUnderscore')
            ->attachByName('StringToLower');

        $link = new Link($filter($mapping['fieldName']));
        $link->setRoute($config['route_name']);
        $link->setRouteParams(['id' => null]);

        if (isset($config['zf-doctrine-querybuilder-options']['filter_key'])) {
            $filterKey = $config['zf-doctrine-querybuilder-options']['filter_key'];
        } else {
            $filterKey = 'filter';
        }

        $filterValue = [
            'field' => $mapping['mappedBy'] ? : $mapping['inversedBy'],
            'type' => isset($mapping['joinTable']) ? 'ismemberof' : 'eq',
            'value' => $value->getOwner()->getId(),
        ];

        $link->setRouteOptions([
            'query' => [
                $filterKey => [
                    $filterValue,
                ],
            ],
        ]);

        return $link;
    }

    public function hydrate($value)
    {
        // Hydration is not supported for collections.
        // A call to PATCH will use hydration to extract then hydrate
        // an entity. In this process a collection will be included
        // so no error is thrown here.
    }
}
