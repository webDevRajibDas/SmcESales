<?php
App::uses('AppModel', 'Model');
/**
 * ProductPrice Model
 *
 * @property Product $Product
 * @property MeasurementUnit $MeasurementUnit
 * @property Institute $Institute
 */
class DistOpenCombinationProduct extends AppModel {


/**
 * belongsTo associations
 *
 * @var array
 */
	
	/*public $hasMany = array(
		'OpenCombination' => array(
			'className' => 'OpenCombination',
			'foreignKey' => 'combination_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'dependent' => true
		)
	);*/
	
	public $belongsTo = array(
		'DistOpenCombination' => array(
			'className' => 'DistOpenCombination',
			'foreignKey' => 'combination_id',
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
		)
	);
	
}
