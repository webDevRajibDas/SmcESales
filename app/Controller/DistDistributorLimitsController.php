<?php

App::uses('AppController', 'Controller');

/**
 * Territories Controller
 *
 * @property Territory $Territory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistDistributorLimitsController extends AppController {

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
        $this->set('page_title', 'Distributor Limit List');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $this->loadModel('Office');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $office_id = $this->UserAuth->getOfficeId();   

		if ($office_parent_id == 0) 
		{
            $conditions = array();
            $office_conditions = array();
            $dist_conditions = array('is_active'=> 1);
        } 
		else 
		{
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
              
               $conditions = array('DistDistributorLimit.dist_distributor_id' => array_keys($tso_dist_list));
               $dist_conditions = array('DistDistributor.id'=>array_keys($tso_dist_list),'is_active'=> 1);
               
            }
            elseif($user_group_id == 1034){
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first',array(
                    'conditions'=>array('DistUserMapping.sales_person_id'=>$sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
        
                $conditions = array('DistDistributorLimit.dist_distributor_id' =>$distributor_id);
               $dist_conditions = array('DistDistributor.id'=>$distributor_id,'is_active'=> 1);
            }
            else{
                $conditions = array('DistDistributorLimit.office_id' => $office_id);
                $dist_conditions = array('DistDistributor.office_id'=>$office_id,'is_active'=> 1);
            }
           
            $office_conditions = array('office_type_id' => 2, 'Office.id' => $this->UserAuth->getOfficeId()); 
        }
        
        $this->paginate = array(
            'fields'=> array('DistDistributorLimit.*','DistDistributor.id','DistDistributor.name'),
            'conditions' => $conditions,
            'recursive' => 0,
            'order' => array('DistDistributorLimit.effective_date' => 'DESC')
        );

        $this->loadModel('Office');           
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
           

        //pr($this->paginate());die();
     

        //================end==25-6-19====//

        $this->set('DistDistributorLimits', $this->paginate());
        $this->loadModel('DistDistributor');
        $office_ids=array();
       
        $distDistributors = $this->DistDistributor->find('list',array('conditions'=>$dist_conditions));
        //pr($distDistributors);die();
        $this->set(compact('offices'));
        $this->set(compact('distDistributors'));
    }

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_view($id = null) {
        $this->set('page_title', 'Distributor Limit Details');
        if (!$this->DistDistributorLimit->exists($id)) {
            throw new NotFoundException(__('Invalid DistAreaExecutive'));
        }
        $options = array('conditions' => array('DistDistributorLimit.' . $this->DistDistributorLimit->primaryKey => $id));
        $this->loadModel('DistDistributorLimitHistory');
        $limit_history = $this->DistDistributorLimitHistory->find('all',array('conditions'=>array(
            'DistDistributorLimitHistory.dist_distributor_limit_id' => $id),
        'order' => array('DistDistributorLimitHistory.id DESC'),
    ));
        //pr($limit_history);die();
        $this->set(compact('limit_history'));
        $this->set('limit', $this->DistDistributorLimit->find('first', $options));
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add() {
        $this->set('page_title', 'Dealer Wise Limit'); 
        
        
        //for company and office list

        $this->loadModel('DistDistributor');
        $this->loadModel('Office');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $designation_id=$this->Session->read('Office.designation_id');

        $this->set(compact('office_parent_id'));
        if ($office_parent_id == 0) 
        {
            $office_conditions = array(
            'office_type_id' => 2,
            "NOT" => array( "id" => array(30, 31, 37))
            );
            $dist_conditions = array('is_active'=> 1);
        }
        else
        {
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
              
               $conditions = array('DistDistributorLimit.dist_distributor_id' => array_keys($tso_dist_list));
               $dist_conditions = array('DistDistributor.id'=>array_keys($tso_dist_list),'is_active'=> 1);
               
            }
            else{
                $conditions = array('DistDistributorLimit.office_id' => $this->UserAuth->getOfficeId());
                $dist_conditions = array('DistDistributor.office_id'=>$this->UserAuth->getOfficeId(),'is_active'=> 1);
            }

            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
            $office_id = $office_conditions['Office.id'];
            
        }

        $distDistributors = $this->DistDistributor->find('list', array('conditions' =>$dist_conditions));

        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
        $this->set(compact('distDistributors'));

        $this->set(compact('offices'));
        
        if ($this->request->is('post')) {
           //pr($this->request->data);die();
            //============golam rabbi ======//
           
           
            $office_id = $this->request->data['DistDistributorLimit']['office_id'];
            $dist_distributor_id = $this->request->data['DistDistributorLimit']['dist_distributor_id'];

            $check_dist_distributor_id = $this->DistDistributorLimit->find('first', array(
                'conditions' => array(
                    'DistDistributorLimit.office_id' => $office_id,
                    'DistDistributorLimit.dist_distributor_id' => $dist_distributor_id,

                ),'order' => array('DistDistributorLimit.id' => 'DESC'),'limit' =>1,
                'recursive' => -1
            ));

            if(!empty($check_dist_distributor_id)){
                $data['id'] = $check_dist_distributor_id['DistDistributorLimit']['id'];
                $data['max_amount'] =$check_dist_distributor_id['DistDistributorLimit']['max_amount'] + $this->request->data['DistDistributorLimit']['max_amount']; 
                
            }
            else{
                $data['office_id'] = $this->request->data['DistDistributorLimit']['office_id'];
                $data['dist_distributor_id'] = $this->request->data['DistDistributorLimit']['dist_distributor_id'];
                $data['max_amount'] = $this->request->data['DistDistributorLimit']['max_amount'];
                $data['is_active'] = 1;
                $data['created_at'] = $this->current_datetime();
                $data['created_by'] = $this->UserAuth->getUserId();
                $data['updated_at'] = $this->current_datetime();
                $data['updated_by'] = $this->UserAuth->getUserId();

                $this->DistDistributorLimit->create();
            }

           //============ end golam rabbi=========//

            if($this->DistDistributorLimit->save($data))
            {
                if(!empty($check_dist_distributor_id)){
                    $id = $check_dist_distributor_id['DistDistributorLimit']['id'];
                    $limit_history['max_amount'] =$check_dist_distributor_id['DistDistributorLimit']['max_amount'] + $this->request->data['DistDistributorLimit']['max_amount'];
                }
                else{
                    $id = $this->DistDistributorLimit->getLastInsertID();
                    $limit_history['max_amount'] = $this->request->data['DistDistributorLimit']['max_amount'];
                }
                $this->loadModel('DistDistributorLimitHistory');

                $limit_history['dist_distributor_limit_id'] = $id;
                $limit_history['transaction_amount'] = $this->request->data['DistDistributorLimit']['max_amount'];
                $limit_history['transaction_type'] = 1;
                $limit_history['is_active'] = 1;

                $this->DistDistributorLimitHistory->create();
                if($this->DistDistributorLimitHistory->save($limit_history)){
                    $this->Session->setFlash(__('Limit added successfully!'), 'flash/success');
                    $this->redirect(array('action' => 'index'));
                }
                 
            }else{
                $this->Session->setFlash(__('The Dealer Wise Limit could not be saved. Please, try again.'), 'flash/error');
                $this->redirect(array('action' => 'index'));
            }
        }

        
    }

    /**
     * admin_edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_edit($id = null) {
        $this->set('page_title', 'Dealer Wise Limit');
        $this->DistDistributorLimit->id = $id;
        if (!$this->DistDistributorLimit->exists($id)) {
            throw new NotFoundException(__('Invalid Dealer Wise Limit'));
        }
        $this->check_data_by_company('DistDistributorLimit',$id);
        //for company and office list
        $this->loadModel('Company');
        $companies = $this->Company->find('list', array());
        $this->set(compact('companies'));
        
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $this->set(compact('office_parent_id'));
        if ($office_parent_id == 0) 
        {
            $office_conditions = array(
            'office_type_id' => 2,
            "NOT" => array( "id" => array(30, 31, 37))
            );
        }
        else
        {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
            $office_id = $office_conditions['Office.id'];
            $company_id=$this->Session->read('Office.company_id');
        }
        
       
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

        $this->set(compact('offices'));
        //for company and office list
        
        
        $this->loadModel('DistDistributorLimit');
        if ($this->request->is('post') || $this->request->is('put')) {
            //pr($this->request->data);die();
            $this->request->data['DistDistributorLimit']['updated_by'] = $this->UserAuth->getUserId();
            $this->request->data['DistDistributorLimit']['effective_date'] = date("Y-m-d", strtotime($this->request->data['DistDistributorLimit']['effective_date']));
            $this->request->data['DistDistributorLimit']['updated_at'] = $this->current_datetime();

            $this->DistDistributorLimit->create();
            if($this->DistDistributorLimit->save($this->request->data['DistDistributorLimit'])){
                $DistDistributorLimitHistoryUpdate['id']=$this->request->data['DistDistributorLimitHistory']['id'];
                $DistDistributorLimitHistoryUpdate['effective_end_date']=$this->request->data['DistDistributorLimit']['effective_date'];
                //pr($DistDistributorLimitHistoryUpdate);die();
                $this->loadModel('DistDistributorLimitHistory');
                $this->DistDistributorLimitHistory->create();
                $this->DistDistributorLimitHistory->save($DistDistributorLimitHistoryUpdate); 

                /*-----------Insert into DistDistributorLimitHistory Table-----------*/
                //$DistDistributorLimitHistory['DistDistributorLimitHistory']=$this->request->data['DistDistributorLimit'];
                $DistDistributorLimitHistory['DistDistributorLimitHistory']['dealer_wise_limit_id']=$id;
                $DistDistributorLimitHistory['DistDistributorLimitHistory']['max_amount']=$this->request->data['DistDistributorLimit']['max_amount'];
                $DistDistributorLimitHistory['DistDistributorLimitHistory']['is_active']=$this->request->data['DistDistributorLimit']['is_active'];
                $DistDistributorLimitHistory['DistDistributorLimitHistory']['effective_start_date']=$this->request->data['DistDistributorLimit']['effective_date'];
                $DistDistributorLimitHistory['DistDistributorLimitHistory']['created_at']=$this->request->data['DistDistributorLimit']['updated_at'];
                $DistDistributorLimitHistory['DistDistributorLimitHistory']['created_by']=$this->UserAuth->getUserId();;
                $DistDistributorLimitHistory['DistDistributorLimitHistory']['updated_at']=$this->request->data['DistDistributorLimit']['updated_at'];
                $DistDistributorLimitHistory['DistDistributorLimitHistory']['updated_by']=$this->UserAuth->getUserId();
                unset($DistDistributorLimitHistory['DistDistributorLimitHistory']['id']);
                //pr($DistDistributorLimitHistory);die();
                $this->DistDistributorLimitHistory->create();
                $this->DistDistributorLimitHistory->save($DistDistributorLimitHistory['DistDistributorLimitHistory']);
            }

            $this->Session->setFlash(__('Data has successfully updated.'), 'flash/success');
            $this->redirect(array('action' => 'index'));
            
        } else {
            $options = array('conditions' => array('DistDistributorLimit.' . $this->DistDistributorLimit->primaryKey => $id));
            $this->request->data = $this->DistDistributorLimit->find('first', $options);
            //pr($this->request->data);die();
        }
        //pr($this->request->data);die();
        $this->loadModel('DistDistributor');
        $distDistributors = $this->DistDistributor->find('list');
        $this->set(compact('distDistributors'));
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
        $this->DistAreaExecutive->id = $id;
        if (!$this->DistAreaExecutive->exists()) {
            throw new NotFoundException(__('Invalid Area Executive'));
        }
        $this->check_data_by_company('DistDistributorLimit',$id);
        /*         * ********** Check foreign key ********** */
        if ($this->DistAreaExecutive->checkForeignKeys("dist_area_executives", $id)) {
            $this->Session->setFlash(__('This Area Executive has transactions'), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }


        if ($this->DistAreaExecutive->delete()) {
            $this->Session->setFlash(__('Area Executive deleted'), 'flash/success');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Area Executive was not deleted'), 'flash/error');
        $this->redirect(array('action' => 'index'));
    }

    public function get_area_executive_list() {
        // $rs = array('---- Plsease Select -----');
        $rs = array();
        $office_id = $this->request->data['office_id'];
        $distAreaExecutives = $this->DistAreaExecutive->find('list', array(
            'conditions' => array(
                'DistAreaExecutive.office_id' => $office_id, 'DistAreaExecutive.is_active' => 1
            )
        ));
        if (!empty($distAreaExecutives)) {
            echo json_encode($distAreaExecutives);
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function check_tso_exist($ae_id) {

        $this->loadModel('DistTso');
        $conditions = array('DistTso.dist_area_executive_id' => $ae_id);
        $existingCount = $this->DistTso->find('all', array('conditions' => $conditions, 'recursive' => -1));
        //pr($existingCount); exit;
        return $existingCount;
    }
    
     public function get_available_ae_list() {
         
         $this->loadModel('DistAreaExecutive');
         
         $dist_ae_except = $this->DistAreaExecutive->find('all', array(
            'recursive' => -1
        ));
        
       
        $ae_except_ids=array();
        foreach ($dist_ae_except as $k => $v) {
            if($v['DistAreaExecutive']['user_id'])
            $ae_except_ids[]=$v['DistAreaExecutive']['user_id'];
        }
       
        $this->loadModel('User');
        $ae_except_id="";
        
        if($ae_except_ids && count($ae_except_ids)>1)
        {
        $ae_except_id = implode(',',$ae_except_ids);
        }
        else if($ae_except_ids)
        {
           $ae_except_id = $ae_except_ids[0];  
        }
        
        if($ae_except_id)
        {
            $qry="select u.id,u.user_group_id,u.sales_person_id,u.username,sp.name,sp.office_id from users u
            left join sales_people sp on sp.id=u.sales_person_id where u.user_group_id=1028 and u.id not in ($ae_except_id)";
        }
        else 
        {
            $qry="select u.id,u.user_group_id,u.sales_person_id,u.username,sp.name,sp.office_id from users u
            left join sales_people sp on sp.id=u.sales_person_id where u.user_group_id=1028";
        }
        
        
        $existing=$this->User->query($qry);
        
        return $existing;
    }

}
