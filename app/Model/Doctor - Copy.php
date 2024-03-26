<?php
App::uses('AppModel', 'Model');
/**
 * Doctor Model
 *
 * @property DoctorQualification $DoctorQualification
 * @property DoctorType $DoctorType
 * @property Territory $Territory
 * @property Market $Market
 * @property Outlet $Outlet
 */
class Doctor extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';
	
	// data filter
	public function filter($params, $conditions) {
        $conditions = array();
		if (!empty($params['Doctor.office_id'])) {
            $conditions[] = array('Territory.office_id' => $params['Doctor.office_id']);
        }
		if (!empty($params['Doctor.territory_id'])) {
            $conditions[] = array('Doctor.territory_id' => $params['Doctor.territory_id']);
        }
		if (!empty($params['Doctor.market_id'])) {
            $conditions[] = array('Doctor.market_id' => $params['Doctor.market_id']);
        }				
        if (!empty($params['Doctor.doctor_qualification_id'])) {
            $conditions[] = array('Doctor.doctor_qualification_id' => $params['Doctor.doctor_qualification_id']);
        }				
        if (!empty($params['Doctor.doctor_type_id'])) {
            $conditions[] = array('Doctor.doctor_type_id' => $params['Doctor.doctor_type_id']);
        }        	
        return $conditions;
    }
	
	
	public $validate = array(
		'name' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Title field required.'
			)
		),
		'doctor_qualification_id' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Doctor qualification_id field required.'
			)
		),
		'doctor_type_id' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Doctor type_id field required.'
			)
		),
		'gender' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Gender field required.'
			)
		),
		'outlet_id' => array(
			'notMustBeEmpty' => array(
				'rule'   => 'notEmpty',
				'message'=> 'Outlet field required.'
			)
		)
	);
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'DoctorQualification' => array(
			'className' => 'DoctorQualification',
			'foreignKey' => 'doctor_qualification_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'DoctorType' => array(
			'className' => 'DoctorType',
			'foreignKey' => 'doctor_type_id',
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
		'Market' => array(
			'className' => 'Market',
			'foreignKey' => 'market_id',
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
	
	/**
 * hasOne associations
 *
 * @var array
 */
	public $hasOne = array(		
		'Program' => array(
			'className' => 'Program',
			'foreignKey' => 'doctor_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
