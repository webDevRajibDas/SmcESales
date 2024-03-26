<?php
App::uses('AppController', 'Controller');

/**
 * Memos Controller
 *
 * @property Memo $Memo
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class MemosController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $uses = array(
		'Memo', 'Thana', 'Market', 'SalesPerson', 'Outlet', 'Product', 'MemoProgramOfficer',
		'ProductPrice', 'ProductCombination', 'Combination', 'DistProductPrice', 'DistProductCombination',
		'DistCombination', 'MemoDetail', 'MeasurementUnit', 'User'
	);
	public $components = array('Paginator', 'Session', 'Filter.Filter');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index()
	{
	
		$product_name = $this->Product->find('all', array(
			'fields' => array('Product.name', 'Product.id', 'MU.name as mes_name', 'Product.product_category_id'),
			'joins' => array(
				array(
					'table' => 'measurement_units',
					'alias' => 'MU',
					'type' => 'LEFT',
					'conditions' => array('MU.id= Product.sales_measurement_unit_id')
				)
			),
			'conditions' => array('NOT' => array('Product.product_category_id' => 32)),
			'order' => 'Product.product_category_id',
			'recursive' => -1
		));
		//pr($product_name); exit();
		$requested_data = $this->request->data;
		 
		$this->set('page_title', 'Memo List');
		$this->Session->delete('cart_session_data');
		$this->Session->delete('matched_session_data');
		$this->Session->delete('combintaion_qty_data');

		$office_parent_id = $this->UserAuth->getOfficeParentId();
		$this->set('office_parent_id', $office_parent_id);
		if ($office_parent_id == 0) {
			$conditions[] = array('Memo.memo_date >=' => $this->current_date() . ' 00:00:00');
			$conditions[] = array('Memo.memo_date <=' => $this->current_date() . ' 23:59:59');
			$office_conditions = array(
				'office_type_id' => 2,
				"NOT" => array("id" => array(30, 31, 37))
			);
		} else {
			$conditions[] = array('Territory.office_id' => $this->UserAuth->getOfficeId());
			$conditions[] = array('Memo.memo_date >=' => $this->current_date() . ' 00:00:00');
			$conditions[] = array('Memo.memo_date <=' => $this->current_date() . ' 23:59:59');
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}

		$group = array('Memo.id', 'Memo.created_by', 'Memo.memo_no', 'Memo.memo_date', 'Memo.from_app', 'Memo.memo_reference_no', 'Memo.gross_value', 'Memo.memo_time', 'Memo.credit_amount', 'Memo.memo_editable', 'Memo.is_distributor', 'Memo.status', 'Memo.action', 'Outlet.name', 'Market.name', 'Territory.name', 'Memo.outlet_id', 'Memo.territory_id', 'Memo.market_id');
		if (isset($requested_data['Memo']['payment_status'])) {
			if ($requested_data['Memo']['payment_status'] == 1) {
				$group = array(
					'Memo.id', 'Memo.created_by', 'Memo.memo_no', 'Memo.memo_date',  'Memo.from_app', 'Memo.memo_reference_no', 'Memo.gross_value', 'Memo.memo_time', 'Memo.credit_amount', 'Memo.memo_editable', 'Memo.is_distributor', 'Memo.status', 'Memo.action', 'Outlet.name', 'Market.name', 'Territory.name', 'Memo.outlet_id', 'Memo.territory_id', 'Memo.market_id 
					HAVING MAX(Collection.collectionAmount) is null OR MAX(Collection.collectionAmount) < Memo.gross_value'
				);
			} elseif ($requested_data['Memo']['payment_status'] == 2) {
				$group = array('Memo.id', 'Memo.created_by', 'Memo.memo_no', 'Memo.memo_date',  'Memo.from_app', 'Memo.memo_reference_no', 'Memo.gross_value', 'Memo.memo_time', 'Memo.credit_amount', 'Memo.memo_editable', 'Memo.is_distributor', 'Memo.status', 'Memo.action', 'Outlet.name', 'Market.name', 'Territory.name', 'Memo.outlet_id', 'Memo.territory_id', 'Memo.market_id 
					HAVING MAX(Collection.collectionAmount) is not null AND MAX(Collection.collectionAmount) = Memo.gross_value');
			}
		}


		if (isset($requested_data['Memo']['operator_product_count'])) {
			$operator_memo_count_conditions = '';
			if ($requested_data['Memo']['operator_product_count'] == 3) {
				$operator_memo_count_conditions .= "count(case when MemoDetail.price >0 then MemoDetail.id end) BETWEEN '" . $requested_data['Memo']['memo_product_count_from'] . "' AND '" . $requested_data['Memo']['memo_product_count_to'] . "'";
			} elseif ($requested_data['Memo']['operator_product_count'] == 1) {
				$operator_memo_count_conditions .= "count(case when MemoDetail.price >0 then MemoDetail.id end)  < '" . $requested_data['Memo']['memo_product_count'] . "'";
			} elseif ($requested_data['Memo']['operator_product_count'] == 2) {
				$operator_memo_count_conditions .= "count(case when MemoDetail.price >0 then MemoDetail.id end)  > '" . $requested_data['Memo']['memo_product_count'] . "'";
			}
			if (strpos($group[16], 'HAVING')) {
				$group[16] .= ' AND ' . $operator_memo_count_conditions;
			} else {
				$group[16] .= ' HAVING ' . $operator_memo_count_conditions;
			}
		}

		if ($requested_data) {
			$conditions_collections = $this->conditions_making_for_collection_subquery($requested_data);

			$conditions_collection_sql = $this->condition_break_as_text($conditions_collections);
		} else
			$conditions_collection_sql = $this->condition_break_as_text($conditions);
		
		
		$usergroupid = $this->UserAuth->getUserGroupId();

		//pr($usergroupid);exit();
		
		if( $usergroupid == 1016 ){
			$conditions[] = array('Memo.created_by'=>$this->UserAuth->getUserId());
			//$conditions[] = array('Memo.is_program'=>1);
		}

		$this->Memo->unbindModel(array('belongsTo' => array('Territory')), false);
		$this->Memo->recursive = 0;
		$this->paginate = array(
			'fields' => array(
				'Memo.id',
				'Memo.memo_no',
				'Memo.from_app',
				'Memo.memo_reference_no',
				'Memo.gross_value',
				'Memo.memo_date',
				'Memo.memo_time',
				'Memo.credit_amount',
				'Memo.status',
				'Memo.memo_editable',
				'Memo.action',
				'Memo.is_distributor',
				'Outlet.name',
				'Market.name',
				'Territory.name',
				'Memo.outlet_id',
				'Memo.territory_id',
				'Memo.created_by',
				'Memo.market_id',
				'MAX(Collection.collectionAmount) as payment_status'
				/*'CASE
					WHEN SUM(Collection.collectionAmount) is null THEN 1
					WHEN SUM(Collection.collectionAmount) < Memo.gross_value THEN 1
					ELSE 2 END as payment_status'*/
			),
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'territories',
					'alias' => 'Territory',
					'conditions' => 'Territory.id=CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end',
					'type' => 'Left'
				),
				array(
					'table' => '(select cl.memo_id,sum(cl.collectionAmount) as collectionAmount from collections cl 
					inner join memos Memo on Memo.id=cl.memo_id inner join territories Territory on Territory.id=CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end where ' . $conditions_collection_sql . '   group by cl.memo_id)',
					'alias' => 'Collection',
					'conditions' => 'Collection.memo_id=Memo.id',
					'type' => 'left'
				),
				array(
					'table' => 'memo_details',
					'alias' => 'MemoDetail',
					'conditions' => 'MemoDetail.memo_id=Memo.id',
					'type' => 'Left'
				)
			),
			'group' => $group,
			'order' => array('Memo.id' => 'desc'),
			'limit' => 100
		);

		/*  pr($this->paginate());
		exit; */
		$this->set('memos', $this->paginate());
		$this->set('office_id', $this->UserAuth->getOfficeId());
		$this->loadModel('Office');
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		$office_id = isset($this->request->data['Memo']['office_id']) != '' ? $this->request->data['Memo']['office_id'] : 0;
		$territory_id = isset($this->request->data['Memo']['territory_id']) != '' ? $this->request->data['Memo']['territory_id'] : 0;
		$market_id = isset($this->request->data['Memo']['market_id']) != '' ? $this->request->data['Memo']['market_id'] : 0;

		/*** Show Except Parent(Who has Child) Territory ***/
		$this->loadModel('Territory');
		$child_territory_parent_id = $this->Territory->find('list', array(
			'conditions' => array(
				'parent_id !=' => 0,
				'office_id' => $office_id
			),
			'fields' => array('Territory.parent_id', 'Territory.name'),

		));

		$territory = $this->Territory->find('all', array(
			'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
			'conditions' => array('Territory.office_id' => $office_id, 'NOT' => array('Territory.id' => array_keys($child_territory_parent_id))),
			'order' => array('Territory.name' => 'asc'),
			'recursive' => 0
		));


		$data_array = array();

		foreach ($territory as $key => $value) {
			$t_id = $value['Territory']['id'];
			$t_val = $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')';
			$data_array[$t_id] = $t_val;
		}

		$territories = $data_array;

		/*
		$territories = $this->Memo->Territory->find('list', array(
			'conditions' => array('Territory.office_id' => $office_id),
			'order' => array('Territory.name' => 'asc')
			));
		*/
		$territory_info = $this->Territory->find(
			'first',
			array(
				'conditions' => array('Territory.id' => $territory_id),
				'joins' => array(
					array(
						'table' => 'users',
						'alias' => 'User',
						'conditions' => 'SalesPerson.id=User.sales_person_id'
					)
				),
				'fields' => array('Office.id', 'User.user_group_id', 'User.id'),
				'recursive' => 0
			)
		);
		$user_group_id = $territory_info['User']['user_group_id'];
		$user_id = $territory_info['User']['id'];
		if ($user_group_id == 1008) {
			$this->loadModel('UserTerritoryList');
			$territory_list = $this->UserTerritoryList->find('list', array(
				'conditions' => array('UserTerritoryList.user_id' => $user_id),
				'fields' => array('UserTerritoryList.territory_id', 'UserTerritoryList.territory_id'),
				'recursive' => -1
			));
			if ($territory_list)
				$territory_id = array_keys($territory_list);
		}
		if ($territory_id) {

			$markets = $this->Memo->Market->find('list', array(
				'conditions' => array('Market.territory_id' => $territory_id),
				'order' => array('Market.name' => 'asc')
			));
		} else {
			$markets = array();
		}

		$outlets = $this->Memo->Outlet->find('list', array(
			'conditions' => array('Outlet.market_id' => $market_id),
			'order' => array('Outlet.name' => 'asc')
		));
		$current_date = date('d-m-Y', strtotime($this->current_date()));

		/*
		 * Report generation query start ;
		 * commented this by Abu naser at 14-june-2022 for not useing in view. in view not showing it 
		 */
		/* if (!empty($requested_data)) {
			if (!empty($requested_data['Memo']['office_id'])) {
				$office_id = $requested_data['Memo']['office_id'];
				$this->Memo->recursive = -1;
				$sales_people = $this->Memo->find('all', array(
					'fields' => array('DISTINCT(sales_person_id) as sales_person_id', 'SalesPerson.name'),
					'joins' => array(
						array(
							'table' => 'sales_people',
							'alias' => 'SalesPerson',
							'type' => 'INNER',
							'conditions' => array(
								' SalesPerson.id=Memo.sales_person_id',
								'SalesPerson.office_id' => $office_id
							)
						)
					),
					'callbacks' => false,
					'conditions' => array(
						'Memo.memo_date BETWEEN ? and ?' => array(date('Y-m-d', strtotime($requested_data['Memo']['date_from'])), date('Y-m-d', strtotime($requested_data['Memo']['date_to'])))
					),
				));

				$sales_person = array();
				foreach ($sales_people as $data) {
					$sales_person[] = $data['0']['sales_person_id'];
				}
				$sales_person = implode(',', $sales_person);
				//pr($sales_person);
				if (!empty($sales_person)) {
					$product_quantity = $this->Memo->query(" SELECT m.sales_person_id,md.product_id,SUM(md.sales_qty) as pro_quantity
					   FROM memos m RIGHT JOIN memo_details md on md.memo_id=m.id
					   WHERE (m.memo_date BETWEEN  '" . date('Y-m-d', strtotime($requested_data['Memo']['date_from'])) . "' AND '" . date('Y-m-d', strtotime($requested_data['Memo']['date_to'])) . "') AND sales_person_id IN (" . $sales_person . ")  GROUP BY m.sales_person_id,md.product_id");
					$this->set(compact('product_quantity', 'sales_people'));
					pr($product_quantity);
				}
			}
		} */
		
		//-------------added by golam rabbi-------------\\
		
		$programoffice_con = array();

		$programoffice_con = array(
			'User.user_group_id'	=>1016,
			'User.active'			=>1,
		);

		if ($office_parent_id == 0) {

		} else {

			$office_id = $this->UserAuth->getOfficeId();

			$programoffice_con['SalesPerson.office_id'] = $office_id;
		}
		
		if( $usergroupid == 1016){
			$programoffice_con['User.id'] = $this->UserAuth->getUserId();
		}

		$program_office_lsit = $this->SalesPerson->find('list', array(
			'conditions'=>$programoffice_con,
			'joins'=>array(
				array(
					'alias'=>'User',
					'table'=>'users',
					'type'=>'left',
					'conditions'=>'SalesPerson.id=User.sales_person_id'
				)
			),
			'fields'=>array('User.id', 'SalesPerson.name')
		));


		//echo "<pre>";print_r($program_office_lsit);exit;

		//--------------------end------------------\\
		

		$this->set(compact('offices', 'usergroupid', 'program_office_lsit', 'territories', 'markets', 'outlets', 'current_date', 'requested_data', 'product_name'));
	}

	private function condition_break_as_text($conditions)
	{
		$condition = '';
		foreach ($conditions as  $values) {
			list($key, $value) = each($values);
			if ($key === 0) {
				$condition .= " $value AND ";
				continue;
			}
			$key = explode(" ", $key);
			$con_key = $key[0];
			$con_operator = isset($key[1]) ? $key[1] : "=";
			if (is_array($value)) {
				$con_operator = "IN";
				$value = "(" . implode(',', $value) . ")";
			} else {
				$value = "'$value'";
			}
			$condition .= "$con_key $con_operator $value AND ";
		}
		$condition = rtrim($condition, "AND ");
		return $condition;
	}

	private function conditions_making_for_collection_subquery($params)
	{
		$conditions = array();
		if (CakeSession::read('Office.parent_office_id') != 0) {
			$conditions[] = array('Territory.office_id' => CakeSession::read('Office.id'));
		} elseif (!empty($params['Memo']['office_id'])) {
			//$conditions[] = array('SalesPerson.office_id' => $params['Memo']['office_id']);
			$conditions[] = array('Territory.office_id' => $params['Memo']['office_id']);
		}


		if (!empty($params['Memo']['memo_no'])) {
			$conditions[] = array('Memo.memo_no Like' => "%" . $params['Memo']['memo_no'] . "%");
		}
		if (!empty($params['Memo']['memo_reference_no'])) {
			$conditions[] = array('Memo.memo_reference_no Like' => "%" . $params['Memo']['memo_reference_no'] . "%");
		}
		if (!empty($params['Memo']['territory_id'])) {
			$conditions[] = array('(Memo.territory_id =' . $params['Memo']['territory_id'] . ' OR Memo.child_territory_id = ' . $params['Memo']['territory_id']  . ')');
		}
		if (!empty($params['Memo']['thana_id'])) {
			$conditions[] = array('Memo.thana_id' => $params['Memo']['thana_id']);
		}
		if (!empty($params['Memo']['market_id'])) {
			$conditions[] = array('Memo.market_id' => $params['Memo']['market_id']);
		}
		if (!empty($params['Memo']['outlet_id'])) {
			$conditions[] = array('Memo.outlet_id' => $params['Memo']['outlet_id']);
		}
		if (isset($params['Memo']['date_from']) != '') {
			$conditions[] = array('Memo.memo_date >=' => Date('Y-m-d H:i:s', strtotime($params['Memo']['date_from'])));
		}
		if (isset($params['Memo']['date_to']) != '') {
			$conditions[] = array('Memo.memo_date <=' => Date('Y-m-d H:i:s', strtotime($params['Memo']['date_to'] . ' 23:59:59')));
		}
		if (isset($params['Memo']['operator'])) {
			if ($params['Memo']['operator'] == 3) {
				$conditions[] = array('Memo.gross_value BETWEEN ? AND ?' => array($params['Memo']['memo_value_from'], $params['Memo']['memo_value_to']));
			} elseif ($params['Memo']['operator'] == 1) {
				$conditions[] = array('Memo.gross_value <' => $params['Memo']['mamo_value']);
			} elseif ($params['Memo']['operator'] == 2) {
				$conditions[] = array('Memo.gross_value >' => $params['Memo']['mamo_value']);
			}
		}
		
		if (!empty($params['Memo']['program_officer_id'])) {
			$conditions[] = array('Memo.created_by' => $params['Memo']['program_officer_id']);
			$conditions[] = array('Memo.is_program' =>1);
		}
		
		return $conditions;
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
		$this->set('page_title', 'Memo Details');

		$this->Memo->unbindModel(array('hasMany' => array('MemoDetail')));
		$memo = $this->Memo->find('first', array(
			'conditions' => array('Memo.id' => $id)
		));

		$this->loadModel('MemoDetail');
		if (!$this->Memo->exists($id)) {
			throw new NotFoundException(__('Invalid district'));
		}
		$this->MemoDetail->recursive = 0;
		$this->MemoDetail->unbindModel(
			array('belongsTo' => array('MeasurementUnit'))
		);
		$memo_details = $this->MemoDetail->find('all', array(
			'conditions' => array('MemoDetail.memo_id' => $id),
			'joins' => array(
				array(
					'table' => 'measurement_units',
					'alias' => 'MeasurementUnit',
					'type' => 'LEFT',
					'conditions' => array(
						'MeasurementUnit.id=case when MemoDetail.measurement_unit_id is null OR MemoDetail.measurement_unit_id=0 then Product.sales_measurement_unit_id else MemoDetail.measurement_unit_id END'
					)
				)
			),
			'fields' => array('MemoDetail.*', 'Product.*', 'Memo.*', 'MeasurementUnit.name'),
			'order' => array('Product.order' => 'asc')
		));
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.*', 'st.name', 'st.address', 'Territory.*', 'Office.office_name', 'Office.address'),
			'joins' => array(
				array(
					'table' => 'stores',
					'alias' => 'st',
					'type' => 'LEFT',
					'conditions' => array(
						'SalesPerson.territory_id=st.territory_id'
					)
				)
			),
			'conditions' => array('SalesPerson.id' => $memo['Memo']['sales_person_id']),
			'recursive' => 0
		));

		$user_name = $this->User->find('first', array(
			'conditions' => array('User.id' => $memo['Memo']['created_by']),
			'recursive' => -1
		));

		$this->set(compact('memo', 'memo_details', 'so_info', 'user_name'));
	}

	public function getProductPrice($product_id, $memo_date)
	{
		$this->LoadModel('ProductPrice');
		$product_prices = $this->ProductPrice->find('first', array(
			'conditions' => array(
				'ProductPrice.product_id' => $product_id,
				'ProductPrice.effective_date <=' => $memo_date,
				'ProductPrice.has_combination' => 0
			),
			'order' => array('ProductPrice.effective_date DESC'),
			'recursive' => -1

		));
		$this->autoRender = false;
		$product_prices = $product_prices['ProductPrice'];
		// pr($product_prices['general_price']); die();
		return $product_prices;
	}

	/**
	 * admin_delete method
	 *
	 * @return void
	 */
	public function admin_delete($id = null, $redirect = 1)
	{

		$this->loadModel('Product');
		$this->loadModel('Memo');
		$this->loadModel('MemoDetail');
		$this->loadModel('Deposit');
		$this->loadModel('Collection');


		//start memo setting
		$this->loadModel('MemoSetting');
		$MemoSettings = $this->MemoSetting->find('all', array(
			//'conditions' => array('ChallanDetail.challan_id' => $challan['Challan']['id']),
			'order' => array('id' => 'asc'),
			'recursive' => 0,
			//'limit' => 100
		));

		foreach ($MemoSettings as $s_result) {
			//echo $s_result['MemoSetting']['name'].'<br>';
			if ($s_result['MemoSetting']['name'] == 'stock_validation') {
				$stock_validation = $s_result['MemoSetting']['value'];
			}
			if ($s_result['MemoSetting']['name'] == 'stock_hit') {
				$stock_hit = $s_result['MemoSetting']['value'];
			}

			if ($s_result['MemoSetting']['name'] == 'ec_calculation') {
				$ec_calculation = $s_result['MemoSetting']['value'];
			}
			if ($s_result['MemoSetting']['name'] == 'oc_calculation') {
				$oc_calculation = $s_result['MemoSetting']['value'];
			}

			if ($s_result['MemoSetting']['name'] == 'sales_calculation') {
				$sales_calculation = $s_result['MemoSetting']['value'];
			}
			if ($s_result['MemoSetting']['name'] == 'stamp_calculation') {
				$stamp_calculation = $s_result['MemoSetting']['value'];
			}
			//pr($MemoSetting);
		}

		$this->set(compact('stock_validation'));
		//end memo setting


		if ($this->request->is('post')) {
			/*
			 * This condition added for data synchronization
			 * Cteated by imrul in 09, April 2017
			 * Duplicate memo check
			 */
			$count = $this->Memo->find('count', array(
				'conditions' => array(
					'Memo.id' => $id
				)
			));

			$memo_id_arr = $this->Memo->find('first', array(
				'conditions' => array(
					'Memo.id' => $id
				)
			));

			$this->loadModel('Store');
			$store_id_arr = $this->Store->find('first', array(
				'conditions' => array(
					'Store.territory_id' => $memo_id_arr['Memo']['child_territory_id']>0?$memo_id_arr['Memo']['child_territory_id']:$memo_id_arr['Memo']['territory_id']
				)
			));
			$store_id = $store_id_arr['Store']['id'];



			$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
			$product_list = Set::extract($products, '{n}.Product');


			// EC Calculation
			if ($ec_calculation) {
				$this->ec_calculation($memo_id_arr['Memo']['gross_value'], $memo_id_arr['Memo']['outlet_id'], $memo_id_arr['Memo']['territory_id'], $memo_id_arr['Memo']['memo_date'], 2);
				// OC Calculation
			}
			if ($ec_calculation) {
				$this->oc_calculation($memo_id_arr['Memo']['territory_id'], $memo_id_arr['Memo']['gross_value'], $memo_id_arr['Memo']['outlet_id'], $memo_id_arr['Memo']['memo_date'], $memo_id_arr['Memo']['memo_time'], 2);
			}

			

			for ($memo_detail_count = 0; $memo_detail_count < count($memo_id_arr['MemoDetail']); $memo_detail_count++) {
				$product_id =  $memo_id_arr['MemoDetail'][$memo_detail_count]['virtual_product_id'] ? $memo_id_arr['MemoDetail'][$memo_detail_count]['virtual_product_id'] : $memo_id_arr['MemoDetail'][$memo_detail_count]['product_id'];
				$sales_qty = $memo_id_arr['MemoDetail'][$memo_detail_count]['sales_qty'];
				$sales_price = $memo_id_arr['MemoDetail'][$memo_detail_count]['price'];
				$memo_territory_id = $memo_id_arr['Memo']['territory_id'];
				$memo_no = $memo_id_arr['Memo']['memo_no'];
				$memo_date = $memo_id_arr['Memo']['memo_date'];
				$outlet_id = $memo_id_arr['Memo']['outlet_id'];
				$market_id = $memo_id_arr['Memo']['market_id'];

				/*$punits_pre = $this->search_array($product_id, 'id', $product_list);
				if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
					$base_quantity = $sales_qty;
				} else {
					$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
				}*/
				
				
				

				$punits_pre = $this->search_array($product_id, 'id', $product_list);

				$measurement_unit_id = $memo_id_arr['MemoDetail'][$memo_detail_count]['measurement_unit_id'];
				
				if ($measurement_unit_id > 0) {
					if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
						$base_quantity = $sales_qty;
					} else {
						$base_quantity = $this->unit_convert($product_id, $measurement_unit_id, $sales_qty);
					}
				} else {
					if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
						$base_quantity = $sales_qty;
					} else {
						$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
					}
				}

				$update_type = 'add';
				$this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 12, $memo_id_arr['Memo']['memo_date']);



				// subract sales achievement and stamp achievemt
				// sales calculation
				$t_price = $sales_qty * $sales_price;
				if ($sales_calculation) {
					$this->sales_calculation($product_id, $memo_territory_id, $sales_qty, $t_price, $memo_date, 1);
				}

				//stamp calculation
				if ($stamp_calculation) {
					$this->stamp_calculation($memo_no, $memo_territory_id, $product_id, $outlet_id, $sales_qty, $memo_date, 1, $t_price, $market_id);
				}
			}

			$memo_id = $memo_id_arr['Memo']['id'];
			$memo_no = $memo_id_arr['Memo']['memo_no'];
			$this->Memo->id = $memo_id;
			$this->MemoDetail->deleteAll(array('MemoDetail.memo_id' => $memo_id));
			$this->Deposit->deleteAll(array('Deposit.memo_id' => $memo_no));
			$this->Collection->deleteAll(array('Collection.memo_id' => $memo_no));
			$this->Memo->delete();

			if ($redirect == 1) {
				$this->MemoProgramOfficer->deleteAll(array('MemoProgramOfficer.memo_id'	=> $memo_id));
				$this->flash(__('Memo was not deleted'), array('action' => 'index'));
				$this->redirect(array('action' => 'index'));
			} else {
			}
		}
	}



	/**
	 * admin_add method
	 *
	 * @return void
	 */

	public function admin_create_memo()
	{
		// Configure::write('debug', 2);
		date_default_timezone_set('Asia/Dhaka');
		$this->set('page_title', 'Create Memo');
		$this->loadModel('MemoDetail');
		/* ------ unset cart data ------- */
		$this->Session->delete('cart_session_data');
		$this->Session->delete('matched_session_data');
		$this->Session->delete('combintaion_qty_data');

		$this->Session->delete('b_data');

		$user_id = $this->UserAuth->getUserId();

		$generate_memo_no = $user_id . date('d') . date('m') . date('h') . date('i') . date('s');
		
		$usergroupid = $this->UserAuth->getUserGroupId();
		
		$this->set(compact('generate_memo_no', 'usergroupid'));



		if ($this->request->is('get') && isset($this->params['url']['csa']) && $this->params['url']['csa'] == 1) {
			$csa = 1;
			/* ------ start code of sale type list ------ */
			$sale_type_list = array(
				//2 => 'CSA Sales'
			);

			/* ------ end code of sale type list ------ */
		} else {
			$csa = 0;
			/* ------ start code of sale type list ------ */
			$sale_type_list = array(
				1 => 'SO Sales',
				//2 => 'CSA Sales',
				3 => 'SPO Sales',
				4 => 'Program Sales'
			);
			/* ------ end code of sale type list ------ */
		}
		$this->set('csa', $csa);

		$this->loadModel('Product');

		//start memo setting
		$this->loadModel('MemoSetting');
		$MemoSettings = $this->MemoSetting->find('all', array(
			//'conditions' => array('ChallanDetail.challan_id' => $challan['Challan']['id']),
			'order' => array('id' => 'asc'),
			'recursive' => 0,
			//'limit' => 100
		));

		foreach ($MemoSettings as $s_result) {
			//echo $s_result['MemoSetting']['name'].'<br>';
			if ($s_result['MemoSetting']['name'] == 'stock_validation') {
				$stock_validation = $s_result['MemoSetting']['value'];
			}
			if ($s_result['MemoSetting']['name'] == 'stock_hit') {
				$stock_hit = $s_result['MemoSetting']['value'];
			}

			if ($s_result['MemoSetting']['name'] == 'ec_calculation') {
				$ec_calculation = $s_result['MemoSetting']['value'];
			}
			if ($s_result['MemoSetting']['name'] == 'oc_calculation') {
				$oc_calculation = $s_result['MemoSetting']['value'];
			}

			if ($s_result['MemoSetting']['name'] == 'sales_calculation') {
				$sales_calculation = $s_result['MemoSetting']['value'];
			}
			if ($s_result['MemoSetting']['name'] == 'stamp_calculation') {
				$stamp_calculation = $s_result['MemoSetting']['value'];
			}
			//pr($MemoSetting);
		}

		$this->set(compact('stock_validation'));
		//end memo setting



		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$office_conditions = array(
				'office_type_id' => 2,
				"NOT" => array("id" => array(30, 31, 37))
			);
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}


		$this->set('office_id', $this->UserAuth->getOfficeId());
		$this->loadModel('Office');
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

		$office_id = 0;
		$territory_id = 0;
		$market_id = 0;
		$outlets = array();

		if ($this->request->is('post')) {
			
			unset($this->request->data['Memo']['thana_filter_id']);
			
			
			$office_id = isset($this->request->data['Memo']['office_id']) != '' ? $this->request->data['Memo']['office_id'] : 0;
			$territory_id = isset($this->request->data['Memo']['territory_id']) != '' ? $this->request->data['Memo']['territory_id'] : 0;
			$market_id = isset($this->request->data['Memo']['market_id']) != '' ? $this->request->data['Memo']['market_id'] : '';

			$this->loadModel('Store');
			$this->Store->recursive = -1;
			$store_info = $this->Store->find('first', array(
				'conditions' => array('territory_id' => $territory_id)
			));
			$store_id = $store_info['Store']['id'];
		} elseif ($office_parent_id != 0) {
			reset($offices);
			$office_id = key($offices);
			$product_id = '';
		}


		$this->loadModel('Territory');
		/***Show Except Parent(Who has Child) Territory ***/

		$child_territory_parent_id = $this->Territory->find('list', array(
			'conditions' => array(
				'parent_id !=' => 0,
			),
			'fields' => array('Territory.parent_id', 'Territory.name'),
		));

		$territories_list = $this->Territory->find('all', array(
			'conditions' => array(
				'Territory.office_id' => $office_id,
				'User.user_group_id !=' => 1008,
				'NOT' => array('Territory.id' => array_keys($child_territory_parent_id))
			),
			'joins' => array(
				array(
					'alias' => 'User',
					'table' => 'users',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.id = User.sales_person_id'
				)
			),
			'order' => array('Territory.name' => 'asc'),
			'recursive' => 0
		));

		$territories = array();
		foreach ($territories_list as $t_result) {
			$territories[$t_result['Territory']['id']] = $t_result['Territory']['name'] . ' (' . $t_result['SalesPerson']['name'] . ')';
		}

		$spo_territories = $this->Territory->find('list', array(
			'conditions' => array('Territory.office_id' => $office_id, 'User.user_group_id' => 1008),
			'joins' => array(
				array(
					'alias' => 'User',
					'table' => 'users',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.id = User.sales_person_id'
				)
			),
			'order' => array('Territory.name' => 'asc'),
			'recursive' => 0
		));
		$this->set(compact('spo_territories'));


		$markets = $this->Market->find('list', array(
			'conditions' => array('territory_id' => $territory_id),
			'order' => array('name' => 'asc')
		));

		if ($market_id) {
			$outlets = $this->Outlet->find('list', array(
				'conditions' => array('market_id' => $market_id),
				'order' => array('name' => 'asc')
			));
		}


		$current_date = date('d-m-Y', strtotime($this->current_date()));




		/* ----- start code of product list ----- */
		$product_list = $this->Product->find('list', array('order' => array('order' => 'asc')));
		/* ----- start code of product list ----- */

		if ($this->request->is('post')) {
			$sale_type_id = $this->request->data['Memo']['sale_type_id'];

			//  pr($this->request->data);
			//  exit;

			$outlet_is_within_group = $this->outletGroupCheck($this->request->data['Memo']['outlet_id']);
			$product_is_injectable = $this->productInjectableCheck($this->request->data['MemoDetail']['product_id']);

			if ($product_is_injectable && !$outlet_is_within_group && $sale_type_id != 2) {
				$this->Session->setFlash(__("Special product can't sell to ordinary outlet!"), 'flash/error');
				$this->redirect(array('action' => 'create_memo'));
				exit;
			}



			$outlet_info = $this->Outlet->find('first', array(
				'conditions' => array('Outlet.id' => $this->request->data['Memo']['outlet_id']),
				'recursive' => -1
			));
			$is_csa_outlet = $outlet_info['Outlet']['is_csa'];

			$this->loadModel('CsaMemo');
			$memo_no = $this->request->data['Memo']['memo_no'];


			if ($sale_type_id == 1 || $sale_type_id == 3 || $sale_type_id == 4) {
				$memo_count = $this->Memo->find('count', array(
					'conditions' => array('Memo.memo_no' => $memo_no),
					'fields' => array('memo_no'),
					'recursive' => -1
				));
			} elseif ($sale_type_id == 2) {
				$memo_count = $this->CsaMemo->find('count', array(
					'conditions' => array('CsaMemo.csa_memo_no' => $memo_no),
					'fields' => array('csa_memo_no'),
					'recursive' => -1
				));
			}

			if ($memo_count == 0) {
				if ($this->request->data['Memo']) {
					//pr($this->request->data);exit; //samiur

					/*START ADD NEW*/
					//get office id
					$office_id = $this->request->data['Memo']['office_id'];

					//get thana id
					$this->loadModel('Market');
					$market_info = $this->Market->find('first', array(
						'conditions' => array('Market.id' => $this->request->data['Memo']['market_id']),
						'fields' => 'Market.thana_id',
						'order' => array('Market.id' => 'asc'),
						'recursive' => -1,
						//'limit' => 100
					));
					$thana_id = $market_info['Market']['thana_id'];
					/*END ADD NEW*/

					$sale_type_id = $this->request->data['Memo']['sale_type_id'];

					if ($sale_type_id == 1 || $sale_type_id == 3 || $sale_type_id == 4) {
						$this->request->data['Memo']['is_active'] = 1;
						//$this->request->data['Memo']['status'] = ($this->request->data['Memo']['credit_amount'] != 0) ? 1 : 2;

						if (array_key_exists('Draft', $this->request->data)) {
							$this->request->data['Memo']['status'] = 0;
							$message = "Memo Has Been Saved as Draft";
						} else {
							$message = "Memo Has Been Saved";
							$this->request->data['Memo']['status'] = ($this->request->data['Memo']['credit_amount'] != 0) ? 1 : 2;
						}

						$sales_person = $this->SalesPerson->find('list', array(
							'conditions' => array('territory_id' => ($sale_type_id == 3 || ($sale_type_id == 4 && isset($this->request->data['Memo']['spo_territory_id']) && $this->request->data['Memo']['spo_territory_id'])) ? $this->request->data['Memo']['spo_territory_id'] : $this->request->data['Memo']['territory_id']),
							'order' => array('name' => 'asc')
						));

						$this->request->data['Memo']['sales_person_id'] = key($sales_person);

						$this->request->data['Memo']['entry_date'] = date('Y-m-d', strtotime($this->request->data['Memo']['entry_date']));
						$this->request->data['Memo']['memo_date'] = date('Y-m-d', strtotime($this->request->data['Memo']['memo_date']));
						/*echo "<pre>";
						  print_r($this->request->data['Memo']);
						echo "</pre>";die();*/

						//pr($this->request->data);
						//exit;

						//$memoData['id'] = 1995;

						$memoData['office_id'] = $this->request->data['Memo']['office_id'];
						$memoData['sale_type_id'] = $this->request->data['Memo']['sale_type_id'];

						/* If territory is Child/ has Parent then Save Parent Territory id in memo 
						and Child territory id in child_territory_id */
						$this->loadModel('Territory');
						$parent_territory_id = 0;
						$child_territory_id = 0;


						$terrtory_id_parent_id = $this->Territory->find('first', array(
							'fields' => array('Territory.parent_id', 'Territory.id'),
							'conditions' => array('Territory.id' => $this->request->data['Memo']['territory_id']),
							'recursive' => -1
						));

						// pr($terrtory_id_parent_id);exit;

						if ($terrtory_id_parent_id['Territory']['parent_id'] > 0) {
							/*Take child territory id and parent id*/
							$parent_territory_id = $terrtory_id_parent_id['Territory']['parent_id'];
							$child_territory_id = $terrtory_id_parent_id['Territory']['id'];
						} else {

							$parent_territory_id = $terrtory_id_parent_id['Territory']['id'];
							$child_territory_id = 0;
						}

						// pr( $parent_territory_id)   ;
						// pr($child_territory_id) ;exit;                  

						$memoData['territory_id'] = ($sale_type_id == 3 || ($sale_type_id == 4 && isset($this->request->data['Memo']['spo_territory_id']) && $this->request->data['Memo']['spo_territory_id'])) ? $this->request->data['Memo']['spo_territory_id'] : $parent_territory_id;
						$memoData['child_territory_id'] = $child_territory_id;
						$memoData['market_id'] = $this->request->data['Memo']['market_id'];
						$memoData['outlet_id'] = $this->request->data['Memo']['outlet_id'];
						//$memoData['entry_date'] = $this->request->data['Memo']['entry_date'];
						$memoData['memo_date'] = $this->request->data['Memo']['memo_date'];
						$memoData['memo_no'] = $this->request->data['Memo']['memo_no'];
						$memoData['gross_value'] = $this->request->data['Memo']['gross_value'];
						
						//$this->request->data['Memo']['total_discount']
						
						$memoData['cash_recieved'] = $this->request->data['Memo']['cash_recieved'];
						$memoData['credit_amount'] = $this->request->data['Memo']['credit_amount'];
						$memoData['is_active'] = $this->request->data['Memo']['is_active'];
						$memoData['status'] = $this->request->data['Memo']['status'];
						//$memoData['memo_time'] = $this->current_datetime();
						$memoData['memo_time'] = $this->request->data['Memo']['entry_date'];
						$memoData['sales_person_id'] = $this->request->data['Memo']['sales_person_id'];
						$memoData['from_app'] = 0;
						$memoData['action'] = 1;
						$memoData['is_program'] = ($sale_type_id == 4) ? 1 : 0;

						$memoData['memo_reference_no'] = $this->request->data['Memo']['memo_reference_no'];

						$memoData['created_at'] = $this->current_datetime();
						$memoData['created_by'] = $this->UserAuth->getUserId();
						$memoData['updated_at'] = $this->current_datetime();
						$memoData['updated_by'] = $this->UserAuth->getUserId();

						$memoData['office_id'] = $office_id ? $office_id : 0;
						$memoData['thana_id'] = $thana_id ? $thana_id : 0;
						if ($outlet_info['Outlet']['category_id'] == 17) {
							$memoData['is_distributor'] = 1;
						} elseif ($outlet_info['Outlet']['category_id'] == 23) {
							$memoData['is_distributor'] = 2;
						} else {
							$memoData['is_distributor'] = 0;
						}
						// pr($memoData);exit; // samiur2
						// exit;

						$memoData['total_discount'] = $this->request->data['Memo']['total_discount'];
						
						//echo '<pre>';print_r($this->request->data['MemoDetail']);exit;

						if ($this->request->data['Memo']['memo_date'] >= date('Y-m-d', strtotime('-3 month'))) {
							$this->Memo->create();

							if ($result = $this->Memo->save($memoData)) {
								if (isset($this->request->data['program_officer_id'])) {
									/*Add program officer*/
									$memo_id = $result['Memo']['id'];
									$this->loadModel('MemoProgramOfficer');
									$this->MemoProgramOfficer->save(
										array(
											'memo_id'				=> $memo_id,
											'program_officer_id'	=> $this->request->data['program_officer_id'],
											'created_by'			=> $this->UserAuth->getUserId(),
											'created_at'			=> $this->current_datetime()
										)
									);
								}
								// EC Calculation
								if ($ec_calculation && !$is_csa_outlet) {
									$this->ec_calculation($memoData['gross_value'], $memoData['outlet_id'], $memoData['territory_id'], $memoData['memo_date'], 1);
								}

								// OC Calculation
								if ($oc_calculation && !$is_csa_outlet) {
									$this->oc_calculation($memoData['territory_id'], $memoData['gross_value'], $memoData['outlet_id'], $memoData['memo_date'], $memoData['memo_time'], 1);
								}

								$memo_id = $this->Memo->getLastInsertId();


								$memo_info_arr = $this->Memo->find('first', array(
									'conditions' => array(
										'Memo.id' => $memo_id
									)
								));

								$this->loadModel('Store');

								$store_id_arr = $this->Store->find('first', array(
									'conditions' => array(
										'Store.territory_id' => $memo_info_arr['Memo']['child_territory_id']>0?$memo_info_arr['Memo']['child_territory_id']:$memo_info_arr['Memo']['territory_id']
									)
								));

								$store_id = $store_id_arr['Store']['id'];


								if ($memo_id) {
									$all_product_id = $this->request->data['MemoDetail']['product_id'];
									if (!empty($this->request->data['MemoDetail'])) {
										$total_product_data = array();
										$memo_details = array();
										$memo_details['MemoDetail']['memo_id'] = $memo_id;

										foreach ($this->request->data['MemoDetail']['product_id'] as $key => $val) {
											if ($val == null || $this->request->data['MemoDetail']['sales_qty'][$key] <= 0) {
												continue;
											}
											$product_details = $this->Product->find('first', array(
												'fields' => array('id', 'is_virtual', 'parent_id'),
												'conditions' => array('Product.id' => $val),
												'recursive' => -1
											));
											if ($product_details['Product']['is_virtual'] == 1) {
												$product_id = $memo_details['MemoDetail']['virtual_product_id'] = $val;
												$memo_details['MemoDetail']['product_id'] = $product_details['Product']['parent_id'];
											} else {
												$memo_details['MemoDetail']['virtual_product_id'] = 0;
												$product_id = $memo_details['MemoDetail']['product_id'] = $product_details['Product']['id'];
											}

											$memo_details['MemoDetail']['measurement_unit_id'] = $this->request->data['MemoDetail']['measurement_unit_id'][$key];
											$sales_price =  $this->request->data['MemoDetail']['Price'][$key];
											$memo_details['MemoDetail']['price'] = $sales_price;
											//$this->request->data['MemoDetail']['discount_amount'][$key];
											$memo_details['MemoDetail']['actual_price'] = $sales_price;
											$sales_qty = $memo_details['MemoDetail']['sales_qty'] = $this->request->data['MemoDetail']['sales_qty'][$key];

											$memo_details['MemoDetail']['product_price_id'] = $this->request->data['MemoDetail']['product_price_id'][$key];
											$memo_details['MemoDetail']['product_combination_id'] = $this->request->data['MemoDetail']['combination_id'][$key];
											$memo_details['MemoDetail']['bonus_qty'] = $this->request->data['MemoDetail']['bonus_product_qty'][$key];


											if ($this->request->data['MemoDetail']['bonus_product_id'][$key] != 0) {
												$memo_details['MemoDetail']['bonus_product_id'] = $this->request->data['MemoDetail']['bonus_product_id'][$key];
											} else {
												$memo_details['MemoDetail']['bonus_product_id'] = null;
											}

											//add new for policy
											$memo_details['MemoDetail']['discount_type'] = $this->request->data['MemoDetail']['disccount_type'][$key];
											$memo_details['MemoDetail']['discount_amount'] = $this->request->data['MemoDetail']['discount_amount'][$key];
											$memo_details['MemoDetail']['policy_type'] = $this->request->data['MemoDetail']['policy_type'][$key];
											$memo_details['MemoDetail']['policy_id'] = $this->request->data['MemoDetail']['policy_id'][$key];
											if ($this->request->data['MemoDetail']['is_bonus'][$key] == 3) {
												$memo_details['MemoDetail']['is_bonus'] = 3;
											}

											$selected_set = '';
											if (isset($this->request->data['MemoDetail']['selected_set'][$memo_details['MemoDetail']['policy_id']])) {
												$selected_set = $this->request->data['MemoDetail']['selected_set'][$memo_details['MemoDetail']['policy_id']];
											}
											$other_info = array();
											if ($selected_set) {
												$other_info = array(
													'selected_set' => $selected_set
												);
											}
											if ($other_info) {
												$memo_details['MemoDetail']['other_info'] = json_encode($other_info);
											}
											//Start for bonus
											$memo_date = date('Y-m-d', strtotime($this->request->data['Memo']['memo_date']));
											$bonus_product_id = $this->request->data['MemoDetail']['bonus_product_id'];
											$bonus_product_qty = $this->request->data['MemoDetail']['bonus_product_qty'];
											$memo_details['MemoDetail']['bonus_id'] = 0;
											$memo_details['MemoDetail']['bonus_scheme_id'] = 0;
											if ($bonus_product_qty[$key] > 0) {
												//echo $bonus_product_id[$key].'<br>';
												$b_product_id = $bonus_product_id[$key];
												$bonus_result = $this->bouns_and_scheme_id_set($b_product_id, $memo_date);
												$memo_details['MemoDetail']['bonus_id'] = $bonus_result['bonus_id'];
											}
											//End for bouns

											//pr($memo_details);
											$total_product_data[] = $memo_details;


											if ($bonus_product_qty[$key] > 0) {

												if ($product_details['Product']['is_virtual'] == 1) {
													$memo_details_bonus['MemoDetail']['virtual_product_id'] = $val;
													$memo_details_bonus['MemoDetail']['product_id'] = $product_details['Product']['parent_id'];
												} else {
													$memo_details_bonus['MemoDetail']['virtual_product_id'] = 0;
													$memo_details_bonus['MemoDetail']['product_id'] = $product_details['Product']['id'];
												}
												$memo_details_bonus['MemoDetail']['memo_id'] = $memo_id;
												$memo_details_bonus['MemoDetail']['is_bonus'] = 1;
												$memo_details_bonus['MemoDetail']['measurement_unit_id'] = $this->request->data['MemoDetail']['measurement_unit_id'][$key];
												$memo_details_bonus['MemoDetail']['price'] = 0.0;
												$memo_details_bonus['MemoDetail']['sales_qty'] = $this->request->data['MemoDetail']['bonus_product_qty'][$key];
												$total_product_data[] = $memo_details_bonus;
												unset($memo_details_bonus);
												//update inventory
												if ($stock_hit) {
													$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
													$product_list = Set::extract($products, '{n}.Product');
													$bonus_qty = $this->request->data['MemoDetail']['bonus_product_qty'][$key];
													$punits_pre = $this->search_array($product_id, 'id', $product_list);
													if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
														$base_quantity = $bonus_qty;
													} else {
														$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $bonus_qty);
													}
													
													$update_type = 'deduct';
													$this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 11, $this->request->data['Memo']['memo_date']);
												}
											}

											//update inventory
											if ($stock_hit) {
												$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
												$product_list = Set::extract($products, '{n}.Product');

												/*$punits_pre = $this->search_array($product_id, 'id', $product_list);
												if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
													$base_quantity = $sales_qty;
												} else {
													$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
												}*/


												$punits_pre = $this->search_array($product_id, 'id', $product_list);
												$measurement_unit_id = $this->request->data['MemoDetail']['measurement_unit_id'][$key];
												if ($measurement_unit_id > 0) {
													if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
														$base_quantity = $sales_qty;
													} else {
														$base_quantity = $this->unit_convert($product_id, $measurement_unit_id, $sales_qty);									
													}
												} else {
													if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
														$base_quantity = $sales_qty;
													} else {
														$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
														
													}
												}

												$update_type = 'deduct';
												$this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 11, $this->request->data['Memo']['memo_date']);
											}



											// sales calculation
											$tt_price = $sales_qty * $sales_price;
											if ($sales_calculation) {
												$this->sales_calculation($memo_details['MemoDetail']['product_id'], $memoData['territory_id'], $memo_details['MemoDetail']['sales_qty'], $tt_price, $memoData['memo_date'], 1);
											}

											//stamp calculation
											if ($stamp_calculation) {
												$this->stamp_calculation($memoData['memo_no'], $memoData['territory_id'], $memo_details['MemoDetail']['product_id'], $memoData['outlet_id'], $memo_details['MemoDetail']['sales_qty'], $memoData['memo_date'], 1, $tt_price, $memoData['market_id']);
											}
										}
										//exit;
										// pr($total_product_data);exit;
										$this->MemoDetail->saveAll($total_product_data);
									}
								}
							}

							$this->Session->setFlash(__($message), 'flash/success');
							$this->redirect(array('action' => 'index'));
						} else {
							$this->Session->setFlash(__('Memo Date Should Not Be less Then 3 Months'), 'flash/error');
						}
					}

					if ($sale_type_id == 2) {
						$this->loadModel('CsaMemo');
						$this->loadModel('CsaMemoDetail');

						$this->request->data['Memo']['is_active'] = 1;
						//$this->request->data['Memo']['status'] = ($this->request->data['Memo']['credit_amount'] != 0) ? 1 : 2;
						if (array_key_exists('draft', $this->request->data)) {
							$this->request->data['Memo']['status'] = 0;
							$message = "CSA Memo Has Been Saved as Draft";
						} else {
							$message = "CSA Memo Has Been Saved";
							$this->request->data['Memo']['status'] = ($this->request->data['Memo']['credit_amount'] != 0) ? 1 : 2;
						}

						$sales_person = $this->SalesPerson->find('list', array(
							'conditions' => array('territory_id' => $this->request->data['Memo']['territory_id']),
							'order' => array('name' => 'asc')
						));

						$this->request->data['Memo']['sales_person_id'] = key($sales_person);


						$this->request->data['Memo']['entry_date'] = date('Y-m-d', strtotime($this->request->data['Memo']['entry_date']));
						$this->request->data['Memo']['memo_date'] = date('Y-m-d', strtotime($this->request->data['Memo']['memo_date']));
						/*echo "<pre>";
						  print_r($this->request->data['Memo']);
						echo "</pre>";die();*/

						$memoData['office_id'] = $this->request->data['Memo']['office_id'];
						$memoData['csa_id'] = $this->request->data['Memo']['csa_id'];
						$memoData['sale_type_id'] = $this->request->data['Memo']['sale_type_id'];
						$memoData['territory_id'] = $this->request->data['Memo']['territory_id'];
						$memoData['market_id'] = $this->request->data['Memo']['market_id'];
						$memoData['outlet_id'] = $this->request->data['Memo']['outlet_id'];
						$memoData['entry_date'] = $this->request->data['Memo']['entry_date'];
						$memoData['memo_date'] = $this->request->data['Memo']['memo_date'];
						$memoData['csa_memo_no'] = $memo_no;
						$memoData['gross_value'] = $this->request->data['Memo']['gross_value'];
						$memoData['cash_recieved'] = $this->request->data['Memo']['cash_recieved'];
						$memoData['credit_amount'] = $this->request->data['Memo']['credit_amount'];
						$memoData['is_active'] = $this->request->data['Memo']['is_active'];
						$memoData['status'] = $this->request->data['Memo']['status'];
						//$memoData['memo_time'] = $this->current_datetime();
						$memoData['memo_time'] = $this->request->data['Memo']['entry_date'];
						$memoData['sales_person_id'] = $this->request->data['Memo']['sales_person_id'];
						$memoData['from_app'] = 0;
						$memoData['action'] = 1;

						$memoData['memo_reference_no'] = $this->request->data['Memo']['memo_reference_no'];

						$memoData['created_at'] = $this->current_datetime();
						$memoData['created_by'] = $this->UserAuth->getUserId();
						$memoData['updated_at'] = $this->current_datetime();
						$memoData['updated_by'] = $this->UserAuth->getUserId();

						$memoData['office_id'] = $office_id ? $office_id : 0;
						$memoData['thana_id'] = $thana_id ? $thana_id : 0;

						$memoData['total_discount'] = $this->request->data['Memo']['total_discount'];

						if ($this->request->data['Memo']['memo_date'] >= date('Y-m-d', strtotime('-3 month'))) {
							$this->CsaMemo->create();
							if ($this->CsaMemo->save($memoData)) {
								$csa_memo_id = $this->CsaMemo->getLastInsertId();


								// EC Calculation
								if ($ec_calculation) {
									$this->ec_calculation($memoData['gross_value'], $memoData['outlet_id'], $memoData['territory_id'], $memoData['memo_date'], 1);
								}

								// OC Calculation
								if ($oc_calculation) {
									$this->oc_calculation($memoData['territory_id'], $memoData['gross_value'], $memoData['outlet_id'], $memoData['memo_date'], $memoData['memo_time'], 1);
								}


								if ($csa_memo_id) {
									if (!empty($this->request->data['MemoDetail'])) {
										$total_product_data = array();
										$memo_details = array();
										$memo_details['CsaMemoDetail']['csa_memo_id'] = $csa_memo_id;

										foreach ($this->request->data['MemoDetail']['product_id'] as $key => $val) {
											if ($val) {
												$product_details = $this->Product->find('first', array(
													'fields' => array('id', 'is_virtual', 'parent_id'),
													'conditions' => array('Product.id' => $val),
													'recursive' => -1
												));
												if ($product_details['Product']['is_virtual'] == 1) {
													$product_id = $memo_details['CsaMemoDetail']['virtual_product_id'] = $val;
													$memo_details['CsaMemoDetail']['product_id'] = $product_details['Product']['parent_id'];
												} else {
													$memo_details['CsaMemoDetail']['virtual_product_id'] = 0;
													$product_id = $memo_details['CsaMemoDetail']['product_id'] = $product_details['Product']['id'];
												}
												$memo_details['CsaMemoDetail']['csa_memo_id'] = $csa_memo_id;
												$memo_details['CsaMemoDetail']['product_id'] = $val;
												$memo_details['CsaMemoDetail']['measurement_unit_id'] = $this->request->data['MemoDetail']['measurement_unit_id'][$key];
												$memo_details['CsaMemoDetail']['price'] = $this->request->data['MemoDetail']['Price'][$key];
												$memo_details['CsaMemoDetail']['sales_qty'] = $this->request->data['MemoDetail']['sales_qty'][$key];
												$memo_details['CsaMemoDetail']['product_price_id'] = $this->request->data['MemoDetail']['product_price_id'][$key];
												$memo_details['CsaMemoDetail']['bonus_qty'] = $this->request->data['MemoDetail']['bonus_product_qty'][$key];
												if ($this->request->data['MemoDetail']['bonus_product_id'][$key] != 0) {
													$memo_details['CsaMemoDetail']['bonus_product_id'] = $this->request->data['MemoDetail']['bonus_product_id'][$key];
												} else {
													$memo_details['CsaMemoDetail']['bonus_product_id'] = null;
												}

												//add new for policy
												$memo_details['CsaMemoDetail']['discount_type'] = $this->request->data['MemoDetail']['discount_type'][$key];
												$memo_details['CsaMemoDetail']['discount_amount'] = $this->request->data['MemoDetail']['discount_amount'][$key];
												$memo_details['CsaMemoDetail']['policy_type'] = $this->request->data['MemoDetail']['policy_type'][$key];

												//Start for bonus
												$memo_date = date('Y-m-d', strtotime($this->request->data['Memo']['memo_date']));
												$bonus_product_id = $this->request->data['MemoDetail']['bonus_product_id'];
												$bonus_product_qty = $this->request->data['MemoDetail']['bonus_product_qty'];
												$memo_details['CsaMemoDetail']['bonus_id'] = 0;
												$memo_details['CsaMemoDetail']['bonus_scheme_id'] = 0;
												if ($bonus_product_qty[$key] > 0) {
													//echo $bonus_product_id[$key].'<br>';
													$b_product_id = $bonus_product_id[$key];
													$bonus_result = $this->bouns_and_scheme_id_set($b_product_id, $memo_date);
													$memo_details['CsaMemoDetail']['bonus_id'] = $bonus_result['bonus_id'];
												}
												//End for bouns

												$total_product_data[] = $memo_details;
												if ($bonus_product_qty[$key] > 0) {
													if ($product_details['Product']['is_virtual'] == 1) {
														$memo_details_bonus['CsaMemoDetail']['virtual_product_id'] = $val;
														$memo_details_bonus['CsaMemoDetail']['product_id'] = $product_details['Product']['parent_id'];
													} else {
														$memo_details_bonus['CsaMemoDetail']['virtual_product_id'] = 0;
														$memo_details_bonus['CsaMemoDetail']['product_id'] = $product_details['Product']['id'];
													}
													$memo_details_bonus['CsaMemoDetail']['csa_memo_id'] = $csa_memo_id;
													$memo_details_bonus['CsaMemoDetail']['product_id'] = $val;
													$memo_details_bonus['CsaMemoDetail']['measurement_unit_id'] = $this->request->data['MemoDetail']['measurement_unit_id'][$key];
													$memo_details_bonus['CsaMemoDetail']['price'] = 0.0;
													$memo_details_bonus['CsaMemoDetail']['sales_qty'] = $this->request->data['MemoDetail']['bonus_product_qty'][$key];
													$memo_details_bonus['CsaMemoDetail']['is_bonus'] = 1;
													$total_product_data[] = $memo_details_bonus;
													unset($memo_details_bonus);
												}
											}
										}
										//pr($total_product_data);
										//exit;
										$this->CsaMemoDetail->saveAll($total_product_data);
									}
								}
							}

							$this->Session->setFlash(__($message), 'flash/success');
							$this->redirect(array("controller" => "CsaMemos", 'action' => 'index'));
							//exit;
						} else {
							$this->Session->setFlash(__('Memo Date Should Not Be less Then 3 Months'), 'flash/error');
						}
					}
				}
			} else {
				$this->Session->setFlash(__('Memo number already exist'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}
		}

		$this->set(compact('offices', 'territories', 'product_list', 'outlets', 'current_date', 'sale_type_list', 'office_parent_id', 'sales_person'));
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
		// Configure::write('debug', 2);
		// pr($id);exit;
		$this->loadModel('ProductCombination');
		$this->loadModel('Combination');
		$this->loadModel('CombinationDetailsV2');
		$this->Session->delete('cart_session_data');
		$this->Session->delete('matched_session_data');
		$this->Session->delete('combintaion_qty_data');
		$this->Session->delete('b_data');

		$this->loadModel('Product');
		//start memo setting
		$this->loadModel('MemoSetting');
		$MemoSettings = $this->MemoSetting->find('all', array(
			//'conditions' => array('ChallanDetail.challan_id' => $challan['Challan']['id']),
			'order' => array('id' => 'asc'),
			'recursive' => 0,
			//'limit' => 100
		));
		//$this->dd($MemoSettings);
		foreach ($MemoSettings as $s_result) {
			//echo $s_result['MemoSetting']['name'].'<br>';
			if ($s_result['MemoSetting']['name'] == 'stock_validation') {
				$stock_validation = $s_result['MemoSetting']['value'];
			}
			if ($s_result['MemoSetting']['name'] == 'stock_hit') {
				$stock_hit = $s_result['MemoSetting']['value'];
			}

			if ($s_result['MemoSetting']['name'] == 'ec_calculation') {
				$ec_calculation = $s_result['MemoSetting']['value'];
			}
			if ($s_result['MemoSetting']['name'] == 'oc_calculation') {
				$oc_calculation = $s_result['MemoSetting']['value'];
			}

			if ($s_result['MemoSetting']['name'] == 'sales_calculation') {
				$sales_calculation = $s_result['MemoSetting']['value'];
			}
			if ($s_result['MemoSetting']['name'] == 'stamp_calculation') {
				$stamp_calculation = $s_result['MemoSetting']['value'];
			}
			//pr($MemoSetting);
		}

		$this->set(compact('stock_validation'));
		//end memo setting

		$current_date = date('d-m-Y', strtotime($this->current_date()));



		/* ------- start get edit data -------- */
		$this->Memo->recursive = 1;
		$options = array(
			'conditions' => array('Memo.id' => $id)
		);

		$existing_record = $this->Memo->find('first', $options);
		
		if( $existing_record['Memo']['action'] == 0 ){
			
			$this->Session->setFlash(__('Memo has been pull already.'), 'flash/success');
			$this->redirect(array('action' => 'index'));
			
		}
		
		
		
		
		/*Retrive memo's program officer id*/
		$memo_program_officer = $this->MemoProgramOfficer->find('first', array(
			'fields'			=> array('MemoProgramOfficer.program_officer_id', 'SalesPerson.name'),
			'conditions'		=> array('MemoProgramOfficer.memo_id'	=> $existing_record['Memo']['id']),
			'joins' => array(
				array(
					'alias'			=> 'User',
					'table'			=> 'users',
					'type'			=> 'left',
					'conditions'	=> 'MemoProgramOfficer.program_officer_id=User.id'
				),
				array(
					'alias'			=> 'SalesPerson',
					'table'			=> 'sales_people',
					'type'			=> 'left',
					'conditions'	=> 'User.sales_person_id=SalesPerson.id'
				)
			),
			'recursive' => -1
		));
		$prom_officer = array(
			'id'	=> $memo_program_officer['MemoProgramOfficer']['program_officer_id'],
			'name'	=> '[' . $memo_program_officer['MemoProgramOfficer']['program_officer_id'] . ']-' . $memo_program_officer['SalesPerson']['name']
		);
		//$this->dd($prom_officer);
		$this->set(compact('prom_officer'));
		/*End Memo program officer scope*/
		// pr($existing_record);die();
		$details_data = array();
		foreach ($existing_record['MemoDetail'] as $detail_val) {
			$product = $detail_val['virtual_product_id'] ? $detail_val['virtual_product_id'] : $detail_val['product_id'];
			if ($detail_val['product_combination_id']) {
				$combined_product = $this->CombinationDetailsV2->find('all', array(
					'conditions' => array('CombinationDetailsV2.combination_id' => $detail_val['product_combination_id']),
					'fields' => array('product_id'),
					'recursive' => -1
				));
				$combined_product = array_map(function ($val) {
					return $val['CombinationDetailsV2']['product_id'];
				}, $combined_product);
				$combined_product = implode(',', $combined_product);
				$detail_val['combined_product'] = $combined_product;
			}
			$details_data[] = $detail_val;
		}

		$existing_record['MemoDetail'] = $details_data;

		$this->loadModel('MeasurementUnit');
		for ($i = 0; $i < count($details_data); $i++) {
			$measurement_unit_id = $details_data[$i]['measurement_unit_id'];
			$measurement_unit_name = $this->MeasurementUnit->find('all', array(
				'conditions' => array('MeasurementUnit.id' => $measurement_unit_id),
				'fields' => array('name'),
				'recursive' => -1
			));
			$existing_record['MemoDetail'][$i]['measurement_unit_name'] = $measurement_unit_name[0]['MeasurementUnit']['name'];
		}

		$existing_record['office_id'] = $existing_record['SalesPerson']['office_id'];
		$existing_record['territory_id'] = $existing_record['Memo']['territory_id'];
		$existing_record['child_territory_id'] = $existing_record['Memo']['child_territory_id'];
		$existing_record['market_id'] = $existing_record['Memo']['market_id'];
		$existing_record['outlet_id'] = $existing_record['Memo']['outlet_id'];
		$existing_record['memo_time'] = date('d-m-Y', strtotime($existing_record['Memo']['memo_time']));
		$existing_record['memo_date'] = date('d-m-Y', strtotime($existing_record['Memo']['memo_date']));
		$existing_record['memo_no'] = $existing_record['Memo']['memo_no'];
		$existing_record['memo_reference_no'] = $existing_record['Memo']['memo_reference_no'];

		$territory_id_for_stock = $existing_record['Memo']['child_territory_id']>0?$existing_record['Memo']['child_territory_id']:$existing_record['Memo']['territory_id'];

		// pr($existing_record);exit;
		$this->LoadModel('Product');
		$this->LoadModel('Store');
		$store_id = $this->Store->find('first', array(
			'fields' => array('Store.id'),
			'conditions' => array('Store.territory_id' => $territory_id_for_stock),
			'recursive' => -1
		));
		$open_bonus_product = $this->Product->find('all', array(
			'fields' => array('Product.id', 'Product.name', 'OpenCombination.id'),
			'joins' => array(
				array(
					'table' => 'current_inventories',
					'alias' => 'CurrentInventory',
					'type' => 'Inner',
					'conditions' => 'CurrentInventory.product_id=Product.id'
				),
				array(
					'table' => 'open_combination_products',
					'alias' => 'OpenCombinationProduct',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.product_id=Product.id'
				),
				array(
					'table' => 'open_combinations',
					'alias' => 'OpenCombination',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.combination_id=OpenCombination.id and is_bonus=1 and \'' . date('Y-m-d', strtotime($existing_record['Memo']['memo_date'])) . '\'BETWEEN OpenCombination.start_date AND OpenCombination.end_date'
				),

			),
			'conditions' => array(
				'CurrentInventory.qty >' => 0,
				'CurrentInventory.store_id' => $store_id['Store']['id'],
				'OpenCombination.start_date <=' => date('Y-m-d'),
				'OpenCombination.end_date >=' => date('Y-m-d'),
				'OpenCombination.is_bonus' => 1
			),
			'recursive' => -1
		));
		
		$open_bonus_product_option = array();
		foreach ($open_bonus_product as $bonus_product) {
			$open_bonus_product_option[$bonus_product['Product']['id']] = $bonus_product['Product']['name'];
		}

		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$office_conditions = array();
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}

		$this->set('office_id', $existing_record['office_id']);
		$this->loadModel('Office');
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

		$office_id = $existing_record['office_id'];
		$territory_id = $existing_record['territory_id'];
		$market_id = $existing_record['market_id'];
		$outlets = array();

		// pr($territory_id);exit;


		$this->loadModel('Territory');
		$territories_list = $this->Territory->find('all', array(
			'conditions' => array('Territory.office_id' => $office_id),
			'order' => array('Territory.name' => 'asc')
		));
		$territories = array();
		foreach ($territories_list as $t_result) {
			$territories[$t_result['Territory']['id']] = $t_result['Territory']['name'] . ' (' . $t_result['SalesPerson']['name'] . ')';
		}




		//for spo user
		$spo_territories = $this->Territory->find('list', array(
			'conditions' => array('Territory.office_id' => $office_id, 'User.user_group_id' => 1008),
			'joins' => array(
				array(
					'alias' => 'User',
					'table' => 'users',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.id = User.sales_person_id'
				)
			),
			'order' => array('Territory.name' => 'asc'),
			'recursive' => 0
		));
		$this->set(compact('spo_territories'));



		//get spo territories id
		$this->loadModel('Usermgmt.User');
		$user_info = $this->User->find('all', array(
			'fields' => array('User.id', 'User.sales_person_id', 'User.username', 'SalesPerson.id', 'SalesPerson.name', 'SalesPerson.office_id', 'SalesPerson.territory_id', 'UserGroup.id', 'UserGroup.name', 'UserTerritoryList.territory_id'),
			'conditions' => array('SalesPerson.id' => $existing_record['Memo']['sales_person_id'], 'User.active' => 1),
			'joins' => array(
				array(
					'alias' => 'UserTerritoryList',
					'table' => 'user_territory_lists',
					'type' => 'INNER',
					'conditions' => 'User.id = UserTerritoryList.user_id'
				)
			),
			'recursive' => 0
		));


		// pr($territory_id);exit;

		$territory_ids = array($territory_id);
		$user_group_id = 0;
		if ($user_info) {
			foreach ($user_info as $u_result) {
				//echo $result['UserTerritoryList']['territory_id'].'<br>';
				array_push($territory_ids, $u_result['UserTerritoryList']['territory_id']);
			}

			$user_group_id = $u_result['UserGroup']['id'];
		}

		//add child Territory id to Territory ids

		if ($existing_record['child_territory_id'] > 0) {
			array_push($territory_ids, $existing_record['child_territory_id']);
		}


		/*pr($existing_record);
		exit;*/

		if ( ($this->UserAuth->getUserGroupId() == 1016 
			AND $existing_record['Memo']['is_program'] == 1)
		|| $existing_record['Memo']['is_program'] == 1) {
			$sale_type_id = 4;
		} elseif ($user_group_id == 1008) {
			$sale_type_id = 3;
		} else {
			$sale_type_id = 1;
		}
	
		$usergroupid = $this->UserAuth->getUserGroupId();

		$this->set(compact('user_group_id', 'usergroupid', 'sale_type_id'));

		// pr($territory_ids);exit;
		//end for spo user



		$markets = $this->Market->find('list', array(
			'conditions' => array('territory_id' => $territory_ids),
			'order' => array('name' => 'asc')
		));

		//exit;
		// pr($markets);exit;

		if ($market_id) {
			$outlets = $this->Outlet->find('list', array(
				'conditions' => array('market_id' => $market_id),
				'order' => array('name' => 'asc')
			));
		}

		$this->loadModel('Store');
		$this->loadModel('Product');
		$this->loadModel('CurrentInventory');
		$store_info = $this->Store->find('first', array(
			'conditions' => array(
				'Store.territory_id' => $territory_id_for_stock
			),
			'recursive' => -1
		));
		$store_id = $store_info['Store']['id'];


		foreach ($existing_record['MemoDetail'] as $key => $single_product) {
			$total_qty_arr = $this->CurrentInventory->find('all', array(
				'conditions' => array('store_id' => $store_id, 'product_id' => $single_product['product_id']),
				'fields' => array('sum(qty) as total'),
				'recursive' => -1
			));

			$total_qty = $total_qty_arr[0][0]['total'];

			$sales_total_qty = $this->unit_convertfrombase($single_product['product_id'], $single_product['measurement_unit_id'], $total_qty);

			$existing_record['MemoDetail'][$key]['stock_qty'] = $sales_total_qty;
		}


		$products_from_ci = $this->CurrentInventory->find('all', array(
			'fields' => array('DISTINCT CurrentInventory.product_id'),
			'conditions' => array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.qty > ' => 0, 'inventory_status_id' => 1)
		));

		$product_ci = array();
		foreach ($products_from_ci as $each_ci) {
			$product_ci[] = $each_ci['CurrentInventory']['product_id'];
		}
		foreach ($existing_record['MemoDetail'] as $value) {
			$product_ci[] = $value['virtual_product_id'] ? $value['virtual_product_id'] : $value['product_id'];
		}

		$product_ci_in = implode(",", $product_ci);

		$products = $this->Product->find('all', array(
			'conditions' => array('Product.id' => $product_ci), 'order' => array('Product.order' => 'asc'),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'ParentProduct',
					'type' => 'left',
					'conditions' => 'ParentProduct.id=Product.parent_id'
				)
			),
			'fields' => array('Product.id as id', 'Product.name as name', 'ParentProduct.id as p_id', 'ParentProduct.name as p_name'),
			'recursive' => -1
		));
		$group_product = array();
		foreach ($products as $data) {
			if ($data[0]['p_id']) {
				$group_product[$data[0]['p_id']][] = $data[0]['id'];
			} else {
				$group_product[$data[0]['id']][] = $data[0]['id'];
			}
		}
		$product_list = array();
		foreach ($products as $data) {
			if ($data['0']['p_id'] && count($group_product[$data[0]['p_id']]) == 1) {
				$name = $data[0]['p_name'];
			} else {
				$name = $data[0]['name'];
			}
			$product_list[$data[0]['id']] = $name;
		}
		/* ------- end get edit data -------- */


		/*-----------My Work--------------*/
		$this->loadModel('ProductPrice');
		$this->loadModel('ProductCombination');

		$selected_bonus = array();
		$selected_set = array();
		$selected_policy_type = array();
		foreach ($existing_record['MemoDetail'] as $key => $value) {
			if ($value['virtual_product_id']) {
				$value['product_id'] = $value['virtual_product_id'];
			}
			$existing_product_category_id_array = $this->Product->find('all', array(
				'conditions' => array('Product.id' => $value['product_id']),
				'fields' => array('product_category_id', 'product_type_id'),
				'recursive' => -1
			));

			$existing_product_category_id = $existing_product_category_id_array[0]['Product']['product_category_id'];
			
			$existing_record['MemoDetail'][$key]['product_type_id'] = $existing_product_category_id_array[0]['Product']['product_type_id'];
			
			if ($value['discount_amount'] > 0 && $value['policy_type'] == 3 ) {
				$selected_policy_type[$value['policy_id']] = 1;
				
			}
			
			if ($value['is_bonus'] == 3 ) {
				if ($value['policy_type'] == 3) {
					$selected_policy_type[$value['policy_id']] = 2;
				}
				if ($value['other_info']) {
					$other_info = json_decode($value['other_info'], 1);
					$selected_set[$value['policy_id']] = $other_info['selected_set'];
					$selected_bonus[$value['policy_id']][$other_info['selected_set']][$value['product_id']] = $value['sales_qty'];
				} else {
					$selected_bonus[$value['policy_id']][1][$value['product_id']] = $value['sales_qty'];
				}
			}
		}
		
		$this->set(compact('selected_bonus', 'selected_set', 'selected_policy_type'));


		$this->set('page_title', 'Edit Memo');
		$this->Memo->id = $id;
		if (!$this->Memo->exists($id)) {
			throw new NotFoundException(__('Invalid Memo'));
		}
		/* -------- create individual Product data --------- */



		global $cart_data, $matched_array;
		$cart_data = array();
		$matched_array = array();
		$qty_session_data = array();

		/* ---------- start Set cart data as Session data ----------- */


		/* ---------creating prepare data---------- */
		$prepare_cart_data = array();
		if (!empty($filter_product['Product']['id'])) {
			$prepare_cart_data[$filter_product['Product']['id']]['Product'] = $filter_product['Product'];
			$prepare_cart_data[$filter_product['Product']['id']]['ProductPrice'] = $filter_product['ProductPrice'];
			foreach ($filter_product['Individual_slab'] as $individual_slab_val) {
				$prepare_cart_data[$filter_product['Product']['id']]['Individual_slab'][$individual_slab_val['min_qty']] = $individual_slab_val['price'];
			}
			if (!empty($filter_product['Combination'])) {
				$prepare_cart_data[$filter_product['Product']['id']]['Combination'] = $filter_product['Combination'];
			}
			if (!empty($filter_product['Combination_id'])) {
				/* ---- start ------- */
				$this->Combination->recursive = 2;
				$condition_value1['Combination.id'] = $filter_product['Combination_id']; //$filter_product['Combination']['id'];
				$combination_slab_data_option = array(
					'conditions' => array($condition_value1),
				);
				$combination_slab_data = $this->Combination->find('all', $combination_slab_data_option);
				/* ----- end -------- */

				$combination_slab = array();
				foreach ($combination_slab_data as $combine_group) {
					foreach ($combine_group['ProductCombination'] as $combine_val) {
						$combination_slab[$combine_val['min_qty']][$combine_val['product_id']] = $combine_val['Childrel']['price'];
					}
				}
				if (!empty($combination_slab)) {
					$prepare_cart_data[$filter_product['Product']['id']]['Combined_slab'] = $combination_slab;
				}
			}
		}
		/* ----------start create cart data and matched data ----------- */



		/* ------ unset cart data ------- */

		$user_office_id = $this->UserAuth->getOfficeId();

		/* ------ start code of sale type list ------ */
		$sale_type_list = array(
			1 => 'SO Sales',
			//2 => 'CSA Sales',
			3 => 'SPO Sales',
			4 => 'Program Sales'
		);
		/* ------ end code of sale type list ------ */
		/* ----- start code of product list ----- */
		//$product_list = $this->Product->find('list');
		/* ----- start code of product list ----- */
		/* ----- start code of sales person ----- */
		$user_office_id = $this->UserAuth->getOfficeId();
		/*$sales_person_list = $this->SalesPerson->find('list', array(
			'conditions' => array('SalesPerson.office_id' => $user_office_id, 'User.user_group_id' => 4)
			));*/
		$sales_person_list = $this->SalesPerson->find('list', array(
			'conditions' => array('SalesPerson.office_id' => $user_office_id)
		));
		/*echo "<pre>";
		  print_r($sales_person_list);
		  exit; */


		if ($this->request->is('post')) {
			$sale_type_id = $this->request->data['Memo']['sale_type_id'];

			//pr($this->request->data);
			//exit;

			$outlet_is_within_group = $this->outletGroupCheck($this->request->data['Memo']['outlet_id']);
			$product_is_injectable = $this->productInjectableCheck($this->request->data['MemoDetail']['product_id']);

			if ($product_is_injectable && !$outlet_is_within_group && $sale_type_id != 2) {
				$this->Session->setFlash(__("Special product can't sell to ordinary outlet!"), 'flash/error');
				$this->redirect(array('action' => 'create_memo'));
				//exit;
			}

			$memo_id = $id;

			$this->admin_delete($memo_id, 0);
			//echo 'done';exit;
			//echo $admin_delete ;exit;

			/*START ADD NEW*/
			//get office id
			$office_id = $this->request->data['Memo']['office_id'];

			//get thana id
			$this->loadModel('Market');
			$market_info = $this->Market->find('first', array(
				'conditions' => array('Market.id' => $this->request->data['Memo']['market_id']),
				'fields' => 'Market.thana_id',
				'order' => array('Market.id' => 'asc'),
				'recursive' => -1,
				//'limit' => 100
			));
			$thana_id = $market_info['Market']['thana_id'];
			/*END ADD NEW*/

			$sale_type_id = $this->request->data['Memo']['sale_type_id'];

			if ($sale_type_id == 1 || $sale_type_id == 3 || $sale_type_id == 4) {
				$this->request->data['Memo']['is_active'] = 1;
				//$this->request->data['Memo']['status'] = ($this->request->data['Memo']['credit_amount'] != 0) ? 1 : 2;

				if (array_key_exists('draft', $this->request->data)) {
					$this->request->data['Memo']['status'] = 0;
					$message = "Memo Has Been Saved as Draft";
				} else {
					$message = "Memo Has Been Saved";
					$this->request->data['Memo']['status'] = ($this->request->data['Memo']['credit_amount'] != 0) ? 1 : 2;
				}

				$sales_person = $this->SalesPerson->find('list', array(
					'conditions' => array('territory_id' => $this->request->data['Memo']['territory_id']),
					'order' => array('name' => 'asc')
				));

				$this->request->data['Memo']['sales_person_id'] = key($sales_person);

				$this->request->data['Memo']['entry_date'] = date('Y-m-d', strtotime($this->request->data['Memo']['entry_date']));
				$this->request->data['Memo']['memo_date'] = date('Y-m-d', strtotime($this->request->data['Memo']['memo_date']));

				/* If territory is Child/ has Parent then Save Parent Territory id in memo 
				and Child territory id in child_territory_id  Start*/
				$this->loadModel('Territory');
				$parent_territory_id = 0;
				$child_territory_id = 0;


				$terrtory_id_parent_id = $this->Territory->find('first', array(
					'fields' => array('Territory.parent_id', 'Territory.id'),
					'conditions' => array('Territory.id' => $this->request->data['Memo']['territory_id']),
					'recursive' => -1
				));

				// pr($terrtory_id_parent_id);exit;

				if ($terrtory_id_parent_id['Territory']['parent_id'] > 0) {
					/*Take child territory id and parent id*/
					$parent_territory_id = $terrtory_id_parent_id['Territory']['parent_id'];
					$child_territory_id = $terrtory_id_parent_id['Territory']['id'];
				} else {

					$parent_territory_id = $terrtory_id_parent_id['Territory']['id'];
					$child_territory_id = 0;
				}

				/* If territory is Child/ has Parent then Save Parent Territory id in memo 
				and Child territory id in child_territory_id  End*/

				$memoData['id'] = $memo_id;
				$memoData['office_id'] = $this->request->data['Memo']['office_id'];
				$memoData['sale_type_id'] = $this->request->data['Memo']['sale_type_id'];
				$memoData['territory_id'] = $parent_territory_id;
				$memoData['child_territory_id'] = $child_territory_id;
				$memoData['market_id'] = $this->request->data['Memo']['market_id'];
				$memoData['outlet_id'] = $this->request->data['Memo']['outlet_id'];
				$memoData['entry_date'] = $this->request->data['Memo']['entry_date'];
				$memoData['memo_date'] = $this->request->data['Memo']['memo_date'];
				$memoData['memo_no'] = $this->request->data['Memo']['memo_no'];
				$memoData['gross_value'] = $this->request->data['Memo']['gross_value'];
				
				//$this->request->data['Memo']['total_discount']
				
				$memoData['cash_recieved'] = $this->request->data['Memo']['cash_recieved'];
				$memoData['credit_amount'] = $this->request->data['Memo']['credit_amount'];
				$memoData['is_active'] = $this->request->data['Memo']['is_active'];
				$memoData['status'] = $this->request->data['Memo']['status'];
				//$memoData['memo_time'] = $this->current_datetime();
				$memoData['memo_time'] = $this->request->data['Memo']['entry_date'];
				$memoData['sales_person_id'] = $this->request->data['Memo']['sales_person_id'];
				$memoData['from_app'] = 0;
				$memoData['action'] = 1;

				$memoData['is_program'] = ($sale_type_id == 4) ? 1 : 0;


				$memoData['memo_reference_no'] = $this->request->data['Memo']['memo_reference_no'];


				$memoData['created_at'] = $this->current_datetime();
				$memoData['created_by'] = $this->UserAuth->getUserId();
				$memoData['updated_at'] = $this->current_datetime();
				$memoData['updated_by'] = $this->UserAuth->getUserId();


				$memoData['office_id'] = $office_id ? $office_id : 0;
				$memoData['thana_id'] = $thana_id ? $thana_id : 0;
				$memoData['total_discount'] = $this->request->data['Memo']['total_discount'];
				// pr($memoData);exit;
				$this->Memo->create();
				
				if ($this->Memo->save($memoData)) {
					
					/*Program officer update*/
					if (
						isset($this->request->data['program_officer_id'])
						&& !empty($this->request->data['program_officer_id'])
					) {
						if (
							!empty($this->request->data['exist_program_officer_id'])
							&& $this->request->data['exist_program_officer_id'] != $this->request->data['program_officer_id']
						) {
							/*Just update MemoProgramOfficer by memo_id*/
							$MemoProgramOfficerInfo = $this->MemoProgramOfficer->find('first', array(
								'fields'		=> array('MemoProgramOfficer.id'),
								'conditions'	=> array(
									'MemoProgramOfficer.memo_id' => $memo_id
								)
							));
							$this->MemoProgramOfficer->saveAll(
								array(
									'MemoProgramOfficer'	=> array(
										'id'					=> $MemoProgramOfficerInfo['MemoProgramOfficer']['id'],
										'program_officer_id'	=> $this->request->data['program_officer_id'],
										'updated_by' 			=> $this->UserAuth->getUserId(),
										'updated_at' 			=> $this->current_datetime()
									)

								)
							);
						} else {
							if (
								$this->request->data['exist_program_officer_id'] == NULL
								&& isset($this->request->data['program_officer_id'])
							) {
								/*insert new data*/
								$this->MemoProgramOfficer->save(
									array(
										'memo_id'				=> $memo_id,
										'program_officer_id'	=> $this->request->data['program_officer_id'],
										'created_by'			=> $this->UserAuth->getUserId(),
										'created_at'			=> $this->current_datetime()
									)
								);
							}
						}
					} else {
						/*Delete existing data by memo_id*/
						$this->MemoProgramOfficer->deleteAll(
							array(
								'MemoProgramOfficer.memo_id'	=> $memo_id
							),
							false
						);
					}
					//$this->dd($this->request->data);
					/*End Program officer update scope*/
					// EC Calculation
					if ($ec_calculation) {
						$this->ec_calculation($memoData['gross_value'], $memoData['outlet_id'], $memoData['territory_id'], $memoData['memo_date'], 1);
					}

					// OC Calculation
					if ($oc_calculation) {
						$this->oc_calculation($memoData['territory_id'], $memoData['gross_value'], $memoData['outlet_id'], $memoData['memo_date'], $memoData['memo_time'], 1);
					}

					$memo_id = $this->Memo->getLastInsertId();


					$memo_info_arr = $this->Memo->find('first', array(
						'conditions' => array(
							'Memo.id' => $memo_id
						)
					));

					$this->loadModel('Store');
					$store_id_arr = $this->Store->find('first', array(
						'conditions' => array(
							'Store.territory_id' => $memo_info_arr['Memo']['child_territory_id']>0?$memo_info_arr['Memo']['child_territory_id']:$memo_info_arr['Memo']['territory_id']
						)
					));

					$store_id = $store_id_arr['Store']['id'];


					if ($memo_id) {
						$all_product_id = $this->request->data['MemoDetail']['product_id'];
						if (!empty($this->request->data['MemoDetail'])) {
							$total_product_data = array();
							$memo_details = array();
							$memo_details['MemoDetail']['memo_id'] = $memo_id;

							foreach ($this->request->data['MemoDetail']['product_id'] as $key => $val) {
								if ($val == null || $this->request->data['MemoDetail']['sales_qty'][$key] <= 0) {
									continue;
								}
								
								
								
								$product_details = $this->Product->find('first', array(
									'fields' => array('id', 'is_virtual', 'parent_id'),
									'conditions' => array('Product.id' => $val),
									'recursive' => -1
								));
								if ($product_details['Product']['is_virtual'] == 1) {
									$product_id = $memo_details['MemoDetail']['virtual_product_id'] = $val;
									$memo_details['MemoDetail']['product_id'] = $product_details['Product']['parent_id'];
								} else {
									$memo_details['MemoDetail']['virtual_product_id'] = 0;
									$product_id = $memo_details['MemoDetail']['product_id'] = $product_details['Product']['id'];
								}
								$memo_details['MemoDetail']['measurement_unit_id'] = $this->request->data['MemoDetail']['measurement_unit_id'][$key];

								$sales_price =  $this->request->data['MemoDetail']['Price'][$key];
								$memo_details['MemoDetail']['price'] = $sales_price ;
								// $this->request->data['MemoDetail']['discount_amount'][$key];
								$memo_details['MemoDetail']['actual_price'] = $sales_price;
								$sales_qty = $memo_details['MemoDetail']['sales_qty'] = $this->request->data['MemoDetail']['sales_qty'][$key];

								$memo_details['MemoDetail']['product_price_id'] = $this->request->data['MemoDetail']['product_price_id'][$key];
								$memo_details['MemoDetail']['product_combination_id'] = $this->request->data['MemoDetail']['combination_id'][$key];
								$memo_details['MemoDetail']['bonus_qty'] = $this->request->data['MemoDetail']['bonus_product_qty'][$key];


								if ($this->request->data['MemoDetail']['bonus_product_id'][$key] != 0) {
									$memo_details['MemoDetail']['bonus_product_id'] = $this->request->data['MemoDetail']['bonus_product_id'][$key];
								} else {
									$memo_details['MemoDetail']['bonus_product_id'] = null;
								}


								//add new for policy
								$memo_details['MemoDetail']['discount_type'] = $this->request->data['MemoDetail']['disccount_type'][$key];
								$memo_details['MemoDetail']['discount_amount'] = $this->request->data['MemoDetail']['discount_amount'][$key];
								$memo_details['MemoDetail']['policy_type'] = $this->request->data['MemoDetail']['policy_type'][$key];
								$memo_details['MemoDetail']['policy_id'] = $this->request->data['MemoDetail']['policy_id'][$key];
								if ($this->request->data['MemoDetail']['is_bonus'][$key] == 3) {
									$memo_details['MemoDetail']['is_bonus'] = 3;
								}
								$selected_set = '';
								if (isset($this->request->data['MemoDetail']['selected_set'][$memo_details['MemoDetail']['policy_id']])) {
									$selected_set = $this->request->data['MemoDetail']['selected_set'][$memo_details['MemoDetail']['policy_id']];
								}
								$other_info = array();
								if ($selected_set) {
									$other_info = array(
										'selected_set' => $selected_set
									);
								}
								if ($other_info) {
									$memo_details['MemoDetail']['other_info'] = json_encode($other_info);
								}
								//Start for bonus
								$memo_date = date('Y-m-d', strtotime($this->request->data['Memo']['memo_date']));
								$bonus_product_id = $this->request->data['MemoDetail']['bonus_product_id'];
								$bonus_product_qty = $this->request->data['MemoDetail']['bonus_product_qty'];
								$memo_details['MemoDetail']['bonus_id'] = 0;
								$memo_details['MemoDetail']['bonus_scheme_id'] = 0;
								if ($bonus_product_qty[$key] > 0) {
									//echo $bonus_product_id[$key].'<br>';
									$b_product_id = $bonus_product_id[$key];
									$bonus_result = $this->bouns_and_scheme_id_set($b_product_id, $memo_date);
									$memo_details['MemoDetail']['bonus_id'] = $bonus_result['bonus_id'];
								}
								//End for bouns

								$total_product_data[] = $memo_details;
								
								if ($bonus_product_qty[$key] > 0) {
									if ($product_details['Product']['is_virtual'] == 1) {
										$memo_details_bonus['MemoDetail']['virtual_product_id'] = $val;
										$memo_details_bonus['MemoDetail']['product_id'] = $product_details['Product']['parent_id'];
									} else {
										$memo_details_bonus['MemoDetail']['virtual_product_id'] = 0;
										$memo_details_bonus['MemoDetail']['product_id'] = $product_details['Product']['id'];
									}
									$memo_details_bonus['MemoDetail']['memo_id'] = $memo_id;
									$memo_details_bonus['MemoDetail']['is_bonus'] = 1;
									$memo_details_bonus['MemoDetail']['measurement_unit_id'] = $this->request->data['MemoDetail']['measurement_unit_id'][$key];
									$memo_details_bonus['MemoDetail']['price'] = 0.0;
									$memo_details_bonus['MemoDetail']['sales_qty'] = $this->request->data['MemoDetail']['bonus_product_qty'][$key];
									$total_product_data[] = $memo_details_bonus;
									unset($memo_details_bonus);
									//update inventory
									if ($stock_hit) {
										$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
										$product_list = Set::extract($products, '{n}.Product');
										$bonus_qty = $this->request->data['MemoDetail']['bonus_product_qty'][$key];
										$punits_pre = $this->search_array($product_id, 'id', $product_list);
										if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
											$base_quantity = $bonus_qty;
										} else {
											$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $bonus_qty);
										}

										$update_type = 'deduct';
										$this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 11, $this->request->data['Memo']['memo_date']);
									}
								}


								//update inventory
								if ($stock_hit) {
									$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
									$product_list = Set::extract($products, '{n}.Product');

									/*$punits_pre = $this->search_array($product_id, 'id', $product_list);
								  if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
								  $base_quantity = $sales_qty;
								  } else {
								  $base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
									}*/

									$punits_pre = $this->search_array($product_id, 'id', $product_list);
									$measurement_unit_id = $this->request->data['MemoDetail']['measurement_unit_id'][$key];
									if ($measurement_unit_id > 0) {
										if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
											$base_quantity = $sales_qty;
										} else {
											$base_quantity = $this->unit_convert($product_id, $measurement_unit_id, $sales_qty);
										}
									} else {
										if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
											$base_quantity = $sales_qty;
										} else {
											$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
										}
									}

									$update_type = 'deduct';
									$this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 11, $this->request->data['Memo']['memo_date']);
								}



								// sales calculation
								$tt_price = $sales_qty * $sales_price;
								if ($sales_calculation) {
									$this->sales_calculation($memo_details['MemoDetail']['product_id'], $memoData['territory_id'], $memo_details['MemoDetail']['sales_qty'], $tt_price, $memoData['memo_date'], 1);
								}

								//stamp calculation
								if ($stamp_calculation) {
									$this->stamp_calculation($memoData['memo_no'], $memoData['territory_id'], $memo_details['MemoDetail']['product_id'], $memoData['outlet_id'], $memo_details['MemoDetail']['sales_qty'], $memoData['memo_date'], 1, $tt_price, $memoData['market_id']);
								}
							}

							$this->MemoDetail->saveAll($total_product_data);
						}
					}
				}
				$this->Session->setFlash(__('The Memo has been Updated'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}

			if ($sale_type_id == 2) {
				$this->loadModel('CsaMemo');
				$this->loadModel('CsaMemoDetail');

				$this->request->data['Memo']['is_active'] = 1;
				//$this->request->data['Memo']['status'] = ($this->request->data['Memo']['credit_amount'] != 0) ? 1 : 2;
				if (array_key_exists('draft', $this->request->data)) {
					$this->request->data['Memo']['status'] = 0;
					$message = "CSA Memo Has Been Saved as Draft";
				} else {
					$message = "CSA Memo Has Been Saved";
					$this->request->data['Memo']['status'] = ($this->request->data['Memo']['credit_amount'] != 0) ? 1 : 2;
				}

				$sales_person = $this->SalesPerson->find('list', array(
					'conditions' => array('territory_id' => $this->request->data['Memo']['territory_id']),
					'order' => array('name' => 'asc')
				));

				$this->request->data['Memo']['sales_person_id'] = key($sales_person);


				$this->CsaMemo->create();
				$this->request->data['Memo']['entry_date'] = date('Y-m-d', strtotime($this->request->data['Memo']['entry_date']));
				$this->request->data['Memo']['memo_date'] = date('Y-m-d', strtotime($this->request->data['Memo']['memo_date']));
				/*echo "<pre>";
				  print_r($this->request->data['Memo']);
				echo "</pre>";die();*/

				$memoData['office_id'] = $this->request->data['Memo']['office_id'];
				$memoData['sale_type_id'] = $this->request->data['Memo']['sale_type_id'];
				$memoData['territory_id'] = $this->request->data['Memo']['territory_id'];
				$memoData['market_id'] = $this->request->data['Memo']['market_id'];
				$memoData['outlet_id'] = $this->request->data['Memo']['outlet_id'];
				$memoData['entry_date'] = $this->request->data['Memo']['entry_date'];
				$memoData['memo_date'] = $this->request->data['Memo']['memo_date'];
				$memoData['csa_memo_no'] = $memo_no;
				$memoData['gross_value'] = $this->request->data['Memo']['gross_value'];
				$memoData['cash_recieved'] = $this->request->data['Memo']['cash_recieved'];
				$memoData['credit_amount'] = $this->request->data['Memo']['credit_amount'];
				$memoData['is_active'] = $this->request->data['Memo']['is_active'];
				$memoData['status'] = $this->request->data['Memo']['status'];
				//$memoData['memo_time'] = $this->current_datetime();
				$memoData['memo_time'] = $this->request->data['Memo']['entry_date'];
				$memoData['sales_person_id'] = $this->request->data['Memo']['sales_person_id'];
				$memoData['from_app'] = 0;
				$memoData['action'] = 1;

				$memoData['memo_reference_no'] = $this->request->data['Memo']['memo_reference_no'];


				$memoData['office_id'] = $office_id ? $office_id : 0;
				$memoData['thana_id'] = $thana_id ? $thana_id : 0;

				if ($this->CsaMemo->save($memoData)) {
					$csa_memo_id = $this->CsaMemo->getLastInsertId();

					if ($csa_memo_id) {
						if (!empty($this->request->data['MemoDetail'])) {
							$total_product_data = array();
							$memo_details = array();
							$memo_details['CsaMemoDetail']['csa_memo_id'] = $csa_memo_id;

							foreach ($this->request->data['MemoDetail']['product_id'] as $key => $val) {
								if ($val) {
									$product_details = $this->Product->find('first', array(
										'fields' => array('id', 'is_virtual', 'parent_id'),
										'conditions' => array('Product.id' => $val),
										'recursive' => -1
									));
									if ($product_details['Product']['is_virtual'] == 1) {
										$product_id = $memo_details['CsaMemoDetail']['virtual_product_id'] = $val;
										$memo_details['CsaMemoDetail']['product_id'] = $product_details['Product']['parent_id'];
									} else {
										$memo_details['CsaMemoDetail']['virtual_product_id'] = 0;
										$product_id = $memo_details['CsaMemoDetail']['product_id'] = $product_details['Product']['id'];
									}
									$memo_details['CsaMemoDetail']['csa_memo_id'] = $csa_memo_id;

									$memo_details['CsaMemoDetail']['measurement_unit_id'] = $this->request->data['MemoDetail']['measurement_unit_id'][$key];
									$memo_details['CsaMemoDetail']['price'] = $this->request->data['MemoDetail']['Price'][$key];
									$memo_details['CsaMemoDetail']['sales_qty'] = $this->request->data['MemoDetail']['sales_qty'][$key];
									$memo_details['CsaMemoDetail']['product_price_id'] = $this->request->data['MemoDetail']['product_price_id'][$key];
									$memo_details['CsaMemoDetail']['bonus_qty'] = $this->request->data['MemoDetail']['bonus_product_qty'][$key];
									if ($this->request->data['MemoDetail']['bonus_product_id'][$key] != 0) {
										$memo_details['CsaMemoDetail']['bonus_product_id'] = $this->request->data['MemoDetail']['bonus_product_id'][$key];
									} else {
										$memo_details['CsaMemoDetail']['bonus_product_id'] = null;
									}

									//Start for bonus
									$memo_date = date('Y-m-d', strtotime($this->request->data['Memo']['memo_date']));
									$bonus_product_id = $this->request->data['MemoDetail']['bonus_product_id'];
									$bonus_product_qty = $this->request->data['MemoDetail']['bonus_product_qty'];
									$memo_details['MemoDetail']['bonus_id'] = 0;
									$memo_details['MemoDetail']['bonus_scheme_id'] = 0;
									if ($bonus_product_qty[$key] > 0) {
										//echo $bonus_product_id[$key].'<br>';
										$b_product_id = $bonus_product_id[$key];
										$bonus_result = $this->bouns_and_scheme_id_set($b_product_id, $memo_date);
										$memo_details['MemoDetail']['bonus_id'] = $bonus_result['bonus_id'];
									}
									//End for bouns

									$total_product_data[] = $memo_details;
									if ($bonus_product_qty[$key] > 0) {
										if ($product_details['Product']['is_virtual'] == 1) {
											$memo_details_bonus['CsaMemoDetail']['virtual_product_id'] = $val;
											$memo_details_bonus['CsaMemoDetail']['product_id'] = $product_details['Product']['parent_id'];
										} else {
											$memo_details_bonus['CsaMemoDetail']['virtual_product_id'] = 0;
											$memo_details_bonus['CsaMemoDetail']['product_id'] = $product_details['Product']['id'];
										}
										$memo_details_bonus['CsaMemoDetail']['csa_memo_id'] = $csa_memo_id;
										$memo_details_bonus['CsaMemoDetail']['measurement_unit_id'] = $this->request->data['MemoDetail']['measurement_unit_id'][$key];
										$memo_details_bonus['CsaMemoDetail']['price'] = 0.0;
										$memo_details_bonus['CsaMemoDetail']['sales_qty'] = $this->request->data['MemoDetail']['bonus_product_qty'][$key];
										$memo_details_bonus['CsaMemoDetail']['is_bonus'] = 1;
										$total_product_data[] = $memo_details_bonus;
										unset($memo_details_bonus);
									}
								}
							}

							$this->CsaMemoDetail->saveAll($total_product_data);
						}
					}
				}

				$this->Session->setFlash(__('The Memo has been Updated'), 'flash/success');
				$this->redirect(array("controller" => "CsaMemos", 'action' => 'index'));
				//exit;
			}
		}

		$this->loadModel('ProductMeasurement');
		$product_measurement_units = $this->ProductMeasurement->find('list', array('fields' => array('product_id', 'measurement_unit_id')));
		$product_category_id_list = $this->Product->find('list', array('fields' => array('id', 'product_category_id')));
		
		//echo '<pre>';print_r($existing_record);exit;
		
		$this->set(compact('offices', 'territories', 'markets', 'outlets', 'current_date', 'product_list', 'sale_type_list', 'existing_record', 'office_parent_id', 'product_measurement_units', 'product_category_id_list', 'open_bonus_product_option'));


		/*$this->set(compact('offices', 'territories', 'product_list', 'markets', 'outlets', 'current_date', 'sale_type_list', 'office_parent_id', 'sales_person'));*/
	}


	//for bonus and bouns schema
	public function bouns_and_scheme_id_set($b_product_id = 0, $memo_date = '')
	{
		$this->loadModel('Bonus');
		//$this->loadModel('OpenCombination');
		//$this->loadModel('OpenCombinationProduct');

		$bonus_result = array();

		$b_product_qty = 0;
		$bonus_id = 0;
		$bonus_scheme_id = 0;

		$bonus_info = $this->Bonus->find('first', array(
			'conditions' => array(
				'Bonus.effective_date <= ' => $memo_date,
				'Bonus.end_date >= ' => $memo_date,
				'Bonus.bonus_product_id' => $b_product_id
			),
			'recursive' => -1,
		));

		//pr($bonus_info);

		if ($bonus_info) {
			$bonus_table_id = $bonus_info['Bonus']['id'];
			$mother_product_id = $bonus_info['Bonus']['mother_product_id'];
			$mother_product_quantity = $bonus_info['Bonus']['mother_product_quantity'];

			$bonus_id = $bonus_table_id;

			//echo $bonus_id;
			//break;
		}


		/*echo 'Bonus = '.$bonus_id;
		echo '<br>';
		echo 'Bonus Scheme = '. $bonus_scheme_id;
		echo '<br>';
		echo '<br>';
		echo '<br>';*/

		$bonus_result['bonus_id'] = $bonus_id;
		$bonus_result['bonus_scheme_id'] = $bonus_scheme_id;

		return $bonus_result;
	}


	/* ----- ajax methods ----- */

	public function market_list()
	{
		$rs = array(array('id' => '', 'name' => '---- Select Market -----'));
		$thana_id = $this->request->data['thana_id'];
		//$thana_id = 2;
		$market_list = $this->Market->find('all', array(
			'conditions' => array('Market.thana_id' => $thana_id),
			'order' => array('Market.name' => 'asc')
		));
		$data_array = Set::extract($market_list, '{n}.Market');
		if (!empty($data_array)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}


	public function get_sales_officer_list()
	{
		$user_office_id = $this->UserAuth->getOfficeId();
		//$user_office_id = 2;
		$rs = array(array('id' => '', 'name' => '---- Select Market -----'));
		$sale_type_id = $this->request->data['sale_type_id'];
		//$sale_type_id = 1;
		if ($sale_type_id == 1 || $sale_type_id == 2 || $sale_type_id == 3 || $sale_type_id == 4) {
			$so_list = $this->SalesPerson->find('all', array(
				'conditions' => array('SalesPerson.office_id' => $user_office_id, 'User.user_group_id' => 4),
				'fields' => array('SalesPerson.id', 'SalesPerson.name')
			));
			$person_list = array();
			foreach ($so_list as $key => $val) {
				$list['id'] = $val['SalesPerson']['id'];
				$list['name'] = $val['SalesPerson']['name'];
				$person_list[] = $list;
			}
		}
		if (!empty($person_list)) {
			echo json_encode(array_merge($rs, $person_list));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}

	public function get_outlet_list()
	{
		$rs = array(array('id' => '', 'name' => '---- Select outlet -----'));
		$market_id = $this->request->data['market_id'];
		$outlet_list = $this->Outlet->find('all', array(
			'conditions' => array('Outlet.market_id' => $market_id),
			'order' => array('Outlet.name' => 'asc')
		));
		$data_array = Set::extract($outlet_list, '{n}.Outlet');

		if (!empty($data_array)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}

	public function get_product_unit()
	{
		$current_date = date('Y-m-d', strtotime($this->request->data['memo_date']));
		$territory_id = $this->request->data['territory_id'];
		$outlet_id = $this->request->data['outlet_id'];

		$this->loadModel('Store');
		$this->loadModel('Outlet');
		$this->Store->recursive = -1;
		$store_info = $this->Store->find('first', array(
			'conditions' => array('territory_id' => $territory_id)
		));

		$outlet_info = $this->Outlet->find('first', array(
			'conditions' => array(
				'Outlet.id' => $this->request->data['outlet_id']
			),
			'recursive' => -1
		));

		$store_id = $store_info['Store']['id'];
		$product_id = $this->request->data['product_id'];


		$this->loadModel('Product');
		$this->Product->recursive = -1;
		$product_category_arr = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $product_id),
			'fields' => array('product_category_id', 'sales_measurement_unit_id', 'product_type_id')
		));
		// pr($product_category_arr);exit;
		$product_category_id = $product_category_arr['Product']['product_category_id'];
		$product_type_id = $product_category_arr['Product']['product_type_id'];
		if ($product_type_id == 3) {
			$product_category_id = 32;
		}
		$measurement_unit_id = $product_category_arr['Product']['sales_measurement_unit_id'];

		$this->loadModel('CurrentInventory');
		$this->CurrentInventory->recursive = -1;
		$total_qty_arr = $this->CurrentInventory->find('all', array(
			'conditions' => array('store_id' => $store_id, 'product_id' => $product_id),
			'fields' => array('sum(qty) as total')
		));
		$total_qty = $total_qty_arr[0][0]['total'];
		$sales_total_qty = $this->unit_convertfrombase($this->request->data['product_id'], $measurement_unit_id, $total_qty);
		$this->loadModel('Product');
		$this->Product->recursive = -1;
		$product_array = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $product_id),
			'joins' => array(
				array(
					'table' => 'measurement_units',
					'alias' => 'MU',
					'conditions' => 'MU.id=Product.sales_measurement_unit_id'
				)
			),
			'fields' => array('sales_measurement_unit_id', 'MU.name')
		));
		$measurement_unit_id = $product_array['Product']['sales_measurement_unit_id'];
		$measurement_unit_name = $product_array['MU']['name'];

		$sales_total_qty = $this->unit_convertfrombase($this->request->data['product_id'], $measurement_unit_id, $total_qty);

		$data_array['product_unit']['name'] = $measurement_unit_name;
		$data_array['product_unit']['id'] = $measurement_unit_id;

		if (!empty($sales_total_qty)) {
			$data_array['total_qty'] = $sales_total_qty;
		} else {
			$data_array['total_qty'] = '';
		}
		echo json_encode($data_array);
		$this->autoRender = false;
	}

	/* ------- set_combind_or_individual_price --------- */

	public function get_combine_or_individual_price()
	{

		$product_items_id = explode(',', rtrim($this->request->data['product_id_list'], ","));

		/* ---- read session data ----- */
		$cart_data = $this->Session->read('cart_session_data');
		$matched_data = $this->Session->read('matched_session_data');

		/*  echo "<pre>";
		  echo "Cart data ----------------";
		  print_r($cart_data);
		  echo "Cart data ----------------";
		  print_r($matched_data); exit;*/


		/* ---- read session data ----- */
		$combined_product = $this->request->data['combined_product'];
		$min_qty = $this->request->data['min_qty'];
		$product_id = $this->request->data['product_id'];
		$memo_date = date('Y-m-d', strtotime($this->request->data['memo_date']));
		/*---------Bonus-----------*/
		/*$this->loadModel('Bonus');
		$this->Bonus->recursive = -1;
		$bonus_info = $this->Bonus->find('first',array(
			'conditions'=>array('mother_product_id'=>$product_id, 'mother_product_quantity'=>$min_qty),
			'fields'=>array('mother_product_quantity', 'bonus_product_id', 'bonus_product_quantity')
		));

		if (count($bonus_info) != 0) {
			$bonus_product_id = $bonus_info['Bonus']['bonus_product_id'];
			$this->loadModel('Product');
			$this->Product->recursive = -1;
			$bonus_product_name = $this->Product->find('first',array(
				'conditions'=>array('id'=>$bonus_product_id),
				'fields'=>array('name', 'sales_measurement_unit_id')
			));
			$result_data['mother_product_quantity'] = $bonus_info['Bonus']['mother_product_quantity'];
			$result_data['bonus_product_id'] = $bonus_product_id;
			$result_data['bonus_product_name'] = $bonus_product_name['Product']['name'];
			$result_data['bonus_measurement_unit_id'] = $bonus_product_name['Product']['sales_measurement_unit_id'];
			$result_data['bonus_product_qty'] = $bonus_info['Bonus']['bonus_product_quantity'];
		}*/

		/*---------Bonus-----------*/
		$this->loadModel('Bonus');
		$this->loadModel('Product');

		$this->Bonus->recursive = -1;
		$bonus_info = $this->Bonus->find('all', array(
			'conditions' => array(
				'mother_product_id' => $product_id,
				'effective_date <=' => $memo_date,
				'end_date >=' => $memo_date,
			),
			'fields' => array('mother_product_quantity', 'bonus_product_id', 'bonus_product_quantity')
		));

		if (!empty($bonus_info[0]['Bonus']['mother_product_quantity'])) {
			$mother_product_quantity_bonus = $bonus_info[0]['Bonus']['mother_product_quantity'];
			$result_data['mother_product_quantity_bonus'] = $mother_product_quantity_bonus;
		}

		$no_of_bonus_slap = count($bonus_info);

		if ($no_of_bonus_slap != 0) {
			for ($slap_count = 0; $slap_count < $no_of_bonus_slap; $slap_count++) {
				$bonus_slap['bonus_product_id'][$slap_count] = $bonus_info[$slap_count]['Bonus']['bonus_product_id'];

				$this->Product->recursive = -1;
				$bonus_product_name = $this->Product->find('first', array(
					'conditions' => array('id' => $bonus_slap['bonus_product_id'][$slap_count]),
					'fields' => array('name', 'sales_measurement_unit_id')
				));
				$quantity_slap['mother_product_quantity'][$slap_count] = $bonus_info[$slap_count]['Bonus']['mother_product_quantity'];

				$result_data['bonus_product_id'][$slap_count] = $bonus_info[$slap_count]['Bonus']['bonus_product_id'];
				$result_data['bonus_product_name'][$slap_count] = $bonus_product_name['Product']['name'];
				$result_data['sales_measurement_unit_id'][$slap_count] = $bonus_product_name['Product']['sales_measurement_unit_id'];
				$result_data['bonus_product_quantity'][$slap_count] = $bonus_info[$slap_count]['Bonus']['bonus_product_quantity'];
			}
			for ($i = 0; $i < count($quantity_slap['mother_product_quantity']); $i++) {
				$result_data['mother_product_quantity'][] = array(
					'min' => $i == 0 ? 0 : $quantity_slap['mother_product_quantity'][$i - 1],
					'max' => $quantity_slap['mother_product_quantity'][$i] - 1
				);
			}
		}

		global $qty_data;
		$qty_data = array();

		function build_inputed_qty_data($product_id = null, $min_qty = null, &$qty_data = array())
		{
			if (!array_key_exists($product_id, $qty_data)) {
				$qty_data[$product_id] = $min_qty;
			} else {
				$qty_data[$product_id] = $min_qty;
			}
		}

		if (!empty($combined_product)) {
			if ($this->Session->read('combintaion_qty_data') == null) {
				$qty_data = array();
			} else {
				$qty_data = $this->Session->read('combintaion_qty_data');
				/*echo '<pre>';
				pr($qty_data);exit;*/
			}
			build_inputed_qty_data($product_id, $min_qty, $qty_data);
			$this->Session->write('combintaion_qty_data', $qty_data);
		}
		/* echo "<br/>";
		  echo "Cart data ----------------";
		  print_r($matched_data);
		  exit; */
		$current_qty = $this->Session->read('combintaion_qty_data');
		/* echo "<br/>";
		  echo "Cart data ----------------";
		  print_r($current_qty); */
		if (!empty($current_qty)) {
			$prev_data = array();
			foreach ($current_qty as $q_key => $q_val) {
				$prev_data[] = $q_key;
			}
			$diff_product_id = array_diff($prev_data, $product_items_id);
			if (!empty($diff_product_id)) {
				foreach ($diff_product_id as $key => $val) {
					unset($current_qty[$val]);
				}
				$this->Session->write('combintaion_qty_data', $current_qty);
			}
		}
		if ($combined_product) {
			foreach ($matched_data as $combined_product_key => $combined_product_val) {
				if ($combined_product_key == $combined_product) {
					/*if ($combined_product_val['is_matched_yet'] == 'NO') {
						foreach ($cart_data as $no_com_key => $no_com_val) {
							if ($no_com_key == $product_id) {
								$less_qty_array = array();
								foreach ($no_com_val['individual_slab'] as $in_slab_qty => $in_slab_val) {
									if ($min_qty >= $in_slab_qty) {
										$less_qty_array[$in_slab_qty] = $in_slab_val;
									}
								}
								ksort($less_qty_array);
								$unit_rate = array_pop($less_qty_array);

								if (empty($unit_rate)) {
									$unit_rate = $no_com_val['product_price']['general_price'];
								}
								if ($unit_rate) {
									$result_data ['unit_rate'] = sprintf("%1\$.6f", $unit_rate);
									$result_data ['total_value'] = sprintf("%1\$.6f", $unit_rate) * $min_qty;
								} else {
									$result_data ['unit_rate'] = '';
									$result_data ['total_value'] = '';
								}
							}
						}
					}*/

					if ($combined_product_val['is_matched_yet'] == 'NO' || $combined_product_val['is_matched_yet'] == 'YES') {
						$current_qty = $this->Session->read('combintaion_qty_data');
						/* echo "<pre>";
						  print_r($current_qty);
						  print_r($cart_data);
						  exit; */
						foreach ($cart_data as $no_com_key => $no_com_val) {
							$combined_product = explode(',', $no_com_val['combined_product']);
							$combined_inputed_val = 0;
							foreach ($combined_product as $qty_key => $qty_val) {
								/* if($qty_val == $no_com_key){
								  $combined_inputed_val = $combined_inputed_val + $current_qty[$qty_val];
							  } */
								if (array_key_exists($qty_val, $current_qty)) {
									$combined_inputed_val = $combined_inputed_val + $current_qty[$qty_val];
								}
							}
							if ($no_com_key == $product_id) {
								//echo $no_com_key." == ".$product_id."<br/>";
								$less_qty_data = array();
								foreach ($no_com_val['combined_slab'] as $in_slab_qty => $in_slab_val) {
									if ($combined_inputed_val >= $in_slab_qty) {
										$less_qty_data[$in_slab_qty] = $in_slab_val;
									}
								}
								ksort($less_qty_data);
								$actual_data = array_pop($less_qty_data);
								$combined_less_qty = array();
								if (is_array($actual_data) && is_array($current_qty)) {
									$combined_common_product = array_intersect_key($actual_data, $current_qty);
								}
								if (!empty($actual_data[$product_id]) && count($combined_common_product) > 1) {
									foreach ($actual_data as $ac_key => $ac_val) {
										$combined_less_qty[$ac_key]['unit_rate'] = sprintf("%1\$.6f", $ac_val);
										if (!empty($current_qty[$ac_key])) {
											$combined_less_qty[$ac_key]['total_value'] = $current_qty[$ac_key] * sprintf("%1\$.6f", $ac_val);
										}
									}
								} else {
									/* --------------------------------- */
									/* =============================================== */
									$individual_less_qty = array();
									foreach ($combined_product as $combined_key => $combined_val) {
										if (array_key_exists($combined_val, $cart_data)) {
											$individual_less_qty_unique = array();
											foreach ($cart_data[$combined_val]['individual_slab'] as $in_slab_qty => $in_slab_val) {
												if ($current_qty[$combined_val] >= $in_slab_qty) {
													$individual_less_qty_unique[$in_slab_qty] = $in_slab_val;
												}
											}
											ksort($individual_less_qty_unique);
											$individual_actual_data = array_pop($individual_less_qty_unique);
											if (empty($individual_actual_data)) {
												$individual_actual_data = $cart_data[$combined_val]['product_price']['general_price'];
												/*  echo "<pre>";
												  echo "dsfsdf";
												  print_r($individual_actual_data);
												  print_r($cart_data[$combined_val]); */
											}
											$individual_less_qty[$combined_val]['unit_rate'] = sprintf("%1\$.6f", $individual_actual_data);
											$individual_less_qty[$combined_val]['total_value'] = sprintf("%1\$.6f", $individual_actual_data) * $current_qty[$combined_val];
										}
									}
									//exit;
									/* --------------------------------- */
								}
							}
						}
					}
				}
			}
		} else {
			foreach ($cart_data as $no_com_key => $no_com_val) {
				if ($product_id == $no_com_key) {
					$less_qty_val = array();
					foreach ($no_com_val['individual_slab'] as $in_slab_key => $in_slab_val) {
						if ($min_qty >= $in_slab_key) {
							$less_qty_val[$in_slab_key] = $in_slab_val;
						}
					}
					ksort($less_qty_val);
					$unit_rate = array_pop($less_qty_val);
					if (empty($unit_rate)) {
						$unit_rate = $no_com_val['product_price']['general_price'];
					}
					if ($unit_rate) {
						$result_data['unit_rate'] = sprintf("%1\$.6f", $unit_rate);
						$result_data['total_value'] = $unit_rate * sprintf("%1\$.6f", $min_qty);
					} else {
						$result_data['unit_rate'] = '';
						$result_data['total_value'] = '';
					}
				}
			}
		}
		if (!empty($result_data)) {
			echo json_encode($result_data);
		} elseif (!empty($individual_less_qty)) {
			echo json_encode($individual_less_qty);
		} elseif (!empty($combined_less_qty)) {
			echo json_encode($combined_less_qty);
		}
		$this->autoRender = false;
	}


	public function delete_memo()
	{
		/* -------- Start Session data --------- */
		$cart_data = $this->Session->read('cart_session_data');
		$matched_data = $this->Session->read('matched_session_data');
		$current_qty = $this->Session->read('combintaion_qty_data');
		/* -------- End Session data --------- */
		$product_id = $this->request->data['product_id'];
		$combined_product = $this->request->data['combined_product'];
		if (!empty($product_id)) {
			unset($cart_data[$product_id]);
			$this->Session->write('cart_session_data', $cart_data);

			if (!empty($combined_product)) {
				if ($matched_data[$combined_product]['matched_count_so_far'] == 1) {
					unset($matched_data[$combined_product]);
					$this->Session->write('matched_session_data', $matched_data);
				} else {
					$matched_id_so_far = rtrim($matched_data[$combined_product]['matched_id_so_far'], ',' . $product_id);
					$matched_count_so_far = $matched_data[$combined_product]['matched_count_so_far'] - 1;
					$matched_data[$combined_product]['is_matched_yet'] = 'NO';
					$matched_data[$combined_product]['matched_count_so_far'] = $matched_count_so_far;
					$matched_data[$combined_product]['matched_id_so_far'] = $matched_id_so_far;
					$this->Session->write('matched_session_data', $matched_data);
				}
			}
			if (!empty($current_qty)) {
				unset($current_qty[$product_id]);
				$this->Session->write('combintaion_qty_data', $current_qty);
			}
			echo 'yes';
		}

		$this->autoRender = false;
	}


	public function get_territory_id()
	{
		$sales_person_id = $this->request->data['sales_person_id'];
		//$sales_person_id = 2;
		$this->SalesPerson->recursive = 0;
		$territory_id = $this->SalesPerson->find('all', array(
			'conditions' => array('SalesPerson.id' => $sales_person_id),
			'fields' => array('SalesPerson.territory_id')
		));

		if ($territory_id) {
			$response['territory_id'] = $territory_id[0]['SalesPerson']['territory_id'];
		} else {
			$response['territory_id'] = '';
		}

		if ($response) {
			echo json_encode($response);
		}
		$this->autoRender = false;
	}

	public function admin_memo_map()
	{

		$this->set('page_title', 'Memo List on Map');
		$message = '';
		$map_data = array();
		$conditions = array();
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			//$conditions[] = array();
			$office_conditions = array();
		} else {
			$conditions[] = array('Territory.office_id' => $this->UserAuth->getOfficeId());
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}
		
		$this->loadModel('Territory');
		
		
			
			//echo '<pre>';print_r($territoryInfo);exit;

		// Custome Search
		if (($this->request->is('post') || $this->request->is('put'))) {
			if ($this->request->data['Memo']['office_id'] != '') {
				$conditions[] = array('Territory.office_id' => $this->request->data['Memo']['office_id']);
			}
			if ($this->request->data['Memo']['territory_id'] != '') {
				
				$territoryInfo = $this->Territory->find('first', array(
					'conditions' => array('Territory.id'=>$this->request->data['Memo']['territory_id']),
					'recursive' => -1
				));
				
				$parentid = $territoryInfo['Territory']['parent_id'];
				
				if( $parentid > 0){
					$conditions[] = array('Memo.child_territory_id' => $this->request->data['Memo']['territory_id']);
				}else{
					$conditions[] = array('Memo.territory_id' => $this->request->data['Memo']['territory_id']);
				}
				
				
			}
			if ($this->request->data['Memo']['date_from'] != '') {
				$conditions[] = array('Memo.memo_date >=' => Date('Y-m-d', strtotime($this->request->data['Memo']['date_from'])));
			}
			if ($this->request->data['Memo']['date_to'] != '') {
				$conditions[] = array('Memo.memo_date <=' => Date('Y-m-d', strtotime($this->request->data['Memo']['date_to'])));
			}
			
			

			$this->Memo->recursive = 0;
			$memo_list = $this->Memo->find('all', array(
				'conditions' => $conditions,
				'order' => array('Memo.id' => 'desc'),
				'recursive' => 0
			));
			
			///echo '<pre>';print_r($memo_list);exit;

			if (!empty($memo_list)) {
				foreach ($memo_list as $val) {
					if ($val['Memo']['latitude'] > 0 and $val['Memo']['longitude'] > 0) {
						$data['title'] = $val['Outlet']['name'];
						$data['lng'] = $val['Memo']['longitude'];
						$data['lat'] = $val['Memo']['latitude'];
						$data['description'] = '<p><b>Outlet : ' . $val['Outlet']['name'] . '</b></br>' .
							'Market : </b>' . $val['Market']['name'] . '</br>' .
							'Territory : </b>' . $val['Territory']['name'] . '</p>' .
							'<p>Memo No. : ' . $val['Memo']['memo_no'] . '</br>' .
							'Memo Date : ' . date('d-M-Y', strtotime($val['Memo']['memo_date'])) . '</br>' .
							'Memo Amount : ' . sprintf('%.2f', $val['Memo']['gross_value']) . '</p>' .
							'<a class="btn btn-primary btn-xs" href="' . BASE_URL . 'admin/memos/view/' . $val['Memo']['id'] . '" target="_blank">Memo Details</a>';
						$map_data[] = $data;
					}
				}
			}
			if (!empty($map_data)) {
				$message = '';
			} else {
				$message = '<div class="alert alert-danger">No memo found.</div>';
			}
		}
		
		

		$this->loadModel('Office');
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		$office_id = (isset($this->request->data['Memo']['office_id']) ? $this->request->data['Memo']['office_id'] : 0);
		$territories = $this->Memo->Territory->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));
		$this->set(compact('offices', 'territories', 'map_data', 'message'));
	}
	public function admin_memo_map_by_outlet()
	{

		$this->set('page_title', 'Memo List on Map By outlet id');
		$message = '';
		$map_data = array();
		$conditions = array();
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$conditions[] = array();
			$office_conditions = array();
		} else {
			$conditions[] = array('Territory.office_id' => $this->UserAuth->getOfficeId());
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}

		// Custome Search
		if (($this->request->is('post') || $this->request->is('put'))) {
			if ($this->request->data['Memo']['office_id'] != '') {
				$conditions[] = array('Territory.office_id' => $this->request->data['Memo']['office_id']);
			}
			if ($this->request->data['Memo']['territory_id'] != '') {
				$conditions[] = array('Memo.territory_id' => $this->request->data['Memo']['territory_id']);
			}
			if ($this->request->data['Memo']['date_from'] != '') {
				$conditions[] = array('Memo.memo_date >=' => Date('Y-m-d', strtotime($this->request->data['Memo']['date_from'])));
			}
			if ($this->request->data['Memo']['date_to'] != '') {
				$conditions[] = array('Memo.memo_date <=' => Date('Y-m-d', strtotime($this->request->data['Memo']['date_to'])));
			}
			if ($this->request->data['Memo']['outlet_id'] != '') {
				$conditions[] = array('Memo.outlet_id' => $this->request->data['Memo']['outlet_id']);
			}

			$this->Memo->recursive = 0;
			$memo_list = $this->Memo->find('all', array(
				'conditions' => $conditions,
				'order' => array('Memo.id' => 'desc'),
				'recursive' => 0
			));

			$outlet_info = $this->Outlet->find('first', array(
				'conditions' => array('Outlet.id' => $this->request->data['Memo']['outlet_id']),
				'recursive' => -1
			));

			if (!empty($memo_list)) {
				foreach ($memo_list as $val) {
					if ($val['Memo']['latitude'] > 0 and $val['Memo']['longitude'] > 0) {
						$data['title'] = $val['Outlet']['name'];
						$data['lng'] = $val['Memo']['longitude'];
						$data['lat'] = $val['Memo']['latitude'];
						$data['description'] = '<p><b>Outlet : ' . $val['Outlet']['name'] . '</b></br>' .
							'Market : </b>' . $val['Market']['name'] . '</br>' .
							'Territory : </b>' . $val['Territory']['name'] . '</p>' .
							'<p>Memo No. : ' . $val['Memo']['memo_no'] . '</br>' .
							'Memo Time : ' . date('d-M-Y h:i a', strtotime($val['Memo']['memo_time'])) . '</br>' .
							'Memo Amount : ' . sprintf('%.2f', $val['Memo']['gross_value']) . '</p>' .
							'<a class="btn btn-primary btn-xs" href="' . BASE_URL . 'admin/memos/view/' . $val['Memo']['id'] . '" target="_blank">Memo Details</a>';
						$map_data[] = $data;
					}
				}
			}
			if (!empty($map_data)) {
				$message = '';
			} else {
				$message = '<div class="alert alert-danger">No memo found.</div>';
			}
		}
		$this->set('office_id', $this->UserAuth->getOfficeId());
		$this->loadModel('Office');
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		$office_id = isset($this->request->data['Memo']['office_id']) != '' ? $this->request->data['Memo']['office_id'] : 0;
		$territory_id = isset($this->request->data['Memo']['territory_id']) != '' ? $this->request->data['Memo']['territory_id'] : 0;
		$market_id = isset($this->request->data['Memo']['market_id']) != '' ? $this->request->data['Memo']['market_id'] : 0;

		$this->loadModel('Territory');
		$territory = $this->Territory->find('all', array(
			'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
			'conditions' => array('Territory.office_id' => $office_id),
			'order' => array('Territory.name' => 'asc'),
			'recursive' => 0
		));

		$data_array = array();

		foreach ($territory as $key => $value) {
			$t_id = $value['Territory']['id'];
			$t_val = $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')';
			$data_array[$t_id] = $t_val;
		}

		$territories = $data_array;

		/*
		$territories = $this->Memo->Territory->find('list', array(
			'conditions' => array('Territory.office_id' => $office_id),
			'order' => array('Territory.name' => 'asc')
			));
		*/

		if ($territory_id) {
			$markets = $this->Memo->Market->find('list', array(
				'conditions' => array('Market.territory_id' => $territory_id),
				'order' => array('Market.name' => 'asc')
			));
		} else {
			$markets = array();
		}

		$outlets = $this->Memo->Outlet->find('list', array(
			'conditions' => array('Outlet.market_id' => $market_id),
			'order' => array('Outlet.name' => 'asc')
		));
		$current_date = date('d-m-Y', strtotime($this->current_date()));
		$this->set(compact('offices', 'territories', 'markets', 'outlets', 'current_date', 'requested_data', 'product_name'));
		$this->set(compact('map_data', 'message', 'outlet_info'));
	}
	public function update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct', $transaction_type_id = 0, $transaction_date = '')
	{

		$this->loadModel('CurrentInventory');

		$find_type = 'all';
		if ($update_type == 'add') {
			$find_type = 'first';
		}

		$inventory_info = $this->CurrentInventory->find($find_type, array(
			'conditions' => array(
				//'CurrentInventory.qty >=' => 0,
				'CurrentInventory.store_id' => $store_id,
				'CurrentInventory.inventory_status_id' => 1,
				'CurrentInventory.product_id' => $product_id
			),
			'order' => array('CurrentInventory.expire_date' => 'asc'),
			'recursive' => -1
		));




		if ($update_type == 'deduct') {
			foreach ($inventory_info as $val) {
				if ($quantity <= $val['CurrentInventory']['qty']) {
					$this->CurrentInventory->id = $val['CurrentInventory']['id'];
					$this->CurrentInventory->updateAll(
						array(
							'CurrentInventory.qty' => 'CurrentInventory.qty - ' . $quantity,
							'CurrentInventory.transaction_type_id' => $transaction_type_id,
							'CurrentInventory.transaction_date' => "'" . $transaction_date . "'",
							'CurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
						),
						array('CurrentInventory.id' => $val['CurrentInventory']['id'])
					);
					break;
				} else {
					$quantity = $quantity - $val['CurrentInventory']['qty'];

					if ($val['CurrentInventory']['qty'] > 0) {
						$this->CurrentInventory->id = $val['CurrentInventory']['id'];
						$this->CurrentInventory->updateAll(
							array(
								'CurrentInventory.qty' => 'CurrentInventory.qty - ' . $val['CurrentInventory']['qty'],
								'CurrentInventory.transaction_type_id' => $transaction_type_id,
								'CurrentInventory.transaction_date' => "'" . $transaction_date . "'",
								'CurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
							),
							array('CurrentInventory.id' => $val['CurrentInventory']['id'])
						);
					}
				}
			}
		} else {
			/* $this->CurrentInventory->updateAll(array('CurrentInventory.qty' => 'CurrentInventory.qty + '.$inventory_info['CurrentInventory']['qty']),array('CurrentInventory.id' => $inventory_info['CurrentInventory']['id'])); */
			if (!empty($inventory_info)) {
				$this->CurrentInventory->updateAll(
					array('CurrentInventory.qty' => 'CurrentInventory.qty + ' . $quantity, 'CurrentInventory.transaction_type_id' => $transaction_type_id, 'CurrentInventory.store_id' => $store_id, 'CurrentInventory.transaction_date' => "'" . $transaction_date . "'"),
					array('CurrentInventory.id' => $inventory_info['CurrentInventory']['id'])
				);
			}
		}

		return true;
	}
	// it will be called from memo not from memo_details
	// cal_type=1 means increment and 2 means deduction

	public function ec_calculation($gross_value, $outlet_id, $terrority_id, $memo_date, $cal_type)
	{
		// from so_id , retrieve aso_id,terrority_id :  no need as we have territory_id
		// check gross_value >0

		if ($gross_value > 0) {
			$this->loadModel('Outlet');
			// from outlet_id, retrieve pharma or non-pharma
			$outlet_info = $this->Outlet->find('first', array(
				'conditions' => array(
					'Outlet.id' => $outlet_id
				),
				'recursive' => -1
			));

			if (!empty($outlet_info)) {
				$is_pharma_type = $outlet_info['Outlet']['is_pharma_type'];
				// from memo_date , split month and get month name and compare month table with memo year
				$memoDate = strtotime($memo_date);
				$month = date("n", $memoDate);
				$year = date("Y", $memoDate);
				$this->loadModel('Month');

				// from outlet_id, retrieve pharma or non-pharma
				$fasical_info = $this->Month->find('first', array(
					'conditions' => array(
						'Month.month' => $month,
						'Month.year' => $year
					),
					'recursive' => -1
				));


				if (!empty($fasical_info)) {
					$this->loadModel('SaleTargetMonth');
					if ($cal_type == 1) {
						if ($is_pharma_type == 1) {
							$update_fields_arr = array('SaleTargetMonth.effective_call_pharma_achievement' => "SaleTargetMonth.effective_call_pharma_achievement+1");
						} elseif ($is_pharma_type == 0) {
							$update_fields_arr = array('SaleTargetMonth.effective_call_non_pharma_achievement' => "SaleTargetMonth.effective_call_non_pharma_achievement+1");
						}
					} else {
						if ($is_pharma_type == 1) {
							$update_fields_arr = array('SaleTargetMonth.effective_call_pharma_achievement' => "SaleTargetMonth.effective_call_pharma_achievement-1");
						} elseif ($is_pharma_type == 0) {
							$update_fields_arr = array('SaleTargetMonth.effective_call_non_pharma_achievement' => "SaleTargetMonth.effective_call_non_pharma_achievement-1");
						}
					}

					$conditions_arr = array('SaleTargetMonth.product_id' => 0, 'SaleTargetMonth.territory_id' => $terrority_id, 'SaleTargetMonth.fiscal_year_id' => $fasical_info['Month']['fiscal_year_id'], 'SaleTargetMonth.month_id' => $fasical_info['Month']['id']);

					$this->SaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
				}
			}
		}
	}

	// cal_type=1 means increment and 2 means deduction
	// it will be called from  memo_details
	public function sales_calculation($product_id, $terrority_id, $quantity, $gross_value, $memo_date, $cal_type)
	{
		// from so_id , retrieve aso_id,terrority_id : no need as we have territory_id
		// from memo_date , split month and get month name and compare month table with memo year
		$memoDate = strtotime($memo_date);
		$month = date("n", $memoDate);
		$year = date("Y", $memoDate);
		$this->loadModel('Month');
		// from outlet_id, retrieve pharma or non-pharma
		$fasical_info = $this->Month->find('first', array(
			'conditions' => array(
				'Month.month' => $month,
				'Month.year' => $year
			),
			'recursive' => -1
		));

		if (!empty($fasical_info)) {
			$this->loadModel('SaleTargetMonth');
			if ($cal_type == 1) {
				$update_fields_arr = array('SaleTargetMonth.target_quantity_achievement' => "SaleTargetMonth.target_quantity_achievement+$quantity", 'SaleTargetMonth.target_amount_achievement' => "SaleTargetMonth.target_amount_achievement+$gross_value");
			} else {
				$update_fields_arr = array('SaleTargetMonth.target_quantity_achievement' => "SaleTargetMonth.target_quantity_achievement-$quantity", 'SaleTargetMonth.target_amount_achievement' => "SaleTargetMonth.target_amount_achievement-$gross_value");
			}

			$conditions_arr = array('SaleTargetMonth.product_id' => $product_id, 'SaleTargetMonth.territory_id' => $terrority_id, 'SaleTargetMonth.fiscal_year_id' => $fasical_info['Month']['fiscal_year_id'], 'SaleTargetMonth.month_id' => $fasical_info['Month']['id']);
			$this->SaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
		}
	}

	// cal_type=1 means increment and 2 means deduction
	// it will be called from memo not from memo_details
	public function oc_calculation($terrority_id, $gross_value, $outlet_id, $memo_date, $memo_time, $cal_type)
	{

		// from so_id , retrieve aso_id,terrority_id :  no need as we have territory_id
		// check gross_value >0
		if ($gross_value > 0) {
			$this->loadModel('Memo');
			// this will be updated monthly , if done then increment else no action
			$month_first_date = date('Y-m-01', strtotime($memo_date));
			$count = $this->Memo->find('count', array(
				'conditions' => array(
					'Memo.outlet_id' => $outlet_id,
					'Memo.memo_date >= ' => $month_first_date,
					'Memo.memo_time < ' => $memo_time
				)
			));

			if ($count == 0) {
				$this->loadModel('Outlet');
				// from outlet_id, retrieve pharma or non-pharma
				$outlet_info = $this->Outlet->find('first', array(
					'conditions' => array(
						'Outlet.id' => $outlet_id
					),
					'recursive' => -1
				));

				if (!empty($outlet_info)) {
					$is_pharma_type = $outlet_info['Outlet']['is_pharma_type'];
					// from memo_date , split month and get month name and compare month table with memo year
					$memoDate = strtotime($memo_date);
					$month = date("n", $memoDate);
					$year = date("Y", $memoDate);
					$this->loadModel('Month');
					// from outlet_id, retrieve pharma or non-pharma
					$fasical_info = $this->Month->find('first', array(
						'conditions' => array(
							'Month.month' => $month,
							'Month.year' => $year
						),
						'recursive' => -1
					));

					if (!empty($fasical_info)) {
						$this->loadModel('SaleTargetMonth');
						if ($cal_type == 1) {
							if ($is_pharma_type == 1) {
								$update_fields_arr = array('SaleTargetMonth.outlet_coverage_pharma_achievement' => "SaleTargetMonth.outlet_coverage_pharma_achievement+1");
							} elseif ($is_pharma_type == 0) {
								$update_fields_arr = array('SaleTargetMonth.outlet_coverage_non_pharma_achievement' => "SaleTargetMonth.outlet_coverage_non_pharma_achievement+1");
							}
						} else {
							if ($is_pharma_type == 1) {
								$update_fields_arr = array('SaleTargetMonth.outlet_coverage_pharma_achievement' => "SaleTargetMonth.outlet_coverage_pharma_achievement-1");
							} elseif ($is_pharma_type == 0) {
								$update_fields_arr = array('SaleTargetMonth.outlet_coverage_non_pharma_achievement' => "SaleTargetMonth.outlet_coverage_non_pharma_achievement-1");
							}
						}

						$conditions_arr = array('SaleTargetMonth.product_id' => 0, 'SaleTargetMonth.territory_id' => $terrority_id, 'SaleTargetMonth.fiscal_year_id' => $fasical_info['Month']['fiscal_year_id'], 'SaleTargetMonth.month_id' => $fasical_info['Month']['id']);
						$this->SaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
						//pr($conditions_arr);
						//pr($update_fields_arr);
						//exit;
					}
				}
			}
		}
	}

	// it will be called from memo_details
	public function stamp_calculation($memo_no, $terrority_id, $product_id, $outlet_id, $quantity, $memo_date, $cal_type, $gross_amount, $market_id)
	{
		// from outlet_id, get bonus_type_id and check if null then no action else action

		$this->loadModel('Outlet');
		// from outlet_id, retrieve pharma or non-pharma
		$outlet_info = $this->Outlet->find('first', array(
			'conditions' => array(
				'Outlet.id' => $outlet_id
			),
			'recursive' => -1
		));

		if (!empty($outlet_info) && $gross_amount > 0) {
			$bonus_type_id = $outlet_info['Outlet']['bonus_type_id'];
			if (($bonus_type_id === null) || (empty($bonus_type_id))) {
				// no action
			} else {
				// from memo_date , split month and get month name and compare month table with memo year (get fascal year id)
				$memoDate = strtotime($memo_date);
				$month = date("n", $memoDate);
				$year = date("Y", $memoDate);
				$this->loadModel('Month');
				$fasical_info = $this->Month->find('first', array(
					'conditions' => array(
						'Month.month' => $month,
						'Month.year' => $year
					),
					'recursive' => -1
				));

				if (!empty($fasical_info)) {
					// check bonus card table , where is_active,and others  and get min qty per memo
					$this->loadModel('BonusCard');
					$bonus_card_info = $this->BonusCard->find('first', array(
						'conditions' => array(
							'BonusCard.fiscal_year_id' => $fasical_info['Month']['fiscal_year_id'],
							'BonusCard.is_active' => 1,
							'BonusCard.product_id' => $product_id,
							'BonusCard.bonus_card_type_id' => $bonus_type_id
						),
						'recursive' => -1
					));

					// if exist min qty per memo , then stamp_no=mod(quantity/min qty per memo)
					if (!empty($bonus_card_info)) {
						$min_qty_per_memo = $bonus_card_info['BonusCard']['min_qty_per_memo'];
						if ($min_qty_per_memo && $min_qty_per_memo <= $quantity) {
							$stamp_no = floor($quantity / $min_qty_per_memo);
							if ($cal_type != 1) {
								$stamp_no = $stamp_no * (-1);
								$quantity = $quantity * (-1);
							}


							$this->loadModel('StoreBonusCard');
							$log_data = array();
							$log_data['StoreBonusCard']['created_at'] = $this->current_datetime();
							$log_data['StoreBonusCard']['territory_id'] = $terrority_id;
							$log_data['StoreBonusCard']['outlet_id'] = $outlet_id;
							$log_data['StoreBonusCard']['market_id'] = $market_id;
							$log_data['StoreBonusCard']['product_id'] = $product_id;
							$log_data['StoreBonusCard']['quantity'] = $quantity;
							$log_data['StoreBonusCard']['no_of_stamp'] = $stamp_no;
							$log_data['StoreBonusCard']['bonus_card_id'] = $bonus_card_info['BonusCard']['id'];
							$log_data['StoreBonusCard']['bonus_card_type_id'] = $bonus_type_id;
							$log_data['StoreBonusCard']['fiscal_year_id'] = $bonus_card_info['BonusCard']['fiscal_year_id'];
							$log_data['StoreBonusCard']['memo_no'] = $memo_no;

							$this->StoreBonusCard->create();
							$this->StoreBonusCard->save($log_data);
						}
					}
				}
			}
		}
	}

	public function admin_memo_no_validation()
	{
		$this->loadModel('CsaMemo');
		if ($this->request->is('post')) {
			$memo_no = $this->request->data['memo_no'];
			$sale_type_id = $this->request->data['sale_type'];

			if ($sale_type_id == 1 || $sale_type_id == 3 || $sale_type_id == 4) {
				$memo_list = $this->Memo->find('list', array(
					'conditions' => array('Memo.memo_no' => $memo_no),
					'fields' => array('memo_no'),
					'recursive' => -1
				));
			} else {
				$memo_list = $this->CsaMemo->find('list', array(
					'conditions' => array('CsaMemo.csa_memo_no' => $memo_no),
					'fields' => array('csa_memo_no'),
					'recursive' => -1
				));
			}
			$memo_exist = count($memo_list);

			echo json_encode($memo_exist);
		}

		$this->autoRender = false;
	}

	public function admin_get_product()
	{
		$this->loadModel('Store');
		$this->loadModel('Outlet');
		$this->loadModel('Product');
		$this->loadModel('CurrentInventory');
		$this->loadModel('ProductCombinationsV2');
		$territory_id = $this->request->data['territory_id'];
		$rs = array(array('id' => '', 'name' => '---- Select Product -----'));
		$store_info = $this->Store->find('first', array(
			'conditions' => array(
				'Store.territory_id' => $territory_id
			),
			'recursive' => -1
		));


		if (isset($store_info['Store']['id']) && $store_info['Store']['id']) {
			$store_id = $store_info['Store']['id'];

			if (isset($this->request->data['csa_id']) && $this->request->data['csa_id'] != 0) {
				$conditions = array('CurrentInventory.store_id' => $store_id, 'inventory_status_id' => 1);
			} else {
				$conditions = array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.qty > ' => 0, 'inventory_status_id' => 1);
			}

			if (isset($this->request->data['outlet_id']) && isset($this->request->data['memo_date'])) {
				$outlet_info = $this->Outlet->find('first', array(
					'conditions' => array(
						'Outlet.id' => $this->request->data['outlet_id']
					),
					'recursive' => -1
				));

				$product_combination = $this->ProductCombinationsV2->find('all', array(
					'fields' => array('DISTINCT ProductCombinationsV2.product_id'),
					'joins' => array(
						array(
							'table' => 'product_price_section_v2',
							'alias' => 'ProductPriceSectionV2',
							'conditions' => 'ProductPriceSectionV2.id=ProductCombinationsV2.section_id'
						),
					),
					'conditions' => array(
						'ProductCombinationsV2.effective_date <=' => date('Y-m-d', strtotime($this->request->data['memo_date'])),
						'ProductPriceSectionV2.is_so' => 1
					),
					'recursive' => -1
				));
				$product = Set::extract($product_combination, '/ProductCombinationsV2/product_id');
				$conditions['OR'] = array('CurrentInventory.product_id' => $product, 'Product.product_type_id !=' => 1);
			}
			$products_from_ci = $this->CurrentInventory->find('all', array(
				'fields' => array('DISTINCT CurrentInventory.product_id'),
				'conditions' => $conditions,
			));

			$product_ci = array();
			foreach ($products_from_ci as $each_ci) {
				$product_ci[] = $each_ci['CurrentInventory']['product_id'];
			}

			$product_ci_in = implode(",", $product_ci);
			$products = $this->Product->find('all', array(
				'conditions' => array('Product.id' => $product_ci), 'order' => array('Product.order' => 'asc'),
				'joins' => array(
					array(
						'table' => 'products',
						'alias' => 'ParentProduct',
						'type' => 'left',
						'conditions' => 'ParentProduct.id=Product.parent_id'
					)
				),
				'fields' => array('Product.id as id', 'Product.name as name', 'ParentProduct.id as p_id', 'ParentProduct.name as p_name'),
				'recursive' => -1
			));
			$group_product = array();
			foreach ($products as $data) {
				if ($data[0]['p_id']) {
					$group_product[$data[0]['p_id']][] = $data[0]['id'];
				} else {
					$group_product[$data[0]['id']][] = $data[0]['id'];
				}
			}
			$product_array = array();
			foreach ($products as $data) {
				if ($data['0']['p_id'] && count($group_product[$data[0]['p_id']]) == 1) {
					$name = $data[0]['p_name'];
				} else {
					$name = $data[0]['name'];
				}
				$product_array[] = array(
					'id' => $data[0]['id'],
					'name' => $name
				);
			}

			// $data_array = Set::extract($products, '{n}.0');

			if (!empty($products)) {
				echo json_encode(array_merge($rs, $product_array));
			} else {
				echo json_encode($rs);
			}
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}

	public function search_array($value, $key, $array)
	{
		foreach ($array as $k => $val) {
			if ($val[$key] == $value) {
				return $array[$k];
			}
		}
		return null;
	}


	public function admin_memo_editable($id = null)
	{
		if ($id) {
			$this->Memo->id = $id;
			if ($this->Memo->id) {
				if ($this->Memo->saveField('memo_editable', 1)) {
					$this->Session->setFlash(__('The setting has been saved!'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash(__('Memo editable failed!'), 'flash/error');
					$this->redirect(array('action' => 'index'));
				}
			}
		}
		$this->autoRender = false;
	}

	public function get_bonus_product_details()
	{
		$this->LoadModel('Product');

		$product_id = $this->request->data['product_id'];
		$territory_id = $this->request->data['territory_id'];

		$product_details = $this->Product->find('first', array(
			'fields' => array('MIN(Product.product_category_id) as category_id', 'MIN(Product.sales_measurement_unit_id) as measurement_unit_id', 'MIN(MeasurementUnit.name) as measurement_unit_name', 'SUM(CurrentInventory.qty) as total_qty'),
			'conditions' => array('Product.id' => $product_id, 'Store.territory_id' => $territory_id),
			'joins' => array(
				array(
					'table' => 'measurement_units',
					'alias' => 'MeasurementUnit',
					'conditions' => 'MeasurementUnit.id=Product.sales_measurement_unit_id'
				),
				array(
					'table' => 'current_inventories',
					'alias' => 'CurrentInventory',
					'type' => 'Inner',
					'conditions' => 'CurrentInventory.product_id=Product.id'
				),
				array(
					'table' => 'stores',
					'alias' => 'Store',
					'type' => 'Inner',
					'conditions' => 'CurrentInventory.store_id=Store.id'
				)
			),
			'group' => array('Product.id', 'Store.id', 'Store.territory_id'),
			'recursive' => -1
		));
		// pr($product_details);exit;
		$data['category_id'] = $product_details[0]['category_id'];
		$data['measurement_unit_id'] = $product_details[0]['measurement_unit_id'];
		$data['measurement_unit_name'] = $product_details[0]['measurement_unit_name'];
		$data['total_qty'] = $this->unit_convertfrombase($product_id, $data['measurement_unit_id'], $product_details[0]['total_qty']);
		echo json_encode($data);
		$this->autoRender = false;
	}
	public function get_bonus_product()
	{
		$this->LoadModel('Product');
		$this->LoadModel('Store');

		$territory_id = $this->request->data['territory_id'];
		$memo_date = date('Y-m-d', strtotime($this->request->data['memo_date']));
		$store_id = $this->Store->find('first', array(
			'fields' => array('Store.id'),
			'conditions' => array('Store.territory_id' => $territory_id),
			'recursive' => -1
		));
		$product_list = $this->Product->find('all', array(
			'fields' => array('Product.id', 'Product.name'),
			'joins' => array(
				array(
					'table' => 'current_inventories',
					'alias' => 'CurrentInventory',
					'type' => 'Inner',
					'conditions' => 'CurrentInventory.product_id=Product.id'
				),
				array(
					'table' => 'open_combination_products',
					'alias' => 'OpenCombinationProduct',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.product_id=Product.id'
				),
				array(
					'table' => 'open_combinations',
					'alias' => 'OpenCombination',
					'type' => 'Inner',
					'conditions' => 'OpenCombinationProduct.combination_id=OpenCombination.id and is_bonus=1 and \'' . $memo_date . '\'BETWEEN OpenCombination.start_date AND OpenCombination.end_date'
				),

			),
			'conditions' => array(
				'CurrentInventory.qty >' => 0,
				'CurrentInventory.store_id' => $store_id['Store']['id'],

			),
			'group' => array('Product.id', 'Product.name'),
			'recursive' => -1
		));
		echo json_encode($product_list);
		$this->autoRender = false;
	}

	public function get_product_price_id($product_id, $product_prices, $all_product_id)
	{
		// echo $product_id.'--'.$product_prices.'<br>';
		$this->LoadModel('ProductCombination');
		$this->LoadModel('Combination');
		$data = array();
		$product_price = $this->ProductCombination->find('first', array(
			'conditions' => array(
				'ProductCombination.product_id' => $product_id,
				'ProductCombination.price' => $product_prices,
				'ProductCombination.effective_date <=' => $this->current_date(),
			),
			'order' => array('ProductCombination.id' => 'DESC'),
			'recursive' => -1
		));

		// pr($product_price);exit;
		// echo $this->ProductCombination->getLastquery().'<br>';
		if ($product_price) {
			$is_combine = 0;
			if ($product_price['ProductCombination']['combination_id'] != 0) {
				$combination = $this->Combination->find('first', array(
					'conditions' => array('Combination.id' => $product_price['ProductCombination']['combination_id']),
					'recursive' => -1
				));
				$combination_product = explode(',', $combination['Combination']['all_products_in_combination']);
				foreach ($combination_product as $combination_prod) {
					if ($product_id != $combination_prod && in_array($combination_prod, $all_product_id)) {
						$data['combination_id'] = $product_price['ProductCombination']['combination_id'];
						$data['product_price_id'] = $product_price['ProductCombination']['id'];
						$is_combine = 1;
						break;
					}
				}
			}
			if ($is_combine == 0) {
				$product_price = $this->ProductCombination->find('first', array(
					'conditions' => array(
						'ProductCombination.product_id' => $product_id,
						'ProductCombination.price' => $product_prices,
						'ProductCombination.effective_date <=' => $this->current_date(),
						'ProductCombination.parent_slab_id' => 0
					),
					'order' => array('ProductCombination.id DESC'),
					'recursive' => -1
				));
				$data['combination_id'] = '';
				$data['product_price_id'] = $product_price['ProductCombination']['id'];
			}
			return $data;
		} else {
			$data['combination_id'] = '';
			$data['product_price_id'] = '';
			return $data;
		}
	}
	function get_csa_list_by_office_id()
	{
		/*pr($this->request->data);*/
		$office_id = $this->request->data['office_id'];
		$output = "<option value=''>--- Select Csa ---</option>";
		if ($office_id) {
			$csa_outlet = $this->Outlet->find('list', array(
				'conditions' => array('Territory.office_id' => $office_id, 'Outlet.is_csa' => 1),
				'joins' => array(
					array(
						'table' => 'markets',
						'alias' => 'Market',
						'conditions' => 'Market.id=Outlet.market_id'
					),
					array(
						'table' => 'territories',
						'alias' => 'Territory',
						'conditions' => 'Territory.id=Market.territory_id'
					),
				)
			));
			if ($csa_outlet) {
				foreach ($csa_outlet as $key => $data) {
					$output .= "<option value='$key'>$data</option>";
				}
			}
		}
		echo $output;
		$this->autoRender = false;
	}
	function get_territory_list_by_csa_id()
	{
		$this->LoadModel('Territory');
		$csa_id = $this->request->data['csa_id'];
		$output = "<option value=''>--- Select Territory ---</option>";
		if ($csa_id) {
			$territory = $this->Territory->find('list', array(
				'conditions' => array('Outlet.id' => $csa_id),
				'joins' => array(
					array(
						'alias' => 'Market',
						'table' => 'markets',
						'type' => 'INNER',
						'conditions' => 'Market.territory_id = Territory.id'
					),
					array(
						'alias' => 'Outlet',
						'table' => 'outlets',
						'type' => 'INNER',
						'conditions' => 'Outlet.market_id = Market.id'
					),
				)
			));

			if ($territory) {
				foreach ($territory as $key => $data) {
					$output .= "<option value='$key'>$data</option>";
				}
			}
		}

		echo $output;
		$this->autoRender = false;
	}
	function get_thana_by_territory_id()
	{
		$territory_id = $this->request->data['territory_id'];
		$this->loadModel('Territory');
		$territory_info = $this->Territory->find(
			'first',
			array(
				'conditions' => array('Territory.id' => $territory_id),
				'joins' => array(
					array(
						'table' => 'users',
						'alias' => 'User',
						'conditions' => 'SalesPerson.id=User.sales_person_id'
					)
				),
				'fields' => array('Office.id', 'User.user_group_id', 'User.id'),
				'recursive' => 0
			)
		);
		$user_group_id = $territory_info['User']['user_group_id'];
		$user_id = $territory_info['User']['id'];
		if ($user_group_id == 1008) {
			$this->loadModel('UserTerritoryList');
			$territory_list = $this->UserTerritoryList->find('list', array(
				'conditions' => array('UserTerritoryList.user_id' => $user_id),
				'fields' => array('UserTerritoryList.territory_id', 'UserTerritoryList.territory_id'),
				'recursive' => -1
			));
			if ($territory_list)
				$territory_id = array_keys($territory_list);
		}
		$output = "<option value=''>--- Select Thana ---</option>";
		if ($territory_id) {
			$thana = $this->Thana->find('list', array(
				'conditions' => array('ThanaTerritory.territory_id' => $territory_id),
				'joins' => array(
					array(
						'table' => 'thana_territories',
						'alias' => 'ThanaTerritory',
						'conditions' => 'ThanaTerritory.thana_id=Thana.id'
					)
				)
			));

			if ($thana) {
				foreach ($thana as $key => $data) {
					$output .= "<option value='$key'>$data</option>";
				}
			}
		}

		echo $output;
		$this->autoRender = false;
	}
	
	function get_thana_by_territory_id_tow()
	{
		$territory_id = $this->request->data['territory_id'];
		$this->loadModel('Territory');
		
		$terrtory_id_parent_id = $this->Territory->find('first', array(
			'fields' => array('Territory.parent_id','Territory.id'),
			'conditions' => array('Territory.id' => $territory_id),
			'recursive' => -1
		));

		if($terrtory_id_parent_id['Territory']['parent_id']>0){
			
			$territory_id = $terrtory_id_parent_id['Territory']['parent_id'];

		}
		
		$output = "<option value=''>--- Select Thana ---</option>";
		if ($territory_id) {
			$thana = $this->Thana->find('list', array(
				'conditions' => array('ThanaTerritory.territory_id' => $territory_id),
				'joins' => array(
					array(
						'table' => 'thana_territories',
						'alias' => 'ThanaTerritory',
						'conditions' => 'ThanaTerritory.thana_id=Thana.id'
					)
				)
			));

			if ($thana) {
				foreach ($thana as $key => $data) {
					$output .= "<option value='$key'>$data</option>";
				}
			}
		}

		echo $output;
		$this->autoRender = false;
	}
	
	
	function get_market_by_thana_id()
	{
		$thana_id = $this->request->data['thana_id'];
		$output = "<option value=''>--- Select Market ---</option>";
		if ($thana_id) {
			$market = $this->Market->find('list', array(
				'conditions' => array('Market.thana_id' => $thana_id)
			));
			if ($market) {
				foreach ($market as $key => $data) {
					$output .= "<option value='$key'>$data</option>";
				}
			}
		}
		echo $output;
		$this->autoRender = false;
	}
	
	function get_market_by_thana_id_territory_id()
	{
		$thana_id = $this->request->data['thana_id'];
		$territory_id = $this->request->data['territory_id'];

		$this->loadModel('Territory');

		$terrtory_id_parent_id = $this->Territory->find('first', array(
			'fields' => array('Territory.parent_id','Territory.id'),
			'conditions' => array('Territory.id' => $territory_id),
			'recursive' => -1
		));
	
		if($terrtory_id_parent_id['Territory']['parent_id']>0){
			
			$territory_id = $terrtory_id_parent_id['Territory']['parent_id'];
	
		}

		$output = "<option value=''>--- Select Market ---</option>";
		if ($thana_id) {
			$market = $this->Market->find('list', array(
				'conditions' => array(
					'Market.thana_id' => $thana_id,
					'Market.territory_id' => $territory_id,
					'Market.is_active' => 1	
				),
				'order' => array('Market.name asc'),
			));
			if ($market) {
				foreach ($market as $key => $data) {
					$output .= "<option value='$key'>$data</option>";
				}
			}
		}
		echo $output;
		$this->autoRender = false;
	}


	private function outletGroupCheck($outlet_id = 0)
	{
		if ($outlet_id) {
			$this->loadModel('Outlet');
			$result = $this->Outlet->find('first', array(
				'fields' => array('is_within_group'),
				'conditions' => array('Outlet.id' => $outlet_id),
				'recursive' => -1
			));
			if ($result) {
				return $result['Outlet']['is_within_group'];
			} else {
				return 0;
			}
		}
	}

	private function productInjectableCheck($products_ids = array())
	{
		if ($products_ids) {
			$this->loadModel('Product');

			$result = $this->Product->find('first', array(
				'fields' => array('is_injectable'),
				'conditions' => array(
					'Product.id' => $products_ids,
					'Product.is_injectable' => 1
				),
				'recursive' => -1
			));
			if ($result) {
				return $result['Product']['is_injectable'];
			} else {
				return 0;
			}
		}
	}
	public function get_fraction_slab()
	{
		$this->LoadModel('ProductFractionSlab');
		$product_id = $this->request->data['product_id'];
		$sales_or_bonus = $this->request->data['sales_or_bonus'];
		$conditions = array('ProductFractionSlab.product_id' => $product_id);
		if ($sales_or_bonus == 1) {
			$conditions['ProductFractionSlab.use_for_sales'] = 1;
		} elseif ($sales_or_bonus == 2) {
			$conditions['ProductFractionSlab.use_for_bonus'] = 1;
		}
		$fraction_slab = $this->ProductFractionSlab->find('list', array(
			'conditions' => $conditions,
			'fields' => array('sales_qty'),
			'recursive' => -1
		));
		echo json_encode($fraction_slab);
		$this->autoRender = false;
		//exit;
	}

	public function get_bonus_product_info()
	{
		$this->LoadModel('Product');
		$this->LoadModel('GroupWiseDiscountBonusPolicyOptionBonusProduct');

		$measurement_unit = $this->MeasurementUnit->find('list', array());

		$product_id = $this->request->data['product_id'];
		$territory_id = $this->request->data['territory_id'];
		$option_id = $this->request->data['option_id'];

		/*$b_product_details = $this->GroupWiseDiscountBonusPolicyOptionBonusProduct->find('first',array(
			'fields'=>array('id', 'bonus_qty', 'measurement_unit_id'),
			'conditions'=>array('group_wise_discount_bonus_policy_option_id'=>$option_id,'bonus_product_id'=>$product_id),
			'recursive'=>-1
			));*/

		//pr($b_product_details);

		$b_data = $this->Session->read('b_data');

		$product_details = $this->GroupWiseDiscountBonusPolicyOptionBonusProduct->find('first', array(
			'fields' => array('SUM(CurrentInventory.qty) as total_qty'),
			'conditions' => array('GroupWiseDiscountBonusPolicyOptionBonusProduct.group_wise_discount_bonus_policy_option_id' => $option_id, 'GroupWiseDiscountBonusPolicyOptionBonusProduct.bonus_product_id' => $product_id, 'Store.territory_id' => $territory_id),
			'joins' => array(
				array(
					'table' => 'current_inventories',
					'alias' => 'CurrentInventory',
					'type' => 'Inner',
					'conditions' => 'CurrentInventory.product_id=GroupWiseDiscountBonusPolicyOptionBonusProduct.bonus_product_id'
				),
				array(
					'table' => 'stores',
					'alias' => 'Store',
					'type' => 'Inner',
					'conditions' => 'CurrentInventory.store_id=Store.id'
				)
			),
			'group' => array('bonus_product_id', 'Store.id', 'Store.territory_id'),
			'recursive' => -1
		));


		//pr($product_details);

		$data = array();
		//$data['category_id']=$product_details[0]['category_id'];
		$data['bonus_qty'] = $b_data[$product_id]['bonus_qty'];
		$measurement_unit_id = $b_data[$product_id]['measurement_unit_id'];
		$data['measurement_unit_id'] = $measurement_unit_id;
		$data['measurement_unit_name'] = $measurement_unit[$measurement_unit_id];
		$data['total_qty'] = $this->unit_convertfrombase($product_id, $measurement_unit_id, $product_details[0]['total_qty']);

		//pr($data);exit;

		echo json_encode($data);
		$this->autoRender = false;
	}
	public function get_remarks()
	{
		$min_qty = $this->request->data['min_qty'];
		$product_id = $this->request->data['product_id'];
		$this->loadModel('Product');
		$products_info = $this->Product->find('first', array('conditions' => array('Product.id' => $product_id), 'recursive' => -1));
		$measurement_unit_id = $products_info['Product']['sales_measurement_unit_id'];

		$base_qty = $this->unit_convert($product_id, $measurement_unit_id, $min_qty);
		$cartoon_qty = $this->unit_convertfrombase($product_id, 16, $base_qty);

		$cartoon = explode('.', $cartoon_qty);
		$cartoon_qty = $cartoon[0];
		if ($cartoon[1] != '00' && $cartoon[1]) {
			$this->loadModel('MeasurementUnit');
			$meauserment_unit = $this->MeasurementUnit->find('first', array(
				'conditions' => array(
					'MeasurementUnit.id' => $measurement_unit_id
				),
				'recursive' => -1
			));
			$measurement_unit_name = $meauserment_unit['MeasurementUnit']['name'];
			if (strlen($measurement_unit_name) > 4) {
				$measurement_unit_name = substr($measurement_unit_name, 0, 4) . '.';
			}
			$base_qty = $this->unit_convert($product_id, 16, '.' . $cartoon[1]);
			$dispenser = $this->unit_convertfrombase($product_id, $measurement_unit_id, $base_qty);
		}
		$result_data = array();
		$result_data['remarks'] = '';
		if ($cartoon_qty) {
			$result_data['remarks'] .= $cartoon_qty . " S/c";
		}
		if (isset($dispenser)) {
			$result_data['remarks'] .= ($result_data['remarks'] ? ', ' : '') . $dispenser . " $measurement_unit_name";
		}
		echo json_encode($result_data);
		$this->autoRender = false;
	}
	public function get_product_price()
	{
		$this->LoadModel('ProductCombinationsV2');
		$this->LoadModel('CombinationsV2');
		$this->LoadModel('CombinationDetailsV2');
		$order_date = $this->request->data['memo_date'];
		$product_id = $this->request->data['product_id'];
		$min_qty = $this->request->data['min_qty'];
		$outlet_category_id = $this->request->data['outlet_category_id'];
		$special_group = json_decode($this->request->data['special_group'], 1);
		if (empty($special_group)) {
			$special_group[] = 0;
		}
		$product_wise_cart_qty = $this->request->data['cart_product'];
		$prev_combine_product = explode(',', $this->request->data['combined_product']);

		/*------------- min price slab finding ----------------------*/
		if ($min_qty < 1) {
			$min_qty = 1;
		}
		$slab_conditions = array();
		$slab_conditions['ProductCombinationsV2.effective_date <='] = date('Y-m-d', strtotime($order_date));
		$slab_conditions['ProductCombinationsV2.product_id'] = $product_id;
		$slab_conditions['ProductCombinationsV2.min_qty <='] = $min_qty;
		$price_slab = $this->ProductCombinationsV2->find('first', array(
			'conditions' => $slab_conditions,
			'joins' => array(
				array(
					'table' => 'product_price_section_v2',
					'alias' => 'PriceSection',
					'conditions' => 'PriceSection.id=ProductCombinationsV2.section_id and PriceSection.is_so=1'
				),
				array(
					'table' => 'product_price_other_for_slabs_v2',
					'alias' => 'SpecialPrice',
					'type' => 'Left',
					'conditions' => '
						SpecialPrice.product_combination_id=ProductCombinationsV2.id 
						and SpecialPrice.price_for=1 
						and SpecialPrice.type=1
						and SpecialPrice.reffrence_id in (' . implode(',', $special_group) . ')'
				),
				array(
					'table' => 'product_price_other_for_slabs_v2',
					'alias' => 'OutletCategoryPrice',
					'type' => 'Left',
					'conditions' => '
						OutletCategoryPrice.product_combination_id=ProductCombinationsV2.id
						and OutletCategoryPrice.price_for=1 
						and OutletCategoryPrice.type=2
						and OutletCategoryPrice.reffrence_id=' . $outlet_category_id

				)
			),
			'fields' => array(
				'ProductCombinationsV2.id',
				'ProductCombinationsV2.effective_date',
				'ProductCombinationsV2.min_qty',
				'ProductCombinationsV2.price',
				'SpecialPrice.price',
				'SpecialPrice.reffrence_id',
				'OutletCategoryPrice.price',
				'OutletCategoryPrice.reffrence_id',
			),
			'group' => array(
				'ProductCombinationsV2.id',
				'ProductCombinationsV2.effective_date',
				'ProductCombinationsV2.min_qty',
				'ProductCombinationsV2.price',
				'SpecialPrice.price',
				'SpecialPrice.reffrence_id',
				'OutletCategoryPrice.price',
				'OutletCategoryPrice.reffrence_id',
			),
			'order' => array(
				'ProductCombinationsV2.id desc',
				'ProductCombinationsV2.effective_date desc',
				'ProductCombinationsV2.min_qty desc',
				'SpecialPrice.reffrence_id desc',
				'OutletCategoryPrice.reffrence_id desc',
			),
			'recursive' => -1
		));
		$product_price_array = array();
		if ($price_slab) {
			if ($price_slab['SpecialPrice']['price']) {
				$product_price_array['price'] = $price_slab['SpecialPrice']['price'];
				$reffrence_id = $price_slab['SpecialPrice']['reffrence_id'];
				$other_combination_joins = array(
					'table' => 'product_price_other_for_slabs_v2',
					'alias' => 'Other',
					'type' => 'Left',
					'conditions' => '
							Other.product_combination_id=PC.id 
							and Other.price_for=1 
							and Other.type=1
							and Other.reffrence_id=' . $reffrence_id
				);
				$combination_data_group = array(
					'CombinationDetailsV2.product_id',
					'PC.id',
					'PC.price',
					'Other.price',
				);
				$combination_data_fields = array(
					'CombinationDetailsV2.product_id',
					'PC.id',
					'PC.price',
					'Other.price',
				);
			} elseif ($price_slab['OutletCategoryPrice']['price']) {
				$product_price_array['price'] = $price_slab['OutletCategoryPrice']['price'];
				$reffrence_id = $price_slab['OutletCategoryPrice']['reffrence_id'];
				$other_combination_joins = array(
					'table' => 'product_price_other_for_slabs_v2',
					'alias' => 'Other',
					'type' => 'Left',
					'conditions' => '
						Other.product_combination_id=PC.id
						and Other.price_for=1 
						and Other.type=1
						and Other.reffrence_id=' . $reffrence_id

				);
				$combination_data_group = array(
					'CombinationDetailsV2.product_id',
					'PC.id',
					'PC.price',
					'Other.price',
				);
				$combination_data_fields = array(
					'CombinationDetailsV2.product_id',
					'PC.id',
					'PC.price',
					'Other.price',
				);
			} else {
				$product_price_array['price'] = $price_slab['ProductCombinationsV2']['price'];
				$reffrence_id = 0;
				$other_combination_joins = '';
				$combination_data_group = array(
					'CombinationDetailsV2.product_id',
					'PC.id',
					'PC.price',
				);
				$combination_data_fields = array(
					'CombinationDetailsV2.product_id',
					'PC.id',
					'PC.price',
				);
			}
			$product_price_array['price_id'] = $price_slab['ProductCombinationsV2']['id'];
			$product_price_array['total_value'] = sprintf('%.2f', $product_price_array['price'] * $product_wise_cart_qty[$product_id]);
			$product_price_array['combine_product'] = '';
			$combine_product = array();
			$combine_product[] = $product_id;
			$product_price_array['recall_product_for_price'] = array_udiff($prev_combine_product, $combine_product, function ($a, $b) {
				if ($a != $b) {
					return 1;
				}
			});
			$combination_conditions = array();


			$combinatios_data_check = $this->CombinationsV2->query(
				"
								select
									t.id,
									t.effective_date,
									t.combined_qty
								from
								(
									select 
										pcl.id,
										pcl.effective_date,
										max(pcl.effective_date) over (partition by pcl.combined_qty) as max_effective_date,
										pcl.combined_qty
									from 
									combinations_v2 pcl
									inner join combination_details_v2 pcld on pcl.id=pcld.combination_id
									inner join product_combinations_v2 pc on pc.min_qty=pcl.combined_qty 
																			and pc.product_id=pcld.product_id 
																			and pc.effective_date='" . $price_slab['ProductCombinationsV2']['effective_date'] . "'
									inner join product_price_section_v2 pcs on pcs.id=pc.section_id
									where
									pcl.effective_date <='" . date('Y-m-d', strtotime($order_date)) . "'
									and pcld.product_id=$product_id
									and pcs.is_so=1
									group by 
										pcl.id,
										pcl.effective_date,
										pcl.combined_qty
								)t
								where t.max_effective_date=t.effective_date
								 
						"
			);
			$combination_product_array = array();

			if ($combinatios_data_check) {
				foreach ($combinatios_data_check as $com_data) {
					$combination_details_conditions = array();
					$combination_details_conditions['CombinationDetailsV2.combination_id'] = $com_data['0']['id'];
					$combination_details = $this->CombinationDetailsV2->find('all', array(
						'conditions' => $combination_details_conditions,
						'joins' => array(
							array(
								'table' => 'product_combinations_v2',
								'alias' => 'PC',
								'type' => 'INNER',
								'conditions' => 'PC.product_id=CombinationDetailsV2.product_id 
												and pc.min_qty=' . intval($com_data['0']['combined_qty']) .
									'and pc.effective_date=\'' . $price_slab['ProductCombinationsV2']['effective_date'] . '\''
							),
							array(
								'table' => 'product_price_section_v2',
								'alias' => 'PriceSection',
								'conditions' => 'PriceSection.id=pc.section_id and PriceSection.is_so=1'
							),
							$other_combination_joins
						),
						'group' => $combination_data_group,
						'fields' => $combination_data_fields,
						'recursive' => -1
					));
					$combined_cart_qty = 0;
					$price_id = 0;
					$price = 0;
					foreach ($combination_details as $details_data) {
						$combined_cart_qty += isset($product_wise_cart_qty[$details_data['CombinationDetailsV2']['product_id']])
							? $product_wise_cart_qty[$details_data['CombinationDetailsV2']['product_id']] : 0;
						if ($product_id == $details_data['CombinationDetailsV2']['product_id']) {
							$price_id = $details_data['PC']['id'];
							if (isset($details_data['Other']['price'])) {
								$price = $details_data['Other']['price'];
							} else {
								$price = $details_data['PC']['price'];
							}
						} else {
							if (isset($product_wise_cart_qty[$details_data['CombinationDetailsV2']['product_id']])) {
								$combine_product[] = $details_data['CombinationDetailsV2']['product_id'];
								if (isset($details_data['Other']['price'])) {
									$com_price = $details_data['Other']['price'];
								} else {
									$com_price = $details_data['PC']['price'];
								}
								$combination_product_array[] = array(
									'product_id' => $details_data['CombinationDetailsV2']['product_id'],
									'price' => $com_price,
									'total_value' => sprintf('%.2f', $com_price * $product_wise_cart_qty[$details_data['CombinationDetailsV2']['product_id']]),
									'price_id' => $details_data['PC']['id'],
									'combination_id' => $com_data['0']['id']
								);
							}
						}
					}
					if ($combined_cart_qty >= $com_data['0']['combined_qty']) {
						$product_price_array['price'] = $price;
						$product_price_array['price_id'] = $price_id;
						$product_price_array['total_value'] = sprintf('%.2f', $price * $product_wise_cart_qty[$product_id]);
						$product_price_array['combination'] = $combination_product_array;
						$product_price_array['combine_product'] = implode(",", $combine_product);
						$product_price_array['combination_id'] = $com_data['0']['id'];
						$product_price_array['recall_product_for_price'] = array_udiff($prev_combine_product, $combine_product, function ($a, $b) {
							if ($a != $b) {
								return 1;
							}
						});
						break;
					}
				}
			}
		} else {
			$product_price_array['price'] = 0;
			$product_price_array['price_id'] = 0;
			$product_price_array['total_value'] = 0;
			$product_price_array['combination'] = array();
			$product_price_array['combine_product'] = '';
			$product_price_array['recall_product_for_price'] = array();
		}
		/*---------Bonus-----------*/
		$this->loadModel('Bonus');
		$this->loadModel('Product');

		$this->Bonus->recursive = -1;
		$bonus_info = $this->Bonus->find('all', array(
			'conditions' => array(
				'mother_product_id' => $product_id,
				'effective_date <=' => date('Y-m-d', strtotime($order_date)),
				'end_date >=' => date('Y-m-d', strtotime($order_date))
			),
			'fields' => array('mother_product_quantity', 'bonus_product_id', 'bonus_product_quantity')
		));

		if (!empty($bonus_info[0]['Bonus']['mother_product_quantity'])) {
			$mother_product_quantity_bonus = $bonus_info[0]['Bonus']['mother_product_quantity'];
			$result_data['mother_product_quantity_bonus'] = $mother_product_quantity_bonus;
		}

		$no_of_bonus_slap = count($bonus_info);

		if ($no_of_bonus_slap != 0) {
			for ($slap_count = 0; $slap_count < $no_of_bonus_slap; $slap_count++) {
				$bonus_slap['bonus_product_id'][$slap_count] = $bonus_info[$slap_count]['Bonus']['bonus_product_id'];

				$this->Product->recursive = -1;
				$bonus_product_name = $this->Product->find('first', array(
					'conditions' => array('id' => $bonus_slap['bonus_product_id'][$slap_count]),
					'fields' => array('name', 'sales_measurement_unit_id')
				));
				$quantity_slap['mother_product_quantity'][$slap_count] = $bonus_info[$slap_count]['Bonus']['mother_product_quantity'];

				$result_data['bonus_product_id'][$slap_count] = $bonus_info[$slap_count]['Bonus']['bonus_product_id'];
				$result_data['bonus_product_name'][$slap_count] = $bonus_product_name['Product']['name'];
				$result_data['sales_measurement_unit_id'][$slap_count] = $bonus_product_name['Product']['sales_measurement_unit_id'];
				$result_data['bonus_product_quantity'][$slap_count] = $bonus_info[$slap_count]['Bonus']['bonus_product_quantity'];
			}
			for ($i = 0; $i < count($quantity_slap['mother_product_quantity']); $i++) {
				$result_data['mother_product_quantity'][] = array(
					'min' => $i == 0 ? 0 : $quantity_slap['mother_product_quantity'][$i - 1],
					'max' => $quantity_slap['mother_product_quantity'][$i] - 1
				);
			}
		}
		if (isset($result_data)) {
			$product_price_array = array_merge($product_price_array, $result_data);
		}
		echo json_encode($product_price_array);
		$this->autoRender = false;
	}
	public function get_product_policy()
	{
		$this->LoadModel('DiscountBonusPolicy');
		$this->LoadModel('Store');
		$this->LoadModel('DiscountBonusPolicyProduct');
		$this->LoadModel('DiscountBonusPolicyOption');
		$this->LoadModel('DiscountBonusPolicyOptionExclusionInclusionProduct');
		$order_date = date('Y-m-d', strtotime($this->request->data['order_date']));
		$outlet_id = $this->request->data['outlet_id'];
		$office_id = $this->request->data['office_id'];
		$territory_id = $this->request->data['territory_id'];
		$product_id = $this->request->data['product_id'];
		$memo_total = $this->request->data['memo_total'];
		$min_qty = $this->request->data['min_qty'];
		
		$product_wise_cart_qty = $this->request->data['cart_product'];
		$product_wise_cart_value = $this->request->data['cart_product_value'];
		$product_rate_discount = $this->request->data['product_rate_discount'];
		$product_price_id_discount = $this->request->data['product_price_id_discount'];
		$outlet_category_id = $this->request->data['outlet_category_id'];
		$special_group = json_decode($this->request->data['special_group'], 1);
		if (empty($special_group)) {
			$special_group[] = 0;
		}
		$old_selected_bonus = json_decode($this->request->data['selected_bonus'], 1);
		$old_selected_set = json_decode($this->request->data['selected_set'], 1);
		$old_selected_policy_type = json_decode($this->request->data['selected_policy_type'], 1);
		$old_selected_option_id = json_decode($this->request->data['selected_option_id'], 1);
		
		$old_other_policy_info = json_decode($this->request->data['other_policy_info'], 1);
		
		$store_info = $this->Store->find('first', array(
			'conditions' => array(
				'Store.territory_id' => $territory_id,
				'Store.store_type_id' => 3
			),
			'recursive' => -1
		));
		$store_id = $store_info['Store']['id'];
		$conditions = array();
		$conditions['DiscountBonusPolicy.start_date <='] = $order_date;
		$conditions['DiscountBonusPolicy.end_date >='] = $order_date;
		$conditions['DiscountBonusPolicy.is_so'] = 1;
		$conditions['DiscountBonusPolicyOption.is_so'] = 1;
		$conditions['DiscountBonusPolicyProduct.product_id'] = array_keys($product_wise_cart_qty);

		$excluding_policy_id = $this->DiscountBonusPolicy->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'discount_bonus_policy_settings',
					'alias' => 'DiscountBonusPolicyOutletGroup',
					'type' => 'inner',
					'conditions' => '
						DiscountBonusPolicyOutletGroup.discount_bonus_policy_id=DiscountBonusPolicy.id 
						and DiscountBonusPolicyOutletGroup.for_so_sr=1
						and DiscountBonusPolicyOutletGroup.create_for=5
					'
				),
				array(
					'table' => 'outlet_group_to_outlets',
					'alias' => 'OutletGroup',
					'type' => 'inner',
					'conditions' => '
					DiscountBonusPolicyOutletGroup.reffrence_id=OutletGroup.outlet_group_id
						and OutletGroup.outlet_id=' . $outlet_id
				),
				array(
					'table' => 'discount_bonus_policy_products',
					'alias' => 'DiscountBonusPolicyProduct',
					'conditions' => 'DiscountBonusPolicyProduct.discount_bonus_policy_id=DiscountBonusPolicy.id'
				),
				array(
					'table' => 'discount_bonus_policy_options',
					'alias' => 'DiscountBonusPolicyOption',
					'conditions' => 'DiscountBonusPolicyOption.discount_bonus_policy_id=DiscountBonusPolicy.id'
				),

			),
			'group' => array(
				'DiscountBonusPolicy.id',
				'DiscountBonusPolicy.name',
				'DiscountBonusPolicy.start_date',
				'DiscountBonusPolicy.end_date',
			),
			'fields' => array(
				'DiscountBonusPolicy.id',
				'DiscountBonusPolicy.name',
				'DiscountBonusPolicy.start_date',
				'DiscountBonusPolicy.end_date',
			),
			'recursive' => -1,
		));
		$excluding_policy_id = array_map(function ($data) {
			return $data['DiscountBonusPolicy']['id'];
		}, $excluding_policy_id);


		$conditions['NOT']['DiscountBonusPolicy.id'] = $excluding_policy_id;
		$conditions['OR'] = array(
			'OutletGroup.id <>' => null,
			'DiscountBonusPolicySpecialGroup.id <>' => null,
			'AND' => array(
				'DiscountBonusPolicyOffice.reffrence_id' => $office_id,
				'DiscountBonusPolicyOutletCategory.reffrence_id' => $outlet_category_id,
				'DiscountBonusPolicyOutletCategory.id <>' => null,
			)
		);
		$policy_data = $this->DiscountBonusPolicy->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'discount_bonus_policy_settings',
					'alias' => 'DiscountBonusPolicyOffice',
					'type' => 'left',
					'conditions' => '
						DiscountBonusPolicyOffice.discount_bonus_policy_id=DiscountBonusPolicy.id 
						and DiscountBonusPolicyOffice.for_so_sr=1
						and DiscountBonusPolicyOffice.create_for=2
					'
				),
				array(
					'table' => 'discount_bonus_policy_settings',
					'alias' => 'DiscountBonusPolicyOutletCategory',
					'type' => 'left',
					'conditions' => '
						DiscountBonusPolicyOutletCategory.discount_bonus_policy_id=DiscountBonusPolicy.id 
						and DiscountBonusPolicyOutletCategory.for_so_sr=1
						and DiscountBonusPolicyOutletCategory.create_for=4
					'
				),
				array(
					'table' => 'discount_bonus_policy_settings',
					'alias' => 'DiscountBonusPolicyOutletGroup',
					'type' => 'left',
					'conditions' => '
						DiscountBonusPolicyOutletGroup.discount_bonus_policy_id=DiscountBonusPolicy.id 
						and DiscountBonusPolicyOutletGroup.for_so_sr=1
						and DiscountBonusPolicyOutletGroup.create_for=3
					'
				),
				array(
					'table' => 'outlet_group_to_outlets',
					'alias' => 'OutletGroup',
					'type' => 'Left',
					'conditions' => '
						DiscountBonusPolicyOutletGroup.reffrence_id=OutletGroup.outlet_group_id
						and OutletGroup.outlet_id=' . $outlet_id
				),
				array(
					'table' => 'discount_bonus_policy_settings',
					'alias' => 'DiscountBonusPolicySpecialGroup',
					'type' => 'left',
					'conditions' => '
						DiscountBonusPolicySpecialGroup.discount_bonus_policy_id=DiscountBonusPolicy.id 
						and DiscountBonusPolicySpecialGroup.for_so_sr=1
						and DiscountBonusPolicySpecialGroup.create_for=1
						and DiscountBonusPolicySpecialGroup.reffrence_id in (' . implode(',', $special_group) . ')
					'
				),
				array(
					'table' => 'discount_bonus_policy_products',
					'alias' => 'DiscountBonusPolicyProduct',
					'conditions' => 'DiscountBonusPolicyProduct.discount_bonus_policy_id=DiscountBonusPolicy.id'
				),
				array(
					'table' => 'discount_bonus_policy_options',
					'alias' => 'DiscountBonusPolicyOption',
					'conditions' => 'DiscountBonusPolicyOption.discount_bonus_policy_id=DiscountBonusPolicy.id'
				),
			),
			'group' => array(
				'DiscountBonusPolicy.id',
				'DiscountBonusPolicy.name',
				'DiscountBonusPolicy.start_date',
				'DiscountBonusPolicy.end_date',
			),
			'fields' => array(
				'DiscountBonusPolicy.id',
				'DiscountBonusPolicy.name',
				'DiscountBonusPolicy.start_date',
				'DiscountBonusPolicy.end_date',
			),
			'recursive' => -1,
		));
		$policy_id = array_map(function ($data) {
			return $data['DiscountBonusPolicy']['id'];
		}, $policy_data);
		$policy_product = $this->DiscountBonusPolicyProduct->find('all', array(
			'conditions' => array('DiscountBonusPolicyProduct.discount_bonus_policy_id' => $policy_id),
			'fields' => array('product_id', 'discount_bonus_policy_id'),
			'recursive' => -1
		));
		$policy_wise_product_data = array();
		foreach ($policy_product as $data) {
			$policy_wise_product_data[$data['DiscountBonusPolicyProduct']['discount_bonus_policy_id']][] = $data['DiscountBonusPolicyProduct']['product_id'];
		}
		$discount_array = array();
		$total_discount = 0;
		$bonus_html = '';
		$selected_bonus = array();
		$selected_set = array();
		$selected_policy_type = array();
		$selected_option_id = array();
		foreach ($policy_data as $p_data) {
			if (!$policy_wise_product_data[$p_data['DiscountBonusPolicy']['id']]) {
				continue;
			}
			$cart_combined_qty = 0;
			$cart_combined_value = 0;
			foreach ($policy_wise_product_data[$p_data['DiscountBonusPolicy']['id']] as $p_id) {
				$cart_combined_qty += isset($product_wise_cart_qty[$p_id]) ? $product_wise_cart_qty[$p_id] : 0;
				$cart_combined_value += isset($product_wise_cart_value[$p_id]) ? $product_wise_cart_value[$p_id] : 0;
			}
			$policy_option = $this->DiscountBonusPolicyOption->find('all', array(
				'conditions' => array(
					'DiscountBonusPolicyOption.discount_bonus_policy_id' => $p_data['DiscountBonusPolicy']['id'],
					'DiscountBonusPolicyOption.is_so' => 1,
					// 'DiscountBonusPolicyOption.min_qty_sale_unit <=' => $cart_combined_qty,
					//'CASE WHEN DiscountBonusPolicyOption.qty_value_flag =1 THEN  DiscountBonusPolicyOption.min_qty_sale_unit <='.$cart_combined_qty.' OR DiscountBonusPolicyOption.min_value <='.$cart_combined_value.' else DiscountBonusPolicyOption.min_qty_sale_unit <='.$cart_combined_qty.' AND DiscountBonusPolicyOption.min_value <='.$cart_combined_value.' END'
					'( (DiscountBonusPolicyOption.min_qty_sale_unit >0 and   DiscountBonusPolicyOption.min_qty_sale_unit <=' . $cart_combined_qty . ') OR DiscountBonusPolicyOption.min_value <=' . $cart_combined_value . ' )'
				),
				'order' => array(
					'DiscountBonusPolicyOption.min_qty desc',
					//'DiscountBonusPolicyOption.min_value desc' //----------for value 
				),
				'recursive' => -1
			));
			
			
			//echo $this->DiscountBonusPolicyOption->getLastQuery();exit;
			$effective_slab_index = null;

			foreach ($policy_option as $key => $slab_data) {

				$qty_value_flag =  $slab_data['DiscountBonusPolicyOption']['qty_value_flag'];
				//    echo $qty_value_flag == 0 .'--'.  $slab_data['DiscountBonusPolicyOption']['min_qty_sale_unit'].'--'.  $cart_combined_qty .'--'.  $slab_data['DiscountBonusPolicyOption']['min_value'] .'--'. $cart_combined_value;exit;
				if ($qty_value_flag == 0 && ($slab_data['DiscountBonusPolicyOption']['min_qty_sale_unit'] > $cart_combined_qty || $slab_data['DiscountBonusPolicyOption']['min_value'] > $cart_combined_value)) {
					continue;
				}
				$min_memo_value = $slab_data['DiscountBonusPolicyOption']['min_memo_value'];
				if ($min_memo_value && $min_memo_value > $memo_total) {
					continue;
				}
				$exclusion_product = $this->DiscountBonusPolicyOptionExclusionInclusionProduct->find('all', array(
					'conditions' => array(
						'DiscountBonusPolicyOptionExclusionInclusionProduct.discount_bonus_policy_option_id' => $slab_data['DiscountBonusPolicyOption']['id'],
						'DiscountBonusPolicyOptionExclusionInclusionProduct.create_for' => 1
					),
					'recursive' => -1
				));
				$is_exclusion = 0;
				foreach ($exclusion_product as $ex_data) {
					$ex_product_id = $ex_data['DiscountBonusPolicyOptionExclusionInclusionProduct']['product_id'];
					$ex_min_qty = $ex_data['DiscountBonusPolicyOptionExclusionInclusionProduct']['min_qty'];
					if ($ex_min_qty) {
						if (isset($product_wise_cart_qty[$ex_product_id]) && $product_wise_cart_qty[$ex_product_id] >= $ex_min_qty) {
							$is_exclusion = 1;
							break;
						}
					} else {
						if (isset($product_wise_cart_qty[$ex_product_id])) {
							$is_exclusion = 1;
							break;
						}
					}
				}
				if ($is_exclusion == 1) {
					continue;
				}
				$inclusion_product = $this->DiscountBonusPolicyOptionExclusionInclusionProduct->find('all', array(
					'conditions' => array(
						'DiscountBonusPolicyOptionExclusionInclusionProduct.discount_bonus_policy_option_id' => $slab_data['DiscountBonusPolicyOption']['id'],
						'DiscountBonusPolicyOptionExclusionInclusionProduct.create_for' => 2
					),
					'recursive' => -1
				));
				$is_inclusion = 1;
				foreach ($inclusion_product as $ex_data) {
					$in_product_id = $ex_data['DiscountBonusPolicyOptionExclusionInclusionProduct']['product_id'];
					$in_min_qty = $ex_data['DiscountBonusPolicyOptionExclusionInclusionProduct']['min_qty'];
					if ($in_min_qty) {
						if (isset($product_wise_cart_qty[$in_product_id]) && $product_wise_cart_qty[$in_product_id] < $in_min_qty) {
							$is_inclusion = 0;
							break;
						}
					} else {
						if (!isset($product_wise_cart_qty[$in_product_id])) {
							$is_inclusion = 0;
							break;
						}
					}
				}
				if ($is_inclusion == 0) {
					continue;
				}
				$effective_slab_index = $key;
				break;
			}
			if ($effective_slab_index === null) {
				continue;
			}
			
			
			$policy_id = $p_data['DiscountBonusPolicy']['id'];
			$option_id = $policy_option[$effective_slab_index]['DiscountBonusPolicyOption']['id'];
			$policy_type = $policy_option[$effective_slab_index]['DiscountBonusPolicyOption']['policy_type'];
			
			/*if (isset($old_selected_option_id[$policy_id]) && $old_selected_option_id[$policy_id] != $option_id) {
				unset($old_selected_bonus[$policy_id]);
				unset($old_selected_set[$policy_id]);
				unset($old_selected_policy_type[$policy_id]);
			}*/
			
			//-----------new-------------\\
			if (isset($old_other_policy_info[$policy_id]) && $old_other_policy_info[$policy_id]['selected_option_id'] != $policy_option[$effective_slab_index]['DiscountBonusPolicyOption']['id']) {
                unset($old_selected_bonus[$policy_id]);
                unset($old_selected_set[$policy_id]);
                unset($old_selected_policy_type[$policy_id]);
            }
            $other_policy_info[$policy_id]['selected_option_id'] = $policy_option[$effective_slab_index]['DiscountBonusPolicyOption']['id'];
			//------------end--------------\\
			
			$selected_option_id[$policy_id] = $option_id;
			
			
			
			if ($policy_type == 0 || $policy_type == 2) {
				$discount_array[] = $this->create_discount_array($policy_option[$effective_slab_index], $product_wise_cart_qty, $total_discount, $product_rate_discount, $product_price_id_discount, $cart_combined_qty);
			}
			if ($policy_type == 1 || $policy_type == 2) {
				$bonus_html .= '<tr class="n_bonus_row"><th colspan="4">' . $p_data['DiscountBonusPolicy']['name'] . '</th><tr>';
				$bonus_html .= $this->create_bonus_html($store_id, $product_wise_cart_qty, $policy_option[$effective_slab_index], $cart_combined_qty, $selected_bonus, $old_selected_bonus, $selected_set, $old_selected_set, $other_policy_info, $old_other_policy_info, $cart_combined_value);
			} elseif ($policy_type == 3) {
				
				$policy_id = $p_data['DiscountBonusPolicy']['id'];
				
				$selected_policy_type_var = 1;
				
				if (isset($old_selected_policy_type[$policy_id])) {
					$selected_policy_type_var = $old_selected_policy_type[$policy_id];
				}
				$selected_policy_type[$policy_id] = $selected_policy_type_var;
				if ($selected_policy_type_var == 1) {
					$btn_type_1 = 'btn-primary';
					$btn_type_2 = 'btn-basic';
				} else {
					$btn_type_1 = 'btn-basic';
					$btn_type_2 = 'btn-primary';
				}
				$bonus_html .= '<tr class="n_bonus_row"><th colspan="4">' . $p_data['DiscountBonusPolicy']['name'] . '</th><tr>';
				$bonus_html .=
					'<tr class="n_bonus_row">
					<th colspan="4">
						<button class="btn ' . $btn_type_1 . ' btn_type" data-type="1" data-policy_id="' . $policy_id . '">Discount</button>
						<button class="btn ' . $btn_type_2 . ' btn_type" data-type="2"  data-policy_id="' . $policy_id . '">Bonus</button>
					</th>
				<tr>';
				
				if ($selected_policy_type_var == 1) {
					$discount_array[] = $this->create_discount_array($policy_option[$effective_slab_index], $product_wise_cart_qty, $total_discount, $product_rate_discount, $product_price_id_discount, $cart_combined_qty);
				} elseif ($selected_policy_type_var == 2) {
					$bonus_html .= $this->create_bonus_html($store_id, $product_wise_cart_qty, $policy_option[$effective_slab_index], $cart_combined_qty, $selected_bonus, $old_selected_bonus, $selected_set, $old_selected_set, $other_policy_info, $old_other_policy_info, $cart_combined_value);
				}
			}
		}
		$result = array();
		$result['discount'] = $discount_array;
		$result['total_discount'] = $total_discount;
		$result['bonus_html'] = $bonus_html;
		$result['selected_bonus'] = $selected_bonus;
		$result['selected_set'] = $selected_set;
		$result['selected_policy_type'] = $selected_policy_type;
		$result['selected_option_id'] = $selected_option_id;
		$result['other_policy_info'] = $other_policy_info;
		echo json_encode($result);
		//exit;
		$this->autoRender = false;
	}
	private function create_discount_array($policy_option, $product_wise_cart_qty, &$total_discount, $product_rate_discount, $product_price_id_discount, $cart_combined_qty)
	{
		$this->loadModel('DiscountBonusPolicyOptionPriceSlab');
		$conditions = array();
		$conditions['DiscountBonusPolicyOptionPriceSlab.discount_bonus_policy_option_id'] = $policy_option['DiscountBonusPolicyOption']['id'];

		$discount_amount = $policy_option['DiscountBonusPolicyOption']['discount_amount'];
		$policy_id = $policy_option['DiscountBonusPolicyOption']['discount_bonus_policy_id'];
		$discount_type = $policy_option['DiscountBonusPolicyOption']['disccount_type'];
		$policy_type = $policy_option['DiscountBonusPolicyOption']['policy_type'];
		$deduct_from_value = $policy_option['DiscountBonusPolicyOption']['deduct_from_value'];
		$deduct_from_value = $policy_option['DiscountBonusPolicyOption']['deduct_from_value'];
		$prodcutncount = count($product_wise_cart_qty);
		$discount_array = array();


		$this->loadModel('DiscountBonusPolicyProduct');

		$policy_product = $this->DiscountBonusPolicyProduct->find('list', array(
			'conditions' => array('DiscountBonusPolicyProduct.discount_bonus_policy_id' => $policy_id),
			'fields' => array('product_id', 'discount_bonus_policy_id'),
			'recursive' => -1
		));

		//echo $cart_combined_qty;exit;

		if ($deduct_from_value == 1) {

			//foreach ($product_price_id_discount as $product_id => $price_id_val) {
			foreach ($policy_product as $product_id => $discount_id) {

				$price_id_val = $product_price_id_discount[$product_id];


				if ($discount_type == 0) {
					$discount_amount_price = ($product_rate_discount[$product_id] * $discount_amount / 100);
				} else {
					$discount_amount_price = $discount_amount / $cart_combined_qty;
				}

				$discount_value = $discount_amount_price * $product_wise_cart_qty[$product_id];
				$total_discount += $discount_value;
				$discount_array[] = array(
					'product_id' => $product_id,
					'policy_id' => $policy_id,
					'policy_type' => $policy_type,
					'discount_type' => $discount_type,
					'discount_amount' => $discount_amount_price,
					'price' => $product_rate_discount[$product_id],
					'total_value' => sprintf("%0.2f", $product_rate_discount[$product_id] * $product_wise_cart_qty[$product_id]),
					'price_id' => $price_id_val,
					'total_discount_value' => sprintf("%0.2f", $discount_value),
				);
			}
		} else {
			$price_slabs = $this->DiscountBonusPolicyOptionPriceSlab->find('all', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'table' => 'product_combinations_v2',
						'alias' => 'PC',
						'type' => 'inner',
						'conditions' => 'PC.id=DiscountBonusPolicyOptionPriceSlab.so_slab_id'
					)
				),
				'fields' => array('PC.price', 'DiscountBonusPolicyOptionPriceSlab.discount_product_id', 'PC.id'),
				'recursive' => -1
			));

			foreach ($price_slabs as $pr_slab_data) {
				if (isset($product_wise_cart_qty[$pr_slab_data['DiscountBonusPolicyOptionPriceSlab']['discount_product_id']])) {

					if ($discount_type == 0) {
						$discount_amount_price = ($pr_slab_data['PC']['price'] * $discount_amount / 100);
					} else {
						$discount_amount_price = $discount_amount;
					}

					$discount_value = $discount_amount_price * $product_wise_cart_qty[$pr_slab_data['DiscountBonusPolicyOptionPriceSlab']['discount_product_id']];
					$total_discount += $discount_value;
					$discount_array[] = array(
						'product_id' => $pr_slab_data['DiscountBonusPolicyOptionPriceSlab']['discount_product_id'],
						'policy_id' => $policy_id,
						'policy_type' => $policy_type,
						'discount_type' => $discount_type,
						'discount_amount' => $discount_amount_price,
						'price' => $pr_slab_data['PC']['price'],
						'total_value' => sprintf("%0.2f", $pr_slab_data['PC']['price'] * $product_wise_cart_qty[$pr_slab_data['DiscountBonusPolicyOptionPriceSlab']['discount_product_id']]),
						'price_id' => $pr_slab_data['PC']['id'],
						'total_discount_value' => sprintf("%0.2f", $discount_value),
					);
				}
			}
		}

		return $discount_array;
	}



	private function create_bonus_html($store_id, $cart_qty, $policy_option, $combined_qty, &$selected_bonus, $old_selected_bonus, &$selected_set, $old_selected_set, $other_policy_info, $old_other_policy_info, $cart_combined_value)
	{
		$this->loadModel('DiscountBonusPolicyDefaultBonusProductSelection');
		$this->loadModel('DiscountBonusPolicyOptionBonusProduct');
		$this->loadModel('CurrentInventory');
		$this->loadModel('Product');



		$con_dbpdbps['DiscountBonusPolicyDefaultBonusProductSelection.discount_bonus_policy_option_id'] = $policy_option['DiscountBonusPolicyOption']['id'];
		$default_product = $this->DiscountBonusPolicyDefaultBonusProductSelection->find('all', array(
			'conditions' => $con_dbpdbps,
		));

		$defaut_product_ids = array();
		if (!empty($default_product)) {
			foreach ($default_product as $v) {
				$defaut_product_ids[$v['DiscountBonusPolicyDefaultBonusProductSelection']['product_id']] = $v['DiscountBonusPolicyDefaultBonusProductSelection']['product_id'];
			}
		}

		//echo '<pre>';print_r($default_product);exit;

		$conditions['DiscountBonusPolicyOptionBonusProduct.discount_bonus_policy_option_id'] = $policy_option['DiscountBonusPolicyOption']['id'];
		$bonus_product = $this->DiscountBonusPolicyOptionBonusProduct->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'conditions' => 'Product.id=DiscountBonusPolicyOptionBonusProduct.bonus_product_id'
				),
				array(
					'table' => 'measurement_units',
					'alias' => 'MU',
					'conditions' => 'MU.id=DiscountBonusPolicyOptionBonusProduct.measurement_unit_id'
				),
			),
			'fields' => array(
				'DiscountBonusPolicyOptionBonusProduct.*',
				'Product.name',
				'MU.name',
			),
			'recursive' => -1
		));
		$formula = $policy_option['DiscountBonusPolicyOption']['bonus_formula_text_with_product_id'];
		$min_qty = $policy_option['DiscountBonusPolicyOption']['min_qty_sale_unit'];
		$policy_id = $policy_option['DiscountBonusPolicyOption']['discount_bonus_policy_id'];
		$policy_type = $policy_option['DiscountBonusPolicyOption']['policy_type'];
		$b_html = '';
		
		if (!$formula) {
			

			$s_disabled = 'readonly';
			// $i=0;
			$given_qty = 0;
			foreach ($bonus_product as $data) {

				$product_id = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'];
				$bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_qty'];
				
				//--------------------min value---------------\\
				/*if( $policy_option['DiscountBonusPolicyOption']['min_qty'] <= 0  AND $policy_option['DiscountBonusPolicyOption']['min_value'] > 0 ){
					
					$provide_bonus_qty = floor(($cart_combined_value * $bonus_qty) / $policy_option['DiscountBonusPolicyOption']['min_value']);

				}else{
					$provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
				}*/
				//-------------------end-----------------\\
				
				$provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
				
				//----------------new--------------\\
				if (isset($old_other_policy_info[$policy_id]) && $old_other_policy_info[$policy_id]['provided_bonus_qty'] != $provide_bonus_qty) {
                    unset($old_selected_bonus[$policy_id]);
                    unset($old_selected_set[$policy_id]);
                }
             
                $other_policy_info[$policy_id]['provided_bonus_qty'] = $provide_bonus_qty;
				//---------------------end--------------\\
				
				$stock_qty = $this->CurrentInventory->find('first', array(
					'conditions' => array(
						'store_id' => $store_id,
						'product_id' => $product_id
					),
					'fields' => array('sum(qty) as qty'),
					'recursive' => -1
				));
				$prod_info = $this->Product->find('first', array(
					'conditions' => array('id' => $product_id),
					'recursive' => -1
				));



				if ($prod_info['Product']['is_virtual'] == 1) {

					$pd_name_replace = $this->get_product_inventroy_check($store_id, $prod_info['Product']['id'], $prod_info['Product']['parent_id']);

					if ($pd_name_replace == 1) {
						$pdname = $this->get_parent_virtual_pd_info($product_id);

						$data['Product']['name'] = $pdname['VirtualProduct']['name'];
					}
				}


				$cart_qty = isset($cart_qty[$product_id]) ? $cart_qty[$product_id] : 0;
				$cart_qty = $this->unit_convert($product_id, $prod_info['Product']['sales_measurement_unit_id'], $cart_qty);
				$stock_qty = $this->unit_convertfrombase($product_id, $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'], ($stock_qty[0]['qty'] - $cart_qty));
				if (isset($old_selected_bonus[$policy_id])) {

					if (isset($old_selected_bonus[$policy_id][1][$product_id]) && $old_selected_bonus[$policy_id][1][$product_id] > 0) {
						$value = $old_selected_bonus[$policy_id][1][$product_id];
						$min_qty_disabled = '';
						$min_qty_checked = 'checked';
						$min_qty_required = 'required';
					} else {
						$value = 0;
						$min_qty_disabled = 'readonly';
						$min_qty_checked = '';
						$min_qty_required = '';
					}
				} else {
					if ($stock_qty > 0 && $given_qty != $provide_bonus_qty && @$defaut_product_ids[$product_id] > 0) {
						if ($stock_qty < ($provide_bonus_qty - $given_qty)) {
							$given_qty += $stock_qty;
							$value = $stock_qty;
						} elseif ($stock_qty > ($provide_bonus_qty - $given_qty)) {
							$value = ($provide_bonus_qty - $given_qty);
							$given_qty += ($provide_bonus_qty - $given_qty);
						}
						$min_qty_disabled = '';
						$min_qty_checked = 'checked';
						$min_qty_required = 'required';
					} else {
						$value = 0;
						$min_qty_disabled = 'readonly';
						$min_qty_checked = '';
						$min_qty_required = '';
					}
				}
				$selected_bonus[$policy_id]['1'][$product_id] = $value;
				$b_html .= '
					<tr class="bonus_row n_bonus_row bonus_policy_id_' . $policy_id . '">
						<th class="text-center" id="bonus_product_list">
							<div class="input select">
								<select ' . $s_disabled . ' name="data[MemoDetail][product_id][]" class="form-control width_100_this policy_bonus_product_id">
								<option value="' . $product_id . '">' . $data['Product']['name'] . '</option>
								</select>
							</div>
						</th>
						<th class="text-center" width="12%">
							<input ' . $s_disabled . ' type="text" name="" class="form-control width_100_this open_bonus_product_unit_name" value="' . $data['MU']['name'] . '" readonly disabled="">
							<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="open_bonus_product_unit_id" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'] . '">
							<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][discount_amount][]" class="form-control width_100 discount_amount" value="" />
							<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][disccount_type][]" class="form-control width_100 disccount_type" value=""/>
							<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][policy_type][]" class="policy_type" value="' . $policy_type . '">
							<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][policy_id][]" class="policy_id" value="' . $policy_id . '">
							<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][is_bonus][]" class="is_bonus" value="3">
							<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][Price][]" class="form-control width_100_this open_bonus_product_rate" value="0.0">
							<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100_this open_bonus_product_price_id" value="">
							<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][combination_id][]" step="any" class="combination_id" value="">
							<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][selected_set][' . $policy_id . ']" step="any" class="set_id" value="1">
						</th>
						<th class="text-center" width="12%">
							<input ' . $min_qty_disabled . ' ' . $min_qty_required . ' data-set="1" data-stock="' . $stock_qty . '" type="number" min="1" max="' . $provide_bonus_qty . '" name="data[MemoDetail][sales_qty][]" class="width_100_this policy_min_qty ' . $policy_id . '_policy_set_1" value="' . $value . '">
						</th>
						<th class="text-center" width="10%">
							<input type="checkbox" ' . $min_qty_checked . ' class="is_bonus_checked" name="data[MemoDetail][bonus_check][]" value="' . $product_id . '">
						</th>
					</tr>';
			}
		} else {
			
			$parsed_fourmula = $this->parse_formla($formula);
			$product_wise_bonus = array();
			foreach ($bonus_product as $data) {
				$product_wise_bonus[$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] = $data;
			}
			if ($parsed_fourmula['set_relation'] == 'AND' || $parsed_fourmula['set_relation'] == '') {
				
				foreach ($parsed_fourmula['formula_product'] as $set => $formula_product) {
					$b_html .= '<tr class="n_bonus_row set"' . $set . '><th colspan="4">Set - ' . $set . '</th><tr>';
					$element_relation = $parsed_fourmula['element_relation'][$set];
					if ($element_relation == 'OR') {
						
						$s_disabled = 'readonly';
						$given_qty = 0;
						foreach ($formula_product as $product_id) {
							$data = $product_wise_bonus[$product_id];
							$bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_qty'];
							
							
							//--------------------min value---------------\\
							/*if( $policy_option['DiscountBonusPolicyOption']['min_qty'] <= 0  AND $policy_option['DiscountBonusPolicyOption']['min_value'] > 0 ){
								
								$provide_bonus_qty = floor(($cart_combined_value * $bonus_qty) / $policy_option['DiscountBonusPolicyOption']['min_value']);

							}else{
								$provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
							}*/
							//-------------------end-----------------\\
							
							$provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
							
							//----------------new--------------\\
							if (isset($old_other_policy_info[$policy_id]) && $old_other_policy_info[$policy_id]['provided_bonus_qty'] != $provide_bonus_qty) {
								unset($old_selected_bonus[$policy_id]);
								unset($old_selected_set[$policy_id]);
							}
						 
							$other_policy_info[$policy_id]['provided_bonus_qty'] = $provide_bonus_qty;
							//---------------------end--------------\\
							
							$stock_qty = $this->CurrentInventory->find('first', array(
								'conditions' => array(
									'store_id' => $store_id,
									'product_id' => $product_id
								),
								'fields' => array('sum(qty) as qty'),
								'recursive' => -1
							));
							$prod_info = $this->Product->find('first', array(
								'conditions' => array('id' => $product_id),
								'recursive' => -1
							));

							if ($prod_info['Product']['is_virtual'] == 1) {

								$pd_name_replace = $this->get_product_inventroy_check($store_id, $prod_info['Product']['id'], $prod_info['Product']['parent_id']);

								if ($pd_name_replace == 1) {
									$pdname = $this->get_parent_virtual_pd_info($product_id);

									$data['Product']['name'] = $pdname['VirtualProduct']['name'];
								}
							}

							$cart_qty = isset($cart_qty[$product_id]) ? $cart_qty[$product_id] : 0;
							$cart_qty = $this->unit_convert($product_id, $prod_info['Product']['sales_measurement_unit_id'], $cart_qty);
							$stock_qty = $this->unit_convertfrombase($product_id, $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'], ($stock_qty[0]['qty'] - $cart_qty));
							if (isset($old_selected_bonus[$policy_id])) {
								if (isset($old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']]) && $old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] > 0) {
									$value = $old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']];
									$min_qty_disabled = '';
									$min_qty_checked = 'checked';
									$min_qty_required = 'required';
								} else {
									$value = 0;
									$min_qty_disabled = 'readonly';
									$min_qty_checked = '';
									$min_qty_required = '';
								}
							} else {
								if ($stock_qty > 0 && $given_qty != $provide_bonus_qty && @$defaut_product_ids[$product_id] > 0) {
									if ($stock_qty < ($provide_bonus_qty - $given_qty)) {
										$given_qty += $stock_qty;
										$value = $stock_qty;
									} elseif ($stock_qty > ($provide_bonus_qty - $given_qty)) {
										$value = ($provide_bonus_qty - $given_qty);
										$given_qty += ($provide_bonus_qty - $given_qty);
									}
									$min_qty_disabled = '';
									$min_qty_checked = 'checked';
									$min_qty_required = 'required';
								} else {
									$value = 0;
									$min_qty_disabled = 'readonly';
									$min_qty_checked = '';
									$min_qty_required = '';
								}
							}
							$selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] = $value;
							$b_html .= '
								<tr class="bonus_row n_bonus_row bonus_policy_id_' . $policy_id . '">
									<th class="text-center" id="bonus_product_list">
										<div class="input select">
											<select ' . $s_disabled . ' name="data[MemoDetail][product_id][]" class="form-control width_100_this policy_bonus_product_id">
											<option value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">' . $data['Product']['name'] . '</option>
											</select>
										</div>
									</th>
									<th class="text-center" width="12%">
										<input ' . $s_disabled . ' type="text" name="" class="form-control width_100_this open_bonus_product_unit_name" value="' . $data['MU']['name'] . '" readonly disabled="">
										<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="open_bonus_product_unit_id" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'] . '">
										<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][discount_amount][]" class="form-control width_100 discount_amount" value="" />
										<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][disccount_type][]" class="form-control width_100 disccount_type" value=""/>
										<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][policy_type][]" class="policy_type" value="' . $policy_type . '">
										<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][policy_id][]" class="policy_id" value="' . $policy_id . '">
										<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][is_bonus][]" class="is_bonus" value="3">
										<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][Price][]" class="form-control width_100_this open_bonus_product_rate" value="0.0">
										<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100_this open_bonus_product_price_id" value="">
										<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][combination_id][]" step="any" class="combination_id" value="">
										<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][selected_set][' . $policy_id . ']" step="any" class="set_id" value="' . $set . '">
									</th>
									<th class="text-center" width="12%">
										<input ' . $min_qty_disabled . ' ' . $min_qty_required . ' data-set="' . $set . '" data-stock="' . $stock_qty . '" type="number" min="1" max="' . $provide_bonus_qty . '" name="data[MemoDetail][sales_qty][]" class="width_100_this policy_min_qty ' . $policy_id . '_policy_set_' . $set . '" value="' . $value . '">
									</th>
									<th class="text-center" width="10%">
										<input type="checkbox" ' . $min_qty_checked . ' class="is_bonus_checked" name="data[MemoDetail][bonus_check][]" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">
									</th>
								</tr>';
						}
					} elseif ($element_relation == 'AND') {
						$i = 0;
						$s_disabled = 'readonly';
						foreach ($formula_product as $product_id) {
							$data = $product_wise_bonus[$product_id];
							$bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_qty'];
							
							
							//--------------------min value---------------\\
							/*if( $policy_option['DiscountBonusPolicyOption']['min_qty'] <= 0  AND $policy_option['DiscountBonusPolicyOption']['min_value'] > 0 ){
								
								$provide_bonus_qty = floor(($cart_combined_value * $bonus_qty) / $policy_option['DiscountBonusPolicyOption']['min_value']);

							}else{
								$provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
							}*/
							//-------------------end-----------------\\
							
							$provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
							
							//----------------new--------------\\
							if (isset($old_other_policy_info[$policy_id]) && $old_other_policy_info[$policy_id]['provided_bonus_qty'] != $provide_bonus_qty) {
								unset($old_selected_bonus[$policy_id]);
								unset($old_selected_set[$policy_id]);
							}
						 
							$other_policy_info[$policy_id]['provided_bonus_qty'] = $provide_bonus_qty;
							//---------------------end--------------\\

							$value = $provide_bonus_qty;
							$min_qty_disabled = 'readonly';
							$min_qty_checked = 'checked';
							$min_qty_required = 'required';


							$prod_info = $this->Product->find('first', array(
								'conditions' => array('id' => $product_id),
								'recursive' => -1
							));

							if ($prod_info['Product']['is_virtual'] == 1) {

								$pd_name_replace = $this->get_product_inventroy_check($store_id, $prod_info['Product']['id'], $prod_info['Product']['parent_id']);

								if ($pd_name_replace == 1) {
									$pdname = $this->get_parent_virtual_pd_info($product_id);

									$data['Product']['name'] = $pdname['VirtualProduct']['name'];
								}
							}



							$selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] = $value;
							$i++;
							$b_html .= '
								<tr class="bonus_row n_bonus_row bonus_policy_id_' . $policy_id . '">
									<th class="text-center" id="bonus_product_list">
										<div class="input select">
											<select ' . $s_disabled . ' name="data[MemoDetail][product_id][]" class="form-control width_100_this policy_bonus_product_id">
											<option value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">' . $data['Product']['name'] . '</option>
											</select>
										</div>
									</th>
									<th class="text-center" width="12%">
										<input ' . $s_disabled . ' type="text" name="" class="form-control width_100_this open_bonus_product_unit_name" value="' . $data['MU']['name'] . '" readonly disabled="">
										<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="open_bonus_product_unit_id" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'] . '">
										<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][discount_amount][]" class="form-control width_100 discount_amount" value="" />
										<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][disccount_type][]" class="form-control width_100 disccount_type" value=""/>
										<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][policy_type][]" class="policy_type" value="' . $policy_type . '">
										<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][policy_id][]" class="policy_id" value="' . $policy_id . '">
										<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][is_bonus][]" class="is_bonus" value="3">
										<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][Price][]" class="form-control width_100_this open_bonus_product_rate" value="0.0">
										<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100_this open_bonus_product_price_id" value="">
										<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][combination_id][]" step="any" class="combination_id" value="">
										<input ' . $s_disabled . ' type="hidden" name="data[MemoDetail][selected_set][' . $policy_id . ']" step="any" class="set_id" value="' . $set . '">
									</th>
									<th class="text-center" width="12%">
										<input ' . $min_qty_disabled . ' ' . $min_qty_required . ' data-set="' . $set . '" type="number" min="1" max="' . $provide_bonus_qty . '" name="data[MemoDetail][sales_qty][]" class="width_100_this policy_min_qty ' . $policy_id . '_policy_set_' . $set . '" value="' . $value . '">
									</th>
									<th class="text-center" width="10%">
										<input type="checkbox" disabled ' . $min_qty_checked . ' class="is_bonus_checked" name="data[MemoDetail][bonus_check][]" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">
									</th>
								</tr>';
						}
					}
				}
			} elseif ($parsed_fourmula['set_relation'] == 'OR') {
				$selected_set_var = 1;
				
				
				
				if (isset($old_selected_set[$policy_id])) {
					$selected_set_var = $old_selected_set[$policy_id];
				}
				$selected_set[$policy_id] = $selected_set_var;
				if ($selected_set_var == 1) {
					$btn_set_1 = 'btn-success';
					$btn_set_2 = 'btn-default';
				} else {
					$btn_set_1 = 'btn-default';
					$btn_set_2 = 'btn-success';
				}
				$b_html .=
					'<tr class="n_bonus_row set">
					<th colspan="4" class="text-center">
						<button class="btn ' . $btn_set_1 . ' btn_set" data-set="1" data-policy_id="' . $policy_id . '">Set-1</button>
						<button class="btn ' . $btn_set_2 . ' btn_set" data-set="2" data-policy_id="' . $policy_id . '">Set-2</button>
					</th>
				<tr>';
				foreach ($parsed_fourmula['formula_product'] as $set => $formula_product) {
					$element_relation = $parsed_fourmula['element_relation'][$set];
					$display_none = 'display_none';
					$disabled = 'disabled';
					if ($selected_set_var == $set) {
						$display_none = '';
						$disabled = '';
					}
					if ($element_relation == 'OR') {
						
						
						$s_disabled = 'readonly';
						$given_qty = 0;
						foreach ($formula_product as $product_id) {
							$data = $product_wise_bonus[$product_id];
							$bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_qty'];
							
							//--------------------min value---------------\\
							/*if( $policy_option['DiscountBonusPolicyOption']['min_qty'] <= 0  AND $policy_option['DiscountBonusPolicyOption']['min_value'] > 0 ){
								
								$provide_bonus_qty = floor(($cart_combined_value * $bonus_qty) / $policy_option['DiscountBonusPolicyOption']['min_value']);

							}else{
								$provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
							}*/
							//-------------------end-----------------\\
							
							$provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
							
							//----------------new--------------\\
							if (isset($old_other_policy_info[$policy_id]) && $old_other_policy_info[$policy_id]['provided_bonus_qty'] != $provide_bonus_qty) {
								unset($old_selected_bonus[$policy_id]);
								unset($old_selected_set[$policy_id]);
							}
						 
							$other_policy_info[$policy_id]['provided_bonus_qty'] = $provide_bonus_qty;
							//---------------------end--------------\\
							
							$stock_qty = $this->CurrentInventory->find('first', array(
								'conditions' => array(
									'store_id' => $store_id,
									'product_id' => $product_id
								),
								'fields' => array('sum(qty) as qty'),
								'recursive' => -1
							));
							$prod_info = $this->Product->find('first', array(
								'conditions' => array('id' => $product_id),
								'recursive' => -1
							));


							if ($prod_info['Product']['is_virtual'] == 1) {

								$pd_name_replace = $this->get_product_inventroy_check($store_id, $prod_info['Product']['id'], $prod_info['Product']['parent_id']);

								if ($pd_name_replace == 1) {
									$pdname = $this->get_parent_virtual_pd_info($product_id);

									$data['Product']['name'] = $pdname['VirtualProduct']['name'];
								}
							}

							$cart_qty = isset($cart_qty[$product_id]) ? $cart_qty[$product_id] : 0;
							$cart_qty = $this->unit_convert($product_id, $prod_info['Product']['sales_measurement_unit_id'], $cart_qty);
							$stock_qty = $this->unit_convertfrombase($product_id, $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'], ($stock_qty[0]['qty'] - $cart_qty));
							if (isset($old_selected_bonus[$policy_id])) {
								if (isset($old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']]) && $old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] > 0) {
									$value = $old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']];
									$min_qty_disabled = '';
									$min_qty_checked = 'checked';
									$min_qty_required = 'required';
								} else {
									$value = 0;
									$min_qty_disabled = 'readonly';
									$min_qty_checked = '';
									$min_qty_required = '';
								}
							} else {
								if ($stock_qty > 0 && $given_qty != $provide_bonus_qty && @$defaut_product_ids[$product_id] > 0) {
									if ($stock_qty < ($provide_bonus_qty - $given_qty)) {
										$given_qty += $stock_qty;
										$value = $stock_qty;
									} elseif ($stock_qty > ($provide_bonus_qty - $given_qty)) {
										$value = ($provide_bonus_qty - $given_qty);
										$given_qty += ($provide_bonus_qty - $given_qty);
									}
									$min_qty_disabled = '';
									$min_qty_checked = 'checked';
									$min_qty_required = 'required';
								} else {
									$value = 0;
									$min_qty_disabled = 'readonly';
									$min_qty_checked = '';
									$min_qty_required = '';
								}
							}
							$selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] = $value;
							$b_html .= '
								<tr class="bonus_row n_bonus_row ' . $display_none . ' bonus_policy_id_' . $policy_id . ' set_' . $set . '">
									<th class="text-center" id="bonus_product_list">
										<div class="input select">
											<select ' . $s_disabled . ' ' . $disabled . ' name="data[MemoDetail][product_id][]" class="form-control width_100_this policy_bonus_product_id">
											<option value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">' . $data['Product']['name'] . '</option>
											</select>
										</div>
									</th>
									<th class="text-center" width="12%">
										<input ' . $s_disabled . ' ' . $disabled . ' type="text" name="" class="form-control width_100_this open_bonus_product_unit_name" value="' . $data['MU']['name'] . '" readonly disabled="">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="open_bonus_product_unit_id" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'] . '">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[MemoDetail][discount_amount][]" class="form-control width_100 discount_amount" value="" />
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[MemoDetail][disccount_type][]" class="form-control width_100 disccount_type" value=""/>
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[MemoDetail][policy_type][]" class="policy_type" value="' . $policy_type . '">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[MemoDetail][policy_id][]" class="policy_id" value="' . $policy_id . '">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[MemoDetail][is_bonus][]" class="is_bonus" value="3">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[MemoDetail][Price][]" class="form-control width_100_this open_bonus_product_rate" value="0.0">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100_this open_bonus_product_price_id" value="">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[MemoDetail][combination_id][]" step="any" class="combination_id" value="">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[MemoDetail][selected_set][' . $policy_id . ']" step="any" class="set_id" value="' . $set . '">
									</th>
									<th class="text-center" width="12%">
										<input ' . $min_qty_disabled . ' ' . $disabled . ' ' . $min_qty_required . ' data-set="' . $set . '"  data-stock="' . $stock_qty . '" type="number" min="1" max="' . $provide_bonus_qty . '" name="data[MemoDetail][sales_qty][]" class="width_100_this policy_min_qty ' . $policy_id . '_policy_set_' . $set . '" value="' . $value . '">
									</th>
									<th class="text-center" width="10%">
										<input type="checkbox" ' . $min_qty_checked . '  class="is_bonus_checked" name="data[MemoDetail][bonus_check][]" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">
									</th>
								</tr>';
						}
					} elseif ($element_relation == 'AND') {
						$i = 0;
						$s_disabled = 'readonly';
						
						
						foreach ($formula_product as $product_id) {
							$data = $product_wise_bonus[$product_id];
							$bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_qty'];
							
							
								//--------------------min value---------------\\
								/*if( $policy_option['DiscountBonusPolicyOption']['min_qty'] <= 0  AND $policy_option['DiscountBonusPolicyOption']['min_value'] > 0 ){
									
									$provide_bonus_qty = floor(($cart_combined_value * $bonus_qty) / $policy_option['DiscountBonusPolicyOption']['min_value']);

								}else{
									$provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
								}*/
								//-------------------end-----------------\\
								
								$provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
							
							//----------------new--------------\\
							if (isset($old_other_policy_info[$policy_id]) && $old_other_policy_info[$policy_id]['provided_bonus_qty'] != $provide_bonus_qty) {
								unset($old_selected_bonus[$policy_id]);
								unset($old_selected_set[$policy_id]);
							}
						 
							$other_policy_info[$policy_id]['provided_bonus_qty'] = $provide_bonus_qty;
							//---------------------end--------------\\

							$value = $provide_bonus_qty;
							$min_qty_disabled = 'readonly';
							$min_qty_checked = 'checked';
							$min_qty_required = 'required';


							$prod_info = $this->Product->find('first', array(
								'conditions' => array('id' => $product_id),
								'recursive' => -1
							));

							if ($prod_info['Product']['is_virtual'] == 1) {

								$pd_name_replace = $this->get_product_inventroy_check($store_id, $prod_info['Product']['id'], $prod_info['Product']['parent_id']);

								if ($pd_name_replace == 1) {
									$pdname = $this->get_parent_virtual_pd_info($product_id);

									$data['Product']['name'] = $pdname['VirtualProduct']['name'];
								}
							}



							$selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] = $value;
							$i++;
							$b_html .= '
								<tr class="bonus_row n_bonus_row ' . $display_none . ' bonus_policy_id_' . $policy_id . ' set_' . $set . '">
									<th class="text-center" id="bonus_product_list">
										<div class="input select">
											<select ' . $s_disabled . ' ' . $disabled . ' name="data[MemoDetail][product_id][]" class="form-control width_100_this policy_bonus_product_id">
											<option value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">' . $data['Product']['name'] . '</option>
											</select>
										</div>
									</th>
									<th class="text-center" width="12%">
										<input ' . $s_disabled . ' ' . $disabled . ' type="text" name="" class="form-control width_100_this open_bonus_product_unit_name" value="' . $data['MU']['name'] . '" disabled="">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[MemoDetail][measurement_unit_id][]" class="open_bonus_product_unit_id" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'] . '">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[MemoDetail][discount_amount][]" class="form-control width_100 discount_amount" value="" />
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[MemoDetail][disccount_type][]" class="form-control width_100 disccount_type" value=""/>
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[MemoDetail][policy_type][]" class="policy_type" value="' . $policy_type . '">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[MemoDetail][policy_id][]" class="policy_id" value="' . $policy_id . '">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[MemoDetail][is_bonus][]" class="is_bonus" value="3">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[MemoDetail][Price][]" class="form-control width_100_this open_bonus_product_rate" value="0.0">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[MemoDetail][product_price_id][]" class="form-control width_100_this open_bonus_product_price_id" value="">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[MemoDetail][combination_id][]" step="any" class="combination_id" value="">
										<input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[MemoDetail][selected_set][' . $policy_id . ']" step="any" class="set_id" value="' . $set . '">
									</th>
									<th class="text-center" width="12%">
										<input ' . $min_qty_disabled . ' ' . $min_qty_required . ' data-set="' . $set . '" type="number" min="1" max="' . $provide_bonus_qty . '" name="data[MemoDetail][sales_qty][]" class="width_100_this policy_min_qty ' . $policy_id . '_policy_set_' . $set . '" value="' . $value . '">
									</th>
									<th class="text-center" width="10%">
										<input type="checkbox" disabled ' . $min_qty_checked . ' class="is_bonus_checked" name="data[MemoDetail][bonus_check][]" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">
									</th>
								</tr>';
						}
					}
				}
			}
		}
		return $b_html;
	}

	public function get_parent_virtual_pd_info($pid)
	{

		$this->loadModel('CurrentInventory');
		$this->loadModel('Product');

		$productinfo = $this->Product->find('first', array(
			'conditions' => array(
				'Product.id' => $pid
			),
			'joins' => array(
				array(
					'alias' => 'VirtualProduct',
					'table' => 'products',
					'type' => 'LEFT',
					'conditions' => 'Product.parent_id = VirtualProduct.id'
				)
			),
			'fields' => array('Product.id', 'Product.name', 'VirtualProduct.id', 'VirtualProduct.name'),
			'recursive' => -1
		));

		return $productinfo;
	}

	public function get_product_inventroy_check($soter_id, $vpid, $parent_product_id)
	{

		$this->loadModel('CurrentInventory');
		$this->loadModel('Product');

		$parentproductcount = $this->CurrentInventory->find('count', array(
			'fields' => array('CurrentInventory.product_id'),
			'conditions' => array(
				'CurrentInventory.store_id' => $soter_id,
				'CurrentInventory.product_id' => $parent_product_id
			),
			'group' => 'CurrentInventory.product_id HAVING sum(CurrentInventory.qty)>0',
			'recursive' => -1
		));

		$chilhproductCount = $this->CurrentInventory->find('count', array(
			'fields' => array('CurrentInventory.product_id'),
			'conditions' => array(
				'CurrentInventory.store_id' => $soter_id,
				'Product.parent_id' => $parent_product_id
			),
			'joins' => array(
				array(
					'alias' => 'Product',
					'table' => 'products',
					'type' => 'INNER',
					'conditions' => 'Product.id = CurrentInventory.product_id'
				)
			),
			'group' => 'CurrentInventory.product_id HAVING sum(CurrentInventory.qty)>0',
			'recursive' => -1
		));

		if (empty($parentproductcount)) {
			$parentproductcount = 0;
		}

		if (empty($chilhproductCount)) {
			$chilhproductCount = 0;
		}

		//echo $soter_id . '---' . $parent_product_id . '---' . $parentproductcount . '--' . $chilhproductCount;exit;

		if ($parentproductcount == 0 and $chilhproductCount == 1) {
			$show = 1;
		} else {
			$show = 0;
		}

		return $show;
	}



	private function parse_formla($formula)
	{
		$set_relation = '';
		$formula = explode(' ', $formula);
		$formula_product = array();
		$formula_element_relation = array();
		$set = 1;
		for ($i = 0; $i < count($formula); $i++) {
			if ($formula[$i] == '(') {
				continue;
			}
			if ($formula[$i] == ')') {
				if (($i + 1) < count($formula)) {
					$set_relation = $formula[$i + 1];
				}
				$set += 1;
				$i = $i + 1;
			} else {
				if ($formula[$i] == 'AND' || $formula[$i] == 'OR') {
					$formula_element_relation[$set] = $formula[$i];
				} else {
					$formula_product[$set][] = $formula[$i];
				}
			}
			if (!isset($formula_element_relation[$set])) {
				$formula_element_relation[$set] = 'AND';
			}
		}
		$parse_formula = array(
			'set_relation' => $set_relation,
			'formula_product' => $formula_product,
			'element_relation' => $formula_element_relation
		);
		return $parse_formula;
	}
	public function get_spcial_group_and_outlet_category_id()
	{
		$this->loadModel('SpecialGroup');
		$this->loadModel('SpecialGroupOtherSetting');
		$memo_date = date('Y-m-d', strtotime($this->request->data['memo_date']));
		$outlet_id = $this->request->data['outlet_id'];
		$office_id = $this->request->data['office_id'];
		$territory_id = $this->request->data['territory_id'];
		$outlet_info = $this->Outlet->find('first', array(
			'conditions' => array(
				'Outlet.id' => $outlet_id
			),
			'recursive' => -1
		));
		$outlet_category_id = $outlet_info['Outlet']['category_id'];
		$conditions[] = array('SpecialGroup.start_date <=' => $memo_date);
		$conditions[] = array('SpecialGroup.end_date >=' => $memo_date);
		$conditions[] = array('SPO.reffrence_id' => $office_id);
		$conditions[] = array('SPOC.reffrence_id' => $outlet_category_id);
		$conditions[] = array('SpecialGroup.is_dist' => 0);

		$special_group_data = $this->SpecialGroup->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'special_group_other_settings',
					'alias' => 'SPO',
					'conditions' => 'SpecialGroup.id=SPO.special_group_id and SPO.create_for=1'
				),
				array(
					'table' => 'special_group_other_settings',
					'alias' => 'SPOC',
					'conditions' => 'SpecialGroup.id=SPOC.special_group_id and SPOC.create_for=3'
				),
			),
			'recursive' => -1
		));
		$special_gorup_id = array();
		foreach ($special_group_data as $data) {
			$group_id = $data['SpecialGroup']['id'];
			$special_group_territory_details = array();
			$special_group_details = array();
			$special_group_details = $this->SpecialGroup->find('all', array(
				'conditions' => array(
					'SpecialGroup.id' => $group_id,
				),
				'joins' => array(
					array(
						'table' => 'special_group_other_settings',
						'alias' => 'SPO',
						'conditions' => 'SpecialGroup.id=SPO.special_group_id and SPO.create_for=1'
					),
					array(
						'table' => '(
									select t.office_id,spg.reffrence_id as territory_id from special_groups sps
									inner join special_group_other_settings spg on sps.id=spg.special_group_id and create_for=2
									inner join territories t on t.id=spg.reffrence_id
									where 
										sps.id=' . $group_id . '
								)',
						'alias' => 'SPT',
						'type' => 'left',
						'conditions' => 'SPT.office_id=SPO.reffrence_id'
					),
				),
				'group' => array('SPO.reffrence_id'),
				'fields' => array('SPO.reffrence_id', 'COUNT(SPT.territory_id) as total_territory'),
				'recursive' => -1
			));
			if ($special_group_details) {
				if ($special_group_details[0][0]['total_territory'] > 0) {
					$special_group_territory_details = $this->SpecialGroup->find('all', array(
						'conditions' => array(
							'SpecialGroup.id' => $group_id,
							'SPO.reffrence_id' => $territory_id
						),
						'joins' => array(
							array(
								'table' => 'special_group_other_settings',
								'alias' => 'SPO',
								'conditions' => 'SpecialGroup.id=SPO.special_group_id and SPO.create_for=2'
							)

						),
						'recursive' => -1
					));
					if ($special_group_territory_details) {
						$special_gorup_id[] = $group_id;
					}
				} else {
					$special_gorup_id[] = $group_id;
				}
			}
		}
		$conditions = array();
		$conditions[] = array('SpecialGroup.start_date <=' => $memo_date);
		$conditions[] = array('SpecialGroup.end_date >=' => $memo_date);
		$conditions[] = array('SpecialGroup.is_dist' => 0);
		$conditions[] = array('OutletGroup.outlet_id' => $outlet_id);
		$special_group_data = $this->SpecialGroup->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'special_group_other_settings',
					'alias' => 'GrpOg',
					'conditions' => 'SpecialGroup.id=GrpOg.special_group_id and GrpOg.create_for=4'
				),
				array(
					'table' => 'outlet_group_to_outlets',
					'alias' => 'OutletGroup',
					'type' => 'Left',
					'conditions' => 'GrpOg.reffrence_id=OutletGroup.outlet_group_id'
				),
			),
			'recursive' => -1
		));
		$special_gorup_id = array_merge($special_gorup_id, array_map(function ($data) {
			return $data['SpecialGroup']['id'];
		}, $special_group_data));
		$result['outlet_category_id'] = $outlet_category_id;
		$result['special_group_id'] = $special_gorup_id;
		echo json_encode($result);
		//exit;
		$this->autoRender = false;
	}
	public function memo_stock_check()
	{

		$this->loadModel('CurrentInventory');
		$this->loadModel('Store');
		$this->loadModel('Product');
		$product = json_decode($this->request->data['product_qty'], 1);
		$territory_id = $this->request->data['territory_id'];

		$store_info = $this->Store->find('first', array(
			'conditions' => array(
				'Store.territory_id' => $territory_id,
				'Store.store_type_id' => 3
			),
			'recursive' => -1
		));
		$store_id = $store_info['Store']['id'];
		$product_wise_qty = array();
		foreach ($product as $pid => $p_data) {
			foreach ($p_data as $unit_id => $qty) {
				$prev_qty = 0;
				if (isset($product_wise_qty[$pid])) {
					$prev_qty = $product_wise_qty[$pid];
				}
				$product_wise_qty[$pid] = $prev_qty + $this->unit_convert($pid, $unit_id, $qty);
			}
		}
		$prev_memo_array = array();
		if (isset($this->request->data['memo_id'])) {
			$memo_id = $this->request->data['memo_id'];
			$prev_memo_data = $this->MemoDetail->find('all', array(
				'conditions' => array('memo_id' => $memo_id, 'product_id' => array_keys($product_wise_qty)),
				'recursive' => -1
			));
			foreach ($prev_memo_data as $data) {
				$prev_qty = 0;
				if (isset($prev_memo_array[$data['MemoDetail']['product_id']])) {
					$prev_qty = $prev_memo_array[$data['MemoDetail']['product_id']];
				}
				$prev_memo_array[$data['MemoDetail']['product_id']] = $prev_qty + $this->unit_convert($data['MemoDetail']['product_id'], $data['MemoDetail']['measurement_unit_id'], $data['MemoDetail']['sales_qty']);
			}
		}
		$this->CurrentInventory->virtualFields = array('qty_sum' => 'sum(CurrentInventory.qty)');
		$inventory_data = $this->CurrentInventory->find('list', array(
			'conditions' => array(
				'store_id' => $store_id,
				'product_id' => array_keys($product_wise_qty)
			),
			'group' => array('product_id'),
			'fields' => array('product_id', 'qty_sum'),
			'recursive' => -1
		));
		$short_stock_product = array();
		$stock_available = 1;
		foreach ($product_wise_qty as $pid => $qty) {
			$prev_memo_qty = 0;
			if (isset($prev_memo_array[$pid])) {
				$prev_memo_qty = $prev_memo_array[$pid];
			}
			if (!isset($inventory_data[$pid]) || ($qty - $prev_memo_qty) > $inventory_data[$pid]) {
				$short_stock_product[] = $pid;
				$stock_available = 0;
			}
		}
		$res['status'] = $stock_available;
		if ($stock_available == 0) {
			$product = $this->Product->find('list', array(
				'conditions' => array('id' => $short_stock_product),
				'fields' => array('id', 'name'),
			));
			$res['msg'] = 'Stock Short For ' . implode(',', $product);
		}
		echo json_encode($res);
		$this->autoRender = false;
	}
	
	function get_program_officer_list()
	{
		$office_id = $this->request->data['office_id'];

		$this->loadModel('Territory');
		
		$programoffice_con = array(
			'User.user_group_id'	=>1016,
			'User.active'			=>1,
		);

		if ($office_id >  0) {
			$programoffice_con['SalesPerson.office_id'] = $office_id;
		}

		$program_office_lsit = $this->SalesPerson->find('list', array(
			'conditions'=>$programoffice_con,
			'joins'=>array(
				array(
					'alias'=>'User',
					'table'=>'users',
					'type'=>'left',
					'conditions'=>'SalesPerson.id=User.sales_person_id'
				)
			),
			'fields'=>array('User.id', 'SalesPerson.name')
		));

		$output = "<option value=''>--- Select Program Officer ---</option>";
		if ($program_office_lsit) {
			foreach ($program_office_lsit as $key => $data) {
				$output .= "<option value='$key'>$data</option>";
			}
		}
		echo $output;
		$this->autoRender = false;
	}
	
	
}
