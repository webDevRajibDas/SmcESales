<?php
/**
 * CombinationDetailsV2Fixture
 *
 */
class CombinationDetailsV2Fixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'combination_details_v2';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4', 'key' => 'primary'),
		'combination_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
		'product_combination_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
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
			'combination_id' => 1,
			'product_combination_id' => 1
		),
	);

}
