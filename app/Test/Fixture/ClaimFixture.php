<?php
/**
 * ClaimFixture
 *
 */
class ClaimFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'claim_no' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => '250'),
		'claim_status' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'claim_type' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => '100'),
		'created_at' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'challan_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
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
			'claim_no' => 'Lorem ipsum dolor sit amet',
			'claim_status' => 1,
			'claim_type' => 'Lorem ipsum dolor sit amet',
			'created_at' => '2017-04-27 19:02:41',
			'challan_id' => 1
		),
	);

}
