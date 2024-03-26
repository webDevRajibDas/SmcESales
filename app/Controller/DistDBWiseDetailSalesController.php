<?php
App::uses('AppController', 'Controller');
/**
 * ProjectionAchievementSettings Controller
 *
 * @property ProjectionAchievementReport $ProjectionAchievementReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistDbWiseDetailSalesController extends AppController
{
	/**
	 * Components
	 *
	 * @var array
	 * 
	 */

	public $uses = array('Product', 'ProductCategory', 'DistMemo', 'Office', 'Territory', 'Division', 'District', 'Thana', 'Market', 'OutletCategory', 'Outlet', 'TerritoryAssignHistory', 'SalesPerson', 'Brand');
	public $components = array('Paginator', 'Session', 'Filter.Filter');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index($id = null)
	{

		ini_set('memory_limit', '2048M');
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes
		$this->set('page_title', 'Product Wise Monthly Sales Detail');
		$territories = array();
		$request_data = array();
		$report_type = array();
		$so_list = array();
		//types
		$types = array(
			'db' => 'By DB',
			'sr' => 'By SR',
		);
		$this->set(compact('types'));

		$columns = array(
			'product' => 'By Product',
			'brand' => 'By Brand',
			'category' => 'By Category'
		);
		$this->set(compact('columns'));


		//for region office list
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id' => 3),
			'order' => array('office_name' => 'asc')
		));

		//products
		$conditions = array(
			'NOT' => array('Product.product_category_id' => 32),
			'is_active' => 1,
			'product_type_id' => 1
		);
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

		//report type
		$unit_types = array(
			'1' => 'Sales Unit',
			'2' => 'Base Unit',
		);
		$this->set(compact('unit_types'));





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
		}



		if ($this->request->is('post') || $this->request->is('put')) {
			$request_data = $this->request->data;

			$this->set(compact('request_data'));

			$date_from = date('Y-m-d', strtotime($request_data['DistDbWiseDetailSales']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['DistDbWiseDetailSales']['date_to']));
			$this->set(compact('date_from', 'date_to'));

			$type = $this->request->data['DistDbWiseDetailSales']['type'];
			$this->set(compact('type'));

			$region_office_id = isset($this->request->data['DistDbWiseDetailSales']['region_office_id']) != '' ? $this->request->data['DistDbWiseDetailSales']['region_office_id'] : $region_office_id;
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
			} else {
				$offices = $this->Office->find('list', array(
					'conditions' => array(
						'office_type_id' 	=> 2,
						/*'parent_office_id' 	=> $region_office_id,*/

						"NOT" => array("id" => array(30, 31, 37))
					),
					'order' => array('office_name' => 'asc')
				));

				$office_ids = array_keys($offices);
			}

			$office_id = isset($this->request->data['DistDbWiseDetailSales']['office_id']) != '' ? $this->request->data['DistDbWiseDetailSales']['office_id'] : $office_id;
			$this->set(compact('office_id'));

			$db_id = isset($this->request->data['DistDbWiseDetailSales']['db_id']) != '' ? $this->request->data['DistDbWiseDetailSales']['db_id'] : 0;
			$this->set(compact('db_iddb_id'));

			$sr_id = isset($this->request->data['DistDbWiseDetailSales']['sr_id']) != '' ? $this->request->data['DistDbWiseDetailSales']['sr_id'] : 0;
			$this->set(compact('sr_id'));

			$unit_type = $this->request->data['DistDbWiseDetailSales']['unit_type'];
			$this->set(compact('unit_type'));
			$unit_type_text = ($unit_type == 2) ? 'Base' : 'Sales';
			$this->set(compact('unit_type_text'));

			$columns = $this->request->data['DistDbWiseDetailSales']['columns'];

			$outlet_category_id = isset($this->request->data['DistDbWiseDetailSales']['outlet_category_id']) != '' ? $this->request->data['DistDbWiseDetailSales']['outlet_category_id'] : 0;


			//For Query Conditon
			$conditions = array(
				'DistMemo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'DistMemo.gross_value >' => 0,
				'DistMemo.status !=' => 0,
				'DistMemoDetail.price >' => 0
			);


			if ($office_ids) $conditions['DistMemo.office_id'] = $office_ids;
			if ($office_id) $conditions['DistMemo.office_id'] = $office_id;

			if ($type == 'sr') {
				if ($sr_id) $conditions['DistMemo.sr_id'] = $sr_id;
			} else {
				if ($db_id) $conditions['DistMemo.distributor_id'] = $db_id;
			}

			$product_ids = isset($this->request->data['DistDbWiseDetailSales']['product_id']) != '' ? $this->request->data['DistDbWiseDetailSales']['product_id'] : 0;
			$brand_ids = isset($this->request->data['DistDbWiseDetailSales']['brand_id']) != '' ? $this->request->data['DistDbWiseDetailSales']['brand_id'] : 0;
			$product_category_ids = isset($this->request->data['DistDbWiseDetailSales']['product_category_id']) != '' ? $this->request->data['DistDbWiseDetailSales']['product_category_id'] : 0;

			if ($product_ids) $conditions['DistMemoDetail.product_id'] = $product_ids;
			if ($brand_ids) $conditions['Product.brand_id'] = $brand_ids;
			if ($product_category_ids) $conditions['Product.product_category_id'] = $product_category_ids;

			//pr($conditions);
			//exit;

			$q_results = $this->DistMemo->find('all', array(
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
						'alias' => 'DistDistributor',
						'table' => 'dist_distributors',
						'type' => 'INNER',
						'conditions' => 'DistMemo.distributor_id = DistDistributor.id'
					),
					array(
						'alias' => 'DistSalesRepresentative',
						'table' => 'dist_sales_representatives',
						'type' => 'INNER',
						'conditions' => 'DistMemo.sr_id = DistSalesRepresentative.id'
					),
					array(
						'alias' => 'Outlet',
						'table' => 'dist_outlets',
						'type' => 'INNER',
						'conditions' => 'DistMemo.outlet_id = Outlet.id'
					),
					array(
						'alias' => 'Market',
						'table' => 'dist_markets',
						'type' => 'INNER',
						'conditions' => 'DistMemo.market_id = Market.id'
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

				'fields' => array(
					'SUM(ROUND((DistMemoDetail.sales_qty * CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END ),0)) as sales_qty',
					'SUM(DistMemoDetail.sales_qty*DistMemoDetail.price) as price',
					'DistMemoDetail.product_id',
					'Product.name',
					'Product.brand_id',
					'Product.product_category_id',
					'Product.sales_measurement_unit_id',
					'Product.order',
					'DistMemo.dist_memo_no',
					'DistMemo.memo_date',
					'DistDistributor.name',
					'DistSalesRepresentative.name',
					'DistMemo.outlet_id',
					'Outlet.name',
					'Market.name',
					'Thana.name',
					'District.name'
				),

				'group' => array(
					'DistMemoDetail.product_id',
					'Product.name',
					'Product.brand_id',
					'Product.product_category_id',
					'Product.sales_measurement_unit_id',
					'Product.order',
					'DistMemo.dist_memo_no',
					'DistMemo.memo_date',
					'DistDistributor.name',
					'DistSalesRepresentative.name',
					'DistDistributor.name',
					'DistMemo.outlet_id',
					'Outlet.name',
					'Market.name',
					'Thana.name',
					'District.name'
				),

				'order' => array('Product.order asc', 'DistMemo.memo_date asc'),

				//'order' => $order,

				'recursive' => -1,
				//'limit' => 200
			));

			// pr($q_results);
			// exit;

			//$unit_type=1;

			$results = array();

			if ($columns == 'product') {
				foreach ($q_results as $result) {
					$sales_qty = ($unit_type == 2) ? $result[0]['sales_qty'] : $this->unit_convertfrombase($result['DistMemoDetail']['product_id'], $result['Product']['sales_measurement_unit_id'], $result[0]['sales_qty']);

					$results[$result['DistSalesRepresentative']['name'] . '<br> Distributor : ' . $result['DistDistributor']['name']][$result['Product']['name']][$result['DistMemo']['dist_memo_no']] =
						array(
							'product_id' 			=> $result['DistMemoDetail']['product_id'],
							'sales_measurement_unit_id' => $result['Product']['sales_measurement_unit_id'],
							'id' 					=> $result['DistMemoDetail']['product_id'],
							'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
							'price' 				=> sprintf("%01.2f", $result[0]['price']),

							'dist_memo_no' 				=> $result['DistMemo']['dist_memo_no'],
							'memo_date' 			=> date('d M Y', strtotime($result['DistMemo']['memo_date'])),

							'outlet_name' 			=> $result['Outlet']['name'],
							//'outlet_category_id' 	=> $result['Outlet']['category_id'],				
							'market_name' 			=> $result['Market']['name'],
							'thana_name' 			=> $result['Thana']['name'],
							'district_name' 		=> $result['District']['name']
						);
				}
			}



			if ($columns == 'brand') {

				/*foreach($q_results as $result)
				{
					$sales_qty = ($unit_type==1)?$result[0]['sales_qty']:$this->unit_convert($result['MemoDetail']['product_id'], $result['Product']['sales_measurement_unit_id'], $result[0]['sales_qty']);
									
					$results[$result['SalesPeople']['name'].' ('.$result['Territory']['name'].')'][$brands[$result['Product']['brand_id']]][$result['Memo']['dist_memo_no']] = 
					array(
						//'product_id' 			=> $result['MemoDetail']['product_id'],
						'id' 					=> $result['Product']['brand_id'],
						'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
						'price' 				=> sprintf("%01.2f", $result[0]['price']),
						
						'dist_memo_no' 				=> $result['Memo']['dist_memo_no'],	
						'memo_date' 			=> date('d M Y', strtotime($result['Memo']['memo_date'])),	
						
						'outlet_name' 			=> $result['Outlet']['name'],	
						//'outlet_category_id' 	=> $result['Outlet']['category_id'],				
						'market_name' 			=> $result['Market']['name'],
						'thana_name' 			=> $result['Thana']['name'],
						'district_name' 		=> $result['District']['name']
					);
				
				}*/


				$type_results = array();
				foreach ($q_results as $result) {
					$type_results[$result['DistSalesRepresentative']['name'] . ' - (' . $result['DistDistributor']['name'] . ')'][$result['Product']['brand_id']][$result['DistMemo']['dist_memo_no']][$result['DistMemoDetail']['product_id']] =
						array(
							'sr_name' 					=> $result['DistSalesRepresentative']['name'],
							'db_name' 			=> $result['DistDistributor']['name'],
							'product_id' 				=> $result['DistMemoDetail']['product_id'],
							'product_name' 				=> $result['Product']['name'],
							'sales_measurement_unit_id' => $result['Product']['sales_measurement_unit_id'],
							'dist_memo_no' 					=> $result['DistMemo']['dist_memo_no'],
							'sales_qty' 				=> $result[0]['sales_qty'],

							'brand_id' 					=> $result['Product']['brand_id'],
							'product_category_id' 		=> $result['Product']['product_category_id'],
							'price' 					=> sprintf("%01.2f", $result[0]['price']),
							'dist_memo_no' 					=> $result['DistMemo']['dist_memo_no'],
							'memo_date' 				=> date('d M Y', strtotime($result['DistMemo']['memo_date'])),
							'outlet_name' 				=> $result['Outlet']['name'],
							'market_name' 				=> $result['Market']['name'],
							'thana_name' 				=> $result['Thana']['name'],
							'district_name' 			=> $result['District']['name']
						);
				}
				//pr($type_results);
				//exit;

				foreach ($type_results as $so_te_name => $brand_ids) {
					foreach ($brand_ids as $brand_id => $memo_datas) {
						foreach ($memo_datas as $dist_memo_no => $product_datas) {
							$sales_qty = 0;
							$price = 0;
							foreach ($product_datas as $product_id => $p_result) {
								$sales_qty += ($unit_type == 2) ? $p_result['sales_qty'] : $this->unit_convertfrombase($p_result['product_id'], $p_result['sales_measurement_unit_id'], $p_result['sales_qty']);
								$price += $p_result['price'];

								$results[$p_result['sr_name'] . '<br>Distributor : ' . $p_result['db_name']][$brands[$p_result['brand_id']]][$p_result['dist_memo_no']] =
									array(
										'product_id' 			=> $p_result['product_id'],
										'sales_measurement_unit_id' => $p_result['sales_measurement_unit_id'],
										'id' 					=> $p_result['brand_id'],
										'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
										'price' 				=> sprintf("%01.2f", $price),

										'dist_memo_no' 				=> $p_result['dist_memo_no'],
										'memo_date' 			=> date('d M Y', strtotime($p_result['memo_date'])),

										'outlet_name' 			=> $p_result['outlet_name'],
										//'outlet_category_id' 	=> $p_result['Outlet']['category_id'],				
										'market_name' 			=> $p_result['market_name'],
										'thana_name' 			=> $p_result['thana_name'],
										'district_name' 		=> $p_result['district_name']
									);
							}
						}
					}
				}
			}

			if ($columns == 'category') {
				/*foreach($q_results as $result)
				{
					$sales_qty = ($unit_type==1)?$result[0]['sales_qty']:$this->unit_convert($result['DistMemoDetail']['product_id'], $result['Product']['sales_measurement_unit_id'], $result[0]['sales_qty']);
									
					$results[$result['SalesPeople']['name'].' ('.$result['Territory']['name'].')'][$categories[$result['Product']['product_category_id']]][$result['DistMemo']['dist_memo_no']] = 
					array(
						//'product_id' 			=> $result['DistMemoDetail']['product_id'],
						'id' 					=> $result['Product']['product_category_id'],
						'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
						'price' 				=> sprintf("%01.2f", $result[0]['price']),
						
						'dist_memo_no' 				=> $result['DistMemo']['dist_memo_no'],	
						'memo_date' 			=> date('d M Y', strtotime($result['DistMemo']['memo_date'])),	
						
						'outlet_name' 			=> $result['Outlet']['name'],	
						//'outlet_category_id' 	=> $result['Outlet']['category_id'],				
						'market_name' 			=> $result['Market']['name'],
						'thana_name' 			=> $result['Thana']['name'],
						'district_name' 		=> $result['District']['name']
					);
				
				}*/


				$type_results = array();
				foreach ($q_results as $result) {
					$type_results[$result['DistSalesRepresentative']['name'] . ' - (' . $result['DistDistributor']['name'] . ')'][$result['Product']['product_category_id']][$result['DistMemo']['dist_memo_no']][$result['DistMemoDetail']['product_id']] =
						array(
							'sr_name' 					=> $result['DistSalesRepresentative']['name'],
							'db_name' 			=> $result['DistDistributor']['name'],
							'product_id' 				=> $result['DistMemoDetail']['product_id'],
							'product_name' 				=> $result['Product']['name'],
							'sales_measurement_unit_id' => $result['Product']['sales_measurement_unit_id'],
							'dist_memo_no' 					=> $result['DistMemo']['dist_memo_no'],
							'sales_qty' 				=> $result[0]['sales_qty'],

							'brand_id' 					=> $result['Product']['brand_id'],
							'product_category_id' 		=> $result['Product']['product_category_id'],
							'price' 					=> sprintf("%01.2f", $result[0]['price']),
							'dist_memo_no' 					=> $result['DistMemo']['dist_memo_no'],
							'memo_date' 				=> date('d M Y', strtotime($result['DistMemo']['memo_date'])),
							'outlet_name' 				=> $result['Outlet']['name'],
							'market_name' 				=> $result['Market']['name'],
							'thana_name' 				=> $result['Thana']['name'],
							'district_name' 			=> $result['District']['name']
						);
				}


				foreach ($type_results as $so_te_name => $product_category_ids) {
					foreach ($product_category_ids as $product_category_id => $memo_datas) {
						foreach ($memo_datas as $dist_memo_no => $product_datas) {
							$sales_qty = 0;
							$price = 0;
							foreach ($product_datas as $product_id => $p_result) {
								$sales_qty += ($unit_type == 2) ? $p_result['sales_qty'] : $this->unit_convertfrombase($p_result['product_id'], $p_result['sales_measurement_unit_id'], $p_result['sales_qty']);
								$price += $p_result['price'];

								$results[$p_result['sr_name'] . '<br>Distributor : ' . $p_result['db_name']][$categories[$p_result['product_category_id']]][$p_result['dist_memo_no']] =
									array(
										'product_id' 			=> $p_result['product_id'],
										'sales_measurement_unit_id' => $p_result['sales_measurement_unit_id'],
										'id' 					=> $p_result['product_category_id'],
										'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
										'price' 				=> sprintf("%01.2f", $price),

										'dist_memo_no' 				=> $p_result['dist_memo_no'],
										'memo_date' 			=> date('d M Y', strtotime($p_result['memo_date'])),

										'outlet_name' 			=> $p_result['outlet_name'],
										//'outlet_category_id' 	=> $p_result['Outlet']['category_id'],				
										'market_name' 			=> $p_result['market_name'],
										'thana_name' 			=> $p_result['thana_name'],
										'district_name' 		=> $p_result['district_name']
									);
							}
						}
					}
				}
			}

			//pr($results);
			//exit;

			$this->set(compact('results'));




			//For Bonus Query Conditon
			$conditions = array(
				'DistMemo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'DistMemo.gross_value >' => 0,
				'DistMemo.status !=' => 0,
				'DistMemoDetail.price <' => 1
			);


			if ($office_ids) $conditions['DistMemo.office_id'] = $office_ids;
			if ($office_id) $conditions['DistMemo.office_id'] = $office_id;

			if ($type == 'sr') {
				if ($sr_id) $conditions['DistMemo.sr_id'] = $sr_id;
			} else {
				if ($db_id) $conditions['DistMemo.distributor_id'] = $db_id;
			}

			$product_ids = isset($this->request->data['DistDbWiseDetailSales']['product_id']) != '' ? $this->request->data['DistDbWiseDetailSales']['product_id'] : 0;
			$brand_ids = isset($this->request->data['DistDbWiseDetailSales']['brand_id']) != '' ? $this->request->data['DistDbWiseDetailSales']['brand_id'] : 0;
			$product_category_ids = isset($this->request->data['DistDbWiseDetailSales']['product_category_id']) != '' ? $this->request->data['DistDbWiseDetailSales']['product_category_id'] : 0;

			if ($product_ids) $conditions['DistMemoDetail.product_id'] = $product_ids;
			if ($brand_ids) $conditions['Product.brand_id'] = $brand_ids;
			if ($product_category_ids) $conditions['Product.product_category_id'] = $product_category_ids;
			// $conditions['Product.product_type_id'] = 1;
			//pr($conditions);
			//exit;

			$b_q_results = $this->DistMemo->find('all', array(
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
						'alias' => 'DistDistributor',
						'table' => 'dist_distributors',
						'type' => 'INNER',
						'conditions' => 'DistMemo.distributor_id = DistDistributor.id'
					),
					array(
						'alias' => 'DistSalesRepresentative',
						'table' => 'dist_sales_representatives',
						'type' => 'INNER',
						'conditions' => 'DistMemo.sr_id = DistSalesRepresentative.id'
					),
					array(
						'alias' => 'Outlet',
						'table' => 'dist_outlets',
						'type' => 'INNER',
						'conditions' => 'DistMemo.outlet_id = Outlet.id'
					),
					array(
						'alias' => 'Market',
						'table' => 'dist_markets',
						'type' => 'INNER',
						'conditions' => 'DistMemo.market_id = Market.id'
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

				'fields' => array(
					'SUM(ROUND((DistMemoDetail.sales_qty * CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END ),0)) as sales_qty',
					'SUM(DistMemoDetail.sales_qty*DistMemoDetail.price) as price',
					'DistMemoDetail.product_id',
					'Product.name',
					'Product.brand_id',
					'Product.product_category_id',
					'Product.sales_measurement_unit_id',
					'Product.order',
					'DistMemo.dist_memo_no',
					'DistMemo.memo_date',
					'DistSalesRepresentative.name',
					'DistDistributor.name',
					'DistMemo.outlet_id',
					'Outlet.name',
					'Market.name',
					'Thana.name',
					'District.name'
				),

				'group' => array(
					'DistMemoDetail.product_id',
					'Product.name',
					'Product.brand_id',
					'Product.product_category_id',
					'Product.sales_measurement_unit_id',
					'Product.order',
					'DistMemo.dist_memo_no',
					'DistMemo.memo_date',
					'DistSalesRepresentative.name',
					'DistDistributor.name',
					'DistMemo.outlet_id',
					'Outlet.name',
					'Market.name',
					'Thana.name',
					'District.name'
				),

				'order' => array('Product.order asc', 'DistMemo.memo_date asc'),

				//'order' => $order,

				'recursive' => -1,
				//'limit' => 200
			));

			$b_results = array();

			if ($columns == 'product') {
				foreach ($b_q_results as $result) {
					$sales_qty = ($unit_type == 2) ? $result[0]['sales_qty'] : $this->unit_convertfrombase($result['DistMemoDetail']['product_id'], $result['Product']['sales_measurement_unit_id'], $result[0]['sales_qty']);

					$b_results[$result['DistSalesRepresentative']['name'] . '<br> Distributor : ' . $result['DistDistributor']['name']][$result['Product']['name']][$result['DistMemo']['dist_memo_no']] =
						array(
							'product_id' 			=> $result['DistMemoDetail']['product_id'],
							'sales_measurement_unit_id' => $result['Product']['sales_measurement_unit_id'],
							'id' 					=> $result['DistMemoDetail']['product_id'],
							'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
							'price' 				=> sprintf("%01.2f", $result[0]['price']),

							'dist_memo_no' 				=> $result['DistMemo']['dist_memo_no'],
							'memo_date' 			=> date('d M Y', strtotime($result['DistMemo']['memo_date'])),

							'outlet_name' 			=> $result['Outlet']['name'],
							//'outlet_category_id' 	=> $result['Outlet']['category_id'],				
							'market_name' 			=> $result['Market']['name'],
							'thana_name' 			=> $result['Thana']['name'],
							'district_name' 		=> $result['District']['name']
						);
				}
			}



			if ($columns == 'brand') {
				$type_results = array();
				foreach ($b_q_results as $result) {
					$type_results[$result['DistSalesRepresentative']['name'] . ' - (' . $result['DistDistributor']['name'] . ')'][$result['Product']['brand_id']][$result['DistMemo']['dist_memo_no']][$result['DistMemoDetail']['product_id']] =
						array(
							'sr_name' 					=> $result['DistSalesRepresentative']['name'],
							'db_name' 			=> $result['DistDistributor']['name'],
							'product_id' 				=> $result['DistMemoDetail']['product_id'],
							'product_name' 				=> $result['Product']['name'],
							'sales_measurement_unit_id' => $result['Product']['sales_measurement_unit_id'],
							'dist_memo_no' 					=> $result['DistMemo']['dist_memo_no'],
							'sales_qty' 				=> $result[0]['sales_qty'],

							'brand_id' 					=> $result['Product']['brand_id'],
							'product_category_id' 		=> $result['Product']['product_category_id'],
							'price' 					=> sprintf("%01.2f", $result[0]['price']),
							'dist_memo_no' 					=> $result['DistMemo']['dist_memo_no'],
							'memo_date' 				=> date('d M Y', strtotime($result['DistMemo']['memo_date'])),
							'outlet_name' 				=> $result['Outlet']['name'],
							'market_name' 				=> $result['Market']['name'],
							'thana_name' 				=> $result['Thana']['name'],
							'district_name' 			=> $result['District']['name']
						);
				}
				foreach ($type_results as $so_te_name => $brand_ids) {
					foreach ($brand_ids as $brand_id => $memo_datas) {
						foreach ($memo_datas as $dist_memo_no => $product_datas) {
							$sales_qty = 0;
							$price = 0;
							foreach ($product_datas as $product_id => $p_result) {
								$sales_qty += ($unit_type == 2) ? $p_result['sales_qty'] : $this->unit_convertfrombase($p_result['product_id'], $p_result['sales_measurement_unit_id'], $p_result['sales_qty']);
								$price += $p_result['price'];

								$b_results[$p_result['sr_name'] . '<br>Distributor : ' . $p_result['db_name']][$brands[$p_result['brand_id']]][$p_result['dist_memo_no']] =
									array(
										'product_id' 			=> $p_result['product_id'],
										'sales_measurement_unit_id' => $p_result['sales_measurement_unit_id'],
										'id' 					=> $p_result['brand_id'],
										'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
										'price' 				=> sprintf("%01.2f", $price),

										'dist_memo_no' 				=> $p_result['dist_memo_no'],
										'memo_date' 			=> date('d M Y', strtotime($p_result['memo_date'])),

										'outlet_name' 			=> $p_result['outlet_name'],
										//'outlet_category_id' 	=> $p_result['Outlet']['category_id'],				
										'market_name' 			=> $p_result['market_name'],
										'thana_name' 			=> $p_result['thana_name'],
										'district_name' 		=> $p_result['district_name']
									);
							}
						}
					}
				}
			}

			if ($columns == 'category') {
				$type_results = array();
				foreach ($b_q_results as $result) {
					$type_results[$result['DistSalesRepresentative']['name'] . ' - (' . $result['DistDistributor']['name'] . ')'][$result['Product']['product_category_id']][$result['DistMemo']['dist_memo_no']][$result['DistMemoDetail']['product_id']] =
						array(
							'sr_name' 					=> $result['DistSalesRepresentative']['name'],
							'db_name' 					=> $result['DistDistributor']['name'],
							'product_id' 				=> $result['DistMemoDetail']['product_id'],
							'product_name' 				=> $result['Product']['name'],
							'sales_measurement_unit_id' => $result['Product']['sales_measurement_unit_id'],
							'dist_memo_no' 					=> $result['DistMemo']['dist_memo_no'],
							'sales_qty' 				=> $result[0]['sales_qty'],

							'brand_id' 					=> $result['Product']['brand_id'],
							'product_category_id' 		=> $result['Product']['product_category_id'],
							'price' 					=> sprintf("%01.2f", $result[0]['price']),
							'dist_memo_no' 					=> $result['DistMemo']['dist_memo_no'],
							'memo_date' 				=> date('d M Y', strtotime($result['DistMemo']['memo_date'])),
							'outlet_name' 				=> $result['Outlet']['name'],
							'market_name' 				=> $result['Market']['name'],
							'thana_name' 				=> $result['Thana']['name'],
							'district_name' 			=> $result['District']['name']
						);
				}


				foreach ($type_results as $so_te_name => $product_category_ids) {
					foreach ($product_category_ids as $product_category_id => $memo_datas) {
						foreach ($memo_datas as $dist_memo_no => $product_datas) {
							$sales_qty = 0;
							$price = 0;
							foreach ($product_datas as $product_id => $p_result) {
								$sales_qty += ($unit_type == 2) ? $p_result['sales_qty'] : $this->unit_convertfrombase($p_result['product_id'], $p_result['sales_measurement_unit_id'], $p_result['sales_qty']);
								$price += $p_result['price'];

								$b_results[$p_result['sr_name'] . '<br>Distributor : ' . $p_result['db_name']][$categories[$p_result['product_category_id']]][$p_result['dist_memo_no']] =
									array(
										'product_id' 			=> $p_result['product_id'],
										'sales_measurement_unit_id' => $p_result['sales_measurement_unit_id'],
										'id' 					=> $p_result['product_category_id'],
										'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
										'price' 				=> sprintf("%01.2f", $price),

										'dist_memo_no' 				=> $p_result['dist_memo_no'],
										'memo_date' 			=> date('d M Y', strtotime($p_result['memo_date'])),

										'outlet_name' 			=> $p_result['outlet_name'],
										//'outlet_category_id' 	=> $p_result['Outlet']['category_id'],				
										'market_name' 			=> $p_result['market_name'],
										'thana_name' 			=> $p_result['thana_name'],
										'district_name' 		=> $p_result['district_name']
									);
							}
						}
					}
				}
			}

			/* pr($b_results);
			exit; */

			$this->set(compact('b_results'));

			$output = '';

			$g_sales_qty = 0;
			$g_price = 0;
			$g_bonus = 0;

			//pr($results);
			//exit;

			foreach ($results as $so_name => $product_datas) {

				$output .= '<tr><td style="text-align:left; font-weight:bold;" colspan="7">Sales Representative : ' . $so_name . '</td></tr>';

				foreach ($product_datas as $product_name => $memo_datas) {
					$i = 1;
					$total_sales_qty = 0;
					$total_price = 0;
					$total_bonus = 0;

					foreach ($memo_datas as $memo_data) {
						$b_sales_qty = @$b_results[$so_name][$product_name][$memo_data['dist_memo_no']]['sales_qty'] ? $b_results[$so_name][$product_name][$memo_data['dist_memo_no']]['sales_qty'] : "0";
						unset($b_results[$so_name][$product_name][$memo_data['dist_memo_no']]);
						// $b_sales_qty = ($unit_type==2)?$b_sales_qty:$this->unit_convertfrombase($memo_data['product_id'], $memo_data['sales_measurement_unit_id'], $b_sales_qty);
						$total_bonus += @$b_sales_qty;

						$sales_qty = $memo_data['sales_qty'];
						$total_sales_qty += $sales_qty;
						$total_price += $memo_data['price'];

						$output .= '<tr>';
						$p_name = $i == 1 ? $product_name : '';
						$output .= '<td style="text-align:left;"><b>' . $p_name . '</b></td>';
						$output .= '<td style="text-align:left;">' . $memo_data['outlet_name'] . '-' . $memo_data['market_name'] . '-' . $memo_data['thana_name'] . '-' . $memo_data['district_name'] . '</td>
								<td>' . $memo_data['memo_date'] . '</td>
								<td style="mso-number-format:\@;">' . $memo_data['dist_memo_no'] . '</td>
								<td style="text-align:right;">' . sprintf("%01.2f", $sales_qty) . '</td>
								<td style="text-align:right;">' . sprintf("%01.2f", $memo_data['price']) . '</td>';


						$output .= '<td style="text-align:right;">' . $b_sales_qty . '</td>
							</tr>';
						$i++;
					}
					/*$output.= '<tr style="font-weight:bold;">
						<td colspan="4" style="text-align:right;">Sub Total :</td>
						<td style="text-align:right;">'.sprintf("%01.2f", $total_sales_qty).'</td>
						<td style="text-align:right;">'.sprintf("%01.2f", $total_price).'</td>
						<td style="text-align:right;">'.$total_bonus.'</td>
					</tr>';
					
					$g_sales_qty+=$total_sales_qty;
					$g_price+=$total_price;
					$g_bonus+=$total_bonus;*/
					if (@$b_results[$so_name][$product_name]) {
						foreach ($b_results[$so_name][$product_name] as $memo_data) {
							if (empty($memo_data)) {
								continue;
							}
							unset($b_results[$so_name][$product_name][$memo_data['dist_memo_no']]);
							$sales_qty = $memo_data['sales_qty'];
							$total_bonus += $sales_qty;
							$total_price += $memo_data['price'];

							$output .= '<tr>';
							$p_name = $i == 1 ? $product_name : '';
							$output .= '<td style="text-align:left;"><b>' . $p_name . '</b></td>';
							$output .= '<td style="text-align:left;">' . $memo_data['outlet_name'] . '-' . $memo_data['market_name'] . '-' . $memo_data['thana_name'] . '-' . $memo_data['district_name'] . '</td>
									<td>' . $memo_data['memo_date'] . '</td>
									<td style="mso-number-format:\@;">' . $memo_data['dist_memo_no'] . '</td>
									<td style="text-align:right;">' . sprintf("%01.2f", 0) . '</td>
									<td style="text-align:right;">' . sprintf("%01.2f", $memo_data['price']) . '</td>';


							$output .= '<td style="text-align:right;">' . $sales_qty . '</td>
								</tr>';
							$i++;
						}
						unset($b_results[$so_name][$product_name]);
					}
					$output .= '<tr style="font-weight:bold;">
						<td colspan="4" style="text-align:right;">Sub Total :</td>
						<td style="text-align:right;">' . sprintf("%01.2f", $total_sales_qty) . '</td>
						<td style="text-align:right;">' . sprintf("%01.2f", $total_price) . '</td>
						<td style="text-align:right;">' . $total_bonus . '</td>
					</tr>';

					$g_sales_qty += $total_sales_qty;
					$g_price += $total_price;
					$g_bonus += $total_bonus;
				}
				if (@$b_results[$so_name]) {
					foreach ($b_results[$so_name] as $product_name => $memo_datas) {
						if (empty($memo_datas)) {
							continue;
						}
						$i = 1;
						$total_sales_qty = 0;
						$total_price = 0;
						$total_bonus = 0;
						foreach ($memo_datas as $memo_data) {

							unset($b_results[$so_name][$product_name][$memo_data['dist_memo_no']]);


							$sales_qty = $memo_data['sales_qty'];
							$total_bonus += $sales_qty;
							$total_price += $memo_data['price'];

							$output .= '<tr>';
							$p_name = $i == 1 ? $product_name : '';
							$output .= '<td style="text-align:left;"><b>' . $p_name . '</b></td>';
							$output .= '<td style="text-align:left;">' . $memo_data['outlet_name'] . '-' . $memo_data['market_name'] . '-' . $memo_data['thana_name'] . '-' . $memo_data['district_name'] . '</td>
									<td>' . $memo_data['memo_date'] . '</td>
									<td style="mso-number-format:\@;">' . $memo_data['dist_memo_no'] . '</td>
									<td style="text-align:right;">' . sprintf("%01.2f", 0) . '</td>
									<td style="text-align:right;">' . sprintf("%01.2f", $memo_data['price']) . '</td>';


							$output .= '<td style="text-align:right;">' . $sales_qty . '</td>
								</tr>';
							$i++;
						}
						$output .= '<tr style="font-weight:bold;">
							<td colspan="4" style="text-align:right;">Sub Total :</td>
							<td style="text-align:right;">' . sprintf("%01.2f", $total_sales_qty) . '</td>
							<td style="text-align:right;">' . sprintf("%01.2f", $total_price) . '</td>
							<td style="text-align:right;">' . $total_bonus . '</td>
						</tr>';

						$g_sales_qty += $total_sales_qty;
						$g_price += $total_price;
						$g_bonus += $total_bonus;
					}
					unset($b_results[$so_name]);
				}
			}

			if ($b_results) {
				foreach ($b_results as $so_name => $product_datas) {
					if (empty($product_datas)) {
						continue;
					}
					$output .= '<tr><td style="text-align:left; font-weight:bold;" colspan="7">Sales Officer : ' . $so_name . '</td></tr>';

					foreach ($product_datas as $product_name => $memo_datas) {
						if (empty($memo_datas)) {
							continue;
						}
						$i = 1;
						$total_sales_qty = 0;
						$total_price = 0;
						$total_bonus = 0;

						foreach ($memo_datas as $memo_data) {
							unset($b_results[$so_name][$product_name][$memo_data['dist_memo_no']]);

							$b_sales_qty = $memo_data['sales_qty'];
							$total_bonus += $b_sales_qty;
							$total_price += $memo_data['price'];

							$output .= '<tr>';
							$p_name = $i == 1 ? $product_name : '';
							$output .= '<td style="text-align:left;"><b>' . $p_name . '</b></td>';
							$output .= '<td style="text-align:left;">' . $memo_data['outlet_name'] . '-' . $memo_data['market_name'] . '-' . $memo_data['thana_name'] . '-' . $memo_data['district_name'] . '</td>
									<td>' . $memo_data['memo_date'] . '</td>
									<td style="mso-number-format:\@;">' . $memo_data['dist_memo_no'] . '</td>
									<td style="text-align:right;">' . sprintf("%01.2f", 0) . '</td>
									<td style="text-align:right;">' . sprintf("%01.2f", $memo_data['price']) . '</td>';


							$output .= '<td style="text-align:right;">' . $b_sales_qty . '</td>
								</tr>';
							$i++;
						}


						$output .= '<tr style="font-weight:bold;">
							<td colspan="4" style="text-align:right;">Sub Total :</td>
							<td style="text-align:right;">' . sprintf("%01.2f", $total_sales_qty) . '</td>
							<td style="text-align:right;">' . sprintf("%01.2f", $total_price) . '</td>
							<td style="text-align:right;">' . $total_bonus . '</td>
						</tr>';

						$g_sales_qty += $total_sales_qty;
						$g_price += $total_price;
						$g_bonus += $total_bonus;
					}
				}
			}

			$output .= '<tr style="font-weight:bold;">
				<td colspan="4" style="text-align:right;">Grand Total :</td>
				<td style="text-align:right;">' . sprintf("%01.2f", $g_sales_qty) . '</td>
				<td style="text-align:right;">' . sprintf("%01.2f", $g_price) . '</td>
				<td style="text-align:right;">' . $g_bonus . '</td>
			</tr>';

			$this->set(compact('output'));
		}


		$this->set(compact('offices', 'territories', 'outlet_type', 'region_offices', 'office_id', 'request_data', 'so_list'));
	}
	public function get_db_list()
	{
		$this->loadModel('DistDistributor');
		$office_id = $this->request->data['office_id'];
		$date_from = date('Y-m-d', strtotime($this->request->data['date_from']));
		$date_to = date('Y-m-d', strtotime($this->request->data['date_to']));
		$dist_conditions = array('DistMemo.office_id' => $office_id, 'DistMemo.memo_date >=' => $date_from, 'DistMemo.memo_date <=' => $date_to);
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$data_array = array();
		if ($date_from && $office_id && $date_to) {

			$distDistributors = $this->DistDistributor->find('all', array(
				'conditions' => $dist_conditions,
				'joins' => array(array('table' => 'dist_memos', 'alias' => 'DistMemo', 'conditions' => 'DistMemo.distributor_id=DistDistributor.id')),
				'group' => array('DistDistributor.id', 'DistDistributor.name'),
				'fields' => array('DistDistributor.id', 'DistDistributor.name'),
				'order' => array('DistDistributor.name' => 'asc'),
			));
			$data_array = Set::extract($distDistributors, '{n}.DistDistributor');
		}
		if (!empty($distDistributors)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}
	public function get_sr_list()
	{
		$this->loadModel('DistSalesRepresentative');
		$office_id = $this->request->data['office_id'];
		$date_from = date('Y-m-d', strtotime($this->request->data['date_from']));
		$date_to = date('Y-m-d', strtotime($this->request->data['date_to']));
		$dist_conditions = array('DistMemo.office_id' => $office_id, 'DistMemo.memo_date >=' => $date_from, 'DistMemo.memo_date <=' => $date_to);
		$data_array = array();
		if ($date_from && $office_id && $date_to) {

			$distSalesrepresentative = $this->DistSalesRepresentative->find('list', array(
				'conditions' => $dist_conditions,
				'joins' => array(array('table' => 'dist_memos', 'alias' => 'DistMemo', 'conditions' => 'DistMemo.sr_id=DistSalesRepresentative.id')),
				'group' => array('DistSalesRepresentative.id', 'DistSalesRepresentative.name'),
				'fields' => array('DistSalesRepresentative.id', 'DistSalesRepresentative.name'),
				'order' => array('DistSalesRepresentative.name' => 'asc'),
			));
		}
		$output = '<option value="">---- All -----</option>';
		if (!empty($distSalesrepresentative)) {
			foreach ($distSalesrepresentative as $key => $name) {
				$output .= '<option value="' . $key . '">' . $name . '</option>';
			}

			echo $output;
		} else {
			echo $output;
		}
		$this->autoRender = false;
	}
}
