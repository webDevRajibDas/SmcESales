<?php
/**
 * DiscountBonusPolicyOptionExclusionInclusionProductFixture
 *
 */
class DiscountBonusPolicyOptionExclusionInclusionProductFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'discount_bonus_policy_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
		'discount_bonus_policy_option_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
		'create_for' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => '4'),
		'product_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => '4'),
		'min_qty' => array('type' => 'decimal', 'null' => true, 'default' => null, 'length' => '9'),
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
			'discount_bonus_policy_id' => 1,
			'discount_bonus_policy_option_id' => 1,
			'create_for' => 1,
			'product_id' => 1,
			'min_qty' => ''
		),
	);

}
