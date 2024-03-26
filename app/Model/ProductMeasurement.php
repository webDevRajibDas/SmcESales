<?php
App::uses('AppModel', 'Model');
/**
 * ProductMeasurement Model
 *
 * @property Product $Product
 * @property MeasurementUnit $MeasurementUnit
 */
class ProductMeasurement extends AppModel {
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
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
