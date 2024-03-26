<?php
App::uses('AppController', 'Controller');
/**
 * ProxySells Controller
 *
 * @property ProxySell $ProxySell
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ProxySellsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() 
	{
		$office_parent_id 	= 	$this->UserAuth->getOfficeParentId();
		$office_id 	= 	$this->UserAuth->getOfficeId();
		
		$this->set('page_title','Proxy sell List');
		$this->ProxySell->recursive = 0;
		
		if($office_parent_id){
			$conditions = array('ProxySell.created_by' => $this->UserAuth->getOfficeId());
		}else{
			$conditions = array();	
		}
		
		$this->paginate = array(
		'conditions' => $conditions,
		'order' => array('ProxySell.id' => 'DESC')
		);
		//pr($this->paginate());
		$this->set('proxySells', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Proxy sell Details');
		if (!$this->ProxySell->exists($id)) {
			throw new NotFoundException(__('Invalid proxy sell'));
		}
		$options = array('conditions' => array('ProxySell.' . $this->ProxySell->primaryKey => $id));
		$this->set('proxySell', $this->ProxySell->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() 
	{
		
		
		$this->set('page_title','Add Proxy sell');
		
		
		if ($this->request->is('post')) 
		{
			$this->ProxySell->create();
			$this->request->data['ProxySell']['created_at'] = $this->current_datetime();
			$this->request->data['ProxySell']['created_by'] = $this->request->data['ProxySell']['office_id'];
			$this->request->data['ProxySell']['updated_at'] = $this->current_datetime();
			$this->request->data['ProxySell']['updated_by'] = $this->request->data['ProxySell']['office_id'];
			
			$this->request->data['ProxySell']['proxy_for_territory_id']=$this->find_territory_id($this->request->data['ProxySell']['proxy_for_so_id']);
			$this->request->data['ProxySell']['proxy_by_territory_id']=$this->find_territory_id($this->request->data['ProxySell']['proxy_by_so_id']);
			//pr($this->request->data);die();
			if ($this->ProxySell->save($this->request->data)) {
				$this->Session->setFlash(__('The proxy sell has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The proxy sell could not be saved. Please, try again.'), 'flash/error');
			}
		}
		
		$sp_name =$this->ProxySell->proxyForSo->find('all', array(
			'fields'=>array('proxyForSo.*','T.name'),
            'conditions' => array('st.store_type_id' => 3, 'st.office_id' => $this->UserAuth->getOfficeId()),
            'joins'=>array(
              array(
                'table'=>'stores',
                'alias'=>'st',
                'type' => 'INNER',
                'conditions'=>array(
                  'proxyForSo.office_id=st.office_id AND proxyForSo.territory_id=st.territory_id'
                  )
                ),
				array(
					'table'=>'territories',
					'alias'=>'T',
					'type' => 'INNER',
					'conditions'=>array(
					  'proxyForSo.territory_id=T.id'
                  )
				 )
              ),
            'order' => array('st.name' => 'asc'),
            'recursive'=>-1
        ));
		
		foreach($sp_name as $data){
			$proxyForSos[$data['proxyForSo']['id']]=$data['proxyForSo']['name'].'  ( '.$data['T']['name'].' )';
		}
		
		//$proxyBySos = $proxyForSos;
		
		//add new
		$office_parent_id 	= 	$this->UserAuth->getOfficeParentId();
		$office_id 	= 	$this->UserAuth->getOfficeId();
		
		$this->set(compact('office_id', 'office_parent_id'));
		
			
		
		if(!$office_parent_id)
		{
			$proxyBySos = array();
			
			$this->loadModel('Office');
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' => 2,
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
			$this->set(compact('offices'));
			
		}
		else
		{
			$this->loadModel('SalesPerson');				
			$proxyBySos = $this->SalesPerson->find('list', array(
				'fields' => array('SalesPerson.id','SalesPerson.name','SalesPerson.last_data_push_time','Territory.name'),
				'conditions' => array(
					'SalesPerson.office_id' => $office_id,
					//'SalesPerson.territory_id >' => 0,
					//'User.user_group_id' => 4
					
				),
				'order' => array('SalesPerson.name' => 'asc'),
				'recursive' => 0
			));
		}
		
		//pr($proxyBySos);
		
		//end add new
		
		$this->set(compact('proxyForSos', 'proxyBySos', 'proxyForTerritories', 'proxyByTerritories'));
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
        $this->set('page_title','Edit Proxy sell');
		$this->ProxySell->id = $id;
		if (!$this->ProxySell->exists($id)) {
			throw new NotFoundException(__('Invalid proxy sell'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['ProxySell']['updated_at'] = $this->current_datetime();
			//$this->request->data['ProxySell']['updated_by'] = $this->UserAuth->getUserId();
			
			if ($this->ProxySell->save($this->request->data)) {
				$this->Session->setFlash(__('The proxy sell has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The proxy sell could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('ProxySell.' . $this->ProxySell->primaryKey => $id));
			$this->request->data = $this->ProxySell->find('first', $options);
		}
		
		
		$proxy_info = $this->ProxySell->find('first', $options);
		
		$office_parent_id 	= 	$this->UserAuth->getOfficeParentId();
		$office_id 	= 	$proxy_info['ProxySell']['created_by'];
		
		$sp_name = $this->ProxySell->proxyForSo->find('all', array(
			'fields'=>array('proxyForSo.*','T.name'),
            'conditions' => array('st.store_type_id' => 3, 'st.office_id' => $office_id),
            'joins'=>array(
              array(
                'table'=>'stores',
                'alias'=>'st',
                'type' => 'INNER',
                'conditions'=>array(
                  'proxyForSo.office_id=st.office_id AND proxyForSo.territory_id=st.territory_id'
                  )
                ),
				array(
					'table'=>'territories',
					'alias'=>'T',
					'type' => 'INNER',
					'conditions'=>array(
					  'proxyForSo.territory_id=T.id'
                  )
				 )
              ),
            'order' => array('st.name' => 'asc'),
            'recursive'=>-1
        ));
		foreach($sp_name as $data){
			$proxyForSos[$data['proxyForSo']['id']]=$data['proxyForSo']['name'].'  ( '.$data['T']['name'].' )';
		}
		//$proxyBySos=$proxyForSos;
		
		
		//add new
		
		
		$this->set(compact('office_id', 'office_parent_id'));
		
		$proxyBySos = array();
		
		$this->loadModel('Office');
		$offices = $this->Office->find('list', array(
			'conditions'=> array(
				'office_type_id' => 2,
				"NOT" => array( "id" => array(30, 31, 37))
				), 
			'order'=>array('office_name'=>'asc')
		));
		$this->set(compact('offices'));
		
	
		$this->loadModel('SalesPerson');				
		$proxyBySos_list = $this->SalesPerson->find('all', array(
			'fields' => array('SalesPerson.id','SalesPerson.name','SalesPerson.last_data_push_time','Territory.name'),
			'conditions' => array(
				'SalesPerson.office_id' => $office_id,
				//'SalesPerson.territory_id >' => 0,
				//'User.user_group_id' => 4
				
			),
			'order' => array('SalesPerson.name' => 'asc'),
			'recursive' => 0
		));
		//pr($proxyBySos_list);
		$proxyBySos = array();
		
		foreach($proxyBySos_list as $key => $value)
		{
			$proxyBySos[$value['SalesPerson']['id']] = $value['SalesPerson']['name'].' ('.$value['Territory']['name'].')';
		}
		
		//pr($proxyBySos);
		//exit;
		
		//end add new
		
		//$proxyForSos = $this->ProxySell->ProxyForSo->find('list');
		//$proxyBySos = $this->ProxySell->ProxyBySo->find('list');
		//$proxyForTerritories = $this->ProxySell->ProxyForTerritory->find('list');
		//$proxyByTerritories = $this->ProxySell->ProxyByTerritory->find('list');
		$this->set(compact('proxyForSos', 'proxyBySos', 'proxyForTerritories', 'proxyByTerritories'));
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
		$this->ProxySell->id = $id;
		if (!$this->ProxySell->exists()) {
			throw new NotFoundException(__('Invalid proxy sell'));
		}
		if ($this->ProxySell->delete()) {
			$this->Session->setFlash(__('Proxy sell deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Proxy sell was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	
	/** 
		Find terrtory for so .... 
		
	**/
	public function find_territory_id($so_id){
		$territory_id =$this->ProxySell->proxyForSo->find('first',array(
			'fields'=>array('proxyForSo.territory_id'),
			'conditions'=>array('proxyForSo.id'=>$so_id),
			'recursive'=>-1
		));
		return $territory_id['proxyForSo']['territory_id'];
	}
	
	
	public function get_so_for_list()
	{
		$this->loadModel('SalesPerson');	
		
		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		
		$office_id = $this->request->data['office_id'];
        			
		$so_list = $this->SalesPerson->find('all', array(
			'fields' => array('SalesPerson.id','SalesPerson.name','Territory.name'),
			'conditions' => array(
				'SalesPerson.office_id' => $office_id,
				'SalesPerson.territory_id >' => 0,
				'User.user_group_id' => 4
			),
			'order' => array('SalesPerson.name' => 'asc'),
			'recursive' => 0
		));	
		
		//pr($so_list);
		
		$data_array = Set::extract($so_list, '{n}.SalesPerson');
		
		$data_array = array();
		
		foreach($so_list as $key => $value)
		{
			$data_array[] = array(
				'id' => $value['SalesPerson']['id'],
				'name' => $value['SalesPerson']['name'].' ('.$value['Territory']['name'].')',
			);
		}
		
		//pr($data_array);
		
		if(!empty($so_list)){
			echo json_encode(array_merge($rs, $data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}
	
	public function get_so_by_list()
	{
		$this->loadModel('SalesPerson');	
		
		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		
		$office_id = $this->request->data['office_id'];
        			
		$so_list = $this->SalesPerson->find('all', array(
			'fields' => array('SalesPerson.id','SalesPerson.name','Territory.name'),
			'conditions' => array(
				'SalesPerson.office_id' => $office_id,
				//'SalesPerson.territory_id >' => 0,
				//'User.user_group_id' => 4
			),
			'order' => array('SalesPerson.name' => 'asc'),
			'recursive' => 0
		));	
		
		//pr($so_list);
		
		$data_array = Set::extract($so_list, '{n}.SalesPerson');
		
		$data_array = array();
		
		foreach($so_list as $key => $value)
		{
			$data_array[] = array(
				'id' => $value['SalesPerson']['id'],
				'name' => $value['SalesPerson']['name'].' ('.$value['Territory']['name'].')',
			);
		}
		
		//pr($data_array);
		
		if(!empty($so_list)){
			echo json_encode(array_merge($rs, $data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}
	
}
