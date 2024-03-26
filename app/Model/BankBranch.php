<?php
App::uses('AppModel', 'Model');
/**
 * BankBranch Model
 *
 * @property SalesPerson $SalesPerson
 */
class BankBranch extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed
		public $validate = array(
			'name' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'Short Bank Branch field is required.'
				)
			),
			'bank_id' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'Short Bank Name field is required.'
				)
			),
			
			/*'territory_id' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'Territory field is required.'
				)
			)*/
		);

/**
 * hasMany associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Bank' => array(
			'className' => 'Bank',
			'foreignKey' => 'bank_id',
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
		'Territory' => array(
			'className' => 'Territory',
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
	public $hasMany = array(
		'BankAccount' => array(
			'className' => 'BankAccount',
			'foreignKey' => 'bank_branch_id',
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
