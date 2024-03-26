<?php

App::uses('AppController', 'Controller');

/**
 * Territories Controller
 *
 * @property Territory $Territory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistDistributorWiseCommissionsController extends AppController {

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
        $this->set('page_title', 'Distributor Wise Commissions List');

        $this->loadModel('Office');
        $this->loadModel('DistDistributor');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' =>2);
            $distributor_conditions = array('DistDistributor.is_active'=> 1);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(),'Office.office_type_id' =>2);
            $distributor_conditions = array('office_id'=>$this->UserAuth->getOfficeId(),'DistDistributor.is_active'=> 1);
        }

        $this->paginate = array(
           // 'conditions' => $conditions,
            'recursive' => 0,
            'order' => array('DistDistributorWiseCommission.id' => 'DESC')
        );

        $this->set('dist_commissions', $this->paginate());

        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        $distributors = $this->DistDistributor->find('list',array('conditions'=>$distributor_conditions));
        $this->set(compact('offices','distributors'));
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
        if (!$this->DistDistributorWiseCommission->exists($id)) {
            throw new NotFoundException(__('Invalid Distributo Wise Commission'));
        }
        $options = array('conditions' => array('DistDistributorWiseCommission.' . $this->DistDistributorWiseCommission->primaryKey => $id));
        $this->set('tso', $this->DistDistributorWiseCommission->find('first', $options));
    }

    /**
     * admin_add method
     *
     * @return void
     */
   
   


    public function admin_add() {
        $this->set('page_title', 'New Commission Rate');
        if ($this->request->is('post')) {

            //pr($this->request->data);die();
            $effective_date = $this->request->data['DistDistributorWiseCommission']['effective_date'];
            $this->request->data['DistDistributorWiseCommission']['effective_date'] = date('Y-m-d', strtotime($effective_date));
            $this->request->data['DistDistributorWiseCommission']['created_at'] = $this->current_datetime();
            $this->request->data['DistDistributorWiseCommission']['created_by'] = $this->UserAuth->getUserId();
            $this->request->data['DistDistributorWiseCommission']['updated_at'] = $this->current_datetime();
            $this->DistDistributorWiseCommission->create();
            if ($this->DistDistributorWiseCommission->save($this->request->data)) {
                $this->Session->setFlash(__('The Distributo Wise Commission has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Distributo Wise Commission could not be saved. Please, try again.'), 'flash/error');
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
        $this->DistDistributorWiseCommission->id = $id;
        if (!$this->DistDistributorWiseCommission->exists($id)) {
            throw new NotFoundException(__('Invalid Route/Beat'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['DistDistributorWiseCommission']['updated_by'] = $this->UserAuth->getUserId();
            if ($this->DistDistributorWiseCommission->save($this->request->data)) {
                $this->Session->setFlash(__('The Distributo Wise Commission has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $options = array('conditions' => array('DistDistributorWiseCommission.' . $this->DistDistributorWiseCommission->primaryKey => $id));
            $this->request->data = $this->DistDistributorWiseCommission->find('first', $options);

            $this->loadmodel('Territory');
            $this->loadmodel('ThanaTerritory');
            $office_id = $this->request->data['DistDistributorWiseCommission']['office_id'];
            $territory = $this->Territory->find('all', array(
                    'fields' => array('Territory.id'),
                    'conditions' => array('Territory.office_id' => $office_id),
                    'order' => array('Territory.name' => 'asc'),
                    'recursive' => 0
            ));
            $thanas = array();
            $territories = array();
            
            foreach($territory as $key => $value)
            {
                $territories[$key] = $value['Territory']['id'];
               
            }
            
            //pr($data_array);die();
           $thana_list = $this->ThanaTerritory->find('all',array('conditions'=>array(
            'ThanaTerritory.territory_id' => $territories,
           )));

           foreach($thana_list as $key => $value)
            {
                $thanas[$value['Thana']['id']] = $value['Thana']['name'];
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
        $this->set(compact('offices','thanas'));
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
        
        $this->DistDistributorWiseCommission->id = $id;
        if (!$this->DistDistributorWiseCommission->exists()) {
            throw new NotFoundException(__('Invalid Distributo Wise Commission'));
        }
        
                /******************************* Check foreign key ****************************/
        
        if($this->DistDistributorWiseCommission->checkForeignKeys("dist_routes",$id))
        {
            $this->Session->setFlash(__('This Distributo Wise Commission has used in another'), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }
        
        
        if ($this->DistDistributorWiseCommission->delete()) {
            $this->Session->setFlash(__('Distributo Wise Commission deleted'), 'flash/success');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Distributo Wise Commission was not deleted'), 'flash/error');
        $this->redirect(array('action' => 'index'));
    }
    
    public function get_distributor_list_list(){
        
        $office_id = $this->request->data['office_id'];
        $rs = array(array('id' => '', 'name' => '---- Select -----'));
        $this->loadModel('DistDistributor');

        $distributor_info = $this->DistDistributor->find('all',array('conditions'=>array(
            'DistDistributor.office_id'=>$office_id,'DistDistributor.is_active'=> 1),
        'order' =>array('DistDistributor.name ASC'),
        ));

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
