<?php
App::uses('AppController', 'Controller');

/**
 * CollectionsController Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class WeeklyBankDepositionInformationController extends AppController
{

	public $uses = array('Office', 'Deposit', 'SalesPerson', 'TerritoryAssignHistory', 'Territory', 'BankBranch');

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'Session', 'Filter.Filter');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index()
	{

		date_default_timezone_set('Asia/Dhaka');
		$this->set('page_title', 'Weekly Bank Deposition Information');


		$territories = array();
		$so_list = array();

		//for region office list
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id' => 3),
			'order' => array('office_name' => 'asc')
		));
		$this->set(compact('region_offices'));

		//types
		$types = array(
			'territory' => 'By Terriotry',
			'so' => 'By SO',
		);
		$this->set(compact('types'));


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

			$districts = $this->District->find('list', array(
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

				),
				'order' =>  array('District.name' => 'asc')
			));
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
			$office_id = $this->UserAuth->getOfficeId();

			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'id' 	=> $office_id,
				),
				'order' => array('office_name' => 'asc')
			));

			$territory_list = $this->Territory->find('all', array(
				'conditions' => array('Territory.office_id' => $office_id),
				'joins' => array(
					array(
						'alias' => 'User',
						'table' => 'users',
						'type' => 'left',
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
					'User.user_group_id' => array(4, 1008),
				),
				'recursive' => 0
			));
			foreach ($so_list_r as $key => $value) {
				$so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
			}

			$conditions['Territory.office_id'] = $office_id;
		}



		//for office list
		$office_conditions['NOT'] = array("id" => array(30, 31, 37));
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));



		$request_data = array();

		$this->LoadModel('InstrumentType');
		$instrument_type = $this->InstrumentType->find('list', array(
			//'conditions' => array('InstrumentType.id !=' => 2)
		));
		$this->set(compact('instrument_type'));


		if ($this->request->is('post') || $this->request->is('put')) {
			$request_data = $this->request->data;

			//pr($request_data);

			$date_from = date('Y-m-d', strtotime($request_data['WeeklyBankDepositionInformation']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['WeeklyBankDepositionInformation']['date_to']));
			$this->set(compact('date_from', 'date_to'));

			$type = $this->request->data['WeeklyBankDepositionInformation']['type'];
			$this->set(compact('type'));

			$region_office_id = isset($this->request->data['WeeklyBankDepositionInformation']['region_office_id']) != '' ? $this->request->data['WeeklyBankDepositionInformation']['region_office_id'] : $region_office_id;
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

			$office_id = isset($this->request->data['WeeklyBankDepositionInformation']['office_id']) != '' ? $this->request->data['WeeklyBankDepositionInformation']['office_id'] : $office_id;
			$this->set(compact('office_id'));

			$territory_id = isset($this->request->data['WeeklyBankDepositionInformation']['territory_id']) != '' ? $this->request->data['WeeklyBankDepositionInformation']['territory_id'] : 0;
			$this->set(compact('territory_id'));

			$so_id = isset($this->request->data['WeeklyBankDepositionInformation']['so_id']) != '' ? $this->request->data['WeeklyBankDepositionInformation']['so_id'] : 0;
			$this->set(compact('so_id'));

			$qumulative = isset($this->request->data['WeeklyBankDepositionInformation']['qumulative']) != '' ? $this->request->data['WeeklyBankDepositionInformation']['qumulative'] : 0;
			$this->set(compact('qumulative'));




			//territory list
			$territory_list = $this->Territory->find('all', array(
				'conditions' => array('Territory.office_id' => $office_id),
				'joins' => array(
					array(
						'alias' => 'User',
						'table' => 'users',
						'type' => 'left',
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



			//For Query CASH 
			$conditions = array(
				'Deposit.deposit_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Deposit.type' => 1,
			);

			if ($region_office_id) $conditions['Office.parent_office_id'] = $region_office_id;

			if ($office_ids) $conditions['Territory.office_id'] = $office_ids;
			if ($office_id) $conditions['Territory.office_id'] = $office_id;

			if ($type == 'so') {
				if ($so_id) $conditions['Deposit.sales_person_id'] = $so_id;
			} else {
				if ($territory_id) $conditions['Deposit.territory_id'] = $territory_id;
			}

			//pr($conditions);
			//exit;

			$fields =  array('SUM(Deposit.deposit_amount) as deposit_amount', 'Deposit.type', 'Office.parent_office_id');
			$group =  array('Office.parent_office_id', 'Deposit.type');

			if (!$qumulative) {
				array_push($fields, 'Office.id');
				array_push($group, 'Office.id');
			}
			if ($office_id && $qumulative) {
				array_push($fields, 'Office.id');
				array_push($group, 'Office.id');
			}
			if ($office_id && !$qumulative) {
				if ($type == 'so') array_push($fields, 'Deposit.sales_person_id');
				if ($type != 'so') array_push($fields, 'Deposit.territory_id');

				if ($type == 'so') array_push($group, 'Deposit.sales_person_id');
				if ($type != 'so') array_push($group, 'Deposit.territory_id');
			}



			//pr($group);


			$cash_q_results = $this->Deposit->find('all', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'Deposit.territory_id = Territory.id'
					),
					array(
						'alias' => 'Office',
						'table' => 'offices',
						'type' => 'INNER',
						'conditions' => 'Territory.office_id = Office.id'
					),
				),

				'fields' => $fields,
				'group' => $group,
				'recursive' => -1,
			));

			//pr($cash_q_results);

			$cash_results = array();
			$child_results = array();
			foreach ($cash_q_results as $result) {
				//pr($result);

				if (@$type == 'territory' && @$result['Deposit']['territory_id']) {
					$child_results[$result['Deposit']['territory_id']]['Cash'] = array(
						'deposit_amount' => $result[0]['deposit_amount'],
					);
				} elseif (@$type == 'so' && @$result['Deposit']['sales_person_id']) {
					$child_results[$result['Deposit']['sales_person_id']]['Cash'] = array(
						'deposit_amount' => $result[0]['deposit_amount'],
					);
				}

				@$cash_results[$result['Office']['parent_office_id']][$result['Office']['id']] = array(
					'deposit_amount' => $result[0]['deposit_amount'],
					'type' => 'Cash',
					'child_results' => $child_results,
				);
			}
			$this->set(compact('cash_results'));
			//pr($cash_results);
			//exit;



			//For Query Instrument 
			$conditions = array(
				'Deposit.deposit_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Deposit.type' => 2,
				'Collection.type' => 2,
			);

			if ($region_office_id) $conditions['Office.parent_office_id'] = $region_office_id;
			if ($office_ids) $conditions['Territory.office_id'] = $office_ids;
			if ($office_id) $conditions['Territory.office_id'] = $office_id;

			if ($type == 'so') {
				if ($so_id) $conditions['Memo.sales_person_id'] = $so_id;
			} else {
				if ($territory_id) $conditions['Memo.territory_id'] = $territory_id;
			}

			//pr($conditions);
			//exit;

			$fields =  array('SUM(Deposit.deposit_amount) as deposit_amount', 'Collection.instrument_type', 'Office.parent_office_id');
			$group =  array('Office.parent_office_id', 'Collection.instrument_type');

			if (!$qumulative) {
				array_push($fields, 'Office.id');
				array_push($group, 'Office.id');
			}
			if ($office_id && $qumulative) {
				array_push($fields, 'Office.id');
				array_push($group, 'Office.id');
			}
			if ($office_id && !$qumulative) {
				array_push($fields, 'Memo.sales_person_id');
				array_push($fields, 'Memo.territory_id');

				array_push($group, 'Memo.sales_person_id');
				array_push($group, 'Memo.territory_id');
			}
			//pr($group);

			$query_ins_results = $this->Deposit->find('all', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'alias' => 'Collection',
						'table' => 'collections',
						'type' => 'INNER',
						'conditions' => 'Deposit.payment_id = Collection.payment_id'
					),
					array(
						'alias' => 'Memo',
						'table' => 'memos',
						'type' => 'INNER',
						'conditions' => 'Deposit.memo_no = Memo.memo_no'
					),
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'Memo.territory_id = Territory.id'
					),
					array(
						'alias' => 'Office',
						'table' => 'offices',
						'type' => 'INNER',
						'conditions' => 'Territory.office_id = Office.id'
					),
				),

				'fields' => $fields,
				'group' => $group,
				'recursive' => -1,
			));

			//pr($query_ins_results);
			//exit;

			$ins_results = array();
			$child_results = array();
			foreach ($query_ins_results as $result) {
				//pr($result);

				if (@$type == 'territory' && @$result['Memo']['territory_id']) {
					$child_results[$result['Memo']['territory_id']][$instrument_type[$result['Collection']['instrument_type']]] = array(
						'deposit_amount' => $result[0]['deposit_amount'],
					);
				} elseif (@$type == 'so' && @$result['Memo']['sales_person_id']) {
					$child_results[$result['Memo']['sales_person_id']][$instrument_type[$result['Collection']['instrument_type']]] = array(
						'deposit_amount' => $result[0]['deposit_amount'],
					);
				}

				if ($office_id && !$qumulative) {
					$ins_results[$result['Office']['parent_office_id']][$result['Office']['id']] = array(
						'deposit_amount' => $child_results ? 0 : $result[0]['deposit_amount'],
						'type' => $child_results ? 0 : $instrument_type[$result['Collection']['instrument_type']],
						'child_results' => $child_results,
					);
				} else {
					@$ins_results[$result['Office']['parent_office_id']][$result['Office']['id']][$instrument_type[$result['Collection']['instrument_type']]] = array(
						'deposit_amount' => $child_results ? 0 : $result[0]['deposit_amount'],
						'type' => $child_results ? 0 : $instrument_type[$result['Collection']['instrument_type']],
						'child_results' => $child_results,
					);
				}
			}

			//pr($ins_results);
			//exit;

			$this->set(compact('ins_results'));
		}

		//pr($so_list);

		$this->set(compact('offices', 'territories', 'region_offices', 'office_id', 'request_data', 'so_list'));
	}

	public function details_report()
	{
		$this->LoadModel('InstrumentType');
		$instrument_type = $this->InstrumentType->find('list', array(
			'conditions' => array('InstrumentType.id !=' => 2)
		));

		$this->set('page_title', 'Sales Deposition Monitoring Detail');

		// pr($this->request->query);exit;

		$date_from = $this->request->query['date_from'];
		$date_to = $this->request->query['date_to'];
		$region_office_id = $this->request->query['region_id'];
		$office_id = $this->request->query['office_id'];
		$territory_id = $this->request->query['territory_id'];
		$so_id = $this->request->query['so_id'];
		if ($so_id) {
			$territory_id = $this->get_territory_id($so_id);
		}



		//for region office list
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id' => 3),
			'order' => array('office_name' => 'asc')
		));

		$offices = $this->Office->find('list', array(
			'conditions' => array(
				'office_type_id' 	=> 2,
				'parent_office_id' 	=> $region_office_id,
				"NOT" => array("id" => array(30, 31, 37))
			),
			'order' => array('office_name' => 'asc')
		));
		$office_ids = array_keys($offices);


		$offices = $this->Office->find('list', array(
			'conditions' => array(
				'office_type_id' 	=> 2,
				"NOT" => array("id" => array(30, 31, 37))
			),
			'order' => array('office_name' => 'asc')
		));


		//territory list
		$territory_list = $this->Territory->find('all', array(
			'conditions' => array('Territory.office_id' => $office_id ? $office_id : $office_ids),
			'joins' => array(
				array(
					'alias' => 'User',
					'table' => 'users',
					'type' => 'LEFT',
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


		$this->set(compact('date_from', 'date_to', 'offices', 'office_id', 'region_offices', 'region_id', 'territories', 'instrument_type'));





		//For Cahs Deposite
		$conditions = array(
			'Deposit.deposit_date BETWEEN ? and ? ' => array($date_from, $date_to),
			'Deposit.type' => 1,
			//'Collection.type' => 2,
		);

		if ($region_office_id) $conditions['Office.parent_office_id'] = $region_office_id;
		if ($office_ids) $conditions['Territory.office_id'] = $office_ids;
		if ($office_id) $conditions['Territory.office_id'] = $office_id;
		if ($so_id) $conditions['Deposit.sales_person_id'] = $so_id;
		if ($territory_id) $conditions['Deposit.territory_id'] = $territory_id;
		//pr($conditions);
		//exit;

		$fields = array('Deposit.*');
		$group =  array('Office.parent_office_id', 'Office.id', 'Deposit.sales_person_id', 'Deposit.territory_id');
		$query_cash_results = $this->Deposit->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'Territory',
					'table' => 'territories',
					'type' => 'INNER',
					'conditions' => 'Deposit.territory_id = Territory.id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'Territory.office_id = Office.id'
				),
			),

			'fields' => $fields,
			//'group' => $group,
			'order' => array('Deposit.deposit_date', 'Office.parent_office_id', 'Office.id', 'Deposit.sales_person_id', 'Deposit.territory_id'),
			'recursive' => -1,
		));

		//pr($query_cash_results);



		//For Ins Deposite
		$conditions = array(
			'Deposit.deposit_date BETWEEN ? and ? ' => array($date_from, $date_to),
			'Deposit.type' => 2,
			'Collection.type' => 2,
		);

		if ($region_office_id) $conditions['Office.parent_office_id'] = $region_office_id;
		if ($office_ids) $conditions['Territory.office_id'] = $office_ids;
		if ($office_id) $conditions['Territory.office_id'] = $office_id;
		if ($so_id) $conditions['Memo.sales_person_id'] = $so_id;
		if ($territory_id) $conditions['Memo.territory_id'] = $territory_id;
		//pr($conditions);
		//exit;

		$fields = array('Deposit.*', 'Memo.sales_person_id', 'Memo.territory_id', 'Collection.instrument_type', 'Collection.instrument_no');
		$group =  array('Office.parent_office_id', 'Office.id', 'Memo.sales_person_id', 'Memo.territory_id');
		$query_ins_results = $this->Deposit->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'Collection',
					'table' => 'collections',
					'type' => 'INNER',
					'conditions' => 'Deposit.payment_id = Collection.payment_id'
				),
				array(
					'alias' => 'Memo',
					'table' => 'memos',
					'type' => 'INNER',
					'conditions' => 'Deposit.memo_no = Memo.memo_no'
				),
				array(
					'alias' => 'Territory',
					'table' => 'territories',
					'type' => 'INNER',
					'conditions' => 'Memo.territory_id = Territory.id'
				),
				// array(
				// 	'alias' => 'Territory',
				// 	'table' => 'territories',
				// 	'type' => 'INNER',
				// 	'conditions' => 'Deposit.territory_id = Territory.id'
				// ),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'Territory.office_id = Office.id'
				),
			),

			'fields' => $fields,
			//'group' => $group,
			'order' => array('Deposit.deposit_date', 'Office.parent_office_id', 'Office.id', 'Memo.sales_person_id', 'Memo.territory_id'),
			'recursive' => -1,
		));
		//pr($query_ins_results);

		$results = array();
		if ($query_ins_results) {
			$results = array_merge($query_cash_results, $query_ins_results);
		} else {
			$results = $query_cash_results;
		}

		//pr($results);
		//exit;

		$data = array();
		foreach ($results as $result) {
			@$data[$territories[$result[(isset($result['Deposit']['type']) && (@$result['Deposit']['type']) == 1 ? 'Deposit' : 'Memo')]['territory_id']]][$result['Deposit']['id']] = array(
				'deposit_id' => $result['Deposit']['id'],
				'slip_no' => $result['Deposit']['slip_no'],
				'deposit_date' => $result['Deposit']['deposit_date'],
				'type' => $result['Deposit']['type'],
				'deposit_amount' => $result['Deposit']['deposit_amount'],
				'bank_branch_id' => $result['Deposit']['bank_branch_id'],
				'instrument_type' => $result['Deposit']['instrument_type'],
				'remarks' => $result['Deposit']['remarks'],
				'c_instrument_type' => @$result['Collection']['instrument_type'] ? $result['Collection']['instrument_type'] : 0,
				'c_instrument_no' => @$result['Collection']['instrument_no'] ? $result['Collection']['instrument_no'] : 0,
			);
		}


		$this->set(compact('data'));
	}

	public function get_territory_id($so_id = 0)
	{
		if ($so_id) {
			$results = $this->SalesPerson->find('first', array(
				'conditions' => array('SalesPerson.id' => $so_id),
				'fields' => array('territory_id'),
			));
			return $results['SalesPerson']['territory_id'];
		}
	}


	public function get_bank_branch_info($id = 0)
	{
		if ($id) {
			return $bank_branch_info = $this->BankBranch->find('first', array(
				'conditions' => array('BankBranch.id' => $id)
			));
		}
		//pr($bank_branch_info);
		//exit;
	}


	/*public function get_office_list() {
        $rs = array(array('id' => '', 'name' => '---- All -----'));

        $parent_office_id = $this->request->data['region_office_id'];

        $office_conditions = array('Office.parent_office_id' => $parent_office_id, 'Office.office_type_id' => 2);

        $offices = $this->Office->find('all', array(
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

    public function get_territory_so_list() {
        $view = new View($this);

        $form = $view->loadHelper('Form');

        $office_id = $this->request->data['office_id'];
        $date_from = $this->request->data['date_from'];
        $date_to = $this->request->data['date_to'];

        if (!$date_from && !$date_to) {
            $form->create('WeeklyBankDepositionInformation', array('role' => 'form', 'action' => 'index'));

            echo $form->input('so_id', array('class' => 'form-control so_id', 'empty' => '--- Select---', 'label' => 'Sales Officer'));
            $form->end();
            exit;
        }

        if ($office_id) {
            $conditions = array('User.user_group_id' => array(4, 1008), 'User.active' => 1);
            $conditions['SalesPerson.office_id'] = $office_id;

            $so_list = array();
            $so_list_r = $this->SalesPerson->find('all', array(
                'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
                'conditions' => array(
                    'SalesPerson.office_id' => $office_id,
                    'SalesPerson.territory_id >' => 0,
                    'User.user_group_id' => array(4, 1008),
                    'User.active' => 1
                    ),
                'recursive' => 0
                ));
            foreach ($so_list_r as $list_r) {
                $so_list[$list_r['SalesPerson']['id']] = $list_r['SalesPerson']['name'] . ' (' . $list_r['Territory']['name'] . ')';
            }

            $conditions = array('Territory.office_id' => $office_id, 'TerritoryAssignHistory.assign_type' => 2);
            $conditions['TerritoryAssignHistory.date BETWEEN ? and ? '] = array(date('Y-m-d', strtotime($date_from)), date('Y-m-d', strtotime($date_to)));
            $old_so_list = $this->TerritoryAssignHistory->find('all', array(
                'conditions' => $conditions,
                'order' => array('Territory.name' => 'asc'),
                'recursive' => 0
                ));
            if ($old_so_list) {
                foreach ($old_so_list as $old_so) {
                    $so_list[$old_so['TerritoryAssignHistory']['so_id']] = $old_so['SalesPerson']['name'];
                }
            }
        }


        if (isset($so_list)) {
            $form->create('WeeklyBankDepositionInformation', array('role' => 'form', 'action' => 'index'));

            echo $form->input('so_id', array('class' => 'form-control so_id', 'empty' => '--- Select---', 'options' => $so_list, 'label' => 'Sales Officer'));
            $form->end();
        } else {
            echo '';
        }


        $this->autoRender = false;
    }

    public function get_territory_list() {
        $view = new View($this);

        $form = $view->loadHelper('Form');

        $office_id = $this->request->data['office_id'];

        //get territory list
        $all_territory = $this->Territory->find('all', array(
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
		
		$territory_list = array();
	
		foreach($all_territory as $key => $value)
		{
			$territory_list[$value['Territory']['id']] = $value['Territory']['name'].' ('.$value['SalesPerson']['name'].')';
		}


        if (isset($territory_list)) {
            $form->create('WeeklyBankDepositionInformation', array('role' => 'form', 'action' => 'index'));

            echo $form->input('territory_id', array('class' => 'form-control so_id', 'empty' => '--- Select---', 'options' => $territory_list));
            $form->end();
        } else {
            echo '';
        }


        $this->autoRender = false;
    }*/
}
