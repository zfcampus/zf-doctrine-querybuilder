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

        [$typeCastMetaData, $typeCastFieldName] = $this->getTypeCastParams($queryBuilder, $metadata, $option['field'], $option['alias']);
        $fieldType = $typeCastMetaData->getTypeOfField($typeCastFieldName);

        $format = isset($option['format']) ? $option['format'] : null;

        $from = $this->typeCastField($typeCastMetaData, $typeCastFieldName, $option['from'], $format);
        $to = $this->typeCastField($typeCastMetaData, $typeCastFieldName, $option['to'], $format);

        $fromParameter = uniqid('a1');
        $toParameter = uniqid('a2');

        $queryBuilder->$queryType(
            $queryBuilder
                ->expr()
                ->between(
                    sprintf('%s.%s', $option['alias'], $option['field']),
                    sprintf(':%s', $fromParameter),
                    sprintf(':%s', $toParameter)
                )
        );
        $queryBuilder->setParameter($fromParameter, $from, $fieldType);
        $queryBuilder->setParameter($toParameter, $to, $fieldType);
    }
}
