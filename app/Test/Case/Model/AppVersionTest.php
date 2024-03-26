<?php
App::uses('AppVersion', 'Model');

/**
 * AppVersion Test Case
 *
 */
class AppVersionTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.app_version'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->AppVersion = ClassRegistry::init('AppVersion');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AppVersion);

		parent::tearDown();
	}

}
