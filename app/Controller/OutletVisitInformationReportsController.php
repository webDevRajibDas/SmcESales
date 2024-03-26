<?php
App::uses('AppController', 'Controller');
/**
 * OutletCharacteristicSettings Controller
 *
 * @property OutletCharacteristicReport $OutletCharacteristicReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
 
class OutletVisitInformationReportsController extends AppController {
	

/**
 * Components
 *
 * @var array
 */
 
	public $uses = array('Product','ProductCategory','Memo', 'Office', 'Territory', 'OutletCategory', 'Outlet', 'SalesPerson', 'Brand', 'ProductCategory', 'TerritoryAssignHistory');
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
				
		
		$this->set('page_title', "Outlet Visit Information Report");
		
		$request_data = array();
		$report_type = array();
		$so_list = array();
		$institute = array();
					
		//for region office list
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id'=>3), 
			'order' => array('office_name' => 'asc')
		));
		
		//types
		$types = array(
			'territory' => 'By Terriotry',
			'so' => 'By SO',
		);
		$this->set(compact('types'));
		
		//report types
		$report_types = array(
			'summary' => 'Summary',
			'details' => 'Details',
		);
		$this->set(compact('report_types'));
		
		//sales
		$sales = array(
			'sales_qty' => 'Volume',
			'price' => 'Value',
		);
		$this->set(compact('sales'));

		//for outlet category list
		$outlet_categories = $this->OutletCategory->find('list', array(
			'conditions' => array('is_active'=>1),
			'order' => array('category_name' => 'asc')
		));
		$this->set(compact('outlet_categories'));
		
		
		
		
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
					
			//pr($conditions);
			//exit;
						
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
		
			foreach($territory_list as $key => $value)
			{
				$territories[$value['Territory']['id']] = $value['Territory']['name'].' ('.$value['SalesPerson']['name'].')';
			}
			
			//get SO list
			$so_list_r = $this->SalesPerson->find('all', array(
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
		}
		
		//pr($offices);
		
		if($this->request->is('post') || $this->request->is('put'))
		{
			
			$all_offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' 	=> 2,					
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
			$this->set(compact('all_offices'));
						
			$request_data = $this->request->data;
			
			//pr($request_data);
			
			$date_from = date('Y-m-d', strtotime($request_data['OutletVisitInformationReports']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['OutletVisitInformationReports']['date_to']));
			$this->set(compact('date_from', 'date_to'));
			
			$type = $this->request->data['OutletVisitInformationReports']['type'];
			$this->set(compact('type'));
			
			$region_office_id = isset($this->request->data['OutletVisitInformationReports']['region_office_id']) != '' ? $this->request->data['OutletVisitInformationReports']['region_office_id'] : $region_office_id;
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
			
			$office_id = isset($this->request->data['OutletVisitInformationReports']['office_id']) != '' ? $this->request->data['OutletVisitInformationReports']['office_id'] : $office_id;
			$this->set(compact('office_id'));
			
			$territory_id = isset($this->request->data['OutletVisitInformationReports']['territory_id']) != '' ? $this->request->data['OutletVisitInformationReports']['territory_id'] : 0;
			$this->set(compact('territory_id'));
			
			$so_id = isset($this->request->data['OutletVisitInformationReports']['so_id']) != '' ? $this->request->data['OutletVisitInformationReports']['so_id'] : 0;
			$this->set(compact('so_id'));
			
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
		
			foreach($territory_list as $key => $value)
			{
				$territories[$value['Territory']['id']] = $value['Territory']['name'].' ('.$value['SalesPerson']['name'].')';
			}	
			
			
			//get SO list
			$so_list_r = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id','SalesPerson.name', 'Territory.name'),
				'conditions' => array(
					'SalesPerson.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					'User.user_group_id' => 4,
				),
				'recursive'=> 0
			));	
			
			foreach($so_list_r as $key => $value){
			  $so_list[$value['SalesPerson']['id']]=$value['SalesPerson']['name'].' ('.$value['Territory']['name'].')';
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
			}
			
			$outlet_category_id = isset($this->request->data['OutletVisitInformationReports']['outlet_category_id']) != '' ? $this->request->data['OutletVisitInformationReports']['outlet_category_id'] : 0;
			
			$brand_id = isset($this->request->data['OutletVisitInformationReports']['brand_id']) != '' ? $this->request->data['OutletVisitInformationReports']['brand_id'] : 0;
						
			$product_id = isset($this->request->data['OutletVisitInformationReports']['product_id']) != '' ? $this->request->data['OutletVisitInformationReports']['product_id'] : 0;
						
			$product_category_id = isset($this->request->data['OutletVisitInformationReports']['product_category_id']) != '' ? $this->request->data['OutletVisitInformationReports']['product_category_id'] : 0;
			
			$no_of_outlet = isset($this->request->data['OutletVisitInformationReports']['no_of_outlet']) != '' && (int)$this->request->data['OutletVisitInformationReports']['no_of_outlet'] ? $this->request->data['OutletVisitInformationReports']['no_of_outlet'] : 10;
			$this->set(compact('no_of_outlet'));
			
			//$report_type = $this->request->data['OutletVisitInformationReports']['report_type'];
			//$this->set(compact('report_type'));
			
									
			//For Query Conditon
			$conditions = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Memo.gross_value >' => 0,
				'Memo.status !=' => 0,
				//'MemoDetail.price >' => 0
			);
			
			
			if($office_ids)$conditions['Memo.office_id'] = $office_ids;
			if($office_id)$conditions['Memo.office_id'] = $office_id;	
						
			if($type=='so'){
				if($so_id)$conditions['Memo.sales_person_id'] = $so_id;
			}else{
				if($territory_id)$conditions['Memo.territory_id'] = $territory_id;
			}
			
			if($outlet_category_id)$conditions['Outlet.category_id'] = $outlet_category_id;
			
			//pr($conditions);
			//exit;
			
			$q_results = $this->Memo->find('all', array(
				'conditions'=> $conditions,
				'joins' => array(
					/*array(
						'alias' => 'MemoDetail',
						'table' => 'memo_details',
						'type' => 'INNER',
						'conditions' => 'Memo.id = MemoDetail.memo_id'
					),*/
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
						'conditions' => 'Market.thana_id = Thana.id'
					),
					array(
						'alias' => 'District',
						'table' => 'districts',
						'type' => 'INNER',
						'conditions' => 'Thana.district_id = District.id'
					)
				),
								
				'fields' => array('COUNT(Memo.memo_no) as ec', 'Memo.outlet_id', 'Outlet.name', 'Memo.market_id','Market.name', 'Thana.name', 'District.name', 'Territory.office_id', 'Memo.sales_person_id', 'SalesPeople.name'),
				
				'group' => array('Memo.outlet_id', 'Outlet.name', 'Memo.market_id', 'Market.name', 'Thana.name', 'District.name', 'Territory.office_id', 'Memo.sales_person_id', 'SalesPeople.name'),
				
				//'order' => array('sales_qty desc', 'Market.name asc', 'Outlet.name asc'),
				
				'order' => array('Thana.name asc', 'Market.name asc', 'Outlet.name'),
				
				'recursive' => -1,
				//'limit' => 200
			));	
			
			//pr($q_results);
			//exit;
			
			$results = array();
			foreach($q_results as $result){
				$results[$result['District']['name']][$result['Thana']['name']][$result['Market']['name']][$result['Memo']['outlet_id']] = 
				array(
					'ec' 					=> $result[0]['ec'],
					'office_id' 			=> $result['Territory']['office_id'],
					'so_id' 				=> $result['Memo']['sales_person_id'],
					'so_name' 				=> $result['SalesPeople']['name'],
					'outlet_id' 			=> $result['Memo']['outlet_id'],
					'outlet_name' 			=> $result['Outlet']['name'],
					'market_id' 			=> $result['Memo']['market_id'],
					'market_name' 			=> $result['Market']['name'],
					'thana_name' 			=> $result['Thana']['name'],
					'district_name' 		=> $result['District']['name'],
				);
			}
			
			//pr($results);
			//exit;
			
			$this->set(compact('results'));
			
						
		}
						
				
		$this->set(compact('offices', 'territories', 'outlet_type', 'region_offices', 'office_id', 'request_data', 'so_list'));
		
	}
	
	
	function details()
	{
							
				
		
				
			
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id'=>3), 
			'order' => array('office_name' => 'asc')
		));
		$this->set(compact('region_offices'));
		
		$all_offices = $this->Office->find('list', array(
			'conditions'=> array(
				'office_type_id' 	=> 2,					
				"NOT" => array( "id" => array(30, 31, 37))
				), 
			'order'=>array('office_name'=>'asc')
		));
		$this->set(compact('all_offices'));
					
		$request_data = $this->params['url'];
		
		$date_from = $request_data['date_from'];
		$date_to = $request_data['date_to'];
		
		$region_office_id = $request_data['region_office_id'];
		$office_id = $request_data['office_id'];
		$territory_id = $request_data['territory_id'];
		$so_id = $request_data['so_id'];
		$market_id = $request_data['market_id'];
		
		
		$this->set(compact('office_id', 'date_from', 'date_to', 'market_id', 'region_office_id', 'territory_id'));
		

		
		//pr($request_data);
		
		//exit;
		
		
		//$sales_type = $this->request->data['OutletVisitInformationReports']['sales'];
		//$this->set(compact('sales_type'));
					
		//For Query Conditon
		$conditions = array(
			'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
			'Memo.gross_value >' => 0,
			'Memo.status !=' => 0,
			//'MemoDetail.price >' => 0
		);
		
		
		//if($office_id)$conditions['Territory.office_id'] = $office_id;	
		
		if($so_id)$conditions['Memo.sales_person_id'] = $so_id;

		//if($territory_id)$conditions['Memo.territory_id'] = $territory_id;
		
		if($market_id)$conditions['Memo.market_id'] = $market_id;
		
		
		//pr($conditions);
		//exit;
		
		$q_results = $this->Memo->find('all', array(
			'conditions'=> $conditions,
			'joins' => array(
				/*array(
					'alias' => 'MemoDetail',
					'table' => 'memo_details',
					'type' => 'INNER',
					'conditions' => 'Memo.id = MemoDetail.memo_id'
				),*/
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
					'conditions' => 'Market.thana_id = Thana.id'
				),
				array(
					'alias' => 'District',
					'table' => 'districts',
					'type' => 'INNER',
					'conditions' => 'Thana.district_id = District.id'
				)
			),
							
			'fields' => array('COUNT(Memo.memo_no) as ec', 'Memo.outlet_id', 'Outlet.name', 'Market.name', 'Thana.name', 'District.name', 'Territory.office_id', 'SalesPeople.name'),
			
			'group' => array('Memo.outlet_id', 'Outlet.name', 'Memo.market_id', 'Market.name', 'Thana.name', 'District.name', 'Territory.office_id', 'SalesPeople.name'),
			
			//'order' => array('sales_qty desc', 'Market.name asc', 'Outlet.name asc'),
			
			'order' => array('Thana.name asc', 'Market.name asc', 'Outlet.name'),
			
			'recursive' => -1,
			//'limit' => 200
		));	
		
		//pr($q_results);
		//exit;
		
		$results1 = array();
		$results2 = array();
		$results3 = array();
		$results4 = array();
		
		foreach($q_results as $result)
		{
			
			if($result[0]['ec']==1)
			{
				$results1[$result['District']['name']][$result['Thana']['name']][$result['Market']['name']][$result['Memo']['outlet_id']] = 
				array(
					'ec' 					=> $result[0]['ec'],
					'office_id' 			=> $result['Territory']['office_id'],
					'so_name' 				=> $result['SalesPeople']['name'],
					'outlet_id' 			=> $result['Memo']['outlet_id'],
					'outlet_name' 			=> $result['Outlet']['name'],
					'market_name' 			=> $result['Market']['name'],
					'thana_name' 			=> $result['Thana']['name'],
					'district_name' 		=> $result['District']['name'],
				);
			}
			if($result[0]['ec']==2)
			{
				$results2[$result['District']['name']][$result['Thana']['name']][$result['Market']['name']][$result['Memo']['outlet_id']] = 
				array(
					'ec' 					=> $result[0]['ec'],
					'office_id' 			=> $result['Territory']['office_id'],
					'so_name' 				=> $result['SalesPeople']['name'],
					'outlet_id' 			=> $result['Memo']['outlet_id'],
					'outlet_name' 			=> $result['Outlet']['name'],
					'market_name' 			=> $result['Market']['name'],
					'thana_name' 			=> $result['Thana']['name'],
					'district_name' 		=> $result['District']['name'],
				);
			}
			if($result[0]['ec']==3)
			{
				$results3[$result['District']['name']][$result['Thana']['name']][$result['Market']['name']][$result['Memo']['outlet_id']] = 
				array(
					'ec' 					=> $result[0]['ec'],
					'office_id' 			=> $result['Territory']['office_id'],
					'so_name' 				=> $result['SalesPeople']['name'],
					'outlet_id' 			=> $result['Memo']['outlet_id'],
					'outlet_name' 			=> $result['Outlet']['name'],
					'market_name' 			=> $result['Market']['name'],
					'thana_name' 			=> $result['Thana']['name'],
					'district_name' 		=> $result['District']['name'],
				);
			}
			if($result[0]['ec']>=4)
			{
				$results4[$result['District']['name']][$result['Thana']['name']][$result['Market']['name']][$result['Memo']['outlet_id']] = 
				array(
					'ec' 					=> $result[0]['ec'],
					'office_id' 			=> $result['Territory']['office_id'],
					'so_name' 				=> $result['SalesPeople']['name'],
					'outlet_id' 			=> $result['Memo']['outlet_id'],
					'outlet_name' 			=> $result['Outlet']['name'],
					'market_name' 			=> $result['Market']['name'],
					'thana_name' 			=> $result['Thana']['name'],
					'district_name' 		=> $result['District']['name'],
				);
			}
		}
		
		//pr($results1);
		//exit;
				
		$this->set(compact('results1', 'results2', 'results3', 'results4'));
		
		
		//For Territory List
		$territory_list = $this->Territory->find('all', array(
			'conditions' => array('Territory.office_id' => $office_id, 'User.user_group_id !=' => 1008),
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
	
		foreach($territory_list as $key => $value)
		{
			$territories[$value['Territory']['id']] = $value['Territory']['name'].' ('.$value['SalesPerson']['name'].')';
		}
		
		$this->set(compact('territories'));	
			
						
		
	}
	
	
}
