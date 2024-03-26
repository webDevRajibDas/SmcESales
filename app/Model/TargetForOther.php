<?php
App::uses('AppModel', 'Model');
/**
 * TargetForOther Model
 *
 * @property Target $Target
 */
class TargetForOther extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Target' => array(
			'className' => 'Target',
			'foreignKey' => 'target_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
