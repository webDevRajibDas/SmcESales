<?php
App::uses('AppController', 'Controller');
/**
 * OutletCharacteristicSettings Controller
 *
 * @property OutletCharacteristicReport $OutletCharacteristicReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class OutletCharacteristicReportsController extends AppController
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


		$this->set('page_title', "Outlet Characteristics Report");

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
		$types = array(
			'territory' => 'By Terriotry',
			'so' => 'By SO',
		);
		$this->set(compact('types'));

		//report type
		$report_types = array(
			'visited' => 'Visited Outlet Information',
			'non_visited' => 'Non-Visited Outlet',
			'detail' => 'Outlet Wise Sales Detail',
			'summary' => 'Outlet Wise Sales Summary'
		);
		$this->set(compact('report_types'));


		//for outlet category list
		$outlet_categories = $this->OutletCategory->find('list', array(
			'conditions' => array('is_active' => 1),
			'order' => array('category_name' => 'asc')
		));
		$this->set(compact('outlet_categories'));



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
			$child_territory_id = $this->Territory->find('list', array(
				'conditions' => array(
					'parent_id !=' => 0,
					'Territory.office_id' => $office_id
				),
				'fields' => array('Territory.id', 'Territory.name'),
			));
			$territory_list = $this->Territory->find('all', array(
				'conditions' => array('Territory.office_id' => $office_id, 'NOT' => array('Territory.id' => array_keys($child_territory_id))),
				'joins' => array(
					array(
						'alias' => 'User',
						'table' => 'users',
						'type' => 'left',
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



		if ($this->request->is('post') || $this->request->is('put')) {

			$request_data = $this->request->data;

			$date_from = date('Y-m-d', strtotime($request_data['OutletCharacteristicReports']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['OutletCharacteristicReports']['date_to']));
			$this->set(compact('date_from', 'date_to'));
			$type = $this->request->data['OutletCharacteristicReports']['type'];
			$this->set(compact('type'));

			$report_type = $this->request->data['OutletCharacteristicReports']['report_type'];
			$this->set(compact('report_type'));

			$region_office_id = isset($this->request->data['OutletCharacteristicReports']['region_office_id']) != '' ? $this->request->data['OutletCharacteristicReports']['region_office_id'] : $region_office_id;
			$this->set(compact('region_office_id'));
			$office_ids = array();
			if ($region_office_id) {
				$offices = $this->Office->find('list', array(
					'conditions' => array(
						'office_type_id' 	=> 2,
						'parent_office_id' 	=> $region_office_id,

						"NOT" => array("id" => array(30, 31, 37))
					),
					'order' => array('office_name' => 'asc')
				));

				$office_ids = array_keys($offices);
			}

			$office_id = isset($this->request->data['OutletCharacteristicReports']['office_id']) != '' ? $this->request->data['OutletCharacteristicReports']['office_id'] : $office_id;
			$this->set(compact('office_id'));
			$territory_id = isset($this->request->data['OutletCharacteristicReports']['territory_id']) != '' ? $this->request->data['OutletCharacteristicReports']['territory_id'] : 0;
			$this->set(compact('territory_id'));

			$so_id = isset($this->request->data['OutletCharacteristicReports']['so_id']) != '' ? $this->request->data['OutletCharacteristicReports']['so_id'] : 0;
			$this->set(compact('so_id'));


			$unit_type = $this->request->data['OutletCharacteristicReports']['unit_type'];
			//$this->set(compact('unit_type'));
			$unit_type_text = ($unit_type == 2) ? 'Base' : 'Sales';
			$this->set(compact('unit_type_text'));


			//for report data
			$district_id = isset($this->request->data['OutletCharacteristicReports']['district_id']) != '' ? $this->request->data['OutletCharacteristicReports']['district_id'] : 0;

			$thana_id = isset($this->request->data['OutletCharacteristicReports']['thana_id']) != '' ? $this->request->data['OutletCharacteristicReports']['thana_id'] : 0;

			$market_id = isset($this->request->data['OutletCharacteristicReports']['market_id']) != '' ? $this->request->data['OutletCharacteristicReports']['market_id'] : 0;

			$outlet_id = isset($this->request->data['OutletCharacteristicReports']['outlet_id']) != '' ? $this->request->data['OutletCharacteristicReports']['outlet_id'] : 0;

			$outlet_category_id = isset($this->request->data['OutletCharacteristicReports']['outlet_category_id']) != '' ? $this->request->data['OutletCharacteristicReports']['outlet_category_id'] : 0;

			//territory list
			$child_territory_id = $this->Territory->find('list', array(
				'conditions' => array(
					'parent_id !=' => 0,
					'Territory.office_id' => $office_id
				),
				'fields' => array('Territory.id', 'Territory.name'),
			));
			$territory_list = $this->Territory->find('all', array(
				'conditions' => array('Territory.office_id' => $office_id, 'NOT' => array('Territory.id' => array_keys($child_territory_id))),
				'joins' => array(
					array(
						'alias' => 'User',
						'table' => 'users',
						'type' => 'left',
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
			/*$so_list_r = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id','SalesPerson.name', 'Territory.name'),
				'conditions' => array(
					'SalesPerson.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					'User.user_group_id' => 4,
				),
				'recursive'=> 0
			));	
			foreach($so_list_r as $key => $value)
			{
				$so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'].' ('.$value['Territory']['name'].')';
			}
			
			//add old so from territory_assign_histories
			if($office_id)
			{
				$conditions = array('Territory.office_id' => $office_id, 'TerritoryAssignHistory.assign_type'=>2);
				$conditions['TerritoryAssignHistory.date BETWEEN ? and ? '] = array($date_from, $date_to);
				//pr($conditions);
				$old_so_list = $this->TerritoryAssignHistory->find('all', array(
					'conditions' => $conditions,
					'order'=>  array('Territory.name'=>'asc'),
					'recursive'=> 0
				));
				if($old_so_list){
					foreach($old_so_list as $old_so){
						$so_list[$old_so['TerritoryAssignHistory']['so_id']] = $old_so['SalesPerson']['name'];
					}
				}
			}*/

			//NEW SO LIST GENERATE FROM MEMO TABLE
			$so_list_r = $this->SalesPerson->find('all', array(
				'fields' => array('distinct (Memo.sales_person_id) as so_id', 'SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
				'joins' => array(
					array(
						'table' => 'memos',
						'alias' => 'Memo',
						'type' => 'INNER',
						'conditions' => 'SalesPerson.id=Memo.sales_person_id',
					),
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'Memo.territory_id = Territory.id'
					),
				),
				'conditions' => array(
					'Memo.memo_date BETWEEN ? and ?' => array($date_from, $date_to),
					'Memo.office_id' => $office_id
				),
				'recursive' => -1
			));
			foreach ($so_list_r as $key => $value) {
				$so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
			}
			//add old so from territory_assign_histories
			if ($office_id) {
				$conditions = array('Territory.office_id' => $office_id, 'TerritoryAssignHistory.assign_type' => 2);
				// $conditions['TerritoryAssignHistory.date BETWEEN ? and ? '] = array($date_from, $date_to);
				$conditions['TerritoryAssignHistory.date >= '] = $date_from;
				//pr($conditions);
				$old_so_list = $this->TerritoryAssignHistory->find('all', array(
					'conditions' => $conditions,
					'order' =>  array('Territory.name' => 'asc'),
					'recursive' => 0
				));
				if ($old_so_list) {
					foreach ($old_so_list as $old_so) {
						$so_list[$old_so['TerritoryAssignHistory']['so_id']] = $old_so['SalesPerson']['name'];
					}
				}
			}
			//district list	
			$conditions = array();
			if ($office_ids) $conditions['Territory.office_id'] = $office_ids;
			if ($office_id) $conditions['Territory.office_id'] = $office_id;
			if ($territory_id) $conditions['Territory.id'] = $territory_id;


			if ($conditions) {
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


			//thana list
			if ($district_id) {
				$t_conditions = array();
				$t_conditions['Thana.district_id'] = $district_id;
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

			//outlet list
			if ($market_id) {
				$outlets = $this->Outlet->find('list', array(
					'conditions' => array('Outlet.market_id' => $market_id, 'Outlet.is_active' => 1),
					'order' => array('Outlet.name' => 'asc')
				));
			}




			//START DATA QUERY
			if ($report_type == 'visited') {
				$conditions = array();

				$conditions = array(
					'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					//'Memo.gross_value >' => 0,
					'Memo.status !=' => 0,
				);
				if ($outlet_id) {
					$conditions['Memo.outlet_id'] = $outlet_id;
				} else {
					if ($office_ids) $conditions['Memo.office_id'] = $office_ids;
					if ($office_id) $conditions['Memo.office_id'] = $office_id;

					if ($type == 'so') {
						if ($so_id) $conditions['Memo.sales_person_id'] = $so_id;
					} else {
						if ($territory_id) $conditions['Memo.territory_id'] = $territory_id;
					}

					if ($district_id) $conditions['Thana.district_id'] = $district_id;
					if ($thana_id) $conditions['Memo.thana_id'] = $thana_id;
					if ($market_id) $conditions['Memo.market_id'] = $market_id;
					/*if($outlet_id)$conditions['Memo.outlet_id'] = $outlet_id;*/

					if ($outlet_category_id) $conditions['Outlet.category_id'] = $outlet_category_id;
				}



				/*pr($conditions);
				exit;*/

				//market wise total outlets
				$m_o_con = array(
					'Outlet.is_active' => 1,
				);
				if ($outlet_category_id) $m_o_con['Outlet.category_id'] = $outlet_category_id;
				$total_outlets_market_wise_results = $this->Outlet->find(
					'all',
					array(
						'conditions' => $m_o_con,
						'fields' => array('count(DISTINCT Outlet.id) as total_outlet', 'Outlet.market_id', 'Market.name'),
						'group' => array('Outlet.market_id', 'Market.name')
					)
				);
				$total_outlets_market_wise = array();
				foreach ($total_outlets_market_wise_results as $market_wise_result) {
					$total_outlets_market_wise[$market_wise_result['Market']['name']] = $market_wise_result[0]['total_outlet'];
				}
				//pr($total_outlets_market_wise);
				//exit;

				//thana wise total outlets
				$total_outlets_thana_wise_results = $this->Outlet->find('all', array(
					'joins' => array(
						array(
							'alias' => 'Market',
							'table' => 'markets',
							'type' => 'INNER',
							'conditions' => 'Outlet.market_id = Market.id'
						),
						array(
							'alias' => 'Thana',
							'table' => 'thanas',
							'type' => 'INNER',
							'conditions' => 'Market.thana_id = Thana.id'
						)
					),
					'conditions' => $m_o_con,
					'fields' => array('count(DISTINCT Outlet.id) as total_outlet', 'Market.thana_id', 'Thana.name'),
					'group' => array('Market.thana_id', 'Thana.name'),
					'recursive' => -1
				));
				$total_outlets_thana_wise = array();
				foreach ($total_outlets_thana_wise_results as $thana_wise_result) {
					$total_outlets_thana_wise[$thana_wise_result['Thana']['name']] = $thana_wise_result[0]['total_outlet'];
				}
				//pr($total_outlets_thana_wise);
				//exit;

				$q_results = $this->Memo->find('all', array(
					'conditions' => $conditions,
					'joins' => array(
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
						array(
							'alias' => 'District',
							'table' => 'districts',
							'type' => 'INNER',
							'conditions' => 'Thana.district_id = District.id'
						)
					),

					//'fields' => array('count(Memo.outlet_id) as total_outlet', 'sum(Memo.gross_value) as memo_total',  'Memo.outlet_id', 'Outlet.name', 'SalesPeople.name', 'Market.name', 'Thana.name', 'District.name'),

					//'group' => array('Memo.outlet_id', 'Outlet.name', 'SalesPeople.name', 'Market.name', 'Thana.name', 'District.name'),

					'fields' => array('count(Memo.outlet_id) as total_outlet', 'sum(Memo.gross_value) as memo_total',  'Memo.outlet_id', 'Memo.memo_date', 'Outlet.name', 'Outlet.category_id', 'SalesPeople.name', 'Market.name', 'Thana.name', 'District.name'),

					'group' => array('Memo.memo_date', 'Memo.outlet_id', 'Outlet.name', 'Outlet.category_id', 'SalesPeople.name', 'Market.name', 'Thana.name', 'District.name'),

					'order' => array('Thana.name asc', 'Market.name asc', 'Outlet.name'),

					'recursive' => -1
				));

				//pr($q_results);
				//exit;

				$results = array();
				foreach ($q_results as $q_result) {
					$results[$q_result['District']['name']][$q_result['Thana']['name']][$q_result['Market']['name']][$q_result['Outlet']['name']][]
						= array(
							'total_outlet'			=> $q_result[0]['total_outlet'],
							'memo_total'			=> $q_result[0]['memo_total'],
							'memo_date'				=> $q_result['Memo']['memo_date'],
							'outlet_id'				=> $q_result['Memo']['outlet_id'],
							'outlet_category_id'	=> $q_result['Outlet']['category_id'],
							'so_name'				=> $q_result['SalesPeople']['name'],
						);
				}

				$this->set(compact('results'));

				/*pr($results);
				exit;*/

				$output = '';
				foreach ($results as $district_name => $result) {
					$output .= '<tr>';
					$output .= '<td style="text-align:left;" colspan="6"><b>District :- ' . $district_name . '</b></td></tr>';

					foreach ($result as $thana_name => $market_data) {
						$thana_total = 0;
						$output .= '<tr>
					  <td style="text-align:left;" colspan="6"><b>Thana :- ' . $thana_name . '</b></td>
					</tr>';

						$thana_total_visited_outlets = 0;

						foreach ($market_data as $market_name => $outlet_data) {

							$market_total = 0;
							$output .= '<tr>
							  <td style="text-align:left;" colspan="6"><b>Market :- ' . $market_name . '</b></td>
							</tr>
							<tr>
								</tr>';


							foreach ($outlet_data as $outlet_name => $memo_data) {
								$market_total_visited_outlets = 0;
								$memo_date = '';
								$memo_total = 0;
								$to = count($memo_data);
								$i = 1;
								foreach ($memo_data as $m_result) {
									$so_name =  $m_result['so_name'];
									if ($i == $to) {
										$memo_date .= date('d-m-Y', strtotime($m_result['memo_date']));
									} else {
										$memo_date .= date('d-m-Y', strtotime($m_result['memo_date'])) . ', ';
									}

									$outlet_category_id = $m_result['outlet_category_id'];

									$memo_total += $m_result['memo_total'];

									$i++;
								}

								$output .= '<tr>
									  <td style="text-align:left;">' . $outlet_name . '</td>
									  <td style="text-align:left;">' . $outlet_categories[$outlet_category_id] . '</td>
									  <td>' . count($memo_data) . '</td>
									  <td>' . $memo_date . '</td>
									  <td style="text-align:right;">' . sprintf("%01.2f", $memo_total) . '</td>
									  <td style="text-align:left;">' . $so_name . '</td>
									</tr>';

								$market_total += $memo_total;
								$market_total_visited_outlets += count($outlet_data);
							}


							$output .= '<tr>
							  <td style="text-align:right;" colspan="4"><span style="float:left;"><b>Market Wise Summary :- Total Outlet: ' . $total_outlets_market_wise[$market_name] . ', Visited Outlets: ' . $market_total_visited_outlets . '</b></span> <span style="float:right;"><b>Memo Total:</b></span></td>
							  <td style="text-align:right;"><b>' . sprintf("%01.2f", $market_total) . '</b></td>
							  <td colspan="3"></td>
							</tr>';
							$thana_total += $market_total;

							$thana_total_visited_outlets += $market_total_visited_outlets;
						}

						$output .= '<tr>
					  <td style="text-align:right;" colspan="4">
					  <span style="float:left;"><b>Thana Wise Summary :- Total Outlet: ' . $total_outlets_thana_wise[$thana_name] . ', Visited Outlets: ' . $thana_total_visited_outlets . '</span> <span style="float:right;"><b>Memo Total:</b></span>
					  </td>
					  <td style="text-align:right;"><b>' . sprintf("%01.2f", $thana_total) . '</b></td>
					  <td colspan="3"></td>
					</tr>';
					}
				}

				$this->set(compact('output'));

				//echo ($output);
				//exit;
			}


			if ($report_type == 'non_visited') {
				$conditions = array();

				$conditions = array(
					'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					//'Memo.gross_value >' => 0,
					'Memo.status !=' => 0,
				);

				if ($outlet_id) {
					$conditions['Memo.outlet_id'] = $outlet_id;
				} else {
					if ($office_ids) $conditions['Memo.office_id'] = $office_ids;
					if ($office_id) $conditions['Memo.office_id'] = $office_id;

					if ($type == 'so') {
						if ($so_id) $conditions['Memo.sales_person_id'] = $so_id;
					} else {
						if ($territory_id) $conditions['Memo.territory_id'] = $territory_id;
					}

					if ($district_id) $conditions['Thana.district_id'] = $district_id;
					if ($thana_id) $conditions['Memo.thana_id'] = $thana_id;
					if ($market_id) $conditions['Memo.market_id'] = $market_id;

					if ($outlet_category_id) $conditions['Outlet.category_id'] = $outlet_category_id;
				}


				/*pr($conditions);
				exit;*/

				//market wise total outlets
				//$outlet_category_id
				$o_con = array(
					'Outlet.is_active' => 1,
				);
				if ($outlet_category_id) $o_con['Outlet.category_id'] = $outlet_category_id;
				$total_outlets_market_wise_results = $this->Outlet->find(
					'all',
					array(
						'conditions' => $o_con,
						'fields' => array('count(DISTINCT Outlet.id) as total_outlet', 'Outlet.market_id', 'Market.name'),
						'group' => array('Outlet.market_id', 'Market.name')
					)
				);
				$total_outlets_market_wise = array();
				foreach ($total_outlets_market_wise_results as $market_wise_result) {
					$total_outlets_market_wise[$market_wise_result['Market']['name']] = $market_wise_result[0]['total_outlet'];
				}


				$q_results = $this->Memo->find('all', array(
					'conditions' => $conditions,
					'joins' => array(
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
						array(
							'alias' => 'District',
							'table' => 'districts',
							'type' => 'INNER',
							'conditions' => 'Thana.district_id = District.id'
						)
					),
					'fields' => array('DISTINCT Memo.outlet_id'),
					'group' => array('Memo.outlet_id'),
					'recursive' => -1
				));
				$outlet_ids = array();
				foreach ($q_results as $q_result) {
					array_push($outlet_ids, $q_result['Memo']['outlet_id']);
				}
				/*pr($outlet_ids);
				exit;*/

				//outlet lists
				$con = array();
				if ($office_ids) $con['Territory.office_id'] = $office_ids;
				if ($office_id) $con['Territory.office_id'] = $office_id;
				//if($territory_id)$con['Territory.id'] = $territory_id;
				if ($type == 'so') {
					if ($so_id) $con['SalesPeople.id'] = $so_id;
				} else {
					if ($territory_id) $con['Territory.id'] = $territory_id;
				}
				if ($district_id) $con['Thana.district_id'] = $district_id;
				if ($thana_id) $con['Market.thana_id'] = $thana_id;
				if ($market_id) $con['Outlet.market_id'] = $market_id;

				if ($outlet_category_id) $con['Outlet.category_id'] = $outlet_category_id;
				if ($outlet_ids) $con['NOT'] = array("Outlet.id" => $outlet_ids);
				if ($outlet_id) $con['Outlet.id'] = $outlet_id;

				//pr($con);
				//exit;

				$o_results = $this->Outlet->find('all', array(
					'conditions' => $con,
					'joins' => array(
						array(
							'alias' => 'Market',
							'table' => 'markets',
							'type' => 'INNER',
							'conditions' => 'Outlet.market_id = Market.id'
						),
						array(
							'alias' => 'Thana',
							'table' => 'thanas',
							'type' => 'INNER',
							'conditions' => 'Market.thana_id = Thana.id'
						),
						array(
							'alias' => 'District',
							'table' => 'districts',
							'type' => 'INNER',
							'conditions' => 'Thana.district_id = District.id'
						),
						array(
							'alias' => 'ThanaTerritory',
							'table' => 'thana_territories',
							'type' => 'INNER',
							'conditions' => 'ThanaTerritory.thana_id = Market.thana_id'
						),
						array(
							'alias' => 'Territory',
							'table' => 'territories',
							'type' => 'INNER',
							'conditions' => 'ThanaTerritory.territory_id = Territory.id'
						),
						array(
							'alias' => 'SalesPeople',
							'table' => 'sales_people',
							'type' => 'INNER',
							'conditions' => 'Territory.id = SalesPeople.territory_id'
						)
					),
					'fields' => array('Outlet.id', 'Outlet.name', 'Outlet.category_id',  'Market.name', 'Thana.name', 'District.name'),
					'group' => array('Outlet.id', 'Outlet.name', 'Outlet.category_id',  'Market.name', 'Thana.name', 'District.name'),

					'order' => array('Market.name asc', 'Thana.name asc', 'Outlet.name'),
					//'limit' => 3000,
					'recursive' => -1

				));


				$results = array();
				foreach ($o_results as $o_result) {
					$results[$o_result['Market']['name']][$o_result['Outlet']['category_id']][] = array(
						'outlet_name'			=> $o_result['Outlet']['name'],
						'outlet_category_id'	=> $o_result['Outlet']['category_id'],
						'market_name'			=> $o_result['Market']['name'],
						'thana_name'			=> $o_result['Thana']['name'],
						'district_name'			=> $o_result['District']['name']
					);
				}

				/*pr($results);
				exit;*/

				$this->set(compact('results'));

				$output = '';

				foreach ($results as $market_name => $outlet_category_datas) {
					$total_market_outlets = 0;
					foreach ($outlet_category_datas as $outlet_category_id => $outlet_datas) {
						$total_market_outlets += count($outlet_datas);
					}

					$output .= '<tr>
									<td colspan="3" style="font-weight:bold; text-align:left;">'
						. $market_name . ' - ' . $outlet_datas[0]['thana_name'] . ' - ' . $outlet_datas[0]['district_name'] . ' (Non Visited Outlets : ' . $total_market_outlets . ', Total Outlets: ' . $total_outlets_market_wise[$market_name] . ')<br>
									</td>
								</tr>';

					foreach ($outlet_category_datas as $outlet_category_id => $outlet_datas) {
						$i = 1;
						$total_outlets = count($outlet_datas);
						$output .= '<tr>
									<td colspan="3" style="font-weight:bold; text-align:left;">'
							. $outlet_categories[$outlet_category_id] . ' Outlet List - ' . $total_outlets . '
									</td>
								</tr>';
						foreach ($outlet_datas as $outlet_data) {


							$output .= '<tr>
							  <td></td>
							  <td style="text-align:left;">' . @$outlet_data['outlet_name'] . '</td>
							  <td style="text-align:left;">' . $outlet_categories[$outlet_category_id] . '</td>
							  </tr>';
							$i++;
						}
					}
				}
				$this->set(compact('output'));
				//echo ($output);
				//exit;
			}


			if ($report_type == 'detail') {
				$conditions = array();

				$conditions = array(
					'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					'Memo.gross_value >' => 0,
					'Memo.status !=' => 0,
					'MemoDetail.price >' => 0,
				);
				if ($outlet_id) {
					$conditions['Memo.outlet_id'] = $outlet_id;
				} else {
					if ($office_ids) $conditions['Memo.office_id'] = $office_ids;
					if ($office_id) $conditions['Memo.office_id'] = $office_id;

					if ($type == 'so') {
						if ($so_id) $conditions['Memo.sales_person_id'] = $so_id;
					} else {
						if ($territory_id) $conditions['Memo.territory_id'] = $territory_id;
					}

					if ($district_id) $conditions['Thana.district_id'] = $district_id;
					if ($thana_id) $conditions['Memo.thana_id'] = $thana_id;
					if ($market_id) $conditions['Memo.market_id'] = $market_id;


					if ($outlet_category_id) $conditions['Outlet.category_id'] = $outlet_category_id;
				}


				/*pr($conditions);
				exit;*/

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
						array(
							'alias' => 'District',
							'table' => 'districts',
							'type' => 'INNER',
							'conditions' => 'Thana.district_id = District.id'
						)
					),

					'fields' => array('Memo.id', 'Memo.memo_no', 'SalesPeople.name', 'Memo.memo_date', 'Memo.gross_value', 'Outlet.category_id', 'MemoDetail.product_id', 'MemoDetail.sales_qty', 'MemoDetail.price', 'Product.order', 'Product.name', 'Outlet.name', 'Market.name', 'Thana.name', 'District.name'),
					'group' => array('Memo.id', 'Memo.memo_no', 'SalesPeople.name', 'Memo.memo_date', 'Memo.gross_value', 'Outlet.category_id', 'MemoDetail.product_id', 'MemoDetail.sales_qty', 'MemoDetail.price', 'Product.order', 'Product.name', 'Outlet.name', 'Market.name', 'Thana.name', 'District.name'),

					'order' => array('Market.name asc', 'Outlet.name asc', 'Memo.memo_no asc',  'Product.order asc'),

					'recursive' => -1
				));

				/*pr($q_results);
				exit;*/

				$results = array();
				foreach ($q_results as $result) {

					$sales_qty = ($unit_type == 1) ? $result['MemoDetail']['sales_qty'] : $this->unit_convert($result['MemoDetail']['product_id'], $product_measurement[$result['MemoDetail']['product_id']], $result['MemoDetail']['sales_qty']);

					$results[$result['Market']['name'] . '-' . $result['Thana']['name'] . '-' . $result['District']['name']][$result['Outlet']['name']][$result['Memo']['memo_no']][$result['MemoDetail']['product_id']] = array(
						'memo_no' 				=> $result['Memo']['memo_no'],
						'memo_date' 			=> $result['Memo']['memo_date'],
						'gross_value' 			=> $result['Memo']['gross_value'],

						'product_id' 			=> $result['MemoDetail']['product_id'],
						'product_name' 			=> $result['Product']['name'],
						//'product_sales_qty' 	=> $result['MemoDetail']['sales_qty'],

						'product_sales_qty' 	=> sprintf("%01.2f", $sales_qty),

						'product_price' 		=> sprintf("%01.2f", $result['MemoDetail']['price'] * $result['MemoDetail']['sales_qty']),

						'outlet_name' 			=> $result['Outlet']['name'],
						//'outlet_category_id' 	=> $result['Outlet']['category_id'],

						'so_name' 				=> $result['SalesPeople']['name'],

						'market_name' 			=> $result['Market']['name'],
						'thana_name' 			=> $result['Thana']['name'],
						'district_name' 		=> $result['District']['name'],
					);
				}

				$this->set(compact('results'));


				//for output
				$output = '';

				$grand_total = 0;
				foreach ($results as $market_name => $outlet_datas) {

					$output .= '<tr>
					  <td style="text-align:left; font-size:15px;" colspan="7"><b>Market :- ' . $market_name . '</b></td>
					</tr>';


					$market_total = 0;
					foreach ($outlet_datas as $outlet_name => $memo_datas) {

						$outlet_total = 0;
						foreach ($memo_datas as $memo_no => $memo_products) {

							$memo_total = 0;
							$i = 1;
							foreach ($memo_products as $memo_product) {
								$memo_total += $memo_product['product_price'];

								$outlet_nam = $i == 1 ? $outlet_name : '';
								$memo_no = $i == 1 ? $memo_no : '';
								$memo_date = $i == 1 ? date('d-m-Y', strtotime($memo_product['memo_date'])) : '';

								$output .= '<tr>
							  <td style="text-align:left;">' . $outlet_nam . '</td>
							  <td style="mso-number-format:\@;">' . $memo_no . '</td>
							  <td>' . $memo_date . '</td>
							  <td style="text-align:left;">' . @$memo_product['product_name'] . '</td>
							  <td style="text-align:right;">' . @$memo_product['product_sales_qty'] . '</td>
							  <td style="text-align:right;">' . @$memo_product['product_price'] . '</td>
							  <td style="text-align:left;">' . @$memo_product['so_name'] . '</td>
							</tr>';

								$i++;
							}
							$outlet_total += $memo_total;

							$output .= '<tr>
							  <td style="text-align:right;" colspan="5"><b>Memo Total :</b></td>
							  <td style="text-align:right;"><b>' . sprintf("%01.2f", $memo_total) . '</b></td>
							  <td colspan="3"></td>
							</tr>';
						}

						$output .= '<tr style="background:#f7f7f7">
						  <td style="text-align:right;" colspan="5"><b>Outlet Wise Memo Total :</b></td>
						  <td style="text-align:right;"><b>' . sprintf("%01.2f", $outlet_total) . '</b></td>
						  <td colspan="3"></td>
						</tr>';

						$market_total += $outlet_total;
					}


					$output .= '<tr style="background:#ccc">
						  <td style="text-align:right;" colspan="5"><b>Market Wise Memo Total :</b></td>
						  <td style="text-align:right;"><b>' . sprintf("%01.2f", $market_total) . '</b></td>
						  <td colspan="3"></td>
						</tr>';

					$grand_total += $market_total;
				}


				$output .= '<tr style="background:#f7f7f7">
				  <td style="text-align:right;" colspan="5"><b>Grand Total :</b></td>
				  <td style="text-align:right;"><b>' . sprintf("%01.2f", $grand_total) . '</b></td>
				  <td colspan="3"></td>
				</tr>';

				$this->set(compact('output'));

				//echo ($output);
				//exit;
			}


			if ($report_type == 'summary') {

				//products
				$product_list = $this->Product->find('list', array(
					'conditions' => array(
						'NOT' => array('Product.product_category_id' => 32),
						'is_active' => 1,
						'Product.product_type_id' => 1
					),
					'order' =>  array('order' => 'asc')
				));
				$this->set(compact('product_list'));


				$conditions = array(
					'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					'Memo.gross_value >' => 0,
					'Memo.status !=' => 0,
					'MemoDetail.price >' => 0,
				);

				if ($outlet_id) {
					$conditions['Memo.outlet_id'] = $outlet_id;
				} else {
					if ($office_ids) $conditions['Memo.office_id'] = $office_ids;
					if ($office_id) $conditions['Memo.office_id'] = $office_id;

					if ($type == 'so') {
						if ($so_id) $conditions['Memo.sales_person_id'] = $so_id;
					} else {
						if ($territory_id) $conditions['Memo.territory_id'] = $territory_id;
					}

					if ($district_id) $conditions['Thana.district_id'] = $district_id;
					if ($thana_id) $conditions['Memo.thana_id'] = $thana_id;
					if ($market_id) $conditions['Memo.market_id'] = $market_id;


					if ($outlet_category_id) $conditions['Outlet.category_id'] = $outlet_category_id;
				}



				/*pr($conditions);
				exit;*/

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
						array(
							'alias' => 'District',
							'table' => 'districts',
							'type' => 'INNER',
							'conditions' => 'Thana.district_id = District.id'
						)
					),

					/*'fields' => array('SUM(MemoDetail.sales_qty) as sales_qty', 'SUM(MemoDetail.sales_qty*MemoDetail.price) as price', 'MemoDetail.product_id',  'Product.name', 'Outlet.name', 'Market.name', 'Thana.name', 'District.name'),
					
					'group' => array('MemoDetail.product_id',  'Product.name', 'Outlet.name', 'Market.name', 'Thana.name', 'District.name'),*/

					'fields' => array('SUM(MemoDetail.sales_qty) as sales_qty', 'SUM(MemoDetail.sales_qty*MemoDetail.price) as price', 'MemoDetail.product_id',  'Product.name', 'Outlet.id', 'Outlet.name', 'Market.id', 'Market.name', 'Thana.id', 'Thana.name', 'District.id', 'District.name'),

					'group' => array('MemoDetail.product_id',  'Product.name', 'Outlet.id', 'Outlet.name', 'Market.id', 'Market.name', 'Thana.id', 'Thana.name', 'District.id', 'District.name'),

					'order' => array('Market.name asc', 'Outlet.name asc'),

					'recursive' => -1
				));

				//pr($q_results);
				//exit;

				$results = array();
				foreach ($q_results as $result) {
					$sales_qty = ($unit_type == 1) ? $result[0]['sales_qty'] : $this->unit_convert($result['MemoDetail']['product_id'], $product_measurement[$result['MemoDetail']['product_id']], $result[0]['sales_qty']);

					$results[$result['Market']['name'] . '-' . $result['Thana']['name'] . '-' . $result['District']['name']][$result['Outlet']['id']][$result['Outlet']['name']][$result['MemoDetail']['product_id']] = array(

						'product_id' 			=> $result['MemoDetail']['product_id'],
						//'product_name' 		=> $result['Product']['name'],
						//'sales_qty' 			=> $result[0]['sales_qty'],
						'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
						'price' 				=> $result[0]['price'],
						//'outlet_name' 		=> $result['Outlet']['name'],
						//'market_name' 		=> $result['Market']['name'],
						//'thana_name' 			=> $result['Thana']['name'],
						//'district_name' 		=> $result['District']['name'],
					);
				}
				$this->set(compact('results'));

				//pr($results);
				//exit;

				$output = '';

				$grand_total = array();
				foreach ($results as $market_name => $outlet_id) {

					$output .= '<tr>
				  <td style="text-align:left; font-size:12px;" colspan="' . count($product_list) . '"><b>Market :- ' . $market_name . '</b></td>
				</tr>';


					$sub_total = array();
					foreach ($outlet_id as $outlet_datas) {
						foreach ($outlet_datas as $outlet_name => $pro_datas) {

							$output .= '<tr>
						  <td style="text-align:left;">' . $outlet_name . '</td>';

							foreach ($product_list as $product_id => $pro_name) {

								$output .= '<td style="text-align:left;">' . @$pro_datas[$product_id]['sales_qty'] . '</td>';

								@$sub_total[$product_id] += $pro_datas[$product_id]['sales_qty'] ? $pro_datas[$product_id]['sales_qty'] : 0;
							}

							$output .= '</tr>';
						}
					}

					$output .= '<tr style="font-weight:bold; background:#f2f2f2;">
				  <td>Sub Total</td>';
					foreach ($product_list as $product_id => $pro_name) {
						$output .= '<td>' . sprintf("%01.2f", $sub_total[$product_id]) . '</td>';

						@$grand_total[$product_id] += $sub_total[$product_id];
					}

					$output .= '</tr>';
				}


				$output .= '<tr style="font-weight:bold; background:#ccc;">
				  <td>Grand Total</td>';
				foreach ($product_list as $product_id => $pro_name) {
					$output .= '<td>' . sprintf("%01.2f", $grand_total[$product_id]) . '</td>';
				}
				$output .= '</tr>';

				$this->set(compact('output'));

				//echo ($output);
				//exit;

			}
		}



		$this->set(compact('offices', 'territories', 'outlet_type', 'region_offices', 'districts', 'thanas', 'markets', 'outlets', 'office_id', 'request_data', 'so_list'));
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



		$this->loadModel('Territory');
		$territory_info = $this->Territory->find(
			'first',
			array(
				'conditions' => array('Territory.id' => $territory_id),
				'joins' => array(
					array(
						'table' => 'users',
						'alias' => 'User',
						'conditions' => 'SalesPerson.id=User.sales_person_id'
					)
				),
				'fields' => array('Office.id', 'User.user_group_id', 'User.id'),
				'recursive' => 0
			)
		);
		$user_group_id = $territory_info['User']['user_group_id'];
		$user_id = $territory_info['User']['id'];
		if ($user_group_id == 1008) {
			$this->loadModel('UserTerritoryList');
			$territory_list = $this->UserTerritoryList->find('list', array(
				'conditions' => array('UserTerritoryList.user_id' => $user_id),
				'fields' => array('UserTerritoryList.territory_id', 'UserTerritoryList.territory_id'),
				'recursive' => -1
			));
			if ($territory_list)
				$territory_id = array_keys($territory_list);
		}

		//pr($conditions);
		//exit;
		$conditions = array();

		if ($office_id) $conditions['Territory.office_id'] = $office_id;

		if ($territory_id) $conditions['Territory.id'] = $territory_id;

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


		$district_id = $this->request->data['district_id'];
		$territory_id = $this->request->data['territory_id'];

		$this->loadModel('Territory');
		$territory_info = $this->Territory->find(
			'first',
			array(
				'conditions' => array('Territory.id' => $territory_id),
				'joins' => array(
					array(
						'table' => 'users',
						'alias' => 'User',
						'conditions' => 'SalesPerson.id=User.sales_person_id'
					)
				),
				'fields' => array('Office.id', 'User.user_group_id', 'User.id'),
				'recursive' => 0
			)
		);
		$user_group_id = $territory_info['User']['user_group_id'];
		$user_id = $territory_info['User']['id'];
		if ($user_group_id == 1008) {
			$this->loadModel('UserTerritoryList');
			$territory_list = $this->UserTerritoryList->find('list', array(
				'conditions' => array('UserTerritoryList.user_id' => $user_id),
				'fields' => array('UserTerritoryList.territory_id', 'UserTerritoryList.territory_id'),
				'recursive' => -1
			));
			if ($territory_list)
				$territory_id = array_keys($territory_list);
		}
		$conditions = array();

		if ($district_id) {
			$ids = explode(',', $district_id);
			$conditions['Thana.district_id'] = $ids;
			if ($this->request->data['territory_id']) $conditions['ThanaTerritory.territory_id'] = $territory_id;

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
  <input type="hidden" name="data[OutletCharacteristicReports][thana_id]" value="" id="thana_id"/>';
				foreach ($thana_list as $key => $val) {
					$output .= '<div class="checkbox">
						<input type="checkbox" onClick="marketBoxList()" name="data[OutletCharacteristicReports][thana_id][]" value="' . $key . '" id="thana_id' . $key . '" />
						<label for="thana_id' . $key . '">' . $val . '</label>
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


	//get market list
	public function get_market_list()
	{
		$view = new View($this);

		$form = $view->loadHelper('Form');

		$thana_id = $this->request->data['thana_id'];
		$territory_id = $this->request->data['territory_id'];

		$this->loadModel('Territory');
		$territory_info = $this->Territory->find(
			'first',
			array(
				'conditions' => array('Territory.id' => $territory_id),
				'joins' => array(
					array(
						'table' => 'users',
						'alias' => 'User',
						'conditions' => 'SalesPerson.id=User.sales_person_id'
					)
				),
				'fields' => array('Office.id', 'User.user_group_id', 'User.id'),
				'recursive' => 0
			)
		);
		$user_group_id = $territory_info['User']['user_group_id'];
		$user_id = $territory_info['User']['id'];
		if ($user_group_id == 1008) {
			$this->loadModel('UserTerritoryList');
			$territory_list = $this->UserTerritoryList->find('list', array(
				'conditions' => array('UserTerritoryList.user_id' => $user_id),
				'fields' => array('UserTerritoryList.territory_id', 'UserTerritoryList.territory_id'),
				'recursive' => -1
			));
			if ($territory_list)
				$territory_id = array_keys($territory_list);
		}
		$conditions = array();

		if ($thana_id) {
			$ids = explode(',', $thana_id);
			$conditions['Market.thana_id'] = $ids;
			$conditions['Market.is_active'] = 1;
			if ($this->request->data['territory_id']) $conditions['Territory.id'] = $territory_id;


			//exit;

			$results = $this->Market->find('list', array(
				'conditions' => $conditions,
				'order' =>  array('Market.name' => 'asc'),
				'recursive' => 1
			));


			if ($results) {
				$output = '<div class="input select">
  <input type="hidden" name="data[OutletCharacteristicReports][market_id]" value="" id="market_id"/>';
				foreach ($results as $key => $val) {
					$output .= '<div class="checkbox">
						<input type="checkbox"  onClick="outletBoxList()" name="data[OutletCharacteristicReports][market_id][]" value="' . $key . '" id="market_id' . $key . '" />
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
  <input type="hidden" name="data[OutletCharacteristicReports][outlet_id]" value="" id="outlet_id"/>';
				foreach ($results as $key => $val) {
					$output .= '<div class="checkbox">
						<input type="checkbox" name="data[OutletCharacteristicReports][outlet_id][]" value="' . $key . '" id="outlet_id' . $key . '" />
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
