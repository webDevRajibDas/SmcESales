<?php
App::uses('AppModel', 'Model');
/**
 * DistDistributorLimit Model
 *
 * @property Sender $Sender
 * @property TransactionType $TransactionType
 * @property ChallanDetail $ChallanDetail
 */
class DistDistributorLimitHistory extends AppModel
{
	public $useDbConfig = 'default_06';
	public $useTable = 'dist_distributor_limit_histories';
	public $validate = array(
		'effective_start_date' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Effective Date is required.'
			)
		),
		'dist_distributor_limit_id' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Sales person field is required.'
			)
		),
		'max_amount' => array(
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
		'effective_start_date' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Effective Date is required.'
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
		'DistDistributorLimit' => array(
			'className' => 'DistDistributorLimit',
			'foreignKey' => 'dist_distributor_limit_id',
			'conditions' => '',
			'fields' => 'id',
			'order' => ''
		)
	);
}
