<?php
App::uses('AppController', 'Controller');
/**
 * Territories Controller
 *
 * @property DistOrderDeliverySchedule $DistOrderDeliverySchedule
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistOrderDeliverySchedulesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session','Filter.Filter');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','Delivery Schedules List');
		$this->loadModel('Office');
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		
		if($office_parent_id == 0)
		{					
			$conditions = array();
			$d_conditions = array();
			//$OfficeConditions = array();
		}else{				
			$conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			$d_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			//$OfficeConditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}

		$this->LoadModel('DistSalesRepresentative');
        $distributor_id = isset($this->request->data['DistOrderDeliverySchedule']['distributor_id']) != '' ? $this->request->data['DistOrderDeliverySchedule']['distributor_id'] : 0;
		$srs = array();
		if($distributor_id){
			$srs = $this->DistSalesRepresentative->find('list', array(
                'conditions' => array('DistSalesRepresentative.dist_distributor_id' => $distributor_id,'DistSalesRepresentative.is_active' => 1),'order' => array('DistSalesRepresentative.name' => 'asc')
            ));
		}
		
		
		$this->LoadModel('DistDistributor');
		$distDistributors = array();
		$f_office_id = isset($this->request->data['DistOrderDeliverySchedule']['office_id']) != '' ? $this->request->data['DistOrderDeliverySchedule']['office_id'] : 0;
		if($f_office_id){
			$distDistributors = $this->DistDistributor->find('list', array(
				'fields' => array('DistDistributor.id', 'DistDistributor.name'),
				'conditions' => array('DistDistributor.office_id' => $f_office_id,'DistDistributor.is_active'=> 1),
				'order' => array('DistDistributor.name' => 'asc'),
				'recursive' => 0
			));
		}
		
		$status_list = array(
			'0'=>'Fail',
			'1'=>'Success',
			'2'=>'Fail',
		);

		$this->paginate = array(			
			'conditions' => $d_conditions,
			'recursive'=>0,
			'order' => array('DistOrderDeliverySchedule.id' => 'DESC')
		);
		
		$this->set('results', $this->paginate());

		$offices = $this->Office->find('list',array('conditions'=> $conditions,'order'=>array('office_name'=>'asc')));

		$this->set(compact('offices', 'srs', 'distDistributors', 'status_list'));
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Schedules Details');
		if (!$this->DistOrderDeliverySchedule->exists($id)) {
			throw new NotFoundException(__('Invalid Schedules'));
		}
		$options = array('conditions' => array('DistOrderDeliverySchedule.' . $this->DistOrderDeliverySchedule->primaryKey => $id));
		$this->set('territory', $this->DistOrderDeliverySchedule->find('first', $options));
	}
	
	public function admin_cancel($id = null) {}
	
/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Schedule');
		
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {}
	
	
	
	public function get_dist_distributor_sr_list() {
		$this->LoadModel('DistSalesRepresentative');
        $rs = array(array('id' => '', 'name' => '---- All -----'));
        $distributor_id = $this->request->data['distributor_id'];
		
		$sr = $this->DistSalesRepresentative->find('list', array(
                'conditions' => array('DistSalesRepresentative.dist_distributor_id' => $distributor_id,'DistSalesRepresentative.is_active' => 1),'order' => array('DistSalesRepresentative.name' => 'asc')
            ));

		//pr($sr);
		
        if (!empty($sr)) {
            echo json_encode(array_merge($rs, $sr));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }
	
	
	/***************** end Changes 30-10-2019***********************************/
	
}
