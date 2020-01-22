<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Doctrine\QueryBuilder\Filter\ODM;

use ZF\Doctrine\QueryBuilder\Filter\FilterInterface;
use ZF\Doctrine\QueryBuilder\Filter\TypeCastInterface;

abstract class AbstractFilter implements FilterInterface
{
    abstract public function filter($queryBuilder, $metadata, $option);

    protected $typeCaster;

    public function __construct($params)
    {
        if (isset($params[1])) {
            $this->setTypeCaster($params[1]);
        }
    }

    public function getTypeCaster()
    {
        if ($this->typeCaster === null) {
            $this->typeCaster = new TypeCaster();
        }

        return $this->typeCaster;
    }

    public function setTypeCaster(TypeCastInterface $typeCaster)
    {
        $this->typeCaster = $typeCaster;
        return $this;
    }

    protected function typeCastField($metadata, $field, $value, $format = null, $doNotTypecastDatetime = false)
    {
        return $this->getTypeCaster()->typeCastField($metadata, $field, $value, $format, $doNotTypecastDatetime);
    }
}
