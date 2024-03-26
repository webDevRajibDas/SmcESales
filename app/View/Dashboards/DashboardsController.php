<?php
App::uses('AppController', 'Controller');
/**
 * Designations Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DashboardsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session');
	//public $components = array('Paginator', 'Session', 'Filter.Filter');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
						
		ini_set('max_execution_time', 300); //300 seconds = 5 minutes
		
		date_default_timezone_set('Asia/Dhaka');
		
		$GLOBALS['OfficeId'] = $this->UserAuth->getOfficeId();
				
		$this->loadModel('SalesPerson');		
		$this->loadModel('Memo');		
		$this->loadModel('MemoSyncHistory');
		$this->loadModel('Office');
						
		
		if($this->UserAuth->getOfficeParentId() && $this->UserAuth->getOfficeParentId()!=14)
		{
			$so_list = $this->SalesPerson->find('all', array(
			'fields' => array('SalesPerson.id','SalesPerson.name','SalesPerson.last_data_push_time','Territory.name'),
			'conditions' => array(
				'SalesPerson.office_id' => $this->UserAuth->getOfficeId(),
				'SalesPerson.territory_id >' => 0,
				
				'User.user_group_id' => array(4, 1008)
			)
			));		
		}
		elseif($this->UserAuth->getOfficeParentId()==14)
		{
			
			//Territory Sales Performance Monitoring
			
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $this->UserAuth->getOfficeId(),
					
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
			
			$office_id = array_keys($offices);
			
			$so_list = $this->SalesPerson->find('all', array(
			'fields' => array('SalesPerson.id','SalesPerson.name','SalesPerson.last_data_push_time','Territory.name'),
			'conditions' => array(
				'SalesPerson.office_id' => $office_id,
				'SalesPerson.territory_id >' => 0,
				'User.user_group_id' => array(4, 1008)
			)
			));		
			
		}
		else
		{
			$so_list = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id','SalesPerson.name','SalesPerson.last_data_push_time','Territory.name'),
				'conditions' => array(
					//'Territory.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					'User.user_group_id' => array(4, 1008),
					"NOT" => array( "Territory.office_id" => array(30, 31, 37)),
				)
			));
		}
		//pr($so_list);
		//exit;
		
		
		$so_list = array();
		
		$data_array = array();
		$i=0;
		$total = count($so_list);

		//$so_list = array();
		//for memo push
		foreach($so_list as $so){ 
		
			//pr($so);
			
			$this->Memo->virtualFields = array(
				'total_memo' => 'COUNT(*)',
				'total_memo_value' => 'SUM(Memo.gross_value)'
			); 
			
			//echo $so['SalesPerson']['id'];
			
			$memo1 = $this->Memo->find('first', array(
				'conditions' => array('Memo.sales_person_id' => $so['SalesPerson']['id'], 'Memo.status !=' => 0),
				'order' => array('Memo.updated_at desc'),
				//'order' => array('Memo.memo_date desc'),
				'fields' => array('memo_date', 'updated_at'),
				'recursive' => -1
			));
			
			$last_memo_sync_date = '';
			
			//pr($memo1);
			
			if(!empty($memo1))
			{
				$last_memo_sync_date = $memo1['Memo']['memo_date'];
				
			}
			else
			{
				$last_memo_sync_date = $this->current_date();
			}
			
			
			$so_last_push_time = $so['SalesPerson']['last_data_push_time'];
			
			/*$memo = $this->Memo->find('first', array(
				//'conditions' => array('Memo.sales_person_id' => $so['SalesPerson']['id'],'Memo.memo_date >=' => $last_memo_sync_date.' 00:00:00','Memo.memo_date <=' => $last_memo_sync_date.' 23:59:59'),
				//'conditions' => array('Memo.sales_person_id' => $so['SalesPerson']['id'], 'Memo.memo_date =' => $last_memo_sync_date, 'Memo.status !=' => 0),
				'conditions' => array(
					'Memo.sales_person_id' => $so['SalesPerson']['id'], 
					'Memo.updated_at >' => $so_last_push_time, 
					'Memo.status !=' => 0
				),
				'fields' => array('total_memo', 'total_memo_value'),
				'recursive' => -1
			));
			pr($memo);*/
			
			//new query from mmemo table with above condition and sum
			//echo $so_last_push_time.'<br>';
			//echo $last_memo_sync_date.'('.$so['SalesPerson']['id'].')<br>';
			$memo = $this->Memo->find('all', array(
				'conditions' => array(
					'Memo.sales_person_id' => $so['SalesPerson']['id'], 
					//'Memo.updated_at >=' => $last_memo_sync_date, 
					'Memo.memo_date >=' => $last_memo_sync_date, 
					'Memo.status !=' => 0
				),
				'fields' => array('COUNT(Memo.memo_no) AS total_memo, sum(Memo.gross_value) AS total_memo_value'),
				'recursive' => -1
			));
			//pr($memo);
			
                                           			
			/*$memo_sync = $this->MemoSyncHistory->find('first', array(
				'conditions' => array('MemoSyncHistory.so_id' => $so['SalesPerson']['id'], 'MemoSyncHistory.date' => $last_memo_sync_date),
				'fields' => array('total_memo'),
				'recursive' => -1
			));*/
			
			//new query 2
			//echo $last_memo_sync_date.'<br>';
			$memo_sync = $this->MemoSyncHistory->find('all', array(
				'conditions' => array(
					'MemoSyncHistory.so_id' => $so['SalesPerson']['id'], 
					'MemoSyncHistory.date >=' => $last_memo_sync_date,
					'not' => array('MemoSyncHistory.missed_memo' => NULL)
				),
				//'fields' => array('sum(MemoSyncHistory.total_memo) as total_memo'),
				'fields' => array('sum(MemoSyncHistory.total_memo - MemoSyncHistory.missed_memo) as total_memo'),
				'recursive' => -1
			));
			
			//pr($memo_sync);
                        
            $last_memo_info = $this->Memo->find('first', array(
				'conditions' => array(
					'Memo.sales_person_id' => $so['SalesPerson']['id'], 
					'Memo.status !=' => 0
				),
				'fields' => array('Memo.memo_no', 'Memo.memo_time'),
                'order' => array('Memo.id' => 'desc'),
				'recursive' => -1
			));
			
			
			
			//$data['total_sync_memo'] = (!empty($memo_sync) ? $memo_sync['MemoSyncHistory']['total_memo'] : 0);
			
			$total_memo = (!empty($memo_sync[0][0]['total_memo']) && $memo_sync[0][0]['total_memo'] >=0) ? $memo_sync[0][0]['total_memo'] : 0; 
			$total_sync_memo = (!empty($memo) ? $memo[0][0]['total_memo'] : 0);
			
			$total_waiting = $total_memo-$total_sync_memo;
			$data['total_waiting_sync_memo'] = ($total_waiting < 0) ? 0 : $total_waiting;
			
			$data['total_memo_value'] = $memo[0][0]['total_memo_value']?$memo[0][0]['total_memo_value']:0;
			
			$data['total_memo'] = $total_memo;
			$data['total_sync_memo'] = $total_sync_memo;
			$data['id'] = $so['SalesPerson']['id'];
			$data['name'] = $so['SalesPerson']['name'];
			$data['territory'] = $so['Territory']['name'];
			$data['time'] = $so['SalesPerson']['last_data_push_time'];
			$data['hours'] = $this->get_hours($so['SalesPerson']['last_data_push_time']);
                        
			if(!empty($last_memo_info))
			{
			$data['last_memo_no'] = $last_memo_info['Memo']['memo_no'];
			$data['last_memo_sync'] = $last_memo_info['Memo']['memo_time'];
			}
             
			/*if($data['total_sync_memo']==$data['total_memo']){    
				$data_array[$total] = $data;
			}else{
				$data_array[$i] = $data;
			}*/
			
			
			$data_array[] = $data;
			//$data_array[$data['total_waiting_sync_memo']] = $data;
			
			
			$i++;
			$total++;
			
			//if($i==1)break;
			
		}
		

		
		rsort($data_array);
		
		//pr($data_array);
		//exit;
		
		
		$this->set(compact('data_array'));	
		
		//echo $this->UserAuth->getOfficeParentId();
		//exit;
		
		
		//START fiscal year & month CalCulation
		$this->loadModel('FiscalYear');	
		$fiscal_year_result = $this->FiscalYear->find('first', 
			array(
			'conditions'=> array("FiscalYear.start_date <=" => date('Y-6-d'), "FiscalYear.end_date >=" => date('Y-6-d')),
			'recursive' => -1
			)
		);
		
		$GLOBALS['fiscal_year_result'] = $fiscal_year_result;
		
		$GLOBALS['fiscal_year_id'] = $fiscal_year_id = $fiscal_year_result['FiscalYear']['id'];
					
		//get month_id
		/*$this->loadModel('Month');	
		$month_result = $this->Month->find('first', 
			array(
			'conditions'=> array("Month.fiscal_year_id" => $fiscal_year_id, "Month.month" => intval(date('m'))),
			'recursive' => -1
			)
		);
		$GLOBALS['fiscal_month_id'] = $month_id = $month_result['Month']['id'];*/
		//END fiscal year & month CalCulation
		
		
		
		if($this->UserAuth->getOfficeParentId() && $this->UserAuth->getOfficeParentId()!=14)
		{
			//area office
			$office_id = $this->UserAuth->getOfficeId();
			$this->set(compact('office_id'));
			
			//START TOP 16 BOXES Revenue & EC
			//Revenue & EC
			$today_SalesTotal = $this->getSalesTotal($box_data='today', '', $office_id);
			$today_Revenue = $this->priceSetting($today_SalesTotal['total_Revenue']);
			$today_EC = $today_SalesTotal['total_EC'];
			$this->set(compact('today_Revenue', 'today_EC'));
			
			$week_SalesTotal = $this->getSalesTotal($box_data='week', '', $office_id);
			$week_Revenue = $this->priceSetting($week_SalesTotal['total_Revenue']);
			$week_EC = $week_SalesTotal['total_EC'];
			$this->set(compact('week_Revenue', 'week_EC'));
			
			
			$month_SalesTotal = $this->getSalesTotal($box_data='month', '', $office_id);
			$month_Revenue = $this->priceSetting($month_SalesTotal['total_Revenue']);
			$month_EC = $month_SalesTotal['total_EC'];
			$this->set(compact('month_Revenue', 'month_EC'));
			
			$year_SalesTotal = $this->getSalesTotal($box_data='year', '', $office_id);
			$year_Revenue = $this->priceSetting($year_SalesTotal['total_Revenue']);
			$year_EC = $year_SalesTotal['total_EC'];
			$this->set(compact('year_Revenue', 'year_EC'));
			
			//OC 
			$today_OC = $this->getOCTotal($box_data='today', $office_id);
			$week_OC = $this->getOCTotal($box_data='week', $office_id);
			$month_OC = $this->getOCTotal($box_data='month', $office_id);
			$year_OC = $this->getOCTotal($box_data='year', $office_id);
			$this->set(compact('today_OC', 'week_OC', 'month_OC', 'year_OC'));
			
			//CYP 
			$today_CYP = $this->getCYPTotal($box_data='today', $office_id);
			$week_CYP = $this->getCYPTotal($box_data='week', $office_id);
			$month_CYP = $this->getCYPTotal($box_data='month', $office_id);
			$year_CYP = $this->getCYPTotal($box_data='year', $office_id);
			$this->set(compact('today_CYP', 'week_CYP', 'month_CYP', 'year_CYP'));
			//END TOP 16 BOXES
			
			
			
			//START Achievement Vs Target 
			/*$data_from = date('Y-6-01');
			$achievement_result = $this->Memo->find('all', 
				array(
				'conditions'=> array(
					'Memo.memo_date >=' => $data_from,
					'Territory.office_id' => $office_id,
					'Memo.status !=' => 0
				),
				'fields'=> array('sum(Memo.gross_value) AS total_amount'),
				'recursive' => 0
				)
			);
			$achievement_amount = $this->priceSetting($achievement_result[0][0]['total_amount']);*/
			
			$year_total_Revenue = $year_SalesTotal['total_Revenue'];
			$achievement_amount = $this->priceSetting($year_total_Revenue);
			
			//get total_target
			$this->loadModel('SaleTarget');		
			$total_target_result = $this->SaleTarget->find('all', 
				array(
				'conditions'=> array(
					'fiscal_year_id' => $fiscal_year_id,
					//'month_id' => $month_id,
					'aso_id' => $office_id
				),
				'fields'=> array('sum(amount) AS total_amount'),
				'recursive' => 0
				)
			);	
			
			
			
			if($total_target_result[0][0]['total_amount']>0){
				//$total_target = ($total_target_result[0][0]['total_amount']/12);
				$total_target = ($total_target_result[0][0]['total_amount']);
			}else{
				$total_target = 0;
			}
			
			if($total_target>0){
				//$achievement_penchant = round(($achievement_result[0][0]['total_amount']*100)/$total_target);
				$achievement_penchant = round(($year_total_Revenue*100)/$total_target);
			}else{
				$achievement_penchant = 100;
			}
			
			$total_target = $this->priceSetting($total_target);
			//echo $achievement_penchant;
			//exit;
			//END Achievement Vs Target 
						
			
			
			//START PRODUCT SALES BAR GRAPH
			$m_1_total_amount =  $this->getSalesTotal('', date('Y-6', time()), $office_id);
			$m_2_total_amount =  $this->getSalesTotal('', date('Y-6', strtotime('-1 month', strtotime(date('Y-6-01')))), $office_id);
			$m_3_total_amount =  $this->getSalesTotal('', date('Y-6', strtotime('-2 month', strtotime(date('Y-6-01')))), $office_id);
			$m_4_total_amount =  $this->getSalesTotal('', date('Y-6', strtotime('-3 month', strtotime(date('Y-6-01')))), $office_id);
			$m_5_total_amount =  $this->getSalesTotal('', date('Y-6', strtotime('-4 month', strtotime(date('Y-6-01')))), $office_id);
			$m_6_total_amount =  $this->getSalesTotal('', date('Y-6', strtotime('-5 month', strtotime(date('Y-6-01')))), $office_id);
			
			$bar_series = $m_1_total_amount.', '.$m_2_total_amount.', '.$m_3_total_amount.', '.$m_4_total_amount.', '.$m_5_total_amount.', '.$m_6_total_amount;
			//END PRODUCT SALES BAR GRAPH
			
			$this->loadModel('ProductSetting');
	   		$product_settings = $this->ProductSetting->find('all', array('order'=>array('sort' => 'ASC')));
			$this->set(compact('product_settings'));
			
			
			$this->loadModel('ReportProductSetting');
	   		$report_products = $this->ReportProductSetting->find('all', array('order'=>array('sort' => 'ASC')));
			$this->set(compact('report_products'));
			
			
			$this->loadModel('PieProductSetting');
	   		$pie_products = $this->PieProductSetting->find('all', array('order'=>array('sort' => 'ASC')));
			$this->set(compact('pie_products'));
			
						
			
			//Territory Sales Performance Monitoring
			$this->loadModel('Office');
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' => 2,
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
			
			
			$this->loadModel('Territory');
			$territories = $this->Territory->find('list', array('conditions'=> array('office_id' => $office_id), 'order'=>array('name'=>'asc')));

			
			
			$this->set(compact('achievement_amount', 'total_target', 'achievement_penchant','bar_series', 'offices', 'territories'));
			
			$this->render('aso_index');
			
		}
		elseif($this->UserAuth->getOfficeParentId()==14)
		{
			
			//for regional admin
			$region_office_id = $this->UserAuth->getOfficeId();
			
			//Territory Sales Performance Monitoring
			$this->loadModel('Office');
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,
					
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
			
			
			
			//$office_id = array();
			
			
			
			$office_id = array_keys($offices);
			$this->set(compact('office_id'));
			/*pr($office_id);
			exit;*/
			
			//START TOP 16 BOXES Revenue & EC
			//Revenue & EC
			$today_SalesTotal = $this->getSalesTotal($box_data='today', '', $office_id);
			$today_Revenue = $this->priceSetting($today_SalesTotal['total_Revenue']);
			$today_EC = $today_SalesTotal['total_EC'];
			$this->set(compact('today_Revenue', 'today_EC'));
			
			$week_SalesTotal = $this->getSalesTotal($box_data='week', '', $office_id);
			$week_Revenue = $this->priceSetting($week_SalesTotal['total_Revenue']);
			$week_EC = $week_SalesTotal['total_EC'];
			$this->set(compact('week_Revenue', 'week_EC'));
			
			
			$month_SalesTotal = $this->getSalesTotal($box_data='month', '', $office_id);
			$month_Revenue = $this->priceSetting($month_SalesTotal['total_Revenue']);
			$month_EC = $month_SalesTotal['total_EC'];
			$this->set(compact('month_Revenue', 'month_EC'));
			
			$year_SalesTotal = $this->getSalesTotal($box_data='year', '', $office_id);
			$year_Revenue = $this->priceSetting($year_SalesTotal['total_Revenue']);
			$year_EC = $year_SalesTotal['total_EC'];
			$this->set(compact('year_Revenue', 'year_EC'));
			
			//OC 
			$today_OC = $this->getOCTotal($box_data='today', $office_id);
			$week_OC = $this->getOCTotal($box_data='week', $office_id);
			$month_OC = $this->getOCTotal($box_data='month', $office_id);
			$year_OC = $this->getOCTotal($box_data='year', $office_id);
			$this->set(compact('today_OC', 'week_OC', 'month_OC', 'year_OC'));
			
			//CYP 
			$today_CYP = $this->getCYPTotal($box_data='today', $office_id);
			$week_CYP = $this->getCYPTotal($box_data='week', $office_id);
			$month_CYP = $this->getCYPTotal($box_data='month', $office_id);
			$year_CYP = $this->getCYPTotal($box_data='year', $office_id);
			$this->set(compact('today_CYP', 'week_CYP', 'month_CYP', 'year_CYP'));
			//END TOP 16 BOXES
			
			
			
			//START Achievement Vs Target 
			/*$data_from = date('Y-6-01');
			$achievement_result = $this->Memo->find('all', 
				array(
				'conditions'=> array(
					'Memo.memo_date >=' => $data_from,
					'Territory.office_id' => $office_id,
					'Memo.status !=' => 0
				),
				'fields'=> array('sum(Memo.gross_value) AS total_amount'),
				'recursive' => 0
				)
			);
			$achievement_amount = $this->priceSetting($achievement_result[0][0]['total_amount']);*/
			
			$year_total_Revenue = $year_SalesTotal['total_Revenue'];
			$achievement_amount = $this->priceSetting($year_total_Revenue);
			
			//get total_target
			$this->loadModel('SaleTarget');		
			$total_target_result = $this->SaleTarget->find('all', 
				array(
				'conditions'=> array(
					'fiscal_year_id' => $fiscal_year_id,
					//'month_id' => $month_id,
					'aso_id' => $office_id
				),
				'fields'=> array('sum(amount) AS total_amount'),
				'recursive' => 0
				)
			);	
			
			
			
			if($total_target_result[0][0]['total_amount']>0){
				//$total_target = ($total_target_result[0][0]['total_amount']/12);
				$total_target = ($total_target_result[0][0]['total_amount']);
			}else{
				$total_target = 0;
			}
			
			if($total_target>0){
				//$achievement_penchant = round(($achievement_result[0][0]['total_amount']*100)/$total_target);
				$achievement_penchant = round(($year_total_Revenue*100)/$total_target);
			}else{
				$achievement_penchant = 100;
			}
			
			$total_target = $this->priceSetting($total_target);
			//echo $achievement_penchant;
			//exit;
			//END Achievement Vs Target 
						
			
			
			//START PRODUCT SALES BAR GRAPH
			$m_1_total_amount =  $this->getSalesTotal('', date('Y-6', time()), $office_id);
			$m_2_total_amount =  $this->getSalesTotal('', date('Y-6', strtotime('-1 month', strtotime(date('Y-6-01')))), $office_id);
			$m_3_total_amount =  $this->getSalesTotal('', date('Y-6', strtotime('-2 month', strtotime(date('Y-6-01')))), $office_id);
			$m_4_total_amount =  $this->getSalesTotal('', date('Y-6', strtotime('-3 month', strtotime(date('Y-6-01')))), $office_id);
			$m_5_total_amount =  $this->getSalesTotal('', date('Y-6', strtotime('-4 month', strtotime(date('Y-6-01')))), $office_id);
			$m_6_total_amount =  $this->getSalesTotal('', date('Y-6', strtotime('-5 month', strtotime(date('Y-6-01')))), $office_id);
			
			$bar_series = $m_1_total_amount.', '.$m_2_total_amount.', '.$m_3_total_amount.', '.$m_4_total_amount.', '.$m_5_total_amount.', '.$m_6_total_amount;
			//END PRODUCT SALES BAR GRAPH
			
			$this->loadModel('ProductSetting');
	   		$product_settings = $this->ProductSetting->find('all', array('order'=>array('sort' => 'ASC')));
			$this->set(compact('product_settings'));
			
			
			$this->loadModel('ReportProductSetting');
	   		$report_products = $this->ReportProductSetting->find('all', array('order'=>array('sort' => 'ASC')));
			$this->set(compact('report_products'));
			
			
			$this->loadModel('PieProductSetting');
	   		$pie_products = $this->PieProductSetting->find('all', array('order'=>array('sort' => 'ASC')));
			$this->set(compact('pie_products'));
			
			$pie_data = '';
			
			$i=1;
			
			foreach($pie_products as $pie_product)
			{
				
				$pie_data.="<li><a>".$pie_product['Product']['name']." <span class='pull-right text-red'> ".$this->getAreaOfficeSales($office_id, $pie_product['PieProductSetting']['product_id'], $this->UserAuth->getOfficeId())."%</span></a></li>";
				//if($i==4){
				//break;
				//}
				$i++;
			}
						
			$this->set(compact('pie_data'));
			
					
			
			
			$this->set(compact('achievement_amount', 'total_target', 'achievement_penchant', 'bar_series',  'offices'));
			
			$region = $this->UserAuth->getOfficeId();	
			$this->set(compact('region'));		
		
		}
		else
		{
			//for admin
			$office_id = 0;
			$this->set(compact('office_id'));
			
			$region = 0;	
			$this->set(compact('region'));	
			
			
			
			
			
			//START TOP 16 BOXES Revenue & EC
			//Revenue & EC
			$today_SalesTotal = $this->getSalesTotal($box_data='today');
			$today_Revenue = $this->priceSetting($today_SalesTotal['total_Revenue']);
			$today_EC = $today_SalesTotal['total_EC'];
			$this->set(compact('today_Revenue', 'today_EC'));
			
			$week_SalesTotal = $this->getSalesTotal($box_data='week');
			
			
			
			$week_Revenue = $this->priceSetting($week_SalesTotal['total_Revenue']);
			$week_EC = $week_SalesTotal['total_EC'];
			$this->set(compact('week_Revenue', 'week_EC'));
			
			
			$month_SalesTotal = $this->getSalesTotal($box_data='month');
			$month_Revenue = $this->priceSetting($month_SalesTotal['total_Revenue']);
			$month_EC = $month_SalesTotal['total_EC'];
			$this->set(compact('month_Revenue', 'month_EC'));
			
			$year_SalesTotal = $this->getSalesTotal($box_data='year');
			$year_Revenue = $this->priceSetting($year_SalesTotal['total_Revenue']);
			$year_EC = $year_SalesTotal['total_EC'];
			$this->set(compact('year_Revenue', 'year_EC'));
			
			//OC 
			$today_OC = $this->getOCTotal($box_data='today');
			$week_OC = $this->getOCTotal($box_data='week');
			$month_OC = $this->getOCTotal($box_data='month');
			$year_OC = $this->getOCTotal($box_data='year');
			$this->set(compact('today_OC', 'week_OC', 'month_OC', 'year_OC'));
			
			
			//CYP 
			/*$today_CYP = $this->getCYPTotal($box_data='today');
			$week_CYP = $this->getCYPTotal($box_data='week');
			$month_CYP = $this->getCYPTotal($box_data='month');
			$year_CYP = $this->getCYPTotal($box_data='year');
			$this->set(compact('today_CYP', 'week_CYP', 'month_CYP', 'year_CYP'));*/
			
			//END TOP 16 BOXES
			
						
			$year_total_Revenue = $year_SalesTotal['total_Revenue'];
			$achievement_amount = $this->priceSetting($year_total_Revenue);
			
			//get total_target (Calculate all offices sales target for this year then divide by per month/12)
			$this->loadModel('SaleTarget');		
			$total_target_result = $this->SaleTarget->find('all', 
				array(
				'conditions'=> array(
					'fiscal_year_id' => $fiscal_year_id,
					'target_category' => 1,
					//'month_id' => $month_id,
					//'aso_id >' => 0
				),
				'fields'=> array('sum(amount) AS total_amount'),
				'recursive' => 0
				)
			);	
			
			
			
			if($total_target_result[0][0]['total_amount']>0){
				//$total_target = ($total_target_result[0][0]['total_amount']/12);
				$total_target = ($total_target_result[0][0]['total_amount']);
			}else{
				$total_target = 0;
			}
			
			if($total_target>0){
				//$achievement_penchant = round(($achievement_result[0][0]['total_amount']*100)/$total_target);
				$achievement_penchant = round(($year_total_Revenue*100)/$total_target);
				
			}else{
				$achievement_penchant = 100;
			}
			
			$total_target = $this->priceSetting($total_target);
			//END Achievement Vs Target 
						
			
			
			//START PRODUCT SALES BAR GRAPH
			$m_1_total_amount =  $this->getSalesTotal('', date('Y-6', time()));
			$m_2_total_amount =  $this->getSalesTotal('', date('Y-6', strtotime('-1 month', strtotime(date('Y-6-01')))));
			$m_3_total_amount =  $this->getSalesTotal('', date('Y-6', strtotime('-2 month', strtotime(date('Y-6-01')))));
			$m_4_total_amount =  $this->getSalesTotal('', date('Y-6', strtotime('-3 month', strtotime(date('Y-6-01')))));
			$m_5_total_amount =  $this->getSalesTotal('', date('Y-6', strtotime('-4 month', strtotime(date('Y-6-01')))));
			$m_6_total_amount =  $this->getSalesTotal('', date('Y-6', strtotime('-5 month', strtotime(date('Y-6-01')))));
			
			$bar_series = $m_1_total_amount.', '.$m_2_total_amount.', '.$m_3_total_amount.', '.$m_4_total_amount.', '.$m_5_total_amount.', '.$m_6_total_amount;
			//END PRODUCT SALES BAR GRAPH
			
			
			
			
			
			$this->loadModel('ProductSetting');
	   		$product_settings = $this->ProductSetting->find('all', array('order'=>array('sort' => 'ASC')));
			$this->set(compact('product_settings'));
			
			
			$this->loadModel('ReportProductSetting');
	   		$report_products = $this->ReportProductSetting->find('all', array('order'=>array('sort' => 'ASC')));
			$this->set(compact('report_products'));
			
			
			$this->loadModel('PieProductSetting');
	   		$pie_products = $this->PieProductSetting->find('all', array('order'=>array('sort' => 'ASC')));
			$this->set(compact('pie_products'));
			
			$pie_data = '';
			
			$i=1;
			foreach($pie_products as $pie_product)
			{
				$pie_data.="<li><a>".$pie_product['Product']['name']." <span class='pull-right text-red'> ".$this->getAreaOfficeSales(0, $pie_product['PieProductSetting']['product_id'], 0)."%</span></a></li>";
				//if($i==4){
				//break;
				//}
				$i++;
			}
						
			$this->set(compact('pie_data'));
			
			//exit;			
			
			//Territory Sales Performance Monitoring
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' => 2,
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
			
			
			
			
			$this->set(compact('achievement_amount', 'total_target', 'achievement_penchant', 'bar_series',  'offices'));
				
			/*//Region Dhaka
			$region_dhaka_offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' => 2,
					'parent_office_id' => 38,
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));	
			//pr($region_dhaka_offices);
			//exit;
			$this->set(compact('region_dhaka_offices'));
			
			//Region East
			$region_east_offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' => 2,
					'parent_office_id' => 20,
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));	
			$this->set(compact('region_east_offices'));
			
			//Region North
			$region_north_offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' => 2,
					'parent_office_id' => 21,
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
			$this->set(compact('region_north_offices'));	
			
			//Region South
			$region_south_offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' => 2,
					'parent_office_id' => 39,
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));	
			$this->set(compact('region_south_offices'));*/	
			
			
			//for region wise office list
			$region_offices = $this->Office->find('list', array(
				'conditions' => array('Office.office_type_id'=>3), 
				'order' => array('office_name' => 'asc')
			));
			$this->set(compact('region_offices'));
			
			$all_offices = $this->Office->find('all', array(
				'conditions'=> array(
					'Office.office_type_id' 	=> 2,
					//'parent_office_id' 	=> $region_office_id,
					
					"NOT" => array( "Office.id" => array(30, 31, 37))
					), 
				'order'=>array('Office.parent_office_id'=>'asc', 'Office.office_name'=>'asc'),
				'recursive' => -1
			));
			
			$all_offices_list = array();
			
			foreach($all_offices as $all_offices_results)
			{				
				$all_offices_list[$all_offices_results['Office']['parent_office_id']][$all_offices_results['Office']['id']] = $all_offices_results['Office']['office_name'];
			}
			$this->set(compact('all_offices_list'));
			//pr($all_offices_list);
			//exit;
			
			//Area wise territory list
			$this->loadModel('Territory');
			$all_territories = $this->Territory->find('all', array(
			'conditions'=> array(
				'Territory.is_active' => 1,
				'Territory.is_assigned' => 1
			), 
			'order'=>array('Territory.office_id'=>'asc', 'Territory.name'=>'asc'),
			'recursive' => -1
			));
			
			
			$all_territories_list = array();
			
			foreach($all_territories as $all_territories_results)
			{				
				$all_territories_list[$all_territories_results['Territory']['office_id']][$all_territories_results['Territory']['id']] = $all_territories_results['Territory']['name'];
			}
			$this->set(compact('all_territories_list'));
			//pr($all_territories_list);
			//exit;
			
			
		}
 		
		
	
	//$this -> layout = 'admin_index';
		
	}
	
	
	
	public function get_hours($datetime = '')
	{
		if($datetime == '')
		{
			return 100;
		}else{
			$dateDiff = intval((strtotime($this->current_datetime()) - strtotime($datetime))/60);
			$hours = intval($dateDiff/60);
			return $hours;
		}		
	}
	
	
	private function priceSetting($amount=0)
	{
		/*if($amount >= 1000 && $amount < 100000){
			$amount = sprintf("%01.2f", $amount/1000).' TH';
		}elseif($amount >= 100000 && $amount < 1000000){
			$amount = sprintf("%01.2f", $amount/100000).' Lac';
		}elseif($amount >= 1000000){
			$amount = sprintf("%01.2f", $amount/1000000).' M';
		}else{
			$amount = sprintf("%01.2f", $amount);
		}*/
		
		$amount = sprintf("%01.2f", $amount/1000000).' M';
		
		return $amount ;
	}
	
	
	
	private function getSalesTotal($box_data='', $date=0, $office_id=0)
	{	
		$this->loadModel('Memo');
			
		//get weekly date
		$this->loadModel('Week');		
		$c_date = date('Y-6-d');
		$w_results = $this->Week->find('first', 
			array(
			'conditions'=> array(
			'Week.start_date <=' => $c_date, 
			'Week.end_date >=' => $c_date, 
			),
			//'fields'=> array('sum(Memo.gross_value) AS total_Revenue, count(Memo.memo_no) AS total_EC'),
			'recursive' => -1
			)
		);
		
		if($w_results)
		{
			$week_start_date = $w_results['Week']['start_date'];
			$week_end_date = $w_results['Week']['end_date'];
		}
		else
		{
			$week_start_date=(date('D')=='Sun')?date("Y-6-d", strtotime('saturday this week')):date("Y-6-d", strtotime('saturday last week'));
			//$week_end_date = date($week_start_date, strtotime('+5 day'));
			$day = 60 * 60 * 24;
			$today = strtotime($week_start_date);
			$week_end_date = date("Y-6-d", $today + $day * 5);
		}
		//end for week
		
		//start fiscale year
		$this->loadModel('FiscalYear');	
		$y_results = $this->FiscalYear->find('first', 
			array(
			'conditions'=> array("FiscalYear.start_date <="=>date('Y-6-d'), "FiscalYear.end_date >="=>date('Y-6-d')),
			'recursive' => -1
			)
		);
		if($y_results){
			$year_start_date = $y_results['FiscalYear']['start_date'];
			$year_end_date = $y_results['FiscalYear']['end_date'];
		}else{
			$year_start_date=date('Y-07-01');
			$year_end_date = date('Y-07-01', strtotime('+1 year'));
		}
		//end fiscale year
		
				
		$month_date = date('Y-6-01');
		$year_date = date('Y-07-01');
		
				
		if($box_data)
		{
			$conditions = array();
			
			if($office_id)
			{
				if($box_data=='week')
				{
					$conditions = array(
					'memo_date >=' => $week_start_date, 
					'memo_date <=' => $week_end_date, 
					'Memo.status !=' => 0,
					'Memo.gross_value >' => 0,
					'Territory.office_id' => $office_id
					);
				}
				elseif($box_data=='month')
				{
					$conditions = array(
					'memo_date >=' => date('Y-6-01'), 
					'memo_date <=' => date('Y-6-d'), 
					'Memo.status !=' => 0,
					'Memo.gross_value >' => 0,
					'Territory.office_id' => $office_id
					);
				}
				elseif($box_data=='year')
				{
					$conditions = array(
					'memo_date >=' => $year_start_date, 
					'memo_date <=' => $year_end_date, 
					'Memo.status !=' => 0,
					'Memo.gross_value >' => 0,
					'Territory.office_id' => $office_id
					);
				}
				else
				{
					//for today
					$conditions = array(
					'memo_date' => date('Y-6-d'), 
					'Memo.status !=' => 0,
					'Memo.gross_value >' => 0,
					'Territory.office_id' => $office_id
					);
				}
			}
			else
			{
				if($box_data=='week'){
					//$conditions = array('memo_date >=' => $week_start_date, 'Memo.status !=' => 0);
					$conditions = array(
					'memo_date >=' => $week_start_date, 
					'memo_date <=' => $week_end_date, 
					'Memo.gross_value >' => 0,
					'Memo.status !=' => 0
					);
				}elseif($box_data=='month'){
					//$conditions = array('memo_date >=' => $month_date, 'Memo.status !=' => 0);
					$conditions = array(
					'memo_date >=' => date('Y-6-01'), 
					'memo_date <=' => date('Y-6-d'), 
					'Memo.gross_value >' => 0,
					'Memo.status !=' => 0
					);
				}elseif($box_data=='year'){
					//$conditions = array('memo_date >=' => $year_date, 'Memo.status !=' => 0);
					$conditions = array(
					'memo_date >=' => $year_start_date, 
					'memo_date <=' => $year_end_date, 
					'Memo.gross_value >' => 0,
					'Memo.status !=' => 0
					);
				}else{
					$conditions = array(
					'memo_date' => date('Y-6-d'), 
					'Memo.gross_value >' => 0, 
					'Memo.status !=' => 0
					);
				}
			}
			
			$result = $this->Memo->find('all', 
				array(
				'conditions'=> $conditions,
				'fields'=> array('sum(Memo.gross_value) AS total_Revenue, count(Memo.memo_no) AS total_EC')
				)
			);
			
			$data = array();
			
			if($result){
				$data['total_Revenue'] = $result[0][0]['total_Revenue'];
				$data['total_EC'] = $result[0][0]['total_EC'];
			}else{
				$data['total_Revenue'] = 0;
				$data['total_EC'] = 0;
			}
			
			return $data;
		}
		
		if($date)
		{
			$y_m = explode('-',$date);
			$year = $y_m[0];
			$month = $y_m[1];
			
			if($office_id){			
				$conditions = array(
				'YEAR(memo_date)' => $year, 
				'MONTH(memo_date)' => $month,  
				'Memo.status !=' => 0,
				'Territory.office_id' => $office_id
				);
			}else{
				$conditions = array(
				'YEAR(memo_date)' => $year, 
				'MONTH(memo_date)' => $month,  
				'Memo.status !=' => 0
				);
			}
			
			$result = $this->Memo->find('all', 
				array(
				'conditions'=> $conditions,
				
				'fields' => array('sum(Memo.gross_value) AS total_amount')
				)
			);
			return sprintf("%01.2f", $result[0][0]['total_amount']/1000000);
		}
		
		return false;
	}
	
	
	
	//for top boxes ajax data
	/*public function getSalesTotal2($box_data='', $office_id=0)
	{	
		$this->loadModel('Memo');
							
		//get weekly date
		$this->loadModel('Week');		
		$c_date = date('Y-6-d');
		$w_results = $this->Week->find('first', 
			array(
				'conditions'=> array(
				'Week.start_date <=' => $c_date, 
				'Week.end_date >=' => $c_date, 
			),
			'recursive' => -1
			)
		);
		
		if($w_results)
		{
			$week_start_date = $w_results['Week']['start_date'];
			$week_end_date = $w_results['Week']['end_date'];
		}
		else
		{
			$week_start_date=(date('D')=='Sun')?date("Y-6-d", strtotime('saturday this week')):date("Y-6-d", strtotime('saturday last week'));
			$week_end_date = date($week_start_date, strtotime('+5 day'));
		}
		//end for week
		
		
		//start fiscale year
		$this->loadModel('FiscalYear');	
		$y_results = $this->FiscalYear->find('first', 
			array(
			'conditions'=>array("FiscalYear.start_date <="=>date('Y-6-d'), "FiscalYear.end_date >="=>date('Y-6-d')),
			'recursive' => -1
			)
		);
		
		if($y_results){
			$year_start_date = $y_results['FiscalYear']['start_date'];
			$year_end_date = $y_results['FiscalYear']['end_date'];
		}else{
			$year_start_date = date('Y-07-01');
			$year_end_date = date('Y-07-01', strtotime('+1 year'));
		}
		//end fiscale year
		
						
		if($box_data && $office_id)
		{
			$conditions = array();
			
			if($box_data=='week')
			{
				$conditions = array(
				'memo_date >=' => $week_start_date, 
				'memo_date <=' => $week_end_date, 
				'Memo.status !=' => 0,
				'Territory.office_id' => $office_id
				);
			}
			elseif($box_data=='month')
			{
				$conditions = array(
				'memo_date >=' => date('Y-6-01'), 
				'memo_date <=' => date('Y-6-d'), 
				'Memo.status !=' => 0,
				'Territory.office_id' => $office_id
				);
			}
			elseif($box_data=='year')
			{
				$conditions = array(
				'memo_date >=' => $year_start_date, 
				'memo_date <=' => $year_end_date, 
				'Memo.status !=' => 0,
				'Territory.office_id' => $office_id
				);
			}
			else
			{
				//for today
				$conditions = array(
				'memo_date' => date('Y-6-d'), 
				'Memo.status !=' => 0,
				'Territory.office_id' => $office_id
				);
			}
			
			$result = $this->Memo->find('all', 
				array(
				'conditions'=> $conditions,
				'fields'=> array('sum(Memo.gross_value) AS total_Revenue, count(Memo.memo_no) AS total_EC'),
				'recursive' => 0
				)
			);
			
			
			
			$data = array();
			
			if($result){
				$data['total_Revenue'] = $result[0][0]['total_Revenue'];
				$data['total_EC'] = $result[0][0]['total_EC'];
			}else{
				$data['total_Revenue'] = 0;
				$data['total_EC'] = 0;
			}
			
			//pr($data);
			//exit;
			
			return $data;
		}
		
		$this->autoRender = false;
				
		return false;
	}*/
	
	public function admin_topBoxesData(){
		
		$office_id = $this->request->data['office_id'];	
		
		$region_office_id = $this->request->data['region_office_id'];	
		
		if(!$office_id)
		{
			$this->loadModel('Office');
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,
					
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
						
			$office_id = array_keys($offices);
		}
	
		$json = array();
		
		$today_SalesTotal = $this->getSalesTotal('today', 0, $office_id);
		$json['today_Revenue'] = $this->priceSetting($today_SalesTotal['total_Revenue']);
		$json['today_EC'] = $today_SalesTotal['total_EC'];
		
		$week_SalesTotal = $this->getSalesTotal('week', 0, $office_id);
		$json['week_Revenue'] = $this->priceSetting($week_SalesTotal['total_Revenue']);
		$json['week_EC'] = $week_SalesTotal['total_EC'];
		
		
		$month_SalesTotal = $this->getSalesTotal('month', 0, $office_id);
		$json['month_Revenue'] = $this->priceSetting($month_SalesTotal['total_Revenue']);
		$json['month_EC'] = $month_SalesTotal['total_EC'];
		
		$year_SalesTotal = $this->getSalesTotal('year', 0, $office_id);
		$json['year_Revenue'] = $this->priceSetting($year_SalesTotal['total_Revenue']);
		$json['year_EC'] = $year_SalesTotal['total_EC'];
		
		//OC 
		$json['today_OC'] = $this->getOCTotal('today', $office_id);
		$json['week_OC'] = $this->getOCTotal('week', $office_id);
		$json['month_OC'] = $this->getOCTotal('month', $office_id);
		$json['year_OC'] = $this->getOCTotal('year', $office_id);
		
		//CYP 
		$json['today_CYP'] = $this->getCYPTotal('today', $office_id);
		$json['week_CYP'] = $this->getCYPTotal('week', $office_id);
		$json['month_CYP'] = $this->getCYPTotal('month', $office_id);
		$json['year_CYP'] = $this->getCYPTotal('year', $office_id);

		
		echo json_encode($json);
		
		exit;
		
	}
	//end for top boxes ajax data
	
	
	private function getOCTotal($box_data='', $office_id=0)
	{
		if($box_data)
		{
			//get weekly date
			$this->loadModel('Week');		
			$c_date = date('Y-6-d');
			$w_results = $this->Week->find('first', 
				array(
				'conditions'=> array(
				'Week.start_date <=' => $c_date, 
				'Week.end_date >=' => $c_date, 
				),
				'recursive' => -1
				)
			);
			
			if($w_results){
				$week_start_date = $w_results['Week']['start_date'];
				$week_end_date = $w_results['Week']['end_date'];
			}else{
				$week_start_date=(date('D')=='Sun')?date("Y-6-d", strtotime('saturday this week')):date("Y-6-d", strtotime('saturday last week'));
				//$week_end_date = date($week_start_date, strtotime('+5 day'));
				$day = 60 * 60 * 24;
				$today = strtotime($week_start_date);
				$week_end_date = date("Y-6-d", $today + $day * 5);
			}
			//end for week
			
			//start fiscale year
			$this->loadModel('FiscalYear');	
			$y_results = $this->FiscalYear->find('first', 
				array(
				'conditions'=>array("FiscalYear.start_date <="=>date('Y-6-d'), "FiscalYear.end_date >="=>date('Y-6-d')),
				'recursive' => -1
				)
			);
			
			if($y_results){
				$year_start_date = $y_results['FiscalYear']['start_date'];
				$year_end_date = $y_results['FiscalYear']['end_date'];
			}else{
				$year_start_date = date('Y-07-01');
				$year_end_date = date('Y-07-01', strtotime('+1 year'));
			}
			//end fiscale year
			
			
			$month_date = date('Y-6-01');
			$year_date = date('Y-07-01');
			
			$conditions = array();
			
			if($office_id)
			{
				if($box_data=='week'){
					$conditions = array(
					'memo_date >=' => $week_start_date, 
					'memo_date <=' => $week_end_date, 
					'Memo.status !=' => 0,
					'Territory.office_id' => $office_id
					);
				}elseif($box_data=='month'){
					$conditions = array(
					'memo_date >=' => date('Y-6-01'), 
					'memo_date <=' => date('Y-6-d'), 
					'Memo.status !=' => 0,
					'Territory.office_id' => $office_id
					);
				}elseif($box_data=='year'){
					$conditions = array(
					'memo_date >=' => $year_start_date, 
					'memo_date <=' => $year_end_date, 
					'Memo.status !=' => 0,
					'Territory.office_id' => $office_id
					);
				}else{
					$conditions = array(
					'memo_date' => date('Y-6-d'), 
					'Memo.status !=' => 0,
					'Territory.office_id' => $office_id
					);
				}
			}
			else
			{
				if($box_data=='week'){
					//$conditions = array('memo_date >=' => $week_start_date, 'Memo.status !=' => 0);
					$conditions = array(
					'memo_date >=' => $week_start_date, 
					'memo_date <=' => $week_end_date, 
					'Memo.status !=' => 0
					);
				}elseif($box_data=='month'){
					//$conditions = array('memo_date >=' => $month_date, 'Memo.status !=' => 0);
					$conditions = array(
					'memo_date >=' => date('Y-6-01'), 
					'memo_date <=' => date('Y-6-d'), 
					'Memo.status !=' => 0
					);
				}elseif($box_data=='year'){
					//$conditions = array('memo_date >=' => $year_date, 'Memo.status !=' => 0);
					$conditions = array(
					'memo_date >=' => $year_start_date, 
					'memo_date <=' => $year_end_date, 
					'Memo.status !=' => 0
					);
				}else{
					$conditions = array('memo_date' => date('Y-6-d'), 'Memo.status !=' => 0);
				}
			}
									
			$result = $this->Memo->find('count', array(
			'conditions'=> $conditions,
			'fields' => 'COUNT(DISTINCT outlet_id) as count'
			));
			
			//pr($result);
			
			return $result;
			
		}
		
		return false;
	}
	
	
	
	
	private function getCYPTotal($box_data='', $office_id=0)
	{
		if($box_data)
		{
			//get weekly date
			$this->loadModel('Week');		
			$c_date = date('Y-6-d');
			$w_results = $this->Week->find('first', 
				array(
				'conditions'=> array(
				'Week.start_date <=' => $c_date, 
				'Week.end_date >=' => $c_date, 
				),
				'recursive' => -1
				)
			);
			
			if($w_results){
				$week_start_date = $w_results['Week']['start_date'];
				$week_end_date = $w_results['Week']['end_date'];
			}else{
				$week_start_date=(date('D')=='Sun')?date("Y-6-d", strtotime('saturday this week')):date("Y-6-d", strtotime('saturday last week'));
				//$week_end_date = date($week_start_date, strtotime('+5 day'));
				$day = 60 * 60 * 24;
				$today = strtotime($week_start_date);
				$week_end_date = date("Y-6-d", $today + $day * 5);
			}
			//end for week
			
			//start fiscale year
			$this->loadModel('FiscalYear');	
			$y_results = $this->FiscalYear->find('first', 
				array(
				'conditions'=>array("FiscalYear.start_date <="=>date('Y-6-d'), "FiscalYear.end_date >="=>date('Y-6-d')),
				'recursive' => -1
				)
			);
			
			if($y_results){
				$year_start_date = $y_results['FiscalYear']['start_date'];
				$year_end_date = $y_results['FiscalYear']['end_date'];
			}else{
				$year_start_date = date('Y-07-01');
				$year_end_date = date('Y-07-01', strtotime('+1 year'));
			}
			//end fiscale year
			
			
			$month_date = date('Y-6-01');
			$year_date = date('Y-07-01');
			
			
			//FOR CYP
			$conditions = array();
			
			if($box_data=='week'){
				$conditions = array(
				'memo_date >=' => $week_start_date, 
				'memo_date <=' => $week_end_date, 
				'Memo.status !=' => 0,
				);
			}elseif($box_data=='month'){
				$conditions = array(
				'memo_date >=' => date('Y-6-01'), 
				'memo_date <=' => date('Y-6-d'), 
				'Memo.status !=' => 0,
				);
			}elseif($box_data=='year'){
				$conditions = array(
				'memo_date >=' => $year_start_date, 
				'memo_date <=' => $year_end_date, 
				'Memo.status !=' => 0,
				);
			}else{
				$conditions = array(
				'memo_date' => date('Y-6-d'), 
				'Memo.status !=' => 0,
				);
			}

			if($office_id)$conditions['Territory.office_id'] = $office_id;			
									
			$results = $this->Memo->find('all', array(
						'conditions'=> $conditions,
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
						),
						'fields' => array('sum(MemoDetail.sales_qty) AS volume, sum(MemoDetail.sales_qty*MemoDetail.price) AS value', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v'),
						'group' => array('MemoDetail.product_id', 'cyp_cal', 'cyp'),
						'order' => array('MemoDetail.product_id DESC'),
						'recursive' => -1
					));
			
			
			
			$final_data = array();
			$sum_cyp = 0;
			$f_cyp = 0;
			foreach($results as $result)
			{
				$pro_qty =  @$result[0]['volume']?$result[0]['volume']:0;
				$cyp_cal =  @$result[0]['cyp']?$result[0]['cyp']:0;
				$cyp_val =  @$result[0]['cyp_v']?$result[0]['cyp_v']:0;
				
				if($cyp_cal)
				{
					if($cyp_cal=='*')$f_cyp =  @sprintf("%01.2f", $pro_qty*$cyp_val);
					if($cyp_cal=='/')$f_cyp =  @sprintf("%01.2f", $pro_qty/$cyp_val);
					if($cyp_cal=='-')$f_cyp =  @sprintf("%01.2f", $pro_qty-$cyp_val);
					if($cyp_cal=='+')$f_cyp =  @sprintf("%01.2f", $pro_qty+$cyp_val);
				}
				else
				{
					$f_cyp = 0;
				}
				
				//echo $cyp_cal.'<br>';
				//echo $f_cyp.'<br>';
				
				$sum_cyp+=$f_cyp;
				//echo $f_cyp.'<br>';
		   }
		   
		   //pr($results);
		   //exit;
			
			return $sum_cyp;
			
		}
		
		return false;
	}
	
	
	
	
	public function getProductSalesAndTarget($product_id = 0, $params = array(), $office_id = 0)
	{
		//pr($params);
		
		//START fiscal year & month CalCulation
		$this->loadModel('FiscalYear');	
		$fiscal_year_result = $this->FiscalYear->find('first', 
			array(
			'conditions'=> array("FiscalYear.start_date <=" => date('Y-6-d'), "FiscalYear.end_date >=" => date('Y-6-d')),
			'recursive' => -1
			)
		);

		//$fiscal_year_result = $GLOBALS['fiscal_year_result'];
		$fiscal_year_id = $fiscal_year_result['FiscalYear']['id'];
		
		$date_from = $fiscal_year_result['FiscalYear']['start_date'];
		$date_to = $fiscal_year_result['FiscalYear']['end_date'];
			
		if($params)
		{
			//$date_from = $params['date_start'];
			//$date_to = $params['date_end'];
			$office_id = $params['office_id']?$params['office_id']:0;
		}
		else
		{
			$office_id = $office_id;
			$total_office = 0;
		}
		
		
		
		$target_revenue = 0;
		
		if($product_id)
		{
			$data = array();
			
			
			//Achive Revenue
			$this->loadModel('MemoDetail');	
			$this->loadModel('Memo');	
			//$office_id = 26;
			if($office_id)
			{
				$con = array(
						'MemoDetail.product_id' => $product_id,
						'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
						'Memo.status !=' => 0,
						'Territory.office_id' => $office_id
					);
					
				$result = $this->Memo->find('all', 
					array(
					'conditions'=> $con,
					'joins' => array(
						array(
							'alias' => 'Territory',
							'table' => 'territories',
							'type' => 'INNER',
							'conditions' => 'Memo.territory_id = Territory.id'
						),
						array(
							'alias' => 'MemoDetail',
							'table' => 'memo_details',
							'type' => 'INNER',
							'conditions' => 'Memo.id = MemoDetail.memo_id'
						)
					),
					'fields'=> array('sum(MemoDetail.price*MemoDetail.sales_qty) AS total_Revenue'),
					'recursive' => -1
					)
				);
			}
			else
			{
				/*$result = $this->MemoDetail->find('all', 
					array(
					'conditions'=> array(
						'MemoDetail.product_id' => $product_id,
						'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
						'Memo.status !=' => 0
					),
					'fields'=> array('sum(MemoDetail.price*MemoDetail.sales_qty) AS total_Revenue'),
					'recursive' => 0
					)
				);*/
				
				/*$result = $this->Memo->find('all', 
					array(
					'conditions'=> array(
						'MemoDetail.product_id' => 27,
						'Memo.memo_date BETWEEN ? and ? ' => array('2017-07-01', '2018-06-30'),
						'Memo.status !=' => 0
					),
					'joins' => array(
						array(
							'alias' => 'MemoDetail',
							'table' => 'memo_details',
							'type' => 'INNER',
							'conditions' => 'Memo.id = MemoDetail.memo_id'
						)
					),
					'fields'=> array('sum(MemoDetail.price*MemoDetail.sales_qty) AS total_Revenue'),
					'recursive' => -1
					)
				);*/
				
				$sql = "SELECT sum(MemoDetail.price*MemoDetail.sales_qty) AS total_Revenue FROM [memo_details] AS [MemoDetail] LEFT JOIN [memos] AS [Memo] ON ([MemoDetail].[memo_id] = [Memo].[id]) WHERE [MemoDetail].[product_id] = ".$product_id." AND [Memo].[memo_date] BETWEEN '".$date_from."' and '".$date_to."' AND [Memo].[status] != 0";
				
				$result = $this->Memo->query($sql);
				
				
				
			}
			
			
			
			$achive_Revenue = $result[0][0]['total_Revenue'] ? $result[0][0]['total_Revenue'] : 0;
			//exit;
			
			
			//$achive_Revenue = $result[0][0]['total_Revenue'] ? $result[0][0]['total_Revenue'] : 0;
			$data['achive'] = round($achive_Revenue);
			
			
			//pr($result);
			
			
			//Target Revenue		
			//get month_id
			$this->loadModel('SaleTarget');	
							
			$targe_month_con = array(
					'product_id' => $product_id,
					'fiscal_year_id' => $fiscal_year_id,
					//'month_id' => $month_id,
					//'Territory.office_id' => $office_id
				);
				
			if($office_id){
				$targe_month_con['aso_id']=$office_id;	
			}else{
				$targe_month_con['target_category']=1;	
			}				
					
			$result = $this->SaleTarget->find('all', 
				array(
				'conditions'=> $targe_month_con,
				'fields'=> array('sum(amount) AS target_amount'),
				'recursive' => -1
				)
			);	
			
			//pr($result);
			//exit;
				
			$target_revenue = $result[0][0]['target_amount'] ? $result[0][0]['target_amount'] : 0;
							
			
			if($target_revenue > 0)
			{
				$data['target'] = round($target_revenue);
				$achievement_penchant = round(($achive_Revenue*100)/$target_revenue);
			}
			else
			{
				$data['target'] = 0;
				$achievement_penchant = 100;
			}
			
			if($achive_Revenue==0 && $monthly_target_Revenue==0){
				$achievement_penchant = 0;
			}
			
			$data['penchant'] = $achievement_penchant;
			//pr($data);
			//exit;
			return $data;
			
		}
		
		
		
		return false;
	}
	
	
	public function admin_ajaxProductSalesAndTarget()
	{
		
		$params['office_id'] = $this->request->data['office_id'];
		
		//$params['date_start'] = $this->request->data['date_start'];
		//$params['date_end'] = $this->request->data['date_end'];
		
		$region_office_id = $this->request->data['region_office_id'];	
		
		if(!$this->request->data['office_id'])
		{
			$this->loadModel('Office');
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,
					
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
						
			$params['office_id'] = array_keys($offices);
		}
		
				
		$this->loadModel('ProductSetting');
		$product_settings = $this->ProductSetting->find('all', array('order'=>array('sort' => 'ASC')));
		
		$html = '';
		
		foreach($product_settings as $settings)
		{
			$sales_data = $this->getProductSalesAndTarget($settings['ProductSetting']['product_id'], $params); 
					 
			 $html.=' <div class="progress-group">';
			 //$html.='<span class="progress-text">'.$settings['Product']['name'].'</span>';
			 $html.='<span class="progress-text">'.$settings['Product']['name'].' ('.$sales_data['penchant'].'%)</span>';
			 $html.='<span class="progress-number"><b>'.number_format($sales_data['achive']).'</b>/'.number_format($sales_data['target']).'</span>';
				$html.='<div class="progress sm">';
				  $html.='<div class="progress-bar" style="background-color:'.$settings['ProductSetting']['colour'].'; width: '.$sales_data['penchant'].'%"></div>';
				$html.='</div>';
			  $html.='</div>';
         }
		 
		 echo $html;
		 exit;
	}
	
	
	public function admin_userSyncData(){
		
		$this->loadModel('SalesPerson');		
		$this->loadModel('Memo');		
		$this->loadModel('MemoSyncHistory');
		
		$office_id = $this->request->data['office_id'];	
		
		$region_office_id = $this->request->data['region_office_id'];	
		
		if(!$office_id)
		{
			$this->loadModel('Office');
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,
					
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
						
			$office_id = array_keys($offices);
		}
		
		if($office_id)
		{
			$so_list = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id','SalesPerson.name','SalesPerson.last_data_push_time','Territory.name'),
				'conditions' => array(
					'SalesPerson.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					'User.user_group_id' => array(4, 1008)
				)
			));	
		}
		else
		{
			$so_list = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id','SalesPerson.name','SalesPerson.last_data_push_time','Territory.name'),
				'conditions' => array(
					//'Territory.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					'User.user_group_id' => array(4, 1008)
				)
			));
		}
		
		$data_array = array();
		$i=0;
		$total = count($so_list);
		
		/*foreach($so_list as $so)
		{ 
			$this->Memo->virtualFields = array(
				'total_memo' => 'COUNT(*)',
				'total_memo_value' => 'SUM(Memo.gross_value)'
			); 
			$memo1 = $this->Memo->find('first', array(
				'conditions' => array('Memo.sales_person_id' => $so['SalesPerson']['id'], 'Memo.status !=' => 0),
				'order' => array('Memo.updated_at desc'),
				//'order' => array('Memo.memo_date desc'),
				'fields' => array('memo_date', 'updated_at'),
				'recursive' => -1
			));
			$last_memo_sync_date = '';
			
			if(!empty($memo1))
			{
				$last_memo_sync_date = $memo1['Memo']['updated_at'];
				
			}else
			{
				$last_memo_sync_date = $this->current_date();
			}
			
			
			
			
			$so_last_push_time = $so['SalesPerson']['last_data_push_time'];
			
			$memo = $this->Memo->find('first', array(
				//'conditions' => array('Memo.sales_person_id' => $so['SalesPerson']['id'],'Memo.memo_date >=' => $last_memo_sync_date.' 00:00:00','Memo.memo_date <=' => $last_memo_sync_date.' 23:59:59'),
				//'conditions' => array('Memo.sales_person_id' => $so['SalesPerson']['id'], 'Memo.memo_date =' => $last_memo_sync_date, 'Memo.status !=' => 0),
				'conditions' => array('Memo.sales_person_id' => $so['SalesPerson']['id'], 'Memo.updated_at >' => $so_last_push_time, 'Memo.status !=' => 0),
				'fields' => array('total_memo', 'total_memo_value'),
				'recursive' => -1
			));
                                               			
			$memo_sync = $this->MemoSyncHistory->find('first', array(
				'conditions' => array('MemoSyncHistory.so_id' => $so['SalesPerson']['id'],'MemoSyncHistory.date' => $last_memo_sync_date),
				'fields' => array('total_memo'),
				'recursive' => -1
			));
                        
            $last_memo_info = $this->Memo->find('first', array(
				'conditions' => array('Memo.sales_person_id' => $so['SalesPerson']['id'], 'Memo.status !=' => 0),
				'fields' => array('Memo.memo_no', 'Memo.memo_time'),
                'order' => array('Memo.id' => 'desc'),
				'recursive' => -1
			));
			
			$data['name'] = $so['SalesPerson']['name'];
			$data['territory'] = $so['Territory']['name'];
			$data['time'] = $so['SalesPerson']['last_data_push_time'];
			$data['hours'] = $this->get_hours($so['SalesPerson']['last_data_push_time']);
			$data['total_sync_memo'] = (!empty($memo_sync) ? $memo_sync['MemoSyncHistory']['total_memo'] : 0);
			$data['total_memo'] = $memo['Memo']['total_memo'];
			$data['total_memo_value'] = $memo['Memo']['total_memo_value'];
                        
			if(!empty($last_memo_info))
			{
			$data['last_memo_no'] = $last_memo_info['Memo']['memo_no'];
			$data['last_memo_sync'] = $last_memo_info['Memo']['memo_time'];
			}
                        
			if($data['total_sync_memo']==$data['total_memo']){    
				$data_array[$total] = $data;
			}else{
				$data_array[$i] = $data;
			}
			
			$i++;
			$total++;
		}*/
		
		//for memo push
		foreach($so_list as $so){ 
		
			//pr($so);
			
			$this->Memo->virtualFields = array(
				'total_memo' => 'COUNT(*)',
				'total_memo_value' => 'SUM(Memo.gross_value)'
			); 
			
			//echo $so['SalesPerson']['id'];
			
			$memo1 = $this->Memo->find('first', array(
				'conditions' => array('Memo.sales_person_id' => $so['SalesPerson']['id'], 'Memo.status !=' => 0),
				'order' => array('Memo.updated_at desc'),
				//'order' => array('Memo.memo_date desc'),
				'fields' => array('memo_date', 'updated_at'),
				'recursive' => -1
			));
			$last_memo_sync_date = '';
			
			//pr($memo1);
			
			if(!empty($memo1))
			{
				$last_memo_sync_date = $memo1['Memo']['memo_date'];
				
			}
			else
			{
				$last_memo_sync_date = $this->current_date();
			}
			
			
			$so_last_push_time = $so['SalesPerson']['last_data_push_time'];
			
			/*$memo = $this->Memo->find('first', array(
				//'conditions' => array('Memo.sales_person_id' => $so['SalesPerson']['id'],'Memo.memo_date >=' => $last_memo_sync_date.' 00:00:00','Memo.memo_date <=' => $last_memo_sync_date.' 23:59:59'),
				//'conditions' => array('Memo.sales_person_id' => $so['SalesPerson']['id'], 'Memo.memo_date =' => $last_memo_sync_date, 'Memo.status !=' => 0),
				'conditions' => array(
					'Memo.sales_person_id' => $so['SalesPerson']['id'], 
					'Memo.updated_at >' => $so_last_push_time, 
					'Memo.status !=' => 0
				),
				'fields' => array('total_memo', 'total_memo_value'),
				'recursive' => -1
			));
			pr($memo);*/
			
			//new query from mmemo table with above condition and sum
			//echo $so_last_push_time.'<br>';
			$memo = $this->Memo->find('all', array(
				'conditions' => array(
					'Memo.sales_person_id' => $so['SalesPerson']['id'], 
					//'Memo.updated_at >=' => $last_memo_sync_date,
					'Memo.memo_date >=' => $last_memo_sync_date,  
					'Memo.status !=' => 0
				),
				'fields' => array('COUNT(Memo.memo_no) AS total_memo, sum(Memo.gross_value) AS total_memo_value'),
				'recursive' => -1
			));
			//pr($memo);
			
                                           			
			/*$memo_sync = $this->MemoSyncHistory->find('first', array(
				'conditions' => array('MemoSyncHistory.so_id' => $so['SalesPerson']['id'], 'MemoSyncHistory.date' => $last_memo_sync_date),
				'fields' => array('total_memo'),
				'recursive' => -1
			));*/
			
			//new query 2
			//echo $last_memo_sync_date.'<br>';
			$memo_sync = $this->MemoSyncHistory->find('all', array(
				'conditions' => array(
					'MemoSyncHistory.so_id' => $so['SalesPerson']['id'], 
					'MemoSyncHistory.date >=' => $last_memo_sync_date,
					'not' => array('MemoSyncHistory.missed_memo' => NULL)
				),
				//'fields' => array('sum(MemoSyncHistory.total_memo) as total_memo'),
				'fields' => array('sum(MemoSyncHistory.total_memo - MemoSyncHistory.missed_memo) as total_memo'),
				'recursive' => -1
			));
			
			//pr($memo_sync);
                        
            $last_memo_info = $this->Memo->find('first', array(
				'conditions' => array(
					'Memo.sales_person_id' => $so['SalesPerson']['id'], 
					'Memo.status !=' => 0
				),
				'fields' => array('Memo.memo_no', 'Memo.memo_time'),
                'order' => array('Memo.id' => 'desc'),
				'recursive' => -1
			));
			
			
			
			//$data['total_sync_memo'] = (!empty($memo_sync) ? $memo_sync['MemoSyncHistory']['total_memo'] : 0);
			
			$total_memo = (!empty($memo_sync[0][0]['total_memo']) && $memo_sync[0][0]['total_memo'] >=0) ? $memo_sync[0][0]['total_memo'] : 0;
			$total_sync_memo = (!empty($memo) ? $memo[0][0]['total_memo'] : 0);
			
			$total_waiting = $total_memo-$total_sync_memo;
			$data['total_waiting_sync_memo'] = ($total_waiting < 0) ? 0 : $total_waiting;
			
			$data['total_memo_value'] = $memo[0][0]['total_memo_value']?$memo[0][0]['total_memo_value']:0;
			
			$data['total_memo'] = $total_memo;
			$data['total_sync_memo'] = $total_sync_memo;
			$data['id'] = $so['SalesPerson']['id'];
			$data['name'] = $so['SalesPerson']['name'];
			$data['territory'] = $so['Territory']['name'];
			$data['time'] = $so['SalesPerson']['last_data_push_time'];
			$data['hours'] = $this->get_hours($so['SalesPerson']['last_data_push_time']);
                        
			if(!empty($last_memo_info))
			{
			$data['last_memo_no'] = $last_memo_info['Memo']['memo_no'];
			$data['last_memo_sync'] = $last_memo_info['Memo']['memo_time'];
			}
             
			/*if($data['total_sync_memo']==$data['total_memo']){    
				$data_array[$total] = $data;
			}else{
				$data_array[$i] = $data;
			}*/
			
			
			$data_array[] = $data;
			//$data_array[$data['total_waiting_sync_memo']] = $data;
			
			
			$i++;
			$total++;
			
			//if($i==1)break;
			
		}
		
		rsort($data_array);
		
		$output = '';
		
		if($data_array)
		{
			foreach($data_array as $so)
			{ 
				$time = $so['time'] ? date('d-M, Y h:i a', strtotime($so['time'])) : '';
				$last_memo_sync = isset($so['last_memo_sync']) ? date('d-M, Y h:i a', strtotime($so['last_memo_sync'])) : '';
				
				if($office_id)
				{
				
				$output .= '<tr>';
				
				//$label = ($so['hours'] > 24 ? 'danger' : 'success');
				$label = (isset($so['last_memo_sync']) && date('Y-6-d') == date('Y-6-d', strtotime($so['last_memo_sync'])) ? 'success' : 'danger');
				
				$output .= '<td><span class="label label-'.$label.'">'.$so['name'].'</span></td>';
				$output .= '<td>'.$so['territory'].'</td>';
				//$output .= '<td>'.$time.'</td>';
				$output .= '<td>'.$last_memo_sync.'</td>';
				
				//$output .= '<td class="text-center">'.$so['total_memo'].'</td>';
				//$output .= '<td class="text-center">'.$so['total_sync_memo'].'</td>';
				//$output .= '<td class="text-center">'.sprintf("%01.2f", $so['total_memo_value']).'</td>';
				
				$output .= '<td class="text-center">'.$so['total_waiting_sync_memo'].'</td>';
				
				$output .= '</tr>';
				}
				else 
				{
					/* checking the so that not synced last previous two days */
								
				$three_days_pre_date = date('Y-6-d', strtotime('-3 days'));								
				$need_shown = (isset($so['last_memo_sync']) && ($three_days_pre_date > date('Y-6-d', strtotime($so['last_memo_sync']))));
				
				if($need_shown)
				{
					
					$output .= '<tr>';
				
				//$label = ($so['hours'] > 24 ? 'danger' : 'success');
				$label = (isset($so['last_memo_sync']) && date('Y-6-d') == date('Y-6-d', strtotime($so['last_memo_sync'])) ? 'success' : 'danger');
				
				$output .= '<td><span class="label label-'.$label.'">'.$so['name'].'</span></td>';
				$output .= '<td>'.$so['territory'].'</td>';
				//$output .= '<td>'.$time.'</td>';
				$output .= '<td>'.$last_memo_sync.'</td>';
				
				//$output .= '<td class="text-center">'.$so['total_memo'].'</td>';
				//$output .= '<td class="text-center">'.$so['total_sync_memo'].'</td>';
				//$output .= '<td class="text-center">'.sprintf("%01.2f", $so['total_memo_value']).'</td>';
				
				$output .= '<td class="text-center">'.$so['total_waiting_sync_memo'].'</td>';
				
				$output .= '</tr>';
				}
				
				}
				
				
			}
		}
		else
		{
			//$output = '<tr><td colspan="7" class="text-center"><span class="label label-danger blink_me">No Result!</span></td></tr>';
			
		}
        
        echo $output;
		
		//pr($data_array);
		exit;
		
	}
	
	
	
	public function getAreaOfficeSales($office_id=0, $product_id=0, $region=0) {
		
		$this->loadModel('Memo');
		$this->loadModel('Office');
		
		
		
		if($region)
		{
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region,
					
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
			$r_office_ids = array_keys($offices);
		}
		
		//$data_from = date('Y-6-d', strtotime('-1 month', strtotime(date('Y-6-01'))));
		$data_from = date('Y-6-01');
		
		$condition = array(
				'Memo.memo_date >=' => $data_from,
				'Memo.status !=' => 0,
				'Memo.gross_value >' => 0
			);
			
		if($region)$condition['Memo.office_id']=$r_office_ids;
		
		//pr($condition);
			
		$total_sales = $this->Memo->find('all', 
			array(
			'conditions'=> $condition,
			'fields'=> array('sum(Memo.gross_value) AS total_Revenue'),
			'recursive' => -1
			)
		);
		//pr($total_sales);
		//exit;
				
		$total_sale = round($total_sales[0][0]['total_Revenue']?$total_sales[0][0]['total_Revenue']:0);
		
		
		//pr($office_id);
		
		if($office_id && !$product_id)
		{
			@$office_total_sales = $this->Memo->find('all', 
				array(
				'conditions'=> array(
					'Memo.memo_date >=' => $data_from,
					'Memo.office_id' => $office_id,
					//'Memo.territory_id ' => $territory_ids,
					'Memo.status !=' => 0
				),
				'fields'=> array('sum(Memo.gross_value) AS total_Revenue'),
				'recursive' => -1
				)
			);
			
			$office_sales = round($office_total_sales[0][0]['total_Revenue']?$office_total_sales[0][0]['total_Revenue']:0);
			//echo $office_sales;
			//exit;
			
			return $office_sales_percentage = sprintf("%01.2f", ($office_sales*100)/$total_sale);
		}
		
		
		
		
		//Product Percentage
		if($product_id > 0)
		{
			$con = array(
						'MemoDetail.product_id' => $product_id,
						'Memo.memo_date >=' => $data_from,
						'Memo.status !=' => 0
					);
			
			if($region)$con['Memo.office_id']=$r_office_ids;
			
			$product_total_sales = $result = $this->Memo->find('all', 
					array(
					'conditions'=> $con,
					'joins' => array(
						array(
							'alias' => 'MemoDetail',
							'table' => 'memo_details',
							'type' => 'INNER',
							'conditions' => 'Memo.id = MemoDetail.memo_id'
						)
					),
					'fields'=> array('sum(MemoDetail.price*MemoDetail.sales_qty) AS total_Revenue'),
					//'fields'=> array('MemoDetail.price', 'MemoDetail.sales_qty', 'Memo.memo_date', 'MemoDetail.product_id'),
					'recursive' => -1
					)
				);
				
			//pr($product_total_sales);
			//echo $total_sale.'<br>';
			
			$product_sales = $product_total_sales[0][0]['total_Revenue'];
								
			return @$product_sales_percentage = sprintf("%01.2f", ($product_sales*100)/$total_sale);
		}
		
		//exit;
		
		return false;
		
	}
	
	
	public function getAreaOfficeTerritorySales($territory_id=0, $product_id=0, $office_id=0) {
		
		$this->loadModel('Memo');
		
		
		/*$office_id = $GLOBALS['OfficeId'];
		
		$this->loadModel('Territory');
		$territories = $this->Territory->find('list', array('conditions'=> array('office_id' => $office_id), 'order'=>array('name'=>'asc')));
		
		$territory_ids = array();
		foreach($territories as $key => $value ){
			array_push($territory_ids, $key);
		}*/
		
		
		
		//$data_from = date('Y-6-d', strtotime('-1 month', strtotime(date('Y-6-01'))));
		$data_from = date('Y-6-01');
		
		//count office total sales
		@$total_sales = $this->Memo->find('all', 
			array(
			'conditions'=> array(
				'Memo.memo_date >=' => $data_from,
				'Memo.status !=' => 0,
				//'Territory.office_id' => $office_id
				'Memo.office_id ' => $office_id,
			),
			'fields'=> array('sum(Memo.gross_value) AS total_Revenue'),
			'recursive' => -1
			)
		);
		//pr($total_sales);
		//exit;
		
		$total_sale = round($total_sales[0][0]['total_Revenue']?$total_sales[0][0]['total_Revenue']:0);
		
		if($territory_id > 0)
		{
			$territory_total_sales = $this->Memo->find('all', 
				array(
				'conditions'=> array(
					'Memo.memo_date >=' => $data_from,
					'Memo.territory_id ' => $territory_id,
					'Memo.status !=' => 0
				),
				'fields'=> array('sum(Memo.gross_value) AS total_Revenue'),
				'recursive' => -1
				)
			);
			$territory_sales = round($territory_total_sales[0][0]['total_Revenue']?$territory_total_sales[0][0]['total_Revenue']:0);
			return $territory_sales_percentage = sprintf("%01.2f", ($territory_sales*100)/$total_sale);
		}
		
		
		//Product Percentage
		if($product_id > 0)
		{
			$this->loadModel('MemoDetail');	
			@$product_total_sales = $result = $this->Memo->find('all', 
					array(
					'conditions'=> array(
						'MemoDetail.product_id' => $product_id,
						'Memo.office_id ' => $office_id,
						'Memo.memo_date >=' => $data_from,
						'Memo.status !=' => 0
					),
					'joins' => array(
						array(
							'alias' => 'MemoDetail',
							'table' => 'memo_details',
							'type' => 'INNER',
							'conditions' => 'Memo.id = MemoDetail.memo_id'
						)
					),
					//'fields'=> array('sum(MemoDetail.price) AS total_Revenue'),
					'fields'=> array('sum(MemoDetail.price*MemoDetail.sales_qty) AS total_Revenue'),
					//'fields'=> array('MemoDetail.price', 'MemoDetail.sales_qty', 'Memo.memo_date', 'MemoDetail.product_id'),
					'recursive' => -1
					)
				);
				
			
			
			/*$product_sales = 0;
			foreach($product_total_sales as $val){
				$product_sales += $val['MemoDetail']['price']*$val['MemoDetail']['sales_qty'];
			}*/
			
			$product_sales = $product_total_sales[0][0]['total_Revenue'];
			
			
					
			return @$product_sales_percentage = sprintf("%01.2f", ($product_sales*100)/$total_sale);
		}
		
		return false;
		
	}
	
	
	
	public function getStocks($product_id=0, $office_id=0) {
		
		$this->loadModel('CurrentCwhInventory');
        $this->loadModel('Store');
		
		$data = array();
		
		if($office_id)
		{
			$so_con = array(
			'CurrentCwhInventory.inventory_status_id' => 1,
			'Store.store_type_id' => 3,
			'Store.office_id' => $office_id,
			'CurrentCwhInventory.product_id' => $product_id,
			'CurrentCwhInventory.transaction_date' => date('Y-6-d')
			);
			
			$aso_con = array(
			'CurrentCwhInventory.inventory_status_id' => 1,
			'Store.store_type_id' => 2,
			'Store.office_id' => $office_id,
			'CurrentCwhInventory.product_id' => $product_id,
			'CurrentCwhInventory.transaction_date' => date('Y-6-d')
			);
		}
		else
		{
			$so_con = array(
			'CurrentCwhInventory.inventory_status_id' => 1,
			'Store.store_type_id' => 3,
			'CurrentCwhInventory.product_id' => $product_id,
			'CurrentCwhInventory.transaction_date' => date('Y-6-d')
			);
			
			$aso_con = array(
			'CurrentCwhInventory.inventory_status_id' => 1,
			'Store.store_type_id' => 2,
			'CurrentCwhInventory.product_id' => $product_id,
			'CurrentCwhInventory.transaction_date' => date('Y-6-d')
			);
		}
		
		//get SO stock qty
        $results = $this->CurrentCwhInventory->find('all', array(
            'conditions' => $so_con,
            'joins' => array( array(
					'alias' => 'Store',
                    'table' => 'stores',
                    
                    'type' => 'INNER',
                    'conditions' => array(
                        'CurrentCwhInventory.store_id = Store.id'
                    )
                ) ),
            'fields'=> array('sum(CurrentCwhInventory.qty) AS so_qty'),
			'recursive' => -1,
        ));
		$data['so_stock'] = $results[0][0]['so_qty'];
		
		//get ASO stock qty
        $results = $this->CurrentCwhInventory->find('all', array(
            'conditions' => $aso_con,
            'joins' => array( array(
					'alias' => 'Store',
                    'table' => 'stores',
                    
                    'type' => 'INNER',
                    'conditions' => array(
                        'CurrentCwhInventory.store_id = Store.id'
                    )
                ) ),
            'fields'=> array('sum(CurrentCwhInventory.qty) AS aso_qty'),
			'recursive' => -1,
        ));
		$data['aso_stock'] = $results[0][0]['aso_qty'];
		
		
		//get CWH stock qty
		$cwh_con = array(
			'CurrentCwhInventory.region_office_id' => 0,
			'CurrentCwhInventory.store_type' => 'CWH',
			//'Store.office_id' => $office_id,
			'CurrentCwhInventory.product_id' => $product_id,
			'CurrentCwhInventory.transaction_date' => date('Y-6-d')
			);
        $results = $this->CurrentCwhInventory->find('all', array(
            'conditions' => $cwh_con,
            'joins' => array( array(
					'alias' => 'Store',
                    'table' => 'stores',
                    'type' => 'INNER',
                    'conditions' => array(
                        'CurrentCwhInventory.store_id = Store.id'
                    )
                ) ),
            'fields'=> array('sum(CurrentCwhInventory.qty) AS aso_qty'),
			'recursive' => -1,
        ));
		if(!$office_id){
			$data['cwh_stock'] = $results[0][0]['aso_qty'];
		}else{
			$data['cwh_stock'] = 0;
		}
		
		return $data;
		
	}
	
	//stock status ajax data
	public function admin_stockStatusData(){
		
		$office_id = $this->request->data['office_id'];	
		
		$region_office_id = $this->request->data['region_office_id'];	
		
		if(!$office_id)
		{
			$this->loadModel('Office');
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,
					
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
						
			$office_id = array_keys($offices);
		}
	
		$json = array();
		
		$this->loadModel('ReportProductSetting');
		
	    $report_products = $this->ReportProductSetting->find('all', array('order'=>array('sort' => 'ASC')));
		
		$output = '';
		
		foreach($report_products as $report_product)
		{
			$data = $this->getStocks($report_product['ReportProductSetting']['product_id'], $office_id);
			
			$output.= '<tr>';
				$output.='<td>'.$report_product['Product']['name'].'</td>';
				$output.='<td class="text-right">'.number_format($data['so_stock']).'</td>';
				$output.='<td class="text-right">'.number_format($data['aso_stock']).'</td>';
				$output.='<td class="text-right">'.number_format(($data['cwh_stock'])).'</td>';
				$output.='<td class="text-right">'.number_format(($data['so_stock']+$data['aso_stock']+$data['cwh_stock'])).'</td>';
			$output.= '</tr>';
			//break;
		}
		
		//echo html_entity_decode($output);
		//exit;
				
		$json['output'] = html_entity_decode($output);
		
		echo json_encode($json);
		
		$this->autoRender = false;
		
	}
	//end for stock status ajax data
	
	
	//achievement vs target ajax data
	public function admin_achievementTargetData(){
		
		$office_id = $this->request->data['office_id'];	
		$region_office_id = $this->request->data['region_office_id'];	
		
		if(!$office_id)
		{
			$this->loadModel('Office');
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,
					
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
						
			$office_id = array_keys($offices);
		}
		
		
		//pr($office_id);
		
		//START fiscal year & month CalCulation
		$this->loadModel('FiscalYear');	
		$fiscal_year_result = $this->FiscalYear->find('first', 
			array(
			'conditions'=> array("FiscalYear.start_date <=" => date('Y-6-d'), "FiscalYear.end_date >=" => date('Y-6-d')),
			'recursive' => -1
			)
		);
		
		$fiscal_year_id = $fiscal_year_result['FiscalYear']['id'];
					
		
	
		$json = array();
		
		$this->loadModel('Memo');
	    //$data_from = date('Y-6-01');
		
		
		$date_from = $fiscal_year_result['FiscalYear']['start_date'];
		$date_to = $fiscal_year_result['FiscalYear']['end_date'];
			
			
		
		if($office_id || $region_office_id){
			$achievement_result = $this->Memo->find('all', 
				array(
				'conditions'=> array(
					//'Memo.memo_date >=' => $data_from,
					'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					'Territory.office_id' => $office_id,
					'Memo.status !=' => 0
				),
				'fields'=> array('sum(Memo.gross_value) AS total_amount'),
				'recursive' => 0
				)
			);
		}else{
			$achievement_result = $this->Memo->find('all', 
				array(
				'conditions'=> array(
					//'Memo.memo_date >=' => $data_from,
					'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					//'Territory.office_id' => $office_id,
					'Memo.status !=' => 0
				),
				'fields'=> array('sum(Memo.gross_value) AS total_amount'),
				'recursive' => -1
				)
			);
		}
		
		$achievement_amount = $this->priceSetting($achievement_result[0][0]['total_amount']);
		$json['achievement_amount'] = $achievement_amount;
		
		
		
		//get total_target
		$this->loadModel('SaleTarget');		
		if($office_id || $region_office_id){			
			$total_target_result = $this->SaleTarget->find('all', 
				array(
				'conditions'=> array(
					'fiscal_year_id' => $fiscal_year_id,
					//'month_id' => $month_id,
					'aso_id' => $office_id
				),
				'fields'=> array('sum(amount) AS total_amount'),
				'recursive' => 0
				)
			);	
		}else{
			$total_target_result = $this->SaleTarget->find('all', 
				array(
				'conditions'=> array(
					'fiscal_year_id' => $fiscal_year_id,
					'target_category' => 1,
					//'month_id' => $month_id,
					//'aso_id' => $office_id
				),
				'fields'=> array('sum(amount) AS total_amount'),
				'recursive' => 0
				)
			);
		}
				
		
		if($total_target_result[0][0]['total_amount']>0){
			//$total_target = ($total_target_result[0][0]['total_amount']/12);
			$total_target = ($total_target_result[0][0]['total_amount']);
		}else{
			$total_target = 0;
		}
		$json['total_target'] = $this->priceSetting($total_target);
		
		
		if($total_target>0)
		{
			$achievement_penchant = round(($achievement_result[0][0]['total_amount']*100)/$total_target);
		}
		else
		{
			$achievement_penchant = 100;
		}
		
		$json['achievement_penchant'] = $achievement_penchant;
		
		echo json_encode($json);
		
		$this->autoRender = false;
		
	}
	//end for achievement vs target ajax data
	
	
	//stock status ajax data
	public function admin_salesTrendData(){
		
		$office_id = $this->request->data['office_id'];	
		
		$region_office_id = $this->request->data['region_office_id'];	
		
		if(!$office_id)
		{
			$this->loadModel('Office');
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,
					
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
						
			$office_id = array_keys($offices);
		}
		
	
		$json = array();
		
		$m_1_total_amount =  $this->getSalesTotal('', date('Y-6', time()), $office_id);
		$m_2_total_amount =  $this->getSalesTotal('', date('Y-6', strtotime('-1 month', strtotime(date('Y-6-01')))), $office_id);
		$m_3_total_amount =  $this->getSalesTotal('', date('Y-6', strtotime('-2 month', strtotime(date('Y-6-01')))), $office_id);
		$m_4_total_amount =  $this->getSalesTotal('', date('Y-6', strtotime('-3 month', strtotime(date('Y-6-01')))), $office_id);
		$m_5_total_amount =  $this->getSalesTotal('', date('Y-6', strtotime('-4 month', strtotime(date('Y-6-01')))), $office_id);
		$m_6_total_amount =  $this->getSalesTotal('', date('Y-6', strtotime('-5 month', strtotime(date('Y-6-01')))), $office_id);
		
		//$bar_series = $m_1_total_amount.', '.$m_2_total_amount.', '.$m_3_total_amount.', '.$m_4_total_amount.', '.$m_5_total_amount.', '.$m_6_total_amount;
		
				
		$json['m_1_total_amount'] = $m_1_total_amount;
		$json['m_2_total_amount'] = $m_2_total_amount;
		$json['m_3_total_amount'] = $m_3_total_amount;
		$json['m_4_total_amount'] = $m_4_total_amount;
		$json['m_5_total_amount'] = $m_5_total_amount;
		$json['m_6_total_amount'] = $m_6_total_amount;
		
		echo json_encode($json);
		
		$this->autoRender = false;
		
	}
	//end for stock status ajax data
	
}
