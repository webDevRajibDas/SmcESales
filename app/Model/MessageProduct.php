<?php
App::uses('AppModel', 'Model');
/**
 * MessageProduct Model
 *
 * @property Message $Message
 * @property Product $Product
 */
class MessageProduct extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(		
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'product_id',
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
