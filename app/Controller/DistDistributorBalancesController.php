<?php

App::uses('AppController', 'Controller');

/**
 * Territories Controller
 *
 * @property Territory $Territory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistDistributorBalancesController extends AppController {

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
        
        $this->set('page_title', 'Distributor Balance List');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $designation_id=$this->Session->read('Office.designation_id');
        $this->loadModel('Office');   
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
       
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        
        $conditions = array();
        $b_conditions = array();
        $dist_con = array();
        //==================25-6-19====//
		//echo $office_parent_id;

        if ($office_parent_id == 0) //for super user admin
        {
            $conditions = array('office_type_id' => 2);
            $b_conditions = array();
            $dist_con = array('DistDistributor.is_active' =>1);
        }
        else //for company office user admin
        {
            $conditions = array('office_type_id' => 2, 'Office.id' => $this->UserAuth->getOfficeId());
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
                $b_conditions = array('DistDistributorBalance.dist_distributor_id' =>array_keys($tso_dist_list));
                $dist_con = array('DistDistributor.id' =>array_keys($tso_dist_list),'DistDistributor.is_active' =>1);
            }
            elseif($user_group_id == 1034){
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first',array(
                    'conditions'=>array('DistUserMapping.sales_person_id'=>$sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
        
                $b_conditions = array('DistDistributorBalance.dist_distributor_id' =>$distributor_id);
                $dist_con = array('DistDistributor.id' =>$distributor_id,'DistDistributor.is_active' =>1);
            }
            else{
                $b_conditions = array('DistDistributorBalance.office_id' =>$this->UserAuth->getOfficeId());
                $dist_con = array('DistDistributor.office_id' =>$this->UserAuth->getOfficeId(),'DistDistributor.is_active' =>1);
            }
        }

		/*pr($conditions);
		exit;*/
		
        $this->loadModel('Office');           
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        //================end==25-6-19====//
        $this->paginate = array(
            'fields'=> array('DistDistributorBalance.*','DistDistributor.id','DistDistributor.name','Office.office_name'),
            'conditions' => $b_conditions,
            'joins'=>array(
                array(
                    'table'=>'offices',
                    'alias'=>'Office',
                    'type' => 'LEFT',
                    'conditions'=>array('Office.id= DistDistributor.office_id')
                    )
                ),
            'recursive' => 0,
            //'group' => array('Office.id'),
            'order' => array('DistDistributorBalance.id' => 'ASC')
        );
        $this->set('dist_distributor_balances', $this->paginate());

        
        $this->loadModel('DistDistributor');
        
        $distDistributors = $this->DistDistributor->find('list',array(
                'conditions' => $dist_con
            ));
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
        $this->set('page_title', 'DistributorBalance');
        if (!$this->DistDistributorBalance->exists($id)) {
            throw new NotFoundException(__('Invalid DistAreaExecutive'));
        }

        if ($this->request->is('post')) 
		{
            $request_data = $this->request->data;
			$this->set(compact('request_data'));

            $date_from = $this->request->data['date_from'];
			$date_to = $this->request->data['date_to'];

			$startdate = date('Y-m-d', strtotime($date_from));
			$enddate = date('Y-m-d', strtotime($date_to));

            $con[] = array(
                'DistDistributorBalanceHistory.transaction_date >=' => $startdate,
                'DistDistributorBalanceHistory.transaction_date <=' => $enddate
            );

        }

        $con[] = array(
            'DistDistributorBalanceHistory.dist_distributor_balance_id' => $id
        );

        $this->loadModel('DistDistributorBalanceHistory');
        $options = array(
            'conditions' =>$con,
            'order'=>array('DistDistributorBalanceHistory.id DESC'),
    	);
        
        $this->set('distributor_balance', $this->DistDistributorBalanceHistory->find('all', $options));
        $this->set(compact('id'));
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add() {
        $this->set('page_title', 'Add Distributor Balance'); 
        
        
        //for company and office list
        
        $this->loadModel('DistDistributor');
        $this->loadModel('Office');
        $this->loadModel('DistBalanceTransactionType');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
       
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        $transaction_type = $this->DistBalanceTransactionType->find('list',array(
            'conditions'=>array(
                'id IN'=> array(1,6),
            ),
        ));
       
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $designation_id=$this->Session->read('Office.designation_id');
        $this->set(compact('office_parent_id'));
        if ($office_parent_id == 0) 
        {
            $office_conditions = array(
                'office_type_id' => 2,
                "NOT" => array( "id" => array(30, 31, 37))
            );
            $office_conditions = array('Office.office_type_id' => 2);
                $officelists = $this->Office->find('all', array('conditions' => $office_conditions, 'order' => array('Office.office_name' => 'asc')));

                 // pr($officelists);die();
                foreach ($officelists as $key => $value) {
                    $office_ids[$key]=$value['Office']['id'];
                    $offices[$value['Office']['id']]=$value['Office']['office_name'];
                }
                $distDistributors = $this->DistDistributor->find('list', array('conditions' =>array('DistDistributor.office_id'=>$office_ids,'DistDistributor.is_active'=> 1,
                    //'DistDistributor.dealer_is_limit_check'=> 1
                     )));
                //pr($distDistributors);die();
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
                $b_conditions = array('DistDistributor.id' =>array_keys($tso_dist_list));

            }
            else{
                $b_conditions = array('DistDistributor.office_id' =>$this->UserAuth->getOfficeId(),'DistDistributor.is_active'=> 1);
            }
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
            $office_id = $office_conditions['Office.id'];
            $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
            $distDistributors = $this->DistDistributor->find('list', array('conditions' =>$b_conditions));
            
        }
        
        $this->set(compact('distDistributors','transaction_type'));

        $this->set(compact('offices'));
        //for company and office list

        
        if ($this->request->is('post')) {
           
            //pr($this->request->data);die();
            $office_id = $this->request->data['DistDistributorBalance']['office_id'];
            $dist_distributor_id= $this->request->data['DistDistributorBalance']['dist_distributor_id'];
            $transaction_type_id= $this->request->data['DistDistributorBalance']['balance_transaction_type_id'];
            $balance_inout= $this->request->data['DistDistributorBalance']['balance_inout'];

            $distributor_balance = $this->DistDistributorBalance->find('first',array(
                'conditions'=>array(
                    'DistDistributorBalance.office_id'=>$office_id,
                    'DistDistributorBalance.dist_distributor_id'=>$dist_distributor_id,
                    )));
            
            $ammount = $this->request->data['DistDistributorBalance']['balance'];
            $balance_type = 1; 
            if($balance_inout == 1){
                $ammount = $ammount * (-1);
                $balance_type = 2;
            }
            
            if(!empty($distributor_balance)){
               
                $this->request->data['DistDistributorBalance']['balance'] = $ammount + $distributor_balance['DistDistributorBalance']['balance'];
             
                   
                $this->request->data['DistDistributorBalance']['id'] = $distributor_balance['DistDistributorBalance']['id'];
                
            }
            else{
                $this->request->data['DistDistributorBalance']['balance'] = $ammount;
                $this->DistDistributorBalance->create();
            }


            $dist_distributor_id = $this->request->data['DistDistributorBalance']['dist_distributor_id'];

            $this->request->data['DistDistributorBalance']['is_active'] = 1;
            $this->request->data['DistDistributorBalance']['created_at'] = $this->current_datetime();
            $this->request->data['DistDistributorBalance']['created_by'] = $this->UserAuth->getUserId();
            $this->request->data['DistDistributorBalance']['updated_at'] = $this->current_datetime();
            $this->request->data['DistDistributorBalance']['updated_by'] = $this->UserAuth->getUserId();
           //pr($this->request->data);die();
            unset($this->request->data['DistDistributorBalance']['balance_transaction_type_id']);
            
            
            if($this->DistDistributorBalance->save($this->request->data['DistDistributorBalance']))
            {
                if(!empty($distributor_balance)){
                    $id = $this->request->data['DistDistributorBalance']['id'];
                }
                else{
                    $id = $this->DistDistributorBalance->getLastInsertID();
                }
                $this->loadModel('DistDistributorBalanceHistory');
                $distributor_balance_info = $this->DistDistributorBalanceHistory->find('first',array(
                'conditions'=>array(
                    'DistDistributorBalanceHistory.office_id'=>$office_id,
                    'DistDistributorBalanceHistory.dist_distributor_id'=>$dist_distributor_id,
                    ),
                'order' => 'DistDistributorBalanceHistory.id DESC',
                ));
               // pr($distributor_balance_info);
                if(!empty($distributor_balance_info)){
                    $balance = $ammount + $distributor_balance_info['DistDistributorBalanceHistory']['balance'];
                }                           
                else{
                    $balance = $ammount;
                }
                if($balance_inout == 1){
                    $ammount = $ammount * (-1);
                }
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']=$this->request->data['DistDistributorBalance'];
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['transaction_amount'] = $ammount;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['balance'] = $balance;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['dist_distributor_balance_id'] = $id;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['balance_type']=$balance_type;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['balance_transaction_type_id']=$transaction_type_id;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['transaction_date']=$this->request->data['DistDistributorBalance']['updated_at'];

                //unset($DistDistributorBalanceHistory['DistDistributorBalanceHistory']['id']);
                //unset($DistDistributorBalanceHistory['DistDistributorBalanceHistory']['balance']);
                //pr($DistDistributorBalanceHistory);die();
                unset($DistDistributorBalanceHistory['DistDistributorBalanceHistory']['id']);
                $this->DistDistributorBalanceHistory->create();
                $this->DistDistributorBalanceHistory->save($DistDistributorBalanceHistory['DistDistributorBalanceHistory']);
                
                $this->Session->setFlash(__('Balance added successfully!'), 'flash/success');
                $this->redirect(array('action' => 'index'));
                    
                }else{
                    $this->Session->setFlash(__('Balance could not be saved. Please, try again.'), 'flash/error');
                    $this->redirect(array('action' => 'index'));
                }
            }

        
    }
    public function admin_adjustment() {
        $this->set('page_title', 'Adjustment Distributor Adjustment'); 
        
        
        //for company and office list
        
        $this->loadModel('DistDistributor');
        $this->loadModel('Office');
        $this->loadModel('DistBalanceTransactionType');

        $transaction_type = $this->DistBalanceTransactionType->find('list',array(
            'conditions'=>array(
                'id'=> 6,
            ),
        ));
       
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $designation_id=$this->Session->read('Office.designation_id');
        $this->set(compact('office_parent_id'));
        if ($office_parent_id == 0) 
        {
            $office_conditions = array(
                'office_type_id' => 2,
                "NOT" => array( "id" => array(30, 31, 37))
            );
            $office_conditions = array('Office.office_type_id' => 2);
                $officelists = $this->Office->find('all', array('conditions' => $office_conditions, 'order' => array('Office.office_name' => 'asc')));

                 // pr($officelists);die();
                foreach ($officelists as $key => $value) {
                    $office_ids[$key]=$value['Office']['id'];
                    $offices[$value['Office']['id']]=$value['Office']['office_name'];
                }
                $distDistributors = $this->DistDistributor->find('list', array('conditions' =>array('DistDistributor.office_id'=>$office_ids,'DistDistributor.is_active'=> 1,
                    //'DistDistributor.dealer_is_limit_check'=> 1
                     )));
                //pr($distDistributors);die();
        }
        else
        {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
            $office_id = $office_conditions['Office.id'];
            $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
            $distDistributors = $this->DistDistributor->find('list', array('conditions' =>array('DistDistributor.office_id'=>$this->UserAuth->getOfficeId(),'DistDistributor.is_active'=> 1,
                    //'DistDistributor.dealer_is_limit_check'=> 1
                )));
        }
        
        $this->set(compact('distDistributors','transaction_type'));

        $this->set(compact('offices'));
        //for company and office list

        
        if ($this->request->is('post')) {
           

            $office_id = $this->request->data['DistDistributorBalance']['office_id'];
            $dist_distributor_id= $this->request->data['DistDistributorBalance']['dist_distributor_id'];
            $transaction_type_id= $this->request->data['DistDistributorBalance']['balance_transaction_type_id'];

            $distributor_balance = $this->DistDistributorBalance->find('first',array(
                'conditions'=>array(
                    'DistDistributorBalance.office_id'=>$office_id,
                    'DistDistributorBalance.dist_distributor_id'=>$dist_distributor_id,
                    )));
            
            $ammount = $this->request->data['DistDistributorBalance']['balance'];
            
            if(!empty($distributor_balance)){
                
                if($distributor_balance['DistDistributorBalance']['balance'] >= $ammount){
                    $this->request->data['DistDistributorBalance']['balance'] =  $distributor_balance['DistDistributorBalance']['balance'] - $ammount;
                   
                    $this->request->data['DistDistributorBalance']['id'] = $distributor_balance['DistDistributorBalance']['id'];
                }
                else{
                    $this->Session->setFlash(__('Given Amount Is Bigger Then Balance. The Balance could not be saved. Please, try again.'), 'flash/error');
                    $this->redirect(array('action' => 'adjustment'));
            }
            }
            else{
                $this->Session->setFlash(__('Please Add Balance First. Please, try again.'), 'flash/error');
                $this->redirect(array('action' => 'index'));
            }


            $dist_distributor_id = $this->request->data['DistDistributorBalance']['dist_distributor_id'];

            $this->request->data['DistDistributorBalance']['is_active'] = 1;
            $this->request->data['DistDistributorBalance']['created_at'] = $this->current_datetime();
            $this->request->data['DistDistributorBalance']['created_by'] = $this->UserAuth->getUserId();
            $this->request->data['DistDistributorBalance']['updated_at'] = $this->current_datetime();
            $this->request->data['DistDistributorBalance']['updated_by'] = $this->UserAuth->getUserId();
           
            unset($this->request->data['DistDistributorBalance']['balance_transaction_type_id']);
            
            
            if($this->DistDistributorBalance->save($this->request->data['DistDistributorBalance']))
            {
                if(!empty($distributor_balance)){
                    $id = $this->request->data['DistDistributorBalance']['id'];
                }
                else{
                    $id = $this->DistDistributorBalance->getLastInsertID();
                }
                $this->loadModel('DistDistributorBalanceHistory');
                $distributor_balance_info = $this->DistDistributorBalanceHistory->find('first',array(
                'conditions'=>array(
                    'DistDistributorBalanceHistory.office_id'=>$office_id,
                    'DistDistributorBalanceHistory.dist_distributor_id'=>$dist_distributor_id,
                    ),
                'order' => 'DistDistributorBalanceHistory.id DESC',
                ));
               // pr($distributor_balance_info);
                if(!empty($distributor_balance_info)){
                    $balance =  $distributor_balance_info['DistDistributorBalanceHistory']['balance'] - $ammount;
                }                           
                else{
                    $balance = $ammount;
                }
                
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']=$this->request->data['DistDistributorBalance'];
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['transaction_amount'] = $ammount;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['balance'] = $balance;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['dist_distributor_balance_id'] = $id;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['balance_type']=2;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['balance_transaction_type_id']=$transaction_type_id;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['transaction_date']=$this->request->data['DistDistributorBalance']['updated_at'];

                //unset($DistDistributorBalanceHistory['DistDistributorBalanceHistory']['id']);
                //unset($DistDistributorBalanceHistory['DistDistributorBalanceHistory']['balance']);
                //pr($DistDistributorBalanceHistory);die();
                unset($DistDistributorBalanceHistory['DistDistributorBalanceHistory']['id']);
                $this->DistDistributorBalanceHistory->create();
                $this->DistDistributorBalanceHistory->save($DistDistributorBalanceHistory['DistDistributorBalanceHistory']);
                
                $this->Session->setFlash(__('Limit added successfully!'), 'flash/success');
                $this->redirect(array('action' => 'index'));
                    
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
        $this->set('page_title', 'Edit Distributor Balance');
        $this->DistDistributorBalance->id = $id;
        if (!$this->DistDistributorBalance->exists($id)) {
            throw new NotFoundException(__('Invalid Dealer Wise Limit'));
        }
        $this->check_data_by_company('DistDistributorBalance',$id);
        //for company and office list

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
            
        }
        

        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

        $this->set(compact('offices'));
        //for company and office list
        
        
        $this->loadModel('DistDistributorBalance');
        if ($this->request->is('post') || $this->request->is('put')) {
            //pr($this->request->data);die();
            $this->request->data['DistDistributorBalance']['updated_by'] = $this->UserAuth->getUserId();
            $this->request->data['DistDistributorBalance']['effective_date'] = date("Y-m-d", strtotime($this->request->data['DistDistributorBalance']['effective_date']));
            $this->request->data['DistDistributorBalance']['updated_at'] = $this->current_datetime();

            $this->DistDistributorBalance->create();
            if($this->DistDistributorBalance->save($this->request->data['DistDistributorBalance'])){
                $DistDistributorBalanceHistoryUpdate['id']=$this->request->data['DistDistributorBalanceHistory']['id'];
                $DistDistributorBalanceHistoryUpdate['effective_end_date']=$this->request->data['DistDistributorBalance']['effective_date'];
                //pr($DistDistributorBalanceHistoryUpdate);die();
                $this->loadModel('DistDistributorBalanceHistory');
                $this->DistDistributorBalanceHistory->create();
                $this->DistDistributorBalanceHistory->save($DistDistributorBalanceHistoryUpdate); 

                /*-----------Insert into DistDistributorBalanceHistory Table-----------*/
                //$DistDistributorBalanceHistory['DistDistributorBalanceHistory']=$this->request->data['DistDistributorBalance'];
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['dist_distributor_balance_id']=$id;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['balance']=$this->request->data['DistDistributorBalance']['balance'];
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['is_active']=$this->request->data['DistDistributorBalance']['is_active'];
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['effective_start_date']=$this->request->data['DistDistributorBalance']['effective_date'];
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['created_at']=$this->request->data['DistDistributorBalance']['updated_at'];
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['created_by']=$this->UserAuth->getUserId();;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['updated_at']=$this->request->data['DistDistributorBalance']['updated_at'];
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['updated_by']=$this->UserAuth->getUserId();
                unset($DistDistributorBalanceHistory['DistDistributorBalanceHistory']['id']);
                //pr($DistDistributorBalanceHistory);die();
                $this->DistDistributorBalanceHistory->create();
                $this->DistDistributorBalanceHistory->save($DistDistributorBalanceHistory['DistDistributorBalanceHistory']);
            }

            $this->Session->setFlash(__('Data has successfully updated.'), 'flash/success');
            $this->redirect(array('action' => 'index'));
            
        } else {
            $options = array('conditions' => array('DistDistributorBalance.' . $this->DistDistributorBalance->primaryKey => $id));
            $this->request->data = $this->DistDistributorBalance->find('first', $options);
            //pr($this->request->data);die();
        }
        //pr($this->request->data);die();
        $this->loadModel('DistDistributor');
        $distDistributors = $this->DistDistributor->find('list',array('conditions'=>array('DistDistributor.is_active'=> 1)));
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
        $this->check_data_by_company('DistDistributorBalance',$id);
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



    
    //download excel 

      public function download_xl($office_id=0,$name=0) {

        $this->loadModel('Office');
        $this->loadModel('DistDistributorBalance');
        
        $conditions=array();
        
        if ($office_id == 0) {
            $office_parent_id = $this->UserAuth->getOfficeParentId();
            if($office_parent_id!=0)
            {
              $conditions = array('DistDistributorBalance.office_id' => $office_parent_id,'DistDistributor.is_active'=> 1,);
            }
        }
        else 
        {
            $conditions = array('DistDistributorBalance.office_id' => $office_id,'DistDistributor.is_active'=> 1,);
        }
        
        if($name)
        {
           $conditions['DistDistributorBalance.name like'] = "%".$name."%";
        }
        
     
       
        $distributors = $this->DistDistributorBalance->find('all', array(
            'conditions' => $conditions,
            'joins'=>array(
                array(
                    'table'=>'offices',
                    'alias'=>'Office',
                    'type' => 'LEFT',
                    'conditions'=>array('Office.id= DistDistributor.office_id')
                    )
                ),
            'fields'=> array('DistDistributorBalance.*','DistDistributor.id','DistDistributor.name','Office.office_name'),
            'order' => array('DistDistributor.name'),
            'recursive' => 1
        ));


    $table = '<table border="1"><tbody>
    <tr>
        <th>Serial No</th>    
        <th>Office Name</th>
        <th>Distributor Name</th>
        <th>Balance</th>
    </tr>
    ';
            $sl=1;
            $total=0;
            foreach ($distributors as $dis_data) {
                
                $distributorblance_id = $dis_data['DistDistributorBalance']['id'];
                $distributorblance_id_name = $dis_data['DistDistributor']['name'];
                $office_name = $dis_data['Office']['office_name'];
                $distributorblance_id_blance = $dis_data['DistDistributorBalance']['balance'];
                $total+=$distributorblance_id_blance;
                    $table .= '<tr>
                    <td>' . $sl++ . '</td>
                    <td>' . $office_name . '</td>
                    <td>' . $distributorblance_id_name . '</td>
                    <td>' . $distributorblance_id_blance . '</td>
                </tr>
                ';
            }
        $table .= '<tr>
         <td colspan="3" style="text-align: right; ">Total : </td>
            <td>' . $total . '</td>
        </tr>'
        ;

        $table .= '</tbody></table>';
        header("Content-Type: application/vnd.ms-excel");
        header('Content-Disposition: attachment; filename="Distributorblance.xls"');
        header("Cache-Control: ");
        header("Pragma: ");
        header("Expires: 0");
        echo $table;
        $this->autoRender = false;
    }


    //----------------------------------------------

    public function admin_get_distribute(){
        $office_id = $this->request->data['office_id'];
        $rs = array(array('id' => '', 'name' => '---- All -----'));
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        $this->loadModel('DistDistributor');
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
               $dist_conditions = array('DistDistributor.id'=>array_keys($tso_dist_list),'DistDistributor.is_active'=> 1);
            }
            elseif($user_group_id == 1034){
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first',array(
                    'conditions'=>array('DistUserMapping.sales_person_id'=>$sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
        
                $dist_conditions = array('DistDistributor.id'=>$distributor_id,'DistDistributor.is_active'=> 1);
            }
            else{
                
                $dist_conditions = array('DistDistributor.office_id'=>$office_id,'DistDistributor.is_active'=> 1);
            }

        $distributor_list = $this->DistDistributor->find('all',array('conditions'=>$dist_conditions,
            'order'=> array('DistDistributor.name'=>'asc'),
            'recursive'=> -1,
        ));

        $data_array= array();
        foreach ($distributor_list as $key => $value) {
            $data_array[]= array(
                'id'=> $value['DistDistributor']['id'],
                'name'=> $value['DistDistributor']['name'],
            );
        }

        if (!empty($distributor_list)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function admin_get_balance_transaction_type(){
        $dist_distributor_id = $this->request->data['dist_distributor_id'];
        $rs = array(array('id' => '', 'name' => '---- Select -----'));
        $this->loadModel('DistDistributorBalance');
        $this->loadModel('DistBalanceTransactionType');

        $distributor_balance_info = $this->DistDistributorBalance->find('first',array('conditions'=>array(
            'DistDistributorBalance.dist_distributor_id'=>$dist_distributor_id),
        ));
        
        $data_array = "<option value=''>--- Select ---</option>";
        if($distributor_balance_info){
            $transaction_type = $this->DistBalanceTransactionType->find('all',array(
                'conditions'=>array(
                    'OR' => array(
                        array('DistBalanceTransactionType.id IN'=> array(1,6)),
                        array('DistBalanceTransactionType.status'=> 1)
                    )
                ),
            ));
            //$dist_balance = $distributor_balance_info['DistDistributorBalance']['balance'];
        }
        else{
            $transaction_type = $this->DistBalanceTransactionType->find('all',array(
                'conditions'=>array(
                    'id'=> 7,
                ),
            ));
            //$dist_balance = 0;
        }
        foreach ($transaction_type as $key => $data) {
            $k = $data['DistBalanceTransactionType']['id'];
            $v = $data['DistBalanceTransactionType']['name'];
            $data_array .= "<option value='$k'>$v</option>";
        }

        echo $data_array;
        $this->autoRender = false;
    }
    public function admin_get_last_balance_ammount(){
        $dist_distributor_id = $this->request->data['dist_distributor_id'];
        $transaction_type_id = $this->request->data['transaction_type_id'];
        //pr($dist_distributor_id);
        $this->loadModel('DistDistributorBalance');
        $this->loadModel('DistBalanceTransactionType');
        $balance_inout_info = $this->DistBalanceTransactionType->find('first',array('conditions'=>array(
            'DistBalanceTransactionType.id'=>$transaction_type_id),
        ));
        
        $distributor_balance_info = $this->DistDistributorBalance->find('first',array('conditions'=>array(
            'DistDistributorBalance.dist_distributor_id'=>$dist_distributor_id),
        ));
        /*pr($distributor_balance_info);
        die();*/
        $dist_balance = $distributor_balance_info['DistDistributorBalance']['balance'];
        if($balance_inout_info){
            $balance_inout = $balance_inout_info['DistBalanceTransactionType']['inout'];
        }
        if($distributor_balance_info){
            if($dist_balance > 0){
                $data_array = array(
                    'balance' => $dist_balance,
                    'balance_inout' => $balance_inout
                );
            }
        }
        //pr($data_array);die();
        echo json_encode($data_array);
        $this->autoRender = false;
    }


    public function balance_history_download_xl($id, $from_date, $to_date)
    {

        $from_date = date('Y-m-d', strtotime($from_date));
		$to_date = date('Y-m-d', strtotime($to_date));

        if($from_date != '0' AND $to_date != '0'){
            $con[] = array(
                'DistDistributorBalanceHistory.transaction_date >=' => $from_date,
                'DistDistributorBalanceHistory.transaction_date <=' => $to_date
            );
        }

        $con[] = array(
            'DistDistributorBalanceHistory.dist_distributor_balance_id' => $id
        );

        $this->loadModel('DistDistributorBalanceHistory');
        $options = array(
            'conditions' => $con,
            'order'=>array('DistDistributorBalanceHistory.id DESC'),
    	);
        
        $list = $this->DistDistributorBalanceHistory->find('all', $options);

		$table='<table border="1"><tbody>
		<tr>
			<td>SL.</td>
			<td>Transaction Date</td>
			<td>Transaction Head</td>
			<td>Transaction Type</td>
			<td>Transaction Amount</td>
			<td>Balance</td>
		</tr>
		';
        $sl = 1;
        $total =0;
        $total_price = 0;
		foreach($list as $val)
		{
            
			$dbdate = date('Y-m-d', strtotime($val['DistDistributorBalanceHistory']['transaction_date']));
			$dbname = $val['DistBalanceTransactionType']['name'];
			$dbcredit = $val['DistDistributorBalanceHistory']['balance_type']==1?'Credit':'Debit';
			$amount = $val['DistDistributorBalanceHistory']['transaction_amount'];
			$balance =  $val['DistDistributorBalanceHistory']['balance'];
	
			$table.='<tr>
                    <td>'.$sl.'</td>
                    <td>'.$dbdate.'</td>
                    <td>'.$dbname.'</td>
					<td>'.$dbcredit.'</td>
					<td>'.$amount.'</td>
					<td>'.$balance.'</td>
				</tr>
            ';
            $total_price =  $total_price + $total;
            $sl++;
		}

		$table.='</tbody></table>';

        $filename = "dist_distributor_balances_". time() . ".xls";

   		header('Content-Type:application/force-download');
   		header('Content-Disposition: attachment; filename="'.$filename.'"');
    	header("Cache-Control: ");
    	header("Pragma: ");
   		echo $table;
    	$this->autoRender=false;
	}

}
