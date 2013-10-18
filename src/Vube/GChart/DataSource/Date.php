<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource;
use Vube\GChart\DataSource\Exception\InvalidDateFormatException;


/**
 * Date class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class Date extends \DateTime
{
	/**
	 * @param int|string $time [optional]
	 * @param \DateTimeZone $timezone [optional] UTC by default
	 */
	public function __construct($time = "now", \DateTimeZone $timezone = null)
	{
		if(is_int($time))
			$time = '@'.$time;

		if($timezone === null)
			$timezone = new \DateTimeZone('UTC');

		parent::__construct($time, $timezone);
	}
}