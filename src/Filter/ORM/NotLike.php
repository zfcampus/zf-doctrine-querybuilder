<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder\Filter\ORM;

class NotLike extends AbstractFilter
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

        if (! isset($option['alias'])) {
            $option['alias'] = 'row';
        }

        $field = '';
        if (false !== $option['alias']) {
            $field = $option['alias'] . '.';
        }
        $field .= $option['field'];

        $queryBuilder->$queryType(
            $queryBuilder
                ->expr()
                ->notlike(
                    $field,
                    $queryBuilder->expr()->literal($option['value'])
                )
        );
    }
}
