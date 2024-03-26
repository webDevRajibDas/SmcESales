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
class EffectiveCallsController extends AppController {

    //var $uses =false;

    public $uses = array('SaleTarget', 'Product', 'Office', 'FiscalYear');

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
        $this->set('page_title', 'EffectiveCall Call List');
       // die();
        if ($this->request->is('post')) {            
            $fiscal_year_id = $this->request->data['EffectiveCall']['fiscal_year_id'];
            $saletarget_list = $this->SaleTarget->find('all', array(
                'fields' => array('SaleTarget.id'),
                'conditions' => array(
                    'SaleTarget.fiscal_year_id' => $fiscal_year_id,
                    'SaleTarget.target_type_id' => 1,
                    'SaleTarget.target_category' => 2,
                ),
                'recursive' => -1
                    )
            );
            if (empty($saletarget_list)) {
                if (!empty($this->request->data['Office'])) {
                    $this->SaleTarget->create();
                    $data_array = array();
                    foreach ($this->request->data['Office']['SaleTarget']['effective_call_pharma'] as $key => $val) {
                        $data['SaleTarget']['aso_id'] = $key;
                        $data['SaleTarget']['target_type_id'] = 1;
                        $data['SaleTarget']['target_category'] = 2;
                        $data['SaleTarget']['fiscal_year_id'] = $this->request->data['EffectiveCall']['fiscal_year_id'];
                        $data['SaleTarget']['outlet_coverage_pharma'] = $this->request->data['Office']['SaleTarget']['outlet_coverage_pharma'][$key];
                        $data['SaleTarget']['outlet_coverage_non_pharma'] = $this->request->data['Office']['SaleTarget']['outlet_coverage_non_pharma'][$key];
                        $data['SaleTarget']['session'] = $this->request->data['Office']['SaleTarget']['session'][$key];
                        $data['SaleTarget']['effective_call_pharma'] = $val;
                        $data['SaleTarget']['effective_call_non_pharma'] = $this->request->data['Office']['SaleTarget']['effective_call_non_pharma'][$key];
                        $data_array[] = $data;
                    }
                    $this->SaleTarget->saveAll($data_array);
                }
            } else {
                if (!empty($this->request->data['Office'])) {
                    $data_array = array();
                    foreach ($this->request->data['Office']['SaleTarget']['effective_call_pharma'] as $key => $val) {
                        $existing_data = $this->SaleTarget->find('all', array(
                            'fields' => array('SaleTarget.id'),
                            'conditions' => array(
                                'SaleTarget.fiscal_year_id' => $fiscal_year_id,
                                'SaleTarget.target_type_id' => 1,
                                'SaleTarget.target_category' => 2,
                                'SaleTarget.aso_id' => $key,
                            ),
                            'recursive' => -1
                                )
                        );
                        if (!empty($existing_data[0]['SaleTarget']['id'])) {
                            $this->SaleTarget->id = $existing_data[0]['SaleTarget']['id'];
                            $data['SaleTarget']['target_type_id'] = 1;
                            $data['SaleTarget']['target_category'] = 2;
                            $data['SaleTarget']['fiscal_year_id'] = $this->request->data['EffectiveCall']['fiscal_year_id'];
                            $data['SaleTarget']['outlet_coverage_pharma'] = $this->request->data['Office']['SaleTarget']['outlet_coverage_pharma'][$key];
                            $data['SaleTarget']['outlet_coverage_non_pharma'] = $this->request->data['Office']['SaleTarget']['outlet_coverage_non_pharma'][$key];
                            $data['SaleTarget']['session'] = $this->request->data['Office']['SaleTarget']['session'][$key];
                            $data['SaleTarget']['effective_call_pharma'] = $val;
                            $data['SaleTarget']['effective_call_non_pharma'] = $this->request->data['Office']['SaleTarget']['effective_call_non_pharma'][$key];
                            $this->SaleTarget->save($data);
                        }
                    }
                }
            }
            $this->Session->setFlash(__('The EffectiveCall has been saved'), 'flash/success');
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
        $this->Office->recursive = -1;
        $office_list = $this->Office->find('all', array(
            'conditions' => array(
                'Office.office_type_id' => 2,
            )
                )
        );
        $this->SaleTarget->recursive = -1;
        $office_list_with_eff_call_list = $this->SaleTarget->find('all', array(
            'conditions' => array(
                'SaleTarget.fiscal_year_id' => $year_id,
                'SaleTarget.target_category' => 2,
                'SaleTarget.target_type_id' => 1
            )
                )
        );
        /* -------start making office list with outlet_coverage and effective_call ------- */
        foreach ($office_list_with_eff_call_list as $key => $val) {
            $aso_id = $val['SaleTarget']['aso_id'];
            foreach ($office_list as $office_key => $office_val) {
                if ($aso_id == $office_val['Office']['id']) {
                    $office_list[$office_key]['SaleTarget'] = $val['SaleTarget'];
                }
            }
        }
        /* -------end making office list with outlet_coverage and effective_call ------- */
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
        $this->SaleTarget->recursive = -1;
        $saletarget_list = $this->SaleTarget->find('all', array('fields' => array('id', 'aso_id', 'outlet_coverage_pharma', 'outlet_coverage_non_pharma', 'session', 'effective_call_pharma','effective_call_non_pharma'),
            'conditions' => array(
                'AND' => array(
                    array(
                        'SaleTarget.fiscal_year_id' => $this->request->data('FiscalYearId'),
                        'SaleTarget.target_type_id' => 1,
                        'SaleTarget.target_category' => 2
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
        $this->SaleTarget->recursive = -1;
        $saletarget_list = $this->SaleTarget->find('all', array('fields' => array('id','outlet_coverage_pharma', 'outlet_coverage_non_pharma', 'session', 'effective_call_pharma','effective_call_non_pharma'),
            'conditions' => array(
                'AND' => array(
                    array(
                        'SaleTarget.fiscal_year_id' => $this->request->data('FiscalYearId'),
                        'SaleTarget.target_type_id' => 1,
                        'SaleTarget.target_category' => 1
                    )
        ))));
        echo json_encode($saletarget_list);
        $this->autoRender = false;
    }
	public function admin_upload_xl(){
        $this->loadModel('FiscalYear');
        $this->loadModel('Office');
        if(!empty($_FILES["file"]["name"])){
            $target_dir = WWW_ROOT.'files/';;
            $target_file = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 30);
            $uploadOk = 1;
            $imageFileType = pathinfo($_FILES["file"]["name"],PATHINFO_EXTENSION);
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir.$target_file.'.'.$imageFileType)) {
                $data_ex = new Spreadsheet_Excel_Reader($target_dir.$target_file.'.'.$imageFileType, true);
                $temp = $data_ex->dumptoarray();
                $this->SaleTarget->recursive = -1;
                $insert_data_array = array();
				$update_data_array = array();
                foreach ($temp as $key => $val) {
                    if($key>1 && !empty($val[1])){
                         $fiscal_year_id = $this->FiscalYear->find('first',array(
                            'fields'=>array('FiscalYear.id'),
                            'conditions'=>array('FiscalYear.year_code LIKE'=>'%'.trim($val[1].'%')),
                            'recursive'=>-1
                            ));
                       
                        $aso_id = $this->Office->find('first',array(
                            'fields'=>'Office.id',
                            'conditions'=>array('lower(Office.office_name) like'=>'%'.strtolower($val[2]).'%'),
                            'recursive'=>-1
                            ));
                        if($fiscal_year_id && !$aso_id)
                        {
                            $this->Session->setFlash(__('The fiscal year or Offie Name missing or incorrect on line '.$key), 'flash/error');
                            $this->redirect(array("controller" => "EffectiveCalls","action" => "admin_index"));
                        }
						$saletargets = $this->SaleTarget->find('first', array('conditions' => array('SaleTarget.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'],'SaleTarget.aso_id' => $aso_id['Office']['id'],'SaleTarget.target_type_id' => 1, 'SaleTarget.target_category' => 2)));
						if(empty($saletargets)){	
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
						}
						else 
						{
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
				if($insert_data_array)
				{
					$this->SaleTarget->create();
					$this->SaleTarget->saveAll($insert_data_array);
				}
				if($update_data_array)
				{
					$this->SaleTarget->saveAll($update_data_array);
				}
				
                $this->Session->setFlash(__('The Sale Targets has been saved'), 'flash/success');
				$this->redirect(array("controller" => " EffectiveCalls", 
                      "action" => "admin_index"));
            }
		}
	}

}
