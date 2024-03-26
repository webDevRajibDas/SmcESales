<?php
App::uses('AppModel', 'Model');
/**
 * TransactionType Model
 *
 * @property Challan $Challan
 */
class DistTransactionType extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

	// set validation rules
	public $validate = array(
		'transaction_code' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Transaction code field is required.'
					),
			'isUnique' => array(
						'rule' => 'isUnique',
						'message'=> 'Transaction code already exist.'
					),
		),
		'name' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Name field is required.'
					)
		)
	);
	
	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'DistChallan' => array(
			'className' => 'DistChallan',
			'foreignKey' => 'transaction_type_id',
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

		'DistCurrentInventory' => array(
			'className' => 'DIstCurrentInventory',
			'foreignKey' => 'transaction_type_id',
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

}
