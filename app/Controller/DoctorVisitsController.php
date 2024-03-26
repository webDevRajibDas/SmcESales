<?php
App::uses('AppController', 'Controller');
/**
 * DoctorVisits Controller
 *
 * @property Doctor $Doctor
 * @property PaginatorComponent $Paginator
 */
class DoctorVisitsController extends AppController {

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
		$this->set('page_title','Doctor Visit List');
		$this->loadModel('District');
		$this->DoctorVisit->recursive = 0;
		if($this->UserAuth->getOfficeParentId() !=0 ){
			$conditions = array('Territory.office_id' => $this->UserAuth->getOfficeId());
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}else{
			$conditions = array();
			$office_conditions = array();
		}
		$this->DoctorVisit->virtualFields = array(
			'thana_name' => 'Thana.name',
			'outlet_name'=> 'Outlet.name'
			);
		$this->paginate = array(
			'conditions' => $conditions,
			'joins'=>array(
				array(
					'table'=>'thanas',
					'alias'=>'Thana',
					'type'=>'Inner',
					'conditions'=>'Thana.id=Market.thana_id'
				),
				array(
					'table'=>'outlets',
					'alias'=>'Outlet',
					'type'=>'left',
					'conditions'=>'Outlet.id=DoctorVisit.outlet_id'
				)
				),
			'fields'=>array('DoctorVisit.*','Doctor.name','Territory.name','Market.name','Thana.name','Outlet.name'),
			 'order' => array('Doctor.id' => 'DESC')
			 );
		$this->set('visits', $this->paginate());		
		
		$this->loadModel('Office');
		$offices = $this->Office->find('list',array(
			'conditions' => $office_conditions,
			'order' => array('Office.office_name'=>'asc')
		));
		$doctorQualifications = $this->DoctorVisit->Doctor->DoctorQualification->find('list');
		$doctorTypes = $this->DoctorVisit->Doctor->DoctorType->find('list');
		$office_id = isset($this->request->data['DoctorVisit']['office_id'])!='' ? $this->request->data['DoctorVisit']['office_id'] : 0;
		$territory_id = isset($this->request->data['DoctorVisit']['territory_id'])!='' ? $this->request->data['DoctorVisit']['territory_id'] : 0;
		$territories = $this->DoctorVisit->Doctor->Territory->find('list',array(
						'conditions' => array('Territory.office_id'=>$office_id),
						'order' => array('Territory.name'=>'asc')
					));
		$markets = $this->DoctorVisit->Doctor->Market->find('list',array(
						'conditions' => array('Market.territory_id'=> $territory_id),
						'order' => array('Market.name'=>'asc')
					));
		
		$this->set(compact('territories','markets','doctorQualifications','doctorTypes','offices'));
		
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Visit Details');
		if (!$this->DoctorVisit->exists($id)) {
			throw new NotFoundException(__('Invalid doctor'));
		}
		$options = array('conditions' => array('DoctorVisit.' . $this->DoctorVisit->primaryKey => $id),'recursive'=>0);
		$this->set('visits', $this->DoctorVisit->find('first', $options));
		
		$visitdetails = $this->DoctorVisit->DoctorVisitDetail->find('all',array(
						'conditions' => array('DoctorVisitDetail.doctor_visit_id'=> $id),
						'recursive' => 0
					));
		$this->set('visitdetails',$visitdetails);			
	}
	
}
