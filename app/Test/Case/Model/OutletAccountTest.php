<?php
App::uses('OutletAccount', 'Model');

/**
 * OutletAccount Test Case
 *
 */
class OutletAccountTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.outlet_account',
		'app.outlet',
		'app.market',
		'app.location_type',
		'app.thana',
		'app.district',
		'app.division',
		'app.territory',
		'app.office',
		'app.office_type',
		'app.office_person',
		'app.sales_person',
		'app.designation',
		'app.user',
		'app.user_group',
		'app.login_token',
		'app.sale_target',
		'app.fiscal_year',
		'app.month',
		'app.week',
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
		'app.store_type',
		'app.inventory_statuses',
		'app.requisition',
		'app.requisition_detail',
		'app.product_measurement',
		'app.product_price',
		'app.institute',
		'app.project',
		'app.project_ngo_outlet',
		'app.product_combination',
		'app.combination',
		'app.outlet_ngo_price',
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
		'app.product_fraction_slab',
		'app.sale_target_month',
		'app.market_person',
		'app.outlet_category',
		'app.program',
		'app.program_type',
		'app.doctor',
		'app.doctor_qualification',
		'app.doctor_type'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->OutletAccount = ClassRegistry::init('OutletAccount');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->OutletAccount);

		parent::tearDown();
	}

}
