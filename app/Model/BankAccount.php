<?php
App::uses('AppModel', 'Model');
/**
 * BankAccount Model
 *
 * @property SalesPerson $SalesPerson
 */
class BankAccount extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed
		public $validate = array(
			'account_number' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'Bank Account Number field is required.'
				),
				'isUnique' => array(
							'rule' => 'isUnique',
							'message'=> 'Account Number already exist.'
				),
			),
			'bank_branch_id' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'Bank Branch Name field is required.'
				),
			)
		);

/**
 * hasMany associations
 *
 * @var array
 */
	public $belongsTo = array(
		'BankBranch' => array(
			'className' => 'BankBranch',
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
		),
	);
}
