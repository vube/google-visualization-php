<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\test\Parser;

use Vube\GoogleVisualization\DataSource\Query\Parser\QueryParser;


class QueryParserTest extends \PHPUnit_Framework_TestCase {

	public function testSingleRawSelectEntityExtraction()
	{
		$text = "select foo";
		$parser = new QueryParser();
		$selection = $parser->readSelect($text);

		$this->assertSame(1, $selection->getNumberOfColumns());
		$this->assertSame(null, $selection->getColumnFunction(0));
		$this->assertSame('foo', $selection->getColumnText(0));
		$this->assertSame(null, $selection->getColumnLabel(0));
		$this->assertSame("", $text, "Text should now be empty");
	}

	public function testSingleAggregateSelectEntityExtraction()
	{
		$text = "select sum(foo)";
		$parser = new QueryParser();
		$selection = $parser->readSelect($text);

		$this->assertSame(1, $selection->getNumberOfColumns());
		$this->assertSame('sum', $selection->getColumnFunction(0));
		$this->assertSame('foo', $selection->getColumnText(0));
		$this->assertSame(null, $selection->getColumnLabel(0));
		$this->assertSame("", $text, "Text should now be empty");
	}

	public function testSingleRawSelectEntityWithLabelExtraction()
	{
		$text = 'select foo "FOO"';
		$parser = new QueryParser();
		$selection = $parser->readSelect($text);

		$this->assertSame(1, $selection->getNumberOfColumns());
		$this->assertSame(null, $selection->getColumnFunction(0));
		$this->assertSame('foo', $selection->getColumnText(0));
		$this->assertSame('FOO', $selection->getColumnLabel(0));
		$this->assertSame("", $text, "Text should now be empty");
	}

	public function testMultipleRawSelectEntityWithLabelExtraction()
	{
		$text = 'select foo "FOO", min(bar) "Min bar", avg(baz) "Average baz"';
		$parser = new QueryParser();
		$selection = $parser->readSelect($text);

		$this->assertSame(3, $selection->getNumberOfColumns());
		$this->assertSame(null, $selection->getColumnFunction(0));
		$this->assertSame('foo', $selection->getColumnText(0));
		$this->assertSame('FOO', $selection->getColumnLabel(0));
		$this->assertSame('min', $selection->getColumnFunction(1));
		$this->assertSame('bar', $selection->getColumnText(1));
		$this->assertSame('Min bar', $selection->getColumnLabel(1));
		$this->assertSame('avg', $selection->getColumnFunction(2));
		$this->assertSame('baz', $selection->getColumnText(2));
		$this->assertSame('Average baz', $selection->getColumnLabel(2));
		$this->assertSame("", $text, "Text should now be empty");
	}

	public function testSingleRawPivotEntityExtraction()
	{
		$text = "pivot foo";
		$parser = new QueryParser();
		$selection = $parser->readPivot($text);

		$this->assertSame(1, $selection->getNumberOfColumns());
		$this->assertSame('foo', $selection->getColumnText(0));
		$this->assertSame("", $text, "Text should now be empty");
	}

	public function testSingleBacktickedPivotEntityExtraction()
	{
		$text = "pivot `foo bar`";
		$parser = new QueryParser();
		$selection = $parser->readPivot($text);

		$this->assertSame(1, $selection->getNumberOfColumns());
		$this->assertSame('foo bar', $selection->getColumnText(0));
		$this->assertSame("", $text, "Text should now be empty");
	}

	public function testMultipleRawPivotEntityExtraction()
	{
		$text = "pivot foo, bar, baz";
		$parser = new QueryParser();
		$selection = $parser->readPivot($text);

		$this->assertSame(3, $selection->getNumberOfColumns());
		$this->assertSame('foo', $selection->getColumnText(0));
		$this->assertSame('bar', $selection->getColumnText(1));
		$this->assertSame('baz', $selection->getColumnText(2));
		$this->assertSame("", $text, "Text should now be empty");
	}

	public function testFullySupportedParse()
	{
		$text = 'select foo "Foo"'
			  .      ', bar "Bar"'
			  .      ', baz'
			  . ' pivot foo, bar';

		$parser = new QueryParser($text);
		$query = $parser->parse();

		$select = $query->getSelect();
		$this->assertNotNull($select);
		$this->assertSame(3, $select->getNumberOfColumns(), "Expect 3 select columns");

		$pivot = $query->getPivot();
		$this->assertNotNull($pivot);
		$this->assertSame(2, $pivot->getNumberOfColumns(), "Expect 2 pivot columns");
	}
}