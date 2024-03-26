<?php
/**
 * VisitedOutletFixture
 *
 */
class VisitedOutletFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'length' => '8', 'key' => 'primary'),
		'outlet_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => '8'),
		'so_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => '8'),
		'longitude' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => '50'),
		'latitude' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => '50'),
		'visited_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_by' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => '8'),
		'updated_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'updated_by' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => '8'),
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
			'outlet_id' => '',
			'so_id' => '',
			'longitude' => 'Lorem ipsum dolor sit amet',
			'latitude' => 'Lorem ipsum dolor sit amet',
			'visited_at' => '2018-05-01 12:10:33',
			'created_at' => '2018-05-01 12:10:33',
			'created_by' => '',
			'updated_at' => '2018-05-01 12:10:33',
			'updated_by' => ''
		),
	);

}
