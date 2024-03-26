<?php
App::uses('AppModel', 'Model');
/**
 * ProductCombination Model
 *
 * @property Product $Product
 */
class ProductPriceDbForSlabs extends AppModel {

public $displayField = 'min_qty';
	public $useTable = 'product_price_db_slabs';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
		'product_combination_id' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Product Combination Id Field is required.'
			)
		),
	);
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		
		'ProductCombinationsV2' => array(
			'className' => 'ProductCombinationsV2',
			'foreignKey' => 'product_combination_id',
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
