<?php
App::uses('AppModel', 'Model');
/**
 * UserDoctorVisitPlanList Model
 *
 * @property UserDoctorVisitPlan $UserDoctorVisitPlan
 * @property Territory $Territory
 * @property Market $Market
 * @property Doctor $Doctor
 */
class UserDoctorVisitPlanList extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'UserDoctorVisitPlan' => array(
			'className' => 'UserDoctorVisitPlan',
			'foreignKey' => 'user_doctor_visit_plan_id',
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
		'Doctor' => array(
			'className' => 'Doctor',
			'foreignKey' => 'doctor_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
