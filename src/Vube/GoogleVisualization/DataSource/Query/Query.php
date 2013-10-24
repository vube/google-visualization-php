<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query;

use Vube\GoogleVisualization\DataSource\Query\Exception\InvalidQueryException;
use Vube\GoogleVisualization\DataSource\Query\Parser\QueryParser;


/**
 * Query class
 *
 * @author Ross Perkins <ross@vubeology.com>
 */
class Query {

	private $text;

	/**
	 * @var null|QuerySelection
	 */
	private $select = null;
	/**
	 * @var null|QuerySelection
	 */
	private $pivot = null;

	public function __construct($text)
	{
		if($text === '')
			$text = null;

		$this->text = $text;
	}

	public function isEmpty()
	{
		return $this->text === null;
	}

	public function getText()
	{
		return $this->text;
	}

	public function getSelect()
	{
		return $this->select;
	}

	public function getPivot()
	{
		return $this->pivot;
	}

	/**
	 * @param QuerySelection|null $selection
	 */
	public function setSelect($selection)
	{
		$this->select = $selection;
	}

	/**
	 * @param QuerySelection|null $selection
	 */
	public function setPivot($selection)
	{
		$this->pivot = $selection;
	}

	/**
	 * @param string|null $text
	 * @return Query
	 * @throws InvalidQueryException
	 */
	public static function constructFromString($text)
	{
		try
		{
			$parser = new QueryParser($text);
			$query = $parser->parse();
			return $query;
		}
		catch(\Exception $e)
		{
			throw new InvalidQueryException("Query parse error (".$e->getMessage()."): ".$text,
				$e->getCode(), $e);
		}
	}
}