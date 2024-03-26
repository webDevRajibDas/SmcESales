<?php
App::uses('AppController', 'Controller');
/**
 * Markets Controller
 *
 * @property Market $Market
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class MarketsController extends AppController {

/**
 * Components
 *
 * @var array
 */	
	public $uses = array('Market','District','Thana');
	public $components = array('Paginator', 'Session','Filter.Filter');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','Market List');	
				
		$office_id = (isset($this->request->data['Market']['office_id']) ? $this->request->data['Market']['office_id'] : 0);
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if($office_parent_id == 0)
		{					
			$conditions = array();
			$office_conditions = array();
			$territory_conditions = array('conditions'=>array('Territory.office_id' => $office_id),'order' => array('Territory.name' => 'ASC'));
		}else{				
			$conditions = array('Territory.office_id' => $this->UserAuth->getOfficeId());
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			$territory_conditions = array('conditions'=>array('Territory.office_id' => $office_id),'order' => array('Territory.name' => 'ASC'));
		}
		
		$this->Market->recursive = 0;
		$this->paginate = array(				
			'joins' => array(
				array(
					'table' => 'offices',
					'alias' => 'Office',
					'type' => 'INNER',
					'conditions' => array(
						'Office.id = Territory.office_id'
					)
				)
			),	
			'conditions' => $conditions,
			'fields' => array('Market.*','LocationType.name','Thana.name','Territory.name','Office.office_name'),
			'order' => array('Market.id'=>'desc')			
		);
		$this->set('markets', $this->paginate());		
		
		$this->loadModel('Office');
		$offices = $this->Office->find('list',array('conditions'=> $office_conditions,'order'=>array('office_name'=>'asc')));
		$location_list = $this->Market->LocationType->find('list');
		$district_list = $this->District->find('list',array('order' => array('District.name' => 'ASC')));
		$this->set('district_list',$district_list);
		$thana_list = $this->Market->Thana->find('list',array(
			'conditions' => array('Thana.district_id'=>(isset($this->request->data['Market']['district_id']) ? $this->request->data['Market']['district_id'] : 0)),
			'order' => array('Thana.name' => 'ASC')
		));		
		$territory_list = $this->Market->Territory->find('list',$territory_conditions);
		
		$this->set(compact('location_list', 'thana_list', 'offices', 'territory_list'));
		
	}
	
	public function get_thana_list()
	{
		$rs = array(array('id' => '', 'title' => '---- Select Thana -----'));
		$district_id = $this->request->data['district_id'];
        $thana = $this->Thana->find('all', array(
			'fields' => array('Thana.id as id', 'Thana.name as title'),
			'conditions' => array('Thana.district_id' => $district_id),
			'order' => array('Thana.name'=>'asc'),
			'recursive' => -1
		));	
		$data_array = Set::extract($thana, '{n}.0');
		if(!empty($thana)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Market Details');
		if (!$this->Market->exists($id)) {
			throw new NotFoundException(__('Invalid market'));
		}
		$options = array('conditions' => array('Market.' . $this->Market->primaryKey => $id));
		$this->set('market', $this->Market->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Market');
		if ($this->request->is('post')) {
			$this->request->data['Market']['created_at'] = $this->current_datetime(); 
			$this->request->data['Market']['updated_at'] = $this->current_datetime(); 
			$this->request->data['Market']['created_by'] = $this->UserAuth->getUserId();
                        $this->request->data['Market']['action'] = 1;
			$this->Market->create();
			if ($this->Market->save($this->request->data)) {
				$this->Session->setFlash(__('The market has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		}
		
		$office_id = (isset($this->request->data['Territory']['office_id']) ? $this->request->data['Territory']['office_id'] : 0);
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if($office_parent_id == 0){
			$office_conditions = array();			
		}else{
			$office_conditions = array('id' => $this->UserAuth->getOfficeId());
		}
		
		$this->loadModel('Office');
		$offices = $this->Office->find('list',array('conditions'=> $office_conditions,'order'=>array('office_name'=>'asc')));
		$locationTypes = $this->Market->LocationType->find('list');
		$thanas = $this->Market->Thana->find('list',array('order'=>array('name'=>'asc')));
		$territories = $this->Market->Territory->find('list',array('conditions'=> array('office_id' => $office_id), 'order'=>array('name'=>'asc')));
		$this->set(compact('locationTypes', 'thanas', 'territories', 'offices'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->set('page_title','Edit Market');
        $this->Market->id = $id;
		if (!$this->Market->exists($id)) {
			throw new NotFoundException(__('Invalid market'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Market']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['Market']['updated_at'] = $this->current_datetime();
                        $this->request->data['Market']['action'] = 1;
			if ($this->Market->save($this->request->data)) {
				$this->Session->setFlash(__('The market has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The market could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$this->Market->recursive = 0;
			$options = array('conditions' => array('Market.' . $this->Market->primaryKey => $id));
			$this->request->data = $this->Market->find('first', $options);
		}
		
		$office_id = $this->request->data['Territory']['office_id'];
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if($office_parent_id == 0){
			$office_conditions = array();			
		}else{
			$office_conditions = array('id' => $this->UserAuth->getOfficeId());
		}
		
		$this->loadModel('Office');
		$offices = $this->Office->find('list',array('conditions'=> $office_conditions,'order'=>array('office_name'=>'asc')));
		$locationTypes = $this->Market->LocationType->find('list');
		$thanas = $this->Market->Thana->find('list',array('order'=>array('name'=>'asc')));
		$territories = $this->Market->Territory->find('list',array('conditions'=> array('office_id' => $office_id), 'order'=>array('name'=>'asc')));
		$this->set(compact('locationTypes', 'thanas', 'territories', 'offices'));
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
		$this->Market->id = $id;
		if (!$this->Market->exists()) {
			throw new NotFoundException(__('Invalid market'));
		}
		if ($this->Market->delete()) {
			$this->Session->setFlash(__('Market deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Market was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	
	public function get_market_list() 
	{		
		$territory_id = $this->request->data['territory_id'];
		$location_type_id = $this->request->data['location_type_id'];
		
		if($territory_id > 0 AND $location_type_id > 0)
		{
			$conditions = array('territory_id' => $territory_id,'location_type_id' => $location_type_id,'is_active' => 1);
		}else{
			$conditions = array('territory_id' => $territory_id,'is_active' => 1);
		}
        
		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		$market_list = $this->Market->find('all',array(
				'conditions' => $conditions, 	
				'order' => array('Market.name'=>'ASC'),
				'recursive' => -1
			));
		$data_array = Set::extract($market_list, '{n}.Market');
		if(!empty($market_list)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
			
	}

	public function download_xl() {
		
		ini_set('memory_limit', '2048M');
		ini_set('max_execution_time', 900); //300 seconds = 5 minutes

		$office_id = (isset($this->request->data['Market']['office_id']) ? $this->request->data['Market']['office_id'] : 0);
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		$params = $this->request->query['data'];
		if($office_parent_id == 0)
		{					
			$conditions = array();
			$office_conditions = array();
			$territory_conditions = array('conditions'=>array('Territory.office_id' => $office_id),'order' => array('Territory.name' => 'ASC'));
		}else{				
			$conditions = array('Territory.office_id' => $this->UserAuth->getOfficeId());
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			$territory_conditions = array('conditions'=>array('Territory.office_id' => $office_id),'order' => array('Territory.name' => 'ASC'));
		}


		$conditions = array();
		//echo $office_id;exit;
		
		if($office_parent_id != 0)
		{
			$conditions[] = array('Territory.office_id' => $this->UserAuth->getOfficeId());
		}
		
		if (!empty($params['Market']['code'])) {
            $conditions[] = array('Market.code' => $params['Market']['code']);
        }
		if (!empty($params['Market']['name'])) {
            $conditions[] = array('Market.name LIKE' => '%'.$params['Market']['name'].'%');
        }
		if (!empty($params['Market']['location_type_id'])) {           
			$conditions[] = array('Market.location_type_id' => $params['Market']['location_type_id']);
        }
		if (!empty($params['Market']['district_id'])) {
            $conditions[] = array('Thana.district_id' => $params['Market']['district_id']);
        }
		if (!empty($params['Market']['thana_id'])) {
            $conditions[] = array('Market.thana_id' => $params['Market']['thana_id']);
        }
		if (!empty($params['Market']['office_id'])) {
			$conditions[] = array('Territory.office_id' => $params['Market']['office_id']);
		}
		if (!empty($params['Market']['territory_id'])) {
            $conditions[] = array('Market.territory_id' => $params['Market']['territory_id']);
        }
        
		
		/*here start */
		// $this->Market->recursive = 0;
		// print_r($conditions);exit;
		
		
		
		$results = $this->Market->find('all',array(				
			'joins' => array(
				array(
					'table' => 'offices',
					'alias' => 'Office',
					'type' => 'INNER',
					'conditions' => array(
						'Office.id = Territory.office_id'
					)
				)
			),	
			'conditions' => $conditions,
			'fields' => array('Market.*','LocationType.name','Thana.name','Territory.name','Office.office_name'),
			'reccursive'=>0,
			'order' => array('Market.id'=>'desc')			
		));
		
		//echo '<pre>';print_r($results);exit;
		
		// $this->set('markets', $this->paginate());
		// print_r($results);exit;
		$markets=$results;		
		
		$this->loadModel('Office');
		$offices = $this->Office->find('list',array('conditions'=> $office_conditions,'order'=>array('office_name'=>'asc')));
		$location_list = $this->Market->LocationType->find('list');
		$district_list = $this->District->find('list',array('order' => array('District.name' => 'ASC')));
		$this->set('district_list',$district_list);
		$thana_list = $this->Market->Thana->find('list',array(
			'conditions' => array('Thana.district_id'=>(isset($this->request->data['Market']['district_id']) ? $this->request->data['Market']['district_id'] : 0)),
			'order' => array('Thana.name' => 'ASC')
		));		
		$territory_list = $this->Market->Territory->find('list',$territory_conditions);
		
		// $this->set(compact('location_list', 'thana_list', 'offices', 'territory_list'));

		$table ='<table border="1">
					<thead>
						<tr>
							<td><b>Id</b></td>
							<td><b>Name</b></td>
							<td><b>Address</b></td>
							<td><b>Location Type</b></td>
							<td><b>Thana</b></td>
							<td><b>Territory</b></td>
							<td><b>Office</b></td>
							<td>Is Active</td>
							
						</tr>
					</thead>
					<tbody>';
					 foreach ($markets as $market): 
					$table .='<tr>
						<td>'.h($market['Market']['id']).'</td>
						<td>'.h($market['Market']['name']).'</td>
						<td>'. h($market['Market']['address']).'</td>
						<td>'. h($market['LocationType']['name']).'</td>
						<td>'. h($market['Thana']['name']).'</td>
						<td>'. h($market['Territory']['name']).'</td>
						<td>'. h($market['Office']['office_name']).'</td>
						<td>';
						
							if($market['Market']['is_active']==1){
								$table .=''. h('Yes').'';
							}else{
								$table .=''. h('No').'';
							}
						
					$table .='</td>
						
					</tr>';
					 endforeach; 
					 $table .='</tbody>
				</table>';


				header("Content-Type: application/vnd.ms-excel");
				header('Content-Disposition: attachment; filename="Markets.xls"');
				header("Cache-Control: ");
				header("Pragma: ");
				header("Expires: 0");
				echo $table;
				$this->autoRender = false;
		
	}
	
}
