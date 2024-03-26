<?php
App::uses('AppModel', 'Model');
/**
 * TerritoryPerson Model
 *
 * @property Territory $Territory
 * @property SalesPerson $SalesPerson
 */
class TerritoryPerson extends AppModel {

	
	// set validation
	
	public $validate = array(
		'territory_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Territory field is required.'
					)
		),
		'sales_person_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Sales Person field is required.'
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
		'Territory' => array(
			'className' => 'Territory',
			'foreignKey' => 'territory_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'SalesPerson' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'sales_person_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
