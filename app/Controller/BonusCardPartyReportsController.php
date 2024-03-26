<?php
App::uses('AppController', 'Controller');
/**
 * OutletCharacteristicSettings Controller
 *
 * @property OutletCharacteristicReport $OutletCharacteristicReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class BonusCardPartyReportsController extends AppController
{



	/**
	 * Components
	 *
	 * @var array
	 */

	public $uses = array('Memo', 'Office', 'Outlet');
	public $components = array('Paginator', 'Session', 'Filter.Filter');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index($id = null)
	{
		//Configure::write('debug',2);

		ini_set('memory_limit', '2048M');
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes


		$this->set('page_title', "Bonus Card Party Report (ORSaline-N+ORSaline-N (25pcs))");

		$request_data = array();


		//for region office list
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id' => 3),
			'order' => array('office_name' => 'asc')
		));

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

		$dis_con = array();

		$resutl = array();


		if ($this->request->is('post') || $this->request->is('put')) {
			$date_from = date('Y-m-d', strtotime($this->request->data['BonusCardPartyReport']['date_from']));
			$date_to = date('Y-m-d', strtotime($this->request->data['BonusCardPartyReport']['date_to']));
			$this->set(compact('date_from', 'date_to'));
			$region_office_id = isset($this->request->data['BonusCardPartyReport']['region_office_id']) != '' ? $this->request->data['BonusCardPartyReport']['region_office_id'] : $region_office_id;
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
			$office_id = isset($this->request->data['BonusCardPartyReport']['office_id']) != '' ? $this->request->data['BonusCardPartyReport']['office_id'] : $office_id;
			$territory_id = isset($this->request->data['BonusCardPartyReport']['territory_id']) != '' ? $this->request->data['BonusCardPartyReport']['territory_id'] : '';
			$this->set(compact('office_id'));
			$conditions = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Memo.gross_value >=' => 0,
				'Memo.status !=' => 0,
				'MemoDetail.product_id' => array(47, 644),
				'MemoDetail.price >' => 0,
				'Outlet.bonus_type_id' => array(1, 2),
			);
			if ($office_ids) $conditions['Memo.office_id'] = $office_ids;
			if ($office_id) $conditions['Memo.office_id'] = $office_id;
			if ($territory_id) $conditions['Market.territory_id'] = $territory_id;
			$result = $this->Memo->find('all', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'table' => 'memo_details',
						'alias' => 'MemoDetail',
						'conditions' => 'MemoDetail.memo_id=Memo.id'
					),
					array(
						'table' => 'outlets',
						'alias' => 'Outlet',
						'conditions' => 'Outlet.id=Memo.outlet_id'
					),
					array(
						'table' => 'markets',
						'alias' => 'Market',
						'conditions' => 'Outlet.market_id=Market.id'
					),
					array(
						'table' => 'thanas',
						'alias' => 'Thana',
						'conditions' => 'Thana.id=Market.thana_id'
					),
					array(
						'table' => 'territories',
						'alias' => 'Territory',
						'conditions' => 'Territory.id=Market.territory_id'
					),
					array(
						'table' => 'offices',
						'alias' => 'Office',
						'conditions' => 'Office.id=Territory.office_id'
					),
				),
				'group' => array(
					'Office.office_name',
					'Office.order',
					'Territory.name',
					'Thana.name',
					'Market.name',
					'Outlet.name',
					'Outlet.id',
					'Outlet.bonus_type_id',
					'Memo.id'
				),
				'order' => array('Office.order'),
				'fields' => array(
					'Office.office_name',
					'Office.order',
					'Territory.name',
					'Thana.name',
					'Market.name',
					'Outlet.name',
					'Outlet.id',
					'Outlet.bonus_type_id',
					'Memo.id',
					'SUM(MemoDetail.sales_qty) as total_qty',
					'sum(case when MemoDetail.product_id=47 then MemoDetail.sales_qty end) as total_qty_47',
					'sum(case when MemoDetail.product_id=644 then MemoDetail.sales_qty end) as total_qty_644'
				),
				'recursive' => -1
			));

			$result_set = array();
			foreach ($result as $data) {
				$total_eligible_47=0;
				$total_eligible_644=0;
				if($data['Outlet']['bonus_type_id'] == 1 && ($data[0]['total_qty']+0) >= 10 ){
					if(($data[0]['total_qty_47']+0)>=10){
						$total_eligible_47 = $data[0]['total_qty_47'];
					}
					if(($data[0]['total_qty_644']+0)>=10){
						$total_eligible_644 = $data[0]['total_qty_644'];
					}
				}
				if($data['Outlet']['bonus_type_id'] == 2 && $data[0]['total_qty'] >= 20 ){
					if(($data[0]['total_qty_47']+0)>=20){
						$total_eligible_47 = $data[0]['total_qty_47'];
					}
					if(($data[0]['total_qty_644']+0)>=20){
						$total_eligible_644 = $data[0]['total_qty_644'];
					}
				}
				$result_set[$data['Outlet']['id']] = array(
					'office' => $data['Office']['office_name'],
					'territory' => $data['Territory']['name'],
					'thana' => $data['Thana']['name'],
					'market' => $data['Market']['name'],
					'outlet' => $data['Outlet']['name'],
					'bonus_type' => $data['Outlet']['bonus_type_id'],
					'total_qty' => (isset($result_set[$data['Outlet']['id']]['total_qty']) ? $result_set[$data['Outlet']['id']]['total_qty'] : 0) + $data[0]['total_qty'],
					'total_qty_47' => (isset($result_set[$data['Outlet']['id']]['total_qty_47']) ? $result_set[$data['Outlet']['id']]['total_qty_47'] : 0) + $data[0]['total_qty_47'],
					'total_qty_644' => (isset($result_set[$data['Outlet']['id']]['total_qty_644']) ? $result_set[$data['Outlet']['id']]['total_qty_644'] : 0) + $data[0]['total_qty_644'],
					'total_eligible_47' => (isset($result_set[$data['Outlet']['id']]['total_eligible_47']) ? $result_set[$data['Outlet']['id']]['total_eligible_47'] : 0) + $total_eligible_47,
					'total_eligible_644' => (isset($result_set[$data['Outlet']['id']]['total_eligible_644']) ? $result_set[$data['Outlet']['id']]['total_eligible_644'] : 0) + $total_eligible_644,
					/* 
						47=ORSalaine (20 pcs)
						644= Orsalaine (25pcs)
						small bonus =1
						if selected outlet bonus type is small and sales qty (47+644) >=10 this memo is eligible for this outlet and sum with total eligible qty

						small bonus =2
						if selected outlet bonus type is small and sales qty (47+644) >=20 this memo is eligible for this outlet and sum with total eligible qty
					*/
					'eligible_qty' => (isset($result_set[$data['Outlet']['id']]['eligible_qty']) ? $result_set[$data['Outlet']['id']]['eligible_qty'] : 0) + ($total_eligible_47+$total_eligible_644)
					//(isset($result_set[$data['Outlet']['id']]['eligible_qty']) ? $result_set[$data['Outlet']['id']]['eligible_qty'] : 0) + ($data['Outlet']['bonus_type_id'] = 1 && $data[0]['total_qty'] >= 10 ? $data[0]['total_qty'] : $data['Outlet']['bonus_type_id'] = 1 && $data[0]['total_qty'] >= 20 ? $data[0]['total_qty'] : 0)
				);
			}
			$this->set(compact('result_set'));
		}

		$this->set(compact('offices',  'region_offices', 'resutl',  'request_data'));
	}
	public function get_territory_list()
	{
		$this->loadModel('Territory');
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$office_id = $this->request->data['office_id'];
		if ($office_id) {

			/***Show Except Child Territory ***/

			$child_territory_id = $this->Territory->find('list', array(
				'conditions' => array(
					'parent_id !=' => 0,
				),
				'fields' => array('Territory.id', 'Territory.name'),
			));

			$territory = $this->Territory->find('all', array(
				'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
				'conditions' => array('Territory.office_id' => $office_id, 'NOT' => array('Territory.id' => array_keys($child_territory_id))),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => 0
			));

			// echo $this->Territory->getLastquery();exit;

		}

		//pr($territory);

		//$data_array = Set::extract($territory, '{n}.Territory');

		$data_array = array();

		foreach ($territory as $key => $value) {
			$data_array[] = array(
				'id' => $value['Territory']['id'],
				'name' => $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')',
			);
		}

		//pr($data_array);

		if (!empty($territory)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}
	public function get_office_list()
	{
		$rs = array(array('id' => '', 'name' => '---- All -----'));

		$parent_office_id = $this->request->data['region_office_id'];

		$office_conditions = array('NOT' => array("id" => array(30, 31, 37)), 'Office.office_type_id' => 2);
		//$office_conditions = array('Office.parent_office_id' => $parent_office_id, 'Office.office_type_id'=>2);

		if ($parent_office_id) $office_conditions['Office.parent_office_id'] = $parent_office_id;

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
}
