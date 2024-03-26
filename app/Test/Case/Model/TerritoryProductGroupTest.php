<?php
App::uses('TerritoryProductGroup', 'Model');

/**
 * TerritoryProductGroup Test Case
 *
 */
class TerritoryProductGroupTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.territory_product_group'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->TerritoryProductGroup = ClassRegistry::init('TerritoryProductGroup');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->TerritoryProductGroup);

		parent::tearDown();
	}

}
