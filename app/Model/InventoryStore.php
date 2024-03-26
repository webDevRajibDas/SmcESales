<?php
App::uses('AppModel', 'Model');
/**
 * InventoryStore Model
 *
 * @property Reference $Reference
 * @property CurrentInventory $CurrentInventory
 */
class InventoryStore extends AppModel {

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
							'message'=> 'Name field is required.'
				)
			),
			'store_type' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'Store Type field is required.'
				)
			)
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	/* public $belongsTo = array(
		'Reference' => array(
			'className' => 'Reference',
			'foreignKey' => 'reference_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	); */

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'CurrentInventory' => array(
			'className' => 'CurrentInventory',
			'foreignKey' => 'inventory_store_id',
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
