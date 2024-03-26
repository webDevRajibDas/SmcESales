<?php
/**
 * UserDoctorVisitPlanListFixture
 *
 */
class UserDoctorVisitPlanListFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'length' => '8', 'key' => 'primary'),
		'user_doctor_visit_plan_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'territory_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'market_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
		'doctor_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'visit_plan_date' => array('type' => 'date', 'null' => true, 'default' => null),
		'is_out_of_plan' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'visit_status' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => '50'),
		'visited_date' => array('type' => 'date', 'null' => true, 'default' => null),
		'created_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_by' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'updated_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			
		),
		'tableParameters' => array()
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '',
			'user_doctor_visit_plan_id' => 1,
			'territory_id' => 1,
			'market_id' => 1,
			'doctor_id' => 1,
			'visit_plan_date' => '2018-05-29',
			'is_out_of_plan' => 1,
			'visit_status' => 'Lorem ipsum dolor sit amet',
			'visited_date' => '2018-05-29',
			'created_at' => '2018-05-29 12:21:58',
			'created_by' => 1,
			'updated_at' => '2018-05-29 12:21:58'
		),
	);

}
