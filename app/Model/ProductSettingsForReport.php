<?php
App::uses('AppModel', 'Model');
/**
 * Product Model
 *
 * @property ProductCategory $ProductCategory
 * @property Brand $Brand
 * @property Variant $Variant
 * @property BaseMeasurementUnit $BaseMeasurementUnit
 */
class ProductSettingsForReport extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

	// data filter
	public function filter($params, $conditions) {
    }
	
	// set validation rules
	public $validate = array();
	
	//The Associations below have been created with all possible keys, those that are not needed can be removed

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
		)
	);
	
	var $hasMany = array();
	
}
