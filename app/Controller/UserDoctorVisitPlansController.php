<?php
App::uses('AppController', 'Controller');
/**
 * UserDoctorVisitPlans Controller
 *
 * @property UserDoctorVisitPlan $UserDoctorVisitPlan
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class UserDoctorVisitPlansController extends AppController {

/**
 * Components
 *
 * @var array
 */
public $components = array('Paginator', 'Session','Filter.Filter');

/**
 * admin_index method
 *
 * @return void
 */
public function admin_index() {
	$this->set('page_title','User doctor visit plan List');
	$this->UserDoctorVisitPlan->recursive = 0;
	$this->UserDoctorVisitPlan->virtualFields=array(
		'total'=>'total'
		);
	$this->paginate = array(
		'joins'=>array(
			array(
				'alias' => 'SalesPerson',
				'table' => 'sales_people',
				'type' => 'INNER',
				'conditions' => 'User.sales_person_id = SalesPerson.id'
				),
			array(
				'alias' => 'UserDoctorVisitPlanList',
				'table' => 'user_doctor_visit_plan_lists',
				'type' => 'INNER',
				'conditions' => 'UserDoctorVisitPlan.id = UserDoctorVisitPlanList.user_doctor_visit_plan_id'
				)
			),
		'fields'=>array('UserDoctorVisitPlan.id','UserDoctorVisitPlan.fiscal_year_id','UserDoctorVisitPlan.user_id','User.name','User.id','SalesPerson.name','FiscalYear.year_code','FiscalYear.year_code','FiscalYear.id','COUNT(UserDoctorVisitPlanList.id) as total'),
		'group'=>array('UserDoctorVisitPlan.id','UserDoctorVisitPlan.fiscal_year_id','UserDoctorVisitPlan.user_id','User.name','User.id','SalesPerson.name','FiscalYear.year_code','FiscalYear.id'),
		'order' => array('UserDoctorVisitPlan.id' => 'DESC')
		);
	$this->set('userDoctorVisitPlans', $this->paginate());
}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
public function admin_view($id = null) {
	$this->LoadModel('UserDoctorVisitPlanList');
	$this->set('page_title','User doctor visit plan Details');
	if (!$this->UserDoctorVisitPlan->exists($id)) {
		throw new NotFoundException(__('Invalid user doctor visit plan'));
	}
		/*$options = array('conditions' => array('UserDoctorVisitPlan.' . $this->UserDoctorVisitPlan->primaryKey => $id));
		$this->set('userDoctorVisitPlan', $this->UserDoctorVisitPlan->find('first', $options));*/
		$this->loadModel('Territory');		
		
		$conditions = array(
			'UserDoctorVisitPlanList.user_doctor_visit_plan_id'=>$id
			);
		$this->paginate = array(
			'conditions' => $conditions,
			'joins'=>array(
				array(
					'table'=>'outlets',
					'alias'=>'Outlet',
					'type'=>'left',
					'conditions'=>'Outlet.id=Doctor.outlet_id'
					)
				),
			'fields'=>array('Territory.*','UserDoctorVisitPlanList.*','Market.*','Doctor.*','Outlet.name'),
			'recursive' => 0
			);
		/*echo $this->UserDoctorVisitPlanList->getLastQuery();
		pr($this->paginate('UserDoctorVisitPlanList'));
		echo $this->UserDoctorVisitPlanList->getLastQuery();
		exit;*/

		$this->set('UserDoctorVisitPlanLists', $this->paginate('UserDoctorVisitPlanList'));
		
		$territory_id = isset($this->request->data['UserDoctorVisitPlanList']['territory_id'])!='' ? $this->request->data['UserDoctorVisitPlanList']['territory_id'] : 0;
		$market_id = isset($this->request->data['UserDoctorVisitPlanList']['market_id'])!='' ? $this->request->data['UserDoctorVisitPlanList']['market_id'] : 0;
		
		$visit_status = isset($this->request->data['UserDoctorVisitPlanList']['visit_status'])!='' ? $this->request->data['UserDoctorVisitPlanList']['visit_status'] : 0;
		$date = isset($this->request->data['UserDoctorVisitPlanList']['date'])!='' ? $this->request->data['UserDoctorVisitPlanList']['date'] : 0;
		
		$this->loadModel('UserTerritoryList');
		$visit_plan=$this->UserDoctorVisitPlan->find('first',array('conditions'=>array('id'=>$id),'recursive'=>-1));      
		$user_id = $visit_plan['UserDoctorVisitPlan']['user_id'];

		$territory = $this->UserTerritoryList->find('all', array(
			'conditions' => array('UserTerritoryList.user_id' => $user_id),
			//'order' => array('Product.order' => 'ASC'),
			'recursive' => 1
			));
		// $territories=array_combine(array_column(array_column($territory,'Territory'),'id'),array_column(array_column($territory,'Territory'),'name'));
		$this->set(compact('territories', 'markets'));
	}

/**
 * admin_add method
 *
 * @return void
 */
public function admin_add() {
	$this->set('page_title','Add User doctor visit plan');
	$this->LoadModel('UserDoctorVisitPlanList');
	if ($this->request->is('post')) {
		$this->UserDoctorVisitPlan->create();
		$this->request->data['UserDoctorVisitPlan']['created_at'] = $this->current_datetime();
		$this->request->data['UserDoctorVisitPlan']['created_by'] = $this->UserAuth->getUserId();			
		if ($this->UserDoctorVisitPlan->save($this->request->data)) {
			$doctor_array = array();
			foreach($this->request->data['selected_doctor'] as $val)
			{
				$this->LoadModel('Doctor');
				$doctor=$this->Doctor->find('first',array('conditions'=>array('Doctor.id'=>$val),'recursive'=>-1));
				$data['user_doctor_visit_plan_id'] = $this->UserDoctorVisitPlan->id;
				$data['territory_id'] =$doctor['Doctor']['territory_id'];
				$data['market_id'] = $doctor['Doctor']['market_id'];
				$data['doctor_id'] = $val;
				$data['is_out_of_plan'] = 0;
				$data['visit_status'] = 'Pending';
				$data['created_at'] = $this->current_datetime();
				$data['created_by'] = $this->UserAuth->getUserId();			
				$data['updated_at'] = $this->current_datetime();
				$doctor_array[] = $data;
			}
			$this->UserDoctorVisitPlanList->saveAll($doctor_array);
			$this->Session->setFlash(__('The user doctor visit plan has been saved'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash(__('The user doctor visit plan could not be saved. Please, try again.'), 'flash/error');
		}
	}
	$fiscalYears = $this->UserDoctorVisitPlan->FiscalYear->find('all',array('recursive'=>-1,'fields'=>array('id','year_code')));
	$fiscalYears=array_combine(array_map(function($elem){return $elem['id'];},array_map(function($elem){return $elem['FiscalYear'];},$fiscalYears)), array_map(function($elem){return $elem['year_code'];},array_map(function($elem){return $elem['FiscalYear'];},$fiscalYears)));
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
				'SalesPerson.office_id' => $this->UserAuth->getOfficeId(), //change here for SPO
				);	
	}
	$user_group_id=$this->UserAuth->getUserGroupId();
	if($user_group_id==1008)
	{
		$user_conditions = array(
			'User.id'=>$this->UserAuth->getUserId(),
			'active' => 1, 
				'user_group_id' => 1008, //change here for SPO
				'SalesPerson.office_id' => $this->UserAuth->getOfficeId(), //change here for SPO
				);	
	}
	$users = $this->User->find('all', 
		array(
			'conditions' => $user_conditions,
			'joins' => array(
				array(
					'alias' => 'SalesPerson',
					'table' => 'sales_people',
					'type' => 'INNER',
					'conditions' => 'User.sales_person_id = SalesPerson.id'
					)
				),
			'fields' => array('User.id','SalesPerson.name','User.username'),
			'order'=>array('User.username'=>'asc')
			)
		);
	$users=array_combine(array_map(function($element){return $element['id'];},array_map(function($element){return $element['User'];},$users)),array_map(function($element){return $element['name'];},array_map(function($element){return $element['SalesPerson'];},$users)));
	$this->set(compact('fiscalYears', 'users'));
}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
public function admin_edit($id = null) {
	$this->set('page_title','Edit User doctor visit plan');
	$this->UserDoctorVisitPlan->id = $id;
	$this->LoadModel('UserDoctorVisitPlanList');
	if (!$this->UserDoctorVisitPlan->exists($id)) {
		throw new NotFoundException(__('Invalid user doctor visit plan'));
	}
	if ($this->request->is('post') || $this->request->is('put')) {
		$this->request->data['UserDoctorVisitPlan']['updated_by'] = $this->UserAuth->getUserId();
		$this->request->data['UserDoctorVisitPlan']['updated_at'] = $this->current_datetime();
		if ($this->UserDoctorVisitPlan->save($this->request->data)) {
			$this->UserDoctorVisitPlanList->deleteAll(array('UserDoctorVisitPlanList.user_doctor_visit_plan_id'=>$id));
			$doctor_array = array();
			foreach($this->request->data['selected_doctor'] as $val)
			{
				$this->LoadModel('Doctor');
				$doctor=$this->Doctor->find('first',array('conditions'=>array('Doctor.id'=>$val),'recursive'=>-1));
				$data['user_doctor_visit_plan_id'] = $this->UserDoctorVisitPlan->id;
				$data['territory_id'] =$doctor['Doctor']['territory_id'];
				$data['market_id'] = $doctor['Doctor']['market_id'];
				$data['doctor_id'] = $val;
				$data['is_out_of_plan'] = 0;
				$data['visit_status'] = 'Pending';
				$data['created_at'] = $this->current_datetime();
				$data['created_by'] = $this->UserAuth->getUserId();			
				$data['updated_at'] = $this->current_datetime();
				$doctor_array[] = $data;
			}
			$this->UserDoctorVisitPlanList->saveAll($doctor_array);
			$this->Session->setFlash(__('The user doctor visit plan has been saved'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash(__('The user doctor visit plan could not be saved. Please, try again.'), 'flash/error');
		}
	} else {
		$options = array('conditions' => array('UserDoctorVisitPlan.' . $this->UserDoctorVisitPlan->primaryKey => $id));
		$this->request->data = $this->UserDoctorVisitPlan->find('first', $options);
		$list_doctor = $this->UserDoctorVisitPlanList->find('all',array(
			'joins'=>array(
				array(
					'table'=>'doctors',
					'alias'=>'Doctor',
					'type'=>'Inner',
					'conditions'=>'Doctor.id=UserDoctorVisitPlanList.doctor_id'
					)
				),
			'conditions'=>array('UserDoctorVisitPlanList.user_doctor_visit_plan_id'=>$id),
			'fields'=>array('UserDoctorVisitPlanList.*','Doctor.name','Doctor.id'),
			'recursive'=>-1
			)
		);
		$list_doctor=array_combine(
			array_map(
				function($elem){
					return $elem['id'];
				},
				array_map(
					function($elem){
						return $elem['Doctor'];
					},$list_doctor
					)
				),
			array_map(
				function($elem){
					return $elem['name'];
				},
				array_map(
					function($elem){
						return $elem['Doctor'];
					},$list_doctor
					)
				)
			);

	}
	$fiscalYears = $this->UserDoctorVisitPlan->FiscalYear->find('all',array('recursive'=>-1,'fields'=>array('id','year_code')));
		// $fiscalYears=array_combine(array_column(array_column($fiscalYears,'FiscalYear'),'id'), array_column(array_column($fiscalYears,'FiscalYear'),'year_code'));
	$fiscalYears=array_combine(array_map(function($elem){return $elem['id'];},array_map(function($elem){return $elem['FiscalYear'];},$fiscalYears)), array_map(function($elem){return $elem['year_code'];},array_map(function($elem){return $elem['FiscalYear'];},$fiscalYears)));
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
				'SalesPerson.office_id' => $this->UserAuth->getOfficeId(), //change here for SPO
				);	
	}
	$user_group_id=$this->UserAuth->getUserGroupId();
	if($user_group_id==1008)
	{
		$user_conditions = array(
			'User.id'=>$this->UserAuth->getUserId(),
			'active' => 1, 
				'user_group_id' => 1008, //change here for SPO
				'SalesPerson.office_id' => $this->UserAuth->getOfficeId(), //change here for SPO
				);	
	}

	$users = $this->User->find('all', 
		array(
			'conditions' => $user_conditions,
			'joins' => array(
				array(
					'alias' => 'SalesPerson',
					'table' => 'sales_people',
					'type' => 'INNER',
					'conditions' => 'User.sales_person_id = SalesPerson.id'
					)
				),
			'fields' => array('User.id','SalesPerson.name','User.username'),
			'order'=>array('User.username'=>'asc')
			)
		);
	$this->loadModel('UserTerritoryList');

	$user_id = $this->request->data['UserDoctorVisitPlan']['user_id'];


	$territory = $this->UserTerritoryList->find('all', array(
		'conditions' => array('UserTerritoryList.user_id' => $user_id),
			//'order' => array('Product.order' => 'ASC'),
		'recursive' => 1
		));
	$territories=array_combine(
					array_map(
						function($elem){
							return $elem['id'];
						},
						array_map(function($elem){
							return $elem['Territory'];
						},$territory
						)
					),
					array_map(
						function($elem){
							return $elem['name'];
						},
						array_map(function($elem){
							return $elem['Territory'];
						},$territory
						)
					)
				);

		// pr($territories);exit;
		// $users=array_combine(array_column(array_column($users,'User'),'id'),array_column(array_column($users,'SalesPerson'),'name'));
	$users=array_combine(array_map(function($element){return $element['id'];},array_map(function($element){return $element['User'];},$users)),array_map(function($element){return $element['name'];},array_map(function($element){return $element['SalesPerson'];},$users)));
	$this->set(compact('fiscalYears', 'users','list_doctor','territories'));
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
	$this->LoadModel('UserDoctorVisitPlanList');
	if (!$this->request->is('post')) {
		throw new MethodNotAllowedException();
	}
	$this->UserDoctorVisitPlan->id = $id;
	if (!$this->UserDoctorVisitPlan->exists()) {
		throw new NotFoundException(__('Invalid user doctor visit plan'));
	}
	if ($this->UserDoctorVisitPlan->delete()) {
		$this->UserDoctorVisitPlanList->deleteAll(array('UserDoctorVisitPlanList.user_doctor_visit_plan_id'=>$id));
		$this->Session->setFlash(__('User doctor visit plan deleted'), 'flash/success');
		$this->redirect(array('action' => 'index'));
	}
	$this->Session->setFlash(__('User doctor visit plan was not deleted'), 'flash/error');
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
	$selected_doctor=json_decode($this->request->data['selected_doctor']);
	if($market_id && $market_id!='null'){
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

		echo $form->input('doctor_id', array('label'=>false,'class'=>'checkbox doctor_id_click', 'multiple' => 'checkbox', 'options' => $doctor_list, 'selected' => $selected_doctor, 'required'=>true));
	}
	else
	{
		echo '';
	}

	$this->autoRender = false;		
}

}
