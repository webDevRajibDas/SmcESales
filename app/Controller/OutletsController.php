<?php
App::uses('AppController', 'Controller');
/**
 * Outlets Controller
 *
 * @property Outlet $Outlet
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class OutletsController extends AppController {

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
		
		$this->set('page_title','Outlet List');
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if($office_parent_id == 0){
			$conditions = array();
		}else{
			$conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}
		$this->Outlet->virtualFields=array(
			'thana_name'=>'Thana.name'
			);
		/*$this->paginate = array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'Territory',
					'table' => 'territories',
					'type' => 'INNER',
					'conditions' => 'Market.territory_id = Territory.id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'Territory.office_id = Office.id'
				),
				array(
					'alias' => 'Thana',
					'table' => 'thanas',
					'type' => 'INNER',
					'conditions' => 'Thana.id = Market.thana_id'
					)
			),
			'fields' => array(
			'Outlet.id','Outlet.code','Outlet.name','Outlet.in_charge','Outlet.address',
			'Outlet.telephone','Outlet.mobile','Outlet.market_id','Outlet.category_id',
			'Outlet.is_pharma_type','Outlet.is_ngo','Outlet.bonus_type_id','Institute.name',
			'Market.id','Market.code','Market.name','Territory.id', 'Territory.name','Office.id','Office.office_code','Office.office_name','Office.office_type_id','OutletCategory.category_name','Thana.id','Thana.name'),
			'order'=>   array('Outlet.id' => 'desc')   
		);
		*/
		$this->paginate = array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'Institute',
					'table' => 'institutes',
					'type' => 'LEFT',
					'conditions' => 'Outlet.institute_id = Institute.id'
				),
				array(
					'alias' => 'OutletCategory',
					'table' => 'outlet_categories',
					'type' => 'INNER',
					'conditions' => 'Outlet.category_id = OutletCategory.id'
				),
				array(
					'alias' => 'Market',
					'table' => 'markets',
					'type' => 'INNER',
					'conditions' => 'Outlet.market_id = Market.id'
				),
				array(
					'alias' => 'Territory',
					'table' => 'territories',
					'type' => 'INNER',
					'conditions' => 'Market.territory_id = Territory.id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'Territory.office_id = Office.id'
				),
				array(
					'alias' => 'Thana',
					'table' => 'thanas',
					'type' => 'INNER',
					'conditions' => 'Thana.id = Market.thana_id'
					)
			),
			'fields' => array(
			'Outlet.id','Outlet.code','Outlet.name','Outlet.in_charge','Outlet.address','Outlet.is_active',
			'Outlet.telephone','Outlet.mobile','Outlet.market_id','Outlet.category_id',
			'Outlet.is_pharma_type','Outlet.is_ngo','Outlet.bonus_type_id','Institute.name',
			'Market.id','Market.code','Market.name','Territory.id', 'Territory.name','Office.id','Office.office_code','Office.office_name','Office.office_type_id','OutletCategory.category_name','Thana.id','Thana.name'),
			'order'=>   array('Outlet.id' => 'desc'),
			'recursive' => -1
		);
		$this->set('outlets', $this->paginate());
		
		
		$this->loadModel('Office');
		$this->loadModel('Territory');
		

		$conditions['NOT']=array( "id" => array(30, 31, 37)); 
		$conditions['Office.office_type_id']=2; 
		$offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
		
		$office_id = (isset($this->request->data['Outlet']['office_id']) ? $this->request->data['Outlet']['office_id'] : 0);
		$territories = $this->Territory->find('list',array('conditions'=> array('office_id' => $office_id), 'order'=>array('name'=>'asc')));
				
		$territory_id = (isset($this->request->data['Outlet']['territory_id']) ? $this->request->data['Outlet']['territory_id'] : 0);
		$thana_id = (isset($this->request->data['Outlet']['thana_id']) ? $this->request->data['Outlet']['thana_id'] : 0);
		
		$market_id = (isset($this->request->data['Outlet']['market_id']) ? $this->request->data['Outlet']['market_id'] : 0);
		
		if($territory_id)
		{
			//for thana list
			
			if($territory_id)
			{
				$this->loadModel('ThanaTerritory');
				$conditions = array('ThanaTerritory.territory_id' => $territory_id);
				
				$rs = array(array('id' => '', 'name' => '---- Select -----'));
				$thana_lists = $this->ThanaTerritory->find('all',array(
						'conditions' => $conditions, 	
						//'order' => array('Thana.name'=>'ASC'),
						'recursive' => 1
					));
							
				foreach($thana_lists as $thana_info){
					$thanas[$thana_info['Thana']['id']]=$thana_info['Thana']['name'];
				}
			}
		}
		else
		{
			$thanas = array();
		}
		
		
		
		if($thana_id)
		{
			$markets = $this->Outlet->Market->find('list',array(
				'conditions' => array('Market.thana_id'=>$thana_id),
				'order' => array('Market.name' => 'ASC'),
				'recursive' => 0
			));
		}
		else
		{
			$markets = array();
		}
		
		$this->LoadModel('BonusCardType');
		$categories = $this->Outlet->OutletCategory->find('list');		
		$bonus_types = $this->BonusCardType->find('list');		
		$this->set(compact('categories', 'offices', 'territories', 'markets', 'thanas','bonus_types'));		
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Outlet Details');
		if (!$this->Outlet->exists($id)) {
			throw new NotFoundException(__('Invalid outlet'));
		}
		$joins = array(
				
				
				array(
					'table' => 'location_types',
					'alias' => 'LocationType',
					'type' => 'INNER',
					'conditions' => array(
						'LocationType.id = Outlet.location_type_id'
					)
				)
			);
		$options = array(
			//'joins' => $joins,
			//'fields' => array('Outlet.*','OutletCategory.category_name','Market.*','LocationType.name','Territory.name','Institute.*','Office.office_name'),
			'conditions' => array('Outlet.id' => $id),
			
			'recursive'=>1
		);
		$options = $this->Outlet->find('first', $options);
	
		$this->set('outlet', $options);
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Outlet');
		if ($this->request->is('post')) {
			
			if($this->request->data['Outlet']['is_ngo'] == 0)
			{
				$this->request->data['Outlet']['institute_id'] = 0;
			}
			
			$this->request->data['Outlet']['created_at'] = $this->current_datetime(); 
			$this->request->data['Outlet']['updated_at'] = $this->current_datetime(); 
			$this->request->data['Outlet']['created_by'] = $this->UserAuth->getUserId();
			$this->Outlet->create();
			if ($this->Outlet->save($this->request->data)) {
				$this->Session->setFlash(__('The outlet has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The outlet could not be saved. Please, try again.'), 'flash/error');
			}
		}
		
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if($office_parent_id == 0){
			$office_conditions = array();			
		}else{
			$office_conditions = array('id' => $this->UserAuth->getOfficeId());
		}
		
		$this->loadModel('Office');
		$this->loadModel('Territory');
		$offices = $this->Office->find('list',array('conditions'=> $office_conditions,'order'=>array('office_name'=>'asc')));
		$office_id = (isset($this->request->data['Outlet']['office_id']) ? $this->request->data['Outlet']['office_id'] : 0);
		$territories = $this->Territory->find('list',array('conditions'=> array('office_id' => $office_id), 'order'=>array('name'=>'asc')));
				
		$territory_id = (isset($this->request->data['Outlet']['territory_id']) ? $this->request->data['Outlet']['territory_id'] : 0);
		$markets = $this->Outlet->Market->find('list',array(
			'conditions' => array('Market.territory_id'=>$territory_id),
			'order' => array('Market.name' => 'ASC'),
			'recursive' => 0
		));
		
		$locationTypes = $this->Outlet->Market->LocationType->find('list');
		$instituteTypes = array(1 => 'NGO',	2 => 'Institute');		
		$categories = $this->Outlet->OutletCategory->find('list',array('conditions'=>array('is_active'=>1)));
		$institute_type_id = (isset($this->request->data['Institute']['type']) ? $this->request->data['Institute']['type'] : 0);
		$institutes = $this->Outlet->Institute->find('list',array('conditions'=>array('type'=>$institute_type_id,'is_active'=>1),'order'=>array('name'=>'asc')));
		$this->set(compact('markets', 'categories', 'institutes', 'offices', 'territories', 'locationTypes', 'instituteTypes'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->set('page_title','Edit Outlet');
        $this->Outlet->id = $id;
		if (!$this->Outlet->exists($id)) {
			throw new NotFoundException(__('Invalid outlet'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if($this->request->data['Outlet']['is_ngo'] == 0)
			{
				$this->request->data['Outlet']['institute_id'] = 0;
			}
			$this->request->data['Outlet']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['Outlet']['updated_at'] = $this->current_datetime();
			if ($this->Outlet->save($this->request->data)) {
				$this->Session->setFlash(__('The outlet has been updated'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$joins = array(
				array(
					'table' => 'territories',
					'alias' => 'Territory',
					'type' => 'INNER',
					'conditions' => array(
						'Territory.id = Market.territory_id'
					)
				)
			);
			$options = array(
				'joins' => $joins,
				'fields' => array('Outlet.*','Market.*','Territory.office_id','Institute.*'),
				'conditions' => array('Outlet.' . $this->Outlet->primaryKey => $id),
				'recursive'=>0
			);
			$this->request->data = $this->Outlet->find('first', $options);
		}	
		
		
		
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if($office_parent_id == 0){
			$office_conditions = array();			
		}else{
			$office_conditions = array('id' => $this->UserAuth->getOfficeId());
		}

		$outlets = $this->request->data;
		$this->loadModel('Office');
		$this->loadModel('Territory');
		$this->loadModel('Thana');
		$offices = $this->Office->find('list',array('conditions'=> $office_conditions,'order'=>array('office_name'=>'asc')));
		$office_id = $this->request->data['Territory']['office_id'];
		$territories = $this->Territory->find('list',array('conditions'=> array('office_id' => $office_id), 'order'=>array('name'=>'asc')));
				
		$territory_id = $this->request->data['Market']['territory_id'];
		$thana_id = $this->request->data['Market']['thana_id'];
		$conditions=array('ThanaTerritory.territory_id'=>$territory_id);
		$joins=array(
			array(
				'table'=>'thana_territories',
				'alias'=>'ThanaTerritory',
				'type'=>'Inner',
				'conditions'=>'ThanaTerritory.thana_id=Thana.id'
				)
			);
		$thanas = $this->Thana->find('list',array(
				'conditions' => $conditions,
				'joins'=>$joins, 	
				'order' => array('Thana.name'=>'ASC'),
				'recursive' => -1
			));
		$markets = $this->Outlet->Market->find('list',array(
			'conditions' => array('Market.territory_id'=>$territory_id,'Market.thana_id'=>$thana_id),
			'order' => array('Market.name' => 'ASC'),
			'recursive' => 0
		));
		$locationTypes = $this->Outlet->Market->LocationType->find('list');
		$location_type_id = $this->request->data['Market']['location_type_id'];
		$instituteTypes = array(1 => 'NGO',	2 => 'Institute');
		//$categories = $this->Outlet->OutletCategory->find('list');	
		$categories = $this->Outlet->OutletCategory->find('list',array('conditions'=>array('is_active'=>1)));
		$institute_type_id = $this->request->data['Institute']['type'];
		$institutes = $this->Outlet->Institute->find('list',array('conditions'=>array('type'=>$institute_type_id,'is_active'=>1),'order'=>array('name'=>'asc')));
		$this->set(compact('outlets', 'markets', 'categories', 'institutes', 'offices', 'office_id', 'territories', 'territory_id', 'locationTypes', 'location_type_id', 'instituteTypes','thanas','thana_id'));
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
		$this->Outlet->id = $id;
		if (!$this->Outlet->exists()) {
			throw new NotFoundException(__('Invalid outlet'));
		}
		if ($this->Outlet->delete()) {
			$this->Session->setFlash(__('Outlet deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Outlet was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	public function get_thana_list() 
	{	
		$this->LoadModel('Thana');
		$territory_id = $this->request->data['territory_id'];
		$conditions=array('ThanaTerritory.territory_id'=>$territory_id);
		$joins=array(
			array(
				'table'=>'thana_territories',
				'alias'=>'ThanaTerritory',
				'type'=>'Inner',
				'conditions'=>'ThanaTerritory.thana_id=Thana.id'
				)
			);
		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		$thana_list = $this->Thana->find('all',array(
				'conditions' => $conditions,
				'joins'=>$joins, 	
				'order' => array('Thana.name'=>'ASC'),
				'recursive' => -1
			));
		$data_array = Set::extract($thana_list, '{n}.Thana');
		if(!empty($thana_list)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}
	public function get_market_list() 
	{	
		$this->LoadModel('Market');
		$thana_id = $this->request->data['thana_id'];
		$territory_id = $this->request->data['territory_id'];
		$location_type_id = $this->request->data['location_type_id'];
		
		if($territory_id > 0 AND $location_type_id > 0)
		{
			$conditions = array('territory_id' => $territory_id,'location_type_id' => $location_type_id,'is_active' => 1,'thana_id'=>$thana_id);
		}else{
			$conditions = array('territory_id' => $territory_id,'is_active' => 1,'thana_id'=>$thana_id);
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
		
		$params = $this->request->query['data'];
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if($office_parent_id == 0){
			$conditions = array();
		}else{
			$conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}
		
		/*Here */ 
		$conditions = array();
		if($office_parent_id != 0)
		{
			$conditions[] = array('Territory.office_id' => $this->UserAuth->getOfficeId());
		}
		if (!empty($params['Outlet']['name'])) {
            $conditions[] = array('Outlet.name LIKE' => '%'.$params['Outlet']['name'].'%');
        }
		if (!empty($params['Outlet']['mobile'])) {
            $conditions[] = array('Outlet.mobile' => $params['Outlet']['mobile']);
        }
		if (!empty($params['Outlet']['category_id'])) {
            $conditions[] = array('Outlet.category_id' => $params['Outlet']['category_id']);
        }
		if (!empty($params['Outlet']['office_id'])) {
            $conditions[] = array('Territory.office_id' => $params['Outlet']['office_id']);
        }
		if (!empty($params['Outlet']['territory_id'])) {
            $conditions[] = array('Market.territory_id' => $params['Outlet']['territory_id']);
        }
		if (!empty($params['Outlet']['market_id'])) {
            $conditions[] = array('Outlet.market_id' => $params['Outlet']['market_id']);
        }
		if (!empty($params['Outlet']['thana_id'])) {
            $conditions[] = array('Thana.id' => $params['Outlet']['thana_id']);
        }
        if (!empty($params['Outlet']['bonus_type'])) {
            $conditions[] = array('Outlet.bonus_type_id' => $params['Outlet']['bonus_type']);
        }
        if (!empty($params['Outlet']['is_active'])) {
            $conditions[] = array('Outlet.is_active' => $params['Outlet']['is_active']==1?$params['Outlet']['is_active']:0);
        }
        
		$this->Outlet->virtualFields=array(
			'thana_name'=>'Thana.name'
			);
		
		$results = $this->Outlet->find('all',array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'Institute',
					'table' => 'institutes',
					'type' => 'LEFT',
					'conditions' => 'Outlet.institute_id = Institute.id'
				),
				array(
					'alias' => 'OutletCategory',
					'table' => 'outlet_categories',
					'type' => 'INNER',
					'conditions' => 'Outlet.category_id = OutletCategory.id'
				),
				array(
					'alias' => 'Market',
					'table' => 'markets',
					'type' => 'INNER',
					'conditions' => 'Outlet.market_id = Market.id'
				),
				array(
					'alias' => 'Territory',
					'table' => 'territories',
					'type' => 'INNER',
					'conditions' => 'Market.territory_id = Territory.id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'Territory.office_id = Office.id'
				),
				array(
					'alias' => 'Thana',
					'table' => 'thanas',
					'type' => 'INNER',
					'conditions' => 'Thana.id = Market.thana_id'
					)
			),
			'fields' => array(
			'Outlet.id','Outlet.code','Outlet.name','Outlet.in_charge','Outlet.address','Outlet.is_active',
			'Outlet.telephone','Outlet.mobile','Outlet.market_id','Outlet.category_id',
			'Outlet.is_pharma_type','Outlet.is_ngo','Outlet.bonus_type_id','Institute.name',
			'Market.id','Market.code','Market.name','Territory.id', 'Territory.name','Office.id','Office.office_code','Office.office_name','Office.office_type_id','OutletCategory.category_name','Thana.id','Thana.name'),
			'order'=>   array('Outlet.id' => 'desc'),
			'recursive' => -1
		));
		
		$outlets=$results;
		// echo '<pre>';
		// print_r($outlets);
		// echo '</pre>';


		$table ='<table border="1">
					<thead>
						<tr>
							<td><b>Id</b></td>
							<td><b>Name</b></td>
							<td><b>Thana Name</b></td>
							<td><b>Mobile</b></td>
							<td><b>Market</b></td>
							<td><b>Territory</b></td>
							<td><b>Office</b></td>
							<td><b>Outlet Type</b></td>
							<td><b>Is Pharma Type</b></td>
							<td><b>Is Ngo</b></td>
							<td><b>Institute</b></td>
							<td><b>Bonus Type</b></td>
							<td><b>Stauts</b></td>
							
						</tr>
					</thead>
					<tbody>';
					 foreach ($outlets as $outlet): 
					$table .='<tr>
						<td>'. h($outlet['Outlet']['id']).'</td>
						<td>'. h($outlet['Outlet']['name']).'</td>
						
						<td>'. h($outlet['Thana']['name']).'</td>
						<td>'. h($outlet['Outlet']['mobile']).'</td>
						<td>'. h($outlet['Market']['name']).'</td>
						<td>'. h($outlet['Territory']['name']).'</td>
						<td>'. h($outlet['Office']['office_name']).'</td>
						<td>'. h($outlet['OutletCategory']['category_name']).'</td>
						<td>';
							
								if($outlet['Outlet']['is_pharma_type']==1){
									$table .=''. h('Yes').'';
								}elseif($outlet['Outlet']['is_pharma_type']==0){
									$table .=''. h('No').'';
								}
							
						$table .='</td>';
						$table .='<td>';
							
								if($outlet['Outlet']['is_ngo']==1){
									$table .=''. h('Yes').'';
								}elseif($outlet['Outlet']['is_ngo']==0){
									$table .=''. h('No').'';
								}
							
						$table .='</td>';
						$table .='<td> '. h($outlet['Institute']['name']).'</td>
						<td>';
							
								if($outlet['Outlet']['bonus_type_id'] == 1){
									$table .=''. h('Small Bonus').'';
								}elseif ($outlet['Outlet']['bonus_type_id'] == 2) {
									$table .=''. h('Big Bonus').'';
								}else{
									$table .=''. h('Not Applicable').'';
								} 
							
						$table .='</td>';
						$table .='<td>';
							
								if($outlet['Outlet']['is_active'] == 1){
									
									$table .=''. h('Active').'';
								}else{
									
									$table .=''. h('In-Active').'';
								} 
							
						$table .='</td>
						
					</tr>';
					endforeach; 
					$table .='</tbody>
				</table>';

				header("Content-Type: application/vnd.ms-excel");
				header('Content-Disposition: attachment; filename="Outlets.xls"');
				header("Cache-Control: ");
				header("Pragma: ");
				header("Expires: 0");
				echo $table;
				$this->autoRender = false;
		
		
			
		//$this->set(compact('categories', 'offices', 'territories', 'markets', 'thanas','bonus_types'));		
	}
}
