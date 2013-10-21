<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Base;


/**
 * ResponseStatus class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class ResponseStatus {

	/**
	 * @var StatusType
	 */
	private $statusType;
	/**
	 * @var ReasonType|null
	 */
	private $reasonType;
	/**
	 * @var string|null
	 */
	private $description;

	/**
	 * Constructor
	 * @param StatusType $statusType
	 * @param ReasonType $reasonType [optional]
	 * @param string $description [optional]
	 */
	public function __construct(StatusType $statusType, ReasonType $reasonType=null, $description=null)
	{
		$this->statusType = $statusType;
		$this->reasonType = $reasonType;
		$this->description = $description;
	}

	/**
	 * @return StatusType
	 */
	public function getStatusType()
	{
		return $this->statusType;
	}

	/**
	 * @return ReasonType|null
	 */
	public function getReasonType()
	{
		return $this->reasonType;
	}

	/**
	 * @return string|null
	 */
	public function getDescription()
	{
		return $this->description;
	}
}