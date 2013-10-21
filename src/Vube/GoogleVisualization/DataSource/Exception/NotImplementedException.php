<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Exception;

use Vube\GoogleVisualization\DataSource\Exception;


/**
 * NotImplementedException class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class NotImplementedException extends Exception {

	/**
	 * Construct the exception. Note: The message is NOT binary safe.
	 * @link http://php.net/manual/en/exception.construct.php
	 * @param string $message [optional] A custom error message
	 * @param int $code [optional] The Exception code.
	 * @param Exception $previous [optional] The previous exception used for the exception chaining.
	 */
	public function __construct($message = "Not Implemented", $code = 0, Exception $previous = null) {
		parent::__construct($message, $code, $previous);
	}
}