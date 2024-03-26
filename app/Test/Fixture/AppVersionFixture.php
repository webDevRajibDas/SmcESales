<?php
/**
 * AppVersionFixture
 *
 */
class AppVersionFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => '100'),
		'status' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => '1'),
		'start_date' => array('type' => 'date', 'null' => true, 'default' => null),
		'end_date' => array('type' => 'date', 'null' => true, 'default' => null),
		'outlet_delete_btn_hide_date' => array('type' => 'date', 'null' => true, 'default' => null),
		'created_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
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
			'id' => 1,
			'name' => 'Lorem ipsum dolor sit amet',
			'status' => 1,
			'start_date' => '2018-04-24',
			'end_date' => '2018-04-24',
			'outlet_delete_btn_hide_date' => '2018-04-24',
			'created_at' => '2018-04-24 19:04:00',
			'updated_at' => '2018-04-24 19:04:00'
		),
	);

}
