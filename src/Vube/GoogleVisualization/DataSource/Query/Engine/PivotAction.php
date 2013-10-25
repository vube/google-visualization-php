<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\Engine;

use Vube\GoogleVisualization\DataSource\DataTable\ColumnDescription;
use Vube\GoogleVisualization\DataSource\DataTable\DataTable;
use Vube\GoogleVisualization\DataSource\DataTable\TableRow;
use Vube\GoogleVisualization\DataSource\DataTable\Value\ValueFactory;
use Vube\GoogleVisualization\DataSource\Query\AggregationType;
use Vube\GoogleVisualization\DataSource\Query\PivotDescription;
use Vube\GoogleVisualization\DataSource\Query\ScalarFunction\AverageDataContainer;
use Vube\GoogleVisualization\DataSource\Query\ScalarFunction\CountDataContainer;
use Vube\GoogleVisualization\DataSource\Query\ScalarFunction\MaxDataContainer;
use Vube\GoogleVisualization\DataSource\Query\ScalarFunction\MinDataContainer;
use Vube\GoogleVisualization\DataSource\Query\ScalarFunction\SumDataContainer;
use Vube\GoogleVisualization\DataSource\Query\Exception\NotImplementedException;


/**
 * PivotAction class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class PivotAction {

	/**
	 * @var PivotDescription
	 */
	private $pivotDescription;
	/**
	 * @var PivotKeyIndex
	 */
	private $rowKeyIndex;
	/**
	 * @var PivotKeyIndex
	 */
	private $columnKeyIndex;
	/**
	 * 3-dimensional array of data containers
	 *
	 * <code>
	 * (PivotKey) Row Key
	 *   -> (PivotKey) Column Key
	 *     -> (int) Data Column Index
	 *       -> (iDataContainer) Data Container
	 * </code>
	 * @var array
	 */
	private $table;

	/**
	 * @param PivotDescription $pivotDescription
	 */
	public function __construct(PivotDescription $pivotDescription)
	{
		$this->pivotDescription = $pivotDescription;
		$this->table = array();
	}

	public function & execute()
	{
		$this->generateTable();

		$data =& $this->initPivotTable();
		$this->addOutputRows($data);

		// TODO: Remove all the custom properties we set on the columns

		return $data;
	}

	public function & initPivotTable()
	{
		$dataOut = new DataTable();

		$dataIn =& $this->pivotDescription->getDataTable();

		// First add all the row fields.
		// Just copy these as-is from the input DataTable

		$rowFieldIndexes = $this->getRowFieldIndexes();
		foreach($rowFieldIndexes as $columnIndex)
		{
			$columnDescription = $dataIn->getColumnDescription($columnIndex);
			$columnDescription->setCustomProperty('rowFieldIndex', $columnIndex);
			$dataOut->addColumn($columnDescription);
		}

		// Next add a column for each unique column in
		// the column key index

		$columnDescriptions = array();
		$columnFieldIndexes = $this->getColumnFieldIndexes();
		$numColumnFields = count($columnFieldIndexes);
		foreach($columnFieldIndexes as $columnIndex)
		{
			$columnDescription = $dataIn->getColumnDescription($columnIndex);
			$columnDescriptions[] = $columnDescription;
		}

		$dataColumnDescriptions = array();
		$dataFieldIndexes = $this->getDataFieldIndexes();
		$numDataFields = count($dataFieldIndexes);
		foreach($dataFieldIndexes as $columnIndex)
		{
			$columnDescription = $dataIn->getColumnDescription($columnIndex);
			$dataColumnDescriptions[] = $columnDescription;
		}

		$uniqueColumnKeys = $this->columnKeyIndex->getKeys();
		foreach($uniqueColumnKeys as $hash => $key)
		{
			$idParts = array();
			$labelParts = array();

			$columnValues = $key->getColumnValues();

			for($i=0; $i<$numColumnFields; $i++)
			{
				$columnDescription = $columnDescriptions[$i];
				$columnValue = $columnValues[$i];

				$idParts[] = $columnDescription->getId() .'='. $columnValue;
				$labelParts[] = $columnValue;
			}

			for($di=0; $di<$numDataFields; $di++)
			{
				$dataFieldIndex = $dataFieldIndexes[$di];
				$dataColumnDescription = $dataColumnDescriptions[$di];

				$fieldId = $dataColumnDescription->getId();
				$fieldLabel = $dataColumnDescription->getLabel();

				$aggregationType = $this->pivotDescription->getAggregationType($fieldId);

				$id = implode("__", $idParts) . "__" . $fieldId;
				$label = ($aggregationType ? $aggregationType->getCode().' ' : '') .
					implode(" ", $labelParts) . " " . $fieldLabel;

				// For now copy the same type as the input column.
				// In the future we may need to set this explicitly based on the calculation.
				$type = $dataColumnDescription->getType();

				$columnDescription = new ColumnDescription($id, $type, $label);
				$columnDescription->setCustomProperty('columnKeyId', $key->__toString());
				$columnDescription->setCustomProperty('dataFieldIndex', $dataFieldIndex);
				$dataOut->addColumn($columnDescription);
			}
		}

		return $dataOut;
	}

	public function addOutputRows(DataTable &$dataOut)
	{
		$dataIn =& $this->pivotDescription->getDataTable();
		$columns = $dataOut->getColumnDescriptions();

		foreach($this->table as $rk => $rowTable)
		{
			$row = new TableRow();

			foreach($columns as $column)
			{
				$ck = $column->getCustomProperty('columnKeyId');
				if($ck === null)
				{
					$rowKey = $this->rowKeyIndex->getKeyRefByHash($rk);
					$firstRowIndex = $rowKey->getFirstRowIndex();
					$rowFieldIndex = $column->getCustomProperty('rowFieldIndex');
					$cell = $dataIn->getRow($firstRowIndex)->getCell($rowFieldIndex);
					$row->addCell($cell);
				}
				else
				{
					// This is a computed data field
					$di = $column->getCustomProperty('dataFieldIndex');
					if(isset($this->table[$rk][$ck][$di]))
					{
						$dataContainer = $this->table[$rk][$ck][$di];
						$value = $dataContainer->getComputedValue();
					}
					else
					{
						$value = ValueFactory::constructNull($column->getType());
					}
					$row->addCell($value);
				}
			}

			$dataOut->addRow($row);
		}
	}

	public function & generateTable()
	{
		$data =& $this->pivotDescription->getDataTable();

		$rowFieldIndexes = $this->getRowFieldIndexes();
		$columnFieldIndexes = $this->getColumnFieldIndexes();
		$dataFieldIndexes = $this->getDataFieldIndexes();

		$this->table = array();
		$this->rowKeyIndex = new PivotKeyIndex($data, $rowFieldIndexes);
		$this->columnKeyIndex = new PivotKeyIndex($data, $columnFieldIndexes);

		$numRows = $data->getNumberOfRows();
		for($ri=0; $ri<$numRows; $ri++)
		{
			$row = $data->getRow($ri);

			$rowKey =& $this->rowKeyIndex->getKeyRefByRowIndex($ri);
			$rowKey->addRowIndex($ri);

			$columnKey =& $this->columnKeyIndex->getKeyRefByRowIndex($ri);
			$columnKey->addRowIndex($ri);

			foreach($dataFieldIndexes as $columnIndex)
				$this->accumulateData($row, $rowKey, $columnKey, $columnIndex);
		}

		return $this->table;
	}

	public function accumulateData(&$row, $rowKey, $columnKey, $dataColumnIndex)
	{
		$cell = $row->getCell($dataColumnIndex);
		$container =& $this->getDataContainerRef($rowKey, $columnKey, $dataColumnIndex);
		$container->addCell($cell);
	}

	public function & getDataContainerRef($rowKey, $columnKey, $dataColumnIndex)
	{
		$rk = $rowKey->__toString();
		$ck = $columnKey->__toString();

		if(! isset($this->table[$rk]))
			$this->table[$rk] = array();

		if(! isset($this->table[$rk][$ck]))
			$this->table[$rk][$ck] = array();

		if(! isset($this->table[$rk][$ck][$dataColumnIndex]))
			$this->table[$rk][$ck][$dataColumnIndex] = $this->constructDataContainer($dataColumnIndex);

		$container =& $this->table[$rk][$ck][$dataColumnIndex];
		return $container;
	}

	/**
	 * @param int $dataColumnIndex Index of the data column whose container to construct
	 * @return iDataContainer
	 * @throws NotImplementedException for aggregation types not yet implemented
	 */
	public function constructDataContainer($dataColumnIndex)
	{
		$data =& $this->pivotDescription->getDataTable();
		$column = $data->getColumnDescription($dataColumnIndex);
		$dataFieldId = $column->getId();

		$aggregationType = $this->pivotDescription->getAggregationType($dataFieldId);
		$aggregationTypeCode = $aggregationType ? $aggregationType->getCode() : null;
		switch($aggregationTypeCode)
		{
			case AggregationType::AVG:
				return new AverageDataContainer();

			case AggregationType::MIN:
				return new MinDataContainer();

			case AggregationType::MAX:
				return new MaxDataContainer();

			case AggregationType::COUNT:
				return new CountDataContainer();

			case AggregationType::SUM:
			default: // e.g. null (no specified aggregation type)
				return new SumDataContainer();
		}
	}

	/**
	 * @return array
	 */
	public function getRowFieldIndexes()
	{
		$indexes = $this->getColumnIndexesById($this->pivotDescription->getRowFields());
		return $indexes;
	}

	/**
	 * @return array
	 */
	public function getColumnFieldIndexes()
	{
		$indexes = $this->getColumnIndexesById($this->pivotDescription->getColumnFields());
		return $indexes;
	}

	/**
	 * @return array
	 */
	public function getDataFieldIndexes()
	{
		$indexes = $this->getColumnIndexesById($this->pivotDescription->getDataFields());
		return $indexes;
	}

	/**
	 * @param array $fieldIds List of field ids whose column indexes to return
	 * @return array
	 */
	public function getColumnIndexesById($fieldIds)
	{
		$data =& $this->pivotDescription->getDataTable();

		$fieldIndexes = array();
		foreach($fieldIds as $fieldId)
		{
			$ci = $data->getColumnIndex($fieldId);
			$fieldIndexes[] = $ci;
		}
		return $fieldIndexes;
	}
}