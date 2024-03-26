<?php
App::uses('AppModel', 'Model');
/**
 * MessageList Model
 *
 * @property MessageCategory $MessageCategory
 * @property Sender $Sender
 */
class LiveSalesTrack extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
		'start_time' => array(
			'notMustBeEmpty' => array(
				'rule' 		=> 'notEmpty',
				'message'   => 'Start time field required.'
			)
		),
		
		'end_time' => array(
			'notMustBeEmpty' => array(
				'rule' 		=> 'notEmpty',
				'message'   => 'End time field required.'
			)
		),
		
		'interval' => array(
			'notMustBeEmpty' => array(
				'rule' 		=> 'notEmpty',
				'message'   => 'Interval field required.'
			)
		),
		
	);
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		/*'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)*/
	);
	
	/**
 * hasMany associations
 *
 * @var array
 */
	/*public $hasMany = array(
		'MessageReceiver' => array(
			'className' => 'MessageReceiver',
			'foreignKey' => 'message_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);*/
}
