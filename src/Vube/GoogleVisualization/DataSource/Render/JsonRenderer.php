<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Render;

use Vube\GoogleVisualization\DataSource\Base\ReasonType;
use Vube\GoogleVisualization\DataSource\Base\ResponseStatus;
use Vube\GoogleVisualization\DataSource\Base\StatusType;
use Vube\GoogleVisualization\DataSource\DataTable\ColumnDescription;
use Vube\GoogleVisualization\DataSource\DataTable\DataTable;
use Vube\GoogleVisualization\DataSource\DataTable\TableCell;
use Vube\GoogleVisualization\DataSource\DataTable\Value\ValueType;
use Vube\GoogleVisualization\DataSource\Exception\NoSuchValueTypeException;
use Vube\GoogleVisualization\DataSource\OutputType;
use Vube\GoogleVisualization\DataSource\RequestParameters;
use Vube\GoogleVisualization\DataSource\Response;


/**
 * JsonRenderer class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class JsonRenderer implements iRenderer {

	/**
	 * If TRUE, we will optimize NULL values by omitting them from the JSON output
	 *
	 * Note that this causes PHP json_decode to FAIL, and probably causes
	 * other JSON libraries to fail.  However Google Visualization JavaScript
	 * API apparently handles this situation correctly.
	 *
	 * I disable it by false since it's non-standard practise.
	 * @var bool
	 */
	private $optimizeNullValues;

	/**
	 * @param bool $optimizeNullValues [optional] Default=FALSE
	 */
	public function __construct($optimizeNullValues=false)
	{
		$this->optimizeNullValues = $optimizeNullValues;
	}

	/**
	 * @param Response $response
	 * @param ResponseStatus $responseStatus
	 * @return string JSON or JSONP output
	 */
	public function render(Response $response, ResponseStatus $responseStatus=null)
	{
		$output = '';

		$request = $response->getRequest();
		$outputTypeCode = $request->getOutputType()->getCode();

		$isJsonP = $outputTypeCode === OutputType::JSONP;

		if($isJsonP)
		{
			$responseHandler = $request->getParameters()->getParameter(RequestParameters::RESPONSE_HANDLER_PARAMETER);
			$output .= $responseHandler.'(';
		}

		$dataTable = $response->getDataTable();

		$output .= "{\"version\":\"0.6\"";

		$reqId = $request->getRequestId();
		if($reqId !== false)
			$output .= ",\"reqId\":" . json_encode($reqId);

		if($responseStatus === null)
		{
			$sig = $request->getSignature();
			if($sig !== false && $sig === $this->getSignature($dataTable))
			{
				$responseStatus = new ResponseStatus(
					new StatusType(StatusType::ERROR),
					new ReasonType(ReasonType::NOT_MODIFIED)
				);
			}
			else
			{
				$responseStatus = new ResponseStatus(
					new StatusType(StatusType::OK)
				);
			}
		}

		$statusTypeCode = $responseStatus->getStatusType()->getCode();
		$output .= ",\"status\":" . json_encode($statusTypeCode);

		if($statusTypeCode != StatusType::OK)
		{
			if($statusTypeCode == StatusType::WARNING)
			{
				$warnings = $dataTable->getWarnings();

				$warningStrings = array();
				foreach($warnings as $warning)
					$warningStrings[] = $this->getFaultString($warning->getReasonType(), $warning->getMessage());

				$output .= ",\"warnings\":[" . implode(",", $warningStrings) . "]";
			}
			else // $statusTypeCode == StatusType::ERROR
			{
				$errorsOutput = $this->getFaultString($responseStatus->getReasonType(), $responseStatus->getDescription());
				$output .= ",\"errors\":[" . $errorsOutput . "]";
			}
		}

		if($statusTypeCode != StatusType::ERROR && $dataTable !== null)
		{
			$output .= ",\"sig\":" . json_encode($this->getSignature($dataTable));
			$output .= ",\"table\":" . $this->renderDataTable($dataTable, true, true, $isJsonP);
		}

		$output .= "}";
		if($isJsonP)
			$output .= ");";
		return $output;
	}

	/**
	 * @param ReasonType $reasonType
	 * @param string $description
	 * @return string JSON-encoded string describing an error or warning
	 */
	public function getFaultString(ReasonType $reasonType, $description)
	{
		$parts = array();

		if($reasonType !== null)
		{
			$parts[] = "\"reason\":" . json_encode($reasonType->getCode());
			$parts[] = "\"message\":" . json_encode($reasonType->getMessage());
		}

		if($description !== null)
			$parts[] = "\"detailed_message\":" . json_encode($description);

		return "{" . implode(",", $parts) . "}";
	}

	/**
	 * Generate the data table signature
	 *
	 * Here we use an MD5 hash of the data.  To generate that MD5 hash,
	 * we always render the DataTable as JSON (_NOT_ as JSONP), we don't
	 * care about the format we're giving back, just the data itself.
	 *
	 * @param DataTable $dataTable
	 * @return string Hashed value of $dataTable
	 */
	public function getSignature(DataTable $dataTable)
	{
		$render = $this->renderDataTable($dataTable, false);
		return md5($render);
	}

	/**
	 * @param DataTable $dataTable
	 * @param bool $includeValues [optional]
	 * @param bool $includeFormatting [optional]
	 * @param bool $renderDateAsDateConstructor [optional]
	 * @return string JSON encoded string describing $dataTable
	 */
	public function renderDataTable(DataTable $dataTable, $includeValues=true, $includeFormatting=true, $renderDateAsDateConstructor=true)
	{
		$numColumns = $dataTable->getNumberOfColumns();
		$columns = $dataTable->getColumnDescriptions();

		$output = "{";
		$output .= "\"cols\":[";
		for($i=0; $i<$numColumns; $i++)
		{
			$column = $columns[$i];
			$output .= $this->renderColumnDescriptionJson($column);
			if($i+1 < $numColumns)
				$output .= ",";
		}
		$output .= "]";

		if($includeValues)
		{
			$numRows = $dataTable->getNumberOfRows();

			$output .= ",\"rows\":[";
			for($ri=0; $ri<$numRows; $ri++)
			{
				$row = $dataTable->getRow($ri);
				$numCells = $row->getNumberOfCells();

				$output .= "{\"c\":[";
				for($ci=0; $ci<$numCells; $ci++)
				{
					$cell = $row->getCell($ci);
					if($ci+1 < $numCells)
					{
						// For fields that are NOT the last field, renderCellJson() may return
						// an empty string if the value is NULL
						// @see https://code.google.com/p/google-visualization-java/source/browse/trunk/src/main/java/com/google/visualization/datasource/render/JsonRenderer.java
						$cellOutput = $this->renderCellJson($cell, $includeFormatting, $this->optimizeNullValues, $renderDateAsDateConstructor);
						$cellOutput .= ",";
					}
					else
					{
						// For fields that ARE the last field, a JSON string is returned
						// even for NULL values
						$cellOutput = $this->renderCellJson($cell, $includeFormatting, false, $renderDateAsDateConstructor);
					}
					$output .= $cellOutput;
				}
				$output .= "]";

				// Row properties
				$customProperties = $this->renderCustomPropertiesString($row->getCustomProperties());
				if($customProperties !== null)
					$output .= ",\"p\":".$customProperties;

				$output .= "}";
				if($ri+1 < $numRows)
					$output .= ",";
			}
			$output .= "]";
		}

		// Table properties

		$customProperties = $this->renderCustomPropertiesString($dataTable->getCustomProperties());
		if($customProperties !== null)
			$output .= ",\"p\":".$customProperties;

		$output .= "}";
		return $output;
	}

	/**
	 * @param ColumnDescription $column
	 * @return string
	 */
	public function renderColumnDescriptionJson(ColumnDescription $column)
	{
		$output = "{";
		$output .= "\"id\":".json_encode($column->getId());
		$label = $column->getLabel();
		if($label !== '')
			$output .= ",\"label\":".json_encode($label);
		$output .= ",\"type\":".json_encode($column->getType()->getTypeName());
		$pattern = $column->getPattern();
		if($pattern !== '')
			$output .= ",\"pattern\":".json_encode($pattern);
		$customProperties = $this->renderCustomPropertiesString($column->getCustomProperties());
		if($customProperties !== null)
			$output .= ",\"p\":".$customProperties;
		$output .= "}";
		return $output;
	}

	/**
	 * @param TableCell $cell
	 * @param bool $includeFormatting
	 * @param bool $optimizeNullValues [optional] Default=FALSE
	 * @param bool $renderDateAsDateConstructor [optional] Default=TRUE
	 * @return string JSON formatted string representing this TableCell
	 * @throws NoSuchValueTypeException
	 */
	public function renderCellJson(TableCell $cell, $includeFormatting, $optimizeNullValues=false, $renderDateAsDateConstructor=true)
	{
		$value = $cell->getValue();
		$valueType = $cell->getValueType();
		$valueTypeCode = $valueType->getCode();
		$isJsonNull = false;
		$escapedFormattedValue = null;

		$json = "";

		if($value->isNull())
		{
			$json .= "null";
			$isJsonNull = true;
		}
		else
		{
			switch($valueTypeCode)
			{
				case ValueType::BOOLEAN:
					$json .= $value->__toString();
					break;

				case ValueType::NUMBER:
				case ValueType::STRING:
					if($value->isNull())
						$json .= "null";
					else
						$json .= json_encode($value->__toString());
					break;

				case ValueType::DATE:
				case ValueType::DATETIME:
					if($value->isNull())
					{
						$json .= "null";
					}
					else
					{
						$date = $value->getValue();
						$year = $date->getYear();
						$month = $date->getMonth();
						$day = $date->getMonthDay();
						$dateValue = "Date($year,$month,$day";
						if($valueTypeCode === ValueType::DATETIME)
						{
							$hours = $date->getHours();
							$mins = $date->getMinutes();
							$secs = $date->getSeconds();
							$dateValue .= ",$hours,$mins,$secs";
						}
						$dateValue .= ")";
						if($renderDateAsDateConstructor)
							$json .= "new ".$dateValue;
						else
							$json .= "\"".$dateValue."\"";
					}
					break;

				case ValueType::TIMEOFDAY:
					if($value->isNull())
						$json .= "null";
					else
					{
						$date = $value->getValue();
						$hours = $date->getHours();
						$mins = $date->getMinutes();
						$secs = $date->getSeconds();
						$millis = $date->getMilliseconds();
						$json .= "[$hours,$mins,$secs,$millis]";
					}
					break;

				default:
					throw new NoSuchValueTypeException($valueTypeCode);
			}
		}

		if($includeFormatting)
		{
			$formattedValue = $cell->getFormattedValue();
			if(! $value->isNull() && $formattedValue !== null)
			{
				// For strings if the value === formattedValue then
				// don't send the formattedValue
				if($valueTypeCode !== ValueType::STRING || $value->getValue() !== $formattedValue)
					$escapedFormattedValue = json_encode($formattedValue);
			}
		}

		// Add a Json for this cell. And,
		// 1) If the formatted value is empty drop it.
		// 2) If the value is null, and it is not the last column in the row drop the entire Json.

		if($isJsonNull && $optimizeNullValues)
		{
			$output = "";
		}
		else
		{
			$output = "{";

			// Value
			$output .= "\"v\":" . $json;

			// Formatted value
			if($includeFormatting && $escapedFormattedValue != '')
				$output .= ",\"f\":" . $escapedFormattedValue;

			// Custom properties
			$customProperties = $this->renderCustomPropertiesString($cell->getCustomProperties());
			if($customProperties !== null)
				$output .= ",\"p\":" . $customProperties;

			$output .= "}";
		}

		return $output;
	}

	/**
	 * @param array $customProperties
	 * @return string
	 */
	public function renderCustomPropertiesString($customProperties)
	{
		if(is_array($customProperties) && count($customProperties))
		{
			$output = json_encode($customProperties);
			return $output;
		}

		return null;
	}
}