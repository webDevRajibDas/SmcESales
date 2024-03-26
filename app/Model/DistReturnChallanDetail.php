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
class DistReturnChallanDetail extends AppModel {

	public $useDbConfig = 'default_06';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'DistReturnChallan' => array(
			'className' => 'DistReturnChallan',
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
		'MeasurementUnit' => array(
			'className' => 'MeasurementUnit',
			'foreignKey' => 'measurement_unit_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
