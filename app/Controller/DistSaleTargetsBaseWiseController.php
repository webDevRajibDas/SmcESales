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
class DistSaleTargetsBaseWiseController extends AppController {

    public $uses = array('DistSaleTarget', 'DistDistributor', 'Product', 'Office', 'DistSaleTargetMonth', 'FiscalYear', 'DistSalesRepresentative');

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
            $array = array();
            $i = 0;
            foreach ($this->request->data['DistSaleTarget']['quantity'] as $key => $value) {
                if (array_key_exists('id', $this->request->data['DistSaleTarget'])):
                    $array[$i]['id'] = $this->request->data['DistSaleTarget']['id'][$key];
                endif;
                $array[$i]['fiscal_year_id'] = $this->request->data['DistSaleTarget']['fiscal_year_id'];
                $array[$i]['target_category'] = 3;
                $array[$i]['aso_id'] = $this->request->data['DistSaleTarget']['aso_id'];
                //$array[$i]['dist_distributor_id'] = $this->request->data['DistSaleTarget']['dist_distributor_id'];
                $array[$i]['dist_sales_representative_code'] = $key;
                $array[$i]['product_id'] = $this->request->data['DistSaleTarget']['product_id'];
                $array[$i]['quantity'] = $value;
                $array[$i]['amount'] = $this->request->data['DistSaleTarget']['amount'][$key];
                $array[$i]['created'] = $this->current_datetime();
                $array[$i]['updated'] = $this->current_datetime();
                $i++;
            }
            if ($this->DistSaleTarget->saveAll($array)) {
                $this->Session->setFlash(__('The Distributor Sale Target Base saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The outlet could not be saved. Please, try again.'), 'flash/error');
            }
        }
        $this->set('page_title', 'Distributor Sale Target Base Wise List');
        $this->DistSaleTarget->recursive = 0;
        $products = $this->Product->find('list', array('conditions' => array(), 'order' => array('Product.id' => 'ASC')));
        $this->Office->recursive = 1;
        $saleOffice_list = $this->Office->find('list', array('conditions' => array('Office.office_type_id' => 2)));
        $fiscalYears = $this->DistSaleTarget->FiscalYear->find('list', array('fields' => array('year_code')));
        $this->set(compact('fiscalYears', 'products', 'saletarget', 'saleOffice_list', 'saletargets_list', 'so_name_list'));
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_set_monthly_target($office_id = null, $product_id = null, $target_id = null, $fiscal_year_id = null, $territory_id = null) {

        $this->set('page_title', 'Monthly Distributor Sale Target');
        $this->loadModel('DistSaleTargetMonth');
        if ($this->request->is('post') || $this->request->is('put')) {
            //pr($this->request->data);die();
            $territory_quantity = $this->request->data['DistSaleTargetMonth']['t_quantity'];
            $territory_amount = $this->request->data['DistSaleTargetMonth']['t_amount'];
            $product_id = $this->request->data['DistSaleTargetMonth']['product_id'];
            $fiscal_year_id = $this->request->data['DistSaleTargetMonth']['fiscal_year_id'];
            $dist_sale_target_id = $this->request->data['DistSaleTargetMonth']['dist_sale_target_id'];
            $office_id = $this->request->data['DistSaleTargetMonth']['aso_id'];
            foreach ($this->request->data['DistSaleTargetMonth']['quantity'] as $key => $val) {
                $single_row = array();
                $single_row['month_id'] = $key;
                $single_row['target_quantity'] = $val;
                $single_row['target_amount'] = $this->request->data['DistSaleTargetMonth']['amount'][$key];
                $single_row['aso_id'] = $this->request->data['DistSaleTargetMonth']['aso_id'];
                $single_row['fiscal_year_id'] = $this->request->data['DistSaleTargetMonth']['fiscal_year_id'];
                $single_row['product_id'] = $this->request->data['DistSaleTargetMonth']['product_id'];
                $single_row['dist_sale_target_id'] = $this->request->data['DistSaleTargetMonth']['dist_sale_target_id'];
                //$single_row['dist_distributor_id'] = $this->request->data['DistSaleTargetMonth']['dist_distributor_id'];
                $single_row['target_type'] = 0;
                $single_row['session'] = 0;
                if ($this->request->data['DistSaleTargetMonth']['id'][$key] != 0) {
                    $single_row['updated_at'] = $this->current_datetime();
                    $single_row['id'] = $this->request->data['DistSaleTargetMonth']['id'][$key];
                } else {
                    $single_row['created_at'] = $this->current_datetime();
                    $single_row['updated_at'] = $this->current_datetime();
                }
                $data_row = array();
                $data_row['DistSaleTargetMonth'] = $single_row;
                if (!empty($data_row['DistSaleTargetMonth'])) {
                    if ($this->request->data['DistSaleTargetMonth']['id'][$key] == 0) {
                        $this->DistSaleTargetMonth->create();
                    }
                    if ($this->DistSaleTargetMonth->save($data_row['DistSaleTargetMonth'])) {
                        $single_row = array();
                    }
                }
            }
            /* ------------ start default set monthly data ---------- */

            $this->set('product_id', $product_id);
            $this->set('fiscal_year_id', $fiscal_year_id);
            $this->set('quantity', $territory_quantity);
            $this->set('amount', $territory_amount);
            $this->set('dist_sale_target_id', $dist_sale_target_id);
            $this->set('office_id', $office_id);


            /* ------------ end default set monthly data ---------- */
            $this->Session->setFlash(__('The Monthly Target has been saved'), 'flash/success');
            //$this->redirect(array('action' => 'index'));
        }
        $this->loadModel('DistDistributor');
        $this->loadModel('DistSalesRepresentative');
        $this->loadModel('Month');

        $products = $this->Product->find('list', array(
            'conditions' => array(
                'Product.product_type_id' => 1
            ),
            'recursive' => -1,
            'order' => 'Product.order ASC',
        ));
        $fiscalYears = $this->FiscalYear->find('list');
        $months = $this->Month->find('list');
        $data = $this->DistSalesRepresentative->find('all', array(
            'conditions' => array(
                'DistSalesRepresentative.office_id' => $office_id,
                'DistSalesRepresentative.is_active' =>1,
            ),
            'fields' => array('DistSalesRepresentative.id', 'DistSalesRepresentative.code', 'DistSalesRepresentative.name'),
            'recursive' => -1
        ));
        foreach ($data as $key => $value) {
        $distSalesRepresentatives[$value['DistSalesRepresentative']['code']]=$value['DistSalesRepresentative']['name']; 
        }

        $sale_target_month_data = $this->DistSaleTargetMonth->find('all', array(
            'conditions' => array(
                'DistSaleTargetMonth.dist_sale_target_id' => $target_id,
                'DistSaleTargetMonth.target_type' => 0,
            ),
            'fields' => array('DistSaleTargetMonth.id', 'DistSaleTargetMonth.target_quantity', 'DistSaleTargetMonth.target_amount', 'DistSaleTargetMonth.month_id'),
            'recursive' => -1
        ));

        $sale_target = $this->DistSaleTarget->find('all', array(
            'fields' => array('DistSaleTarget.quantity', 'DistSaleTarget.amount'),
            'conditions' => array(
                'DistSaleTarget.id' => $target_id,
                'DistSaleTarget.product_id' => $product_id,
                'DistSaleTarget.aso_id' => $office_id,
                'DistSaleTarget.dist_sales_representative_code' => $territory_id,
                'DistSaleTarget.fiscal_year_id' => $fiscal_year_id
            )
        ));

        $new_manupulated_array = array();
        $new_manupulated_array['DistSaleTarget'] = (isset($sale_target[0]['DistSaleTarget']) ? $sale_target[0]['DistSaleTarget'] : 0);
        foreach ($sale_target_month_data as $key => $val) {
            $new_manupulated_array[$val['DistSaleTargetMonth']['month_id']] = $val;
        }
        $this->set('sale_target_month_data', $new_manupulated_array);
        $this->set(compact('fiscalYears', 'products', 'months', 'fiscal_year_id', 'target_id', 'territory_id', 'product_id', 'office_id', 'dist_distributor_id', 'distSalesRepresentatives'));
    }

    public function admin_month_target_view() {
        $this->loadModel('DistDistributor');
        $this->loadModel('Month');
        $this->loadModel('DistSaleTargetMonth');
        $month_list = $this->Month->find('list', array(
            'fields' => array('Month.id', 'Month.name'),
            'conditions' => array('Month.fiscal_year_id' => $this->request->data('fiscal_year_id'))
        ));
        $filter_array = array();
        foreach ($month_list as $key => $val) {
            $filter_array[] = array('id' => $key, 'name' => $val);
        }
        $this->DistDistributor->recursive = -1;
        $saletargets_list = $this->DistDistributor->find('all', array(
            'conditions' => array(
                'DistSaleTarget.fiscal_year_id' => $this->request->data('fiscal_year_id'),
                'DistSaleTarget.variant_id' => $this->request->data('variant_id'),
                'DistSaleTarget.aso_id' => $this->request->data('aso_id'),
                'DistSaleTarget.target_category' => 3,
                'DistSaleTarget.target_type_id' => 0,
            ),
            "joins" => array(
                array(
                    "table" => "dist_sale_targets",
                    "alias" => "DistSaleTarget",
                    "type" => "INNER",
                    "conditions" => array("DistDistributor.id = DistSaleTarget.dist_distributor_id")
                )
            ),
            'fields' => array('DistDistributor.id')
                )
        );
        //pr($saletargets_list);
        $this->DistSaleTargetMonth->recursive = -1;
        $monthly_targets = $this->DistSaleTargetMonth->find('all', array(
            'conditions' => array(
                'DistSaleTargetMonth.fiscal_year_id' => $this->request->data('fiscal_year_id'),
                'DistSaleTargetMonth.variant_id' => $this->request->data('variant_id'),
                'DistSaleTargetMonth.aso_id' => $this->request->data('aso_id'),
                'DistSaleTargetMonth.dist_distributor_id' => $this->request->data('dist_distributor_id'),
            )
        ));

        if (!empty($monthly_targets)) {
            foreach ($saletargets_list as $saletarget_key => $saletarget_val) {
                $territory_id = $saletarget_val['DistDistributor']['id'];
                foreach ($monthly_targets as $month_target_key => $month_target_val) {
                    $territory_id_in_month_target = $month_target_val['DistSaleTargetMonth']['dist_distributor_id'];
                    $unique_month_id = $month_target_val['DistSaleTargetMonth']['month_id'];
                    if ($territory_id == $territory_id_in_month_target) {
                        $saletargets_list[$saletarget_key]['DistSaleTargetMonth'][$unique_month_id] = $month_target_val['DistSaleTargetMonth'];
                    }
                }
            }
        }

        /* ---------- end monthly target data --------- */
        $this->set(compact('saletargets_list', 'filter_array', 'month_list'));
        /* -------- end territory list with saletarget --------- */
    }

    public function admin_get_sales_target_base_wise_data() {
        $this->loadModel('DistSaleTarget');
        $this->loadModel('DistSalesRepresentative');
        $saletargets_list = $this->DistSaleTarget->find('all', array(
            'joins' => array(
                array(
                    'table' => 'dist_sales_representatives',
                    'alias' => 'DistSalesRepresentative',
                    'type' => 'INNER',
                    'conditions' => array(
                        'DistSalesRepresentative.code = DistSaleTarget.dist_sales_representative_code'
                    )
                )
            ),
            'conditions' => array(
                'DistSaleTarget.fiscal_year_id' => $this->request->data('FiscalYearId'),
                'DistSaleTarget.product_id' => $this->request->data('product_id'),
                'DistSaleTarget.aso_id' => $this->request->data('SaleTargetAsoId'),
                'DistSaleTarget.target_category' => 3,
            ),
            'fields' => array('DistSalesRepresentative.id', 'DistSalesRepresentative.code', 'DistSalesRepresentative.name', 'DistSaleTarget.id', 'DistSaleTarget.aso_id', 'DistSaleTarget.dist_distributor_id', 'DistSaleTarget.quantity', 'DistSaleTarget.amount', 'DistSaleTarget.product_id', 'DistSaleTarget.fiscal_year_id')
        ));
        /* -----------------Total amount and quantity----------------------- */
        $saletarget = $this->DistSaleTarget->find('all', array(
            'conditions' => array(
                'DistSaleTarget.fiscal_year_id' => $this->request->data('FiscalYearId'),
                'DistSaleTarget.product_id' => $this->request->data('product_id'),
                'DistSaleTarget.aso_id' => $this->request->data('SaleTargetAsoId'),
                'DistSaleTarget.target_category' => 2,
            ),
            'fields' => array('SUM(DistSaleTarget.quantity) as quantity', 'SUM(DistSaleTarget.amount) as amount')
        ));
        if (count($saletargets_list) > 0) {
            $this->set(compact('saletargets_list', 'saletarget'));
        } else {
            $distDistributors = $this->DistSalesRepresentative->find('all', array(
                'conditions' => array(
                    'DistSalesRepresentative.office_id' => $this->request->data('SaleTargetAsoId'),
                ),
                'recursive' => -1
            ));
            $this->set(compact('distDistributors', 'saletarget'));
        }
    }

    public function admin_get_sales_target_base_wise_data_for_distributor() {
        $this->loadModel('DistSaleTarget');
        $this->loadModel('DistDistributor');
        $saletargets_list = $this->DistSaleTarget->find('all', array(
            'joins' => array(
                array(
                    'table' => 'dist_distributors',
                    'alias' => 'DistDistributor',
                    'type' => 'INNER',
                    'conditions' => array(
                        'DistDistributor.id = DistSaleTarget.dist_distributor_id'
                    )
                )
            ),
            'conditions' => array(
                'DistSaleTarget.fiscal_year_id' => $this->request->data('FiscalYearId'),
                'DistSaleTarget.product_id' => $this->request->data('product_id'),
                'DistSaleTarget.aso_id' => $this->request->data('SaleTargetAsoId'),
                'DistSaleTarget.target_category' => 3,
            ),
            'fields' => array('DistDistributor.id', 'DistDistributor.name', 'DistSaleTarget.id', 'DistSaleTarget.aso_id', 'DistSaleTarget.dist_distributor_id', 'DistSaleTarget.quantity', 'DistSaleTarget.amount', 'DistSaleTarget.product_id', 'DistSaleTarget.fiscal_year_id')
        ));
        /* -----------------Total amount and quantity----------------------- */
        $saletarget = $this->DistSaleTarget->find('all', array(
            'conditions' => array(
                'DistSaleTarget.fiscal_year_id' => $this->request->data('FiscalYearId'),
                'DistSaleTarget.product_id' => $this->request->data('product_id'),
                'DistSaleTarget.aso_id' => $this->request->data('SaleTargetAsoId'),
                'DistSaleTarget.target_category' => 2,
            ),
            'fields' => array('SUM(DistSaleTarget.quantity) as quantity', 'SUM(DistSaleTarget.amount) as amount')
        ));
        if (count($saletargets_list) > 0) {
            $this->set(compact('saletargets_list', 'saletarget'));
        } else {
            $this->loadModel('DistDistributor');
            $distDistributors = $this->DistDistributor->find('all', array(
                'conditions' => array(
                    'DistDistributor.office_id' => $this->request->data('SaleTargetAsoId'),
                ),
                'recursive' => -1
            ));
            $this->set(compact('distDistributors', 'saletarget'));
        }
    }

    public function admin_sales_base_wise_data() {
        $this->loadModel('DistSaleTarget');
        $this->DistSaleTarget->recursive = -1;
        $saletarget = $this->DistSaleTarget->find('all', array(
            'conditions' => array(
                'DistSaleTarget.fiscal_year_id' => $this->request->data('FiscalYearId'),
                'DistSaleTarget.product_id' => $this->request->data('product_id'),
                'DistSaleTarget.target_category' => 2,
                'DistSaleTarget.aso_id' => $this->request->data('SaleTargetAsoId'),
            )
        ));
        $array = array();
        if (count($saletarget) > 0) {
            $array['qty'] = $saletarget[0]['DistSaleTarget']['quantity'];
            $array['amount'] = $saletarget[0]['DistSaleTarget']['amount'];
        } else {
            $array['qty'] = 0;
            $array['amount'] = 0;
        }
        echo json_encode($array);

        $this->autoRender = false;
    }

    public function admin_get_total_area_targets_data() {
        $this->loadModel('DistDistributor');
        $this->loadModel('DistSaleTarget');
        $fiscal_year_id = $this->request->data['fiscal_year_id'];
        $variant_id = $this->request->data['variant_id'];
        $aso_id = $this->request->data['aso_id'];

        $dist_distributor_id = $this->request->data['dist_distributor_id'];
        $saletargets_list = $this->DistDistributor->find('all', array(
            'conditions' => array(
                'DistSaleTarget.fiscal_year_id' => $fiscal_year_id,
                'DistSaleTarget.variant_id' => $variant_id,
                'DistSaleTarget.aso_id' => $aso_id,
                'DistSaleTarget.dist_distributor_id' => $dist_distributor_id,
            ),
            "joins" => array(
                array(
                    "table" => "dist_sale_targets",
                    "alias" => "DistSaleTarget",
                    "type" => "INNER",
                    "conditions" => array("DistDistributor.id = DistSaleTarget.dist_distributor_id"
                    )
                )
            ),
            'fields' => array('DistSaleTarget.quantity', 'DistSaleTarget.amount')
                )
        );
        $total_qty = 0;
        $total_amount = 0;
        $data = array();
        if (!empty($saletargets_list)) {
            foreach ($saletargets_list as $val) {
                $total_qty = $total_qty + $val['DistSaleTarget']['quantity'];
                $total_amount = $total_amount + $val['DistSaleTarget']['amount'];
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

    public function admin_upload_xl() {
        $this->loadModel('Product');
        $this->loadModel('FiscalYear');
        $this->loadModel('Office');

        if (!empty($_FILES["file"]["name"])) {
            $target_dir = WWW_ROOT . 'files/';
            $target_file = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 30);
            $uploadOk = 1;
            $imageFileType = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);

            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir . $target_file . '.' . $imageFileType)) {
                $data_ex = new Spreadsheet_Excel_Reader($target_dir . $target_file . '.' . $imageFileType, true);
                $temp = $data_ex->dumptoarray();
                $this->DistSaleTarget->recursive = -1;
                $insert_data_array = array();
                $update_data_array = array();

                // pr($temp);
                // exit;

                foreach ($temp as $key => $val) {
                    if ($key > 0 && !empty($val[1]) && !empty($val[2])) {

                        $fiscal_year_id = $this->FiscalYear->find('first', array(
                            'fields' => array('FiscalYear.id'),
                            'conditions' => array('FiscalYear.year_code LIKE' => '%' . trim($val[1] . '%')),
                            'recursive' => -1
                        ));
                        $product_id = $this->Product->find('first', array(
                            'fields' => 'Product.id',
                            'conditions' => array('lower(Product.name) like' => '%' . strtolower(html_entity_decode($val[4])) . '%'),
                            'recursive' => -1
                        ));
                        $aso_id = $this->Office->find('first', array(
                            'fields' => 'Office.id',
                            'conditions' => array('lower(Office.office_name) like' => '%' . strtolower($val[2]) . '%'),
                            'recursive' => -1
                        ));
                        $distDistributors = $this->DistSalesRepresentative->find('first', array(
                            'fields' => 'DistSalesRepresentative.code',
                            'conditions' => array('lower(DistSalesRepresentative.name) like' => '%' . strtolower(html_entity_decode($val[3])) . '%'),
                            'recursive' => -1
                        ));

                        if (!$product_id && !$fiscal_year_id && !$aso_id && !$distDistributors) {
                            $this->Session->setFlash(__('The Product Name or fiscal year or Offie Name or Territory missing or incorrect on line ' . $key), 'flash/error');
                            $this->redirect(array("controller" => "DistSaleTargetsBaseWise", "action" => "admin_index"));
                        }

                        if (!isset($chk_product_sum['qty'][$product_id['Product']['id']])) {
                            $chk_product_sum['qty'][$product_id['Product']['id']] = 0;
                        }
                        if (!isset($chk_product_sum['amount'][$product_id['Product']['id']])) {
                            $chk_product_sum['amount'][$product_id['Product']['id']] = 0;
                        }

                        $chk_product_sum['qty'][$product_id['Product']['id']] += $val[5];
                        $chk_product_sum['amount'][$product_id['Product']['id']] += $val[6];

                        $saletargets = $this->DistSaleTarget->find('first', array(
                            'conditions' => array(
                                'DistSaleTarget.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'],
                                'DistSaleTarget.aso_id' => $aso_id['Office']['id'],
                                'DistSaleTarget.dist_sales_representative_code' => $distDistributors['DistSalesRepresentative']['code'],
                                'DistSaleTarget.product_id' => $product_id['Product']['id'],
                                'DistSaleTarget.target_type_id' => 0,
                                'DistSaleTarget.target_category' => 3
                        )));

                        if (empty($saletargets)) {
                            $insert_data['DistSaleTarget']['product_id'] = $product_id['Product']['id'];
                            $insert_data['DistSaleTarget']['target_category'] = 3;
                            $insert_data['DistSaleTarget']['fiscal_year_id'] = $fiscal_year_id['FiscalYear']['id'];

                            $insert_data['DistSaleTarget']['quantity'] = $val[5];
                            $insert_data['DistSaleTarget']['amount'] = $val[6];

                            $insert_data['DistSaleTarget']['aso_id'] = $aso_id['Office']['id'];
                            $insert_data['DistSaleTarget']['dist_sales_representative_code'] = $distDistributors['DistSalesRepresentative']['code'];
                            $insert_data_array[] = $insert_data;
                        } else {
                            $updated_data['DistSaleTarget']['id'] = $saletargets['DistSaleTarget']['id'];

                            $updated_data['DistSaleTarget']['quantity'] = $val[5];
                            $updated_data['DistSaleTarget']['amount'] = $val[6];

                            $update_data_array[] = $updated_data;
                        }
                    }
                }


                // pr($insert_data_array);
                // exit;
                $is_error = 0;
                $error_msg = '';
                $fiscal_year_chk = $fiscal_year_id['FiscalYear']['id'];
                $office_id = $aso_id['Office']['id'];
                foreach ($chk_product_sum['qty'] as $key => $val) {
                    $sale_target_area = $this->DistSaleTarget->find('first', array(
                        'conditions' => array(
                            'DistSaleTarget.product_id' => $key,
                            'DistSaleTarget.fiscal_year_id' => $fiscal_year_chk,
                            'DistSaleTarget.aso_id' => $office_id,
                            'DistSaleTarget.target_category' => 2
                        ),
                        'recursive' => -1
                    ));

                    //pr($sale_target_area);die;
                    $product_name = $this->Product->find('first', array(
                        'fields' => 'Product.name',
                        'conditions' => array('Product.id' => $key),
                        'recursive' => -1
                    ));
                    if (count($sale_target_area) > 0):
                        if ($sale_target_area['DistSaleTarget']['quantity'] < $val) {
                            $is_error = 1;
                            $error_msg .= "Target Quantity is gretter than national Qty for product " . $product_name['Product']['name'] . "<br>";
                        }

                        if ($sale_target_area['DistSaleTarget']['amount'] < $chk_product_sum['amount'][$key]) {
                            $is_error = 1;
                            $error_msg .= "Target Amount is gretter than National Amount for product " . $product_name['Product']['name'] . "<br>";
                        }
                    endif;
                }

                if ($is_error == 0) {
                    if ($insert_data_array) {
                        $this->DistSaleTarget->create();
                        $this->DistSaleTarget->saveAll($insert_data_array);
                    }
                    if ($update_data_array) {
                        $this->DistSaleTarget->saveAll($update_data_array);
                    }
                    $this->Session->setFlash(__('The Distributor Sale Targets has been saved'), 'flash/success');
                    $this->redirect(array("controller" => "DistSaleTargetsBaseWise",
                        "action" => "admin_index"));
                } else {
                    $this->Session->setFlash(__($error_msg), 'flash/error');
                    $this->redirect(array("controller" => "DistSaleTargetsBaseWise",
                        "action" => "admin_index"));
                }
            }
        }
    }

    public function admin_upload_xl_month() {

        $this->loadModel('Product');
        $this->loadModel('FiscalYear');
        $this->loadModel('Office');
        $this->loadModel('DistSalesRepresentative');
        $this->loadModel('Month');

        if (!empty($_FILES["file"]["name"])) {
            $target_dir = WWW_ROOT . 'files/';
            $target_file = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 30);
            $uploadOk = 1;
            $imageFileType = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);

            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir . $target_file . '.' . $imageFileType)) {
                $data_ex = new Spreadsheet_Excel_Reader($target_dir . $target_file . '.' . $imageFileType, true);
                $temp = $data_ex->dumptoarray();
                $this->DistSaleTargetMonth->recursive = -1;
                $this->DistSaleTarget->recursive = -1;
                $insert_data_array = array();
                $update_data_array = array();
                foreach ($temp as $key => $val) {
                    if ($key > 0 && !empty($val[1]) && !empty($val[2])) {
                        $fiscal_year_id = $this->FiscalYear->find('first', array(
                            'fields' => array('FiscalYear.id'),
                            'conditions' => array('FiscalYear.year_code LIKE' => '%' . trim($val[1] . '%')),
                            'recursive' => -1
                        ));
                        //pr($fiscal_year_id);
                        $product_id = $this->Product->find('first', array(
                            'fields' => 'Product.id',
                            'conditions' => array('lower(Product.name) like' => '%' . strtolower(html_entity_decode($val[5])) . '%'),
                            'recursive' => -1
                        ));
                        $aso_id = $this->Office->find('first', array(
                            'fields' => 'Office.id',
                            'conditions' => array('lower(Office.office_name) like' => '%' . strtolower($val[3]) . '%'),
                            'recursive' => -1
                        ));
                        $territory_id = $this->DistSalesRepresentative->find('first', array(
                            'fields' => 'DistSalesRepresentative.code',
                            'conditions' => array('lower(DistSalesRepresentative.name) like' => '%' . strtolower(html_entity_decode($val[4])) . '%'),
                            'recursive' => -1
                        ));
                        $month_id = $this->Month->find('first', array(
                            'fields' => 'Month.id',
                            'conditions' => array(
                                //'Month.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'], 
                                'lower(Month.name) like' => '%' . strtolower($val[2]) . '%'),
                            'recursive' => -1
                        ));

                        if (!$product_id && !$fiscal_year_id && !$aso_id && !$territory_id && !$month_id) {
                            $this->Session->setFlash(__('The Product Name or fiscal year or Offie Name or Territory or month missing or incorrect on line ' . $key), 'flash/error');
                            $this->redirect(array("controller" => "DistSaleTargetsBaseWise", "action" => "admin_index"));
                        }
                        $saletargets = $this->DistSaleTarget->find('first', array(
                            'conditions' => array(
                                'DistSaleTarget.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'],
                                'DistSaleTarget.aso_id' => $aso_id['Office']['id'],
                                'DistSaleTarget.dist_sales_representative_code' => $territory_id['DistSalesRepresentative']['code'],
                                'DistSaleTarget.product_id' => $product_id['Product']['id'],
                                'DistSaleTarget.target_type_id' => 0,
                                'DistSaleTarget.target_category' => 3
                        )));

                        if (empty($saletargets)) {
                            $this->Session->setFlash(__('The Distributor Sale Target Base wise Not Set'), 'flash/error');
                            $this->redirect(array("controller" => "DistSaleTargetsBaseWise", "action" => "admin_index"));
                        }

                        $saletarget_month = $this->DistSaleTargetMonth->find('first', array(
                            'conditions' => array(
                                'DistSaleTargetMonth.dist_sale_target_id' => $saletargets['DistSaleTarget']['id'],
                                'DistSaleTargetMonth.product_id' => $product_id['Product']['id'],
                                'DistSaleTargetMonth.month_id' => $month_id['Month']['id'],
                                'DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'],
                                'DistSaleTargetMonth.aso_id' => $aso_id['Office']['id'],
                            // 'DistSaleTargetMonth.dist_sales_representative_id' => $territory_id['DistSalesRepresentative']['id'],
                            )
                        ));
                        if (!isset($chk_product_sum['qty'][$product_id['Product']['id']][$territory_id['DistSalesRepresentative']['code']])) {
                            $chk_product_sum['qty'][$product_id['Product']['id']][$territory_id['DistSalesRepresentative']['code']] = 0;
                        }
                        if (!isset($chk_product_sum['amount'][$product_id['Product']['id']][$territory_id['DistSalesRepresentative']['code']])) {
                            $chk_product_sum['amount'][$product_id['Product']['id']][$territory_id['DistSalesRepresentative']['code']] = 0;
                        }

                        $chk_product_sum['qty'][$product_id['Product']['id']][$territory_id['DistSalesRepresentative']['code']] += $val[6];
                        $chk_product_sum['amount'][$product_id['Product']['id']][$territory_id['DistSalesRepresentative']['code']] += $val[7];
                        //pr($saletarget_month);
                        //pr($saletargets);
                        //die();
                        if (empty($saletarget_month)) {
                            $insert_data['DistSaleTargetMonth']['dist_sale_target_id'] = $saletargets['DistSaleTarget']['id'];
                            $insert_data['DistSaleTargetMonth']['product_id'] = $product_id['Product']['id'];
                            $insert_data['DistSaleTargetMonth']['month_id'] = $month_id['Month']['id'];
                            $insert_data['DistSaleTargetMonth']['fiscal_year_id'] = $fiscal_year_id['FiscalYear']['id'];
                            $insert_data['DistSaleTargetMonth']['target_quantity'] = $val[6];
                            $insert_data['DistSaleTargetMonth']['target_amount'] = $val[7];
                            $insert_data['DistSaleTargetMonth']['aso_id'] = $aso_id['Office']['id'];
                            //$insert_data['DistSaleTargetMonth']['dist_sales_representative_id'] = $territory_id['DistSalesRepresentative']['id'];
                            $insert_data['DistSaleTargetMonth']['target_type'] = 0;
                            $insert_data['DistSaleTargetMonth']['session'] = 0;
                            $insert_data_array[] = $insert_data;
                        } else {
                            $updated_data['DistSaleTargetMonth']['id'] = $saletarget_month['DistSaleTargetMonth']['id'];
                            $updated_data['DistSaleTargetMonth']['target_quantity'] = $val[6];
                            $updated_data['DistSaleTargetMonth']['target_amount'] = $val[7];
                            $update_data_array[] = $updated_data;
                        }
                    }
                }

                /* pr($chk_product_sum);
                  exit; */

                $is_error = 0;
                $error_msg = '';
                $fiscal_year_chk = $fiscal_year_id['FiscalYear']['id'];
                $office_id = $aso_id['Office']['id'];
                foreach ($chk_product_sum['qty'] as $key_p => $val_p) {
                    foreach ($val_p as $key_t => $val_t) {
                        $sale_target_base = $this->DistSaleTarget->find('first', array(
                            'conditions' => array(
                                'DistSaleTarget.product_id' => $key_p,
                                'DistSaleTarget.fiscal_year_id' => $fiscal_year_chk,
                                'DistSaleTarget.aso_id' => $office_id,
                                'DistSaleTarget.target_category' => 3,
                                'DistSaleTarget.dist_sales_representative_code' => $key_t
                            ),
                            'recursive' => -1
                        ));
                        // pr($sale_target_base);die;
                        $product_name = $this->Product->find('first', array(
                            'fields' => 'Product.name',
                            'conditions' => array('Product.id' => $key_p),
                            'recursive' => -1
                        ));
                        if ($sale_target_base['DistSaleTarget']['quantity'] < $val_t) {
                            $is_error = 1;
                            $error_msg .= "Target Quantity is gretter than national Qty for product " . $product_name['Product']['name'] . "<br>";
                        }

                        if ($sale_target_base['DistSaleTarget']['amount'] < $chk_product_sum['amount'][$key_p][$key_t]) {
                            $is_error = 1;
                            $error_msg .= "Target Amount is gretter than National Amount for product " . $product_name['Product']['name'] . "<br>";
                        }
                    }
                }
                if ($is_error == 0) {
                    if ($insert_data_array) { //pr($insert_data_array);die();			
                        $this->DistSaleTargetMonth->create();
                        $this->DistSaleTargetMonth->saveAll($insert_data_array);
                    }
                    if ($update_data_array) {
                        //pr($update_data_array);die();
                        $this->DistSaleTargetMonth->saveAll($update_data_array);
                    }
                    $this->Session->setFlash(__('The Distributor Sale Targets monthly has been saved'), 'flash/success');
                    $this->redirect(array("controller" => "DistSaleTargetsBaseWise",
                        "action" => "admin_index"));
                } else {
                    $this->Session->setFlash(__($error_msg), 'flash/error');
                    $this->redirect(array("controller" => "DistSaleTargetsBaseWise",
                        "action" => "admin_index"));
                }
            }
        }
    }

    public function download_xl($fiscal_year_id = 20, $ofice_id = 29) {
        $this->Office->recursive = -1;
        $office_list = $this->Office->find('all', array(
            'conditions' => array(
                'Office.office_type_id' => 2,
                'Office.id' => $ofice_id
            )
                )
        );
        $product = $this->Product->find('all', array(
            'conditions' => array('Product.product_type_id' => 1),
            'order' => array('Product.order'),
            'recursive' => -1));
        $fiscal_year = $this->FiscalYear->find('first', array(
            'fields' => array('FiscalYear.id', 'FiscalYear.year_code'),
            'conditions' => array('FiscalYear.id' => $fiscal_year_id),
            'recursive' => -1
        ));
        $table = '<table border="1"><tbody>
    <tr>
        <td>Fiscal Year</td>
        <td>Office Name</td>
        <td>Sales Representative</td>
        <td>Product Name</td>
        <td>Quantity</td>
        <td>Amount</td>
    </tr>
    ';
        foreach ($office_list as $o_data) {
            $fiscal_year_code = $fiscal_year['FiscalYear']['year_code'];
            $ofice_name = $o_data['Office']['office_name'];
            $distributor_list = $this->DistSalesRepresentative->find('all', array(
                'conditions' => array(
					'DistSalesRepresentative.office_id' => $o_data['Office']['id'],
					'DistSalesRepresentative.is_active' =>1
					),
                'recursive' => -1
            ));
            foreach ($distributor_list as $t_data) {
                $distributor_name = $t_data['DistSalesRepresentative']['name'];
                foreach ($product as $p_data) {
                    $product_name = $p_data['Product']['name'];
                    $sale_target = $this->DistSaleTarget->find('all', array(
                        'conditions' => array('DistSaleTarget.fiscal_year_id' => $fiscal_year_id,
                            'DistSaleTarget.target_category' => 3,
                            'DistSaleTarget.product_id' => $p_data['Product']['id'],
                            'DistSaleTarget.dist_sales_representative_code' => $t_data['DistSalesRepresentative']['code']
                        ),
                        'recursive' => -1
                    ));
                    $qty = 0;
                    $amount = 0;
                    if ($sale_target) {
                        $qty = $sale_target[0]['DistSaleTarget']['quantity'];
                        $amount = $sale_target[0]['DistSaleTarget']['amount'];
                    }

                    $table .= '<tr>
                    <td>' . $fiscal_year_code . '</td>
                    <td>' . $ofice_name . '</td>
                    <td>' . $distributor_name . '</td>
                    <td>' . $product_name . '</td>
                    <td>' . $qty . '</td>
                    <td>' . $amount . '</td>
                </tr>
                ';
                }
            }
        }
        $table .= '</tbody></table>';
        header("Content-Type: application/vnd.ms-excel");
        header('Content-Disposition: attachment; filename="sale_target_base_wise.xls"');
        header("Cache-Control: ");
        header("Pragma: ");
        header("Expires: 0");
        echo $table;

        $this->autoRender = false;
    }

    public function download_xl_month($fiscal_year_id = 20, $ofice_id = 29) {
        $this->loadModel('Month');
        $this->Office->recursive = -1;
        $office_list = $this->Office->find('all', array(
            'conditions' => array(
                'Office.office_type_id' => 2,
                'Office.id' => $ofice_id
            )
                )
        );
        $product = $this->Product->find('all', array(
            'conditions' => array('Product.product_type_id' => 1),
            'order' => array('Product.order'),
            'recursive' => -1));
        $fiscal_year = $this->FiscalYear->find('first', array(
            'fields' => array('FiscalYear.id', 'FiscalYear.year_code'),
            'conditions' => array('FiscalYear.id' => $fiscal_year_id),
            'recursive' => -1
        ));
        $table = '<table border="1"><tbody>
    <tr>
        <td>Fiscal Year</td>
        <td>Month</td>
        <td>Office Name</td>
        <td>Sales Representative</td>
        <td>Product Name</td>
        <td>Quantity</td>
        <td>Amount</td>
    </tr>
    ';
        $all_month = $this->Month->find('all', array(
            //'conditions' => array('Month.fiscal_year_id' => $fiscal_year_id),
            'order' => array('Month.month'),
            'recursive' => -1
        ));
        //pr($all_month);die;
        foreach ($office_list as $o_data) {
            $fiscal_year_code = $fiscal_year['FiscalYear']['year_code'];
            $ofice_name = $o_data['Office']['office_name'];
            $territory_list = $this->DistSalesRepresentative->find('all', array(
                'conditions' => array(
						'DistSalesRepresentative.office_id' => $o_data['Office']['id'],
						'DistSalesRepresentative.is_active' =>1
				),
                'recursive' => -1
            ));
            foreach ($all_month as $m_data) {
                $month_name = $m_data['Month']['name'];
                foreach ($territory_list as $t_data) {
                    $territory_name = $t_data['DistSalesRepresentative']['name'];
                    foreach ($product as $p_data) {
                        $product_name = $p_data['Product']['name'];
                        $sale_target = $this->DistSaleTarget->find('all', array(
                            'conditions' => array('DistSaleTarget.fiscal_year_id' => $fiscal_year_id,
                                'DistSaleTarget.target_category' => 3,
                                'DistSaleTarget.product_id' => $p_data['Product']['id'],
                                'DistSaleTarget.dist_sales_representative_code' => $t_data['DistSalesRepresentative']['code']
                            ),
                            'recursive' => -1
                        ));
                        $qty = 0;
                        $amount = 0;
                        if ($sale_target) {
                            $qty = $sale_target[0]['DistSaleTarget']['quantity'];
                            $amount = $sale_target[0]['DistSaleTarget']['amount'];
                        }

                        $table .= '<tr>
                    <td>' . $fiscal_year_code . '</td>
                    <td>' . $month_name . '</td>
                    <td>' . $ofice_name . '</td>
                    <td>' . $territory_name . '</td>
                    <td>' . $product_name . '</td>
                    <td>' . $qty . '</td>
                    <td>' . $amount . '</td>
                </tr>
                ';
                    }
                }
            }
        }
        $table .= '</tbody></table>';
        header("Content-Type: application/vnd.ms-excel");
        header('Content-Disposition: attachment; filename="sale_target_base_wise_month.xls"');
        header("Cache-Control: ");
        header("Pragma: ");
        header("Expires: 0");
        echo $table;

        $this->autoRender = false;
    }

}
