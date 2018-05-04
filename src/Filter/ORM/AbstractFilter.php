<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder\Filter\ORM;

use DateTime;
use ZF\Doctrine\QueryBuilder\Filter\FilterInterface;
use ZF\Doctrine\QueryBuilder\Filter\Service\ORMFilterManager;

abstract class AbstractFilter implements FilterInterface
{
    abstract public function filter($queryBuilder, $metadata, $option);

    protected $filterManager;

    public function __construct($params)
    {
        $this->setFilterManager($params[0]);
    }

    public function setFilterManager(ORMFilterManager $filterManager)
    {
        $this->filterManager = $filterManager;
        return $this;
    }

    public function getFilterManager()
    {
        return $this->filterManager;
    }

    protected function typeCastField($metadata, $field, $value, $format, $doNotTypecastDatetime = false)
    {
        if (! isset($metadata->fieldMappings[$field])) {
            return $value;
        }

        switch ($metadata->fieldMappings[$field]['type']) {
            case 'string':
                settype($value, 'string');
                break;
            case 'integer':
            case 'smallint':
            #case 'bigint':  // Don't try to manipulate bigints?
                settype($value, 'integer');
                break;
            case 'boolean':
                settype($value, 'boolean');
                break;
            case 'decimal':
            case 'float':
                settype($value, 'float');
                break;
            case 'date':
                // For dates set time to midnight
                if ($value && ! $doNotTypecastDatetime) {
                    if (! $format) {
                        $format = 'Y-m-d';
                    }
                    $value = DateTime::createFromFormat($format, $value);
                    $value = DateTime::createFromFormat('Y-m-d H:i:s', $value->format('Y-m-d') . ' 00:00:00');
                }
                break;
            case 'time':
                if ($value && ! $doNotTypecastDatetime) {
                    if (! $format) {
                        $format = 'H:i:s';
                    }
                    $value = DateTime::createFromFormat($format, $value);
                }
                break;
            case 'datetime':
                if ($value && ! $doNotTypecastDatetime) {
                    if (! $format) {
                        $format = 'Y-m-d H:i:s';
                    }
                    $value = DateTime::createFromFormat($format, $value);
                }
                break;
            default:
                break;
        }

        return $value;
    }

    protected function getTypeCastParams($queryBuilder, $metadata, $fieldName, $alias)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
        /** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
        /** @var \Doctrine\ORM\Query\Expr\Join $joinObj */
        $typeCastMetaData = $this->getMetadataForAlias($queryBuilder, $metadata, $alias);
        if ($typeCastMetaData->hasField($fieldName)) {
            $typeCastFieldName = $fieldName;
        } else {
            $typeCastFieldName = $typeCastMetaData->getSingleAssociationReferencedJoinColumnName($fieldName); //remote
            $typeCastMetaData = $queryBuilder->getEntityManager()->getMetadataFactory()->getMetadataFor(
                $typeCastMetaData->getAssociationTargetClass($fieldName)
            );
        }

        return [$typeCastMetaData, $typeCastFieldName];
    }

    protected function getMetadataForAlias($queryBuilder, $metadata, $alias)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
        /** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
        /** @var \Doctrine\ORM\Query\Expr\Join $joinObj */
        $joinObj = null;
        foreach ($queryBuilder->getDQLPart('join') as $joinPart) {
            /** @var \Doctrine\ORM\Query\Expr\Join $obj */
            foreach ($joinPart as $obj) {
                if ($obj->getAlias() == $alias) {
                    $joinObj = $obj;
                    break 2;
                }
            }
        }

        if ($joinObj) {
            [$parentAlias, $joinField] = explode('.', $joinObj->getJoin());
            $parentMetadata = $this->getMetadataForAlias($queryBuilder, $metadata, $parentAlias);

            return $queryBuilder->getEntityManager()->getMetadataFactory()->getMetadataFor(
                $parentMetadata->getAssociationTargetClass($joinField)
            );
        } else {
            return $metadata;
        }
    }
}
