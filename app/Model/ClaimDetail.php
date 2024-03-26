<?php
App::uses('AppModel', 'Model');
/**
 * ClaimDetail Model
 *
 * @property Claim $Claim
 * @property Product $Product
 * @property MeasurementUnit $MeasurementUnit
 * @property InventoryStatus $InventoryStatus
 */
class ClaimDetail extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Claim' => array(
			'className' => 'Claim',
			'foreignKey' => 'claim_id',
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
		),


	);
}
