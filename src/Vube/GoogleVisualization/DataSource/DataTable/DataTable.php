<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\DataTable;

use Vube\GoogleVisualization\DataSource\Base\Warning;
use Vube\GoogleVisualization\DataSource\DataTable\Value\DateTimeValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\DateValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\TimeOfDayValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\ValueType;
use Vube\GoogleVisualization\DataSource\Exception;
use Vube\GoogleVisualization\DataSource\Exception\ColumnCountMismatchException;
use Vube\GoogleVisualization\DataSource\Exception\IndexOutOfBoundsException;
use Vube\GoogleVisualization\DataSource\Exception\NoSuchColumnIdException;
use Vube\GoogleVisualization\DataSource\Exception\ValueTypeMismatchException;


/**
 * DataTable class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class DataTable
{
	private $columns = array();
	private $rows = array();
	private $warnings = array();
	/**
	 * @var array
	 */
	private $customProperties = array();

	private $columnCount = 0;
	private $rowCount = 0;

	public function __construct() {}

	/**
	 * @return array Array of Warning objects
	 */
	public function getWarnings()
	{
		return $this->warnings;
	}

	/**
	 * @return int
	 */
	public function getNumberOfWarnings()
	{
		return count($this->warnings);
	}

	/**
	 * @param Warning $warning
	 */
	public function addWarning(Warning $warning)
	{
		$this->warnings[] = $warning;
	}

	/**
	 * @param ColumnDescription $column
	 */
	public function addColumn(ColumnDescription $column)
	{
		$this->columns[] = $column;
		$this->columnCount++;
	}

	/**
	 * @param array $columns Array of ColumnDescription objects
	 */
	public function addColumns(array $columns)
	{
		foreach($columns as $column)
			$this->addColumn($column);
	}

	/**
	 * @param TableRow $row
	 */
	public function addRow(TableRow $row)
	{
		if($this->columnCount == 0)
			throw new Exception("You must add columns to a DataTable before you add rows");

		// Check to make sure the number of columns in the row
		// matches the number of columns in our table

		$rowCellCount = $row->getNumberOfCells();
		if($rowCellCount != $this->columnCount)
			throw new ColumnCountMismatchException($this->columnCount, $rowCellCount);

		// Check the data type of each cell to ensure it matches
		// the data type we expect for that column

		$cells = $row->getCells();
		for($i=0; $i<$this->columnCount; $i++)
		{
			$columnDataType = $this->columns[$i]->getType();
			$cellValueType = $cells[$i]->getValue()->getType();

			if($columnDataType->getCode() != $cellValueType->getCode())
			{
				// If this is a DateValue, DateTimeValue or TimeOfDayValue,
				// we can just cast it, the underlying data is the same

				$castedCell = false;

				$cellIsDatelike = in_array($cellValueType->getCode(), array(ValueType::DATE, ValueType::DATETIME, ValueType::TIMEOFDAY));
				if($cellIsDatelike)
				{
					$castedCell = $cells[$i];
					$rawValue = $castedCell->getValue()->getValue();

					switch($columnDataType->getCode())
					{
						case ValueType::DATE:
							$castedCell->setValue(new DateValue($rawValue));
							break;
						case ValueType::DATETIME:
							$castedCell->setValue(new DateTimeValue($rawValue));
							break;
						case ValueType::TIMEOFDAY:
							$castedCell->setValue(new TimeOfDayValue($rawValue));
							break;
						default:
							break;
					}
				}
				if(! $castedCell)
					throw new ValueTypeMismatchException($columnDataType, $i);
				$row->setCell($i, $castedCell);
			}
		}

		// Add the row to the table

		$this->rows[] = $row;
		$this->rowCount++;
	}

	/**
	 * @param array $rows Array of TableRow objects
	 */
	public function addRows(array $rows)
	{
		foreach($rows as $row)
			$this->addRow($row);
	}

	/**
	 * @param array $rows Array of TableRow objects
	 */
	public function setRows(array $rows)
	{
		$this->rows = array();
		$this->rowCount = 0;

		$this->addRows($rows);
	}

	/**
	 * @param int $index
	 * @return TableRow
	 * @throws \Vube\GoogleVisualization\DataSource\Exception\IndexOutOfBoundsException
	 */
	public function getRow($index)
	{
		if($index < 0 || $index >= $this->rowCount)
			throw new IndexOutOfBoundsException($index);

		$row = $this->rows[$index];
		return $row;
	}

	/**
	 * @return int
	 */
	public function getNumberOfRows()
	{
		return $this->rowCount;
	}

	/**
	 * @return int
	 */
	public function getNumberOfColumns()
	{
		return $this->columnCount;
	}

	/**
	 * @return array
	 */
	public function getColumnDescriptions()
	{
		return $this->columns;
	}

	/**
	 * @param int $index
	 * @return ColumnDescription
	 * @throws \Vube\GoogleVisualization\DataSource\Exception\IndexOutOfBoundsException
	 */
	public function getColumnDescription($index)
	{
		if($index < 0 || $index >= $this->columnCount)
			throw new IndexOutOfBoundsException($index);

		$column = $this->columns[$index];
		return $column;
	}

	/**
	 * @param string $columnId
	 * @return ColumnDescription
	 */
	public function getColumnDescriptionById($columnId)
	{
		$index = $this->getColumnIndex($columnId);
		$column = $this->getColumnDescription($index);
		return $column;
	}

	/**
	 * @param int $index
	 * @return array Array of TableCell objects
	 * @throws \Vube\GoogleVisualization\DataSource\Exception\IndexOutOfBoundsException
	 */
	public function getColumnCells($index)
	{
		if($index < 0 || $index >= $this->columnCount)
			throw new IndexOutOfBoundsException($index);

		$cells = array();
		foreach($this->rows as $row)
		{
			$cell = $row->getCell($index);
			$cells[] = $cell;
		}

		return $cells;
	}

	/**
	 * @param string $columnId
	 * @return int
	 * @throws \Vube\GoogleVisualization\DataSource\Exception\NoSuchColumnIdException
	 */
	public function getColumnIndex($columnId)
	{
		for($i=0; $i<$this->columnCount; $i++)
		{
			if($this->columns[$i]->getId() === $columnId)
			{
				return $i;
			}
		}

		// No matching column was found
		throw new NoSuchColumnIdException($columnId);
	}

	/**
	 * @return array
	 */
	public function getCustomProperties()
	{
		return $this->customProperties;
	}

	/**
	 * @param string $name Name of the custom property to retrieve.
	 * @return null|string
	 */
	public function getCustomProperty($name)
	{
		if(! isset($this->customProperties[$name]))
			return null;

		$property = $this->customProperties[$name];
		return $property;
	}

	/**
	 * @param string $name Name of the custom property to set.
	 * @param string $value Value of the custom property to set.
	 */
	public function setCustomProperty($name, $value)
	{
		$this->customProperties[$name] = $value;
	}
}