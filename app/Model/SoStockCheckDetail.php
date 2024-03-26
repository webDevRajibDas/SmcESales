<?php
App::uses('AppModel', 'Model');
/**
 * SoStockCheckDetail Model
 *
 * @property SoStockCheck $SoStockCheck
 * @property Product $Product
 */
class SoStockCheckDetail extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'SoStockCheck' => array(
			'className' => 'SoStockCheck',
			'foreignKey' => 'so_stock_check_id',
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
