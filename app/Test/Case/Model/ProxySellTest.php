<?php
App::uses('ProxySell', 'Model');

/**
 * ProxySell Test Case
 *
 */
class ProxySellTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.proxy_sell',
		'app.proxy_for_so',
		'app.proxy_by_so',
		'app.proxy_for_territory',
		'app.proxy_by_territory'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ProxySell = ClassRegistry::init('ProxySell');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ProxySell);

		parent::tearDown();
	}

}
