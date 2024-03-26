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
class DistAreaEcOcMonthlyTargetsController extends AppController {

    //var $uses =false;

    public $uses = array('DistSaleTargetMonth', 'Product', 'Office', 'FiscalYear');

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
        $this->set('page_title', 'Distributor Area Ec Oc Targets (Monthly)');
       // die();
        if ($this->request->is('post')) 
		{            
            $fiscal_year_id = $this->request->data['DistAreaEcOcMonthlyTarget']['fiscal_year_id'];
			$month_id = $this->request->data['DistAreaEcOcMonthlyTarget']['month_id'];
            $saletarget_list = $this->DistSaleTargetMonth->find('all', array(
                'fields' => array('DistSaleTargetMonth.id'),
                'conditions' => array(
                    'DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_id,
					'DistSaleTargetMonth.month_id' => $month_id,
                    'DistSaleTargetMonth.target_type' => 4,
                    //'DistSaleTargetMonth.target_category' => 2,
                ),
                'recursive' => -1
                    )
            );
            if (empty($saletarget_list)) 
			{
                if (!empty($this->request->data['Office'])) 
				{
                    $this->DistSaleTargetMonth->create();
                    $data_array = array();
                    foreach ($this->request->data['Office']['DistSaleTargetMonth']['effective_call_pharma'] as $key => $val) {
                        $data['DistSaleTargetMonth']['aso_id'] = $key;
                        $data['DistSaleTargetMonth']['target_type'] = 4;
                        //$data['DistSaleTargetMonth']['target_category'] = 2;
                        $data['DistSaleTargetMonth']['fiscal_year_id'] = $this->request->data['DistAreaEcOcMonthlyTarget']['fiscal_year_id'];
						$data['DistSaleTargetMonth']['month_id'] = $this->request->data['DistAreaEcOcMonthlyTarget']['month_id'];
                        $data['DistSaleTargetMonth']['outlet_coverage_pharma'] = $this->request->data['Office']['DistSaleTargetMonth']['outlet_coverage_pharma'][$key]?$this->request->data['Office']['DistSaleTargetMonth']['outlet_coverage_pharma'][$key]:0;
                        $data['DistSaleTargetMonth']['outlet_coverage_non_pharma'] = $this->request->data['Office']['DistSaleTargetMonth']['outlet_coverage_non_pharma'][$key]?$this->request->data['Office']['DistSaleTargetMonth']['outlet_coverage_non_pharma'][$key]:0;
                        
                        $data['DistSaleTargetMonth']['effective_call_pharma'] = $val?$val:0;
                        $data['DistSaleTargetMonth']['effective_call_non_pharma'] = $this->request->data['Office']['DistSaleTargetMonth']['effective_call_non_pharma'][$key]?$this->request->data['Office']['DistSaleTargetMonth']['effective_call_non_pharma'][$key]:0;
						
						$data['DistSaleTargetMonth']['created_at'] = $this->current_datetime();
						$data['DistSaleTargetMonth']['created_by'] = $this->UserAuth->getUserId();
						$data['DistSaleTargetMonth']['updated_at'] = $this->current_datetime();
						$data['DistSaleTargetMonth']['updated_by'] = $this->UserAuth->getUserId();
						
						$data['DistSaleTargetMonth']['target_quantity'] = 0;
						$data['DistSaleTargetMonth']['target_amount'] = 0;
						
                        $data_array[] = $data;
                    }
					//pr($data_array); exit;
					
                    $this->DistSaleTargetMonth->saveAll($data_array);
                }
            } 
			else 
			{
                if (!empty($this->request->data['Office'])) 
				{
                    $data_array = array();
                    foreach ($this->request->data['Office']['DistSaleTargetMonth']['effective_call_pharma'] as $key => $val) 
					{
                        $existing_data = $this->DistSaleTargetMonth->find('all', array(
                            'fields' => array('DistSaleTargetMonth.id'),
                            'conditions' => array(
                                'DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_id,
								'DistSaleTargetMonth.month_id' => $month_id,
                                'DistSaleTargetMonth.target_type' => 4,
                                //'DistSaleTargetMonth.target_category' => 2,
                                'DistSaleTargetMonth.aso_id' => $key,
                            ),
                            'recursive' => -1
                                )
                        );
						
                        if (!empty($existing_data[0]['DistSaleTargetMonth']['id'])) 
						{
                            $this->DistSaleTargetMonth->id = $existing_data[0]['DistSaleTargetMonth']['id'];
                            $data['DistSaleTargetMonth']['target_type'] = 4;
                            //$data['DistSaleTargetMonth']['target_category'] = 2;
                            $data['DistSaleTargetMonth']['fiscal_year_id'] = $this->request->data['DistAreaEcOcMonthlyTarget']['fiscal_year_id'];
							$data['DistSaleTargetMonth']['month_id'] = $this->request->data['DistAreaEcOcMonthlyTarget']['month_id'];
                            $data['DistSaleTargetMonth']['outlet_coverage_pharma'] = $this->request->data['Office']['DistSaleTargetMonth']['outlet_coverage_pharma'][$key];
                            $data['DistSaleTargetMonth']['outlet_coverage_non_pharma'] = $this->request->data['Office']['DistSaleTargetMonth']['outlet_coverage_non_pharma'][$key];
                           
                            $data['DistSaleTargetMonth']['effective_call_pharma'] = $val;
                            $data['DistSaleTargetMonth']['effective_call_non_pharma'] = $this->request->data['Office']['DistSaleTargetMonth']['effective_call_non_pharma'][$key];
							
							$data['DistSaleTargetMonth']['updated_at'] = $this->current_datetime();
							$data['DistSaleTargetMonth']['updated_by'] = $this->UserAuth->getUserId();
							
                            $this->DistSaleTargetMonth->save($data);
                        }
                    }
                }
            }
            $this->Session->setFlash(__('The DistAreaEcOcMonthlyTarget has been saved'), 'flash/success');
        }
        //exit;
		
		//get current month id
		$current_month = date("m");
        $this->loadModel('Month');
		$this->Month->recursive = -1;
        $current_month_info = $this->Month->find('first', array(
            'fields' => array('id'),
            'conditions' => array('month' => $current_month)
        ));
		
		$months = $this->Month->find('list', array('order' => array('Month.month' => 'asc')));	
			
		$current_month_id = $current_month_info['Month']['id'];
        if (isset($this->request->data['DistSaleTargetMonth']['month_id'])) {
            $current_month_id = $this->request->data['DistSaleTargetMonth']['month_id'];
        }
		$this->set(compact('months','current_month_id'));
		//echo $current_month_id;
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
        $this->DistSaleTargetMonth->recursive = -1;
        $office_list_with_eff_call_list = $this->DistSaleTargetMonth->find('all', array(
            'conditions' => array(
                'DistSaleTargetMonth.fiscal_year_id' => $year_id,
				'DistSaleTargetMonth.month_id' => $current_month_id,
                //'DistSaleTargetMonth.target_category' => 2,
                'DistSaleTargetMonth.target_type' => 4
            )
                )
        );
        /* -------start making office list with outlet_coverage and effective_call ------- */
        foreach ($office_list_with_eff_call_list as $key => $val) {
            $aso_id = $val['DistSaleTargetMonth']['aso_id'];
            foreach ($office_list as $office_key => $office_val) {
                if ($aso_id == $office_val['Office']['id']) {
                    $office_list[$office_key]['DistSaleTargetMonth'] = $val['DistSaleTargetMonth'];
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
		$this->loadModel('DistSaleTargetMonth');
		$saletarget_list = $this->DistSaleTargetMonth->find('all', array(
                'fields' => array('id', 'aso_id', 'outlet_coverage_pharma', 'outlet_coverage_non_pharma', 'effective_call_pharma','effective_call_non_pharma'),
                'conditions' => array(
                    'DistSaleTargetMonth.fiscal_year_id' => $this->request->data('FiscalYearId'),
					'DistSaleTargetMonth.month_id' => $this->request->data('month_id'),
                    'DistSaleTargetMonth.target_type' => 4,
                    //'DistSaleTargetMonth.target_category' => 2,
                ),
                'recursive' => -1
                    )
            );
		
        echo json_encode($saletarget_list);
        $this->autoRender = false;
    }

    public function admin_get_national_effective_call_data() {
        $this->DistSaleTargetMonth->recursive = -1;
        $saletarget_list = $this->DistSaleTargetMonth->find('all', array('fields' => array('id','outlet_coverage_pharma', 'outlet_coverage_non_pharma', 'effective_call_pharma','effective_call_non_pharma'),
            'conditions' => array(
                'AND' => array(
                    array(
                        'DistSaleTargetMonth.fiscal_year_id' => $this->request->data('FiscalYearId'),
						'DistSaleTargetMonth.month_id' => $this->request->data('month_id'),
                        'DistSaleTargetMonth.target_type' => 3,
                        //'DistSaleTargetMonth.target_category' => 1
                    )
        ))));
        echo json_encode($saletarget_list);
        $this->autoRender = false;
    }
	public function admin_upload_xl(){
        $this->loadModel('FiscalYear');
		$this->loadModel('Month');
        $this->loadModel('Office');
        if(!empty($_FILES["file"]["name"])){
            $target_dir = WWW_ROOT.'files/';;
            $target_file = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 30);
            $uploadOk = 1;
            $imageFileType = pathinfo($_FILES["file"]["name"],PATHINFO_EXTENSION);
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir.$target_file.'.'.$imageFileType)) {
                $data_ex = new Spreadsheet_Excel_Reader($target_dir.$target_file.'.'.$imageFileType, true);
                $temp = $data_ex->dumptoarray();
                $this->DistSaleTargetMonth->recursive = -1;
                $insert_data_array = array();
				$update_data_array = array();
                foreach ($temp as $key => $val) {
                    if($key>1 && !empty($val[1])){
                         $fiscal_year_id = $this->FiscalYear->find('first',array(
                            'fields'=>array('FiscalYear.id'),
                            'conditions'=>array('FiscalYear.year_code LIKE'=>'%'.trim($val[0].'%')),
                            'recursive'=>-1
                            ));
                        $month_info = $this->Month->find('first',array(
                            'fields'=>array('Month.id'),
                            'conditions'=>array('Month.name LIKE'=>'%'.trim($val[1].'%')),
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
                            $this->redirect(array("controller" => "DistAreaEcOcMonthlyTargets","action" => "admin_index"));
                        }
						$saletargets = $this->DistSaleTargetMonth->find('first', array('conditions' => array('DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'],'DistSaleTargetMonth.aso_id' => $aso_id['Office']['id'],'DistSaleTargetMonth.target_type' => 4)));
						if(empty($saletargets)){	
                            //$insert_data['DistSaleTargetMonth']['target_category'] = 2;
                            $insert_data['DistSaleTargetMonth']['fiscal_year_id'] = $fiscal_year_id['FiscalYear']['id'];
							$insert_data['DistSaleTargetMonth']['month_id'] = $fiscal_year_id['Month']['id'];
							$insert_data['DistSaleTargetMonth']['aso_id'] = $aso_id['Office']['id'];
							$insert_data['DistSaleTargetMonth']['target_type'] = 4;
							$insert_data['DistSaleTargetMonth']['outlet_coverage_pharma'] = $val[6];
							$insert_data['DistSaleTargetMonth']['outlet_coverage_non_pharma'] = $val[7];
							$insert_data['DistSaleTargetMonth']['effective_call_pharma'] = $val[3];
							$insert_data['DistSaleTargetMonth']['effective_call_non_pharma'] = $val[4];
                            $insert_data_array[] = $insert_data;
						}
						else 
						{
							$updated_data['DistSaleTargetMonth']['id'] = $saletargets['DistSaleTargetMonth']['id'];
							$updated_data['DistSaleTargetMonth']['target_type'] = 4;
                            $updated_data['DistSaleTargetMonth']['outlet_coverage_pharma'] = $val[6];
                            $updated_data['DistSaleTargetMonth']['outlet_coverage_non_pharma'] = $val[7];
                            $updated_data['DistSaleTargetMonth']['effective_call_pharma'] = $val[3];
                            $updated_data['DistSaleTargetMonth']['effective_call_non_pharma'] = $val[4];
                            $update_data_array[] = $updated_data;
						}
                    }

                }
				if($insert_data_array)
				{
					$this->DistSaleTargetMonth->create();
					$this->DistSaleTargetMonth->saveAll($insert_data_array);
				}
				if($update_data_array)
				{
					$this->DistSaleTargetMonth->saveAll($update_data_array);
				}
				
                $this->Session->setFlash(__('The Sale Targets has been saved'), 'flash/success');
				$this->redirect(array("controller" => " DistAreaEcOcMonthlyTargets", 
                      "action" => "admin_index"));
            }
		}
	}
	
	

}
