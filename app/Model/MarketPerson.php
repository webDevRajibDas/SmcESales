<?php
App::uses('AppModel', 'Model');
/**
 * MarketPerson Model
 *
 * @property Market $Market
 * @property SalesPerson $SalesPerson
 */
class MarketPerson extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed
	/*======  validation  ===========*/
	public $validate = array(
		'market_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Market Id field is required.'
			)
		),
		'sales_person_id'  => array(
			'mustNotEmpty'	=> array(
				'rule'		=> 'notEmpty',
				'message'	=> 'Sales Person field is required.'
			)
		)
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Market' => array(
			'className' => 'Market',
			'foreignKey' => 'market_id',
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
