<?php
App::uses('AppModel', 'Model');
/**
 * MonthlyOfficewiseProductSalesSummary Model
 *
 * @property FiscalYear $FiscalYear
 * @property Month $Month
 * @property Office $Office
 * @property Product $Product
 * @property ProductMeasurement $ProductMeasurement
 */
class MonthlyOfficewiseProductSalesSummary extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'FiscalYear' => array(
			'className' => 'FiscalYear',
			'foreignKey' => 'fiscal_year_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Month' => array(
			'className' => 'Month',
			'foreignKey' => 'month_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
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
