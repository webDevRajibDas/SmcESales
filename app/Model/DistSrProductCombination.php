<?php
App::uses('AppModel', 'Model');
/**
 * ProductCombination Model
 *
 * @property Product $Product
 */
class DistSrProductCombination extends AppModel {

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
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'DistSrProductPrice' => array(
			'className' => 'DistSrProductPrice',
			'foreignKey' => 'product_price_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'dependent' => true
		),
		'DistSrCombination' => array(
			'className' => 'DistSrCombination',
			'foreignKey' => 'combination_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
            'dependent' => true
		),
		'Childrel' => array(
			'className' => 'DistSrProductCombination',
			'foreignKey' => 'parent_slab_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
            'dependent' => true
		)
	);
	public $hasMany = array(
		'Parent' => array(
			'className' => 'DistSrProductCombination',
			'foreignKey' => 'parent_slab_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
            'dependent' => true
		)
	);
	// data filter
	public function filter($params, $conditions) {
		if (!empty($params['DistSrProductCombination.product_id'])) {
            $conditions = array('DistSrProductCombination.product_id' => $params['DistSrProductCombination.product_id']);
        }
        return $conditions;
    }

}
