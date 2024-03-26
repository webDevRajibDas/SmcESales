<?php
App::uses('SessionType', 'Model');

/**
 * SessionType Test Case
 *
 */
class SessionTypeTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.session_type',
		'app.session'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->SessionType = ClassRegistry::init('SessionType');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->SessionType);

		parent::tearDown();
	}

}
