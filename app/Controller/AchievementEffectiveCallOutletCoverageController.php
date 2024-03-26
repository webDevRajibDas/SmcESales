<?php
App::uses('AppController', 'Controller');
/**
 * Sale Targets Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class AchievementEffectiveCallOutletCoverageController extends AppController {
	//var $uses =false;
	
	public $uses = array('SaleTargetMonth','FiscalYear','months');
/**
 * Components
 *
 * @var array
 */

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index()
	{
		$this->set('page_title','Effective Call Outlet coverage set');

		if ($this->request->is('post')) {
			$territory_id=$this->request->data['ec_oc_achievement']['territory_id'];
			$fiscal_year_id=$this->request->data['ec_oc_achievement']['fiscal_year_id'];
			$month_id=$this->request->data['ec_oc_achievement']['month_id'];
		
			$data=array('SaleTargetMonth.effective_call_pharma_achievement'=>$this->request->data['ec_oc_achievement']['effective_call_pharma'],
			'SaleTargetMonth.effective_call_non_pharma_achievement'=>$this->request->data['ec_oc_achievement']['effective_call_non_pharma'],
			'SaleTargetMonth.outlet_coverage_pharma_achievement'=>$this->request->data['ec_oc_achievement']['outlet_coverage_pharma'],
			'SaleTargetMonth.outlet_coverage_non_pharma_achievement'=>$this->request->data['ec_oc_achievement']['outlet_coverage_non_pharma']);
			//pr($data);exit;
			if($this->SaleTargetMonth->updateAll($data,array('SaleTargetMonth.territory_id'=>$territory_id,'SaleTargetMonth.fiscal_year_id'=>$fiscal_year_id,'SaleTargetMonth.month_id'=>$month_id,'product_id'=>0))){
			$this->loadModel('AchievementBallenceLog');
			$data=array();
			$data['AchievementBallenceLog']['created_at'] = $this->current_datetime(); 
			$data['AchievementBallenceLog']['created_by'] = $this->UserAuth->getUserId();
			$data['AchievementBallenceLog']['territory_id']=$this->request->data['ec_oc_achievement']['territory_id'];

			$data['AchievementBallenceLog']['fiscal_year_id']=$this->request->data['ec_oc_achievement']['fiscal_year_id'];
			$data['AchievementBallenceLog']['month_id']=$this->request->data['ec_oc_achievement']['month_id'];
			$data['AchievementBallenceLog']['effective_call_pharma_achievement']=$this->request->data['ec_oc_achievement']['effective_call_pharma'];
			$data['AchievementBallenceLog']['effective_call_non_pharma_achievement']=$this->request->data['ec_oc_achievement']['effective_call_non_pharma'];
			$data['AchievementBallenceLog']['outlet_coverage_pharma_achievement']=$this->request->data['ec_oc_achievement']['outlet_coverage_pharma'];
			$data['AchievementBallenceLog']['outlet_coverage_non_pharma_achievement']=$this->request->data['ec_oc_achievement']['outlet_coverage_non_pharma'];
			$this->AchievementBallenceLog->create();
			if($this->AchievementBallenceLog->save($data)){
				$this->Session->setFlash(__('The Achievement  saved successfully.'), 'flash/success');
			}
			
			}
		}
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if($office_parent_id == 0){
			$office_conditions = array();			
		}else{
			$office_conditions = array('id' => $this->UserAuth->getOfficeId());
		}
		
		$this->loadModel('Office');
		$this->loadModel('Territory');
		$this->loadModel('Month');
		$offices = $this->Office->find('list',array('conditions'=> $office_conditions,'order'=>array('office_name'=>'asc')));
		$office_id = (isset($this->request->data['ec_oc_achievement']['office_id']) ? $this->request->data['ec_oc_achievement']['office_id'] : 0);
		$territories = $this->Territory->find('list',array('conditions'=> array('office_id' => $office_id), 'order'=>array('name'=>'asc')));
				
		$territory_id = (isset($this->request->data['ec_oc_achievement']['territory_id']) ? $this->request->data['ec_oc_achievement']['territory_id'] : 0);
		$fiscal_years=$this->FiscalYear->find('list',array('fields'=>array('year_code')));
		$fiscal_year_id = (isset($this->request->data['ec_oc_achievement']['fiscal_year_id']) ? $this->request->data['ec_oc_achievement']['fiscal_year_id'] : 0);
		$months=$this->Month->find('list',array('conditions'=> array('Month' => $fiscal_year_id)));
		$month_id=(isset($this->request->data['ec_oc_achievement']['month_id']) ? $this->request->data['ec_oc_achievement']['month_id'] : 0);
		$this->set(compact('offices', 'territories','fiscal_years','months'));
	}	
	public function get_month_by_fiscal_year_id(){
		$this->loadModel('Month');
		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		$month_id = $this->request->data['month_id'];
		if($month_id==''){
			$rs = array(array('id' => '', 'name' => '---- Select -----'));
		}
		else{
        $months = $this->Month->find('all', array(
        	'fields' => array('Month.id', 'Month.name'),
			'conditions' => array('Month.fiscal_year_id' => $month_id),
			'recursive' => -1
		));
		//pr($months);
		$data_array = Set::extract($months, '{n}.Month');
		if(!empty($months)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} }
		$this->autoRender = false;
	}
}	