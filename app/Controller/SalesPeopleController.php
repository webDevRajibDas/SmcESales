<?php
App::uses('AppController', 'Controller');
/**
 * SalesPeople Controller
 *
 * @property SalesPerson $SalesPerson
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class SalesPeopleController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session','RequestHandler', 'Filter.Filter');
	public $uses = array('SalesPerson');
	
	

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','Sales Person List');
		$designations = $this->SalesPerson->Designation->find('list',array('fields'=>array('Designation.id','Designation.designation_name')));
		$parentSalesPeople = $this->SalesPerson->ParentSalesPerson->find('list',array('fields'=>array('ParentSalesPerson.id','ParentSalesPerson.name')));
		$offices = $this->SalesPerson->Office->find('list',array('fields'=>array('Office.id','Office.office_name')));
		$this->set(compact('designations', 'parentSalesPeople', 'offices'));
		
		$this->SalesPerson->recursive = 0;
		$this->paginate = array(			
			//'order' => array('SalesPerson.id' => 'DESC')
		);
		$this->set('salesPeople', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->SalesPerson->exists($id)) {
			throw new NotFoundException(__('Invalid sales person'));
		}
		$options = array('conditions' => array('SalesPerson.' . $this->SalesPerson->primaryKey => $id));
		$this->set('salesPerson', $this->SalesPerson->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Sales Person');
		if ($this->request->is('post')) {
			$this->SalesPerson->create();
			$this->request->data['SalesPerson']['parent_id'] = ($this->request->data['SalesPerson']['parent_id']!='' ? $this->request->data['SalesPerson']['parent_id'] : 0);
			$this->request->data['SalesPerson']['created_at'] = $this->current_datetime();
			$this->request->data['SalesPerson']['updated_at'] = $this->current_datetime();
			$this->request->data['SalesPerson']['created_by'] = $this->UserAuth->getUserId();
			if ($this->SalesPerson->save($this->request->data)) {
				$this->Session->setFlash(__('The sales person has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The sales person could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$designations = $this->SalesPerson->Designation->find('list',array('fields'=>array('Designation.id','Designation.designation_name')));
		$parentSalesPeople = $this->SalesPerson->ParentSalesPerson->find('list',array('fields'=>array('ParentSalesPerson.id','ParentSalesPerson.name')));
		$offices = $this->SalesPerson->Office->find('list',array('fields'=>array('Office.id','Office.office_name')));
		$this->set(compact('designations', 'parentSalesPeople', 'offices'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->loadModel('Territory');
        $this->set('page_title','Edit Sales Person');
		$this->SalesPerson->id = $id;
		if (!$this->SalesPerson->exists($id)) {
			throw new NotFoundException(__('Invalid sales person'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['SalesPerson']['parent_id'] = ($this->request->data['SalesPerson']['parent_id']!='' ? $this->request->data['SalesPerson']['parent_id'] : 0);
			$this->request->data['SalesPerson']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['SalesPerson']['updated_at'] = $this->current_datetime();
			if ($this->SalesPerson->save($this->request->data)) {
				$this->Session->setFlash(__('The sales person has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The sales person could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$this->SalesPerson->recursive = 0;
			$options = array('conditions' => array('SalesPerson.' . $this->SalesPerson->primaryKey => $id));
			$this->request->data = $this->SalesPerson->find('first', $options);
		}
		$designations = $this->SalesPerson->Designation->find('list',array('fields'=>array('Designation.id','Designation.designation_name')));
		$parentSalesPeople = $this->SalesPerson->ParentSalesPerson->find('list',array('fields'=>array('ParentSalesPerson.id','ParentSalesPerson.name')));
		$offices = $this->SalesPerson->Office->find('list',array('fields'=>array('Office.id','Office.office_name')));
		
		$territory_list = $this->Territory->find('list',array(
			'conditions' => array('Territory.office_id'=> $this->request->data['Office']['id'])
		));
		$this->set(compact('designations', 'parentSalesPeople', 'offices','territory_list'));
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
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->SalesPerson->id = $id;
		if (!$this->SalesPerson->exists()) {
			throw new NotFoundException(__('Invalid sales person'));
		}
		if ($this->SalesPerson->delete()) {
			$this->Session->setFlash(__('Sales person deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Sales person was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	
	
	public function get_territory_list_new(){
		$this->loadModel('Territory');
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$office_id = $this->request->data['office_id'];
		if($office_id)
		{

			/***Show Except Child Territory ***/
			
			$child_territory_id = $this->Territory->find('list',array(
                'conditions'=> array(
                    'parent_id !=' => 0,
                ),
                'fields'=>array('Territory.id','Territory.name'),
            ));
			
			$territory = $this->Territory->find('all', array(
				'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
				'conditions' => array('Territory.office_id' => $office_id,'NOT'=>array('Territory.id'=>array_keys($child_territory_id))),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => 0
			));

			// echo $this->Territory->getLastquery();exit;
			
    	}
		
		//pr($territory);
		
		//$data_array = Set::extract($territory, '{n}.Territory');
		
		$data_array = array();
		
		foreach($territory as $key => $value)
		{
			$data_array[] = array(
				'id' => $value['Territory']['id'],
				'name' => $value['Territory']['name'].' ('.$value['SalesPerson']['name'].')',
			);
		}
		
		//pr($data_array);
		
		if(!empty($territory)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}

	public function get_territory_list(){

		$this->loadModel('Territory');
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$office_id = $this->request->data['office_id'];
		if($office_id) {
			/***Show Except Parent(Who has Child) Territory ***/
			$child_territory_parent_id = $this->Territory->find('list',array(
				'conditions'=> array(
					'parent_id !=' => 0,
					
				),
				'fields'=>array('Territory.parent_id','Territory.name'),
			));
			$territory = $this->Territory->find('all', array(
				'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
				'conditions' => array('Territory.office_id' => $office_id,'NOT'=>array('Territory.id'=>array_keys($child_territory_parent_id))),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => 0
			));
			// echo $this->Territory->getLastquery();exit;
    	}
		//pr($territory);
		//$data_array = Set::extract($territory, '{n}.Territory');
		$data_array = array();
		foreach($territory as $key => $value) {
			$data_array[] = array(
				'id' => $value['Territory']['id'],
				'name' => $value['Territory']['name'].' ('.$value['SalesPerson']['name'].')',
			);
		}
		//pr($data_array);
		if(!empty($territory)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}

	public function get_outlet_list_with_distributor_name(){
        $office_id = $this->request->data['office_id'];
        $rs = array(array('id' => '', 'name' => '---- Select -----'));
        $this->loadModel('DistDistributor');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        if($user_group_id == 1029 || $user_group_id == 1028){
            if($user_group_id == 1028){
                $dist_ae_info = $this->DistAreaExecutive->find('first',array(
                    'conditions'=>array('DistAreaExecutive.user_id'=>$user_id, 'DistAreaExecutive.is_active'=>1),
                    'recursive'=> -1,
                ));
                $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
                $dist_tso_info = $this->DistTso->find('list',array(
                    'conditions'=>array('dist_area_executive_id'=>$dist_ae_id),
                    'fields'=> array('DistTso.id','DistTso.dist_area_executive_id'),
                ));
                
                $dist_tso_id = array_keys($dist_tso_info);
            }
            else{
                $dist_tso_info = $this->DistTso->find('first',array(
                    'conditions'=>array('DistTso.user_id'=>$user_id, 'DistTso.is_active'=>1),
                    'recursive'=> -1,
                ));
                $dist_tso_id = $dist_tso_info['DistTso']['id'];
            }
           
            $tso_dist_list = $this->DistTsoMapping->find('list',array(
                'conditions'=> array(
                    'dist_tso_id' => $dist_tso_id,
                ),
                'fields'=>array('DistTsoMapping.dist_distributor_id','DistTsoMapping.dist_tso_id'),
            ));
           
           $conditions=array(
                'conditions' => array('DistDistributor.id' =>array_keys($tso_dist_list), 'DistDistributor.is_active' => 1),'order' => array('DistDistributor.name' => 'asc'),
            );
        }
        elseif($user_group_id == 1034){
            $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
            $this->loadModel('DistUserMapping');
            $distributor = $this->DistUserMapping->find('first',array(
                'conditions'=>array('DistUserMapping.sales_person_id'=>$sales_people_id),
            ));
            $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
            $conditions=array(
                'conditions' => array('DistDistributor.id' => $distributor_id, 'DistDistributor.is_active' => 1),'order' => array('DistDistributor.name' => 'asc'),
            );
        }
        else{
            $conditions=array(
                'conditions' => array('DistDistributor.office_id' => $office_id, 'DistDistributor.is_active' => 1),'order' => array('DistDistributor.name' => 'asc'),
            );
        }
        $distributor_info = $this->DistDistributor->find('all',$conditions);
        
        
        foreach ($distributor_info as $key => $value) {
        	if($value['DistOutletMap']['outlet_id'] != null){
        		$data_array[]= array(
		            'id'=>$value['DistOutletMap']['outlet_id'],
		            'name'=>$value['DistDistributor']['name'],
		        );
        	}
        }
        //pr($data_array);die();
        if(!empty($distributor_info)){
            echo json_encode(array_merge($rs,$data_array));
        }else{
            echo json_encode($rs);
        } 
       
        $this->autoRender = false;
    }
	
	public function get_territory_list_memo(){
		$this->loadModel('Territory');
		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		$office_id = $this->request->data['office_id'];
        
		/*$territory = $this->Territory->find('all', array(
			'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
			'conditions' => array('Territory.office_id' => $office_id),
			'order' => array('Territory.name' => 'asc'),
			'recursive' => 0
		));*/

		/***Show Except Parent(Who has Child) Territory ***/
				
		$child_territory_parent_id = $this->Territory->find('list',array(
			'conditions'=> array(
				'parent_id !=' => 0,
				
			),
			'fields'=>array('Territory.parent_id','Territory.name'),
			
		));
		
		$territory = $this->Territory->find('all', array(
			'conditions' => array('Territory.office_id' => $office_id, 'User.user_group_id !=' => 1008,'NOT'=>array('Territory.id'=>array_keys($child_territory_parent_id))),
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
		
		//pr($territory);
		
		//$data_array = Set::extract($territory, '{n}.Territory');
		
		$data_array = array();
		
		foreach($territory as $key => $value)
		{
			$data_array[] = array(
				'id' => $value['Territory']['id'],
				'name' => $value['Territory']['name'].' ('.$value['SalesPerson']['name'].')',
			);
		}
		
		//pr($data_array);
		
		if(!empty($territory)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}
	
	public function get_spo_territory_list(){
		$this->loadModel('Territory');
		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		$office_id = $this->request->data['office_id'];
        		
		$territory = $this->Territory->find('all', array(
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
		
		//pr($territory);
		
		//$data_array = Set::extract($territory, '{n}.Territory');
		
		$data_array = array();
		
		foreach($territory as $key => $value)
		{
			$data_array[] = array(
				'id' => $value['Territory']['id'],
				'name' => $value['Territory']['name'].' ('.$value['SalesPerson']['name'].')',
			);
		}
		
		//pr($data_array);
		
		if(!empty($territory)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}
	
	public function get_child_territory_list()
	{
		$this->loadModel('Territory');
		
		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		
		$spo_territory_id = $this->request->data['spo_territory_id'];
        
		if($spo_territory_id)
		{
			$territory_info = $this->Territory->find('first', array(
				'conditions' => array('Territory.id' => $spo_territory_id),
				'joins' => array(
					array(
						'alias' => 'User',
						'table' => 'users',
						'type' => 'INNER',
						'conditions' => 'SalesPerson.id = User.sales_person_id'
					)
				),
				'fields' => array('User.id'),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => 0
			));
			
			$user_id = $territory_info['User']['id'];
			
			
			$territory = $this->Territory->find('all', array(
				'conditions' => array('UT.user_id' => $user_id),
				'joins' => array(
					array(
						'alias' => 'UT',
						'table' => 'user_territory_lists',
						'type' => 'INNER',
						'conditions' => 'Territory.id = UT.territory_id'
					)
				),
				//'fields' => array('User.id'),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => 0
			));
			
			
			
			/*pr($territory);
			exit;*/
			
			//$data_array = Set::extract($territory, '{n}.Territory');
			
			$data_array = array();
			
			foreach($territory as $key => $value)
			{
				$data_array[] = array(
					'id' => $value['Territory']['id'],
					'name' => $value['Territory']['name'].' ('.$value['SalesPerson']['name'].')',
				);
			}
			//pr($data_array);
		}
		
		if($spo_territory_id && !empty($territory)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}
	
	public function get_so_list(){
		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		$territory_id = $this->request->data['territory_id'];
        $so = $this->SalesPerson->find('all', array(
			'fields' => array('SalesPerson.id', 'SalesPerson.name'),
			'conditions' => array('SalesPerson.territory_id' => $territory_id),
			'order' => array('SalesPerson.name' => 'asc'),
			'recursive' => -1
		));
		$data_array = Set::extract($so, '{n}.SalesPerson');
		if(!empty($so)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}
}
