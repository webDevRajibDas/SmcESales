<?php
App::uses('AppController', 'Controller');
/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
class OrderComparisonReportsController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $uses = array('Memo', 'Thana', 'Market', 'SalesPerson', 'Outlet', 'Product', 'MeasurementUnit', 'ProductPrice', 'ProductCombination', 'Combination', 'MemoDetail', 'MeasurementUnit', 'ProductCategory', 'TerritoryAssignHistory', 'Office', 'Territory', 'ProductType');
	public $components = array('Paginator', 'Session', 'Filter.Filter');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index()
	{

		$user_group_id = $this->Session->read('Office.group_id');
		$office_parent_id = $this->UserAuth->getOfficeParentId();

		$this->loadModel('Division');
		$this->loadModel('District');
		$this->loadModel('Thana');
		$this->loadModel('DistDistributor');
		$this->loadModel('Office');
		$this->loadModel('DistTso');
		$this->loadModel('DistSalesRepresentative');
		$this->loadModel('DistOutlet');
		$this->loadModel('DistMarket');
		$this->loadModel('Product');
		$this->loadModel('DistOrder');
		$this->loadModel('DistMemo');

		ini_set('memory_limit', '2048M');
		ini_set('max_execution_time', 600);

		$divisions = $this->Division->find('list');

		$this->set('page_title', 'Order Comparison Report');

		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$office_id = 0;
		} else {
			$office_id = $this->UserAuth->getOfficeId();
		}
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id != 0) {
			$office_type = $this->Office->find('first', array(
				'conditions' => array('Office.id' => $this->UserAuth->getOfficeId()),
				'recursive' => -1
			));
			$office_type_id = $office_type['Office']['office_type_id'];
		}


		if ($office_parent_id == 0) {
			$region_office_condition = array('office_type_id' => 3);
			$office_conditions = array('office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
		} else {
			if ($office_type_id == 3) {
				$region_office_condition = array('office_type_id' => 3, 'Office.id' => $this->UserAuth->getOfficeId());
				$office_conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId(), 'office_type_id' => 2);
			} elseif ($office_type_id == 2) {
				$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'office_type_id' => 2);
			}
		}

		$offices = $this->Office->find('list', array(
			'conditions' => $office_conditions,
			'fields' => array('office_name')
		));

		if (isset($region_office_condition)) {
			$region_offices = $this->Office->find('list', array(
				'conditions' => $region_office_condition,
				'order' => array('office_name' => 'asc')
			));

			$this->set(compact('region_offices'));
		}
		$product_type_list = $this->ProductType->find('list');
		$product_category_list = $this->ProductCategory->find('list');
		$by_rows = array(
			/* 'area' 			=> 'By Area', */
			'db' 			=> 'By DB',
			'tso' 			=> 'By TSO',
			'sr' 			=> 'By SR',
			'division' 		=> 'By Division',
			'district' 		=> 'By District',
			'thana' 		=> 'By Thana',
			'product' 		=> 'By Product',
		);
		$this->set(compact('rows'));
		$by_colums = array(
			'order' => 'Order',
			'order_value' => 'Order Value',
			'pending' => 'Pending',
			'pending_value' => 'Pending Value',
			'delivery' => 'Delivery',
			'delivery_value' => 'Delivery Value',
			'invoice' => 'Invoice',
			'invoice_value' => 'Invoice Value',
			'cancel' => 'Cancel',
			'cancel_value' => 'Cancel Value',
		);

		$this->set(compact('offices', 'office_id', 'sales_people'));
		$this->set(compact('office_parent_id', 'divisions', 'product_type_list', 'product_category_list', 'by_rows', 'by_colums'));

		if ($this->request->is('post')) {

			$request_data = $this->request->data;
			$this->Session->write('request_data', $request_data);
			$conditions = $this->get_report_details($request_data);
			$conditions[] = array('DistOrderDetail.product_id' => $conditions['products']);
			//$conditions[] = array('DistOrderDetail.price >'=>0;
			unset($conditions['products']);
			//pr($conditions);die();
			$rows = $this->request->data['OrderComparisonReport']['row_id'];
			$fields = array();
			$columns = $this->request->data['OrderComparisonReport']['column_id'];
			$column_id = array();
			foreach ($columns as $key => $value) {
				$column_id[$value] = $value;
			}
			$this->set(compact('column_id'));
			$fields = array(
				'count(distinct(DistOrder.id)) as orders,
				SUM( DistOrderDetail.price * DistOrderDetail.sales_qty) as order_value ,

				count(distinct(case when DistOrder.status = 1 and processing_status IN (0,3) then DistOrder.id end)) as pending,
				SUM(case when DistOrder.status = 1 and processing_status IN (0,3)   then DistOrderDetail.price * DistOrderDetail.sales_qty end) as pending_value ,

				count(distinct(case when DistOrder.status=2 and processing_status=1 then DistOrder.id end)) as invoice,
				SUM(case when DistOrder.status=2 and processing_status = 1   then DistOrderDetail.price * DistOrderDetail.sales_qty end) as invoice_value ,

				count(distinct(case when DistOrder.status=2 and processing_status=2 then DistOrder.id end)) as delivery, 
				SUM(case when DistOrder.status=2 and processing_status=2 then DistOrderDetail.price * DistOrderDetail.sales_qty end) as delivery_value,

				count(distinct(case when DistOrder.status=3 and processing_status=0 then DistOrder.id end)) as cancel,
				SUM(case when DistOrder.status=3 and processing_status = 0   then DistOrderDetail.price * DistOrderDetail.sales_qty end) as cancel_value ,

				count(distinct(DistMemo.id)) as memos,
				SUM( DistMemoDetail.price * DistMemoDetail.sales_qty) as memo_value 
				',

			);
			$order = array();
			if ($rows == 'area') {
				$group = array('DistOrder.office_id, Office.office_name', 'Office.order');
				$fields[] = 'Office.office_name';
				$order = array('Office.order');
			} elseif ($rows == 'db') {
				$group = array('DistOrder.office_id, Office.office_name', 'Office.order', 'DistOrder.tso_id,TSO.name',  'DistOrder.distributor_id, DistDistributor.name', 'DistAE.name');
				$fields[] = 'Office.office_name';
				$fields[] = 'DistAE.name';
				$fields[] = 'TSO.name';
				$fields[] = 'DistDistributor.name';
				$order = array('Office.order', 'DistAE.name', 'TSO.name', 'DistDistributor.name');
			} elseif ($rows == 'tso') {
				$group = array('DistOrder.office_id, Office.office_name', 'Office.order', 'DistOrder.tso_id,TSO.name', 'DistAE.name');
				$fields[] = 'Office.office_name';
				$fields[] = 'DistAE.name';
				$fields[] = 'TSO.name';
				$order = array('Office.order', 'DistAE.name', 'TSO.name');
			} elseif ($rows == 'sr') {
				$group = array('DistOrder.office_id, Office.office_name', 'Office.order', 'DistOrder.tso_id,TSO.name', 'DistOrder.sr_id, SR.name', 'DistOrder.distributor_id, DistDistributor.name');
				$fields[] = 'Office.office_name';
				$fields[] = 'TSO.name';
				$fields[] = 'DistDistributor.name';
				$fields[] = 'SR.name';
				$order = array('Office.order', 'TSO.name', 'DistDistributor.name', 'SR.name');
			} elseif ($rows == 'division') {
				$group = array('Division.id,Division.name');
				$fields[] = 'Division.name';
			} elseif ($rows == 'district') {
				$group = array('District.id, District.name');
				$fields[] = 'District.name';
			} elseif ($rows == 'thana') {
				$group = array('DistOrder.thana_id, Thana.name');
				$fields[] = 'Thana.name';
			} elseif ($rows == 'product') {
				$group = array('DistOrderDetail.product_id, Product.name,Product.order');
				$order = array('Product.order');
				$fields[] = 'Product.name';
			}
			//pr($fields);die();
			$order_data = $this->DistOrder->find('all', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'alias' => 'Thana',
						'table' => 'thanas',
						'type' => 'LEFT',
						'conditions' => 'Thana.id = DistOrder.thana_id'
					),
					array(
						'alias' => 'District',
						'table' => 'districts',
						'type' => 'LEFT',
						'conditions' => 'District.id = Thana.district_id'
					),
					array(
						'alias' => 'Division',
						'table' => 'divisions',
						'type' => 'LEFT',
						'conditions' => 'Division.id = District.division_id'
					),
					array(
						'alias' => 'TSO',
						'table' => 'dist_tsos',
						'type' => 'LEFT',
						'conditions' => 'TSO.id = DistOrder.tso_id'
					),
					array(
						'alias' => 'DistOutlet',
						'table' => 'dist_outlets',
						'type' => 'LEFT',
						'conditions' => 'DistOutlet.id = DistOrder.outlet_id'
					),
					array(
						'alias' => 'DistMarket',
						'table' => 'dist_markets',
						'type' => 'Left',
						'conditions' => 'DistMarket.id = DistOrder.market_id'
					),
					array(
						'alias' => 'SR',
						'table' => 'dist_sales_representatives',
						'type' => 'LEFT',
						'conditions' => 'SR.id = DistOrder.sr_id'
					),
					array(
						'alias' => 'DistDistributor',
						'table' => 'dist_distributors',
						'type' => 'LEFT',
						'conditions' => 'DistDistributor.id = DistOrder.distributor_id'
					),
					array(
						'alias' => 'Office',
						'table' => 'offices',
						'type' => 'INNER',
						'conditions' => 'Office.id = DistOrder.office_id'
					),
					array(
						'alias' => 'DistOrderDetail',
						'table' => 'dist_order_details',
						'type' => 'Left',
						'conditions' => 'DistOrderDetail.dist_order_id = DistOrder.id and DistOrderDetail.price > 0 '
					),
					array(
						'alias' => 'Product',
						'table' => 'products',
						'type' => 'LEFT',
						'conditions' => 'DistOrderDetail.product_id = Product.id'
					),
					array(
						'alias' => 'DistMemo',
						'table' => 'dist_memos',
						'type' => 'LEFT',
						'conditions' => 'DistMemo.dist_order_no = DistOrder.dist_order_no'
					),
					array(
						'alias' => 'DistMemoDetail',
						'table' => 'dist_memo_details',
						'type' => 'LEFT',
						'conditions' => 'DistMemoDetail.dist_memo_id = DistMemo.id AND DistMemoDetail.product_id = Product.id'
					),
					array(
						'table' => 'dist_area_executives',
						'alias' => 'DistAE',
						'type' => 'LEFT',
						'conditions' => 'DistAE.id = TSO.dist_area_executive_id'

					),

				),
				'fields' => $fields,
				'group' => $group,
				'order' => $order,
				'recursive' => -1,
			));

			/* echo $this->DistOrder->getLastquery();
			//pr($order_data);
			die(); */

			$this->set(compact('order_data'));
		}
	}

	function get_report_details($request_data = array())
	{

		$this->loadModel('Division');
		$this->loadModel('District');
		$this->loadModel('Thana');
		$region_office_id = $this->UserAuth->getOfficeId();
		$region_office_id = isset($request_data['OrderComparisonReport']['region_office_id']) != '' ? $request_data['OrderComparisonReport']['region_office_id'] : $region_office_id;
		$this->set(compact('region_office_id'));
		$office_id = $request_data['OrderComparisonReport']['office_id'];
		$office_ids = array();
		$conditions = array();
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

			if (!empty($request_data['OrderComparisonReport']['office_id'])) {
				$office_id = $request_data['OrderComparisonReport']['office_id'];

				$conditions[] = array('DistOrder.office_id' => $office_id);
			} else {
				$conditions[] = array('DistOrder.office_id' => $office_ids);
			}
		}
		$this->set(compact('office_id'));

		/*if(!empty($request_data['OrderComparisonReport']['office_id'])){*/

		@$date_from = date('Y-m-d', strtotime($request_data['OrderComparisonReport']['date_from']));
		@$date_to = date('Y-m-d', strtotime($request_data['OrderComparisonReport']['date_to']));
		$conditions[] = "DistOrder.order_date >= '" . @$date_from . "' AND DistOrder.order_date <= '" . @$date_to . "'";
		$product_type = $request_data['OrderComparisonReport']['product_type'];
		$by_row = $request_data['OrderComparisonReport']['row_id'];

		if (!empty($request_data['OrderComparisonReport']['tso_id'])) {
			/*********** TSO Start **************/
			$tso_id = $request_data['OrderComparisonReport']['tso_id'];

			if (!empty(@$request_data['OrderComparisonReport']['dist_distributor_id'])) {
				$dist_distributors = $request_data['OrderComparisonReport']['dist_distributor_id'];

				if (!empty(@$request_data['OrderComparisonReport']['sr_id'])) {
					$srs = $request_data['OrderComparisonReport']['sr_id'];

					if (!empty(@$request_data['OrderComparisonReport']['route_beat_id'])) {
						$route_beats = $request_data['OrderComparisonReport']['route_beat_id'];

						if (!empty(@$request_data['OrderComparisonReport']['market_id'])) {
							$markets = $request_data['OrderComparisonReport']['market_id'];

							if (!empty(@$request_data['OrderComparisonReport']['outlet_id'])) {
								$outlets = $request_data['OrderComparisonReport']['outlet_id'];
								if (count($outlets) > 1) {
									$conditions[] = array('DistOrder.outlet_id IN' => $outlets);
								} else {
									$conditions[] = array('DistOrder.outlet_id' => $outlets);
								}
							} else {
								if (count($markets) > 1) {
									$conditions[] = array('DistOrder.market_id IN' => $markets);
								} else {
									$conditions[] = array('DistOrder.market_id' => $markets);
								}
							}
						} else {
							if (count($route_beats) > 1) {
								$conditions[] = array('DistOrder.dist_route_id IN' => $route_beats);
							} else {
								$conditions[] = array('DistOrder.dist_route_id' => $route_beats);
							}
						}
					} else {
						if (count($srs) > 1) {
							$conditions[] = array('DistOrder.sr_id IN' => $srs);
						} else {
							$conditions[] = array('DistOrder.sr_id' => $srs);
						}
					}
				} else {
					if (count($dist_distributors) > 1) {
						$conditions[] = array('DistOrder.distributor_id IN' => $dist_distributors);
					} else {
						$conditions[] = array('DistOrder.distributor_id' => $dist_distributors);
					}
				}
			} else {
				$conditions[] = array('DistOrder.tso_id' => $tso_id);
			}
			/***** TSO End*****/
		}

		/*********************************Geo Location Start******************************/
		if (!empty(@$request_data['OrderComparisonReport']['division_id'])) {
			$division_id = $request_data['OrderComparisonReport']['division_id'];

			if (!empty(@$request_data['OrderComparisonReport']['district_id'])) {
				$district_id = $request_data['OrderComparisonReport']['district_id'];

				if (!empty(@$request_data['OrderComparisonReport']['thana_id'])) {
					$thana_id = $request_data['OrderComparisonReport']['thana_id'];
					$conditions[] = array('DistOrder.thana_id' => $thana_id);
				} else {
					$thanas = $this->Thana->find('list', array(
						'conditions' => array('district_id' => $district_id),
					));
					$thana_list = array_keys($thanas);
					$conditions[] = array('DistOrder.thana_id' => $thana_list);
				}
			} else {
				$district_list = $this->District->find('list', array(
					'conditions' => array('division_id' => $division_id),
				));
				$thanas = $this->Thana->find('list', array(
					'conditions' => array('district_id' => array_keys($district_list)),
				));
				$thana_list = array_keys($thanas);
				$conditions[] = array('DistOrder.thana_id' => $thana_list);
			}
		}
		/*********************************Geo Location End******************************/
		/*********************************Products Start********************************/
		if (!empty(@$request_data['OrderComparisonReport']['product_type'])) {
			$product_type = $request_data['OrderComparisonReport']['product_type'];

			if (!empty(@$request_data['OrderComparisonReport']['product_type_categories_id'])) {
				$product_type_categories_id = $request_data['OrderComparisonReport']['product_type_categories_id'];
				if (!empty(@$request_data['OrderComparisonReport']['product_id'])) {
					$products = $request_data['OrderComparisonReport']['product_id'];

					$conditions['products'] = $products;
				} else {
					$products = $this->Product->find('list', array(
						'conditions' => array('product_category_id' => $product_type_categories_id),
					));
					$product_list = array_keys($products);
					$conditions['products'] = $product_list;
				}
			} else {
				$products = $this->Product->find('list', array(
					'conditions' => array('product_type_id' => $product_type),
				));
				$product_list = array_keys($products);
				$conditions['products'] = $product_list;
			}
		}
		/*********************************Products End********************************/

		/*}*/
		return $conditions;
	}
	/******************************* Filtering Areaa******************************************/
	function get_office_list()
	{
		$region_office_id = $this->request->data['region_office_id'];
		$this->loadModel('Office');
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$offices = $this->Office->find('all', array(
			'conditions' => array('Office.parent_office_id' => $region_office_id),
			'order' => array('Office.office_name' => 'asc'),
			'recursive' => 0,
		));
		$data_array = array();
		foreach ($offices as $key => $value) {
			$data_array[] = array(
				'id' => $value['Office']['id'],
				'name' => $value['Office']['office_name'],
			);
		}
		if (!empty($offices)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}
	function get_tso_list()
	{
		$office_id = $this->request->data['office_id'];
		$this->loadModel('DistTso');
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$dist_tsos = $this->DistTso->find('all', array(
			'conditions' => array(
				'DistTso.office_id' => $office_id,
				'DistTso.is_active' => 1,
			),
			'recursive' => 0,
		));

		$data_array = array();
		foreach ($dist_tsos as $key => $value) {
			$data_array[] = array(
				'id' => $value['DistTso']['id'],
				'name' => $value['DistTso']['name'],
			);
		}
		if (!empty($dist_tsos)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}
	function get_district_list()
	{
		$division_id = $this->request->data['division_id'];
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$this->loadModel('District');
		$districts = $this->District->find('all', array(
			'conditions' => array(
				'District.division_id' => $division_id,
			),
			'recursive' => 0,
		));
		$data_array = array();
		foreach ($districts as $key => $value) {
			$data_array[] = array(
				'id' => $value['District']['id'],
				'name' => $value['District']['name'],
			);
		}
		if (!empty($districts)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}
	function get_thana_list()
	{
		$district_id = $this->request->data['district_id'];
		$this->loadModel('Thana');
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$thanas = $this->Thana->find('all', array(
			'conditions' => array(
				'Thana.district_id' => $district_id,
			),
			'recursive' => 0,
		));
		$data_array = array();
		foreach ($thanas as $key => $value) {
			$data_array[] = array(
				'id' => $value['Thana']['id'],
				'name' => $value['Thana']['name'],
			);
		}
		if (!empty($thanas)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}
	function get_distributor_list()
	{
		$view = new View($this);

		$form = $view->loadHelper('Form');
		$office_id = @$this->request->data['office_id'];
		$tso_id = @$this->request->data['tso_id'];
		$this->loadModel('DistTsoMapping');
		$distributors = $this->DistTsoMapping->find('list', array(
			'conditions' => array(
				'DistTsoMapping.office_id' => $office_id,
				'DistTsoMapping.dist_tso_id' => $tso_id,
			),
			'joins' =>
			array(
				array(
					'table' => 'dist_distributors',
					'alias' => 'DB',
					'type' => 'LEFT',
					'conditions' => array('DB.id= DistTsoMapping.dist_distributor_id')
				)
			),
			'fields' => array('DB.id', 'DB.name'),
		));

		if ($distributors) {
			echo $form->create('OrderComparisonReport', array('role' => 'form'));
			echo $form->input('dist_distributor_id', array('id' => 'dist_distributor_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $distributors));
			$form->end();
		} else {
			echo '';
		}
		$this->autoRender = false;
		exit;
	}
	public function get_sr_list()
	{
		$view = new View($this);
		$form = $view->loadHelper('Form');
		//$office_id=@$this->request->data['office_id'];
		$dist_distributor_id = @$this->request->data['dist_distributor_id'];
		$this->loadModel('DistSalesRepresentative');
		$sr_infos = $this->DistSalesRepresentative->find('list', array(
			'conditions' => array(
				'dist_distributor_id' => $dist_distributor_id
			)
		));

		$data_array = array();
		if ($sr_infos) {
			echo $form->create('OrderComparisonReport', array('role' => 'form'));
			echo $form->input('sr_id', array('id' => 'sr_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $sr_infos));
			$form->end();
		} else {
			echo '';
		}
		$this->autoRender = false;
		exit;
	}
	public function get_rout_beat_list()
	{
		$view = new View($this);
		$form = $view->loadHelper('Form');
		//$office_id=@$this->request->data['office_id'];
		$sr_id = @$this->request->data['sr_id'];

		$this->loadModel('DistSrRouteMapping');
		$this->loadModel('DistRoute');
		$route_beat_maps = $this->DistSrRouteMapping->find('all', array(
			'conditions' => array('dist_sr_id' => $sr_id),
			'fields' => array('DISTINCT(DistRoute.id) as dist_route_id'),
		));
		$route_beats = array();
		foreach ($route_beat_maps as $key => $value) {
			$route_beats[$key] = $value[0]['dist_route_id'];
		}

		$dist_route_list = $this->DistRoute->find('list', array(
			'conditions' => array('id' => $route_beats)
		));

		$data_array = array();
		if ($dist_route_list) {
			echo $form->create('OrderComparisonReport', array('role' => 'form'));
			echo $form->input('route_beat_id', array('id' => 'route_beat_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $dist_route_list));
			$form->end();
		} else {
			echo '';
		}
		$this->autoRender = false;
		exit;
	}
	public function get_market_list()
	{
		$view = new View($this);
		$form = $view->loadHelper('Form');
		//$office_id=@$this->request->data['office_id'];
		$route_beat_id = @$this->request->data['route_beat_id'];

		$this->loadModel('DistMarket');
		$dist_market_list = $this->DistMarket->find('list', array(
			'conditions' => array('dist_route_id' => $route_beat_id)
		));
		$data_array = array();
		if ($dist_market_list) {
			echo $form->create('OrderComparisonReport', array('role' => 'form'));
			echo $form->input('market_id', array('id' => 'market_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $dist_market_list));
			$form->end();
		} else {
			echo '';
		}
		$this->autoRender = false;
		exit;
	}
	public function get_outlet_categories_list()
	{
		$view = new View($this);
		$form = $view->loadHelper('Form');
		//$office_id=@$this->request->data['office_id'];
		$market_id = @$this->request->data['market_id'];
		$this->loadModel('DistOutletCategory');
		$this->loadModel('DistOutlet');
		$dist_outlet_categoris_info = $this->DistOutlet->find('all', array(
			'conditions' => array('dist_market_id' => $market_id),
			'fields' => array('DISTINCT(DistOutlet.category_id) as dist_category_id'),
			'recursive' => -1,
		));

		$dist_outlet_categories = array();
		foreach ($dist_outlet_categoris_info as $key => $value) {
			$dist_outlet_categories[$key] = $value[0]['dist_category_id'];
		}

		$dist_outlet_category_list = $this->DistOutletCategory->find('list', array(
			'conditions' => array('id' => $dist_outlet_categories)
		));
		$data_array = array();
		if ($dist_outlet_category_list) {
			echo $form->create('OrderComparisonReport', array('role' => 'form'));
			echo $form->input('outlet_categories_id', array('id' => 'outlet_categories_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $dist_outlet_category_list));
			$form->end();
		} else {
			echo '';
		}
		$this->autoRender = false;
		exit;
	}
	public function get_outlet_list_by_category()
	{
		$view = new View($this);
		$form = $view->loadHelper('Form');
		//$office_id=@$this->request->data['office_id'];
		$outlet_categories_id = @$this->request->data['outlet_categories_id'];
		$market_id = @$this->request->data['market_id'];

		$conditions = array();
		if (!empty($market_id)) {
			$conditions[] = array('dist_market_id' => $market_id);
		}
		if (!empty($outlet_categories_id)) {
			$conditions[] = array('category_id' => $outlet_categories_id);
		}

		$this->loadModel('DistOutlet');
		$dist_outlet_list = $this->DistOutlet->find('list', array(
			'conditions' => $conditions,
		));
		$data_array = array();
		if ($dist_outlet_list) {
			echo $form->create('OrderComparisonReport', array('role' => 'form'));
			echo $form->input('outlet_id', array('id' => 'outlet_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $dist_outlet_list));
			$form->end();
		} else {
			echo '';
		}
		$this->autoRender = false;
		exit;
	}
	public function get_outlet_list()
	{
		$view = new View($this);
		$form = $view->loadHelper('Form');
		//$office_id=@$this->request->data['office_id'];
		$market_id = @$this->request->data['market_id'];

		$this->loadModel('DistOutlet');
		$dist_outlet_list = $this->DistOutlet->find('list', array(
			'conditions' => array('dist_market_id' => $market_id)
		));
		$data_array = array();
		if ($dist_outlet_list) {
			echo $form->create('OrderComparisonReport', array('role' => 'form'));
			echo $form->input('outlet_id', array('id' => 'outlet_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $dist_outlet_list));
			$form->end();
		} else {
			echo '';
		}
		$this->autoRender = false;
		exit;
	}
	function get_product_list()
	{
		$view = new View($this);
		$form = $view->loadHelper('Form');
		$product_types = @$this->request->data['OrderComparisonReport']['product_type'];
		$conditions = array();
		if ($product_types) {
			$conditions['product_type_id'] = $product_types;
		}
		$product_list = $this->Product->find('list', array(
			'conditions' => $conditions,
			'order' =>  array('order' => 'asc')
		));
		if ($product_list) {
			echo $form->create('OrderComparisonReport', array('role' => 'form'));

			echo $form->input('product_id', array('id' => 'product_id', 'label' => false, 'class' => 'product_id checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $product_list));
			$form->end();
		} else {
			echo '';
		}
		$this->autoRender = false;
	}
	public function get_product_list_by_category()
	{
		$view = new View($this);
		$form = $view->loadHelper('Form');
		$product_type_categories_id = @$this->request->data['product_type_categories_id'];
		$product_types = @$this->request->data['product_type'][0]['value'];

		$conditions = array();
		if ($product_types) {
			$conditions['product_type_id'] = $product_types;
		}
		if ($product_type_categories_id) {
			$conditions[] = array('product_category_id' => $product_type_categories_id);
		}

		$product_list = $this->Product->find('list', array(
			'conditions' => $conditions,
			'order' =>  array('order' => 'asc')
		));

		if ($product_list) {
			echo $form->create('OrderComparisonReport', array('role' => 'form'));

			echo $form->input('product_id', array('id' => 'product_id', 'label' => false, 'class' => 'product_id checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $product_list));
			$form->end();
		} else {
			echo '';
		}
		$this->autoRender = false;
	}

	/********Back up******/
	function get_report_details_back($request_data = array())
	{

		$this->loadModel('Division');
		$this->loadModel('District');
		$this->loadModel('Thana');
		$region_office_id = $this->UserAuth->getOfficeId();
		$region_office_id = isset($request_data['OrderComparisonReport']['region_office_id']) != '' ? $request_data['OrderComparisonReport']['region_office_id'] : $region_office_id;
		$this->set(compact('region_office_id'));
		$office_id = $request_data['OrderComparisonReport']['office_id'];
		$office_ids = array();
		$conditions = array();
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

			if (!empty($request_data['OrderComparisonReport']['office_id'])) {
				$office_id = $request_data['OrderComparisonReport']['office_id'];

				$conditions[] = array('DistOrder.office_id' => $office_id);
			} else {
				$conditions[] = array('DistOrder.office_id' => $office_ids);
			}
		}
		$this->set(compact('office_id'));

		if (!empty($request_data['OrderComparisonReport']['office_id'])) {

			@$date_from = date('Y-m-d', strtotime($request_data['OrderComparisonReport']['date_from']));
			@$date_to = date('Y-m-d', strtotime($request_data['OrderComparisonReport']['date_to']));

			$product_type = $request_data['OrderComparisonReport']['product_type'];
			$by_row = $request_data['OrderComparisonReport']['row_id'];

			if (!empty($request_data['OrderComparisonReport']['tso_id'])) {
				/*********** TSO Start **************/
				$tso_id = $request_data['OrderComparisonReport']['tso_id'];

				$conditions[] = array('DistOrder.tso_id' => $tso_id);

				if (!empty(@$request_data['OrderComparisonReport']['dist_distributor_id'])) {
					$dist_distributors = $request_data['OrderComparisonReport']['dist_distributor_id'];
					if (count($dist_distributors) > 1) {
						$conditions[] = array('DistOrder.distributor_id IN' => $dist_distributors);
					} else {
						$conditions[] = array('DistOrder.distributor_id' => $dist_distributors);
					}

					if (!empty(@$request_data['OrderComparisonReport']['sr_id'])) {
						$srs = $request_data['OrderComparisonReport']['sr_id'];
						if (count($srs) > 1) {
							$conditions[] = array('DistOrder.sr_id IN' => $srs);
						} else {
							$conditions[] = array('DistOrder.sr_id' => $srs);
						}
						if (!empty(@$request_data['OrderComparisonReport']['route_beat_id'])) {
							$route_beats = $request_data['OrderComparisonReport']['route_beat_id'];
							$conditions[] = array('DistOrder.dist_route_id' => $route_beats);
							if (count($route_beats) > 1) {
								$conditions[] = array('DistOrder.dist_route_id IN' => $route_beats);
							} else {
								$conditions[] = array('DistOrder.dist_route_id' => $route_beats);
							}
							if (!empty(@$request_data['OrderComparisonReport']['market_id'])) {
								$markets = $request_data['OrderComparisonReport']['market_id'];

								if (count($markets) > 1) {
									$conditions[] = array('DistOrder.market_id IN' => $markets);
								} else {
									$conditions[] = array('DistOrder.market_id' => $markets);
								}
								if (!empty(@$request_data['OrderComparisonReport']['outlet_id'])) {
									$outlets = $request_data['OrderComparisonReport']['outlet_id'];
									if (count($outlets) > 1) {
										$conditions[] = array('DistOrder.outlet_id IN' => $outlets);
									} else {
										$conditions[] = array('DistOrder.outlet_id' => $outlets);
									}
								}
							}
						}
					}
				}
				/***** TSO End*****/
			}

			/*********************************Geo Location Start******************************/
			if (!empty(@$request_data['OrderComparisonReport']['division_id'])) {
				$division_id = $request_data['OrderComparisonReport']['division_id'];

				if (!empty(@$request_data['OrderComparisonReport']['district_id'])) {
					$district_id = $request_data['OrderComparisonReport']['district_id'];

					if (!empty(@$request_data['OrderComparisonReport']['thana_id'])) {
						$thana_id = $request_data['OrderComparisonReport']['thana_id'];
						$conditions[] = array('DistOrder.thana_id' => $thana_id);
					} else {
						$thanas = $this->Thana->find('list', array(
							'conditions' => array('district_id' => $district_id),
						));
						$thana_list = array_keys($thanas);
						$conditions[] = array('DistOrder.thana_id' => $thana_list);
					}
				} else {
					$district_list = $this->District->find('list', array(
						'conditions' => array('division_id' => $division_id),
					));
					$thanas = $this->Thana->find('list', array(
						'conditions' => array('district_id' => array_keys($district_list)),
					));
					$thana_list = array_keys($thanas);
					$conditions[] = array('DistOrder.thana_id' => $thana_list);
				}
			}
			/*********************************Geo Location End******************************/
			/*********************************Products Start********************************/
			if (!empty(@$request_data['OrderComparisonReport']['product_type'])) {
				$product_type = $request_data['OrderComparisonReport']['product_type'];

				if (!empty(@$request_data['OrderComparisonReport']['product_type_categories_id'])) {
					$product_type_categories_id = $request_data['OrderComparisonReport']['product_type_categories_id'];
					if (!empty(@$request_data['OrderComparisonReport']['product_id'])) {
						$products = $request_data['OrderComparisonReport']['product_id'];

						$conditions['products'] = $products;
					} else {
						$products = $this->Product->find('list', array(
							'conditions' => array('product_category_id' => $product_type_categories_id),
						));
						$product_list = array_keys($products);
						$conditions['products'] = $product_list;
					}
				} else {
					$products = $this->Product->find('list', array(
						'conditions' => array('product_type_id' => $product_type),
					));
					$product_list = array_keys($products);
					$conditions['products'] = $product_list;
				}
			}
			/*********************************Products End********************************/
		}
		return $conditions;
	}
	/*********End*********/
}
