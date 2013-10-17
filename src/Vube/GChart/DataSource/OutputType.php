<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource;

use Vube\GChart\DataSource\Exception\InvalidOutputTypeException;


/**
 * OutputType class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class OutputType {

	const JSON = 'json';
	const JSONP = 'jsonp';
	const HTML = 'html';
	const CSV = 'csv';
	const TSV_EXCEL = 'tsv-excel';

	private static $validCodes = array(
		self::JSON,
		self::JSONP,
		self::HTML,
		self::CSV,
		self::TSV_EXCEL,
	);

	private $code;

	public function __construct($code)
	{
		if(! in_array($code, self::$validCodes))
			throw new InvalidOutputTypeException($code);

		$this->code = $code;
	}

	public function getCode()
	{
		return $this->code;
	}
}