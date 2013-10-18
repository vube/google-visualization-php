<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\DataTable;

use Vube\GChart\DataSource\DataTable\Value\ValueType;


/**
 * ColumnDescription class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class ColumnDescription
{
	/**
	 * @var string
	 */
	public $id;
	/**
	 * @var ValueType
	 */
	public $type;
	/**
	 * @var string
	 */
	public $label;
	/**
	 * @var string
	 */
	public $pattern = "";
	/**
	 * @var array
	 */
	private $customProperties = array();

	/**
	 * Constructor
	 * @param string $id
	 * @param ValueType|int $type
	 * @param string $label
	 */
	public function __construct($id, $type, $label='')
	{
		$this->id = $id;
		$this->type = ($type instanceof ValueType) ? $type : new ValueType($type);
		$this->label = $label;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return ValueType
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * @return string
	 */
	public function getPattern()
	{
		return $this->pattern;
	}

	/**
	 * @param string $label
	 */
	public function setLabel($label)
	{
		$this->label = $label;
	}

	/**
	 * @param string $pattern
	 */
	public function setPattern($pattern)
	{
		$this->pattern = $pattern;
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

		return $this->customProperties[$name];
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