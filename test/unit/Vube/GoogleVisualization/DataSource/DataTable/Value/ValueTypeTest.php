<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\DataTable\Value\test;

use Vube\GoogleVisualization\DataSource\Date;
use Vube\GoogleVisualization\DataSource\DataTable\Value\ValueType;


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
		$this->assertSame('string', $type->getTypeName());
	}

	public function testValueTypeNumber()
	{
		$type = new ValueType(ValueType::NUMBER);
		$this->assertSame('number', $type->getTypeName());
	}

	public function testValueTypeBoolean()
	{
		$type = new ValueType(ValueType::BOOLEAN);
		$this->assertSame('boolean', $type->getTypeName());
	}

	public function testValueTypeDate()
	{
		$type = new ValueType(ValueType::DATE);
		$this->assertSame('date', $type->getTypeName());
	}

	public function testCopyConstructor()
	{
		$type1 = new ValueType(ValueType::NUMBER);
		$type2 = new ValueType($type1);

		$this->assertSame($type1->getCode(), $type2->getCode(), "type2 must be of the same type as type1");
	}

	public function testConstructorWithInvalidValue()
	{
		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Exception\\NoSuchValueTypeException');
		$type = new ValueType('no-such-type-value');
	}
}