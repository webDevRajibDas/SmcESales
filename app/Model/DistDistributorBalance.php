<?php
App::uses('AppModel', 'Model');
/**
 * DealerWiseLimit Model
 *
 * @property Sender $Sender
 * @property TransactionType $TransactionType
 * @property ChallanDetail $ChallanDetail
 */
class DistDistributorBalance extends AppModel
{

	public $useDbConfig = 'default_06';
	public $validate = array(
		'balance' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Amount is required.'
			)
		),
		'is_active' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Active is required.'
			)
		),

		'dist_distributor_id' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Distributor field is required.'
			),
		),
		'balance_transaction_type_id' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Transaction type field is required'
			),
		),

	);

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'DistDistributor' => array(
			'className' => 'DistDistributor',
			'foreignKey' => 'dist_distributor_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		/*'SenderStore' => array(
			'className' => 'Store',
			'foreignKey' => 'sender_store_id',
			'conditions' => '',
			'fields' => 'id,name,territory_id',
			'order' => ''
		)*/
	);

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'DistDistributorBalanceHistory' => array(
			'className' => 'DistDistributorBalanceHistory',
			'foreignKey' => 'dist_distributor_balance_id',
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
