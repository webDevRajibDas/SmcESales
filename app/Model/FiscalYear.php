<?php
App::uses('AppModel', 'Model');
/**
 * FiscalYear Model
 *
 * @property Month $Month
 */
class FiscalYear extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

	
	public $validate = array(
		'year_code' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Year code field is required.'
					),
			'isUnique' => array(
						'rule' => 'isUnique',
						'message'=> 'Year code already exist.'
					),
		),
		'start_date' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Start date field is required.'
					)
		),
		'end_date' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'End date field is required.'
					)
		)
	);
	
/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Month' => array(
			'className' => 'Month',
			'foreignKey' => 'fiscal_year_id',
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
		'SaleTarget' => array(
			'className' => 'SaleTarget',
			'foreignKey' => 'fiscal_year_id',
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
