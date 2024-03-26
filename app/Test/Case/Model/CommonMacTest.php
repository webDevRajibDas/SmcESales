<?php
App::uses('CommonMac', 'Model');

/**
 * CommonMac Test Case
 *
 */
class CommonMacTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.common_mac'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->CommonMac = ClassRegistry::init('CommonMac');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CommonMac);

		parent::tearDown();
	}

}
