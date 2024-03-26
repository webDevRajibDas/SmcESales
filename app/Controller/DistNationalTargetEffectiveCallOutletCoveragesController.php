<?php

App::uses('AppController', 'Controller');

/**
 * Sale Targets Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistNationalTargetEffectiveCallOutletCoveragesController extends AppController {

    public $uses = array('DistSaleTarget', 'Office', 'FiscalYear');

    public function admin_index() {
        $this->set('page_title', 'Distributor National target(Effective Call, Outlet Coverage)');
        if ($this->request->is('post')) {
            if ($this->request->data['DistSaleTarget']['id'] != '') {
                $receivedData['id'] = $this->request->data['DistSaleTarget']['id'];
                $receivedData['fiscal_year_id'] = $this->request->data['DistSaleTarget']['fiscal_year_id'];
                $receivedData['outlet_coverage_pharma'] = $this->request->data['DistSaleTarget']['outlet_coverage_pharma'];
                $receivedData['outlet_coverage_non_pharma'] = $this->request->data['DistSaleTarget']['outlet_coverage_non_pharma'];
                $receivedData['effective_call_non_pharma'] = $this->request->data['DistSaleTarget']['effective_call_non_pharma'];
                $receivedData['effective_call_pharma'] = $this->request->data['DistSaleTarget']['effective_call_pharma'];
                $receivedData['updated'] = $this->current_datetime();

                if ($this->DistSaleTarget->save($receivedData)) {
                    $this->Session->setFlash(__('Data has been updated'), 'flash/success');
                    $this->redirect(array('controller' => 'DistNationalTargetEffectiveCallOutletCoverages', 'action' => 'index'));
                }
            } else {
                $receivedData['fiscal_year_id'] = $this->request->data['DistSaleTarget']['fiscal_year_id'];
                $receivedData['outlet_coverage_pharma'] = $this->request->data['DistSaleTarget']['outlet_coverage_pharma'];
                $receivedData['outlet_coverage_non_pharma'] = $this->request->data['DistSaleTarget']['outlet_coverage_non_pharma'];
                $receivedData['effective_call_non_pharma'] = $this->request->data['DistSaleTarget']['effective_call_non_pharma'];
                $receivedData['effective_call_pharma'] = $this->request->data['DistSaleTarget']['effective_call_pharma'];
                $receivedData['created'] = $this->current_datetime();
                $receivedData['updated'] = $this->current_datetime();
                $receivedData['target_type_id'] = 1;
                $receivedData['target_category'] = 1;

                if ($this->DistSaleTarget->save($receivedData)) {
                    $this->Session->setFlash(__('Data has been saved'), 'flash/success');
                    $this->redirect(array('controller' => 'DistNationalTargetEffectiveCallOutletCoverages', 'action' => 'index'));
                }
            }
        }
        $fiscalYears = $this->FiscalYear->find('list', array('fields' => array('year_code')));
        $this->set('fiscalYears', $fiscalYears);
    }

    public function admin_get_national_target_effective_call_outlet_coverage() {
        $retrievedData = $this->DistSaleTarget->find('all', array(
            'fields' => ['id', 'outlet_coverage_pharma', 'outlet_coverage_non_pharma', 'effective_call_pharma', 'effective_call_non_pharma'],
            'conditions' => ['target_category' => 1, 'target_type_id' => 1, 'fiscal_year_id' => $this->request->data['fiscal_year_id']]
                )
        );
        echo json_encode($retrievedData);
        $this->autoRender = false;
    }

}
