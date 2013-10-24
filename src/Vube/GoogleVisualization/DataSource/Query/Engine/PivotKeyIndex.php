<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\Engine;

use Vube\GoogleVisualization\DataSource\DataTable\DataTable;


/**
 * PivotKeyIndex class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class PivotKeyIndex {

	/**
	 * @var DataTable
	 */
	private $dataTable;
	/**
	 * @var array
	 */
	private $columnIndexes;
	/**
	 * @var array
	 */
	private $keys;

	/**
	 * @param DataTable &$dataTable [IN]
	 * @param array $columnIndexes List of column indexes used by this key
	 */
	public function __construct(DataTable &$dataTable, $columnIndexes)
	{
		$this->dataTable =& $dataTable;
		$this->columnIndexes = $columnIndexes;
		$this->keys = array();
	}

	public function getKeys()
	{
		return $this->keys;
	}

	/**
	 * @param int $rowIndex
	 * @return PivotKey reference
	 */
	public function & getKeyRefByRowIndex($rowIndex)
	{
		$row = $this->dataTable->getRow($rowIndex);

		$columnValues = array();
		foreach($this->columnIndexes as $ci)
			$columnValues[] = $row->getCell($ci)->getValue()->getRawValue();

		$hash = PivotKey::hash($columnValues);
		if(! isset($this->keys[$hash]))
			$this->keys[$hash] = new PivotKey($columnValues);

		$key =& $this->keys[$hash];
		return $key;
	}

	/**
	 * @param string $hash
	 * @return PivotKey reference
	 * @throws Exception
	 */
	public function & getKeyRefByHash($hash)
	{
		if(! isset($this->keys[$hash]))
			throw new Exception("No such hash key: ".$hash);

		$key =& $this->keys[$hash];
		return $key;
	}
}