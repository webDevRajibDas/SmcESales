<?php
/**
 * DistSrCheckInOutFixture
 *
 */
class DistSrCheckInOutFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'dist_sr_check_in_out';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => '8', 'key' => 'primary'),
		'office_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
		'db_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
		'sr_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
		'check_in_time' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'check_out_time' => array('type' => 'datetime', 'null' => true, 'default' => null),
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
			'office_id' => 1,
			'db_id' => 1,
			'sr_id' => 1,
			'check_in_time' => '2020-10-14 15:21:30',
			'check_out_time' => '2020-10-14 15:21:30'
		),
	);

}
