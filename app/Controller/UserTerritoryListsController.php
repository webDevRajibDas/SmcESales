<?php
App::uses('AppController', 'Controller');
/**
 * UserTerritoryLists Controller
 *
 * @property UserTerritoryList $UserTerritoryList
 * @property PaginatorComponent $Paginator
 */
class UserTerritoryListsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Filter.Filter');
	//public $uses = array('UserTerritoryList');
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() 
	{
		$this->set('page_title', 'User to Territory List');
		
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		
		if($office_parent_id == 0){
			$conditions = array();
		}else{
			$conditions = array(
				//'active' => 1, 
				//'User.user_group_id' => 1008, //change here for SPO
				'UserTerritoryList.office_id' => $this->UserAuth->getOfficeId(), //change here for SPO
			);	
		}
		
		
		
		
		
		$this->paginate = array(
			'conditions' => $conditions,
			'joins' => array(
						array(
							'alias' => 'User',
							'table' => 'users',
							'type' => 'INNER',
							'conditions' => 'UserTerritoryList.user_id = User.id'
						),
						array(
							'alias' => 'SalesPeople',
							'table' => 'sales_people',
							'type' => 'INNER',
							'conditions' => 'User.sales_person_id = SalesPeople.id'
						)
					),
			'recursive' => -1,
			'fields' => array('user_id', 'UserTerritoryList.office_id', 'SalesPeople.name'),
			'group' => array('user_id', 'UserTerritoryList.office_id', 'SalesPeople.name'),
			//'limit' => 4,
			//'order' => array('id' => 'desc')
		);	
		
		//pr($this->paginate());
		//exit;
		
		$this->set('UserTerritoryLists', $this->paginate());
		
		
		
		
		$office_id = (isset($this->request->data['UserTerritoryList']['office_id']) ? $this->request->data['UserTerritoryList']['office_id'] : 0);
		$user_id = (isset($this->request->data['UserTerritoryList']['user_id']) ? $this->request->data['UserTerritoryList']['user_id'] : 0);
		$territory_id = (isset($this->request->data['UserTerritoryList']['territory_id']) ? $this->request->data['UserTerritoryList']['territory_id'] : 0);
		
		$office_parent_id = $this->UserAuth->getOfficeParentId();
	
		/*if($office_parent_id == 0){
			$office_conditions = array();
		}else{
			$office_conditions = array('id' => $this->UserAuth->getOfficeId());
		}*/
		
		if($office_parent_id == 0){
			$office_conditions = array(
			'office_type_id' => 2,
			"NOT" => array( "id" => array(30, 31, 37))
			);
		}else{
			$office_conditions = array(
			'office_type_id' => 2,
			"NOT" => array( "id" => array(30, 31, 37)), 
			'id' => $this->UserAuth->getOfficeId());
		}
		
		$this->loadModel('Office');
		$offices = $this->Office->find('list',array('conditions'=> $office_conditions, 'order'=>array('office_name'=>'asc')));
		
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
				//'fields' => 'User.username',
				'fields' => 'SalesPeople.name',
				'order'=>array('User.username'=>'asc'),
				
			)
		);
		
		//for territory
		$this->loadModel('Territory');
		if($office_id)
		{
			/***Show Except Child Territory ***/
			
			$child_territory_id = $this->Territory->find('list',array(
				'conditions'=> array(
					'parent_id !=' => 0,
				),
				'fields'=>array('Territory.id','Territory.name'),
			));

			$territories = $this->Territory->find('list',array('conditions'=> array('office_id' => $office_id,'NOT'=>array('Territory.id'=>array_keys($child_territory_id))), 'order'=>array('name'=>'asc')));
		}else{
			$territories = array();
		}
		
		
		$this->set(compact('users', 'offices', 'territories'));
				
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Territory list Details');
		if (!$this->UserTerritoryList->exists($id)) {
			throw new NotFoundException(__('Invalid message list'));
		}
		$options = array('conditions' => array('UserTerritoryList.' . $this->UserTerritoryList->primaryKey => $id));
		$this->set('messageList', $this->UserTerritoryList->find('first', $options));
		$this->loadModel('UserTerritoryList');	
		$this->set('messageProduct', $this->UserTerritoryList->find('all', array(
		'conditions' => array('UserTerritoryList.message_id' => $id),
		'order' => array('Product.order'=>'asc'),
		'recursive' => 0
		)
		));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','User to Territory Assign');
		if ($this->request->is('post')) {
			
			if(!empty($this->request->data['UserTerritoryList']['territory_id']))
			{
				//pr($this->request->data);
				//exit;
				//$this->UserTerritoryList->saveAll($this->request->data);
				
				/*pr($this->request->data['UserTerritoryList']['territory_id']);
				exit;*/
			
				$territory_array = array();
				
				foreach($this->request->data['UserTerritoryList']['territory_id'] as $key => $val)
				{
					$data['user_id'] = $this->request->data['UserTerritoryList']['user_id'];
					
					$data['territory_id'] = $val;
					$data['office_id'] = $this->request->data['UserTerritoryList']['office_id'];
					$territory_array[] = $data;
				}
				
				//pr($territory_array);
				//exit;
				
				if($this->UserTerritoryList->saveAll($territory_array)){
					$this->Session->setFlash(__('The message has been saved'), 'flash/success');
					$this->redirect(array('action' => 'index'));
					exit;
				}
			}
			else 
			{
				$this->Session->setFlash(__('Please select at least one Territory.'), 'flash/error');
				$this->redirect(array('action' => 'add'));
			}
		}
	
		
		$current_user_id = $this->UserAuth->getUserId();		;		
		$office_id = (isset($this->request->data['Territory']['office_id']) ? $this->request->data['Territory']['office_id'] : 0);
		$office_parent_id = $this->UserAuth->getOfficeParentId();
	
		if($office_parent_id == 0){
			$office_conditions = array(
		'office_type_id' => 2,
		"NOT" => array( "id" => array(30, 31, 37))
		);
		}else{
			$office_conditions = array(
			'office_type_id' => 2,
			"NOT" => array( "id" => array(30, 31, 37)), 
			'id' => $this->UserAuth->getOfficeId());
		}
		
		$this->loadModel('Office');
		$offices = $this->Office->find('list',array('conditions'=> $office_conditions, 'order'=>array('office_name'=>'asc')));
		
		
		
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
				'fields' => 'SalesPeople.name',
				//'order'=>array('User.username'=>'asc')
			)
		);
		
		
		
		
		$Territoryusers = $this->UserTerritoryList->find('list', 
			array(
				'fields' => 'UserTerritoryList.user_id',
				'recursive' => -1,
				//'group' => array('UserTerritoryList.user_id')
			)
		);
		
		$n_users = array();
		
		foreach($users as $key=>$val){
			//echo $key.'<br>';
			if (!in_array($key, $Territoryusers)){
					$n_users[$key] = $val;
			}
		}
		
		$users = $n_users;
		
		//$this->loadModel('Market');
		
		//$territories = $this->Market->Territory->find('list',array('conditions'=> array('office_id' => $office_id), 'order'=>array('name'=>'asc')));
		
		$this->set(compact('users', 'offices'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) 
	{
	   $user_id = $id;
	   
       $this->set('page_title','Edit User to Territory Assign');
	 

	   //get detail by user id
	   $results = $this->UserTerritoryList->find('first', array('conditions'=> array('user_id' => $user_id)));
	   
	
	   /*if (!$results) {
			//throw new NotFoundException(__('Invalid View.'));
			$this->Session->setFlash(__('Invalid View'), 'flash/error');
			$this->redirect(array('action' => 'index'));
			exit;
		}*/
	   
	   $office_id = $results['Office']['id'];
	   
		if ($this->request->is('post')) {
			
			if(!empty($this->request->data['UserTerritoryList']['territory_id']))
			{
						
				/*pr($this->request->data['UserTerritoryList']['territory_id']);
				exit;*/
				
				$this->UserTerritoryList-> deleteAll(array('user_id' => $user_id));
			
				$territory_array = array();
				
				foreach($this->request->data['UserTerritoryList']['territory_id'] as $key => $val)
				{
					$data['user_id'] = $user_id;
					
					$data['territory_id'] = $val;
					$data['office_id'] = $this->request->data['UserTerritoryList']['office_id'];
					$territory_array[] = $data;
				}
				
				/*pr($territory_array);
				exit;*/
				
				if($this->UserTerritoryList->saveAll($territory_array)){
					$this->Session->setFlash(__('The Territory list has been saved'), 'flash/success');
					$this->redirect(array('action' => 'index'));
					exit;
				}
			}
			else 
			{
				$this->Session->setFlash(__('Please select at least one Territory.'), 'flash/error');
				$this->redirect(array('action' => 'edit/'.$user_id));
			}
		}
	
	
		//$office_id = 29;
		
		
		
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if($office_parent_id == 0){
			$office_conditions = array(
		'office_type_id' => 2,
		"NOT" => array( "id" => array(30, 31, 37))
		);
		}else{
			$office_conditions = array(
			'office_type_id' => 2,
			"NOT" => array( "id" => array(30, 31, 37)), 
			'id' => $this->UserAuth->getOfficeId());
		}
		
		$this->loadModel('Office');
		$offices = $this->Office->find('list',array('conditions'=> $office_conditions, 'order'=>array('office_name'=>'asc')));
		
		$this->loadModel('User');
		
		/*$users = $this->User->find('list', 
			array(
				'conditions' => array(
				'active' => 1, 
				'user_group_id' => 1008 //change here for SPO
			),
			'fields' => 'username',
			'order'=>array('id'=>'asc')
		));*/
		
		//echo $id;
		
		$users = $this->User->find('list', 
			array(
				'conditions' => array(
					'User.active' => 1, 
					'User.user_group_id' => 1008, //change here for SPO
					'User.id' => $id
				),
				'joins' => array(
					array(
						'alias' => 'SalesPeople',
						'table' => 'sales_people',
						'type' => 'INNER',
						'conditions' => 'User.sales_person_id = SalesPeople.id'
					)
				),
				'fields' => 'SalesPeople.name',
				//'order'=>array('User.username'=>'asc')
			)
		);
		
		//pr($users);
		
		//$this->loadModel('Market');
		
		//$territories = $this->Market->Territory->find('list',array('conditions'=> array('office_id' => $office_id), 'order'=>array('name'=>'asc')));
		
		$username = $this->getUserName($id);

		$this->set(compact('users', 'offices', 'username', 'office_id', 'user_id'));
		
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) 
	{
		if ($this->UserTerritoryList-> deleteAll(array('user_id' => $id))) 
		{
			$this->Session->setFlash(__('Territory list deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Territory list was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	
	
	public function get_territory_list()
	{
		$this->loadModel('Territory');
		
		$office_id = $this->request->data['office_id'];
		$user_id = $this->request->data['user_id'];
		
		//get detail by user id
		$results = $this->UserTerritoryList->find('first', array('conditions'=> array('user_id' => $user_id)));
		@$sales_person_id = $results['User']['sales_person_id'];
		$this->loadModel('SalesPerson');
		$so_info=$this->SalesPerson->find('first', array('conditions'=> array('id' => $sales_person_id), 'recursive' => -1));
		@$parent_territory_id = $so_info['SalesPerson']['territory_id'];
		
		
		
		$rs = array();
		
		$user_territory = array();
		if($user_id){
			$user_territory = $this->UserTerritoryList->find('list', array(
			'fields' => array('territory_id'),
			'conditions' => array('user_id' => $user_id),
			//'order' => array('Territory.name' => 'asc'),
			'recursive' => -1
			));
			//pr($user_territory);
		}

		/***Show Except Child Territory ***/
			
		$child_territory_id = $this->Territory->find('list',array(
			'conditions'=> array(
				'parent_id !=' => 0,
			),
			'fields'=>array('Territory.id','Territory.name'),
		));
        
		$territory = $this->Territory->find('all', array(
			'fields' => array('Territory.id', 'Territory.name'),
			'conditions' => array('Territory.office_id' => $office_id,'NOT'=>array('Territory.id'=>array_keys($child_territory_id))),
			'order' => array('Territory.name' => 'asc'),
			'recursive' => -1
		));
				
		
		$output = '';
		
		foreach($territory as $result)
		{
			//pr($result);
			if($parent_territory_id!=$result['Territory']['id'])
			{
				if (in_array($result['Territory']['id'], $user_territory)){
					$output .= '<div  style="position: relative;width: 100%;float: left;" class="checkbox">
					<div style="position: relative;width: 100%;float: left;">
						<input checked name="data[UserTerritoryList][territory_id][]" value="'.$result['Territory']['id'].'" id="UserTerritoryListProductId'.$result['Territory']['id'].'" type="checkbox">
					</div>
					<label for="UserTerritoryListProductId'.$result['Territory']['id'].'" style="width:auto;margin: 0; padding: 0;" for="UserTerritoryListProductId27" class="">'.$result['Territory']['name'].'</label>
					</div>';
				}else{
					$output .= '<div  style="position: relative;width: 100%;float: left;" class="checkbox">
					<div style="position: relative;width: 100%;float: left;">
						<input name="data[UserTerritoryList][territory_id][]" value="'.$result['Territory']['id'].'" id="UserTerritoryListProductId'.$result['Territory']['id'].'" type="checkbox">
					</div>
					<label for="UserTerritoryListProductId'.$result['Territory']['id'].'" style="width:auto;margin: 0; padding: 0;" for="UserTerritoryListProductId27" class="">'.$result['Territory']['name'].'</label>
					</div>';
				}
			}
			
		}
		
		echo $output;
		
		$this->autoRender = false;
		
	}
	
	function getUserTerritory($user_id = 0, $list = 0)
	{
		$this->loadModel('UserTerritoryList');
		
		
		
		$sql = "SELECT utl.*, t.name as territory_name from user_territory_lists utl
		left join territories t ON(utl.territory_id=t.id)
		 WHERE utl.user_id = $user_id";
		
		$results = $this->UserTerritoryList->query($sql);	
		
		if($list)
		{
			$TerritoryList = '';
			$total = count($results);
			$i=1;
			foreach($results as $result)
			{
				if($total==$i){
					$TerritoryList .= $result[0]['territory_name'];
				}else{
					$TerritoryList .= $result[0]['territory_name'].', ';
				}
				$i++;
			}
			
			return $TerritoryList;
		}
				
		return $results ;
	}
	
	function getUserName($user_id = 0)
	{
		$this->loadModel('User');
		$user_detail=$this->User->find('all', array(
		'conditions'=>array('id'=>$user_id),
		'recursive' => -1,
		));
		return $user_detail[0]['User']['username'];
	}
	
	function getOfficeName($id = 0)
	{
		$this->loadModel('Office');
		$detail=$this->Office->find('all', array(
		'conditions'=>array('id'=>$id),
		'recursive' => -1,
		));
		return $detail[0]['Office']['office_name'];
	}
	
	
	
	
}
