<?php
App::uses('AppController', 'Controller');
/**
 * OutletCharacteristicSettings Controller
 *
 * @property OutletCharacteristicReport $OutletCharacteristicReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
/* ----------Showing transaction wise opening and closing in report--------- */
class DistTranInventoryStatementReportsController extends AppController
{
	/**
	 * Components
	 *
	 * @var array
	 */

	public $uses = array('DistStore', 'CurrentInventory', 'Product', 'ProductCategory', 'DistMemo', 'Office', 'Territory', 'Division', 'District', 'Thana', 'Market', 'OutletCategory', 'Outlet', 'TerritoryAssignHistory', 'SalesPerson', 'GiftItem', 'DoctorVisit', 'DistInventoryAdjustment', 'Claim', 'DistDistributor', 'DistOrder');
	public $components = array('Session', 'Filter.Filter');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index($id = null)
	{

		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0); //300 seconds = 5 minutes


		$this->set('page_title', "Distributor Inventory Statement Report");

		$territories = array();
		$request_data = array();
		$report_type = array();
		$dist_list = array();




		//for region office list
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id' => 3),
			'order' => array('office_name' => 'asc')
		));

		//types
		$types = array(
			'dist' => 'By Distributor'
		);
		$this->set(compact('types'));

		//report type
		$unit_types = array(
			'1' => 'Sales Unit',
			'2' => 'Base Unit',
		);
		$this->set(compact('unit_types'));

		$report_product_types = array(
			'1' => 'Only Main Product',
			'2' => 'With Virtual Product',
		);
		$this->set(compact('report_product_types'));

		//for product type
		$product_types = $this->Product->ProductType->find('list', array('order' => array('name' => 'asc')));
		$this->set(compact('product_types'));


		//for product source type
		$sql = "SELECT * FROM product_sources";
		$sources_datas = $this->Product->query($sql);
		$product_sources = array();
		foreach ($sources_datas as $sources_data) {
			$product_sources[$sources_data[0]['name']] = $sources_data[0]['name'];
		}
		$this->set(compact('product_sources'));



		$region_office_id = 0;

		$office_parent_id = $this->UserAuth->getOfficeParentId();

		$this->set(compact('office_parent_id'));

		$office_conditions = array('Office.office_type_id' => 2);

		if ($office_parent_id == 0) {
			$office_id = 0;
		} elseif ($office_parent_id == 14) {
			$region_office_id = $this->UserAuth->getOfficeId();
			$region_offices = $this->Office->find('list', array(
				'conditions' => array('Office.office_type_id' => 3, 'Office.id' => $region_office_id),
				'order' => array('office_name' => 'asc')
			));

			$office_conditions = array('Office.parent_office_id' => $region_office_id);

			$office_id = 0;

			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,

					"NOT" => array("id" => array(30, 31, 37))
				),
				'order' => array('office_name' => 'asc')
			));

			$office_ids = array_keys($offices);

			if ($office_ids) $conditions['Territory.office_id'] = $office_ids;

			//pr($conditions);
			//exit;
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
			$office_id = $this->UserAuth->getOfficeId();

			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'id' 	=> $office_id,
				),
				'order' => array('office_name' => 'asc')
			));

			$dist_list = $this->DistDistributor->find('list', array(
				'conditions' => array('DistDistributor.office_id' => $office_id),
				'order' => array('DistDistributor.name' => 'asc')
			));
		}

		//pr($offices);

		//for product list
		$pro_conditions = array(
			//'NOT' => array('Product.product_category_id'=>32),
			'Product.is_active' => 1,
			//'Product.product_type_id'=>1
		);

		$product_measurement = $this->Product->find('list', array(
			//'conditions'=> $pro_conditions,
			'fields' => array('Product.id', 'Product.sales_measurement_unit_id'),
			'order' =>  array('order' => 'asc'),
			'recursive' => -1
		));


		if ($this->request->is('post') || $this->request->is('put')) {
			$request_data = $this->request->data;

			//pr($request_data);

			$date_from = date('Y-m-d', strtotime($request_data['DistTranInventoryStatementReports']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['DistTranInventoryStatementReports']['date_to']));
			$this->set(compact('date_from', 'date_to'));



			if ($date_from <= '2023-03-04') {
				$date_from = '2023-03-04';
			}

			/*$type = $this->request->data['DistTranInventoryStatementReports']['type'];
			$this->set(compact('type'));*/

			$region_office_id = isset($this->request->data['DistTranInventoryStatementReports']['region_office_id']) != '' ? $this->request->data['DistTranInventoryStatementReports']['region_office_id'] : $region_office_id;
			$this->set(compact('region_office_id'));
			$office_ids = array();
			if ($region_office_id) {
				$offices = $this->Office->find('list', array(
					'conditions' => array(
						'office_type_id' 	=> 2,
						'parent_office_id' 	=> $region_office_id,

						"NOT" => array("id" => array(30, 31, 37))
					),
					'order' => array('office_name' => 'asc')
				));

				$office_ids = array_keys($offices);
			}

			$office_id = isset($this->request->data['DistTranInventoryStatementReports']['office_id']) != '' ? $this->request->data['DistTranInventoryStatementReports']['office_id'] : $office_id;
			$this->set(compact('office_id'));

			$dist_list = $this->DistDistributor->find('list', array(
				'conditions' => array('DistDistributor.office_id' => $office_id),
				'order' => array('DistDistributor.name' => 'asc')
			));



			$dist_distributor_id = isset($this->request->data['DistTranInventoryStatementReports']['dist_id']) != '' ? $this->request->data['DistTranInventoryStatementReports']['dist_id'] : 0;
			$this->set(compact('dist_distributor_id'));

			$unit_type = $this->request->data['DistTranInventoryStatementReports']['unit_type'];
			$unit_type_text = ($unit_type == 2) ? 'Base' : 'Sales';
			$this->set(compact('unit_type_text'));

			$report_product_type = $this->request->data['DistTranInventoryStatementReports']['report_product_type'];
			$this->set(compact('report_product_type'));

			$product_type_id = isset($this->request->data['DistTranInventoryStatementReports']['product_type_id']) != '' ? $this->request->data['DistTranInventoryStatementReports']['product_type_id'] : 0;
			$this->set(compact('product_type_id'));

			$source = isset($this->request->data['DistTranInventoryStatementReports']['source']) != '' ? $this->request->data['DistTranInventoryStatementReports']['source'] : 0;
			$this->set(compact('source'));


			//For Product List Conditon Update
			if ($source) $pro_conditions['Product.source'] = $source;
			if ($product_type_id) $pro_conditions['Product.product_type_id'] = $product_type_id;

			if ($report_product_type == 1) {
				$pro_conditions['Product.parent_id'] = 0;
			}

			/*==== FOR SO STOCK QUERY ====*/
			//OPENING BALANCE

			/*==== Current inventory add manually ====*/
			$con = array(
				'DistManualInventory.transaction_date >=' => $date_from,
				'DistManualInventory.transaction_date <=' => $date_to,
				'DistStore.store_type_id' => 4,
			);
			if ($office_ids) $con['DistStore.office_id'] = $office_ids;
			if ($office_id) $con['DistStore.office_id'] = $office_id;
			if ($dist_distributor_id) $con['DistStore.dist_distributor_id'] = $dist_distributor_id;

			//pr($con);

			$opening_received_query_results = $this->get_manual_inventory($con, $report_product_type);
			
			//echo '<pre>';print_r($opening_received_query_results);exit;
	
			$opening_manual_results = array();

			foreach ($opening_received_query_results as $received_query_result) {
				//for receive from challan

				$opening_manual_results[$received_query_result['0']['product_id']]['qty'] = sprintf("%01.2f", ($unit_type == 1) ? $received_query_result[0]['qty'] : $this->unit_convert($received_query_result['0']['product_id'], $product_measurement[$received_query_result['0']['product_id']], $received_query_result[0]['qty']));
				$opening_manual_results[$received_query_result['0']['product_id']]['b_qty']  = sprintf("%01.2f", ($unit_type == 1) ? $received_query_result[0]['b_qty'] : $this->unit_convert($received_query_result['0']['product_id'], $product_measurement[$received_query_result['0']['product_id']], $received_query_result[0]['b_qty']));
			}

			$this->set(compact('opening_manual_results'));

			/*==== CHALLAN RECEIVED====*/
			$con = array(
				'DistChallan.received_date >' => '2023-03-03',
				'DistChallan.received_date <' => $date_from,
				'DistChallan.transaction_type_id' => array(2),
				// 2 Memo to distributor challan(Receive)
				'DistStore.store_type_id' => 4,
			);
			if ($office_ids) $con['DistChallan.office_id'] = $office_ids;
			if ($office_id) $con['DistChallan.office_id'] = $office_id;
			if ($dist_distributor_id) $con['DistChallan.dist_distributor_id'] = $dist_distributor_id;

			//pr($con);

			$opening_received_query_results = $this->get_challan($con, $report_product_type);



			$opening_received_results = array();

			foreach ($opening_received_query_results as $received_query_result) {
				//for receive from challan
				if ($received_query_result['DistChallan']['transaction_type_id'] == 2) {
					$opening_received_results[$received_query_result['0']['product_id']]['qty'] = sprintf("%01.2f", ($unit_type == 1) ? $received_query_result[0]['qty'] : $this->unit_convert($received_query_result['0']['product_id'], $product_measurement[$received_query_result['0']['product_id']], $received_query_result[0]['qty']));

					$opening_received_results[$received_query_result['0']['product_id']]['b_qty'] = sprintf("%01.2f", ($unit_type == 1) ? $received_query_result[0]['b_qty'] : $this->unit_convert($received_query_result['0']['product_id'], $product_measurement[$received_query_result['0']['product_id']], $received_query_result[0]['b_qty']));
				}
			}

			$this->set(compact('opening_received_results'));

			/*==== Return CHALLAN ====*/
			$con = array(
				'DistReturnChallan.challan_date >' => '2023-03-03',
				'DistReturnChallan.challan_date <' => $date_from,
				'DistReturnChallan.transaction_type_id' => array(13, 14),
				// 2 Memo to distributor challan(Receive)
				'DistStore.store_type_id' => 4,
			);
			if ($office_ids) $con['DistReturnChallan.office_id'] = $office_ids;
			if ($office_id) $con['DistReturnChallan.office_id'] = $office_id;
			if ($dist_distributor_id) $con['DistStore.dist_distributor_id'] = $dist_distributor_id;

			//pr($con);
			$received_query_results = $this->get_return_challan($con, $report_product_type);

			$opening_return_results = array();

			foreach ($received_query_results as $received_query_result) {
				//for receive from challan
				if ($received_query_result['DistReturnChallan']['transaction_type_id'] == 13 || $received_query_result['DistReturnChallan']['transaction_type_id'] == 14) {
					$opening_return_results[$received_query_result['0']['product_id']]['qty'] = sprintf("%01.2f", ($unit_type == 1) ? $received_query_result[0]['qty'] : $this->unit_convert($received_query_result['0']['product_id'], $product_measurement[$received_query_result['0']['product_id']], $received_query_result[0]['qty']));
					$opening_return_results[$received_query_result['0']['product_id']]['b_qty'] = sprintf("%01.2f", ($unit_type == 1) ? $received_query_result[0]['b_qty'] : $this->unit_convert($received_query_result['0']['product_id'], $product_measurement[$received_query_result['0']['product_id']], $received_query_result[0]['b_qty']));
				}
			}

			$this->set(compact('opening_return_results'));


			/*==== CHALLAN RECEIVED ADJUSTMENT====*/
			$con = array(
				'convert(date,DistInventoryAdjustment.created_at) >' => '2023-03-03',
				'convert(date,DistInventoryAdjustment.created_at) <' => $date_from,
				'DistTransactionType.inout' => 2
				/* 'DistInventoryAdjustment.transaction_type_id' => array(5, 17, 15), */
				// 5 Distributor Adjustment(IN)
			);
			if ($office_ids) $con['DistInventoryAdjustment.office_id'] = $office_ids;
			if ($office_id) $con['DistInventoryAdjustment.office_id'] = $office_id;
			if ($dist_distributor_id) $con['DistInventoryAdjustment.distributor_id'] = $dist_distributor_id;

			//pr($con);

			$received_adjustment_query_results = $this->get_adjustment_increment_data($con, $report_product_type);

			$opening_receive_adjustment_results = array();

			foreach ($received_adjustment_query_results as $received_adjustment_query_result) {
				//for receive from challan
				//if($received_adjustment_query_result['DistChallan']['transaction_type_id']==2){
				$opening_receive_adjustment_results[$received_adjustment_query_result['0']['product_id']] = sprintf("%01.2f", ($unit_type == 2) ? $received_adjustment_query_result[0]['qty'] : $this->unit_convertfrombase($received_adjustment_query_result['0']['product_id'], $product_measurement[$received_adjustment_query_result['0']['product_id']], $received_adjustment_query_result[0]['qty']));
				$bonus_opening_receive_adjustment_results[$received_adjustment_query_result['0']['product_id']] = sprintf("%01.2f", ($unit_type == 2) ? $received_adjustment_query_result[0]['b_qty'] : $this->unit_convertfrombase($received_adjustment_query_result['0']['product_id'], $product_measurement[$received_adjustment_query_result['0']['product_id']], $received_adjustment_query_result[0]['b_qty']));
				//}

			}

			//pr($receive_adjustment_results);
			//exit;
			$this->set(compact('opening_receive_adjustment_results'));




			//FOR PRODUCT ISSUED ADJUSTMENT
			$con = array(
				'convert(date,DistInventoryAdjustment.created_at) >' => '2023-03-03',
				'convert(date,DistInventoryAdjustment.created_at) <' => $date_from,
				'DistTransactionType.inout' => 1
				/* 'DistInventoryAdjustment.transaction_type_id' => array(6, 18), */
				// Distributor Adjustment(OUT)
			);
			if ($office_ids) $con['DistInventoryAdjustment.office_id'] = $office_ids;
			if ($office_id) $con['DistInventoryAdjustment.office_id'] = $office_id;
			if ($dist_distributor_id) $con['DistInventoryAdjustment.distributor_id'] = $dist_distributor_id;

			//pr($con);

			$issue_adjustment_query_results = $this->get_adjustment_decrement_data($con, $report_product_type);

			$opening_issue_adjustment_results = array();

			foreach ($issue_adjustment_query_results as $issue_adjustment_query_result) {
				//for receive from challan
				//if($issue_adjustment_query_result['DistChallan']['transaction_type_id']==2){
				$opening_issue_adjustment_results[$issue_adjustment_query_result['0']['product_id']] = sprintf("%01.2f", ($unit_type == 2) ? $issue_adjustment_query_result[0]['qty'] : $this->unit_convertfrombase($issue_adjustment_query_result['0']['product_id'], $product_measurement[$issue_adjustment_query_result['0']['product_id']], $issue_adjustment_query_result[0]['qty']));
				$bonus_opening_issue_adjustment_results[$issue_adjustment_query_result['0']['product_id']] = sprintf("%01.2f", ($unit_type == 2) ? $issue_adjustment_query_result[0]['b_qty'] : $this->unit_convertfrombase($issue_adjustment_query_result['0']['product_id'], $product_measurement[$issue_adjustment_query_result['0']['product_id']], $issue_adjustment_query_result[0]['b_qty']));
				//}

			}

			//pr($issue_adjustment_results);
			//exit;
			$this->set(compact('opening_issue_adjustment_results'));


			/*==== ISSUE SALES QTY ====*/
			//sales and bonus
			$sales_con = array(
				'DistMemo.memo_date >' => '2023-03-03',
				'DistMemo.memo_date <' => $date_from,
				//'DistMemo.gross_value >' => 0,
				//'DistMemo.status !=' => 0,
				'DistMemoDetail.price >' => 0,
			);

			if ($office_ids) $sales_con['DistMemo.office_id'] = $office_ids;
			if ($office_id) $sales_con['DistMemo.office_id'] = $office_id;
			if ($dist_distributor_id) $sales_con['DistMemo.distributor_id'] = $dist_distributor_id;

			//pr($sales_con);

			$sales_query_results = $this->get_sales_data($sales_con, $report_product_type);


			$opening_sales_results = array();

			foreach ($sales_query_results as $s_query_result) {
				$opening_sales_results[$s_query_result['DistMemoDetail']['product_id']] = sprintf("%01.2f", ($unit_type == 2) ? $s_query_result[0]['sales_qty'] : $this->unit_convertfrombase($s_query_result['DistMemoDetail']['product_id'], $product_measurement[$s_query_result['DistMemoDetail']['product_id']], $s_query_result[0]['sales_qty']));
			}


			//for bounus
			$bonus_con = array(
				'DistMemo.memo_date > ' => '2023-03-03',
				'DistMemo.memo_date < ' => $date_from,
				//'DistMemo.gross_value >' => 0,
				//'DistMemo.status !=' => 0,
				'DistMemoDetail.price <' => 1,
			);

			if ($office_ids) $bonus_con['DistMemo.office_id'] = $office_ids;
			if ($office_id) $bonus_con['DistMemo.office_id'] = $office_id;
			if ($dist_distributor_id) $bonus_con['DistMemo.distributor_id'] = $dist_distributor_id;

			$bonus_query_results = $this->get_bonus_data($bonus_con, $report_product_type);
			//pr($bonus_query_results);
			//exit;

			$opening_bounus_results = array();

			foreach ($bonus_query_results as $b_query_result) {
				$opening_bounus_results[$b_query_result['DistMemoDetail']['product_id']] = sprintf("%01.2f", ($unit_type == 2) ? $b_query_result[0]['bonus_qty'] : $this->unit_convertfrombase($b_query_result['DistMemoDetail']['product_id'], $product_measurement[$b_query_result['DistMemoDetail']['product_id']], $b_query_result[0]['bonus_qty']));
			}

			$this->set(compact('opening_sales_results', 'opening_bounus_results'));

			/* if ($dist_distributor_id) { */
			/*==== CHALLAN RECEIVED====*/
			$con = array(
				'DistChallan.received_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'DistChallan.transaction_type_id' => array(2),
				// 2 Memo to distributor challan(Receive)
				'DistStore.store_type_id' => 4,
			);
			if ($office_ids) $con['DistChallan.office_id'] = $office_ids;
			if ($office_id) $con['DistChallan.office_id'] = $office_id;
			if ($dist_distributor_id) $con['DistChallan.dist_distributor_id'] = $dist_distributor_id;

			//pr($con);

			$received_query_results = $this->get_challan($con, $report_product_type);


			$received_results = array();

			foreach ($received_query_results as $received_query_result) {
				//for receive from challan
				if ($received_query_result['DistChallan']['transaction_type_id'] == 2) {
					$received_results[$received_query_result['0']['product_id']]['receive_from_challan'] = sprintf("%01.2f", ($unit_type == 1) ? $received_query_result[0]['qty'] : $this->unit_convert($received_query_result['0']['product_id'], $product_measurement[$received_query_result['0']['product_id']], $received_query_result[0]['qty']));
					$received_results[$received_query_result['0']['product_id']]['receive_from_challan_b_qty'] = sprintf("%01.2f", ($unit_type == 1) ? $received_query_result[0]['b_qty'] : $this->unit_convert($received_query_result['0']['product_id'], $product_measurement[$received_query_result['0']['product_id']], $received_query_result[0]['b_qty']));
				}
			}

			$this->set(compact('received_results'));

			/*==== REturn CHALLAN ====*/
			$con = array(
				'DistReturnChallan.challan_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'DistReturnChallan.transaction_type_id' => array(13, 14),
				// 2 Memo to distributor challan(Receive)
				'DistStore.store_type_id' => 4,
			);
			if ($office_ids) $con['DistReturnChallan.office_id'] = $office_ids;
			if ($office_id) $con['DistReturnChallan.office_id'] = $office_id;
			if ($dist_distributor_id) $con['DistStore.dist_distributor_id'] = $dist_distributor_id;

			//pr($con);
			$received_query_results = $this->get_return_challan($con, $report_product_type);

			$return_results = array();

			foreach ($received_query_results as $received_query_result) {
				//for receive from challan
				if ($received_query_result['DistReturnChallan']['transaction_type_id'] == 13 || $received_query_result['DistReturnChallan']['transaction_type_id'] == 14) {
					$return_results[$received_query_result['0']['product_id']]['return_qty'] = sprintf("%01.2f", ($unit_type == 1) ? $received_query_result[0]['qty'] : $this->unit_convert($received_query_result['0']['product_id'], $product_measurement[$received_query_result['0']['product_id']], $received_query_result[0]['qty']));
					$return_results[$received_query_result['0']['product_id']]['return_b_qty'] = sprintf("%01.2f", ($unit_type == 1) ? $received_query_result[0]['b_qty'] : $this->unit_convert($received_query_result['0']['product_id'], $product_measurement[$received_query_result['0']['product_id']], $received_query_result[0]['b_qty']));
				}
			}

			$this->set(compact('return_results'));


			/*==== CHALLAN RECEIVED ADJUSTMENT====*/
			$con = array(
				'convert(date,DistInventoryAdjustment.created_at) BETWEEN ? and ? ' => array($date_from, $date_to),
				'DistTransactionType.inout' => 2
				/* 'DistInventoryAdjustment.transaction_type_id' => array(5, 17, 15), */
				// 5 Distributor Adjustment(IN)
			);
			if ($office_ids) $con['DistInventoryAdjustment.office_id'] = $office_ids;
			if ($office_id) $con['DistInventoryAdjustment.office_id'] = $office_id;
			if ($dist_distributor_id) $con['DistInventoryAdjustment.distributor_id'] = $dist_distributor_id;

			//pr($con);

			$received_adjustment_query_results = $this->get_adjustment_increment_data($con, $report_product_type);

			$receive_adjustment_results = array();

			foreach ($received_adjustment_query_results as $received_adjustment_query_result) {
				//for receive from challan
				//if($received_adjustment_query_result['DistChallan']['transaction_type_id']==2){
				$receive_adjustment_results[$received_adjustment_query_result['0']['product_id']]['receive_adjustment'] = sprintf("%01.2f", ($unit_type == 2) ? $received_adjustment_query_result[0]['qty'] : $this->unit_convertfrombase($received_adjustment_query_result['0']['product_id'], $product_measurement[$received_adjustment_query_result['0']['product_id']], $received_adjustment_query_result[0]['qty']));
				$receive_adjustment_results[$received_adjustment_query_result['0']['product_id']]['bonus_receive_adjustment'] = sprintf("%01.2f", ($unit_type == 2) ? $received_adjustment_query_result[0]['b_qty'] : $this->unit_convertfrombase($received_adjustment_query_result['0']['product_id'], $product_measurement[$received_adjustment_query_result['0']['product_id']], $received_adjustment_query_result[0]['b_qty']));
				//}

			}

			//pr($receive_adjustment_results);
			//exit;
			$this->set(compact('receive_adjustment_results'));




			//FOR PRODUCT ISSUED ADJUSTMENT
			$con = array(
				'convert(date,DistInventoryAdjustment.created_at) BETWEEN ? and ? ' => array($date_from, $date_to),
				'DistTransactionType.inout' => 1
				/* 'DistInventoryAdjustment.transaction_type_id' => array(6, 18), */
				// Distributor Adjustment(OUT)
			);
			if ($office_ids) $con['DistInventoryAdjustment.office_id'] = $office_ids;
			if ($office_id) $con['DistInventoryAdjustment.office_id'] = $office_id;
			if ($dist_distributor_id) $con['DistInventoryAdjustment.distributor_id'] = $dist_distributor_id;

			//pr($con);

			$issue_adjustment_query_results = $this->get_adjustment_decrement_data($con, $report_product_type);

			$issue_adjustment_results = array();

			foreach ($issue_adjustment_query_results as $issue_adjustment_query_result) {
				//for receive from challan
				//if($issue_adjustment_query_result['DistChallan']['transaction_type_id']==2){
				$issue_adjustment_results[$issue_adjustment_query_result['0']['product_id']]['issue_adjustment'] = sprintf("%01.2f", ($unit_type == 2) ? $issue_adjustment_query_result[0]['qty'] : $this->unit_convertfrombase($issue_adjustment_query_result['0']['product_id'], $product_measurement[$issue_adjustment_query_result['0']['product_id']], $issue_adjustment_query_result[0]['qty']));
				$issue_adjustment_results[$issue_adjustment_query_result['0']['product_id']]['bonus_issue_adjustment'] = sprintf("%01.2f", ($unit_type == 2) ? $issue_adjustment_query_result[0]['b_qty'] : $this->unit_convertfrombase($issue_adjustment_query_result['0']['product_id'], $product_measurement[$issue_adjustment_query_result['0']['product_id']], $issue_adjustment_query_result[0]['b_qty']));
				//}

			}

			//pr($issue_adjustment_results);
			//exit;
			$this->set(compact('issue_adjustment_results'));
			/* } else {
			}
 */



			/*==== ISSUE SALES QTY ====*/
			//sales and bonus
			$sales_con = array(
				'DistMemo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				//'DistMemo.gross_value >' => 0,
				//'DistMemo.status !=' => 0,
				'DistMemoDetail.price >' => 0,
			);

			if ($office_ids) $sales_con['DistMemo.office_id'] = $office_ids;
			if ($office_id) $sales_con['DistMemo.office_id'] = $office_id;
			if ($dist_distributor_id) $sales_con['DistMemo.distributor_id'] = $dist_distributor_id;

			//pr($sales_con);

			$sales_query_results = $this->get_sales_data($sales_con, $report_product_type);




			$sales_results = array();

			foreach ($sales_query_results as $s_query_result) {
				$sales_results[$s_query_result['DistMemoDetail']['product_id']]['sales_qty'] = sprintf("%01.2f", ($unit_type == 2) ? $s_query_result[0]['sales_qty'] : $this->unit_convertfrombase($s_query_result['DistMemoDetail']['product_id'], $product_measurement[$s_query_result['DistMemoDetail']['product_id']], $s_query_result[0]['sales_qty']));
			}
			//pr($sales_results);

			$this->set(compact('sales_results'));
			//exit;

			//for bounus
			$bonus_con = array(
				'DistMemo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				//'DistMemo.gross_value >' => 0,
				//'DistMemo.status !=' => 0,
				'DistMemoDetail.price <' => 1,
			);

			if ($office_ids) $bonus_con['DistMemo.office_id'] = $office_ids;
			if ($office_id) $bonus_con['DistMemo.office_id'] = $office_id;
			if ($dist_distributor_id) $bonus_con['DistMemo.distributor_id'] = $dist_distributor_id;

			$bonus_query_results = $this->get_bonus_data($bonus_con, $report_product_type);
			//pr($bonus_query_results);
			//exit;

			$bounus_results = array();

			foreach ($bonus_query_results as $b_query_result) {
				$bounus_results[$b_query_result['DistMemoDetail']['product_id']]['bonus_qty'] = sprintf("%01.2f", ($unit_type == 2) ? $b_query_result[0]['bonus_qty'] : $this->unit_convertfrombase($b_query_result['DistMemoDetail']['product_id'], $product_measurement[$b_query_result['DistMemoDetail']['product_id']], $b_query_result[0]['bonus_qty']));
			}

			$this->set(compact('bounus_results'));
		}
		if ($dist_distributor_id)
			$pro_conditions['DistStore.dist_distributor_id'] = $dist_distributor_id;

		$product_results = $this->Product->find('all', array(
			'conditions' => $pro_conditions,
			'joins' => array(
				array(
					'alias' => 'ProductType',
					'table' => 'product_type',
					'type' => 'INNER',
					'conditions' => array(
						'Product.product_type_id = ProductType.id'
					)
				),
				array(
					'alias' => 'DistCurrentInventory',
					'table' => 'dist_current_inventories',
					'type' => 'INNER',
					'conditions' => array(
						'Product.id = DistCurrentInventory.product_id',
					)
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => array(
						'DistStore.id = DistCurrentInventory.store_id',
					)
				),
			),
			'fields' => array('Product.id', 'Product.name', 'Product.product_type_id', 'ProductType.name', 'Product.source'),
			'recursive' => -1,
			'group' => array('Product.id', 'Product.name', 'Product.product_type_id', 'ProductType.name', 'Product.source', 'Product.order'),
			'order' =>  array('Product.product_type_id' => 'asc', 'Product.source' => 'desc', 'Product.order' => 'asc',)
		));

		$product_list = array();

		foreach ($product_results as $product_result) {
			$product_list[$product_result['Product']['source']][$product_result['ProductType']['name']][$product_result['Product']['id']] = array(
				'product_id'			=> $product_result['Product']['id'],
				'product_name'			=> $product_result['Product']['name'],
				'product_type_id'		=> $product_result['Product']['product_type_id'],
				'product_type_name'		=> $product_result['ProductType']['name'],
				'Product'				=> $product_result['Product']['source'],
			);
		}

		unset($pro_conditions['Product.parent_id']);
		$pro_conditions['Product.parent_id >'] = 0;
		$product_results_paernt = $this->Product->find('all', array(
			'conditions' => $pro_conditions,
			'joins' => array(
				array(
					'alias' => 'ProductType',
					'table' => 'product_type',
					'type' => 'INNER',
					'conditions' => array(
						'Product.product_type_id = ProductType.id'
					)
				),
				array(
					'alias' => 'ParentProduct',
					'table' => 'products',
					'type' => 'INNER',
					'conditions' => array(
						'Product.parent_id = ParentProduct.id'
					)
				),
				array(
					'alias' => 'DistCurrentInventory',
					'table' => 'dist_current_inventories',
					'type' => 'INNER',
					'conditions' => array(
						'Product.id = DistCurrentInventory.product_id',
					)
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => array(
						'DistStore.id = DistCurrentInventory.store_id',
					)
				),
			),
			'fields' => array('ParentProduct.id', 'ParentProduct.name', 'Product.product_type_id', 'ProductType.name', 'Product.source'),
			'recursive' => -1,
			'group' => array('ParentProduct.id', 'ParentProduct.name', 'Product.product_type_id', 'ProductType.name', 'Product.source', 'Product.order'),
			'order' =>  array('Product.product_type_id' => 'asc', 'Product.source' => 'desc', 'Product.order' => 'asc',)
		));
		foreach ($product_results_paernt as $product_result) {
			$product_list[$product_result['Product']['source']][$product_result['ProductType']['name']][$product_result['ParentProduct']['id']] = array(
				'product_id'			=> $product_result['ParentProduct']['id'],
				'product_name'			=> $product_result['ParentProduct']['name'],
				'product_type_id'		=> $product_result['Product']['product_type_id'],
				'product_type_name'		=> $product_result['ProductType']['name'],
				'Product'				=> $product_result['Product']['source'],
			);
		}

		$sales_con = array(
			//'DistOrder.order_date >' =>  '2023-03-03',
			'DistOrder.order_date <=' =>  $date_to,
			'OR' => array(
				'DistMemo.memo_date >' =>  $date_to,
				'DistMemo.memo_date IS NULL'
			),
			'DistOrder.status' => 2,
			'DistOrder.processing_status' => array(1, 2),
			//'DistMemo.gross_value >' => 0,
			//'DistMemo.status !=' => 0,
			'DistOrderDetail.price >' => 0,
		);
		if ($office_ids) $sales_con['DistOrder.office_id'] = $office_ids;
		if ($office_id) $sales_con['DistOrder.office_id'] = $office_id;
		if ($dist_distributor_id) $sales_con['DistOrder.distributor_id'] = $dist_distributor_id;

		if ($report_product_type == 2) {
			$fields = 'CASE WHEN DistOrderDetail.virtual_product_id is null or DistOrderDetail.virtual_product_id=0 then  DistOrderDetail.product_id ELSE DistOrderDetail.virtual_product_id END as DistOrderDetail__product_id';
			$groups = 'CASE WHEN DistOrderDetail.virtual_product_id is null or DistOrderDetail.virtual_product_id=0 then  DistOrderDetail.product_id ELSE DistOrderDetail.virtual_product_id END';
			$order_product_join_conditions = 'CASE WHEN DistOrderDetail.virtual_product_id is null or DistOrderDetail.virtual_product_id=0 then  DistOrderDetail.product_id ELSE DistOrderDetail.virtual_product_id END= Product.id';
			$this->DistOrder->DistOrderDetail->virtualFields['product_id'] = "CASE WHEN DistOrderDetail.virtual_product_id is null or DistOrderDetail.virtual_product_id=0 then  DistOrderDetail.product_id ELSE DistOrderDetail.virtual_product_id END";
		} else {
			$fields = 'DistOrderDetail.product_id';
			$groups = 'DistOrderDetail.product_id';
			$order_product_join_conditions = 'DistOrderDetail.product_id = Product.id';
		}

		$sales_query_results = $this->DistOrder->find('all', array(
			'conditions' => $sales_con,
			'joins' => array(

				array(
					'alias' => 'DistOrderDetail',
					'table' => 'dist_order_details',
					'type' => 'INNER',
					'conditions' => 'DistOrder.id = DistOrderDetail.dist_order_id'
				),
				array(
					'alias' => 'DistMemo',
					'table' => 'dist_memos',
					'type' => 'left',
					'conditions' => 'DistOrder.dist_order_no = DistMemo.dist_order_no'
				),
				array(
					'alias' => 'Product',
					'table' => 'products',
					'type' => 'INNER',
					'conditions' => $order_product_join_conditions
				),
				array(
					'alias' => 'ProductMeasurement',
					'table' => 'product_measurements',
					'type' => 'LEFT',
					'conditions' => 'Product.id = ProductMeasurement.product_id AND 
							CASE WHEN (DistOrderDetail.measurement_unit_id is null or DistOrderDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
							ELSE 
								DistOrderDetail.measurement_unit_id
							END =ProductMeasurement.measurement_unit_id'
				)
			),
			'fields' => array(
				'sum(ROUND((DistOrderDetail.sales_qty * CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END ),0)) AS sales_qty',
				$fields
			),
			'group' => array($groups),
			'recursive' => -1,
		));
		$invoice_results = array();
		foreach ($sales_query_results as $s_query_result) {
			$invoice_results[$s_query_result['DistOrderDetail']['product_id']]['sales_qty'] = sprintf("%01.2f", ($unit_type == 2) ? $s_query_result[0]['sales_qty'] : $this->unit_convertfrombase($s_query_result['DistOrderDetail']['product_id'], $product_measurement[$s_query_result['DistOrderDetail']['product_id']], $s_query_result[0]['sales_qty']));
		}
		$this->set(compact('invoice_results'));

		$bonus_con = array(
			//'DistOrder.order_date >' =>  '2023-03-03',
			'DistOrder.order_date <=' => $date_to,
			'OR' => array(
				'DistMemo.memo_date >' =>  $date_to,
				'DistMemo.memo_date IS NULL'
			),
			'DistOrder.status' => 2,
			'DistOrder.processing_status' => array(1, 2),
			//'DistMemo.gross_value >' => 0,
			//'DistMemo.status !=' => 0,
			'DistOrderDetail.price <' => 1,
		);
		if ($office_ids) $bonus_con['DistOrder.office_id'] = $office_ids;
		if ($office_id) $bonus_con['DistOrder.office_id'] = $office_id;
		if ($dist_distributor_id) $bonus_con['DistOrder.distributor_id'] = $dist_distributor_id;

		$bonus_query_results = $this->DistOrder->find('all', array(
			'conditions' => $bonus_con,
			'joins' => array(
				array(
					'alias' => 'DistOrderDetail',
					'table' => 'dist_order_details',
					'type' => 'INNER',
					'conditions' => 'DistOrder.id = DistOrderDetail.dist_order_id'
				),
				array(
					'alias' => 'DistMemo',
					'table' => 'dist_memos',
					'type' => 'left',
					'conditions' => 'DistOrder.dist_order_no = DistMemo.dist_order_no'
				),
				array(
					'alias' => 'Product',
					'table' => 'products',
					'type' => 'INNER',
					'conditions' => $order_product_join_conditions
				),
				array(
					'alias' => 'ProductMeasurement',
					'table' => 'product_measurements',
					'type' => 'LEFT',
					'conditions' => 'Product.id = ProductMeasurement.product_id AND 
							CASE WHEN (DistOrderDetail.measurement_unit_id is null or DistOrderDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
							ELSE 
								DistOrderDetail.measurement_unit_id
							END =ProductMeasurement.measurement_unit_id'
				)
			),
			'fields' => array(
				'sum(ROUND((DistOrderDetail.sales_qty * CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END ),0)) AS bonus_qty',
				$fields
			),
			'group' => array($groups),
			'recursive' => -1,
		));
		$bounus_invoice_results = array();
		foreach ($bonus_query_results as $b_query_result) {
			$bounus_invoice_results[$b_query_result['DistOrderDetail']['product_id']]['bonus_qty'] = sprintf("%01.2f", ($unit_type == 2) ? $b_query_result[0]['bonus_qty'] : $this->unit_convertfrombase($b_query_result['DistOrderDetail']['product_id'], $product_measurement[$b_query_result['DistOrderDetail']['product_id']], $b_query_result[0]['bonus_qty']));
		}
		$this->set(compact('bounus_invoice_results'));

		$this->set(compact('offices', 'territories', 'outlet_type', 'region_offices', 'office_id', 'request_data', 'dist_list', 'product_list'));
	}


	private function get_manual_inventory($con, $report_product_type)
	{
		/* array(
			'(sum(DistManualInventory.qty)/(case when min(ProductMeasurement.qty_in_base) is null then 1 else min(ProductMeasurement.qty_in_base) end)) as qty',
			'(sum(DistManualInventory.bonus_qty)/(case when min(ProductMeasurement.qty_in_base) is null then 1 else min(ProductMeasurement.qty_in_base) end)) as b_qty',
			'DistManualInventory.product_id'
		) 
		
		array('DistManualInventory.product_id')
		*/
		if ($report_product_type == 2) {
			$fields = array(
				'(sum(DistManualInventory.qty)/(case when min(ProductMeasurement.qty_in_base) is null then 1 else min(ProductMeasurement.qty_in_base) end)) as qty',
				'(sum(DistManualInventory.bonus_qty)/(case when min(ProductMeasurement.qty_in_base) is null then 1 else min(ProductMeasurement.qty_in_base) end)) as b_qty,DistManualInventory.product_id  product_id'
			);
			$groups = array('DistManualInventory.product_id  ');
		} else {
			$fields = array(
				'(sum(DistManualInventory.qty)/(case when min(ProductMeasurement.qty_in_base) is null then 1 else min(ProductMeasurement.qty_in_base) end)) as qty',
				'(sum(DistManualInventory.bonus_qty)/(case when min(ProductMeasurement.qty_in_base) is null then 1 else min(ProductMeasurement.qty_in_base) end)) as b_qty, CASE WHEN Product.parent_id>0 then Product.parent_id else DistManualInventory.product_id end product_id'
			);
			$groups = array('CASE WHEN Product.parent_id>0 then Product.parent_id else DistManualInventory.product_id end ');
		}
		return $this->DistStore->find('all', array(
			'conditions' => $con,

			'joins' => array(
				array(
					'alias' => 'DistManualInventory',
					'table' => 'dist_current_inventory_balance_logs',
					'type' => 'INNER',
					'conditions' => array(
						'DistStore.id = DistManualInventory.store_id'
					)
				),

				array(
					'alias' => 'Product',
					'table' => 'products',
					'type' => 'INNER',
					'conditions' => 'DistManualInventory.product_id = Product.id'
				),

				array(
					'alias' => 'ProductMeasurement',
					'table' => 'product_measurements',
					'type' => 'LEFT',
					'conditions' => '
						Product.id = ProductMeasurement.product_id 
						AND ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id
					'
				),
			),
			'fields' => $fields,
			'group' => $groups,
			'recursive' => -1,
		));
	}
	private function get_challan($con, $report_product_type)
	{
		if ($report_product_type == 2) {
			$fields = 'CASE WHEN DistChallanDetails.virtual_product_id is null or DistChallanDetails.virtual_product_id=0 then  DistChallanDetails.product_id ELSE DistChallanDetails.virtual_product_id END as product_id';
			$groups = 'CASE WHEN DistChallanDetails.virtual_product_id is null or DistChallanDetails.virtual_product_id=0 then  DistChallanDetails.product_id ELSE DistChallanDetails.virtual_product_id END';
			$challan_product_join_conditions = 'CASE WHEN DistChallanDetails.virtual_product_id is null or DistChallanDetails.virtual_product_id=0 then  DistChallanDetails.product_id ELSE DistChallanDetails.virtual_product_id END= Product.id';
		} else {
			$fields = 'DistChallanDetails.product_id as product_id';
			$groups = 'DistChallanDetails.product_id';
			$challan_product_join_conditions = 'DistChallanDetails.product_id = Product.id';
		}
		return $this->DistStore->find('all', array(
			'conditions' => $con,

			'joins' => array(
				array(
					'alias' => 'DistChallan',
					'table' => 'dist_challans',
					'type' => 'INNER',
					'conditions' => array(
						'DistStore.id = DistChallan.receiver_dist_store_id'
					)
				),
				array(
					'alias' => 'DistChallanDetails',
					'table' => 'dist_challan_details',
					'type' => 'INNER',
					'conditions' => array(
						'DistChallan.id = DistChallanDetails.challan_id'
					)
				),
				array(
					'alias' => 'Product',
					'table' => 'products',
					'type' => 'INNER',
					'conditions' => $challan_product_join_conditions
				),
				array(
					'alias' => 'ProductMeasurementSales',
					'table' => 'product_measurements',
					'type' => 'LEFT',
					'conditions' => '
						Product.id = ProductMeasurementSales.product_id 
						AND ProductMeasurementSales.measurement_unit_id=DistChallanDetails.measurement_unit_id
					'
				),
				array(
					'alias' => 'ProductMeasurement',
					'table' => 'product_measurements',
					'type' => 'LEFT',
					'conditions' => '
						Product.id = ProductMeasurement.product_id 
						AND ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id
					'
				),
			),
			'fields' => array(
				'(sum(case when DistChallanDetails.price>0 then  DistChallanDetails.received_qty end *(case when ProductMeasurementSales.qty_in_base is null then 1 else ProductMeasurementSales.qty_in_base end))/(case when min(ProductMeasurement.qty_in_base) is null then 1 else min(ProductMeasurement.qty_in_base) end)) as qty ',
				'(sum(case when DistChallanDetails.price=0 then  DistChallanDetails.received_qty end *(case when ProductMeasurementSales.qty_in_base is null then 1 else ProductMeasurementSales.qty_in_base end))/(case when min(ProductMeasurement.qty_in_base) is null then 1 else min(ProductMeasurement.qty_in_base) end)) as b_qty ',
				$fields,
				'DistChallan.transaction_type_id'
			),
			'group' => array($groups, 'DistChallan.transaction_type_id'),
			'recursive' => -1,
		));
	}

	private function get_return_challan($con, $report_product_type)
	{
		if ($report_product_type == 2) {
			$fields = 'CASE WHEN DistReturnChallanDetails.virtual_product_id is null or DistReturnChallanDetails.virtual_product_id=0 then  DistReturnChallanDetails.product_id ELSE DistReturnChallanDetails.virtual_product_id END as product_id';
			$groups = 'CASE WHEN DistReturnChallanDetails.virtual_product_id is null or DistReturnChallanDetails.virtual_product_id=0 then  DistReturnChallanDetails.product_id ELSE DistReturnChallanDetails.virtual_product_id END';
		} else {
			$fields = 'DistReturnChallanDetails.product_id as product_id';
			$groups = 'DistReturnChallanDetails.product_id';
		}
		return $this->DistStore->find('all', array(
			'conditions' => $con,

			'joins' => array(
				array(
					'alias' => 'DistReturnChallan',
					'table' => 'dist_return_challans',
					'type' => 'INNER',
					'conditions' => array(
						'DistStore.id = DistReturnChallan.sender_store_id'
					)
				),
				array(
					'alias' => 'DistReturnChallanDetails',
					'table' => 'dist_return_challan_details',
					'type' => 'INNER',
					'conditions' => array(
						'DistReturnChallan.id = DistReturnChallanDetails.challan_id'
					)
				)
			),

			'fields' => array(
				'sum(case when DistReturnChallanDetails.unit_price >0 then DistReturnChallanDetails.challan_qty end) AS qty',
				'sum(case when DistReturnChallanDetails.unit_price =0 then DistReturnChallanDetails.challan_qty end) AS b_qty',
				$fields,
				'DistReturnChallan.transaction_type_id'
			),
			'group' => array($groups, 'DistReturnChallan.transaction_type_id'),
			'recursive' => -1,
		));
	}
	private function get_adjustment_increment_data($con, $report_product_type)
	{
		if ($report_product_type == 2) {
			$fields = array('sum(DistInventoryAdjustmentDetail.quantity) AS qty', 'sum(DistInventoryAdjustmentDetail.bonus_quantity) AS b_qty , DistCurrentInventory.product_id  product_id');
			$groups = array('DistCurrentInventory.product_id  ');
		} else {
			$fields = array('sum(DistInventoryAdjustmentDetail.quantity) AS qty', 'sum(DistInventoryAdjustmentDetail.bonus_quantity) AS b_qty ,CASE WHEN Product.parent_id>0 then Product.parent_id else DistCurrentInventory.product_id end product_id');
			$groups = array('CASE WHEN Product.parent_id>0 then Product.parent_id else DistCurrentInventory.product_id end ');
		}

		return $this->DistStore->find('all', array(
			'conditions' => $con,

			'joins' => array(
				array(
					'alias' => 'DistInventoryAdjustment',
					'table' => 'dist_inventory_adjustments',
					'type' => 'INNER',
					'conditions' => array(
						'DistStore.id = DistInventoryAdjustment.dist_store_id'
					)
				),
				array(
					'alias' => 'DistInventoryAdjustmentDetail',
					'table' => 'dist_inventory_adjustment_details',
					'type' => 'INNER',
					'conditions' => array(
						'DistInventoryAdjustment.id = DistInventoryAdjustmentDetail.dist_inventory_adjustment_id'
					)
				),
				array(
					'alias' => 'DistTransactionType',
					'table' => 'dist_transaction_types',
					'type' => 'INNER',
					'conditions' => array(
						'DistInventoryAdjustment.transaction_type_id = DistTransactionType.id'
					)
				),
				array(
					'alias' => 'DistCurrentInventory',
					'table' => 'dist_current_inventories',
					'type' => 'INNER',
					'conditions' => array(
						'DistInventoryAdjustmentDetail.dist_current_inventory_id = DistCurrentInventory.id'
					)
				),
				array(
					'alias' => 'Product',
					'table' => 'products',
					'type' => 'INNER',
					'conditions' => array(
						'Product.id = DistCurrentInventory.product_id'
					)
				),
			),

			'fields' => $fields,
			'group' => $groups,
			'recursive' => -1,
		));
	}
	private function get_adjustment_decrement_data($con, $report_product_type)
	{
		if ($report_product_type == 2) {
			$fields = array('sum(DistInventoryAdjustmentDetail.quantity) AS qty', 'sum(DistInventoryAdjustmentDetail.bonus_quantity) AS b_qty , DistCurrentInventory.product_id  product_id');
			$groups = array('DistCurrentInventory.product_id  ');
		} else {
			$fields = array('sum(DistInventoryAdjustmentDetail.quantity) AS qty', 'sum(DistInventoryAdjustmentDetail.bonus_quantity) AS b_qty ,CASE WHEN Product.parent_id>0 then Product.parent_id else DistCurrentInventory.product_id end product_id');
			$groups = array('CASE WHEN Product.parent_id>0 then Product.parent_id else DistCurrentInventory.product_id end ');
		}
		return $this->DistStore->find('all', array(
			'conditions' => $con,

			'joins' => array(
				array(
					'alias' => 'DistInventoryAdjustment',
					'table' => 'dist_inventory_adjustments',
					'type' => 'INNER',
					'conditions' => array(
						'DistStore.id = DistInventoryAdjustment.dist_store_id'
					)
				),
				array(
					'alias' => 'DistInventoryAdjustmentDetail',
					'table' => 'dist_inventory_adjustment_details',
					'type' => 'INNER',
					'conditions' => array(
						'DistInventoryAdjustment.id = DistInventoryAdjustmentDetail.dist_inventory_adjustment_id'
					)
				),
				array(
					'alias' => 'DistTransactionType',
					'table' => 'dist_transaction_types',
					'type' => 'INNER',
					'conditions' => array(
						'DistInventoryAdjustment.transaction_type_id = DistTransactionType.id'
					)
				),
				array(
					'alias' => 'DistCurrentInventory',
					'table' => 'dist_current_inventories',
					'type' => 'INNER',
					'conditions' => array(
						'DistInventoryAdjustmentDetail.dist_current_inventory_id = DistCurrentInventory.id'
					)
				),
				array(
					'alias' => 'Product',
					'table' => 'products',
					'type' => 'INNER',
					'conditions' => array(
						'Product.id = DistCurrentInventory.product_id'
					)
				),
			),

			'fields' => $fields,
			'group' => $groups,
			'recursive' => -1,
		));
	}
	private function get_sales_data($sales_con, $report_product_type)
	{
		if ($report_product_type == 2) {
			$fields = 'CASE WHEN DistMemoDetail.virtual_product_id is null or DistMemoDetail.virtual_product_id=0 then  DistMemoDetail.product_id ELSE DistMemoDetail.virtual_product_id END as DistMemoDetail__product_id';
			$groups = 'CASE WHEN DistMemoDetail.virtual_product_id is null or DistMemoDetail.virtual_product_id=0 then  DistMemoDetail.product_id ELSE DistMemoDetail.virtual_product_id END';
			$memo_product_join_conditions = 'CASE WHEN DistMemoDetail.virtual_product_id is null or DistMemoDetail.virtual_product_id=0 then  DistMemoDetail.product_id ELSE DistMemoDetail.virtual_product_id END= Product.id';
			$this->DistMemo->DistMemoDetail->virtualFields['product_id'] = "CASE WHEN DistMemoDetail.virtual_product_id is null or DistMemoDetail.virtual_product_id=0 then  DistMemoDetail.product_id ELSE DistMemoDetail.virtual_product_id END";
		} else {
			$fields = 'DistMemoDetail.product_id';
			$groups = 'DistMemoDetail.product_id';
			$memo_product_join_conditions = 'DistMemoDetail.product_id = Product.id';
		}
		return $this->DistMemo->find('all', array(
			'conditions' => $sales_con,
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
					'conditions' => $memo_product_join_conditions
				),
				array(
					'alias' => 'ProductMeasurement',
					'table' => 'product_measurements',
					'type' => 'LEFT',
					'conditions' => 'Product.id = ProductMeasurement.product_id AND 
							CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
							ELSE 
								DistMemoDetail.measurement_unit_id
							END =ProductMeasurement.measurement_unit_id'
				)
			),
			'fields' => array(
				'sum(ROUND((DistMemoDetail.sales_qty * CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END ),0)) AS sales_qty',
				$fields
			),
			'group' => array($groups),
			'recursive' => -1,
		));
	}
	private function get_bonus_data($bonus_con, $report_product_type)
	{
		if ($report_product_type == 2) {
			$fields = 'CASE WHEN DistMemoDetail.virtual_product_id is null or DistMemoDetail.virtual_product_id=0 then  DistMemoDetail.product_id ELSE DistMemoDetail.virtual_product_id END as DistMemoDetail__product_id';
			$groups = 'CASE WHEN DistMemoDetail.virtual_product_id is null or DistMemoDetail.virtual_product_id=0 then  DistMemoDetail.product_id ELSE DistMemoDetail.virtual_product_id END';
			$memo_product_join_conditions = 'CASE WHEN DistMemoDetail.virtual_product_id is null or DistMemoDetail.virtual_product_id=0 then  DistMemoDetail.product_id ELSE DistMemoDetail.virtual_product_id END= Product.id';
			$this->DistMemo->DistMemoDetail->virtualFields['product_id'] = "CASE WHEN DistMemoDetail.virtual_product_id is null or DistMemoDetail.virtual_product_id=0 then  DistMemoDetail.product_id ELSE DistMemoDetail.virtual_product_id END";
		} else {
			$fields = 'DistMemoDetail.product_id';
			$groups = 'DistMemoDetail.product_id';
			$memo_product_join_conditions = 'DistMemoDetail.product_id = Product.id';
		}
		return $this->DistMemo->find('all', array(
			'conditions' => $bonus_con,
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
					'conditions' => $memo_product_join_conditions
				),
				array(
					'alias' => 'ProductMeasurement',
					'table' => 'product_measurements',
					'type' => 'LEFT',
					'conditions' => 'Product.id = ProductMeasurement.product_id AND 
							CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
							ELSE 
								DistMemoDetail.measurement_unit_id
							END =ProductMeasurement.measurement_unit_id'
				)
			),
			'fields' => array(
				'sum(ROUND((DistMemoDetail.sales_qty * CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END ),0)) AS bonus_qty',
				$fields
			),
			'group' => array($groups),
			'recursive' => -1,
		));
	}
	public function get_office_dist_list()
	{
		$view = new View($this);

		$form = $view->loadHelper('Form');

		$office_id = $this->request->data['office_id'];

		$date_from = date('Y-m-d', strtotime($this->request->data['date_from']));
		$date_to = date('Y-m-d', strtotime($this->request->data['date_to']));

		$sr_list = array();

		if ($office_id) {
			$this->loadModel('DistDistributor');

			$conditions = array('DistDistributor.office_id' => $office_id);
			//$conditions['DistDistMemo.memo_date BETWEEN ? and ? '] = array($date_from, $date_to);

			$dist_list_from_memo = array();
			$dist_list_from_memo = $this->DistDistributor->find('list', array(
				'conditions' => $conditions,
				'recursive' => -1
			));
		}

		$form->create('DistTranInventoryStatementReports', array('role' => 'form', 'action' => 'index'));

		echo $form->input('dist_id', array('label' => false, 'id' => 'dist_id', 'class' => 'form-control dist_id', 'required' => false, 'options' => $dist_list_from_memo, 'empty' => '---- All ----'));
		$form->end();

		$this->autoRender = false;
	}
}
