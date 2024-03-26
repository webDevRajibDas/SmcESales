<?php
App::uses('AppController', 'Controller');
/**
 * EsalesSettings Controller
 *
 * @property SalesAnalysisReport $SalesAnalysisReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistSalesAnalysisReportsController extends AppController
{



	/**
	 * Components
	 *
	 * @var array
	 */

	public $uses = array('Memo', 'DistMemoDetail', 'Office', 'Territory', 'OutletCategory', 'Outlet', 'Product', 'SalesPerson', 'Division', 'District', 'Thana', 'Brand', 'ProductCategory', 'Program', 'TerritoryAssignHistory', 'NotundinProgram', 'DistMemo', 'DistMemoDetail', 'DistSalesRepresentative', 'DistMarket', 'DistOutlet', 'DistDistributor', 'DistTso', 'DistAreaExecutive');
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


		//pr($this->request->data); exit;
		ini_set('memory_limit', '512M');
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes

		$this->set('page_title', 'Distributor Sales Analysis Report');

		$request_data = array();

		$sr_id = isset($this->request->data['DistSalesAnalysisReports']['sr_id']) != '' ? $this->request->data['DistSalesAnalysisReports']['sr_id'] : 0;
		$this->set(compact('sr_id'));
		$srs = array();

		//for outlet category list
		$outlet_categories = $this->OutletCategory->find('list', array(
			'conditions' => array('OutletCategory.id !=' => '17'),
			'order' => array('id' => 'asc')
		));
		$this->set(compact('outlet_categories'));

		$conditions = array(
			'OutletCategory.id !=' => '17'
		);
		$outlet_category_ids = isset($this->request->data['DistSalesAnalysisReports']['outlet_category_id']) != '' ? $this->request->data['DistSalesAnalysisReports']['outlet_category_id'] : 0;

		if ($outlet_category_ids) $conditions['OutletCategory.id'] = $outlet_category_ids;
		$outlet_categories2 = $this->OutletCategory->find('list', array(
			'conditions' => $conditions,
			'order' => array('id' => 'asc')
		));


		//for brands list
		$conditions = array(
			'NOT' => array('Brand.id' => 44),
			'Product.is_distributor_product' => 1
		);
		$brands = $this->Brand->find('list', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'conditions' => 'Product.brand_id=Brand.id'
				)
			),
			'group' => array('Brand.id', 'Brand.name'),
			'fields' => array('Brand.id', 'Brand.name'),
			'order' => array('Brand.id' => 'asc')
		));

		$this->set(compact('brands'));


		$conditions = array(
			'NOT' => array('Brand.id' => 44),
			'Product.is_distributor_product' => 1
		);

		$brand_ids = isset($this->request->data['DistSalesAnalysisReports']['brand_id']) != '' ? $this->request->data['DistSalesAnalysisReports']['brand_id'] : 0;

		if ($brand_ids) $conditions['Brand.id'] = $brand_ids;

		$brands2 = $this->Brand->find('list', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'conditions' => 'Product.brand_id=Brand.id'
				)
			),
			'group' => array('Brand.id', 'Brand.name'),
			'fields' => array('Brand.id', 'Brand.name'),
			'order' => array('Brand.id' => 'asc')
		));


		//report type
		$unit_types = array(
			'1' => 'Sales Unit',
			'2' => 'Base Unit',
		);
		$this->set(compact('unit_types'));


		//for cateogry list
		$conditions = array(
			'NOT' => array('ProductCategory.id' => 32),
			'Product.is_distributor_product' => 1
		);
		$categories = $this->ProductCategory->find('list', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'conditions' => 'Product.product_category_id=ProductCategory.id'
				)
			),
			'group' => array('ProductCategory.id', 'ProductCategory.name'),
			'fields' => array('ProductCategory.id', 'ProductCategory.name'),
			'order' => array('ProductCategory.id' => 'asc')
		));
		$this->set(compact('categories'));

		$conditions = array(
			'NOT' => array('ProductCategory.id' => 32),
			'Product.is_distributor_product' => 1
		);
		$product_category_ids = isset($this->request->data['DistSalesAnalysisReports']['product_category_id']) != '' ? $this->request->data['DistSalesAnalysisReports']['product_category_id'] : 0;
		if ($product_category_ids) $conditions['ProductCategory.id'] = $product_category_ids;

		$category_list2 = $this->ProductCategory->find('list', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'conditions' => 'Product.product_category_id=ProductCategory.id'
				)
			),
			'group' => array('ProductCategory.id', 'ProductCategory.name'),
			'fields' => array('ProductCategory.id', 'ProductCategory.name'),
			'order' => array('ProductCategory.id' => 'asc')
		));


		//for rows
		$rows = array(
			'sr' 			=> 'By SR',
			'dist' 			=> 'By Dist',
			'tso' 			=> 'By TSO',
			'ae' 			=> 'By AE',
			'territory' 	=> 'By Territory',
			'area' 			=> 'By Area',
			'month' 		=> 'By Month',
			'division' 		=> 'By Division',
			'district' 		=> 'By District',
			'thana' 		=> 'By Thana',
			'national' 		=> 'By National',
		);
		$this->set(compact('rows'));

		//for columns
		$columns = array(
			'product' 		=> 'By Product',
			'brand' 		=> 'By Brand',
			'category' 		=> 'By Category',
			'outlet_type' 	=> 'By Outlet Type',
			'national' 		=> 'By National',
		);
		$this->set(compact('columns'));


		//for indicator
		$indicators = array(
			'volume' 		=> 'Volume',
			'value' 		=> 'Value',
			'oc' 			=> 'OC',
			'ec' 			=> 'EC',
			'cyp' 			=> 'CYP',
			'bonus' 			=> 'Bonus',
		);
		$this->set(compact('indicators'));

		//for product type
		$sql = "SELECT * FROM product_sources";
		$sources_datas = $this->Product->query($sql);
		$product_types = array();
		foreach ($sources_datas as $sources_data) {
			$product_types[$sources_data[0]['name']] = $sources_data[0]['name'];
		}
		/*$product_types = array(
			'smcel' 		=> 'SMCEL',
			'smc' 			=> 'SMC',
		);*/
		$this->set(compact('product_types'));

		//Location Types
		$locationTypes = $this->DistOutlet->DistMarket->LocationType->find('list');
		$this->set(compact('locationTypes'));

		//products
		$conditions = array(
			'NOT' => array('Product.product_category_id' => 32),
			'is_active' => 1,
			'is_distributor_product' => 1,
			'Product.product_type_id' => 1
		);
		//if($product_type)
		$product_type = isset($this->request->data['DistSalesAnalysisReports']['product_type']) != '' ? $this->request->data['DistSalesAnalysisReports']['product_type'] : '';
		if ($product_type) $conditions['source'] = $product_type;
		$product_list = $this->Product->find('list', array(
			'conditions' => $conditions,
			'order' =>  array('order' => 'asc')
		));
		$this->set(compact('product_list'));

		//divisions
		$divisions = $this->Division->find('list', array(
			//'conditions'=>array('NOT' => array('Product.product_category_id'=>32), 'is_active' => 1),
			'order' =>  array('name' => 'asc')
		));
		$this->set(compact('divisions'));

		//product_measurement
		$product_measurement = $this->Product->find('list', array(
			//'conditions'=> array('is_distributor_product' => 1),
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
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,

					"NOT" => array("id" => array(30, 31, 37))
				),
				'order' => array('office_name' => 'asc')
			));

			$office_id = array_keys($offices);
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
			$office_id = $this->UserAuth->getOfficeId();
		}

		$this->set(compact('office_id'));

		$report_esales_setting_id = array();

		$territories = array();

		if ($office_id || $this->request->is('post') || $this->request->is('put')) {



			if (!$office_parent_id) {
				$office_id = isset($this->request->data['DistSalesAnalysisReports']['office_id']) != '' ? $this->request->data['DistSalesAnalysisReports']['office_id'] : $office_id;
			}


			//Start SR list
			$sos = array();

			if ($office_id) {
				@$date_from = date('Y-m-d', strtotime($this->request->data['DistSalesAnalysisReports']['date_from']));
				@$date_to = date('Y-m-d', strtotime($this->request->data['DistSalesAnalysisReports']['date_to']));

				$sr_conditions = array('DistMemo.office_id' => $office_id);
				$sr_conditions['DistMemo.memo_date BETWEEN ? and ? '] = array($date_from, $date_to);

				$sr_list_from_memo = array();
				$sr_list_from_memo = $this->DistMemo->find('all', array(
					'fields' => array('DISTINCT DistMemo.sr_id'),
					'conditions' => $sr_conditions,
					'recursive' => -1
				));

				$sr_list_from_memo_ar = array();
				foreach ($sr_list_from_memo as $key => $value) {
					$sr_list_from_memo_ar[] = $value['DistMemo']['sr_id'];
				}


				$sr_list = $this->DistSalesRepresentative->find('list', array('conditions' => array('DistSalesRepresentative.id' => $sr_list_from_memo_ar), 'order' =>  array('DistSalesRepresentative.name' => 'asc')));
				$srs = $sr_list;
			}



			$this->set(compact('sr_list'));
			//End sr list

			//district list
			$division_id = isset($this->request->data['DistSalesAnalysisReports']['division_id']) != '' ? $this->request->data['DistSalesAnalysisReports']['division_id'] : 0;
			$districts = $this->District->find('list', array(
				'conditions' => array('District.division_id' => $division_id),
				'order' => array('District.name' => 'asc')
			));
			$this->set(compact('districts'));



			//thana list
			$district_id = isset($this->request->data['DistSalesAnalysisReports']['district_id']) != '' ? $this->request->data['DistSalesAnalysisReports']['district_id'] : 0;
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
			@$unit_type = $this->request->data['DistSalesAnalysisReports']['unit_type'];
			//$this->set(compact('unit_type'));
			$unit_type_text = ($unit_type == 2) ? 'Base' : 'Sales';
			$this->set(compact('unit_type_text'));


			if ($this->request->data['DistSalesAnalysisReports']['indicators']) {
				//pr($indicators);
				// pr($this->request->data);
				// exit;

				$request_data = $this->request->data;
				$date_from = $request_data['DistSalesAnalysisReports']['date_from'];
				$date_to = $request_data['DistSalesAnalysisReports']['date_to'];
				$this->set(compact('date_from', 'date_to', 'request_data'));

				if (!$office_id && $request_data['DistSalesAnalysisReports']['date_from']) {
					$office_id = $request_data['DistSalesAnalysisReports']['office_id'];
				}
				$territory_id = (isset($request_data['DistSalesAnalysisReports']['territory_id'])) ? $request_data['DistSalesAnalysisReports']['territory_id'] : 0;


				//for columns
				$columns_list = array();

				if ($request_data['DistSalesAnalysisReports']['columns'] == 'product') {
					//for products list
					$conditions = array('NOT' => array('Product.product_category_id' => 32), 'Product.product_type_id' => 1, 'is_distributor_product' => 1, 'is_active' => 1);
					if ($request_data['DistSalesAnalysisReports']['product_id']) $conditions['id'] = $request_data['DistSalesAnalysisReports']['product_id'];
					$product_type = isset($this->request->data['DistSalesAnalysisReports']['product_type']) != '' ? $this->request->data['DistSalesAnalysisReports']['product_type'] : '';
					if ($product_type) $conditions['source'] = $product_type;
					$product_list = $this->Product->find('list', array(

						'conditions' => $conditions,
						'order' =>  array('order' => 'asc')
					));
					$columns_list = $product_list;
				} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'brand') {
					$columns_list = $brands2;
				} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'category') {
					$columns_list = $category_list2;
				} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'outlet_type') {
					$columns_list = $outlet_categories2;
				} else {
					$columns_list = array('National');
				}
				$this->set(compact('columns_list'));
				//end for columns



				//for rows
				$rows_list = array();
				if ($request_data['DistSalesAnalysisReports']['rows'] == 'sr') {

					$date_from = date('Y-m-d', strtotime($request_data['DistSalesAnalysisReports']['date_from']));
					$date_to = date('Y-m-d', strtotime($request_data['DistSalesAnalysisReports']['date_to']));


					$sr_list = array();
					$sr_conditions = array();

					if ($office_id) {
						$sr_conditions = array('DistMemo.office_id' => $office_id);
					}

					$sr_conditions['DistMemo.memo_date BETWEEN ? and ? '] = array($date_from, $date_to);

					$sr_list_from_memo = array();
					$sr_list_from_memo = $this->DistMemo->find('all', array(

						'fields' => array('DISTINCT DistMemo.sr_id', 'DistTso.name', 'DistDistributor.name', 'DistAE.name', 'Office.office_name'),
						'joins' => array(


							array(
								'alias' => 'DistDistributor',
								'table' => 'dist_distributors',
								'type' => 'LEFT',
								'conditions' => 'DistDistributor.id = DistMemo.distributor_id'
							),


							array(
								'table' => 'dist_tsos',
								'alias' => 'DistTso',
								'type' => 'LEFT',
								'conditions' => 'DistTso.id = DistMemo.tso_id'

							),
							array(
								'table' => 'dist_area_executives',
								'alias' => 'DistAE',
								'type' => 'LEFT',
								'conditions' => 'DistAE.id = DistTso.dist_area_executive_id'

							),
							array(
								'table' => 'offices',
								'alias' => 'Office',
								'type' => 'LEFT',
								'conditions' => array('Office.id=DistMemo.office_id')
							),


						),
						'conditions' => $sr_conditions,
						'recursive' => -1
					));

					// print_r($sr_list_from_memo);exit;
					$sr_info = array();
					$sr_list_from_memo_ar = array();
					foreach ($sr_list_from_memo as $key => $value) {
						$sr_list_from_memo_ar[] = $value['DistMemo']['sr_id'];
						//taking Sr wise db,ae,tso,office name
						$sr_info[$value['DistMemo']['sr_id']]['DB'] = $value['DistDistributor']['name'];
						$sr_info[$value['DistMemo']['sr_id']]['TSO'] = $value['DistTso']['name'];
						$sr_info[$value['DistMemo']['sr_id']]['AE'] = $value['DistAE']['name'];
						$sr_info[$value['DistMemo']['sr_id']]['OFFICE'] = $value['Office']['office_name'];
					}

					$sr_list = $this->DistSalesRepresentative->find('list', array('conditions' => array('DistSalesRepresentative.id' => $sr_list_from_memo_ar), 'order' =>  array('DistSalesRepresentative.name' => 'asc')));

					// pr($sr_info);exit;
					$rows_list = $sr_list;
				} elseif ($request_data['DistSalesAnalysisReports']['rows'] == 'dist') {
					$date_from = date('Y-m-d', strtotime($request_data['DistSalesAnalysisReports']['date_from']));
					$date_to = date('Y-m-d', strtotime($request_data['DistSalesAnalysisReports']['date_to']));

					$dist_list = array();
					$dist_conditions = array();

					if ($office_id) {
						$dist_conditions = array('DistMemo.office_id' => $office_id);
					}

					$dist_conditions['DistMemo.memo_date BETWEEN ? and ? '] = array($date_from, $date_to);

					$dist_list_from_memo = array();
					$dist_list_from_memo = $this->DistMemo->find('all', array(
						'fields' => array(' DistMemo.distributor_id', 'DistDistributor.id', 'DistDistributor.name', 'DistTso.name', 'DistAE.name', 'Office.office_name'),
						'joins' => array(

							array(
								'table' => 'dist_distributors',
								'alias' => 'DistDistributor',
								'type' => 'LEFT',
								'conditions' => 'DistDistributor.id = DistMemo.distributor_id'

							),
							array(
								'table' => 'dist_tsos',
								'alias' => 'DistTso',
								'type' => 'LEFT',
								'conditions' => 'DistTso.id = DistMemo.tso_id'

							),
							array(
								'table' => 'dist_area_executives',
								'alias' => 'DistAE',
								'type' => 'LEFT',
								'conditions' => 'DistAE.id = DistTso.dist_area_executive_id'

							),
							array(
								'table' => 'offices',
								'alias' => 'Office',
								'type' => 'LEFT',
								'conditions' => array('Office.id=DistMemo.office_id')
							),


						),
						'group' => array('DistDistributor.id', 'DistDistributor.name', 'DistTso.name', 'DistAE.name', 'Office.office_name', 'Office.order', 'DistMemo.distributor_id'),
						'order' =>  array('Office.order asc', 'DistTso.name asc', 'DistDistributor.id'),
						'conditions' => $dist_conditions,
						'recursive' => -1
					));

					$dist_list_from_memo_ar = array();
					$dist_info = array();
					$dist_list = array();
					foreach ($dist_list_from_memo as $key => $value) {
						$dist_list[$value['DistDistributor']['id']] = $value['DistDistributor']['name'];
						$dist_list_from_memo_ar[] = $value['DistDistributor']['id'];
						//taking DB wise ae,tso,office name
						$dist_info[$value['DistDistributor']['id']]['TSO'] = $value['DistTso']['name'];
						$dist_info[$value['DistDistributor']['id']]['AE'] = $value['DistAE']['name'];
						$dist_info[$value['DistDistributor']['id']]['OFFICE'] = $value['Office']['office_name'];
					}

					/* $dist_list = $this->DistDistributor->find(
						'list',
						array(
							'conditions' => array('DistDistributor.id' => $dist_list_from_memo_ar),
							'joins' => array(


								array(
									'table' => 'dist_tso_mappings',
									'alias' => 'DistTsoMapping',
									'type' => 'LEFT',
									'conditions' => 'DistTsoMapping.dist_distributor_id = DistDistributor.id'

								),
								array(
									'table' => 'dist_tsos',
									'alias' => 'DistTso',
									'type' => 'LEFT',
									'conditions' => 'DistTso.id = DistTsoMapping.dist_tso_id'

								),
								array(
									'table' => 'dist_area_executives',
									'alias' => 'DistAE',
									'type' => 'LEFT',
									'conditions' => 'DistAE.id = DistTso.dist_area_executive_id'

								),
								array(
									'table' => 'offices',
									'alias' => 'Office',
									'type' => 'LEFT',
									'conditions' => array('Office.id=DistAE.office_id')
								),
							),
							'group' =>  array('DistDistributor.name', 'DistDistributor.id', 'Office.order', 'DistTso.name'),
							'order' =>  array('Office.order asc', 'DistTso.name asc', 'DistDistributor.id'),
							'fields' =>  array('DistDistributor.id', 'DistDistributor.name'),
							'recursive' => -1
						)
					); */

					/* echo  $this->DistDistributor->getLastQuery();
					pr($dist_list);
					exit; */

					$rows_list = $dist_list;
				} elseif ($request_data['DistSalesAnalysisReports']['rows'] == 'tso') {

					$date_from = date('Y-m-d', strtotime($request_data['DistSalesAnalysisReports']['date_from']));
					$date_to = date('Y-m-d', strtotime($request_data['DistSalesAnalysisReports']['date_to']));


					$tso_list = array();
					$tso_conditions = array();

					if ($office_id) {
						$tso_conditions = array('DistMemo.office_id' => $office_id);
					}

					$tso_conditions['DistMemo.memo_date BETWEEN ? and ? '] = array($date_from, $date_to);

					$tso_list_from_memo = array();
					$tso_list_from_memo = $this->DistMemo->find('all', array(
						'fields' => array('DISTINCT DistMemo.tso_id', 'DistAE.name', 'Office.office_name'),
						'joins' => array(


							array(
								'table' => 'dist_tsos',
								'alias' => 'DistTso',
								'type' => 'LEFT',
								'conditions' => 'DistTso.id = DistMemo.tso_id'

							),
							array(
								'table' => 'dist_area_executives',
								'alias' => 'DistAE',
								'type' => 'LEFT',
								'conditions' => 'DistAE.id = DistTso.dist_area_executive_id'

							),
							array(
								'table' => 'offices',
								'alias' => 'Office',
								'type' => 'LEFT',
								'conditions' => array('Office.id=DistMemo.office_id')
							),


						),
						'conditions' => $tso_conditions,
						'recursive' => -1
					));

					$tso_list_from_memo_ar = array();
					$tso_info = array();
					foreach ($tso_list_from_memo as $key => $value) {
						if ($value) {
							$tso_list_from_memo_ar[] = $value['DistMemo']['tso_id'];
							//taking Tso wise ae,office name

							$tso_info[$value['DistMemo']['tso_id']]['AE'] = $value['DistAE']['name'];
							$tso_info[$value['DistMemo']['tso_id']]['OFFICE'] = $value['Office']['office_name'];
						}
					}

					$tso_list = $this->DistTso->find('list', array('conditions' => array('DistTso.id' => $tso_list_from_memo_ar), 'order' =>  array('DistTso.name' => 'asc')));
					$rows_list = $tso_list;
				} elseif ($request_data['DistSalesAnalysisReports']['rows'] == 'ae') {

					$date_from = date('Y-m-d', strtotime($request_data['DistSalesAnalysisReports']['date_from']));
					$date_to = date('Y-m-d', strtotime($request_data['DistSalesAnalysisReports']['date_to']));


					$ae_list = array();
					$ae_conditions = array();

					if ($office_id) {
						$ae_conditions = array('DistMemo.office_id' => $office_id);
					}

					$ae_conditions['DistMemo.memo_date BETWEEN ? and ? '] = array($date_from, $date_to);

					$ae_list_from_memo = array();
					$ae_list_from_memo = $this->DistMemo->find('all', array(
						'fields' => array('DistAE.id', 'Office.office_name'),
						'group' => array('DistAE.id', 'Office.office_name'),
						'joins' => array(
							array(
								'table' => 'dist_tsos',
								'alias' => 'DistTso',
								'type' => 'LEFT',
								'conditions' => 'DistTso.id = DistMemo.tso_id'

							),
							array(
								'table' => 'dist_area_executives',
								'alias' => 'DistAE',
								'type' => 'LEFT',
								'conditions' => 'DistAE.id = DistTso.dist_area_executive_id'

							),
							array(
								'table' => 'offices',
								'alias' => 'Office',
								'type' => 'LEFT',
								'conditions' => array('Office.id=DistMemo.office_id')
							),


						),
						'conditions' => $ae_conditions,
						'recursive' => -1
					));


					$ae_list_from_memo_ar = array();
					$ae_info = array();
					foreach ($ae_list_from_memo as $key => $value) {
						if ($value) {
							$ae_list_from_memo_ar[] = $value['DistAE']['id'];
							//taking Tso wise ae,office name
							$ae_info[$value['DistAE']['id']]['OFFICE'] = $value['Office']['office_name'];
						}
					}

					$ae_list = $this->DistAreaExecutive->find(
						'list',
						array(
							'conditions' => array('DistAreaExecutive.id' => $ae_list_from_memo_ar),
							'joins' => array(
								array(
									'table' => 'offices',
									'alias' => 'Office',
									'conditions' => 'Office.id=DistAreaExecutive.office_id'
								)
							),
							'group' => array('Office.order', 'DistAreaExecutive.id', 'DistAreaExecutive.name'),
							'fields' => array('DistAreaExecutive.id', 'DistAreaExecutive.name'),
							'order' =>  array('Office.order' => 'asc', 'DistAreaExecutive.name' => 'asc')
						)
					);
					$rows_list = $ae_list;
				} elseif ($request_data['DistSalesAnalysisReports']['rows'] == 'territory') {

					$date_from = date('Y-m-d', strtotime($request_data['DistSalesAnalysisReports']['date_from']));
					$date_to = date('Y-m-d', strtotime($request_data['DistSalesAnalysisReports']['date_to']));

					$territory_list = array();
					$territory_conditions = array();

					if ($office_id) {
						$territory_conditions = array('DistMemo.office_id' => $office_id);
					}

					$territory_conditions['DistMemo.memo_date BETWEEN ? and ? '] = array($date_from, $date_to);

					$territory_list_from_memo = array();
					$territory_list_from_memo = $this->DistMemo->find('all', array(
						'fields' => array('DISTINCT DistMemo.territory_id'),
						'conditions' => $territory_conditions,
						'recursive' => -1
					));

					$territory_list_from_memo_ar = array();
					foreach ($territory_list_from_memo as $key => $value) {
						if ($value) {
							$territory_list_from_memo_ar[] = $value['DistMemo']['territory_id'];
						}
					}

					$territory_list = $this->Territory->find('list', array('conditions' => array('Territory.id' => $territory_list_from_memo_ar), 'order' =>  array('Territory.name' => 'asc')));
					$rows_list = $territory_list;
				} elseif ($request_data['DistSalesAnalysisReports']['rows'] == 'area') {
					$conditions = array('Office.office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
					if ($office_id) $conditions['Office.id'] = $office_id;
					$area_list = $this->Office->find('list', array(
						'conditions' => $conditions,
						//'order' => array('office_name' => 'asc')
						'order' => array('order' => 'asc')
					));

					$rows_list = $area_list;
				} elseif ($request_data['DistSalesAnalysisReports']['rows'] == 'month') {

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
				} elseif ($request_data['DistSalesAnalysisReports']['rows'] == 'division') {
					if ($request_data['DistSalesAnalysisReports']['division_id']) {
						$conditions = array('Division.id' => $request_data['DistSalesAnalysisReports']['division_id']);
					} else {
						$conditions = array();
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
				} elseif ($request_data['DistSalesAnalysisReports']['rows'] == 'district') {
					$conditions = array();

					if ($request_data['DistSalesAnalysisReports']['division_id']) $conditions['District.division_id'] = $request_data['DistSalesAnalysisReports']['division_id'];

					if ($request_data['DistSalesAnalysisReports']['district_id']) $conditions['District.id'] = $request_data['DistSalesAnalysisReports']['district_id'];

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
				} elseif ($request_data['DistSalesAnalysisReports']['rows'] == 'thana') {
					$conditions = array();

					if ($request_data['DistSalesAnalysisReports']['division_id']) $conditions['District.division_id'] = $request_data['DistSalesAnalysisReports']['division_id'];

					if ($request_data['DistSalesAnalysisReports']['district_id']) $conditions['District.id'] = $request_data['DistSalesAnalysisReports']['district_id'];

					if ($request_data['DistSalesAnalysisReports']['thana_id']) $conditions['Thana.id'] = $request_data['DistSalesAnalysisReports']['thana_id'];

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
				} else {
					$rows_list = array('National');
				}

				$this->set(compact('rows_list'));
				//pr($rows_list);		
				//exit;
				//end for rows



				/*START FOR RESULT QUERY*/
				$date_from = date('Y-m-d', strtotime($request_data['DistSalesAnalysisReports']['date_from']));
				$date_to = date('Y-m-d', strtotime($request_data['DistSalesAnalysisReports']['date_to']));
				$rows 		= $request_data['DistSalesAnalysisReports']['rows'];
				$columns 	= $request_data['DistSalesAnalysisReports']['columns'];

				//program sales
				$p_outlet_ids = array();
				$where = '';


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


				if ($rows == 'month' || $rows == 'national') {
					//for month row
					if ($rows == 'month') {
						foreach ($rows_list as $row_key => $row_val) {
							//foreach($columns_list as $col_key => $col_val){					
							$conditions = array(
								//'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
								'DistMemo.gross_value >=' => 0,
								//'DistMemo.status !=' => 0,
							);

							if ($request_data['DistSalesAnalysisReports']['location_type_id']) {
								$conditions['DistMarket.location_type_id'] = $request_data['DistSalesAnalysisReports']['location_type_id'];
							}


							//add new
							//pr($request_data);
							if ($request_data['DistSalesAnalysisReports']['product_type']) {
								$conditions['Product.source'] = $request_data['DistSalesAnalysisReports']['product_type'];
							}

							if (@$request_data['DistSalesAnalysisReports']['sr_id']) {
								$conditions['DistMemo.sr_id'] = $request_data['DistSalesAnalysisReports']['sr_id'];
							}


							if ($request_data['DistSalesAnalysisReports']['product_id']) {
								$conditions['DistMemoDetail.product_id'] = $request_data['DistSalesAnalysisReports']['product_id'];
							}
							//end add new


							//for rows
							if ($rows == 'month') {
								$a_date =  $row_key;
								$month_first_day = date("Y-m-d", strtotime($a_date));
								$month_last_day = date("Y-m-t", strtotime($a_date));
								$conditions['DistMemo.memo_date BETWEEN ? and ? '] = array($month_first_day, $month_last_day);
							}


							if ($request_data['DistSalesAnalysisReports']['division_id']) {
								$conditions['Division.id'] = $request_data['DistSalesAnalysisReports']['division_id'];
							}

							//if($rows=='district')$conditions['District.id'] = $row_key;

							if ($request_data['DistSalesAnalysisReports']['district_id']) {
								$conditions['District.id'] = $request_data['DistSalesAnalysisReports']['district_id'];
							}

							if ($request_data['DistSalesAnalysisReports']['thana_id']) {
								$conditions['Thana.id'] = $request_data['DistSalesAnalysisReports']['thana_id'];
							}


							//for columns
							if ($columns == 'product') {
								$conditions['DistMemoDetail.product_id'] = $col_keys;
							}


							if ($columns == 'category') {
								$conditions['Product.product_category_id'] = $col_keys;
							}
							if ($columns != 'category' && $request_data['DistSalesAnalysisReports']['product_category_id']) {
								$conditions['Product.product_category_id'] = $request_data['DistSalesAnalysisReports']['product_category_id'];
							}

							if ($columns == 'brand') {
								$conditions['Product.brand_id'] = $col_keys;
							}
							if ($columns != 'brand' && $request_data['DistSalesAnalysisReports']['brand_id']) {
								$conditions['Product.brand_id'] = $request_data['DistSalesAnalysisReports']['brand_id'];
							}


							if ($columns == 'outlet_type') {
								$conditions['DistOutlet.category_id'] = $col_keys;
							}
							if ($columns != 'outlet_type' && $request_data['DistSalesAnalysisReports']['outlet_category_id']) {
								$conditions['DistOutlet.category_id'] = $request_data['DistSalesAnalysisReports']['outlet_category_id'];
							}


							if ($office_id) {
								$conditions['DistMemo.office_id'] = $office_id;
							}

							//$conditions['DistMemoDetail.price >']=0;

							//pr($conditions);	exit;




							if ($request_data['DistSalesAnalysisReports']['columns'] == 'category') {
								$q_results[$row_key] = $this->DistMemo->find('all', array(
									'conditions' => $conditions,
									'joins' => array(
										array(
											'alias' => 'DistMemoDetail',
											'table' => 'dist_memo_details',
											'type' => 'INNER',
											'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
										),
										array(
											'alias' => 'Product',
											'table' => 'products',
											'type' => 'INNER',
											'conditions' => 'DistMemoDetail.product_id = Product.id'
										),
										array(
											'alias' => 'ProductMeasurement',
											'table' => 'product_measurements',
											'type' => 'LEFT',
											'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														DistMemoDetail.measurement_unit_id
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
											'alias' => 'DistOutlet',
											'table' => 'dist_outlets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
										),
										array(
											'alias' => 'DistMarket',
											'table' => 'dist_markets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.market_id = DistMarket.id'
										),
										array(
											'alias' => 'DistRoute',
											'table' => 'dist_routes',
											'type' => 'INNER',
											'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
										),
										array(
											'alias' => 'Thana',
											'table' => 'thanas',
											'type' => 'INNER',
											'conditions' => 'DistRoute.thana_id = Thana.id'
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


									'fields' => array('
										 SUM(ROUND((ROUND((CASE
											WHEN DistMemoDetail.price > 0 THEN DistMemoDetail.sales_qty
										  END) * (CASE
											WHEN ProductMeasurement.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurement.qty_in_base
										  END), 0)) / (CASE
											WHEN ProductMeasurementSales.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurementSales.qty_in_base
										  END), 2, 1)) AS volume,
										  SUM(ROUND((ROUND((CASE
											WHEN DistMemoDetail.price = 0 THEN DistMemoDetail.sales_qty
										  END) * (CASE
											WHEN ProductMeasurement.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurement.qty_in_base
										  END), 0)) / (CASE
											WHEN ProductMeasurementSales.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurementSales.qty_in_base
										  END), 2, 1)) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc',  'Product.product_category_id'),
									'group' => array('Product.product_category_id'),

									'order' => array('Product.product_category_id asc'),

									'recursive' => -1
								));
								//pr($q_results);
								//exit;
								$q_results2[$row_key] = $this->DistMemo->find('all', array(
									'conditions' => $conditions,
									'joins' => array(
										array(
											'alias' => 'DistMemoDetail',
											'table' => 'dist_memo_details',
											'type' => 'INNER',
											'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
										),
										array(
											'alias' => 'Product',
											'table' => 'products',
											'type' => 'INNER',
											'conditions' => 'DistMemoDetail.product_id = Product.id'
										),
										array(
											'alias' => 'ProductMeasurement',
											'table' => 'product_measurements',
											'type' => 'LEFT',
											'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														DistMemoDetail.measurement_unit_id
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
											'alias' => 'DistOutlet',
											'table' => 'dist_outlets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
										),
										array(
											'alias' => 'DistMarket',
											'table' => 'dist_markets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.market_id = DistMarket.id'
										),
										array(
											'alias' => 'DistRoute',
											'table' => 'dist_routes',
											'type' => 'INNER',
											'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
										),
										array(
											'alias' => 'Thana',
											'table' => 'thanas',
											'type' => 'INNER',
											'conditions' => 'DistRoute.thana_id = Thana.id'
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

									'fields' => array('
										SUM(ROUND((ROUND((CASE
											WHEN DistMemoDetail.price > 0 THEN DistMemoDetail.sales_qty
										  END) * (CASE
											WHEN ProductMeasurement.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurement.qty_in_base
										  END), 0)) / (CASE
											WHEN ProductMeasurementSales.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurementSales.qty_in_base
										  END), 2, 1)) AS volume,
										  SUM(ROUND((ROUND((CASE
											WHEN DistMemoDetail.price = 0 THEN DistMemoDetail.sales_qty
										  END) * (CASE
											WHEN ProductMeasurement.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurement.qty_in_base
										  END), 0)) / (CASE
											WHEN ProductMeasurementSales.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurementSales.qty_in_base
										  END), 2, 1)) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc',  'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v', 'Product.product_category_id'),
									'group' => array('Product.product_category_id', 'DistMemoDetail.product_id, Product.cyp_cal, Product.cyp'),
									'order' => array('Product.product_category_id asc'),

									'recursive' => -1
								));
							} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'brand') {

								$q_results[$row_key] = $this->DistMemo->find('all', array(
									'conditions' => $conditions,
									'joins' => array(
										array(
											'alias' => 'DistMemoDetail',
											'table' => 'dist_memo_details',
											'type' => 'INNER',
											'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
										),
										array(
											'alias' => 'Product',
											'table' => 'products',
											'type' => 'INNER',
											'conditions' => 'DistMemoDetail.product_id = Product.id'
										),
										array(
											'alias' => 'ProductMeasurement',
											'table' => 'product_measurements',
											'type' => 'LEFT',
											'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														DistMemoDetail.measurement_unit_id
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
											'alias' => 'DistOutlet',
											'table' => 'dist_outlets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
										),
										array(
											'alias' => 'DistMarket',
											'table' => 'dist_markets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.market_id = DistMarket.id'
										),
										array(
											'alias' => 'DistRoute',
											'table' => 'dist_routes',
											'type' => 'INNER',
											'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
										),
										array(
											'alias' => 'Thana',
											'table' => 'thanas',
											'type' => 'INNER',
											'conditions' => 'DistRoute.thana_id = Thana.id'
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


									'fields' => array('
										SUM(ROUND((ROUND((CASE
											WHEN DistMemoDetail.price > 0 THEN DistMemoDetail.sales_qty
										END) * (CASE
											WHEN ProductMeasurement.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurement.qty_in_base
										END), 0)) / (CASE
											WHEN ProductMeasurementSales.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurementSales.qty_in_base
										END), 2, 1)) AS volume,
										SUM(ROUND((ROUND((CASE
											WHEN DistMemoDetail.price = 0 THEN DistMemoDetail.sales_qty
										END) * (CASE
											WHEN ProductMeasurement.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurement.qty_in_base
										END), 0)) / (CASE
											WHEN ProductMeasurementSales.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurementSales.qty_in_base
										END), 2, 1)) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc',  'Product.brand_id'),
									'group' => array('Product.brand_id'),

									'order' => array('Product.brand_id asc'),

									'recursive' => -1
								));
								//pr($q_results);
								//exit;
								$q_results2[$row_key] = $this->DistMemo->find('all', array(
									'conditions' => $conditions,
									'joins' => array(
										array(
											'alias' => 'DistMemoDetail',
											'table' => 'dist_memo_details',
											'type' => 'INNER',
											'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
										),
										array(
											'alias' => 'Product',
											'table' => 'products',
											'type' => 'INNER',
											'conditions' => 'DistMemoDetail.product_id = Product.id'
										),
										array(
											'alias' => 'ProductMeasurement',
											'table' => 'product_measurements',
											'type' => 'LEFT',
											'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														DistMemoDetail.measurement_unit_id
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
											'alias' => 'DistOutlet',
											'table' => 'dist_outlets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
										),
										array(
											'alias' => 'DistMarket',
											'table' => 'dist_markets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.market_id = DistMarket.id'
										),
										array(
											'alias' => 'DistRoute',
											'table' => 'dist_routes',
											'type' => 'INNER',
											'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
										),
										array(
											'alias' => 'Thana',
											'table' => 'thanas',
											'type' => 'INNER',
											'conditions' => 'DistRoute.thana_id = Thana.id'
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

									'fields' => array('
										SUM(ROUND((ROUND((CASE
												WHEN DistMemoDetail.price > 0 THEN DistMemoDetail.sales_qty
											END) * (CASE
												WHEN ProductMeasurement.qty_in_base IS NULL THEN 1
												ELSE ProductMeasurement.qty_in_base
											END), 0)) / (CASE
												WHEN ProductMeasurementSales.qty_in_base IS NULL THEN 1
												ELSE ProductMeasurementSales.qty_in_base
											END), 2, 1)) AS volume,
											SUM(ROUND((ROUND((CASE
												WHEN DistMemoDetail.price = 0 THEN DistMemoDetail.sales_qty
											END) * (CASE
												WHEN ProductMeasurement.qty_in_base IS NULL THEN 1
												ELSE ProductMeasurement.qty_in_base
											END), 0)) / (CASE
												WHEN ProductMeasurementSales.qty_in_base IS NULL THEN 1
												ELSE ProductMeasurementSales.qty_in_base
											END), 2, 1)) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc',  'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v', 'Product.brand_id'),
									'group' => array('Product.brand_id', 'DistMemoDetail.product_id, Product.cyp_cal, Product.cyp'),
									'order' => array('Product.brand_id asc'),

									'recursive' => -1
								));
							} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'outlet_type') {

								$q_results[$row_key] = $this->DistMemo->find('all', array(
									'conditions' => $conditions,
									'joins' => array(
										array(
											'alias' => 'DistMemoDetail',
											'table' => 'dist_memo_details',
											'type' => 'INNER',
											'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
										),
										array(
											'alias' => 'Product',
											'table' => 'products',
											'type' => 'INNER',
											'conditions' => 'DistMemoDetail.product_id = Product.id'
										),
										array(
											'alias' => 'ProductMeasurement',
											'table' => 'product_measurements',
											'type' => 'LEFT',
											'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														DistMemoDetail.measurement_unit_id
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
											'alias' => 'DistOutlet',
											'table' => 'dist_outlets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
										),
										array(
											'alias' => 'DistMarket',
											'table' => 'dist_markets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.market_id = DistMarket.id'
										),
										array(
											'alias' => 'DistRoute',
											'table' => 'dist_routes',
											'type' => 'INNER',
											'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
										),
										array(
											'alias' => 'Thana',
											'table' => 'thanas',
											'type' => 'INNER',
											'conditions' => 'DistRoute.thana_id = Thana.id'
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


									'fields' => array('
										SUM(ROUND((ROUND((CASE
												WHEN DistMemoDetail.price > 0 THEN DistMemoDetail.sales_qty
											END) * (CASE
												WHEN ProductMeasurement.qty_in_base IS NULL THEN 1
												ELSE ProductMeasurement.qty_in_base
											END), 0)) / (CASE
												WHEN ProductMeasurementSales.qty_in_base IS NULL THEN 1
												ELSE ProductMeasurementSales.qty_in_base
											END), 2, 1)) AS volume,
											SUM(ROUND((ROUND((CASE
												WHEN DistMemoDetail.price = 0 THEN DistMemoDetail.sales_qty
											END) * (CASE
												WHEN ProductMeasurement.qty_in_base IS NULL THEN 1
												ELSE ProductMeasurement.qty_in_base
											END), 0)) / (CASE
												WHEN ProductMeasurementSales.qty_in_base IS NULL THEN 1
												ELSE ProductMeasurementSales.qty_in_base
											END), 2, 1)) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc',  'DistOutlet.category_id'),
									'group' => array('DistOutlet.category_id'),

									'order' => array('DistOutlet.category_id asc'),

									'recursive' => -1
								));
								//pr($q_results);
								//exit;
								$q_results2[$row_key] = $this->DistMemo->find('all', array(
									'conditions' => $conditions,
									'joins' => array(
										array(
											'alias' => 'DistMemoDetail',
											'table' => 'dist_memo_details',
											'type' => 'INNER',
											'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
										),
										array(
											'alias' => 'Product',
											'table' => 'products',
											'type' => 'INNER',
											'conditions' => 'DistMemoDetail.product_id = Product.id'
										),
										array(
											'alias' => 'ProductMeasurement',
											'table' => 'product_measurements',
											'type' => 'LEFT',
											'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														DistMemoDetail.measurement_unit_id
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
											'alias' => 'DistOutlet',
											'table' => 'dist_outlets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
										),
										array(
											'alias' => 'DistMarket',
											'table' => 'dist_markets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.market_id = DistMarket.id'
										),
										array(
											'alias' => 'DistRoute',
											'table' => 'dist_routes',
											'type' => 'INNER',
											'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
										),
										array(
											'alias' => 'Thana',
											'table' => 'thanas',
											'type' => 'INNER',
											'conditions' => 'DistRoute.thana_id = Thana.id'
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

									'fields' => array('
										SUM(ROUND((ROUND((CASE
											WHEN DistMemoDetail.price > 0 THEN DistMemoDetail.sales_qty
										END) * (CASE
											WHEN ProductMeasurement.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurement.qty_in_base
										END), 0)) / (CASE
											WHEN ProductMeasurementSales.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurementSales.qty_in_base
										END), 2, 1)) AS volume,
										SUM(ROUND((ROUND((CASE
											WHEN DistMemoDetail.price = 0 THEN DistMemoDetail.sales_qty
										END) * (CASE
											WHEN ProductMeasurement.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurement.qty_in_base
										END), 0)) / (CASE
											WHEN ProductMeasurementSales.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurementSales.qty_in_base
										END), 2, 1)) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc',  'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v', 'DistOutlet.category_id'),
									'group' => array('DistOutlet.category_id', 'DistMemoDetail.product_id, Product.cyp_cal, Product.cyp'),
									'order' => array('DistOutlet.category_id asc'),

									'recursive' => -1
								));
							} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'national') {
								$q_results[$row_key] = $this->DistMemo->find('all', array(
									'conditions' => $conditions,
									'joins' => array(
										array(
											'alias' => 'DistMemoDetail',
											'table' => 'dist_memo_details',
											'type' => 'INNER',
											'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
										),
										array(
											'alias' => 'Product',
											'table' => 'products',
											'type' => 'INNER',
											'conditions' => 'DistMemoDetail.product_id = Product.id'
										),
										array(
											'alias' => 'ProductMeasurement',
											'table' => 'product_measurements',
											'type' => 'LEFT',
											'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														DistMemoDetail.measurement_unit_id
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
											'alias' => 'DistOutlet',
											'table' => 'dist_outlets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
										),
										array(
											'alias' => 'DistMarket',
											'table' => 'dist_markets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.market_id = DistMarket.id'
										),
										array(
											'alias' => 'DistRoute',
											'table' => 'dist_routes',
											'type' => 'INNER',
											'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
										),
										array(
											'alias' => 'Thana',
											'table' => 'thanas',
											'type' => 'INNER',
											'conditions' => 'DistRoute.thana_id = Thana.id'
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


									'fields' => array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume, SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc'),
									'recursive' => -1
								));
								//pr($q_results);
								//exit;
								$q_results2[$row_key] = $this->DistMemo->find('all', array(
									'conditions' => $conditions,
									'joins' => array(
										array(
											'alias' => 'DistMemoDetail',
											'table' => 'dist_memo_details',
											'type' => 'INNER',
											'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
										),
										array(
											'alias' => 'Product',
											'table' => 'products',
											'type' => 'INNER',
											'conditions' => 'DistMemoDetail.product_id = Product.id'
										),
										array(
											'alias' => 'ProductMeasurement',
											'table' => 'product_measurements',
											'type' => 'LEFT',
											'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														DistMemoDetail.measurement_unit_id
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
											'alias' => 'DistOutlet',
											'table' => 'dist_outlets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
										),
										array(
											'alias' => 'DistMarket',
											'table' => 'dist_markets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.market_id = DistMarket.id'
										),
										array(
											'alias' => 'DistRoute',
											'table' => 'dist_routes',
											'type' => 'INNER',
											'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
										),
										array(
											'alias' => 'Thana',
											'table' => 'thanas',
											'type' => 'INNER',
											'conditions' => 'DistRoute.thana_id = Thana.id'
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

									'fields' => array('SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume, SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc',  'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v'),
									'group' => array('DistMemoDetail.product_id, Product.cyp_cal, Product.cyp'),
									'order' => array('DistMemoDetail.product_id asc'),

									'recursive' => -1
								));
							} else {
								//pr('hello');pr($conditions);exit;
								$q_results[$row_key] = $this->DistMemo->find('all', array(
									'conditions' => $conditions,
									'joins' => array(
										array(
											'alias' => 'DistMemoDetail',
											'table' => 'dist_memo_details',
											'type' => 'INNER',
											'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
										),
										array(
											'alias' => 'Product',
											'table' => 'products',
											'type' => 'INNER',
											'conditions' => 'DistMemoDetail.product_id = Product.id'
										),
										array(
											'alias' => 'ProductMeasurement',
											'table' => 'product_measurements',
											'type' => 'LEFT',
											'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														DistMemoDetail.measurement_unit_id
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
											'alias' => 'DistOutlet',
											'table' => 'dist_outlets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
										),
										array(
											'alias' => 'DistMarket',
											'table' => 'dist_markets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.market_id = DistMarket.id'
										),
										array(
											'alias' => 'DistRoute',
											'table' => 'dist_routes',
											'type' => 'INNER',
											'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
										),
										array(
											'alias' => 'Thana',
											'table' => 'thanas',
											'type' => 'INNER',
											'conditions' => 'DistRoute.thana_id = Thana.id'
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

									//'fields' => array('sum(DistMemoDetail.sales_qty) AS volume, sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc'),
									'fields' => array('SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc',  'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v', 'Product.order'),
									'group' => array('DistMemoDetail.product_id, Product.cyp_cal, Product.cyp', 'Product.order'),
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
								'DistMemo.gross_value >=' => 0,
								//'DistMemo.status !=' => 0,
							);


							if ($request_data['DistSalesAnalysisReports']['location_type_id']) {
								$conditions['DistMarket.location_type_id'] = $request_data['DistSalesAnalysisReports']['location_type_id'];
							}

							//add new
							//pr($request_data);
							if ($request_data['DistSalesAnalysisReports']['product_type']) {
								$conditions['Product.source'] = $request_data['DistSalesAnalysisReports']['product_type'];
							}

							if (@$request_data['DistSalesAnalysisReports']['sr_id']) {
								$conditions['DistMemo.sr_id'] = $request_data['DistSalesAnalysisReports']['sr_id'];
							}


							if ($request_data['DistSalesAnalysisReports']['product_id']) {
								$conditions['DistMemoDetail.product_id'] = $request_data['DistSalesAnalysisReports']['product_id'];
							}
							//end add new


							//for rows
							$conditions['DistMemo.memo_date BETWEEN ? and ? '] = array($date_from, $date_to);


							/*if($rows=='so')$conditions['Memo.sales_person_id'] = $row_key;
								if($rows=='territory')$conditions['Memo.territory_id'] = $row_key;
								if($rows=='area')$conditions['Memo.office_id'] = $row_key;*/

							if ($rows == 'division') {
								$conditions['Division.id'] = $row_key;
							} elseif ($request_data['DistSalesAnalysisReports']['division_id']) {
								$conditions['Division.id'] = $request_data['DistSalesAnalysisReports']['division_id'];
							}

							//if($rows=='district')$conditions['District.id'] = $row_key;

							if ($rows == 'district') {
								$conditions['District.id'] = $row_key;
							} elseif ($request_data['DistSalesAnalysisReports']['district_id']) {
								$conditions['District.id'] = $request_data['DistSalesAnalysisReports']['district_id'];
							}

							if ($request_data['DistSalesAnalysisReports']['thana_id']) {
								$conditions['Thana.id'] = $request_data['DistSalesAnalysisReports']['thana_id'];
							}


							//for columns
							if ($columns == 'product') {
								$conditions['DistMemoDetail.product_id'] = $col_keys;
							}


							if ($columns == 'category') {
								$conditions['Product.product_category_id'] = $col_keys;
							}
							if ($columns != 'category' && $request_data['DistSalesAnalysisReports']['product_category_id']) {
								$conditions['Product.product_category_id'] = $request_data['DistSalesAnalysisReports']['product_category_id'];
							}

							if ($columns == 'brand') {
								$conditions['Product.brand_id'] = $col_keys;
							}
							if ($columns != 'brand' && $request_data['DistSalesAnalysisReports']['brand_id']) {
								$conditions['Product.brand'] = $request_data['DistSalesAnalysisReports']['brand_id'];
							}


							if ($office_id) {
								$conditions['DistMemo.office_id'] = $office_id;
							}

							if ($outlet_category_ids) {
								$conditions['DistOutlet.category_id'] = $outlet_category_ids;
							}

							//$conditions['DistMemoDetail.price >']=0;

							//pr($conditions);
							//exit;

							if ($request_data['DistSalesAnalysisReports']['columns'] == 'category') {
								$q_results[$row_key] = $this->DistMemo->find('all', array(
									'conditions' => $conditions,
									'joins' => array(
										array(
											'alias' => 'DistMemoDetail',
											'table' => 'dist_memo_details',
											'type' => 'INNER',
											'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
										),
										array(
											'alias' => 'Product',
											'table' => 'products',
											'type' => 'INNER',
											'conditions' => 'DistMemoDetail.product_id = Product.id'
										),
										array(
											'alias' => 'ProductMeasurement',
											'table' => 'product_measurements',
											'type' => 'LEFT',
											'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														DistMemoDetail.measurement_unit_id
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
											'alias' => 'DistOutlet',
											'table' => 'dist_outlets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
										),
										array(
											'alias' => 'DistMarket',
											'table' => 'dist_markets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.market_id = DistMarket.id'
										),
										array(
											'alias' => 'DistRoute',
											'table' => 'dist_routes',
											'type' => 'INNER',
											'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
										),
										array(
											'alias' => 'Thana',
											'table' => 'thanas',
											'type' => 'INNER',
											'conditions' => 'DistRoute.thana_id = Thana.id'
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


									'fields' => array('
											SUM(ROUND((ROUND((CASE
											WHEN DistMemoDetail.price > 0 THEN DistMemoDetail.sales_qty
										  END) * (CASE
											WHEN ProductMeasurement.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurement.qty_in_base
										  END), 0)) / (CASE
											WHEN ProductMeasurementSales.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurementSales.qty_in_base
										  END), 2, 1)) AS volume,
										  SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc',  'Product.product_category_id'),
									'group' => array('Product.product_category_id'),

									'order' => array('Product.product_category_id asc'),

									'recursive' => -1
								));
								//pr($q_results);
								//exit;
								$q_results2[$row_key] = $this->DistMemo->find('all', array(
									'conditions' => $conditions,
									'joins' => array(
										array(
											'alias' => 'DistMemoDetail',
											'table' => 'dist_memo_details',
											'type' => 'INNER',
											'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
										),
										array(
											'alias' => 'Product',
											'table' => 'products',
											'type' => 'INNER',
											'conditions' => 'DistMemoDetail.product_id = Product.id'
										),
										array(
											'alias' => 'ProductMeasurement',
											'table' => 'product_measurements',
											'type' => 'LEFT',
											'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														DistMemoDetail.measurement_unit_id
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
											'alias' => 'DistOutlet',
											'table' => 'dist_outlets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
										),
										array(
											'alias' => 'DistMarket',
											'table' => 'dist_markets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.market_id = DistMarket.id'
										),
										array(
											'alias' => 'DistRoute',
											'table' => 'dist_routes',
											'type' => 'INNER',
											'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
										),
										array(
											'alias' => 'Thana',
											'table' => 'thanas',
											'type' => 'INNER',
											'conditions' => 'DistRoute.thana_id = Thana.id'
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

									'fields' => array('SUM(ROUND((ROUND((CASE
											WHEN DistMemoDetail.price > 0 THEN DistMemoDetail.sales_qty
										  END) * (CASE
											WHEN ProductMeasurement.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurement.qty_in_base
										  END), 0)) / (CASE
											WHEN ProductMeasurementSales.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurementSales.qty_in_base
										  END), 2, 1)) AS volume, SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc',  'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v', 'Product.product_category_id'),
									'group' => array('Product.product_category_id', 'DistMemoDetail.product_id, Product.cyp_cal, Product.cyp'),
									'order' => array('Product.product_category_id asc'),

									'recursive' => -1
								));
							} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'brand') {



								$q_results[$row_key] = $this->DistMemo->find('all', array(
									'conditions' => $conditions,
									'joins' => array(
										array(
											'alias' => 'DistMemoDetail',
											'table' => 'dist_memo_details',
											'type' => 'INNER',
											'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
										),
										array(
											'alias' => 'Product',
											'table' => 'products',
											'type' => 'INNER',
											'conditions' => 'DistMemoDetail.product_id = Product.id'
										),
										array(
											'alias' => 'ProductMeasurement',
											'table' => 'product_measurements',
											'type' => 'LEFT',
											'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														DistMemoDetail.measurement_unit_id
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
											'alias' => 'DistOutlet',
											'table' => 'dist_outlets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
										),
										array(
											'alias' => 'DistMarket',
											'table' => 'dist_markets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.market_id = DistMarket.id'
										),
										array(
											'alias' => 'DistRoute',
											'table' => 'dist_routes',
											'type' => 'INNER',
											'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
										),
										array(
											'alias' => 'Thana',
											'table' => 'thanas',
											'type' => 'INNER',
											'conditions' => 'DistRoute.thana_id = Thana.id'
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


									'fields' => array('SUM(ROUND((ROUND((CASE
											WHEN DistMemoDetail.price > 0 THEN DistMemoDetail.sales_qty
										  END) * (CASE
											WHEN ProductMeasurement.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurement.qty_in_base
										  END), 0)) / (CASE
											WHEN ProductMeasurementSales.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurementSales.qty_in_base
										  END), 2, 1)) AS volume, SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc',  'Product.brand_id'),
									'group' => array('Product.brand_id'),

									'order' => array('Product.brand_id asc'),

									'recursive' => -1
								));
								//pr($q_results);
								//exit;
								$q_results2[$row_key] = $this->DistMemo->find('all', array(
									'conditions' => $conditions,
									'joins' => array(
										array(
											'alias' => 'DistMemoDetail',
											'table' => 'dist_memo_details',
											'type' => 'INNER',
											'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
										),
										array(
											'alias' => 'Product',
											'table' => 'products',
											'type' => 'INNER',
											'conditions' => 'DistMemoDetail.product_id = Product.id'
										),
										array(
											'alias' => 'ProductMeasurement',
											'table' => 'product_measurements',
											'type' => 'LEFT',
											'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														DistMemoDetail.measurement_unit_id
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
											'alias' => 'DistOutlet',
											'table' => 'dist_outlets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
										),
										array(
											'alias' => 'DistMarket',
											'table' => 'dist_markets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.market_id = DistMarket.id'
										),
										array(
											'alias' => 'DistRoute',
											'table' => 'dist_routes',
											'type' => 'INNER',
											'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
										),
										array(
											'alias' => 'Thana',
											'table' => 'thanas',
											'type' => 'INNER',
											'conditions' => 'DistRoute.thana_id = Thana.id'
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

									'fields' => array('SUM(ROUND((ROUND((CASE
											WHEN DistMemoDetail.price > 0 THEN DistMemoDetail.sales_qty
										  END) * (CASE
											WHEN ProductMeasurement.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurement.qty_in_base
										  END), 0)) / (CASE
											WHEN ProductMeasurementSales.qty_in_base IS NULL THEN 1
											ELSE ProductMeasurementSales.qty_in_base
										  END), 2, 1)) AS volume, SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc',  'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v', 'Product.brand_id'),
									'group' => array('Product.brand_id', 'DistMemoDetail.product_id, Product.cyp_cal, Product.cyp'),
									'order' => array('Product.brand_id asc'),

									'recursive' => -1
								));
							} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'outlet_type') {



								$q_results[$row_key] = $this->DistMemo->find('all', array(
									'conditions' => $conditions,
									'joins' => array(
										array(
											'alias' => 'DistMemoDetail',
											'table' => 'dist_memo_details',
											'type' => 'INNER',
											'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
										),
										array(
											'alias' => 'Product',
											'table' => 'products',
											'type' => 'INNER',
											'conditions' => 'DistMemoDetail.product_id = Product.id'
										),
										array(
											'alias' => 'ProductMeasurement',
											'table' => 'product_measurements',
											'type' => 'LEFT',
											'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														DistMemoDetail.measurement_unit_id
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
											'alias' => 'DistOutlet',
											'table' => 'dist_outlets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
										),
										array(
											'alias' => 'DistMarket',
											'table' => 'dist_markets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.market_id = DistMarket.id'
										),
										array(
											'alias' => 'DistRoute',
											'table' => 'dist_routes',
											'type' => 'INNER',
											'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
										),
										array(
											'alias' => 'Thana',
											'table' => 'thanas',
											'type' => 'INNER',
											'conditions' => 'DistRoute.thana_id = Thana.id'
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


									'fields' => array('SUM(
										ROUND((ROUND(
												(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
									(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
											,2,1)
									) AS volume, SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.bonus_qty) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc',  'DistOutlet.category_id'),
									'group' => array('DistOutlet.category_id'),

									'order' => array('DistOutlet.category_id asc'),

									'recursive' => -1
								));
								//pr($q_results);
								//exit;
								$q_results2[$row_key] = $this->DistMemo->find('all', array(
									'conditions' => $conditions,
									'joins' => array(
										array(
											'alias' => 'DistMemoDetail',
											'table' => 'dist_memo_details',
											'type' => 'INNER',
											'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
										),
										array(
											'alias' => 'Product',
											'table' => 'products',
											'type' => 'INNER',
											'conditions' => 'DistMemoDetail.product_id = Product.id'
										),
										array(
											'alias' => 'ProductMeasurement',
											'table' => 'product_measurements',
											'type' => 'LEFT',
											'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														DistMemoDetail.measurement_unit_id
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
											'alias' => 'DistOutlet',
											'table' => 'dist_outlets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
										),
										array(
											'alias' => 'DistMarket',
											'table' => 'dist_markets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.market_id = DistMarket.id'
										),
										array(
											'alias' => 'DistRoute',
											'table' => 'dist_routes',
											'type' => 'INNER',
											'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
										),
										array(
											'alias' => 'Thana',
											'table' => 'thanas',
											'type' => 'INNER',
											'conditions' => 'DistRoute.thana_id = Thana.id'
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

									'fields' => array('SUM(
										ROUND((ROUND(
												(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
									(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
											,2,1)
									) AS volume, SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc',  'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v', 'DistOutlet.category_id'),
									'group' => array('DistOutlet.category_id', 'DistMemoDetail.product_id, Product.cyp_cal, Product.cyp'),
									'order' => array('DistOutlet.category_id asc'),

									'recursive' => -1
								));
							} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'national') {


								$q_results[] = $this->DistMemo->find('all', array(
									'conditions' => $conditions,
									'joins' => array(
										array(
											'alias' => 'DistMemoDetail',
											'table' => 'dist_memo_details',
											'type' => 'INNER',
											'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
										),
										array(
											'alias' => 'Product',
											'table' => 'products',
											'type' => 'INNER',
											'conditions' => 'DistMemoDetail.product_id = Product.id'
										),
										array(
											'alias' => 'ProductMeasurement',
											'table' => 'product_measurements',
											'type' => 'LEFT',
											'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														DistMemoDetail.measurement_unit_id
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
											'alias' => 'DistOutlet',
											'table' => 'dist_outlets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
										),
										array(
											'alias' => 'DistMarket',
											'table' => 'dist_markets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.market_id = DistMarket.id'
										),
										array(
											'alias' => 'DistRoute',
											'table' => 'dist_routes',
											'type' => 'INNER',
											'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
										),
										array(
											'alias' => 'Thana',
											'table' => 'thanas',
											'type' => 'INNER',
											'conditions' => 'DistRoute.thana_id = Thana.id'
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


									'fields' => array('SUM(
										ROUND((ROUND(
												(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
									(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
											,2,1)
									) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus, sum(DistMemoDetail.bonus_qty) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc'),
									'recursive' => -1
								));
								//pr($q_results);
								//exit;
								$q_results2[] = $this->DistMemo->find('all', array(
									'conditions' => $conditions,
									'joins' => array(
										array(
											'alias' => 'DistMemoDetail',
											'table' => 'dist_memo_details',
											'type' => 'INNER',
											'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
										),
										array(
											'alias' => 'Product',
											'table' => 'products',
											'type' => 'INNER',
											'conditions' => 'DistMemoDetail.product_id = Product.id'
										),
										array(
											'alias' => 'ProductMeasurement',
											'table' => 'product_measurements',
											'type' => 'LEFT',
											'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														DistMemoDetail.measurement_unit_id
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
											'alias' => 'DistOutlet',
											'table' => 'dist_outlets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
										),
										array(
											'alias' => 'DistMarket',
											'table' => 'dist_markets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.market_id = DistMarket.id'
										),
										array(
											'alias' => 'DistRoute',
											'table' => 'dist_routes',
											'type' => 'INNER',
											'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
										),
										array(
											'alias' => 'Thana',
											'table' => 'thanas',
											'type' => 'INNER',
											'conditions' => 'DistRoute.thana_id = Thana.id'
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

									'fields' => array('SUM(
										ROUND((ROUND(
												(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
									(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
											,2,1)
									) AS volume,
											SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc',  'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v'),
									'group' => array('DistMemoDetail.product_id, Product.cyp_cal, Product.cyp'),
									'recursive' => -1
								));
							} else {
								//pr($conditions);
								$q_results[$row_key] = $this->DistMemo->find('all', array(
									'conditions' => $conditions,
									'joins' => array(
										array(
											'alias' => 'DistMemoDetail',
											'table' => 'dist_memo_details',
											'type' => 'INNER',
											'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
										),
										array(
											'alias' => 'Product',
											'table' => 'products',
											'type' => 'INNER',
											'conditions' => 'DistMemoDetail.product_id = Product.id'
										),
										array(
											'alias' => 'ProductMeasurement',
											'table' => 'product_measurements',
											'type' => 'LEFT',
											'conditions' => 'Product.id = ProductMeasurement.product_id AND 
													CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
													ELSE 
														DistMemoDetail.measurement_unit_id
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
											'alias' => 'DistOutlet',
											'table' => 'dist_outlets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
										),
										array(
											'alias' => 'DistMarket',
											'table' => 'dist_markets',
											'type' => 'INNER',
											'conditions' => 'DistMemo.market_id = DistMarket.id'
										),
										array(
											'alias' => 'DistRoute',
											'table' => 'dist_routes',
											'type' => 'INNER',
											'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
										),
										array(
											'alias' => 'Thana',
											'table' => 'thanas',
											'type' => 'INNER',
											'conditions' => 'DistRoute.thana_id = Thana.id'
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
									//'fields' => array('Memo.id', 'DistMemoDetail.id', 'Territory.id', 'Outlet.id', 'Outlet.name', 'Market.id', 'Thana.id', 'District.name', 'Division.name'),
									'fields' => array('SUM(
										ROUND((ROUND(
												(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
									(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
											,2,1)
									) AS volume, 
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v', 'Product.order'),
									'group' => array('DistMemoDetail.product_id', 'Product.cyp_cal', 'Product.cyp', 'Product.order'),
									'order' => array('Product.order asc'),
									'recursive' => -1
								));

								//echo $this->DistMemo->getLastQuery();exit;

								//pr($q_results);exit;

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
						'DistMemo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
						'DistMemo.gross_value >=' => 0,
						//'DistMemo.status !=' => 0,
					);

					if ($request_data['DistSalesAnalysisReports']['location_type_id']) {
						$conditions['DistMarket.location_type_id'] = $request_data['DistSalesAnalysisReports']['location_type_id'];
					}

					//add new
					//pr($request_data);
					if ($request_data['DistSalesAnalysisReports']['product_type']) {
						$conditions['Product.source'] = $request_data['DistSalesAnalysisReports']['product_type'];
					}

					if (@$request_data['DistSalesAnalysisReports']['sr_id']) {
						$conditions['DistMemo.sr_id'] = $request_data['DistSalesAnalysisReports']['sr_id'];
					}


					if ($request_data['DistSalesAnalysisReports']['product_id']) {
						$conditions['DistMemoDetail.product_id'] = $request_data['DistSalesAnalysisReports']['product_id'];
					}
					//end add new

					//for rows
					if ($rows == 'sr') {

						$conditions['DistMemo.sr_id'] = $row_keys;
					}

					if ($rows == 'dist') $conditions['DistMemo.distributor_id'] = $row_keys;

					if ($rows == 'tso') $conditions['DistMemo.tso_id'] = $row_keys;
					if ($rows == 'ae') $conditions['DistAE.id'] = $row_keys;
					if ($rows == 'territory') $conditions['DistMemo.territory_id'] = $row_keys;

					if ($rows == 'area') $conditions['DistMemo.office_id'] = $row_keys;





					if ($rows == 'division') {
						$conditions['Division.id'] = $row_keys;
					} elseif ($request_data['DistSalesAnalysisReports']['division_id']) {
						$conditions['Division.id'] = $request_data['DistSalesAnalysisReports']['division_id'];
					}

					if ($rows == 'district') {
						$conditions['District.id'] = $row_keys;
					} elseif ($request_data['DistSalesAnalysisReports']['district_id']) {
						$conditions['District.id'] = $request_data['DistSalesAnalysisReports']['district_id'];
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
					} elseif ($request_data['DistSalesAnalysisReports']['thana_id']) {
						$conditions['Thana.id'] = $request_data['DistSalesAnalysisReports']['thana_id'];
					}

					//for thana option
					if ($request_data['DistSalesAnalysisReports']['thana_id']) {
						$conditions['Thana.id'] = $request_data['DistSalesAnalysisReports']['thana_id'];
					}


					//for columns
					if ($columns == 'product') {
						$conditions['DistMemoDetail.product_id'] = $col_keys;
					}

					if ($columns == 'category') {
						$conditions['Product.product_category_id'] = $col_keys;
					}
					if ($columns != 'category' && $request_data['DistSalesAnalysisReports']['product_category_id']) {
						$conditions['Product.product_category_id'] = $request_data['DistSalesAnalysisReports']['product_category_id'];
					}


					if ($columns == 'brand') {
						$conditions['Product.brand_id'] = $col_keys;
					}
					if ($columns != 'brand' && $request_data['DistSalesAnalysisReports']['brand_id']) {
						$conditions['Product.brand_id'] = $request_data['DistSalesAnalysisReports']['brand_id'];
					}


					if ($columns == 'outlet_type') {
						$conditions['DistOutlet.category_id'] = $col_keys;
					}
					if ($columns != 'outlet_type' && $request_data['DistSalesAnalysisReports']['outlet_category_id']) {
						$conditions['DistOutlet.category_id'] = $request_data['DistSalesAnalysisReports']['outlet_category_id'];
					}



					if ($office_id) {
						$conditions['DistMemo.office_id'] = $office_id;
					}



					$fields = array();
					$group = array();

					$fields2 = array();
					$group2 = array();

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'sr') {
						if ($request_data['DistSalesAnalysisReports']['columns'] == 'product') {
							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'sr_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group = array('sr_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'category') {

							$fields = array('										
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistMemo.sr_id', 'Product.product_category_id');
							$group = array('sr_id', 'Product.product_category_id');

							$fields2 = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'sr_id', 'Product.product_category_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('DistMemo.sr_id', 'Product.product_category_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'brand') {

							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume, 
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistMemo.sr_id', 'Product.brand_id');
							$group = array('sr_id', 'Product.brand_id');

							$fields2 = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'sr_id', 'Product.brand_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('DistMemo.sr_id', 'Product.brand_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'outlet_type') {

							$fields = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'sr_id', 'DistOutlet.category_id');
							$group = array('sr_id', 'DistOutlet.category_id');

							$fields2 = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'sr_id', 'DistOutlet.category_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('sr_id', 'DistOutlet.category_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'national') {
							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'sr_id');
							$group = array('sr_id');

							$fields2 = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'sr_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('sr_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						}
					}


					if ($request_data['DistSalesAnalysisReports']['rows'] == 'tso') {
						if ($request_data['DistSalesAnalysisReports']['columns'] == 'product') {
							$fields = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'tso_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group = array('tso_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'category') {

							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistMemo.tso_id', 'Product.product_category_id');
							$group = array('tso_id', 'Product.product_category_id');

							$fields2 = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'tso_id', 'Product.product_category_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('DistMemo.tso_id', 'Product.product_category_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'brand') {

							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistMemo.tso_id', 'Product.brand_id');
							$group = array('tso_id', 'Product.brand_id');

							$fields2 = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'tso_id', 'Product.brand_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('DistMemo.tso_id', 'Product.brand_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'outlet_type') {

							$fields = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'tso_id', 'DistOutlet.category_id');
							$group = array('tso_id', 'DistOutlet.category_id');

							$fields2 = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'tso_id', 'DistOutlet.category_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('tso_id', 'DistOutlet.category_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'national') {
							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'tso_id');
							$group = array('tso_id');

							$fields2 = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'tso_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('tso_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						}
					}


					if ($request_data['DistSalesAnalysisReports']['rows'] == 'dist') {
						if ($request_data['DistSalesAnalysisReports']['columns'] == 'product') {
							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'distributor_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group = array('distributor_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'category') {
							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistMemo.distributor_id', 'Product.product_category_id');
							$group = array('distributor_id', 'Product.product_category_id');

							$fields2 = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'distributor_id', 'Product.product_category_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('DistMemo.distributor_id', 'Product.product_category_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'brand') {

							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistMemo.distributor_id', 'Product.brand_id');
							$group = array('distributor_id', 'Product.brand_id');

							$fields2 = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'distributor_id', 'Product.brand_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('DistMemo.distributor_id', 'Product.brand_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'outlet_type') {

							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'distributor_id', 'DistOutlet.category_id');
							$group = array('distributor_id', 'DistOutlet.category_id');

							$fields2 = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'distributor_id', 'DistOutlet.category_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('distributor_id', 'DistOutlet.category_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'national') {
							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume, 
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'distributor_id');
							$group = array('distributor_id');

							$fields2 = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus, sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'distributor_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('distributor_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						}
					}


					if ($request_data['DistSalesAnalysisReports']['rows'] == 'ae') {
						if ($request_data['DistSalesAnalysisReports']['columns'] == 'product') {
							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistAE.id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group = array('DistAE.id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'category') {

							$fields = array('sum(DistMemoDetail.sales_qty) AS volume, 
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistAE.id', 'Product.product_category_id');
							$group = array('DistAE.id', 'Product.product_category_id');

							$fields2 = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistAE.id', 'Product.product_category_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('DistAE.id', 'Product.product_category_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'brand') {

							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistAE.id', 'Product.brand_id');
							$group = array('DistAE.id', 'Product.brand_id');

							$fields2 = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistAE.id', 'Product.brand_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('DistAE.id', 'Product.brand_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'outlet_type') {

							$fields = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistAE.id', 'DistOutlet.category_id');
							$group = array('DistAE.id', 'DistOutlet.category_id');

							$fields2 = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistAE.id', 'DistOutlet.category_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('DistAE.id', 'DistOutlet.category_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'national') {
							$fields = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistAE.id');
							$group = array('DistAE.id');

							$fields2 = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistAE.id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('DistAE.id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						}
					}



					if ($request_data['DistSalesAnalysisReports']['rows'] == 'territory') {
						if ($request_data['DistSalesAnalysisReports']['columns'] == 'product') {
							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'territory_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group = array('DistMemo.territory_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'category') {

							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistMemo.territory_id', 'Product.product_category_id');
							$group = array('DistMemo.territory_id', 'Product.product_category_id');

							$fields2 = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'territory_id', 'Product.product_category_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('DistMemo.territory_id', 'Product.product_category_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'brand') {

							$fields = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistMemo.territory_id', 'Product.brand_id');
							$group = array('DistMemo.territory_id', 'Product.brand_id');

							$fields2 = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'territory_id', 'Product.brand_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('DistMemo.territory_id', 'Product.brand_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'outlet_type') {

							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'territory_id', 'DistOutlet.category_id');
							$group = array('DistMemo.territory_id', 'DistOutlet.category_id');

							$fields2 = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'territory_id', 'DistOutlet.category_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('DistMemo.territory_id', 'DistOutlet.category_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'national') {
							$fields = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'territory_id');
							$group = array('DistMemo.territory_id');

							$fields2 = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'territory_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('DistMemo.territory_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						}
					}



					if ($request_data['DistSalesAnalysisReports']['rows'] == 'area') {
						if ($request_data['DistSalesAnalysisReports']['columns'] == 'product') {
							$fields = array('
									SUM(
										ROUND((ROUND(
												(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
											,2,1)
									) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistMemo.office_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group = array('DistMemo.office_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'category') {
							$fields = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistMemo.office_id', 'Product.product_category_id');
							$group = array('DistMemo.office_id', 'Product.product_category_id');

							$fields2 = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistMemo.office_id', 'Product.product_category_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('DistMemo.office_id', 'Product.product_category_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'brand') {
							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistMemo.office_id', 'Product.brand_id');
							$group = array('DistMemo.office_id', 'Product.brand_id');

							$fields2 = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistMemo.office_id', 'Product.brand_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('DistMemo.office_id', 'Product.brand_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'outlet_type') {
							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistMemo.office_id', 'DistOutlet.category_id');
							$group = array('DistMemo.office_id', 'DistOutlet.category_id');

							$fields2 = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistMemo.office_id', 'DistOutlet.category_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('DistMemo.office_id', 'DistOutlet.category_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'national') {
							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistMemo.office_id');
							$group = array('DistMemo.office_id');

							$fields2 = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'DistMemo.office_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('DistMemo.office_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'division') {
						if ($request_data['DistSalesAnalysisReports']['columns'] == 'product') {
							$fields = array('
									SUM(
										ROUND((ROUND(
												(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
											,2,1)
									) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'Division.id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group = array('Division.id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'category') {
							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'Division.id', 'Product.product_category_id');
							$group = array('Division.id', 'Product.product_category_id');

							$fields2 = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'Division.id', 'Product.product_category_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('Division.id', 'Product.product_category_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'brand') {
							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'Division.id', 'Product.brand_id');
							$group = array('Division.id', 'Product.brand_id');

							$fields2 = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'Division.id', 'Product.brand_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('Division.id', 'Product.brand_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'outlet_type') {
							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'Division.id', 'DistOutlet.category_id');
							$group = array('Division.id', 'DistOutlet.category_id');

							$fields2 = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'Division.id', 'DistOutlet.category_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('Division.id', 'DistOutlet.category_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'national') {
							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'Division.id');
							$group = array('Division.id');

							$fields2 = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'Division.id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('Division.id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						}
					}


					if ($request_data['DistSalesAnalysisReports']['rows'] == 'district') {
						if ($request_data['DistSalesAnalysisReports']['columns'] == 'product') {
							$fields = array('
									SUM(
										ROUND((ROUND(
												(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
											,2,1)
									) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'District.id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group = array('District.id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'category') {
							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'District.id', 'Product.product_category_id');
							$group = array('District.id', 'Product.product_category_id');

							$fields2 = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'District.id', 'Product.product_category_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('District.id', 'Product.product_category_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'brand') {
							$fields = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
									 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'District.id', 'Product.brand_id');
							$group = array('District.id', 'Product.brand_id');

							$fields2 = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
									 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'District.id', 'Product.brand_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('District.id', 'Product.brand_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'outlet_type') {
							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'District.id', 'DistOutlet.category_id');
							$group = array('District.id', 'DistOutlet.category_id');

							$fields2 = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
									 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'District.id', 'DistOutlet.category_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('District.id', 'DistOutlet.category_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'national') {
							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'District.id');
							$group = array('District.id');

							$fields2 = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
									 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'District.id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('District.id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						}
					}


					if ($request_data['DistSalesAnalysisReports']['rows'] == 'thana') {
						if ($request_data['DistSalesAnalysisReports']['columns'] == 'product') {
							$fields = array('
									SUM(
										ROUND((ROUND(
												(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
											,2,1)
									) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
									 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'Thana.id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group = array('Thana.id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'category') {
							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'Thana.id', 'Product.product_category_id');
							$group = array('Thana.id', 'Product.product_category_id');

							$fields2 = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'Thana.id', 'Product.product_category_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('Thana.id', 'Product.product_category_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'brand') {
							$fields = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
									 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'Thana.id', 'Product.brand_id');
							$group = array('Thana.id', 'Product.brand_id');

							$fields2 = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'Thana.id', 'Product.brand_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('Thana.id', 'Product.brand_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'outlet_type') {
							$fields = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'Thana.id', 'DistOutlet.category_id');
							$group = array('Thana.id', 'DistOutlet.category_id');

							$fields2 = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
										sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'Thana.id', 'DistOutlet.category_id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('Thana.id', 'DistOutlet.category_id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'national') {
							$fields = array('
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
									SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus,
									 sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'Thana.id');
							$group = array('Thana.id');

							$fields2 = array('
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price>0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
												(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS volume,
										SUM(
											ROUND((ROUND(
													(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
													(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
												,0)) / 
										(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
												,2,1)
										) AS bonus, sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS value, COUNT(DISTINCT DistMemo.dist_memo_no) AS ec, COUNT(DISTINCT DistMemo.outlet_id) as oc', 'Thana.id', 'DistMemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
							$group2 = array('Thana.id', 'DistMemoDetail.product_id', 'cyp_cal', 'cyp');
						}
					}


					//pr($fields);
					//pr($conditions);
					//exit;

					//pr($group);
					//exit;

					//$conditions['DistMemoDetail.price >']=0;

					//echo '<pre>fasdf';pr($conditions);exit;

					//pr('hello');exit;

					$q_results = $this->DistMemo->find('all', array(
						'conditions' => $conditions,
						'joins' => array(
							array(
								'table' => 'dist_tsos',
								'alias' => 'DistTso',
								'type' => 'LEFT',
								'conditions' => 'DistTso.id = DistMemo.tso_id'

							),
							array(
								'table' => 'dist_area_executives',
								'alias' => 'DistAE',
								'type' => 'LEFT',
								'conditions' => 'DistAE.id = DistTso.dist_area_executive_id'

							),
							array(
								'alias' => 'DistMemoDetail',
								'table' => 'dist_memo_details',
								'type' => 'INNER',
								'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
							),
							array(
								'alias' => 'Product',
								'table' => 'products',
								'type' => 'INNER',
								'conditions' => 'DistMemoDetail.product_id = Product.id'
							),
							array(
								'alias' => 'DistOutlet',
								'table' => 'dist_outlets',
								'type' => 'INNER',
								'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
							),
							array(
								'alias' => 'DistMarket',
								'table' => 'dist_markets',
								'type' => 'INNER',
								'conditions' => 'DistMemo.market_id = DistMarket.id'
							),
							array(
								'alias' => 'DistRoute',
								'table' => 'dist_routes',
								'type' => 'INNER',
								'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
							),
							array(
								'alias' => 'Thana',
								'table' => 'thanas',
								'type' => 'INNER',
								'conditions' => 'DistRoute.thana_id = Thana.id'
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
								'alias' => 'ProductMeasurement',
								'table' => 'product_measurements',
								'type' => 'LEFT',
								'conditions' => 'Product.id = ProductMeasurement.product_id AND 
											CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
											ELSE 
												DistMemoDetail.measurement_unit_id
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
						),
						//'fields' => array('Memo.id', 'DistMemoDetail.id', 'Territory.id', 'Outlet.id', 'Outlet.name', 'Market.id', 'Thana.id', 'District.name', 'Division.name'),
						'fields' => $fields,
						'group' => $group,
						'recursive' => -1
					));

					//echo $this->DistMemo->getLastQuery();exit;
					//pr($conditions);	exit;

					//pr($q_results);
					//exit;

					if ($fields2 && $group2) {
						$q_results2 = $this->DistMemo->find('all', array(
							'conditions' => $conditions,
							'joins' => array(
								array(
									'table' => 'dist_tsos',
									'alias' => 'DistTso',
									'type' => 'LEFT',
									'conditions' => 'DistTso.id = DistMemo.tso_id'

								),
								array(
									'table' => 'dist_area_executives',
									'alias' => 'DistAE',
									'type' => 'LEFT',
									'conditions' => 'DistAE.id = DistTso.dist_area_executive_id'

								),
								array(
									'alias' => 'DistMemoDetail',
									'table' => 'dist_memo_details',
									'type' => 'INNER',
									'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
								),
								array(
									'alias' => 'Product',
									'table' => 'products',
									'type' => 'INNER',
									'conditions' => 'DistMemoDetail.product_id = Product.id'
								),
								array(
									'alias' => 'DistOutlet',
									'table' => 'dist_outlets',
									'type' => 'INNER',
									'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
								),
								array(
									'alias' => 'DistMarket',
									'table' => 'dist_markets',
									'type' => 'INNER',
									'conditions' => 'DistMemo.market_id = DistMarket.id'
								),
								array(
									'alias' => 'DistRoute',
									'table' => 'dist_routes',
									'type' => 'INNER',
									'conditions' => 'DistMarket.dist_route_id = DistRoute.id'
								),
								array(
									'alias' => 'Thana',
									'table' => 'thanas',
									'type' => 'INNER',
									'conditions' => 'DistRoute.thana_id = Thana.id'
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
									'alias' => 'ProductMeasurement',
									'table' => 'product_measurements',
									'type' => 'LEFT',
									'conditions' => 'Product.id = ProductMeasurement.product_id AND 
											CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
											ELSE 
												DistMemoDetail.measurement_unit_id
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
							),
							//'fields' => array('Memo.id', 'DistMemoDetail.id', 'Territory.id', 'Outlet.id', 'Outlet.name', 'Market.id', 'Thana.id', 'District.name', 'Division.name'),
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

				//pr($q_results);
				// pr($q_results2);
				//exit;

				$results = array();
				$results2 = array();
				if ($request_data['DistSalesAnalysisReports']['columns'] == 'product') {

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'sr') {
						foreach ($q_results as $q_result) {
							$results[$q_result['DistMemo']['sr_id']][$q_result['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result[0]['volume'],
								'bonus' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => $q_result[0]['cyp'],
								'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}



					if ($request_data['DistSalesAnalysisReports']['rows'] == 'dist') {

						foreach ($q_results as $q_result) {
							$results[$q_result['DistMemo']['distributor_id']][$q_result['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result[0]['volume'],
								'bonus' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => $q_result[0]['cyp'],
								'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}




					if ($request_data['DistSalesAnalysisReports']['rows'] == 'tso') {
						foreach ($q_results as $q_result) {
							$results[$q_result['DistMemo']['tso_id']][$q_result['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result[0]['volume'],
								'bonus' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => $q_result[0]['cyp'],
								'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'ae') {
						foreach ($q_results as $q_result) {
							$results[$q_result['DistAE']['id']][$q_result['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result[0]['volume'],
								'bonus' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => $q_result[0]['cyp'],
								'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}




					if ($request_data['DistSalesAnalysisReports']['rows'] == 'territory') {
						foreach ($q_results as $q_result) {
							$results[$q_result['DistMemo']['territory_id']][$q_result['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result[0]['volume'],
								'bonus' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => $q_result[0]['cyp'],
								'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'area') {
						foreach ($q_results as $q_result) {
							$results[$q_result['DistMemo']['office_id']][$q_result['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result[0]['volume'],
								'bonus' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => $q_result[0]['cyp'],
								'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}


					if ($request_data['DistSalesAnalysisReports']['rows'] == 'division') {
						foreach ($q_results as $q_result) {
							$results[$q_result['Division']['id']][$q_result['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result[0]['volume'],
								'bonus' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => $q_result[0]['cyp'],
								'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}


					if ($request_data['DistSalesAnalysisReports']['rows'] == 'district') {
						foreach ($q_results as $q_result) {
							$results[$q_result['District']['id']][$q_result['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result[0]['volume'],
								'bonus' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => $q_result[0]['cyp'],
								'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'thana') {
						foreach ($q_results as $q_result) {
							$results[$q_result['Thana']['id']][$q_result['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result[0]['volume'],
								'bonus' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => $q_result[0]['cyp'],
								'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}
				} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'category') {
					if ($request_data['DistSalesAnalysisReports']['rows'] == 'sr') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistMemo']['sr_id']][$q_result2['Product']['product_category_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['Product']['product_category_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						//pr($results2);
						//exit;

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['DistMemo']['sr_id']][$q_result['Product']['product_category_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);
								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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

							$results[$q_result['DistMemo']['sr_id']][$q_result['Product']['product_category_id']] = array(
								//'volume' => $q_result[0]['volume'],
								'volume' => $sales_qty,
								'bonus' => $bonus_qty,
								'value' => $q_result[0]['value'],
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


					if ($request_data['DistSalesAnalysisReports']['rows'] == 'dist') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistMemo']['distributor_id']][$q_result2['Product']['product_category_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['Product']['product_category_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						//pr($results2);
						//exit;

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['DistMemo']['distributor_id']][$q_result['Product']['product_category_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);


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

							$results[$q_result['DistMemo']['distributor_id']][$q_result['Product']['product_category_id']] = array(
								//'volume' => $q_result[0]['volume'],
								'volume' => $sales_qty,
								'bonus' => $bonus_qty,
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
							//break;
						}
					}



					if ($request_data['DistSalesAnalysisReports']['rows'] == 'ae') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistAE']['id']][$q_result2['Product']['product_category_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['Product']['product_category_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						//pr($results2);
						//exit;

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['DistAE']['id']][$q_result['Product']['product_category_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);


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

							$results[$q_result['DistAE']['id']][$q_result['Product']['product_category_id']] = array(
								//'volume' => $q_result[0]['volume'],
								'volume' => $sales_qty,
								'bonus' => $bonus_qty,
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
							//break;
						}
					}



					if ($request_data['DistSalesAnalysisReports']['rows'] == 'tso') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistMemo']['tso_id']][$q_result2['Product']['product_category_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['Product']['product_category_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						//pr($results2);
						//exit;

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['DistMemo']['tso_id']][$q_result['Product']['product_category_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);


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

							$results[$q_result['DistMemo']['tso_id']][$q_result['Product']['product_category_id']] = array(
								//'volume' => $q_result[0]['volume'],
								'volume' => $sales_qty,
								'bonus' => $bonus_qty,
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
							//break;
						}
					}


					if ($request_data['DistSalesAnalysisReports']['rows'] == 'territory') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistMemo']['territory_id']][$q_result2['Product']['product_category_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['Product']['product_category_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['DistMemo']['territory_id']][$q_result['Product']['product_category_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);


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

							$results[$q_result['DistMemo']['territory_id']][$q_result['Product']['product_category_id']] = array(
								//'volume' => $q_result[0]['volume'],
								'volume' => $sales_qty,
								'bonus' => $bonus_qty,
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'area') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistMemo']['office_id']][$q_result2['Product']['product_category_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['Product']['product_category_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['DistMemo']['office_id']][$q_result['Product']['product_category_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);


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

							$results[$q_result['DistMemo']['office_id']][$q_result['Product']['product_category_id']] = array(
								//'volume' => $q_result[0]['volume'],
								'volume' => $sales_qty,
								'bonus' => $bonus_qty,
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'division') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['Division']['id']][$q_result2['Product']['product_category_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['Product']['product_category_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['Division']['id']][$q_result['Product']['product_category_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);


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
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'district') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['District']['id']][$q_result2['Product']['product_category_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['Product']['product_category_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['District']['id']][$q_result['Product']['product_category_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);


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
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}


					if ($request_data['DistSalesAnalysisReports']['rows'] == 'thana') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['Thana']['id']][$q_result2['Product']['product_category_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['Product']['product_category_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['Thana']['id']][$q_result['Product']['product_category_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);


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
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}
				} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'brand') {
					if ($request_data['DistSalesAnalysisReports']['rows'] == 'sr') {

						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistMemo']['sr_id']][$q_result2['Product']['brand_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['Product']['brand_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['DistMemo']['sr_id']][$q_result['Product']['brand_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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

							$results[$q_result['DistMemo']['sr_id']][$q_result['Product']['brand_id']] = array(
								//'volume' => $q_result[0]['volume'],
								'volume' => $sales_qty,
								'bonus' => $bonus_qty,
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
							//break;
						}
					}


					if ($request_data['DistSalesAnalysisReports']['rows'] == 'dist') {

						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistMemo']['distributor_id']][$q_result2['Product']['brand_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['Product']['brand_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['DistMemo']['distributor_id']][$q_result['Product']['brand_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);


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

							$results[$q_result['DistMemo']['distributor_id']][$q_result['Product']['brand_id']] = array(
								//'volume' => $q_result[0]['volume'],
								'volume' => $sales_qty,
								'bonus' => $bonus_qty,
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
							//break;
						}
					}


					if ($request_data['DistSalesAnalysisReports']['rows'] == 'tso') {

						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistMemo']['tso_id']][$q_result2['Product']['brand_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['Product']['brand_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['DistMemo']['tso_id']][$q_result['Product']['brand_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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

							$results[$q_result['DistMemo']['tso_id']][$q_result['Product']['brand_id']] = array(
								//'volume' => $q_result[0]['volume'],
								'volume' => $sales_qty,
								'bonus' => $bonus_qty,
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
							//break;
						}
					}


					if ($request_data['DistSalesAnalysisReports']['rows'] == 'ae') {

						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistAE']['id']][$q_result2['Product']['brand_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['Product']['brand_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['DistAE']['id']][$q_result['Product']['brand_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);


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

							$results[$q_result['DistAE.id']['id']][$q_result['Product']['brand_id']] = array(
								//'volume' => $q_result[0]['volume'],
								'volume' => $sales_qty,
								'bonus' => $bonus_qty,
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
							//break;
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'territory') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistMemo']['territory_id']][$q_result2['Product']['brand_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['Product']['brand_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['DistMemo']['territory_id']][$q_result['Product']['brand_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);


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

							$results[$q_result['DistMemo']['territory_id']][$q_result['Product']['brand_id']] = array(
								//'volume' => $q_result[0]['volume'],
								'volume' => $sales_qty,
								'bonus' => $bonus_qty,
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'area') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistMemo']['office_id']][$q_result2['Product']['brand_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['Product']['brand_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['DistMemo']['office_id']][$q_result['Product']['brand_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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

							$results[$q_result['DistMemo']['office_id']][$q_result['Product']['brand_id']] = array(
								//'volume' => $q_result[0]['volume'],
								'volume' => $sales_qty,
								'bonus' => $bonus_qty,
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'division') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['Division']['id']][$q_result2['Product']['brand_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['Product']['brand_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['Division']['id']][$q_result['Product']['brand_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);


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
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'district') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['District']['id']][$q_result2['Product']['brand_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['Product']['brand_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['District']['id']][$q_result['Product']['brand_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}


					if ($request_data['DistSalesAnalysisReports']['rows'] == 'thana') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['Thana']['id']][$q_result2['Product']['brand_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['Product']['brand_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['Thana']['id']][$q_result['Product']['brand_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);


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
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}
				} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'outlet_type') {
					if ($request_data['DistSalesAnalysisReports']['rows'] == 'sr') {

						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistMemo']['sr_id']][$q_result2['DistOutlet']['category_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								//'value' => $q_result2[0]['value'],
								//'ec' => $q_result2[0]['ec'],
								//'oc' => $q_result2[0]['oc'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['DistOutlet']['category_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}


						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['DistMemo']['sr_id']][$q_result['DistOutlet']['category_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;

								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);


								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);


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

							$results[$q_result['DistMemo']['sr_id']][$q_result['DistOutlet']['category_id']] = array(
								//'volume' => $q_result[0]['volume'],
								'volume' => $sales_qty,
								'bonus' => $bonus_qty,
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
							//break;
						}
					}


					if ($request_data['DistSalesAnalysisReports']['rows'] == 'dist') {

						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistMemo']['distributor_id']][$q_result2['DistOutlet']['category_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								//'value' => $q_result2[0]['value'],
								//'ec' => $q_result2[0]['ec'],
								//'oc' => $q_result2[0]['oc'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['DistOutlet']['category_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}


						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['DistMemo']['distributor_id']][$q_result['DistOutlet']['category_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;

								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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

							$results[$q_result['DistMemo']['distributor_id']][$q_result['DistOutlet']['category_id']] = array(
								//'volume' => $q_result[0]['volume'],
								'volume' => $sales_qty,
								'bonus' => $bonus_qty,
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
							//break;
						}
					}


					if ($request_data['DistSalesAnalysisReports']['rows'] == 'tso') {

						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistMemo']['tso_id']][$q_result2['DistOutlet']['category_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								//'value' => $q_result2[0]['value'],
								//'ec' => $q_result2[0]['ec'],
								//'oc' => $q_result2[0]['oc'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['DistOutlet']['category_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}


						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['DistMemo']['tso_id']][$q_result['DistOutlet']['category_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;

								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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

							$results[$q_result['DistMemo']['tso_id']][$q_result['DistOutlet']['category_id']] = array(
								//'volume' => $q_result[0]['volume'],
								'volume' => $sales_qty,
								'bonus' => $bonus_qty,
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
							//break;
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'ae') {

						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistAE']['id']][$q_result2['DistOutlet']['category_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								//'value' => $q_result2[0]['value'],
								//'ec' => $q_result2[0]['ec'],
								//'oc' => $q_result2[0]['oc'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['DistOutlet']['category_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}


						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							$sales_qty = 0;
							foreach ($results2[$q_result['DistAE']['id']][$q_result['DistOutlet']['category_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;

								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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

							$results[$q_result['DistAE']['id']][$q_result['DistOutlet']['category_id']] = array(
								//'volume' => $q_result[0]['volume'],
								'volume' => $sales_qty,
								'bonus' => $bonus_qty,
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
							//break;
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'territory') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistMemo']['territory_id']][$q_result2['DistOutlet']['category_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['DistOutlet']['category_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							foreach ($results2[$q_result['DistMemo']['territory_id']][$q_result['DistOutlet']['category_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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

							$results[$q_result['DistMemo']['territory_id']][$q_result['DistOutlet']['category_id']] = array(
								'volume' => $q_result[0]['volume'],
								'bonus' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'area') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistMemo']['office_id']][$q_result2['DistOutlet']['category_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['DistOutlet']['category_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							foreach ($results2[$q_result['DistMemo']['office_id']][$q_result['DistOutlet']['category_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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

							$results[$q_result['DistMemo']['office_id']][$q_result['DistOutlet']['category_id']] = array(
								'volume' => $q_result[0]['volume'],
								'bonus' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'division') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['Division']['id']][$q_result2['DistOutlet']['category_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['DistOutlet']['category_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							foreach ($results2[$q_result['Division']['id']][$q_result['DistOutlet']['category_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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

							$results[$q_result['Division']['id']][$q_result['DistOutlet']['category_id']] = array(
								'volume' => $q_result[0]['volume'],
								'bonus' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'district') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['District']['id']][$q_result2['DistOutlet']['category_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['DistOutlet']['category_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							foreach ($results2[$q_result['District']['id']][$q_result['DistOutlet']['category_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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

							$results[$q_result['District']['id']][$q_result['DistOutlet']['category_id']] = array(
								'volume' => $q_result[0]['volume'],
								'bonus' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}


					if ($request_data['DistSalesAnalysisReports']['rows'] == 'thana') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['Thana']['id']][$q_result2['DistOutlet']['category_id']][$q_result2['DistMemoDetail']['product_id']] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'category_id' => $q_result2['DistOutlet']['category_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;
							foreach ($results2[$q_result['Thana']['id']][$q_result['DistOutlet']['category_id']] as $cyp_result) {
								//pr($cyp_result);
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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

							$results[$q_result['Thana']['id']][$q_result['DistOutlet']['category_id']] = array(
								'volume' => $q_result[0]['volume'],
								'bonus' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}
				} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'national') {
					if ($request_data['DistSalesAnalysisReports']['rows'] == 'sr') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistMemo']['sr_id']][] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'sales_person_id' => $q_result2['DistMemo']['sr_id'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;

							foreach ($results2[$q_result['DistMemo']['sr_id']] as $cyp_result) {
								//pr($cyp_result);

								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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

							$results[$q_result['DistMemo']['sr_id']] = array(
								'volume' => $q_result[0]['volume'],
								'bonus' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
						//pr($results);
						//exit;
					}



					if ($request_data['DistSalesAnalysisReports']['rows'] == 'dist') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistMemo']['distributor_id']][] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;

							foreach ($results2[$q_result['DistMemo']['distributor_id']] as $cyp_result) {
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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

							$results[$q_result['DistMemo']['distributor_id']] = array(
								'volume' => $q_result[0]['volume'],
								'bonus' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'tso') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistMemo']['tso_id']][] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;

							foreach ($results2[$q_result['DistMemo']['tso_id']] as $cyp_result) {
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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

							$results[$q_result['DistMemo']['tso_id']] = array(
								'volume' => $q_result[0]['volume'],
								'bonus' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'ae') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistAE']['id']][] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;

							foreach ($results2[$q_result['DistAE']['id']] as $cyp_result) {
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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

							$results[$q_result['DistAE']['id']] = array(
								'volume' => $q_result[0]['volume'],
								'bonus' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}


					if ($request_data['DistSalesAnalysisReports']['rows'] == 'territory') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistMemo']['territory_id']][] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;

							foreach ($results2[$q_result['DistMemo']['territory_id']] as $cyp_result) {
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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

							$results[$q_result['DistMemo']['territory_id']] = array(
								'volume' => $q_result[0]['volume'],
								'bonus' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'area') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['DistMemo']['office_id']][] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;

							foreach ($results2[$q_result['DistMemo']['office_id']] as $cyp_result) {
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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

							$results[$q_result['DistMemo']['office_id']] = array(
								'volume' => $q_result[0]['volume'],
								'bonus' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'division') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['Division']['id']][] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;

							foreach ($results2[$q_result['Division']['id']] as $cyp_result) {
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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
								'volume' => $q_result[0]['bonus'],
								'value' => $q_result[0]['value'],
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'district') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['District']['id']][] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;

							foreach ($results2[$q_result['District']['id']] as $cyp_result) {
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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
								'ec' => $q_result[0]['ec'],
								'oc' => $q_result[0]['oc'],
								'cyp' => sprintf("%01.2f", $total_cyp),
								//'cyp_v' => $q_result[0]['cyp_v'],
							);
						}
					}

					if ($request_data['DistSalesAnalysisReports']['rows'] == 'thana') {
						foreach ($q_results2 as $q_result2) {
							$results2[$q_result2['Thana']['id']][] = array(
								'volume' => $q_result2[0]['volume'],
								'bonus' => $q_result2[0]['bonus'],
								'cyp' => $q_result2[0]['cyp'],
								'cyp_v' => $q_result2[0]['cyp_v'],
								'product_id' => $q_result2['DistMemoDetail']['product_id'],
							);
						}

						foreach ($q_results as $q_result) {
							$cyp = 0;
							$total_cyp = 0;

							foreach ($results2[$q_result['Thana']['id']] as $cyp_result) {
								$pro_volume = $cyp_result['volume'] ? $cyp_result['volume'] : 0;
								$base_qty = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume);

								$pro_volume_bonus = $cyp_result['bonus'] ? $cyp_result['bonus'] : 0;
								$base_qty_bonus = $this->unit_convert($cyp_result['product_id'], $product_measurement[$cyp_result['product_id']], $pro_volume_bonus);

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

						if ($request_data['DistSalesAnalysisReports']['columns'] == 'category') {
							$n_results[$r_val['Product']['product_category_id']] = array(
								'volume' => $r_val[0]['volume'],
								'bonus' => $r_val[0]['bonus'],
								'value' => $r_val[0]['value'],
								'ec' => $r_val[0]['ec'],
								'oc' => $r_val[0]['oc'],
								'product_category_id' => $r_val['Product']['product_category_id'],
							);
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'brand') {
							$n_results[$r_val['Product']['brand_id']] = array(
								'volume' => $r_val[0]['volume'],
								'bonus' => $r_val[0]['bonus'],
								'value' => $r_val[0]['value'],
								'ec' => $r_val[0]['ec'],
								'oc' => $r_val[0]['oc'],
								'brand_id' => $r_val['Product']['brand_id'],
							);
						} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'outlet_type') {
							$n_results[$r_val['DistOutlet']['category_id']] = array(
								'volume' => $r_val[0]['volume'],
								'bonus' => $r_val[0]['bonus'],
								'value' => $r_val[0]['value'],
								'ec' => $r_val[0]['ec'],
								'oc' => $r_val[0]['oc'],
								'outlet_category_id' => $r_val['DistOutlet']['category_id'],
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
				}
				//pr($n_results);
				//pr($results);
				//pr($results2);
				//exit;

				$this->set(compact('results'));


				//FOR OUTPUT
				$indicators_array = array();
				if (empty($request_data['DistSalesAnalysisReports']['indicators'])) {
					foreach ($indicators as $key => $val) {
						array_push($indicators_array, $key);
					}
				} else {
					$indicators_array = $request_data['DistSalesAnalysisReports']['indicators'];
				}

				//$results = $results;


				//pr($results);
				//exit;

				$this->Session->write('request_data', $request_data);
				$this->Session->write('results', $results);
				$this->Session->write('results2', $results2);
				$this->Session->write('rows_list', $rows_list);
				$this->Session->write('columns_list', $columns_list);
				$this->Session->write('indicators', $indicators);

				$i = 0;
				$output = '';
				$d_val = 0;
				$g_col_total = array();
				$g_total = 0;
				$sub_total = 0;

				// pr($rows_list);exit;
				foreach ($rows_list as $row_key => $row_val) {
					if (isset($results[$row_key]) && $results[$row_key]) {


						if ($request_data['DistSalesAnalysisReports']['rows'] == 'sr') {

							$output .= '<tr>';
							$output .= '<td>' . $sr_info[$row_key]['OFFICE'] . '</td>';
							$output .= '<td>' . $sr_info[$row_key]['AE'] . '</td>';
							$output .= '<td>' . $sr_info[$row_key]['TSO'] . '</td>';
							$output .= '<td>' . $sr_info[$row_key]['DB'] . '</td>';
						} elseif ($request_data['DistSalesAnalysisReports']['rows'] == 'dist') {

							$output .= '<tr>';
							$output .= '<td>' . $dist_info[$row_key]['OFFICE'] . '</td>';
							$output .= '<td>' . $dist_info[$row_key]['AE'] . '</td>';
							$output .= '<td>' . $dist_info[$row_key]['TSO'] . '</td>';
						} elseif ($request_data['DistSalesAnalysisReports']['rows'] == 'tso') {
							$output .= '<tr>';
							$output .= '<td>' . $tso_info[$row_key]['OFFICE'] . '</td>';
							$output .= '<td>' . $tso_info[$row_key]['AE'] . '</td>';
						} elseif ($request_data['DistSalesAnalysisReports']['rows'] == 'ae') {
							$output .= '<tr>';
							$output .= '<td>' . $ae_info[$row_key]['OFFICE'] . '</td>';
						} else {
							$output .= '<tr>';
						}
						$output .= '<td style="text-align:left;">' . str_replace('Sales Office', '', $row_val) . '</td>';

						$c = 0;


						foreach ($columns_list as $col_key => $col_val) {
							foreach ($indicators as $in_key => $in_val) {
								if (in_array($in_key, $indicators_array)) {
									if ($request_data['DistSalesAnalysisReports']['rows'] == 'national') {
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
													$base_qty = $this->unit_convert($result2['DistMemoDetail']['product_id'], $product_measurement[$result2['DistMemoDetail']['product_id']], $pro_volume);

													$pro_volume_bonus = $result2['bonus'] ? $result2['bonus'] : 0;
													$base_qty_bonus = $this->unit_convert($result2['product_id'], $product_measurement[$result2['product_id']], $pro_volume_bonus);


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

													//$sales_qty+= ($unit_type==1)?$pro_volume:$this->unit_convert($result2['DistMemoDetail']['product_id'], $product_measurement[$result2['DistMemoDetail']['product_id']], $pro_volume);
													$sales_qty += ($unit_type == 1) ? $pro_volume : $base_qty;
													$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
												}
											}
											//echo $sales_qty;
											//exit;



											if (@$n_results[$col_key]) {
												if ($in_key == 'cyp') {
													$d_val = sprintf("%01.2f", $total_cyp);
												} elseif ($in_key == 'volume') {
													//@$d_val = ($unit_type==1)?$sales_qty:$this->unit_convert($col_key, $product_measurement[$col_key], $sales_qty);	  
													$d_val = @sprintf("%01.2f", $sales_qty);
												} elseif ($in_key == 'bonus') {
													//@$d_val = ($unit_type==1)?$sales_qty:$this->unit_convert($col_key, $product_measurement[$col_key], $sales_qty);	  
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
													$base_qty = $this->unit_convert($result2['DistMemoDetail']['product_id'], $product_measurement[$result2['DistMemoDetail']['product_id']], $pro_volume);

													$pro_volume_bonus = $result2['bonus'] ? $result2['bonus'] : 0;
													$base_qty_bonus = $this->unit_convert($result2['product_id'], $product_measurement[$result2['product_id']], $pro_volume_bonus);

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
													$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
												}
											}

											if (@$n_results[$col_key]) {
												if ($in_key == 'cyp') {
													$d_val = sprintf("%01.2f", $total_cyp);
												} elseif ($in_key == 'volume') {
													//@$d_val = ($unit_type==1)?$sales_qty:$this->unit_convert($col_key, $product_measurement[$col_key], $sales_qty);	  
													$d_val = @sprintf("%01.2f", $sales_qty);
												} elseif ($in_key == 'bonus') {
													//@$d_val = ($unit_type==1)?$sales_qty:$this->unit_convert($col_key, $product_measurement[$col_key], $sales_qty);	  
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
											$bonus_qty = 0;
											foreach ($results2[$row_key] as $result2) {
												if ($result2['DistOutlet']['category_id'] == $col_key) {
													//pr($result2);
													$pro_volume = $result2[0]['volume'];
													$base_qty = $this->unit_convert($result2['DistMemoDetail']['product_id'], $product_measurement[$result2['DistMemoDetail']['product_id']], $pro_volume);

													$pro_volume_bonus = $result2['bonus'] ? $result2['bonus'] : 0;
													$base_qty_bonus = $this->unit_convert($result2['product_id'], $product_measurement[$result2['product_id']], $pro_volume_bonus);

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
													$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
												}
											}


											if (@$n_results[$col_key]) {
												if ($in_key == 'cyp') {
													$d_val = sprintf("%01.2f", $total_cyp);
												} else if ($in_key == 'bonus') {
													$d_val = sprintf("%01.2f", $bonus_qty);
												} else {
													//$d_val = '1.00';
													$d_val = @sprintf("%01.2f", $n_results[$col_key][$in_key]);
												}
											}
										} elseif ($columns == 'national') {
											$cyp = 0;
											$total_cyp = 0;
											$bonus_qty = 0;
											foreach ($results2[0] as $result2) {
												$pro_volume = $result2[0]['volume'];
												$base_qty = $this->unit_convert($result2['DistMemoDetail']['product_id'], $product_measurement[$result2['DistMemoDetail']['product_id']], $pro_volume);

												$pro_volume_bonus = $result2['bonus'] ? $result2['bonus'] : 0;
												$base_qty_bonus = $this->unit_convert($result2['product_id'], $product_measurement[$result2['product_id']], $pro_volume_bonus);

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
												$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
											}
											//echo $total_cyp;
											//exit;

											if ($in_key == 'cyp') {
												$d_val = sprintf("%01.2f", $total_cyp);
											} else if ($in_key == 'bonus') {
												$d_val = sprintf("%01.2f", $bonus_qty);
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
												if (@$results[0][$n]['DistMemoDetail']['product_id'] == $col_key) {
													$sales_qty = @$results[0][$n][0]['volume'];
													$pro_qty = @$sales_qty;
													$base_qty = $this->unit_convert($col_key, $product_measurement[$col_key], $sales_qty);

													$pro_volume_bonus = @$results[0][$n][0]['bonus'] ? @$results[0][$n][0]['bonus'] : 0;
													$base_qty_bonus = $this->unit_convert($col_key, $product_measurement[$col_key], $pro_volume_bonus);

													if ($in_key == 'cyp') {

														$cyp_cal = @$results[0][$n][0]['cyp'];
														$cyp_val = @$results[0][$n][0]['cyp_v'];



														if ($cyp_cal && $cyp_val) {
															if ($cyp_cal == '*') $d_val = @sprintf("%01.2f", $base_qty * $cyp_val);
															if ($cyp_cal == '/') $d_val = @sprintf("%01.2f", $base_qty / $cyp_val);
															if ($cyp_cal == '-') $d_val = @sprintf("%01.2f", $base_qty - $cyp_val);
															if ($cyp_cal == '+') $d_val = @sprintf("%01.2f", $base_qty + $cyp_val);
														}
													} elseif ($in_key == 'volume') {
														$d_val = ($unit_type == 1) ? $sales_qty : $base_qty;
														$d_val = @sprintf("%01.2f", $d_val);
													} elseif ($in_key == 'bonus') {
														$d_val = ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
														$d_val = @sprintf("%01.2f", $d_val);
													} else {
														$d_val = @sprintf("%01.2f", $results[0][$n][0][$in_key]);
													}
												}
											}
											//exit;

										}
									} elseif ($request_data['DistSalesAnalysisReports']['rows'] == 'month') {
										if ($columns == 'category') {
											$cyp = 0;
											$total_cyp = 0;
											$sales_qty = 0;
											$bonus_qty = 0;
											foreach ($results2[$row_key] as $result2) {
												if ($result2['Product']['product_category_id'] == $col_key) {
													//pr($result2);

													$pro_volume = $result2[0]['volume'];
													$base_qty = $this->unit_convert($result2['DistMemoDetail']['product_id'], $product_measurement[$result2['DistMemoDetail']['product_id']], $pro_volume);

													$pro_volume_bonus = $result2['bonus'] ? $result2['bonus'] : 0;
													$base_qty_bonus = $this->unit_convert($result2['product_id'], $product_measurement[$result2['product_id']], $pro_volume_bonus);


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
													$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
												}
											}

											if (@$results[$row_key][$c]['Product']['product_category_id'] == $col_key) {
												if ($in_key == 'cyp') {
													$d_val = sprintf("%01.2f", $total_cyp);
												} elseif ($in_key == 'volume') {
													//@$d_val = ($unit_type==1)?$sales_qty:$this->unit_convert($col_key, $product_measurement[$col_key], $sales_qty);	  
													$d_val = @sprintf("%01.2f", $sales_qty);
												} elseif ($in_key == 'bonus') {
													//@$d_val = ($unit_type==1)?$sales_qty:$this->unit_convert($col_key, $product_measurement[$col_key], $sales_qty);	  
													$d_val = @sprintf("%01.2f", $bonus_qty);
												} else {
													$d_val = @sprintf("%01.2f", $results[$row_key][$c][0][$in_key]);
												}
											} else {
												$d_val = '0.00';
											}
										} elseif ($columns == 'brand') {
											$cyp = 0;
											$total_cyp = 0;
											$sales_qty = 0;
											$bonus_qty = 0;
											foreach ($results2[$row_key] as $result2) {
												if ($result2['Product']['brand_id'] == $col_key) {
													//pr($result2);
													$pro_volume = $result2[0]['volume'];
													$base_qty = $this->unit_convert($result2['DistMemoDetail']['product_id'], $product_measurement[$result2['DistMemoDetail']['product_id']], $pro_volume);

													$pro_volume_bonus = $result2['bonus'] ? $result2['bonus'] : 0;
													$base_qty_bonus = $this->unit_convert($result2['product_id'], $product_measurement[$result2['product_id']], $pro_volume_bonus);

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
													$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
												}
											}

											if (@$results[$row_key][$c]['Product']['brand_id'] == $col_key) {
												if ($in_key == 'cyp') {
													$d_val = sprintf("%01.2f", $total_cyp);
												} elseif ($in_key == 'volume') {
													//@$d_val = ($unit_type==1)?$sales_qty:$this->unit_convert($col_key, $product_measurement[$col_key], $sales_qty);	  
													$d_val = @sprintf("%01.2f", $sales_qty);
												} elseif ($in_key == 'bonus') {
													//@$d_val = ($unit_type==1)?$sales_qty:$this->unit_convert($col_key, $product_measurement[$col_key], $sales_qty);	  
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
											$total_cyp = 0;
											foreach ($results2[$row_key] as $result2) {
												if ($result2['DistOutlet']['category_id'] == $col_key) {
													//pr($result2);
													$pro_volume = $result2[0]['volume'];
													$base_qty = $this->unit_convert($result2['DistMemoDetail']['product_id'], $product_measurement[$result2['DistMemoDetail']['product_id']], $pro_volume);

													$pro_volume_bonus = $result2['bonus'] ? $result2['bonus'] : 0;
													$base_qty_bonus = $this->unit_convert($result2['product_id'], $product_measurement[$result2['product_id']], $pro_volume_bonus);

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

													$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
												}
											}
											//echo $total_cyp;
											//exit;
											$d_val = '0.00';
											if (@$results[$row_key][$c]['DistOutlet']['category_id'] == $col_key) {
												if ($in_key == 'cyp') {
													$d_val = sprintf("%01.2f", $total_cyp);
												} else if ($in_key == 'bonus') {
													$d_val = sprintf("%01.2f", $bonus_qty);
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
											$bonus_qty = 0;
											foreach ($results2[$row_key] as $result2) {

												//pr($result2);
												$pro_volume = $result2[0]['volume'];
												$base_qty = $this->unit_convert($result2['DistMemoDetail']['product_id'], $product_measurement[$result2['DistMemoDetail']['product_id']], $pro_volume);
												$pro_volume_bonus = $result2['bonus'] ? $result2['bonus'] : 0;
												$base_qty_bonus = $this->unit_convert($result2['product_id'], $product_measurement[$result2['product_id']], $pro_volume_bonus);

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
												$bonus_qty += ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
											}
											//echo $total_cyp;
											//exit;

											if ($in_key == 'cyp') {
												$d_val = sprintf("%01.2f", $total_cyp);
											} elseif ($in_key == 'bonus') {
												$d_val = sprintf("%01.2f", $bonus_qty);
											} else {
												$d_val = @sprintf("%01.2f", $results[$row_key][$c][0][$in_key]);
											}
										} else {
											$d_val = '0.00';

											$total = count($results[$row_key]);

											for ($n = 0; $n < $total; $n++) {
												//echo $n.'<br>'; 
												if (@$results[$row_key][$n]['DistMemoDetail']['product_id'] == $col_key) {
													$sales_qty = @$results[$row_key][$n][0]['volume'];
													$base_qty = $this->unit_convert($col_key, $product_measurement[$col_key], $sales_qty);

													$pro_volume_bonus = @$results[0][$n][0]['bonus'] ? @$results[0][$n][0]['bonus'] : 0;
													$base_qty_bonus = $this->unit_convert($col_key, $product_measurement[$col_key], $pro_volume_bonus);

													if ($in_key == 'cyp') {
														$pro_qty =  @$sales_qty;
														$cyp_cal =  @$results[$row_key][$n][0]['cyp'];
														$cyp_val =  @$results[$row_key][$n][0]['cyp_v'];

														if ($cyp_cal && $cyp_val) {
															if ($cyp_cal == '*') $d_val =  @sprintf("%01.2f", $base_qty * $cyp_val);
															if ($cyp_cal == '/') $d_val =  @sprintf("%01.2f", $base_qty / $cyp_val);
															if ($cyp_cal == '-') $d_val =  @sprintf("%01.2f", $base_qty - $cyp_val);
															if ($cyp_cal == '+') $d_val =  @sprintf("%01.2f", $base_qty + $cyp_val);
														}
													} elseif ($in_key == 'volume') {
														$d_val = ($unit_type == 1) ? $sales_qty : $base_qty;
														$d_val = @sprintf("%01.2f", $d_val);
													} elseif ($in_key == 'bonus') {
														$d_val = ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
														$d_val = @sprintf("%01.2f", $d_val);
													} else {
														$d_val = @sprintf("%01.2f", $results[$row_key][$n][0][$in_key]);
													}
												}
											}
											//exit;
										}
									} else {
										if ($request_data['DistSalesAnalysisReports']['columns'] == 'product') {
											//$d_val = ($in_key!='cyp')?@sprintf("%01.2f", $results[$row_key][$col_key][$in_key]):'';
											$sales_qty = @$results[$row_key][$col_key]['volume'];
											$base_qty = $this->unit_convert($col_key, $product_measurement[$col_key], $sales_qty);

											$pro_volume_bonus = @$results[$row_key][$col_key]['bonus'] ? $results[$row_key][$col_key]['bonus']  : 0;
											$base_qty_bonus = $this->unit_convert($col_key, $product_measurement[$col_key], $pro_volume_bonus);

											if ($in_key == 'cyp') {
												$d_val = '';
												$pro_qty =  @$sales_qty;

												$cyp_cal =  @$results[$row_key][$col_key]['cyp'];
												$cyp_val =  @$results[$row_key][$col_key]['cyp_v'];

												if ($cyp_cal && $cyp_val) {
													if ($cyp_cal == '*') $d_val =  @sprintf("%01.2f", $base_qty * $cyp_val);
													if ($cyp_cal == '/') $d_val =  @sprintf("%01.2f", $base_qty / $cyp_val);
													if ($cyp_cal == '-') $d_val =  @sprintf("%01.2f", $base_qty - $cyp_val);
													if ($cyp_cal == '+') $d_val =  @sprintf("%01.2f", $base_qty + $cyp_val);
												}
											} elseif ($in_key == 'volume') {
												$d_val = ($unit_type == 1) ? $sales_qty : $base_qty;
												$d_val = @sprintf("%01.2f", $d_val);
											} elseif ($in_key == 'bonus') {
												$d_val = ($unit_type == 1) ? $pro_volume_bonus : $base_qty_bonus;
												$d_val = @sprintf("%01.2f", $d_val);
											} else {
												$d_val = @sprintf("%01.2f", $results[$row_key][$col_key][$in_key]);
											}
										} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'category') {
											$d_val = @sprintf("%01.2f", $results[$row_key][$col_key][$in_key]);
											//$d_val = $col_key;
										} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'brand') {
											$d_val = @sprintf("%01.2f", $results[$row_key][$col_key][$in_key]);
											//$d_val = $col_key;
										} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'outlet_type') {
											$d_val = @sprintf("%01.2f", $results[$row_key][$col_key][$in_key]);
											//$d_val = $col_key;
										} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'national') {
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
				if ($request_data['DistSalesAnalysisReports']['rows'] == 'sr') { //for SR

					$output .= '<tr>';
					$output .= '<td colspan="4"></td>';
				} elseif ($request_data['DistSalesAnalysisReports']['rows'] == 'dist') { //for DB
					$output .= '<tr>';
					$output .= '<td colspan="3"></td>';
				} elseif ($request_data['DistSalesAnalysisReports']['rows'] == 'tso') { //for tso
					$output .= '<tr>';
					$output .= '<td colspan="2"></td>';
				} elseif ($request_data['DistSalesAnalysisReports']['rows'] == 'ae') { //for AE
					$output .= '<tr>';
					$output .= '<td></td>';
				} else {

					$output .= '<tr>'; //previous

				}
				$output .= '<td style="text-align:right;"><b>Total :</b></td>';

				foreach ($columns_list as $col_key => $col_val) {
					foreach ($indicators as $in_key => $in_val) {
						if (in_array($in_key, $indicators_array)) {
							$output .= '<td><b>' . @sprintf("%01.2f", $g_col_total[$col_key][$in_key]) . '</b></td>';
						}
					}
				}

				$output .= '</tr>';



				//exit;
				//echo $output;
				$this->set(compact('output'));

				//END OUTPUT


				/*END FOR RESULT QUERY*/
			} else {
				$this->Session->setFlash(__('Please select an indicators!'), 'flash/error');
			}
		}




		//for office list
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

		$this->set(compact('offices', 'outlet_type', 'territories', 'srs'));
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
			));	*/
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
			$conditions['TerritoryAssignHistory.date BETWEEN ? and ? '] = array($date_from, $date_to);
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
		$form->create('DistSalesAnalysisReports', array('role' => 'form', 'action' => 'index'));

		echo $form->input('so_id', array('label' => false, 'id' => 'so_id', 'class' => 'form-control so_id', 'required' => false, 'options' => $so_list, 'empty' => '---- All ----'));
		$form->end();

		//}
		//else
		//{
		//echo '';	
		//}


		$this->autoRender = false;
	}


	public function get_office_sr_list()
	{
		$view = new View($this);

		$form = $view->loadHelper('Form');

		$office_id = $this->request->data['office_id'];

		$date_from = date('Y-m-d', strtotime($this->request->data['date_from']));
		$date_to = date('Y-m-d', strtotime($this->request->data['date_to']));

		$sr_list = array();

		if ($office_id) {
			$this->loadModel('DistMemo');
			$this->loadModel('DistSalesRepresentative');

			$conditions = array('DistMemo.office_id' => $office_id);
			$conditions['DistMemo.memo_date BETWEEN ? and ? '] = array($date_from, $date_to);

			$sr_list_from_memo = array();
			$sr_list_from_memo = $this->DistMemo->find('all', array(
				'fields' => array('DISTINCT DistMemo.sr_id'),
				'conditions' => $conditions,
				'recursive' => -1
			));

			$sr_list_from_memo_ar = array();
			foreach ($sr_list_from_memo as $key => $value) {
				$sr_list_from_memo_ar[] = $value['DistMemo']['sr_id'];
			}


			$sr_list = $this->DistSalesRepresentative->find('list', array('conditions' => array('DistSalesRepresentative.id' => $sr_list_from_memo_ar), 'order' =>  array('DistSalesRepresentative.name' => 'asc')));
		}

		$form->create('DistSalesAnalysisReports', array('role' => 'form', 'action' => 'index'));

		echo $form->input('sr_id', array('label' => false, 'id' => 'sr_id', 'class' => 'form-control sr_id', 'required' => false, 'options' => $sr_list, 'empty' => '---- All ----'));
		$form->end();

		$this->autoRender = false;
	}


	public function get_district_list()
	{

		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		$division_id = $this->request->data['division_id'];


		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		$division_id = $this->request->data['division_id'];
		$district = $this->District->find('all', array(
			'fields' => array('District.id', 'District.name'),
			'conditions' => array('District.division_id' => $division_id),
			'order' => array('District.name' => 'asc'),
			'recursive' => -1
		));

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
			'is_distributor_product' => 1,
			'Product.product_type_id' => 1
		);

		if ($product_type) $conditions['source'] = $product_type;

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
			$form->create('DistSalesAnalysisReports', array('role' => 'form', 'action' => 'index'));

			echo $form->input('product_id', array('id' => 'product_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $product_list));


			$form->end();
		} else {
			echo '';
		}


		$this->autoRender = false;
	}














	public function getOutletOCTotal($request_data = array(), $so_id = 0, $outlet_category_id = 0)
	{
		$date_from = date('Y-m-d', strtotime($request_data['DistSalesAnalysisReports']['date_from']));
		$date_to = date('Y-m-d', strtotime($request_data['DistSalesAnalysisReports']['date_to']));

		$conditions = array(
			'DistMemo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
			//'DistMemo.status !=' => 0,
			'DistMemo.sales_person_id' => $so_id,
			'DistOutlet.category_id' => $outlet_category_id,
		);

		$result = $this->DistMemo->find('count', array(
			'conditions' => $conditions,
			'fields' => 'COUNT(DISTINCT DistMemo.outlet_id) as count',
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

		$columns 	= $request_data['DistSalesAnalysisReports']['columns'];

		$indicators_array = array();
		if (empty($request_data['DistSalesAnalysisReports']['indicators'])) {
			foreach ($indicators as $key => $val) {
				array_push($indicators_array, $key);
			}
		} else {
			$indicators_array = $request_data['DistSalesAnalysisReports']['indicators'];
		}

		$header = "";
		$data1 = "";

		$data1 .= ucfirst('By ' . ucfirst($request_data['DistSalesAnalysisReports']['rows']) . "\t");
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

						if ($request_data['DistSalesAnalysisReports']['rows'] == 'month' || $request_data['DistSalesAnalysisReports']['rows'] == 'national') {
							if ($request_data['DistSalesAnalysisReports']['rows'] == 'national') {
								if ($columns == 'outlet_type') {
									$cyp = 0;
									$total_cyp = 0;
									foreach ($results2[$row_key] as $result2) {
										if ($result2['DistOutlet']['category_id'] == $col_key) {
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

									if (@$results[0][$c]['DistOutlet']['category_id'] == $col_key) {
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
									if (@$results[0][$c]['DistMemoDetail']['product_id'] == $col_key) {
										$d_val = '0.00';
										if ($in_key == 'cyp') {
											$pro_qty =  @$results[0][$c][0]['volume'];
											$cyp_cal =  @$results[0][$c][0]['cyp'];
											$cyp_val =  @$results[0][$c][0]['cyp_v'];

											if ($cyp_cal && $cyp_val) {
												if ($cyp_cal == '*') $d_val =  @sprintf("%01.2f", $pro_qty * $cyp_val);
												if ($cyp_cal == '/') $d_val =  @sprintf("%01.2f", $pro_qty / $cyp_val);
												if ($cyp_cal == '-') $d_val =  @sprintf("%01.2f", $pro_qty - $cyp_val);
												if ($cyp_cal == '+') $d_val =  @sprintf("%01.2f", $pro_qty + $cyp_val);
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
										if ($result2['DistOutlet']['category_id'] == $col_key) {
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

									if (@$results[$row_key][$c]['DistOutlet']['category_id'] == $col_key) {
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
									if (@$results[$row_key][$c]['DistMemoDetail']['product_id'] == $col_key) {
										//echo $d_val;
										//exit;
										//exit;
										if ($in_key == 'cyp') {
											$d_val = '0.00';
											$pro_qty =  @$results[$row_key][$c][0]['volume'];
											$cyp_cal =  @$results[$row_key][$c][0]['cyp'];
											$cyp_val =  @$results[$row_key][$c][0]['cyp_v'];

											if ($cyp_cal && $cyp_val) {
												if ($cyp_cal == '*') $d_val =  @sprintf("%01.2f", $pro_qty * $cyp_val);
												if ($cyp_cal == '/') $d_val =  @sprintf("%01.2f", $pro_qty / $cyp_val);
												if ($cyp_cal == '-') $d_val =  @sprintf("%01.2f", $pro_qty - $cyp_val);
												if ($cyp_cal == '+') $d_val =  @sprintf("%01.2f", $pro_qty + $cyp_val);
											}
										} else {
											$d_val = @sprintf("%01.2f", $results[$row_key][$c][0][$in_key]);
										}

										//$d_val = 0.00;
									}
								}
							}
						} else {
							if ($request_data['DistSalesAnalysisReports']['columns'] == 'product' || $request_data['DistSalesAnalysisReports']['columns'] == 'brand' || $request_data['DistSalesAnalysisReports']['columns'] == 'category') {
								$d_val = ($in_key != 'cyp') ? @sprintf("%01.2f", $results[$row_key][$col_key][$in_key]) : '';

								if ($in_key == 'cyp') {
									$d_val = '';
									$pro_qty =  @$results[$row_key][$col_key]['volume'];
									$cyp_cal =  @$results[$row_key][$col_key]['cyp'];
									$cyp_val =  @$results[$row_key][$col_key]['cyp_v'];

									if ($cyp_cal && $cyp_val) {
										if ($cyp_cal == '*') $d_val =  @sprintf("%01.2f", $pro_qty * $cyp_val);
										if ($cyp_cal == '/') $d_val =  @sprintf("%01.2f", $pro_qty / $cyp_val);
										if ($cyp_cal == '-') $d_val =  @sprintf("%01.2f", $pro_qty - $cyp_val);
										if ($cyp_cal == '+') $d_val =  @sprintf("%01.2f", $pro_qty + $cyp_val);
									}
								}
							} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'outlet_type') {
								$d_val = @sprintf("%01.2f", $results[$row_key][$col_key][$in_key]);
							} elseif ($request_data['DistSalesAnalysisReports']['columns'] == 'national') {
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
}
