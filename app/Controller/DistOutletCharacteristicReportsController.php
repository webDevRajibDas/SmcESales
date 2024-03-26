<?php
App::uses('AppController', 'Controller');
/**
 * OutletCharacteristicSettings Controller
 *
 * @property OutletCharacteristicReport $OutletCharacteristicReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistOutletCharacteristicReportsController extends AppController {
	
	

/**
 * Components
 *
 * @var array
 */
 
	public $uses = array('DistMemo', 'Office', 'DistRoute', 'DistMarket', 'DistOutletCategory', 'DistOutlet', 'DistSalesRepresentative', 'Product','DistRouteMappingHistory');
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
				
		
		$this->set('page_title', "Distributor Outlet Characteristics Report");
		
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
			'conditions' => array('Office.office_type_id'=>3), 
			'order' => array('office_name' => 'asc')
		));
		
		//report type
		$report_types = array(
			'visited' => 'Visited Outlet Information',
			'non_visited' => 'Non-Visited Outlet',
			'detail' => 'Outlet Wise Sales Detail',
			'summary' => 'Outlet Wise Sales Summary'
		);
		$this->set(compact('report_types'));
		
		
		//for outlet category list
		$outlet_categories = $this->DistOutletCategory->find('list', array(
			'conditions' => array('is_active'=>1), 
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
				'fields'=> array('Product.id', 'Product.sales_measurement_unit_id'),
				'order'=>  array('order'=>'asc'),
				'recursive'=> -1
			));
		$this->set(compact('product_measurement'));
		
		
		
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
			
			$date_from = date('Y-m-d', strtotime($request_data['DistOutletCharacteristicReports']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['DistOutletCharacteristicReports']['date_to']));
			$this->set(compact('date_from', 'date_to'));
			
			$report_type = $this->request->data['DistOutletCharacteristicReports']['report_type'];
			$this->set(compact('report_type'));
			
			$region_office_id = isset($this->request->data['DistOutletCharacteristicReports']['region_office_id']) != '' ? $this->request->data['DistOutletCharacteristicReports']['region_office_id'] : $region_office_id;
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
			
			$office_id = isset($this->request->data['DistOutletCharacteristicReports']['office_id']) != '' ? $this->request->data['DistOutletCharacteristicReports']['office_id'] : $office_id;
			$this->set(compact('office_id'));
			$db_id = isset($this->request->data['DistOutletCharacteristicReports']['db_id']) != '' ? $this->request->data['DistOutletCharacteristicReports']['db_id'] : 0;
			
			$this->set(compact('db_id'));
			$unit_type = $this->request->data['DistOutletCharacteristicReports']['unit_type'];
			//$this->set(compact('unit_type'));
			$unit_type_text = ($unit_type==2)?'Base':'Sales';
			$this->set(compact('unit_type_text'));
			
			$route_id = isset($this->request->data['DistOutletCharacteristicReports']['route_id']) != '' ? $this->request->data['DistOutletCharacteristicReports']['route_id'] : 0;
			
			$market_id = isset($this->request->data['DistOutletCharacteristicReports']['market_id']) != '' ? $this->request->data['DistOutletCharacteristicReports']['market_id'] : 0;
			
			$outlet_id = isset($this->request->data['DistOutletCharacteristicReports']['outlet_id']) != '' ? $this->request->data['DistOutletCharacteristicReports']['outlet_id'] : 0;
			
			$outlet_category_id = isset($this->request->data['DistOutletCharacteristicReports']['outlet_category_id']) != '' ? $this->request->data['DistOutletCharacteristicReports']['outlet_category_id'] : 0;
			
			
			//START DATA QUERY
			if($report_type=='visited')
			{
				$conditions = array();
				
				$conditions = array(
					'DistMemo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					//'Memo.gross_value >' => 0,
					'DistMemo.status !=' => 0,
				);
				if($outlet_id)
				{
					$conditions['DistMemo.outlet_id'] = $outlet_id;
				}
				else
				{
					if($office_ids)$conditions['DistMemo.office_id'] = $office_ids;
					if($office_id)$conditions['DistMemo.office_id'] = $office_id;	
				
					
					if($route_id)$conditions['DistMemo.dist_route_id'] = $route_id;
					if($db_id)$conditions['DistMemo.distributor_id'] = $db_id;
					if($market_id)$conditions['DistMemo.market_id'] = $market_id;
					/*if($outlet_id)$conditions['Memo.outlet_id'] = $outlet_id;*/
					
					if($outlet_category_id)$conditions['DistOutlet.category_id'] = $outlet_category_id;
				}
				
				//market wise total outlets
				$m_o_con = array(
						'DistOutlet.is_active' => 1,
					);
				if($outlet_category_id)$m_o_con['DistOutlet.category_id'] = $outlet_category_id;
				$total_outlets_market_wise_results = $this->DistOutlet->find('all', 
				array(
				'conditions' => $m_o_con,
				'joins' => array(
					array(
						'table'=> 'dist_markets',
						'alias'=> 'DistMarket',
						'conditions'=>'DistMarket.id=DistOutlet.dist_market_id'
						)
					),
				'fields' => array('count(DISTINCT DistOutlet.id) as total_outlet', 'DistOutlet.dist_market_id', 'DistMarket.name'),
				'group' => array('DistOutlet.dist_market_id', 'DistMarket.name'),
				'recursive'=>-1
				));
				$total_outlets_market_wise = array();
				foreach($total_outlets_market_wise_results as $market_wise_result){
					$total_outlets_market_wise[$market_wise_result['DistMarket']['name']]=$market_wise_result[0]['total_outlet'];
				}
				//pr($total_outlets_market_wise);
				//exit;
				
				//thana wise total outlets
				$total_outlets_thana_wise_results = $this->DistOutlet->find('all', array(
				'joins' => array(
						array(
							'alias' => 'DistMarket',
							'table' => 'dist_markets',
							'type' => 'INNER',
							'conditions' => 'DistOutlet.dist_market_id = DistMarket.id'
						),
						array(
							'alias' => 'DistRoute',
							'table' => 'dist_routes',
							'type' => 'INNER',
							'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
						)
					),
				'conditions' => $m_o_con,
				'fields' => array('count(DISTINCT DistOutlet.id) as total_outlet', 'DistMarket.dist_route_id', 'DistRoute.name'),
				'group' => array('DistMarket.dist_route_id', 'DistRoute.name'),
				'recursive' => -1
				));
				$total_outlets_thana_wise = array();
				foreach($total_outlets_thana_wise_results as $thana_wise_result){
					$total_outlets_thana_wise[$thana_wise_result['DistRoute']['name']]=$thana_wise_result[0]['total_outlet'];
				}
				//pr($total_outlets_thana_wise);
				//exit;
				
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
						array(
							'alias' => 'DistOutlet',
							'table' => 'dist_outlets',
							'type' => 'INNER',
							'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
						),
					),
					'fields' => array('count(DistMemo.outlet_id) as total_outlet', 'sum(DistMemo.gross_value) as memo_total',  'DistMemo.outlet_id', 'DistMemo.memo_date', 'DistOutlet.name', 'DistOutlet.category_id', 'DistSalesRrepresentatives.name', 'DistMarket.name', 'DistRoute.name'),
					
					'group' => array('DistMemo.memo_date', 'DistMemo.outlet_id', 'DistOutlet.name', 'DistOutlet.category_id', 'DistSalesRrepresentatives.name', 'DistMarket.name', 'DistRoute.name'),
					
					'order' => array('DistRoute.name asc', 'DistMarket.name asc', 'DistOutlet.name'),
					
					'recursive' => -1
				));	
				
				//pr($q_results);
				//exit;
				
				$results = array();
				foreach($q_results as $q_result)
				{
					$results[$q_result['DistRoute']['name']][$q_result['DistMarket']['name']][$q_result['DistOutlet']['name']][] 
					= array(
						'total_outlet'			=> $q_result[0]['total_outlet'],
						'memo_total'			=> $q_result[0]['memo_total'],
						'memo_date'				=> $q_result['DistMemo']['memo_date'],
						'outlet_id'				=> $q_result['DistMemo']['outlet_id'],
						'outlet_category_id'	=> $q_result['DistOutlet']['category_id'],
						'so_name'				=> $q_result['DistSalesRrepresentatives']['name'],
					);
				}
				
				$this->set(compact('results'));
				
				$output = '';
				foreach($results as $route_name => $market_data)
				{
					$route_total = 0;
					$output.= '<tr>
					  <td style="text-align:left;" colspan="6"><b>Route :- '.$route_name.'</b></td>
					</tr>';
						
						$route_total_visited_outlets = 0;
						
						foreach($market_data as $market_name => $outlet_data)
						{
							
							$market_total = 0;		
							$output.= '<tr>
							  <td style="text-align:left;" colspan="6"><b>Market :- '.$market_name.'</b></td>
							</tr>
							<tr>
								</tr>';
								
								
								foreach($outlet_data as $outlet_name => $memo_data)
								{
									$market_total_visited_outlets = 0;
									$memo_date = '';
									$memo_total = 0;
									$to = count($memo_data);
									$i=1;
									foreach($memo_data as $m_result)
									{
										$so_name =  $m_result['so_name'];
										if($i==$to){
											$memo_date.= date('d-m-Y', strtotime($m_result['memo_date']));
										}else{
											$memo_date.= date('d-m-Y', strtotime($m_result['memo_date'])).', ';
										}
										
										$outlet_category_id = $m_result['outlet_category_id'];
										
										$memo_total+=$m_result['memo_total'];
										
										$i++;
									}
									
									$output.= '<tr>
									  <td style="text-align:left;">'.$outlet_name.'</td>
									  <td style="text-align:left;">'.$outlet_categories[$outlet_category_id].'</td>
									  <td>'.count($memo_data).'</td>
									  <td>'.$memo_date.'</td>
									  <td style="text-align:right;">'.sprintf("%01.2f", $memo_total).'</td>
									  <td style="text-align:left;">'.$so_name.'</td>
									</tr>';
									
									$market_total+= $memo_total;
									$market_total_visited_outlets+=count($outlet_data);
								} 
							
							   
							$output.= '<tr>
							  <td style="text-align:right;" colspan="4"><span style="float:left;"><b>Market Wise Summary :- Total Outlet: '.$total_outlets_market_wise[$market_name].', Visited Outlets: '.$market_total_visited_outlets.'</b></span> <span style="float:right;"><b>Memo Total:</b></span></td>
							  <td style="text-align:right;"><b>'.sprintf("%01.2f", $market_total).'</b></td>
							  <td colspan="3"></td>
							</tr>';
							$route_total+=$market_total;
							
							$route_total_visited_outlets+= $market_total_visited_outlets;
						}
					
						$output.= '<tr>
						  <td style="text-align:right;" colspan="4">
						  <span style="float:left;"><b>Route Wise Summary :- Total Outlet: '.$total_outlets_thana_wise[$route_name].', Visited Outlets: '.$route_total_visited_outlets.'</span> <span style="float:right;"><b>Memo Total:</b></span>
						  </td>
						  <td style="text-align:right;"><b>'.sprintf("%01.2f", $route_total).'</b></td>
						  <td colspan="3"></td>
						</tr>';
				}
				$this->set(compact('output'));				
			}

			
			if($report_type=='non_visited')
			{
				$conditions = array();
				
				$conditions = array(
					'DistMemo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					//'Memo.gross_value >' => 0,
					'DistMemo.status !=' => 0,
				);
				
				if($outlet_id)
				{
					$conditions['DistMemo.outlet_id'] = $outlet_id;
				}
				else
				{
					if($office_ids)$conditions['DistMemo.office_id'] = $office_ids;
					if($office_id)$conditions['DistMemo.office_id'] = $office_id;	
				
					if($route_id)$conditions['DistMemo.dist_route_id'] = $route_id;
					if($db_id)$conditions['DistMemo.distributor_id'] = $db_id;
					if($market_id)$conditions['DistMemo.market_id'] = $market_id;
					
					if($outlet_category_id)$conditions['DistOutlet.category_id'] = $outlet_category_id;
				}
								
				
				/*pr($conditions);
				exit;*/
				
				//market wise total outlets
				//$outlet_category_id
				$o_con = array(
						'DistOutlet.is_active' => 1,
					);
				if($outlet_category_id)$o_con['DistOutlet.category_id'] = $outlet_category_id;
				$total_outlets_market_wise_results = $this->DistOutlet->find('all', 
					array(
						'conditions' => $o_con,
						'joins' => array(
							array(
								'table'=> 'dist_markets',
								'alias'=> 'DistMarket',
								'conditions'=>'DistMarket.id=DistOutlet.dist_market_id'
								)
						),
						'fields' => array('count(DISTINCT DistOutlet.id) as total_outlet', 'DistOutlet.dist_market_id', 'DistMarket.name'),
						'group' => array('DistOutlet.dist_market_id', 'DistMarket.name'),
						'recursive'=>-1
					)
				);
				$total_outlets_market_wise = array();
				foreach($total_outlets_market_wise_results as $market_wise_result){
					$total_outlets_market_wise[$market_wise_result['DistMarket']['name']]=$market_wise_result[0]['total_outlet'];
				}
								
				
				$q_results = $this->DistMemo->find('all', array(
					'conditions'=> $conditions,
					'joins' => array(
						
						array(
							'alias' => 'DistOutlet',
							'table' => 'outlets',
							'type' => 'INNER',
							'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
						)
					),
					'fields' => array('DISTINCT DistMemo.outlet_id'),
					'group' => array('DistMemo.outlet_id'),
					'recursive' => -1
				));	
				$outlet_ids = array();
				foreach($q_results as $q_result)
				{
					array_push($outlet_ids, $q_result['DistMemo']['outlet_id']);
				}
				/*pr($outlet_ids);
				exit;*/
				
				//outlet lists
				$con = array();
				if($office_ids)$con['DistRoute.office_id'] = $office_ids;
				if($office_id)$con['DistRoute.office_id'] = $office_id;	
			
				if($db_id)$con['DistRouteMappingHistory.dist_distributor_id'] = $db_id;
				if($route_id)$con['DistMarket.dist_route_id'] = $route_id;
				if($market_id)$con['DistOutlet.dist_market_id'] = $market_id;
				
				if($outlet_category_id)$con['DistOutlet.category_id'] = $outlet_category_id;
				if($outlet_ids)$con['NOT'] = array( "DistOutlet.id" => $outlet_ids);
				if($outlet_id)$con['DistOutlet.id'] = $outlet_id;
				
				//pr($con);
				//exit;
				
				$o_results = $this->DistOutlet->find('all', array(
					'conditions'=> $con,
					'joins' => array(
						array(
							'alias' => 'DistMarket',
							'table' => 'dist_markets',
							'type' => 'INNER',
							'conditions' => 'DistOutlet.dist_market_id = DistMarket.id'
						),
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
					'fields' => array('DistOutlet.id', 'DistOutlet.name', 'DistOutlet.category_id',  'DistMarket.name', 'DistRoute.name'),
					'group' => array('DistOutlet.id', 'DistOutlet.name', 'DistOutlet.category_id',  'DistMarket.name', 'DistRoute.name'),
					
					'order' => array( 'DistMarket.name asc', 'DistRoute.name asc', 'DistOutlet.name'),
					'recursive' => -1
					
				));	
				
				
				$results = array();
				foreach($o_results as $o_result){
					$results[$o_result['DistMarket']['name']][$o_result['DistOutlet']['category_id']][] = array(
						'outlet_name'			=> $o_result['DistOutlet']['name'],
						'outlet_category_id'	=> $o_result['DistOutlet']['category_id'],
						'market_name'			=> $o_result['DistMarket']['name'],
						'route_name'			=> $o_result['DistRoute']['name'],
					);
				}
				
				/*pr($results);
				exit;*/
				
				$this->set(compact('results'));
				
				$output = '';
				
				foreach($results as $market_name => $outlet_category_datas)
				{ 
                    $total_market_outlets = 0;
					foreach($outlet_category_datas as $outlet_category_id => $outlet_datas)
					{
						$total_market_outlets+=count($outlet_datas);
					}
					
					$output .= '<tr>
									<td colspan="3" style="font-weight:bold; text-align:left;">'
										.$market_name.' - '.$outlet_datas[0]['route_name'].' - (Non Visited Outlets : '.$total_market_outlets.', Total Outlets: '.$total_outlets_market_wise[$market_name].')<br>
									</td>
								</tr>';
					
                    foreach($outlet_category_datas as $outlet_category_id => $outlet_datas)
					{ 
						 $i=1;
						 $total_outlets = count($outlet_datas);
						 $output .= '<tr>
									<td colspan="3" style="font-weight:bold; text-align:left;">'
										.$outlet_categories[$outlet_category_id].' Outlet List - '.$total_outlets.'
									</td>
								</tr>';
						 foreach($outlet_datas as $outlet_data)
						 {
							  
							  
							  $output .= '<tr>
							  <td></td>
							  <td style="text-align:left;">'.@$outlet_data['outlet_name'].'</td>
							  <td style="text-align:left;">'.$outlet_categories[$outlet_category_id].'</td>
							  </tr>';
						  $i++; 
						  
						 }
                    
					}
                    
                }
				$this->set(compact('output'));
				//echo ($output);
				//exit;
			}
			
			
			if($report_type=='detail')
			{
				$conditions = array();
				
				$conditions = array(
					'DistMemo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					'DistMemo.gross_value >' => 0,
					'DistMemo.status !=' => 0,
					'DistMemoDetail.price >' => 0,
				);
				if($outlet_id)
				{
					$conditions['DistMemo.outlet_id'] = $outlet_id;
				}
				else
				{
					if($office_ids)$conditions['DistMemo.office_id'] = $office_ids;
					if($office_id)$conditions['DistMemo.office_id'] = $office_id;	
					
					
					if($route_id)$conditions['DistMemo.dist_route_id'] = $route_id;
					if($db_id)$conditions['DistMemo.distributor_id'] = $db_id;
					if($market_id)$conditions['DistMemo.market_id'] = $market_id;
					
					
					if($outlet_category_id)$conditions['DistOutlet.category_id'] = $outlet_category_id;
				}
								
				
				/*pr($conditions);
				exit;*/
	
				$q_results = $this->DistMemo->find('all', array(
					'conditions'=> $conditions,
					'joins' => array(
						array(
							'alias' => 'DistMemoDetail',
							'table' => 'dist_memo_details',
							'type' => 'INNER',
							'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
						),
						array(
							'alias' => 'Product',
							'table' => 'products',
							'type' => 'INNER',
							'conditions' => 'DistMemoDetail.product_id = Product.id'
						),
						array(
							'alias' => 'DistSalesRrepresentatives',
							'table' => 'dist_sales_representatives',
							'type' => 'INNER',
							'conditions' => 'DistMemo.sr_id = DistSalesRrepresentatives.id'
						),
						array(
							'alias' => 'DistOutlet',
							'table' => 'dist_outlets',
							'type' => 'INNER',
							'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
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
					
					'fields' => array('DistMemo.id', 'DistMemo.dist_memo_no', 'DistSalesRrepresentatives.name', 'DistMemo.memo_date', 'DistMemo.gross_value', 'DistOutlet.category_id', 'DistMemoDetail.product_id', 'DistMemoDetail.sales_qty', 'DistMemoDetail.price', 'Product.order', 'Product.name', 'DistOutlet.name', 'DistMarket.name', 'DistRoute.name'),
					'group' => array('DistMemo.id', 'DistMemo.dist_memo_no', 'DistSalesRrepresentatives.name', 'DistMemo.memo_date', 'DistMemo.gross_value', 'DistOutlet.category_id', 'DistMemoDetail.product_id', 'DistMemoDetail.sales_qty', 'DistMemoDetail.price', 'Product.order', 'Product.name', 'DistOutlet.name', 'DistMarket.name', 'DistRoute.name'),
					
					'order' => array('DistMarket.name asc', 'DistOutlet.name asc', 'DistMemo.dist_memo_no asc',  'Product.order asc'),
					
					'recursive' => -1
				));	
				
				/*pr($q_results);
				exit;*/
				
				$results = array();
				foreach($q_results as $result)
				{
					
					$sales_qty = ($unit_type==1)?$result['DistMemoDetail']['sales_qty']:$this->unit_convert($result['DistMemoDetail']['product_id'], $product_measurement[$result['DistMemoDetail']['product_id']], $result['DistMemoDetail']['sales_qty']);
					
					$results[$result['DistMarket']['name'].'-'.$result['DistRoute']['name']][$result['DistOutlet']['name']][$result['DistMemo']['dist_memo_no']][$result['DistMemoDetail']['product_id']] = array(
						'memo_no' 				=> $result['DistMemo']['dist_memo_no'],
						'memo_date' 			=> $result['DistMemo']['memo_date'],
						'gross_value' 			=> $result['DistMemo']['gross_value'],
						
						'product_id' 			=> $result['DistMemoDetail']['product_id'],
						'product_name' 			=> $result['Product']['name'],
						
						'product_sales_qty' 	=> sprintf("%01.2f", $sales_qty),
						
						'product_price' 		=> sprintf("%01.2f", $result['DistMemoDetail']['price']*$result['DistMemoDetail']['sales_qty']),
						
						'outlet_name' 			=> $result['DistOutlet']['name'],
						
						'so_name' 				=> $result['DistSalesRrepresentatives']['name'],
						
						'market_name' 			=> $result['DistMarket']['name'],
						'route_name' 			=> $result['DistRoute']['name']
					);
				}
				
				$this->set(compact('results'));
				
				
				//for output
				$output = '';
				
				$grand_total = 0;
				foreach($results as $market_name => $outlet_datas)
				{ 
				
				$output.= '<tr>
					  <td style="text-align:left; font-size:15px;" colspan="7"><b>Market :- '.$market_name.'</b></td>
					</tr>';
				
					
					$market_total = 0;
					foreach($outlet_datas as $outlet_name => $memo_datas)
					{ 
					
						$outlet_total = 0;
						foreach($memo_datas as $memo_no => $memo_products){ 
						 
							$memo_total = 0;
							$i=1;
							foreach($memo_products as $memo_product)
							{ 
							$memo_total+= $memo_product['product_price'];
							
							$outlet_nam=$i==1?$outlet_name:'';
							$memo_no=$i==1?$memo_no:'';
							$memo_date=$i==1?date('d-m-Y', strtotime($memo_product['memo_date'])):'';
							
							$output.= '<tr>
							  <td style="text-align:left;">'.$outlet_nam.'</td>
							  <td style="mso-number-format:\@;">'.$memo_no.'</td>
							  <td>'.$memo_date.'</td>
							  <td style="text-align:left;">'.@$memo_product['product_name'].'</td>
							  <td style="text-align:right;">'.@$memo_product['product_sales_qty'].'</td>
							  <td style="text-align:right;">'.@$memo_product['product_price'].'</td>
							  <td style="text-align:left;">'.@$memo_product['so_name'].'</td>
							</tr>';
							 
							$i++;
							} 
							$outlet_total+= $memo_total;
							
							$output.= '<tr>
							  <td style="text-align:right;" colspan="5"><b>Memo Total :</b></td>
							  <td style="text-align:right;"><b>'.sprintf("%01.2f", $memo_total).'</b></td>
							  <td colspan="3"></td>
							</tr>';
						} 
						
						$output.= '<tr style="background:#f7f7f7">
						  <td style="text-align:right;" colspan="5"><b>Outlet Wise Memo Total :</b></td>
						  <td style="text-align:right;"><b>'.sprintf("%01.2f", $outlet_total).'</b></td>
						  <td colspan="3"></td>
						</tr>';
					
					$market_total+= $outlet_total;
					} 
					
				
						$output.= '<tr style="background:#ccc">
						  <td style="text-align:right;" colspan="5"><b>Market Wise Memo Total :</b></td>
						  <td style="text-align:right;"><b>'.sprintf("%01.2f", $market_total).'</b></td>
						  <td colspan="3"></td>
						</tr>';
				
				$grand_total+= $market_total;
				} 
				
				
				$output.= '<tr style="background:#f7f7f7">
				  <td style="text-align:right;" colspan="5"><b>Grand Total :</b></td>
				  <td style="text-align:right;"><b>'.sprintf("%01.2f", $grand_total).'</b></td>
				  <td colspan="3"></td>
				</tr>';
				
				$this->set(compact('output'));
				
				//echo ($output);
				//exit;
			}
			
			
			if($report_type=='summary')
			{
				
				//products
				$product_list = $this->Product->find('list', array(
							'conditions'=> array(
								'NOT' => array('Product.product_category_id'=>32),
								'is_active' => 1,
								'Product.product_type_id' => 1,
								'Product.is_distributor_product'=>1
							),
							'order'=>  array('order'=>'asc')
				));
				$this->set(compact('product_list'));
				
								
				$conditions = array(
					'DistMemo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					'DistMemo.gross_value >' => 0,
					'DistMemo.status !=' => 0,
                    'DistMemoDetail.price >' => 0,
				);
				
				if($outlet_id)
				{
					$conditions['DistMemo.outlet_id'] = $outlet_id;
				}
				else
				{
					if($office_ids)$conditions['DistMemo.office_id'] = $office_ids;
					if($office_id)$conditions['DistMemo.office_id'] = $office_id;	
					
					
					
					if($route_id)$conditions['DistMemo.dist_route_id'] = $route_id;
					if($db_id)$conditions['DistMemo.distributor_id'] = $db_id;
					if($market_id)$conditions['DistMemo.market_id'] = $market_id;
					
					
					if($outlet_category_id)$conditions['DistOutlet.category_id'] = $outlet_category_id;
				}
				
							
				
				/*pr($conditions);
				exit;*/
	
				$q_results = $this->DistMemo->find('all', array(
					'conditions'=> $conditions,
					'joins' => array(
						array(
							'alias' => 'DistMemoDetail',
							'table' => 'dist_memo_details',
							'type' => 'INNER',
							'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
						),
						array(
							'alias' => 'Product',
							'table' => 'products',
							'type' => 'INNER',
							'conditions' => 'DistMemoDetail.product_id = Product.id'
						),
						array(
							'alias' => 'DistSalesRrepresentatives',
							'table' => 'dist_sales_representatives',
							'type' => 'INNER',
							'conditions' => 'DistMemo.sr_id = DistSalesRrepresentatives.id'
						),
						array(
							'alias' => 'DistOutlet',
							'table' => 'dist_outlets',
							'type' => 'INNER',
							'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
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
							'conditions' => 'DistMemo.dist_route_id = DistRoute.id'
						),
						
					),
					
					'fields' => array('SUM(DistMemoDetail.sales_qty) as sales_qty', 'SUM(DistMemoDetail.sales_qty*DistMemoDetail.price) as price', 'DistMemoDetail.product_id',  'Product.name', 'DistOutlet.id', 'DistOutlet.name', 'DistMarket.id', 'DistMarket.name', 'DistRoute.id', 'DistRoute.name'),
				
				'group' => array('DistMemoDetail.product_id',  'Product.name', 'DistOutlet.id', 'DistOutlet.name', 'DistMarket.id', 'DistMarket.name', 'DistRoute.id', 'DistRoute.name'),
					
					'order' => array('DistMarket.name asc', 'DistOutlet.name asc'),
					
					'recursive' => -1
				));	
				
				// pr($q_results);
				// exit;
				
				$results = array();
				foreach($q_results as $result)
				{
					$sales_qty = ($unit_type==1)?$result[0]['sales_qty']:$this->unit_convert($result['DistMemoDetail']['product_id'], $product_measurement[$result['DistMemoDetail']['product_id']], $result[0]['sales_qty']);
					
					$results[$result['DistMarket']['name'].'-'.$result['DistRoute']['name']][$result['DistOutlet']['id']][$result['DistOutlet']['name']][$result['DistMemoDetail']['product_id']] = array(
						
						'product_id' 			=> $result['DistMemoDetail']['product_id'],
						
						'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
						'price' 				=> $result[0]['price'],
						
					);
				}
				$this->set(compact('results'));
				
				/*pr($results);
				exit;*/
				
				$output = '';
				
				$grand_total = array();
				foreach($results as $market_name => $outlet_id){ 
				
				$output.= '<tr>
				  <td style="text-align:left; font-size:12px;" colspan="'.count($product_list).'"><b>Market :- '.$market_name.'</b></td>
				</tr>';
				
					 
					$sub_total = array(); 
					foreach($outlet_id as $outlet_datas)
					{
						foreach($outlet_datas as $outlet_name => $pro_datas){ 
						
						$output.= '<tr>
						  <td style="text-align:left;">'.$outlet_name.'</td>';
						  
						  foreach($product_list as $product_id => $pro_name)
						  { 
						  
						  $output.= '<td style="text-align:left;">'.@$pro_datas[$product_id]['sales_qty'].'</td>';
						 
						  @$sub_total[$product_id]+= $pro_datas[$product_id]['sales_qty']?$pro_datas[$product_id]['sales_qty']:0;
						  } 
						  
						 $output.= '</tr>';
						}
					}
				
				 $output.= '<tr style="font-weight:bold; background:#f2f2f2;">
				  <td>Sub Total</td>';
				  foreach($product_list as $product_id => $pro_name){ 
				  $output.= '<td>'.sprintf("%01.2f", $sub_total[$product_id]).'</td>';
				  
					@$grand_total[$product_id]+=$sub_total[$product_id];
				  } 
				  
				$output.= '</tr>';
				
				} 
				
				$output.= '<tr style="font-weight:bold; background:#ccc;">
				  <td>Grand Total</td>';
				  foreach($product_list as $product_id => $pro_name){ 
				  $output.= '<td>'.sprintf("%01.2f", $grand_total[$product_id]).'</td>';
				}
				$output.= '</tr>';
				$this->set(compact('output'));
			}			
						
		}
		
				
				
		$this->set(compact('offices', 'territories', 'outlet_type', 'region_offices', 'districts', 'thanas', 'markets', 'outlets', 'office_id', 'request_data', 'so_list'));
		
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
	//get route list
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
  							<input type="hidden" name="data[DistOutletCharacteristicReports][route_id]" value="" id="route_id"/>';
							foreach($route_list as $key => $val)
							{
								$output.= '<div class="checkbox">
									<input type="checkbox" onClick="marketBoxList()" name="data[DistOutletCharacteristicReports][route_id][]" value="'.$key.'" id="route_id'.$key.'" />
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
  							<input type="hidden" name="data[DistOutletCharacteristicReports][market_id]" value="" id="market_id"/>';
							foreach($results as $key => $val)
							{
								$output.= '<div class="checkbox">
									<input type="checkbox" onclick="outletBoxList()" name="data[DistOutletCharacteristicReports][market_id][]" value="'.$key.'" id="market_id'.$key.'" />
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
	
	
	//get outlet list
	public function get_outlet_list()
	{
		$view = new View($this);
		
        $form = $view->loadHelper('Form');	
		
	    $market_id = $this->request->data['market_id'];
		
		$conditions = array();
		
		if($market_id)
		{
			$ids = explode(',', $market_id);
			$conditions['DistOutlet.dist_market_id'] = $ids;
			$conditions['DistOutlet.is_active'] = 1;
			//pr($conditions);
			//exit;
			
			$results = $this->DistOutlet->find('list', array(
				'conditions'=> $conditions,
				'order'=>  array('DistOutlet.name'=>'asc')
			));
			
			
			if($results)
			{	
				$output = '<div class="input select">
  							<input type="hidden" name="data[DistOutletCharacteristicReports][outlet_id]" value="" id="outlet_id"/>';
							foreach($results as $key => $val)
							{
								$output.= '<div class="checkbox">
									<input type="checkbox" name="data[DistOutletCharacteristicReports][outlet_id][]" value="'.$key.'" id="outlet_id'.$key.'" />
									<label for="outlet_id'.$key.'">'.$val.'</label>
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
