<?php
App::uses('AppController', 'Controller');

/**
 * Memos Controller
 *
 * @property Memo $Memo
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class MemoEditablePermissionsController extends AppController {

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
        $this->set('page_title', 'Memo Edit Permissions');
        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');
        $this->loadModel('Office');
        $this->loadModel('Territory');
        $this->update_notifications('MemoEditablePermissions','admin_create_requisition');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if($office_parent_id == 0){
            $territory_conditions = array();
            $office_conditions = array('office_type_id'=> 2);
            $memo_permited_conditions = array();
        }
        else{
            $territory_conditions = array('office_id'=> $this->UserAuth->getOfficeId());
            $office_conditions = array('id'=> $this->UserAuth->getOfficeId());
            $memo_permited_conditions = array('Memo.office_id' => $this->UserAuth->getOfficeId());
        }
        $office_list = $this->Office->find('list',array(
            'conditions'=>$office_conditions,
            'order' => array('Office.office_name' => 'asc')
        ));
        $territory_list = $this->Territory->find('list',array(
            'conditions'=>$territory_conditions,
            'order' => array('Territory.name' => 'asc')
        ));
        

        $permited = $this->MemoEditablePermission->find('all',array(
            'fields'=>array('Memo.id','Memo.memo_no','Market.name','Outlet.name','Territory.name','Thana.name','MemoEditablePermission.remarks','MemoEditablePermission.recommender_note','MemoEditablePermission.approval_note','Office.office_name','MemoEditablePermission.memo_no','MemoEditablePermission.status'),
            'conditions'=> $memo_permited_conditions,
            'joins'=>array(
                array(
                    'table'=>'markets',
                    'alias'=>'Market',
                    'type' => 'LEFT',
                    'conditions'=>array('Market.id= Memo.market_id')
                ),
                array(
                    'table'=>'outlets',
                    'alias'=>'Outlet',
                    'type' => 'LEFT',
                    'conditions'=>array('Outlet.id= Memo.outlet_id')
                ),
                array(
                    'table'=>'territories',
                    'alias'=>'Territory',
                    'type' => 'LEFT',
                    'conditions'=>array('Territory.id= Memo.territory_id')
                ),
                array(
                    'table'=>'thanas',
                    'alias'=>'Thana',
                    'type' => 'LEFT',
                    'conditions'=>array('Thana.id= Memo.thana_id')
                ),
                array(
                    'table'=>'offices',
                    'alias'=>'Office',
                    'type' => 'LEFT',
                    'conditions'=>array('Office.id= Memo.office_id')
                )
            ),
            'order'=>array('MemoEditablePermission.id DESC')
        ));
        $this->set(compact('territory_list','office_list','permited'));

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
        $this->loadModel('Memo');
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
            
            $memos = $this->request->data['MemoEditablePermission']['memo_id'];
            $memo_info = $this->Memo->find('all',array(
                'conditions'=>array(
                    'Memo.id'=>$memos
                ),
                'recursive'=> -1
            ));
            $memo_for_editable = array();
            $memo_no_lists = array();
            foreach ($memo_info as $key => $value) {
                $memo_for_editable[$value['Memo']['id']] = $value['Memo']['office_id'];
                $memo_no_lists[$value['Memo']['id']] = $value['Memo']['memo_no'];
            }

            $data_array = array();
            $notification_array = array();
            foreach ($memos as $key => $value) {
                if(!empty($value)){

                    $data = array();
                    $memo_id = $value;
                    $data['memo_id'] = $memo_id;
                    $data['memo_no'] = $memo_no_lists[$memo_id];
                    $data['remarks'] = $this->request->data['MemoEditablePermission']['remarks'][$key];
                    $data['office_id'] = $memo_for_editable[$memo_id];
                    $data['requisition_at'] = $this->current_datetime();
                    $data['requisition_by'] = $this->UserAuth->getUserId();
                    $data['status'] = 0;
                    $data['created_at'] = $this->current_datetime();
                    $data['created_by'] = $this->UserAuth->getUserId();
                    $data['updated_at'] = $this->current_datetime();
                    $data['updated_by'] = $this->UserAuth->getUserId();

                    $data_array[] = $data;
                    
                    //notifications
                    $notification = array();
                    $notification['notification'] = "<a  href='".BASE_URL."admin/MemoEditablePermissions/recommender_list'>A requisition has for this ".$memo_no_lists[$memo_id]." Memo</a>";
                    $notification['controller'] = 'MemoEditablePermissions';
                    $notification['methods'] = 'admin_recommender_list';
                    $notification['status'] = 0;
                    $notification['notification_seen'] = 0;
                    $notification['office_id'] = $this->UserAuth->getOfficeId();
                    $notification['user_id'] = $this->UserAuth->getUserId();
                    $notification['created_at'] = $this->current_datetime();
                    $notification['created_by'] = $this->UserAuth->getUserId();
                    $notification['updated_at'] = $this->current_datetime();
                    $notification['updated_by'] = $this->UserAuth->getUserId();
                   
                    $notification_array[] = $notification;
                    //end notifications
                }
            }
            $this->loadModel('MemoEditablePermission');
            $this->MemoEditablePermission->create();
            if($this->MemoEditablePermission->saveAll($data_array)){

                $this->SystemNotification->create();
                $this->SystemNotification->saveAll($notification_array);

                $message = "Requision Sbmited";
                $this->Session->setFlash(__($message), 'flash/success');
                $this->redirect(array('action' => 'index'));
            }else{
                $message = "Requision is not Sbmited";
                $this->Session->setFlash(__($message), 'flash/error');
            }

            
        }
        
    }
    public function admin_recommender_list(){
        ini_set('max_execution_time', 1000);   
        $this->set('page_title', 'Memo Recommendations');
        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');
        $this->loadModel('Office');
        $this->loadModel('Territory');

        $office_id = $this->UserAuth->getOfficeId();
        $this->update_notifications('MemoEditablePermissions','admin_recommender_list');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if($office_parent_id == 0){
            $territory_conditions = array();
            $office_conditions = array('office_type_id'=> 2);
            $memo_permited_conditions = array('MemoEditablePermission.status'=> 0);
        }
        else{
            $territory_conditions = array('office_id'=> $this->UserAuth->getOfficeId());
            $office_conditions = array('id'=> $this->UserAuth->getOfficeId());
            $memo_permited_conditions = array('MemoEditablePermission.status'=> 0,'MemoEditablePermission.office_id' => $this->UserAuth->getOfficeId());
        }
        $office_list = $this->Office->find('list',array(
            'conditions'=>$office_conditions,
            'order' => array('Office.office_name' => 'asc')
        ));
        $territory_list = $this->Territory->find('list',array(
            'conditions'=>$territory_conditions,
            'order' => array('Territory.name' => 'asc')
        ));
        

        $permited = $this->MemoEditablePermission->find('all',array(
            'fields'=>array('MemoEditablePermission.id','Memo.id','Memo.memo_no','Market.name','Outlet.name','Territory.name','Thana.name','MemoEditablePermission.remarks','Office.office_name'),
            'conditions'=> $memo_permited_conditions,
            'joins'=>array(
                array(
                    'table'=>'markets',
                    'alias'=>'Market',
                    'type' => 'LEFT',
                    'conditions'=>array('Market.id= Memo.market_id')
                ),
                array(
                    'table'=>'outlets',
                    'alias'=>'Outlet',
                    'type' => 'LEFT',
                    'conditions'=>array('Outlet.id= Memo.outlet_id')
                ),
                array(
                    'table'=>'territories',
                    'alias'=>'Territory',
                    'type' => 'LEFT',
                    'conditions'=>array('Territory.id= Memo.territory_id')
                ),
                array(
                    'table'=>'thanas',
                    'alias'=>'Thana',
                    'type' => 'LEFT',
                    'conditions'=>array('Thana.id= Memo.thana_id')
                ),
                array(
                    'table'=>'offices',
                    'alias'=>'Office',
                    'type' => 'LEFT',
                    'conditions'=>array('Office.id= Memo.office_id')
                ),
            ),
            'order'=>array('MemoEditablePermission.id DESC')
        ));
        $this->set(compact('territory_list','office_list','permited'));

    }
    public function admin_approval_list(){
        ini_set('max_execution_time', 1000);   
        $this->set('page_title', 'Memo Approval');
        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');
        $this->loadModel('Office');
        $this->loadModel('Territory');

        $this->update_notifications('MemoEditablePermissions','admin_approval_list');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if($office_parent_id == 0){
            $territory_conditions = array();
            $office_conditions = array('office_type_id'=> 2);
            $memo_permited_conditions = array('MemoEditablePermission.status'=> 1);
        }
        else{
            $territory_conditions = array('office_id'=> $this->UserAuth->getOfficeId());
            $office_conditions = array('id'=> $this->UserAuth->getOfficeId());
            $memo_permited_conditions = array('MemoEditablePermission.status'=> 1,'MemoEditablePermission.office_id' => $this->UserAuth->getOfficeId());
        }
        $office_list = $this->Office->find('list',array(
            'conditions'=>$office_conditions,
            'order' => array('Office.office_name' => 'asc')
        ));
        $territory_list = $this->Territory->find('list',array(
            'conditions'=>$territory_conditions,
            'order' => array('Territory.name' => 'asc')
        ));
        

        $permited = $this->MemoEditablePermission->find('all',array(
            'fields'=>array('Memo.id','Memo.memo_no','Market.name','Outlet.name','Territory.name','Thana.name','MemoEditablePermission.id','MemoEditablePermission.remarks','MemoEditablePermission.recommender_note','Office.office_name'),
            'conditions'=> $memo_permited_conditions,
            'joins'=>array(
                array(
                    'table'=>'markets',
                    'alias'=>'Market',
                    'type' => 'LEFT',
                    'conditions'=>array('Market.id= Memo.market_id')
                ),
                array(
                    'table'=>'outlets',
                    'alias'=>'Outlet',
                    'type' => 'LEFT',
                    'conditions'=>array('Outlet.id= Memo.outlet_id')
                ),
                array(
                    'table'=>'territories',
                    'alias'=>'Territory',
                    'type' => 'LEFT',
                    'conditions'=>array('Territory.id= Memo.territory_id')
                ),
                array(
                    'table'=>'thanas',
                    'alias'=>'Thana',
                    'type' => 'LEFT',
                    'conditions'=>array('Thana.id= Memo.thana_id')
                ),
                array(
                    'table'=>'offices',
                    'alias'=>'Office',
                    'type' => 'LEFT',
                    'conditions'=>array('Office.id= Memo.office_id')
                )
            ),
            'order'=>array('MemoEditablePermission.id DESC')
        ));
        $this->set(compact('territory_list','office_list','permited'));
    }


    public function admin_view($id = null) {
        $this->set('page_title', 'Memo Details');
        $this->loadModel('Memo');
        $this->loadModel('MemoDetail');
        $this->Memo->unbindModel(array('hasMany' => array('MemoDetail')));
        $memo = $this->Memo->find('first', array(
            'conditions' => array('Memo.id' => $id)
            ));

        $this->loadModel('MemoDetail');
        if (!$this->Memo->exists($id)) {
            throw new NotFoundException(__('Invalid Memo'));
        }
        $this->MemoDetail->recursive = 0;
        $memo_details = $this->MemoDetail->find('all', array(
            'conditions' => array('MemoDetail.memo_id' => $id),
            'order' => array('Product.order'=>'asc')
            )
        );
        $this->set(compact('memo', 'memo_details'));
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
    public function admin_get_market(){
        $this->loadModel('Market');
        $territory_id = $this->request->data['territory_id'];
        $rs = array(array('id' => '', 'name' => '--- Select ----'));
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
    public function admin_get_market_by_thana(){
        $this->loadModel('Market');
        $thana_id = $this->request->data['thana_id'];
        $rs = array(array('id' => '', 'name' => '--- Select ----'));
        $market_list = $this->Market->find('all', array(
            'fields' => array('Market.id as id','Market.name as name'),
            'conditions' => array(
                'Market.thana_id' => $thana_id              
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
    public function admin_get_outlet(){
        $this->loadModel('Outlet');
        $market_id = $this->request->data['market_id'];
        $rs = array(array('id' => '', 'name' => '--- Select ----'));
        $outlet_list = $this->Outlet->find('all', array(
            'fields' => array('Outlet.id as id','Outlet.name as name'),
            'conditions' => array(
                'Outlet.market_id' => $market_id              
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


    public function get_memo_list(){
        $date_from = $this->request->data['date_from'];
        $date_to = $this->request->data['date_to'];
        $office_id = $this->request->data['office_id'];
        $territory_id = $this->request->data['territory_id'];
        $thana_id = $this->request->data['thana_id'];
        $market_id = $this->request->data['market_id'];
        $outlet_id = $this->request->data['outlet_id'];
        $memo_no = $this->request->data['memo_no'];
        ini_set('max_execution_time', 1000);

        $this->loadModel('MemoEditablePermission');
        $exist_permited_memo = $this->MemoEditablePermission->find('list',array(
            'fields'=>array('MemoEditablePermission.memo_id','MemoEditablePermission.id')
        ));
        $exist_permited_memo_list = array_keys($exist_permited_memo);
        $memo_conditions = array();
        $existed_memo_conditions=array();
        if(!empty($date_from)){
            $memo_date_from = date('Y-m-d', strtotime($date_from));
            $memo_conditions[] = array('Memo.memo_date >=' => $memo_date_from);
            $existed_memo_conditions['Memo.memo_date >=']=$memo_date_from;
        }
        if(!empty($date_to)){
            $memo_date_to = date('Y-m-d', strtotime($date_to));
            $memo_conditions[] = array('Memo.memo_date <=' => $memo_date_to);
            $existed_memo_conditions['Memo.memo_date <=']=$memo_date_to;
        }
        if(!empty($office_id))
        {
            $memo_conditions[] = array('Memo.office_id' => $office_id);
            $existed_memo_conditions['MemoEditablePermission.office_id']=$office_id;
        }
        if(!empty($territory_id)){
            $memo_conditions[] = array('Memo.territory_id' => $territory_id);
        }
        if(!empty($thana_id)){
            $memo_conditions[] = array('Memo.thana_id' => $thana_id);
        }
        if(!empty($market_id)){
            $memo_conditions[] = array('Memo.market_id' => $market_id);
        }
        if(!empty($outlet_id)){
            $memo_conditions[] = array('Memo.outlet_id' => $outlet_id);
        }
        if(!empty($memo_no)){
            $memo_conditions[] = array('Memo.memo_no like' => "%".$memo_no."%");
        }
        //$memo_conditions[] = array('Memo.action' => 1);
        $memo_conditions[] = array('Memo.status >' => 0);
        $memo_conditions[] = array(
			'OR'=>array(
				array('Memo.memo_editable' => 0),
				array('Memo.memo_editable' => NULL)
			)
		);

        $this->loadModel('MemoEditablePermission');
        $existed_memo_conditions['MemoEditablePermission.status']=array(0,1);
        $exist_permited_memo = $this->MemoEditablePermission->find('list',array(
            'conditions'=>$existed_memo_conditions,
            'fields'=>array('MemoEditablePermission.memo_id','MemoEditablePermission.id'),
            'joins'=>array(
                array(
                    'table'=>'memos',
                    'alias'=>'Memo',
                    'type'=>'inner',
                    'conditions'=>'Memo.id=MemoEditablePermission.memo_id'
                    ),
                ),
            'recursive'=>-1
        ));
        $exist_permited_memo_list = array_keys($exist_permited_memo);
        $memo_conditions[] = array('NOT'=>array('Memo.id' => $exist_permited_memo_list));
        $this->loadModel('Memo');
        $memos = $this->Memo->find('all',array(
            'fields'=>array('Memo.id','Memo.memo_no','Memo.market_id','Memo.office_id','Memo.outlet_id','Memo.territory_id','Memo.thana_id','Market.name','Outlet.name','Territory.name','Thana.name','Office.office_name'), 
            'conditions'=> $memo_conditions,
            'joins'=>array(
                array(
                    'table'=>'markets',
                    'alias'=>'Market',
                    'type' => 'LEFT',
                    'conditions'=>array('Market.id= Memo.market_id')
                ),
                array(
                    'table'=>'outlets',
                    'alias'=>'Outlet',
                    'type' => 'LEFT',
                    'conditions'=>array('Outlet.id= Memo.outlet_id')
                ),
                array(
                    'table'=>'territories',
                    'alias'=>'Territory',
                    'type' => 'LEFT',
                    'conditions'=>array('Territory.id= Memo.territory_id')
                ),
                array(
                    'table'=>'thanas',
                    'alias'=>'Thana',
                    'type' => 'LEFT',
                    'conditions'=>array('Thana.id= Memo.thana_id')
                ),
                array(
                    'table'=>'offices',
                    'alias'=>'Office',
                    'type' => 'LEFT',
                    'conditions'=>array('Office.id= Memo.office_id')
                ),
            ),
            'recursive' => -1
        ));
		
		/*echo $this->Memo->getLastQuery();
        pr($memos);exit;*/
		
        echo json_encode($memos);
        $this->autoRender = false;
    }
    public function get_memo_editable_permited_list(){

        $date_from = $this->request->data['date_from'];
        $date_to = $this->request->data['date_to'];
        $office_id = $this->request->data['office_id'];
        $memo_no = $this->request->data['memo_no'];
        ini_set('max_execution_time', 1000);   
        $memo_conditions = array();

        if(!empty($date_from)){
            $memo_date_from = date('Y-m-d', strtotime($date_from));
            $memo_conditions[] = array('Memo.memo_date >=' => $memo_date_from);
        }
        if(!empty($date_to)){
            $memo_date_to = date('Y-m-d', strtotime($date_to));
            $memo_conditions[] = array('Memo.memo_date <=' => $memo_date_to);
        }
        if(!empty($office_id)){
            $memo_conditions[] = array('Memo.office_id' => $office_id);
        }
        if(!empty($memo_no)){
            $memo_conditions[] = array('Memo.memo_no like' => "%".$memo_no."%");
        }

        $this->loadModel('MemoEditablePermission');
        $memos = $this->MemoEditablePermission->find('all',array(
            'fields'=>array('Memo.id','Memo.memo_no','Market.name','Outlet.name','Territory.name','Thana.name','MemoEditablePermission.remarks','MemoEditablePermission.recommender_note','MemoEditablePermission.approval_note','Office.office_name','MemoEditablePermission.memo_no','MemoEditablePermission.status'), 
            'conditions'=> $memo_conditions,
            'joins'=>array(
                array(
                    'table'=>'markets',
                    'alias'=>'Market',
                    'type' => 'LEFT',
                    'conditions'=>array('Market.id= Memo.market_id')
                ),
                array(
                    'table'=>'outlets',
                    'alias'=>'Outlet',
                    'type' => 'LEFT',
                    'conditions'=>array('Outlet.id= Memo.outlet_id')
                ),
                array(
                    'table'=>'territories',
                    'alias'=>'Territory',
                    'type' => 'LEFT',
                    'conditions'=>array('Territory.id= Memo.territory_id')
                ),
                array(
                    'table'=>'thanas',
                    'alias'=>'Thana',
                    'type' => 'LEFT',
                    'conditions'=>array('Thana.id= Memo.thana_id')
                ),
                array(
                    'table'=>'offices',
                    'alias'=>'Office',
                    'type' => 'LEFT',
                    'conditions'=>array('Office.id= Memo.office_id')
                )
            )
        ));
        $data_array =array();
        if(!empty($memos)){
            foreach ($memos as $key => $value) {
               $data = array();
               $data['memo_id'] = $value['Memo']['id'];
               $data['memo_no'] = $value['MemoEditablePermission']['memo_no'];
               $data['market_name'] = $value['Market']['name'];
               $data['outlet_name'] = $value['Outlet']['name'];
               $data['thana_name'] = $value['Thana']['name'];
               $data['territory_name'] = $value['Territory']['name'];
               $data['remarks'] = $value['MemoEditablePermission']['remarks'];
               $data['recommender_note'] = $value['MemoEditablePermission']['recommender_note'];
               $data['approval_note'] = $value['MemoEditablePermission']['approval_note'];
               $data['office_name'] = $value['Office']['office_name'];
               $data['status'] = $value['MemoEditablePermission']['status'];

               $data_array[] = $data;
            }
        }
        
        echo json_encode($data_array);
        $this->autoRender = false;
    }


    public function get_memo_recommended_list(){
        $office_id = $this->request->data['office_id'];
        $memo_no = $this->request->data['memo_no'];

        ini_set('max_execution_time', 1000);   
        $memo_conditions = array();

        if(!empty($office_id)){
            $memo_conditions[] = array('Memo.office_id' => $office_id);
        }
        if(!empty($memo_no)){
            $memo_conditions[] = array('Memo.memo_no like' => "%".$memo_no."%");
        }
        $memo_conditions[] = array('MemoEditablePermission.status'=> 0);

        $this->loadModel('MemoEditablePermission');
        $memos = $this->MemoEditablePermission->find('all',array(
            'fields'=>array('Memo.id','Memo.memo_no','Market.name','Outlet.name','Territory.name','Thana.name','MemoEditablePermission.id','MemoEditablePermission.remarks','Office.office_name'), 
            'conditions'=> $memo_conditions,
            'joins'=>array(
                array(
                    'table'=>'markets',
                    'alias'=>'Market',
                    'type' => 'LEFT',
                    'conditions'=>array('Market.id= Memo.market_id')
                ),
                array(
                    'table'=>'outlets',
                    'alias'=>'Outlet',
                    'type' => 'LEFT',
                    'conditions'=>array('Outlet.id= Memo.outlet_id')
                ),
                array(
                    'table'=>'territories',
                    'alias'=>'Territory',
                    'type' => 'LEFT',
                    'conditions'=>array('Territory.id= Memo.territory_id')
                ),
                array(
                    'table'=>'thanas',
                    'alias'=>'Thana',
                    'type' => 'LEFT',
                    'conditions'=>array('Thana.id= Memo.thana_id')
                ),
                array(
                    'table'=>'offices',
                    'alias'=>'Office',
                    'type' => 'LEFT',
                    'conditions'=>array('Office.id= Memo.office_id')
                ),
            ),
            'order'=>array('MemoEditablePermission.id DESC')
        ));
        $data_array =array();
        if(!empty($memos)){
            foreach ($memos as $key => $value) {
               $data = array();
               $data['id'] = $value['MemoEditablePermission']['id'];
               $data['memo_id'] = $value['Memo']['id'];
               $data['memo_no'] = $value['Memo']['memo_no'];
               $data['market_name'] = $value['Market']['name'];
               $data['outlet_name'] = $value['Outlet']['name'];
               $data['thana_name'] = $value['Thana']['name'];
               $data['territory_name'] = $value['Territory']['name'];
               $data['remarks'] = $value['MemoEditablePermission']['remarks'];
               $data['office_name'] = $value['Office']['office_name'];

               $data_array[] = $data;
            }
        }
        
        echo json_encode($data_array);
        $this->autoRender = false;
    }
    public function memo_recommended(){
        $id = $this->request->data['id'];
        $recommender_note = $this->request->data['recommender_note'];
        $memo_info = $this->MemoEditablePermission->find('first',array(
            'conditions'=>array('MemoEditablePermission.id'=>$id)
        ));
        $memo_no = $memo_info['Memo']['memo_no'];

        $this->loadModel('SystemNotification');

        $data_array = array();
        $data_array['id'] = $id;
        $data_array['recommender_note'] = $recommender_note;
        $data_array['recommended_at'] = $this->current_datetime();
        $data_array['recommended_by'] = $this->UserAuth->getUserId();
        $data_array['status'] = 1;

        if($this->MemoEditablePermission->save($data_array)){
            $msg[0] = '1';

            $sp_name = $this->Session->read('UserAuth.SalesPerson.name');

            $notification = array();
            $notification['notification'] = "<a href='".BASE_URL."admin/MemoEditablePermissions/approval_list'>This ".$memo_no." is recommended</a>";
            $notification['controller'] = 'MemoEditablePermissions';
            $notification['methods'] = 'admin_approval_list';
            $notification['status'] = 0;
            $notification['notification_seen'] = 0;
            $notification['office_id'] = $this->UserAuth->getOfficeId();
            $notification['user_id'] = $this->UserAuth->getUserId();
            $notification['created_at'] = $this->current_datetime();
            $notification['created_by'] = $this->UserAuth->getUserId();
            $notification['updated_at'] = $this->current_datetime();
            $notification['updated_by'] = $this->UserAuth->getUserId();
           
            $this->SystemNotification->create();
            $this->SystemNotification->save($notification);
        }
        else{
            $msg[0] = '0';
        }

        echo json_encode($msg);
        $this->autoRender = false;
    }


    public function get_memo_approved_list(){
        $office_id = $this->request->data['office_id'];
        $memo_no = $this->request->data['memo_no'];

        ini_set('max_execution_time', 1000);   
        $memo_conditions = array();

        if(!empty($office_id)){
            $memo_conditions[] = array('Memo.office_id' => $office_id);
        }
        if(!empty($memo_no)){
            $memo_conditions[] = array('Memo.memo_no like' => "%".$memo_no."%");
        }
        $memo_conditions[] = array('MemoEditablePermission.status'=> 1);

        $this->loadModel('MemoEditablePermission');
        $memos = $this->MemoEditablePermission->find('all',array(
            'fields'=>array('Memo.id','Memo.memo_no','Market.name','Outlet.name','Territory.name','Thana.name','MemoEditablePermission.id','MemoEditablePermission.remarks','MemoEditablePermission.recommender_note','Office.office_name'), 
            'conditions'=> $memo_conditions,
            'joins'=>array(
                array(
                    'table'=>'markets',
                    'alias'=>'Market',
                    'type' => 'LEFT',
                    'conditions'=>array('Market.id= Memo.market_id')
                ),
                array(
                    'table'=>'outlets',
                    'alias'=>'Outlet',
                    'type' => 'LEFT',
                    'conditions'=>array('Outlet.id= Memo.outlet_id')
                ),
                array(
                    'table'=>'territories',
                    'alias'=>'Territory',
                    'type' => 'LEFT',
                    'conditions'=>array('Territory.id= Memo.territory_id')
                ),
                array(
                    'table'=>'thanas',
                    'alias'=>'Thana',
                    'type' => 'LEFT',
                    'conditions'=>array('Thana.id= Memo.thana_id')
                ),
                array(
                    'table'=>'offices',
                    'alias'=>'Office',
                    'type' => 'LEFT',
                    'conditions'=>array('Office.id= Memo.office_id')
                ),
            ),
            'order'=>array('MemoEditablePermission.id DESC')
        ));
        $data_array =array();
        if(!empty($memos)){
            foreach ($memos as $key => $value) {
               $data = array();
               $data['id'] = $value['MemoEditablePermission']['id'];
               $data['memo_id'] = $value['Memo']['id'];
               $data['memo_no'] = $value['Memo']['memo_no'];
               $data['market_name'] = $value['Market']['name'];
               $data['outlet_name'] = $value['Outlet']['name'];
               $data['thana_name'] = $value['Thana']['name'];
               $data['territory_name'] = $value['Territory']['name'];
               $data['remarks'] = $value['MemoEditablePermission']['remarks'];
               $data['recommender_note'] = $value['MemoEditablePermission']['recommender_note'];
               $data['office_name'] = $value['Office']['office_name'];

               $data_array[] = $data;
            }
        }
        
        echo json_encode($data_array);
        $this->autoRender = false;
    }
    public function memo_approved(){
        $id = $this->request->data['id'];
        $approval_note = $this->request->data['approval_note'];

        $this->loadModel('Memo');
        $this->loadModel('MemoEditablePermission');
        $this->loadModel('SystemNotification');
        $memo_editable_info = $this->MemoEditablePermission->find('first',array(
            'conditions'=>array(
                'MemoEditablePermission.id'=>$id
            ),
            //'recursive'=> -1
        ));
        $memo_id = $memo_editable_info['MemoEditablePermission']['memo_id'];
        $memo_no = $memo_editable_info['Memo']['memo_no'];
        $data_array = array();
        $data_array['id'] = $id;
        $data_array['approval_note'] = $approval_note;
        $data_array['approval_at'] = $this->current_datetime();
        $data_array['approval_by'] = $this->UserAuth->getUserId();
        $data_array['status'] = 2;

        if($this->MemoEditablePermission->save($data_array)){
            $msg[0] = '1';
            //Update Memo
            $memo_arr['id'] = $memo_id;
            $memo_arr['memo_editable'] = 1;
            $this->Memo->save($memo_arr);
            //end Update
            $sp_name = $this->Session->read('UserAuth.SalesPerson.name');
            //notifications
            $notification = array();
            $notification['notification'] = "<a href='".BASE_URL."admin/MemoEditablePermissions'>This ".$memo_no." is Approved</a>";
            $notification['status'] = 0;
            $notification['controller'] = 'MemoEditablePermissions';
            $notification['methods'] = 'admin_create_requisition';
            $notification['notification_seen'] = 0;
            $notification['office_id'] = $this->UserAuth->getOfficeId();
            $notification['user_id'] = $this->UserAuth->getUserId();
            $notification['created_at'] = $this->current_datetime();
            $notification['created_by'] = $this->UserAuth->getUserId();
            $notification['updated_at'] = $this->current_datetime();
            $notification['updated_by'] = $this->UserAuth->getUserId();
           
            $this->SystemNotification->create();
            $this->SystemNotification->save($notification);
            //end notifications
        }
        else{
            $msg[0] = '0';
        }

        echo json_encode($msg);
        $this->autoRender = false;
    }
    public function memo_not_approved(){
        $id = $this->request->data['id'];
        $approval_note = $this->request->data['approval_note'];

        $this->loadModel('Memo');
        $this->loadModel('MemoEditablePermission');
        $this->loadModel('SystemNotification');

        $memo_editable_info = $this->MemoEditablePermission->find('first',array(
            'conditions'=>array(
                'MemoEditablePermission.id'=>$id
            ),
            //'recursive'=> -1
        ));
        $memo_id = $memo_editable_info['MemoEditablePermission']['memo_id'];
        $memo_no = $memo_editable_info['Memo']['memo_no'];
        $data_array = array();
        $data_array['id'] = $id;
        $data_array['approval_note'] = $approval_note;
        $data_array['approval_at'] = $this->current_datetime();
        $data_array['approval_by'] = $this->UserAuth->getUserId();
        $data_array['status'] = 3;

        if($this->MemoEditablePermission->save($data_array)){
            $msg[0] = '1';


            //end Update
            //$sp_name = $this->Session->read('UserAuth.SalesPerson.name');
            //notifications
            $notification = array();
            $notification['notification'] = "<a href='".BASE_URL."admin/MemoEditablePermissions'>This ".$memo_no." is Not Approved</a>";
            $notification['status'] = 0;
            $notification['notification_seen'] = 0;
            $notification['controller'] = 'MemoEditablePermissions';
            $notification['methods'] = 'admin_create_requisition';
            $notification['office_id'] = $this->UserAuth->getOfficeId();
            $notification['user_id'] = $this->UserAuth->getUserId();
            $notification['created_at'] = $this->current_datetime();
            $notification['created_by'] = $this->UserAuth->getUserId();
            $notification['updated_at'] = $this->current_datetime();
            $notification['updated_by'] = $this->UserAuth->getUserId();
           
            $this->SystemNotification->create();
            $this->SystemNotification->save($notification);
            //end notifications
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



