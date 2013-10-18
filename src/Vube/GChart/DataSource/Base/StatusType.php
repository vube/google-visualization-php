<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\Base;

use Vube\GChart\DataSource\Exception;


/**
 * StatusType class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class StatusType {

	/**
	 * The query completed successfully and the data can be returned.
	 */
	const OK = 'ok';

	/**
	 * The query failed to complete. In this case, no data table is passed in the response.
	 */
	const ERROR = 'error';

	/**
	 * The query completed with a warning. In some cases, part of the data is returned.
	 */
	const WARNING = 'warning';

	private $type;

	/**
	 * @param string $type Must be one of the constants described above
	 * @throws Exception if $type is not a recognized type string
	 */
	public function __construct($type)
	{
		$this->validateType($type);
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getCode()
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 * @throws Exception if $type is not a recognized type string
	 */
	public function validateType($type)
	{
		if($type !== self::OK &&
			$type !== self::ERROR &&
			$type !== self::WARNING)
			throw new Exception("Invalid StatusType: ".$type);
	}
}