<?php
App::uses('AppController', 'Controller');
/**
 * OutletCharacteristicSettings Controller
 *
 * @property OutletCharacteristicReport $OutletCharacteristicReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class MarketCharacteristicReportsController extends AppController
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


		$this->set('page_title', "Market Characteristics Report");

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
			'visit_info' => 'Market Visit Information',
			'non_visited' => 'Non-Visited Market List',
			'visited' => 'Visited Market List',

		);
		$this->set(compact('report_types'));



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
				//'conditions' => array('Territory.office_id' => $office_id, 'User.user_group_id !=' => 1008),
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
					//'User.user_group_id' => 4,
					'User.user_group_id' => array(4, 1008),
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

			$date_from = date('Y-m-d', strtotime($request_data['MarketCharacteristicReports']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['MarketCharacteristicReports']['date_to']));
			$this->set(compact('date_from', 'date_to'));

			$type = $this->request->data['MarketCharacteristicReports']['type'];
			$this->set(compact('type'));

			$report_type = $this->request->data['MarketCharacteristicReports']['report_type'];
			$this->set(compact('report_type'));

			$region_office_id = isset($this->request->data['MarketCharacteristicReports']['region_office_id']) != '' ? $this->request->data['MarketCharacteristicReports']['region_office_id'] : $region_office_id;
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

			$office_id = isset($this->request->data['MarketCharacteristicReports']['office_id']) != '' ? $this->request->data['MarketCharacteristicReports']['office_id'] : $office_id;
			$this->set(compact('office_id'));
			$territory_id = isset($this->request->data['MarketCharacteristicReports']['territory_id']) != '' ? $this->request->data['MarketCharacteristicReports']['territory_id'] : 0;
			$this->set(compact('territory_id'));

			$so_id = isset($this->request->data['MarketCharacteristicReports']['so_id']) != '' ? $this->request->data['MarketCharacteristicReports']['so_id'] : 0;
			$this->set(compact('so_id'));


			//for report data
			$district_id = isset($this->request->data['MarketCharacteristicReports']['district_id']) != '' ? $this->request->data['MarketCharacteristicReports']['district_id'] : 0;

			$thana_id = isset($this->request->data['MarketCharacteristicReports']['thana_id']) != '' ? $this->request->data['MarketCharacteristicReports']['thana_id'] : 0;

			$market_id = isset($this->request->data['MarketCharacteristicReports']['market_id']) != '' ? $this->request->data['MarketCharacteristicReports']['market_id'] : 0;




			//territory list
			$territory_list = $this->Territory->find('all', array(
				//'conditions' => array('Territory.office_id' => $office_id, 'User.user_group_id !=' => 1008),
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
					//'User.user_group_id' => 4,
					'User.user_group_id' => array(4, 1008),
				),
				'recursive' => 0
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
				if ($thana_id) $m_conditions['Market.thana_id'] = $thana_id;
				if ($territory_id) $m_conditions['Territory.id'] = $territory_id;
				$m_conditions['Market.is_active'] = 1;

				$markets = $this->Market->find('list', array(
					'conditions' => $m_conditions,
					'order' => array('Market.name' => 'asc'),
					'recursive' => 1
				));
			}



			//thana wise total markets
			$t_m_con = array();
			$t_m_con['Market.is_active'] = 1;
			$total_markets_thana_wise_results = $this->Market->find('all', array(
				'joins' => array(
					array(
						'alias' => 'Thana',
						'table' => 'thanas',
						'type' => 'INNER',
						'conditions' => 'Market.thana_id = Thana.id'
					)
				),
				'conditions' => $t_m_con,
				'fields' => array('count(DISTINCT Market.id) as total_market', 'Thana.name'),
				'group' => array('Market.thana_id', 'Thana.name'),
				'recursive' => -1
			));

			$total_markets_thana_wise = array();
			foreach ($total_markets_thana_wise_results as $thana_wise_result) {
				$total_markets_thana_wise[($thana_wise_result['Thana']['name'])] = $thana_wise_result[0]['total_market'];
			}
			$this->set(compact('total_markets_thana_wise'));


			//market wise total outlets
			$m_o_con = array('Outlet.is_active' => 1);
			$total_outlets_market_wise_results = $this->Outlet->find(
				'all',
				array(
					'conditions' => $m_o_con,
					'fields' => array('count(DISTINCT Outlet.id) as total_outlet', 'Market.name'),
					'group' => array('Outlet.market_id', 'Market.name')
				)
			);
			$total_outlets_market_wise = array();
			foreach ($total_outlets_market_wise_results as $market_wise_result) {
				$total_outlets_market_wise[($market_wise_result['Market']['name'])] = $market_wise_result[0]['total_outlet'];
			}
			$this->set(compact('total_outlets_market_wise'));
			//pr($total_outlets_market_wise);
			//exit;


			//START DATA QUERY
			if ($report_type == 'visited') {

				/*//thana wise total markets
				$total_markets_thana_wise_results = $this->Market->find('all', array(
				'joins' => array(
						array(
							'alias' => 'Thana',
							'table' => 'thanas',
							'type' => 'INNER',
							'conditions' => 'Market.thana_id = Thana.id'
						)
					),
				'fields' => array('count(DISTINCT Market.id) as total_market', 'Thana.name'),
				'group' => array('Thana.name'),
				'recursive' => -1
				));
				
				$total_markets_thana_wise = array();
				foreach($total_markets_thana_wise_results as $thana_wise_result){
					$total_markets_thana_wise[$thana_wise_result['Thana']['name']]=$thana_wise_result[0]['total_market'];
				}
				//pr($total_markets_thana_wise);
				//exit;
				$this->set(compact('total_markets_thana_wise'));
				
				
				//market wise total outlets
				$total_outlets_market_wise_results = $this->Outlet->find('all', array(
				'fields' => array('count(DISTINCT Outlet.id) as total_outlet', 'Market.name'),
				'group' => array('Market.name')
				));
				$total_outlets_market_wise = array();
				foreach($total_outlets_market_wise_results as $market_wise_result){
					$total_outlets_market_wise[$market_wise_result['Market']['name']]=$market_wise_result[0]['total_outlet'];
				}
				$this->set(compact('total_outlets_market_wise'));*/


				$conditions = array();

				$conditions = array(
					'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					//'Memo.gross_value >' => 0,
					'Memo.status !=' => 0,
				);

				if ($office_ids) $conditions['Memo.office_id'] = $office_ids;
				if ($office_id) $conditions['Memo.office_id'] = $office_id;

				if ($type == 'so') {
					if ($so_id) $conditions['Memo.sales_person_id'] = $so_id;
				} else {
					if ($territory_id) $conditions['Memo.territory_id'] = $territory_id;
				}

				if ($district_id) $conditions['Thana.district_id'] = $district_id;
				if ($thana_id) $conditions['Market.thana_id'] = $thana_id;
				if ($market_id) $conditions['Memo.market_id'] = $market_id;


				/*pr($conditions);
				exit;*/






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
							'alias' => 'Market',
							'table' => 'markets',
							'type' => 'INNER',
							'conditions' => 'Memo.market_id = Market.id'
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
						)
					),

					//'fields' => array('count(Memo.outlet_id) as total_outlet', 'sum(Memo.gross_value) as memo_total',  'Memo.outlet_id', 'Outlet.name', 'SalesPeople.name', 'Market.name', 'Thana.name', 'District.name'),

					//'group' => array('Memo.outlet_id', 'Outlet.name', 'SalesPeople.name', 'Market.name', 'Thana.name', 'District.name'),

					'fields' => array('count(Memo.outlet_id) as total_outlet', 'sum(Memo.gross_value) as memo_total',  'Memo.outlet_id', 'Memo.memo_date', 'SalesPeople.name', 'Market.name', 'Thana.name', 'District.name'),

					'group' => array('Memo.memo_date', 'Memo.outlet_id', 'SalesPeople.name', 'Market.name', 'Thana.name', 'District.name'),

					'order' => array('Thana.name asc', 'Market.name asc', 'Memo.memo_date asc'),

					'recursive' => -1
				));

				//pr($q_results);

				$results = array();
				foreach ($q_results as $q_result) {
					$results[$q_result['District']['name']][$q_result['Thana']['name']][$q_result['Market']['name']][$q_result['Memo']['outlet_id']][]
						= array(
							'memo_total'			=> $q_result[0]['memo_total'],
							'memo_date'				=> $q_result['Memo']['memo_date'],
							'outlet_id'				=> $q_result['Memo']['outlet_id'],
							'so_name'				=> $q_result['SalesPeople']['name'],
						);
				}

				$this->set(compact('results'));

				//pr($results);
				//exit;


			}

			if ($report_type == 'non_visited') {
				/*//thana wise total markets
				$total_markets_thana_wise_results = $this->Market->find('all', array(
				'joins' => array(
						array(
							'alias' => 'Thana',
							'table' => 'thanas',
							'type' => 'INNER',
							'conditions' => 'Market.thana_id = Thana.id'
						)
					),
				'fields' => array('count(DISTINCT Market.id) as total_market', 'Thana.name'),
				'group' => array('Thana.name'),
				'recursive' => -1
				));
				
				$total_markets_thana_wise = array();
				foreach($total_markets_thana_wise_results as $thana_wise_result){
					$total_markets_thana_wise[$thana_wise_result['Thana']['name']]=$thana_wise_result[0]['total_market'];
				}
				//pr($total_markets_thana_wise);
				//exit;
				$this->set(compact('total_markets_thana_wise'));
				
				
				//market wise total outlets
				$total_outlets_market_wise_results = $this->Outlet->find('all', array(
				'fields' => array('count(DISTINCT Outlet.id) as total_outlet', 'Market.name'),
				'group' => array('Market.name')
				));
				$total_outlets_market_wise = array();
				foreach($total_outlets_market_wise_results as $market_wise_result){
					$total_outlets_market_wise[$market_wise_result['Market']['name']]=$market_wise_result[0]['total_outlet'];
				}
				$this->set(compact('total_outlets_market_wise'));*/


				$conditions = array();

				$conditions = array(
					'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					//'Memo.gross_value >' => 0,
					'Memo.status !=' => 0,
				);

				if ($office_ids) $conditions['Memo.office_id'] = $office_ids;
				if ($office_id) $conditions['Memo.office_id'] = $office_id;

				if ($type == 'so') {
					if ($so_id) $conditions['Memo.sales_person_id'] = $so_id;
				} else {
					if ($territory_id) $conditions['Memo.territory_id'] = $territory_id;
				}

				if ($district_id) $conditions['Thana.district_id'] = $district_id;
				if ($thana_id) $conditions['Market.thana_id'] = $thana_id;
				if ($market_id) $conditions['Memo.market_id'] = $market_id;


				/*pr($conditions);
				exit;*/

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
							'alias' => 'Market',
							'table' => 'markets',
							'type' => 'INNER',
							'conditions' => 'Memo.market_id = Market.id'
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
						)
					),


					'fields' => array('DISTINCT Memo.market_id'),

					'group' => array('Memo.market_id'),

					'recursive' => -1
				));

				$market_ids = array();
				foreach ($q_results as $q_result) {
					array_push($market_ids, $q_result['Memo']['market_id']);
				}

				//pr($market_ids);
				//exit;


				//outlet lists
				$con = array();
				if ($office_ids) $con['Territory.office_id'] = $office_ids;
				if ($office_id) $con['Territory.office_id'] = $office_id;
				if ($territory_id) $con['Territory.id'] = $territory_id;
				if ($district_id) $con['Thana.district_id'] = $district_id;
				if ($thana_id) $con['Market.thana_id'] = $thana_id;
				if ($market_id) $con['Market.id'] = $market_id;

				if ($market_ids) $con['NOT'] = array("Market.id" => $market_ids);

				//pr($con);
				//exit;

				$m_results = $this->Market->find('all', array(
					'conditions' => $con,
					'joins' => array(
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
						)
					),

					'fields' => array('Market.name', 'Thana.name', 'District.name'),
					'group' => array('Market.name', 'Thana.name', 'District.name'),
					'order' => array('District.name', 'Thana.name asc', 'Market.name asc'),
					//'limit' => 3000,
					'recursive' => -1

				));

				//pr($m_results);
				//exit;

				$results = array();
				foreach ($m_results as $q_result) {
					$results[$q_result['District']['name']][$q_result['Thana']['name']][$q_result['Market']['name']][]
						= array();
				}

				$this->set(compact('results'));

				//pr($results);
				//exit;


			}


			if ($report_type == 'visit_info') {




				$conditions = array();

				$conditions = array(
					'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					//'Memo.gross_value >' => 0,
					'Memo.status !=' => 0,
				);

				if ($office_ids) $conditions['Memo.office_id'] = $office_ids;
				if ($office_id) $conditions['Memo.office_id'] = $office_id;

				if ($type == 'so') {
					if ($so_id) $conditions['Memo.sales_person_id'] = $so_id;
				} else {
					if ($territory_id) $conditions['Memo.territory_id'] = $territory_id;
				}

				if ($district_id) $conditions['Thana.district_id'] = $district_id;
				if ($thana_id) $conditions['Market.thana_id'] = $thana_id;
				if ($market_id) $conditions['Memo.market_id'] = $market_id;


				/*pr($conditions);
				exit;*/





				//For Visited 
				$q_results1 = $this->Memo->find('all', array(
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
							'alias' => 'Market',
							'table' => 'markets',
							'type' => 'INNER',
							'conditions' => 'Memo.market_id = Market.id'
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
						)
					),

					'fields' => array('count(Memo.outlet_id) as total_outlet', 'sum(Memo.gross_value) as memo_total',  'Memo.outlet_id', 'Memo.market_id', 'Memo.memo_date', 'SalesPeople.name',  'Market.name', 'Thana.name', 'District.name'),

					'group' => array('Memo.memo_date', 'Memo.outlet_id', 'Memo.market_id', 'SalesPeople.name', 'Market.name', 'Thana.name', 'District.name'),

					'order' => array('Thana.name asc', 'Market.name asc', 'Memo.memo_date asc'),

					'recursive' => -1
				));

				/*pr($q_results1);
				exit;*/

				$results1 = array();
				foreach ($q_results1 as $q_result) {
					$results1[$q_result['District']['name']][$q_result['Thana']['name']][$q_result['Market']['name']][$q_result['Memo']['outlet_id']][]
						= array(
							'memo_total'			=> $q_result[0]['memo_total'],
							'memo_date'				=> $q_result['Memo']['memo_date'],
							'outlet_id'				=> $q_result['Memo']['outlet_id'],
							'so_name'				=> $q_result['SalesPeople']['name'],
						);
				}

				$this->set(compact('results1'));

				/*$market_ids = array();
				foreach($q_results1 as $q_result)
				{
					array_push($market_ids, $q_result['Memo']['market_id']);
				}*/


				//For Non-Visited 
				$con = array();
				if ($office_ids) $con['Territory.office_id'] = $office_ids;
				if ($office_id) $con['Territory.office_id'] = $office_id;
				if ($territory_id) $con['Territory.id'] = $territory_id;
				if ($district_id) $con['Thana.district_id'] = $district_id;
				if ($thana_id) $con['Market.thana_id'] = $thana_id;
				if ($market_id) $con['Market.id'] = $market_id;

				//if($market_ids)$con['NOT'] = array("Market.id" => $market_ids);

				$m_results = $this->Market->find('all', array(
					'conditions' => $con,
					'joins' => array(
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
						)
					),

					'fields' => array('Market.name', 'Thana.name', 'District.name'),
					'group' => array('Market.name', 'Thana.name', 'District.name'),
					'order' => array('District.name', 'Thana.name asc', 'Market.name asc'),
					//'limit' => 3000,
					'recursive' => -1

				));



				$results = array();
				foreach ($m_results as $q_result) {
					$results[$q_result['District']['name']][$q_result['Thana']['name']][$q_result['Market']['name']][]
						= array();
				}



				$this->set(compact('results'));
			}
		}




		$this->set(compact('offices', 'territories', 'outlet_type', 'region_offices', 'districts', 'thanas', 'markets', 'outlets', 'office_id', 'request_data', 'so_list'));
	}



	//Office List
	public function get_office_list()
	{
		$rs = array(array('id' => '', 'name' => '---- All -----'));

		$parent_office_id = $this->request->data['region_office_id'];

		$office_conditions = array('NOT' => array("id" => array(30, 31, 37)), 'Office.office_type_id' => 2);
		//$office_conditions = array('Office.parent_office_id' => $parent_office_id, 'Office.office_type_id'=>2);

		if ($parent_office_id) $office_conditions['Office.parent_office_id'] = $parent_office_id;

		$offices = $this->Office->find(
			'all',
			array(
				'fields' => array('id', 'office_name'),
				'conditions' => $office_conditions,
				'order' => array('office_name' => 'asc'),
				'recursive' => -1
			)
		);


		$data_array = array();
		foreach ($offices as $office) {
			$data_array[] = array(
				'id' => $office['Office']['id'],
				'name' => $office['Office']['office_name'],
			);
		}

		//$data_array = Set::extract($offices, '{n}.Office');

		if (!empty($offices)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}

		$this->autoRender = false;
	}


	//Sales Officers (SO) List
	public function get_office_so_list()
	{
		$view = new View($this);

		$form = $view->loadHelper('Form');

		$office_id = $this->request->data['office_id'];

		$date_from = date('Y-m-d', strtotime($this->request->data['date_from']));
		$date_to = date('Y-m-d', strtotime($this->request->data['date_to']));

		//$conditions = array('SalesPerson.territory_id >' => 0, 'User.user_group_id' => 4, 'User.active' => 1);
		if ($office_id) {
			$conditions = array('User.user_group_id' => array(4, 1008), 'User.active' => 1);
			$conditions['SalesPerson.office_id'] = $office_id;

			$so_list = array();

			/*$so_list = $this->SalesPerson->find('list', array(
				'conditions' => $conditions,
				'order'=>  array('SalesPerson.name'=>'asc'),
				'recursive'=> 0
			));	*/
			$so_list_r = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
				'conditions' => array(
					'SalesPerson.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					//'User.user_group_id' => 4,
					'User.user_group_id' => array(4, 1008),
					'User.active' => 1
				),
				'recursive' => 0
			));
			foreach ($so_list_r as $list_r) {
				$so_list[$list_r['SalesPerson']['id']] = $list_r['SalesPerson']['name'] . ' (' . $list_r['Territory']['name'] . ')';
			}
			//pr($so_list);
			//exit;			
			//add old so from territory_assign_histories

			$conditions = array('Territory.office_id' => $office_id, 'TerritoryAssignHistory.assign_type' => 2);
			// $conditions['TerritoryAssignHistory.date BETWEEN ? and ? '] = array($date_from, $date_to);
			$conditions['TerritoryAssignHistory.date >= '] = $date_from;
			//pr($conditions);
			$old_so_list = $this->TerritoryAssignHistory->find('all', array(
				'conditions' => $conditions,
				'order' =>  array('Territory.name' => 'asc'),
				'recursive' => 0
			));
			//pr($old_so_list);
			//exit;
			if ($old_so_list) {
				foreach ($old_so_list as $old_so) {
					$so_list[$old_so['TerritoryAssignHistory']['so_id']] = $old_so['SalesPerson']['name'];
				}
			}
		}
		//pr($so_list);


		/*foreach($so_list_r as $key => $value)
		{
			$so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'].' ('.$value['Territory']['name'].')';
		}*/


		if ($so_list) {
			$output = '<option value="">---- All -----</option>';
			foreach ($so_list as $key => $so_name) {
				$output .= '<option value="' . $key . '">' . $so_name . '</option>';
			}

			echo $output;

			/*$form->create('MarketCharacteristicReports', array('role' => 'form', 'action'=>'index'))	;
			echo $form->input('so_id', array('label' => 'Sales Officers', 'id' => 'so_id', 'class' => 'form-control so_id', 'required'=>false, 'options' => $so_list, 'empty'=>'---- All ----'));
			$form->end();*/
		} else {
			echo '';
		}


		$this->autoRender = false;
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
			/*$form->create('MarketCharacteristicReports', array('role' => 'form', 'action'=>'index'))	;
			echo $form->input('district_id', array('id' => 'district_id', 'label'=>false, 'class' => 'checkbox district_box', 'onClick' => 'getThanaList()', 'fieldset' => false, 'multiple' => 'checkbox', 'options'=> $districts));
			$form->end();*/

			$output = '<div class="input select">
  <input type="hidden" name="data[MarketCharacteristicReports][district_id]" value="" id="district_id"/>';
			foreach ($districts as $key => $val) {
				$output .= '<div class="checkbox">
					<input type="checkbox" onClick="thanaBoxList()" name="data[MarketCharacteristicReports][district_id][]" value="' . $key . '" id="district_id' . $key . '" />
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
				/*$form->create('MarketCharacteristicReports', array('role' => 'form', 'action'=>'index'))	;
				echo $form->input('thana_id', array('id' => 'thana_id', 'label'=>false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required'=> true, 'options'=> $thana_list));
				$form->end();*/

				$output = '<div class="input select">
  <input type="hidden" name="data[MarketCharacteristicReports][thana_id]" value="" id="thana_id"/>';
				foreach ($thana_list as $key => $val) {
					$output .= '<div class="checkbox">
						<input type="checkbox" onClick="marketBoxList()" name="data[MarketCharacteristicReports][thana_id][]" value="' . $key . '" id="thana_id' . $key . '" />
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

			//pr($conditions);
			//exit;

			$results = $this->Market->find('list', array(
				'conditions' => $conditions,
				'order' =>  array('Market.name' => 'asc'),
				'recursive' => 1
			));


			if ($results) {
				$output = '<div class="input select">
  <input type="hidden" name="data[MarketCharacteristicReports][market_id]" value="" id="market_id"/>';
				foreach ($results as $key => $val) {
					$output .= '<div class="checkbox">
						<input type="checkbox" name="data[MarketCharacteristicReports][market_id][]" value="' . $key . '" id="market_id' . $key . '" />
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
}
