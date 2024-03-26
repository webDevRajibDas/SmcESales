<?php
App::uses('AppModel', 'Model');
/**
 * Memo Model
 *
 */
class Memo extends AppModel
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

		if (CakeSession::read('Office.parent_office_id') != 0) {
			$conditions[] = array('Territory.office_id' => CakeSession::read('Office.id'));
		} elseif (!empty($params['Memo.office_id'])) {
			//$conditions[] = array('SalesPerson.office_id' => $params['Memo.office_id']);
			$conditions[] = array('Territory.office_id' => $params['Memo.office_id']);
		}


		if (!empty($params['Memo.memo_no'])) {
			$conditions[] = array('Memo.memo_no Like' => "%" . $params['Memo.memo_no'] . "%");
		}
		if (!empty($params['Memo.memo_reference_no'])) {
			$conditions[] = array('Memo.memo_reference_no Like' => "%" . $params['Memo.memo_reference_no'] . "%");
		}
		if (!empty($params['Memo.territory_id'])) {
			$conditions[] = array('(Memo.territory_id =' . $params['Memo.territory_id'] . ' OR Memo.child_territory_id = ' . $params['Memo.territory_id'] . ')');
		}
		if (!empty($params['Memo.thana_id'])) {
			$conditions[] = array('Memo.thana_id' => $params['Memo.thana_id']);
		}
		if (!empty($params['Memo.market_id'])) {
			$conditions[] = array('Memo.market_id' => $params['Memo.market_id']);
		}
		if (!empty($params['Memo.outlet_id'])) {
			$conditions[] = array('Memo.outlet_id' => $params['Memo.outlet_id']);
		}
		if (isset($params['Memo.date_from']) != '') {
			$conditions[] = array('Memo.memo_date >=' => Date('Y-m-d H:i:s', strtotime($params['Memo.date_from'])));
		}
		if (isset($params['Memo.date_to']) != '') {
			$conditions[] = array('Memo.memo_date <=' => Date('Y-m-d H:i:s', strtotime($params['Memo.date_to'] . ' 23:59:59')));
		}
		if (isset($params['Memo.operator'])) {
			if ($params['Memo.operator'] == 3) {
				$conditions[] = array('Memo.gross_value BETWEEN ? AND ?' => array($params['Memo.memo_value_from'], $params['Memo.memo_value_to']));
			} elseif ($params['Memo.operator'] == 1) {
				$conditions[] = array('Memo.gross_value <' => $params['Memo.mamo_value']);
			} elseif ($params['Memo.operator'] == 2) {
				$conditions[] = array('Memo.gross_value >' => $params['Memo.mamo_value']);
			}
		}
		
		if (!empty($params['Memo.from_app'])) {
			
			if ($params['Memo.from_app'] == 2) {
				$conditions[] = array('Memo.from_app' =>0);
			} else {
				$conditions[] = array('Memo.from_app' => $params['Memo.from_app']);
			}
		}

		if (!empty($params['Memo.program_officer_id'])) {
			$conditions[] = array('Memo.created_by' => $params['Memo.program_officer_id']);
			$conditions[] = array('Memo.is_program' =>1);
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
		'csa_name' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'CSA Name field is required.'
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
				'message' => 'Memo Date field is required.'
			)
		),
		'memo_no' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Memo No field is required.'
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
		)
	);


	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'MemoDetail' => array(
			'className' => 'MemoDetail',
			'foreignKey' => 'memo_id',
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
