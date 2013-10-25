<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\Exception;

use Vube\GoogleVisualization\DataSource\Exception;


/**
 * InvalidFieldNameException class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class InvalidFieldNameException extends Exception {

	/**
	 * @param string $fieldName Name of the invalid field id
	 * @param int $fieldType Type of field you thought it was
	 * @param int $code [optional]
	 * @param \Exception $previous [optional]
	 */
	public function __construct($fieldName, $fieldType,
	                            $code = 0, \Exception $previous = null)
	{
		$message = "Invalid field name '$fieldName' specified in $fieldType config";
		parent::__construct($message, $code, $previous);
	}
} 