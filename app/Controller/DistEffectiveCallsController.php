<?php

App::uses('AppController', 'Controller');
App::import('Vendor', 'php-excel-reader/excel_reader2');

/**
 * Sale Targets Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistEffectiveCallsController extends AppController {

    //var $uses =false;

    public $uses = array('DistSaleTarget', 'Product', 'Office', 'FiscalYear');

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
    public function admin_index() {
        $this->set('page_title', 'Distributor Effective Call List');
        // die();
        if ($this->request->is('post')) {
            $fiscal_year_id = $this->request->data['DistEffectiveCall']['fiscal_year_id'];
            $saletarget_list = $this->DistSaleTarget->find('all', array(
                'fields' => array('DistSaleTarget.id'),
                'conditions' => array(
                    'DistSaleTarget.fiscal_year_id' => $fiscal_year_id,
                    'DistSaleTarget.target_type_id' => 1,
                    'DistSaleTarget.target_category' => 2,
                ),
                'recursive' => -1
                    )
            );

            if (count($saletarget_list) > 0) {
                if (!empty($this->request->data['DistDistributor'])) {
                    $data_array = array();
                    foreach ($this->request->data['DistDistributor']['DistSaleTarget']['effective_call_pharma'] as $key => $val) {
                        $data['DistSaleTarget']['id'] = $this->request->data['DistSaleTarget']['id'][$key];
                        $data['DistSaleTarget']['aso_id'] = $this->request->data['aso_id'][$key];
                        $data['DistSaleTarget']['target_type_id'] = 1;
                        $data['DistSaleTarget']['target_category'] = 2;
                        $data['DistSaleTarget']['fiscal_year_id'] = $this->request->data['DistEffectiveCall']['fiscal_year_id'];
                        $data['DistSaleTarget']['outlet_coverage_pharma'] = !empty($this->request->data['DistDistributor']['DistSaleTarget']['outlet_coverage_pharma'][$key]) ? $this->request->data['DistDistributor']['DistSaleTarget']['outlet_coverage_pharma'][$key] : 0;
                        $data['DistSaleTarget']['outlet_coverage_non_pharma'] = !empty($this->request->data['DistDistributor']['DistSaleTarget']['outlet_coverage_non_pharma'][$key]) ? $this->request->data['DistDistributor']['DistSaleTarget']['outlet_coverage_non_pharma'][$key] : 0;
                        $data['DistSaleTarget']['effective_call_pharma'] = !empty($val) ? $val : 0;
                        $data['DistSaleTarget']['effective_call_non_pharma'] = !empty($this->request->data['DistDistributor']['DistSaleTarget']['effective_call_non_pharma'][$key]) ? $this->request->data['DistDistributor']['DistSaleTarget']['effective_call_non_pharma'][$key] : 0;
                        $data_array[] = $data;
                    }
                    $this->DistSaleTarget->saveAll($data_array);
                }
            } else {
                if (!empty($this->request->data['DistDistributor'])) {
                    $data = array();
                    $i = 0;
                    $distSaleTarget = $this->request->data['DistDistributor']['DistSaleTarget'];
                    foreach ($this->request->data['DistDistributor']['DistSaleTarget']['effective_call_pharma'] as $key => $val) {
                        $data[$i]['DistSaleTarget']['target_type_id'] = 1;
                        $data[$i]['DistSaleTarget']['target_category'] = 2;
                        $data[$i]['DistSaleTarget']['fiscal_year_id'] = $this->request->data['DistEffectiveCall']['fiscal_year_id'];
                        $data[$i]['DistSaleTarget']['aso_id'] = $this->request->data['aso_id'][$key];
                        //$data[$i]['DistSaleTarget']['dist_distributor_id'] = $key;
                        $data[$i]['DistSaleTarget']['outlet_coverage_pharma'] = !empty($distSaleTarget['outlet_coverage_pharma'][$key]) ? $distSaleTarget['outlet_coverage_pharma'][$key] : 0;
                        $data[$i]['DistSaleTarget']['outlet_coverage_non_pharma'] = !empty($distSaleTarget['outlet_coverage_non_pharma'][$key]) ? $distSaleTarget['outlet_coverage_non_pharma'][$key] : 0;
                        $data[$i]['DistSaleTarget']['effective_call_pharma'] = !empty($val) ? $val : 0;
                        $data[$i]['DistSaleTarget']['effective_call_non_pharma'] = !empty($distSaleTarget['effective_call_non_pharma'][$key]) ? $distSaleTarget['effective_call_non_pharma'][$key] : 0;
                        $data[$i]['DistSaleTarget']['created'] = $this->current_datetime();
                        $data[$i]['DistSaleTarget']['updated'] = $this->current_datetime();
                        $i++;
                    }
                    if ($this->DistSaleTarget->saveAll($data)) {
                        $this->Session->setFlash(__('The Effective Call has been saved'), 'flash/success');
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash(__('The Effective Call could not be saved. Please, try again.'), 'flash/error');
                    }
                }
            }
            $this->Session->setFlash(__('The Distributor Effective Call has been saved'), 'flash/success');
        }
        //exit;

        $current_year = date("Y");
        $this->FiscalYear->recursive = -1;
        $current_year_info = $this->FiscalYear->find('first', array(
            'fields' => array('id'),
            'conditions' => array('YEAR(FiscalYear.created_at)' => $current_year)
        ));
        $current_year_code = $current_year_info['FiscalYear']['id'];
        if (!empty($fiscal_year_id)) {
            $year_id = $fiscal_year_id;
        } else {
            $year_id = $current_year_code;
        }

        /* --------------Get Distributor data-------------------- */
        $this->loadModel('Office');
        $this->Office->recursive = -1;
        $office_list = $this->Office->find('all', array(
            'conditions' => array(
                'Office.office_type_id' => 2,
            )
                )
        );
        $this->DistSaleTarget->recursive = -1;
        $office_list_with_eff_call_list = $this->DistSaleTarget->find('all', array(
            'conditions' => array(
                'DistSaleTarget.fiscal_year_id' => $year_id,
                'DistSaleTarget.target_category' => 2,
                'DistSaleTarget.target_type_id' => 1
            )
                )
        );
        $fiscalYears = $this->FiscalYear->find('list', array('fields' => array('year_code')));
        $this->set(compact('office_list', 'fiscalYears', 'selected_saletarget_list', 'effective_calls_list', 'current_year_code'));
    }

    /**
     * get_national_target_area_wise_data method
     *
     * @return void
     */
    public function admin_get_effective_call_list() {
        //echo 'hdgshgs';
        $this->DistSaleTarget->recursive = -1;
        $saletarget_list = $this->DistSaleTarget->find('all', array('fields' => array('id', 'dist_distributor_id', 'aso_id', 'outlet_coverage_pharma', 'outlet_coverage_non_pharma', 'effective_call_pharma', 'effective_call_non_pharma'),
            'conditions' => array(
                'AND' => array(
                    array(
                        'DistSaleTarget.fiscal_year_id' => $this->request->data('fiscal_year_id'),
                        'DistSaleTarget.target_type_id' => 1,
                        'DistSaleTarget.target_category' => 2
                    )
        ))));
        // $test=array('a','b' );
        // echo '<pre>';
        //print_r($saletarget_list);
        //echo '</pre>';
        echo json_encode($saletarget_list);
        $this->autoRender = false;
    }

    public function admin_get_national_effective_call_data() {
        $this->DistSaleTarget->recursive = -1;
        $saletarget_list = $this->DistSaleTarget->find('all', array('fields' => array('id', 'outlet_coverage_pharma', 'outlet_coverage_non_pharma', 'effective_call_pharma', 'effective_call_non_pharma'),
            'conditions' => array(
                'AND' => array(
                    array(
                        'DistSaleTarget.fiscal_year_id' => $this->request->data('fiscal_year_id'),
                        'DistSaleTarget.target_type_id' => 1,
                        'DistSaleTarget.target_category' => 1
                    )
        ))));
        echo json_encode($saletarget_list);
        $this->autoRender = false;
    }

    public function admin_upload_xl() {
        $this->loadModel('FiscalYear');
        $this->loadModel('Office');
        if (!empty($_FILES["file"]["name"])) {
            $target_dir = WWW_ROOT . 'files/';
            ;
            $target_file = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 30);
            $uploadOk = 1;
            $imageFileType = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir . $target_file . '.' . $imageFileType)) {
                $data_ex = new Spreadsheet_Excel_Reader($target_dir . $target_file . '.' . $imageFileType, true);
                $temp = $data_ex->dumptoarray();
                $this->SaleTarget->recursive = -1;
                $insert_data_array = array();
                $update_data_array = array();
                foreach ($temp as $key => $val) {
                    if ($key > 1 && !empty($val[1])) {
                        $fiscal_year_id = $this->FiscalYear->find('first', array(
                            'fields' => array('FiscalYear.id'),
                            'conditions' => array('FiscalYear.year_code LIKE' => '%' . trim($val[1] . '%')),
                            'recursive' => -1
                        ));

                        $aso_id = $this->Office->find('first', array(
                            'fields' => 'Office.id',
                            'conditions' => array('lower(Office.office_name) like' => '%' . strtolower($val[2]) . '%'),
                            'recursive' => -1
                        ));
                        if ($fiscal_year_id && !$aso_id) {
                            $this->Session->setFlash(__('The fiscal year or Offie Name missing or incorrect on line ' . $key), 'flash/error');
                            $this->redirect(array("controller" => "EffectiveCalls", "action" => "admin_index"));
                        }
                        $saletargets = $this->SaleTarget->find('first', array('conditions' => array('SaleTarget.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'], 'SaleTarget.aso_id' => $aso_id['Office']['id'], 'SaleTarget.target_type_id' => 1, 'SaleTarget.target_category' => 2)));
                        if (empty($saletargets)) {
                            $insert_data['SaleTarget']['target_category'] = 2;
                            $insert_data['SaleTarget']['fiscal_year_id'] = $fiscal_year_id['FiscalYear']['id'];
                            $insert_data['SaleTarget']['aso_id'] = $aso_id['Office']['id'];
                            $insert_data['SaleTarget']['target_type_id'] = 1;
                            $insert_data['SaleTarget']['outlet_coverage_pharma'] = $val[6];
                            $insert_data['SaleTarget']['outlet_coverage_non_pharma'] = $val[7];
                            $insert_data['SaleTarget']['session'] = $val[5];
                            $insert_data['SaleTarget']['effective_call_pharma'] = $val[3];
                            $insert_data['SaleTarget']['effective_call_non_pharma'] = $val[4];
                            $insert_data_array[] = $insert_data;
                        } else {
                            $updated_data['SaleTarget']['id'] = $saletargets['SaleTarget']['id'];
                            $updated_data['SaleTarget']['outlet_coverage_pharma'] = $val[6];
                            $updated_data['SaleTarget']['outlet_coverage_non_pharma'] = $val[7];
                            $updated_data['SaleTarget']['session'] = $val[5];
                            $updated_data['SaleTarget']['effective_call_pharma'] = $val[3];
                            $updated_data['SaleTarget']['effective_call_non_pharma'] = $val[4];
                            $update_data_array[] = $updated_data;
                        }
                    }
                }
                if ($insert_data_array) {
                    $this->SaleTarget->create();
                    $this->SaleTarget->saveAll($insert_data_array);
                }
                if ($update_data_array) {
                    $this->SaleTarget->saveAll($update_data_array);
                }

                $this->Session->setFlash(__('The Sale Targets has been saved'), 'flash/success');
                $this->redirect(array("controller" => " EffectiveCalls",
                    "action" => "admin_index"));
            }
        }
    }

}
