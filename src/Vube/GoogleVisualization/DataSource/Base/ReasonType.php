<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Base;

use Vube\GoogleVisualization\DataSource\Exception\InvalidReasonTypeException;


/**
 * ReasonType class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class ReasonType {

	/**
	 * The user has no access to the requested data.
	 */
	const ACCESS_DENIED = 'access_denied';

	/**
	 * The user cannot be authenticated. Used when the data requires
	 * authentication. Enable the data source to distinguish between
	 * 'no user' and 'user has no access' scenarios.
	 */
	const USER_NOT_AUTHENTICATED = 'user_not_authenticated';

	/**
	 * The query sent to the data source contains an operation that
	 * the data source does not support.
	 */
	const UNSUPPORTED_QUERY_OPERATION = 'unsupported_query_operation';

	/**
	 * The query sent to the data source contains invalid data.
	 */
	const INVALID_QUERY = 'invalid_query';

	/**
	 * The request from the client is invalid.
	 */
	const INVALID_REQUEST = 'invalid_request';

	/**
	 * An internal error occured.
	 */
	const INTERNAL_ERROR = 'internal_error';

	/**
	 * This operation is not supported.
	 */
	const NOT_SUPPORTED = 'not_supported';

	/**
	 * Not all data is retrieved.
	 */
	const DATA_TRUNCATED = 'data_truncated';

	/**
	 * The data hasn't been changed (signatures are the same).
	 */
	const NOT_MODIFIED = 'not_modified';

	/**
	 * The request has timed out. This is used only in the client, it is defined here for
	 * completeness.
	 */
	const TIMEOUT = 'timeout';

	/**
	 * Illegal user given formatting patterns.
	 */
	const ILLEGAL_FORMATTING_PATTERNS = 'illegal_formatting_patterns';

	/**
	 * Any other error that occured and prevented the data source from completing the action.
	 */
	const OTHER = 'other';

	/**
	 * @var string
	 */
	private $code;

	/**
	 * @param string $code Must be one of the constants described above
	 * @throws Exception if $code is not a recognized code string
	 */
	public function __construct($code)
	{
		self::validateType($code);
		$this->code = $code;
	}

	/**
	 * @return string
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @param string $code
	 * @throws Exception if $code is not a recognized code string
	 */
	public static function validateType($code)
	{
		switch($code)
		{
			case self::ACCESS_DENIED:
			case self::USER_NOT_AUTHENTICATED:
			case self::UNSUPPORTED_QUERY_OPERATION:
			case self::INVALID_QUERY:
			case self::INVALID_REQUEST:
			case self::INTERNAL_ERROR:
			case self::NOT_SUPPORTED:
			case self::DATA_TRUNCATED:
			case self::NOT_MODIFIED:
			case self::TIMEOUT:
			case self::ILLEGAL_FORMATTING_PATTERNS:
			case self::OTHER:
				break;

			// Anything else is not a valid ReasonType
			default:
				throw new InvalidReasonTypeException($code);
		}
	}

	/**
	 * @param string $code [optional]
	 * @return string
	 */
	public function getMessage($code=null)
	{
		if($code === null)
			$code = $this->code;

		// I18N: Allow for translations; for now we just convert code->English
		return str_replace('_', ' ', $code);
	}
}