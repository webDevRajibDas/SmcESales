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
class DistAreaSrEcOcMonthlyTargetsController extends AppController {

    public $uses = array('DistSaleTargetMonth', 'Office', 'DistSalesRepresentative', 'DistSalesRepresentative');

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

        if ($this->request->is('post')) {
            //echo '<pre>';
            //print_r($this->request->data);
            // echo '</pre>';
            //exit();
            $fiscal_year_id = $this->request->data['DistSaleTargetMonth']['fiscal_year_id'];
			$month_id = $this->request->data['DistSaleTargetMonth']['month_id'];
            $aso_id = $this->request->data['DistSaleTargetMonth']['aso_id'];
            $effective_call_base_list = $this->DistSaleTargetMonth->find('list', array('fields' => array('id'),
                'conditions' => array(
                    'AND' => array(
                        array(
                            'DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_id,
                            'DistSaleTargetMonth.target_type' => 5,
                            'DistSaleTargetMonth.month_id' => $month_id,
                            'DistSaleTargetMonth.aso_id' => $aso_id,
                        )
            ))));
            if (empty($effective_call_base_list)) {
                if (!empty($this->request->data['DistSaleTargetMonth'])) {
                    $this->DistSaleTargetMonth->create();
                    $data_array = array();

                    foreach ($this->request->data['DistSaleTargetMonth']['effective_call_pharma'] as $key => $val) 
					{
                        $data['DistSaleTargetMonth']['target_type'] = 5;
                        $data['DistSaleTargetMonth']['month_id'] = $month_id;
                        $data['DistSaleTargetMonth']['aso_id'] = $this->request->data['DistSaleTargetMonth']['aso_id'];
                        $data['DistSaleTargetMonth']['dist_sales_representative_code'] = $this->request->data['DistSaleTargetMonth']['dist_sales_representative_code'][$key];
                        $data['DistSaleTargetMonth']['fiscal_year_id'] = $this->request->data['DistSaleTargetMonth']['fiscal_year_id'];
                        $data['DistSaleTargetMonth']['outlet_coverage_pharma'] = $this->request->data['DistSaleTargetMonth']['outlet_coverage_pharma'][$key];
                        $data['DistSaleTargetMonth']['outlet_coverage_non_pharma'] = $this->request->data['DistSaleTargetMonth']['outlet_coverage_non_pharma'][$key];
                        $data['DistSaleTargetMonth']['effective_call_pharma'] = $val;
                        $data['DistSaleTargetMonth']['effective_call_non_pharma'] = $this->request->data['DistSaleTargetMonth']['effective_call_non_pharma'][$key];
                        
						$data['DistSaleTargetMonth']['created_at'] = $this->current_datetime();
						$data['DistSaleTargetMonth']['created_by'] = $this->UserAuth->getUserId();
						$data['DistSaleTargetMonth']['updated_at'] = $this->current_datetime();
						$data['DistSaleTargetMonth']['updated_by'] = $this->UserAuth->getUserId();
						
						$data['DistSaleTargetMonth']['target_quantity'] = 0;
						$data['DistSaleTargetMonth']['target_amount'] = 0;
						
                        $data_array[] = $data;
                    }
                    $this->DistSaleTargetMonth->saveAll($data_array);
                }
            } 
			else 
			{
                if (!empty($this->request->data['DistSaleTargetMonth'])) {
                    $data_array = array();
                    foreach ($this->request->data['DistSaleTargetMonth']['id'] as $key => $val) {
                        $data['DistSaleTargetMonth']['id'] = $val;
                        $data['DistSaleTargetMonth']['outlet_coverage_pharma'] = $this->request->data['DistSaleTargetMonth']['outlet_coverage_pharma'][$key];
                        $data['DistSaleTargetMonth']['outlet_coverage_non_pharma'] = $this->request->data['DistSaleTargetMonth']['outlet_coverage_non_pharma'][$key];
                        $data['DistSaleTargetMonth']['effective_call_pharma'] = $this->request->data['DistSaleTargetMonth']['effective_call_pharma'][$key];
                        $data['DistSaleTargetMonth']['effective_call_non_pharma'] = $this->request->data['DistSaleTargetMonth']['effective_call_non_pharma'][$key];
                        $data_array[] = $data;
                    }
                    $this->DistSaleTargetMonth->saveAll($data_array);
                }
            }
            $this->DistSaleTargetMonth->unbindModel(
                array('belongsTo' => array('FiscalYear','Product','MeasurementUnit','Office'))
            );
			
			$effective_call_list_base_wise = $this->DistSaleTargetMonth->find('all', array(
			'conditions' => array(
                'DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_id,
                'DistSalesRepresentative.office_id' => $aso_id,
				'DistSaleTargetMonth.month_id' => $month_id,
                'DistSaleTargetMonth.target_type' => 5,
				'DistSaleTargetMonth.dist_sales_representative_code >' => 0,
            ),
            'joins' => array(
                array(
                    'table' => 'dist_sales_representatives',
                    'alias' => 'DistSalesRepresentative',
                    'type' => 'INNER',
                    'conditions' => array(
                        'DistSalesRepresentative.code = DistSaleTargetMonth.dist_sales_representative_code'
                    )
                )
            ),
            'fields' => array(
                'DistSaleTargetMonth.*', 'DistSalesRepresentative.id', 'DistSalesRepresentative.code', 'DistSalesRepresentative.name')
        ));
			
            $office_val=$this->DistSaleTargetMonth->find('all', array(
                'conditions' => array(
                    'AND' => array(
                        array(
                            'DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_id,
                            'DistSaleTargetMonth.target_type' => 5,
                            'DistSaleTargetMonth.month_id' => $month_id,
                            'DistSaleTargetMonth.aso_id' => $aso_id,
                        )
            )),
                'recursive'=>-1
                ));
            $this->set(compact('effective_call_list_base_wise','office_val'));

            $this->Session->setFlash(__('Data has been saved'), 'flash/success');
            //$this->redirect(array('controller' => 'DistAreaSrEcOcMonthlyTargets', 'action' => 'index'));
        }
		
		//get current month id
		$current_month = date("m");
        $this->loadModel('Month');
		$this->Month->recursive = -1;
        /*$current_month_info = $this->Month->find('first', array(
            'fields' => array('id'),
            'conditions' => array('month' => $current_month)
        ));*/
		
		$months = $this->Month->find('list', array('order' => array('Month.month' => 'asc')));	
			
		$current_month_id = '';
        if (isset($this->request->data['DistSaleTargetMonth']['month_id'])) {
            $current_month_id = $this->request->data['DistSaleTargetMonth']['month_id'];
        }
		$this->set(compact('months','current_month_id'));
		//echo $c


        $this->set('page_title', 'Distributor Area SR Wise EC, OC Targets (Monthly)');
        $this->Office->recursive = 1;
        $saleOffice_list = $this->Office->find('list', array('conditions' => array('Office.office_type_id' => 2)));
        $fiscalYears = $this->DistSaleTargetMonth->FiscalYear->find('list', array('fields' => array('year_code')));
        $this->set(compact('fiscalYears', 'saleOffice_list'));
    }

    public function admin_get_effective_call_target_base_wise_data() {

        $this->DistSaleTargetMonth->recursive = -1;
        $effective_call_list_base_wise = $this->DistSaleTargetMonth->find('all', array(
			'conditions' => array(
                'DistSaleTargetMonth.fiscal_year_id' => $this->request->data('FiscalYearId'),
                'DistSalesRepresentative.office_id' => $this->request->data('aso_id'),
				'DistSaleTargetMonth.month_id' => $this->request->data('month_id'),
                'DistSaleTargetMonth.target_type' => 5,
				'DistSaleTargetMonth.dist_sales_representative_code >' => 0,
            ),
            'joins' => array(
                array(
                    'table' => 'dist_sales_representatives',
                    'alias' => 'DistSalesRepresentative',
                    'type' => 'INNER',
                    'conditions' => array(
                        'DistSalesRepresentative.code = DistSaleTargetMonth.dist_sales_representative_code'
                    )
                )
            ),
            'fields' => array(
                'DistSaleTargetMonth.*', 'DistSalesRepresentative.id', 'DistSalesRepresentative.code', 'DistSalesRepresentative.name')
        ));

        if (empty($effective_call_list_base_wise)) {
			$effective_call_list_base_wise_empty = $this->DistSalesRepresentative->find('all', array(
                'conditions' => array(
                    'DistSalesRepresentative.office_id' => $this->request->data('aso_id'),
                ),
                'recursive' => -1
            ));
        }
		
        $this->set(compact('effective_call_list_base_wise_empty', 'effective_call_list_base_wise'));
    }

    public function admin_get_effective_call_outlet_area_wise_data() {
        $this->loadModel('DistSaleTargetMonth');
        $DistSaleTargetMonth = $this->DistSaleTargetMonth->find('all', array(
            'conditions' => array(
                'DistSaleTargetMonth.aso_id' => $this->request->data['aso_id'],
                'DistSaleTargetMonth.fiscal_year_id' => $this->request->data['fiscalYearId'],
				'DistSaleTargetMonth.month_id' => $this->request->data['month_id'],
                'DistSaleTargetMonth.target_type' => 4
            ),
            'fields' => array('DistSaleTargetMonth.outlet_coverage_pharma', 'DistSaleTargetMonth.outlet_coverage_non_pharma', 'DistSaleTargetMonth.effective_call_pharma', 'DistSaleTargetMonth.effective_call_non_pharma'),
            'recursive' => -1
                )
        );
        echo json_encode($DistSaleTargetMonth);
        $this->autoRender = false;
    }





	
	public function admin_upload_xl_month(){
        $this->loadModel('Product');
        $this->loadModel('FiscalYear');
        $this->loadModel('Office');
        $this->loadModel('DistSalesRepresentative');
        $this->loadModel('Month');
        $this->loadModel('DistSaleTargetMonth');
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
                            'conditions'=>array('FiscalYear.year_code LIKE'=>'%'.trim($val[1].'%')),
                            'recursive'=>-1
                            ));
                        $aso_id = $this->Office->find('first',array(
                            'fields'=>'Office.id',
                            'conditions'=>array('lower(Office.office_name) like'=>'%'.strtolower($val[3]).'%'),
                            'recursive'=>-1
                            ));
                        $dist_sales_representative_code= $this->DistSalesRepresentative->find('first',array(
                            'fields'=>'DistSalesRepresentative.code',
                            'conditions'=>array('lower(DistSalesRepresentative.name) like'=>'%'.strtolower($val[4]).'%'),
                            'recursive'=>-1
                            ));
                        $month_id = $this->Month->find('first',array(
                            'fields'=>'Month.id',
                            'conditions'=>array('Month.fiscal_year_id'=>$fiscal_year_id['FiscalYear']['id'],'lower(Month.name) like'=>'%'.strtolower($val[2]).'%'),
                            'recursive'=>-1
                            ));
                        if($fiscal_year_id && !$aso_id && !$dist_sales_representative_code && !$month_id)
                        {
                            $this->Session->setFlash(__('The fiscal year or Offie Name or DistSalesRepresentative or month missing or incorrect on line '.$key), 'flash/error');
                            $this->redirect(array("controller" => "DistAreaSrEcOcMonthlyTargets","action" => "admin_index"));
                        }
						$saletarget_month = $this->DistSaleTargetMonth->find('first', array('conditions' => array('DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'],'DistSaleTargetMonth.aso_id' => $aso_id['Office']['id'],'DistSaleTargetMonth.month_id' =>$month_id['Month']['id'],'DistSaleTargetMonth.dist_sales_representative_code' => $dist_sales_representative_code['DistSalesRepresentative']['id'])));
						if(empty($saletarget_month)){
							$saletargets = $this->DistSaleTargetMonth->find('first', array('conditions' => array('DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'],'DistSaleTargetMonth.aso_id' => $aso_id['Office']['id'],'DistSaleTargetMonth.dist_sales_representative_code' =>$dist_sales_representative_code['DistSalesRepresentative']['id'],'DistSaleTargetMonth.target_type' => 1, 'DistSaleTargetMonth.target_category' => 3)));
							if(empty($saletargets))
							{
								$this->Session->setFlash(__('The Effective call and Outlet coverage not set'.$key), 'flash/error');
								$this->redirect(array("controller" => "DistAreaSrEcOcMonthlyTargets","action" => "admin_index"));
							}
							$insert_data['DistSaleTargetMonth']['sale_target_id'] = $saletargets['DistSaleTargetMonth']['id'];
                            $insert_data['DistSaleTargetMonth']['fiscal_year_id'] = $fiscal_year_id['FiscalYear']['id'];
							$insert_data['DistSaleTargetMonth']['month_id'] = $month_id['Month']['id'];;
							$insert_data['DistSaleTargetMonth']['aso_id'] = $aso_id['Office']['id'];
							$insert_data['DistSaleTargetMonth']['dist_sales_representative_code'] = $dist_sales_representative_code['DistSalesRepresentative']['code'];
							$insert_data['DistSaleTargetMonth']['target_type'] = 1;
							$insert_data['DistSaleTargetMonth']['outlet_coverage_pharma'] = $val[7];
							$insert_data['DistSaleTargetMonth']['outlet_coverage_non_pharma'] = $val[8];
							$insert_data['DistSaleTargetMonth']['effective_call_pharma'] = $val[5];
							$insert_data['DistSaleTargetMonth']['effective_call_non_pharma'] = $val[6];
                            $insert_data['DistSaleTargetMonth']['target_quantity'] = 0;
                            $insert_data_array[] = $insert_data;
						}
						else 
						{
							$updated_data['DistSaleTargetMonth']['id'] = $saletarget_month['DistSaleTargetMonth']['id'];
							$updated_data['DistSaleTargetMonth']['target_type'] = 1;
							$updated_data['DistSaleTargetMonth']['outlet_coverage_pharma'] = $val[7];
							$updated_data['DistSaleTargetMonth']['outlet_coverage_non_pharma'] = $val[8];
							$updated_data['DistSaleTargetMonth']['effective_call_pharma'] = $val[5];
							$updated_data['DistSaleTargetMonth']['effective_call_non_pharma'] = $val[6];
                            $update_data_array[] = $updated_data;
						}
                    }

                }
				//pr($update_data_array);
				//pr($insert_data_array);
				//exit;
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
				$this->redirect(array("controller" => " DistAreaSrEcOcMonthlyTargets", 
                      "action" => "admin_index"));
            }
		}
	}

}
