<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\ScalarFunction;

use Vube\GoogleVisualization\DataSource\DataTable\TableCell;
use Vube\GoogleVisualization\DataSource\DataTable\Value\Value;


/**
 * iDataContainer interface
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
interface iDataContainer {

	/**
	 * @param TableCell $cell
	 */
	public function addCell(TableCell $cell);

	/**
	 * @return Value
	 */
	public function getComputedValue();
}