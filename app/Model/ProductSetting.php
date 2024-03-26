<?php
App::uses('AppModel', 'Model');
/**
 * ProductSetting Model
 *
 * @property Product $Product
 */
class ProductSetting extends AppModel
{

	public $displayField = 'min_qty';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(

		'brand_id' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Product Field is required.'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Product already exists.'
			)
		),
		'product_id' => array(
			'isUnique' => array(
				'rule' => array('checkUnique', array('brand_id', 'product_id'), false),
				'message' => 'Product already exists.',
				'allowEmpty' => true
			)
		),
		'colour' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Colour Field is required.'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Product Colour already exists.'
			)
		),

		'sort' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Field is required.'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Sort must be unique.'
			)
		)
	);
	public function checkUnique($ignoredData, $fields, $or = true)
	{
		return $this->isUnique($fields, $or);
	}
	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Brand' => array(
			'className' => 'Brand',
			'foreignKey' => 'brand_id',
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
		
	);

	// data filter
	public function filter($params, $conditions)
	{
		if (!empty($params['ProductSetting.product_id'])) {
			$conditions = array('ProductSetting.product_id' => $params['ProductSetting.product_id']);
		}
		return $conditions;
	}
}
