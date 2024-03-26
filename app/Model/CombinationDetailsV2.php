<?php
App::uses('AppModel', 'Model');
/**
 * CombinationDetailsV2 Model
 *
 * @property Combination $Combination
 * @property ProductCombination $ProductCombination
 */
class CombinationDetailsV2 extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'combination_details_v2';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'combination_id' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'product_combination_id' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
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
	public $belongsTo = array(
		'CombinationsV2' => array(
			'className' => 'CombinationsV2',
			'foreignKey' => 'combination_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProductCombinationsV2' => array(
			'className' => 'ProductCombinationsV2',
			'foreignKey' => 'product_combination_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
