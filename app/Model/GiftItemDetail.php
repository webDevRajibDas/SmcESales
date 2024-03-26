<?php
App::uses('AppModel', 'Model');
/**
 * GiftItemDetail Model
 *
 * @property GiftItemDetail $GiftItemDetail
 */
class GiftItemDetail extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';


	//The Associations below have been created with all possible keys, those that are not needed can be removed
		
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'GiftItem' => array(
			'className' => 'GiftItem',
			'foreignKey' => 'gift_item_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
