<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\DataTable\test;

use Vube\GChart\DataSource\Base\ReasonType;
use Vube\GChart\DataSource\Base\Warning;
use Vube\GChart\DataSource\DataTable\ColumnDescription;
use Vube\GChart\DataSource\DataTable\DataTable;
use Vube\GChart\DataSource\DataTable\TableCell;
use Vube\GChart\DataSource\DataTable\TableRow;
use Vube\GChart\DataSource\DataTable\Value\NumberValue;
use Vube\GChart\DataSource\DataTable\Value\TextValue;
use Vube\GChart\DataSource\DataTable\Value\ValueType;


/**
 * DataTableTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class DataTableTest extends \PHPUnit_Framework_TestCase
{
	public function testConstruct()
	{
		$data = new DataTable();

		$this->assertSame(0, $data->getNumberOfColumns(), "data must contain zero columns");
		$this->assertSame(0, $data->getNumberOfRows(), "data must contain zero rows");
		$this->assertSame(0, $data->getNumberOfWarnings(), "data must contain zero warnings");
		$this->assertSame(array(), $data->getColumnDescriptions(), "column descriptions array must be empty array");
		$this->assertSame(array(), $data->getWarnings(), "data must contain zero warnings");
	}

	public function testAddWarning()
	{
		$data = new DataTable();
		$warning = new Warning(new ReasonType(ReasonType::INVALID_QUERY), "invalid query");
		$data->addWarning($warning);

		$warnings = $data->getWarnings();

		$this->assertSame(1, $data->getNumberOfWarnings(), "data must contain zero warnings");
		$this->assertSame(1, count($warnings), "warnings array must contain 1 element");
		$this->assertSame($warning, $warnings[0], "Returned warning must match input warning");
	}

	public function testAddColumn()
	{
		$col0 = new ColumnDescription('foo', ValueType::STRING);
		$data = new DataTable();
		$data->addColumn($col0);

		$this->assertSame(1, $data->getNumberOfColumns(), "data must contain 2 columns");

		$get0 = $data->getColumnDescription(0);
		$this->assertEquals($col0, $get0, "first column must match");
	}

	public function testAddColumns()
	{
		$col0 = new ColumnDescription('foo', ValueType::STRING);
		$col1 = new ColumnDescription('bar', ValueType::NUMBER);

		$data = new DataTable();
		$data->addColumns(array($col0, $col1));

		$this->assertSame(2, $data->getNumberOfColumns(), "data must contain 2 columns");

		$get0 = $data->getColumnDescription(0);
		$this->assertEquals($col0, $get0, "first column must match");

		$get1 = $data->getColumnDescription(1);
		$this->assertEquals($col1, $get1, "second column must match");
	}

	public function testGetColumnDescription()
	{
		$col0 = new ColumnDescription('first', ValueType::STRING);
		$data = new DataTable();
		$data->addColumn($col0);
		$get0 = $data->getColumnDescription(0);

		$this->assertEquals($col0, $get0, "Retrieved column must match original");
	}

	public function testGetColumnDescriptionOutOfBounds()
	{
		$data = new DataTable();

		$this->setExpectedException('\\Vube\\GChart\\DataSource\\Exception\\IndexOutOfBoundsException');
		$cd = $data->getColumnDescription(0);
	}

	public function testGetColumnDescriptions()
	{
		$data = new DataTable();
		$col0 = new ColumnDescription('foo', ValueType::STRING);
		$col1 = new ColumnDescription('bar', ValueType::NUMBER);
		$data->addColumns(array($col0, $col1));

		$columns = $data->getColumnDescriptions();
		$this->assertSame(2, count($columns), "data must contain 2 columns");

		$this->assertEquals($col0, $columns[0], "first column id must match");
		$this->assertEquals($col1, $columns[1], "first column id must match");
	}

	public function testGetColumnDescriptionById()
	{
		$col0 = new ColumnDescription('first', ValueType::STRING);
		$data = new DataTable();
		$data->addColumn($col0);
		$get0 = $data->getColumnDescriptionById('first');

		$this->assertEquals($col0, $get0, "Retrieved column must match original");
	}

	public function testAddRowToTableWithZeroColumns()
	{
		$data = new DataTable();
		$row = new TableRow();
		$row->addCell(new TextValue('test'));

		$this->setExpectedException('\\Vube\\GChart\\DataSource\\Exception');
		$data->addRow($row);
	}

	public function testAddRow()
	{
		$data = new DataTable();
		$data->addColumn(new ColumnDescription('first', ValueType::STRING));
		$row = new TableRow();
		$row->addCell(new TableCell(new TextValue('value1')));
		$data->addRow($row);

		$this->assertSame(1, $data->getNumberOfRows(), "row count must match");
	}

	public function testAddRowWithTooFewColumns()
	{
		$data = new DataTable();
		$data->addColumn(new ColumnDescription('first', ValueType::STRING));
		$row = new TableRow();

		$this->setExpectedException('\\Vube\\GChart\\DataSource\\Exception\\ColumnCountMismatchException');
		$data->addRow($row);
	}

	public function testAddRowWithTooManyColumns()
	{
		$data = new DataTable();
		$data->addColumn(new ColumnDescription('first', ValueType::STRING));
		$row = new TableRow();
		$row->addCell(new TableCell(new TextValue('value1')));
		$row->addCell(new TableCell(new TextValue('value2')));

		$this->setExpectedException('\\Vube\\GChart\\DataSource\\Exception\\ColumnCountMismatchException');
		$data->addRow($row);
	}

	public function testAddRowWithWrongDataType()
	{
		$data = new DataTable();
		$data->addColumn(new ColumnDescription('first', ValueType::STRING));
		$row = new TableRow();
		$row->addCell(new TableCell(new NumberValue(0)));

		$this->setExpectedException('\\Vube\\GChart\\DataSource\\Exception\\ValueTypeMismatchException');
		$data->addRow($row);
	}

	public function testAddRows()
	{
		$count = 10;
		$rows = array();
		for($i=0; $i<$count; $i++)
		{
			$row = new TableRow();
			$row->addCell(new TableCell(new NumberValue($i)));
			$rows[] = $row;
		}

		$data = new DataTable();
		$data->addColumn(new ColumnDescription('index', ValueType::NUMBER));
		$data->addRows($rows);

		$this->assertSame($count, $data->getNumberOfRows(), "row count must match");
	}

	public function testSetRowsAfterAddRows()
	{
		$count = 10;
		$this->assertTrue($count > 1, "Test doesn't work if count <= 1");
		$rows = array();
		for($i=0; $i<$count; $i++)
		{
			$row = new TableRow();
			$row->addCell(new TableCell(new NumberValue($i)));
			$rows[] = $row;
		}

		$data = new DataTable();
		$data->addColumn(new ColumnDescription('index', ValueType::NUMBER));
		$data->addRows($rows);
		array_shift($rows); // remove 1 element from rows
		$data->setRows($rows); // reset table rows to new $rows

		$this->assertSame($count-1, $data->getNumberOfRows(), "row count must match");
	}

	public function testGetRow()
	{
		$data = new DataTable();
		$data->addColumn(new ColumnDescription('first', ValueType::STRING));
		$row = new TableRow();
		$row->addCell(new TableCell(new TextValue('value1')));
		$data->addRow($row);

		$this->assertSame(1, $data->getNumberOfRows(), "row count must match");

		$row2 = $data->getRow(0);
		$this->assertEquals($row->getCell(0), $row2->getCell(0),
			"Value of retrieved cell must match original");
	}

	public function testGetRowOutOfBoundsException()
	{
		$data = new DataTable();

		$this->assertSame(0, $data->getNumberOfRows(), "row count must match");

		$this->setExpectedException('\\Vube\\GChart\\DataSource\\Exception\\IndexOutOfBoundsException');
		$row = $data->getRow(0);
	}

	public function testGetColumnCells()
	{
		$columnIndex = 1; // which column to get for our test
		$count = 10;
		$rows = array();
		for($i=0; $i<$count; $i++)
		{
			$row = new TableRow();
			$row->addCell(new TableCell(new TextValue('a'.$i)));
			$row->addCell(new TableCell(new TextValue('b'.$i)));
			$rows[] = $row;
		}

		$data = new DataTable();
		$data->addColumn(new ColumnDescription('c0', ValueType::STRING));
		$data->addColumn(new ColumnDescription('c1', ValueType::STRING));
		$cells = $data->getColumnCells(0);
		$this->assertSame(0, count($cells), "cells must be an empty array when no rows exist");

		$data->addRows($rows);
		$cells = $data->getColumnCells($columnIndex);
		$this->assertSame($count, count($cells), "cells must contain an entry for every row of data");

		for($i=0; $i<$count; $i++)
		{
			$inputCell = $rows[$i]->getCell($columnIndex);
			$this->assertEquals($inputCell, $cells[$i], "Cell[$i] must match input data");
		}
	}

	public function testGetColumnCellsOutOfBounds()
	{
		$data = new DataTable();

		$this->setExpectedException('\\Vube\\GChart\\DataSource\\Exception\\IndexOutOfBoundsException');
		$cells = $data->getColumnCells(0);
	}

	public function testGetColumnIndex()
	{
		$data = new DataTable();
		$data->addColumn(new ColumnDescription('c0', ValueType::STRING));
		$data->addColumn(new ColumnDescription('c1', ValueType::STRING));

		$this->assertSame(0, $data->getColumnIndex('c0'), "column id lookup must succeed");
		$this->assertSame(1, $data->getColumnIndex('c1'), "column id lookup must succeed");
	}

	public function testGetColumnIndexWithInvalidId()
	{
		$data = new DataTable();

		$this->setExpectedException('\\Vube\\GChart\\DataSource\\Exception\\NoSuchColumnIdException');
		$index = $data->getColumnIndex('no-such-column');
	}

	public function testDataTableCustomProperties()
	{
		$data = new DataTable('first', ValueType::STRING);
		$data->setCustomProperty('a', 'foo');
		$properties = $data->getCustomProperties();

		$this->assertTrue(is_array($properties), "properties must be an array");
		$this->assertArrayHasKey('a', $properties, "properties[a] must exist");
		$this->assertSame('foo', $data->getCustomProperty('a'), "getCustomProperty must return expected value");
	}

	public function testGetCustomNonexistentProperty()
	{
		$data = new DataTable('first', ValueType::STRING);
		$this->assertNull($data->getCustomProperty('no-such-property'), "Expect null return for no-such-property");
	}
}