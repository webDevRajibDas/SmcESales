<?php
App::uses('AppModel', 'Model');
/**
 * AchievementBallenceLog Model
 *
 * @property Territory $Territory
 * @property FiscalYear $FiscalYear
 * @property Month $Month
 */
class AchievementBallenceLog extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'AchievementBallenceLog';


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
		'FiscalYear' => array(
			'className' => 'FiscalYear',
			'foreignKey' => 'fiscal_year_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Month' => array(
			'className' => 'Month',
			'foreignKey' => 'month_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
