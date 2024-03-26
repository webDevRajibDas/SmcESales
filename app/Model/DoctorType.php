<?php
App::uses('AppModel', 'Model');
/**
 * DoctorType Model
 *
 * @property Doctor $Doctor
 */
class DoctorType extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'title';

	public $validate = array(
		'title' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Title field required.'
			),
			'isUnique' => array(
						'rule' => 'isUnique',
						'message'=> 'Title already exists.'
			)
		)
	);
/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Doctor' => array(
			'className' => 'Doctor',
			'foreignKey' => 'doctor_type_id',
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
