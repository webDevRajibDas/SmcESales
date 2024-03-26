<?php
/**
 * UserDoctorVisitPlanFixture
 *
 */
class UserDoctorVisitPlanFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'user_doctor_visit_plan';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'fiscal_year_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
		'user_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 10),
		'created_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_by' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
		'updated_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'updated_by' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
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
			'id' => 1,
			'fiscal_year_id' => 1,
			'user_id' => 'Lorem ip',
			'created_at' => '2018-05-29 12:19:24',
			'created_by' => 1,
			'updated_at' => '2018-05-29 12:19:24',
			'updated_by' => 1
		),
	);

}
