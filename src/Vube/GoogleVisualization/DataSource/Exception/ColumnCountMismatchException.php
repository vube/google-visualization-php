<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Exception;

use Vube\GoogleVisualization\DataSource\Exception;


/**
 * ColumnCountMismatchException class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class ColumnCountMismatchException extends Exception {

	/**
	 * Construct the exception. Note: The message is NOT binary safe.
	 * @link http://php.net/manual/en/exception.construct.php
	 * @param int $expectedColumnCount [required] Number of columns we expected to find.
	 * @param int $actualColumnCount [required] Number of columns we actually found.
	 * @param int $code [optional] The Exception code.
	 * @param \Exception $previous [optional] The previous exception used for the exception chaining.
	 */
	public function __construct($expectedColumnCount, $actualColumnCount, $code=0, \Exception $previous=null)
	{
		$message = "Unexpected column count, expected ".$expectedColumnCount.", got ".$actualColumnCount;
		parent::__construct($message, $code, $previous);
	}

}