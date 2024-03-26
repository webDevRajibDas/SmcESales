<?php
App::uses('AppModel', 'Model');
/**
 * DealerWiseLimit Model
 *
 * @property Sender $Sender
 * @property TransactionType $TransactionType
 * @property ChallanDetail $ChallanDetail
 */
class DistDistributorBalanceHistory extends AppModel
{

	public $useDbConfig = 'default_06';
	public $useTable = 'dist_distributor_balance_histories';

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	/*public $belongsTo = array(		
		'DealerWiseLimit' => array(
			'className' => 'DealerWiseLimit',
			'foreignKey' => 'dealer_wise_limit_id',
			'conditions' => '',
			'fields' => 'id',
			'order' => ''
		)
	);*/
	public $belongsTo = array(
		'DistDistributor' => array(
			'className' => 'DistDistributor',
			'foreignKey' => 'dist_distributor_id',
			'conditions' => '',
			'fields' => 'id',
			'order' => ''
		),
		'DistBalanceTransactionType' => array(
			'className' => 'DistBalanceTransactionType',
			'foreignKey' => 'balance_transaction_type_id',
			'conditions' => '',
			'fields' => 'id,name',
			'order' => ''
		),
		'Deposit' => array(
			'className' => 'Deposit',
			'foreignKey' => 'deposit_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
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
}
