<?php
App::uses('AppController', 'Controller');
/**
 * DayCloses Controller
 *
 * @property Memo $Memo
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DayClosesController extends AppController {
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
		$this->set('page_title','Day Close History');
		
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if($office_parent_id == 0){
			$conditions = array();
			$office_conditions = array();
		}else{
			$conditions = array('Territory.office_id' => $this->UserAuth->getOfficeId());
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}
			
		$this->DayClose->recursive = 0;	
		$this->paginate = array(
			'conditions' => $conditions,
			'order' => array('DayClose.created_at' => 'desc')
		);
		
		$this->set('list',$this->paginate());
		$this->set('office_id',$this->UserAuth->getOfficeId());
		
		$this->loadModel('Office');
		$this->loadModel('Territory');
		$this->loadModel('SalesPerson');
		
		$offices = $this->Office->find('list',array('conditions'=> $office_conditions,'order'=>array('office_name'=>'asc')));
		$office_id = isset($this->request->data['DayClose']['office_id'])!='' ? $this->request->data['DayClose']['office_id'] : 0;
		$territory_id = isset($this->request->data['DayClose']['territory_id'])!='' ? $this->request->data['DayClose']['territory_id'] : 0;
		
		/*$territories = $this->Territory->find('list',array(
						'conditions' => array('Territory.office_id'=>$office_id),
						'order' => array('Territory.name'=>'asc')
					));
		$salesPersons = $this->SalesPerson->find('list',array(
						'conditions' => array('SalesPerson.territory_id'=> $territory_id),
						'order' => array('SalesPerson.name'=>'asc')
					));	*/	
		$territory = $this->Territory->find('all', array(
			'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
			'conditions' => array('Territory.office_id' => $office_id),
			'order' => array('Territory.name' => 'asc'),
			'recursive' => 0
			));
		$territories=array();
		foreach($territory as $key => $value)
		{
			$territories[$value['Territory']['id']]=$value['Territory']['name'].' ('.$value['SalesPerson']['name'].')';
		}				
		$this->set(compact('offices','territories'));	
	}

}
