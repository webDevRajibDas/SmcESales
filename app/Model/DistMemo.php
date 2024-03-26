<?php
App::uses('AppModel', 'Model');
/**
 * DistMemo Model
 *
 */
class DistMemo extends AppModel
{

	/**
	 * Display field
	 *
	 * @var string
	 */

	// data filter
	public function filter($params, $conditions)
	{

		$conditions = array();

		if (!empty($params['DistMemo.office_id'])) {
			$conditions[] = array('DistMemo.office_id' => $params['DistMemo.office_id']);
		} else {

			if (CakeSession::read('Office.parent_office_id') == 0) {
			} else {

				$conditions[] = array('DistMemo.office_id' => CakeSession::read('Office.id'));
			}
		}
		if (!empty($params['DistMemo.memo_reference_no'])) {
			$conditions[] = array('DistMemo.dist_memo_no Like' => "%" . $params['DistMemo.memo_reference_no'] . "%");
		}
		if (!empty($params['DistMemo.market_id'])) {

			$conditions[] = array('DistMemo.market_id' => $params['DistMemo.market_id']);
		}
		if (!empty($params['DistMemo.thana_id'])) {

			$conditions[] = array('Market.thana_id' => $params['DistMemo.thana_id']);
		}
		if (!empty($params['DistMemo.territory_id'])) {
			$conditions[] = array('DistMemo.territory_id' => $params['DistMemo.territory_id']);
		}
		if (!empty($params['DistMemo.outlet_id'])) {
			$conditions[] = array('DistMemo.outlet_id' => $params['DistMemo.outlet_id']);
		}
		if (!empty($params['DistMemo.status'])) {
			$conditions[] = array('DistMemo.status' => $params['DistMemo.status']);
		}
		if (!empty($params['DistMemo.sr_id'])) {
			$conditions[] = array('DistMemo.sr_id' => $params['DistMemo.sr_id']);
		}
		if (!empty($params['DistMemo.dist_route_id'])) {
			$conditions[] = array('DistMemo.dist_route_id' => $params['DistMemo.dist_route_id']);
		}
		if (!empty($params['DistMemo.distributor_id'])) {
			$conditions[] = array('DistMemo.distributor_id' => $params['DistMemo.distributor_id']);
		} else {
			if (CakeSession::read('UserAuth.User.user_group_id') == 1034) {
				App::import('Model', 'DistUserMapping');
				$sp_id = CakeSession::read('UserAuth.User.sales_person_id');
				$this->DistUserMapping = new DistUserMapping();
				$data = $this->DistUserMapping->find('first', array('conditions' => array('DistUserMapping.sales_person_id' => $sp_id)));
				$distributor_id = $data['DistUserMapping']['dist_distributor_id'];
				$conditions[] = array('DistMemo.distributor_id' => $distributor_id);
			}
		}


		if (isset($params['DistMemo.date_from']) != '') {
			$conditions[] = array('DistMemo.memo_date >=' => Date('Y-m-d H:i:s', strtotime($params['DistMemo.date_from'])));
		}
		if (isset($params['DistMemo.date_to']) != '') {
			$conditions[] = array('DistMemo.memo_date <=' => Date('Y-m-d H:i:s', strtotime($params['DistMemo.date_to'] . ' 23:59:59')));
		}
		if (isset($params['DistMemo.operator'])) {
			if ($params['DistMemo.operator'] == 3) {
				$conditions[] = array('DistMemo.gross_value BETWEEN ? AND ?' => array($params['DistMemo.memo_value_from'], $params['DistMemo.memo_value_to']));
			} elseif ($params['DistMemo.operator'] == 1) {
				$conditions[] = array('DistMemo.gross_value <' => $params['DistMemo.mamo_value']);
			} elseif ($params['DistMemo.operator'] == 2) {
				$conditions[] = array('DistMemo.gross_value >' => $params['DistMemo.mamo_value']);
			}
		}
		return $conditions;
	}


	public $validate = array(
		'office_id' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Office field is required.'
			)
		),
		'sale_type_id' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Sale Type field is required.'
			)
		),
		/*
		'distibutor_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Distributot field is required.'
					)
		),
               'sr_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'SR field is required.'
					)
		),
             * 
             */
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
		'outlet_id' => array(
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
		'memo_date' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'CsaMemo Date field is required.'
			)
		),
		'dist_memo_no' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'CsaMemo No field is required.'
			)
		)
	);


	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'DistSalesRepresentative' => array(
			'className' => 'DistSalesRepresentative',
			'foreignKey' => 'sr_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
		),
		'Outlet' => array(
			'className' => 'DistOutlet',
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
			'className' => 'DistMarket',
			'foreignKey' => 'market_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		),

		'Distributor' => array(
			'className' => 'DistDistributor',
			'foreignKey' => 'distributor_id',
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
		'DistMemoDetail' => array(
			'className' => 'DistMemoDetail',
			'foreignKey' => 'dist_memo_id',
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
