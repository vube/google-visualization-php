<?php
/**
 * Query Example Servlet class
 *
 * This class creates a DataTable with static data.  You should
 * obviously update this to get the data from your source.
 *
 * This creates a data table with multiple row and column fields
 * to demonstrate the pivot capabilities of this library.
 *
 * @author Ross Perkins <ross@vubeology.com>
 */

use Vube\GoogleVisualization\DataSource\DataTable\ColumnDescription;
use Vube\GoogleVisualization\DataSource\DataTable\DataTable;
use Vube\GoogleVisualization\DataSource\DataTable\TableRow;
use Vube\GoogleVisualization\DataSource\DataTable\Value\ValueType;
use Vube\GoogleVisualization\DataSource\Date;
use Vube\GoogleVisualization\DataSource\Request;
use Vube\GoogleVisualization\DataSource\Servlet;

/**
 * MyServlet class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class MyQueryServlet extends Servlet {

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
	public function & getDataTable(Request $request)
	{
		$data = new DataTable();

		$data->addColumn(new ColumnDescription('date', ValueType::DATE, 'Date'));
		$data->addColumn(new ColumnDescription('country', ValueType::STRING, 'Country'));
		$data->addColumn(new ColumnDescription('region', ValueType::STRING, 'Region'));
		$data->addColumn(new ColumnDescription('income', ValueType::NUMBER, 'Income'));
		$data->addColumn(new ColumnDescription('expense', ValueType::NUMBER, 'Expense'));

		$countryRegions = array(
			'US' => array('TX', 'CA', 'WA'),
			'CA' => array(null),
		);

		for($i=0; $i<10; $i++)
		{
			$date = "2013-01-".sprintf("%02d",1+$i);

			foreach($countryRegions as $country => $regions)
			{
				foreach($regions as $region)
				{
					$income = 1000+rand(0,(1+$i)*10);
					$expense = -800-rand(0,(1+$i)*10);

					$row = new TableRow(array(new Date($date), $country, $region, $income, $expense));
					$data->addRow($row);
				}
			}
		}

		return $data;
	}
}