<?php
App::uses('AppModel', 'Model');
/**
 * Territory Model
 *
 * @property Office $Office
 * @property Market $Market
 * @property TerritoryPerson $TerritoryPerson
 */
class Territory extends AppModel
{

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'name';
	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty')
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Territory already exists.'
			)
		),
		'office_id' => array(
			'NotMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Office Id field required.'
			)
		),
		'product_group_id' => array(

			'rule'    => array('product_group_check'),
			'message' => 'Product group is  required.'

		)
	);
	public function product_group_check($data)
	{

		return count(array_filter($data['product_group_id'], function ($x) {
			return !empty($x);
		})) > 0;
	}

	//The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'office_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public $hasOne = array(
		'SalesPerson' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'territory_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'Market' => array(
			'className' => 'Market',
			'foreignKey' => 'territory_id',
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
		'SaleTarget' => array(
			'className' => 'SaleTarget',
			'foreignKey' => 'territory_id',
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
		'SaleTargetMonth' => array(
			'className' => 'SaleTargetMonth',
			'foreignKey' => 'territory_id',
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
	public function filter($params, $conditions)
	{

		$conditions = array();
		$conditions[] = array('Territory.office_id' => $params['Territory.office_id']);
		if (!empty($params['Territory.name'])) {
			$conditions[] = array('Territory.name' => $params['Territory.name']);
		}
		return $conditions;
	}
}
