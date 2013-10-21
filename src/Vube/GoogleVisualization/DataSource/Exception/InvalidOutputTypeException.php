<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Exception;

use Vube\GoogleVisualization\DataSource\Exception;


/**
 * InvalidOutputTypeException class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class InvalidOutputTypeException extends Exception {

	public function __construct($code)
	{
		parent::__construct("Invalid Output Type: ".$code);
	}
}