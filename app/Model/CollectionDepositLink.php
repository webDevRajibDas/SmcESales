<?php
App::uses('AppModel', 'Model');
/**
 * CollectionDepositLink Model
 *
 * @property Collection $Collection
 * @property Deposit $Deposit
 */
class CollectionDepositLink extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'collection_deposit_link';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Collection' => array(
			'className' => 'Collection',
			'foreignKey' => 'collection_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Deposit' => array(
			'className' => 'Deposit',
			'foreignKey' => 'deposit_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
