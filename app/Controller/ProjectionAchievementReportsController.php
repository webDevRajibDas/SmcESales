<?php
App::uses('AppController', 'Controller');
/**
 * ProjectionAchievementSettings Controller
 *
 * @property ProjectionAchievementReport $ProjectionAchievementReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ProjectionAchievementReportsController extends AppController
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


		$this->set('page_title', 'Projection and Achievement Analysis Report');



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

		$q_lists = array(
			'q1' => 'Q1',
			'q2' => 'Q2',
			'q3' => 'Q3',
			'q4' => 'Q4',
			'semi_annual_1' => 'Semi Annual 1',
			'semi_annual_2' => 'Semi Annual 2'
		);
		$this->set(compact('q_lists'));

		$columns = array(
			'product' => 'By Product',
			'brand' => 'By Brand',
			'category' => 'By Category'
		);
		$this->set(compact('columns'));


		$indicators = array(
			'1' => 'Projection',
			'2' => 'Achievement',
			'3' => '% Achieved',
			'4' => '% Change (Achi.)',
		);
		$this->set(compact('indicators'));


		// For Indicator Unit 

		$indicator_unit = array(
			'1' => 'Revenue',
			'2' => 'Quantity',
		);
		$this->set(compact('indicator_unit'));


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
			$office_conditions = array('Office.parent_office_id' => $this->data['ProjectionAchievementReports']['region_office_id']);
		}

		$this->set(compact('office_id'));


		// for office list
		$office_conditions['NOT'] = array("id" => array(30, 31, 37));
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));




		if ($office_id || $this->request->is('post') || $this->request->is('put')) {
			$office_id = isset($this->request->data['ProjectionAchievementReports']['office_id']) != '' ? $this->request->data['ProjectionAchievementReports']['office_id'] : $office_id;

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
			if ($this->request->data['ProjectionAchievementReports']['indicators'] && $this->request->data['ProjectionAchievementReports']['indicator_unit']) {

				$request_data = $this->request->data;
				// pr($request_data);exit;
				$this->set(compact('request_data'));

				$region_office_ids = $request_data['ProjectionAchievementReports']['region_office_id'];
				$office_ids = $request_data['ProjectionAchievementReports']['office_id'];
				$territory_ids = $request_data['ProjectionAchievementReports']['territory_id'];
				$qumulative = $request_data['ProjectionAchievementReports']['qumulative'];
				$fiscal_year_id = $request_data['ProjectionAchievementReports']['fiscal_year_id'];


				$q_list = $request_data['ProjectionAchievementReports']['q_list'];
				$indicators_data = $request_data['ProjectionAchievementReports']['indicators'];
				$indicator_unit = $request_data['ProjectionAchievementReports']['indicator_unit'];

				/*check qumlative */
				$details = 0;
				$details_type = "";
				$find_territories = array();
				$find_offices = array();
				if (is_array($qumulative) && count($qumulative) > 0) {
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

				$month_start_end_info = $this->get_month_start_end_info($fiscal_year_id, $fiscal_years_info);


				foreach ($month_info as $mk => $mv) {
					$month = $mv['Month']['name'];
					$months_info[$month]['id'] = $mv['Month']['id'];
					$months_info[$month]['start_date'] = $month_start_end_info[$month]['start_date'];
					$months_info[$month]['end_date'] = $month_start_end_info[$month]['end_date'];
				}



				//for columns
				$columns_list = array();

				if ($request_data['ProjectionAchievementReports']['columns'] == 'product') {
					//for products list
					$conditions = array('NOT' => array('Product.product_category_id' => 32), 'is_active' => 1);
					if ($request_data['ProjectionAchievementReports']['product_id']) $conditions['id'] = $request_data['ProjectionAchievementReports']['product_id'];
					$product_type = isset($this->request->data['ProjectionAchievementReports']['product_type']) != '' ? $this->request->data['ProjectionAchievementReports']['product_type'] : '';
					if ($product_type) $conditions['source'] = $product_type;
					$product_list = $this->Product->find('list', array(
						'conditions' => $conditions,
						'order' =>  array('order' => 'asc')
					));
					$columns_list = $product_list;
				} elseif ($request_data['ProjectionAchievementReports']['columns'] == 'brand') {
					//for products list
					$conditions = array('NOT' => array('Product.product_category_id' => 32), 'is_active' => 1);
					if ($request_data['ProjectionAchievementReports']['brand_id']) $conditions['brand_id'] = $request_data['ProjectionAchievementReports']['brand_id'];

					$product_type = isset($this->request->data['ProjectionAchievementReports']['product_type']) != '' ? $this->request->data['ProjectionAchievementReports']['product_type'] : '';
					if ($product_type) $conditions['source'] = $product_type;

					$product_list = $this->Product->find('list', array(
						'conditions' => $conditions,
						'order' =>  array('order' => 'asc')
					));

					$columns_list = $product_list;
				} elseif ($request_data['ProjectionAchievementReports']['columns'] == 'category') {
					//for products list
					$conditions = array('NOT' => array('Product.product_category_id' => 32), 'is_active' => 1);
					if ($request_data['ProjectionAchievementReports']['product_category_id']) $conditions['product_category_id'] = $request_data['ProjectionAchievementReports']['product_category_id'];
					$product_type = isset($this->request->data['ProjectionAchievementReports']['product_type']) != '' ? $this->request->data['ProjectionAchievementReports']['product_type'] : '';
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
					$q1 = array('July', 'August', 'September');
					$q1_final = array('Quarter-1');
					$q2 = array('October', 'November', 'December');
					$q2_final = array('Quarter-2');
					$semi_annual_1 = array('Semi Annual 1');
					$q3 = array('January', 'February', 'March');
					$q3_final = array('Quarter-3');
					$q4 = array('April', 'May', 'June');
					$q4_final = array('Quarter-4');
					$semi_annual_2 = array('Semi Annual 2');
					$annual = array('Annual');

					if (is_array($q_list) && count($q_list) > 0) {

						if (in_array('semi_annual_1', $q_list) || (in_array('q1', $q_list) && in_array('q2', $q_list))) {
							$full_semi_annual_1 = array_merge($q1, $q1_final, $q2, $q2_final, $semi_annual_1);
							$rows_list = $full_semi_annual_1;
						} else if (in_array('q1', $q_list)) {
							$rows_list = array_merge($q1, $q1_final);
							//$rows_list=$q1;                           		

						} else if (in_array('q2', $q_list)) {
							$rows_list = array_merge($q2, $q2_final);
						}


						if (in_array('semi_annual_2', $q_list) || (in_array('q3', $q_list) && in_array('q4', $q_list))) {

							$full_semi_annual_2 = array_merge($q3, $q3_final, $q4, $q4_final, $semi_annual_2);
							$rows_list = array_merge($rows_list, $full_semi_annual_2);
						} else if (in_array('q3', $q_list)) {
							$q3_full = array_merge($q3, $q3_final);
							$rows_list = array_merge($rows_list, $q3_full);
						} else if (in_array('q4', $q_list)) {
							$q4_full = array_merge($q4, $q4_final);
							$rows_list = array_merge($rows_list, $q4_full);
						}


						if (in_array('semi_annual_1', $q_list) && in_array('semi_annual_2', $q_list)) {
							$rows_list = array_merge($rows_list, $annual);
						}
					} else {
						$rows_list = array_merge($q1, $q1_final, $q2, $q2_final, $semi_annual_1, $q3, $q3_final, $q4, $q4_final, $semi_annual_2, $annual);
					}

					$this->set(compact('rows_list'));


					/* generate report data */

					$rows_data = "";
					$row_count = 1;
					$details_rows = array();

					if ($details) {
						$tables_data = array();
						if ($details_type == "region_wise") {

							$all_regoin_ids = $this->get_all_office_ids($region_office_ids, 1);
							$details_rows = $all_regoin_ids;
							foreach ($all_regoin_ids as $ak => $av) {
								$rows_data = "";
								$target_data_arr = array();

								$find_territories = $this->get_territory_ids($ak, 3);
								$find_offices = $this->get_all_office_ids($ak, 3);


								foreach ($rows_list as $rk => $rv) {
									$has_month = 0;
									if (array_key_exists($rv, $months_info)) {
										$month_id = $months_info[$rv]['id'];
										$target_data = $this->get_target_data($month_id, $fiscal_year_id, 1, $find_offices, $find_territories);
										$sales_data = $this->get_sales_info($months_info[$rv]['start_date'], $months_info[$rv]['end_date'], $find_offices, $find_territories);

										/* Calculating Pre-month sales data */

										if (array_key_exists(4, $sub_columns_arr)) {
											$month_no = 1;
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$rv]['start_date'], $month_no);

											$pmonth_sales_data = $this->get_sales_info($pmonth_start_end['start_date'], $pmonth_start_end['end_date']);
										}


										$has_month = 1;
									} else {

										if ($rv == "Quarter-1") {
											$m = $q1[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 3);
										} elseif ($rv == "Quarter-2") {
											$m = $q2[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 3);
										} elseif ($rv == "Quarter-3") {
											$m = $q3[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 3);
										} elseif ($rv == "Quarter-4") {
											$m = $q4[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 3);
										} elseif ($rv == "Semi Annual 1") {
											$m = $q1[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 6);
										} elseif ($rv == "Semi Annual 2") {
											$m = $q3[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 6);
										} elseif ($rv == "Annual") {
											$m = $q1[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 12);
										}

										if (is_array($pmonth_start_end)) {
											$pre_sales_data = $this->get_sales_info($pmonth_start_end['start_date'], $pmonth_start_end['end_date']);
										}
									}







									$rows_data = $rows_data . "<tr>";
									$rows_data = $rows_data . "<td>$rv</td>";

									foreach ($columns_list as $ck => $cv) {

										foreach ($sub_columns_arr as $sck => $scv) {


											if ($has_month) {

												if (array_key_exists($ck, $target_data)) {
													$amount = $target_data[$ck]['total_amount'];
													$quantity = $target_data[$ck]['total_quantity'];
												} else {
													$amount = 0;
													$quantity = 0;
												}

												if (array_key_exists($ck, $sales_data)) {
													$sales_qty = $sales_data[$ck]['sales_qty'];
													$sales_amount = $sales_data[$ck]['sales_amount'];
												} else {
													$sales_qty = 0;
													$sales_amount = 0;
												}


												$target_data_arr[$ck]['ta'][$row_count] = $amount;
												$target_data_arr[$ck]['tq'][$row_count] = $quantity;

												$sales_data_arr[$ck]['sa'][$row_count] = $sales_amount;
												$sales_data_arr[$ck]['sq'][$row_count] = $sales_qty;

												if ($sck == 1) {
													if ($indicator_unit == 1) {
														$rows_data = $rows_data . "<th>$amount</th>";
													} else if ($indicator_unit == 2) {
														$rows_data = $rows_data . "<th>$quantity</th>";
													}
												} elseif ($sck == 2) {

													if ($indicator_unit == 1) {
														$rows_data = $rows_data . "<th>$sales_amount</th>";
													} else if ($indicator_unit == 2) {
														$rows_data = $rows_data . "<th>$sales_qty</th>";
													}
												} elseif ($sck == 3) {


													if ($indicator_unit == 1) {
														$dd = number_format((float)@($amount / $sales_amount), 2, '.', '');
														$rows_data = $rows_data . "<th>$dd</th>";
													} else if ($indicator_unit == 2) {
														$dd = number_format((float)@($quantity / $sales_qty), 2, '.', '');
														$rows_data = $rows_data . "<th>$dd</th>";
													}
												} elseif ($sck == 4) {

													if (array_key_exists($ck, $pmonth_sales_data)) {
														$p_sales_qty = $pmonth_sales_data[$ck]['sales_qty'];
														$p_sales_amount = $pmonth_sales_data[$ck]['sales_amount'];
													} else {
														$p_sales_qty = 0;
														$p_sales_amount = 0;
													}





													if ($indicator_unit == 1) {
														$achi_chg = number_format((float)@(($p_sales_amount - $sales_amount) / $sales_amount), 2, '.', '');
														$rows_data = $rows_data . "<th>$achi_chg</th>";
													} else if ($indicator_unit == 2) {
														$achi_chg = number_format((float)@(($p_sales_qty - $sales_qty) / $sales_qty), 2, '.', '');
														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												}
											} else {
												if ($rv == "Quarter-1") {

													$q1_amount = $target_data_arr[$ck]['ta'][$row_count - 1] + $target_data_arr[$ck]['ta'][$row_count - 2] + $target_data_arr[$ck]['ta'][$row_count - 3];

													$q1_amount_qty = $target_data_arr[$ck]['tq'][$row_count - 1] + $target_data_arr[$ck]['tq'][$row_count - 2] + $target_data_arr[$ck]['tq'][$row_count - 3];

													$q1_sales_amount = $sales_data_arr[$ck]['sa'][$row_count - 1] + $sales_data_arr[$ck]['sa'][$row_count - 2] + $sales_data_arr[$ck]['sa'][$row_count - 3];

													$q1_sales_qty = $sales_data_arr[$ck]['sq'][$row_count - 1] + $sales_data_arr[$ck]['sq'][$row_count - 2] + $sales_data_arr[$ck]['sq'][$row_count - 3];



													$target_data_arr[$ck]['ta']['q1'] = $q1_amount;

													$target_data_arr[$ck]['tq']['q1'] = $q1_amount_qty;

													$sales_data_arr[$ck]['sa']['q1'] = $q1_sales_amount;

													$sales_data_arr[$ck]['sq']['q1'] = $q1_sales_qty;





													if ($sck == 1) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q1_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q1_amount_qty</th>";
														}
													} elseif ($sck == 2) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q1_sales_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q1_sales_qty</th>";
														}
													} elseif ($sck == 3) {


														if ($indicator_unit == 1) {
															$dd = number_format((float)@($q1_amount / $q1_sales_amount), 2, '.', '');
															$rows_data = $rows_data . "<th>$dd</th>";
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($q1_amount_qty / $q1_sales_qty), 2, '.', '');
															$rows_data = $rows_data . "<th>$dd</th>";
														}
													} elseif ($sck == 4) {

														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}


														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $q1_amount) / $q1_amount), 2, '.', '');

															$rows_data = $rows_data . "<th>$achi_chg</th>";
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_qty - $q1_amount_qty) / $q1_amount_qty), 2, '.', '');

															$rows_data = $rows_data . "<th>$achi_chg</th>";
														}
													}
												} else if ($rv == "Quarter-2") {
													$q2_amount = $target_data_arr[$ck]['ta'][$row_count - 1] + $target_data_arr[$ck]['ta'][$row_count - 2] + $target_data_arr[$ck]['ta'][$row_count - 3];

													$q2_amount_qty = $target_data_arr[$ck]['tq'][$row_count - 1] + $target_data_arr[$ck]['tq'][$row_count - 2] + $target_data_arr[$ck]['tq'][$row_count - 3];

													$q2_sales_amount = $sales_data_arr[$ck]['sa'][$row_count - 1] + $sales_data_arr[$ck]['sa'][$row_count - 2] + $sales_data_arr[$ck]['sa'][$row_count - 3];

													$q2_sales_qty = $sales_data_arr[$ck]['sq'][$row_count - 1] + $sales_data_arr[$ck]['sq'][$row_count - 2] + $sales_data_arr[$ck]['sq'][$row_count - 3];


													$target_data_arr[$ck]['ta']['q2'] = $q2_amount;

													$target_data_arr[$ck]['tq']['q2'] = $q2_amount_qty;

													$sales_data_arr[$ck]['sa']['q2'] = $q2_sales_amount;
													$sales_data_arr[$ck]['sq']['q2'] = $q2_sales_qty;




													if ($sck == 1) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q2_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q2_amount_qty</th>";
														}
													} elseif ($sck == 2) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q2_sales_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q2_sales_qty</th>";
														}
													} elseif ($sck == 3) {

														if ($indicator_unit == 1) {
															$dd = number_format((float)@($q2_amount / $q2_sales_amount), 2, '.', '');
															$rows_data = $rows_data . "<th>$dd</th>";
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($q2_amount_qty / $q2_sales_qty), 2, '.', '');
															$rows_data = $rows_data . "<th>$dd</th>";
														}
													} elseif ($sck == 4) {

														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}

														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $q2_amount) / $q2_amount), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_qty - $q2_amount_qty) / $q2_amount_qty), 2, '.', '');
														}








														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												} else if ($rv == "Quarter-3") {
													$q3_amount = $target_data_arr[$ck]['ta'][$row_count - 1] + $target_data_arr[$ck]['ta'][$row_count - 2] + $target_data_arr[$ck]['ta'][$row_count - 3];

													$q3_amount_qty = $target_data_arr[$ck]['tq'][$row_count - 1] + $target_data_arr[$ck]['tq'][$row_count - 2] + $target_data_arr[$ck]['tq'][$row_count - 3];

													$q3_sales_amount = $sales_data_arr[$ck]['sa'][$row_count - 1] + $sales_data_arr[$ck]['sa'][$row_count - 2] + $sales_data_arr[$ck]['sa'][$row_count - 3];

													$q3_sales_amount_qty = $sales_data_arr[$ck]['sq'][$row_count - 1] + $sales_data_arr[$ck]['sq'][$row_count - 2] + $sales_data_arr[$ck]['sq'][$row_count - 3];


													$target_data_arr[$ck]['ta']['q3'] = $q3_amount;

													$target_data_arr[$ck]['tq']['q3'] = $q3_amount_qty;

													$sales_data_arr[$ck]['sa']['q3'] = $q3_sales_amount;

													$sales_data_arr[$ck]['sq']['q3'] = $q3_sales_amount_qty;


													if ($sck == 1) {
														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q3_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q3_amount_qty</th>";
														}
													} elseif ($sck == 2) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q3_sales_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q3_sales_amount_qty</th>";
														}
													} elseif ($sck == 3) {
														if ($indicator_unit == 1) {
															$dd = number_format((float)@($q3_amount / $q3_sales_amount), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($q3_amount_qty / $q3_sales_amount_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$dd</th>";
													} elseif ($sck == 4) {


														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}





														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $q3_amount) / $q3_amount), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_amount_qty - $q3_amount_qty) / $q3_amount_qty), 2, '.', '');
														}




														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												} else if ($rv == "Quarter-4") {
													$q4_amount = $target_data_arr[$ck]['ta'][$row_count - 1] + $target_data_arr[$ck]['ta'][$row_count - 2] + $target_data_arr[$ck]['ta'][$row_count - 3];

													$q4_amount_qty = $target_data_arr[$ck]['tq'][$row_count - 1] + $target_data_arr[$ck]['tq'][$row_count - 2] + $target_data_arr[$ck]['tq'][$row_count - 3];

													$q4_sales_amount = $sales_data_arr[$ck]['sa'][$row_count - 1] + $sales_data_arr[$ck]['sa'][$row_count - 2] + $sales_data_arr[$ck]['sa'][$row_count - 3];

													$q4_sales_amount_qty = $sales_data_arr[$ck]['sq'][$row_count - 1] + $sales_data_arr[$ck]['sq'][$row_count - 2] + $sales_data_arr[$ck]['sq'][$row_count - 3];


													$target_data_arr[$ck]['ta']['q4'] = $q4_amount;
													$target_data_arr[$ck]['tq']['q4'] = $q4_amount_qty;

													$sales_data_arr[$ck]['sa']['q4'] = $q4_sales_amount;
													$sales_data_arr[$ck]['sq']['q4'] = $q4_sales_amount_qty;




													if ($sck == 1) {
														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q4_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q4_amount_qty</th>";
														}
													} elseif ($sck == 2) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q4_sales_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q4_sales_amount_qty</th>";
														}
													} elseif ($sck == 3) {

														if ($indicator_unit == 1) {
															$dd = number_format((float)@($q4_amount / $q4_sales_amount), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($q4_amount_qty / $q4_sales_amount_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$dd</th>";
													} elseif ($sck == 4) {


														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}





														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $q4_amount) / $q4_amount), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_amount_qty - $q4_amount_qty) / $q4_amount_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												} else if ($rv == "Semi Annual 1") {

													$semi_annual_am1 = $target_data_arr[$ck]['ta']['q1'] + $target_data_arr[$ck]['ta']['q2'];

													$semi_annual_am1_qty = $target_data_arr[$ck]['tq']['q1'] + $target_data_arr[$ck]['tq']['q2'];

													$semi_annual_am1_sales = $sales_data_arr[$ck]['sa']['q1'] + $sales_data_arr[$ck]['sa']['q2'];

													$semi_annual_am1_sales_qty = $sales_data_arr[$ck]['sq']['q1'] + $sales_data_arr[$ck]['sq']['q2'];



													if ($sck == 1) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$semi_annual_am1</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$semi_annual_am1_qty</th>";
														}
													} elseif ($sck == 2) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$semi_annual_am1_sales</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$semi_annual_am1_sales_qty</th>";
														}
													} elseif ($sck == 3) {

														if ($indicator_unit == 1) {
															$dd = number_format((float)@($semi_annual_am1 / $semi_annual_am1_sales), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($semi_annual_am1_qty / $semi_annual_am1_sales_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$dd</th>";
													} elseif ($sck == 4) {


														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}





														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $semi_annual_am1) / $semi_annual_am1), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_amount_qty - $semi_annual_am1_qty) / $semi_annual_am1_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												} else if ($rv == "Semi Annual 2") {
													$semi_annual_am2 = $target_data_arr[$ck]['ta']['q3'] + $target_data_arr[$ck]['ta']['q4'];
													$semi_annual_am2_qty = $target_data_arr[$ck]['tq']['q3'] + $target_data_arr[$ck]['tq']['q4'];

													$semi_annual_am2_sales = $sales_data_arr[$ck]['sa']['q3'] + $sales_data_arr[$ck]['sa']['q4'];

													$semi_annual_am2_sales_qty = $sales_data_arr[$ck]['sq']['q3'] + $sales_data_arr[$ck]['sq']['q4'];




													if ($sck == 1) {
														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$semi_annual_am2</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$semi_annual_am2_qty</th>";
														}
													} elseif ($sck == 2) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$semi_annual_am2_sales</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$semi_annual_am2_sales_qty</th>";
														}
													} elseif ($sck == 3) {

														if ($indicator_unit == 1) {
															$dd = number_format((float)@($semi_annual_am2 / $semi_annual_am2_sales), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($semi_annual_am2_qty / $semi_annual_am2_sales_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$dd</th>";
													} elseif ($sck == 4) {

														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}





														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $semi_annual_am2) / $semi_annual_am2), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_amount_qty - $semi_annual_am2_qty) / $semi_annual_am2_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												} else if ($rv == "Annual") {
													$annual_am = $target_data_arr[$ck]['ta']['q1'] + $target_data_arr[$ck]['ta']['q2'] + $target_data_arr[$ck]['ta']['q3'] + $target_data_arr[$ck]['ta']['q4'];

													$annual_am_qty = $target_data_arr[$ck]['tq']['q1'] + $target_data_arr[$ck]['tq']['q2'] + $target_data_arr[$ck]['tq']['q3'] + $target_data_arr[$ck]['tq']['q4'];

													$annual_am_sales = $sales_data_arr[$ck]['sa']['q1'] + $sales_data_arr[$ck]['sa']['q2'] + $sales_data_arr[$ck]['sa']['q3'] + $sales_data_arr[$ck]['sa']['q4'];

													$annual_am_sales_qty = $sales_data_arr[$ck]['sq']['q1'] + $sales_data_arr[$ck]['sq']['q2'] + $sales_data_arr[$ck]['sq']['q3'] + $sales_data_arr[$ck]['sq']['q4'];




													if ($sck == 1) {
														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$annual_am</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$annual_am_qty</th>";
														}
													} elseif ($sck == 2) {
														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$annual_am_sales</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$annual_am_sales_qty</th>";
														}
													} elseif ($sck == 3) {
														if ($indicator_unit == 1) {
															$dd = number_format((float)@($annual_am / $annual_am_sales), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($annual_am_qty / $annual_am_sales_qty), 2, '.', '');
														}


														$rows_data = $rows_data . "<th>$dd</th>";
													} elseif ($sck == 4) {
														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}





														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $annual_am_) / $annual_am), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_amount_qty - $annual_am_qty) / $annual_am_qty), 2, '.', '');
														}


														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												} else {
													$rows_data = $rows_data . "<th>..</th>";
												}
											}
										}
									}

									$rows_data = $rows_data . "</tr>";
									$row_count++;
								}
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


								foreach ($rows_list as $rk => $rv) {
									$has_month = 0;
									if (array_key_exists($rv, $months_info)) {
										$month_id = $months_info[$rv]['id'];
										$target_data = $this->get_target_data($month_id, $fiscal_year_id, 1, $find_offices, $find_territories);
										$sales_data = $this->get_sales_info($months_info[$rv]['start_date'], $months_info[$rv]['end_date'], $find_offices, $find_territories);

										/* Calculating Pre-month sales data */

										if (array_key_exists(4, $sub_columns_arr)) {
											$month_no = 1;
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$rv]['start_date'], $month_no);

											$pmonth_sales_data = $this->get_sales_info($pmonth_start_end['start_date'], $pmonth_start_end['end_date']);
										}


										$has_month = 1;
									} else {

										if ($rv == "Quarter-1") {
											$m = $q1[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 3);
										} elseif ($rv == "Quarter-2") {
											$m = $q2[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 3);
										} elseif ($rv == "Quarter-3") {
											$m = $q3[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 3);
										} elseif ($rv == "Quarter-4") {
											$m = $q4[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 3);
										} elseif ($rv == "Semi Annual 1") {
											$m = $q1[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 6);
										} elseif ($rv == "Semi Annual 2") {
											$m = $q3[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 6);
										} elseif ($rv == "Annual") {
											$m = $q1[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 12);
										}

										if (is_array($pmonth_start_end)) {
											$pre_sales_data = $this->get_sales_info($pmonth_start_end['start_date'], $pmonth_start_end['end_date']);
										}
									}







									$rows_data = $rows_data . "<tr>";
									$rows_data = $rows_data . "<td>$rv</td>";

									foreach ($columns_list as $ck => $cv) {

										foreach ($sub_columns_arr as $sck => $scv) {


											if ($has_month) {

												if (array_key_exists($ck, $target_data)) {
													$amount = $target_data[$ck]['total_amount'];
													$quantity = $target_data[$ck]['total_quantity'];
												} else {
													$amount = 0;
													$quantity = 0;
												}

												if (array_key_exists($ck, $sales_data)) {
													$sales_qty = $sales_data[$ck]['sales_qty'];
													$sales_amount = $sales_data[$ck]['sales_amount'];
												} else {
													$sales_qty = 0;
													$sales_amount = 0;
												}


												$target_data_arr[$ck]['ta'][$row_count] = $amount;
												$target_data_arr[$ck]['tq'][$row_count] = $quantity;

												$sales_data_arr[$ck]['sa'][$row_count] = $sales_amount;
												$sales_data_arr[$ck]['sq'][$row_count] = $sales_qty;

												if ($sck == 1) {
													if ($indicator_unit == 1) {
														$rows_data = $rows_data . "<th>$amount</th>";
													} else if ($indicator_unit == 2) {
														$rows_data = $rows_data . "<th>$quantity</th>";
													}
												} elseif ($sck == 2) {

													if ($indicator_unit == 1) {
														$rows_data = $rows_data . "<th>$sales_amount</th>";
													} else if ($indicator_unit == 2) {
														$rows_data = $rows_data . "<th>$sales_qty</th>";
													}
												} elseif ($sck == 3) {


													if ($indicator_unit == 1) {
														$dd = number_format((float)@($amount / $sales_amount), 2, '.', '');
														$rows_data = $rows_data . "<th>$dd</th>";
													} else if ($indicator_unit == 2) {
														$dd = number_format((float)@($quantity / $sales_qty), 2, '.', '');
														$rows_data = $rows_data . "<th>$dd</th>";
													}
												} elseif ($sck == 4) {

													if (array_key_exists($ck, $pmonth_sales_data)) {
														$p_sales_qty = $pmonth_sales_data[$ck]['sales_qty'];
														$p_sales_amount = $pmonth_sales_data[$ck]['sales_amount'];
													} else {
														$p_sales_qty = 0;
														$p_sales_amount = 0;
													}





													if ($indicator_unit == 1) {
														$achi_chg = number_format((float)@(($p_sales_amount - $sales_amount) / $sales_amount), 2, '.', '');
														$rows_data = $rows_data . "<th>$achi_chg</th>";
													} else if ($indicator_unit == 2) {
														$achi_chg = number_format((float)@(($p_sales_qty - $sales_qty) / $sales_qty), 2, '.', '');
														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												}
											} else {
												if ($rv == "Quarter-1") {

													$q1_amount = $target_data_arr[$ck]['ta'][$row_count - 1] + $target_data_arr[$ck]['ta'][$row_count - 2] + $target_data_arr[$ck]['ta'][$row_count - 3];

													$q1_amount_qty = $target_data_arr[$ck]['tq'][$row_count - 1] + $target_data_arr[$ck]['tq'][$row_count - 2] + $target_data_arr[$ck]['tq'][$row_count - 3];

													$q1_sales_amount = $sales_data_arr[$ck]['sa'][$row_count - 1] + $sales_data_arr[$ck]['sa'][$row_count - 2] + $sales_data_arr[$ck]['sa'][$row_count - 3];

													$q1_sales_qty = $sales_data_arr[$ck]['sq'][$row_count - 1] + $sales_data_arr[$ck]['sq'][$row_count - 2] + $sales_data_arr[$ck]['sq'][$row_count - 3];



													$target_data_arr[$ck]['ta']['q1'] = $q1_amount;

													$target_data_arr[$ck]['tq']['q1'] = $q1_amount_qty;

													$sales_data_arr[$ck]['sa']['q1'] = $q1_sales_amount;

													$sales_data_arr[$ck]['sq']['q1'] = $q1_sales_qty;





													if ($sck == 1) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q1_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q1_amount_qty</th>";
														}
													} elseif ($sck == 2) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q1_sales_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q1_sales_qty</th>";
														}
													} elseif ($sck == 3) {


														if ($indicator_unit == 1) {
															$dd = number_format((float)@($q1_amount / $q1_sales_amount), 2, '.', '');
															$rows_data = $rows_data . "<th>$dd</th>";
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($q1_amount_qty / $q1_sales_qty), 2, '.', '');
															$rows_data = $rows_data . "<th>$dd</th>";
														}
													} elseif ($sck == 4) {

														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}


														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $q1_amount) / $q1_amount), 2, '.', '');

															$rows_data = $rows_data . "<th>$achi_chg</th>";
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_qty - $q1_amount_qty) / $q1_amount_qty), 2, '.', '');

															$rows_data = $rows_data . "<th>$achi_chg</th>";
														}
													}
												} else if ($rv == "Quarter-2") {
													$q2_amount = $target_data_arr[$ck]['ta'][$row_count - 1] + $target_data_arr[$ck]['ta'][$row_count - 2] + $target_data_arr[$ck]['ta'][$row_count - 3];

													$q2_amount_qty = $target_data_arr[$ck]['tq'][$row_count - 1] + $target_data_arr[$ck]['tq'][$row_count - 2] + $target_data_arr[$ck]['tq'][$row_count - 3];

													$q2_sales_amount = $sales_data_arr[$ck]['sa'][$row_count - 1] + $sales_data_arr[$ck]['sa'][$row_count - 2] + $sales_data_arr[$ck]['sa'][$row_count - 3];

													$q2_sales_qty = $sales_data_arr[$ck]['sq'][$row_count - 1] + $sales_data_arr[$ck]['sq'][$row_count - 2] + $sales_data_arr[$ck]['sq'][$row_count - 3];


													$target_data_arr[$ck]['ta']['q2'] = $q2_amount;

													$target_data_arr[$ck]['tq']['q2'] = $q2_amount_qty;

													$sales_data_arr[$ck]['sa']['q2'] = $q2_sales_amount;
													$sales_data_arr[$ck]['sq']['q2'] = $q2_sales_qty;




													if ($sck == 1) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q2_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q2_amount_qty</th>";
														}
													} elseif ($sck == 2) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q2_sales_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q2_sales_qty</th>";
														}
													} elseif ($sck == 3) {

														if ($indicator_unit == 1) {
															$dd = number_format((float)@($q2_amount / $q2_sales_amount), 2, '.', '');
															$rows_data = $rows_data . "<th>$dd</th>";
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($q2_amount_qty / $q2_sales_qty), 2, '.', '');
															$rows_data = $rows_data . "<th>$dd</th>";
														}
													} elseif ($sck == 4) {

														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}

														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $q2_amount) / $q2_amount), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_qty - $q2_amount_qty) / $q2_amount_qty), 2, '.', '');
														}








														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												} else if ($rv == "Quarter-3") {
													$q3_amount = $target_data_arr[$ck]['ta'][$row_count - 1] + $target_data_arr[$ck]['ta'][$row_count - 2] + $target_data_arr[$ck]['ta'][$row_count - 3];

													$q3_amount_qty = $target_data_arr[$ck]['tq'][$row_count - 1] + $target_data_arr[$ck]['tq'][$row_count - 2] + $target_data_arr[$ck]['tq'][$row_count - 3];

													$q3_sales_amount = $sales_data_arr[$ck]['sa'][$row_count - 1] + $sales_data_arr[$ck]['sa'][$row_count - 2] + $sales_data_arr[$ck]['sa'][$row_count - 3];

													$q3_sales_amount_qty = $sales_data_arr[$ck]['sq'][$row_count - 1] + $sales_data_arr[$ck]['sq'][$row_count - 2] + $sales_data_arr[$ck]['sq'][$row_count - 3];


													$target_data_arr[$ck]['ta']['q3'] = $q3_amount;

													$target_data_arr[$ck]['tq']['q3'] = $q3_amount_qty;

													$sales_data_arr[$ck]['sa']['q3'] = $q3_sales_amount;

													$sales_data_arr[$ck]['sq']['q3'] = $q3_sales_amount_qty;


													if ($sck == 1) {
														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q3_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q3_amount_qty</th>";
														}
													} elseif ($sck == 2) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q3_sales_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q3_sales_amount_qty</th>";
														}
													} elseif ($sck == 3) {
														if ($indicator_unit == 1) {
															$dd = number_format((float)@($q3_amount / $q3_sales_amount), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($q3_amount_qty / $q3_sales_amount_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$dd</th>";
													} elseif ($sck == 4) {


														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}





														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $q3_amount) / $q3_amount), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_amount_qty - $q3_amount_qty) / $q3_amount_qty), 2, '.', '');
														}




														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												} else if ($rv == "Quarter-4") {
													$q4_amount = $target_data_arr[$ck]['ta'][$row_count - 1] + $target_data_arr[$ck]['ta'][$row_count - 2] + $target_data_arr[$ck]['ta'][$row_count - 3];

													$q4_amount_qty = $target_data_arr[$ck]['tq'][$row_count - 1] + $target_data_arr[$ck]['tq'][$row_count - 2] + $target_data_arr[$ck]['tq'][$row_count - 3];

													$q4_sales_amount = $sales_data_arr[$ck]['sa'][$row_count - 1] + $sales_data_arr[$ck]['sa'][$row_count - 2] + $sales_data_arr[$ck]['sa'][$row_count - 3];

													$q4_sales_amount_qty = $sales_data_arr[$ck]['sq'][$row_count - 1] + $sales_data_arr[$ck]['sq'][$row_count - 2] + $sales_data_arr[$ck]['sq'][$row_count - 3];


													$target_data_arr[$ck]['ta']['q4'] = $q4_amount;
													$target_data_arr[$ck]['tq']['q4'] = $q4_amount_qty;

													$sales_data_arr[$ck]['sa']['q4'] = $q4_sales_amount;
													$sales_data_arr[$ck]['sq']['q4'] = $q4_sales_amount_qty;




													if ($sck == 1) {
														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q4_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q4_amount_qty</th>";
														}
													} elseif ($sck == 2) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q4_sales_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q4_sales_amount_qty</th>";
														}
													} elseif ($sck == 3) {

														if ($indicator_unit == 1) {
															$dd = number_format((float)@($q4_amount / $q4_sales_amount), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($q4_amount_qty / $q4_sales_amount_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$dd</th>";
													} elseif ($sck == 4) {


														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}





														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $q4_amount) / $q4_amount), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_amount_qty - $q4_amount_qty) / $q4_amount_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												} else if ($rv == "Semi Annual 1") {

													$semi_annual_am1 = $target_data_arr[$ck]['ta']['q1'] + $target_data_arr[$ck]['ta']['q2'];

													$semi_annual_am1_qty = $target_data_arr[$ck]['tq']['q1'] + $target_data_arr[$ck]['tq']['q2'];

													$semi_annual_am1_sales = $sales_data_arr[$ck]['sa']['q1'] + $sales_data_arr[$ck]['sa']['q2'];

													$semi_annual_am1_sales_qty = $sales_data_arr[$ck]['sq']['q1'] + $sales_data_arr[$ck]['sq']['q2'];



													if ($sck == 1) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$semi_annual_am1</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$semi_annual_am1_qty</th>";
														}
													} elseif ($sck == 2) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$semi_annual_am1_sales</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$semi_annual_am1_sales_qty</th>";
														}
													} elseif ($sck == 3) {

														if ($indicator_unit == 1) {
															$dd = number_format((float)@($semi_annual_am1 / $semi_annual_am1_sales), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($semi_annual_am1_qty / $semi_annual_am1_sales_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$dd</th>";
													} elseif ($sck == 4) {


														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}





														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $semi_annual_am1) / $semi_annual_am1), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_amount_qty - $semi_annual_am1_qty) / $semi_annual_am1_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												} else if ($rv == "Semi Annual 2") {
													$semi_annual_am2 = $target_data_arr[$ck]['ta']['q3'] + $target_data_arr[$ck]['ta']['q4'];
													$semi_annual_am2_qty = $target_data_arr[$ck]['tq']['q3'] + $target_data_arr[$ck]['tq']['q4'];

													$semi_annual_am2_sales = $sales_data_arr[$ck]['sa']['q3'] + $sales_data_arr[$ck]['sa']['q4'];

													$semi_annual_am2_sales_qty = $sales_data_arr[$ck]['sq']['q3'] + $sales_data_arr[$ck]['sq']['q4'];




													if ($sck == 1) {
														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$semi_annual_am2</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$semi_annual_am2_qty</th>";
														}
													} elseif ($sck == 2) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$semi_annual_am2_sales</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$semi_annual_am2_sales_qty</th>";
														}
													} elseif ($sck == 3) {

														if ($indicator_unit == 1) {
															$dd = number_format((float)@($semi_annual_am2 / $semi_annual_am2_sales), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($semi_annual_am2_qty / $semi_annual_am2_sales_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$dd</th>";
													} elseif ($sck == 4) {

														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}





														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $semi_annual_am2) / $semi_annual_am2), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_amount_qty - $semi_annual_am2_qty) / $semi_annual_am2_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												} else if ($rv == "Annual") {
													$annual_am = $target_data_arr[$ck]['ta']['q1'] + $target_data_arr[$ck]['ta']['q2'] + $target_data_arr[$ck]['ta']['q3'] + $target_data_arr[$ck]['ta']['q4'];

													$annual_am_qty = $target_data_arr[$ck]['tq']['q1'] + $target_data_arr[$ck]['tq']['q2'] + $target_data_arr[$ck]['tq']['q3'] + $target_data_arr[$ck]['tq']['q4'];

													$annual_am_sales = $sales_data_arr[$ck]['sa']['q1'] + $sales_data_arr[$ck]['sa']['q2'] + $sales_data_arr[$ck]['sa']['q3'] + $sales_data_arr[$ck]['sa']['q4'];

													$annual_am_sales_qty = $sales_data_arr[$ck]['sq']['q1'] + $sales_data_arr[$ck]['sq']['q2'] + $sales_data_arr[$ck]['sq']['q3'] + $sales_data_arr[$ck]['sq']['q4'];




													if ($sck == 1) {
														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$annual_am</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$annual_am_qty</th>";
														}
													} elseif ($sck == 2) {
														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$annual_am_sales</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$annual_am_sales_qty</th>";
														}
													} elseif ($sck == 3) {
														if ($indicator_unit == 1) {
															$dd = number_format((float)@($annual_am / $annual_am_sales), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($annual_am_qty / $annual_am_sales_qty), 2, '.', '');
														}


														$rows_data = $rows_data . "<th>$dd</th>";
													} elseif ($sck == 4) {
														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}





														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $annual_am_) / $annual_am), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_amount_qty - $annual_am_qty) / $annual_am_qty), 2, '.', '');
														}


														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												} else {
													$rows_data = $rows_data . "<th>..</th>";
												}
											}
										}
									}

									$rows_data = $rows_data . "</tr>";
									$row_count++;
								}
								$tables_data[$ak]['data'] = $rows_data;
								$tables_data[$ak]['area_name'] = "Area Office Name:" . $av;
							}
						} else if ($details_type == "territory_wise") {



							$all_regoin_ids = $this->get_territory_ids($office_ids, 2);
							$details_rows = $all_regoin_ids;
							foreach ($all_regoin_ids as $ak => $av) {
								$rows_data = "";
								$target_data_arr = array();

								//$find_territories=$this->get_territory_ids($ak,2);
								//$find_offices=$this->get_all_office_ids($ak,2);

								$find_territories = array($ak => $ak);
								$find_offices = array($office_ids => $office_ids);


								foreach ($rows_list as $rk => $rv) {
									$has_month = 0;
									if (array_key_exists($rv, $months_info)) {
										$month_id = $months_info[$rv]['id'];
										$target_data = $this->get_target_data($month_id, $fiscal_year_id, 1, $find_offices, $find_territories);
										$sales_data = $this->get_sales_info($months_info[$rv]['start_date'], $months_info[$rv]['end_date'], $find_offices, $find_territories);

										/* Calculating Pre-month sales data */

										if (array_key_exists(4, $sub_columns_arr)) {
											$month_no = 1;
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$rv]['start_date'], $month_no);

											$pmonth_sales_data = $this->get_sales_info($pmonth_start_end['start_date'], $pmonth_start_end['end_date']);
										}


										$has_month = 1;
									} else {

										if ($rv == "Quarter-1") {
											$m = $q1[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 3);
										} elseif ($rv == "Quarter-2") {
											$m = $q2[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 3);
										} elseif ($rv == "Quarter-3") {
											$m = $q3[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 3);
										} elseif ($rv == "Quarter-4") {
											$m = $q4[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 3);
										} elseif ($rv == "Semi Annual 1") {
											$m = $q1[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 6);
										} elseif ($rv == "Semi Annual 2") {
											$m = $q3[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 6);
										} elseif ($rv == "Annual") {
											$m = $q1[0];
											$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 12);
										}

										if (is_array($pmonth_start_end)) {
											$pre_sales_data = $this->get_sales_info($pmonth_start_end['start_date'], $pmonth_start_end['end_date']);
										}
									}







									$rows_data = $rows_data . "<tr>";
									$rows_data = $rows_data . "<td>$rv</td>";

									foreach ($columns_list as $ck => $cv) {

										foreach ($sub_columns_arr as $sck => $scv) {


											if ($has_month) {

												if (array_key_exists($ck, $target_data)) {
													$amount = $target_data[$ck]['total_amount'];
													$quantity = $target_data[$ck]['total_quantity'];
												} else {
													$amount = 0;
													$quantity = 0;
												}

												if (array_key_exists($ck, $sales_data)) {
													$sales_qty = $sales_data[$ck]['sales_qty'];
													$sales_amount = $sales_data[$ck]['sales_amount'];
												} else {
													$sales_qty = 0;
													$sales_amount = 0;
												}


												$target_data_arr[$ck]['ta'][$row_count] = $amount;
												$target_data_arr[$ck]['tq'][$row_count] = $quantity;

												$sales_data_arr[$ck]['sa'][$row_count] = $sales_amount;
												$sales_data_arr[$ck]['sq'][$row_count] = $sales_qty;

												if ($sck == 1) {
													if ($indicator_unit == 1) {
														$rows_data = $rows_data . "<th>$amount</th>";
													} else if ($indicator_unit == 2) {
														$rows_data = $rows_data . "<th>$quantity</th>";
													}
												} elseif ($sck == 2) {

													if ($indicator_unit == 1) {
														$rows_data = $rows_data . "<th>$sales_amount</th>";
													} else if ($indicator_unit == 2) {
														$rows_data = $rows_data . "<th>$sales_qty</th>";
													}
												} elseif ($sck == 3) {


													if ($indicator_unit == 1) {
														$dd = number_format((float)@($amount / $sales_amount), 2, '.', '');
														$rows_data = $rows_data . "<th>$dd</th>";
													} else if ($indicator_unit == 2) {
														$dd = number_format((float)@($quantity / $sales_qty), 2, '.', '');
														$rows_data = $rows_data . "<th>$dd</th>";
													}
												} elseif ($sck == 4) {

													if (array_key_exists($ck, $pmonth_sales_data)) {
														$p_sales_qty = $pmonth_sales_data[$ck]['sales_qty'];
														$p_sales_amount = $pmonth_sales_data[$ck]['sales_amount'];
													} else {
														$p_sales_qty = 0;
														$p_sales_amount = 0;
													}





													if ($indicator_unit == 1) {
														$achi_chg = number_format((float)@(($p_sales_amount - $sales_amount) / $sales_amount), 2, '.', '');
														$rows_data = $rows_data . "<th>$achi_chg</th>";
													} else if ($indicator_unit == 2) {
														$achi_chg = number_format((float)@(($p_sales_qty - $sales_qty) / $sales_qty), 2, '.', '');
														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												}
											} else {
												if ($rv == "Quarter-1") {

													$q1_amount = $target_data_arr[$ck]['ta'][$row_count - 1] + $target_data_arr[$ck]['ta'][$row_count - 2] + $target_data_arr[$ck]['ta'][$row_count - 3];

													$q1_amount_qty = $target_data_arr[$ck]['tq'][$row_count - 1] + $target_data_arr[$ck]['tq'][$row_count - 2] + $target_data_arr[$ck]['tq'][$row_count - 3];

													$q1_sales_amount = $sales_data_arr[$ck]['sa'][$row_count - 1] + $sales_data_arr[$ck]['sa'][$row_count - 2] + $sales_data_arr[$ck]['sa'][$row_count - 3];

													$q1_sales_qty = $sales_data_arr[$ck]['sq'][$row_count - 1] + $sales_data_arr[$ck]['sq'][$row_count - 2] + $sales_data_arr[$ck]['sq'][$row_count - 3];



													$target_data_arr[$ck]['ta']['q1'] = $q1_amount;

													$target_data_arr[$ck]['tq']['q1'] = $q1_amount_qty;

													$sales_data_arr[$ck]['sa']['q1'] = $q1_sales_amount;

													$sales_data_arr[$ck]['sq']['q1'] = $q1_sales_qty;





													if ($sck == 1) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q1_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q1_amount_qty</th>";
														}
													} elseif ($sck == 2) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q1_sales_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q1_sales_qty</th>";
														}
													} elseif ($sck == 3) {


														if ($indicator_unit == 1) {
															$dd = number_format((float)@($q1_amount / $q1_sales_amount), 2, '.', '');
															$rows_data = $rows_data . "<th>$dd</th>";
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($q1_amount_qty / $q1_sales_qty), 2, '.', '');
															$rows_data = $rows_data . "<th>$dd</th>";
														}
													} elseif ($sck == 4) {

														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}


														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $q1_amount) / $q1_amount), 2, '.', '');

															$rows_data = $rows_data . "<th>$achi_chg</th>";
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_qty - $q1_amount_qty) / $q1_amount_qty), 2, '.', '');

															$rows_data = $rows_data . "<th>$achi_chg</th>";
														}
													}
												} else if ($rv == "Quarter-2") {
													$q2_amount = $target_data_arr[$ck]['ta'][$row_count - 1] + $target_data_arr[$ck]['ta'][$row_count - 2] + $target_data_arr[$ck]['ta'][$row_count - 3];

													$q2_amount_qty = $target_data_arr[$ck]['tq'][$row_count - 1] + $target_data_arr[$ck]['tq'][$row_count - 2] + $target_data_arr[$ck]['tq'][$row_count - 3];

													$q2_sales_amount = $sales_data_arr[$ck]['sa'][$row_count - 1] + $sales_data_arr[$ck]['sa'][$row_count - 2] + $sales_data_arr[$ck]['sa'][$row_count - 3];

													$q2_sales_qty = $sales_data_arr[$ck]['sq'][$row_count - 1] + $sales_data_arr[$ck]['sq'][$row_count - 2] + $sales_data_arr[$ck]['sq'][$row_count - 3];


													$target_data_arr[$ck]['ta']['q2'] = $q2_amount;

													$target_data_arr[$ck]['tq']['q2'] = $q2_amount_qty;

													$sales_data_arr[$ck]['sa']['q2'] = $q2_sales_amount;
													$sales_data_arr[$ck]['sq']['q2'] = $q2_sales_qty;




													if ($sck == 1) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q2_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q2_amount_qty</th>";
														}
													} elseif ($sck == 2) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q2_sales_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q2_sales_qty</th>";
														}
													} elseif ($sck == 3) {

														if ($indicator_unit == 1) {
															$dd = number_format((float)@($q2_amount / $q2_sales_amount), 2, '.', '');
															$rows_data = $rows_data . "<th>$dd</th>";
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($q2_amount_qty / $q2_sales_qty), 2, '.', '');
															$rows_data = $rows_data . "<th>$dd</th>";
														}
													} elseif ($sck == 4) {

														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}

														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $q2_amount) / $q2_amount), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_qty - $q2_amount_qty) / $q2_amount_qty), 2, '.', '');
														}








														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												} else if ($rv == "Quarter-3") {
													$q3_amount = $target_data_arr[$ck]['ta'][$row_count - 1] + $target_data_arr[$ck]['ta'][$row_count - 2] + $target_data_arr[$ck]['ta'][$row_count - 3];

													$q3_amount_qty = $target_data_arr[$ck]['tq'][$row_count - 1] + $target_data_arr[$ck]['tq'][$row_count - 2] + $target_data_arr[$ck]['tq'][$row_count - 3];

													$q3_sales_amount = $sales_data_arr[$ck]['sa'][$row_count - 1] + $sales_data_arr[$ck]['sa'][$row_count - 2] + $sales_data_arr[$ck]['sa'][$row_count - 3];

													$q3_sales_amount_qty = $sales_data_arr[$ck]['sq'][$row_count - 1] + $sales_data_arr[$ck]['sq'][$row_count - 2] + $sales_data_arr[$ck]['sq'][$row_count - 3];


													$target_data_arr[$ck]['ta']['q3'] = $q3_amount;

													$target_data_arr[$ck]['tq']['q3'] = $q3_amount_qty;

													$sales_data_arr[$ck]['sa']['q3'] = $q3_sales_amount;

													$sales_data_arr[$ck]['sq']['q3'] = $q3_sales_amount_qty;


													if ($sck == 1) {
														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q3_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q3_amount_qty</th>";
														}
													} elseif ($sck == 2) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q3_sales_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q3_sales_amount_qty</th>";
														}
													} elseif ($sck == 3) {
														if ($indicator_unit == 1) {
															$dd = number_format((float)@($q3_amount / $q3_sales_amount), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($q3_amount_qty / $q3_sales_amount_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$dd</th>";
													} elseif ($sck == 4) {


														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}





														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $q3_amount) / $q3_amount), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_amount_qty - $q3_amount_qty) / $q3_amount_qty), 2, '.', '');
														}




														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												} else if ($rv == "Quarter-4") {
													$q4_amount = $target_data_arr[$ck]['ta'][$row_count - 1] + $target_data_arr[$ck]['ta'][$row_count - 2] + $target_data_arr[$ck]['ta'][$row_count - 3];

													$q4_amount_qty = $target_data_arr[$ck]['tq'][$row_count - 1] + $target_data_arr[$ck]['tq'][$row_count - 2] + $target_data_arr[$ck]['tq'][$row_count - 3];

													$q4_sales_amount = $sales_data_arr[$ck]['sa'][$row_count - 1] + $sales_data_arr[$ck]['sa'][$row_count - 2] + $sales_data_arr[$ck]['sa'][$row_count - 3];

													$q4_sales_amount_qty = $sales_data_arr[$ck]['sq'][$row_count - 1] + $sales_data_arr[$ck]['sq'][$row_count - 2] + $sales_data_arr[$ck]['sq'][$row_count - 3];


													$target_data_arr[$ck]['ta']['q4'] = $q4_amount;
													$target_data_arr[$ck]['tq']['q4'] = $q4_amount_qty;

													$sales_data_arr[$ck]['sa']['q4'] = $q4_sales_amount;
													$sales_data_arr[$ck]['sq']['q4'] = $q4_sales_amount_qty;




													if ($sck == 1) {
														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q4_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q4_amount_qty</th>";
														}
													} elseif ($sck == 2) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$q4_sales_amount</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$q4_sales_amount_qty</th>";
														}
													} elseif ($sck == 3) {

														if ($indicator_unit == 1) {
															$dd = number_format((float)@($q4_amount / $q4_sales_amount), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($q4_amount_qty / $q4_sales_amount_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$dd</th>";
													} elseif ($sck == 4) {


														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}





														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $q4_amount) / $q4_amount), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_amount_qty - $q4_amount_qty) / $q4_amount_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												} else if ($rv == "Semi Annual 1") {

													$semi_annual_am1 = $target_data_arr[$ck]['ta']['q1'] + $target_data_arr[$ck]['ta']['q2'];

													$semi_annual_am1_qty = $target_data_arr[$ck]['tq']['q1'] + $target_data_arr[$ck]['tq']['q2'];

													$semi_annual_am1_sales = $sales_data_arr[$ck]['sa']['q1'] + $sales_data_arr[$ck]['sa']['q2'];

													$semi_annual_am1_sales_qty = $sales_data_arr[$ck]['sq']['q1'] + $sales_data_arr[$ck]['sq']['q2'];



													if ($sck == 1) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$semi_annual_am1</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$semi_annual_am1_qty</th>";
														}
													} elseif ($sck == 2) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$semi_annual_am1_sales</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$semi_annual_am1_sales_qty</th>";
														}
													} elseif ($sck == 3) {

														if ($indicator_unit == 1) {
															$dd = number_format((float)@($semi_annual_am1 / $semi_annual_am1_sales), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($semi_annual_am1_qty / $semi_annual_am1_sales_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$dd</th>";
													} elseif ($sck == 4) {


														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}





														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $semi_annual_am1) / $semi_annual_am1), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_amount_qty - $semi_annual_am1_qty) / $semi_annual_am1_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												} else if ($rv == "Semi Annual 2") {
													$semi_annual_am2 = $target_data_arr[$ck]['ta']['q3'] + $target_data_arr[$ck]['ta']['q4'];
													$semi_annual_am2_qty = $target_data_arr[$ck]['tq']['q3'] + $target_data_arr[$ck]['tq']['q4'];

													$semi_annual_am2_sales = $sales_data_arr[$ck]['sa']['q3'] + $sales_data_arr[$ck]['sa']['q4'];

													$semi_annual_am2_sales_qty = $sales_data_arr[$ck]['sq']['q3'] + $sales_data_arr[$ck]['sq']['q4'];




													if ($sck == 1) {
														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$semi_annual_am2</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$semi_annual_am2_qty</th>";
														}
													} elseif ($sck == 2) {

														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$semi_annual_am2_sales</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$semi_annual_am2_sales_qty</th>";
														}
													} elseif ($sck == 3) {

														if ($indicator_unit == 1) {
															$dd = number_format((float)@($semi_annual_am2 / $semi_annual_am2_sales), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($semi_annual_am2_qty / $semi_annual_am2_sales_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$dd</th>";
													} elseif ($sck == 4) {

														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}





														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $semi_annual_am2) / $semi_annual_am2), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_amount_qty - $semi_annual_am2_qty) / $semi_annual_am2_qty), 2, '.', '');
														}



														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												} else if ($rv == "Annual") {
													$annual_am = $target_data_arr[$ck]['ta']['q1'] + $target_data_arr[$ck]['ta']['q2'] + $target_data_arr[$ck]['ta']['q3'] + $target_data_arr[$ck]['ta']['q4'];

													$annual_am_qty = $target_data_arr[$ck]['tq']['q1'] + $target_data_arr[$ck]['tq']['q2'] + $target_data_arr[$ck]['tq']['q3'] + $target_data_arr[$ck]['tq']['q4'];

													$annual_am_sales = $sales_data_arr[$ck]['sa']['q1'] + $sales_data_arr[$ck]['sa']['q2'] + $sales_data_arr[$ck]['sa']['q3'] + $sales_data_arr[$ck]['sa']['q4'];

													$annual_am_sales_qty = $sales_data_arr[$ck]['sq']['q1'] + $sales_data_arr[$ck]['sq']['q2'] + $sales_data_arr[$ck]['sq']['q3'] + $sales_data_arr[$ck]['sq']['q4'];




													if ($sck == 1) {
														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$annual_am</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$annual_am_qty</th>";
														}
													} elseif ($sck == 2) {
														if ($indicator_unit == 1) {
															$rows_data = $rows_data . "<th>$annual_am_sales</th>";
														} else if ($indicator_unit == 2) {
															$rows_data = $rows_data . "<th>$annual_am_sales_qty</th>";
														}
													} elseif ($sck == 3) {
														if ($indicator_unit == 1) {
															$dd = number_format((float)@($annual_am / $annual_am_sales), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$dd = number_format((float)@($annual_am_qty / $annual_am_sales_qty), 2, '.', '');
														}


														$rows_data = $rows_data . "<th>$dd</th>";
													} elseif ($sck == 4) {
														if (array_key_exists($ck, $pre_sales_data)) {
															$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
															$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
														} else {
															$p_sales_qty = 0;
															$p_sales_amount = 0;
														}





														if ($indicator_unit == 1) {
															$achi_chg = number_format((float)@(($p_sales_amount - $annual_am_) / $annual_am), 2, '.', '');
														} else if ($indicator_unit == 2) {
															$achi_chg = number_format((float)@(($p_sales_amount_qty - $annual_am_qty) / $annual_am_qty), 2, '.', '');
														}


														$rows_data = $rows_data . "<th>$achi_chg</th>";
													}
												} else {
													$rows_data = $rows_data . "<th>..</th>";
												}
											}
										}
									}

									$rows_data = $rows_data . "</tr>";
									$row_count++;
								}
								$tables_data[$ak]['data'] = $rows_data;
								$tables_data[$ak]['area_name'] = "Territory Name:" . $av;
							}
						}

						$this->set(compact('tables_data'));
					} else {

						foreach ($rows_list as $rk => $rv) {
							$has_month = 0;
							if (array_key_exists($rv, $months_info)) {
								$month_id = $months_info[$rv]['id'];
								$target_data = $this->get_target_data($month_id, $fiscal_year_id, 1, $find_offices, $find_territories);
								$sales_data = $this->get_sales_info($months_info[$rv]['start_date'], $months_info[$rv]['end_date'], $find_offices, $find_territories);

								/* Calculating Pre-month sales data */

								if (array_key_exists(4, $sub_columns_arr)) {
									$month_no = 1;
									$pmonth_start_end = $this->get_pre_month_start_end($months_info[$rv]['start_date'], $month_no);

									$pmonth_sales_data = $this->get_sales_info($pmonth_start_end['start_date'], $pmonth_start_end['end_date']);
								}


								$has_month = 1;
							} else {

								if ($rv == "Quarter-1") {
									$m = $q1[0];
									$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 3);
								} elseif ($rv == "Quarter-2") {
									$m = $q2[0];
									$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 3);
								} elseif ($rv == "Quarter-3") {
									$m = $q3[0];
									$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 3);
								} elseif ($rv == "Quarter-4") {
									$m = $q4[0];
									$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 3);
								} elseif ($rv == "Semi Annual 1") {
									$m = $q1[0];
									$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 6);
								} elseif ($rv == "Semi Annual 2") {
									$m = $q3[0];
									$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 6);
								} elseif ($rv == "Annual") {
									$m = $q1[0];
									$pmonth_start_end = $this->get_pre_month_start_end($months_info[$m]['start_date'], 12);
								}

								if (is_array($pmonth_start_end)) {
									$pre_sales_data = $this->get_sales_info($pmonth_start_end['start_date'], $pmonth_start_end['end_date']);
								}
							}







							$rows_data = $rows_data . "<tr>";
							$rows_data = $rows_data . "<td>$rv</td>";

							foreach ($columns_list as $ck => $cv) {

								foreach ($sub_columns_arr as $sck => $scv) {


									if ($has_month) {

										if (array_key_exists($ck, $target_data)) {
											$amount = $target_data[$ck]['total_amount'];
											$quantity = $target_data[$ck]['total_quantity'];
										} else {
											$amount = 0;
											$quantity = 0;
										}

										if (array_key_exists($ck, $sales_data)) {
											$sales_qty = $sales_data[$ck]['sales_qty'];
											$sales_amount = $sales_data[$ck]['sales_amount'];
										} else {
											$sales_qty = 0;
											$sales_amount = 0;
										}


										$target_data_arr[$ck]['ta'][$row_count] = $amount;
										$target_data_arr[$ck]['tq'][$row_count] = $quantity;

										$sales_data_arr[$ck]['sa'][$row_count] = $sales_amount;
										$sales_data_arr[$ck]['sq'][$row_count] = $sales_qty;

										if ($sck == 1) {
											if ($indicator_unit == 1) {
												$rows_data = $rows_data . "<th>$amount</th>";
											} else if ($indicator_unit == 2) {
												$rows_data = $rows_data . "<th>$quantity</th>";
											}
										} elseif ($sck == 2) {

											if ($indicator_unit == 1) {
												$rows_data = $rows_data . "<th>$sales_amount</th>";
											} else if ($indicator_unit == 2) {
												$rows_data = $rows_data . "<th>$sales_qty</th>";
											}
										} elseif ($sck == 3) {


											if ($indicator_unit == 1) {
												$dd = number_format((float)@($amount / $sales_amount), 2, '.', '');
												$rows_data = $rows_data . "<th>$dd</th>";
											} else if ($indicator_unit == 2) {
												$dd = number_format((float)@($quantity / $sales_qty), 2, '.', '');
												$rows_data = $rows_data . "<th>$dd</th>";
											}
										} elseif ($sck == 4) {

											if (array_key_exists($ck, $pmonth_sales_data)) {
												$p_sales_qty = $pmonth_sales_data[$ck]['sales_qty'];
												$p_sales_amount = $pmonth_sales_data[$ck]['sales_amount'];
											} else {
												$p_sales_qty = 0;
												$p_sales_amount = 0;
											}





											if ($indicator_unit == 1) {
												$achi_chg = number_format((float)@(($p_sales_amount - $sales_amount) / $sales_amount), 2, '.', '');
												$rows_data = $rows_data . "<th>$achi_chg</th>";
											} else if ($indicator_unit == 2) {
												$achi_chg = number_format((float)@(($p_sales_qty - $sales_qty) / $sales_qty), 2, '.', '');
												$rows_data = $rows_data . "<th>$achi_chg</th>";
											}
										}
									} else {
										if ($rv == "Quarter-1") {

											$q1_amount = $target_data_arr[$ck]['ta'][$row_count - 1] + $target_data_arr[$ck]['ta'][$row_count - 2] + $target_data_arr[$ck]['ta'][$row_count - 3];

											$q1_amount_qty = $target_data_arr[$ck]['tq'][$row_count - 1] + $target_data_arr[$ck]['tq'][$row_count - 2] + $target_data_arr[$ck]['tq'][$row_count - 3];

											$q1_sales_amount = $sales_data_arr[$ck]['sa'][$row_count - 1] + $sales_data_arr[$ck]['sa'][$row_count - 2] + $sales_data_arr[$ck]['sa'][$row_count - 3];

											$q1_sales_qty = $sales_data_arr[$ck]['sq'][$row_count - 1] + $sales_data_arr[$ck]['sq'][$row_count - 2] + $sales_data_arr[$ck]['sq'][$row_count - 3];



											$target_data_arr[$ck]['ta']['q1'] = $q1_amount;

											$target_data_arr[$ck]['tq']['q1'] = $q1_amount_qty;

											$sales_data_arr[$ck]['sa']['q1'] = $q1_sales_amount;

											$sales_data_arr[$ck]['sq']['q1'] = $q1_sales_qty;





											if ($sck == 1) {

												if ($indicator_unit == 1) {
													$rows_data = $rows_data . "<th>$q1_amount</th>";
												} else if ($indicator_unit == 2) {
													$rows_data = $rows_data . "<th>$q1_amount_qty</th>";
												}
											} elseif ($sck == 2) {

												if ($indicator_unit == 1) {
													$rows_data = $rows_data . "<th>$q1_sales_amount</th>";
												} else if ($indicator_unit == 2) {
													$rows_data = $rows_data . "<th>$q1_sales_qty</th>";
												}
											} elseif ($sck == 3) {


												if ($indicator_unit == 1) {
													$dd = number_format((float)@($q1_amount / $q1_sales_amount), 2, '.', '');
													$rows_data = $rows_data . "<th>$dd</th>";
												} else if ($indicator_unit == 2) {
													$dd = number_format((float)@($q1_amount_qty / $q1_sales_qty), 2, '.', '');
													$rows_data = $rows_data . "<th>$dd</th>";
												}
											} elseif ($sck == 4) {

												if (array_key_exists($ck, $pre_sales_data)) {
													$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
													$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
												} else {
													$p_sales_qty = 0;
													$p_sales_amount = 0;
												}


												if ($indicator_unit == 1) {
													$achi_chg = number_format((float)@(($p_sales_amount - $q1_amount) / $q1_amount), 2, '.', '');

													$rows_data = $rows_data . "<th>$achi_chg</th>";
												} else if ($indicator_unit == 2) {
													$achi_chg = number_format((float)@(($p_sales_qty - $q1_amount_qty) / $q1_amount_qty), 2, '.', '');

													$rows_data = $rows_data . "<th>$achi_chg</th>";
												}
											}
										} else if ($rv == "Quarter-2") {
											$q2_amount = $target_data_arr[$ck]['ta'][$row_count - 1] + $target_data_arr[$ck]['ta'][$row_count - 2] + $target_data_arr[$ck]['ta'][$row_count - 3];

											$q2_amount_qty = $target_data_arr[$ck]['tq'][$row_count - 1] + $target_data_arr[$ck]['tq'][$row_count - 2] + $target_data_arr[$ck]['tq'][$row_count - 3];

											$q2_sales_amount = $sales_data_arr[$ck]['sa'][$row_count - 1] + $sales_data_arr[$ck]['sa'][$row_count - 2] + $sales_data_arr[$ck]['sa'][$row_count - 3];

											$q2_sales_qty = $sales_data_arr[$ck]['sq'][$row_count - 1] + $sales_data_arr[$ck]['sq'][$row_count - 2] + $sales_data_arr[$ck]['sq'][$row_count - 3];


											$target_data_arr[$ck]['ta']['q2'] = $q2_amount;

											$target_data_arr[$ck]['tq']['q2'] = $q2_amount_qty;

											$sales_data_arr[$ck]['sa']['q2'] = $q2_sales_amount;
											$sales_data_arr[$ck]['sq']['q2'] = $q2_sales_qty;




											if ($sck == 1) {

												if ($indicator_unit == 1) {
													$rows_data = $rows_data . "<th>$q2_amount</th>";
												} else if ($indicator_unit == 2) {
													$rows_data = $rows_data . "<th>$q2_amount_qty</th>";
												}
											} elseif ($sck == 2) {

												if ($indicator_unit == 1) {
													$rows_data = $rows_data . "<th>$q2_sales_amount</th>";
												} else if ($indicator_unit == 2) {
													$rows_data = $rows_data . "<th>$q2_sales_qty</th>";
												}
											} elseif ($sck == 3) {

												if ($indicator_unit == 1) {
													$dd = number_format((float)@($q2_amount / $q2_sales_amount), 2, '.', '');
													$rows_data = $rows_data . "<th>$dd</th>";
												} else if ($indicator_unit == 2) {
													$dd = number_format((float)@($q2_amount_qty / $q2_sales_qty), 2, '.', '');
													$rows_data = $rows_data . "<th>$dd</th>";
												}
											} elseif ($sck == 4) {

												if (array_key_exists($ck, $pre_sales_data)) {
													$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
													$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
												} else {
													$p_sales_qty = 0;
													$p_sales_amount = 0;
												}

												if ($indicator_unit == 1) {
													$achi_chg = number_format((float)@(($p_sales_amount - $q2_amount) / $q2_amount), 2, '.', '');
												} else if ($indicator_unit == 2) {
													$achi_chg = number_format((float)@(($p_sales_qty - $q2_amount_qty) / $q2_amount_qty), 2, '.', '');
												}








												$rows_data = $rows_data . "<th>$achi_chg</th>";
											}
										} else if ($rv == "Quarter-3") {
											$q3_amount = $target_data_arr[$ck]['ta'][$row_count - 1] + $target_data_arr[$ck]['ta'][$row_count - 2] + $target_data_arr[$ck]['ta'][$row_count - 3];

											$q3_amount_qty = $target_data_arr[$ck]['tq'][$row_count - 1] + $target_data_arr[$ck]['tq'][$row_count - 2] + $target_data_arr[$ck]['tq'][$row_count - 3];

											$q3_sales_amount = $sales_data_arr[$ck]['sa'][$row_count - 1] + $sales_data_arr[$ck]['sa'][$row_count - 2] + $sales_data_arr[$ck]['sa'][$row_count - 3];

											$q3_sales_amount_qty = $sales_data_arr[$ck]['sq'][$row_count - 1] + $sales_data_arr[$ck]['sq'][$row_count - 2] + $sales_data_arr[$ck]['sq'][$row_count - 3];


											$target_data_arr[$ck]['ta']['q3'] = $q3_amount;

											$target_data_arr[$ck]['tq']['q3'] = $q3_amount_qty;

											$sales_data_arr[$ck]['sa']['q3'] = $q3_sales_amount;

											$sales_data_arr[$ck]['sq']['q3'] = $q3_sales_amount_qty;


											if ($sck == 1) {
												if ($indicator_unit == 1) {
													$rows_data = $rows_data . "<th>$q3_amount</th>";
												} else if ($indicator_unit == 2) {
													$rows_data = $rows_data . "<th>$q3_amount_qty</th>";
												}
											} elseif ($sck == 2) {

												if ($indicator_unit == 1) {
													$rows_data = $rows_data . "<th>$q3_sales_amount</th>";
												} else if ($indicator_unit == 2) {
													$rows_data = $rows_data . "<th>$q3_sales_amount_qty</th>";
												}
											} elseif ($sck == 3) {
												if ($indicator_unit == 1) {
													$dd = number_format((float)@($q3_amount / $q3_sales_amount), 2, '.', '');
												} else if ($indicator_unit == 2) {
													$dd = number_format((float)@($q3_amount_qty / $q3_sales_amount_qty), 2, '.', '');
												}



												$rows_data = $rows_data . "<th>$dd</th>";
											} elseif ($sck == 4) {


												if (array_key_exists($ck, $pre_sales_data)) {
													$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
													$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
												} else {
													$p_sales_qty = 0;
													$p_sales_amount = 0;
												}





												if ($indicator_unit == 1) {
													$achi_chg = number_format((float)@(($p_sales_amount - $q3_amount) / $q3_amount), 2, '.', '');
												} else if ($indicator_unit == 2) {
													$achi_chg = number_format((float)@(($p_sales_amount_qty - $q3_amount_qty) / $q3_amount_qty), 2, '.', '');
												}




												$rows_data = $rows_data . "<th>$achi_chg</th>";
											}
										} else if ($rv == "Quarter-4") {
											$q4_amount = $target_data_arr[$ck]['ta'][$row_count - 1] + $target_data_arr[$ck]['ta'][$row_count - 2] + $target_data_arr[$ck]['ta'][$row_count - 3];

											$q4_amount_qty = $target_data_arr[$ck]['tq'][$row_count - 1] + $target_data_arr[$ck]['tq'][$row_count - 2] + $target_data_arr[$ck]['tq'][$row_count - 3];

											$q4_sales_amount = $sales_data_arr[$ck]['sa'][$row_count - 1] + $sales_data_arr[$ck]['sa'][$row_count - 2] + $sales_data_arr[$ck]['sa'][$row_count - 3];

											$q4_sales_amount_qty = $sales_data_arr[$ck]['sq'][$row_count - 1] + $sales_data_arr[$ck]['sq'][$row_count - 2] + $sales_data_arr[$ck]['sq'][$row_count - 3];


											$target_data_arr[$ck]['ta']['q4'] = $q4_amount;
											$target_data_arr[$ck]['tq']['q4'] = $q4_amount_qty;

											$sales_data_arr[$ck]['sa']['q4'] = $q4_sales_amount;
											$sales_data_arr[$ck]['sq']['q4'] = $q4_sales_amount_qty;




											if ($sck == 1) {
												if ($indicator_unit == 1) {
													$rows_data = $rows_data . "<th>$q4_amount</th>";
												} else if ($indicator_unit == 2) {
													$rows_data = $rows_data . "<th>$q4_amount_qty</th>";
												}
											} elseif ($sck == 2) {

												if ($indicator_unit == 1) {
													$rows_data = $rows_data . "<th>$q4_sales_amount</th>";
												} else if ($indicator_unit == 2) {
													$rows_data = $rows_data . "<th>$q4_sales_amount_qty</th>";
												}
											} elseif ($sck == 3) {

												if ($indicator_unit == 1) {
													$dd = number_format((float)@($q4_amount / $q4_sales_amount), 2, '.', '');
												} else if ($indicator_unit == 2) {
													$dd = number_format((float)@($q4_amount_qty / $q4_sales_amount_qty), 2, '.', '');
												}



												$rows_data = $rows_data . "<th>$dd</th>";
											} elseif ($sck == 4) {


												if (array_key_exists($ck, $pre_sales_data)) {
													$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
													$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
												} else {
													$p_sales_qty = 0;
													$p_sales_amount = 0;
												}





												if ($indicator_unit == 1) {
													$achi_chg = number_format((float)@(($p_sales_amount - $q4_amount) / $q4_amount), 2, '.', '');
												} else if ($indicator_unit == 2) {
													$achi_chg = number_format((float)@(($p_sales_amount_qty - $q4_amount_qty) / $q4_amount_qty), 2, '.', '');
												}



												$rows_data = $rows_data . "<th>$achi_chg</th>";
											}
										} else if ($rv == "Semi Annual 1") {

											$semi_annual_am1 = $target_data_arr[$ck]['ta']['q1'] + $target_data_arr[$ck]['ta']['q2'];

											$semi_annual_am1_qty = $target_data_arr[$ck]['tq']['q1'] + $target_data_arr[$ck]['tq']['q2'];

											$semi_annual_am1_sales = $sales_data_arr[$ck]['sa']['q1'] + $sales_data_arr[$ck]['sa']['q2'];

											$semi_annual_am1_sales_qty = $sales_data_arr[$ck]['sq']['q1'] + $sales_data_arr[$ck]['sq']['q2'];



											if ($sck == 1) {

												if ($indicator_unit == 1) {
													$rows_data = $rows_data . "<th>$semi_annual_am1</th>";
												} else if ($indicator_unit == 2) {
													$rows_data = $rows_data . "<th>$semi_annual_am1_qty</th>";
												}
											} elseif ($sck == 2) {

												if ($indicator_unit == 1) {
													$rows_data = $rows_data . "<th>$semi_annual_am1_sales</th>";
												} else if ($indicator_unit == 2) {
													$rows_data = $rows_data . "<th>$semi_annual_am1_sales_qty</th>";
												}
											} elseif ($sck == 3) {

												if ($indicator_unit == 1) {
													$dd = number_format((float)@($semi_annual_am1 / $semi_annual_am1_sales), 2, '.', '');
												} else if ($indicator_unit == 2) {
													$dd = number_format((float)@($semi_annual_am1_qty / $semi_annual_am1_sales_qty), 2, '.', '');
												}



												$rows_data = $rows_data . "<th>$dd</th>";
											} elseif ($sck == 4) {


												if (array_key_exists($ck, $pre_sales_data)) {
													$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
													$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
												} else {
													$p_sales_qty = 0;
													$p_sales_amount = 0;
												}





												if ($indicator_unit == 1) {
													$achi_chg = number_format((float)@(($p_sales_amount - $semi_annual_am1) / $semi_annual_am1), 2, '.', '');
												} else if ($indicator_unit == 2) {
													$achi_chg = number_format((float)@(($p_sales_amount_qty - $semi_annual_am1_qty) / $semi_annual_am1_qty), 2, '.', '');
												}



												$rows_data = $rows_data . "<th>$achi_chg</th>";
											}
										} else if ($rv == "Semi Annual 2") {
											$semi_annual_am2 = $target_data_arr[$ck]['ta']['q3'] + $target_data_arr[$ck]['ta']['q4'];
											$semi_annual_am2_qty = $target_data_arr[$ck]['tq']['q3'] + $target_data_arr[$ck]['tq']['q4'];

											$semi_annual_am2_sales = $sales_data_arr[$ck]['sa']['q3'] + $sales_data_arr[$ck]['sa']['q4'];

											$semi_annual_am2_sales_qty = $sales_data_arr[$ck]['sq']['q3'] + $sales_data_arr[$ck]['sq']['q4'];




											if ($sck == 1) {
												if ($indicator_unit == 1) {
													$rows_data = $rows_data . "<th>$semi_annual_am2</th>";
												} else if ($indicator_unit == 2) {
													$rows_data = $rows_data . "<th>$semi_annual_am2_qty</th>";
												}
											} elseif ($sck == 2) {

												if ($indicator_unit == 1) {
													$rows_data = $rows_data . "<th>$semi_annual_am2_sales</th>";
												} else if ($indicator_unit == 2) {
													$rows_data = $rows_data . "<th>$semi_annual_am2_sales_qty</th>";
												}
											} elseif ($sck == 3) {

												if ($indicator_unit == 1) {
													$dd = number_format((float)@($semi_annual_am2 / $semi_annual_am2_sales), 2, '.', '');
												} else if ($indicator_unit == 2) {
													$dd = number_format((float)@($semi_annual_am2_qty / $semi_annual_am2_sales_qty), 2, '.', '');
												}



												$rows_data = $rows_data . "<th>$dd</th>";
											} elseif ($sck == 4) {

												if (array_key_exists($ck, $pre_sales_data)) {
													$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
													$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
												} else {
													$p_sales_qty = 0;
													$p_sales_amount = 0;
												}





												if ($indicator_unit == 1) {
													$achi_chg = number_format((float)@(($p_sales_amount - $semi_annual_am2) / $semi_annual_am2), 2, '.', '');
												} else if ($indicator_unit == 2) {
													$achi_chg = number_format((float)@(($p_sales_amount_qty - $semi_annual_am2_qty) / $semi_annual_am2_qty), 2, '.', '');
												}



												$rows_data = $rows_data . "<th>$achi_chg</th>";
											}
										} else if ($rv == "Annual") {
											$annual_am = $target_data_arr[$ck]['ta']['q1'] + $target_data_arr[$ck]['ta']['q2'] + $target_data_arr[$ck]['ta']['q3'] + $target_data_arr[$ck]['ta']['q4'];

											$annual_am_qty = $target_data_arr[$ck]['tq']['q1'] + $target_data_arr[$ck]['tq']['q2'] + $target_data_arr[$ck]['tq']['q3'] + $target_data_arr[$ck]['tq']['q4'];

											$annual_am_sales = $sales_data_arr[$ck]['sa']['q1'] + $sales_data_arr[$ck]['sa']['q2'] + $sales_data_arr[$ck]['sa']['q3'] + $sales_data_arr[$ck]['sa']['q4'];

											$annual_am_sales_qty = $sales_data_arr[$ck]['sq']['q1'] + $sales_data_arr[$ck]['sq']['q2'] + $sales_data_arr[$ck]['sq']['q3'] + $sales_data_arr[$ck]['sq']['q4'];




											if ($sck == 1) {
												if ($indicator_unit == 1) {
													$rows_data = $rows_data . "<th>$annual_am</th>";
												} else if ($indicator_unit == 2) {
													$rows_data = $rows_data . "<th>$annual_am_qty</th>";
												}
											} elseif ($sck == 2) {
												if ($indicator_unit == 1) {
													$rows_data = $rows_data . "<th>$annual_am_sales</th>";
												} else if ($indicator_unit == 2) {
													$rows_data = $rows_data . "<th>$annual_am_sales_qty</th>";
												}
											} elseif ($sck == 3) {
												if ($indicator_unit == 1) {
													$dd = number_format((float)@($annual_am / $annual_am_sales), 2, '.', '');
												} else if ($indicator_unit == 2) {
													$dd = number_format((float)@($annual_am_qty / $annual_am_sales_qty), 2, '.', '');
												}


												$rows_data = $rows_data . "<th>$dd</th>";
											} elseif ($sck == 4) {
												if (array_key_exists($ck, $pre_sales_data)) {
													$p_sales_qty = $pre_sales_data[$ck]['sales_qty'];
													$p_sales_amount = $pre_sales_data[$ck]['sales_amount'];
												} else {
													$p_sales_qty = 0;
													$p_sales_amount = 0;
												}





												if ($indicator_unit == 1) {
													$achi_chg = number_format((float)@(($p_sales_amount - $annual_am_) / $annual_am), 2, '.', '');
												} else if ($indicator_unit == 2) {
													$achi_chg = number_format((float)@(($p_sales_amount_qty - $annual_am_qty) / $annual_am_qty), 2, '.', '');
												}


												$rows_data = $rows_data . "<th>$achi_chg</th>";
											}
										} else {
											$rows_data = $rows_data . "<th>..</th>";
										}
									}
								}
							}

							$rows_data = $rows_data . "</tr>";
							$row_count++;
						}
					}



					$this->set(compact('rows_data'));
				} else {
					$this->Session->setFlash(__('Please select an Fiscal Year!'), 'flash/error');
				}
			} else if (!$this->request->data['ProjectionAchievementReports']['indicators']) {
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

	public function get_target_data($month_id, $fiscal_year_id, $unit_type, $office_ids = array(), $territory_ids = array())
	{


		//get total_target

		$this->loadModel('SaleTargetMonth');
		$total_target_result = array();
		$target_data = array();

		$conditions = array(
			'SaleTargetMonth.fiscal_year_id' => $fiscal_year_id,
			'SaleTargetMonth.month_id' => $month_id
		);

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

	public function get_month_info($fiscal_year_id)
	{

		$this->loadModel('Month');

		$month_result = $this->Month->find(
			'all',
			array(
				'fields' => array("Month.id", "Month.name", "Month.month"),
				//'conditions'=> array("Month.fiscal_year_id" => $fiscal_year_id),
				'recursive' => -1
			)
		);
		return $month_result;
	}

	public function get_month_start_end_info($fiscal_year_id, $years_info)
	{

		$month_info = $years_info[$fiscal_year_id];
		$start_date = $month_info['start_date'];
		$end_date = $month_info['end_date'];

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


	public function get_sales_info($start_date, $end_date, $offices = array(), $territory_ids = array())
	{

		$total_sales_info = array();

		$where = "";


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
}
