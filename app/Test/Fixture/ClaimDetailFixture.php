<?php
/**
 * ClaimDetailFixture
 *
 */
class ClaimDetailFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'length' => '8', 'key' => 'primary'),
		'claim_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'product_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'measurement_unit_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'challan_qty' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'claim_qty' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'batch_no' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => '50'),
		'expire_date' => array('type' => 'date', 'null' => false, 'default' => null),
		'inventory_status_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'remarks' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => '100'),
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
			'claim_id' => 1,
			'product_id' => 1,
			'measurement_unit_id' => 1,
			'challan_qty' => 1,
			'claim_qty' => 1,
			'batch_no' => 'Lorem ipsum dolor sit amet',
			'expire_date' => '2017-04-27',
			'inventory_status_id' => 1,
			'remarks' => 'Lorem ipsum dolor sit amet'
		),
	);

}
