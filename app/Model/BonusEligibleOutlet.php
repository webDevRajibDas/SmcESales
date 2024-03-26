<?php
App::uses('AppModel', 'Model');
/**
 * BonusEligibleOutlet Model
 *
 * @property BonusCard $BonusCard
 * @property BonusType $BonusType
 * @property FiscalYear $FiscalYear
 * @property Outlet $Outlet
 */
class BonusEligibleOutlet extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'BonusCard' => array(
			'className' => 'BonusCard',
			'foreignKey' => 'bonus_card_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'BonusType' => array(
			'className' => 'BonusCardType',
			'foreignKey' => 'bonus_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'FiscalYear' => array(
			'className' => 'FiscalYear',
			'foreignKey' => 'fiscal_year_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Outlet' => array(
			'className' => 'Outlet',
			'foreignKey' => 'outlet_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
