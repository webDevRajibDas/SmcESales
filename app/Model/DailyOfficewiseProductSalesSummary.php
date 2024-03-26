<?php
App::uses('AppModel', 'Model');
/**
 * DailyOfficewiseProductSalesSummary Model
 *
 * @property Office $Office
 * @property Product $Product
 * @property ProductMeasurement $ProductMeasurement
 */
class DailyOfficewiseProductSalesSummary extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'office_id',
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
		'ProductMeasurement' => array(
			'className' => 'ProductMeasurement',
			'foreignKey' => 'product_measurement_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
