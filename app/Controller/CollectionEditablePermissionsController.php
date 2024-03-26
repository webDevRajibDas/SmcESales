<?php
App::uses('AppController', 'Controller');

/**
 * Deposits Controller
 *
 * @property Deposit $Deposit
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class CollectionEditablePermissionsController extends AppController {

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
        $this->set('page_title', 'Collection Edit Permissions');
        $this->loadModel('Office');
        $this->loadModel('Territory');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if($office_parent_id == 0){
            $territory_conditions = array();
            $office_conditions = array('office_type_id'=> 2);
            $collection_permited_conditions = array();
        }
        else{
            $territory_conditions = array('office_id'=> $this->UserAuth->getOfficeId());
            $office_conditions = array('id'=> $this->UserAuth->getOfficeId());
            $collection_permited_conditions = array('Collection.office_id' => $this->UserAuth->getOfficeId());
        }
        $office_list = $this->Office->find('list',array(
            'conditions'=>$office_conditions,
            'order' => array('Office.office_name' => 'asc')
        ));
        $territory_list = $this->Territory->find('list',array(
            'conditions'=>$territory_conditions,
            'order' => array('Territory.name' => 'asc')
        ));
        

        $permited = $this->CollectionEditablePermission->find('all',array(
            'fields'=>array(
                'Collection.id',
                'Collection.memo_id',
                'Collection.memo_no',
                'Collection.memo_value',
                'Collection.memo_date',
                'Collection.type',
                'Collection.instrument_type',
                'Collection.instrumentRefNo',
                'Collection.collectionAmount',
                'Collection.collectionDate',
                'Collection.deposit_amount',
                'Office.office_name',
                'Memo.memo_no',
                'Outlet.name',
                'Type.name',
                'InstrumentType.name',
                'CollectionEditablePermission.remarks',
                'CollectionEditablePermission.recommender_note',
                'CollectionEditablePermission.approval_note',
                'CollectionEditablePermission.status'
                ), 
            'conditions'=> $collection_permited_conditions,
            'joins'=>array(
                array(
                    'table'=>'memos',
                    'alias'=>'Memo',
                    'type' => 'LEFT',
                    'conditions'=>array('Memo.id= Collection.memo_id')
                ),
                array(
                    'table'=>'offices',
                    'alias'=>'Office',
                    'type' => 'LEFT',
                    'conditions'=>array('Office.id= Memo.office_id')
                ),
                array(
                    'table'=>'outlets',
                    'alias'=>'Outlet',
                    'type' => 'LEFT',
                    'conditions'=>array('Outlet.id= Collection.outlet_id')
                ),
                array(
                    'table'=>'instrument_type',
                    'alias'=>'Type',
                    'type' => 'LEFT',
                    'conditions'=>array('Type.id= Collection.type')
                ),
                array(
                    'table'=>'instrument_type',
                    'alias'=>'InstrumentType',
                    'type' => 'LEFT',
                    'conditions'=>array('InstrumentType.id= Collection.instrument_type')
                ),
            ),
            'order'=>array('CollectionEditablePermission.id DESC')
        ));

        $instrument_types = array(
                '1'=>'Cash',
                '2'=>'Instrument'
                );
        $this->set(compact('instrument_types'));
        $this->set(compact('territory_list','office_list','permited'));

         //pr($permited);die();
    }

    public function admin_create_requisition() {
        ini_set('max_execution_time', 1000);   
        $this->set('page_title', 'Create Requisitions');
        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');
        $this->loadModel('Office');
        $this->loadModel('Territory');
        $this->loadModel('Collection');
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

            $collection = $this->request->data['CollectionEditablePermission']['collection_id'];


            


            $collection_info = $this->Collection->find('all',array(
                'conditions'=>array(
                    'Collection.id'=>$collection
                ),
                'joins'=>array(
                    array(
                            'table'=>'memos',
                            'alias'=>'Memo',
                            'type' => 'LEFT',
                            'conditions'=>array('Memo.id= Collection.memo_id')
                        ),
                    ),
                'fields'=>array('Collection.*','Memo.office_id'),
                'recursive'=> -1
            ));

            

            $collection_for_editable = array();
            $deposit_no_lists = array();
            foreach ($collection_info as $key => $value) {
                $collection_for_editable[$value['Collection']['id']] = $value['Memo']['office_id'];
                
            }



            $data_array = array();
            foreach ($collection as $key => $value) {
                if(!empty($value)){
                    $data = array();
                    $collection_id = $value;
                    $data['collection_id'] = $collection_id;
                    $data['remarks'] = $this->request->data['CollectionEditablePermission']['remarks'][$key];
                    $data['office_id'] = $collection_for_editable[$collection_id];
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
            $this->loadModel('CollectionEditablePermission');
            $this->CollectionEditablePermission->create();
            if($this->CollectionEditablePermission->saveAll($data_array)){
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
        $this->set('page_title', 'Collection Recommendations');
        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');
        $this->loadModel('Office');
        $this->loadModel('Territory');
        

        $office_id = $this->UserAuth->getOfficeId();
        $this->update_notifications('CollectionEditablePermissions','admin_recommender_list');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if($office_parent_id == 0){
            $territory_conditions = array();
            $office_conditions = array('office_type_id'=> 2);
            $collection_permited_conditions = array('CollectionEditablePermission.status'=> 0);
        }
        else{
            $territory_conditions = array('office_id'=> $this->UserAuth->getOfficeId());
            $office_conditions = array('id'=> $this->UserAuth->getOfficeId());
            $collection_permited_conditions = array('CollectionEditablePermission.status'=> 0,'CollectionEditablePermission.office_id' => $this->UserAuth->getOfficeId());
        }
        $office_list = $this->Office->find('list',array(
            'conditions'=>$office_conditions,
            'order' => array('Office.office_name' => 'asc')
        ));
        $territory_list = $this->Territory->find('list',array(
            'conditions'=>$territory_conditions,
            'order' => array('Territory.name' => 'asc')
        ));

        //pr($collection_permited_conditions);exit();
        

         $permited = $this->CollectionEditablePermission->find('all',array(
            'fields'=>array(
                'Collection.id',
                'Collection.memo_id',
                'Collection.memo_no',
                'Collection.memo_value',
                'Collection.memo_date',
                'Collection.type',
                'Collection.instrument_type',
                'Collection.instrumentRefNo',
                'Collection.collectionAmount',
                'Collection.collectionDate',
                'Collection.deposit_amount',
                'Office.office_name',
                'Memo.memo_no',
                'Outlet.name',
                'Type.name',
                'InstrumentType.name',
                'CollectionEditablePermission.id',
                'CollectionEditablePermission.remarks',
                'CollectionEditablePermission.recommender_note',
                'CollectionEditablePermission.approval_note',
                'CollectionEditablePermission.status'
                ), 
            'conditions'=> $collection_permited_conditions,
            'joins'=>array(
                array(
                    'table'=>'memos',
                    'alias'=>'Memo',
                    'type' => 'LEFT',
                    'conditions'=>array('Memo.id= Collection.memo_id')
                ),
                array(
                    'table'=>'offices',
                    'alias'=>'Office',
                    'type' => 'LEFT',
                    'conditions'=>array('Office.id= Memo.office_id')
                ),
                array(
                    'table'=>'outlets',
                    'alias'=>'Outlet',
                    'type' => 'LEFT',
                    'conditions'=>array('Outlet.id= Collection.outlet_id')
                ),
                array(
                    'table'=>'instrument_type',
                    'alias'=>'Type',
                    'type' => 'LEFT',
                    'conditions'=>array('Type.id= Collection.type')
                ),
                array(
                    'table'=>'instrument_type',
                    'alias'=>'InstrumentType',
                    'type' => 'LEFT',
                    'conditions'=>array('InstrumentType.id= Collection.instrument_type')
                ),
            ),
            'order'=>array('CollectionEditablePermission.id DESC')
        ));

        // pr($permited);exit();

        $instrument_types = array(
                '1'=>'Cash',
                '2'=>'Instrument'
                );
        $this->set(compact('instrument_types'));
        $this->set(compact('territory_list','office_list','permited'));

    }
    public function admin_approval_list(){
        ini_set('max_execution_time', 1000);   
        $this->set('page_title', 'Collection Approval');
        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');
        $this->loadModel('Office');
        $this->loadModel('Territory');

        $this->update_notifications('CollectionEditablePermissions','admin_approval_list');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if($office_parent_id == 0){
            $territory_conditions = array();
            $office_conditions = array('office_type_id'=> 2);
            $collection_permited_conditions = array('CollectionEditablePermission.status'=> 1);
        }
        else{
            $territory_conditions = array('office_id'=> $this->UserAuth->getOfficeId());
            $office_conditions = array('id'=> $this->UserAuth->getOfficeId());
            $collection_permited_conditions = array('CollectionEditablePermission.status'=> 1,'CollectionEditablePermission.office_id' => $this->UserAuth->getOfficeId());
        }
        $office_list = $this->Office->find('list',array(
            'conditions'=>$office_conditions,
            'order' => array('Office.office_name' => 'asc')
        ));
        $territory_list = $this->Territory->find('list',array(
            'conditions'=>$territory_conditions,
            'order' => array('Territory.name' => 'asc')
        ));
        

       $permited = $this->CollectionEditablePermission->find('all',array(
            'fields'=>array(
                'Collection.id',
                'Collection.memo_id',
                'Collection.memo_no',
                'Collection.memo_value',
                'Collection.memo_date',
                'Collection.type',
                'Collection.instrument_type',
                'Collection.instrumentRefNo',
                'Collection.collectionAmount',
                'Collection.collectionDate',
                'Collection.deposit_amount',
                'Office.office_name',
                'Memo.memo_no',
                'Outlet.name',
                'Type.name',
                'InstrumentType.name',
                'CollectionEditablePermission.id',
                'CollectionEditablePermission.remarks',
                'CollectionEditablePermission.recommender_note',
                'CollectionEditablePermission.approval_note',
                'CollectionEditablePermission.status'
                ), 
            'conditions'=> $collection_permited_conditions,
            'joins'=>array(
                array(
                    'table'=>'memos',
                    'alias'=>'Memo',
                    'type' => 'LEFT',
                    'conditions'=>array('Memo.id= Collection.memo_id')
                ),
                array(
                    'table'=>'offices',
                    'alias'=>'Office',
                    'type' => 'LEFT',
                    'conditions'=>array('Office.id= Memo.office_id')
                ),
                array(
                    'table'=>'outlets',
                    'alias'=>'Outlet',
                    'type' => 'LEFT',
                    'conditions'=>array('Outlet.id= Collection.outlet_id')
                ),
                array(
                    'table'=>'instrument_type',
                    'alias'=>'Type',
                    'type' => 'LEFT',
                    'conditions'=>array('Type.id= Collection.type')
                ),
                array(
                    'table'=>'instrument_type',
                    'alias'=>'InstrumentType',
                    'type' => 'LEFT',
                    'conditions'=>array('InstrumentType.id= Collection.instrument_type')
                ),
            ),
            'order'=>array('CollectionEditablePermission.id DESC')
        ));

        $instrument_types = array(
                '1'=>'Cash',
                '2'=>'Instrument'
                );
        $this->set(compact('instrument_types'));
        $this->set(compact('territory_list','office_list','permited'));
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



    public function get_collection_list(){
        $date_from = $this->request->data['date_from'];
        $date_to = $this->request->data['date_to'];
        $office_id = $this->request->data['office_id'];
        $territory_id = $this->request->data['territory_id'];
        $market_id = $this->request->data['market_id'];
        $outlet_id = $this->request->data['outlet_id'];
        $type = $this->request->data['type'];
     
        ini_set('max_execution_time', 1000);

        $this->loadModel('CollectionEditablePermission');

        $Collection_conditions = array();

        $existed_collection_conditions=array();

        if(!empty($date_from)){

            $Collection_date_from = date('Y-m-d', strtotime($date_from));
            $Collection_conditions[] = array('Collection.collectionDate >=' => $Collection_date_from);
            $existed_collection_conditions['Collection.collectionDate >=']=$Collection_date_from;
            
        }
        if(!empty($date_to)){

            $Collection_date_to = date('Y-m-d', strtotime($date_to));
            $Collection_conditions[] = array('Collection.collectionDate <=' => $Collection_date_to);
            $existed_collection_conditions['Collection.collectionDate <=']=$Collection_date_to;
        }
        if(!empty($office_id))
        {
            $Collection_conditions[] = array('Memo.office_id' => $office_id);
            $existed_collection_conditions['CollectionEditablePermission.office_id']=$office_id;
            
        }
        if(!empty($territory_id)){
            $Collection_conditions[] = array('Memo.territory_id' => $territory_id);
        }
        if(!empty($type)){
            $Collection_conditions[] = array('Collection.type' => $type);
        }
        
        $Collection_conditions[] = array(
            'OR'=>array(
                array('Collection.editable' => 0),
                array('Collection.editable' => NULL)
            )
        );

        $Collection_conditions[]=array('Collection.is_credit_collection' =>1);
        $existed_collection_conditions[]=array('Collection.is_credit_collection' =>1);

        $this->loadModel('CollectionEditablePermission');
        $existed_collection_conditions['CollectionEditablePermission.status']=array(0,1);
        $exist_permited_Collection = $this->CollectionEditablePermission->find('list',array(
            'conditions'=>$existed_collection_conditions,
            'fields'=>array('CollectionEditablePermission.collection_id','CollectionEditablePermission.id'),
            'joins'=>array(
                array(
                    'table'=>'collections',
                    'alias'=>'Collection',
                    'type'=>'inner',
                    'conditions'=>'Collection.id=CollectionEditablePermission.collection_id'
                    ),
                ),
            'recursive'=>-1
        ));

        //print_r($exist_permited_Collection);exit();
        $exist_permited_Collection_list = array_keys($exist_permited_Collection);

        $Collection_conditions[] = array('NOT'=>array('Collection.id' => $exist_permited_Collection_list));
        
        $this->loadModel('Collection');
        $Collections = $this->Collection->find('all',array(
            'fields'=>array(
                'Collection.*',
                'Memo.market_id', 
                'Memo.territory_id', 
                'Memo.outlet_id',
                'Outlet.name',
                'InstrumentType.name'
                ), 
            'conditions'=> $Collection_conditions,
            'joins'=>array(
                array(
                    'table' => 'memos',
                    'alias' => 'Memo',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Memo.memo_no = Collection.memo_no'
                    )
                ),
                array(
                    'table' => 'outlets',
                    'alias' => 'Outlet',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Outlet.id = Collection.outlet_id'
                    )
                ),
                array(
                    'table'=>'instrument_type',
                    'alias'=>'InstrumentType',
                    'type' => 'LEFT',
                    'conditions'=>array('InstrumentType.id= Collection.instrument_type')
                ),
            ),
            'limit' => 100,
            'recursive' => -1
        ));
		
		/*echo $this->Deposit->getLastQuery();
        pr($Deposits);exit;*/
        //pr($Collections);exit;
		
        echo json_encode($Collections);
        $this->autoRender = false;
    }
    public function get_deposit_editable_permited_list(){

        $date_from = $this->request->data['date_from'];
        $date_to = $this->request->data['date_to'];
        $office_id = $this->request->data['office_id'];
        $territory_id = $this->request->data['territory_id'];
        $type = $this->request->data['type'];
       
        ini_set('max_execution_time', 1000);   
        $deposit_conditions = array();

        if(!empty($date_from)){
            $deposit_date_from = date('Y-m-d', strtotime($date_from));
            $deposit_conditions[] = array('Deposit.deposit_date >=' => $deposit_date_from);
        }
        if(!empty($date_to)){
            $deposit_date_to = date('Y-m-d', strtotime($date_to));
            $deposit_conditions[] = array('Deposit.deposit_date <=' => $deposit_date_to);
        }
        if(!empty($office_id)){
            $deposit_conditions[] = array('Territory.office_id' => $office_id);
        }

        if(!empty($territory_id)){
            $deposit_conditions[] = array('Deposit.territory_id' => $territory_id);
        }
        if(!empty($type)){
            $deposit_conditions[] = array('Deposit.type' => $type);
        }
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
                'DepositEditablePermission.remarks',
                'DepositEditablePermission.recommender_note',
                'DepositEditablePermission.approval_note',
                'DepositEditablePermission.status'
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
               $data['approval_note'] = $value['DepositEditablePermission']['approval_note'];
               $data['status'] = $value['DepositEditablePermission']['status'];

               $data_array[] = $data;
            }
        }
        
        echo json_encode($data_array);
        $this->autoRender = false;
    }


    public function get_collection_editable_permited_list(){
		$date_from = $this->request->data['date_from'];
        $date_to = $this->request->data['date_to'];
        $office_id = $this->request->data['office_id'];
        $territory_id = $this->request->data['territory_id'];
        //$market_id = $this->request->data['market_id'];
        //$outlet_id = $this->request->data['outlet_id'];
        $type = $this->request->data['type'];
		
		ini_set('max_execution_time', 1000);

        $this->loadModel('CollectionEditablePermission');

        $collection_permited_conditions = array();

        if(!empty($date_from)){

            $Collection_date_from = date('Y-m-d', strtotime($date_from));
            $collection_permited_conditions[] = array('Collection.collectionDate >=' => $Collection_date_from);
            
            
        }
        if(!empty($date_to)){

            $Collection_date_to = date('Y-m-d', strtotime($date_to));
            $collection_permited_conditions[] = array('Collection.collectionDate <=' => $Collection_date_to);
            
        }
        if(!empty($office_id))
        {
            $collection_permited_conditions[] = array('Memo.office_id' => $office_id);
            
        }
        if(!empty($territory_id)){
            $collection_permited_conditions[] = array('Memo.territory_id' => $territory_id);
        }
        if(!empty($type)){
            $collection_permited_conditions[] = array('Collection.type' => $type);
        }
		
		$permited = $this->CollectionEditablePermission->find('all',array(
            'fields'=>array(
                'Collection.id',
                'Collection.memo_id',
                'Collection.memo_no',
                'Collection.memo_value',
                'Collection.memo_date',
                'Collection.type',
                'Collection.instrument_type',
                'Collection.instrumentRefNo',
                'Collection.collectionAmount',
                'Collection.collectionDate',
                'Collection.deposit_amount',
                'Office.office_name',
                'Memo.memo_no',
                'Outlet.name',
                'Type.name',
                'InstrumentType.name',
                'CollectionEditablePermission.remarks',
                'CollectionEditablePermission.recommender_note',
                'CollectionEditablePermission.approval_note',
                'CollectionEditablePermission.status'
                ), 
            'conditions'=> $collection_permited_conditions,
            'joins'=>array(
                array(
                    'table'=>'memos',
                    'alias'=>'Memo',
                    'type' => 'LEFT',
                    'conditions'=>array('Memo.id= Collection.memo_id')
                ),
                array(
                    'table'=>'offices',
                    'alias'=>'Office',
                    'type' => 'LEFT',
                    'conditions'=>array('Office.id= Memo.office_id')
                ),
                array(
                    'table'=>'outlets',
                    'alias'=>'Outlet',
                    'type' => 'LEFT',
                    'conditions'=>array('Outlet.id= Collection.outlet_id')
                ),
                array(
                    'table'=>'instrument_type',
                    'alias'=>'Type',
                    'type' => 'LEFT',
                    'conditions'=>array('Type.id= Collection.type')
                ),
                array(
                    'table'=>'instrument_type',
                    'alias'=>'InstrumentType',
                    'type' => 'LEFT',
                    'conditions'=>array('InstrumentType.id= Collection.instrument_type')
                ),
            ),
            'order'=>array('CollectionEditablePermission.id DESC')
        ));
		
		$data_array =array();
        if(!empty($permited)){
            foreach ($permited as $key => $value) {
               $data = array();
               $data['id'] = $value['Collection']['id'];
               $data['office_name'] = $value['Office']['office_name'];
               $data['memo_no'] = $value['Collection']['memo_no'];
               $data['outlet_name'] = $value['Outlet']['name'];
               $data['type'] = $value['Type']['name'];
               $data['instrument'] = $value['InstrumentType']['name'];
               $data['instrumentRefNo'] = $value['Collection']['instrumentRefNo'];
               $data['collectionDate'] = $value['Collection']['collectionDate'];
               $data['collectionAmount'] = $value['Collection']['collectionAmount'];
               $data['remarks'] = $value['CollectionEditablePermission']['remarks'];
               $data['recommender_note'] = $value['CollectionEditablePermission']['recommender_note'];
               $data['approval_note'] = $value['CollectionEditablePermission']['approval_note'];
               $data['status'] = $value['CollectionEditablePermission']['status'];
               $data_array[] = $data;
            }
        }
		
        
        echo json_encode($data_array);
        $this->autoRender = false;
    }
	
	public function get_collection_recommended_list(){
       
        $office_id = $this->request->data['office_id'];
        $territory_id = $this->request->data['territory_id'];
        $market_id = $this->request->data['market_id'];
        $outlet_id = $this->request->data['outlet_id'];
        $type = $this->request->data['type'];
       
        ini_set('max_execution_time', 1000);   
        $collection_conditions = array();

       
        if(!empty($office_id)){
            $collection_conditions[] = array('Memo.office_id' => $office_id);
        }

        if(!empty($territory_id)){
            $collection_conditions[] = array('Memo.territory_id' => $territory_id);
        }
        if(!empty($market_id)){
            $collection_conditions[] = array('Memo.market_id' => $market_id);
        }
        if(!empty($outlet_id)){
            $collection_conditions[] = array('Memo.outlet_id' => $outlet_id);
        }
        if(!empty($type)){
            $collection_conditions[] = array('Collection.type' => $type);
        }

        $collection_conditions[] = array('CollectionEditablePermission.status'=> 0);
        $this->loadModel('CollectionEditablePermission');
        $permited = $this->CollectionEditablePermission->find('all',array(
            'fields'=>array(
                'Collection.id',
                'Collection.memo_id',
                'Collection.memo_no',
                'Collection.memo_value',
                'Collection.memo_date',
                'Collection.type',
                'Collection.instrument_type',
                'Collection.instrumentRefNo',
                'Collection.collectionAmount',
                'Collection.collectionDate',
                'Collection.deposit_amount',
                'Office.office_name',
                'Memo.memo_no',
                'Outlet.name',
                'Type.name',
                'InstrumentType.name',
                'CollectionEditablePermission.id',
                'CollectionEditablePermission.remarks',
                'CollectionEditablePermission.recommender_note',
                'CollectionEditablePermission.approval_note',
                'CollectionEditablePermission.status'
                ), 
            'conditions'=> $collection_conditions,
            'joins'=>array(
                array(
                    'table'=>'memos',
                    'alias'=>'Memo',
                    'type' => 'LEFT',
                    'conditions'=>array('Memo.id= Collection.memo_id')
                ),
                array(
                    'table'=>'offices',
                    'alias'=>'Office',
                    'type' => 'LEFT',
                    'conditions'=>array('Office.id= Memo.office_id')
                ),
                array(
                    'table'=>'outlets',
                    'alias'=>'Outlet',
                    'type' => 'LEFT',
                    'conditions'=>array('Outlet.id= Collection.outlet_id')
                ),
                array(
                    'table'=>'instrument_type',
                    'alias'=>'Type',
                    'type' => 'LEFT',
                    'conditions'=>array('Type.id= Collection.type')
                ),
                array(
                    'table'=>'instrument_type',
                    'alias'=>'InstrumentType',
                    'type' => 'LEFT',
                    'conditions'=>array('InstrumentType.id= Collection.instrument_type')
                ),
            ),
            'order'=>array('CollectionEditablePermission.id DESC')
        ));
        $data_array =array();

       // pr($permited);exit();

        if(!empty($permited)){
            foreach ($permited as $key => $value) {
               $data = array();

               $data['id'] = $value['CollectionEditablePermission']['id'];
               $data['office_name'] = $value['Office']['office_name'];
               $data['memo'] = $value['Collection']['memo_no'];
               $data['outlet'] = $value['Outlet']['name'];
               $data['type'] = $value['Type']['name'];
               $data['inst_type'] = $value['InstrumentType']['name'];
               $data['collectionDate'] = $value['Collection']['collectionDate'];
               $data['collectionAmount'] = $value['Collection']['collectionAmount'];
               $data['remarks'] = $value['CollectionEditablePermission']['remarks'];

               $data_array[] = $data;
            }
        }
        echo json_encode($data_array);
        $this->autoRender = false;
    }
    public function collection_recommended(){
        $id = $this->request->data['id'];
        $recommender_note = $this->request->data['recommender_note'];

        $data_array = array();
        $data_array['id'] = $id;
        $data_array['recommender_note'] = $recommender_note;
        $data_array['recommended_at'] = $this->current_datetime();
        $data_array['recommended_by'] = $this->UserAuth->getUserId();
        $data_array['status'] = 1;

        if($this->CollectionEditablePermission->save($data_array)){
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
    public function collection_approved(){
        $id = $this->request->data['id'];
        $approval_note = $this->request->data['approval_note'];

        $this->loadModel('Collection');
        $this->loadModel('CollectionEditablePermission');
        $this->loadModel('SystemNotification');
        $collection_editable_info = $this->CollectionEditablePermission->find('first',array(
            'conditions'=>array(
                'CollectionEditablePermission.id'=>$id
            ),
            //'recursive'=> -1
        ));
        $collection_id = $collection_editable_info['CollectionEditablePermission']['collection_id'];
        $data_array = array();
        $data_array['id'] = $id;
        $data_array['approval_note'] = $approval_note;
        $data_array['approval_at'] = $this->current_datetime();
        $data_array['approval_by'] = $this->UserAuth->getUserId();
        $data_array['status'] = 2;

        if($this->CollectionEditablePermission->save($data_array)){
            $msg[0] = '1';
            //Update Deposit
            $deposit_arr['id'] = $collection_id;
            $deposit_arr['editable'] = 1;
            $this->Collection->Query("ALTER TABLE collections DISABLE TRIGGER collection_logs_after_update");
            $this->Collection->save($deposit_arr);
            $this->Collection->Query("ALTER TABLE collections ENABLE TRIGGER collection_logs_after_update");
            //end Update
          
        }
        else{
            $msg[0] = '0';
        }

        echo json_encode($msg);
        $this->autoRender = false;
    }
    public function collection_not_approved(){
        $id = $this->request->data['id'];
        $approval_note = $this->request->data['approval_note'];

        $this->loadModel('Collection');
        $this->loadModel('CollectionEditablePermission');
        $this->loadModel('SystemNotification');

        $data_array = array();
        $data_array['id'] = $id;
        $data_array['approval_note'] = $approval_note;
        $data_array['approval_at'] = $this->current_datetime();
        $data_array['approval_by'] = $this->UserAuth->getUserId();
        $data_array['status'] = 3;

        if($this->CollectionEditablePermission->save($data_array)){
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



