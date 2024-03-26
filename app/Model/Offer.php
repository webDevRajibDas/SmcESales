<?php
App::uses('AppModel', 'Model');
/**
 * Offer Model
 *
 * @property Reference $Reference
 * @property Combination $Combination
 * @property Product $Product
 */
class Offer extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Reference' => array(
			'className' => 'Reference',
			'foreignKey' => 'reference_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Combination' => array(
			'className' => 'Combination',
			'foreignKey' => 'combination_id',
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
