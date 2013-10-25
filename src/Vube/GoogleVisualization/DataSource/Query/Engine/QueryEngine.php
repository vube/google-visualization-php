<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\Engine;

use Vube\GoogleVisualization\DataSource\DataTable\DataTable;
use Vube\GoogleVisualization\DataSource\DataTable\TableRow;
use Vube\GoogleVisualization\DataSource\Query\PivotDescription;
use Vube\GoogleVisualization\DataSource\Query\Query;


/**
 * QueryEngine class
 *
 * @author Ross Perkins <ross@vubeology.com>
 */
class QueryEngine {

	public static function & execute(Query $query, DataTable &$data)
	{
		// Reduce the source data down to only the data we need for this query
		$select = $query->getSelect();
		if($select !== null)
			$data =& self::reduceData($query, $data);

		// Perform data grouping
		// (Not implemented)

		// Perform data pivoting
		$pivot = $query->getPivot();
		if($pivot !== null)
			$data =& self::doPivot($query, $data);

		return $data;
	}

	public static function & reduceData(Query $query, DataTable &$data)
	{
		// If we aren't narrowing the selection, return the source data
		$select = $query->getSelect();
		if($select === null)
			return $data;

		// Get a list of the fields we want to select
		$selectFieldIds = $select->getAllColumnIds();
		$neededFieldIds = $selectFieldIds;

		// If we're going to pivot, we also need to preserve any/all
		// of the pivot fields.
		$pivot = $query->getPivot();
		if($pivot !== null)
			$neededFieldIds = array_merge($neededFieldIds, $pivot->getAllColumnIds());

		// Keep just the unique field ids we need
		$neededFieldIds = array_unique($neededFieldIds);

		// If the number of unique selected fields equals the number of
		// input columns, we need all the data in the source table, so
		// just return the source table itself.

		$numInputColumns = $data->getNumberOfColumns();
		if(count($neededFieldIds) == $numInputColumns)
			return $data;

		// We're removing at least one column from the source table

		$result = new DataTable();

		// Copy the columns we're saving

		$preservedColumnIndexes = array();

		for($ci=0; $ci<$numInputColumns; $ci++)
		{
			$column = $data->getColumnDescription($ci);
			if(! in_array($column->getId(), $neededFieldIds))
				continue;

			$result->addColumn($column);
			$preservedColumnIndexes[] = $ci;
		}

		// For each row, copy the cells from the columns we're saving

		$numInputRows = $data->getNumberOfRows();

		for($ri=0; $ri<$numInputRows; $ri++)
		{
			$inputRow = $data->getRow($ri);

			// Copy just the columns we're saving
			$row = new TableRow();
			foreach($preservedColumnIndexes as $ci)
				$row->addCell($inputRow->getCell($ci));

			$result->addRow($row);
		}

		return $result;
	}

	public static function & doPivot(Query $query, DataTable &$data)
	{
		// If there is no pivot clause specified in the query,
		// return the source data
		$pivot = $query->getPivot();
		if($pivot === null)
			return $data;

		// Generate a pivot description so we know what fields are
		// used for what.

		$pivotDescription = new PivotDescription($data);
		$pivotDescription->setColumnFields($pivot->getAllColumnIds());

		$select = $query->getSelect();
		if($select !== null)
		{
			$numSelectedFields = $select->getNumberOfColumns();
			for($i=0; $i<$numSelectedFields; $i++)
			{
				$fieldId = $select->getColumnText($i);
				$func = $select->getColumnFunction($i);

				// adds a Row field if $func is null, else adds a data field
				$pivotDescription->addField($fieldId, $func);
			}
		}

		$pivotAction = new PivotAction($pivotDescription);
		$result =& $pivotAction->execute();
		return $result;
	}
} 