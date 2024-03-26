<?php
App::uses('AppModel', 'Model');
/**
 * StoreType Model
 *
 * @property Store $Store
 */
class StoreType extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


	public $validate = array(
		'name' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Name field is required.'
					),
			'isUnique' => array(
						'rule' => 'isUnique',
						'message'=> 'Name already exist.'
					),
		)
	); 

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Store' => array(
			'className' => 'Store',
			'foreignKey' => 'store_type_id',
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
