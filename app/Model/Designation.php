<?php
App::uses('AppModel', 'Model');
/**
 * Designation Model
 *
 * @property SalesPerson $SalesPerson
 */
class Designation extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed
		public $validate = array(
			'designation_name' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'Designation Name field is required.'
				),
				'isUnique' => array(
							'rule' => 'isUnique',
							'message'=> 'Designation Name already exist.'
				),
			)
		);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'SalesPerson' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'designation_id',
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
