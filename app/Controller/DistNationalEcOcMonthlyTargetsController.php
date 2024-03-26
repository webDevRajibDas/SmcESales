<?php

App::uses('AppController', 'Controller');

/**
 * Sale Targets Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistNationalEcOcMonthlyTargetsController extends AppController {

    public $uses = array('DistSaleTargetMonth', 'Office', 'FiscalYear');



    public function admin_index() 
	{
        if ($this->request->is('post')) 
		{
            if ($this->request->data['DistSaleTargetMonth']['id']) 
			{				
                $receivedData['id'] = $this->request->data['DistSaleTargetMonth']['id'];
                $receivedData['fiscal_year_id'] = $this->request->data['DistSaleTargetMonth']['fiscal_year_id'];
				$receivedData['month_id'] = $this->request->data['DistSaleTargetMonth']['month_id'];
				
				$receivedData['target_quantity'] = 0;
				$receivedData['target_amount'] = 0;
                
				$receivedData['outlet_coverage_pharma'] = $this->request->data['DistSaleTargetMonth']['outlet_coverage_pharma'];
                $receivedData['outlet_coverage_non_pharma'] = $this->request->data['DistSaleTargetMonth']['outlet_coverage_non_pharma'];
                $receivedData['effective_call_non_pharma'] = $this->request->data['DistSaleTargetMonth']['effective_call_non_pharma'];
                $receivedData['effective_call_pharma'] = $this->request->data['DistSaleTargetMonth']['effective_call_pharma'];
                
				$receivedData['created_at'] = $this->current_datetime();
				$receivedData['created_by'] = $this->UserAuth->getUserId();
				$receivedData['updated_at'] = $this->current_datetime();
				$receivedData['updated_by'] = $this->UserAuth->getUserId();
				
				$receivedData['target_type'] = 3;
				
                if ($this->DistSaleTargetMonth->save($receivedData)) {
                    $this->Session->setFlash(__('Data has been updated'), 'flash/success');
                    //$this->redirect(array('controller' => 'DistNationalEcOcMonthlyTargets', 'action' => 'index'));
                }
            } 
			else 
			{
                $receivedData['fiscal_year_id'] = $this->request->data['DistSaleTargetMonth']['fiscal_year_id'];
				$receivedData['month_id'] = $this->request->data['DistSaleTargetMonth']['month_id'];
                
				$receivedData['target_quantity'] = 0;
				$receivedData['target_amount'] = 0;
				
				$receivedData['outlet_coverage_pharma'] = $this->request->data['DistSaleTargetMonth']['outlet_coverage_pharma'];
                $receivedData['outlet_coverage_non_pharma'] = $this->request->data['DistSaleTargetMonth']['outlet_coverage_non_pharma'];
                $receivedData['effective_call_non_pharma'] = $this->request->data['DistSaleTargetMonth']['effective_call_non_pharma'];
                $receivedData['effective_call_pharma'] = $this->request->data['DistSaleTargetMonth']['effective_call_pharma'];

				$receivedData['created_at'] = $this->current_datetime();
				$receivedData['created_by'] = $this->UserAuth->getUserId();
				$receivedData['updated_at'] = $this->current_datetime();
				$receivedData['updated_by'] = $this->UserAuth->getUserId();
				
                $receivedData['target_type'] = 3;
					
				//pr($receivedData);exit;
				
                if ($this->DistSaleTargetMonth->save($receivedData)) {
                    $this->Session->setFlash(__('Data has been saved'), 'flash/success');
                    //$this->redirect(array('controller' => 'DistNationalEcOcMonthlyTargets', 'action' => 'index'));
                }
            }
        }
		
		//get current month id
		$current_month = date("m");
        $this->loadModel('Month');
		$this->Month->recursive = -1;
        $current_month_info = $this->Month->find('first', array(
            'fields' => array('id'),
            'conditions' => array('month' => $current_month)
        ));
		
		$months = $this->Month->find('list', array('order' => array('Month.month' => 'asc')));	
			
		$current_month_id = '';
        if (isset($this->request->data['DistSaleTargetMonth']['month_id'])) {
            $current_month_id = $this->request->data['DistSaleTargetMonth']['month_id'];
        }
		$this->set(compact('months','current_month_id'));
		//echo $current_month_id;
		//exit;
		
        $this->set('page_title', 'Distributor National target EC, OC (Monthly)');
        $fiscalYears = $this->DistSaleTargetMonth->FiscalYear->find('list', array('fields' => array('year_code')));
        $this->set('fiscalYears', $fiscalYears);
    }

    public function admin_get_national_target_ec_oc() {
		$this->DistSaleTargetMonth->recursive = -1;
        $retrievedData = $this->DistSaleTargetMonth->find('all', array(
            'fields' => array('id', 'outlet_coverage_pharma', 'outlet_coverage_non_pharma', 'effective_call_pharma','effective_call_non_pharma'),
            'conditions' => array(
			'target_type' => 3, 
			'fiscal_year_id' => $this->request->data['fiscalYearId'],
			'month_id' => $this->request->data['month_id']
			)
           )
        );
        echo json_encode($retrievedData);
        $this->autoRender = false;
    }

}
