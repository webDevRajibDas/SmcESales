<?php
/**
 * OutletAccountFixture
 *
 */
class OutletAccountFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'phone_number' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
		'password' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
		'user_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => '250'),
		'outlet_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
		'is_active' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '1'),
		'status' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '1'),
		'created_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
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
			'phone_number' => 1,
			'password' => 1,
			'user_name' => 'Lorem ipsum dolor sit amet',
			'outlet_id' => 1,
			'is_active' => 1,
			'status' => 1,
			'created_at' => '2022-01-31 12:27:44',
			'updated_at' => '2022-01-31 12:27:44',
			'updated_by' => 1
		),
	);

}
