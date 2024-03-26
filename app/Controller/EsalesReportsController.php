<?php
App::uses('AppController', 'Controller');
/**
 * EsalesSettings Controller
 *
 * @property EsalesReport $EsalesReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class EsalesReportsController extends AppController {
	
	

/**
 * Components
 *
 * @var array
 */
 
	public $uses = array('ReportEsalesSetting', 'Memo', 'Office', 'Territory', 'OutletCategory', 'Outlet');
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
		
		ini_set('memory_limit', '1024M');
		ini_set('max_execution_time', 300); //300 seconds = 5 minutes
				
		//$this->loadModel('Memo');
		
		//$this->Session->write('Person.eyeColor', 'Green');
		//echo $green = $this->Session->read('Person.eyeColor');
		//exit;
		
		$this->set('page_title', 'Esales Sales Report');
		
		$year = date('Y');
		$year2 = date('Y')+1;
		
		$date_list = array(
			$year.'-07' => date('M-Y', strtotime($year.'-07')),
			$year.'-08' => date('M-Y', strtotime($year.'-08')),
			$year.'-09' => date('M-Y', strtotime($year.'-09')),
			$year.'-10' => date('M-Y', strtotime($year.'-10')),
			$year.'-11' => date('M-Y', strtotime($year.'-11')),
			$year.'-12' => date('M-Y', strtotime($year.'-12')),
			
			$year2.'-01' => date('M-Y', strtotime($year2.'-01')),
			$year2.'-02' => date('M-Y', strtotime($year2.'-02')),
			$year2.'-03' => date('M-Y', strtotime($year2.'-03')),
			$year2.'-04' => date('M-Y', strtotime($year2.'-04')),
			$year2.'-05' => date('M-Y', strtotime($year2.'-05')),
			$year2.'-06' => date('M-Y', strtotime($year2.'-06')),
		);
		$this->set(compact('date_list'));
		
		
		$type_list = array(
			'1' => 'Monthly Sales',
			'2' => 'Total Sales'
		);
		$this->set(compact('type_list'));
		
		
		
		
		
		//for region office list
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id'=>3), 
			'order' => array('office_name' => 'asc')
		));
		$this->set(compact('region_offices'));
		
		
		//for outlet category list
		$outlet_categories = $this->OutletCategory->find('list', array(
			'conditions' => array('is_active'=>1), 
			'order' => array('category_name' => 'asc')
		));
		$this->set(compact('outlet_categories'));
		
		
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		
		$this->set(compact('office_parent_id'));
		
		if ($office_parent_id == 0)
		{
			if ($this->request->is('post') || $this->request->is('put')){
				$office_conditions = array('Office.office_type_id'=>2, 'Office.parent_office_id'=>$this->data['EsalesReports']['region_office_id']);
			}else{
				$office_conditions = array('Office.office_type_id'=>2);
			}			
			$office_id = 0;
		} 
		else 
		{
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id'=>2);
			$office_id = $this->UserAuth->getOfficeId();
		}
		
		$this->set(compact('office_id'));
		
		$report_esales_setting_id = array();
		
		if($office_id || $this->request->is('post') || $this->request->is('put'))
		{
			$office_id = isset($this->request->data['EsalesReports']['office_id']) != '' ? $this->request->data['EsalesReports']['office_id'] : $office_id;
			
			$territories = $this->Territory->find('list', array(
				'conditions' => array('Territory.office_id' => $office_id),
				'order' => array('Territory.name' => 'asc')
				));	
				
			$this->set(compact('territories'));
			
			
			if(!empty($this->request->data['EsalesReports']['territory_id'])){
				$t_con = array('Territory.id' => $this->request->data['EsalesReports']['territory_id'], 'Territory.office_id' => $office_id);
			}else{
				$t_con = array('Territory.office_id' => $office_id);
			}
			
			$territories_2 = $this->Territory->find('list', array(
				'conditions' => $t_con,
				'order' => array('Territory.name' => 'asc')
				));
			$this->Session->write('territories_2', $territories_2);
			$this->set(compact('territories_2'));
		}
		
		
		
		$outlet_type = 1;		
		$ranks_2_conditions = array('type' => $outlet_type);	
		$region_offices_2_conditions = array('Office.office_type_id' => 3);	
						
		if ($this->request->is('post') || $this->request->is('put')) 
		{			
			$request_data = $this->request->data;
			//pr($request_data);
			//exit;
			
			$date_from = $this->data['EsalesReports']['date_from'];
			$date_to = $this->data['EsalesReports']['date_to'];
			$this->set(compact('date_from', 'date_to', 'request_data'));
			
						
			if(!empty($this->data['EsalesReports']['region_office_id']))
			{
				$region_offices_2_conditions = array(
					'Office.office_type_id' => 3,
					'Office.id' => $this->data['EsalesReports']['region_office_id']
				);
			}			
			
			$outlet_type = $this->data['EsalesReports']['outlet_type'];
			if(!empty($this->data['EsalesReports']['report_esales_setting_id']))
			{
				$report_esales_setting_id = $this->data['EsalesReports']['report_esales_setting_id'];
				$ranks_2_conditions = array(
					'type' => $outlet_type,
					'id' => $report_esales_setting_id
				);	
			}else{
				$ranks_2_conditions = array(
					'type' => $outlet_type,
				);
			}
			
			/*pr($this->data);
			exit;*/
			
			$detail_output = array();
			if(@!$this->data['summary'])
			{
				$detail_results = $this->getEsalesDetailOutlet($request_data);
				$this->set(compact('detail_results'));
				
				$detail_output = '';
				foreach($detail_results as $detail_result)
				{
				   $detail_output .='<tr class="rowDataSd">
						<td>'.$detail_result['si_no'].'</td>
						<td>'.$detail_result['sales_area'].'</td>
						<td>'.$detail_result['outlet_id'].'</td>
						<td>'.$detail_result['total_memo_value'].'</td>
						<td>'.$detail_result['avg_monthly_memo_value'].'</td>
						<td>'.$detail_result['total_effective_call_(EC)'].'</td>
						<td>'.$detail_result['pharmacy_name'].'</td>
						<td>'.$detail_result['market'].'</td>
						<td>'.$detail_result['territory'].'</td>
						<td>'.$detail_result['thana'].'</td>
						<td>'.$detail_result['district'].'</td>
						<td>'.$detail_result['division'].'</td>
						<td>'.$detail_result['rank'].'</td>
					</tr>';
				}
			}
			$this->set(compact('detail_output'));
			
		}
		
		//for office list
		$office_conditions['NOT']=array( "id" => array(30, 31, 37)); 
		//pr($office_conditions);
		//exit;
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		
		
		//for rank list
		$ranks = $this->ReportEsalesSetting->find('all', array(
			'conditions' => array('type'=>$outlet_type), 
			'order' => array('id' => 'asc')
		));
				
		//for region office list
		$region_offices_2 = $this->Office->find('list', array(
			'conditions' => $region_offices_2_conditions, 
			'order' => array('office_name' => 'asc')
		));
		$this->Session->write('region_offices_2', $region_offices_2);
						
		//for rank list
		$ranks_2 = $this->ReportEsalesSetting->find('all', array(
			'conditions' => $ranks_2_conditions, 
			'order' => array('id' => 'asc')
		));
		
		$this->set(compact('offices', 'outlet_type', 'ranks', 'ranks_2', 'report_esales_setting_id', 'region_offices_2'));
		
	}



/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add($id = null) {}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {}
	
	//for summary report
	public function getEsalesSum($request_data = array(), $office_id=0, $operator_1='', $range_start=0, $operator_2='', $range_end=0){
		
		//pr($request_data);
		
		$date_from = date('Y-m-d', strtotime($request_data['EsalesReports']['date_from']));
		//$date_to = date('Y-m-t', strtotime($request_data['EsalesReports']['date_to']));
		$date_to = date('Y-m-d', strtotime($request_data['EsalesReports']['date_to']));
		
		//month count
		$date1 = $date_from;
		$date2 = $date_to;
		$output = [];
		$time   = strtotime($date1);
		$last   = date('Y-m', strtotime($date2));
		do {
			$month = date('Y-m', $time);
			$total = date('t', $time);
		
			$output[] = [
				'month' => $month,
				'total_days' => $total,
			];
		
			$time = strtotime('+1 month', $time);
		} while ($month != $last);
		$month_list = $output;
		$total_month = count($month_list);
		
		
		$sales_data = array();
		
		
		
		if ($office_id) 
		{
			/*if(!empty($request_data['EsalesReports']['outlet_category_id'])){
				$conditions = array(
					'Office.id' => $office_id,
					'Outlet.category_id' => $request_data['EsalesReports']['outlet_category_id'],
					'Memo.memo_date BETWEEN ? and ?'=>array($date_from, $date_to),
					'Memo.status !=' => 0
				);
			}else{
				$conditions = array(
					'Office.id' => $office_id,
					'Memo.memo_date BETWEEN ? and ?'=>array($date_from, $date_to),
					'Memo.status !=' => 0
				);
			}*/
			
			//pr($conditions);			
						
			/*$outlet_lists = $this->Outlet->find('all', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'Market.territory_id = Territory.id'
					),
					array(
						'alias' => 'Office',
						'table' => 'offices',
						'type' => 'INNER',
						'conditions' => 'Territory.office_id = Office.id'
					),
					array(
						'alias' => 'Memo',
						'table' => 'memos',
						'type' => 'INNER',
						'conditions' => 'Outlet.id = Memo.outlet_id'
					)
				),
				'fields' => array(
				'Outlet.id', 'sum(Memo.gross_value) as total'
				),
				'group' => array('Outlet.id'),
				//'order'=>   array('Outlet.id' => 'desc'),
				//'limit' => 100,
				'recursive' => 1  
			));*/
			
			//pr($outlet_lists);
			
			
			
			//AND o.category_id IN(7)
			
						
			if(!empty($request_data['EsalesReports']['outlet_category_id']))
			{
				$outlet_category_ids = join(",", $request_data['EsalesReports']['outlet_category_id']);  
				$sql = "SELECT o.id as outlet_id, sum(m.gross_value) as total FROM outlets as o 
				INNER JOIN memos as m ON(o.id=m.outlet_id)
				INNER JOIN territories as t ON(m.territory_id=t.id)
				WHERE t.office_id=".$office_id." AND o.id=m.outlet_id AND o.category_id IN(".$outlet_category_ids.") AND m.memo_date BETWEEN '".$date_from."' and '".$date_to."' AND m.status>0 GROUP BY o.id";
			}
			else
			{
				$sql = "SELECT o.id as outlet_id, sum(m.gross_value) as total FROM outlets as o 
				INNER JOIN memos as m ON(o.id=m.outlet_id)
				INNER JOIN territories as t ON(m.territory_id=t.id)
				WHERE t.office_id=".$office_id." AND o.id=m.outlet_id  AND m.memo_date BETWEEN '".$date_from."' and '".$date_to."' AND m.status>0 GROUP BY o.id";
			}
			
			//echo $sql;
			//exit;
			
			$outlet_lists = $this->Outlet->query($sql);
			//pr($outlet_lists);
			//exit;
			
			//SessionComponent::write('outlet_lists', $outlet_lists);
			
			//$this->Session->write('outlet_lists', $outlet_lists);
			
			$outlet_type = $request_data['EsalesReports']['outlet_type'];
			$count_outlet = 0;
			
			foreach($outlet_lists as $outlet_result)
			{
				$office_sales = round($outlet_result[0]['total']?$outlet_result[0]['total']:0);
				
				if($request_data['EsalesReports']['outlet_type']==1){
					$office_sales = $office_sales/$total_month;
				}
				
				//echo $office_sales.'<br>';
				
				if($range_start && $range_start > 0 && !$range_end)
				{
					if($office_sales >= $range_start ){
						$count_outlet++;
					}
				}
				
				if($range_start && $range_start > 0 && $range_end && $range_end > 0)
				{
					if($office_sales >= $range_start && $office_sales < $range_end){
						$count_outlet++;
					}
				}
				
				if($range_end && $range_end > 0 && !$range_start)
				{
					if($office_sales < $range_end ){
						$count_outlet++;
					}
				}
				
			}
			
			return $count_outlet;
							
        }
	}
	
	//for summary report
	public function getEsalesSumTerritory($request_data = array(), $territory_id=0, $operator_1='', $range_start=0, $operator_2='', $range_end=0)
	{
		
		
		$date_from = date('Y-m-d', strtotime($request_data['EsalesReports']['date_from']));
		//$date_to = date('Y-m-t', strtotime($request_data['EsalesReports']['date_to']));
		$date_to = date('Y-m-d', strtotime($request_data['EsalesReports']['date_to']));
		
		//month count
		$date1 = $date_from;
		$date2 = $date_to;
		$output = [];
		$time   = strtotime($date1);
		$last   = date('Y-m', strtotime($date2));
		do {
			$month = date('Y-m', $time);
			$total = date('t', $time);
		
			$output[] = [
				'month' => $month,
				'total_days' => $total,
			];
		
			$time = strtotime('+1 month', $time);
		} while ($month != $last);
		$month_list = $output;
		$total_month = count($month_list);
		
		
		$sales_data = array();
		
		if ($territory_id) 
		{
			/*if(!empty($request_data['EsalesReports']['outlet_category_id'])){
				$conditions = array(
					'Memo.territory_id' => $territory_id,
					'Outlet.category_id' => $request_data['EsalesReports']['outlet_category_id'],
					'Memo.memo_date BETWEEN ? and ?'=>array($date_from, $date_to),
					'Memo.status !=' => 0
				);
			}else{
				$conditions = array(
					'Memo.territory_id' => $territory_id,
					'Memo.memo_date BETWEEN ? and ?'=>array($date_from, $date_to),
					'Memo.status !=' => 0
				);
			}*/
			//pr($conditions);
						
			/*$outlet_lists = $this->Outlet->find('all', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'alias' => 'Memo',
						'table' => 'memos',
						'type' => 'INNER',
						'conditions' => 'Outlet.id = Memo.outlet_id'
					)
				),
				'fields' => array(
				'Outlet.id', 'sum(Memo.gross_value) as total'
				),
				'group' => array('Outlet.id'),
				//'order'=>   array('Outlet.id' => 'desc'),
				//'limit' => 100,
				'recursive' => -1  
			));
			//pr($outlet_lists);*/
			
			
			if(!empty($request_data['EsalesReports']['outlet_category_id']))
			{
				$outlet_category_ids = join(",", $request_data['EsalesReports']['outlet_category_id']);  
				$sql = "SELECT [Outlet].[id] AS [id], sum(Memo.gross_value) as total FROM [outlets] AS [Outlet] INNER JOIN [memos] AS [Memo] ON ([Outlet].[id] = [Memo].[outlet_id]) WHERE [Memo].[territory_id] = ".$territory_id." AND [Outlet].[category_id] IN(".$outlet_category_ids.") AND [Memo].[memo_date] BETWEEN '".$date_from."' and '".$date_to."' AND [Memo].[status] > 0 GROUP BY [Outlet].[id]";
			}
			else
			{
				$sql = "SELECT [Outlet].[id] AS [id], sum(Memo.gross_value) as total FROM [outlets] AS [Outlet] INNER JOIN [memos] AS [Memo] ON ([Outlet].[id] = [Memo].[outlet_id]) WHERE [Memo].[territory_id] = ".$territory_id." AND [Memo].[memo_date] BETWEEN '".$date_from."' and '".$date_to."' AND [Memo].[status] > 0 GROUP BY [Outlet].[id]";
			}
			
			$outlet_lists = $this->Outlet->query($sql);
			//pr($outlet_lists);
			//exit;
			
			SessionComponent::write('outlet_lists', $outlet_lists);
			//$this->Session->write('outlet_lists', $outlet_lists);
			
			$outlet_type = $request_data['EsalesReports']['outlet_type'];			
			$count_outlet = 0;
			
			foreach($outlet_lists as $outlet_result)
			{
				$office_sales = round($outlet_result[0]['total']?$outlet_result[0]['total']:0);
				
				if($request_data['EsalesReports']['outlet_type']==1){
					$office_sales = $office_sales/$total_month;
				}
				
				//echo $office_sales.'<br>';
				
				if($range_start && $range_start > 0 && !$range_end)
				{
					if($office_sales >= $range_start ){
						$count_outlet++;
					}
				}
				
				if($range_start && $range_start > 0 && $range_end && $range_end > 0)
				{
					if($office_sales >= $range_start && $office_sales < $range_end){
						$count_outlet++;
					}
				}
				
				if($range_end && $range_end > 0 && !$range_start)
				{
					if($office_sales < $range_end ){
						$count_outlet++;
					}
				}
				
			}
						
			return $count_outlet;
				
        }				
        
	}
	
	
	

	//for detail report
	public function getEsalesDetailOutlet($request_data = array())
	{
		//pr($request_data);
		
		$date_from = date('Y-m-d', strtotime($request_data['EsalesReports']['date_from']));
		$date_to = date('Y-m-t', strtotime($request_data['EsalesReports']['date_to']));
		$region_office_id = $request_data['EsalesReports']['region_office_id']?$request_data['EsalesReports']['region_office_id']:0;
		
		$territory_id = $request_data['EsalesReports']['territory_id']?$request_data['EsalesReports']['territory_id']:0;
		
		
		$office_ids = array();
		
		$office_id = $request_data['EsalesReports']['office_id']?$request_data['EsalesReports']['office_id']:0;
		
		if($office_id)
		{
			array_push($office_ids, $office_id);
		}
		else
		{
			$office_conditions = array('Office.office_type_id'=>2, 'Office.parent_office_id'=>$region_office_id);
			$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
			foreach($offices as $key => $o_result){
				array_push($office_ids, $key);
			}
		}
		
		
		//month count
		$date1 = $date_from;
		$date2 = $date_to;
		$output = [];
		$time   = strtotime($date1);
		$last   = date('Y-m', strtotime($date2));
		do {
			$month = date('Y-m', $time);
			$total = date('t', $time);
		
			$output[] = [
				'month' => $month,
				'total_days' => $total,
			];
		
			$time = strtotime('+1 month', $time);
		} while ($month != $last);
		$month_list = $output;
		$total_month = count($month_list);
		
		
		$sales_data = array();
		
		
		if ($office_ids) 
		{
			//$outlet_category_ids = $request_data['EsalesReports']['outlet_category_id'];
			//AND o.category_id IN (".$outlet_category_id.") 
			//AND o.category_id IN (".$outlet_category_id.") 
			
			/*$results = $this->Memo->query("
				SELECT m.outlet_id, m.territory_id, m.market_id, o.name, o.category_id, SUM(m.gross_value) as total FROM memos m 
				INNER JOIN outlets o on o.id=m.outlet_id
                WHERE (m.memo_date BETWEEN '".$date_from."' AND '". $date_to."') 
				
				AND m.status!=0  
				GROUP BY m.outlet_id");
				
		    pr($results);
			exit;*/
			
			
			if(!empty($request_data['EsalesReports']['outlet_category_id']))
			{
				if($territory_id)
				{
					$conditions = array(
						'Office.id' => $office_ids,
						'Memo.territory_id' => $territory_id,
						'Outlet.category_id' => $request_data['EsalesReports']['outlet_category_id'],
						'Memo.memo_date BETWEEN ? and ?'=>array($date_from, $date_to),
						'Memo.status !=' => 0
					);
				}
				else
				{
					$conditions = array(
						'Office.id' => $office_ids,
						'Outlet.category_id' => $request_data['EsalesReports']['outlet_category_id'],
						'Memo.memo_date BETWEEN ? and ?'=>array($date_from, $date_to),
						'Memo.status !=' => 0
					);
				}
			}
			else
			{
				if($territory_id)
				{
					$conditions = array(
						'Office.id' => $office_ids,
						'Memo.territory_id' => $territory_id,
						'Memo.memo_date BETWEEN ? and ?'=>array($date_from, $date_to),
						'Memo.status !=' => 0
					);
				}
				else
				{
					$conditions = array(
						'Office.id' => $office_ids,
						'Memo.memo_date BETWEEN ? and ?'=>array($date_from, $date_to),
						'Memo.status !=' => 0
					);
				}
			}
						
			$outlet_lists = $this->Outlet->find('all', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'Market.territory_id = Territory.id'
					),
					array(
						'alias' => 'Office',
						'table' => 'offices',
						'type' => 'INNER',
						'conditions' => 'Territory.office_id = Office.id'
					),
					array(
						'alias' => 'Memo',
						'table' => 'memos',
						'type' => 'INNER',
						'conditions' => 'Outlet.id = Memo.outlet_id'
					),
					array(
						'alias' => 'Thana',
						'table' => 'thanas',
						'type' => 'LEFT',
						'conditions' => 'Thana.id = Market.thana_id'
					)
				),
				'fields' => array(
				'Memo.outlet_id', 'Outlet.name', 'Market.name','Thana.name', 'Territory.id', 'Territory.name', 'sum(Memo.gross_value) as total, count(Memo.memo_no) AS total_EC'
				),
				'group' => array('Memo.outlet_id', 'Outlet.name', 'Market.name', 'Territory.id', 'Territory.name','Thana.name'),
				//'order'=>   array('Outlet.id' => 'desc'),
				//'limit' => 100,
				'recursive' => 1  
			));
			
			// pr($outlet_lists);
			// exit;
			
			/*$outlet_lists = $this->Outlet->find('all', array(
				'conditions' => $conditions,
				'joins' => array(
					
					
					array(
						'alias' => 'Memo',
						'table' => 'memos',
						'type' => 'INNER',
						'conditions' => 'Outlet.id = Memo.outlet_id'
					),
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'Memo.territory_id = Territory.id'
					),
					array(
						'alias' => 'Office',
						'table' => 'offices',
						'type' => 'INNER',
						'conditions' => 'Territory.office_id = Office.id'
					),
					array(
						'alias' => 'Market',
						'table' => 'markets',
						'type' => 'INNER',
						'conditions' => 'Memo.market_id = Market.id'
					),
					array(
						'alias' => 'ThanaTerritory',
						'table' => 'thana_territories',
						'type' => 'INNER',
						'conditions' => 'Territory.id = ThanaTerritory.territory_id'
					),
					array(
						'alias' => 'Thana',
						'table' => 'thanas',
						'type' => 'INNER',
						'conditions' => 'ThanaTerritory.thana_id = Thana.id'
					),
					array(
						'alias' => 'District',
						'table' => 'districts',
						'type' => 'INNER',
						'conditions' => 'Thana.district_id = District.id'
					),
					array(
						'alias' => 'Division',
						'table' => 'divisions',
						'type' => 'INNER',
						'conditions' => 'District.division_id = Division.id'
					)
				),
				'fields' => array(
				'Outlet.id', 'Outlet.name', 'Market.name', 'Territory.id', 'Territory.name', 'Office.office_name', 'Thana.name', 'District.name', 'Division.name', 'sum(Memo.gross_value) as total, count(Memo.memo_no) AS total_EC'
				),
				'group' => array('Outlet.id', 'Outlet.name', 'Market.name', 'Territory.id', 'Territory.name', 'Office.office_name', 'Thana.name', 'District.name', 'Division.name'),
				//'order'=>   array('Outlet.id' => 'desc'),
				//'limit' => 100,
				'recursive' => -1  
			));*/
			
			/*$sql="SELECT [Outlet].[id] AS [Outlet__id], [Outlet].[name] AS [Outlet__name], [Market].[name] AS [Market__name], [Territory].[id] AS [Territory__id], [Territory].[name] AS [Territory__name], sum(Memo.gross_value) as total, count(Memo.memo_no) AS total_EC FROM [outlets] AS [Outlet] LEFT JOIN [markets] AS [Market] ON ([Outlet].[market_id] = [Market].[id]) LEFT JOIN [outlet_categories] AS [OutletCategory] ON ([Outlet].[category_id] = [OutletCategory].[id]) LEFT JOIN [institutes] AS [Institute] ON ([Outlet].[institute_id] = [Institute].[id]) LEFT JOIN [programs] AS [Program] ON ([Program].[outlet_id] = [Outlet].[id]) INNER JOIN [territories] AS [Territory] ON ([Market].[territory_id] = [Territory].[id]) INNER JOIN [offices] AS [Office] ON ([Territory].[office_id] = [Office].[id]) INNER JOIN [memos] AS [Memo] ON ([Outlet].[id] = [Memo].[outlet_id]) WHERE [Office].[id] = (26) AND [Memo].[memo_date] BETWEEN N'2017-12-01' and N'2018-01-31' AND [Memo].[status] != 0 GROUP BY [Outlet].[id], [Outlet].[name], [Market].[name], [Territory].[id], [Territory].[name]";*/
			
			//exit;
			
			$outlet_type = $request_data['EsalesReports']['outlet_type'];
						
			$count_outlet = 0;
			
			$results = array();
			
			$i=1;
			
			foreach($outlet_lists as $key => $outlet_result)
			{
				
				$office_sales = round($outlet_result[0]['total']?$outlet_result[0]['total']:0);
				
				if($outlet_type==1){
					$office_sales = $office_sales/$total_month;
				}
								
				$rank_info = $this->rankCheck($office_sales, $outlet_type);
				
				$info = $this->getInfo($outlet_result['Territory']['id']);
				
				/*pr($info);
				echo $info['Office']['office_name'];
				exit;*/				
				
				if(!empty($request_data['EsalesReports']['report_esales_setting_id']))
				{
					$report_esales_setting_id =	$request_data['EsalesReports']['report_esales_setting_id'];
					
					if(in_array($rank_info['id'], $report_esales_setting_id))
					{						
						$results[] = array(
							'si_no'						=> $i,
							'sales_area'				=> $info[0][0]['office_name'],
							'outlet_id'					=> $outlet_result['Memo']['outlet_id'],
							'total_memo_value'			=> sprintf("%01.2f", $office_sales),
							'avg_monthly_memo_value'	=> sprintf("%01.2f", $office_sales/$total_month),
							'total_effective_call_(EC)'	=> $outlet_result[0]['total_EC'],
							'pharmacy_name'				=> $outlet_result['Outlet']['name'],
							'market'					=> $outlet_result['Market']['name'],
							'territory'					=> $outlet_result['Territory']['name'],
							/*'thana'						=> $info[0][0]['thana_name'],commented by naser on 10 march 2019*/
							'thana'						=> $outlet_result['Thana']['name'],
							'district'					=> $info[0][0]['district_name'],
							'division'					=> $info[0][0]['division_name'],
							'rank'						=> $rank_info['name'],
							//'rank_id'					=> $rank_info['id']
						);
						
						$i++;
					}
				}
				else
				{
					$results[] = array(
							'si_no'						=> $i,
							'sales_area'				=> $info[0][0]['office_name'],
							'outlet_id'					=> $outlet_result['Memo']['outlet_id'],
							'total_memo_value'			=> sprintf("%01.2f", $office_sales),
							'avg_monthly_memo_value'	=> sprintf("%01.2f", $office_sales/$total_month),
							'total_effective_call_(EC)'	=> $outlet_result[0]['total_EC'],
							'pharmacy_name'				=> $outlet_result['Outlet']['name'],
							'market'					=> $outlet_result['Market']['name'],
							'territory'					=> $outlet_result['Territory']['name'],
							/*'thana'						=> $info[0][0]['thana_name'],*/
							'thana'						=> $outlet_result['Thana']['name'],
							'district'					=> $info[0][0]['district_name'],
							'division'					=> $info[0][0]['division_name'],
							'rank'						=> $rank_info['name'],
							//'rank_id'					=> $rank_info['id']
						);
						
					$i++;
				}
				//break;				
			}
			
			
			//pr($results);
			//exit;
			
			$this->Session->write('detail_results', $results);
						
			return $results;
				
        }
	}
	
	
	
	
	public function rankCheck($office_sales=0, $outlet_type=1)
	{
		/*$ranks = $this->ReportEsalesSetting->find('all', array(
			'conditions' => array('type'=>$outlet_type), 
			'order' => array('id' => 'asc')
		));
		pr($ranks);*/
		
		$sql = "SELECT * FROM report_esales_settings WHERE type = $outlet_type ORDER BY id asc";
		$ranks = $this->ReportEsalesSetting->query($sql);
		//pr($ranks);
		//exit;
		
		$rank_info = array();
		
		foreach($ranks as $rank)
		{
			$range_start = $rank[0]['range_start'];
			$range_end = $rank[0]['range_end'];
			
			//echo $range_start.'<br>';
			//echo $range_end.'<br>';
			
			if($range_start && $range_start > 0 && !$range_end)
			{
				if($office_sales >= $range_start ){
					$rank_info['name'] = $rank[0]['name'];
					$rank_info['id'] = $rank[0]['id'];
				}
			}
			
			if($range_start && $range_start > 0 && $range_end && $range_end > 0)
			{
				if($office_sales >= $range_start && $office_sales < $range_end){
					$rank_info['name'] = $rank[0]['name'];
					$rank_info['id'] = $rank[0]['id'];
				}
			}
			
			if($range_end && $range_end > 0 && !$range_start)
			{
				if($office_sales < $range_end ){
					$rank_info['name'] = $rank[0]['name'];
					$rank_info['id'] = $rank[0]['id'];
				}
			}
		}
		
		return $rank_info;
	}
	
	
	
	public function getInfo($territory_id=0)
	{
		/*$territories = $this->Territory->find('first', array(
				'conditions' => array('Territory.id' => $territory_id),
				
				'joins' => array(
					array(
						'alias' => 'Office',
						'table' => 'offices',
						'type' => 'INNER',
						'conditions' => 'Territory.office_id = Office.id'
					),
					array(
						'alias' => 'ThanaTerritory',
						'table' => 'thana_territories',
						'type' => 'INNER',
						'conditions' => 'Territory.id = ThanaTerritory.territory_id'
					),
					array(
						'alias' => 'Thana',
						'table' => 'thanas',
						'type' => 'INNER',
						'conditions' => 'ThanaTerritory.thana_id = Thana.id'
					),
					array(
						'alias' => 'District',
						'table' => 'districts',
						'type' => 'INNER',
						'conditions' => 'Thana.district_id = District.id'
					),
					array(
						'alias' => 'Division',
						'table' => 'divisions',
						'type' => 'INNER',
						'conditions' => 'District.division_id = Division.id'
					)
				),
				'fields' => 'Office.office_name, Thana.name, District.name, Division.name',
				//'order' => array('Territory.name' => 'asc'),
				'recursive' => -1
				));*/
		
		
		//pr($territories);
		//exit;
		
		$sql = "SELECT TOP 1 [Territory].[id] AS [territory_id], [Territory].[name] AS [territory_name], [Office].[office_name] AS [office_name], [Thana].[name] AS [thana_name], [District].[name] AS [district_name], [Division].[name] AS [division_name] FROM [territories] AS [Territory] 
		INNER JOIN [offices] AS [Office] ON ([Territory].[office_id] = [Office].[id]) 
		INNER JOIN [thana_territories] AS [ThanaTerritory] ON ([Territory].[id] = [ThanaTerritory].[territory_id]) 
		INNER JOIN [thanas] AS [Thana] ON ([ThanaTerritory].[thana_id] = [Thana].[id]) 
		INNER JOIN [districts] AS [District] ON ([Thana].[district_id] = [District].[id]) 
		INNER JOIN [divisions] AS [Division] ON ([District].[division_id] = [Division].[id]) 
		WHERE [Territory].[id] = $territory_id";
		
		$territories = $this->Territory->query($sql);
		
		//pr($territories);
		//exit;		
		return $territories;
	}
	
	
	
	public function get_office_list_by_region($parent_office_id)
	{
				
		$office_conditions = array('Office.parent_office_id' => $parent_office_id, 'Office.office_type_id'=>2);
		
		/*$offices = $this->Office->find('all', array(
			'fields' => array('id', 'office_name'),
			'conditions' => $office_conditions, 
			'order' => array('office_name' => 'asc'),
			'recursive' => -1
			)
		);
		pr($offices);*/
		
		$sql = "SELECT [Office].[id] AS [id], [Office].[office_name] AS [office_name] FROM [offices] AS [Office] WHERE [Office].[parent_office_id] = ".$parent_office_id." AND [Office].[office_type_id] = 2 ORDER BY [office_name] asc";
		$offices = $this->Office->query($sql);
		//pr($offices);
		//exit;
		return $offices;
		
	}
	
	
	public function get_office_list()
	{
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		
		$parent_office_id = $this->request->data['region_office_id'];
		
		$office_conditions = array('NOT' => array( "id" => array(30, 31, 37)), 'Office.office_type_id'=>2);
		//$office_conditions = array('Office.parent_office_id' => $parent_office_id, 'Office.office_type_id'=>2);
		
		if($parent_office_id && (int)$parent_office_id)$office_conditions['Office.parent_office_id'] = $parent_office_id;
		
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
	
	
	public function get_rank_list()
	{
		$this->loadModel('ReportEsalesSetting');
		
		//pr($this->request->data);
              
	    $type = $this->request->data['type'];
				
		$ranks = $this->ReportEsalesSetting->find('all', array(
			'conditions' => array('type'=>$type), 
			'order' => array('id' => 'asc')
		));
		
		$output = '';
		
		if($ranks)
		{	
			foreach($ranks as $value)
			{
				$output .= '<tr class="text-center">';
				$output .= '<td class="text-center" style="width:20%;">
							<div class="checkbox simple">';
							
				$output .= '<input type="checkbox" id="report_esales_setting_id'.$value['ReportEsalesSetting']['id'].'" value="'.$value['ReportEsalesSetting']['id'].'" name="data[EsalesReports][report_esales_setting_id][]">
						<label for="report_esales_setting_id'.$value['ReportEsalesSetting']['id'].'">'.$value['ReportEsalesSetting']['name'].'</label>';
					
				$output .= '</div></td>';
						
				$output .= '<td style="width:20%;">'.$value['ReportEsalesSetting']['operator_1'].'</td>';
				$output .= '<td style="width:20%;">'.$value['ReportEsalesSetting']['range_start'].'</td>';
						
				$output .= '<td style="width:20%;">'.$value['ReportEsalesSetting']['operator_2'].'</td>';
				$output .= '<td style="width:20%;">'.$value['ReportEsalesSetting']['range_end'].'</td>';
				$output .= '</tr>';
			}
			
			echo $output;
			
		}
		else
		{
			echo '';	
		}
		
        $this->autoRender = false;
    }
	
	
	
	
	public function admin_dwonload_csv() 
    {
		
		$detail_results = $this->Session->read('detail_results');
		
		if($detail_results)
		{
			$data1 = '';
		
			foreach($detail_results  as $e_orders){
				foreach($e_orders as $key => $e_order){
					$data1 .= ucfirst($key.",");
				}
				break;
			}
			//echo $data1;
			
			
			$data1 .= "\n";
		
			foreach($detail_results as $row1){
				$line = '';
				foreach($row1 as $value)
				{
					//pr($value);
					if ((!isset($value)) OR ($value == "")){
						$value = ","; //for Tab Delimitated use \t
					}else{
						$value = str_replace('"', '""', $value);
						$value = '"' . $value . '"' . ","; //for Tab Delimitated use \t
					}
					$line .= $value;
				}
				$data1 .= trim($line)."\n";
			}
			
			
			$data1 = str_replace("\r", "", $data1);
			if ($data1 == ""){
				$data1 = "\n(0) Records Found!\n";
			}
			
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"Esales-Detail-Report-".date("jS-F-Y-H:i:s").".csv\"");
			//$data="col1, col start and end col2,col3, \n";
			//$data .= "seond linedata here from site to download col1";
			
			echo $data1; 
			
			
			//pr($detail_results);
			
			$this->autoRender = false;
		
			exit;
			
		}
		
		
		
		//$outlet_lists = $this->Session->read('outlet_lists');
		
		
		if ($this->request->is('get'))
		{ 
			
			$region_offices_2 = $this->Session->read('region_offices_2');
			$territories_2 = $this->Session->read('territories_2');
			
			//pr($region_offices_2);
			//exit;
			
			$r_data = $this->params['url']['data'];
			$request_data = unserialize($r_data);
			
			
			$date_from = date('Y-m-d', strtotime($request_data['EsalesReports']['date_from']));
			//$date_to = date('Y-m-t', strtotime($request_data['EsalesReports']['date_to']));
			$date_to = date('Y-m-d', strtotime($request_data['EsalesReports']['date_to']));
			
			
			$outlet_type = $request_data['EsalesReports']['outlet_type'];
			
			//for rank list
			$ranks = $this->ReportEsalesSetting->find('all', array(
				'conditions' => array('type' => $outlet_type), 
				'order' => array('id' => 'asc')
			));
			
			@$report_esales_setting_id = $request_data['EsalesReports']['report_esales_setting_id'];
			
			if(!$request_data['EsalesReports']['office_id'])
			{
				//FOR CSV
				$data1 = '';
				$data1 .= ucfirst("Rank\t");
				
				foreach($region_offices_2 as $key => $r_value)
				{
					foreach($this->get_office_list_by_region($key) as $o_value){
						$data1 .= ucfirst(str_replace('Sales Office', '', $o_value[0]['office_name'])."\t");
					}
					
					$data1 .= ucfirst("$r_value\t");
				}
				
				$data1 .= ucfirst("National Total\t");
				//echo $data1;
				
				$data1 .= "\n";
				
				$line = '';
				
                $outlet_total_data = 0;
				
				
					
					
				
				foreach($ranks as $row1)
				{
					if($report_esales_setting_id){
						if(in_array($row1['ReportEsalesSetting']['id'], $report_esales_setting_id))
						{
							$national_total = 0; 
							$line = '"'.$row1['ReportEsalesSetting']['name'].'"'."\t";
							$i=0;
							foreach($region_offices_2 as $key => $r_value)
							{
								
								$regional_total = 0; 
								foreach($this->get_office_list_by_region($key) as $o_value)
								{
									$outlet_total_data = $this->getEsalesSum($request_data, $o_value[0]['id'], $row1['ReportEsalesSetting']['operator_1'], $row1['ReportEsalesSetting']['range_start'], $row1['ReportEsalesSetting']['operator_2'], $row1['ReportEsalesSetting']['range_end']);
									
									$line .= '"'.$outlet_total_data.'"'."\t";
									
									$regional_total+=$outlet_total_data;
									
									/*$tt[] = array(
									'total_outlet'=>$outlet_total_data
									);*/
								}
								$line .= '"'.$regional_total.'"'."\t";
								$national_total+=$regional_total; 
								
								
								
								$i++;
							}
							
							$line .= '"'.$national_total.'"'."\t";
							
							
							$data1 .= trim($line)."\n";
						}
					}
					else
					{
						$national_total = 0; 
							$line = '"'.$row1['ReportEsalesSetting']['name'].'"'."\t";
							$i=0;
							foreach($region_offices_2 as $key => $r_value)
							{
								
								$regional_total = 0; 
								foreach($this->get_office_list_by_region($key) as $o_value)
								{
									$outlet_total_data = $this->getEsalesSum($request_data, $o_value[0]['id'], $row1['ReportEsalesSetting']['operator_1'], $row1['ReportEsalesSetting']['range_start'], $row1['ReportEsalesSetting']['operator_2'], $row1['ReportEsalesSetting']['range_end']);
									
									$line .= '"'.$outlet_total_data.'"'."\t";
									
									$regional_total+=$outlet_total_data;
									
									/*$tt[] = array(
									'total_outlet'=>$outlet_total_data
									);*/
								}
								$line .= '"'.$regional_total.'"'."\t";
								$national_total+=$regional_total; 
								
								
								
								$i++;
							}
							
							$line .= '"'.$national_total.'"'."\t";
							
							
							$data1 .= trim($line)."\n";	
					}
				}
				
				//pr($tt);
				//$line .= '"Total,';
				//$data1 .= trim($line)."\n";
				
				//echo $data1;
				//exit;
				
			}
			else
			{
				//FOR CSV
				$data1 = '';
				$data1 .= ucfirst("Rank\t");
				
				foreach($territories_2 as $key => $t_value){
					$data1 .= ucfirst($t_value."\t");
				}
									
				$data1 .= ucfirst("National Total\t");
				//echo $data1;
				
				$data1 .= "\n";
				
				$line = '';
				
                $outlet_total_data = 0;
				foreach($ranks as $row1)
				{
					if($report_esales_setting_id){
						if(in_array($row1['ReportEsalesSetting']['id'], $report_esales_setting_id))
						{
							$national_total = 0; 
							$line = '"'.$row1['ReportEsalesSetting']['name'].'"'."\t";
	
							
							$regional_total = 0; 
							foreach($territories_2 as $key => $t_value)
							{
								$outlet_total_data = $this->getEsalesSumTerritory($request_data, $key, $row1['ReportEsalesSetting']['operator_1'], $row1['ReportEsalesSetting']['range_start'], $row1['ReportEsalesSetting']['operator_2'], $row1['ReportEsalesSetting']['range_end']);
								
								$line .= '"'.$outlet_total_data.'"'."\t";
								
								$national_total+=$outlet_total_data;
							}
						
							$line .= '"'.$national_total.'"'."\t";
						
						
							$data1 .= trim($line)."\n";
						}
					}
					else
					{
						$national_total = 0; 
						$line = '"'.$row1['ReportEsalesSetting']['name'].'"'."\t";
						$regional_total = 0; 
						foreach($territories_2 as $key => $t_value)
						{
							$outlet_total_data = $this->getEsalesSumTerritory($request_data, $key, $row1['ReportEsalesSetting']['operator_1'], $row1['ReportEsalesSetting']['range_start'], $row1['ReportEsalesSetting']['operator_2'], $row1['ReportEsalesSetting']['range_end']);
							
							$line .= '"'.$outlet_total_data.'"'."\t";
							
							$national_total+=$outlet_total_data;
						}
					
						$line .= '"'.$national_total.'"'."\t";
					
					
						$data1 .= trim($line)."\n";
					}
				}
				
				//echo $data1;
				//exit;
				
			}
			
			
			
			$data1 = str_replace("\r", "", $data1);
			if ($data1 == ""){
				$data1 = "\n(0) Records Found!\n";
			}
			
			//header("Content-type: application/octet-stream");
			//header("Content-Disposition: attachment; filename=\"Esales-Report-".date("jS-F-Y-H:i:s").".csv\"");
			
			header("Content-type: application/vnd.ms-excel; name='excel'");
			header("Content-Disposition: attachment; filename=\"Esales-Summary-Report-".date("jS-F-Y-H:i:s").".xls\"");
			header("Pragma: no-cache");
			header("Expires: 0");
			
			echo $data1; 
			
			exit;
			
			
			
			
		}
		
	    $this->autoRender = false;
    }
	
	
	
}
