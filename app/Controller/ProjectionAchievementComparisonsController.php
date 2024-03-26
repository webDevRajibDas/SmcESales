<?php
App::uses('AppController', 'Controller');
/**
 * ProjectionAchievementComparisons Controller
 */
class ProjectionAchievementComparisonsController extends AppController
{



	/**
	 * Components
	 *
	 * @var array
	 */

	public $uses = array('Memo', 'Office', 'Territory', 'OutletCategory', 'Outlet', 'Product', 'Brand', 'ProductCategory');
	public $components = array('Paginator', 'Session', 'Filter.Filter');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index($id = null)
	{
		$this->Session->delete('detail_results');
		$this->Session->delete('outlet_lists');

		ini_set('memory_limit', '1024M');
		ini_set('max_execution_time', 300); //300 seconds = 5 minutes

		$this->set('page_title', 'Projection and Achievement Comparison');

		$qumulatives  = array(
			'1' => 'Qumulative',
		);
		$this->set(compact('qumulatives'));


		$this->loadModel('FiscalYear');
		$fiscal_years_result = $this->FiscalYear->find(
			'all',
			array(
				/* 'conditions'=> array("FiscalYear.start_date <=" => date('Y-m-d'), "FiscalYear.end_date >=" => date('Y-m-d')), */
				'recursive' => -1
			)
		);
		$fiscal_years = array();
		$fiscal_years_info = array();
		foreach ($fiscal_years_result as $f_result) {

			$y_id = $f_result['FiscalYear']['id'];
			$fiscal_years[$f_result['FiscalYear']['id']] = $f_result['FiscalYear']['year_code'];

			$fiscal_years_info[$y_id]['code'] = $f_result['FiscalYear']['year_code'];
			$fiscal_years_info[$y_id]['start_date'] = $f_result['FiscalYear']['start_date'];
			$fiscal_years_info[$y_id]['end_date'] = $f_result['FiscalYear']['end_date'];
		}

		$this->set(compact('fiscal_years'));


		/* Comparison duration Type */

		$duration_types = array(
			'monthly' => 'Monthly',
			'quater' => 'Quater',
			'semi_annual' => 'Semi Annual',
			'Annual' => 'Annual'
		);
		$this->set(compact('duration_types'));


		/* Comparison Particular */

		$duration_particulars['monthly'] = array(
			'July' => 'July',
			'August' => 'August',
			'September' => 'September',
			'October' => 'October',
			'November' => 'November',
			'December' => 'December',
			'January' => 'January',
			'February' => 'February',
			'March' => 'March',
			'April' => 'April',
			'May' => 'May',
			'June' => 'June'
		);

		$duration_particulars['quater'] = array(
			'Q1' => 'Quater -1',
			'Q2' => 'Quater -2',
			'Q3' => 'Quater -3',
			'Q4' => 'Quater -4'
		);

		$duration_particulars['semi_annual'] = array(
			'semi_annual_1' => 'Semi Annual 1',
			'semi_annual_2' => 'Semi Annual 2'
		);

		$duration_particulars['Annual'] = array(
			'Annual' => 'Annual'
		);

		if ($this->request->is('post') || $this->request->is('put')) {
			$duration_type = $this->data['ProjectionAchievementComparisons']['duration_type'];
		} else {
			$duration_type = "monthly";
		}

		$selected_duration_particulars = $duration_particulars[$duration_type];
		$json_duration_particulars = json_encode($duration_particulars);

		$this->set(compact('selected_duration_particulars'));
		$this->set(compact('json_duration_particulars'));


		$past_years = array(
			'1' => 1,
			'2' => 2,
			'3' => 3,
			'4' => 4,
			'5' => 5,
			'6' => 6,
			'7' => 7,
			'8' => 8,
			'9' => 9,
			'10' => 10
		);
		$this->set(compact('past_years'));

		$columns = array(
			'product' => 'By Product',
			'brand' => 'By Brand',
			'category' => 'By Category'
		);
		$this->set(compact('columns'));


		$indicators = array(
			'1' => 'Target',
			'2' => 'Achievement',
			'3' => '% Achieved',
			'4' => '% Change (Achi.)',
		);
		$this->set(compact('indicators'));


		// For Indicator Unit 

		$indicator_units = array(
			'1' => 'Revenue',
			'2' => 'Quantity',
		);
		$this->set(compact('indicator_units'));


		// For SO Wise or Territory Wise

		$territoty_selection = array(
			'1' => 'Territory Wise',
			'2' => 'SO Wise',
		);
		$this->set(compact('territoty_selection'));


		//for region office list
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id' => 3),
			'order' => array('office_name' => 'asc')
		));

		//products
		$conditions = array(
			'NOT' => array('Product.product_category_id' => 32),
			'is_active' => 1
		);
		$conditions['is_virtual'] = 0;
		$product_list = $this->Product->find('list', array(
			'conditions' => $conditions,
			'order' =>  array('order' => 'asc')
		));
		$this->set(compact('product_list'));

		//for brands list
		$conditions = array(
			'NOT' => array('Brand.id' => 44)
		);
		$brands = $this->Brand->find('list', array(
			'conditions' => $conditions,
			'order' =>  array('name' => 'asc')
		));
		$this->set(compact('brands'));


		//for cateogry list
		$conditions = array(
			'NOT' => array('ProductCategory.id' => 32)
		);
		$categories = $this->ProductCategory->find('list', array(
			'conditions' => $conditions,
			'order' =>  array('name' => 'asc')
		));
		$this->set(compact('categories'));


		$office_parent_id = $this->UserAuth->getOfficeParentId();

		$this->set(compact('office_parent_id'));

		$office_conditions = array('Office.office_type_id' => 2);

		if ($office_parent_id == 0) {
			$office_id = 0;
		} elseif ($office_parent_id == 14) {
			$region_office_id = $this->UserAuth->getOfficeId();
			$region_offices = $this->Office->find('list', array(
				'conditions' => array('Office.office_type_id' => 3, 'Office.id' => $region_office_id),
				'order' => array('office_name' => 'asc')
			));

			$office_conditions = array('Office.parent_office_id' => $region_office_id);

			$office_id = 0;
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
			$office_id = $this->UserAuth->getOfficeId();
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			$office_conditions = array('Office.parent_office_id' => $this->data['ProjectionAchievementComparisons']['region_office_id']);
		}

		$this->set(compact('office_id'));


		// for office list
		$office_conditions['NOT'] = array("id" => array(30, 31, 37));
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));




		if ($office_id || $this->request->is('post') || $this->request->is('put')) {
			$office_id = isset($this->request->data['ProjectionAchievementComparisons']['office_id']) != '' ? $this->request->data['ProjectionAchievementComparisons']['office_id'] : $office_id;

			$territories = $this->Territory->find('all', array(
				'conditions' => array('Territory.office_id' => $office_id),
				'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
				'order' => array('Territory.name' => 'asc')
			));

			foreach ($territories as $value) {
				$territories[$value['Territory']['id']] =  $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')';
			}

			$this->set(compact('territories'));
		}



		$this->set(compact('offices', 'outlet_type', 'region_offices'));

		/* Generating Report Start */

		if ($this->request->is('post')) {
			if ($this->request->data['ProjectionAchievementComparisons']['indicators'] && $this->request->data['ProjectionAchievementComparisons']['indicator_unit']) {

				$request_data = $this->request->data;
				// pr($request_data);exit;
				$this->set(compact('request_data'));

				$region_office_ids = $request_data['ProjectionAchievementComparisons']['region_office_id'];
				$office_ids = $request_data['ProjectionAchievementComparisons']['office_id'];
				$territory_ids = $request_data['ProjectionAchievementComparisons']['territory_id'];
				$qumulative = $request_data['ProjectionAchievementComparisons']['qumulative'];
				$fiscal_year_id = $request_data['ProjectionAchievementComparisons']['fiscal_year_id'];

				$duration_type = $request_data['ProjectionAchievementComparisons']['duration_type'];
				$duration_particular = $request_data['ProjectionAchievementComparisons']['duration_particular'];
				$past_year = $request_data['ProjectionAchievementComparisons']['past_year'];
				$indicators_data = $request_data['ProjectionAchievementComparisons']['indicators'];
				$indicator_unit = $request_data['ProjectionAchievementComparisons']['indicator_unit'];

				/*check qumlative */
				$details = 0;
				$details_type = "";
				$find_territories = array();
				$find_offices = array();
				if (is_array($qumulative) && count($qumulative) > 0) {
					$summery_area_name = "";
					if ($territory_ids) {
						$find_territories[$territory_ids] = $territory_ids;
						$summery_area_name = "Territory : " . $this->get_name("Territory", $territory_ids);
						$find_offices = array();
					} else if ($office_id) {
						$find_territories = $this->get_territory_ids($office_id, 2);
						$find_offices[$office_id] = $office_id;
						$summery_area_name = "Area Office : " . $this->get_name("Office", $office_id);
					} else if ($region_office_ids) {
						$find_territories = $this->get_territory_ids($region_office_ids, 3);
						$find_offices = array();
						$summery_area_name = "Regional Office : " . $this->get_name("Office", $region_office_ids);
					}
					$summery_area_name = ($summery_area_name) ? $summery_area_name : "Head Office";
					$this->set(compact('summery_area_name'));
				} else {
					$details = 1;
					if (!$region_office_ids) {
						$details_type = "region_wise";
					} else if ($region_office_ids && !$office_id) {
						$details_type = "office_wise";
					} else if ($office_id) {
						$details_type = "territory_wise";
					}
				}

				$this->set(compact('details'));
				/* get month info */

				$month_info = $this->get_month_info($fiscal_year_id);
				$months_info = array();
				$month_name_id = array();

				$month_start_end_info = $this->get_month_start_end_info($fiscal_years_info[$fiscal_year_id]);


				foreach ($month_info as $mk => $mv) {
					$month = $mv['Month']['name'];
					$months_info[$month]['id'] = $mv['Month']['id'];
					$months_info[$month]['start_date'] = $month_start_end_info[$month]['start_date'];
					$months_info[$month]['end_date'] = $month_start_end_info[$month]['end_date'];
				}

				foreach ($month_info as $mk => $mv) {
					$month = $mv['Month']['name'];
					$months_info[$month]['id'] = $mv['Month']['id'];
					$months_info[$month]['start_date'] = $month_start_end_info[$month]['start_date'];
					$months_info[$month]['end_date'] = $month_start_end_info[$month]['end_date'];
				}


				//for columns
				$columns_list = array();

				if ($request_data['ProjectionAchievementComparisons']['columns'] == 'product') {
					//for products list
					$conditions = array('NOT' => array('Product.product_category_id' => 32), 'is_active' => 1);
					if ($request_data['ProjectionAchievementComparisons']['product_id']) $conditions['id'] = $request_data['ProjectionAchievementComparisons']['product_id'];
					$product_type = isset($this->request->data['ProjectionAchievementComparisons']['product_type']) != '' ? $this->request->data['ProjectionAchievementComparisons']['product_type'] : '';
					if ($product_type) $conditions['source'] = $product_type;
					$product_list = $this->Product->find('list', array(
						'conditions' => $conditions,
						'order' =>  array('order' => 'asc')
					));
					$columns_list = $product_list;
				} elseif ($request_data['ProjectionAchievementComparisons']['columns'] == 'brand') {
					//for products list
					$conditions = array('NOT' => array('Product.product_category_id' => 32), 'is_active' => 1);
					if ($request_data['ProjectionAchievementComparisons']['brand_id']) $conditions['brand_id'] = $request_data['ProjectionAchievementComparisons']['brand_id'];

					$product_type = isset($this->request->data['ProjectionAchievementComparisons']['product_type']) != '' ? $this->request->data['ProjectionAchievementComparisons']['product_type'] : '';
					if ($product_type) $conditions['source'] = $product_type;

					$product_list = $this->Product->find('list', array(
						'conditions' => $conditions,
						'order' =>  array('order' => 'asc')
					));

					$columns_list = $product_list;
				} elseif ($request_data['ProjectionAchievementComparisons']['columns'] == 'category') {
					//for products list
					$conditions = array('NOT' => array('Product.product_category_id' => 32), 'is_active' => 1);
					if ($request_data['ProjectionAchievementComparisons']['product_category_id']) $conditions['product_category_id'] = $request_data['ProjectionAchievementComparisons']['product_category_id'];
					$product_type = isset($this->request->data['ProjectionAchievementComparisons']['product_type']) != '' ? $this->request->data['ProjectionAchievementComparisons']['product_type'] : '';
					if ($product_type) $conditions['source'] = $product_type;
					$product_list = $this->Product->find('list', array(
						'conditions' => $conditions,
						'order' =>  array('order' => 'asc')
					));
					$columns_list = $product_list;
				}

				$this->set(compact('columns_list'));

				/* sub columns start */

				$sub_columns = array();
				$sub_columns_arr = array();



				if (is_array($indicators_data) && count($indicators_data) > 0) {

					foreach ($indicators_data as $key => $value) {
						$sub_columns[] = $indicators[$value];
						$sub_columns_arr[$value] = $indicators[$value];
					}
				}


				$this->set(compact('sub_columns'));


				/* sub columns end */

				//end for columns

				//for rows
				$rows_list = array();

				if ($fiscal_year_id) {
					$quater_months['Q1'] = array('July', 'August', 'September');
					$quater_months['Q2'] = array('October', 'November', 'December');
					$quater_months['Q3'] = array('January', 'February', 'March');
					$quater_months['Q4'] = array('April', 'May', 'June');

					$semi_annual_months['semi_annual_1'] = array('July', 'August', 'September', 'October', 'November', 'December');
					$semi_annual_months['semi_annual_2'] = array('January', 'February', 'March', 'April', 'May', 'June');

					$semi_annual_1 = array('Semi Annual 1');
					$semi_annual_2 = array('Semi Annual 2');
					$annual = array('Annual');


					$particular_name = $duration_particulars[$duration_type][$duration_particular];
					$is_annual = 0;
					if ($duration_type == "monthly") {
						$rows_list[0]['particular_name'] = $particular_name . " (" . $fiscal_years_info[$fiscal_year_id]['code'] . ")";
						$rows_list[0]['start_date'] = $months_info[$particular_name]['start_date'];
						$rows_list[0]['end_date'] = $months_info[$particular_name]['end_date'];
						$rows_list[0]['month_ids'] = array($months_info[$particular_name]['id']);
						$rows_list[0]['year_start_date'] = $fiscal_years_info[$fiscal_year_id]['start_date'];
						$rows_list[0]['year_end_date'] = $fiscal_years_info[$fiscal_year_id]['end_date'];
						$rows_list[0]['fiscal_year_id'] = $fiscal_year_id;


						for ($i = 1; $i <= $past_year; $i++) {

							$month_no = 12;
							$pre_year_info = $this->get_pre_year_start_end($rows_list[$i - 1]['year_start_date'], $month_no);
							$rows_list[$i]['particular_name'] = $particular_name . " (" . $pre_year_info['code'] . ")";
							$new_fiscal_year_id = $this->get_fiscal_year_id($fiscal_years_info, $pre_year_info['start_date']);
							if ($new_fiscal_year_id) {
								$month_start_end = $this->get_month_start_end_info($fiscal_years_info[$new_fiscal_year_id]);
								$rows_list[$i]['start_date'] = $month_start_end[$particular_name]['start_date'];
								$rows_list[$i]['end_date'] = $month_start_end[$particular_name]['end_date'];
								$rows_list[$i]['month_ids'] = array($months_info[$particular_name]['id']);
							} else {
								$month_start_end = $this->get_month_start_end_info($pre_year_info);
								$rows_list[$i]['start_date'] = $month_start_end[$particular_name]['start_date'];
								$rows_list[$i]['end_date'] = $month_start_end[$particular_name]['end_date'];
								$rows_list[$i]['month_ids'] = array();
							}

							$rows_list[$i]['year_start_date'] = $pre_year_info['start_date'];
							$rows_list[$i]['year_end_date'] = $pre_year_info['end_date'];
							$rows_list[$i]['fiscal_year_id'] = $new_fiscal_year_id;
						}
					} elseif ($duration_type == "quater") {
						// duration_particular

						$rows_list[0]['particular_name'] = $particular_name . " (" . $fiscal_years_info[$fiscal_year_id]['code'] . ")";
						$start_m = $quater_months[$duration_particular][0];
						$end_m = $quater_months[$duration_particular][2];
						$rows_list[0]['start_date'] = $months_info[$start_m]['start_date'];
						$rows_list[0]['end_date'] = $months_info[$end_m]['end_date'];
						$month_ids = $this->get_monthIdFromName($quater_months[$duration_particular], $months_info);
						$rows_list[0]['month_ids'] = $month_ids;
						$rows_list[0]['year_start_date'] = $fiscal_years_info[$fiscal_year_id]['start_date'];
						$rows_list[0]['year_end_date'] = $fiscal_years_info[$fiscal_year_id]['end_date'];
						$rows_list[0]['fiscal_year_id'] = $fiscal_year_id;

						for ($i = 1; $i <= $past_year; $i++) {
							$month_no = 12;
							$pre_year_info = $this->get_pre_year_start_end($rows_list[$i - 1]['year_start_date'], $month_no);
							$rows_list[$i]['particular_name'] = $particular_name . " (" . $pre_year_info['code'] . ")";
							$new_fiscal_year_id = $this->get_fiscal_year_id($fiscal_years_info, $pre_year_info['start_date']);
							if ($new_fiscal_year_id) {
								$month_start_end = $this->get_month_start_end_info($fiscal_years_info[$new_fiscal_year_id]);
								$rows_list[$i]['start_date'] = $month_start_end[$start_m]['start_date'];
								$rows_list[$i]['end_date'] = $month_start_end[$end_m]['end_date'];
								$rows_list[$i]['month_ids'] = $month_ids;
							} else {
								$month_start_end = $this->get_month_start_end_info($pre_year_info);
								$rows_list[$i]['start_date'] = $month_start_end[$start_m]['start_date'];
								$rows_list[$i]['end_date'] = $month_start_end[$end_m]['end_date'];
								$rows_list[$i]['month_ids'] = array();
							}

							$rows_list[$i]['year_start_date'] = $pre_year_info['start_date'];
							$rows_list[$i]['year_end_date'] = $pre_year_info['end_date'];
							$rows_list[$i]['fiscal_year_id'] = $new_fiscal_year_id;
						}
					} elseif ($duration_type == "semi_annual") {

						// duration_particular                                        
						$rows_list[0]['particular_name'] = $particular_name . " (" . $fiscal_years_info[$fiscal_year_id]['code'] . ")";
						$start_m = $semi_annual_months[$duration_particular][0];
						$end_m = $semi_annual_months[$duration_particular][5];
						$rows_list[0]['start_date'] = $months_info[$start_m]['start_date'];
						$rows_list[0]['end_date'] = $months_info[$end_m]['end_date'];
						$month_ids = $this->get_monthIdFromName($semi_annual_months[$duration_particular], $months_info);
						$rows_list[0]['month_ids'] = $month_ids;
						$rows_list[0]['year_start_date'] = $fiscal_years_info[$fiscal_year_id]['start_date'];
						$rows_list[0]['year_end_date'] = $fiscal_years_info[$fiscal_year_id]['end_date'];
						$rows_list[0]['fiscal_year_id'] = $fiscal_year_id;

						for ($i = 1; $i <= $past_year; $i++) {
							$month_no = 12;
							$pre_year_info = $this->get_pre_year_start_end($rows_list[$i - 1]['year_start_date'], $month_no);
							$rows_list[$i]['particular_name'] = $particular_name . " (" . $pre_year_info['code'] . ")";
							$new_fiscal_year_id = $this->get_fiscal_year_id($fiscal_years_info, $pre_year_info['start_date']);
							if ($new_fiscal_year_id) {
								$month_start_end = $this->get_month_start_end_info($fiscal_years_info[$new_fiscal_year_id]);
								$rows_list[$i]['start_date'] = $month_start_end[$start_m]['start_date'];
								$rows_list[$i]['end_date'] = $month_start_end[$end_m]['end_date'];
								$rows_list[$i]['month_ids'] = $month_ids;
							} else {
								$month_start_end = $this->get_month_start_end_info($pre_year_info);
								$rows_list[$i]['start_date'] = $month_start_end[$start_m]['start_date'];
								$rows_list[$i]['end_date'] = $month_start_end[$end_m]['end_date'];
								$rows_list[$i]['month_ids'] = array();
							}

							$rows_list[$i]['year_start_date'] = $pre_year_info['start_date'];
							$rows_list[$i]['year_end_date'] = $pre_year_info['end_date'];
							$rows_list[$i]['fiscal_year_id'] = $new_fiscal_year_id;
						}
					} elseif ($duration_type == "Annual") {
						$is_annual = 1;
						$rows_list[0]['particular_name'] = $particular_name . " (" . $fiscal_years_info[$fiscal_year_id]['code'] . ")";
						$rows_list[0]['start_date'] = $fiscal_years_info[$fiscal_year_id]['start_date'];
						$rows_list[0]['end_date'] = $fiscal_years_info[$fiscal_year_id]['end_date'];
						$rows_list[0]['fiscal_year_id'] = $fiscal_year_id;

						for ($i = 1; $i <= $past_year; $i++) {
							$month_no = 12;
							$pre_year_info = $this->get_pre_year_start_end($rows_list[$i - 1]['start_date'], $month_no);
							$rows_list[$i]['particular_name'] = $particular_name . " (" . $pre_year_info['code'] . ")";
							$rows_list[$i]['start_date'] = $pre_year_info['start_date'];
							$rows_list[$i]['end_date'] = $pre_year_info['end_date'];
							$rows_list[$i]['fiscal_year_id'] = $this->get_fiscal_year_id($fiscal_years_info, $pre_year_info['start_date']);
						}
					}

					$this->set(compact('rows_list'));

					/* generate report data */

					$rows_data = "";
					$row_count = 1;
					$details_rows = array();


					if ($details) {
						/* Details Report start */

						$tables_data = array();
						if ($details_type == "region_wise") {

							$all_regoin_ids = $this->get_all_office_ids($region_office_ids, 1);
							$details_rows = $all_regoin_ids;
							foreach ($all_regoin_ids as $ak => $av) {
								$rows_data = "";
								$target_data_arr = array();

								$find_territories = $this->get_territory_ids($ak, 3);
								$find_offices = $this->get_all_office_ids($ak, 3);
								$rows_data = $this->generate_summary_reports($indicator_unit, $rows_list, $columns_list, $sub_columns_arr, $find_offices, $find_territories, $fiscal_year_id, $is_annual);
								$tables_data[$ak]['data'] = $rows_data;
								$tables_data[$ak]['area_name'] = "Region Name:" . $av;
							}
						} else if ($details_type == "office_wise") {

							$all_regoin_ids = $this->get_all_office_ids($region_office_ids, 3);
							$details_rows = $all_regoin_ids;
							foreach ($all_regoin_ids as $ak => $av) {
								$rows_data = "";
								$target_data_arr = array();

								$find_territories = $this->get_territory_ids($ak, 2);
								$find_offices = $this->get_all_office_ids($ak, 2);
								$rows_data = $this->generate_summary_reports($indicator_unit, $rows_list, $columns_list, $sub_columns_arr, $find_offices, $find_territories, $fiscal_year_id, $is_annual);
								$tables_data[$ak]['data'] = $rows_data;
								$tables_data[$ak]['area_name'] = "Area Office Name:" . $av;
							}
						} else if ($details_type == "territory_wise") {
							$all_regoin_ids = $this->get_territory_ids($office_ids, 2);
							$details_rows = $all_regoin_ids;
							foreach ($all_regoin_ids as $ak => $av) {
								$rows_data = "";
								$target_data_arr = array();
								$find_territories = array($ak => $ak);
								$find_offices = array($office_ids => $office_ids);
								$rows_data = $this->generate_summary_reports($indicator_unit, $rows_list, $columns_list, $sub_columns_arr, $find_offices, $find_territories, $fiscal_year_id, $is_annual);
								$tables_data[$ak]['data'] = $rows_data;
								$tables_data[$ak]['area_name'] = "Territory Name:" . $av;
							}
						}

						$this->set(compact('tables_data'));

						/* Details Report End */
					} else {
						$rows_data = $this->generate_summary_reports($indicator_unit, $rows_list, $columns_list, $sub_columns_arr, $find_offices, $find_territories, $fiscal_year_id, $is_annual);
					}
					$this->set(compact('rows_data'));
				} else {
					$this->Session->setFlash(__('Please select an Fiscal Year!'), 'flash/error');
				}
			} else if (!$this->request->data['ProjectionAchievementComparisons']['indicators']) {
				$this->Session->setFlash(__('Please select an indicators!'), 'flash/error');
			} else {
				$this->Session->setFlash(__('Please select an indicator unit!'), 'flash/error');
			}
		}
		/* Generating Report End */
	}



	/**
	 * admin_view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_view($id = null)
	{
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add($id = null)
	{
	}

	/**
	 * admin_edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_edit($id = null)
	{
	}

	/**
	 * admin_delete method
	 *
	 * @throws NotFoundException
	 * @throws MethodNotAllowedException
	 * @param string $id
	 * @return void
	 */
	public function admin_delete($id = null)
	{
	}





	public function getInfo($territory_id = 0)
	{
		/*$territories = $this->Territory->find('first', array(
				'conditions' => array('Territory.id' => $territory_id),
				
				'joins' => array(
					array(
						'alias' => 'Office',
						'table' => 'offices',
						'type' => 'INNER',
						'conditions' => 'Territory.office_id = Office.id'
					),
					array(
						'alias' => 'ThanaTerritory',
						'table' => 'thana_territories',
						'type' => 'INNER',
						'conditions' => 'Territory.id = ThanaTerritory.territory_id'
					),
					array(
						'alias' => 'Thana',
						'table' => 'thanas',
						'type' => 'INNER',
						'conditions' => 'ThanaTerritory.thana_id = Thana.id'
					),
					array(
						'alias' => 'District',
						'table' => 'districts',
						'type' => 'INNER',
						'conditions' => 'Thana.district_id = District.id'
					),
					array(
						'alias' => 'Division',
						'table' => 'divisions',
						'type' => 'INNER',
						'conditions' => 'District.division_id = Division.id'
					)
				),
				'fields' => 'Office.office_name, Thana.name, District.name, Division.name',
				//'order' => array('Territory.name' => 'asc'),
				'recursive' => -1
				));*/


		//pr($territories);
		//exit;

		$sql = "SELECT TOP 1 [Territory].[id] AS [territory_id], [Territory].[name] AS [territory_name], [Office].[office_name] AS [office_name], [Thana].[name] AS [thana_name], [District].[name] AS [district_name], [Division].[name] AS [division_name] FROM [territories] AS [Territory] 
				INNER JOIN [offices] AS [Office] ON ([Territory].[office_id] = [Office].[id]) 
				INNER JOIN [thana_territories] AS [ThanaTerritory] ON ([Territory].[id] = [ThanaTerritory].[territory_id]) 
				INNER JOIN [thanas] AS [Thana] ON ([ThanaTerritory].[thana_id] = [Thana].[id]) 
				INNER JOIN [districts] AS [District] ON ([Thana].[district_id] = [District].[id]) 
				INNER JOIN [divisions] AS [Division] ON ([District].[division_id] = [Division].[id]) 
				WHERE [Territory].[id] = $territory_id";

		$territories = $this->Territory->query($sql);

		//pr($territories);
		//exit;		
		return $territories;
	}



	public function get_office_list_by_region($parent_office_id)
	{

		$office_conditions = array('Office.parent_office_id' => $parent_office_id, 'Office.office_type_id' => 2);

		/*$offices = $this->Office->find('all', array(
			'fields' => array('id', 'office_name'),
			'conditions' => $office_conditions, 
			'order' => array('office_name' => 'asc'),
			'recursive' => -1
			)
		);
		pr($offices);*/

		$sql = "SELECT [Office].[id] AS [id], [Office].[office_name] AS [office_name] FROM [offices] AS [Office] WHERE [Office].[parent_office_id] = " . $parent_office_id . " AND [Office].[office_type_id] = 2 ORDER BY [office_name] asc";
		$offices = $this->Office->query($sql);
		//pr($offices);
		//exit;
		return $offices;
	}


	public function get_office_list()
	{
		$rs = array(array('id' => '', 'name' => '---- All -----'));

		$parent_office_id = $this->request->data['region_office_id'];

		$office_conditions = array('NOT' => array("id" => array(30, 31, 37)), 'Office.office_type_id' => 2);
		//$office_conditions = array('Office.parent_office_id' => $parent_office_id, 'Office.office_type_id'=>2);

		if ($parent_office_id) $office_conditions['Office.parent_office_id'] = $parent_office_id;

		$offices = $this->Office->find(
			'all',
			array(
				'fields' => array('id', 'office_name'),
				'conditions' => $office_conditions,
				'order' => array('office_name' => 'asc'),
				'recursive' => -1
			)
		);


		$data_array = array();
		foreach ($offices as $office) {
			$data_array[] = array(
				'id' => $office['Office']['id'],
				'name' => $office['Office']['office_name'],
			);
		}

		//$data_array = Set::extract($offices, '{n}.Office');

		if (!empty($offices)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}

		$this->autoRender = false;
	}






	/**
	 *  get_target_data  method   
	 * parameters : 
	 * type=1 for National ,
	 * type=2 for Area ,
	 * type=3 for Territory ,
	 * type=4 for Reginal ,
	 * unit_type 1 for amount, 2 for quantity 
	 * @return array
	 */

	public function get_target_data($fiscal_year_id, $office_ids = array(), $territory_ids = array(), $is_annual, $row_info = array())
	{

		$this->loadModel('SaleTargetMonth');
		$total_target_result = array();
		$target_data = array();


		if (!$is_annual) {
			$conditions = array(
				'SaleTargetMonth.fiscal_year_id' => $fiscal_year_id,
				'SaleTargetMonth.month_id' => $row_info['month_ids']
			);
		} else {
			$conditions = array('SaleTargetMonth.fiscal_year_id' => $fiscal_year_id);
		}



		if (is_array($office_ids) && count($office_ids) > 0) {
			$office_ids = array_keys($office_ids);
			$conditions['SaleTargetMonth.aso_id'] = $office_ids;
		} else {
			$conditions['SaleTargetMonth.aso_id'] = 0;
			$conditions['SaleTargetMonth.territory_id'] = 0;
		}

		if (is_array($territory_ids) && count($territory_ids) > 0) {
			$territory_ids = array_keys($territory_ids);
			$conditions['SaleTargetMonth.territory_id'] = $territory_ids;
		}

		if ($fiscal_year_id) {
			$total_target_result = $this->SaleTargetMonth->find(
				'all',
				array(
					'conditions' => $conditions,

					'fields' => array('product_id', 'sum(target_amount) as total_amount', 'sum(target_quantity) as total_quantity'),
					'group' => array('SaleTargetMonth.product_id'),
					'recursive' => 0
				)
			);
		}

		foreach ($total_target_result as $tk => $tv) {
			$product_id = $tv['SaleTargetMonth']['product_id'];
			$target_data[$product_id]['total_amount'] = $tv[0]['total_amount'];
			$target_data[$product_id]['total_quantity'] = $tv[0]['total_quantity'];
		}

		return $target_data;
	}


	/**
	 *  get_month_info  method   
	 * parameters : 
	 * fiscal_year_id 
	 * @return array
	 */

	public function get_month_info($fiscal_year_id, $particular_name = "")
	{

		$this->loadModel('Month');

		if ($particular_name) {
			$month_result = $this->Month->find(
				'all',
				array(
					'fields' => array("Month.id", "Month.name", "Month.month"),
					'conditions' => array("Month.name" => $particular_name),
					'recursive' => -1
				)
			);
		} else {
			$month_result = $this->Month->find(
				'all',
				array(
					'fields' => array("Month.id", "Month.name", "Month.month"),
					'recursive' => -1
				)
			);
		}

		return $month_result;
	}


	public function get_month_start_end_info($years_info)
	{

		$start_date = $years_info['start_date'];
		$end_date = $years_info['end_date'];

		$month_info = array();
		$start    = (new DateTime($start_date))->modify('first day of this month');
		$end      = (new DateTime($end_date))->modify('first day of next month');
		$interval = DateInterval::createFromDateString('1 month');
		$period   = new DatePeriod($start, $interval, $end);

		foreach ($period as $dt) {
			$month_first_date = $dt->format("Y-m-01");
			$month_last_date = $dt->format("Y-m-t");
			$month_name = $dt->format("F");
			$month_info[$month_name] = array("start_date" => $month_first_date, "end_date" => $month_last_date);
		}

		return $month_info;
	}


	public function get_quater_start_end_info($years_info)
	{

		$start_date = $years_info['start_date'];
		$end_date = $years_info['end_date'];
		$quaters = array('q1', 'q2', 'q3', 'q4');


		$info = array();
		$start    = (new DateTime($start_date))->modify('first day of this month');
		$end      = (new DateTime($end_date))->modify('first day of next month');
		$interval = DateInterval::createFromDateString('3 month');
		$period   = new DatePeriod($start, $interval, $end);

		$q = 0;
		foreach ($period as $dt) {
			$quater_first_date = $dt->format("Y-m-01");
			$quater_last_date = $dt->format("Y-m-t");
			$q_name = $quaters[$q];
			$info[$q_name] = array("start_date" => $quater_first_date, "end_date" => $quater_last_date);
			$q++;
		}

		return $info;
	}


	public function get_sales_info($offices = array(), $territory_ids = array(), $is_annual, $row_info = array())
	{

		$total_sales_info = array();
		$where = "";

		$start_date = $row_info['start_date'];
		$end_date = $row_info['end_date'];

		if (is_array($territory_ids) && count($territory_ids) > 0) {
			$territory_ids = array_keys($territory_ids);
			$where = "m.territory_id in (" . implode(",", $territory_ids) . ")  and ";
		}

		$sql = "SELECT md.product_id,sum(md.sales_qty) as qty,sum(md.price*md.sales_qty) as p_amount
    	FROM  [memo_details] md
    	left join [memos] m
    	on m.id=md.memo_id
    	where $where m.memo_date between '" . $start_date . "' and '" . $end_date . "'
    	group by md.product_id";

		$total_sales = $this->Memo->query($sql);

		foreach ($total_sales as $tsk => $tsv) {
			$product_id = $tsv[0]['product_id'];
			$total_sales_info[$product_id]['sales_qty'] = $tsv[0]['qty'];
			$total_sales_info[$product_id]['sales_amount'] = $tsv[0]['p_amount'];
		}

		return $total_sales_info;
	}


	public function get_pre_month_start_end($start_date, $month_no)
	{
		$month_data = array();


		if ($month_no > 1) {
			$month_no_re = $month_no - 1;
			$start_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($start_date)) . "-$month_no_re month"));
		}
		$start    = (new DateTime($start_date))->modify('first day of previous month');
		$end      = (new DateTime($start_date))->modify('last day of previous month');

		$start = $start->format("Y-m-01");
		$end = $end->format("Y-m-t");


		if ($month_no > 1) {
			$end = date("Y-m-d", strtotime(date("Y-m-d", strtotime($end)) . "+$month_no_re month"));
		}

		$month_data = array("start_date" => $start, "end_date" => $end);
		return $month_data;
	}

	/*
        $from =3 for regional ids
        $from=2  for Area Office  
   */

	public function get_territory_ids($office_id, $from)
	{
		$conditions = array();
		if ($from == 2) {
			$conditions = array("Territory.office_id" => $office_id);
		} else if ($from == 3) {
			$conditions = array("Office.parent_office_id" => $office_id);
		}

		$territory_result = $this->Territory->find(
			'list',
			array(
				'conditions' => $conditions,
				'recursive' => 0
			)
		);
		return $territory_result;
	}

	/*
        $from =3 for regional ids
       $from =2 for area ids
        $from=1  for Head Office  
   */

	public function get_all_office_ids($office_id, $from)
	{
		$conditions = array();
		if (!$office_id && $from == 1) {
			$conditions = array("Office.office_type_id" => 3);
		} else if ($office_id && $from == 3) {
			$conditions = array("Office.parent_office_id" => $office_id);
		} else if ($office_id && $from == 2) {
			$conditions = array("Office.parent_office_id" => $office_id);
		}




		$office_result = $this->Office->find(
			'list',
			array(
				'conditions' => $conditions,
				'recursive' => -1
			)
		);


		return $office_result;
	}




	public function get_name($modal, $id)
	{

		$office_result = $this->$modal->find(
			'list',
			array(
				'conditions' => array('id' => $id),
				'recursive' => -1
			)
		);

		return $office_result[$id];
	}


	public function get_pre_year_start_end($start, $month_no)
	{
		$month_data = array();
		$start = date("Y-m-d", strtotime(date("Y-m-d", strtotime($start)) . "-$month_no month"));

		$end = date("Y-m-d", strtotime(date("Y-m-d", strtotime($start)) . "+$month_no month"));
		$end = date("Y-m-d", strtotime(date("Y-m-d", strtotime($end)) . "-1 day"));

		$code = date("Y", strtotime($start)) . "-" . date("Y", strtotime($end));

		$month_data = array("start_date" => $start, "end_date" => $end, "code" => $code);
		return $month_data;
	}

	/*
     * $fiscal_year_id,$find_offices,$find_territories
     * range type array value 1 for yearly, 2 for monthly
     * 
     */

	public function generate_summary_reports($indicator_unit, $rows_list, $columns_list, $sub_columns_arr, $find_offices, $find_territories, $fiscal_year_id, $is_annual)
	{

		$rows_data = "";
		$target_data_arr = array();
		$sale_data_arr = array();
		$count = 1;

		foreach ($rows_list as $rk => $rv) {
			$target_data = array();
			$sales_data = array();
			$fiscal_year_id = $rv['fiscal_year_id'];

			if ($fiscal_year_id) {
				$target_data = $this->get_target_data($fiscal_year_id, $find_offices, $find_territories, $is_annual, $rv);
				$sales_data = $this->get_sales_info($find_offices, $find_territories, $is_annual, $rv);
			}

			$particular_name_cur = $rv['particular_name'];
			$rows_data = $rows_data . "<tr>";
			$rows_data = $rows_data . "<td>$particular_name_cur</td>";

			foreach ($columns_list as $ck => $cv) {

				foreach ($sub_columns_arr as $sck => $scv) {

					$sales_amount = array();
					$target_amount = array();

					if (array_key_exists($ck, $target_data)) {
						/* 1 means amount/TK and 2 means quantity */
						$target_amount[1] = $target_data[$ck]['total_amount'];
						$target_amount[2] = $target_data[$ck]['total_quantity'];
					} else {
						$target_amount[1] = 0;
						$target_amount[2] = 0;
					}

					if (array_key_exists($ck, $sales_data)) {
						/* 1 means amount/TK and 2 means quantity */
						$sales_amount[1] = $sales_data[$ck]['sales_amount'];
						$sales_amount[2] = $sales_data[$ck]['sales_qty'];
					} else {
						$sales_amount[1] = 0;
						$sales_amount[2] = 0;
					}

					$target_data_arr[$ck][1][$count] = $target_amount[1];
					$target_data_arr[$ck][2][$count] = $target_amount[2];

					$sales_data_arr[$ck][1][$count] = $sales_amount[1];
					$sales_data_arr[$ck][2][$count] = $sales_amount[2];


					if ($sck == 1) {
						$rows_data = $rows_data . "<td>$target_amount[$indicator_unit]</td>";
					} else if ($sck == 2) {
						$rows_data = $rows_data . "<td>$sales_amount[$indicator_unit]</td>";
					} else if ($sck == 3) {
						$dd = number_format((float)@($target_amount[$indicator_unit] / $sales_amount[$indicator_unit]), 2, '.', '');
						$rows_data = $rows_data . "<td>$dd</td>";
					} else if ($sck == 4) {
						if ($count == 1) {
							$achi_chg = 0;
						} else {
							$p_sales_amount = $sales_data_arr[$ck][$indicator_unit][$count - 1];
							$sales_amount = $sales_amount[$indicator_unit];
							$achi_chg = number_format((float)@(($p_sales_amount - $sales_amount) / $sales_amount), 2, '.', '');
						}

						$rows_data = $rows_data . "<td>$achi_chg</td>";
					}
				}
			}

			$rows_data = $rows_data . "</tr>";
			$count++;
		}

		return $rows_data;
	}




	public function get_fiscal_year_id($fiscal_years_info, $start_date)
	{
		$fiscal_year_id = 0;
		foreach ($fiscal_years_info as $key => $value) {
			if ($value['start_date'] == $start_date) {
				$fiscal_year_id = $key;
			}
		}

		return $fiscal_year_id;
	}

	public function get_monthIdFromName($quater_months, $months_info)
	{
		$ids = array();
		foreach ($quater_months as $key => $value) {
			$ids[] = $months_info[$value]['id'];
		}
		return $ids;
	}
}
