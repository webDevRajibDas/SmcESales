<?php
App::uses('AppModel', 'Model');
/**
 * BonusTarget Model
 *
 */
class BonusTarget extends AppModel {

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'office_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Territory' => array(
			'className' => 'Territory',
			'foreignKey' => 'territory_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Bonus' => array(
			'className' => 'Bonus',
			'foreignKey' => 'bonus_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

}
