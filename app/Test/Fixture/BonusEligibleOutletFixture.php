<?php
/**
 * BonusEligibleOutletFixture
 *
 */
class BonusEligibleOutletFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'length' => '8', 'key' => 'primary'),
		'bonus_card_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => '8'),
		'bonus_type_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => '8'),
		'fiscal_year_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => '8'),
		'outlet_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 10),
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
			'bonus_card_id' => '',
			'bonus_type_id' => '',
			'fiscal_year_id' => '',
			'outlet_id' => 'Lorem ip'
		),
	);

}
