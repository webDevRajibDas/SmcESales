<?php
App::uses('AppModel', 'Model');
/**
 * MessageList Model
 *
 * @property MessageCategory $MessageCategory
 * @property Sender $Sender
 */
class UserTerritoryList extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
		'user_id' => array(
			'notMustBeEmpty' => array(
				'rule' 		=> 'notEmpty',
				'message'   => 'User field required.'
			)
		),
		
		'office_id' => array(
			'notMustBeEmpty' => array(
				'rule' 		=> 'notEmpty',
				'message'   => 'Office field required.'
			)
		),
		
	);
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'office_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Territory' => array(
			'className' => 'Territory',
			'foreignKey' => 'territory_id',
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
	);*/
}
