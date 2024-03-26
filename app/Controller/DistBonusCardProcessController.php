<?php
App::uses('AppController', 'Controller');

class DistBonusCardProcessController extends AppController
{
	public $components = array('Paginator', 'Session');
	public $uses = array('DistBonusEligibleOutlet', 'Product', 'ProductType', 'BonusCard', 'DistStoreBonusCard', 'DistMemo', 'DistMemoDetail', 'DistOutlet', 'Office', 'DistRoute');
	public function admin_index()
	{
		ini_set('max_execution_time', 99999);
		ini_set('memory_limit', '256M');
		$this->set('page_title', 'Bonus Card Process');
		$fiscalYears = $this->BonusCard->FiscalYear->find('list', array('fields' => array('year_code')));
		// $bonusCards = $this->BonusCard->find('list');
		$this->set(compact('fiscalYears'));
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id != 0) {
			$office_type = $this->Office->find('first', array(
				'conditions' => array('Office.id' => $this->UserAuth->getOfficeId()),
				'recursive' => -1
			));
			$office_type_id = $office_type['Office']['office_type_id'];
		}


		if ($office_parent_id == 0) {
			//$region_office_condition = array('office_type_id' => 3);
			$office_conditions = array('office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
		} else {
			if ($office_type_id == 3) {
				//$region_office_condition = array('office_type_id' => 3, 'Office.id' => $this->UserAuth->getOfficeId());
				$office_conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId(), 'office_type_id' => 2);
				/*if($this->request->data)
				{
					$office_conditions["Office.parent_office_id"]=$this->request->data['search']['region_office_id'];
				}*/
			} elseif ($office_type_id == 2) {
				$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'office_type_id' => 2);
			}
		}

		$offices = $this->Office->find('list', array(
			'conditions' => $office_conditions,
			'fields' => array('office_name')
		));

		/*if (isset($region_office_condition)) {
			$region_offices = $this->Office->find('list', array(
				'conditions' => $region_office_condition,
				'order' => array('office_name' => 'asc')
				));

			$this->set(compact('region_offices'));
		}*/
		$this->set(compact('offices', 'office_id'));

		if ($this->request->is('post')) {
			$office_id = $this->request->data['search']['office_id'];
			$fiscal_year_id = $this->request->data['search']['fiscal_year_id'];
			$bonus_card_id = $this->request->data['search']['bonus_card_id'];
			$route_id = $this->request->data['search']['route_id'];
			$conditions = array();
			if ($office_id && $route_id) {
				$conditions["DistRoute.id"] = $route_id;
			} elseif ($office_id && !$route_id) {
				$conditions["DistRoute.office_id"] = $office_id;
			}
			$conditions["DistStoreBonusCard.fiscal_year_id"] = $fiscal_year_id;
			$conditions["DistStoreBonusCard.bonus_card_id"] = $bonus_card_id;
			$data = $this->DistStoreBonusCard->find('all', array(
				'fields' => array('SUM(DistStoreBonusCard.no_of_stamp) as total_stamp', 'DistOutlet.name', 'DistMarket.name', 'DistRoute.name', 'DistOutlet.id'),
				'conditions' => $conditions,
				'joins' => array(

					array(
						'table' => 'dist_outlets',
						'alias' => 'DistOutlet',
						'conditions' => 'DistOutlet.id=DistStoreBonusCard.outlet_id'
					),
					array(
						'table' => 'dist_markets',
						'alias' => 'DistMarket',
						'conditions' => 'DistMarket.id=DistStoreBonusCard.market_id'
					),
					array(
						'table' => 'dist_routes',
						'alias' => 'DistRoute',
						'conditions' => 'DistRoute.id=DistMarket.dist_route_id'
					),
				),
				'group' => array('DistOutlet.name', 'DistOutlet.id', 'DistMarket.name', 'DistRoute.name'),
				'order' => array('DistRoute.name', 'DistMarket.name', 'DistOutlet.name'),
				'recursive' => -1
			));
			$eligible_outlet = $this->DistBonusEligibleOutlet->find('list', array(
				'fields' => array('outlet_id', 'is_eligible'),
				'conditions' => array(
					'DistBonusEligibleOutlet.bonus_card_id' => $bonus_card_id,
					'DistBonusEligibleOutlet.fiscal_year_id' => $fiscal_year_id
				)
			));

			$this->set(compact('data', 'eligible_outlet'));
		}
	}
	public function admin_set_eligible_bonus_outlet()
	{
		if ($this->request->is('post')) {
			// pr($this->request->data);exit;
			$post_data = $this->request->data['DistBonusCardProcess'];
			// $this->BonusEligibleOutlet->deleteAll(array('bonus_card_id'=>$post_data['bonus_card_id'],'fiscal_year_id'=>$post_data['fiscal_year_id']));
			$bonus_info = $this->BonusCard->find('first', array('conditions' => array('BonusCard.id' => $post_data['bonus_card_id']), 'recursive' => -1));
			$bonus_type_id = $bonus_info['BonusCard']['bonus_card_type_id'];

			$data_array = array();

			foreach ($post_data['eligible_outlet'] as $key => $value) {
				$previous_data = $this->DistBonusEligibleOutlet->find('first', array(
					'conditions' => array(
						'bonus_card_id' => $post_data['bonus_card_id'],
						'fiscal_year_id' => $post_data['fiscal_year_id'],
						'outlet_id' => $key,
					),
					'recursive' => -1
				));
				if ($previous_data) {
					$data['id'] = $previous_data['DistBonusEligibleOutlet']['id'];
					$data['is_eligible'] = $post_data['eligible_outlet'][$key];
				} else {
					$data['bonus_card_id'] = $post_data['bonus_card_id'];
					$data['bonus_type_id'] = $bonus_type_id;
					$data['fiscal_year_id'] = $post_data['fiscal_year_id'];
					$data['outlet_id'] = $key;
					$data['is_eligible'] = $value;
				}
				$data_array[] = $data;
				unset($data);
			}


			// $this->LoadModel('BonusEligibleOutlet');
			if ($this->DistBonusEligibleOutlet->saveAll($data_array)) {
				$this->Session->setFlash(__('Bonus Card Has Been Processed'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('Please Try Again!'), 'flash/warning');
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$this->Session->setFlash(__('Please Try Again!'), 'flash/warning');
			$this->redirect(array('action' => 'index'));
		}
	}
	public function get_route_list()
	{
		$view = new View($this);

		$form = $view->loadHelper('Form');

		$office_id = $this->request->data['office_id'];

		//get territory list
		$route_list = $this->DistRoute->find('list', array(
			'conditions' => array(
				'DistRoute.office_id' => $office_id,
			),
			'order' => array('RTRIM(LTRIM(DistRoute.name))'),
			'recursive' => -1
		));

		if (isset($route_list)) {
			$form->create('search', array('role' => 'form', 'action' => 'index'));

			echo $form->input('route_id', array('class' => 'form-control route_id', 'empty' => '--- Select---', 'options' => $route_list));
		} else {
			$form->create('search', array('role' => 'form', 'action' => 'index'));
			echo $form->input('route_id', array('class' => 'form-control route_id', 'empty' => '--- Select---'));
			$form->end();
		}


		$this->autoRender = false;
	}
}
