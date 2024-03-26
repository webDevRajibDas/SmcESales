<?php
App::uses('AppModel', 'Model');
/**
 * DoctorQualification Model
 *
 * @property Doctor $Doctor
 */
class DoctorQualification extends AppModel {

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
		),
		'doctor_type_id' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Type field required.'
			)
		)
	);
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'DoctorType' => array(
			'className' => 'DoctorType',
			'foreignKey' => 'doctor_type_id',
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
		'Doctor' => array(
			'className' => 'Doctor',
			'foreignKey' => 'doctor_qualification_id',
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
