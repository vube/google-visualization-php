<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query;

use Vube\GoogleVisualization\DataSource\DataTable\DataTable;
use Vube\GoogleVisualization\DataSource\Exception;
use Vube\GoogleVisualization\DataSource\Exception\NoSuchColumnIdException;
use Vube\GoogleVisualization\DataSource\Query\Exception\DuplicateFieldException;
use Vube\GoogleVisualization\DataSource\Query\Exception\EmptyDataTableException;
use Vube\GoogleVisualization\DataSource\Query\Exception\InvalidFieldIdException;
use Vube\GoogleVisualization\DataSource\Query\Exception\InvalidFieldNameException;
use Vube\GoogleVisualization\DataSource\Query\Exception\NoDataFieldsException;


/**
 * PivotDescription class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class PivotDescription {

	/**
	 * @var DataTable
	 */
	private $dataTable;
	/**
	 * Group-by fields
	 * @var array|null
	 */
	private $columnFields = null;
	/**
	 * Axis fields
	 * @var array|null
	 */
	private $rowFields = null;
	/**
	 * Data fields
	 * @var array|null
	 */
	private $dataFields = null;
	/**
	 * Has this object been fixed up?
	 * @var bool
	 */
	private $isFixed = false;

	/**
	 * @var array
	 */
	private $fieldAggregationTypes = array();

	/**
	 * Constructor
	 * @param DataTable &$dataTable [IN]
	 * @param array $columnFields
	 * @throws EmptyDataTableException
	 */
	public function __construct(DataTable &$dataTable, $columnFields = null)
	{
		if($dataTable->getNumberOfColumns() < 1)
			throw new EmptyDataTableException();

		$this->dataTable =& $dataTable;

		if($columnFields !== null)
			$this->setColumnFields($columnFields);
	}

	/**
	 * @return DataTable reference
	 */
	public function & getDataTable()
	{
		return $this->dataTable;
	}

	/**
	 * @return array|null
	 */
	public function getColumnFields()
	{
		// No need to fixup fields before returning column fields, these
		// must be configured by the app, there are no default settings.
		return $this->columnFields;
	}

	/**
	 * @return array|null
	 * @throws NoDataFieldsException If there are no row fields
	 * @throws Exception if the DataTable cannot answer this pivot request
	 */
	public function getRowFields()
	{
		// If we need to fixup the fields before returning, do so
		if(! $this->isFixed) $this->fixupFields();
		return $this->rowFields;
	}

	/**
	 * @return array|null
	 * @throws NoDataFieldsException If there are no row fields
	 * @throws Exception if the DataTable cannot answer this pivot request
	 */
	public function getDataFields()
	{
		// If we need to fixup the fields before returning, do so
		if(! $this->isFixed) $this->fixupFields();
		return $this->dataFields;
	}

	/**
	 * @param string $fieldId
	 * @return AggregationType|null
	 */
	public function getAggregationType($fieldId)
	{
		// If we need to fixup the fields before processing, do so
		if(! $this->isFixed) $this->fixupFields();

		// If this field is not a data field, there is no aggregation type
		if(! in_array($fieldId, $this->dataFields))
			return null;

		if(! isset($this->fieldAggregationTypes[$fieldId]))
			return null;

		return $this->fieldAggregationTypes[$fieldId];
	}

	/**
	 * @param string $fieldId
	 * @param AggregationType|string $type
	 * @throws InvalidFieldIdException
	 */
	public function setAggregationType($fieldId, $type)
	{
		if(! is_array($this->dataFields) ||
			! in_array($fieldId, $this->dataFields))
			throw new InvalidFieldIdException($fieldId, "not a data field");

		$this->fieldAggregationTypes[$fieldId] = new AggregationType($type);
	}

	/**
	 * @param array $columnFields List of strings of column field ids
	 * @throws InvalidFieldNameException if one of the fields does not exist
	 */
	public function setColumnFields($columnFields)
	{
		$this->columnFields = array();
		$this->isFixed = false;

		foreach($columnFields as $fieldId)
			$this->addColumnField($fieldId);
	}

	/**
	 * @param array $rowFields List of strings of row field ids
	 * @throws InvalidFieldNameException if one of the fields does not exist
	 */
	public function setRowFields($rowFields)
	{
		$this->rowFields = array();
		$this->isFixed = false;

		foreach($rowFields as $fieldId)
			$this->addRowField($fieldId);
	}

	/**
	 * @param array $dataFields List of strings of data field ids
	 * @throws InvalidFieldNameException if one of the fields does not exist
	 */
	public function setDataFields($dataFields)
	{
		$this->dataFields = array();
		$this->isFixed = false;

		foreach($dataFields as $fieldId)
			$this->addDataField($fieldId);
	}

	/**
	 * @param string $fieldId
	 * @throws InvalidFieldNameException if one of the fields does not exist
	 */
	public function addRowField($fieldId)
	{
		$this->verifyTheseFieldsExist(array($fieldId), 'row');

		$this->rowFields[] = $fieldId;
		$this->isFixed = false;
	}

	/**
	 * @param string $fieldId
	 * @throws InvalidFieldNameException if one of the fields does not exist
	 */
	public function addColumnField($fieldId)
	{
		$this->verifyTheseFieldsExist(array($fieldId), 'column');

		$this->columnFields[] = $fieldId;
		$this->isFixed = false;
	}

	/**
	 * @param string $fieldId
	 * @param AggregationType|string $aggregationType
	 * @throws InvalidFieldNameException if one of the fields does not exist
	 */
	public function addDataField($fieldId, $aggregationType = AggregationType::SUM)
	{
		$this->verifyTheseFieldsExist(array($fieldId), 'data');

		$this->dataFields[] = $fieldId;
		$this->setAggregationType($fieldId, $aggregationType);
		$this->isFixed = false;
	}

	/**
	 * @param string $fieldId
	 * @param AggregationType|string|null $aggregationType
	 * @throws InvalidFieldNameException if one of the fields does not exist
	 */
	public function addField($fieldId, $aggregationType = null)
	{
		if($aggregationType === null)
			$this->addRowField($fieldId);
		else
			$this->addDataField($fieldId, $aggregationType);
	}

	/**
	 * Apply default settings to rowFields and dataFields if needed
	 * @throws NoDataFieldsException If there are no row fields
	 * @throws Exception if the DataTable cannot answer this pivot request
	 */
	protected function fixupFields()
	{
		$this->isFixed = true;

		// If they didn't give us any column fields, there is nothing to pivot!
		if(empty($this->columnFields))
			return;

		// If they didn't give us any row fields, by default the first field is the row field
		if($this->rowFields === null)
			$this->rowFields = array($this->dataTable->getColumnDescription(0)->getId());

		// If they didn't give us any data fields, by default ALL fields that are neither
		// column fields nor row fields are data fields
		if($this->dataFields === null)
		{
			$dataFields = array();
			$allColumnIds = $this->getAllColumnIds($this->dataTable->getColumnDescriptions());
			foreach($allColumnIds as $fieldId)
				if(! in_array($fieldId, $this->columnFields) && ! in_array($fieldId, $this->rowFields))
					$dataFields[] = $fieldId;
			$this->dataFields = $dataFields;
		}

		if(count($this->dataFields) === 0)
			throw new NoDataFieldsException();

		// Verify that the same field is not specified in multiple types
		$this->verifyNoDuplicateFields($this->columnFields, $this->rowFields, 'column', 'row');
		$this->verifyNoDuplicateFields($this->columnFields, $this->dataFields, 'column', 'data');
		$this->verifyNoDuplicateFields($this->rowFields, $this->dataFields, 'row', 'data');

		// Verify the minimum number of fields exist in this DataTable to perform this pivot
		$numColumns = $this->dataTable->getNumberOfColumns();
		$minimumNumColumns = count($this->columnFields) + count($this->rowFields) + count($this->dataFields);
		if($numColumns < $minimumNumColumns)
			throw new Exception("Not enough columns in this DataTable to pivot (you have ".$numColumns.
				", minimum is ".$minimumNumColumns.")");
	}

	/**
	 * @param array $fields List of field ids
	 * @param string $typeName
	 * @throws InvalidFieldNameException if one of the fields does not exist
	 */
	public function verifyTheseFieldsExist($fields, $typeName)
	{
		foreach($fields as $field) {
			try {
				$this->dataTable->getColumnDescriptionById($field);
			}
			catch(NoSuchColumnIdException $e) {
				throw new InvalidFieldNameException($field, $typeName, 0, $e);
			}
		}
	}

	/**
	 * Verify there are no duplicate fields defined
	 *
	 * If we do detect duplicate fields, show a nice friendly exception
	 * specifying which fields are duplicates and in what arrays they
	 * exist.
	 *
	 * @param array $fields1 List of one type of field
	 * @param array $fields2 List of another type of field
	 * @param string $name1 Name of first field type
	 * @param string $name2 Name of second field type
	 * @throws DuplicateFieldException if any fields exist in both $fields1 and $fields2
	 */
	public function verifyNoDuplicateFields($fields1, $fields2, $name1, $name2)
	{
		$total = array_merge($fields1, $fields2);
		$duplicates = array_intersect($total, $fields1, $fields2);

		if(count($duplicates))
			throw new DuplicateFieldException($name1, $name2, $duplicates);
	}

	/**
	 * @param array $columnDescriptions List of ColumnDescription objects
	 * @return array List of column IDs
	 */
	public function getAllColumnIds($columnDescriptions)
	{
		$allColumnIds = array();
		foreach($columnDescriptions as $column)
			$allColumnIds[] = $column->getId();
		return $allColumnIds;
	}
}