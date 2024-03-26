<?php
App::uses('AppModel', 'Model');
/**
 * BonusCard Model
 *
 * @property FiscalYear $FiscalYear
 * @property BonusCardType $BonusCardType
 * @property Product $Product
 */
class BonusCard extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

	// data filter
	public function filter($params, $conditions) {
        $conditions = array();
		if (!empty($params['BonusCard.name'])) {
            $conditions[] = array('BonusCard.name' => $params['BonusCard.name']);
        }
		if (!empty($params['BonusCard.fiscal_year_id'])) {
            $conditions[] = array('BonusCard.fiscal_year_id' => $params['BonusCard.fiscal_year_id']);
        }				
        if (!empty($params['BonusCard.bonus_card_type_id'])) {
            $conditions[] = array('BonusCard.bonus_card_type_id' => $params['BonusCard.bonus_card_type_id']);
        }				
        if (!empty($params['BonusCard.product_id'])) {
            $conditions[] = array('BonusCard.product_id' => $params['BonusCard.product_id']);
        }	       				
        return $conditions;
    }

	public $validate = array(
			'name' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'Name field is required.'
				),
				'isUnique' => array(
							'rule' => 'isUnique',
							'message'=> 'Name already exists.'
				)
			),
			'fiscal_year_id' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'Fiscal Year field is required.'
				)
			),
			'bonus_card_type_id' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'Bonus card type field is required.'
				)
			),
			'product_id' => array(
	            'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'Product field is required.'
				),
				'unique' => array(
	                'rule' => array('checkUnique', array('product_id','bonus_card_type_id','fiscal_year_id')),
	                'message' => 'This Product,Bonus card type and Fiscal Year combination has been already used.',
	            )
	        ),
			'min_qty_per_memo' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'Minimum quantity per memo field is required.'
				),
				'numeric' => array(
                	'rule' => array('numeric'),
            	)
			),
			'min_qty_per_year' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'Minimum quantity per year field is required.'
				),
				'numeric' => array(
                	'rule' => array('numeric'),
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
		'FiscalYear' => array(
			'className' => 'FiscalYear',
			'foreignKey' => 'fiscal_year_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'BonusCardType' => array(
			'className' => 'BonusCardType',
			'foreignKey' => 'bonus_card_type_id',
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
