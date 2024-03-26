<?php
App::uses('AppController', 'Controller');
/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
class DistRevenueReportsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $uses = array('Memo', 'Thana', 'Market', 'SalesPerson', 'Outlet', 'Product', 'MeasurementUnit', 'ProductPrice', 'ProductCombination', 'Combination', 'MemoDetail', 'MeasurementUnit', 'ProductCategory', 'TerritoryAssignHistory', 'Office', 'Territory', 'ProductType', 'DistAreaExecutive', 'DistTso', 'DistTsoMapping');
    public $components = array('Paginator', 'Session', 'Filter.Filter');
	
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {

		$user_group_id=$this->Session->read('Office.group_id');
		$office_parent_id = $this->UserAuth->getOfficeParentId();

		ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600); 
        
        $this->set('page_title', 'Distributor Revenue Report');
		
		//for region office list
        $region_offices = $this->Office->find('list', array(
            'conditions' => array('Office.office_type_id'=>3),
            'order' => array('order' => 'asc')
        ));
		
        $territories = array();
        $request_data = array();
        $report_type = array();
        $so_list = array();
		
		
		
		$region_office_id = 0;
						
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		
		$this->set(compact('office_parent_id'));
		
		if ($office_parent_id == 0)
		{
			$office_conditions = array('Office.office_type_id'=>2, "NOT" => array( "id" => array(30, 31, 37)));
						
			$office_id = 0;
		}
		elseif($office_parent_id == 14)
		{
			$region_office_id = $this->UserAuth->getOfficeId();
			$region_offices = $this->Office->find('list', array(
				'conditions' => array('Office.office_type_id'=>3, 'Office.id'=>$region_office_id), 
				'order' => array('order' => 'asc')
			));
			
			$office_conditions = array('Office.parent_office_id'=>$region_office_id);
			
			$office_id = 0;
			
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,
					
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('order'=>'asc')
			));
			
			$office_id = array_keys($offices);
			
		} 
		else 
		{
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id'=>2);
			$office_id = $this->UserAuth->getOfficeId();
			
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'id' 	=> $office_id,
				),	 
				'order'=>array('order'=>'asc')
			));
			
		}

		
		
		
		if($this->request->is('post')){
            $request_data = $this->request->data;
			$this->Session->write('request_data', $request_data);
            $office_id = $request_data['DistRevenueReport']['office_id'];
            //pr($office_id);die();
        }


		$by_colums = array(
			'region'	=> 'By Region',
			'area'		=> 'By Area',
			'national'	=> 'By National',
			'ae'		=> 'By Area Executive',
			'tso'		=> 'By TSO',
			'db'		=> 'By Distributor'
		);

    	

		if($this->request->is('post'))
		{
		 	$request_data = $this->request->data;
			
			//pr($request_data);
			
			$region_office_id = isset($this->request->data['DistRevenueReport']['region_office_id']) != '' ? $this->request->data['DistRevenueReport']['region_office_id'] : $region_office_id;
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
					'order'=>array('order'=>'asc')
				));
				
				$office_ids = array_keys($offices);
			}
			else
			{
				$offices = $this->Office->find('list', array(
						'conditions'=> array(
							'office_type_id' 	=> 2,
							
							"NOT" => array( "id" => array(30, 31, 37))
							), 
						'order'=>array('order'=>'asc')
					));
			}

			$office_id = isset($this->request->data['DistRevenueReport']['office_id']) != '' ? $this->request->data['DistRevenueReport']['office_id'] : $office_id;
			
			if($office_id)
			{
				$aes = $this->DistAreaExecutive->find('list',array(
					'conditions'=>array(
						'DistAreaExecutive.office_id'=>$office_id,
						'DistAreaExecutive.is_active'=> 1,
					),
					'recursive' => 0,
				));
				$this->set(compact('aes'));
			}else{
				$aes = $this->DistAreaExecutive->find('list',array(
					'conditions'=>array(
						//'DistAreaExecutive.office_id'=>$office_id,
						'DistAreaExecutive.is_active'=> 1,
					),
					'recursive' => 0,
				));	
			}
			$tso_ids = array();
			$ae_id = isset($this->request->data['DistRevenueReport']['ae_id']) != '' ? $this->request->data['DistRevenueReport']['ae_id'] : 0;

				@$date_from = date('Y-m-d', strtotime($this->request->data['DistRevenueReport']['date_from']));
			@$date_to = date('Y-m-d', strtotime($this->request->data['DistRevenueReport']['date_to']));
			$this->set(compact('date_from', 'date_to'));
			
			if($ae_id)
			{
				$tsos = $this->DistTso->find('list',array(
					'conditions'=>array(
						'DistTso.dist_area_executive_id'=>$ae_id,
						'DistTso.is_active'=> 1,
					),
					'recursive' => 0,
				));
				$this->set(compact('tsos'));
			}else{
				$tsos = $this->DistTso->find('list',array(
					'conditions'=>array(
						//'DistTso.dist_area_executive_id'=>$ae_id,
						'DistTso.is_active'=> 1,
					),
					'recursive' => 0,
				));	
			}
			
			$tso_id = isset($this->request->data['DistRevenueReport']['tso_id']) != '' ? $this->request->data['DistRevenueReport']['tso_id'] : 0;
			$db_ids = array();
			if($tso_id)
			{
				$this->loadModel('DistTsoMappingHistory');
				$distributors = $this->DistTsoMappingHistory->find('list', array(
					'conditions'=> array(
						'DistTsoMappingHistory.dist_tso_id'=>$tso_id,
						'DistTsoMappingHistory.effective_date <='=>$date_to,
						'or'=>array(
								array('DistTsoMappingHistory.end_date'=>NULL),
								array('DistTsoMappingHistory.end_date >='=>$date_from)
							)
					),
					'joins'=>
						array(
							array(
								'table'=>'dist_distributors',
								'alias'=>'DB',
								'type' => 'LEFT',
								'conditions'=>array('DB.id= DistTsoMappingHistory.dist_distributor_id')
							)
						),
					'groups'=>array('DB.id', 'DB.name'),
					'fields'=>array('DB.id', 'DB.name'),
				));	
				$this->set(compact('distributors'));
			}
			else
			{
				$this->loadModel('DistTsoMappingHistory');
				$distributors = $this->DistTsoMappingHistory->find('list', array(
					'conditions'=> array(
						'DistTsoMappingHistory.effective_date <='=>$date_to,
						'or'=>array(
								array('DistTsoMappingHistory.end_date'=>NULL),
								array('DistTsoMappingHistory.end_date >='=>$date_from)
							)
					),
					'joins'=>
						array(
							array(
								'table'=>'dist_distributors',
								'alias'=>'DB',
								'type' => 'LEFT',
								'conditions'=>array('DB.id= DistTsoMappingHistory.dist_distributor_id')
							)
						),
					'groups'=>array('DB.id', 'DB.name'),
					'fields'=>array('DB.id', 'DB.name'),
				));	
			}
			$distributor_id = isset($this->request->data['DistRevenueReport']['distributor_id']) != '' ? $this->request->data['DistRevenueReport']['distributor_id'] : 0;
			
		
			$conditions = array();
			$conditions['Memo.memo_date BETWEEN ? and ? '] = array($date_from, $date_to);
			$conditions['Memo.is_distributor'] = 1;
			$conditions['Memo.status'] = 2;
			if($region_office_id)$conditions['Office.parent_office_id'] = $region_office_id;
			if($office_id)$conditions['Memo.office_id'] = $office_id;
			if($ae_id)$conditions['DistTso.dist_area_executive_id'] = $ae_id;
			if($tso_id)$conditions['DistTso.id'] = $tso_id;
			//if($db_ids)$conditions['DistOutletmap.dist_distributor_id'] = $db_ids;
			if($distributor_id)$conditions['DistOutletmap.dist_distributor_id'] = $distributor_id;
			
			//pr($conditions);
			
			$group = array();
			$fields = array();
			$col_name = '';
			$col_id = $this->request->data['DistRevenueReport']['col_id'];
			if($col_id=='region'){
				$fields = array('SUM(Memo.gross_value) as total_revenue,Office.parent_office_id as office_id');
				$group = array('Office.parent_office_id');
				$order = array();
				$col_name = 'Region Office';
			}
			if($col_id=='area'){
				$fields = array('SUM(Memo.gross_value) as total_revenue, Memo.office_id as office_id');
				$group = array('Office.order','Memo.office_id');
				$order = array('Office.order asc');
				$col_name = 'Area Office';
			}
			if($col_id=='national'){
				$fields = array('SUM(Memo.gross_value) as total_revenue');
				$group = array();
				$order = array();
				$col_name = 'National';
			}
			if($col_id=='ae'){
				$fields = array('SUM(Memo.gross_value) as total_revenue, DistTso.dist_area_executive_id as ae_id','Office.office_name');
				$group = array('Office.order','DistTso.dist_area_executive_id','Office.office_name');
				$order = array('Office.order asc','DistTso.dist_area_executive_id');
				$col_name = 'Area Executive';
			}
			if($col_id=='tso'){
				$fields = array('SUM(Memo.gross_value) as total_revenue, DistTso.id as tso_id','Office.office_name','DistTso.dist_area_executive_id');
				$group = array('Office.order','DistTso.id','DistTso.name','Office.office_name','DistTso.dist_area_executive_id');
				$order = array('Office.order asc','DistTso.name');
				$col_name = 'TSO';
			}
			if($col_id=='db'){
				$fields = array('SUM(Memo.gross_value) as total_revenue, DistOutletmap.dist_distributor_id as distributor_id','Office.office_name','DistTso.name','DistTso.dist_area_executive_id');
				$group = array('Office.order','DistOutletmap.dist_distributor_id','Office.office_name','DistTso.name','DistTso.dist_area_executive_id');
				$order = array('Office.order asc','DistTso.name asc','DistOutletmap.dist_distributor_id');
				$col_name = 'Distributor';
			}
			//pr($group);
			$joins=array(
						array(
							'table'=>'offices',
							'alias'=>'Office',
							'type' => 'INNER',
							'conditions'=>array('Memo.office_id=Office.id')
						),
						array(
							'table'=>'dist_outlet_maps',
							'alias'=>'DistOutletmap',
							'type' => 'INNER',
							'conditions'=>array('Memo.outlet_id=DistOutletmap.outlet_id')
						)
					
					);
			if($col_id=='tso' || $col_id=='ae' || $ae_id || $tso_id || $col_id=='db')
			{
				/*$joins[]=array(
							'table'=>'dist_tso_mapping_histories',
							'alias'=>'DistTsoMapping',
							'type' => 'INNER',
							'conditions'=>array('DistOutletmap.dist_distributor_id=DistTsoMapping.dist_distributor_id AND is_change=1 AND Memo.memo_date between DistTsoMapping.effective_date and (case when DistTsoMapping.end_date is null then getdate() else DistTsoMapping.end_date end)')
						);*/
				$joins[]=array(
							'table'=>'dist_tsos',
							'alias'=>'DistTso',
							'type' => 'INNER',
							'conditions'=>array('DistTso.id=(
															  SELECT 
																TOP 1 dsmh.dist_tso_id
															  FROM [dist_tso_mapping_histories] AS dsmh
															  WHERE 
															  (
																[DistOutletmap].[dist_distributor_id] = dsmh.[dist_distributor_id]
																AND is_change = 1
																AND [Memo].[memo_date] BETWEEN dsmh.[effective_date] 
																AND (
																	CASE
																		WHEN dsmh.[end_date] IS NULL THEN GETDATE()
																		ELSE dsmh.[end_date]
																		END
																	)
																)
																order by dsmh.id asc
															)
														')
						);
			}
			$m_results = $this->Memo->find('all', array(
				'conditions'=> $conditions,
				'joins'=>$joins,
				'fields' => $fields,
				'group' => $group,
				'order' => $order,
				'recursive' => -1
			));
			/*echo $this->Memo->getLastQuery();	
			pr($m_results);exit;*/

			if($ae_id)
			{
				$tsos_report = $this->DistTso->find('list',array(
					'conditions'=>array(
						'DistTso.dist_area_executive_id'=>$ae_id,
					),
					'recursive' => 0,
				));
				$this->set(compact('tsos'));
			}else{
				$tsos_report = $this->DistTso->find('list',array(
					
					'recursive' => 0,
				));	
			}
			// echo '<pre>';
			// print_r($m_results);
			// echo '</pre>';exit;
			$this->set(compact('m_results'));
			
			$th='';
			
			if($col_id=='ae')
			{
				$th.='<th>Area Office</th>';
				
				
			}
			if($col_id=='tso')
			{
				$th.='<th>Area Office</th>';
				$th.='<th>Area Executive</th>';
				
			}
			if($col_id=='db')
			{
				$th.='<th>Area Office</th>';
				$th.='<th>Area Executive</th>';
				$th.='<th>TSO</th>';
			}
			
			
			$th.='<th>'.$col_name.'</th>';
			
			$html = '<tr class="titlerow">
						  '.$th.'
						  <th>Revenue</th>
					  </tr>';
			
			$g_total = 0;
			foreach($m_results as $result)
			{	$td='';
				if($col_id=='region')
				{
					$r_name = $region_offices[$result[0]['office_id']];	
				}
				if($col_id=='area')
				{
					$r_name = $offices[$result[0]['office_id']];	
				}
				if($col_id=='national')
				{
					$r_name = 'National';	
				}
				if($col_id=='ae')
				{
					$td.='<td>'.$result['Office']['office_name'].'</td>';
					$r_name = $aes[$result[0]['ae_id']]; //ae
						
				}
				if($col_id=='tso')
				{
					$td.='<td>'.$result['Office']['office_name'].'</td>';
					$td.='<td>'.$aes[$result['DistTso']['dist_area_executive_id']].'</td>';
					$r_name = $tsos_report[$result[0]['tso_id']]; //tso
					
				}
				if($col_id=='db')
				{
					$td.='<td>'.$result['Office']['office_name'].'</td>';
					$td.='<td>'.$aes[$result['DistTso']['dist_area_executive_id']].'</td>';
					$td.='<td>'.$result['DistTso']['name'].'</td>';
					$r_name = $distributors[$result[0]['distributor_id']]; //db

					
				}
				
				$td.='<td>'.$r_name.'</td>';
					  
				$html.= '<tr>
						  '.$td.'
						  <td>'.sprintf("%01.2f", $result[0]['total_revenue']).'</td>
					  </tr>';
				$g_total+=$result[0]['total_revenue'];
			}
			$gtd='';
			
			if($col_id=='ae')
			{
				
				$gtd.='<td></td>';
				
				
			}
			if($col_id=='tso')
			{
				
				$gtd.='<td></td>';
				$gtd.='<td></td>';
				
			}
			if($col_id=='db')
			{
				
				$gtd.='<td></td>';
				$gtd.='<td></td>';
				$gtd.='<td></td>';
			}
			
			
			$gtd.='<td style="text-align:right;">Grand Total :</td>';
			
			$html.= '<tr class="titlerow">
						  '.$gtd.'
						  <td>'.sprintf("%01.2f", $g_total).'</td>
					  </tr>';
			
			//echo $html;
			$this->set(compact('html'));
			
			//exit;
		}
		
		
		$this->set(compact('offices', 'office_id', 'sales_people'));
		$this->set(compact('office_parent_id','by_colums','region_offices', 'request_data')); 
		
		
	}

/******************************* Filtering Areaa******************************************/	
	function get_office_list(){
		$region_office_id = $this->request->data['region_office_id'];
		$this->loadModel('Office');
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$offices = $this->Office->find('all', array(
			'conditions' => array('Office.parent_office_id' => $region_office_id),
			'order' => array('Office.order' => 'asc'),
			'recursive' => 0,
		));
		$data_array = array();
		foreach($offices as $key => $value)
		{
			$data_array[] = array(
				'id' => $value['Office']['id'],
				'name' => $value['Office']['office_name'],
			);
		}
		if(!empty($offices)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}
	function get_ae_list(){
		$office_id = $this->request->data['office_id'];
		$this->loadModel('DistAreaExecutive');
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$dist_aes = $this->DistAreaExecutive->find('all',array(
			'conditions'=>array(
				'DistAreaExecutive.office_id'=>$office_id,
				'DistAreaExecutive.is_active'=> 1,
			),
			'recursive' => 0,
		));

		$data_array = array();
		foreach($dist_aes as $key => $value)
		{
			$data_array[] = array(
				'id' => $value['DistAreaExecutive']['id'],
				'name' => $value['DistAreaExecutive']['name'],
			);
		}
		if(!empty($dist_aes)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}
	function get_tso_list(){
		$ae_id = $this->request->data['ae_id'];
		$this->loadModel('DistTso');
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$dist_tsos = $this->DistTso->find('all',array(
			'conditions'=>array(
				'DistTso.dist_area_executive_id'=>$ae_id,
				'DistTso.is_active'=> 1,
			),
			'recursive' => 0,
		));

		$data_array = array();
		foreach($dist_tsos as $key => $value)
		{
			$data_array[] = array(
				'id' => $value['DistTso']['id'],
				'name' => $value['DistTso']['name'],
			);
		}
		if(!empty($dist_tsos)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}
	function get_distributor_list(){		
		
		$tso_id = $this->request->data['tso_id'];
		$this->loadModel('DistTso');
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$this->loadModel('DistTsoMapping');
		$distributors = $this->DistTsoMapping->find('list', array(
			'conditions'=> array(
				//'DistTsoMapping.office_id'=>$office_id,
				'DistTsoMapping.dist_tso_id'=>$tso_id,
			),
			'joins'=>
				array(
	                array(
	                    'table'=>'dist_distributors',
	                    'alias'=>'DB',
	                    'type' => 'LEFT',
	                    'conditions'=>array('DB.id= DistTsoMapping.dist_distributor_id')
	                )
	            ),
	        'fields'=>array('DB.id','DB.name'),
		));
		
		//pr($distributors);exit;

		$data_array = array();
		foreach($distributors as $key => $value)
		{
			$data_array[] = array(
				'id' => $key,
				'name' => $value,
			);
		}
		if(!empty($distributors)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}

	/*********End*********/
}
