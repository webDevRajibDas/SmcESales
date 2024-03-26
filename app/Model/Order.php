<?php
App::uses('AppModel', 'Model');
/**
 * Order Model
 *
 */
class Order extends AppModel
{

	/**
	 * Display field
	 *
	 * @var string
	 */

	public $useDbConfig = 'default_06';
	// data filter
	public function filter($params, $conditions)
	{


		$conditions = array();
		//pr($params);
		if (CakeSession::read('Office.parent_office_id') != 0) {
			$conditions[] = array('SalesPerson.office_id' => CakeSession::read('Office.id'));
		} elseif (!empty($params['Order.office_id'])) {
			$conditions[] = array('SalesPerson.office_id' => $params['Order.office_id']);
		}

		if (!empty($params['Order.order_status'])) {
			$conditions[] = array('Order.order_status >' => 0);
		}
		if (!empty($params['Order.confirm_status'])) {
			if ($params['Order.confirm_status'] == 3) {
				$conditions[] = array('Order.confirm_status' => 0);
			} else {
				$conditions[] = array('Order.confirm_status' => $params['Order.confirm_status']);
			}
		}

		if (!empty($params['Order.order_no'])) {
			$conditions[] = array('Order.order_no Like' => "%" . $params['Order.order_no'] . "%");
		}
		if (!empty($params['Order.confirmed'])) {
			$conditions[] = array('Order.confirmed' => $params['Order.confirmed']);
		}
		if (!empty($params['Order.memo_reference_no'])) {
			$conditions[] = array('Order.memo_reference_no Like' => "%" . $params['Order.memo_reference_no'] . "%");
		}
		if (!empty($params['Order.territory_id'])) {
			$conditions[] = array('Order.territory_id' => $params['Order.territory_id']);
		}
		if (!empty($params['Order.thana_id'])) {
			$conditions[] = array('Order.thana_id' => $params['Order.thana_id']);
		}
		if (!empty($params['Order.market_id'])) {
			$conditions[] = array('Order.market_id' => $params['Order.market_id']);
		}
		if (!empty($params['Order.outlet_id'])) {
			$conditions[] = array('Order.outlet_id' => $params['Order.outlet_id']);
		} else {
			if (CakeSession::read('UserAuth.User.user_group_id') == 1034) {
				App::import('Model', 'DistUserMapping');
				App::import('Model', 'DistOutletMap');
				$this->DistOutletMap = new DistOutletMap();
				$this->DistUserMapping = new DistUserMapping();
				$sp_id = CakeSession::read('UserAuth.User.sales_person_id');
				$data = $this->DistUserMapping->find('first', array('conditions' => array('DistUserMapping.sales_person_id' => $sp_id)));
				$distributor_id = $data['DistUserMapping']['dist_distributor_id'];
				$dist_data = $this->DistOutletMap->find('first', array('conditions' => array('DistOutletMap.dist_distributor_id' => $distributor_id)));
				$outlet_id = $dist_data['DistOutletMap']['outlet_id'];
				$conditions[] = array('Order.outlet_id' => $outlet_id);
			}
		}
		if (!empty($params['Order.tso_id'])) {
			if (CakeSession::read('UserAuth.User.user_group_id') != 1034  && empty($params['Order.outlet_id'])) {
				App::import('Model', 'DistTsoMapping');
				App::import('Model', 'DistOutletMap');
				$this->DistOutletMap = new DistOutletMap();
				$this->DistTsoMapping = new DistTsoMapping();
				$sp_id = CakeSession::read('UserAuth.User.sales_person_id');

				$data = $this->DistTsoMapping->find('all', array('conditions' => array('DistTsoMapping.dist_tso_id' => $params['Order.tso_id'], 'DistDistributor.is_active' => 1)));
				$dist_list = array();
				foreach ($data as $key => $value) {
					$dist_list[$key] = $value['DistTsoMapping']['dist_distributor_id'];
				}
				$dist_data = $this->DistOutletMap->find('all', array(
					'conditions' => array(
						'DistOutletMap.dist_distributor_id' => $dist_list
					),
					'fields' => array('Outlet.id', 'DistDistributor.name',),
				));

				foreach ($dist_data as $key => $value) {
					$outlet_list[$key] = $value['Outlet']['id'];
				}

				$conditions[] = array('Order.outlet_id' => $outlet_list);
			}
		} else {
			if (CakeSession::read('UserAuth.User.user_group_id') == 1029  && empty($params['Order.outlet_id'])) {
				App::import('Model', 'DistTsoMapping');
				App::import('Model', 'DistOutletMap');
				App::import('Model', 'DistTso');
				$this->DistOutletMap = new DistOutletMap();
				$this->DistTsoMapping = new DistTsoMapping();
				$this->DistTso = new DistTso();
				$user_id = CakeSession::read('UserAuth.User.id');

				$dist_tso_info = $this->DistTso->find('first', array(
					'conditions' => array('DistTso.user_id' => $user_id, 'DistTso.is_active' => 1),
					'fields' => array('DistTso.id', 'DistTso.name'),
					'recursive' => -1,
				));
				$dist_tso_id = $dist_tso_info['DistTso']['id'];

				$data = $this->DistTsoMapping->find('all', array('conditions' => array(
					'DistTsoMapping.dist_tso_id' => $dist_tso_id,
					'DistDistributor.is_active' => 1
				)));
				$dist_list = array();
				foreach ($data as $key => $value) {
					$dist_list[$key] = $value['DistTsoMapping']['dist_distributor_id'];
				}
				$dist_data = $this->DistOutletMap->find('all', array(
					'conditions' => array(
						'DistOutletMap.dist_distributor_id' => $dist_list
					),
					'fields' => array('Outlet.id', 'DistDistributor.name',),
				));

				foreach ($dist_data as $key => $value) {
					$outlet_list[$key] = $value['Outlet']['id'];
				}

				$conditions[] = array('Order.outlet_id' => $outlet_list);
			}
		}
		if (isset($params['Order.date_from']) != '') {
			$conditions[] = array('Order.order_date >=' => Date('Y-m-d', strtotime($params['Order.date_from'])));
		}
		if (isset($params['Order.date_to']) != '') {
			$conditions[] = array('Order.order_date <=' => Date('Y-m-d', strtotime($params['Order.date_to'])));
		}


		if (isset($params['Order.operator'])) {
			if ($params['Order.operator'] == 3) {
				$conditions[] = array('Order.gross_value BETWEEN ? AND ?' => array($params['Order.order_value_from'], $params['Order.order_value_to']));
			} elseif ($params['Order.operator'] == 1) {
				$conditions[] = array('Order.gross_value <' => $params['Order.order_value']);
			} elseif ($params['Order.operator'] == 2) {
				$conditions[] = array('Order.gross_value >' => $params['Order.order_value']);
			}
		}
		//pr($conditions);die();
		return $conditions;
	}


	public $validate = array(

		'office_id' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Office field is required.'
			)
		),

		'territory_id' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Territory field is required.'
			)
		),
		'market_id' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Market field is required.'
			)
		),
		'market' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Market field is required.'
			)
		),
		'outlet_id' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Outlet field is required.'
			)
		),
		'outlet' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Outlet field is required.'
			)
		),

		'entry_date' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Entry Date field is required.'
			)
		),
		'order_time' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Order Date field is required.'
			)
		),
		'memo_no' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Order No field is required.'
			)
		)
	);


	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'SalesPerson' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'sales_person_id',
			'conditions' => '',
			'fields' => 'name,office_id',
			'order' => '',
		),
		'Outlet' => array(
			'className' => 'Outlet',
			'foreignKey' => 'outlet_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		),
		'Territory' => array(
			'className' => 'Territory',
			'foreignKey' => 'territory_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		),
		'Market' => array(
			'className' => 'Market',
			'foreignKey' => 'market_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		),

		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'office_id',
			'conditions' => '',
			'fields' => 'office_name',
			'order' => ''
		),
		'Store' => array(
			'className' => 'Store',
			'foreignKey' => 'w_store_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		),
		'InstrumentType' => array(
			'className' => 'InstrumentType',
			'foreignKey' => 'instrument_type',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		)
	);


	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'OrderDetail' => array(
			'className' => 'OrderDetail',
			'foreignKey' => 'order_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'TempOrderDetail' => array(
			'className' => 'TempOrderDetail',
			'foreignKey' => 'order_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);


	/*----- quaery Methods -----*/
}
