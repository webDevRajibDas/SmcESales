<?php
/**
 * BonusCardFixture
 *
 */
class BonusCardFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'fiscal_year_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'bonus_card_type_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 2, 'unsigned' => false),
		'product_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'min_qty_per_memo' => array('type' => 'decimal', 'null' => false, 'default' => null, 'length' => '13,2', 'unsigned' => false),
		'min_qty_per_year' => array('type' => 'decimal', 'null' => false, 'default' => null, 'length' => '13,2', 'unsigned' => false),
		'is_active' => array('type' => 'boolean', 'null' => false, 'default' => null),
		'created_at' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'created_by' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'updated_at' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'updated_by' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'name' => 'Lorem ipsum dolor sit amet',
			'fiscal_year_id' => 1,
			'bonus_card_type_id' => 1,
			'product_id' => 1,
			'min_qty_per_memo' => '',
			'min_qty_per_year' => '',
			'is_active' => 1,
			'created_at' => '2017-05-10 13:54:48',
			'created_by' => 1,
			'updated_at' => '2017-05-10 13:54:48',
			'updated_by' => 1
		),
	);

}
