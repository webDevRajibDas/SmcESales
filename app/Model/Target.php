<?php
App::uses('AppModel', 'Model');
/**
 * Target Model
 *
 * @property OfficeSalesPerson $OfficeSalesPerson
 * @property TargetType $TargetType
 * @property TargetForOther $TargetForOther
 * @property TargetForProductSale $TargetForProductSale
 */
class Target extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'OfficeSalesPerson' => array(
			'className' => 'OfficeSalesPerson',
			'foreignKey' => 'office_sales_person_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'TargetType' => array(
			'className' => 'TargetType',
			'foreignKey' => 'target_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'TargetForOther' => array(
			'className' => 'TargetForOther',
			'foreignKey' => 'target_id',
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
		'TargetForProductSale' => array(
			'className' => 'TargetForProductSale',
			'foreignKey' => 'target_id',
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
