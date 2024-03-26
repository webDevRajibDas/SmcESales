<?php
/**
 * DivisionFixture
 *
 */
class DivisionFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'length' => '8', 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => '100'),
		'created_at' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'created_by' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'updated_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'updated_by' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
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
			'name' => 'Lorem ipsum dolor sit amet',
			'created_at' => '2017-04-09 13:01:07',
			'created_by' => 1,
			'updated_at' => '2017-04-09 13:01:07',
			'updated_by' => 1
		),
	);

}
