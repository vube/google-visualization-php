<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\test;

use Vube\GoogleVisualization\DataSource\DataTable\ColumnDescription;
use Vube\GoogleVisualization\DataSource\DataTable\DataTable;
use Vube\GoogleVisualization\DataSource\DataTable\Value\ValueType;
use Vube\GoogleVisualization\DataSource\Query\AggregationType;
use Vube\GoogleVisualization\DataSource\Query\PivotDescription;


/**
 * PivotDescriptionTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class PivotDescriptionTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var DataTable
	 */
	private $dataTable;

	public function setUp()
	{
		$data = new DataTable();
		$data->addColumn(new ColumnDescription('day', ValueType::DATE));
		$data->addColumn(new ColumnDescription('country', ValueType::STRING));
		$data->addColumn(new ColumnDescription('region', ValueType::STRING));
		$data->addColumn(new ColumnDescription('income', ValueType::STRING));
		$data->addColumn(new ColumnDescription('expense', ValueType::STRING));

		$this->dataTable = $data;
	}

	public function testGetAllColumnIds()
	{
		$expected = array('day', 'country', 'region', 'income', 'expense');
		$pivot = new PivotDescription($this->dataTable);
		$actual = $pivot->getAllColumnIds($this->dataTable->getColumnDescriptions());
		$this->assertEquals($expected, $actual, "Expect to have all columns returned");
	}

	public function testConstructorThrowsIfNoColumns()
	{
		$emptyDataTable = new DataTable();
		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Exception');
		$pivot = new PivotDescription($emptyDataTable);
	}

	public function testConstructorDoesNotThrowIfColumns()
	{
		$pivot = new PivotDescription($this->dataTable);
	}

	public function testGetColumnFieldsReturnsConstructorValue()
	{
		$expected = array('day');
		$pivot = new PivotDescription($this->dataTable, $expected);
		$actual = $pivot->getColumnFields();
		$this->assertEquals($expected, $actual, "Expect to get my value back");
	}

	public function testNoFalsePositiveDuplicateFields()
	{
		$fieldsA = array('a', 'b', 'c');
		$fieldsB = array('d', 'e');

		$pivot = new PivotDescription($this->dataTable);
		$pivot->verifyNoDuplicateFields($fieldsA, $fieldsB, 'A', 'B');
	}

	public function testOneDuplicateFieldDetected()
	{
		$fieldsA = array('dup', 'b', 'c');
		$fieldsB = array('dup', 'e');

		$pivot = new PivotDescription($this->dataTable);
		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Query\\Exception\\DuplicateFieldException',
			'Duplicate fields specified as both A and B: dup, dup');
		$pivot->verifyNoDuplicateFields($fieldsA, $fieldsB, 'A', 'B');
	}

	public function testDefaultRowFieldsCalculation()
	{
		$pivot = new PivotDescription($this->dataTable);
		$pivot->setColumnFields(array('country', 'region'));
		$rowFields = $pivot->getRowFields();

		$this->assertSame(1, count($rowFields), "rowFields must contain 1 element");
		$this->assertSame("day", $rowFields[0], "rowFields[0] must be the name of the first column");
	}

	public function testDefaultDataFieldsCalculation()
	{
		$pivot = new PivotDescription($this->dataTable);
		$pivot->setColumnFields(array('country', 'region'));
		$dataFields = $pivot->getDataFields();

		$this->assertSame(2, count($dataFields), "dataFields must contain 1 element");
		$this->assertSame("income", $dataFields[0], "dataFields[0] must be the name of the first field that's not a row and not a column");
		$this->assertSame("expense", $dataFields[1], "dataFields[1] must be the name of the second field that's not a row and not a column");
	}

	public function testNoDataFieldsException()
	{
		$pivot = new PivotDescription($this->dataTable);
		// define _all_ the non-row fields as column fields, so there
		// are no fields left to be data fields
		$pivot->setColumnFields(array('country', 'region', 'income', 'expense'));
		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Query\\Exception\\NoDataFieldsException');
		$dataFields = $pivot->getDataFields();
	}

	public function testVerifyFieldNamesThrowsOnInvalidName()
	{
		$pivot = new PivotDescription($this->dataTable);
		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Query\\Exception\\InvalidFieldNameException',
			"Invalid field name 'not-a-field' specified in test config");
		$pivot->verifyTheseFieldsExist(array('country', 'not-a-field'), 'test');
	}

	public function testVerifyFieldNamesDoesNotThrowForValidNames()
	{
		$pivot = new PivotDescription($this->dataTable);
		$allColumnIds = $pivot->getAllColumnIds($this->dataTable->getColumnDescriptions());
		$pivot->verifyTheseFieldsExist($allColumnIds, 'test');
	}

	public function testInvalidColumnFieldThrowsException()
	{
		$pivot = new PivotDescription($this->dataTable);
		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Query\\Exception\\InvalidFieldNameException');
		$pivot->setColumnFields(array('no-such-field'));
	}

	public function testInvalidRowFieldThrowsException()
	{
		$pivot = new PivotDescription($this->dataTable);
		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Query\\Exception\\InvalidFieldNameException');
		$pivot->setRowFields(array('no-such-field'));
	}

	public function testInvalidDataFieldThrowsException()
	{
		$pivot = new PivotDescription($this->dataTable);
		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Query\\Exception\\InvalidFieldNameException');
		$pivot->setDataFields(array('no-such-field'));
	}

	public function testSetAggregationTypeThrowsExceptionForUnknownFieldId()
	{
		$pivot = new PivotDescription($this->dataTable);
		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Query\\Exception\\InvalidFieldIdException');
		$pivot->setAggregationType('no-such-field', AggregationType::SUM);
	}

	public function testSetAggregationTypeThrowsExceptionForNonDataFieldId()
	{
		$pivot = new PivotDescription($this->dataTable);
		$pivot->setDataFields(array('income', 'expense'));
		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Query\\Exception\\InvalidFieldIdException');
		// try to set aggregation type for a column field
		$pivot->setAggregationType('country', AggregationType::SUM);
	}

	public function testSetAggregationTypeWithValidFieldId()
	{
		$pivot = new PivotDescription($this->dataTable);
		$pivot->setDataFields(array('income', 'expense'));
		$pivot->setAggregationType('income', AggregationType::SUM);
		$expected = AggregationType::SUM;
		$actual = $pivot->getAggregationType('income');
		$this->assertSame($expected, $actual->getCode(), "Expect SUM type is set");
	}

	public function testSetAggregationTypeObjectWithValidFieldId()
	{
		$pivot = new PivotDescription($this->dataTable);
		$pivot->setDataFields(array('income', 'expense'));
		$type = new AggregationType(AggregationType::AVG);
		$pivot->setAggregationType('income', $type);
		$actual = $pivot->getAggregationType('income');
		$this->assertEquals($type, $actual, "Expect AVG type is set");
	}

	public function testGetAggregationTypeReturnsNullForUnknownFieldId()
	{
		$pivot = new PivotDescription($this->dataTable);
		$pivot->setColumnFields(array('country', 'region'));

		$expected = null;
		$actual = $pivot->getAggregationType('not-a-data-field');
		$this->assertSame($expected, $actual, "Expect null for non-data fields");
	}

	public function testAddDataFieldWithAggregationType()
	{
		$type = AggregationType::MAX;

		$pivot = new PivotDescription($this->dataTable);
		$pivot->addDataField('expense', $type);

		$expected = array('expense');
		$actual = $pivot->getDataFields();
		$this->assertEquals($expected, $actual);

		$expected = $type;
		$actual = $pivot->getAggregationType('expense');
		$this->assertNotNull($actual);
		$this->assertEquals($expected, $actual->getCode());
	}
}