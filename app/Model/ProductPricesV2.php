<?php
App::uses('AppModel', 'Model');
/**
 * ProductPrice Model
 *
 * @property Product $Product
 * @property MeasurementUnit $MeasurementUnit
 * @property Institute $Institute
 */
class ProductPricesV2 extends AppModel {
	public $useTable = 'product_prices_v2';
	public function filter($params, $conditions) {
		$conditions = array();
		
		if (!empty($params['Product.product_category_id'])) {
            $conditions[] = array('ProductPrice.id' => $params['Product.product_category_id']);
        }
		if (!empty($params['ProductPrice.institute_id'])) {
            $conditions[] = array('ProductPrice.institute_id' => $params['ProductPrice.institute_id']);
        }
		if (!empty($params['ProductPrice.target_custommer'])) {
            $conditions[] = array('ProductPrice.target_custommer' => $params['ProductPrice.target_custommer']);
        }
        return $conditions;
    }
	
	
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
		'product_id' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Product is required'
			)
		),
		'target_custommer' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Target Customer is required'
			)
		),
		'measurement_unit_id' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Measurement Unit is required'
			)
		),
		'institute_id' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Institute is required'
			)
		),
		'effective_date' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Effective Date is required'
			)
		),
		'general_price' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'General Price is required'
			)
		),
		'end_date' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'End Date is required'
			)
		),
		'name' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Name is required'
			)
		)
		
	);
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

	);
	
	public $hasMany = array(
		'ProductCombinationsV2' => array(
			'className' => 'ProductCombinationsV2',
			'foreignKey' => 'product_price_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'dependent' => true
		),
		'ProductPriceSectionV2' => array(
			'className' => 'ProductPriceSectionV2',
			'foreignKey' => 'product_price_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'dependent' => true
		),
	);
	
	// data filter
	
}
