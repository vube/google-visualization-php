<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\test;

use Vube\GoogleVisualization\DataSource\DataTable\ColumnDescription;
use Vube\GoogleVisualization\DataSource\DataTable\DataTable;
use Vube\GoogleVisualization\DataSource\DataTable\TableRow;
use Vube\GoogleVisualization\DataSource\DataTable\Value\ValueType;
use Vube\GoogleVisualization\DataSource\Date;
use Vube\GoogleVisualization\DataSource\Query\Engine\PivotKeyIndex;


/**
 * PivotKeyIndexTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class PivotKeyIndexTest extends \PHPUnit_Framework_TestCase {

	private $rawData;
	private $dataTable;

	public function setUp()
	{
		$data = new DataTable();

		// DO NOT change the order of this stuff it will break the tests
		$data->addColumn(new ColumnDescription('day', ValueType::DATE, 'Date'));
		$data->addColumn(new ColumnDescription('country', ValueType::STRING, 'Country'));
		$data->addColumn(new ColumnDescription('region', ValueType::STRING, 'Region'));
		$data->addColumn(new ColumnDescription('income', ValueType::NUMBER, 'Income'));
		$data->addColumn(new ColumnDescription('expense', ValueType::NUMBER, 'Expense'));

		// DO NOT change the order of this stuff it will break the tests
		$this->rawData = array(
			array(new Date('2013-10-01'), 'US', 'TX', 1000, 900),
			array(new Date('2013-10-01'), 'US', 'CA', 1000, 900),
			array(new Date('2013-10-01'), 'US', 'WA', 1000, 900),
			array(new Date('2013-10-02'), 'US', 'TX', 1000, 900),
			array(new Date('2013-10-02'), 'US', 'WA', 1000, 900),
			array(new Date('2013-10-03'), 'US', 'TX', 1000, 900),
			array(new Date('2013-10-03'), 'US', 'WA', 1000, 900),
		);

		foreach($this->rawData as $row)
			$data->addRow(new TableRow($row));

		$this->dataTable = $data;
	}

	public function testGetKeyRefByRowIndex()
	{
		$columnIndexes = array(1, 2);
		$keyIndex = new PivotKeyIndex($this->dataTable, $columnIndexes);
		$key =& $keyIndex->getKeyRefByRowIndex(0);

		$expected = array('US', 'TX');
		$actual = $key->getColumnValues();

		$this->assertEquals($expected, $actual, "Key values should match input data");
	}

	public function testGetKeyRefByRowIndexReturnsReference()
	{
		$columnIndexes = array(1, 2);
		$keyIndex = new PivotKeyIndex($this->dataTable, $columnIndexes);
		$key1 =& $keyIndex->getKeyRefByRowIndex(0); // US,TX
		$key2 =& $keyIndex->getKeyRefByRowIndex(3); // US,TX

		$this->assertSame($key1, $key2, "Expect the exact same referenced object");
	}

}