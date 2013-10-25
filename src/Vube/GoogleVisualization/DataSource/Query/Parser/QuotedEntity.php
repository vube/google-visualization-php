<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\Parser;
use Vube\GoogleVisualization\DataSource\Query\Exception\MalformedQuotedEntityException;


/**
 * QuotedEntity class
 *
 * @author Ross Perkins <ross@vubeology.com>
 */
class QuotedEntity {

	const RAW = null;
	const BACK_TICK = "`";
	const DOUBLE_QUOTE = '"';
	const SINGLE_QUOTE = "'";

	private $quoteType;
	private $text;

	public function __construct($text)
	{
		$this->quoteType = self::checkQuoteType($text);
		$this->text = $this->removeQuotes($text);
	}

	public function getQuoteType()
	{
		return $this->quoteType;
	}

	public function getText()
	{
		return $this->text;
	}

	public static function isQuoteChar($char)
	{
		switch($char)
		{
			case self::BACK_TICK: case self::DOUBLE_QUOTE: case self::SINGLE_QUOTE:
				return true;
			default:
				return false;
		}
	}

	public static function checkQuoteType($text)
	{
		// The first character should be a quote
		$char = substr($text, 0, 1);

		if(self::isQuoteChar($char))
		{
			// The string MUST end with this char if it's really a quote
			if(substr($text, -1) !== $char)
				throw new MalformedQuotedEntityException($text);
			return $char;
		}

		// This text is not quoted, it's a raw string
		return self::RAW;
	}

	public function removeQuotes($text)
	{
		if($this->quoteType !== self::RAW)
			$text = substr($text, 1, -1);
		return $text;
	}
} 