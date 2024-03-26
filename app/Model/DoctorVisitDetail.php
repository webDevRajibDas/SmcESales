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
class DoctorVisitDetail extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';
		
/**
 * belongsTo associations
 *
 * @var array
 */
	
	public $belongsTo = array(
		'DoctorVisit' => array(
			'className' => 'DoctorVisit',
			'foreignKey' => 'doctor_visit_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
}
