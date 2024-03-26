<?php
App::uses('AppController', 'Controller');
/**
 * SpecialGroups Controller
 *
 * @property SpecialGroup $SpecialGroup
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class SrSpecialGroupsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session');
	public $uses=array('SpecialGroup','OutletGroup','DistOutletCategory','SpecialGroupOtherSetting');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','Special group List');
		$this->SpecialGroup->recursive = 0;
		$this->paginate = array('order' => array('SpecialGroup.id' => 'DESC'),'conditions'=>array('SpecialGroup.is_dist'=>1));
		$this->set('specialGroups', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Special group Details');
		if (!$this->SpecialGroup->exists($id)) {
			throw new NotFoundException(__('Invalid special group'));
		}

		$options = array('conditions' => array('SpecialGroup.' . $this->SpecialGroup->primaryKey => $id));
		$this->request->data = $this->SpecialGroup->find('first', $options);
		$office_ids=$this->SpecialGroupOtherSetting->find('list',array(
			'conditions'=>array(
				'special_group_id'=>$id,
				'create_for'=>1
				),
			'fields'=>array('reffrence_id'),
			'recursive'=>-1
			)
		);
		$office_ids=array_values($office_ids);
		$territory_ids=$this->SpecialGroupOtherSetting->find('all',array(
			'conditions'=>array(
				'special_group_id'=>$id,
				'create_for'=>2
				),
			'joins'=>array(
				array(
						'table'=>'territories',
						'alias'=>'Territory',
						'conditions'=>'SpecialGroupOtherSetting.reffrence_id=Territory.id'
					),
				array(
						'table'=>'offices',
						'alias'=>'Office',
						'conditions'=>'Office.id=Territory.office_id'
					)
				),
			'fields'=>array('Territory.id','Territory.name','Office.office_name'),
			'group'=>array('Territory.id','Territory.name','Office.office_name','SpecialGroupOtherSetting.id'),
			'order'=>array('SpecialGroupOtherSetting.id'),
			'recursive'=>-1
			)
		);
		$outlet_category_id=$this->SpecialGroupOtherSetting->find('list',array(
			'conditions'=>array(
				'special_group_id'=>$id,
				'create_for'=>3
				),
			'fields'=>array('reffrence_id'),
			'recursive'=>-1
			)
		);
		$outlet_category_id=array_values($outlet_category_id);

		$outlet_group_id=$this->SpecialGroupOtherSetting->find('list',array(
			'conditions'=>array(
				'special_group_id'=>$id,
				'create_for'=>4
				),
			'fields'=>array('reffrence_id'),
			'recursive'=>-1
			)
		);
		$outlet_group_id=array_values($outlet_group_id);
		$this->set(compact('office_ids','territory_ids','outlet_category_id','outlet_group_id'));
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		$this->set('office_parent_id', $office_parent_id);
        if ($office_parent_id == 0) {
            $office_conditions = array(
			'office_type_id' => 2,
			"NOT" => array( "id" => array(30, 31, 37))
			);
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }
		$this->set('office_id', $this->UserAuth->getOfficeId());
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		$this->set(compact('offices'));
		$outlet_categories = $this->DistOutletCategory->find('list',array('conditions'=>array('is_active'=>1)));
		$this->set(compact('outlet_categories'));
		$outlet_groups = array();
		$o_con =array('OutletGroup.is_distributor' => 1);
		$outlet_groups = $this->OutletGroup->find('list', array('conditions' => $o_con, 'order' => array('name' => 'asc')));
		$this->set(compact('outlet_groups'));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Special group');
		if ($this->request->is('post')) {
			pr($this->request->data);
			$this->SpecialGroup->create();
			$this->request->data['SpecialGroup']['start_date'] = date('Y-m-d',strtotime($this->request->data['SpecialGroup']['start_date']));
			$this->request->data['SpecialGroup']['end_date'] = date('Y-m-d',strtotime($this->request->data['SpecialGroup']['end_date']));
			$this->request->data['SpecialGroup']['is_dist'] =1;
			$this->request->data['SpecialGroup']['created_at'] = $this->current_datetime();
			$this->request->data['SpecialGroup']['created_by'] = $this->UserAuth->getUserId();
			$this->request->data['SpecialGroup']['updated_at'] = $this->current_datetime();
			$this->request->data['SpecialGroup']['updated_by'] = $this->UserAuth->getUserId();
			$datasource = $this->SpecialGroup->getDataSource();
			try 
			{
			    $datasource->begin();
			    if(!$this->SpecialGroup->save($this->request->data['SpecialGroup']))
			    {
			    	$this->SpecialGroup->getLastQuery();
			    	throw new Exception();
			    }    
			    else
			    {
			    	$special_group_id=$this->SpecialGroup->getInsertID();
			    	if(!empty($this->request->data['SpecialGroup']['office_id']))
			    	{
			    		$data['special_group_id']=$special_group_id;
			    		$data['create_for']=1; //1=Office
			    		foreach($this->request->data['SpecialGroup']['office_id'] as $office_id)
			    		{
			    			$data['reffrence_id']=$office_id;
			    			$this->SpecialGroupOtherSetting->create();
			    			
			    			if(!$this->SpecialGroupOtherSetting->save($data))
						    {
						    	throw new Exception();
						    }  
			    		}
			    	}
			    
			    	if(!empty($this->request->data['SpecialGroup']['outlet_category_id']))
			    	{
			    		$data['special_group_id']=$special_group_id;
			    		$data['create_for']=3; //3=outlet category id
			    		foreach($this->request->data['SpecialGroup']['outlet_category_id'] as $category_id)
			    		{
			    			$data['reffrence_id']=$category_id;
			    			$this->SpecialGroupOtherSetting->create();
			    			if(!$this->SpecialGroupOtherSetting->save($data))
						    {
						    	throw new Exception();
						    }  
			    		}
			    	}
			    	if(!empty($this->request->data['SpecialGroup']['outlet_group_id']))
			    	{
			    		$data['special_group_id']=$special_group_id;
			    		$data['create_for']=4; //4=outlet GROUP id
			    		foreach($this->request->data['SpecialGroup']['outlet_group_id'] as $group_id)
			    		{
			    			$data['reffrence_id']=$group_id;
			    			$this->SpecialGroupOtherSetting->create();
			    			if(!$this->SpecialGroupOtherSetting->save($data))
						    {
						    	throw new Exception();
						    }  
			    		}
			    	}
			    }
			    $datasource->commit();
			}
			catch(Exception $e) 
			{
			    $datasource->rollback();
			    $this->Session->setFlash(__('SomeThing Went Wrong! Please Try Again.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}
			$this->Session->setFlash(__('The special group has been saved'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->set('page_title','Add New');
		
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		$this->set('office_parent_id', $office_parent_id);
        if ($office_parent_id == 0) {
            $office_conditions = array(
			'office_type_id' => 2,
			"NOT" => array( "id" => array(30, 31, 37))
			);
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }
		$this->set('office_id', $this->UserAuth->getOfficeId());
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		$this->set(compact('offices'));
		$outlet_categories = $this->DistOutletCategory->find('list',array('conditions'=>array('is_active'=>1)));
		$this->set(compact('outlet_categories'));
		$outlet_groups = array();
		$o_con =array('OutletGroup.is_distributor' => 1);
		$outlet_groups = $this->OutletGroup->find('list', array('conditions' => $o_con, 'order' => array('name' => 'asc')));
		$this->set(compact('outlet_groups'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
        $this->set('page_title','Edit Special group');
		$this->SpecialGroup->id = $id;
		if (!$this->SpecialGroup->exists($id)) {
			throw new NotFoundException(__('Invalid special group'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['SpecialGroup']['start_date'] = date('Y-m-d',strtotime($this->request->data['SpecialGroup']['start_date']));
			$this->request->data['SpecialGroup']['end_date'] = date('Y-m-d',strtotime($this->request->data['SpecialGroup']['end_date']));
			$this->request->data['SpecialGroup']['is_dist'] =1;
			$this->request->data['SpecialGroup']['updated_at'] = $this->current_datetime();
			$this->request->data['SpecialGroup']['updated_by'] = $this->UserAuth->getUserId();
			$datasource = $this->SpecialGroup->getDataSource();
			try 
			{
			    $datasource->begin();
			    if(!$this->SpecialGroup->save($this->request->data['SpecialGroup']))
			    {
			    	throw new Exception();
			    }    
			    else
			    {
			    	$special_group_id=$id;
			    	if(!$this->SpecialGroupOtherSetting->deleteAll(array('SpecialGroupOtherSetting.special_group_id'=>$id)))
			    	{
			    		throw new Exception();
			    	}
			    	if(!empty($this->request->data['SpecialGroup']['office_id']))
			    	{
			    		$data['special_group_id']=$special_group_id;
			    		$data['create_for']=1; //1=Office
			    		foreach($this->request->data['SpecialGroup']['office_id'] as $office_id)
			    		{
			    			$data['reffrence_id']=$office_id;
			    			$this->SpecialGroupOtherSetting->create();
			    			
			    			if(!$this->SpecialGroupOtherSetting->save($data))
						    {
						    	throw new Exception();
						    }  
			    		}
			    	}
			    	if(!empty($this->request->data['SpecialGroup']['outlet_category_id']) )
			    	{
			    		$data['special_group_id']=$special_group_id;
			    		$data['create_for']=3; //2=outlet category id
			    		foreach($this->request->data['SpecialGroup']['outlet_category_id'] as $category_id)
			    		{
			    			$data['reffrence_id']=$category_id;
			    			$this->SpecialGroupOtherSetting->create();
			    			if(!$this->SpecialGroupOtherSetting->save($data))
						    {
						    	throw new Exception();
						    }  
			    		}
			    	}
			    	if(!empty($this->request->data['SpecialGroup']['outlet_group_id']) )
			    	{
			    		$data['special_group_id']=$special_group_id;
			    		$data['create_for']=4; //2=outlet group id
			    		foreach($this->request->data['SpecialGroup']['outlet_group_id'] as $group_id)
			    		{
			    			$data['reffrence_id']=$group_id;
			    			$this->SpecialGroupOtherSetting->create();
			    			if(!$this->SpecialGroupOtherSetting->save($data))
						    {
						    	throw new Exception();
						    }  
			    		}
			    	}
			    }
			    $datasource->commit();
			}
			catch(Exception $e) 
			{
			    $datasource->rollback();
			    $this->Session->setFlash(__('SomeThing Went Wrong! Please Try Again.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}
			$this->Session->setFlash(__('The special group has been saved'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		} 
		else 
		{
			$options = array('conditions' => array('SpecialGroup.' . $this->SpecialGroup->primaryKey => $id));
			$this->request->data = $this->SpecialGroup->find('first', $options);
			$this->request->data['SpecialGroup']['start_date'] = date('d-m-Y',strtotime($this->request->data['SpecialGroup']['start_date']));
			$this->request->data['SpecialGroup']['end_date'] = date('d-m-Y',strtotime($this->request->data['SpecialGroup']['end_date']));
			$office_ids=$this->SpecialGroupOtherSetting->find('list',array(
				'conditions'=>array(
					'special_group_id'=>$id,
					'create_for'=>1
					),
				'fields'=>array('reffrence_id'),
				'recursive'=>-1
				)
			);
			$office_ids=array_values($office_ids);
			$territory_ids=$this->SpecialGroupOtherSetting->find('all',array(
				'conditions'=>array(
					'special_group_id'=>$id,
					'create_for'=>2
					),
				'joins'=>array(
					array(
							'table'=>'territories',
							'alias'=>'Territory',
							'conditions'=>'SpecialGroupOtherSetting.reffrence_id=Territory.id'
						),
					array(
							'table'=>'offices',
							'alias'=>'Office',
							'conditions'=>'Office.id=Territory.office_id'
						)
					),
				'fields'=>array('Territory.id','Territory.name','Office.office_name'),
				'group'=>array('Territory.id','Territory.name','Office.office_name','SpecialGroupOtherSetting.id'),
				'order'=>array('SpecialGroupOtherSetting.id'),
				'recursive'=>-1
				)
			);
			$outlet_category_id=$this->SpecialGroupOtherSetting->find('list',array(
				'conditions'=>array(
					'special_group_id'=>$id,
					'create_for'=>3
					),
				'fields'=>array('reffrence_id'),
				'recursive'=>-1
				)
			);
			$outlet_category_id=array_values($outlet_category_id);

			$outlet_group_id=$this->SpecialGroupOtherSetting->find('list',array(
				'conditions'=>array(
					'special_group_id'=>$id,
					'create_for'=>4
					),
				'fields'=>array('reffrence_id'),
				'recursive'=>-1
				)
			);
			$outlet_group_id=array_values($outlet_group_id);
			$this->set(compact('office_ids','territory_ids','outlet_category_id','outlet_group_id'));
			$office_parent_id = $this->UserAuth->getOfficeParentId();
			$this->set('office_parent_id', $office_parent_id);
	        if ($office_parent_id == 0) {
	            $office_conditions = array(
				'office_type_id' => 2,
				"NOT" => array( "id" => array(30, 31, 37))
				);
	        } else {
	            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
	        }
			$this->set('office_id', $this->UserAuth->getOfficeId());
	        $this->loadModel('Office');
	        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
			$this->set(compact('offices'));
			$outlet_categories = $this->DistOutletCategory->find('list',array('conditions'=>array('is_active'=>1)));
			$this->set(compact('outlet_categories'));
			$outlet_groups = array();
			$o_con =array('OutletGroup.is_distributor' => 1);
			$outlet_groups = $this->OutletGroup->find('list', array('conditions' => $o_con, 'order' => array('name' => 'asc')));
			$this->set(compact('outlet_groups'));
		}
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
		$this->SpecialGroup->id = $id;
		if (!$this->SpecialGroup->exists()) {
			throw new NotFoundException(__('Invalid special group'));
		}
		if ($this->SpecialGroup->delete()) {
			$this->Session->setFlash(__('Special group deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Special group was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	public function get_territory_list(){
		$this->loadModel('Territory');
		$this->loadModel('Outlet');
		$office_id = $this->request->data['office_id'];
		$conditions=array();
		if($office_id)
		{
			$conditions['Territory.office_id'] = $office_id;
		}
		$conditions['Territory.name NOT LIKE']='%Corporate%';
		//$conditions['Outlet.is_ngo'] = 1;
		
		$this->Territory->unbindModel(
			array('hasMany' => array('Market','SaleTarget','SaleTargetMonth','TerritoryPerson'),'belongsTo'=>array('Office'))
		);
		
		
		
		$territory_list = $this->Territory->find('all',array(
			'conditions' => $conditions,
			'joins' => array(
				
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'Office.id = Territory.office_id'
					)
			),
			'order' => array('Territory.name' => 'asc'),
			'fields' => array('Office.office_name','Territory.name','Territory.id'),
			'group' => array('Office.office_name','Territory.name','Territory.id')
		));
		
		$data_array = Set::extract($territory_list, '{n}.Territory');
		$html = '';
		$outlet_other_info=array();
		if(!empty($territory_list)){
			foreach($territory_list as $data)
			{
				$territory_other_info[$data['Territory']['id']]=array(
					'office'=>$data['Office']['office_name'],
					'territory'=>$data['Territory']['name'],
					);
			}
			$html .= '<option value="all">---- All ----</option>'; 
			foreach($data_array as $key=>$val)
			{
				$html .= '<option value="'.$val['id'].'">'.$val['name'].'</option>';
			}
		}else{
			$html .= '<option value="">---- All ----</option>'; 
		}
		$json_array['territory_html']=$html;
		$json_array['other_info']=$territory_other_info;
		// echo $html;
		echo json_encode($json_array);
		$this->autoRender = false;
		
	}
}
