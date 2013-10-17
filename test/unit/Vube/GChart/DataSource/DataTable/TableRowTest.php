<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\DataTable\test;
use Vube\GChart\DataSource\DataTable\TableCell;
use Vube\GChart\DataSource\DataTable\TableRow;


/**
 * TableRowTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class TableRowTest extends \PHPUnit_Framework_TestCase
{
	public function testTableRowConstructor()
	{
		$row = new TableRow();
		$rows = $row->getCells();
		$properties = $row->getCustomProperties();

		$this->assertTrue(is_array($rows), "cells must be an array");
		$this->assertEquals(0, count($rows), "cells must be an empty array");
		$this->assertSame(0, $row->getCellCount(), "getCellCount must return zero");
		$this->assertTrue(is_array($properties), "properties must be an array");
		$this->assertEquals(0, count($properties), "properties must be an empty array");
	}

	public function testTableRowWithProperties()
	{
		$row = new TableRow();
		$row->setCustomProperty('a', 'foo');
		$properties = $row->getCustomProperties();

		$this->assertTrue(is_array($properties), "properties must be an array");
		$this->assertArrayHasKey('a', $properties, "properties[a] must exist");
		$this->assertSame('foo', $row->getCustomProperty('a'), "getCustomProperty must return expected value");
	}

	public function testGetCustomNonexistentProperty()
	{
		$row = new TableRow();
		$this->assertNull($row->getCustomProperty('no-such-property'), "Expect null return for no-such-property");
	}

	public function testAddCell()
	{
		$row = new TableRow();
		$row->addCell(new TableCell('a'));
		$row->addCell(new TableCell('b'));

		$this->assertSame(2, $row->getCellCount(), "getCellCount must return expected result");

		$cell0 = $row->getCell(0);
		$cell1 = $row->getCell(1);

		$this->assertSame('a', $cell0->getValue(), "first cell value must match");
		$this->assertSame('b', $cell1->getValue(), "second cell value must match");
	}

	public function testAddCellWithRawValue()
	{
		$row = new TableRow();
		$row->addCell('value');

		$this->assertSame(1, $row->getCellCount(), "getCellCount must return expected result");

		$cell0 = $row->getCell(0);

		$this->assertSame('value', $cell0->getValue(), "cell value must match");
	}

	public function testSetCell()
	{
		$row = new TableRow();
		$row->addCell(new TableCell('add value'));
		$row->setCell(0, new TableCell('set value'));

		$this->assertSame(1, $row->getCellCount(), "getCellCount must return expected result");

		$cell0 = $row->getCell(0);

		$this->assertSame('set value', $cell0->getValue(), "cell value must match");
	}

	public function testSetCellWithRawValue()
	{
		$row = new TableRow();
		$row->addCell('add value');
		$row->setCell(0, 'set value');

		$this->assertSame(1, $row->getCellCount(), "getCellCount must return expected result");

		$cell0 = $row->getCell(0);

		$this->assertSame('set value', $cell0->getValue(), "cell value must match");
	}

	public function testGetCellInvalidIndex()
	{
		$row = new TableRow();
		$this->setExpectedException('\\Vube\\GChart\\DataSource\\Exception\\IndexOutOfBoundsException');
		$row->getCell(0);
	}

	public function testSetCellInvalidIndex()
	{
		$row = new TableRow();
		$this->setExpectedException('\\Vube\\GChart\\DataSource\\Exception\\IndexOutOfBoundsException');
		$row->setCell(0, 'value');
	}
}