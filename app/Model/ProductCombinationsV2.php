<?php
App::uses('AppModel', 'Model');
/**
 * ProductCombination Model
 *
 * @property Product $Product
 */
class ProductCombinationsV2 extends AppModel {
	public $useTable = 'product_combinations_v2';
	public $displayField = 'min_qty';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
		'product_id' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Product Field is required.'
			),
			/*'isUnique' => array(
					'rule' => 'isUnique',
					'message'=> 'Product already exists.'
			)*/
		),
		'indivisual_or_total' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Indivisual or Total Field is required.'
			)
		),
		'min_qty' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Mim Qty Field is required.'
			)
		),
		'min_total_qty' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Mim Total Qty Field is required.'
			)
		),
		'price' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Price Field is required.'
			)
		),
		'effective_date' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Effective date Field is required.'
			)
		)
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
		),
	);
	public $hasMany = array(
		
		'ProductPriceOtherForSlabsV2' => array(
			'className' => 'ProductPriceOtherForSlabsV2',
			'foreignKey' => 'product_combination_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
            'dependent' => true
		)
	);
	public $hasOne=array(
		'ProductPriceDbForSlabs' => array(
			'className' => 'ProductPriceDbForSlabs',
			'foreignKey' => 'product_combination_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
            'dependent' => true
		),
		);
	// data filter
	public function filter($params, $conditions) {
		if (!empty($params['ProductCombination.product_id'])) {
            $conditions = array('ProductCombination.product_id' => $params['ProductCombination.product_id']);
        }
        return $conditions;
    }

}
