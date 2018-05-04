<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder\Filter\ORM;

class GreaterThanOrEquals extends AbstractFilter
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

        $value = $this->typeCastField($typeCastMetaData, $typeCastFieldName, $option['value'], $format);

        $parameter = uniqid('a');
        $queryBuilder->$queryType(
            $queryBuilder
                ->expr()
                ->gte($option['alias'] . '.' . $option['field'], ':' . $parameter)
        );
        $queryBuilder->setParameter($parameter, $value, $fieldType);
    }
}
