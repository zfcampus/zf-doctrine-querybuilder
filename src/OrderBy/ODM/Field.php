<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder\OrderBy\ODM;

use ZF\Doctrine\QueryBuilder\Exception\InvalidOrderByException;

class Field extends AbstractOrderBy
{
    public function orderBy($queryBuilder, $metadata, $option)
    {
        if (! isset($option['direction']) || ! in_array(strtolower($option['direction']), ['asc', 'desc'])) {
            throw new InvalidOrderByException(
                'Invalid direction in order-by directive for field [' . $option['field'] . ']'
            );
        }

        $queryBuilder->sort($option['field'], $option['direction']);
    }
}
