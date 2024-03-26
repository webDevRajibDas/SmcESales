<?php
App::uses('AppController', 'Controller');
/**
 * MemoNotifications Controller
 *
 * @property Memo $Memo
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class MemoNotificationsController extends AppController {
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
public function admin_index() {
	$this->set('page_title','Memo Notifications');
	$conditions = array();
	$office_parent_id = $this->UserAuth->getOfficeParentId();
	$territory_conditions=array();
	$thana_conditions=array();
	if($office_parent_id !=0)
	{
		$this->LoadModel('Office');
		$office_type = $this->Office->find('first',array(
			'conditions' => array('Office.id' => $this->UserAuth->getOfficeId()),
			'recursive'=>-1
			));
		$office_type_id = $office_type['Office']['office_type_id'];
	}


	if ($office_parent_id == 0) {
		$region_office_condition=array('office_type_id'=>3);
		$office_conditions = array('office_type_id'=>2, "NOT" => array( "id" => array(30, 31, 37)));
	} 
	else 
	{
		if($office_type_id==3)
		{
			$territory_conditions=array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
			$thana_conditions=array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
			$conditions[] = array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
			$region_office_condition=array('office_type_id'=>3,'Office.id' => $this->UserAuth->getOfficeId());
			$office_conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId(), 'office_type_id'=>2);
		}
		elseif($office_type_id==2)
		{

			$territory_conditions=array('Territory.office_id' => $this->UserAuth->getOfficeId());
			$thana_conditions=array('Territory.office_id' => $this->UserAuth->getOfficeId());
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(),'office_type_id'=>2);
			$conditions[] = array('Office.id' => $this->UserAuth->getOfficeId());
		}

	}
	$this->LoadModel('Territory');
	$this->LoadModel('ThanaTerritory');
	$thanas=$this->ThanaTerritory->find('all',
		array(
			'conditions'=>$thana_conditions,
			'joins'=>array(
				array(
					'table'=>'territories',
					'alias'=>'Territory',
					'type'=>'inner',
					'conditions'=>'Territory.id=ThanaTerritory.territory_id'
					),
				array(
					'table'=>'offices',
					'alias'=>'Office',
					'type'=>'inner',
					'conditions'=>'Office.id=Territory.office_id'
					),
				),
			'fields'=>array('Thana.id','Thana.name')
			)
		);
	$thana_list=array();
	foreach($thanas as $key => $value)
	{
		$thana_list[$value['Thana']['id']]=$value['Thana']['name'];
	}
	/*$this->Territory->unbindModel(
		array(
			'belongsTo'=>array('Office')
			)
			);*/
			$territory_list_r = $this->Territory->find('all', array(
				'fields' => array('Territory.id','Territory.name','SalesPerson.name'),
				'conditions' => $territory_conditions,
				'recursive'=>0
				)); 
			foreach($territory_list_r as $key => $value)
			{
				$territory_list[$value['Territory']['id']] = $value['Territory']['name'].' ('.$value['SalesPerson']['name'].')';
			}
			$this->set(compact('territory_list','thana_list'));
			
			$this->LoadModel('Office');

			$offices = $this->Office->find('list', array(
				'conditions'=> $office_conditions,
				'fields'=>array('office_name')
				));

			if(isset($region_office_condition))
			{
				$region_offices = $this->Office->find('list', array(
					'conditions' => $region_office_condition, 
					'order' => array('office_name' => 'asc')
					));

				$this->set(compact('region_offices'));
			}
			$this->set(compact('offices', 'office_id'));			
			$this->MemoNotification->recursive = 0;	
			$this->paginate = array(
				'conditions' =>$conditions,
				'joins'=>array(
					array(
						'table'=>'markets',
						'alias'=>'Market',
						'conditions'=>'Market.id=Outlet.market_id'
						),
					array(
						'table'=>'thanas',
						'alias'=>'Thana',
						'conditions'=>'Thana.id=Market.thana_id'
						),
					array(
						'table'=>'offices',
						'alias'=>'Office',
						'conditions'=>'Office.id=SalesPerson.office_id'
						)
					),
				'order' => array('MemoNotification.created_at' => 'desc')
				);

			$this->set('list',$this->paginate());		
		}

	}
