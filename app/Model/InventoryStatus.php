<?php
App::uses('AppModel', 'Model');
/**
 * InventoryStatus Model
 *
 * @property CurrentInventory $CurrentInventory
 */
class InventoryStatus extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	//public $useTable = 'inventory_status';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
			'name' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'Short Name field is required.'
				)
			)
		);
	
/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'CurrentInventory' => array(
			'className' => 'CurrentInventory',
			'foreignKey' => 'inventory_status_id',
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
