<?php
App::uses('AppModel', 'Model');
/**
 * Brand Model
 *
 * @property Product $Product
 */
class Brand extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
		'name' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Name field required.'
			),
			'isUnique' => array(
						'rule' => 'isUnique',
						'message'=> 'Brand Name already exists.'
			)
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'brand_id',
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
