<?php
App::uses('AppModel', 'Model');
/**
 * DealerWiseLimit Model
 *
 * @property Sender $Sender
 * @property TransactionType $TransactionType
 * @property ChallanDetail $ChallanDetail
 */
class DealerWiseLimitHistory extends AppModel {

	public $useTable = 'dealer_wise_limit_history'; 
	public $validate = array(
		'effective_start_date' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Effective Date is required.'
					)
		),
		'dealer_wise_limit_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Sales person field is required.'
					)		
		),
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
		'effective_start_date' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Effective Date is required.'
					)
		),
		/*'created_at' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Created date is required.'
					)
		),
		'updated_by' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Updated By date is required.'
					)
		)*/
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(		
		'DealerWiseLimit' => array(
			'className' => 'DealerWiseLimit',
			'foreignKey' => 'dealer_wise_limit_id',
			'conditions' => '',
			'fields' => 'id',
			'order' => ''
		)
	);
}
