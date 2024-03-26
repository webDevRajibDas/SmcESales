<?php
App::uses('AppModel', 'Model');
/**
 * PriceOpenProduct Model
 *
 * @property PriceOpenProductCategory $PriceOpenProductCategory
 * @property Brand $Brand
 * @property Variant $Variant
 * @property BaseMeasurementUnit $BaseMeasurementUnit
 */
class PriceOpenProduct extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

	// data filter
	
	
	// set validation rules
	public $validate = array(
		'name' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Name field is required.'
					),
			'isUnique' => array(
						'rule' => 'isUnique',
						'message'=> 'PriceOpenProduct Name already exists.'
			)
		)
	);
	
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
			'fields' => 'Product.name',
			'order' => ''
		),
		
	);
	
	var $hasMany = array(
		
	);
	
}
