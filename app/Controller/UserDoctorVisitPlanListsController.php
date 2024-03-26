<?php
App::uses('AppController', 'Controller');
/**
 * UserDoctorVisitPlanLists Controller
 *
 * @property UserDoctorVisitPlanList $UserDoctorVisitPlanList
 * @property PaginatorComponent $Paginator
 */
class UserDoctorVisitPlanListsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Filter.Filter');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
				
		$this->set('page_title','Doctor Visit Plan List');
		$this->loadModel('Office');
		$this->loadModel('Territory');		
		
		/*if($this->UserAuth->getOfficeParentId() !=0 ){
			$conditions = array('Territory.office_id' => $this->UserAuth->getOfficeId());
		}else{
			$conditions = array();
		}*/
		
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		
		if($office_parent_id == 0){
			$conditions = array(
				//'active' => 1, 
				//'User.user_group_id' => 1008 //change here for SPO
			);
		}else{
			$conditions = array(
				//'active' => 1, 
				//'User.user_group_id' => 1008, //change here for SPO
				'SalesPeople.office_id' => $this->UserAuth->getOfficeId(), //change here for SPO
			);	
		}
		
		$this->paginate = array('conditions' => $conditions,
									'joins' => array(
										array(
											'alias' => 'SalesPeople',
											'table' => 'sales_people',
											'type' => 'INNER',
											'conditions' => 'User.sales_person_id = SalesPeople.id'
										),
										
									)
								
								//'order' => array('UserDoctorVisitPlanList.id' => 'DESC'),
								//'recursive' => 0
								);
		
		//pr($this->paginate());
		//exit;
						
		$this->set('UserDoctorVisitPlanLists', $this->paginate());

		
		//for user
		$this->loadModel('User');
		
		if($office_parent_id == 0){
			$user_conditions = array(
				'active' => 1, 
				'user_group_id' => 1008 //change here for SPO
			);
		}else{
			$user_conditions = array(
				'active' => 1, 
				'user_group_id' => 1008, //change here for SPO
				'SalesPeople.office_id' => $this->UserAuth->getOfficeId(), //change here for SPO
			);	
		}
		
		$users = $this->User->find('list', 
			array(
				'conditions' => $user_conditions,
				'joins' => array(
					array(
						'alias' => 'SalesPeople',
						'table' => 'sales_people',
						'type' => 'INNER',
						'conditions' => 'User.sales_person_id = SalesPeople.id'
					)
				),
				'fields' => 'User.username',
				'order'=>array('User.username'=>'asc')
			)
		);
		
		
		
		$user_id = isset($this->request->data['UserDoctorVisitPlanList']['user_id'])!='' ? $this->request->data['UserDoctorVisitPlanList']['user_id'] : 0;
		$territory_id = isset($this->request->data['UserDoctorVisitPlanList']['territory_id'])!='' ? $this->request->data['UserDoctorVisitPlanList']['territory_id'] : 0;
		$market_id = isset($this->request->data['UserDoctorVisitPlanList']['market_id'])!='' ? $this->request->data['UserDoctorVisitPlanList']['market_id'] : 0;
		
		$visit_status = isset($this->request->data['UserDoctorVisitPlanList']['visit_status'])!='' ? $this->request->data['UserDoctorVisitPlanList']['visit_status'] : 0;
		$date = isset($this->request->data['UserDoctorVisitPlanList']['date'])!='' ? $this->request->data['UserDoctorVisitPlanList']['date'] : 0;
		
		
		//for territory
		$this->loadModel('Territory');
		$office_id = $this->UserAuth->getOfficeId();
		if($office_parent_id)
		{
			$territories = $this->Territory->find('list', array('conditions'=> array('office_id' => $office_id), 'order'=>array('name'=>'asc')));
		}else{
			$territories = $this->Territory->find('list', array('conditions'=> '', 'order'=>array('name'=>'asc')));
		}
		
		
		
		
		//pr($territories);
		//exit;
		
		if($territory_id){
			$markets = $this->UserDoctorVisitPlanList->Market->find('list',array(
				'conditions' => array('Market.territory_id'=> $territory_id),
				'order' => array('Market.name'=>'asc')
			));
		}else{
			$markets = array();
		}
		$this->set(compact('territories', 'markets', 'users'));

	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Visit plan list Details');
		if (!$this->UserDoctorVisitPlanList->exists($id)) {
			throw new NotFoundException(__('Invalid visit plan list'));
		}
		$options = array('conditions' => array('UserDoctorVisitPlanList.' . $this->UserDoctorVisitPlanList->primaryKey => $id));
		$this->set('visitPlanList', $this->UserDoctorVisitPlanList->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add new list');
		if ($this->request->is('post')) {
			//$this->UserDoctorVisitPlanList->create();
			if(!empty($this->request->data['doctor_id']))
			{
				$doctor_array = array();
				foreach($this->request->data['doctor_id'] as $val)
				{
					$data['user_id'] = $this->request->data['UserDoctorVisitPlanList']['user_id'];
					$data['territory_id'] = $this->request->data['UserDoctorVisitPlanList']['territory_id'];
					$data['market_id'] = $this->request->data['UserDoctorVisitPlanList']['market_id'];
					$data['doctor_id'] = $val;
					$data['visit_plan_date'] = DATE('Y-m-d',strtotime($this->request->data['UserDoctorVisitPlanList']['visit_plan_date']));
					$data['is_out_of_plan'] = 0;
					$data['visit_status'] = 'Pending';
					$data['created_at'] = $this->current_datetime();
					$data['created_by'] = $this->UserAuth->getUserId();			
					$data['updated_at'] = $this->current_datetime();
					$doctor_array[] = $data;					
				}
				
				$this->UserDoctorVisitPlanList->saveAll($doctor_array);
				$this->Session->setFlash(__('The visit plan has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
				
			}
			else
			{
				$this->Session->setFlash(__('Please select at least one Doctor'), 'flash/error');
				$this->redirect(array('action' => 'add'));
			}
			
			
		}
		
		
		//for user
		$this->loadModel('User');
		
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		
		if($office_parent_id == 0){
			$user_conditions = array(
				'active' => 1, 
				'user_group_id' => 1008 //change here for SPO
			);
		}else{
			$user_conditions = array(
				'active' => 1, 
				'user_group_id' => 1008, //change here for SPO
				'SalesPeople.office_id' => $this->UserAuth->getOfficeId(), //change here for SPO
			);	
		}
		
		$users = $this->User->find('list', 
			array(
				'conditions' => $user_conditions,
				'joins' => array(
					array(
						'alias' => 'SalesPeople',
						'table' => 'sales_people',
						'type' => 'INNER',
						'conditions' => 'User.sales_person_id = SalesPeople.id'
					)
				),
				'fields' => 'User.username',
				'order'=>array('User.username'=>'asc')
			)
		);
		
		//pr($users);
		
				
		//exit;
		
		
		$this->set(compact('users'));
	}
	
	
	
	

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
        $this->set('page_title','Edit Visit plan list');
		$this->UserDoctorVisitPlanList->id = $id;
		if (!$this->UserDoctorVisitPlanList->exists($id)) {
			throw new NotFoundException(__('Invalid visit plan list'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['UserDoctorVisitPlanList']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->UserDoctorVisitPlanList->save($this->request->data)) {
				$this->Session->setFlash(__('The visit plan list has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The visit plan list could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('UserDoctorVisitPlanList.' . $this->UserDoctorVisitPlanList->primaryKey => $id));
			$this->request->data = $this->UserDoctorVisitPlanList->find('first', $options);
		}
		$markets = $this->UserDoctorVisitPlanList->Market->find('list');
		$this->set(compact('markets'));
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
		$this->UserDoctorVisitPlanList->id = $id;
		if (!$this->UserDoctorVisitPlanList->exists()) {
			throw new NotFoundException(__('Invalid visit plan list'));
		}
		if ($this->UserDoctorVisitPlanList->delete($id)) {
			$this->Session->setFlash(__('Visit plan list deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Visit plan list was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	
	
	 public function get_spo_territory_list()
	 {
		$this->loadModel('UserTerritoryList');
              
	    $user_id = $this->request->data['user_id'];
        
		$rs = array(array('id' => '', 'name' => '---- Select -----'));
  
		$territory = $this->UserTerritoryList->find('all', array(
			'conditions' => array('UserTerritoryList.user_id' => $user_id),
			//'order' => array('Product.order' => 'ASC'),
			'recursive' => 1
		));
		
		//pr($territory);
		
		$data_array = Set::extract($territory, '{n}.Territory');
		
		if(!empty($territory))
		{
			echo json_encode(array_merge($rs, $data_array));
		}
		else
		{
			echo json_encode($rs);
		} 
		
		
        $this->autoRender = false;
    }
	
	
	public function get_market_list()
	 {
		$this->loadModel('Market');
              
	    $territory_id = $this->request->data['territory_id'];
		
		$rs = array(array('id' => '', 'name' => '---- Select -----'));
   
		$markets = $this->Market->find('all', array(
			'conditions' => array('Market.territory_id' => $territory_id),
			//'order' => array('Product.order' => 'ASC'),
			'recursive' => 1
		));
		
		//pr($markets);
		
		$data_array = Set::extract($markets, '{n}.Market');
		
		if(!empty($markets))
		{
			echo json_encode(array_merge($rs, $data_array));
		}
		else
		{
			echo json_encode($rs);
		} 
		
		
        $this->autoRender = false;
    }
	
	
	public function admin_get_doctor_list() 
	{		
		$view = new View($this);
        $form = $view->loadHelper('Form');	
		$this->loadModel('Doctor');
		
		$territory_id = $this->request->data['territory_id'];
		$market_id = $this->request->data['market_id'];
		
		
		if($market_id){
			$conditions = array('Doctor.territory_id' => $territory_id, 'Doctor.market_id' => $market_id);
		}else{
			$conditions = array('Doctor.territory_id' => $territory_id);
		}
		
		
		if($territory_id !='')
		{	
			$doctor_list = $this->Doctor->find('list',array(
				'conditions' => $conditions, 	
				'order' => array('Doctor.name'=>'ASC'),
				'recursive' => -1
			));
				
			echo $form->input('doctor_id', array('label'=>false, 'multiple' => 'checkbox', 'options' => $doctor_list, 'required'=>true));
		}
		else
		{
			echo '';
		}
		
		$this->autoRender = false;		
	}
	
}
