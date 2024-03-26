<?php
/**
 * InstallmentNoFixture
 *
 */
class InstallmentNoFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'installment_no';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'length' => '8', 'key' => 'primary'),
		'installment_no_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => '8'),
		'installment_no_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => '100'),
		'memo_no' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => '100'),
		'memo_value' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => '100'),
		'is_used' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '1'),
		'is_pushed' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '1'),
		'payment' => array('type' => 'float', 'null' => true, 'default' => null, 'length' => '8'),
		'payment_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => '8'),
		'so_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => '8'),
		'created_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_by' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => '50'),
		'updated_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'updated_by' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => '50'),
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
			'installment_no_id' => '',
			'installment_no_name' => 'Lorem ipsum dolor sit amet',
			'memo_no' => 'Lorem ipsum dolor sit amet',
			'memo_value' => 'Lorem ipsum dolor sit amet',
			'is_used' => 1,
			'is_pushed' => 1,
			'payment' => 1,
			'payment_id' => '',
			'so_id' => '',
			'created_at' => '2018-04-26 16:13:37',
			'created_by' => 'Lorem ipsum dolor sit amet',
			'updated_at' => '2018-04-26 16:13:37',
			'updated_by' => 'Lorem ipsum dolor sit amet'
		),
	);

}
