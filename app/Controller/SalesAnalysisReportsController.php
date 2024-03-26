
<?php
App::uses('AppController', 'Controller');
/**
 * EsalesSettings Controller
 *
 * @property SalesAnalysisReport $SalesAnalysisReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */

class SalesAnalysisReportsController extends AppController
{



	/**
	 * Components
	 *
	 * @var array
	 */

	public $uses = array('Memo', 'MemoDetail', 'Office', 'Territory', 'OutletCategory', 'Outlet', 'Product', 'SalesPerson', 'Division', 'District', 'Thana', 'Brand', 'ProductCategory', 'Program', 'TerritoryAssignHistory', 'NotundinProgram');
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


		ini_set('memory_limit', '512M');
		ini_set('max_execution_time', 99999); //300 seconds = 5 minutes


		$this->set('page_title', 'Sales Analysis Report');

		$request_data = array();

		//for outlet category list
		$outlet_categories = $this->OutletCategory->find('list', array(
			//'conditions' => array('is_active'=>1),
			'order' => array('id' => 'asc')
		));
		$this->set(compact('outlet_categories'));

		$conditions = array(
			//'is_active' => 1
		);
		$outlet_category_ids = isset($this->request->data['SalesAnalysisReports']['outlet_category_id']) != '' ? $this->request->data['SalesAnalysisReports']['outlet_category_id'] : 0;
		if ($outlet_category_ids) {
			$conditions['id'] = $outlet_category_ids;
		}
		$outlet_categories2 = $this->OutletCategory->find('list', array(
			'conditions' => $conditions,
			'order' => array('id' => 'asc')
		));


		//for brands list
		$conditions = array(
			'NOT' => array('Brand.id' => 44)
		);
		$brands = $this->Brand->find('list', array(
			'conditions' => $conditions,
			'order' => array('id' => 'asc')
		));
		$this->set(compact('brands'));


		$conditions = array(
			'NOT' => array('Brand.id' => 44)
		);
		$brand_ids = isset($this->request->data['SalesAnalysisReports']['brand_id']) != '' ? $this->request->data['SalesAnalysisReports']['brand_id'] : 0;
		if ($brand_ids) {
			$conditions['id'] = $brand_ids;
		}
		$brands2 = $this->Brand->find('list', array(
			'conditions' => $conditions,
			'order' => array('id' => 'asc')
		));


		//report type
		$unit_types = array(
			'1' => 'Sales Unit',
			'2' => 'Base Unit',
		);
		$this->set(compact('unit_types'));


		//for cateogry list
		$conditions = array(
			'NOT' => array('ProductCategory.id' => 32)
		);
		//$product_category_ids = isset($this->request->data['SalesAnalysisReports']['product_category_id']) != '' ? $this->request->data['SalesAnalysisReports']['product_category_id'] : 0;
		//if($cateogry_ids)$conditions['id'] = $product_category_ids;
		$categories = $this->ProductCategory->find('list', array(
			'conditions' => $conditions,
			'order' => array('id' => 'asc')
		));
		$this->set(compact('categories'));

		$conditions = array(
			'NOT' => array('ProductCategory.id' => 32)
		);
		$product_category_ids = isset($this->request->data['SalesAnalysisReports']['product_category_id']) != '' ? $this->request->data['SalesAnalysisReports']['product_category_id'] : 0;
		if ($product_category_ids) {
			$conditions['id'] = $product_category_ids;
		}
		$category_list2 = $this->ProductCategory->find('list', array(
			'conditions' => $conditions,
			'order' => array('id' => 'asc')
		));


		//for rows
		$rows = array(
			'so'              => 'By SO',
			'territory'       => 'By Territory',
			'area'            => 'By Area',
			'month'           => 'By Month',
			'division'        => 'By Division',
			'district'        => 'By District',
			'thana'           => 'By Thana',
			'national'        => 'By National',
			'region'          => 'By Region'
		);

		$this->set(compact('rows'));

		//for target rows
		$rows_for_target = array(
			'territory'    => 'By Territory',
			'area'         => 'By Area',
			'national'     => 'By National',
			'region'       => 'By Region'
		);

		$this->set(compact('rows_for_target'));

		$salesTypes = array(
			'1'             => 'Pharma Type',
			'0'             => 'NON Pharma Type',
		);

		$this->set(compact('salesTypes'));

		$stockistRetailers = array(
			'1'             => 'Stockist',
			'2'             => 'Retailers',
		);

		$this->set(compact('stockistRetailers'));

		//for columns
		$columns = array(
			'product'       => 'By Product',
			'brand'         => 'By Brand',
			'category'      => 'By Category',
			'outlet_type'   => 'By Outlet Type',
			'national'      => 'By National',
			'product_virtual'       => 'By Product With Virtual',
		);
		$this->set(compact('columns'));

		//for target columns
		$columns_for_target = array(
			'product'       => 'By Product',
			'brand'         => 'By Brand',
			'category'      => 'By Category',
			'national'      => 'By National',
		);
		$this->set(compact('columns_for_target'));

		//for indicator
		$indicators = array(
			'volume'        => 'Volume',
			'value'         => 'Value',
			'oc'            => 'OC',
			'ec'            => 'EC',
			'cyp'           => 'CYP',
			'bonus'         =>  'Bonus',
			'discount_value'         =>  'Discount'
		);
		$this->set(compact('indicators'));


		//for target indicator
		$indicators_fot_target = array(
			'volume'        => 'Volume',
			'value'         => 'Value'
		);
		$this->set(compact('indicators_fot_target'));

		//for product type
		$sql = "SELECT * FROM product_sources";
		$sources_datas = $this->Product->query($sql);
		$product_types = array();
		foreach ($sources_datas as $sources_data) {
			$product_types[$sources_data[0]['name']] = $sources_data[0]['name'];
		}
		/*$product_types = array(
			'smcel'         => 'SMCEL',
			'smc'           => 'SMC',
		);*/
		$this->set(compact('product_types'));

		//Location Types
		$locationTypes = $this->Outlet->Market->LocationType->find('list');
		$this->set(compact('locationTypes'));

		//products
		$conditions = array(
			'NOT' => array('Product.product_category_id' => 32),
			'is_active' => 1,
			'Product.product_type_id' => 1
		);
		//if($product_type)
		$product_type = isset($this->request->data['SalesAnalysisReports']['product_type']) != '' ? $this->request->data['SalesAnalysisReports']['product_type'] : '';
		if ($product_type) {
			$conditions['source'] = $product_type;
		}

		$conditions['is_virtual'] = 0;

		$product_list = $this->Product->find('list', array(
			'conditions' => $conditions,
			'order' =>  array('order' => 'asc')
		));
		$this->set(compact('product_list'));


		unset($conditions['is_virtual']);

		$product_list_virtual = $this->Product->find('list', array(
			'conditions' => $conditions,
			'order' =>  array('order' => 'asc')
		));
		$this->set(compact('product_list_virtual'));

		//divisions
		$divisions = $this->Division->find('list', array(
			//'conditions'=>array('NOT' => array('Product.product_category_id'=>32), 'is_active' => 1),
			'order' =>  array('name' => 'asc')
		));
		$this->set(compact('divisions'));


		//for program sales
		$program_sales = array(
			'1'             => 'GSP',
			'2'             => 'BSP',
			'3'             => 'Pink Star',
			'6'             => 'Notundin',
			'4'             => 'Stockist For Injectable',
			'5'             => 'NGO For Injectable',
		);
		$this->set(compact('program_sales'));
		//for outlet group sales
		$outlet_group_sales = array(
			'29'             => 'Three star business'
		);
		$this->set(compact('outlet_group_sales'));

		//product_measurement
		$product_measurement = $this->Product->find('list', array(
			//'conditions'=> $pro_conditions,
			'fields' => array('Product.id', 'Product.sales_measurement_unit_id'),
			'order' =>  array('order' => 'asc'),
			'recursive' => -1
		));
		$this->set(compact('product_measurement'));


		$office_parent_id = $this->UserAuth->getOfficeParentId();

		$this->set(compact('office_parent_id'));

		if ($office_parent_id == 0) {
			$office_conditions = array('Office.office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));

			$office_id = 0;
		} elseif ($office_parent_id == 14) {
			$region_office_id = $this->UserAuth->getOfficeId();
			$region_offices = $this->Office->find('list', array(
				'conditions' => array('Office.office_type_id' => 3, 'Office.id' => $region_office_id),
				'order' => array('office_name' => 'asc')
			));

			$office_conditions = array('Office.parent_office_id' => $region_office_id);

			$office_id = 0;

			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'office_type_id'    => 2,
					'parent_office_id'  => $region_office_id,

					"NOT" => array("id" => array(30, 31, 37))
				),
				'order' => array('order' => 'asc')
			));
			if (isset($this->request->data['SalesAnalysisReports']['office_id'])) {
				$office_id = $this->request->data['SalesAnalysisReports']['office_id'];
			} else {
				$office_id = array_keys($offices);
			}
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
			$office_id = $this->UserAuth->getOfficeId();
		}

		$this->set(compact('office_id'));

		$report_esales_setting_id = array();

		$territories = array();

		if ($office_id || $this->request->is('post') || $this->request->is('put')) {
			//pr($this->request->data);

			if (!$office_parent_id) {
				$office_id = isset($this->request->data['SalesAnalysisReports']['office_id']) != '' ? $this->request->data['SalesAnalysisReports']['office_id'] : $office_id;
			}

			//territory list
			/*$territories = $this->Territory->find('list', array(
				'conditions' => array('Territory.office_id' => $office_id),
				'order' => array('Territory.name' => 'asc')
				));
			*/
			$territory_list = $this->Territory->find('all', array(
				'conditions' => array('Territory.office_id' => $office_id, 'User.user_group_id' => array(4, 1008)),
				'joins' => array(
					array(
						'alias' => 'User',
						'table' => 'users',
						'type' => 'INNER',
						'conditions' => 'SalesPerson.id = User.sales_person_id'
					)
				),
				'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => 0
			));



			foreach ($territory_list as $key => $value) {
				//$territories[$value['Territory']['id']] = $value['SalesPerson']['name'].' ('.$value['Territory']['name'].')';
				$territories[$value['Territory']['id']] = $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')';
			}
			//pr($territories);
			//exit;
			$this->set(compact('territories'));

			//Start so list
			$sos = array();
			if ($office_id) {
				@$date_from = date('Y-m-d', strtotime($this->request->data['SalesAnalysisReports']['date_from']));
				@$date_to = date('Y-m-d', strtotime($this->request->data['SalesAnalysisReports']['date_to']));

				/*$conditions = array('User.user_group_id' => 4, 'User.active' => 1);
				$conditions['SalesPerson.office_id']= $office_id;

				$so_list_r = $this->SalesPerson->find('list', array(
					'conditions' => $conditions,
					'order'=>  array('SalesPerson.name'=>'asc'),
					'recursive'=> 0
				));*/

				$so_list_r = $this->SalesPerson->find('all', array(
					'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
					'conditions' => array(
						'SalesPerson.office_id' => $office_id,
						'SalesPerson.territory_id >' => 0,

						'User.user_group_id' => array(4, 1008),
					),
					'recursive' => 0
				));

				foreach ($so_list_r as $key => $value) {
					$so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
				}
				//add old so from territory_assign_histories
				if ($office_id) {
					$conditions = array('Territory.office_id' => $office_id, 'TerritoryAssignHistory.assign_type' => 2);

					if ($date_from && $date_to) {
						// $conditions['TerritoryAssignHistory.date BETWEEN ? and ? '] = array($date_from, $date_to);
						$conditions['TerritoryAssignHistory.date >='] = $date_from;
					}

					//pr($conditions);
					$old_so_list = $this->TerritoryAssignHistory->find('all', array(
						'conditions' => $conditions,
						'order' =>  array('Territory.name' => 'asc'),
						'recursive' => 0
					));
					if ($old_so_list) {
						foreach ($old_so_list as $old_so) {
							//$so_list[$old_so['TerritoryAssignHistory']['so_id']] = $old_so['SalesPerson']['name'];
							$so_list[$old_so['SalesPerson']['id']] = $old_so['SalesPerson']['name'] . ' (' . $old_so['Territory']['name'] . ')';
						}
					}
				}
				$sos = $so_list;
			}
			$this->set(compact('sos'));
			//End so list

			//district list
			$division_id = isset($this->request->data['SalesAnalysisReports']['division_id']) != '' ? $this->request->data['SalesAnalysisReports']['division_id'] : 0;
			$districts = $this->District->find('list', array(
				'conditions' => array('District.division_id' => $division_id),
				'order' => array('District.name' => 'asc')
			));
			$this->set(compact('districts'));



			//thana list
			$district_id = isset($this->request->data['SalesAnalysisReports']['district_id']) != '' ? $this->request->data['SalesAnalysisReports']['district_id'] : 0;
			$thanas = $this->Thana->find('list', array(
				'conditions' => array('Thana.district_id' => $district_id),
				'order' => array('Thana.name' => 'asc')
			));
			$this->set(compact('thanas'));
		}



		$outlet_type = 1;
		$ranks_2_conditions = array('type' => $outlet_type);
		$region_offices_2_conditions = array('Office.office_type_id' => 3);


		//echo $office_id;


		if ($this->request->is('post')) {
			/* pr($this->request->data);
			exit; */
			@$unit_type = $this->request->data['SalesAnalysisReports']['unit_type'];
			//$this->set(compact('unit_type'));
			$unit_type_text = ($unit_type == 2) ? 'Base' : 'Sales';
			$this->set(compact('unit_type_text'));

			if ($this->request->data['SalesAnalysisReports']['target']) {
				if ($this->request->data['SalesAnalysisReports']['indicators_fot_target']) {
					$request_data = $this->request->data;
					$date_from = date('Y-m-d', strtotime($request_data['SalesAnalysisReports']['date_from']));
					$date_to = date('Y-m-d', strtotime($request_data['SalesAnalysisReports']['date_to']));
					$this->set(compact('date_from', 'date_to', 'request_data'));
					if (!$office_id && $request_data['SalesAnalysisReports']['office_id']) {
						$office_id = $request_data['SalesAnalysisReports']['office_id'];
					}
					$territory_id = (isset($request_data['SalesAnalysisReports']['territory_id'])) ? $request_data['SalesAnalysisReports']['territory_id'] : 0;

					$row_type_for_target = $request_data['SalesAnalysisReports']['target_rows_type'];
					$rows_for_target = $request_data['SalesAnalysisReports']['rows_for_target'];
					$columns_for_target = $request_data['SalesAnalysisReports']['columns_for_target'];
					$stockist_retailer = $request_data['SalesAnalysisReports']['stockist_retailer'];

					$columns_list = array();

					if ($request_data['SalesAnalysisReports']['columns_for_target'] == 'product') {
						//for products list
						$conditions = array('NOT' => array('Product.product_category_id' => 32), 'Product.product_type_id' => 1, 'is_active' => 1);
						if ($request_data['SalesAnalysisReports']['product_id']) {
							$conditions['id'] = $request_data['SalesAnalysisReports']['product_id'];
						}
						$product_type = isset($this->request->data['SalesAnalysisReports']['product_type']) != '' ? $this->request->data['SalesAnalysisReports']['product_type'] : '';

						$product_list = $this->Product->find('list', array(

							'conditions' => $conditions,
							'order' =>  array('order' => 'asc')
						));
						$columns_list = $product_list;
					} elseif ($request_data['SalesAnalysisReports']['columns_for_target'] == 'brand') {
						//for products list
						$columns_list = $brands2;
					} elseif ($request_data['SalesAnalysisReports']['columns_for_target'] == 'category') {
						$columns_list = $category_list2;
					} else {
						$columns_list = array('national' => 'National');
					}
					$this->set(compact('columns_list'));

					//for rows
					$rows_list = array();

					if ($request_data['SalesAnalysisReports']['rows_for_target'] == 'territory') {
						$conditions = array();
						if ($office_id) {
							$conditions['Territory.office_id'] = $office_id;
						}
						if ($territory_id) {
							$conditions['Territory.id'] = $territory_id;
						}

						$conditions['Territory.name not LIKE'] = '%corporate%';
						$territory = $this->Territory->find('all', array(
							'conditions' => $conditions,
							'order' => array('Territory.name' => 'asc'),
							'recursive' => 0
						));
						$territory_list = array();

						foreach ($territory as $key => $value) {
							$territory_list[$value['Territory']['id']] = array(
								'territory_name' => $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')',
								'office_name' => $value['Office']['office_name']
							);
						}
						// pr($territory_list);
						// exit;

						$rows_list = $territory_list;
					} elseif ($request_data['SalesAnalysisReports']['rows_for_target'] == 'area') {
						$conditions = array('Office.office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
						if ($office_id) {
							$conditions['Office.id'] = $office_id;
						}
						$area_list = $this->Office->find('list', array(
							'conditions' => $conditions,
							'order' => array('order' => 'asc')
						));

						$rows_list = $area_list;
					} elseif ($request_data['SalesAnalysisReports']['rows_for_target'] == 'region') {
						$conditions = array('Office.office_type_id' => 3);
						if ($region_office_id) {
							$conditions['Office.id'] = $region_office_id;
						}
						$area_list = $this->Office->find('list', array(
							'conditions' => $conditions,
							'order' => array('order' => 'asc')
						));

						$rows_list = $area_list;
					} else {
						$rows_list = array('national' => 'National');
					}
					$this->set(compact('rows_list'));
					$rows_type_list = array();
					if ($row_type_for_target == 'day') {
						$output = [];
						for ($i = $date_from; $i <= $date_to; $i = date('Y-m-d', strtotime("+1 day" . $i))) {
							$output[$i] = $i;
						}
						$rows_type_list = $output;
					} else {
						//month count
						$date1 = $date_from;
						$date2 = $date_to;
						$output = [];
						$output2 = array();
						$time   = strtotime($date1);
						$last   = date('Y-m', strtotime($date2));
						do {
							$month = date('Y-m', $time);
							$total = date('t', $time);

							$output[] = [
								'month' => $month,
								'total_days' => $total,
							];

							$output2[$month] = date('M, Y', $time);

							$time = strtotime('+1 month', $time);
						} while ($month != $last);
						$rows_type_list = $output2;
					}
					$this->set(compact('rows_type_list'));

					//for column
					$col_keys = array();
					foreach ($columns_list as $col_key => $col_val) {
						array_push($col_keys, $col_key);
					}
					$row_keys = array();
					foreach ($rows_list as $row_key => $row_val) {
						array_push($row_keys, $row_key);
					}
					/*
						in this function return a 4 dimension array
						$month_wise_target['row_item_id']['col_item_id']['year_month'] = array(target_qty,target_amount)
					 */
					$month_wise_target = $this->get_monthly_target($request_data, $col_keys, $row_keys);
					/*
						in this function return a 4 dimension array
						$achivement['row_item_id']['col_item_id']['year_month/Y-m-d']=array(sales_qty,sales_amount)
					 */
					$achivement = $this->get_achievement($request_data, $col_keys, $row_keys);
					$output = $this->create_output_for_target($request_data, $month_wise_target, $achivement, $rows_list, $columns_list, $rows_type_list, $indicators_fot_target);
					$this->set(compact('month_wise_target', 'achivement', 'output'));
				} else {
					$this->Session->setFlash(__('Please select an indicators!'), 'flash/error');
				}
			} else {
				if ($this->request->data['SalesAnalysisReports']['indicators']) {
					$request_data = $this->request->data;
					$date_from = $request_data['SalesAnalysisReports']['date_from'];
					$date_to = $request_data['SalesAnalysisReports']['date_to'];
					$this->set(compact('date_from', 'date_to', 'request_data'));

					if (!$office_id && $request_data['SalesAnalysisReports']['date_from']) {
						$office_id = $request_data['SalesAnalysisReports']['office_id'];
					}
					$territory_id = (isset($request_data['SalesAnalysisReports']['territory_id'])) ? $request_data['SalesAnalysisReports']['territory_id'] : 0;


					$stockist_retailer = $request_data['SalesAnalysisReports']['stockist_retailer'];
					//for columns
					$columns_list = array();

					if ($request_data['SalesAnalysisReports']['columns'] == 'product') {
						//for products list
						$conditions = array('NOT' => array('Product.product_category_id' => 32), 'Product.product_type_id' => 1, 'is_active' => 1, 'Product.is_virtual' => 0);
						if ($request_data['SalesAnalysisReports']['product_id']) {
							$conditions['id'] = $request_data['SalesAnalysisReports']['product_id'];
						}
						$product_type = isset($this->request->data['SalesAnalysisReports']['product_type']) != '' ? $this->request->data['SalesAnalysisReports']['product_type'] : '';
						if ($product_type) {
							$conditions['source'] = $product_type;
						}
						$product_list = $this->Product->find('list', array(
							'conditions' => $conditions,
							//'fields'=>array("Product.id", "Product.name", "Product.product_code"),
							//'recursive'=>-1
							'order' =>  array(/* 'source' => 'desc', */'order' => 'asc')
						));
						
						$product_code_list = $this->Product->find('list', array(
							'conditions' => $conditions,
							'fields'=>array("Product.id", "Product.product_code"),
							'order' =>  array(/* 'source' => 'desc', */'order' => 'asc')
						));
						
						$this->set(compact('product_code_list'));
						
						$columns_list = $product_list;
						
					} elseif ($request_data['SalesAnalysisReports']['columns'] == 'product_virtual') {
						//for products list
						$conditions = array('NOT' => array('Product.product_category_id' => 32), 'Product.product_type_id' => 1, 'is_active' => 1);
						if ($request_data['SalesAnalysisReports']['virtual_product_id']) {
							$conditions['id'] = $request_data['SalesAnalysisReports']['virtual_product_id'];
						}
						$product_type = isset($this->request->data['SalesAnalysisReports']['product_type']) != '' ? $this->request->data['SalesAnalysisReports']['product_type'] : '';
						if ($product_type) {
							$conditions['source'] = $product_type;
						}
						$product_list = $this->Product->find('list', array(
							'conditions' => $conditions,
							'order' =>  array(/* 'source' => 'desc', */'order' => 'asc')
						));

						$columns_list = $product_list;
					} elseif ($request_data['SalesAnalysisReports']['columns'] == 'brand') {
						//for products list
						/*$conditions = array('NOT' => array('Product.product_category_id'=>32), 'Product.product_type_id' => 1, 'is_active' => 1);
						if($request_data['SalesAnalysisReports']['brand_id'])$conditions['brand_id'] = $request_data['SalesAnalysisReports']['brand_id'];

						$product_type = isset($this->request->data['SalesAnalysisReports']['product_type']) != '' ? $this->request->data['SalesAnalysisReports']['product_type'] : '';
						if($product_type)$conditions['source'] = $product_type;

						$product_list = $this->Product->find('list', array(
							'conditions'=> $conditions,
							'order'=>  array('order'=>'asc')
						));

						$columns_list = $product_list;*/


						//add new
						/*$b_con = array(
							'NOT' => array('Brand.id'=>44)
						);
						if($request_data['SalesAnalysisReports']['brand_id'])$b_con['Brand.id'] = $request_data['SalesAnalysisReports']['brand_id'];
						$brand_list = $this->Brand->find('list', array(
								'conditions'=> $b_con,
								'order' => array('id' => 'asc')
							));
						$columns_list = $brand_list;*/
						$columns_list = $brands2;
					} elseif ($request_data['SalesAnalysisReports']['columns'] == 'category') {
						/*$category_list = $this->ProductCategory->find('list', array(
							'conditions'=>array('NOT' => array('ProductCategory.id'=>32)),
							'order'=>  array('name'=>'asc')
						));
						$columns_list = $category_list;*/

						//for products list
						/*$conditions = array('NOT' => array('Product.product_category_id'=>32), 'Product.product_type_id' => 1, 'is_active' => 1);
						if($request_data['SalesAnalysisReports']['product_category_id'])$conditions['product_category_id'] = $request_data['SalesAnalysisReports']['product_category_id'];
						$product_type = isset($this->request->data['SalesAnalysisReports']['product_type']) != '' ? $this->request->data['SalesAnalysisReports']['product_type'] : '';
						if($product_type)$conditions['source'] = $product_type;
						$product_list = $this->Product->find('list', array(
							'conditions'=> $conditions,
							'order'=>  array('order'=>'asc')
						));
						$columns_list = $product_list;*/

						/*$c_con = array('NOT' => array('ProductCategory.id'=>32));

						if($request_data['SalesAnalysisReports']['product_category_id'])$c_con['ProductCategory.id'] = $request_data['SalesAnalysisReports']['product_category_id'];
						$category_list = $this->ProductCategory->find('list', array(
							'conditions'=>$c_con,
							'order' => array('id' => 'asc')
						));*/
						$columns_list = $category_list2;
					} elseif ($request_data['SalesAnalysisReports']['columns'] == 'outlet_type') {
						$columns_list = $outlet_categories2;
					} else {
						$columns_list = array('National');
					}
					$this->set(compact('columns_list'));
					//end for columns



					//for rows
					$rows_list = array();
					if ($request_data['SalesAnalysisReports']['rows'] == 'so') {
						$date_from = date('Y-m-d', strtotime($request_data['SalesAnalysisReports']['date_from']));
						$date_to = date('Y-m-d', strtotime($request_data['SalesAnalysisReports']['date_to']));

						//$conditions = array('SalesPerson.territory_id >' => 0, 'User.user_group_id' => 4, 'User.active' => 1);
						$conditions = array('User.user_group_id' => array(4, 1008), 'User.active' => 1);
						if ($office_id) {
							$conditions['SalesPerson.office_id'] = $office_id;
						}
						if ($territory_id) {
							$conditions['SalesPerson.territory_id'] = $territory_id;
						}
						if (@$request_data['SalesAnalysisReports']['so_id']) {
							$conditions['SalesPerson.id'] = $request_data['SalesAnalysisReports']['so_id'];
						}




						$so_list = array();

						/*$so_list = $this->SalesPerson->find('list', array(
							'conditions' => $conditions,
							'order'=>  array('SalesPerson.name'=>'asc'),
							'recursive'=> 0
						));*/

						$so_list_r = $this->SalesPerson->find('all', array(
							'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
							'conditions' => $conditions,
							'order' =>  array('SalesPerson.name' => 'asc'),
							'recursive' => 0
						));
						foreach ($so_list_r as $list_r) {
							$so_list[$list_r['SalesPerson']['id']] = $list_r['SalesPerson']['name'] . ' (' . $list_r['Territory']['name'] . ')';
						}



						//add old so from territory_assign_histories
						if ($office_id) {
							$conditions = array('Territory.office_id' => $office_id, 'TerritoryAssignHistory.assign_type' => 2);
							// $conditions['TerritoryAssignHistory.date BETWEEN ? and ? '] = array($date_from, $date_to);
							$conditions['TerritoryAssignHistory.date >='] = $date_from;
							if ($request_data['SalesAnalysisReports']['so_id']) {
								$conditions['TerritoryAssignHistory.so_id'] = $request_data['SalesAnalysisReports']['so_id'];
							}
							//pr($conditions);
							$old_so_list = $this->TerritoryAssignHistory->find('all', array(
								'conditions' => $conditions,
								'order' =>  array('Territory.name' => 'asc'),
								'recursive' => 0
							));
							/*pr($so_list);
							echo $this->TerritoryAssignHistory->getLastQuery;
							pr($old_so_list);
							exit;*/
							if ($old_so_list) {
								foreach ($old_so_list as $old_so) {
									$so_list[$old_so['TerritoryAssignHistory']['so_id']] = $old_so['SalesPerson']['name'];
								}
							}
						}
						// pr($so_list);
						// exit;

						$rows_list = $so_list;
					} elseif ($request_data['SalesAnalysisReports']['rows'] == 'territory') {
						$conditions = array();
						if ($office_id) {
							$conditions['Territory.office_id'] = $office_id;
						}
						if ($territory_id) {
							$conditions['Territory.id'] = $territory_id;
						}

						$territory = $this->Territory->find('all', array(
							'conditions' => $conditions,
							'order' => array('Territory.name' => 'asc'),
							'recursive' => 0
						));

						$territory_list = array();

						foreach ($territory as $key => $value) {
							$territory_list[$value['Territory']['id']] = $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')';
						}
						//pr($territory_list);
						//exit;

						$rows_list = $territory_list;
					} elseif ($request_data['SalesAnalysisReports']['rows'] == 'area') {
						$conditions = array('Office.office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
						if ($office_id) {
							$conditions['Office.id'] = $office_id;
						}
						$area_list = $this->Office->find('list', array(
							'conditions' => $conditions,
							'order' => array('order' => 'asc')
							//'order' => array('office_name' => 'asc')
						));

						$rows_list = $area_list;
					} elseif ($request_data['SalesAnalysisReports']['rows'] == 'region') {
						$conditions = array('Office.office_type_id' => 3);
						if ($region_office_id) {
							$conditions['Office.id'] = $region_office_id;
						}
						$area_list = $this->Office->find('list', array(
							'conditions' => $conditions,
							'order' => array('order' => 'asc')
						));

						$rows_list = $area_list;
					} elseif ($request_data['SalesAnalysisReports']['rows'] == 'month') {
						$d1 = new DateTime($date_from);
						$d2 = new DateTime($date_to);
						$months = 0;

						$d1->add(new \DateInterval('P1M'));
						while ($d1 <= $d2) {
							$months++;
							$d1->add(new \DateInterval('P1M'));
						}

						//print_r($months);


						//month count
						$date1 = $date_from;
						$date2 = $date_to;
						$output = [];
						$output2 = array();
						$time   = strtotime($date1);
						$last   = date('Y-m', strtotime($date2));
						do {
							$month = date('Y-m', $time);
							$total = date('t', $time);

							$output[] = [
								'month' => $month,
								'total_days' => $total,
							];

							$output2[$month] = date('M, Y', $time);

							$time = strtotime('+1 month', $time);
						} while ($month != $last);

						//$month_list = $output;

						//pr($output2);
						//exit;

						$rows_list = $output2;
					} elseif ($request_data['SalesAnalysisReports']['rows'] == 'division') {
						if ($request_data['SalesAnalysisReports']['division_id']) {
							$conditions = array('Division.id' => $request_data['SalesAnalysisReports']['division_id']);
						} else {
							$conditions = array();
						}

						if ($request_data['SalesAnalysisReports']['territory_id']) {
							$conditions['Territory.id'] = $request_data['SalesAnalysisReports']['territory_id'];
						}

						if ($office_id) {
							$conditions['Territory.office_id'] = $office_id;
						}

						//pr($conditions);

						$division_list = $this->Division->find('list', array(
							'conditions' => $conditions,
							'joins' => array(
								array(
									'alias' => 'District',
									'table' => 'districts',
									'type' => 'INNER',
									'conditions' => 'Division.id = District.division_id'
								),
								array(
									'alias' => 'Thana',
									'table' => 'thanas',
									'type' => 'INNER',
									'conditions' => 'District.id = Thana.district_id'
								),
								array(
									'alias' => 'ThanaTerritory',
									'table' => 'thana_territories',
									'type' => 'INNER',
									'conditions' => 'Thana.id = ThanaTerritory.thana_id'
								),
								array(
									'alias' => 'Territory',
									'table' => 'territories',
									'type' => 'INNER',
									'conditions' => 'ThanaTerritory.territory_id = Territory.id'
								)
							),
							'order' => array('Division.name' => 'asc'),
							'recursive' => -1
						));

						$rows_list = $division_list;
					} elseif ($request_data['SalesAnalysisReports']['rows'] == 'district') {
						$conditions = array();

						if ($request_data['SalesAnalysisReports']['division_id']) {
							$conditions['District.division_id'] = $request_data['SalesAnalysisReports']['division_id'];
						}

						if ($request_data['SalesAnalysisReports']['district_id']) {
							$conditions['District.id'] = $request_data['SalesAnalysisReports']['district_id'];
						}

						if ($request_data['SalesAnalysisReports']['territory_id']) {
							$conditions['Territory.id'] = $request_data['SalesAnalysisReports']['territory_id'];
						}

						if ($office_id) {
							$conditions['Territory.office_id'] = $office_id;
						}

						//echo $office_id;

						$list = $this->District->find('list', array(
							'conditions' => $conditions,

							'joins' => array(
								array(
									'alias' => 'Thana',
									'table' => 'thanas',
									'type' => 'INNER',
									'conditions' => 'District.id = Thana.district_id'
								),
								array(
									'alias' => 'ThanaTerritory',
									'table' => 'thana_territories',
									'type' => 'INNER',
									'conditions' => 'Thana.id = ThanaTerritory.thana_id'
								),
								array(
									'alias' => 'Territory',
									'table' => 'territories',
									'type' => 'INNER',
									'conditions' => 'ThanaTerritory.territory_id = Territory.id'
								),
								array(
									'alias' => 'Division',
									'table' => 'divisions',
									'type' => 'INNER',
									'conditions' => 'District.division_id = Division.id'
								)
							),
							//'fields' => 'District.*, Territory.*',
							//'fields' => array('District.*', 'Territory.office_id'),
							'order' => array('District.name' => 'asc'),
							'recursive' => -1
						));

						$rows_list = $list;
					} elseif ($request_data['SalesAnalysisReports']['rows'] == 'thana') {
						$conditions = array();

						if ($request_data['SalesAnalysisReports']['division_id']) {
							$conditions['District.division_id'] = $request_data['SalesAnalysisReports']['division_id'];
						}

						if ($request_data['SalesAnalysisReports']['district_id']) {
							$conditions['District.id'] = $request_data['SalesAnalysisReports']['district_id'];
						}

						if ($request_data['SalesAnalysisReports']['thana_id']) {
							$conditions['Thana.id'] = $request_data['SalesAnalysisReports']['thana_id'];
						}

						if ($request_data['SalesAnalysisReports']['territory_id']) {
							$conditions['Territory.id'] = $request_data['SalesAnalysisReports']['territory_id'];
						}

						if ($office_id) {
							$conditions['Territory.office_id'] = $office_id;
						}

						//echo $office_id;

						$list = $this->Thana->find('list', array(
							'conditions' => $conditions,

							'joins' => array(
								array(
									'alias' => 'District',
									'table' => 'districts',
									'type' => 'INNER',
									'conditions' => 'Thana.district_id = District.id'
								),
								array(
									'alias' => 'ThanaTerritory',
									'table' => 'thana_territories',
									'type' => 'INNER',
									'conditions' => 'Thana.id = ThanaTerritory.thana_id'
								),
								array(
									'alias' => 'Territory',
									'table' => 'territories',
									'type' => 'INNER',
									'conditions' => 'ThanaTerritory.territory_id = Territory.id'
								),
								array(
									'alias' => 'Division',
									'table' => 'divisions',
									'type' => 'INNER',
									'conditions' => 'District.division_id = Division.id'
								),

							),
							'order' => array('Thana.name' => 'asc', 'District.name' => 'asc', 'Division.name' => 'asc'),
							'recursive' => -1
						));

						$rows_list = $list;
					} elseif ($request_data['SalesAnalysisReports']['rows'] == 'ProgramOfficer') { //program officer part
						$conditions = array('User.user_group_id' => array(1016), 'User.active' => 1);
						if ($office_id) {
							$conditions['SalesPerson.office_id'] = $office_id;
						}
						/* if ($territory_id) {
							$conditions['SalesPerson.territory_id'] = $territory_id;
						} */
						if (@$request_data['SalesAnalysisReports']['so_id']) {
							$conditions['SalesPerson.id'] = $request_data['SalesAnalysisReports']['so_id'];
						}




						$so_list = array();

						$so_list_r = $this->SalesPerson->find('all', array(
							'fields' => array('User.id', 'SalesPerson.name', 'Territory.name'),
							'conditions' => $conditions,
							'order' =>  array('SalesPerson.name' => 'asc'),
							'recursive' => 0
						));
						foreach ($so_list_r as $list_r) {
							$so_list[$list_r['User']['id']] = $list_r['SalesPerson']['name'] . ' (' . $list_r['Territory']['name'] . ')';
						}

						$rows_list = $so_list;
					} else {
						$rows_list = array('National');
					}

					$this->set(compact('rows_list'));
					// pr($rows_list);
					// exit;
					//end for rows



					/*START FOR RESULT QUERY*/
					$date_from = date('Y-m-d', strtotime($request_data['SalesAnalysisReports']['date_from']));
					$date_to = date('Y-m-d', strtotime($request_data['SalesAnalysisReports']['date_to']));
					$rows       = $request_data['SalesAnalysisReports']['rows'];
					$columns    = $request_data['SalesAnalysisReports']['columns'];

					//program sales
					$p_outlet_ids = array();
					$where = '';
					if ($request_data['SalesAnalysisReports']['program_type_id']) {
						$program_type_ids = $request_data['SalesAnalysisReports']['program_type_id'];
						$all_p_type_ids = join(",", $program_type_ids);
						// $where = " where programs.program_type_id IN ($all_p_type_ids) "; //comment by naser in 06 Feb 2019
						$where = " where programs.program_type_id IN ($all_p_type_ids) AND programs.assigned_date <= '$date_to' AND (programs.deassigned_date is null OR programs.deassigned_date >'$date_from')";

						if ($office_id) {
							$where .= "AND programs.officer_id=$office_id";
						}

						// Added by naser due to jitu vai request.
						/*$p_conditions = array(
								'Program.program_type_id' => $program_type_ids,
								//'Program.status' => 1,
								//'Program.assigned_date BETWEEN ? and ? ' => array($date_from, $date_to),
								//'Program.assigned_date <=' => $date_to,
							);

						if($request_data['SalesAnalysisReports']['division_id'])$p_conditions['District.division_id'] = $request_data['SalesAnalysisReports']['division_id'];

						if($request_data['SalesAnalysisReports']['district_id'])$p_conditions['District.id'] = $request_data['SalesAnalysisReports']['district_id'];

						if($request_data['SalesAnalysisReports']['thana_id'])$p_conditions['Thana.id'] = $request_data['SalesAnalysisReports']['thana_id'];

						if($request_data['SalesAnalysisReports']['office_id']){
							$p_conditions['Program.officer_id'] = $request_data['SalesAnalysisReports']['office_id'];
						}

						if($request_data['SalesAnalysisReports']['territory_id']){
							$p_conditions['Program.territory_id'] = $request_data['SalesAnalysisReports']['territory_id'];
						}

						//pr($p_conditions);

						$program_list = $this->Program->find('all', array(
							'conditions' => $p_conditions,
							'joins' => array(
								array(
									'alias' => 'Outlet',
									'table' => 'outlets',
									'type' => 'INNER',
									'conditions' => 'Program.outlet_id = Outlet.id'
								),
								array(
									'alias' => 'Market',
									'table' => 'markets',
									'type' => 'INNER',
									'conditions' => 'Outlet.market_id = Market.id'
								),
								array(
									'alias' => 'Thana',
									'table' => 'thanas',
									'type' => 'INNER',
									'conditions' => 'Market.thana_id = Thana.id'
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
								),

							),
							'fields' => array('Program.outlet_id'),
							'order'=>  array('Program.id'=>'desc'),
							'recursive'=>-1
						));


						foreach($program_list as $p_val){
							array_push($p_outlet_ids, $p_val['Program']['outlet_id']);
						}*/



						//pr($p_outlet_ids);
						//exit;

						//Notundin Program
						if (in_array(6, $program_type_ids)) {
							$where .= " union 
										select o.id as outlet_id from notundin_programs np
										inner join outlets o on o.institute_id=np.institute_id";

							/*$n_con = array(
							//'NotundinProgram.institute_id'    => $institute_id,
							//'NotundinProgram.status'          => 1
							);

							if($request_data['SalesAnalysisReports']['division_id'])$n_con['District.division_id'] = $request_data['SalesAnalysisReports']['division_id'];

							if($request_data['SalesAnalysisReports']['district_id'])$n_con['District.id'] = $request_data['SalesAnalysisReports']['district_id'];

							if($request_data['SalesAnalysisReports']['thana_id'])$n_con['Thana.id'] = $request_data['SalesAnalysisReports']['thana_id'];

							if($request_data['SalesAnalysisReports']['territory_id'])$n_con['Territory.id'] = $request_data['SalesAnalysisReports']['territory_id'];

							if($office_id)$n_con['Territory.office_id'] = $office_id;

							//pr($n_con);

							$n_program_list = $this->NotundinProgram->find('all',array(
								'conditions' => $n_con,
								'joins' => array(
									array(
										'alias' => 'Outlet',
										'table' => 'outlets',
										'type' => 'INNER',
										'conditions' => 'NotundinProgram.institute_id = Outlet.institute_id'
									),
									array(
										'alias' => 'Market',
										'table' => 'markets',
										'type' => 'INNER',
										'conditions' => 'Outlet.market_id = Market.id'
									),
									array(
										'alias' => 'Thana',
										'table' => 'thanas',
										'type' => 'INNER',
										'conditions' => 'Market.thana_id = Thana.id'
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
									),
									array(
										'alias' => 'ThanaTerritory',
										'table' => 'thana_territories',
										'type' => 'INNER',
										'conditions' => 'Thana.id = ThanaTerritory.thana_id'
									),
									array(
										'alias' => 'Territory',
										'table' => 'territories',
										'type' => 'INNER',
										'conditions' => 'ThanaTerritory.territory_id = Territory.id'
									)
								),
								'fields' => array('Outlet.id'),
								//'order' => array('Thana.name'=>'ASC'),
								//'recursive' => -1
							));
							//pr($n_program_list);

							foreach($n_program_list as $n_val){
								array_push($p_outlet_ids, $n_val['Outlet']['id']);
							}*/
						}

						//pr($p_outlet_ids);

						//exit;
					}



					//end program sales

					//echo count($rows_list);
					//pr($rows_list);
					//exit;

					$q_results = array();
					$q_results2 = array();

					//for column
					$col_keys = array();

					foreach ($columns_list as $col_key => $col_val) {
						array_push($col_keys, $col_key);
					}

					$row_keys = array();
					foreach ($rows_list as $row_key => $row_val) {
						array_push($row_keys, $row_key);
					}
					//pr($row_keys);
					//exit;

					if ($rows == 'month' || $rows == 'national') {
						//for month row
						if ($rows == 'month') {
							foreach ($rows_list as $row_key => $row_val) {
								//foreach($columns_list as $col_key => $col_val){
								$conditions = array(
									//'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
									'Memo.gross_value >=' => 0,
									'Memo.status !=' => 0,
								);

								if ($request_data['SalesAnalysisReports']['sales_type_id'] != '') {
									$conditions['Outlet.is_pharma_type'] = $request_data['SalesAnalysisReports']['sales_type_id'];
								}

								if ($request_data['SalesAnalysisReports']['location_type_id']) {
									$conditions['Market.location_type_id'] = $request_data['SalesAnalysisReports']['location_type_id'];
								}


								//add new
								//pr($request_data);
								if ($request_data['SalesAnalysisReports']['product_type']) {
									$conditions['Product.source'] = $request_data['SalesAnalysisReports']['product_type'];
								}

								if (@$request_data['SalesAnalysisReports']['so_id']) {
									$conditions['Memo.sales_person_id'] = $request_data['SalesAnalysisReports']['so_id'];
								}

								if ($request_data['SalesAnalysisReports']['territory_id']) {
									$conditions['Memo.territory_id'] = $request_data['SalesAnalysisReports']['territory_id'];
								}

								if ($request_data['SalesAnalysisReports']['product_id']) {
									$conditions['MemoDetail.product_id'] = $request_data['SalesAnalysisReports']['product_id'];
								}
								if ($request_data['SalesAnalysisReports']['virtual_product_id']) {
									$conditions['OR'] = array(
										'MemoDetail.product_id' => $request_data['SalesAnalysisReports']['virtual_product_id'],
										'MemoDetail.virtual_product_id' => $request_data['SalesAnalysisReports']['virtual_product_id']
									);
								}
								//end add new


								//for rows
								if ($rows == 'month') {
									$a_date =  $row_key;
									$month_first_day = date("Y-m-d", strtotime($a_date));
									$month_last_day = date("Y-m-t", strtotime($a_date));
									$conditions['Memo.memo_date BETWEEN ? and ? '] = array($month_first_day, $month_last_day);
								}


								if ($request_data['SalesAnalysisReports']['division_id']) {
									$conditions['Division.id'] = $request_data['SalesAnalysisReports']['division_id'];
								}

								//if($rows=='district')$conditions['District.id'] = $row_key;

								if ($request_data['SalesAnalysisReports']['district_id']) {
									$conditions['District.id'] = $request_data['SalesAnalysisReports']['district_id'];
								}

								if ($request_data['SalesAnalysisReports']['thana_id']) {
									$conditions['Thana.id'] = $request_data['SalesAnalysisReports']['thana_id'];
								}


								//for columns
								if ($columns == 'product') {
									$conditions['MemoDetail.product_id'] = $col_keys;
								}
								if ($columns == 'product_virtual') {
									$conditions['OR'] = array(
										'MemoDetail.product_id' => $col_keys,
										'MemoDetail.virtual_product_id' => $col_keys,
									);
								}


								if ($columns == 'category') {
									$conditions['Product.product_category_id'] = $col_keys;
								}
								if ($columns != 'category' && $request_data['SalesAnalysisReports']['product_category_id']) {
									$conditions['Product.product_category_id'] = $request_data['SalesAnalysisReports']['product_category_id'];
								}

								if ($columns == 'brand') {
									$conditions['Product.brand_id'] = $col_keys;
								}
								if ($columns != 'brand' && $request_data['SalesAnalysisReports']['brand_id']) {
									$conditions['Product.brand_id'] = $request_data['SalesAnalysisReports']['brand_id'];
								}
								if ($stockist_retailer == 1) {
									$conditions['OR'] = array(
										'ProductionCombination.id is null ',
										'ProductionCombination.min_qty >' => 1
									);
								}
								if ($stockist_retailer == 2) {
									$conditions['ProductionCombination.min_qty'] = 1;
								}


								if ($columns == 'outlet_type') {
									$conditions['Outlet.category_id'] = $col_keys;
								}
								if ($columns != 'outlet_type' && $request_data['SalesAnalysisReports']['outlet_category_id']) {
									$conditions['Outlet.category_id'] = $request_data['SalesAnalysisReports']['outlet_category_id'];
								}


								//program sales
								$program_joins = array();
								if ($request_data['SalesAnalysisReports']['program_type_id']) {
									/*if($p_outlet_ids){
											$conditions['Memo.outlet_id'] = $p_outlet_ids;
										}else{
											$conditions['Memo.outlet_id'] = 0;
										}*/
									// $conditions[] = ("Memo.outlet_id in (select outlet_id from programs $where)");
									//
									$program_joins = array(
										'alias' => 'ProgramOutlet',
										'table' => "(select outlet_id from programs $where)",
										'type' => 'INNER',
										'conditions' => 'ProgramOutlet.outlet_id = Memo.outlet_id'
									);
								}
								//end program sales

								if ($office_id) {
									$conditions['Memo.office_id'] = $office_id;
								}

								// $conditions['MemoDetail.price >']=0;

								//pr($conditions);




								if ($request_data['SalesAnalysisReports']['columns'] == 'category') {
									$q_results[$row_key] = $this->Memo->find('all', array(
										'conditions' => $conditions,
										'joins' => array(
											array(
												'alias' => 'MemoDetail',
												'table' => 'memo_details',
												'type' => 'INNER',
												'conditions' => 'Memo.id = MemoDetail.memo_id'
											),
											array(
												'alias' => 'dbp',
												'table' => 'discount_bonus_policies',
												'type' => 'left',
												'conditions' => 'MemoDetail.policy_id = dbp.id'
											),

											array(
												'alias' => 'Product',
												'table' => 'products',
												'type' => 'INNER',
												'conditions' => 'MemoDetail.product_id = Product.id'
											),
											array(
												'alias' => 'ProductMeasurement',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => 'Product.id = ProductMeasurement.product_id AND 
														CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
														ELSE 
															MemoDetail.measurement_unit_id
														END =ProductMeasurement.measurement_unit_id'
											),
											array(
												'alias' => 'ProductMeasurementSales',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => '
														Product.id = ProductMeasurementSales.product_id 
														AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
											),
											array(
												'alias' => 'Territory',
												'table' => 'territories',
												'type' => 'INNER',
												'conditions' => 'Memo.territory_id = Territory.id'
											),
											array(
												'alias' => 'Outlet',
												'table' => 'outlets',
												'type' => 'INNER',
												'conditions' => 'Memo.outlet_id = Outlet.id'
											),
											array(
												'alias' => 'Market',
												'table' => 'markets',
												'type' => 'INNER',
												'conditions' => 'Memo.market_id = Market.id'
											),
											array(
												'alias' => 'Thana',
												'table' => 'thanas',
												'type' => 'INNER',
												'conditions' => 'Memo.thana_id = Thana.id'
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
											),
											array(
												'alias' => 'ProductionCombination',
												'table' => 'product_combinations_v2',
												'type' => 'LEFT',
												'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
											),
											$program_joins ? $program_joins : ''
										),

										// sum(MemoDetail.sales_qty*MemoDetail.price) AS value, this comment by naser. for smc emergency request for not showing value without discount . comment date 2023-june-15
										'fields' => array(
											'ROUND(SUM(
															(ROUND(
																	(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
																	(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
																,0)) / 
																(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
																
														),2,1) AS volume,
											ROUND(SUM(
													(ROUND(
															(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
															(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
														,0)) / 
														(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
														
												),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value, 
											sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value, 
											COUNT(DISTINCT Memo.memo_no) AS ec, 
											COUNT(DISTINCT Memo.outlet_id) as oc',
											'Product.product_category_id'
										),
										'group' => array('Product.product_category_id'),

										'order' => array('Product.product_category_id asc'),

										'recursive' => -1
									));

									$q_results2[$row_key] = $this->Memo->find('all', array(
										'conditions' => $conditions,
										'joins' => array(
											array(
												'alias' => 'MemoDetail',
												'table' => 'memo_details',
												'type' => 'INNER',
												'conditions' => 'Memo.id = MemoDetail.memo_id'
											),
											array(
												'alias' => 'dbp',
												'table' => 'discount_bonus_policies',
												'type' => 'left',
												'conditions' => 'MemoDetail.policy_id = dbp.id'
											),
											array(
												'alias' => 'Product',
												'table' => 'products',
												'type' => 'INNER',
												'conditions' => 'MemoDetail.product_id = Product.id'
											),
											array(
												'alias' => 'ProductMeasurement',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => 'Product.id = ProductMeasurement.product_id AND 
														CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
														ELSE 
															MemoDetail.measurement_unit_id
														END =ProductMeasurement.measurement_unit_id'
											),
											array(
												'alias' => 'ProductMeasurementSales',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => '
														Product.id = ProductMeasurementSales.product_id 
														AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
											),
											array(
												'alias' => 'Territory',
												'table' => 'territories',
												'type' => 'INNER',
												'conditions' => 'Memo.territory_id = Territory.id'
											),
											array(
												'alias' => 'Outlet',
												'table' => 'outlets',
												'type' => 'INNER',
												'conditions' => 'Memo.outlet_id = Outlet.id'
											),
											array(
												'alias' => 'Market',
												'table' => 'markets',
												'type' => 'INNER',
												'conditions' => 'Memo.market_id = Market.id'
											),
											array(
												'alias' => 'Thana',
												'table' => 'thanas',
												'type' => 'INNER',
												'conditions' => 'Memo.thana_id = Thana.id'
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
											),
											array(
												'alias' => 'ProductionCombination',
												'table' => 'product_combinations_v2',
												'type' => 'LEFT',
												'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
											),
											$program_joins ? $program_joins : ''
										),

										'fields' => array('ROUND(SUM(
															(ROUND(
																	(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
																	(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
																,0)) / 
																(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
														),2,1) AS volume,
													ROUND(SUM(
														(ROUND(
																(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
																(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
															,0)) / 
															(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
														),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
														sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
														COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc',  'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v', 'Product.product_category_id'),
										'group' => array('Product.product_category_id', 'MemoDetail.product_id, Product.cyp_cal, Product.cyp'),
										'order' => array('Product.product_category_id asc'),

										'recursive' => -1
									));
								} elseif ($request_data['SalesAnalysisReports']['columns'] == 'brand') {
									$q_results[$row_key] = $this->Memo->find('all', array(
										'conditions' => $conditions,
										'joins' => array(
											array(
												'alias' => 'MemoDetail',
												'table' => 'memo_details',
												'type' => 'INNER',
												'conditions' => 'Memo.id = MemoDetail.memo_id'
											),
											array(
												'alias' => 'dbp',
												'table' => 'discount_bonus_policies',
												'type' => 'left',
												'conditions' => 'MemoDetail.policy_id = dbp.id'
											),
											array(
												'alias' => 'Product',
												'table' => 'products',
												'type' => 'INNER',
												'conditions' => 'MemoDetail.product_id = Product.id'
											),
											array(
												'alias' => 'ProductMeasurement',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => 'Product.id = ProductMeasurement.product_id AND 
														CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
														ELSE 
															MemoDetail.measurement_unit_id
														END =ProductMeasurement.measurement_unit_id'
											),
											array(
												'alias' => 'ProductMeasurementSales',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => '
														Product.id = ProductMeasurementSales.product_id 
														AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
											),
											array(
												'alias' => 'Territory',
												'table' => 'territories',
												'type' => 'INNER',
												'conditions' => 'Memo.territory_id = Territory.id'
											),
											array(
												'alias' => 'Outlet',
												'table' => 'outlets',
												'type' => 'INNER',
												'conditions' => 'Memo.outlet_id = Outlet.id'
											),
											array(
												'alias' => 'Market',
												'table' => 'markets',
												'type' => 'INNER',
												'conditions' => 'Memo.market_id = Market.id'
											),
											array(
												'alias' => 'Thana',
												'table' => 'thanas',
												'type' => 'INNER',
												'conditions' => 'Memo.thana_id = Thana.id'
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
											),
											array(
												'alias' => 'ProductionCombination',
												'table' => 'product_combinations_v2',
												'type' => 'LEFT',
												'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
											),
											$program_joins ? $program_joins : ''
										),


										'fields' => array('ROUND(SUM(
															(ROUND(
																	(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
																	(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
																,0)) / 
																(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
														),2,1) AS volume,
													ROUND(SUM(
														(ROUND(
																(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
																(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
																,0)) / 
																(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
														),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
														sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
														COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc',  'Product.brand_id'),
										'group' => array('Product.brand_id'),

										'order' => array('Product.brand_id asc'),

										'recursive' => -1
									));
									//pr($q_results);
									//exit;
									$q_results2[$row_key] = $this->Memo->find('all', array(
										'conditions' => $conditions,
										'joins' => array(
											array(
												'alias' => 'MemoDetail',
												'table' => 'memo_details',
												'type' => 'INNER',
												'conditions' => 'Memo.id = MemoDetail.memo_id'
											),
											array(
												'alias' => 'dbp',
												'table' => 'discount_bonus_policies',
												'type' => 'left',
												'conditions' => 'MemoDetail.policy_id = dbp.id'
											),
											array(
												'alias' => 'Product',
												'table' => 'products',
												'type' => 'INNER',
												'conditions' => 'MemoDetail.product_id = Product.id'
											),
											array(
												'alias' => 'ProductMeasurement',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => 'Product.id = ProductMeasurement.product_id AND 
														CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
														ELSE 
															MemoDetail.measurement_unit_id
														END =ProductMeasurement.measurement_unit_id'
											),
											array(
												'alias' => 'ProductMeasurementSales',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => '
														Product.id = ProductMeasurementSales.product_id 
														AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
											),
											array(
												'alias' => 'Territory',
												'table' => 'territories',
												'type' => 'INNER',
												'conditions' => 'Memo.territory_id = Territory.id'
											),
											array(
												'alias' => 'Outlet',
												'table' => 'outlets',
												'type' => 'INNER',
												'conditions' => 'Memo.outlet_id = Outlet.id'
											),
											array(
												'alias' => 'Market',
												'table' => 'markets',
												'type' => 'INNER',
												'conditions' => 'Memo.market_id = Market.id'
											),
											array(
												'alias' => 'Thana',
												'table' => 'thanas',
												'type' => 'INNER',
												'conditions' => 'Memo.thana_id = Thana.id'
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
											),
											array(
												'alias' => 'ProductionCombination',
												'table' => 'product_combinations_v2',
												'type' => 'LEFT',
												'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
											),
											$program_joins ? $program_joins : ''
										),

										'fields' => array('ROUND(SUM(
															(ROUND(
																	(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
																	(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
																,0)) / 
																(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
														),2,1) AS volume,
													ROUND(SUM(
															(ROUND(
																	(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
																	(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
																,0)) / 
																(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
														),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
														sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
														COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc',  'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v', 'Product.brand_id'),
										'group' => array('Product.brand_id', 'MemoDetail.product_id, Product.cyp_cal, Product.cyp'),
										'order' => array('Product.brand_id asc'),

										'recursive' => -1
									));
								} elseif ($request_data['SalesAnalysisReports']['columns'] == 'outlet_type') {
									$q_results[$row_key] = $this->Memo->find('all', array(
										'conditions' => $conditions,
										'joins' => array(
											array(
												'alias' => 'MemoDetail',
												'table' => 'memo_details',
												'type' => 'INNER',
												'conditions' => 'Memo.id = MemoDetail.memo_id'
											),
											array(
												'alias' => 'dbp',
												'table' => 'discount_bonus_policies',
												'type' => 'left',
												'conditions' => 'MemoDetail.policy_id = dbp.id'
											),
											array(
												'alias' => 'Product',
												'table' => 'products',
												'type' => 'INNER',
												'conditions' => 'MemoDetail.product_id = Product.id'
											),
											array(
												'alias' => 'ProductMeasurement',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => 'Product.id = ProductMeasurement.product_id AND 
														CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
														ELSE 
															MemoDetail.measurement_unit_id
														END =ProductMeasurement.measurement_unit_id'
											),
											array(
												'alias' => 'ProductMeasurementSales',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => '
														Product.id = ProductMeasurementSales.product_id 
														AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
											),
											array(
												'alias' => 'Territory',
												'table' => 'territories',
												'type' => 'INNER',
												'conditions' => 'Memo.territory_id = Territory.id'
											),
											array(
												'alias' => 'Outlet',
												'table' => 'outlets',
												'type' => 'INNER',
												'conditions' => 'Memo.outlet_id = Outlet.id'
											),
											array(
												'alias' => 'Market',
												'table' => 'markets',
												'type' => 'INNER',
												'conditions' => 'Memo.market_id = Market.id'
											),
											array(
												'alias' => 'Thana',
												'table' => 'thanas',
												'type' => 'INNER',
												'conditions' => 'Memo.thana_id = Thana.id'
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
											),
											array(
												'alias' => 'ProductionCombination',
												'table' => 'product_combinations_v2',
												'type' => 'LEFT',
												'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
											),
											$program_joins ? $program_joins : ''
										),


										'fields' => array('ROUND(SUM(
															(ROUND(
																	(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
																	(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
																,0)) / 
																(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
														),2,1) AS volume,
														ROUND(SUM(
															(ROUND(
																	(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
																	(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
																,0)) / 
																(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
														),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
														sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
														COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc',  'Outlet.category_id'),
										'group' => array('Outlet.category_id'),

										'order' => array('Outlet.category_id asc'),

										'recursive' => -1
									));
									//pr($q_results);
									//exit;
									$q_results2[$row_key] = $this->Memo->find('all', array(
										'conditions' => $conditions,
										'joins' => array(
											array(
												'alias' => 'MemoDetail',
												'table' => 'memo_details',
												'type' => 'INNER',
												'conditions' => 'Memo.id = MemoDetail.memo_id'
											),
											array(
												'alias' => 'dbp',
												'table' => 'discount_bonus_policies',
												'type' => 'left',
												'conditions' => 'MemoDetail.policy_id = dbp.id'
											),
											array(
												'alias' => 'Product',
												'table' => 'products',
												'type' => 'INNER',
												'conditions' => 'MemoDetail.product_id = Product.id'
											),
											array(
												'alias' => 'ProductMeasurement',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => 'Product.id = ProductMeasurement.product_id AND 
														CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
														ELSE 
															MemoDetail.measurement_unit_id
														END =ProductMeasurement.measurement_unit_id'
											),
											array(
												'alias' => 'ProductMeasurementSales',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => '
														Product.id = ProductMeasurementSales.product_id 
														AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
											),
											array(
												'alias' => 'Territory',
												'table' => 'territories',
												'type' => 'INNER',
												'conditions' => 'Memo.territory_id = Territory.id'
											),
											array(
												'alias' => 'Outlet',
												'table' => 'outlets',
												'type' => 'INNER',
												'conditions' => 'Memo.outlet_id = Outlet.id'
											),
											array(
												'alias' => 'Market',
												'table' => 'markets',
												'type' => 'INNER',
												'conditions' => 'Memo.market_id = Market.id'
											),
											array(
												'alias' => 'Thana',
												'table' => 'thanas',
												'type' => 'INNER',
												'conditions' => 'Memo.thana_id = Thana.id'
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
											),
											array(
												'alias' => 'ProductionCombination',
												'table' => 'product_combinations_v2',
												'type' => 'LEFT',
												'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
											),
											$program_joins ? $program_joins : ''
										),

										'fields' => array('ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS volume,
										ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
										sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
										COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc',  'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v', 'Outlet.category_id'),
										'group' => array('Outlet.category_id', 'MemoDetail.product_id, Product.cyp_cal, Product.cyp'),
										'order' => array('Outlet.category_id asc'),

										'recursive' => -1
									));
								} elseif ($request_data['SalesAnalysisReports']['columns'] == 'national') {
									$q_results[$row_key] = $this->Memo->find('all', array(
										'conditions' => $conditions,
										'joins' => array(
											array(
												'alias' => 'MemoDetail',
												'table' => 'memo_details',
												'type' => 'INNER',
												'conditions' => 'Memo.id = MemoDetail.memo_id'
											),
											array(
												'alias' => 'dbp',
												'table' => 'discount_bonus_policies',
												'type' => 'left',
												'conditions' => 'MemoDetail.policy_id = dbp.id'
											),
											array(
												'alias' => 'Product',
												'table' => 'products',
												'type' => 'INNER',
												'conditions' => 'MemoDetail.product_id = Product.id'
											),
											array(
												'alias' => 'ProductMeasurement',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => 'Product.id = ProductMeasurement.product_id AND 
														CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
														ELSE 
															MemoDetail.measurement_unit_id
														END =ProductMeasurement.measurement_unit_id'
											),
											array(
												'alias' => 'ProductMeasurementSales',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => '
														Product.id = ProductMeasurementSales.product_id 
														AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
											),
											array(
												'alias' => 'Territory',
												'table' => 'territories',
												'type' => 'INNER',
												'conditions' => 'Memo.territory_id = Territory.id'
											),
											array(
												'alias' => 'Outlet',
												'table' => 'outlets',
												'type' => 'INNER',
												'conditions' => 'Memo.outlet_id = Outlet.id'
											),
											array(
												'alias' => 'Market',
												'table' => 'markets',
												'type' => 'INNER',
												'conditions' => 'Memo.market_id = Market.id'
											),
											array(
												'alias' => 'Thana',
												'table' => 'thanas',
												'type' => 'INNER',
												'conditions' => 'Memo.thana_id = Thana.id'
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
											),
											array(
												'alias' => 'ProductionCombination',
												'table' => 'product_combinations_v2',
												'type' => 'LEFT',
												'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
											),
											$program_joins ? $program_joins : ''
										),


										'fields' => array('ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS volume,
										ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
										sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
										COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc'),
										//'group' => array( 'Outlet.category_id'),

										//'order' => array( 'Outlet.category_id asc'),

										'recursive' => -1
									));
									// echo $this->Memo->getLastQuery();
									// pr($q_results);
									// exit;
									$q_results2[$row_key] = $this->Memo->find('all', array(
										'conditions' => $conditions,
										'joins' => array(
											array(
												'alias' => 'MemoDetail',
												'table' => 'memo_details',
												'type' => 'INNER',
												'conditions' => 'Memo.id = MemoDetail.memo_id'
											),
											array(
												'alias' => 'dbp',
												'table' => 'discount_bonus_policies',
												'type' => 'left',
												'conditions' => 'MemoDetail.policy_id = dbp.id'
											),
											array(
												'alias' => 'Product',
												'table' => 'products',
												'type' => 'INNER',
												'conditions' => 'MemoDetail.product_id = Product.id'
											),
											array(
												'alias' => 'ProductMeasurement',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => 'Product.id = ProductMeasurement.product_id AND 
														CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
														ELSE 
															MemoDetail.measurement_unit_id
														END =ProductMeasurement.measurement_unit_id'
											),
											array(
												'alias' => 'ProductMeasurementSales',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => '
														Product.id = ProductMeasurementSales.product_id 
														AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
											),
											array(
												'alias' => 'Territory',
												'table' => 'territories',
												'type' => 'INNER',
												'conditions' => 'Memo.territory_id = Territory.id'
											),
											array(
												'alias' => 'Outlet',
												'table' => 'outlets',
												'type' => 'INNER',
												'conditions' => 'Memo.outlet_id = Outlet.id'
											),
											array(
												'alias' => 'Market',
												'table' => 'markets',
												'type' => 'INNER',
												'conditions' => 'Memo.market_id = Market.id'
											),
											array(
												'alias' => 'Thana',
												'table' => 'thanas',
												'type' => 'INNER',
												'conditions' => 'Memo.thana_id = Thana.id'
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
											),
											array(
												'alias' => 'ProductionCombination',
												'table' => 'product_combinations_v2',
												'type' => 'LEFT',
												'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
											),
											$program_joins ? $program_joins : ''
										),

										'fields' => array('ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS volume,
										ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
										sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
										COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc',  'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v'),
										'group' => array('MemoDetail.product_id, Product.cyp_cal, Product.cyp'),
										'order' => array('MemoDetail.product_id asc'),

										'recursive' => -1
									));
								} elseif ($request_data['SalesAnalysisReports']['columns'] == 'product_virtual') {

									$this->Memo->MemoDetail->virtualFields['product_id'] = "CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END";
									$q_results[$row_key] = $this->Memo->find('all', array(
										'conditions' => $conditions,
										'joins' => array(
											array(
												'alias' => 'MemoDetail',
												'table' => 'memo_details',
												'type' => 'INNER',
												'conditions' => 'Memo.id = MemoDetail.memo_id'
											),
											array(
												'alias' => 'dbp',
												'table' => 'discount_bonus_policies',
												'type' => 'left',
												'conditions' => 'MemoDetail.policy_id = dbp.id'
											),
											array(
												'alias' => 'Product',
												'table' => 'products',
												'type' => 'INNER',
												'conditions' => 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END = Product.id'
											),
											array(
												'alias' => 'ProductMeasurement',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => 'Product.id = ProductMeasurement.product_id AND 
														CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
														ELSE 
															MemoDetail.measurement_unit_id
														END =ProductMeasurement.measurement_unit_id'
											),
											array(
												'alias' => 'ProductMeasurementSales',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => '
														Product.id = ProductMeasurementSales.product_id 
														AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
											),
											array(
												'alias' => 'Territory',
												'table' => 'territories',
												'type' => 'INNER',
												'conditions' => 'Memo.territory_id = Territory.id'
											),
											array(
												'alias' => 'Outlet',
												'table' => 'outlets',
												'type' => 'INNER',
												'conditions' => 'Memo.outlet_id = Outlet.id'
											),
											array(
												'alias' => 'Market',
												'table' => 'markets',
												'type' => 'INNER',
												'conditions' => 'Memo.market_id = Market.id'
											),
											array(
												'alias' => 'Thana',
												'table' => 'thanas',
												'type' => 'INNER',
												'conditions' => 'Memo.thana_id = Thana.id'
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
											),
											array(
												'alias' => 'ProductionCombination',
												'table' => 'product_combinations_v2',
												'type' => 'LEFT',
												'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
											),
											$program_joins ? $program_joins : ''
										),

										//'fields' => array('SUM(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) AS volume,SUM(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) AS bonus, sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value, COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc'),
										'fields' => array('ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS volume,
										ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
										sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
										COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END as MemoDetail__product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v', 'Product.order'),
										'group' => array('CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END, Product.cyp_cal, Product.cyp', 'Product.order'),
										'order' => array('Product.order asc'),
										'recursive' => -1
									));
								} else {
									$q_results[$row_key] = $this->Memo->find('all', array(
										'conditions' => $conditions,
										'joins' => array(
											array(
												'alias' => 'MemoDetail',
												'table' => 'memo_details',
												'type' => 'INNER',
												'conditions' => 'Memo.id = MemoDetail.memo_id'
											),
											array(
												'alias' => 'dbp',
												'table' => 'discount_bonus_policies',
												'type' => 'left',
												'conditions' => 'MemoDetail.policy_id = dbp.id'
											),
											array(
												'alias' => 'Product',
												'table' => 'products',
												'type' => 'INNER',
												'conditions' => 'MemoDetail.product_id = Product.id'
											),
											array(
												'alias' => 'ProductMeasurement',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => 'Product.id = ProductMeasurement.product_id AND 
														CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
														ELSE 
															MemoDetail.measurement_unit_id
														END =ProductMeasurement.measurement_unit_id'
											),
											array(
												'alias' => 'ProductMeasurementSales',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => '
														Product.id = ProductMeasurementSales.product_id 
														AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
											),
											array(
												'alias' => 'Territory',
												'table' => 'territories',
												'type' => 'INNER',
												'conditions' => 'Memo.territory_id = Territory.id'
											),
											array(
												'alias' => 'Outlet',
												'table' => 'outlets',
												'type' => 'INNER',
												'conditions' => 'Memo.outlet_id = Outlet.id'
											),
											array(
												'alias' => 'Market',
												'table' => 'markets',
												'type' => 'INNER',
												'conditions' => 'Memo.market_id = Market.id'
											),
											array(
												'alias' => 'Thana',
												'table' => 'thanas',
												'type' => 'INNER',
												'conditions' => 'Memo.thana_id = Thana.id'
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
											),
											array(
												'alias' => 'ProductionCombination',
												'table' => 'product_combinations_v2',
												'type' => 'LEFT',
												'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
											),
											$program_joins ? $program_joins : ''
										),

										//'fields' => array('SUM(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) AS volume,SUM(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) AS bonus, sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value, COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc'),
										'fields' => array('ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS volume,
										ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
										sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
										COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc',  'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v', 'Product.order'),
										'group' => array('MemoDetail.product_id, Product.cyp_cal, Product.cyp', 'Product.order'),
										'order' => array('Product.order asc'),
										'recursive' => -1
									));
								}

								//}
							}
						}

						//for national row
						if ($rows == 'national') {
							foreach ($rows_list as $row_key => $row_val) {
								//foreach($columns_list as $col_key => $col_val){

								//echo $row_val.'<br>';

								$conditions = array(
									//'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
									'Memo.gross_value >=' => 0,
									'Memo.status !=' => 0,
								);

								if ($request_data['SalesAnalysisReports']['sales_type_id'] != '') {
									$conditions['Outlet.is_pharma_type'] = $request_data['SalesAnalysisReports']['sales_type_id'];
								}

								if ($request_data['SalesAnalysisReports']['location_type_id']) {
									$conditions['Market.location_type_id'] = $request_data['SalesAnalysisReports']['location_type_id'];
								}

								//add new
								//pr($request_data);
								if ($request_data['SalesAnalysisReports']['product_type']) {
									$conditions['Product.source'] = $request_data['SalesAnalysisReports']['product_type'];
								}

								if (@$request_data['SalesAnalysisReports']['so_id']) {
									$conditions['Memo.sales_person_id'] = $request_data['SalesAnalysisReports']['so_id'];
								}

								if ($request_data['SalesAnalysisReports']['territory_id']) {
									$conditions['Memo.territory_id'] = $request_data['SalesAnalysisReports']['territory_id'];
								}

								if ($request_data['SalesAnalysisReports']['product_id']) {
									$conditions['MemoDetail.product_id'] = $request_data['SalesAnalysisReports']['product_id'];
								}
								if ($request_data['SalesAnalysisReports']['virtual_product_id']) {
									$conditions['OR'] = array(
										'MemoDetail.product_id' => $request_data['SalesAnalysisReports']['virtual_product_id'],
										'MemoDetail.virtual_product_id' => $request_data['SalesAnalysisReports']['virtual_product_id']
									);
								}
								//end add new

								if ($stockist_retailer == 1) {
									$conditions['OR'] = array(
										'ProductionCombination.id is null ',
										'ProductionCombination.min_qty >' => 1
									);
								}
								if ($stockist_retailer == 2) {
									$conditions['ProductionCombination.min_qty'] = 1;
								}
								//for rows
								$conditions['Memo.memo_date BETWEEN ? and ? '] = array($date_from, $date_to);


								/*if($rows=='so')$conditions['Memo.sales_person_id'] = $row_key;
									if($rows=='territory')$conditions['Memo.territory_id'] = $row_key;
									if($rows=='area')$conditions['Memo.office_id'] = $row_key;*/

								if ($rows == 'division') {
									$conditions['Division.id'] = $row_key;
								} elseif ($request_data['SalesAnalysisReports']['division_id']) {
									$conditions['Division.id'] = $request_data['SalesAnalysisReports']['division_id'];
								}

								//if($rows=='district')$conditions['District.id'] = $row_key;

								if ($rows == 'district') {
									$conditions['District.id'] = $row_key;
								} elseif ($request_data['SalesAnalysisReports']['district_id']) {
									$conditions['District.id'] = $request_data['SalesAnalysisReports']['district_id'];
								}

								if ($request_data['SalesAnalysisReports']['thana_id']) {
									$conditions['Thana.id'] = $request_data['SalesAnalysisReports']['thana_id'];
								}


								//for columns
								if ($columns == 'product') {
									$conditions['MemoDetail.product_id'] = $col_keys;
								}
								if ($columns == 'product_virtual') {
									$conditions['OR'] = array(
										'MemoDetail.product_id' => $col_keys,
										'MemoDetail.virtual_product_id' => $col_keys,
									);
								}


								if ($columns == 'category') {
									$conditions['Product.product_category_id'] = $col_keys;
								}
								if ($columns != 'category' && $request_data['SalesAnalysisReports']['product_category_id']) {
									$conditions['Product.product_category_id'] = $request_data['SalesAnalysisReports']['product_category_id'];
								}

								if ($columns == 'brand') {
									$conditions['Product.brand_id'] = $col_keys;
								}
								if ($columns != 'brand' && $request_data['SalesAnalysisReports']['brand_id']) {
									$conditions['Product.brand'] = $request_data['SalesAnalysisReports']['brand_id'];
								}

								//program sales
								$program_joins = array();
								if ($request_data['SalesAnalysisReports']['program_type_id']) {
									/*if($p_outlet_ids){
											$conditions['Memo.outlet_id'] = $p_outlet_ids;
										}else{
											$conditions['Memo.outlet_id'] = 0;
										}*/
									// $conditions[] = ("Memo.outlet_id in (select outlet_id from programs $where)");
									//
									$program_joins = array(
										'alias' => 'ProgramOutlet',
										'table' => "(select outlet_id from (select outlet_id from programs $where ) tt group by outlet_id  )",
										'type' => 'INNER',
										'conditions' => 'ProgramOutlet.outlet_id = Memo.outlet_id'
									);
								}
								//end program sales

								if ($office_id) {
									$conditions['Memo.office_id'] = $office_id;
								}

								if ($outlet_category_ids) {
									$conditions['Outlet.category_id'] = $outlet_category_ids;
								}

								// $conditions['MemoDetail.price >']=0;

								//pr($conditions);
								//exit;

								if ($request_data['SalesAnalysisReports']['columns'] == 'category') {
									$q_results[$row_key] = $this->Memo->find('all', array(
										'conditions' => $conditions,
										'joins' => array(
											array(
												'alias' => 'MemoDetail',
												'table' => 'memo_details',
												'type' => 'INNER',
												'conditions' => 'Memo.id = MemoDetail.memo_id'
											),
											array(
												'alias' => 'dbp',
												'table' => 'discount_bonus_policies',
												'type' => 'left',
												'conditions' => 'MemoDetail.policy_id = dbp.id'
											),
											array(
												'alias' => 'Product',
												'table' => 'products',
												'type' => 'INNER',
												'conditions' => 'MemoDetail.product_id = Product.id'
											),
											array(
												'alias' => 'ProductMeasurement',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => 'Product.id = ProductMeasurement.product_id AND 
														CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
														ELSE 
															MemoDetail.measurement_unit_id
														END =ProductMeasurement.measurement_unit_id'
											),
											array(
												'alias' => 'ProductMeasurementSales',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => '
														Product.id = ProductMeasurementSales.product_id 
														AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
											),
											array(
												'alias' => 'Territory',
												'table' => 'territories',
												'type' => 'INNER',
												'conditions' => 'Memo.territory_id = Territory.id'
											),
											array(
												'alias' => 'Outlet',
												'table' => 'outlets',
												'type' => 'INNER',
												'conditions' => 'Memo.outlet_id = Outlet.id'
											),
											array(
												'alias' => 'Market',
												'table' => 'markets',
												'type' => 'INNER',
												'conditions' => 'Memo.market_id = Market.id'
											),
											array(
												'alias' => 'Thana',
												'table' => 'thanas',
												'type' => 'INNER',
												'conditions' => 'Memo.thana_id = Thana.id'
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
											),
											array(
												'alias' => 'ProductionCombination',
												'table' => 'product_combinations_v2',
												'type' => 'LEFT',
												'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
											),
											$program_joins ? $program_joins : ''
										),


										'fields' => array('ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS volume,
										ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
										sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
										COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc',  'Product.product_category_id'),
										'group' => array('Product.product_category_id'),

										'order' => array('Product.product_category_id asc'),

										'recursive' => -1
									));
									//pr($q_results);
									//exit;
									$q_results2[$row_key] = $this->Memo->find('all', array(
										'conditions' => $conditions,
										'joins' => array(
											array(
												'alias' => 'MemoDetail',
												'table' => 'memo_details',
												'type' => 'INNER',
												'conditions' => 'Memo.id = MemoDetail.memo_id'
											),
											array(
												'alias' => 'dbp',
												'table' => 'discount_bonus_policies',
												'type' => 'left',
												'conditions' => 'MemoDetail.policy_id = dbp.id'
											),
											array(
												'alias' => 'Product',
												'table' => 'products',
												'type' => 'INNER',
												'conditions' => 'MemoDetail.product_id = Product.id'
											),
											array(
												'alias' => 'ProductMeasurement',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => 'Product.id = ProductMeasurement.product_id AND 
														CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
														ELSE 
															MemoDetail.measurement_unit_id
														END =ProductMeasurement.measurement_unit_id'
											),
											array(
												'alias' => 'ProductMeasurementSales',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => '
														Product.id = ProductMeasurementSales.product_id 
														AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
											),
											array(
												'alias' => 'Territory',
												'table' => 'territories',
												'type' => 'INNER',
												'conditions' => 'Memo.territory_id = Territory.id'
											),
											array(
												'alias' => 'Outlet',
												'table' => 'outlets',
												'type' => 'INNER',
												'conditions' => 'Memo.outlet_id = Outlet.id'
											),
											array(
												'alias' => 'Market',
												'table' => 'markets',
												'type' => 'INNER',
												'conditions' => 'Memo.market_id = Market.id'
											),
											array(
												'alias' => 'Thana',
												'table' => 'thanas',
												'type' => 'INNER',
												'conditions' => 'Memo.thana_id = Thana.id'
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
											),
											array(
												'alias' => 'ProductionCombination',
												'table' => 'product_combinations_v2',
												'type' => 'LEFT',
												'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
											),
											$program_joins ? $program_joins : ''
										),

										'fields' => array('ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS volume,
										ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
										sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
										COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc',  'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v', 'Product.product_category_id'),
										'group' => array('Product.product_category_id', 'MemoDetail.product_id, Product.cyp_cal, Product.cyp'),
										'order' => array('Product.product_category_id asc'),

										'recursive' => -1
									));
								} elseif ($request_data['SalesAnalysisReports']['columns'] == 'brand') {
									$q_results[$row_key] = $this->Memo->find('all', array(
										'conditions' => $conditions,
										'joins' => array(
											array(
												'alias' => 'MemoDetail',
												'table' => 'memo_details',
												'type' => 'INNER',
												'conditions' => 'Memo.id = MemoDetail.memo_id'
											),
											array(
												'alias' => 'dbp',
												'table' => 'discount_bonus_policies',
												'type' => 'left',
												'conditions' => 'MemoDetail.policy_id = dbp.id'
											),
											array(
												'alias' => 'Product',
												'table' => 'products',
												'type' => 'INNER',
												'conditions' => 'MemoDetail.product_id = Product.id'
											),
											array(
												'alias' => 'ProductMeasurement',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => 'Product.id = ProductMeasurement.product_id AND 
														CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
														ELSE 
															MemoDetail.measurement_unit_id
														END =ProductMeasurement.measurement_unit_id'
											),
											array(
												'alias' => 'ProductMeasurementSales',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => '
														Product.id = ProductMeasurementSales.product_id 
														AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
											),
											array(
												'alias' => 'Territory',
												'table' => 'territories',
												'type' => 'INNER',
												'conditions' => 'Memo.territory_id = Territory.id'
											),
											array(
												'alias' => 'Outlet',
												'table' => 'outlets',
												'type' => 'INNER',
												'conditions' => 'Memo.outlet_id = Outlet.id'
											),
											array(
												'alias' => 'Market',
												'table' => 'markets',
												'type' => 'INNER',
												'conditions' => 'Memo.market_id = Market.id'
											),
											array(
												'alias' => 'Thana',
												'table' => 'thanas',
												'type' => 'INNER',
												'conditions' => 'Memo.thana_id = Thana.id'
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
											),
											array(
												'alias' => 'ProductionCombination',
												'table' => 'product_combinations_v2',
												'type' => 'LEFT',
												'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
											),
											$program_joins ? $program_joins : ''
										),


										'fields' => array('ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS volume,
										ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
										sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
										COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc',  'Product.brand_id'),
										'group' => array('Product.brand_id'),

										'order' => array('Product.brand_id asc'),

										'recursive' => -1
									));
									//pr($q_results);
									//exit;
									$q_results2[$row_key] = $this->Memo->find('all', array(
										'conditions' => $conditions,
										'joins' => array(
											array(
												'alias' => 'MemoDetail',
												'table' => 'memo_details',
												'type' => 'INNER',
												'conditions' => 'Memo.id = MemoDetail.memo_id'
											),
											array(
												'alias' => 'dbp',
												'table' => 'discount_bonus_policies',
												'type' => 'left',
												'conditions' => 'MemoDetail.policy_id = dbp.id'
											),
											array(
												'alias' => 'Product',
												'table' => 'products',
												'type' => 'INNER',
												'conditions' => 'MemoDetail.product_id = Product.id'
											),
											array(
												'alias' => 'ProductMeasurement',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => 'Product.id = ProductMeasurement.product_id AND 
														CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
														ELSE 
															MemoDetail.measurement_unit_id
														END =ProductMeasurement.measurement_unit_id'
											),
											array(
												'alias' => 'ProductMeasurementSales',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => '
														Product.id = ProductMeasurementSales.product_id 
														AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
											),
											array(
												'alias' => 'Territory',
												'table' => 'territories',
												'type' => 'INNER',
												'conditions' => 'Memo.territory_id = Territory.id'
											),
											array(
												'alias' => 'Outlet',
												'table' => 'outlets',
												'type' => 'INNER',
												'conditions' => 'Memo.outlet_id = Outlet.id'
											),
											array(
												'alias' => 'Market',
												'table' => 'markets',
												'type' => 'INNER',
												'conditions' => 'Memo.market_id = Market.id'
											),
											array(
												'alias' => 'Thana',
												'table' => 'thanas',
												'type' => 'INNER',
												'conditions' => 'Memo.thana_id = Thana.id'
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
											),
											array(
												'alias' => 'ProductionCombination',
												'table' => 'product_combinations_v2',
												'type' => 'LEFT',
												'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
											),
											$program_joins ? $program_joins : ''
										),

										'fields' => array('ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS volume,
										ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS bonus,
										sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
										sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
										COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc',  'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v', 'Product.brand_id'),
										'group' => array('Product.brand_id', 'MemoDetail.product_id, Product.cyp_cal, Product.cyp'),
										'order' => array('Product.brand_id asc'),

										'recursive' => -1
									));
								} elseif ($request_data['SalesAnalysisReports']['columns'] == 'outlet_type') {
									$q_results[$row_key] = $this->Memo->find('all', array(
										'conditions' => $conditions,
										'joins' => array(
											array(
												'alias' => 'MemoDetail',
												'table' => 'memo_details',
												'type' => 'INNER',
												'conditions' => 'Memo.id = MemoDetail.memo_id'
											),
											array(
												'alias' => 'dbp',
												'table' => 'discount_bonus_policies',
												'type' => 'left',
												'conditions' => 'MemoDetail.policy_id = dbp.id'
											),
											array(
												'alias' => 'Product',
												'table' => 'products',
												'type' => 'INNER',
												'conditions' => 'MemoDetail.product_id = Product.id'
											),
											array(
												'alias' => 'ProductMeasurement',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => 'Product.id = ProductMeasurement.product_id AND 
														CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
														ELSE 
															MemoDetail.measurement_unit_id
														END =ProductMeasurement.measurement_unit_id'
											),
											array(
												'alias' => 'ProductMeasurementSales',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => '
														Product.id = ProductMeasurementSales.product_id 
														AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
											),
											array(
												'alias' => 'Territory',
												'table' => 'territories',
												'type' => 'INNER',
												'conditions' => 'Memo.territory_id = Territory.id'
											),
											array(
												'alias' => 'Outlet',
												'table' => 'outlets',
												'type' => 'INNER',
												'conditions' => 'Memo.outlet_id = Outlet.id'
											),
											array(
												'alias' => 'Market',
												'table' => 'markets',
												'type' => 'INNER',
												'conditions' => 'Memo.market_id = Market.id'
											),
											array(
												'alias' => 'Thana',
												'table' => 'thanas',
												'type' => 'INNER',
												'conditions' => 'Memo.thana_id = Thana.id'
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
											),
											array(
												'alias' => 'ProductionCombination',
												'table' => 'product_combinations_v2',
												'type' => 'LEFT',
												'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
											),
											$program_joins ? $program_joins : ''
										),


										'fields' => array('ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS volume,
										ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
										sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
										COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc',  'Outlet.category_id'),
										'group' => array('Outlet.category_id'),

										'order' => array('Outlet.category_id asc'),

										'recursive' => -1
									));
									//pr($q_results);
									//exit;
									$q_results2[$row_key] = $this->Memo->find('all', array(
										'conditions' => $conditions,
										'joins' => array(
											array(
												'alias' => 'MemoDetail',
												'table' => 'memo_details',
												'type' => 'INNER',
												'conditions' => 'Memo.id = MemoDetail.memo_id'
											),
											array(
												'alias' => 'dbp',
												'table' => 'discount_bonus_policies',
												'type' => 'left',
												'conditions' => 'MemoDetail.policy_id = dbp.id'
											),
											array(
												'alias' => 'Product',
												'table' => 'products',
												'type' => 'INNER',
												'conditions' => 'MemoDetail.product_id = Product.id'
											),
											array(
												'alias' => 'ProductMeasurement',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => 'Product.id = ProductMeasurement.product_id AND 
														CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
														ELSE 
															MemoDetail.measurement_unit_id
														END =ProductMeasurement.measurement_unit_id'
											),
											array(
												'alias' => 'ProductMeasurementSales',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => '
														Product.id = ProductMeasurementSales.product_id 
														AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
											),
											array(
												'alias' => 'Territory',
												'table' => 'territories',
												'type' => 'INNER',
												'conditions' => 'Memo.territory_id = Territory.id'
											),
											array(
												'alias' => 'Outlet',
												'table' => 'outlets',
												'type' => 'INNER',
												'conditions' => 'Memo.outlet_id = Outlet.id'
											),
											array(
												'alias' => 'Market',
												'table' => 'markets',
												'type' => 'INNER',
												'conditions' => 'Memo.market_id = Market.id'
											),
											array(
												'alias' => 'Thana',
												'table' => 'thanas',
												'type' => 'INNER',
												'conditions' => 'Memo.thana_id = Thana.id'
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
											),
											array(
												'alias' => 'ProductionCombination',
												'table' => 'product_combinations_v2',
												'type' => 'LEFT',
												'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
											),
											$program_joins ? $program_joins : ''
										),

										'fields' => array('ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS volume,
										ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS bonus,
										sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
										sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
										COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc',  'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v', 'Outlet.category_id'),
										'group' => array('Outlet.category_id', 'MemoDetail.product_id, Product.cyp_cal, Product.cyp'),
										'order' => array('Outlet.category_id asc'),

										'recursive' => -1
									));
								} elseif ($request_data['SalesAnalysisReports']['columns'] == 'national') {
									$q_results[] = $this->Memo->find('all', array(
										'conditions' => $conditions,
										'joins' => array(
											array(
												'alias' => 'MemoDetail',
												'table' => 'memo_details',
												'type' => 'INNER',
												'conditions' => 'Memo.id = MemoDetail.memo_id'
											),
											array(
												'alias' => 'dbp',
												'table' => 'discount_bonus_policies',
												'type' => 'left',
												'conditions' => 'MemoDetail.policy_id = dbp.id'
											),
											array(
												'alias' => 'Product',
												'table' => 'products',
												'type' => 'INNER',
												'conditions' => 'MemoDetail.product_id = Product.id'
											),
											array(
												'alias' => 'ProductMeasurement',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => 'Product.id = ProductMeasurement.product_id AND 
														CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
														ELSE 
															MemoDetail.measurement_unit_id
														END =ProductMeasurement.measurement_unit_id'
											),
											array(
												'alias' => 'ProductMeasurementSales',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => '
														Product.id = ProductMeasurementSales.product_id 
														AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
											),
											array(
												'alias' => 'Territory',
												'table' => 'territories',
												'type' => 'INNER',
												'conditions' => 'Memo.territory_id = Territory.id'
											),
											array(
												'alias' => 'Outlet',
												'table' => 'outlets',
												'type' => 'INNER',
												'conditions' => 'Memo.outlet_id = Outlet.id'
											),
											array(
												'alias' => 'Market',
												'table' => 'markets',
												'type' => 'INNER',
												'conditions' => 'Memo.market_id = Market.id'
											),
											array(
												'alias' => 'Thana',
												'table' => 'thanas',
												'type' => 'INNER',
												'conditions' => 'Memo.thana_id = Thana.id'
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
											),
											array(
												'alias' => 'ProductionCombination',
												'table' => 'product_combinations_v2',
												'type' => 'LEFT',
												'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
											),
											$program_joins ? $program_joins : ''
										),


										'fields' => array('ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS volume,
										ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS bonus,
										sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
										sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
										COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc'),
										//'group' => array( 'Outlet.category_id'),

										//'order' => array( 'Outlet.category_id asc'),

										'recursive' => -1
									));
									/*echo $this->Memo->getLastQuery();
										pr($q_results);
										exit;*/
									$q_results2[] = $this->Memo->find('all', array(
										'conditions' => $conditions,
										'joins' => array(
											array(
												'alias' => 'MemoDetail',
												'table' => 'memo_details',
												'type' => 'INNER',
												'conditions' => 'Memo.id = MemoDetail.memo_id'
											),
											array(
												'alias' => 'dbp',
												'table' => 'discount_bonus_policies',
												'type' => 'left',
												'conditions' => 'MemoDetail.policy_id = dbp.id'
											),
											array(
												'alias' => 'Product',
												'table' => 'products',
												'type' => 'INNER',
												'conditions' => 'MemoDetail.product_id = Product.id'
											),
											array(
												'alias' => 'ProductMeasurement',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => 'Product.id = ProductMeasurement.product_id AND 
														CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
														ELSE 
															MemoDetail.measurement_unit_id
														END =ProductMeasurement.measurement_unit_id'
											),
											array(
												'alias' => 'ProductMeasurementSales',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => '
														Product.id = ProductMeasurementSales.product_id 
														AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
											),
											array(
												'alias' => 'Territory',
												'table' => 'territories',
												'type' => 'INNER',
												'conditions' => 'Memo.territory_id = Territory.id'
											),
											array(
												'alias' => 'Outlet',
												'table' => 'outlets',
												'type' => 'INNER',
												'conditions' => 'Memo.outlet_id = Outlet.id'
											),
											array(
												'alias' => 'Market',
												'table' => 'markets',
												'type' => 'INNER',
												'conditions' => 'Memo.market_id = Market.id'
											),
											array(
												'alias' => 'Thana',
												'table' => 'thanas',
												'type' => 'INNER',
												'conditions' => 'Memo.thana_id = Thana.id'
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
											),
											array(
												'alias' => 'ProductionCombination',
												'table' => 'product_combinations_v2',
												'type' => 'LEFT',
												'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
											),
											$program_joins ? $program_joins : ''
										),

										'fields' => array('ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS volume,
										ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
										sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
										COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc',  'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v'),
										'group' => array('MemoDetail.product_id, Product.cyp_cal, Product.cyp'),
										//'order' => array( 'Outlet.category_id asc'),

										'recursive' => -1
									));
								} elseif ($request_data['SalesAnalysisReports']['columns'] == 'product_virtual') {
									//pr($conditions);
									$this->Memo->MemoDetail->virtualFields['product_id'] = "CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END";
									$q_results[$row_key] = $this->Memo->find('all', array(
										'conditions' => $conditions,
										'joins' => array(
											array(
												'alias' => 'MemoDetail',
												'table' => 'memo_details',
												'type' => 'INNER',
												'conditions' => 'Memo.id = MemoDetail.memo_id'
											),
											array(
												'alias' => 'dbp',
												'table' => 'discount_bonus_policies',
												'type' => 'left',
												'conditions' => 'MemoDetail.policy_id = dbp.id'
											),
											array(
												'alias' => 'Product',
												'table' => 'products',
												'type' => 'INNER',
												'conditions' => 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END = Product.id'
											),
											array(
												'alias' => 'ProductMeasurement',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														MemoDetail.measurement_unit_id
													END =ProductMeasurement.measurement_unit_id'
											),
											array(
												'alias' => 'ProductMeasurementSales',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => '
													Product.id = ProductMeasurementSales.product_id 
													AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
											),
											array(
												'alias' => 'Territory',
												'table' => 'territories',
												'type' => 'INNER',
												'conditions' => 'Memo.territory_id = Territory.id'
											),
											array(
												'alias' => 'Outlet',
												'table' => 'outlets',
												'type' => 'INNER',
												'conditions' => 'Memo.outlet_id = Outlet.id'
											),
											array(
												'alias' => 'Market',
												'table' => 'markets',
												'type' => 'INNER',
												'conditions' => 'Memo.market_id = Market.id'
											),
											array(
												'alias' => 'Thana',
												'table' => 'thanas',
												'type' => 'INNER',
												'conditions' => 'Memo.thana_id = Thana.id'
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
											),
											array(
												'alias' => 'ProductionCombination',
												'table' => 'product_combinations_v2',
												'type' => 'LEFT',
												'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
											),
											$program_joins ? $program_joins : ''
										),
										//'fields' => array('Memo.id', 'MemoDetail.id', 'Territory.id', 'Outlet.id', 'Outlet.name', 'Market.id', 'Thana.id', 'District.name', 'Division.name'),
										'fields' => array('ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS volume,
										ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
										sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
										COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END as MemoDetail__product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v', 'Product.order'),
										'group' => array('CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END', 'Product.cyp_cal', 'Product.cyp', 'Product.order'),
										'order' => array('Product.order asc'),
										'recursive' => -1
									));
								} else {
									//pr($conditions);
									$q_results[$row_key] = $this->Memo->find('all', array(
										'conditions' => $conditions,
										'joins' => array(
											array(
												'alias' => 'MemoDetail',
												'table' => 'memo_details',
												'type' => 'INNER',
												'conditions' => 'Memo.id = MemoDetail.memo_id'
											),
											array(
												'alias' => 'dbp',
												'table' => 'discount_bonus_policies',
												'type' => 'left',
												'conditions' => 'MemoDetail.policy_id = dbp.id'
											),
											array(
												'alias' => 'Product',
												'table' => 'products',
												'type' => 'INNER',
												'conditions' => 'MemoDetail.product_id = Product.id'
											),
											array(
												'alias' => 'ProductMeasurement',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														MemoDetail.measurement_unit_id
													END =ProductMeasurement.measurement_unit_id'
											),
											array(
												'alias' => 'ProductMeasurementSales',
												'table' => 'product_measurements',
												'type' => 'LEFT',
												'conditions' => '
													Product.id = ProductMeasurementSales.product_id 
													AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
											),
											array(
												'alias' => 'Territory',
												'table' => 'territories',
												'type' => 'INNER',
												'conditions' => 'Memo.territory_id = Territory.id'
											),
											array(
												'alias' => 'Outlet',
												'table' => 'outlets',
												'type' => 'INNER',
												'conditions' => 'Memo.outlet_id = Outlet.id'
											),
											array(
												'alias' => 'Market',
												'table' => 'markets',
												'type' => 'INNER',
												'conditions' => 'Memo.market_id = Market.id'
											),
											array(
												'alias' => 'Thana',
												'table' => 'thanas',
												'type' => 'INNER',
												'conditions' => 'Memo.thana_id = Thana.id'
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
											),
											array(
												'alias' => 'ProductionCombination',
												'table' => 'product_combinations_v2',
												'type' => 'LEFT',
												'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
											),
											$program_joins ? $program_joins : ''
										),
										//'fields' => array('Memo.id', 'MemoDetail.id', 'Territory.id', 'Outlet.id', 'Outlet.name', 'Market.id', 'Thana.id', 'District.name', 'Division.name'),
										'fields' => array('ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS volume,
										ROUND(SUM(
											(ROUND(
													(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
										),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value, 
										sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
										COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v', 'Product.order'),
										'group' => array('MemoDetail.product_id', 'Product.cyp_cal', 'Product.cyp', 'Product.order'),
										'order' => array('Product.order asc'),
										'recursive' => -1
									));
								}

								//}
							}
						}
					} else {
						//foreach($rows_list as $row_key => $row_val)
						//{
						//foreach($columns_list as $col_key => $col_val)
						//{
						$conditions = array(
							'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
							'Memo.gross_value >=' => 0,
							'Memo.status !=' => 0,
						);

						if ($request_data['SalesAnalysisReports']['sales_type_id']  != '') {
							$conditions['Outlet.is_pharma_type'] = $request_data['SalesAnalysisReports']['sales_type_id'];
						}

						if ($request_data['SalesAnalysisReports']['location_type_id']) {
							$conditions['Market.location_type_id'] = $request_data['SalesAnalysisReports']['location_type_id'];
						}

						//add new
						//pr($request_data);
						if ($request_data['SalesAnalysisReports']['product_type']) {
							$conditions['Product.source'] = $request_data['SalesAnalysisReports']['product_type'];
						}

						if (@$request_data['SalesAnalysisReports']['so_id']) {
							$conditions['Memo.sales_person_id'] = $request_data['SalesAnalysisReports']['so_id'];
						}

						if ($request_data['SalesAnalysisReports']['territory_id']) {
							$conditions['Memo.territory_id'] = $request_data['SalesAnalysisReports']['territory_id'];
						}

						if ($request_data['SalesAnalysisReports']['product_id']) {
							$conditions['MemoDetail.product_id'] = $request_data['SalesAnalysisReports']['product_id'];
						}
						if ($request_data['SalesAnalysisReports']['virtual_product_id']) {
							$conditions['OR'] = array(
								'MemoDetail.product_id' => $request_data['SalesAnalysisReports']['virtual_product_id'],
								'MemoDetail.virtual_product_id' => $request_data['SalesAnalysisReports']['virtual_product_id']
							);
						}
						if ($stockist_retailer == 1) {
							$conditions['OR'] = array(
								'ProductionCombination.id is null ',
								'ProductionCombination.min_qty >' => 1
							);
						}
						if ($stockist_retailer == 2) {
							$conditions['ProductionCombination.min_qty'] = 1;
						}

						//end add new

						//for rows
						if ($rows == 'so') {
							$conditions['Memo.sales_person_id'] = $row_keys;
						}
						if ($rows == 'territory') {
							$conditions['Memo.territory_id'] = $row_keys;
						}
						if ($rows == 'area') {
							$conditions['Memo.office_id'] = $row_keys;
						}
						if ($rows == 'region') {
							$conditions['Office.parent_office_id'] = $row_keys;
						}





						if ($rows == 'division') {
							$conditions['Division.id'] = $row_keys;
						} elseif ($request_data['SalesAnalysisReports']['division_id']) {
							$conditions['Division.id'] = $request_data['SalesAnalysisReports']['division_id'];
						}

						if ($rows == 'district') {
							$conditions['District.id'] = $row_keys;
						} elseif ($request_data['SalesAnalysisReports']['district_id']) {
							$conditions['District.id'] = $request_data['SalesAnalysisReports']['district_id'];
						}

						if (($rows == 'division' || $rows == 'district') && $office_id) {
							$sql = "SELECT th_te.thana_id, th_te.territory_id, te.name as territory_name, te.office_id as office_id, th.name as thana_name, th.district_id, dis.name as district_name, dis.division_id as division_id
									FROM thana_territories as th_te 
									INNER JOIN territories as te ON (th_te.territory_id=te.id)
									INNER JOIN thanas as th ON (th_te.thana_id=th.id)
									INNER JOIN districts as dis ON (th.district_id=dis.id)
									WHERE te.office_id=$office_id";
							$t_results = $this->SalesPerson->query($sql);
							//pr($t_results);

							$thana_ids = array();
							foreach ($t_results as $t_result) {
								array_push($thana_ids, $t_result[0]['thana_id']);
							}

							$conditions['Thana.id'] = $thana_ids;
						}


						if ($rows == 'thana') {
							$conditions['Thana.id'] = $row_keys;
						} elseif ($request_data['SalesAnalysisReports']['thana_id']) {
							$conditions['Thana.id'] = $request_data['SalesAnalysisReports']['thana_id'];
						}

						//for thana option
						if ($request_data['SalesAnalysisReports']['thana_id']) {
							$conditions['Thana.id'] = $request_data['SalesAnalysisReports']['thana_id'];
						}


						//for columns
						if ($columns == 'product') {
							$conditions['MemoDetail.product_id'] = $col_keys;
						}
						if ($columns == 'product_virtual') {
							$conditions['OR'] = array(
								'MemoDetail.product_id' => $col_keys,
								'MemoDetail.virtual_product_id' => $col_keys,
							);
						}


						if ($columns == 'category') {
							$conditions['Product.product_category_id'] = $col_keys;
						}
						if ($columns != 'category' && $request_data['SalesAnalysisReports']['product_category_id']) {
							$conditions['Product.product_category_id'] = $request_data['SalesAnalysisReports']['product_category_id'];
						}


						if ($columns == 'brand') {
							$conditions['Product.brand_id'] = $col_keys;
						}
						if ($columns != 'brand' && $request_data['SalesAnalysisReports']['brand_id']) {
							$conditions['Product.brand_id'] = $request_data['SalesAnalysisReports']['brand_id'];
						}


						if ($columns == 'outlet_type') {
							$conditions['Outlet.category_id'] = $col_keys;
						}
						if ($columns != 'outlet_type' && $request_data['SalesAnalysisReports']['outlet_category_id']) {
							$conditions['Outlet.category_id'] = $request_data['SalesAnalysisReports']['outlet_category_id'];
						}

						//program sales
						$program_joins = array();
						if ($request_data['SalesAnalysisReports']['program_type_id']) {
							/*if($p_outlet_ids){
										$conditions['Memo.outlet_id'] = $p_outlet_ids;
									}else{
										$conditions['Memo.outlet_id'] = 0;
									}*/
							// $conditions[] = ("Memo.outlet_id in (select outlet_id from programs $where)");
							//
							$program_joins = array(
								'alias' => 'ProgramOutlet',
								'table' => "(select outlet_id from programs $where)",
								'type' => 'INNER',
								'conditions' => 'ProgramOutlet.outlet_id = Memo.outlet_id'
							);
						}
						//end program sales

						if ($office_id) {
							$conditions['Memo.office_id'] = $office_id;
						}







						$fields = array();
						$group = array();

						$fields2 = array();
						$group2 = array();
						$program_officer_join = array();
						if ($request_data['SalesAnalysisReports']['rows'] == 'so') {
							if ($request_data['SalesAnalysisReports']['columns'] == 'product') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'sales_person_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group = array('sales_person_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');

								if ($request_data['SalesAnalysisReports']['program_type_id']) {
									$fields2 = array('ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS volume,
									ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
									sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
									COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'sales_person_id', 'Outlet.category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
									$group2 = array('sales_person_id', 'Outlet.category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
								}
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'product_virtual') {
								$this->Memo->MemoDetail->virtualFields['product_id'] = "CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END";
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'sales_person_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END as MemoDetail__product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group = array('sales_person_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END', 'cyp_cal', 'cyp');

								if ($request_data['SalesAnalysisReports']['program_type_id']) {
									$fields2 = array('ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS volume,
									ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
									sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
									COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'sales_person_id', 'Outlet.category_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END as MemoDetail__product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
									$group2 = array('sales_person_id', 'Outlet.category_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END', 'cyp_cal', 'cyp');
								}
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'category') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Memo.sales_person_id', 'Product.product_category_id');
								$group = array('sales_person_id', 'Product.product_category_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'sales_person_id', 'Product.product_category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('Memo.sales_person_id', 'Product.product_category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'brand') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Memo.sales_person_id', 'Product.brand_id');
								$group = array('sales_person_id', 'Product.brand_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'sales_person_id', 'Product.brand_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('Memo.sales_person_id', 'Product.brand_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'outlet_type') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'sales_person_id', 'Outlet.category_id');
								$group = array('sales_person_id', 'Outlet.category_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'sales_person_id', 'Outlet.category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('sales_person_id', 'Outlet.category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'national') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'sales_person_id');
								$group = array('sales_person_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'sales_person_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('sales_person_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'territory') {
							if ($request_data['SalesAnalysisReports']['columns'] == 'product') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc,CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end territory_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group = array('CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end', 'MemoDetail.product_id', 'cyp_cal', 'cyp');

								if ($request_data['SalesAnalysisReports']['program_type_id']) {
									$fields2 = array('ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS volume,
									ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
									sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
									COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc,CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end territory_id', 'Outlet.category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
									$group2 = array('CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end', 'Outlet.category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
								}
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'product_virtual') {
								$this->Memo->MemoDetail->virtualFields['product_id'] = "CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END";
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc,CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end territory_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END as MemoDetail__product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group = array('CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END', 'cyp_cal', 'cyp');

								if ($request_data['SalesAnalysisReports']['program_type_id']) {
									$fields2 = array('ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS volume,
									ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
									sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
									COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc,CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end territory_id', 'Outlet.category_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END as MemoDetail__product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
									$group2 = array('CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end', 'Outlet.category_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END ', 'cyp_cal', 'cyp');
								}
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'category') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc,CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end territory_id', 'Product.product_category_id');
								$group = array('CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end', 'Product.product_category_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc,CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end territory_id', 'Product.product_category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end', 'Product.product_category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'brand') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc,CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end territory_id', 'Product.brand_id');
								$group = array('CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end', 'Product.brand_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc,CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end territory_id', 'Product.brand_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end', 'Product.brand_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'outlet_type') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc,CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end territory_id', 'Outlet.category_id');
								$group = array('CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end', 'Outlet.category_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc,CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end territory_id', 'Outlet.category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end', 'Outlet.category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'national') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc,CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end territory_id');
								$group = array('CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc,CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end territory_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'area') {
							if ($request_data['SalesAnalysisReports']['columns'] == 'product') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Memo.office_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group = array('Memo.office_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');


								if ($request_data['SalesAnalysisReports']['program_type_id']) {
									$fields2 = array('ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS volume,
									ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
									sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
									COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Memo.office_id', 'Outlet.category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
									$group2 = array('Memo.office_id', 'Outlet.category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
								}
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'product_virtual') {
								$this->Memo->MemoDetail->virtualFields['product_id'] = "CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END";
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Memo.office_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END as MemoDetail__product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group = array('Memo.office_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END ', 'cyp_cal', 'cyp');


								if ($request_data['SalesAnalysisReports']['program_type_id']) {
									$fields2 = array('ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS volume,
									ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
									sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
									COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Memo.office_id', 'Outlet.category_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END as MemoDetail__product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
									$group2 = array('Memo.office_id', 'Outlet.category_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END', 'cyp_cal', 'cyp');
								}
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'category') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Memo.office_id', 'Product.product_category_id');
								$group = array('Memo.office_id', 'Product.product_category_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Memo.office_id', 'Product.product_category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('Memo.office_id', 'Product.product_category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'brand') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Memo.office_id', 'Product.brand_id');
								$group = array('Memo.office_id', 'Product.brand_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Memo.office_id', 'Product.brand_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('Memo.office_id', 'Product.brand_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'outlet_type') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Memo.office_id', 'Outlet.category_id');
								$group = array('Memo.office_id', 'Outlet.category_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Memo.office_id', 'Outlet.category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('Memo.office_id', 'Outlet.category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'national') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Memo.office_id');
								$group = array('Memo.office_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Memo.office_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('Memo.office_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							}
						}
						if ($request_data['SalesAnalysisReports']['rows'] == 'region') {
							if ($request_data['SalesAnalysisReports']['columns'] == 'product') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Office.parent_office_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group = array('Office.parent_office_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');


								if ($request_data['SalesAnalysisReports']['program_type_id']) {
									$fields2 = array('ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS volume,
									ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
									sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
									COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Office.parent_office_id', 'Outlet.category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
									$group2 = array('Office.parent_office_id', 'Outlet.category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
								}
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'product_virtual') {
								$this->Memo->MemoDetail->virtualFields['product_id'] = "CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END";
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Office.parent_office_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END as MemoDetail__product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group = array('Office.parent_office_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END', 'cyp_cal', 'cyp');


								if ($request_data['SalesAnalysisReports']['program_type_id']) {
									$fields2 = array('ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS volume,
									ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
									sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
									COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Office.parent_office_id', 'Outlet.category_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END ', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
									$group2 = array('Office.parent_office_id', 'Outlet.category_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END ', 'cyp_cal', 'cyp');
								}
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'category') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Office.parent_office_id', 'Product.product_category_id');
								$group = array('Office.parent_office_id', 'Product.product_category_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Office.parent_office_id', 'Product.product_category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('Office.parent_office_id', 'Product.product_category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'brand') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Office.parent_office_id', 'Product.brand_id');
								$group = array('Office.parent_office_id', 'Product.brand_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Office.parent_office_id', 'Product.brand_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('Office.parent_office_id', 'Product.brand_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'outlet_type') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Office.parent_office_id', 'Outlet.category_id');
								$group = array('Office.parent_office_id', 'Outlet.category_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Office.parent_office_id', 'Outlet.category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('Office.parent_office_id', 'Outlet.category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'national') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Office.parent_office_id');
								$group = array('Office.parent_office_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Office.parent_office_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('Office.parent_office_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							}
						}



						if ($request_data['SalesAnalysisReports']['rows'] == 'division') {
							if ($request_data['SalesAnalysisReports']['columns'] == 'product') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Division.id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group = array('Division.id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');

								if ($request_data['SalesAnalysisReports']['program_type_id']) {
									$fields2 = array('ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS volume,
									ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
									sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
									COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Division.id', 'Outlet.category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
									$group2 = array('Division.id', 'Outlet.category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
								}
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'product_virtual') {
								$this->Memo->MemoDetail->virtualFields['product_id'] = "CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END";
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Division.id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END as MemoDetail__product_id ', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group = array('Division.id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END', 'cyp_cal', 'cyp');

								if ($request_data['SalesAnalysisReports']['program_type_id']) {
									$fields2 = array('ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS volume,
									ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
									sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
									COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Division.id', 'Outlet.category_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END as MemoDetail__product_id  ', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
									$group2 = array('Division.id', 'Outlet.category_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END ', 'cyp_cal', 'cyp');
								}
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'category') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Division.id', 'Product.product_category_id');
								$group = array('Division.id', 'Product.product_category_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Division.id', 'Product.product_category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('Division.id', 'Product.product_category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'brand') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Division.id', 'Product.brand_id');
								$group = array('Division.id', 'Product.brand_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Division.id', 'Product.brand_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('Division.id', 'Product.brand_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'outlet_type') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Division.id', 'Outlet.category_id');
								$group = array('Division.id', 'Outlet.category_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Division.id', 'Outlet.category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('Division.id', 'Outlet.category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'national') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Division.id');
								$group = array('Division.id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Division.id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('Division.id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							}
						}


						if ($request_data['SalesAnalysisReports']['rows'] == 'district') {
							if ($request_data['SalesAnalysisReports']['columns'] == 'product') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'District.id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group = array('District.id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');

								if ($request_data['SalesAnalysisReports']['program_type_id']) {
									$fields2 = array('ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS volume,
									ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
									sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
									COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'District.id', 'Outlet.category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
									$group2 = array('District.id', 'Outlet.category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
								}
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'product_virtual') {
								$this->Memo->MemoDetail->virtualFields['product_id'] = "CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END";
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'District.id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END as MemoDetail__product_id ', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group = array('District.id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END ', 'cyp_cal', 'cyp');

								if ($request_data['SalesAnalysisReports']['program_type_id']) {
									$fields2 = array('ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS volume,
									ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
									sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
									COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'District.id', 'Outlet.category_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END as MemoDetail__product_id ', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
									$group2 = array('District.id', 'Outlet.category_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END', 'cyp_cal', 'cyp');
								}
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'category') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'District.id', 'Product.product_category_id');
								$group = array('District.id', 'Product.product_category_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'District.id', 'Product.product_category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('District.id', 'Product.product_category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'brand') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'District.id', 'Product.brand_id');
								$group = array('District.id', 'Product.brand_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'District.id', 'Product.brand_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('District.id', 'Product.brand_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'outlet_type') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'District.id', 'Outlet.category_id');
								$group = array('District.id', 'Outlet.category_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'District.id', 'Outlet.category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('District.id', 'Outlet.category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'national') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'District.id');
								$group = array('District.id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'District.id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('District.id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							}
						}


						if ($request_data['SalesAnalysisReports']['rows'] == 'thana') {
							if ($request_data['SalesAnalysisReports']['columns'] == 'product') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Thana.id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group = array('Thana.id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');

								if ($request_data['SalesAnalysisReports']['program_type_id']) {
									$fields2 = array('ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS volume,
									ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
									sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
									COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Thana.id', 'Outlet.category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
									$group2 = array('Thana.id', 'Outlet.category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
								}
							}
							if ($request_data['SalesAnalysisReports']['columns'] == 'product_virtual') {
								$this->Memo->MemoDetail->virtualFields['product_id'] = "CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END";
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Thana.id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END as MemoDetail__product_id    ', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group = array('Thana.id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END', 'cyp_cal', 'cyp');

								if ($request_data['SalesAnalysisReports']['program_type_id']) {
									$fields2 = array('ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS volume,
									ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
									sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
									COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Thana.id', 'Outlet.category_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END as MemoDetail__product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
									$group2 = array('Thana.id', 'Outlet.category_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END', 'cyp_cal', 'cyp');
								}
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'category') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Thana.id', 'Product.product_category_id');
								$group = array('Thana.id', 'Product.product_category_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Thana.id', 'Product.product_category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('Thana.id', 'Product.product_category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'brand') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Thana.id', 'Product.brand_id');
								$group = array('Thana.id', 'Product.brand_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Thana.id', 'Product.brand_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('Thana.id', 'Product.brand_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'outlet_type') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Thana.id', 'Outlet.category_id');
								$group = array('Thana.id', 'Outlet.category_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Thana.id', 'Outlet.category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('Thana.id', 'Outlet.category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'national') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Thana.id');
								$group = array('Thana.id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Thana.id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('Thana.id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'ProgramOfficer') { //program officer part
							$program_officer_join = array(
								'table' => 'memo_program_officers',
								'alias' => 'MemoProgramOfficer',
								'type' => 'inner',
								'conditions' => 'MemoProgramOfficer.memo_id=Memo.id'
							);
							if ($request_data['SalesAnalysisReports']['columns'] == 'product') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'MemoProgramOfficer.program_officer_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group = array('MemoProgramOfficer.program_officer_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');

								if ($request_data['SalesAnalysisReports']['program_type_id']) {
									$fields2 = array('ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS volume,
									ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
									sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
									COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'MemoProgramOfficer.program_officer_id', 'Outlet.category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
									$group2 = array('MemoProgramOfficer.program_officer_id', 'Outlet.category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
								}
							}
							if ($request_data['SalesAnalysisReports']['columns'] == 'product_virtual') {
								$this->Memo->MemoDetail->virtualFields['product_id'] = "CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END";
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'MemoProgramOfficer.program_officer_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END as MemoDetail__product_id    ', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group = array('MemoProgramOfficer.program_officer_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END', 'cyp_cal', 'cyp');

								if ($request_data['SalesAnalysisReports']['program_type_id']) {
									$fields2 = array('ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS volume,
									ROUND(SUM(
										(ROUND(
												(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
									sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
									COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'MemoProgramOfficer.program_officer_id', 'Outlet.category_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END as MemoDetail__product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
									$group2 = array('MemoProgramOfficer.program_officer_id', 'Outlet.category_id', 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END', 'cyp_cal', 'cyp');
								}
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'category') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'MemoProgramOfficer.program_officer_id', 'Product.product_category_id');
								$group = array('MemoProgramOfficer.program_officer_id', 'Product.product_category_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'MemoProgramOfficer.program_officer_id', 'Product.product_category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('MemoProgramOfficer.program_officer_id', 'Product.product_category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'brand') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'MemoProgramOfficer.program_officer_id', 'Product.brand_id');
								$group = array('MemoProgramOfficer.program_officer_id', 'Product.brand_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'MemoProgramOfficer.program_officer_id', 'Product.brand_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('MemoProgramOfficer.program_officer_id', 'Product.brand_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'outlet_type') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'MemoProgramOfficer.program_officer_id', 'Outlet.category_id');
								$group = array('MemoProgramOfficer.program_officer_id', 'Outlet.category_id');

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'MemoProgramOfficer.program_officer_id', 'Outlet.category_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('MemoProgramOfficer.program_officer_id', 'Outlet.category_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'national') {
								$fields = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'MemoProgramOfficer.program_officer_id',);
								$group = array('MemoProgramOfficer.program_officer_id',);

								$fields2 = array('ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS volume,
								ROUND(SUM(
									(ROUND(
											(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
											(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
										,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
								),2,1) AS bonus,sum(MemoDetail.sales_qty*MemoDetail.discount_amount) AS discount_value,
								sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
								COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'MemoProgramOfficer.program_officer_id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
								$group2 = array('MemoProgramOfficer.program_officer_id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');
							}
						}
						/*End ProgramOfficer*/
						/*  pr($fields);
						pr($group);
						exit; */


						//pr($conditions);
						//exit;

						// $conditions['MemoDetail.price >']=0;
						//pr($conditions);exit;
						if ($request_data['SalesAnalysisReports']['columns'] == 'product_virtual') {

							$product_join_conditions = 'CASE WHEN MemoDetail.virtual_product_id IS null OR MemoDetail.virtual_product_id=0 THEN MemoDetail.product_id ELSE MemoDetail.virtual_product_id END = Product.id';
						} else {

							$product_join_conditions = 'MemoDetail.product_id = Product.id';
						}
						$q_results = $this->Memo->find('all', array(
							'conditions' => $conditions,
							'joins' => array(
								array(
									'alias' => 'MemoDetail',
									'table' => 'memo_details',
									'type' => 'INNER',
									'conditions' => 'Memo.id = MemoDetail.memo_id'
								),
								array(
									'alias' => 'dbp',
									'table' => 'discount_bonus_policies',
									'type' => 'left',
									'conditions' => 'MemoDetail.policy_id = dbp.id'
								),
								array(
									'alias' => 'Product',
									'table' => 'products',
									'type' => 'INNER',
									'conditions' => $product_join_conditions
								),
								array(
									'alias' => 'ProductMeasurement',
									'table' => 'product_measurements',
									'type' => 'LEFT',
									'conditions' => 'Product.id = ProductMeasurement.product_id AND 
												CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
												ELSE 
													MemoDetail.measurement_unit_id
												END =ProductMeasurement.measurement_unit_id'
								),
								array(
									'alias' => 'ProductMeasurementSales',
									'table' => 'product_measurements',
									'type' => 'LEFT',
									'conditions' => '
												Product.id = ProductMeasurementSales.product_id 
												AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
								),
								array(
									'alias' => 'Territory',
									'table' => 'territories',
									'type' => 'INNER',
									'conditions' => 'Memo.territory_id = Territory.id'
								),
								array(
									'alias' => 'Outlet',
									'table' => 'outlets',
									'type' => 'INNER',
									'conditions' => 'Memo.outlet_id = Outlet.id'
								),
								array(
									'alias' => 'Market',
									'table' => 'markets',
									'type' => 'INNER',
									'conditions' => 'Memo.market_id = Market.id'
								),
								array(
									'alias' => 'Thana',
									'table' => 'thanas',
									'type' => 'INNER',
									'conditions' => 'Memo.thana_id = Thana.id'
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
								),
								array(
									'alias' => 'Office',
									'table' => 'offices',
									'type' => 'INNER',
									'conditions' => 'Memo.office_id = Office.id'
								),
								array(
									'alias' => 'ProductionCombination',
									'table' => 'product_combinations_v2',
									'type' => 'LEFT',
									'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
								),
								$program_joins ? $program_joins : '',
								$program_officer_join ? $program_officer_join : ''
							),
							//'fields' => array('Memo.id', 'MemoDetail.id', 'Territory.id', 'Outlet.id', 'Outlet.name', 'Market.id', 'Thana.id', 'District.name', 'Division.name'),
							'fields' => $fields,
							'group' => $group,
							'recursive' => -1
						));
						//echo $this->Memo->getLastQuery();
						//exit;
						/* echo '------------------------------- <br>';*/
						//pr($conditions);

						if ($fields2 && $group2) {
							$q_results2 = $this->Memo->find('all', array(
								'conditions' => $conditions,
								'joins' => array(
									array(
										'alias' => 'MemoDetail',
										'table' => 'memo_details',
										'type' => 'INNER',
										'conditions' => 'Memo.id = MemoDetail.memo_id'
									),
									array(
										'alias' => 'dbp',
										'table' => 'discount_bonus_policies',
										'type' => 'left',
										'conditions' => 'MemoDetail.policy_id = dbp.id'
									),
									array(
										'alias' => 'Product',
										'table' => 'products',
										'type' => 'INNER',
										'conditions' => $product_join_conditions
									),
									array(
										'alias' => 'ProductMeasurement',
										'table' => 'product_measurements',
										'type' => 'LEFT',
										'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														MemoDetail.measurement_unit_id
													END =ProductMeasurement.measurement_unit_id'
									),
									array(
										'alias' => 'ProductMeasurementSales',
										'table' => 'product_measurements',
										'type' => 'LEFT',
										'conditions' => '
													Product.id = ProductMeasurementSales.product_id 
													AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
									),
									array(
										'alias' => 'Territory',
										'table' => 'territories',
										'type' => 'INNER',
										'conditions' => 'Memo.territory_id = Territory.id'
									),
									array(
										'alias' => 'Outlet',
										'table' => 'outlets',
										'type' => 'INNER',
										'conditions' => 'Memo.outlet_id = Outlet.id'
									),
									array(
										'alias' => 'Market',
										'table' => 'markets',
										'type' => 'INNER',
										'conditions' => 'Memo.market_id = Market.id'
									),
									array(
										'alias' => 'Thana',
										'table' => 'thanas',
										'type' => 'INNER',
										'conditions' => 'Memo.thana_id = Thana.id'
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
									),
									array(
										'alias' => 'Office',
										'table' => 'offices',
										'type' => 'INNER',
										'conditions' => 'Memo.office_id = Office.id'
									),
									array(
										'alias' => 'ProductionCombination',
										'table' => 'product_combinations_v2',
										'type' => 'LEFT',
										'conditions' => 'ProductionCombination.id = MemoDetail.product_price_id'
									),
									$program_joins ? $program_joins : '',
									$program_officer_join ? $program_officer_join : ''
								),
								//'fields' => array('Memo.id', 'MemoDetail.id', 'Territory.id', 'Outlet.id', 'Outlet.name', 'Market.id', 'Thana.id', 'District.name', 'Division.name'),
								'fields' => $fields2,
								'group' => $group2,
								'recursive' => -1
							));
						}

						//pr($q_results2);
						//exit;

						//}
						//}
					}

					/* echo $this->Memo->getLastQuery();
					//echo '<pre>'; print_r($q_results);exit;
					pr($q_results2);
					pr($q_results);
					exit;  */

					$results = array();
					$results2 = array();
					if ($request_data['SalesAnalysisReports']['columns'] == 'product' || $request_data['SalesAnalysisReports']['columns'] == 'product_virtual') {
						if ($request_data['SalesAnalysisReports']['rows'] == 'so') {
							foreach ($q_results as $q_result) {
								$results[$q_result['Memo']['sales_person_id']][$q_result['MemoDetail']['product_id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => $q_result[0]['cyp'],
									'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'territory') {
							foreach ($q_results as $q_result) {
								$results[$q_result['0']['territory_id']][$q_result['MemoDetail']['product_id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => $q_result[0]['cyp'],
									'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'area') {
							foreach ($q_results as $q_result) {
								$results[$q_result['Memo']['office_id']][$q_result['MemoDetail']['product_id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => $q_result[0]['cyp'],
									'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'region') {
							foreach ($q_results as $q_result) {
								$results[$q_result['Office']['parent_office_id']][$q_result['MemoDetail']['product_id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => $q_result[0]['cyp'],
									'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}


						if ($request_data['SalesAnalysisReports']['rows'] == 'division') {
							foreach ($q_results as $q_result) {
								$results[$q_result['Division']['id']][$q_result['MemoDetail']['product_id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => $q_result[0]['cyp'],
									'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}


						if ($request_data['SalesAnalysisReports']['rows'] == 'district') {
							foreach ($q_results as $q_result) {
								$results[$q_result['District']['id']][$q_result['MemoDetail']['product_id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => $q_result[0]['cyp'],
									'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'thana') {
							foreach ($q_results as $q_result) {
								$results[$q_result['Thana']['id']][$q_result['MemoDetail']['product_id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => $q_result[0]['cyp'],
									'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'ProgramOfficer') {
							foreach ($q_results as $q_result) {
								$results[$q_result['MemoProgramOfficer']['program_officer_id']][$q_result['MemoDetail']['product_id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => $q_result[0]['cyp'],
									'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}
					} elseif ($request_data['SalesAnalysisReports']['columns'] == 'category') {
						if ($request_data['SalesAnalysisReports']['rows'] == 'so') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['Memo']['sales_person_id']][$q_result2['Product']['product_category_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Product']['product_category_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							//pr($results2);
							//exit;

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								$sales_qty = 0;
								$bonus_qty = 0;
								foreach ($results2[$q_result['Memo']['sales_person_id']][$q_result['Product']['product_category_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);
									$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
									$base_qty_bonus = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);
									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;

									$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
									$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
								}

								$results[$q_result['Memo']['sales_person_id']][$q_result['Product']['product_category_id']] = array(
									//'volume' => $q_result[0]['volume'],
									'volume' => $sales_qty,
									'bonus' => $bonus_qty,
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
								//break;
							}

							//pr($results);
							//exit;
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'territory') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['0']['territory_id']][$q_result2['Product']['product_category_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Product']['product_category_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								$sales_qty = 0;
								$bonus_qty = 0;
								foreach ($results2[$q_result['0']['territory_id']][$q_result['Product']['product_category_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

									$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
									$base_qty_bonus = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;

									$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
									$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
								}

								$results[$q_result['0']['territory_id']][$q_result['Product']['product_category_id']] = array(
									//'volume' => $q_result[0]['volume'],
									'volume' => $sales_qty,
									'bonus' => $bonus_qty,
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'area') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['Memo']['office_id']][$q_result2['Product']['product_category_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Product']['product_category_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								$sales_qty = 0;
								$bonus_qty = 0;
								foreach ($results2[$q_result['Memo']['office_id']][$q_result['Product']['product_category_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

									$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
									$base_qty_bonus = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;

									$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
									$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
								}

								$results[$q_result['Memo']['office_id']][$q_result['Product']['product_category_id']] = array(
									//'volume' => $q_result[0]['volume'],
									'volume' => $sales_qty,
									'bonus' => $bonus_qty,
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'region') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['Office']['parent_office_id']][$q_result2['Product']['product_category_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Product']['product_category_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								$sales_qty = 0;
								$bonus_qty = 0;
								foreach ($results2[$q_result['Office']['parent_office_id']][$q_result['Product']['product_category_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

									$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
									$base_qty_bonus = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;

									$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
									$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
								}

								$results[$q_result['Office']['parent_office_id']][$q_result['Product']['product_category_id']] = array(
									//'volume' => $q_result[0]['volume'],
									'volume' => $sales_qty,
									'bonus' => $bonus_qty,
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'division') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['Division']['id']][$q_result2['Product']['product_category_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Product']['product_category_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								$sales_qty = 0;
								$bonus_qty = 0;
								foreach ($results2[$q_result['Division']['id']][$q_result['Product']['product_category_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

									$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
									$base_qty_bonus = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;

									$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
									$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
								}

								$results[$q_result['Division']['id']][$q_result['Product']['product_category_id']] = array(
									//'volume' => $q_result[0]['volume'],
									'volume' => $sales_qty,
									'bonus' => $bonus_qty,
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'district') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['District']['id']][$q_result2['Product']['product_category_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Product']['product_category_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								$sales_qty = 0;
								$bonus_qty = 0;
								foreach ($results2[$q_result['District']['id']][$q_result['Product']['product_category_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

									$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
									$base_qty_bonus = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;

									$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
									$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
								}

								$results[$q_result['District']['id']][$q_result['Product']['product_category_id']] = array(
									//'volume' => $q_result[0]['volume'],
									'volume' => $sales_qty,
									'bonus' => $bonus_qty,
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}


						if ($request_data['SalesAnalysisReports']['rows'] == 'thana') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['Thana']['id']][$q_result2['Product']['product_category_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Product']['product_category_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								$sales_qty = 0;
								$bonus_qty = 0;
								foreach ($results2[$q_result['Thana']['id']][$q_result['Product']['product_category_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

									$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
									$base_qty_bonus = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;

									$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
									$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
								}

								$results[$q_result['Thana']['id']][$q_result['Product']['product_category_id']] = array(
									//'volume' => $q_result[0]['volume'],
									'volume' => $sales_qty,
									'bonus' => $bonus_qty,
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}
						if ($request_data['SalesAnalysisReports']['rows'] == 'ProgramOfficer') {
							
							
							
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['MemoProgramOfficer']['program_officer_id']][$q_result2['Product']['product_category_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Product']['product_category_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}
							
							

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								$sales_qty = 0;
								$bonus_qty = 0;
								
								
								//echo'<pre>q';print_r($q_result);
								
								
								foreach ($results2[$q_result['MemoProgramOfficer']['program_officer_id']][$q_result['Product']['product_category_id']] as $cyp_result) {
									//pr($cyp_result);
									
									
									
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);
									
									//echo'<pre>r';print_r($pro_volume);exit;
									
									$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
									$base_qty_bonus = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;

									$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
									$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
								}
								
								
								//echo'<pre>';print_r($q_result);
								
								$results[$q_result['MemoProgramOfficer']['program_officer_id']][$q_result['Product']['product_category_id']] = array(
									//'volume' => $q_result[0]['volume'],
									'volume' => $sales_qty,
									'bonus' => $bonus_qty,
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
								
								//echo'<pre>';print_r($results);exit;
							}
						}
					} elseif ($request_data['SalesAnalysisReports']['columns'] == 'brand') {
						if ($request_data['SalesAnalysisReports']['rows'] == 'so') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['Memo']['sales_person_id']][$q_result2['Product']['brand_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Product']['brand_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								$sales_qty = 0;
								$bonus_qty = 0;
								foreach ($results2[$q_result['Memo']['sales_person_id']][$q_result['Product']['brand_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

									$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
									$base_qty_bonus = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;


									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;

									$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
									$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
								}

								$results[$q_result['Memo']['sales_person_id']][$q_result['Product']['brand_id']] = array(
									//'volume' => $q_result[0]['volume'],
									'volume' => $sales_qty,
									'bonus' => $bonus_qty,
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
								//break;
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'territory') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['0']['territory_id']][$q_result2['Product']['brand_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Product']['brand_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								$sales_qty = 0;
								$bonus_qty = 0;
								foreach ($results2[$q_result['0']['territory_id']][$q_result['Product']['brand_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

									$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
									$base_qty_bonus = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;

									$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
									$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
								}

								$results[$q_result['0']['territory_id']][$q_result['Product']['brand_id']] = array(
									//'volume' => $q_result[0]['volume'],
									'volume' => $sales_qty,
									'bonus' => $bonus_qty,
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'area') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['Memo']['office_id']][$q_result2['Product']['brand_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Product']['brand_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								$sales_qty = 0;
								$bonus_qty = 0;
								foreach ($results2[$q_result['Memo']['office_id']][$q_result['Product']['brand_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

									$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
									$base_qty_bonus = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;

									$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
									$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
								}

								$results[$q_result['Memo']['office_id']][$q_result['Product']['brand_id']] = array(
									//'volume' => $q_result[0]['volume'],
									'volume' => $sales_qty,
									'bonus' => $bonus_qty,
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'region') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['Office']['parent_office_id']][$q_result2['Product']['brand_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Product']['brand_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								$sales_qty = 0;
								$bonus_qty = 0;
								foreach ($results2[$q_result['Office']['parent_office_id']][$q_result['Product']['brand_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

									$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
									$base_qty_bonus = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;

									$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
									$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
								}

								$results[$q_result['Office']['parent_office_id']][$q_result['Product']['brand_id']] = array(
									//'volume' => $q_result[0]['volume'],
									'volume' => $sales_qty,
									'bonus' => $bonus_qty,
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'division') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['Division']['id']][$q_result2['Product']['brand_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Product']['brand_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								$sales_qty = 0;
								$bonus_qty = 0;
								foreach ($results2[$q_result['Division']['id']][$q_result['Product']['brand_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

									$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
									$base_qty_bonus = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;

									$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
									$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
								}

								$results[$q_result['Division']['id']][$q_result['Product']['brand_id']] = array(
									//'volume' => $q_result[0]['volume'],
									'volume' => $sales_qty,
									'bonus' => $bonus_qty,
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'district') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['District']['id']][$q_result2['Product']['brand_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Product']['brand_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								$sales_qty = 0;
								$bonus_qty = 0;
								foreach ($results2[$q_result['District']['id']][$q_result['Product']['brand_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

									$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
									$base_qty_bonus = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;

									$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
									$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
								}

								$results[$q_result['District']['id']][$q_result['Product']['brand_id']] = array(
									//'volume' => $q_result[0]['volume'],
									'volume' => $sales_qty,
									'bonus' => $bonus_qty,
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}


						if ($request_data['SalesAnalysisReports']['rows'] == 'thana') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['Thana']['id']][$q_result2['Product']['brand_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Product']['brand_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								$sales_qty = 0;
								$bonus_qty = 0;
								foreach ($results2[$q_result['Thana']['id']][$q_result['Product']['brand_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

									$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
									$base_qty_bonus = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;

									$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
									$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
								}

								$results[$q_result['Thana']['id']][$q_result['Product']['brand_id']] = array(
									//'volume' => $q_result[0]['volume'],
									'volume' => $sales_qty,
									'bonus' => $bonus_qty,
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}
						if ($request_data['SalesAnalysisReports']['rows'] == 'ProgramOfficer') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['MemoProgramOfficer']['program_officer_id']][$q_result2['Product']['brand_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Product']['brand_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								$sales_qty = 0;
								$bonus_qty = 0;
								foreach ($results2[$q_result['MemoProgramOfficer']['program_officer_id']][$q_result['Product']['brand_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

									$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
									$base_qty_bonus = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;

									$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
									$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
								}

								$results[$q_result['MemoProgramOfficer']['program_officer_id']][$q_result['Product']['brand_id']] = array(
									//'volume' => $q_result[0]['volume'],
									'volume' => $sales_qty,
									'bonus' => $bonus_qty,
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}
					} elseif ($request_data['SalesAnalysisReports']['columns'] == 'outlet_type') {
						if ($request_data['SalesAnalysisReports']['rows'] == 'so') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['Memo']['sales_person_id']][$q_result2['Outlet']['category_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									//'value' => $q_result2[0]['value'],
									//'ec' => $q_result2[0]['ec'],
									//'oc' => $q_result2[0]['oc'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Outlet']['category_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}


							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								$sales_qty = 0;
								$bonus_qty = 0;
								foreach ($results2[$q_result['Memo']['sales_person_id']][$q_result['Outlet']['category_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;

									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

									$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;

									$base_qty_bonus = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;


									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;

									$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
									$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
								}

								$results[$q_result['Memo']['sales_person_id']][$q_result['Outlet']['category_id']] = array(
									//'volume' => $q_result[0]['volume'],
									'volume' => $sales_qty,
									'bonus' => $bonus_qty,
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
								//break;
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'territory') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['0']['territory_id']][$q_result2['Outlet']['category_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Outlet']['category_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								foreach ($results2[$q_result['0']['territory_id']][$q_result['Outlet']['category_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);
									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;
								}

								$results[$q_result['0']['territory_id']][$q_result['Outlet']['category_id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'area') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['Memo']['office_id']][$q_result2['Outlet']['category_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Outlet']['category_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								foreach ($results2[$q_result['Memo']['office_id']][$q_result['Outlet']['category_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);
									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;
								}

								$results[$q_result['Memo']['office_id']][$q_result['Outlet']['category_id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}
						if ($request_data['SalesAnalysisReports']['rows'] == 'region') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['Office']['parent_office_id']][$q_result2['Outlet']['category_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Outlet']['category_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								foreach ($results2[$q_result['Office']['parent_office_id']][$q_result['Outlet']['category_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);
									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;
								}

								$results[$q_result['Office']['parent_office_id']][$q_result['Outlet']['category_id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}
						if ($request_data['SalesAnalysisReports']['rows'] == 'division') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['Division']['id']][$q_result2['Outlet']['category_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Outlet']['category_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								foreach ($results2[$q_result['Division']['id']][$q_result['Outlet']['category_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);
									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;
								}

								$results[$q_result['Division']['id']][$q_result['Outlet']['category_id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'district') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['District']['id']][$q_result2['Outlet']['category_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Outlet']['category_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								foreach ($results2[$q_result['District']['id']][$q_result['Outlet']['category_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);
									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;
								}

								$results[$q_result['District']['id']][$q_result['Outlet']['category_id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}


						if ($request_data['SalesAnalysisReports']['rows'] == 'thana') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['Thana']['id']][$q_result2['Outlet']['category_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Outlet']['category_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								foreach ($results2[$q_result['Thana']['id']][$q_result['Outlet']['category_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);
									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;
								}

								$results[$q_result['Thana']['id']][$q_result['Outlet']['category_id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}
						if ($request_data['SalesAnalysisReports']['rows'] == 'ProgramOfficer') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['MemoProgramOfficer']['program_officer_id']][$q_result2['Outlet']['category_id']][$q_result2['MemoDetail']['product_id']] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'category_id' => $q_result2['Outlet']['category_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;
								foreach ($results2[$q_result['MemoProgramOfficer']['program_officer_id']][$q_result['Outlet']['category_id']] as $cyp_result) {
									//pr($cyp_result);
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);
									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;
								}

								$results[$q_result['MemoProgramOfficer']['program_officer_id']][$q_result['Outlet']['category_id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}
					} elseif ($request_data['SalesAnalysisReports']['columns'] == 'national') {
						if ($request_data['SalesAnalysisReports']['rows'] == 'so') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['Memo']['sales_person_id']][] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'sales_person_id' => $q_result2['Memo']['sales_person_id'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;

								foreach ($results2[$q_result['Memo']['sales_person_id']] as $cyp_result) {
									//pr($cyp_result);

									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);
									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;
								}

								$results[$q_result['Memo']['sales_person_id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
							//pr($results);
							//exit;
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'territory') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['0']['territory_id']][] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;

								foreach ($results2[$q_result['0']['territory_id']] as $cyp_result) {
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);
									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;
								}

								$results[$q_result['0']['territory_id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'area') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['Memo']['office_id']][] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;

								foreach ($results2[$q_result['Memo']['office_id']] as $cyp_result) {
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);
									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;
								}

								$results[$q_result['Memo']['office_id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}
						if ($request_data['SalesAnalysisReports']['rows'] == 'region') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['Office']['parent_office_id']][] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;

								foreach ($results2[$q_result['Office']['parent_office_id']] as $cyp_result) {
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);
									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;
								}

								$results[$q_result['Office']['parent_office_id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'division') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['Division']['id']][] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;

								foreach ($results2[$q_result['Division']['id']] as $cyp_result) {
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);
									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;
								}

								$results[$q_result['Division']['id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'district') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['District']['id']][] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;

								foreach ($results2[$q_result['District']['id']] as $cyp_result) {
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);
									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;
								}

								$results[$q_result['District']['id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}

						if ($request_data['SalesAnalysisReports']['rows'] == 'thana') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['Thana']['id']][] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;

								foreach ($results2[$q_result['Thana']['id']] as $cyp_result) {
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);
									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;
								}

								$results[$q_result['Thana']['id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}
						if ($request_data['SalesAnalysisReports']['rows'] == 'ProgramOfficer') {
							foreach ($q_results2 as $q_result2) {
								$results2[$q_result2['MemoProgramOfficer']['program_officer_id']][] = array(
									'volume' => $q_result2[0]['volume'],
									'bonus' => $q_result2[0]['bonus'],
									'cyp' => $q_result2[0]['cyp'],
									'cyp_v' => $q_result2[0]['cyp_v'],
									'product_id' => $q_result2['MemoDetail']['product_id'],
								);
							}

							foreach ($q_results as $q_result) {
								$cyp = 0;
								$total_cyp = 0;

								foreach ($results2[$q_result['MemoProgramOfficer']['program_officer_id']] as $cyp_result) {
									$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
									$base_qty = $this->unit_convert_from_global($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);
									$cyp_s = $cyp_result['cyp'] ? $cyp_result['cyp'] : 0;
									$cyp_v = $cyp_result['cyp_v'] ? $cyp_result['cyp_v'] : 0;

									if ($cyp_s == '/') {
										@$cyp = $base_qty / $cyp_v;
									} elseif ($cyp_s == '*') {
										@$cyp = $base_qty * $cyp_v;
									} elseif ($cyp_s == '-') {
										@$cyp = $base_qty - $cyp_v;
									} elseif ($cyp_s == '+') {
										@$cyp = $base_qty + $cyp_v;
									} else {
										$cyp = 0;
									}
									$total_cyp += $cyp;
								}
								
								

								$results[$q_result['MemoProgramOfficer']['program_officer_id']] = array(
									'volume' => $q_result[0]['volume'],
									'bonus' => $q_result[0]['bonus'],
									'value' => $q_result[0]['value'],
									'discount_value' => $q_result[0]['discount_value'],
									'ec' => $q_result[0]['ec'],
									'oc' => $q_result[0]['oc'],
									'cyp' => sprintf("%01.2f", $total_cyp),
									//'cyp_v' => $q_result[0]['cyp_v'],
								);
							}
						}
					}

					//pr($q_results);

					if ($rows == 'month') {
						$results = $q_results;
						$results2 = $q_results2;
					}

					if ($rows == 'national') {

						$results = $q_results;
						$results2 = $q_results2;
						$n_results = array();
						foreach ($results[0] as $r_val) {
							//pr($result_val);

							if ($request_data['SalesAnalysisReports']['columns'] == 'category') {
								$n_results[$r_val['Product']['product_category_id']] = array(
									'volume' => $r_val[0]['volume'],
									'value' => $r_val[0]['value'],
									'bonus' => $r_val[0]['bonus'],
									'discount_value' => $r_val[0]['discount_value'],
									'ec' => $r_val[0]['ec'],
									'oc' => $r_val[0]['oc'],
									'product_category_id' => $r_val['Product']['product_category_id'],
								);
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'brand') {
								$n_results[$r_val['Product']['brand_id']] = array(
									'volume' => $r_val[0]['volume'],
									'value' => $r_val[0]['value'],
									'bonus' => $r_val[0]['bonus'],
									'discount_value' => $r_val[0]['discount_value'],
									'ec' => $r_val[0]['ec'],
									'oc' => $r_val[0]['oc'],
									'brand_id' => $r_val['Product']['brand_id'],
								);
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'outlet_type') {
								$n_results[$r_val['Outlet']['category_id']] = array(
									'volume' => $r_val[0]['volume'],
									'value' => $r_val[0]['value'],
									'bonus' => $r_val[0]['bonus'],
									'discount_value' => $r_val[0]['discount_value'],
									'ec' => $r_val[0]['ec'],
									'oc' => $r_val[0]['oc'],
									'outlet_category_id' => $r_val['Outlet']['category_id'],
								);
							}
							/*else
							{
								$n_results[$r_val['Outlet']['category_id']] = array(
									'volume' => $r_val[0]['volume'],
									'value' => $r_val[0]['value'],
									'ec' => $r_val[0]['ec'],
									'oc' => $r_val[0]['oc'],
									//'outlet_category_id'=> $r_val['Outlet']['category_id'],
								);
							}*/
						}

						//echo '<pre>---';pr($n_results);exit;
					}
					//echo'<pre>';print_r($results);
					//pr($results);
					//pr($results2);
					//exit;
					
					//echo'<pre>';print_r($results);exit;

					$this->set(compact('results'));


					//FOR OUTPUT
					$indicators_array = array();
					if (empty($request_data['SalesAnalysisReports']['indicators'])) {
						foreach ($indicators as $key => $val) {
							array_push($indicators_array, $key);
						}
					} else {
						$indicators_array = $request_data['SalesAnalysisReports']['indicators'];
					}



					$i = 0;
					$output = '';
					$d_val = 0;
					$g_col_total = array();
					$g_total = 0;
					$sub_total = 0;

					//pr($results);
					//exit;

					foreach ($rows_list as $row_key => $row_val) {
						if (isset($results[$row_key]) && $results[$row_key]) {
							$output .= '<tr>';
							$output .= '<td style="text-align:left;">' . str_replace('Sales Office', '', $row_val) . '</td>';

							$c = 0;


							foreach ($columns_list as $col_key => $col_val) {
								foreach ($indicators as $in_key => $in_val) {
									if (in_array($in_key, $indicators_array)) {
										if ($request_data['SalesAnalysisReports']['rows'] == 'national') {
											if ($columns == 'category') {
												$d_val = 0;
												$cyp = 0;
												$total_cyp = 0;
												$sales_qty = 0;
												$bonus_qty = 0;
												foreach ($results2[$row_key] as $result2) {
													if ($result2['Product']['product_category_id'] == $col_key) {
														//pr($result2);
														$pro_volume = $result2[0]['volume'];
														$base_qty = $this->unit_convert_from_global($result2['MemoDetail']['product_id'], $product_measurement[$result2['MemoDetail']['product_id']], $pro_volume);

														$pro_bonus = $result2[0]['bonus'];
														$bonus_base_qty = $this->unit_convert_from_global($result2['MemoDetail']['product_id'], $product_measurement[$result2['MemoDetail']['product_id']], $pro_bonus);

														$cyp_s = $result2[0]['cyp'];
														$cyp_v = $result2[0]['cyp_v'];

														if ($cyp_s == '/') {
															@$cyp = $base_qty / $cyp_v;
														} elseif ($cyp_s == '*') {
															@$cyp = $base_qty * $cyp_v;
														} elseif ($cyp_s == '-') {
															@$cyp = $base_qty - $cyp_v;
														} elseif ($cyp_s == '+') {
															@$cyp = $base_qty + $cyp_v;
														} else {
															$cyp = 0;
														}
														$total_cyp += $cyp;

														//$sales_qty+= ($unit_type==1)?$pro_volume:$this->unit_convert_from_global($result2['MemoDetail']['product_id'], $product_measurement[$result2['MemoDetail']['product_id']], $pro_volume);
														$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
														$bonus_qty += ($unit_type == 1) ? $pro_bonus : $bonus_base_qty;
													}
												}
												//echo $sales_qty;
												//exit;



												if (@$n_results[$col_key]) {
													if ($in_key == 'cyp') {
														$d_val = sprintf("%01.2f", $total_cyp);
													} elseif ($in_key == 'volume') {
														//@$d_val = ($unit_type==1)?$sales_qty:$this->unit_convert_from_global($col_key, $product_measurement[$col_key], $sales_qty);
														$d_val = @sprintf("%01.2f", $sales_qty);
													} elseif ($in_key == 'bonus') {
														//@$d_val = ($unit_type==1)?$sales_qty:$this->unit_convert_from_global($col_key, $product_measurement[$col_key], $sales_qty);
														$d_val = @sprintf("%01.2f", $bonus_qty);
													} else {
														//$d_val = '1.00';
														$d_val = @sprintf("%01.2f", $n_results[$col_key][$in_key]);
													}
												}
											} elseif ($columns == 'brand') {
												$d_val = 0;
												$cyp = 0;
												$total_cyp = 0;
												$sales_qty = 0;
												$bonus_qty = 0;
												foreach ($results2[$row_key] as $result2) {
													if ($result2['Product']['brand_id'] == $col_key) {
														//pr($result2);
														$pro_volume = $result2[0]['volume'];
														$base_qty = $this->unit_convert_from_global($result2['MemoDetail']['product_id'], $product_measurement[$result2['MemoDetail']['product_id']], $pro_volume);

														$pro_bonus = $result2[0]['bonus'];
														$bonus_base_qty = $this->unit_convert_from_global($result2['MemoDetail']['product_id'], $product_measurement[$result2['MemoDetail']['product_id']], $pro_bonus);

														$cyp_s = $result2[0]['cyp'];
														$cyp_v = $result2[0]['cyp_v'];

														if ($cyp_s == '/') {
															@$cyp = $base_qty / $cyp_v;
														} elseif ($cyp_s == '*') {
															@$cyp = $base_qty * $cyp_v;
														} elseif ($cyp_s == '-') {
															@$cyp = $base_qty - $cyp_v;
														} elseif ($cyp_s == '+') {
															@$cyp = $base_qty + $cyp_v;
														} else {
															$cyp = 0;
														}
														$total_cyp += $cyp;

														$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
														$bonus_qty += ($unit_type == 1) ? $pro_bonus : $bonus_base_qty;
													}
												}

												if (@$n_results[$col_key]) {
													if ($in_key == 'cyp') {
														$d_val = sprintf("%01.2f", $total_cyp);
													} elseif ($in_key == 'volume') {
														//@$d_val = ($unit_type==1)?$sales_qty:$this->unit_convert_from_global($col_key, $product_measurement[$col_key], $sales_qty);
														$d_val = @sprintf("%01.2f", $sales_qty);
													} elseif ($in_key == 'bonus') {
														//@$d_val = ($unit_type==1)?$sales_qty:$this->unit_convert_from_global($col_key, $product_measurement[$col_key], $sales_qty);
														$d_val = @sprintf("%01.2f", $bonus_qty);
													} else {
														//$d_val = '1.00';
														$d_val = @sprintf("%01.2f", $n_results[$col_key][$in_key]);
													}
												}
											} elseif ($columns == 'outlet_type') {
												$d_val = 0;
												$cyp = 0;
												$total_cyp = 0;
												foreach ($results2[$row_key] as $result2) {
													if ($result2['Outlet']['category_id'] == $col_key) {
														//pr($result2);
														$pro_volume = $result2[0]['volume'];
														$base_qty = $this->unit_convert_from_global($result2['MemoDetail']['product_id'], $product_measurement[$result2['MemoDetail']['product_id']], $pro_volume);

														$cyp_s = $result2[0]['cyp'];
														$cyp_v = $result2[0]['cyp_v'];

														if ($cyp_s == '/') {
															@$cyp = $base_qty / $cyp_v;
														} elseif ($cyp_s == '*') {
															@$cyp = $base_qty * $cyp_v;
														} elseif ($cyp_s == '-') {
															@$cyp = $base_qty - $cyp_v;
														} elseif ($cyp_s == '+') {
															@$cyp = $base_qty + $cyp_v;
														} else {
															$cyp = 0;
														}
														$total_cyp += $cyp;
													}
												}


												if (@$n_results[$col_key]) {
													if ($in_key == 'cyp') {
														$d_val = sprintf("%01.2f", $total_cyp);
													} else {
														//$d_val = '1.00';
														$d_val = @sprintf("%01.2f", $n_results[$col_key][$in_key]);
													}
												}
											} elseif ($columns == 'national') {
												$cyp = 0;
												$total_cyp = 0;

												foreach ($results2[0] as $result2) {
													$pro_volume = $result2[0]['volume'];
													$base_qty = $this->unit_convert_from_global($result2['MemoDetail']['product_id'], $product_measurement[$result2['MemoDetail']['product_id']], $pro_volume);
													$cyp_s = $result2[0]['cyp'];
													$cyp_v = $result2[0]['cyp_v'];

													if ($cyp_s == '/') {
														@$cyp = $base_qty / $cyp_v;
													} elseif ($cyp_s == '*') {
														@$cyp = $base_qty * $cyp_v;
													} elseif ($cyp_s == '-') {
														@$cyp = $base_qty - $cyp_v;
													} elseif ($cyp_s == '+') {
														@$cyp = $base_qty + $cyp_v;
													} else {
														$cyp = 0;
													}
													$total_cyp += $cyp;
												}
												//echo $total_cyp;
												//exit;

												if ($in_key == 'cyp') {
													$d_val = sprintf("%01.2f", $total_cyp);
												} else {
													//$d_val = '0.00';
													$d_val = @sprintf("%01.2f", $results[0][0][0][$in_key]);
												}

												//$d_val = 0.00;
											} else {
												//echo $c.'<br>';
												$d_val = '0.00';

												$total = count($results[0]);

												for ($n = 0; $n < $total; $n++) {
													//echo $n.'<br>';
													if (@$results[0][$n]['MemoDetail']['product_id'] == $col_key) {
														$sales_qty = @$results[0][$n][0]['volume'];
														$pro_qty = @$sales_qty;
														$base_qty = $this->unit_convert_from_global($col_key, $product_measurement[$col_key], $sales_qty);

														$pro_bonus = @$results[0][$n][0]['bonus'];
														$bonus_base_qty = $this->unit_convert_from_global($col_key, $product_measurement[$col_key], $pro_bonus);

														if ($in_key == 'cyp') {
															$cyp_cal = @$results[0][$n][0]['cyp'];
															$cyp_val = @$results[0][$n][0]['cyp_v'];



															if ($cyp_cal && $cyp_val) {
																if ($cyp_cal == '*') {
																	$d_val = @sprintf("%01.2f", $base_qty * $cyp_val);
																}
																if ($cyp_cal == '/') {
																	$d_val = @sprintf("%01.2f", $base_qty / $cyp_val);
																}
																if ($cyp_cal == '-') {
																	$d_val = @sprintf("%01.2f", $base_qty - $cyp_val);
																}
																if ($cyp_cal == '+') {
																	$d_val = @sprintf("%01.2f", $base_qty + $cyp_val);
																}
															}
														} elseif ($in_key == 'volume') {
															$d_val = ($unit_type == 1) ? $sales_qty : $base_qty;
															$d_val = @sprintf("%01.2f", $d_val);
														} elseif ($in_key == 'bonus') {
															$d_val = ($unit_type == 1) ? $pro_bonus : $bonus_base_qty;
															$d_val = @sprintf("%01.2f", $d_val);
														} else {
															$d_val = @sprintf("%01.2f", $results[0][$n][0][$in_key]);
														}
													}
												}
												//exit;
											}
										} elseif ($request_data['SalesAnalysisReports']['rows'] == 'month') {
											if ($columns == 'category') {
												$cyp = 0;
												$total_cyp = 0;
												$sales_qty = 0;
												$bonus_qty = 0;
												foreach ($results2[$row_key] as $result2) {
													if ($result2['Product']['product_category_id'] == $col_key) {
														//pr($result2);

														$pro_volume = $result2[0]['volume'];
														$base_qty = $this->unit_convert_from_global($result2['MemoDetail']['product_id'], $product_measurement[$result2['MemoDetail']['product_id']], $pro_volume);

														$pro_bonus = $result2[0]['bonus'];
														$bonus_base_qty = $this->unit_convert_from_global($result2['MemoDetail']['product_id'], $product_measurement[$result2['MemoDetail']['product_id']], $pro_bonus);

														$cyp_s = $result2[0]['cyp'];
														$cyp_v = $result2[0]['cyp_v'];

														if ($cyp_s == '/') {
															@$cyp = $base_qty / $cyp_v;
														} elseif ($cyp_s == '*') {
															@$cyp = $base_qty * $cyp_v;
														} elseif ($cyp_s == '-') {
															@$cyp = $base_qty - $cyp_v;
														} elseif ($cyp_s == '+') {
															@$cyp = $base_qty + $cyp_v;
														} else {
															$cyp = 0;
														}
														$total_cyp += $cyp;

														$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
														$bonus_qty += ($unit_type == 1) ? $pro_bonus : $bonus_base_qty;
													}
												}

												/*if(@$results[$row_key][$c]['Product']['product_category_id']==$col_key) //comment by naser on 20 Feb 2019
											  {
											   if($in_key=='cyp')
											   {
												  $d_val = sprintf("%01.2f", $total_cyp);
											   }
											   elseif($in_key=='volume')
											   {
												  //@$d_val = ($unit_type==1)?$sales_qty:$this->unit_convert_from_global($col_key, $product_measurement[$col_key], $sales_qty);
												  $d_val = @sprintf("%01.2f", $sales_qty);
											   }
											   else
											   {
												  $d_val = @sprintf("%01.2f", $results[$row_key][$c][0][$in_key]);
											   }
											  }
											  else
											  {
												$d_val = '0.00';
											   }*/
												/*Added By Naser 20 Feb 2019: STart */
												$d_val = '0.00';
												foreach ($results[$row_key] as $result) {
													// pr($result);exit;
													// echo $result['Product']['product_category_id'].'===='.$col_key.'<br>';
													if (@$result['Product']['product_category_id'] == $col_key) {
														if ($in_key == 'cyp') {
															$d_val = sprintf("%01.2f", $total_cyp);
														} elseif ($in_key == 'volume') {
															// echo $col_key.'--'.$sales_qty.'<br>';
															//@$d_val = ($unit_type==1)?$sales_qty:$this->unit_convert_from_global($col_key, $product_measurement[$col_key], $sales_qty);
															$d_val = @sprintf("%01.2f", $sales_qty);
														} elseif ($in_key == 'bonus') {
															// echo $col_key.'--'.$sales_qty.'<br>';
															//@$d_val = ($unit_type==1)?$sales_qty:$this->unit_convert_from_global($col_key, $product_measurement[$col_key], $sales_qty);
															$d_val = @sprintf("%01.2f", $bonus_qty);
														} else {
															$d_val = @sprintf("%01.2f", $result[0][$in_key]);
														}
													}
												}
												/*Added By Naser 20 Feb 2019: END */
											} elseif ($columns == 'brand') {
												$cyp = 0;
												$total_cyp = 0;
												$sales_qty = 0;
												$bonus_qty = 0;
												foreach ($results2[$row_key] as $result2) {
													if ($result2['Product']['brand_id'] == $col_key) {
														//pr($result2);
														$pro_volume = $result2[0]['volume'];
														$base_qty = $this->unit_convert_from_global($result2['MemoDetail']['product_id'], $product_measurement[$result2['MemoDetail']['product_id']], $pro_volume);

														$pro_bonus = $result2[0]['bonus'];
														$bonus_base_qty = $this->unit_convert_from_global($result2['MemoDetail']['product_id'], $product_measurement[$result2['MemoDetail']['product_id']], $pro_bonus);

														$cyp_s = $result2[0]['cyp'];
														$cyp_v = $result2[0]['cyp_v'];

														if ($cyp_s == '/') {
															@$cyp = $base_qty / $cyp_v;
														} elseif ($cyp_s == '*') {
															@$cyp = $base_qty * $cyp_v;
														} elseif ($cyp_s == '-') {
															@$cyp = $base_qty - $cyp_v;
														} elseif ($cyp_s == '+') {
															@$cyp = $base_qty + $cyp_v;
														} else {
															$cyp = 0;
														}
														$total_cyp += $cyp;

														$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
														$bonus_qty += ($unit_type == 1) ? $pro_bonus : $bonus_base_qty;
													}
												}

												if (@$results[$row_key][$c]['Product']['brand_id'] == $col_key) {
													if ($in_key == 'cyp') {
														$d_val = sprintf("%01.2f", $total_cyp);
													} elseif ($in_key == 'volume') {
														//@$d_val = ($unit_type==1)?$sales_qty:$this->unit_convert_from_global($col_key, $product_measurement[$col_key], $sales_qty);
														$d_val = @sprintf("%01.2f", $sales_qty);
													} elseif ($in_key == 'bonus') {
														//@$d_val = ($unit_type==1)?$sales_qty:$this->unit_convert_from_global($col_key, $product_measurement[$col_key], $sales_qty);
														$d_val = @sprintf("%01.2f", $bonus_qty);
													} else {
														$d_val = @sprintf("%01.2f", $results[$row_key][$c][0][$in_key]);
													}
												} else {
													$d_val = '0.00';
												}
											} elseif ($columns == 'outlet_type') {
												$cyp = 0;
												$total_cyp = 0;
												foreach ($results2[$row_key] as $result2) {
													if ($result2['Outlet']['category_id'] == $col_key) {
														//pr($result2);
														$pro_volume = $result2[0]['volume'];
														$base_qty = $this->unit_convert_from_global($result2['MemoDetail']['product_id'], $product_measurement[$result2['MemoDetail']['product_id']], $pro_volume);
														$cyp_s = $result2[0]['cyp'];
														$cyp_v = $result2[0]['cyp_v'];

														if ($cyp_s) {
															if ($cyp_s == '/') {
																@$cyp = $base_qty / $cyp_v;
															} elseif ($cyp_s == '*') {
																@$cyp = $base_qty * $cyp_v;
															} elseif ($cyp_s == '-') {
																@$cyp = $base_qty - $cyp_v;
															} elseif ($cyp_s == '+') {
																@$cyp = $base_qty + $cyp_v;
															}
														} else {
															$cyp = 0;
														}

														$total_cyp += $cyp;
													}
												}
												//echo $total_cyp;
												//exit;
												$d_val = '0.00';
												if (@$results[$row_key][$c]['Outlet']['category_id'] == $col_key) {
													if ($in_key == 'cyp') {
														$d_val = sprintf("%01.2f", $total_cyp);
													} else {
														//$d_val = '0.00';
														$d_val = @sprintf("%01.2f", $results[$row_key][$c][0][$in_key]);
													}
													//$d_val = 0.00;
												}
											} elseif ($columns == 'national') {
												//pr($results2);
												//exit;
												$cyp = 0;
												$total_cyp = 0;
												foreach ($results2[$row_key] as $result2) {
													//pr($result2);
													$pro_volume = $result2[0]['volume'];
													$base_qty = $this->unit_convert_from_global($result2['MemoDetail']['product_id'], $product_measurement[$result2['MemoDetail']['product_id']], $pro_volume);

													$cyp_s = $result2[0]['cyp'];
													$cyp_v = $result2[0]['cyp_v'];

													if ($cyp_s == '/') {
														@$cyp = $base_qty / $cyp_v;
													} elseif ($cyp_s == '*') {
														@$cyp = $base_qty * $cyp_v;
													} elseif ($cyp_s == '-') {
														@$cyp = $base_qty - $cyp_v;
													} elseif ($cyp_s == '+') {
														@$cyp = $base_qty + $cyp_v;
													} else {
														$cyp = 0;
													}
													$total_cyp += $cyp;
												}
												//echo $total_cyp;
												//exit;

												if ($in_key == 'cyp') {
													$d_val = sprintf("%01.2f", $total_cyp);
												} else {
													$d_val = @sprintf("%01.2f", $results[$row_key][$c][0][$in_key]);
												}
											} else {
												$d_val = '0.00';

												$total = count($results[$row_key]);

												for ($n = 0; $n < $total; $n++) {
													//echo $n.'<br>';
													if (@$results[$row_key][$n]['MemoDetail']['product_id'] == $col_key) {
														$sales_qty = @$results[$row_key][$n][0]['volume'];
														$base_qty = $this->unit_convert_from_global($col_key, $product_measurement[$col_key], $sales_qty);

														$bonus_qty = @$results[$row_key][$n][0]['bonus'];
														$bonus_base_qty = $this->unit_convert_from_global($col_key, $product_measurement[$col_key], $bonus_qty);
														if ($in_key == 'cyp') {
															$pro_qty =  @$sales_qty;
															$cyp_cal =  @$results[$row_key][$n][0]['cyp'];
															$cyp_val =  @$results[$row_key][$n][0]['cyp_v'];

															if ($cyp_cal && $cyp_val) {
																if ($cyp_cal == '*') {
																	$d_val =  @sprintf("%01.2f", $base_qty * $cyp_val);
																}
																if ($cyp_cal == '/') {
																	$d_val =  @sprintf("%01.2f", $base_qty / $cyp_val);
																}
																if ($cyp_cal == '-') {
																	$d_val =  @sprintf("%01.2f", $base_qty - $cyp_val);
																}
																if ($cyp_cal == '+') {
																	$d_val =  @sprintf("%01.2f", $base_qty + $cyp_val);
																}
															}
														} elseif ($in_key == 'volume') {
															$d_val = ($unit_type == 1) ? $sales_qty : $base_qty;
															$d_val = @sprintf("%01.2f", $d_val);
														} elseif ($in_key == 'bonus') {
															$d_val = ($unit_type == 1) ? $bonus_qty : $bonus_base_qty;
															$d_val = @sprintf("%01.2f", $d_val);
														} else {
															$d_val = @sprintf("%01.2f", $results[$row_key][$n][0][$in_key]);
														}
													}
												}
												//exit;
											}
										} else {
											if ($request_data['SalesAnalysisReports']['columns'] == 'product' || $request_data['SalesAnalysisReports']['columns'] == 'product_virtual') {
												//$d_val = ($in_key!='cyp')?@sprintf("%01.2f", $results[$row_key][$col_key][$in_key]):'';
												$sales_qty = @$results[$row_key][$col_key]['volume'];
												$base_qty = $this->unit_convert_from_global($col_key, $product_measurement[$col_key], $sales_qty);
												if ($in_key == 'cyp') {
													$d_val = '';
													$pro_qty =  @$sales_qty;

													$cyp_cal =  @$results[$row_key][$col_key]['cyp'];
													$cyp_val =  @$results[$row_key][$col_key]['cyp_v'];

													if ($cyp_cal && $cyp_val) {
														if ($cyp_cal == '*') {
															$d_val =  @sprintf("%01.2f", $base_qty * $cyp_val);
														}
														if ($cyp_cal == '/') {
															$d_val =  @sprintf("%01.2f", $base_qty / $cyp_val);
														}
														if ($cyp_cal == '-') {
															$d_val =  @sprintf("%01.2f", $base_qty - $cyp_val);
														}
														if ($cyp_cal == '+') {
															$d_val =  @sprintf("%01.2f", $base_qty + $cyp_val);
														}
													}
												} elseif ($in_key == 'volume') {
													$d_val = ($unit_type == 1) ? $sales_qty : $base_qty;
													$d_val = @sprintf("%01.2f", $d_val);
												} elseif ($in_key == 'bonus') {
													$sales_qty_bonus = @$results[$row_key][$col_key]['bonus'];
													$base_qty_bonus = $this->unit_convert_from_global($col_key, $product_measurement[$col_key], $sales_qty_bonus);
													$d_val = ($unit_type == 1) ? $sales_qty_bonus : $base_qty_bonus;
													$d_val = @sprintf("%01.2f", $d_val);
												} else {
													$d_val = @sprintf("%01.2f", $results[$row_key][$col_key][$in_key]);
												}
											} elseif ($request_data['SalesAnalysisReports']['columns'] == 'category') {
												$d_val = @sprintf("%01.2f", $results[$row_key][$col_key][$in_key]);
												//$d_val = $col_key;
											} elseif ($request_data['SalesAnalysisReports']['columns'] == 'brand') {
												$d_val = @sprintf("%01.2f", $results[$row_key][$col_key][$in_key]);
												//$d_val = $col_key;
											} elseif ($request_data['SalesAnalysisReports']['columns'] == 'outlet_type') {
												$d_val = @sprintf("%01.2f", $results[$row_key][$col_key][$in_key]);
												//$d_val = $col_key;
											} elseif ($request_data['SalesAnalysisReports']['columns'] == 'national') {
												$d_val = @sprintf("%01.2f", $results[$row_key][$in_key]);
											}
										}

										$output .= '<td><div>' . @$d_val . '</div></td>';

										//@$sub_total+=$d_val;

										@$g_col_total[$col_key][$in_key] += $d_val;
									}
								}

								//echo $i.'<br>';
								$i++;
								$c++;
							}

							//$output.= '<td><div>'.@$total1.'</div></td>';

							$output .= '</tr>';
						}
					}

					//pr($g_col_total);
					//exit;
					//for total cal
					$output .= '<tr><td style="text-align:right;"><b>Total :</b></td>';

					foreach ($columns_list as $col_key => $col_val) {
						foreach ($indicators as $in_key => $in_val) {
							if (in_array($in_key, $indicators_array)) {
								$output .= '<td><b>' . @sprintf("%01.2f", $g_col_total[$col_key][$in_key]) . '</b></td>';
							}
						}
					}

					$output .= '</tr>';


					//echo $output;exit;
					//exit;
					//echo $output;
					$this->set(compact('output'));

					//END OUTPUT


					/*END FOR RESULT QUERY*/
				} else {
					$this->Session->setFlash(__('Please select an indicators!'), 'flash/error');
				}
			}
		}




		//for office list
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

		$this->set(compact('offices', 'outlet_type', 'territories'));
	}

	public function get_office_so_list()
	{
		$view = new View($this);

		$form = $view->loadHelper('Form');

		$office_id = $this->request->data['office_id'];

		$date_from = date('Y-m-d', strtotime($this->request->data['date_from']));
		$date_to = date('Y-m-d', strtotime($this->request->data['date_to']));

		$so_list = array();

		//$conditions = array('SalesPerson.territory_id >' => 0, 'User.user_group_id' => 4, 'User.active' => 1);
		if ($office_id) {
			$conditions = array('User.user_group_id' => 4, 'User.active' => 1);
			$conditions['SalesPerson.office_id'] = $office_id;

			$so_list = array();

			/*$so_list = $this->SalesPerson->find('list', array(
				'conditions' => $conditions,
				'order'=>  array('SalesPerson.name'=>'asc'),
				'recursive'=> 0
			)); */
			$so_list_r = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
				'conditions' => array(
					'SalesPerson.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					//'User.user_group_id' => 4,
					'User.user_group_id' => array(4, 1008),
					'User.active' => 1
				),
				'recursive' => 0
			));
			foreach ($so_list_r as $list_r) {
				$so_list[$list_r['SalesPerson']['id']] = $list_r['SalesPerson']['name'] . ' (' . $list_r['Territory']['name'] . ')';
			}
			//pr($so_list);
			//exit;
			//add old so from territory_assign_histories

			$conditions = array('Territory.office_id' => $office_id, 'TerritoryAssignHistory.assign_type' => 2);
			// $conditions['TerritoryAssignHistory.date BETWEEN ? and ? '] = array($date_from, $date_to);
			$conditions['TerritoryAssignHistory.date >= '] = $date_from;
			//pr($conditions);
			$old_so_list = $this->TerritoryAssignHistory->find('all', array(
				'conditions' => $conditions,
				'order' =>  array('Territory.name' => 'asc'),
				'recursive' => 0
			));
			//pr($old_so_list);
			//exit;
			if ($old_so_list) {
				foreach ($old_so_list as $old_so) {
					$so_list[$old_so['TerritoryAssignHistory']['so_id']] = $old_so['SalesPerson']['name'];
				}
			}
		}
		//pr($so_list);


		/*foreach($so_list_r as $key => $value)
		{
			$so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'].' ('.$value['Territory']['name'].')';
		}*/


		//if($so_list)
		//{
		$form->create('SalesAnalysisReports', array('role' => 'form', 'action' => 'index'));

		echo $form->input('so_id', array('label' => false, 'id' => 'so_id', 'class' => 'form-control so_id', 'required' => false, 'options' => $so_list, 'empty' => '---- All ----'));
		$form->end();

		//}
		//else
		//{
		//echo '';
		//}


		$this->autoRender = false;
	}


	public function get_district_list()
	{

		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		$division_id = $this->request->data['division_id'];


		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		$division_id = $this->request->data['division_id'];
		if ($division_id) {
			$district = $this->District->find('all', array(
				'fields' => array('District.id', 'District.name'),
				'conditions' => array('District.division_id' => $division_id),
				'order' => array('District.name' => 'asc'),
				'recursive' => -1
			));
		}

		$data_array = Set::extract($district, '{n}.District');
		if (!empty($district)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}

	public function get_thana_list()
	{

		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		$district_id = $this->request->data['district_id'];


		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		$district_id = $this->request->data['district_id'];
		$thana = $this->Thana->find('all', array(
			'fields' => array('Thana.id', 'Thana.name'),
			'conditions' => array('Thana.district_id' => $district_id),
			'order' => array('Thana.name' => 'asc'),
			'recursive' => -1
		));

		$data_array = Set::extract($thana, '{n}.Thana');
		if (!empty($thana)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}


	public function get_product_list()
	{
		$view = new View($this);

		$form = $view->loadHelper('Form');

		$product_type = $this->request->data['product_type'];

		$conditions = array(
			'NOT' => array('Product.product_category_id' => 32),
			'is_active' => 1,
			'Product.product_type_id' => 1
		);

		if ($product_type) {
			$conditions['source'] = $product_type;
		}

		//get SO list
		$list = $this->Product->find('all', array(
			'fields' => array('id', 'name'),
			'conditions' => $conditions,
			'recursive' => -1
		));


		$product_list = array();

		foreach ($list as $key => $value) {
			$product_list[$value['Product']['id']] = $value['Product']['name'];
		}


		if ($product_list) {
			$form->create('SalesAnalysisReports', array('role' => 'form', 'action' => 'index'));

			echo $form->input('product_id', array('id' => 'product_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $product_list));


			$form->end();
		} else {
			echo '';
		}


		$this->autoRender = false;
	}














	public function getOutletOCTotal($request_data = array(), $so_id = 0, $outlet_category_id = 0)
	{
		$date_from = date('Y-m-d', strtotime($request_data['SalesAnalysisReports']['date_from']));
		$date_to = date('Y-m-d', strtotime($request_data['SalesAnalysisReports']['date_to']));

		$conditions = array(
			'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
			'Memo.status !=' => 0,
			'Memo.sales_person_id' => $so_id,
			'Outlet.category_id' => $outlet_category_id,
		);

		$result = $this->Memo->find('count', array(
			'conditions' => $conditions,
			'fields' => 'COUNT(DISTINCT Memo.outlet_id) as count',
			'recursive' => 0
		));

		//pr($result);

		return $result;
	}



	//xls download
	public function admin_dwonload_xls()
	{
		$request_data = $this->Session->read('request_data');
		$results = $this->Session->read('results');
		$results2 = $this->Session->read('results2');
		$rows_list = $this->Session->read('rows_list');
		$columns_list = $this->Session->read('columns_list');
		$indicators = $this->Session->read('indicators');

		$columns    = $request_data['SalesAnalysisReports']['columns'];

		$indicators_array = array();
		if (empty($request_data['SalesAnalysisReports']['indicators'])) {
			foreach ($indicators as $key => $val) {
				array_push($indicators_array, $key);
			}
		} else {
			$indicators_array = $request_data['SalesAnalysisReports']['indicators'];
		}

		$header = "";
		$data1 = "";

		$data1 .= ucfirst('By ' . ucfirst($request_data['SalesAnalysisReports']['rows']) . "\t");
		foreach ($columns_list as $col_key => $col_val) {
			foreach ($indicators as $in_key => $in_val) {
				if (in_array($in_key, $indicators_array)) {
					$data1 .= ucfirst($col_val . ' ' . $in_val . "\t");
				}
			}
		}
		$data1 .= "\n";

		$i = 0;

		foreach ($rows_list as $row_key => $row_val) {
			$line = '';

			$line .= str_replace('Sales Office', '', $row_val) . "\t";

			$c = '0';
			foreach ($columns_list as $col_key => $col_val) {
				foreach ($indicators as $in_key => $in_val) {
					if (in_array($in_key, $indicators_array)) {
						if ($request_data['SalesAnalysisReports']['rows'] == 'month' || $request_data['SalesAnalysisReports']['rows'] == 'national') {
							if ($request_data['SalesAnalysisReports']['rows'] == 'national') {
								if ($columns == 'outlet_type') {
									$cyp = 0;
									$total_cyp = 0;
									foreach ($results2[$row_key] as $result2) {
										if ($result2['Outlet']['category_id'] == $col_key) {
											//pr($result2);
											$pro_volume = $result2[0]['volume'];
											$cyp_s = $result2[0]['cyp'];
											$cyp_v = $result2[0]['cyp_v'];

											if ($cyp_s == '/') {
												@$cyp = $pro_volume / $cyp_v;
											} elseif ($cyp_s == '*') {
												@$cyp = $pro_volume * $cyp_v;
											} elseif ($cyp_s == '-') {
												@$cyp = $pro_volume - $cyp_v;
											} elseif ($cyp_s == '+') {
												@$cyp = $pro_volume + $cyp_v;
											} else {
												$cyp = 0;
											}
											$total_cyp += $cyp;
										}
									}
									//echo $total_cyp;
									//exit;

									if (@$results[0][$c]['Outlet']['category_id'] == $col_key) {
										if ($in_key == 'cyp') {
											$d_val = sprintf("%01.2f", $total_cyp);
										} else {
											//$d_val = '0.00';
											$d_val = @sprintf("%01.2f", $results[0][$c][0][$in_key]);
										}


										//$d_val = 0.00;
									}
								} elseif ($columns == 'national') {
									$cyp = 0;
									$total_cyp = 0;

									foreach ($results2[0] as $result2) {
										$pro_volume = $result2[0]['volume'];
										$cyp_s = $result2[0]['cyp'];
										$cyp_v = $result2[0]['cyp_v'];

										if ($cyp_s == '/') {
											@$cyp = $pro_volume / $cyp_v;
										} elseif ($cyp_s == '*') {
											@$cyp = $pro_volume * $cyp_v;
										} elseif ($cyp_s == '-') {
											@$cyp = $pro_volume - $cyp_v;
										} elseif ($cyp_s == '+') {
											@$cyp = $pro_volume + $cyp_v;
										} else {
											$cyp = 0;
										}
										$total_cyp += $cyp;
									}
									//echo $total_cyp;
									//exit;


									if ($in_key == 'cyp') {
										$d_val = sprintf("%01.2f", $total_cyp);
									} else {
										//$d_val = '0.00';
										$d_val = @sprintf("%01.2f", $results[0][0][0][$in_key]);
									}

									//$d_val = 0.00;
								} else {
									if (@$results[0][$c]['MemoDetail']['product_id'] == $col_key) {
										$d_val = '0.00';
										if ($in_key == 'cyp') {
											$pro_qty =  @$results[0][$c][0]['volume'];
											$cyp_cal =  @$results[0][$c][0]['cyp'];
											$cyp_val =  @$results[0][$c][0]['cyp_v'];

											if ($cyp_cal && $cyp_val) {
												if ($cyp_cal == '*') {
													$d_val =  @sprintf("%01.2f", $pro_qty * $cyp_val);
												}
												if ($cyp_cal == '/') {
													$d_val =  @sprintf("%01.2f", $pro_qty / $cyp_val);
												}
												if ($cyp_cal == '-') {
													$d_val =  @sprintf("%01.2f", $pro_qty - $cyp_val);
												}
												if ($cyp_cal == '+') {
													$d_val =  @sprintf("%01.2f", $pro_qty + $cyp_val);
												}
											}
										} else {
											$d_val = @sprintf("%01.2f", $results[0][$c][0][$in_key]);
										}
									} else {
										$d_val = '0.00';
									}
								}
							} else {
								if ($columns == 'outlet_type') {
									$cyp = 0;
									$total_cyp = 0;
									foreach ($results2[$row_key] as $result2) {
										if ($result2['Outlet']['category_id'] == $col_key) {
											//pr($result2);
											$pro_volume = $result2[0]['volume'];
											$cyp_s = $result2[0]['cyp'];
											$cyp_v = $result2[0]['cyp_v'];

											if ($cyp_s == '/') {
												@$cyp = $pro_volume / $cyp_v;
											} elseif ($cyp_s == '*') {
												@$cyp = $pro_volume * $cyp_v;
											} elseif ($cyp_s == '-') {
												@$cyp = $pro_volume - $cyp_v;
											} elseif ($cyp_s == '+') {
												@$cyp = $pro_volume + $cyp_v;
											} else {
												$cyp = 0;
											}
											$total_cyp += $cyp;
										}
									}
									//echo $total_cyp;
									//exit;

									if (@$results[$row_key][$c]['Outlet']['category_id'] == $col_key) {
										if ($in_key == 'cyp') {
											$d_val = sprintf("%01.2f", $total_cyp);
										} else {
											//$d_val = '0.00';
											$d_val = @sprintf("%01.2f", $results[$row_key][$c][0][$in_key]);
										}


										//$d_val = 0.00;
									}
								} elseif ($columns == 'national') {
									//pr($results2);
									//exit;
									$cyp = 0;
									$total_cyp = 0;
									foreach ($results2[$row_key] as $result2) {
										//pr($result2);
										$pro_volume = $result2[0]['volume'];
										$cyp_s = $result2[0]['cyp'];
										$cyp_v = $result2[0]['cyp_v'];

										if ($cyp_s == '/') {
											@$cyp = $pro_volume / $cyp_v;
										} elseif ($cyp_s == '*') {
											@$cyp = $pro_volume * $cyp_v;
										} elseif ($cyp_s == '-') {
											@$cyp = $pro_volume - $cyp_v;
										} elseif ($cyp_s == '+') {
											@$cyp = $pro_volume + $cyp_v;
										} else {
											$cyp = 0;
										}
										$total_cyp += $cyp;
									}
									//echo $total_cyp;
									//exit;

									if ($in_key == 'cyp') {
										$d_val = sprintf("%01.2f", $total_cyp);
									} else {
										$d_val = @sprintf("%01.2f", $results[$row_key][$c][0][$in_key]);
									}
								} else {
									if (@$results[$row_key][$c]['MemoDetail']['product_id'] == $col_key) {
										//echo $d_val;
										//exit;
										//exit;
										if ($in_key == 'cyp') {
											$d_val = '0.00';
											$pro_qty =  @$results[$row_key][$c][0]['volume'];
											$cyp_cal =  @$results[$row_key][$c][0]['cyp'];
											$cyp_val =  @$results[$row_key][$c][0]['cyp_v'];

											if ($cyp_cal && $cyp_val) {
												if ($cyp_cal == '*') {
													$d_val =  @sprintf("%01.2f", $pro_qty * $cyp_val);
												}
												if ($cyp_cal == '/') {
													$d_val =  @sprintf("%01.2f", $pro_qty / $cyp_val);
												}
												if ($cyp_cal == '-') {
													$d_val =  @sprintf("%01.2f", $pro_qty - $cyp_val);
												}
												if ($cyp_cal == '+') {
													$d_val =  @sprintf("%01.2f", $pro_qty + $cyp_val);
												}
											}
										} else {
											$d_val = @sprintf("%01.2f", $results[$row_key][$c][0][$in_key]);
										}

										//$d_val = 0.00;
									}
								}
							}
						} else {
							if ($request_data['SalesAnalysisReports']['columns'] == 'product' || $request_data['SalesAnalysisReports']['columns'] == 'brand' || $request_data['SalesAnalysisReports']['columns'] == 'category') {
								$d_val = ($in_key != 'cyp') ? @sprintf("%01.2f", $results[$row_key][$col_key][$in_key]) : '';

								if ($in_key == 'cyp') {
									$d_val = '';
									$pro_qty =  @$results[$row_key][$col_key]['volume'];
									$cyp_cal =  @$results[$row_key][$col_key]['cyp'];
									$cyp_val =  @$results[$row_key][$col_key]['cyp_v'];

									if ($cyp_cal && $cyp_val) {
										if ($cyp_cal == '*') {
											$d_val =  @sprintf("%01.2f", $pro_qty * $cyp_val);
										}
										if ($cyp_cal == '/') {
											$d_val =  @sprintf("%01.2f", $pro_qty / $cyp_val);
										}
										if ($cyp_cal == '-') {
											$d_val =  @sprintf("%01.2f", $pro_qty - $cyp_val);
										}
										if ($cyp_cal == '+') {
											$d_val =  @sprintf("%01.2f", $pro_qty + $cyp_val);
										}
									}
								}
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'outlet_type') {
								$d_val = @sprintf("%01.2f", $results[$row_key][$col_key][$in_key]);
							} elseif ($request_data['SalesAnalysisReports']['columns'] == 'national') {
								$d_val = @sprintf("%01.2f", $results[$row_key][$in_key]);
							}
						}

						$line .= $d_val . "\t";
					}
				}

				//echo $i.'<br>';
				$i++;
				$c++;
			}


			$data1 .= trim($line) . "\n";
		}

		$data1 .= "\n";


		//exit;

		$data1 = str_replace("\r", "", $data1);
		if ($data1 == "") {
			$data1 = "\n(0) Records Found!\n";
		}

		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: attachment; filename=\"Sales-Analysis-Reports-" . date("jS-F-Y-H:i:s") . ".xls\"");
		header("Pragma: no-cache");
		header("Expires: 0");

		echo $data1;

		exit;

		$this->autoRender = false;
	}
	function get_monthly_target($request_data, $col_keys, $row_keys)
	{
		$this->LoadModel('FiscalYear');
		$this->LoadModel('SaleTargetMonth');
		@$unit_type = $request_data['SalesAnalysisReports']['unit_type'];
		$output = [];
		$date_from = date('Y-m-01', strtotime($request_data['SalesAnalysisReports']['date_from']));
		$date_to = date('Y-m-t', strtotime($request_data['SalesAnalysisReports']['date_to']));
		$office_id = isset($request_data['SalesAnalysisReports']['office_id']) ? $request_data['SalesAnalysisReports']['office_id'] : 0;
		$territory_id = (isset($request_data['SalesAnalysisReports']['territory_id'])) ? $request_data['SalesAnalysisReports']['territory_id'] : 0;
		$rows_for_target = $request_data['SalesAnalysisReports']['rows_for_target'];
		$columns_for_target = $request_data['SalesAnalysisReports']['columns_for_target'];

		$fields = array();
		$groups = array();
		$conditions = array();
		if ($unit_type == 2) {
			array_push($fields, 'SUM(SaleTargetMonth.target_quantity) as target_qty');
		} elseif ($unit_type == 1) {
			array_push($fields, 'CAST(SUM(ROUND(SaleTargetMonth.target_quantity/(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END),2,1))as decimal(20,2)) as target_qty');
		}
		array_push($fields, 'SUM(SaleTargetMonth.target_amount) as target_amount');
		if ($rows_for_target == 'territory') {
			$conditions['SaleTargetMonth.territory_id'] = $row_keys;
			array_push($fields, 'SaleTargetMonth.territory_id as row_item_id');
			array_push($groups, 'SaleTargetMonth.territory_id');
		}
		if ($rows_for_target == 'area') {
			$conditions['SaleTargetMonth.aso_id'] = $row_keys;
			array_push($fields, 'SaleTargetMonth.aso_id as row_item_id');
			array_push($groups, 'SaleTargetMonth.aso_id');
		}
		if ($rows_for_target == 'region') {
			$conditions['Office.parent_office_id'] = $row_keys;
			array_push($fields, 'Office.parent_office_id as row_item_id');
			array_push($groups, 'Office.parent_office_id');
		}
		if ($rows_for_target == 'national') {
			array_push($fields, '\'national\' as row_item_id');
		}
		//for columns
		if ($columns_for_target == 'product') {
			$conditions['SaleTargetMonth.product_id'] = $col_keys;
			array_push($fields, 'SaleTargetMonth.product_id as col_item_id');
			array_push($groups, 'SaleTargetMonth.product_id');
		}

		if ($columns_for_target == 'category') {
			$conditions['Product.product_category_id'] = $col_keys;
			array_push($fields, 'Product.product_category_id as col_item_id');
			array_push($groups, 'Product.product_category_id');
		}
		if ($columns_for_target != 'category' && $request_data['SalesAnalysisReports']['product_category_id']) {
			$conditions['Product.product_category_id'] = $request_data['SalesAnalysisReports']['product_category_id'];
			array_push($fields, 'Product.product_category_id as col_item_id');
			array_push($groups, 'Product.product_category_id');
		}


		if ($columns_for_target == 'brand') {
			$conditions['Product.brand_id'] = $col_keys;
			array_push($fields, 'Product.brand_id as col_item_id');
			array_push($groups, 'Product.brand_id');
		}
		if ($columns_for_target != 'brand' && $request_data['SalesAnalysisReports']['brand_id']) {
			$conditions['Product.brand_id'] = $request_data['SalesAnalysisReports']['brand_id'];
			array_push($fields, 'Product.brand_id as col_item_id');
			array_push($groups, 'Product.brand_id');
		}
		if ($columns_for_target == 'national') {
			array_push($fields, '\'national\' as col_item_id');
		}
		for ($i = $date_from; $i <= $date_to; $i = date('Y-m-d', strtotime("+1 month" . $i))) {
			$fiscal_years = $this->FiscalYear->find('first', array(
				'conditions' => array(
					'FiscalYear.start_date <=' => $i,
					'FiscalYear.end_date >=' => $i
				),
				'recursive' => -1
			));
			$fiscal_year_id = $fiscal_years['FiscalYear']['id'];

			$conditions['SaleTargetMonth.fiscal_year_id'] = $fiscal_year_id;
			$conditions['Month.name'] = date('F', strtotime($i));

			$sale_target_month = $this->SaleTargetMonth->find('all', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'table' => 'months',
						'alias' => 'Month',
						'conditions' => 'Month.id=SaleTargetMonth.month_id'
					),
					array(
						'table' => 'offices',
						'alias' => 'Office',
						'type' => 'inner',
						'conditions' => 'Office.id=SaleTargetMonth.aso_id'
					),
					array(
						'table' => 'products',
						'alias' => 'Product',
						'type' => 'inner',
						'conditions' => 'Product.id=SaleTargetMonth.product_id'
					),
					array(
						'table' => 'product_measurements',
						'alias' => 'ProductMeasurement',
						'type' => 'left',
						'conditions' => 'ProductMeasurement.product_id=Product.id AND ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id'
					),
				),
				'group' => $groups,
				'fields' => $fields,
				'recursive' => -1
			));
			foreach ($sale_target_month as $data) {
				$output[$data['0']['row_item_id']][$data['0']['col_item_id']][date('Y-m', strtotime($i))] = array(
					'target_volume' => $data['0']['target_qty'],
					'target_value' => $data['0']['target_amount'],
				);
			}
		}
		return $output;
	}

	function get_achievement($request_data, $col_keys, $row_keys)
	{
		$this->LoadModel('Memo');
		@$unit_type = $request_data['SalesAnalysisReports']['unit_type'];
		$output = [];
		$date_from = date('Y-m-d', strtotime($request_data['SalesAnalysisReports']['date_from']));
		$date_to = date('Y-m-d', strtotime($request_data['SalesAnalysisReports']['date_to']));
		$office_id = isset($request_data['SalesAnalysisReports']['office_id']) ? $request_data['SalesAnalysisReports']['office_id'] : 0;
		$territory_id = (isset($request_data['SalesAnalysisReports']['territory_id'])) ? $request_data['SalesAnalysisReports']['territory_id'] : 0;
		$rows_for_target = $request_data['SalesAnalysisReports']['rows_for_target'];
		$target_rows_type = $request_data['SalesAnalysisReports']['target_rows_type'];
		$columns_for_target = $request_data['SalesAnalysisReports']['columns_for_target'];

		$fields = array();
		$groups = array();
		$conditions = array();
		if ($unit_type == 2) {
			array_push($fields, 'sum(ROUND((MemoDetail.sales_qty* CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END ),0)) AS sales_qty');
		} elseif ($unit_type == 1) {
			array_push($fields, 'CAST(sum(ROUND(ROUND((MemoDetail.sales_qty* CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END ),0)/(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END),2,1)) as decimal(20,2)) AS sales_qty');
		}
		array_push($fields, 'SUM(MemoDetail.sales_qty*MemoDetail.price) as value');
		if ($rows_for_target == 'territory') {
			$conditions['Memo.territory_id'] = $row_keys;
			array_push($fields, 'Memo.territory_id as row_item_id');
			array_push($groups, 'Memo.territory_id');
		}
		if ($rows_for_target == 'area') {
			$conditions['Memo.office_id'] = $row_keys;
			array_push($fields, 'Memo.office_id as row_item_id');
			array_push($groups, 'Memo.office_id');
		}
		if ($rows_for_target == 'region') {
			$conditions['Office.parent_office_id'] = $row_keys;
			array_push($fields, 'Office.parent_office_id as row_item_id');
			array_push($groups, 'Office.parent_office_id');
		}
		if ($rows_for_target == 'national') {
			array_push($fields, '\'national\' as row_item_id');
		}
		//for columns
		if ($columns_for_target == 'product') {
			$conditions['MemoDetail.product_id'] = $col_keys;
			array_push($fields, 'MemoDetail.product_id as col_item_id');
			array_push($groups, 'MemoDetail.product_id');
		}

		if ($columns_for_target == 'category') {
			$conditions['Product.product_category_id'] = $col_keys;
			array_push($fields, 'Product.product_category_id as col_item_id');
			array_push($groups, 'Product.product_category_id');
		}
		if ($columns_for_target != 'category' && $request_data['SalesAnalysisReports']['product_category_id']) {
			$conditions['Product.product_category_id'] = $request_data['SalesAnalysisReports']['product_category_id'];
			array_push($fields, 'Product.product_category_id as col_item_id');
			array_push($groups, 'Product.product_category_id');
		}


		if ($columns_for_target == 'brand') {
			$conditions['Product.brand_id'] = $col_keys;
			array_push($fields, 'Product.brand_id as col_item_id');
			array_push($groups, 'Product.brand_id');
		}
		if ($columns_for_target != 'brand' && $request_data['SalesAnalysisReports']['brand_id']) {
			$conditions['Product.brand_id'] = $request_data['SalesAnalysisReports']['brand_id'];
			array_push($fields, 'Product.brand_id as col_item_id');
			array_push($groups, 'Product.brand_id');
		}
		if ($columns_for_target == 'national') {
			array_push($fields, '\'national\' as col_item_id');
		}
		if ($target_rows_type == 'day') {
			array_push($fields, 'Memo.memo_date as date_col');
			array_push($groups, 'Memo.memo_date');
			$conditions['Memo.memo_date BETWEEN ? AND ?'] = array($date_from, $date_to);
		} else {
			array_push($fields, 'format(memo_date,\'yyyy-MM\') as date_col');
			array_push($groups, 'format(memo_date,\'yyyy-MM\')');
			$conditions['Memo.memo_date BETWEEN ? AND ?'] = array(date('Y-m-01', strtotime($date_from)), date('Y-m-t', strtotime($date_to)));
		}

		$conditions['MemoDetail.price >'] = 0;

		$memo_results = $this->Memo->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'memo_details',
					'alias' => 'MemoDetail',
					'type' => 'inner',
					'conditions' => 'Memo.id=MemoDetail.memo_id'
				),
				array(
					'alias' => 'dbp',
					'table' => 'discount_bonus_policies',
					'type' => 'left',
					'conditions' => 'MemoDetail.policy_id = dbp.id'
				),
				array(
					'table' => 'offices',
					'alias' => 'Office',
					'type' => 'inner',
					'conditions' => 'Office.id=Memo.office_id'
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'inner',
					'conditions' => 'Product.id=MemoDetail.product_id'
				),
				array(
					'table' => 'product_measurements',
					'alias' => 'ProductMeasurement',
					'type' => 'left',
					'conditions' => 'ProductMeasurement.product_id=Product.id AND ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id'
				),
			),
			'group' => $groups,
			'fields' => $fields,
			'recursive' => -1
		));

		foreach ($memo_results as $data) {
			$output[$data['0']['row_item_id']][$data['0']['col_item_id']][$data['0']['date_col']] = array(
				'volume' => $data['0']['sales_qty'],
				'value' => $data['0']['value'],
			);
		}
		return $output;
	}

	function create_output_for_target($request_data, $month_wise_target, $achivement, $rows_list, $columns_list, $rows_type_list, $indicators_fot_target)
	{
		// pr(compact('month_wise_target','request_data'));exit;
		$working_days = $request_data['SalesAnalysisReports']['target_working_days'];
		$working_days = (($working_days)) ? $working_days : 26;
		$total_colummn = array();
		$indicators_array = array();
		if (empty($request_data['SalesAnalysisReports']['indicators_fot_target'])) {
			foreach ($indicators_fot_target as $key => $val) {
				array_push($indicators_array, $key);
			}
		} else {
			$indicators_array = $request_data['SalesAnalysisReports']['indicators_fot_target'];
		}
		$output = '';
		foreach ($rows_list as $row_item_id => $row_header) {
			foreach ($rows_type_list as $row_type_id => $row_item_name) {
				$output .= '<tr class="titlerow">';
				if ($request_data['SalesAnalysisReports']['rows_for_target'] == 'territory') {
					$output .= '<td> ' . $row_header['office_name'] . '</td>';
					$output .= '<td> ' . $row_header['territory_name'] . ' </td>';
				} else {
					$output .= '<td style="text-align:left;"><div>' . $row_header . '</div></td>';
				}
				$output .= '<td style="text-align:left;"><div>' . $row_item_name . '</div></td>';

				$color = '#f1f1f1';
				foreach ($columns_list as $col_key => $col_val) {
					$color = ($color == '#f1f1f1') ? '#e2e2e2' : '#f1f1f1';
					foreach ($indicators_fot_target as $in_key => $in_val) {
						if (in_array($in_key, $indicators_array)) {
							// echo 'row : -'.$row_item_id.'- col :- '.$col_key.'- rowitem:- '.date('Y-m',strtotime($row_type_id)).'- indicator :- target_'.$in_key;exit;
							if ($request_data['SalesAnalysisReports']['target_rows_type'] == 'day') {
								$target = (@$month_wise_target[$row_item_id][$col_key][date('Y-m', strtotime($row_type_id))]['target_' . $in_key] / $working_days);
							} else {
								$target = @$month_wise_target[$row_item_id][$col_key][$row_type_id]['target_' . $in_key];
							}
							$achievement = @$achivement[$row_item_id][$col_key][$row_type_id][$in_key];
							$achievement = $achievement ? $achievement : 0;
							$target = $target ? $target : 0;

							$total_colummn[$col_key][$in_key] = (isset($total_colummn[$col_key][$in_key]) ? $total_colummn[$col_key][$in_key] : 0) + $achievement;
							$total_colummn[$col_key]['target_' . $in_key] = (isset($total_colummn[$col_key]['target_' . $in_key]) ? $total_colummn[$col_key]['target_' . $in_key] : 0) + $target;

							$output .= '<td style="background:' . $color . ';"><div>' . sprintf('%0.2f', $target) . '</div></td>';
							$output .= '<td style="background:' . $color . ';"><div> ' . sprintf('%0.2f', $achievement) . ' </div></td>';
							$output .= '<td style="background:' . $color . ';"><div>' . sprintf('%0.2f', ($achievement / ($target ? $target : 1)) * 100) . '%</div></td>';
						}
					}
				}
				$output .= '</tr>';
			}
		}
		$output .= '<tr>';
		if ($request_data['SalesAnalysisReports']['rows_for_target'] == 'territory') {
			$output .= '<td colspan="3"> Total </td>';
		} else {
			$output .= '<td colspan="2"> Total </td>';
		}
		$color = '#f1f1f1';
		foreach ($columns_list as $col_key => $col_val) {
			$color = ($color == '#f1f1f1') ? '#e2e2e2' : '#f1f1f1';
			foreach ($indicators_fot_target as $in_key => $in_val) {
				if (in_array($in_key, $indicators_array)) {
					$target = $total_colummn[$col_key]['target_' . $in_key];

					$achievement = $total_colummn[$col_key][$in_key];
					$achievement = $achievement ? $achievement : 0;
					$target = $target ? $target : 0;

					$output .= '<td style="background:' . $color . ';"><div>' . sprintf('%0.2f', $target) . '</div></td>';
					$output .= '<td style="background:' . $color . ';"><div> ' . sprintf('%0.2f', $achievement) . ' </div></td>';
					$output .= '<td style="background:' . $color . ';"><div>' . sprintf('%0.2f', ($achievement / ($target ? $target : 1)) * 100) . '%</div></td>';
				}
			}
		}
		$output .= '</tr>';
		return $output;
	}
}
