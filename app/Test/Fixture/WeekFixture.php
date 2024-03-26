<?php
/**
 * WeekFixture
 *
 */
class WeekFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'length' => '8', 'key' => 'primary'),
		'week_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => '100'),
		'start_date' => array('type' => 'date', 'null' => false, 'default' => null),
		'end_date' => array('type' => 'date', 'null' => false, 'default' => null),
		'month_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'created_at' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'created_by' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'updated_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'updated_by' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => '4'),
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
			'week_name' => 'Lorem ipsum dolor sit amet',
			'start_date' => '2018-07-16',
			'end_date' => '2018-07-16',
			'month_id' => 1,
			'created_at' => '2018-07-16 19:31:55',
			'created_by' => 1,
			'updated_at' => '2018-07-16 19:31:55',
			'updated_by' => 1
		),
	);

}
