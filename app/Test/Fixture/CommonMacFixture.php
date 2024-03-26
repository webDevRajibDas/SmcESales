<?php
/**
 * CommonMacFixture
 *
 */
class CommonMacFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'common_mac';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'length' => '8', 'key' => 'primary'),
		'mac_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => '250'),
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
			'mac_id' => 'Lorem ipsum dolor sit amet'
		),
	);

}
