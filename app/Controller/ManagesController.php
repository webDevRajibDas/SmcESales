<?php
App::uses('AppController', 'Controller');

/**
 * Orders Controller
 *
 * @property Order $Order
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ManagesController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $uses = array('Order', 'Thana', 'Market', 'SalesPerson', 'Outlet', 'Product', 'MeasurementUnit', 'ProductPrice', 'ProductCombination', 'User', 'Combination', 'OrderDetail', 'MeasurementUnit');
	public $components = array('Paginator', 'Session', 'Filter.Filter');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index()
	{
		//echo 'hello';exit;
		//pr($this->Session->read('Office.id'));die();
		$product_name = $this->Product->find('all', array(
			'fields' => array('Product.name', 'Product.id', 'MU.name as mes_name', 'Product.product_category_id'),
			'joins' => array(
				array(
					'table' => 'measurement_units',
					'alias' => 'MU',
					'type' => 'LEFT',
					'conditions' => array('MU.id= Product.sales_measurement_unit_id')
				)
			),
			'conditions' => array('NOT' => array('Product.product_category_id' => 32)),
			'order' => 'Product.product_category_id',
			'recursive' => -1
		));

		//pr($product_name);
		$requested_data = $this->request->data;
		//pr($requested_data);die();
		$this->set('page_title', 'Distributor Product Issues');
		$this->Session->delete('cart_session_data');
		$this->Session->delete('matched_session_data');
		$this->Session->delete('combintaion_qty_data');

		$confirmation_status_optn = array(3 => 'Pending', 1 => 'Processing', 2 => 'Deliverd');

		$office_parent_id = $this->UserAuth->getOfficeParentId();
		$user_group_id = $this->Session->read('Office.group_id');

		$designation_id = $this->Session->read('Office.designation_id');

		$this->set('office_parent_id', $office_parent_id);


		if ($office_parent_id == 0) {
			//$conditions = array('Order.confirm_status >'=> 0);
			$conditions = array('Order.confirmed >' => 0, 'Order.order_date >=' => $this->current_date() . ' 00:00:00', 'Order.order_date <=' => $this->current_date() . ' 23:59:59');
			$office_conditions = array(
				'office_type_id' => 2,
				"NOT" => array("id" => array(30, 31, 37))
			);
		} else {
			$conditions = array('Order.confirmed >' => 0, 'Order.office_id' => $this->Session->read('Office.id'), 'Order.order_date >=' => $this->current_date() . ' 00:00:00', 'Order.order_date <=' => $this->current_date() . ' 23:59:59');
			$office_conditions = array(
				'office_type_id' => 2,
				'id' => $this->Session->read('Office.id'),
			);
		}
		//pr($conditions);
		//exit;

		// pr($conditions);die();



		$group = array('Order.id', 'Order.order_no', 'Order.order_date', 'Order.confirmed', 'Order.from_app', 'Order.memo_reference_no', 'Order.gross_value', 'Order.order_time', 'Order.credit_amount', 'Order.memo_editable', 'Order.status', 'Order.is_closed', 'Order.confirm_status', 'Order.action', 'Outlet.name', 'Market.name', 'Territory.name', 'Order.outlet_id', 'Order.territory_id', 'Order.market_id');
		if (isset($requested_data['Order']['payment_status'])) {
			if ($requested_data['Order']['payment_status'] == 1) {
				$group = array(
					'Order.id', 'Order.order_no', 'Order.order_date', 'Order.from_app', 'Order.memo_reference_no', 'Order.confirmed', 'Order.gross_value', 'Order.order_time', 'Order.credit_amount', 'Order.memo_editable', 'Order.status', 'Order.is_closed', 'Order.confirm_status', 'Order.action', 'Outlet.name', 'Market.name', 'Territory.name', 'Order.outlet_id', 'Order.territory_id', 'Order.market_id 
					HAVING SUM(Collection.collectionAmount) is null OR SUM(Collection.collectionAmount) < Order.gross_value'
				);
			} elseif ($requested_data['Order']['payment_status'] == 2) {
				$group = array('Order.id', 'Order.order_no', 'Order.order_date', 'Order.from_app', 'Order.memo_reference_no', 'Order.confirmed', 'Order.gross_value', 'Order.order_time', 'Order.credit_amount', 'Order.memo_editable', 'Order.status', 'Order.is_closed', 'Order.action', 'Outlet.name', 'Market.name', 'Territory.name', 'Order.outlet_id', 'Order.territory_id', 'Order.confirm_status', 'Order.market_id 
					HAVING SUM(Collection.collectionAmount) is not null AND SUM(Collection.collectionAmount) = Order.gross_value');
			}
		}

		$this->Order->recursive = 0;

		$conditions1 = array(
			"NOT" => array("Order.id" => strtotime("now"))
		);

		$conditions2 = array_merge($conditions, $conditions1);

		$this->paginate = array(
			'fields' => array(
				'Order.id', 'Order.order_no', 'Order.from_app', 'Order.order_date', 'Order.confirmed', 'Order.memo_reference_no', 'Order.gross_value', 'Order.order_time', 'Order.credit_amount', 'Order.status', 'Order.is_closed', 'Order.memo_editable', 'Order.action', 'Outlet.name', 'Market.name', 'Territory.name', 'Order.outlet_id', 'Order.territory_id', 'Order.market_id', 'Order.confirm_status'
				/*'CASE 
					WHEN SUM(Collection.collectionAmount) is null THEN 1 
					WHEN SUM(Collection.collectionAmount) < Order.gross_value THEN 1 
					ELSE 2 END as payment_status'*/
			),
			'conditions' => $conditions2,
			'joins' => array(

				array(
					'table' => 'offices',
					'alias' => 'Office',
					'conditions' => 'Order.office_id=Office.id',
					'type' => 'Left'
				),
				array(
					'table' => 'markets',
					'alias' => 'Market',
					'conditions' => 'Order.market_id=Market.id',
					'type' => 'Left'
				),
				array(
					'table' => 'outlets',
					'alias' => 'Outlet',
					'conditions' => 'Order.outlet_id=Outlet.id',
					'type' => 'Left'
				),
				array(
					'table' => 'dist_outlet_maps',
					'alias' => 'DistOutletMap',
					'conditions' => 'Order.outlet_id=DistOutletMap.outlet_id',
					'type' => 'Left'
				),
				array(
					'table' => 'territories',
					'alias' => 'Territory',
					'conditions' => 'Territory.id=Order.territory_id',
					'type' => 'Left'
				),

			),
			'group' => $group,
			'order' => array('Order.status' => 'asc', 'Order.confirm_status' => 'asc'),
			'limit' => 100
		);


		//pr($this->paginate());exit;
		$this->set('orders', $this->paginate());

		//$order = array();
		//$this->set('orders', $order);

		$this->set('office_id', $this->UserAuth->getOfficeId());

		$this->loadModel('Office');
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		$office_id = isset($this->request->data['Order']['office_id']) != '' ? $this->request->data['Order']['office_id'] : 0;
		$territory_id = isset($this->request->data['Order']['territory_id']) != '' ? $this->request->data['Order']['territory_id'] : 0;
		$market_id = isset($this->request->data['Order']['market_id']) != '' ? $this->request->data['Order']['market_id'] : 0;
		$distribut_outlet_id = isset($this->request->data['Order']['distribut_outlet_id']) != '' ? $this->request->data['Order']['distribut_outlet_id'] : 0;
		$distributors = array();
		//pr($this->request->data);die();
		if ($office_id) {
			$this->loadModel('DistDistributor');
			$distributor_info = $this->DistDistributor->find('all', array(
				'conditions' => array(
					'DistDistributor.office_id' => $office_id,
					'DistDistributor.is_active' => 1
				),
				'order' => array('DistDistributor.name' => 'asc'),
				// 'recursive'=> -1
			));

			foreach ($distributor_info as $key => $value) {
				if ($value['DistOutletMap']['outlet_id'] != null) {
					$distributors[$value['DistOutletMap']['outlet_id']] = $value['DistDistributor']['name'];
				}
			}
		}
		$this->loadModel('Territory');
		$territory = $this->Territory->find('all', array(
			'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
			'conditions' => array('Territory.office_id' => $office_id),
			'order' => array('Territory.name' => 'asc'),
			'recursive' => 0
		));
		//$this->dd($territory);
		$data_array = array();

		foreach ($territory as $key => $value) {
			$t_id = $value['Territory']['id'];
			$t_val = $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')';
			$data_array[$t_id] = $t_val;
		}

		$territories = $data_array;

		/*
		$territories = $this->Order->Territory->find('list', array(
			'conditions' => array('Territory.office_id' => $office_id),
			'order' => array('Territory.name' => 'asc')
			));
		*/

		if ($territory_id) {
			$markets = $this->Order->Market->find('list', array(
				'conditions' => array('Market.territory_id' => $territory_id),
				'order' => array('Market.name' => 'asc')
			));
		} else {
			$markets = array();
		}

		$outlets = $this->Order->Outlet->find('list', array(
			'conditions' => array('Outlet.market_id' => $market_id),
			'order' => array('Outlet.name' => 'asc')
		));
		// print_r($outlets);die();
		$this->loadModel('DistDistributor');
		$distributers = $this->DistDistributor->find('list', array(
			'conditions' => array('DistDistributor.office_id' => $office_id),
			'order' => array('DistDistributor.name' => 'asc')
		));
		//print_r($distributers);die();
		$current_date = date('d-m-Y', strtotime($this->current_date()));

		/*
		 * Report generation query start ;
		 */
		if (!empty($requested_data)) {
			if (!empty($requested_data['Order']['office_id'])) {


				$office_id = $requested_data['Order']['office_id'];
				$this->Order->recursive = -1;
				$sales_people = $this->Order->find('all', array(
					'fields' => array('DISTINCT(sales_person_id) as sales_person_id', 'SalesPerson.name'),
					'joins' => array(
						array(
							'table' => 'sales_people',
							'alias' => 'SalesPerson',
							'type' => 'INNER',
							'conditions' => array(
								' SalesPerson.id=Order.sales_person_id',
								'SalesPerson.office_id' => $office_id
							)
						)
					),
					'conditions' => array(
						'Order.order_date BETWEEN ? and ?' => array(date('Y-m-d', strtotime($requested_data['Order']['date_from'])), date('Y-m-d', strtotime($requested_data['Order']['date_to'])))
					),
				));

				$sales_person = array();
				foreach ($sales_people as  $data) {
					$sales_person[] = $data['0']['sales_person_id'];
				}
				$sales_person = implode(',', $sales_person);

				//pr($sales_person);

				if (!empty($sales_person)) {
					$product_quantity = $this->Order->query(" SELECT m.sales_person_id,md.product_id,SUM(md.sales_qty) as pro_quantity
					   FROM orders m RIGHT JOIN order_details md on md.order_id=m.id
					   WHERE (m.order_date BETWEEN  '" . date('Y-m-d', strtotime($requested_data['Order']['date_from'])) . "' AND '" . date('Y-m-d', strtotime($requested_data['Order']['date_to'])) . "') AND sales_person_id IN (" . $sales_person . ")  GROUP BY m.sales_person_id,md.product_id");
					$this->set(compact('product_quantity', 'sales_people'));
				}
			}
		}

		$this->set(compact('offices', 'territories', 'markets', 'outlets', 'current_date', 'distribut_outlet_id', 'requested_data', 'product_name', 'distributers', 'confirmation_status_optn', 'distributors'));
	}


	public function admin_distributor_update_date()
	{
		//pr($this->Session->read('Office.id'));die();
		$product_name = $this->Product->find('all', array(
			'fields' => array('Product.name', 'Product.id', 'MU.name as mes_name', 'Product.product_category_id'),
			'joins' => array(
				array(
					'table' => 'measurement_units',
					'alias' => 'MU',
					'type' => 'LEFT',
					'conditions' => array('MU.id= Product.sales_measurement_unit_id')
				)
			),
			'conditions' => array('NOT' => array('Product.product_category_id' => 32)),
			'order' => 'Product.product_category_id',
			'recursive' => -1
		));

		//pr($product_name);
		$requested_data = $this->request->data;
		//pr($requested_data);die();
		$this->set('page_title', 'Distributor Product Issues');
		$this->Session->delete('cart_session_data');
		$this->Session->delete('matched_session_data');
		$this->Session->delete('combintaion_qty_data');

		$confirmation_status_optn = array(3 => 'Pending', 1 => 'Processing', 2 => 'Deliverd');

		$office_parent_id = $this->UserAuth->getOfficeParentId();
		$user_group_id = $this->Session->read('Office.group_id');

		$designation_id = $this->Session->read('Office.designation_id');

		$this->set('office_parent_id', $office_parent_id);


		if ($office_parent_id == 0) {
			//$conditions = array('Order.confirm_status >'=> 0);
			$conditions = array('Order.confirmed >' => 0, 'Order.order_date >=' => $this->current_date() . ' 00:00:00', 'Order.order_date <=' => $this->current_date() . ' 23:59:59');
			$office_conditions = array(
				'office_type_id' => 2,
				"NOT" => array("id" => array(30, 31, 37))
			);
		} else {
			$conditions = array('Order.confirmed >' => 0, 'Order.office_id' => $this->Session->read('Office.id'), 'Order.order_date >=' => $this->current_date() . ' 00:00:00', 'Order.order_date <=' => $this->current_date() . ' 23:59:59');
			$office_conditions = array(
				'office_type_id' => 2,
				'id' => $this->Session->read('Office.id'),
			);
		}
		//pr($conditions);
		//exit;

		// pr($conditions);die();
		$group = array('Order.id', 'Order.order_no', 'Order.order_date', 'Order.confirmed', 'Order.editable', 'Order.from_app', 'Order.memo_reference_no', 'Order.gross_value', 'Order.order_time', 'Order.credit_amount', 'Order.memo_editable', 'Order.status', 'Order.is_closed', 'Order.confirm_status', 'Order.action', 'Outlet.name', 'Market.name', 'Territory.name', 'Order.outlet_id', 'Order.territory_id', 'Order.market_id');
		if (isset($requested_data['Order']['payment_status'])) {
			if ($requested_data['Order']['payment_status'] == 1) {
				$group = array(
					'Order.id', 'Order.order_no', 'Order.order_date', 'Order.from_app', 'Order.memo_reference_no', 'Order.confirmed', 'Order.gross_value', 'Order.order_time', 'Order.credit_amount', 'Order.memo_editable', 'Order.status', 'Order.is_closed', 'Order.confirm_status', 'Order.action', 'Outlet.name', 'Market.name', 'Territory.name', 'Order.outlet_id', 'Order.territory_id', 'Order.editable', 'Order.market_id 
					HAVING SUM(Collection.collectionAmount) is null OR SUM(Collection.collectionAmount) < Order.gross_value'
				);
			} elseif ($requested_data['Order']['payment_status'] == 2) {
				$group = array('Order.id', 'Order.order_no', 'Order.order_date', 'Order.from_app', 'Order.memo_reference_no', 'Order.confirmed', 'Order.gross_value', 'Order.order_time', 'Order.credit_amount', 'Order.memo_editable', 'Order.status', 'Order.is_closed', 'Order.action', 'Outlet.name', 'Market.name', 'Territory.name', 'Order.outlet_id', 'Order.territory_id', 'Order.confirm_status', 'Order.editable', 'Order.market_id 
					HAVING SUM(Collection.collectionAmount) is not null AND SUM(Collection.collectionAmount) = Order.gross_value');
			}
		}

		$this->Order->recursive = 0;
		$this->paginate = array(
			'fields' => array(
				'Order.id', 'Order.order_no', 'Order.from_app', 'Order.order_date', 'Order.confirmed', 'Order.memo_reference_no', 'Order.gross_value', 'Order.order_time', 'Order.credit_amount', 'Order.status', 'Order.is_closed', 'Order.memo_editable', 'Order.action', 'Outlet.name', 'Market.name', 'Territory.name', 'Order.outlet_id', 'Order.territory_id', 'Order.market_id', 'Order.confirm_status', 'Order.editable'
				/*'CASE 
					WHEN SUM(Collection.collectionAmount) is null THEN 1 
					WHEN SUM(Collection.collectionAmount) < Order.gross_value THEN 1 
					ELSE 2 END as payment_status'*/
			),
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'collections',
					'alias' => 'Collection',
					'conditions' => 'Collection.memo_id=Order.id',
					'type' => 'Left'
				)
			),
			'group' => $group,
			'order' => array('Order.status' => 'asc', 'Order.confirm_status' => 'asc'),
			'limit' => 100
		);
		//pr($this->paginate());exit;
		$this->set('orders', $this->paginate());

		$this->set('office_id', $this->UserAuth->getOfficeId());

		$this->loadModel('Office');
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		$office_id = isset($this->request->data['Order']['office_id']) != '' ? $this->request->data['Order']['office_id'] : 0;
		$territory_id = isset($this->request->data['Order']['territory_id']) != '' ? $this->request->data['Order']['territory_id'] : 0;
		$market_id = isset($this->request->data['Order']['market_id']) != '' ? $this->request->data['Order']['market_id'] : 0;
		$distribut_outlet_id = isset($this->request->data['Order']['distribut_outlet_id']) != '' ? $this->request->data['Order']['distribut_outlet_id'] : 0;
		$distributors = array();
		//pr($this->request->data);die();
		if ($office_id) {
			$this->loadModel('DistDistributor');
			$distributor_info = $this->DistDistributor->find('all', array(
				'conditions' => array(
					'DistDistributor.office_id' => $office_id,
					'DistDistributor.is_active' => 1
				),
				'order' => array('DistDistributor.name' => 'asc'),
				// 'recursive'=> -1
			));

			foreach ($distributor_info as $key => $value) {
				if ($value['DistOutletMap']['outlet_id'] != null) {
					$distributors[$value['DistOutletMap']['outlet_id']] = $value['DistDistributor']['name'];
				}
			}
		}
		$this->loadModel('Territory');
		$territory = $this->Territory->find('all', array(
			'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
			'conditions' => array('Territory.office_id' => $office_id),
			'order' => array('Territory.name' => 'asc'),
			'recursive' => 0
		));

		$data_array = array();

		foreach ($territory as $key => $value) {
			$t_id = $value['Territory']['id'];
			$t_val = $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')';
			$data_array[$t_id] = $t_val;
		}

		$territories = $data_array;

		/*
		$territories = $this->Order->Territory->find('list', array(
			'conditions' => array('Territory.office_id' => $office_id),
			'order' => array('Territory.name' => 'asc')
			));
		*/

		if ($territory_id) {
			$markets = $this->Order->Market->find('list', array(
				'conditions' => array('Market.territory_id' => $territory_id),
				'order' => array('Market.name' => 'asc')
			));
		} else {
			$markets = array();
		}

		$outlets = $this->Order->Outlet->find('list', array(
			'conditions' => array('Outlet.market_id' => $market_id),
			'order' => array('Outlet.name' => 'asc')
		));
		// print_r($outlets);die();
		$this->loadModel('DistDistributor');
		$distributers = $this->DistDistributor->find('list', array(
			'conditions' => array('DistDistributor.office_id' => $office_id),
			'order' => array('DistDistributor.name' => 'asc')
		));
		//print_r($distributers);die();
		$current_date = date('d-m-Y', strtotime($this->current_date()));

		/*
		 * Report generation query start ;
		 */
		if (!empty($requested_data)) {
			if (!empty($requested_data['Order']['office_id'])) {


				$office_id = $requested_data['Order']['office_id'];
				$this->Order->recursive = -1;
				$sales_people = $this->Order->find('all', array(
					'fields' => array('DISTINCT(sales_person_id) as sales_person_id', 'SalesPerson.name'),
					'joins' => array(
						array(
							'table' => 'sales_people',
							'alias' => 'SalesPerson',
							'type' => 'INNER',
							'conditions' => array(
								' SalesPerson.id=Order.sales_person_id',
								'SalesPerson.office_id' => $office_id
							)
						)
					),
					'conditions' => array(
						'Order.order_date BETWEEN ? and ?' => array(date('Y-m-d', strtotime($requested_data['Order']['date_from'])), date('Y-m-d', strtotime($requested_data['Order']['date_to'])))
					),
				));

				$sales_person = array();
				foreach ($sales_people as  $data) {
					$sales_person[] = $data['0']['sales_person_id'];
				}
				$sales_person = implode(',', $sales_person);

				//pr($sales_person);

				if (!empty($sales_person)) {
					$product_quantity = $this->Order->query(" SELECT m.sales_person_id,md.product_id,SUM(md.sales_qty) as pro_quantity
					   FROM orders m RIGHT JOIN order_details md on md.order_id=m.id
					   WHERE (m.order_date BETWEEN  '" . date('Y-m-d', strtotime($requested_data['Order']['date_from'])) . "' AND '" . date('Y-m-d', strtotime($requested_data['Order']['date_to'])) . "') AND sales_person_id IN (" . $sales_person . ")  GROUP BY m.sales_person_id,md.product_id");
					$this->set(compact('product_quantity', 'sales_people'));
				}
			}
		}

		$this->set(compact('offices', 'territories', 'markets', 'outlets', 'current_date', 'distribut_outlet_id', 'requested_data', 'product_name', 'distributers', 'confirmation_status_optn', 'distributors'));
	}

	/**
	 * admin_view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */

	public function admin_view($id = null)
	{

		//$this->check_data_by_company('Order',$id);
		$this->set('page_title', 'Order Manage Confirmation Details');
		$dealer_is_limit_check = 1;
		$this->Order->unbindModel(array('hasMany' => array('OrderDetail')));
		$order = $this->Order->find('first', array(
			'conditions' => array('Order.id' => $id)
		));
		$this->loadModel('DistOutletMap');
		$outletInfo = $this->DistOutletMap->find('first', array('conditions' => array('DistOutletMap.outlet_id' => $order['Order']['outlet_id'])));

		// pr($order);die();
		$distributor_id = $outletInfo['DistDistributor']['id'];
		$this->loadModel('DistDistributor');
		$distributor = $this->DistDistributor->find('first', array('conditions' => array('DistDistributor.id' => $distributor_id)));
		$this->loadModel('User');
		$user_name = $this->User->find('first', array(
			'conditions' => array('User.id' => $order['Order']['updated_by']),
			'recursive' => -1
		));

		//$orderLimits=$distributor['DistDistributorBalance'];
		$orderDate = $order['Order']['order_date'];
		//pr($orderLimits);die();

		$this->loadModel('DistDistributorBalance');
		$limts = $this->DistDistributorBalance->find('first', array(
			'fields' => array('balance'),
			'conditions' => array(
				//'DistDistributorBalance.effective_date >=' => $orderDate,
				'DistDistributorBalance.dist_distributor_id' => $distributor_id,
			),
			// //'order' => 'DistDistributorBalance.effective_date DESC',
			'limit' => 1,
			'recursive' => -1

		));
		$orderLimits = $limts['DistDistributorBalance']['balance'];

		$this->loadModel('DistDistributorBalanceHistory');
		$dealer_balance_info = $this->DistDistributorBalanceHistory->find('first', array(
			'conditions' => array(
				'DistDistributorBalanceHistory.dist_distributor_id' => $distributor_id,
				////'DistDistributorBalanceHistory.is_execute' => 1,
			),
			'order' => 'DistDistributorBalanceHistory.id DESC',
			'recursive' => -1
		));
		$balance = $dealer_balance_info['DistDistributorBalanceHistory']['balance'];


		//pr($dealer_balance_info);die();
		$this->loadModel('OrderDetail');
		if (!$this->Order->exists($id)) {
			throw new NotFoundException(__('Invalid district'));
		}
		$this->OrderDetail->recursive = 0;
		$order_details = $this->OrderDetail->find(
			'all',
			array(
				'conditions' => array('OrderDetail.order_id' => $id),
				'joins' => array(
					array(
						'table' => 'products',
						'alias' => 'Product',
						'type' => 'Left',
						'conditions' => 'Product.id=OrderDetail.product_id'
					)
				),
				'order' => array('Product.order' => 'asc')
			)
		);
		$this->loadModel('Product');
		$bns_product = array();
		foreach ($order_details as $key => $value) {
			if ($value['OrderDetail']['bonus_qty'] != 0) {
				$bns_product_list = $this->Product->find('first', array(
					'conditions' => array('Product.id' => $value['OrderDetail']['bonus_product_id']),
					'recursive' => -1,
				));
				$bns_product[$bns_product_list['Product']['id']] = $bns_product_list['Product']['name'];
			}
		}
		//pr($bns_product);die();
		$this->set(compact('order', 'order_details', 'distributor', 'orderLimits', 'balance', 'dealer_is_limit_check', 'bns_product', 'user_name'));
	}


	/**
	 * admin_delete method
	 *
	 * @return void
	 */
	public function admin_delete($id = null, $redirect = 1)
	{

		$this->loadModel('Product');
		$this->loadModel('Order');
		$this->loadModel('TempOrderDetail');
		$this->loadModel('OrderDetail');
		$this->loadModel('Deposit');
		$this->loadModel('Collection');
		$this->loadModel('Memo');
		$this->loadModel('MemoDetail');
		//$this->check_data_by_company('Order',$id);
		if ($this->request->is('post')) {
			/* $path = APP . 'logs/';
			$myfile = fopen($path . "db_requisition_process.txt", "a") or die("Unable to open file!"); */
			/*
			* This condition added for data synchronization 
			* Cteated by imrul in 09, April 2017
			* Duplicate order check
			*/
			$count = $this->Order->find('count', array(
				'conditions' => array(
					'Order.id' => $id
				)
			));

			$order_id_arr = $this->Order->find('first', array(
				'conditions' => array(
					'Order.id' => $id
				)
			));
			// fwrite($myfile, "\n" . $this->current_datetime() . ': ' . $order_id_arr['Order']['order_no'] . 'Order Delete function');

			$this->loadModel('Store');
			$store_id_arr = $this->Store->find('first', array(
				'conditions' => array(
					'Store.office_id' => $order_id_arr['Order']['office_id']
				)
			));
			$store_id = $store_id_arr['Store']['id'];



			$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
			$product_list = Set::extract($products, '{n}.Product');
		}

		$order_id = $order_id_arr['Order']['id'];
		$order_no = $order_id_arr['Order']['order_no'];
		$this->Order->id = $order_id;

		$memo_info = array();
		$memo_info = $this->Memo->find('first', array(
			'conditions' => array('Memo.memo_no Like' => "%" . $order_no . "%"),
			'recursive' => -1
		));
		if (!empty($memo_info)) {
			$memo_id = $memo_info['Memo']['id'];
			$this->admin_deletememo($memo_id, 0);
			/*$this->Collection->deleteAll(array('Collection.memo_id' => $memo_id));
				$this->MemoDetail->deleteAll(array('MemoDetail.memo_id' => $memo_id));
				$this->Memo->delete(array('Memo.id' => $memo_id));*/
		}
		//pr($order_id);die();
		// $this->TempOrderDetail->deleteAll(array('TempOrderDetail.order_id' => $order_id));
		$this->OrderDetail->deleteAll(array('OrderDetail.order_id' => $order_id));



		//$this->Deposit->deleteAll(array('Deposit.order_id' => $order_no));
		//$this->Collection->deleteAll(array('Collection.order_id' => $order_no));
		$this->Order->delete();
		// fwrite($myfile, "\n" . $this->current_datetime() . ': ' . $order_id_arr['Order']['order_no'] . 'Order Deleted');

		if ($redirect == 1) {
			$this->flash(__('Order was not deleted'), array('action' => 'index'));
			$this->redirect(array('action' => 'index'));
		} else {
		}
	}
	/**
	 * admin_add method
	 *
	 * @return void
	 */

	public function admin_create_order()
	{
		
		
		
		ini_set('max_execution_time', 999999);
		ini_set('memory_limit', '-1');
		$this->loadModel('Order');
		$this->loadModel('ProductCombination');
		$this->loadModel('Combination');
		$this->loadModel('DistCombination');
		$this->loadModel('Product');
		$this->loadModel('Memo');
		$this->loadModel('DistOutletMap');
		$this->loadModel('CurrentInventory');
		$this->loadModel('MeasurementUnit');
		$this->LoadModel('Store');
		$this->loadModel('ProductPrice');
		$this->loadModel('ProductCombination');
		$this->loadModel('Outlet');
		$this->loadModel('DistDistributorBalance');
		$this->loadModel('DistDistributorBalanceHistory');
		$this->loadModel('DistDistributorLimit');
		$this->loadModel('DistDistributorLimitHistory');

		$office_parent_id = $this->UserAuth->getOfficeParentId();

		$current_date = date('d-m-Y', strtotime($this->current_date()));
		$order_list_conditions = array();
		if ($office_parent_id == 0) {
			$order_list_conditions = array(
				'Order.confirmed' => 1,
				'Order.confirm_status ' => 0
			);
		} else {
			$order_list_conditions = array(
				'Order.confirmed' => 1,
				'Order.confirm_status ' => 0,
				'Order.office_id' => $this->Session->read('Office.id')
			);
		}
		$orsers = $this->Order->find('all', array('conditions' => $order_list_conditions));

		$order_list = array();
		foreach ($orsers as $key => $value) {
			$order_list[$value['Order']['id']] = $value['Order']['order_no'];
		}
		$requisition_type_list = array(0 => 'Pull', 1 => 'Push');
		$this->set(compact('order_list', 'requisition_type_list'));

		if ($this->request->is('post')) {
			// pr($this->request->data); die();
			/* unnecessary code -- commented by naser
			$outlet_is_within_group = $this->outletGroupCheck($this->request->data['distribut_outlet_id']);
			$product_is_injectable = $this->productInjectableCheck($this->request->data['OrderDetail']['product_id']); 
			*/
			$office_id = $this->request->data['office_id'];
			$outlet_id = $this->request->data['distribut_outlet_id'];

			$getOutlets = $this->Outlet->find('all', array(
				'conditions' => array('Outlet.id' => $outlet_id),
			));
			$outlet_info = $this->DistOutletMap->find('first', array(
				'conditions' => array('DistOutletMap.outlet_id' => $this->request->data['distribut_outlet_id']),

			));

			$market_id = $getOutlets[0]['Outlet']['market_id'];
			$this->loadModel('Market');
			$market_info = $this->Market->find('first', array(
				'conditions' => array('Market.id' => $market_id),
				'fields' => 'Market.thana_id',
				'order' => array('Market.id' => 'asc'),
				'recursive' => -1,
			));

			$thana_id = $market_info['Market']['thana_id'];
			$distributor_id = $outlet_info['DistDistributor']['id'];

			if ($this->request->data['OrderProces']['requisition_type_id'] == 0) {
				$id = $this->request->data['OrderProces']['order_id'];
				$order_id = $id;
				$store_id = $this->request->data['w_store_id'];
				//$this->admin_delete($order_id, 0);
				$orderData['id'] = $order_id;
			} else {
				$this->loadModel('Store');
				$this->Store->recursive = -1;
				$store_info = $this->Store->find('first', array(
					'conditions' => array(
						'office_id' => $office_id,
						'store_type_id' => 2
					)
				));
				$store_id = $store_info['Store']['id'];

				$w_store_id = $store_id;
				$this->request->data['w_store_id'] = $w_store_id;
				$this->Order->create();
			}

			$stock_check = 0;
			$stock_available = 1;
			$m = "";
			$gross_amount = 0;
			$order_product_array_for_stock_check = array();
			$products = $this->Product->find('all', array('fields' => array('id', 'name', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
			$product_list = Set::extract($products, '{n}.Product');
			foreach ($this->request->data['OrderDetail']['product_id'] as $key => $val) {
				if ($val == NULL) {
					continue;
				}
				/*------ Stock Checking array preparation :start  --------------*/
				if (!isset($order_product_array_for_stock_check[$val])) {
					$order_product_array_for_stock_check[$val] = 0;
				}
				$punits_pre = $this->search_array($val, 'id', $product_list);
				$price = $this->request->data['OrderDetail']['Price'][$key];
				if ($price == 0.0) {
					$qty = $this->request->data['OrderDetail']['sales_qty'][$key];
				} else {
					$qty = $this->request->data['OrderDetail']['deliverd_qty'][$key];
				}
				$measurement_unit_id = isset($this->request->data['OrderDetail']['measurement_unit_id'][$key]) ? $this->request->data['OrderDetail']['measurement_unit_id'][$key] : $punits_pre['sales_measurement_unit_id'];
				$base_qty = 0;
				if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
					$base_qty = round($qty);
				} else {
					$base_qty = $this->unit_convert($val, $measurement_unit_id, $qty);
				}
				$bonus_base_qty = 0;
				if (isset($this->request->data['OrderDetail']['bonus_product_qty'][$key])) {
					$bonus_qty = $this->request->data['OrderDetail']['bonus_product_qty'][$key];
					$measurement_unit_id = isset($this->request->data['OrderDetail']['bonus_measurement_unit_id'][$key]) ? $this->request->data['OrderDetail']['measurement_unit_id'][$key] : $punits_pre['sales_measurement_unit_id'];
					if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
						$bonus_base_qty = round($bonus_qty);
					} else {
						$bonus_base_qty = $this->unit_convert($val, $measurement_unit_id, $bonus_qty);
					}
				}
				$order_product_array_for_stock_check[$val] += ($base_qty + $bonus_base_qty);
				/*------ Stock Checking array preparation :end  --------------*/
				if (!isset($this->request->data['OrderDetail']['deliverd_qty'][$key]) || $this->request->data['OrderDetail']['deliverd_qty'][$key] == 0) {
					continue;
				}
				$total_product_price = 0;
				$total_product_price = $qty * $price;
				$gross_amount = $gross_amount + $total_product_price;
			}
			$this->request->data['Order']['gross_value'] = $gross_amount;
			if (array_key_exists('save', $this->request->data)) {
				/*-------------------- Stock Checking : Start -------------------*/
				foreach ($order_product_array_for_stock_check as $product_id => $qty) {
					$punits_pre = $this->search_array($product_id, 'id', $product_list);
					$qty = $this->unit_convertfrombase($product_id, $punits_pre['sales_measurement_unit_id'], $qty);
					$stock_check = $this->stock_check($store_id, $product_id, $qty);
					if ($stock_check != 1) {
						$stock_available = 0;
						$msg_for_stock_unavailable = "Stock Not Available For <b>" . $punits_pre['name'] . '</b>';
						break;
					}
				}
				/*-------------------- Stock Checking : END --------------------*/
			}

			if ($stock_available == 0) {
				$this->Session->setFlash(__($msg_for_stock_unavailable), 'flash/error');
				$this->redirect(array('action' => 'create_order'));
			}
			$sales_person = $this->SalesPerson->find('list', array(
				'conditions' => array('territory_id' => $this->request->data['territory_id']),
				'order' => array('name' => 'asc')
			));

			$this->request->data['OrderProces']['sales_person_id'] = key($sales_person);

			$this->request->data['order_date'] = date('Y-m-d', strtotime($this->request->data['order_date']));


			$orderData['office_id'] = $this->request->data['office_id'];

			$orderData['territory_id'] = $this->request->data['territory_id'];
			$orderData['market_id'] = $market_id;
			$orderData['outlet_id'] = $this->request->data['distribut_outlet_id'];
			$orderData['entry_date'] = $this->current_datetime();
			$orderData['order_date'] = $this->request->data['order_date'];
			$order_no = $orderData['order_no'] = $this->request->data['order_no'];
			$orderData['gross_value'] = $this->request->data['Order']['gross_value'] - $this->request->data['Order']['total_discount'];
			$orderData['w_store_id'] = $this->request->data['w_store_id'];
			$orderData['is_active'] = 1;

			$orderData['order_time'] = $this->current_datetime();
			$orderData['order_reference_no'] = '';
			$orderData['sales_person_id'] = $this->request->data['OrderProces']['sales_person_id'];
			$orderData['from_app'] = 0;
			$orderData['action'] = 1;
			$orderData['confirmed'] = 1;
			$orderData['order_reference_no'] = '';


			$orderData['created_at'] = $this->current_datetime();
			$orderData['created_by'] = $this->UserAuth->getUserId();
			$orderData['updated_at'] = $this->current_datetime();
			$orderData['updated_by'] = $this->UserAuth->getUserId();


			$orderData['office_id'] = $office_id ? $office_id : 0;
			$orderData['thana_id'] = $thana_id ? $thana_id : 0;
			$orderData['total_discount'] = $this->request->data['Order']['total_discount'];

			$orderData['status'] = 2;
			$balance = 0;
			$limit = 0;
			$dist_balance_info = array();
			$dealer_balance_info = array();
			$dist_limit_info = array();
			$this->request->data['OrderProces']['is_active'] = 1;
			if (array_key_exists('draft', $this->request->data)) {
				$this->request->data['OrderProces']['status'] = 2;
				$orderData['confirm_status'] = $this->request->data['OrderProces']['confirm_status'] = 1;
				$message = "Order Has Been Saved as Draft";

				$is_execute = 0;
			} else {
				$message = "Order Has Been Saved";
				$this->request->data['OrderProces']['status'] = 2;
				$orderData['confirm_status'] = $this->request->data['OrderProces']['confirm_status'] = 2;
				$is_execute = 1;

				/*************************** Balance Check **************************************/
				$dist_balance_info = $this->DistDistributorBalance->find('first', array(
					'conditions' => array(
						'DistDistributorBalance.dist_distributor_id' => $distributor_id
					),
					'limit' => 1,
					'recursive' => -1
				));

				$dist_limit_info = $this->DistDistributorLimit->find('first', array(
					'conditions' => array(
						'DistDistributorLimit.office_id' => $office_id,
						'DistDistributorLimit.dist_distributor_id' => $distributor_id,
					),
					'limit' => 1,
					'recursive' => -1
				));

				if (empty($dist_balance_info)) {
					$this->Session->setFlash(__('Please Check the Balance of Distributor!!!'), 'flash/error');
					$this->redirect(array('action' => 'index'));
				}
				$credit_amount = $this->request->data['Order']['gross_value'] - $this->request->data['Order']['total_discount'];
				$dist_balance = $dist_balance_info['DistDistributorBalance']['balance'];
				if ($dist_balance < $credit_amount) {
					$this->Session->setFlash(__('Insufficient Balance of This Distributor!!!'), 'flash/error');
					$this->redirect(array('action' => 'index'));
				}

				/*if($dist_limit_info){
					$dist_balance =$dist_balance + $dist_limit_info['DistDistributorLimit']['max_amount'];
				}*/

				$dealer_balance_info = $this->DistDistributorBalanceHistory->find('first', array(
					'conditions' => array(
						'DistDistributorBalanceHistory.dist_distributor_id' => $distributor_id,

					),
					'order' => 'DistDistributorBalanceHistory.id DESC',
					'recursive' => -1
				));
				/*************************** Balance Check End *********************************************************/
			}
			//$orderData['status'] = $this->request->data['OrderProces']['status'];
			//$orderData['confirm_status'] = $this->request->data['OrderProces']['confirm_status'];
			if ($this->request->data['OrderProces']['requisition_type_id'] == 0) {
				$order_id = $this->request->data['OrderProces']['order_id'];
				$this->admin_delete($order_id, 0);
			}

			if ($this->Order->save($orderData)) {
				if ($this->request->data['OrderProces']['requisition_type_id'] == 0) {
					$order_info_arr = $this->Order->find('first', array(
						'conditions' => array(
							'Order.id' => $order_id
						)
					));
					$office_id = $order_info_arr['Order']['office_id'];
				} else {
					$order_id = $this->Order->getLastInsertId();
				}

				$this->loadModel('Store');
				$store_id_arr = $this->Store->find('first', array(
					'conditions' => array(
						'Store.office_id' => $office_id
					)
				));
				$store_id = $store_id_arr['Store']['id'];
				if ($order_id) {
					$all_product_id = $this->request->data['OrderDetail']['product_id'];
					if (!empty($this->request->data['OrderDetail'])) {
						$total_product_data = array();
						$order_details = array();

						foreach ($this->request->data['OrderDetail']['product_id'] as $key => $val) {
							if ($val == NULL) {
								continue;
							}

							$product_details = $this->Product->find('first', array(
								'fields' => array('id', 'is_virtual', 'parent_id'),
								'conditions' => array('Product.id' => $val),
								'recursive' => -1
							));

							//$measurement_unit_id = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
							$sales_price = $this->request->data['OrderDetail']['Price'][$key];
							if ($sales_price != 0 && !empty($sales_price)) {
								//pr($sales_price);


								if ($product_details['Product']['is_virtual'] == 1) {
									$product_id = $order_details['OrderDetail']['virtual_product_id'] = $val;
									$order_details['OrderDetail']['product_id'] = $product_details['Product']['parent_id'];
								} else {
									$order_details['OrderDetail']['virtual_product_id'] = 0;
									$product_id = $order_details['OrderDetail']['product_id'] = $product_details['Product']['id'];
								}


								$order_details['OrderDetail']['order_id'] = $order_id;
								$order_details['OrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
								$sales_price = $order_details['OrderDetail']['price'] = $this->request->data['OrderDetail']['Price'][$key];
								$order_qty = $order_details['OrderDetail']['sales_qty'] = $this->request->data['OrderDetail']['sales_qty'][$key];
								$sales_qty = $order_details['OrderDetail']['deliverd_qty'] = $this->request->data['OrderDetail']['deliverd_qty'][$key];
								$order_details['OrderDetail']['product_price_id'] = $this->request->data['OrderDetail']['product_price_id'][$key];
								$order_details['OrderDetail']['remaining_qty'] = $order_qty - $order_details['OrderDetail']['deliverd_qty'];
								$product_price_slab_id = 0;
								$order_details['OrderDetail']['product_price_id'] = $this->request->data['OrderDetail']['product_price_id'][$key];
								$order_details['OrderDetail']['product_combination_id'] = $this->request->data['OrderDetail']['combination_id'][$key];
								$order_details['OrderDetail']['bonus_qty'] = $this->request->data['OrderDetail']['bonus_product_qty'][$key];


								if ($this->request->data['OrderDetail']['bonus_product_id'][$key] != 0) {
									$b_p_id = $order_details['OrderDetail']['bonus_product_id'] = $this->request->data['OrderDetail']['bonus_product_id'][$key];
									$bonus_sales_qty = $this->request->data['OrderDetail']['bonus_product_qty'][$key];
									if (array_key_exists('save', $this->request->data)) {

										$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
										$product_list = Set::extract($products, '{n}.Product');

										$punits_pre = $this->search_array($b_p_id, 'id', $product_list);
										if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
											$bonus_base_quantity = $bonus_sales_qty;
										} else {
											$bonus_base_quantity = $this->unit_convert($b_p_id, $punits_pre['sales_measurement_unit_id'], $bonus_sales_qty);
										}

										$update_type = 'deduct';
										$this->update_current_inventory($bonus_base_quantity, $b_p_id, $store_id, $update_type, 11, date('Y-m-d'));
									}
								} else {
									$order_details['OrderDetail']['bonus_product_id'] = NULL;
								}

								//Start for bonus
								$order_date = date('Y-m-d', strtotime($this->request->data['order_date']));
								$bonus_product_id = $this->request->data['OrderDetail']['bonus_product_id'];
								$bonus_product_qty = $this->request->data['OrderDetail']['bonus_product_qty'];
								$order_details['OrderDetail']['bonus_id'] = 0;
								$order_details['OrderDetail']['bonus_scheme_id'] = 0;
								if ($bonus_product_qty[$key] > 0) {
									$b_product_id = $bonus_product_id[$key];
									$bonus_result = $this->bouns_and_scheme_id_set($b_product_id, $order_date);
									$order_details['OrderDetail']['bonus_id'] = $bonus_result['bonus_id'];
								}
								//End for bouns
								// Temp order Details
								/*$new_order_details['OrderDetail']=$OrderDetail_record[$product_id];
								if($order_details['OrderDetail']['deliverd_qty'] == 0){
									$new_order_details['OrderDetail']['remaining_qty']= $sales_qty;
								}
								else{
									$new_order_details['OrderDetail']['remaining_qty']= $order_details['OrderDetail']['remaining_qty'];
								}
								$newtempOrderDetails['TempOrderDetail']=$new_order_details['OrderDetail'];
								$temp_new_total_product_data[] = $newtempOrderDetails;

								$deliverd_qty=$OrderDetail_record[$product_id]['deliverd_qty'];
								if (array_key_exists('save', $this->request->data))
								{
									$new_order_details['OrderDetail']['deliverd_qty']=$order_details['OrderDetail']['deliverd_qty'];
								}else{
									$new_order_details['OrderDetail']['deliverd_qty']=$order_details['OrderDetail']['deliverd_qty'] + $deliverd_qty;
								}
								$new_order_details['OrderDetail']['order_id'] = $order_id;
								$new_total_product_data[] = $new_order_details;*/
								$order_details['OrderDetail']['discount_type'] = $this->request->data['OrderDetail']['disccount_type'][$key];
								$order_details['OrderDetail']['discount_amount'] = $this->request->data['OrderDetail']['discount_amount'][$key];
								$order_details['OrderDetail']['policy_type'] = $this->request->data['OrderDetail']['policy_type'][$key];
								$order_details['OrderDetail']['policy_id'] = $this->request->data['OrderDetail']['policy_id'][$key];

								$total_product_data[] = $order_details;
							} else {
								$sales_qty = $this->request->data['OrderDetail']['sales_qty'][$key];
								if (!empty($sales_qty)) {
									$bouns_order_details = array();
									if ($product_details['Product']['is_virtual'] == 1) {
										$product_id = $bouns_order_details['OrderDetail']['virtual_product_id'] = $val;
										$bouns_order_details['OrderDetail']['product_id'] = $product_details['Product']['parent_id'];
									} else {
										$bouns_order_details['OrderDetail']['virtual_product_id'] = 0;
										$product_id = $bouns_order_details['OrderDetail']['product_id'] = $product_details['Product']['id'];
									}
									$bouns_order_details['OrderDetail']['order_id'] = $order_id;
									$bouns_order_details['OrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
									$sales_price = $bouns_order_details['OrderDetail']['price'] = 0;
									$bouns_order_details['OrderDetail']['sales_qty'] = $sales_qty;
									$bouns_order_details['OrderDetail']['bonus_qty'] = $this->request->data['OrderDetail']['sales_qty'][$key];
									$bouns_order_details['OrderDetail']['bonus_product_id'] = $product_id;
									$bouns_order_details['OrderDetail']['is_bonus'] = 1;
									$order_date = date('Y-m-d', strtotime($this->request->data['order_date']));
									$bouns_order_details['OrderDetail']['deliverd_qty'] = $sales_qty;
									$bouns_order_details['OrderDetail']['discount_type'] = $this->request->data['OrderDetail']['disccount_type'][$key];
									$bouns_order_details['OrderDetail']['discount_amount'] = $this->request->data['OrderDetail']['discount_amount'][$key];
									$bouns_order_details['OrderDetail']['policy_type'] = $this->request->data['OrderDetail']['policy_type'][$key];
									$bouns_order_details['OrderDetail']['policy_id'] = $this->request->data['OrderDetail']['policy_id'][$key];
									$bouns_order_details['OrderDetail']['is_bonus'] = 1;
									if ($this->request->data['OrderDetail']['is_bonus'][$key] == 3)
										$bouns_order_details['OrderDetail']['is_bonus'] = 3;
									$selected_set = '';
									if (isset($this->request->data['OrderDetail']['selected_set'][$bouns_order_details['OrderDetail']['policy_id']])) {
										$selected_set = $this->request->data['OrderDetail']['selected_set'][$bouns_order_details['OrderDetail']['policy_id']];
									}
									if ($selected_set) {
										$other_info = array(
											'selected_set' => $selected_set
										);
									}
									if ($other_info)
										$bouns_order_details['OrderDetail']['other_info'] = json_encode($other_info);
									$total_product_data[] = $bouns_order_details;
								}
							}
							if (array_key_exists('save', $this->request->data)) {

								$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
								$product_list = Set::extract($products, '{n}.Product');

								$punits_pre = $this->search_array($product_id, 'id', $product_list);
								$measurement_unit_id = $this->request->data['OrderDetail']['measurement_unit_id'][$key] ? $this->request->data['OrderDetail']['measurement_unit_id'][$key] : $punits_pre['sales_measurement_unit_id'];

								if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
									$base_quantity = $sales_qty;
								} else {
									$base_quantity = $this->unit_convert($product_id, $measurement_unit_id, $sales_qty);
								}

								$update_type = 'deduct';
								$this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 11, date('Y-m-d'));
							}
						}
						/*if (array_key_exists('draft', $this->request->data))
						{
							$this->loadModel('TempOrderDetail');
							$this->TempOrderDetail->create();
							$this->TempOrderDetail->saveAll($temp_new_total_product_data);
						}
						else{
							$this->loadModel('TempOrderDetail');
							$this->TempOrderDetail->deleteAll(array('TempOrderDetail.order_id' => $order_id));
						}*/
						$this->OrderDetail->saveAll($total_product_data);
					}
				}

				/************ Memo create date: 04-09-2019 *****************/
				/***************Memo Create for no Confirmation Type **************/
				$this->loadModel('Memo');
				$this->loadModel('OrderDetail');
				$this->loadModel('Order');
				$this->loadModel('Collection');
				$order_info = $this->Order->find('first', array(
					'conditions' => array('Order.id' => $order_id),
					'recursive' => -1
				));

				$order_detail_info = $this->OrderDetail->find('all', array(
					'conditions' => array('OrderDetail.order_id' => $order_id),
					'recursive' => -1
				));

				$memo = array();
				$memo['sales_person_id'] = $order_info['Order']['sales_person_id'];
				$memo['entry_date'] = $this->current_datetime();
				$memo['memo_date'] =  date('Y-m-d');

				$memo['office_id'] = $order_info['Order']['office_id'];
				$memo['sale_type_id'] = 1;
				$memo['territory_id'] = $order_info['Order']['territory_id'];
				$memo['thana_id'] = $order_info['Order']['thana_id'];
				$memo['market_id'] = $order_info['Order']['market_id'];
				$memo['outlet_id'] = $order_info['Order']['outlet_id'];

				$memo['memo_no'] = $order_info['Order']['order_no'];
				$memo['gross_value'] = $order_info['Order']['gross_value'];
				$memo['cash_recieved'] = $order_info['Order']['gross_value'];
				$memo['is_active'] = $order_info['Order']['is_active'];
				$memo['is_distributor'] = 1;
				$memo['w_store_id'] = $order_info['Order']['w_store_id'];
				if (array_key_exists('save', $this->request->data)) {
					$memo['status'] = 2;
				} else {
					$memo['status'] = 0;
					$this->Memo->create();
				}
				$memo['memo_time'] = $this->current_datetime();
				$memo['sales_person_id'] = $order_info['Order']['sales_person_id'];
				$memo['from_app'] = 0;
				$memo['action'] = 1;
				$memo['is_distributor'] = 1;
				$memo['is_program'] = 0;


				$memo['memo_reference_no'] = $order_info['Order']['memo_reference_no'];

				$memo['created_at'] = $this->current_datetime();
				$memo['created_by'] = $this->UserAuth->getUserId();
				$memo['updated_at'] = $this->current_datetime();
				$memo['updated_by'] = $this->UserAuth->getUserId();

				$memo['total_discount'] = $order_info['Order']['total_discount'];
				/*if (array_key_exists('save', $this->request->data))
				{
					$this->loadModel('Memo');
					$memos=$this->Memo->find('first',array('conditions'=>array('Memo.memo_no'=>$order_no),'order'=>'Memo.id DESC'));
					$memo_id= $memos['Memo']['id'];
					$this->admin_deletememo($memo_id,0);
				}*/

				if ($this->Memo->save($memo)) {

					$memo_id = $this->Memo->getLastInsertId();
					$memo_info_arr = $this->Memo->find('first', array(
						'conditions' => array(
							'Memo.id' => $memo_id
						)
					));

					if ($memo_id) {
						if (!empty($order_detail_info[0]['OrderDetail'])) {
							$this->loadModel('MemoDetail');
							$total_product_data = array();
							$memo_details = array();
							$bonus_memo_details = array();

							foreach ($order_detail_info as $order_detail_result) {
								if ($order_detail_result['OrderDetail']['deliverd_qty'] > 0) {

									$product_id = $order_detail_result['OrderDetail']['product_id'];
									$virtual_product_id = $order_detail_result['OrderDetail']['virtual_product_id'];

									if ($virtual_product_id > 0) {
										$product_id = $virtual_product_id;
									}


									$product_details = $this->Product->find('first', array(
										'fields' => array('id', 'is_virtual', 'parent_id'),
										'conditions' => array('Product.id' => $product_id),
										'recursive' => -1
									));

									if ($product_details['Product']['is_virtual'] == 1) {
										$memo_details['MemoDetail']['virtual_product_id'] = $product_id;
										$memo_details['MemoDetail']['product_id'] = $product_details['Product']['parent_id'];
									} else {
										$memo_details['MemoDetail']['virtual_product_id'] = 0;
										$memo_details['MemoDetail']['product_id'] = $product_details['Product']['id'];
									}
									// $memo_details['MemoDetail']['product_id'] = $product_id;

									$memo_details['MemoDetail']['memo_id'] = $memo_id;
									$memo_details['MemoDetail']['measurement_unit_id'] = $order_detail_result['OrderDetail']['measurement_unit_id'];
									$memo_details['MemoDetail']['actual_price'] = $order_detail_result['OrderDetail']['price'];
									$memo_details['MemoDetail']['price'] = $order_detail_result['OrderDetail']['price'] - $order_detail_result['OrderDetail']['discount_amount'];
									$memo_details['MemoDetail']['sales_qty'] = $order_detail_result['OrderDetail']['deliverd_qty'];

									$memo_details['MemoDetail']['product_price_id'] = $order_detail_result['OrderDetail']['product_price_id'];
									$memo_details['MemoDetail']['bonus_qty'] = NULL;
									$memo_details['MemoDetail']['offer_id'] = $order_detail_result['OrderDetail']['offer_id'];
									$memo_details['MemoDetail']['bonus_product_id'] = NULL;
									$memo_details['MemoDetail']['bonus_id'] = NULL;
									$memo_details['MemoDetail']['bonus_scheme_id'] = NULL;
									$memo_details['MemoDetail']['price_combination_id'] = $order_detail_result['OrderDetail']['price_combination_id'];
									$memo_details['MemoDetail']['product_combination_id'] = $order_detail_result['OrderDetail']['product_combination_id'];
									$memo_details['MemoDetail']['is_bonus'] = $order_detail_result['OrderDetail']['is_bonus'];
									$memo_details['MemoDetail']['deliverd_qty'] = $order_detail_result['OrderDetail']['deliverd_qty'];

									$memo_details['MemoDetail']['discount_type'] = $order_detail_result['OrderDetail']['discount_type'];
									$memo_details['MemoDetail']['discount_amount'] = $order_detail_result['OrderDetail']['discount_amount'];
									$memo_details['MemoDetail']['policy_type'] = $order_detail_result['OrderDetail']['policy_type'];
									$memo_details['MemoDetail']['policy_id'] = $order_detail_result['OrderDetail']['policy_id'];
									$memo_details['MemoDetail']['is_bonus'] = 0;
									if ($order_detail_result['OrderDetail']['is_bonus'] == 3)
										$memo_details['MemoDetail']['is_bonus'] = 3;

									$total_product_data[] = $memo_details;

									if ($order_detail_result['OrderDetail']['bonus_qty'] > 0 && $order_detail_result['OrderDetail']['is_bonus'] == 0) {

										$product_id = $order_detail_result['OrderDetail']['bonus_product_id'];

										$bproduct_details = $this->Product->find('first', array(
											'fields' => array('id', 'is_virtual', 'parent_id'),
											'conditions' => array('Product.id' => $product_id),
											'recursive' => -1
										));

										if ($bproduct_details['Product']['is_virtual'] == 1) {
											$bonus_memo_details['MemoDetail']['virtual_product_id'] = $product_id;
											$bonus_memo_details['MemoDetail']['product_id'] = $bproduct_details['Product']['parent_id'];
										} else {
											$bonus_memo_details['MemoDetail']['virtual_product_id'] = 0;
											$bonus_memo_details['MemoDetail']['product_id'] = $bproduct_details['Product']['id'];
										}

										$bonus_memo_details['MemoDetail']['memo_id'] = $memo_id;
										//$bonus_memo_details['MemoDetail']['product_id'] = $product_id;
										$bonus_memo_details['MemoDetail']['measurement_unit_id'] = $order_detail_result['OrderDetail']['measurement_unit_id'];
										$bonus_memo_details['MemoDetail']['price'] = 0;
										$bonus_memo_details['MemoDetail']['sales_qty'] = $order_detail_result['OrderDetail']['bonus_qty'];

										$bonus_memo_details['MemoDetail']['product_price_id'] = $order_detail_result['OrderDetail']['product_price_id'];
										$bonus_memo_details['MemoDetail']['bonus_qty'] = $order_detail_result['OrderDetail']['bonus_qty'];
										$bonus_memo_details['MemoDetail']['offer_id'] = $order_detail_result['OrderDetail']['offer_id'];
										$bonus_memo_details['MemoDetail']['bonus_product_id'] = $order_detail_result['OrderDetail']['bonus_product_id'];
										$bonus_memo_details['MemoDetail']['bonus_id'] = $order_detail_result['OrderDetail']['bonus_id'];
										$bonus_memo_details['MemoDetail']['bonus_scheme_id'] = $order_detail_result['OrderDetail']['bonus_scheme_id'];
										$bonus_memo_details['MemoDetail']['price_combination_id'] = $order_detail_result['OrderDetail']['price_combination_id'];
										$bonus_memo_details['MemoDetail']['product_combination_id'] = $order_detail_result['OrderDetail']['product_combination_id'];
										$bonus_memo_details['MemoDetail']['is_bonus'] = 1;
										$bonus_memo_details['MemoDetail']['deliverd_qty'] = $order_detail_result['OrderDetail']['bonus_qty'];

										$total_product_data[] = $bonus_memo_details;
									}
								}
							}

							$this->MemoDetail->saveAll($total_product_data);
						}
						$order_outlet_id = $order_info['Order']['outlet_id'];
						$this->loadmodel('DistOutletMap');
						$outlet_info = $this->DistOutletMap->find('first', array(
							'conditions' => array('DistOutletMap.outlet_id' => $order_outlet_id),
							'fields' => array('DistOutletMap.dist_distributor_id'),
						));
						$distibuter_id = $outlet_info['DistOutletMap']['dist_distributor_id'];
						$this->loadmodel('DistStore');
						$dist_store_id = $this->DistStore->find('first', array(
							'conditions' => array('DistStore.dist_distributor_id' => $distibuter_id),
							'fields' => array('DistStore.id'),
						));
						/************* create Challan *************/

						if (array_key_exists('save', $this->request->data)) {

							/*****************Create Chalan and *****************/
							$this->loadModel('DistChallan');
							$this->loadModel('DistChallanDetail');
							$this->loadModel('CurrentInventory');
							//$company_id  =$this->Session->read('Office.company_id');
							$office_id  = $this->request->data['office_id'];
							$store_id = $this->request->data['w_store_id'];
							//$challan['company_id']=$company_id;
							$challan['office_id'] = $office_id;
							$challan['memo_id'] = $memo_info_arr['Memo']['id'];
							$challan['memo_no'] = $memo_info_arr['Memo']['memo_no'];
							$challan['challan_no'] = $memo_info_arr['Memo']['memo_no'];
							$challan['receiver_dist_store_id'] = $dist_store_id['DistStore']['id'];
							$challan['receiving_transaction_type'] = 2;
							$challan['received_date'] = '';
							$challan['challan_date'] = date('Y-m-d');
							$challan['dist_distributor_id'] = $distibuter_id;
							$challan['challan_referance_no'] = '';
							$challan['challan_type'] = "";
							$challan['remarks'] = 0;
							$challan['status'] = 1;
							$challan['so_id'] = $order_info['Order']['sales_person_id'];
							$challan['is_close'] = 0;
							$challan['inventory_status_id'] = 2;
							$challan['transaction_type_id'] = 2;
							$challan['sender_store_id'] = $store_id;
							$challan['created_at'] = $this->current_datetime();
							$challan['created_by'] = $this->UserAuth->getUserId();
							$challan['updated_at'] = $this->current_datetime();
							$challan['updated_by'] = $this->UserAuth->getUserId();
							//pr();die();
							$this->DistChallan->create();
							// pr($challan);
							if ($this->DistChallan->save($challan)) {

								$challan_id = $this->DistChallan->getLastInsertId();
								if ($challan_id) {

									$challan_no = 'Ch-' . $distibuter_id . '-' . date('Y') . '-' . $challan_id;

									$challan_data['id'] = $challan_id;
									$challan_data['challan_no'] = $challan_no;

									$this->DistChallan->save($challan_data);
								}
								$product_list = $this->request->data['OrderDetail'];
								//pr($product_list);
								if (!empty($product_list['product_id'])) {
									$data_array = array();

									foreach ($product_list['product_id'] as $key => $val) {
										if ($product_list['product_id'][$key] != '') {

											if ($product_list['Price'][$key] != 0 && !empty($product_list['Price'][$key])) {

												if ($product_list['deliverd_qty'][$key] > 0) {
													if (!empty($val)) {
														$inventories = $this->CurrentInventory->find('first', array(
															'conditions' => array(
																'CurrentInventory.product_id' => $val,
																'CurrentInventory.store_id' => $store_id,
															),
															'recursive' => -1,
														));
														$batch_no = $inventories['CurrentInventory']['batch_number'];

														$product_details = $this->Product->find('first', array(
															'fields' => array('id', 'is_virtual', 'parent_id'),
															'conditions' => array('Product.id' => $val),
															'recursive' => -1
														));

														if ($product_details['Product']['is_virtual'] == 1) {
															$data['DistChallanDetail']['virtual_product_id'] = $val;
															$data['DistChallanDetail']['product_id'] = $product_details['Product']['parent_id'];
														} else {
															$data['DistChallanDetail']['virtual_product_id'] = 0;
															$data['DistChallanDetail']['product_id'] = $product_details['Product']['id'];
														}
														//$data['DistChallanDetail']['product_id'] = $val;

														$data['DistChallanDetail']['challan_id'] = $this->DistChallan->id;

														$data['DistChallanDetail']['measurement_unit_id'] = $product_list['measurement_unit_id'][$key];
														$data['DistChallanDetail']['challan_qty'] = $product_list['deliverd_qty'][$key];
														$data['DistChallanDetail']['received_qty'] = $product_list['deliverd_qty'][$key];
														$data['DistChallanDetail']['batch_no'] = $batch_no;
														//$data['DistChallanDetail']['remaining_qty'] =$product_list['remaining_qty'][$key];
														$data['DistChallanDetail']['price'] = $product_list['Price'][$key];
														/* if(!empty($product_list['bonus_product_id'][$key])){
													$data['DistChallanDetail']['is_bonus'] = 1;
													}else{*/
														$data['DistChallanDetail']['is_bonus'] = 0;
														//}

														$data['DistChallanDetail']['source'] = "";
														$data['DistChallanDetail']['remarks'] = $this->request->data['OrderDetail']['remarks'][$key];

														//$data['ChallanDetail']['expire_date'] = (($this->request->data['expire_date'][$key] != '' ) ? Date('Y-m-d', strtotime($this->request->data['expire_date'][$key])) : Null);
														$date = (($this->request->data['order_date'][$key] != ' ' && $this->request->data['order_date'][$key] != 'null' && $this->request->data['order_date'][$key] != '') ? explode('-', $this->request->data['order_date'][$key]) : '');
														if (!empty($date[1])) {
															$date[0] = date('m', strtotime($date[0]));
															$a_date = date('y-m-d', mktime(0, 0, 0, $date[0], 1, $date[1]));
															$data['DistChallanDetail']['expire_date'] = date("Y-m-t", strtotime($a_date));
														} else {
															$data['DistChallanDetail']['expire_date'] = '';
														}
														$data['DistChallanDetail']['inventory_status_id'] = 1;  // set 1 for Sound product
														//$data['ChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
													}
													$data_array[] = $data;
													if ($product_list['bonus_product_id'][$key] != 0) {

														$inventories = $this->CurrentInventory->find('first', array(
															'conditions' => array(
																'CurrentInventory.product_id' => $product_list['bonus_product_id'][$key],
																'CurrentInventory.store_id' => $store_id,
															),
															'recursive' => -1,
														));
														$batch_no = $inventories['CurrentInventory']['batch_number'];
														$bonus_data['DistChallanDetail']['batch_no'] = $batch_no;
														$bonus_data['DistChallanDetail']['challan_id'] = $this->DistChallan->id;
														$bonus_data['DistChallanDetail']['product_id'] = $product_list['bonus_product_id'][$key];
														$bonus_data['DistChallanDetail']['measurement_unit_id'] = $product_list['bonus_measurement_unit_id'][$key];
														$bonus_data['DistChallanDetail']['challan_qty'] = $product_list['bonus_product_qty'][$key];
														$bonus_data['DistChallanDetail']['received_qty'] = $product_list['bonus_product_qty'][$key];
														$bonus_data['DistChallanDetail']['price'] = 0;
														$bonus_data['DistChallanDetail']['is_bonus'] = 1;

														$data_array[] = $bonus_data;
													}
												}
											} else {
												if (!empty($product_list['sales_qty'][$key])) {
													$inventories = $this->CurrentInventory->find('first', array(
														'conditions' => array(
															'CurrentInventory.product_id' => $val,
															'CurrentInventory.store_id' => $store_id,
														),
														'recursive' => -1,
													));
													$bonus_data = array();
													$batch_no = $inventories['CurrentInventory']['batch_number'];
													$bonus_data['DistChallanDetail']['challan_id'] = $this->DistChallan->id;


													$product_details = $this->Product->find('first', array(
														'fields' => array('id', 'is_virtual', 'parent_id'),
														'conditions' => array('Product.id' => $val),
														'recursive' => -1
													));

													if ($product_details['Product']['is_virtual'] == 1) {
														$bonus_data['DistChallanDetail']['virtual_product_id'] = $val;
														$bonus_data['DistChallanDetail']['product_id'] = $product_details['Product']['parent_id'];
													} else {
														$bonus_data['DistChallanDetail']['virtual_product_id'] = 0;
														$bonus_data['DistChallanDetail']['product_id'] = $product_details['Product']['id'];
													}

													//$bonus_data['DistChallanDetail']['product_id'] = $val;

													$bonus_data['DistChallanDetail']['challan_qty'] = $product_list['sales_qty'][$key];
													$bonus_data['DistChallanDetail']['received_qty'] = $product_list['sales_qty'][$key];
													$bonus_data['DistChallanDetail']['batch_no'] = $batch_no;
													$bonus_data['DistChallanDetail']['measurement_unit_id'] = $product_list['measurement_unit_id'][$key];
													$bonus_data['DistChallanDetail']['price'] = $product_list['Price'][$key];
													$bonus_data['DistChallanDetail']['is_bonus'] = 1;

													$data_array[] = $bonus_data;
												}
											}
										}
									}
									//pr($data_array);die();
									$this->DistChallanDetail->saveAll($data_array);
								}
							}
						}
						//die();
						/************* end Challan *************/
						/**************** Balance Deduct *******************/
						if (array_key_exists('save', $this->request->data)) {
							if ($dist_balance_info) {
								$balance = $dist_balance - $credit_amount;
							} else {
								$balance = 0;
							}
							if ($balance < 1) {
								if ($dist_limit_info) {
									$dist_limit_data['DistDistributorLimit']['id'] =  $dist_limit_info['DistDistributorLimit']['id'];
									$dist_limit_data['DistDistributorLimit']['max_amount'] = $dist_limit_info['DistDistributorLimit']['max_amount'] - $balance;
									$limit_data_history = array();

									if ($this->DistDistributorLimit->save($dist_limit_data)) {

										$limit_data_history['dist_distributor_limit_id'] = $dist_limit_info['DistDistributorLimit']['id'];
										$limit_data_history['max_amount'] = $dist_limit_info['DistDistributorLimit']['max_amount'] - $balance;
										$limit_data_history['transaction_amount'] = $balance * (-1);
										$limit_data_history['transaction_type'] = 0;
										$limit_data_history['is_active'] = 1;

										$this->DistDistributorLimitHistory->create();
										$this->DistDistributorLimitHistory->save($limit_data_history);

										$balance = 0;
									}
								}
							}

							$dealer_balance_data = array();
							$dealer_balance = array();
							$dealer_balance['id'] = $dist_balance_info['DistDistributorBalance']['id'];
							$dealer_balance['office_id'] = $dist_balance_info['DistDistributorBalance']['office_id'];
							$dealer_balance['dist_distributor_id'] = $dist_balance_info['DistDistributorBalance']['dist_distributor_id'];
							$dealer_balance['balance'] = $balance;

							if ($this->DistDistributorBalance->save($dealer_balance)) {

								$dealer_balance_data['dist_distributor_id'] = $distributor_id;
								$dealer_balance_data['dist_distributor_balance_id'] = $dist_balance_info['DistDistributorBalance']['id'];
								$dealer_balance_data['office_id'] = $this->request->data['office_id'];
								$dealer_balance_data['memo_value'] = $this->request->data['Order']['gross_value'];
								$dealer_balance_data['balance'] = $balance;
								$dealer_balance_data['balance_type'] = 2;
								$dealer_balance_data['balance_transaction_type_id'] = 2;
								$dealer_balance_data['transaction_amount'] = $this->request->data['Order']['gross_value'];
								$dealer_balance_data['transaction_date'] = date('Y-m-d');
								$dealer_balance_data['created_at'] = $this->current_datetime();
								$dealer_balance_data['created_by'] = $this->UserAuth->getUserId();
								$dealer_balance_data['updated_at'] = $this->current_datetime();
								$dealer_balance_data['updated_by'] = $this->UserAuth->getUserId();
								$this->DistDistributorBalanceHistory->create();
								$this->DistDistributorBalanceHistory->save($dealer_balance_data);
							}
						}
						/**************** end Balance Deduct *******************/
					}
					//start collection crate
					$collection_data = array();
					$collection_data['memo_id'] = $memo_info_arr['Memo']['id'];
					$collection_data['memo_no'] = $memo_info_arr['Memo']['memo_no'];
					$collection_data['so_id'] = $memo_info_arr['Memo']['sales_person_id'];

					$collection_data['memo_value'] = $memo_info_arr['Memo']['gross_value'];
					$collection_data['credit_or_due'] = $memo_info_arr['Memo']['credit_amount'];
					$collection_data['memo_date'] = $memo_info_arr['Memo']['memo_date'];
					$collection_data['type'] = 1;
					$collection_data['instrument_type'] = $order_info['Order']['instrument_type'];
					$collection_data['collectionAmount'] = $memo_info_arr['Memo']['gross_value'];
					$collection_data['collectionDate'] = date('Y-m-d');
					$collection_data['created_at'] = $this->current_datetime();

					$collection_data['territory_id'] = $memo_info_arr['Memo']['territory_id'];
					$collection_data['outlet_id'] = $memo_info_arr['Memo']['outlet_id'];
					$collection_data['market_id'] = $memo_info_arr['Memo']['market_id'];
					$collection_data['office_id'] = $memo_info_arr['Memo']['office_id'];

					$this->Collection->create();
					$this->Collection->save($collection_data);

					//end collection careate    

				}
			}
			//if (array_key_exists('save', $this->request->data)){
			$this->redirect(array('action' => 'index'));
			/*}
			else{
				$this->redirect(array('action' => 'edit',$id));
			}*/
		}
	}


	/**
	 * admin_edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_edit_backup_12_09_2019($id = null)
	{


		ini_set('memory_limit', '-1');
		// $instrumentType=array(0=>"Cash",1=>"Cheque");

		$this->loadModel('InstrumentType');
		$instrumenttype_condition = array(
			"NOT" => array("id" => array(2, 10, 11))
		);
		$instrumentType = $this->InstrumentType->find('list', array('conditions' => $instrumenttype_condition));

		$this->set(compact('instrumentType'));
		$this->loadModel('ProductCombination');
		$this->loadModel('Combination');
		$this->Session->delete('cart_session_data');
		$this->Session->delete('matched_session_data');
		$this->Session->delete('combintaion_qty_data');

		$this->loadModel('Product');

		/* $this->loadModel('Company');
		$companies = $this->Company->find('list', array());
		$this->set(compact('companies'));*/

		$current_date = date('d-m-Y', strtotime($this->current_date()));

		$this->loadModel('Product');
		$company_id = $this->Session->read('Office.company_id');
		$user_group_id = $this->Session->read('Office.group_id');

		/* ----- start code of product list ----- */
		$maintain_dealer_type = $this->Session->read('Office.company_info.Company.maintain_dealer');
		if ($user_group_id == 1) {
			$product_list = $this->Product->find('list', array('order' => array('order' => 'asc')));
		} else {
			$product_list = $this->Product->find('list', array(
				'conditions' => array('company_id' => $company_id),
				'order' => array('order' => 'asc')
			));
			if ($user_group_id == 2) {
				if ($maintain_dealer_type == 1) {
					$distributers_list = $this->getDistributorListWithName($company_id);
					foreach ($distributers_list as $key => $value) {
						$distributers[$value['id']] = $value['name'];
					}
					//pr($distributers_list);
					//pr($distributers);die();
					$this->set(compact('distributers'));
				} else {

					$this->loadModel('Outlet');
					$office_outlets = $this->Outlet->find('list', array('conditions' => array('company_id' => $company_id)));
					$this->loadModel('Market');
					$market_list = $this->Market->find('list', array('conditions' => array('company_id' => $company_id)));
				}
				//$offices=$this->Office->find('');
			} else {
				if ($maintain_dealer_type == 1) {
					$distributers_list = $this->getDistributorListWithName($company_id);
					foreach ($distributers_list as $key => $value) {
						$distributers[$value['id']] = $value['name'];
					}
					$this->set(compact('distributers'));
				} else {

					$this->loadModel('Outlet');
					$office_outlets = $this->Outlet->find('list', array('conditions' => array('company_id' => $company_id)));
					$this->loadModel('Market');
					$market_list = $this->Market->find('list', array('conditions' => array('company_id' => $company_id)));
					//pr($outlets);die(); 
				}
			}
		}

		$this->set(compact('office_outlets'));
		$this->set(compact('market_list'));
		$this->set(compact('product_list'));

		/* ------- start get edit data -------- */
		$this->Order->recursive = 1;
		$options = array(
			'conditions' => array('Order.id' => $id)
		);

		$existing_record = $this->Order->find('first', $options);
		//pr( $existing_record);die();
		$this->loadModel('CurrentInventory');
		/* $stoks=$this->CurrentInventory->find('all',array(
				'conditions'=>array(
					'CurrentInventory.store_id'=>$existing_record['Order']['w_store_id']
				),
				'fields'=>array('Product.id','Product.name','CurrentInventory.qty'),
				
			));*/
		// pr($stoks);die();
		$msg = "";
		$canPermitted = 1;
		$not_in_stock_product = '';
		if (!empty($existing_record['OrderDetail'])) {
			foreach ($existing_record['OrderDetail'] as $key => $value) {
				$OrderDetail_record[$value['product_id']] = $value;
				$stoks = $this->CurrentInventory->find('first', array(
					'conditions' => array(
						'CurrentInventory.store_id' => $existing_record['Order']['w_store_id'],
						'CurrentInventory.product_id' => $value['product_id']
					),
					'fields' => array('Product.id', 'Product.name', 'CurrentInventory.qty'),
				));
				//pr($value);
				if (empty($stoks)) {
					$empty_stoks = $value;
					$canPermitted = 0;
					$productName = $this->Product->find('first', array(
						'conditions' => array('id' => $value['product_id']),
						'fields' => array('id', 'name'),
						'recursive' => -1
					));
					$msg = $msg . $productName['Product']['name'] . " Is not Available!!! ";
					$not_in_stock_product['Product']['id'] = $productName['Product']['id'];
					$msg = $msg . "<br>";
				} elseif (!empty($stoks)) {
					if ($stoks['CurrentInventory']['qty'] < $value['sales_qty']) {
						$canPermitted = 0;
						$not_in_stock_product['Product']['id'] = $stoks['Product']['id'];
						$msg = $msg . $stoks['Product']['name'] . " Is Insufficient!!! ";
						$msg = $msg . "<br>";
					}
				}
			}
		}
		//pr($OrderDetail_record);die();
		//pr($not_in_stock_product);die();
		if ($canPermitted == 0) {
			$this->Session->setFlash(__($msg), 'flash/warning');
		}
		$this->set(compact('canPermitted'));
		$this->set(compact('not_in_stock_product'));

		//pr($existing_record);//die();
		/*if(!empty($existing_record['OrderDetail'])){
			foreach ($existing_record['OrderDetail'] as $key => $value) {
				$Order_products[$key]=$value['product_id'];
			}
		}
		
		$this->loadModel('CurrentInventory');
	   
		 $stoks=$this->CurrentInventory->find('all',array(
				'conditions'=>array(
					'CurrentInventory.store_id'=>$existing_record['Order']['w_store_id'],
					'CurrentInventory.product_id IN'=>$Order_products
				),
				'recursive'=>-1
			));
		 $i=0;
		 //pr($stoks);die();
		foreach ($existing_record['OrderDetail'] as $key => $value) {
			if($stoks[$i]['CurrentInventory']['product_id'] != $value['product_id'] ){
					pr($value);  
					$i = $i+1;
			}
		}
		die();*/
		$details_data = array();
		foreach ($existing_record['OrderDetail'] as $detail_val) {
			$product = $detail_val['product_id'];
			$this->Combination->unbindModel(
				array('hasMany' => array('ProductCombination'))
			);
			$combination_list = $this->Combination->find('all', array(
				'conditions' => array('ProductCombination.product_id' => $product),
				'joins' => array(
					array(
						'alias' => 'ProductCombination',
						'table' => 'product_combinations',
						'type' => 'INNER',
						'conditions' => 'Combination.id = ProductCombination.combination_id'
					)
				),
				'fields' => array('Combination.all_products_in_combination'),
				'limit' => 1
			));
			if (!empty($combination_list)) {
				$combined_product = $combination_list[0]['Combination']['all_products_in_combination'];
				$detail_val['combined_product'] = $combined_product;
			}
			$details_data[] = $detail_val;
		}

		$existing_record['OrderDetail'] = $details_data;

		$this->loadModel('MeasurementUnit');
		for ($i = 0; $i < count($details_data); $i++) {
			$measurement_unit_id = $details_data[$i]['measurement_unit_id'];
			$measurement_unit_name = $this->MeasurementUnit->find('all', array(
				'conditions' => array('MeasurementUnit.id' => $measurement_unit_id),
				'fields' => array('name'),
				'recursive' => -1
			));
			$existing_record['OrderDetail'][$i]['measurement_unit_name'] = $measurement_unit_name[0]['MeasurementUnit']['name'];
		}

		$existing_record['office_id'] = $existing_record['SalesPerson']['office_id'];
		$existing_record['territory_id'] = $existing_record['Order']['territory_id'];
		$existing_record['market_id'] = $existing_record['Order']['market_id'];
		$existing_record['outlet_id'] = $existing_record['Order']['outlet_id'];
		$existing_record['order_time'] = date('d-m-Y', strtotime($existing_record['Order']['order_time']));
		$existing_record['order_date'] = date('d-m-Y', strtotime($existing_record['Order']['order_date']));
		$existing_record['order_no'] = $existing_record['Order']['order_no'];
		$existing_record['memo_reference_no'] = $existing_record['Order']['memo_reference_no'];

		$existing_record['order_reference_no'] = $existing_record['Order']['order_reference_no'];
		$existing_record['reference_number'] = $existing_record['Order']['instrument_reference_no'];
		$existing_record['instrumentType_id'] = $existing_record['Order']['instrument_type'];

		//pr($existing_record);die();
		$this->LoadModel('Product');
		$this->LoadModel('Store');
		$store_id = $this->Store->find('first', array(
			'fields' => array('Store.id'),
			'conditions' => array('Store.id' => $existing_record['Order']['w_store_id']),
			'recursive' => -1
		));

		$open_bonus_product = $this->Product->find('all', array(
			'fields' => array('Product.id', 'Product.name'),
			'joins' => array(
				array(
					'table' => 'current_inventories',
					'alias' => 'CurrentInventory',
					'type' => 'Inner',
					'conditions' => 'CurrentInventory.product_id=Product.id'
				),
				array(
					'table' => 'open_combination_products',
					'alias' => 'OpenCombinationProduct',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.product_id=Product.id'
				),
				array(
					'table' => 'open_combinations',
					'alias' => 'OpenCombination',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.combination_id=OpenCombination.id'
				),

			),
			'conditions' => array(
				'CurrentInventory.qty >' => 0,
				'CurrentInventory.store_id' => $store_id['Store']['id'],
				'OpenCombination.start_date <=' => date('Y-m-d'),
				'OpenCombination.end_date >=' => date('Y-m-d'),
				'OpenCombination.is_bonus' => 1
			),
			'recursive' => -1
		));
		$open_bonus_product_option = array();
		foreach ($open_bonus_product as $bonus_product) {
			$open_bonus_product_option[$bonus_product['Product']['id']] = $bonus_product['Product']['name'];
		}

		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($user_group_id == 1) {
			$office_conditions = array();
		} elseif ($user_group_id == 2) {
			$office_conditions = array('Office.company_id' => $company_id, 'Office.office_type_id' => 2);
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}

		// $this->set('office_id', $existing_record['office_id']);
		$this->loadModel('Office');
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

		$office_id = $existing_record['office_id'];
		$territory_id = $existing_record['territory_id'];
		$market_id = $existing_record['market_id'];
		$outlets = array();


		$this->loadModel('Territory');
		$territories_list = $this->Territory->find('all', array(
			'conditions' => array('Territory.office_id' => $office_id),
			'order' => array('Territory.name' => 'asc')
		));
		$territories = array();
		foreach ($territories_list as $t_result) {
			$territories[$t_result['Territory']['id']] = $t_result['Territory']['name'] . ' (' . $t_result['SalesPerson']['name'] . ')';
		}


		$territory_ids = array($territory_id);


		$markets = $this->Market->find('list', array(
			'conditions' => array('territory_id' => $territory_ids),
			'order' => array('name' => 'asc')
		));


		//$company_id=$existing_record['Order']['company_id'];
		$outlets = $this->get_outlet_list_with_distributor_name($company_id);
		//pr($outlets);die();
		$this->loadModel('Store');
		$this->loadModel('Product');
		$this->loadModel('CurrentInventory');
		$store_info = $this->Store->find('first', array(
			'conditions' => array(
				'Store.id' => $existing_record['Order']['w_store_id']
			),
			'recursive' => -1
		));
		// pr($store_info);die();
		$store_id = $store_info['Store']['id'];
		// pr($existing_record['OrderDetail']);die();
		foreach ($existing_record['OrderDetail'] as $key => $single_product) {



			$total_qty_arr = $this->CurrentInventory->find('all', array(
				'conditions' => array('store_id' => $store_id, 'product_id' => $single_product['product_id']),
				'fields' => array('sum(qty) as total'),
				'recursive' => -1
			));

			$total_qty = $total_qty_arr[0][0]['total'];

			$sales_total_qty = $this->unit_convertfrombase($single_product['product_id'], $single_product['measurement_unit_id'], $total_qty);

			$existing_record['OrderDetail'][$key]['stock_qty'] = $sales_total_qty;
		}


		$products_from_ci = $this->CurrentInventory->find('all', array(
			'fields' => array('DISTINCT CurrentInventory.product_id'),
			'conditions' => array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.qty > ' => 0, 'inventory_status_id' => 1)
		));

		$product_ci = array();
		foreach ($products_from_ci as $each_ci) {
			$product_ci[] = $each_ci['CurrentInventory']['product_id'];
		}
		foreach ($existing_record['OrderDetail'] as $value) {
			$product_ci[] = $value['product_id'];
		}

		$product_ci_in = implode(",", $product_ci);

		/*$product_list = $this->Product->find('list', array('conditions' => array('Product.id' => $product_ci),'order' => array('order' => 'asc')));*/

		/* ------- end get edit data -------- */


		/*-----------My Work--------------*/
		$this->loadModel('ProductPrice');
		$this->loadModel('ProductCombination');


		foreach ($existing_record['OrderDetail'] as $key => $value) {
			$existing_product_category_id_array = $this->Product->find('all', array(
				'conditions' => array('Product.id' => $value['product_id']),
				'fields' => array('product_category_id'),
				'recursive' => -1
			));

			$existing_product_category_id = $existing_product_category_id_array[0]['Product']['product_category_id'];

			if ($existing_product_category_id != 32) {
				$individual_slab = array();
				$combined_slab = array();
				$all_combination_id = array();

				$retrieve_price_combination[$value['product_id']] = $this->ProductPrice->find('all', array(
					'conditions' => array('ProductPrice.product_id' => $value['product_id'], 'ProductPrice.has_combination' => 0)
				));

				foreach ($retrieve_price_combination[$value['product_id']][0]['ProductCombination'] as $key => $value2) {
					$individual_slab[$value2['min_qty']] = $value2['price'];
				}


				$combination_info = $this->ProductCombination->find('first', array(
					'conditions' => array('ProductCombination.product_id' => $value['product_id'], 'ProductCombination.combination_id !=' => 0)
				));

				if (!empty($combination_info['ProductCombination']['combination_id'])) {
					$combination_id = $combination_info['ProductCombination']['combination_id'];
					$all_combination_id_info = $this->ProductCombination->find('all', array(
						'conditions' => array('ProductCombination.combination_id' => $combination_id)
					));

					$combined_product = '';
					foreach ($all_combination_id_info as $key => $individual_combination_id) {
						$all_combination_id[$individual_combination_id['ProductCombination']['product_id']] = $individual_combination_id['ProductCombination']['price'];

						$individual_combined_product_id = $individual_combination_id['ProductCombination']['product_id'];

						$combined_product = $combined_product . ',' . $individual_combined_product_id;
					}
					$trimmed_combined_product = ltrim($combined_product, ',');

					$combined_slab[$combination_info['ProductCombination']['min_qty']] = $all_combination_id;

					$matched_combined_product_id_array = explode(',', $trimmed_combined_product);
					asort($matched_combined_product_id_array);
					$matched_combined_product_id = implode(',', $matched_combined_product_id_array);
				} else {
					$combined_slab = array();
					$matched_combined_product_id = '';
				}



				$edited_cart_data[$value['product_id']] = array(
					'product_price' => array(
						'id' => $retrieve_price_combination[$value['product_id']][0]['ProductPrice']['id'],
						'product_id' => $value['product_id'],
						'general_price' => $retrieve_price_combination[$value['product_id']][0]['ProductPrice']['general_price'],
						'effective_date' => $retrieve_price_combination[$value['product_id']][0]['ProductPrice']['effective_date']
					),
					'individual_slab' => $individual_slab,
					'combined_slab' => $combined_slab,
					'combined_product' => $matched_combined_product_id
				);



				if (!empty($matched_combined_product_id)) {
					$edited_matched_data[$matched_combined_product_id] = array(
						'count' => '4',
						'is_matched_yet' => 'NO',
						'matched_count_so_far' => '2',
						'matched_id_so_far' => '63,65'
					);

					$edited_current_qty_data[$value['product_id']] = $value['sales_qty'];
				}
			}
		}

		if (!empty($edited_cart_data)) {
			$this->Session->write('cart_session_data', $edited_cart_data);
		}
		if (!empty($edited_matched_data)) {
			$this->Session->write('matched_session_data', $edited_matched_data);
		}
		if (!empty($edited_current_qty_data)) {
			$this->Session->write('combintaion_qty_data', $edited_current_qty_data);
		}


		$this->set('page_title', 'Edit Order');
		$this->Order->id = $id;
		if (!$this->Order->exists($id)) {
			throw new NotFoundException(__('Invalid Order'));
		}
		/* -------- create individual Product data --------- */



		global $cart_data, $matched_array;
		$cart_data = array();
		$matched_array = array();
		$qty_session_data = array();


		/* ---------creating prepare data---------- */
		$prepare_cart_data = array();
		if (!empty($filter_product['Product']['id'])) {
			$prepare_cart_data[$filter_product['Product']['id']]['Product'] = $filter_product['Product'];
			$prepare_cart_data[$filter_product['Product']['id']]['ProductPrice'] = $filter_product['ProductPrice'];
			foreach ($filter_product['Individual_slab'] as $individual_slab_val) {
				$prepare_cart_data[$filter_product['Product']['id']]['Individual_slab'][$individual_slab_val['min_qty']] = $individual_slab_val['price'];
			}
			if (!empty($filter_product['Combination'])) {
				$prepare_cart_data[$filter_product['Product']['id']]['Combination'] = $filter_product['Combination'];
			}
			if (!empty($filter_product['Combination_id'])) {

				/* ---- start ------- */
				$this->Combination->recursive = 2;
				$condition_value1['Combination.id'] = $filter_product['Combination_id']; //$filter_product['Combination']['id'];
				$combination_slab_data_option = array(
					'conditions' => array($condition_value1),
				);
				$combination_slab_data = $this->Combination->find('all', $combination_slab_data_option);
				/* ----- end -------- */

				$combination_slab = array();
				foreach ($combination_slab_data as $combine_group) {
					foreach ($combine_group['ProductCombination'] as $combine_val) {
						$combination_slab[$combine_val['min_qty']][$combine_val['product_id']] = $combine_val['Childrel']['price'];
					}
				}
				if (!empty($combination_slab)) {
					$prepare_cart_data[$filter_product['Product']['id']]['Combined_slab'] = $combination_slab;
				}
			}
		}
		/* ----------start create cart data and matched data ----------- */



		/* ------ unset cart data ------- */

		$user_office_id = $this->UserAuth->getOfficeId();
		/*$sales_person_list = $this->SalesPerson->find('list', array(
			'conditions' => array('SalesPerson.office_id' => $user_office_id, 'User.user_group_id' => 4)
			));*/
		$sales_person_list = $this->SalesPerson->find('list', array(
			'conditions' => array('SalesPerson.office_id' => $user_office_id)
		));

		//$this->Order->id = $id;
		$count = 0;
		if ($this->request->is('post')) {

			//$sale_type_id = $this->request->data['OrderProces']['sale_type_id'];

			//pr($this->request->data); die();
			//exit;
			if ($office_parent_id == 0) {
				$company_id = $this->request->data['OrderProces']['company_id'];
			} else {
				$company_id  = $this->Session->read('Office.company_id');
			}

			//$outlet_is_within_group = $this->outletGroupCheck($this->request->data['OrderProces']['outlet_id']);
			$outlet_is_within_group = $this->outletGroupCheck($this->request->data['OrderProces']['distribut_outlet_id']);
			$product_is_injectable = $this->productInjectableCheck($this->request->data['OrderDetail']['product_id']);

			/*if($product_is_injectable && !$outlet_is_within_group && $sale_type_id != 2){
				$this->Session->setFlash(__("Special product can't sell to ordinary outlet!"), 'flash/error');
				$this->redirect(array('action' => 'create_order'));
				exit;
			}*/
			//pr($id);die();
			if (array_key_exists('save', $this->request->data)) {
				foreach ($this->request->data['OrderDetail']['remaining_qty'] as $key => $val) {
					if ($val != 0) {
						//$orderData['is_closed'] = 1;
						$count++;
					}
				}
				if ($count == 0) {
					$orderData['is_closed'] = 1;
				} else {
					$orderData['is_closed'] = 0;
				}
			}
			$order_id = $id;

			//$this->admin_delete($order_id, 0);
			//pr($this->request->data);die();

			/*START ADD NEW*/
			//get office id 
			$office_id = $this->request->data['OrderProces']['office_id'];
			$outlet_id = $this->request->data['OrderProces']['distribut_outlet_id'];
			//get thana id 
			$this->loadModel('Outlet');
			$getOutlets = $this->Outlet->find('all', array(
				'conditions' => array('Outlet.id' => $outlet_id),
			));

			// pr( $getOutlets);//die();
			$outlet_info = $this->Outlet->find('first', array(
				'conditions' => array('Outlet.id' => $this->request->data['OrderProces']['distribut_outlet_id']),
				'recursive' => -1
			));
			// pr($outlet_info);die();

			$market_id = $getOutlets[0]['Outlet']['market_id'];
			$this->loadModel('Market');
			$market_info = $this->Market->find(
				'first',
				array(
					'conditions' => array('Market.id' => $market_id),
					'fields' => 'Market.thana_id',
					'order' => array('Market.id' => 'asc'),
					'recursive' => -1,
					//'limit' => 100
				)
			);
			$thana_id = $market_info['Market']['thana_id'];
			/*END ADD NEW*/
			$dealer_is_limit_check = 0;
			//pr($thana_id);die();
			$this->request->data['OrderProces']['is_active'] = 1;
			//$this->request->data['OrderProces']['status'] = ($this->request->data['OrderProces']['credit_amount'] != 0) ? 1 : 2;

			if (array_key_exists('draft', $this->request->data)) {
				$this->request->data['OrderProces']['status'] = 2;
				$this->request->data['OrderProces']['confirm_status'] = 1;
				//$this->request->data['OrderProces']['manage_draft_status'] = 1;
				//$this->request->data['OrderProces']['confirmed'] = 1;
				$message = "Order Has Been Saved as Draft";

				$is_execute = 0;
			} else {
				$message = "Order Has Been Saved";
				$this->request->data['OrderProces']['status'] = 2;
				// $this->request->data['OrderProces']['confirmed'] = 1;
				// $this->request->data['OrderProces']['status'] = ($this->request->data['Order']['credit_amount'] != 0) ? 1 : 2;
				$this->request->data['OrderProces']['confirm_status'] = 2;
				//$this->request->data['OrderProces']['manage_draft_status'] = 2;
				$is_execute = 1;
			}

			//for distibuter limit amount
			$maintain_dealer_type = $this->Session->read('Office.company_info.Company.maintain_dealer');

			$distributor_id = $outlet_info['Outlet']['distributor_id'];

			$this->loadModel('DistDistributor');
			$distributor_info = $this->DistDistributor->find('first', array(
				'conditions' => array(
					'DistDistributor.id' => $distributor_id
				),
				'recursive' => -1
			));
			//pr($distributor_info);//die();

			//for distibuter limit amount
			if (!empty($distributor_info['DistDistributor']['dealer_is_limit_check'])) {
				$dealer_is_limit_check = $distributor_info['DistDistributor']['dealer_is_limit_check'];
			}
			$maintain_dealer_type = $this->Session->read('Office.company_info.Company.maintain_dealer');
			if ($maintain_dealer_type == 1) {

				if ($dealer_is_limit_check == 1) {

					$this->loadModel('DistDistributorBalance');
					$dealer_limit_info = $this->DistDistributorBalance->find('first', array(
						'conditions' => array(
							'DistDistributorBalance.dist_distributor_id' => $getOutlets[0]['Outlet']['distributor_id']
							//'DistDistributorBalance.effective_date >=' => date('Y-m-d'),
							//'DistDistributorBalance.end_effective_date =' => '',
						),
						////'order' => 'DistDistributorBalance.effective_date DESC',
						'limit' => 1,
						'recursive' => -1
					));


					//pr($dealer_limit_info);


					$credit_amount = $this->request->data['Order']['credit_amount'];
					$dealer_limit = $dealer_limit_info['DistDistributorBalance']['balance'];

					//print_r($dealer_limit);exit;

					$this->loadModel('DistDistributorBalanceHistory');
					$dealer_balance_info = $this->DistDistributorBalanceHistory->find('first', array(
						'conditions' => array(
							'DistDistributorBalanceHistory.dist_distributor_id' => $getOutlets[0]['Outlet']['distributor_id'],
							//'DistDistributorBalanceHistory.is_execute' => 1,
						),
						'order' => 'DistDistributorBalanceHistory.id DESC',
						'recursive' => -1
					));

					if ($dealer_balance_info) {
						if ($dealer_balance_info['DistDistributorBalanceHistory']['balance'] < 0) {
							$balance = $dealer_limit + ($dealer_balance_info['DistDistributorBalanceHistory']['balance'] - $this->request->data['Order']['credit_amount']);
						} else {
							$balance = $dealer_limit - $dealer_balance_info['DistDistributorBalanceHistory']['balance'] - $this->request->data['Order']['credit_amount'];
						}
					} else {
						$balance = $dealer_limit;
					}
					//pr($balance);die();
					if ($office_parent_id == 0) {
						$company_id = $this->request->data['OrderProces']['company_id'];
					} else {
						$company_id  = $this->Session->read('Office.company_id');
					}

					$dealer_balance_data = array();
					$dealer_balance_data['dist_distributor_id'] = $dealer_limit_info['DistDistributorBalance']['dist_distributor_id'];
					$dealer_balance_data['company_id'] = $company_id;
					$dealer_balance_data['office_id'] = $this->request->data['OrderProces']['office_id'];
					$dealer_balance_data['memo_value'] = $this->request->data['Order']['gross_value'];
					$dealer_balance_data['balance'] = $balance;

					$dealer_balance_data['transaction_amount'] = $this->request->data['Order']['credit_amount'];
					$dealer_balance_data['transaction_date'] = date('Y-m-d');
					$dealer_balance_data['is_execute'] = $is_execute;

					$dealer_balance_data['created_at'] = $this->current_datetime();
					$dealer_balance_data['created_by'] = $this->UserAuth->getUserId();
					$dealer_balance_data['updated_at'] = $this->current_datetime();
					$dealer_balance_data['updated_by'] = $this->UserAuth->getUserId();

					$this->DistDistributorBalanceHistory->create();

					$this->DistDistributorBalanceHistory->save($dealer_balance_data);
				}
			}
			//end for distributer limit 

			$sales_person = $this->SalesPerson->find('list', array(
				'conditions' => array('territory_id' => $this->request->data['OrderProces']['territory_id']),
				'order' => array('name' => 'asc')
			));
			//pr($this->request->data);die();
			$this->request->data['OrderProces']['sales_person_id'] = key($sales_person);

			$this->request->data['OrderProces']['entry_date'] = date('Y-m-d', strtotime($this->request->data['OrderProces']['entry_date']));
			$this->request->data['OrderProces']['order_date'] = date('Y-m-d', strtotime($this->request->data['OrderProces']['order_date']));

			$orderData['id'] = $order_id;
			$orderData['office_id'] = $this->request->data['OrderProces']['office_id'];

			$orderData['territory_id'] = $this->request->data['OrderProces']['territory_id'];
			$orderData['market_id'] = $market_id;
			//$orderData['market_id'] = $this->request->data['OrderProces']['market_id'];
			//$orderData['outlet_id'] = $this->request->data['OrderProces']['outlet_id'];
			$orderData['outlet_id'] = $this->request->data['OrderProces']['distribut_outlet_id'];
			$orderData['entry_date'] = $this->request->data['OrderProces']['entry_date'];
			$orderData['order_date'] = $this->request->data['OrderProces']['order_date'];
			$orderData['order_no'] = $this->request->data['OrderProces']['order_no'];
			$orderData['gross_value'] = $this->request->data['Order']['gross_value'];
			$orderData['cash_recieved'] = $this->request->data['Order']['cash_recieved'];
			$orderData['credit_amount'] = $this->request->data['Order']['credit_amount'];
			$orderData['w_store_id'] = $this->request->data['OrderProces']['w_store_id'];
			$orderData['is_active'] = $this->request->data['OrderProces']['is_active'];
			$orderData['status'] = $this->request->data['OrderProces']['status'];
			$orderData['confirm_status'] = $this->request->data['OrderProces']['confirm_status'];
			//$orderData['manage_draft_status'] = $this->request->data['OrderProces']['manage_draft_status'];
			//$orderData['order_time'] = $this->current_datetime();	
			$orderData['order_time'] = $this->request->data['OrderProces']['entry_date'];


			$orderData['order_reference_no'] = $this->request->data['OrderProces']['order_reference_no'];

			$orderData['instrument_reference_no'] = $this->request->data['Order']['reference_number'];

			$orderData['instrument_type'] = $this->request->data['OrderProces']['instrumentType_id'];

			$orderData['sales_person_id'] = $this->request->data['OrderProces']['sales_person_id'];
			$orderData['from_app'] = 0;
			$orderData['action'] = 1;
			$orderData['confirmed'] = 1;


			if ($office_parent_id == 0) {
				$company_id = $this->request->data['OrderProces']['company_id'];
			} else {
				$company_id  = $this->Session->read('Office.company_id');
			}
			$orderData['company_id'] = $company_id;


			$orderData['order_reference_no'] = $this->request->data['OrderProces']['order_reference_no'];


			$orderData['created_at'] = $this->current_datetime();
			$orderData['created_by'] = $this->UserAuth->getUserId();
			$orderData['updated_at'] = $this->current_datetime();
			$orderData['updated_by'] = $this->UserAuth->getUserId();


			$orderData['office_id'] = $office_id ? $office_id : 0;
			$orderData['thana_id'] = $thana_id ? $thana_id : 0;

			$new_orderdata['id'] = $orderData['id'];
			// $new_orderdata['is_closed']=$orderData['is_closed'];
			$new_orderdata['status'] = $orderData['status'];            //$this->Order->create();
			//$new_orderdata['manage_draft_status']=$orderData['manage_draft_status'];            //$this->Order->create();
			$new_orderdata['confirm_status'] = $orderData['confirm_status'];            //$this->Order->create();
			//pr($new_orderdata);die();
			//if ($this->Order->save($orderData)) {
			if ($this->Order->save($new_orderdata)) {
				//pr($order_id);die();

				//$order_id = $this->Order->getLastInsertId();


				$order_info_arr = $this->Order->find('first', array(
					'conditions' => array(
						'Order.id' => $order_id
					)
				));
				//pr($order_id);die();
				$this->loadModel('Store');
				$store_id_arr = $this->Store->find('first', array(
					'conditions' => array(
						'Store.office_id' => $order_info_arr['Order']['office_id']
					)
				));

				$store_id = $store_id_arr['Store']['id'];




				if ($order_id) {
					$all_product_id = $this->request->data['OrderDetail']['product_id'];
					if (!empty($this->request->data['OrderDetail'])) {
						$total_product_data = array();
						$order_details = array();
						$order_details['OrderDetail']['order_id'] = $order_id;
						//pr($this->request->data);die();
						foreach ($this->request->data['OrderDetail']['product_id'] as $key => $val) {
							if ($val == NULL) {
								continue;
							}
							$product_id = $order_details['OrderDetail']['product_id'] = $val;
							$order_details['OrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
							$sales_price = $order_details['OrderDetail']['price'] = $this->request->data['OrderDetail']['Price'][$key];
							$sales_qty = $order_details['OrderDetail']['sales_qty'] = $this->request->data['OrderDetail']['sales_qty'][$key];
							$order_details['OrderDetail']['remaining_qty'] = $this->request->data['OrderDetail']['remaining_qty'][$key];
							$order_details['OrderDetail']['deliverd_qty'] = $this->request->data['OrderDetail']['deliverd_qty'][$key];
							$order_details['OrderDetail']['product_price_id'] = $this->request->data['OrderDetail']['product_price_id'][$key];
							$order_details['OrderDetail']['bonus_qty'] = $this->request->data['OrderDetail']['bonus_product_qty'][$key];

							$product_price_slab_id = 0;
							if ($sales_price > 0) {
								$product_price_slab_id = $this->get_product_price_id($val, $sales_price, $all_product_id);
								// pr($product_price_slab_id);exit;
							}
							$order_details['OrderDetail']['product_price_id'] = $product_price_slab_id['product_price_id'];
							$order_details['OrderDetail']['product_combination_id'] = $product_price_slab_id['combination_id'];
							$order_details['OrderDetail']['bonus_qty'] = $this->request->data['OrderDetail']['bonus_product_qty'][$key];


							if ($this->request->data['OrderDetail']['bonus_product_id'][$key] != 0) {
								$order_details['OrderDetail']['bonus_product_id'] = $this->request->data['OrderDetail']['bonus_product_id'][$key];
							} else {
								$order_details['OrderDetail']['bonus_product_id'] = NULL;
							}

							//Start for bonus
							$order_date = date('Y-m-d', strtotime($this->request->data['OrderProces']['order_date']));
							$bonus_product_id = $this->request->data['OrderDetail']['bonus_product_id'];
							$bonus_product_qty = $this->request->data['OrderDetail']['bonus_product_qty'];
							$order_details['OrderDetail']['bonus_id'] = 0;
							$order_details['OrderDetail']['bonus_scheme_id'] = 0;
							if ($bonus_product_qty[$key] > 0) {
								//echo $bonus_product_id[$key].'<br>';
								$b_product_id = $bonus_product_id[$key];
								$bonus_result = $this->bouns_and_scheme_id_set($b_product_id, $order_date);
								$order_details['OrderDetail']['bonus_id'] = $bonus_result['bonus_id'];
							}
							//End for bouns


							//pr($product_id);die();
							//foreach ($OrderDetail_record as $value) {

							$new_order_details['OrderDetail'] = $OrderDetail_record[$product_id];
							$deliverd_qty = $OrderDetail_record[$product_id]['deliverd_qty'];

							$new_order_details['OrderDetail']['remaining_qty'] = $order_details['OrderDetail']['remaining_qty'];
							$new_order_details['OrderDetail']['deliverd_qty'] = $order_details['OrderDetail']['deliverd_qty'] + $deliverd_qty;
							$new_total_product_data[] = $new_order_details;
							//}
							/* pr($order_details);
						   pr($new_order_details);die();*/
							$total_product_data[] = $order_details;

							if ($bonus_product_qty[$key] > 0) {
								$order_details_bonus['OrderDetail']['order_id'] = $order_id;
								$order_details_bonus['OrderDetail']['is_bonus'] = 1;
								$order_details_bonus['OrderDetail']['product_id'] = $product_id;
								$order_details_bonus['OrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
								$order_details_bonus['OrderDetail']['price'] = 0.0;
								$order_details_bonus['OrderDetail']['sales_qty'] = $this->request->data['OrderDetail']['bonus_product_qty'][$key];
								$total_product_data[] = $order_details_bonus;
								unset($order_details_bonus);
								if (array_key_exists('save', $this->request->data)) {
									$stock_hit = 1;
									if ($stock_hit) {
										$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
										$product_list = Set::extract($products, '{n}.Product');
										$bonus_qty = $this->request->data['MemoDetail']['bonus_product_qty'][$key];
										$punits_pre = $this->search_array($product_id, 'id', $product_list);
										if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
											$base_quantity = $bonus_qty;
										} else {
											$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $bonus_qty);
										}

										$update_type = 'deduct';
										$this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 11, $this->request->data['OrderProces']['order_date']);
									}
								}
							}
							if (array_key_exists('save', $this->request->data)) {
								$stock_hit = 1;
								if ($stock_hit) {
									$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
									$product_list = Set::extract($products, '{n}.Product');

									$punits_pre = $this->search_array($product_id, 'id', $product_list);
									if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
										$base_quantity = $sales_qty;
									} else {
										$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
									}

									$update_type = 'deduct';
									$this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 11, $this->request->data['OrderProces']['order_date']);
								}
							}

							$tempOrderDetails['TempOrderDetail'] = $order_details['OrderDetail'];
							$temp_total_product_data[] = $tempOrderDetails;
						}
						if (array_key_exists('draft', $this->request->data)) {
							$this->loadModel('TempOrderDetail');
							$this->TempOrderDetail->create();
							$this->TempOrderDetail->saveAll($temp_total_product_data);
						}
						//pr($new_total_product_data);die();	
						$this->OrderDetail->saveAll($new_total_product_data);
					}
				}

				/******************* Memo create date: 04-09-2019 ***************************/
				/*********************Memo Create for no Confirmation Type *************************/
				$this->loadModel('Memo');
				$this->loadModel('OrderDetail');
				$this->loadModel('Order');
				$this->loadModel('Collection');
				/***************Delete Data from temp Table 05-09-2019**************************/
				$this->loadModel('TempOrderDetail');
				$this->TempOrderDetail->deleteAll(array('TempOrderDetail.order_id' => $order_id));
				// $this->admin_delete($order_id, 0);
				/***************end  Delete Data from temp Table 05-09-2019**************************/
				$order_info = $this->Order->find('first', array(
					'conditions' => array('Order.id' => $order_id),
					'recursive' => -1
				));

				$order_detail_info = $this->OrderDetail->find('all', array(
					'conditions' => array('OrderDetail.order_id' => $order_id),
					'recursive' => -1
				));

				// pr($order_info);
				//pr($order_detail_info);
				// exit;

				$memo = array();
				$memo['sales_person_id'] = $order_info['Order']['sales_person_id'];
				$memo['entry_date'] = date('Y-m-d');
				$memo['memo_date'] = date('Y-m-d');

				$memo['office_id'] = $order_info['Order']['office_id'];
				$memo['sale_type_id'] = 1;
				$memo['territory_id'] = $order_info['Order']['territory_id'];
				$memo['thana_id'] = $order_info['Order']['thana_id'];
				$memo['market_id'] = $order_info['Order']['market_id'];
				$memo['outlet_id'] = $order_info['Order']['outlet_id'];

				$memo['memo_date'] = $order_info['Order']['order_date'];
				$memo['memo_no'] = $order_info['Order']['order_no'];
				$memo['gross_value'] = $order_info['Order']['gross_value'];
				$memo['cash_recieved'] = $order_info['Order']['cash_recieved'];
				$memo['credit_amount'] = $order_info['Order']['credit_amount'];
				$memo['is_active'] = $order_info['Order']['is_active'];

				if (array_key_exists('save', $this->request->data)) {
					$memo['status'] = 1;
				} else {
					$memo['status'] = 0;
				}
				$memo['memo_time'] = $this->current_datetime();
				$memo['sales_person_id'] = $order_info['Order']['sales_person_id'];
				$memo['from_app'] = 0;
				$memo['action'] = 1;
				$memo['is_program'] = 0;
				$memo['company_id'] = $order_info['Order']['company_id'];

				$memo['memo_reference_no'] = $order_info['Order']['memo_reference_no'];

				$memo['created_at'] = $this->current_datetime();
				$memo['created_by'] = $this->UserAuth->getUserId();
				$memo['updated_at'] = $this->current_datetime();
				$memo['updated_by'] = $this->UserAuth->getUserId();

				//pr($memo);
				//exit;

				$this->Memo->create();

				if ($this->Memo->save($memo)) {

					$memo_id = $this->Memo->getLastInsertId();
					$memo_info_arr = $this->Memo->find('first', array(
						'conditions' => array(
							'Memo.id' => $memo_id
						)
					));


					//pr($memo_info_arr);


					if ($memo_id) {
						if (!empty($order_detail_info[0]['OrderDetail'])) {
							//pr($order_detail_info[0]);
							$this->loadModel('MemoDetail');
							$total_product_data = array();
							$memo_details = array();
							$memo_details['MemoDetail']['memo_id'] = $memo_id;

							foreach ($order_detail_info as $order_detail_result) {
								//pr($order_detail_result);
								$product_id = $order_detail_result['OrderDetail']['product_id'];
								$memo_details['MemoDetail']['product_id'] = $product_id;
								$memo_details['MemoDetail']['measurement_unit_id'] = $order_detail_result['OrderDetail']['measurement_unit_id'];
								$memo_details['MemoDetail']['price'] = $order_detail_result['OrderDetail']['price'];
								$memo_details['MemoDetail']['sales_qty'] = $order_detail_result['OrderDetail']['sales_qty'];

								$memo_details['MemoDetail']['product_price_id'] = $order_detail_result['OrderDetail']['product_price_id'];
								$memo_details['MemoDetail']['bonus_qty'] = $order_detail_result['OrderDetail']['bonus_qty'];
								$memo_details['MemoDetail']['offer_id'] = $order_detail_result['OrderDetail']['offer_id'];
								$memo_details['MemoDetail']['bonus_product_id'] = $order_detail_result['OrderDetail']['bonus_product_id'];
								$memo_details['MemoDetail']['bonus_id'] = $order_detail_result['OrderDetail']['bonus_id'];
								$memo_details['MemoDetail']['bonus_scheme_id'] = $order_detail_result['OrderDetail']['bonus_scheme_id'];
								$memo_details['MemoDetail']['price_combination_id'] = $order_detail_result['OrderDetail']['price_combination_id'];
								$memo_details['MemoDetail']['product_combination_id'] = $order_detail_result['OrderDetail']['product_combination_id'];
								$memo_details['MemoDetail']['is_bonus'] = $order_detail_result['OrderDetail']['is_bonus'];
								$memo_details['MemoDetail']['deliverd_qty'] = $order_detail_result['OrderDetail']['deliverd_qty'];

								//pr($order_details);
								$total_product_data[] = $memo_details;
							}
							// pr($total_product_data);die();
							$this->MemoDetail->saveAll($total_product_data);
						}
					}


					//start collection crate
					$collection_data = array();
					$collection_data['memo_id'] = $memo_info_arr['Memo']['id'];
					$collection_data['memo_no'] = $memo_info_arr['Memo']['memo_no'];
					$collection_data['so_id'] = $memo_info_arr['Memo']['sales_person_id'];

					$collection_data['memo_value'] = $memo_info_arr['Memo']['gross_value'];
					$collection_data['credit_or_due'] = $memo_info_arr['Memo']['credit_amount'];
					$collection_data['memo_date'] = $memo_info_arr['Memo']['memo_date'];
					$collection_data['type'] = 1;
					$collection_data['instrument_type'] = $order_info['Order']['instrument_type'];
					$collection_data['collectionAmount'] = $memo_info_arr['Memo']['gross_value'];
					$collection_data['collectionDate'] = date('Y-m-d');
					$collection_data['created_at'] = $this->current_datetime();

					$collection_data['territory_id'] = $memo_info_arr['Memo']['territory_id'];
					$collection_data['outlet_id'] = $memo_info_arr['Memo']['outlet_id'];
					$collection_data['market_id'] = $memo_info_arr['Memo']['market_id'];
					$collection_data['office_id'] = $memo_info_arr['Memo']['office_id'];

					//pr($collection_data);
					//exit;

					$this->Collection->save($collection_data);

					//end collection careate    

				}

				/*******************************End Memo********************************************/
				/******************* Memo Create end date: 04-09-2019************************/
				/************* create Challan *************/
				if (array_key_exists('save', $this->request->data)) {
					/*****************Create Chalan and *****************/
					$this->loadModel('Challan');
					$this->loadModel('ChallanDetail');
					$this->loadModel('CurrentInventory');
					$company_id  = $this->Session->read('Office.company_id');
					$office_id  = $this->request->data['OrderProces']['office_id'];
					$store_id = $this->request->data['OrderProces']['w_store_id'];
					$challan['company_id'] = $company_id;
					$challan['office_id'] = $office_id;
					$challan['receiver_store_id'] = $store_id;
					$challan['challan_date'] = $this->current_datetime();
					//$challan['product_type']="";
					//$challan['product_id']=;
					//$challan['batch_no']="";
					//$challan['challan_qty']="";
					//$challan['expire_date']="";
					$challan['status'] = 0;
					$challan['transaction_type_id'] = 1;
					$challan['inventory_status_id'] = 1;
					$challan['sender_store_id'] = $store_id;
					$challan['created_at'] = $this->current_datetime();
					$challan['created_by'] = $this->UserAuth->getUserId();
					$challan['updated_at'] = $this->current_datetime();
					$challan['updated_by'] = $this->UserAuth->getUserId();
					//pr();die();
					$this->Challan->create();
					if ($this->Challan->save($challan)) {
						// pr('challan has been saved');//die();
						$udata['id'] = $this->Challan->id;
						$udata['challan_no'] = 'CH' . (10000 + $this->Challan->id);
						$this->Challan->save($udata);
						$product_list = $this->request->data['OrderDetail'];
						if (!empty($product_list['product_id'])) {
							$data_array = array();
							//pr($product_list);
							// pr($store_id);//die();
							foreach ($product_list['product_id'] as $key => $val) {
								if (!empty($val)) {
									$inventories = $this->CurrentInventory->find('first', array(
										'conditions' => array(
											'CurrentInventory.product_id' => $val,
											'CurrentInventory.store_id' => $store_id,
										),
										'recursive' => -1,
									));
									$batch_no = $inventories['CurrentInventory']['batch_number'];

									$data['ChallanDetail']['challan_id'] = $this->Challan->id;
									$data['ChallanDetail']['product_id'] = $val;
									$data['ChallanDetail']['measurement_unit_id'] = $product_list['measurement_unit_id'][$key];
									$data['ChallanDetail']['challan_qty'] = $product_list['sales_qty'][$key];
									$data['ChallanDetail']['batch_no'] = $batch_no;
									//$data['ChallanDetail']['expire_date'] = (($this->request->data['expire_date'][$key] != '' ) ? Date('Y-m-d', strtotime($this->request->data['expire_date'][$key])) : Null);
									$date = (($this->request->data['OrderProces']['order_date'][$key] != ' ' && $this->request->data['OrderProces']['order_date'][$key] != 'null' && $this->request->data['OrderProces']['order_date'][$key] != '') ? explode('-', $this->request->data['OrderProces']['order_date'][$key]) : '');
									if (!empty($date[1])) {
										$date[0] = date('m', strtotime($date[0]));
										$a_date = date('y-m-d', mktime(0, 0, 0, $date[0], 1, $date[1]));
										$data['ChallanDetail']['expire_date'] = date("Y-m-t", strtotime($a_date));
									} else {
										$data['ChallanDetail']['expire_date'] = '';
									}
									$data['ChallanDetail']['inventory_status_id'] = 1;  // set 1 for Sound product
									//$data['ChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
									$data_array[] = $data;
								}
							}
							//pr($data_array);//die();
							$this->ChallanDetail->saveAll($data_array);
						}
					}
				}
				/************* end Challan *************/
			}

			// pr($credit_amount);pr($balance);die();
			if ($maintain_dealer_type == 1) {
				if ($dealer_is_limit_check == 1) {
					if ($credit_amount > $balance) {
						$this->Session->setFlash(__('Please Check the Credit Amount!!!  The Order has been Updated'), 'flash/warning');
					} else {
						$this->Session->setFlash(__($message), 'flash/success');
					}
				}
			}

			//$this->Session->setFlash(__('The Order has been Updated'), 'flash/success');
			if (array_key_exists('save', $this->request->data)) {
				$this->redirect(array('action' => 'delivery', $id));
			} else {
				$this->redirect(array('action' => 'edit', $id));
			}
		}



		$this->loadModel('ProductMeasurement');
		$product_measurement_units = $this->ProductMeasurement->find('list', array('fields' => array('product_id', 'measurement_unit_id')));
		$product_category_id_list = $this->Product->find('list', array('fields' => array('id', 'product_category_id')));


		$this->set(compact('offices', 'territories', 'markets', 'outlets', 'current_date', 'product_list', 'sale_type_list', 'existing_record', 'office_parent_id', 'product_measurement_units', 'product_category_id_list', 'open_bonus_product_option'));
	}
	public function admin_edit($id = null)
	{
		ini_set('max_execution_time', 999999);
		ini_set('memory_limit', '-1');
		$this->loadmodel('InstrumentType');

		$this->set('page_title', 'Edit Order');
		$this->Order->id = $id;
		if (!$this->Order->exists($id)) {
			throw new NotFoundException(__('Invalid Order'));
		}

		$this->loadModel('ProductCombination');
		$this->loadModel('DistCombination');
		$this->loadModel('Combination');
		$this->Session->delete('cart_session_data');
		$this->Session->delete('matched_session_data');
		$this->Session->delete('combintaion_qty_data');

		$this->loadModel('Product');
		$this->loadModel('Memo');
		$this->loadModel('DistOutletMap');
		$this->loadModel('CurrentInventory');
		$this->loadModel('MeasurementUnit');
		$this->LoadModel('Product');
		$this->LoadModel('Store');
		$this->loadModel('ProductPrice');
		$this->loadModel('DistProductPrice');
		$this->loadModel('DistProductCombination');
		$this->loadModel('ProductCombination');
		$this->loadModel('Outlet');
		$this->loadModel('DistDistributorBalance');
		$this->loadModel('DistDistributorBalanceHistory');
		$this->loadModel('DistDistributorLimit');
		$this->loadModel('DistDistributorLimitHistory');
		$this->loadModel('CombinationDetailsV2');
		$this->loadModel('ProductBatchInfo');
		$count = 0;
		if ($this->request->is('post')) {
			
			// echo '<pre>';print_r($this->request->data);exit; 
			 $path = APP . 'logs/';
			/*$myfile = fopen($path . "db_requisition_process.txt", "a") or die("Unable to open file!"); */
			/*  pr($this->request->data);
			exit; */

		   //-------------------array create for serial ------------\\
		   
			$product_key_value_null = array();
			foreach ($this->request->data['OrderDetail']['product_id'] as $key => $val) {
				if(empty($val)){
					$product_key_value_null[$key]=1;
				}
			}   

			$n=0;
			//echo '<pre>';print_r($this->request->data['OrderDetail']['product_current_inventory_id']);
				ksort($this->request->data['OrderDetail']['product_current_inventory_id']);
			// echo '<pre>';print_r($this->request->data['OrderDetail']['product_current_inventory_id']);exit;

			foreach ($this->request->data['OrderDetail']['product_current_inventory_id'] as $key => $val) {
				$pvalue = $product_key_value_null[$n];
				if( !empty($pvalue)){
					
					$this->request->data['OrderDetail']['product_batch_current_inventory_id'][$n+1]= $val;
					$this->request->data['OrderDetail']['product_batch_given_stock'][$n+1]= $this->request->data['OrderDetail']['product_given_stock'][$key];
					$n = $n+1; 
				}else{
					$this->request->data['OrderDetail']['product_batch_current_inventory_id'][$n]= $val;
					$this->request->data['OrderDetail']['product_batch_given_stock'][$n]= $this->request->data['OrderDetail']['product_given_stock'][$key];
				}

				$n++;

			}
		  
			unset($this->request->data['OrderDetail']['product_current_inventory_id']);
			unset($this->request->data['OrderDetail']['product_given_stock']);  

			//-------------------end------------\\
			
			//echo '<pre>';print_r($this->request->data['OrderDetail']);exit;
		   

			$this->loadModel('Order');
			$order_info = $this->Order->find('first', array(
				'conditions' => array(
					'Order.id' => $id,
					'Order.confirm_status' => 2,
					'Order.status' => 2,
				),
				'recursive' => -1
			));
			if (!empty($order_info)) {
				$this->Session->setFlash(__('This Order is Already Deliverd'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}
			//pr($this->request->data);die();



			$order_id = $id;

			$this->loadModel('Store');
			$store_id_arr = $this->Store->find('first', array(
				'conditions' => array(
					'Store.office_id' => $this->request->data['OrderProces']['office_id'],
					'Store.store_type_id' => 2
				),
				'recursive' => -1
			));
			$store_id = $store_id_arr['Store']['id'];
			$stock_check = 0;
			$stock_available = 1;
			$m = "";
			$gross_amount = 0;
			$order_product_array_for_stock_check = array();
			$products = $this->Product->find('all', array('fields' => array('id', 'name', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
			$product_list = Set::extract($products, '{n}.Product');

			foreach ($this->request->data['OrderDetail']['product_id'] as $key => $val) {
				if ($val == NULL) {
					continue;
				}

				/*------ Stock Checking array preparation :start  --------------*/
				if (!isset($order_product_array_for_stock_check[$val])) {
					$order_product_array_for_stock_check[$val] = 0;
				}
				$punits_pre = $this->search_array($val, 'id', $product_list);
				$price = $this->request->data['OrderDetail']['Price'][$key];
				if ($price == 0.0) {
					$qty = $this->request->data['OrderDetail']['sales_qty'][$key];
				} else {
					$qty = $this->request->data['OrderDetail']['deliverd_qty'][$key];
				}
				$measurement_unit_id = isset($this->request->data['OrderDetail']['measurement_unit_id'][$key]) ? $this->request->data['OrderDetail']['measurement_unit_id'][$key] : $punits_pre['sales_measurement_unit_id'];
				$base_qty = 0;
				if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
					$base_qty = round($qty);
				} else {
					$base_qty = $this->unit_convert($val, $measurement_unit_id, $qty);
				}
				$bonus_base_qty = 0;
				if (isset($this->request->data['OrderDetail']['bonus_product_qty'][$key])) {
					$bonus_qty = $this->request->data['OrderDetail']['bonus_product_qty'][$key];
					$measurement_unit_id = isset($this->request->data['OrderDetail']['bonus_measurement_unit_id'][$key]) ? $this->request->data['OrderDetail']['measurement_unit_id'][$key] : $punits_pre['sales_measurement_unit_id'];
					if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
						$bonus_base_qty = round($bonus_qty);
					} else {
						$bonus_base_qty = $this->unit_convert($val, $measurement_unit_id, $bonus_qty);
					}
				}
				$order_product_array_for_stock_check[$val] += ($base_qty + $bonus_base_qty);
				/*------ Stock Checking array preparation :end  --------------*/

				if (!isset($this->request->data['OrderDetail']['deliverd_qty'][$key]) || $this->request->data['OrderDetail']['deliverd_qty'][$key] == 0) {
					continue;
				}

				$total_product_price = 0;
				$total_product_price = $qty * $price;
				$gross_amount = $gross_amount + $total_product_price;
			}
			$this->request->data['Order']['gross_value'] = $gross_amount;
			if (array_key_exists('save', $this->request->data)) {
				/*-------------------- Stock Checking : Start -------------------*/
				foreach ($order_product_array_for_stock_check as $product_id => $qty) {
					if ($qty == 0)
						continue;
					$punits_pre = $this->search_array($product_id, 'id', $product_list);
					$qty = $this->unit_convertfrombase($product_id, $punits_pre['sales_measurement_unit_id'], $qty);
					$stock_check = $this->stock_check($store_id, $product_id, $qty);
					if ($stock_check != 1) {
						$stock_available = 0;
						$msg_for_stock_unavailable = "Stock Not Available For <b>" . $punits_pre['name'] . '</b>';
						break;
					}
				}
				/*-------------------- Stock Checking : END --------------------*/
			}
			if ($stock_available == 0) {
				$this->Session->setFlash(__($msg_for_stock_unavailable), 'flash/error');
				$this->redirect(array('action' => 'edit', $id));
			}
			//$this->admin_delete($order_id, 0);
			$office_id = $this->request->data['OrderProces']['office_id'];
			$outlet_id = $this->request->data['OrderProces']['distribut_outlet_id'];

			$getOutlets = $this->Outlet->find('all', array(
				'conditions' => array('Outlet.id' => $outlet_id),
			));
			$outlet_info = $this->DistOutletMap->find('first', array(
				'conditions' => array('DistOutletMap.outlet_id' => $this->request->data['OrderProces']['distribut_outlet_id']),

			));

			$market_id = $getOutlets[0]['Outlet']['market_id'];
			$this->loadModel('Market');
			$market_info = $this->Market->find('first', array(
				'conditions' => array('Market.id' => $market_id),
				'fields' => 'Market.thana_id',
				'order' => array('Market.id' => 'asc'),
				'recursive' => -1,
			));

			$thana_id = $market_info['Market']['thana_id'];
			$distributor_id = $outlet_info['DistDistributor']['id'];

			$sales_person = $this->SalesPerson->find('list', array(
				'conditions' => array('territory_id' => $this->request->data['OrderProces']['territory_id']),
				'order' => array('name' => 'asc')
			));

			$this->request->data['OrderProces']['sales_person_id'] = key($sales_person);

			$this->request->data['OrderProces']['order_date'] = date('Y-m-d', strtotime($this->request->data['OrderProces']['order_date']));

			$orderData['id'] = $order_id;
			$orderData['office_id'] = $this->request->data['OrderProces']['office_id'];

			$orderData['territory_id'] = $this->request->data['OrderProces']['territory_id'];
			$orderData['market_id'] = $market_id;
			$orderData['outlet_id'] = $this->request->data['OrderProces']['distribut_outlet_id'];
			$orderData['entry_date'] = $this->current_datetime();
			$orderData['order_date'] = $this->request->data['OrderProces']['order_date'];
			$order_no = $orderData['order_no'] = $this->request->data['OrderProces']['order_no'];
			$orderData['gross_value'] = $this->request->data['Order']['gross_value'] - $this->request->data['Order']['total_discount'];
			$orderData['w_store_id'] = $this->request->data['OrderProces']['w_store_id'];
			$orderData['is_active'] = 1;

			$orderData['order_time'] = $this->current_datetime();
			$orderData['order_reference_no'] = $this->request->data['OrderProces']['order_reference_no'];
			$orderData['sales_person_id'] = $this->request->data['OrderProces']['sales_person_id'];
			$orderData['from_app'] = 0;
			$orderData['action'] = 1;
			$orderData['confirmed'] = 1;
			$orderData['order_reference_no'] = $this->request->data['OrderProces']['order_reference_no'];
			$orderData['driver_name'] = $this->request->data['OrderProces']['driver_name'];
			$orderData['truck_no'] = $this->request->data['OrderProces']['truck_no'];


			/* $orderData['created_at'] = $this->current_datetime();
			$orderData['created_by'] = $this->UserAuth->getUserId(); */
			$orderData['updated_at'] = $this->current_datetime();
			$orderData['updated_by'] = $this->UserAuth->getUserId();


			$orderData['office_id'] = $office_id ? $office_id : 0;
			$orderData['thana_id'] = $thana_id ? $thana_id : 0;
			$orderData['total_discount'] = $this->request->data['Order']['total_discount'];

			$balance = 0;
			$balance = 0;
			$limit = 0;
			$dist_balance_info = array();
			$dealer_balance_info = array();
			$dist_limit_info = array();
			/*************************** Balance Check ***********************************/
			$dist_balance_info = array();
			$dist_balance_info = $this->DistDistributorBalance->find('first', array(
				'conditions' => array(
					'DistDistributorBalance.dist_distributor_id' => $distributor_id
				),
				'limit' => 1,
				'recursive' => -1
			));

			if (empty($dist_balance_info)) {
				$this->Session->setFlash(__('Please check Balance of This Distributor!!!'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}
			$credit_amount = $this->request->data['Order']['gross_value'] - $this->request->data['Order']['total_discount'];
			$dist_balance = $dist_balance_info['DistDistributorBalance']['balance'];

			if ($dist_balance < $credit_amount) {
				$this->Session->setFlash(__('Insufficient Balance of This Distributor!!!'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}
			/*************************** end Balance Check ***********************************/
			$this->request->data['OrderProces']['is_active'] = 1;
			$datasource = $this->Order->getDataSource();
			$datasource1 = $this->Memo->getDataSource();
			//$this->admin_delete($order_id, 0);
			try {
				$datasource->begin();
				$datasource1->begin();

				 //-------get all details id------------\\

				 $order_detilas_ids = $this->OrderDetail->find('list', array(
					'conditions'=>array('OrderDetail.order_id' => $order_id),
					'fields'=>array('OrderDetail.id', 'OrderDetail.product_id'),
					'recursive'=>-1
				));

				if(!empty($order_detilas_ids)){
					$orderdetailsid = implode(", ", array_keys($order_detilas_ids));
					if (!$this->Memo->query(" Delete From product_batch_infos where order_details_id IN ($orderdetailsid) ")) {
						throw new Exception();
					}
				} 

				

				//-------------end---------------\\


				if (!$this->OrderDetail->deleteAll(array('OrderDetail.order_id' => $order_id))) {
					throw new Exception();
				}


				if (array_key_exists('draft', $this->request->data)) {
					$orderData['status'] =  $this->request->data['OrderProces']['status'] = 2;
					$orderData['confirm_status'] =  $this->request->data['OrderProces']['confirm_status'] = 1;
					$message = "Order Has Been Saved as Draft";

					$is_execute = 0;
				} else {
					$message = "Order Has Been Saved";
					$orderData['status'] =  $this->request->data['OrderProces']['status'] = 2;
					$orderData['confirm_status'] = $this->request->data['OrderProces']['confirm_status'] = 2;
					$is_execute = 1;

					/*************************** Balance Check ********************************/
					$dist_limit_info = $this->DistDistributorLimit->find('first', array(
						'conditions' => array(
							'DistDistributorLimit.office_id' => $office_id,
							'DistDistributorLimit.dist_distributor_id' => $distributor_id,
						),
						'limit' => 1,
						'recursive' => -1
					));
					/*if($dist_limit_info){
					$dist_balance =$dist_balance + $dist_limit_info['DistDistributorLimit']['max_amount'];
				}*/

					$dealer_balance_info = $this->DistDistributorBalanceHistory->find('first', array(
						'conditions' => array(
							'DistDistributorBalanceHistory.dist_distributor_id' => $distributor_id,

						),
						'order' => 'DistDistributorBalanceHistory.id DESC',
						'recursive' => -1
					));
					/*************************** Balance Check End *********************************/
				}
				//$orderData['status'] = $this->request->data['OrderProces']['status'];
				//$orderData['confirm_status'] = $this->request->data['OrderProces']['confirm_status'];

				if (!$this->Order->saveAll($orderData)) {
					throw new Exception();
				} else {
					$order_info_arr = $this->Order->find('first', array(
						'conditions' => array(
							'Order.id' => $order_id
						)
					));
					// fwrite($myfile, "\n" . $this->current_datetime() . ':' . $order_no . ' : order created ');
					if ($order_id) {
						$all_product_id = $this->request->data['OrderDetail']['product_id'];
						//pr($all_product_id);exit;
						if (!empty($this->request->data['OrderDetail'])) {
							$order_details = array();
							
							
							foreach ($this->request->data['OrderDetail']['product_id'] as $key => $val) {
								if ($val == NULL) {
									continue;
								}
								$total_product_data = array();

								$product_details = $this->Product->find('first', array(
									'fields' => array('id', 'is_virtual', 'parent_id'),
									'conditions' => array('Product.id' => $val),
									'recursive' => -1
								));

								//echo '<pre>';print_r($product_details);exit;


								//$measurement_unit_id = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
								$sales_price = $this->request->data['OrderDetail']['Price'][$key];
								if ($sales_price != 0 && !empty($sales_price)) {

									if ($product_details['Product']['is_virtual'] == 1) {
										$product_id = $order_details['OrderDetail']['virtual_product_id'] = $val;
										$order_details['OrderDetail']['product_id'] = $product_details['Product']['parent_id'];
									} else {
										$order_details['OrderDetail']['virtual_product_id'] = 0;
										$product_id = $order_details['OrderDetail']['product_id'] = $product_details['Product']['id'];
									}

									$order_details['OrderDetail']['order_id'] = $order_id;
									$order_details['OrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
									$sales_price = $order_details['OrderDetail']['price'] = $this->request->data['OrderDetail']['Price'][$key];
									$order_qty = $order_details['OrderDetail']['sales_qty'] = $this->request->data['OrderDetail']['sales_qty'][$key];
									$sales_qty = $order_details['OrderDetail']['deliverd_qty'] = $this->request->data['OrderDetail']['deliverd_qty'][$key];
									$order_details['OrderDetail']['product_price_id'] = $this->request->data['OrderDetail']['product_price_id'][$key];
									$order_details['OrderDetail']['remaining_qty'] = $order_qty - $order_details['OrderDetail']['deliverd_qty'];
									$order_details['OrderDetail']['challan_remarks'] = $this->request->data['OrderDetail']['remarks'][$key];
									$product_price_slab_id = 0;
									$order_details['OrderDetail']['product_price_id'] = $this->request->data['OrderDetail']['product_price_id'][$key];
									$order_details['OrderDetail']['product_combination_id'] = $this->request->data['OrderDetail']['combination_id'][$key];
									$order_details['OrderDetail']['bonus_qty'] = $this->request->data['OrderDetail']['bonus_product_qty'][$key];


									if ($this->request->data['OrderDetail']['bonus_product_id'][$key] != 0) {
										$b_p_id = $order_details['OrderDetail']['bonus_product_id'] = $this->request->data['OrderDetail']['bonus_product_id'][$key];
										$bonus_sales_qty = $this->request->data['OrderDetail']['bonus_product_qty'][$key];
										
										if (array_key_exists('save', $this->request->data)) {

											$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
											$product_list = Set::extract($products, '{n}.Product');

											$punits_pre = $this->search_array($b_p_id, 'id', $product_list);
											if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
												$bonus_base_quantity = $bonus_sales_qty;
											} else {
												$bonus_base_quantity = $this->unit_convert($b_p_id, $punits_pre['sales_measurement_unit_id'], $bonus_sales_qty);
											}

											$update_type = 'deduct';
											$this->update_current_inventory($bonus_base_quantity, $b_p_id, $store_id, $update_type, 11, date('Y-m-d'));
										}
									} else {
										$order_details['OrderDetail']['bonus_product_id'] = NULL;
									}

									//Start for bonus
									$order_date = date('Y-m-d', strtotime($this->request->data['OrderProces']['order_date']));
									$bonus_product_id = $this->request->data['OrderDetail']['bonus_product_id'];
									$bonus_product_qty = $this->request->data['OrderDetail']['bonus_product_qty'];
									$order_details['OrderDetail']['bonus_id'] = 0;
									$order_details['OrderDetail']['bonus_scheme_id'] = 0;
									if ($bonus_product_qty[$key] > 0) {
										$b_product_id = $bonus_product_id[$key];
										$bonus_result = $this->bouns_and_scheme_id_set($b_product_id, $order_date);
										$order_details['OrderDetail']['bonus_id'] = $bonus_result['bonus_id'];
									}
									
									$order_details['OrderDetail']['discount_type'] = $this->request->data['OrderDetail']['disccount_type'][$key];
									$order_details['OrderDetail']['discount_amount'] = $this->request->data['OrderDetail']['discount_amount'][$key];
									$order_details['OrderDetail']['policy_type'] = $this->request->data['OrderDetail']['policy_type'][$key];
									$order_details['OrderDetail']['policy_id'] = $this->request->data['OrderDetail']['policy_id'][$key];
									//$total_product_data[] = $order_details;
									$total_product_data = $order_details;
									
								} else {
									$sales_qty = $this->request->data['OrderDetail']['sales_qty'][$key];
									if (!empty($sales_qty)) {
										
										if ($product_details['Product']['is_virtual'] == 1) {
											$product_id = $bouns_order_details['OrderDetail']['virtual_product_id'] = $val;
											$bouns_order_details['OrderDetail']['product_id'] = $product_details['Product']['parent_id'];
										} else {
											$bouns_order_details['OrderDetail']['virtual_product_id'] = 0;
											$product_id = $bouns_order_details['OrderDetail']['product_id'] = $product_details['Product']['id'];
										}

										$bouns_order_details['OrderDetail']['order_id'] = $order_id;
										$bouns_order_details['OrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
										$sales_price = $bouns_order_details['OrderDetail']['price'] = 0;
										$bouns_order_details['OrderDetail']['sales_qty'] = $sales_qty;
										$bouns_order_details['OrderDetail']['bonus_qty'] = $this->request->data['OrderDetail']['sales_qty'][$key];
										$bouns_order_details['OrderDetail']['bonus_product_id'] = $product_id;
										$bouns_order_details['OrderDetail']['is_bonus'] = 1;
										$bouns_order_details['OrderDetail']['deliverd_qty'] = $sales_qty;
										//$bouns_order_details['OrderDetail']['deliverd_qty'] = $this->request->data['OrderDetail']['deliverd_qty'][$key];
										$order_date = date('Y-m-d', strtotime($this->request->data['OrderProces']['order_date']));
										$bouns_order_details['OrderDetail']['discount_type'] = $this->request->data['OrderDetail']['disccount_type'][$key];
										$bouns_order_details['OrderDetail']['discount_amount'] = $this->request->data['OrderDetail']['discount_amount'][$key];
										$bouns_order_details['OrderDetail']['policy_type'] = $this->request->data['OrderDetail']['policy_type'][$key];
										$bouns_order_details['OrderDetail']['policy_id'] = $this->request->data['OrderDetail']['policy_id'][$key];
										$bouns_order_details['OrderDetail']['is_bonus'] = 1;
										if ($this->request->data['OrderDetail']['is_bonus'][$key] == 3)
											$bouns_order_details['OrderDetail']['is_bonus'] = 3;
										$selected_set = '';
										if (isset($this->request->data['OrderDetail']['selected_set'][$bouns_order_details['OrderDetail']['policy_id']])) {
											$selected_set = $this->request->data['OrderDetail']['selected_set'][$bouns_order_details['OrderDetail']['policy_id']];
										}
										$other_info = array();
										if ($selected_set) {
											$other_info = array(
												'selected_set' => $selected_set
											);
										}
										if ($other_info)
											$bouns_order_details['OrderDetail']['other_info'] = json_encode($other_info);
									   // $total_product_data[] = $bouns_order_details;
										$total_product_data = $bouns_order_details;
									   
									   
									}
								}

								if(empty($total_product_data))
									continue;



								if (array_key_exists('save', $this->request->data)) {

									$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
									$product_list = Set::extract($products, '{n}.Product');

									$punits_pre = $this->search_array($product_id, 'id', $product_list);
									$measurement_unit_id = $this->request->data['OrderDetail']['measurement_unit_id'][$key] ? $this->request->data['OrderDetail']['measurement_unit_id'][$key] : $punits_pre['sales_measurement_unit_id'];
								   
									//-----------------------stock update new--------------\\
									$given_stock = $this->request->data['OrderDetail']['product_batch_given_stock'][$key];
									//$i= 0;
									foreach ($this->request->data['OrderDetail']['product_batch_current_inventory_id'][$key] as $pbkey => $current_invenoty) {
										$qty_sales = $given_stock[$pbkey];
										$current_invenoty_id = $current_invenoty;
										if(!empty($qty_sales) AND $qty_sales > 0){

											if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
												$base_quantity = $qty_sales;
											} else {
												$base_quantity = $this->unit_convert($product_id, $measurement_unit_id, $qty_sales);
											}
											
											
											$stock_deducted = $this->new_update_current_inventory($current_invenoty_id, $base_quantity);
											if (!$stock_deducted) {
												throw new Exception();
											}
											
											//$i++;

										}
									}

									//--------------end-------------------\\

								}
								
							   
								//--------one by one insert order details--------\\
								$this->OrderDetail->create();
								if (!$this->OrderDetail->save($total_product_data)) {
									throw new Exception();
								}else{ 

									$order_details_id = $this->OrderDetail->getLastInsertId();
									
									$given_stock = $this->request->data['OrderDetail']['product_batch_given_stock'][$key];
									$insertProductBatch = array();
									foreach ($this->request->data['OrderDetail']['product_batch_current_inventory_id'][$key] as $pbkey => $current_invenoty) {
										$bgivenqty = $given_stock[$pbkey];
										
										if(!empty($bgivenqty) and $bgivenqty > 0){
										
											$batch_info_data['ProductBatchInfo']['current_inventory_id'] = $current_invenoty;
											$batch_info_data['ProductBatchInfo']['order_details_id'] = $order_details_id;
											$batch_info_data['ProductBatchInfo']['memo_details_id'] = 0;
											$batch_info_data['ProductBatchInfo']['product_id'] = $val;
											$batch_info_data['ProductBatchInfo']['given_stock'] = $bgivenqty;
											$batch_info_data['ProductBatchInfo']['created_at'] = $this->current_datetime();
											$batch_info_data['ProductBatchInfo']['created_by'] = $this->UserAuth->getUserId();
											$batch_info_data['ProductBatchInfo']['updated_at'] = $this->current_datetime();
											$batch_info_data['ProductBatchInfo']['updated_by'] = $this->UserAuth->getUserId();
						
											$insertProductBatch[] = $batch_info_data;
										
										}
					
									}

									
									
									/* if(!empty($insertProductBatch)){
										$this->ProductBatchInfo->saveAll($insertProductBatch); 
									} */

									if (!$this->ProductBatchInfo->saveAll($insertProductBatch)) {
										throw new Exception();
									}

									
								}


								//--------end--------\\

							}
							
							/* if (!$this->OrderDetail->saveAll($total_product_data)) {
								throw new Exception();
							} */
						   
						}
					}

					if (array_key_exists('save', $this->request->data)) {
						/************ Memo create date: 04-09-2019 *****************/
						/***************Memo Create for no Confirmation Type **************/
						$this->loadModel('Memo');
						$this->loadModel('OrderDetail');
						$this->loadModel('Order');
						$this->loadModel('Collection');
						$order_info = $this->Order->find('first', array(
							'conditions' => array('Order.id' => $order_id),
							'recursive' => -1
						));

						$order_detail_info = $this->OrderDetail->find('all', array(
							'conditions' => array('OrderDetail.order_id' => $order_id),
							'recursive' => -1
						));
						//pr($order_detail_info);//die();
						$memo = array();
						$memo['sales_person_id'] = $order_info['Order']['sales_person_id'];
						$memo['entry_date'] = $this->current_datetime();
						$memo['memo_date'] =  date('Y-m-d');

						$memo['office_id'] = $order_info['Order']['office_id'];
						$memo['sale_type_id'] = 1;
						$memo['territory_id'] = $order_info['Order']['territory_id'];
						$memo['thana_id'] = $order_info['Order']['thana_id'];
						$memo['market_id'] = $order_info['Order']['market_id'];
						$memo['outlet_id'] = $order_info['Order']['outlet_id'];

						$memo['memo_no'] = $order_info['Order']['order_no'];
						$memo['gross_value'] = $order_info['Order']['gross_value'];
						$memo['cash_recieved'] = $order_info['Order']['gross_value'];
						$memo['is_active'] = $order_info['Order']['is_active'];
						$memo['w_store_id'] = $order_info['Order']['w_store_id'];
						if (array_key_exists('save', $this->request->data)) {
							$memo['status'] = 2;
						} else {
							$memo['status'] = 0;
							$this->Memo->create();
						}
						$memo['memo_time'] = $this->current_datetime();
						$memo['sales_person_id'] = $order_info['Order']['sales_person_id'];
						$memo['from_app'] = 0;
						$memo['action'] = 1;
						$memo['is_distributor'] = 1;
						$memo['is_program'] = 0;


						$memo['memo_reference_no'] = $order_info['Order']['memo_reference_no'];

						$memo['created_at'] = $this->current_datetime();
						$memo['created_by'] = $this->UserAuth->getUserId();
						$memo['updated_at'] = $this->current_datetime();
						$memo['updated_by'] = $this->UserAuth->getUserId();
						$memo['total_discount'] = $order_info['Order']['total_discount'];
						/* if (array_key_exists('save', $this->request->data)) {
						$this->loadModel('Memo');
						$memos = $this->Memo->find('first', array('conditions' => array('Memo.memo_no like' => "%" . $order_no . "%"), 'order' => 'Memo.id DESC'));
						$memo_id = $memos['Memo']['id'];
						$this->admin_deletememo($memo_id, 0);
					} */
						//pr($memo);die();

						if (!$this->Memo->save($memo)) {
							throw new Exception();
						} else {
							// fwrite($myfile, "\n" . $this->current_datetime() . ':' . $order_no . ' : Memo  created :');
							$memo_id = $this->Memo->getLastInsertId();
							$memo_info_arr = $this->Memo->find('first', array(
								'conditions' => array(
									'Memo.id' => $memo_id
								)
							));

							if ($memo_id) {
								if (!empty($order_detail_info[0]['OrderDetail'])) {
									$this->loadModel('MemoDetail');
									$total_product_data = array();
									$memo_details = array();
									$bonus_memo_details = array();
									$memodetails_ids = array();
									foreach ($order_detail_info as $order_detail_result) {

										if ($order_detail_result['OrderDetail']['deliverd_qty'] > 0) {

											$orderdetailsid = $order_detail_result['OrderDetail']['id'];
											$product_id = $order_detail_result['OrderDetail']['product_id'];

											$virtual_product_id = $order_detail_result['OrderDetail']['virtual_product_id'];

											$memo_details['MemoDetail']['memo_id'] = $memo_id;

											if ($virtual_product_id > 0) {
												$product_id = $virtual_product_id;
											}


											$product_details = $this->Product->find('first', array(
												'fields' => array('id', 'is_virtual', 'parent_id'),
												'conditions' => array('Product.id' => $product_id),
												'recursive' => -1
											));

											//echo '<pre>';print_r($product_details);

											if ($product_details['Product']['is_virtual'] == 1) {
												$memo_details['MemoDetail']['virtual_product_id'] = $product_id;
												$memo_details['MemoDetail']['product_id'] = $product_details['Product']['parent_id'];
											} else {
												$memo_details['MemoDetail']['virtual_product_id'] = 0;
												$memo_details['MemoDetail']['product_id'] = $product_details['Product']['id'];
											}

											//echo '<pre>';print_r($memo_details);exit;

											//$memo_details['MemoDetail']['product_id'] = $product_id;

											$memo_details['MemoDetail']['measurement_unit_id'] = $order_detail_result['OrderDetail']['measurement_unit_id'];
											$memo_details['MemoDetail']['actual_price'] = $order_detail_result['OrderDetail']['price'];
											$memo_details['MemoDetail']['price'] = $order_detail_result['OrderDetail']['price'] - $order_detail_result['OrderDetail']['discount_amount'];
											$memo_details['MemoDetail']['sales_qty'] = $order_detail_result['OrderDetail']['deliverd_qty'];

											$memo_details['MemoDetail']['product_price_id'] = $order_detail_result['OrderDetail']['product_price_id'];
											$memo_details['MemoDetail']['bonus_qty'] = NULL;
											$memo_details['MemoDetail']['offer_id'] = $order_detail_result['OrderDetail']['offer_id'];
											$memo_details['MemoDetail']['bonus_product_id'] = NULL;
											$memo_details['MemoDetail']['bonus_id'] = NULL;
											$memo_details['MemoDetail']['bonus_scheme_id'] = NULL;
											$memo_details['MemoDetail']['price_combination_id'] = $order_detail_result['OrderDetail']['price_combination_id'];
											$memo_details['MemoDetail']['product_combination_id'] = $order_detail_result['OrderDetail']['product_combination_id'];
											$memo_details['MemoDetail']['is_bonus'] = $order_detail_result['OrderDetail']['is_bonus'];
											$memo_details['MemoDetail']['deliverd_qty'] = $order_detail_result['OrderDetail']['deliverd_qty'];

											$memo_details['MemoDetail']['discount_type'] = $order_detail_result['OrderDetail']['discount_type'];
											$memo_details['MemoDetail']['discount_amount'] = $order_detail_result['OrderDetail']['discount_amount'];
											$memo_details['MemoDetail']['policy_type'] = $order_detail_result['OrderDetail']['policy_type'];
											$memo_details['MemoDetail']['policy_id'] = $order_detail_result['OrderDetail']['policy_id'];
											$memo_details['MemoDetail']['is_bonus'] = 0;
											if ($order_detail_result['OrderDetail']['is_bonus'] == 3)
												$memo_details['MemoDetail']['is_bonus'] = 3;
											//$total_product_data[] = $memo_details;
											$total_product_data = $memo_details;

											if ($order_detail_result['OrderDetail']['bonus_qty'] > 0 && $order_detail_result['OrderDetail']['is_bonus'] == 0) {

												$product_id = $order_detail_result['OrderDetail']['bonus_product_id'];
												$bonus_memo_details['MemoDetail']['memo_id'] = $memo_id;

												$bproduct_details = $this->Product->find('first', array(
													'fields' => array('id', 'is_virtual', 'parent_id'),
													'conditions' => array('Product.id' => $product_id),
													'recursive' => -1
												));

												if ($bproduct_details['Product']['is_virtual'] == 1) {
													$bonus_memo_details['MemoDetail']['virtual_product_id'] = $product_id;
													$bonus_memo_details['MemoDetail']['product_id'] = $bproduct_details['Product']['parent_id'];
												} else {
													$bonus_memo_details['MemoDetail']['virtual_product_id'] = 0;
													$bonus_memo_details['MemoDetail']['product_id'] = $bproduct_details['Product']['id'];
												}

												// $bonus_memo_details['MemoDetail']['product_id'] = $product_id;

												$bonus_memo_details['MemoDetail']['measurement_unit_id'] = $order_detail_result['OrderDetail']['measurement_unit_id'];
												$bonus_memo_details['MemoDetail']['price'] = 0;
												$bonus_memo_details['MemoDetail']['sales_qty'] = $order_detail_result['OrderDetail']['bonus_qty'];

												$bonus_memo_details['MemoDetail']['product_price_id'] = $order_detail_result['OrderDetail']['product_price_id'];
												$bonus_memo_details['MemoDetail']['bonus_qty'] = $order_detail_result['OrderDetail']['bonus_qty'];
												$bonus_memo_details['MemoDetail']['offer_id'] = $order_detail_result['OrderDetail']['offer_id'];
												$bonus_memo_details['MemoDetail']['bonus_product_id'] = $order_detail_result['OrderDetail']['bonus_product_id'];
												$bonus_memo_details['MemoDetail']['bonus_id'] = $order_detail_result['OrderDetail']['bonus_id'];
												$bonus_memo_details['MemoDetail']['bonus_scheme_id'] = $order_detail_result['OrderDetail']['bonus_scheme_id'];
												$bonus_memo_details['MemoDetail']['price_combination_id'] = $order_detail_result['OrderDetail']['price_combination_id'];
												$bonus_memo_details['MemoDetail']['product_combination_id'] = $order_detail_result['OrderDetail']['product_combination_id'];
												$bonus_memo_details['MemoDetail']['is_bonus'] = 1;
												$bonus_memo_details['MemoDetail']['deliverd_qty'] = $order_detail_result['OrderDetail']['bonus_qty'];

												//$total_product_data[] = $bonus_memo_details;
												$total_product_data = $bonus_memo_details;
											}
											
											
											//--------------one by one memo details insert---------\\
											$this->MemoDetail->create();
											if (!$this->MemoDetail->save($total_product_data)) {
												throw new Exception();
											}else{
												
												
												//$orderdetailsid
												
												$memo_details_id = $this->MemoDetail->getLastInsertId();
												
												$memodetails_ids[$orderdetailsid] = $memo_details_id;
												
												//$product_batch_sql = "update product_batch_infos set memo_details_id=$memo_details_id,  updated_by=$up_userid, updated_at='$up_time' where  order_details_id=$orderdetailsid";
												//echo $product_batch_sql;
												
												/*$pbinfo_ids = $this->ProductBatchInfo->find('list', array(
													'conditions'=>array('ProductBatchInfo.order_details_id' => $orderdetailsid),
													'fields'=>array('ProductBatchInfo.id', 'ProductBatchInfo.order_details_id'),
													'recursive'=>-1
												));

												if(!empty($pbinfo_ids)){
													
													$pro_up_ids = implode(", ", array_keys($pbinfo_ids));
													if (!$this->MemoDetail->query(" update product_batch_infos set memo_details_id=$memo_details_id,  updated_by=$up_userid, updated_at='$up_time' where  id IN($pro_up_ids) ")) {
														throw new Exception();
													}
													
												} */
												
												/*
												
												try{
													$this->MemoDetail->query($product_batch_sql);
												}catch(Exception $e){
													
													throw new Exception($e);
												} */
												

												/*if(!$this->ProductBatchInfo->updateAll(
													array(
														'ProductBatchInfo.memo_details_id' => $memo_details_id,
														'ProductBatchInfo.updated_by' => "'" . $this->UserAuth->getUserId() . "'",
														'ProductBatchInfo.updated_at' => "'" . $this->current_datetime() . "'"
													),
													array('ProductBatchInfo.order_details_id' => $orderdetailsid)
												)){
													throw new Exception(); 
												}*/

											}

											//-----------end----------------\\
											
										}

										


									}

									/* if (!$this->MemoDetail->saveAll($total_product_data)) {
										throw new Exception();
									} */
									// fwrite($myfile, "\n" . $this->current_datetime() . ':' . $order_no . ' : Memo details created :');
									//pr($total_product_data);//die();
								}
								$order_outlet_id = $order_info['Order']['outlet_id'];
								$this->loadmodel('DistOutletMap');
								$outlet_info = $this->DistOutletMap->find('first', array(
									'conditions' => array('DistOutletMap.outlet_id' => $order_outlet_id),
									'fields' => array('DistOutletMap.dist_distributor_id'),
								));
								$distibuter_id = $outlet_info['DistOutletMap']['dist_distributor_id'];
								$this->loadmodel('DistStore');
								$dist_store_id = $this->DistStore->find('first', array(
									'conditions' => array('DistStore.dist_distributor_id' => $distibuter_id),
									'fields' => array('DistStore.id'),
								));
								/************* create Challan *************/



								/*****************Create Chalan and *****************/
								$this->loadModel('DistChallan');
								$this->loadModel('DistChallanDetail');
								$this->loadModel('CurrentInventory');
								//$company_id  =$this->Session->read('Office.company_id');
								$office_id  = $this->request->data['OrderProces']['office_id'];
								$store_id = $this->request->data['OrderProces']['w_store_id'];
								//$challan['company_id']=$company_id;
								$challan['office_id'] = $office_id;
								$challan['memo_id'] = $memo_info_arr['Memo']['id'];
								$challan['memo_no'] = $memo_info_arr['Memo']['memo_no'];
								$challan['challan_no'] = $memo_info_arr['Memo']['memo_no'];
								$challan['receiver_dist_store_id'] = $dist_store_id['DistStore']['id'];
								$challan['receiving_transaction_type'] = 2;
								$challan['received_date'] = '';
								$challan['challan_date'] = date('Y-m-d');
								$challan['dist_distributor_id'] = $distibuter_id;
								$challan['challan_referance_no'] = '';
								$challan['challan_type'] = "";
								$challan['remarks'] = 0;
								$challan['status'] = 1;
								$challan['so_id'] = $order_info['Order']['sales_person_id'];
								$challan['is_close'] = 0;
								$challan['inventory_status_id'] = 2;
								$challan['transaction_type_id'] = 2;
								$challan['sender_store_id'] = $store_id;
								$challan['created_at'] = $this->current_datetime();
								$challan['created_by'] = $this->UserAuth->getUserId();
								$challan['updated_at'] = $this->current_datetime();
								$challan['updated_by'] = $this->UserAuth->getUserId();
								//pr();die();
								$this->DistChallan->create();
								// pr($challan);
								if (!$this->DistChallan->save($challan)) {
									throw new Exception();
								} else {
									// fwrite($myfile, "\n" . $this->current_datetime() . ':' . $order_no . ' :challan created :');
									$challan_id = $this->DistChallan->getLastInsertId();
									if ($challan_id) {

										$challan_no = 'Ch-' . $distibuter_id . '-' . date('Y') . '-' . $challan_id;

										$challan_data['id'] = $challan_id;
										$challan_data['challan_no'] = $challan_no;

										$this->DistChallan->save($challan_data);
									}
									$product_list = $this->request->data['OrderDetail'];
									//pr($product_list);
									if (!empty($product_list['product_id'])) {
										$data_array = array();

										foreach ($product_list['product_id'] as $key => $val) {
											if ($product_list['product_id'][$key] != '') {
												if ($product_list['Price'][$key] != 0 && !empty($product_list['Price'][$key])) {

													if ($product_list['deliverd_qty'][$key] > 0) {
														if (!empty($val)) {
															$inventories = $this->CurrentInventory->find('first', array(
																'conditions' => array(
																	'CurrentInventory.product_id' => $val,
																	'CurrentInventory.store_id' => $store_id,
																),
																'recursive' => -1,
															));
															$batch_no = $inventories['CurrentInventory']['batch_number'];

															$data['DistChallanDetail']['challan_id'] = $this->DistChallan->id;

															$product_details = $this->Product->find('first', array(
																'fields' => array('id', 'is_virtual', 'parent_id'),
																'conditions' => array('Product.id' => $val),
																'recursive' => -1
															));

															if ($product_details['Product']['is_virtual'] == 1) {
																$data['DistChallanDetail']['virtual_product_id'] = $val;
																$data['DistChallanDetail']['product_id'] = $product_details['Product']['parent_id'];
															} else {
																$data['DistChallanDetail']['virtual_product_id'] = 0;
																$data['DistChallanDetail']['product_id'] = $product_details['Product']['id'];
															}

															//$data['DistChallanDetail']['product_id'] = $val;

															$data['DistChallanDetail']['measurement_unit_id'] = $product_list['measurement_unit_id'][$key];
															$data['DistChallanDetail']['challan_qty'] = $product_list['deliverd_qty'][$key];
															$data['DistChallanDetail']['received_qty'] = $product_list['deliverd_qty'][$key];
															$data['DistChallanDetail']['batch_no'] = $batch_no;
															//$data['DistChallanDetail']['remaining_qty'] =$product_list['remaining_qty'][$key];
															$data['DistChallanDetail']['price'] = $product_list['Price'][$key];
															/* if(!empty($product_list['bonus_product_id'][$key])){
															$data['DistChallanDetail']['is_bonus'] = 1;
															 }else{*/
															$data['DistChallanDetail']['is_bonus'] = 0;
															//}

															$data['DistChallanDetail']['source'] = "";
															$data['DistChallanDetail']['remarks'] = $this->request->data['OrderDetail']['remarks'][$key];

															//$data['ChallanDetail']['expire_date'] = (($this->request->data['expire_date'][$key] != '' ) ? Date('Y-m-d', strtotime($this->request->data['expire_date'][$key])) : Null);
															$date = (($this->request->data['OrderProces']['order_date'][$key] != ' ' && $this->request->data['OrderProces']['order_date'][$key] != 'null' && $this->request->data['OrderProces']['order_date'][$key] != '') ? explode('-', $this->request->data['OrderProces']['order_date'][$key]) : '');
															if (!empty($date[1])) {
																$date[0] = date('m', strtotime($date[0]));
																$a_date = date('y-m-d', mktime(0, 0, 0, $date[0], 1, $date[1]));
																$data['DistChallanDetail']['expire_date'] = date("Y-m-t", strtotime($a_date));
															} else {
																$data['DistChallanDetail']['expire_date'] = '';
															}
															$data['DistChallanDetail']['inventory_status_id'] = 1;  // set 1 for Sound product
															//$data['ChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
														}
														$data_array[] = $data;
														if ($product_list['bonus_product_qty'][$key] != 0) {

															$inventories = $this->CurrentInventory->find('first', array(
																'conditions' => array(
																	'CurrentInventory.product_id' => $product_list['bonus_product_id'][$key],
																	'CurrentInventory.store_id' => $store_id,
																),
																'recursive' => -1,
															));
															$batch_no = $inventories['CurrentInventory']['batch_number'];
															$bonus_data['DistChallanDetail']['batch_no'] = $batch_no;
															$bonus_data['DistChallanDetail']['challan_id'] = $this->DistChallan->id;

															$product_details = $this->Product->find('first', array(
																'fields' => array('id', 'is_virtual', 'parent_id'),
																'conditions' => array('Product.id' => $product_list['bonus_product_id'][$key]),
																'recursive' => -1
															));

															if ($product_details['Product']['is_virtual'] == 1) {
																$bonus_data['DistChallanDetail']['virtual_product_id'] = $product_list['bonus_product_id'][$key];
																$bonus_data['DistChallanDetail']['product_id'] = $product_details['Product']['parent_id'];
															} else {
																$bonus_data['DistChallanDetail']['virtual_product_id'] = 0;
																$bonus_data['DistChallanDetail']['product_id'] = $product_details['Product']['id'];
															}

															// $bonus_data['DistChallanDetail']['product_id'] = $product_list['bonus_product_id'][$key];

															$bonus_data['DistChallanDetail']['measurement_unit_id'] = $product_list['measurement_unit_id'][$key];
															$bonus_data['DistChallanDetail']['challan_qty'] = $product_list['bonus_product_qty'][$key];
															$bonus_data['DistChallanDetail']['received_qty'] = $product_list['bonus_product_qty'][$key];
															$bonus_data['DistChallanDetail']['price'] = 0;
															$bonus_data['DistChallanDetail']['is_bonus'] = 1;
															//pr($bonus_data);
															$data_array[] = $bonus_data;
														}
													}
												} else {
													if (!empty($product_list['sales_qty'][$key])) {
														if ($product_list['Price'][$key] == 0) {
															$inventories = $this->CurrentInventory->find('first', array(
																'conditions' => array(
																	'CurrentInventory.product_id' => $val,
																	'CurrentInventory.store_id' => $store_id,
																),
																'recursive' => -1,
															));
															$bonus_data = array();
															$batch_no = $inventories['CurrentInventory']['batch_number'];
															$bonus_data['DistChallanDetail']['challan_id'] = $this->DistChallan->id;

															$product_details = $this->Product->find('first', array(
																'fields' => array('id', 'is_virtual', 'parent_id'),
																'conditions' => array('Product.id' => $val),
																'recursive' => -1
															));

															if ($product_details['Product']['is_virtual'] == 1) {
																$bonus_data['DistChallanDetail']['virtual_product_id'] = $val;
																$bonus_data['DistChallanDetail']['product_id'] = $product_details['Product']['parent_id'];
															} else {
																$bonus_data['DistChallanDetail']['virtual_product_id'] = 0;
																$bonus_data['DistChallanDetail']['product_id'] = $product_details['Product']['id'];
															}

															//$bonus_data['DistChallanDetail']['product_id'] = $val;

															$bonus_data['DistChallanDetail']['challan_qty'] = $product_list['sales_qty'][$key];
															$bonus_data['DistChallanDetail']['received_qty'] = $product_list['sales_qty'][$key];
															$bonus_data['DistChallanDetail']['batch_no'] = $batch_no;
															$bonus_data['DistChallanDetail']['measurement_unit_id'] = $product_list['measurement_unit_id'][$key];
															$bonus_data['DistChallanDetail']['price'] = $product_list['Price'][$key];
															$bonus_data['DistChallanDetail']['is_bonus'] = 1;

															$data_array[] = $bonus_data;
														}
													}
												}
											}
										}
										//pr($data_array);die();
										if (!$this->DistChallanDetail->saveAll($data_array)) {
											throw new Exception();
										}
										// fwrite($myfile, "\n" . $this->current_datetime() . ':' . $order_no . ' :challan Details created :');
										//pr($data_array);die();
									}
								}

								/************* end Challan *************/

								/***************** Balance Deduct********************/

								if ($dist_balance_info) {
									$balance = $dist_balance - $credit_amount;
								} else {
									$balance = 0;
								}

								if ($balance < 1) {
									if ($dist_limit_info) {
										$dist_limit_data['DistDistributorLimit']['id'] =  $dist_limit_info['DistDistributorLimit']['id'];
										$dist_limit_data['DistDistributorLimit']['max_amount'] = $dist_limit_info['DistDistributorLimit']['max_amount'] - $balance;
										$limit_data_history = array();

										if ($this->DistDistributorLimit->save($dist_limit_data)) {

											$limit_data_history['dist_distributor_limit_id'] = $dist_limit_info['DistDistributorLimit']['id'];
											$limit_data_history['max_amount'] = $dist_limit_info['DistDistributorLimit']['max_amount'] - $balance;
											$limit_data_history['transaction_amount'] = $balance * (-1);
											$limit_data_history['transaction_type'] = 0;
											$limit_data_history['is_active'] = 1;

											$this->DistDistributorLimitHistory->create();
											if (!$this->DistDistributorLimitHistory->save($limit_data_history)) {
												throw new Exception();
											}

											$balance = 0;
										}
									}
								}

								$dealer_balance_data = array();
								$dealer_balance = array();
								$dealer_balance['id'] = $dist_balance_info['DistDistributorBalance']['id'];
								$dealer_balance['office_id'] = $dist_balance_info['DistDistributorBalance']['office_id'];
								$dealer_balance['dist_distributor_id'] = $dist_balance_info['DistDistributorBalance']['dist_distributor_id'];
								$dealer_balance['balance'] = $balance;

								if ($this->DistDistributorBalance->save($dealer_balance)) {
									$dealer_balance_data['dist_distributor_id'] = $distributor_id;
									$dealer_balance_data['dist_distributor_balance_id'] = $dist_balance_info['DistDistributorBalance']['id'];
									$dealer_balance_data['office_id'] = $this->request->data['OrderProces']['office_id'];
									$dealer_balance_data['memo_value'] = $this->request->data['Order']['gross_value'];
									$dealer_balance_data['balance'] = $balance;
									$dealer_balance_data['balance_type'] = 2;
									$dealer_balance_data['balance_transaction_type_id'] = 2;
									$dealer_balance_data['transaction_amount'] = $this->request->data['Order']['gross_value'];
                                    $dealer_balance_data['transaction_amount'] = $this->request->data['Order']['gross_value']-$this->request->data['Order']['total_discount'];
									$dealer_balance_data['transaction_date'] = date('Y-m-d');
									$dealer_balance_data['created_at'] = $this->current_datetime();
									$dealer_balance_data['created_by'] = $this->UserAuth->getUserId();
									$dealer_balance_data['updated_at'] = $this->current_datetime();
									$dealer_balance_data['updated_by'] = $this->UserAuth->getUserId();
									$this->DistDistributorBalanceHistory->create();
									if (!$this->DistDistributorBalanceHistory->save($dealer_balance_data)) {
										throw new Exception();
									}
								}
								// fwrite($myfile, "\n" . $this->current_datetime() . ':' . $order_no . ' : Balance Deducted :');
								/****************end  Balance Deduct*********************/
							}

							//start collection crate
							$collection_data = array();
							$collection_data['memo_id'] = $memo_info_arr['Memo']['id'];
							$collection_data['memo_no'] = $memo_info_arr['Memo']['memo_no'];
							$collection_data['so_id'] = $memo_info_arr['Memo']['sales_person_id'];

							$collection_data['memo_value'] = $memo_info_arr['Memo']['gross_value'];
							$collection_data['credit_or_due'] = $memo_info_arr['Memo']['credit_amount'];
							$collection_data['memo_date'] = $memo_info_arr['Memo']['memo_date'];
							$collection_data['type'] = 1;
							$collection_data['instrument_type'] = $order_info['Order']['instrument_type'];
							$collection_data['collectionAmount'] = $memo_info_arr['Memo']['gross_value'];
							$collection_data['collectionDate'] = date('Y-m-d');
							$collection_data['created_at'] = $this->current_datetime();

							$collection_data['territory_id'] = $memo_info_arr['Memo']['territory_id'];
							$collection_data['outlet_id'] = $memo_info_arr['Memo']['outlet_id'];
							$collection_data['market_id'] = $memo_info_arr['Memo']['market_id'];
							$collection_data['office_id'] = $memo_info_arr['Memo']['office_id'];

							$this->Collection->create();
							if (!$this->Collection->save($collection_data)) {
								throw new Exception();
							}
						}
						// fwrite($myfile, "\n" . $this->current_datetime() . ':' . $order_no . ' : Collection Created :');
						//die();        
						//end collection careate    

					}
				}
				//throw new Exception();
				$datasource->commit();
				$datasource1->commit();
				if (array_key_exists('save', $this->request->data)) {
					
					foreach($memodetails_ids as $key => $memodetailsid){
						
						$up_userid = $this->UserAuth->getUserId();
						$up_time = $this->current_datetime();
						
						$this->MemoDetail->query(" update product_batch_infos set memo_details_id=$memodetailsid,  updated_by=$up_userid, updated_at='$up_time' where  order_details_id =$key ");
						
					}
					
					$this->redirect(array('action' => 'index'));
				} else {
					$this->redirect(array('action' => 'edit', $id));
				}
			} catch (Exception $e) {
				//echo $e;
				$datasource->rollback();
				$datasource1->rollback();
				//echo '<pre>';print_r($insertProductBatch);
				//echo $e;exit;
				$this->Session->setFlash(__('SomeThing Went Wrong! Please Try Again.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}
			// fclose($myfile);
		}

		$office_parent_id = $this->UserAuth->getOfficeParentId();

		$current_date = date('d-m-Y', strtotime($this->current_date()));

		if ($office_parent_id == 0) {
			$product_list = $this->Product->find('list', array('order' => array('order' => 'asc')));
			$distributor_conditions = array();
		} else {
			$office_id = $this->UserAuth->getOfficeId();
			$product_list = $this->Product->find('list', array(
				'order' => array('order' => 'asc')
			));
			$distributor_conditions = array('DistOutletMap.office_id' => $office_id);
		}

		$distributers_list = $this->DistOutletMap->find('all', array(
			'conditions' => $distributor_conditions,
		));
		foreach ($distributers_list as $key => $value) {
			$distributers[$value['Outlet']['id']] = $value['DistDistributor']['name'];
		}

		$office_outlets = $distributers;
		$this->set(compact('distributers'));
		$this->set(compact('office_outlets'));
		$this->set(compact('market_list'));
		$this->set(compact('product_list'));

		/* ------- start get edit data -------- */
		$this->Order->recursive = 1;
		$options = array(
			'conditions' => array('Order.id' => $id)
		);
		$existing_record = $this->Order->find('first', $options);

		$store_id = $existing_record['Order']['w_store_id'];
		//pr($store_id);
		$conditions = array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.qty > ' => 0, 'inventory_status_id' => 1);
		$products_from_ci = $this->CurrentInventory->find('all', array(
			'fields' => array('DISTINCT CurrentInventory.product_id'),
			'conditions' => $conditions,
		));

		$product_ci = array();
		foreach ($products_from_ci as $each_ci) {
			$product_ci[] = $each_ci['CurrentInventory']['product_id'];
		}
		//pr($product_ci);die();
		/*$this->loadModel('DistProductPrice');
			$product_list_for_distributor = $this->DistProductPrice->find('all',array(
				//'conditions'=>array('DistProductPrice.product_id'=>$product_ci),
			));
			$product_lists=array();
			foreach ($product_list_for_distributor as $val) {
				$product_lists[]=$val['DistProductPrice']['product_id'];
			}*/

		$products = $this->Product->find('all', array(
			'conditions' => array(

				'Product.id' => $product_ci,
				'Product.is_distributor_product' => 1,
			), 'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'ParentProduct',
					'type' => 'left',
					'conditions' => 'ParentProduct.id=Product.parent_id'
				)
			),
			'fields' => array('Product.id as id', 'Product.name as name', 'ParentProduct.id as p_id', 'ParentProduct.name as p_name'),
			'recursive' => -1
		));
		$group_product = array();
		foreach ($products as $data) {
			if ($data[0]['p_id']) {
				$group_product[$data[0]['p_id']][] = $data[0]['id'];
			} else {
				$group_product[$data[0]['id']][] = $data[0]['id'];
			}
		}
		$product_array = array();
		foreach ($products as $data) {
			if ($data['0']['p_id'] && count($group_product[$data[0]['p_id']]) == 1) {
				$name = $data[0]['p_name'];
			} else {
				$name = $data[0]['name'];
			}
			$product_array[$data[0]['id']] = $name;
		}
		$products = $product_array;
		//pr($products);die();
		$this->set(compact('products'));
		$details_data = array();
		foreach ($existing_record['OrderDetail'] as $detail_val) {
			$product = $detail_val['virtual_product_id'] ? $detail_val['virtual_product_id'] : $detail_val['product_id'];
			//pr($product);
			if ($detail_val['product_combination_id']) {
				$combined_product = $this->CombinationDetailsV2->find('all', array(
					'conditions' => array('CombinationDetailsV2.combination_id' => $detail_val['product_combination_id']),
					'fields' => array('product_id'),
					'recursive' => -1
				));
				$combined_product = array_map(function ($val) {
					return $val['CombinationDetailsV2']['product_id'];
				}, $combined_product);
				$combined_product = implode(',', $combined_product);
				$detail_val['combined_product'] = $combined_product;
			}
			$details_data[] = $detail_val;
		}

		$existing_record['OrderDetail'] = $details_data;
		for ($i = 0; $i < count($details_data); $i++) {
			$measurement_unit_id = $details_data[$i]['measurement_unit_id'];
			if ($measurement_unit_id != 0) {
				$measurement_unit_name = $this->MeasurementUnit->find('all', array(
					'conditions' => array('MeasurementUnit.id' => $measurement_unit_id),
					'fields' => array('name'),
					'recursive' => -1
				));
				$existing_record['OrderDetail'][$i]['measurement_unit_name'] = $measurement_unit_name[0]['MeasurementUnit']['name'];
			}
		}
		if (!empty($existing_record['OrderDetail'])) {
			foreach ($existing_record['OrderDetail'] as $key => $value) {
				$OrderDetail_record[($value['virtual_product_id'] ? $value['virtual_product_id'] : $value['product_id'])] = $value;

				$product_info = $this->Product->find('first', array(
					'conditions' => array('Product.id' => ($value['virtual_product_id'] ? $value['virtual_product_id'] : $value['product_id'])),
					'recursive' => -1
				));
				$existing_record['OrderDetail'][$key]['product_type_id'] = $product_info['Product']['product_type_id'];
				$stoksinfo = $this->CurrentInventory->find('all', array(
					'conditions' => array(
						'CurrentInventory.store_id' => $existing_record['Order']['w_store_id'],
						'CurrentInventory.product_id' => ($value['virtual_product_id'] ? $value['virtual_product_id'] : $value['product_id'])
					),
					'fields' => array('sum(qty) as total'),
				));
				$total_qty = $stoksinfo[0][0]['total'];
				$sales_total_qty = $this->unit_convertfrombase($value['product_id'], $value['measurement_unit_id'], $total_qty);
				$existing_record['OrderDetail'][$key]['aso_stock_qty'] = $sales_total_qty;
				$stoks = $this->CurrentInventory->find('first', array(
					'conditions' => array(
						'CurrentInventory.store_id' => $existing_record['Order']['w_store_id'],
						'CurrentInventory.product_id' => ($value['virtual_product_id'] ? $value['virtual_product_id'] : $value['product_id'])
					),
					'fields' => array('Product.id', 'Product.name', 'CurrentInventory.qty'),
				));
				$productName = $this->Product->find('first', array(
					'conditions' => array('id' => ($value['virtual_product_id'] ? $value['virtual_product_id'] : $value['product_id'])),
					'fields' => array('id', 'name'),
					'recursive' => -1
				));
			}
		}

		$existing_record['office_id'] = $existing_record['SalesPerson']['office_id'];
		$existing_record['territory_id'] = $existing_record['Order']['territory_id'];
		$existing_record['market_id'] = $existing_record['Order']['market_id'];
		$existing_record['outlet_id'] = $existing_record['Order']['outlet_id'];
		$existing_record['order_time'] = date('d-m-Y', strtotime($existing_record['Order']['order_time']));
		$existing_record['order_date'] = date('d-m-Y', strtotime($existing_record['Order']['order_date']));
		$existing_record['order_no'] = $existing_record['Order']['order_no'];
		$existing_record['memo_reference_no'] = $existing_record['Order']['memo_reference_no'];

		$existing_record['driver_name'] = $existing_record['Order']['driver_name'];
		$existing_record['truck_no'] = $existing_record['Order']['truck_no'];
		
		$existing_record['reference_number'] = $existing_record['Order']['instrument_reference_no'];
		$existing_record['reference_number'] = $existing_record['Order']['instrument_reference_no'];
		$store_id = $this->Store->find('first', array(
			'fields' => array('Store.id'),
			'conditions' => array('Store.id' => $existing_record['Order']['w_store_id']),
			'recursive' => -1
		));

		$open_bonus_product = $this->Product->find('all', array(
			'fields' => array('Product.id', 'Product.name'),
			'joins' => array(
				array(
					'table' => 'current_inventories',
					'alias' => 'CurrentInventory',
					'type' => 'Inner',
					'conditions' => 'CurrentInventory.product_id=Product.id'
				),
				array(
					'table' => 'open_combination_products',
					'alias' => 'OpenCombinationProduct',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.product_id=Product.id'
				),
				array(
					'table' => 'open_combinations',
					'alias' => 'OpenCombination',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.combination_id=OpenCombination.id'
				),

			),
			'conditions' => array(
				'CurrentInventory.qty >' => 0,
				'CurrentInventory.store_id' => $store_id['Store']['id'],
				'OpenCombination.start_date <=' => date('Y-m-d'),
				'OpenCombination.end_date >=' => date('Y-m-d'),
				'OpenCombination.is_bonus' => 1
			),
			'recursive' => -1
		));
		$open_bonus_product_option = array();
		foreach ($open_bonus_product as $bonus_product) {
			$open_bonus_product_option[$bonus_product['Product']['id']] = $bonus_product['Product']['name'];
		}

		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$office_conditions = array();
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}

		$this->loadModel('Office');
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

		$office_id = $existing_record['office_id'];
		$territory_id = $existing_record['territory_id'];
		$market_id = $existing_record['market_id'];
		$outlet_id = $existing_record['outlet_id'];
		$outlets = array();


		$this->loadModel('Territory');
		$territories_list = $this->Territory->find('all', array(
			'conditions' => array('Territory.office_id' => $office_id),
			'order' => array('Territory.name' => 'asc')
		));
		$territories = array();
		foreach ($territories_list as $t_result) {
			$territories[$t_result['Territory']['id']] = $t_result['Territory']['name'] . ' (' . $t_result['SalesPerson']['name'] . ')';
		}


		$territory_ids = array($territory_id);


		$markets = $this->Market->find('list', array(
			'conditions' => array('territory_id' => $territory_ids),
			'order' => array('name' => 'asc')
		));

		$outlets = $distributers;

		$store_info = $this->Store->find('first', array(
			'conditions' => array(
				'Store.id' => $existing_record['Order']['w_store_id']
			),
			'recursive' => -1
		));
		$store_id = $store_info['Store']['id'];
		foreach ($existing_record['OrderDetail'] as $key => $single_product) {
			$total_qty_arr = $this->CurrentInventory->find('all', array(
				'conditions' => array('store_id' => $store_id, 'product_id' => $single_product['product_id']),
				'fields' => array('sum(qty) as total'),
				'recursive' => -1
			));

			$total_qty = $total_qty_arr[0][0]['total'];

			$sales_total_qty = $this->unit_convertfrombase($single_product['product_id'], $single_product['measurement_unit_id'], $total_qty);

			$existing_record['OrderDetail'][$key]['stock_qty'] = $sales_total_qty;
		}


		$products_from_ci = $this->CurrentInventory->find('all', array(
			'fields' => array('DISTINCT CurrentInventory.product_id'),
			'conditions' => array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.qty > ' => 0, 'inventory_status_id' => 1)
		));

		$product_ci = array();
		foreach ($products_from_ci as $each_ci) {
			$product_ci[] = $each_ci['CurrentInventory']['product_id'];
		}
		foreach ($existing_record['OrderDetail'] as $value) {
			$product_ci[] = $value['product_id'];
		}

		$product_ci_in = implode(",", $product_ci);
		$selected_bonus = array();
		$selected_set = array();
		$selected_policy_type = array();
		foreach ($existing_record['OrderDetail'] as $key => $value) {

			if ($value['virtual_product_id']) {
				$value['product_id'] = $value['virtual_product_id'];
			}

			$existing_product_category_id_array = $this->Product->find('all', array(
				'conditions' => array('Product.id' => $value['product_id']),
				'fields' => array('product_category_id'),
				'recursive' => -1
			));

			$existing_product_category_id = $existing_product_category_id_array[0]['Product']['product_category_id'];
			if ($value['discount_amount'] && $value['policy_type'] == 3) {
				$selected_policy_type[$value['policy_id']] = 1;
			}
			if ($value['is_bonus'] == 3) {
				if ($value['policy_type'] == 3) {
					$selected_policy_type[$value['policy_id']] = 2;
				}
				if ($value['other_info']) {
					$other_info = json_decode($value['other_info'], 1);
					$selected_set[$value['policy_id']] = $other_info['selected_set'];
					$selected_bonus[$value['policy_id']][$other_info['selected_set']][$value['product_id']] = $value['sales_qty'];
				} else {
					$selected_bonus[$value['policy_id']][1][$value['product_id']] = $value['sales_qty'];
				}
			}
		}
		$this->set(compact('selected_bonus', 'selected_set', 'selected_policy_type'));


		$distributor_info = $this->DistOutletMap->find('first', array(
			'conditions' => array(
				'DistOutletMap.office_id' => $office_id,
				'DistOutletMap.outlet_id' => $outlet_id,
			),
		));
		$distributor_id = $distributor_info['DistOutletMap']['dist_distributor_id'];
		$dist_balance_info = $this->DistDistributorBalance->find('first', array(
			'conditions' => array(
				'DistDistributorBalance.dist_distributor_id' => $distributor_id
			),
			'limit' => 1,
			'recursive' => -1
		));

		$existing_record['current_balance'] =  $dist_balance_info['DistDistributorBalance']['balance'];

		/* -------- create individual Product data --------- */
		global $cart_data, $matched_array;
		$cart_data = array();
		$matched_array = array();
		$qty_session_data = array();

		/* ---------creating prepare data---------- */
		$prepare_cart_data = array();
		if (!empty($filter_product['Product']['id'])) {
			$prepare_cart_data[$filter_product['Product']['id']]['Product'] = $filter_product['Product'];
			$prepare_cart_data[$filter_product['Product']['id']]['ProductPrice'] = $filter_product['ProductPrice'];
			foreach ($filter_product['Individual_slab'] as $individual_slab_val) {
				$prepare_cart_data[$filter_product['Product']['id']]['Individual_slab'][$individual_slab_val['min_qty']] = $individual_slab_val['price'];
			}
			if (!empty($filter_product['Combination'])) {
				$prepare_cart_data[$filter_product['Product']['id']]['Combination'] = $filter_product['Combination'];
			}
			if (!empty($filter_product['Combination_id'])) {
				/* ---- start ------- */
				$this->Combination->recursive = 2;
				$condition_value1['Combination.id'] = $filter_product['Combination_id']; //$filter_product['Combination']['id'];
				$combination_slab_data_option = array(
					'conditions' => array($condition_value1),
				);
				$combination_slab_data = $this->Combination->find('all', $combination_slab_data_option);
				/* ----- end -------- */

				$combination_slab = array();
				foreach ($combination_slab_data as $combine_group) {
					foreach ($combine_group['ProductCombination'] as $combine_val) {
						$combination_slab[$combine_val['min_qty']][$combine_val['product_id']] = $combine_val['Childrel']['price'];
					}
				}
				if (!empty($combination_slab)) {
					$prepare_cart_data[$filter_product['Product']['id']]['Combined_slab'] = $combination_slab;
				}
			}
		}
		/* ------ unset cart data ------- */

		$user_office_id = $this->UserAuth->getOfficeId();
		$sales_person_list = $this->SalesPerson->find('list', array(
			'conditions' => array('SalesPerson.office_id' => $user_office_id)
		));

		$this->loadModel('ProductMeasurement');
		$product_measurement_units = $this->ProductMeasurement->find('list', array('fields' => array('product_id', 'measurement_unit_id')));
		$product_category_id_list = $this->Product->find('list', array('fields' => array('id', 'product_category_id')));


		$this->set(compact('offices', 'territories', 'markets', 'outlets', 'current_date', 'product_list', 'sale_type_list', 'existing_record', 'office_parent_id', 'product_measurement_units', 'product_category_id_list', 'open_bonus_product_option'));
	}

	public function admin_edit_date($id = null)
	{
		ini_set('max_execution_time', 999999);
		ini_set('memory_limit', '-1');
		$this->loadmodel('InstrumentType');

		$this->set('page_title', 'Edit Order');
		$this->Order->id = $id;
		if (!$this->Order->exists($id)) {
			throw new NotFoundException(__('Invalid Order'));
		}

		$this->loadModel('ProductCombination');
		$this->loadModel('DistCombination');
		$this->loadModel('Combination');
		$this->Session->delete('cart_session_data');
		$this->Session->delete('matched_session_data');
		$this->Session->delete('combintaion_qty_data');

		$this->loadModel('Product');
		$this->loadModel('Memo');
		$this->loadModel('DistOutletMap');
		$this->loadModel('CurrentInventory');
		$this->loadModel('MeasurementUnit');
		$this->LoadModel('Product');
		$this->LoadModel('Store');
		$this->loadModel('ProductPrice');
		$this->loadModel('DistProductPrice');
		$this->loadModel('DistProductCombination');
		$this->loadModel('ProductCombination');
		$this->loadModel('Outlet');
		$this->loadModel('DistDistributorBalance');
		$this->loadModel('DistDistributorBalanceHistory');
		$this->loadModel('DistDistributorLimit');
		$this->loadModel('DistDistributorLimitHistory');
		$this->loadModel('CombinationDetailsV2');
		$count = 0;
		if ($this->request->is('post') || $this->request->is('put')) {

			//echo "<pre>";print_r($this->request->data);exit();

			$date = $this->request->data['OrderProces']['update_date'];

			$updatedate = date('Y-m-d', strtotime($date));


			$this->loadModel('Order');
			/*$order_info = $this->Order->find('first', array(
			'conditions' => array(
				'Order.id' => $id,
				'Order.confirm_status'=> 2,
				'Order.status'=> 2,
			),
			'recursive' => -1
			));
			if(!empty($order_info)){
				$this->Session->setFlash(__('This Order is Already Deliverd'), 'flash/error'); 
				$this->redirect(array('action' => 'index'));
			}*/
			//pr($this->request->data);die();
			$outlet_is_within_group = $this->outletGroupCheck($this->request->data['OrderProces']['distribut_outlet_id']);
			$product_is_injectable = $this->productInjectableCheck($this->request->data['OrderDetail']['product_id']);

			$order_id = $id;

			$this->loadModel('Store');
			$store_id_arr = $this->Store->find('first', array(
				'conditions' => array(
					'Store.office_id' => $this->request->data['OrderProces']['office_id'],
					'Store.store_type_id' => 2
				)
			));
			$store_id = $store_id_arr['Store']['id'];
			$stock_check = 0;
			$stock_available = 1;
			$m = "";
			$gross_amount = 0;
			$order_product_array_for_stock_check = array();
			$products = $this->Product->find('all', array('fields' => array('id', 'name', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
			$product_list = Set::extract($products, '{n}.Product');

			foreach ($this->request->data['OrderDetail']['product_id'] as $key => $val) {
				if ($val == NULL) {
					continue;
				}

				/*------ Stock Checking array preparation :start  --------------*/
				if (!isset($order_product_array_for_stock_check[$val])) {
					$order_product_array_for_stock_check[$val] = 0;
				}
				$punits_pre = $this->search_array($val, 'id', $product_list);
				$price = $this->request->data['OrderDetail']['Price'][$key];
				if ($price == 0.0) {
					$qty = $this->request->data['OrderDetail']['sales_qty'][$key];
				} else {
					$qty = $this->request->data['OrderDetail']['deliverd_qty'][$key];
				}
				$measurement_unit_id = isset($this->request->data['OrderDetail']['measurement_unit_id'][$key]) ? $this->request->data['OrderDetail']['measurement_unit_id'][$key] : $punits_pre['sales_measurement_unit_id'];
				$base_qty = 0;
				if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
					$base_qty = round($qty);
				} else {
					$base_qty = $this->unit_convert($val, $measurement_unit_id, $qty);
				}
				$bonus_base_qty = 0;
				if (isset($this->request->data['OrderDetail']['bonus_product_qty'][$key])) {
					$bonus_qty = $this->request->data['OrderDetail']['bonus_product_qty'][$key];
					$measurement_unit_id = isset($this->request->data['OrderDetail']['bonus_measurement_unit_id'][$key]) ? $this->request->data['OrderDetail']['measurement_unit_id'][$key] : $punits_pre['sales_measurement_unit_id'];
					if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
						$bonus_base_qty = round($bonus_qty);
					} else {
						$bonus_base_qty = $this->unit_convert($val, $measurement_unit_id, $bonus_qty);
					}
				}
				$order_product_array_for_stock_check[$val] += ($base_qty + $bonus_base_qty);
				/*------ Stock Checking array preparation :end  --------------*/

				if (!isset($this->request->data['OrderDetail']['deliverd_qty'][$key]) || $this->request->data['OrderDetail']['deliverd_qty'][$key] == 0) {
					continue;
				}

				$total_product_price = 0;
				$total_product_price = $qty * $price;
				$gross_amount = $gross_amount + $total_product_price;
			}
			$this->request->data['Order']['gross_value'] = $gross_amount;
			if (array_key_exists('save', $this->request->data)) {
				/*-------------------- Stock Checking : Start -------------------*/
				foreach ($order_product_array_for_stock_check as $product_id => $qty) {
					if ($qty == 0)
						continue;
					$punits_pre = $this->search_array($product_id, 'id', $product_list);
					$qty = $this->unit_convertfrombase($product_id, $punits_pre['sales_measurement_unit_id'], $qty);
					$stock_check = $this->stock_check($store_id, $product_id, $qty);
					if ($stock_check != 1) {
						$stock_available = 0;
						$msg_for_stock_unavailable = "Stock Not Available For <b>" . $punits_pre['name'] . '</b>';
						break;
					}
				}
				/*-------------------- Stock Checking : END --------------------*/
			}
			if ($stock_available == 0) {
				$this->Session->setFlash(__($msg_for_stock_unavailable), 'flash/error');
				$this->redirect(array('action' => 'edit', $id));
			}
			//$this->admin_delete($order_id, 0);
			$office_id = $this->request->data['OrderProces']['office_id'];
			$outlet_id = $this->request->data['OrderProces']['distribut_outlet_id'];

			$getOutlets = $this->Outlet->find('all', array(
				'conditions' => array('Outlet.id' => $outlet_id),
			));
			$outlet_info = $this->DistOutletMap->find('first', array(
				'conditions' => array('DistOutletMap.outlet_id' => $this->request->data['OrderProces']['distribut_outlet_id']),

			));

			$market_id = $getOutlets[0]['Outlet']['market_id'];
			$this->loadModel('Market');
			$market_info = $this->Market->find('first', array(
				'conditions' => array('Market.id' => $market_id),
				'fields' => 'Market.thana_id',
				'order' => array('Market.id' => 'asc'),
				'recursive' => -1,
			));

			$thana_id = $market_info['Market']['thana_id'];
			$distributor_id = $outlet_info['DistDistributor']['id'];

			$sales_person = $this->SalesPerson->find('list', array(
				'conditions' => array('territory_id' => $this->request->data['OrderProces']['territory_id']),
				'order' => array('name' => 'asc')
			));

			$this->request->data['OrderProces']['sales_person_id'] = key($sales_person);

			$this->request->data['OrderProces']['order_date'] = date('Y-m-d', strtotime($this->request->data['OrderProces']['order_date']));

			$orderData['id'] = $order_id;
			$orderData['office_id'] = $this->request->data['OrderProces']['office_id'];

			$orderData['territory_id'] = $this->request->data['OrderProces']['territory_id'];
			$orderData['market_id'] = $market_id;
			$orderData['outlet_id'] = $this->request->data['OrderProces']['distribut_outlet_id'];
			$orderData['entry_date'] = $this->current_datetime();
			$orderData['order_date'] = $this->request->data['OrderProces']['order_date'];
			$order_no = $orderData['order_no'] = $this->request->data['OrderProces']['order_no'];
			$orderData['gross_value'] = $this->request->data['Order']['gross_value'] - $this->request->data['Order']['total_discount'];
			$orderData['w_store_id'] = $this->request->data['OrderProces']['w_store_id'];
			$orderData['is_active'] = 1;

			$orderData['order_time'] = $this->current_datetime();
			$orderData['order_reference_no'] = $this->request->data['OrderProces']['order_reference_no'];
			$orderData['sales_person_id'] = $this->request->data['OrderProces']['sales_person_id'];
			$orderData['from_app'] = 0;
			$orderData['action'] = 1;
			$orderData['confirmed'] = 1;
			$orderData['order_reference_no'] = $this->request->data['OrderProces']['order_reference_no'];


			$orderData['created_at'] = $this->current_datetime();
			$orderData['created_by'] = $this->UserAuth->getUserId();
			$orderData['updated_at'] = $this->current_datetime();
			$orderData['updated_by'] = $this->UserAuth->getUserId();


			$orderData['office_id'] = $office_id ? $office_id : 0;
			$orderData['thana_id'] = $thana_id ? $thana_id : 0;
			$orderData['total_discount'] = $this->request->data['Order']['total_discount'];

			$balance = 0;
			$balance = 0;
			$limit = 0;
			$dist_balance_info = array();
			$dealer_balance_info = array();
			$dist_limit_info = array();
			/*************************** Balance Check ***********************************/
			$dist_balance_info = array();
			$dist_balance_info = $this->DistDistributorBalance->find('first', array(
				'conditions' => array(
					'DistDistributorBalance.dist_distributor_id' => $distributor_id
				),
				'limit' => 1,
				'recursive' => -1
			));

			if (empty($dist_balance_info)) {
				$this->Session->setFlash(__('Please check Balance of This Distributor!!!'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}
			$credit_amount = $this->request->data['Order']['gross_value'] - $this->request->data['Order']['total_discount'];
			$dist_balance = $dist_balance_info['DistDistributorBalance']['balance'];

			if ($dist_balance < $credit_amount) {
				$this->Session->setFlash(__('Insufficient Balance of This Distributor!!!'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}
			/*************************** end Balance Check ***********************************/
			$this->request->data['OrderProces']['is_active'] = 1;

			$this->admin_delete($order_id, 0);

			if (array_key_exists('draft', $this->request->data)) {
				$orderData['status'] =  $this->request->data['OrderProces']['status'] = 2;
				$orderData['confirm_status'] =  $this->request->data['OrderProces']['confirm_status'] = 1;
				$message = "Order Has Been Saved as Draft";

				$is_execute = 0;
			} else {
				$message = "Order Has Been Saved";
				$orderData['status'] =  $this->request->data['OrderProces']['status'] = 2;
				$orderData['confirm_status'] = $this->request->data['OrderProces']['confirm_status'] = 2;
				$is_execute = 1;

				/*************************** Balance Check ********************************/
				$dist_limit_info = $this->DistDistributorLimit->find('first', array(
					'conditions' => array(
						'DistDistributorLimit.office_id' => $office_id,
						'DistDistributorLimit.dist_distributor_id' => $distributor_id,
					),
					'limit' => 1,
					'recursive' => -1
				));
				/*if($dist_limit_info){
					$dist_balance =$dist_balance + $dist_limit_info['DistDistributorLimit']['max_amount'];
				}*/

				$dealer_balance_info = $this->DistDistributorBalanceHistory->find('first', array(
					'conditions' => array(
						'DistDistributorBalanceHistory.dist_distributor_id' => $distributor_id,

					),
					'order' => 'DistDistributorBalanceHistory.id DESC',
					'recursive' => -1
				));
				/*************************** Balance Check End *********************************/
			}
			//$orderData['status'] = $this->request->data['OrderProces']['status'];
			//$orderData['confirm_status'] = $this->request->data['OrderProces']['confirm_status'];

			if ($this->Order->save($orderData)) {
				$order_info_arr = $this->Order->find('first', array(
					'conditions' => array(
						'Order.id' => $order_id
					)
				));

				if ($order_id) {
					$all_product_id = $this->request->data['OrderDetail']['product_id'];
					if (!empty($this->request->data['OrderDetail'])) {
						$total_product_data = array();
						$order_details = array();

						foreach ($this->request->data['OrderDetail']['product_id'] as $key => $val) {
							if ($val == NULL) {
								continue;
							}
							//$measurement_unit_id = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
							$sales_price = $this->request->data['OrderDetail']['Price'][$key];
							if ($sales_price != 0 && !empty($sales_price)) {
								$product_id = $order_details['OrderDetail']['product_id'] = $val;
								$order_details['OrderDetail']['order_id'] = $order_id;
								$order_details['OrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
								$sales_price = $order_details['OrderDetail']['price'] = $this->request->data['OrderDetail']['Price'][$key];
								$order_qty = $order_details['OrderDetail']['sales_qty'] = $this->request->data['OrderDetail']['sales_qty'][$key];
								$sales_qty = $order_details['OrderDetail']['deliverd_qty'] = $this->request->data['OrderDetail']['deliverd_qty'][$key];
								$order_details['OrderDetail']['product_price_id'] = $this->request->data['OrderDetail']['product_price_id'][$key];
								$order_details['OrderDetail']['remaining_qty'] = $order_qty - $order_details['OrderDetail']['deliverd_qty'];
								$order_details['OrderDetail']['challan_remarks'] = $this->request->data['OrderDetail']['remarks'][$key];
								$product_price_slab_id = 0;
								$order_details['OrderDetail']['product_price_id'] = $this->request->data['OrderDetail']['product_price_id'][$key];
								$order_details['OrderDetail']['product_combination_id'] = $this->request->data['OrderDetail']['combination_id'][$key];
								$order_details['OrderDetail']['bonus_qty'] = $this->request->data['OrderDetail']['bonus_product_qty'][$key];


								if ($this->request->data['OrderDetail']['bonus_product_id'][$key] != 0) {
									$b_p_id = $order_details['OrderDetail']['bonus_product_id'] = $this->request->data['OrderDetail']['bonus_product_id'][$key];
									$bonus_sales_qty = $this->request->data['OrderDetail']['bonus_product_qty'][$key];
									if (array_key_exists('save', $this->request->data)) {

										$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
										$product_list = Set::extract($products, '{n}.Product');

										$punits_pre = $this->search_array($b_p_id, 'id', $product_list);
										if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
											$bonus_base_quantity = $bonus_sales_qty;
										} else {
											$bonus_base_quantity = $this->unit_convert($b_p_id, $punits_pre['sales_measurement_unit_id'], $bonus_sales_qty);
										}

										$update_type = 'deduct';
										$this->update_current_inventory($bonus_base_quantity, $b_p_id, $store_id, $update_type, 11, date('Y-m-d'));
									}
								} else {
									$order_details['OrderDetail']['bonus_product_id'] = NULL;
								}

								//Start for bonus
								$order_date = date('Y-m-d', strtotime($this->request->data['OrderProces']['order_date']));
								$bonus_product_id = $this->request->data['OrderDetail']['bonus_product_id'];
								$bonus_product_qty = $this->request->data['OrderDetail']['bonus_product_qty'];
								$order_details['OrderDetail']['bonus_id'] = 0;
								$order_details['OrderDetail']['bonus_scheme_id'] = 0;
								if ($bonus_product_qty[$key] > 0) {
									$b_product_id = $bonus_product_id[$key];
									$bonus_result = $this->bouns_and_scheme_id_set($b_product_id, $order_date);
									$order_details['OrderDetail']['bonus_id'] = $bonus_result['bonus_id'];
								}

								$order_details['OrderDetail']['discount_type'] = $this->request->data['OrderDetail']['disccount_type'][$key];
								$order_details['OrderDetail']['discount_amount'] = $this->request->data['OrderDetail']['discount_amount'][$key];
								$order_details['OrderDetail']['policy_type'] = $this->request->data['OrderDetail']['policy_type'][$key];
								$order_details['OrderDetail']['policy_id'] = $this->request->data['OrderDetail']['policy_id'][$key];
								$total_product_data[] = $order_details;
							} else {
								$sales_qty = $this->request->data['OrderDetail']['sales_qty'][$key];
								if (!empty($sales_qty)) {
									$product_id = $bouns_order_details['OrderDetail']['product_id'] = $val;
									$bouns_order_details['OrderDetail']['order_id'] = $order_id;
									$bouns_order_details['OrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
									$sales_price = $bouns_order_details['OrderDetail']['price'] = 0;
									$bouns_order_details['OrderDetail']['sales_qty'] = $sales_qty;
									$bouns_order_details['OrderDetail']['bonus_qty'] = $this->request->data['OrderDetail']['sales_qty'][$key];
									$bouns_order_details['OrderDetail']['bonus_product_id'] = $product_id;
									$bouns_order_details['OrderDetail']['is_bonus'] = 1;
									$bouns_order_details['OrderDetail']['deliverd_qty'] = $sales_qty;
									$order_date = date('Y-m-d', strtotime($this->request->data['OrderProces']['order_date']));
									$bouns_order_details['OrderDetail']['discount_type'] = $this->request->data['OrderDetail']['disccount_type'][$key];
									$bouns_order_details['OrderDetail']['discount_amount'] = $this->request->data['OrderDetail']['discount_amount'][$key];
									$bouns_order_details['OrderDetail']['policy_type'] = $this->request->data['OrderDetail']['policy_type'][$key];
									$bouns_order_details['OrderDetail']['policy_id'] = $this->request->data['OrderDetail']['policy_id'][$key];
									$bouns_order_details['OrderDetail']['is_bonus'] = 1;
									if ($this->request->data['OrderDetail']['is_bonus'][$key] == 3)
										$bouns_order_details['OrderDetail']['is_bonus'] = 3;
									$selected_set = '';
									if (isset($this->request->data['OrderDetail']['selected_set'][$bouns_order_details['OrderDetail']['policy_id']])) {
										$selected_set = $this->request->data['OrderDetail']['selected_set'][$bouns_order_details['OrderDetail']['policy_id']];
									}
									if ($selected_set) {
										$other_info = array(
											'selected_set' => $selected_set
										);
									}
									if ($other_info)
										$bouns_order_details['OrderDetail']['other_info'] = json_encode($other_info);
									$total_product_data[] = $bouns_order_details;
								}
							}
							if (array_key_exists('save', $this->request->data)) {

								$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
								$product_list = Set::extract($products, '{n}.Product');

								$punits_pre = $this->search_array($product_id, 'id', $product_list);
								$measurement_unit_id = $this->request->data['OrderDetail']['measurement_unit_id'][$key] ? $this->request->data['OrderDetail']['measurement_unit_id'][$key] : $punits_pre['sales_measurement_unit_id'];
								if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
									$base_quantity = $sales_qty;
								} else {
									$base_quantity = $this->unit_convert($product_id, $measurement_unit_id, $sales_qty);
								}

								$update_type = 'deduct';
								$this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 11, date('Y-m-d'));
							}
						}

						$this->OrderDetail->saveAll($total_product_data);
						//pr($total_product_data);die();
					}
				}
				/************ Memo create date: 04-09-2019 *****************/
				/***************Memo Create for no Confirmation Type **************/
				$this->loadModel('Memo');
				$this->loadModel('OrderDetail');
				$this->loadModel('Order');
				$this->loadModel('Collection');
				$order_info = $this->Order->find('first', array(
					'conditions' => array('Order.id' => $order_id),
					'recursive' => -1
				));

				$order_detail_info = $this->OrderDetail->find('all', array(
					'conditions' => array('OrderDetail.order_id' => $order_id),
					'recursive' => -1
				));
				//pr($order_detail_info);//die();
				$memo = array();
				$memo['sales_person_id'] = $order_info['Order']['sales_person_id'];
				$memo['entry_date'] = $this->current_datetime();
				$memo['memo_date'] =  $updatedate;

				$memo['office_id'] = $order_info['Order']['office_id'];
				$memo['sale_type_id'] = 1;
				$memo['territory_id'] = $order_info['Order']['territory_id'];
				$memo['thana_id'] = $order_info['Order']['thana_id'];
				$memo['market_id'] = $order_info['Order']['market_id'];
				$memo['outlet_id'] = $order_info['Order']['outlet_id'];

				$memo['memo_no'] = $order_info['Order']['order_no'];
				$memo['gross_value'] = $order_info['Order']['gross_value'];
				$memo['cash_recieved'] = $order_info['Order']['gross_value'];
				$memo['is_active'] = $order_info['Order']['is_active'];
				$memo['w_store_id'] = $order_info['Order']['w_store_id'];
				if (array_key_exists('save', $this->request->data)) {
					$memo['status'] = 2;
				} else {
					$memo['status'] = 0;
					$this->Memo->create();
				}
				$memo['memo_time'] = $this->current_datetime();
				$memo['sales_person_id'] = $order_info['Order']['sales_person_id'];
				$memo['from_app'] = 0;
				$memo['action'] = 1;
				$memo['is_distributor'] = 1;
				$memo['is_program'] = 0;


				$memo['memo_reference_no'] = $order_info['Order']['memo_reference_no'];

				$memo['created_at'] = $this->current_datetime();
				$memo['created_by'] = $this->UserAuth->getUserId();
				$memo['updated_at'] = $this->current_datetime();
				$memo['updated_by'] = $this->UserAuth->getUserId();
				$memo['total_discount'] = $order_info['Order']['total_discount'];
				if (array_key_exists('save', $this->request->data)) {
					$this->loadModel('Memo');
					$memos = $this->Memo->find('first', array('conditions' => array('Memo.memo_no like' => "%" . $order_no . "%"), 'order' => 'Memo.id DESC'));
					$memo_id = $memos['Memo']['id'];
					$this->admin_deletememo($memo_id, 0);
				}
				//pr($memo);die();
				if ($this->Memo->save($memo)) {

					$memo_id = $this->Memo->getLastInsertId();
					$memo_info_arr = $this->Memo->find('first', array(
						'conditions' => array(
							'Memo.id' => $memo_id
						)
					));

					if ($memo_id) {
						if (!empty($order_detail_info[0]['OrderDetail'])) {
							$this->loadModel('MemoDetail');
							$total_product_data = array();
							$memo_details = array();
							$bonus_memo_details = array();

							foreach ($order_detail_info as $order_detail_result) {
								if ($order_detail_result['OrderDetail']['deliverd_qty'] > 0) {
									$product_id = $order_detail_result['OrderDetail']['product_id'];
									$memo_details['MemoDetail']['memo_id'] = $memo_id;
									$memo_details['MemoDetail']['product_id'] = $product_id;
									$memo_details['MemoDetail']['measurement_unit_id'] = $order_detail_result['OrderDetail']['measurement_unit_id'];
									$memo_details['MemoDetail']['actual_price'] = $order_detail_result['OrderDetail']['price'];
									$memo_details['MemoDetail']['price'] = $order_detail_result['OrderDetail']['price'] - $order_detail_result['OrderDetail']['discount_amount'];
									$memo_details['MemoDetail']['sales_qty'] = $order_detail_result['OrderDetail']['deliverd_qty'];

									$memo_details['MemoDetail']['product_price_id'] = $order_detail_result['OrderDetail']['product_price_id'];
									$memo_details['MemoDetail']['bonus_qty'] = NULL;
									$memo_details['MemoDetail']['offer_id'] = $order_detail_result['OrderDetail']['offer_id'];
									$memo_details['MemoDetail']['bonus_product_id'] = NULL;
									$memo_details['MemoDetail']['bonus_id'] = NULL;
									$memo_details['MemoDetail']['bonus_scheme_id'] = NULL;
									$memo_details['MemoDetail']['price_combination_id'] = $order_detail_result['OrderDetail']['price_combination_id'];
									$memo_details['MemoDetail']['product_combination_id'] = $order_detail_result['OrderDetail']['product_combination_id'];
									$memo_details['MemoDetail']['is_bonus'] = $order_detail_result['OrderDetail']['is_bonus'];
									$memo_details['MemoDetail']['deliverd_qty'] = $order_detail_result['OrderDetail']['deliverd_qty'];

									$memo_details['MemoDetail']['discount_type'] = $order_detail_result['OrderDetail']['discount_type'];
									$memo_details['MemoDetail']['discount_amount'] = $order_detail_result['OrderDetail']['discount_amount'];
									$memo_details['MemoDetail']['policy_type'] = $order_detail_result['OrderDetail']['policy_type'];
									$memo_details['MemoDetail']['policy_id'] = $order_detail_result['OrderDetail']['policy_id'];
									$memo_details['MemoDetail']['is_bonus'] = 0;
									if ($order_detail_result['OrderDetail']['is_bonus'] == 3)
										$memo_details['MemoDetail']['is_bonus'] = 3;
									$total_product_data[] = $memo_details;

									if ($order_detail_result['OrderDetail']['bonus_qty'] > 0 && $order_detail_result['OrderDetail']['is_bonus'] == 0) {
										$product_id = $order_detail_result['OrderDetail']['bonus_product_id'];
										$bonus_memo_details['MemoDetail']['memo_id'] = $memo_id;
										$bonus_memo_details['MemoDetail']['product_id'] = $product_id;
										$bonus_memo_details['MemoDetail']['measurement_unit_id'] = $order_detail_result['OrderDetail']['measurement_unit_id'];
										$bonus_memo_details['MemoDetail']['price'] = 0;
										$bonus_memo_details['MemoDetail']['sales_qty'] = $order_detail_result['OrderDetail']['bonus_qty'];

										$bonus_memo_details['MemoDetail']['product_price_id'] = $order_detail_result['OrderDetail']['product_price_id'];
										$bonus_memo_details['MemoDetail']['bonus_qty'] = $order_detail_result['OrderDetail']['bonus_qty'];
										$bonus_memo_details['MemoDetail']['offer_id'] = $order_detail_result['OrderDetail']['offer_id'];
										$bonus_memo_details['MemoDetail']['bonus_product_id'] = $order_detail_result['OrderDetail']['bonus_product_id'];
										$bonus_memo_details['MemoDetail']['bonus_id'] = $order_detail_result['OrderDetail']['bonus_id'];
										$bonus_memo_details['MemoDetail']['bonus_scheme_id'] = $order_detail_result['OrderDetail']['bonus_scheme_id'];
										$bonus_memo_details['MemoDetail']['price_combination_id'] = $order_detail_result['OrderDetail']['price_combination_id'];
										$bonus_memo_details['MemoDetail']['product_combination_id'] = $order_detail_result['OrderDetail']['product_combination_id'];
										$bonus_memo_details['MemoDetail']['is_bonus'] = 1;
										$bonus_memo_details['MemoDetail']['deliverd_qty'] = $order_detail_result['OrderDetail']['bonus_qty'];

										$total_product_data[] = $bonus_memo_details;
									}
								}
							}

							$this->MemoDetail->saveAll($total_product_data);
							//pr($total_product_data);//die();
						}
						$order_outlet_id = $order_info['Order']['outlet_id'];
						$this->loadmodel('DistOutletMap');
						$outlet_info = $this->DistOutletMap->find('first', array(
							'conditions' => array('DistOutletMap.outlet_id' => $order_outlet_id),
							'fields' => array('DistOutletMap.dist_distributor_id'),
						));
						$distibuter_id = $outlet_info['DistOutletMap']['dist_distributor_id'];
						$this->loadmodel('DistStore');
						$dist_store_id = $this->DistStore->find('first', array(
							'conditions' => array('DistStore.dist_distributor_id' => $distibuter_id),
							'fields' => array('DistStore.id'),
						));
						/************* create Challan *************/

						if (array_key_exists('save', $this->request->data)) {

							/*****************Create Chalan and *****************/
							$this->loadModel('DistChallan');
							$this->loadModel('DistChallanDetail');
							$this->loadModel('CurrentInventory');
							//$company_id  =$this->Session->read('Office.company_id');
							$office_id  = $this->request->data['OrderProces']['office_id'];
							$store_id = $this->request->data['OrderProces']['w_store_id'];
							//$challan['company_id']=$company_id;
							$challan['office_id'] = $office_id;
							$challan['memo_id'] = $memo_info_arr['Memo']['id'];
							$challan['memo_no'] = $memo_info_arr['Memo']['memo_no'];
							$challan['challan_no'] = $memo_info_arr['Memo']['memo_no'];
							$challan['receiver_dist_store_id'] = $dist_store_id['DistStore']['id'];
							$challan['receiving_transaction_type'] = 2;
							$challan['received_date'] = '';
							$challan['challan_date'] = $updatedate;
							$challan['dist_distributor_id'] = $distibuter_id;
							$challan['challan_referance_no'] = '';
							$challan['challan_type'] = "";
							$challan['remarks'] = 0;
							$challan['status'] = 1;
							$challan['so_id'] = $order_info['Order']['sales_person_id'];
							$challan['is_close'] = 0;
							$challan['inventory_status_id'] = 2;
							$challan['transaction_type_id'] = 2;
							$challan['sender_store_id'] = $store_id;
							$challan['created_at'] = $this->current_datetime();
							$challan['created_by'] = $this->UserAuth->getUserId();
							$challan['updated_at'] = $this->current_datetime();
							$challan['updated_by'] = $this->UserAuth->getUserId();
							//pr();die();
							$this->DistChallan->create();
							// pr($challan);
							if ($this->DistChallan->save($challan)) {

								$challan_id = $this->DistChallan->getLastInsertId();
								if ($challan_id) {

									$challan_no = 'Ch-' . $distibuter_id . '-' . date('Y') . '-' . $challan_id;

									$challan_data['id'] = $challan_id;
									$challan_data['challan_no'] = $challan_no;

									$this->DistChallan->save($challan_data);
								}
								$product_list = $this->request->data['OrderDetail'];
								//pr($product_list);
								if (!empty($product_list['product_id'])) {
									$data_array = array();

									foreach ($product_list['product_id'] as $key => $val) {
										if ($product_list['product_id'][$key] != '') {
											if ($product_list['Price'][$key] != 0 && !empty($product_list['Price'][$key])) {

												if ($product_list['deliverd_qty'][$key] > 0) {
													if (!empty($val)) {
														$inventories = $this->CurrentInventory->find('first', array(
															'conditions' => array(
																'CurrentInventory.product_id' => $val,
																'CurrentInventory.store_id' => $store_id,
															),
															'recursive' => -1,
														));
														$batch_no = $inventories['CurrentInventory']['batch_number'];

														$data['DistChallanDetail']['challan_id'] = $this->DistChallan->id;
														$data['DistChallanDetail']['product_id'] = $val;
														$data['DistChallanDetail']['measurement_unit_id'] = $product_list['measurement_unit_id'][$key];
														$data['DistChallanDetail']['challan_qty'] = $product_list['deliverd_qty'][$key];
														$data['DistChallanDetail']['received_qty'] = $product_list['deliverd_qty'][$key];
														$data['DistChallanDetail']['batch_no'] = $batch_no;
														//$data['DistChallanDetail']['remaining_qty'] =$product_list['remaining_qty'][$key];
														$data['DistChallanDetail']['price'] = $product_list['Price'][$key];
														/* if(!empty($product_list['bonus_product_id'][$key])){
													$data['DistChallanDetail']['is_bonus'] = 1;
													}else{*/
														$data['DistChallanDetail']['is_bonus'] = 0;
														//}

														$data['DistChallanDetail']['source'] = "";
														$data['DistChallanDetail']['remarks'] = $this->request->data['OrderDetail']['remarks'][$key];

														//$data['ChallanDetail']['expire_date'] = (($this->request->data['expire_date'][$key] != '' ) ? Date('Y-m-d', strtotime($this->request->data['expire_date'][$key])) : Null);
														$date = (($this->request->data['OrderProces']['order_date'][$key] != ' ' && $this->request->data['OrderProces']['order_date'][$key] != 'null' && $this->request->data['OrderProces']['order_date'][$key] != '') ? explode('-', $this->request->data['OrderProces']['order_date'][$key]) : '');
														if (!empty($date[1])) {
															$date[0] = date('m', strtotime($date[0]));
															$a_date = date('y-m-d', mktime(0, 0, 0, $date[0], 1, $date[1]));
															$data['DistChallanDetail']['expire_date'] = date("Y-m-t", strtotime($a_date));
														} else {
															$data['DistChallanDetail']['expire_date'] = '';
														}
														$data['DistChallanDetail']['inventory_status_id'] = 1;  // set 1 for Sound product
														//$data['ChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
													}
													$data_array[] = $data;
													if ($product_list['bonus_product_qty'][$key] != 0) {

														$inventories = $this->CurrentInventory->find('first', array(
															'conditions' => array(
																'CurrentInventory.product_id' => $product_list['bonus_product_id'][$key],
																'CurrentInventory.store_id' => $store_id,
															),
															'recursive' => -1,
														));
														$batch_no = $inventories['CurrentInventory']['batch_number'];
														$bonus_data['DistChallanDetail']['batch_no'] = $batch_no;
														$bonus_data['DistChallanDetail']['challan_id'] = $this->DistChallan->id;
														$bonus_data['DistChallanDetail']['product_id'] = $product_list['bonus_product_id'][$key];
														$bonus_data['DistChallanDetail']['measurement_unit_id'] = $product_list['measurement_unit_id'][$key];
														$bonus_data['DistChallanDetail']['challan_qty'] = $product_list['bonus_product_qty'][$key];
														$bonus_data['DistChallanDetail']['received_qty'] = $product_list['bonus_product_qty'][$key];
														$bonus_data['DistChallanDetail']['price'] = 0;
														$bonus_data['DistChallanDetail']['is_bonus'] = 1;
														//pr($bonus_data);
														$data_array[] = $bonus_data;
													}
												}
											} else {
												if (!empty($product_list['sales_qty'][$key])) {
													if ($product_list['Price'][$key] == 0) {
														$inventories = $this->CurrentInventory->find('first', array(
															'conditions' => array(
																'CurrentInventory.product_id' => $val,
																'CurrentInventory.store_id' => $store_id,
															),
															'recursive' => -1,
														));
														$bonus_data = array();
														$batch_no = $inventories['CurrentInventory']['batch_number'];
														$bonus_data['DistChallanDetail']['challan_id'] = $this->DistChallan->id;
														$bonus_data['DistChallanDetail']['product_id'] = $val;
														$bonus_data['DistChallanDetail']['challan_qty'] = $product_list['sales_qty'][$key];
														$bonus_data['DistChallanDetail']['received_qty'] = $product_list['sales_qty'][$key];
														$bonus_data['DistChallanDetail']['batch_no'] = $batch_no;
														$bonus_data['DistChallanDetail']['measurement_unit_id'] = $product_list['measurement_unit_id'][$key];
														$bonus_data['DistChallanDetail']['price'] = $product_list['Price'][$key];
														$bonus_data['DistChallanDetail']['is_bonus'] = 1;

														$data_array[] = $bonus_data;
													}
												}
											}
										}
									}
									//pr($data_array);die();
									$this->DistChallanDetail->saveAll($data_array);
									//pr($data_array);die();
								}
							}
						}
						/************* end Challan *************/

						/***************** Balance Deduct********************/
						if (array_key_exists('save', $this->request->data)) {
							if ($dist_balance_info) {
								$balance = $dist_balance - $credit_amount;
							} else {
								$balance = 0;
							}

							if ($balance < 1) {
								if ($dist_limit_info) {
									$dist_limit_data['DistDistributorLimit']['id'] =  $dist_limit_info['DistDistributorLimit']['id'];
									$dist_limit_data['DistDistributorLimit']['max_amount'] = $dist_limit_info['DistDistributorLimit']['max_amount'] - $balance;
									$limit_data_history = array();

									if ($this->DistDistributorLimit->save($dist_limit_data)) {

										$limit_data_history['dist_distributor_limit_id'] = $dist_limit_info['DistDistributorLimit']['id'];
										$limit_data_history['max_amount'] = $dist_limit_info['DistDistributorLimit']['max_amount'] - $balance;
										$limit_data_history['transaction_amount'] = $balance * (-1);
										$limit_data_history['transaction_type'] = 0;
										$limit_data_history['is_active'] = 1;

										$this->DistDistributorLimitHistory->create();
										$this->DistDistributorLimitHistory->save($limit_data_history);

										$balance = 0;
									}
								}
							}

							$dealer_balance_data = array();
							$dealer_balance = array();
							$dealer_balance['id'] = $dist_balance_info['DistDistributorBalance']['id'];
							$dealer_balance['office_id'] = $dist_balance_info['DistDistributorBalance']['office_id'];
							$dealer_balance['dist_distributor_id'] = $dist_balance_info['DistDistributorBalance']['dist_distributor_id'];
							$dealer_balance['balance'] = $balance;

							if ($this->DistDistributorBalance->save($dealer_balance)) {

								$dealer_balance_data['dist_distributor_id'] = $distributor_id;
								$dealer_balance_data['dist_distributor_balance_id'] = $dist_balance_info['DistDistributorBalance']['id'];
								$dealer_balance_data['office_id'] = $this->request->data['OrderProces']['office_id'];
								$dealer_balance_data['memo_value'] = $this->request->data['Order']['gross_value'];
								$dealer_balance_data['balance'] = $balance;
								$dealer_balance_data['balance_type'] = 2;
								$dealer_balance_data['balance_transaction_type_id'] = 2;
								$dealer_balance_data['transaction_amount'] = $this->request->data['Order']['gross_value'];
								$dealer_balance_data['transaction_date'] = $updatedate;
								$dealer_balance_data['created_at'] = $this->current_datetime();
								$dealer_balance_data['created_by'] = $this->UserAuth->getUserId();
								$dealer_balance_data['updated_at'] = $this->current_datetime();
								$dealer_balance_data['updated_by'] = $this->UserAuth->getUserId();
								$this->DistDistributorBalanceHistory->create();
								$this->DistDistributorBalanceHistory->save($dealer_balance_data);
							}
						}
						/****************end  Balance Deduct*********************/
					}
					//start collection crate
					$collection_data = array();
					$collection_data['memo_id'] = $memo_info_arr['Memo']['id'];
					$collection_data['memo_no'] = $memo_info_arr['Memo']['memo_no'];
					$collection_data['so_id'] = $memo_info_arr['Memo']['sales_person_id'];

					$collection_data['memo_value'] = $memo_info_arr['Memo']['gross_value'];
					$collection_data['credit_or_due'] = $memo_info_arr['Memo']['credit_amount'];
					$collection_data['memo_date'] = $memo_info_arr['Memo']['memo_date'];
					$collection_data['type'] = 1;
					$collection_data['instrument_type'] = $order_info['Order']['instrument_type'];
					$collection_data['collectionAmount'] = $memo_info_arr['Memo']['gross_value'];
					$collection_data['collectionDate'] = $updatedate;
					$collection_data['created_at'] = $this->current_datetime();

					$collection_data['territory_id'] = $memo_info_arr['Memo']['territory_id'];
					$collection_data['outlet_id'] = $memo_info_arr['Memo']['outlet_id'];
					$collection_data['market_id'] = $memo_info_arr['Memo']['market_id'];
					$collection_data['office_id'] = $memo_info_arr['Memo']['office_id'];

					$this->Collection->create();
					$this->Collection->save($collection_data);
					//die();        
					//end collection careate    

				}
			}
			if (array_key_exists('save', $this->request->data)) {
				$this->redirect(array('action' => 'distributor_update_date'));
			} else {
				$this->redirect(array('action' => 'edit_date', $id));
			}
		}

		$office_parent_id = $this->UserAuth->getOfficeParentId();

		$current_date = date('d-m-Y', strtotime($this->current_date()));

		if ($office_parent_id == 0) {
			$product_list = $this->Product->find('list', array('order' => array('order' => 'asc')));
			$distributor_conditions = array();
		} else {
			$office_id = $this->UserAuth->getOfficeId();
			$product_list = $this->Product->find('list', array(
				'order' => array('order' => 'asc')
			));
			$distributor_conditions = array('DistOutletMap.office_id' => $office_id);
		}

		$distributers_list = $this->DistOutletMap->find('all', array(
			'conditions' => $distributor_conditions,
		));
		foreach ($distributers_list as $key => $value) {
			$distributers[$value['Outlet']['id']] = $value['DistDistributor']['name'];
		}

		$office_outlets = $distributers;
		$this->set(compact('distributers'));
		$this->set(compact('office_outlets'));
		$this->set(compact('market_list'));
		$this->set(compact('product_list'));

		/* ------- start get edit data -------- */
		$this->Order->recursive = 1;
		$options = array(
			'conditions' => array('Order.id' => $id)
		);
		$existing_record = $this->Order->find('first', $options);

		$store_id = $existing_record['Order']['w_store_id'];
		//pr($store_id);
		$conditions = array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.qty > ' => 0, 'inventory_status_id' => 1);
		$products_from_ci = $this->CurrentInventory->find('all', array(
			'fields' => array('DISTINCT CurrentInventory.product_id'),
			'conditions' => $conditions,
		));

		$product_ci = array();
		foreach ($products_from_ci as $each_ci) {
			$product_ci[] = $each_ci['CurrentInventory']['product_id'];
		}
		//pr($product_ci);die();
		/*$this->loadModel('DistProductPrice');
			$product_list_for_distributor = $this->DistProductPrice->find('all',array(
				//'conditions'=>array('DistProductPrice.product_id'=>$product_ci),
			));
			$product_lists=array();
			foreach ($product_list_for_distributor as $val) {
				$product_lists[]=$val['DistProductPrice']['product_id'];
			}*/

		$products = $this->Product->find('list', array(
			'conditions' => array(

				'id' => $product_ci,
				'is_distributor_product' => 1,
			),
			'order' => array('order' => 'asc'),
			//'fields'=>array('Product.id as id','Product.name as name'),
			//'recursive'=>-1
		));
		//pr($products);die();
		$this->set(compact('products'));
		$details_data = array();
		foreach ($existing_record['OrderDetail'] as $detail_val) {
			$product = $detail_val['product_id'];
			//pr($product);
			if ($detail_val['product_combination_id']) {
				$combined_product = $this->CombinationDetailsV2->find('all', array(
					'conditions' => array('CombinationDetailsV2.combination_id' => $detail_val['product_combination_id']),
					'fields' => array('product_id'),
					'recursive' => -1
				));
				$combined_product = array_map(function ($val) {
					return $val['CombinationDetailsV2']['product_id'];
				}, $combined_product);
				$combined_product = implode(',', $combined_product);
				$detail_val['combined_product'] = $combined_product;
			}
			$details_data[] = $detail_val;
		}

		$existing_record['OrderDetail'] = $details_data;
		for ($i = 0; $i < count($details_data); $i++) {
			$measurement_unit_id = $details_data[$i]['measurement_unit_id'];
			if ($measurement_unit_id != 0) {
				$measurement_unit_name = $this->MeasurementUnit->find('all', array(
					'conditions' => array('MeasurementUnit.id' => $measurement_unit_id),
					'fields' => array('name'),
					'recursive' => -1
				));
				$existing_record['OrderDetail'][$i]['measurement_unit_name'] = $measurement_unit_name[0]['MeasurementUnit']['name'];
			}
		}
		if (!empty($existing_record['OrderDetail'])) {
			foreach ($existing_record['OrderDetail'] as $key => $value) {
				$OrderDetail_record[$value['product_id']] = $value;

				$product_info = $this->Product->find('first', array(
					'conditions' => array('Product.id' => $value['product_id']),
					'recursive' => -1
				));
				$existing_record['OrderDetail'][$key]['product_type_id'] = $product_info['Product']['product_type_id'];
				$stoksinfo = $this->CurrentInventory->find('all', array(
					'conditions' => array(
						'CurrentInventory.store_id' => $existing_record['Order']['w_store_id'],
						'CurrentInventory.product_id' => $value['product_id']
					),
					'fields' => array('sum(qty) as total'),
				));
				$total_qty = $stoksinfo[0][0]['total'];
				$sales_total_qty = $this->unit_convertfrombase($value['product_id'], $value['measurement_unit_id'], $total_qty);
				$existing_record['OrderDetail'][$key]['aso_stock_qty'] = $sales_total_qty;
				$stoks = $this->CurrentInventory->find('first', array(
					'conditions' => array(
						'CurrentInventory.store_id' => $existing_record['Order']['w_store_id'],
						'CurrentInventory.product_id' => $value['product_id']
					),
					'fields' => array('Product.id', 'Product.name', 'CurrentInventory.qty'),
				));
				$productName = $this->Product->find('first', array(
					'conditions' => array('id' => $value['product_id']),
					'fields' => array('id', 'name'),
					'recursive' => -1
				));
			}
		}

		$existing_record['office_id'] = $existing_record['SalesPerson']['office_id'];
		$existing_record['territory_id'] = $existing_record['Order']['territory_id'];
		$existing_record['market_id'] = $existing_record['Order']['market_id'];
		$existing_record['outlet_id'] = $existing_record['Order']['outlet_id'];
		$existing_record['order_time'] = date('d-m-Y', strtotime($existing_record['Order']['order_time']));
		$existing_record['order_date'] = date('d-m-Y', strtotime($existing_record['Order']['order_date']));
		$existing_record['order_no'] = $existing_record['Order']['order_no'];
		$existing_record['memo_reference_no'] = $existing_record['Order']['memo_reference_no'];

		$existing_record['order_reference_no'] = $existing_record['Order']['order_reference_no'];
		$existing_record['reference_number'] = $existing_record['Order']['instrument_reference_no'];
		$store_id = $this->Store->find('first', array(
			'fields' => array('Store.id'),
			'conditions' => array('Store.id' => $existing_record['Order']['w_store_id']),
			'recursive' => -1
		));

		$open_bonus_product = $this->Product->find('all', array(
			'fields' => array('Product.id', 'Product.name'),
			'joins' => array(
				array(
					'table' => 'current_inventories',
					'alias' => 'CurrentInventory',
					'type' => 'Inner',
					'conditions' => 'CurrentInventory.product_id=Product.id'
				),
				array(
					'table' => 'open_combination_products',
					'alias' => 'OpenCombinationProduct',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.product_id=Product.id'
				),
				array(
					'table' => 'open_combinations',
					'alias' => 'OpenCombination',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.combination_id=OpenCombination.id'
				),

			),
			'conditions' => array(
				'CurrentInventory.qty >' => 0,
				'CurrentInventory.store_id' => $store_id['Store']['id'],
				'OpenCombination.start_date <=' => date('Y-m-d'),
				'OpenCombination.end_date >=' => date('Y-m-d'),
				'OpenCombination.is_bonus' => 1
			),
			'recursive' => -1
		));
		$open_bonus_product_option = array();
		foreach ($open_bonus_product as $bonus_product) {
			$open_bonus_product_option[$bonus_product['Product']['id']] = $bonus_product['Product']['name'];
		}

		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$office_conditions = array();
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}

		$this->loadModel('Office');
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

		$office_id = $existing_record['office_id'];
		$territory_id = $existing_record['territory_id'];
		$market_id = $existing_record['market_id'];
		$outlet_id = $existing_record['outlet_id'];
		$outlets = array();


		$this->loadModel('Territory');
		$territories_list = $this->Territory->find('all', array(
			'conditions' => array('Territory.office_id' => $office_id),
			'order' => array('Territory.name' => 'asc')
		));
		$territories = array();
		foreach ($territories_list as $t_result) {
			$territories[$t_result['Territory']['id']] = $t_result['Territory']['name'] . ' (' . $t_result['SalesPerson']['name'] . ')';
		}


		$territory_ids = array($territory_id);


		$markets = $this->Market->find('list', array(
			'conditions' => array('territory_id' => $territory_ids),
			'order' => array('name' => 'asc')
		));

		$outlets = $distributers;

		$store_info = $this->Store->find('first', array(
			'conditions' => array(
				'Store.id' => $existing_record['Order']['w_store_id']
			),
			'recursive' => -1
		));
		$store_id = $store_info['Store']['id'];
		foreach ($existing_record['OrderDetail'] as $key => $single_product) {
			$total_qty_arr = $this->CurrentInventory->find('all', array(
				'conditions' => array('store_id' => $store_id, 'product_id' => $single_product['product_id']),
				'fields' => array('sum(qty) as total'),
				'recursive' => -1
			));

			$total_qty = $total_qty_arr[0][0]['total'];

			$sales_total_qty = $this->unit_convertfrombase($single_product['product_id'], $single_product['measurement_unit_id'], $total_qty);

			$existing_record['OrderDetail'][$key]['stock_qty'] = $sales_total_qty;
		}


		$products_from_ci = $this->CurrentInventory->find('all', array(
			'fields' => array('DISTINCT CurrentInventory.product_id'),
			'conditions' => array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.qty > ' => 0, 'inventory_status_id' => 1)
		));

		$product_ci = array();
		foreach ($products_from_ci as $each_ci) {
			$product_ci[] = $each_ci['CurrentInventory']['product_id'];
		}
		foreach ($existing_record['OrderDetail'] as $value) {
			$product_ci[] = $value['product_id'];
		}

		$product_ci_in = implode(",", $product_ci);
		$selected_bonus = array();
		$selected_set = array();
		$selected_policy_type = array();
		foreach ($existing_record['OrderDetail'] as $key => $value) {
			$existing_product_category_id_array = $this->Product->find('all', array(
				'conditions' => array('Product.id' => $value['product_id']),
				'fields' => array('product_category_id'),
				'recursive' => -1
			));

			$existing_product_category_id = $existing_product_category_id_array[0]['Product']['product_category_id'];
			if ($value['discount_amount'] && $value['policy_type'] == 3) {
				$selected_policy_type[$value['policy_id']] = 1;
			}
			if ($value['is_bonus'] == 3) {
				if ($value['policy_type'] == 3) {
					$selected_policy_type[$value['policy_id']] = 2;
				}
				if ($value['other_info']) {
					$other_info = json_decode($value['other_info'], 1);
					$selected_set[$value['policy_id']] = $other_info['selected_set'];
					$selected_bonus[$value['policy_id']][$other_info['selected_set']][$value['product_id']] = $value['sales_qty'];
				} else {
					$selected_bonus[$value['policy_id']][1][$value['product_id']] = $value['sales_qty'];
				}
			}
		}
		$this->set(compact('selected_bonus', 'selected_set', 'selected_policy_type'));


		$distributor_info = $this->DistOutletMap->find('first', array(
			'conditions' => array(
				'DistOutletMap.office_id' => $office_id,
				'DistOutletMap.outlet_id' => $outlet_id,
			),
		));
		$distributor_id = $distributor_info['DistOutletMap']['dist_distributor_id'];
		$dist_balance_info = $this->DistDistributorBalance->find('first', array(
			'conditions' => array(
				'DistDistributorBalance.dist_distributor_id' => $distributor_id
			),
			'limit' => 1,
			'recursive' => -1
		));

		$existing_record['current_balance'] =  $dist_balance_info['DistDistributorBalance']['balance'];

		/* -------- create individual Product data --------- */
		global $cart_data, $matched_array;
		$cart_data = array();
		$matched_array = array();
		$qty_session_data = array();

		/* ---------creating prepare data---------- */
		$prepare_cart_data = array();
		if (!empty($filter_product['Product']['id'])) {
			$prepare_cart_data[$filter_product['Product']['id']]['Product'] = $filter_product['Product'];
			$prepare_cart_data[$filter_product['Product']['id']]['ProductPrice'] = $filter_product['ProductPrice'];
			foreach ($filter_product['Individual_slab'] as $individual_slab_val) {
				$prepare_cart_data[$filter_product['Product']['id']]['Individual_slab'][$individual_slab_val['min_qty']] = $individual_slab_val['price'];
			}
			if (!empty($filter_product['Combination'])) {
				$prepare_cart_data[$filter_product['Product']['id']]['Combination'] = $filter_product['Combination'];
			}
			if (!empty($filter_product['Combination_id'])) {
				/* ---- start ------- */
				$this->Combination->recursive = 2;
				$condition_value1['Combination.id'] = $filter_product['Combination_id']; //$filter_product['Combination']['id'];
				$combination_slab_data_option = array(
					'conditions' => array($condition_value1),
				);
				$combination_slab_data = $this->Combination->find('all', $combination_slab_data_option);
				/* ----- end -------- */

				$combination_slab = array();
				foreach ($combination_slab_data as $combine_group) {
					foreach ($combine_group['ProductCombination'] as $combine_val) {
						$combination_slab[$combine_val['min_qty']][$combine_val['product_id']] = $combine_val['Childrel']['price'];
					}
				}
				if (!empty($combination_slab)) {
					$prepare_cart_data[$filter_product['Product']['id']]['Combined_slab'] = $combination_slab;
				}
			}
		}
		/* ------ unset cart data ------- */

		$user_office_id = $this->UserAuth->getOfficeId();
		$sales_person_list = $this->SalesPerson->find('list', array(
			'conditions' => array('SalesPerson.office_id' => $user_office_id)
		));

		$this->loadModel('ProductMeasurement');
		$product_measurement_units = $this->ProductMeasurement->find('list', array('fields' => array('product_id', 'measurement_unit_id')));
		$product_category_id_list = $this->Product->find('list', array('fields' => array('id', 'product_category_id')));


		$this->set(compact('offices', 'territories', 'markets', 'outlets', 'current_date', 'product_list', 'sale_type_list', 'existing_record', 'office_parent_id', 'product_measurement_units', 'product_category_id_list', 'open_bonus_product_option'));
	}

	public function admin_editmemo($id = null)
	{
		$this->loadModel('ProductCombination');
		$this->loadModel('Combination');
		$this->loadModel('Order');
		$this->Session->delete('cart_session_data');
		$this->Session->delete('matched_session_data');
		$this->Session->delete('combintaion_qty_data');


		$this->loadModel('Product');
		$this->loadModel('Memo');
		// $this->check_data_by_company('Memo',$id);

		$this->loadModel('MemoSetting');
		$MemoSettings = $this->MemoSetting->find(
			'all',
			array(
				//'conditions' => array('ChallanDetail.challan_id' => $challan['Challan']['id']),
				'order' => array('id' => 'asc'),
				'recursive' => 0,
				//'limit' => 100
			)
		);

		foreach ($MemoSettings as $s_result) {
			//echo $s_result['MemoSetting']['name'].'<br>';
			if ($s_result['MemoSetting']['name'] == 'stock_validation') {
				$stock_validation = $s_result['MemoSetting']['value'];
			}
			if ($s_result['MemoSetting']['name'] == 'stock_hit') {
				$stock_hit = $s_result['MemoSetting']['value'];
			}

			if ($s_result['MemoSetting']['name'] == 'ec_calculation') {
				$ec_calculation = $s_result['MemoSetting']['value'];
			}
			if ($s_result['MemoSetting']['name'] == 'oc_calculation') {
				$oc_calculation = $s_result['MemoSetting']['value'];
			}

			if ($s_result['MemoSetting']['name'] == 'sales_calculation') {
				$sales_calculation = $s_result['MemoSetting']['value'];
			}
			if ($s_result['MemoSetting']['name'] == 'stamp_calculation') {
				$stamp_calculation = $s_result['MemoSetting']['value'];
			}
			//pr($MemoSetting);
		}

		$this->set(compact('stock_validation'));
		//end memo setting

		$current_date = date('d-m-Y', strtotime($this->current_date()));
		/* ------- start get edit data -------- */
		$this->Memo->recursive = 1;
		$options = array(
			'conditions' => array('Memo.id' => $id)
		);

		$existing_record = $this->Memo->find('first', $options);
		//pr($existing_record);exit;
		$order_no = $existing_record['Memo']['memo_no'];
		$order_info = $this->Order->find('first', array('conditions' => array(
			'Order.order_no' => $order_no
		)));
		//pr($order_info);die();
		//$w_store_id=$existing_record['Memo']['w_store_id'];
		$w_store_id = $order_info['Order']['w_store_id'];

		//pr($existing_record);die();
		$details_data = array();
		foreach ($existing_record['MemoDetail'] as $detail_val) {
			$product = $detail_val['product_id'];
			$this->Combination->unbindModel(
				array('hasMany' => array('ProductCombination'))
			);
			$combination_list = $this->Combination->find('all', array(
				'conditions' => array('ProductCombination.product_id' => $product),
				'joins' => array(
					array(
						'alias' => 'ProductCombination',
						'table' => 'product_combinations',
						'type' => 'INNER',
						'conditions' => 'Combination.id = ProductCombination.combination_id'
					)
				),
				'fields' => array('Combination.all_products_in_combination'),
				'limit' => 1
			));
			if (!empty($combination_list)) {
				$combined_product = $combination_list[0]['Combination']['all_products_in_combination'];
				$detail_val['combined_product'] = $combined_product;
			}
			$details_data[] = $detail_val;
		}

		$existing_record['MemoDetail'] = $details_data;

		$this->loadModel('MeasurementUnit');
		for ($i = 0; $i < count($details_data); $i++) {
			$measurement_unit_id = $details_data[$i]['measurement_unit_id'];
			$measurement_unit_name = $this->MeasurementUnit->find('all', array(
				'conditions' => array('MeasurementUnit.id' => $measurement_unit_id),
				'fields' => array('name'),
				'recursive' => -1
			));
			$existing_record['MemoDetail'][$i]['measurement_unit_name'] = $measurement_unit_name[0]['MeasurementUnit']['name'];
		}

		$existing_record['office_id'] = $existing_record['SalesPerson']['office_id'];
		$existing_record['territory_id'] = $existing_record['Memo']['territory_id'];
		$existing_record['market_id'] = $existing_record['Memo']['market_id'];
		$existing_record['outlet_id'] = $existing_record['Memo']['outlet_id'];
		$existing_record['memo_time'] = date('d-m-Y', strtotime($existing_record['Memo']['memo_time']));
		$existing_record['memo_date'] = date('d-m-Y', strtotime($existing_record['Memo']['memo_date']));
		$existing_record['memo_no'] = $existing_record['Memo']['memo_no'];
		$existing_record['memo_reference_no'] = $existing_record['Memo']['memo_reference_no'];
		$existing_record['w_store_id'] = $w_store_id;

		//pr($existing_record);
		$this->LoadModel('Product');
		$this->LoadModel('Store');
		$store_id = $this->Store->find('first', array(
			'fields' => array('Store.id'),
			//'conditions'=>array('Store.territory_id'=>$existing_record['territory_id']),
			'conditions' => array('Store.id' => $w_store_id),
			'recursive' => -1
		));
		//$store_id=$existing_record['w_store_id'];
		$open_bonus_product = $this->Product->find('all', array(
			'fields' => array('Product.id', 'Product.name'),
			'joins' => array(
				array(
					'table' => 'current_inventories',
					'alias' => 'CurrentInventory',
					'type' => 'Inner',
					'conditions' => 'CurrentInventory.product_id=Product.id'
				),
				array(
					'table' => 'open_combination_products',
					'alias' => 'OpenCombinationProduct',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.product_id=Product.id'
				),
				array(
					'table' => 'open_combinations',
					'alias' => 'OpenCombination',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.combination_id=OpenCombination.id'
				),

			),
			'conditions' => array(
				'CurrentInventory.qty >' => 0,
				'CurrentInventory.store_id' => $w_store_id,
				'OpenCombination.start_date <=' => date('Y-m-d'),
				'OpenCombination.end_date >=' => date('Y-m-d'),
				'OpenCombination.is_bonus' => 1
			),
			'recursive' => -1
		));
		$open_bonus_product_option = array();
		foreach ($open_bonus_product as $bonus_product) {
			$open_bonus_product_option[$bonus_product['Product']['id']] = $bonus_product['Product']['name'];
		}

		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$office_conditions = array();
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}

		$this->set('office_id', $existing_record['office_id']);
		$this->loadModel('Office');
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

		$office_id = $existing_record['office_id'];
		$territory_id = $existing_record['territory_id'];
		$market_id = $existing_record['market_id'];
		$outlets = array();


		$this->loadModel('Territory');
		$territories_list = $this->Territory->find('all', array(
			'conditions' => array(
				'Territory.office_id' => $office_id,
				'Territory.name like' => '%Corporate Territory%'
			),
			'order' => array('Territory.name' => 'asc')
		));
		//pr( $territories_list);die();
		$territories = array();
		foreach ($territories_list as $t_result) {
			$territories[$t_result['Territory']['id']] = $t_result['Territory']['name'] . ' (' . $t_result['SalesPerson']['name'] . ')';
		}




		//for spo user
		$spo_territories = $this->Territory->find('list', array(
			'conditions' => array('Territory.office_id' => $office_id, 'User.user_group_id' => 1008),
			'joins' => array(
				array(
					'alias' => 'User',
					'table' => 'users',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.id = User.sales_person_id'
				)
			),
			'order' => array('Territory.name' => 'asc'),
			'recursive' => 0
		));
		$this->set(compact('spo_territories'));



		//get spo territories id
		$this->loadModel('Usermgmt.User');
		$user_info = $this->User->find('all', array(
			'fields' => array('User.id', 'User.sales_person_id', 'User.username', 'SalesPerson.id', 'SalesPerson.name', 'SalesPerson.office_id', 'SalesPerson.territory_id', 'UserGroup.id', 'UserGroup.name', 'UserTerritoryList.territory_id'),
			'conditions' => array('SalesPerson.id' => $existing_record['Memo']['sales_person_id'], 'User.active' => 1),
			'joins' => array(
				array(
					'alias' => 'UserTerritoryList',
					'table' => 'user_territory_lists',
					'type' => 'INNER',
					'conditions' => 'User.id = UserTerritoryList.user_id'
				)
			),
			'recursive' => 0
		));




		$territory_ids = array($territory_id);
		$user_group_id = 0;
		if ($user_info) {
			foreach ($user_info as $u_result) {
				//echo $result['UserTerritoryList']['territory_id'].'<br>';
				array_push($territory_ids, $u_result['UserTerritoryList']['territory_id']);
			}

			$user_group_id = $u_result['UserGroup']['id'];
		}


		/*pr($existing_record);
		exit;*/

		if ($user_group_id == 1008) {
			$sale_type_id = 3;
		} elseif ($existing_record['Memo']['is_program'] == 1) {
			$sale_type_id = 4;
		} else {
			$sale_type_id = 1;
		}


		$this->set(compact('user_group_id', 'sale_type_id'));

		//pr($territory_ids);
		//end for spo user
		$markets = $this->Market->find('list', array(
			'conditions' => array('id' => $market_id),
			'order' => array('name' => 'asc')
		));


		/*$markets = $this->Market->find('list', array(
			'conditions' => array('territory_id' => $territory_ids),
			'order' => array('name' => 'asc')
			));*/
		//pr( $markets);die();
		//exit;

		if ($market_id) {
			$outlets = $this->Outlet->find('list', array(
				'conditions' => array('market_id' => $market_id),
				'order' => array('name' => 'asc')
			));
		}

		$this->loadModel('Store');
		$this->loadModel('Product');
		$this->loadModel('CurrentInventory');
		$store_info = $this->Store->find('first', array(
			'conditions' => array(
				//'Store.territory_id' => $territory_id              
				'Store.id' => $w_store_id
			),
			'recursive' => -1
		));
		//pr($territory_id);pr($store_info);die();$existing_record['w_store_id']
		$store_id = $store_info['Store']['id'];


		foreach ($existing_record['MemoDetail'] as $key => $single_product) {

			$total_qty_arr = $this->CurrentInventory->find('all', array(
				'conditions' => array('store_id' => $store_id, 'product_id' => $single_product['product_id']),
				'fields' => array('sum(qty) as total'),
				'recursive' => -1
			));

			$total_qty = $total_qty_arr[0][0]['total'];

			$sales_total_qty = $this->unit_convertfrombase($single_product['product_id'], $single_product['measurement_unit_id'], $total_qty);

			$existing_record['MemoDetail'][$key]['stock_qty'] = $sales_total_qty;
		}


		$products_from_ci = $this->CurrentInventory->find('all', array(
			'fields' => array('DISTINCT CurrentInventory.product_id'),
			'conditions' => array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.qty > ' => 0, 'inventory_status_id' => 1)
		));

		$product_ci = array();
		foreach ($products_from_ci as $each_ci) {
			$product_ci[] = $each_ci['CurrentInventory']['product_id'];
		}
		foreach ($existing_record['MemoDetail'] as $value) {
			$product_ci[] = $value['product_id'];
		}

		$product_ci_in = implode(",", $product_ci);

		$product_list = $this->Product->find('list', array('conditions' => array('Product.id' => $product_ci), 'order' => array('order' => 'asc')));

		/* ------- end get edit data -------- */


		/*-----------My Work--------------*/
		$this->loadModel('ProductPrice');
		$this->loadModel('ProductCombination');


		foreach ($existing_record['MemoDetail'] as $key => $value) {
			$existing_product_category_id_array = $this->Product->find('all', array(
				'conditions' => array('Product.id' => $value['product_id']),
				'fields' => array('product_category_id'),
				'recursive' => -1
			));

			$existing_product_category_id = $existing_product_category_id_array[0]['Product']['product_category_id'];

			if ($existing_product_category_id != 32) {
				$individual_slab = array();
				$combined_slab = array();
				$all_combination_id = array();

				$retrieve_price_combination[$value['product_id']] = $this->ProductPrice->find('all', array(
					'conditions' => array('ProductPrice.product_id' => $value['product_id'], 'ProductPrice.has_combination' => 0)
				));

				foreach ($retrieve_price_combination[$value['product_id']][0]['ProductCombination'] as $key => $value2) {
					$individual_slab[$value2['min_qty']] = $value2['price'];
				}


				$combination_info = $this->ProductCombination->find('first', array(
					'conditions' => array('ProductCombination.product_id' => $value['product_id'], 'ProductCombination.combination_id !=' => 0)
				));

				if (!empty($combination_info['ProductCombination']['combination_id'])) {
					$combination_id = $combination_info['ProductCombination']['combination_id'];
					$all_combination_id_info = $this->ProductCombination->find('all', array(
						'conditions' => array('ProductCombination.combination_id' => $combination_id)
					));

					$combined_product = '';
					foreach ($all_combination_id_info as $key => $individual_combination_id) {
						$all_combination_id[$individual_combination_id['ProductCombination']['product_id']] = $individual_combination_id['ProductCombination']['price'];

						$individual_combined_product_id = $individual_combination_id['ProductCombination']['product_id'];

						$combined_product = $combined_product . ',' . $individual_combined_product_id;
					}
					$trimmed_combined_product = ltrim($combined_product, ',');

					$combined_slab[$combination_info['ProductCombination']['min_qty']] = $all_combination_id;

					$matched_combined_product_id_array = explode(',', $trimmed_combined_product);
					asort($matched_combined_product_id_array);
					$matched_combined_product_id = implode(',', $matched_combined_product_id_array);
				} else {
					$combined_slab = array();
					$matched_combined_product_id = '';
				}



				$edited_cart_data[$value['product_id']] = array(
					'product_price' => array(
						'id' => $retrieve_price_combination[$value['product_id']][0]['ProductPrice']['id'],
						'product_id' => $value['product_id'],
						'general_price' => $retrieve_price_combination[$value['product_id']][0]['ProductPrice']['general_price'],
						'effective_date' => $retrieve_price_combination[$value['product_id']][0]['ProductPrice']['effective_date']
					),
					'individual_slab' => $individual_slab,
					'combined_slab' => $combined_slab,
					'combined_product' => $matched_combined_product_id
				);



				if (!empty($matched_combined_product_id)) {
					$edited_matched_data[$matched_combined_product_id] = array(
						'count' => '4',
						'is_matched_yet' => 'NO',
						'matched_count_so_far' => '2',
						'matched_id_so_far' => '63,65'
					);

					$edited_current_qty_data[$value['product_id']] = $value['sales_qty'];
				}
			}
		}

		if (!empty($edited_cart_data)) {
			$this->Session->write('cart_session_data', $edited_cart_data);
		}
		if (!empty($edited_matched_data)) {
			$this->Session->write('matched_session_data', $edited_matched_data);
		}
		if (!empty($edited_current_qty_data)) {
			$this->Session->write('combintaion_qty_data', $edited_current_qty_data);
		}


		$this->set('page_title', 'Edit Memo');
		$this->Memo->id = $id;
		if (!$this->Memo->exists($id)) {
			throw new NotFoundException(__('Invalid Memo'));
		}
		/* -------- create individual Product data --------- */

		/*function make_cart_data($product_id, $product_price = array(), $individual_slab = array(), $combined_slab = array(), $all_products = '', &$cart_data = array()) {
			$cart_data[$product_id]['product_price'] = $product_price;

			$cart_data[$product_id]['individual_slab'] = $individual_slab;

			$cart_data[$product_id]['combined_slab'] = $combined_slab;

			$cart_data[$product_id]['combined_product'] = $all_products;
		}*/

		/* -------- create Matched Product data --------- */

		/*function make_matched_array($product_id, $all_products = '', $products_count = 0, &$matched_array) {

			if (!array_key_exists($all_products, $matched_array)) {


				$matched_array[$all_products] = array(
					'count' => $products_count,
					'is_matched_yet' => 'NO',
					'matched_count_so_far' => 1,
					'matched_id_so_far' => $product_id
				);
			} else {
				$updated_matched_count_so_far = $matched_array[$all_products]['matched_count_so_far'] + 1;
				$updated_matched_id_so_far = $matched_array[$all_products]['matched_id_so_far'] . ',' . $product_id;

				if ($matched_array[$all_products]['count'] == $updated_matched_count_so_far) {
					$is_matched_yet_value = 'YES';
				} else {
					$is_matched_yet_value = 'NO';
				}
				$matched_array[$all_products] = array(
					'count' => $products_count,
					'is_matched_yet' => $is_matched_yet_value,
					'matched_count_so_far' => $updated_matched_count_so_far,
					'matched_id_so_far' => $updated_matched_id_so_far
				);
			}
		}*/

		global $cart_data, $matched_array;
		$cart_data = array();
		$matched_array = array();
		$qty_session_data = array();

		/* ---------- start Set cart data as Session data ----------- */

		/*foreach ($existing_record['MemoDetail'] as $val) {
			$product_id = $val['product_id'];
			$current_date = $this->current_date();
			$condition_value['Product.id'] = $val['product_id'];
			$condition_value['ProductPrice.effective_date >='] = $current_date;
			$product_option = array(
				'conditions' => array($condition_value),
				'joins' => array(
					array(
						'alias' => 'ProductPrice',
						'table' => 'product_prices',
						'type' => 'LEFT',
						'conditions' => 'Product.id = ProductPrice.product_id'
					),
					array(
						'alias' => 'MeasurementUnit',
						'table' => 'measurement_units',
						'type' => 'LEFT',
						'conditions' => 'Product.sales_measurement_unit_id = MeasurementUnit.id'
					),
					array(
						'alias' => 'ProductCombination',
						'table' => 'product_combinations',
						'type' => 'LEFT',
						'conditions' => 'ProductPrice.id = ProductCombination.product_price_id'
					),
					array(
						'alias' => 'Combination',
						'table' => 'combinations',
						'type' => 'LEFT',
						'conditions' => 'Combination.id = ProductCombination.Combination_id'
					)
				),
				'fields' => array('Product.id', 'Product.product_code', 'Product.name', 'ProductPrice.id', 'ProductPrice.product_id', 'ProductPrice.general_price', 'ProductPrice.effective_date', 'MeasurementUnit.id', 'MeasurementUnit.name', 'ProductCombination.id', 'ProductCombination.price', 'ProductCombination.min_qty', 'ProductCombination.combination_id', 'Combination.id', 'Combination.all_products_in_combination'),
				'order' => array('ProductPrice.effective_date' => 'asc')
			);
			$product_list = $this->Product->find('all', $product_option);

			/* ----------- creating filter data ------------- */

		/*$product_count = count($product_list);
			$filter_product = array();
			$check_product_id = array();*/

		//pr($check_product_id);die();
		/*for ($i = 0; $i < $product_count; $i++) {
				if (!in_array($product_list[$i]['Product']['id'], $check_product_id)) {
					array_push($check_product_id, $product_list[$i]['Product']['id']);
					$filter_product = $product_list[$i];
					unset($filter_product['Combination']);
					unset($filter_product['ProductMeasurement']);
					if (!empty($product_list[$i]['Combination']['id'])) {
						$filter_product['Combined_min_qty'][] = $product_list[$i]['ProductCombination']['min_qty'];
						$filter_product['Combination_id'][] = $product_list[$i]['Combination']['id'];
						$filter_product['Combination'] = $product_list[$i]['Combination']['all_products_in_combination'];
					} else {
						$filter_product['Individual_slab'][] = $product_list[$i]['ProductCombination'];
					}
				} else {
					if (!empty($product_list[$i]['Combination']['id'])) {
						$filter_product['Combined_min_qty'][] = $product_list[$i]['ProductCombination']['min_qty'];
						$filter_product['Combination_id'][] = $product_list[$i]['Combination']['id'];
						$filter_product['Combination'] = $product_list[$i]['Combination']['all_products_in_combination'];
					} else {
						$filter_product['Individual_slab'][] = $product_list[$i]['ProductCombination'];
					}
				}
			}*/
		/* ---------creating prepare data---------- */
		$prepare_cart_data = array();
		if (!empty($filter_product['Product']['id'])) {
			$prepare_cart_data[$filter_product['Product']['id']]['Product'] = $filter_product['Product'];
			$prepare_cart_data[$filter_product['Product']['id']]['ProductPrice'] = $filter_product['ProductPrice'];
			foreach ($filter_product['Individual_slab'] as $individual_slab_val) {
				$prepare_cart_data[$filter_product['Product']['id']]['Individual_slab'][$individual_slab_val['min_qty']] = $individual_slab_val['price'];
			}
			if (!empty($filter_product['Combination'])) {
				$prepare_cart_data[$filter_product['Product']['id']]['Combination'] = $filter_product['Combination'];
			}
			if (!empty($filter_product['Combination_id'])) {

				/* ---- start ------- */
				$this->Combination->recursive = 2;
				$condition_value1['Combination.id'] = $filter_product['Combination_id']; //$filter_product['Combination']['id'];
				$combination_slab_data_option = array(
					'conditions' => array($condition_value1),
				);
				$combination_slab_data = $this->Combination->find('all', $combination_slab_data_option);
				/* ----- end -------- */

				$combination_slab = array();
				foreach ($combination_slab_data as $combine_group) {
					foreach ($combine_group['ProductCombination'] as $combine_val) {
						$combination_slab[$combine_val['min_qty']][$combine_val['product_id']] = $combine_val['Childrel']['price'];
					}
				}
				if (!empty($combination_slab)) {
					$prepare_cart_data[$filter_product['Product']['id']]['Combined_slab'] = $combination_slab;
				}
			}
		}
		/* ----------start create cart data and matched data ----------- */
		/* ---------------- cart data store in session ---------------- */

		/*if (!empty($prepare_cart_data[$product_id]['Combination'])) {
				$products_count = count(explode(',', $prepare_cart_data[$product_id]['Combination']));
			}
			if ($this->Session->read('cart_session_data') == NULL) {
				$cart_data = array();
			} else {
				$cart_data = $this->Session->read('cart_session_data');
			}
			if ($this->Session->read('matched_session_data') == NULL) {
				$matched_array = array();
			} else {
				$matched_array = $this->Session->read('matched_session_data');
			}

			if (!empty($prepare_cart_data[$product_id]['Combination'])) {
				make_cart_data($product_id, $prepare_cart_data[$product_id]['ProductPrice'], $prepare_cart_data[$product_id]['Individual_slab'], $prepare_cart_data[$product_id]['Combined_slab'], $prepare_cart_data[$product_id]['Combination'], $cart_data);
				$this->Session->write('cart_session_data', $cart_data);
				//session array updated

				make_matched_array($product_id, $prepare_cart_data[$product_id]['Combination'], $products_count, $matched_array);
				$this->Session->write('matched_session_data', $matched_array);
			} else {
				make_cart_data($product_id, $prepare_cart_data[$product_id]['ProductPrice'], $prepare_cart_data[$product_id]['Individual_slab'], array(), '', $cart_data);
				$this->Session->write('cart_session_data', $cart_data);
			}*/


		/* ----------end create cart data and matched data ----------- */
		/* ---------- start create qty session data data ----------------- */
		/*if (!array_key_exists($product_id, $qty_session_data)) {
				$qty_session_data[$product_id] = $val['sales_qty'];
			} else {
				$qty_session_data[$product_id] = $val['sales_qty'];
			}*/
		/* ---------- end create qty session data data ----------------- */
		/*}*/
		/*$this->Session->write('combintaion_qty_data', $qty_session_data);*/
		/*$current_session_data = $this->Session->read('cart_session_data');
		$current_matched_data = $this->Session->read('matched_session_data');*/

		/* ---------- end Set cart data as Session data ----------- */


		/* ------ unset cart data ------- */

		$user_office_id = $this->UserAuth->getOfficeId();

		/* ------ start code of sale type list ------ */
		$sale_type_list = array(
			1 => 'SO Sales',
			2 => 'CSA Sales',
			3 => 'SPO Sales',
			4 => 'Program Sales'
		);
		/* ------ end code of sale type list ------ */
		/* ----- start code of product list ----- */
		//$product_list = $this->Product->find('list');
		/* ----- start code of product list ----- */
		/* ----- start code of sales person ----- */
		$user_office_id = $this->UserAuth->getOfficeId();
		/*$sales_person_list = $this->SalesPerson->find('list', array(
			'conditions' => array('SalesPerson.office_id' => $user_office_id, 'User.user_group_id' => 4)
			));*/
		$sales_person_list = $this->SalesPerson->find('list', array(
			'conditions' => array('SalesPerson.office_id' => $user_office_id)
		));
		/*      echo "<pre>";
		  print_r($sales_person_list);
		  exit; */
		/* ----- end code of sales person ----- */
		/* ----- start code of market list ----- */
		//$market_list = $this->Market->find('list');
		/* ----- end code of market list ----- */
		/* ----- start code of outlet list ----- */
		//$outlet_list = $this->Outlet->find('list');
		/* ----- end code of outlet list ----- */
		/* ------------ code for update memo ---------------- */
		//$this->Memo->id = $id;

		if ($this->request->is('post')) {

			$sale_type_id = $this->request->data['Memo']['sale_type_id'];

			//pr($this->request->data); 
			//exit;

			$outlet_is_within_group = $this->outletGroupCheck($this->request->data['Memo']['outlet_id']);
			$product_is_injectable = $this->productInjectableCheck($this->request->data['MemoDetail']['product_id']);

			if ($product_is_injectable && !$outlet_is_within_group && $sale_type_id != 2) {
				$this->Session->setFlash(__("Special product can't sell to ordinary outlet!"), 'flash/error');
				$this->redirect(array('action' => 'create_memo'));
				exit;
			}

			$memo_id = $id;

			$this->admin_deletememo($memo_id, 0);

			/*START ADD NEW*/
			//get office id 
			$office_id = $this->request->data['Memo']['office_id'];

			//get thana id 
			$this->loadModel('Market');
			$market_info = $this->Market->find(
				'first',
				array(
					'conditions' => array('Market.id' => $this->request->data['Memo']['market_id']),
					'fields' => 'Market.thana_id',
					'order' => array('Market.id' => 'asc'),
					'recursive' => -1,
					//'limit' => 100
				)
			);
			$thana_id = $market_info['Market']['thana_id'];
			/*END ADD NEW*/

			$sale_type_id = $this->request->data['Memo']['sale_type_id'];

			if ($sale_type_id == 1 || $sale_type_id == 3 || $sale_type_id == 4) {
				$this->request->data['Memo']['is_active'] = 1;
				//$this->request->data['Memo']['status'] = ($this->request->data['Memo']['credit_amount'] != 0) ? 1 : 2;

				if (array_key_exists('draft', $this->request->data)) {
					$this->request->data['Memo']['status'] = 0;
					$message = "Memo Has Been Saved as Draft";
				} else {
					$message = "Memo Has Been Saved";
					$this->request->data['Memo']['status'] = ($this->request->data['Memo']['credit_amount'] != 0) ? 1 : 2;
				}

				$sales_person = $this->SalesPerson->find('list', array(
					'conditions' => array('territory_id' => $this->request->data['Memo']['territory_id']),
					'order' => array('name' => 'asc')
				));

				$this->request->data['Memo']['sales_person_id'] = key($sales_person);

				$this->request->data['Memo']['entry_date'] = date('Y-m-d', strtotime($this->request->data['Memo']['entry_date']));
				$this->request->data['Memo']['memo_date'] = date('Y-m-d', strtotime($this->request->data['Memo']['memo_date']));

				$memoData['id'] = $memo_id;
				$memoData['office_id'] = $this->request->data['Memo']['office_id'];
				$memoData['sale_type_id'] = $this->request->data['Memo']['sale_type_id'];
				$memoData['territory_id'] = $this->request->data['Memo']['territory_id'];
				$memoData['market_id'] = $this->request->data['Memo']['market_id'];
				$memoData['outlet_id'] = $this->request->data['Memo']['outlet_id'];
				$memoData['entry_date'] = $this->request->data['Memo']['entry_date'];
				$memoData['memo_date'] = $this->request->data['Memo']['memo_date'];
				$memoData['memo_no'] = $this->request->data['Memo']['memo_no'];
				$memoData['gross_value'] = $this->request->data['Memo']['gross_value'];
				$memoData['cash_recieved'] = $this->request->data['Memo']['cash_recieved'];
				$memoData['credit_amount'] = $this->request->data['Memo']['credit_amount'];
				$memoData['is_active'] = $this->request->data['Memo']['is_active'];
				$memoData['status'] = $this->request->data['Memo']['status'];
				//$memoData['memo_time'] = $this->current_datetime();   
				$memoData['memo_time'] = $this->request->data['Memo']['entry_date'];
				$memoData['sales_person_id'] = $this->request->data['Memo']['sales_person_id'];
				$memoData['from_app'] = 0;
				$memoData['action'] = 1;
				$memoData['w_store_id'] = $w_store_id;
				$memoData['is_program'] = ($sale_type_id == 4) ? 1 : 0;
				//$memoData['company_id']=$company_id;

				$memoData['memo_reference_no'] = $this->request->data['Memo']['memo_reference_no'];


				$memoData['created_at'] = $this->current_datetime();
				$memoData['created_by'] = $this->UserAuth->getUserId();
				$memoData['updated_at'] = $this->current_datetime();
				$memoData['updated_by'] = $this->UserAuth->getUserId();


				$memoData['office_id'] = $office_id ? $office_id : 0;
				$memoData['thana_id'] = $thana_id ? $thana_id : 0;


				$this->Memo->create();

				if ($this->Memo->save($memoData)) {

					// EC Calculation 
					if ($ec_calculation) {
						$this->ec_calculation($memoData['gross_value'], $memoData['outlet_id'], $memoData['territory_id'], $memoData['memo_date'], 1);
					}

					// OC Calculation 
					if ($oc_calculation) {
						$this->oc_calculation($memoData['territory_id'], $memoData['gross_value'], $memoData['outlet_id'], $memoData['memo_date'], $memoData['memo_time'], 1);
					}

					$memo_id = $this->Memo->getLastInsertId();


					$memo_info_arr = $this->Memo->find('first', array(
						'conditions' => array(
							'Memo.id' => $memo_id
						)
					));

					$this->loadModel('Store');
					$store_id_arr = $this->Store->find('first', array(
						'conditions' => array(
							//'Store.territory_id'=> $memo_info_arr['Memo']['territory_id']
							'Store.id' => $w_store_id
						)
					));
					// pr($store_id_arr);die();
					$store_id = $store_id_arr['Store']['id'];


					if ($memo_id) {
						$all_product_id = $this->request->data['MemoDetail']['product_id'];
						if (!empty($this->request->data['MemoDetail'])) {
							$total_product_data = array();
							$memo_details = array();
							$memo_details['MemoDetail']['memo_id'] = $memo_id;

							foreach ($this->request->data['MemoDetail']['product_id'] as $key => $val) {
								if ($val == NULL) {
									continue;
								}
								$product_id = $memo_details['MemoDetail']['product_id'] = $val;
								$memo_details['MemoDetail']['measurement_unit_id'] = $this->request->data['MemoDetail']['measurement_unit_id'][$key];
								$sales_price = $memo_details['MemoDetail']['price'] = $this->request->data['MemoDetail']['Price'][$key];
								$sales_qty = $memo_details['MemoDetail']['sales_qty'] = $this->request->data['MemoDetail']['sales_qty'][$key];
								$memo_details['MemoDetail']['product_price_id'] = $this->request->data['MemoDetail']['product_price_id'][$key];
								$memo_details['MemoDetail']['bonus_qty'] = $this->request->data['MemoDetail']['bonus_product_qty'][$key];

								$product_price_slab_id = 0;
								if ($sales_price > 0) {
									$product_price_slab_id = $this->get_product_price_id($val, $sales_price, $all_product_id);
									// pr($product_price_slab_id);exit;
								}
								$memo_details['MemoDetail']['product_price_id'] = $product_price_slab_id['product_price_id'];
								$memo_details['MemoDetail']['product_combination_id'] = $product_price_slab_id['combination_id'];
								$memo_details['MemoDetail']['bonus_qty'] = $this->request->data['MemoDetail']['bonus_product_qty'][$key];


								if ($this->request->data['MemoDetail']['bonus_product_id'][$key] != 0) {
									$memo_details['MemoDetail']['bonus_product_id'] = $this->request->data['MemoDetail']['bonus_product_id'][$key];
								} else {
									$memo_details['MemoDetail']['bonus_product_id'] = NULL;
								}

								//Start for bonus
								$memo_date = date('Y-m-d', strtotime($this->request->data['Memo']['memo_date']));
								$bonus_product_id = $this->request->data['MemoDetail']['bonus_product_id'];
								$bonus_product_qty = $this->request->data['MemoDetail']['bonus_product_qty'];
								$memo_details['MemoDetail']['bonus_id'] = 0;
								$memo_details['MemoDetail']['bonus_scheme_id'] = 0;
								if ($bonus_product_qty[$key] > 0) {
									//echo $bonus_product_id[$key].'<br>';
									$b_product_id = $bonus_product_id[$key];
									$bonus_result = $this->bouns_and_scheme_id_set($b_product_id, $memo_date);
									$memo_details['MemoDetail']['bonus_id'] = $bonus_result['bonus_id'];
								}
								//End for bouns

								$total_product_data[] = $memo_details;

								if ($bonus_product_qty[$key] > 0) {
									$memo_details_bonus['MemoDetail']['memo_id'] = $memo_id;
									$memo_details_bonus['MemoDetail']['is_bonus'] = 1;
									$memo_details_bonus['MemoDetail']['product_id'] = $product_id;
									$memo_details_bonus['MemoDetail']['measurement_unit_id'] = $this->request->data['MemoDetail']['measurement_unit_id'][$key];
									$memo_details_bonus['MemoDetail']['price'] = 0.0;
									$memo_details_bonus['MemoDetail']['sales_qty'] = $this->request->data['MemoDetail']['bonus_product_qty'][$key];
									$total_product_data[] = $memo_details_bonus;
									unset($memo_details_bonus);
									//update inventory
									if ($stock_hit) {
										$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
										$product_list = Set::extract($products, '{n}.Product');
										$bonus_qty = $this->request->data['MemoDetail']['bonus_product_qty'][$key];
										$punits_pre = $this->search_array($product_id, 'id', $product_list);
										if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
											$base_quantity = $bonus_qty;
										} else {
											$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $bonus_qty);
										}

										$update_type = 'deduct';
										$this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 11, $this->request->data['Memo']['memo_date']);
									}
								}


								//update inventory
								if ($stock_hit) {
									$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
									$product_list = Set::extract($products, '{n}.Product');

									$punits_pre = $this->search_array($product_id, 'id', $product_list);
									if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
										$base_quantity = $sales_qty;
									} else {
										$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
									}

									$update_type = 'deduct';
									$this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 11, $this->request->data['Memo']['memo_date']);
								}



								// sales calculation
								$tt_price = $sales_qty * $sales_price;
								if ($sales_calculation) {
									$this->sales_calculation($memo_details['MemoDetail']['product_id'], $memoData['territory_id'], $memo_details['MemoDetail']['sales_qty'], $tt_price, $memoData['memo_date'], 1);
								}

								//stamp calculation
								if ($stamp_calculation) {
									$this->stamp_calculation($memoData['memo_no'], $memoData['territory_id'], $memo_details['MemoDetail']['product_id'], $memoData['outlet_id'], $memo_details['MemoDetail']['sales_qty'], $memoData['memo_date'], 1, $tt_price, $memoData['market_id']);
								}
							}

							$this->MemoDetail->saveAll($total_product_data);
						}
					}
				}
				$this->Session->setFlash(__('The Memo has been Updated'), 'flash/success');
				if (array_key_exists('save', $this->request->data)) {
					$this->redirect(array('action' => 'index'));
				}
			}

			if ($sale_type_id == 2) {
				$this->loadModel('CsaMemo');
				$this->loadModel('CsaMemoDetail');

				$this->request->data['Memo']['is_active'] = 1;
				//$this->request->data['Memo']['status'] = ($this->request->data['Memo']['credit_amount'] != 0) ? 1 : 2;
				if (array_key_exists('draft', $this->request->data)) {
					$this->request->data['Memo']['status'] = 0;
					$message = "CSA Memo Has Been Saved as Draft";
				} else {
					$message = "CSA Memo Has Been Saved";
					$this->request->data['Memo']['status'] = ($this->request->data['Memo']['credit_amount'] != 0) ? 1 : 2;
				}

				$sales_person = $this->SalesPerson->find('list', array(
					'conditions' => array('territory_id' => $this->request->data['Memo']['territory_id']),
					'order' => array('name' => 'asc')
				));

				$this->request->data['Memo']['sales_person_id'] = key($sales_person);


				$this->CsaMemo->create();
				$this->request->data['Memo']['entry_date'] = date('Y-m-d', strtotime($this->request->data['Memo']['entry_date']));
				$this->request->data['Memo']['memo_date'] = date('Y-m-d', strtotime($this->request->data['Memo']['memo_date']));
				/*echo "<pre>";
				print_r($this->request->data['Memo']);
				echo "</pre>";die();*/

				$memoData['office_id'] = $this->request->data['Memo']['office_id'];
				$memoData['sale_type_id'] = $this->request->data['Memo']['sale_type_id'];
				$memoData['territory_id'] = $this->request->data['Memo']['territory_id'];
				$memoData['market_id'] = $this->request->data['Memo']['market_id'];
				$memoData['outlet_id'] = $this->request->data['Memo']['outlet_id'];
				$memoData['entry_date'] = $this->request->data['Memo']['entry_date'];
				$memoData['memo_date'] = $this->request->data['Memo']['memo_date'];
				$memoData['csa_memo_no'] = $memo_no;
				$memoData['gross_value'] = $this->request->data['Memo']['gross_value'];
				$memoData['cash_recieved'] = $this->request->data['Memo']['cash_recieved'];
				$memoData['credit_amount'] = $this->request->data['Memo']['credit_amount'];
				$memoData['is_active'] = $this->request->data['Memo']['is_active'];
				$memoData['status'] = $this->request->data['Memo']['status'];
				//$memoData['memo_time'] = $this->current_datetime();
				$memoData['memo_time'] = $this->request->data['Memo']['entry_date'];
				$memoData['sales_person_id'] = $this->request->data['Memo']['sales_person_id'];
				$memoData['from_app'] = 0;
				$memoData['action'] = 1;

				$memoData['memo_reference_no'] = $this->request->data['Memo']['memo_reference_no'];


				$memoData['office_id'] = $office_id ? $office_id : 0;
				$memoData['thana_id'] = $thana_id ? $thana_id : 0;

				if ($this->CsaMemo->save($memoData)) {
					$csa_memo_id = $this->CsaMemo->getLastInsertId();

					if ($csa_memo_id) {
						if (!empty($this->request->data['MemoDetail'])) {
							$total_product_data = array();
							$memo_details = array();
							$memo_details['CsaMemoDetail']['csa_memo_id'] = $csa_memo_id;

							foreach ($this->request->data['MemoDetail']['product_id'] as $key => $val) {
								if ($val) {
									$memo_details['CsaMemoDetail']['csa_memo_id'] = $csa_memo_id;
									$memo_details['CsaMemoDetail']['product_id'] = $val;
									$memo_details['CsaMemoDetail']['measurement_unit_id'] = $this->request->data['MemoDetail']['measurement_unit_id'][$key];
									$memo_details['CsaMemoDetail']['price'] = $this->request->data['MemoDetail']['Price'][$key];
									$memo_details['CsaMemoDetail']['sales_qty'] = $this->request->data['MemoDetail']['sales_qty'][$key];
									$memo_details['CsaMemoDetail']['product_price_id'] = $this->request->data['MemoDetail']['product_price_id'][$key];
									$memo_details['CsaMemoDetail']['bonus_qty'] = $this->request->data['MemoDetail']['bonus_product_qty'][$key];
									if ($this->request->data['MemoDetail']['bonus_product_id'][$key] != 0) {
										$memo_details['CsaMemoDetail']['bonus_product_id'] = $this->request->data['MemoDetail']['bonus_product_id'][$key];
									} else {
										$memo_details['CsaMemoDetail']['bonus_product_id'] = NULL;
									}

									//Start for bonus
									$memo_date = date('Y-m-d', strtotime($this->request->data['Memo']['memo_date']));
									$bonus_product_id = $this->request->data['MemoDetail']['bonus_product_id'];
									$bonus_product_qty = $this->request->data['MemoDetail']['bonus_product_qty'];
									$memo_details['MemoDetail']['bonus_id'] = 0;
									$memo_details['MemoDetail']['bonus_scheme_id'] = 0;
									if ($bonus_product_qty[$key] > 0) {
										//echo $bonus_product_id[$key].'<br>';
										$b_product_id = $bonus_product_id[$key];
										$bonus_result = $this->bouns_and_scheme_id_set($b_product_id, $memo_date);
										$memo_details['MemoDetail']['bonus_id'] = $bonus_result['bonus_id'];
									}
									//End for bouns

									$total_product_data[] = $memo_details;
									if ($bonus_product_qty[$key] > 0) {
										$memo_details_bonus['CsaMemoDetail']['csa_memo_id'] = $csa_memo_id;
										$memo_details_bonus['CsaMemoDetail']['product_id'] = $val;
										$memo_details_bonus['CsaMemoDetail']['measurement_unit_id'] = $this->request->data['MemoDetail']['measurement_unit_id'][$key];
										$memo_details_bonus['CsaMemoDetail']['price'] = 0.0;
										$memo_details_bonus['CsaMemoDetail']['sales_qty'] = $this->request->data['MemoDetail']['bonus_product_qty'][$key];
										$memo_details_bonus['CsaMemoDetail']['is_bonus'] = 1;
										$total_product_data[] = $memo_details_bonus;
										unset($memo_details_bonus);
									}
								}
							}

							$this->CsaMemoDetail->saveAll($total_product_data);
						}
					}
				}

				$this->Session->setFlash(__('The Memo has been Updated'), 'flash/success');
				$this->redirect(array("controller" => "CsaMemos", 'action' => 'index'));
				exit;
			}
		}

		$this->loadModel('ProductMeasurement');
		$product_measurement_units = $this->ProductMeasurement->find('list', array('fields' => array('product_id', 'measurement_unit_id')));
		$product_category_id_list = $this->Product->find('list', array('fields' => array('id', 'product_category_id')));


		$this->set(compact('offices', 'territories', 'markets', 'outlets', 'current_date', 'product_list', 'sale_type_list', 'existing_record', 'office_parent_id', 'product_measurement_units', 'product_category_id_list', 'open_bonus_product_option'));


		/*$this->set(compact('offices', 'territories', 'product_list', 'markets', 'outlets', 'current_date', 'sale_type_list', 'office_parent_id', 'sales_person'));*/
	}

	public function admin_delivery($id = null)
	{

		$this->set('page_title', 'Order Manage Confirmation Details');
		$dealer_is_limit_check = 1;
		$this->Order->unbindModel(array('hasMany' => array('OrderDetail')));
		$order = $this->Order->find('first', array(
			'conditions' => array('Order.id' => $id)
		));
		$this->loadModel('DistOutletMap');
		$outletInfo = $this->DistOutletMap->find('first', array('conditions' => array('DistOutletMap.outlet_id' => $order['Order']['outlet_id'])));
		$distributor_id = $outletInfo['DistDistributor']['id'];
		// pr($order['Order']['order_no']);die();
		$this->loadModel('DistDistributor');
		$distributor = $this->DistDistributor->find('first', array('conditions' => array('DistDistributor.id' => $distributor_id)));


		//$orderLimits=$distributor['DistDistributorBalance'];
		$orderDate = $order['Order']['order_date'];
		//pr($orderLimits);die();

		$this->loadModel('Memo');
		$memolist = $this->Memo->find('all', array('conditions' => array('Memo.memo_no' => $order['Order']['order_no'])));
		//pr($memolist);die();
		$maintain_dealer_type = 1;

		$this->loadModel('DistDistributorBalance');
		$limts = $this->DistDistributorBalance->find('first', array(
			'fields' => array('balance'),
			'conditions' => array(
				//'DistDistributorBalance.effective_date >=' => $orderDate,
				'DistDistributorBalance.dist_distributor_id' => $distributor_id,
			),
			////'order' => 'DistDistributorBalance.effective_date DESC',
			'limit' => 1,
			'recursive' => -1

		));
		$orderLimits = $limts['DistDistributorBalance']['balance'];

		$this->loadModel('DistDistributorBalanceHistory');
		$dealer_balance_info = $this->DistDistributorBalanceHistory->find('first', array(
			'conditions' => array(
				'DistDistributorBalanceHistory.dist_distributor_id' => $distributor_id,
				////'DistDistributorBalanceHistory.is_execute' => 1,
			),
			'order' => 'DistDistributorBalanceHistory.id DESC',
			'recursive' => -1
		));
		$balance = $dealer_balance_info['DistDistributorBalanceHistory']['balance'];


		//pr($dealer_balance_info);die();
		$this->loadModel('OrderDetail');
		if (!$this->Order->exists($id)) {
			throw new NotFoundException(__('Invalid district'));
		}
		$this->OrderDetail->recursive = 0;
		$order_details = $this->OrderDetail->find(
			'all',
			array(
				'conditions' => array('OrderDetail.order_id' => $id),
				'order' => array('Product.order' => 'asc')
			)
		);

		$this->set(compact('order', 'order_details', 'distributor', 'orderLimits', 'balance', 'dealer_is_limit_check', 'memolist'));
	}
	public function admin_deletememo($id = null, $redirect = 1)
	{

		$this->loadModel('Product');
		$this->loadModel('Memo');
		$this->loadModel('MemoDetail');
		$this->loadModel('Deposit');
		$this->loadModel('Collection');
		//pr($id);die();
		//start memo setting
		$this->loadModel('MemoSetting');
		$MemoSettings = $this->MemoSetting->find(
			'all',
			array(
				//'conditions' => array('ChallanDetail.challan_id' => $challan['Challan']['id']),
				'order' => array('id' => 'asc'),
				'recursive' => 0,
				//'limit' => 100
			)
		);

		foreach ($MemoSettings as $s_result) {
			//echo $s_result['MemoSetting']['name'].'<br>';
			if ($s_result['MemoSetting']['name'] == 'stock_validation') {
				$stock_validation = $s_result['MemoSetting']['value'];
			}
			if ($s_result['MemoSetting']['name'] == 'stock_hit') {
				$stock_hit = $s_result['MemoSetting']['value'];
			}

			if ($s_result['MemoSetting']['name'] == 'ec_calculation') {
				$ec_calculation = $s_result['MemoSetting']['value'];
			}
			if ($s_result['MemoSetting']['name'] == 'oc_calculation') {
				$oc_calculation = $s_result['MemoSetting']['value'];
			}

			if ($s_result['MemoSetting']['name'] == 'sales_calculation') {
				$sales_calculation = $s_result['MemoSetting']['value'];
			}
			if ($s_result['MemoSetting']['name'] == 'stamp_calculation') {
				$stamp_calculation = $s_result['MemoSetting']['value'];
			}
			//pr($MemoSetting);
		}

		$this->set(compact('stock_validation'));
		//end memo setting


		if ($this->request->is('post')) {

			/*  $path = APP . 'logs/';
			$myfile = fopen($path . "db_requisition_process.txt", "a") or die("Unable to open file!"); */


			/*
			 * This condition added for data synchronization 
			 * Cteated by imrul in 09, April 2017
			 * Duplicate memo check
			 */
			$count = $this->Memo->find('count', array(
				'conditions' => array(
					'Memo.id' => $id
				)
			));

			$memo_id_arr = $this->Memo->find('first', array(
				'conditions' => array(
					'Memo.id' => $id
				)
			));
			// fwrite($myfile, "\n" . $this->current_datetime() . ': ' . $memo_id_arr['Memo']['memo_no'] . ': Memo Deleted function');
			$this->loadModel('Store');
			$store_id_arr = $this->Store->find('first', array(
				'conditions' => array(
					//'Store.territory_id'=> $memo_id_arr['Memo']['territory_id'],
					'Store.office_id' => $memo_id_arr['Memo']['office_id'],
					'Store.store_type_id' => 2,
				)
			));
			$store_id = $store_id_arr['Store']['id'];



			$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
			$product_list = Set::extract($products, '{n}.Product');


			// EC Calculation 
			if ($ec_calculation) {
				$this->ec_calculation($memo_id_arr['Memo']['gross_value'], $memo_id_arr['Memo']['outlet_id'], $memo_id_arr['Memo']['territory_id'], $memo_id_arr['Memo']['memo_date'], 2);
				// OC Calculation 
			}
			if ($ec_calculation) {
				$this->oc_calculation($memo_id_arr['Memo']['territory_id'], $memo_id_arr['Memo']['gross_value'], $memo_id_arr['Memo']['outlet_id'], $memo_id_arr['Memo']['memo_date'], $memo_id_arr['Memo']['memo_time'], 2);
			}



			for ($memo_detail_count = 0; $memo_detail_count < count($memo_id_arr['MemoDetail']); $memo_detail_count++) {
				$product_id = $memo_id_arr['MemoDetail'][$memo_detail_count]['product_id'];
				$sales_qty = $memo_id_arr['MemoDetail'][$memo_detail_count]['sales_qty'];
				$sales_price = $memo_id_arr['MemoDetail'][$memo_detail_count]['price'];
				$memo_territory_id = $memo_id_arr['Memo']['territory_id'];
				$memo_no = $memo_id_arr['Memo']['memo_no'];
				$memo_date = $memo_id_arr['Memo']['memo_date'];
				$outlet_id = $memo_id_arr['Memo']['outlet_id'];
				$market_id = $memo_id_arr['Memo']['market_id'];

				$punits_pre = $this->search_array($product_id, 'id', $product_list);
				if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
					$base_quantity = $sales_qty;
				} else {
					$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
				}

				$update_type = 'add';
				//$this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 12, $memo_id_arr['Memo']['memo_date']);



				// subract sales achievement and stamp achievemt 
				// sales calculation
				$t_price = $sales_qty * $sales_price;
				if ($sales_calculation) {
					$this->sales_calculation($product_id, $memo_territory_id, $sales_qty, $t_price, $memo_date, 1);
				}

				//stamp calculation
				if ($stamp_calculation) {
					$this->stamp_calculation($memo_no, $memo_territory_id, $product_id, $outlet_id, $sales_qty, $memo_date, 1, $t_price, $market_id);
				}
			}

			$memo_id = $memo_id_arr['Memo']['id'];
			$memo_no = $memo_id_arr['Memo']['memo_no'];

			$this->MemoDetail->deleteAll(array('MemoDetail.memo_id' => $memo_id));
			//$this->Deposit->deleteAll(array('Deposit.memo_id' => $memo_no));
			$this->Deposit->deleteAll(array('Deposit.memo_id' => $memo_id));
			$this->Collection->deleteAll(array('Collection.memo_id' => $memo_id));

			$this->Memo->id = $memo_id;
			//pr($this->Memo->id);die();
			$this->Memo->delete();
			// fwrite($myfile, "\n" . $this->current_datetime() . ': ' . $memo_id_arr['Memo']['memo_no'] . ': Memo Deleted');

			if ($redirect == 1) {
				$this->flash(__('Memo was not deleted'), array('action' => 'index'));
				$this->redirect(array('action' => 'index'));
			} else {
			}
			// fclose($myfile);
		}
	}
	//for bonus and bouns schema
	public function bouns_and_scheme_id_set($b_product_id = 0, $order_date = '')
	{
		$this->loadModel('Bonus');
		//$this->loadModel('OpenCombination');
		//$this->loadModel('OpenCombinationProduct');

		$bonus_result = array();

		$b_product_qty = 0;
		$bonus_id = 0;
		$bonus_scheme_id = 0;

		$bonus_info = $this->Bonus->find(
			'first',
			array(
				'conditions' => array(
					'Bonus.effective_date <= ' => $order_date,
					'Bonus.end_date >= ' => $order_date,
					'Bonus.bonus_product_id' => $b_product_id
				),
				'recursive' => -1,
			)
		);

		//pr($bonus_info);

		if ($bonus_info) {
			$bonus_table_id = $bonus_info['Bonus']['id'];
			$mother_product_id = $bonus_info['Bonus']['mother_product_id'];
			$mother_product_quantity = $bonus_info['Bonus']['mother_product_quantity'];

			$bonus_id = $bonus_table_id;

			//echo $bonus_id;
			//break;
		}


		/*echo 'Bonus = '.$bonus_id;
		echo '<br>';
		echo 'Bonus Scheme = '. $bonus_scheme_id;
		echo '<br>';
		echo '<br>';
		echo '<br>';*/

		$bonus_result['bonus_id'] = $bonus_id;
		$bonus_result['bonus_scheme_id'] = $bonus_scheme_id;

		return $bonus_result;
	}


	/* ----- ajax methods ----- */

	public function market_list()
	{
		$rs = array(array('id' => '', 'name' => '---- Select Market -----'));
		$thana_id = $this->request->data['thana_id'];
		$territory_id = $this->request->data['territory_id'];
		//$thana_id = 2;
		$market_list = $this->Market->find('all', array(
			'conditions' => array('Market.thana_id' => $thana_id, 'Market.territory_id' => $territory_id)
		));
		$data_array = Set::extract($market_list, '{n}.Market');
		if (!empty($data_array)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}

	public function get_sales_officer_list()
	{
		$user_office_id = $this->UserAuth->getOfficeId();
		//$user_office_id = 2;
		$rs = array(array('id' => '', 'name' => '---- Select Market -----'));
		$sale_type_id = $this->request->data['sale_type_id'];
		//$sale_type_id = 1;
		if ($sale_type_id == 1 || $sale_type_id == 2 || $sale_type_id == 3 || $sale_type_id == 4) {
			$so_list = $this->SalesPerson->find('all', array(
				'conditions' => array('SalesPerson.office_id' => $user_office_id, 'User.user_group_id' => 4),
				'fields' => array('SalesPerson.id', 'SalesPerson.name')
			));
			$person_list = array();
			foreach ($so_list as $key => $val) {
				$list['id'] = $val['SalesPerson']['id'];
				$list['name'] = $val['SalesPerson']['name'];
				$person_list[] = $list;
			}
		}
		if (!empty($person_list)) {
			echo json_encode(array_merge($rs, $person_list));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}

	/* public function get_outlet_list() {
		$rs = array(array('id' => '', 'name' => '---- Select outlet -----'));
		$market_id = $this->request->data['market_id'];
		$outlet_list = $this->Outlet->find('all', array(
			'conditions' => array('Outlet.market_id' => $market_id)
			));
		$data_array = Set::extract($outlet_list, '{n}.Outlet');

		if (!empty($data_array)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}
*/

	/***** Work at 02-12-2019*****/
	public function admin_edit_backup($id = null)
	{

		ini_set('memory_limit', '-1');
		$this->loadmodel('InstrumentType');
		$instrumenttype_condition = array(
			"NOT" => array("id" => array(2, 10, 11))
		);
		$instrumentType = $this->InstrumentType->find('list', array('conditions' => $instrumenttype_condition));
		$this->set(compact('instrumentType'));

		$this->loadModel('ProductCombination');
		$this->loadModel('Combination');
		$this->Session->delete('cart_session_data');
		$this->Session->delete('matched_session_data');
		$this->Session->delete('combintaion_qty_data');

		$this->loadModel('Product');
		$this->loadModel('Memo');
		$this->loadModel('DistOutletMap');

		$office_parent_id = $this->UserAuth->getOfficeParentId();

		$current_date = date('d-m-Y', strtotime($this->current_date()));

		$this->loadModel('Product');


		/* ----- start code of product list ----- */
		$maintain_dealer_type = $this->Session->read('Office.company_info.Company.maintain_dealer');
		if ($office_parent_id == 0) {
			$product_list = $this->Product->find('list', array('order' => array('order' => 'asc')));
			$distributor_conditions = array();
		} else {
			$office_id = $this->UserAuth->getOfficeId();
			$product_list = $this->Product->find('list', array(
				'order' => array('order' => 'asc')
			));
			$distributor_conditions = array('DistOutletMap.office_id' => $office_id);
		}

		$distributers_list = $this->DistOutletMap->find('all', array(
			'conditions' => $distributor_conditions,
		));
		foreach ($distributers_list as $key => $value) {
			$distributers[$value['Outlet']['id']] = $value['DistDistributor']['name'];
		}
		$this->set(compact('distributers'));
		$office_outlets = $distributers;
		$this->set(compact('office_outlets'));
		$this->set(compact('market_list'));
		$this->set(compact('product_list'));

		/* ------- start get edit data -------- */
		$this->Order->recursive = 1;
		$options = array(
			'conditions' => array('Order.id' => $id)
		);
		$existing_record = $this->Order->find('first', $options);

		$existing_memo = $this->Memo->find('all', array('conditions' => array('Memo.memo_no' => $existing_record['Order']['order_no'])));
		//pr($existing_record);die();
		$this->loadModel('CurrentInventory');
		$msg = "";
		$canPermitted = 1;
		$not_in_stock_product = array();
		$i = 0;
		if (!empty($existing_record['OrderDetail'])) {
			foreach ($existing_record['OrderDetail'] as $key => $value) {
				$OrderDetail_record[$value['product_id']] = $value;
				$stoksinfo = $this->CurrentInventory->find('all', array(
					'conditions' => array(
						'CurrentInventory.store_id' => $existing_record['Order']['w_store_id'],
						'CurrentInventory.product_id' => $value['product_id']
					),
					'fields' => array('sum(qty) as total'),
				));
				$total_qty = $stoksinfo[0][0]['total'];
				$sales_total_qty = $this->unit_convertfrombase($value['product_id'], $value['measurement_unit_id'], $total_qty);
				$existing_record['OrderDetail'][$key]['aso_stock_qty'] = $sales_total_qty;
				$stoks = $this->CurrentInventory->find('first', array(
					'conditions' => array(
						'CurrentInventory.store_id' => $existing_record['Order']['w_store_id'],
						'CurrentInventory.product_id' => $value['product_id']
					),
					'fields' => array('Product.id', 'Product.name', 'CurrentInventory.qty'),
				));
				$productName = $this->Product->find('first', array(
					'conditions' => array('id' => $value['product_id']),
					'fields' => array('id', 'name'),
					'recursive' => -1
				));
				//$total_qty = $total_qty_arr[0][0]['total'];
				//pr($OrderDetail_record[$value['product_id']]);
				//pr($productName);
				//pr($stoks);
				if (empty($stoks)) {
					$empty_stoks = $value;
					$canPermitted = 0;

					$msg = $msg . $productName['Product']['name'] . " Is not Available!!! ";
					$not_in_stock_product[$productName['Product']['id']] = $productName['Product']['id'];
					//array_push($not_in_stock_product,$productName['Product']['id']);
					$i++;
					$msg = $msg . "<br>";
				} elseif (!empty($stoks)) {
					if ($total_qty < $value['sales_qty']) {
						$canPermitted = 0;
						$msg = $msg . $stoks['Product']['name'] . " Is Insufficient!!! ";
						$not_in_stock_product[$productName['Product']['id']] = $productName['Product']['id'];
						//$not_in_stock_product[$i]['id']=$stoks['Product']['id'];
						//array_push($not_in_stock_product,$productName['Product']['id']);
						$i++;
						$msg = $msg . "<br>";
					}
				}
			}
		}
		//die();
		//pr($not_in_stock_product);die();
		if ($canPermitted == 0) {
			$this->Session->setFlash(__($msg), 'flash/warning');
		}
		$this->set(compact('canPermitted'));
		$this->set(compact('not_in_stock_product'));
		$details_data = array();
		foreach ($existing_record['OrderDetail'] as $detail_val) {
			$product = $detail_val['product_id'];
			$this->Combination->unbindModel(
				array('hasMany' => array('ProductCombination'))
			);
			$combination_list = $this->Combination->find('all', array(
				'conditions' => array('ProductCombination.product_id' => $product),
				'joins' => array(
					array(
						'alias' => 'ProductCombination',
						'table' => 'product_combinations',
						'type' => 'INNER',
						'conditions' => 'Combination.id = ProductCombination.combination_id'
					)
				),
				'fields' => array('Combination.all_products_in_combination'),
				'limit' => 1
			));
			if (!empty($combination_list)) {
				$combined_product = $combination_list[0]['Combination']['all_products_in_combination'];
				$detail_val['combined_product'] = $combined_product;
			}
			$details_data[] = $detail_val;
		}

		$existing_record['OrderDetail'] = $details_data;

		$this->loadModel('MeasurementUnit');
		for ($i = 0; $i < count($details_data); $i++) {
			$measurement_unit_id = $details_data[$i]['measurement_unit_id'];
			if ($measurement_unit_id != 0) {
				$measurement_unit_name = $this->MeasurementUnit->find('all', array(
					'conditions' => array('MeasurementUnit.id' => $measurement_unit_id),
					'fields' => array('name'),
					'recursive' => -1
				));
				$existing_record['OrderDetail'][$i]['measurement_unit_name'] = $measurement_unit_name[0]['MeasurementUnit']['name'];
			}
		}

		$existing_record['office_id'] = $existing_record['SalesPerson']['office_id'];
		$existing_record['territory_id'] = $existing_record['Order']['territory_id'];
		$existing_record['market_id'] = $existing_record['Order']['market_id'];
		$existing_record['outlet_id'] = $existing_record['Order']['outlet_id'];
		$existing_record['order_time'] = date('d-m-Y', strtotime($existing_record['Order']['order_time']));
		$existing_record['order_date'] = date('d-m-Y', strtotime($existing_record['Order']['order_date']));
		$existing_record['order_no'] = $existing_record['Order']['order_no'];
		$existing_record['memo_reference_no'] = $existing_record['Order']['memo_reference_no'];

		$existing_record['order_reference_no'] = $existing_record['Order']['order_reference_no'];
		$existing_record['reference_number'] = $existing_record['Order']['instrument_reference_no'];

		//pr($existing_record);die();
		$this->LoadModel('Product');
		$this->LoadModel('Store');
		$store_id = $this->Store->find('first', array(
			'fields' => array('Store.id'),
			'conditions' => array('Store.id' => $existing_record['Order']['w_store_id']),
			'recursive' => -1
		));

		$open_bonus_product = $this->Product->find('all', array(
			'fields' => array('Product.id', 'Product.name'),
			'joins' => array(
				array(
					'table' => 'current_inventories',
					'alias' => 'CurrentInventory',
					'type' => 'Inner',
					'conditions' => 'CurrentInventory.product_id=Product.id'
				),
				array(
					'table' => 'open_combination_products',
					'alias' => 'OpenCombinationProduct',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.product_id=Product.id'
				),
				array(
					'table' => 'open_combinations',
					'alias' => 'OpenCombination',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.combination_id=OpenCombination.id'
				),

			),
			'conditions' => array(
				'CurrentInventory.qty >' => 0,
				'CurrentInventory.store_id' => $store_id['Store']['id'],
				'OpenCombination.start_date <=' => date('Y-m-d'),
				'OpenCombination.end_date >=' => date('Y-m-d'),
				'OpenCombination.is_bonus' => 1
			),
			'recursive' => -1
		));
		$open_bonus_product_option = array();
		foreach ($open_bonus_product as $bonus_product) {
			$open_bonus_product_option[$bonus_product['Product']['id']] = $bonus_product['Product']['name'];
		}

		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$office_conditions = array();
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}

		$this->loadModel('Office');
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

		$office_id = $existing_record['office_id'];
		$territory_id = $existing_record['territory_id'];
		$market_id = $existing_record['market_id'];
		$outlets = array();


		$this->loadModel('Territory');
		$territories_list = $this->Territory->find('all', array(
			'conditions' => array('Territory.office_id' => $office_id),
			'order' => array('Territory.name' => 'asc')
		));
		$territories = array();
		foreach ($territories_list as $t_result) {
			$territories[$t_result['Territory']['id']] = $t_result['Territory']['name'] . ' (' . $t_result['SalesPerson']['name'] . ')';
		}


		$territory_ids = array($territory_id);


		$markets = $this->Market->find('list', array(
			'conditions' => array('territory_id' => $territory_ids),
			'order' => array('name' => 'asc')
		));

		$outlets = $distributers;
		//pr($outlets);die();
		//$outlets = $this->get_outlet_list_with_distributor_name($company_id);
		$this->loadModel('Store');
		$this->loadModel('Product');
		$this->loadModel('CurrentInventory');
		$store_info = $this->Store->find('first', array(
			'conditions' => array(
				'Store.id' => $existing_record['Order']['w_store_id']
			),
			'recursive' => -1
		));
		$store_id = $store_info['Store']['id'];
		foreach ($existing_record['OrderDetail'] as $key => $single_product) {
			$total_qty_arr = $this->CurrentInventory->find('all', array(
				'conditions' => array('store_id' => $store_id, 'product_id' => $single_product['product_id']),
				'fields' => array('sum(qty) as total'),
				'recursive' => -1
			));

			$total_qty = $total_qty_arr[0][0]['total'];

			$sales_total_qty = $this->unit_convertfrombase($single_product['product_id'], $single_product['measurement_unit_id'], $total_qty);

			$existing_record['OrderDetail'][$key]['stock_qty'] = $sales_total_qty;
		}


		$products_from_ci = $this->CurrentInventory->find('all', array(
			'fields' => array('DISTINCT CurrentInventory.product_id'),
			'conditions' => array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.qty > ' => 0, 'inventory_status_id' => 1)
		));

		$product_ci = array();
		foreach ($products_from_ci as $each_ci) {
			$product_ci[] = $each_ci['CurrentInventory']['product_id'];
		}
		foreach ($existing_record['OrderDetail'] as $value) {
			$product_ci[] = $value['product_id'];
		}

		$product_ci_in = implode(",", $product_ci);

		/* ------- end get edit data -------- */


		/*-----------My Work--------------*/
		$this->loadModel('ProductPrice');
		$this->loadModel('ProductCombination');


		foreach ($existing_record['OrderDetail'] as $key => $value) {
			$existing_product_category_id_array = $this->Product->find('all', array(
				'conditions' => array('Product.id' => $value['product_id']),
				'fields' => array('product_category_id'),
				'recursive' => -1
			));

			$existing_product_category_id = $existing_product_category_id_array[0]['Product']['product_category_id'];

			if ($existing_product_category_id != 32) {
				$individual_slab = array();
				$combined_slab = array();
				$all_combination_id = array();

				$retrieve_price_combination[$value['product_id']] = $this->ProductPrice->find('all', array(
					'conditions' => array('ProductPrice.product_id' => $value['product_id'], 'ProductPrice.has_combination' => 0)
				));

				foreach ($retrieve_price_combination[$value['product_id']][0]['ProductCombination'] as $key => $value2) {
					$individual_slab[$value2['min_qty']] = $value2['price'];
				}


				$combination_info = $this->ProductCombination->find('first', array(
					'conditions' => array('ProductCombination.product_id' => $value['product_id'], 'ProductCombination.combination_id !=' => 0)
				));

				if (!empty($combination_info['ProductCombination']['combination_id'])) {
					$combination_id = $combination_info['ProductCombination']['combination_id'];
					$all_combination_id_info = $this->ProductCombination->find('all', array(
						'conditions' => array('ProductCombination.combination_id' => $combination_id)
					));

					$combined_product = '';
					foreach ($all_combination_id_info as $key => $individual_combination_id) {
						$all_combination_id[$individual_combination_id['ProductCombination']['product_id']] = $individual_combination_id['ProductCombination']['price'];

						$individual_combined_product_id = $individual_combination_id['ProductCombination']['product_id'];

						$combined_product = $combined_product . ',' . $individual_combined_product_id;
					}
					$trimmed_combined_product = ltrim($combined_product, ',');

					$combined_slab[$combination_info['ProductCombination']['min_qty']] = $all_combination_id;

					$matched_combined_product_id_array = explode(',', $trimmed_combined_product);
					asort($matched_combined_product_id_array);
					$matched_combined_product_id = implode(',', $matched_combined_product_id_array);
				} else {
					$combined_slab = array();
					$matched_combined_product_id = '';
				}



				$edited_cart_data[$value['product_id']] = array(
					'product_price' => array(
						'id' => $retrieve_price_combination[$value['product_id']][0]['ProductPrice']['id'],
						'product_id' => $value['product_id'],
						'general_price' => $retrieve_price_combination[$value['product_id']][0]['ProductPrice']['general_price'],
						'effective_date' => $retrieve_price_combination[$value['product_id']][0]['ProductPrice']['effective_date']
					),
					'individual_slab' => $individual_slab,
					'combined_slab' => $combined_slab,
					'combined_product' => $matched_combined_product_id
				);



				if (!empty($matched_combined_product_id)) {
					$edited_matched_data[$matched_combined_product_id] = array(
						'count' => '4',
						'is_matched_yet' => 'NO',
						'matched_count_so_far' => '2',
						'matched_id_so_far' => '63,65'
					);

					$edited_current_qty_data[$value['product_id']] = $value['sales_qty'];
				}
			}
		}

		if (!empty($edited_cart_data)) {
			$this->Session->write('cart_session_data', $edited_cart_data);
		}
		if (!empty($edited_matched_data)) {
			$this->Session->write('matched_session_data', $edited_matched_data);
		}
		if (!empty($edited_current_qty_data)) {
			$this->Session->write('combintaion_qty_data', $edited_current_qty_data);
		}


		$this->set('page_title', 'Edit Order');
		$this->Order->id = $id;
		if (!$this->Order->exists($id)) {
			throw new NotFoundException(__('Invalid Order'));
		}
		/* -------- create individual Product data --------- */



		global $cart_data, $matched_array;
		$cart_data = array();
		$matched_array = array();
		$qty_session_data = array();


		/* ---------creating prepare data---------- */
		$prepare_cart_data = array();
		if (!empty($filter_product['Product']['id'])) {
			$prepare_cart_data[$filter_product['Product']['id']]['Product'] = $filter_product['Product'];
			$prepare_cart_data[$filter_product['Product']['id']]['ProductPrice'] = $filter_product['ProductPrice'];
			foreach ($filter_product['Individual_slab'] as $individual_slab_val) {
				$prepare_cart_data[$filter_product['Product']['id']]['Individual_slab'][$individual_slab_val['min_qty']] = $individual_slab_val['price'];
			}
			if (!empty($filter_product['Combination'])) {
				$prepare_cart_data[$filter_product['Product']['id']]['Combination'] = $filter_product['Combination'];
			}
			if (!empty($filter_product['Combination_id'])) {
				/* ---- start ------- */
				$this->Combination->recursive = 2;
				$condition_value1['Combination.id'] = $filter_product['Combination_id']; //$filter_product['Combination']['id'];
				$combination_slab_data_option = array(
					'conditions' => array($condition_value1),
				);
				$combination_slab_data = $this->Combination->find('all', $combination_slab_data_option);
				/* ----- end -------- */

				$combination_slab = array();
				foreach ($combination_slab_data as $combine_group) {
					foreach ($combine_group['ProductCombination'] as $combine_val) {
						$combination_slab[$combine_val['min_qty']][$combine_val['product_id']] = $combine_val['Childrel']['price'];
					}
				}
				if (!empty($combination_slab)) {
					$prepare_cart_data[$filter_product['Product']['id']]['Combined_slab'] = $combination_slab;
				}
			}
		}
		/* ----------start create cart data and matched data ----------- */



		/* ------ unset cart data ------- */

		$user_office_id = $this->UserAuth->getOfficeId();
		$sales_person_list = $this->SalesPerson->find('list', array(
			'conditions' => array('SalesPerson.office_id' => $user_office_id)
		));
		$count = 0;
		if ($this->request->is('post')) {

			//pr($this->request->data);die(); 


			$outlet_is_within_group = $this->outletGroupCheck($this->request->data['OrderProces']['distribut_outlet_id']);
			$product_is_injectable = $this->productInjectableCheck($this->request->data['OrderDetail']['product_id']);

			if (array_key_exists('save', $this->request->data)) {
				/*foreach ($this->request->data['OrderDetail']['remaining_qty'] as $key => $val) 
				{
				   if($val != 0){
						$count++;
					}
				}*/
				if ($count == 0) {
					$orderData['is_closed'] = 1;
				} else {
					$orderData['is_closed'] = 0;
				}
			}
			$order_id = $id;

			/*START ADD NEW*/
			//get office id 
			$office_id = $this->request->data['OrderProces']['office_id'];
			$outlet_id = $this->request->data['OrderProces']['distribut_outlet_id'];
			//get thana id 
			$this->loadModel('Outlet');
			$getOutlets = $this->Outlet->find('all', array(
				'conditions' => array('Outlet.id' => $outlet_id),
			));
			$outlet_info = $this->DistOutletMap->find('first', array(
				'conditions' => array('DistOutletMap.outlet_id' => $this->request->data['OrderProces']['distribut_outlet_id']),

			));
			//pr($outlet_info);die();
			$market_id = $getOutlets[0]['Outlet']['market_id'];
			$this->loadModel('Market');
			$market_info = $this->Market->find('first', array(
				'conditions' => array('Market.id' => $market_id),
				'fields' => 'Market.thana_id',
				'order' => array('Market.id' => 'asc'),
				'recursive' => -1,
				//'limit' => 100
			));
			$thana_id = $market_info['Market']['thana_id'];
			$distributor_id = $outlet_info['DistDistributor']['id'];
			/*END ADD NEW*/
			$dealer_is_limit_check = 1;
			$credit_amount = $this->request->data['Order']['gross_value'];
			$balance = 0;
			$this->request->data['OrderProces']['is_active'] = 1;
			if (array_key_exists('draft', $this->request->data)) {
				$this->request->data['OrderProces']['status'] = 2;
				$this->request->data['OrderProces']['confirm_status'] = 1;
				//$this->request->data['OrderProces']['manage_draft_status'] = 1;
				$message = "Order Has Been Saved as Draft";

				$is_execute = 0;
			} else {
				$message = "Order Has Been Saved";
				$this->request->data['OrderProces']['status'] = 2;
				$this->request->data['OrderProces']['confirm_status'] = 2;
				//$this->request->data['OrderProces']['manage_draft_status'] = 2;
				$is_execute = 1;

				/*************************** Balance Check *********************************************************/

				$this->loadModel('DistDistributorBalance');
				$dealer_limit_info = $this->DistDistributorBalance->find('first', array(
					'conditions' => array(
						'DistDistributorBalance.dist_distributor_id' => $distributor_id
					),
					'limit' => 1,
					'recursive' => -1
				));

				$credit_amount = $this->request->data['Order']['gross_value'];
				$dealer_limit = $dealer_limit_info['DistDistributorBalance']['balance'];

				$this->loadModel('DistDistributorBalanceHistory');
				$dealer_balance_info = $this->DistDistributorBalanceHistory->find('first', array(
					'conditions' => array(
						'DistDistributorBalanceHistory.dist_distributor_id' => $distributor_id,
						//'DistDistributorBalanceHistory.is_execute' => 1,
					),
					'order' => 'DistDistributorBalanceHistory.id DESC',
					'recursive' => -1
				));

				if ($dealer_balance_info) {
					$balance = $dealer_limit - $this->request->data['Order']['gross_value'];
				} else {
					$balance = 0;
				}

				$dealer_balance_data = array();
				$dealer_balance = array();
				$dealer_balance['id'] = $dealer_limit_info['DistDistributorBalance']['id'];
				$dealer_balance['office_id'] = $dealer_limit_info['DistDistributorBalance']['office_id'];
				$dealer_balance['dist_distributor_id'] = $dealer_limit_info['DistDistributorBalance']['dist_distributor_id'];
				$dealer_balance['balance'] = $balance;

				if ($this->DistDistributorBalance->save($dealer_balance)) {

					$dealer_balance_data['dist_distributor_id'] = $distributor_id;
					$dealer_balance_data['dist_distributor_balance_id'] = $dealer_balance_info['DistDistributorBalanceHistory']['dist_distributor_balance_id'];
					$dealer_balance_data['office_id'] = $this->request->data['OrderProces']['office_id'];
					$dealer_balance_data['memo_value'] = $this->request->data['Order']['gross_value'];
					$dealer_balance_data['balance'] = $balance;
					$dealer_balance_data['balance_type'] = 2;
					$dealer_balance_data['balance_transaction_type_id'] = 2;
					$dealer_balance_data['transaction_amount'] = $this->request->data['Order']['gross_value'];
					$dealer_balance_data['transaction_date'] = date('Y-m-d');
					$dealer_balance_data['created_at'] = $this->current_datetime();
					$dealer_balance_data['created_by'] = $this->UserAuth->getUserId();
					$dealer_balance_data['updated_at'] = $this->current_datetime();
					$dealer_balance_data['updated_by'] = $this->UserAuth->getUserId();
					$this->DistDistributorBalanceHistory->create();
					$this->DistDistributorBalanceHistory->save($dealer_balance_data);
				}

				/*************************** Balance Check End *********************************************************/
			}

			//for distibuter limit amount
			$maintain_dealer_type = 1;
			//for distibuter limit amount

			$maintain_dealer_type = 1;
			$dealer_is_limit_check = 1;

			//end for distributer limit 

			$sales_person = $this->SalesPerson->find('list', array(
				'conditions' => array('territory_id' => $this->request->data['OrderProces']['territory_id']),
				'order' => array('name' => 'asc')
			));
			$this->request->data['OrderProces']['sales_person_id'] = key($sales_person);

			//$this->request->data['OrderProces']['entry_date'] = date('Y-m-d', strtotime($this->request->data['OrderProces']['entry_date']));
			$this->request->data['OrderProces']['order_date'] = date('Y-m-d', strtotime($this->request->data['OrderProces']['order_date']));

			$orderData['id'] = $order_id;
			$orderData['office_id'] = $this->request->data['OrderProces']['office_id'];

			$orderData['territory_id'] = $this->request->data['OrderProces']['territory_id'];
			$orderData['market_id'] = $market_id;
			$orderData['outlet_id'] = $this->request->data['OrderProces']['distribut_outlet_id'];
			$orderData['entry_date'] = $this->request->data['OrderProces']['entry_date'];
			$orderData['order_date'] = $this->request->data['OrderProces']['order_date'];
			$order_no = $orderData['order_no'] = $this->request->data['OrderProces']['order_no'];
			$orderData['gross_value'] = $this->request->data['Order']['gross_value'];
			//$orderData['cash_recieved'] = $this->request->data['Order']['cash_recieved'];
			//$orderData['credit_amount'] = $this->request->data['Order']['credit_amount'];
			$orderData['w_store_id'] = $this->request->data['OrderProces']['w_store_id'];
			$orderData['is_active'] = $this->request->data['OrderProces']['is_active'];
			$orderData['status'] = $this->request->data['OrderProces']['status'];
			$orderData['confirm_status'] = $this->request->data['OrderProces']['confirm_status'];
			//$orderData['cash_recieved'] = $this->request->data['Order']['cash_recieved'];
			$orderData['order_time'] = $this->request->data['OrderProces']['entry_date'];


			$orderData['order_reference_no'] = $this->request->data['OrderProces']['order_reference_no'];

			//$orderData['instrument_reference_no'] = $this->request->data['Order']['instrument_reference_no'];

			//$orderData['instrument_type'] = $this->request->data['OrderProces']['instrument_type'];

			$orderData['sales_person_id'] = $this->request->data['OrderProces']['sales_person_id'];
			$orderData['from_app'] = 0;
			$orderData['action'] = 1;
			$orderData['confirmed'] = 1;
			//pr($orderData);die();

			$orderData['order_reference_no'] = $this->request->data['OrderProces']['order_reference_no'];


			$orderData['created_at'] = $this->current_datetime();
			$orderData['created_by'] = $this->UserAuth->getUserId();
			$orderData['updated_at'] = $this->current_datetime();
			$orderData['updated_by'] = $this->UserAuth->getUserId();


			$orderData['office_id'] = $office_id ? $office_id : 0;
			$orderData['thana_id'] = $thana_id ? $thana_id : 0;

			$new_orderdata['id'] = $orderData['id'];
			$new_orderdata['status'] = $orderData['status'];
			//$new_orderdata['cash_recieved']=$orderData['cash_recieved'];           
			//$new_orderdata['credit_amount']=$orderData['credit_amount'];           
			//$new_orderdata['instrument_type']=$orderData['instrument_type'];
			//$new_orderdata['instrument_reference_no']=$orderData['instrument_reference_no'];
			$new_orderdata['confirm_status'] = $orderData['confirm_status'];
			//pr($new_orderdata);die();
			if ($this->Order->save($new_orderdata)) {

				/************** Delete Draft memo first 17-09-2019 *****************/
				if (array_key_exists('save', $this->request->data)) {
					$this->loadModel('Memo');
					$memos = $this->Memo->find('first', array('conditions' => array('Memo.memo_no' => $order_no), 'order' => 'Memo.id DESC'));
					$memo_id = $memos['Memo']['id'];
					$this->admin_deletememo($memo_id, 0);
				}
				/************** Memo Deleted Draft 17-09-2019 *****************/
				$order_info_arr = $this->Order->find('first', array(
					'conditions' => array(
						'Order.id' => $order_id
					)
				));
				$this->loadModel('Store');
				$store_id_arr = $this->Store->find('first', array(
					'conditions' => array(
						'Store.office_id' => $order_info_arr['Order']['office_id']
					)
				));
				$store_id = $store_id_arr['Store']['id'];
				if ($order_id) {

					$all_product_id = $this->request->data['OrderDetail']['product_id'];
					if (!empty($this->request->data['OrderDetail'])) {
						$total_product_data = array();
						$order_details = array();
						$order_details['OrderDetail']['order_id'] = $order_id;
						foreach ($this->request->data['OrderDetail']['product_id'] as $key => $val) {
							/*if($val == NULL)
							{
								continue;
							}*/
							/*$measurement_unit_id = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
							if($measurement_unit_id != 0 || !empty($measurement_unit_id)){*/
							$price = $this->request->data['OrderDetail']['Price'][$key];
							if ($price != 0) {
								$product_id = $order_details['OrderDetail']['product_id'] = $val;
								$order_details['OrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
								$sales_price = $order_details['OrderDetail']['price'] = $this->request->data['OrderDetail']['Price'][$key];
								$sales_qty = $order_details['OrderDetail']['sales_qty'] = $this->request->data['OrderDetail']['sales_qty'][$key];
								//$order_details['OrderDetail']['remaining_qty'] = $this->request->data['OrderDetail']['remaining_qty'][$key];
								$order_details['OrderDetail']['deliverd_qty'] = $this->request->data['OrderDetail']['deliverd_qty'][$key];
								$order_details['OrderDetail']['product_price_id'] = $this->request->data['OrderDetail']['product_price_id'][$key];
								$order_details['OrderDetail']['bonus_qty'] = $this->request->data['OrderDetail']['bonus_product_qty'][$key];

								$product_price_slab_id = 0;
								if ($sales_price > 0) {
									$product_price_slab_id = $this->get_product_price_id($val, $sales_price, $all_product_id);
								}
								$order_details['OrderDetail']['product_price_id'] = $product_price_slab_id['product_price_id'];
								$order_details['OrderDetail']['product_combination_id'] = $product_price_slab_id['combination_id'];
								$order_details['OrderDetail']['bonus_qty'] = $this->request->data['OrderDetail']['bonus_product_qty'][$key];


								if ($this->request->data['OrderDetail']['bonus_product_id'][$key] != 0) {
									$order_details['OrderDetail']['bonus_product_id'] = $this->request->data['OrderDetail']['bonus_product_id'][$key];
								} else {
									$order_details['OrderDetail']['bonus_product_id'] = NULL;
								}

								//Start for bonus
								$order_date = date('Y-m-d', strtotime($this->request->data['OrderProces']['order_date']));
								$bonus_product_id = $this->request->data['OrderDetail']['bonus_product_id'];
								$bonus_product_qty = $this->request->data['OrderDetail']['bonus_product_qty'];
								$order_details['OrderDetail']['bonus_id'] = 0;
								$order_details['OrderDetail']['bonus_scheme_id'] = 0;
								if ($bonus_product_qty[$key] > 0) {
									$b_product_id = $bonus_product_id[$key];
									$bonus_result = $this->bouns_and_scheme_id_set($b_product_id, $order_date);
									$order_details['OrderDetail']['bonus_id'] = $bonus_result['bonus_id'];
								}
								//End for bouns
								$new_order_details['OrderDetail'] = $OrderDetail_record[$product_id];

								$newtempOrderDetails['TempOrderDetail'] = $new_order_details['OrderDetail'];
								$temp_new_total_product_data[] = $newtempOrderDetails;

								$deliverd_qty = $OrderDetail_record[$product_id]['deliverd_qty'];

								/*if($order_details['OrderDetail']['deliverd_qty'] == 0){
									$new_order_details['OrderDetail']['remaining_qty']= $sales_qty;
								}
								else{
									$new_order_details['OrderDetail']['remaining_qty']= $order_details['OrderDetail']['remaining_qty'];
								}*/

								if (array_key_exists('save', $this->request->data)) {
									$new_order_details['OrderDetail']['deliverd_qty'] = $order_details['OrderDetail']['deliverd_qty'];
								} else {
									$new_order_details['OrderDetail']['deliverd_qty'] = $order_details['OrderDetail']['deliverd_qty'] + $deliverd_qty;
								}
								$new_total_product_data[] = $new_order_details;

								$total_product_data[] = $order_details;
							} else {
								$sales_qty = $this->request->data['OrderDetail']['sales_qty'][$key];
								if (!empty($sales_qty)) {
									$product_id = $order_details['OrderDetail']['product_id'] = $val;
									$order_details['OrderDetail']['measurement_unit_id'] = 0;
									$sales_price = $order_details['OrderDetail']['price'] = 0;
									$order_details['OrderDetail']['sales_qty'] = $sales_qty;
									$order_details['OrderDetail']['bonus_qty'] = $this->request->data['OrderDetail']['sales_qty'][$key];
									$order_details['OrderDetail']['bonus_product_id'] = $product_id;
									$order_details['OrderDetail']['is_bonus'] = 1;
									$order_date = date('Y-m-d', strtotime($this->request->data['OrderProces']['order_date']));

									$total_product_data[] = $order_details;
								}
							}
							/*if($bonus_product_qty[$key] > 0)
							{
								$order_details_bonus['OrderDetail']['order_id'] = $order_id;
								$order_details_bonus['OrderDetail']['is_bonus'] = 1;
								$order_details_bonus['OrderDetail']['product_id'] = $product_id;
								$order_details_bonus['OrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
								$order_details_bonus['OrderDetail']['price'] = 0.0;
								$order_details_bonus['OrderDetail']['sales_qty'] = $this->request->data['OrderDetail']['bonus_product_qty'][$key];
								$total_product_data[] = $order_details_bonus;
								unset($order_details_bonus);

								if (array_key_exists('save', $this->request->data)){
									$stock_hit=1;
									if($stock_hit)
										{
											$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
											$product_list = Set::extract($products, '{n}.Product');
											$bonus_qty = $this->request->data['MemoDetail']['bonus_product_qty'][$key];
											$punits_pre = $this->search_array($product_id, 'id', $product_list);
											if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
												$base_quantity = $bonus_qty;
											} else {
												$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $bonus_qty);
											}
											
											$update_type = 'deduct';
											$this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 11, $this->request->data['OrderProces']['order_date']);
										}
									}
							}*/

							if (array_key_exists('save', $this->request->data)) {
								$stock_hit = 1;
								/*if($stock_hit)
								{
									$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
									$product_list = Set::extract($products, '{n}.Product');
									
									$punits_pre = $this->search_array($product_id, 'id', $product_list);
									if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
										$base_quantity = $sales_qty;
									} else {
										$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
									}
									
									$update_type = 'deduct';
									$this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 11, $this->request->data['OrderProces']['order_date']);
								}*/
							}

							$tempOrderDetails['TempOrderDetail'] = $order_details['OrderDetail'];
							$temp_total_product_data[] = $tempOrderDetails;
						}
						if (array_key_exists('draft', $this->request->data)) {
							$this->loadModel('TempOrderDetail');
							$this->TempOrderDetail->create();
							//$this->TempOrderDetail->saveAll($temp_total_product_data);
							$this->TempOrderDetail->saveAll($temp_new_total_product_data);
						} else {
							$this->loadModel('TempOrderDetail');
							$this->TempOrderDetail->deleteAll(array('TempOrderDetail.order_id' => $order_id));
						}

						$this->OrderDetail->saveAll($new_total_product_data);
					}
				}

				/******************* Memo create date: 04-09-2019 ***************************/
				/*********************Memo Create for no Confirmation Type *************************/
				$this->loadModel('Memo');
				$this->loadModel('OrderDetail');
				$this->loadModel('Order');
				$this->loadModel('Collection');
				/***************Delete Data from temp Table 05-09-2019**************************/
				/*$this->loadModel('TempOrderDetail');
			$this->TempOrderDetail->deleteAll(array('TempOrderDetail.order_id' => $order_id));*/
				// $this->admin_delete($order_id, 0);
				/***************end  Delete Data from temp Table 05-09-2019**************************/
				$order_info = $this->Order->find('first', array(
					'conditions' => array('Order.id' => $order_id),
					'recursive' => -1
				));

				$order_detail_info = $this->OrderDetail->find('all', array(
					'conditions' => array('OrderDetail.order_id' => $order_id),
					'recursive' => -1
				));
				// pr($this->request->data['OrderDetail']);die();
				$memo = array();
				$memo['sales_person_id'] = $order_info['Order']['sales_person_id'];
				$memo['entry_date'] = $this->current_datetime();
				$memo['memo_date'] = $this->current_datetime();

				$memo['office_id'] = $order_info['Order']['office_id'];
				$memo['sale_type_id'] = 1;
				$memo['territory_id'] = $order_info['Order']['territory_id'];
				$memo['thana_id'] = $order_info['Order']['thana_id'];
				$memo['market_id'] = $order_info['Order']['market_id'];
				$memo['outlet_id'] = $order_info['Order']['outlet_id'];

				$memo['memo_date'] = $order_info['Order']['order_date'];
				$memo['memo_no'] = $order_info['Order']['order_no'];
				$memo['gross_value'] = $order_info['Order']['gross_value'];
				$memo['is_active'] = $order_info['Order']['is_active'];
				$memo['w_store_id'] = $order_info['Order']['w_store_id'];
				if (array_key_exists('save', $this->request->data)) {
					$memo['status'] = 1;
				} else {
					$memo['status'] = 0;
					$this->Memo->create();
				}
				$memo['memo_time'] = $this->current_datetime();
				$memo['sales_person_id'] = $order_info['Order']['sales_person_id'];
				$memo['from_app'] = 0;
				$memo['action'] = 1;
				$memo['is_distributor'] = 1;
				$memo['is_program'] = 0;


				$memo['memo_reference_no'] = $order_info['Order']['memo_reference_no'];

				$memo['created_at'] = $this->current_datetime();
				$memo['created_by'] = $this->UserAuth->getUserId();
				$memo['updated_at'] = $this->current_datetime();
				$memo['updated_by'] = $this->UserAuth->getUserId();

				//$this->Memo->create();
				// pr($memo);die();

				if ($this->Memo->save($memo)) {

					$memo_id = $this->Memo->getLastInsertId();
					$memo_info_arr = $this->Memo->find('first', array(
						'conditions' => array(
							'Memo.id' => $memo_id
						)
					));

					if ($memo_id) {
						if (!empty($order_detail_info[0]['OrderDetail'])) {
							$this->loadModel('MemoDetail');
							$total_product_data = array();
							$memo_details = array();
							$memo_details['MemoDetail']['memo_id'] = $memo_id;

							foreach ($order_detail_info as $order_detail_result) {
								if ($order_detail_result['OrderDetail']['deliverd_qty'] > 0) {
									$product_id = $order_detail_result['OrderDetail']['product_id'];
									$memo_details['MemoDetail']['product_id'] = $product_id;
									$memo_details['MemoDetail']['measurement_unit_id'] = $order_detail_result['OrderDetail']['measurement_unit_id'];
									$memo_details['MemoDetail']['price'] = $order_detail_result['OrderDetail']['price'];
									$memo_details['MemoDetail']['sales_qty'] = $order_detail_result['OrderDetail']['deliverd_qty'];

									$memo_details['MemoDetail']['product_price_id'] = $order_detail_result['OrderDetail']['product_price_id'];
									$memo_details['MemoDetail']['bonus_qty'] = $order_detail_result['OrderDetail']['bonus_qty'];
									$memo_details['MemoDetail']['offer_id'] = $order_detail_result['OrderDetail']['offer_id'];
									$memo_details['MemoDetail']['bonus_product_id'] = $order_detail_result['OrderDetail']['bonus_product_id'];
									$memo_details['MemoDetail']['bonus_id'] = $order_detail_result['OrderDetail']['bonus_id'];
									$memo_details['MemoDetail']['bonus_scheme_id'] = $order_detail_result['OrderDetail']['bonus_scheme_id'];
									$memo_details['MemoDetail']['price_combination_id'] = $order_detail_result['OrderDetail']['price_combination_id'];
									$memo_details['MemoDetail']['product_combination_id'] = $order_detail_result['OrderDetail']['product_combination_id'];
									$memo_details['MemoDetail']['is_bonus'] = $order_detail_result['OrderDetail']['is_bonus'];
									$memo_details['MemoDetail']['deliverd_qty'] = $order_detail_result['OrderDetail']['deliverd_qty'];


									$total_product_data[] = $memo_details;
								}
							}

							$this->MemoDetail->saveAll($total_product_data);
						}

						$order_outlet_id = $order_info['Order']['outlet_id'];
						$this->loadmodel('DistOutletMap');
						$outlet_info = $this->DistOutletMap->find('first', array(
							'conditions' => array('DistOutletMap.outlet_id' => $order_outlet_id),
							'fields' => array('DistOutletMap.dist_distributor_id'),
						));
						$distibuter_id = $outlet_info['DistOutletMap']['dist_distributor_id'];
						$this->loadmodel('DistStore');
						$dist_store_id = $this->DistStore->find('first', array(
							'conditions' => array('DistStore.dist_distributor_id' => $distibuter_id),
							'fields' => array('DistStore.id'),
						));
						//pr($dist_store_id['DistStore']['id']);die();

						/************* create Challan *************/

						if (array_key_exists('save', $this->request->data)) {
							if ($maintain_dealer_type == 1) {
								/*****************Create Chalan and *****************/
								$this->loadModel('DistChallan');
								$this->loadModel('DistChallanDetail');
								$this->loadModel('CurrentInventory');
								//$company_id  =$this->Session->read('Office.company_id');
								$office_id  = $this->request->data['OrderProces']['office_id'];
								$store_id = $this->request->data['OrderProces']['w_store_id'];
								//$challan['company_id']=$company_id;
								$challan['office_id'] = $office_id;
								$challan['memo_id'] = $memo_info_arr['Memo']['id'];
								$challan['memo_no'] = $memo_info_arr['Memo']['memo_no'];
								$challan['challan_no'] = $memo_info_arr['Memo']['memo_no'];
								$challan['receiver_dist_store_id'] = $dist_store_id['DistStore']['id'];
								$challan['receiving_transaction_type'] = 2;
								$challan['received_date'] = '';
								$challan['challan_date'] = $this->current_datetime();
								$challan['dist_distributor_id'] = $distibuter_id;
								$challan['challan_referance_no'] = '';
								$challan['challan_type'] = "";
								$challan['remarks'] = 0;
								$challan['status'] = 1;
								$challan['so_id'] = $order_info['Order']['sales_person_id'];
								$challan['is_close'] = 0;
								$challan['inventory_status_id'] = 2;
								$challan['transaction_type_id'] = 2;
								$challan['sender_store_id'] = $store_id;
								$challan['created_at'] = $this->current_datetime();
								$challan['created_by'] = $this->UserAuth->getUserId();
								$challan['updated_at'] = $this->current_datetime();
								$challan['updated_by'] = $this->UserAuth->getUserId();
								//pr();die();
								$this->DistChallan->create();
								// pr($challan);
								if ($this->DistChallan->save($challan)) {

									$product_list = $this->request->data['OrderDetail'];
									//pr($product_list);
									if (!empty($product_list['product_id'])) {
										$data_array = array();

										foreach ($product_list['product_id'] as $key => $val) {
											if ($product_list['product_id'][$key] != '') {
												if ($product_list['measurement_unit_id'][$key] != 0 || !empty($product_list['measurement_unit_id'][$key])) {

													if ($product_list['deliverd_qty'][$key] > 0) {
														if (!empty($val)) {
															$inventories = $this->CurrentInventory->find('first', array(
																'conditions' => array(
																	'CurrentInventory.product_id' => $val,
																	'CurrentInventory.store_id' => $store_id,
																),
																'recursive' => -1,
															));
															$batch_no = $inventories['CurrentInventory']['batch_number'];

															$data['DistChallanDetail']['challan_id'] = $this->DistChallan->id;
															$data['DistChallanDetail']['product_id'] = $val;
															$data['DistChallanDetail']['measurement_unit_id'] = $product_list['measurement_unit_id'][$key];
															$data['DistChallanDetail']['challan_qty'] = $product_list['sales_qty'][$key];
															$data['DistChallanDetail']['received_qty'] = $product_list['deliverd_qty'][$key];
															$data['DistChallanDetail']['batch_no'] = $batch_no;
															//$data['DistChallanDetail']['remaining_qty'] =$product_list['remaining_qty'][$key];
															$data['DistChallanDetail']['price'] = $product_list['Price'][$key];
															/* if(!empty($product_list['bonus_product_id'][$key])){
													$data['DistChallanDetail']['is_bonus'] = 1;
													}else{*/
															$data['DistChallanDetail']['is_bonus'] = 0;
															//}

															$data['DistChallanDetail']['source'] = "";
															$data['DistChallanDetail']['remarks'] = "";

															//$data['ChallanDetail']['expire_date'] = (($this->request->data['expire_date'][$key] != '' ) ? Date('Y-m-d', strtotime($this->request->data['expire_date'][$key])) : Null);
															$date = (($this->request->data['OrderProces']['order_date'][$key] != ' ' && $this->request->data['OrderProces']['order_date'][$key] != 'null' && $this->request->data['OrderProces']['order_date'][$key] != '') ? explode('-', $this->request->data['OrderProces']['order_date'][$key]) : '');
															if (!empty($date[1])) {
																$date[0] = date('m', strtotime($date[0]));
																$a_date = date('y-m-d', mktime(0, 0, 0, $date[0], 1, $date[1]));
																$data['DistChallanDetail']['expire_date'] = date("Y-m-t", strtotime($a_date));
															} else {
																$data['DistChallanDetail']['expire_date'] = '';
															}
															$data['DistChallanDetail']['inventory_status_id'] = 1;  // set 1 for Sound product
															//$data['ChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
														}
														$data_array[] = $data;
														if ($product_list['bonus_product_id'][$key] != 0 || !empty($product_list['bonus_product_id'][$key])) {

															$inventories = $this->CurrentInventory->find('first', array(
																'conditions' => array(
																	'CurrentInventory.product_id' => $product_list['bonus_product_id'][$key],
																	'CurrentInventory.store_id' => $store_id,
																),
																'recursive' => -1,
															));
															$batch_no = $inventories['CurrentInventory']['batch_number'];
															$bonus_data['DistChallanDetail']['batch_no'] = $batch_no;
															$bonus_data['DistChallanDetail']['challan_id'] = $this->DistChallan->id;
															$bonus_data['DistChallanDetail']['product_id'] = $product_list['bonus_product_id'][$key];
															$bonus_data['DistChallanDetail']['measurement_unit_id'] = $product_list['bonus_measurement_unit_id'][$key];
															$bonus_data['DistChallanDetail']['challan_qty'] = $product_list['bonus_product_qty'][$key];
															$bonus_data['DistChallanDetail']['received_qty'] = $product_list['bonus_product_qty'][$key];
															$bonus_data['DistChallanDetail']['price'] = 0;
															$bonus_data['DistChallanDetail']['is_bonus'] = 1;

															$data_array[] = $bonus_data;
														}
													}
												} else {
													if (!empty($product_list['sales_qty'][$key])) {
														$inventories = $this->CurrentInventory->find('first', array(
															'conditions' => array(
																'CurrentInventory.product_id' => $val,
																'CurrentInventory.store_id' => $store_id,
															),
															'recursive' => -1,
														));
														$batch_no = $inventories['CurrentInventory']['batch_number'];
														$data['DistChallanDetail']['challan_id'] = $this->DistChallan->id;
														$data['DistChallanDetail']['product_id'] = $val;
														$data['DistChallanDetail']['challan_qty'] = $product_list['sales_qty'][$key];
														$data['DistChallanDetail']['received_qty'] = $product_list['sales_qty'][$key];
														$data['DistChallanDetail']['batch_no'] = $batch_no;

														$data['DistChallanDetail']['price'] = $product_list['Price'][$key];
														$data['DistChallanDetail']['is_bonus'] = 1;

														$data_array[] = $data;
													}
												}
											}
										}
										//pr($data_array);die();
										$this->DistChallanDetail->saveAll($data_array);
									}
								}
							}
						}
						/************* end Challan *************/
					}


					//start collection crate
					$collection_data = array();
					$collection_data['memo_id'] = $memo_info_arr['Memo']['id'];
					$collection_data['memo_no'] = $memo_info_arr['Memo']['memo_no'];
					$collection_data['so_id'] = $memo_info_arr['Memo']['sales_person_id'];

					$collection_data['memo_value'] = $memo_info_arr['Memo']['gross_value'];
					$collection_data['credit_or_due'] = $memo_info_arr['Memo']['credit_amount'];
					$collection_data['memo_date'] = $memo_info_arr['Memo']['memo_date'];
					$collection_data['type'] = 1;
					$collection_data['instrument_type'] = $order_info['Order']['instrument_type'];
					$collection_data['collectionAmount'] = $memo_info_arr['Memo']['gross_value'];
					$collection_data['collectionDate'] = date('Y-m-d');
					$collection_data['created_at'] = $this->current_datetime();

					$collection_data['territory_id'] = $memo_info_arr['Memo']['territory_id'];
					$collection_data['outlet_id'] = $memo_info_arr['Memo']['outlet_id'];
					$collection_data['market_id'] = $memo_info_arr['Memo']['market_id'];
					$collection_data['office_id'] = $memo_info_arr['Memo']['office_id'];


					$this->Collection->save($collection_data);

					//end collection careate    

				}

				/*******************************End Memo********************************************/
				/******************* Memo Create end date: 04-09-2019************************/
			}

			// pr($credit_amount);pr($balance);die();
			/*if($maintain_dealer_type==1)
		{
			if($dealer_is_limit_check==1)
			{
				if($credit_amount > $balance)
				{
					$this->Session->setFlash(__('Please Check the Credit Amount!!!  The Order has been Updated'), 'flash/warning');      
				}
				else
				{
					$this->Session->setFlash(__($message), 'flash/success');
				}
			}
		}*/

			//$this->Session->setFlash(__('The Order has been Updated'), 'flash/success');
			if (array_key_exists('save', $this->request->data)) {
				//$this->redirect(array('action' => 'delivery',$id));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->redirect(array('action' => 'edit', $id));
			}
		}



		$this->loadModel('ProductMeasurement');
		$product_measurement_units = $this->ProductMeasurement->find('list', array('fields' => array('product_id', 'measurement_unit_id')));
		$product_category_id_list = $this->Product->find('list', array('fields' => array('id', 'product_category_id')));


		$this->set(compact('offices', 'territories', 'markets', 'outlets', 'current_date', 'product_list', 'sale_type_list', 'existing_record', 'office_parent_id', 'product_measurement_units', 'product_category_id_list', 'open_bonus_product_option'));
	}
	/******end work at ****/
	public function get_outlet_list()
	{
		$rs = array(array('id' => '', 'name' => '---- Select outlet -----'));
		$market_id = $this->request->data['office_id'];
		$outlet_list = $this->DistDistributor->find('all', array(
			'conditions' => array('Outlet.office_id' => $market_id)
		));
		$data_array = Set::extract($outlet_list, '{n}.Outlet');

		if (!empty($data_array)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}
	public function company_confirmation_type()
	{
		$company_id = $this->request->data['company_id'];
		$this->loadModel('Company');
		$comfirmation_type = $this->Company->find('first', array(
			'conditions' => array(
				'Company.id' => $company_id,
			)
		));
		$data_array['confirmation_type'] = $comfirmation_type['Company']['confirmation_type'];

		if (!empty($data_array)) {
			echo json_encode($data_array);
		}

		$this->autoRender = false;
	}
	public function get_product_unit()
	{
		$current_date = $this->current_date();
		$territory_id = $this->request->data['territory_id'];
		$outlet_id = $this->request->data['outlet_id'];
		$this->loadModel('Territory');
		$office_info = $this->Territory->find('first', array(
			'fields' => array('Territory.office_id'),
			'conditions' => array('Territory.id' => $territory_id),
			'recursive' => -1
		));

		$this->loadModel('Store');
		$this->Store->recursive = -1;
		$store_info = $this->Store->find('first', array(
			'conditions' => array(
				'office_id' => $office_info['Territory']['office_id'],
				'store_type_id' => 2
			)
		));
		$store_id = $store_info['Store']['id'];
		$product_id = $this->request->data['product_id'];

		$this->loadModel('DistOutletMap');
		$outlet_info = $this->DistOutletMap->find('first', array(
			'conditions' => array('DistOutletMap.outlet_id' => $outlet_id),
		));
		$distributor_id = $outlet_info['DistDistributor']['id'];


		$this->loadModel('DistStore');
		$dist_store_info = $this->DistStore->find('first', array(
			'conditions' => array('DistStore.dist_distributor_id' => $distributor_id, 'DistStore.office_id' => $office_info['Territory']['office_id']),
		));
		$dist_store_id = $dist_store_info['DistStore']['id'];

		//----------------product expire last date-----------\\
		$this->loadModel('ProductMonth');

		$product_expire_month_info = $this->ProductMonth->find('first', array(
			'conditions' => array(
				'ProductMonth.product_id' => $product_id
			),
			'fields' => array('ProductMonth.day_month'),
			'recursive' => -1
		));
		if (empty($product_expire_month_info)) {
			$productExpireLimit = 0;
		} else {
			$productExpireLimit = $product_expire_month_info['ProductMonth']['day_month'];
		}

		$p_expire_date = date('Y-m-t', strtotime("+" . $productExpireLimit . " months"));

		//--------------end-------------\\

		$this->loadModel('CurrentInventory');
		$this->CurrentInventory->recursive = -1;
		$total_qty_arr = $this->CurrentInventory->find('all', array(
			'conditions' => array(
				'store_id' => $store_id,
				'product_id' => $product_id,
				//"(expire_date is null OR expire_date > '$p_expire_date' )",
			),
			'fields' => array('sum(qty) as total')
		));
		$total_qty = $total_qty_arr[0][0]['total'];


		$this->loadModel('DistCurrentInventory');
		$dist_inventory_info = $this->DistCurrentInventory->find('all', array(
			'conditions' => array('DistCurrentInventory.store_id' => $dist_store_id, 'DistCurrentInventory.product_id' => $product_id),
			'fields' => array('sum(qty) as total_dist_qty')
		));
		$this->loadModel('Product');
		$this->Product->recursive = -1;
		$product_array = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $product_id),
			'joins' => array(
				array(
					'table' => 'measurement_units',
					'alias' => 'MU',
					'conditions' => 'MU.id=Product.sales_measurement_unit_id'
				)
			),
			'fields' => array('sales_measurement_unit_id', 'MU.name')
		));
		$measurement_unit_id = $product_array['Product']['sales_measurement_unit_id'];
		$measurement_unit_name = $product_array['MU']['name'];

		$total_dist_qty = $dist_inventory_info[0][0]['total_dist_qty'];
		$sales_total_qty = $this->unit_convertfrombase($this->request->data['product_id'], $measurement_unit_id, $total_qty);
		$sales_total_dist_qty = $this->unit_convertfrombase($this->request->data['product_id'], $measurement_unit_id, $total_dist_qty);

		$data_array['product_unit']['name'] = $measurement_unit_name;
		$data_array['product_unit']['id'] = $measurement_unit_id;

		if (!empty($sales_total_dist_qty)) {
			$data_array['total_dist_qty'] = $sales_total_dist_qty;
		} else {
			$data_array['total_dist_qty'] = '';
		}
		if (!empty($sales_total_qty)) {
			$data_array['total_qty'] = $sales_total_qty;
		} else {
			$data_array['total_qty'] = '';
		}
		echo json_encode($data_array);
		$this->autoRender = false;
	}

	/* ------- set_combind_or_individual_price --------- */

	public function get_combine_or_individual_price()
	{
		//pr($this->request->data);//die();

		$product_items_id = explode(',', rtrim($this->request->data['product_id_list'], ","));

		/* ---- read session data ----- */
		$cart_data = $this->Session->read('cart_session_data');
		$matched_data = $this->Session->read('matched_session_data');
		//pr($matched_data);pr($cart_data);die();
		/*echo "<pre>";
		  echo "Cart data ----------------";
		  print_r($cart_data);
		  echo "Cart data ----------------";
		  print_r($matched_data); exit;*/


		/* ---- read session data ----- */
		$combined_product = $this->request->data['combined_product'];
		$min_qty = $this->request->data['min_qty'];
		$product_id = $this->request->data['product_id'];
		/*$this->loadModel('Product');
		  $products_info = $this->Product->find('first',array('conditions'=>array('Product.id'=>$product_id),'recursive'=> -1));
		  
		  $measurement_unit_id = $products_info['Product']['sales_measurement_unit_id'];
		  $this->loadModel('ProductMeasurement');
		  $sales_measurements = $this->ProductMeasurement->find('first',array(
			'conditions'=> array('ProductMeasurement.product_id' => $product_id,
				'ProductMeasurement.measurement_unit_id' => 16
				),
			));
		  //pr($sales_measurements);die();
		  $measurement_qty = $sales_measurements['ProductMeasurement']['qty_in_base'];
		  $calculate_measurement = $min_qty / $measurement_qty;*/

		// $result_data = array();
		// $result_data['remarks'] = $calculate_measurement." CARTON";
		//pr($combined_product);pr($min_qty);pr($product_id);die();
		/*---------Bonus-----------*/
		/*$this->loadModel('Bonus');
		$this->Bonus->recursive = -1;
		$bonus_info = $this->Bonus->find('first',array(
			'conditions'=>array('mother_product_id'=>$product_id, 'mother_product_quantity'=>$min_qty),
			'fields'=>array('mother_product_quantity', 'bonus_product_id', 'bonus_product_quantity')
		));

		if (count($bonus_info) != 0) {
			$bonus_product_id = $bonus_info['Bonus']['bonus_product_id'];
			$this->loadModel('Product');
			$this->Product->recursive = -1;
			$bonus_product_name = $this->Product->find('first',array(
				'conditions'=>array('id'=>$bonus_product_id),
				'fields'=>array('name', 'sales_measurement_unit_id')
			));
			$result_data['mother_product_quantity'] = $bonus_info['Bonus']['mother_product_quantity'];
			$result_data['bonus_product_id'] = $bonus_product_id;
			$result_data['bonus_product_name'] = $bonus_product_name['Product']['name'];
			$result_data['bonus_measurement_unit_id'] = $bonus_product_name['Product']['sales_measurement_unit_id'];
			$result_data['bonus_product_qty'] = $bonus_info['Bonus']['bonus_product_quantity'];
		}*/

		/*---------Bonus-----------*/

		$this->loadModel('Bonus');
		$this->loadModel('Product');

		$this->Bonus->recursive = -1;
		$bonus_info = $this->Bonus->find('all', array(
			'conditions' => array(
				'mother_product_id' => $product_id,
				'effective_date <=' => date('Y-m-d'),
				'end_date >=' => date('Y-m-d'),
			),
			'fields' => array('mother_product_quantity', 'bonus_product_id', 'bonus_product_quantity')
		));

		if (!empty($bonus_info[0]['Bonus']['mother_product_quantity'])) {
			$mother_product_quantity_bonus = $bonus_info[0]['Bonus']['mother_product_quantity'];
			$result_data['mother_product_quantity_bonus'] = $mother_product_quantity_bonus;
		}

		$no_of_bonus_slap = count($bonus_info);


		if ($no_of_bonus_slap != 0) {
			for ($slap_count = 0; $slap_count < $no_of_bonus_slap; $slap_count++) {
				$bonus_slap['bonus_product_id'][$slap_count] = $bonus_info[$slap_count]['Bonus']['bonus_product_id'];

				$this->Product->recursive = -1;
				$bonus_product_name = $this->Product->find('first', array(
					'conditions' => array('id' => $bonus_slap['bonus_product_id'][$slap_count]),
					'fields' => array('name', 'sales_measurement_unit_id')
				));
				$quantity_slap['mother_product_quantity'][$slap_count] = $bonus_info[$slap_count]['Bonus']['mother_product_quantity'];

				$result_data['bonus_product_id'][$slap_count] = $bonus_info[$slap_count]['Bonus']['bonus_product_id'];
				$result_data['bonus_product_name'][$slap_count] = $bonus_product_name['Product']['name'];
				$result_data['sales_measurement_unit_id'][$slap_count] = $bonus_product_name['Product']['sales_measurement_unit_id'];
				$result_data['bonus_product_quantity'][$slap_count] = $bonus_info[$slap_count]['Bonus']['bonus_product_quantity'];
			}
			for ($i = 0; $i < count($quantity_slap['mother_product_quantity']); $i++) {
				$result_data['mother_product_quantity'][] = array(
					'min' => $i == 0 ? 0 : $quantity_slap['mother_product_quantity'][$i - 1],
					'max' => $quantity_slap['mother_product_quantity'][$i] - 1
				);
			}
		}

		global $qty_data;
		$qty_data = array();

		function build_inputed_qty_data($product_id = null, $min_qty = null, &$qty_data = array())
		{
			if (!array_key_exists($product_id, $qty_data)) {
				$qty_data[$product_id] = $min_qty;
			} else {
				$qty_data[$product_id] = $min_qty;
			}
		}

		if (!empty($combined_product)) {
			if ($this->Session->read('combintaion_qty_data') == NULL) {
				$qty_data = array();
			} else {
				$qty_data = $this->Session->read('combintaion_qty_data');
				/*echo '<pre>';
				pr($qty_data);exit;*/
			}
			build_inputed_qty_data($product_id, $min_qty, $qty_data);
			$this->Session->write('combintaion_qty_data', $qty_data);
		}
		//pr($qty_data);
		/* echo "<br/>";
		  echo "Cart data ----------------";
		  print_r($matched_data);
		  exit; */
		$current_qty = $this->Session->read('combintaion_qty_data');
		/* echo "<br/>";
		  echo "Cart data ----------------";
		  print_r($current_qty); */
		if (!empty($current_qty)) {
			$prev_data = array();
			foreach ($current_qty as $q_key => $q_val) {
				$prev_data[] = $q_key;
			}
			$diff_product_id = array_diff($prev_data, $product_items_id);
			if (!empty($diff_product_id)) {
				foreach ($diff_product_id as $key => $val) {
					unset($current_qty[$val]);
				}
				$this->Session->write('combintaion_qty_data', $current_qty);
			}
		}
		//pr($combined_product);
		//pr($matched_data);die();
		if ($combined_product) {
			foreach ($matched_data as $combined_product_key => $combined_product_val) {
				if ($combined_product_key == $combined_product) {
					/*if ($combined_product_val['is_matched_yet'] == 'NO') {
						foreach ($cart_data as $no_com_key => $no_com_val) {
							if ($no_com_key == $product_id) {
								$less_qty_array = array();
								foreach ($no_com_val['individual_slab'] as $in_slab_qty => $in_slab_val) {
									if ($min_qty >= $in_slab_qty) {
										$less_qty_array[$in_slab_qty] = $in_slab_val;
									}
								}
								ksort($less_qty_array);
								$unit_rate = array_pop($less_qty_array);

								if (empty($unit_rate)) {
									$unit_rate = $no_com_val['product_price']['general_price'];
								}
								if ($unit_rate) {
									$result_data ['unit_rate'] = sprintf("%1\$.6f", $unit_rate);
									$result_data ['total_value'] = sprintf("%1\$.6f", $unit_rate) * $min_qty;
								} else {
									$result_data ['unit_rate'] = '';
									$result_data ['total_value'] = '';
								}
							}
						}
					}*/
					if ($combined_product_val['is_matched_yet'] == 'NO' || $combined_product_val['is_matched_yet'] == 'YES') {
						$current_qty = $this->Session->read('combintaion_qty_data');
						/*echo "<pre>";
						  print_r($current_qty);
						  print_r($cart_data);
						  exit;*/
						foreach ($cart_data as $no_com_key => $no_com_val) {
							$combined_product = explode(',', $no_com_val['combined_product']);
							$combined_inputed_val = 0;
							foreach ($combined_product as $qty_key => $qty_val) {
								/* if($qty_val == $no_com_key){
								  $combined_inputed_val = $combined_inputed_val + $current_qty[$qty_val];
							  } */
								if (array_key_exists($qty_val, $current_qty)) {
									$combined_inputed_val = $combined_inputed_val + $current_qty[$qty_val];
								}
							}
							if ($no_com_key == $product_id) {
								//echo $no_com_key." == ".$product_id."<br/>";
								$less_qty_data = array();
								foreach ($no_com_val['combined_slab'] as $in_slab_qty => $in_slab_val) {
									if ($combined_inputed_val >= $in_slab_qty) {

										$less_qty_data[$in_slab_qty] = $in_slab_val;
									}
								}
								ksort($less_qty_data);
								$actual_data = array_pop($less_qty_data);
								$combined_less_qty = array();
								if (is_array($actual_data) && is_array($current_qty)) {
									$combined_common_product = array_intersect_key($actual_data, $current_qty);
								}
								if (!empty($actual_data[$product_id]) && count($combined_common_product) > 1) {
									foreach ($actual_data as $ac_key => $ac_val) {
										$combined_less_qty[$ac_key]['unit_rate'] = sprintf("%1\$.6f", $ac_val);
										if (!empty($current_qty[$ac_key])) {
											$combined_less_qty[$ac_key]['total_value'] = $current_qty[$ac_key] * sprintf("%1\$.6f", $ac_val);
										}
									}
								} else {
									/* --------------------------------- */
									/* =============================================== */
									$individual_less_qty = array();
									foreach ($combined_product as $combined_key => $combined_val) {
										if (array_key_exists($combined_val, $cart_data)) {
											$individual_less_qty_unique = array();
											foreach ($cart_data[$combined_val]['individual_slab'] as $in_slab_qty => $in_slab_val) {
												if ($current_qty[$combined_val] >= $in_slab_qty) {
													$individual_less_qty_unique[$in_slab_qty] = $in_slab_val;
												}
											}
											ksort($individual_less_qty_unique);
											$individual_actual_data = array_pop($individual_less_qty_unique);
											if (empty($individual_actual_data)) {
												$individual_actual_data = $cart_data[$combined_val]['product_price']['general_price'];
												/* 	echo "<pre>";
												  echo "dsfsdf";
												  print_r($individual_actual_data);
												  print_r($cart_data[$combined_val]); */
											}
											$individual_less_qty[$combined_val]['unit_rate'] = sprintf("%1\$.6f", $individual_actual_data);
											$individual_less_qty[$combined_val]['total_value'] = sprintf("%1\$.6f", $individual_actual_data) * $current_qty[$combined_val];
										}
									}
									//exit;
									/* --------------------------------- */
								}
							}
						}
					}
				}
			}
		} else {
			foreach ($cart_data as $no_com_key => $no_com_val) {
				if ($product_id == $no_com_key) {
					$less_qty_val = array();
					foreach ($no_com_val['individual_slab'] as $in_slab_key => $in_slab_val) {
						if ($min_qty >= $in_slab_key) {
							$less_qty_val[$in_slab_key] = $in_slab_val;
						}
					}
					ksort($less_qty_val);
					$unit_rate = array_pop($less_qty_val);
					if (empty($unit_rate)) {
						$unit_rate = $no_com_val['product_price']['general_price'];
					}
					if ($unit_rate) {
						$result_data['unit_rate'] = sprintf("%1\$.6f", $unit_rate);
						$result_data['total_value'] = $unit_rate * sprintf("%1\$.6f", $min_qty);
					} else {
						$result_data['unit_rate'] = '';
						$result_data['total_value'] = '';
					}
				}
			}
		}

		//pr($result_data);die();
		if (!empty($result_data)) {
			echo json_encode($result_data);
		} elseif (!empty($individual_less_qty)) {
			echo json_encode($individual_less_qty);
		} elseif (!empty($combined_less_qty)) {
			echo json_encode($combined_less_qty);
		}
		$this->autoRender = false;
	}


	public function delete_order()
	{
		/* -------- Start Session data --------- */
		$cart_data = $this->Session->read('cart_session_data');
		$matched_data = $this->Session->read('matched_session_data');
		$current_qty = $this->Session->read('combintaion_qty_data');
		/* -------- End Session data --------- */
		$product_id = $this->request->data['product_id'];
		$combined_product = $this->request->data['combined_product'];
		if (!empty($product_id)) {
			unset($cart_data[$product_id]);
			$this->Session->write('cart_session_data', $cart_data);

			if (!empty($combined_product)) {
				if ($matched_data[$combined_product]['matched_count_so_far'] == 1) {
					unset($matched_data[$combined_product]);
					$this->Session->write('matched_session_data', $matched_data);
				} else {
					$matched_id_so_far = rtrim($matched_data[$combined_product]['matched_id_so_far'], ',' . $product_id);
					$matched_count_so_far = $matched_data[$combined_product]['matched_count_so_far'] - 1;
					$matched_data[$combined_product]['is_matched_yet'] = 'NO';
					$matched_data[$combined_product]['matched_count_so_far'] = $matched_count_so_far;
					$matched_data[$combined_product]['matched_id_so_far'] = $matched_id_so_far;
					$this->Session->write('matched_session_data', $matched_data);
				}
			}
			if (!empty($current_qty)) {
				unset($current_qty[$product_id]);
				$this->Session->write('combintaion_qty_data', $current_qty);
			}
			echo 'yes';
		}

		$this->autoRender = false;
	}


	public function get_territory_id()
	{
		$sales_person_id = $this->request->data['sales_person_id'];
		//$sales_person_id = 2;
		$this->SalesPerson->recursive = 0;
		$territory_id = $this->SalesPerson->find('all', array(
			'conditions' => array('SalesPerson.id' => $sales_person_id),
			'fields' => array('SalesPerson.territory_id')
		));

		if ($territory_id) {
			$response['territory_id'] = $territory_id[0]['SalesPerson']['territory_id'];
		} else {
			$response['territory_id'] = '';
		}

		if ($response) {
			echo json_encode($response);
		}
		$this->autoRender = false;
	}

	public function admin_order_map()
	{

		$this->set('page_title', 'Order Manage List on Map');
		$message = '';
		$map_data = array();
		$conditions = array();
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$conditions[] = array();
			$office_conditions = array();
		} else {
			$conditions[] = array('Territory.office_id' => $this->UserAuth->getOfficeId());
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}

		// Custome Search
		if (($this->request->is('post') || $this->request->is('put'))) {

			if ($this->request->data['Order']['office_id'] != '') {
				$conditions[] = array('Territory.office_id' => $this->request->data['Order']['office_id']);
			}
			if ($this->request->data['Order']['territory_id'] != '') {
				$conditions[] = array('Order.territory_id' => $this->request->data['Order']['territory_id']);
			}
			if ($this->request->data['Order']['date_from'] != '') {
				$conditions[] = array('Order.order_date >=' => Date('Y-m-d', strtotime($this->request->data['Order']['date_from'])));
			}
			if ($this->request->data['Order']['date_to'] != '') {
				$conditions[] = array('Order.order_date <=' => Date('Y-m-d', strtotime($this->request->data['Order']['date_to'])));
			}

			$this->Order->recursive = 0;
			$order_list = $this->Order->find('all', array(
				'conditions' => $conditions,
				'order' => array('Order.id' => 'desc'),
				'recursive' => 0
			));

			if (!empty($order_list)) {
				foreach ($order_list as $val) {
					if ($val['Order']['latitude'] > 0 and $val['Order']['longitude'] > 0) {
						$data['title'] = $val['Outlet']['name'];
						$data['lng'] = $val['Order']['longitude'];
						$data['lat'] = $val['Order']['latitude'];
						$data['description'] = '<p><b>Outlet : ' . $val['Outlet']['name'] . '</b></br>' .
							'Market : </b>' . $val['Market']['name'] . '</br>' .
							'Territory : </b>' . $val['Territory']['name'] . '</p>' .
							'<p>Order No. : ' . $val['Order']['order_no'] . '</br>' .
							'Order Date : ' . date('d-M-Y', strtotime($val['Order']['order_date'])) . '</br>' .
							'Order Amount : ' . sprintf('%.2f', $val['Order']['gross_value']) . '</p>' .
							'<a class="btn btn-primary btn-xs" href="' . BASE_URL . 'admin/orders/view/' . $val['Order']['id'] . '" target="_blank">Order Details</a>';
						$map_data[] = $data;
					}
				}
			}
			if (!empty($map_data))
				$message = '';
			else
				$message = '<div class="alert alert-danger">No order found.</div>';
		}

		$this->loadModel('Office');
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		$office_id = (isset($this->request->data['Order']['office_id']) ? $this->request->data['Order']['office_id'] : 0);
		$territories = $this->Order->Territory->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));
		$this->set(compact('offices', 'territories', 'map_data', 'message'));
	}

	public function update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct', $transaction_type_id = 0, $transaction_date = '')
	{

		$this->loadModel('CurrentInventory');

		$find_type = 'all';
		if ($update_type == 'add')
			$find_type = 'first';

		/* pr($quantity);
		pr($product_id);
		pr($store_id);
		pr($update_type);
		pr($transaction_type_id);
		pr($transaction_date);*/

		//-------prodcut expire limit---------\\

		$this->loadModel('ProductMonth');

		$product_expire_month_info = $this->ProductMonth->find('first', array(
			'conditions' => array(
				'ProductMonth.product_id' => $product_id
			),
			'fields' => array('ProductMonth.day_month'),
			'recursive' => -1
		));
		if (empty($product_expire_month_info)) {
			$productExpireLimit = 0;
		} else {
			$productExpireLimit = $product_expire_month_info['ProductMonth']['day_month'];
		}

		$p_expire_date = date('Y-m-t', strtotime("+" . $productExpireLimit . " months"));

		//--------------end---------------\\

		$inventory_info = $this->CurrentInventory->find($find_type, array(
			'conditions' => array(
				//'CurrentInventory.qty >=' => 0,
				'CurrentInventory.store_id' => $store_id,
				'CurrentInventory.inventory_status_id' => 1,
				'CurrentInventory.product_id' => $product_id,
				//"(CurrentInventory.expire_date is null OR CurrentInventory.expire_date > '$p_expire_date' )",
			),
			'order' => array('CurrentInventory.expire_date' => 'asc'),
			'recursive' => -1
		));


		//echo '<pre>';print_r($inventory_info);die();

		if ($update_type == 'deduct') {
			foreach ($inventory_info as $val) {
				if ($quantity <= $val['CurrentInventory']['qty']) {
					$this->CurrentInventory->id = $val['CurrentInventory']['id'];
					if (!$this->CurrentInventory->updateAll(
						array(
							'CurrentInventory.qty' => 'CurrentInventory.qty - ' . $quantity,
							'CurrentInventory.transaction_type_id' => $transaction_type_id,
							'CurrentInventory.transaction_date' => "'" . $transaction_date . "'",
							'CurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
						),
						array('CurrentInventory.id' => $val['CurrentInventory']['id'])
					)) {
						return false;
					}
					break;
				} else {

					if ($val['CurrentInventory']['qty'] > 0) {
						$quantity = $quantity - $val['CurrentInventory']['qty'];
						$this->CurrentInventory->id = $val['CurrentInventory']['id'];
						if (!$this->CurrentInventory->updateAll(
							array(
								'CurrentInventory.qty' => 'CurrentInventory.qty - ' . $val['CurrentInventory']['qty'],
								'CurrentInventory.transaction_type_id' => $transaction_type_id,
								'CurrentInventory.transaction_date' => "'" . $transaction_date . "'",
								'CurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
							),
							array('CurrentInventory.id' => $val['CurrentInventory']['id'])
						)) {
							return false;
						}
					}
				}
			}
		} else {
			/* $this->CurrentInventory->updateAll(array('CurrentInventory.qty' => 'CurrentInventory.qty + '.$inventory_info['CurrentInventory']['qty']),array('CurrentInventory.id' => $inventory_info['CurrentInventory']['id'])); */
			if (!empty($inventory_info)) {

				/*$this->CurrentInventory->updateAll(
						array('CurrentInventory.qty' => 'CurrentInventory.qty + ' . $quantity, 'CurrentInventory.transaction_type_id' => $transaction_type_id, 'CurrentInventory.store_id' => $store_id, 'CurrentInventory.transaction_date' => "'" . $transaction_date . "'"), array('CurrentInventory.id' => $inventory_info['CurrentInventory']['id'])
				);*/
			}
		}

		return true;
	}

	public function new_update_current_inventory($current_inventory_id, $quantity)
	{

	   

		$this->loadModel('CurrentInventory');

		//-------prodcut expire limit---------\\
		/* 
		$this->loadModel('ProductMonth');

		$product_expire_month_info = $this->ProductMonth->find('first', array(
			'conditions' => array(
				'ProductMonth.product_id' => $product_id
			),
			'fields' => array('ProductMonth.day_month'),
			'recursive' => -1
		));
		if (empty($product_expire_month_info)) {
			$productExpireLimit = 0;
		} else {
			$productExpireLimit = $product_expire_month_info['ProductMonth']['day_month'];
		}

		$p_expire_date = date('Y-m-t', strtotime("+" . $productExpireLimit . " months")); */

		//--------------end---------------\\

		$inventory_info = $this->CurrentInventory->find('first', array(
			'conditions' => array(
				'CurrentInventory.id' => $current_inventory_id,
				'CurrentInventory.inventory_status_id' => 1,
			),
			'recursive' => -1
		));
	   
		$transaction_type_id = 11;
		$transaction_date = date('Y-m-d');
		   
		if ($quantity <= $inventory_info['CurrentInventory']['qty']) {
			$this->CurrentInventory->id = $inventory_info['CurrentInventory']['id'];
			if (!$this->CurrentInventory->updateAll(
				array(
					'CurrentInventory.qty' => 'CurrentInventory.qty - ' . $quantity,
					'CurrentInventory.transaction_type_id' => $transaction_type_id,
					'CurrentInventory.transaction_date' => "'" . $transaction_date . "'",
					'CurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
				),
				array('CurrentInventory.id' => $inventory_info['CurrentInventory']['id'])
			)) {
				return false;
			}
		}else{
			return false;
		}
			
		return true;

	}


	// it will be called from order not from order_details 
	// cal_type=1 means increment and 2 means deduction 

	public function ec_calculation($gross_value, $outlet_id, $terrority_id, $order_date, $cal_type)
	{
		// from so_id , retrieve aso_id,terrority_id :  no need as we have territory_id
		// check gross_value >0

		if ($gross_value > 0) {
			$this->loadModel('Outlet');
			// from outlet_id, retrieve pharma or non-pharma
			$outlet_info = $this->Outlet->find('first', array(
				'conditions' => array(
					'Outlet.id' => $outlet_id
				),
				'recursive' => -1
			));

			if (!empty($outlet_info)) {
				$is_pharma_type = $outlet_info['Outlet']['is_pharma_type'];
				// from order_date , split month and get month name and compare month table with order year
				$orderDate = strtotime($order_date);
				$month = date("n", $orderDate);
				$year = date("Y", $orderDate);
				$this->loadModel('Month');

				// from outlet_id, retrieve pharma or non-pharma
				$fasical_info = $this->Month->find('first', array(
					'conditions' => array(
						'Month.month' => $month,
						'Month.year' => $year
					),
					'recursive' => -1
				));


				if (!empty($fasical_info)) {
					$this->loadModel('SaleTargetMonth');
					if ($cal_type == 1) {
						if ($is_pharma_type == 1) {
							$update_fields_arr = array('SaleTargetMonth.effective_call_pharma_achievement' => "SaleTargetMonth.effective_call_pharma_achievement+1");
						} else if ($is_pharma_type == 0) {
							$update_fields_arr = array('SaleTargetMonth.effective_call_non_pharma_achievement' => "SaleTargetMonth.effective_call_non_pharma_achievement+1");
						}
					} else {
						if ($is_pharma_type == 1) {
							$update_fields_arr = array('SaleTargetMonth.effective_call_pharma_achievement' => "SaleTargetMonth.effective_call_pharma_achievement-1");
						} else if ($is_pharma_type == 0) {
							$update_fields_arr = array('SaleTargetMonth.effective_call_non_pharma_achievement' => "SaleTargetMonth.effective_call_non_pharma_achievement-1");
						}
					}

					$conditions_arr = array('SaleTargetMonth.product_id' => 0, 'SaleTargetMonth.territory_id' => $terrority_id, 'SaleTargetMonth.fiscal_year_id' => $fasical_info['Month']['fiscal_year_id'], 'SaleTargetMonth.month_id' => $fasical_info['Month']['id']);

					$this->SaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
				}
			}
		}
	}

	// cal_type=1 means increment and 2 means deduction 
	// it will be called from  order_details 
	public function sales_calculation($product_id, $terrority_id, $quantity, $gross_value, $order_date, $cal_type)
	{
		// from so_id , retrieve aso_id,terrority_id : no need as we have territory_id
		// from order_date , split month and get month name and compare month table with order year
		$orderDate = strtotime($order_date);
		$month = date("n", $orderDate);
		$year = date("Y", $orderDate);
		$this->loadModel('Month');
		// from outlet_id, retrieve pharma or non-pharma
		$fasical_info = $this->Month->find('first', array(
			'conditions' => array(
				'Month.month' => $month,
				'Month.year' => $year
			),
			'recursive' => -1
		));

		if (!empty($fasical_info)) {
			$this->loadModel('SaleTargetMonth');
			if ($cal_type == 1) {
				$update_fields_arr = array('SaleTargetMonth.target_quantity_achievement' => "SaleTargetMonth.target_quantity_achievement+$quantity", 'SaleTargetMonth.target_amount_achievement' => "SaleTargetMonth.target_amount_achievement+$gross_value");
			} else {
				$update_fields_arr = array('SaleTargetMonth.target_quantity_achievement' => "SaleTargetMonth.target_quantity_achievement-$quantity", 'SaleTargetMonth.target_amount_achievement' => "SaleTargetMonth.target_amount_achievement-$gross_value");
			}

			$conditions_arr = array('SaleTargetMonth.product_id' => $product_id, 'SaleTargetMonth.territory_id' => $terrority_id, 'SaleTargetMonth.fiscal_year_id' => $fasical_info['Month']['fiscal_year_id'], 'SaleTargetMonth.month_id' => $fasical_info['Month']['id']);
			$this->SaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
		}
	}

	// cal_type=1 means increment and 2 means deduction 
	// it will be called from order not from order_details 
	public function oc_calculation($terrority_id, $gross_value, $outlet_id, $order_date, $order_time, $cal_type)
	{

		// from so_id , retrieve aso_id,terrority_id :  no need as we have territory_id
		// check gross_value >0
		if ($gross_value > 0) {
			$this->loadModel('Order');
			// this will be updated monthly , if done then increment else no action
			$month_first_date = date('Y-m-01', strtotime($order_date));
			$count = $this->Order->find('count', array(
				'conditions' => array(
					'Order.outlet_id' => $outlet_id,
					'Order.order_date >= ' => $month_first_date,
					'Order.order_time < ' => $order_time
				)
			));

			if ($count == 0) {

				$this->loadModel('Outlet');
				// from outlet_id, retrieve pharma or non-pharma
				$outlet_info = $this->Outlet->find('first', array(
					'conditions' => array(
						'Outlet.id' => $outlet_id
					),
					'recursive' => -1
				));

				if (!empty($outlet_info)) {
					$is_pharma_type = $outlet_info['Outlet']['is_pharma_type'];
					// from order_date , split month and get month name and compare month table with order year
					$orderDate = strtotime($order_date);
					$month = date("n", $orderDate);
					$year = date("Y", $orderDate);
					$this->loadModel('Month');
					// from outlet_id, retrieve pharma or non-pharma
					$fasical_info = $this->Month->find('first', array(
						'conditions' => array(
							'Month.month' => $month,
							'Month.year' => $year
						),
						'recursive' => -1
					));

					if (!empty($fasical_info)) {
						$this->loadModel('SaleTargetMonth');
						if ($cal_type == 1) {
							if ($is_pharma_type == 1) {
								$update_fields_arr = array('SaleTargetMonth.outlet_coverage_pharma_achievement' => "SaleTargetMonth.outlet_coverage_pharma_achievement+1");
							} else if ($is_pharma_type == 0) {
								$update_fields_arr = array('SaleTargetMonth.outlet_coverage_non_pharma_achievement' => "SaleTargetMonth.outlet_coverage_non_pharma_achievement+1");
							}
						} else {
							if ($is_pharma_type == 1) {
								$update_fields_arr = array('SaleTargetMonth.outlet_coverage_pharma_achievement' => "SaleTargetMonth.outlet_coverage_pharma_achievement-1");
							} else if ($is_pharma_type == 0) {
								$update_fields_arr = array('SaleTargetMonth.outlet_coverage_non_pharma_achievement' => "SaleTargetMonth.outlet_coverage_non_pharma_achievement-1");
							}
						}

						$conditions_arr = array('SaleTargetMonth.product_id' => 0, 'SaleTargetMonth.territory_id' => $terrority_id, 'SaleTargetMonth.fiscal_year_id' => $fasical_info['Month']['fiscal_year_id'], 'SaleTargetMonth.month_id' => $fasical_info['Month']['id']);
						$this->SaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
						//pr($conditions_arr);
						//pr($update_fields_arr);
						//exit;
					}
				}
			}
		}
	}

	// it will be called from order_details 
	public function stamp_calculation($order_no, $terrority_id, $product_id, $outlet_id, $quantity, $order_date, $cal_type, $gross_amount, $market_id)
	{
		// from outlet_id, get bonus_type_id and check if null then no action else action

		$this->loadModel('Outlet');
		// from outlet_id, retrieve pharma or non-pharma
		$outlet_info = $this->Outlet->find('first', array(
			'conditions' => array(
				'Outlet.id' => $outlet_id
			),
			'recursive' => -1
		));

		if (!empty($outlet_info) && $gross_amount > 0) {
			$bonus_type_id = $outlet_info['Outlet']['bonus_type_id'];
			if (($bonus_type_id === NULL) || (empty($bonus_type_id))) {
				// no action 
			} else {
				// from order_date , split month and get month name and compare month table with order year (get fascal year id)
				$orderDate = strtotime($order_date);
				$month = date("n", $orderDate);
				$year = date("Y", $orderDate);
				$this->loadModel('Month');
				$fasical_info = $this->Month->find('first', array(
					'conditions' => array(
						'Month.month' => $month,
						'Month.year' => $year
					),
					'recursive' => -1
				));

				if (!empty($fasical_info)) {
					// check bonus card table , where is_active,and others  and get min qty per order
					$this->loadModel('BonusCard');
					$bonus_card_info = $this->BonusCard->find('first', array(
						'conditions' => array(
							'BonusCard.fiscal_year_id' => $fasical_info['Month']['fiscal_year_id'],
							'BonusCard.is_active' => 1,
							'BonusCard.product_id' => $product_id,
							'BonusCard.bonus_card_type_id' => $bonus_type_id
						),
						'recursive' => -1
					));

					// if exist min qty per order , then stamp_no=mod(quantity/min qty per order)
					if (!empty($bonus_card_info)) {
						$min_qty_per_order = $bonus_card_info['BonusCard']['min_qty_per_order'];
						if ($min_qty_per_order && $min_qty_per_order <= $quantity) {
							$stamp_no = floor($quantity / $min_qty_per_order);
							if ($cal_type != 1) {
								$stamp_no = $stamp_no * (-1);
								$quantity = $quantity * (-1);
							}


							$this->loadModel('StoreBonusCard');
							$log_data = array();
							$log_data['StoreBonusCard']['created_at'] = $this->current_datetime();
							$log_data['StoreBonusCard']['territory_id'] = $terrority_id;
							$log_data['StoreBonusCard']['outlet_id'] = $outlet_id;
							$log_data['StoreBonusCard']['market_id'] = $market_id;
							$log_data['StoreBonusCard']['product_id'] = $product_id;
							$log_data['StoreBonusCard']['quantity'] = $quantity;
							$log_data['StoreBonusCard']['no_of_stamp'] = $stamp_no;
							$log_data['StoreBonusCard']['bonus_card_id'] = $bonus_card_info['BonusCard']['id'];
							$log_data['StoreBonusCard']['bonus_card_type_id'] = $bonus_type_id;
							$log_data['StoreBonusCard']['fiscal_year_id'] = $bonus_card_info['BonusCard']['fiscal_year_id'];
							$log_data['StoreBonusCard']['order_no'] = $order_no;

							$this->StoreBonusCard->create();
							$this->StoreBonusCard->save($log_data);
						}
					}
				}
			}
		}
	}

	public function admin_order_no_validation()
	{
		$this->loadModel('CsaOrder');
		//pr($this->request->data);die();
		if ($this->request->is('post')) {
			$order_no = $this->request->data['order_no'];
			$sale_type_id = $this->request->data['sale_type'];

			if ($sale_type_id == 1 || $sale_type_id == 3 || $sale_type_id == 4) {
				$order_list = $this->Order->find('list', array(
					'conditions' => array('Order.order_no' => $order_no),
					'fields' => array('order_no'),
					'recursive' => -1
				));
			} else {
				$order_list = $this->CsaOrder->find('list', array(
					'conditions' => array('CsaOrder.csa_order_no' => $order_no),
					'fields' => array('csa_order_no'),
					'recursive' => -1
				));
			}
			$order_exist = count($order_list);

			echo json_encode($order_exist);
		}

		$this->autoRender = false;
	}

	public function admin_get_product()
	{
		$this->loadModel('Store');
		$this->loadModel('Product');
		$this->loadModel('CurrentInventory');
		$territory_id = $this->request->data['territory_id'];
		$rs = array(array('id' => '', 'name' => '---- Select Product -----'));
		$store_info = $this->Store->find('first', array(
			'conditions' => array(
				'Store.territory_id' => $territory_id
			),
			'recursive' => -1
		));

		if (isset($store_info['Store']['id']) && $store_info['Store']['id']) {
			$store_id = $store_info['Store']['id'];

			if (isset($this->request->data['csa_id']) && $this->request->data['csa_id'] != 0) {
				$conditions = array('CurrentInventory.store_id' => $store_id, 'inventory_status_id' => 1);
			} else {
				$conditions = array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.qty > ' => 0, 'inventory_status_id' => 1);
			}

			$products_from_ci = $this->CurrentInventory->find('all', array(
				'fields' => array('DISTINCT CurrentInventory.product_id'),
				'conditions' => $conditions,
			));

			$product_ci = array();
			foreach ($products_from_ci as $each_ci) {
				$product_ci[] = $each_ci['CurrentInventory']['product_id'];
			}

			$product_ci_in = implode(",", $product_ci);
			$products = $this->Product->find('all', array(
				'conditions' => array('Product.id' => $product_ci), 'order' => array('order' => 'asc'),
				'fields' => array('Product.id as id', 'Product.name as name'),
				'recursive' => -1
			));

			$data_array = Set::extract($products, '{n}.0');

			if (!empty($products)) {
				echo json_encode(array_merge($rs, $data_array));
			} else {
				echo json_encode($rs);
			}
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}


	public function search_array($value, $key, $array)
	{
		foreach ($array as $k => $val) {
			if ($val[$key] == $value) {
				return $array[$k];
			}
		}
		return null;
	}


	public function admin_memo_editable($id = null)
	{
		if ($id) {
			$this->Order->id = $id;
			if ($this->Order->id) {
				if ($this->Order->saveField('memo_editable', 1)) {
					$this->Session->setFlash(__('The setting has been saved!'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash(__('Order editable failed!'), 'flash/error');
					$this->redirect(array('action' => 'index'));
				}
			}
		}
		$this->autoRender = false;
	}

	public function get_bonus_product_details()
	{
		$this->LoadModel('Product');

		$product_id = $this->request->data['product_id'];
		$territory_id = $this->request->data['territory_id'];
		$office_id = $this->request->data['office_id'];

		$product_details = $this->Product->find('first', array(
			'fields' => array('MIN(Product.product_category_id) as category_id', 'MIN(Product.sales_measurement_unit_id) as measurement_unit_id', 'MIN(MeasurementUnit.name) as measurement_unit_name', 'SUM(CurrentInventory.qty) as total_qty'),
			'conditions' => array('Product.id' => $product_id, 'Store.office_id' => $office_id),
			'joins' => array(
				array(
					'table' => 'measurement_units',
					'alias' => 'MeasurementUnit',
					'conditions' => 'MeasurementUnit.id=Product.sales_measurement_unit_id'
				),
				array(
					'table' => 'current_inventories',
					'alias' => 'CurrentInventory',
					'type' => 'Inner',
					'conditions' => 'CurrentInventory.product_id=Product.id'
				),
				array(
					'table' => 'stores',
					'alias' => 'Store',
					'type' => 'Inner',
					'conditions' => 'CurrentInventory.store_id=Store.id'
				)
			),
			'group' => array('Product.id', 'Store.id', 'Store.territory_id'),
			'recursive' => -1
		));
		//pr($product_details);exit;
		$data['category_id'] = $product_details[0]['category_id'];
		$data['measurement_unit_id'] = $product_details[0]['measurement_unit_id'];
		$data['measurement_unit_name'] = $product_details[0]['measurement_unit_name'];
		$data['total_qty'] = $this->unit_convertfrombase($product_id, $data['measurement_unit_id'], $product_details[0]['total_qty']);
		echo json_encode($data);
		$this->autoRender = false;
	}
	public function get_bonus_product()
	{
		$this->LoadModel('Product');
		$this->LoadModel('Store');

		$territory_id = $this->request->data['territory_id'];
		$store_id = $this->Store->find('first', array(
			'fields' => array('Store.id'),
			'conditions' => array('Store.territory_id' => $territory_id),
			'recursive' => -1
		));
		$product_list = $this->Product->find('all', array(
			'fields' => array('Product.id', 'Product.name'),
			'joins' => array(
				array(
					'table' => 'current_inventories',
					'alias' => 'CurrentInventory',
					'type' => 'Inner',
					'conditions' => 'CurrentInventory.product_id=Product.id'
				),
				array(
					'table' => 'open_combination_products',
					'alias' => 'OpenCombinationProduct',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.product_id=Product.id'
				),
				array(
					'table' => 'open_combinations',
					'alias' => 'OpenCombination',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.combination_id=OpenCombination.id'
				),

			),
			'conditions' => array(
				'CurrentInventory.qty >' => 0,
				'CurrentInventory.store_id' => $store_id['Store']['id'],
				'OpenCombination.start_date <=' => date('Y-m-d'),
				'OpenCombination.end_date >=' => date('Y-m-d'),
				'OpenCombination.is_bonus' => 1
			),
			'group' => array('Product.id', 'Product.name'),
			'recursive' => -1
		));
		echo json_encode($product_list);
		$this->autoRender = false;
	}

	public function get_product_price_id($product_id, $product_prices, $all_product_id)
	{
		// echo $product_id.'--'.$product_prices.'<br>';
		$this->LoadModel('ProductCombination');
		$this->LoadModel('Combination');
		$data = array();
		$product_price = $this->ProductCombination->find('first', array(
			'conditions' => array(
				'ProductCombination.product_id' => $product_id,
				'ProductCombination.price' => $product_prices,
				'ProductCombination.effective_date <=' => $this->current_date(),
			),
			'order' => array('ProductCombination.id' => 'DESC'),
			'recursive' => -1
		));

		// pr($product_price);exit;
		// echo $this->ProductCombination->getLastquery().'<br>';
		if ($product_price) {
			$is_combine = 0;
			if ($product_price['ProductCombination']['combination_id'] != 0) {
				$combination = $this->Combination->find('first', array(
					'conditions' => array('Combination.id' => $product_price['ProductCombination']['combination_id']),
					'recursive' => -1
				));
				$combination_product = explode(',', $combination['Combination']['all_products_in_combination']);
				foreach ($combination_product as $combination_prod) {
					if ($product_id != $combination_prod && in_array($combination_prod, $all_product_id)) {
						$data['combination_id'] = $product_price['ProductCombination']['combination_id'];
						$data['product_price_id'] = $product_price['ProductCombination']['id'];
						$is_combine = 1;
						break;
					}
				}
			}
			if ($is_combine == 0) {
				$product_price = $this->ProductCombination->find('first', array(
					'conditions' => array(
						'ProductCombination.product_id' => $product_id,
						'ProductCombination.price' => $product_prices,
						'ProductCombination.effective_date <=' => $this->current_date(),
						'ProductCombination.parent_slab_id' => 0
					),
					'order' => array('ProductCombination.id DESC'),
					'recursive' => -1
				));
				$data['combination_id'] = '';
				$data['product_price_id'] = $product_price['ProductCombination']['id'];
			}
			return $data;
		} else {
			$data['combination_id'] = '';
			$data['product_price_id'] = '';
			return $data;
		}
	}
	function get_csa_list_by_office_id()
	{
		/*pr($this->request->data);*/
		$office_id = $this->request->data['office_id'];
		$output = "<option value=''>--- Select Csa ---</option>";
		if ($office_id) {
			$csa_outlet = $this->Outlet->find('list', array(
				'conditions' => array('Territory.office_id' => $office_id, 'Outlet.is_csa' => 1),
				'joins' => array(
					array(
						'table' => 'markets',
						'alias' => 'Market',
						'conditions' => 'Market.id=Outlet.market_id'
					),
					array(
						'table' => 'territories',
						'alias' => 'Territory',
						'conditions' => 'Territory.id=Market.territory_id'
					),
				)
			));
			if ($csa_outlet) {
				foreach ($csa_outlet as $key => $data) {
					$output .= "<option value='$key'>$data</option>";
				}
			}
		}
		echo $output;
		$this->autoRender = false;
	}
	function get_territory_list_by_csa_id()
	{
		$this->LoadModel('Territory');
		$csa_id = $this->request->data['csa_id'];
		$output = "<option value=''>--- Select Territory ---</option>";
		if ($csa_id) {
			$territory = $this->Territory->find('list', array(
				'conditions' => array('Outlet.id' => $csa_id),
				'joins' => array(
					array(
						'alias' => 'Market',
						'table' => 'markets',
						'type' => 'INNER',
						'conditions' => 'Market.territory_id = Territory.id'
					),
					array(
						'alias' => 'Outlet',
						'table' => 'outlets',
						'type' => 'INNER',
						'conditions' => 'Outlet.market_id = Market.id'
					),
				)
			));

			if ($territory) {
				foreach ($territory as $key => $data) {
					$output .= "<option value='$key'>$data</option>";
				}
			}
		}

		echo $output;
		$this->autoRender = false;
	}
	function get_thana_by_territory_id()
	{
		$territory_id = $this->request->data['territory_id'];
		$output = "<option value=''>--- Select Thana ---</option>";
		if ($territory_id) {
			$thana = $this->Thana->find('list', array(
				'conditions' => array('ThanaTerritory.territory_id' => $territory_id),
				'joins' => array(
					array(
						'table' => 'thana_territories',
						'alias' => 'ThanaTerritory',
						'conditions' => 'ThanaTerritory.thana_id=Thana.id'
					)
				)
			));

			if ($thana) {
				foreach ($thana as $key => $data) {
					$output .= "<option value='$key'>$data</option>";
				}
			}
		}

		echo $output;
		$this->autoRender = false;
	}
	function get_market_by_thana_id()
	{
		$thana_id = $this->request->data['thana_id'];
		$output = "<option value=''>--- Select Market ---</option>";
		if ($thana_id) {
			$market = $this->Market->find('list', array(
				'conditions' => array('Market.thana_id' => $thana_id)
			));
			if ($market) {
				foreach ($market as $key => $data) {
					$output .= "<option value='$key'>$data</option>";
				}
			}
		}
		echo $output;
		$this->autoRender = false;
	}


	private function outletGroupCheck($outlet_id = 0)
	{
		if ($outlet_id) {
			$this->loadModel('Outlet');
			$result = $this->Outlet->find('first', array(
				'fields' => array('is_within_group'),
				'conditions' => array('Outlet.id' => $outlet_id),
				'recursive' => -1
			));
			if ($result) {
				return $result['Outlet']['is_within_group'];
			} else {
				return 0;
			}
		}
	}

	private function productInjectableCheck($products_ids = array())
	{
		if ($products_ids) {
			$this->loadModel('Product');

			$result = $this->Product->find('first', array(
				'fields' => array('is_injectable'),
				'conditions' => array(
					'Product.id' => $products_ids,
					'Product.is_injectable' => 1
				),
				'recursive' => -1
			));
			if ($result) {
				return $result['Product']['is_injectable'];
			} else {
				return 0;
			}
		}
	}



	private function getoutletlist($company_id)
	{

		$this->loadModel('Outlet');
		$this->loadModel('DistDistributor');
		//$rs = array(array('id' => '', 'name' => '---- Select Distributor -----'));
		$rs = array();
		//$company_id = $this->request->data['company_id'];
		$outlet_list = $this->Outlet->find('all', array(
			'fields' => array('Outlet.id', 'Outlet.name', 'Outlet.distributor_id'),
			'conditions' => array(
				'Outlet.company_id' => $company_id
			),
			'order' => array('Outlet.id' => 'asc'),
			'recursive' => -1
		));

		//pr($outlet_list);die();

		//$data_array = Set::extract($outlet_list, '{n}.0');

		$data_array = array();

		foreach ($outlet_list as $key => $value) {
			if ($value['Outlet']['distributor_id'] != null) {

				$name = $this->DistDistributor->find('all', array(
					'fields' => array('DistDistributor.name'),
					'conditions' => array(
						'DistDistributor.id' => $value['Outlet']['distributor_id']
					),

					'recursive' => -1
				));

				$data_array[] = array(
					'id' => $value['Outlet']['id'],
					'name' => $name[0]['DistDistributor']['name'],
				);
			}
		}
		//pr($data_array);die();
		/* if(!empty($outlet_list)){
		echo json_encode(array_merge($rs,$data_array));
	}else{
		echo json_encode($rs);
	} 
	$this->autoRender = false;*/
		return $data_array;
	}

	private function getDistributorListWithName($company_id)
	{

		$this->loadModel('Outlet');
		$this->loadModel('DistDistributor');
		//$rs = array(array('id' => '', 'name' => '---- Select Distributor -----'));
		$rs = array();
		// $company_id = $this->request->data['company_id'];
		$outlet_list = $this->Outlet->find('all', array(
			'fields' => array('Outlet.id', 'Outlet.name', 'Outlet.distributor_id'),
			'conditions' => array(
				'Outlet.company_id' => $company_id
			),
			'order' => array('Outlet.id' => 'asc'),
			'recursive' => -1
		));

		$data_array = array();

		foreach ($outlet_list as $key => $value) {
			if ($value['Outlet']['distributor_id'] != null) {

				$name = $this->DistDistributor->find('all', array(
					'fields' => array('DistDistributor.name'),
					'conditions' => array(
						'DistDistributor.id' => $value['Outlet']['distributor_id']
					),

					'recursive' => -1
				));

				/* $data_array[] = array(
				'id' => $value['Outlet']['id'],
				 'name'=>$name[0]['DistDistributor']['name'],
				);*/

				$data_array[] = array(
					'id' => $value['Outlet']['id'],
					'name' => $name[0]['DistDistributor']['name'],
				);
			}
		}
		// pr($data_array);die();
		return $data_array;
	}
	public function get_outlet_list_with_distributor_name($company_id)
	{

		$this->loadModel('Outlet');
		$this->loadModel('DistDistributor');
		$rs = array(array('id' => '', 'name' => '---- Select Dealer -----'));
		//$company_id = $this->request->data['company_id'];
		$outlet_list = $this->Outlet->find('all', array(
			'fields' => array('Outlet.id', 'Outlet.name', 'Outlet.distributor_id'),
			'conditions' => array(
				'Outlet.company_id' => $company_id
			),
			'order' => array('Outlet.id' => 'asc'),
			'recursive' => -1
		));

		//pr($outlet_list);die();

		//$data_array = Set::extract($outlet_list, '{n}.0');

		$data_array = array();

		foreach ($outlet_list as $key => $value) {
			if ($value['Outlet']['distributor_id'] != null) {

				$name = $this->DistDistributor->find('all', array(
					'fields' => array('DistDistributor.name'),
					'conditions' => array(
						'DistDistributor.id' => $value['Outlet']['distributor_id']
					),

					'recursive' => -1
				));

				$data_array[] = array(
					'id' => $value['Outlet']['id'],
					'name' => $name[0]['DistDistributor']['name'],
				);
			}
		}
		// pr($data_array);die();
		return $data_array;
	}

	public function get_inventory_product_list()
	{
		$product_list = $this->request->data['products'];
		$store_id = $this->request->data['store_id'];
		$products = array();
		$canPermitted = 1;
		$msg = "";
		$this->loadModel('CurrentInventory');
		$this->loadModel('Product');
		if (!empty($product_list)) {
			//pr($product_list);die();
			foreach ($product_list as $key => $value) {
				if (!empty($value['product_id'])) {
					$stoksinfo = $this->CurrentInventory->find('all', array(
						'conditions' => array(
							'CurrentInventory.store_id' => $store_id,
							'CurrentInventory.product_id' => $value['product_id']
						),
						'fields' => array('sum(qty) as total'),
					));
					$total_qty = $stoksinfo[0][0]['total'];
					$stoks = $this->CurrentInventory->find('first', array(
						'conditions' => array(
							'CurrentInventory.store_id' => $store_id,
							'CurrentInventory.product_id' => $value['product_id']
						),
						'fields' => array('Product.id', 'Product.name', 'CurrentInventory.qty'),
					));

					if (empty($stoks)) {
						$empty_stoks = $value;
						$canPermitted = 0;
						$productName = $this->Product->find('first', array(
							'conditions' => array('id' => $value['product_id']),
							'fields' => array('id', 'name'),
							'recursive' => -1
						));
						$products['Product'][$key]['name'] = $productName['Product']['name'];
						$products['Product'][$key]['id'] = $productName['Product']['id'];
						$msg = $msg . "This " . $productName['Product']['name'] . " is not Avilable In Stoke";
						$msg = $msg . " \n ";
					} elseif (!empty($stoks)) {
						if ($total_qty < $value['sales_qty']) {
							$canPermitted = 0;
							$products['Product'][$key]['name'] = $stoks['Product']['name'];
							$products['Product'][$key]['id'] = $stoks['Product']['id'];

							$msg = $msg . " " . $stoks['Product']['name'] . " Product has " . $stoks['CurrentInventory']['qty'] . " Quantity in Stoke";
							$msg = $msg . " \n ";
						}
					}
				}
			}
			//pr($productName);die();
			//pr($products);die();
			$products['canPermitted'] = $canPermitted;
			$products['msg'] = $msg;
			echo json_encode($products);
			exit;
		}
	}

	/*    public function get_inventory_by_product_id(){
		$product_id = $this->request->data['id'];
		$store_id = $this->request->data['store_id'];
	}*/

	public function get_remaining_quantity()
	{
		// pr($this->request->data);die();
		$this->loadModel('OrderDetail');
		$this->loadModel('Order');
		$this->loadModel('TempOrderDetail');
		$order_id = $this->request->data['order_id'];
		$product_id = $this->request->data['product_id'];

		$orderStatus = $this->Order->find('first', array('conditions' => array('Order.id' => $order_id)));
		if ($orderStatus['Order']['confirm_status'] != 1) {
			$remaining_qty = $this->OrderDetail->find('first', array(
				//'fields'=>array('remaining_qty','sales_qty','Order.manage_draft_status'),
				'conditions' => array(
					'OrderDetail.order_id' => $order_id,
					'OrderDetail.product_id' => $product_id,
				),
			));
			$qty['remaining_qty'] = $remaining_qty['OrderDetail']['remaining_qty'];
			$qty['sales_qty'] = $remaining_qty['OrderDetail']['sales_qty'];
			$qty['deliverd_qty'] = $remaining_qty['OrderDetail']['deliverd_qty'];
		} else {
			$remaining_qty = $this->TempOrderDetail->find('first', array(
				//'fields'=>array('remaining_qty','sales_qty','Order.manage_draft_status'),
				'conditions' => array(
					'TempOrderDetail.order_id' => $order_id,
					'TempOrderDetail.product_id' => $product_id,
				),
			));
			$qty['remaining_qty'] = $remaining_qty['TempOrderDetail']['remaining_qty'];
			$qty['sales_qty'] = $remaining_qty['TempOrderDetail']['sales_qty'];
			$qty['deliverd_qty'] = $remaining_qty['TempOrderDetail']['deliverd_qty'];
		}
		//pr($remaining_qty);die();

		//$qty['status']=$remaining_qty['Order']['manage_draft_status'];
		echo json_encode($qty);
		exit;
	}
	public function forcefullyclosed()
	{
		$order_id = $this->request->data('order_id');
		$this->loadModel('Order');
		$order = $this->Order->find('first', array('conditions' => array('id' => $order_id), 'recursive' => -1));
		//pr($order);die();
		$new_order = $order['Order'];
		$new_order['is_closed'] = 1;
		//$new_order['manage_draft_status']=2;
		$new_order['confirm_status'] = 2;

		$this->Order->save($new_order);

		echo 1;
		exit;
	}
	public function admin_edit_backup_15_09_2019($id = null)
	{


		ini_set('memory_limit', '-1');
		$this->loadModel('InstrumentType');
		$instrumenttype_condition = array(
			"NOT" => array("id" => array(2, 10, 11))
		);
		$instrumentType = $this->InstrumentType->find('list', array('conditions' => $instrumenttype_condition));

		$this->set(compact('instrumentType'));
		$this->loadModel('ProductCombination');
		$this->loadModel('Combination');
		$this->Session->delete('cart_session_data');
		$this->Session->delete('matched_session_data');
		$this->Session->delete('combintaion_qty_data');

		$this->loadModel('Product');

		$this->loadModel('Company');
		$companies = $this->Company->find('list', array());
		$this->set(compact('companies'));

		$current_date = date('d-m-Y', strtotime($this->current_date()));

		$this->loadModel('Product');
		$company_id = $this->Session->read('Office.company_id');
		$user_group_id = $this->Session->read('Office.group_id');

		/* ----- start code of product list ----- */
		$maintain_dealer_type = $this->Session->read('Office.company_info.Company.maintain_dealer');
		if ($user_group_id == 1) {
			$product_list = $this->Product->find('list', array('order' => array('order' => 'asc')));
		} else {
			$product_list = $this->Product->find('list', array(
				'conditions' => array('company_id' => $company_id),
				'order' => array('order' => 'asc')
			));
			if ($user_group_id == 2) {
				if ($maintain_dealer_type == 1) {
					$distributers_list = $this->getDistributorListWithName($company_id);
					foreach ($distributers_list as $key => $value) {
						$distributers[$value['id']] = $value['name'];
					}
					//pr($distributers_list);
					//pr($distributers);die();
					$this->set(compact('distributers'));
				} else {

					$this->loadModel('Outlet');
					$office_outlets = $this->Outlet->find('list', array('conditions' => array('company_id' => $company_id)));
					$this->loadModel('Market');
					$market_list = $this->Market->find('list', array('conditions' => array('company_id' => $company_id)));
				}
				//$offices=$this->Office->find('');
			} else {
				if ($maintain_dealer_type == 1) {
					$distributers_list = $this->getDistributorListWithName($company_id);
					foreach ($distributers_list as $key => $value) {
						$distributers[$value['id']] = $value['name'];
					}
					$this->set(compact('distributers'));
				} else {

					$this->loadModel('Outlet');
					$office_outlets = $this->Outlet->find('list', array('conditions' => array('company_id' => $company_id)));
					$this->loadModel('Market');
					$market_list = $this->Market->find('list', array('conditions' => array('company_id' => $company_id)));
					//pr($outlets);die(); 
				}
			}
		}

		$this->set(compact('office_outlets'));
		$this->set(compact('market_list'));
		$this->set(compact('product_list'));

		/* ------- start get edit data -------- */
		$this->Order->recursive = 1;
		$options = array(
			'conditions' => array('Order.id' => $id)
		);

		$existing_record = $this->Order->find('first', $options);
		//pr( $existing_record);die();
		$this->loadModel('CurrentInventory');
		/* $stoks=$this->CurrentInventory->find('all',array(
				'conditions'=>array(
					'CurrentInventory.store_id'=>$existing_record['Order']['w_store_id']
				),
				'fields'=>array('Product.id','Product.name','CurrentInventory.qty'),
				
			));*/
		// pr($stoks);die();
		$msg = "";
		$canPermitted = 1;

		if (!empty($existing_record['OrderDetail'])) {
			foreach ($existing_record['OrderDetail'] as $key => $value) {
				$OrderDetail_record[$value['product_id']] = $value;
				$stoks = $this->CurrentInventory->find('first', array(
					'conditions' => array(
						'CurrentInventory.store_id' => $existing_record['Order']['w_store_id'],
						'CurrentInventory.product_id' => $value['product_id']
					),
					'fields' => array('Product.id', 'Product.name', 'CurrentInventory.qty'),
				));
				//pr($value);
				if (empty($stoks)) {
					$empty_stoks = $value;
					$canPermitted = 0;
					$productName = $this->Product->find('first', array(
						'conditions' => array('id' => $value['product_id']),
						'fields' => array('name'),
						'recursive' => -1
					));
					$msg = $msg . $productName['Product']['name'] . " Is not Available!!! ";
					$msg = $msg . "<br>";
				} elseif (!empty($stoks)) {
					if ($stoks['CurrentInventory']['qty'] < $value['sales_qty']) {
						$canPermitted = 0;
						$msg = $msg . $stoks['Product']['name'] . " Is Insufficient!!! ";
						$msg = $msg . "<br>";
					}
				}
			}
		}
		//pr($OrderDetail_record);die();

		if ($canPermitted == 0) {
			$this->Session->setFlash(__($msg), 'flash/warning');
		}
		$this->set(compact('canPermitted'));


		//pr($existing_record);//die();
		/*if(!empty($existing_record['OrderDetail'])){
				foreach ($existing_record['OrderDetail'] as $key => $value) {
					$Order_products[$key]=$value['product_id'];
				}
			}
			
			$this->loadModel('CurrentInventory');
		   
			 $stoks=$this->CurrentInventory->find('all',array(
					'conditions'=>array(
						'CurrentInventory.store_id'=>$existing_record['Order']['w_store_id'],
						'CurrentInventory.product_id IN'=>$Order_products
					),
					'recursive'=>-1
				));
			 $i=0;
			 //pr($stoks);die();
			foreach ($existing_record['OrderDetail'] as $key => $value) {
				if($stoks[$i]['CurrentInventory']['product_id'] != $value['product_id'] ){
						pr($value);  
						$i = $i+1;
				}
			}
			die();*/
		$details_data = array();
		foreach ($existing_record['OrderDetail'] as $detail_val) {
			$product = $detail_val['product_id'];
			$this->Combination->unbindModel(
				array('hasMany' => array('ProductCombination'))
			);
			$combination_list = $this->Combination->find('all', array(
				'conditions' => array('ProductCombination.product_id' => $product),
				'joins' => array(
					array(
						'alias' => 'ProductCombination',
						'table' => 'product_combinations',
						'type' => 'INNER',
						'conditions' => 'Combination.id = ProductCombination.combination_id'
					)
				),
				'fields' => array('Combination.all_products_in_combination'),
				'limit' => 1
			));
			if (!empty($combination_list)) {
				$combined_product = $combination_list[0]['Combination']['all_products_in_combination'];
				$detail_val['combined_product'] = $combined_product;
			}
			$details_data[] = $detail_val;
		}

		$existing_record['OrderDetail'] = $details_data;

		$this->loadModel('MeasurementUnit');
		for ($i = 0; $i < count($details_data); $i++) {
			$measurement_unit_id = $details_data[$i]['measurement_unit_id'];
			$measurement_unit_name = $this->MeasurementUnit->find('all', array(
				'conditions' => array('MeasurementUnit.id' => $measurement_unit_id),
				'fields' => array('name'),
				'recursive' => -1
			));
			$existing_record['OrderDetail'][$i]['measurement_unit_name'] = $measurement_unit_name[0]['MeasurementUnit']['name'];
		}

		$existing_record['office_id'] = $existing_record['SalesPerson']['office_id'];
		$existing_record['territory_id'] = $existing_record['Order']['territory_id'];
		$existing_record['market_id'] = $existing_record['Order']['market_id'];
		$existing_record['outlet_id'] = $existing_record['Order']['outlet_id'];
		$existing_record['order_time'] = date('d-m-Y', strtotime($existing_record['Order']['order_time']));
		$existing_record['order_date'] = date('d-m-Y', strtotime($existing_record['Order']['order_date']));
		$existing_record['order_no'] = $existing_record['Order']['order_no'];
		$existing_record['memo_reference_no'] = $existing_record['Order']['memo_reference_no'];

		$existing_record['order_reference_no'] = $existing_record['Order']['order_reference_no'];
		$existing_record['reference_number'] = $existing_record['Order']['instrument_reference_no'];
		$existing_record['instrumentType_id'] = $existing_record['Order']['instrument_type'];

		//pr($existing_record);die();
		$this->LoadModel('Product');
		$this->LoadModel('Store');
		$store_id = $this->Store->find('first', array(
			'fields' => array('Store.id'),
			'conditions' => array('Store.id' => $existing_record['Order']['w_store_id']),
			'recursive' => -1
		));

		$open_bonus_product = $this->Product->find('all', array(
			'fields' => array('Product.id', 'Product.name'),
			'joins' => array(
				array(
					'table' => 'current_inventories',
					'alias' => 'CurrentInventory',
					'type' => 'Inner',
					'conditions' => 'CurrentInventory.product_id=Product.id'
				),
				array(
					'table' => 'open_combination_products',
					'alias' => 'OpenCombinationProduct',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.product_id=Product.id'
				),
				array(
					'table' => 'open_combinations',
					'alias' => 'OpenCombination',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.combination_id=OpenCombination.id'
				),

			),
			'conditions' => array(
				'CurrentInventory.qty >' => 0,
				'CurrentInventory.store_id' => $store_id['Store']['id'],
				'OpenCombination.start_date <=' => date('Y-m-d'),
				'OpenCombination.end_date >=' => date('Y-m-d'),
				'OpenCombination.is_bonus' => 1
			),
			'recursive' => -1
		));
		$open_bonus_product_option = array();
		foreach ($open_bonus_product as $bonus_product) {
			$open_bonus_product_option[$bonus_product['Product']['id']] = $bonus_product['Product']['name'];
		}

		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($user_group_id == 1) {
			$office_conditions = array();
		} elseif ($user_group_id == 2) {
			$office_conditions = array('Office.company_id' => $company_id, 'Office.office_type_id' => 2);
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}

		// $this->set('office_id', $existing_record['office_id']);
		$this->loadModel('Office');
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

		$office_id = $existing_record['office_id'];
		$territory_id = $existing_record['territory_id'];
		$market_id = $existing_record['market_id'];
		$outlets = array();


		$this->loadModel('Territory');
		$territories_list = $this->Territory->find('all', array(
			'conditions' => array('Territory.office_id' => $office_id),
			'order' => array('Territory.name' => 'asc')
		));
		$territories = array();
		foreach ($territories_list as $t_result) {
			$territories[$t_result['Territory']['id']] = $t_result['Territory']['name'] . ' (' . $t_result['SalesPerson']['name'] . ')';
		}


		$territory_ids = array($territory_id);


		$markets = $this->Market->find('list', array(
			'conditions' => array('territory_id' => $territory_ids),
			'order' => array('name' => 'asc')
		));


		$company_id = $existing_record['Order']['company_id'];
		$outlets = $this->get_outlet_list_with_distributor_name($company_id);
		//pr($outlets);die();
		$this->loadModel('Store');
		$this->loadModel('Product');
		$this->loadModel('CurrentInventory');
		$store_info = $this->Store->find('first', array(
			'conditions' => array(
				'Store.id' => $existing_record['Order']['w_store_id']
			),
			'recursive' => -1
		));
		// pr($store_info);die();
		$store_id = $store_info['Store']['id'];
		// pr($existing_record['OrderDetail']);die();
		foreach ($existing_record['OrderDetail'] as $key => $single_product) {



			$total_qty_arr = $this->CurrentInventory->find('all', array(
				'conditions' => array('store_id' => $store_id, 'product_id' => $single_product['product_id']),
				'fields' => array('sum(qty) as total'),
				'recursive' => -1
			));

			$total_qty = $total_qty_arr[0][0]['total'];

			$sales_total_qty = $this->unit_convertfrombase($single_product['product_id'], $single_product['measurement_unit_id'], $total_qty);

			$existing_record['OrderDetail'][$key]['stock_qty'] = $sales_total_qty;
		}


		$products_from_ci = $this->CurrentInventory->find('all', array(
			'fields' => array('DISTINCT CurrentInventory.product_id'),
			'conditions' => array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.qty > ' => 0, 'inventory_status_id' => 1)
		));

		$product_ci = array();
		foreach ($products_from_ci as $each_ci) {
			$product_ci[] = $each_ci['CurrentInventory']['product_id'];
		}
		foreach ($existing_record['OrderDetail'] as $value) {
			$product_ci[] = $value['product_id'];
		}

		$product_ci_in = implode(",", $product_ci);

		/*$product_list = $this->Product->find('list', array('conditions' => array('Product.id' => $product_ci),'order' => array('order' => 'asc')));*/

		/* ------- end get edit data -------- */


		/*-----------My Work--------------*/
		$this->loadModel('ProductPrice');
		$this->loadModel('ProductCombination');


		foreach ($existing_record['OrderDetail'] as $key => $value) {
			$existing_product_category_id_array = $this->Product->find('all', array(
				'conditions' => array('Product.id' => $value['product_id']),
				'fields' => array('product_category_id'),
				'recursive' => -1
			));

			$existing_product_category_id = $existing_product_category_id_array[0]['Product']['product_category_id'];

			if ($existing_product_category_id != 32) {
				$individual_slab = array();
				$combined_slab = array();
				$all_combination_id = array();

				$retrieve_price_combination[$value['product_id']] = $this->ProductPrice->find('all', array(
					'conditions' => array('ProductPrice.product_id' => $value['product_id'], 'ProductPrice.has_combination' => 0)
				));

				foreach ($retrieve_price_combination[$value['product_id']][0]['ProductCombination'] as $key => $value2) {
					$individual_slab[$value2['min_qty']] = $value2['price'];
				}


				$combination_info = $this->ProductCombination->find('first', array(
					'conditions' => array('ProductCombination.product_id' => $value['product_id'], 'ProductCombination.combination_id !=' => 0)
				));

				if (!empty($combination_info['ProductCombination']['combination_id'])) {
					$combination_id = $combination_info['ProductCombination']['combination_id'];
					$all_combination_id_info = $this->ProductCombination->find('all', array(
						'conditions' => array('ProductCombination.combination_id' => $combination_id)
					));

					$combined_product = '';
					foreach ($all_combination_id_info as $key => $individual_combination_id) {
						$all_combination_id[$individual_combination_id['ProductCombination']['product_id']] = $individual_combination_id['ProductCombination']['price'];

						$individual_combined_product_id = $individual_combination_id['ProductCombination']['product_id'];

						$combined_product = $combined_product . ',' . $individual_combined_product_id;
					}
					$trimmed_combined_product = ltrim($combined_product, ',');

					$combined_slab[$combination_info['ProductCombination']['min_qty']] = $all_combination_id;

					$matched_combined_product_id_array = explode(',', $trimmed_combined_product);
					asort($matched_combined_product_id_array);
					$matched_combined_product_id = implode(',', $matched_combined_product_id_array);
				} else {
					$combined_slab = array();
					$matched_combined_product_id = '';
				}



				$edited_cart_data[$value['product_id']] = array(
					'product_price' => array(
						'id' => $retrieve_price_combination[$value['product_id']][0]['ProductPrice']['id'],
						'product_id' => $value['product_id'],
						'general_price' => $retrieve_price_combination[$value['product_id']][0]['ProductPrice']['general_price'],
						'effective_date' => $retrieve_price_combination[$value['product_id']][0]['ProductPrice']['effective_date']
					),
					'individual_slab' => $individual_slab,
					'combined_slab' => $combined_slab,
					'combined_product' => $matched_combined_product_id
				);



				if (!empty($matched_combined_product_id)) {
					$edited_matched_data[$matched_combined_product_id] = array(
						'count' => '4',
						'is_matched_yet' => 'NO',
						'matched_count_so_far' => '2',
						'matched_id_so_far' => '63,65'
					);

					$edited_current_qty_data[$value['product_id']] = $value['sales_qty'];
				}
			}
		}

		if (!empty($edited_cart_data)) {
			$this->Session->write('cart_session_data', $edited_cart_data);
		}
		if (!empty($edited_matched_data)) {
			$this->Session->write('matched_session_data', $edited_matched_data);
		}
		if (!empty($edited_current_qty_data)) {
			$this->Session->write('combintaion_qty_data', $edited_current_qty_data);
		}


		$this->set('page_title', 'Edit Order');
		$this->Order->id = $id;
		if (!$this->Order->exists($id)) {
			throw new NotFoundException(__('Invalid Order'));
		}
		/* -------- create individual Product data --------- */



		global $cart_data, $matched_array;
		$cart_data = array();
		$matched_array = array();
		$qty_session_data = array();


		/* ---------creating prepare data---------- */
		$prepare_cart_data = array();
		if (!empty($filter_product['Product']['id'])) {
			$prepare_cart_data[$filter_product['Product']['id']]['Product'] = $filter_product['Product'];
			$prepare_cart_data[$filter_product['Product']['id']]['ProductPrice'] = $filter_product['ProductPrice'];
			foreach ($filter_product['Individual_slab'] as $individual_slab_val) {
				$prepare_cart_data[$filter_product['Product']['id']]['Individual_slab'][$individual_slab_val['min_qty']] = $individual_slab_val['price'];
			}
			if (!empty($filter_product['Combination'])) {
				$prepare_cart_data[$filter_product['Product']['id']]['Combination'] = $filter_product['Combination'];
			}
			if (!empty($filter_product['Combination_id'])) {

				/* ---- start ------- */
				$this->Combination->recursive = 2;
				$condition_value1['Combination.id'] = $filter_product['Combination_id']; //$filter_product['Combination']['id'];
				$combination_slab_data_option = array(
					'conditions' => array($condition_value1),
				);
				$combination_slab_data = $this->Combination->find('all', $combination_slab_data_option);
				/* ----- end -------- */

				$combination_slab = array();
				foreach ($combination_slab_data as $combine_group) {
					foreach ($combine_group['ProductCombination'] as $combine_val) {
						$combination_slab[$combine_val['min_qty']][$combine_val['product_id']] = $combine_val['Childrel']['price'];
					}
				}
				if (!empty($combination_slab)) {
					$prepare_cart_data[$filter_product['Product']['id']]['Combined_slab'] = $combination_slab;
				}
			}
		}
		/* ----------start create cart data and matched data ----------- */



		/* ------ unset cart data ------- */

		$user_office_id = $this->UserAuth->getOfficeId();
		/*$sales_person_list = $this->SalesPerson->find('list', array(
				'conditions' => array('SalesPerson.office_id' => $user_office_id, 'User.user_group_id' => 4)
				));*/
		$sales_person_list = $this->SalesPerson->find('list', array(
			'conditions' => array('SalesPerson.office_id' => $user_office_id)
		));

		//$this->Order->id = $id;
		$count = 0;
		if ($this->request->is('post')) {

			//$sale_type_id = $this->request->data['OrderProces']['sale_type_id'];

			//pr($this->request->data); die();
			//exit;
			if ($office_parent_id == 0) {
				$company_id = $this->request->data['OrderProces']['company_id'];
			} else {
				$company_id  = $this->Session->read('Office.company_id');
			}

			//$outlet_is_within_group = $this->outletGroupCheck($this->request->data['OrderProces']['outlet_id']);
			$outlet_is_within_group = $this->outletGroupCheck($this->request->data['OrderProces']['distribut_outlet_id']);
			$product_is_injectable = $this->productInjectableCheck($this->request->data['OrderDetail']['product_id']);

			/*if($product_is_injectable && !$outlet_is_within_group && $sale_type_id != 2){
					$this->Session->setFlash(__("Special product can't sell to ordinary outlet!"), 'flash/error');
					$this->redirect(array('action' => 'create_order'));
					exit;
				}*/
			//pr($id);die();
			if (array_key_exists('save', $this->request->data)) {
				foreach ($this->request->data['OrderDetail']['remaining_qty'] as $key => $val) {
					if ($val != 0) {
						//$orderData['is_closed'] = 1;
						$count++;
					}
				}
				if ($count == 0) {
					$orderData['is_closed'] = 1;
				} else {
					$orderData['is_closed'] = 0;
				}
			}
			$order_id = $id;

			//$this->admin_delete($order_id, 0);
			//pr($this->request->data);die();

			/*START ADD NEW*/
			//get office id 
			$office_id = $this->request->data['OrderProces']['office_id'];
			$outlet_id = $this->request->data['OrderProces']['distribut_outlet_id'];
			//get thana id 
			$this->loadModel('Outlet');
			$getOutlets = $this->Outlet->find('all', array(
				'conditions' => array('Outlet.id' => $outlet_id),
			));

			// pr( $getOutlets);//die();
			$outlet_info = $this->Outlet->find('first', array(
				'conditions' => array('Outlet.id' => $this->request->data['OrderProces']['distribut_outlet_id']),
				'recursive' => -1
			));
			// pr($outlet_info);die();

			$market_id = $getOutlets[0]['Outlet']['market_id'];
			$this->loadModel('Market');
			$market_info = $this->Market->find(
				'first',
				array(
					'conditions' => array('Market.id' => $market_id),
					'fields' => 'Market.thana_id',
					'order' => array('Market.id' => 'asc'),
					'recursive' => -1,
					//'limit' => 100
				)
			);
			$thana_id = $market_info['Market']['thana_id'];
			/*END ADD NEW*/
			$dealer_is_limit_check = 0;
			//pr($thana_id);die();
			$this->request->data['OrderProces']['is_active'] = 1;
			//$this->request->data['OrderProces']['status'] = ($this->request->data['OrderProces']['credit_amount'] != 0) ? 1 : 2;

			if (array_key_exists('draft', $this->request->data)) {
				$this->request->data['OrderProces']['status'] = 2;
				$this->request->data['OrderProces']['confirm_status'] = 1;
				//$this->request->data['OrderProces']['manage_draft_status'] = 1;
				//$this->request->data['OrderProces']['confirmed'] = 1;
				$message = "Order Has Been Saved as Draft";

				$is_execute = 0;
			} else {
				$message = "Order Has Been Saved";
				$this->request->data['OrderProces']['status'] = 2;
				// $this->request->data['OrderProces']['confirmed'] = 1;
				// $this->request->data['OrderProces']['status'] = ($this->request->data['Order']['credit_amount'] != 0) ? 1 : 2;
				$this->request->data['OrderProces']['confirm_status'] = 2;
				//$this->request->data['OrderProces']['manage_draft_status'] = 2;
				$is_execute = 1;
			}

			//for distibuter limit amount
			$maintain_dealer_type = $this->Session->read('Office.company_info.Company.maintain_dealer');

			$distributor_id = $outlet_info['Outlet']['distributor_id'];

			$this->loadModel('DistDistributor');
			$distributor_info = $this->DistDistributor->find('first', array(
				'conditions' => array(
					'DistDistributor.id' => $distributor_id
				),
				'recursive' => -1
			));
			//pr($distributor_info);//die();

			//for distibuter limit amount
			if (!empty($distributor_info['DistDistributor']['dealer_is_limit_check'])) {
				$dealer_is_limit_check = $distributor_info['DistDistributor']['dealer_is_limit_check'];
			}
			$maintain_dealer_type = $this->Session->read('Office.company_info.Company.maintain_dealer');
			if ($maintain_dealer_type == 1) {

				if ($dealer_is_limit_check == 1) {

					$this->loadModel('DistDistributorBalance');
					$dealer_limit_info = $this->DistDistributorBalance->find('first', array(
						'conditions' => array(
							'DistDistributorBalance.dist_distributor_id' => $getOutlets[0]['Outlet']['distributor_id']
							//'DistDistributorBalance.effective_date >=' => date('Y-m-d'),
							//'DistDistributorBalance.end_effective_date =' => '',
						),
						////'order' => 'DistDistributorBalance.effective_date DESC',
						'limit' => 1,
						'recursive' => -1
					));


					//pr($dealer_limit_info);


					$credit_amount = $this->request->data['Order']['credit_amount'];
					$dealer_limit = $dealer_limit_info['DistDistributorBalance']['balance'];

					//print_r($dealer_limit);exit;

					$this->loadModel('DistDistributorBalanceHistory');
					$dealer_balance_info = $this->DistDistributorBalanceHistory->find('first', array(
						'conditions' => array(
							'DistDistributorBalanceHistory.dist_distributor_id' => $getOutlets[0]['Outlet']['distributor_id'],
							//'DistDistributorBalanceHistory.is_execute' => 1,
						),
						'order' => 'DistDistributorBalanceHistory.id DESC',
						'recursive' => -1
					));

					if ($dealer_balance_info) {
						if ($dealer_balance_info['DistDistributorBalanceHistory']['balance'] < 0) {
							$balance = $dealer_limit + ($dealer_balance_info['DistDistributorBalanceHistory']['balance'] - $this->request->data['Order']['credit_amount']);
						} else {
							$balance = $dealer_limit - $dealer_balance_info['DistDistributorBalanceHistory']['balance'] - $this->request->data['Order']['credit_amount'];
						}
					} else {
						$balance = $dealer_limit;
					}
					//pr($balance);die();
					if ($office_parent_id == 0) {
						$company_id = $this->request->data['OrderProces']['company_id'];
					} else {
						$company_id  = $this->Session->read('Office.company_id');
					}

					$dealer_balance_data = array();
					$dealer_balance_data['dist_distributor_id'] = $dealer_limit_info['DistDistributorBalance']['dist_distributor_id'];
					$dealer_balance_data['company_id'] = $company_id;
					$dealer_balance_data['office_id'] = $this->request->data['OrderProces']['office_id'];
					$dealer_balance_data['memo_value'] = $this->request->data['Order']['gross_value'];
					$dealer_balance_data['balance'] = $balance;

					$dealer_balance_data['transaction_amount'] = $this->request->data['Order']['credit_amount'];
					$dealer_balance_data['transaction_date'] = date('Y-m-d');
					$dealer_balance_data['is_execute'] = $is_execute;

					$dealer_balance_data['created_at'] = $this->current_datetime();
					$dealer_balance_data['created_by'] = $this->UserAuth->getUserId();
					$dealer_balance_data['updated_at'] = $this->current_datetime();
					$dealer_balance_data['updated_by'] = $this->UserAuth->getUserId();

					$this->DistDistributorBalanceHistory->create();

					$this->DistDistributorBalanceHistory->save($dealer_balance_data);
				}
			}
			//end for distributer limit 

			$sales_person = $this->SalesPerson->find('list', array(
				'conditions' => array('territory_id' => $this->request->data['OrderProces']['territory_id']),
				'order' => array('name' => 'asc')
			));
			//pr($this->request->data);die();
			$this->request->data['OrderProces']['sales_person_id'] = key($sales_person);

			$this->request->data['OrderProces']['entry_date'] = date('Y-m-d', strtotime($this->request->data['OrderProces']['entry_date']));
			$this->request->data['OrderProces']['order_date'] = date('Y-m-d', strtotime($this->request->data['OrderProces']['order_date']));

			$orderData['id'] = $order_id;
			$orderData['office_id'] = $this->request->data['OrderProces']['office_id'];

			$orderData['territory_id'] = $this->request->data['OrderProces']['territory_id'];
			$orderData['market_id'] = $market_id;
			//$orderData['market_id'] = $this->request->data['OrderProces']['market_id'];
			//$orderData['outlet_id'] = $this->request->data['OrderProces']['outlet_id'];
			$orderData['outlet_id'] = $this->request->data['OrderProces']['distribut_outlet_id'];
			$orderData['entry_date'] = $this->request->data['OrderProces']['entry_date'];
			$orderData['order_date'] = $this->request->data['OrderProces']['order_date'];
			$orderData['order_no'] = $this->request->data['OrderProces']['order_no'];
			$orderData['gross_value'] = $this->request->data['Order']['gross_value'];
			$orderData['cash_recieved'] = $this->request->data['Order']['cash_recieved'];
			$orderData['credit_amount'] = $this->request->data['Order']['credit_amount'];
			$orderData['w_store_id'] = $this->request->data['OrderProces']['w_store_id'];
			$orderData['is_active'] = $this->request->data['OrderProces']['is_active'];
			$orderData['status'] = $this->request->data['OrderProces']['status'];
			$orderData['confirm_status'] = $this->request->data['OrderProces']['confirm_status'];
			//$orderData['manage_draft_status'] = $this->request->data['OrderProces']['manage_draft_status'];
			//$orderData['order_time'] = $this->current_datetime(); 
			$orderData['order_time'] = $this->request->data['OrderProces']['entry_date'];


			$orderData['order_reference_no'] = $this->request->data['OrderProces']['order_reference_no'];

			$orderData['instrument_reference_no'] = $this->request->data['Order']['reference_number'];

			$orderData['instrument_type'] = $this->request->data['OrderProces']['instrumentType_id'];

			$orderData['sales_person_id'] = $this->request->data['OrderProces']['sales_person_id'];
			$orderData['from_app'] = 0;
			$orderData['action'] = 1;
			$orderData['confirmed'] = 1;


			if ($office_parent_id == 0) {
				$company_id = $this->request->data['OrderProces']['company_id'];
			} else {
				$company_id  = $this->Session->read('Office.company_id');
			}
			$orderData['company_id'] = $company_id;


			$orderData['order_reference_no'] = $this->request->data['OrderProces']['order_reference_no'];


			$orderData['created_at'] = $this->current_datetime();
			$orderData['created_by'] = $this->UserAuth->getUserId();
			$orderData['updated_at'] = $this->current_datetime();
			$orderData['updated_by'] = $this->UserAuth->getUserId();


			$orderData['office_id'] = $office_id ? $office_id : 0;
			$orderData['thana_id'] = $thana_id ? $thana_id : 0;

			$new_orderdata['id'] = $orderData['id'];
			// $new_orderdata['is_closed']=$orderData['is_closed'];
			$new_orderdata['status'] = $orderData['status'];            //$this->Order->create();
			//$new_orderdata['manage_draft_status']=$orderData['manage_draft_status'];            //$this->Order->create();
			$new_orderdata['confirm_status'] = $orderData['confirm_status'];          //$this->Order->create();
			//pr($new_orderdata);die();
			//if ($this->Order->save($orderData)) {
			if ($this->Order->save($new_orderdata)) {
				//pr($order_id);die();

				//$order_id = $this->Order->getLastInsertId();


				$order_info_arr = $this->Order->find('first', array(
					'conditions' => array(
						'Order.id' => $order_id
					)
				));
				//pr($order_id);die();
				$this->loadModel('Store');
				$store_id_arr = $this->Store->find('first', array(
					'conditions' => array(
						'Store.office_id' => $order_info_arr['Order']['office_id']
					)
				));

				$store_id = $store_id_arr['Store']['id'];




				if ($order_id) {
					$all_product_id = $this->request->data['OrderDetail']['product_id'];
					if (!empty($this->request->data['OrderDetail'])) {
						$total_product_data = array();
						$order_details = array();
						$order_details['OrderDetail']['order_id'] = $order_id;
						//pr($this->request->data);die();
						foreach ($this->request->data['OrderDetail']['product_id'] as $key => $val) {
							if ($val == NULL) {
								continue;
							}
							$product_id = $order_details['OrderDetail']['product_id'] = $val;
							$order_details['OrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
							$sales_price = $order_details['OrderDetail']['price'] = $this->request->data['OrderDetail']['Price'][$key];
							$sales_qty = $order_details['OrderDetail']['sales_qty'] = $this->request->data['OrderDetail']['sales_qty'][$key];
							$order_details['OrderDetail']['remaining_qty'] = $this->request->data['OrderDetail']['remaining_qty'][$key];
							$order_details['OrderDetail']['deliverd_qty'] = $this->request->data['OrderDetail']['deliverd_qty'][$key];
							$order_details['OrderDetail']['product_price_id'] = $this->request->data['OrderDetail']['product_price_id'][$key];
							$order_details['OrderDetail']['bonus_qty'] = $this->request->data['OrderDetail']['bonus_product_qty'][$key];

							$product_price_slab_id = 0;
							if ($sales_price > 0) {
								$product_price_slab_id = $this->get_product_price_id($val, $sales_price, $all_product_id);
								// pr($product_price_slab_id);exit;
							}
							$order_details['OrderDetail']['product_price_id'] = $product_price_slab_id['product_price_id'];
							$order_details['OrderDetail']['product_combination_id'] = $product_price_slab_id['combination_id'];
							$order_details['OrderDetail']['bonus_qty'] = $this->request->data['OrderDetail']['bonus_product_qty'][$key];


							if ($this->request->data['OrderDetail']['bonus_product_id'][$key] != 0) {
								$order_details['OrderDetail']['bonus_product_id'] = $this->request->data['OrderDetail']['bonus_product_id'][$key];
							} else {
								$order_details['OrderDetail']['bonus_product_id'] = NULL;
							}

							//Start for bonus
							$order_date = date('Y-m-d', strtotime($this->request->data['OrderProces']['order_date']));
							$bonus_product_id = $this->request->data['OrderDetail']['bonus_product_id'];
							$bonus_product_qty = $this->request->data['OrderDetail']['bonus_product_qty'];
							$order_details['OrderDetail']['bonus_id'] = 0;
							$order_details['OrderDetail']['bonus_scheme_id'] = 0;
							if ($bonus_product_qty[$key] > 0) {
								//echo $bonus_product_id[$key].'<br>';
								$b_product_id = $bonus_product_id[$key];
								$bonus_result = $this->bouns_and_scheme_id_set($b_product_id, $order_date);
								$order_details['OrderDetail']['bonus_id'] = $bonus_result['bonus_id'];
							}
							//End for bouns


							//pr($product_id);die();
							//foreach ($OrderDetail_record as $value) {

							$new_order_details['OrderDetail'] = $OrderDetail_record[$product_id];
							$deliverd_qty = $OrderDetail_record[$product_id]['deliverd_qty'];

							$new_order_details['OrderDetail']['remaining_qty'] = $order_details['OrderDetail']['remaining_qty'];
							$new_order_details['OrderDetail']['deliverd_qty'] = $order_details['OrderDetail']['deliverd_qty'] + $deliverd_qty;
							$new_total_product_data[] = $new_order_details;
							//}
							/* pr($order_details);
							   pr($new_order_details);die();*/
							$total_product_data[] = $order_details;

							if ($bonus_product_qty[$key] > 0) {
								$order_details_bonus['OrderDetail']['order_id'] = $order_id;
								$order_details_bonus['OrderDetail']['is_bonus'] = 1;
								$order_details_bonus['OrderDetail']['product_id'] = $product_id;
								$order_details_bonus['OrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
								$order_details_bonus['OrderDetail']['price'] = 0.0;
								$order_details_bonus['OrderDetail']['sales_qty'] = $this->request->data['OrderDetail']['bonus_product_qty'][$key];
								$total_product_data[] = $order_details_bonus;
								unset($order_details_bonus);
								if (array_key_exists('save', $this->request->data)) {
									$stock_hit = 1;
									if ($stock_hit) {
										$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
										$product_list = Set::extract($products, '{n}.Product');
										$bonus_qty = $this->request->data['MemoDetail']['bonus_product_qty'][$key];
										$punits_pre = $this->search_array($product_id, 'id', $product_list);
										if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
											$base_quantity = $bonus_qty;
										} else {
											$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $bonus_qty);
										}

										$update_type = 'deduct';
										$this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 11, $this->request->data['OrderProces']['order_date']);
									}
								}
							}
							if (array_key_exists('save', $this->request->data)) {
								$stock_hit = 1;
								if ($stock_hit) {
									$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
									$product_list = Set::extract($products, '{n}.Product');

									$punits_pre = $this->search_array($product_id, 'id', $product_list);
									if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
										$base_quantity = $sales_qty;
									} else {
										$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
									}

									$update_type = 'deduct';
									$this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 11, $this->request->data['OrderProces']['order_date']);
								}
							}

							$tempOrderDetails['TempOrderDetail'] = $order_details['OrderDetail'];
							$temp_total_product_data[] = $tempOrderDetails;
						}
						if (array_key_exists('draft', $this->request->data)) {
							$this->loadModel('TempOrderDetail');
							$this->TempOrderDetail->create();
							$this->TempOrderDetail->saveAll($temp_total_product_data);
						}
						//pr($new_total_product_data);die();    
						$this->OrderDetail->saveAll($new_total_product_data);
					}
				}

				/******************* Memo create date: 04-09-2019 ***************************/
				/*********************Memo Create for no Confirmation Type *************************/
				$this->loadModel('Memo');
				$this->loadModel('OrderDetail');
				$this->loadModel('Order');
				$this->loadModel('Collection');
				/***************Delete Data from temp Table 05-09-2019**************************/
				$this->loadModel('TempOrderDetail');
				$this->TempOrderDetail->deleteAll(array('TempOrderDetail.order_id' => $order_id));
				// $this->admin_delete($order_id, 0);
				/***************end  Delete Data from temp Table 05-09-2019**************************/
				$order_info = $this->Order->find('first', array(
					'conditions' => array('Order.id' => $order_id),
					'recursive' => -1
				));

				$order_detail_info = $this->OrderDetail->find('all', array(
					'conditions' => array('OrderDetail.order_id' => $order_id),
					'recursive' => -1
				));

				//pr($order_info);
				//pr($order_detail_info);
				//exit;

				$memo = array();
				$memo['sales_person_id'] = $order_info['Order']['sales_person_id'];
				$memo['entry_date'] = date('Y-m-d');
				$memo['memo_date'] = date('Y-m-d');

				$memo['office_id'] = $order_info['Order']['office_id'];
				$memo['sale_type_id'] = 1;
				$memo['territory_id'] = $order_info['Order']['territory_id'];
				$memo['thana_id'] = $order_info['Order']['thana_id'];
				$memo['market_id'] = $order_info['Order']['market_id'];
				$memo['outlet_id'] = $order_info['Order']['outlet_id'];

				$memo['memo_date'] = $order_info['Order']['order_date'];
				$memo['memo_no'] = $order_info['Order']['order_no'];
				$memo['gross_value'] = $order_info['Order']['gross_value'];
				$memo['cash_recieved'] = $order_info['Order']['cash_recieved'];
				$memo['credit_amount'] = $order_info['Order']['credit_amount'];
				$memo['is_active'] = $order_info['Order']['is_active'];
				$memo['w_store_id'] = $order_info['Order']['w_store_id'];
				if (array_key_exists('save', $this->request->data)) {
					$memo['status'] = 2;
					$this->Memo->create();
				} else {
					$memo['status'] = 0;
				}
				$memo['memo_time'] = $this->current_datetime();
				$memo['sales_person_id'] = $order_info['Order']['sales_person_id'];
				$memo['from_app'] = 0;
				$memo['action'] = 1;
				$memo['is_program'] = 0;
				$memo['company_id'] = $order_info['Order']['company_id'];

				$memo['memo_reference_no'] = $order_info['Order']['memo_reference_no'];

				$memo['created_at'] = $this->current_datetime();
				$memo['created_by'] = $this->UserAuth->getUserId();
				$memo['updated_at'] = $this->current_datetime();
				$memo['updated_by'] = $this->UserAuth->getUserId();

				//pr($memo);
				//exit;

				//$this->Memo->create();

				if ($this->Memo->save($memo)) {

					$memo_id = $this->Memo->getLastInsertId();
					$memo_info_arr = $this->Memo->find('first', array(
						'conditions' => array(
							'Memo.id' => $memo_id
						)
					));


					//pr($memo_info_arr);


					if ($memo_id) {
						if (!empty($order_detail_info[0]['OrderDetail'])) {
							//pr($order_detail_info[0]);
							$this->loadModel('MemoDetail');
							$total_product_data = array();
							$memo_details = array();
							$memo_details['MemoDetail']['memo_id'] = $memo_id;

							foreach ($order_detail_info as $order_detail_result) {
								//pr($order_detail_result);
								$product_id = $order_detail_result['OrderDetail']['product_id'];
								$memo_details['MemoDetail']['product_id'] = $product_id;
								$memo_details['MemoDetail']['measurement_unit_id'] = $order_detail_result['OrderDetail']['measurement_unit_id'];
								$memo_details['MemoDetail']['price'] = $order_detail_result['OrderDetail']['price'];
								$memo_details['MemoDetail']['sales_qty'] = $order_detail_result['OrderDetail']['sales_qty'];

								$memo_details['MemoDetail']['product_price_id'] = $order_detail_result['OrderDetail']['product_price_id'];
								$memo_details['MemoDetail']['bonus_qty'] = $order_detail_result['OrderDetail']['bonus_qty'];
								$memo_details['MemoDetail']['offer_id'] = $order_detail_result['OrderDetail']['offer_id'];
								$memo_details['MemoDetail']['bonus_product_id'] = $order_detail_result['OrderDetail']['bonus_product_id'];
								$memo_details['MemoDetail']['bonus_id'] = $order_detail_result['OrderDetail']['bonus_id'];
								$memo_details['MemoDetail']['bonus_scheme_id'] = $order_detail_result['OrderDetail']['bonus_scheme_id'];
								$memo_details['MemoDetail']['price_combination_id'] = $order_detail_result['OrderDetail']['price_combination_id'];
								$memo_details['MemoDetail']['product_combination_id'] = $order_detail_result['OrderDetail']['product_combination_id'];
								$memo_details['MemoDetail']['is_bonus'] = $order_detail_result['OrderDetail']['is_bonus'];
								$memo_details['MemoDetail']['deliverd_qty'] = $order_detail_result['OrderDetail']['deliverd_qty'];

								//pr($order_details);
								$total_product_data[] = $memo_details;
							}
							// pr($total_product_data);die();
							$this->MemoDetail->saveAll($total_product_data);
						}
					}


					//start collection crate
					$collection_data = array();
					$collection_data['memo_id'] = $memo_info_arr['Memo']['id'];
					$collection_data['memo_no'] = $memo_info_arr['Memo']['memo_no'];
					$collection_data['so_id'] = $memo_info_arr['Memo']['sales_person_id'];

					$collection_data['memo_value'] = $memo_info_arr['Memo']['gross_value'];
					$collection_data['credit_or_due'] = $memo_info_arr['Memo']['credit_amount'];
					$collection_data['memo_date'] = $memo_info_arr['Memo']['memo_date'];
					$collection_data['type'] = 1;
					$collection_data['instrument_type'] = $order_info['Order']['instrument_type'];
					$collection_data['collectionAmount'] = $memo_info_arr['Memo']['gross_value'];
					$collection_data['collectionDate'] = date('Y-m-d');
					$collection_data['created_at'] = $this->current_datetime();

					$collection_data['territory_id'] = $memo_info_arr['Memo']['territory_id'];
					$collection_data['outlet_id'] = $memo_info_arr['Memo']['outlet_id'];
					$collection_data['market_id'] = $memo_info_arr['Memo']['market_id'];
					$collection_data['office_id'] = $memo_info_arr['Memo']['office_id'];

					//pr($collection_data);
					//exit;

					$this->Collection->save($collection_data);

					//end collection careate    

				}

				/*******************************End Memo********************************************/
				/******************* Memo Create end date: 04-09-2019************************/
				/************* create Challan *************/
				if (array_key_exists('save', $this->request->data)) {
					/*****************Create Chalan and *****************/
					$this->loadModel('Challan');
					$this->loadModel('ChallanDetail');
					$this->loadModel('CurrentInventory');
					$company_id  = $this->Session->read('Office.company_id');
					$office_id  = $this->request->data['OrderProces']['office_id'];
					$store_id = $this->request->data['OrderProces']['w_store_id'];
					$challan['company_id'] = $company_id;
					$challan['office_id'] = $office_id;
					$challan['receiver_store_id'] = $store_id;
					$challan['challan_date'] = $this->current_datetime();
					//$challan['product_type']="";
					//$challan['product_id']=;
					//$challan['batch_no']="";
					//$challan['challan_qty']="";
					//$challan['expire_date']="";
					$challan['status'] = 0;
					$challan['transaction_type_id'] = 1;
					$challan['inventory_status_id'] = 1;
					$challan['sender_store_id'] = $store_id;
					$challan['created_at'] = $this->current_datetime();
					$challan['created_by'] = $this->UserAuth->getUserId();
					$challan['updated_at'] = $this->current_datetime();
					$challan['updated_by'] = $this->UserAuth->getUserId();
					//pr();die();
					$this->Challan->create();
					if ($this->Challan->save($challan)) {
						// pr('challan has been saved');//die();
						$udata['id'] = $this->Challan->id;
						$udata['challan_no'] = 'CH' . (10000 + $this->Challan->id);
						$this->Challan->save($udata);
						$product_list = $this->request->data['OrderDetail'];
						if (!empty($product_list['product_id'])) {
							$data_array = array();
							//pr($product_list);
							// pr($store_id);//die();
							foreach ($product_list['product_id'] as $key => $val) {
								if (!empty($val)) {
									$inventories = $this->CurrentInventory->find('first', array(
										'conditions' => array(
											'CurrentInventory.product_id' => $val,
											'CurrentInventory.store_id' => $store_id,
										),
										'recursive' => -1,
									));
									$batch_no = $inventories['CurrentInventory']['batch_number'];

									$data['ChallanDetail']['challan_id'] = $this->Challan->id;
									$data['ChallanDetail']['product_id'] = $val;
									$data['ChallanDetail']['measurement_unit_id'] = $product_list['measurement_unit_id'][$key];
									$data['ChallanDetail']['challan_qty'] = $product_list['sales_qty'][$key];
									$data['ChallanDetail']['batch_no'] = $batch_no;
									//$data['ChallanDetail']['expire_date'] = (($this->request->data['expire_date'][$key] != '' ) ? Date('Y-m-d', strtotime($this->request->data['expire_date'][$key])) : Null);
									$date = (($this->request->data['OrderProces']['order_date'][$key] != ' ' && $this->request->data['OrderProces']['order_date'][$key] != 'null' && $this->request->data['OrderProces']['order_date'][$key] != '') ? explode('-', $this->request->data['OrderProces']['order_date'][$key]) : '');
									if (!empty($date[1])) {
										$date[0] = date('m', strtotime($date[0]));
										$a_date = date('y-m-d', mktime(0, 0, 0, $date[0], 1, $date[1]));
										$data['ChallanDetail']['expire_date'] = date("Y-m-t", strtotime($a_date));
									} else {
										$data['ChallanDetail']['expire_date'] = '';
									}
									$data['ChallanDetail']['inventory_status_id'] = 1;  // set 1 for Sound product
									//$data['ChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
									$data_array[] = $data;
								}
							}
							//pr($data_array);//die();
							$this->ChallanDetail->saveAll($data_array);
						}
					}
				}
				/************* end Challan *************/
			}

			// pr($credit_amount);pr($balance);die();
			if ($maintain_dealer_type == 1) {
				if ($dealer_is_limit_check == 1) {
					if ($credit_amount > $balance) {
						$this->Session->setFlash(__('Please Check the Credit Amount!!!  The Order has been Updated'), 'flash/warning');
					} else {
						$this->Session->setFlash(__($message), 'flash/success');
					}
				}
			}

			//$this->Session->setFlash(__('The Order has been Updated'), 'flash/success');
			if (array_key_exists('save', $this->request->data)) {
				$this->redirect(array('action' => 'delivery', $id));
			} else {
				$this->redirect(array('action' => 'edit', $id));
			}
		}



		$this->loadModel('ProductMeasurement');
		$product_measurement_units = $this->ProductMeasurement->find('list', array('fields' => array('product_id', 'measurement_unit_id')));
		$product_category_id_list = $this->Product->find('list', array('fields' => array('id', 'product_category_id')));


		$this->set(compact('offices', 'territories', 'markets', 'outlets', 'current_date', 'product_list', 'sale_type_list', 'existing_record', 'office_parent_id', 'product_measurement_units', 'product_category_id_list', 'open_bonus_product_option'));
	}

	/*public function admin_get_product()
   {     
		//pr($this->request->data);die();        
		$this->loadModel('Store');
		$this->loadModel('Outlet');
		$this->loadModel('Product');
		$this->loadModel('CurrentInventory');
		$this->loadModel('DistProductCombination');
		$office_id = $this->request->data['office_id'];
		// $outlet_id = $this->request->data['outlet_id'];
		$rs = array(array('id' => '', 'name' => '---- Select Product -----'));
		$store_info = $this->Store->find('first', array(
				'conditions' => array(
				'Store.territory_id' => null,
				'Store.office_id' => $office_id,
				'Store.store_type_id' => 2,          

				),
			'recursive'=>-1    
		));

		
		if(isset($store_info['Store']['id']) && $store_info['Store']['id'])
		{
			$store_id = $store_info['Store']['id'];

			if(isset($this->request->data['csa_id']) && $this->request->data['csa_id']!=0)
			{
				$conditions=array('CurrentInventory.store_id' => $store_id,'inventory_status_id'=>1);
			}
			else
			{
				$conditions=array('CurrentInventory.store_id' => $store_id,'CurrentInventory.qty > ' => 0,'inventory_status_id'=>1);
			}
		   
			if(isset($this->request->data['outlet_id']) && isset($this->request->data['memo_date']))
			{
				$outlet_info = $this->Outlet->find('first', array(
					'conditions' => array(
						'Outlet.id' => $this->request->data['outlet_id']              
						),
					'recursive'=>-1    
					));
				if($outlet_info['Outlet']['category_id']==17)
				{
					$product_combination=$this->DistProductCombination->find('all',array(
						'fields'=>array('DISTINCT DistProductCombination.product_id'),
						'conditions'=>array('DistProductCombination.effective_date <='=>date('Y-m-d',strtotime($this->request->data['memo_date']))),
						'recursive'=>-1
					));
					$product=Set::extract($product_combination,'/DistProductCombination/product_id');
					// pr($product);exit;
					// $conditions['CurrentInventory.product_id']=$product;
					$conditions['OR']=array('CurrentInventory.product_id'=>$product,'Product.product_type_id !='=>1);

				}
			}
			$products_from_ci = $this->CurrentInventory->find('all', array('fields' => array('DISTINCT CurrentInventory.product_id'),
				'conditions' => $conditions,
			));
			
			$product_ci=array();
			foreach ($products_from_ci as $each_ci) {
				$product_ci[]=$each_ci['CurrentInventory']['product_id'];
			}
			
			$product_ci_in=implode(",",$product_ci);        
			$products = $this->Product->find('all', array('conditions' => array('Product.id' => $product_ci),'order' => array('order' => 'asc'),
				 'fields'=>array('Product.id as id','Product.name as name'),
				 'recursive'=>-1
				));
	
			$data_array = Set::extract($products, '{n}.0');
	
			if(!empty($products)){
				echo json_encode(array_merge($rs,$data_array));
			}else{
				echo json_encode($rs);
			} 
		}else{
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}*/
	public function get_order_info()
	{

		$order_id = $this->request->data['order_id'];

		$this->loadModel('ProductCombination');
		$this->loadModel('Combination');
		$this->loadModel('DistCombination');
		$this->loadModel('Product');
		$this->loadModel('Memo');
		$this->loadModel('DistOutletMap');
		$this->loadModel('CurrentInventory');
		$this->loadModel('MeasurementUnit');
		$this->LoadModel('Store');
		$this->loadModel('ProductPrice');
		$this->loadModel('DistProductPrice');
		$this->loadModel('ProductCombination');
		$this->loadModel('DistProductCombination');
		$this->loadModel('Outlet');
		$this->loadModel('DistDistributorBalance');
		$this->loadModel('DistDistributorBalanceHistory');
		$this->loadModel('DistDistributorLimit');
		$this->loadModel('DistDistributorLimitHistory');

		$office_parent_id = $this->UserAuth->getOfficeParentId();

		$current_date = date('d-m-Y', strtotime($this->current_date()));

		$orders = $this->Order->find('first', array('conditions' => array('Order.id' => $order_id)));

		$existing_record = $orders;


		$office_id = $orders['Order']['office_id'];
		$outlet_id = $orders['Order']['outlet_id'];
		$territory_id = $orders['Order']['territory_id'];

		$dist_outlet_mapping_info = $this->DistOutletMap->find('first', array('conditions' => array(
			'DistOutletMap.office_id' => $office_id,
			'DistOutletMap.outlet_id' => $outlet_id,
		)));

		$distributor_id = null;
		if ($dist_outlet_mapping_info) {
			$distributor_id = $dist_outlet_mapping_info['DistOutletMap']['dist_distributor_id'];
			$orders['Order']['dist_distributor_id'] = $distributor_id;
		}

		$this->set(compact('orders'));
		$this->loadModel('Product');

		if ($office_parent_id == 0) {
			/*$product_list = $this->Product->find('list', array(
				'conditions'=>array(
					'is_distributor_product'=> 1,
			),
			'order' => array('order'=>'asc')));*/
			$distributor_conditions = array();
		} else {
			$office_id = $this->UserAuth->getOfficeId();
			/*$product_list = $this->Product->find('list', array(
				'conditions'=>array(
					'is_distributor_product'=> 1,
			),
			'order' => array('order'=>'asc')));*/
			$distributor_conditions = array('DistOutletMap.office_id' => $office_id);
		}

		$this->loadModel('DistProductPrice');
		$product_list_for_distributor = $this->DistProductPrice->find('all', array(
			//'conditions'=>array('DistProductPrice.product_id'=>$product_ci),
		));
		$product_lists = array();
		foreach ($product_list_for_distributor as $val) {
			$product_lists[] = $val['DistProductPrice']['product_id'];
		}

		$product_list = $this->Product->find('list', array(
			'conditions' => array(

				'id' => $product_lists,
				'is_distributor_product' => 1,
			),
			'order' => array('order' => 'asc'),
			//'fields'=>array('Product.id as id','Product.name as name'),
			//'recursive'=>-1
		));
		$distributers_list = $this->DistOutletMap->find('all', array(
			'conditions' => $distributor_conditions,
		));
		foreach ($distributers_list as $key => $value) {
			$distributers[$value['Outlet']['id']] = $value['DistDistributor']['name'];
		}
		$details_data = array();
		$this->loadModel('CombinationDetailsV2');
		foreach ($existing_record['OrderDetail'] as $detail_val) {
			$product = $detail_val['product_id'];
			if ($detail_val['product_combination_id']) {
				$combined_product = $this->CombinationDetailsV2->find('all', array(
					'conditions' => array('CombinationDetailsV2.combination_id' => $detail_val['product_combination_id']),
					'fields' => array('product_id'),
					'recursive' => -1
				));
				$combined_product = array_map(function ($val) {
					return $val['CombinationDetailsV2']['product_id'];
				}, $combined_product);
				$combined_product = implode(',', $combined_product);
				$detail_val['combined_product'] = $combined_product;
			}
			$details_data[] = $detail_val;
		}

		$existing_record['OrderDetail'] = $details_data;
		for ($i = 0; $i < count($details_data); $i++) {
			$measurement_unit_id = $details_data[$i]['measurement_unit_id'];
			if ($measurement_unit_id != 0) {
				$measurement_unit_name = $this->MeasurementUnit->find('all', array(
					'conditions' => array('MeasurementUnit.id' => $measurement_unit_id),
					'fields' => array('name'),
					'recursive' => -1
				));
				$existing_record['OrderDetail'][$i]['measurement_unit_name'] = $measurement_unit_name[0]['MeasurementUnit']['name'];
			}
		}
		$selected_bonus = array();
		$selected_set = array();
		$selected_policy_type = array();
		foreach ($existing_record['OrderDetail'] as $key => $value) {
			$existing_product_category_id_array = $this->Product->find('all', array(
				'conditions' => array('Product.id' => $value['product_id']),
				'fields' => array('product_category_id'),
				'recursive' => -1
			));

			$existing_product_category_id = $existing_product_category_id_array[0]['Product']['product_category_id'];
			if ($value['discount_amount'] && $value['policy_type'] == 3) {
				$selected_policy_type[$value['policy_id']] = 1;
			}
			if ($value['is_bonus'] == 3) {
				if ($value['policy_type'] == 3) {
					$selected_policy_type[$value['policy_id']] = 2;
				}
				if ($value['other_info']) {
					$other_info = json_decode($value['other_info'], 1);
					$selected_set[$value['policy_id']] = $other_info['selected_set'];
					$selected_bonus[$value['policy_id']][$other_info['selected_set']][$value['product_id']] = $value['sales_qty'];
				} else {
					$selected_bonus[$value['policy_id']][1][$value['product_id']] = $value['sales_qty'];
				}
			}
		}
		$this->set(compact('selected_bonus', 'selected_set', 'selected_policy_type'));

		//pr($existing_record['OrderDetail']);
		if (!empty($existing_record['OrderDetail'])) {
			foreach ($existing_record['OrderDetail'] as $key => $value) {
				$OrderDetail_record[$value['product_id']] = $value;
				$stoksinfo = $this->CurrentInventory->find('all', array(
					'conditions' => array(
						'CurrentInventory.store_id' => $existing_record['Order']['w_store_id'],
						'CurrentInventory.product_id' => $value['product_id']
					),
					'fields' => array('sum(qty) as total'),
				));
				$total_qty = $stoksinfo[0][0]['total'];
				$sales_total_qty = $this->unit_convertfrombase($value['product_id'], $value['measurement_unit_id'], $total_qty);
				$existing_record['OrderDetail'][$key]['aso_stock_qty'] = $sales_total_qty;
				$stoks = $this->CurrentInventory->find('first', array(
					'conditions' => array(
						'CurrentInventory.store_id' => $existing_record['Order']['w_store_id'],
						'CurrentInventory.product_id' => $value['product_id']
					),
					'fields' => array('Product.id', 'Product.name', 'CurrentInventory.qty'),
				));
				$productName = $this->Product->find('first', array(
					'conditions' => array('id' => $value['product_id']),
					'fields' => array('id', 'name'),
					'recursive' => -1
				));
			}
		}
		$store_info = $this->Store->find('first', array(
			'conditions' => array(
				'Store.id' => $existing_record['Order']['w_store_id']
			),
			'recursive' => -1
		));
		$open_bonus_product = $this->Product->find('all', array(
			'fields' => array('Product.id', 'Product.name'),
			'joins' => array(
				array(
					'table' => 'current_inventories',
					'alias' => 'CurrentInventory',
					'type' => 'Inner',
					'conditions' => 'CurrentInventory.product_id=Product.id'
				),
				array(
					'table' => 'open_combination_products',
					'alias' => 'OpenCombinationProduct',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.product_id=Product.id'
				),
				array(
					'table' => 'open_combinations',
					'alias' => 'OpenCombination',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.combination_id=OpenCombination.id'
				),

			),
			'conditions' => array(
				'CurrentInventory.qty >' => 0,
				'CurrentInventory.store_id' => $store_info['Store']['id'],
				'OpenCombination.start_date <=' => date('Y-m-d'),
				'OpenCombination.end_date >=' => date('Y-m-d'),
				'OpenCombination.is_bonus' => 1
			),
			'recursive' => -1
		));
		$open_bonus_product_option = array();
		foreach ($open_bonus_product as $bonus_product) {
			$open_bonus_product_option[$bonus_product['Product']['id']] = $bonus_product['Product']['name'];
		}
		//pr($open_bonus_product_option);die();
		$outlets = $distributers;


		$store_id = $store_info['Store']['id'];
		foreach ($existing_record['OrderDetail'] as $key => $single_product) {
			$product_info = $this->Product->find('first', array(
				'conditions' => array('Product.id' => $single_product['product_id']),
				'recursive' => -1
			));
			$existing_record['OrderDetail'][$key]['product_type_id'] = $product_info['Product']['product_type_id'];
			$total_qty_arr = $this->CurrentInventory->find('all', array(
				'conditions' => array('store_id' => $store_id, 'product_id' => $single_product['product_id']),
				'fields' => array('sum(qty) as total'),
				'recursive' => -1
			));

			$total_qty = $total_qty_arr[0][0]['total'];

			$sales_total_qty = $this->unit_convertfrombase($single_product['product_id'], $single_product['measurement_unit_id'], $total_qty);

			$existing_record['OrderDetail'][$key]['stock_qty'] = $sales_total_qty;
		}

		$product_category_id_list = $this->Product->find('list', array('fields' => array('id', 'product_category_id')));

		$measurement_units = $this->MeasurementUnit->find('list');

		$this->set(compact('product_list', 'product_category_id_list', 'measurement_units', 'existing_record', 'open_bonus_product_option'));
	}
	public function get_order_create_details()
	{
		$this->loadModel('InstrumentType');
		$this->loadModel('DistDistributor');
		$this->loadModel('Outlet');
		$this->loadModel('Product');
		$this->loadModel('Office');
		$this->loadModel('Territory');
		$this->loadModel('OfficeWarehouse');
		$this->loadModel('Market');
		$this->loadModel('Store');
		$this->loadModel('Product');
		$maintain_dealer_type = 1;
		$this->set(compact('instrumentType'));
		date_default_timezone_set('Asia/Dhaka');
		$this->set('page_title', 'Create Requisition Order');
		$this->loadModel('OrderDetail');
		$this->loadModel('DistTerritoryMap');
		/* ------ unset cart data ------- */
		$this->Session->delete('cart_session_data');
		$this->Session->delete('matched_session_data');
		$this->Session->delete('combintaion_qty_data');

		$user_id = $this->UserAuth->getUserId();

		$generate_order_no = $user_id . date('d') . date('m') . date('h') . date('i') . date('s');
		$this->set(compact('generate_order_no'));

		$office_parent_id = $this->UserAuth->getOfficeParentId();

		$user_group_id = $this->Session->read('Office.group_id');
		$office_id = $this->Session->read('Office.id');
		$office_type_id = $this->Session->read('Office.office_type_id');

		$outlets = array();
		$Distributors = array();
		$markets = array();
		if ($office_parent_id == 0) {
			$office_conditions = array(
				'office_type_id' => 2,
				"NOT" => array("id" => array(30, 31, 37))
			);
			$office_id = 0;

			$distributers = $this->DistDistributor->find('list', array(
				'order' => array('DistDistributor.name' => 'asc')
			));
			$territories = 0;
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			$office_id = $office_conditions['Office.id'];
			$sales_person_territory_id = $this->Session->read('UserAuth.SalesPerson.territory_id');
			$dist_conditions = array('DistDistributor.office_id' => $office_id);

			/******************This is for all Distributor in this office******************************/
			$Distributors_list = $this->DistDistributor->find('all', array(
				'fields' => array('DistDistributor.id', 'DistDistributor.name'),
				'conditions' => $dist_conditions
			));
			foreach ($Distributors_list as $key => $value) {
				$dist_list[$key] = $value['DistDistributor']['id'];
			}
			$this->loadModel('DistOutletMap');
			$distributers = array();
			$outlets_list = $this->DistOutletMap->find('all', array('conditions' => array('DistOutletMap.dist_distributor_id' => $dist_list)));

			foreach ($outlets_list as $key => $value) {
				$distributers[$value['Outlet']['id']] =  $value['DistDistributor']['name'];
			}
			/********************************** end **************************************************/
			$territories = $sales_person_territory_id;
		}
		$this->set(compact('distributers'));
		$this->set(compact('outlets'));
		$this->set(compact('market_list'));
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('id' => 'asc')));
		/******************Open Bonus Start************************/

		$open_bonus_product_option = array();
		$store_info = $this->Store->find('all', array(
			'conditions' => array(
				'Store.office_id' => array_keys($offices),
				'Store.store_type_id' => 2
			),
			'recursive' => -1
		));
		$store_ids = array();
		foreach ($store_info as $key => $value) {
			$store_ids[$key] = $value['Store']['id'];
		}

		$open_bonus_product = $this->Product->find('all', array(
			'fields' => array('Product.id', 'Product.name'),
			'joins' => array(
				array(
					'table' => 'current_inventories',
					'alias' => 'CurrentInventory',
					'type' => 'Inner',
					'conditions' => 'CurrentInventory.product_id=Product.id'
				),
				array(
					'table' => 'open_combination_products',
					'alias' => 'OpenCombinationProduct',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.product_id=Product.id'
				),
				array(
					'table' => 'open_combinations',
					'alias' => 'OpenCombination',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.combination_id=OpenCombination.id'
				),

			),
			'conditions' => array(
				'CurrentInventory.qty >' => 0,
				'CurrentInventory.store_id' => $store_ids,
				'OpenCombination.start_date <=' => date('Y-m-d'),
				'OpenCombination.end_date >=' => date('Y-m-d'),
				'OpenCombination.is_bonus' => 1
			),
			'recursive' => -1
		));

		$open_bonus_product_option = array();
		foreach ($open_bonus_product as $bonus_product) {
			$open_bonus_product_option[$bonus_product['Product']['id']] = $bonus_product['Product']['name'];
		}
		/******************Open Bonus End***********************/
		$this->set(compact('offices'));
		$this->set(compact('territories'));

		$stock_validation = 1;
		$this->set(compact('stock_validation'));

		$territory_id = 0;
		$market_id = 0;
		$outlets = array();


		$markets = $this->Market->find('list', array(
			'conditions' => array('territory_id' => $territory_id),
			'order' => array('name' => 'asc')
		));

		if ($market_id) {
			$outlets = $this->Outlet->find('list', array(
				'conditions' => array('market_id' => $market_id),
				'order' => array('name' => 'asc')
			));
		}


		$current_date = date('d-m-Y', strtotime($this->current_date()));

		/* ----- start code of product list ----- */
		/*$product_list = $this->Product->find('list', array('conditions'=>array('is_distributor_product'=> 1),'order' => array('order'=>'asc')));*/

		$this->loadModel('DistProductPrice');
		$product_list_for_distributor = $this->DistProductPrice->find('all', array(
			//'conditions'=>array('DistProductPrice.product_id'=>$product_ci),
		));
		$product_lists = array();
		foreach ($product_list_for_distributor as $val) {
			$product_lists[] = $val['DistProductPrice']['product_id'];
		}

		$product_list = $this->Product->find('list', array(
			'conditions' => array(

				'id' => $product_lists,
				'is_distributor_product' => 1,
			),
			'order' => array('order' => 'asc'),
			//'fields'=>array('Product.id as id','Product.name as name'),
			//'recursive'=>-1
		));
		$this->set(compact('offices', 'product_list', 'current_date', 'sale_type_list', 'office_parent_id', 'sales_person', 'open_bonus_product_option'));
	}

	private function stock_check($store_id, $product_id, $qty)
	{

		//----------------product expire last date-----------\\
		$this->loadModel('ProductMonth');

		$product_expire_month_info = $this->ProductMonth->find('first', array(
			'conditions' => array(
				'ProductMonth.product_id' => $product_id
			),
			'fields' => array('ProductMonth.day_month'),
			'recursive' => -1
		));
		if (empty($product_expire_month_info)) {
			$productExpireLimit = 0;
		} else {
			$productExpireLimit = $product_expire_month_info['ProductMonth']['day_month'];
		}

		$p_expire_date = date('Y-m-t', strtotime("+" . $productExpireLimit . " months"));

		//--------------end-------------\\

		$this->loadModel('CurrentInventory');
		$current_inventory = $this->CurrentInventory->find('all', array(
			'conditions' => array(
				'CurrentInventory.store_id' => $store_id,
				'CurrentInventory.product_id' => $product_id,
				//"(CurrentInventory.expire_date is null OR CurrentInventory.expire_date > '$p_expire_date' )",

			),
			'joins' => array(
				array(
					'table' => 'product_measurements',
					'alias' => 'ProductMeasurement',
					'type' => 'LEFT',
					'conditions' => 'ProductMeasurement.product_id=Product.id AND ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id'
				)
			),
			'group' => array('CurrentInventory.store_id', 'ProductMeasurement.qty_in_base', 'CurrentInventory.product_id HAVING (sum(CurrentInventory.qty))  >= ROUND((case when ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end) *' . $qty . ',0)'),
			'fields' => array('CurrentInventory.store_id', 'CurrentInventory.product_id', 'sum(CurrentInventory.qty) as qty')
		));

		//pr($current_inventory);
		//echo $this->DistCurrentInventory->getLastQuery();exit;

		if (!$current_inventory) {
			return false;
		}

		return true;
	}
	public function getProductPrice($product_id, $challan_date)
	{
		$this->LoadModel('ProductPrice');
		$product_prices = $this->ProductPrice->find('first', array(
			'conditions' => array(
				'ProductPrice.product_id' => $product_id,
				'ProductPrice.effective_date <=' => $challan_date,
				'ProductPrice.has_combination' => 0,
				'OR' => array('ProductPrice.project_id is null', 'ProductPrice.project_id' => 0),
			),
			'order' => array('ProductPrice.effective_date DESC'),
			'recursive' => -1

		));
		$this->autoRender = false;
		//pr($product_prices);exit;
		return $product_prices['ProductPrice'];
	}

	public function get_remarks()
	{
		$min_qty = $this->request->data['min_qty'];
		$product_id = $this->request->data['product_id'];
		$this->loadModel('Product');
		$products_info = $this->Product->find('first', array('conditions' => array('Product.id' => $product_id), 'recursive' => -1));
		$measurement_unit_id = $products_info['Product']['sales_measurement_unit_id'];

		$base_qty = $this->unit_convert($product_id, $measurement_unit_id, $min_qty);
		$cartoon_qty = $this->cartoon_convertfrombase($product_id, 16, $base_qty);

		$cartoon = explode('.', $cartoon_qty);
		$cartoon_qty = $cartoon[0];
		if ($cartoon[1] != '00' && $cartoon[1]) {
			$this->loadModel('MeasurementUnit');
			$meauserment_unit = $this->MeasurementUnit->find('first', array(
				'conditions' => array(
					'MeasurementUnit.id' => $measurement_unit_id
				),
				'recursive' => -1
			));
			$measurement_unit_name = $meauserment_unit['MeasurementUnit']['name'];
			if (strlen($measurement_unit_name) > 4) {
				$measurement_unit_name = substr($measurement_unit_name, 0, 4) . '.';
			}
			$base_qty = $this->unit_convert($product_id, 16, '.' . $cartoon[1]);
			$dispenser = $this->unit_convertfrombase($product_id, $measurement_unit_id, $base_qty);
		}
		$result_data = array();
		$result_data['remarks'] = '';
		if ($cartoon_qty)
			$result_data['remarks'] .= $cartoon_qty . " S/c";
		if (isset($dispenser)) {
			$result_data['remarks'] .= ($result_data['remarks'] ? ', ' : '') . $dispenser . " $measurement_unit_name";
		}
		echo json_encode($result_data);
		$this->autoRender = false;
	}

	public function cartoon_convertfrombase($product_id = '', $measurement_unit_id = '', $qty = '')
	{
		$this->loadModel('ProductMeasurement');
		$unit_info = $this->ProductMeasurement->find('first', array(
			'conditions' => array(
				'ProductMeasurement.product_id' => $product_id,
				'ProductMeasurement.measurement_unit_id' => $measurement_unit_id
			)
		));
		$number = $qty;
		if (!empty($unit_info)) {
			$number = $qty / $unit_info['ProductMeasurement']['qty_in_base'];
			return $number;
		} else {
			return $number;
		}
	}



	public function get_remarks_product_id_munit_id($product_id, $unit_id, $min_qty)
	{
		$this->loadModel('Product');
		$products_info = $this->Product->find('first', array('conditions' => array('Product.id' => $product_id), 'recursive' => -1));
		$measurement_unit_id = $products_info['Product']['sales_measurement_unit_id'];
		$base_measurement_unit_id = $products_info['Product']['base_measurement_unit_id'];

		$base_qty = $this->convert_unit_to_unit($product_id, $unit_id, $base_measurement_unit_id, $min_qty);
		$cartoon_qty = $this->unit_convertfrombase($product_id, 16, $base_qty);

		$cartoon = explode('.', $cartoon_qty);
		$cartoon_qty = $cartoon[0];
		if ($cartoon_qty <= 0) {
			$this->loadModel('MeasurementUnit');
			$meauserment_unit = $this->MeasurementUnit->find('first', array(
				'conditions' => array(
					'MeasurementUnit.id' => $measurement_unit_id
				),
				'recursive' => -1
			));
			$measurement_unit_name = $meauserment_unit['MeasurementUnit']['name'];
			if (strlen($measurement_unit_name) > 4) {
				$measurement_unit_name = substr($measurement_unit_name, 0, 4) . '.';
			}
			$dispenser = $this->unit_convertfrombase($product_id, $measurement_unit_id, $base_qty);
		} else {
			if ($cartoon[1] != '00' && $cartoon[1]) {
				$this->loadModel('MeasurementUnit');
				$meauserment_unit = $this->MeasurementUnit->find('first', array(
					'conditions' => array(
						'MeasurementUnit.id' => $measurement_unit_id
					),
					'recursive' => -1
				));
				$measurement_unit_name = $meauserment_unit['MeasurementUnit']['name'];
				if (strlen($measurement_unit_name) > 4) {
					$measurement_unit_name = substr($measurement_unit_name, 0, 4) . '.';
				}
				$base_qty = $this->unit_convert($product_id, 16, '.' . $cartoon[1]);
				$dispenser = $this->unit_convertfrombase($product_id, $measurement_unit_id, $base_qty);
			}
		}

		$result_data = array();
		$result_data['remarks'] = '';
		if ($cartoon_qty)
			$result_data['remarks'] .= $cartoon_qty . " S/c";
		if (isset($dispenser)) {
			$result_data['remarks'] .= ($result_data['remarks'] ? ', ' : '') . $dispenser . " $measurement_unit_name";
		}
		$this->autoRender = false;
		return $result_data['remarks'];
	}
	public function get_product_price()
	{
		$this->LoadModel('ProductCombinationsV2');
		$this->LoadModel('CombinationsV2');
		$this->LoadModel('CombinationDetailsV2');
		$order_date = $this->request->data['order_date'];
		$product_id = $this->request->data['product_id'];
		$min_qty = $this->request->data['min_qty'];
		$product_wise_cart_qty = $this->request->data['cart_product'];
		$prev_combine_product = explode(',', $this->request->data['combined_product']);

		/*------------- min price slab finding ----------------------*/
		if ($min_qty < 1) {
			$min_qty = 1;
		}
		$slab_conditions = array();
		$slab_conditions['ProductCombinationsV2.effective_date <='] = date('Y-m-d', strtotime($order_date));
		$slab_conditions['ProductCombinationsV2.product_id'] = $product_id;
		$slab_conditions['ProductCombinationsV2.min_qty <='] = $min_qty;
		$price_slab = $this->ProductCombinationsV2->find('first', array(
			'conditions' => $slab_conditions,
			'joins' => array(
				array(
					'table' => 'product_price_section_v2',
					'alias' => 'PriceSection',
					'conditions' => 'PriceSection.id=ProductCombinationsV2.section_id and PriceSection.is_db=1'
				),
				array(
					'table' => 'product_price_db_slabs',
					'alias' => 'DBSlab',
					'conditions' => 'DBSlab.product_combination_id=ProductCombinationsV2.id'
				)
			),
			'fields' => array(
				'ProductCombinationsV2.id',
				'ProductCombinationsV2.effective_date',
				'ProductCombinationsV2.min_qty',
				'ProductCombinationsV2.price',
				'DBSlab.price',
				'DBSlab.discount_amount',
			),
			'order' => array(
				'ProductCombinationsV2.effective_date desc',
				'ProductCombinationsV2.min_qty desc'
			),
			'recursive' => -1
		));
		$product_price_array = array();
		if ($price_slab) {
			$product_price_array['price'] = $price_slab['DBSlab']['price'];
			$product_price_array['price_id'] = $price_slab['ProductCombinationsV2']['id'];
			$product_price_array['total_value'] = sprintf('%.2f', $price_slab['DBSlab']['price'] * $product_wise_cart_qty[$product_id]);
			$product_price_array['combine_product'] = '';
			$combine_product = array();
			$combine_product[] = $product_id;

			sort($combine_product);
			sort($prev_combine_product);
			$product_price_array['recall_product_for_price'] = array_udiff($prev_combine_product, $combine_product, function ($a, $b) {
				if ($a != $b) return 1;
			});
			$combination_conditions = array();


			$combinatios_data_check = $this->CombinationsV2->query(
				"
								select
									t.id,
									t.effective_date,
									t.combined_qty
								from
								(
									select 
										pcl.id,
										pcl.effective_date,
										max(pcl.effective_date) over (partition by pcl.combined_qty) as max_effective_date,
										pcl.combined_qty
									from 
									combinations_v2 pcl
									inner join combination_details_v2 pcld on pcl.id=pcld.combination_id
									inner join product_combinations_v2 pc on pc.min_qty=pcl.combined_qty 
																			and pc.product_id=pcld.product_id 
																			and pc.effective_date='" . $price_slab['ProductCombinationsV2']['effective_date'] . "'
									inner join product_price_section_v2 pcs on pcs.id=pc.section_id
									where
									pcl.effective_date <='" . date('Y-m-d', strtotime($order_date)) . "'
									and pcld.product_id=$product_id
									and pcs.is_db=1
									group by 
										pcl.id,
										pcl.effective_date,
										pcl.combined_qty
								)t
								where t.max_effective_date=t.effective_date
								 
						"
			);
			$combination_product_array = array();
			if ($combinatios_data_check) {
				foreach ($combinatios_data_check as $com_data) {
					$combination_details_conditions = array();
					$combination_details_conditions['CombinationDetailsV2.combination_id'] = $com_data['0']['id'];
					$combination_details = $this->CombinationDetailsV2->find('all', array(
						'conditions' => $combination_details_conditions,
						'joins' => array(
							array(
								'table' => 'product_combinations_v2',
								'alias' => 'PC',
								'type' => 'INNER',
								'conditions' => 'PC.product_id=CombinationDetailsV2.product_id 
												and pc.min_qty=' . intval($com_data['0']['combined_qty']) .
									'and pc.effective_date=\'' . $price_slab['ProductCombinationsV2']['effective_date'] . '\''
							),
							array(
								'table' => 'product_price_db_slabs',
								'alias' => 'DBSlab',
								'conditions' => 'DBSlab.product_combination_id=PC.id'
							)
						),
						'group' => array(
							'CombinationDetailsV2.product_id',
							'PC.id',
							'DBSlab.price',
						),
						'fields' => array(
							'CombinationDetailsV2.product_id',
							'PC.id',
							'DBSlab.price',
						),
						'recursive' => -1
					));
					$combined_cart_qty = 0;
					$price_id = 0;
					$price = 0;
					foreach ($combination_details as $details_data) {
						$combined_cart_qty += isset($product_wise_cart_qty[$details_data['CombinationDetailsV2']['product_id']])
							? $product_wise_cart_qty[$details_data['CombinationDetailsV2']['product_id']] : 0;
						if ($product_id == $details_data['CombinationDetailsV2']['product_id']) {
							$price_id = $details_data['PC']['id'];
							$price = $details_data['DBSlab']['price'];
						} else {
							if (isset($product_wise_cart_qty[$details_data['CombinationDetailsV2']['product_id']])) {
								$combine_product[] = $details_data['CombinationDetailsV2']['product_id'];
								$combination_product_array[] = array(
									'product_id' => $details_data['CombinationDetailsV2']['product_id'],
									'price' => $details_data['DBSlab']['price'],
									'total_value' => sprintf('%.2f', $details_data['DBSlab']['price'] * $product_wise_cart_qty[$details_data['CombinationDetailsV2']['product_id']]),
									'price_id' => $details_data['PC']['id'],
									'combination_id' => $com_data['0']['id']
								);
							}
						}
					}
					if ($combined_cart_qty >= $com_data['0']['combined_qty']) {
						$product_price_array['price'] = $price;
						$product_price_array['price_id'] = $price_id;
						$product_price_array['total_value'] = sprintf('%.2f', $price * $product_wise_cart_qty[$product_id]);
						$product_price_array['combination'] = $combination_product_array;
						$product_price_array['combine_product'] = implode(",", $combine_product);
						$product_price_array['combination_id'] = $com_data['0']['id'];
						sort($combine_product);
						sort($prev_combine_product);
						$product_price_array['recall_product_for_price'] = array_udiff($prev_combine_product, $combine_product, function ($a, $b) {
							if ($a != $b) return 1;
						});
						break;
					}
				}
			}
		} else {
			$product_price_array['price'] = 0;
			$product_price_array['price_id'] = 0;
			$product_price_array['total_value'] = 0;
			$product_price_array['combination'] = array();
			$product_price_array['combine_product'] = '';
			$product_price_array['recall_product_for_price'] = array();
		}
		/*---------Bonus-----------*/
		$this->loadModel('Bonus');
		$this->loadModel('Product');

		$this->Bonus->recursive = -1;
		$bonus_info = $this->Bonus->find('all', array(
			'conditions' => array(
				'mother_product_id' => $product_id,
				'effective_date <=' => date('Y-m-d', strtotime($order_date)),
				'end_date >=' => date('Y-m-d', strtotime($order_date))
			),
			'fields' => array('mother_product_quantity', 'bonus_product_id', 'bonus_product_quantity')
		));

		if (!empty($bonus_info[0]['Bonus']['mother_product_quantity'])) {
			$mother_product_quantity_bonus = $bonus_info[0]['Bonus']['mother_product_quantity'];
			$result_data['mother_product_quantity_bonus'] = $mother_product_quantity_bonus;
		}

		$no_of_bonus_slap = count($bonus_info);

		if ($no_of_bonus_slap != 0) {
			for ($slap_count = 0; $slap_count < $no_of_bonus_slap; $slap_count++) {
				$bonus_slap['bonus_product_id'][$slap_count] = $bonus_info[$slap_count]['Bonus']['bonus_product_id'];

				$this->Product->recursive = -1;
				$bonus_product_name = $this->Product->find('first', array(
					'conditions' => array('id' => $bonus_slap['bonus_product_id'][$slap_count]),
					'fields' => array('name', 'sales_measurement_unit_id')
				));
				$quantity_slap['mother_product_quantity'][$slap_count] = $bonus_info[$slap_count]['Bonus']['mother_product_quantity'];

				$result_data['bonus_product_id'][$slap_count] = $bonus_info[$slap_count]['Bonus']['bonus_product_id'];
				$result_data['bonus_product_name'][$slap_count] = $bonus_product_name['Product']['name'];
				$result_data['sales_measurement_unit_id'][$slap_count] = $bonus_product_name['Product']['sales_measurement_unit_id'];
				$result_data['bonus_product_quantity'][$slap_count] = $bonus_info[$slap_count]['Bonus']['bonus_product_quantity'];
			}
			for ($i = 0; $i < count($quantity_slap['mother_product_quantity']); $i++) {
				$result_data['mother_product_quantity'][] = array(
					'min' => $i == 0 ? 0 : $quantity_slap['mother_product_quantity'][$i - 1],
					'max' => $quantity_slap['mother_product_quantity'][$i] - 1
				);
			}
		}
		if (isset($result_data)) {
			$product_price_array = array_merge($product_price_array, $result_data);
		}
		echo json_encode($product_price_array);
		$this->autoRender = false;
	}
	public function get_product_policy()
	{
		$this->LoadModel('Store');
		$this->LoadModel('DiscountBonusPolicy');
		$this->LoadModel('DiscountBonusPolicyProduct');
		$this->LoadModel('DiscountBonusPolicyOption');
		$this->LoadModel('DiscountBonusPolicyOptionExclusionInclusionProduct');
		$order_date = date('Y-m-d', strtotime($this->request->data['order_date']));

		$office_id = $this->request->data['office_id'];

		$store_info = $this->Store->find('first', array(
			'conditions' => array(
				'Store.territory_id' => null,
				'Store.office_id' => $office_id,
				'Store.store_type_id' => 2,
			),
			'recursive' => -1
		));
		$store_id = $store_info['Store']['id'];

		$product_id = $this->request->data['product_id'];
		$memo_total = $this->request->data['memo_total'];
		$min_qty = $this->request->data['min_qty'];
		$product_wise_cart_qty = $this->request->data['cart_product'];
		$product_wise_cart_value = $this->request->data['cart_product_value'];
		$old_selected_bonus = json_decode($this->request->data['selected_bonus'], 1);
		$old_selected_set = json_decode($this->request->data['selected_set'], 1);
		$old_selected_policy_type = json_decode($this->request->data['selected_policy_type'], 1);
		$old_other_policy_info = json_decode($this->request->data['other_policy_info'], 1);

		$product_rate_discount = $this->request->data['product_rate_discount'];
		$product_price_id_discount = $this->request->data['product_price_id_discount'];
		
		$conditions = array();
		$conditions['DiscountBonusPolicy.start_date <='] = $order_date;
		$conditions['DiscountBonusPolicy.end_date >='] = $order_date;
		$conditions['DiscountBonusPolicy.is_db'] = 1;
		$conditions['DiscountBonusPolicyOption.is_db'] = 1;
		$conditions['DiscountBonusPolicyProduct.product_id'] = array_keys($product_wise_cart_qty);


	   // echo '<pre>';print_r($conditions);exit;

		$policy_data = $this->DiscountBonusPolicy->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'discount_bonus_policy_products',
					'alias' => 'DiscountBonusPolicyProduct',
					'conditions' => 'DiscountBonusPolicyProduct.discount_bonus_policy_id=DiscountBonusPolicy.id'
				),
				array(
					'table' => 'discount_bonus_policy_options',
					'alias' => 'DiscountBonusPolicyOption',
					'conditions' => 'DiscountBonusPolicyOption.discount_bonus_policy_id=DiscountBonusPolicy.id'
				),
			),
			'group' => array(
				'DiscountBonusPolicy.id',
				'DiscountBonusPolicy.name',
				'DiscountBonusPolicy.start_date',
				'DiscountBonusPolicy.end_date',
			),
			'fields' => array(
				'DiscountBonusPolicy.id',
				'DiscountBonusPolicy.name',
				'DiscountBonusPolicy.start_date',
				'DiscountBonusPolicy.end_date',
			),
			'recursive' => -1,
		));

		

		$policy_id = array_map(function ($data) {
			return $data['DiscountBonusPolicy']['id'];
		}, $policy_data);

		
	   
		$policy_product = $this->DiscountBonusPolicyProduct->find('all', array(
			'conditions' => array('DiscountBonusPolicyProduct.discount_bonus_policy_id' => $policy_id),
			'fields' => array('product_id', 'discount_bonus_policy_id'),
			'recursive' => -1
		));

	   
	   
		$other_policy_info = array();
		$policy_wise_product_data = array();
		foreach ($policy_product as $data) {
			$policy_wise_product_data[$data['DiscountBonusPolicyProduct']['discount_bonus_policy_id']][] = $data['DiscountBonusPolicyProduct']['product_id'];
			if (isset($product_wise_cart_qty[$data['DiscountBonusPolicyProduct']['product_id']]))
				$other_policy_info['policy_product'][$data['DiscountBonusPolicyProduct']['product_id']] =  $data['DiscountBonusPolicyProduct']['product_id'];
		}
	   
		$discount_array = array();
		$total_discount = 0;
		$bonus_html = '';
		$selected_bonus = array();
		$selected_set = array();
		$selected_policy_type = array();
		foreach ($policy_data as $p_data) {
			if (!$policy_wise_product_data[$p_data['DiscountBonusPolicy']['id']]) {
				continue;
			}
			$cart_combined_qty = 0;
			$cart_combined_value = 0;
			foreach ($policy_wise_product_data[$p_data['DiscountBonusPolicy']['id']] as $p_id) {
				$cart_combined_qty += isset($product_wise_cart_qty[$p_id]) ? $product_wise_cart_qty[$p_id] : 0;
				$cart_combined_value += isset($product_wise_cart_value[$p_id]) ? $product_wise_cart_value[$p_id] : 0;
			}
			$policy_option = $this->DiscountBonusPolicyOption->find('all', array(
				'conditions' => array(
					'DiscountBonusPolicyOption.discount_bonus_policy_id' => $p_data['DiscountBonusPolicy']['id'],
					'DiscountBonusPolicyOption.is_db' => 1,
					//'DiscountBonusPolicyOption.min_qty_sale_unit <=' => $cart_combined_qty,
					'( (DiscountBonusPolicyOption.min_qty_sale_unit >0 and   DiscountBonusPolicyOption.min_qty_sale_unit <=' . $cart_combined_qty . ') OR DiscountBonusPolicyOption.min_value <=' . $cart_combined_value . ' )'
				),
				'order' => array('DiscountBonusPolicyOption.min_qty_sale_unit desc'),
				'recursive' => -1
			));
			$effective_slab_index = null;

			foreach ($policy_option as $key => $slab_data) {
				
				$qty_value_flag =  $slab_data['DiscountBonusPolicyOption']['qty_value_flag'];

				if ($qty_value_flag == 0 && ($slab_data['DiscountBonusPolicyOption']['min_qty_sale_unit'] > $cart_combined_qty || $slab_data['DiscountBonusPolicyOption']['min_value'] > $cart_combined_value)) {
					continue;
				}
				
				$min_memo_value = $slab_data['DiscountBonusPolicyOption']['min_memo_value'];
				if ($min_memo_value && $min_memo_value > $memo_total) {
					continue;
				}
				$exclusion_product = $this->DiscountBonusPolicyOptionExclusionInclusionProduct->find('all', array(
					'conditions' => array(
						'DiscountBonusPolicyOptionExclusionInclusionProduct.discount_bonus_policy_option_id' => $slab_data['DiscountBonusPolicyOption']['id'],
						'DiscountBonusPolicyOptionExclusionInclusionProduct.create_for' => 1
					),
					'recursive' => -1
				));
				$is_exclusion = 0;
				foreach ($exclusion_product as $ex_data) {
					$ex_product_id = $ex_data['DiscountBonusPolicyOptionExclusionInclusionProduct']['product_id'];
					$ex_min_qty = $ex_data['DiscountBonusPolicyOptionExclusionInclusionProduct']['min_qty'];
					if ($ex_min_qty) {

						if (isset($product_wise_cart_qty[$ex_product_id]) && $product_wise_cart_qty[$ex_product_id] >= $ex_min_qty) {
							$is_exclusion = 1;
							break;
						}
					} else {
						if (isset($product_wise_cart_qty[$ex_product_id])) {
							$is_exclusion = 1;
							break;
						}
					}
				}
				if ($is_exclusion == 1) {
					continue;
				}
				$inclusion_product = $this->DiscountBonusPolicyOptionExclusionInclusionProduct->find('all', array(
					'conditions' => array(
						'DiscountBonusPolicyOptionExclusionInclusionProduct.discount_bonus_policy_option_id' => $slab_data['DiscountBonusPolicyOption']['id'],
						'DiscountBonusPolicyOptionExclusionInclusionProduct.create_for' => 2
					),
					'recursive' => -1
				));
				$is_inclusion = 1;
				foreach ($inclusion_product as $ex_data) {
					$in_product_id = $ex_data['DiscountBonusPolicyOptionExclusionInclusionProduct']['product_id'];
					$in_min_qty = $ex_data['DiscountBonusPolicyOptionExclusionInclusionProduct']['min_qty'];
					if ($in_min_qty) {

						if (isset($product_wise_cart_qty[$in_product_id]) && $product_wise_cart_qty[$in_product_id] < $in_min_qty) {
							$is_inclusion = 0;
							break;
						}
					} else {
						if (!isset($product_wise_cart_qty[$in_product_id])) {
							$is_inclusion = 0;
							break;
						}
					}
				}
				if ($is_inclusion == 0) {
					continue;
				}
				$effective_slab_index = $key;
				break;
			}
			if ($effective_slab_index === null) {
				continue;
			}

			$policy_type = $policy_option[$effective_slab_index]['DiscountBonusPolicyOption']['policy_type'];
			$policy_id = $p_data['DiscountBonusPolicy']['id'];

			if (isset($old_other_policy_info[$policy_id]) && $old_other_policy_info[$policy_id]['selected_option_id'] != $policy_option[$effective_slab_index]['DiscountBonusPolicyOption']['id']) {
				unset($old_selected_bonus[$policy_id]);
				unset($old_selected_set[$policy_id]);
				unset($old_selected_policy_type[$policy_id]);
			}
			$other_policy_info[$policy_id]['selected_option_id'] = $policy_option[$effective_slab_index]['DiscountBonusPolicyOption']['id'];

			

			if ($policy_type == 0 || $policy_type == 2) {
				$discount_array[] = $this->create_discount_array($policy_option[$effective_slab_index], $product_wise_cart_qty, $total_discount, $product_rate_discount, $product_price_id_discount, $cart_combined_qty);
			}

		   
			if ($policy_type == 1 || $policy_type == 2) {
				$bonus_html .= '<tr class="n_bonus_row"><th colspan="4">' . $p_data['DiscountBonusPolicy']['name'] . '</th><tr>';
				$bonus_html .= $this->create_bonus_html($store_id, $policy_option[$effective_slab_index], $cart_combined_qty, $selected_bonus, $old_selected_bonus, $selected_set, $old_selected_set, $other_policy_info, $old_other_policy_info);
			} else if ($policy_type == 3) {
				$policy_id = $p_data['DiscountBonusPolicy']['id'];
				$selected_policy_type_var = 1;
				if (isset($old_selected_policy_type[$policy_id])) {
					$selected_policy_type_var = $old_selected_policy_type[$policy_id];
				}
				$selected_policy_type[$policy_id] = $selected_policy_type_var;
				if ($selected_policy_type_var == 1) {
					$btn_type_1 = 'btn-primary';
					$btn_type_2 = 'btn-basic';
				} else {
					$btn_type_1 = 'btn-basic';
					$btn_type_2 = 'btn-primary';
				}
				$bonus_html .= '<tr class="n_bonus_row"><th colspan="4">' . $p_data['DiscountBonusPolicy']['name'] . '</th><tr>';
				$bonus_html .=
					'<tr class="n_bonus_row">
					<th colspan="4">
						<button class="btn ' . $btn_type_1 . ' btn_type" data-type="1" data-policy_id="' . $policy_id . '">Discount</button>
						<button class="btn ' . $btn_type_2 . ' btn_type" data-type="2"  data-policy_id="' . $policy_id . '">Bonus</button>
					</th>
				<tr>';
			  
				if ($selected_policy_type_var == 1)
					$discount_array[] = $this->create_discount_array($policy_option[$effective_slab_index], $product_wise_cart_qty, $total_discount,  $product_rate_discount, $product_price_id_discount, $cart_combined_qty);

				else if ($selected_policy_type_var == 2)
					$bonus_html .= $this->create_bonus_html($store_id, $policy_option[$effective_slab_index], $cart_combined_qty, $selected_bonus, $old_selected_bonus, $selected_set, $old_selected_set, $other_policy_info, $old_other_policy_info);
			}
		}
		$result = array();
		$result['discount'] = $discount_array;
		$result['total_discount'] = $total_discount;
		$result['bonus_html'] = $bonus_html;
		$result['selected_bonus'] = $selected_bonus;
		$result['selected_set'] = $selected_set;
		$result['selected_policy_type'] = $selected_policy_type;
		$result['other_policy_info'] = $other_policy_info;
		echo json_encode($result);
		exit;
		$this->autoRender = false;
	}
	private function create_discount_array($policy_option, $product_wise_cart_qty, &$total_discount, $product_rate_discount, $product_price_id_discount, $cart_combined_qty)
	{
		$this->loadModel('DiscountBonusPolicyOptionPriceSlab');
		
		$this->loadModel('DiscountBonusPolicyProduct');


		$policy_id = $policy_option['DiscountBonusPolicyOption']['discount_bonus_policy_id'];

		$policy_product = $this->DiscountBonusPolicyProduct->find('list', array(
			'conditions' => array('DiscountBonusPolicyProduct.discount_bonus_policy_id' => $policy_id),
			'fields' => array('product_id', 'discount_bonus_policy_id'),
			'recursive' => -1
		));

		$conditions = array();
		$conditions['DiscountBonusPolicyOptionPriceSlab.discount_bonus_policy_option_id'] = $policy_option['DiscountBonusPolicyOption']['id'];

		$discount_amount = $policy_option['DiscountBonusPolicyOption']['discount_amount'];
		$discount_in_hand = $policy_option['DiscountBonusPolicyOption']['in_hand_discount_amount'];

		if (!$discount_in_hand) {
			$discount_in_hand = 0;
		}
		
		$discount_amount = $discount_amount - $discount_in_hand;
		$policy_id = $policy_option['DiscountBonusPolicyOption']['discount_bonus_policy_id'];
		$discount_type = $policy_option['DiscountBonusPolicyOption']['disccount_type'];
		$policy_type = $policy_option['DiscountBonusPolicyOption']['policy_type'];
		$deduct_from_value = $policy_option['DiscountBonusPolicyOption']['deduct_from_value'];

		$discount_array = array();

		if ($deduct_from_value == 1) {

			//foreach ($product_price_id_discount as $product_id => $price_id_val) {
			foreach ($policy_product as $product_id => $discount_id) {
				if(!isset($product_price_id_discount[$product_id]))
					continue;
				$price_id_val=$product_price_id_discount[$product_id];
				
				if ($discount_type == 0) {
					$discount_amount_price = ($product_rate_discount[$product_id] * $discount_amount / 100);
				} else {
					$discount_amount_price = $discount_amount / $cart_combined_qty;
				}

				$discount_value = $discount_amount_price * $product_wise_cart_qty[$product_id];
				$total_discount += $discount_value;
				$discount_array[] = array(
					'product_id' => $product_id,
					'policy_id' => $policy_id,
					'policy_type' => $policy_type,
					'discount_type' => $discount_type,
					'discount_amount' => $discount_amount_price,
					'price' => $product_rate_discount[$product_id],
					'total_value' => sprintf("%0.2f", $product_rate_discount[$product_id] * $product_wise_cart_qty[$product_id]),
					'price_id' => $price_id_val,
					'total_discount_value' => sprintf("%0.2f", $discount_value),
				);
			}


		}else{

			$price_slabs = $this->DiscountBonusPolicyOptionPriceSlab->find('all', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'table' => 'product_combinations_v2',
						'alias' => 'PC',
						'type' => 'inner',
						'conditions' => 'PC.id=DiscountBonusPolicyOptionPriceSlab.db_slab_id'
					),
					array(
						'table' => 'product_price_db_slabs',
						'alias' => 'PCD',
						'type' => 'inner',
						'conditions' => 'PC.id=PCD.product_combination_id'
					),
				),
				'fields' => array('PCD.price', 'DiscountBonusPolicyOptionPriceSlab.discount_product_id', 'PC.id'),
				'recursive' => -1
			));


			foreach ($price_slabs as $pr_slab_data) {

				if (isset($product_wise_cart_qty[$pr_slab_data['DiscountBonusPolicyOptionPriceSlab']['discount_product_id']])) {
				
					if ($discount_type == 0)
						$discount_amount_price = ($pr_slab_data['PCD']['price'] * $discount_amount / 100);
					else
						$discount_amount_price = $discount_amount;
					$discount_value = $discount_amount_price * $product_wise_cart_qty[$pr_slab_data['DiscountBonusPolicyOptionPriceSlab']['discount_product_id']];
					$total_discount += $discount_value;
					$discount_array[] = array(
						'product_id' => $pr_slab_data['DiscountBonusPolicyOptionPriceSlab']['discount_product_id'],
						'policy_id' => $policy_id,
						'policy_type' => $policy_type,
						'discount_type' => $discount_type,
						'discount_amount' => $discount_amount_price,
						'price' => $pr_slab_data['PCD']['price'],
						'total_value' => sprintf("%0.2f", $pr_slab_data['PCD']['price'] * $product_wise_cart_qty[$pr_slab_data['DiscountBonusPolicyOptionPriceSlab']['discount_product_id']]),
						'price_id' => $pr_slab_data['PC']['id'],
						'total_discount_value' => sprintf("%0.2f", $discount_value),
					);
				}
			}

		}

		return $discount_array;


	}
	private function create_bonus_html($store_id, $policy_option, $combined_qty, &$selected_bonus, $old_selected_bonus, &$selected_set, $old_selected_set, &$other_policy_info, $old_other_policy_info)
	{

		$this->loadModel('DiscountBonusPolicyOptionBonusProduct');
		$conditions['DiscountBonusPolicyOptionBonusProduct.discount_bonus_policy_option_id'] = $policy_option['DiscountBonusPolicyOption']['id'];
		$bonus_product = $this->DiscountBonusPolicyOptionBonusProduct->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'conditions' => 'Product.id=DiscountBonusPolicyOptionBonusProduct.bonus_product_id'
				),
				array(
					'table' => 'measurement_units',
					'alias' => 'MU',
					'conditions' => 'MU.id=DiscountBonusPolicyOptionBonusProduct.measurement_unit_id'
				),
			),
			'fields' => array(
				'DiscountBonusPolicyOptionBonusProduct.*',
				'Product.name',
				'MU.name',
			),
			'recursive' => -1
		));
		$formula = $policy_option['DiscountBonusPolicyOption']['bonus_formula_text_with_product_id'];
		$min_qty = $policy_option['DiscountBonusPolicyOption']['min_qty_sale_unit'];
		$policy_id = $policy_option['DiscountBonusPolicyOption']['discount_bonus_policy_id'];
		$policy_type = $policy_option['DiscountBonusPolicyOption']['policy_type'];
		$b_html = '';
		if (!$formula) {
			$s_disabled = 'readonly';
			$i = 0;
		   
			foreach ($bonus_product as $data) {
				$bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_qty'];
				$in_hand_bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['in_hand_bonus_qty'];
				if (!$in_hand_bonus_qty)
					$in_hand_bonus_qty = 0;
				$bonus_qty = $bonus_qty - $in_hand_bonus_qty;
				$provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
				// echo $old_other_policy_info[$policy_id]['provided_bonus_qty'] . '----' . $provide_bonus_qty;
				if (isset($old_other_policy_info[$policy_id]) && $old_other_policy_info[$policy_id]['provided_bonus_qty'] != $provide_bonus_qty) {
					unset($old_selected_bonus[$policy_id]);
					unset($old_selected_set[$policy_id]);
				}
				/* echo $old_other_policy_info[$policy_id]['provided_bonus_qty'] . '----' . $provide_bonus_qty;
				exit; */
				$other_policy_info[$policy_id]['provided_bonus_qty'] = $provide_bonus_qty;
				if (isset($old_selected_bonus[$policy_id])) {
					if (isset($old_selected_bonus[$policy_id][1][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']]) && $old_selected_bonus[$policy_id][1][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] > 0) {
						$value = $old_selected_bonus[$policy_id][1][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']];
						$batch_select_disabled = '';
						$min_qty_disabled = '';
						$min_qty_checked = 'checked';
						$min_qty_required = 'required';
					} else {
						$value = 0;
						$min_qty_disabled = 'readonly';
						$batch_select_disabled = 'disabled';
						$min_qty_checked = '';
						$min_qty_required = '';
					}
				} else {
					if ($i == 0) {
						$value = $provide_bonus_qty;
						$min_qty_disabled = '';
						$batch_select_disabled = '';
						$min_qty_checked = 'checked';
						$min_qty_required = 'required';
					} else {
						$value = 0;
						$min_qty_disabled = 'readonly';
						$batch_select_disabled = 'disabled';
						$min_qty_checked = '';
						$min_qty_required = '';
					}
				}

				$prod_info = $this->Product->find('first', array(
					'conditions' => array('id' => $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']),
					'recursive' => -1
				));



				if ($prod_info['Product']['is_virtual'] == 1) {

					$pd_name_replace = $this->get_product_inventroy_check($store_id, $prod_info['Product']['id'], $prod_info['Product']['parent_id']);

					if ($pd_name_replace == 1) {
						$pdname = $this->get_parent_virtual_pd_info($data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']);

						$data['Product']['name'] = $pdname['VirtualProduct']['name'];
					}
				}

				$selected_bonus[$policy_id]['1'][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] = $value;
				$i++;
				$batch_product_id = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'];
				$b_html .= '
					<tr class="bonus_row n_bonus_row bonus_policy_id_' . $policy_id . '">
						<th class="text-center" id="bonus_product_list">
							<div class="input select">
								<select ' . $s_disabled . ' name="data[OrderDetail][product_id][]" class="form-control width_100_this policy_bonus_product_id">
								<option value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">' . $data['Product']['name'] . '</option>
								</select>
							</div>
						</th>
						<th class="text-center" width="12%">
							<input ' . $s_disabled . ' type="text" name="" class="form-control width_100_this open_bonus_product_unit_name" value="' . $data['MU']['name'] . '" readonly disabled="">
							<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="open_bonus_product_unit_id" id="bonus_product_unit_id_'.$policy_id.'_'. $batch_product_id .'" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'] . '">
							<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][discount_amount][]" class="form-control width_100 discount_amount" value="" />
							<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type" value=""/>
							<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][policy_type][]" class="policy_type" value="' . $policy_type . '">
							<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][policy_id][]" class="policy_id" value="' . $policy_id . '">
							<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][is_bonus][]" class="is_bonus" value="3">
							<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100_this open_bonus_product_rate" value="0.0">
							<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100_this open_bonus_product_price_id" value="">
							<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value="">
							<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][selected_set][' . $policy_id . ']" step="any" class="set_id" value="1">
						</th>
						<th class="text-center" width="12%">
							<input step="any"  style="width: 50px;" ' . $min_qty_disabled . ' ' . $min_qty_required . ' data-set="1" type="number" min="1" max="' . $provide_bonus_qty . '" name="data[OrderDetail][sales_qty][]" class="width_100_this policy_min_qty ' . $policy_id . '_policy_set_1 ' . $policy_id . '_qty_validation" value="' . $value . '">&nbsp;
							
							<a  data-policy="'.$policy_id.'" value="'.$batch_product_id.'"  data-toggle="modal"  class="btn btn-primary btn-sm multi_batch_modal remove_select_batch">Batch Select</a>
							
						</th>
						<th class="text-center" width="10%">
							
							<input type="checkbox" ' . $min_qty_checked . ' class="is_bonus_checked" name="data[OrderDetail][bonus_check][]" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">
						</th>
					</tr>';

					


			}
		} else {
			$parsed_fourmula = $this->parse_formla($formula);
			$product_wise_bonus = array();
			foreach ($bonus_product as $data) {
				$product_wise_bonus[$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] = $data;
			}
			if ($parsed_fourmula['set_relation'] == 'AND' || $parsed_fourmula['set_relation'] == '') {
				foreach ($parsed_fourmula['formula_product'] as $set => $formula_product) {
					$b_html .= '<tr class="n_bonus_row set"' . $set . '><th colspan="4">Set - ' . $set . '</th><tr>';
					$element_relation = $parsed_fourmula['element_relation'][$set];
					
					if ($element_relation == 'OR') {
						$i = 0;
						$s_disabled = 'readonly';
						
						foreach ($formula_product as $product_id) {
							$data = $product_wise_bonus[$product_id];
							$bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_qty'];
							$in_hand_bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['in_hand_bonus_qty'];
							
							if (!$in_hand_bonus_qty)
								$in_hand_bonus_qty = 0;
							$bonus_qty = $bonus_qty - $in_hand_bonus_qty;
							$provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
							if ($old_other_policy_info[$policy_id]['provided_bonus_qty'] != $provide_bonus_qty) {
								unset($old_selected_bonus[$policy_id]);
								unset($old_selected_set[$policy_id]);
							}
							
							$other_policy_info[$policy_id]['provided_bonus_qty'] = $provide_bonus_qty;
							if (isset($old_selected_bonus[$policy_id])) {
								
								if (isset($old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']]) && $old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] > 0) {
									
									$value = $old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']];
									$min_qty_disabled = '';
									$min_qty_checked = 'checked';
									$batch_select_disabled = '';
									$min_qty_required = 'required';
									
								} else {
									$value = 0;
									$min_qty_disabled = 'readonly';
									$batch_select_disabled = 'disabled';
									$min_qty_checked = '';
									$min_qty_required = '';
								}
							} else {
								
								if ($i == 0) {
									$value = $provide_bonus_qty;
									$min_qty_disabled = '';
									$min_qty_checked = 'checked';
									$batch_select_disabled = '';
									$min_qty_required = 'required';
								} else {
									$value = 0;
									$min_qty_disabled = 'readonly';
									$batch_select_disabled = 'disabled';
									$min_qty_checked = '';
									$min_qty_required = '';
									
								}
							}

							$prod_info = $this->Product->find('first', array(
								'conditions' => array('id' => $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']),
								'recursive' => -1
							));

							if ($prod_info['Product']['is_virtual'] == 1) {

								$pd_name_replace = $this->get_product_inventroy_check($store_id, $prod_info['Product']['id'], $prod_info['Product']['parent_id']);

								if ($pd_name_replace == 1) {
									$pdname = $this->get_parent_virtual_pd_info($data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']);

									$data['Product']['name'] = $pdname['VirtualProduct']['name'];
								}
							}

							$selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] = $value;
							$batch_product_id = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'];
							$i++;
							$b_html .= '
								<tr class="bonus_row n_bonus_row bonus_policy_id_' . $policy_id . '">
									<th class="text-center" id="bonus_product_list">
										<div class="input select">
											<select ' . $s_disabled . ' name="data[OrderDetail][product_id][]" class="form-control width_100_this policy_bonus_product_id">
											<option value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">' . $data['Product']['name'] . '</option>
											</select>
										</div>
									</th>
									<th class="text-center" width="12%">
										<input ' . $s_disabled . ' type="text" name="" class="form-control width_100_this open_bonus_product_unit_name" value="' . $data['MU']['name'] . '" readonly disabled="">
										<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][measurement_unit_id][]" id="bonus_product_unit_id_'.$policy_id.'_'. $batch_product_id .'" class="open_bonus_product_unit_id" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'] . '">
										<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][discount_amount][]" class="form-control width_100 discount_amount" value="" />
										<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type" value=""/>
										<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][policy_type][]" class="policy_type" value="' . $policy_type . '">
										<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][policy_id][]" class="policy_id" value="' . $policy_id . '">
										<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][is_bonus][]" class="is_bonus" value="3">
										<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100_this open_bonus_product_rate" value="0.0">
										<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100_this open_bonus_product_price_id" value="">
										<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value="">
										<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][selected_set][' . $policy_id . ']" step="any" class="set_id" value="' . $set . '">
									</th>
									<th class="text-center" width="12%">
										<input step="any"  style="width: 50px;" ' . $min_qty_disabled . ' ' . $min_qty_required . ' data-set="' . $set . '" type="number" min="1" max="' . $provide_bonus_qty . '" name="data[OrderDetail][sales_qty][]" class="width_100_this policy_min_qty ' . $policy_id . '_policy_set_' . $set . ' ' . $policy_id . '_qty_validation" value="' . $value . '">
										
										<a data-policy="'.$policy_id.'" value="'.$batch_product_id.'"  data-toggle="modal"  class="btn btn-primary btn-sm multi_batch_modal remove_select_batch">Batch Select</a>
									</th>
									<th class="text-center" width="10%">
										<input type="checkbox" ' . $min_qty_checked . ' class="is_bonus_checked" name="data[OrderDetail][bonus_check][]" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">
									</th>
								</tr>';
						}
					} else if ($element_relation == 'AND') {
						$i = 0;
						$s_disabled = 'readonly';
						foreach ($formula_product as $product_id) {
							$data = $product_wise_bonus[$product_id];
							$bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_qty'];
							$in_hand_bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['in_hand_bonus_qty'];
							if (!$in_hand_bonus_qty)
								$in_hand_bonus_qty = 0;
							$bonus_qty = $bonus_qty - $in_hand_bonus_qty;
							$provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
							if (isset($old_other_policy_info[$policy_id]) && $old_other_policy_info[$policy_id]['provided_bonus_qty'] != $provide_bonus_qty) {
								unset($old_selected_bonus[$policy_id]);
								unset($old_selected_set[$policy_id]);
							}
							$other_policy_info[$policy_id]['provided_bonus_qty'] = $provide_bonus_qty;
							$value = $provide_bonus_qty;
							$min_qty_disabled = 'readonly';
							$min_qty_checked = 'checked';
							$min_qty_required = 'required';
							$batch_select_disabled = '';

							$prod_info = $this->Product->find('first', array(
								'conditions' => array('id' => $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']),
								'recursive' => -1
							));

							if ($prod_info['Product']['is_virtual'] == 1) {

								$pd_name_replace = $this->get_product_inventroy_check($store_id, $prod_info['Product']['id'], $prod_info['Product']['parent_id']);

								if ($pd_name_replace == 1) {
									$pdname = $this->get_parent_virtual_pd_info($data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']);

									$data['Product']['name'] = $pdname['VirtualProduct']['name'];
								}
							}

							$selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] = $value;
							$batch_product_id = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'];
							$i++;
							$b_html .= '
								<tr class="bonus_row n_bonus_row bonus_policy_id_' . $policy_id . '">
									<th class="text-center" id="bonus_product_list">
										<div class="input select">
											<select ' . $s_disabled . ' name="data[OrderDetail][product_id][]" class="form-control width_100_this policy_bonus_product_id">
											<option value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">' . $data['Product']['name'] . '</option>
											</select>
										</div>
									</th>
									<th class="text-center" width="12%">
										<input ' . $s_disabled . ' type="text" name="" class="form-control width_100_this open_bonus_product_unit_name" value="' . $data['MU']['name'] . '" readonly disabled="">
										<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][measurement_unit_id][]" id="bonus_product_unit_id_'.$policy_id.'_'. $batch_product_id .'" class="open_bonus_product_unit_id" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'] . '">
										<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][discount_amount][]" class="form-control width_100 discount_amount" value="" />
										<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type" value=""/>
										<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][policy_type][]" class="policy_type" value="' . $policy_type . '">
										<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][policy_id][]" class="policy_id" value="' . $policy_id . '">
										<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][is_bonus][]" class="is_bonus" value="3">
										<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100_this open_bonus_product_rate" value="0.0">
										<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100_this open_bonus_product_price_id" value="">
										<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value="">
										<input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][selected_set][' . $policy_id . ']" step="any" class="set_id" value="' . $set . '">
									</th>
									<th class="text-center" width="12%">
										<input step="any" style="width: 50px;" ' . $min_qty_disabled . ' ' . $min_qty_required . ' data-set="' . $set . '" type="number" min="1" max="' . $provide_bonus_qty . '" name="data[OrderDetail][sales_qty][]" class="width_100_this policy_min_qty ' . $policy_id . '_policy_set_' . $set . ' ' . $policy_id . '_qty_validation" value="' . $value . '">
										
										<a   data-policy="'.$policy_id.'" value="'.$batch_product_id.'"  data-toggle="modal"  class="btn btn-primary btn-sm multi_batch_modal remove_select_batch">Batch Select</a>
							
									</th>
									<th class="text-center" width="10%">
										<input type="checkbox" disabled ' . $min_qty_checked . ' class="is_bonus_checked" name="data[OrderDetail][bonus_check][]" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">
									</th>
								</tr>';
						}
					}
				}
			} else if ($parsed_fourmula['set_relation'] == 'OR') {
				$selected_set_var = 1;
				if (isset($old_selected_set[$policy_id])) {
					$selected_set_var = $old_selected_set[$policy_id];
				}
				$selected_set[$policy_id] = $selected_set_var;
				if ($selected_set_var == 1) {
					$btn_set_1 = 'btn-success';
					$btn_set_2 = 'btn-default';
				} else {
					$btn_set_1 = 'btn-default';
					$btn_set_2 = 'btn-success';
				}
				$b_html .=
					'<tr class="n_bonus_row set">
					<th colspan="4" class="text-center">
						<button class="btn ' . $btn_set_1 . ' btn_set" data-set="1" data-policy_id="' . $policy_id . '">Set-1</button>
						<button class="btn ' . $btn_set_2 . ' btn_set" data-set="2" data-policy_id="' . $policy_id . '">Set-2</button>
					</th>
				<tr>';
				foreach ($parsed_fourmula['formula_product'] as $set => $formula_product) {
					$element_relation = $parsed_fourmula['element_relation'][$set];
					$display_none = 'display_none';
					$disabled = 'disabled';
					$batch_select_disabled = '';
					
					if ($selected_set_var == $set) {
						$display_none = '';
						$disabled = '';
						$batch_select_disabled = '';
						
					}
					if ($element_relation == 'OR') {
						$i = 0;
						$s_disabled = 'readonly';
						foreach ($formula_product as $product_id) {
							$data = $product_wise_bonus[$product_id];
							$bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_qty'];
							$in_hand_bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['in_hand_bonus_qty'];
							if (!$in_hand_bonus_qty)
								$in_hand_bonus_qty = 0;
							$bonus_qty = $bonus_qty - $in_hand_bonus_qty;
							$provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
							if (isset($old_other_policy_info[$policy_id]) && $old_other_policy_info[$policy_id]['provided_bonus_qty'] != $provide_bonus_qty) {
								unset($old_selected_bonus[$policy_id]);
								unset($old_selected_set[$policy_id]);
							}
							$other_policy_info[$policy_id]['provided_bonus_qty'] = $provide_bonus_qty;
							if (isset($old_selected_bonus[$policy_id])) {
								if (isset($old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']]) && $old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] > 0) {
									$value = $old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']];
									$min_qty_disabled = '';
									$min_qty_checked = 'checked';
									$min_qty_required = 'required';
									
								} else {
									$value = 0;
									$min_qty_disabled = 'readonly';
									
									$min_qty_checked = '';
									$min_qty_required = '';
								}
							} else {
								if ($i == 0) {
									$value = $provide_bonus_qty;
									$min_qty_disabled = '';
									$min_qty_checked = 'checked';
								   
									$min_qty_required = 'required';
								} else {
									$value = 0;
									$min_qty_disabled = 'readonly';
									
									$min_qty_checked = '';
									$min_qty_required = '';
								}
							}

							$prod_info = $this->Product->find('first', array(
								'conditions' => array('id' => $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']),
								'recursive' => -1
							));

							if ($prod_info['Product']['is_virtual'] == 1) {

								$pd_name_replace = $this->get_product_inventroy_check($store_id, $prod_info['Product']['id'], $prod_info['Product']['parent_id']);

								if ($pd_name_replace == 1) {
									$pdname = $this->get_parent_virtual_pd_info($data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']);

									$data['Product']['name'] = $pdname['VirtualProduct']['name'];
								}
							}

							$selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] = $value;
							$batch_product_id = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'];
							$i++;
							$b_html .= '
								<tr class="bonus_row n_bonus_row ' . $display_none . ' bonus_policy_id_' . $policy_id . ' set_' . $set . '">
									<th class="text-center" id="bonus_product_list">
										<div class="input select">
											<select ' . $s_disabled . ' ' . $disabled . ' name="data[OrderDetail][product_id][]" class="form-control width_100_this policy_bonus_product_id">
											<option value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">' . $data['Product']['name'] . '</option>
											</select>
										</div>
									</th>
									<th class="text-center" width="12%">
										<input ' . $s_disabled . ' ' . $disabled . ' type="text" name="" class="form-control width_100_this open_bonus_product_unit_name" value="' . $data['MU']['name'] . '" readonly disabled="">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][measurement_unit_id][]" id="bonus_product_unit_id_'.$policy_id.'_'. $batch_product_id .'" class="open_bonus_product_unit_id" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'] . '">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][discount_amount][]" class="form-control width_100 discount_amount" value="" />
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type" value=""/>
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][policy_type][]" class="policy_type" value="' . $policy_type . '">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][policy_id][]" class="policy_id" value="' . $policy_id . '">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][is_bonus][]" class="is_bonus" value="3">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100_this open_bonus_product_rate" value="0.0">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100_this open_bonus_product_price_id" value="">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value="">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][selected_set][' . $policy_id . ']" step="any" class="set_id" value="' . $set . '">
									</th>
									<th class="text-center" width="12%">
										<input step="any" ' . $disabled . ' style="width: 50px;" ' . $min_qty_disabled . '  ' . $min_qty_required . ' data-set="' . $set . '" type="number" min="1" max="' . $provide_bonus_qty . '" name="data[OrderDetail][sales_qty][]" class="width_100_this policy_min_qty ' . $policy_id . '_policy_set_' . $set . ' ' . $policy_id . '_qty_validation" value="' . $value . '">
										
										<a data-policy="'.$policy_id.'"  '.$batch_select_disabled.'   value="'.$batch_product_id.'"  data-toggle="modal"  class="btn btn-primary btn-sm multi_batch_modal remove_select_batch">Batch Select</a>
							
									</th>
									<th class="text-center" width="10%">
										<input type="checkbox" ' . $min_qty_checked . '  class="is_bonus_checked" name="data[OrderDetail][bonus_check][]" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">
									</th>
								</tr>';
						}
					} else if ($element_relation == 'AND') {
						$i = 0;
						$s_disabled = 'readonly';
						foreach ($formula_product as $product_id) {
							$data = $product_wise_bonus[$product_id];
							$bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_qty'];
							$in_hand_bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['in_hand_bonus_qty'];
							if (!$in_hand_bonus_qty)
								$in_hand_bonus_qty = 0;
							$bonus_qty = $bonus_qty - $in_hand_bonus_qty;
							$provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
							if (isset($old_other_policy_info[$policy_id]) && $old_other_policy_info[$policy_id]['provided_bonus_qty'] != $provide_bonus_qty) {
								unset($old_selected_bonus[$policy_id]);
								unset($old_selected_set[$policy_id]);
							}
							$other_policy_info[$policy_id]['provided_bonus_qty'] = $provide_bonus_qty;
							$value = $provide_bonus_qty;
							$min_qty_disabled = 'readonly';
							$min_qty_checked = 'checked';
							$min_qty_required = 'required';

							$prod_info = $this->Product->find('first', array(
								'conditions' => array('id' => $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']),
								'recursive' => -1
							));

							if ($prod_info['Product']['is_virtual'] == 1) {

								$pd_name_replace = $this->get_product_inventroy_check($store_id, $prod_info['Product']['id'], $prod_info['Product']['parent_id']);

								if ($pd_name_replace == 1) {
									$pdname = $this->get_parent_virtual_pd_info($data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']);

									$data['Product']['name'] = $pdname['VirtualProduct']['name'];
								}
							}

							$selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] = $value;
							$batch_product_id = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'];
							$i++;
							$b_html .= '
								<tr class="bonus_row n_bonus_row ' . $display_none . ' bonus_policy_id_' . $policy_id . ' set_' . $set . '">
									<th class="text-center" id="bonus_product_list">
										<div class="input select">
											<select ' . $s_disabled . ' ' . $disabled . ' name="data[OrderDetail][product_id][]" class="form-control width_100_this policy_bonus_product_id">
											<option value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">' . $data['Product']['name'] . '</option>
											</select>
										</div>
									</th>
									<th class="text-center" width="12%">
										<input ' . $s_disabled . ' ' . $disabled . ' type="text" name="" class="form-control width_100_this open_bonus_product_unit_name" value="' . $data['MU']['name'] . '" disabled="">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][measurement_unit_id][]" id="bonus_product_unit_id_'.$policy_id.'_'. $batch_product_id .'" class="open_bonus_product_unit_id" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'] . '">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][discount_amount][]" class="form-control width_100 discount_amount" value="" />
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type" value=""/>
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][policy_type][]" class="policy_type" value="' . $policy_type . '">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][policy_id][]" class="policy_id" value="' . $policy_id . '">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][is_bonus][]" class="is_bonus" value="3">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100_this open_bonus_product_rate" value="0.0">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100_this open_bonus_product_price_id" value="">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value="">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][selected_set][' . $policy_id . ']" step="any" class="set_id" value="' . $set . '">
									</th>
									<th class="text-center" width="12%">
										<input step="any" ' . $disabled . ' style="width: 50px;" ' . $min_qty_disabled . ' ' . $min_qty_required . ' data-set="' . $set . '" type="number" min="1" max="' . $provide_bonus_qty . '" name="data[OrderDetail][sales_qty][]" class="width_100_this policy_min_qty ' . $policy_id . '_policy_set_' . $set . ' ' . $policy_id . '_qty_validation" value="' . $value . '">
										
										<a  '.$batch_select_disabled.'  data-policy="'.$policy_id.'" value="'.$batch_product_id.'"  data-toggle="modal"  class="btn btn-primary btn-sm multi_batch_modal remove_select_batch">Batch Select</a>
									</th>
									<th class="text-center" width="10%">
										<input type="checkbox" disabled ' . $min_qty_checked . ' class="is_bonus_checked" name="data[OrderDetail][bonus_check][]" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">
									</th>
								</tr>';
						}
					}
				}
			}
		}
		return $b_html;
	}


	public function get_parent_virtual_pd_info($pid)
	{

		$this->loadModel('CurrentInventory');
		$this->loadModel('Product');

		$productinfo = $this->Product->find('first', array(
			'conditions' => array(
				'Product.id' => $pid
			),
			'joins' => array(
				array(
					'alias' => 'VirtualProduct',
					'table' => 'products',
					'type' => 'LEFT',
					'conditions' => 'Product.parent_id = VirtualProduct.id'
				)
			),
			'fields' => array('Product.id', 'Product.name', 'VirtualProduct.id', 'VirtualProduct.name'),
			'recursive' => -1
		));

		return $productinfo;
	}

	public function get_product_inventroy_check($soter_id, $vpid, $parent_product_id)
	{

		$this->loadModel('CurrentInventory');
		$this->loadModel('Product');

		$parentproductcount = $this->CurrentInventory->find('count', array(
			'fields' => array('CurrentInventory.product_id'),
			'conditions' => array(
				'CurrentInventory.store_id' => $soter_id,
				'CurrentInventory.product_id' => $parent_product_id
			),
			'group' => 'CurrentInventory.product_id HAVING sum(CurrentInventory.qty)>0',
			'recursive' => -1
		));

		$chilhproductCount = $this->CurrentInventory->find('count', array(
			'fields' => array('CurrentInventory.product_id'),
			'conditions' => array(
				'CurrentInventory.store_id' => $soter_id,
				'Product.parent_id' => $parent_product_id
			),
			'joins' => array(
				array(
					'alias' => 'Product',
					'table' => 'products',
					'type' => 'INNER',
					'conditions' => 'Product.id = CurrentInventory.product_id'
				)
			),
			'group' => 'CurrentInventory.product_id HAVING sum(CurrentInventory.qty)>0',
			'recursive' => -1
		));

		if (empty($parentproductcount)) {
			$parentproductcount = 0;
		}

		if (empty($chilhproductCount)) {
			$chilhproductCount = 0;
		}

		//echo $soter_id . '---' . $parent_product_id . '---' . $parentproductcount . '--' . $chilhproductCount;exit;

		if ($parentproductcount == 0 and $chilhproductCount == 1) {
			$show = 1;
		} else {
			$show = 0;
		}

		return $show;
	}


	private function parse_formla($formula)
	{
		$set_relation = '';
		$formula = explode(' ', $formula);
		$formula_product = array();
		$formula_element_relation = array();
		$set = 1;
		for ($i = 0; $i < count($formula); $i++) {
			if ($formula[$i] == '(') {
				continue;
			}
			if ($formula[$i] == ')') {
				if (($i + 1) < count($formula))
					$set_relation = $formula[$i + 1];
				$set += 1;
				$i = $i + 1;
			} else {
				if ($formula[$i] == 'AND' || $formula[$i] == 'OR') {
					$formula_element_relation[$set] = $formula[$i];
				} else {
					$formula_product[$set][] = $formula[$i];
				}
			}
		}
		$parse_formula = array(
			'set_relation' => $set_relation,
			'formula_product' => $formula_product,
			'element_relation' => $formula_element_relation
		);
		return $parse_formula;
	}


	//-----------------product batch selection --------------\\


	public function get_product_batch_list()
	{
		$order_details_id = $this->request->data['order_details_id'];
		$min_qty = $this->request->data['min_qty'];
		
		$product_id = $this->request->data['product_id'];
		$office_id = $this->request->data['office_id'];
		$current_row_no = $this->request->data['current_row_no'];
		$product_unit_id = $this->request->data['product_unit_id'];
		$product_order_id = $this->request->data['product_order_id'];
		

		$this->loadModel('Product');
		$pinfo_maintain_batch = $this->Product->find('first',array(
			'conditions'=>array('Product.id'=>$product_id), 
			'fields'=>array('Product.maintain_batch', 'Product.is_maintain_expire_date', 'Product.parent_id'),
			'recursive'=>-1
		));
		$disable = 0;
		if( $pinfo_maintain_batch['Product']['maintain_batch'] == 0 || $pinfo_maintain_batch['Product']['is_maintain_expire_date'] == 0 ){
			$disable = 1;
		}
		
		$is_virtual = $pinfo_maintain_batch['Product']['parent_id'];
	
		$this->loadModel('ProductBatchInfo');

		$product_batch_list = $this->ProductBatchInfo->find('all', array(
			'conditions' => array(
				'ProductBatchInfo.order_details_id' => $order_details_id,
				'ProductBatchInfo.product_id' => $product_id,
				'ProductBatchInfo.memo_details_id' => 0,
			),
			'recursive'=>-1
		));
		
			//echo '<pre>';print_r($product_batch_list);exit;
		
		

		$exting_batch_list = array();

		if(!empty($product_batch_list)){
			
			foreach($product_batch_list as $val){
				$exting_batch_list[$val['ProductBatchInfo']['current_inventory_id']] = $val['ProductBatchInfo']['given_stock'];
			}

		}

		
		
		$this->loadModel('Store');
		$this->loadModel('CurrentInventory');

		$store_info = $this->Store->find('first',array(
			'conditions'=>array('Store.office_id'=>$office_id), 
			'fields'=>array('Store.id'),
			'recursive'=>-1
		));
	
		$store_id = $store_info['Store']['id'];
		
		//----------------product expire last date-----------\\
		$this->loadModel('ProductMonth');

		$product_expire_month_info = $this->ProductMonth->find('first', array(
			'conditions' => array(
				'ProductMonth.product_id' => $product_id
			),
			'fields'=>array('ProductMonth.day_month'),
			'recursive' => -1
		));
		if(empty($product_expire_month_info)){
			$productExpireLimit = 0;
		}else{
			$productExpireLimit = $product_expire_month_info['ProductMonth']['day_month'];
		}

		$p_expire_date = date('Y-m-t', strtotime("+".$productExpireLimit." months"));

		//--------------end-------------\\
	   
		$inventory_info = $this->CurrentInventory->find('all', array(
			'conditions' => array(
				'CurrentInventory.qty >' => 0,
				'CurrentInventory.store_id' => $store_id,
				'CurrentInventory.inventory_status_id' => 1,
				'CurrentInventory.product_id' => $product_id,
				"(CurrentInventory.expire_date is null OR CurrentInventory.expire_date > '$p_expire_date' )"
			),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'INNER',
					'conditions' => 'Product.id=CurrentInventory.product_id'
				),
				array(
					'table' => 'product_measurements',
					'alias' => 'ProductMeasurement',
					'type' => 'LEFT',
					'conditions' => 'ProductMeasurement.product_id=Product.id AND ProductMeasurement.measurement_unit_id='.$product_unit_id
				)
			),
			'fields'=>array(
				'CurrentInventory.id',
				'CurrentInventory.batch_number',
				'CurrentInventory.expire_date',
				'CurrentInventory.qty',
				'(case when ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end) as qty_base',
			),
		   'order' => array('CurrentInventory.expire_date' => 'asc'),
		   'recursive'=>-1
			
		));
		$html = '';
		
		
			$m = 1; 
				
			foreach ($inventory_info as $val) {

				$cid = $val['CurrentInventory']['id'] ;
				$batch_number = $val['CurrentInventory']['batch_number'] ;
				$expire_date = $val['CurrentInventory']['expire_date'] ;
				$qty = $val['CurrentInventory']['qty'] ;

				$number = $qty / $val[0]['qty_base'] ;
				$number = explode('.', $number);
				$qty = $number[0] . '.' . (isset($number[1]) ? substr($number[1], 0, 2) : 00);

				$checked = '';

				$exting_given_stock_info = $exting_batch_list[$cid];
				
				

				$given_stock_deduct = 0;
				$given_qty = 0;
				if(!empty($exting_given_stock_info)){

					$checked = 'checked="checked"';
					
					$min_qty = sprintf("%0.2f", $min_qty);
					$exting_given_stock_info = sprintf("%0.2f", $exting_given_stock_info);
						
					if( $min_qty < $exting_given_stock_info ){
						
						$checked = '';
						$given_qty = 0;
						$given_stock_deduct = 0;
						
					}else{
						$given_qty = $exting_given_stock_info;
						$given_stock_deduct = $exting_given_stock_info;
					}
					
				}else{
				   
					if($disable == 1 AND empty($exting_batch_list)){
						$checked = 'checked="checked"';
						$given_qty = $min_qty;
						$given_stock_deduct = $min_qty;
					}
				}
				
				$onekeyup = " onkeyup=stock_validataion($current_row_no)";
				$oneclick = " onclick=checkbox_check($cid".",". "$current_row_no)";
				$checkboxid = 'ciid_' . $cid;
				$givenstockid = 'given_stock_id_' . $cid;
				
				
				if(!empty($checked)){
					$readonly = " ";
				}else{
					$readonly = " disabled='disabled' ";
				
				}
				
				if($m == 1 AND empty($exting_batch_list) || $min_qty == 0 ){
					$checked = 'checked="checked"';
					$readonly = '';
				}

				$html .="<tr>";
				$html .="
					<td> 
						<input type='checkbox'  id='".$checkboxid."'  $oneclick  value='".$cid."' name='data[OrderDetail][product_current_inventory_id][$current_row_no][]' $checked>
					</td>
					<td> $batch_number </td>
					<td> $expire_date </td>
					<td> $qty </td>
					<td> <input style='width: 25%;' step='any'  max='".$qty."'  $readonly  id='".$givenstockid."' class='given_stock_qty'  $onekeyup   type='number' name='data[OrderDetail][product_given_stock][$current_row_no][]' value='".$given_qty."'> </td>
				";
				$html .="</tr>";
				

				$min_qty = $min_qty - $given_stock_deduct;
				$given_qty = 0;
				
				$m++;
				
			}

		$result['html'] = $html;
		$result['disable'] = $disable;
		echo json_encode($result);
		$this->autoRender = false;

	}

	public function get_bonus_product_batch_list()
	{
		
		$product_order_id = $this->request->data['product_order_id'];
	   // $order_details_id = $this->request->data['order_details_id'];
		$policy_id = $this->request->data['policy_id'];
		$min_qty = $this->request->data['min_qty'];
		$product_id = $this->request->data['product_id'];
		$office_id = $this->request->data['office_id'];
		$current_row_no = $this->request->data['current_row_no'];
		$row_number = $this->request->data['row_number'];
		$bonus_product_unit_id = $this->request->data['bonus_product_unit_id'];

		$this->loadModel('Product');
		$pinfo_maintain_batch = $this->Product->find('first',array(
			'conditions'=>array('Product.id'=>$product_id), 
			'fields'=>array('Product.maintain_batch', 'Product.is_maintain_expire_date', 'Product.parent_id'),
			'recursive'=>-1
		));
		$disable = 0;
		if( $pinfo_maintain_batch['Product']['maintain_batch'] == 0 || $pinfo_maintain_batch['Product']['is_maintain_expire_date'] == 0 ){
			$disable = 1;
		}

		$this->loadModel('OrderDetail');
		$this->loadModel('Order');
		
		$is_virtual = $pinfo_maintain_batch['Product']['parent_id'];
		
		
		if($is_virtual < 1){
			$order_con = array(
				'OrderDetail.order_id' => $product_order_id,
				'OrderDetail.product_id' => $product_id,
				'OrderDetail.price <' => 1,
			);
		}else{
			$order_con = array(
				'OrderDetail.order_id' => $product_order_id,
				'OrderDetail.virtual_product_id' => $product_id,
				'OrderDetail.price <' => 1,
			);
		}
		
		$orderDetailsId = $this->OrderDetail->find('first', array(
			'conditions' => $order_con,
			'recursive'=>-1
		));
		$order_detailsids = $orderDetailsId['OrderDetail']['id'];
		
		
		/*
		if($is_virtual < 1){
			$sql = "Select * From order_details where order_id=$product_order_id and product_id =$product_id and price < 1 ";
			
		}else{
			
			$sql = "Select * From order_details where order_id=$product_order_id and virtual_product_id =$product_id and price < 1 ";
		}
		
		
		$orderDetailsId = $this->Order->query($sql);
		
		$order_detailsids = $orderDetailsId[0][0]['id'];
		
		*/
		
		$this->loadModel('ProductBatchInfo');

		$product_batch_list = $this->ProductBatchInfo->find('all', array(
			'conditions' => array(
				'ProductBatchInfo.order_details_id' => $order_detailsids,
			),
			'recursive'=>-1
		));

		$exting_batch_list = array();

		if(!empty($product_batch_list)){
			
			foreach($product_batch_list as $val){
				$exting_batch_list[$val['ProductBatchInfo']['current_inventory_id']] = $val['ProductBatchInfo']['given_stock'];
			}

		}

		$this->loadModel('Store');
		$this->loadModel('CurrentInventory');

		$store_info = $this->Store->find('first',array(
			'conditions'=>array('Store.office_id'=>$office_id), 
			'fields'=>array('Store.id'),
			'recursive'=>-1
		));
	
		$store_id = $store_info['Store']['id'];
		
		//----------------product expire last date-----------\\
		
		$this->loadModel('ProductMonth');

		$product_expire_month_info = $this->ProductMonth->find('first', array(
			'conditions' => array(
				'ProductMonth.product_id' => $product_id
			),
			'fields'=>array('ProductMonth.day_month'),
			'recursive' => -1
		));
		if(empty($product_expire_month_info)){
			$productExpireLimit = 0;
		}else{
			$productExpireLimit = $product_expire_month_info['ProductMonth']['day_month'];
		}

		$p_expire_date = date('Y-m-t', strtotime("+".$productExpireLimit." months"));
		

		//--------------end-------------\\

		$inventory_info = $this->CurrentInventory->find('all', array(
			'conditions' => array(
				'CurrentInventory.qty >' => 0,
				'CurrentInventory.store_id' => $store_id,
				'CurrentInventory.inventory_status_id' => 1,
				'CurrentInventory.product_id' => $product_id,
				"(CurrentInventory.expire_date is null OR CurrentInventory.expire_date > '$p_expire_date' )"
			),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'INNER',
					'conditions' => 'Product.id=CurrentInventory.product_id'
				),
				array(
					'table' => 'product_measurements',
					'alias' => 'ProductMeasurement',
					'type' => 'LEFT',
					'conditions' => 'ProductMeasurement.product_id=Product.id AND ProductMeasurement.measurement_unit_id='.$bonus_product_unit_id
				)
			),
			'fields'=>array(
				'CurrentInventory.id',
				'CurrentInventory.batch_number',
				'CurrentInventory.expire_date',
				'CurrentInventory.qty',
				'(case when ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end) as qty_base',
			),
		   'order' => array('CurrentInventory.expire_date' => 'asc'),
		   'recursive'=>-1
			
		));
		$html = '';
		$m = 1;
		if(!empty($inventory_info)){
			
			foreach ($inventory_info as $val) {

				$cid = $val['CurrentInventory']['id'] ;
				$batch_number = $val['CurrentInventory']['batch_number'] ;
				$expire_date = $val['CurrentInventory']['expire_date'] ;
				$qty = $val['CurrentInventory']['qty'] ;

				$number = $qty / $val[0]['qty_base'] ;

				$number = explode('.', $number);
				$qty = $number[0] . '.' . (isset($number[1]) ? substr($number[1], 0, 2) : 00);
			   
				// $qty = $val['CurrentInventory']['qty'] ;

				$checked = '';

				$exting_given_stock_info = $exting_batch_list[$cid];
				$given_stock_deduct = 0;
				$given_qty = 0;
				if(!empty($exting_given_stock_info)){

					$checked = 'checked="checked"';
					
					$min_qty = sprintf("%0.2f", $min_qty);
					$exting_given_stock_info = sprintf("%0.2f", $exting_given_stock_info);

					if( $min_qty < $exting_given_stock_info ){
						$checked = '';
						$given_qty = 0;
						$given_stock_deduct = 0;
					}else{
						$given_qty = $exting_given_stock_info;
						$given_stock_deduct = $exting_given_stock_info;
					}

					//$given_qty = $exting_given_stock_info;
					//$given_stock_deduct = $exting_given_stock_info;
					
				}else{


					if($disable == 1 AND empty($exting_batch_list)){
						$checked = 'checked="checked"';
						$given_qty = $min_qty;
						$given_stock_deduct = $min_qty;
					}

				}

				$onekeyup = " onkeyup=bonus_stock_validataion($product_id".",". "$current_row_no)";
				$oneclick = " onclick=bonus_checkbox_check($cid".",". "$product_id".","."$current_row_no)";
				$checkboxid = 'bonus_ciid_' . $cid;
				$givenstockid = 'bonus_given_stock_id_' . $cid;

				if(!empty($checked)){
					 $readonly = " ";
				}else{
					$readonly = " disabled='disabled' ";
				   
				}
				
				if($m == 1 AND empty($exting_batch_list) || $min_qty == 0 ){
					$checked = 'checked="checked"';
					$readonly = '';
				}

				$html .="<tr>";
				$html .="
					<td> 
						<input class='disable_class' type='checkbox' id='".$checkboxid."'  $oneclick  value='".$cid."' name='data[OrderDetail][product_current_inventory_id][$row_number][]' $checked>
					</td>
					<td> $batch_number </td>
					<td> $expire_date </td>
					<td> $qty </td>
					<td> <input style='width: 25%;' step='any' max='".$qty."'  $readonly  id='".$givenstockid."' class='given_stock_qty'  $onekeyup   type='number' name='data[OrderDetail][product_given_stock][$row_number][]' value='".$given_qty."'> </td>
				";
				$html .="</tr>";

				$min_qty = $min_qty - $given_stock_deduct;
				$given_qty = 0;
				
				$m++;
				
			}
		}
		
		else{
			$html ="
				<tr>
				<td> 
					<input class='disable_class' onclick='return false;' checked='checked' type='checkbox'  value='0' name='data[OrderDetail][product_current_inventory_id][$row_number][]' >
				</td>
				<td>  </td>
				<td>  </td>
				<td> </td>
				<td> <input style='width: 25%;' step='any'  readonly  class='given_stock_qty'  type='number' name='data[OrderDetail][product_given_stock][$row_number][]' value='0'> </td>
				</tr>
			";
		   
		}

		$result['html'] = $html;
		$result['disable'] = $disable;
		echo json_encode($result);
		$this->autoRender = false;

	}
	
	
	

	//--------------end---------------\\
	
	/*get memo_details actual price using in admin_view*/
	public function get_memo_actual_price($order_no){
		$return_val = array();
		$this->loadModel('MemoDetails');
		$memo_detils = $this->MemoDetails->find('all', array(
			'fields'	=> array('MemoDetails.actual_price', 'MemoDetails.product_id'),
			'joins'		=> array(
				array(
					'table' => 'memos',
					'alias' => 'Memos',
					'type' => 'left',
					'conditions' => 'MemoDetails.memo_id=Memos.id'
				),
			),
			 'conditions' => array(
				'Memos.memo_no' => $order_no,
				'MemoDetails.price >' => 0,
			),
			 'recursive'=>-1
		));
		foreach($memo_detils as $rows){
			$return_val[$rows['MemoDetails']['product_id']] = $rows['MemoDetails']['actual_price'];
		}
		//$this->dd($);
		return $return_val;
	}
	
	public function getProductBatchList($orderdetailsinfo)
	{
		$this->loadModel('ProductBatchInfo');
		$this->loadModel('Product');

		$product_batch_list = $this->ProductBatchInfo->find('all', array(
			'conditions' => array(
				'ProductBatchInfo.order_details_id' => $orderdetailsinfo['id'],
			),
			'joins'=>array(
				array(
					'table' => 'current_inventories',
					'alias' => 'CurrentInventory',
					'type' => 'INNER',
					'conditions' => 'CurrentInventory.id=ProductBatchInfo.current_inventory_id'
				),
			),
			'fields'=>array('CurrentInventory.product_id', 'CurrentInventory.batch_number', 'ProductBatchInfo.given_stock'),
			'recursive'=>-1
		));
		$batch = '';
		
		if(!empty($product_batch_list)){

			$i=0;
			$bqty = 0; 

			foreach( $product_batch_list as $val){

				if($orderdetailsinfo['price'] < 1){

					$pinfo = $this->Product->find('first', array(
						'conditions' => array(
							'Product.id' => $val['CurrentInventory']['product_id'],
						),
						'fields'=>array('Product.sales_measurement_unit_id'),
						'recursive'=>-1
					));

					$sale_unit_id = $pinfo['Product']['sales_measurement_unit_id'];
					$bonus_unit_id = $orderdetailsinfo['measurement_unit_id'];
					$product_id = $val['CurrentInventory']['product_id'];

					$given_stock = $val['ProductBatchInfo']['given_stock'];

					$b_s_qty = $this->convert_unit_to_unit($product_id, $bonus_unit_id, $sale_unit_id, $given_stock);

					$val['ProductBatchInfo']['given_stock'] = $b_s_qty;
					$bqty +=$b_s_qty;

				}

				$batch .= $val['CurrentInventory']['batch_number'] . ' : ' . $val['ProductBatchInfo']['given_stock'] . '<br>';
				$i++;

			}
			
			$batch = rtrim($batch,", ");
		}else{
			$batch = '';
		}
		
		$batch_list = array(
			'batch'=>$batch,
			'count'=>$i,
			'convert_qty'=>$bqty,
		);

		$this->autoRender = false;
		return $batch_list;


	}

	public function getProductSalebaleBonusBatchList($selable, $bonus){

		$sale_unit_id = $selable['measurement_unit_id'];
		$bonus_unit_id = $bonus['measurement_unit_id'];

		$this->loadModel('ProductBatchInfo');

		$salabelProductBatch = $this->ProductBatchInfo->find('all', array(
			'conditions' => array(
				'ProductBatchInfo.order_details_id' => $selable['id'],
			),
			'joins'=>array(
				array(
					'table' => 'current_inventories',
					'alias' => 'CurrentInventory',
					'type' => 'INNER',
					'conditions' => 'CurrentInventory.id=ProductBatchInfo.current_inventory_id'
				),
			),
			'fields'=>array('CurrentInventory.id', 'CurrentInventory.batch_number', 'ProductBatchInfo.given_stock'),
			'recursive'=>-1
		));

		$s_current_inventory_id = array();

		foreach( $salabelProductBatch as $val ){

			$s_current_inventory_id[$val['CurrentInventory']['id']] = array(
				'batch'=>$val['CurrentInventory']['batch_number'],
				'givenstock'=>$val['ProductBatchInfo']['given_stock']
			);
		}

		$bonusProductBatch = $this->ProductBatchInfo->find('all', array(
			'conditions' => array(
				'ProductBatchInfo.order_details_id' => $bonus['id'],
			),
			'joins'=>array(
				array(
					'table' => 'current_inventories',
					'alias' => 'CurrentInventory',
					'type' => 'INNER',
					'conditions' => 'CurrentInventory.id=ProductBatchInfo.current_inventory_id'
				),
			),
			'fields'=>array( 'CurrentInventory.id', 'CurrentInventory.product_id', 'CurrentInventory.batch_number', 'ProductBatchInfo.given_stock'),
			'recursive'=>-1
		));

		$b_current_inventory_id = array();

		foreach( $bonusProductBatch as $val ){

			$product_id = $val['CurrentInventory']['product_id'];
			$given_stock = $val['ProductBatchInfo']['given_stock'];

			$b_s_qty = $this->convert_unit_to_unit($product_id, $bonus_unit_id, $sale_unit_id, $given_stock);

			$b_current_inventory_id[$val['CurrentInventory']['id']] = array(
				'batch'=>$val['CurrentInventory']['batch_number'],
				'givenstock'=>$b_s_qty
			);

		}

		$batch = '';
		$i = 0;
		$total_qty = 0;
		foreach(  $s_current_inventory_id as $key =>$value ){

			$bonusqty = $b_current_inventory_id[$key];

			if( !empty($bonusqty)){

				$gstcok = $value['givenstock']+$bonusqty['givenstock'];

				$total_qty += $gstcok;

				$batch .= $value['batch'] . ' : ' . $gstcok . '<br>';

				unset($b_current_inventory_id[$key]);

			}else{
				$total_qty += $value['givenstock'];
				$batch .= $value['batch'] . ' : ' . $value['givenstock'] . '<br>';
			}

			$i++;

		}

		if(!empty( $b_current_inventory_id )){

			foreach(  $b_current_inventory_id as $key =>$value ){

				$total_qty += $value['givenstock'];
			  
				$batch .= $value['batch'] . ' : ' . $value['givenstock'] . '<br>';
				$i++;
	
			}

		}

		$batch_list = array(
			'batch'=>$batch,
			'count'=>$i,
			'total_qty'=>$total_qty,
		);
		
		$this->autoRender = false;
		return $batch_list;

	}


}
