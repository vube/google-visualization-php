<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\Base;


/**
 * Warning class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class Warning {

	/**
	 * @var ReasonType
	 */
	private $reasonType;
	/**
	 * @var string
	 */
	private $messageToUser;

	/**
	 * @param ReasonType $reasonType
	 * @param string $messageToUser
	 */
	public function __construct(ReasonType $reasonType, $messageToUser)
	{
		$this->reasonType = $reasonType;
		$this->messageToUser = $messageToUser;
	}

	/**
	 * @return ReasonType
	 */
	public function getReasonType()
	{
		return $this->reasonType;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->messageToUser;
	}
}