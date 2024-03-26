<?php
App::uses('AppController', 'Controller');
/**
 * ProductSettings Controller
 *
 * @property OutletGroup $OutletGroup
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistOutletGroupsController extends AppController {

/**
 * Components
 *
 * @var array
 */
 	public $uses = array('OutletGroup', 'Thana', 'Market', 'SalesPerson', 'Outlet', 'Product', 'OutletGroupToOutlet');
	public $components = array('Paginator', 'Session','Filter.Filter');
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index($id = null) {
		
		if ($this->request->is('post') || $this->request->is('put')) {
			// for update data
			$data_array = array();
			foreach($this->request->data['product_setting_id'] as $key=>$val)
			{
				$update_data['OutletGroup']['id'] = $val;
				$update_data['OutletGroup']['sort'] = $this->request->data['sort'][$key];
				$data_array[] = $update_data;
			}	
			
			/*pr($data_array);
			exit;*/
										
			//$this->Product->saveAll($data_array); 	
			if($this->OutletGroup->saveMany($data_array)){			
				$this->Session->setFlash(__('The request has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));		
			}else{
				$this->Session->setFlash(__('Sort must be unique.'), 'flash/warning');
			}
		}
		
		$this->set('page_title', 'View List');
		$conditions = array();
		$this->paginate = array(	
			//'fields' => array('DISTINCT Combination.*'),		
			//'joins' => $joins,
			'conditions' => array(
				'OutletGroup.is_distributor' => 1
			),
			'limit' => 100,
			'order'=>   array('sort' => 'asc')   
		);
		//pr($this->paginate());
		//$this->set('product_id', $product_id);
		$this->set('results', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','View Details');
		if (!$this->OutletGroup->exists($id)) {
			throw new NotFoundException(__('Invalid request!'));
		}
		$options = array('conditions' => array('OutletGroup.' . $this->OutletGroup->primaryKey => $id));
		$this->set('productCombination', $this->OutletGroup->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add($id = null) 
	{
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
        
        /*$territory_id = isset($this->request->data['Memo']['territory_id']) != '' ? $this->request->data['Memo']['territory_id'] : 0;
        $market_id = isset($this->request->data['Memo']['market_id']) != '' ? $this->request->data['Memo']['market_id'] : 0;
		
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
				
		 $territories =$data_array;*/
		

			
		/*if($territory_id){
			$markets = $this->Memo->Market->find('list', array(
            'conditions' => array('Market.territory_id' => $territory_id),
            'order' => array('Market.name' => 'asc')
            ));
		}else{
			$markets = array();
		}
			
        $outlets = $this->Outlet->find('list', array(
            'conditions' => array('Outlet.market_id' => $market_id),
            'order' => array('Outlet.name' => 'asc')
            ));*/
		
		
		if ($this->request->is('post')) 
		{
			//pr($this->request->data);
			//exit;
			$this->request->data['OutletGroup']['created_at'] = $this->current_datetime(); 
			$this->request->data['OutletGroup']['updated_at'] = $this->current_datetime(); 
			$this->request->data['OutletGroup']['created_by'] = $this->UserAuth->getUserId();
			$this->request->data['OutletGroup']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['OutletGroup']['is_distributor'] = 1;

			$this->OutletGroup->create();
			if ($this->OutletGroup->save($this->request->data)) 
			{
				$outlet_group_id = $this->OutletGroup->getInsertID();


				if(!empty($this->request->data['OutletGroupToOutlet']))
				{
					$data_array = array();
					$data_array['OutletGroupToOutlet']['outlet_group_id'] = $outlet_group_id;

					$group_data = array();
					foreach($this->request->data['OutletGroupToOutlet']['outlet_id'] as $key=>$val){
						$data_array['OutletGroupToOutlet']['outlet_id'] = $val;
						$data_array['OutletGroupToOutlet']['is_distributor'] = 1;
						$data_array['OutletGroupToOutlet']['outlet_name'] = $this->request->data['OutletGroupToOutlet']['outlet_name'][$key];
						$group_data[] = $data_array;
					}
					$this->OutletGroupToOutlet->saveAll($group_data);
				
					$this->Session->setFlash(__('The request has been saved.'), 'flash/success');
					$this->redirect(array('action' => 'index'));
					
				
				}
			}
		}
		/*if ($this->request->is('post')) {
			
				if ($this->OutletGroup->save($this->request->data)) {
					$this->Session->setFlash(__('The data has been saved'), 'flash/success');
					$this->redirect(array('action' => 'index'));
					exit;
				}
			
		}*/
		
		
		
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
	   
	    $this->set('page_title','Edit Product Setting');
        $this->OutletGroup->id = $id;
		if (!$this->OutletGroup->exists($id)) {
			throw new NotFoundException(__('Invalid Setting'));
		}
	   
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
	   
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			//pr($this->request->data);
			//exit;
 
			$this->request->data['OutletGroup']['updated_at'] = $this->current_datetime(); 
			$this->request->data['OutletGroup']['updated_by'] = $this->UserAuth->getUserId();

			//$this->OutletGroup->create();
			if ($this->OutletGroup->save($this->request->data)) 
			{
				$outlet_group_id = $id;

				/*--------- end add campaine project ---------*/
				if(!empty($this->request->data['OutletGroupToOutlet']))
				{
					$this->OutletGroupToOutlet->deleteAll(array('OutletGroupToOutlet.outlet_group_id'=>$id));
					
					$data_array = array();
					$data_array['OutletGroupToOutlet']['outlet_group_id'] = $outlet_group_id;

					$group_data = array();
					foreach($this->request->data['OutletGroupToOutlet']['outlet_id'] as $key=>$val){
						$data_array['OutletGroupToOutlet']['outlet_id'] = $val;
						$data_array['OutletGroupToOutlet']['is_distributor'] = 1;
						$data_array['OutletGroupToOutlet']['outlet_name'] = $this->request->data['OutletGroupToOutlet']['outlet_name'][$key];
						$group_data[] = $data_array;
					}
					$this->OutletGroupToOutlet->saveAll($group_data);
				
					$this->Session->setFlash(__('The request has been update successfully.'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				}
			}
			else 
			{
				$this->Session->setFlash(__('The request could not be update. Please, try again.'), 'flash/error');
			}
			
			
			
		} 
		else 
		{
			$options = array('conditions' => array('OutletGroup.' . $this->OutletGroup->primaryKey => $id));
			$this->request->data = $this->OutletGroup->find('first', $options);
			//pr($this->request->data);
		}
				
		$this->set(compact('products'));
		
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
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->OutletGroup->id = $id;
		if (!$this->OutletGroup->exists()) {
			throw new NotFoundException(__('Invalid Data!'));
		}
		
		if ($this->OutletGroup->delete())
		{
			$this->OutletGroupToOutlet->deleteAll(array('OutletGroupToOutlet.outlet_group_id'=>$id));
			$this->Session->setFlash(__('Deleted successfully!'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('List was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	
	
	public function get_outlet_list(){
		$this->loadModel('Territory');
		$this->loadModel('DistOutlet');
		$office_id = $this->request->data['office_id'];
		$dist_distributor_id = $this->request->data['dist_distributor_id'];
		$dist_route_id = $this->request->data['dist_route_id'];
		
		$conditions=array();



		if($dist_route_id)
		{
			$conditions['DistOutlet.dist_route_id'] = $dist_route_id;
		}
		$conditions['DistOutlet.is_active'] = 1;
		//$conditions['Outlet.is_ngo'] = 1;
		

		
		
		
		$outlet_list = $this->DistOutlet->find('all',array(
			'conditions' => $conditions,
			'joins' => array(	
					array(
					'alias' => 'DistMarket',
					'table' => 'dist_markets',
					'type' => 'INNER',
					'conditions' => 'DistMarket.id = DistOutlet.dist_market_id'
					),		
					array(
					'alias' => 'DistRoute',
					'table' => 'dist_routes',
					'type' => 'INNER',
					'conditions' => 'DistRoute.id = DistOutlet.dist_route_id'
					),
					array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'Office.id = DistRoute.office_id'
					),
			),
			'recursive' => -1,
			'order' => array('DistOutlet.name' => 'asc'),
			'fields' => array('Office.office_name','DistOutlet.id','DistOutlet.name','DistRoute.name','DistMarket.name'),
			'group' => array('Office.office_name','DistOutlet.id','DistOutlet.name','DistRoute.name','DistMarket.name')
		));
		
		/*echo $this->Territory->getLastQuery();
		pr($outlet_list);exit;*/
		
		$data_array = Set::extract($outlet_list, '{n}.DistOutlet');
		$html = '';
		$outlet_other_info=array();
		if(!empty($outlet_list)){
			foreach($outlet_list as $data)
			{
				$outlet_other_info[$data['DistOutlet']['id']]=array(
					'office'=>$data['Office']['office_name'],
					//'territory'=>$data['Territory']['name'],
					'territory'=>$data['DistRoute']['name'],
					'market'=>$data['DistMarket']['name']
					);
			}
			$html .= '<option value="all">---- All ----</option>'; 
			foreach($data_array as $key=>$val){
				$html .= '<option value="'.$val['id'].'">'.$val['name'].'</option>';
			}
		}else{
			$html .= '<option value="">---- All ----</option>'; 
		}
		$json_array['outlet_html']=$html;
		$json_array['other_info']=$outlet_other_info;
		//echo $html;
		echo json_encode($json_array);
		$this->autoRender = false;
		
	}
	
	function get_outlet_info($outlet_id)
	{
		$this->loadModel('DistOutlet');
		$outlet_info=$this->DistOutlet->find('first',array(
			'conditions'=>array('DistOutlet.id'=>$outlet_id),
			'joins'=>array(
				array(
					'alias' => 'DistMarket',
					'table' => 'dist_markets',
					'type' => 'INNER',
					'conditions' => 'DistMarket.id = DistOutlet.dist_market_id'
					),		
					array(
					'alias' => 'DistRoute',
					'table' => 'dist_routes',
					'type' => 'INNER',
					'conditions' => 'DistRoute.id = DistOutlet.dist_route_id'
					),
					array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'Office.id = DistRoute.office_id'
					),
				),
			'fields'=>array(
					'DistOutlet.*',
					'DistMarket.name',
					'DistRoute.name',
					'Office.office_name'),
			'recursive'=>-1
			));
		return $outlet_info;
	}
	

	public function admin_get_route_list_by_distributor () {
        
        $office_id = $this->request->data['office_id'];
        $dist_distributor_id = $this->request->data['distributor_id'];
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistRoute');
        
        //route list ot this distrinutor
        $dist_route_list = $this->DistRouteMapping->find('all', array(
            'conditions' => array(
                'DistRouteMapping.office_id' => $office_id,
                'DistRouteMapping.dist_distributor_id' => $dist_distributor_id,
                //'DistRoute.is_active' => 1,
            ),
            'fields' => array('DistRouteMapping.dist_route_id'),
            'recursive' => -1
        ));
        
        $dist_route_ids = array();
        foreach ($dist_route_list as $k => $v) {
            $dist_route_ids[]=$v['DistRouteMapping']['dist_route_id'];
        }
        //pr($dist_route_ids);
        
        //route list out of this distrinutor
        $distRouteMappings_except = $this->DistRouteMapping->find('all', array(
            'conditions' => array(
                'DistRouteMapping.office_id' => $office_id,
                'DistRouteMapping.dist_distributor_id !=' => $dist_distributor_id
            ),
            'fields' => array('DistRouteMapping.dist_route_id'),
            'recursive' => -1
        ));
        
        $route_except_ids=array();
        foreach ($distRouteMappings_except as $k => $v) {
            $route_except_ids[]=$v['DistRouteMapping']['dist_route_id'];
        }
        //pr($route_except_ids);exit;
        
        //pr($route_except_ids);exit;
        $this->DistRoute->virtualFields = array(
            "route_with_thana" => "CONCAT(DistRoute.name, ' (', Thana.name,')')"
        );
        $distRoutes = $this->DistRoute->find('list', array(
          'conditions' => array(
                'DistRoute.office_id' => $office_id,
                'DistRoute.is_active' => 1,
                'NOT'=>array('DistRoute.id'=>$route_except_ids)
            ),
            'joins'=>array(
                array(
                    'table'=>'thanas',
                    'alias'=>'Thana',
                    'conditions'=>'Thana.id=DistRoute.thana_id',
                    'type'=>'LEFT'
                    ),
                ),
            'fields'=>array('route_with_thana'),
            'order'=>array('DistRoute.name')
        ));



        //pr($distRoutes);die();
        if(!$dist_distributor_id)$distRoutes=array();
        /*if($distRoutes)
        {
        $output = '<div class="input select"><input type="hidden" name="data[DistSrRouteMaping][route_id]" value="" id="route_id"/>';
            foreach($distRoutes as $key => $val)
            {
                if(in_array($key, $dist_route_ids)){
                    $output.= '<div class="checkbox">
                    <input checked type="checkbox" name="data[DistSrRouteMaping][route_id][]" value="'.$key.'" id="route_id'.$key.'" onclick="getCheckedroutes('.$key.')" /><label for="route_id'.$key.'">'.$val.'</label></div>';
                }
                else
                {
                    $output.= '<div class="checkbox">
                    <input type="checkbox" name="data[DistSrRouteMaping][route_id][]" value="'.$key.'" id="route_id'.$key.'" onclick="getCheckedroutes('.$key.')" /><label for="route_id'.$key.'">'.$val.'</label></div>';
                }
            }
            $output.='</div>';

            echo $output;
        }
        else
        {
            echo '';
        }*/
		
		$output = "<option value=''>--- Select ---</option>";
        if ($distRoutes) {

			foreach($distRoutes as $key => $val)
            {
				$output .= "<option value='$key'>$val</option>";
			}
            
        }
        echo $output;
        $this->autoRender = false;
    }
	
}
