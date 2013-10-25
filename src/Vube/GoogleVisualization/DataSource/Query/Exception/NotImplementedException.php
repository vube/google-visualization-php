<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\Exception;

use Vube\GoogleVisualization\DataSource\Exception;


/**
 * NotImplementedException class
 *
 * @author Ross Perkins <ross@vubeology.com>
 */
class NotImplementedException extends Exception {

	public function __construct()
	{
		parent::__construct("Not implemented");
	}
}