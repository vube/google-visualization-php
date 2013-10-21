<?php
/**
 * Simple Example Servlet class
 *
 * This class creates a DataTable with static data.  You should
 * obviously update this to get the data from your source.
 *
 * @author Ross Perkins <ross@vubeology.com>
 */

use Vube\GoogleVisualization\DataSource\DataTable\ColumnDescription;
use Vube\GoogleVisualization\DataSource\DataTable\DataTable;
use Vube\GoogleVisualization\DataSource\DataTable\TableCell;
use Vube\GoogleVisualization\DataSource\DataTable\TableRow;
use Vube\GoogleVisualization\DataSource\DataTable\Value\DateValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\NumberValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\ValueType;
use Vube\GoogleVisualization\DataSource\Date;
use Vube\GoogleVisualization\DataSource\Request;
use Vube\GoogleVisualization\DataSource\Servlet;

/**
 * MyServlet class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class MyServlet extends Servlet {

	/**
	 * Constructor
	 *
	 * This disables restricted access mode so that you can execute
	 * this query from anywhere.
	 *
	 * This is not recommended in a production setting if you are
	 * returning sensitive data.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->isRestrictedAccessModeEnabled = false;
	}

	/**
	 * @param Request $request
	 * @return DataTable
	 */
	public function getDataTable(Request $request)
	{
		$data = new DataTable();

		$data->addColumn(new ColumnDescription('date', ValueType::DATE, 'Date'));
		$data->addColumn(new ColumnDescription('income', ValueType::NUMBER, 'Gross Income'));
		$data->addColumn(new ColumnDescription('expense', ValueType::NUMBER, 'Expenses'));
		$data->addColumn(new ColumnDescription('net', ValueType::NUMBER, 'Net Income'));

		for($i=0; $i<10; $i++)
		{
			$row = new TableRow();

			$date = "2013-01-".sprintf("%02d",1+$i);
			$income = 1000+rand(0,(1+$i)*10);
			$expense = -800-rand(0,(1+$i)*10);

			$row->addCell(new TableCell(new DateValue(new Date($date))));
			$row->addCell(new TableCell(new NumberValue($income)));
			$row->addCell(new TableCell(new NumberValue($expense)));
			$row->addCell(new TableCell(new NumberValue($income+$expense)));

			$data->addRow($row);
		}

		return $data;
	}
}