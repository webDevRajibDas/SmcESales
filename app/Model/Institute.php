<?php
App::uses('AppModel', 'Model');
/**
 * Institute Model
 *
 * @property ProductPrice $ProductPrice
 * @property Project $Project
 */
class Institute extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


	//The Associations below have been created with all possible keys, those that are not needed can be removed
		public $validate = array(
			'short_name' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'Short Name field is required.'
				)
			),
			'name'		=> array(
				'mustNotEmpty'	=> array(
					'rule'		=> 'notEmpty',
					'message'	=> 'Institute Name field is required.'
				)
			),
			'type'		=> array(
				'mustNotEmpty'	=> array(
					'rule'		=> 'notEmpty',
					'message'	=> 'Institute Type field is required.'
				)
			),
			'address'	=> array(
				'mustNotEmpty'	=> array(
						'rule'		=> 'notEmpty',
						'message'	=> 'Address field is required.'
				)
			),
			'email'	=> array(
				'validEmail'	=> array(
						'rule'		=> array('email'),
						'message'	=> 'Please enter a valid email address.',
						'allowEmpty' => true
				)
			),
			'telephone'	=> array(
				'mustNotEmpty'	=> array(
						'rule'		=> 'notEmpty',
						'message'	=> 'Telephone field is required.'
				),
				 'numericNumber' => array(
						'rule' => 'numeric',
						'message' => 'Telephone number should be numeric'
				)
				
			),
			'contactname'	=> array(
				'mustNotEmpty'	=> array(
						'rule'		=> 'notEmpty',
						'message'	=> 'Contact Name field is required.'
				)
			)
		);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'ProductPrice' => array(
			'className' => 'ProductPrice',
			'foreignKey' => 'institute_id',
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
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'institute_id',
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
	// data filter
	public function admin_filter($params, $conditions) {
        if (!empty($params['Institute.short_name'])) {
            $conditions = array('Institute.short_name' => $params['Institute.short_name']);
        }
		if (!empty($params['Institute.name'])) {
            $conditions = array('Institute.name' => $params['Institute.name']);
        }
		if (!empty($params['Institute.type'])) {
            $conditions = array('Institute.type' => $params['Institute.type']);
        }
		if (!empty($params['Institute.telephone'])) {
            $conditions = array('Institute.telephone' => $params['Institute.telephone']);
        }
        return $conditions;
    }

}
