<?php
App::uses('AppController', 'Controller');
/**
 * ProjectionAchievementSettings Controller
 *
 * @property ProjectionAchievementReport $ProjectionAchievementReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class AreaBatchLotByStocksController extends AppController
{



	/**
	 * Components
	 *
	 * @var array
	 */



	public $uses = array('Product', 'ProductCategory', 'Memo', 'Office', 'Territory', 'Division', 'District', 'Thana', 'Market', 'OutletCategory', 'Outlet', 'TerritoryAssignHistory', 'SalesPerson', 'Brand', 'RptDailyTranBalance', 'Store', 'TransactionType', 'Challan', 'ReturnChallan', 'CurrentInventory');
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


		$this->set('page_title', 'Stock By Batch/Lot');

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



		$region_office_id = 0;

		$office_parent_id = $this->UserAuth->getOfficeParentId();

		$this->set(compact('office_parent_id'));

		$office_conditions = array('Office.office_type_id' => 2);
		$is_office_user = 0;
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
			$is_office_user = 1;
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

		$next_month_expire = 0;

		if ($this->request->is('post')) {
			$next_month_expire = 0;
		} else {
			$next_month_expire = 1;
		}

		$this->set(compact('next_month_expire'));


		if ($this->request->is('post') || $this->request->is('put') || $next_month_expire = 1) {
			$request_data = $this->request->data;

			if ($next_month_expire == 1) {
				$date_from = date('Y-m-1');
				$date_to = date('Y-m-t');
			}


			// pr($request_data);exit;

			//$date_from = date('Y-m-d', strtotime($request_data['AreaBatchLotByStocks']['date_from']));
			//$date_to = date('Y-m-d', strtotime($request_data['AreaBatchLotByStocks']['date_to']));
			$this->set(compact('date_from', 'date_to'));

			$type = $this->request->data['AreaBatchLotByStocks']['type'];
			$this->set(compact('type'));

			$region_office_id = isset($this->request->data['AreaBatchLotByStocks']['region_office_id']) != '' ? $this->request->data['AreaBatchLotByStocks']['region_office_id'] : $region_office_id;
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
			} else {
				$offc_conditions = array(
					'office_type_id' 	=> 2,

					"NOT" => array("id" => array(30, 31, 37))
				);
				if ($is_office_user) {
					$offc_conditions['id'] = $office_id;
				}
				$offices = $this->Office->find('list', array(
					'conditions' => $offc_conditions,
					'order' => array('office_name' => 'asc')
				));

				$office_ids = array_keys($offices);
			}

			$office_id = isset($this->request->data['AreaBatchLotByStocks']['office_id']) != '' ? $this->request->data['AreaBatchLotByStocks']['office_id'] : $office_id;
			$this->set(compact('office_id'));

			$territory_id = isset($this->request->data['AreaBatchLotByStocks']['territory_id']) != '' ? $this->request->data['AreaBatchLotByStocks']['territory_id'] : 0;
			$this->set(compact('territory_id'));

			$so_id = isset($this->request->data['AreaBatchLotByStocks']['so_id']) != '' ? $this->request->data['AreaBatchLotByStocks']['so_id'] : 0;
			$this->set(compact('so_id'));

			$unit_type = $this->request->data['AreaBatchLotByStocks']['unit_type'];
			//$this->set(compact('unit_type'));
			$unit_type_text = ($unit_type == 2) ? 'Base' : 'Sales';
			$this->set(compact('unit_type_text'));
			$product_type_id = isset($this->request->data['AreaBatchLotByStocks']['product_type_id']) != '' ? $this->request->data['AreaBatchLotByStocks']['product_type_id'] : 0;


			$product_ids = isset($this->request->data['AreaBatchLotByStocks']['product_id']) != '' ? $this->request->data['AreaBatchLotByStocks']['product_id'] : 0;
			$brand_ids = isset($this->request->data['AreaBatchLotByStocks']['brand_id']) != '' ? $this->request->data['AreaBatchLotByStocks']['brand_id'] : 0;
			$product_category_ids = isset($this->request->data['AreaBatchLotByStocks']['product_category_id']) != '' ? $this->request->data['AreaBatchLotByStocks']['product_category_id'] : 0;


			//products
			$p_conditions = array(
				'NOT' => array('Product.product_category_id' => 32),
				'is_active' => 1,
				'product_type_id' => $product_type_id
			);

			if ($product_ids) $p_conditions['Product.id'] = $product_ids;
			if ($brand_ids) $p_conditions['Product.brand_id'] = $brand_ids;
			if ($product_category_ids) $p_conditions['Product.product_category_id'] = $product_category_ids;
			$f_product_list = $this->Product->find('list', array(
				'conditions' => $p_conditions,
				'order' =>  array('order' => 'asc')
			));
			$this->set(compact('f_product_list'));


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
				@$conditions['TerritoryAssignHistory.date BETWEEN ? and ? '] = array($date_from, $date_to);
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

			$outlet_category_id = isset($this->request->data['AreaBatchLotByStocks']['outlet_category_id']) != '' ? $this->request->data['AreaBatchLotByStocks']['outlet_category_id'] : 0;


			//For Query Conditon
			//Arena OPENING/CLOSING STOCK
			$con = array(
				//'RptDailyTranBalance.tran_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'CurrentInventory.qty >' => 0,
				'CurrentInventory.inventory_status_id !=' => 2,
				'Store.store_type_id' => 2,
			);
			if ($office_ids) $con['Store.office_id'] = $office_ids;
			if ($office_id) $con['Store.office_id'] = $office_id;

			if ($product_ids) $con['CurrentInventory.product_id'] = $product_ids;
			if ($brand_ids) $con['Product.brand_id'] = $brand_ids;
			if ($product_category_ids) $con['Product.product_category_id'] = $product_category_ids;
			if ($product_type_id) $con['Product.product_type_id'] = $product_type_id;

			/*---- operator part current inventory: start ------------- */
			if ($request_data['AreaBatchLotByStocks']['operator'] && $request_data['AreaBatchLotByStocks']['operator'] == 1) {
				$less_date = explode('-', $request_data['AreaBatchLotByStocks']['expire_date']);

				$less_date = date('Y-m-01', mktime(0, 0, 0, date('m', strtotime($less_date[0])), 1, $less_date[1]));
				$con["CurrentInventory.expire_date <"] = $less_date;
			} elseif ($request_data['AreaBatchLotByStocks']['operator'] && $request_data['AreaBatchLotByStocks']['operator'] == 2) {
				$gretter_date = explode('-', $request_data['AreaBatchLotByStocks']['expire_date']);
				$gretter_date = date('Y-m-t', mktime(0, 0, 0, date('m', strtotime($gretter_date[0])), 1, $gretter_date[1]));

				$con["CurrentInventory.expire_date >"] = $gretter_date;
			} elseif ($request_data['AreaBatchLotByStocks']['operator'] && $request_data['AreaBatchLotByStocks']['operator'] == 3) {
				$date_from = explode('-', $request_data['AreaBatchLotByStocks']['expire_date_from']);
				$date_from = date('Y-m-01', mktime(0, 0, 0, date('m', strtotime($date_from[0])), 1, $date_from[1]));

				$date_to = explode('-', $request_data['AreaBatchLotByStocks']['expire_date_to']);
				$date_to = date('Y-m-t', mktime(0, 0, 0, date('m', strtotime($date_to[0])), 1, $date_to[1]));

				$con["CurrentInventory.expire_date BETWEEN ? and ?"] = array($date_from, $date_to);
			}

			if ($next_month_expire == 1) {
				$con["CurrentInventory.expire_date BETWEEN ? and ?"] = array($date_from, $date_to);
			}
			/*---- operator part current inventory: END ------------- */
			// pr($con);
			$q_results = $this->Store->find('all', array(
				'conditions' => $con,
				'joins' => array(
					array(
						'alias' => 'CurrentInventory',
						'table' => 'current_inventories',
						'type' => 'INNER',
						'conditions' => array(
							'Store.id = CurrentInventory.store_id'
						)
					),
					array(
						'alias' => 'Product',
						'table' => 'products',
						'type' => 'INNER',
						'conditions' => array(
							'CurrentInventory.product_id = Product.id'
						)
					)
				),
				'fields' => array('sum(CurrentInventory.qty) AS qty', 'Store.id', 'Store.name', 'Store.office_id', 'CurrentInventory.product_id', 'CurrentInventory.batch_number', 'CurrentInventory.expire_date',  'CurrentInventory.inventory_status_id', 'Store.office_id', 'Product.name', 'Product.source', 'Product.sales_measurement_unit_id'),
				'group' => array('CurrentInventory.product_id', 'Store.id', 'Store.name', 'Store.office_id', 'CurrentInventory.batch_number', 'CurrentInventory.expire_date', 'CurrentInventory.inventory_status_id', 'Store.office_id',  'Product.name', 'Product.source', 'Product.sales_measurement_unit_id', 'Product.order'),
				'order' => array('Product.order asc'),
				'recursive' => -1,
			));

			// echo $this->Store->getLastQuery();
			// pr($q_results);
			// exit;


			$results = array();
			$results_span = array();
			foreach ($q_results as $result) {

				if( $result['CurrentInventory']['inventory_status_id'] == 1 ){

					$qty = ($unit_type == 2) ? $result[0]['qty'] : $this->unit_convertfrombase($result['CurrentInventory']['product_id'], $result['Product']['sales_measurement_unit_id'], $result[0]['qty']);

					$results_span[$result['CurrentInventory']['product_id']][$result['Store']['id']]['row_span'] = isset($results_span[$result['CurrentInventory']['product_id']][$result['Store']['id']]['row_span']) ? ($results_span[$result['CurrentInventory']['product_id']][$result['Store']['id']]['row_span'] += 1) : 1;
					$results_span[$result['CurrentInventory']['product_id']]['row_span'] = isset($results_span[$result['CurrentInventory']['product_id']]['row_span']) ? ($results_span[$result['CurrentInventory']['product_id']]['row_span'] += 1) : 1;
					$results_span[$result['CurrentInventory']['product_id']][$result['CurrentInventory']['inventory_status_id']]['grand_total'] = isset($results_span[$result['CurrentInventory']['product_id']][$result['CurrentInventory']['inventory_status_id']]['grand_total']) ? ($results_span[$result['CurrentInventory']['product_id']][$result['CurrentInventory']['inventory_status_id']]['grand_total'] += $qty) : $qty;
					$results_span[$result['CurrentInventory']['inventory_status_id']]['total'] = isset($results_span[$result['CurrentInventory']['inventory_status_id']]['total']) ? ($results_span[$result['CurrentInventory']['inventory_status_id']]['total'] += $qty) : $qty;

					$results[$result['CurrentInventory']['product_id']][$result['Store']['id']][$result['CurrentInventory']['batch_number']][$result['CurrentInventory']['expire_date']] =
						array(
							'qty' 					=> sprintf("%01.2f", $qty),
							'store_name'			=> $result['Store']['name'],
							'office_id'				=> $result['Store']['office_id'],
							'product_id' 			=> $result['CurrentInventory']['product_id'],
							'product_name' 			=> $result['Product']['name'],
							'product_source' 		=> $result['Product']['source'],
							'batch_number' 			=> $result['CurrentInventory']['batch_number'],
							'expire_date' 			=> date('d M Y', strtotime($result['CurrentInventory']['expire_date'])),
							'inventory_status_id' 	=> $result['CurrentInventory']['inventory_status_id'],
						);
				}
			}

			/*pr($results_span);
			exit;*/
			$this->set(compact('results'));



			//From Challan
			$con = array(
				//'Challan.challan_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Challan.transaction_type_id' => array(1, 2),
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

			/*---- operator part Challan: start ------------- */
			if ($request_data['AreaBatchLotByStocks']['operator'] && $request_data['AreaBatchLotByStocks']['operator'] == 1) {
				$less_date = explode('-', $request_data['AreaBatchLotByStocks']['expire_date']);
				$less_date = date('Y-m-01', mktime(0, 0, 0, date('m', strtotime($less_date[0])), 1, $less_date[1]));
				$con["ChallanDetail.expire_date <"] = $less_date;
			} elseif ($request_data['AreaBatchLotByStocks']['operator'] && $request_data['AreaBatchLotByStocks']['operator'] == 2) {
				$gretter_date = explode('-', $request_data['AreaBatchLotByStocks']['expire_date']);
				$gretter_date = date('Y-m-t', mktime(0, 0, 0, date('m', strtotime($gretter_date[0])), 1, $gretter_date[1]));
				$con["ChallanDetail.expire_date >"] = $gretter_date;
			} elseif ($request_data['AreaBatchLotByStocks']['operator'] && $request_data['AreaBatchLotByStocks']['operator'] == 3) {
				$date_from = explode('-', $request_data['AreaBatchLotByStocks']['expire_date_from']);
				$date_from = date('Y-m-01', mktime(0, 0, 0, date('m', strtotime($date_from[0])), 1, $date_from[1]));

				$date_to = explode('-', $request_data['AreaBatchLotByStocks']['expire_date_to']);
				$date_to = date('Y-m-t', mktime(0, 0, 0, date('m', strtotime($date_to[0])), 1, $date_to[1]));
				$con["ChallanDetail.expire_date BETWEEN ? and ?"] = array($date_from, $date_to);
			}

			if ($next_month_expire == 1) {
				$con["ChallanDetail.expire_date BETWEEN ? and ?"] = array($date_from, $date_to);
			}

			/*---- operator part Challan: END ------------- */

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
				'fields' => array('SUM(ChallanDetail.challan_qty) as challan_qty', 'ChallanDetail.product_id', 'Store.id', 'Store.name', 'ChallanDetail.measurement_unit_id',  'ChallanDetail.batch_no', 'ChallanDetail.expire_date'),
				'group' => array('ChallanDetail.product_id', 'Store.id', 'Store.name', 'ChallanDetail.measurement_unit_id', 'ChallanDetail.batch_no', 'ChallanDetail.expire_date'),
				'recursive' => -1,
			));

			$challan_results = array();
			foreach ($challan_q_results as $result) {
				$challan_qty = ($unit_type == 1) ? $result[0]['challan_qty'] : $this->unit_convert($result['ChallanDetail']['product_id'], $result['ChallanDetail']['measurement_unit_id'], $result[0]['challan_qty']);
				//$results_span[$result['ChallanDetail']['product_id']]['transit_stock']['grand_total']=isset($results_span[$result['ChallanDetail']['product_id']]['transit_stock']['grand_total'])?($results_span[$result['ChallanDetail']['product_id']]['transit_stock']['grand_total']+=$challan_qty):$challan_qty;

				$challan_results[$result['ChallanDetail']['product_id']][$result['Store']['id']][$result['ChallanDetail']['batch_no']][$result['ChallanDetail']['expire_date']] =
					array(
						'product_id' 			=> $result['ChallanDetail']['product_id'],
						//'transaction_type_id' 	=> $result['Challan']['transaction_type_id'],
						'batch_no' 				=> $result['ChallanDetail']['batch_no'],
						'expire_date' 			=> $result['ChallanDetail']['expire_date'],
						'challan_qty' 			=> sprintf("%01.2f", $challan_qty),
					);
			}
			// pr($challan_q_results);
			// pr($challan_results);
			// pr($results_span);
			// exit;
			$this->set(compact('challan_results', 'results_span'));
		}

		$this->set(compact('offices', 'territories', 'outlet_type', 'region_offices', 'office_id', 'request_data', 'so_list'));
	}
}
