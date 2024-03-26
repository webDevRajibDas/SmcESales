<?php

App::uses('AppController', 'Controller');

/**
 * DistDiscounts Controller
 *
 * @property DistDiscount $DistDiscount
 * @property PaginatorComponent $Paginator
 */
class DistDiscountsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Filter.Filter');
    public $uses = array('DistDiscount', 'Store','ProductType');

    /**
     * admin_index method
     *
     * @return void
     */
   
    public function admin_index() {
        $this->set('page_title', 'Distributor Discount');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $this->DistDiscount->recursive = 1;
		$conditions = array();
        if($office_parent_id == 0){
            $conditions = array();
        }else{
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
               $conditions = array(
                'OR'=>array(
                    array('DistDiscount.distributor_id'=>array_keys($tso_dist_list)),
                    array('DistDiscount.distributor_id'=> NULL),
                ),
                'DistDiscount.office_id'=>$this->UserAuth->getOfficeId(),
                /*'OR'=>array(
                    array('DistDiscount.office_id'=>$this->UserAuth->getOfficeId()),
                    array('DistDiscount.office_id'=>NULL),
                ),*/
               );
            }
            else{
                $conditions = array(
                    'OR'=> array(
                        array('DistDiscount.office_id'=>$this->UserAuth->getOfficeId()),
                        array('DistDiscount.office_id'=> NULL),
                    ),
                );
            }
            
        }
        //pr($conditions);die();
        $this->paginate = array(
            'conditions' => $conditions,
			'order' => array('id' => 'desc'),
        );
        $distDiscounts = $this->paginate();
		//pr($distDiscounts);
        $this->set(compact('distDiscounts'));
    }

   

    /*
     * view details method
     */

    

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_view($id = null) {
        if (!$this->DistDiscount->exists($id)) {
            throw new NotFoundException(__('Invalid current inventory'));
        }
        $options = array(
            'conditions' => array('DistDiscount.' . $this->DistDiscount->primaryKey => $id),
        );
        $distDiscount = $this->DistDiscount->find('first', $options);

        $this->set('distDiscount',$distDiscount);
    }

    public function admin_add() {
        $this->loadModel('DistDiscountDetail');
        $this->loadModel('DistDistributor');
        $this->loadModel('Office');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0 ) {
            $dist_conditions = array();
            $office_conditions = array(
            'office_type_id' => 2,
            "NOT" => array( "id" => array(30, 31, 37))
            );
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
               $dist_conditions = array('DistDistributor.id'=>array_keys($tso_dist_list));
            }
            else{
                $dist_conditions = array('DistDistributor.office_id' => $this->UserAuth->getOfficeId());
            }
            
            
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());  
        }

        $offices = $this->Office->find('list',array('conditions'=>$office_conditions));
        $distributors = $this->DistDistributor->find('list',array('conditions'=>$dist_conditions));
        $discounts_types = array(1=>'Percentage',2=>'Taka');
        $this->set(compact('offices','distributors','office_parent_id','discounts_types'));
        //$this->set(compact('last_effective_date'));

        $last_discount_info = $this->DistDiscount->find('first',array('order'=>array('DistDiscount.id DESC')));
        
        if(!empty($last_discount_info)){
            $last_effective_date = $last_discount_info['DistDiscount']['date_to'];
        }
        else{
            $last_effective_date = $this->current_datetime();
        }
        $this->set(compact('last_effective_date'));

        if ($this->request->is('post')) { 
            

            $date_from = $this->request->data['DistDiscount']['date_from'];
            $date_to = $this->request->data['DistDiscount']['date_to'];

            $discont['date_from'] = date('Y-m-d',strtotime($date_from));
            $discont['date_to'] = date('Y-m-d',strtotime($date_to));
			
			$discont['office_id'] = $this->request->data['DistDiscount']['office_id'];
            $discont['distributor_id'] = $this->request->data['DistDiscount']['distributor_id'];
			$discont['is_active'] = $this->request->data['DistDiscount']['is_active'];
			
            $discont['description'] = $this->request->data['DistDiscount']['description'];
            $discont['created_at']  = $this->current_datetime();
            $discont['created_by']  = $this->UserAuth->getUserId();
            $discont['updated_at']  = $this->current_datetime();
            $discont['updated_by']  = $this->UserAuth->getUserId();

            $this->DistDiscount->create();
            if($this->DistDiscount->save($discont)){
            	$dist_discount_id = $this->DistDiscount->getLastInsertId();
            	$discont_details = array();
            	$total_discont_details = array();
            	if($this->request->data['memo_value'] != null){
            		foreach ($this->request->data['memo_value'] as $key => $value) {
            			$discont_details['DistDiscountDetail']['dist_discount_id'] = $dist_discount_id;
            			$discont_details['DistDiscountDetail']['memo_value'] = $this->request->data['memo_value'][$key];
            			$discont_details['DistDiscountDetail']['discount_percent'] = $this->request->data['discount_percent'][$key];
						$discont_details['DistDiscountDetail']['discount_type'] = $this->request->data['discount_type'][$key];
            			$discont_details['DistDiscountDetail']['date_from'] = date('Y-m-d',strtotime($date_from));
            			$discont_details['DistDiscountDetail']['date_to'] = date('Y-m-d',strtotime($date_to));

            			$total_discont_details[] = $discont_details;
            		}
            		$this->DistDiscountDetail->create();
            		if($this->DistDiscountDetail->saveAll($total_discont_details)){
            			$this->flash(__('Discount has been saved'), array('action' => 'index'));
        				$this->redirect(array('action' => 'index'));
					}
            	}
            }
            
        }
       
    }

    public function get_effective_date(){
        $office_id = $this->request->data['office_id'];
        $distributor_id = $this->request->data['distributor_id'];

        $this->loadModel('DistDiscount');
        $conditions = array();
        if($office_id != -1){
            $conditions[] =  array('DistDiscount.office_id' => $office_id);
        }
        if($distributor_id != -1){
            $conditions[] =  array('DistDiscount.distributor_id' => $distributor_id);
        }

        $last_discount_info = $this->DistDiscount->find('first',array(
            'conditions'=>$conditions,
            'order'=>array('DistDiscount.id DESC')));
        //pr($last_discount_info);//die();
        if(!empty($last_discount_info)){
            $last_effective_date = date('d-m-Y', strtotime($last_discount_info['DistDiscount']['date_to']));
        }
        else{
            $last_effective_date = date('d-m-Y', strtotime($this->current_datetime()));
        }
        $data_array = array('last_effective_date' => $last_effective_date);
        if(!empty($last_discount_info)){
            echo json_encode($data_array);
        }
        else{
            echo json_encode(array());
        }
        $this->autoRender = false;
    }

    /**
     * admin_edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_edit($id = null) {
        $this->DistDiscount->id = $id;
        if (!$this->DistDiscount->exists($id)) {
            throw new NotFoundException(__('Distributor Bonus Card'));
        }
        $this->loadModel('DistDiscountDetail');
        $this->loadModel('DistDistributor');
        $this->loadModel('Office');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $office_parent_id = $this->UserAuth->getOfficeParentId();

        if ($office_parent_id == 0 ) {
            $dist_conditions = array('is_active'=> 1);
            $office_conditions = array(
            'office_type_id' => 2,
            "NOT" => array( "id" => array(30, 31, 37))
            );
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
               $dist_conditions = array('DistDistributor.id'=>array_keys($tso_dist_list),'is_active'=> 1);
               
            }
            else{
                $dist_conditions = array('DistDistributor.office_id' => $this->UserAuth->getOfficeId(),'is_active'=> 1);
            }

            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());  
        }

        $offices = $this->Office->find('list',array('conditions'=>$office_conditions));
        $distributors = $this->DistDistributor->find('list',array('conditions'=>$dist_conditions));
        $this->set(compact('offices','distributors','office_parent_id'));
        
		$options = array('conditions' => array('DistDiscount.' . $this->DistDiscount->primaryKey => $id));
		
		$discount_types = array(
			1 => 'Percentage',
			2 => 'Taka',
		);
		$this->set(compact('discount_types'));
		
        if ($this->request->is('post') || $this->request->is('put')) {

                //pr($this->request->data);die();
				$discont = array();
			    
				
				$date_from = $this->request->data['DistDiscount']['date_from'];
				$date_to = $this->request->data['DistDiscount']['date_to'];
				
				$discont['id'] = $this->request->data['DistDiscount']['id'];
				$discont['date_from'] = date('Y-m-d',strtotime($date_from));
				$discont['date_to'] = date('Y-m-d',strtotime($date_to));

				$discont['description'] = $this->request->data['DistDiscount']['description'];
				$discont['office_id'] = $this->request->data['DistDiscount']['office_id'];
				$discont['distributor_id'] = $this->request->data['DistDiscount']['distributor_id'];
                $discont['is_active'] = $this->request->data['DistDiscount']['is_active'];
				//$discont['created_at']  = $this->current_datetime();
			   // $discont['created_by']  = $this->UserAuth->getUserId();
				$discont['updated_at']  = $this->current_datetime();
				$discont['updated_by']  = $this->UserAuth->getUserId();
				
				if($this->DistDiscount->save($discont))
				{
					//delete all/many
					$this->DistDiscountDetail->deleteAll(
						array( 'DistDiscountDetail.dist_discount_id' => $id )   //condition
					);

					$discont_details = array();
					$total_discont_details = array();
					if($this->request->data['memo_value'] != null)
					{
						foreach ($this->request->data['memo_value'] as $key => $value) 
						{
							$discont_details['DistDiscountDetail']['dist_discount_id'] = $id;
							$discont_details['DistDiscountDetail']['memo_value'] = $this->request->data['memo_value'][$key];
							$discont_details['DistDiscountDetail']['discount_percent'] = $this->request->data['discount_percent'][$key];
							$discont_details['DistDiscountDetail']['discount_type'] = $this->request->data['discount_type'][$key];
							$discont_details['DistDiscountDetail']['date_from'] = date('Y-m-d',strtotime($date_from));
							$discont_details['DistDiscountDetail']['date_to'] = date('Y-m-d',strtotime($date_to));

							$total_discont_details[] = $discont_details;
						}
						$this->DistDiscountDetail->create();
						if($this->DistDiscountDetail->saveAll($total_discont_details)){
							$this->flash(__('Discount has been saved'), array('action' => 'index'));
							$this->redirect(array('action' => 'index'));
						}
					}
				}
                
        }else{
			$this->request->data = $this->DistDiscount->find('first', $options);
            $this->request->data['DistDiscount']['date_from'] = date('d-m-Y',strtotime($this->request->data['DistDiscount']['date_from']));
            $this->request->data['DistDiscount']['date_to'] = date('d-m-Y',strtotime($this->request->data['DistDiscount']['date_to']));	
		}
		//pr($this->request->data);
        
        $distDiscount = $this->request->data;

        $this->set(compact('distDiscount'));
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
        $this->DistDiscount->id = $id;
        if (!$this->DistDiscount->exists()) {
            throw new NotFoundException(__('Invalid current inventory'));
        }
        $this->loadModel('DistDiscountDetail');
        $this->DistDiscountDetail->deleteAll(array('DistDiscountDetail.dist_discount_id' => $id));
       
        if ($this->DistDiscount->delete()) {
            $this->flash(__('Current inventory deleted'), array('action' => 'index'));
        }
        $this->flash(__('Current inventory was not deleted'), array('action' => 'index'));
        $this->redirect(array('action' => 'index'));
    }
    public function admin_delete_data($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->DistDiscount->id = $id;
        if (!$this->DistDiscount->exists()) {
            throw new NotFoundException(__('Invalid current inventory'));
        }
        $this->loadModel('DistDiscountDetail');
        $this->DistDiscountDetail->deleteAll(array('DistDiscountDetail.dist_discount_id' => $id));
        $this->DistDiscount->delete();
    }


    public function get_distributor_list(){
        
        $office_id = $this->request->data['office_id'];
        $rs = array(array('id' => '', 'name' => '---- Select -----'));
        $this->loadModel('DistDistributor');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
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
           
            $tso_dist_list = $this->DistTsoMapping->find('list',array(
                'conditions'=> array(
                    'dist_tso_id' => $dist_tso_id,
                ),
                'fields'=>array('DistTsoMapping.dist_distributor_id','DistTsoMapping.dist_tso_id'),
            ));
           
           $conditions=array(
                'conditions' => array('DistDistributor.id' =>array_keys($tso_dist_list), 'DistDistributor.is_active' => 1),'order' => array('DistDistributor.name' => 'asc'),
            );
        }
        else{
            $conditions=array(
                'conditions' => array('DistDistributor.office_id' => $office_id, 'DistDistributor.is_active' => 1),'order' => array('DistDistributor.name' => 'asc'),
            );
        }

        $distributor_info = $this->DistDistributor->find('all',$conditions);

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

}
