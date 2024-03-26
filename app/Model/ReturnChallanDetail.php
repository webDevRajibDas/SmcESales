<?php
App::uses('AppModel', 'Model');
/**
 * ChallanDetail Model
 *
 * @property Challan $Challan
 * @property Product $Product
 * @property MeasurementUnit $MeasurementUnit
 * @property Batch $Batch
 */
class ReturnChallanDetail extends AppModel{


	//The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'ReturnChallan' => array(
			'className' => 'ReturnChallan',
			'foreignKey' => 'challan_id',
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
		'VirtualProduct' => array(
			'className' => 'Product',
			'foreignKey' => '',
			'conditions' => 'VirtualProduct.id=ReturnChallanDetail.virtual_product_id',
			'fields' => '',
			'order' => ''
		),
		'MeasurementUnit' => array(
			'className' => 'MeasurementUnit',
			'foreignKey' => 'measurement_unit_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
