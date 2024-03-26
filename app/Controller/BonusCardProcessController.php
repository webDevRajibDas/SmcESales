<?php 
App::uses('AppController', 'Controller');

class BonusCardProcessController extends AppController 
{
	public $components = array('Paginator', 'Session');
	public $uses = array('BonusEligibleOutlet','Product','ProductType','BonusCard','StoreBonusCard','Memo','MemoDetail','Outlet','Office','Territory');
	public function admin_index() 
	{
		ini_set('max_execution_time', 99999);
		ini_set('memory_limit', '256M');
		$this->set('page_title','Bonus Card Process');
		$fiscalYears = $this->BonusCard->FiscalYear->find('list',array('fields'=>array('year_code')));
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
			
		} 
		else 
		{
			if ($office_type_id == 3) 
			{
				//$region_office_condition = array('office_type_id' => 3, 'Office.id' => $this->UserAuth->getOfficeId());
				$office_conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId(), 'office_type_id' => 2);
				/*if($this->request->data)
				{
					$office_conditions["Office.parent_office_id"]=$this->request->data['search']['region_office_id'];
				}*/
			} 
			elseif ($office_type_id == 2)
			{
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

		if($this->request->is('post'))
		{
			$office_id=$this->request->data['search']['office_id'];
			$fiscal_year_id=$this->request->data['search']['fiscal_year_id'];
			$bonus_card_id=$this->request->data['search']['bonus_card_id'];
			$territory_id=$this->request->data['search']['territory_id'];
			$conditions=array();
			if($office_id && $territory_id)
			{
				$conditions["StoreBonusCard.territory_id"]=$territory_id;
			}
			elseif($office_id && !$territory_id)
			{
				$conditions["Territory.office_id"]=$office_id;
			}
			$conditions["StoreBonusCard.fiscal_year_id"]=$fiscal_year_id;
			$conditions["StoreBonusCard.bonus_card_id"]=$bonus_card_id;
			$data=$this->StoreBonusCard->find('all',array(
				'fields'=>array('SUM(StoreBonusCard.no_of_stamp) as total_stamp','Outlet.name','Market.name','Thana.name','Outlet.id'),
				'conditions'=>$conditions,
				'joins'=>array(
					array(
						'table'=>'territories',
						'alias'=>'Territory',
						'conditions'=>'Territory.id=StoreBonusCard.territory_id'
						),
					array(
						'table'=>'outlets',
						'alias'=>'Outlet',
						'conditions'=>'Outlet.id=StoreBonusCard.outlet_id'
						),
					array(
						'table'=>'markets',
						'alias'=>'Market',
						'conditions'=>'Market.id=StoreBonusCard.market_id'
						),
					array(
						'table'=>'thanas',
						'alias'=>'Thana',
						'conditions'=>'Thana.id=Market.thana_id'
						),
					),
				'group'=>array('Outlet.name','Outlet.id','Market.name','Thana.name'),
				'order'=>array('Thana.name','Market.name','Outlet.name'),
				'recursive'=>-1
				));
			$eligible_outlet=$this->BonusEligibleOutlet->find('list',array(
				'fields'=>array('outlet_id','is_eligible'),
				'conditions'=>array(
					'BonusEligibleOutlet.bonus_card_id'=>$bonus_card_id,
					'BonusEligibleOutlet.fiscal_year_id'=>$fiscal_year_id
				)
				));
			// pr($data);exit;
			$this->set(compact('data','eligible_outlet'));
		}
	}
	public function admin_set_eligible_bonus_outlet()
	{
		if($this->request->is('post'))
		{
			// pr($this->request->data);exit;
			$post_data=$this->request->data['BonusCardProcess'];
			// $this->BonusEligibleOutlet->deleteAll(array('bonus_card_id'=>$post_data['bonus_card_id'],'fiscal_year_id'=>$post_data['fiscal_year_id']));
			$bonus_info=$this->BonusCard->find('first',array('conditions'=>array('BonusCard.id'=>$post_data['bonus_card_id']),'recursive'=>-1));
			$bonus_type_id=$bonus_info['BonusCard']['bonus_card_type_id'];
			
			$data_array=array();
			
				foreach($post_data['eligible_outlet'] as $key=>$value)
				{
					$previous_data= $this->BonusEligibleOutlet->find('first',array(
						'conditions'=>array(
							'bonus_card_id'=>$post_data['bonus_card_id'],
							'fiscal_year_id'=>$post_data['fiscal_year_id'],
							'outlet_id'=>$key,
							),
						'recursive'=>-1
						));
					if($previous_data)
					{
						$data['id']=$previous_data['BonusEligibleOutlet']['id'];
						$data['is_eligible']=$post_data['eligible_outlet'][$key];
					
					}
					else
					{
						$data['bonus_card_id']=$post_data['bonus_card_id'];
						$data['bonus_type_id']=$bonus_type_id;
						$data['fiscal_year_id']=$post_data['fiscal_year_id'];
						$data['outlet_id']=$key;
						$data['is_eligible']=$value;
					}
					$data_array[]=$data;
					unset($data);
				}
			
			
			// $this->LoadModel('BonusEligibleOutlet');
			if($this->BonusEligibleOutlet->saveAll($data_array))
			{
				$this->Session->setFlash(__('Bonus Card Has Been Processed'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('Please Try Again!'), 'flash/warning');
				$this->redirect(array('action' => 'index'));
			}
		}
		else{
			$this->Session->setFlash(__('Please Try Again!'), 'flash/warning');
			$this->redirect(array('action' => 'index'));
		}
	}
	public function get_territory_list() {
		$view = new View($this);

		$form = $view->loadHelper('Form');

		$office_id = $this->request->data['office_id'];

        //get territory list
		$territory_list_r = $this->Territory->find('all', array(
			'fields' => array('Territory.id', 'Territory.name','SalesPerson.name'),
			'conditions' => array(
				'Territory.office_id' => $office_id,
				),
			'joins'=>array(
				array(
					'table'=>'sales_people',
					'alias'=>'SalesPerson',
					'type'=>'LEFT',
					'conditions'=>array('SalesPerson.territory_id=Territory.id')
					)
				),
			'recursive' => -1
			));

		// pr($territory_list_r);exit;
		foreach ($territory_list_r as $key => $value) {
			$territory_list[$value['Territory']['id']] = $value['Territory']['name'].'('.$value['SalesPerson']['name'].')';
		}


		if (isset($territory_list)) {
			$form->create('search', array('role' => 'form', 'action' => 'index'));

			echo $form->input('territory_id', array('class' => 'form-control territory_id', 'empty' => '--- Select---', 'options' => $territory_list));

		} else {
			$form->create('search', array('role' => 'form', 'action' => 'index'));
			echo $form->input('territory_id', array('class' => 'form-control territory_id', 'empty' => '--- Select---'));
			$form->end();
		}


		$this->autoRender = false;
	}

}
