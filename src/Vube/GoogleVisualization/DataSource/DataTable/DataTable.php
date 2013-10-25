<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\DataTable;

use Vube\GoogleVisualization\DataSource\Base\Warning;
use Vube\GoogleVisualization\DataSource\DataTable\Value\BooleanValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\DateTimeValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\DateValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\NumberValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\TimeOfDayValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\ValueFactory;
use Vube\GoogleVisualization\DataSource\DataTable\Value\ValueType;
use Vube\GoogleVisualization\DataSource\Exception;
use Vube\GoogleVisualization\DataSource\Exception\ColumnCountMismatchException;
use Vube\GoogleVisualization\DataSource\Exception\IndexOutOfBoundsException;
use Vube\GoogleVisualization\DataSource\Exception\NoSuchColumnIdException;
use Vube\GoogleVisualization\DataSource\Exception\NotImplementedException;
use Vube\GoogleVisualization\DataSource\Exception\ValueTypeMismatchException;


/**
 * DataTable class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class DataTable
{
	/**
	 * @var array
	 */
	private $columns = array();
	/**
	 * @var array
	 */
	private $rows = array();
	/**
	 * @var array
	 */
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
	 * @throws ColumnCountMismatchException
	 * @throws Exception
	 * @throws NotImplementedException
	 * @throws ValueTypeMismatchException
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
			$cellValue = $cells[$i]->getValue();
			$cellValueType = $cellValue->getType();

			if($columnDataType->getCode() != $cellValueType->getCode())
			{
				$castedCell = false;

				if($cellValue->isNull())
				{
					// Create a new null cell of the appropriate type
					$castedCell = clone $cells[$i];
					$castedCell->setValue(ValueFactory::constructNull($columnDataType));
				}
				else if($columnDataType->getCode() == ValueType::BOOLEAN)
				{
					switch($cellValueType->getCode())
					{
						case ValueType::STRING:
							$bool = $cellValue->getRawValue() === 'true';
							$castedCell = clone $cells[$i];
							$castedCell->setValue(new BooleanValue($bool));
							break;
						default:
							break;
					}
				}
				else if($columnDataType->getCode() == ValueType::NUMBER)
				{
					switch($cellValueType->getCode())
					{
						case ValueType::STRING:
							$castedCell = clone $cells[$i];
							$castedCell->setValue(new NumberValue($cellValue->getRawValue()));
							break;
						default:
							break;
					}
				}
				else if($columnDataType->isDateValue())
				{
					// This column is supposed to have a date, be pretty flexible
					// what kind of input data we have.
					//
					// Cast from whatever types we can to the date type.  This may be
					// casting from a string to a date, or casting from one type of
					// date value to another.

					$castedCell = clone $cells[$i];
					$rawValue = $cellValue->getRawValue();

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
							throw new NotImplementedException("Not Implemented: Some sort of new date type?");
							break;
					}
				}

				// If no new cell was created that has been casted to the appropriate
				// type, there is a ValueType mismatch exception
				if(! $castedCell)
					throw new ValueTypeMismatchException($columnDataType, $i);

				// Assign the casted cell to the row
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