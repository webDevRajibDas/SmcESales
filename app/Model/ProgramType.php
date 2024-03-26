<?php
App::uses('AppModel', 'Model');
/**
 * ProgramType Model
 *
 * @property Program $Program
 */
class ProgramType extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';
	
/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Program' => array(
			'className' => 'Program',
			'foreignKey' => 'program_type_id',
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
