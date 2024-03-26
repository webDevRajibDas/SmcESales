<?php
App::uses('AppModel', 'Model');
/**
 * OpeningBalanceCollection Model
 *
 * @property SalesPerson $SalesPerson
 */
class OpeningBalanceCollection extends AppModel {

	
	
	public function filter($params, $conditions) {

		$conditions = array();
		if (!empty($params['OpeningBalanceCollection.office_id'])) {
			$conditions[] = array('OpeningBalanceCollection.office_id' => $params['OpeningBalanceCollection.office_id']);
		}
		if (!empty($params['OpeningBalanceCollection.territory_id'])) {
			$conditions[] = array('OpeningBalance.territory_id' => $params['OpeningBalanceCollection.territory_id']);
		}
		if (!empty($params['OpeningBalanceCollection.date'])) {
			$conditions[] = array('OpeningBalanceCollection.entry_date' => date('Y-m-d', strtotime($params['OpeningBalanceCollection.date'])));
		}

		return $conditions;
	}
	
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
			'opening_balance_id' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'This field is required.'
				)
			),
			'date_added' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'This field is required.'
				)
			),
			'amount' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'This field is required.'
				)
			),
			/*'total_sales' => array(
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
		'OpeningBalance' => array(
			'className' => 'OpeningBalance',
			'foreignKey' => 'opening_balance_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		/*'FiscalYear' => array(
			'className' => 'FiscalYear',
			'foreignKey' => 'fiscal_year_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),*/
		/*'Office' => array(
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
		),	*/	
		/*'Territory' => array(
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
		)*/
	);		
}

