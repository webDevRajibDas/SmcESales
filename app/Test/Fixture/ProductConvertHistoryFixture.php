<?php
/**
 * ProductConvertHistoryFixture
 *
 */
class ProductConvertHistoryFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'product_convert_history';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'store_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'from_product_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'to_product_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'from_status_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'to_status_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'quantity' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '2'),
		'created_at' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'created_by' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
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
			'store_id' => 1,
			'from_product_id' => 1,
			'to_product_id' => 1,
			'from_status_id' => 1,
			'to_status_id' => 1,
			'quantity' => 1,
			'type' => 1,
			'created_at' => '2017-04-02 17:18:36',
			'created_by' => 1
		),
	);

}
