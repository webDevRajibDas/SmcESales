<?php
App::uses('CombinationsV2', 'Model');

/**
 * CombinationsV2 Test Case
 *
 */
class CombinationsV2Test extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.combinations_v2',
		'app.reffrence'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->CombinationsV2 = ClassRegistry::init('CombinationsV2');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CombinationsV2);

		parent::tearDown();
	}

}
