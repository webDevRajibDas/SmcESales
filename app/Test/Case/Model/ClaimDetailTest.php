<?php
App::uses('ClaimDetail', 'Model');

/**
 * ClaimDetail Test Case
 *
 */
class ClaimDetailTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.claim_detail',
		'app.claim',
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
		'app.product',
		'app.product_category',
		'app.brand',
		'app.variant',
		'app.measurement_unit',
		'app.challan_detail',
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
		'app.sale_target',
		'app.fiscal_year',
		'app.month',
		'app.week',
		'app.project',
		'app.project_ngo_outlet',
		'app.product_combination',
		'app.combination',
		'app.outlet_ngo_price',
		'app.sale_target_month',
		'app.user',
		'app.user_group',
		'app.login_token',
		'app.store_type',
		'app.inventory_statuses',
		'app.requisition',
		'app.requisition_detail',
		'app.inventory_status'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ClaimDetail = ClassRegistry::init('ClaimDetail');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ClaimDetail);

		parent::tearDown();
	}

}
