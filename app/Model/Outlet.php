<?php
App::uses('AppModel', 'Model');
/**
 * Outlet Model
 *
 * @property Market $Market
 * @property Category $Category
 */
class Outlet extends AppModel
{

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'name';


	//The Associations below have been created with all possible keys, those that are not needed can be removed
	/*========== validate===============*/
	public $validate = array(
		'name'		=> array(
			'mustNotEmpty'	=> array(
				'rule'		=> 'notEmpty',
				'message'	=> 'Name field is required.'
			),
			'isUnique' => array(
				'rule' => array('checkUnique', array('market_id', 'name'), false),
				'message' => 'This market & outlet combination has already been used.'
			)
		),
		/*'telephone'		=> array(
				'mustNotEmpty'	=> array(
					'rule'		=> 'notEmpty',
					'message'	=> 'Telephone field is required.'
				)
			),
			'mobile'		=> array(
				'mustNotEmpty'	=> array(
					'rule'		=> 'notEmpty',
					'message'	=> 'Mobile field is required.'
				)
			),*/
		'market_id' => array(
			'mustNotEmpty'	=> array(
				'rule'		=> 'notEmpty',
				'message'	=> 'Market field is required.'
			)
		),
		'category_id' => array(
			'mustNotEmpty'	=> array(
				'rule'		=> 'notEmpty',
				'message'	=> 'Category field is required.'
			)
		),
		'institute_id' => array(
			'unique' => array(
				'rule' => array('check_ngo'),
				'message' => 'NGO field is required.',
			)
		)
	);

	public function checkUnique($ignoredData, $fields, $or = true)
	{
		return $this->isUnique($fields, $or);
	}


	function check_ngo($data, $fields)
	{
		if ($this->data[$this->name]['is_ngo'] == 1 and $this->data[$this->name]['institute_id'] == '') {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Market' => array(
			'className' => 'Market',
			'foreignKey' => 'market_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'OutletCategory' => array(
			'className' => 'OutletCategory',
			'foreignKey' => 'category_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Institute' => array(
			'className' => 'Institute',
			'foreignKey' => 'institute_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);


	public $hasOne = array(
		'Program' => array(
			'className' => 'Program',
			'foreignKey' => 'outlet_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	// data filter
	public function filter($params, $conditions)
	{
		$conditions = array();
		if (CakeSession::read('Office.parent_office_id') != 0) {
			$conditions[] = array('Territory.office_id' => CakeSession::read('Office.id'));
		}
		if (!empty($params['Outlet.name'])) {
			$conditions[] = array('Outlet.name LIKE' => '%' . $params['Outlet.name'] . '%');
		}
		if (!empty($params['Outlet.mobile'])) {
			$conditions[] = array('Outlet.mobile' => $params['Outlet.mobile']);
		}
		if (!empty($params['Outlet.category_id'])) {
			$conditions[] = array('Outlet.category_id' => $params['Outlet.category_id']);
		}
		if (!empty($params['Outlet.office_id'])) {
			$conditions[] = array('Territory.office_id' => $params['Outlet.office_id']);
		}
		if (!empty($params['Outlet.territory_id'])) {
			$conditions[] = array('Market.territory_id' => $params['Outlet.territory_id']);
		}
		if (!empty($params['Outlet.market_id'])) {
			$conditions[] = array('Outlet.market_id' => $params['Outlet.market_id']);
		}
		if (!empty($params['Outlet.thana_id'])) {
			$conditions[] = array('Thana.id' => $params['Outlet.thana_id']);
		}
		if (!empty($params['Outlet.bonus_type'])) {
			$conditions[] = array('Outlet.bonus_type_id' => $params['Outlet.bonus_type']);
		}
		if (!empty($params['Outlet.is_active'])) {
			$conditions[] = array('Outlet.is_active' => $params['Outlet.is_active'] == 1 ? $params['Outlet.is_active'] : 0);
		}
		return $conditions;
	}
}
