<?php
App::uses('AppController', 'Controller');
/**
 * OutletCharacteristicSettings Controller
 *
 * @property OutletCharacteristicReport $OutletCharacteristicReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistMarketCharacteristicReportsController extends AppController {
	
	

/**
 * Components
 *
 * @var array
 */
 
	public $uses = array('DistMemo', 'Office', 'Territory', 'Division', 'District', 'Thana', 'DistMarket', 'OutletCategory', 'DistOutlet', 'DistRouteMappingHistory', 'SalesPerson', 'Product');
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
				
		
		$this->set('page_title', "Distributor Market Characteristics Report");
		
		$territories = array();
		
		$markets = array();
		$outlets = array();
		$request_data = array();
		$report_type = array();
					
		//for region office list
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id'=>3), 
			'order' => array('office_name' => 'asc')
		));
		
		
		
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
		
		$office_conditions = array('Office.office_type_id'=>2);
		
		if ($office_parent_id == 0)
		{
			$office_id = 0;
		}
		elseif($office_parent_id == 14)
		{
			$region_office_id = $this->UserAuth->getOfficeId();
			$region_offices = $this->Office->find('list', array(
				'conditions' => array('Office.office_type_id'=>3, 'Office.id'=>$region_office_id), 
				'order' => array('office_name' => 'asc')
			));
			
			$office_conditions = array('Office.parent_office_id'=>$region_office_id);
			
			$office_id = 0;
			
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,
					
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
			
			$office_ids = array_keys($offices);
			
			if($office_ids)$conditions['Territory.office_id'] = $office_ids;

		}
		else 
		{
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id'=>2);
			$office_id = $this->UserAuth->getOfficeId();
			
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'id' 	=> $office_id,
				),	 
				'order'=>array('office_name'=>'asc')
			));
			
		}
		
		
		
		$dis_con = array();
		
		
		
		if($this->request->is('post') || $this->request->is('put'))
		{
			
			$request_data = $this->request->data;
			
			$date_from = date('Y-m-d', strtotime($request_data['DistMarketCharacteristicReports']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['DistMarketCharacteristicReports']['date_to']));
			$this->set(compact('date_from', 'date_to'));
			
			$report_type = $this->request->data['DistMarketCharacteristicReports']['report_type'];
			$this->set(compact('report_type'));
			
			$region_office_id = isset($this->request->data['DistMarketCharacteristicReports']['region_office_id']) != '' ? $this->request->data['DistMarketCharacteristicReports']['region_office_id'] : $region_office_id;
			$this->set(compact('region_office_id'));
			$office_ids = array();
			if($region_office_id)
			{
				$offices = $this->Office->find('list', array(
					'conditions'=> array(
						'office_type_id' 	=> 2,
						'parent_office_id' 	=> $region_office_id,
						
						"NOT" => array( "id" => array(30, 31, 37))
						), 
					'order'=>array('office_name'=>'asc')
				));
				
				$office_ids = array_keys($offices);
			}
			
			$office_id = isset($this->request->data['DistMarketCharacteristicReports']['office_id']) != '' ? $this->request->data['DistMarketCharacteristicReports']['office_id'] : $office_id;
			$this->set(compact('office_id'));
			$db_id = isset($this->request->data['DistMarketCharacteristicReports']['db_id']) != '' ? $this->request->data['DistMarketCharacteristicReports']['db_id'] : 0;
			
			$this->set(compact('db_id'));
			//for report data
			$route_id = isset($this->request->data['DistMarketCharacteristicReports']['route_id']) != '' ? $this->request->data['DistMarketCharacteristicReports']['route_id'] : 0;
			
			$market_id = isset($this->request->data['DistMarketCharacteristicReports']['market_id']) != '' ? $this->request->data['DistMarketCharacteristicReports']['market_id'] : 0;
			
			//thana wise total markets
			$t_m_con = array();
			$t_m_con['DistMarket.is_active'] = 1;
			if($route_id)
				$t_m_con['DistMarket.dist_route_id'] = $route_id;

			if($office_ids)$conditions['DistRoute.office_id'] = $office_ids;
			if($office_id)$conditions['DistRoute.office_id'] = $office_id;

			$total_markets_thana_wise_results = $this->DistMarket->find('all', array(
			'joins' => array(
					array(
						'alias' => 'DistRoute',
						'table' => 'dist_routes',
						'type' => 'INNER',
						'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
					)
				),
			'conditions' => $t_m_con,
			'fields' => array('count(DISTINCT DistMarket.id) as total_market', 'DistRoute.name'),
			'group' => array('DistMarket.dist_route_id', 'DistRoute.name'),
			'recursive' => -1
			));
			
			$total_markets_thana_wise = array();
			foreach($total_markets_thana_wise_results as $thana_wise_result){
				$total_markets_thana_wise[($thana_wise_result['DistRoute']['name'])]=$thana_wise_result[0]['total_market'];
			}
			$this->set(compact('total_markets_thana_wise'));
			
			//market wise total outlets
			$m_o_con = array( 'DistOutlet.is_active' => 1);
			if($route_id)
				$m_o_con ['DistMarket.dist_route_id'] = $route_id;
			if($market_id)
				$m_o_con ['DistOutlet.dist_market_id'] = $market_id;
			
			$total_outlets_market_wise_results = $this->DistOutlet->find('all', 
			array(
				'conditions' => $m_o_con,
				'joins'=>array(
					array(
						'table'=>'dist_markets',
						'alias'=>'DistMarket',
						'conditions'=>'DistMarket.id=DistOutlet.dist_market_id'
						)
					),
				'group' => array('DistOutlet.dist_market_id', 'DistMarket.name'),
				'fields' => array('count(DISTINCT DistOutlet.id) as total_outlet', 'DistMarket.name'),
				'recursive'=>-1
			));
			$total_outlets_market_wise = array();
			foreach($total_outlets_market_wise_results as $market_wise_result){
				$total_outlets_market_wise[($market_wise_result['DistMarket']['name'])]=$market_wise_result[0]['total_outlet'];
			}
			$this->set(compact('total_outlets_market_wise'));
			// pr($total_outlets_market_wise);
			// exit;
			
			
			//START DATA QUERY
			if($report_type=='visited')
			{
				
				$conditions = array();
				
				$conditions = array(
					'DistMemo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					//'Memo.gross_value >' => 0,
					'DistMemo.status !=' => 0,
				);
				
				if($office_ids)$conditions['DistMemo.office_id'] = $office_ids;
				if($office_id)$conditions['DistMemo.office_id'] = $office_id;	
				if($db_id)$conditions['DistMemo.distributor_id'] = $db_id;
				if($route_id)$conditions['DistMarket.dist_route_id'] = $route_id;
				if($market_id)$conditions['DistMemo.market_id'] = $market_id;
												
				
				/*pr($conditions);
				exit;*/
				
				$q_results = $this->DistMemo->find('all', array(
					'conditions'=> $conditions,
					'joins' => array(
						
						array(
							'alias' => 'DistSalesRrepresentatives',
							'table' => 'dist_sales_representatives',
							'type' => 'INNER',
							'conditions' => 'DistMemo.sr_id = DistSalesRrepresentatives.id'
						),
						array(
							'alias' => 'DistMarket',
							'table' => 'dist_markets',
							'type' => 'INNER',
							'conditions' => 'DistMemo.market_id = DistMarket.id'
						),
						array(
							'alias' => 'DistRoute',
							'table' => 'dist_routes',
							'type' => 'INNER',
							'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
						),
					
					),
					
					'fields' => array('count(DistMemo.outlet_id) as total_outlet', 'sum(DistMemo.gross_value) as memo_total',  'DistMemo.outlet_id', 'DistMemo.memo_date', 'DistSalesRrepresentatives.name', 'DistMarket.name', 'DistRoute.name'),
					
					'group' => array('DistMemo.memo_date', 'DistMemo.outlet_id', 'DistSalesRrepresentatives.name', 'DistMarket.name', 'DistRoute.name'),
					
					'order' => array('DistRoute.name asc', 'DistMarket.name asc', 'DistMemo.memo_date asc'),
					
					'recursive' => -1
				));	
				
				//pr($q_results);
				
				$results = array();
				foreach($q_results as $q_result)
				{
					$results[$q_result['DistRoute']['name']][$q_result['DistMarket']['name']][$q_result['DistMemo']['outlet_id']][] 
					= array(
						'memo_total'			=> $q_result[0]['memo_total'],
						'memo_date'				=> $q_result['DistMemo']['memo_date'],
						'outlet_id'				=> $q_result['DistMemo']['outlet_id'],
						'so_name'				=> $q_result['DistSalesRrepresentatives']['name'],
					);
				}
				
				$this->set(compact('results'));
				
				//pr($results);
				//exit;
				
				
			}
			
			if($report_type=='non_visited')
			{				
				
				$conditions = array();
				
				$conditions = array(
					'DistMemo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					//'Memo.gross_value >' => 0,
					'DistMemo.status !=' => 0,
				);
				
				if($office_ids)$conditions['DistMemo.office_id'] = $office_ids;
				if($office_id)$conditions['DistMemo.office_id'] = $office_id;	
				if($db_id)$conditions['DistMemo.distributor_id'] = $db_id;
				if($route_id)$conditions['DistMarket.dist_route_id'] = $route_id;
				if($market_id)$conditions['DistMemo.market_id'] = $market_id;
												
				
				/*pr($conditions);
				exit;*/
				
				$q_results = $this->DistMemo->find('all', array(
					'conditions'=> $conditions,
					'joins' => array(
						array(
							'alias' => 'DistSalesRrepresentatives',
							'table' => 'dist_sales_representatives',
							'type' => 'INNER',
							'conditions' => 'DistMemo.sr_id = DistSalesRrepresentatives.id'
						),
						array(
							'alias' => 'DistMarket',
							'table' => 'dist_markets',
							'type' => 'INNER',
							'conditions' => 'DistMemo.market_id = DistMarket.id'
						),
						array(
							'alias' => 'DistRoute',
							'table' => 'dist_routes',
							'type' => 'INNER',
							'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
						),
					),
					
					
					'fields' => array('DISTINCT DistMemo.market_id'),
					
					'group' => array('DistMemo.market_id'),
										
					'recursive' => -1
				));	
				
				$market_ids = array();
				foreach($q_results as $q_result)
				{
					array_push($market_ids, $q_result['DistMemo']['market_id']);
				}
				
				//pr($market_ids);
				//exit;
				
				
				//outlet lists
				$con = array();
				if($office_ids)$con['DistRoute.office_id'] = $office_ids;
				if($office_id)$con['DistRoute.office_id'] = $office_id;	
				if($db_id)$con['DistRouteMappingHistory.dist_distributor_id'] = $db_id;
				
				if($route_id)$con['DistMarket.dist_route_id'] = $route_id;
				if($market_id)$con['DistMarket.id'] = $market_id;
				
				if($market_ids)$con['NOT'] = array("DistMarket.id" => $market_ids);
				
				//pr($con);
				//exit;
				
				$m_results = $this->DistMarket->find('all', array(
					'conditions'=> $con,
					'joins' => array(
						array(
							'table'=>'dist_routes',
							'alias'=>'DistRoute',
							'conditions'=>'DistRoute.id=DistMarket.dist_route_id'
							),
						array(
							'table'=>'dist_route_mapping_histories',
							'alias'=>'DistRouteMappingHistory',
							'conditions'=>'DistRoute.id=DistRouteMappingHistory.dist_route_id and is_change=1 and DistRouteMappingHistory.effective_date <=\''.$date_to.'\' AND (DistRouteMappingHistory.end_date is null OR DistRouteMappingHistory.end_date >\''.$date_from.'\')'
							),
					),

					'fields' => array('DistMarket.name', 'DistRoute.name'),
					'group' => array('DistMarket.name', 'DistRoute.name'),
					'order' => array('DistRoute.name asc', 'DistMarket.name asc'),
					//'limit' => 3000,
					'recursive' => -1
					
				));	
				
				//pr($m_results);
				//exit;
				
				$results = array();
				foreach($m_results as $q_result)
				{
					$results[$q_result['DistRoute']['name']][$q_result['DistMarket']['name']][] 
					= array();
				}
				
				$this->set(compact('results'));
				
				//pr($results);
				//exit;
				
				
			}
			
			
			if($report_type=='visit_info')
			{
				
				$conditions = array();
				
				$conditions = array(
					'DistMemo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					//'Memo.gross_value >' => 0,
					'DistMemo.status !=' => 0,
				);
				
				if($office_ids)$conditions['DistMemo.office_id'] = $office_ids;
				if($office_id)$conditions['DistMemo.office_id'] = $office_id;	
				if($db_id)$conditions['DistMemo.distributor_id'] = $db_id;
				if($route_id)$conditions['DistMarket.dist_route_id'] = $route_id;
				if($market_id)$conditions['DistMemo.market_id'] = $market_id;
												
				
				/*pr($conditions);
				exit;*/
				
				
				
				
				
				//For Visited 
				$q_results1 = $this->DistMemo->find('all', array(
					'conditions'=> $conditions,
					'joins' => array(
						array(
							'alias' => 'DistSalesRrepresentatives',
							'table' => 'dist_sales_representatives',
							'type' => 'INNER',
							'conditions' => 'DistMemo.sr_id = DistSalesRrepresentatives.id'
						),
						array(
							'alias' => 'DistMarket',
							'table' => 'dist_markets',
							'type' => 'INNER',
							'conditions' => 'DistMemo.market_id = DistMarket.id'
						),
						array(
							'alias' => 'DistRoute',
							'table' => 'dist_routes',
							'type' => 'INNER',
							'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
						),
					),
					
					'fields' => array('count(DistMemo.outlet_id) as total_outlet', 'sum(DistMemo.gross_value) as memo_total',  'DistMemo.outlet_id', 'DistMemo.market_id', 'DistMemo.memo_date', 'DistSalesRrepresentatives.name',  'DistMarket.name', 'DistRoute.name'),
					
					'group' => array('DistMemo.memo_date', 'DistMemo.outlet_id', 'DistMemo.market_id', 'DistSalesRrepresentatives.name', 'DistMarket.name', 'DistRoute.name'),
					
					'order' => array('DistRoute.name asc', 'DistMarket.name asc', 'DistMemo.memo_date asc'),
					
					'recursive' => -1
				));	
				
				/*pr($q_results1);
				exit;*/
				
				$results1 = array();
				foreach($q_results1 as $q_result)
				{
					$results1[$q_result['DistRoute']['name']][$q_result['DistMarket']['name']][$q_result['DistMemo']['outlet_id']][] 
					= array(
						'memo_total'			=> $q_result[0]['memo_total'],
						'memo_date'				=> $q_result['DistMemo']['memo_date'],
						'outlet_id'				=> $q_result['DistMemo']['outlet_id'],
						'so_name'				=> $q_result['DistSalesRrepresentatives']['name'],
					);
				}
				
				$this->set(compact('results1'));
		
				//For Non-Visited 
				$con = array();
				if($office_ids)$con['DistRoute.office_id'] = $office_ids;
				if($office_id)$con['DistRoute.office_id'] = $office_id;	
				if($db_id)$con['DistRouteMappingHistory.dist_distributor_id'] = $db_id;
				
				if($route_id)$con['DistMarket.dist_route_id'] = $route_id;
				if($market_id)$con['DistMarket.id'] = $market_id;
				
				
				$m_results = $this->DistMarket->find('all', array(
					'conditions'=> $con,
					'joins' => array(
						array(
							'table'=>'dist_routes',
							'alias'=>'DistRoute',
							'conditions'=>'DistRoute.id=DistMarket.dist_route_id'
							),
						array(
							'table'=>'dist_route_mapping_histories',
							'alias'=>'DistRouteMappingHistory',
							'conditions'=>'DistRoute.id=DistRouteMappingHistory.dist_route_id and is_change=1 and DistRouteMappingHistory.effective_date <=\''.$date_to.'\' AND (DistRouteMappingHistory.end_date is null OR DistRouteMappingHistory.end_date >\''.$date_from.'\')'
							),
					),
					
					'fields' => array('DistMarket.name', 'DistRoute.name'),
					'group' => array('DistMarket.name', 'DistRoute.name'),
					'order' => array('DistRoute.name asc', 'DistMarket.name asc'),
					//'limit' => 3000,
					'recursive' => -1
					
				));	
				
				
				$results = array();
				foreach($m_results as $q_result)
				{
					$results[$q_result['DistRoute']['name']][$q_result['DistMarket']['name']][] 
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
		
		$office_conditions = array('NOT' => array( "id" => array(30, 31, 37)), 'Office.office_type_id'=>2);
		//$office_conditions = array('Office.parent_office_id' => $parent_office_id, 'Office.office_type_id'=>2);
		
		if($parent_office_id)$office_conditions['Office.parent_office_id'] = $parent_office_id;
		
		$offices = $this->Office->find('all', array(
			'fields' => array('id', 'office_name'),
			'conditions' => $office_conditions, 
			'order' => array('office_name' => 'asc'),
			'recursive' => -1
			)
		);
		
		
		$data_array = array();
		foreach($offices as $office){
			$data_array[] = array(
				'id'=>$office['Office']['id'],
				'name'=>$office['Office']['office_name'],
			);
		}
				
		//$data_array = Set::extract($offices, '{n}.Office');
						
		if(!empty($offices)){
			echo json_encode(array_merge($rs, $data_array));
		}else{
			echo json_encode($rs);
		} 
		
		$this->autoRender = false;
	}
	function get_db_list() {
		$this->loadModel('DistTso');
		$this->loadModel('DistTsoMapping');
		$this->loadModel('DistRouteMapping');
		$this->loadModel('DistAreaExecutive');
		$user_id = $this->UserAuth->getUserId();
		$user_group_id = $this->Session->read('UserAuth.UserGroup.id');

		$office_id = $this->request->data['office_id'];
		$memo_date_from = $this->request->data['date_from'];
		$memo_date_to = $this->request->data['date_to'];
		$output = "<option value=''>--- Select Distributor ---</option>";

		if($memo_date_from && $office_id && $memo_date_to){
			$memo_date_from = date("Y-m-d", strtotime($memo_date_from));
			$memo_date_to = date("Y-m-d", strtotime($memo_date_to));
		}
		$this->loadModel('DistTsoMappingHistory');
		$this->loadModel('DistDistributor');
		if($user_group_id == 1029 || $user_group_id == 1028){
			if($user_group_id == 1028){
				$dist_ae_info = $this->DistAreaExecutive->find('first',array(
					'conditions'=>array('DistAreaExecutive.user_id'=>$user_id),
					'recursive'=> -1,
					));
				$dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
				$dist_tso_info = $this->DistTso->find('list',array(
					'conditions'=>array('dist_area_executive_id'=>$dist_ae_id),
					'fields'=> array('DistTso.id','DistTso.dist_area_executive_id'),
					));

				$dist_tso_id = array_keys($dist_tso_info);
			}
			else{
				$dist_tso_info = $this->DistTso->find('first',array(
					'conditions'=>array('DistTso.user_id'=>$user_id),
					'recursive'=> -1,
					));
				$dist_tso_id = $dist_tso_info['DistTso']['id'];
			}

			$tso_dist_list = $this->DistTsoMapping->find('list',array(
				'conditions'=> array(
					'dist_tso_id' => $dist_tso_id,
					),
				'fields'=>array('DistTsoMapping.dist_distributor_id','DistTsoMapping.dist_tso_id'),
				));
			$dist_conditions = array(
				'DistMemo.distributor_id'=>array_keys($tso_dist_list),
				'OR'=>array(
						array(
							'DistMemo.memo_date' => null,
							'DistDistributor.is_active' => 1
						),
								array(
							'DistMemo.memo_date >=' => $memo_date_from,
							'DistMemo.memo_date <=' => $memo_date_to
						)
					)
				);

		}
		elseif($user_group_id == 1034){
			$sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
			$this->loadModel('DistUserMapping');
			$distributor = $this->DistUserMapping->find('first',array(
				'conditions'=>array('DistUserMapping.sales_person_id'=>$sales_people_id),
				));
			$distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];

			$dist_conditions = array('DistMemo.distributor_id'=>$distributor_id,'DistMemo.memo_date >=' => $memo_date_from,'DistMemo.memo_date <=' => $memo_date_to);
		}
		else{
			$dist_conditions = array(
					'DistMemo.office_id'=>$office_id,
					'OR'=>array(
						array(
							'DistMemo.memo_date' => null,
							'DistDistributor.is_active' => 1
						),
								array(
							'DistMemo.memo_date >=' => $memo_date_from,
							'DistMemo.memo_date <=' => $memo_date_to
						)
					)
					);
		}
		if ($memo_date_from && $office_id && $memo_date_to) {

			$distDistributors = $this->DistDistributor->find('list', array(
				'conditions' => $dist_conditions,
				'joins'=>array(array('table'=>'dist_memos','alias'=>'DistMemo','conditions'=>'DistMemo.distributor_id=DistDistributor.id','type'=>'Left')),
				'group'=>array('DistDistributor.id','DistDistributor.name'),
				'order' => array('DistDistributor.name' => 'asc'),
				));
			
			if ($distDistributors) {
				$selected="";
				foreach ($distDistributors as $key => $data) {
					$output .= "<option $selected value='$key'>$data</option>";
				}
			}
		}
		echo $output;
		$this->autoRender = false;
	}
	//get thana list
	public function get_route_list()
	{
		$view = new View($this);
		
        $form = $view->loadHelper('Form');	
		
	    $db_id = $this->request->data['db_id'];
	    $date_from=date('Y-m-d',strtotime($this->request->data['date_from']));
	    $date_to=date('Y-m-d',strtotime($this->request->data['date_to']));
		
		$conditions = array();
		
		if($db_id)
		{
			$route_list=$this->DistRouteMappingHistory->find('list',
				array(
					'conditions'=>array(
						'DistRouteMappingHistory.dist_distributor_id'=>$db_id,
						'DistRouteMappingHistory.is_change'=>1,
						'DistRouteMappingHistory.effective_date <='=>$date_to,
						'OR'=>array(
							'DistRouteMappingHistory.end_date'=>null,
							'DistRouteMappingHistory.end_date >'=>$date_from
							),
						),
					'joins'=>array(
						array(
							'table'=>'dist_routes',
							'alias'=>'DistRoute',
							'conditions'=>'DistRoute.id=DistRouteMappingHistory.dist_route_id'
							)
						),
					'fields'=>array('DistRoute.id','DistRoute.name'),
					'group'=>array('DistRoute.id','DistRoute.name'),
					'order'=>array('DistRoute.name'),
					)
				);
			
			if($route_list)
			{	
				
				$output = '<div class="input select">
  							<input type="hidden" name="data[DistMarketCharacteristicReports][route_id]" value="" id="route_id"/>';
							foreach($route_list as $key => $val)
							{
								$output.= '<div class="checkbox">
									<input type="checkbox" onClick="marketBoxList()" name="data[DistMarketCharacteristicReports][route_id][]" value="'.$key.'" id="route_id'.$key.'" />
									<label for="route_id'.$key.'">'.$val.'</label>
								  </div>';
							}
							$output.='</div>';
				
				echo $output;
			}
			else
			{
				echo '';	
			}
		}
		else
		{
			echo '';	
		}
		
		
        $this->autoRender = false;
    }
	
	
	//get market list
	public function get_market_list()
	{
		$view = new View($this);
		
        $form = $view->loadHelper('Form');	
		
		$route_id = $this->request->data['route_id'];
	    
		
		$conditions = array();
		
		if($route_id)
		{
			$ids = explode(',', $route_id);
			$conditions['DistMarket.dist_route_id'] = $ids;
			$conditions['DistMarket.is_active'] = 1;
			
			//pr($conditions);
			//exit;
			
			$results = $this->DistMarket->find('list', array(
				'conditions'=> $conditions,
				'order'=>  array('DistMarket.name'=>'asc'),
				'recursive' => 1
			));
			
			if($results)
			{	
				$output = '<div class="input select">
  							<input type="hidden" name="data[DistMarketCharacteristicReports][market_id]" value="" id="market_id"/>';
							foreach($results as $key => $val)
							{
								$output.= '<div class="checkbox">
									<input type="checkbox" name="data[DistMarketCharacteristicReports][market_id][]" value="'.$key.'" id="market_id'.$key.'" />
									<label for="market_id'.$key.'">'.$val.'</label>
								  </div>';
							}
				$output.='</div>';
				
				echo $output;
			}
			else
			{
				echo '';	
			}
		}
		else
		{
			echo '';	
		}
		
		
        $this->autoRender = false;
    }
		
	
}
