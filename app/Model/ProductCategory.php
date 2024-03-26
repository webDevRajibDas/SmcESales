<?php
App::uses('AppModel', 'Model');
/**
 * ProductCategory Model
 *
 * @property ProductCategory $ParentProductCategory
 * @property ProductCategory $ChildProductCategory
 * @property Product $Product
 */
class ProductCategory extends AppModel {

/**
 * Display field
 *
 * @var string
 */
 	
 
	public $displayField = 'name';
	
	public $actsAs = array('Tree', 'Containable');

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty')				
			),
			'isUnique' => array(
						'rule' => 'isUnique',
						'message'=> 'Category name already exists.'
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
		'ParentProductCategory' => array(
			'className' => 'ProductCategory',
			'foreignKey' => 'parent_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'ChildProductCategory' => array(
			'className' => 'ProductCategory',
			'foreignKey' => 'parent_id',
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
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'product_category_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => 'order',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		
		/*'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'sub_category_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)*/
	);

}
