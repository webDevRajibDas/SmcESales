<?php
App::uses('AppController', 'Controller');
/**
 * ProjectionAchievementSettings Controller
 *
 * @property ProjectionAchievementReport $ProjectionAchievementReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class CreditCollectionReportsController extends AppController
{



	/**
	 * Components
	 *
	 * @var array
	 */

	public $uses = array('Product', 'ProductCategory', 'Memo', 'Office', 'Territory', 'Division', 'District', 'Thana', 'Market', 'OutletCategory', 'Outlet', 'TerritoryAssignHistory', 'SalesPerson', 'Brand', 'Collection');
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
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes


		$this->set('page_title', 'Credit Collection Report');
		$territories = array();
		$request_data = array();
		$report_type = array();
		$so_list = array();


		//types
		$types = array(
			'territory' => 'By Terriotry',
			'so' => 'By SO',
		);
		$this->set(compact('types'));

		$columns = array(
			'product' => 'By Product',
			'brand' => 'By Brand',
			'category' => 'By Category'
		);
		$this->set(compact('columns'));



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
			'is_active' => 1,
			'Product.product_type_id' => 1
		);

		$product_list = $this->Product->find('list', array(
			'conditions' => $conditions,
			'order' =>  array('order' => 'asc')
		));
		$this->set(compact('product_list'));


		//for outlet category list
		$outlet_categories = $this->OutletCategory->find('list', array(
			'conditions' => array('is_active' => 1),
			'order' => array('category_name' => 'DESC')
		));
		$this->set(compact('outlet_categories'));


		$lengths = array(
			'<' => '<',
			'>' => '>',
			'<=' => '<=',
			'>=' => '>=',
		);
		$this->set(compact('lengths'));


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
			$request_data = $this->request->data;

			$date_from = date('Y-m-d', strtotime($request_data['CreditCollectionReports']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['CreditCollectionReports']['date_to']));
			$this->set(compact('date_from', 'date_to'));

			$type = $this->request->data['CreditCollectionReports']['type'];
			$this->set(compact('type'));

			$region_office_id = isset($this->request->data['CreditCollectionReports']['region_office_id']) != '' ? $this->request->data['CreditCollectionReports']['region_office_id'] : $region_office_id;
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

			$office_id = isset($this->request->data['CreditCollectionReports']['office_id']) != '' ? $this->request->data['CreditCollectionReports']['office_id'] : $office_id;
			$this->set(compact('office_id'));

			$territory_id = isset($this->request->data['CreditCollectionReports']['territory_id']) != '' ? $this->request->data['CreditCollectionReports']['territory_id'] : 0;
			$this->set(compact('territory_id'));

			$so_id = isset($this->request->data['CreditCollectionReports']['so_id']) != '' ? $this->request->data['CreditCollectionReports']['so_id'] : 0;
			$this->set(compact('so_id'));


			$length = $this->request->data['CreditCollectionReports']['length'];
			$qty = $this->request->data['CreditCollectionReports']['qty'];


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
				$conditions['TerritoryAssignHistory.date BETWEEN ? and ? '] = array($date_from, $date_to);
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
				'Collection.collectionDate BETWEEN ? and ? ' => array($date_from, $date_to),
				'Collection.memo_date >' => '2018-10-01',
				'Collection.is_credit_collection >' => 0,
				//'Collection.status !=' => 0,
				//'Outlet.is_active' => 1,
				//'Market.is_active' => 1,
			);


			//if($office_ids)$conditions['Collection.office_id'] = $office_ids;
			//if($office_id)$conditions['Collection.office_id'] = $office_id;

			if ($office_ids) $conditions['Territory.office_id'] = $office_ids;
			if ($office_id) $conditions['Territory.office_id'] = $office_id;


			if ($type == 'so') {
				if ($so_id) $conditions['Collection.so_id'] = $so_id;
			} else {
				if ($territory_id) $conditions['Collection.territory_id'] = $territory_id;
			}


			/*$product_category_ids = isset($this->request->data['CreditCollectionReports']['product_category_id']) != '' ? $this->request->data['CreditCollectionReports']['product_category_id'] : 0;*/


			//pr($conditions);
			//exit;

			$q_results = $this->Collection->find('all', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'alias' => 'InstrumentType',
						'table' => 'instrument_type',
						'type' => 'INNER',
						'conditions' => 'Collection.instrument_type = InstrumentType.id'
					),
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'Collection.territory_id = Territory.id'
					),
					array(
						'alias' => 'SalesPeople',
						'table' => 'sales_people',
						'type' => 'INNER',
						'conditions' => 'Collection.so_id = SalesPeople.id'
					),
					array(
						'alias' => 'Outlet',
						'table' => 'outlets',
						'type' => 'INNER',
						'conditions' => 'Collection.outlet_id = Outlet.id'
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
					)
				),

				'group' => array('Collection.memo_no', 'Territory.office_id', 'Collection.so_id', 'Collection.memo_date', 'Collection.id', 'Collection.collectionDate', 'Collection.collectionAmount', 'InstrumentType.name', 'SalesPeople.name', 'Collection.outlet_id', 'Outlet.id', 'Outlet.name', 'Collection.territory_id', 'Territory.name', 'Thana.id', 'Thana.name', 'Market.id', 'Market.name'),

				'fields' => array('Collection.memo_no', 'Collection.id', 'Territory.office_id', 'Collection.so_id', 'Collection.memo_date', 'Collection.collectionDate', 'Collection.collectionAmount', 'InstrumentType.name', 'SalesPeople.name', 'Collection.outlet_id', 'Outlet.id', 'Outlet.name', 'Collection.territory_id', 'Territory.name', 'Thana.id', 'Thana.name', 'Market.id', 'Market.name'),

				'order' => array('Collection.collectionDate asc', 'Collection.memo_no asc', 'Collection.outlet_id asc'),

				//'order' => $order,

				'recursive' => -1,
				//'limit' => 200
			));


			// pr($q_results);
			// exit;


			$results = array();
			foreach ($q_results as $result) {
				$date1 = date_create($result['Collection']['memo_date']);
				$date2 = date_create($result['Collection']['collectionDate']);
				$diff = date_diff($date1, $date2);

				$days = $diff->format("%a");
				//$days = 20;

				if ($length == '<' && $days < $qty) {
					$results[$result['SalesPeople']['name']][$result['Collection']['memo_no']][$result['Collection']['id']] =
						array(
							'territory_name' 		=> $result['Territory']['name'],
							'outlet_name' 			=> $result['Outlet']['name'],
							'market_name' 			=> $result['Market']['name'],
							'thana_name' 			=> $result['Thana']['name'],
							'instrument_type' 		=> $result['InstrumentType']['name'],

							'memo_no' 				=> $result['Collection']['memo_no'],
							'sales_date' 			=> $result['Collection']['memo_date'],
							'collection_date' 		=> $result['Collection']['collectionDate'],
							'amount' 				=> sprintf("%01.2f", $result['Collection']['collectionAmount']),
							'no_of_days' 			=> $days . ' days'
						);
				} elseif ($length == '>' && $days > $qty) {
					$results[$result['SalesPeople']['name']][$result['Collection']['memo_no']][$result['Collection']['id']] =
						array(
							'territory_name' 		=> $result['Territory']['name'],
							'outlet_name' 			=> $result['Outlet']['name'],
							'market_name' 			=> $result['Market']['name'],
							'thana_name' 			=> $result['Thana']['name'],
							'instrument_type' 		=> $result['InstrumentType']['name'],

							'memo_no' 				=> $result['Collection']['memo_no'],
							'sales_date' 			=> $result['Collection']['memo_date'],
							'collection_date' 		=> $result['Collection']['collectionDate'],
							'amount' 				=> sprintf("%01.2f", $result['Collection']['collectionAmount']),
							'no_of_days' 			=> $days . ' days'
						);
				} elseif ($length == '<=' && $days <= $qty) {
					$results[$result['SalesPeople']['name']][$result['Collection']['memo_no']][$result['Collection']['id']] =
						array(
							'territory_name' 		=> $result['Territory']['name'],
							'outlet_name' 			=> $result['Outlet']['name'],
							'market_name' 			=> $result['Market']['name'],
							'thana_name' 			=> $result['Thana']['name'],
							'instrument_type' 		=> $result['InstrumentType']['name'],

							'memo_no' 				=> $result['Collection']['memo_no'],
							'sales_date' 			=> $result['Collection']['memo_date'],
							'collection_date' 		=> $result['Collection']['collectionDate'],
							'amount' 				=> sprintf("%01.2f", $result['Collection']['collectionAmount']),
							'no_of_days' 			=> $days . ' days'
						);
				} elseif ($length == '>=' && $days >= $qty) {
					$results[$result['SalesPeople']['name']][$result['Collection']['memo_no']][$result['Collection']['id']] =
						array(
							'territory_name' 		=> $result['Territory']['name'],
							'outlet_name' 			=> $result['Outlet']['name'],
							'market_name' 			=> $result['Market']['name'],
							'thana_name' 			=> $result['Thana']['name'],
							'instrument_type' 		=> $result['InstrumentType']['name'],

							'memo_no' 				=> $result['Collection']['memo_no'],
							'sales_date' 			=> $result['Collection']['memo_date'],
							'collection_date' 		=> $result['Collection']['collectionDate'],
							'amount' 				=> sprintf("%01.2f", $result['Collection']['collectionAmount']),
							'no_of_days' 			=> $days . ' days'
						);
				}
				/*else
				{
					$results[$result['SalesPeople']['name']][$result['Collection']['memo_no']] = 
					array(
						'territory_name' 		=> $result['Territory']['name'],
						'outlet_name' 			=> $result['Outlet']['name'],				
						'market_name' 			=> $result['Market']['name'],
						'thana_name' 			=> $result['Thana']['name'],
						'instrument_type' 		=> $result['InstrumentType']['name'],
						
						'memo_no' 				=> $result['Collection']['memo_no'],
						'sales_date' 			=> $result['Collection']['memo_date'],
						'collection_date' 		=> $result['Collection']['collectionDate'],
						'amount' 				=> sprintf("%01.2f",$result['Collection']['collectionAmount']),
						'no_of_days' 			=> $days.' days'
					);
				}*/
			}

			//pr($results);
			//exit;

			$this->set(compact('results'));
		}


		$this->set(compact('offices', 'territories', 'outlet_type', 'region_offices', 'office_id', 'request_data', 'so_list'));
	}
}
