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
class Product extends AppModel
{

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'name';

	// data filter
	public function filter($params, $conditions)
	{
		$conditions = array();
		if (!empty($params['Product.name'])) {
			$conditions[] = array('Product.name LIKE' => '%' . $params['Product.name'] . '%');
		}
		if (!empty($params['Product.product_category_id'])) {
			$conditions[] = array('Product.product_category_id' => $params['Product.product_category_id']);
		}
		if (!empty($params['Product.brand_id'])) {
			$conditions[] = array('Product.brand_id' => $params['Product.brand_id']);
		}
		if (!empty($params['Product.variant_id'])) {
			$conditions[] = array('Product.variant_id' => $params['Product.variant_id']);
		}
		if (!empty($params['Product.base_measurement_unit_id'])) {
			$conditions[] = array('Product.base_measurement_unit_id' => $params['Product.base_measurement_unit_id']);
		}
		if (!empty($params['Product.product_type_id'])) {
			$conditions[] = array('Product.product_type_id' => $params['Product.product_type_id']);
		}
		if (!empty($params['Product.group_id'])) {
			$conditions[] = array('Product.group_id' => $params['Product.group_id']);
		}
		if (!empty($params['Product.is_distributor_product'])) {
			$conditions[] = array('Product.is_distributor_product' => $params['Product.is_distributor_product']);
		}
		return $conditions;
	}

	// set validation rules
	public $validate = array(
		'name' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Name field is required.'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Product Name already exists.'
			)
		),
		'product_category_id' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Product category field is required.'
			)
		),
		'brand_id' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Brand field is required.'
			)
		),
		'variant_id' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Variant field is required.'
			)
		),
		'group_id' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Group field is required.'
			)
		),
		'source' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Source field is required.'
			)
		),

		'order' => array(
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Order must be unique.'
			)
		),

		'base_measurement_unit_id' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Base unit field is required.'
			)
		),
		'sales_measurement_unit_id' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Sales unit field is required.'
			)
		),
		'challan_measurement_unit_id' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Challan unit field is required.'
			)
		),
		'return_measurement_unit_id' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Challan unit field is required.'
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
		'Parent' => array(
			'className' => 'Product',
			'foreignKey' => 'parent_id',
			'conditions' => '',
			'order' => ''
		),
		'ProductCategory' => array(
			'className' => 'ProductCategory',
			'foreignKey' => 'product_category_id',
			'conditions' => '',
			'fields' => 'ProductCategory.id,ProductCategory.name',
			'order' => ''
		),
		'Brand' => array(
			'className' => 'Brand',
			'foreignKey' => 'brand_id',
			'conditions' => '',
			'fields' => 'Brand.id,Brand.name',
			'order' => ''
		),
		'Variant' => array(
			'className' => 'Variant',
			'foreignKey' => 'variant_id',
			'conditions' => '',
			'fields' => 'Variant.id,Variant.name',
			'order' => ''
		),
		'BaseMeasurementUnit' => array(
			'className' => 'MeasurementUnit',
			'foreignKey' => 'base_measurement_unit_id',
			'conditions' => '',
			'fields' => 'id,name',
			'order' => ''
		),
		'SalesMeasurementUnit' => array(
			'className' => 'MeasurementUnit',
			'foreignKey' => 'sales_measurement_unit_id',
			'conditions' => '',
			'fields' => 'id,name',
			'order' => ''
		),
		'ChallanMeasurementUnit' => array(
			'className' => 'MeasurementUnit',
			'foreignKey' => 'challan_measurement_unit_id',
			'conditions' => '',
			'fields' => 'id,name',
			'order' => ''
		),
		'ReturnMeasurementUnit' => array(
			'className' => 'MeasurementUnit',
			'foreignKey' => 'return_measurement_unit_id',
			'conditions' => '',
			'fields' => 'id,name',
			'order' => ''
		),
		'ProductType' => array(
			'className' => 'ProductType',
			'foreignKey' => 'product_type_id',
			'conditions' => '',
			'fields' => 'id,name',
			'order' => ''
		),
		'Group' => array(
			'className' => 'ProductGroup',
			'foreignKey' => 'group_id',
			'conditions' => '',
			'fields' => 'id,name',
			'order' => ''
		)
	);

	var $hasMany = array(
		'ProductMeasurement' => array(
			'className' => 'ProductMeasurement',
			'foreignKey' => 'product_id',
			'dependent' => true,
		),
		'SaleTarget' => array(
			'className' => 'SaleTarget',
			'foreignKey' => 'product_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'MemoDetail' => array(
			'className' => 'MemoDetail',
			'foreignKey' => 'product_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'PriceOpenProduct' => array(
			'className' => 'PriceOpenProduct',
			'foreignKey' => 'product_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'ProductFractionSlab' => array(
			'className' => 'ProductFractionSlab',
			'foreignKey' => 'product_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
}
