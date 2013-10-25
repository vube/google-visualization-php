<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\Parser;

use Vube\GoogleVisualization\DataSource\Exception\NotImplementedException;
use Vube\GoogleVisualization\DataSource\Query\Exception\InvalidQueryException;
use Vube\GoogleVisualization\DataSource\Query\Query;
use Vube\GoogleVisualization\DataSource\Query\QuerySelection;


/**
 * QueryParser class
 *
 * @author Ross Perkins <ross@vubeology.com>
 */
class QueryParser {

	/**
	 * @var Query
	 */
	private $query;

	/**
	 * @var array
	 */
	private static $quoteTypes = array("`", "'", '"');

	/**
	 * @param string|null $text
	 */
	public function __construct($text = null)
	{
		$this->query = new Query($text);
	}

	/**
	 * Parse the query
	 * @throws \Exception any number of exceptions may be thrown on errors
	 * @return Query
	 */
	public function parse()
	{
		if($this->query->isEmpty())
			return $this->query;

		$text = $this->query->getText();

		$this->query->setSelect($this->readSelect($text));
		$this->readWhere($text);
		$this->readGroupBy($text);
		$this->query->setPivot($this->readPivot($text));
		$this->readOrderBy($text);
		$this->readSkipping($text);
		$this->readLimit($text);
		$this->readOffset($text);
		$this->readLabel($text);
		$this->readFormat($text);
		$this->readOptions($text);

		if($text !== '')
			throw new InvalidQueryException("Syntax error near: ".$text);

		return $this->query;
	}

	/**
	 * @param string &$text [IN] [OUT] The select command is removed from the string
	 * @return QuerySelection|null
	 * @throws InvalidQueryException
	 */
	public function readSelect(&$text)
	{
		if(! preg_match("/^\s*select\s+(.*)/i", $text, $match))
			return null;

		$selection = new QuerySelection();
		$workingText = $match[1];

		$functionName = "(?<func>sum|avg|min|max|count)";
		$rawName = "[a-z][a-z0-9_]*";
		$backtickedName = "`[^`]+`";
		$doubleQuotedName = '"(?:\\\\.|[^\\\\"])*"';

		// Expect field id
		$expectFields = true;
		while($expectFields &&
			preg_match("/($functionName\()?(?<column>$backtickedName|$rawName|\*)(?<closingParen>\))?(\s+(?<label>$doubleQuotedName))?(?<comma>\s*,\s*)?(?<end>.*)/i", $workingText, $match))
		{
			// If there is no func(field) specified, set $func=null
			if($match['func'] === '')
				$func = null;
			// Else if there is func(field but no ")", throw exception
			else if($match['closingParen'] === '')
				throw new InvalidQueryException("Unclosed parenthesis near: ".$workingText);
			else // There is func(field), remember the func
				$func = $match['func'];

			$columnEntity = new QuotedEntity($match['column']);
			$columnName = $columnEntity->getText();

			if($match['label'] === '')
				$label = null;
			else
			{
				$labelEntity = new QuotedEntity($match['label']);
				$label = $labelEntity->getText();
			}

			$selection->addColumn($columnName, $label, $func);
			$workingText = $match['end'];

			// Keep looking for fields if we found a comma at the end of this field
			$expectFields = $match['comma'] !== '';
		}

		$text = $workingText;
		return $selection;
	}

	public function readWhere(&$text)
	{
		if(preg_match("/^\s*where\s/i", $text))
			throw new NotImplementedException();
	}

	public function readGroupBy(&$text)
	{
		if(preg_match("/^\s*group\s+by\s/i", $text))
			throw new NotImplementedException();
	}

	/**
	 * @param string &$text [IN] [OUT] The select command is removed from the string
	 * @return QuerySelection|null
	 */
	public function readPivot(&$text)
	{
		if(! preg_match("/^\s*pivot\s+(.+)/i", $text, $match))
			return null;

		$selection = new QuerySelection();
		$workingText = $match[1];

		$rawName = "[a-z][a-z0-9_]*";
		$backtickedName = "`[^`]+`";

		// Expect field id
		$expectFields = true;
		while($expectFields &&
			preg_match("/(?<column>$backtickedName|$rawName)(?<comma>\s*,\s*)?(?<end>.*)/i", $workingText, $match))
		{
			$columnEntity = new QuotedEntity($match['column']);
			$columnName = $columnEntity->getText();

			$selection->addColumn($columnName);
			$workingText = $match['end'];

			// Keep looking for fields if we found a comma at the end of this field
			$expectFields = $match['comma'] !== '';
		}

		$text = $workingText;
		return $selection;
	}

	public function readOrderBy(&$text)
	{
		if(preg_match("/^\s*order\s+by\s/i", $text))
			throw new NotImplementedException();
	}

	public function readSkipping(&$text)
	{
		if(preg_match("/^\s*skipping\s/i", $text))
			throw new NotImplementedException();
	}

	public function readLimit(&$text)
	{
		if(preg_match("/^\s*limit\s/i", $text))
			throw new NotImplementedException();
	}

	public function readOffset(&$text)
	{
		if(preg_match("/^\s*offset\s/i", $text))
			throw new NotImplementedException();
	}

	public function readLabel(&$text)
	{
		if(preg_match("/^\s*label\s/i", $text))
			throw new NotImplementedException();
	}

	public function readFormat(&$text)
	{
		if(preg_match("/^\s*format\s/i", $text))
			throw new NotImplementedException();
	}

	public function readOptions(&$text)
	{
		if(preg_match("/^\s*options\s/i", $text))
			throw new NotImplementedException();
	}

}