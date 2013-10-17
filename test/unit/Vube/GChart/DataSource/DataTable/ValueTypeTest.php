<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\DataTable\test;
use Vube\GChart\DataSource\Date;
use Vube\GChart\DataSource\DataTable\ValueType;


/**
 * ValueTypeTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class ValueTypeTest extends \PHPUnit_Framework_TestCase
{
	public function testValueTypeString()
	{
		$type = new ValueType(ValueType::STRING);

		$this->assertTrue($type->isSameDataType('data'), "'data' is a STRING type");

		$this->assertFalse($type->isSameDataType(true), "true is not a STRING type");
		$this->assertFalse($type->isSameDataType(null), "null is not a STRING type");
		$this->assertFalse($type->isSameDataType(0), "0 is not a STRING type");
		$this->assertFalse($type->isSameDataType(1.23), "1.23 is not a STRING type");
		$this->assertFalse($type->isSameDataType(new Date()), "Date() object is not a STRING type");
		$this->assertFalse($type->isSameDataType(new \ArrayObject()), "ArrayObject() object is not a STRING type");
		$this->assertFalse($type->isSameDataType(array()), "array is not a STRING type");
	}

	public function testValueTypeNumber()
	{
		$type = new ValueType(ValueType::NUMBER);

		$this->assertTrue($type->isSameDataType(0), "0 is a NUMBER type");
		$this->assertTrue($type->isSameDataType(1.23), "1.23 is a NUMBER type");

		$this->assertFalse($type->isSameDataType('data'), "'data' is a NUMBER type");
		$this->assertFalse($type->isSameDataType(true), "true is not a NUMBER type");
		$this->assertFalse($type->isSameDataType(null), "null is not a NUMBER type");
		$this->assertFalse($type->isSameDataType(new Date()), "Date() object is not a NUMBER type");
		$this->assertFalse($type->isSameDataType(new \ArrayObject()), "ArrayObject() object is not a NUMBER type");
		$this->assertFalse($type->isSameDataType(array()), "array is not a NUMBER type");
	}

	public function testValueTypeBoolean()
	{
		$type = new ValueType(ValueType::BOOLEAN);

		$this->assertTrue($type->isSameDataType(true), "true is a BOOLEAN type");
		$this->assertTrue($type->isSameDataType(false), "false is a BOOLEAN type");

		$this->assertFalse($type->isSameDataType('data'), "'data' is not a BOOLEAN type");
		$this->assertFalse($type->isSameDataType(null), "null is not a BOOLEAN type");
		$this->assertFalse($type->isSameDataType(0), "0 is not a BOOLEAN type");
		$this->assertFalse($type->isSameDataType(1.23), "1.23 is not a BOOLEAN type");
		$this->assertFalse($type->isSameDataType(new Date()), "Date() object is not a BOOLEAN type");
		$this->assertFalse($type->isSameDataType(new \ArrayObject()), "ArrayObject() object is not a BOOLEAN type");
		$this->assertFalse($type->isSameDataType(array()), "array is not a BOOLEAN type");
	}

	public function testValueTypeDate()
	{
		$type = new ValueType(ValueType::DATE);

		$this->assertTrue($type->isSameDataType(new Date()), "Date() object is a DATE type");

		$this->assertFalse($type->isSameDataType(true), "true is a DATE type");
		$this->assertFalse($type->isSameDataType('data'), "'data' is not a DATE type");
		$this->assertFalse($type->isSameDataType(null), "null is not a DATE type");
		$this->assertFalse($type->isSameDataType(0), "0 is not a DATE type");
		$this->assertFalse($type->isSameDataType(1.23), "1.23 is not a DATE type");
		$this->assertFalse($type->isSameDataType(new \ArrayObject()), "ArrayObject() object is not a DATE type");
		$this->assertFalse($type->isSameDataType(array()), "array is not a DATE type");
	}

	public function testCopyConstructor()
	{
		$type1 = new ValueType(ValueType::NUMBER);
		$type2 = new ValueType($type1);

		$this->assertSame($type1->getTypeId(), $type2->getTypeId(), "type2 must be of the same type as type1");
	}

	public function testConstructorWithInvalidValue()
	{
		$this->setExpectedException('\\Vube\\GChart\\DataSource\\Exception\\NoSuchValueTypeException');
		$type = new ValueType('no-such-type-value');
	}

	public function testConstructorWithWrongDataType()
	{
		$this->setExpectedException('\\Vube\\GChart\\DataSource\\Exception\\TypeMismatchException');
		$type = new ValueType(null);
	}
}