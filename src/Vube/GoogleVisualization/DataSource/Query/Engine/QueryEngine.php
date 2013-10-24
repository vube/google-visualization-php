<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\Engine;

use Vube\GoogleVisualization\DataSource\DataTable\DataTable;
use Vube\GoogleVisualization\DataSource\Exception\NotImplementedException;
use Vube\GoogleVisualization\DataSource\Query\Query;


/**
 * QueryEngine class
 *
 * @author Ross Perkins <ross@vubeology.com>
 */
class QueryEngine {

	public static function & execute(Query $query, DataTable &$data)
	{
		throw new NotImplementedException();
		$result = null;
		return $result;
	}
} 