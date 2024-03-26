<?php
App::uses('AppController', 'Controller');
/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
class SrRevenueReportsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $uses = array('Memo', 'Thana', 'Market', 'SalesPerson', 'Outlet', 'Product', 'MeasurementUnit', 'ProductPrice', 'ProductCombination', 'Combination', 'MemoDetail', 'MeasurementUnit', 'ProductCategory', 'TerritoryAssignHistory', 'Office', 'Territory', 'ProductType', 'DistAreaExecutive', 'DistTso', 'DistTsoMapping','DistMemo','DistOrder');
    public $components = array('Paginator', 'Session', 'Filter.Filter');
	
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {

		
		ini_set('memory_limit', '2048M');
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes

		$user_group_id=$this->Session->read('Office.group_id');
		$office_parent_id = $this->UserAuth->getOfficeParentId();

		ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600); 
        
        $this->set('page_title', 'SR Revenue Report');
		
		//for region office list
        $region_offices = $this->Office->find('list', array(
            'conditions' => array('Office.office_type_id'=>3),
            'order' => array('order' => 'asc')
        ));

		$report_for = [1 =>'Memo', 2 => 'Order'];
		
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

	
		if($this->request->is('post'))
		{
		 	$request_data = $this->request->data;
			
			// pr($request_data);exit;

			$report_for_array = $request_data['DistRevenueReport']['report_for'];
			$report_for_flag=0; //initial value
			
			if($report_for_array){
				$count_array = count($report_for_array);
				if($count_array==2){

					$report_for_flag=3; //show both Distmemo and Dist Order EC & Revenue

				}else{

					if(in_array(1,$report_for_array)){
						$report_for_flag=1; //show Distmemo EC & Revenue
					}else{
						$report_for_flag=2; //show Distorder EC & Revenue
					}

				}
				

			}else{
				
				$report_for_flag=0; //show both Distmemo and Dist Order EC & Revenue
			}

			if($report_for_flag==0){
				
					$this->Session->setFlash(__('Please Select Atleast One Report Type.'), 'flash/error');
					$url = 'index';
					$this->redirect(array('action' => $url));
				
			}
			// echo $report_for;exit;
			
			

			

			if(empty($request_data['DistRevenueReport']['office_id'])){
				$index_area_office_show=0;
			}else{
				$index_area_office_show=1;
			}
			// echo $index_area_office_show;exit;
			$this->set(compact('index_area_office_show'));
	
			$month_year=explode("-",$request_data['DistRevenueReport']['date_from']);
			$month = $month_year[0];
			$year = $month_year[1];

			$last_date_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

			$datelist=array();
			for($day=1; $day<=$last_date_month; $day++)
			{
				$time=mktime(12, 0, 0, $month, $day, $year);
				if (date($month, $time)==date($month))
					$datelist[]=date('Y-m-d', $time);
			}
			// echo "<pre>";
			// print_r($datelist);
			// echo "</pre>";

			$date_from=$datelist[0]; //first day of month
			$date_to=$datelist[count($datelist)-1]; //last day of month
			
			
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

			@$date_from = date('Y-m-d', strtotime($date_from));
			@$date_to = date('Y-m-d', strtotime($date_to));
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
			// $db_ids = array();
			if($tso_id)
			{
				$this->loadModel('DistTsoMappingHistory');
				$srs = $this->DistTsoMappingHistory->find('list', array(
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
							),
							array(
								'table'=>'dist_sales_representatives',
								'alias'=>'SR',
								'type' => 'LEFT',
								'conditions'=>array('SR.dist_distributor_id= DB.id')
							)
						),
					'groups'=>array('SR.id', 'SR.name'),
					'fields'=>array('SR.id', 'SR.name'),
				));	
				$this->set(compact('srs'));
			}

			$sr_id = isset($this->request->data['DistRevenueReport']['sr_id']) != '' ? $this->request->data['DistRevenueReport']['sr_id'] : 0;
			
			// pr($sr_id);exit;
			
			$data_result = array(); //EC and Revenue Loaded in this array
			
			/* Condition For DistMemo */
			$conditions = array();
			
			$conditions['DistMemo.memo_date BETWEEN ? and ? '] = array($date_from, $date_to);
			$conditions['DistMemo.status'] = 1;
			if($region_office_id)$conditions['Office.parent_office_id'] = $region_office_id;
			if($office_id)$conditions['DistMemo.office_id'] = $office_id;
			if($ae_id)$conditions['DistMemo.ae_id'] = $ae_id;
			if($tso_id)$conditions['DistMemo.tso_id'] = $tso_id;
			if($sr_id)$conditions['DistMemo.sr_id'] = $sr_id;
			
			/* Condition For DistOrder */
			$conditions_for_distorder = array();
			
			$conditions_for_distorder['DistOrder.order_date BETWEEN ? and ? '] = array($date_from, $date_to);
			$conditions_for_distorder['DistOrder.status !='] = array(0,3);
			if($region_office_id)$conditions_for_distorder['Office.parent_office_id'] = $region_office_id;
			if($office_id)$conditions_for_distorder['DistOrder.office_id'] = $office_id;
			if($ae_id)$conditions_for_distorder['DistOrder.ae_id'] = $ae_id;
			if($tso_id)$conditions_for_distorder['DistOrder.tso_id'] = $tso_id;
			if($sr_id)$conditions_for_distorder['DistOrder.sr_id'] = $sr_id;

			$sql_condition='';
			if($region_office_id){
				$sql_condition.=" and ofc.parent_office_id=".$region_office_id;
			}
			if($office_id){
				$sql_condition.=" and dt.office_id=".$office_id;
			}
			if($ae_id){
				$sql_condition.=" and dt.dist_area_executive_id=".$ae_id;
			}
			if($tso_id){
				$sql_condition.=" and dt.id=".$tso_id;
			}
			if($sr_id){
				$sql_condition.=" and ds.id=".$sr_id;
			}
			
			
	
			
					
				

		$sql="select 
					ofc.office_name as office_name,
					dt.name as tso,
					dd.name as db,
					ds.name as sr,
					ofc.id as ofc_id,
					dt.id as tso_id,
					dd.id as db_ids,
					ds.id as sr_id
				from dist_sr_route_mapping_histories dsrmh
				inner join dist_tso_mapping_histories dtmh on dtmh.dist_distributor_id=dsrmh.dist_distributor_id and dtmh.is_change=1
				inner join dist_tsos dt on dt.id=dtmh.dist_tso_id
				inner join offices ofc on ofc.id=dt.office_id
				inner join dist_distributors dd on dd.id=dsrmh.dist_distributor_id
				inner join dist_sales_representatives ds on ds.id=dsrmh.dist_sr_id
				
				
				where
					dsrmh.is_change=1
					and dsrmh.effective_date <= '".$date_to."'
					and (dsrmh.end_date is null or dsrmh.end_date > '".$date_from."')
					and dtmh.effective_date <= '".$date_to."'
					and (dtmh.end_date is null or dtmh.end_date > '".$date_from."')
					".$sql_condition."
				group by
					ofc.office_name,
					ofc.id,
					ofc.[order],
					dt.name,
					dt.id,
					dd.name,
					dd.id,
					ds.name,
					ds.id
					
				order by ofc.[order],dt.name,dd.name,ds.name";
				
				// echo $sql;exit;

				$data_all_sr = $this->DistMemo->query($sql); // all active SR
				// pr($data_all_sr);exit;
				// echo $report_for;exit;

			
				

			
			
				$fields = array('SUM(DistMemo.gross_value) as total_revenue','count(distinct DistMemo.id) as ec','DistMemo.office_id','DistMemo.tso_id','DistMemo.distributor_id','DistMemo.sr_id','DistMemo.memo_date');
				$group = array('DistMemo.office_id','DistMemo.tso_id','DistMemo.distributor_id','DistMemo.sr_id','DistMemo.memo_date');
				$order = array('DistMemo.sr_id asc');

				$joins=array(
					array(
						'table'=>'offices',
						'alias'=>'Office',
						'type' => 'LEFT',
						'conditions'=>array('DistMemo.office_id=Office.id')
					),
					
					array(
						'table'=>'dist_tsos',
						'alias'=>'DistTso',
						'type' => 'LEFT',
						'conditions' => 'DistTso.id = DistMemo.tso_id'
						
					),
					array(
						'table'=>'dist_area_executives',
						'alias'=>'DistAE',
						'type' => 'LEFT',
						'conditions' => 'DistAE.id = DistMemo.ae_id'
						
					),
					array(
						'table'=>'dist_sales_representatives',
						'alias'=>'DistSalesRepresentative',
						'type' => 'LEFT',
						'conditions' => 'DistSalesRepresentative.id = DistMemo.sr_id'
						
					),
					array(
						'table'=>'dist_distributors',
						'alias'=>'DistDistributor',
						'type' => 'LEFT',
						'conditions' => 'DistDistributor.id = DistMemo.distributor_id'
						
					),
					
				
				);

				$m_results = $this->DistMemo->find('all', array( //get all data SR wise from dist_memo
					'conditions'=> $conditions,
					'joins'=>$joins,
					'fields' => $fields,
					'group' => $group,
					'order' => $order,
					'recursive' => -1
				));

				foreach ($m_results as $key => $result) {
					$office_id=$result['DistMemo']['office_id'];
					$tso_id=$result['DistMemo']['tso_id'];
					$db_id=$result['DistMemo']['distributor_id'];
					$sr_id=$result['DistMemo']['sr_id'];
					$memo_date=$result['DistMemo']['memo_date'];
					// $memo_date
					$day_array=explode("-",$memo_date);
					$day = $day_array[2];
					$revenue=$result[0]['total_revenue'];
					$ec=$result[0]['ec'];
					
					$data_result[$office_id][$tso_id][$db_id][$sr_id][$day]['ec']= $ec;
					$data_result[$office_id][$tso_id][$db_id][$sr_id][$day]['rev']= $revenue;

					
					
				}

				
			
			

			

				$fields_for_distorder = array('SUM(DistOrder.gross_value) as total_revenue_distorder','count(distinct DistOrder.id) as ec_distorder','DistOrder.office_id','DistOrder.tso_id','DistOrder.distributor_id','DistOrder.sr_id','DistOrder.order_date');
				$group_for_distorder = array('DistOrder.office_id','DistOrder.tso_id','DistOrder.distributor_id','DistOrder.sr_id','DistOrder.order_date');
				$order_for_distorder = array('DistOrder.sr_id asc');
					

				$joins_for_distorder=array(
							array(
								'table'=>'offices',
								'alias'=>'Office',
								'type' => 'LEFT',
								'conditions'=>array('DistOrder.office_id=Office.id')
							),
							
							array(
								'table'=>'dist_tsos',
								'alias'=>'DistTso',
								'type' => 'LEFT',
								'conditions' => 'DistTso.id = DistOrder.tso_id'
								
							),
							array(
								'table'=>'dist_area_executives',
								'alias'=>'DistAE',
								'type' => 'LEFT',
								'conditions' => 'DistAE.id = DistOrder.ae_id'
								
							),
							array(
								'table'=>'dist_sales_representatives',
								'alias'=>'DistSalesRepresentative',
								'type' => 'LEFT',
								'conditions' => 'DistSalesRepresentative.id = DistOrder.sr_id'
								
							),
							array(
								'table'=>'dist_distributors',
								'alias'=>'DistDistributor',
								'type' => 'LEFT',
								'conditions' => 'DistDistributor.id = DistOrder.distributor_id'
								
							),
							
						
						);
						
						// echo $date;exit;
						

						

						$m_results_for_distorder = $this->DistOrder->find('all', array( //get all data SR wise from dist_order
							'conditions'=> $conditions_for_distorder,
							'joins'=>$joins_for_distorder,
							'fields' => $fields_for_distorder,
							'group' => $group_for_distorder,
							'order' => $order_for_distorder,
							'recursive' => -1
						));
				
				
				// echo $this->DistOrder->getLastQuery();	
				// pr($m_results_for_distorder);exit;

				foreach ($m_results_for_distorder as $key => $result_distorder) {
					$office_id=$result_distorder['DistOrder']['office_id'];
					$tso_id=$result_distorder['DistOrder']['tso_id'];
					$db_id=$result_distorder['DistOrder']['distributor_id'];
					$sr_id=$result_distorder['DistOrder']['sr_id'];
					$memo_date=$result_distorder['DistOrder']['order_date'];
					// $memo_date
					$day_array=explode("-",$memo_date);
					$day = $day_array[2];
					$revenue_distorder=$result_distorder[0]['total_revenue_distorder'];
					$ec_distorder=$result_distorder[0]['ec_distorder'];
					
					$data_result[$office_id][$tso_id][$db_id][$sr_id][$day]['ec_distorder']= $ec_distorder;
					$data_result[$office_id][$tso_id][$db_id][$sr_id][$day]['rev_distorder']= $revenue_distorder;

					
					
				}
			

			
			// echo '<pre>';
			// print_r($data_result);
			// echo '</pre>';exit;
			$this->set(compact('m_results','m_results_for_distorder'));
			
			if($report_for_flag==1){

				$colspan=2;

			}else if($report_for_flag==2){
				
				$colspan=2;

			}else if($report_for_flag==3){
				
				$colspan=4;

			}
			$html='';
			
	
			$html.='
			<tr class="titlerow">
				<td rowspan="3">SL</td>
				<td rowspan="3">Area Office Name</td>
				<td rowspan="3">TSO Name</td>
				<td rowspan="3">Distributor Name</td>
				<td rowspan="3">SR Name</td>
				';

	
			foreach ($datelist as $date) {
				$day_array=explode("-",$date);
				$day = "Day ".$day_array[2];
				
				$html.='<td colspan="'.$colspan.'">'.$day.'</td>';
				
			}

			$html .='<td colspan="'.$colspan.'" style="background: #f1f1f1;font-size: 12px;font-weight: bold;">Total</td>';

			$html .='</tr><tr>';

			foreach ($datelist as $date) {


				if($report_for_flag==1){ //for memo

					
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;" colspan="2" >Memo</td>';
					
	
				}else if($report_for_flag==2){ //for order
					
					
					
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;" colspan="2">Order</td>';
	
				}else if($report_for_flag==3){ //for both
					
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;" colspan="2">Memo</td>';
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;" colspan="2">Order</td>';
	
				}
				
				
			}

				if($report_for_flag==1){ //for memo

						
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;" colspan="2">Memo</td>';
					

				}else if($report_for_flag==2){ //for order
					
					
					
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;" colspan="2">Order</td>';

				}else if($report_for_flag==3){ //for both
					
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;" colspan="2">Memo</td>';
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;" colspan="2">Order</td>';

				}
			
			
			$html .='</tr><tr>'; //here

			foreach ($datelist as $date) {

				if($report_for_flag==1){ //for memo

						
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;" >Revenue</td>';
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;">EC</td>';
					

				}else if($report_for_flag==2){ //for order
					
					
					
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;" >Revenue</td>';
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;">EC</td>';

				}else if($report_for_flag==3){ //for both
					
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;" >Revenue</td>';
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;">EC</td>';
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;" >Revenue</td>';
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;">EC</td>';

				}
				
				
				
			}

				if($report_for_flag==1){ //for memo

							
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;">Revenue</td>';
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;">EC</td>';
					

				}else if($report_for_flag==2){ //for order
					
					
					
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;">Revenue</td>';
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;">EC</td>';

				}else if($report_for_flag==3){ //for both
					
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;">Revenue</td>';
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;">EC</td>';
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;">Revenue</td>';
					$html.='<td style="background: #f1f1f1;font-size: 12px;font-weight: bold;">EC</td>';

				}
			
			
			
			$html .='</tr>';

				    
			
			
			$this->set(compact('data_all_sr'));
			
			$grand_total_revenue = 0;
			$grand_total_ec = 0;
			$grand_total_revenue_distorder = 0;
			$grand_total_ec_distorder = 0;
			$counter=1;
			$day_ec_total=array();
			$day_rev_total=array();
			$day_ec_total_distorder=array();
			$day_rev_total_distorder=array();
			foreach($data_all_sr as $key =>$sr)
			{		
				
				
				$html.='<tr>';
					$html.='<td>'.$counter.'</td>';
					$html.='<td>'.$sr[0]['office_name'].'</td>';
					$html.='<td>'.$sr[0]['tso'].'</td>';
					$html.='<td>'.$sr[0]['db'].'</td>';
					$html.='<td>'.$sr[0]['sr'].'</td>';

					
					$sr_month_total_ec=0;
					$sr_month_total_revenue=0;
					$sr_month_total_ec_distorder=0;
					$sr_month_total_revenue_distorder=0;
					foreach ($datelist as $date) {
						$day_array=explode("-",$date);
						$day = $day_array[2];

						
						
					
						if(isset($data_result[$sr[0]['ofc_id']][$sr[0]['tso_id']][$sr[0]['db_ids']][$sr[0]['sr_id']][$day])){

							$revenue = $data_result[$sr[0]['ofc_id']][$sr[0]['tso_id']][$sr[0]['db_ids']][$sr[0]['sr_id']][$day]['rev'];
							$ec = $data_result[$sr[0]['ofc_id']][$sr[0]['tso_id']][$sr[0]['db_ids']][$sr[0]['sr_id']][$day]['ec'];

							$revenue_distorder = $data_result[$sr[0]['ofc_id']][$sr[0]['tso_id']][$sr[0]['db_ids']][$sr[0]['sr_id']][$day]['rev_distorder'];
							$ec_distorder = $data_result[$sr[0]['ofc_id']][$sr[0]['tso_id']][$sr[0]['db_ids']][$sr[0]['sr_id']][$day]['ec_distorder'];

						}else{
							$revenue=0;
							$ec=0;
							$revenue_distorder = 0;
							$ec_distorder = 0;
						}

						$day_ec_total[$day]=(isset($day_ec_total[$day])?$day_ec_total[$day]:0)+$ec;
						$day_rev_total[$day]=(isset($day_rev_total[$day])?$day_rev_total[$day]:0)+$revenue;

						$day_ec_total_distorder[$day]=(isset($day_ec_total_distorder[$day])?$day_ec_total_distorder[$day]:0)+$ec;
						$day_rev_total_distorder[$day]=(isset($day_rev_total_distorder[$day])?$day_rev_total_distorder[$day]:0)+$revenue;
						
						$sr_month_total_revenue += $revenue;
						$sr_month_total_ec += $ec;
						$sr_month_total_revenue_distorder += $revenue_distorder;
						$sr_month_total_ec_distorder += $ec_distorder;
						
						

						if($report_for_flag==1){ //for memo

							$html.='<td>'.number_format($revenue, 2).'</td>';
							$html.='<td>'.number_format($ec, 2).'</td>';
			
						}else if($report_for_flag==2){ //for order
							
							$html.='<td>'.number_format($revenue_distorder, 2).'</td>';
							$html.='<td>'.number_format($ec_distorder, 2).'</td>';
			
						}else if($report_for_flag==3){ //for both
							
							$html.='<td>'.number_format($revenue, 2).'</td>';
							$html.='<td>'.number_format($ec, 2).'</td>';
							$html.='<td>'.number_format($revenue_distorder, 2).'</td>';
							$html.='<td>'.number_format($ec_distorder, 2).'</td>';
			
						}
						
						
					}
					
					

					if($report_for_flag==1){ //for memo

						$html.='<td>'.number_format($sr_month_total_revenue, 2).'</td>';
						$html.='<td>'.number_format($sr_month_total_ec, 2).'</td>';
		
					}else if($report_for_flag==2){ //for order
						
						$html.='<td>'.number_format($sr_month_total_revenue_distorder, 2).'</td>';
						$html.='<td>'.number_format($sr_month_total_ec_distorder, 2).'</td>';
		
					}else if($report_for_flag==3){ //for both
						
						$html.='<td>'.number_format($sr_month_total_revenue, 2).'</td>';
						$html.='<td>'.number_format($sr_month_total_ec, 2).'</td>';
						$html.='<td>'.number_format($sr_month_total_revenue_distorder, 2).'</td>';
						$html.='<td>'.number_format($sr_month_total_ec_distorder, 2).'</td>';
		
					}
					
					$html.='</tr>';
					// echo $html;exit;
					$grand_total_revenue +=$sr_month_total_revenue;
					$grand_total_ec +=$sr_month_total_ec;

					$grand_total_revenue_distorder +=$sr_month_total_revenue_distorder;
					$grand_total_ec_distorder +=$sr_month_total_ec_distorder;

					
				
					$counter++;
			}
			

			$html.=	'<tr class="titlerow">
						  <td colspan="5">Total</td>';
				
				foreach ($datelist as $date) {
					$day_array=explode("-",$date);
					$day = $day_array[2];
					
					if($report_for_flag==1){ //for memo

						$html.='<td>'.number_format($day_rev_total[$day], 2).'</td>';
						$html.='<td>'.number_format($day_ec_total[$day], 2).'</td>';
		
					}else if($report_for_flag==2){ //for order
						
						$html.='<td>'.number_format($day_rev_total_distorder[$day], 2).'</td>';
						$html.='<td>'.number_format($day_ec_total_distorder[$day], 2).'</td>';
		
					}else if($report_for_flag==3){ //for both
						
						$html.='<td>'.number_format($day_rev_total[$day], 2).'</td>';
						$html.='<td>'.number_format($day_ec_total[$day], 2).'</td>';
						$html.='<td>'.number_format($day_rev_total_distorder[$day], 2).'</td>';
						$html.='<td>'.number_format($day_ec_total_distorder[$day], 2).'</td>';
		
					}
				}
				
					if($report_for_flag==1){ //for memo

						$html.='<td>'.number_format($grand_total_revenue, 2).'</td>';
						$html.='<td>'.number_format($grand_total_ec, 2).'</td>';
		
					}else if($report_for_flag==2){ //for order
						
						$html.='<td>'.number_format($grand_total_revenue_distorder, 2).'</td>';
						$html.='<td>'.number_format($grand_total_ec_distorder, 2).'</td>';
		
					}else if($report_for_flag==3){ //for both
						
						$html.='<td>'.number_format($grand_total_revenue, 2).'</td>';
						$html.='<td>'.number_format($grand_total_ec, 2).'</td>';
						$html.='<td>'.number_format($grand_total_revenue_distorder, 2).'</td>';
						$html.='<td>'.number_format($grand_total_ec_distorder, 2).'</td>';
		
					}

			$html.=	'</tr>';
			
			// echo $html;
			$this->set(compact('html'));
			
			//exit;
		}
		
		
		
		
		
		$this->set(compact('offices', 'office_id', 'sales_people'));
		$this->set(compact('office_parent_id','by_colums','region_offices', 'request_data','report_for')); 
		
		
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
		$sql_condition = ' ';
		if($office_id){
			$sql_condition.=" and dt.office_id=".$office_id;
		}
		$given_date=$this->request->data['month'];
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		if($given_date){ //check month given or not
			$month_year=explode("-",$this->request->data['month']);
				$month = $month_year[0];
				$year = $month_year[1];

				$last_date_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

				$datelist=array();
				for($day=1; $day<=$last_date_month; $day++)
				{
					$time=mktime(12, 0, 0, $month, $day, $year);
					if (date($month, $time)==date($month))
						$datelist[]=date('Y-m-d', $time);
				}
				// echo "<pre>";
				// print_r($datelist);
				// echo "</pre>";

			$date_from=$datelist[0]; //first day of month
			$date_to=$datelist[count($datelist)-1]; //last day of month
			
			$this->loadModel('DistAreaExecutive');
			

			$sql="select 
						
						dae.name as ae,
						dae.id as ae_id
						
					from dist_sr_route_mapping_histories dsrmh
					inner join dist_tso_mapping_histories dtmh on dtmh.dist_distributor_id=dsrmh.dist_distributor_id and dtmh.is_change=1
					inner join dist_tsos dt on dt.id=dtmh.dist_tso_id
					inner join dist_area_executives dae on dae.id=dt.dist_area_executive_id
					
					
					where
						dsrmh.is_change=1
						and dsrmh.effective_date <= '".$date_to."'
						and (dsrmh.end_date is null or dsrmh.end_date > '".$date_from."')
						and dtmh.effective_date <= '".$date_to."'
						and (dtmh.end_date is null or dtmh.end_date > '".$date_from."')
						".$sql_condition."
					group by
						
						dae.name,
						dae.id
						
					order by dae.name";
					
					// echo $sql;exit;

					$data_all_ae = $this->DistAreaExecutive->query($sql); // all active SR
			// $dist_aes = $this->DistAreaExecutive->find('all',array(
			// 	'conditions'=>array(
			// 		'DistAreaExecutive.office_id'=>$office_id,
			// 		'DistAreaExecutive.is_active'=> 1,
			// 	),
			// 	'recursive' => 0,
			// ));
				// pr($data_all_ae);exit;
			$data_array = array();
			foreach($data_all_ae as $key => $value)
			{
				$data_array[] = array(
					'id' => $value[0]['ae_id'],
					'name' => $value[0]['ae'],
				);
			}
		}
		if(!empty($office_id) && !empty($given_date)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}
	function get_tso_list(){
		$ae_id = $this->request->data['ae_id'];
		$sql_condition=' ';
		if($ae_id){
			$sql_condition.=" and dt.dist_area_executive_id=".$ae_id;
		}
		$given_date=$this->request->data['month'];
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		if($given_date){ //check month given or not
				$month_year=explode("-",$this->request->data['month']);
					$month = $month_year[0];
					$year = $month_year[1];

					$last_date_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

					$datelist=array();
					for($day=1; $day<=$last_date_month; $day++)
					{
						$time=mktime(12, 0, 0, $month, $day, $year);
						if (date($month, $time)==date($month))
							$datelist[]=date('Y-m-d', $time);
					}
					// echo "<pre>";
					// print_r($datelist);
					// echo "</pre>";

				$date_from=$datelist[0]; //first day of month
				$date_to=$datelist[count($datelist)-1]; //last day of month

				$this->loadModel('DistTso');
				
				// $dist_tsos = $this->DistTso->find('all',array(
				// 	'conditions'=>array(
				// 		'DistTso.dist_area_executive_id'=>$ae_id,
				// 		'DistTso.is_active'=> 1,
				// 	),
				// 	'recursive' => 0,
				// ));

				$sql="select 
							
							dt.name as tso,
							
							dt.id as tso_id
							
						from dist_sr_route_mapping_histories dsrmh
						inner join dist_tso_mapping_histories dtmh on dtmh.dist_distributor_id=dsrmh.dist_distributor_id and dtmh.is_change=1
						inner join dist_tsos dt on dt.id=dtmh.dist_tso_id
						
						
						
						
						where
							dsrmh.is_change=1
							and dsrmh.effective_date <= '".$date_to."'
							and (dsrmh.end_date is null or dsrmh.end_date > '".$date_from."')
							and dtmh.effective_date <= '".$date_to."'
							and (dtmh.end_date is null or dtmh.end_date > '".$date_from."')
							".$sql_condition."
						group by
							
							dt.name,
							dt.id
							
							
						order by dt.name";
						
						// echo $sql;exit;

						$data_all_tso = $this->DistMemo->query($sql); // all active SR
					// pr($data_all_tso);exit;
				$data_array = array();
				foreach($data_all_tso as $key => $value)
				{
					$data_array[] = array(
						'id' => $value[0]['tso_id'],
						'name' => $value[0]['tso'],
					);
				}
			}
			if(!empty($ae_id) && !empty($given_date)){
				echo json_encode(array_merge($rs,$data_array));
			}else{
				echo json_encode($rs);
			} 
			$this->autoRender = false;
		}
		function get_sr_list(){		
			
			$tso_id = $this->request->data['tso_id'];
			// $tso_id = $this->request->data['tso_id'];
			$sql_condition=' ';
			if($tso_id){
				$sql_condition.=" and dt.id=".$tso_id;
			}
			$given_date=$this->request->data['month'];
			$rs = array(array('id' => '', 'name' => '---- All -----'));
			if($given_date){ //check month given or not
			$month_year=explode("-",$this->request->data['month']);
				$month = $month_year[0];
				$year = $month_year[1];

				$last_date_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

				$datelist=array();
				for($day=1; $day<=$last_date_month; $day++)
				{
					$time=mktime(12, 0, 0, $month, $day, $year);
					if (date($month, $time)==date($month))
						$datelist[]=date('Y-m-d', $time);
				}
				// echo "<pre>";
				// print_r($datelist);
				// echo "</pre>";

			$date_from=$datelist[0]; //first day of month
			$date_to=$datelist[count($datelist)-1]; //last day of month

			// echo $date_from.' '.$date_to;
			$this->loadModel('DistTso');
			
			$this->loadModel('DistTsoMapping');
			//actually taking 
			// $distributors = $this->DistTsoMapping->find('list', array(
			// 	'conditions'=> array(
			// 		//'DistTsoMapping.office_id'=>$office_id,
			// 		'DistTsoMapping.dist_tso_id'=>$tso_id,
			// 	),
			// 	'joins'=>
			// 		array(
			//             array(
			//                 'table'=>'dist_distributors',
			//                 'alias'=>'DB',
			//                 'type' => 'LEFT',
			//                 'conditions'=>array('DB.id= DistTsoMapping.dist_distributor_id')
			// 			),
			// 			array(
			//                 'table'=>'dist_sales_representatives',
			//                 'alias'=>'SR',
			//                 'type' => 'LEFT',
			//                 'conditions'=>array('SR.dist_distributor_id= DB.id')
			//             )
			//         ),
			//     'fields'=>array('SR.id','SR.name'),
			// ));

			$sql="select 
						
						ds.name as sr,
						
						ds.id as sr_id
					from dist_sr_route_mapping_histories dsrmh
					inner join dist_tso_mapping_histories dtmh on dtmh.dist_distributor_id=dsrmh.dist_distributor_id and dtmh.is_change=1
					inner join dist_tsos dt on dt.id=dtmh.dist_tso_id
					
					inner join dist_sales_representatives ds on ds.id=dsrmh.dist_sr_id
					
					
					where
						dsrmh.is_change=1
						and dsrmh.effective_date <= '".$date_to."'
						and (dsrmh.end_date is null or dsrmh.end_date > '".$date_from."')
						and dtmh.effective_date <= '".$date_to."'
						and (dtmh.end_date is null or dtmh.end_date > '".$date_from."')
						".$sql_condition."
						
					group by
						
						ds.name,
						ds.id
						
					order by ds.name";
					
					// echo $sql;exit;

					$data_all_sr = $this->DistTso->query($sql); // all active SR
			
			// pr($data_all_sr);exit;

			$data_array = array();
			foreach($data_all_sr as $key => $value)
			{
				$data_array[] = array(
					'id' => $value[0]['sr_id'],
					'name' => $value[0]['sr'],
				);
			}
		}
		// pr($data_array);exit;
		if(!empty($tso_id) && !empty($given_date)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}

	

	/*********End*********/
}
