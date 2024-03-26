<?php

App::uses('AppController', 'Controller');
App::import('Vendor', 'php-excel-reader/excel_reader2');

ini_set('max_execution_time', 99999);
ini_set('memory_limit', '256M');
/**
 * Sale Targets Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class SaleTargetsBaseWiseController extends AppController {


    public $uses = array('SaleTarget', 'Product', 'Office', 'Territory', 'SaleTargetMonth','FiscalYear');

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
//            echo "<pre>";
//            print_r($this->request->data);
//            exit;
            $saletarget = $this->SaleTarget->find('first', array('fields' => array('quantity', 'amount'),
                'conditions' => array(
                    'AND' => array(
                        array(
                            'SaleTarget.fiscal_year_id' => $this->request->data['SaleTarget']['fiscal_year_id'],
                            'SaleTarget.product_id' => $this->request->data['SaleTarget']['product_id'],
                            'SaleTarget.target_category' => 3,
                            'SaleTarget.target_type_id' => 0,
                            'SaleTarget.aso_id' => $this->request->data['SaleTarget']['aso_id'],
                            )
                        ))));

            if (empty($saletarget)) {
                $this->SaleTarget->create();
                if (!empty($this->request->data['SaleTarget'])) {
                    $data_array = array();
                    foreach ($this->request->data['SaleTarget']['quantity'] as $key => $val) {
                        $data['SaleTarget']['product_id'] = $this->request->data['SaleTarget']['product_id'];
                        $data['SaleTarget']['target_category'] = 3;
                        $data['SaleTarget']['fiscal_year_id'] = $this->request->data['SaleTarget']['fiscal_year_id'];
                        $data['SaleTarget']['aso_id'] = $this->request->data['SaleTarget']['aso_id'];
                        $data['SaleTarget']['territory_id'] = $this->request->data['SaleTarget']['Territory_id'][$key];
                        $data['SaleTarget']['amount'] = $this->request->data['SaleTarget']['amount'][$key];
                        $data['SaleTarget']['quantity'] = $val;
                        $data_array[] = $data;
                    }
                    $this->SaleTarget->saveAll($data_array);
                }
            } else {
                if (!empty($this->request->data['SaleTarget'])) {
                    $data_array = array();
                    foreach ($this->request->data['SaleTarget']['quantity'] as $key => $val) {
                        $exiting_data = $this->SaleTarget->find('all', array(
                            'conditions' => array(
                                'SaleTarget.fiscal_year_id' => $this->request->data['SaleTarget']['fiscal_year_id'],
                                'SaleTarget.product_id' => $this->request->data['SaleTarget']['product_id'],
                                'SaleTarget.target_category' => 3,
                                'SaleTarget.territory_id' => $key,
                                'SaleTarget.target_type_id' => 0,
                                ),
                            'fields' => array('saletarget.id')
                            ));
                        if (!empty($exiting_data)) {
                            $data['SaleTarget']['id'] = $exiting_data[0]['saletarget']['id'];
                            $data['SaleTarget']['target_category'] = 3;
                            $data['SaleTarget']['fiscal_year_id'] = $this->request->data['SaleTarget']['fiscal_year_id'];
                            $data['SaleTarget']['amount'] = $this->request->data['SaleTarget']['amount'][$key];
                            $data['SaleTarget']['quantity'] = $val;
                            $this->SaleTarget->save($data);
                        }
                    }
                    $this->SaleTarget->saveAll($data_array);
                }
            }
            $this->Session->setFlash(__('The Sale Targets has been saved'), 'flash/success');
            $saletargets_list = $this->SaleTarget->find('all', array(
                'conditions' => array(
                    'AND' => array(
                        array(
                            'SaleTarget.fiscal_year_id' => $this->request->data['SaleTarget']['fiscal_year_id'],
                            'SaleTarget.product_id' => $this->request->data['SaleTarget']['product_id'],
                            'SaleTarget.target_category' => 3,
                            'SaleTarget.target_type_id' => 0,
                            'SaleTarget.aso_id' => $this->request->data['SaleTarget']['aso_id'],
                            )
                        ))));
            $saletarget = $this->SaleTarget->find('first', array('fields' => array('quantity', 'amount'),
                'conditions' => array(
                    'AND' => array(
                        array(
                            'SaleTarget.fiscal_year_id' => $this->request->data['SaleTarget']['fiscal_year_id'],
                            'SaleTarget.product_id' => $this->request->data['SaleTarget']['product_id'],
                            'SaleTarget.target_category' => 2,
                            'SaleTarget.target_type_id' => 0,
                            'SaleTarget.aso_id' => $this->request->data['SaleTarget']['aso_id'],
                            )
                        ))));
            $this->loadModel('SalesPerson');
            $so_name_list = $this->SalesPerson->find('list', array(
                'conditions'=>array('SalesPerson.office_id'=>$this->request->data['SaleTarget']['aso_id']),
                'fields'=>array('territory_id', 'name')
                ));
        }
        $this->set('page_title', 'National Sale Target Base Wise List');
        $this->SaleTarget->recursive = 0;
        $product_options = $this->Product->find('list',array('conditions'=>array('Product.product_type_id'=>'1'), 'order' => array('Product.order' => 'ASC')));
        $this->Office->recursive = 1;
        $saleOffice_list = $this->Office->find('list', array('conditions' => array('Office.office_type_id' => 2)));
        $fiscalYears = $this->SaleTarget->FiscalYear->find('list', array('fields' => array('year_code')));
        $this->set(compact('fiscalYears', 'product_options', 'saletarget', 'saleOffice_list', 'saletargets_list', 'so_name_list'));
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_set_monthly_target($office_id = null, $product_id = null, $target_id = null, $fiscal_year_id = null, $territory_id = null) {
        $this->loadModel('Territory');
        $this->loadModel('Month');
        $this->loadModel('SaleTargetMonth');
        $this->loadModel('SalesPeople');
        $this->loadModel('TerritoryPerson');
        $this->loadModel('SaleTarget');
        $this->loadModel('Office');
        $this->set('page_title', 'Monthly Sale Target');
        $this->SaleTarget->recursive = 0;
        $this->Territory->recursive = 1;
        $this->SaleTargetMonth->recursive = 2;

        $product_options = $this->Product->find('list', array('order' => array('Product.order' => 'ASC')));
        //$saletargets = $this->SaleTarget->find('first');
        $territory = $this->Territory->find('first');
        //$saleTargetMonth = $this->SaleTargetMonth->find('all');
        //$office = $this->Office->find('list');
        $this->set('product_id', $product_id);
        $this->set('fiscal_year_id', $fiscal_year_id);
        $this->set('area_office_id', $territory['Office']['id']);
        $territory_quantity=(isset($territory['SaleTarget'][0]['quantity']))?$territory['SaleTarget'][0]['quantity']:0;
        $territory_amount=(isset($territory['SaleTarget'][0]['amount']))?$territory['SaleTarget'][0]['amount']:0;
        $this->set('quantity', $territory_quantity);
        $this->set('amount', $territory_amount);
        $this->set('territory_id', $territory_id);
        $this->set('sale_target_id', $target_id);
        $this->set('office_id', $office_id);



        $this->Office->recursive = -1;
        // $saleOffice_list = $this->Office->find('list', array('conditions' => array('Office.office_type_id' => 2)));
        $territories=$this->Territory->find('list',array('conditions'=>array('Territory.office_id'=>$this->Session->read('Office.id'))));
       
        //pr($territories);
        $fiscalYears = $this->SaleTarget->FiscalYear->find('list', array('fields' => array('year_code')));
        /* ---------- start new ---------- */
        $month_list = $this->Month->find('list', array('fields' => array('Month.id', 'Month.name')));
        $this->set(compact('fiscalYears', 'product_options', 'saletargets', 'territories', 'month_list'));


//         echo '<pre>';
//         print_r($this->request->data);
//         echo '</pre>';
//        die();
        if ($this->request->is('post') || $this->request->is('put')) {
            //$this->dd($this->request->data);
            $territory_quantity=$this->request->data['SaleTargetMonth']['t_quantity'];
            $territory_amount=$this->request->data['SaleTargetMonth']['t_amount'];
            $product_id=$this->request->data['SaleTargetMonth']['product_id'];
            $fiscal_year_id= $this->request->data['SaleTargetMonth']['fiscal_year_id'];
            $target_id=$this->request->data['SaleTargetMonth']['sale_target_id'];
            $office_id=$this->request->data['SaleTargetMonth']['aso_id'];
            $territory_id=$this->request->data['SaleTargetMonth']['territory_id'];
            //echo $product_id.' - '.$fiscal_year_id.' - '.$target_id.' - '.$office_id.' - '.$territory_id;die();
            foreach ($this->request->data['SaleTargetMonth']['quantity'] as $key => $val) {
                //$amount_list = $this->request->data['SaleTargetMonth']['amount'][$key];
                //echo $key . '     >>>>>>>>>>>  ';
                $single_row = array();
                $single_row['month_id'] = $key;

                $single_row['target_quantity'] = $val;
                $single_row['target_amount'] = $this->request->data['SaleTargetMonth']['amount'][$key];

                
                $single_row['aso_id'] = $this->request->data['SaleTargetMonth']['aso_id'];
                $single_row['fiscal_year_id'] = $this->request->data['SaleTargetMonth']['fiscal_year_id'];
                $single_row['product_id'] = $this->request->data['SaleTargetMonth']['product_id'];
                $single_row['sale_target_id'] = $this->request->data['SaleTargetMonth']['sale_target_id'];
                $single_row['territory_id'] = $this->request->data['SaleTargetMonth']['territory_id'];
                $single_row['target_type'] = 0;
                $single_row['session'] = 0;
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
            /* ------------ start default set monthly data ---------- */

            $this->set('product_id', $product_id);
            $this->set('fiscal_year_id', $fiscal_year_id);
            $this->set('quantity', $territory_quantity);
            $this->set('amount', $territory_amount);
            $this->set('territory_id', $territory_id);
            $this->set('sale_target_id', $target_id);
            $this->set('office_id', $office_id);


            /* ------------ end default set monthly data ---------- */
            $this->Session->setFlash(__('The Monthly Target has been saved'), 'flash/success');
        }

        $this->SaleTargetMonth->recursive = -1;
        $sale_target_month_data = $this->SaleTargetMonth->find('all', array(
            'fields' => array('SaleTargetMonth.id', 'SaleTargetMonth.target_quantity', 'SaleTargetMonth.target_amount', 'SaleTargetMonth.month_id'),
            'conditions' => array(
                'SaleTargetMonth.sale_target_id' => $target_id,
                'SaleTargetMonth.target_type' => 0,
                )
            ));

        $sale_target = $this->SaleTarget->find('all', array(
            'fields' => array('SaleTarget.quantity', 'SaleTarget.amount'),
            'conditions' => array(
                'SaleTarget.id' => $target_id,
                'SaleTarget.product_id' => $product_id,
                'SaleTarget.aso_id' => $office_id,
                'SaleTarget.territory_id' => $territory_id,
                'SaleTarget.fiscal_year_id' => $fiscal_year_id
                )
            ));

        $new_manupulated_array = array();
        $new_manupulated_array['SaleTarget'] = (isset($sale_target[0]['SaleTarget']) ? $sale_target[0]['SaleTarget'] : 0);
        foreach ($sale_target_month_data as $key => $val) {
            $new_manupulated_array[$val['SaleTargetMonth']['month_id']] = $val;
        }
        $this->set('sale_target_month_data', $new_manupulated_array);
    }
 
    public function admin_month_target_view() {
        $this->loadModel('Territory');
        $this->loadModel('Month');
        $this->loadModel('SalesPeople');
        $this->loadModel('SaleTargetMonth');
        $month_list = $this->Month->find('list', array(
            'fields' => array('Month.id', 'Month.name'),
            'conditions' => array('Month.fiscal_year_id' => $this->request->data('FiscalYearId'))
            ));
        $filter_array = array();
        foreach ($month_list as $key => $val) {
            $filter_array[] = array('id' => $key, 'name' => $val);
        }
        /* echo "<pre>";
          print_r($filter_array);
          exit; */
          /* -------- start territory list with saletarget --------- */
          $this->Territory->bindModel(
            array('hasMany' => array(
                'SaleTarget', 'TerritoryPerson', 'SalesPerson'
                )
            )
            );
        //pr($this->request->data);
          $this->Territory->recursive = -1;
          $saletargets_list = $this->Territory->find('all', array(
            'conditions' => array(
                'SaleTarget.fiscal_year_id' => $this->request->data('FiscalYearId'),
                'SaleTarget.product_id' => $this->request->data('ProductId'),
                'SaleTarget.aso_id' => $this->request->data('SaleTargetAsoId'),
                'Territory.id'=>$this->request->data('territory_id'),
                'SaleTarget.target_category' => 3,
                'SaleTarget.target_type_id' => 0,
                ),
            "joins" => array(
                array(
                    "table" => "sale_targets",
                    "alias" => "SaleTarget",
                    "type" => "INNER",
                    "conditions" => array("territory.id = SaleTarget.territory_id"
                        )
                    ),
                /*array(
                    "table" => "territory_people",
                    "alias" => "TerritoryPerson",
                    "type" => "left",
                    "conditions" => array("Territory.id = TerritoryPerson.territory_id"
                    )
                    ),*/
                    array(
                        "table" => "sales_people",
                        "alias" => "SalesPerson",
                        "type" => "INNER",
                        "conditions" => array("Territory.id = SalesPerson.territory_id"
                            )
                        )
                    ),
            'fields' => array('Territory.*', 'SaleTarget.*',/* 'TerritoryPerson.*', */'SalesPerson.*')
            )
          );
        //pr($saletargets_list);
          $this->SaleTargetMonth->recursive = -1;
          $monthly_targets = $this->SaleTargetMonth->find('all', array(
            'conditions' => array(
                'SaleTargetMonth.fiscal_year_id' => $this->request->data('FiscalYearId'),
                'SaleTargetMonth.product_id' => $this->request->data('ProductId'),
                'SaleTargetMonth.aso_id' => $this->request->data('SaleTargetAsoId'),
                'SaleTargetMonth.territory_id'=>$this->request->data('territory_id'),
                )
            ));
        //pr($monthly_targets);
          if (!empty($monthly_targets)) {
            foreach ($saletargets_list as $saletarget_key => $saletarget_val) {
                $territory_id = $saletarget_val['Territory']['id'];
                foreach ($monthly_targets as $month_target_key => $month_target_val) {
                    $territory_id_in_month_target = $month_target_val['SaleTargetMonth']['territory_id'];
                    $unique_month_id = $month_target_val['SaleTargetMonth']['month_id'];
                    if ($territory_id == $territory_id_in_month_target) {
                        $saletargets_list[$saletarget_key]['SaleTargetMonth'][$unique_month_id] = $month_target_val['SaleTargetMonth'];
                    }
                }
            }
        }

        /* ---------- end monthly target data --------- */
        $this->set(compact('saletargets_list', 'filter_array', 'month_list'));
        /* -------- end territory list with saletarget --------- */
    }

    public function admin_get_sales_target_base_wise_data() 
    {
        $this->Territory->recursive = 1;
        $this->Territory->unbindModel(
            array('hasMany' => array('SaleTarget')));

        $this->Territory->bindModel(
            array('hasMany' => array(
                'SaleTarget' => array(
                    'className' => 'SaleTarget',
                    'foreignKey' => 'territory_id',
                    'conditions' => array('SaleTarget.product_id' => $this->request->data('ProductId'),
                        'SaleTarget.fiscal_year_id' => $this->request->data('FiscalYearId'),
                        'SaleTarget.aso_id' => $this->request->data('SaleTargetAsoId')
                        ),
                    )
                )
            )
            );

        $saletargets_list = $this->Territory->find('all', array(
            'conditions' => array(
                'AND' => array(
                    array(
                        'Territory.office_id' => $this->request->data('SaleTargetAsoId'),
                        )
                    )),
		    /*'conditions' => array(
                'AND' => array(
                    array(
                        //'SaleTarget.fiscal_year_id' => $this->request->data('FiscalYearId'),
                        //'SaleTarget.product_id' => $this->request->data('ProductId'),
                        'SaleTarget.aso_id' => $this->request->data('SaleTargetAsoId'),
                    )
                )),
            "joins" => array(
                array(
                    "table" => "sale_targets",
                    "alias" => "SaleTarget",
                    "type" => "INNER",
                    "conditions" => array(
                        "territory.id = SaleTarget.territory_id"
                    )
                )
                )*/
                ));
        $this->SaleTarget->recursive = -1;
        $saletarget = $this->SaleTarget->find('first', array(
            'conditions' => array(
                'AND' => array(
                    array(
                        'SaleTarget.fiscal_year_id' => $this->request->data('FiscalYearId'),
                        'SaleTarget.product_id' => $this->request->data('ProductId'),
                        'SaleTarget.target_category' => 2,
                        'SaleTarget.aso_id' => $this->request->data('SaleTargetAsoId'),
                        )
                    ))));
        
		//pr($saletargets_list);
		//exit;

        if (empty($saletargets_list)) {
            $saletargets_empty = $this->Territory->find('all', array('conditions' => array('Office.id' => $this->request->data('SaleTargetAsoId'))));
            foreach ($saletargets_empty as $key => $value) {
                $this->loadModel('SalesPerson');
                $so_name[$key] = $this->SalesPerson->find('first', array(
                    'conditions'=>array('SalesPerson.territory_id'=>$saletargets_empty[$key]['Territory']['id']),
                    'fields'=>array('name')
                    ));

            }
            $this->set(compact('saletargets_empty', 'saletarget'));
        } else {

            $this->set(compact('saletargets_list', 'saletarget'));
        }
    }

    public function admin_sales_base_wise_data() {
        $this->loadModel('Territory');
        $this->SaleTarget->recursive = -1;
        $saletarget = $this->SaleTarget->find('first', array('fields' => array('quantity', 'amount'),
            'conditions' => array(
                'AND' => array(
                    array(
                        'SaleTarget.fiscal_year_id' => $this->request->data('FiscalYearId'),
                        'SaleTarget.product_id' => $this->request->data('ProductId'),
                        'SaleTarget.target_category' => 2,
                        'SaleTarget.aso_id' => $this->request->data('SaleTargetAsoId'),
                        )
                    ))));

        if (empty($saletarget['SaleTarget'])) {
            $saletarget['SaleTarget']['quantity'] = 0;
            $saletarget['SaleTarget']['amount'] = 0;
        }

        $this->Territory->unbindModel(
            array('hasMany' => array('SaleTarget'))
            );
        $this->Territory->recursive = 0;
        $saletargets_list = $this->Territory->find('all', array(
            'conditions' => array(
                'AND' => array(
                    array(
                        'SaleTarget.fiscal_year_id' => $this->request->data['FiscalYearId'],
                        'SaleTarget.product_id' => $this->request->data('ProductId'),
                        'SaleTarget.aso_id' => $this->request->data('SaleTargetAsoId')
                        )
                    )),
            "joins" => array(
                array(
                    "table" => "sale_targets",
                    "alias" => "SaleTarget",
                    "type" => "INNER",
                    "conditions" => array(
                        "territory.id = SaleTarget.territory_id"
                        )
                    )
                ),
            'fields' => array('Territory.*', 'SaleTarget.*')));
        /* ---- start making assigned qty and ammount ---- */
        $total_qty = 0;
        $total_ammount = 0;
        foreach ($saletargets_list as $val) {
            $total_qty = $total_qty + $val['SaleTarget']['quantity'];
            $total_ammount = $total_ammount + $val['SaleTarget']['amount'];
        }
        if ($total_qty > 0) {
            $qty_and_ammount['qty'] = $total_qty;
        } else {
            $qty_and_ammount['qty'] = 0;
        }
        if ($total_ammount > 0) {
            $qty_and_ammount['ammount'] = $total_ammount;
        } else {
            $qty_and_ammount['ammount'] = 0;
        }
        /* ---- end making assigned qty and ammount ---- */
        $saletarget['qty_and_ammount'] = $qty_and_ammount;

        echo json_encode($saletarget);

        $this->autoRender = false;
    }

    public function admin_get_total_area_targets_data() {
        $fiscal_year_id = $this->request->data['fiscal_year_id'];
        //$fiscal_year_id = 17;
        $product_id = $this->request->data['product_id'];
        //$product_id = 1;
        $aso_id = $this->request->data['aso_id'];
        //$aso_id = 2;
        $territory_id=$this->request->data['territory_id'];
        $this->Territory->bindModel(
            array('hasMany' => array(
                'SaleTarget', 'TerritoryPerson', 'SalesPerson'
                )
            )
            );
        $this->Territory->recursive = 0;
        $saletargets_list = $this->Territory->find('all', array(
            'conditions' => array(
                'SaleTarget.fiscal_year_id' => $fiscal_year_id,
                'SaleTarget.product_id' => $product_id,
                'SaleTarget.aso_id' => $aso_id,
                'SaleTarget.territory_id' => $territory_id,

                ),
            "joins" => array(
                array(
                    "table" => "sale_targets",
                    "alias" => "SaleTarget",
                    "type" => "INNER",
                    "conditions" => array("territory.id = SaleTarget.territory_id"
                        )
                    )
                ),
            'fields' => array('SaleTarget.quantity', 'SaleTarget.amount')
            )
        );
        //pr($saletargets_list);
        $total_qty = 0;
        $total_amount = 0;
        $data = array();
        if (!empty($saletargets_list)) {
            foreach ($saletargets_list as $val) {
                $total_qty = $total_qty + $val['SaleTarget']['quantity'];
                $total_amount = $total_amount + $val['SaleTarget']['amount'];
            }
        } else {
            $total_qty = 0;
            $total_amount = 0;
        }
        $data['qty'] = $total_qty;
        $data['amount'] = $total_amount;
        if ($total_qty != 0 || $total_amount != 0) {
            echo json_encode($data);
        } else {
            $data['qty'] = '';
            $data['amount'] = '';
            echo json_encode($data);
        }
        $this->autoRender = false;
    }

    public function admin_upload_xl()
    {
        $this->loadModel('Product');
        $this->loadModel('FiscalYear');
        $this->loadModel('Office');
        $this->loadModel('Territory');
        
        if(!empty($_FILES["file"]["name"]))
        {
            $target_dir = WWW_ROOT.'files/';;
            $target_file = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 30);
            $uploadOk = 1;
            $imageFileType = pathinfo($_FILES["file"]["name"],PATHINFO_EXTENSION);
            
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir.$target_file.'.'.$imageFileType)) 
            {
                $data_ex = new Spreadsheet_Excel_Reader($target_dir.$target_file.'.'.$imageFileType, true);
                $temp = $data_ex->dumptoarray();
                $this->SaleTarget->recursive = -1;
                $insert_data_array = array();
                $update_data_array = array();

				// pr($temp);
				// exit;

                foreach ($temp as $key => $val) 
                {
                 if($key>1 && !empty($val[1]) && !empty($val[2]))
                 {

                    $fiscal_year_id = $this->FiscalYear->find('first',array(
                        'fields'=>array('FiscalYear.id'),
                        'conditions'=>array('FiscalYear.year_code LIKE'=>'%'.trim($val[1].'%')),
                        'recursive'=>-1
                        ));
                    $product_id =  $this->Product->find('first',array(
                        'fields'=>'Product.id',
                        'conditions'=>array('lower(Product.name) like'=>'%'.strtolower(html_entity_decode($val[4])).'%'),
                        'recursive'=>-1
                        ));
                    $aso_id = $this->Office->find('first',array(
                        'fields'=>'Office.id',
                        'conditions'=>array('lower(Office.office_name) like'=>'%'.strtolower($val[2]).'%'),
                        'recursive'=>-1
                        ));
                    $territory_id= $this->Territory->find('first',array(
                        'fields'=>'Territory.id',
                        'conditions'=>array('lower(Territory.name) like'=>'%'.strtolower(html_entity_decode($val[3])).'%'),
                        'recursive'=>-1
                        ));
                    if(!$product_id && !$fiscal_year_id && !$aso_id && !$territory_id)
                    {
                        $this->Session->setFlash(__('The Product Name or fiscal year or Offie Name or Territory missing or incorrect on line '.$key), 'flash/error');
                        $this->redirect(array("controller" => "SaleTargetsBaseWise","action" => "admin_index"));
                    }

                    if(!isset($chk_product_sum['qty'][$product_id['Product']['id']]))
                    {
                        $chk_product_sum['qty'][$product_id['Product']['id']]=0;
                    }
                    if(!isset($chk_product_sum['amount'][$product_id['Product']['id']]))
                    {
                        $chk_product_sum['amount'][$product_id['Product']['id']]=0;
                    }

                    $chk_product_sum['qty'][$product_id['Product']['id']]+=$val[5];
                    $chk_product_sum['amount'][$product_id['Product']['id']]+=$val[6];

                    $saletargets = $this->SaleTarget->find('first', array('conditions' => array('SaleTarget.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'],'SaleTarget.aso_id' => $aso_id['Office']['id'],'SaleTarget.territory_id' => $territory_id['Territory']['id'], 'SaleTarget.product_id' => $product_id['Product']['id'],'SaleTarget.target_type_id' => 0, 'SaleTarget.target_category' => 3)));

                    if(empty($saletargets))
                    {	
                       $insert_data['SaleTarget']['product_id'] = $product_id['Product']['id'];
                       $insert_data['SaleTarget']['target_category'] = 3;
                       $insert_data['SaleTarget']['fiscal_year_id'] = $fiscal_year_id['FiscalYear']['id'];

                       $insert_data['SaleTarget']['quantity'] = $val[5];
                       $insert_data['SaleTarget']['amount'] = $val[6];

                       $insert_data['SaleTarget']['aso_id'] = $aso_id['Office']['id'];
                       $insert_data['SaleTarget']['territory_id'] = $territory_id['Territory']['id'];
                       $insert_data_array[] = $insert_data;
                   }
                   else 
                   {
                       $updated_data['SaleTarget']['id'] = $saletargets['SaleTarget']['id'];

                       $updated_data['SaleTarget']['quantity'] = $val[5];
                       $updated_data['SaleTarget']['amount'] = $val[6];

                       $update_data_array[] = $updated_data;
                   }
               }
           }


				// pr($insert_data_array);
				// exit;
           $is_error=0;
           $error_msg='';
           $fiscal_year_chk=$fiscal_year_id['FiscalYear']['id'];
           $office_id = $aso_id['Office']['id'];
           foreach($chk_product_sum['qty'] as $key=>$val)
           {
            $sale_target_area=$this->SaleTarget->find('first',array(
                'conditions'=>array(
                    'SaleTarget.product_id'=>$key,
                    'SaleTarget.fiscal_year_id'=>$fiscal_year_chk,
                    'SaleTarget.aso_id'=>$office_id,
                    'SaleTarget.target_category'=>2
                    ),
                'recursive'=>-1
                ));
            
            // pr($sale_target_base_wise);die;
            $product_name = $this->Product->find('first',array(
                'fields'=>'Product.name',
                'conditions'=>array('Product.id'=>$key),
                'recursive'=>-1
                ));
            if($sale_target_area['SaleTarget']['quantity'] < $val)
            {
                $is_error=1;
                $error_msg .="Target Quantity is gretter than national Qty for product ".$product_name['Product']['name']."<br>";
            }

            if($sale_target_area['SaleTarget']['amount'] < $chk_product_sum['amount'][$key])
            {
                $is_error=1;
                $error_msg .="Target Amount is gretter than National Amount for product ".$product_name['Product']['name']."<br>";
            }
        }

        if($is_error==0)
        {
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
       $this->redirect(array("controller" => "SaleTargetsBaseWise", 
          "action" => "admin_index"));
   }
   else
   {
    $this->Session->setFlash(__($error_msg), 'flash/error');
    $this->redirect(array("controller" => "SaleTargetsBaseWise", 
        "action" => "admin_index"));
}


}
}
}

public function admin_upload_xl_month()
{

    $this->loadModel('Product');
    $this->loadModel('FiscalYear');
    $this->loadModel('Office');
    $this->loadModel('Territory');
    $this->loadModel('Month');

    if(!empty($_FILES["file"]["name"]))
    {
        $target_dir = WWW_ROOT.'files/';;
        $target_file = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 30);
        $uploadOk = 1;
        $imageFileType = pathinfo($_FILES["file"]["name"],PATHINFO_EXTENSION);

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir.$target_file.'.'.$imageFileType)) 
        {
            $data_ex = new Spreadsheet_Excel_Reader($target_dir.$target_file.'.'.$imageFileType, true);
            $temp = $data_ex->dumptoarray();
            $this->SaleTargetMonth->recursive = -1;
            $this->SaleTarget->recursive = -1;
            $insert_data_array = array();
            $update_data_array = array();


				//pr($temp);
				//exit;
            
            foreach ($temp as $key => $val) 
            {
             if($key >1 && !empty($val[1]) && !empty($val[2]))
             {
                $fiscal_year_id = $this->FiscalYear->find('first',array(
                    'fields'=>array('FiscalYear.id'),
                    'conditions'=>array('FiscalYear.year_code LIKE'=>'%'.trim($val[1].'%')),
                    'recursive'=>-1
                    ));
                $product_id =  $this->Product->find('first',array(
                    'fields'=>'Product.id',
                    'conditions'=>array('lower(Product.name) like'=>'%'.strtolower(html_entity_decode($val[5])).'%'),
                    'recursive'=>-1
                    ));
                $aso_id = $this->Office->find('first',array(
                    'fields'=>'Office.id',
                    'conditions'=>array('lower(Office.office_name) like'=>'%'.strtolower($val[3]).'%'),
                    'recursive'=>-1
                    ));
                $territory_id= $this->Territory->find('first',array(
                    'fields'=>'Territory.id',
                    'conditions'=>array('lower(Territory.name) like'=>'%'.strtolower(html_entity_decode($val[4])).'%'),
                    'recursive'=>-1
                    ));
                $month_id = $this->Month->find('first',array(
                    'fields'=>'Month.id',
                    'conditions'=>array(/* 'Month.fiscal_year_id'=>$fiscal_year_id['FiscalYear']['id'], */'lower(Month.name) like'=>'%'.strtolower($val[2]).'%'),
                    'recursive'=>-1
                    ));
                if(!$product_id && !$fiscal_year_id && !$aso_id && !$territory_id && !$month_id)
                {
                    $this->Session->setFlash(__('The Product Name or fiscal year or Offie Name or Territory or month missing or incorrect on line '.$key), 'flash/error');
                    $this->redirect(array("controller" => "SaleTargetsBaseWise","action" => "admin_index"));
                }
                $saletargets = $this->SaleTarget->find('first', array('conditions' => array('SaleTarget.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'],'SaleTarget.aso_id' => $aso_id['Office']['id'],'SaleTarget.territory_id' => $territory_id['Territory']['id'], 'SaleTarget.product_id' => $product_id['Product']['id'],'SaleTarget.target_type_id' => 0, 'SaleTarget.target_category' => 3)));
                if(empty($saletargets))
                {
                    $this->Session->setFlash(__('The Sale Target Base wise Not Set'), 'flash/error');
                    $this->redirect(array("controller" => "SaleTargetsBaseWise","action" => "admin_index"));
                }
                $saletarget_month=$this->SaleTargetMonth->find('first',array(
                    'conditions'=>array(
                        'SaleTargetMonth.sale_target_id'=>$saletargets['SaleTarget']['id'],
                        'SaleTargetMonth.product_id' => $product_id['Product']['id'],
                        'SaleTargetMonth.month_id' => $month_id['Month']['id'],
                        'SaleTargetMonth.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'],
                        'SaleTargetMonth.aso_id' => $aso_id['Office']['id'],
                        'SaleTargetMonth.territory_id' => $territory_id['Territory']['id'],
                        )
                    ));
                if(!isset($chk_product_sum['qty'][$product_id['Product']['id']][$territory_id['Territory']['id']]))
                {
                    $chk_product_sum['qty'][$product_id['Product']['id']][$territory_id['Territory']['id']]=0;
                }
                if(!isset($chk_product_sum['amount'][$product_id['Product']['id']][$territory_id['Territory']['id']]))
                {
                    $chk_product_sum['amount'][$product_id['Product']['id']][$territory_id['Territory']['id']]=0;
                }

                $chk_product_sum['qty'][$product_id['Product']['id']][$territory_id['Territory']['id']]+=$val[6];
                $chk_product_sum['amount'][$product_id['Product']['id']][$territory_id['Territory']['id']]+=$val[7];

                if(empty($saletarget_month))
                {

                    $insert_data['SaleTargetMonth']['sale_target_id'] = $saletargets['SaleTarget']['id'];
                    $insert_data['SaleTargetMonth']['product_id'] = $product_id['Product']['id'];
                    $insert_data['SaleTargetMonth']['month_id'] = $month_id['Month']['id'];
                    $insert_data['SaleTargetMonth']['fiscal_year_id'] = $fiscal_year_id['FiscalYear']['id'];

                    $insert_data['SaleTargetMonth']['target_quantity'] = $val[6];
                    $insert_data['SaleTargetMonth']['target_amount'] = $val[7];

                    $insert_data['SaleTargetMonth']['aso_id'] = $aso_id['Office']['id'];
                    $insert_data['SaleTargetMonth']['territory_id'] = $territory_id['Territory']['id'];
                    $insert_data['SaleTargetMonth']['target_type'] = 0;
                    $insert_data['SaleTargetMonth']['session'] = 0;
                    $insert_data_array[]= $insert_data;
                }
                else 
                {
                   $updated_data['SaleTargetMonth']['id'] = $saletarget_month['SaleTargetMonth']['id'];

                   $updated_data['SaleTargetMonth']['target_quantity'] = $val[6];
                   $updated_data['SaleTargetMonth']['target_amount'] = $val[7];

                   $update_data_array[] = $updated_data;
               }
           }
       }

				/*pr($chk_product_sum);
				exit;*/

                $is_error=0;
                $error_msg='';
                $fiscal_year_chk=$fiscal_year_id['FiscalYear']['id'];
                $office_id = $aso_id['Office']['id'];
                foreach($chk_product_sum['qty'] as $key_p=>$val_p)
                {
                    foreach($val_p as $key_t=>$val_t)
                    {
                        $sale_target_base = $this->SaleTarget->find('first',array(
                            'conditions'=>array(
                                'SaleTarget.product_id'=>$key_p,
                                'SaleTarget.fiscal_year_id'=>$fiscal_year_chk,
                                'SaleTarget.aso_id'=>$office_id,
                                'SaleTarget.target_category'=>3,
                                'SaleTarget.territory_id' => $key_t
                                ),
                            'recursive'=>-1
                            ));
                        // pr($sale_target_base);die;
                        $product_name = $this->Product->find('first',array(
                            'fields'=>'Product.name',
                            'conditions'=>array('Product.id'=>$key_p),
                            'recursive'=>-1
                            ));
                        if($sale_target_base['SaleTarget']['quantity'] < $val_t)
                        {
                            $is_error=1;
                            $error_msg .="Target Quantity is gretter than national Qty for product ".$product_name['Product']['name']."<br>";
                        }

                        if($sale_target_base['SaleTarget']['amount'] < $chk_product_sum['amount'][$key_p][$key_t])
                        {
                            $is_error=1;
                            $error_msg .="Target Amount is gretter than National Amount for product ".$product_name['Product']['name']."<br>";
                        }
                    }
                }
                if($is_error == 0)
                {
                    if($insert_data_array)
				{	//pr($insert_data_array);die();			
					$this->SaleTargetMonth->create();
					$this->SaleTargetMonth->saveAll($insert_data_array);
				}
				if($update_data_array)
				{
					//pr($update_data_array);die();
					$this->SaleTargetMonth->saveAll($update_data_array);
				}
                $this->Session->setFlash(__('The Sale Targets monthly has been saved'), 'flash/success');
                $this->redirect(array("controller" => "SaleTargetsBaseWise", 
                  "action" => "admin_index"));

            }
            else
            {
                $this->Session->setFlash(__($error_msg), 'flash/error');
                $this->redirect(array("controller" => "SaleTargetsBaseWise", 
                    "action" => "admin_index"));
            }


        }
    }
}
public function download_xl($fiscal_year_id=null,$ofice_id)
{
    $this->Office->recursive = -1;
    $office_list = $this->Office->find('all',
        array(
            'conditions'=>array(
                'Office.office_type_id'=>2,
                'Office.id'=>$ofice_id
                )
            )
        );
    $product = $this->Product->find('all',array(
        'conditions'=>array('Product.product_type_id'=>1),
        'order'=>array('Product.order'),
        'recursive'=>-1));
    $fiscal_year = $this->FiscalYear->find('first',array(
        'fields'=>array('FiscalYear.id','FiscalYear.year_code'),
        'conditions'=>array('FiscalYear.id'=>$fiscal_year_id),
        'recursive'=>-1
        ));
    $table='<table border="1"><tbody>
    <tr>
        <td>Fiscal Year</td>
        <td>Office Name</td>
        <td>Territory Name</td>
        <td>Product Name</td>
        <td>Quantity</td>
        <td>Amount</td>
    </tr>
    ';
    foreach($office_list as $o_data)
    {
        $fiscal_year_code = $fiscal_year['FiscalYear']['year_code'];
        $ofice_name=$o_data['Office']['office_name'];
        $territory_list = $this->Territory->find('all', array(
            'conditions' => array(
                'AND' => array(
                    array(
                        'Territory.office_id' => $o_data['Office']['id'],
                        )
                    )),
            'recursive'=>-1
            ));
        foreach($territory_list as $t_data)
        {
            $territory_name = $t_data['Territory']['name'];
            foreach($product as $p_data)
            {
                $product_name = $p_data['Product']['name'];
                   /* $sale_target = $this->SaleTarget->find('all',array(
                        'conditions'=>array('SaleTarget.fiscal_year_id'=>$fiscal_year_id,
                            'SaleTarget.target_category'=>3,
                            'SaleTarget.product_id'=>$p_data['Product']['id'],
                            'SaleTarget.so_id'=>$t_data['Territory']['id']
                            ),
                        'recursive' => -1
                        ));*/
                        $qty=0;
                        $amount = 0;
                    /*if($sale_target)
                    {
                        $qty=$sale_target[0]['SaleTarget']['quantity'];
                        $amount = $sale_target[0]['SaleTarget']['amount'];
                    }*/
                    
                    $table.='<tr>
                    <td>'.$fiscal_year_code.'</td>
                    <td>'.$ofice_name.'</td>
                    <td>'.$territory_name.'</td>
                    <td>'.$product_name.'</td>
                    <td>'.$qty.'</td>
                    <td>'.$amount.'</td>
                </tr>
                ';
            }
        }

    }
    $table.='</tbody></table>';
    header("Content-Type: application/vnd.ms-excel");
    header('Content-Disposition: attachment; filename="sale_target_base_wise.xls"');
    header("Cache-Control: ");
    header("Pragma: ");
    header("Expires: 0");  
    echo $table;
    
    $this->autoRender=false;
}

public function download_xl_month($fiscal_year_id=null,$ofice_id)
{
    $this->loadModel('Month');
    $this->Office->recursive = -1;
    $office_list = $this->Office->find('all',
        array(
            'conditions'=>array(
                'Office.office_type_id'=>2,
                'Office.id'=>$ofice_id
                )
            )
        );
		
    $product = $this->Product->find('all',array(
        'conditions'=>array('Product.product_type_id'=>1),
        'order'=>array('Product.order'),
        'recursive'=>-1));
    $fiscal_year = $this->FiscalYear->find('first',array(
        'fields'=>array('FiscalYear.id','FiscalYear.year_code'),
        'conditions'=>array('FiscalYear.id'=>$fiscal_year_id),
        'recursive'=>-1
        ));
    $table='<table border="1"><tbody>
    <tr>
        <td>Fiscal Year</td>
        <td>Month</td>
        <td>Office Name</td>
        <td>Territory Name</td>
        <td>Product Name</td>
        <td>Quantity</td>
        <td>Amount</td>
    </tr>
    ';
    $all_month = $this->Month->find('all',array(
        /* 'conditions'=>array('Month.fiscal_year_id'=>$fiscal_year_id) ,*/
        'order'=>array('Month.month'),
        'recursive'=>-1
        ));
    // pr($all_month);die;
    foreach($office_list as $o_data)
    {
        $fiscal_year_code = $fiscal_year['FiscalYear']['year_code'];
        $ofice_name=$o_data['Office']['office_name'];
        $territory_list = $this->Territory->find('all', array(
            'conditions' => array(
                'AND' => array(
                    array(
                        'Territory.office_id' => $o_data['Office']['id'],
                        )
                    )),
            'recursive'=>-1
            ));
        foreach($all_month as $m_data)
        {
            $month_name = $m_data['Month']['name'];
            foreach($territory_list as $t_data)
            {
                $territory_name = $t_data['Territory']['name'];
                foreach($product as $p_data)
                {
                    $product_name = $p_data['Product']['name'];
                    /*$sale_target = $this->SaleTarget->find('all',array(
                        'conditions'=>array('SaleTarget.fiscal_year_id'=>$fiscal_year_id,
                            'SaleTarget.target_category'=>3,
                            'SaleTarget.product_id'=>$p_data['Product']['id'],
                            'SaleTarget.so_id'=>$t_data['Territory']['id']
                            ),
                        'recursive' => -1
                        ));
*/                    $qty=0;
                        $amount = 0;
                   /* if($sale_target)
                    {
                        $qty=$sale_target[0]['SaleTarget']['quantity'];
                        $amount = $sale_target[0]['SaleTarget']['amount'];
                    }*/
                    
                    $table.='<tr>
                    <td>'.$fiscal_year_code.'</td>
                    <td>'.$month_name.'</td>
                    <td>'.$ofice_name.'</td>
                    <td>'.$territory_name.'</td>
                    <td>'.$product_name.'</td>
                    <td>'.$qty.'</td>
                    <td>'.$amount.'</td>
                </tr>
                ';
            }
        }
    }

}
$table.='</tbody></table>';
header("Content-Type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename="sale_target_base_wise_month.xls"');
header("Cache-Control: ");
header("Pragma: ");
header("Expires: 0");  
echo $table;

$this->autoRender=false;
}

}
