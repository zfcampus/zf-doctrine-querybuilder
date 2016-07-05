<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder\Filter\ORM;

class Between extends AbstractFilter
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

        $format = isset($option['format']) ? $option['format'] : null;

        $from = $this->typeCastField($metadata, $option['field'], $option['from'], $format);
        $to = $this->typeCastField($metadata, $option['field'], $option['to'], $format);

        $fromParameter = uniqid('a1');
        $toParameter = uniqid('a2');

        $queryBuilder->$queryType(
            $queryBuilder
                ->expr()
                ->between(
                    $field,
                    sprintf(':%s', $fromParameter),
                    sprintf(':%s', $toParameter)
                )
        );
        $queryBuilder->setParameter($fromParameter, $from);
        $queryBuilder->setParameter($toParameter, $to);
    }
}
