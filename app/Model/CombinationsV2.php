<?php
App::uses('AppModel', 'Model');
/**
 * CombinationsV2 Model
 *
 * @property Reffrence $Reffrence
 */
class CombinationsV2 extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'combinations_v2';

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
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'create_for' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'reffrence_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'combined_qty' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'created_at' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $hasMany = array(
		'CombinationDetailsV2' => array(
			'className' => 'CombinationDetailsV2',
			'foreignKey' => 'combination_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public $belongsTo = array(
		'SoSpecialGroup' => array(
			'className' => 'SpecialGroup',
			'foreignKey' => 'reffrence_id',
			'conditions' => 'CombinationsV2.create_for=6',
			'fields' => '',
			'order' => ''
		),
		'SrSpecialGroup' => array(
			'className' => 'SpecialGroup',
			'foreignKey' => 'reffrence_id',
			'conditions' => 'CombinationsV2.create_for=7',
			'fields' => '',
			'order' => '',
		),
		'SoOutletCategory' => array(
			'className' => 'OutletCategory',
			'foreignKey' => 'reffrence_id',
			'conditions' => 'CombinationsV2.create_for=4',
			'fields' => '',
			'order' => ''
		),
		'SrOutletCategory' => array(
			'className' => 'DistOutletCategory',
			'foreignKey' => 'reffrence_id',
			'conditions' => 'CombinationsV2.create_for=5',
			'fields' => '',
			'order' => '',
		)
	);
}
