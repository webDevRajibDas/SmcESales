<?php

App::uses('AppController', 'Controller');

/**
 * Territories Controller
 *
 * @property Territory $Territory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistRoutesController extends AppController {

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
    public function admin_index() {
        $this->set('page_title', 'Route/Beat List');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('Office');
        $this->loadModel('DistAreaExecutive');
        
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $dist_route_conditions = array();
        if ($office_parent_id == 0) {
            $dist_route_conditions = $conditions = array('Office.office_type_id' =>2);
        } else {
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
               $route_list = $this->DistRouteMapping->find('list',array(
                'conditions'=>array('dist_distributor_id'=>array_keys($tso_dist_list)),
                'fields'=>array('DistRouteMapping.dist_route_id','DistRouteMapping.dist_distributor_id'),
               ));
               $dist_route_conditions = array('DistRoute.id'=>array_keys($route_list));

               $conditions = array('Office.id' => $this->UserAuth->getOfficeId(),'Office.office_type_id' =>2);
            }
            else{
                $dist_route_conditions = $conditions = array('Office.id' => $this->UserAuth->getOfficeId(),'Office.office_type_id' =>2);
            }
        }

        

        $this->paginate = array(
            'conditions' => $dist_route_conditions,
            'recursive' => 0,
            'order' => array('DistTsoMapping.id' => 'DESC')
        );
        $this->set('tsos', $this->paginate());
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        $this->set(compact('offices'));
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
        if (!$this->DistRoute->exists($id)) {
            throw new NotFoundException(__('Invalid Route/Beat'));
        }
        $options = array('conditions' => array('DistRoute.' . $this->DistRoute->primaryKey => $id));
        $this->set('tso', $this->DistRoute->find('first', $options));
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_dist_route_mapping($office_id, $dist_distributor_id) {
        $this->set('page_title', 'Distributor Routing/Beat Mapping');
        if ($this->request->is('post')) {
            
           //pr($this->request->data); exit;

            /* -------------Rows for Assign Data------------ */
            
            $update_history_qry="";   
            $update_remove_history_qry="";
            $array = array();
            $i = 0;
            if (array_key_exists('id', $this->request->data['DistRoute'])):
                foreach ($this->request->data['DistRoute']['id'] as $key => $dist_route_id) {
                    $array[$i]['created_at'] = $this->current_datetime();
                    $array[$i]['created_by'] = $this->UserAuth->getUserId();
                    $array[$i]['updated_at'] = $this->current_datetime();
                    $effective_date=date("Y-m-d H:i:s", strtotime($this->request->data['DistRoute']['effective_date'][$dist_route_id]));
                    $array[$i]['effective_date'] = $effective_date;
                    $array[$i]['office_id'] = $this->request->data['DistRoute']['office_id'];
                    $array[$i]['dist_distributor_id'] = $this->request->data['DistRoute']['dist_distributor_id'];
                    $array[$i]['dist_route_id'] = $dist_route_id;
                    $array[$i]['is_active'] = 1;
                    $array[$i]['is_assign'] = 1;
                    $array[$i]['is_change'] = 1;
                    
                    
                   /********************************* Checking Old Data start **********************************************/
                         if(array_key_exists('DistRouteMapping', $this->request->data) && array_key_exists('dist_route_id', $this->request->data['DistRouteMapping']))
                         {
                             /************** Working for Previous Data ***************/
                            if(array_key_exists($key, $this->request->data['DistRouteMapping']['dist_route_id']))
                            {
                             $old_effective_date=date("Y-m-d H:i:s", strtotime($this->request->data['DistRouteMapping']['dist_route_id'][$key]['effective_date'])); 
                             
                        /********************************* Checking Valid effective date start **********************************************/
                             
                                if (new DateTime($old_effective_date) > new DateTime($effective_date)) {
                                    $this->Session->setFlash(__('New Effective should not be less than the existing effective date'), 'flash/error');
                                    $this->redirect(array('action' => "dist_route_mapping/$office_id/$dist_distributor_id"));
                                }
                                
                                

                       /********************************* Checking Valid effective date start **********************************************/
                                
                                //echo $key."--".$effective_date."--".$old_effective_date."<br>";
                                
                               if($effective_date==$old_effective_date)
                               {
                                   $array[$i]['is_change'] = 0;
                                   $array[$i]['active'] = 0;
                               }
                               else 
                               {
                                   
                                 $curr_effective_date=date("Y-m-d");
                                /*
                                 if (new DateTime($curr_effective_date) > new DateTime($effective_date)) {
                                    $this->Session->setFlash(__('Effective date can not be any past date'), 'flash/error');
                                    $this->redirect(array('action' => "dist_route_mapping/$office_id/$dist_distributor_id"));
                                }
                                   */
                                   
                                  if($update_history_qry)
                                  {
                                      $update_history_qry=$update_history_qry.";update dist_route_mapping_histories set active=0,end_date='$effective_date' where office_id = $office_id and dist_distributor_id=$dist_distributor_id and dist_route_id = $dist_route_id and active=1 and is_assign=1 and end_date is NULL";
                                  }
                                  else 
                                  {
                                     $update_history_qry="update dist_route_mapping_histories set active=0,end_date='$effective_date' where office_id = $office_id and dist_distributor_id=$dist_distributor_id and dist_route_id = $dist_route_id and active=1 and is_assign=1 and end_date is NULL"; 
                                  }
                                   
                               }
                            }
                            else 
                            {
                                /**************** Working for New Data ****************/
                                 $curr_effective_date=date("Y-m-d");
                                /*
                                 if (new DateTime($curr_effective_date) > new DateTime($effective_date)) {
                                    $this->Session->setFlash(__('Effective date can not be any past date'), 'flash/error');
                                    $this->redirect(array('action' => "dist_route_mapping/$office_id/$dist_distributor_id"));
                                }
                                 * 
                                 */
                            }
                         }
                    
                       /********************************* Checking Old Data End **********************************************/
                    
                    
                    
                    
                    $i++;
                }
            endif;

            /* -------------Rows for DeAssign Data------------ */
            $array1 = array();
            $i = 0;
            
            
            
            
            /*********************  Checking data those are removed  start **************************/
            
            if ((array_key_exists('DistRouteMapping', $this->request->data)) && (array_key_exists('dist_route_id', $this->request->data['DistRouteMapping']))) {
                foreach ($this->request->data['DistRouteMapping']['dist_route_id'] as $key => $value) {
                    
                    if((!array_key_exists('id', $this->request->data['DistRoute'])) || (!array_key_exists($key, $this->request->data['DistRoute']['id'])))
                            {
                             $effective_date=date("Y-m-d H:i:s", strtotime($this->request->data['DistRoute']['effective_date'][$key]));
                             
                                /* 
                                $curr_effective_date=date("Y-m-d");
                                
                                 if (new DateTime($curr_effective_date) > new DateTime($effective_date)) {
                                    $this->Session->setFlash(__('Effective End Date can not be any past date'), 'flash/error');
                                    $this->redirect(array('action' => "dist_route_mapping/$office_id/$dist_distributor_id"));
                                }
                               */
                             if($update_remove_history_qry)
                                  {
                                      $update_remove_history_qry=$update_remove_history_qry.";update dist_route_mapping_histories set active=0,is_assign=0,end_date='$effective_date' where office_id = $office_id and dist_distributor_id=$dist_distributor_id and dist_route_id = $key and active=1 and is_assign=1 and end_date is NULL";
                                  }
                                  else 
                                  {
                                     $update_remove_history_qry="update dist_route_mapping_histories set active=0,is_assign=0,end_date='$effective_date' where office_id = $office_id and dist_distributor_id=$dist_distributor_id and dist_route_id = $key and active=1 and is_assign=1 and end_date is NULL"; 
                                  }
                           }       
                }
            }
            
           
            
            /*********************  Checking data those are removed  end  **************************/
            

            $this->loadModel('DistRouteMapping');
            $this->loadModel('DistRouteMappingHistory');
            
            if($update_history_qry)
            {
                $this->DistRouteMapping->query($update_history_qry);
            }
            
            if($update_remove_history_qry)
            {
                $this->DistRouteMapping->query($update_remove_history_qry);
            }
            
            $this->DistRouteMapping->query("delete from dist_route_mappings where office_id = $office_id and dist_distributor_id = $dist_distributor_id");

            
            if(!empty($array))
               {
                   if ($this->DistRouteMapping->saveAll($array)) {
                    $this->DistRouteMappingHistory->saveAll($array);
                   
                    $this->Session->setFlash(__('Distributor and Route Mapping has been Completed.'), 'flash/success');
                    $this->redirect(array('controller' => 'DistDistributors', 'action' => 'index'));
                    } else {
                        $this->Session->setFlash(__('There has some problem!'), 'flash/error');
                         $this->redirect(array('action' => "dist_route_mapping/$office_id/$dist_distributor_id"));
                    }
               }
               else 
               {
                    $this->Session->setFlash(__('Distributor and Route Mapping has been Completed.'), 'flash/success');
                   $this->redirect(array('controller' => 'DistDistributors', 'action' => 'index'));
               }
               
        }

        $this->loadModel('Office');
        $this->loadModel('DistDistributor');
        $this->loadModel('DistRoute');
        $this->loadModel('DistRouteMapping');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
        
        
        $distRouteMappings_except = $this->DistRouteMapping->find('all', array(
            //'fields'=>array('dist_route_id'),
            'conditions' => array(
                'DistRouteMapping.office_id' => $office_id,
                'DistRouteMapping.dist_distributor_id !=' => $dist_distributor_id
            ),
            'recursive' => -1
        ));
        
       // pr($distRouteMappings_except); exit;
        $route_except_ids=array();
        foreach ($distRouteMappings_except as $k => $v) {
            $route_except_ids[]=$v['DistRouteMapping']['dist_route_id'];
        }
        
       
        
        $distRouteMappings = $this->DistRouteMapping->find('all', array(
            'conditions' => array(
                'DistRouteMapping.office_id' => $office_id,
                'DistRouteMapping.dist_distributor_id' => $dist_distributor_id
            ),
            'recursive' => -1
        ));

        $mappingData = array();
        foreach ($distRouteMappings as $key => $value) {
            $mappingData[$value['DistRouteMapping']['dist_route_id']]['id'] = $value['DistRouteMapping']['id'];
            $mappingData[$value['DistRouteMapping']['dist_route_id']]['dist_route_id'] = $value['DistRouteMapping']['dist_route_id'];
            $mappingData[$value['DistRouteMapping']['dist_route_id']]['office_id'] = $value['DistRouteMapping']['office_id'];
            $mappingData[$value['DistRouteMapping']['dist_route_id']]['dist_distributor_id'] = $value['DistRouteMapping']['dist_distributor_id'];
            $mappingData[$value['DistRouteMapping']['dist_route_id']]['created_at'] = date("Y-m-d", strtotime($value['DistRouteMapping']['created_at']));
            $mappingData[$value['DistRouteMapping']['dist_route_id']]['effective_date'] = date("d-m-Y", strtotime($value['DistRouteMapping']['effective_date']));
        }
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        $distDistributors = $this->DistDistributor->find('list', array('conditions' => array('DistDistributor.office_id' => $office_id,'DistDistributor.is_active' => 1)));
       
        $distRoutes = $this->DistRoute->find('all', array('conditions' => array('DistRoute.office_id' => $office_id,'DistRoute.is_active' => 1,'NOT'=>array('DistRoute.id'=>$route_except_ids))));
        $this->set(compact('offices', 'office_id', 'distDistributors', 'dist_distributor_id', 'distRoutes', 'mappingData'));
    }
    
    
    /**
     * SR and Route Mapping_add method
     *
     * @return void
     */
    public function admin_sr_route_mapping($office_id,$dist_distributor_id,$dist_sr_id) {
        $this->set('page_title', 'SR Routing/Beat Mapping');
        $this->loadModel('DistSrRouteMapping');
        $this->loadModel('DistSrRouteMappingHistory');
        
        
                    

       
        if ($this->request->is('post')) {
            /* -------------Rows for Assign Data------------ */
            // pr($this->request->data);exit;
            $update_history_qry="";   
            $update_remove_history_qry="";
            $array = array();
            $i = 0;
            if (array_key_exists('id', $this->request->data['DistRoute'])):
                foreach ($this->request->data['DistRoute']['id'] as $key => $dist_route_id) {
                    $array[$i]['created_at'] = $this->current_datetime();
                    $array[$i]['created_by'] = $this->UserAuth->getUserId();
                    $array[$i]['updated_at'] = $this->current_datetime();
                    $array[$i]['updated_by'] = $this->UserAuth->getUserId();
                    $effective_date=date("Y-m-d H:i:s", strtotime($this->request->data['DistRoute']['effective_date'][$dist_route_id]));
                    $array[$i]['effective_date'] = $effective_date;
                    $array[$i]['office_id'] = $this->request->data['DistRoute']['office_id'];
                    $array[$i]['dist_distributor_id'] = $this->request->data['DistRoute']['dist_distributor_id'];
                    $array[$i]['dist_sr_id'] = $this->request->data['DistRoute']['dist_sr_id'];
                    $array[$i]['dist_route_id'] = $dist_route_id;
                    $array[$i]['is_assign'] = 1;
                    $array[$i]['is_active'] = 1;
                    $array[$i]['is_change'] = 1;
                    
                    
                    
                      
                   /********************************* Checking Old Data start **********************************************/
                         if(array_key_exists('DistSrRouteMapping', $this->request->data) && array_key_exists('dist_route_id', $this->request->data['DistSrRouteMapping']))
                         {
                             /************** Working for Previous Data ***************/
                            if(array_key_exists($key, $this->request->data['DistSrRouteMapping']['dist_route_id']))
                            {
                             $old_effective_date=date("Y-m-d H:i:s", strtotime($this->request->data['DistSrRouteMapping']['dist_route_id'][$key]['effective_date'])); 
                             
                        /********************************* Checking Valid effective date start **********************************************/
                             
                                if (new DateTime($old_effective_date) > new DateTime($effective_date)) {
                                    $this->Session->setFlash(__('New Effective should not be less than the existing effective date'), 'flash/error');
                                    $this->redirect(array('action' => "sr_route_mapping/$office_id/$dist_distributor_id/$dist_sr_id"));
                                }

                       /********************************* Checking Valid effective date start **********************************************/
                                
                                //echo $key."--".$effective_date."--".$old_effective_date."<br>";
                                
                               if($effective_date==$old_effective_date)
                               {
                                   $array[$i]['is_change'] = 0;
                                   $array[$i]['active'] = 0;
                               }
                               else 
                               {
                                   
                                   $curr_effective_date=date("Y-m-d");
                                /*
                                 if (new DateTime($curr_effective_date) > new DateTime($effective_date)) {
                                    $this->Session->setFlash(__('Effective date can not be any past date'), 'flash/error');
                                    $this->redirect(array('action' => "sr_route_mapping/$office_id/$dist_distributor_id/$dist_sr_id"));
                                }
                                   */
                                   
                                  if($update_history_qry)
                                  {
                                      $update_history_qry=$update_history_qry.";update dist_sr_route_mapping_histories set active=0,end_date='$effective_date' where office_id = $office_id and dist_distributor_id=$dist_distributor_id and dist_sr_id = $dist_sr_id and dist_route_id=$key  and active=1 and is_assign=1 and end_date is NULL";
                                  }
                                  else 
                                  {
                                     $update_history_qry="update dist_sr_route_mapping_histories set active=0,end_date='$effective_date' where office_id = $office_id and dist_distributor_id=$dist_distributor_id and dist_sr_id = $dist_sr_id and dist_route_id=$key  and active=1 and is_assign=1 and end_date is NULL"; 
                                  }
                                   
                               }
                            }
                            else 
                            {
                                /**************** Working for New Data ****************/
                                $curr_effective_date=date("Y-m-d");
                                /*
                                 if (new DateTime($curr_effective_date) > new DateTime($effective_date)) {
                                    $this->Session->setFlash(__('Effective date can not be any past date'), 'flash/error');
                                    $this->redirect(array('action' => "sr_route_mapping/$office_id/$dist_distributor_id/$dist_sr_id"));
                                }
                                 * 
                                 */
                            }
                         }
                    
                       /********************************* Checking Old Data End **********************************************/
                    
                    
                    
                    
                    $i++;
                }
            endif;

            /* -------------Rows for DeAssign Data------------ */
            $array1 = array();
            
            
            /*********************  Checking data those are removed  start **************************/
            
            if ((array_key_exists('DistSrRouteMapping', $this->request->data)) && (array_key_exists('dist_route_id', $this->request->data['DistSrRouteMapping']))) {
                foreach ($this->request->data['DistSrRouteMapping']['dist_route_id'] as $key => $value) {
                    
                     if((!array_key_exists('id', $this->request->data['DistRoute'])) || (!array_key_exists($key, $this->request->data['DistRoute']['id'])))
                            {
                             $effective_date=date("Y-m-d H:i:s", strtotime($this->request->data['DistRoute']['effective_date'][$key]));
                             
                              $curr_effective_date=date("Y-m-d");
                                
                              /*
                                 if (new DateTime($curr_effective_date) > new DateTime($effective_date)) {
                                    $this->Session->setFlash(__('Effective date can not be any past date'), 'flash/error');
                                    $this->redirect(array('action' => "sr_route_mapping/$office_id/$dist_distributor_id/$dist_sr_id"));
                                }
                              */
                             if($update_remove_history_qry)
                                  {
                                      $update_remove_history_qry=$update_remove_history_qry.";update dist_sr_route_mapping_histories set active=0,is_assign=0,end_date='$effective_date' where office_id = $office_id and dist_distributor_id=$dist_distributor_id and dist_sr_id = $dist_sr_id and dist_route_id = $key and active=1 and is_assign=1 and end_date is NULL";
                                  }
                                  else 
                                  {
                                     $update_remove_history_qry="update dist_sr_route_mapping_histories set active=0,is_assign=0,end_date='$effective_date' where office_id = $office_id and dist_distributor_id=$dist_distributor_id and dist_sr_id = $dist_sr_id and dist_route_id = $key and active=1 and is_assign=1 and end_date is NULL"; 
                                  }
                           }       
                }
            }
            
           
            
            /*********************  Checking data those are removed  end  **************************/
            
            
            
            
            $this->loadModel('DistSrRouteMapping');
            $this->loadModel('DistSrRouteMappingHistory');
            
            if($update_history_qry)
            {
                $this->DistSrRouteMapping->query($update_history_qry);
            }
            
            if($update_remove_history_qry)
            {
                $this->DistSrRouteMapping->query($update_remove_history_qry);
            }
            
            $this->DistSrRouteMapping->query("delete from dist_sr_route_mappings where office_id = $office_id and dist_sr_id = $dist_sr_id");
            
            
               if(!empty($array))
               {
                   if ($this->DistSrRouteMapping->saveAll($array)) {
                    $this->DistSrRouteMappingHistory->saveAll($array);
                   
                    $this->Session->setFlash(__('SR and Route Mapping has been Completed.'), 'flash/success');
                    $this->redirect(array('controller' => 'DistSalesRepresentatives', 'action' => 'index'));
                    } else {
                        $this->Session->setFlash(__('There has some problem!'), 'flash/error');
                        $this->redirect(array('action' => "sr_route_mapping/$office_id/$dist_distributor_id/$dist_sr_id"));
                    }
               }
               else 
               {
                    $this->Session->setFlash(__('SR and Route Mapping has been Completed.'), 'flash/success');
                   $this->redirect(array('controller' => 'DistSalesRepresentatives', 'action' => 'index'));
               }
        }

        $this->loadModel('Office');
        $this->loadModel('DistSalesRepresentative');
        $this->loadModel('DistRoute');
        $this->loadModel('DistSrRouteMapping');
        $this->loadModel('DistDistributor');
        $this->loadModel('DistRouteMapping');
        
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
        
        
         $distRouteMappings_except = $this->DistSrRouteMapping->find('all', array(
            'fields'=>array('dist_route_id'),
            'conditions' => array(
                'DistSrRouteMapping.office_id' => $office_id,
                'DistSrRouteMapping.dist_distributor_id' => $dist_distributor_id,
                'DistSrRouteMapping.dist_sr_id !=' => $dist_sr_id
            ),
            'recursive' => -1
        ));
       
        $route_except_ids=array();
        foreach ($distRouteMappings_except as $k => $v) {
            $route_except_ids[]=$v['DistSrRouteMapping']['dist_route_id'];
        }
        
        
        
        
        $distSrRouteMappings = $this->DistSrRouteMapping->find('all', array(
            'conditions' => array(
                'DistSrRouteMapping.office_id' => $office_id,
                'DistSrRouteMapping.dist_sr_id' => $dist_sr_id
            ),
            'recursive' => -1
        ));

        $mappingData = array();
        foreach ($distSrRouteMappings as $key => $value) {
            $mappingData[$value['DistSrRouteMapping']['dist_route_id']]['id'] = $value['DistSrRouteMapping']['id'];
            $mappingData[$value['DistSrRouteMapping']['dist_route_id']]['dist_route_id'] = $value['DistSrRouteMapping']['dist_route_id'];
            $mappingData[$value['DistSrRouteMapping']['dist_route_id']]['office_id'] = $value['DistSrRouteMapping']['office_id'];
            $mappingData[$value['DistSrRouteMapping']['dist_route_id']]['dist_distributor_id'] = $value['DistSrRouteMapping']['dist_distributor_id'];
            $mappingData[$value['DistSrRouteMapping']['dist_route_id']]['dist_sr_id'] = $value['DistSrRouteMapping']['dist_sr_id'];
            $mappingData[$value['DistSrRouteMapping']['dist_route_id']]['created_at'] = date("Y-m-d", strtotime($value['DistSrRouteMapping']['created_at']));
            $mappingData[$value['DistSrRouteMapping']['dist_route_id']]['effective_date'] = date("d-m-Y", strtotime($value['DistSrRouteMapping']['effective_date']));
        }
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        $distDistributors = $this->DistDistributor->find('list', array('conditions' => array('DistDistributor.id' => $dist_distributor_id)));
        $distSalesRepresentatives = $this->DistSalesRepresentative->find('list', array('conditions' => array('DistSalesRepresentative.office_id' => $office_id)));
        
        
        $distRoutes = $this->DistRouteMapping->find('all', array('conditions' => array('DistRouteMapping.office_id' => $office_id,'DistRouteMapping.dist_distributor_id' => $dist_distributor_id,'NOT'=>array('DistRouteMapping.dist_route_id'=>$route_except_ids))));
		//pr($distRoutes);
        //$distRoutes = $this->DistRouteMapping->find('all', array('conditions' => array('DistRouteMapping.office_id' => $office_id,'DistRouteMapping.dist_distributor_id' => $dist_distributor_id)));
        //pr($distRoutes); exit;
        $this->set(compact('offices', 'office_id','distDistributors','dist_distributor_id','distSalesRepresentatives', 'dist_sr_id', 'distRoutes', 'mappingData'));
    }


    public function admin_add() {
        $this->set('page_title', 'Add Route/Beat');
        if ($this->request->is('post')) {
            $this->request->data['DistRoute']['created_at'] = $this->current_datetime();
            $this->request->data['DistRoute']['created_by'] = $this->UserAuth->getUserId();
            $this->request->data['DistRoute']['updated_at'] = $this->current_datetime();
            $this->DistRoute->create();
            if ($this->DistRoute->save($this->request->data)) {
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
        $this->DistRoute->id = $id;
        if (!$this->DistRoute->exists($id)) {
            throw new NotFoundException(__('Invalid Route/Beat'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['DistRoute']['updated_by'] = $this->UserAuth->getUserId();
            if ($this->DistRoute->save($this->request->data)) {
                $this->Session->setFlash(__('The Route/Beat has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            }
        } 
        else 
        {
            $options = array('conditions' => array('DistRoute.' . $this->DistRoute->primaryKey => $id));
            $this->request->data = $this->DistRoute->find('first', $options);
            $this->request->data['DistRoute']['district_id']=$this->request->data['Thana']['district_id'];
            // pr($this->request->data);exit;

            $this->loadmodel('Territory');
            $this->loadmodel('ThanaTerritory');
            $office_id = $this->request->data['DistRoute']['office_id'];
            $territory = $this->Territory->find('all', array(
                'fields' => array('Territory.id'),
                'conditions' => array('Territory.office_id' => $office_id),
                'order' => array('Territory.name' => 'asc'),
                'recursive' => 0
                ));
            $thanas = array();
            $districts = array();
            $territories = array();

            foreach($territory as $key => $value)
            {
                $territories[$key] = $value['Territory']['id'];

            }

            //pr($data_array);die();
            $thana_list = $this->ThanaTerritory->find('all',array('conditions'=>array(
                'ThanaTerritory.territory_id' => $territories,
                'Thana.district_id'=> $this->request->data['Thana']['district_id']
                )));

            foreach($thana_list as $key => $value)
            {
                $thanas[$value['Thana']['id']] = $value['Thana']['name'];
            }
            $district_list = $this->ThanaTerritory->find('all',array('conditions'=>array(
             'ThanaTerritory.territory_id' => $territories),
            'joins'=>array(
                array(
                    'table'=>'districts',
                    'alias'=>'District',
                    'conditions'=>'District.id=Thana.district_id'
                    ),
                ),
            'order'=>array('District.name ASC'),
            'fields' => 'DISTINCT District.id, District.name'
            ));
            foreach($district_list as $key => $value)
            {
                $districts[$value['District']['id']] = $value['District']['name'];
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
        $this->set(compact('offices','thanas','districts'));
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
        
        $this->DistRoute->id = $id;
        if (!$this->DistRoute->exists()) {
            throw new NotFoundException(__('Invalid Route/Beat'));
        }
        
                /******************************* Check foreign key ****************************/
        
        if($this->DistRoute->checkForeignKeys("dist_routes",$id))
        {
            $this->Session->setFlash(__('This Route/Beat has used in another'), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }
        
        
        if ($this->DistRoute->delete()) {
            $this->Session->setFlash(__('Route/Beat deleted'), 'flash/success');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Route/Beat was not deleted'), 'flash/error');
        $this->redirect(array('action' => 'index'));
    }
    
     public function get_dist_mapping_end_date($office_id = 0,$dist_distributor_id = 0,$route_id=0) {
        $this->loadModel('DistRouteMappingHistory');
        $data=array();
        $distTsoMappings = $this->DistRouteMappingHistory->find('first', array('conditions' => array(
                'DistRouteMappingHistory.office_id' => $office_id,
                'DistRouteMappingHistory.dist_distributor_id' => $dist_distributor_id,
                'DistRouteMappingHistory.dist_route_id' => $route_id,
                'DistRouteMappingHistory.is_change' => 1,
            ),
            'order' => 'DistRouteMappingHistory.id DESC',
            'recursive' => -1));
       
        if (!empty($distTsoMappings)) {
            $data=$distTsoMappings['DistRouteMappingHistory'];
            return $data;
        } else {
            return $data;
        }
    }
    
    public function get_sr_mapping_end_date($office_id = 0,$dist_distributor_id = 0,$route_id=0,$sr_id=0) {
        $this->loadModel('DistSrRouteMappingHistory');
        $data=array();
        $distTsoMappings = $this->DistSrRouteMappingHistory->find('first', array('conditions' => array(
                'DistSrRouteMappingHistory.office_id' => $office_id,
                'DistSrRouteMappingHistory.dist_distributor_id' => $dist_distributor_id,
                'DistSrRouteMappingHistory.dist_route_id' => $route_id,
                'DistSrRouteMappingHistory.dist_sr_id' => $sr_id,
                'DistSrRouteMappingHistory.is_change' => 1,
            ),
            'order' => 'DistSrRouteMappingHistory.id DESC',
            'recursive' => -1));
       
        if (!empty($distTsoMappings)) {
            $data=$distTsoMappings['DistSrRouteMappingHistory'];
            return $data;
        } else {
            return $data;
        }
    }

    
    public function admin_dist_history() {
        $this->set('page_title', 'Distributor and Route/Beat Mapping History');
        
        //pr($this->request->data);exit;
        $this->loadModel('Office');
        $this->loadModel('DistDistributor');
        $this->loadModel('DistRoute');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistRouteMappingHistory');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistAreaExecutive');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        $office_id=0;
        $dist_distributor_id=0;
        $dist_route_id=0;
        $distRoutes=array();
        $distDistributors=array();
        $routes=array();
        $dist=array();
        $page_conditions=array();
        $route_conditions=array();
        $office_parent_id = $this->UserAuth->getOfficeParentId();
         if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
        } else {
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
            }
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
                
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        
        /***** Route Start  ******/
        
        $routes=$this->DistRouteMappingHistory->find('all',array('fields' => array('DISTINCT DistRouteMappingHistory.dist_route_id'),'recursive' =>0));
       
        if(!empty($routes))
        {
              $dist_route_ids=array();
              foreach ($routes as $key => $value) {
                  $dist_route_ids[]=$value['DistRouteMappingHistory']['dist_route_id'];
              }
              
              $distTso = $this->DistRoute->find('all', array('conditions' => array(
                 'DistRoute.id' => $dist_route_ids
             ),
             'order' => 'DistRoute.name asc',
             'recursive' =>0));
              
         foreach ($distTso as $key => $value) {
             $id=$value['DistRoute']['id'];
             $distRoutes[$id]=$value['DistRoute']['name'];
         }
              
        }
        
          /***** Route End  ******/
        
        
         /***** Distributor Start  ******/
        $dist=$this->DistRouteMappingHistory->find('all',array('fields' => array('DISTINCT DistRouteMappingHistory.dist_distributor_id'),'recursive' =>0));
       
        if(!empty($dist))
        {
              $dist_ids=array();
              foreach ($dist as $key => $value) {
                  $dist_ids[]=$value['DistRouteMappingHistory']['dist_distributor_id'];
              }
              
              $dist_data = $this->DistDistributor->find('all', array('conditions' => array(
                 'DistDistributor.id' => $dist_ids
             ),
             'order' => 'DistDistributor.name asc',
             'recursive' =>0));
              
         foreach ($dist_data as $key => $value) {
             $id=$value['DistDistributor']['id'];
             $distDistributors[$id]=$value['DistDistributor']['name'];
         }
              
        }
        
          /***** Distributor End  ******/
        
        
        if ($this->request->is('post')) {
            $office_id=(isset($this->request->data['DistRoutes']['office_id']))?$this->request->data['DistRoutes']['office_id']:0;
            $dist_route_id=(isset($this->request->data['DistRoutes']['dist_route_id']))?$this->request->data['DistRoutes']['dist_route_id']:0;
            $dist_distributor_id=(isset($this->request->data['DistRoutes']['dist_distributor_id']))?$this->request->data['DistRoutes']['dist_distributor_id']:0;
            $dist_date_from=date("Y-m-d H:i:s", strtotime($this->request->data['DistRoutes']['date_from']));
            $dist_date_to=date("Y-m-d H:i:s", strtotime($this->request->data['DistRoutes']['date_to']));
          
            if($office_id)
                {
                    $distRoutes = $this->DistRoute->find('list', array('conditions' => array(
                      'DistRoute.office_id' => $office_id
                    ),
                    'order' => 'DistRoute.name ASC',
                    'recursive' => -1));
                }
            
            if($office_id)
                {
                $distDistributors = $this->DistDistributor->find('list', array('conditions' => array(
                      'DistDistributor.office_id' => $office_id
                    ),
                    'order' => 'DistDistributor.name ASC',
                    'recursive' => -1));
                }
                
                if($office_id)
                {
                    $page_conditions['DistRouteMappingHistory.office_id']=$office_id;
                }
                
                if($dist_route_id)
                {
                  $page_conditions['DistRouteMappingHistory.dist_route_id']=$dist_route_id;  
                }
                
                if($dist_distributor_id)
                {
                  $page_conditions['DistRouteMappingHistory.dist_distributor_id']=$dist_distributor_id;  
                }
                
                if($dist_date_from)
                {
                  $page_conditions['DistRouteMappingHistory.effective_date >=']=$dist_date_from;  
                }
                
                if($dist_date_to)
                {
                    
                  $page_conditions['case 
                    when DistRouteMappingHistory.end_date is not null then 
                     DistRouteMappingHistory.end_date
                    else 
                    convert(varchar(10),getDate(), 120)
                    end <=']=$dist_date_to;  
                }
                
                
                 
        }
     
        $this->paginate = array(
            'conditions' => $page_conditions,
            'recursive' => 0
        );
        
        $data = $this->paginate('DistRouteMappingHistory');
          // echo $this->DistTsoMappingHistory->getLastQuery();
          // pr($data);exit;
        
        $this->loadModel('User');
                
                $users_data = $this->User->find('all', array(
                'fields'=>array('User.id','User.user_group_id','User.username','SalesPerson.id','SalesPerson.name','SalesPerson.office_id'),
                'conditions' => array('User.active' => 1,'User.user_group_id'=>array(1,2,3,5,7,1017,1018,1020)),
                'joins' => array(
                                array(
                                    'table' => 'sales_people',
                                    'alias' => 'SalesPerson',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'User.sales_person_id = SalesPerson.id',
                                    ))),                           
                'order' => array('SalesPerson.name' => 'asc'),
                'recursive' =>-1
            ));

            $users=array();
            foreach ($users_data as $key => $value) {
                $u_id=$value['User']['id'];
                $u_name=$value['SalesPerson']['name'];
                $users[$u_id]=$u_name;
            }
            
            
       // pr($data); exit;
        $this->set(compact('data','distDistributors','office_id','dist_route_id','dist_distributor_id','offices','users','distRoutes'));
    }
    
    
    public function admin_sr_history() {
        $this->set('page_title', 'Sales Representatives and Route/Beat Mapping History');
        
        //pr($this->request->data);exit;
        $this->loadModel('Office');
        $this->loadModel('DistSalesRepresentative');
        $this->loadModel('DistRoute');
        $this->loadModel('DistSrRouteMapping');
        $this->loadModel('DistDistributor');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistSrRouteMappingHistory');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        
        $office_id=0;
        $dist_distributor_id=0;
        $dist_route_id=0;
        $dist_sr_id=0;
        $distRoutes=array();
        $distDistributors=array();
        $distsrs=array();
        $routes=array();
        $dist=array();
        $srs=array();
        $page_conditions=array();
        $sr_conditions =  array();
        $office_parent_id = $this->UserAuth->getOfficeParentId();
         if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
        } else {
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
               $page_conditions = $sr_conditions = array('DistSrRouteMappingHistory.dist_distributor_id'=>array_keys($tso_dist_list));
            }
            elseif($user_group_id == 1034){
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first',array(
                    'conditions'=>array('DistUserMapping.sales_person_id'=>$sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
                $page_conditions = $sr_conditions = array('DistSrRouteMappingHistory.dist_distributor_id'=>$distributor_id);

            }else{
                $page_conditions = $sr_conditions = array(
                    'DistSrRouteMappingHistory.office_id'=>$this->UserAuth->getOfficeId(),
                );
            }
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
                
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        
        /***** Route Start  ******/
        
        $routes=$this->DistSrRouteMappingHistory->find('all',array('conditions'=>$sr_conditions,'fields' => array('DISTINCT DistSrRouteMappingHistory.dist_route_id'),'recursive' =>0));
       
        if(!empty($routes))
        {
              $dist_route_ids=array();
              foreach ($routes as $key => $value) {
                  $dist_route_ids[]=$value['DistSrRouteMappingHistory']['dist_route_id'];
              }
              
              $distTso = $this->DistRoute->find('all', array('conditions' => array(
                 'DistRoute.id' => $dist_route_ids
             ),
             'order' => 'DistRoute.name asc',
             'recursive' =>0));
              
         foreach ($distTso as $key => $value) {
             $id=$value['DistRoute']['id'];
             $distRoutes[$id]=$value['DistRoute']['name'];
         }
              
        }
        
          /***** Route End  ******/
        
        /***** SR Start  ******/
        
        $srs=$this->DistSrRouteMappingHistory->find('all',array('conditions'=>$sr_conditions,'fields' => array('DISTINCT DistSrRouteMappingHistory.dist_sr_id'),'recursive' =>0));
       
        if(!empty($srs))
        {
              $dist_sr_ids=array();
              foreach ($srs as $key => $value) {
                  $dist_sr_ids[]=$value['DistSrRouteMappingHistory']['dist_sr_id'];
              }
              
              $distTso = $this->DistSalesRepresentative->find('all', array('conditions' => array(
                 'DistSalesRepresentative.id' => $dist_sr_ids, 'DistSalesRepresentative.is_active' => 1,
             ),
             'order' => 'DistSalesRepresentative.name asc',
             'recursive' =>0));
              
         foreach ($distTso as $key => $value) {
             $id=$value['DistSalesRepresentative']['id'];
             $distsrs[$id]=$value['DistSalesRepresentative']['name'];
         }
              
        }
        
          /***** SR End  ******/
        
        
         /***** Distributor Start  ******/
        $dist=$this->DistSrRouteMappingHistory->find('all',array('conditions'=>$sr_conditions,'fields' => array('DISTINCT DistSrRouteMappingHistory.dist_distributor_id'),'recursive' =>0));
       
        if(!empty($dist))
        {
              $dist_ids=array();
              foreach ($dist as $key => $value) {
                  $dist_ids[]=$value['DistSrRouteMappingHistory']['dist_distributor_id'];
              }
              
              $dist_data = $this->DistDistributor->find('all', array('conditions' => array(
                 'DistDistributor.id' => $dist_ids,'DistDistributor.is_active'=>1,
             ),
             'order' => 'DistDistributor.name asc',
             'recursive' =>0));
              
         foreach ($dist_data as $key => $value) {
             $id=$value['DistDistributor']['id'];
             $distDistributors[$id]=$value['DistDistributor']['name'];
         }
              
        }
        
          /***** Distributor End  ******/
        
        // pr($this->request->query);exit;
        if ($this->request->is('post') || $this->request->query) 
        {
            if($this->request->query)
                $this->request->data=$this->request->query;
            $request_data=$this->request->data;
            $office_id=(isset($this->request->data['DistRoutes']['office_id']))?$this->request->data['DistRoutes']['office_id']:0;
            $dist_route_id=(isset($this->request->data['DistRoutes']['dist_route_id']))?$this->request->data['DistRoutes']['dist_route_id']:0;
            $dist_distributor_id=(isset($this->request->data['DistRoutes']['dist_distributor_id']))?$this->request->data['DistRoutes']['dist_distributor_id']:0;
            $dist_sr_id=(isset($this->request->data['DistRoutes']['dist_sr_id']))?$this->request->data['DistRoutes']['dist_sr_id']:0;
            $dist_date_from=date("Y-m-d H:i:s", strtotime($this->request->data['DistRoutes']['date_from']));
            $dist_date_to=date("Y-m-d H:i:s", strtotime($this->request->data['DistRoutes']['date_to']));
          
            if($office_id)
                {
                    $distRoutes = $this->DistRoute->find('list', array('conditions' => array(
                      'DistRoute.office_id' => $office_id
                    ),
                    'order' => 'DistRoute.name ASC',
                    'recursive' => -1));
                }
            
            if($office_id)
                {
                $distDistributors = $this->DistDistributor->find('list', array('conditions' => array(
                      'DistDistributor.office_id' => $office_id,'DistDistributor.is_active'=>1
                    ),
                    'order' => 'DistDistributor.name ASC',
                    'recursive' => -1));
                }
                
                if($office_id)
                {
                $distsrs = $this->DistSalesRepresentative->find('list', array('conditions' => array(
                      'DistSalesRepresentative.office_id' => $office_id,'DistSalesRepresentative.is_active'=>1,
                    ),
                    'order' => 'DistSalesRepresentative.name ASC',
                    'recursive' => -1));
                }
                
                if($office_id)
                {
                    $page_conditions['DistSrRouteMappingHistory.office_id']=$office_id;
                }
                
                if($dist_route_id)
                {
                  $page_conditions['DistSrRouteMappingHistory.dist_route_id']=$dist_route_id;  
                }
                
                if($dist_distributor_id)
                {
                  $page_conditions['DistSrRouteMappingHistory.dist_distributor_id']=$dist_distributor_id;  
                }
                
                if($dist_sr_id)
                {
                  $page_conditions['DistSrRouteMappingHistory.dist_sr_id']=$dist_sr_id;  
                }
                
                if($dist_date_from)
                {
                  $page_conditions['DistSrRouteMappingHistory.effective_date >=']=$dist_date_from;  
                }
                
                if($dist_date_to)
                {
                    
                  $page_conditions['case 
                    when DistSrRouteMappingHistory.end_date is not null then 
                     DistSrRouteMappingHistory.end_date
                    else 
                    convert(varchar(10),getDate(), 120)
                    end <=']=$dist_date_to;  
                }
        }
     
        $this->paginate = array(
            'conditions' => $page_conditions,
            'recursive' => 0
        );
        
        $data = $this->paginate('DistSrRouteMappingHistory');
          // echo $this->DistTsoMappingHistory->getLastQuery();
          //pr($data);exit;
        
        $this->loadModel('User');
                
                $users_data = $this->User->find('all', array(
                'fields'=>array('User.id','User.user_group_id','User.username','SalesPerson.id','SalesPerson.name','SalesPerson.office_id'),
                'conditions' => array('User.active' => 1,'User.user_group_id'=>array(1,2,3,5,7,1017,1018,1020)),
                'joins' => array(
                                array(
                                    'table' => 'sales_people',
                                    'alias' => 'SalesPerson',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'User.sales_person_id = SalesPerson.id',
                                    ))),                           
                'order' => array('SalesPerson.name' => 'asc'),
                'recursive' =>-1
            ));

            $users=array();
            foreach ($users_data as $key => $value) {
                $u_id=$value['User']['id'];
                $u_name=$value['SalesPerson']['name'];
                $users[$u_id]=$u_name;
            }
            
            
       // pr($data); exit;
        $this->set(compact('data','distDistributors','office_id','dist_route_id','dist_distributor_id','offices','users','distRoutes','distsrs','dist_sr_id','request_data'));
    }
    

    public function admin_dm_history() {
        $this->set('page_title', 'Delivery Man and Route/Beat Mapping History');
        
        //pr($this->request->data);exit;
        $this->loadModel('Office');
        $this->loadModel('DistDeliveryMan');
        $this->loadModel('DistRoute');
        $this->loadModel('DistSrRouteMapping');
        $this->loadModel('DistDistributor');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistSrRouteMappingHistory');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        
        $office_id=0;
        $dist_distributor_id=0;
        $dist_route_id=0;
        $dist_sr_id=0;
        $distRoutes=array();
        $distDistributors=array();
        $distsrs=array();
        $routes=array();
        $dist=array();
        $srs=array();
        $page_conditions=array();
        $sr_conditions =  array();
        $office_parent_id = $this->UserAuth->getOfficeParentId();
         if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
            $page_conditions = $sr_conditions = array('DistSrRouteMappingHistory.dist_dm_id !='=>0);
        } else {
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
               $page_conditions = $sr_conditions = array(
                'DistSrRouteMappingHistory.dist_distributor_id'=>array_keys($tso_dist_list),
                'DistSrRouteMappingHistory.dist_dm_id !='=>0
            );
            }
            elseif($user_group_id == 1034){
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first',array(
                    'conditions'=>array('DistUserMapping.sales_person_id'=>$sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
                $page_conditions = $sr_conditions = array(
                    'DistSrRouteMappingHistory.dist_distributor_id'=>$distributor_id,
                    'DistSrRouteMappingHistory.dist_dm_id !='=>0
                );

            }else{
                $page_conditions = $sr_conditions = array(
                    'DistSrRouteMappingHistory.office_id'=>$this->UserAuth->getOfficeId(),
                    'DistSrRouteMappingHistory.dist_dm_id !='=>0
                );
            }
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
                
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        
        /***** Route Start  ******/
        
        $routes=$this->DistSrRouteMappingHistory->find('all',array('conditions'=>$sr_conditions,'fields' => array('DISTINCT DistSrRouteMappingHistory.dist_route_id'),'recursive' =>0));
      
        if(!empty($routes))
        {
              $dist_route_ids=array();
              foreach ($routes as $key => $value) {
                  $dist_route_ids[]=$value['DistSrRouteMappingHistory']['dist_route_id'];
              }
              
              $distTso = $this->DistRoute->find('all', array('conditions' => array(
                 'DistRoute.id' => $dist_route_ids
             ),
             'order' => 'DistRoute.name asc',
             'recursive' =>0));
              
         foreach ($distTso as $key => $value) {
             $id=$value['DistRoute']['id'];
             $distRoutes[$id]=$value['DistRoute']['name'];
         }
              
        }
        
          /***** Route End  ******/
        
        /***** SR Start  ******/
        
        $srs=$this->DistSrRouteMappingHistory->find('all',array('conditions'=>$sr_conditions,'fields' => array('DISTINCT DistSrRouteMappingHistory.dist_dm_id'),'recursive' =>0));
       //pr($srs);
        if(!empty($srs))
        {
              $dist_sr_ids=array();
              foreach ($srs as $key => $value) {
                  $dist_sr_ids[]=$value['DistSrRouteMappingHistory']['dist_dm_id'];
              }
               
              $distTso = $this->DistDeliveryMan->find('all', array('conditions' => array(
                 'DistDeliveryMan.id' => $dist_sr_ids, 'DistDeliveryMan.is_active' => 1,
             ),
             'order' => 'DistDeliveryMan.name asc',
             'recursive' =>0));
              
         foreach ($distTso as $key => $value) {
             $id=$value['DistDeliveryMan']['id'];
             $distsrs[$id]=$value['DistDeliveryMan']['name'];
         }
              
        }
        //pr($distsrs);die();
          /***** SR End  ******/
        
        
         /***** Distributor Start  ******/
        $dist=$this->DistSrRouteMappingHistory->find('all',array('conditions'=>$sr_conditions,'fields' => array('DISTINCT DistSrRouteMappingHistory.dist_distributor_id'),'recursive' =>0));
       
        if(!empty($dist))
        {
              $dist_ids=array();
              foreach ($dist as $key => $value) {
                  $dist_ids[]=$value['DistSrRouteMappingHistory']['dist_distributor_id'];
              }
              
              $dist_data = $this->DistDistributor->find('all', array('conditions' => array(
                 'DistDistributor.id' => $dist_ids,'DistDistributor.is_active'=>1,
             ),
             'order' => 'DistDistributor.name asc',
             'recursive' =>0));
              
         foreach ($dist_data as $key => $value) {
             $id=$value['DistDistributor']['id'];
             $distDistributors[$id]=$value['DistDistributor']['name'];
         }
              
        }
        
          /***** Distributor End  ******/
        
        // pr($this->request->query);exit;
        if ($this->request->is('post') || $this->request->query) 
        {
            if($this->request->query)
                $this->request->data=$this->request->query;
            $request_data=$this->request->data;
            $office_id=(isset($this->request->data['DistRoutes']['office_id']))?$this->request->data['DistRoutes']['office_id']:0;
            $dist_route_id=(isset($this->request->data['DistRoutes']['dist_route_id']))?$this->request->data['DistRoutes']['dist_route_id']:0;
            $dist_distributor_id=(isset($this->request->data['DistRoutes']['dist_distributor_id']))?$this->request->data['DistRoutes']['dist_distributor_id']:0;
            $dist_sr_id=(isset($this->request->data['DistRoutes']['dist_sr_id']))?$this->request->data['DistRoutes']['dist_sr_id']:0;
            $dist_date_from=date("Y-m-d H:i:s", strtotime($this->request->data['DistRoutes']['date_from']));
            $dist_date_to=date("Y-m-d H:i:s", strtotime($this->request->data['DistRoutes']['date_to']));
          
            if($office_id)
            {
                $distRoutes = $this->DistRoute->find('list', array('conditions' => array(
                  'DistRoute.office_id' => $office_id
                ),
                'order' => 'DistRoute.name ASC',
                'recursive' => -1));
            }
            
            if($office_id)
            {
                $distDistributors = $this->DistDistributor->find('list', array('conditions' => array(
                      'DistDistributor.office_id' => $office_id,'DistDistributor.is_active'=>1
                    ),
                    'order' => 'DistDistributor.name ASC',
                    'recursive' => -1));
            }
                
            if($office_id)
            {
                $distsrs = $this->DistDeliveryMan->find('list', array('conditions' => array(
                      'DistDeliveryMan.office_id' => $office_id,'DistDeliveryMan.is_active'=>1,
                    ),
                    'order' => 'DistDeliveryMan.name ASC',
                    'recursive' => -1));
            }
            
            
            if($office_id)
            {
                $page_conditions['DistSrRouteMappingHistory.office_id']=$office_id;
            }
                
            if($dist_route_id)
            {
              $page_conditions['DistSrRouteMappingHistory.dist_route_id']=$dist_route_id;  
            }
                
            if($dist_distributor_id)
            {
              $page_conditions['DistSrRouteMappingHistory.dist_distributor_id']=$dist_distributor_id;  
            }
                
            if($dist_sr_id)
            {
              $page_conditions['DistSrRouteMappingHistory.dist_dm_id']=$dist_sr_id;  
            }
                
            if($dist_date_from)
            {
              $page_conditions['DistSrRouteMappingHistory.effective_date >=']=$dist_date_from;  
            }
                
            if($dist_date_to)
            {
                
              $page_conditions['case 
                when DistSrRouteMappingHistory.end_date is not null then 
                 DistSrRouteMappingHistory.end_date
                else 
                convert(varchar(10),getDate(), 120)
                end <=']=$dist_date_to;  
            }
        }
        
       
        $this->paginate = array(
            'conditions' => $page_conditions,
            'recursive' => 0
        );
        
        $data = $this->paginate('DistSrRouteMappingHistory');
          // echo $this->DistTsoMappingHistory->getLastQuery();  
        $this->loadModel('User');
                
                $users_data = $this->User->find('all', array(
                'fields'=>array('User.id','User.user_group_id','User.username','SalesPerson.id','SalesPerson.name','SalesPerson.office_id'),
                'conditions' => array('User.active' => 1,'User.user_group_id'=>array(1,2,3,5,7,1017,1018,1020)),
                'joins' => array(
                                array(
                                    'table' => 'sales_people',
                                    'alias' => 'SalesPerson',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'User.sales_person_id = SalesPerson.id',
                                    ))),                           
                'order' => array('SalesPerson.name' => 'asc'),
                'recursive' =>-1
            ));

            $users=array();
            foreach ($users_data as $key => $value) {
                $u_id=$value['User']['id'];
                $u_name=$value['SalesPerson']['name'];
                $users[$u_id]=$u_name;
            }
            
            
       // pr($data); exit;
        $this->set(compact('data','distDistributors','office_id','dist_route_id','dist_distributor_id','offices','users','distRoutes','distsrs','dist_sr_id','request_data'));
    }
    
     public function get_dist_distributor_list() {
        $this->loadModel('Office');
        $this->loadModel('DistDistributor');
        $this->loadModel('DistRoute');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistRouteMappingHistory');
         
         $office_id=$this->request->data('office_id');
         $rs = array(array('id' => '', 'name' => '---- Select Distributor -----'));
         $data_array = array();
         $dist=$this->DistRouteMappingHistory->find('all',array('conditions'=>array('DistRouteMappingHistory.office_id'=>$office_id),'fields' => array('DISTINCT DistRouteMappingHistory.dist_distributor_id'),'recursive' =>0));
       
        if(!empty($dist))
        {
              $dist_ids=array();
              foreach ($dist as $key => $value) {
                  $dist_ids[]=$value['DistRouteMappingHistory']['dist_distributor_id'];
              }
              
              $dist_data = $this->DistDistributor->find('all', array('conditions' => array(
                 'DistDistributor.id' => $dist_ids,'DistDistributor.is_active'=>1,
             ),
             'order' => 'DistDistributor.name asc',
             'recursive' =>0));
              
         foreach ($dist_data as $key => $value) {
             $id=$value['DistDistributor']['id'];
             $name=$value['DistDistributor']['name'];
              $data_array[] = array(
                'id' => $id,
                'name' => $name,
            );
         }
              
        }
         

        if (!empty($data_array)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }
    
    
    public function get_dist_route_list() {
        $this->loadModel('Office');
        $this->loadModel('DistDistributor');
        $this->loadModel('DistRoute');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistRouteMappingHistory');
         
         $dist_distributor_id=$this->request->data('dist_distributor_id');
         $rs = array(array('id' => '', 'name' => '---- Select Route -----'));
         $data_array = array();
         $dist=$this->DistRouteMappingHistory->find('all',array('conditions'=>array('DistRouteMappingHistory.dist_distributor_id'=>$dist_distributor_id),'fields' => array('DISTINCT DistRouteMappingHistory.dist_route_id'),'recursive' =>0));
       
        if(!empty($dist))
        {
              $dist_ids=array();
              foreach ($dist as $key => $value) {
                  $dist_ids[]=$value['DistRouteMappingHistory']['dist_route_id'];
              }
              
              $dist_data = $this->DistRoute->find('all', array('conditions' => array(
                 'DistRoute.id' => $dist_ids
             ),
             'order' => 'DistRoute.name asc',
             'recursive' =>0));
              
         foreach ($dist_data as $key => $value) {
             $id=$value['DistRoute']['id'];
             $name=$value['DistRoute']['name'];
              $data_array[] = array(
                'id' => $id,
                'name' => $name,
            );
         }
              
        }
         

        if (!empty($data_array)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }
    
    public function get_sr_distributor_list() {
        $this->loadModel('Office');
        $this->loadModel('DistSalesRepresentative');
        $this->loadModel('DistRoute');
        $this->loadModel('DistSrRouteMapping');
        $this->loadModel('DistDistributor');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistSrRouteMappingHistory');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $office_id=$this->request->data('office_id');
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
           $sr_conditions = array('DistSalesRepresentative.dist_distributor_id'=>array_keys($tso_dist_list));
        }
        else{
            $sr_conditions = array('DistSrRouteMappingHistory.office_id'=>$office_id);
        }
         
         
         $rs = array(array('id' => '', 'name' => '---- Select Distributor -----'));
         $data_array = array();
         $dist=$this->DistSrRouteMappingHistory->find('all',array('conditions'=>$sr_conditions,'fields' => array('DISTINCT DistSrRouteMappingHistory.dist_distributor_id'),'recursive' =>0));
       
        if(!empty($dist))
        {
              $dist_ids=array();
              foreach ($dist as $key => $value) {
                  $dist_ids[]=$value['DistSrRouteMappingHistory']['dist_distributor_id'];
              }
              
              $dist_data = $this->DistDistributor->find('all', array('conditions' => array(
                 'DistDistributor.id' => $dist_ids,'DistDistributor.is_active'=>1,
             ),
             'order' => 'DistDistributor.name asc',
             'recursive' =>0));
              
         foreach ($dist_data as $key => $value) {
             $id=$value['DistDistributor']['id'];
             $name=$value['DistDistributor']['name'];
              $data_array[] = array(
                'id' => $id,
                'name' => $name,
            );
         }
              
        }
         

        if (!empty($data_array)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }
    
    public function get_sr_sr_list() {
        $this->loadModel('Office');
        $this->loadModel('DistSalesRepresentative');
        $this->loadModel('DistRoute');
        $this->loadModel('DistSrRouteMapping');
        $this->loadModel('DistDistributor');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistSrRouteMappingHistory');
         
         $dist_distributor_id=$this->request->data('dist_distributor_id');
         $rs = array(array('id' => '', 'name' => '---- Select SR -----'));
         $data_array = array();
         $dist=$this->DistSrRouteMappingHistory->find('all',array('conditions'=>array('DistSrRouteMappingHistory.dist_distributor_id'=>$dist_distributor_id),'fields' => array('DISTINCT DistSrRouteMappingHistory.dist_sr_id'),'recursive' =>0));
       
        if(!empty($dist))
        {
              $dist_ids=array();
              foreach ($dist as $key => $value) {
                  $dist_ids[]=$value['DistSrRouteMappingHistory']['dist_sr_id'];
              }
              
              $dist_data = $this->DistSalesRepresentative->find('all', array('conditions' => array(
                 'DistSalesRepresentative.id' => $dist_ids
             ),
             'order' => 'DistSalesRepresentative.name asc',
             'recursive' =>0));
              
         foreach ($dist_data as $key => $value) {
             $id=$value['DistSalesRepresentative']['id'];
             $name=$value['DistSalesRepresentative']['name'];
              $data_array[] = array(
                'id' => $id,
                'name' => $name,
            );
         }
              
        }
         

        if (!empty($data_array)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }
    public function get_dm_list() {
        $this->loadModel('Office');
        $this->loadModel('DistDeliveryMan');
        $this->loadModel('DistRoute');
        $this->loadModel('DistSrRouteMapping');
        $this->loadModel('DistDistributor');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistSrRouteMappingHistory');
         
         $dist_distributor_id=$this->request->data('dist_distributor_id');
         $rs = array(array('id' => '', 'name' => '---- Select -----'));
         $data_array = array();
         $dist=$this->DistSrRouteMappingHistory->find('all',array('conditions'=>array('DistSrRouteMappingHistory.dist_distributor_id'=>$dist_distributor_id),'fields' => array('DISTINCT DistSrRouteMappingHistory.dist_dm_id'),'recursive' =>0));
       
        if(!empty($dist))
        {
              $dist_ids=array();
              foreach ($dist as $key => $value) {
                  $dist_ids[]=$value['DistSrRouteMappingHistory']['dist_dm_id'];
              }
              
              $dist_data = $this->DistDeliveryMan->find('all', array('conditions' => array(
                 'DistDeliveryMan.id' => $dist_ids
             ),
             'order' => 'DistDeliveryMan.name asc',
             'recursive' =>0));
              
            foreach ($dist_data as $key => $value) {
                 $id=$value['DistDeliveryMan']['id'];
                 $name=$value['DistDeliveryMan']['name'];
                  $data_array[] = array(
                    'id' => $id,
                    'name' => $name,
                );
            }
              
        }
         

        if (!empty($data_array)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }
    public function get_sr_route_list() {
        $this->loadModel('Office');
        $this->loadModel('DistSalesRepresentative');
        $this->loadModel('DistRoute');
        $this->loadModel('DistSrRouteMapping');
        $this->loadModel('DistDistributor');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistSrRouteMappingHistory');
         
         $dist_sr_id=$this->request->data('dist_sr_id');
         $rs = array(array('id' => '', 'name' => '---- Select Route -----'));
         $data_array = array();
         $dist=$this->DistSrRouteMappingHistory->find('all',array('conditions'=>array('DistSrRouteMappingHistory.dist_sr_id'=>$dist_sr_id),'fields' => array('DISTINCT DistSrRouteMappingHistory.dist_route_id'),'recursive' =>0));
       
        if(!empty($dist))
        {
              $dist_ids=array();
              foreach ($dist as $key => $value) {
                  $dist_ids[]=$value['DistSrRouteMappingHistory']['dist_route_id'];
              }
              
              $dist_data = $this->DistRoute->find('all', array('conditions' => array(
                 'DistRoute.id' => $dist_ids
             ),
             'order' => 'DistRoute.name asc',
             'recursive' =>0));
              
         foreach ($dist_data as $key => $value) {
             $id=$value['DistRoute']['id'];
             $name=$value['DistRoute']['name'];
              $data_array[] = array(
                'id' => $id,
                'name' => $name,
            );
         }
              
        }
         

        if (!empty($data_array)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function get_dm_route_list() {
        $this->loadModel('Office');
        $this->loadModel('DistDeliveryMan');
        $this->loadModel('DistRoute');
        $this->loadModel('DistSrRouteMapping');
        $this->loadModel('DistDistributor');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistSrRouteMappingHistory');
         
         $dist_dm_id=$this->request->data('dist_sr_id');
         $rs = array(array('id' => '', 'name' => '---- Select Route -----'));
         $data_array = array();
         $dist=$this->DistSrRouteMappingHistory->find('all',array('conditions'=>array('DistSrRouteMappingHistory.dist_dm_id'=>$dist_dm_id),'fields' => array('DISTINCT DistSrRouteMappingHistory.dist_route_id'),'recursive' =>0));
       
        if(!empty($dist))
        {
            $dist_ids=array();
            foreach ($dist as $key => $value) {
              $dist_ids[]=$value['DistSrRouteMappingHistory']['dist_route_id'];
            }

            $dist_data = $this->DistRoute->find('all', array('conditions' => array(
                'DistRoute.id' => $dist_ids
                ),
                'order' => 'DistRoute.name asc',
                'recursive' =>0
            ));

            foreach ($dist_data as $key => $value) {
                $id=$value['DistRoute']['id'];
                $name=$value['DistRoute']['name'];
                $data_array[] = array(
                    'id' => $id,
                    'name' => $name,
                );
            }
              
        }
         

        if (!empty($data_array)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }
    public function get_thana_list(){
        $this->loadModel('Territory');
        $this->loadModel('ThanaTerritory');
        $rs = array(array('id' => '', 'name' => '---- All -----'));
        $office_id = $this->request->data['office_id'];
        $district_id = $this->request->data['district_id'];
        $territory = $this->Territory->find('all', array(
            'fields' => array('Territory.id'),
            'conditions' => array('Territory.office_id' => $office_id),
            'order' => array('Territory.name' => 'asc'),
            'recursive' => 0
        ));
        
        //pr($territory);die();
        
        //$data_array = Set::extract($territory, '{n}.Territory');
        
        $data_array = array();
        $territories = array();
        
        foreach($territory as $key => $value)
        {
            $territories[$key] = $value['Territory']['id'];
        }
        
        //pr($data_array);die();
       $thana_list = $this->ThanaTerritory->find('all',array(
        'conditions'=>array(
           'ThanaTerritory.territory_id' => $territories,
           'Thana.district_id'=>$district_id
           ),
        'order'=>array('Thana.name ASC'),
		'fields' => 'DISTINCT Thana.id, Thana.name'
       ));

       foreach($thana_list as $key => $value)
        {
            $data_array[] = array(
                'id' => $value['Thana']['id'],
                'name' => $value['Thana']['name'],
            );
        }

        
        if(!empty($thana_list)){
            echo json_encode(array_merge($rs,$data_array));
        }else{
            echo json_encode($rs);
        } 
        $this->autoRender = false;
    }

    public function get_district_list(){
        $this->loadModel('Territory');
        $this->loadModel('ThanaTerritory');
        $rs = array(array('id' => '', 'name' => '---- All -----'));
        $office_id = $this->request->data['office_id'];
        $territory = $this->Territory->find('all', array(
            'fields' => array('Territory.id'),
            'conditions' => array('Territory.office_id' => $office_id),
            'order' => array('Territory.name' => 'asc'),
            'recursive' => 0
        ));
        
        //pr($territory);die();
        
        //$data_array = Set::extract($territory, '{n}.Territory');
        
        $data_array = array();
        $territories = array();
        
        foreach($territory as $key => $value)
        {
            $territories[$key] = $value['Territory']['id'];
           
        }
        
        //pr($data_array);die();
       $district_list = $this->ThanaTerritory->find('all',array('conditions'=>array(
           'ThanaTerritory.territory_id' => $territories),
            'joins'=>array(
                array(
                    'table'=>'districts',
                    'alias'=>'District',
                    'conditions'=>'District.id=Thana.district_id'
                    ),
                ),
           'order'=>array('District.name ASC'),
           'fields' => 'DISTINCT District.id, District.name'
       ));
       /*echo $this->ThanaTerritory->getLastQuery();
       pr($thana_list);exit;*/

       foreach($district_list as $key => $value)
        {
            $data_array[] = array(
                'id' => $value['District']['id'],
                'name' => $value['District']['name'],
            );
        }

        
        if(!empty($district_list)){
            echo json_encode(array_merge($rs,$data_array));
        }else{
            echo json_encode($rs);
        } 
        $this->autoRender = false;
    }

}
