<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\test;

use Vube\GoogleVisualization\DataSource\Query\AggregationType;


class AggregationTypeTest extends \PHPUnit_Framework_TestCase {

	public function testValidateKnownTypes()
	{
		AggregationType::validateCode(AggregationType::SUM);
		AggregationType::validateCode(AggregationType::AVG);
		AggregationType::validateCode(AggregationType::MIN);
		AggregationType::validateCode(AggregationType::MAX);
		AggregationType::validateCode(AggregationType::COUNT);
	}

	public function testValidateUnknownType()
	{
		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Query\\Exception\\InvalidAggregationTypeException');
		AggregationType::validateCode('not-a-valid-type');
	}

	public function testConstructValidConstant()
	{
		$type = new AggregationType(AggregationType::SUM);
		$this->assertEquals(AggregationType::SUM, $type->getCode());
	}

	public function testConstructValidObject()
	{
		$typeSrc = new AggregationType(AggregationType::SUM);
		$type = new AggregationType($typeSrc);
		$this->assertEquals(AggregationType::SUM, $type->getCode());
	}

	public function testConstructInvalidConstant()
	{
		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Query\\Exception\\InvalidAggregationTypeException');
		$type = new AggregationType('not-a-valid-type');
	}
}
 