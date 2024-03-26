<?php
App::uses('InstallmentNo', 'Model');

/**
 * InstallmentNo Test Case
 *
 */
class InstallmentNoTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.installment_no',
		'app.payment',
		'app.so'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->InstallmentNo = ClassRegistry::init('InstallmentNo');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->InstallmentNo);

		parent::tearDown();
	}

}
