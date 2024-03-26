<?php

App::uses('AppController', 'Controller');

/**
 * Territories Controller
 *
 * @property Territory $Territory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistSrRouteMapingsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Session', 'Filter.Filter');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index() 
    {
        $this->set('page_title', 'Distributor SR Route/Beat Maping');

        $this->loadModel('Office');
        
        $this->loadModel('DistDistributor');
        
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistRouteMappingHistory');
        
        $this->loadModel('DistSrRouteMapping');
        $this->loadModel('DistSrRouteMappingHistory');
        
        $this->loadModel('DistSalesRepresentative');
        
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistAreaExecutive');
        
        
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' =>2);
            $distributor_conditions = array('is_active'=> 1);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(),'Office.office_type_id' =>2);
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
                
                $distributor_conditions = array( 'is_active'=>1,'id' => array_keys($tso_dist_list));
            }
            elseif($user_group_id == 1034){
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first',array(
                    'conditions'=>array('DistUserMapping.sales_person_id'=>$sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
                $distributor_conditions = array( 'is_active'=>1,'id' =>$distributor_id);

            }
            else{
                $distributor_conditions = array('is_active'=>1,'office_id' => $this->UserAuth->getOfficeId());
            }
            //$distributor_conditions = array('office_id'=>$this->UserAuth->getOfficeId(),'is_active'=> 1);
        }

        if($this->request->is('post'))
        {
            $office_id = $this->request->data['DistDistributor']['office_id'];
            $distributor_id = $dist_distributor_id = $this->request->data['DistDistributor']['distributor_id'];
            
            //for Dist SR update
            $update_sr_qry="update dist_sales_representatives set dist_distributor_id=NULL where office_id = $office_id and dist_distributor_id=$dist_distributor_id";
            $this->DistSalesRepresentative->query($update_sr_qry);
            $dist_sr_assign = $this->request->data['DistSrRouteMaping']['sr_id'];
            $sr_data = array();
            $sr_data_array = array();
            foreach($dist_sr_assign as $sr_id)
            {
                $sr_data['id'] = $sr_id;
                $sr_data['dist_distributor_id'] = $dist_distributor_id;
                $sr_data['updated_at'] = $this->current_datetime();
                $sr_data['updated_by'] = $this->UserAuth->getUserId();
                $sr_data_array[] = $sr_data;
                
            }
            $this->DistSalesRepresentative->saveAll($sr_data_array);


            $effective_date = date('Y-m-d');
            //for distributor route mapping
            $dist_route_maping = $this->request->data['DistSrRouteMaping']['route_id'];
            $this->DistRouteMapping->query("delete from dist_route_mappings where office_id = $office_id and dist_distributor_id = $dist_distributor_id");

            //$update_remove_route_history_qry="update dist_route_mapping_histories set active=0,is_assign=0,end_date='$effective_date' where office_id = $office_id and dist_distributor_id=$dist_distributor_id and dist_route_id = $dist_route_id and active=1 and is_assign=1 and end_date is NULL";
            $update_remove_route_history_qry="update dist_route_mapping_histories set active=0,is_assign=0,end_date='$effective_date' where office_id = $office_id and dist_distributor_id=$dist_distributor_id and active=1 and is_assign=1 and end_date is NULL";
            $this->DistRouteMappingHistory->query($update_remove_route_history_qry);

            $d_data = array();
            $d_data_array = array();
            $d_data_history_array = array();
            
            foreach($dist_route_maping as $dist_route_id)
            {
                //echo $dist_route_id.'<br>';
                                
                $d_data['office_id'] = $office_id;
                $d_data['dist_distributor_id'] = $distributor_id;
                $d_data['dist_route_id'] = $dist_route_id;
                $d_data['effective_date'] = $effective_date;
                
                $d_data['created_at'] = $this->current_datetime();
                $d_data['created_by'] = $this->UserAuth->getUserId();
                $d_data['updated_at'] = $this->current_datetime();
                $d_data['updated_by'] = $this->UserAuth->getUserId();
                
                $d_data_array[] = $d_data;
                

                $d_data['is_assign'] = 1;
                $d_data['active'] = 1;
                $d_data['is_change'] = 1;


                $d_data_history_array[] = $d_data;
                
            
            }
            $this->DistRouteMapping->create();
            $this->DistRouteMapping->saveAll($d_data_array);

            $this->DistRouteMappingHistory->create();
            $this->DistRouteMappingHistory->saveAll($d_data_history_array);
            //end for distributor route mapping
            //exit;
            $this->DistSrRouteMapping->query("delete from dist_sr_route_mappings where office_id = $office_id and dist_distributor_id = $dist_distributor_id");

            $update_remove_history_qry="update dist_sr_route_mapping_histories set active=0,is_assign=0,end_date='$effective_date' where office_id = $office_id and dist_distributor_id=$distributor_id and active=1 and is_assign=1 and end_date is NULL"; 
            $this->DistSrRouteMappingHistory->query($update_remove_history_qry);
            //for SR and DM route mapping
            $sr_dm_route_maping = $this->request->data['RouteMaping'];

            $r_data_array = array();
            $r_data_history_array = array();
            foreach($sr_dm_route_maping as $route_data)
            {
                $r_data = array();
                
                if($route_data['dist_sr_id'])
                {
                    /*if($route_data['id'])
                    {
                            $dist_route_id = $route_data['dist_route_id'];
                            $effective_date = date('Y-m-d');
                            
                            //$r_data['id'] = $route_data['id'];
                            $r_data['office_id'] = $office_id;
                            $r_data['dist_distributor_id'] = $distributor_id;
                            $r_data['dist_route_id'] = $route_data['dist_route_id'];
                            $r_data['dist_sr_id'] = $route_data['dist_sr_id'];
                            $r_data['dist_dm_id'] = $route_data['dist_dm_id'];
                            //$r_data['effective_date'] = $effective_date;
                            
                            $r_data['effective_date'] = date('Y-m-d');
                            $r_data['created_at'] = $this->current_datetime();
                            $r_data['created_by'] = $this->UserAuth->getUserId();

                            $r_data['updated_by'] = $this->UserAuth->getUserId();
                            $r_data['updated_at'] = $this->current_datetime();

                            $this->DistSrRouteMappingHistory->create();
                            $this->DistSrRouteMapping->save($r_data);
                            
                            //$update_remove_history_qry="update dist_sr_route_mapping_histories set active=0,is_assign=0,end_date='$effective_date' where office_id = $office_id and dist_distributor_id=$distributor_id and dist_route_id = $dist_route_id and active=1 and is_assign=1 and end_date is NULL"; 
                            $update_remove_history_qry="update dist_sr_route_mapping_histories set active=0,is_assign=0,end_date='$effective_date' where office_id = $office_id and dist_distributor_id=$distributor_id and active=1 and is_assign=1 and end_date is NULL"; 
                            $this->DistSrRouteMappingHistory->query($update_remove_history_qry);
                            
                            unset($r_data['id']);
                            $r_data['is_assign'] = 1;
                            $r_data['is_active'] = 1;
                            $r_data['is_change'] = 1;
                            $r_data['created_at'] = $this->current_datetime();
                            $r_data['created_by'] = $this->UserAuth->getUserId();
                            $this->DistSrRouteMappingHistory->create();
                            $this->DistSrRouteMappingHistory->save($r_data);
                        
                    }
                    else
                    {*/
                        $r_data['office_id'] = $office_id;
                        $r_data['dist_distributor_id'] = $distributor_id;
                        
                        $r_data['dist_route_id'] = $route_data['dist_route_id'];
                        $r_data['dist_sr_id'] = $route_data['dist_sr_id'];
                        $r_data['dist_dm_id'] = $route_data['dist_dm_id'];
                        
                        $r_data['effective_date'] = date('Y-m-d');
                        $r_data['created_at'] = $this->current_datetime();
                        $r_data['created_by'] = $this->UserAuth->getUserId();
                        $r_data['updated_at'] = $this->current_datetime();
                        $r_data['updated_by'] = $this->UserAuth->getUserId();
                        
                        $r_data_array[] = $r_data;                
                       
                        /*$update_remove_history_qry="update dist_sr_route_mapping_histories set active=0,is_assign=0,end_date='$effective_date' where office_id = $office_id and dist_distributor_id=$distributor_id and active=1 and is_assign=1 and end_date is NULL"; 
                        $this->DistSrRouteMappingHistory->query($update_remove_history_qry);*/
                        $r_data['is_assign'] = 1;
                        $r_data['is_active'] = 1;
                        $r_data['is_change'] = 1;
                       
                        $r_data_history_array[] = $r_data;
                        
                    //}
                }
                
            }

            if(!empty($r_data_array) && !empty($r_data_history_array)){
                $this->DistSrRouteMapping->saveAll($r_data_array);
                $this->DistSrRouteMappingHistory->saveAll($r_data_history_array);

                $this->Session->setFlash(__('SR and Route Mapping has been Completed.'), 'flash/success');
            }else{
                $this->Session->setFlash(__('Route Mapping has been Completed.'), 'flash/success');
            }           
           
            $this->redirect(array('controller' => 'DistSrRouteMapings', 'action' => 'index'));
            //end for SR and DM route mapping
            
        }

        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        $distributors = $this->DistDistributor->find('list',array('conditions'=>$distributor_conditions));
        $this->set(compact('offices','distributors'));
    }

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_view($id = null) {
        $this->set('page_title', 'Route/Beat Details');
        if (!$this->DistSrRouteMaping->exists($id)) {
            throw new NotFoundException(__('Invalid Route/Beat'));
        }
        $options = array('conditions' => array('DistSrRouteMaping.' . $this->DistSrRouteMaping->primaryKey => $id));
        $this->set('tso', $this->DistSrRouteMaping->find('first', $options));
    }

    /**
     * admin_add method
     *
     * @return void
     */
   
   


    public function admin_add() {
        $this->set('page_title', 'New Commission Rate');
        if ($this->request->is('post')) {

            //pr($this->request->data);die();
            $effective_date = $this->request->data['DistSrRouteMaping']['effective_date'];
            $this->request->data['DistSrRouteMaping']['effective_date'] = date('Y-m-d', strtotime($effective_date));
            $this->request->data['DistSrRouteMaping']['created_at'] = $this->current_datetime();
            $this->request->data['DistSrRouteMaping']['created_by'] = $this->UserAuth->getUserId();
            $this->request->data['DistSrRouteMaping']['updated_at'] = $this->current_datetime();
            $this->DistSrRouteMaping->create();
            if ($this->DistSrRouteMaping->save($this->request->data)) {
                $this->Session->setFlash(__('The Route/Beat has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Route/Beat could not be saved. Please, try again.'), 'flash/error');
            }
        }

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {

            $conditions = array('Office.office_type_id' =>2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(),'Office.office_type_id' =>2);
        }
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        $this->set(compact('offices'));
    }

    /**
     * admin_edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_edit($id = null) {
        $this->set('page_title', 'Edit Route/Beat');
        $this->DistSrRouteMaping->id = $id;
        if (!$this->DistSrRouteMaping->exists($id)) {
            throw new NotFoundException(__('Invalid Route/Beat'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['DistSrRouteMaping']['updated_by'] = $this->UserAuth->getUserId();
            if ($this->DistSrRouteMaping->save($this->request->data)) {
                $this->Session->setFlash(__('The Route/Beat has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $options = array('conditions' => array('DistSrRouteMaping.' . $this->DistSrRouteMaping->primaryKey => $id));
            $this->request->data = $this->DistSrRouteMaping->find('first', $options);

            $this->loadmodel('Territory');
            $this->loadmodel('ThanaTerritory');
            $office_id = $this->request->data['DistSrRouteMaping']['office_id'];
            $territory = $this->Territory->find('all', array(
                    'fields' => array('Territory.id'),
                    'conditions' => array('Territory.office_id' => $office_id),
                    'order' => array('Territory.name' => 'asc'),
                    'recursive' => 0
            ));
            $thanas = array();
            $territories = array();
            
            foreach($territory as $key => $value)
            {
                $territories[$key] = $value['Territory']['id'];
               
            }
            
            //pr($data_array);die();
           $thana_list = $this->ThanaTerritory->find('all',array('conditions'=>array(
            'ThanaTerritory.territory_id' => $territories,
           )));

           foreach($thana_list as $key => $value)
            {
                $thanas[$value['Thana']['id']] = $value['Thana']['name'];
            }

            

        }
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' =>2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(),'Office.office_type_id' =>2);
        }
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        $this->set(compact('offices','thanas'));
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
        
        $this->DistSrRouteMaping->id = $id;
        if (!$this->DistSrRouteMaping->exists()) {
            throw new NotFoundException(__('Invalid Route/Beat'));
        }
        
                /******************************* Check foreign key ****************************/
        
        if($this->DistSrRouteMaping->checkForeignKeys("dist_routes",$id))
        {
            $this->Session->setFlash(__('This Route/Beat has used in another'), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }
        
        
        if ($this->DistSrRouteMaping->delete()) {
            $this->Session->setFlash(__('Route/Beat deleted'), 'flash/success');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Route/Beat was not deleted'), 'flash/error');
        $this->redirect(array('action' => 'index'));
    }
    
    public function get_distributor_list_list(){
        
        $office_id = $this->request->data['office_id'];
        $rs = array(array('id' => '', 'name' => '---- Select -----'));
        $this->loadModel('DistDistributor');

        $distributor_info = $this->DistDistributor->find('all',array('conditions'=>array(
            'DistDistributor.office_id'=>$office_id, 'DistDistributor.is_active'=> 1),
       // 'order' =>array('DistDistributor.id DESC'),
        ));

        $data_array = array();

        foreach ($distributor_info as $key => $value) {
            $data_array[] = array(
                'id' => $value['DistDistributor']['id'],
                'name' =>$value['DistDistributor']['name'],
            );
        }

        if(!empty($distributor_info)){
            echo json_encode(array_merge($rs,$data_array));
        }
        else{
            echo json_encode($rs);
        }
        $this->autoRender = false;

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
        if($distRoutes)
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
        }
        

        $this->autoRender = false;
    }
    
    public function admin_get_sr_list_by_distributor_id() {
        
        $office_id = $this->request->data['office_id'];
        $dist_distributor_id = $this->request->data['distributor_id'];
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistSalesRepresentative');

        $distSalesRepresentatives = $this->DistSalesRepresentative->find('all', array(
        'conditions' => array(
                'DistSalesRepresentative.office_id' => $office_id, 
                'DistSalesRepresentative.is_active' => 1, 
                'OR' => array(
                        array('DistSalesRepresentative.dist_distributor_id' => $dist_distributor_id),
                        array('DistSalesRepresentative.dist_distributor_id' => NULL),
                    )
            ),
        'recursive' => -1,
        'fields' => array('DistSalesRepresentative.id', 'DistSalesRepresentative.name', 'DistSalesRepresentative.dist_distributor_id'),
        'order' => array('DistSalesRepresentative.name' => 'asc'),
        ));

        //pr($distSalesRepresentatives);die();
        
        if(!$dist_distributor_id)$distSalesRepresentatives=array();
        
        if($distSalesRepresentatives)
        {
            $output = '<div class="input select"><input type="hidden" name="data[DistSrRouteMaping][sr_id]" value="" id="sr_id"/>';
            foreach($distSalesRepresentatives as $key => $val)
            {
                if($val['DistSalesRepresentative']['dist_distributor_id']==$dist_distributor_id)
                {
                    $output.= '<div class="checkbox">
                        <input checked type="checkbox" name="data[DistSrRouteMaping][sr_id][]" value="'.$val['DistSalesRepresentative']['id'].'" id="sr_id'.$val['DistSalesRepresentative']['name'].'" onclick="getCheckedsr('.$val['DistSalesRepresentative']['id'].')"/>
                        <label for="sr_id'.$val['DistSalesRepresentative']['id'].'">'.$val['DistSalesRepresentative']['name'].'</label>
                      </div>';
                }
                else
                {
                    $output.= '<div class="checkbox">
                        <input type="checkbox" name="data[DistSrRouteMaping][sr_id][]" value="'.$val['DistSalesRepresentative']['id'].'" id="sr_id'.$val['DistSalesRepresentative']['name'].'" onclick="getCheckedsr('.$val['DistSalesRepresentative']['id'].')"/>
                        <label for="sr_id'.$val['DistSalesRepresentative']['id'].'">'.$val['DistSalesRepresentative']['name'].'</label>
                      </div>';
                }
                
            }
            $output.='</div>';

            echo $output;
        }
        else
        {
            echo '';
        }
        

        $this->autoRender = false;
    }
    
    public function admin_get_dm_list_by_distributor_id() {
        
        $office_id = $this->request->data['office_id'];
        $dist_distributor_id = $this->request->data['distributor_id'];
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistDeliveryMan');

        $dist_delivery_mem = $this->DistDeliveryMan->find('all', array(
            'conditions' => array(
                'DistDeliveryMan.office_id' => $office_id, 
                'DistDeliveryMan.is_active' => 1, 
                'DistDeliveryMan.dist_distributor_id'=>$dist_distributor_id
            ),
            'fields' => array('DistDeliveryMan.id', 'DistDeliveryMan.name', 'DistDeliveryMan.dist_distributor_id')
        ));
        

        //pr($distRoutes);die();
        if($dist_delivery_mem)
        {
        $output = '<div class="input select"><input type="hidden" name="data[DistSrRouteMaping][dm_id]" value="" id="dm_id" />';
            foreach($dist_delivery_mem as $key => $val)
            {
                /*$output.= '<div class="checkbox">
                    <input type="checkbox" name="data[DistSrRouteMaping][dm_id][]" value="'.$key.'" id="dm_id'.$key.'" onclick="getCheckedDm('.$key.')"/>
                    <label for="dm_id'.$key.'">'.$val.'</label>
                </div>';*/
                  
                
                if($val['DistDeliveryMan']['dist_distributor_id']==$dist_distributor_id)
                {
                    $output.= '<div class="checkbox">
                        <input checked type="checkbox" name="data[DistSrRouteMaping][dm_id][]" value="'.$val['DistDeliveryMan']['id'].'" id="sr_id'.$val['DistDeliveryMan']['name'].'" onclick="getCheckedDm('.$val['DistDeliveryMan']['id'].')"/>
                        <label for="sr_id'.$val['DistDeliveryMan']['id'].'">'.$val['DistDeliveryMan']['name'].'</label>
                      </div>';
                }
                else
                {
                    $output.= '<div class="checkbox">
                        <input type="checkbox" name="data[DistSrRouteMaping][dm_id][]" value="'.$val['DistDeliveryMan']['id'].'" id="sr_id'.$val['DistDeliveryMan']['name'].'" onclick="getCheckedDm('.$val['DistDeliveryMan']['id'].')"/>
                        <label for="sr_id'.$val['DistDeliveryMan']['id'].'">'.$val['DistDeliveryMan']['name'].'</label>
                      </div>';
                }
                
            }
            $output.='</div>';

            echo $output;
        }
        else
        {
            echo '';
        }
        

        $this->autoRender = false;
    }
    
    
    public function admin_get_route_mapping_list() {
        
        $office_id = $this->request->data['office_id'];
        $dist_distributor_id = $this->request->data['distributor_id'];
        $this->loadModel('DistSrRouteMapping');
        //$this->loadModel('DistDmRouteMapping');
        
        
        $this->loadModel('DistSalesRepresentative');
        $dist_sr_list = $this->DistSalesRepresentative->find('list', array(
        'conditions' => array(
                'DistSalesRepresentative.office_id' => $office_id, 
                'DistSalesRepresentative.dist_distributor_id' => $dist_distributor_id,
                'DistSalesRepresentative.is_active' => 1,
            ),
        'order' => array('DistSalesRepresentative.name' => 'asc'),
        ));
        
        $this->loadModel('DistDeliveryMan');
        $dist_delivery_list = $this->DistDeliveryMan->find('list', array(
            'conditions' => array(
                'DistDeliveryMan.office_id' => $office_id, 
                'DistDeliveryMan.dist_distributor_id'=>$dist_distributor_id,
                'DistDeliveryMan.is_active' => 1,
            ),
            'order' => array('DistDeliveryMan.name' => 'asc'),
        ));
        
        
        
        $results = array();
        $sr_results = $this->DistSrRouteMapping->find('all', array(
            'conditions' => array(
                'DistSrRouteMapping.office_id' => $office_id, 
                'DistSrRouteMapping.dist_distributor_id'=>$dist_distributor_id,
                'DistRoute.is_active' => 1,
            ),
            'fields'=>array('DistSrRouteMapping.*','DistRoute.*','DistDeliveryMan.*','DistSalesRepresentative.*','Thana.name'),
            'joins'=>array(
            	array(
            		'table'=>'thanas',
            		'alias'=>'Thana',
            		'conditions'=>'Thana.id=DistRoute.thana_id',
            		'type'=>'LEFT'
            		),
            	),
        ));
    // pr($sr_results);exit;
        /*foreach($sr_results as $sr_result){
            $results[] = array(
                'id'                => $sr_result['DistSrRouteMapping']['id'],
                'dist_route_id'     => $sr_result['DistSrRouteMapping']['dist_route_id'],
                'dist_route_name'   => $sr_result['DistRoute']['name'],
                'dist_sr_id'        => $sr_result['DistSrRouteMapping']['dist_sr_id'],
                'dist_sr_name'      => $sr_result['DistSalesRepresentative']['name'],
                'dist_dm_id'        => $sr_result['DistSrRouteMapping']['dist_dm_id'],
                'dist_dm_name'      => $sr_result['DistDeliveryMan']['name'],
            );
        }*/
        
        //pr($results);die();
        

    
        $sr_route_id=array();
        foreach($sr_results as $sr_result){
            $sr_route_id[]=$sr_result['DistSrRouteMapping']['dist_route_id'];
            $results[] = array(
                'id'                => $sr_result['DistSrRouteMapping']['id'],
                'dist_route_id'     => $sr_result['DistSrRouteMapping']['dist_route_id'],
                'dist_route_name'   => $sr_result['DistRoute']['name'].'('.$sr_result['Thana']['name'].')',
                'dist_sr_id'        => $sr_result['DistSrRouteMapping']['dist_sr_id'],
                'dist_sr_name'      => $sr_result['DistSalesRepresentative']['name'],
                'dist_dm_id'        => $sr_result['DistSrRouteMapping']['dist_dm_id'],
                'dist_dm_name'      => $sr_result['DistDeliveryMan']['name'],
            );
        }


        $this->loadModel('DistRouteMapping');
        $dist_mappings = $this->DistRouteMapping->find('all', array(
            'conditions' => array(
                'DistRouteMapping.office_id' => $office_id, 
                'DistRouteMapping.dist_distributor_id'=>$dist_distributor_id,
                'DistRoute.is_active' => 1,
                'NOT'=>array('DistRouteMapping.dist_route_id'=>$sr_route_id)
            ),
            'fields'=>array('DistRouteMapping.*','DistRoute.*','Thana.name'),
            'joins'=>array(
            	array(
            		'table'=>'thanas',
            		'alias'=>'Thana',
            		'conditions'=>'Thana.id=DistRoute.thana_id',
            		'type'=>'LEFT'
            		),
            	),

        ));

        foreach($dist_mappings as $sr_result){
            $results[] = array(
                'id'                => "0",
                'dist_route_id'     => $sr_result['DistRouteMapping']['dist_route_id'],
                'dist_route_name'   => $sr_result['DistRoute']['name'].'('.$sr_result['Thana']['name'].')',
                'dist_sr_id'        => "",
                'dist_sr_name'      => "",
                'dist_dm_id'        => "",
                'dist_dm_name'      => "",
            );
        } 

        if($results)
        {
        
            $output = '';
            
            foreach($results as $result)
            {
                //pr($result);die();
                
                    $output.= '<tr class="table_row" id="'.$result['dist_route_id'].'">
                <td class="text-center">'. $result['dist_route_name'] . '</td><input name="data[RouteMaping]['.$result['dist_route_id'].'][id]" type="hidden" value="'.$result['id'].'"><input name="data[RouteMaping]['.$result['dist_route_id'].'][dist_route_id]" type="hidden" value="'.$result['dist_route_id'].'">';
                    
                    /*$output.='<td class="text-center">
                    <div class="input select">
                    <select name="data[RouteMaping]['.$result['dist_route_id'].'][dist_sr_id]" class="full_width form-control sr_id chosen">
                    <option selected value="'. $result['dist_sr_id'] . '">'.$result['dist_sr_name'].'</option>
                    
                    </select>
                    </div>
                    </td>';*/
                    
                    $output.='<td class="text-center">
                    <div class="input select">
                    <select name="data[RouteMaping]['.$result['dist_route_id'].'][dist_sr_id]" class="full_width form-control sr_id chosen"><option value="">--</option>';
                    foreach($dist_sr_list as $sr_id => $sr_name){
                        if(($result['dist_sr_id']==$sr_id)){
                            $output.='<option selected value="'.$sr_id.'">'.$sr_name.'</option>';
                        }else{
                            $output.='<option value="'.$sr_id.'">'.$sr_name.'</option>';
            
                        }
                    }
                    $output.='</select></div></td>';                
                    

                    
                    $output.='<td class="text-center">
                    <div class="input select">
                    <select name="data[RouteMaping]['.$result['dist_route_id'].'][dist_dm_id]" class="full_width form-control dm_id chosen"><option value="">--</option>';
                    foreach($dist_delivery_list as $dm_id => $dm_name){
                        if(($result['dist_dm_id']==$dm_id)){
                            $output.='<option selected value="'.$dm_id.'">'.$dm_name.'</option>';
                        }else{
                            $output.='<option value="'.$dm_id.'">'.$dm_name.'</option>';
            
                        }
                    }
                    $output.='</select></div></td>';
                                     
                 
                 $output.='</tr>';
            }
            $output.='</div>';

            echo $output;
        }
        else
        {
            echo '';
        }
        

        $this->autoRender = false;
    }
    
    
    
    
    
    public function admin_get_route_info() {
        
        $office_id = $this->request->data['office_id'];
        $route_id = $this->request->data['route_id'];
        $this->loadModel('DistRoute');
        
         $this->DistRoute->virtualFields = array(
            "name" => "CONCAT(DistRoute.name, ' (', Thana.name,')')"
        );
        $route_info = $this->DistRoute->find('first', array('conditions' => array('DistRoute.office_id' => $office_id, 'DistRoute.id'=>$route_id)));

        // pr($route_info);die();
        $data_array = array();
        if($route_info)
        {
            $data_array = array('id'=> $route_info['DistRoute']['id'],'name'=> $route_info['DistRoute']['name']);

            echo json_encode($data_array);
        }
        else
        {
            echo json_encode($data_array);
        }
        

        $this->autoRender = false;
    }
    public function admin_get_sr_info() {
        
        $office_id = $this->request->data['office_id'];
        $sr_id = $this->request->data['sr_id'];
        //$distributor_id = $this->request->data['distributor_id'];
        $this->loadModel('DistSalesRepresentative');
        

        $sr_info = $this->DistSalesRepresentative->find('first', array('conditions' => array('DistSalesRepresentative.office_id' => $office_id, 'DistSalesRepresentative.id'=>$sr_id)));

        //pr($distRoutes);die();
        $data_array = array();
        if($sr_info)
        {
            $data_array = array('id'=> $sr_info['DistSalesRepresentative']['id'],'name'=> $sr_info['DistSalesRepresentative']['name']);

            echo json_encode($data_array);
        }
        else
        {
            echo json_encode($data_array);
        }
        

        $this->autoRender = false;
    }
    public function admin_get_dm_info() {
        
        $office_id = $this->request->data['office_id'];
        $dm_id = $this->request->data['dm_id'];
        $distributor_id = $this->request->data['distributor_id'];
        $this->loadModel('DistDeliveryMan');
        

        $dm_info = $this->DistDeliveryMan->find('first', array('conditions' => array('DistDeliveryMan.office_id' => $office_id, 'DistDeliveryMan.id'=>$dm_id)));

        //pr($distRoutes);die();
        $data_array = array();
        if($dm_info)
        {
            $data_array = array('id'=> $dm_info['DistDeliveryMan']['id'],'name'=> $dm_info['DistDeliveryMan']['name']);

            echo json_encode($data_array);
        }
        else
        {
            echo json_encode($data_array);
        }
        

        $this->autoRender = false;
    }

}
