<?php
App::uses('AppModel', 'Model');
/**
 * ProductCombination Model
 *
 * @property Product $Product
 */
class ProductPriceSectionV2 extends AppModel {
	public $useTable = 'product_price_section_v2';
	public $displayField = 'min_qty';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
		
		
		'product_price_id' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Product Price Id Field is required.'
			)
		),
		
	);
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'ProductPricesV2' => array(
			'className' => 'ProductPricesV2',
			'foreignKey' => 'product_price_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'dependent' => true
		)
	);
	public $hasMany = array(
		'ProductCombinationsV2' => array(
			'className' => 'ProductCombinationsV2',
			'foreignKey' => 'section_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
            'dependent' => true
		)
	);
	// data filter
	public function filter($params, $conditions) {
		if (!empty($params['ProductCombination.product_id'])) {
            $conditions = array('ProductCombination.product_id' => $params['ProductCombination.product_id']);
        }
        return $conditions;
    }

}
