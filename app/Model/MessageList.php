<?php
App::uses('AppModel', 'Model');
/**
 * MessageList Model
 *
 * @property MessageCategory $MessageCategory
 * @property Sender $Sender
 */
class MessageList extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
		'message_category_id' => array(
			'notMustBeEmpty' => array(
				'rule' 		=> 'notEmpty',
				'message'   => 'Category field required.'
			)
		),
		'message_type' => array(
			'notMustBeEmpty' => array(
				'rule' 		=> 'notEmpty',
				'message'   => 'Message type field required.'
			)
		),
		'message' => array(
			'notMustBeEmpty' => array(
				'rule' 		=> 'notEmpty',
				'message'   => 'Message field required.'
			)
		)
	);
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'MessageCategory' => array(
			'className' => 'MessageCategory',
			'foreignKey' => 'message_category_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'SalesPerson' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'sender_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
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
		),
		'MessageProduct' => array(
			'className' => 'MessageProduct',
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
	);
}
