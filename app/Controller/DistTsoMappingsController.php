<?php

App::uses('AppController', 'Controller');

/**
 * Territories Controller
 *
 * @property Territory $Territory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistTsoMappingsController extends AppController {

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
        $this->set('page_title', 'Tso Mapping List');
        $office_id=0;
        
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistTsoMappingHistory');
        $dist_tso_id=0;
        $dist_tsos=array();
        $is_data_post=0;
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        if ($this->request->is('post')) {
           //pr($this->request->data); exit;
            $dist_tso_id = $this->request->data['DistTsoMapping']['dist_tso_id'];
            $office_id=$this->request->data['DistTsoMapping']['office_id'];
            $is_data_post=1;
            
            if($office_id)
            {
                $dist_tsos = $this->DistTso->find('list', array(
                    'conditions' => array(
                        'DistTso.office_id' => $office_id, 'DistTso.is_active' => 1
                    )
                ));
            }
            
            $this->set(compact('dist_tso_id','office_id','dist_tsos','is_data_post'));
            
            $this->request->data['DistTsoMapping']['created_at'] = $this->current_datetime();
            $this->request->data['DistTsoMapping']['created_by'] = $this->UserAuth->getUserId();
            $this->request->data['DistTsoMapping']['updated_at'] = $this->current_datetime();
            $this->request->data['DistTsoMapping']['updated_by'] = $this->UserAuth->getUserId();
            $array = array();
            $i = 0;
            $update_history_qry="";   
            $update_remove_history_qry="";
            /******************* Check same data start ********************/
            
              if ((array_key_exists('DistDistributor', $this->request->data)) && (array_key_exists('id', $this->request->data['DistDistributor']))) {
                foreach ($this->request->data['DistDistributor']['id'] as $key => $value) {
                    $array[$i]['dist_distributor_id'] = $value;
                    $array[$i]['office_id'] = $office_id;
                    $array[$i]['dist_tso_id'] = $dist_tso_id;
                    $effective_date=date("Y-m-d H:i:s", strtotime($this->request->data['DistDistributor']['effective_date'][$value]));
                    $array[$i]['effective_date'] = $effective_date;
                    $array[$i]['created_at'] = $this->request->data['DistTsoMapping']['created_at'];
                    $array[$i]['created_by'] = $this->request->data['DistTsoMapping']['created_by'];
                    $array[$i]['updated_at'] = $this->request->data['DistTsoMapping']['updated_at'];
                    $array[$i]['updated_by'] = $this->request->data['DistTsoMapping']['updated_by'];
                    $array[$i]['is_active'] = 1;
                    $array[$i]['is_assign'] = 1;
                    $array[$i]['is_change'] = 1;
                    
                       
                     
                       /********************************* Checking Old Data start **********************************************/
                         if(array_key_exists('dist_distributor_id', $this->request->data['DistTsoMapping']))
                         {
                             /************** Working for Previous Data ***************/
                            if(array_key_exists($key, $this->request->data['DistTsoMapping']['dist_distributor_id']))
                            {
                             $old_effective_date=date("Y-m-d H:i:s", strtotime($this->request->data['DistTsoMapping']['dist_distributor_id'][$key]['effective_date'])); 
                             
                        /********************************* Checking Valid effective date start **********************************************/
                             
                                if (new DateTime($old_effective_date) > new DateTime($effective_date)) {
                                    $this->Session->setFlash(__('New Effective should not be less than the existing effective date'), 'flash/error');
                                    //$this->redirect(array('action' => "index"));
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
                                 if (new DateTime($curr_effective_date) > new DateTime($effective_date)) {
                                  //  $this->Session->setFlash(__('Effective date can not be any past date'), 'flash/error');
                                   // $this->redirect(array('action' => "index"));
                                }
                                
                                
                                  if($update_history_qry)
                                  {
                                      $update_history_qry=$update_history_qry.";update dist_tso_mapping_histories set active=0,end_date='$effective_date' where office_id = $office_id and dist_distributor_id=$key and dist_tso_id = $dist_tso_id and active=1 and is_assign=1 and end_date is NULL";
                                  }
                                  else 
                                  {
                                     $update_history_qry="update dist_tso_mapping_histories set active=0,end_date='$effective_date' where office_id = $office_id and dist_distributor_id=$key and dist_tso_id = $dist_tso_id and active=1 and is_assign=1 and end_date is NULL"; 
                                  }
                                   
                               }
                            }
                            else 
                            {
                                /**************** Working for New Data ****************/
                                   $curr_effective_date=date("Y-m-d");
                                 if (new DateTime($curr_effective_date) > new DateTime($effective_date)) {
                                   // $this->Session->setFlash(__('Effective End Date can not be any past date'), 'flash/error');
                                   // $this->redirect(array('action' => "index"));
                                }
                            }
                         }
                    
                       /********************************* Checking Old Data End **********************************************/
                    
                    
                    $i++;
                }
            }
          
           // pr($array); exit;
            $this->DistTsoMapping->query("delete from dist_tso_mappings where dist_tso_id = $dist_tso_id and office_id=$office_id");
            
            if($update_history_qry)
            {
                $this->DistTsoMapping->query($update_history_qry);
            }
            
            
            /*********************  Checking data those are removed  start **************************/
            
            if ((array_key_exists('DistTsoMapping', $this->request->data)) && (array_key_exists('dist_distributor_id', $this->request->data['DistTsoMapping']))) {
                foreach ($this->request->data['DistTsoMapping']['dist_distributor_id'] as $key => $value) {
                    
                     if( (!array_key_exists('id', $this->request->data['DistDistributor'])) || (!array_key_exists($key, $this->request->data['DistDistributor']['id'])))
                            {
                             $effective_date=date("Y-m-d H:i:s", strtotime($this->request->data['DistDistributor']['effective_date'][$key]));
                             
                                 
                                 $curr_effective_date=date("Y-m-d");
                                 if (new DateTime($curr_effective_date) > new DateTime($effective_date)) {
                                   // $this->Session->setFlash(__('Effective End Date can not be any past date'), 'flash/error');
                                   // $this->redirect(array('action' => "index"));
                                }
                             
                             if($update_remove_history_qry)
                                  {
                                      $update_remove_history_qry=$update_remove_history_qry.";update dist_tso_mapping_histories set active=0,is_assign=0,end_date='$effective_date' where office_id = $office_id and dist_distributor_id=$key and dist_tso_id = $dist_tso_id and active=1 and is_assign=1 and end_date is NULL";
                                  }
                                  else 
                                  {
                                     $update_remove_history_qry="update dist_tso_mapping_histories set active=0,is_assign=0,end_date='$effective_date' where office_id = $office_id and dist_distributor_id=$key and dist_tso_id = $dist_tso_id and active=1 and is_assign=1 and end_date is NULL"; 
                                  }
                           }       
                }
            }
            
            if($update_remove_history_qry)
            {
                $this->DistTsoMapping->query($update_remove_history_qry);
            }
            
            /*********************  Checking data those are removed  end  **************************/
            
           
               if(!empty($array))
               {
                   if ($this->DistTsoMapping->saveAll($array)) {
                    $this->DistTsoMappingHistory->saveAll($array);
                   
                    $this->Session->setFlash(__('TSO Mapping has been Completed.'), 'flash/success');
                    //$this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash(__('There has some problem!'), 'flash/error');
                    }
               }
               else 
               {
                    $this->Session->setFlash(__('TSO Mapping has been Completed.'), 'flash/success');
                    //$this->redirect(array('action' => 'index'));
               }
                
        }
        
        $this->loadModel('DistTso');
        $this->loadModel('User');
        $this->loadModel('Office');
        $this->set(compact('dist_tso_id','office_id','dist_tsos','is_data_post'));
        $tso_conditions = array();
        $office_parent_id = $this->UserAuth->getOfficeParentId();
         if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
            $tso_conditions = array(
              'DistTso.office_id' => $office_id,'DistTso.is_active' => 1
            );
        } else {
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
                $tso_conditions = array('DistTso.id' => $dist_tso_id);
            }
            else{
                $tso_conditions = array(
                  'DistTso.office_id' => $office_id,'DistTso.is_active' => 1
                );
            }
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
        //$office_id = $this->UserAuth->getOfficeId();
        //Need to add office Id
        //$data = $this->User->query("select users.id,sales_people.name,sales_people.office_id from users inner join sales_people on users.sales_person_id=sales_people.id Inner join user_groups on user_groups.id=users.user_group_id where user_groups.name='TSO'");
        

        //Need to add office Id
        /* $data = $this->User->query("select users.id,sales_people.name,sales_people.office_id from users inner join sales_people on users.sales_person_id=sales_people.id Inner join user_groups on user_groups.id=users.user_group_id where user_groups.name='TSO'"); 
          $distTsos = array();
          $i = 0;
          foreach ($data as $key => $value) {
          $distTsos[$value[0]['id']] = $value[0]['name'];
          $i++;
          } */
        //$distTsos = $this->DistTso->find('list', array('conditions' => $conditions, 'order' => array('id' => 'asc')));
       
        
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        $this->set(compact('offices'));
        
        $distTsos = $this->DistTso->find('list', array('conditions' => $tso_conditions,
            'order' => 'DistTso.name ASC',
            'recursive' => -1));
        
         $this->set(compact('distTsos'));
         
         
         
         if($is_data_post)
         {
            $this->loadModel('DistDistributor');
            $distTso_distributors = $this->DistTsoMapping->find('all', array('conditions' => array(
                    'DistTsoMapping.office_id' => $office_id,'DistTsoMapping.dist_tso_id !=' => $dist_tso_id
                ),
                'fields' => array('DistTsoMapping.dist_distributor_id'),
                'order' => 'DistTsoMapping.id asc',
                'recursive' => -1));

            $curr_mapping_data=array();
            foreach ($distTso_distributors as $key => $value) {
                $curr_mapping_data[]=$value['DistTsoMapping']['dist_distributor_id'];
            }

            if($curr_mapping_data)
            {
                $conditions=array('DistDistributor.office_id' => $office_id,
                    'DistDistributor.is_active' => 1,
                    'NOT'=>array('DistDistributor.id '=>$curr_mapping_data));
            }
            else 
            {
                $conditions=array('DistDistributor.office_id' => $office_id,'DistDistributor.is_active' => 1);
            }


            $distDistributors = $this->DistDistributor->find('all', array('conditions' =>$conditions,
                'order' => 'DistDistributor.name ASC',
                'recursive' => -1));

            $this->set(compact('distDistributors'));
             
         }
    }

    public function get_tso_mapping_id($office_id = 0, $dist_tso_id = 0, $dist_distributor_id = 0) {
        $this->loadModel('DistTsoMapping');
        $data=array();
        $distTsoMappings = $this->DistTsoMapping->find('first', array('conditions' => array(
                'DistTsoMapping.office_id' => $office_id,
                'DistTsoMapping.dist_tso_id' => $dist_tso_id,
                'DistTsoMapping.dist_distributor_id' => $dist_distributor_id,
            ),
            'order' => 'DistTsoMapping.id DESC',
            'recursive' => -1));
       
        if (!empty($distTsoMappings)) {
            $data=$distTsoMappings['DistTsoMapping'];
            return $data;
        } else {
            return $data;
        }
    }
    
    
        public function get_mapping_end_date($office_id = 0, $dist_tso_id = 0, $dist_distributor_id = 0) {
        $this->loadModel('DistTsoMappingHistory');
        $data=array();
        $distTsoMappings = $this->DistTsoMappingHistory->find('first', array('conditions' => array(
                'DistTsoMappingHistory.office_id' => $office_id,
                'DistTsoMappingHistory.dist_tso_id' => $dist_tso_id,
                'DistTsoMappingHistory.dist_distributor_id' => $dist_distributor_id,
            ),
            'order' => 'DistTsoMappingHistory.id DESC',
            'recursive' => -1));
       
        if (!empty($distTsoMappings)) {
            $data=$distTsoMappings['DistTsoMappingHistory'];
            return $data;
        } else {
            return $data;
        }
    }
    
    
public function get_distributor_list() {
         $this->loadModel('DistDistributor');
         $this->loadModel('DistTso');
         $this->loadModel('DistTsoMapping');
         $dist_tso_id=$this->request->data('dist_tso_id');
         $office_id=$this->request->data('office_id');
         /*
         $distTsos = $this->DistTso->find('first', array('conditions' => array(
                 'DistTso.id' => $this->request->data('dist_tso_id'),
             ),
             'order' => 'DistTso.id DESC',
             'recursive' => -1));
          * 
          */
         
         $distTso_distributors = $this->DistTsoMapping->find('all', array('conditions' => array(
                 'DistTsoMapping.office_id' => $office_id,'DistTsoMapping.dist_tso_id !=' => $dist_tso_id
             ),
             'fields' => array('DistTsoMapping.dist_distributor_id'),
             'order' => 'DistTsoMapping.id asc',
             'recursive' => -1));
       
         $curr_mapping_data=array();
         foreach ($distTso_distributors as $key => $value) {
             $curr_mapping_data[]=$value['DistTsoMapping']['dist_distributor_id'];
         }
               
         if($curr_mapping_data)
         {
             $conditions=array('DistDistributor.office_id' => $office_id,
                 'DistDistributor.is_active' => 1,
                 'NOT'=>array('DistDistributor.id '=>$curr_mapping_data));
         }
         else 
         {
             $conditions=array('DistDistributor.office_id' => $office_id,'DistDistributor.is_active' => 1);
         }
         
         
         $distDistributors = $this->DistDistributor->find('all', array('conditions' =>$conditions,
             'order' => 'DistDistributor.name ASC',
             'recursive' => -1));
        
         $this->set(compact('distDistributors'));
     }
     
     public function admin_history() {
		 
		 
        $this->set('page_title', 'TSO and Distributor Mapping History');
        
        //pr($this->request->data);exit;
        $this->loadModel('Office');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistTsoMappingHistory');
        $this->loadModel('DistDistributor');
        
        $office_id=0;
        $dist_tso_id=0;
        $dist_distributor_id=0;
        $distTsos=array();
        $distDistributors=array();
        $tso=array();
        $dist=array();
        $page_conditions=array();
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $tso_id = 0;
        $dist_tso_info = array();
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
            $page_conditions = array();
        }else {
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
                        'fields'=> array('DistTso.id','DistTso.name'),
                    ));
                    
                    $tso_id = array_keys($dist_tso_info);
                }
                else{
                    $dist_tso_info = $this->DistTso->find('first',array(
                        'conditions'=>array('DistTso.user_id'=>$user_id),
                        'recursive'=> -1,
                    ));
                    $tso_id = $dist_tso_info['DistTso']['id'];
                }
                $page_conditions = array('DistTsoMappingHistory.dist_tso_id'=>$tso_id);
            }else{
                $page_conditions = array('DistTsoMappingHistory.office_id'=>$this->UserAuth->getOfficeId());
            }

            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
                
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        
        /***** TSO Start  ******/
        if($user_group_id == 1029 || $user_group_id == 1028)
        {
            if($user_group_id == 1028){
                $distTsos = $dist_tso_info;
            }else{
                $distTsos[$dist_tso_info['DistTso']['id']]=$dist_tso_info['DistTso']['name'];
            }

            $dist_con = array('DistTsoMappingHistory.dist_tso_id'=>$tso_id);
        }
        else{
            $tso=$this->DistTsoMappingHistory->find('all',array('fields' => array('DISTINCT DistTsoMappingHistory.dist_tso_id'),'recursive' =>0));
        
            if(!empty($tso))
            {
                  $dist_tso_ids=array();
                  foreach ($tso as $key => $value) {
                      $dist_tso_ids[]=$value['DistTsoMappingHistory']['dist_tso_id'];
                  }
                  
                  $distTso = $this->DistTso->find('all', array('conditions' => array(
                     'DistTso.id' => $dist_tso_ids
                 ),
                 'order' => 'DistTso.name asc',
                 'recursive' =>0));
                  
                foreach ($distTso as $key => $value) {
                    $id=$value['DistTso']['id'];
                    $distTsos[$id]=$value['DistTso']['name'];
                }
                  
            }

            $dist_con = array('DistTsoMappingHistory.office_id'=>$this->UserAuth->getOfficeId());
            
        }

        $dist=$this->DistTsoMappingHistory->find('all',array(
                'conditions'=>array('DistTsoMappingHistory.dist_tso_id'=>$tso_id),
                'fields' => array('DISTINCT DistTsoMappingHistory.dist_distributor_id'),
                'recursive' =>0
            ));

            if(!empty($dist))
            {
                $dist_ids=array();
                foreach ($dist as $key => $value) {
                    $dist_ids[]=$value['DistTsoMappingHistory']['dist_distributor_id'];
                }

                $dist_data = $this->DistDistributor->find('all', array('conditions' => array(
                    'DistDistributor.id' => $dist_ids,'DistDistributor.is_active' => 1
                    ),
                    'order' => 'DistDistributor.name asc',
                    'recursive' =>0
                ));

                foreach ($dist_data as $key => $value) {
                    $id=$value['DistDistributor']['id'];
                    $distDistributors[$id]=$value['DistDistributor']['name'];
                }
            }
          /***** Distributor End  ******/
        
        
        if ($this->request->is('post')) {
            $office_id=(isset($this->request->data['DistTsoMapping']['office_id']))?$this->request->data['DistTsoMapping']['office_id']:0;
            $dist_tso_id=(isset($this->request->data['DistTsoMapping']['dist_tso_id']))?$this->request->data['DistTsoMapping']['dist_tso_id']:0;
            $dist_distributor_id=(isset($this->request->data['DistTsoMapping']['dist_distributor_id']))?$this->request->data['DistTsoMapping']['dist_distributor_id']:0;
            $dist_date_from=date("Y-m-d H:i:s", strtotime($this->request->data['DistTsoMapping']['date_from']));
            $dist_date_to=date("Y-m-d H:i:s", strtotime($this->request->data['DistTsoMapping']['date_to']));
          
            if($office_id)
                {
                    if($user_group_id == 1029 || $user_group_id == 1028){
                        $off_con = array('DistTso.id' => $tso_id);
                    }
                    else{
                        $off_con = array('DistTso.office_id' => $office_id);
                    }
                    $distTsos = $this->DistTso->find('list', array('conditions' => $off_con,
                    'order' => 'DistTso.name ASC',
                    'recursive' => -1));
                }
            
            if($office_id)
                {
                    if($user_group_id == 1029 || $user_group_id == 1028){
                        
                        $tso_dist_list = $this->DistTsoMapping->find('list',array(
                            'conditions'=> array(
                            'dist_tso_id' => $tso_id,
                            ),
                            'fields'=>array('DistTsoMapping.dist_distributor_id','DistTsoMapping.dist_tso_id'),
                        ));
                        $dist_con = array('DistDistributor.id' => array_keys($tso_dist_list));
                    }
                    else{
                        $dist_con = array('DistDistributor.office_id' => $office_id,'DistDistributor.is_active' => 1);
                    }

                $distDistributors = $this->DistDistributor->find('list', array('conditions' => $dist_con,
                    'order' => 'DistDistributor.name ASC',
                    'recursive' => -1));
                }
                
                if($office_id)
                {
                    $page_conditions['DistTsoMappingHistory.office_id']=$office_id;
                }
                
                if($dist_tso_id)
                {
                  $page_conditions['DistTsoMappingHistory.dist_tso_id']=$dist_tso_id;  
                }
                
                if($dist_distributor_id)
                {
                  $page_conditions['DistTsoMappingHistory.dist_distributor_id']=$dist_distributor_id;  
                }
                
                if($dist_date_from)
                {
                  $page_conditions['DistTsoMappingHistory.effective_date >=']=$dist_date_from;  
                }
                
                if($dist_date_to)
                {
                    
                  $page_conditions['case 
                    when DistTsoMappingHistory.end_date is not null then 
                     DistTsoMappingHistory.end_date
                    else 
                    convert(varchar(10),getDate(), 120)
                    end <=']=$dist_date_to;  
                }
                
                
                 
        }
     
        $this->paginate = array(
            'conditions' => $page_conditions,
            'recursive' => 0
        );
        
        $data = $this->paginate('DistTsoMappingHistory');
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
            
            
        
        $this->set(compact('data','distTsos','distDistributors','office_id','dist_tso_id','dist_distributor_id','offices','users'));
    }
    
    public function get_dist_tso_list() {
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMappingHistory');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistAreaExecutive');
        $rs = array(array('id' => '', 'name' => '---- All -----'));
        $office_id = $this->request->data['office_id'];
        $data_array = array();

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
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
            $conditions = array('DistTsoMappingHistory.dist_tso_id'=>$dist_tso_id);
        }else{
            $conditions = array('DistTsoMappingHistory.office_id'=>$office_id);
        }
        $tso=$this->DistTsoMappingHistory->find('all',array('conditions'=>$conditions,'fields' => array('DISTINCT DistTsoMappingHistory.dist_tso_id'),'recursive' =>0));
        
        if(!empty($tso))
        {
              $dist_tso_ids=array();
              foreach ($tso as $key => $value) {
                  $dist_tso_ids[]=$value['DistTsoMappingHistory']['dist_tso_id'];
              }
              
              $distTso = $this->DistTso->find('all', array('conditions' => array(
                 'DistTso.id' => $dist_tso_ids
             ),
             'order' => 'DistTso.name asc',
             'recursive' =>0));
              
         foreach ($distTso as $key => $value) {
             $id=$value['DistTso']['id'];
             $name=$value['DistTso']['name'];
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
    
     public function get_dist_list() {
         $this->loadModel('DistTsoMappingHistory');
         $this->loadModel('DistTso');
         $this->loadModel('DistTsoMapping');
         $this->loadModel('DistDistributor');
         
         $dist_tso_id=$this->request->data('dist_tso_id');
         $rs = array(array('id' => '', 'name' => '---- All -----'));
         $data_array = array();
         $dist=$this->DistTsoMappingHistory->find('all',array('conditions'=>array('DistTsoMappingHistory.dist_tso_id'=>$dist_tso_id),'fields' => array('DISTINCT DistTsoMappingHistory.dist_distributor_id'),'recursive' =>0));
       
        if(!empty($dist))
        {
              $dist_ids=array();
              foreach ($dist as $key => $value) {
                  $dist_ids[]=$value['DistTsoMappingHistory']['dist_distributor_id'];
              }
              
              $dist_data = $this->DistDistributor->find('all', array('conditions' => array(
                 'DistDistributor.id' => $dist_ids,'DistDistributor.is_active' => 1
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

}
