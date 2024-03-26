<?php
App::uses('AppModel', 'Model');
/**
 * SessionType Model
 *
 * @property Session $Session
 */
class SessionType extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Session' => array(
			'className' => 'Session',
			'foreignKey' => 'session_type_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
		)
	);

}
