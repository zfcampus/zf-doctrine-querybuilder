<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder\Filter\ORM;

use Doctrine\Common\Collections\ArrayCollection;

class AndX extends AbstractFilter
{
    public function filter($queryBuilder, $metadata, $option)
    {
        if (isset($option['where'])) {
            if ($option['where'] === 'and') {
                $queryType = 'andWhere';
            } elseif ($option['where'] === 'or') {
                $queryType = 'orWhere';
            }
        }

        if (! isset($queryType)) {
            $queryType = 'andWhere';
        }

        $andX = $queryBuilder->expr()->andX();
        $em   = $queryBuilder->getEntityManager();
        $qb   = $em->createQueryBuilder();

        foreach ($option['conditions'] as $condition) {
            $filter = $this->getFilterManager()->get(
                strtolower($condition['type']),
                [$this->getFilterManager()]
            );
            $filter->filter($qb, $metadata, $condition);
        }

        $dqlParts = $qb->getDqlParts();
        $andX->addMultiple($dqlParts['where']->getParts());
        $queryBuilder->setParameters(
            new ArrayCollection(array_merge_recursive(
                $queryBuilder->getParameters()->toArray(),
                $qb->getParameters()->toArray()
            ))
        );

        $queryBuilder->$queryType($andX);
    }
}
