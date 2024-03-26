<?php
App::uses('AppModel', 'Model');
/**
 * District Model
 *
 * @property Thana $Thana
 */
class District extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


	//The Associations below have been created with all possible keys, those that are not needed can be removed
		public $validate = array(
			'name' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'District Name field is required.'
						),
				'isUnique' => array(
							'rule' => 'isUnique',
							'message'=> 'District Name already exist.'
						),
			)
		);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Thana' => array(
			'className' => 'Thana',
			'foreignKey' => 'district_id',
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

	public $belongsTo = array(
		'Division' => array(
			'className' => 'Division',
			'foreignKey' => 'division_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function filter($params, $conditions) {
		$conditions = array();
		if (!empty($params['District.name'])) {
			$conditions[] = array('District.name' => $params['District.name']);
		}
		if (!empty($params['District.division_id'])) {
			$conditions[] = array('Division.id' => $params['District.division_id']);
		}

		return $conditions;
	}
}
