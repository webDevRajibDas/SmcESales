<?php
App::uses('SpecialGroupOtherSetting', 'Model');

/**
 * SpecialGroupOtherSetting Test Case
 *
 */
class SpecialGroupOtherSettingTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.special_group_other_setting',
		'app.special_group'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->SpecialGroupOtherSetting = ClassRegistry::init('SpecialGroupOtherSetting');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->SpecialGroupOtherSetting);

		parent::tearDown();
	}

}
