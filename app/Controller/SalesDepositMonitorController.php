<?php

App::uses('AppController', 'Controller');

/**
 * CollectionsController Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class SalesDepositMonitorController extends AppController
{
	public $uses = false;
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
		$this->set('page_title', 'Sales Deposition Monitoring List');
		$this->loadModel('Office');
		$this->loadModel('Collection');
		$this->loadModel('Deposit');
		$this->loadModel('SalesPerson');
		$this->loadModel('Territory');
		$this->loadModel('TerritoryAssignHistory');
		$this->loadModel('Memo');

		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$office_id = 0;
		} else {
			$office_id = $this->UserAuth->getOfficeId();
		}

		$so_list = array();

		if ($office_id || $this->request->is('post') || $this->request->is('put')) {
			$office_id = isset($this->request->data['SalesDepositMonitor']['office_id']) != '' ? $this->request->data['SalesDepositMonitor']['office_id'] : $office_id;

			//get SO list
			$spo_so_group_ids = [4, 1008];
			$so_list_r = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
				'conditions' => array(
					'SalesPerson.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					'User.user_group_id' => $spo_so_group_ids,
				),
				'recursive' => 0
			));


			foreach ($so_list_r as $key => $value) {
				$so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
			}

			$conditions = array('Territory.office_id' => $office_id, 'TerritoryAssignHistory.assign_type' => 2);
			// $conditions['TerritoryAssignHistory.date BETWEEN ? and ? '] = array($date_from, $date_to);
			$conditions['TerritoryAssignHistory.date >= '] = date('Y-m-d', strtotime($this->request->data['SalesDepositMonitor']['date_from']));
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
		$this->set(compact('so_list'));
		$this->Session->write('so_list', $so_list);



		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id != 0) {
			$office_type = $this->Office->find('first', array(
				'conditions' => array('Office.id' => $this->UserAuth->getOfficeId()),
				'recursive' => -1
			));
			$office_type_id = $office_type['Office']['office_type_id'];
		}


		if ($office_parent_id == 0) {
			$region_office_condition = array('office_type_id' => 3);
			$office_conditions = array('office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
		} else {
			if ($office_type_id == 3) {
				$region_office_condition = array('office_type_id' => 3, 'Office.id' => $this->UserAuth->getOfficeId());
				$office_conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId(), 'office_type_id' => 2);
			} elseif ($office_type_id == 2) {
				$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'office_type_id' => 2);
			}
		}

		$offices = $this->Office->find('list', array(
			'conditions' => $office_conditions,
			'fields' => array('office_name')
		));

		if (isset($region_office_condition)) {
			$region_offices = $this->Office->find('list', array(
				'conditions' => $region_office_condition,
				'order' => array('office_name' => 'asc')
			));

			$this->set(compact('region_offices'));
		}
		$this->set(compact('offices', 'office_id', 'sales_people'));
		if ($this->request->is('post')) {
			// pr($this->request->data);exit;
			$office_id = $this->request->data['SalesDepositMonitor']['office_id'];
			$so_list_array = array();
			if ($this->request->data['SalesDepositMonitor']['so_id']) {
				$so_list_array = $this->request->data['SalesDepositMonitor']['so_id'];
			} else {
				$so_list_array = $this->SalesPerson->find('list', array(
					'fields' => array('SalesPerson.id'),
					'conditions' => array(
						'SalesPerson.office_id' => $office_id,
						'SalesPerson.territory_id >' => 0,
						'User.user_group_id' => 4,
					),
					'recursive' => 0
				));
			}
			$first_date_memo = '2018-10-01';
			$date_from = date('Y-m-d', strtotime($this->request->data['SalesDepositMonitor']['date_from']));
			$date_to = date('Y-m-d', strtotime($this->request->data['SalesDepositMonitor']['date_to']));
			$max_retain = $this->request->data['SalesDepositMonitor']['max_amount_retain'];
			foreach ($so_list_array as $so_id) {
				$sales_person = $this->SalesPerson->find('first', array(
					'conditions' => array('SalesPerson.id' => $so_id),
					'recursive' => -1
				));
				$collection_amount_sum = $this->Collection->find(
					'all',
					array(
						'conditions' => array(
							'Memo.sales_person_id' => $so_id,
							'Collection.collectionDate <' => $date_from,
							'Memo.memo_date >=' => $first_date_memo,
						),
						'joins' => array(
							array(
								'alias' => 'Memo',
								'table' => 'memos',
								'type' => 'INNER',
								'conditions' => 'Collection.memo_id = Memo.id'
							)
						),
						'fields' => array('SUM(Collection.collectionAmount) AS total_collection'),
						'recursive' => -1
					)
				);
				$deposit_amount_sum = $this->Deposit->query(
					"select sum(dp.deposit_amount) AS total_deposit 
					from deposits dp
					left join memos m on m.id=dp.memo_id
					where dp.sales_person_id = " . $so_id . " 
					and dp.deposit_date <'" . $date_from . "'
					and dp.deposit_date >='" . $first_date_memo . "'
					and m.id is null
					"
				);

				$crdeposit_amount_sum = $this->Deposit->query(
					"select sum(dp.deposit_amount) AS total_deposit 
						from deposits dp
						left join memos m on m.id=dp.memo_id
						where m.sales_person_id = " . $so_id . " 
                        and dp.deposit_date <'" . $date_from . "'
						and dp.deposit_date >='" . $first_date_memo . "'
						"
				);

				$data[$so_id]['opening_balance'] = sprintf('%0.2f', $collection_amount_sum[0][0]['total_collection'] - $deposit_amount_sum[0][0]['total_deposit'] - $crdeposit_amount_sum[0][0]['total_deposit']);
				$data[$so_id]['so_name'] = $sales_person['SalesPerson']['name'];
				$opening_balance = $data[$so_id]['opening_balance'];
				$count = 0;
				for ($date = $date_from; $date <= $date_to; $date = date('Y-m-d', strtotime($date . '+1 days'))) {

					$held = 0;
					// echo 'Date : '.$date.'---- so id : '.$so_id.'<br>';
					$cash_sales = $this->Memo->find('all', array(
						'conditions' => array(
							'Memo.memo_date' => $date,
							'Memo.sales_person_id' => $so_id
						),
						'fields' => array('SUM(Memo.cash_recieved) as cash'),
						'recursive' => -1
					));
					$credit_sales = $this->Memo->find('all', array(
						'conditions' => array(
							'Memo.memo_date' => $date,
							'Memo.sales_person_id' => $so_id
						),
						'fields' => array('SUM(Memo.credit_amount) as credit'),
						'recursive' => -1
					));
					$credit_collection = $this->Collection->find('all', array(
						'conditions' => array(
							'Collection.collectionDate' => $date,
							'Memo.memo_date >=' => $first_date_memo,
							'Memo.sales_person_id' => $so_id,
							'Collection.is_credit_collection' => 1
						),
						'joins' => array(
							array(
								'alias' => 'Memo',
								'table' => 'memos',
								'type' => 'INNER',
								'conditions' => 'Collection.memo_id = Memo.id'
							)
						),
						'fields' => array('SUM(Collection.collectionAmount) as collection'),
						'recursive' => -1
					));

					//-----------cash deposit------\\
					$deposit = $this->Deposit->find('all', array(
						'conditions' => array(
							'Deposit.sales_person_id' => $so_id,
							'Deposit.deposit_date' => $date,
							'Memo.id is null'
						),
						'joins' => array(
							array(
								'alias' => 'Memo',
								'table' => 'memos',
								'type' => 'LEFT',
								'conditions' => 'Deposit.memo_id = Memo.id'
							),
						),
						'fields' => array('SUM(Deposit.deposit_amount) as deposit'),
						'recursive' => -1
					));

					//-----------credit depost-----\\

					$crdeposit = $this->Deposit->find('all', array(
						'conditions' => array(
							'Memo.sales_person_id' => $so_id,
							'Deposit.deposit_date' => $date
						),
						'joins' => array(
							array(
								'alias' => 'Memo',
								'table' => 'memos',
								'type' => 'LEFT',
								'conditions' => 'Deposit.memo_id = Memo.id'
							),
						),
						'fields' => array('SUM(Deposit.deposit_amount) as deposit'),
						'recursive' => -1
					));





					// echo $this->Deposit->getlastQuery().'<br>';
					$balance_collection = $this->Collection->find('all', array(
						'conditions' => array(
							'Collection.collectionDate <=' => $date,
							'Collection.collectionDate >=' => $first_date_memo,
							'Memo.memo_date >=' => $first_date_memo,
							'Memo.sales_person_id' => $so_id,
						),
						'joins' => array(
							array(
								'alias' => 'Memo',
								'table' => 'memos',
								'type' => 'INNER',
								'conditions' => 'Collection.memo_id = Memo.id'
							)
						),
						'fields' => array('SUM(Collection.collectionAmount) as collection'),
						'recursive' => -1
					));

					//-------------cash deposit-----\\
					$balance_deposit = $this->Deposit->find('all', array(
						'conditions' => array(
							'Deposit.sales_person_id' => $so_id,
							'Deposit.deposit_date <=' => $date,
							'Deposit.deposit_date >=' => $first_date_memo,
							'Memo.id is null'
						),
						'joins' => array(
							array(
								'alias' => 'Memo',
								'table' => 'memos',
								'type' => 'LEFT',
								'conditions' => 'Deposit.memo_id = Memo.id'
							),
						),
						'fields' => array('SUM(Deposit.deposit_amount) as deposit'),
						'recursive' => -1
					));

					//---------------credit ----

					$crbalance_deposit = $this->Deposit->find('all', array(
						'conditions' => array(
							'Memo.sales_person_id' => $so_id,
							'Deposit.deposit_date <=' => $date,
							'Deposit.deposit_date >=' => $first_date_memo,
						),
						'joins' => array(
							array(
								'alias' => 'Memo',
								'table' => 'memos',
								'type' => 'LEFT',
								'conditions' => 'Deposit.memo_id = Memo.id'
							),
						),
						'fields' => array('SUM(Deposit.deposit_amount) as deposit'),
						'recursive' => -1
					));


					$balance = $balance_collection[0][0]['collection'] - $balance_deposit[0][0]['deposit'] - $crbalance_deposit[0][0]['deposit'];
					$data[$so_id]['rpt_data'][$date]['cash_sales'] = sprintf('%0.2f', $cash_sales[0][0]['cash']);
					$data[$so_id]['rpt_data'][$date]['credit_sales'] = sprintf('%0.2f', $credit_sales[0][0]['credit']);
					$data[$so_id]['rpt_data'][$date]['total_sales'] = sprintf('%0.2f', $cash_sales[0][0]['cash'] + $credit_sales[0][0]['credit']);
					$data[$so_id]['rpt_data'][$date]['credit_collection'] = sprintf('%0.2f', $credit_collection[0][0]['collection']);
					$data[$so_id]['rpt_data'][$date]['amount_collected'] = sprintf('%0.2f', $credit_collection[0][0]['collection'] + $data[$so_id]['rpt_data'][$date]['total_sales']);
					$data[$so_id]['rpt_data'][$date]['deposited'] = sprintf('%0.2f', $deposit[0][0]['deposit'] + $crdeposit[0][0]['deposit']);
					$data[$so_id]['rpt_data'][$date]['balance'] = sprintf('%0.2f', $balance);
					$deposited =  $data[$so_id]['rpt_data'][$date]['deposited'];
					if ($deposited > 0 && $opening_balance > $max_retain) {
						$count++;
						$held = sprintf('%0.2f', $opening_balance - $deposited);
						$data[$so_id]['rpt_data'][$date]['exceed'] = 'Exceeded + Held';
						if ($held > 0) {
							$data[$so_id]['rpt_data'][$date]['remarks'] = $held;
						} else {
							$data[$so_id]['rpt_data'][$date]['remarks'] = '';
						}
					} elseif ($deposited > 0 && $opening_balance < $max_retain) {
						$count = 0;
						$held = sprintf('%0.2f', $opening_balance - $deposited);
						$data[$so_id]['rpt_data'][$date]['exceed'] = 'Held';
						if ($held > 0) {
							$data[$so_id]['rpt_data'][$date]['remarks'] = $held;
						} else {
							$data[$so_id]['rpt_data'][$date]['remarks'] = '';
						}
					} elseif ($deposited == 0 && $opening_balance > $max_retain) {
						$count++;
						$held = sprintf('%0.2f', $opening_balance - $deposited);
						$data[$so_id]['rpt_data'][$date]['exceed'] = 'Held';
						if ($held > 0) {
							$data[$so_id]['rpt_data'][$date]['remarks'] = $held;
						} else {
							$data[$so_id]['rpt_data'][$date]['remarks'] = '';
						}
					} else {
						$count = 0;
						$data[$so_id]['rpt_data'][$date]['exceed'] = '';
						$data[$so_id]['rpt_data'][$date]['remarks'] = '';
					}
					if ($count > 0) {
						$data[$so_id]['rpt_data'][$date]['process_retained'] = $count;
					} else {
						$data[$so_id]['rpt_data'][$date]['process_retained'] = '';
					}
					$opening_balance = $balance ? $balance : 0;
				}
			}
			// exit;
			$this->set(compact('data', 'date_from', 'date_to', 'office_id'));
			/*pr($data);
    		exit;*/
		}
	}

	public function get_office_list()
	{
		$this->loadModel('Office');
		$rs = array(array('id' => '', 'name' => '---- All -----'));

		$parent_office_id = $this->request->data['region_office_id'];

		$office_conditions = array('Office.parent_office_id' => $parent_office_id, 'Office.office_type_id' => 2);

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
	public function get_territory_so_list()
	{
		$this->loadModel('SalesPerson');
		$this->loadModel('TerritoryAssignHistory');
		$view = new View($this);

		$form = $view->loadHelper('Form');

		$office_id = $this->request->data['office_id'];
		$date_from = date('Y-m-d', strtotime($this->request->data['date_from']));

		//get SO list
		$spo_so_group_ids = [4, 1008];
		$so_list_r = $this->SalesPerson->find('all', array(
			'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
			'conditions' => array(
				'SalesPerson.office_id' => $office_id,
				'SalesPerson.territory_id >' => 0,
				'User.user_group_id' => $spo_so_group_ids,
			),
			'recursive' => 0
		));


		foreach ($so_list_r as $key => $value) {
			$so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
		}

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
		if ($so_list) {
			$form->create('SalesDepositMonitor', array('role' => 'form', 'action' => 'index'));

			echo $form->input('so_id', array('label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $so_list));
			$form->end();
		} else {
			echo '';
		}


		$this->autoRender = false;
	}
}
