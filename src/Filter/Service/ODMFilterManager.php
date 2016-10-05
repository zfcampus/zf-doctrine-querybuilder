<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder\Filter\Service;

use RuntimeException;
use ZF\ApiProblem\ApiProblem;
use ZF\Doctrine\QueryBuilder\Filter\FilterInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata as Metadata;

class ODMFilterManager extends AbstractPluginManager
{
    protected $invokableClasses = array();
    protected $instanceOf = FilterInterface::class;

    public function filter(QueryBuilder $queryBuilder, Metadata $metadata, $filters)
    {
        foreach ($filters as $option) {
            if (! isset($option['type']) or ! $option['type']) {
                // @codeCoverageIgnoreStart
                return new ApiProblem(500, 'Array element "type" is required for all filters');
            }
            // @codeCoverageIgnoreEnd

            try {
                $filter = $this->get(strtolower($option['type']), array($this));
            } catch (InvalidServiceException $e) {
                // @codeCoverageIgnoreStart
                return new ApiProblem(500, $e->getMessage());
            }

            // @codeCoverageIgnoreEnd
            $filter->filter($queryBuilder, $metadata, $option);
        }
    }

    /**
     * @param mixed $filter
     *
     * @return void
     * @throws Exception\RuntimeException
     */
    public function validate($filter)
    {
        if (! $filter instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                'Invalid plugin "%s" created; not an instance of %s',
                get_class($filter),
                $this->instanceOf
            ));
        }
    }

    /**
     * @param mixed $filter
     *
     * @return void
     * @throws Exception\RuntimeException
     */
    public function validatePlugin($filter)
    {
        try {
            $this->validate($filter);
        } catch (InvalidServiceException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
