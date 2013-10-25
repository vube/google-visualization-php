<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\Exception;

use Vube\GoogleVisualization\DataSource\Exception;


/**
 * InvalidFieldIdException class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class InvalidFieldIdException extends Exception {

	/**
	 * @param string $fieldId Field ID
	 * @param string $extraMessage [optional]
	 * @param int $code [optional]
	 * @param \Exception $previous [optional]
	 */
	public function __construct($fieldId, $extraMessage = '',
	                            $code = 0, \Exception $previous = null)
	{
		$message = "Invalid field id: ".$fieldId;
		if($extraMessage !== '')
			$message .= " ".$extraMessage;
		parent::__construct($message, $code, $previous);
	}
} 