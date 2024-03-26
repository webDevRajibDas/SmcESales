<?php
App::uses('AppModel', 'Model');
/**
 * OpeningBalance Model
 *
 * @property SalesPerson $SalesPerson
 */
class OpeningBalance extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed
		public $validate = array(
		   'fiscal_year_id' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'This field is required.'
				)
			),
			'office_id' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'This field is required.'
				)
			),
			/*'territory_id' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'This field is required.'
				)
			),
			'total_sales' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'This field is required.'
				)
			),
			'total_collection' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'This field is required.'
				)
			),
			'total_dposite' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'This field is required.'
				)
			),
			'total_outstanding' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'This field is required.'
				)
			)*/
		);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'FiscalYear' => array(
			'className' => 'FiscalYear',
			'foreignKey' => 'fiscal_year_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'office_id',
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
		'Territory' => array(
			'className' => 'Territory',
			'foreignKey' => 'territory_id',
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

