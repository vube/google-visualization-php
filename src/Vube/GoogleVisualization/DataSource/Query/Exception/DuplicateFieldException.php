<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\Exception;

use Vube\GoogleVisualization\DataSource\Exception;


/**
 * DuplicateFieldException class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class DuplicateFieldException extends Exception {

	/**
	 * @param string $type1
	 * @param string $type2
	 * @param array $duplicates
	 */
	public function __construct($type1, $type2, $duplicates)
	{
		$message = "Duplicate fields specified as both $type1 and $type2: ".
			implode(", ", $duplicates);

		parent::__construct($message);
	}
} 