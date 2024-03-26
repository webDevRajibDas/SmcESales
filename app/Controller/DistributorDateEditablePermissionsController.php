<?php
App::uses('AppController', 'Controller');

/**
 * Deposits Controller
 *
 * @property Deposit $Deposit
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistributorDateEditablePermissionsController extends AppController {

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

        ini_set('max_execution_time', 1000);   
        $this->set('page_title', 'distributor date editable issues list');
        
       

        $office_parent_id = $this->UserAuth->getOfficeParentId();

        if($office_parent_id == 0){

            $conditions = array('Order.confirmed >'=> 0,
                'DistributorDateEditablePermission.created_at >=' => $this->current_date() . ' 00:00:00', 
                'DistributorDateEditablePermission.created_at <=' => $this->current_date() . ' 23:59:59');

            $office_conditions = array(
                    'office_type_id' => 2,
                    "NOT" => array( "id" => array(30, 31, 37))
                );
            
        }
        else{
             $conditions = array('Order.confirmed >'=> 0,
                'DistributorDateEditablePermission.created_at >=' => $this->current_date() . ' 00:00:00', 
                'DistributorDateEditablePermission.created_at <=' => $this->current_date() . ' 23:59:59');

            $office_conditions = array(
                'office_type_id' => 2,
                'id'=> $this->Session->read('Office.id'),
            );
        }


        $permited = $this->DistributorDateEditablePermission->find('all',array(
            'fields'=>array(
                'Order.id',
                'Order.order_no',
                'Order.order_date',
                'Order.gross_value',
                'Territory.name',
                'Outlet.name',
                'Market.name',
                'DistributorDateEditablePermission.remarks',
                'DistributorDateEditablePermission.recommender_note',
                'DistributorDateEditablePermission.approval_note',
                'DistributorDateEditablePermission.status'
                ), 
            'conditions'=> $conditions,
            'joins'=>array(
                array(
                    'table'=>'territories',
                    'alias'=>'Territory',
                    'type' => 'LEFT',
                    'conditions'=>array('Territory.id= Order.territory_id')
                ),
                array(
                    'table'=>'outlets',
                    'alias'=>'Outlet',
                    'type' => 'LEFT',
                    'conditions'=>array('Outlet.id= Order.outlet_id')
                ),
                array(
                    'table'=>'markets',
                    'alias'=>'Market',
                    'type' => 'LEFT',
                    'conditions'=>array('Market.id= Order.market_id')
                ),
                
            ),
            'order'=>array('DistributorDateEditablePermission.id DESC')
        ));

      //pr($permited);exit();

        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

        $distributors = array();
        $office_id = 0;
        if($office_id){
            $this->loadModel('DistDistributor');
            $distributor_info = $this->DistDistributor->find('all',array('conditions'=>array(
                'DistDistributor.office_id' => $office_id,
                'DistDistributor.is_active' => 1),
            'order'=>array('DistDistributor.name'=>'asc'),
           // 'recursive'=> -1
            ));
            
            foreach ($distributor_info as $key => $value) {
                if($value['DistOutletMap']['outlet_id'] != null){
                    $distributors[$value['DistOutletMap']['outlet_id']]=$value['DistDistributor']['name'];
                }
            }
    
        }

        
        $this->set(compact('offices','permited', 'distributors'));

        // pr($permited);die();
    }

    public function admin_create_requisition() {
        ini_set('max_execution_time', 1000);   
        $this->set('page_title', 'Create Requisitions');
        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');
        $this->loadModel('Office');
        $this->loadModel('Territory');
        $this->loadModel('Order');
        $this->loadModel('SystemNotification');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if($office_parent_id == 0){
            $territory_conditions = array();
            $office_conditions = array('office_type_id'=> 2);
        }
        else{
            $territory_conditions = array('office_id'=> $this->UserAuth->getOfficeId());
            $office_conditions = array('id'=> $this->UserAuth->getOfficeId());
        }
        $office_list = $this->Office->find('list',array(
            'conditions'=>$office_conditions,
            'order' => array('Office.office_name' => 'asc')
        ));
        $territory_list = $this->Territory->find('list',array(
            'conditions'=>$territory_conditions,
            'order' => array('Territory.name' => 'asc')
        ));
        $this->set(compact('territory_list','office_list'));
       
        if($this->request->is('post')){            
            $orders = $this->request->data['DistributorDateEditablePermission']['order_id'];
            $order_info = $this->Order->find('all',array(
                'conditions'=>array(
                    'Order.id'=>$orders
                ),
                'fields'=>array('Order.*'),
                'recursive'=> -1
            ));
            $order_for_editable = array();

            $deposit_no_lists = array();

            foreach ($order_info as $key => $value) {
                $order_for_editable[$value['Order']['id']] = $value['Order']['office_id'];
                
            }

            $data_array = array();
            foreach ($orders as $key => $value) {
                if(!empty($value)){
                    $data = array();
                    $order_id = $value;
                    $data['order_id'] = $order_id;
                    $data['remarks'] = $this->request->data['DistributorDateEditablePermission']['remarks'][$key];
                    $data['office_id'] = $order_for_editable[$order_id];
                    $data['requisition_at'] = $this->current_datetime();
                    $data['requisition_by'] = $this->UserAuth->getUserId();
                    $data['status'] = 0;
                    $data['created_at'] = $this->current_datetime();
                    $data['created_by'] = $this->UserAuth->getUserId();
                    $data['updated_at'] = $this->current_datetime();
                    $data['updated_by'] = $this->UserAuth->getUserId();

                    $data_array[] = $data;
                }
            }
            $this->loadModel('DistributorDateEditablePermission');
            $this->DistributorDateEditablePermission->create();
            if($this->DistributorDateEditablePermission->saveAll($data_array)){
                $message = "Requision Sbmited";
                $this->Session->setFlash(__($message), 'flash/success');
                $this->redirect(array('action' => 'index'));
            }else{
                $message = "Requision is not Sbmited";
                $this->Session->setFlash(__($message), 'flash/error');
            }

            
        }
        $instrument_types = array(
                '1'=>'Cash',
                '2'=>'Instrument'
                );
        $this->set(compact('instrument_types'));
    }
    public function admin_recommender_list(){
        ini_set('max_execution_time', 1000);   
        $this->set('page_title', 'Deposit Recommendations');
        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');
        $this->loadModel('Office');
        $this->loadModel('Territory');

        $office_id = $this->UserAuth->getOfficeId();
        

        $office_parent_id = $this->UserAuth->getOfficeParentId();

        if($office_parent_id == 0){

            $conditions = array(
                'DistributorDateEditablePermission.status '=> 0,
                );
            $office_conditions = array(
                    'office_type_id' => 2,
                    "NOT" => array( "id" => array(30, 31, 37))
                );
            
        }
        else{
             $conditions = array(
                'DistributorDateEditablePermission.status '=> 0,
                );

            $office_conditions = array(
                'office_type_id' => 2,
                'id'=> $this->Session->read('Office.id'),
            );
        }
        
        $permited = $this->DistributorDateEditablePermission->find('all',array(
            'fields'=>array(
                'Order.id',
                'Order.order_no',
                'Order.order_date',
                'Order.gross_value',
                'Territory.name',
                'Outlet.name',
                'Market.name',
                'DistributorDateEditablePermission.id',
                'DistributorDateEditablePermission.remarks',
                'DistributorDateEditablePermission.recommender_note',
                'DistributorDateEditablePermission.approval_note',
                'DistributorDateEditablePermission.status'
                ), 
            'conditions'=> $conditions,
            'joins'=>array(
                array(
                    'table'=>'territories',
                    'alias'=>'Territory',
                    'type' => 'LEFT',
                    'conditions'=>array('Territory.id= Order.territory_id')
                ),
                array(
                    'table'=>'outlets',
                    'alias'=>'Outlet',
                    'type' => 'LEFT',
                    'conditions'=>array('Outlet.id= Order.outlet_id')
                ),
                array(
                    'table'=>'markets',
                    'alias'=>'Market',
                    'type' => 'LEFT',
                    'conditions'=>array('Market.id= Order.market_id')
                ),
                
            ),
            'order'=>array('DistributorDateEditablePermission.id DESC')
        ));

      //pr($permited);exit();

        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

        $distributors = array();
        $office_id = 0;
        if($office_id){
            $this->loadModel('DistDistributor');
            $distributor_info = $this->DistDistributor->find('all',array('conditions'=>array(
                'DistDistributor.office_id' => $office_id,
                'DistDistributor.is_active' => 1),
            'order'=>array('DistDistributor.name'=>'asc'),
           // 'recursive'=> -1
            ));
            
            foreach ($distributor_info as $key => $value) {
                if($value['DistOutletMap']['outlet_id'] != null){
                    $distributors[$value['DistOutletMap']['outlet_id']]=$value['DistDistributor']['name'];
                }
            }
    
        }

        $this->set(compact('offices','permited', 'distributors'));

    }
    public function admin_approval_list(){
        ini_set('max_execution_time', 1000);   
        $this->set('page_title', 'Deposit Approval');
        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');
        
        $office_parent_id = $this->UserAuth->getOfficeParentId();

        if($office_parent_id == 0){

            $conditions = array(
                'DistributorDateEditablePermission.status' => 1
            );

            $office_conditions = array(
                    'office_type_id' => 2,
                    "NOT" => array( "id" => array(30, 31, 37))
                );
            
        }
        else{
            $conditions = array(
                'DistributorDateEditablePermission.status' => 1,
                'DistributorDateEditablePermission.office_id' => $this->UserAuth->getOfficeId()
            );

            $office_conditions = array(
                'office_type_id' => 2,
                'id'=> $this->Session->read('Office.id'),
            );
        }


        $permited = $this->DistributorDateEditablePermission->find('all',array(
            'fields'=>array(
                'Order.id',
                'Order.order_no',
                'Order.order_date',
                'Order.gross_value',
                'Territory.name',
                'Outlet.name',
                'Market.name',
                'DistributorDateEditablePermission.id',
                'DistributorDateEditablePermission.remarks',
                'DistributorDateEditablePermission.recommender_note',
                'DistributorDateEditablePermission.approval_note',
                'DistributorDateEditablePermission.status'
                ), 
            'conditions'=> $conditions,
            'joins'=>array(
                array(
                    'table'=>'territories',
                    'alias'=>'Territory',
                    'type' => 'LEFT',
                    'conditions'=>array('Territory.id= Order.territory_id')
                ),
                array(
                    'table'=>'outlets',
                    'alias'=>'Outlet',
                    'type' => 'LEFT',
                    'conditions'=>array('Outlet.id= Order.outlet_id')
                ),
                array(
                    'table'=>'markets',
                    'alias'=>'Market',
                    'type' => 'LEFT',
                    'conditions'=>array('Market.id= Order.market_id')
                ),
                
            ),
            'order'=>array('DistributorDateEditablePermission.id DESC')
        ));

      //pr($permited);exit();

        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

        $distributors = array();
        $office_id = 0;
        if($office_id){
            $this->loadModel('DistDistributor');
            $distributor_info = $this->DistDistributor->find('all',array('conditions'=>array(
                'DistDistributor.office_id' => $office_id,
                'DistDistributor.is_active' => 1),
            'order'=>array('DistDistributor.name'=>'asc'),
           // 'recursive'=> -1
            ));
            
            foreach ($distributor_info as $key => $value) {
                if($value['DistOutletMap']['outlet_id'] != null){
                    $distributors[$value['DistOutletMap']['outlet_id']]=$value['DistDistributor']['name'];
                }
            }
    
        }

        
        $this->set(compact('offices','permited', 'distributors'));
    }





    public function admin_get_territory(){
        $this->loadModel('Territory');
        $office_id = $this->request->data['office_id'];
        $rs = array(array('id' => '', 'name' => '--- Select ----'));
        $territory_list = $this->Territory->find('all', array(
            'fields' => array('Territory.id as id','Territory.name as name'),
            'conditions' => array(
                'Territory.office_id' => $office_id              
                ),
            'order' => array('Territory.name'=>'asc'),
            'recursive' => -1
            ));
        $data_array = Set::extract($territory_list, '{n}.0');
        if(!empty($territory_list)){
            echo json_encode(array_merge($rs,$data_array));
        }else{
            echo json_encode($rs);
        } 
        $this->autoRender = false;
    }



    public function get_distributor_issues_list(){

        $date_from = $this->request->data['date_from'];
        $date_to = $this->request->data['date_to'];
        $office_id = $this->request->data['office_id'];
        $distribut_outlet_id = $this->request->data['distribut_outlet_id'];

     
        ini_set('max_execution_time', 1000);

        $this->loadModel('DistributorDateEditablePermission');
        $order_conditions = array();
        $existed_order_conditions=array();

        if(!empty($date_from)){
            $Deposit_date_from = date('Y-m-d', strtotime($date_from));
            $order_conditions[] = array('Order.order_date >=' => $Deposit_date_from);
            $existed_order_conditions['Order.order_date >=']=$Deposit_date_from;
        }
        if(!empty($date_to)){
            $Deposit_date_to = date('Y-m-d', strtotime($date_to));
            $order_conditions[] = array('Order.order_date <=' => $Deposit_date_to);
            $existed_order_conditions['Order.order_date <=']=$Deposit_date_to;
        }
        if(!empty($office_id))
        {
            $order_conditions[] = array('Order.office_id' => $office_id);
            $existed_order_conditions['DistributorDateEditablePermission.office_id']=$office_id;
        }
        if(!empty($distribut_outlet_id)){
            $order_conditions[] = array('Order.outlet_id' => $distribut_outlet_id);
        }
        
        
        $order_conditions[] = array(
			'OR'=>array(
				array('Order.editable' => 0),
				array('Order.editable' => NULL)
			)
		);
       

        $this->loadModel('DistributorDateEditablePermission');
        $existed_order_conditions['DistributorDateEditablePermission.status']=array(0,1);
        $exist_permited_order = $this->DistributorDateEditablePermission->find('list',array(
            'conditions'=>$existed_order_conditions,
            'fields'=>array('DistributorDateEditablePermission.order_id','DistributorDateEditablePermission.id'),
            'joins'=>array(
                array(
                    'table'=>'orders',
                    'alias'=>'Order',
                    'type'=>'inner',
                    'conditions'=>'Order.id=DistributorDateEditablePermission.order_id'
                    ),
                ),
            'recursive'=>-1
        ));
        $exist_permited_order_list = array_keys($exist_permited_order);

        $order_conditions[] = array('NOT'=>array('Order.id' => $exist_permited_order_list));
        $order_conditions[] = array('Order.confirmed >' => 0);

        $this->loadModel('Order');
        $Orders = $this->Order->find('all',array(
            'fields'=>array(
                'Order.id',
                'Order.order_no',
                'Order.order_date',
                'Order.gross_value',
                'Order.confirmed',
                'Territory.name',
                'Outlet.name',
                'Market.name',
                ), 
            'conditions'=> $order_conditions,
            'joins'=>array(
                array(
                    'table'=>'territories',
                    'alias'=>'Territory',
                    'type' => 'LEFT',
                    'conditions'=>array('Territory.id= Order.territory_id')
                ),
                 array(
                    'table'=>'outlets',
                    'alias'=>'Outlet',
                    'type' => 'LEFT',
                    'conditions'=>array('Outlet.id= Order.outlet_id')
                ),
                array(
                    'table'=>'markets',
                    'alias'=>'Market',
                    'type' => 'LEFT',
                    'conditions'=>array('Market.id= Order.market_id')
                ),
               
            ),
            'recursive' => -1
        ));

        //pr($Orders);exit();
		
        echo json_encode($Orders);
        $this->autoRender = false;
    }
    public function get_order_editable_permited_list(){

        $date_from = $this->request->data['date_from'];
        $date_to = $this->request->data['date_to'];
        $office_id = $this->request->data['office_id'];
        $distribut_outlet_id = $this->request->data['distribut_outlet_id'];
        
       
        ini_set('max_execution_time', 1000);   
        $deposit_conditions = array();

        if(!empty($date_from)){
            $deposit_date_from = date('Y-m-d', strtotime($date_from));
            $deposit_conditions[] = array('DistributorDateEditablePermission.created_at >=' => $deposit_date_from);
        }
        if(!empty($date_to)){
            $deposit_date_to = date('Y-m-d', strtotime($date_to));
            $deposit_conditions[] = array('DistributorDateEditablePermission.created_at <=' => $deposit_date_to);
        }
        if(!empty($office_id)){
            $deposit_conditions[] = array('Order.office_id' => $office_id);
        }

        if(!empty($distribut_outlet_id)){
            $deposit_conditions[] = array('Order.outlet_id' => $distribut_outlet_id);
        }
        

        $this->loadModel('DistributorDateEditablePermission');
        $permited = $this->DistributorDateEditablePermission->find('all',array(
            'fields'=>array(
                'Order.id',
                'Order.order_no',
                'Order.order_date',
                'Order.gross_value',
                'Territory.name',
                'Outlet.name',
                'Market.name',
                'DistributorDateEditablePermission.remarks',
                'DistributorDateEditablePermission.recommender_note',
                'DistributorDateEditablePermission.approval_note',
                'DistributorDateEditablePermission.status'
                ), 
            'conditions'=> $deposit_conditions,
            'joins'=>array(
                array(
                    'table'=>'territories',
                    'alias'=>'Territory',
                    'type' => 'LEFT',
                    'conditions'=>array('Territory.id= Order.territory_id')
                ),
                array(
                    'table'=>'outlets',
                    'alias'=>'Outlet',
                    'type' => 'LEFT',
                    'conditions'=>array('Outlet.id= Order.outlet_id')
                ),
                array(
                    'table'=>'markets',
                    'alias'=>'Market',
                    'type' => 'LEFT',
                    'conditions'=>array('Market.id= Order.market_id')
                ),
            ),
            'order'=>array('DistributorDateEditablePermission.id DESC')
        ));
        $data_array =array();
        if(!empty($permited)){
            foreach ($permited as $key => $value) {
               $data = array();
               $data['order_id'] = $value['Order']['id'];
               $data['order_no'] = $value['Order']['order_no'];
               $data['outlet'] = $value['Outlet']['name'];
               $data['market'] = $value['Market']['name'];
               $data['territorie'] = $value['Territory']['name'];
               $data['order_toal'] = $value['Order']['gross_value'];
               $data['order_date'] = $value['Order']['order_date'];
               $data['remarks'] = $value['DistributorDateEditablePermission']['remarks'];
               $data['recommender_note'] = $value['DistributorDateEditablePermission']['recommender_note'];
               $data['approval_note'] = $value['DistributorDateEditablePermission']['approval_note'];
               $data['status'] = $value['DistributorDateEditablePermission']['status'];

               $data_array[] = $data;
            }
        }
        
        echo json_encode($data_array);
        $this->autoRender = false;
    }


    public function get_order_recommended_list(){
       
        $date_from = $this->request->data['date_from'];
        $date_to = $this->request->data['date_to'];
        $office_id = $this->request->data['office_id'];
        $distribut_outlet_id = $this->request->data['distribut_outlet_id'];
        
       
        ini_set('max_execution_time', 1000);   
        $deposit_conditions = array();

        if(!empty($date_from)){
            $deposit_date_from = date('Y-m-d', strtotime($date_from));
            $deposit_conditions[] = array('DistributorDateEditablePermission.created_at >=' => $deposit_date_from);
        }
        if(!empty($date_to)){
            $deposit_date_to = date('Y-m-d', strtotime($date_to));
            $deposit_conditions[] = array('DistributorDateEditablePermission.created_at <=' => $deposit_date_to);
        }
        if(!empty($office_id)){
            $deposit_conditions[] = array('Order.office_id' => $office_id);
        }

        if(!empty($distribut_outlet_id)){
            $deposit_conditions[] = array('Order.outlet_id' => $distribut_outlet_id);
        }
        
        $deposit_conditions[] = array('DistributorDateEditablePermission.status' => 0);
        $this->loadModel('DistributorDateEditablePermission');
        $permited = $this->DistributorDateEditablePermission->find('all',array(
            'fields'=>array(
                'Order.id',
                'Order.order_no',
                'Order.order_date',
                'Order.gross_value',
                'Territory.name',
                'Outlet.name',
                'Market.name',
                'DistributorDateEditablePermission.id',
                'DistributorDateEditablePermission.remarks',
                'DistributorDateEditablePermission.recommender_note',
                'DistributorDateEditablePermission.approval_note',
                'DistributorDateEditablePermission.status'
                ), 
            'conditions'=> $deposit_conditions,
            'joins'=>array(
                array(
                    'table'=>'territories',
                    'alias'=>'Territory',
                    'type' => 'LEFT',
                    'conditions'=>array('Territory.id= Order.territory_id')
                ),
                array(
                    'table'=>'outlets',
                    'alias'=>'Outlet',
                    'type' => 'LEFT',
                    'conditions'=>array('Outlet.id= Order.outlet_id')
                ),
                array(
                    'table'=>'markets',
                    'alias'=>'Market',
                    'type' => 'LEFT',
                    'conditions'=>array('Market.id= Order.market_id')
                ),
            ),
            'order'=>array('DistributorDateEditablePermission.id DESC')
        ));
        $data_array =array();
        if(!empty($permited)){
            foreach ($permited as $key => $value) {
               $data = array();
               $data['order_id'] = $value['Order']['id'];
               $data['order_no'] = $value['Order']['order_no'];
               $data['outlet'] = $value['Outlet']['name'];
               $data['market'] = $value['Market']['name'];
               $data['territorie'] = $value['Territory']['name'];
               $data['order_toal'] = $value['Order']['gross_value'];
               $data['order_date'] = $value['Order']['order_date'];
               $data['ddep_id'] = $value['DistributorDateEditablePermission']['id'];
               $data['remarks'] = $value['DistributorDateEditablePermission']['remarks'];
               $data['recommender_note'] = $value['DistributorDateEditablePermission']['recommender_note'];
               $data['approval_note'] = $value['DistributorDateEditablePermission']['approval_note'];
               $data['status'] = $value['DistributorDateEditablePermission']['status'];

               $data_array[] = $data;
            }
        }
        echo json_encode($data_array);
        $this->autoRender = false;

    }


    public function order_recommended(){
        $id = $this->request->data['id'];
        $recommender_note = $this->request->data['recommender_note'];

        $data_array = array();
        $data_array['id'] = $id;
        $data_array['recommender_note'] = $recommender_note;
        $data_array['recommended_at'] = $this->current_datetime();
        $data_array['recommended_by'] = $this->UserAuth->getUserId();
        $data_array['status'] = 1;

        if($this->DistributorDateEditablePermission->save($data_array)){
            $msg[0] = '1';

        }
        else{
            $msg[0] = '0';
        }

        echo json_encode($msg);
        $this->autoRender = false;
    }


    public function get_deposit_approved_list(){
         $office_id = $this->request->data['office_id'];
        $territory_id = $this->request->data['territory_id'];
        $type = $this->request->data['type'];
       
        ini_set('max_execution_time', 1000);   
        $deposit_conditions = array();

       
        if(!empty($office_id)){
            $deposit_conditions[] = array('Territory.office_id' => $office_id);
        }

        if(!empty($territory_id)){
            $deposit_conditions[] = array('Deposit.territory_id' => $territory_id);
        }
        if(!empty($type)){
            $deposit_conditions[] = array('Deposit.type' => $type);
        }
        $deposit_conditions[] = array('DepositEditablePermission.status'=> 1);
        $this->loadModel('DepositEditablePermission');
        $permited = $this->DepositEditablePermission->find('all',array(
            'fields'=>array(
                'Deposit.id',
                'Territory.office_id',
                'Deposit.territory_id',
                'Territory.name',
                'Office.office_name',
                'Type.name',
                'InstrumentType.name',
                'Deposit.deposit_date',
                'Deposit.deposit_amount',
                'Deposit.slip_no',
                'DepositEditablePermission.id',
                'DepositEditablePermission.remarks',
                'DepositEditablePermission.recommender_note',
                ), 
            'conditions'=> $deposit_conditions,
            'joins'=>array(
                array(
                    'table'=>'territories',
                    'alias'=>'Territory',
                    'type' => 'LEFT',
                    'conditions'=>array('Territory.id= Deposit.territory_id')
                ),
                array(
                    'table'=>'offices',
                    'alias'=>'Office',
                    'type' => 'LEFT',
                    'conditions'=>array('Office.id= Territory.office_id')
                ),
                array(
                    'table'=>'instrument_type',
                    'alias'=>'Type',
                    'type' => 'LEFT',
                    'conditions'=>array('Type.id= Deposit.type')
                ),
                array(
                    'table'=>'instrument_type',
                    'alias'=>'InstrumentType',
                    'type' => 'LEFT',
                    'conditions'=>array('InstrumentType.id= Deposit.instrument_type')
                ),
            ),
            'order'=>array('DepositEditablePermission.id DESC')
        ));
        $data_array =array();
        if(!empty($permited)){
            foreach ($permited as $key => $value) {
               $data = array();

               $data['id'] = $value['DepositEditablePermission']['id'];
               $data['deposit_id'] = $value['Deposit']['id'];
               $data['office_name'] = $value['Office']['office_name'];
               $data['territory_name'] = $value['Territory']['name'];
               $data['type'] = $value['Type']['name'];
               $data['inst_type'] = $value['InstrumentType']['name'];
               $data['slip_no'] = $value['Deposit']['slip_no'];
               $data['deposit_date'] = $value['Deposit']['deposit_date'];
               $data['deposit_amount'] = $value['Deposit']['deposit_amount'];
               $data['remarks'] = $value['DepositEditablePermission']['remarks'];
               $data['recommender_note'] = $value['DepositEditablePermission']['recommender_note'];

               $data_array[] = $data;
            }
        }
        
        echo json_encode($data_array);
        $this->autoRender = false;
    }
    public function order_approved(){
        $id = $this->request->data['id'];
        $approval_note = $this->request->data['approval_note'];

        $this->loadModel('Order');
        $this->loadModel('DistributorDateEditablePermission');
        $this->loadModel('SystemNotification');
        $deposit_editable_info = $this->DistributorDateEditablePermission->find('first',array(
            'conditions'=>array(
                'DistributorDateEditablePermission.id'=>$id
            ),
            //'recursive'=> -1
        ));
        $order_id = $deposit_editable_info['DistributorDateEditablePermission']['order_id'];
        $data_array = array();
        $data_array['id'] = $id;
        $data_array['approval_note'] = $approval_note;
        $data_array['approval_at'] = $this->current_datetime();
        $data_array['approval_by'] = $this->UserAuth->getUserId();
        $data_array['status'] = 2;

        if($this->DistributorDateEditablePermission->save($data_array)){
            $msg[0] = '1';
            //Update Deposit
            $deposit_arr['id'] = $order_id;
            $deposit_arr['editable'] = 1;

            //$this->Deposit->Query("ALTER TABLE deposits DISABLE TRIGGER deposit_logs_after_update");

            $this->Order->save($deposit_arr);
            
            //$this->Deposit->Query("ALTER TABLE deposits ENABLE TRIGGER deposit_logs_after_update");
            //end Update
          
        }
        else{
            $msg[0] = '0';
        }

        echo json_encode($msg);
        $this->autoRender = false;
    }
    public function order_not_approved(){
        $id = $this->request->data['id'];
        $approval_note = $this->request->data['approval_note'];

        $this->loadModel('Deposit');
        $this->loadModel('DistributorDateEditablePermission');
        $this->loadModel('SystemNotification');

        $data_array = array();
        $data_array['id'] = $id;
        $data_array['approval_note'] = $approval_note;
        $data_array['approval_at'] = $this->current_datetime();
        $data_array['approval_by'] = $this->UserAuth->getUserId();
        $data_array['status'] = 3;

        if($this->DistributorDateEditablePermission->save($data_array)){
            $msg[0] = '1';
        }
        else{
            $msg[0] = '0';
        }

        echo json_encode($msg);
        $this->autoRender = false;
    }


    public function update_shown_notifications(){
        $this->loadModel('SystemNotification');
        $id = $this->request->data['id'];
        $data = array();
        $data['id'] = id;
        $data['notification_seen'] = 1;
        $data['status'] = 1;
        if($this->SystemNotification->save($data)){
            $msg = 1;
        }else{
            $msg = 0;
        }
        echo json_encode($msg);
        $this->autoRender = false;
    }
}



