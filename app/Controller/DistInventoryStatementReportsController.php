<?php
App::uses('AppController', 'Controller');
/**
 * OutletCharacteristicSettings Controller
 *
 * @property OutletCharacteristicReport $OutletCharacteristicReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistInventoryStatementReportsController extends AppController
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
		//Configure::write('debug',0);
		$this->Session->delete('detail_results');
		$this->Session->delete('outlet_lists');

		ini_set('memory_limit', '2048M');
		ini_set('max_execution_time', 1800); //300 seconds = 5 minutes

		//for insert to dist_rpt_daily_tran_balance
		/*$sql = "SELECT * FROM rpt_daily_tran_balance ORDER BY id DESC OFFSET 3000000 ROWS FETCH FIRST 500000 ROWS ONLY";
		$results = $this->Office->query($sql);
		foreach($results as $result){
			//pr($result);
			$insert = "INSERT INTO dist_rpt_daily_tran_balance (store_id,product_id,tran_date,opening_balance,received_qty,issue_to_so,sales_qty,bonus_qty,return_qty,adjustment_decrement,adjustment_increment,closing_balance,tran_note) VALUES ('".$result[0]['store_id']."','".$result[0]['product_id']."','".$result[0]['tran_date']."','".$result[0]['opening_balance']."','".$result[0]['received_qty']."','".$result[0]['issue_to_so']."','".$result[0]['sales_qty']."','".$result[0]['bonus_qty']."','".$result[0]['return_qty']."','".$result[0]['adjustment_decrement']."','".$result[0]['adjustment_increment']."','".$result[0]['closing_balance']."','".$result[0]['tran_note']."')";
			$this->Office->query($insert);
		}
		exit;
				*/

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
			'is_active' => 1,
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

			$date_from = date('Y-m-d', strtotime($request_data['DistInventoryStatementReports']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['DistInventoryStatementReports']['date_to']));
			$this->set(compact('date_from', 'date_to'));

			/*$type = $this->request->data['DistInventoryStatementReports']['type'];
			$this->set(compact('type'));*/

			$region_office_id = isset($this->request->data['DistInventoryStatementReports']['region_office_id']) != '' ? $this->request->data['DistInventoryStatementReports']['region_office_id'] : $region_office_id;
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

			$office_id = isset($this->request->data['DistInventoryStatementReports']['office_id']) != '' ? $this->request->data['DistInventoryStatementReports']['office_id'] : $office_id;
			$this->set(compact('office_id'));

			$dist_list = $this->DistDistributor->find('list', array(
				'conditions' => array('DistDistributor.office_id' => $office_id),
				'order' => array('DistDistributor.name' => 'asc')
			));

			/*$territory_id = isset($this->request->data['DistInventoryStatementReports']['territory_id']) != '' ? $this->request->data['DistInventoryStatementReports']['territory_id'] : 0;
			$this->set(compact('territory_id'));
			
			$dist_id = isset($this->request->data['DistInventoryStatementReports']['dist_id']) != '' ? $this->request->data['DistInventoryStatementReports']['dist_id'] : 0;
			$this->set(compact('dist_id'));*/

			$dist_distributor_id = isset($this->request->data['DistInventoryStatementReports']['dist_id']) != '' ? $this->request->data['DistInventoryStatementReports']['dist_id'] : 0;
			$this->set(compact('dist_distributor_id'));

			$unit_type = $this->request->data['DistInventoryStatementReports']['unit_type'];
			$this->set(compact('unit_type'));
			$unit_type_text = ($unit_type == 2) ? 'Base' : 'Sales';
			$this->set(compact('unit_type_text'));

			$product_type_id = isset($this->request->data['DistInventoryStatementReports']['product_type_id']) != '' ? $this->request->data['DistInventoryStatementReports']['product_type_id'] : 0;
			$this->set(compact('product_type_id'));

			$source = isset($this->request->data['DistInventoryStatementReports']['source']) != '' ? $this->request->data['DistInventoryStatementReports']['source'] : 0;
			$this->set(compact('source'));


			//For Product List Conditon Update
			if ($source) $pro_conditions['Product.source'] = $source;
			if ($product_type_id) $pro_conditions['Product.product_type_id'] = $product_type_id;


			/*==== FOR SO STOCK QUERY ====*/
			//OPENING BALANCE
			$dist_con = array(
				'DistRptDailyTranBalance.tran_date BETWEEN ? and ? ' => array($date_from, $date_from),
				//'CurrentInventory.inventory_status_id' => 1,
				'DistStore.store_type_id' => 4,
			);

			if ($office_ids) $dist_con['DistStore.office_id'] = $office_ids;
			if ($office_id) $dist_con['DistStore.office_id'] = $office_id;
			if ($dist_distributor_id) $dist_con['DistStore.dist_distributor_id'] = $dist_distributor_id;

			//pr($dist_con);

			$dist_stock_opening_results = $this->DistStore->find('all', array(
				'conditions' => $dist_con,
				'joins' => array(
					array(
						'alias' => 'DistRptDailyTranBalance',
						'table' => 'dist_rpt_daily_tran_balance',
						'type' => 'INNER',
						'conditions' => array(
							'DistStore.id = DistRptDailyTranBalance.store_id'
						)
					)
				),
				'fields' => array('sum(DistRptDailyTranBalance.opening_balance) AS opening_balance',  'DistRptDailyTranBalance.product_id'),
				'group' => array('DistRptDailyTranBalance.product_id'),
				'recursive' => -1,
			));

			/* echo $this->DistStore->getLastQuery();
			pr($dist_stock_opening_results);
			exit; */



			//CLOSING BALANCE
			$dist_con = array(
				'DistRptDailyTranBalance.tran_date BETWEEN ? and ? ' => array($date_to, $date_to),
				//'CurrentInventory.inventory_status_id' => 1,
				'DistStore.store_type_id' => 4,
			);
			if ($office_ids) $dist_con['DistStore.office_id'] = $office_ids;
			if ($office_id) $dist_con['DistStore.office_id'] = $office_id;
			if ($dist_distributor_id) $dist_con['DistStore.dist_distributor_id'] = $dist_distributor_id;

			$dist_stock_closing_results = $this->DistStore->find('all', array(
				'conditions' => $dist_con,
				'joins' => array(
					array(
						'alias' => 'DistRptDailyTranBalance',
						'table' => 'dist_rpt_daily_tran_balance',
						'type' => 'INNER',
						'conditions' => array(
							'DistStore.id = DistRptDailyTranBalance.store_id'
						)
					)
				),
				'fields' => array('sum(DistRptDailyTranBalance.closing_balance) AS closing_balance',  'DistRptDailyTranBalance.product_id'),
				'group' => array('DistRptDailyTranBalance.product_id'),
				'recursive' => -1,
			));


			$dist_final_opening_closing = array();

			foreach ($dist_stock_opening_results as $dist_stock) {
				$dist_final_opening_closing[$dist_stock['DistRptDailyTranBalance']['product_id']]['opening_balance'] = sprintf("%01.2f", ($unit_type == 2) ? $dist_stock[0]['opening_balance'] : $this->unit_convertfrombase($dist_stock['DistRptDailyTranBalance']['product_id'], $product_measurement[$dist_stock['DistRptDailyTranBalance']['product_id']], $dist_stock[0]['opening_balance']));
			}

			foreach ($dist_stock_closing_results as $dist_stock) {
				$dist_final_opening_closing[$dist_stock['DistRptDailyTranBalance']['product_id']]['closing_balance'] = sprintf("%01.2f", ($unit_type == 2) ? $dist_stock[0]['closing_balance'] : $this->unit_convertfrombase($dist_stock['DistRptDailyTranBalance']['product_id'], $product_measurement[$dist_stock['DistRptDailyTranBalance']['product_id']], $dist_stock[0]['closing_balance']));
			}

			//pr($dist_final_opening_closing);
			//exit;

			$this->set(compact('dist_final_opening_closing'));

			if ($dist_distributor_id) {
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

				$received_query_results = $this->DistStore->find('all', array(
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
							'conditions' => 'DistChallanDetails.product_id = Product.id'
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
						'(sum(DistChallanDetails.received_qty*(case when ProductMeasurementSales.qty_in_base is null then 1 else ProductMeasurementSales.qty_in_base end))/(case when min(ProductMeasurement.qty_in_base) is null then 1 else min(ProductMeasurement.qty_in_base) end)) as qty',
						'DistChallanDetails.product_id',
						'DistChallan.transaction_type_id'
					),
					'group' => array('DistChallanDetails.product_id', 'DistChallan.transaction_type_id'),
					'recursive' => -1,
				));


				$received_results = array();

				foreach ($received_query_results as $received_query_result) {
					//for receive from challan
					if ($received_query_result['DistChallan']['transaction_type_id'] == 2) {
						$received_results[$received_query_result['DistChallanDetails']['product_id']]['receive_from_challan'] = sprintf("%01.2f", ($unit_type == 1) ? $received_query_result[0]['qty'] : $this->unit_convert($received_query_result['DistChallanDetails']['product_id'], $product_measurement[$received_query_result['DistChallanDetails']['product_id']], $received_query_result[0]['qty']));
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
				$received_query_results = $this->DistStore->find('all', array(
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

					'fields' => array('sum(DistReturnChallanDetails.challan_qty) AS qty', 'DistReturnChallanDetails.product_id', 'DistReturnChallan.transaction_type_id'),
					'group' => array('DistReturnChallanDetails.product_id', 'DistReturnChallan.transaction_type_id'),
					'recursive' => -1,
				));

				$return_results = array();

				foreach ($received_query_results as $received_query_result) {
					//for receive from challan
					if ($received_query_result['DistReturnChallan']['transaction_type_id'] == 13 || $received_query_result['DistReturnChallan']['transaction_type_id'] == 14) {
						$return_results[$received_query_result['DistReturnChallanDetails']['product_id']]['return_qty'] = sprintf("%01.2f", ($unit_type == 1) ? $received_query_result[0]['qty'] : $this->unit_convert($received_query_result['DistReturnChallanDetails']['product_id'], $product_measurement[$received_query_result['DistReturnChallanDetails']['product_id']], $received_query_result[0]['qty']));
					}
				}

				$this->set(compact('return_results'));


				/*==== CHALLAN RECEIVED ADJUSTMENT====*/
				$con = array(
					'convert(date,DistInventoryAdjustment.created_at) BETWEEN ? and ? ' => array($date_from, $date_to),
					'DistInventoryAdjustment.transaction_type_id' => array(5, 17, 15),
					// 5 Distributor Adjustment(IN)
				);
				if ($office_ids) $con['DistInventoryAdjustment.office_id'] = $office_ids;
				if ($office_id) $con['DistInventoryAdjustment.office_id'] = $office_id;
				if ($dist_distributor_id) $con['DistInventoryAdjustment.distributor_id'] = $dist_distributor_id;

				//pr($con);

				$received_adjustment_query_results = $this->DistStore->find('all', array(
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
							'alias' => 'DistCurrentInventory',
							'table' => 'dist_current_inventories',
							'type' => 'INNER',
							'conditions' => array(
								'DistInventoryAdjustmentDetail.dist_current_inventory_id = DistCurrentInventory.id'
							)
						),
					),

					'fields' => array('sum(DistInventoryAdjustmentDetail.quantity) AS qty', 'DistCurrentInventory.product_id', 'DistInventoryAdjustment.transaction_type_id'),
					'group' => array('DistCurrentInventory.product_id', 'DistInventoryAdjustment.transaction_type_id'),
					'recursive' => -1,
				));

				$receive_adjustment_results = array();

				foreach ($received_adjustment_query_results as $received_adjustment_query_result) {
					//for receive from challan
					//if($received_adjustment_query_result['DistChallan']['transaction_type_id']==2){
					$receive_adjustment_results[$received_adjustment_query_result['DistCurrentInventory']['product_id']]['receive_adjustment'] = sprintf("%01.2f", ($unit_type == 2) ? $received_adjustment_query_result[0]['qty'] : $this->unit_convertfrombase($received_adjustment_query_result['DistCurrentInventory']['product_id'], $product_measurement[$received_adjustment_query_result['DistCurrentInventory']['product_id']], $received_adjustment_query_result[0]['qty']));
					//}

				}

				//pr($receive_adjustment_results);
				//exit;
				$this->set(compact('receive_adjustment_results'));




				//FOR PRODUCT ISSUED ADJUSTMENT
				$con = array(
					'convert(date,DistInventoryAdjustment.created_at) BETWEEN ? and ? ' => array($date_from, $date_to),
					'DistInventoryAdjustment.transaction_type_id' => array(6, 18),
					// Distributor Adjustment(OUT)
				);
				if ($office_ids) $con['DistInventoryAdjustment.office_id'] = $office_ids;
				if ($office_id) $con['DistInventoryAdjustment.office_id'] = $office_id;
				if ($dist_distributor_id) $con['DistInventoryAdjustment.distributor_id'] = $dist_distributor_id;

				//pr($con);

				$issue_adjustment_query_results = $this->DistStore->find('all', array(
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
							'alias' => 'DistCurrentInventory',
							'table' => 'dist_current_inventories',
							'type' => 'INNER',
							'conditions' => array(
								'DistInventoryAdjustmentDetail.dist_current_inventory_id = DistCurrentInventory.id'
							)
						),
					),

					'fields' => array('sum(DistInventoryAdjustmentDetail.quantity) AS qty', 'DistCurrentInventory.product_id', 'DistInventoryAdjustment.transaction_type_id'),
					'group' => array('DistCurrentInventory.product_id', 'DistInventoryAdjustment.transaction_type_id'),
					'recursive' => -1,
				));

				$issue_adjustment_results = array();

				foreach ($issue_adjustment_query_results as $issue_adjustment_query_result) {
					//for receive from challan
					//if($issue_adjustment_query_result['DistChallan']['transaction_type_id']==2){
					$issue_adjustment_results[$issue_adjustment_query_result['DistCurrentInventory']['product_id']]['issue_adjustment'] = sprintf("%01.2f", ($unit_type == 2) ? $issue_adjustment_query_result[0]['qty'] : $this->unit_convertfrombase($issue_adjustment_query_result['DistCurrentInventory']['product_id'], $product_measurement[$issue_adjustment_query_result['DistCurrentInventory']['product_id']], $issue_adjustment_query_result[0]['qty']));
					//}

				}

				//pr($issue_adjustment_results);
				//exit;
				$this->set(compact('issue_adjustment_results'));
			} else {
			}


			/*----------------- invoice(before delivery) qty sum : prev, for opening .  ------------------------*/
			$prev_sales_con = array(
				'DistOrder.order_date <' =>  $date_from,
				'OR' => array(
					'DistMemo.memo_date >=' =>  $date_from,
					'DistMemo.memo_date IS NULL'
				),
				'DistOrder.status' => 2,
				'DistOrder.processing_status' => array(1, 2)
			);
			if ($office_ids) $prev_sales_con['DistOrder.office_id'] = $office_ids;
			if ($office_id) $prev_sales_con['DistOrder.office_id'] = $office_id;
			if ($dist_distributor_id) $prev_sales_con['DistOrder.distributor_id'] = $dist_distributor_id;

			$prev_sales_query_results = $this->DistOrder->find('all', array(
				'conditions' => $prev_sales_con,
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
						'conditions' => 'Product.id = DistOrderDetail.product_id'
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
				'fields' => array('sum(ROUND((DistOrderDetail.sales_qty * CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END ),0)) AS sales_qty', 'DistOrderDetail.product_id'),
				'group' => array('DistOrderDetail.product_id'),
				'recursive' => -1,
			));
			$prev_invoice_results = array();
			foreach ($prev_sales_query_results as $s_query_result) {
				$prev_invoice_results[$s_query_result['DistOrderDetail']['product_id']]['sales_qty'] = sprintf("%01.2f", ($unit_type == 2) ? $s_query_result[0]['sales_qty'] : $this->unit_convertfrombase($s_query_result['DistOrderDetail']['product_id'], $product_measurement[$s_query_result['DistOrderDetail']['product_id']], $s_query_result[0]['sales_qty']));
			}
			$this->set(compact('prev_invoice_results'));


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

			$sales_query_results = $this->DistMemo->find('all', array(
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
						'conditions' => 'Product.id = DistMemoDetail.product_id'
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
				'fields' => array('sum(ROUND((DistMemoDetail.sales_qty * CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END ),0)) AS sales_qty', 'DistMemoDetail.product_id'),
				'group' => array('DistMemoDetail.product_id'),
				'recursive' => -1,
			));




			$sales_results = array();

			foreach ($sales_query_results as $s_query_result) {
				$sales_results[$s_query_result['DistMemoDetail']['product_id']]['sales_qty'] = sprintf("%01.2f", ($unit_type == 2) ? $s_query_result[0]['sales_qty'] : $this->unit_convertfrombase($s_query_result['DistMemoDetail']['product_id'], $product_measurement[$s_query_result['DistMemoDetail']['product_id']], $s_query_result[0]['sales_qty']));
			}
			//pr($sales_results);
			/*----------------- invoice(before delivery) qty sum ------------------------*/
			$sales_con = array(
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
						'conditions' => 'Product.id = DistOrderDetail.product_id'
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
				'fields' => array('sum(ROUND((DistOrderDetail.sales_qty * CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END ),0)) AS sales_qty', 'DistOrderDetail.product_id'),
				'group' => array('DistOrderDetail.product_id'),
				'recursive' => -1,
			));
			$invoice_results = array();
			foreach ($sales_query_results as $s_query_result) {
				$invoice_results[$s_query_result['DistOrderDetail']['product_id']]['sales_qty'] = sprintf("%01.2f", ($unit_type == 2) ? $s_query_result[0]['sales_qty'] : $this->unit_convertfrombase($s_query_result['DistOrderDetail']['product_id'], $product_measurement[$s_query_result['DistOrderDetail']['product_id']], $s_query_result[0]['sales_qty']));
			}
			$this->set(compact('sales_results', 'invoice_results'));
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

			$bonus_query_results = $this->DistMemo->find('all', array(
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
						'conditions' => 'Product.id = DistMemoDetail.product_id'
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
				'fields' => array('sum(ROUND((DistMemoDetail.sales_qty * CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END ),0)) AS bonus_qty', 'DistMemoDetail.product_id'),
				'group' => array('DistMemoDetail.product_id'),
				'recursive' => -1,
			));
			//pr($bonus_query_results);
			//exit;

			$bounus_results = array();

			foreach ($bonus_query_results as $b_query_result) {
				$bounus_results[$b_query_result['DistMemoDetail']['product_id']]['bonus_qty'] = sprintf("%01.2f", ($unit_type == 2) ? $b_query_result[0]['bonus_qty'] : $this->unit_convertfrombase($b_query_result['DistMemoDetail']['product_id'], $product_measurement[$b_query_result['DistMemoDetail']['product_id']], $b_query_result[0]['bonus_qty']));
			}

			/*----------------- invoice(before delivery) qty sum ------------------------*/
			$bonus_con = array(
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
						'conditions' => 'Product.id = DistOrderDetail.product_id'
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
				'fields' => array('sum(ROUND((DistOrderDetail.sales_qty * CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END ),0)) AS bonus_qty', 'DistOrderDetail.product_id'),
				'group' => array('DistOrderDetail.product_id'),
				'recursive' => -1,
			));
			$bounus_invoice_results = array();
			foreach ($bonus_query_results as $b_query_result) {
				$bounus_invoice_results[$b_query_result['DistOrderDetail']['product_id']]['bonus_qty'] = sprintf("%01.2f", ($unit_type == 2) ? $b_query_result[0]['bonus_qty'] : $this->unit_convertfrombase($b_query_result['DistOrderDetail']['product_id'], $product_measurement[$b_query_result['DistOrderDetail']['product_id']], $b_query_result[0]['bonus_qty']));
			}
			$this->set(compact('bounus_results', 'bounus_invoice_results'));
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

		//pr($product_list);
		//exit;

		$this->set(compact('offices', 'territories', 'outlet_type', 'region_offices', 'office_id', 'request_data', 'dist_list', 'product_list'));
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

		$form->create('DistInventoryStatementReports', array('role' => 'form', 'action' => 'index'));

		echo $form->input('dist_id', array('label' => false, 'id' => 'dist_id', 'class' => 'form-control dist_id', 'required' => false, 'options' => $dist_list_from_memo, 'empty' => '---- All ----'));
		$form->end();

		$this->autoRender = false;
	}
}
