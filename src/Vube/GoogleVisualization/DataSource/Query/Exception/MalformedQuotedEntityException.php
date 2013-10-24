<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\Exception;

use Vube\GoogleVisualization\DataSource\Exception;


/**
 * MalformedQuotedEntityException class
 *
 * @author Ross Perkins <ross@vubeology.com>
 */
class MalformedQuotedEntityException extends Exception {

	public function __construct($text)
	{
		$message = "Malformed quoted entity: ".$text;
		parent::__construct($message);
	}
} 