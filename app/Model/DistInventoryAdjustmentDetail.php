<?php
App::uses('AppModel', 'Model');


class DistInventoryAdjustmentDetail extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'DistInventoryAdjustment' => array(
			'className' => 'DistInventoryAdjustment',
			'foreignKey' => 'dist_inventory_adjustment_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'DistCurrentInventory' => array(
			'className' => 'DistCurrentInventory',
			'foreignKey' => 'dist_current_inventory_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
