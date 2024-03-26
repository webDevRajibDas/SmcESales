<?php
/**
 * ProductFractionSlabFixture
 *
 */
class ProductFractionSlabFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'product_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'sales_qty' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '9'),
		'base_qty' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '9'),
		'use_for_sales' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
		'use_for_bonus' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
		'created_at' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_by' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
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
			'product_id' => 1,
			'sales_qty' => '',
			'base_qty' => '',
			'use_for_sales' => 1,
			'use_for_bonus' => 1,
			'created_at' => '2020-01-28 19:45:03',
			'created_by' => 1,
			'updated_at' => '2020-01-28 19:45:03',
			'updated_by' => 1
		),
	);

}
