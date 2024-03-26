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
class DistEffectiveCallsBaseWiseController extends AppController {

    public $uses = array('DistSaleTarget', 'Office', 'Territory');

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
            $fiscal_year_id = $this->request->data['SaleTarget']['fiscal_year_id'];
            $aso_id = $this->request->data['SaleTarget']['aso_id'];
            $effective_call_base_list = $this->DistSaleTarget->find('list', array('fields' => array('id'),
                'conditions' => array(
                    'AND' => array(
                        array(
                            'DistSaleTarget.fiscal_year_id' => $fiscal_year_id,
                            'DistSaleTarget.target_type_id' => 1,
                            'DistSaleTarget.target_category' => 3,
                            'DistSaleTarget.aso_id' => $aso_id,
                        )
            ))));
            if (empty($effective_call_base_list)) {
                if (!empty($this->request->data['SaleTarget'])) {
                    $this->SaleTarget->create();
                    $data_array = array();
                    foreach ($this->request->data['SaleTarget']['effective_call_pharma'] as $key => $val) {
                        $data['SaleTarget']['target_type_id'] = 1;
                        $data['SaleTarget']['target_category'] = 3;
                        $data['SaleTarget']['aso_id'] = $this->request->data['SaleTarget']['aso_id'];
                        $data['SaleTarget']['territory_id'] = $this->request->data['SaleTarget']['territory_id'][$key];
                        $data['SaleTarget']['fiscal_year_id'] = $this->request->data['SaleTarget']['fiscal_year_id'];
                        $data['SaleTarget']['outlet_coverage_pharma'] = $this->request->data['SaleTarget']['outlet_coverage_pharma'][$key];
                        $data['SaleTarget']['outlet_coverage_non_pharma'] = $this->request->data['SaleTarget']['outlet_coverage_non_pharma'][$key];
                        $data['SaleTarget']['session'] = $this->request->data['SaleTarget']['session'][$key];
                        $data['SaleTarget']['effective_call_pharma'] = $val;
                        $data['SaleTarget']['effective_call_non_pharma'] = $this->request->data['SaleTarget']['effective_call_non_pharma'][$key];
                        ;
                        $data_array[] = $data;
                    }
                    $this->SaleTarget->saveAll($data_array);
                }
            } else {
                if (!empty($this->request->data['SaleTarget'])) {
                    $data_array = array();
                    foreach ($this->request->data['SaleTarget']['id'] as $key => $val) {
                        $data['SaleTarget']['id'] = $val;
                        $data['SaleTarget']['outlet_coverage_pharma'] = $this->request->data['SaleTarget']['outlet_coverage_pharma'][$key];
                        $data['SaleTarget']['outlet_coverage_non_pharma'] = $this->request->data['SaleTarget']['outlet_coverage_non_pharma'][$key];
                        $data['SaleTarget']['session'] = $this->request->data['SaleTarget']['session'][$key];
                        $data['SaleTarget']['effective_call_pharma'] = $this->request->data['SaleTarget']['effective_call_pharma'][$key];
                        $data['SaleTarget']['effective_call_non_pharma'] = $this->request->data['SaleTarget']['effective_call_non_pharma'][$key];
                        $data_array[] = $data;
                    }
                    $this->SaleTarget->saveAll($data_array);
                }
            }
            $this->SaleTarget->unbindModel(
                    array('belongsTo' => array('FiscalYear', 'Product', 'MeasurementUnit', 'Office'))
            );
            $effective_call_list_base_wise = $this->SaleTarget->find('all', array(
                'conditions' => array(
                    'AND' => array(
                        array(
                            'SaleTarget.fiscal_year_id' => $fiscal_year_id,
                            'SaleTarget.target_type_id' => 1,
                            'SaleTarget.target_category' => 3,
                            'SaleTarget.aso_id' => $aso_id,
                        )
            ))));
            $office_val = $this->SaleTarget->find('all', array(
                'conditions' => array(
                    'AND' => array(
                        array(
                            'SaleTarget.fiscal_year_id' => $fiscal_year_id,
                            'SaleTarget.target_type_id' => 1,
                            'SaleTarget.target_category' => 2,
                            'SaleTarget.aso_id' => $aso_id,
                        )
                    )),
                'recursive' => -1
            ));
            $this->set(compact('effective_call_list_base_wise', 'office_val'));

            $this->Session->setFlash(__('Data has been saved'), 'flash/success');
            //$this->redirect(array('controller' => 'EffectiveCallsBaseWise', 'action' => 'index'));
        }


        $this->set('page_title', 'Distributor Effective Calls Base Wise');
        $this->Office->recursive = 1;
        $saleOffice_list = $this->Office->find('list', array('conditions' => array('Office.office_type_id' => 2)));
        $fiscalYears = $this->DistSaleTarget->FiscalYear->find('list', array('fields' => array('year_code')));
        $this->set(compact('fiscalYears', 'saleOffice_list'));
    }

    public function admin_get_effective_call_target_base_wise_data() {

        $this->DistSaleTarget->recursive = -1;
        $effective_call_list_base_wise = $this->DistSaleTarget->find('all', array(
            'conditions' => array(
                'AND' => array(
                    array(
                        'DistSaleTarget.fiscal_year_id' => $this->request->data('FiscalYearId'),
                        'DistSaleTarget.target_type_id' => 1,
                        'DistSaleTarget.target_category' => 3,
                        'DistSaleTarget.aso_id' => $this->request->data('SaleTargetAsoId'),
                    ))),
            'joins' => array(
                array(
                    'alias' => 'DistDistributor',
                    'table' => 'dist_distributors',
                    'type' => 'INNER',
                    'conditions' => 'DistSaleTarget.dist_distributor_id = DistDistributor.id'
                )
            ),
            'fields' => array(
                'DistSaleTarget.id', 'DistDistributor.id','DistDistributor.name'
            )
        ));
        if (empty($effective_call_list_base_wise)) {
            $this->loadModel('DistDistributor');
            $this->DistDistributor->recursive = -1;
            $effective_call_list_base_wise_empty = $this->DistDistributor->find('all', array(
                'conditions' => array('DistDistributor.office_id' => $this->request->data('SaleTargetAsoId'))
                    )
            );
        }
        $this->set(compact('effective_call_list_base_wise_empty', 'effective_call_list_base_wise'));
    }

    public function admin_get_effective_call_outlet_session_base_wise_data() {
        $this->loadModel('DistSaleTarget');
        $SaleTarget = $this->DistSaleTarget->find('all', array(
            'conditions' => array(
                'DistSaleTarget.aso_id' => $this->request->data['saleTargetAsoId'],
                'DistSaleTarget.fiscal_year_id' => $this->request->data['fiscalYearId'],
                'DistSaleTarget.target_category' => 2,
                'DistSaleTarget.target_type_id' => 1
            ),
            'fields' => array('DistSaleTarget.outlet_coverage_pharma', 'DistSaleTarget.outlet_coverage_non_pharma','DistSaleTarget.effective_call_pharma', 'DistSaleTarget.effective_call_non_pharma'),
            'recursive' => -1
                )
        );
        echo json_encode($SaleTarget);
        $this->autoRender = false;
    }

    /* ----------------------- Start effective call month wise ------------------------ */

    public function admin_set_monthly_effective_call() {
        $this->loadModel('Month');
        $this->loadModel('Territory');
        $this->loadModel('SalesPerson');
        $this->loadModel('TerritoryPerson');
        $this->loadModel('SaleTargetMonth');
        $this->set('page_title', 'Effective Calls Month Wise');
        $saleOffice_list = $this->Office->find('list', array('conditions' => array('Office.office_type_id' => 2)));
        $fiscalYears = $this->SaleTarget->FiscalYear->find('list', array('fields' => array('year_code')));
        $month_list = $this->Month->find('list', array('fields' => array('Month.id', 'Month.name')));
        $created_at = $this->current_datetime();
        if ($this->request->is('post') || $this->request->is('put')) {
            $fiscal_year_id = $this->request->data['SaleTargetMonth']['fiscal_year_id'];
            $aso_id = $this->request->data['SaleTargetMonth']['aso_id'];
            /* ----- start effective call and outlet coverage insert or update ----- */
            $this->SaleTargetMonth->create();
            foreach ($this->request->data['SaleTargetMonth']['effective_call'] as $key => $val) {
                $single_row = array();
                $single_row['territory_id'] = $key;
                $single_row['aso_id'] = $this->request->data['SaleTargetMonth']['aso_id'];
                $single_row['fiscal_year_id'] = $this->request->data['SaleTargetMonth']['fiscal_year_id'];
                $data_row = array();
                foreach ($val as $month_key => $month_val) {
                    $unique_sale_target_id = $this->request->data['SaleTargetMonth']['sale_target_id'][$key][$month_key];
                    $this->SaleTargetMonth->recursive = -1;
                    $exist_month_data = $this->SaleTargetMonth->find('all', array(
                        'conditions' => array(
                            'SaleTargetMonth.fiscal_year_id' => $fiscal_year_id,
                            'SaleTargetMonth.aso_id' => $aso_id,
                            'SaleTargetMonth.sale_target_id' => $unique_sale_target_id,
                            'SaleTargetMonth.month_id' => $month_key,
                        )
                    ));
                    if (!empty($exist_month_data)) {
                        $this->SaleTargetMonth->id = $exist_month_data[0]['SaleTargetMonth']['id'];
                        $single_row['created_at'] = $created_at;
                        $single_row['updated_at'] = $created_at;
                        $single_row['month_id'] = $month_key;
                        $single_row['effective_call'] = $month_val;
                        $single_row['sale_target_id'] = $unique_sale_target_id;
                        $updated_data['SaleTargetMonth'] = $single_row;
                        $this->SaleTargetMonth->save($updated_data);
                    } else {
                        $single_row['month_id'] = $month_key;
                        $single_row['effective_call'] = $month_val;
                        $single_row['sale_target_id'] = $this->request->data['SaleTargetMonth']['sale_target_id'][$key][$month_key];
                        $single_row['created_at'] = $created_at;
                        $single_row['updated_at'] = $created_at;
                        $data_row['SaleTargetMonth'][] = $single_row;
                    }
                }
                if (!empty($data_row['SaleTargetMonth'])) {
                    if ($this->SaleTargetMonth->saveAll($data_row['SaleTargetMonth'])) {
                        $data_row['SaleTargetMonth'] = array();
                    }
                }
            }
            /* ----- end effective call and outlet coverage insert or update ----- */

            /* ---- start show default data ----- */
            /* ---- start create month list ------ */
            $month_list = $this->Month->find('list', array(
                'fields' => array('Month.id', 'Month.name'),
                'conditions' => array('Month.fiscal_year_id' => $fiscal_year_id)
            ));
            $filter_array = array();
            foreach ($month_list as $key => $val) {
                $filter_array[] = array('id' => $key, 'name' => $val);
            }
            /* ----- end create month list ----- */
            $this->Territory->bindModel(
                    array('hasMany' => array(
                            'SaleTarget', 'TerritoryPerson', 'SalesPerson'
                        )
                    )
            );
            /* ----- start Territory infomariont ----- */
            $this->Territory->recursive = 0;
            $saletargets_list = $this->Territory->find('all', array(
                'conditions' => array(
                    'SaleTarget.fiscal_year_id' => $fiscal_year_id,
                    'SaleTarget.aso_id' => $aso_id,
                    'SaleTarget.target_type_id' => 1,
                    'SaleTarget.target_category' => 3,
                ),
                "joins" => array(
                    array(
                        "table" => "sale_targets",
                        "alias" => "SaleTarget",
                        "type" => "INNER",
                        "conditions" => array("territory.id = SaleTarget.territory_id"
                        )
                    ),
                    array(
                        "table" => "territory_people",
                        "alias" => "TerritoryPerson",
                        "type" => "INNER",
                        "conditions" => array("Territory.id = TerritoryPerson.territory_id"
                        )
                    ),
                    array(
                        "table" => "sales_people",
                        "alias" => "SalesPerson",
                        "type" => "INNER",
                        "conditions" => array("TerritoryPerson.sales_person_id = SalesPerson.id"
                        )
                    )
                ),
                'fields' => array('Territory.*', 'SaleTarget.*', 'TerritoryPerson.*', 'SalesPerson.*')
                    )
            );
            /* ------ start make data with monthly effective call ------- */
            $monthly_effective_data = array();
            foreach ($saletargets_list as $saletarget_key => $saletarget_val) {
                $sales_target_id = $saletarget_val['SaleTarget']['id'];
                $this->SaleTargetMonth->recursive = -1;
                $monthly_targets = $this->SaleTargetMonth->find('all', array(
                    'conditions' => array(
                        'SaleTargetMonth.fiscal_year_id' => $fiscal_year_id,
                        'SaleTargetMonth.aso_id' => $aso_id,
                        'SaleTargetMonth.sale_target_id' => $sales_target_id,
                    )
                ));
                /* ---- set monthly data by index month id ---- */
                $territory_id = $saletarget_val['Territory']['id'];
                foreach ($monthly_targets as $month_target_key => $month_target_val) {
                    $territory_id_in_month_target = $month_target_val['SaleTargetMonth']['territory_id'];
                    $unique_month_id = $month_target_val['SaleTargetMonth']['month_id'];
                    if ($territory_id == $territory_id_in_month_target) {
                        $saletargets_list[$saletarget_key]['SaleTargetMonth'][$unique_month_id] = $month_target_val['SaleTargetMonth'];
                    }
                }
            }
            /* ---- end show default data ----- */


            $this->Session->setFlash(__('The Monthly Effective Call has been saved'), 'flash/success');
        }


        $this->set(compact('fiscalYears', 'saleOffice_list', 'month_list', 'saletargets_list', 'filter_array'));
    }

    /* ----------------------- End effective call month wise ------------------------ */

    public function admin_month_effective_call_view() {
        $this->loadModel('Territory');
        $this->loadModel('Month');
        $this->loadModel('SalesPeople');
        $this->loadModel('SaleTargetMonth');
        $fiscal_year_id = $this->request->data['fiscal_year_id'];
        $aso_id = $this->request->data['aso_id'];
        /* ---- start create month list ------ */
        $month_list = $this->Month->find('list', array(
            'fields' => array('Month.id', 'Month.name'),
            'conditions' => array('Month.fiscal_year_id' => $fiscal_year_id)
        ));
        $filter_array = array();
        foreach ($month_list as $key => $val) {
            $filter_array[] = array('id' => $key, 'name' => $val);
        }
        /* ----- end create month list ----- */
        $this->Territory->bindModel(
                array('hasMany' => array(
                        'SaleTarget', 'TerritoryPerson', 'SalesPerson'
                    )
                )
        );
        /* ----- start Territory infomariont ----- */
        $this->Territory->recursive = 0;
        $saletargets_list = $this->Territory->find('all', array(
            'conditions' => array(
                'SaleTarget.fiscal_year_id' => $fiscal_year_id,
                'SaleTarget.aso_id' => $aso_id,
                'SaleTarget.target_type_id' => 1,
                'SaleTarget.target_category' => 3,
            ),
            "joins" => array(
                array(
                    "table" => "sale_targets",
                    "alias" => "SaleTarget",
                    "type" => "INNER",
                    "conditions" => array("territory.id = SaleTarget.territory_id"
                    )
                ),
                array(
                    "table" => "territory_people",
                    "alias" => "TerritoryPerson",
                    "type" => "INNER",
                    "conditions" => array("Territory.id = TerritoryPerson.territory_id"
                    )
                ),
                array(
                    "table" => "sales_people",
                    "alias" => "SalesPerson",
                    "type" => "INNER",
                    "conditions" => array("TerritoryPerson.sales_person_id = SalesPerson.id"
                    )
                )
            ),
            'fields' => array('Territory.*', 'SaleTarget.*', 'TerritoryPerson.*', 'SalesPerson.*')
                )
        );
        /* ------ start make data with monthly effective call ------- */
        $monthly_effective_data = array();
        foreach ($saletargets_list as $saletarget_key => $saletarget_val) {
            $sales_target_id = $saletarget_val['SaleTarget']['id'];
            $this->SaleTargetMonth->recursive = -1;
            $monthly_targets = $this->SaleTargetMonth->find('all', array(
                'conditions' => array(
                    'SaleTargetMonth.fiscal_year_id' => $fiscal_year_id,
                    'SaleTargetMonth.aso_id' => $aso_id,
                    'SaleTargetMonth.sale_target_id' => $sales_target_id,
                )
            ));
            /* ---- set monthly data by index month id ---- */
            $territory_id = $saletarget_val['Territory']['id'];
            foreach ($monthly_targets as $month_target_key => $month_target_val) {
                $territory_id_in_month_target = $month_target_val['SaleTargetMonth']['territory_id'];
                $unique_month_id = $month_target_val['SaleTargetMonth']['month_id'];
                if ($territory_id == $territory_id_in_month_target) {
                    $saletargets_list[$saletarget_key]['SaleTargetMonth'][$unique_month_id] = $month_target_val['SaleTargetMonth'];
                }
            }
        }
        /* ------ end make data with monthly effective call ------- */
        /* ----- end Territory infomariont ----- */
        $this->set(compact('month_list', 'saletargets_list', 'filter_array'));
    }

    /* ================= end monthly effective call ================== */

    public function admin_set_monthly_outlet_coverage() {
        $this->loadModel('Month');
        $this->loadModel('Territory');
        $this->loadModel('SalesPerson');
        $this->loadModel('TerritoryPerson');
        $this->loadModel('SaleTargetMonth');
        $this->set('page_title', 'Effective Calls Month Wise');
        $saleOffice_list = $this->Office->find('list', array('conditions' => array('Office.office_type_id' => 2)));
        $fiscalYears = $this->SaleTarget->FiscalYear->find('list', array('fields' => array('year_code')));
        $month_list = $this->Month->find('list', array('fields' => array('Month.id', 'Month.name')));
        $created_at = $this->current_datetime();
        if ($this->request->is('post') || $this->request->is('put')) {
            /* 			echo "<pre>";
              print_r($this->request->data);
              exit; */
            $fiscal_year_id = $this->request->data['SaleTargetMonth']['fiscal_year_id'];
            $aso_id = $this->request->data['SaleTargetMonth']['aso_id'];
            /* ----- start effective call and outlet coverage insert or update ----- */
            $this->SaleTargetMonth->create();
            foreach ($this->request->data['SaleTargetMonth']['outlet_coverage'] as $key => $val) {
                $single_row = array();
                $single_row['territory_id'] = $key;
                $single_row['aso_id'] = $this->request->data['SaleTargetMonth']['aso_id'];
                $single_row['fiscal_year_id'] = $this->request->data['SaleTargetMonth']['fiscal_year_id'];
                $data_row = array();
                $exist_month_data[0]['SaleTargetMonth']['id'] = 0;
                foreach ($val as $month_key => $month_val) {
                    $unique_sale_target_id = $this->request->data['SaleTargetMonth']['sale_target_id'][$key][$month_key];
                    $this->SaleTargetMonth->recursive = -1;
                    $exist_month_data = $this->SaleTargetMonth->find('all', array(
                        'conditions' => array(
                            'SaleTargetMonth.fiscal_year_id' => $fiscal_year_id,
                            'SaleTargetMonth.aso_id' => $aso_id,
                            'SaleTargetMonth.sale_target_id' => $unique_sale_target_id,
                            'SaleTargetMonth.month_id' => $month_key,
                        )
                    ));
                    if ($exist_month_data[0]['SaleTargetMonth']['id'] != 0) {
                        $this->SaleTargetMonth->id = $exist_month_data[0]['SaleTargetMonth']['id'];
                        $single_row['created_at'] = $created_at;
                        $single_row['updated_at'] = $created_at;
                        $single_row['month_id'] = $month_key;
                        $single_row['outlet_coverage'] = $month_val;
                        $single_row['sale_target_id'] = $unique_sale_target_id;
                        $updated_data['SaleTargetMonth'] = $single_row;
                        $this->SaleTargetMonth->save($updated_data);
                    } else {
                        $single_row['month_id'] = $month_key;
                        $single_row['outlet_coverage'] = $month_val;
                        $single_row['sale_target_id'] = $this->request->data['SaleTargetMonth']['sale_target_id'][$key][$month_key];
                        $single_row['created_at'] = $created_at;
                        $single_row['updated_at'] = $created_at;
                        $data_row['SaleTargetMonth'][] = $single_row;
                    }
                }
                if (!empty($data_row['SaleTargetMonth'])) {
                    if ($this->SaleTargetMonth->saveAll($data_row['SaleTargetMonth'])) {
                        $data_row['SaleTargetMonth'] = array();
                    }
                }
            }
            /* ----- end effective call and outlet coverage insert or update ----- */

            /* ---- start show default data ----- */
            /* ---- start create month list ------ */
            $month_list = $this->Month->find('list', array(
                'fields' => array('Month.id', 'Month.name'),
                'conditions' => array('Month.fiscal_year_id' => $fiscal_year_id)
            ));
            $filter_array = array();
            foreach ($month_list as $key => $val) {
                $filter_array[] = array('id' => $key, 'name' => $val);
            }
            /* ----- end create month list ----- */
            $this->Territory->bindModel(
                    array('hasMany' => array(
                            'SaleTarget', 'TerritoryPerson', 'SalesPerson'
                        )
                    )
            );
            /* ----- start Territory infomariont ----- */
            $this->Territory->recursive = 0;
            $saletargets_list = $this->Territory->find('all', array(
                'conditions' => array(
                    'SaleTarget.fiscal_year_id' => $fiscal_year_id,
                    'SaleTarget.aso_id' => $aso_id,
                    'SaleTarget.target_type_id' => 1,
                    'SaleTarget.target_category' => 3,
                ),
                "joins" => array(
                    array(
                        "table" => "sale_targets",
                        "alias" => "SaleTarget",
                        "type" => "INNER",
                        "conditions" => array("territory.id = SaleTarget.territory_id"
                        )
                    ),
                    array(
                        "table" => "territory_people",
                        "alias" => "TerritoryPerson",
                        "type" => "INNER",
                        "conditions" => array("Territory.id = TerritoryPerson.territory_id"
                        )
                    ),
                    array(
                        "table" => "sales_people",
                        "alias" => "SalesPerson",
                        "type" => "INNER",
                        "conditions" => array("TerritoryPerson.sales_person_id = SalesPerson.id"
                        )
                    )
                ),
                'fields' => array('Territory.*', 'SaleTarget.*', 'TerritoryPerson.*', 'SalesPerson.*')
                    )
            );
            /* ------ start make data with monthly effective call ------- */
            $monthly_effective_data = array();
            foreach ($saletargets_list as $saletarget_key => $saletarget_val) {
                $sales_target_id = $saletarget_val['SaleTarget']['id'];
                $this->SaleTargetMonth->recursive = -1;
                $monthly_targets = $this->SaleTargetMonth->find('all', array(
                    'conditions' => array(
                        'SaleTargetMonth.fiscal_year_id' => $fiscal_year_id,
                        'SaleTargetMonth.aso_id' => $aso_id,
                        'SaleTargetMonth.sale_target_id' => $sales_target_id,
                    )
                ));
                /* ---- set monthly data by index month id ---- */
                $territory_id = $saletarget_val['Territory']['id'];
                foreach ($monthly_targets as $month_target_key => $month_target_val) {
                    $territory_id_in_month_target = $month_target_val['SaleTargetMonth']['territory_id'];
                    $unique_month_id = $month_target_val['SaleTargetMonth']['month_id'];
                    if ($territory_id == $territory_id_in_month_target) {
                        $saletargets_list[$saletarget_key]['SaleTargetMonth'][$unique_month_id] = $month_target_val['SaleTargetMonth'];
                    }
                }
            }
            /* ---- end show default data ----- */


            $this->Session->setFlash(__('The Monthly Outlet Coverage has been saved'), 'flash/success');
        }
        $this->set(compact('fiscalYears', 'saleOffice_list', 'month_list', 'saletargets_list', 'filter_array'));
    }

    /* ================= Start monthly outlet coverage ================ */

    public function admin_month_outlet_coverage_view() {
        $this->loadModel('Territory');
        $this->loadModel('Month');
        $this->loadModel('SalesPeople');
        $this->loadModel('SaleTargetMonth');
        $fiscal_year_id = $this->request->data['fiscal_year_id'];
        $aso_id = $this->request->data['aso_id'];
        /* ---- start create month list ------ */
        $month_list = $this->Month->find('list', array(
            'fields' => array('Month.id', 'Month.name'),
            'conditions' => array('Month.fiscal_year_id' => $fiscal_year_id)
        ));
        $filter_array = array();
        foreach ($month_list as $key => $val) {
            $filter_array[] = array('id' => $key, 'name' => $val);
        }
        /* ----- end create month list ----- */
        $this->Territory->bindModel(
                array('hasMany' => array(
                        'SaleTarget', 'TerritoryPerson', 'SalesPerson'
                    )
                )
        );
        /* ----- start Territory infomariont ----- */
        $this->Territory->recursive = 0;
        $saletargets_list = $this->Territory->find('all', array(
            'conditions' => array(
                'SaleTarget.fiscal_year_id' => $fiscal_year_id,
                'SaleTarget.aso_id' => $aso_id,
                'SaleTarget.target_type_id' => 1,
                'SaleTarget.target_category' => 3,
            ),
            "joins" => array(
                array(
                    "table" => "sale_targets",
                    "alias" => "SaleTarget",
                    "type" => "INNER",
                    "conditions" => array("territory.id = SaleTarget.territory_id"
                    )
                ),
                array(
                    "table" => "territory_people",
                    "alias" => "TerritoryPerson",
                    "type" => "INNER",
                    "conditions" => array("Territory.id = TerritoryPerson.territory_id"
                    )
                ),
                array(
                    "table" => "sales_people",
                    "alias" => "SalesPerson",
                    "type" => "INNER",
                    "conditions" => array("TerritoryPerson.sales_person_id = SalesPerson.id"
                    )
                )
            ),
            'fields' => array('Territory.*', 'SaleTarget.*', 'TerritoryPerson.*', 'SalesPerson.*')
                )
        );
        /* ------ start make data with monthly effective call ------- */
        $monthly_effective_data = array();
        foreach ($saletargets_list as $saletarget_key => $saletarget_val) {
            $sales_target_id = $saletarget_val['SaleTarget']['id'];
            $this->SaleTargetMonth->recursive = -1;
            $monthly_targets = $this->SaleTargetMonth->find('all', array(
                'conditions' => array(
                    'SaleTargetMonth.fiscal_year_id' => $fiscal_year_id,
                    'SaleTargetMonth.aso_id' => $aso_id,
                    'SaleTargetMonth.sale_target_id' => $sales_target_id,
                )
            ));
            /* ---- set monthly data by index month id ---- */
            $territory_id = $saletarget_val['Territory']['id'];
            foreach ($monthly_targets as $month_target_key => $month_target_val) {
                $territory_id_in_month_target = $month_target_val['SaleTargetMonth']['territory_id'];
                $unique_month_id = $month_target_val['SaleTargetMonth']['month_id'];
                if ($territory_id == $territory_id_in_month_target) {
                    $saletargets_list[$saletarget_key]['SaleTargetMonth'][$unique_month_id] = $month_target_val['SaleTargetMonth'];
                }
            }
        }
        /* echo '<pre>';
          print_r($saletargets_list); */
        /* ------ end make data with monthly effective call ------- */
        /* ----- end Territory infomariont ----- */
        $this->set(compact('month_list', 'saletargets_list', 'filter_array'));
    }

    public function admin_set_monthly_effective_call_outlet_session($office_id = null, $target_id = null, $fiscal_year_id = null, $territory_id = null) {
        $this->set('page_title', 'Monthly Target for Outlet coverage,Session and Effective Call');
        $this->loadModel('Office');
        $this->loadModel('Month');
        $this->loadModel('SaleTarget');
        $this->loadModel('Territory');
        $this->loadModel('SaleTargetMonth');
        $this->set('fiscal_year_id', $fiscal_year_id);
        $this->set('office_id', $office_id);
        $this->set('territory_id', $territory_id);
        $this->set('sale_target_id', $target_id);
        $this->Territory->recursive = -1;
        $this->Office->recursive = 1;

        //echo '<pre>';
        //print_r($this->request->data);
        //echo '</pre>';
        //die();
        $territoryList = $this->Territory->find('list', array('conditions' => array('Territory.office_id' => $office_id)));
        $saleOffice_list = $this->Office->find('list', array('conditions' => array('Office.office_type_id' => 2)));
        $fiscalYears = $this->SaleTarget->FiscalYear->find('list', array('fields' => array('year_code')));
        /* ---------- start new ---------- */
        $month_list = $this->Month->find('list', array('fields' => array('Month.id', 'Month.name')));

        if ($this->request->is('post')) {
            foreach ($this->request->data['SaleTargetMonth']['outlet_coverage_pharma'] as $key => $val) {
                $single_row = array();
                $single_row['month_id'] = $key;
                $single_row['aso_id'] = $this->request->data['SaleTargetMonth']['aso_id'];
                $single_row['fiscal_year_id'] = $this->request->data['SaleTargetMonth']['fiscal_year_id'];
                $single_row['outlet_coverage_pharma'] = $this->request->data['SaleTargetMonth']['outlet_coverage_pharma'][$key];
                $single_row['outlet_coverage_non_pharma'] = $this->request->data['SaleTargetMonth']['outlet_coverage_non_pharma'][$key];
                $single_row['effective_call_pharma'] = $this->request->data['SaleTargetMonth']['effective_call_pharma'][$key];
                $single_row['effective_call_non_pharma'] = $this->request->data['SaleTargetMonth']['effective_call_non_pharma'][$key];
                $single_row['session'] = $this->request->data['SaleTargetMonth']['session'][$key];
                $single_row['sale_target_id'] = $this->request->data['SaleTargetMonth']['sale_target_id'];
                $single_row['territory_id'] = $this->request->data['SaleTargetMonth']['territory_id'];
                $single_row['target_quantity'] = 0;
                $single_row['target_type'] = 1;

                if ($this->request->data['SaleTargetMonth']['id'][$key] != 0) {
                    $single_row['updated_at'] = $this->current_datetime();
                    $single_row['id'] = $this->request->data['SaleTargetMonth']['id'][$key];
                } else {
                    $single_row['created_at'] = $this->current_datetime();
                    $single_row['updated_at'] = $this->current_datetime();
                }

                $data_row = array();

                $data_row['SaleTargetMonth'] = $single_row;
                if (!empty($data_row['SaleTargetMonth'])) {
                    if ($this->request->data['SaleTargetMonth']['id'][$key] == 0) {
                        $this->SaleTargetMonth->create();
                    }
                    if ($this->SaleTargetMonth->save($data_row['SaleTargetMonth'])) {
                        //$data_row['SaleTargetMonth'] = array();
                        $single_row = array();
                    }
                }
                // die();
            }
            $aso_id = $this->request->data['SaleTargetMonth']['aso_id'];
            $sale_target_id = $this->request->data['SaleTargetMonth']['sale_target_id'];
            $fiscal_year_id = $this->request->data['SaleTargetMonth']['fiscal_year_id'];
            $territory_id = $this->request->data['SaleTargetMonth']['territory_id'];

            $this->Session->setFlash(__('The Monthly Target has been saved'), 'flash/success');
            $this->redirect(array('controller' => 'EffectiveCallsBaseWise', 'action' => "set_monthly_effective_call_outlet_session/$aso_id/$sale_target_id/$fiscal_year_id/$territory_id"));
        }

        $byDefultOutletCoverageEffectiveCallSessionData = $this->SaleTarget->find('all', array(
            'fields' => array('SaleTarget.id', 'SaleTarget.session', 'SaleTarget.effective_call_pharma', 'SaleTarget.effective_call_non_pharma', 'SaleTarget.outlet_coverage_pharma', 'SaleTarget.outlet_coverage_non_pharma'),
            'conditions' => array(
                'SaleTarget.target_category' => 3,
                'SaleTarget.target_type_id' => 1,
                'SaleTarget.territory_id' => isset($this->request->data['SaleTargetMonth']['territory_id']) ? $this->request->data['SaleTargetMonth']['territory_id'] : $territory_id
            )
        ));
        //echo "<pre>";
        //print_r($byDefultOutletCoverageEffectiveCallSessionData);
        //echo "</pre>";
        $getTargetData['SaleTarget'] = isset($byDefultOutletCoverageEffectiveCallSessionData[0]['SaleTarget']) ? $byDefultOutletCoverageEffectiveCallSessionData[0]['SaleTarget'] : 0;
        $this->SaleTargetMonth->recursive = -1;
        $sale_target_month_datas = $this->SaleTargetMonth->find('all', array(
            'fields' => array('SaleTargetMonth.id', 'SaleTargetMonth.month_id', 'SaleTargetMonth.outlet_coverage_pharma', 'SaleTargetMonth.outlet_coverage_pharma', 'SaleTargetMonth.outlet_coverage_non_pharma', 'SaleTargetMonth.effective_call_pharma', 'SaleTargetMonth.effective_call_non_pharma', 'SaleTargetMonth.session'),
            'conditions' => array(
                'SaleTargetMonth.sale_target_id' => isset($this->request->data['SaleTargetMonth']['sale_target_id']) ? $this->request->data['SaleTargetMonth']['sale_target_id'] : $target_id,
                'SaleTargetMonth.target_type' => 1
            )
        ));

        $sale_target_month_data = array();

        foreach ($sale_target_month_datas as $value) {
            $sale_target_month_data[$value['SaleTargetMonth']['month_id']] = $value;
        }

        //pr($sale_target_month_data);

        $this->set('sale_target_month_data', $sale_target_month_data);


        $this->set(compact('fiscalYears', 'product_options', 'saletargets', 'saleOffice_list', 'month_list', 'territoryList', 'getTargetData'));
        // $this->redirect(array('controller' => 'EffectiveCallsBaseWise', 'action' => "set_monthly_effective_call_outlet_session/"));
    }

    public function admin_national_target_effective_call_outlet_coverage_session() {
        if ($this->request->is('post')) {
            // date_default_timezone_set('Asia/Dhaka');
            /* echo "<pre>";
              print_r($this->request->data);
              echo "</pre>"; */ //die();
            if ($this->request->data['SaleTarget']['id'] != '') {
                $receivedData['id'] = $this->request->data['SaleTarget']['id'];
                $receivedData['fiscal_year_id'] = $this->request->data['SaleTarget']['fiscal_year_id'];
                $receivedData['outlet_coverage_pharma'] = $this->request->data['SaleTarget']['outlet_coverage_pharma'];
                $receivedData['outlet_coverage_non_pharma'] = $this->request->data['SaleTarget']['outlet_coverage_non_pharma'];
                $receivedData['session'] = $this->request->data['SaleTarget']['session'];
                $receivedData['effective_call'] = $this->request->data['SaleTarget']['effective_call'];
                $receivedData['updated'] = $this->current_datetime();

                if ($this->SaleTarget->save($receivedData)) {
                    $this->Session->setFlash(__('Data has been updated'), 'flash/success');
                    $this->redirect(array('controller' => 'EffectiveCallsBaseWise', 'action' => 'national_target_effective_call_outlet_coverage_session'));
                }
            } else {
                $receivedData['fiscal_year_id'] = $this->request->data['SaleTarget']['fiscal_year_id'];
                $receivedData['outlet_coverage_pharma'] = $this->request->data['SaleTarget']['outlet_coverage_pharma'];
                $receivedData['outlet_coverage_non_pharma'] = $this->request->data['SaleTarget']['outlet_coverage_non_pharma'];
                $receivedData['session'] = $this->request->data['SaleTarget']['session'];
                $receivedData['effective_call'] = $this->request->data['SaleTarget']['effective_call'];
                $receivedData['created'] = $this->current_datetime();
                $receivedData['updated'] = $this->current_datetime();
                $receivedData['target_type_id'] = 1;
                $receivedData['target_category'] = 1;

                if ($this->SaleTarget->save($receivedData)) {
                    $this->Session->setFlash(__('Data has been saved'), 'flash/success');
                    $this->redirect(array('controller' => 'EffectiveCallsBaseWise', 'action' => 'national_target_effective_call_outlet_coverage_session'));
                }
            }
        }
        $this->set('page_title', 'National target (Effective Call, Outlet Coverage and Session)');
        $fiscalYears = $this->SaleTarget->FiscalYear->find('list', array('fields' => array('year_code')));
        $this->set('fiscalYears', $fiscalYears);
    }

    public function admin_get_national_target_effective_call_outlet_coverage_session() {
        $retrievedData = $this->SaleTarget->find('all', array(
            'fields' => ['id', 'outlet_coverage_pharma', 'outlet_coverage_non_pharma', 'effective_call', 'session'],
            'conditions' => ['target_category' => 1, 'target_type_id' => 1, 'fiscal_year_id' => $this->request->data['fiscalYearId']]
                )
        );
        echo json_encode($retrievedData);
        $this->autoRender = false;
    }

    public function admin_get_effective_call_monthly_data() {
        $this->loadModel('SaleTarget');
        $this->loadModel('SaleTargetMonth');
        $retrievedData = $this->SaleTarget->find('all', array(
            'fields' => ['id', 'territory_id', 'fiscal_year_id', 'aso_id', 'outlet_coverage_pharma', 'outlet_coverage_non_pharma', 'effective_call_pharma', 'effective_call_non_pharma', 'session'],
            'conditions' => ['target_category' => 3, 'target_type_id' => 1, 'fiscal_year_id' => $this->request->data['fiscalYearId'], 'territory_id' => $this->request->data['territoryId'] /* $this->request->data['fiscalYearId'] */]
                )
        );
        if ($retrievedData[0]['SaleTarget']['id'] != 0) {
            $this->SaleTargetMonth->recursive = -1;
            $retrievedSaleTargetMonthData = $this->SaleTargetMonth->find('all', array(
                'fields' => ['id', 'month_id', 'outlet_coverage_pharma', 'outlet_coverage_non_pharma', 'effective_call_pharma', 'effective_call_non_pharma', 'session'],
                'conditions' => ['target_type' => 1, 'sale_target_id' => $retrievedData[0]['SaleTarget']['id'], 'fiscal_year_id' => $this->request->data['fiscalYearId'], 'territory_id' => $this->request->data['territoryId']]
                    )
            );
            $margingData = array_merge($retrievedData, $retrievedSaleTargetMonthData);
        } else {
            $margingData = $retrievedData;
        }

        // echo '<pre>';
        // print_r($retrievedData);
        // echo '</pre>';
        //  echo '<pre>';
        // print_r($margingData);
        // echo '</pre>';
        echo json_encode($margingData);
        $this->autoRender = false;
    }

    public function admin_upload_xl() {
        $this->loadModel('Product');
        $this->loadModel('FiscalYear');
        $this->loadModel('Office');
        $this->loadModel('Territory');
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
                        $territory_id = $this->Territory->find('first', array(
                            'fields' => 'Territory.id',
                            'conditions' => array('lower(Territory.name) like' => '%' . strtolower($val[3]) . '%'),
                            'recursive' => -1
                        ));
                        if ($fiscal_year_id && !$aso_id && !$territory_id) {
                            $this->Session->setFlash(__('The fiscal year or Offie Name or Territory is  missing or incorrect on line ' . $key), 'flash/error');
                            $this->redirect(array("controller" => "EffectiveCallsBaseWise", "action" => "admin_index"));
                        }
                        $saletargets = $this->SaleTarget->find('first', array('conditions' => array('SaleTarget.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'], 'SaleTarget.aso_id' => $aso_id['Office']['id'], 'SaleTarget.territory_id' => $territory_id['Territory']['id'], 'SaleTarget.target_type_id' => 1, 'SaleTarget.target_category' => 3)));
                        if (empty($saletargets)) {
                            $insert_data['SaleTarget']['target_category'] = 3;
                            $insert_data['SaleTarget']['fiscal_year_id'] = $fiscal_year_id['FiscalYear']['id'];
                            $insert_data['SaleTarget']['aso_id'] = $aso_id['Office']['id'];
                            $insert_data['SaleTarget']['territory_id'] = $territory_id['Territory']['id'];
                            $insert_data['SaleTarget']['target_type_id'] = 1;
                            $insert_data['SaleTarget']['outlet_coverage_pharma'] = $val[7];
                            $insert_data['SaleTarget']['outlet_coverage_non_pharma'] = $val[8];
                            $insert_data['SaleTarget']['session'] = $val[6];
                            $insert_data['SaleTarget']['effective_call_pharma'] = $val[4];
                            $insert_data['SaleTarget']['effective_call_non_pharma'] = $val[5];
                            $insert_data_array[] = $insert_data;
                        } else {
                            $updated_data['SaleTarget']['id'] = $saletargets['SaleTarget']['id'];
                            $updated_data['SaleTarget']['outlet_coverage_pharma'] = $val[7];
                            $updated_data['SaleTarget']['outlet_coverage_non_pharma'] = $val[8];
                            $updated_data['SaleTarget']['session'] = $val[6];
                            $updated_data['SaleTarget']['effective_call_pharma'] = $val[4];
                            $updated_data['SaleTarget']['effective_call_non_pharma'] = $val[5];
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
                $this->redirect(array("controller" => " EffectiveCallsBaseWise",
                    "action" => "admin_index"));
            }
        }
    }

    public function admin_upload_xl_month() {
        $this->loadModel('Product');
        $this->loadModel('FiscalYear');
        $this->loadModel('Office');
        $this->loadModel('Territory');
        $this->loadModel('Month');
        $this->loadModel('DiatSaleTargetMonth');
        if (!empty($_FILES["file"]["name"])) {
            $target_dir = WWW_ROOT . 'files/';
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
                            'conditions' => array('lower(Office.office_name) like' => '%' . strtolower($val[3]) . '%'),
                            'recursive' => -1
                        ));
                        $territory_id = $this->Territory->find('first', array(
                            'fields' => 'Territory.id',
                            'conditions' => array('lower(Territory.name) like' => '%' . strtolower($val[4]) . '%'),
                            'recursive' => -1
                        ));
                        $month_id = $this->Month->find('first', array(
                            'fields' => 'Month.id',
                            'conditions' => array('Month.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'], 'lower(Month.name) like' => '%' . strtolower($val[2]) . '%'),
                            'recursive' => -1
                        ));
                        if ($fiscal_year_id && !$aso_id && !$territory_id && !$month_id) {
                            $this->Session->setFlash(__('The fiscal year or Offie Name or Territory or month missing or incorrect on line ' . $key), 'flash/error');
                            $this->redirect(array("controller" => "EffectiveCallsBaseWise", "action" => "admin_index"));
                        }
                        $saletarget_month = $this->SaleTargetMonth->find('first', array('conditions' => array('SaleTargetMonth.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'], 'SaleTargetMonth.aso_id' => $aso_id['Office']['id'], 'SaleTargetMonth.month_id' => $month_id['Month']['id'], 'SaleTargetMonth.territory_id' => $territory_id['Territory']['id'])));
                        if (empty($saletarget_month)) {
                            $saletargets = $this->SaleTarget->find('first', array('conditions' => array('SaleTarget.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'], 'SaleTarget.aso_id' => $aso_id['Office']['id'], 'SaleTarget.territory_id' => $territory_id['Territory']['id'], 'SaleTarget.target_type_id' => 1, 'SaleTarget.target_category' => 3)));
                            if (empty($saletargets)) {
                                $this->Session->setFlash(__('The Effective call and Outlet coverage not set' . $key), 'flash/error');
                                $this->redirect(array("controller" => "EffectiveCallsBaseWise", "action" => "admin_index"));
                            }
                            $insert_data['SaleTargetMonth']['sale_target_id'] = $saletargets['SaleTarget']['id'];
                            $insert_data['SaleTargetMonth']['fiscal_year_id'] = $fiscal_year_id['FiscalYear']['id'];
                            $insert_data['SaleTargetMonth']['month_id'] = $month_id['Month']['id'];
                            ;
                            $insert_data['SaleTargetMonth']['aso_id'] = $aso_id['Office']['id'];
                            $insert_data['SaleTargetMonth']['territory_id'] = $territory_id['Territory']['id'];
                            $insert_data['SaleTargetMonth']['target_type'] = 1;
                            $insert_data['SaleTargetMonth']['outlet_coverage_pharma'] = $val[7];
                            $insert_data['SaleTargetMonth']['outlet_coverage_non_pharma'] = $val[8];
                            $insert_data['SaleTargetMonth']['session'] = $val[9];
                            $insert_data['SaleTargetMonth']['effective_call_pharma'] = $val[5];
                            $insert_data['SaleTargetMonth']['effective_call_non_pharma'] = $val[6];
                            $insert_data['SaleTargetMonth']['target_quantity'] = 0;
                            $insert_data_array[] = $insert_data;
                        } else {
                            $updated_data['SaleTargetMonth']['id'] = $saletarget_month['SaleTargetMonth']['id'];
                            $updated_data['SaleTargetMonth']['target_type'] = 1;
                            $updated_data['SaleTargetMonth']['outlet_coverage_pharma'] = $val[7];
                            $updated_data['SaleTargetMonth']['outlet_coverage_non_pharma'] = $val[8];
                            $updated_data['SaleTargetMonth']['session'] = $val[9];
                            $updated_data['SaleTargetMonth']['effective_call_pharma'] = $val[5];
                            $updated_data['SaleTargetMonth']['effective_call_non_pharma'] = $val[6];
                            $update_data_array[] = $updated_data;
                        }
                    }
                }
                //pr($update_data_array);
                //pr($insert_data_array);
                //exit;
                if ($insert_data_array) {


                    $this->SaleTargetMonth->create();
                    $this->SaleTargetMonth->saveAll($insert_data_array);
                }
                if ($update_data_array) {
                    $this->SaleTargetMonth->saveAll($update_data_array);
                }

                $this->Session->setFlash(__('The Sale Targets has been saved'), 'flash/success');
                $this->redirect(array("controller" => " EffectiveCallsBaseWise",
                    "action" => "admin_index"));
            }
        }
    }

}
