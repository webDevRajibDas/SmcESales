<?php

App::uses('AppController', 'Controller');

/**
 * CurrentInventories Controller
 *
 * @property DistDistribu $DistDistribu
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */

class ProgramProviderReportsController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */

	public $uses = array('Program', 'Office', 'Territory', 'Product', 'Division', 'District', 'Thana', 'ProductCategory', 'ProductCategory', 'Memo', 'MemoDetail', 'ThanaTerritory');
	public $components = array('Paginator', 'Session', 'Filter.Filter');


	public function admin_index()
	{

		$this->set('page_title', 'Program Provider Report');

		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 99999); //300 seconds = 5 minutes


		$programs = array(
			'1' 			=> 'PCHP',
			'2' 			=> 'BSP',
			'3' 			=> 'Pink Star',
			'4' 			=> 'Stockist For Injectable',
			'5' 			=> 'NGO For Injectable',
			//'6' 			=> 'Notundin',
		);
		$this->set(compact('programs'));

		$status = array(
			'1' => 'Assigned',
			'2' => 'De-Assigned'
		);
		$this->set(compact('status'));

		$filter_types = array(
			'1' => 'By Sales Office',
			'2' => 'By Administrative Location'
		);
		$this->set(compact('filter_types'));


		$report_types = array(
			'1' => 'Provider List',
			'2' => 'Summary Report',
			'3' => 'Detail Report'
		);
		$this->set(compact('report_types'));


		$productCategories = $this->Product->ProductCategory->find('list', array(
			'conditions' => array(
				'Product.product_type_id' => 1,
				'Product.source' => 'SMC'
			),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'conditions' => 'Product.product_category_id=ProductCategory.id'
				),
			),
			'order' => array(
				'ProductCategory.name' => 'asc'
			)
		));

		$brands = $this->Product->Brand->find('list', array(
			'conditions' => array(
				'Product.product_type_id' => 1,
				'Product.source' => 'SMC'
			),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'conditions' => 'Product.brand_id=Brand.id'
				),
			),
			'order' => array('Brand.name' => 'asc')
		));
		$variants = $this->Product->Variant->find('list', array(
			'conditions' => array(
				'Product.product_type_id' => 1,
				'Product.source' => 'SMC'
			),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'conditions' => 'Product.variant_id=Variant.id'
				),
			),
			'order' => array('Variant.name' => 'asc')
		));

		$product_list = $this->Product->find('list', array(
			'conditions' => array('Product.product_type_id' => 1, 'Product.source' => 'SMC'),
			'order' => array('Product.order'),
			'recursive' => -1
		));

		$this->set(compact('productCategories', 'brands', 'variants', 'product_list'));


		//for divisions
		$divisions = $this->Division->find('list', array(
			//'conditions'=> $conditions,
			'order' =>  array('name' => 'asc')
		));
		//end for divisions


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


		$request_data = array();

		if ($this->request->is('post')) {
			//pr($this->request->data);
			//exit;
			$product_measurement = $this->Product->find('list', array(
				//'conditions'=> $pro_conditions,
				'fields' => array('Product.id', 'Product.sales_measurement_unit_id'),
				'order' =>  array('order' => 'asc'),
				'recursive' => -1
			));
			$this->set(compact('product_measurement'));
			$office_id = isset($this->request->data['ProgramProviderReports']['office_id']) != '' ? $this->request->data['ProgramProviderReports']['office_id'] : $office_id;

			//territory list
			$territory_list = $this->Territory->find('all', array(
				'conditions' => array('Territory.office_id' => $office_id),
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

			$territories = array();

			foreach ($territory_list as $key => $value) {
				$territories[$value['Territory']['id']] = $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')';
			}
			$this->set(compact('territories'));


			$division_id = isset($this->request->data['ProgramProviderReports']['division_id']) != '' ? $this->request->data['ProgramProviderReports']['division_id'] : 0;


			//district list
			$districts = $this->District->find('list', array(
				'conditions' => array('District.division_id' => $division_id),
				'order' => array('District.name' => 'asc')
			));
			$this->set(compact('districts'));


			//thana list
			$territory_id = isset($this->request->data['ProgramProviderReports']['territory_id']) != '' ? $this->request->data['ProgramProviderReports']['territory_id'] : 0;

			$district_id = isset($this->request->data['ProgramProviderReports']['district_id']) != '' ? $this->request->data['ProgramProviderReports']['district_id'] : 0;

			$thana_id = isset($this->request->data['ProgramProviderReports']['thana_id']) != '' ? $this->request->data['ProgramProviderReports']['thana_id'] : 0;


			$thanas = array();
			if ($territory_id) {
				$conditions = array('ThanaTerritory.territory_id' => $territory_id);
				$thana_list = $this->ThanaTerritory->find('all', array(
					'conditions' => $conditions,
					//'order' => array('Thana.name'=>'ASC'),
					'recursive' => 1
				));
				foreach ($thana_list as $thana_info) {
					$thanas[$thana_info['Thana']['id']] = $thana_info['Thana']['name'];
				}
			} else {
				$thana_conditions = array('Thana.district_id' => $district_id);
				$thanas = $this->Thana->find('list', array(
					'conditions' => $thana_conditions,
					'order' => array('Thana.name' => 'asc')
				));
			}
			//pr($thana_list);	
			$this->set(compact('thanas'));




			if ($this->request->data['ProgramProviderReports']['date_from']) {
				$date_from = date('Y-m-d', strtotime($this->request->data['ProgramProviderReports']['date_from']));
			} else {
				$date_from = '';
			}

			if ($this->request->data['ProgramProviderReports']['date_to']) {
				$date_to = date('Y-m-d', strtotime($this->request->data['ProgramProviderReports']['date_to']));
			} else {
				$date_to = '';
			}

			$request_data = $this->request->data;

			// $this->Session->write('request_data', $request_data);
			$this->set(compact('date_from', 'date_to', 'request_data'));



			$conditions = array();

			// if($date_to)$conditions['Program.assigned_date BETWEEN ? and ? '] = array('2018-01-01', $date_to);

			if ($date_to) $conditions['Program.assigned_date <='] = $date_to;

			$conditions['Program.program_type_id'] = $this->request->data['ProgramProviderReports']['program_type_id'];

			if ($this->request->data['ProgramProviderReports']['status']) $conditions['Program.status'] = $this->request->data['ProgramProviderReports']['status'];

			if ($request_data['ProgramProviderReports']['filter_type'] == 1) {
				if ($office_id) $conditions['Program.officer_id'] = $office_id;
				if ($territory_id) $conditions['Program.territory_id'] = $territory_id;
			}
			if ($request_data['ProgramProviderReports']['filter_type'] == 2) {
				if ($district_id) $conditions['District.id'] = $district_id;
				if ($division_id) $conditions['Division.id'] = $division_id;
			}

			if(!empty($request_data['ProgramProviderReports']['program_officer_id']) and $request_data['ProgramProviderReports']['program_officer_id'] > 0){
				$conditions['Program.program_officer_id'] = $request_data['ProgramProviderReports']['program_officer_id'];
			}

			if ($thana_id) $conditions['Thana.id'] = $thana_id;
			$final_results = array();
			if ($request_data['ProgramProviderReports']['report_type'] == 1) {
				$results = $this->Program->find('all', array(
					'conditions' => $conditions,
					'joins' => array(
						array(
							'alias' => 'Office',
							'table' => 'offices',
							'type' => 'INNER',
							'conditions' => 'Program.officer_id = Office.id'
						),
						array(
							'alias' => 'Territory',
							'table' => 'territories',
							'type' => 'INNER',
							'conditions' => 'Program.territory_id = Territory.id'
						),
						array(
							'alias' => 'SalesPeople',
							'table' => 'sales_people',
							'type' => 'INNER',
							'conditions' => 'Territory.id = SalesPeople.territory_id'
						),
						array(
							'alias' => 'Outlet',
							'table' => 'outlets',
							'type' => 'LEFT',
							'conditions' => 'Program.outlet_id = Outlet.id'
						),
						array(
							'alias' => 'Market',
							'table' => 'markets',
							'type' => 'LEFT',
							'conditions' => 'Program.market_id = Market.id'
						),
						array(
							'alias' => 'Thana',
							'table' => 'thanas',
							'type' => 'LEFT',
							'conditions' => 'Market.thana_id = Thana.id'
						),
						array(
							'alias' => 'District',
							'table' => 'districts',
							'type' => 'LEFT',
							'conditions' => 'Thana.district_id = District.id'
						),
						array(
							'alias' => 'Division',
							'table' => 'divisions',
							'type' => 'LEFT',
							'conditions' => 'District.division_id = Division.id'
						),
						array(
							'alias' => 'ProgramOffice',
							'table' => 'sales_people',
							'type' => 'left',
							'conditions' => 'ProgramOffice.id = Program.program_officer_id'
						),
					),

					'fields' => array('Program.*', 'ProgramOffice.name', 'Office.office_name', 'Territory.name', 'SalesPeople.name', 'Market.name', 'Thana.id', 'Thana.name', 'Outlet.name', 'District.id', 'Division.id', 'Division.name', 'District.name'),
					'order' => array('Program.id' => 'desc'),
					'recursive' => -1
				));

				//echo '<pre>';print_r($results);exit;
				

				//pr($results);
				//exit;

				$this->set(compact('results'));
			} else if ($request_data['ProgramProviderReports']['report_type'] == 2) {
				//pr($conditions);
				//$conditions['Program.status']=1;

				if ($request_data['ProgramProviderReports']['filter_type'] == 2) {
					$order = 'Division.name, District.name, Thana.name';
				} else {
					$order = 'Office.office_name, Territory.name, Thana.name';
				}

				$results = $this->Program->find('all', array(
					'conditions' => $conditions,
					'joins' => array(
						array(
							'alias' => 'ProgramOffice',
							'table' => 'sales_people',
							'type' => 'left',
							'conditions' => 'ProgramOffice.id = Program.program_officer_id'
						),
						array(
							'alias' => 'Office',
							'table' => 'offices',
							'type' => 'INNER',
							'conditions' => 'Program.officer_id = Office.id'
						),

						array(
							'alias' => 'Outlet',
							'table' => 'outlets',
							'type' => 'LEFT',
							'conditions' => 'Program.outlet_id = Outlet.id'
						),
						array(
							'alias' => 'Market',
							'table' => 'markets',
							'type' => 'LEFT',
							'conditions' => 'Outlet.market_id = Market.id'
						),
						array(
							'alias' => 'Territory',
							'table' => 'territories',
							'type' => 'INNER',
							'conditions' => 'Market.territory_id = Territory.id'
						),
						array(
							'alias' => 'SalesPeople',
							'table' => 'sales_people',
							'type' => 'INNER',
							'conditions' => 'Territory.id = SalesPeople.territory_id'
						),
						array(
							'alias' => 'Thana',
							'table' => 'thanas',
							'type' => 'LEFT',
							'conditions' => 'Market.thana_id = Thana.id'
						),
						array(
							'alias' => 'District',
							'table' => 'districts',
							'type' => 'LEFT',
							'conditions' => 'Thana.district_id = District.id'
						),
						array(
							'alias' => 'Division',
							'table' => 'divisions',
							'type' => 'LEFT',
							'conditions' => 'District.division_id = Division.id'
						)
					),

					//'fields' => array('Program.*', 'Office.office_name', 'Territory.name', 'Market.name', 'Thana.id', 'Thana.name', 'Outlet.name', 'District.id', 'Division.id'),
					'fields' => array('count(Program.id) AS active', 'ProgramOffice.name',   'Thana.id', 'Thana.name', 'District.id', 'District.name', 'Office.id', 'Office.office_name', 'Territory.id', 'Territory.name', 'SalesPeople.name', 'Division.name'),

					//'fields' => array('count(Program.outlet_id) AS active', 'Thana.id', 'Thana.name', 'Office.office_name', 'District.id', 'District.name', 'sum(MemoDetail.sales_qty) AS volume, sum(MemoDetail.sales_qty*MemoDetail.price) AS value, COUNT(Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc'),

					'group' => array('Thana.id', 'ProgramOffice.name',  'Thana.name', 'District.id', 'District.name', 'Office.id',   'Office.office_name', 'Territory.id', 'Territory.name', 'SalesPeople.name', 'Division.name'),
					'order' => $order,
					'recursive' => -1
				));




				//pr($results);
				//exit;

				$this->set(compact('results'));


				//FOR SALES INFO
				$f_results = $this->Program->find('all', array(
					'conditions' => $conditions,
					'joins' => array(
						array(
							'alias' => 'Office',
							'table' => 'offices',
							'type' => 'INNER',
							'conditions' => 'Program.officer_id = Office.id'
						),

						array(
							'alias' => 'Outlet',
							'table' => 'outlets',
							'type' => 'LEFT',
							'conditions' => 'Program.outlet_id = Outlet.id'
						),
						array(
							'alias' => 'Market',
							'table' => 'markets',
							'type' => 'LEFT',
							'conditions' => 'Outlet.market_id = Market.id'
						),
						array(
							'alias' => 'Territory',
							'table' => 'territories',
							'type' => 'LEFT',
							'conditions' => 'Market.territory_id = Territory.id'
						),
						array(
							'alias' => 'SalesPeople',
							'table' => 'sales_people',
							'type' => 'LEFT',
							'conditions' => 'Territory.id = SalesPeople.territory_id'
						),
						array(
							'alias' => 'Thana',
							'table' => 'thanas',
							'type' => 'LEFT',
							'conditions' => 'Market.thana_id = Thana.id'
						),
						array(
							'alias' => 'District',
							'table' => 'districts',
							'type' => 'LEFT',
							'conditions' => 'Thana.district_id = District.id'
						),
						array(
							'alias' => 'Division',
							'table' => 'divisions',
							'type' => 'LEFT',
							'conditions' => 'District.division_id = Division.id'
						)
					),

					'fields' => array('Program.*', 'Office.office_name', 'Territory.name', 'Market.name', 'Thana.id', 'Thana.name', 'Outlet.name', 'District.id', 'Division.id'),
					'order' => array('Program.id' => 'desc'),
					'recursive' => -1
				));
				$p_outlet_ids = array();
				foreach ($f_results as $p_val) {
					array_push($p_outlet_ids, $p_val['Program']['outlet_id']);
				}

				if ($date_from && $date_to) {
					$conditions2 = array(
						'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
						'Memo.gross_value >' => 0,
						'Memo.status !=' => 0,
					);
				} else {
					$conditions2 = array(
						//'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
						'Memo.gross_value >' => 0,
						'Memo.status !=' => 0,
					);
				}

				if ($p_outlet_ids) {
					$conditions2['Memo.outlet_id'] = $p_outlet_ids;
				} else {
					$conditions2['Memo.outlet_id'] = 0;
				}

				if ($request_data['ProgramProviderReports']['filter_type'] == 1) {
					if ($office_id) $conditions2['Memo.office_id'] = $office_id;
					if ($territory_id) $conditions2['Memo.territory_id'] = $territory_id;
				}

				$fields = array('SUM(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) AS volume,SUM(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) AS bonus, sum(MemoDetail.sales_qty*MemoDetail.price) AS value, COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Thana.id', 'Territory.id');
				$group = array('Thana.id', 'Territory.id');

				$fields2 = array('SUM(CASE WHEN MemoDetail.price>0 THEN MemoDetail.sales_qty END) AS volume,SUM(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) AS bonus, sum(MemoDetail.sales_qty*MemoDetail.price) AS value, COUNT(DISTINCT Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Thana.id', 'Territory.id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');

				$group2 = array('Thana.id', 'Territory.id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');

				$q_results = $this->Memo->find('all', array(
					'conditions' => $conditions2,
					'joins' => array(
						array(
							'alias' => 'MemoDetail',
							'table' => 'memo_details',
							'type' => 'INNER',
							'conditions' => 'Memo.id = MemoDetail.memo_id'
						),
						array(
							'alias' => 'Product',
							'table' => 'products',
							'type' => 'INNER',
							'conditions' => 'MemoDetail.product_id = Product.id'
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
							'conditions' => 'Outlet.market_id = Market.id'
						),
						array(
							'alias' => 'Territory',
							'table' => 'territories',
							'type' => 'LEFT',
							'conditions' => 'Market.territory_id = Territory.id'
						),
						array(
							'alias' => 'SalesPeople',
							'table' => 'sales_people',
							'type' => 'LEFT',
							'conditions' => 'Territory.id = SalesPeople.territory_id'
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
						)
					),
					//'fields' => array('Memo.id', 'MemoDetail.id', 'Territory.id', 'Outlet.id', 'Outlet.name', 'Market.id', 'Thana.id', 'District.name', 'Division.name'),
					'fields' => $fields,
					'group' => $group,
					'recursive' => -1
				));



				if ($fields2 && $group2) {
					$q_results2 = $this->Memo->find('all', array(
						'conditions' => $conditions2,
						'joins' => array(
							array(
								'alias' => 'MemoDetail',
								'table' => 'memo_details',
								'type' => 'INNER',
								'conditions' => 'Memo.id = MemoDetail.memo_id'
							),
							array(
								'alias' => 'Product',
								'table' => 'products',
								'type' => 'INNER',
								'conditions' => 'MemoDetail.product_id = Product.id'
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
								'conditions' => 'Outlet.market_id = Market.id'
							),
							array(
								'alias' => 'Territory',
								'table' => 'territories',
								'type' => 'LEFT',
								'conditions' => 'Market.territory_id = Territory.id'
							),
							array(
								'alias' => 'SalesPeople',
								'table' => 'sales_people',
								'type' => 'LEFT',
								'conditions' => 'Territory.id = SalesPeople.territory_id'
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
							)
						),
						//'fields' => array('Memo.id', 'MemoDetail.id', 'Territory.id', 'Outlet.id', 'Outlet.name', 'Market.id', 'Thana.id', 'District.name', 'Division.name'),
						'fields' => $fields2,
						'group' => $group2,
						'recursive' => -1
					));
				}

				//pr($q_results);
				//pr($q_results2);				
				$results2 = array();
				foreach ($q_results2 as $q_result2) {
					$results2[$q_result2['Territory']['id']][$q_result2['Thana']['id']][$q_result2['MemoDetail']['product_id']] = array(
						'volume' => $q_result2[0]['volume'],
						'cyp' => $q_result2[0]['cyp'],
						'cyp_v' => $q_result2[0]['cyp_v'],
					);
				}

				$final_results = array();
				foreach ($q_results as $q_result) {
					$cyp = 0;
					$total_cyp = 0;

					foreach ($results2[$q_result['Territory']['id']][$q_result['Thana']['id']] as $product_id => $cyp_result) {
						$pro_volume = $cyp_result['volume'];
						$base_qty = $this->unit_convert($product_id, $product_measurement[$product_id], $pro_volume);
						$cyp_s = $cyp_result['cyp'];
						$cyp_v = $cyp_result['cyp_v'];

						if ($cyp_s == '/') {
							$cyp = $base_qty / $cyp_v;
						} elseif ($cyp_s == '*') {
							$cyp = $base_qty * $cyp_v;
						} elseif ($cyp_s == '-') {
							$cyp = $base_qty - $cyp_v;
						} elseif ($cyp_s == '+') {
							$cyp = $base_qty + $cyp_v;
						} else {
							$cyp = 0;
						}
						$total_cyp += $cyp;
					}

					$final_results[$q_result['Territory']['id']][$q_result['Thana']['id']] = array(
						'volume' => $q_result[0]['volume'],
						'value' => $q_result[0]['value'],
						'ec' => $q_result[0]['ec'],
						'oc' => $q_result[0]['oc'],
						'cyp' => sprintf("%01.2f", $total_cyp),
						//'cyp_v' => $q_result[0]['cyp_v'],
					);
				}

				//pr($final_results);
				$this->set(compact('final_results'));

				//exit;
				//END FOR SALES INFO

			} else if ($request_data['ProgramProviderReports']['report_type'] == 3) {
				
				$product_column_conditions = array('Product.product_type_id' => 1, 'Product.source' => 'SMC');
				if (isset($this->request->data['ProgramProviderReports']['outlet_coverage']) && $this->request->data['ProgramProviderReports']['outlet_coverage'] > 0) {
					if (@$this->request->data['ProgramProviderReports']['product_category_id'] > 0) {
						$product_column_conditions['Product.product_category_id'] = $this->request->data['ProgramProviderReports']['product_category_id'];
					}
					if (@$this->request->data['ProgramProviderReports']['brand_id'] > 0) {
						$product_column_conditions['Product.brand_id'] = $this->request->data['ProgramProviderReports']['brand_id'];
					}
					if (@$this->request->data['ProgramProviderReports']['variant_id'] > 0) {
						$product_column_conditions['Product.variant_id'] = $this->request->data['ProgramProviderReports']['variant_id'];
					}
					if (@$this->request->data['ProgramProviderReports']['product_id'] > 0) {
						$product_column_conditions['Product.id'] = $this->request->data['ProgramProviderReports']['product_id'];
					}
				}
				
				$product_columns = $this->Product->find('list', array(
					'conditions' => $product_column_conditions,
					'order' => array('Product.order'),
					'recursive' => -1
				));
								

				$this->set(compact('product_columns'));
				if ($request_data['ProgramProviderReports']['filter_type'] == 2) {
					$order = 'Division.name, District.name, Thana.name';
				} else {
					$order = 'Office.office_name, Territory.name, Thana.name';
				}
				
				$conditions[] = array("(Program.deassigned_date IS NULL OR Program.deassigned_date >'$date_to')");
					
				$results = $this->Program->find('all', array(
					'conditions' => $conditions,
					'joins' => array(
						array(
							'alias' => 'Office',
							'table' => 'offices',
							'type' => 'INNER',
							'conditions' => 'Program.officer_id = Office.id'
						),
						array(
							'alias' => 'Territory',
							'table' => 'territories',
							'type' => 'LEFT',
							'conditions' => 'Program.territory_id = Territory.id'
						),
						array(
							'alias' => 'SalesPeople',
							'table' => 'sales_people',
							'type' => 'LEFT',
							'conditions' => 'Territory.id = SalesPeople.territory_id'
						),
						array(
							'alias' => 'Outlet',
							'table' => 'outlets',
							'type' => 'LEFT',
							'conditions' => 'Program.outlet_id = Outlet.id'
						),
						array(
							'alias' => 'Market',
							'table' => 'markets',
							'type' => 'LEFT',
							'conditions' => 'Program.market_id = Market.id'
						),
						array(
							'alias' => 'Thana',
							'table' => 'thanas',
							'type' => 'LEFT',
							'conditions' => 'Market.thana_id = Thana.id'
						),
						array(
							'alias' => 'District',
							'table' => 'districts',
							'type' => 'LEFT',
							'conditions' => 'Thana.district_id = District.id'
						),
						array(
							'alias' => 'Division',
							'table' => 'divisions',
							'type' => 'LEFT',
							'conditions' => 'District.division_id = Division.id'
						),
						array(
							'alias' => 'ProgramOffice',
							'table' => 'sales_people',
							'type' => 'left',
							'conditions' => 'ProgramOffice.id = Program.program_officer_id'
						),
					),

					'fields' => array('Program.*', 'Office.office_name', 'ProgramOffice.name', 'Territory.name', 'SalesPeople.name', 'Market.name', 'Thana.id', 'Thana.name', 'Outlet.name', 'Outlet.id', 'District.id', 'Division.id', 'Division.name', 'District.name'),
					'order' => $order,
					'recursive' => -1
				));

				/*pr($results);
				exit;*/

				$this->set(compact('results'));
				$p_outlet_ids = array();
				foreach ($results as $p_val) {
					array_push($p_outlet_ids, $p_val['Program']['outlet_id']);
				}
				// pr($p_outlet_ids);exit;
				if ($date_from && $date_to) {
					$conditions2 = array(
						'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
						'Memo.gross_value >' => 0,
						'Memo.status !=' => 0,
					);
				} else {
					$conditions2 = array(
						//'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
						'Memo.gross_value >' => 0,
						'Memo.status !=' => 0,
					);
				}

				if ($p_outlet_ids) {
					$conditions2['Memo.outlet_id'] = $p_outlet_ids;
				} else {
					$conditions2['Memo.outlet_id'] = 0;
				}
				$conditions2['MemoDetail.product_id'] = array_keys($product_columns);

				$fields = array('sum(MemoDetail.sales_qty) AS volume, sum(MemoDetail.sales_qty*MemoDetail.price) AS value, COUNT(Memo.memo_no) AS ec', 'Thana.id', 'Outlet.id', 'MemoDetail.product_id');
				$group = array('Thana.id', 'Outlet.id', 'MemoDetail.product_id');

				$fields2 = array('sum(MemoDetail.sales_qty) AS volume, sum(MemoDetail.sales_qty*MemoDetail.price) AS value, COUNT(Memo.memo_no) AS ec, COUNT(DISTINCT Memo.outlet_id) as oc', 'Thana.id', 'MemoDetail.product_id', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v');
				$group2 = array('Thana.id', 'MemoDetail.product_id', 'cyp_cal', 'cyp');

				$q_results = $this->Memo->find('all', array(
					'conditions' => $conditions2,
					'joins' => array(
						array(
							'alias' => 'MemoDetail',
							'table' => 'memo_details',
							'type' => 'INNER',
							'conditions' => 'Memo.id = MemoDetail.memo_id'
						),
						array(
							'alias' => 'Product',
							'table' => 'products',
							'type' => 'INNER',
							'conditions' => 'MemoDetail.product_id = Product.id'
						),
						array(
							'alias' => 'Territory',
							'table' => 'territories',
							'type' => 'LEFT',
							'conditions' => 'Memo.territory_id = Territory.id'
						),
						array(
							'alias' => 'SalesPeople',
							'table' => 'sales_people',
							'type' => 'LEFT',
							'conditions' => 'Territory.id = SalesPeople.territory_id'
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
					//'fields' => array('Memo.id', 'MemoDetail.id', 'Territory.id', 'Outlet.id', 'Outlet.name', 'Market.id', 'Thana.id', 'District.name', 'Division.name'),
					'fields' => $fields,
					'group' => $group,
					'recursive' => -1
				));
				$final_results = array();
				foreach ($q_results as $q_result) {


					$final_results[$q_result['Outlet']['id']][$q_result['MemoDetail']['product_id']] = array(
						'qty' => $q_result[0]['volume'],
						'value' => $q_result[0]['value']
					);
				}
				
				
				//echo '<pre>';print_r($final_results);exit;

				$this->set(compact('final_results'));
				//END FOR SALES INFO

			}

			// $this->Session->write('results', $results);
			// $this->Session->write('final_results', $final_results);

		}


		//for office list
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

		$this->set(compact('offices'));

		$this->set(compact('request_data', 'divisions', 'productCategories', 'categories_products'));
	}


	public function getProductSales($request_data, $district_id = 0, $product_id = 0, $division_id = 0)
	{

		$this->loadModel('Memo');

		$sales_data = array();

		//return 1;

		if ($product_id) {
			$date_from = date('Y-m-d', strtotime($request_data['ProgramProviderReports']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['ProgramProviderReports']['date_to']));

			if ($date_from && $date_to) {
				$conditions = array(
					'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					'Memo.gross_value >' => 0,
					'Memo.status >' => 0,
				);
			} else {
				$conditions = array(
					//'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					'Memo.gross_value >' => 0,
					'Memo.status >' => 0,
				);
			}

			$conditions['MemoDetail.product_id'] = $product_id;

			if ($district_id > 0) {
				$conditions['District.id'] = $district_id;
			}

			if ($division_id > 0) {
				$conditions['District.division_id'] = $division_id;
			}

			//pr($conditions);
			//exit;

			$result = $this->Memo->find('all', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'alias' => 'MemoDetail',
						'table' => 'memo_details',
						'type' => 'INNER',
						'conditions' => 'Memo.id = MemoDetail.memo_id'
					),
					array(
						'alias' => 'ThanaTerritory',
						'table' => 'thana_territories',
						'type' => 'INNER',
						'conditions' => 'ThanaTerritory.territory_id = Memo.territory_id'
					),
					array(
						'alias' => 'Thana',
						'table' => 'Thanas',
						'type' => 'INNER',
						'conditions' => 'ThanaTerritory.thana_id = Thana.id'
					),
					array(
						'alias' => 'District',
						'table' => 'districts',
						'type' => 'INNER',
						'conditions' => 'Thana.district_id = District.id'
					),
					/*array(
					'alias' => 'Division',
					'table' => 'divisions',
					'type' => 'INNER',
					'conditions' => 'District.division_id = Division.id'
				)*/
				),
				//'fields' => array('Challan.id', 'ChallanDetail.id', 'ChallanDetail.product_id', 'Store.id', 'Store.office_id', 'Office.id', 'Territory.id', 'Territory.office_id', 'ThanaTerritory.thana_id', 'Thana.id', 'District.id', 'District.division_id'),
				'fields' => array('sum(MemoDetail.sales_qty) AS volume', 'District.id', 'MemoDetail.product_id'),
				'group' => array('District.id', 'MemoDetail.product_id'),
				'recursive' => -1
			));



			//pr($result);
			//exit;

			return $result;
		}
	}


	//get product list
	public function get_product_list()
	{
		$view = new View($this);

		$form = $view->loadHelper('Form');

		$this->loadModel('Product');

		$product_categories_id = $this->request->data['product_categories_id'];

		$conditions = array(
			'NOT' => array('Product.product_category_id' => 32),
			'is_active' => 1,
			'Product.product_type_id' => 1
		);

		if ($product_categories_id) {
			$ids = explode(',', $product_categories_id);
			$conditions['Product.product_category_id'] = $ids;
		}

		//pr($conditions);
		//exit;

		$product_list = $this->Product->find('list', array(
			'conditions' => $conditions,
			'order' =>  array('order' => 'asc')
		));


		if ($product_list) {
			$form->create('search', array('role' => 'form', 'action' => 'index'));
			echo $form->input('product_id', array('id' => 'product_id', 'label' => false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $product_list));
			$form->end();
		} else {
			echo '';
		}


		$this->autoRender = false;
	}



	//xls download
	public function admin_dwonload_xls()
	{

		$request_data = $this->Session->read('request_data');
		$results = $this->Session->read('results');
		$final_results = $this->Session->read('final_results');

		$header = "";
		$data1 = "";

		if ($request_data['ProgramProviderReports']['report_type'] == 1) {

			$d1 = ($request_data['ProgramProviderReports']['filter_type'] == 1) ? 'Sales Office' : 'Division';
			$d2 = ($request_data['ProgramProviderReports']['filter_type'] == 1) ? 'Territory' : 'District';

			$data1 .= ucfirst("$d1\t");
			$data1 .= ucfirst("$d2\t");
			$data1 .= ucfirst("Thana\t");
			$data1 .= ucfirst("Outlet Name\t");
			$data1 .= ucfirst("Enrolled Date\t");
			$data1 .= ucfirst("Drop Date\t");
			$data1 .= ucfirst("Drop Reason\t");

			$data1 .= "\n";

			foreach ($results as $result) {
				$line = '';

				$d1 = ($request_data['ProgramProviderReports']['filter_type'] == 1) ? str_replace('Sales Office', '', $result['Office']['office_name']) : $result['Division']['name'];
				$line .= $d1 . "\t";

				$d2 = ($request_data['ProgramProviderReports']['filter_type'] == 1) ? $result['Territory']['name'] : $result['District']['name'];
				$line .= $d2 . "\t";

				$line .= $result['Thana']['name'] . "\t";

				$line .= $result['Outlet']['name'] . "\t";
				$line .= date('d-m-Y', strtotime($result['Program']['assigned_date'])) . "\t";

				$drop_date = $result['Program']['deassigned_date'] ? date('d-m-Y', strtotime($result['Program']['deassigned_date'])) : '';
				$line .= $drop_date . "\t";
				$line .= $result['Program']['reason'] . "\t";


				$data1 .= trim($line) . "\n";
			}
		} else {
			$d1 = ($request_data['ProgramProviderReports']['filter_type'] == 1) ? 'Sales Office' : 'Division';
			$d2 = ($request_data['ProgramProviderReports']['filter_type'] == 1) ? 'Territory' : 'District';

			$data1 .= ucfirst("$d1\t");
			$data1 .= ucfirst("$d2\t");
			$data1 .= ucfirst("Thana\t");
			$data1 .= ucfirst("Active Provider\t");
			$data1 .= ucfirst("Visited Provider (OC)\t");
			$data1 .= ucfirst("Total Visit (EC)\t");
			$data1 .= ucfirst("Total Revenue\t");
			$data1 .= ucfirst("CYP\t");

			$data1 .= "\n";

			foreach ($results as $result) {
				$line = '';

				$d1 = ($request_data['ProgramProviderReports']['filter_type'] == 1) ? str_replace('Sales Office', '', $result['Office']['office_name']) : $result['Division']['name'];
				$line .= $d1 . "\t";

				$d2 = ($request_data['ProgramProviderReports']['filter_type'] == 1) ? $result['Territory']['name'] : $result['District']['name'];
				$line .= $d2 . "\t";

				$line .= $result['Thana']['name'] . "\t";

				$line .= $result[0]['active'] . "\t";
				$line .= @$final_results[$result['Thana']['id']]['oc'] ? $final_results[$result['Thana']['id']]['oc'] : '0';
				$line .= "\t";
				$line .= @$final_results[$result['Thana']['id']]['ec'] ? $final_results[$result['Thana']['id']]['ec'] : '0';
				$line .= "\t";
				$line .= @$final_results[$result['Thana']['id']]['value'] ? sprintf("%01.2f", $final_results[$result['Thana']['id']]['value']) : '0';
				$line .= "\t";
				$line .= @$final_results[$result['Thana']['id']]['cyp'] ? $final_results[$result['Thana']['id']]['cyp'] : '0';
				$line .= "\t";

				$data1 .= trim($line) . "\n";
			}
		}


		//exit;

		$data1 = str_replace("\r", "", $data1);
		if ($data1 == "") {
			$data1 = "\n(0) Records Found!\n";
		}

		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: attachment; filename=\"Program-Provider-Reports-" . date("jS-F-Y-H:i:s") . ".xls\"");
		header("Pragma: no-cache");
		header("Expires: 0");

		echo $data1;

		exit;

		$this->autoRender = false;
	}
	function get_product_brands()
	{
		$category_id = $this->request->data['category_id'];
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$brands = $this->Product->Brand->find('all', array(
			'conditions' => array(
				'Product.product_type_id' => 1,
				'Product.source' => 'SMC',
				'Product.product_category_id' => $category_id
			),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'conditions' => 'Product.brand_id=Brand.id'
				),
			),
			'fields' => array('Brand.id', 'Brand.name'),
			'group' => array('Brand.id', 'Brand.name'),
			'order' => array('Brand.name' => 'asc'),
			'recursive' => -1
		));
		$data_array = Set::extract($brands, '{n}.Brand');

		if (!empty($brands)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}
	function get_product_variant()
	{
		$brand_id = $this->request->data['brand_id'];
		$category_id = $this->request->data['category_id'];
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$variants = $this->Product->Variant->find('all', array(
			'conditions' => array(
				'Product.product_type_id' => 1,
				'Product.source' => 'SMC',
				'Product.brand_id' => $brand_id
			),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'conditions' => 'Product.variant_id=Variant.id'
				),
			),
			'order' => array('Variant.name' => 'asc'),
			'fields' => array('Variant.id', 'Variant.name'),
			'group' => array('Variant.id', 'Variant.name'),
			'recursive' => -1
		));
		$data_array = Set::extract($variants, '{n}.Variant');

		if (!empty($variants)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}
	function get_product_list_by_variant()
	{
		$brand_id = $this->request->data['brand_id'];
		$category_id = $this->request->data['category_id'];
		$variant_id = $this->request->data['variant_id'];
		$rs = array(array('id' => '', 'name' => '---- All -----'));

		$product_list = $this->Product->find('all', array(
			'conditions' => array(
				'Product.product_type_id' => 1,
				'Product.source' => 'SMC',

				'Product.variant_id' => $variant_id,
			),
			'order' => array('Product.order'),
			'fields' => array('Product.id', 'Product.name'),
			'group' => array('Product.id', 'Product.name', 'Product.order'),
			'recursive' => -1
		));

		$data_array = Set::extract($product_list, '{n}.Product');

		if (!empty($product_list)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}


	public function get_program_officer_list(){

		$this->autoRender = false;

		$this->loadModel('Program');

		$office_id = $this->request->data['office_id'];
		$program_type_id = $this->request->data['program_type_id'];

		$options = array(
			'conditions' => array('Program.program_type_id'=>$program_type_id, 'Program.officer_id'=>$office_id),
			'joins' => array(
				array(
					'table' => 'sales_people',
					'alias' => 'SalesPerson',
					'conditions' => 'SalesPerson.id=Program.program_officer_id',
					'type' => 'Left'
				),
			),
			'fields'=>array(
				'SalesPerson.id',
				'SalesPerson.name',
			),
			'group'=>array(
				'SalesPerson.id',
				'SalesPerson.name',
			),
			'recursive'=>-1
		);
		$pof_list = $this->Program->find('all', $options);
		
		$output = "<option value=''>--- Select ---</option>";
        
        foreach ($pof_list as $key => $val) {
        	$name = $val['SalesPerson']['name'];
        	$id = $val['SalesPerson']['id'];
            $output .= "<option value='$id'>$name</option>";
        }
            
        echo $output;
        $this->autoRender = false;
		
	}










}
