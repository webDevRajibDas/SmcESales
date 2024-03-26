<?php

App::uses('AppController', 'Controller');

/**
 * Territories Controller
 *
 * @property Territory $Territory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistDeliveryMenController extends AppController {

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
        $this->set('page_title', 'Delivery Man List');
        $this->loadModel('Office');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id'=>2);
            $dm_conditions = array();
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
               
                $tso_dist_list = $this->DistTsoMapping->find('list',array(
                    'conditions'=> array(
                        'dist_tso_id' => $dist_tso_id,
                    ),
                    'fields'=>array('DistTsoMapping.dist_distributor_id','DistTsoMapping.dist_tso_id'),
                ));
               $dm_conditions = array('DistDeliveryMan.dist_distributor_id'=>array_keys($tso_dist_list));
            }
            elseif($user_group_id == 1034){
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first',array(
                    'conditions'=>array('DistUserMapping.sales_person_id'=>$sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
                $dm_conditions = array( 'DistDeliveryMan.dist_distributor_id'=>$distributor_id);

            }
            else{
                $dm_conditions = array('Office.id' => $this->UserAuth->getOfficeId(),'Office.office_type_id'=>2);
            }
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(),'Office.office_type_id'=>2);
        }

        $this->paginate = array(
            'conditions' => $dm_conditions,
            'recursive' => 0,
            'order' => array('DistDeliveryMan.id' => 'DESC')
        );
		

        $this->set('territories', $this->paginate());
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
        $this->set('page_title', 'Delivery Man Details');
        if (!$this->DistDeliveryMan->exists($id)) {
            throw new NotFoundException(__('Invalid Delivery Man'));
        }
        $options = array('conditions' => array('DistDeliveryMan.' . $this->DistDeliveryMan->primaryKey => $id));
        $this->set('territory', $this->DistDeliveryMan->find('first', $options));
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add() {
        $this->set('page_title', 'Add Delivery Man');
        
        //$adding_type=array('1'=>'New','2'=>'Replacement','3'=>'Distributor to Distributor Transfer');
        $is_new=1;
        
        if ($this->request->is('post')) {
            //pr($this->request->data);die();
            $code=$this->request->data['DistDeliveryMen']['code'];
            $off_id = $this->request->data['DistDeliveryMen']['office_id'];
           if($this->DistDeliveryMan->check_available_code($code,0,$off_id))
            {
                $this->Session->setFlash(__('This SR Code is already used in another SR.'), 'flash/error');
                $this->redirect(array('action' => 'add'));
            }
            
            $data['name']= $this->request->data['DistDeliveryMen']['name'];
            $data['mobile_number']= $this->request->data['DistDeliveryMen']['mobile_number'];
            $data['office_id']= $this->request->data['DistDeliveryMen']['office_id'];
            $data['dist_distributor_id']= $this->request->data['DistDeliveryMen']['dist_distributor_id'];
            $data['code']= $this->request->data['DistDeliveryMen']['code'];
            $data['created_at']= $this->current_datetime();;
            $data['created_by']= $this->UserAuth->getUserId();
            $data['updated_at']= $this->current_datetime();;
            $data['updated_by']= $this->UserAuth->getUserId();
            $data['is_active']= $this->request->data['DistDeliveryMen']['is_active'];
     //pr($data);die();
            $this->DistDeliveryMan->create();
          
            if ($this->DistDeliveryMan->save($data)) {
                $this->Session->setFlash(__('The Delivery Man has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Delivery Man could not be saved. Please, try again.'), 'flash/error');
                
            }
        }

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id'=>2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(),'Office.office_type_id'=>2);
        }
        $this->loadModel('Office');
        $offices_raw = $this->Office->find('all', array('fields'=>array('Office.id','Office.office_name','Office.office_code'),'conditions' => $conditions,'recursive'=>-1, 'order' => array('office_name' => 'asc')));
        $offices=array();
        $office_code=array();
        
        foreach ($offices_raw as $key => $value) {
            $id=$value['Office']['id'];
            $offices[$id]=$value['Office']['office_name'];
            $office_code[$id]=$value['Office']['office_code'];
        }
        
        $this->set(compact('offices','office_code','adding_type','is_new'));
    }

    /**
     * admin_edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_edit($id = null) {
        $this->set('page_title', 'Edit Delivery Man');
        $this->DistDeliveryMan->id = $id;
        if (!$this->DistDeliveryMan->exists($id)) {
            throw new NotFoundException(__('Invalid Delivery Man'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            //pr($this->request->data);die();
            $this->request->data['DistDeliveryMan']['updated_by'] = $this->UserAuth->getUserId();
            $code=$this->request->data['DistDeliveryMan']['code'];
            $off_id=$this->request->data['DistDeliveryMan']['office_id'];
            //$code=$off_id.$code;
            if($this->DistDeliveryMan->check_available_code($code,$id,$off_id))
            {
                $this->Session->setFlash(__('This SR Code is already used in another SR.'), 'flash/error');
                $this->redirect(array('action' => "edit/$id"));
            }
            
            
            if ($this->DistDeliveryMan->save($this->request->data)) {
                $this->loadModel('DistSalesRepTransferHistory');
                
                $this->request->data['DistSalesRepTransferHistory'] = $this->request->data['DistDeliveryMan'];
                $this->request->data['DistSalesRepTransferHistory']['created_at'] = $this->current_datetime();
                $this->request->data['DistSalesRepTransferHistory']['created_by'] = $this->current_datetime();
                $this->request->data['DistSalesRepTransferHistory']['created_by'] = $this->UserAuth->getUserId();
                unset($this->request->data['DistSalesRepTransferHistory']['id']);
                //  pr($this->request->data);exit;
                $this->DistSalesRepTransferHistory->create();
                $this->DistSalesRepTransferHistory->save($this->request->data['DistSalesRepTransferHistory']);
                
                $this->Session->setFlash(__('The Delivery Man has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $options = array('conditions' => array('DistDeliveryMan.' . $this->DistDeliveryMan->primaryKey => $id));
            $this->request->data = $this->DistDeliveryMan->find('first', $options);
        }

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id'=>2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(),'Office.office_type_id'=>2);
        }
        $this->loadModel('Office');
        $this->loadModel('Territory');
        $this->loadModel('DistDistributor');
        //$offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        
        
        
        $has_node=0;
        if($this->check_sr_child($id))
        {
            $has_node=1;
        }
        $this->set(compact('has_node'));
        
        $offices_raw = $this->Office->find('all', array('fields'=>array('Office.id','Office.office_name','Office.office_code'),'conditions' => $conditions,'recursive'=>-1, 'order' => array('office_name' => 'asc')));
        $offices=array();
        $office_code=array();
        
        foreach ($offices_raw as $key => $value) {
            $id=$value['Office']['id'];
            $offices[$id]=$value['Office']['office_name'];
            $office_code[$id]=$value['Office']['office_code'];
        }
        
        $territories = $this->Territory->find('list', array('conditions' => array('office_id' => $this->request->data['DistDeliveryMan']['office_id']), 'order' => array('name' => 'asc')));
        $distDistributors = $this->DistDistributor->find('list', array('conditions' => array('office_id' => $this->request->data['DistDeliveryMan']['office_id']), 'order' => array('name' => 'asc')));
        $this->set(compact('offices', 'territories', 'distDistributors','office_code'));       
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
        $this->DistDeliveryMan->id = $id;
        if (!$this->DistDeliveryMan->exists()) {
            throw new NotFoundException(__('Invalid SR'));
        }
        
        
                /*         * ***************************** Check foreign key *************************** */
        if ($this->DistDeliveryMan->checkForeignKeys("dist_delivery_man", $id)) {
            $this->Session->setFlash(__('This Delivery Man has transactions'), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }
        
        if ($this->DistDeliveryMan->delete()) {
            $this->Session->setFlash(__('Delivery Man deleted'), 'flash/success');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Delivery Man was not deleted'), 'flash/error');
        $this->redirect(array('action' => 'index'));
    }
    
    public function check_sr_child($id) {
       
      /***********************  check Route mapping start ****************************************/
        
        $this->loadModel('DistSrRouteMapping');
        $conditions2 = array('DistSrRouteMapping.dist_sr_id' => $id);
        $existingCount2 = $this->DistSrRouteMapping->find('count', array('conditions' => $conditions2, 'recursive' => -1));
        if($existingCount2)
        {
            return 2;
        }
      
      /***********************  check Route mapping End ****************************************/

      return 0;
    }
    
    public function get_sr_code() {
       
        $existing=array();
        $office_id=$this->request->data['office_id'];
        $this->loadModel('DistDeliveryMan');
        $conditions2 = array('DistDeliveryMan.office_id' => $office_id);
        $existing = $this->DistDeliveryMan->find('all', array('conditions' => $conditions2,
            'fields' => array('MAX(CAST(DistDeliveryMan.code AS INT)) AS code')));
        
        
        if($existing)
        {
          echo $existing[0][0]['code']+1;   
        }
        else 
        {
           echo 1;   
        }
        
        $this->autoRender = false;
    }
    
     public function get_inactive_sr_code() {
       
        $existing=array();
        $office_id=$this->request->data['office_id'];
        $dist_distributor_id=$this->request->data['dist_distributor_id'];
        $this->loadModel('DistDeliveryMan');
        $conditions2 = array('DistDeliveryMan.office_id' => $office_id,'DistDeliveryMan.dist_distributor_id' => $dist_distributor_id,'DistDeliveryMan.is_active' =>0);
        $existing = $this->DistDeliveryMan->find('all', array('conditions' => $conditions2,'recursive'=>-1,
            'fields' => array('distinct DistDeliveryMan.code AS code')));
        
        $output = "<option value=''>--- Select Code ---</option>";
        foreach ($existing as $key => $value) {
            $code=$value[0]['code'];
             $output .= "<option value='$code'>$code</option>";
        }
        
         echo $output;
        $this->autoRender = false;
    }

    function  get_sr_name_by_office_id_dist_id_sr_code()
    {
        /*pr($this->request->data);exit;*/
        $office_id=$this->request->data['office_id'];
        $dist_distributor_id=$this->request->data['dist_distributor_id'];
        $sr_code=$this->request->data['sr_code'];
        $conditions2 = array('DistDeliveryMan.office_id' => $office_id,'DistDeliveryMan.dist_distributor_id' => $dist_distributor_id,'DistDeliveryMan.is_active' =>0,'DistDeliveryMan.code'=>$sr_code);
        $sr_info = $this->DistDeliveryMan->find('first', array('conditions' => $conditions2,'recursive'=>-1,
            ));
        echo json_encode($sr_info);
        $this->autoRender = false;
    }

}
