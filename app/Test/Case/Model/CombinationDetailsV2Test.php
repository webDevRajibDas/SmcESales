<?php
App::uses('CombinationDetailsV2', 'Model');

/**
 * CombinationDetailsV2 Test Case
 *
 */
class CombinationDetailsV2Test extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.combination_details_v2',
		'app.combination',
		'app.product_combination',
		'app.product',
		'app.product_category',
		'app.brand',
		'app.variant',
		'app.measurement_unit',
		'app.challan_detail',
		'app.challan',
		'app.transaction_type',
		'app.current_inventory',
		'app.store',
		'app.office',
		'app.office_type',
		'app.office_person',
		'app.sales_person',
		'app.designation',
		'app.territory',
		'app.market',
		'app.location_type',
		'app.thana',
		'app.district',
		'app.division',
		'app.market_person',
		'app.outlet',
		'app.outlet_category',
		'app.institute',
		'app.product_price',
		'app.project',
		'app.project_ngo_outlet',
		'app.outlet_ngo_price',
		'app.program',
		'app.program_type',
		'app.doctor',
		'app.doctor_qualification',
		'app.doctor_type',
		'app.sale_target',
		'app.fiscal_year',
		'app.month',
		'app.week',
		'app.sale_target_month',
		'app.user',
		'app.user_group',
		'app.login_token',
		'app.store_type',
		'app.inventory_statuses',
		'app.requisition',
		'app.requisition_detail',
		'app.product_measurement',
		'app.target_for_product_sale',
		'app.target',
		'app.office_sales_person',
		'app.target_type',
		'app.target_for_other',
		'app.period',
		'app.memo_detail',
		'app.memo',
		'app.product_type',
		'app.price_open_product',
		'app.product_fraction_slab'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->CombinationDetailsV2 = ClassRegistry::init('CombinationDetailsV2');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CombinationDetailsV2);

		parent::tearDown();
	}

}
