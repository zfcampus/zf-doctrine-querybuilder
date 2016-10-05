<?php

namespace ZF\Doctrine\QueryBuilder\Hydrator\Strategy;

use Zend\Hydrator\Strategy\StrategyInterface;
use DoctrineModule\Stdlib\Hydrator\Strategy\AbstractCollectionStrategy;
use ZF\Hal\Link\Link;
use Zend\ServiceManager\ServiceManager;
use Zend\Filter\FilterChain;

/**
 * A field-specific hydrator for collections.
 *
 * @returns Link
 */
class CollectionLink extends AbstractCollectionStrategy implements StrategyInterface
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
        $config = $this->getServiceManager()->get('Config');
        if (!method_exists($value, 'getTypeClass')
            || !isset($config['zf-hal']['metadata_map'][$value->getTypeClass()->name])) {
            return;
        }

        $config = $config['zf-hal']['metadata_map'][$value->getTypeClass()->name];
        $mapping = $value->getMapping();

        $filter = new FilterChain();
        $filter->attachByName('WordCamelCaseToUnderscore')
            ->attachByName('StringToLower');

        $link = new Link($filter($mapping['fieldName']));
        $link->setRoute($config['route_name']);
        $link->setRouteParams(array('id' => null));

        if (isset($config['zf-doctrine-querybuilder-options']['filter_key'])) {
            $filterKey = $config['zf-doctrine-querybuilder-options']['filter_key'];
        } else {
            $filterKey = 'filter';
        }

        $filterValue = array(
            'field' => $mapping['mappedBy'] ? : $mapping['inversedBy'],
            'type' =>isset($mapping['joinTable']) ? 'ismemberof' : 'eq',
            'value' => $value->getOwner()->getId(),
        );

        $link->setRouteOptions(array(
            'query' => array(
                $filterKey => array(
                    $filterValue,
                ),
            ),
        ));

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
