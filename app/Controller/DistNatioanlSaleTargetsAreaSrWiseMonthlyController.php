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
class DistNatioanlSaleTargetsAreaSrWiseMonthlyController extends AppController {

    public $uses = array('DistSaleTargetMonth', 'DistDistributor', 'Product', 'Office', 'DistSaleTargetMonth', 'FiscalYear', 'DistSalesRepresentative');

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

        if ($this->request->is('post')) 
		{
			
            $array = array();
            $i = 0;
            foreach ($this->request->data['DistSaleTargetMonth']['target_quantity'] as $key => $value) 
			{
                if (array_key_exists('id', $this->request->data['DistSaleTargetMonth'])):
                    $array[$i]['id'] = $this->request->data['DistSaleTargetMonth']['id'][$key];
                endif;
                $array[$i]['fiscal_year_id'] = $this->request->data['DistSaleTargetMonth']['fiscal_year_id'];
				$array[$i]['month_id'] = $this->request->data['DistSaleTargetMonth']['month_id'];
                $array[$i]['target_type'] = 2;
                $array[$i]['aso_id'] = $this->request->data['DistSaleTargetMonth']['aso_id'];
                //$array[$i]['dist_distributor_id'] = $this->request->data['DistSaleTargetMonth']['dist_distributor_id'];
                $array[$i]['dist_sales_representative_code'] = $key;
                $array[$i]['product_id'] = $this->request->data['DistSaleTargetMonth']['product_id'];
                $array[$i]['target_quantity'] = $value;
                $array[$i]['target_amount'] = $this->request->data['DistSaleTargetMonth']['target_amount'][$key];
				
				$array[$i]['created_at'] = $this->current_datetime();
				$array[$i]['created_by'] = $this->UserAuth->getUserId();
				$array[$i]['updated_at'] = $this->current_datetime();
				$array[$i]['updated_by'] = $this->UserAuth->getUserId();
				
                $i++;
            }
			
			//pr($array);exit;
			
            if ($this->DistSaleTargetMonth->saveAll($array)) {
                $this->Session->setFlash(__('The Distributor Sale Target Base saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The outlet could not be saved. Please, try again.'), 'flash/error');
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
		//echo $current_month_id;
		//exit;
		
		
        $this->set('page_title', 'Distributor Sale Target Area SR Wise (Monthly)');
        $this->DistSaleTargetMonth->recursive = 0;
        $products = $this->Product->find('list', array(
            'conditions' => array('is_distributor_product' => 1,), 
            'order' => array('Product.order' => 'ASC')
        ));
        $this->Office->recursive = 1;
        $saleOffice_list = $this->Office->find('list', array('conditions' => array('Office.office_type_id' => 2)));
        $fiscalYears = $this->DistSaleTargetMonth->FiscalYear->find('list', array('fields' => array('year_code')));
        $this->set(compact('fiscalYears', 'products', 'saletarget', 'saleOffice_list', 'saletargets_list', 'so_name_list', 'months', 'current_month_id'));
    }

    /**
     * admin_add method
     *
     * @return void
     */


    public function admin_get_sales_target_base_wise_data() {
        $this->loadModel('DistSaleTargetMonth');
        $this->loadModel('DistSalesRepresentative');
        $saletargets_list = $this->DistSaleTargetMonth->find('all', array(
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
            'conditions' => array(
                'DistSaleTargetMonth.fiscal_year_id' => $this->request->data('FiscalYearId'),
                'DistSaleTargetMonth.product_id' => $this->request->data('product_id'),
                'DistSalesRepresentative.office_id' => $this->request->data('SaleTargetAsoId'),
				'DistSaleTargetMonth.month_id' => $this->request->data('month_id'),
                'DistSaleTargetMonth.target_type' => 2,
				'DistSaleTargetMonth.dist_sales_representative_code >' => 0,
            ),
			'order' => array('DistSalesRepresentative.id' => 'asc'),
			'recursive' => -1,
            'fields' => array('DistSalesRepresentative.id', 'DistSalesRepresentative.code', 'DistSalesRepresentative.name', 'DistSaleTargetMonth.id', 'DistSaleTargetMonth.aso_id', 'DistSaleTargetMonth.dist_distributor_id', 'DistSaleTargetMonth.target_quantity', 'DistSaleTargetMonth.target_amount', 'DistSaleTargetMonth.product_id', 'DistSaleTargetMonth.fiscal_year_id')
        ));
		
		//echo count($saletargets_list);
		//exit;
		
        /* -----------------Total target_amount and target_quantity----------------------- */
        $saletarget = $this->DistSaleTargetMonth->find('all', array(
            'conditions' => array(
                'DistSaleTargetMonth.fiscal_year_id' => $this->request->data('FiscalYearId'),
                'DistSaleTargetMonth.product_id' => $this->request->data('product_id'),
                'DistSaleTargetMonth.aso_id' => $this->request->data('SaleTargetAsoId'),
                'DistSaleTargetMonth.target_type' => 1,
            ),
            'fields' => array('SUM(DistSaleTargetMonth.target_quantity) as target_quantity', 'SUM(DistSaleTargetMonth.target_amount) as target_amount')
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
        $this->loadModel('DistSaleTargetMonth');
        $this->loadModel('DistDistributor');
        $saletargets_list = $this->DistSaleTargetMonth->find('all', array(
            'joins' => array(
                array(
                    'table' => 'dist_distributors',
                    'alias' => 'DistDistributor',
                    'type' => 'INNER',
                    'conditions' => array(
                        'DistDistributor.id = DistSaleTargetMonth.dist_distributor_id'
                    )
                )
            ),
            'conditions' => array(
                'DistSaleTargetMonth.fiscal_year_id' => $this->request->data('FiscalYearId'),
                'DistSaleTargetMonth.product_id' => $this->request->data('product_id'),
                'DistSaleTargetMonth.aso_id' => $this->request->data('SaleTargetAsoId'),
                'DistSaleTargetMonth.target_type' => 1,
            ),
            'fields' => array('DistDistributor.id', 'DistDistributor.name', 'DistSaleTargetMonth.id', 'DistSaleTargetMonth.aso_id', 'DistSaleTargetMonth.dist_distributor_id', 'DistSaleTargetMonth.target_quantity', 'DistSaleTargetMonth.target_amount', 'DistSaleTargetMonth.product_id', 'DistSaleTargetMonth.fiscal_year_id')
        ));
        /* -----------------Total target_amount and target_quantity----------------------- */
        $saletarget = $this->DistSaleTargetMonth->find('all', array(
            'conditions' => array(
                'DistSaleTargetMonth.fiscal_year_id' => $this->request->data('FiscalYearId'),
                'DistSaleTargetMonth.product_id' => $this->request->data('product_id'),
                'DistSaleTargetMonth.aso_id' => $this->request->data('SaleTargetAsoId'),
                'DistSaleTargetMonth.target_type' => 2,
            ),
            'fields' => array('SUM(DistSaleTargetMonth.target_quantity) as target_quantity', 'SUM(DistSaleTargetMonth.target_amount) as target_amount')
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
        $this->loadModel('DistSaleTargetMonth');
        $this->DistSaleTargetMonth->recursive = -1;
        $saletarget = $this->DistSaleTargetMonth->find('all', array(
            'conditions' => array(
                'DistSaleTargetMonth.fiscal_year_id' => $this->request->data('FiscalYearId'),
                'DistSaleTargetMonth.product_id' => $this->request->data('product_id'),
                'DistSaleTargetMonth.target_type' => 1,
                'DistSaleTargetMonth.aso_id' => $this->request->data('SaleTargetAsoId'),
				'DistSaleTargetMonth.month_id' => $this->request->data('month_id'),
            )
        ));
		//pr($this->request->data);exit;
		
        $array = array();
        if (count($saletarget) > 0) {
            $array['qty'] = $saletarget[0]['DistSaleTargetMonth']['target_quantity'];
            $array['target_amount'] = $saletarget[0]['DistSaleTargetMonth']['target_amount'];
        } else {
            $array['qty'] = 0;
            $array['target_amount'] = 0;
        }
        echo json_encode($array);

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
                $this->DistSaleTargetMonth->recursive = -1;
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
                            $this->redirect(array("controller" => "DistNatioanlSaleTargetsAreaSrWiseMonthly", "action" => "admin_index"));
                        }

                        if (!isset($chk_product_sum['qty'][$product_id['Product']['id']])) {
                            $chk_product_sum['qty'][$product_id['Product']['id']] = 0;
                        }
                        if (!isset($chk_product_sum['target_amount'][$product_id['Product']['id']])) {
                            $chk_product_sum['target_amount'][$product_id['Product']['id']] = 0;
                        }

                        $chk_product_sum['qty'][$product_id['Product']['id']] += $val[5];
                        $chk_product_sum['target_amount'][$product_id['Product']['id']] += $val[6];

                        $saletargets = $this->DistSaleTargetMonth->find('first', array(
                            'conditions' => array(
                                'DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'],
                                'DistSaleTargetMonth.aso_id' => $aso_id['Office']['id'],
                                'DistSaleTargetMonth.dist_sales_representative_code' => $distDistributors['DistSalesRepresentative']['code'],
                                'DistSaleTargetMonth.product_id' => $product_id['Product']['id'],
                                'DistSaleTargetMonth.target_type_id' => 0,
                                'DistSaleTargetMonth.target_type' => 2
                        )));

                        if (empty($saletargets)) {
                            $insert_data['DistSaleTargetMonth']['product_id'] = $product_id['Product']['id'];
                            $insert_data['DistSaleTargetMonth']['target_type'] = 3;
                            $insert_data['DistSaleTargetMonth']['fiscal_year_id'] = $fiscal_year_id['FiscalYear']['id'];

                            $insert_data['DistSaleTargetMonth']['target_quantity'] = $val[5];
                            $insert_data['DistSaleTargetMonth']['target_amount'] = $val[6];

                            $insert_data['DistSaleTargetMonth']['aso_id'] = $aso_id['Office']['id'];
                            $insert_data['DistSaleTargetMonth']['dist_sales_representative_code'] = $distDistributors['DistSalesRepresentative']['code'];
                            $insert_data_array[] = $insert_data;
                        } else {
                            $updated_data['DistSaleTargetMonth']['id'] = $saletargets['DistSaleTargetMonth']['id'];

                            $updated_data['DistSaleTargetMonth']['target_quantity'] = $val[5];
                            $updated_data['DistSaleTargetMonth']['target_amount'] = $val[6];

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
                    $sale_target_area = $this->DistSaleTargetMonth->find('first', array(
                        'conditions' => array(
                            'DistSaleTargetMonth.product_id' => $key,
                            'DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_chk,
                            'DistSaleTargetMonth.aso_id' => $office_id,
                            'DistSaleTargetMonth.target_type' => 2
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
                        if ($sale_target_area['DistSaleTargetMonth']['target_quantity'] < $val) {
                            $is_error = 1;
                            $error_msg .= "Target Quantity is greater than national Qty for product " . $product_name['Product']['name'] . "<br>";
                        }

                        if ($sale_target_area['DistSaleTargetMonth']['target_amount'] < $chk_product_sum['target_amount'][$key]) {
                            $is_error = 1;
                            $error_msg .= "Target Amount is gretter than National Amount for product " . $product_name['Product']['name'] . "<br>";
                        }
                    endif;
                }

                if ($is_error == 0) {
                    if ($insert_data_array) {
                        $this->DistSaleTargetMonth->create();
                        $this->DistSaleTargetMonth->saveAll($insert_data_array);
                    }
                    if ($update_data_array) {
                        $this->DistSaleTargetMonth->saveAll($update_data_array);
                    }
                    $this->Session->setFlash(__('The Distributor Sale Targets has been saved'), 'flash/success');
                    $this->redirect(array("controller" => "DistNatioanlSaleTargetsAreaSrWiseMonthly",
                        "action" => "admin_index"));
                } else {
                    $this->Session->setFlash(__($error_msg), 'flash/error');
                    $this->redirect(array("controller" => "DistNatioanlSaleTargetsAreaSrWiseMonthly",
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
                $this->DistSaleTargetMonth->recursive = -1;
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
                            $this->redirect(array("controller" => "DistNatioanlSaleTargetsAreaSrWiseMonthly", "action" => "admin_index"));
                        }
                        $saletargets = $this->DistSaleTargetMonth->find('first', array(
                            'conditions' => array(
                                'DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'],
                                'DistSaleTargetMonth.aso_id' => $aso_id['Office']['id'],
                                'DistSaleTargetMonth.dist_sales_representative_code' => $territory_id['DistSalesRepresentative']['code'],
                                'DistSaleTargetMonth.product_id' => $product_id['Product']['id'],
                                'DistSaleTargetMonth.target_type_id' => 0,
                                'DistSaleTargetMonth.target_type' => 2
                        )));

                        if (empty($saletargets)) {
                            $this->Session->setFlash(__('The Distributor Sale Target Base wise Not Set'), 'flash/error');
                            $this->redirect(array("controller" => "DistNatioanlSaleTargetsAreaSrWiseMonthly", "action" => "admin_index"));
                        }

                        $saletarget_month = $this->DistSaleTargetMonth->find('first', array(
                            'conditions' => array(
                                'DistSaleTargetMonth.dist_sale_target_id' => $saletargets['DistSaleTargetMonth']['id'],
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
                        if (!isset($chk_product_sum['target_amount'][$product_id['Product']['id']][$territory_id['DistSalesRepresentative']['code']])) {
                            $chk_product_sum['target_amount'][$product_id['Product']['id']][$territory_id['DistSalesRepresentative']['code']] = 0;
                        }

                        $chk_product_sum['qty'][$product_id['Product']['id']][$territory_id['DistSalesRepresentative']['code']] += $val[6];
                        $chk_product_sum['target_amount'][$product_id['Product']['id']][$territory_id['DistSalesRepresentative']['code']] += $val[7];
                        //pr($saletarget_month);
                        //pr($saletargets);
                        //die();
                        if (empty($saletarget_month)) {
                            $insert_data['DistSaleTargetMonth']['dist_sale_target_id'] = $saletargets['DistSaleTargetMonth']['id'];
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
                        $sale_target_base = $this->DistSaleTargetMonth->find('first', array(
                            'conditions' => array(
                                'DistSaleTargetMonth.product_id' => $key_p,
                                'DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_chk,
                                'DistSaleTargetMonth.aso_id' => $office_id,
                                'DistSaleTargetMonth.target_type' => 3,
                                'DistSaleTargetMonth.dist_sales_representative_code' => $key_t
                            ),
                            'recursive' => -1
                        ));
                        // pr($sale_target_base);die;
                        $product_name = $this->Product->find('first', array(
                            'fields' => 'Product.name',
                            'conditions' => array('Product.id' => $key_p),
                            'recursive' => -1
                        ));
                        if ($sale_target_base['DistSaleTargetMonth']['target_quantity'] < $val_t) {
                            $is_error = 1;
                            $error_msg .= "Target Quantity is gretter than national Qty for product " . $product_name['Product']['name'] . "<br>";
                        }

                        if ($sale_target_base['DistSaleTargetMonth']['target_amount'] < $chk_product_sum['target_amount'][$key_p][$key_t]) {
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
                    $this->redirect(array("controller" => "DistNatioanlSaleTargetsAreaSrWiseMonthly",
                        "action" => "admin_index"));
                } else {
                    $this->Session->setFlash(__($error_msg), 'flash/error');
                    $this->redirect(array("controller" => "DistNatioanlSaleTargetsAreaSrWiseMonthly",
                        "action" => "admin_index"));
                }
            }
        }
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
                    $sr_name = $t_data['DistSalesRepresentative']['name'];
                    foreach ($product as $p_data) {
                        $product_name = $p_data['Product']['name'];
                        $sale_target = $this->DistSaleTargetMonth->find('all', array(
                            'conditions' => array('DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_id,
                                'DistSaleTargetMonth.target_type' => 2,
                                'DistSaleTargetMonth.product_id' => $p_data['Product']['id'],
                                'DistSaleTargetMonth.dist_sales_representative_code' => $t_data['DistSalesRepresentative']['code']
                            ),
                            'recursive' => -1
                        ));
                        $qty = 0;
                        $target_amount = 0;
                        if ($sale_target) {
                            $qty = $sale_target[0]['DistSaleTargetMonth']['target_quantity'];
                            $target_amount = $sale_target[0]['DistSaleTargetMonth']['target_amount'];
                        }

                        $table .= '<tr>
                    <td>' . $fiscal_year_code . '</td>
                    <td>' . $month_name . '</td>
                    <td>' . $ofice_name . '</td>
                    <td>' . $sr_name . '</td>
                    <td>' . $product_name . '</td>
                    <td>' . $qty . '</td>
                    <td>' . $target_amount . '</td>
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
