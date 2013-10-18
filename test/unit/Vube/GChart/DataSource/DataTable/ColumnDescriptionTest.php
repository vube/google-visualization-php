<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\DataTable\test;

use Vube\GChart\DataSource\DataTable\ColumnDescription;
use Vube\GChart\DataSource\DataTable\Value\ValueType;


/**
 * ColumnDescriptionTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class ColumnDescriptionTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructor()
	{
		$cd = new ColumnDescription('columnName', ValueType::NUMBER, 'pretty column label');

		$this->assertSame('columnName', $cd->getId(), "id must match");
		$this->assertSame(ValueType::NUMBER, $cd->getType()->getCode(), "type must match");
		$this->assertSame('pretty column label', $cd->getLabel(), "label must match");
		$this->assertSame('', $cd->getPattern(), "Default pattern is empty string");
	}

	public function testSetLabel()
	{
		$cd = new ColumnDescription('foo', ValueType::STRING);
		$cd->setLabel('test-label');

		$this->assertSame('test-label', $cd->getLabel(), "label value must match");
	}

	public function testSetPattern()
	{
		$cd = new ColumnDescription('foo', ValueType::DATE);
		$cd->setPattern('yyyy-MM-dd');

		$this->assertSame('yyyy-MM-dd', $cd->getPattern(), "pattern value must match");
	}

	public function testColumnDescriptionCustomProperties()
	{
		$cd = new ColumnDescription('first', ValueType::STRING);
		$cd->setCustomProperty('a', 'foo');
		$properties = $cd->getCustomProperties();

		$this->assertTrue(is_array($properties), "properties must be an array");
		$this->assertArrayHasKey('a', $properties, "properties[a] must exist");
		$this->assertSame('foo', $cd->getCustomProperty('a'), "getCustomProperty must return expected value");
	}

	public function testGetCustomNonexistentProperty()
	{
		$cd = new ColumnDescription('first', ValueType::STRING);
		$this->assertNull($cd->getCustomProperty('no-such-property'), "Expect null return for no-such-property");
	}

}