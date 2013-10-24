<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\DataTable\test;

use Vube\GoogleVisualization\DataSource\DataTable\TableCell;
use Vube\GoogleVisualization\DataSource\DataTable\Value\BooleanValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\DateValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\NumberValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\TextValue;
use Vube\GoogleVisualization\DataSource\Date;
use Vube\GoogleVisualization\DataSource\Exception;


class SomeUnknownClass {};


/**
 * TableCellTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class TableCellTest extends \PHPUnit_Framework_TestCase
{
	public function testTableCellValue()
	{
		$value = new TextValue('foo');
		$cell = new TableCell($value);
		$properties = $cell->getCustomProperties();

		$this->assertEquals($value, $cell->getValue(), "Table cell value must match input");
		$this->assertSame(null, $cell->getFormattedValue(), "Table cell must have null formatted value");
		$this->assertTrue(is_array($properties), "properties must be an array");
		$this->assertEquals(0, count($properties), "properties must be an empty array");
	}

	public function testTableCellValueWithFormat()
	{
		$value = new NumberValue(1.29159386);
		$formattedValue = sprintf("%0.2f", $value->getRawValue());
		$cell = new TableCell($value, $formattedValue);
		$properties = $cell->getCustomProperties();

		$this->assertEquals($value, $cell->getValue(), "Table cell value must match input");
		$this->assertSame($formattedValue, $cell->getFormattedValue(), "Table cell formatted value must match input");
		$this->assertTrue(is_array($properties), "properties must be an array");
		$this->assertEquals(0, count($properties), "properties must be an empty array");
	}

	public function testImplicitValueTypeInt()
	{
		$cell = new TableCell(123);
		$value = $cell->getValue();
		$this->assertTrue($value instanceof NumberValue, "Expected 123 to cast to a NumberValue");
	}

	public function testImplicitValueTypeFloat()
	{
		$cell = new TableCell(1.2);
		$value = $cell->getValue();
		$this->assertTrue($value instanceof NumberValue, "Expected 1.2 to cast to a NumberValue");
	}

	public function testImplicitValueTypeBool()
	{
		$cell = new TableCell(true);
		$value = $cell->getValue();
		$this->assertTrue($value instanceof BooleanValue, "Expected true to cast to a BooleanValue");
	}

	public function testImplicitValueTypeDate()
	{
		$cell = new TableCell(new Date());
		$value = $cell->getValue();
		$this->assertTrue($value instanceof DateValue, "Expected Date() to cast to a DateValue");
	}

	public function testImplicitValueTypeString()
	{
		$cell = new TableCell("string");
		$value = $cell->getValue();
		$this->assertTrue($value instanceof TextValue, "Expected 'string' to cast to a TextValue");
	}

	public function testImplicitValueTypeNull()
	{
		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Exception\\TypeMismatchException');
		$cell = new TableCell(null);
	}

	public function testImplicitValueTypeUnknownClass()
	{
		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Exception\\TypeMismatchException');
		$cell = new TableCell(new SomeUnknownClass());
	}

	public function testTableCellValueWithProperties()
	{
		$value = new TextValue('foo');
		$properties = array('a' => 'enabled');
		$cell = new TableCell($value, $value->getRawValue(), $properties);
		$properties = $cell->getCustomProperties();

		$this->assertEquals($value, $cell->getValue(), "Table cell value must match input");
		$this->assertSame($value->getRawValue(), $cell->getFormattedValue(), "Table cell formatted value must match input");
		$this->assertTrue(is_array($properties), "properties must be an array");
		$this->assertArrayHasKey('a', $properties, "properties[a] must exist");
		$this->assertSame($properties['a'], $cell->getCustomProperty('a'), "getCustomProperty must return expected value");
	}

	public function testSetCustomProperty()
	{
		$propertyName = 'property-name';
		$propertyValue = 'value';
		$cell = new TableCell(new TextValue('test'));
		$cell->setCustomProperty($propertyName, $propertyValue);
		$this->assertSame($propertyValue, $cell->getCustomProperty($propertyName), "getCustomProperty must return the property name");
	}

	public function testGetCustomNonexistentProperty()
	{
		$cell = new TableCell(new TextValue('test'));
		$this->assertNull($cell->getCustomProperty('no-such-property'), "Expect null return for no-such-property");
	}
}