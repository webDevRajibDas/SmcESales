<?php
App::uses('AppModel', 'Model');
/**
 * SessionDetail Model
 *
 * @property SessionDetail $SessionDetail
 */
class SessionDetail extends AppModel {

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
		'Session' => array(
			'className' => 'Session',
			'foreignKey' => 'session_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
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
