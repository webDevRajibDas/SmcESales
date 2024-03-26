<?php
App::uses('AppModel', 'Model');
/**
 * MessageReceiver Model
 *
 * @property Message $Message
 * @property Receiver $Receiver
 */
class MessageReceiver extends AppModel {


	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(		
		'SalesPerson' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'receiver_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'MessageList' => array(
			'className' => 'MessageList',
			'foreignKey' => 'message_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
