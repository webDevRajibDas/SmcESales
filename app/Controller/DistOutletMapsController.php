<?php

App::uses('AppController', 'Controller');

/**
 * Territories Controller
 *
 * @property Territory $Territory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistOutletMapsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Session', 'Filter.Filter');
    public $uses = array('DistNotification','DistOutletMap');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index() {
        $this->set('page_title', 'Distributor and Outlet Mapping List');
        $this->loadModel('DistDistributor');
        
        $this->loadModel('User');
        $this->loadModel('Office');
        
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id'=>2);
        } else {
            $conditions = array('Office.office_type_id'=>2,'Office.id' => $this->UserAuth->getOfficeId());
        }
        
        /* Load Office List */       
        $offices = $this->Office->find('list',array('conditions'=>$conditions));
        $this->set(compact('offices'));
    }

    public function get_distributor_list() {
        $this->loadModel('DistDistributor');
        $this->loadModel('Office');
        $this->loadModel('User');
        $this->loadModel('Outlet');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $office_id=$this->request->data("office_id");

        $this->loadModel('DistAreaExecutive');
            if($user_group_id == 1029 || $user_group_id == 1028){
                if($user_group_id == 1028){
                    $dist_ae_info = $this->DistAreaExecutive->find('first',array(
                        'conditions'=>array('DistAreaExecutive.user_id'=>$user_id),
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
                        'conditions'=>array('DistTso.user_id'=>$user_id),
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
            $dist_conditions = array('DistDistributor.id'=>array_keys($tso_dist_list));
        }
        elseif($user_group_id == 1034){
            $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
            $this->loadModel('DistUserMapping');
            $distributor = $this->DistUserMapping->find('first',array(
                'conditions'=>array('DistUserMapping.sales_person_id'=>$sales_people_id),
            ));
            $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
            $dist_conditions = array( 'DistDistributor.is_active'=>1,'DistDistributor.id' =>$distributor_id);

        }
        else{
            $dist_conditions = array('DistDistributor.office_id' => $office_id,'DistDistributor.is_active' => 1);
        }
        
         $distDistributors = $this->DistDistributor->find('all', array(
            'conditions' => $dist_conditions,			
			'order' => 'DistDistributor.name asc',
			'recursive' =>0
        ));
         
        /* 
        $outlets = $this->Outlet->find('all', array(
            'conditions' => array('Outlet.category_id' => 17),			
			'recursive' =>-1
        ));
         * 
         */
         
         $sql="select o.id,o.name,t.name as territory_name,t.id as territory_id from outlets o left join markets m on m.id=o.market_id
left join territories t on t.id=m.territory_id where o.category_id=17";
         
         $outlet_raw=$this->Outlet->query($sql);
         $outlets=array();
         
         foreach ($outlet_raw as $key => $value) {
           $id=$value[0]['id'];
           $outlets[$id]['id']=$id;  
           $outlets[$id]['name']=$value[0]['name'];
           $outlets[$id]['territory_name']=$value[0]['territory_name'];
           $outlets[$id]['territory_id']=$value[0]['territory_id'];
         }
         
             
        $this->set(compact('distDistributors','outlets'));
    }
    
    /* Mapping Distributor to Outlets */
    public function admin_mapping($param_office_id=0,$param_dist_distributor_id=0,$param_id=0) 
	{
        $this->set('page_title', 'Mapping Distributor to Outlets');
        $this->loadModel('Market');
        $this->loadModel('Outlet');
        
        $office_id="";
        $dist_distributor_id="";
        $exist_data=array();
        
        
        
        if($this->request->is('post') && ($this->request->data("save")!="") && ($this->request->data("save")=="Save & Submit"))
        {
            $mapping_data=$this->request->data("DistOutletMaps");
            $office_id=$mapping_data["office_id"];
            $dist_distributor_id=$mapping_data["dist_distributor_id"];
            $id=(array_key_exists('id', $mapping_data))?$mapping_data['id']:"";
            
            $dist_outlet_map_id= (array_key_exists('id', $this->request->data['DistOutletMaps']))?$this->request->data['DistOutletMaps']['id']:"";
            
            $this->request->data['DistOutletMaps']['is_active'] = 1;
            $this->request->data['DistOutletMaps']['updated_by'] = $this->UserAuth->getUserId();           
            $this->request->data['DistOutletMaps']['updated_at'] = $this->current_datetime();
            
            
            if($dist_outlet_map_id)
            {
             $this->request->data['DistOutletMaps']['id']=$dist_outlet_map_id;
            }
            else 
            {                  
            $this->request->data['DistOutletMaps']['created_at'] = $this->current_datetime();
            $this->request->data['DistOutletMaps']['created_by'] = $this->UserAuth->getUserId();       
            }
            
            $redirect_con=($this->request->data['DistOutletMaps']['re_url'])?$this->request->data['DistOutletMaps']['re_url']:"dist_outlet_maps";
            
            unset($this->request->data['DistOutletMaps']['re_url']);
            
                    if ($this->DistOutletMap->saveAll($this->request->data['DistOutletMaps'])) {
                      
                         $this->Session->setFlash(__('Outlet has been mapped to Distributor successfully'), 'flash/success');
                         $this->redirect(array('controller'=>$redirect_con,'action' => 'index'));
                    }
                    else 
                    {
                         $this->Session->setFlash(__('Outlet has not been mapped to Distributor successfully'), 'flash/error');
                         $this->redirect(array('controller'=>$redirect_con,'action' => 'index'));
                    }
             
            
  
        }
        else 
        {
            if($this->request->is('post')  && ($this->request->data("DistOutletMap")!=""))
            {
                $DistOutletMap_data=$this->request->data("DistOutletMap");
                $office_id=$DistOutletMap_data["office_id"];
                $dist_distributor_id=$DistOutletMap_data["dist_distributor_id"];
                $id=(array_key_exists('id', $DistOutletMap_data))?$DistOutletMap_data['id']:"";
            }
        }
       
        
        /************************Fetching data from URL start ******************/
        $re_url="";
        if(!$office_id)
        {
            $office_id=$param_office_id;
            $dist_distributor_id=$param_dist_distributor_id;
            $id=($param_id)?$param_id:"";
            $re_url="DistDistributors";
        }
        
        
        /************************Fetching data from URL End ******************/
        
        
     
        if(!$office_id)
        {
            $this->Session->setFlash(__('Office ID required'), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }
         $office_conditions = array('Office.id' => $office_id);
               
        if(!$dist_distributor_id)
        {
            $this->Session->setFlash(__('Distributor ID required'), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }
        
        
  
        $this->set('office_id', $office_id);
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
        
       
        

        $territory_id = 0;
        $market_id = 0;
        $outlet_id = 0;
        $markets=array();
        $outlets = array();
     
        if ($this->request->is('post')) {
            /*
            $office_id = isset($this->request->data['Memo']['office_id']) != '' ? $this->request->data['Memo']['office_id'] : 0;
            $territory_id = isset($this->request->data['Memo']['territory_id']) != '' ? $this->request->data['Memo']['territory_id'] : 0;
            $market_id = isset($this->request->data['Memo']['market_id']) != '' ? $this->request->data['Memo']['market_id'] : '';

            $this->loadModel('Store');
            $this->Store->recursive = -1;
            $store_info = $this->Store->find('first',array(
                'conditions' => array('territory_id' => $territory_id)
                ));
            $store_id = $store_info['Store']['id'];
             */
        } 


		
        $this->loadModel('Territory');
		
        $territories_list = $this->Territory->find('all', array(
            'conditions' => array('Territory.office_id' => $office_id),
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
       
		
		$territories = array();
		foreach($territories_list as $t_result){
			$territories[$t_result['Territory']['id']]= $t_result['Territory']['name'].' ('.$t_result['SalesPerson']['name'].')';
		}
              
          
                  
        if($id)
        {
            $exist_data = $this->DistOutletMap->find('first', array('conditions' => array('DistOutletMap.id'=>$id)));   
            $territory_id=$exist_data['DistOutletMap']['territory_id'];
            $markets = $this->Market->find('list', array(
                        'conditions' => array(
                                'Market.territory_id' => $territory_id				
                                ),
                        'order' => array('Market.name'=>'asc'),
                        'recursive' => -1
                        ));
            $market_id=$exist_data['DistOutletMap']['market_id'];
            
            $outlets = $this->Outlet->find('list', array(
                        'conditions' => array(
                                'Outlet.market_id' => $market_id,
                                'Outlet.category_id' => 17
                                ),
                        'order' => array('Outlet.name'=>'asc'),
                        'recursive' => -1
                        ));
            
            $outlet_id=$exist_data['DistOutletMap']['outlet_id'];
            
        }         
                  
        $this->set(compact('offices','office_id','territories','territory_id','markets','market_id','dist_distributor_id','outlets','outlet_id','id','exist_data','re_url'));
    }
    
    
    /* fetch outlet data */
    public function get_outlet()
        {				
                $this->loadModel('Outlet');
                $rs = array(array('id' => '', 'name' => '---- Select Outlet -----'));
                $market_id = $this->request->data['market_id'];
                $outlet_list = $this->Outlet->find('all', array(
                        'fields' => array('Outlet.id as id','Outlet.name as name'),
                        'conditions' => array(
                                'Outlet.market_id' => $market_id,
                                'Outlet.category_id' => 17
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
        
        public function get_market()
        {				
                $this->loadModel('Market');
                $territory_id = $this->request->data['territory_id'];
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

}
