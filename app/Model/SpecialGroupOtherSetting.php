<?php
App::uses('AppModel', 'Model');
/**
 * SpecialGroupOtherSetting Model
 *
 * @property SpecialGroup $SpecialGroup
 */
class SpecialGroupOtherSetting extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'SpecialGroup' => array(
			'className' => 'SpecialGroup',
			'foreignKey' => 'special_group_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
