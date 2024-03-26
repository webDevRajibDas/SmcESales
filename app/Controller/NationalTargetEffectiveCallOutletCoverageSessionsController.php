<?php

App::uses('AppController', 'Controller');

/**
 * Sale Targets Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class NationalTargetEffectiveCallOutletCoverageSessionsController extends AppController {

    public $uses = array('SaleTarget', 'Office', 'FiscalYear');



    public function admin_index() {
        if ($this->request->is('post')) {
           // date_default_timezone_set('Asia/Dhaka');
            //echo "<pre>";
             // print_r($this->request->data);
             // echo "</pre>";//die();
            if ($this->request->data['SaleTarget']['id'] != '') {
                $receivedData['id'] = $this->request->data['SaleTarget']['id'];
                $receivedData['fiscal_year_id'] = $this->request->data['SaleTarget']['fiscal_year_id'];
                $receivedData['outlet_coverage_pharma'] = $this->request->data['SaleTarget']['outlet_coverage_pharma'];
                $receivedData['outlet_coverage_non_pharma'] = $this->request->data['SaleTarget']['outlet_coverage_non_pharma'];
                $receivedData['session'] = $this->request->data['SaleTarget']['session'];
                $receivedData['effective_call_non_pharma'] = $this->request->data['SaleTarget']['effective_call_non_pharma'];
                $receivedData['effective_call_pharma'] = $this->request->data['SaleTarget']['effective_call_pharma'];
                $receivedData['updated'] = $this->current_datetime();

                if ($this->SaleTarget->save($receivedData)) {
                    $this->Session->setFlash(__('Data has been updated'), 'flash/success');
                    //$this->redirect(array('controller' => 'NationalTargetEffectiveCallOutletCoverageSessions', 'action' => 'index'));
                }
            } else {
                $receivedData['fiscal_year_id'] = $this->request->data['SaleTarget']['fiscal_year_id'];
                $receivedData['outlet_coverage_pharma'] = $this->request->data['SaleTarget']['outlet_coverage_pharma'];
                $receivedData['outlet_coverage_non_pharma'] = $this->request->data['SaleTarget']['outlet_coverage_non_pharma'];
                $receivedData['session'] = $this->request->data['SaleTarget']['session'];
                $receivedData['effective_call_non_pharma'] = $this->request->data['SaleTarget']['effective_call_non_pharma'];
                $receivedData['effective_call_pharma'] = $this->request->data['SaleTarget']['effective_call_pharma'];
                $receivedData['created'] = $this->current_datetime();
                $receivedData['updated'] = $this->current_datetime();
                $receivedData['target_type_id'] = 1;
                $receivedData['target_category'] = 1;

                if ($this->SaleTarget->save($receivedData)) {
                    $this->Session->setFlash(__('Data has been saved'), 'flash/success');
                    //$this->redirect(array('controller' => 'NationalTargetEffectiveCallOutletCoverageSessions', 'action' => 'index'));
                }
            }
        }
        $this->set('page_title', 'National target (Effective Call, Outlet Coverage and Session)');
        $fiscalYears = $this->SaleTarget->FiscalYear->find('list', array('fields' => array('year_code')));
        $this->set('fiscalYears', $fiscalYears);
    }

    public function admin_get_national_target_effective_call_outlet_coverage_session() {
        $retrievedData = $this->SaleTarget->find('all', array(
            'fields' => ['id', 'outlet_coverage_pharma', 'outlet_coverage_non_pharma', 'effective_call_pharma','effective_call_non_pharma', 'session'],
            'conditions' => ['target_category' => 1, 'target_type_id' => 1, 'fiscal_year_id' => $this->request->data['fiscalYearId']]
                )
        );
        echo json_encode($retrievedData);
        $this->autoRender = false;
    }

}
