<?php
App::uses('AppModel', 'Model');
/**
 * DailyStaffwiseProductSalesSummary Model
 *
 * @property SalesPerson $SalesPerson
 * @property Product $Product
 * @property ProductMeasurement $ProductMeasurement
 */
class DailyStaffwiseProductSalesSummary extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'SalesPerson' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'sales_person_id',
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
