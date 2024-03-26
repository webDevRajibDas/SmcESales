<?php
App::uses('SpecialGroup', 'Model');

/**
 * SpecialGroup Test Case
 *
 */
class SpecialGroupTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.special_group'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->SpecialGroup = ClassRegistry::init('SpecialGroup');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->SpecialGroup);

		parent::tearDown();
	}

}
