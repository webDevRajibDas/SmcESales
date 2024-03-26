<?php
App::uses('AppModel', 'Model');
/**
 * InventoryAdjustmentDetail Model
 *
 * @property InventoryAdjustment $InventoryAdjustment
 * @property CurrentInventory $CurrentInventory
 */
class InventoryAdjustmentDetail extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'InventoryAdjustment' => array(
			'className' => 'InventoryAdjustment',
			'foreignKey' => 'inventory_adjustment_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CurrentInventory' => array(
			'className' => 'CurrentInventory',
			'foreignKey' => 'current_inventory_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
