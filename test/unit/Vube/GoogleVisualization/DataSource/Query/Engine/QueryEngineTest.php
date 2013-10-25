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
use Vube\GoogleVisualization\DataSource\Query\Engine\QueryEngine;
use Vube\GoogleVisualization\DataSource\Query\Query;


class QueryEngineTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var DataTable
	 */
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

		$this->dataTable = $data;
	}

	public function testDataReductionNoSelect()
	{
		$query = new Query();
		$result =& QueryEngine::reduceData($query, $this->dataTable);

		$this->assertSame($result, $this->dataTable,
			"Expect reference to this->dataTable returned when query is empty");
	}

	public function testDataReductionSingleFieldSelected()
	{
		$text = 'select day';
		$query = Query::constructFromString($text);
		$result =& QueryEngine::reduceData($query, $this->dataTable);

		$this->assertNotSame($result, $this->dataTable,
			"Expect new DataTable when selection is narrowed");

		$this->assertSame(1, $result->getNumberOfColumns(),
			"Expect there is only 1 column in the result");
		$this->assertSame($this->dataTable->getNumberOfRows(), $result->getNumberOfRows(),
			"Expect the number of rows stays the same");

		$column = $result->getColumnDescription(0);
		$this->assertSame('day', $column->getId(), "Expect column name is preserved");
	}

	public function testDataReductionWithPivot()
	{
		$text = 'select sum(income) pivot country';
		$query = Query::constructFromString($text);
		$result =& QueryEngine::reduceData($query, $this->dataTable);

		$this->assertNotSame($result, $this->dataTable,
			"Expect new DataTable when selection is narrowed");

		$this->assertSame(2, $result->getNumberOfColumns(),
			"Expect there are only 2 columns in the result");
		$this->assertSame($this->dataTable->getNumberOfRows(), $result->getNumberOfRows(),
			"Expect the number of rows stays the same");

		// Make sure the income column exists (exception is thrown otherwise)
		$result->getColumnDescriptionById('income');
		// Make sure the country column exists (exception is thrown otherwise)
		$result->getColumnDescriptionById('country');
	}

	public function testExecuteWithPivot()
	{
		$text = 'select day, sum(income) pivot country';
		$query = Query::constructFromString($text);
		$result =& QueryEngine::execute($query, $this->dataTable);

		$expected = 2;
		$actual = $result->getNumberOfColumns();
		$this->assertSame($expected, $actual, "Expect day and 'US Income' columns");

		$expected = 3;
		$actual = $result->getNumberOfRows();
		$this->assertSame($expected, $actual, "Expect 3 rows (1 for each day)");
	}

	public function testExecuteWithOnlyPivot()
	{
		$text = 'pivot country, region';
		$query = Query::constructFromString($text);
		$result =& QueryEngine::execute($query, $this->dataTable);

		$expected = 7;
		$actual = $result->getNumberOfColumns();
		$this->assertSame($expected, $actual, "Expect day column plus 1 for each country+region pair == 7");

		$expected = 3;
		$actual = $result->getNumberOfRows();
		$this->assertSame($expected, $actual, "Expect 3 rows (1 for each day)");
	}
}