<?php
/**
 * SoStockCheckFixture
 *
 */
class SoStockCheckFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'so_stock_check';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'length' => '8', 'key' => 'primary'),
		'so_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => '8'),
		'store_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => '8'),
		'reported_time' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => '8'),
		'created_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_by' => array('type' => 'datetime', 'null' => true, 'default' => null),
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
			'so_id' => '',
			'store_id' => '',
			'reported_time' => '',
			'created_at' => '2019-10-16 16:35:01',
			'created_by' => '2019-10-16 16:35:01'
		),
	);

}
