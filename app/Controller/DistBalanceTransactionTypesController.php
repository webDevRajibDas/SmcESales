<?php

App::uses('AppController', 'Controller');

/**
 * Territories Controller
 *
 * @property Territory $Territory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistBalanceTransactionTypesController extends AppController {

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
        $this->set('page_title', 'Balance Transaction Type List');
        $conditions = array();

        $this->paginate = array(
            'fields'=> array('DistBalanceTransactionType.*'),
            'conditions' => $conditions,
            'recursive' => 0,
        );
        $this->set('dist_balance_transaction_type', $this->paginate());
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
        if (!$this->DistBalanceTransactionType->exists($id)) {
            throw new NotFoundException(__('Invalid DistAreaExecutive'));
        }
        $this->loadModel('DistBalanceTransactionType');
        $options = array(
            'conditions' => array('DistBalanceTransactionType.id' => $id),
    );
        $this->set('distributor_balance', $this->DistBalanceTransactionType->find('first', $options));
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add() {
        $this->set('page_title', 'Add Balance Transaction Type'); 
        
        $inout_options = array( 0 => 'Credit', 1 => 'Debit');
        $this->set(compact('inout_options'));

        if ($this->request->is('post')) {
           
            $data['name'] = $this->request->data['DistBalanceTransactionType']['name'];
            $data['inout'] = $this->request->data['DistBalanceTransactionType']['inout'];
            $data['status'] = 1;
            $data['created_at'] = $this->current_datetime();;
            $data['created_by'] = $this->UserAuth->getUserId();
            $data['updated_at'] = $this->current_datetime();;
            $data['updated_by'] = $this->UserAuth->getUserId();
            
            $this->DistBalanceTransactionType->create();
            if($this->DistBalanceTransactionType->save($data))
            {
                $this->Session->setFlash(__('Limit added successfully!'), 'flash/success');
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
        $this->set('page_title', 'Edit Balance Transaction Type');
        $this->DistBalanceTransactionType->id = $id;
        if (!$this->DistBalanceTransactionType->exists($id)) {
            throw new NotFoundException(__('Invalid Balance Transaction Type'));
        }

        if ($this->request->is('post')) {
            $data['id'] = $this->request->data['DistBalanceTransactionType']['id'];
            $data['name'] = $this->request->data['DistBalanceTransactionType']['name'];
            $data['inout'] = $this->request->data['DistBalanceTransactionType']['inout'];
            $data['status'] = 1;
            $data['created_at'] = $this->current_datetime();;
            $data['created_by'] = $this->UserAuth->getUserId();
            $data['updated_at'] = $this->current_datetime();;
            $data['updated_by'] = $this->UserAuth->getUserId();

            if($this->DistBalanceTransactionType->save($data))
            {
                $this->Session->setFlash(__('Transaction Type Updated successfully!'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            }   
            
        }
        $options = array('conditions' => array('DistBalanceTransactionType.' . $this->DistBalanceTransactionType->primaryKey => $id));
        $transactions = $this->DistBalanceTransactionType->find('first', $options);
        $inout_options = array( 0 => 'Credit', 1 => 'Debit');
        $this->set(compact('inout_options'));
        $this->set(compact('transactions'));
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
        $this->check_data_by_company('DistBalanceTransactionType',$id);
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

    public function admin_get_distribute(){
        $office_id = $this->request->data['office_id'];
        $rs = array(array('id' => '', 'name' => '---- All -----'));

        $this->loadModel('DistDistributor');
        $distributor_list = $this->DistDistributor->find('all',array('conditions'=>array(
            'DistDistributor.office_id'=>$office_id
            ),
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

}
