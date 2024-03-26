<?php
/**
 * SoStockCheckDetailFixture
 *
 */
class SoStockCheckDetailFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'length' => '8', 'key' => 'primary'),
		'so_stock_check_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => '8'),
		'product_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => '8'),
		'web_stock' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '9'),
		'app_stock' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '9'),
		'physical_stock' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '9'),
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
			'so_stock_check_id' => '',
			'product_id' => '',
			'web_stock' => '',
			'app_stock' => '',
			'physical_stock' => ''
		),
	);

}
