<?php
App::uses('AppController', 'Controller');

/**
 * Controller
 *
 * @property ApiDataRetrives $ApiDataRetrives
 * @property PaginatorComponent $Paginator
 */
class ApiDataFullcareSalesReportsController extends AppController 
{
    
	public $components = array('RequestHandler', 'Usermgmt.UserAuth');
	
	public function total_sales() {
		
        $this->loadModel('Memo');
	
        $json_data = $this->request->input('json_decode', true);
		
		$divisionid = $json_data['division_id'];
		$districtid = $json_data['district_id'];
		$thanaid = $json_data['thana_id'];
		
		
		$conditions['MemoDetail.price >'] = 0;
		$conditions['MemoDetail.product_id'] = 465;
		
		if($divisionid > 0){$conditions['Division.id'] = $divisionid;}
		if($districtid > 0){$conditions['District.id'] = $districtid;}
		if($thanaid > 0){$conditions['Thana.id'] = $thanaid;}
		
		$memoTotal = $this->Memo->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'MemoDetail',
					'table' => 'memo_details',
					'type' => 'INNER',
					'conditions' => 'Memo.id = MemoDetail.memo_id'
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
				),
				array(
					'alias' => 'Division',
					'table' => 'divisions',
					'type' => 'INNER',
					'conditions' => 'District.division_id = Division.id'
				),
			),
			'fields' => array('SUM(MemoDetail.price*MemoDetail.sales_qty) as total_sales', 'MONTH(Memo.memo_date) as month_id'),
			'group' => array('MONTH(Memo.memo_date)'),
			'order' => array('MONTH(Memo.memo_date) asc'),
			'recursive' => -1
		));
		
		$rsArray = array();
		
		$alltime = 0;
		$currentMonth = 0;
		
		if(!empty($memoTotal)){
			foreach($memoTotal as $val){
			
				$amount = $val[0]['total_sales'];
				$month_id = $val[0]['month_id'];
				
				$amount = number_format((float)$amount, 2, '.', '');
				
				if($month_id == date('m')){
					$alltime += $amount;
					$currentMonth = $amount;
				}else{
					$alltime += $amount;
				}
				
				$rsArray[$month_id] = $amount;
			}
		}
		
		$data['total_sales'] = $alltime;
		$data['current_month_sales'] = $currentMonth;
		$data['month_wise_sales'] = $rsArray;
		
		$this->set(array(
            'sales_info' => array($data),
            '_serialize' => array('sales_info')
            ));
        
    }
	
	public function area_office_wise_sales() {
		
        $this->loadModel('Memo');
		
		$json_data = $this->request->input('json_decode', true);
		
		$fromDate = $json_data['fromDate'];
		$toDate = $json_data['toDate'];
		
		
		$conditions['MemoDetail.price >'] = 0;
		$conditions['MemoDetail.product_id'] = 465;
		
		if($fromDate != 0 AND $toDate != 0){
			$conditions['Memo.memo_date >='] = date('Y-m-d', strtotime($fromDate));
			$conditions['Memo.memo_date <='] = date('Y-m-d', strtotime($toDate));
		}
	
		
		$memoTotal = $this->Memo->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'MemoDetail',
					'table' => 'memo_details',
					'type' => 'INNER',
					'conditions' => 'Memo.id = MemoDetail.memo_id'
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
				),
				array(
					'alias' => 'Division',
					'table' => 'divisions',
					'type' => 'INNER',
					'conditions' => 'District.division_id = Division.id'
				),
			),
			'fields' => array('SUM(MemoDetail.sales_qty) as total_sales', 'Memo.office_id'),
			'group' => array('Memo.office_id'),
			//'order' => array('MONTH(Memo.memo_date) asc'),
			'recursive' => -1
		));
		
		$rsArray = array();
		
		$alltime = 0;
		
		if(!empty($memoTotal)){
			foreach($memoTotal as $val){
			
				$vol = $val[0]['total_sales'];
					
				$amount = $this->unit_convert(465, 8, $vol);
				
				$office_id = $val['Memo']['office_id'];
				
				$amount = number_format((float)$amount, 2, '.', '');
				
				$rsArray[$office_id] = array('amount'=>$amount);
				$alltime += $amount;
			}
		}
		
		$data['total_sales'] = $alltime;
		$data['areawise_sales'] = $rsArray;
		
		$this->set(array(
            'sales_info' => array($data),
            '_serialize' => array('sales_info')
            ));
        
    }
	
	public function division_wise_sales() {
		
        $this->loadModel('Memo');
		
		//$json_data = $this->request->input('json_decode', true);
		
		$fromDate = $json_data['fromDate'];
		$toDate = $json_data['toDate'];
		$division_id = $json_data['division_id'];
		
		//$division_id = $json_data['division_id'];
		
		$conditions['MemoDetail.price >'] = 0;
		$conditions['MemoDetail.product_id'] = 465;
		
		if($fromDate != 0 AND $toDate != 0){
			$conditions['Memo.memo_date >='] = date('Y-m-d', strtotime($fromDate));
			$conditions['Memo.memo_date <='] = date('Y-m-d', strtotime($toDate));
		}
		
		if($division_id > 0){
			$conditions['Division.id'] = $division_id;
		}
		
		//echo '<pre>aa';print_r($conditions);exit;
	
		
		$memoTotal = $this->Memo->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'MemoDetail',
					'table' => 'memo_details',
					'type' => 'INNER',
					'conditions' => 'Memo.id = MemoDetail.memo_id'
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
				),
				array(
					'alias' => 'Division',
					'table' => 'divisions',
					'type' => 'INNER',
					'conditions' => 'District.division_id = Division.id'
				),
			),
			'fields' => array('SUM(MemoDetail.sales_qty) as total_sales', 'District.id'),
			'group' => array('District.id'),
			'recursive' => -1
		));
		
		$rsArray = array();
		
		$alltime = 0;
		
		//echo '<pre>aa';print_r($memoTotal);exit;
		
		if(!empty($memoTotal)){
			foreach($memoTotal as $val){
			
				$vol = $val[0]['total_sales'];
					
				$amount = $this->unit_convert(465, 8, $vol);
				
				$district_id = $val['District']['id'];
				
				$amount = number_format((float)$amount, 2, '.', '');
				
				$rsArray[$district_id] = array('amount'=>$amount);
				$alltime += $amount;
			}
		}
		
		$data['total_sales'] = $alltime;
		$data['district_sales'] = $rsArray;
		
		$this->set(array(
            'sales_info' => array($data),
            '_serialize' => array('sales_info')
            ));
        
    }
	
	public function unit_convert($product_id = '', $measurement_unit_id = '', $qty = '') {
		$this->loadModel('ProductMeasurement');
		$unit_info = $this->ProductMeasurement->find('first', array(
			'conditions' => array(
				'ProductMeasurement.product_id' => $product_id,
				'ProductMeasurement.measurement_unit_id' => $measurement_unit_id
				)
			));
		$number = $qty;
		if (!empty($unit_info)) {
		   $number = $unit_info['ProductMeasurement']['qty_in_base'] * $qty;
		   $number=round($number);
		   return $number;
	   } 
	   else 
	   {
			$number=round($number);
			return  $number;
		}
	}
	
	public function get_value_and_volume() {
		
        $this->loadModel('Memo');
	
        $json_data = $this->request->input('json_decode', true);
		
		//$divisionid = $json_data['division_id'];
		
		for ($x = 0; $x <= 23; $x++) {
			
			$count = '-' . $x . ' month'; 	
		
			$dateYMname =  date("Y-M",strtotime($count));
		
			$conditions['MemoDetail.price >'] = 0;
			$conditions['MemoDetail.product_id'] = 465;
			
			$conditions['MONTH(Memo.memo_date)'] = date("m",strtotime($count));
			$conditions['YEAR(Memo.memo_date)'] = date("Y",strtotime($count));
			
			$memo = $this->Memo->find('first', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'alias' => 'MemoDetail',
						'table' => 'memo_details',
						'type' => 'INNER',
						'conditions' => 'Memo.id = MemoDetail.memo_id'
					),
				),
				'fields' => array('SUM(MemoDetail.price*MemoDetail.sales_qty) as total_value', 'SUM(MemoDetail.sales_qty) as total_sales'),
				'group' => array('MONTH(Memo.memo_date)', 'YEAR(Memo.memo_date)'),
				//'order' => array('MONTH(Memo.memo_date) asc'),
				'recursive' => -1
			));
			
			if(!empty($memo)){
				$value = $memo[0]['total_value'];
				$totalsales = $memo[0]['total_sales'];
				
				$volume = $this->unit_convert(465, 8, $totalsales);
				
			}else{
				$value = 0;
				$volume = 0;
			}
			
			
			$rsArray[$dateYMname] = array(
				'value'=>$value,
				'volume'=>$volume
			);
		
		}
		
		//echo '<pre>';print_r($rsArray);exit;
		
		$this->set(array(
            'sales_info' => array($rsArray),
            '_serialize' => array('sales_info')
             ));
        
    }
	
	public function sales_report_date_wise(){
		
		$json_data = $this->request->input('json_decode', true);
		
		$this->loadModel('Product');
		$this->loadModel('Memo');
		
		$date_from = $json_data['from_date'];
		$date_to = $json_data['to_date'];
		
		$productInfo = $this->Product->find('all', array(
                //'fields' => array('id', 'name', 'source'),
                'conditions' => array('Product.id'=>465),
                'recursive'=> -1
            )); 

        $productInfo = $productInfo[0]['Product'];
		

		$sql = "SELECT SUM(memo_details.sales_qty) as vol, SUM(memo_details.sales_qty*memo_details.price) as amount, markets.location_type_id as loc_id, districts.division_id as divisions FROM memos 
			INNER JOIN memo_details ON memos.id = memo_details.memo_id
			INNER JOIN markets ON memos.market_id = markets.id
			INNER JOIN location_types ON markets.location_type_id = location_types.id
			INNER JOIN thanas ON markets.thana_id = thanas.id
			INNER JOIN districts ON thanas.district_id = districts.id
			INNER JOIN divisions ON districts.division_id = divisions.id
			WHERE memo_date BETWEEN '$date_from' AND '$date_to' AND memo_details.product_id=465 GROUP BY markets.location_type_id, districts.division_id";

		$result = $this->Memo->query($sql);


		foreach ($result as $key => $val) {

			$divisionid = $val[0]['divisions'];

			$dataArray[$divisionid] = array_filter($result, function ($var) use($divisionid) {
				return ($var[0]['divisions'] == $divisionid);
			});

		}

		//pr($productInfo);exit();
		$pid = $productInfo['id'];
		$sales_measurement_unit_id = $productInfo['sales_measurement_unit_id'];
	
		 $i=1;
		 $uamount = 0 ;
		 $ramount = 0;
		 $unit_type = 2;
		 foreach ($dataArray as $key => $value) {
			
			$resData[$i]['division_id'] = $key;
			$resData[$i]['rural'] = 0;
			$resData[$i]['urban'] = 0;
			

			foreach ($value as $key => $v) {
					
					if($v[0]['loc_id'] == 6){
						$resData[$i]['urban'] = sprintf("%01.2f", ($unit_type==1)?$v[0]['vol']:$this->unit_convert($pid, $sales_measurement_unit_id, $v[0]['vol']));

						$uamount = $v[0]['amount'];
					}else{
						
						$resData[$i]['rural'] = sprintf("%01.2f", ($unit_type==1)?$v[0]['vol']:$this->unit_convert($pid, $sales_measurement_unit_id, $v[0]['vol']));
						$ramount = $v[0]['amount'];
					}

			}

			$resData[$i]['amount'] = $uamount + $ramount;

			$i++;
		 }
		 
		$this->set(array(
            'sales_info' => array($resData),
            '_serialize' => array('sales_info')
             ));
		 
	}
	
	
	
}
