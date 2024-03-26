<?php
/**
 * CombinationsV2Fixture
 *
 */
class CombinationsV2Fixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'combinations_v2';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => '100'),
		'create_for' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'reffrence_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => '4'),
		'combined_qty' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '9'),
		'created_at' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'created_by' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
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
			'name' => 'Lorem ipsum dolor sit amet',
			'create_for' => 1,
			'reffrence_id' => 1,
			'combined_qty' => '',
			'created_at' => '2021-04-10 10:29:09',
			'created_by' => 1,
			'updated_at' => '2021-04-10 10:29:09',
			'updated_by' => 1
		),
	);

}
