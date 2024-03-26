<?php
App::uses('AppController', 'Controller');
/**
 * DcrSettings Controller
 *
 * @property DcrReport $DcrReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
 //Configure::write('debug', 2);

class DcrReportsController extends AppController
{



	/**
	 * Components
	 *
	 * @var array
	 */

	public $uses = array('Memo', 'MemoDetail', 'Office', 'Territory', 'OutletCategory', 'Thana', 'Market', 'Outlet', 'TerritoryAssignHistory', 'SalesPerson', 'Product');
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

		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0); //300 seconds = 5 minutes

		$this->set('page_title', "Sales Officer's Daily Call Report");

		$territories = array();
		$request_data = array();
		$types = array();
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
		$report_types = array(
			'visit_info' => 'Market Visit Information',
			'non_visited' => 'Non-Visited Market List',
			'visited' => 'Visited Market List',

		);
		$this->set(compact('report_types'));



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

		if ($this->request->is('post') || $this->request->is('put')) {

			//products
			$product_list = $this->Product->find('list', array(
				'conditions' => array(
					'NOT' => array('Product.product_category_id' => 32),
					'is_active' => 1,
					'Product.product_type_id' => 1
				),
				'order' =>  array('order' => 'asc')
			));
			$this->set(compact('product_list'));

			//for outlet category list
			$outlet_categories = $this->OutletCategory->find('list', array(
				'conditions' => array('is_active' => 1),
				'order' => array('category_name' => 'asc')
			));
			$this->set(compact('outlet_categories'));


			$request_data = $this->request->data;

			$date_from = date('Y-m-d', strtotime($request_data['DcrReports']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['DcrReports']['date_to']));
			$this->set(compact('date_from', 'date_to'));

			$type = $this->request->data['DcrReports']['type'];
			$this->set(compact('type'));

			$region_office_id = isset($this->request->data['DcrReports']['region_office_id']) != '' ? $this->request->data['DcrReports']['region_office_id'] : $region_office_id;
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

			$office_id = isset($this->request->data['DcrReports']['office_id']) != '' ? $this->request->data['DcrReports']['office_id'] : $office_id;
			$this->set(compact('office_id'));

			$territory_id = isset($this->request->data['DcrReports']['territory_id']) != '' ? $this->request->data['DcrReports']['territory_id'] : 0;
			$this->set(compact('territory_id'));

			$so_id = isset($this->request->data['DcrReports']['so_id']) != '' ? $this->request->data['DcrReports']['so_id'] : 0;
			$this->set(compact('so_id'));

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
			/*$so_list_r = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id','SalesPerson.name', 'Territory.name'),
				'conditions' => array(
					'SalesPerson.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					'User.user_group_id' => 4,
				),
				'recursive'=> 0
			));	*/
			//NEW SO LIST GENERATE FROM MEMO TABLE
			$so_list_r = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
				'joins' => array(
					array(
						'table' => 'memos',
						'alias' => 'Memo',
						'type' => 'INNER',
						'conditions' => 'SalesPerson.id=Memo.sales_person_id',
					),
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'Memo.territory_id = Territory.id'
					),
				),
				'conditions' => array(
					'Memo.memo_date BETWEEN ? and ?' => array($date_from, $date_to),
					'Memo.office_id' => $office_id
				),
				'recursive' => -1
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




			//For Query Conditon
			$conditions = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),

				//'Memo.id' => 8500816,
				'Memo.gross_value >' => 0,
				'Memo.status !=' => 0,
				'MemoDetail.price >' => 0,

				//'Memo.memo_date BETWEEN ? and ? ' => array('2018-03-03', '2018-04-23'),
				//'Memo.outlet_id' => array(247268, 255743),
			);


			if ($office_ids) $conditions['Memo.office_id'] = $office_ids;
			if ($office_id) $conditions['Memo.office_id'] = $office_id;

			if ($type == 'so') {
				if ($so_id) $conditions['Memo.sales_person_id'] = $so_id;
			} else {
				if ($territory_id) $conditions['Memo.territory_id'] = $territory_id;
			}

			/*pr($conditions);
			exit;*/

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
						'conditions' => 'MemoDetail.product_id = Product.id'
					),
					array(
						'alias' => 'Collection',
						'table' => 'collections',
						'type' => 'LEFT',
						'conditions' => 'Collection.memo_id = Memo.id AND Collection.is_credit_collection = 1'
					),
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'Memo.territory_id = Territory.id'
					),
					array(
						'alias' => 'SalesPeople',
						'table' => 'sales_people',
						'type' => 'INNER',
						'conditions' => 'Memo.sales_person_id = SalesPeople.id'
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
						'conditions' => 'Market.thana_id = Thana.id'
					),
					array(
						'alias' => 'District',
						'table' => 'districts',
						'type' => 'INNER',
						'conditions' => 'Thana.district_id = District.id'
					)
				),

				/*'fields' => array('SUM(MemoDetail.sales_qty) as sales_qty', 'SUM(MemoDetail.sales_qty*MemoDetail.price) as price', 'SUM(Memo.cash_recieved) as memo_cash_recieved', 'SUM(Memo.credit_amount) as memo_credit_amount', 'SUM(Memo.gross_value) as memo_gross_value', 'MemoDetail.product_id',  'Product.name', 'Outlet.id', 'Outlet.name', 'Outlet.category_id', 'Market.id', 'Market.name', 'Thana.id', 'Thana.name', 'District.id', 'District.name'),
				
				'group' => array('MemoDetail.product_id',  'Product.name', 'Outlet.id', 'Outlet.category_id', 'Outlet.name', 'Market.id', 'Market.name', 'Thana.id', 'Thana.name', 'District.id', 'District.name'),*/


				'fields' => array(
					'SUM(MemoDetail.sales_qty) as sales_qty', 
					'SUM(MemoDetail.sales_qty*MemoDetail.price) as price', 
					'COUNT(MemoDetail.product_id) as product_count', 
					'sum(MemoDetail.sales_qty * (CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END)) AS discount_value',
					'Memo.cash_recieved', 
					'Memo.credit_amount', 
					'Memo.total_discount', 
					'Memo.gross_value', 
					'Memo.id', 
					'MemoDetail.product_id',  
					'Product.name', 
					'Memo.outlet_id', 
					'Outlet.id', 
					'Outlet.name', 
					'Outlet.category_id', 
					'Market.id', 
					'Market.name', 
					'Thana.id', 
					'Thana.name', 
					'District.id', 'District.name', 'SUM(Collection.collectionAmount) as creadit_collection'
					
					),

				'group' => array('Memo.id', 'Memo.cash_recieved', 'Memo.credit_amount', 'Memo.gross_value', 'Memo.total_discount', 'MemoDetail.product_id',  'Product.name', 'Memo.outlet_id', 'Outlet.id', 'Outlet.category_id', 'Outlet.name', 'Market.id', 'Market.name', 'Thana.id', 'Thana.name', 'District.id', 'District.name'),

				'order' => array('Market.name asc', 'Outlet.name asc'),
				'recursive' => -1,
				//'limit' => 15
			));
			
			//echo $this->Memo->getLastquery();
			
			

			//pr($q_results);
			//exit;

			$results = array();
			/*foreach($q_results as $result){
				$results[$result['Market']['name'].'-'.$result['Thana']['name'].'-'.$result['District']['name']][$result['Outlet']['name']][$result['MemoDetail']['product_id']] = 
				array(
					
					'product_id' 			=> $result['MemoDetail']['product_id'],
					'sales_qty' 			=> $result[0]['sales_qty'],
					'price' 				=> $result[0]['price'],
					'memo_cash_recieved' 	=> $result[0]['memo_cash_recieved'],
					'memo_credit_amount' 	=> $result[0]['memo_credit_amount'],
					'memo_gross_value' 		=> $result[0]['memo_gross_value'],
					
					'outlet_category_id' 	=> $result['Outlet']['category_id'],
					'outlet_id' 			=> $result['Outlet']['id'],
					//'market_name' 		=> $result['Market']['name'],
					//'thana_name' 			=> $result['Thana']['name'],
					//'district_name' 		=> $result['District']['name'],
				);
			}*/

			$memo_ids = array();
			
			//echo '<pre>';print_r( $q_results );exit;

			foreach ($q_results as $result) {
				
				if( $result[0]['product_count'] > 1 ){
					$result[0]['discount_value'] = ($result[0]['discount_value']/$result[0]['product_count']);
				}

				$results[$result['Market']['name'] . '-' . $result['Thana']['name'] . '-' . $result['District']['name']][$result['Outlet']['name']][$result['Memo']['id']]['memo_detial'][$result['MemoDetail']['product_id']] =
					array(
						//'memo_id' 				=> $result['Memo']['id'],
						'product_id' 			=> $result['MemoDetail']['product_id'],
						//'outlet_category_id' 	=> $result['Outlet']['category_id'],
						'sales_qty' 			=> $result[0]['sales_qty'],
						'price' 				=> $result[0]['price'],
						'discount_value' 				=> $result[0]['discount_value'],
						//'outlet_name' 		=> $result['Outlet']['name'],
						//'market_name' 		=> $result['Market']['name'],
						//'thana_name' 			=> $result['Thana']['name'],
						//'district_name' 		=> $result['District']['name'],
					);

				$results[$result['Market']['name'] . '-' . $result['Thana']['name'] . '-' . $result['District']['name']][$result['Outlet']['name']][$result['Memo']['id']]['memo'] =
					array(
						'memo_id' 				=> $result['Memo']['id'],
						'cash_recieved' 		=> $result['Memo']['cash_recieved'],
						'credit_amount' 		=> $result['Memo']['credit_amount'],
						'gross_value' 			=> $result['Memo']['gross_value'],
						'total_discount' 			=> $result['Memo']['total_discount'],
						'outlet_category_name' 	=> $outlet_categories[$result['Outlet']['category_id']],
						'outlet_id' 			=> $result['Memo']['outlet_id'],
						'market_id' 			=> $result['Market']['id'],
						'creadit_collection' 	=> $result[0]['creadit_collection']
					);

				array_push($memo_ids, $result['Memo']['id']);
			}
			
			//echo '<pre>';print_r( $results );exit;
			
			

			$this->set(compact('results'));














			//For Stokist Price
			$conditions = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Memo.gross_value >' => 0,
				'Memo.status !=' => 0,
				'MemoDetail.price >' => 0,
				'Memo.is_distributor' => 0
			);

			if ($office_ids) $conditions['Memo.office_id'] = $office_ids;
			if ($office_id) $conditions['Memo.office_id'] = $office_id;

			if ($type == 'so') {
				if ($so_id) $conditions['Memo.sales_person_id'] = $so_id;
			} else {
				if ($territory_id) $conditions['Memo.territory_id'] = $territory_id;
			}

			//$conditions['ProductCombinations.min_qty >'] = 1;
			$conditions['OR'] = array(
				array('ProductCombinationsV2.min_qty  !=' => 1),
				array('ProductCombinationsV2.min_qty is null'),
			);
			//$conditions['(ProductCombinationsV2.min_qty !=1 or ProductCombinationsV2.min_qty is null)'] = '';

			$s_q_results = $this->Memo->find('all', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'alias' => 'MemoDetail',
						'table' => 'memo_details',
						'type' => 'INNER',
						'conditions' => 'Memo.id = MemoDetail.memo_id'
					),
					array(
						'alias' => 'ProductCombinationsV2',
						'table' => 'product_combinations_v2',
						'type' => 'LEFT',
						'conditions' => 'ProductCombinationsV2.id = MemoDetail.product_price_id'
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
					)
				),

				'fields' => array('SUM(MemoDetail.sales_qty) as sales_qty', 'SUM(MemoDetail.sales_qty*MemoDetail.price) as price', 'MemoDetail.product_id', 'Product.sales_measurement_unit_id', 'Product.order'),

				'group' => array('MemoDetail.product_id', 'Product.sales_measurement_unit_id', 'Product.order'),

				'order' => array('Product.order asc'),

				//'order' => $order,

				'recursive' => -1,
				//'limit' => 200
			));
			       


			//pr($s_q_results);
			//exit;

			$stockist_results = array();

			foreach ($s_q_results as $s_q_result) {
				$stockist_results[$s_q_result['MemoDetail']['product_id']] = array(
					'sales_qty' => $s_q_result[0]['sales_qty'],
				);
			}
			
			

			//For DB Price
			$conditions = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Memo.gross_value >' => 0,
				'Memo.status !=' => 0,
				'MemoDetail.price >' => 0,
				'Memo.is_distributor' => 1
			);

			if ($office_ids) $conditions['Memo.office_id'] = $office_ids;
			if ($office_id) $conditions['Memo.office_id'] = $office_id;

			if ($type == 'so') {
				if ($so_id) $conditions['Memo.sales_person_id'] = $so_id;
			} else {
				if ($territory_id) $conditions['Memo.territory_id'] = $territory_id;
			}

			/*$conditions['ProductCombinations.min_qty >'] = 1;*/

			$s_q_results = $this->Memo->find('all', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'alias' => 'MemoDetail',
						'table' => 'memo_details',
						'type' => 'INNER',
						'conditions' => 'Memo.id = MemoDetail.memo_id'
					),
					array(
						'alias' => 'ProductCombinations',
						'table' => 'dist_product_combinations',
						'type' => 'INNER',
						'conditions' => 'ProductCombinations.id = MemoDetail.product_price_id'
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
					)
				),

				'fields' => array('SUM(MemoDetail.sales_qty) as sales_qty', 'SUM(MemoDetail.sales_qty*MemoDetail.price) as price', 'MemoDetail.product_id', 'Product.sales_measurement_unit_id', 'Product.order'),

				'group' => array('MemoDetail.product_id', 'Product.sales_measurement_unit_id', 'Product.order'),

				'order' => array('Product.order asc'),

				//'order' => $order,

				'recursive' => -1,
				//'limit' => 200
			));
			
			

			foreach ($s_q_results as $s_q_result) {
				$stockist_results[$s_q_result['MemoDetail']['product_id']]['sales_qty'] = (isset($stockist_results[$s_q_result['MemoDetail']['product_id']]['sales_qty']) ? $stockist_results[$s_q_result['MemoDetail']['product_id']]['sales_qty'] : 0) + $s_q_result[0]['sales_qty'];
			}

			//For DB Price
			$conditions = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Memo.gross_value >' => 0,
				'Memo.status !=' => 0,
				'MemoDetail.price >' => 0,
				'Memo.is_distributor' => 1
			);

			if ($office_ids) $conditions['Memo.office_id'] = $office_ids;
			if ($office_id) $conditions['Memo.office_id'] = $office_id;

			if ($type == 'so') {
				if ($so_id) $conditions['Memo.sales_person_id'] = $so_id;
			} else {
				if ($territory_id) $conditions['Memo.territory_id'] = $territory_id;
			}

			/*$conditions['ProductCombinations.min_qty >'] = 1;*/

			$s_q_results = $this->Memo->find('all', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'alias' => 'MemoDetail',
						'table' => 'memo_details',
						'type' => 'INNER',
						'conditions' => 'Memo.id = MemoDetail.memo_id'
					),
					array(
						'alias' => 'ProductCombinations',
						'table' => 'special_product_combinations',
						'type' => 'INNER',
						'conditions' => 'ProductCombinations.id = MemoDetail.product_price_id'
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
					)
				),

				'fields' => array('SUM(MemoDetail.sales_qty) as sales_qty', 'SUM(MemoDetail.sales_qty*MemoDetail.price) as price', 'MemoDetail.product_id', 'Product.sales_measurement_unit_id', 'Product.order'),

				'group' => array('MemoDetail.product_id', 'Product.sales_measurement_unit_id', 'Product.order'),

				'order' => array('Product.order asc'),

				//'order' => $order,

				'recursive' => -1,
				//'limit' => 200
			));


			foreach ($s_q_results as $s_q_result) {
				$stockist_results[$s_q_result['MemoDetail']['product_id']]['sales_qty'] = (isset($stockist_results[$s_q_result['MemoDetail']['product_id']]['sales_qty']) ? $stockist_results[$s_q_result['MemoDetail']['product_id']]['sales_qty'] : 0) + $s_q_result[0]['sales_qty'];
			}
			

			$this->set(compact('stockist_results'));



			//For Retailer Price
			$conditions = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Memo.gross_value >' => 0,
				'Memo.status !=' => 0,
				'MemoDetail.price >' => 0
			);

			if ($office_ids) $conditions['Memo.office_id'] = $office_ids;
			if ($office_id) $conditions['Memo.office_id'] = $office_id;

			if ($type == 'so') {
				if ($so_id) $conditions['Memo.sales_person_id'] = $so_id;
			} else {
				if ($territory_id) $conditions['Memo.territory_id'] = $territory_id;
			}

			//$conditions['ProductCombinations.min_qty'] = 1;
			$conditions['ProductCombinationsV2.min_qty'] = 1;

			$r_q_results = $this->Memo->find('all', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'alias' => 'MemoDetail',
						'table' => 'memo_details',
						'type' => 'INNER',
						'conditions' => 'Memo.id = MemoDetail.memo_id'
					),
					array(
						'alias' => 'ProductCombinationsV2',
						'table' => 'product_combinations_v2',
						'type' => 'LEFT',
						'conditions' => 'ProductCombinationsV2.id = MemoDetail.product_price_id'
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
					)
				),

				'fields' => array('SUM(MemoDetail.sales_qty) as sales_qty', 'SUM(MemoDetail.sales_qty*MemoDetail.price) as price', 'MemoDetail.product_id', 'Product.sales_measurement_unit_id', 'Product.order', 'ProductCombinationsV2.min_qty'),

				'group' => array('MemoDetail.product_id', 'Product.sales_measurement_unit_id', 'Product.order', 'ProductCombinationsV2.min_qty'),

				'order' => array('Product.order asc'),

				//'order' => $order,

				'recursive' => -1,
				//'limit' => 200
			));

			//pr($r_q_results);
			//exit;

			$retailer_results = array();

			foreach ($r_q_results as $r_q_results) {
				$retailer_results[$r_q_results['MemoDetail']['product_id']] = array(
					'sales_qty' => $r_q_results[0]['sales_qty'],
				);
			}
			
			//echo '<pre>sales';print_r($results);
		//echo '<pre>stock';print_r($stockist_results);
		//echo '<pre>retilarestock';print_r($retailer_results);exit;
			
			
			$this->set(compact('retailer_results'));



			//for bonus
			$bonus_conditions = array(
				'MemoDetail.memo_id' => $memo_ids,
				'MemoDetail.price <' => 1,
			);
			$bonus_q_results = $this->MemoDetail->find('all', array(
				'conditions' => $bonus_conditions,

				'fields' => array('SUM(MemoDetail.sales_qty) as sales_qty', 'SUM(MemoDetail.sales_qty*MemoDetail.price) as price', 'MemoDetail.product_id'),

				'group' => array('MemoDetail.product_id'),

				//'order' => array('Market.name asc', 'Outlet.name asc'),
				'recursive' => -1,
				//'limit' => 15
			));

			//pr($bonus_q_results);

			$bonus_results = array();

			foreach ($bonus_q_results as $bonus_q_result) {
				$bonus_results[$bonus_q_result['MemoDetail']['product_id']] = array(
					'sales_qty' => $bonus_q_result[0]['sales_qty'],
				);
			}
			$this->set(compact('bonus_results'));



			//OC results
			$oc_conditions = array();
			$oc_conditions = array('Memo.id' => $memo_ids);
			$oc_q_results = $this->Memo->find('all', array(
				'conditions' => $oc_conditions,
				'joins' => array(
					array(
						'alias' => 'Outlet',
						'table' => 'outlets',
						'type' => 'INNER',
						'conditions' => 'Memo.outlet_id = Outlet.id'
					)
				),
				'fields' => array('Outlet.category_id', 'COUNT(DISTINCT Memo.outlet_id) as oc'),
				'group' => array('Outlet.category_id'),
				'recursive' => -1,
			));

			$oc_results = array();

			foreach ($oc_q_results as $oc_q_result) {
				$oc_results[$oc_q_result['Outlet']['category_id']] = array(
					'oc' => $oc_q_result[0]['oc'],
				);
			}
			$this->set(compact('oc_results'));


			//EC results
			$ec_conditions = array();
			$ec_conditions = array('Memo.id' => $memo_ids);
			$ec_q_results = $this->Memo->find('all', array(
				'conditions' => $ec_conditions,
				'joins' => array(
					array(
						'alias' => 'Outlet',
						'table' => 'outlets',
						'type' => 'INNER',
						'conditions' => 'Memo.outlet_id = Outlet.id'
					)
				),
				'fields' => array('Outlet.category_id', 'COUNT(Memo.outlet_id) as ec'),
				'group' => array('Outlet.category_id'),
				'recursive' => -1,
			));

			$ec_results = array();

			foreach ($ec_q_results as $ec_q_result) {
				$ec_results[$ec_q_result['Outlet']['category_id']] = array(
					'ec' => $ec_q_result[0]['ec'],
				);
			}
			$this->set(compact('ec_results'));

			//pr($ec_results);
			//exit;



		}

		$this->set(compact('offices', 'territories', 'region_offices', 'office_id', 'request_data', 'so_list'));
	}
}
