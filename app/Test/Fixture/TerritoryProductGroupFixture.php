<?php
/**
 * TerritoryProductGroupFixture
 *
 */
class TerritoryProductGroupFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'territory_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
		'product_group_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
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
			'territory_id' => 1,
			'product_group_id' => 1
		),
	);

}
