<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder\Filter\ORM;

use ZF\Doctrine\QueryBuilder\Exception\InvalidFilterException;

class InnerJoin extends AbstractFilter
{
    public function filter($queryBuilder, $metadata, $option)
    {
        if (! isset($option['field']) || ! $option['field']) {
            throw new InvalidFilterException('Field must be specified for inner join');
        }

        if (! isset($option['alias']) || ! $option['alias']) {
            throw new InvalidFilterException('Alias must be specified for inner join');
        }

        if (! isset($option['parentAlias']) || ! $option['parentAlias']) {
            $option['parentAlias'] = 'row';
        }

        if (! isset($option['conditionType']) && isset($option['condition'])) {
            throw new InvalidFilterException('A conditionType must be specified for a condition');
        }

        if (! isset($option['condition']) && isset($option['conditionType'])) {
            throw new InvalidFilterException('A condition must be specified for a conditionType');
        }

        if (! isset($option['conditionType'])) {
            $option['conditionType'] = null;
        }

        if (! isset($option['condition'])) {
            $option['condition'] = null;
        }

        if (! isset($option['indexBy'])) {
            $option['indexBy'] = null;
        }

        $queryBuilder->innerJoin(
            $option['parentAlias'] . '.' . $option['field'],
            $option['alias'],
            $option['conditionType'],
            $option['condition'],
            $option['indexBy']
        );
    }
}
