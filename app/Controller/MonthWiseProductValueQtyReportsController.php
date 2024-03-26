<?php
App::uses('AppController', 'Controller');
/**
 * OutletCharacteristicSettings Controller
 *
 * @property OutletCharacteristicReport $OutletCharacteristicReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class MonthWiseProductValueQtyReportsController extends AppController
{



	/**
	 * Components
	 *
	 * @var array
	 */

	public $uses = array('Memo', 'Office', 'Territory', 'Division', 'District', 'Thana', 'Market', 'OutletCategory', 'Outlet', 'TerritoryAssignHistory', 'SalesPerson', 'Product');
	public $components = array('Paginator', 'Session', 'Filter.Filter');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index($id = null)
	{
		$this->Session->delete('detail_results');
		$this->Session->delete('outlet_lists');

		ini_set('memory_limit', '2048M');
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes


		$this->set('page_title', "Day wise product volume and qty report");

		$territories = array();
		$districts = array();
		$thanas = array();
		$markets = array();
		$outlets = array();
		$request_data = array();
		$report_type = array();
		$so_list = array();

		//for region office list
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id' => 3),
			'order' => array('office_name' => 'asc')
		));

		//types
		$rows = array(
			'product' => 'By Product',
			'area' => 'By Area',
		);
		$this->set(compact('rows'));

		//report type
		$unit_types = array(
			'1' => 'Sales Unit',
			'2' => 'Base Unit',
		);
		$this->set(compact('unit_types'));


		//product_measurement
		$product_measurement = $this->Product->find('list', array(
			//'conditions'=> $pro_conditions,
			'fields' => array('Product.id', 'Product.sales_measurement_unit_id'),
			'order' =>  array('order' => 'asc'),
			'recursive' => -1
		));
		$this->set(compact('product_measurement'));



		$region_office_id = 0;

		$office_parent_id = $this->UserAuth->getOfficeParentId();

		$this->set(compact('office_parent_id'));

		$office_conditions = array('Office.office_type_id' => 2);

		if ($office_parent_id == 0) {
			$office_id = 0;
		} elseif ($office_parent_id == 14) {
			$region_office_id = $this->UserAuth->getOfficeId();
			$region_offices = $this->Office->find('list', array(
				'conditions' => array('Office.office_type_id' => 3, 'Office.id' => $region_office_id),
				'order' => array('office_name' => 'asc')
			));

			$office_conditions = array('Office.parent_office_id' => $region_office_id);

			$office_id = 0;

			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,

					"NOT" => array("id" => array(30, 31, 37))
				),
				'order' => array('office_name' => 'asc')
			));

			$office_ids = array_keys($offices);

			if ($office_ids) $conditions['Territory.office_id'] = $office_ids;

			//pr($conditions);
			//exit;

			$districts = $this->District->find('list', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'alias' => 'Thana',
						'table' => 'thanas',
						'type' => 'INNER',
						'conditions' => 'District.id = Thana.district_id'
					),
					array(
						'alias' => 'ThanaTerritory',
						'table' => 'thana_territories',
						'type' => 'INNER',
						'conditions' => 'Thana.id = ThanaTerritory.thana_id'
					),
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'ThanaTerritory.territory_id = Territory.id'
					),

				),
				'order' =>  array('District.name' => 'asc')
			));
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
			$office_id = $this->UserAuth->getOfficeId();

			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'id' 	=> $office_id,
				),
				'order' => array('office_name' => 'asc')
			));

			/*$territories = $this->Territory->find('list', array(
				'conditions' => array('Territory.office_id' => $office_id),
				'order' => array('Territory.name' => 'asc')
				));	*/

			$territory_list = $this->Territory->find('all', array(
				'conditions' => array('Territory.office_id' => $office_id),
				'joins' => array(
					array(
						'alias' => 'User',
						'table' => 'users',
						'type' => 'INNER',
						'conditions' => 'SalesPerson.id = User.sales_person_id'
					)
				),
				'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => 0
			));

			$territories = array();

			foreach ($territory_list as $key => $value) {
				$territories[$value['Territory']['id']] = $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')';
			}

			//get SO list
			$so_list_r = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
				'conditions' => array(
					'SalesPerson.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					'User.user_group_id' => 4,
				),
				'recursive' => 0
			));

			foreach ($so_list_r as $key => $value) {
				$so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
			}

			$conditions['Territory.office_id'] = $office_id;
			$districts = $this->District->find('list', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'alias' => 'Thana',
						'table' => 'thanas',
						'type' => 'INNER',
						'conditions' => 'District.id = Thana.district_id'
					),
					array(
						'alias' => 'ThanaTerritory',
						'table' => 'thana_territories',
						'type' => 'INNER',
						'conditions' => 'Thana.id = ThanaTerritory.thana_id'
					),
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'ThanaTerritory.territory_id = Territory.id'
					),

				),
				'order' =>  array('District.name' => 'asc')
			));
		}
		$dis_con = array();
		$resultAray = array();

		$totalDay = 0;

		if ($this->request->is('post') || $this->request->is('put')) {

			$request_data = $this->request->data;
			//echo '<pre>';print_r($request_data);exit;
			$date_from = $request_data['MonthWiseProductValueQtyReports']['date_from'];
			$this->set(compact('date_from'));

			$region_office_id = isset($this->request->data['MonthWiseProductValueQtyReports']['region_office_id']) != '' ? $this->request->data['MonthWiseProductValueQtyReports']['region_office_id'] : $region_office_id;
			$this->set(compact('region_office_id'));

			$office_id = isset($this->request->data['MonthWiseProductValueQtyReports']['office_id']) != '' ? $this->request->data['MonthWiseProductValueQtyReports']['office_id'] : $office_id;

			$this->set(compact('office_id'));
			$territory_id = isset($this->request->data['MonthWiseProductValueQtyReports']['territory_id']) != '' ? $this->request->data['MonthWiseProductValueQtyReports']['territory_id'] : 0;
			$this->set(compact('territory_id'));

			$rows_by = $request_data['MonthWiseProductValueQtyReports']['rows'];
			$this->set(compact('rows_by'));

			$unit_type = $request_data['MonthWiseProductValueQtyReports']['unit_type'];

			if ($rows_by == 'product') {
				$productList = $this->Product->find('list', array(
					'conditions' => array('Product.product_type_id' => 1),
					'order' => array('Product.order asc'),
				));
			}

			$office_ids = array();
			$office_for_report = array();
			if ($region_office_id) {
				$office_conditions = array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,

					"NOT" => array("id" => array(30, 31, 37))
				);
				$offices = $this->Office->find('list', array(
					'conditions' => $office_conditions,
					'order' => array('office_name' => 'asc')
				));
				$office_ids = array_keys($offices);
				if ($rows_by == 'area') {
					if ($office_id) $office_conditions['Office.id'] = $office_id;
					$office_for_report = $this->Office->find('list', array(
						'conditions' => $office_conditions,
						'order' => array('order' => 'asc')
					));
				}
			} else {
				if ($rows_by == 'area') {
					$office_conditions = array(
						'office_type_id' 	=> 2,
						"NOT" => array("id" => array(30, 31, 37))
					);
					if ($office_id) $office_conditions['Office.id'] = $office_id;
					$office_for_report = $this->Office->find('list', array(
						'conditions' => $office_conditions,
						'order' => array('order' => 'asc')
					));
				}
			}
			//for report data
			//$district_id = isset($this->request->data['OutletCharacteristicReports']['district_id']) != '' ? $this->request->data['OutletCharacteristicReports']['district_id'] : 0;

			$thana_id = isset($this->request->data['MonthWiseProductValueQtyReports']['thana_id']) != '' ? $this->request->data['MonthWiseProductValueQtyReports']['thana_id'] : 0;

			$market_id = isset($this->request->data['MonthWiseProductValueQtyReports']['market_id']) != '' ? $this->request->data['MonthWiseProductValueQtyReports']['market_id'] : 0;

			$outlet_id = isset($this->request->data['MonthWiseProductValueQtyReports']['outlet_id']) != '' ? $this->request->data['MonthWiseProductValueQtyReports']['outlet_id'] : 0;

			//$outlet_category_id = isset($this->request->data['OutletCharacteristicReports']['outlet_category_id']) != '' ? $this->request->data['OutletCharacteristicReports']['outlet_category_id'] : 0;

			//territory list
			$territory_list = $this->Territory->find('all', array(
				'conditions' => array('Territory.office_id' => $office_id),
				'joins' => array(
					array(
						'alias' => 'User',
						'table' => 'users',
						'type' => 'INNER',
						'conditions' => 'SalesPerson.id = User.sales_person_id'
					)
				),
				'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => 0
			));

			$territories = array();

			foreach ($territory_list as $key => $value) {
				$territories[$value['Territory']['id']] = $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')';
			}

			//thana list
			if ($territory_id) {
				$t_conditions = array();
				//$t_conditions['Thana.district_id'] = $district_id;
				if ($territory_id) $t_conditions['ThanaTerritory.territory_id'] = $territory_id;

				$thanas = $this->Thana->find('list', array(
					'joins' => array(
						array(
							'alias' => 'ThanaTerritory',
							'table' => 'thana_territories',
							'type' => 'INNER',
							'conditions' => 'ThanaTerritory.thana_id=Thana.id'
						)
					),
					'conditions' => $t_conditions,
					'order' => array('Thana.name' => 'asc')
				));
			}

			//market list
			if ($thana_id) {
				$m_conditions = array();
				$m_conditions['Market.thana_id'] = $thana_id;
				$m_conditions['Market.is_active'] = 1;
				if ($territory_id) $m_conditions['Territory.id'] = $territory_id;

				$markets = $this->Market->find('list', array(
					'conditions' => $m_conditions,
					'order' => array('Market.name' => 'asc'),
					'recursive' => 1
				));
			}

			//echo '<pre>';print_r($thana_id);exit;

			//outlet list
			if ($market_id) {
				$outlets = $this->Outlet->find('list', array(
					'conditions' => array('Outlet.market_id' => $market_id, 'Outlet.is_active' => 1),
					'order' => array('Outlet.name' => 'asc')
				));
			}

			$infomonthyear = explode("-", $date_from);
			$month = $infomonthyear[0];
			$year = $infomonthyear[1];

			$conditions = array();

			$conditions = array(
				'MONTH(Memo.memo_date)' => $month,
				'YEAR(Memo.memo_date)' => $year,
				'Memo.gross_value >' => 0,
				'Memo.status !=' => 0,
				'MemoDetail.price >' => 0,
			);
			if ($outlet_id) {
				$conditions['Memo.outlet_id'] = $outlet_id;
			}

			if ($office_ids) $conditions['Memo.office_id'] = $office_ids;
			if ($office_id) $conditions['Memo.office_id'] = $office_id;

			if ($territory_id) $conditions['Memo.territory_id'] = $territory_id;
			if ($thana_id) $conditions['Memo.thana_id'] = $thana_id;
			if ($market_id) $conditions['Memo.market_id'] = $market_id;

			//echo '<pre>';print_r($conditions);exit;
			$fields = array();
			$group = array();
			$orders = array();
			$joins = array();
			if ($rows_by == 'area') {
				$fields =  array(
					'Memo.office_id',
					'Office.office_name',
					'Memo.memo_date',
					'ROUND(sum(case when MemoDetail.price > 0 then (ROUND((MemoDetail.sales_qty * (case when PM.qty_in_base is null then 1 else PM.qty_in_base end)),0)/(case when PM_sales.qty_in_base is null then 1 else PM_sales.qty_in_base end)) end),2,1) as sales_qty_sale_unit',
					'sum(case when MemoDetail.price > 0 then (ROUND((MemoDetail.sales_qty * (case when PM.qty_in_base is null then 1 else PM.qty_in_base end)),0)) end) as sales_qty_base_unit',
					'SUM(MemoDetail.sales_qty*MemoDetail.price) as price'
				);
				$group = array('Office.order',  'Office.office_name', 'Memo.memo_date',  'Memo.office_id');
				$orders = array('Office.order asc');
				$joins = array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'Memo.office_id = Office.id'
				);
			} else {
				$fields =  array(
					'MemoDetail.product_id',
					'Product.name', 'Memo.memo_date',
					'ROUND(sum(case when MemoDetail.price > 0 then (ROUND((MemoDetail.sales_qty * (case when PM.qty_in_base is null then 1 else PM.qty_in_base end)),0)/(case when PM_sales.qty_in_base is null then 1 else PM_sales.qty_in_base end)) end),2,1) as sales_qty_sale_unit',
					'sum(case when MemoDetail.price > 0 then (ROUND((MemoDetail.sales_qty * (case when PM.qty_in_base is null then 1 else PM.qty_in_base end)),0)) end) as sales_qty_base_unit',
					'SUM(MemoDetail.sales_qty) as sales_qty',
					'SUM(MemoDetail.sales_qty*MemoDetail.price) as price'
				);
				$group = array('Product.order',  'Product.name', 'Memo.memo_date',  'MemoDetail.product_id');
				$orders = array('Product.order asc');
				/* $joins = array(
					'alias' => 'Product',
					'table' => 'products',
					'type' => 'INNER',
					'conditions' => 'MemoDetail.product_id = Product.id'
				); */
			}
			$q_results = $this->Memo->find('all', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'alias' => 'MemoDetail',
						'table' => 'memo_details',
						'type' => 'INNER',
						'conditions' => 'Memo.id = MemoDetail.memo_id'
					),
					array(
						'alias' => 'Product',
						'table' => 'products',
						'type' => 'INNER',
						'conditions' => 'MemoDetail.product_id = Product.id'
					),
					array(
						'alias' => 'PM',
						'table' => 'product_measurements',
						'type' => 'LEFT',
						'conditions' => 'PM.product_id=MemoDetail.product_id and PM.measurement_unit_id=(case when MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0 then Product.sales_measurement_unit_id else MemoDetail.measurement_unit_id end)'
					),
					array(
						'alias' => 'PM_sales',
						'table' => 'product_measurements',
						'type' => 'LEFT',
						'conditions' => 'PM_sales.product_id=MemoDetail.product_id and PM_sales.measurement_unit_id=Product.sales_measurement_unit_id'
					),
					count($joins) > 1 ? $joins : '',
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'Memo.territory_id = Territory.id'
					),
					array(
						'alias' => 'SalesPeople',
						'table' => 'sales_people',
						'type' => 'INNER',
						'conditions' => 'Memo.sales_person_id = SalesPeople.id'
					),
					array(
						'alias' => 'Outlet',
						'table' => 'outlets',
						'type' => 'INNER',
						'conditions' => 'Memo.outlet_id = Outlet.id'
					),
					array(
						'alias' => 'Market',
						'table' => 'markets',
						'type' => 'INNER',
						'conditions' => 'Memo.market_id = Market.id'
					),
					array(
						'alias' => 'Thana',
						'table' => 'thanas',
						'type' => 'INNER',
						'conditions' => 'Memo.thana_id = Thana.id'
					),

				),

				'fields' => $fields,
				'group' => $group,
				'order' => $orders,

				'recursive' => -1
			));

			//echo '<pre>';print_r($q_results);exit;

			//echo $this->Memo->getLastQuery();exit;

			$resultAray = array();

			foreach ($q_results as $v) {
				$dayinfo = explode("-", $v['Memo']['memo_date']);
				$day = $dayinfo[2] + 0;
				$row_key = '';
				if ($rows_by == 'area') {
					$row_key = $v['Memo']['office_id'];
				} else {
					$row_key = $v['MemoDetail']['product_id'];
				}
				$qty = ($unit_type == 1) ?  $v[0]['sales_qty_sale_unit'] : $v[0]['sales_qty_base_unit'];
				$resultAray[$row_key][$day] = array(
					'memoDate' => $v['Memo']['memo_date'],
					//'sales_qty' => $v[0]['sales_qty'],
					'sales_qty' => $qty,
					'volume' => $v[0]['price'],
					'day' => $day
				);
			}

			$totalDay = cal_days_in_month(CAL_GREGORIAN, $month, $year);
			$productValue = '';
			$salevalueArray = '';

			/* echo '<pre>';
			print_r($resultAray);
			exit; */

			$output = '';

			$totalQty = 0;
			$totalValue = 0;
			$grandTotalValue = 0;
			$dayTotalValue = array();
			if (!empty($resultAray)) {
				if ($rows_by == 'area') {
					$row_generate = $office_for_report;
				} else {
					$row_generate = $productList;
				}
				foreach ($row_generate as $pkey => $pval) {

					@$productValue = $resultAray[$pkey];
					if (!empty($productValue)) {
						$output .= '<tr><td>' . $pval . '</td>';

						for ($j = 1; $j <= $totalDay; $j++) {

							@$salevalueArray = $productValue[$j];
							if (!empty($salevalueArray)) {
								$qty  = $salevalueArray['sales_qty'];
								$totalQty += $qty;
								$volume  = $salevalueArray['volume'];
								$totalValue += $volume;
								$grandTotalValue += $volume;
								if (isset($dayTotalValue[$j]))
									$dayTotalValue[$j] += $volume;
								else
									$dayTotalValue[$j] = $volume;
							} else {
								$qty = 0;
								$volume = 0;
							}
							$output .= '<td>' . number_format($qty, 2, '.', ',') . '</td><td>' . number_format($volume, 2, '.', ',') . '</td>';
						}
						$output .= '<td>' . number_format($totalQty, 2, '.', ',') . '</td><td>' . number_format($totalValue, 2, '.', ',') . '</td></tr>';
					}/* else{
						$output.='<tr><td>'.$pval.'</td>';
						for($x = 1; $x <= $totalDay; $x++) {
							$output.= '<td>0</td><td>0</td>';
						}
						$output.='<td>'.$totalQty.'</td><td>'.$totalValue.'</td></tr>';

						//echo '<pre>';print_r($output);exit;
					} */

					$totalQty = 0;
					$totalValue = 0;
				}
				$output .= '<tr><td> <b>Grand Total</b> </td>';
				for ($j = 1; $j <= $totalDay; $j++) {

					$output .= '<td> - </td><td><b>' . number_format(@$dayTotalValue[$j], 2, '.', ',') . '</b></td>';
				}
				$output .= '<td> - </td><td><b>' . number_format($grandTotalValue, 2, '.', ',') . '</b></td></tr>';
			} else {
				$output .= '
				<tr>
					<td colspan="63" align="left"> Data Not Found !.</td>
				</tr>';
			}

			//echo '<pre>';print_r($output);exit;

			//$this->set(compact('output'));

			//echo ($output);
			//exit;



			$this->set(compact('output'));
		}





		$this->set(compact('offices', 'output', 'productList', 'totalDay', 'territories', 'outlet_type', 'region_offices', 'thanas', 'markets', 'outlets', 'office_id', 'request_data'));
	}



	//get district list
	public function get_district_list()
	{
		$view = new View($this);

		$form = $view->loadHelper('Form');

		$this->loadModel('District');

		$office_id = 0;

		$region_office_id = $this->request->data['region_office_id'];

		if ($region_office_id) {
			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,

					"NOT" => array("id" => array(30, 31, 37))
				),
				'order' => array('office_name' => 'asc')
			));

			$office_id = array_keys($offices);
		}

		if ($this->request->data['office_id']) $office_id = $this->request->data['office_id'];

		$territory_id = $this->request->data['territory_id'];


		$conditions = array();

		if ($office_id) $conditions['Territory.office_id'] = $office_id;

		if ($territory_id) $conditions['Territory.id'] = $territory_id;



		//pr($conditions);
		//exit;

		$districts = $this->District->find('list', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'Thana',
					'table' => 'thanas',
					'type' => 'INNER',
					'conditions' => 'District.id = Thana.district_id'
				),
				array(
					'alias' => 'ThanaTerritory',
					'table' => 'thana_territories',
					'type' => 'INNER',
					'conditions' => 'Thana.id = ThanaTerritory.thana_id'
				),
				array(
					'alias' => 'Territory',
					'table' => 'territories',
					'type' => 'INNER',
					'conditions' => 'ThanaTerritory.territory_id = Territory.id'
				),

			),
			'order' =>  array('District.name' => 'asc')
		));


		if ($districts) {
			/*$form->create('OutletCharacteristicReports', array('role' => 'form', 'action'=>'index'))	;
			echo $form->input('district_id', array('id' => 'district_id', 'label'=>false, 'class' => 'checkbox district_box', 'onClick' => 'getThanaList()', 'fieldset' => false, 'multiple' => 'checkbox', 'options'=> $districts));
			$form->end();*/

			$output = '<div class="input select">
  <input type="hidden" name="data[OutletCharacteristicReports][district_id]" value="" id="district_id"/>';
			foreach ($districts as $key => $val) {
				$output .= '<div class="checkbox">
					<input type="checkbox" onClick="thanaBoxList()" name="data[OutletCharacteristicReports][district_id][]" value="' . $key . '" id="district_id' . $key . '" />
					<label for="district_id' . $key . '">' . $val . '</label>
				  </div>';
			}
			$output .= '</div>';

			echo $output;
		} else {
			echo '';
		}


		$this->autoRender = false;
	}


	//get thana list
	public function get_thana_list()
	{
		$view = new View($this);

		$form = $view->loadHelper('Form');

		$conditions = array();

		if ($this->request->data['territory_id']) $conditions['ThanaTerritory.territory_id'] = $this->request->data['territory_id'];

		$thana_list = $this->Thana->find('list', array(
			'conditions' => $conditions,
			'joins' => array(

				array(
					'alias' => 'ThanaTerritory',
					'table' => 'thana_territories',
					'type' => 'INNER',
					'conditions' => 'ThanaTerritory.thana_id=Thana.id'
				)

			),
			'order' =>  array('Thana.name' => 'asc')
		));


		if ($thana_list) {
			/*$form->create('OutletCharacteristicReports', array('role' => 'form', 'action'=>'index'))	;
				echo $form->input('thana_id', array('id' => 'thana_id', 'label'=>false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required'=> true, 'options'=> $thana_list));
				$form->end();*/

			$output = '<div class="input select">
                <input type="hidden" name="data[MonthWiseProductValueQtyReports][thana_id]" value="" id="thana_id"/>';
			foreach ($thana_list as $key => $val) {
				$output .= '<div class="checkbox">
						<input type="checkbox" onClick="marketBoxList()" name="data[MonthWiseProductValueQtyReports][thana_id][]" value="' . $key . '" id="thana_id' . $key . '" />
						<label for="thana_id' . $key . '">' . $val . '</label>
					  </div>';
			}
			$output .= '</div>';

			echo $output;
		} else {
			echo '';
		}



		$this->autoRender = false;
	}


	//get market list
	public function get_market_list()
	{
		$view = new View($this);

		$form = $view->loadHelper('Form');

		$thana_id = $this->request->data['thana_id'];

		$conditions = array();

		if ($thana_id) {
			$ids = explode(',', $thana_id);
			$conditions['Market.thana_id'] = $ids;
			$conditions['Market.is_active'] = 1;
			if ($this->request->data['territory_id']) $conditions['Territory.id'] = $this->request->data['territory_id'];


			//exit;

			$results = $this->Market->find('list', array(
				'conditions' => $conditions,
				'order' =>  array('Market.name' => 'asc'),
				'recursive' => 1
			));


			if ($results) {
				$output = '<div class="input select">
  <input type="hidden" name="data[MonthWiseProductValueQtyReports][market_id]" value="" id="market_id"/>';
				foreach ($results as $key => $val) {
					$output .= '<div class="checkbox">
						<input type="checkbox"  onClick="outletBoxList()" name="data[MonthWiseProductValueQtyReports][market_id][]" value="' . $key . '" id="market_id' . $key . '" />
						<label for="market_id' . $key . '">' . $val . '</label>
					  </div>';
				}
				$output .= '</div>';

				echo $output;
			} else {
				echo '';
			}
		} else {
			echo '';
		}


		$this->autoRender = false;
	}


	//get outlet list
	public function get_outlet_list()
	{
		$view = new View($this);

		$form = $view->loadHelper('Form');

		$market_id = $this->request->data['market_id'];

		$conditions = array();

		if ($market_id) {
			$ids = explode(',', $market_id);
			$conditions['Outlet.market_id'] = $ids;
			$conditions['Outlet.is_active'] = 1;
			//pr($conditions);
			//exit;

			$results = $this->Outlet->find('list', array(
				'conditions' => $conditions,
				'order' =>  array('Outlet.name' => 'asc')
			));


			if ($results) {
				/*$form->create('OutletCharacteristicReports', array('role' => 'form', 'action'=>'index'))	;
				echo $form->input('thana_id', array('id' => 'thana_id', 'label'=>false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required'=> true, 'options'=> $thana_list));
				$form->end();*/

				$output = '<div class="input select">
  <input type="hidden" name="data[MonthWiseProductValueQtyReports][outlet_id]" value="" id="outlet_id"/>';
				foreach ($results as $key => $val) {
					$output .= '<div class="checkbox">
						<input type="checkbox" name="data[MonthWiseProductValueQtyReports][outlet_id][]" value="' . $key . '" id="outlet_id' . $key . '" />
						<label for="outlet_id' . $key . '">' . $val . '</label>
					  </div>';
				}
				$output .= '</div>';

				echo $output;
			} else {
				echo '';
			}
		} else {
			echo '';
		}


		$this->autoRender = false;
	}
}
