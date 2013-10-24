<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\Exception;

use Vube\GoogleVisualization\DataSource\Exception;


/**
 * InvalidAggregationTypeException class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class InvalidAggregationTypeException extends Exception {

	public function __construct($code)
	{
		$message = "Invalid AggregationType code: ".$code;
		parent::__construct($message);
	}
}