<?php
App::uses('AppModel', 'Model');
/**
 * MessageCategory Model
 *
 * @property MessageList $MessageList
 */
class MessageCategory extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'title';


	//The Associations below have been created with all possible keys, those that are not needed can be removed
	
	public $validate = array(
		'title' => array(
			'notMustBeEmpty' => array(
				'rule' 		=> 'notEmpty',
				'message'   => 'Category title field required.'
			),
			'isUnique' => array(
						'rule' => 'isUnique',
						'message'=> 'Category already exists.'
			)
		)
	);
	
/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'MessageList' => array(
			'className' => 'MessageList',
			'foreignKey' => 'message_category_id',
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
