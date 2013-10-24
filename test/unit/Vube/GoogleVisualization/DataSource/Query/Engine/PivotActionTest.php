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
use Vube\GoogleVisualization\DataSource\Query\Engine\PivotAction;
use Vube\GoogleVisualization\DataSource\Query\PivotDescription;


/**
 * PivotActionTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class PivotActionTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var DataTable
	 */
	private $dataTable;
	/**
	 * @var PivotDescription
	 */
	private $pivotDescription;

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
		$rawData = array(
			array(new Date('2013-10-01'), 'US', 'TX', 1000, 900),
			array(new Date('2013-10-01'), 'US', 'CA', 1000, 900),
			array(new Date('2013-10-01'), 'US', 'WA', 1000, 900),
			array(new Date('2013-10-02'), 'US', 'TX', 1000, 900),
			array(new Date('2013-10-02'), 'US', 'WA', 1000, 900),
			array(new Date('2013-10-03'), 'US', 'TX', 1000, 900),
			array(new Date('2013-10-03'), 'US', 'WA', 1000, 900),
		);

		foreach($rawData as $row)
			$data->addRow(new TableRow($row));

		$pivotDescription = new PivotDescription($data,
			array('country', 'region'));

		$this->dataTable = $data;
		$this->pivotDescription = $pivotDescription;
	}

	public function testGetRowFieldIndexes()
	{
		$expected = array(0);
		$pivotAction = new PivotAction($this->pivotDescription);
		$actual = $pivotAction->getRowFieldIndexes();
		$this->assertEquals($expected, $actual, "Failed to extract row field indexes");
	}

	public function testGetColumnFieldIndexes()
	{
		$expected = array(1,2);
		$pivotAction = new PivotAction($this->pivotDescription);
		$actual = $pivotAction->getColumnFieldIndexes();
		$this->assertEquals($expected, $actual, "Failed to extract column field indexes");
	}

	public function testGetDataFieldIndexes()
	{
		$expected = array(3,4);
		$pivotAction = new PivotAction($this->pivotDescription);
		$actual = $pivotAction->getDataFieldIndexes();
		$this->assertEquals($expected, $actual, "Failed to extract data field indexes");
	}

	public function testRowKeyGeneration()
	{
		$pivotAction = new PivotAction($this->pivotDescription);
		$table =& $pivotAction->generateTable();

		$expectedRowKeys = array(
			'["2013-10-01T00:00:00+0000"]',
			'["2013-10-02T00:00:00+0000"]',
			'["2013-10-03T00:00:00+0000"]',
		);
		$rowKeys = array_keys($table);
		$this->assertSame($expectedRowKeys, $rowKeys,
			"Row keys should match");
	}

	public function testColumnKeyGeneration()
	{
		$pivotAction = new PivotAction($this->pivotDescription);
		$table =& $pivotAction->generateTable();

		$expectedColumnKeys = array(
			'["2013-10-01T00:00:00+0000"]' => array(
				'["US","TX"]',
				'["US","CA"]',
				'["US","WA"]',
			),
			'["2013-10-02T00:00:00+0000"]' => array(
				'["US","TX"]',
				'["US","WA"]',
			),
			'["2013-10-03T00:00:00+0000"]' => array(
				'["US","TX"]',
				'["US","WA"]',
			),
		);

		$rowKeys = array_keys($table);
		$columnKeys = array();
		foreach($rowKeys as $rk)
		{
			$ck = array_keys($table[$rk]);
			$columnKeys[$rk] = $ck;
		}

		$this->assertSame($expectedColumnKeys, $columnKeys,
			"Column keys should match");
	}

	public function testExecute()
	{
		$pivotAction = new PivotAction($this->pivotDescription);
		$data =& $pivotAction->execute();

		$expected = 7;
		$actual = $data->getNumberOfColumns();

		$this->assertSame($expected, $actual,
			"Number of output columns must match expected");

		$expected = 3;
		$actual = $data->getNumberOfRows();

		$this->assertSame($expected, $actual,
			"Number of output rows must match expected");

//		var_export($data);
	}
}