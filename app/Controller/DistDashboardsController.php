<?php
App::uses('AppController', 'Controller');
/**
 * Designations Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistDashboardsController extends AppController
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
		ini_set('max_execution_time', 300);
		date_default_timezone_set('Asia/Dhaka');
		$this->loadModel('Office');

		$sources = array('SMC' => 'SMC', 'SMCEL' => 'SMCEL');
		$this->set(compact('sources'));

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
	public function get_total_db_tso_sr()
	{
		$office_id = $this->request->data['office_id'];
		$tso = $this->request->data['tso'];
		$region_office_id = $this->request->data['region_office_id'];
		if (!$office_id) {
			$this->loadModel('Office');
			$conditions = array(
				'office_type_id' 	=> 2,
				"NOT" => array("id" => array(30, 31, 37)),
				/*'parent_office_id'=> $region_office_id*/
			);
			if ($region_office_id) {
				$conditions['parent_office_id'] = $region_office_id;
			}
			$offices = $this->Office->find('list', array(
				'conditions' => $conditions,
				'order' => array('office_name' => 'asc')
			));

			$office_id = array_keys($offices);
		}

		$this->loadModel('DistDistributor');
		$this->loadModel('DistSalesRepresentative');
		$this->loadModel('DistTso');
		$this->loadModel('SalesPerson');
		$this->loadModel('DistSalesRepresentatives');

		$dist_conditions = array(
			'DistDistributor.office_id' => $office_id,
			'DistDistributor.is_active' => 1
		);
		if ($tso) {
			$dist_conditions['DistTsoMapping.dist_tso_id'] = $tso;
		}
		$total_dist = $this->DistDistributor->find('all', array(
			'conditions' => $dist_conditions,
			'joins' => array(
				array(
					'alias' => 'DistTsoMapping',
					'table' => 'dist_tso_mappings',
					'type' => 'left',
					'conditions' => 'DistTsoMapping.dist_distributor_id = DistDistributor.id'
				),
			),
			'fields' => array('COUNT(DISTINCT(DistDistributor.id)) as total'),
			'recursive' => -1
		));

		$tso_conditions = array(
			'DistTso.office_id' => $office_id,
			'DistTso.is_active' => 1
		);
		if ($tso) {
			$tso_conditions['DistTso.id'] = $tso;
		}
		$total_tso = $this->DistTso->find('all', array(
			'conditions' => $tso_conditions,
			'fields' => array('COUNT(DISTINCT(DistTso.id)) as total'),
			'recursive' => -1
		));

		$sr_conditions = array(
			'DistSalesRepresentative.office_id' => $office_id,
			'DistSalesRepresentative.is_active' => 1,
			'User.user_group_id' => array(1032),
			'User.active' => 1
		);
		if ($tso) {
			$sr_conditions['DistTsoMapping.dist_tso_id'] = $tso;
		}
		/*$total_sr=$this->DistSalesRepresentative->find('all',array(
			'conditions'=>$sr_conditions,
			'fields'=>array('COUNT(DISTINCT(DistSalesRepresentative.id)) as total'),
			'recursive'=>-1
			));*/
		$total_sr = $this->SalesPerson->find('all', array(
			'fields' => array('COUNT(DISTINCT(DistSalesRepresentative.id)) as total'),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.id = SalesPerson.dist_sales_representative_id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistTsoMapping',
					'table' => 'dist_tso_mappings',
					'type' => 'left',
					'conditions' => 'DistTsoMapping.dist_distributor_id = DistDistributor.id'
				),
			),
			'conditions' => $sr_conditions
		));
		$date = date('Y-m-d');
		$sr_login_conditions = array(
			'DistSalesRepresentatives.office_id' => $office_id,
			'DistSalesRepresentatives.is_active' => 1,
			'User.user_group_id' => array(1032),
			'User.active' => 1
		);
		if ($tso) {
			$sr_login_conditions['DistTsoMapping.dist_tso_id'] = $tso;
		}
		$total_present_sr = $this->DistSalesRepresentatives->find('all', array(
			'conditions' => $sr_login_conditions,
			'joins' => array(
				array(
					'table' => 'dist_sr_check_in_out',
					'alias' => 'DistSrCheckInOut',
					'type' => 'inner',
					'conditions' => 'DistSrCheckInOut.sr_id=DistSalesRepresentatives.id AND DistSrCheckInOut.date=\'' . $date . '\''
				),
				array(
					'table' => 'dist_tso_mappings',
					'alias' => 'DistTsoMapping',
					'conditions' => 'DistTsoMapping.dist_distributor_id=DistSrCheckInOut.db_id'
				),
				array(
					'table' => 'sales_people',
					'alias' => 'SalesPerson',
					'conditions' => 'DistSalesRepresentatives.id=SalesPerson.dist_sales_representative_id'
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'conditions' => 'User.sales_person_id=SalesPerson.id'
				),
			),
			'fields' => array(
				'COUNT(DISTINCT DistSalesRepresentatives.id) as total_present'
			),
			'recursive' => -1
		));
		$this->set(compact('result'));

		$data_array = array(
			'dist' => $total_dist[0][0]['total'],
			'tso' => $total_tso[0][0]['total'],
			'sr' => $total_sr[0][0]['total'],
			'present_sr' => $total_present_sr[0][0]['total_present'],
		);
		echo json_encode($data_array);
		exit;
		$this->autoRender = false;
	}
	public function get_sales_activity()
	{
		$office_id = $this->request->data['office_id'];
		$region_office_id = $this->request->data['region_office_id'];
		$source = $this->request->data['source'];
		$tso = $this->request->data['tso'];
		if (!$office_id) {
			$this->loadModel('Office');
			$conditions = array(
				'office_type_id' 	=> 2,
				"NOT" => array("id" => array(30, 31, 37)),
				/*'parent_office_id'=> $region_office_id*/
			);
			if ($region_office_id) {
				$conditions['parent_office_id'] = $region_office_id;
			}
			$offices = $this->Office->find('list', array(
				'conditions' => $conditions,
				'order' => array('office_name' => 'asc')
			));

			$office_id = array_keys($offices);
		}
		$order_activity = $this->get_order_activity_data('today', 0, $office_id, $source, $tso);
		$invoice_activity = $this->get_invoice_activity_data('today', 0, $office_id, $source, $tso);
		$revenue = $this->get_revenue('today', 0, $office_id, $source, $tso);

		$json_data['today_revenue'] = $this->priceSetting($revenue['revenue']);
		$json_data['today_order'] = $order_activity['total_order'];
		$json_data['today_invoice'] = $invoice_activity['total_invoice'];
		$json_data['today_delivery'] = $revenue['total_delivery'];

		$order_activity = $this->get_order_activity_data('week', 0, $office_id, $source, $tso);
		$invoice_activity = $this->get_invoice_activity_data('week', 0, $office_id, $source, $tso);
		$revenue = $this->get_revenue('week', 0, $office_id, $source, $tso);
		$json_data['week_revenue'] = $this->priceSetting($revenue['revenue']);
		$json_data['week_order'] = $order_activity['total_order'];
		$json_data['week_invoice'] = $invoice_activity['total_invoice'];
		$json_data['week_delivery'] = $revenue['total_delivery'];

		$order_activity = $this->get_order_activity_data('month', 0, $office_id, $source, $tso);
		$invoice_activity = $this->get_invoice_activity_data('month', 0, $office_id, $source, $tso);
		$revenue = $this->get_revenue('month', 0, $office_id, $source, $tso);
		$json_data['month_revenue'] = $this->priceSetting($revenue['revenue']);
		$json_data['month_order'] = $order_activity['total_order'];
		$json_data['month_invoice'] = $invoice_activity['total_invoice'];
		$json_data['month_delivery'] = $revenue['total_delivery'];

		$order_activity = $this->get_order_activity_data('year', 0, $office_id, $source, $tso);
		$invoice_activity = $this->get_invoice_activity_data('year', 0, $office_id, $source, $tso);
		$revenue = $this->get_revenue('year', 0, $office_id, $source, $tso);
		$json_data['year_revenue'] = $this->priceSetting($revenue['revenue']);
		$json_data['year_order'] = $order_activity['total_order'];
		$json_data['year_invoice'] = $invoice_activity['total_invoice'];
		$json_data['year_delivery'] = $revenue['total_delivery'];

		echo json_encode($json_data);
		exit;
	}

	private function get_order_activity_data($box_data = '', $date = 0, $office_id = 0, $source = 0, $tso = 0)
	{
		$this->loadModel('DistOrder');

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
						'order_date >=' => $week_start_date,
						'order_date <=' => $week_end_date,
						'DistOrder.status !=' => 0,
						'DistOrder.gross_value >' => 0,
						'DistOrder.office_id' => $office_id
					);
				} elseif ($box_data == 'month') {
					$conditions = array(
						'order_date >=' => date('Y-m-01'),
						'order_date <=' => date('Y-m-d'),
						'DistOrder.status !=' => 0,
						'DistOrder.gross_value >' => 0,
						'DistOrder.office_id' => $office_id
					);
				} elseif ($box_data == 'year') {
					$conditions = array(
						'order_date >=' => $year_start_date,
						'order_date <=' => $year_end_date,
						'DistOrder.status !=' => 0,
						'DistOrder.gross_value >' => 0,
						'DistOrder.office_id' => $office_id
					);
				} else {
					//for today
					$conditions = array(
						'order_date' => date('Y-m-d'),
						'DistOrder.status !=' => 0,
						'DistOrder.gross_value >' => 0,
						'DistOrder.office_id' => $office_id
					);
				}
			}

			if ($source) {
				$conditions['Product.source'] = $source;
			}
			if ($tso) {
				$conditions['DistTsoMapping.dist_tso_id'] = $tso;
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
			$query = "
			select
				count(distinct(DistOrder.id)) as orders,
				count(distinct(case when status=2 and processing_status in (1,2) then DistOrder.id end)) as invoice,
				count(distinct(case when status=2 and processing_status=2 then DistOrder.id end)) as delivery
			from dist_orders DistOrder
			inner join dist_order_details dod on DistOrder.id=dod.dist_order_id
			inner join products Product on Product.id=dod.product_id
			LEFT join dist_distributors DistDistributor on DistDistributor.id=DistOrder.distributor_id
			LEFT join dist_tso_mappings DistTsoMapping on DistTsoMapping.dist_distributor_id = DistDistributor.id
			WHERE $condition
			";
			$result = $this->DistOrder->Query($query);
			$data = array();

			if ($result) {
				$data['total_order'] = $result[0][0]['orders'];
				$data['total_invoice'] = $result[0][0]['invoice'];
				$data['total_delivery'] = $result[0][0]['delivery'];
			} else {
				$data['total_order'] = 0;
				$data['total_invoice'] = 0;
				$data['total_delivery'] = 0;
			}

			return $data;
		}

		return false;
	}

	private function get_invoice_activity_data($box_data = '', $date = 0, $office_id = 0, $source = 0, $tso = 0)
	{
		$this->loadModel('DistOrder');

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
						'convert(date,DistOrderDeliveryScheduleOrderDetail.created_at) >=' => $week_start_date,
						'convert(date,DistOrderDeliveryScheduleOrderDetail.created_at) <=' => $week_end_date,
						'DistOrder.status' => 2,
						'DistOrder.processing_status ' => array(1, 2),
						'DistOrder.gross_value >' => 0,
						'DistOrder.office_id' => $office_id
					);
				} elseif ($box_data == 'month') {
					$conditions = array(
						'convert(date,DistOrderDeliveryScheduleOrderDetail.created_at) >=' => date('Y-m-01'),
						'convert(date,DistOrderDeliveryScheduleOrderDetail.created_at) <=' => date('Y-m-d'),
						'DistOrder.status' => 2,
						'DistOrder.processing_status ' => array(1, 2),
						'DistOrder.gross_value >' => 0,
						'DistOrder.office_id' => $office_id
					);
				} elseif ($box_data == 'year') {
					$conditions = array(
						'convert(date,DistOrderDeliveryScheduleOrderDetail.created_at) >=' => $year_start_date,
						'convert(date,DistOrderDeliveryScheduleOrderDetail.created_at) <=' => $year_end_date,
						'DistOrder.status' => 2,
						'DistOrder.processing_status ' => array(1, 2),
						'DistOrder.gross_value >' => 0,
						'DistOrder.office_id' => $office_id
					);
				} else {
					//for today
					$conditions = array(
						'convert(date,DistOrderDeliveryScheduleOrderDetail.created_at)' => date('Y-m-d'),
						'DistOrder.status' => 2,
						'DistOrder.processing_status ' => array(1, 2),
						'DistOrder.gross_value >' => 0,
						'DistOrder.office_id' => $office_id
					);
				}
			}

			if ($source) {
				$conditions['Product.source'] = $source;
			}
			if ($tso) {
				$conditions['DistTsoMapping.dist_tso_id'] = $tso;
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
			$query = "
			select
				count(distinct(DistOrder.id)) as invoice
				
			from dist_orders DistOrder
			inner join dist_order_details dod on DistOrder.id=dod.dist_order_id
			inner join dist_order_delivery_schedule_order_details DistOrderDeliveryScheduleOrderDetail on DistOrderDeliveryScheduleOrderDetail.dist_order_id=DistOrder.id and DistOrderDeliveryScheduleOrderDetail.product_id=dod.product_id
			inner join products Product on Product.id=dod.product_id
			LEFT join dist_distributors DistDistributor on DistDistributor.id=DistOrder.distributor_id
			LEFT join dist_tso_mappings DistTsoMapping on DistTsoMapping.dist_distributor_id = DistDistributor.id
			WHERE $condition
			";
			$result = $this->DistOrder->Query($query);
			$data = array();

			if ($result) {
				$data['total_invoice'] = $result[0][0]['invoice'];
			} else {
				$data['total_invoice'] = 0;
			}

			return $data;
		}

		return false;
	}

	private function get_revenue($box_data = '', $date = 0, $office_id = 0, $source = 0, $tso = 0)
	{
		$this->loadModel('DistMemo');

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
						'DistMemo.status !=' => 0,
						'DistMemo.gross_value >' => 0,
						'DistMemo.office_id' => $office_id
					);
				} elseif ($box_data == 'month') {
					$conditions = array(
						'memo_date >=' => date('Y-m-01'),
						'memo_date <=' => date('Y-m-d'),
						'DistMemo.status !=' => 0,
						'DistMemo.gross_value >' => 0,
						'DistMemo.office_id' => $office_id
					);
				} elseif ($box_data == 'year') {
					$conditions = array(
						'memo_date >=' => $year_start_date,
						'memo_date <=' => $year_end_date,
						'DistMemo.status !=' => 0,
						'DistMemo.gross_value >' => 0,
						'DistMemo.office_id' => $office_id
					);
				} else {
					//for today
					$conditions = array(
						'memo_date' => date('Y-m-d'),
						'DistMemo.status !=' => 0,
						'DistMemo.gross_value >' => 0,
						'DistMemo.office_id' => $office_id
					);
				}
			}
			if ($source) {
				$conditions['Product.source'] = $source;
			}
			if ($tso) {
				$conditions['DistTsoMapping.dist_tso_id'] = $tso;
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
			$query = "
				select 
					SUM(DistMemoDetail.sales_qty*DistMemoDetail.price) as total_Revenue,
					COUNT(DISTINCT DistMemo.id) as total_delivery
				from 
					dist_memos DistMemo
				inner join 
					dist_memo_details DistMemoDetail on DistMemo.id=DistMemoDetail.dist_memo_id
				inner join 
					products Product on Product.id=DistMemoDetail.product_id
				LEFT join dist_distributors DistDistributor on DistDistributor.id=DistMemo.distributor_id
				LEFT join dist_tso_mappings DistTsoMapping on DistTsoMapping.dist_distributor_id = DistDistributor.id
				WHERE $condition
			";
			$result = $this->DistMemo->Query($query);

			$data = array();

			if ($result) {
				$data['revenue'] = $result[0][0]['total_Revenue'] ? $result[0][0]['total_Revenue'] : 0;
				$data['total_delivery'] = $result[0][0]['total_delivery'] ? $result[0][0]['total_delivery'] : 0;
			} else {
				$data['revenue'] = 0;
				$data['total_delivery'] = 0;
			}

			return $data;
		}
		return false;
	}
	private function priceSetting($amount = 0)
	{

		// $amount = sprintf("%01.2f", $amount);

		return number_format($amount, 2, ".", ",");
	}

	public function userSyncData()
	{

		$this->loadModel('SalesPerson');
		$this->loadModel('DistOrder');

		$office_id = $this->request->data['office_id'];

		$region_office_id = $this->request->data['region_office_id'];
		$tso_id = $this->request->data['tso'];

		if (!$office_id) {
			$this->loadModel('Office');
			$conditions = array(
				'office_type_id' 	=> 2,
				"NOT" => array("id" => array(30, 31, 37)),
				/*'parent_office_id' => $region_office_id*/
			);
			if ($region_office_id) {
				$conditions['parent_office_id'] = $region_office_id;
			}
			$offices = $this->Office->find('list', array(
				'conditions' => $conditions,
				'order' => array('office_name' => 'asc')
			));

			$office_id = array_keys($offices);
		}
		$conditions = array(
			'DistDistributor.office_id' => $office_id,
			'User.user_group_id' => array(1032),
			'User.active' => 1,
			'DistSalesRepresentative.is_active' => 1
		);
		if ($tso_id) {
			$conditions['DistTsoMapping.dist_tso_id'] = $tso_id;
		}
		if (!$region_office_id) {
			$three_days_pre_date = date('Y-m-d', strtotime('-3 days'));

			$conditions['AND'] = array(
				'OR' => array(
					array('SalesPerson.last_data_push_time is null'),
					array('SalesPerson.last_data_push_time <=' => $three_days_pre_date),
				)
			);
		}

		$so_list = array();
		if ($office_id) {
			$so_list = $this->SalesPerson->find('all', array(
				'fields' => array(
					'SalesPerson.id',
					'SalesPerson.name',
					'DistSalesRepresentative.name',
					'DistSalesRepresentative.id',
					'SalesPerson.last_data_push_time',
					'DistDistributor.name',
					'Office.office_name'
				),
				'joins' => array(
					array(
						'alias' => 'DistSalesRepresentative',
						'table' => 'dist_sales_representatives',
						'type' => 'INNER',
						'conditions' => 'DistSalesRepresentative.id = SalesPerson.dist_sales_representative_id'
					),
					array(
						'alias' => 'DistDistributor',
						'table' => 'dist_distributors',
						'type' => 'INNER',
						'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
					),
					array(
						'alias' => 'DistTsoMapping',
						'table' => 'dist_tso_mappings',
						'type' => 'left',
						'conditions' => 'DistTsoMapping.dist_distributor_id = DistDistributor.id'
					),

				),
				'conditions' => $conditions,
				'order' => array('Office.office_name', 'DistDistributor.name')

			));
		}

		$output = '';
		if ($so_list) {
			foreach ($so_list as $so) {
				$total_order = $this->DistOrder->find('all', array(
					'conditions' => array(
						'DistOrder.sr_id' => $so['DistSalesRepresentative']['id'],
						'DistOrder.order_date' => date('Y-m-d'),
					),
					'fields' => array(
						'COUNT(DISTINCT DistOrder.id) as total_order',
						'sum(DistOrder.gross_value) as order_value'
					),
					'recursive' => -1
				));;
				$label = (isset($so['SalesPerson']['last_data_push_time']) && date('Y-m-d') == date('Y-m-d', strtotime($so['SalesPerson']['last_data_push_time'])) ? 'success' : 'danger');
				$output .= '<tr>';
				$output .= '<td>' . $so['Office']['office_name'] . '</td>';
				$output .= '<td>' . $so['DistDistributor']['name'] . '</td>';
				$output .= '<td><span class="label label-' . $label . '">' . $so['DistSalesRepresentative']['name'] . '</span></td>';
				$output .= '<td>' . ($so['SalesPerson']['last_data_push_time'] ? date('d-M, Y h:i a', strtotime($so['SalesPerson']['last_data_push_time'])) : "") . '</td>';
				$output .= '<td>' . (isset($total_order[0][0]['total_order']) ? $total_order[0][0]['total_order'] : 0) . '</td>';
				$output .= '<td>' . (isset($total_order[0][0]['order_value']) ? sprintf("%01.2f", $total_order[0][0]['order_value']) : 0) . '</td>';
				$output .= '</tr>';
			}
		}
		echo $output;
		exit;
	}

	function get_tso_list()
	{
		$this->LoadModel('DistTso');
		$office_id = $this->request->data['office_id'];
		$tso_condition = array('DistTso.is_active' => 1);
		if ($office_id) {
			$tso_condition['DistTso.office_id'] = $office_id;
		}

		$this->DistTso->virtualFields = array(
			"name" => "CONCAT(DistTso.name, ' (', Territory.name,')')"
		);
		$tsos = $this->DistTso->find(
			'list',
			array(
				'conditions' => $tso_condition,
				'joins' => array(
					array(
						'table' => 'territories',
						'alias' => 'Territory',
						'conditions' => 'Territory.id=DistTso.territory_id',
						'type' => 'left'
					),
				),
				'order' => array('DistTso.name' => 'ASC')
			)
		);

		$output = "<option value=''>--- Select ---</option>";
		foreach ($tsos as $id => $name) {
			$output .= "<option value='$id'>$name</option>";
		}
		echo $output;
		$this->autoRender = false;
	}
}
