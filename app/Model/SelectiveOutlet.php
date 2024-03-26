<?php
App::uses('AppModel', 'Model');
/**
 * Brand Model
 *
 * @property Product $Product
 */
class SelectiveOutlet extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';


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
	public $belongsTo = array(
		'OutletCategory' => array(
			'className' => 'OutletCategory',
			'foreignKey' => 'outlet_category_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

}
