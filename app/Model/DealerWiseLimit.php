<?php
App::uses('AppModel', 'Model');
/**
 * DealerWiseLimit Model
 *
 * @property Sender $Sender
 * @property TransactionType $TransactionType
 * @property ChallanDetail $ChallanDetail
 */
class DealerWiseLimit extends AppModel {

	
	public $validate = array(
		'max_amount' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Amount is required.'
					)
		),
		'is_active' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Active is required.'
					)
		),
		'effective_date' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Effective Date is required.'
					)
		),
		'dist_distributor_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Distributor field is required.'
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
			'fields' => 'id',
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
		'DealerWiseLimitHistory' => array(
			'className' => 'DealerWiseLimitHistory',
			'foreignKey' => 'dealer_wise_limit_id',
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
