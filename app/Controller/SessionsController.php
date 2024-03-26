<?php
App::uses('AppController', 'Controller');
/**
 * Sessions Controller
 *
 * @property Doctor $Doctor
 * @property PaginatorComponent $Paginator
 */
class SessionsController extends AppController {

/**
 * Components
 *
 * @var array
 */
public $components = array('Paginator', 'Session', 'Filter.Filter');
public $uses = array('ProgramSession','UserTerritoryList');
/**
 * admin_index method
 *
 * @return void
 */
public function admin_index() {
	$this->set('page_title','Session List');
	$this->ProgramSession->recursive = 0;
	if($this->UserAuth->getOfficeParentId() !=0 ){
		$conditions = array('Territory.office_id' => $this->UserAuth->getOfficeId());
		$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
	}else{
		$conditions = array();
		$office_conditions = array();
	}

	$this->paginate = array('conditions' => $conditions, 'order' => array('ProgramSession.id' => 'DESC'));
	$this->set('sessions', $this->paginate());

	$this->loadModel('Office');
	$this->loadModel('SalesPerson');
	$offices = $this->Office->find('list',array(
		'conditions' => $office_conditions,
		'order' => array('Office.office_name'=>'asc')
		));

	$office_id = isset($this->request->data['Session']['office_id'])!='' ? $this->request->data['Session']['office_id'] : 0;
	$territory_id = isset($this->request->data['Session']['territory_id'])!='' ? $this->request->data['Session']['territory_id'] : 0;
	$territories = $this->ProgramSession->Territory->find('list',array(
		'conditions' => array('Territory.office_id'=>$this->UserAuth->getOfficeId()),
		'order' => array('Territory.name'=>'asc')
		));		
	$salesPersons = $this->SalesPerson->find('list',array(
		'conditions' => array('SalesPerson.designation_id'=> 3,'SalesPerson.office_id'=> $this->UserAuth->getOfficeId()),
		'order' => array('SalesPerson.name'=>'asc')
		));	
	$sessionTypes=$this->ProgramSession->SessionType->find('list');							
	$this->set(compact('offices','territories','salesPersons','sessionTypes'));

}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
public function admin_view($id = null) {
	$this->set('page_title','Session Details');
	if (!$this->ProgramSession->exists($id)) {
		throw new NotFoundException(__('Invalid doctor'));
	}
	$options = array('conditions' => array('ProgramSession.' . $this->ProgramSession->primaryKey => $id),'recursive'=>0);
	$this->set('sessions', $this->ProgramSession->find('first', $options));

	$sessiondetails = $this->ProgramSession->SessionDetail->find('all',array(
		'conditions' => array('SessionDetail.session_id'=> $id),
		'recursive' => 0
		));

	$this->set('sessiondetails',$sessiondetails);			
}


/**
 * admin_add method
 *
 * @return void
 */
public function admin_add() {

	$this->set('page_title','Add Session');
	$territories = array();
	$salesPersons = array();

	if ($this->request->is('post')) {
		$this->ProgramSession->create();
		$this->request->data['ProgramSession']['session_date'] = date('Y-m-d',strtotime($this->request->data['ProgramSession']['session_date']));
		$this->request->data['ProgramSession']['created_at'] = $this->current_datetime();
		$this->request->data['ProgramSession']['created_by'] = $this->UserAuth->getUserId();			
		$this->request->data['ProgramSession']['updated_at'] = $this->current_datetime();
		$this->request->data['ProgramSession']['updated_by'] = $this->UserAuth->getUserId();
		$this->request->data['ProgramSession']['action'] = "1";			
		if ($this->ProgramSession->save($this->request->data)) {
			$this->Session->setFlash(__('The Session has been saved'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		} else {
			$this->loadModel('SalesPerson');

			$office_id = isset($this->request->data['ProgramSession']['office_id'])!='' ? $this->request->data['ProgramSession']['office_id'] : 0;
			$territory_id = isset($this->request->data['ProgramSession']['territory_id'])!='' ? $this->request->data['ProgramSession']['territory_id'] : 0;
			$territories = $this->ProgramSession->Territory->find('list',array(
				'conditions' => array('Territory.office_id'=>$office_id),
				'order' => array('Territory.name'=>'asc')
				));		
			$salesPersons = $this->SalesPerson->find('list',array(
				'conditions' => array('SalesPerson.designation_id'=> 3,'SalesPerson.territory_id'=> $territory_id),
				'order' => array('SalesPerson.name'=>'asc')
				));						

		}
	}

	$office_parent_id = $this->UserAuth->getOfficeParentId();
	if($office_parent_id == 0){
		$office_conditions = array();
	}else{
		$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
	}

	$this->loadModel('Office');
	$offices = $this->Office->find('list',array(
		'conditions' => $office_conditions,
		'order' => array('Office.office_name'=>'asc')
		));	
	$sessionTypes=$this->ProgramSession->SessionType->find('list');
	$this->set(compact('offices','territories','salesPersons','sessionTypes'));
}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
public function admin_edit($id = null) {
	$this->set('page_title','Edit Session');
	$territories = array();

	$this->ProgramSession->id = $id;
	if (!$this->ProgramSession->exists($id)) {
		throw new NotFoundException(__('Invalid ProgramSession'));
	}
	if ($this->request->is('post') || $this->request->is('put')) {
		$this->request->data['ProgramSession']['session_date'] = date('Y-m-d',strtotime($this->request->data['ProgramSession']['session_date']));
		$this->request->data['ProgramSession']['updated_by'] = $this->UserAuth->getUserId();
		$this->request->data['ProgramSession']['updated_at'] = $this->current_datetime();
		$this->request->data['ProgramSession']['action'] = "2";	
		if ($this->ProgramSession->save($this->request->data)) {
			$this->Session->setFlash(__('The Session has been saved'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
	} else {
		$options = array('conditions' => array('ProgramSession.' . $this->ProgramSession->primaryKey => $id),'recursive'=>0);
		$this->request->data = $this->ProgramSession->find('first', $options);
		$this->request->data['ProgramSession']['office_id'] = $this->request->data['Territory']['office_id'];

		$this->loadModel('SalesPerson');

		$office_id = isset($this->request->data['ProgramSession']['office_id'])!='' ? $this->request->data['ProgramSession']['office_id'] : $this->request->data['Territory']['office_id'];
		$territory_id = isset($this->request->data['ProgramSession']['territory_id'])!='' ? $this->request->data['ProgramSession']['territory_id'] : $this->request->data['ProgramSession']['office_id'];
		$territories = $this->ProgramSession->Territory->find('list',array(
			'conditions' => array('Territory.office_id'=>$office_id),
			'order' => array('Territory.name'=>'asc')
			));		
		$salesPersons = $this->SalesPerson->find('list',array(
			'conditions' => array('SalesPerson.designation_id'=> 3,'SalesPerson.territory_id'=> $territory_id),
			'order' => array('SalesPerson.name'=>'asc')
			));	

	}

	$office_parent_id = $this->UserAuth->getOfficeParentId();
	if($office_parent_id == 0){
		$office_conditions = array();
	}else{
		$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
	}

	$this->loadModel('Office');
	$offices = $this->Office->find('list',array(
		'conditions' => $office_conditions,
		'order' => array('Office.office_name'=>'asc')
		));	
	$sessionTypes=$this->ProgramSession->SessionType->find('list');	
	$this->set(compact('offices','territories','salesPersons','sessionTypes'));
}

function admin_session_map(){
	$this->set('page_title','Session Map');

	$message = '';
    $map_data = array();

	if(($this->request->is('post') || $this->request->is('put'))){

		$conditions = array();
		if (!empty($this->request->data['Session']['office_id'])) {
			$conditions[] = array('Territory.office_id' => $this->request->data['Session']['office_id']);
		}
		if (!empty($this->request->data['Session']['territory_id'])) {
			$conditions[] = array('ProgramSession.territory_id' => $this->request->data['Session']['territory_id']);
		}
		if (!empty($this->request->data['Session']['date_from'])) {
			$conditions[] = array('ProgramSession.session_date >=' => Date('Y-m-d',strtotime($this->request->data['Session']['date_from'])));
		}
		if (!empty($this->request->data['Session.date_to'])) {
			$conditions[] = array('ProgramSession.session_date <=' => Date('Y-m-d',strtotime($this->request->data['Session']['date_to'])));
		}
		if(!empty($this->request->data['Session']['session_type_id'])){
			$conditions[] = array('ProgramSession.session_type_id' =>$this->request->data['Session']['session_type_id']);
		}
		$session_list=$this->ProgramSession->find('all',array('conditions'=>$conditions));
		if (!empty($session_list)) {
                foreach ($session_list as $val) {
                    if ($val['ProgramSession']['latitude'] > 0 AND $val['ProgramSession']['longitude'] > 0) {
                        $data['title'] = $val['ProgramSession']['name'];
                        $data['lng'] = $val['ProgramSession']['longitude'];
                        $data['lat'] = $val['ProgramSession']['latitude'];
                        $data['description'] = '<p><b>Session Name : ' . $val['ProgramSession']['name'] . '</b></br>' .
                        '<b>Sales Person Name : </b>' . $val['SalesPerson']['name'] . '</br>' .
                        '<b>Territory : </b>' . $val['Territory']['name'] . '</p>' .
                        '<p>Session Type : ' . $val['SessionType']['name'] . '</br>' .
                        'Session arranged date : ' . date('d-M-Y', strtotime($val['ProgramSession']['session_arranged_date'])) . '</br>' .
                        'Total participant : ' . $val['ProgramSession']['total_participant'] .'</br>'.'Total Attend : '. $val['ProgramSession']['total_attend'].' ( Male-'.$val['ProgramSession']['total_male'].', Female -'.$val['ProgramSession']['total_female'].')'.'</p>' .
                        '<a class="btn btn-primary btn-xs" href="' . BASE_URL . 'admin/sessions/view/' . $val['ProgramSession']['id'] . '" target="_blank">Session Details</a>';
                        $map_data[] = $data;
                    }
                }
            }
            if (!empty($map_data))
                $message = '';
            else
                $message = '<div class="alert alert-danger">No memo found.</div>';

		//$this->set(compact('offices', 'territories', ));
	}


	if($this->UserAuth->getOfficeParentId() !=0 ){
		$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
	}else{
		$conditions = array();
		$office_conditions = array();
	}

	$this->loadModel('Office');
	$this->loadModel('SalesPerson');
	$offices = $this->Office->find('list',array(
		'conditions' => $office_conditions,
		'order' => array('Office.office_name'=>'asc')
		));

	$office_id = isset($this->request->data['Session']['office_id'])!='' ? $this->request->data['Session']['office_id'] : 0;
	$territory_id = isset($this->request->data['Session']['territory_id'])!='' ? $this->request->data['Session']['territory_id'] : 0;
	$territories = $this->ProgramSession->Territory->find('list',array(
		'conditions' => array('Territory.office_id'=>$this->UserAuth->getOfficeId()),
		'order' => array('Territory.name'=>'asc')
		));		
	$salesPersons = $this->SalesPerson->find('list',array(
		'conditions' => array('SalesPerson.designation_id'=> 3,'SalesPerson.office_id'=> $this->UserAuth->getOfficeId()),
		'order' => array('SalesPerson.name'=>'asc')
		));	
	$sessionTypes=$this->ProgramSession->SessionType->find('list');							
	$this->set(compact('offices','territories','salesPersons','sessionTypes','map_data', 'message'));
}

public function get_territory_list(){
		$this->loadModel('Territory');
		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		$office_id = $this->request->data['office_id'];
        $territory = $this->Territory->find('all', array(
			'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name','User.id','User.user_group_id'),
			'conditions' => array('Territory.office_id' => $office_id),
			'joins'=>array(
				array(
					'table'=>'users',
					'alias'=>'User',
					'type'=>'inner',
					'conditions'=>'User.sales_person_id=SalesPerson.id'
					)
				),
			'order' => array('Territory.name' => 'asc'),
			'recursive' => 0
		));
		
		// pr($territory);exit;
		
		//$data_array = Set::extract($territory, '{n}.Territory');
		
		$data_array = array();
		
		foreach($territory as $key => $value)
		{
			$user_group_id= $value['User']['user_group_id'];
			$user_id= $value['User']['id'];
			if($user_group_id == 1008)
			{
				$user_territory=$this->UserTerritoryList->find('list',
					array('conditions'=>array('UserTerritoryList.user_id'=>$user_id))
					);
				if($user_territory)
				{
					$data_array[] = array(
						'id' => $value['Territory']['id'],
						'name' => $value['Territory']['name'].' ('.$value['SalesPerson']['name'].')',
						);
				}
				else
				{
					continue;
				}
			}
			else
			{
				$data_array[] = array(
						'id' => $value['Territory']['id'],
						'name' => $value['Territory']['name'].' ('.$value['SalesPerson']['name'].')',
						);
			}
			
		}
		
		//pr($data_array);
		
		if(!empty($territory)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}	

}
