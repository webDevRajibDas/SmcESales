<?php
App::uses('AppController', 'Controller');
/**
 * OutletCharacteristicSettings Controller
 *
 * @property OutletCharacteristicReport $OutletCharacteristicReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
 
 //Configure::write('debug',2);
class QueryOnSalesReportsController extends AppController
{



	/**
	 * Components
	 *
	 * @var array
	 */

	public $uses = array('Product', 'ProductCategory', 'Memo', 'Office', 'Territory', 'Division', 'District', 'Thana', 'Market', 'OutletCategory', 'Outlet', 'TerritoryAssignHistory', 'SalesPerson', 'Institute');
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

		ini_set('memory_limit', '2048M');
		ini_set('max_execution_time', 99999); //300 seconds = 5 minutes


		$this->set('page_title', "Query On Sales Information");

		$territories = array();
		$request_data = array();
		$report_type = array();
		$so_list = array();


		//for region office list
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id' => 3),
			'order' => array('office_name' => 'asc')
		));

		//types
		$types = array(
			'territory' => 'By Terriotry',
			'so' => 'By SO',
		);
		$this->set(compact('types'));

		//report type
		$unit_types = array(
			'1' => 'Sales Unit',
			'2' => 'Base Unit',
		);
		$this->set(compact('unit_types'));

		$compotators = array(
			'<' => '<',
			'>' => '>',
			'<=' => '<=',
			'>=' => '>=',
			'between' => 'Between',
		);
		$this->set(compact('compotators'));

		$memo_totals = array(
			'permemo' => 'Per Memo',
			'memototal' => 'Memo Total',
		);
		$this->set(compact('memo_totals'));




		//for outlet category list
		$outlet_categories = $this->OutletCategory->find('list', array(
			'conditions' => array('is_active' => 1),
			'order' => array('category_name' => 'asc')
		));
		$this->set(compact('outlet_categories'));


		$conditions = array(
			'NOT' => array('Product.product_category_id' => 32),
			'is_active' => 1,
			'Product.product_type_id' => 1
		);
		$conditions['is_virtual'] =0;
		$product_list = $this->Product->find('list', array(
			'conditions' => $conditions,
			'order' =>  array('order' => 'asc')
		));
		$this->set(compact('product_list'));

		//product_measurement
		$product_measurement = $this->Product->find('list', array(
			//'conditions'=> $pro_conditions,
			'fields' => array('Product.id', 'Product.sales_measurement_unit_id'),
			'order' =>  array('order' => 'asc'),
			'recursive' => -1
		));
		$this->set(compact('product_measurement'));



		$region_office_id = 0;

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

			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,

					"NOT" => array("id" => array(30, 31, 37))
				),
				'order' => array('office_name' => 'asc')
			));

			$office_ids = array_keys($offices);

			if ($office_ids) $conditions['Territory.office_id'] = $office_ids;

			//pr($conditions);
			//exit;

		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
			$office_id = $this->UserAuth->getOfficeId();

			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'id' 	=> $office_id,
				),
				'order' => array('office_name' => 'asc')
			));

			/*$territories = $this->Territory->find('list', array(
				'conditions' => array('Territory.office_id' => $office_id),
				'order' => array('Territory.name' => 'asc')
				));	*/

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

			//get SO list
			$so_list_r = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
				'conditions' => array(
					'SalesPerson.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					'User.user_group_id' => 4,
				),
				'recursive' => 0
			));
			foreach ($so_list_r as $key => $value) {
				$so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
			}
		}

		//pr($offices);


		if ($this->request->is('post') || $this->request->is('put')) {
			$all_offices = $this->Office->find('list', array(
				'conditions' => array(
					'office_type_id' 	=> 2,
					//'parent_office_id' 	=> $region_office_id,

					"NOT" => array("id" => array(30, 31, 37))
				),
				'order' => array('office_name' => 'asc')
			));

			$request_data = $this->request->data;

			$request_data1 = $this->request->data['QueryOnSalesReports'];
			//pr($request_data1);
			$product_rows = array();
			$product_ids = array();
			$product_compotator = array();
			$in = 0;
			foreach ($request_data1['product_id'] as $i => $p) {
				if (!empty($p)) {
					array_push($product_ids, $p);
					$product_rows[$in]['product_id'] = $p;
					$product_rows[$in]['compotator'] = $request_data1['compotator'][$i];
					$product_rows[$in]['qty'] = $request_data1['qty'][$i];
					$product_rows[$in]['qty2'] = @$request_data1['qty2'][$i] ? $request_data1['qty2'][$i] : 0;
					$product_rows[$in]['memo_total'] = $request_data1['memo_total'][$i];
					$in++;

					$product_compotator[$p]['compotator'] = $request_data1['compotator'][$i];
					$product_compotator[$p]['qty'] = $request_data1['qty'][$i];
					$product_compotator[$p]['qty2'] = @$request_data1['qty2'][$i] ? $request_data1['qty2'][$i] : 0;
					$product_compotator[$p]['memo_total'] = $request_data1['memo_total'][$i];
				}
			}
			$this->set(compact('product_rows'));
			$this->set(compact('product_compotator'));

			//pr($product_rows);
			//pr($product_ids);
			//exit;


			$date_from = date('Y-m-d', strtotime($request_data['QueryOnSalesReports']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['QueryOnSalesReports']['date_to']));
			$this->set(compact('date_from', 'date_to'));

			$type = $this->request->data['QueryOnSalesReports']['type'];
			$this->set(compact('type'));

			$region_office_id = isset($this->request->data['QueryOnSalesReports']['region_office_id']) != '' ? $this->request->data['QueryOnSalesReports']['region_office_id'] : $region_office_id;
			$this->set(compact('region_office_id'));
			$office_ids = array();
			if ($region_office_id) {
				$offices = $this->Office->find('list', array(
					'conditions' => array(
						'office_type_id' 	=> 2,
						'parent_office_id' 	=> $region_office_id,

						"NOT" => array("id" => array(30, 31, 37))
					),
					'order' => array('office_name' => 'asc')
				));

				$office_ids = array_keys($offices);
			}

			$office_id = isset($this->request->data['QueryOnSalesReports']['office_id']) != '' ? $this->request->data['QueryOnSalesReports']['office_id'] : $office_id;
			$this->set(compact('office_id'));

			$territory_id = isset($this->request->data['QueryOnSalesReports']['territory_id']) != '' ? $this->request->data['QueryOnSalesReports']['territory_id'] : 0;
			$this->set(compact('territory_id'));

			$so_id = isset($this->request->data['QueryOnSalesReports']['so_id']) != '' ? $this->request->data['QueryOnSalesReports']['so_id'] : 0;
			$this->set(compact('so_id'));

			$unit_type = $this->request->data['QueryOnSalesReports']['unit_type'];
			$this->set(compact('unit_type'));
			$unit_type_text = ($unit_type == 2) ? 'Base' : 'Sales';
			$this->set(compact('unit_type_text'));

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


			//get SO list
			$so_list_r = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
				'conditions' => array(
					'SalesPerson.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					'User.user_group_id' => 4,
				),
				'recursive' => 0
			));

			foreach ($so_list_r as $key => $value) {
				$so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
			}

			//add old so from territory_assign_histories
			if ($office_id) {
				$conditions = array('Territory.office_id' => $office_id, 'TerritoryAssignHistory.assign_type' => 2);
				// $conditions['TerritoryAssignHistory.date BETWEEN ? and ? '] = array($date_from, $date_to);
				$conditions['TerritoryAssignHistory.date >= '] = $date_from;
				//pr($conditions);
				$old_so_list = $this->TerritoryAssignHistory->find('all', array(
					'conditions' => $conditions,
					'order' =>  array('Territory.name' => 'asc'),
					'recursive' => 0
				));
				if ($old_so_list) {
					foreach ($old_so_list as $old_so) {
						$so_list[$old_so['TerritoryAssignHistory']['so_id']] = $old_so['SalesPerson']['name'];
					}
				}
			}

			$outlet_category_id = isset($this->request->data['QueryOnSalesReports']['outlet_category_id']) != '' ? $this->request->data['QueryOnSalesReports']['outlet_category_id'] : 0;


			$pro_con = array(
				'NOT' => array('Product.product_category_id' => 32),
				'is_active' => 1,
				'Product.product_type_id' => 1,
				'Product.id' => $product_ids
			);
			$all_products = $this->Product->find('list', array(
				'conditions' => $pro_con,
				'order' =>  array('order' => 'asc')
			));
			$this->set(compact('all_products'));

			//For Query Conditon
			$conditions = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Memo.gross_value >' => 0,
				'Memo.status !=' => 0,
				'MemoDetail.price >' => 0,
				/*'Memo.outlet_id' => 297037*/
			);


			if ($office_ids) $conditions['Memo.office_id'] = $office_ids;
			if ($office_id) $conditions['Memo.office_id'] = $office_id;

			if ($type == 'so') {
				if ($so_id) $conditions['Memo.sales_person_id'] = $so_id;
			} else {
				if ($territory_id) $conditions['Memo.territory_id'] = $territory_id;
			}

			if ($outlet_category_id) $conditions['Outlet.category_id'] = $outlet_category_id;
			if ($product_ids) $conditions['MemoDetail.product_id'] = $product_ids;

			//pr($conditions);
			//exit;

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
						'alias' => 'Product',
						'table' => 'products',
						'type' => 'INNER',
						'conditions' => 'MemoDetail.product_id = Product.id'
					),
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'Memo.territory_id = Territory.id'
					),
					/*array(
						'alias' => 'SalesPeople',
						'table' => 'sales_people',
						'type' => 'INNER',
						'conditions' => 'Memo.sales_person_id = SalesPeople.id'
					),*/
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
						'conditions' => 'Market.thana_id = Thana.id'
					),
					array(
						'alias' => 'District',
						'table' => 'districts',
						'type' => 'INNER',
						'conditions' => 'Thana.district_id = District.id'
					)
				),

				'fields' => array('COUNT(Memo.outlet_id) as ec', 'SUM(MemoDetail.sales_qty) as sales_qty', 'SUM(MemoDetail.sales_qty*MemoDetail.price) as price', 'MemoDetail.memo_id', 'MemoDetail.product_id', 'Product.name', 'Product.order',  'Memo.office_id', 'Memo.territory_id', 'Territory.name', 'Memo.outlet_id', 'Outlet.name', 'Outlet.category_id', 'Market.name', 'Thana.name', 'District.name'),

				'group' => array('MemoDetail.memo_id', 'MemoDetail.product_id', 'Product.name',  'Product.order',  'Memo.outlet_id', 'Outlet.name', 'Outlet.category_id', 'Memo.office_id', 'Memo.territory_id', 'Territory.name', 'Market.name', 'Thana.name', 'District.name'),

				'order' => array('Product.order asc', 'sales_qty desc', 'Outlet.name asc'),

				//'order' => $order,

				'recursive' => -1,
				//'limit' => 200
			));
			/*echo $this->Memo->getLastQuery();
			pr($q_results);
			exit;*/


			//pr($product_compotator);
			//exit;

			$results = array();
			$results2 = array();
			$memo_total_datas = array();
			foreach ($q_results as $result) {
				//$sales_qty = ($unit_type==1)?$result[0]['sales_qty']:$this->unit_convertfrombase($result['MemoDetail']['product_id'], $result['Product']['sales_measurement_unit_id'], $result[0]['sales_qty']);

				if ($product_compotator[$result['MemoDetail']['product_id']]['memo_total'] == 'permemo') {
					$sales_qty = ($unit_type == 1) ? $result[0]['sales_qty'] : $this->unit_convert($result['MemoDetail']['product_id'], $product_measurement[$result['MemoDetail']['product_id']], $result[0]['sales_qty']);

					//$results[$result['Product']['name']][$result['Memo']['outlet_id']] = 
					// $results[$result['MemoDetail']['product_id']][$result['Memo']['outlet_id']] = 
					$results[$result['MemoDetail']['product_id']][$result['MemoDetail']['memo_id']] =
						array(
							'product_id' 			=> $result['MemoDetail']['product_id'],
							'product_name' 			=> $result['Product']['name'],
							'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
							'sales_qty2' 			=> $result[0]['sales_qty'],
							'price' 				=> $result[0]['price'],
							'ec' 					=> $result[0]['ec'],
							'office_name' 			=> $all_offices[$result['Memo']['office_id']],
							'outlet_id' 			=> $result['Memo']['outlet_id'],
							'outlet_name' 			=> $result['Outlet']['name'],
							'outlet_category_id' 	=> $result['Outlet']['category_id'],
							'market_name' 			=> $result['Market']['name'],
							'thana_name' 			=> $result['Thana']['name'],
							'district_name' 		=> $result['District']['name'],
							'territory_name' 		=> $result['Territory']['name']
						);
				} else {
					$sales_qty = ($unit_type == 1) ? $result[0]['sales_qty'] : $this->unit_convert($result['MemoDetail']['product_id'], $product_measurement[$result['MemoDetail']['product_id']], $result[0]['sales_qty']);

					$memo_total_datas[$result['MemoDetail']['product_id']][$result['Memo']['outlet_id']][$result['MemoDetail']['memo_id']] =
						array(
							'product_id' 			=> $result['MemoDetail']['product_id'],
							'product_name' 			=> $result['Product']['name'],
							'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
							'sales_qty2' 			=> $result[0]['sales_qty'],
							'price' 				=> $result[0]['price'],
							'ec' 					=> $result[0]['ec'],
							'office_name' 			=> $all_offices[$result['Memo']['office_id']],
							'outlet_id' 			=> $result['Memo']['outlet_id'],
							'outlet_name' 			=> $result['Outlet']['name'],
							'outlet_category_id' 	=> $result['Outlet']['category_id'],
							'market_name' 			=> $result['Market']['name'],
							'thana_name' 			=> $result['Thana']['name'],
							'territory_name' 		=> $result['Territory']['name'],
							'district_name' 		=> $result['District']['name']
						);
				}
			}

			//pr($results[27]);
			//exit;

			$this->set(compact('results'));
			
			//echo '<pre>';print_r($memo_total_datas);exit;
				
			if ($memo_total_datas) {
				foreach ($memo_total_datas as $product_id => $outlet_datas) {
					//pr($outlet_datas);
					foreach ($outlet_datas as $outlet_id => $memo_datas) {
						$sales_qty = 0;
						$price = 0;
						$ec = 0;

						foreach ($memo_datas as $memo_id => $product_data) {
							$sales_qty += $product_data['sales_qty'];
							$price += $product_data['price'];
							$ec += $product_data['ec'];
							$results2[$product_data['product_id']][$product_data['outlet_id']] =
								array(
									'product_id' 			=> $product_data['product_id'],
									'product_name' 			=> $product_data['product_name'],
									'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
									//'sales_qty2' 			=> $product_data['sales_qty'],
									'price' 				=> $price,
									'ec' 					=> $ec,
									'office_name' 			=> $all_offices[$result['Memo']['office_id']],
									'outlet_name' 			=> $product_data['outlet_name'],
									'outlet_category_id' 	=> $product_data['outlet_category_id'],
									'market_name' 			=> $product_data['market_name'],
									'thana_name' 			=> $product_data['thana_name'],
									//'territory_name' 		=> $result['Territory']['name'],
									'territory_name' 		=> $product_data['territory_name'],
									'district_name' 		=> $product_data['district_name']
								);
						}
					}
				}
			}
			
			//echo '<pre>';print_r($results2);exit;

			//pr($results2);
			//exit;



			$this->set(compact('results2'));
		}

		$this->set(compact('offices', 'territories', 'outlet_type', 'region_offices', 'office_id', 'request_data', 'so_list'));
	}
}
