<?php
App::uses('ProductConvertHistory', 'Model');

/**
 * ProductConvertHistory Test Case
 *
 */
class ProductConvertHistoryTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.product_convert_history',
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
		'app.challan',
		'app.transaction_type',
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
		'app.current_inventory',
		'app.inventory_statuses',
		'app.from_product',
		'app.to_product',
		'app.from_status',
		'app.to_status'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ProductConvertHistory = ClassRegistry::init('ProductConvertHistory');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ProductConvertHistory);

		parent::tearDown();
	}

}
