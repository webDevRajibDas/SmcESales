<?php
App::uses('AppController', 'Controller');
/**
 * Doctors Controller
 *
 * @property Doctor $Doctor
 * @property PaginatorComponent $Paginator
 */
class DoctorsController extends AppController {

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
	$this->set('page_title','Doctor List');
	$this->loadModel('District');
	$this->Doctor->recursive = 0;
	if($this->UserAuth->getOfficeParentId() !=0 ){
		$conditions = array('Territory.office_id' => $this->UserAuth->getOfficeId());
	}else{
		$conditions = array();
	}
	$this->paginate = array(
		'conditions' => $conditions, 
		'order' => array('Doctor.id' => 'DESC'),
		'joins'=>array(
			array(
				'table'=>'thanas',
				'alias'=>'Thana',
				'conditions'=>'Thana.id=Market.thana_id'
				),
			),
		'fields'=>array('Doctor.*','Outlet.*','Market.*','Territory.*','DoctorType.*','DoctorQualification.*','Thana.id','Thana.name')
		);
	$this->set('doctors', $this->paginate());
	$this->loadModel('Office');

		/*$offices = $this->Office->find('list',array(
			'conditions' => array('id'=>$this->UserAuth->getOfficeId()),
			'order' => array('Office.office_name'=>'asc')
			));*/

			$office_parent_id = $this->UserAuth->getOfficeParentId();
			if($office_parent_id == 0)
			{					
				$office_conditions = array(
					'office_type_id' => 2,
					"NOT" => array( "id" => array(30, 31, 37))
					);
			}else{				
				$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			}

			$offices = $this->Office->find('list', array(
				'conditions'=> $office_conditions, 
				'order'=>array('office_name'=>'asc')
				));

			$doctorQualifications = $this->Doctor->DoctorQualification->find('list');
			$doctorTypes = $this->Doctor->DoctorType->find('list');
			$office_id = isset($this->request->data['Doctor']['office_id'])!='' ? $this->request->data['Doctor']['office_id'] : 0;
			$territory_id = isset($this->request->data['Doctor']['territory_id'])!='' ? $this->request->data['Doctor']['territory_id'] : 0;
			
			
			$this->loadModel('Territory');
			$territory = $this->Territory->find('all', array(
				'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
				'conditions' => array('Territory.office_id' => $office_id),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => 0
			));
						
			$data_array = array();
			
			foreach($territory as $key => $value)
			{
				$t_id=$value['Territory']['id'];
				$t_val=$value['Territory']['name'].' ('.$value['SalesPerson']['name'].')';
				$data_array[$t_id] =$t_val;
			}
					
			 $territories =$data_array;
			
			/*
			
			$territories = $this->Doctor->Territory->find('list',array(
				'conditions' => array('Territory.office_id'=>$office_id),
				'order' => array('Territory.name'=>'asc')
				));			
			*/

			
			$markets = $this->Doctor->Market->find('list',array(
				'conditions' => array('Market.territory_id'=> $territory_id),
				'order' => array('Market.name'=>'asc')
				));
			$districts = $this->District->find('list',array(
				'order' => array('District.name'=>'asc')
				));


			$this->set(compact('territories','markets','doctorQualifications','doctorTypes','offices','districts'));

		}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
public function admin_view($id = null) {
	$this->set('page_title','Doctor Details');
	if (!$this->Doctor->exists($id)) {
		throw new NotFoundException(__('Invalid doctor'));
	}
	$options = array('conditions' => array('Doctor.' . $this->Doctor->primaryKey => $id));
	$this->set('doctor', $this->Doctor->find('first', $options));
}

/**
 * admin_add method
 *
 * @return void
 */
public function admin_add() {

	$this->set('page_title','Add Doctor');
	if ($this->request->is('post')) {
		$this->Doctor->create();
		$this->request->data['Doctor']['created_at'] = $this->current_datetime();
		$this->request->data['Doctor']['created_by'] = $this->UserAuth->getUserId();			
		$this->request->data['Doctor']['updated_at'] = $this->current_datetime();
		$this->request->data['Doctor']['updated_by'] = $this->UserAuth->getUserId();			
		if ($this->Doctor->save($this->request->data)) {
			$this->Session->setFlash(__('The doctor has been saved'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash(__('The doctor could not be saved. Please, try again.'), 'flash/error');
		}
	}

	$territory_id = isset($this->request->data['Doctor']['territory_id'])!='' ? $this->request->data['Doctor']['territory_id'] : 0;
	$market_id = isset($this->request->data['Doctor']['market_id'])!='' ? $this->request->data['Doctor']['market_id'] : 0;

	$doctorQualifications = $this->Doctor->DoctorQualification->find('list');
	$doctorTypes = $this->Doctor->DoctorType->find('list');
	$territories = $this->Doctor->Territory->find('list',array(
		'conditions' => array('Territory.office_id'=>$this->UserAuth->getOfficeId()),
		'order' => array('Territory.name'=>'asc')
		));
	$markets = $this->Doctor->Market->find('list',array(
		'conditions' => array('Market.territory_id'=> $territory_id),
		'order' => array('Market.name'=>'asc')
		));
	$outlets = $this->Doctor->Outlet->find('list',array(
		'conditions' => array('Outlet.market_id'=> $market_id),
		'order' => array('Outlet.name'=>'asc')
		));
	$this->set(compact('doctorQualifications', 'doctorTypes', 'territories', 'markets', 'outlets'));
}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
public function admin_edit($id = null) {
	$this->set('page_title','Edit Doctor');
	$this->Doctor->id = $id;
	if (!$this->Doctor->exists($id)) {
		throw new NotFoundException(__('Invalid doctor'));
	}
	if ($this->request->is('post') || $this->request->is('put')) {
		$this->request->data['Doctor']['updated_by'] = $this->UserAuth->getUserId();
		$this->request->data['Doctor']['updated_at'] = $this->current_datetime();
		if ($this->Doctor->save($this->request->data)) {
			$this->Session->setFlash(__('The doctor has been saved'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash(__('The doctor could not be saved. Please, try again.'), 'flash/error');
		}
	} else {
		$options = array('conditions' => array('Doctor.' . $this->Doctor->primaryKey => $id));
		$this->request->data = $this->Doctor->find('first', $options);
	}

	$doctorQualifications = $this->Doctor->DoctorQualification->find('list');
	$doctorTypes = $this->Doctor->DoctorType->find('list');
	$user_group_id = $this->UserAuth->getUserGroupId();
	if($user_group_id==1008)
	{
		$territories = $this->Doctor->Territory->find('list',array(
			'joins'=>array(
				array(
					'table'=>'user_territory_lists',
					'alias'=>'UserTerritoryList',
					'type'=>'Inner',
					'conditions'=>'Territory.id=UserTerritoryList.territory_id'
					)
				),
			'conditions' =>array('UserTerritoryList.user_id'=>$this->UserAuth->getUserId()),
			'order' => array('Territory.name'=>'asc')
			));
	}
	else
	{
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		$territory_conditions=array();
		if($office_parent_id !=0)
		{
			$this->LoadModel('Office');
			$office_type = $this->Office->find('first',array(
				'conditions' => array('Office.id' => $this->UserAuth->getOfficeId()),
				'recursive'=>-1
				));
			$office_type_id = $office_type['Office']['office_type_id'];
			if($office_type_id==3)
			{
				$territory_conditions=array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
			}
			elseif($office_type_id==2)
			{
				$territory_conditions=array('Territory.office_id' => $this->UserAuth->getOfficeId());
			}
		}

		$territories = $this->Doctor->Territory->find('list',array(
			'conditions' =>$territory_conditions,
			'order' => array('Territory.name'=>'asc')
			));
	}
	
	$markets = $this->Doctor->Market->find('list',array(
		'conditions' => array('Market.territory_id'=> $this->request->data['Doctor']['territory_id']),
		'order' => array('Market.name'=>'asc')
		));
	$outlets = $this->Doctor->Outlet->find('list',array(
		'conditions' => array('Outlet.market_id'=> $this->request->data['Doctor']['market_id']),
		'order' => array('Outlet.name'=>'asc')
		));
	$this->set(compact('doctorQualifications', 'doctorTypes', 'territories', 'markets', 'outlets'));
}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
public function admin_delete($id = null) {
	$this->loadModel('DoctorVisit');
	if (!$this->request->is('post')) {
		throw new MethodNotAllowedException();
	}
	$this->Doctor->id = $id;
	$doctor_visit_history=$this->DoctorVisit->find('first',array(
		'conditions'=>array('DoctorVisit.doctor_id'=>$id),
		'recursive'=>-1
		));
	if(!empty($doctor_visit_history))
	{
		$this->Session->setFlash(__('Doctor Can not be deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	if (!$this->Doctor->exists()) {
		throw new NotFoundException(__('Invalid doctor'));
	}
	if ($this->Doctor->delete()) {
		$this->Session->setFlash(__('Doctor deleted'), 'flash/success');
		$this->redirect(array('action' => 'index'));
	}
	
}


public function admin_get_market()
{				
	$this->loadModel('Market');
	$this->loadModel('Territory');
	$territory_id = $this->request->data['territory_id'];

	$terrtory_id_parent_id = $this->Territory->find('first', array(
		'fields' => array('Territory.parent_id','Territory.id'),
		'conditions' => array('Territory.id' => $territory_id),
		'recursive' => -1
	));

	if($terrtory_id_parent_id['Territory']['parent_id']>0){
		
		$territory_id = $terrtory_id_parent_id['Territory']['parent_id'];

	}

	$rs = array(array('id' => '', 'name' => '---- Select Market -----'));
	$market_list = $this->Market->find('all', array(
		'fields' => array('Market.id as id','Market.name as name'),
		'conditions' => array(
			'Market.territory_id' => $territory_id				
			),
		'order' => array('Market.name'=>'asc'),
		'recursive' => -1
		));
	$data_array = Set::extract($market_list, '{n}.0');
	if(!empty($market_list)){
		echo json_encode(array_merge($rs,$data_array));
	}else{
		echo json_encode($rs);
	} 
	$this->autoRender = false;
}


public function admin_get_outlet()
{				
	$this->loadModel('Outlet');
	$rs = array(array('id' => '', 'name' => '---- Select Outlet -----'));
	$market_id = $this->request->data['market_id'];
	$outlet_list = $this->Outlet->find('all', array(
		'fields' => array('Outlet.id as id','Outlet.name as name'),
		'conditions' => array(
			'Outlet.market_id' => $market_id,				
			'Outlet.is_active' => 1,				
			),
		'order' => array('Outlet.name'=>'asc'),
		'recursive' => -1
		));
	$data_array = Set::extract($outlet_list, '{n}.0');
	if(!empty($outlet_list)){
		echo json_encode(array_merge($rs,$data_array));
	}else{
		echo json_encode($rs);
	} 
	$this->autoRender = false;
}
public function admin_change_status($id)
{
	
	$options = array('conditions' => array('Doctor.id'=>$id));
	$doctor=$this->Doctor->find('first',array(
		'conditions' => array('Doctor.id'=>$id),
		'recursive'=>-1
		));
	/*pr($doctor);
	echo $this->Doctor->getLastQuery();*/
	$data['id']=$id;
	$data['is_active']=($doctor['Doctor']['is_active']==1?0:1);
	$data['updated_at'] = $this->current_datetime();
	$msg=$data['is_active']==1?'Active':'Deactive';
	// pr($data);exit;
	if ($this->Doctor->save($data)) {
		$this->Session->setFlash(__('<b>'.$doctor['Doctor']['name'].'</b> is '.$msg), 'flash/success');
		$this->redirect(array('action' => 'index'));
	}
	$this->Session->setFlash(__('Doctor Status  was not Changed'), 'flash/error');
	$this->redirect(array('action' => 'index'));
}

}
