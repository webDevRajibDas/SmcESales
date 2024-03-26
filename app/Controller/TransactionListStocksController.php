<?php
App::uses('AppController', 'Controller');
/**
 * ProjectionAchievementSettings Controller
 *
 * @property ProjectionAchievementReport $ProjectionAchievementReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
 
class TransactionListStocksController extends AppController
{



	/**
	 * Components
	 *
	 * @var array
	 */

	public $uses = array('Product', 'ProductCategory', 'Memo', 'Office', 'Territory', 'Division', 'District', 'Thana', 'Market', 'OutletCategory', 'Outlet', 'TerritoryAssignHistory', 'SalesPerson', 'Brand', 'RptDailyTranBalance', 'Store', 'TransactionType', 'Challan', 'ReturnChallan');
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


		$this->set('page_title', 'Transaction List On Stocks');
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


		//report type
		$unit_types = array(
			'1' => 'Sales Unit',
			'2' => 'Base Unit',
		);
		$this->set(compact('unit_types'));

		//for product type
		$product_types = $this->Product->ProductType->find('list', array('order' => array('name' => 'asc')));
		$this->set(compact('product_types'));


		//for region office list
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id' => 3),
			'order' => array('office_name' => 'asc')
		));

		//products
		$conditions = array(
			'NOT' => array('Product.product_category_id' => 32),
			'is_active' => 1,
			//'product_type_id' => 1
		);
		$conditions['is_virtual'] = 0;
		$product_list = $this->Product->find('list', array(
			'conditions' => $conditions,
			'order' =>  array('order' => 'asc')
		));
		$this->set(compact('product_list'));




		//for brands list
		$conditions = array(
			'NOT' => array('Brand.id' => 44)
		);
		$brands = $this->Brand->find('list', array(
			'conditions' => $conditions,
			'order' =>  array('name' => 'asc')
		));
		$this->set(compact('brands'));


		//for cateogry list
		$conditions = array(
			'NOT' => array('ProductCategory.id' => 32)
		);
		$categories = $this->ProductCategory->find('list', array(
			'conditions' => $conditions,
			'order' =>  array('name' => 'asc')
		));
		$this->set(compact('categories'));





		//product_measurement
		$product_measurement = $this->Product->find('list', array(
			//'conditions'=> $pro_conditions,
			'fields' => array('Product.id', 'Product.sales_measurement_unit_id'),
			'order' =>  array('order' => 'asc'),
			'recursive' => -1
		));
		$this->set(compact('product_measurement'));

		//for transaction
		$transaction_types = $this->TransactionType->find('list', array(
			//'conditions'=> $pro_conditions,
			//'fields'=> array('Product.id', 'Product.sales_measurement_unit_id'),
			//'order'=>  array('order'=>'asc'),
			'recursive' => -1
		));
		$this->set(compact('transaction_types'));

		$transaction_types_inout = $this->TransactionType->find('list', array(
			//'conditions'=> $pro_conditions,
			'fields' => array('TransactionType.id', 'TransactionType.inout'),
			//'order'=>  array('order'=>'asc'),
			'recursive' => -1
		));
		$this->set(compact('transaction_types_inout'));
		//pr($transaction_types_inout);
		//exit;

		//for stores
		$a_store_con = array(
			'Store.store_type_id' => 2,
		);
		$area_stores = $this->Store->find('list', array(
			'conditions' => $a_store_con,
			'fields' => array('Store.office_id', 'Store.id'),
			//'order'=>  array('order'=>'asc'),
			'recursive' => -1
		));
		$this->set(compact('area_stores'));
		$t_store_con = array(
			'Store.store_type_id' => 3,
		);
		$territoty_stores = $this->Store->find('list', array(
			'conditions' => $t_store_con,
			'fields' => array('Store.territory_id', 'Store.id'),
			//'order'=>  array('order'=>'asc'),
			'recursive' => -1
		));
		$this->set(compact('territoty_stores'));
		//pr($territoty_stores);


		//SalesPerson List
		$territory_id_wise_so_name = $this->SalesPerson->find('list', array(
			'conditions' => array(
				'SalesPerson.territory_id >' => 0,
			),
			'fields' => array('SalesPerson.territory_id', 'SalesPerson.name'),
			//'order'=>  array('order'=>'asc'),
			'recursive' => -1
		));
		$this->set(compact('territory_id_wise_so_name'));
		//pr($so_list);
		//exit;

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

			foreach ($territory_list as $key => $value) {
				$territories[$value['Territory']['id']] = $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')';
			}

			//get SO list
			$so_list_r = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
				'conditions' => array(
					'SalesPerson.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					'User.user_group_id' => 4,
				),
				'recursive' => 0
			));
			foreach ($so_list_r as $key => $value) {
				$so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
			}
		}



		if ($this->request->is('post') || $this->request->is('put')) {


			$all_territory_list = $this->Territory->find('list', array(
				/*'conditions' => array('User.user_group_id !=' => 1008),
				'joins' => array(
					array(
						'alias' => 'User',
						'table' => 'users',
						'type' => 'INNER',
						'conditions' => 'SalesPerson.id = User.sales_person_id'
					)
				),*/
				'fields' => array('Territory.id', 'Territory.name'),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => -1
			));
			$this->set(compact('all_territory_list'));

			$request_data = $this->request->data;

			$date_from = date('Y-m-d', strtotime($request_data['TransactionListStocks']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['TransactionListStocks']['date_to']));
			$this->set(compact('date_from', 'date_to'));

			$type = $this->request->data['TransactionListStocks']['type'];
			$this->set(compact('type'));

			$region_office_id = isset($this->request->data['TransactionListStocks']['region_office_id']) != '' ? $this->request->data['TransactionListStocks']['region_office_id'] : $region_office_id;
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

			$office_id = isset($this->request->data['TransactionListStocks']['office_id']) != '' ? $this->request->data['TransactionListStocks']['office_id'] : $office_id;
			$this->set(compact('office_id'));

			$territory_id = isset($this->request->data['TransactionListStocks']['territory_id']) != '' ? $this->request->data['TransactionListStocks']['territory_id'] : 0;
			$this->set(compact('territory_id'));

			$so_id = isset($this->request->data['TransactionListStocks']['so_id']) != '' ? $this->request->data['TransactionListStocks']['so_id'] : 0;
			$this->set(compact('so_id'));

			$unit_type = $this->request->data['TransactionListStocks']['unit_type'];
			//$this->set(compact('unit_type'));
			$unit_type_text = ($unit_type == 2) ? 'Base' : 'Sales';
			$this->set(compact('unit_type_text'));

			$product_type_id = isset($this->request->data['TransactionListStocks']['product_type_id']) != '' ? $this->request->data['TransactionListStocks']['product_type_id'] : 0;


			$product_ids = isset($this->request->data['TransactionListStocks']['product_id']) != '' ? $this->request->data['TransactionListStocks']['product_id'] : 0;
			$brand_ids = isset($this->request->data['TransactionListStocks']['brand_id']) != '' ? $this->request->data['TransactionListStocks']['brand_id'] : 0;
			$product_category_ids = isset($this->request->data['TransactionListStocks']['product_category_id']) != '' ? $this->request->data['TransactionListStocks']['product_category_id'] : 0;


			if (!in_array(3, $product_type_id)) {
				//products
				$p_conditions = array(
					'NOT' => array('Product.product_category_id' => 32),
					'Product.is_active' => 1,
				);
			} else {
				$p_conditions = array(
					'Product.is_active' => 1,
				);
			}
			if ($product_ids) $p_conditions['Product.id'] = $product_ids;
			if ($brand_ids) $p_conditions['Product.brand_id'] = $brand_ids;
			if ($product_category_ids) $p_conditions['Product.product_category_id'] = $product_category_ids;
			if ($product_type_id) $p_conditions['Product.product_type_id'] = $product_type_id;

			//pr($p_conditions);

			$f_product_list = $this->Product->find('list', array(
				'conditions' => $p_conditions,
				'order' =>  array('order' => 'asc')
			));
			//pr($f_product_list);
			$this->set(compact('f_product_list'));
			//exit;

			//territory list
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


			foreach ($territory_list as $key => $value) {
				$territories[$value['Territory']['id']] = $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')';
			}


			//get SO list
			$so_list_r = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
				'conditions' => array(
					'SalesPerson.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					'User.user_group_id' => 4,
				),
				'recursive' => 0
			));

			foreach ($so_list_r as $key => $value) {
				$so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
			}

			//add old so from territory_assign_histories
			if ($office_id) {
				$conditions = array('Territory.office_id' => $office_id, 'TerritoryAssignHistory.assign_type' => 2);
				$conditions['TerritoryAssignHistory.date BETWEEN ? and ? '] = array($date_from, $date_to);
				//pr($conditions);
				$old_so_list = $this->TerritoryAssignHistory->find('all', array(
					'conditions' => $conditions,
					'order' =>  array('Territory.name' => 'asc'),
					'recursive' => 0
				));
				if ($old_so_list) {
					foreach ($old_so_list as $old_so) {
						$so_list[$old_so['TerritoryAssignHistory']['so_id']] = $old_so['SalesPerson']['name'];
					}
				}
			}

			$outlet_category_id = isset($this->request->data['TransactionListStocks']['outlet_category_id']) != '' ? $this->request->data['TransactionListStocks']['outlet_category_id'] : 0;






			//For Query Conditon
			//Arena OPENING/CLOSING STOCK
			$con = array(
				'RptDailyTranBalance.tran_date BETWEEN ? and ? ' => array($date_from, $date_to),
				//'CurrentInventory.inventory_status_id' => 1,
				'Store.store_type_id' => 2,
			);
			if ($office_ids) $con['Store.office_id'] = $office_ids;
			if ($office_id) $con['Store.office_id'] = $office_id;
			if ($product_ids) $con['OR'] = array('Product.id' => $product_ids, 'Product.parent_id' => $product_ids);
			//pr($con);
			$aso_opening_closing_q_results = $this->Store->find('all', array(
				'conditions' => $con,
				'joins' => array(
					array(
						'alias' => 'RptDailyTranBalance',
						'table' => 'rpt_daily_tran_balance',
						'type' => 'INNER',
						'conditions' => array(
							'Store.id = RptDailyTranBalance.store_id'
						)
					),
					array(
						'alias' => 'Product',
						'table' => 'products',
						'type' => 'INNER',
						'conditions' => 'Product.id=RptDailyTranBalance.product_id'
					),
				),
				'fields' => array('sum(RptDailyTranBalance.opening_balance) AS opening_balance', 'sum(RptDailyTranBalance.closing_balance) AS closing_balance', 'CASE WHEN Product.parent_id is null OR Product.parent_id=0 THEN Product.id ELSE Product.parent_id END as product_id', 'RptDailyTranBalance.tran_date'),
				'group' => array('RptDailyTranBalance.tran_date', 'CASE WHEN Product.parent_id is null OR Product.parent_id=0 THEN Product.id ELSE Product.parent_id END'),
				'order' => array('RptDailyTranBalance.tran_date ASC'),
				'recursive' => -1,
			));

			//pr($aso_opening_closing_q_results);
			//exit;

			$aso_opening_closing_results = array();
			foreach ($aso_opening_closing_q_results as $result) {
				$opening_balance = ($unit_type == 2) ? $result[0]['opening_balance'] : $this->unit_convertfrombase($result['0']['product_id'], $product_measurement[$result['0']['product_id']], $result[0]['opening_balance']);

				$closing_balance = ($unit_type == 2) ? $result[0]['closing_balance'] : $this->unit_convertfrombase($result['0']['product_id'], $product_measurement[$result['0']['product_id']], $result[0]['closing_balance']);

				$aso_opening_closing_results[$result['RptDailyTranBalance']['tran_date']][$result['0']['product_id']] =
					array(
						'product_id' 			=> $result['0']['product_id'],
						'opening_balance' 		=> sprintf("%01.2f", $opening_balance),
						'closing_balance' 		=> sprintf("%01.2f", $closing_balance),
					);
			}

			//pr($aso_opening_closing_results);
			//exit;
			$this->set(compact('aso_opening_closing_results'));




			//CWH to ASO wise (product received)		
			$con = array(
				'Challan.received_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Challan.transaction_type_id' =>  array(4),
				'Challan.status !=' => 0,
			);
			if ($office_id) {
				$con['AND'] = array(
					'OR' => array(
						array('Challan.sender_store_id' => $area_stores[$office_id]),
						array('Challan.receiver_store_id' => $area_stores[$office_id])
					),
				);
				//$con['Challan.sender_store_id'] = $area_stores[$office_id];	
			}

			if ($product_ids) $con['ChallanDetail.product_id'] = $product_ids;
			if ($brand_ids) $con['Product.brand_id'] = $brand_ids;
			if ($product_category_ids) $con['Product.product_category_id'] = $product_category_ids;
			if ($product_type_id) $con['Product.product_type_id'] = $product_type_id;



			$challan_q_results = $this->Challan->find('all', array(
				'conditions' => $con,
				'joins' => array(
					array(
						'alias' => 'ChallanDetail',
						'table' => 'challan_details',
						'type' => 'INNER',
						'conditions' => array(
							'Challan.id = ChallanDetail.challan_id'
						)
					),
					array(
						'alias' => 'Store',
						'table' => 'stores',
						'type' => 'INNER',
						'conditions' => 'Challan.receiver_store_id = Store.id'
					),
					array(
						'alias' => 'Product',
						'table' => 'products',
						'type' => 'INNER',
						'conditions' => array(
							'ChallanDetail.product_id = Product.id'
						)
					)
				),
				'fields' => array('Challan.challan_no', 'Challan.received_date', 'Challan.transaction_type_id', 'Challan.sender_store_id', 'Challan.receiver_store_id', 'Store.office_id', 'Store.territory_id', 'ChallanDetail.product_id', 'ChallanDetail.measurement_unit_id', 'sum(ChallanDetail.challan_qty) as challan_qty', 'Product.sales_measurement_unit_id'),
				'group' => array('Challan.challan_no', 'Challan.received_date', 'Challan.transaction_type_id', 'Challan.sender_store_id', 'Challan.receiver_store_id', 'Store.office_id', 'Store.territory_id', 'ChallanDetail.product_id', 'ChallanDetail.measurement_unit_id', 'Product.sales_measurement_unit_id'),
				'recursive' => -1,
			));

			//pr($challan_q_results);

			$challan_results = array();
			foreach ($challan_q_results as $result) {
				$challan_qty = $this->unit_convert($result['ChallanDetail']['product_id'], $result['ChallanDetail']['measurement_unit_id'], $result[0]['challan_qty']);

				//echo '<br>';

				$challan_qty = ($unit_type == 1) ? $this->unit_convertfrombase($result['ChallanDetail']['product_id'], $result['Product']['sales_measurement_unit_id'], $challan_qty) : $challan_qty;

				$challan_results[$result['Challan']['received_date']][$result['Challan']['transaction_type_id']][$result['Challan']['challan_no']][$result['ChallanDetail']['product_id']] =
					array(
						'product_id' 			=> $result['ChallanDetail']['product_id'],
						'transaction_type_id' 	=> $result['Challan']['transaction_type_id'],
						'challan_no' 			=> $result['Challan']['challan_no'],
						'received_date' 			=> $result['Challan']['received_date'],
						'challan_qty' 			=> sprintf("%01.2f", $challan_qty),

						'receiver_store_id' 	=> $result['Challan']['receiver_store_id'],
						//'office_name' 			=> $offices[$result['Store']['office_id']],
						'territory_id' 			=> $result['Store']['territory_id'],
						'so_name' 				=> $result['Store']['territory_id'] ? $territory_id_wise_so_name[$result['Store']['territory_id']] : '',

					);
			}
			//$this->set(compact('challan_results'));



			//ASO wise Challan (product issue)		
			$con = array(
				'Challan.challan_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Challan.transaction_type_id !=' =>  array(0, 1, 4),
				'Challan.status !=' => 0,
			);
			if ($office_id) {
				$con['AND'] = array(
					'OR' => array(
						array('Challan.sender_store_id' => $area_stores[$office_id]),
						array('Challan.receiver_store_id' => $area_stores[$office_id])
					),
				);
				//$con['Challan.sender_store_id'] = $area_stores[$office_id];	
			}

			if ($product_ids) $con['ChallanDetail.product_id'] = $product_ids;
			if ($brand_ids) $con['Product.brand_id'] = $brand_ids;
			if ($product_category_ids) $con['Product.product_category_id'] = $product_category_ids;
			if ($product_type_id) $con['Product.product_type_id'] = $product_type_id;



			$challan_q_results = $this->Challan->find('all', array(
				'conditions' => $con,
				'joins' => array(
					array(
						'alias' => 'ChallanDetail',
						'table' => 'challan_details',
						'type' => 'INNER',
						'conditions' => array(
							'Challan.id = ChallanDetail.challan_id'
						)
					),
					array(
						'alias' => 'Store',
						'table' => 'stores',
						'type' => 'INNER',
						'conditions' => 'Challan.receiver_store_id = Store.id'
					),
					array(
						'alias' => 'Product',
						'table' => 'products',
						'type' => 'INNER',
						'conditions' => array(
							'ChallanDetail.product_id = Product.id'
						)
					)
				),
				'fields' => array('Challan.challan_no',  'Challan.challan_date', 'Challan.transaction_type_id', 'Challan.sender_store_id', 'Challan.receiver_store_id', 'Store.office_id', 'Store.territory_id', 'ChallanDetail.product_id', 'ChallanDetail.measurement_unit_id', 'sum(ChallanDetail.challan_qty) as challan_qty'),
				'group' => array('Challan.challan_no', 'Challan.challan_date', 'Challan.transaction_type_id', 'Challan.sender_store_id', 'Challan.receiver_store_id', 'Store.office_id', 'Store.territory_id', 'ChallanDetail.product_id', 'ChallanDetail.measurement_unit_id'),
				'recursive' => -1,
			));

			//pr($challan_q_results);

			//$challan_results = array();
			foreach ($challan_q_results as $result) {
				$challan_qty = ($unit_type == 1) ? $result[0]['challan_qty'] : $this->unit_convert($result['ChallanDetail']['product_id'], $result['ChallanDetail']['measurement_unit_id'], $result[0]['challan_qty']);

				$challan_results[$result['Challan']['challan_date']][$result['Challan']['transaction_type_id']][$result['Challan']['challan_no']][$result['ChallanDetail']['product_id']] =
					array(
						'product_id' 			=> $result['ChallanDetail']['product_id'],
						'transaction_type_id' 	=> $result['Challan']['transaction_type_id'],
						'challan_no' 			=> $result['Challan']['challan_no'],
						'challan_date' 			=> $result['Challan']['challan_date'],
						'challan_qty' 			=> sprintf("%01.2f", $challan_qty),

						'receiver_store_id' 	=> $result['Challan']['receiver_store_id'],
						//'office_name' 			=> $offices[$result['Store']['office_id']],
						'territory_id' 			=> $result['Store']['territory_id'],
						'so_name' 				=> $result['Store']['territory_id'] ? $territory_id_wise_so_name[$result['Store']['territory_id']] : '',

					);
			}
			//pr($challan_results);
			$this->set(compact('challan_results'));
			//exit;
			//End ASO Wise Challan





			//ASO to CWH Return Challan			
			$con = array(
				'ReturnChallan.challan_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'ReturnChallan.transaction_type_id' => array(7, 22),
				'ReturnChallan.status !=' => 0,
			);
			if ($office_id) {
				$con['AND'] = array(
					'OR' => array(
						array('ReturnChallan.sender_store_id' => $area_stores[$office_id]),
						array('ReturnChallan.receiver_store_id' => $area_stores[$office_id])
					),
				);
			}

			if ($product_ids) $con['ReturnChallanDetail.product_id'] = $product_ids;
			if ($brand_ids) $con['Product.brand_id'] = $brand_ids;
			if ($product_category_ids) $con['Product.product_category_id'] = $product_category_ids;
			if ($product_type_id) $con['Product.product_type_id'] = $product_type_id;

			$r_challan_q_results = $this->ReturnChallan->find('all', array(
				'conditions' => $con,
				'joins' => array(
					array(
						'alias' => 'ReturnChallanDetail',
						'table' => 'return_challan_details',
						'type' => 'INNER',
						'conditions' => array(
							'ReturnChallan.id = ReturnChallanDetail.challan_id'
						)
					),
					array(
						'alias' => 'Store',
						'table' => 'stores',
						'type' => 'INNER',
						'conditions' => 'ReturnChallan.receiver_store_id = Store.id'
					),
					array(
						'alias' => 'Product',
						'table' => 'products',
						'type' => 'INNER',
						'conditions' => array(
							'ReturnChallanDetail.product_id = Product.id'
						)
					)
				),
				'fields' => array('ReturnChallan.challan_no', 'ReturnChallan.challan_date', 'ReturnChallan.transaction_type_id', 'ReturnChallan.sender_store_id', 'ReturnChallan.receiver_store_id', 'Store.office_id', 'Store.territory_id', 'ReturnChallanDetail.product_id', 'ReturnChallanDetail.measurement_unit_id', 'sum(ReturnChallanDetail.challan_qty) as challan_qty'),
				'group' => array('ReturnChallan.challan_no', 'ReturnChallan.challan_date', 'ReturnChallan.transaction_type_id', 'ReturnChallan.sender_store_id', 'ReturnChallan.receiver_store_id', 'Store.office_id', 'Store.territory_id', 'ReturnChallanDetail.product_id', 'ReturnChallanDetail.measurement_unit_id'),
				'recursive' => -1,
			));

			//pr($r_challan_q_results);

			$this->set(compact('r_challan_q_results'));



			$r_challan_results = array();
			foreach ($r_challan_q_results as $result) {
				$challan_qty = ($unit_type == 1) ? $result[0]['challan_qty'] : $this->unit_convert($result['ReturnChallanDetail']['product_id'], $result['ReturnChallanDetail']['measurement_unit_id'], $result[0]['challan_qty']);

				$r_challan_results[$result['ReturnChallan']['challan_date']][$result['ReturnChallan']['transaction_type_id']][$result['ReturnChallan']['challan_no']][$result['ReturnChallanDetail']['product_id']] =
					array(
						'product_id' 			=> $result['ReturnChallanDetail']['product_id'],
						'transaction_type_id' 	=> $result['ReturnChallan']['transaction_type_id'],
						'challan_no' 			=> $result['ReturnChallan']['challan_no'],
						'challan_date' 			=> $result['ReturnChallan']['challan_date'],
						'challan_qty' 			=> sprintf("%01.2f", $challan_qty),

						'receiver_store_id' 	=> $result['ReturnChallan']['receiver_store_id'],
						//'office_name' 			=> $offices[$result['Store']['office_id']],
						'territory_id' 			=> $result['Store']['territory_id'],
						'so_name' 				=> $result['Store']['territory_id'] ? $territory_id_wise_so_name[$result['Store']['territory_id']] : '',

					);
			}
			//pr($r_challan_results);
			//$this->set(compact('r_challan_results'));
			//exit;
			//End ASO Wise Return Challan




			//SO to ASO Return Challan			
			$con = array(
				'ReturnChallan.received_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'ReturnChallan.transaction_type_id' => array(19),
				'ReturnChallan.status !=' => 0,
			);
			if ($office_id) {
				$con['AND'] = array(
					'OR' => array(
						array('ReturnChallan.sender_store_id' => $area_stores[$office_id]),
						array('ReturnChallan.receiver_store_id' => $area_stores[$office_id])
					),
				);
			}

			if ($product_ids) $con['ReturnChallanDetail.product_id'] = $product_ids;
			if ($brand_ids) $con['Product.brand_id'] = $brand_ids;
			if ($product_category_ids) $con['Product.product_category_id'] = $product_category_ids;
			if ($product_type_id) $con['Product.product_type_id'] = $product_type_id;

			$r_challan_q_results = $this->ReturnChallan->find('all', array(
				'conditions' => $con,
				'joins' => array(
					array(
						'alias' => 'ReturnChallanDetail',
						'table' => 'return_challan_details',
						'type' => 'INNER',
						'conditions' => array(
							'ReturnChallan.id = ReturnChallanDetail.challan_id'
						)
					),
					array(
						'alias' => 'Store',
						'table' => 'stores',
						'type' => 'INNER',
						'conditions' => 'ReturnChallan.receiver_store_id = Store.id'
					),
					array(
						'alias' => 'Product',
						'table' => 'products',
						'type' => 'INNER',
						'conditions' => array(
							'ReturnChallanDetail.product_id = Product.id'
						)
					)
				),
				'fields' => array('ReturnChallan.challan_no', 'ReturnChallan.received_date', 'ReturnChallan.transaction_type_id', 'ReturnChallan.sender_store_id', 'ReturnChallan.receiver_store_id', 'Store.office_id', 'Store.territory_id', 'ReturnChallanDetail.product_id', 'ReturnChallanDetail.measurement_unit_id', 'sum(ReturnChallanDetail.challan_qty) as challan_qty'),
				'group' => array('ReturnChallan.challan_no', 'ReturnChallan.received_date', 'ReturnChallan.transaction_type_id', 'ReturnChallan.sender_store_id', 'ReturnChallan.receiver_store_id', 'Store.office_id', 'Store.territory_id', 'ReturnChallanDetail.product_id', 'ReturnChallanDetail.measurement_unit_id'),
				'recursive' => -1,
			));

			//pr($r_challan_q_results);

			$this->set(compact('r_challan_q_results'));



			//$r_challan_results = array();
			foreach ($r_challan_q_results as $result) {
				$challan_qty = ($unit_type == 1) ? $result[0]['challan_qty'] : $this->unit_convert($result['ReturnChallanDetail']['product_id'], $result['ReturnChallanDetail']['measurement_unit_id'], $result[0]['challan_qty']);

				$r_challan_results[$result['ReturnChallan']['received_date']][$result['ReturnChallan']['transaction_type_id']][$result['ReturnChallan']['challan_no']][$result['ReturnChallanDetail']['product_id']] =
					array(
						'product_id' 			=> $result['ReturnChallanDetail']['product_id'],
						'transaction_type_id' 	=> $result['ReturnChallan']['transaction_type_id'],
						'challan_no' 			=> $result['ReturnChallan']['challan_no'],
						'received_date' 			=> $result['ReturnChallan']['received_date'],
						'challan_qty' 			=> sprintf("%01.2f", $challan_qty),

						'receiver_store_id' 	=> $result['ReturnChallan']['receiver_store_id'],
						//'office_name' 			=> $offices[$result['Store']['office_id']],
						'territory_id' 			=> $result['Store']['territory_id'],
						'so_name' 				=> $result['Store']['territory_id'] ? $territory_id_wise_so_name[$result['Store']['territory_id']] : '',
					);
			}
			//pr($r_challan_results);
			$this->set(compact('r_challan_results'));
			//exit;
			//End SO to ASO Wise Return Challan			

			/*------------------------------ Inventory Adjustment : Start ------------------------------------------- */
			/*Added By Naser */

			$con = array(
				'Convert(Date, InventoryAdjustment.created_at) BETWEEN ? and ? ' => array($date_from, $date_to),
				'TransactionType.adjust' => 1,
				'InventoryAdjustment.approval_status' => 1
			);

			if ($office_id) $con['Store.office_id'] = $office_id;
			//if ($product_ids) $con['Product.id'] = $product_ids;
			if ($product_ids){
				$pid = implode(',', $product_ids);
				$con[] = "(Product.id IN($pid) OR Product.parent_id IN($pid))";
			}
			if ($brand_ids) $con['Product.brand_id'] = $brand_ids;

			$inventory_adjustment_query_result = $this->Store->find('all', array(
				'conditions' => $con,
				'joins' => array(
					array(
						'alias' => 'InventoryAdjustment',
						'table' => 'inventory_adjustments',
						'type' => 'INNER',
						'conditions' => array(
							'Store.id = InventoryAdjustment.store_id'
						)
					),
					array(
						'alias' => 'InventoryAdjustmentDetail',
						'table' => 'inventory_adjustment_details',
						'type' => 'INNER',
						'conditions' => array(
							'InventoryAdjustment.id = InventoryAdjustmentDetail.inventory_adjustment_id'
						)
					),
					array(
						'alias' => 'CurrentInventory',
						'table' => 'current_inventories',
						'type' => 'INNER',
						'conditions' => array(
							'InventoryAdjustmentDetail.current_inventory_id = CurrentInventory.id'
						)
					),
					array(
						'alias' => 'Product',
						'table' => 'products',
						'type' => 'INNER',
						'conditions' => array(
							'CurrentInventory.product_id = Product.id'
						)
					),
					
					array(
						'alias' => 'TransactionType',
						'table' => 'transaction_types',
						'type' => 'INNER',
						'conditions' => array(
							'InventoryAdjustment.transaction_type_id = TransactionType.id'
						)
					),
				),
				'fields' => array('Product.parent_id', 'sum(InventoryAdjustmentDetail.quantity) AS qty', 'CurrentInventory.product_id', 'InventoryAdjustment.transaction_type_id', 'Convert(Date, InventoryAdjustment.created_at) as tran_date'),
				'group' => array('Product.parent_id', 'CurrentInventory.product_id', 'InventoryAdjustment.transaction_type_id', 'Convert(Date, InventoryAdjustment.created_at)'),
				'recursive' => -1,
			));
			// pr($inventory_adjustment_query_result);exit;
			$inventory_adjustment_results = array();
			foreach ($inventory_adjustment_query_result as $result) {
				
				if($result['Product']['parent_id'] > 0){
					$result['CurrentInventory']['product_id'] = $result['Product']['parent_id'];
				}
				
				$qty = ($unit_type == 2) ? $result[0]['qty'] : $this->unit_convertfrombase($result['CurrentInventory']['product_id'], $product_measurement[$result['CurrentInventory']['product_id']], $result[0]['qty']);
				$inventory_adjustment_results[$result['0']['tran_date']][$result['InventoryAdjustment']['transaction_type_id']][$result['CurrentInventory']['product_id']] =
					array(
						'product_id' => $result['CurrentInventory']['product_id'],
						'transaction_type_id' => $result['InventoryAdjustment']['transaction_type_id'],
						'transaction_date' => $result['0']['tran_date'],
						'adjust_qty' => $qty
					);
			}
			// pr($inventory_adjustment_results);exit;
			$this->set(compact('inventory_adjustment_results'));
			/*------------------------------ Inventory Adjustment : END --------------------------------------------- */

			/*-------------------- DB Memo part : Start --------------------*/
			/*
				Added by Naser
				Date : 20-July-2020 12:24PM
			 */
			$con = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Memo.status !=' => 0,
				'Territory.name LIKE' => '%corporate%'
			);

			if ($office_id) $con['Memo.office_id'] = $office_id;
			if ($product_ids) $con['MemoDetail.product_id'] = $product_ids;
			if ($brand_ids) $con['Product.brand_id'] = $brand_ids;
			if ($product_category_ids) $con['Product.product_category_id'] = $product_category_ids;
			if ($product_type_id) $con['Product.product_type_id'] = $product_type_id;

			$dist_issue = $this->Memo->find('all', array(
				'conditions' => $con,
				'joins' => array(
					array(
						'table' => 'memo_details',
						'alias' => 'MemoDetail',
						'conditions' => 'Memodetail.memo_id=Memo.id'
					),
					array(
						'table' => 'products',
						'alias' => 'Product',
						'conditions' => 'Memodetail.product_id=Product.id'
					),
					array(
						'alias' => 'ProductMeasurement',
						'table' => 'product_measurements',
						'type' => 'LEFT',
						'conditions' => 'Product.id = ProductMeasurement.product_id AND 
								CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
								ELSE 
									MemoDetail.measurement_unit_id
								END =ProductMeasurement.measurement_unit_id'
					),
					array(
						'table' => 'territories',
						'alias' => 'Territory',
						'conditions' => 'Memo.territory_id=Territory.id'
					),
				),
				'recursive' => -1,
				'fields' => array('Memo.memo_no', 'Memo.memo_date', 'MemoDetail.product_id', 'Product.sales_measurement_unit_id', 'sum(ROUND((MemoDetail.sales_qty* CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END ),0))  as challan_qty'),
				'group' => array('Memo.memo_no', 'Memo.memo_date', 'MemoDetail.product_id', 'Product.sales_measurement_unit_id'),
			));

			foreach ($dist_issue as $result) {
				$challan_qty = ($unit_type == 2) ? $result[0]['challan_qty'] : $this->unit_convertfrombase($result['MemoDetail']['product_id'], $result['Product']['sales_measurement_unit_id'], $result[0]['challan_qty']);

				$r_dist_issue[$result['Memo']['memo_date']][$result['Memo']['memo_no']][$result['MemoDetail']['product_id']] =
					array(
						'product_id' 			=> $result['MemoDetail']['product_id'],
						'memo_no' 			=> $result['Memo']['memo_no'],
						'chalan_date' 			=> $result['Memo']['memo_date'],
						'challan_qty' 			=> sprintf("%01.2f", $challan_qty),
					);
			}
			//pr($r_challan_results);
			$this->set(compact('r_dist_issue'));
			/*-------------------- DB Memo part : END --------------------*/

			/*--------------- DB Return To Area : Start----------------------*/
			/*
				Added By : Naser
				Added At : 20-July-2020 06:11 PM
			 */

			$this->LoadModel('DistReturnChallan');
			$con = array(
				'DistReturnChallan.received_date BETWEEN ? and ? ' => array($date_from, $date_to),

				'DistReturnChallan.status' => 2,
			);
			if ($office_id) {
				$con['DistReturnChallan.receiver_store_id'] = $area_stores[$office_id];
				$con['DistReturnChallan.office_id'] = $office_id;
			}

			if ($product_ids) $con['DistReturnChallanDetail.product_id'] = $product_ids;
			if ($brand_ids) $con['Product.brand_id'] = $brand_ids;
			if ($product_category_ids) $con['Product.product_category_id'] = $product_category_ids;
			if ($product_type_id) $con['Product.product_type_id'] = $product_type_id;
			$con['DistReturnChallanDetail.is_ncp'] = 0;
			$r_challan_q_results = $this->DistReturnChallan->find('all', array(
				'conditions' => $con,
				'joins' => array(
					array(
						'alias' => 'DistReturnChallanDetail',
						'table' => 'dist_return_challan_details',
						'type' => 'INNER',
						'conditions' => array(
							'DistReturnChallan.id = DistReturnChallanDetail.challan_id'
						)
					),
					array(
						'alias' => 'Store',
						'table' => 'stores',
						'type' => 'INNER',
						'conditions' => 'DistReturnChallan.receiver_store_id = Store.id'
					),
					array(
						'alias' => 'Product',
						'table' => 'products',
						'type' => 'INNER',
						'conditions' => array(
							'DistReturnChallanDetail.product_id = Product.id'
						)
					)
				),
				'fields' => array(
					'DistReturnChallan.challan_no',
					'DistReturnChallan.received_date',
					'DistReturnChallan.transaction_type_id',
					'DistReturnChallan.sender_store_id',
					'DistReturnChallan.receiver_store_id',
					'Store.office_id',
					'Store.territory_id',
					'DistReturnChallanDetail.product_id',
					'DistReturnChallanDetail.measurement_unit_id',
					'sum(DistReturnChallanDetail.challan_qty) as challan_qty'
				),
				'group' => array(
					'DistReturnChallan.challan_no',
					'DistReturnChallan.received_date',
					'DistReturnChallan.transaction_type_id',
					'DistReturnChallan.sender_store_id',
					'DistReturnChallan.receiver_store_id',
					'Store.office_id',
					'Store.territory_id',
					'DistReturnChallanDetail.product_id',
					'DistReturnChallanDetail.measurement_unit_id'
				),
				'recursive' => -1,
			));

			//pr($r_challan_q_results);

			$this->set(compact('r_challan_q_results'));



			//$r_challan_results = array();
			foreach ($r_challan_q_results as $result) {
				$challan_qty = ($unit_type == 1) ? $result[0]['challan_qty'] : $this->unit_convert($result['DistReturnChallanDetail']['product_id'], $result['ReturnChallanDetail']['measurement_unit_id'], $result[0]['challan_qty']);

				$r_dist_challan_results[$result['DistReturnChallan']['received_date']][$result['DistReturnChallan']['challan_no']][$result['DistReturnChallanDetail']['product_id']] =
					array(
						'product_id' 			=> $result['DistReturnChallanDetail']['product_id'],
						'transaction_type_id' 	=> $result['DistReturnChallan']['transaction_type_id'],
						'challan_no' 			=> $result['DistReturnChallan']['challan_no'],
						'received_date' 		=> $result['DistReturnChallan']['received_date'],
						'challan_qty' 			=> sprintf("%01.2f", $challan_qty),

						'receiver_store_id' 	=> $result['DistReturnChallan']['receiver_store_id'],
						//'office_name' 			=> $offices[$result['Store']['office_id']],
						'territory_id' 			=> $result['Store']['territory_id'],
						'so_name' 				=> $result['Store']['territory_id'] ? $territory_id_wise_so_name[$result['Store']['territory_id']] : '',
					);
			}
			//pr($r_challan_results);
			$this->set(compact('r_dist_challan_results'));
			/*--------------- DB To Area : END----------------------*/
		}

		$this->set(compact('offices', 'territories', 'outlet_type', 'region_offices', 'office_id', 'request_data', 'so_list'));
	}
}
