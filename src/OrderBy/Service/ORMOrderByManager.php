<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder\OrderBy\Service;

use RuntimeException;
use ZF\Doctrine\QueryBuilder\OrderBy\OrderByInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Doctrine\ORM\QueryBuilder;

class ORMOrderByManager extends AbstractPluginManager
{
    protected $invokableClasses = array();
    protected $instanceOf = OrderByInterface::class;

    public function orderBy(QueryBuilder $queryBuilder, $metadata, $orderBy)
    {
        foreach ($orderBy as $option) {
            if (! isset($option['type']) or ! $option['type']) {
                // @codeCoverageIgnoreStart
                throw new RuntimeException('Array element "type" is required for all orderby directives');
            }
            // @codeCoverageIgnoreEnd

            $orderByHandler = $this->get(strtolower($option['type']), array($this));

            $orderByHandler->orderBy($queryBuilder, $metadata, $option);
        }
    }

    /**
     * @param mixed $orderBy
     *
     * @return void
     * @throws InvalidServiceException
     */
    public function validate($orderBy)
    {
        if (! $orderBy instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                'Invalid plugin "%s" created; not an instance of %s',
                get_class($orderBy),
                $this->instanceOf
            ));
        }
    }

    /**
     * @param mixed $orderBy
     *
     * @return void
     * @throws RuntimeException
     */
    public function validatePlugin($orderBy)
    {
        try {
            $this->validate($orderBy);
        } catch (InvalidServiceException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
