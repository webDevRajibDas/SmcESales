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
class DoctorVisit extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';
		
	// data filter
	public function filter($params, $conditions) {
        $conditions = array();
		if (!empty($params['DoctorVisit.office_id'])) {
            $conditions[] = array('Territory.office_id' => $params['DoctorVisit.office_id']);
        }
		if (!empty($params['DoctorVisit.territory_id'])) {
            $conditions[] = array('DoctorVisit.territory_id' => $params['DoctorVisit.territory_id']);
        }
		if (!empty($params['DoctorVisit.market_id'])) {
            $conditions[] = array('DoctorVisit.market_id' => $params['DoctorVisit.market_id']);
        }				
        if (!empty($params['DoctorVisit.doctor_qualification_id'])) {
            $conditions[] = array('Doctor.doctor_qualification_id' => $params['DoctorVisit.doctor_qualification_id']);
        }				
        if (!empty($params['DoctorVisit.doctor_type_id'])) {
            $conditions[] = array('Doctor.doctor_type_id' => $params['DoctorVisit.doctor_type_id']);
        }   
		if (isset($params['DoctorVisit.date_from'])!='') {
            $conditions[] = array('DoctorVisit.visit_date >=' => Date('Y-m-d',strtotime($params['DoctorVisit.date_from'])));
        }
		if (isset($params['DoctorVisit.date_to'])!='') {
            $conditions[] = array('DoctorVisit.visit_date <=' => Date('Y-m-d',strtotime($params['DoctorVisit.date_to'])));
        }	
        return $conditions;
    }
	
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Doctor' => array(
			'className' => 'Doctor',
			'foreignKey' => 'doctor_id',
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
		)
	);
	
/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'DoctorVisitDetail' => array(
			'className' => 'DoctorVisitDetail',
			'foreignKey' => 'doctor_visit_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);	
	
	
}
