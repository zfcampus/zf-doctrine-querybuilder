<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZFTest\Doctrine\QueryBuilder\Filter;

use PHPUnit\Framework\TestCase;
use ZF\Doctrine\QueryBuilder\Filter\ODM\TypeCaster;

class ODMTypeCasterTest extends TestCase
{
    private $typeCaster;

    protected function setUp()
    {
        $this->typeCaster = new TypeCaster();
    }

    public function testTypeCastingToInteger()
    {
        $metadata = new \stdClass();
        $metadata->fieldMappings = [
            'field' => [
                'type' => 'int'
            ],
        ];

        $field = 'field';
        $value = '2144211244';
        $format = null;
        $doNotTypecastDateTime = false;

        $result = $this->typeCaster->typeCastField($metadata, $field, $value, $format, $doNotTypecastDateTime);
        $this->assertSame(2144211244, $result);
    }

    public function testTypeCastingToBoolean()
    {
        $metadata = new \stdClass();
        $metadata->fieldMappings = [
            'field' => [
                'type' => 'boolean'
            ],
        ];

        $field = 'field';
        $value = 'abc';
        $format = null;
        $doNotTypecastDateTime = false;

        $result = $this->typeCaster->typeCastField($metadata, $field, $value, $format, $doNotTypecastDateTime);
        $this->assertSame(true, $result);

        $value = 0;
        $result = $this->typeCaster->typeCastField($metadata, $field, $value, $format, $doNotTypecastDateTime);
        $this->assertSame(false, $result);
    }

    public function testTypeCastingToFloat()
    {
        $metadata = new \stdClass();
        $metadata->fieldMappings = [
            'field' => [
                'type' => 'float'
            ],
        ];

        $field = 'field';
        $value = '1.242';
        $format = null;
        $doNotTypecastDateTime = false;

        $result = $this->typeCaster->typeCastField($metadata, $field, $value, $format, $doNotTypecastDateTime);
        $this->assertSame(1.242, $result);
    }

    public function testTypeCastingToString()
    {
        $metadata = new \stdClass();
        $metadata->fieldMappings = [
            'field' => [
                'type' => 'string'
            ],
        ];

        $field = 'field';
        $value = 1;
        $format = null;
        $doNotTypecastDateTime = false;

        $result = $this->typeCaster->typeCastField($metadata, $field, $value, $format, $doNotTypecastDateTime);

        $this->assertSame('1', $result);
    }

    public function testTypeCastingToDate()
    {
        $metadata = new \stdClass();
        $metadata->fieldMappings = [
            'field' => [
                'type' => 'date'
            ],
        ];

        $field = 'field';
        $value = '2019-09-01 12:19:01';
        $format = null;
        $doNotTypecastDateTime = false;

        $result = $this->typeCaster->typeCastField($metadata, $field, $value, $format, $doNotTypecastDateTime);
        $this->assertInstanceOf(\DateTime::class, $result);
        $this->assertEquals($result->format('Y-m-d H:i:s'), '2019-09-01 12:19:01');
    }

    public function testNoTypeCastingToDateWhenFlaggedSo()
    {
        $metadata = new \stdClass();
        $metadata->fieldMappings = [
            'field' => [
                'type' => 'date'
            ],
        ];

        $field = 'field';
        $value = '2019-09-01 12:19:01';
        $format = null;
        $doNotTypecastDateTime = true;

        $result = $this->typeCaster->typeCastField($metadata, $field, $value, $format, $doNotTypecastDateTime);
        $this->assertSame($result, $value);
    }
}
