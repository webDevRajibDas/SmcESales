<?php
/**
 * SpecialGroupOtherSettingFixture
 *
 */
class SpecialGroupOtherSettingFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'special_group_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
		'create_for' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '1'),
		'reffrence_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => '4'),
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
			'special_group_id' => 1,
			'create_for' => 1,
			'reffrence_id' => 1
		),
	);

}
