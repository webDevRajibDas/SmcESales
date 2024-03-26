
<?php
App::uses('AppController', 'Controller');
/**
 * Designations Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DashboardsController extends AppController
{

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
	public function admin_index()
	{
		// pr($_SESSION);exit;
		/*$dashboard=$this->Session->read('UserAuth.User.dashboard');
		$this->redirect(array('controller' => 'dashboard','admin'=>false)
			);
		
		$this->autoRender=false;*/
	}

	public function dashboard1()
	{
		$view = new View($this);
		if (!$view->App->menu_permission('dashboards', 'admin_index')) {
			$this->redirect('/accessDenied');
		}

		ini_set('max_execution_time', 300); //300 seconds = 5 minutes

		date_default_timezone_set('Asia/Dhaka');

		$GLOBALS['OfficeId'] = $this->UserAuth->getOfficeId();

		$this->loadModel('SalesPerson');
		$this->loadModel('Memo');
		$this->loadModel('MemoSyncHistory');
		$this->loadModel('Office');

		$sources = array('SMC' => 'SMC', 'SMCEL' => 'SMCEL');
		$this->set(compact('sources'));

		//START fiscal year & month CalCulation
		$this->loadModel('FiscalYear');
		$fiscal_year_result = $this->FiscalYear->find(
			'first',
			array(
				'conditions' => array("FiscalYear.start_date <=" => date('Y-m-d'), "FiscalYear.end_date >=" => date('Y-m-d')),
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


		if ($this->UserAuth->getOfficeParentId() && $this->UserAuth->getOfficeParentId() != 14) {
			//area office
			$office_id = $this->UserAuth->getOfficeId();
			$region = $this->UserAuth->getOfficeParentId();
			$this->set(compact('region'));
			$this->set(compact('office_id'));
			//Territory Sales Performance Monitoring
			$this->loadModel('Office');
			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'office_type_id' => 2,
					'id' => $office_id,
					"NOT" => array("id" => array(30, 31, 37))
				),
				'order' => array('office_name' => 'asc')
			));

			$this->set(compact('offices'));
			$region_offices = $this->Office->find('list', array(
				'conditions' => array('Office.id' => $region),
				'order' => array('office_name' => 'asc')
			));
			$this->set(compact('region_offices'));
			// $this->render('aso_index');

		} elseif ($this->UserAuth->getOfficeParentId() == 14) {
			$office_id = 0;
			$this->set(compact('office_id'));


			//for regional admin
			$region_office_id = $this->UserAuth->getOfficeId();

			//Territory Sales Performance Monitoring
			$this->loadModel('Office');
			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,

					"NOT" => array("id" => array(30, 31, 37))
				),
				'order' => array('office_name' => 'asc')
			));

			$this->set(compact('offices'));
			$region = $this->UserAuth->getOfficeId();
			$this->set(compact('region'));

			//for region wise office list
			$region_offices = $this->Office->find('list', array(
				'conditions' => array('Office.id' => $region),
				'order' => array('office_name' => 'asc')
			));
			$this->set(compact('region_offices'));
		} else {
			//for admin
			$office_id = 0;
			$this->set(compact('office_id'));
			$region = 0;
			$this->set(compact('region'));

			//Territory Sales Performance Monitoring
			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'office_type_id' => 2,
					"NOT" => array("id" => array(30, 31, 37))
				),
				'order' => array('office_name' => 'asc')
			));
			$this->set(compact('offices'));
			//for region wise office list
			$region_offices = $this->Office->find('list', array(
				'conditions' => array('Office.office_type_id' => 3),
				'order' => array('office_name' => 'asc')
			));
			$this->set(compact('region_offices'));
		}
	}

	public function dashboard2()
	{

		$view = new View($this);
		if (!$view->App->menu_permission('dashboards', 'admin_index')) {
			$this->redirect('/accessDenied');
		}
		/*$this->loadModel('SalesPerson');		
		$this->loadModel('Memo');		
		$this->loadModel('MemoSyncHistory');		
		$so_list = $this->SalesPerson->find('all', array(
			'fields' => array('SalesPerson.id','SalesPerson.name','SalesPerson.last_data_push_time','Territory.name'),
			'conditions' => array(
				'SalesPerson.office_id' => $this->UserAuth->getOfficeId(),
				'SalesPerson.territory_id >' => 0,
				'User.user_group_id' => 4
			)
		));	
		$data_array = array();
		foreach($so_list as $so){ 
			
			$this->Memo->virtualFields = array(
				'total_memo' => 'COUNT(*)',
				'total_memo_value' => 'SUM(Memo.gross_value)'
			); 
			$memo1 = $this->Memo->find('first', array(
				'conditions' => array('Memo.sales_person_id' => $so['SalesPerson']['id']),
				'order' => array('Memo.memo_date desc'),
				'fields' => array('memo_date'),
				'recursive' => -1
			));
			$last_memo_sync_date = '';
			
			if(!empty($memo1))
			{
				$last_memo_sync_date = $memo1['Memo']['memo_date'];
				
			}else
			{
				$last_memo_sync_date = $this->current_date();
			}
			$memo = $this->Memo->find('first', array(
				'conditions' => array('Memo.sales_person_id' => $so['SalesPerson']['id'],'Memo.memo_date >=' => $last_memo_sync_date.' 00:00:00','Memo.memo_date <=' => $last_memo_sync_date.' 23:59:59'),
				'fields' => array('total_memo', 'total_memo_value'),
				'recursive' => -1
			));
                                               			
			$memo_sync = $this->MemoSyncHistory->find('first', array(
				'conditions' => array('MemoSyncHistory.so_id' => $so['SalesPerson']['id'],'MemoSyncHistory.date' => $last_memo_sync_date),
				'fields' => array('total_memo'),
				'recursive' => -1
			));
                        
                         $last_memo_info = $this->Memo->find('first', array(
				'conditions' => array('Memo.sales_person_id' => $so['SalesPerson']['id']),
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
                        
			$data_array[] = $data;
		}
		
		$this->set(compact('data_array'));*/
	}

	public function get_hours($datetime = '')
	{
		if ($datetime == '') {
			return 100;
		} else {
			$dateDiff = intval((strtotime($this->current_datetime()) - strtotime($datetime)) / 60);
			$hours = intval($dateDiff / 60);
			return $hours;
		}
	}


	private function priceSetting($amount = 0)
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

		$amount = sprintf("%01.2f", $amount / 1000000) . ' M';

		return $amount;
	}

	private function condition_break_as_text($conditions)
	{
		$condition = '';
		foreach ($conditions as $key => $value) {
			$key = explode(" ", $key);
			$con_key = $key[0];
			$con_operator = isset($key[1]) ? $key[1] : "=";
			if (is_array($value)) {
				$con_operator = "IN";
				$value = "(" . implode(',', $value) . ")";
			} else {
				$value = "'$value'";
			}
			$condition .= "$con_key $con_operator $value AND ";
		}
		$condition = rtrim($condition, "AND ");
		return $condition;
	}


	private function getEcOCRev($office_id = 0, $source = 0)
	{
		$this->loadModel('Memo');

		//get weekly date
		$this->loadModel('Week');
		$c_date = date('Y-m-d');
		$w_results = $this->Week->find(
			'first',
			array(
				'conditions' => array(
					'Week.start_date <=' => $c_date,
					'Week.end_date >=' => $c_date,
				),
				//'fields'=> array('sum(Memo.gross_value) AS total_Revenue, count(Memo.memo_no) AS total_EC'),
				'recursive' => -1
			)
		);

		if ($w_results) {
			$week_start_date = $w_results['Week']['start_date'];
			$week_end_date = $w_results['Week']['end_date'];
		} else {
			$week_start_date = (date('D') == 'Sun') ? date("Y-6-d", strtotime('saturday this week')) : date("Y-6-d", strtotime('saturday last week'));
			//$week_end_date = date($week_start_date, strtotime('+5 day'));
			$day = 60 * 60 * 24;
			$today = strtotime($week_start_date);
			$week_end_date = date("Y-6-d", $today + $day * 5);
		}
		//end for week

		//start fiscale year
		$this->loadModel('FiscalYear');
		$y_results = $this->FiscalYear->find(
			'first',
			array(
				'conditions' => array("FiscalYear.start_date <=" => date('Y-m-d'), "FiscalYear.end_date >=" => date('Y-m-d')),
				'recursive' => -1
			)
		);
		if ($y_results) {
			$year_start_date = $y_results['FiscalYear']['start_date'];
			$year_end_date = $y_results['FiscalYear']['end_date'];
		} else {
			$year_start_date = date('Y-07-01');
			$year_end_date = date('Y-07-01', strtotime('+1 year'));
		}
		//end fiscale year


		$month_date = date('Y-m-01');
		$year_date = date('Y-07-01');


		$conditions = array();


		/* --------------- week data --------------- */
		$conditions = array(
			'date >=' => $week_start_date,
			'date <=' => $week_end_date,
		);
		if ($office_id)
			$conditions['office_id'] = $office_id;

		$condition = $this->condition_break_as_text($conditions);
		$week_sql =
			"SElECT
			'week' as type,
			SUM(ec_0) as all_ec,SUM(ec_1) smcel_ec,SUM(ec_2) smc_ec,
			SUM(oc_0) all_oc,SUM(oc_1) smcel_oc,SUM(oc_2) smc_oc,
			SUM(revenue_0) all_revenue,SUM(revenue_1) smcel_revenue,SUM(revenue_2) smc_revenue,
			SUM(cyp_0) all_cyp,SUM(cyp_1) smcel_cype,SUM(cyp_2) smc_cyp
		FROM
			dashboard_daily_sales_summery
		WHERE $condition
		";

		/* --------------- week data --------------- */
		/* --------------- Month Data --------------- */
		$conditions = array(
			'date >=' => date('Y-m-01'),
			'date <=' => date('Y-m-d'),
		);
		if ($office_id)
			$conditions['office_id'] = $office_id;

		$condition = $this->condition_break_as_text($conditions);
		$month_sql =
			"SElECT
			'month' as type,
			SUM(ec_0) as all_ec,SUM(ec_1) smcel_ec,SUM(ec_2) smc_ec,
			SUM(oc_0) all_oc,SUM(oc_1) smcel_oc,SUM(oc_2) smc_oc,
			SUM(revenue_0) all_revenue,SUM(revenue_1) smcel_revenue,SUM(revenue_2) smc_revenue,
			SUM(cyp_0) all_cyp,SUM(cyp_1) smcel_cype,SUM(cyp_2) smc_cyp
		FROM
			dashboard_daily_sales_summery
		WHERE $condition
		";

		/* --------------- Month data --------------- */
		/* ---------------- Year Data --------------- */
		$conditions = array(
			'date >=' => $year_start_date,
			'date <=' => $year_end_date
		);
		if ($office_id)
			$conditions['office_id'] = $office_id;

		$condition = $this->condition_break_as_text($conditions);
		$year_sql =
			"SElECT
			'year' as type,
			SUM(ec_0) as all_ec,SUM(ec_1) smcel_ec,SUM(ec_2) smc_ec,
			SUM(oc_0) all_oc,SUM(oc_1) smcel_oc,SUM(oc_2) smc_oc,
			SUM(revenue_0) all_revenue,SUM(revenue_1) smcel_revenue,SUM(revenue_2) smc_revenue,
			SUM(cyp_0) all_cyp,SUM(cyp_1) smcel_cype,SUM(cyp_2) smc_cyp
		FROM
			dashboard_daily_sales_summery
		WHERE $condition
		";
		/* ---------------- Year Data --------------- */

		$sql = $week_sql . " UNION " . $month_sql . " UNION " . $year_sql;
		// $sql = $week_sql . " ; " . $month_sql . " ; " . $year_sql . " ; " . $today_sql;
		$result = $this->Memo->Query($sql);
		/* pr($result);
		echo $this->Memo->getLastQuery();
		exit; */
		return $result;
	}
	private function today_topbox_data($office_id = 0, $source = 0, $date, $type = 'Today')
	{
		$this->loadModel('Memo');
		/* ----------------- today Data --------------- */
		$conditions = array(
			'memo_date' => date('Y-m-d', strtotime($date)),
			'Memo.status !=' => 0,
			/* 'Memo.gross_value >' => 0, */
		);
		if ($office_id)
			$conditions['Territory.office_id'] = $office_id;
		if ($source) {
			$conditions['Product.source'] = $source;
		}
		$condition = $this->condition_break_as_text($conditions);
		$today_sql = "SElECT
					'$type' as type,
					Count(Distinct(Memo.id)) as ec,
					SUM((MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))*MemoDetail.sales_qty) as gross_value
				FROM memos Memo WITH (NOLOCK)
				inner join territories Territory WITH (NOLOCK) on Territory.id=Memo.territory_id
				inner join memo_details MemoDetail WITH (NOLOCK) on MemoDetail.memo_id=Memo.id
				left join discount_bonus_policies dbp on dbp.id=MemoDetail.policy_id
				inner join products Product WITH (NOLOCK) on Product.id=MemoDetail.product_id
				WHERE $condition
				";
		$conditions = array(
			'memo_date' => date('Y-m-d', strtotime($date)),
			'Memo.status !=' => 0
		);
		if ($office_id)
			$conditions['Territory.office_id'] = $office_id;
		if ($source) {
			$conditions['Product.source'] = $source;
		}
		$condition = $this->condition_break_as_text($conditions);
		$today_sql = "SELECT ec_q.type,ec,oc,gross_value FROM
						($today_sql) ec_q
						LEFT JOIN (SElECT
						'$type' as type,
							Count(Distinct(Memo.outlet_id)) oc
						FROM memos Memo WITH (NOLOCK)
						inner join territories Territory WITH (NOLOCK) on Territory.id=Memo.territory_id
						inner join memo_details MemoDetail WITH (NOLOCK) on MemoDetail.memo_id=Memo.id
						inner join products Product WITH (NOLOCK) on Product.id=MemoDetail.product_id
						WHERE $condition) oc_q on ec_q.type=oc_q.type
						";
		/* ----------------- today Data --------------- */
		$result = $this->Memo->Query($today_sql);
		return $result;
	}
	private function getSalesTotal($box_data = '', $date = 0, $office_id = 0, $source = 0)
	{
		$this->loadModel('Memo');

		//get weekly date
		$this->loadModel('Week');
		$c_date = date('Y-m-d');
		$w_results = $this->Week->find(
			'first',
			array(
				'conditions' => array(
					'Week.start_date <=' => $c_date,
					'Week.end_date >=' => $c_date,
				),
				//'fields'=> array('sum(Memo.gross_value) AS total_Revenue, count(Memo.memo_no) AS total_EC'),
				'recursive' => -1
			)
		);

		if ($w_results) {
			$week_start_date = $w_results['Week']['start_date'];
			$week_end_date = $w_results['Week']['end_date'];
		} else {
			$week_start_date = (date('D') == 'Sun') ? date("Y-6-d", strtotime('saturday this week')) : date("Y-6-d", strtotime('saturday last week'));
			//$week_end_date = date($week_start_date, strtotime('+5 day'));
			$day = 60 * 60 * 24;
			$today = strtotime($week_start_date);
			$week_end_date = date("Y-6-d", $today + $day * 5);
		}
		//end for week

		//start fiscale year
		$this->loadModel('FiscalYear');
		$y_results = $this->FiscalYear->find(
			'first',
			array(
				'conditions' => array("FiscalYear.start_date <=" => date('Y-m-d'), "FiscalYear.end_date >=" => date('Y-m-d')),
				'recursive' => -1
			)
		);
		if ($y_results) {
			$year_start_date = $y_results['FiscalYear']['start_date'];
			$year_end_date = $y_results['FiscalYear']['end_date'];
		} else {
			$year_start_date = date('Y-07-01');
			$year_end_date = date('Y-07-01', strtotime('+1 year'));
		}
		//end fiscale year


		$month_date = date('Y-m-01');
		$year_date = date('Y-07-01');


		if ($box_data) {
			$conditions = array();

			if ($office_id) {
				if ($box_data == 'week') {
					$conditions = array(
						'memo_date >=' => $week_start_date,
						'memo_date <=' => $week_end_date,
						'Memo.status !=' => 0,
						'Memo.gross_value >' => 0,
						'Territory.office_id' => $office_id
					);
				} elseif ($box_data == 'month') {
					$conditions = array(
						'memo_date >=' => date('Y-m-01'),
						'memo_date <=' => date('Y-m-d'),
						'Memo.status !=' => 0,
						'Memo.gross_value >' => 0,
						'Territory.office_id' => $office_id
					);
				} elseif ($box_data == 'year') {
					$conditions = array(
						'memo_date >=' => $year_start_date,
						'memo_date <=' => $year_end_date,
						'Memo.status !=' => 0,
						'Memo.gross_value >' => 0,
						'Territory.office_id' => $office_id
					);
				} else {
					//for today
					$conditions = array(
						'memo_date' => date('Y-m-d'),
						'Memo.status !=' => 0,
						'Memo.gross_value >' => 0,
						'Territory.office_id' => $office_id
					);
				}
			} else {
				if ($box_data == 'week') {
					//$conditions = array('memo_date >=' => $week_start_date, 'Memo.status !=' => 0);
					$conditions = array(
						'memo_date >=' => $week_start_date,
						'memo_date <=' => $week_end_date,
						'Memo.gross_value >' => 0,
						'Memo.status !=' => 0
					);
				} elseif ($box_data == 'month') {
					//$conditions = array('memo_date >=' => $month_date, 'Memo.status !=' => 0);
					$conditions = array(
						'memo_date >=' => date('Y-m-01'),
						'memo_date <=' => date('Y-m-d'),
						'Memo.gross_value >' => 0,
						'Memo.status !=' => 0
					);
				} elseif ($box_data == 'year') {
					//$conditions = array('memo_date >=' => $year_date, 'Memo.status !=' => 0);
					$conditions = array(
						'memo_date >=' => $year_start_date,
						'memo_date <=' => $year_end_date,
						'Memo.gross_value >' => 0,
						'Memo.status !=' => 0
					);
				} else {
					$conditions = array(
						'memo_date' => date('Y-m-d'),
						'Memo.gross_value >' => 0,
						'Memo.status !=' => 0
					);
				}
			}
			if ($source) {
				$conditions['Product.source'] = $source;
			}
			$condition = $this->condition_break_as_text($conditions);

			$query = "SELECT 
						SUM(gross_value) as total_Revenue,
						count(distinct(memo_no)) as total_EC
						FROM 
							(SElECT 
								Memo.id,
								Memo.memo_no,
								SUM(MemoDetail.price*MemoDetail.sales_qty) as gross_value
							FROM memos Memo
							inner join territories Territory on Territory.id=Memo.territory_id
							inner join memo_details MemoDetail on MemoDetail.memo_id=Memo.id
							inner join products Product on Product.id=MemoDetail.product_id
							WHERE $condition
							Group by Memo.id,Memo.memo_no,Memo.gross_value) t";
			$result = $this->Memo->Query($query);
			/*$result = $this->Memo->find('all', 
				array(
				'conditions'=> $conditions,
				'joins'=>array(
					array(
						'table'			=>	'memo_details',
						'alias'			=>	'MemoDetail',
						'Type'			=>	'INNER',
						'conditions'	=>	'MemoDetail.memo_id=Memo.id',
					),

					array(
						'table'			=>	'products',
						'alias'			=>	'Product',
						'Type'			=>	'INNER',
						'conditions'	=>	'MemoDetail.product_id=Product.id',
					),
				),
				'group' => array(''),
				'fields'=> array('sum(Memo.gross_value) AS total_Revenue, count(Memo.memo_no) AS total_EC')
				)
			);*/
			// echo $this->Memo->getLastQuery();exit;
			$data = array();

			if ($result) {
				$data['total_Revenue'] = $result[0][0]['total_Revenue'];
				$data['total_EC'] = $result[0][0]['total_EC'];
			} else {
				$data['total_Revenue'] = 0;
				$data['total_EC'] = 0;
			}

			return $data;
		}

		if ($date) {
			$y_m = explode('-', $date);
			$year = $y_m[0];
			$month = $y_m[1];

			if ($office_id) {
				$conditions = array(
					'YEAR(memo_date)' => $year,
					'MONTH(memo_date)' => $month,
					'Memo.status !=' => 0,
					'Territory.office_id' => $office_id
				);
			} else {
				$conditions = array(
					'YEAR(memo_date)' => $year,
					'MONTH(memo_date)' => $month,
					'Memo.status !=' => 0
				);
			}
			if ($source) {
				$conditions['Product.source'] = $source;
			}
			$condition = '';
			foreach ($conditions as $key => $value) {
				$key = explode(" ", $key);
				$con_key = $key[0];
				$con_operator = isset($key[1]) ? $key[1] : "=";
				if (is_array($value)) {
					$con_operator = "IN";
					$value = "(" . implode(',', $value) . ")";
				} else {
					$value = "'$value'";
				}
				$condition .= "$con_key $con_operator $value AND ";
			}
			$condition = rtrim($condition, "AND ");
			$query = "SELECT 
						SUM(gross_value) as total_amount
						FROM 
							(SElECT 
								Memo.id,
								Memo.memo_no,
								SUM(MemoDetail.price*MemoDetail.sales_qty) as gross_value 
							FROM memos Memo
							inner join territories Territory on Territory.id=Memo.territory_id
							inner join memo_details MemoDetail on MemoDetail.memo_id=Memo.id
							inner join products Product on Product.id=MemoDetail.product_id
							WHERE $condition
							Group by Memo.id,Memo.memo_no,Memo.gross_value) t";
			$result = $this->Memo->Query($query);
			/*$result = $this->Memo->find('all', 
				array(
				'conditions'=> $conditions,
				'joins'=>array(
					array(
						'table'			=>	'memo_details',
						'alias'			=>	'MemoDetail',
						'Type'			=>	'INNER',
						'conditions'	=>	'MemoDetail.memo_id=Memo.id',
					),

					array(
						'table'			=>	'products',
						'alias'			=>	'Product',
						'Type'			=>	'INNER',
						'conditions'	=>	'MemoDetail.product_id=Product.id',
					),
				),
				'fields' => array('sum(Memo.gross_value) AS total_amount')
				)
			);*/
			// echo $this->Memo->getLastQuery();exit;
			return sprintf("%01.2f", $result[0][0]['total_amount'] / 1000000);
		}

		return false;
	}



	//for top boxes ajax data
	/*public function getSalesTotal2($box_data='', $office_id=0)
	{	
		$this->loadModel('Memo');
							
		//get weekly date
		$this->loadModel('Week');		
		$c_date = date('Y-m-d');
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
			'conditions'=>array("FiscalYear.start_date <="=>date('Y-m-d'), "FiscalYear.end_date >="=>date('Y-m-d')),
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
				'memo_date >=' => date('Y-m-01'), 
				'memo_date <=' => date('Y-m-d'), 
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
				'memo_date' => date('Y-m-d'), 
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
	private function getOC($office_id = 0, $source = 0)
	{
		$this->loadModel('Memo');
		$conditions = array();
		/* --------------- week data --------------- */
		$conditions = array();
		if ($office_id)
			$conditions['office_id'] = $office_id;
		$condition = $this->condition_break_as_text($conditions);
		if ($condition) {
			$condition = "WHERE " . $condition;
		}
		$sql =
			"SElECT
				SUM(week_oc_0) as week_all,SUM(week_oc_1) as week_smcel,SUM(week_oc_2) as week_smc,
				SUM(month_oc_0) as month_all,SUM(month_oc_1) as month_smcel,SUM(month_oc_2) as month_smc,
				SUM(year_oc_0) as year_all,SUM(year_oc_1) as year_smcel,SUM(year_oc_2) as year_smc
			FROM
				dashboard_oc_summery
			$condition
			";

		/* --------------- week data --------------- */

		$result = $this->Memo->Query($sql);

		return $result;
	}
	public function admin_topBoxesData()
	{

		$office_id = $this->request->data['office_id'];

		$region_office_id = $this->request->data['region_office_id'];

		$source = $this->request->data['source'];

		if (!$office_id) {
			$this->loadModel('Office');
			$conditions = array(
				'office_type_id' 	=> 2,
				"NOT" => array("id" => array(30, 31, 37)),
				'parent_office_id' => $region_office_id
			);
			/*if($region_office_id)
			{
				$conditions['parent_office_id']= $region_office_id;
			}*/
			$offices = $this->Office->find('list', array(
				'conditions' => $conditions,
				'order' => array('office_name' => 'asc')
			));

			$office_id = array_keys($offices);
		}

		$json = array();

		// $today_SalesTotal = $this->getSalesTotal('today', 0, $office_id, $source);


		$today_SalesTotal = $this->today_topbox_data($office_id, $source, date('Y-m-d'), 'today');
		$today_rev = array();
		foreach ($today_SalesTotal as $data) {
			$json[$data[0]['type'] . '_EC'] = $data[0]['ec'];
			$json[$data[0]['type'] . '_OC'] = $data[0]['oc'];
			$json[$data[0]['type'] . '_Revenue'] = $this->priceSetting($data[0]['gross_value']);
			$today_rev[$data[0]['type'] . '_Revenue'] = $data[0]['gross_value'];
		}
		$json['today_CYP'] = round($this->getCYPTotal('today', $office_id), 2);

		$yesterday_SalesTotal = $this->today_topbox_data($office_id, $source, date('Y-m-d', strtotime('-1 days')), 'yesterday');
		foreach ($yesterday_SalesTotal as $data) {
			$json[$data[0]['type'] . '_EC'] = $data[0]['ec'];
			$json[$data[0]['type'] . '_OC'] = $data[0]['oc'];
			$json[$data[0]['type'] . '_Revenue'] = $this->priceSetting($data[0]['gross_value']);
		}
		$json['yesterday_CYP'] = round($this->getCYPTotal('yesterday', $office_id), 2);

		$other_SalesTotal = $this->getEcOCRev($office_id, $source);
		$other_oc = $this->getOC($office_id, $source);
		foreach ($other_SalesTotal as $data) {
			if ($source == 'SMCEL') {
				$json[$data[0]['type'] . '_EC'] = $data[0]['smcel_ec'] + $json['today_EC'];
				$json[$data[0]['type'] . '_OC'] = ($other_oc[0][0][$data[0]['type'] . '_smcel'] ? $other_oc[0][0][$data[0]['type'] . '_smcel'] : 0) + $json['today_OC'];
				$json[$data[0]['type'] . '_CYP'] = $data[0]['smcel_cyp'] + $json['today_CYP'];
				$json[$data[0]['type'] . '_Revenue'] = $this->priceSetting($data[0]['smcel_revenue'] + $today_rev['today_Revenue']);
			} else if ($source == 'SMC') {
				$json[$data[0]['type'] . '_EC'] = $data[0]['smc_ec'] + $json['today_EC'];
				$json[$data[0]['type'] . '_OC'] = ($other_oc[0][0][$data[0]['type'] . '_smc'] ? $other_oc[0][0][$data[0]['type'] . '_smc'] : 0) + $json['today_OC'];
				$json[$data[0]['type'] . '_CYP'] = $data[0]['smc_cyp'] + $json['today_CYP'];
				$json[$data[0]['type'] . '_Revenue'] = $this->priceSetting($data[0]['smc_revenue'] + $today_rev['today_Revenue']);
			} else {
				$json[$data[0]['type'] . '_EC'] = $data[0]['all_ec'] + $json['today_EC'];
				$json[$data[0]['type'] . '_OC'] = ($other_oc[0][0][$data[0]['type'] . '_all'] ? $other_oc[0][0][$data[0]['type'] . '_all'] : 0) + $json['today_OC'];
				$json[$data[0]['type'] . '_CYP'] = $data[0]['all_cyp'] + $json['today_CYP'];
				$json[$data[0]['type'] . '_Revenue'] = $this->priceSetting($data[0]['all_revenue'] + $today_rev['today_Revenue']);
			}
		}


		echo json_encode($json);

		exit;
	}
	//end for top boxes ajax data


	private function getOCTotal($box_data = '', $office_id = 0, $source = 0)
	{
		if ($box_data) {
			//get weekly date
			$this->loadModel('Week');
			$c_date = date('Y-m-d');
			$w_results = $this->Week->find(
				'first',
				array(
					'conditions' => array(
						'Week.start_date <=' => $c_date,
						'Week.end_date >=' => $c_date,
					),
					'recursive' => -1
				)
			);

			if ($w_results) {
				$week_start_date = $w_results['Week']['start_date'];
				$week_end_date = $w_results['Week']['end_date'];
			} else {
				$week_start_date = (date('D') == 'Sun') ? date("Y-6-d", strtotime('saturday this week')) : date("Y-6-d", strtotime('saturday last week'));
				//$week_end_date = date($week_start_date, strtotime('+5 day'));
				$day = 60 * 60 * 24;
				$today = strtotime($week_start_date);
				$week_end_date = date("Y-6-d", $today + $day * 5);
			}
			//end for week

			//start fiscale year
			$this->loadModel('FiscalYear');
			$y_results = $this->FiscalYear->find(
				'first',
				array(
					'conditions' => array("FiscalYear.start_date <=" => date('Y-m-d'), "FiscalYear.end_date >=" => date('Y-m-d')),
					'recursive' => -1
				)
			);

			if ($y_results) {
				$year_start_date = $y_results['FiscalYear']['start_date'];
				$year_end_date = $y_results['FiscalYear']['end_date'];
			} else {
				$year_start_date = date('Y-07-01');
				$year_end_date = date('Y-07-01', strtotime('+1 year'));
			}
			//end fiscale year


			$month_date = date('Y-m-01');
			$year_date = date('Y-07-01');

			$conditions = array();

			if ($office_id) {
				if ($box_data == 'week') {
					$conditions = array(
						'memo_date >=' => $week_start_date,
						'memo_date <=' => $week_end_date,
						'Memo.status !=' => 0,
						'Territory.office_id' => $office_id
					);
				} elseif ($box_data == 'month') {
					$conditions = array(
						'memo_date >=' => date('Y-m-01'),
						'memo_date <=' => date('Y-m-d'),
						'Memo.status !=' => 0,
						'Territory.office_id' => $office_id
					);
				} elseif ($box_data == 'year') {
					$conditions = array(
						'memo_date >=' => $year_start_date,
						'memo_date <=' => $year_end_date,
						'Memo.status !=' => 0,
						'Territory.office_id' => $office_id
					);
				} else {
					$conditions = array(
						'memo_date' => date('Y-m-d'),
						'Memo.status !=' => 0,
						'Territory.office_id' => $office_id
					);
				}
			} else {
				if ($box_data == 'week') {
					//$conditions = array('memo_date >=' => $week_start_date, 'Memo.status !=' => 0);
					$conditions = array(
						'memo_date >=' => $week_start_date,
						'memo_date <=' => $week_end_date,
						'Memo.status !=' => 0
					);
				} elseif ($box_data == 'month') {
					//$conditions = array('memo_date >=' => $month_date, 'Memo.status !=' => 0);
					$conditions = array(
						'memo_date >=' => date('Y-m-01'),
						'memo_date <=' => date('Y-m-d'),
						'Memo.status !=' => 0
					);
				} elseif ($box_data == 'year') {
					//$conditions = array('memo_date >=' => $year_date, 'Memo.status !=' => 0);
					$conditions = array(
						'memo_date >=' => $year_start_date,
						'memo_date <=' => $year_end_date,
						'Memo.status !=' => 0
					);
				} else {
					$conditions = array('memo_date' => date('Y-m-d'), 'Memo.status !=' => 0);
				}
			}
			if ($source) {
				$conditions['Product.source'] = $source;
			}
			$result = $this->Memo->find('count', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'table'			=>	'memo_details',
						'alias'			=>	'MemoDetail',
						'Type'			=>	'INNER',
						'conditions'	=>	'MemoDetail.memo_id=Memo.id',
					),

					array(
						'table'			=>	'products',
						'alias'			=>	'Product',
						'Type'			=>	'INNER',
						'conditions'	=>	'MemoDetail.product_id=Product.id',
					),
				),
				'fields' => 'COUNT(DISTINCT outlet_id) as count'
			));

			//pr($result);

			return $result;
		}

		return false;
	}




	private function getCYPTotal($box_data = '', $office_id = 0, $source = 0)
	{
		if ($box_data) {
			$this->loadModel('Product');
			$product_measurement = $this->Product->find('list', array(
				//'conditions'=> $pro_conditions,
				'fields' => array('Product.id', 'Product.sales_measurement_unit_id'),
				'order' =>  array('order' => 'asc'),
				'recursive' => -1
			));
			//get weekly date
			$this->loadModel('Week');
			$c_date = date('Y-m-d');
			$w_results = $this->Week->find(
				'first',
				array(
					'conditions' => array(
						'Week.start_date <=' => $c_date,
						'Week.end_date >=' => $c_date,
					),
					'recursive' => -1
				)
			);

			if ($w_results) {
				$week_start_date = $w_results['Week']['start_date'];
				$week_end_date = $w_results['Week']['end_date'];
			} else {
				$week_start_date = (date('D') == 'Sun') ? date("Y-6-d", strtotime('saturday this week')) : date("Y-6-d", strtotime('saturday last week'));
				//$week_end_date = date($week_start_date, strtotime('+5 day'));
				$day = 60 * 60 * 24;
				$today = strtotime($week_start_date);
				$week_end_date = date("Y-6-d", $today + $day * 5);
			}
			//end for week

			//start fiscale year
			$this->loadModel('FiscalYear');
			$y_results = $this->FiscalYear->find(
				'first',
				array(
					'conditions' => array("FiscalYear.start_date <=" => date('Y-m-d'), "FiscalYear.end_date >=" => date('Y-m-d')),
					'recursive' => -1
				)
			);

			if ($y_results) {
				$year_start_date = $y_results['FiscalYear']['start_date'];
				$year_end_date = $y_results['FiscalYear']['end_date'];
			} else {
				$year_start_date = date('Y-07-01');
				$year_end_date = date('Y-07-01', strtotime('+1 year'));
			}
			//end fiscale year


			$month_date = date('Y-m-01');
			$year_date = date('Y-07-01');


			//FOR CYP
			$conditions = array();

			if ($box_data == 'week') {
				$conditions = array(
					'memo_date >=' => $week_start_date,
					'memo_date <=' => $week_end_date,
					'Memo.status !=' => 0,
				);
			} elseif ($box_data == 'month') {
				$conditions = array(
					'memo_date >=' => date('Y-m-01'),
					'memo_date <=' => date('Y-m-d'),
					'Memo.status !=' => 0,
				);
			} elseif ($box_data == 'year') {
				$conditions = array(
					'memo_date >=' => $year_start_date,
					'memo_date <=' => $year_end_date,
					'Memo.status !=' => 0,
				);
			} elseif ($box_data == 'yesterday') {
				$conditions = array(
					'memo_date' => date('Y-m-d', strtotime('-1 days')),
					'Memo.status !=' => 0,
				);
			} else {
				$conditions = array(
					'memo_date' => date('Y-m-d'),
					'Memo.status !=' => 0,
				);
			}

			if ($office_id) $conditions['Territory.office_id'] = $office_id;
			if ($source) {
				$conditions['Product.source'] = $source;
			}
			$results = $this->Memo->find('all', array(
				'conditions' => $conditions,
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
				'fields' => array('sum(case when MemoDetail.price>0 then MemoDetail.sales_qty end) AS volume, sum(MemoDetail.sales_qty*MemoDetail.price) AS value', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v'),
				'group' => array('MemoDetail.product_id', 'cyp_cal', 'cyp'),
				'order' => array('MemoDetail.product_id DESC'),
				'recursive' => -1
			));



			$final_data = array();
			$sum_cyp = 0;
			$f_cyp = 0;
			foreach ($results as $result) {
				$pro_qty =  @$result[0]['volume'] ? $result[0]['volume'] : 0;
				$base_qty = $this->unit_convert($result['MemoDetail']['product_id'], $product_measurement[$result['MemoDetail']['product_id']], $pro_qty);
				$cyp_cal =  @$result[0]['cyp'] ? $result[0]['cyp'] : 0;
				$cyp_val =  @$result[0]['cyp_v'] ? $result[0]['cyp_v'] : 0;

				if ($cyp_cal) {
					if ($cyp_cal == '*') $f_cyp =  @sprintf("%01.2f", $base_qty * $cyp_val);
					if ($cyp_cal == '/') $f_cyp =  @sprintf("%01.2f", $base_qty / $cyp_val);
					if ($cyp_cal == '-') $f_cyp =  @sprintf("%01.2f", $base_qty - $cyp_val);
					if ($cyp_cal == '+') $f_cyp =  @sprintf("%01.2f", $base_qty + $cyp_val);
				} else {
					$f_cyp = 0;
				}

				//echo $cyp_cal.'<br>';
				//echo $f_cyp.'<br>';

				$sum_cyp += $f_cyp;
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
		$fiscal_year_result = $this->FiscalYear->find(
			'first',
			array(
				'conditions' => array("FiscalYear.start_date <=" => date('Y-m-d'), "FiscalYear.end_date >=" => date('Y-m-d')),
				'recursive' => -1
			)
		);

		//$fiscal_year_result = $GLOBALS['fiscal_year_result'];
		$fiscal_year_id = $fiscal_year_result['FiscalYear']['id'];

		$date_from = $fiscal_year_result['FiscalYear']['start_date'];
		$date_to = $fiscal_year_result['FiscalYear']['end_date'];

		if ($params) {
			//$date_from = $params['date_start'];
			//$date_to = $params['date_end'];
			$office_id = $params['office_id'] ? $params['office_id'] : 0;
		} else {
			$office_id = $office_id;
			$total_office = 0;
		}



		$target_revenue = 0;

		if ($product_id) {
			$data = array();


			//Achive Revenue
			$this->loadModel('MemoDetail');
			$this->loadModel('Memo');
			//$office_id = 26;
			if ($office_id) {
				$con = array(
					'MemoDetail.product_id' => $product_id,
					'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					'Memo.status !=' => 0,
					'Territory.office_id' => $office_id
				);

				$result = $this->Memo->find(
					'all',
					array(
						'conditions' => $con,
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
						'fields' => array('sum(MemoDetail.price*MemoDetail.sales_qty) AS total_Revenue'),
						'recursive' => -1
					)
				);
			} else {
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

				$sql = "SELECT sum(MemoDetail.price*MemoDetail.sales_qty) AS total_Revenue FROM [memo_details] AS [MemoDetail] LEFT JOIN [memos] AS [Memo] ON ([MemoDetail].[memo_id] = [Memo].[id]) WHERE [MemoDetail].[product_id] = " . $product_id . " AND [Memo].[memo_date] BETWEEN '" . $date_from . "' and '" . $date_to . "' AND [Memo].[status] != 0";

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

			if ($office_id) {
				$targe_month_con['aso_id'] = $office_id;
				$targe_month_con['target_category'] = 2;
			} else {
				$targe_month_con['target_category'] = 1;
			}

			$result = $this->SaleTarget->find(
				'all',
				array(
					'conditions' => $targe_month_con,
					'fields' => array('sum(amount) AS target_amount'),
					'recursive' => -1
				)
			);

			//pr($result);
			//exit;

			$target_revenue = $result[0][0]['target_amount'] ? $result[0][0]['target_amount'] : 0;


			if ($target_revenue > 0) {
				$data['target'] = round($target_revenue);
				$achievement_penchant = round(($achive_Revenue * 100) / $target_revenue);
			} else {
				$data['target'] = 0;
				$achievement_penchant = 100;
			}

			if ($achive_Revenue == 0 && $monthly_target_Revenue == 0) {
				$achievement_penchant = 0;
			}

			$data['penchant'] = $achievement_penchant;
			//pr($data);
			//exit;
			return $data;
		}



		return false;
	}



	public function getProductSalesAndTargetAllProduct($product_id = array(), $params = array())
	{
		$this->loadModel('FiscalYear');
		$fiscal_year_result = $this->FiscalYear->find(
			'first',
			array(
				'conditions' => array("FiscalYear.start_date <=" => date('Y-m-d'), "FiscalYear.end_date >=" => date('Y-m-d')),
				'recursive' => -1
			)
		);

		$fiscal_year_id = $fiscal_year_result['FiscalYear']['id'];

		$date_from = $fiscal_year_result['FiscalYear']['start_date'];
		$date_to = $fiscal_year_result['FiscalYear']['end_date'];

		if ($params) {
			$office_id = $params['office_id'] ? $params['office_id'] : 0;
		} else {
			$office_id = $office_id;
			$total_office = 0;
		}



		$target_revenue = 0;

		if ($product_id) {
			$data = array();


			//Achive Revenue
			$this->loadModel('MemoDetail');
			$this->loadModel('Memo');


			$conditions = array(
				'date >=' => $date_from,
				'date <=' => $date_to,
				'product_id' => $product_id
			);
			if ($office_id)
				$conditions['office_id'] = $office_id;
			$condition = $this->condition_break_as_text($conditions);
			$sql =
				"
				SELECT
					product_id,
					sum(value) as revenue
				FROM 
					dashboard_daily_sales_summery_product_wise
				WHERE $condition
				GROUP BY 
					product_id
			";
			$result = $this->Memo->query($sql);
			$product_wise_rev = array();
			foreach ($result as $data) {
				$product_wise_rev[$data[0]['product_id']] = round($data[0]['revenue']);
			}


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

			if ($office_id) {
				$targe_month_con['aso_id'] = $office_id;
				$targe_month_con['target_category'] = 2;
			} else {
				$targe_month_con['target_category'] = 1;
			}

			$result = $this->SaleTarget->find(
				'all',
				array(
					'conditions' => $targe_month_con,
					'fields' => array('product_id,sum(amount) AS target_amount'),
					'group' => array('product_id'),
					'recursive' => -1
				)
			);
			$product_wise_target = array();
			foreach ($result as $data) {
				$product_wise_target[$data[0]['product_id']] = $data['0']['target_amount'];
			}
			$data = array(
				'target' => $product_wise_target,
				'revenue' => $product_wise_rev
			);
			return $data;
		}



		return false;
	}

	public function getProductSalesAndTargetAllBrand($brand_id = array(), $params = array())
	{
		$this->loadModel('FiscalYear');
		$fiscal_year_result = $this->FiscalYear->find(
			'first',
			array(
				'conditions' => array("FiscalYear.start_date <=" => date('Y-m-d'), "FiscalYear.end_date >=" => date('Y-m-d')),
				'recursive' => -1
			)
		);

		$fiscal_year_id = $fiscal_year_result['FiscalYear']['id'];

		$date_from = $fiscal_year_result['FiscalYear']['start_date'];
		$date_to = $fiscal_year_result['FiscalYear']['end_date'];

		if ($params) {
			$office_id = $params['office_id'] ? $params['office_id'] : 0;
		} else {
			$office_id = 0;
			$total_office = 0;
		}



		$target_revenue = 0;

		if ($brand_id) {
			$data = array();


			//Achive Revenue
			$this->loadModel('MemoDetail');
			$this->loadModel('Memo');


			$conditions = array(
				'date >=' => $date_from,
				'date <=' => $date_to,
				'Product.brand_id' => $brand_id
			);
			if ($office_id)
				$conditions['office_id'] = $office_id;
			$condition = $this->condition_break_as_text($conditions);
			$sql =
				"
				SELECT
					Product.brand_id as brand_id,
					sum(value) as revenue
				FROM 
					dashboard_daily_sales_summery_product_wise ddss
				inner join products Product on Product.id=ddss.product_id
				WHERE $condition
				GROUP BY 
					Product.brand_id
			";

			$result = $this->Memo->query($sql);
			$product_wise_rev = array();
			foreach ($result as $data) {
				$product_wise_rev[$data[0]['brand_id']] = round($data[0]['revenue']);
			}


			//pr($result);


			//Target Revenue		
			//get month_id
			$this->loadModel('SaleTarget');

			$targe_month_con = array(
				'fiscal_year_id' => $fiscal_year_id,
				'Product.brand_id' => $brand_id
				//'month_id' => $month_id,
				//'Territory.office_id' => $office_id
			);

			if ($office_id) {
				$targe_month_con['aso_id'] = $office_id;
				$targe_month_con['target_category'] = 2;
			} else {
				$targe_month_con['target_category'] = 1;
			}

			$result = $this->SaleTarget->find(
				'all',
				array(
					'conditions' => $targe_month_con,
					'joins' => array(
						array(
							'table' => 'products',
							'alias' => 'Product',
							'type' => 'inner',
							'conditions' => 'Product.id=SaleTarget.product_id'
						),
					),
					'fields' => array('Product.brand_id as brand_id,sum(amount) AS target_amount'),
					'group' => array('Product.brand_id'),
					'recursive' => -1
				)
			);
			$product_wise_target = array();
			foreach ($result as $data) {
				$product_wise_target[$data[0]['brand_id']] = $data['0']['target_amount'];
			}
			$data = array(
				'target' => $product_wise_target,
				'revenue' => $product_wise_rev
			);
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
		$source = $this->request->data['source'];

		if (!$this->request->data['office_id']) {
			$this->loadModel('Office');
			$conditions = array(
				'office_type_id' 	=> 2,
				"NOT" => array("id" => array(30, 31, 37)),
				'parent_office_id' => $region_office_id
			);
			/*if($region_office_id)
			{
				$conditions['parent_office_id'] = $region_office_id;
			}
			*/
			$offices = $this->Office->find('list', array(
				'conditions' => $conditions,
				'order' => array('office_name' => 'asc')
			));

			$params['office_id'] = array_keys($offices);
		}


		$this->loadModel('ProductSetting');
		$conditions = array();
		if ($source) {
			$conditions['Product.source'] = $source;
		}
		$conditions['ProductSetting.product_id !='] = '0';
		$product_list = $this->ProductSetting->find(
			'list',
			array(
				'conditions' => $conditions,
				'fields' => 'product_id',
				'joins' => array(
					array(
						'table' => 'products',
						'alias' => 'Product',
						'conditions' => 'Product.id=ProductSetting.product_id',
						'type' => 'left'
					),
				),
				'order' => array('sort' => 'ASC'),
			)
		);

		$target_and_rev_data = $this->getProductSalesAndTargetAllProduct($product_list, $params);

		$conditions = array();

		$conditions['ProductSetting.product_id'] = '0';
		$brand_list = $this->ProductSetting->find(
			'list',
			array(
				'order' => array('sort' => 'ASC'),
				'conditions' => $conditions,
				'fields' => 'brand_id'
			)
		);

		$brand_target_and_rev_data = $this->getProductSalesAndTargetAllBrand($brand_list, $params);


		$conditions = array();
		if ($source) {
			$conditions['OR'] = array(
				'Product.source' => $source,
				'ProductSetting.product_id' => 0
			);
		}
		$product_settings = $this->ProductSetting->find(
			'all',
			array(
				'conditions' => $conditions,

				'order' => array('sort' => 'ASC'),
			)
		);

		$html = '';
		foreach ($product_settings as $settings) {
			$progress_text = '';
			if ($settings['ProductSetting']['product_id']) {
				$progress_text = $settings['Product']['name'];
				$target = isset($target_and_rev_data['target'][$settings['ProductSetting']['product_id']]) ? $target_and_rev_data['target'][$settings['ProductSetting']['product_id']] : 0;
				$revenue = isset($target_and_rev_data['revenue'][$settings['ProductSetting']['product_id']]) ? $target_and_rev_data['revenue'][$settings['ProductSetting']['product_id']] : 0;
			} else {
				$progress_text = $settings['Brand']['name'] . ' (Brand )';
				$target = isset($brand_target_and_rev_data['target'][$settings['ProductSetting']['brand_id']]) ? $brand_target_and_rev_data['target'][$settings['ProductSetting']['brand_id']] : 0;
				$revenue = isset($brand_target_and_rev_data['revenue'][$settings['ProductSetting']['brand_id']]) ? $brand_target_and_rev_data['revenue'][$settings['ProductSetting']['brand_id']] : 0;
			}


			if ($target > 0) {

				$achievement_penchant = round(($revenue * 100) / $target);
			} else {
				$achievement_penchant = 100;
			}

			if ($revenue == 0 && $target == 0) {
				$achievement_penchant = 0;
			}

			$html .= ' <div class="progress-group">';
			//$html.='<span class="progress-text">'.$settings['Product']['name'].'</span>';
			$html .= '<span class="progress-text">' . $progress_text . ' (' . $achievement_penchant . '%)</span>';
			$html .= '<span class="progress-number"><b>' . number_format($revenue) . '</b>/' . number_format($target) . '</span>';
			$html .= '<div class="progress sm">';
			$html .= '<div class="progress-bar" style="background-color:' . $settings['ProductSetting']['colour'] . '; width: ' . $achievement_penchant . '%"></div>';
			$html .= '</div>';
			$html .= '</div>';
		}

		echo $html;
		exit;
	}


	public function admin_sync_history()
	{

		$view = new View($this);
		if (!$view->App->menu_permission('dashboards', 'admin_index')) {
			$this->redirect('/accessDenied');
		}

		ini_set('max_execution_time', 300); //300 seconds = 5 minutes

		date_default_timezone_set('Asia/Dhaka');

		$GLOBALS['OfficeId'] = $this->UserAuth->getOfficeId();

		$this->loadModel('SalesPerson');
		$this->loadModel('Memo');
		$this->loadModel('MemoSyncHistory');
		$this->loadModel('Office');

		$sources = array('SMC' => 'SMC', 'SMCEL' => 'SMCEL');
		$this->set(compact('sources'));

		//START fiscal year & month CalCulation
		$this->loadModel('FiscalYear');
		$fiscal_year_result = $this->FiscalYear->find(
			'first',
			array(
				'conditions' => array("FiscalYear.start_date <=" => date('Y-m-d'), "FiscalYear.end_date >=" => date('Y-m-d')),
				'recursive' => -1
			)
		);

		$GLOBALS['fiscal_year_result'] = $fiscal_year_result;

		$GLOBALS['fiscal_year_id'] = $fiscal_year_id = $fiscal_year_result['FiscalYear']['id'];

		if ($this->UserAuth->getOfficeParentId() && $this->UserAuth->getOfficeParentId() != 14) {
			//area office
			$office_id = $this->UserAuth->getOfficeId();
			$region = $this->UserAuth->getOfficeParentId();
			$this->set(compact('region'));
			$this->set(compact('office_id'));
			//Territory Sales Performance Monitoring
			$this->loadModel('Office');
			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'office_type_id' => 2,
					'id' => $office_id,
					"NOT" => array("id" => array(30, 31, 37))
				),
				'order' => array('office_name' => 'asc')
			));

			$this->set(compact('offices'));
			$region_offices = $this->Office->find('list', array(
				'conditions' => array('Office.id' => $region),
				'order' => array('office_name' => 'asc')
			));
			$this->set(compact('region_offices'));
			// $this->render('aso_index');

		} elseif ($this->UserAuth->getOfficeParentId() == 14) {
			$office_id = 0;
			$this->set(compact('office_id'));


			//for regional admin
			$region_office_id = $this->UserAuth->getOfficeId();

			//Territory Sales Performance Monitoring
			$this->loadModel('Office');
			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,

					"NOT" => array("id" => array(30, 31, 37))
				),
				'order' => array('office_name' => 'asc')
			));

			$this->set(compact('offices'));
			$region = $this->UserAuth->getOfficeId();
			$this->set(compact('region'));

			//for region wise office list
			$region_offices = $this->Office->find('list', array(
				'conditions' => array('Office.id' => $region),
				'order' => array('office_name' => 'asc')
			));
			$this->set(compact('region_offices'));
		} else {
			//for admin
			$office_id = 0;
			$this->set(compact('office_id'));
			$region = 0;
			$this->set(compact('region'));

			//Territory Sales Performance Monitoring
			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'office_type_id' => 2,
					"NOT" => array("id" => array(30, 31, 37))
				),
				'order' => array('office_name' => 'asc')
			));
			$this->set(compact('offices'));
			//for region wise office list
			$region_offices = $this->Office->find('list', array(
				'conditions' => array('Office.office_type_id' => 3),
				'order' => array('office_name' => 'asc')
			));
			$this->set(compact('region_offices'));
		}
	}

	public function admin_userSyncData()
	{

		$this->loadModel('SalesPerson');
		$this->loadModel('Memo');
		$this->loadModel('MemoSyncHistory');

		$office_id = $this->request->data['office_id'];

		$region_office_id = $this->request->data['region_office_id'];

		if (!$office_id) {
			$this->loadModel('Office');
			$conditions = array(
				'office_type_id' 	=> 2,
				"NOT" => array("id" => array(30, 31, 37)),

			);

			if ($region_office_id = 'all') {
			} else {
				$conditions['parent_office_id'] = $region_office_id;
			}
			$offices = $this->Office->find('list', array(
				'conditions' => $conditions,
				'order' => array('office_name' => 'asc')
			));

			$office_id = array_keys($offices);
		}

		if ($office_id) {
			$so_list = $this->SalesPerson->find('all', array(
				'fields' => array('Office.office_name', 'RegionOffice.office_name', 'SalesPerson.id', 'SalesPerson.name', 'SalesPerson.last_data_push_time', 'Territory.name'),
				'conditions' => array(
					'SalesPerson.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					'User.user_group_id' => array(4, 1008)
				),
				'joins' => array(
					array(
						'alias' => 'RegionOffice',
						'table' => 'offices',
						'type' => 'left',
						'conditions' => 'RegionOffice.id=Office.parent_office_id'
					)
				),
				'order' => array('RegionOffice.order' => 'asc', 'Office.order' => 'asc', 'SalesPerson.name' => 'asc')
			));
		} else {
			$so_list = $this->SalesPerson->find('all', array(
				'fields' => array('Office.office_name', 'RegionOffice.office_name', 'SalesPerson.id', 'SalesPerson.name', 'SalesPerson.last_data_push_time', 'Territory.name'),
				'conditions' => array(
					//'Territory.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					'User.user_group_id' => array(4, 1008)
				),
				'joins' => array(
					array(
						'alias' => 'RegionOffice',
						'table' => 'offices',
						'type' => 'left',
						'conditions' => 'RegionOffice.id=Office.parent_office_id'
					)
				),
				'order' => array('RegionOffice.order' => 'asc', 'Office.order' => 'asc', 'SalesPerson.name' => 'asc')
			));
		}
		///pr($so_list);exit;
		//echo $this->SalesPerson->getLastQuery();exit;

		$data_array = array();
		$i = 0;
		$total = count($so_list);

		//for memo push
		foreach ($so_list as $so) {

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

			if (!empty($memo1)) {
				$last_memo_sync_date = $memo1['Memo']['memo_date'];
			} else {
				$last_memo_sync_date = $this->current_date();
			}


			$so_last_push_time = $so['SalesPerson']['last_data_push_time'];


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
				// 'order' => array('Memo.id' => 'desc'),
				'order' => array('Memo.memo_time' => 'desc'),
				'recursive' => -1
			));



			//$data['total_sync_memo'] = (!empty($memo_sync) ? $memo_sync['MemoSyncHistory']['total_memo'] : 0);

			$total_memo = (!empty($memo_sync[0][0]['total_memo']) && $memo_sync[0][0]['total_memo'] >= 0) ? $memo_sync[0][0]['total_memo'] : 0;
			$total_sync_memo = (!empty($memo) ? $memo[0][0]['total_memo'] : 0);

			$total_waiting = $total_memo - $total_sync_memo;
			$data['total_waiting_sync_memo'] = ($total_waiting < 0) ? 0 : $total_waiting;

			$data['total_memo_value'] = $memo[0][0]['total_memo_value'] ? $memo[0][0]['total_memo_value'] : 0;

			$data['total_memo'] = $total_memo;
			$data['total_sync_memo'] = $total_sync_memo;
			$data['id'] = $so['SalesPerson']['id'];
			$data['region'] = $so['RegionOffice']['office_name'];
			$data['office'] = $so['Office']['office_name'];
			$data['territory'] = $so['Territory']['name'];
			$data['name'] = $so['SalesPerson']['name'];
			$data['time'] = $so['SalesPerson']['last_data_push_time'];
			$data['hours'] = $this->get_hours($so['SalesPerson']['last_data_push_time']);

			if (!empty($last_memo_info)) {
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

		//rsort($data_array);

		$output = '';

		if ($data_array) {
			foreach ($data_array as $so) {
				$time = $so['time'] ? date('d-M, Y h:i a', strtotime($so['time'])) : '';
				$last_memo_sync = isset($so['last_memo_sync']) ? date('d-M, Y h:i a', strtotime($so['last_memo_sync'])) : '';

				if ($office_id) {

					$output .= '<tr>';

					//$label = ($so['hours'] > 24 ? 'danger' : 'success');
					$label = (isset($so['time']) && date('Y-m-d') == date('Y-m-d', strtotime($so['time'])) ? 'success' : 'danger');

					$output .= '<td>' . $so['region'] . '</td>';
					$output .= '<td>' . $so['office'] . '</td>';
					$output .= '<td>' . $so['territory'] . '</td>';
					$output .= '<td><span class="label label-' . $label . '">' . $so['name'] . '</span></td>';
					$output .= '<td>' . $last_memo_sync . '</td>';
					$output .= '<td>' . $time . '</td>';

					$output .= '</tr>';
				} else {
					/* checking the so that not synced last previous two days */

					$three_days_pre_date = date('Y-m-d', strtotime('-3 days'));
					$need_shown = (isset($so['last_memo_sync']) && ($three_days_pre_date > date('Y-m-d', strtotime($so['last_memo_sync']))));

					if ($need_shown) {

						$output .= '<tr>';

						$output .= '<td>' . $so['region'] . '</td>';
						$output .= '<td>' . $so['office'] . '</td>';
						$output .= '<td>' . $so['territory'] . '</td>';
						$label = (isset($so['last_memo_sync']) && date('Y-m-d') == date('Y-m-d', strtotime($so['last_memo_sync'])) ? 'success' : 'danger');
						$output .= '<td><span class="label label-' . $label . '">' . $so['name'] . '</span></td>';
						$output .= '<td>' . $last_memo_sync . '</td>';
						$output .= '<td>' . $time . '</td>';
						$output .= '</tr>';
					}
				}
			}
		} else {
			//$output = '<tr><td colspan="7" class="text-center"><span class="label label-danger blink_me">No Result!</span></td></tr>';

		}

		echo $output;

		//pr($data_array);
		exit;
	}



	public function getAreaOfficeSales($office_id = 0, $product_id = 0, $region = 0, $source = 0)
	{

		$this->loadModel('Memo');
		$this->loadModel('Office');



		if ($region) {
			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region,

					"NOT" => array("id" => array(30, 31, 37))
				),
				'order' => array('office_name' => 'asc')
			));
			$r_office_ids = array_keys($offices);
		}

		//$data_from = date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m-01'))));
		$data_from = date('Y-m-01');

		$conditions = array(
			'Memo.memo_date >=' => $data_from,
			'Memo.status !=' => 0,
			'Memo.gross_value >' => 0
		);

		if ($region) $conditions['Memo.office_id'] = $r_office_ids;

		if ($source) {
			$conditions['Product.source'] = $source;
		}
		$condition = '';
		foreach ($conditions as $key => $value) {
			$key = explode(" ", $key);
			$con_key = $key[0];
			$con_operator = isset($key[1]) ? $key[1] : "=";
			if (is_array($value)) {
				$con_operator = "IN";
				$value = "(" . implode(',', $value) . ")";
			} else {
				$value = "'$value'";
			}
			$condition .= "$con_key $con_operator $value AND ";
		}
		$condition = rtrim($condition, "AND ");
		$query = "SELECT 
					SUM(gross_value) as  total_Revenue
					FROM 
						(SElECT 
							Memo.id,
							Memo.memo_no,
							SUM(MemoDetail.price*MemoDetail.sales_qty) as gross_value 
						FROM memos Memo
						inner join territories Territory on Territory.id=Memo.territory_id
						inner join memo_details MemoDetail on MemoDetail.memo_id=Memo.id
						inner join products Product on Product.id=MemoDetail.product_id
						WHERE $condition
						Group by Memo.id,Memo.memo_no,Memo.gross_value) t";
		@$total_sales = $this->Memo->Query($query);

		/*$total_sales = $this->Memo->find('all', 
			array(
			'conditions'=> $condition,
			'fields'=> array('sum(Memo.gross_value) AS total_Revenue'),
			'recursive' => -1
			)
		);*/
		//pr($total_sales);
		//exit;

		$total_sale = round($total_sales[0][0]['total_Revenue'] ? $total_sales[0][0]['total_Revenue'] : 0);


		//pr($office_id);

		if ($office_id && !$product_id) {
			$conditions =  array(
				'Memo.memo_date >=' => $data_from,
				'Memo.office_id' => $office_id,
				//'Memo.territory_id ' => $territory_ids,
				'Memo.status !=' => 0
			);


			if ($source) {
				$conditions['Product.source'] = $source;
			}
			$condition = '';
			foreach ($conditions as $key => $value) {
				$key = explode(" ", $key);
				$con_key = $key[0];
				$con_operator = isset($key[1]) ? $key[1] : "=";
				if (is_array($value)) {
					$con_operator = "IN";
					$value = "(" . implode(',', $value) . ")";
				} else {
					$value = "'$value'";
				}
				$condition .= "$con_key $con_operator $value AND ";
			}
			$condition = rtrim($condition, "AND ");
			$query = "SELECT 
						SUM(gross_value) as  total_Revenue
						FROM 
							(SElECT 
								Memo.id,
								Memo.memo_no,
								SUM(MemoDetail.price*MemoDetail.sales_qty) as gross_value 
							FROM memos Memo
							inner join territories Territory on Territory.id=Memo.territory_id
							inner join memo_details MemoDetail on MemoDetail.memo_id=Memo.id
							inner join products Product on Product.id=MemoDetail.product_id
							WHERE $condition
							Group by Memo.id,Memo.memo_no,Memo.gross_value) t";
			@$office_total_sales = $this->Memo->Query($query);
			/*@$office_total_sales = $this->Memo->find('all', 
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
			);*/

			$office_sales = round($office_total_sales[0][0]['total_Revenue'] ? $office_total_sales[0][0]['total_Revenue'] : 0);
			//echo $office_sales;
			//exit;

			return @$office_sales_percentage = sprintf("%01.2f", ($office_sales * 100) / $total_sale);
		}




		//Product Percentage
		if ($product_id > 0) {
			$con = array(
				'MemoDetail.product_id' => $product_id,
				'Memo.memo_date >=' => $data_from,
				'Memo.status !=' => 0
			);

			if ($region) $con['Memo.office_id'] = $r_office_ids;

			$product_total_sales = $result = $this->Memo->find(
				'all',
				array(
					'conditions' => $con,
					'joins' => array(
						array(
							'alias' => 'MemoDetail',
							'table' => 'memo_details',
							'type' => 'INNER',
							'conditions' => 'Memo.id = MemoDetail.memo_id'
						)
					),
					'fields' => array('sum(MemoDetail.price*MemoDetail.sales_qty) AS total_Revenue'),
					//'fields'=> array('MemoDetail.price', 'MemoDetail.sales_qty', 'Memo.memo_date', 'MemoDetail.product_id'),
					'recursive' => -1
				)
			);

			//pr($product_total_sales);
			//echo $total_sale.'<br>';

			$product_sales = $product_total_sales[0][0]['total_Revenue'];

			return @$product_sales_percentage = sprintf("%01.2f", ($product_sales * 100) / $total_sale);
		}

		//exit;

		return false;
	}


	public function getAreaOfficeTerritorySales($territory_id = 0, $product_id = 0, $office_id = 0, $source = 0)
	{

		$this->loadModel('Memo');


		/*$office_id = $GLOBALS['OfficeId'];
		
		$this->loadModel('Territory');
		$territories = $this->Territory->find('list', array('conditions'=> array('office_id' => $office_id), 'order'=>array('name'=>'asc')));
		
		$territory_ids = array();
		foreach($territories as $key => $value ){
			array_push($territory_ids, $key);
		}*/

		$data_from = date('Y-m-01');
		$conditions = array(
			'Memo.memo_date >=' => $data_from,
			'Memo.status !=' => 0,
			//'Territory.office_id' => $office_id
			'Memo.office_id' => $office_id,
		);

		//$data_from = date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m-01'))));

		//count office total sales
		if ($source) {
			$conditions['Product.source'] = $source;
		}
		$condition = '';
		foreach ($conditions as $key => $value) {
			$key = explode(" ", $key);
			$con_key = $key[0];
			$con_operator = isset($key[1]) ? $key[1] : "=";
			if (is_array($value)) {
				$con_operator = "IN";
				$value = "(" . implode(',', $value) . ")";
			} else {
				$value = "'$value'";
			}
			$condition .= "$con_key $con_operator $value AND ";
		}
		$condition = rtrim($condition, "AND ");
		$query = "SELECT 
					SUM(gross_value) as  total_Revenue
					FROM 
						(SElECT 
							Memo.id,
							Memo.memo_no,
							SUM(MemoDetail.price*MemoDetail.sales_qty) as gross_value 
						FROM memos Memo
						inner join territories Territory on Territory.id=Memo.territory_id
						inner join memo_details MemoDetail on MemoDetail.memo_id=Memo.id
						inner join products Product on Product.id=MemoDetail.product_id
						WHERE $condition
						Group by Memo.id,Memo.memo_no,Memo.gross_value) t";
		@$total_sales = $this->Memo->Query($query);
		/*@$total_sales = $this->Memo->find('all', 
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
		);*/
		//pr($total_sales);
		//exit;

		$total_sale = round($total_sales[0][0]['total_Revenue'] ? $total_sales[0][0]['total_Revenue'] : 0);

		if ($territory_id > 0) {
			$conditions = array(
				'Memo.memo_date >=' => $data_from,
				'Memo.territory_id' => $territory_id,
				'Memo.status !=' => 0
			);
			if ($source) {
				$conditions['Product.source'] = $source;
			}
			$condition = '';
			foreach ($conditions as $key => $value) {
				$key = explode(" ", $key);
				$con_key = $key[0];
				$con_operator = isset($key[1]) ? $key[1] : "=";
				if (is_array($value)) {
					$con_operator = "IN";
					$value = "(" . implode(',', $value) . ")";
				} else {
					$value = "'$value'";
				}
				$condition .= "$con_key $con_operator $value AND ";
			}
			$condition = rtrim($condition, "AND ");
			$query = "SELECT 
						SUM(gross_value) as  total_Revenue
						FROM 
							(SElECT 
								Memo.id,
								Memo.memo_no,
								SUM(MemoDetail.price*MemoDetail.sales_qty) as gross_value 
							FROM memos Memo
							inner join territories Territory on Territory.id=Memo.territory_id
							inner join memo_details MemoDetail on MemoDetail.memo_id=Memo.id
							inner join products Product on Product.id=MemoDetail.product_id
							WHERE $condition
							Group by Memo.id,Memo.memo_no,Memo.gross_value) t";
			@$territory_total_sales = $this->Memo->Query($query);
			/*$territory_total_sales = $this->Memo->find('all', 
				array(
				'conditions'=> array(
					'Memo.memo_date >=' => $data_from,
					'Memo.territory_id ' => $territory_id,
					'Memo.status !=' => 0
				),
				'fields'=> array('sum(Memo.gross_value) AS total_Revenue'),
				'recursive' => -1
				)
			);*/
			$territory_sales = round($territory_total_sales[0][0]['total_Revenue'] ? $territory_total_sales[0][0]['total_Revenue'] : 0);
			return @$territory_sales_percentage = sprintf("%01.2f", ($territory_sales * 100) / $total_sale);
		}


		//Product Percentage
		if ($product_id > 0) {
			$this->loadModel('MemoDetail');
			@$product_total_sales = $result = $this->Memo->find(
				'all',
				array(
					'conditions' => array(
						'MemoDetail.product_id' => $product_id,
						'Memo.office_id' => $office_id,
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
					'fields' => array('sum(MemoDetail.price*MemoDetail.sales_qty) AS total_Revenue'),
					//'fields'=> array('MemoDetail.price', 'MemoDetail.sales_qty', 'Memo.memo_date', 'MemoDetail.product_id'),
					'recursive' => -1
				)
			);



			/*$product_sales = 0;
			foreach($product_total_sales as $val){
				$product_sales += $val['MemoDetail']['price']*$val['MemoDetail']['sales_qty'];
			}*/

			$product_sales = $product_total_sales[0][0]['total_Revenue'];



			return @$product_sales_percentage = sprintf("%01.2f", ($product_sales * 100) / $total_sale);
		}

		return false;
	}



	public function getStocks($product_id = 0, $office_id = 0)
	{

		$this->loadModel('CurrentCwhInventory');
		$this->loadModel('Store');

		$data = array();

		if ($office_id) {
			$so_con = array(
				'CurrentCwhInventory.inventory_status_id' => 1,
				'Store.store_type_id' => 3,
				'Store.office_id' => $office_id,
				'CurrentCwhInventory.product_id' => $product_id,
				'CurrentCwhInventory.transaction_date' => date('Y-m-d')
			);

			$aso_con = array(
				'CurrentCwhInventory.inventory_status_id' => 1,
				'Store.store_type_id' => 2,
				'Store.office_id' => $office_id,
				'CurrentCwhInventory.product_id' => $product_id,
				'CurrentCwhInventory.transaction_date' => date('Y-m-d')
			);
		} else {
			$so_con = array(
				'CurrentCwhInventory.inventory_status_id' => 1,
				'Store.store_type_id' => 3,
				'CurrentCwhInventory.product_id' => $product_id,
				'CurrentCwhInventory.transaction_date' => date('Y-m-d')
			);

			$aso_con = array(
				'CurrentCwhInventory.inventory_status_id' => 1,
				'Store.store_type_id' => 2,
				'CurrentCwhInventory.product_id' => $product_id,
				'CurrentCwhInventory.transaction_date' => date('Y-m-d')
			);
		}

		//get SO stock qty
		$results = $this->CurrentCwhInventory->find('all', array(
			'conditions' => $so_con,
			'joins' => array(array(
				'alias' => 'Store',
				'table' => 'stores',

				'type' => 'INNER',
				'conditions' => array(
					'CurrentCwhInventory.store_id = Store.id'
				)
			)),
			'fields' => array('sum(CurrentCwhInventory.qty) AS so_qty'),
			'recursive' => -1,
		));
		$data['so_stock'] = $results[0][0]['so_qty'];

		//get ASO stock qty
		$results = $this->CurrentCwhInventory->find('all', array(
			'conditions' => $aso_con,
			'joins' => array(array(
				'alias' => 'Store',
				'table' => 'stores',

				'type' => 'INNER',
				'conditions' => array(
					'CurrentCwhInventory.store_id = Store.id'
				)
			)),
			'fields' => array('sum(CurrentCwhInventory.qty) AS aso_qty'),
			'recursive' => -1,
		));
		$data['aso_stock'] = $results[0][0]['aso_qty'];


		//get CWH stock qty
		$cwh_con = array(
			'CurrentCwhInventory.region_office_id' => 0,
			'CurrentCwhInventory.store_type' => 'CWH',
			//'Store.office_id' => $office_id,
			'CurrentCwhInventory.product_id' => $product_id,
			'CurrentCwhInventory.transaction_date' => date('Y-m-d')
		);
		$results = $this->CurrentCwhInventory->find('all', array(
			'conditions' => $cwh_con,
			'joins' => array(array(
				'alias' => 'Store',
				'table' => 'stores',
				'type' => 'INNER',
				'conditions' => array(
					'CurrentCwhInventory.store_id = Store.id'
				)
			)),
			'fields' => array('sum(CurrentCwhInventory.qty) AS aso_qty'),
			'recursive' => -1,
		));
		if (!$office_id) {
			$data['cwh_stock'] = $results[0][0]['aso_qty'];
		} else {
			$data['cwh_stock'] = 0;
		}

		return $data;
	}

	//stock status ajax data
	public function admin_stockStatusData()
	{

		$office_id = $this->request->data['office_id'];

		$region_office_id = $this->request->data['region_office_id'];
		$source = $this->request->data['source'];

		if (!$office_id) {
			$this->loadModel('Office');
			$conditions = array(
				'office_type_id' 	=> 2,
				"NOT" => array("id" => array(30, 31, 37)),
				'parent_office_id' => $region_office_id
			);

			$offices = $this->Office->find('list', array(
				'conditions' => $conditions,
				'order' => array('office_name' => 'asc')
			));

			$office_id = array_keys($offices);
		}

		$json = array();

		$this->loadModel('ReportProductSetting');
		$conditions = array();
		if ($source) {
			$conditions['Product.source'] = $source;
		}

		$report_products = $this->ReportProductSetting->find('all', array('order' => array('sort' => 'ASC'), 'conditions' => $conditions));
		//echo $this->ReportProductSetting->getLastQuery();exit;
		$output = '';

		foreach ($report_products as $report_product) {
			$data = $this->getStocks($report_product['ReportProductSetting']['product_id'], $office_id);

			$output .= '<tr>';
			$output .= '<td>' . $report_product['Product']['name'] . '</td>';
			$output .= '<td class="text-right">' . number_format($data['so_stock']) . '</td>';
			$output .= '<td class="text-right">' . number_format($data['aso_stock']) . '</td>';
			$output .= '<td class="text-right">' . number_format(($data['cwh_stock'])) . '</td>';
			$output .= '<td class="text-right">' . number_format(($data['so_stock'] + $data['aso_stock'] + $data['cwh_stock'])) . '</td>';
			$output .= '</tr>';
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
	public function admin_achievementTargetData()
	{

		$office_id = $this->request->data['office_id'];
		$region_office_id = $this->request->data['region_office_id'];
		$source = $this->request->data['source'];

		if (!$office_id) {
			$this->loadModel('Office');
			$conditions = array(
				'office_type_id' 	=> 2,
				"NOT" => array("id" => array(30, 31, 37)),
				'parent_office_id' => $region_office_id
			);
			/*if($region_office_id)
			{
				$conditions['parent_office_id'] = $region_office_id;
			}*/
			$offices = $this->Office->find('list', array(
				'conditions' => $conditions,
				'order' => array('office_name' => 'asc')
			));

			$office_id = array_keys($offices);
		}


		//pr($office_id);

		//START fiscal year & month CalCulation
		$this->loadModel('FiscalYear');
		$fiscal_year_result = $this->FiscalYear->find(
			'first',
			array(
				'conditions' => array("FiscalYear.start_date <=" => date('Y-m-d'), "FiscalYear.end_date >=" => date('Y-m-d')),
				'recursive' => -1
			)
		);

		$fiscal_year_id = $fiscal_year_result['FiscalYear']['id'];



		$json = array();

		$this->loadModel('Memo');
		//$data_from = date('Y-m-01');


		$date_from = $fiscal_year_result['FiscalYear']['start_date'];
		$date_to = $fiscal_year_result['FiscalYear']['end_date'];




		$conditions = array(
			'date >=' => $date_from,
			'date <=' =>  $date_to
		);
		if ($office_id)
			$conditions['office_id'] = $office_id;

		$condition = $this->condition_break_as_text($conditions);

		$query =
			"
			SElECT
				SUM(revenue_0) all_revenue,SUM(revenue_1) smcel_revenue,SUM(revenue_2) smc_revenue
			FROM
				dashboard_daily_sales_summery
			WHERE $condition
		";
		$achievement_result = $this->Memo->Query($query);
		if ($source == 'SMCEL') {
			$achievement_raw = $achievement_result[0][0]['smcel_revenue'];
			$$achievement_amount = $this->priceSetting($achievement_result[0][0]['smcel_revenue']);
		} else if ($source == 'SMC') {
			$achievement_raw = $achievement_result[0][0]['smc_revenue'];
			$achievement_amount = $this->priceSetting($achievement_result[0][0]['smc_revenue']);
		} else {
			$achievement_raw = $achievement_result[0][0]['all_revenue'];
			$achievement_amount = $this->priceSetting($achievement_result[0][0]['all_revenue']);
		}
		$json['achievement_amount'] = $achievement_amount;



		//get total_target
		$this->loadModel('SaleTarget');
		$conditions = array(
			'fiscal_year_id' => $fiscal_year_id,
			'target_category' => 1,

		);
		if ($source) {
			$conditions['Product.source'] = $source;
		}
		if ($office_id) {
			$conditions['aso_id'] = $office_id;
			$conditions['target_category'] = 1;
		}
		$total_target_result = $this->SaleTarget->find(
			'all',
			array(
				'conditions' => $conditions,
				'fields' => array('sum(amount) AS total_amount'),
				'recursive' => 0
			)
		);

		if ($total_target_result[0][0]['total_amount'] > 0) {
			//$total_target = ($total_target_result[0][0]['total_amount']/12);
			$total_target = ($total_target_result[0][0]['total_amount']);
		} else {
			$total_target = 0;
		}
		$json['total_target'] = $this->priceSetting($total_target);


		if ($total_target > 0) {
			$achievement_penchant = round(($achievement_raw * 100) / $total_target);
		} else {
			$achievement_penchant = 100;
		}

		$json['achievement_penchant'] = $achievement_penchant;

		echo json_encode($json);

		$this->autoRender = false;
	}
	//end for achievement vs target ajax data


	private function getSalesTrendSalesMonthWise($office_id, $source)
	{
		$this->loadModel('Memo');
		$date_to = date('Y-m-d');
		$date_from = date('Y-m-d', strtotime('-5 month', strtotime(date('Y-m-01'))));
		/* ---------------- Year Data --------------- */
		$conditions = array(
			'date >=' => $date_from,
			'date <=' => $date_to
		);
		if ($office_id)
			$conditions['office_id'] = $office_id;

		$condition = $this->condition_break_as_text($conditions);
		$sql =
			"SElECT
			FORMAT(date,'yyyy-MM') as month,
			SUM(revenue_0) all_revenue,SUM(revenue_1) smcel_revenue,SUM(revenue_2) smc_revenue
		FROM
			dashboard_daily_sales_summery
		WHERE $condition
		GROUP BY FORMAT(date,'yyyy-MM')
		ORDER BY
		FORMAT(date,'yyyy-MM') DESC
		";
		/* ---------------- Year Data --------------- */


		$result = $this->Memo->Query($sql);
		/* pr($result);
		echo $this->Memo->getLastQuery();
		exit; */
		$data_array = array();
		foreach ($result as $data) {
			if ($source == 'SMCEL') {
				$data_array[$data['0']['month']] = sprintf("%01.2f", $data[0]['smcel_revenue'] / 1000000);
			} else if ($source == 'SMC') {
				$data_array[$data['0']['month']] = sprintf("%01.2f", $data[0]['smc_revenue'] / 1000000);
			} else {
				$data_array[$data['0']['month']] = sprintf("%01.2f", $data[0]['all_revenue'] / 1000000);
			}
		}
		return $data_array;
	}
	//stock status ajax data
	public function admin_salesTrendData()
	{

		$office_id = $this->request->data['office_id'];

		$region_office_id = $this->request->data['region_office_id'];
		$source = $this->request->data['source'];

		if (!$office_id) {
			$this->loadModel('Office');
			$conditions = array(
				'office_type_id' 	=> 2,
				"NOT" => array("id" => array(30, 31, 37)),
				'parent_office_id' => $region_office_id
			);
			/*if($region_office_id)
			{
				$conditions['parent_office_id'] = $region_office_id;
			}*/
			$offices = $this->Office->find('list', array(
				'conditions' => $conditions,
				'order' => array('office_name' => 'asc')
			));

			$office_id = array_keys($offices);
		}


		$json = array();
		$month_wise_sales_total = $this->getSalesTrendSalesMonthWise($office_id, $source);

		/* $m_1_total_amount =  $this->getSalesTotal('', date('Y-m', time()), $office_id, $source);
		$m_2_total_amount =  $this->getSalesTotal('', date('Y-m', strtotime('-1 month', strtotime(date('Y-m-01')))), $office_id, $source);
		$m_3_total_amount =  $this->getSalesTotal('', date('Y-m', strtotime('-2 month', strtotime(date('Y-m-01')))), $office_id, $source);
		$m_4_total_amount =  $this->getSalesTotal('', date('Y-m', strtotime('-3 month', strtotime(date('Y-m-01')))), $office_id, $source);
		$m_5_total_amount =  $this->getSalesTotal('', date('Y-m', strtotime('-4 month', strtotime(date('Y-m-01')))), $office_id, $source);
		$m_6_total_amount =  $this->getSalesTotal('', date('Y-m', strtotime('-5 month', strtotime(date('Y-m-01')))), $office_id, $source); */




		$json['m_1_total_amount'] = $month_wise_sales_total[date('Y-m', time())];
		$json['m_2_total_amount'] = $month_wise_sales_total[date('Y-m', strtotime('-1 month', strtotime(date('Y-m-01'))))];
		$json['m_3_total_amount'] = $month_wise_sales_total[date('Y-m', strtotime('-2 month', strtotime(date('Y-m-01'))))];
		$json['m_4_total_amount'] = $month_wise_sales_total[date('Y-m', strtotime('-3 month', strtotime(date('Y-m-01'))))];
		$json['m_5_total_amount'] = $month_wise_sales_total[date('Y-m', strtotime('-4 month', strtotime(date('Y-m-01'))))];
		$json['m_6_total_amount'] = $month_wise_sales_total[date('Y-m', strtotime('-5 month', strtotime(date('Y-m-01'))))];

		echo json_encode($json);

		$this->autoRender = false;
	}

	public function getAreaOfficeTerritoryWiseSales($territory_id = array(), $office_id = 0, $source = 0)
	{

		$this->loadModel('Memo');
		$data_from = date('Y-m-01');
		$conditions = array(
			'date >=' => $data_from,
			'office_id' => $office_id
		);
		//count office total sales

		$condition = $this->condition_break_as_text($conditions);
		$query =
			"
			SElECT
				SUM(revenue_0) all_revenue,SUM(revenue_1) smcel_revenue,SUM(revenue_2) smc_revenue
			FROM
				dashboard_daily_sales_summery
			WHERE $condition
		";
		@$total_sales = $this->Memo->Query($query);

		// $total_sale = round($total_sales[0][0]['total_Revenue'] ? $total_sales[0][0]['total_Revenue'] : 0);

		if ($source == 'SMCEL') {
			$total_sale = round($total_sales[0][0]['smcel_revenue']);
		} else if ($source == 'SMC') {
			$total_sale = round($total_sales[0][0]['smc_revenue']);
		} else {
			$total_sale = round($total_sales[0][0]['all_revenue']);
		}


		if ($territory_id > 0) {
			$conditions = array(
				'date >=' => $data_from,
				'territory_id' => $territory_id
			);
			$condition = $this->condition_break_as_text($conditions);
			$query =
				"
				SElECT
				territory_id,
				SUM(revenue_0) all_revenue,SUM(revenue_1) smcel_revenue,SUM(revenue_2) smc_revenue
				FROM
					dashboard_daily_sales_summery
				WHERE $condition
				GROUP BY
				territory_id
			";
			@$territory_total_sales = $this->Memo->Query($query);
			/* echo $this->Memo->getLastQuery();
			pr($territory_total_sales);
			exit; */
			$territory_sales_percentage = array();
			foreach ($territory_total_sales as $data) {

				if ($source == 'SMCEL') {
					$territory_sales_percentage[$data['0']['territory_id']] = sprintf("%01.2f", ($data[0]['smcel_revenue'] * 100) / $total_sale);
				} else if ($source == 'SMC') {
					$territory_sales_percentage[$data['0']['territory_id']] = sprintf("%01.2f", ($data[0]['smc_revenue'] * 100) / $total_sale);
				} else {
					$territory_sales_percentage[$data['0']['territory_id']] =  sprintf("%01.2f", ($data[0]['all_revenue'] * 100) / $total_sale);
				}
			}
			$territory_sales_percentage['total_sales'] = $total_sale;
			return $territory_sales_percentage;
		}
		return false;
	}
	public function getAreaOfficeWiseSales($office_id = array(), $source = 0)
	{

		$this->loadModel('Memo');
		$data_from = date('Y-m-01');
		$conditions = array(
			'date >=' => $data_from,
			'office_id' => $office_id
		);
		//count office total sales

		$condition = $this->condition_break_as_text($conditions);
		$query =
			"
			SElECT
				SUM(revenue_0) all_revenue,SUM(revenue_1) smcel_revenue,SUM(revenue_2) smc_revenue
			FROM
				dashboard_daily_sales_summery
			WHERE $condition
		";
		@$total_sales = $this->Memo->Query($query);

		// $total_sale = round($total_sales[0][0]['total_Revenue'] ? $total_sales[0][0]['total_Revenue'] : 0);

		if ($source == 'SMCEL') {
			$total_sale = round($total_sales[0][0]['smcel_revenue']);
		} else if ($source == 'SMC') {
			$total_sale = round($total_sales[0][0]['smc_revenue']);
		} else {
			$total_sale = round($total_sales[0][0]['all_revenue']);
		}


		if ($office_id) {
			$conditions = array(
				'date >=' => $data_from,
				'office_id' => $office_id
			);
			$condition = $this->condition_break_as_text($conditions);
			$query =
				"
				SElECT
				office_id,
				SUM(revenue_0) all_revenue,SUM(revenue_1) smcel_revenue,SUM(revenue_2) smc_revenue
				FROM
					dashboard_daily_sales_summery
				WHERE $condition
				GROUP BY
				office_id
			";
			@$office_total_sales = $this->Memo->Query($query);
			/* echo $this->Memo->getLastQuery();
			pr($office_total_sales);
			exit; */
			$office_sales_percentage = array();
			foreach ($office_total_sales as $data) {

				if ($source == 'SMCEL') {
					$office_sales_percentage[$data['0']['office_id']] = sprintf("%01.2f", ($data[0]['smcel_revenue'] * 100) / $total_sale);
				} else if ($source == 'SMC') {
					$office_sales_percentage[$data['0']['office_id']] = sprintf("%01.2f", ($data[0]['smc_revenue'] * 100) / $total_sale);
				} else {
					$office_sales_percentage[$data['0']['office_id']] =  sprintf("%01.2f", ($data[0]['all_revenue'] * 100) / $total_sale);
				}
			}
			$office_sales_percentage['total_sales'] = $total_sale;
			return $office_sales_percentage;
		}
		return false;
	}

	private function ProductWiseSalesDataPie($product_id = array(), $office_id = 0, $total_sale)
	{
		$this->loadModel('Memo');
		$data_from = date('Y-m-01');

		if ($product_id > 0) {
			$conditions = array(
				'date >=' => $data_from,
				'product_id' => $product_id
			);
			if ($office_id)
				$conditions['office_id'] = $office_id;
			$condition = $this->condition_break_as_text($conditions);
			$query =
				"
				SELECT
					product_id,
					sum(value) as revenue
				FROM 
					dashboard_daily_sales_summery_product_wise
				WHERE $condition
				GROUP BY 
					product_id
			";
			@$product_wise_sales = $this->Memo->Query($query);
			/* echo $this->Memo->getLastQuery();
			pr($territory_total_sales);
			exit; */
			$product_wise_sales_percentage = array();
			foreach ($product_wise_sales as $data) {
				$product_wise_sales_percentage[$data[0]['product_id']] = sprintf("%01.2f", ($data[0]['revenue'] * 100) / $total_sale);
			}

			return $product_wise_sales_percentage;
		}
		return false;
	}

	private function brandWiseSalesDataPie($brand_id = array(), $office_id = 0, $total_sale)
	{
		$this->loadModel('Memo');
		$data_from = date('Y-m-01');

		if ($brand_id > 0) {
			$conditions = array(
				'date >=' => $data_from,
				'p.brand_id' => $brand_id
			);
			if ($office_id)
				$conditions['office_id'] = $office_id;
			$condition = $this->condition_break_as_text($conditions);
			$query =
				"
				SELECT
					p.brand_id brand_id,
					sum(value) as revenue
				FROM 
					dashboard_daily_sales_summery_product_wise ddss
				inner join products p on p.id=ddss.product_id
				WHERE $condition
				GROUP BY 
					p.brand_id
			";
			@$brand_wise_sales = $this->Memo->Query($query);
			/* echo $this->Memo->getLastQuery();
			pr($territory_total_sales);
			exit; */
			$brand_wise_sales_percentage = array();
			foreach ($brand_wise_sales as $data) {
				$brand_wise_sales_percentage[$data[0]['brand_id']] = sprintf("%01.2f", ($data[0]['revenue'] * 100) / $total_sale);
			}

			return $brand_wise_sales_percentage;
		}
		return false;
	}

	public function getPieChartData()
	{
		$office_id = $this->request->data['office_id'];
		$region_office_id = $this->request->data['region_office_id'];
		$source = $this->request->data['source'];
		/*--------- finding Pie product from setting ------------------*/
		$this->loadModel('PieProductSetting');
		$conditions = array();
		if ($source) {
			$conditions['OR'] = array(
				'Product.source' => $source,
				'PieProductSetting.product_id' => 0
			);
		}

		$pie_products = $this->PieProductSetting->find(
			'all',
			array(
				'order' => array('sort' => 'ASC'),
				'conditions' => $conditions,
			)
		);
		$conditions['PieProductSetting.product_id !='] = 0;
		$pie_products_list = $this->PieProductSetting->find('list', array(
			'order' => array('sort' => 'ASC'),
			'conditions' => $conditions,
			'fields' => array('product_id'),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'conditions' => 'Product.id=PieProductSetting.product_id',
					'type' => 'left'
				),
			),
		));
		$conditions = array();
		$conditions['PieProductSetting.product_id ='] = 0;
		$pie_brand_list = $this->PieProductSetting->find('list', array(
			'order' => array('sort' => 'ASC'),
			'conditions' => $conditions,
			'fields' => array('brand_id')
		));
		if ($office_id) {
			$this->loadModel('Territory');
			$territorieslist = $this->Territory->find('all', array(
				'conditions' => array('Territory.office_id' => $office_id),
				'fields' => array('Territory.id', 'Territory.name', 'Territory.short_name'),
				'order' => array('Territory.name' => 'asc')
			));
			$chart_legend = '';
			$pie_data = array();
			$i = 0;

			$territories = array();

			foreach ($territorieslist as $val) {
				$territories[$val['Territory']['id']] = $val['Territory']['name'];
			}

			$territory_wise_sales_summery = $this->getAreaOfficeTerritoryWiseSales(array_keys($territories), $office_id, $source);
			if (!empty($pie_products_list))
				$product_wise = $this->ProductWiseSalesDataPie(array_values($pie_products_list), $office_id, $territory_wise_sales_summery['total_sales']);
			if (!empty($pie_brand_list))
				$brand_wise = $this->brandWiseSalesDataPie(array_values($pie_brand_list), $office_id, $territory_wise_sales_summery['total_sales']);

			foreach ($territorieslist as $territoryval) {

				$key = $territoryval['Territory']['id'];
				$value = $territoryval['Territory']['name'];
				$shortname = $territoryval['Territory']['short_name'];

				if ($i == 0) {
					$color = 'f56954';
				}
				if ($i == 1) {
					$color = 'f39c12';
				}
				if ($i == 2) {
					$color = '00c0ef';
				}
				if ($i == 3) {
					$color = '0073b7';
				}
				if ($i == 4) {
					$color = '222222';
				}
				if ($i == 5) {
					$color = '4043A0';
				}
				if ($i == 6) {
					$color = '00a65a';
				}

				if ($i == 7) {
					$color = '001f3f';
				}
				if ($i == 8) {
					$color = '39cccc';
				}
				if ($i == 9) {
					$color = '3d9970';
				}
				if ($i == 10) {
					$color = '01ff70';
				}
				if ($i == 11) {
					$color = 'ff851b';
				}
				if ($i == 12) {
					$color = 'f012be';
				}
				if ($i == 13) {
					$color = '932ab6';
				}
				if ($i == 14) {
					$color = '85144b';
				}
				if ($i == 15) {
					$color = '888';
				}
				if ($i == 16) {
					$color = 'd2d6de';
				}

				$chart_legend .= '<li><i style="color:#' . $color . '" class="fa fa-circle-o"></i> ' . $value . '</li>';

				$value_pie_data = str_replace('Sales ', '', $value);
				$pie_data[] = array(
					'value'    	=> isset($territory_wise_sales_summery[$key]) ? $territory_wise_sales_summery[$key] : 0,
					'color'    	=> "#$color",
					'highlight' => "#$color",
					'fullname'    	=> "$value_pie_data",
					'shortname'    	=> "$shortname",
				);
				$i++;
			}
			$pie_product_data = '';
			$i = 1;
			foreach ($pie_products as $pie_product) {

				$pie_text = '';
				$pie_amount = 0;
				if ($pie_product['PieProductSetting']['product_id']) {
					$pie_text = $pie_product['Product']['name'];
					$pie_amount = (isset($product_wise[$pie_product['PieProductSetting']['product_id']]) ? $product_wise[$pie_product['PieProductSetting']['product_id']] : 0);
				} else {
					$pie_text = $pie_product['Brand']['name'] . '(Brand)';
					$pie_amount = (isset($brand_wise[$pie_product['PieProductSetting']['brand_id']]) ? $brand_wise[$pie_product['PieProductSetting']['brand_id']] : 0);
				}

				$pie_product_data .= "<li><a>" . $pie_text . " <span class='pull-right text-red'> " . $pie_amount . "%</span></a></li>";
				$i++;
			}
		} else {
			$conditions = array(
				'Office.office_type_id' 	=> 2,
				"NOT" => array("Office.id" => array(30, 31, 37))
			);
			if ($region_office_id) {
				$conditions['Office.parent_office_id'] = $region_office_id;
			}
			$this->loadModel('Office');
			$officeList = $this->Office->find('all', array(
				'conditions' => $conditions,
				'fields' => array('Office.id', 'Office.office_name', 'Office.short_name'),
				'order' => array('Office.office_name' => 'asc'),
				'recursive' => -1
			));

			$offices = array();

			foreach ($officeList as $val) {
				$offices[$val['Office']['id']] = $val['Office']['office_name'];
			}

			$area_wise_sales_summery = $this->getAreaOfficeWiseSales(array_keys($offices), $office_id, $source);
			if (!empty($pie_products_list))
				$product_wise = $this->ProductWiseSalesDataPie(array_values($pie_products_list), array_keys($offices), $area_wise_sales_summery['total_sales']);
			if (!empty($pie_brand_list))
				$brand_wise = $this->brandWiseSalesDataPie(array_values($pie_brand_list), array_keys($offices), $area_wise_sales_summery['total_sales']);
			$chart_legend = '';
			$pie_data = array();
			$i = 0;
			foreach ($officeList as $officeval) {
				$key = $officeval['Office']['id'];
				$value = $officeval['Office']['office_name'];
				$short_name = $officeval['Office']['short_name'];
				if ($key != 14) {
					if ($i == 0) {
						$color = 'f56954';
					}
					if ($i == 1) {
						$color = 'f39c12';
					}
					if ($i == 2) {
						$color = '00c0ef';
					}
					if ($i == 3) {
						$color = '0073b7';
					}
					if ($i == 4) {
						$color = '222222';
					}
					if ($i == 5) {
						$color = '4043A0';
					}
					if ($i == 6) {
						$color = '00a65a';
					}

					if ($i == 7) {
						$color = '001f3f';
					}
					if ($i == 8) {
						$color = '39cccc';
					}
					if ($i == 9) {
						$color = '3d9970';
					}
					if ($i == 10) {
						$color = '01ff70';
					}
					if ($i == 11) {
						$color = 'ff851b';
					}
					if ($i == 12) {
						$color = 'f012be';
					}
					if ($i == 13) {
						$color = '932ab6';
					}
					if ($i == 14) {
						$color = '85144b';
					}
					if ($i == 15) {
						$color = '888';
					}
					if ($i == 16) {
						$color = 'd2d6de';
					}

					$chart_legend .= '<li><i style="color:#' . $color . '" class="fa fa-circle-o"></i> ' . $value . '</li>';
					$value_pie_data = str_replace('Sales ', '', $value);
					$pie_data[] = array(
						'value'    	=> isset($area_wise_sales_summery[$key]) ? $area_wise_sales_summery[$key] : 0,
						'color'    	=> "#$color",
						'highlight' => "#$color",
						'fullname'    	=> "$value_pie_data",
						'shortname'    	=> "$short_name"
					);
					$i++;
				}
			}
			$office_id = array_keys($offices);
			$pie_product_data = '';
			$i = 1;
			foreach ($pie_products as $pie_product) {
				$pie_text = '';
				$pie_amount = 0;
				if ($pie_product['PieProductSetting']['product_id']) {
					$pie_text = $pie_product['Product']['name'];
					$pie_amount = (isset($product_wise[$pie_product['PieProductSetting']['product_id']]) ? $product_wise[$pie_product['PieProductSetting']['product_id']] : 0);
				} else {
					$pie_text = $pie_product['Brand']['name'] . '(Brand)';
					$pie_amount = (isset($brand_wise[$pie_product['PieProductSetting']['brand_id']]) ? $brand_wise[$pie_product['PieProductSetting']['brand_id']] : 0);
				}
				$pie_product_data .= "<li><a>" . $pie_text . " <span class='pull-right text-red'> " . $pie_amount . "%</span></a></li>";
				$i++;
			}
		}

		// pr($chart_legend);exit;


		$data_array = array();
		$data_array['pie_data'] = $pie_data;
		$data_array['pie_product_data'] = $pie_product_data;
		$data_array['chart_legend'] = $chart_legend;
		echo json_encode($data_array);
		$this->autoRender = false;
	}
	//end for stock status ajax data

}
