<?php
App::uses('AppModel', 'Model');
/**
 * TargetForProductSale Model
 *
 * @property Target $Target
 * @property Period $Period
 * @property Product $Product
 * @property MeasurementUnit $MeasurementUnit
 */
class TargetForProductSale extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Target' => array(
			'className' => 'Target',
			'foreignKey' => 'target_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Period' => array(
			'className' => 'Period',
			'foreignKey' => 'period_id',
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
