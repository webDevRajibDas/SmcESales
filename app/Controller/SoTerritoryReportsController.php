<?php
App::uses('AppController', 'Controller');
/**
 * ProjectionAchievementSettings Controller
 *
 * @property ProjectionAchievementReport $ProjectionAchievementReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class SoTerritoryReportsController extends AppController {



    /**
     * Components
     *
     * @var array
     */

    public $uses = array('Product','ProductCategory','Memo', 'Office', 'Territory', 'Division', 'District', 'Thana', 'Market', 'OutletCategory', 'Outlet', 'TerritoryAssignHistory', 'SalesPerson', 'Brand');
    public $components = array('Paginator', 'Session', 'Filter.Filter');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index($id = null)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600); //300 seconds = 5 minutes


        $this->set('page_title', 'So Territory Report');
        $territories = array();
        $request_data = array();
        $report_type = array();
        $so_list = array();


        //types
        $types = array(
            'territory' => 'By Terriotry',
            'so' => 'By SO',
        );
        $this->set(compact('types'));

        $columns = array(
            'product' => 'By Product',
            'brand' => 'By Brand',
            'category' => 'By Category'
        );
        $this->set(compact('columns'));



        // For SO Wise or Territory Wise

        $territoty_selection = array(
            '1' => 'Territory Wise',
            '2' => 'SO Wise',
        );
        $this->set(compact('territoty_selection'));


        //for region office list
        $region_offices = $this->Office->find('list', array(
            'conditions' => array('Office.office_type_id'=>3),
            'order' => array('office_name' => 'asc')
        ));

        //products
        $conditions = array(
            'NOT' => array('Product.product_category_id'=>32),
            'is_active' => 1,
			'Product.product_type_id' => 1
        );

        $product_list = $this->Product->find('list', array(
            'conditions'=> $conditions,
            'order'=>  array('order'=>'asc')
        ));
        $this->set(compact('product_list'));

        
		//for outlet category list
		$outlet_categories = $this->OutletCategory->find('list', array(
			'conditions' => array('is_active'=>1), 
			'order' => array('category_name' => 'DESC')
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
			
			//get SO list
			$so_list_r = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id','SalesPerson.name', 'Territory.name'),
				'conditions' => array(
					'SalesPerson.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					//'User.user_group_id' => 4,
					'User.user_group_id' => array(4,1008),
				),
				'recursive'=> 0
			));	
			foreach($so_list_r as $key => $value)
			{
				$so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'].' ('.$value['Territory']['name'].')';
			}						
		}



        if($this->request->is('post') || $this->request->is('put'))
		{
			$request_data = $this->request->data;
			
			$date_from = date('Y-m-d', strtotime($request_data['SoTerritoryReports']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['SoTerritoryReports']['date_to']));
			$this->set(compact('date_from', 'date_to'));
			
			$type = $this->request->data['SoTerritoryReports']['type'];
			$this->set(compact('type'));
			
			$region_office_id = isset($this->request->data['SoTerritoryReports']['region_office_id']) != '' ? $this->request->data['SoTerritoryReports']['region_office_id'] : $region_office_id;
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
			
			$office_id = isset($this->request->data['SoTerritoryReports']['office_id']) != '' ? $this->request->data['SoTerritoryReports']['office_id'] : $office_id;
			$this->set(compact('office_id'));
			
			$territory_id = isset($this->request->data['SoTerritoryReports']['territory_id']) != '' ? $this->request->data['SoTerritoryReports']['territory_id'] : 0;
			$this->set(compact('territory_id'));
			
			$so_id = isset($this->request->data['SoTerritoryReports']['so_id']) != '' ? $this->request->data['SoTerritoryReports']['so_id'] : 0;
			$this->set(compact('so_id'));
			
			
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
					//'User.user_group_id' => 4,
					'User.user_group_id' => array(4,1008),
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
			
			$outlet_category_id = isset($this->request->data['SoTerritoryReports']['outlet_category_id']) != '' ? $this->request->data['SoTerritoryReports']['outlet_category_id'] : 0;
									
						
			//For Query Conditon
			$conditions = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				//'Memo.gross_value >' => 0,
				'Memo.status !=' => 0,
				//'Outlet.is_active' => 1,
				//'Market.is_active' => 1,
			);
			
			
			if($office_ids)$conditions['Memo.office_id'] = $office_ids;
			if($office_id)$conditions['Memo.office_id'] = $office_id;	
						
			if($type=='so'){
				if($so_id)$conditions['Memo.sales_person_id'] = $so_id;
			}else{
				if($territory_id)$conditions['Memo.territory_id'] = $territory_id;
			}
			
			
			/*$product_category_ids = isset($this->request->data['SoTerritoryReports']['product_category_id']) != '' ? $this->request->data['SoTerritoryReports']['product_category_id'] : 0;*/
			
			
			//pr($conditions);
			//exit;

			$q_results = $this->Memo->find('all', array(
				'conditions'=> $conditions,
				'joins' => array(
					array(
						'alias' => 'MemoDetail',
						'table' => 'memo_details',
						'type' => 'INNER',
						'conditions' => 'Memo.id = MemoDetail.memo_id'
					),
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'Memo.territory_id = Territory.id'
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
					)
				),
								
				'fields' => array('COUNT(DISTINCT(Memo.outlet_id)) as total_visited', 'Outlet.category_id', 'Thana.id', 'Thana.name', 'Market.id', 'Market.name'),
				
				'group' => array('Outlet.category_id', 'Thana.id', 'Thana.name', 'Market.id', 'Market.name'),
				
				//'order' => array('Product.order asc', 'Memo.memo_date asc'),
				
				//'order' => $order,
				
				'recursive' => -1,
				//'limit' => 200
			));	
			
			//pr($q_results);
			//exit;
			
			
			
			
			$results = array();
			foreach($q_results as $result)
			{
				$results[$result['Thana']['name']][$result['Market']['name']][$result['Outlet']['category_id']] = 
				array(
					'total_visited' 		=> $result[0]['total_visited'],
					'outlet_category_id' 	=> $result['Outlet']['category_id'],				
					'market_id' 			=> $result['Market']['id'],
					'thana_id' 				=> $result['Thana']['id']
				);
				
				
			}
			
			//pr($results);
			//exit;
			
			
			
			
			//For Non Visited
			$visited_outlet_results = $this->Memo->find('all', array(
				'conditions'=> $conditions,
				'joins' => array(
					array(
						'alias' => 'MemoDetail',
						'table' => 'memo_details',
						'type' => 'INNER',
						'conditions' => 'Memo.id = MemoDetail.memo_id'
					),
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'Memo.territory_id = Territory.id'
					)
				),
								
				'fields' => array('Memo.outlet_id'),
				
				'group' => array('Memo.outlet_id'),
				
				//'order' => array('Product.order asc', 'Memo.memo_date asc'),
				
				//'order' => $order,
				
				'recursive' => -1,
				//'limit' => 200
			));	
			
			//pr($visited_outlet_results);
			//exit;
			
			$outlet_ids = array();
			foreach($visited_outlet_results as $q_result)
			{
				array_push($outlet_ids, $q_result['Memo']['outlet_id']);
			}
			
			//non-visited outlet lists
			$con = array();
			//$con['Outlet.is_active'] = 1;
			//$con['Market.is_active'] = 1;
			if($office_ids)$con['Territory.office_id'] = $office_ids;
			if($office_id)$con['Territory.office_id'] = $office_id;	
			if($type=='so'){
				if($so_id)$con['SalesPeople.id'] = $so_id;
			}else{
				if($territory_id)$con['Territory.id'] = $territory_id;
			}			
			if($outlet_ids)$con['NOT'] = array( "Outlet.id" => $outlet_ids);
			
			//pr($con);
			//exit;
			
			$non_visited_query_results = $this->Outlet->find('all', array(
				'conditions'=> $con,
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
					),
				),
				'fields' => array('COUNT(DISTINCT(Outlet.id)) as total_non_visited', 'Outlet.category_id', 'Thana.id', 'Thana.name', 'Market.id',  'Market.name'),
				'group' => array('Outlet.category_id', 'Thana.id', 'Thana.name', 'Market.id', 'Market.name'),
				
				//'order' => array( 'Market.name asc', 'Thana.name asc', 'Outlet.name'),
				//'limit' => 3000,
				'recursive' => -1
				
			));	
			
			//pr($non_visited_query_results);
			
			$non_visited_results = array();
			foreach($non_visited_query_results as $result)
			{
				/*$non_visited_results[$result['Thana']['name']][$result['Market']['name']][$result['Outlet']['category_id']] = 
				array(
					'total_non_visited' 	=> $result[0]['total_non_visited'],
					'outlet_category_id' 	=> $result['Outlet']['category_id'],				
					'market_id' 			=> $result['Market']['id'],
					'thana_id' 				=> $result['Thana']['id']
				);*/
				
				$results[$result['Thana']['name']][$result['Market']['name']][$result['Outlet']['category_id']]['total_non_visited'] = $result[0]['total_non_visited'];
				
			}	
			
			$this->set(compact('results'));
			
			
			
			$output = '';
			$thana_grand_total_visited = array(); 
            $thana_grand_total_non_visited = array();
			foreach($results as $thana_name => $market_datas)
			{ 
                $i=1;
                $thana_sub_total_visited = array(); 
                $thana_sub_total_non_visited = array(); 
                foreach($market_datas as $market_name=>$outlet_category_datas)
				{
             
            	$output.= '<tr>';
				$t_name=$i==1?$thana_name:'';
                $output.= '<td style="text-align:left;"><b>'.$t_name.'</b></td>';
                $output.= '<td style="text-align:left;">'.$market_name.'</td>';
				
                foreach($outlet_categories as $outlet_category_id => $outlet_categorie)
				{
                $output.= '<td style="text-align:right;">'.@$outlet_category_datas[$outlet_category_id]['total_visited'].'</td>';
                $output.= '<td style="text-align:right;">'.@$outlet_category_datas[$outlet_category_id]['total_non_visited'].'</td>';
                
                @$thana_sub_total_visited[$outlet_category_id]+= $outlet_category_datas[$outlet_category_id]['total_visited'];
                @$thana_sub_total_non_visited[$outlet_category_id]+= $outlet_category_datas[$outlet_category_id]['total_non_visited'];
				
				
				
                } 
				
				
                
                $output.= '</tr>';
                $i++; 
				}                 
                
                $output.= '<tr style="font-weight:bold; background:#f7f7f7;">
                <td colspan="2" style="text-align:right;">Sub Total :</td>';
                 
                //pr($thana_sub_total_visited);
                foreach($outlet_categories as $outlet_category_id => $outlet_categorie)
				{ 
                $output.= '<td style="text-align:right;">'.@$thana_sub_total_visited[$outlet_category_id].'</td>';
                $output.= '<td style="text-align:right;">'.@$thana_sub_total_non_visited[$outlet_category_id].'</td>';
				
				@$thana_grand_total_visited[$outlet_category_id]+= $thana_sub_total_visited[$outlet_category_id];
                @$thana_grand_total_non_visited[$outlet_category_id]+= $thana_sub_total_non_visited[$outlet_category_id];
				
                }
                $output.= '</tr>';
            }
			
			
			$output.= '<tr style="font-weight:bold; background:#f7f7f7;">
			<td colspan="2" style="text-align:right;">Grand Total :</td>';
			 
			//pr($thana_sub_total_visited);
			foreach($outlet_categories as $outlet_category_id => $outlet_categorie)
			{ 
			$output.= '<td style="text-align:right;">'.@$thana_grand_total_visited[$outlet_category_id].'</td>';
			$output.= '<td style="text-align:right;">'.@$thana_grand_total_non_visited[$outlet_category_id].'</td>';
			}
			$output.= '</tr>';
			
			
			$this->set(compact('output'));		
			
						
		}
		
				
		$this->set(compact('offices', 'territories', 'outlet_type', 'region_offices', 'office_id', 'request_data', 'so_list'));


    }



}
