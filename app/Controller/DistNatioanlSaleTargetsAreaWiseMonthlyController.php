<?php

App::uses('AppController', 'Controller');
App::import('Vendor', 'php-excel-reader/excel_reader2');

ini_set('max_execution_time', 99999);
ini_set('memory_limit', '-1');

/**
 * Sale Targets Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistNatioanlSaleTargetsAreaWiseMonthlyController extends AppController { 

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
    public function admin_index() {
        $this->set('page_title', 'Distributor National Sale Target Area Wise List (Montly)');
        $current_year = date("Y");
        $this->loadModel('FiscalYear');
        $this->loadModel('Product');
        $this->FiscalYear->recursive = -1;
        /* $current_year_info = $this->FiscalYear->find('first', array(
          'fields' => array('id'),
          'conditions' => array('YEAR(FiscalYear.created_at)' => $current_year)
          ));
          $fiscal_year_id = $current_year_info['FiscalYear']['id']; */


        if ($this->request->is('post')) 
		{
            $fiscal_year_id = $this->request->data['DistSaleTargetMonth']['fiscal_year_id'];
			$current_month_id = $this->request->data['DistSaleTargetMonth']['month_id'];
            $product_id = $this->request->data['DistSaleTargetMonth']['product_id'];
            $saletarget_list = $this->DistSaleTargetMonth->find('all', array(
                'fields' => array('DistSaleTargetMonth.*'),
                'conditions' => array(
                    'AND' => array(
                        array(
                            'DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_id,
                            'DistSaleTargetMonth.product_id' => $product_id,
							'DistSaleTargetMonth.month_id' => $this->request->data['DistSaleTargetMonth']['month_id'],
                            //'DistSaleTargetMonth.target_category' => 2,
                            'DistSaleTargetMonth.target_type' => 1,
                        )
            ))));
            if (empty($saletarget_list)) {
                if (!empty($this->request->data['Office'])) 
				{
                    $this->DistSaleTargetMonth->create();
                    $data_array = array();
                    foreach ($this->request->data['Office']['DistSaleTargetMonth']['target_quantity'] as $key => $val) 
					{
                        $data['DistSaleTargetMonth']['product_id'] = $this->request->data['DistSaleTargetMonth']['product_id'];
						$data['DistSaleTargetMonth']['month_id'] = $this->request->data['DistSaleTargetMonth']['month_id'];
                        $data['DistSaleTargetMonth']['aso_id'] = $this->request->data['DistSaleTargetMonth']['aso_id'][$key];
                        //$data['DistSaleTargetMonth']['target_category'] = 2;
						$data['DistSaleTargetMonth']['target_type'] = 1;
                        $data['DistSaleTargetMonth']['fiscal_year_id'] = $this->request->data['DistSaleTargetMonth']['fiscal_year_id'];
                        $data['DistSaleTargetMonth']['target_amount'] = str_replace(',', '', $this->request->data['Office']['DistSaleTargetMonth']['target_amount'][$key]);
                        $data['DistSaleTargetMonth']['target_quantity'] = str_replace(',', '', $val);
						
						$data['DistSaleTargetMonth']['created_at'] = $this->current_datetime();
						$data['DistSaleTargetMonth']['created_by'] = $this->UserAuth->getUserId();
						$data['DistSaleTargetMonth']['updated_at'] = $this->current_datetime();
						$data['DistSaleTargetMonth']['updated_by'] = $this->UserAuth->getUserId();
						
                        $data_array[] = $data;
                    }
                    if ($this->DistSaleTargetMonth->saveAll($data_array)) {
                        $this->Session->setFlash(__('The Distributor National Sale Target Area Wise has been saved'), 'flash/success');
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash(__('The Distributor National Sale Target Area Wise could not be saved. Please, try again.'), 'flash/error');
                    }
                }
            } else {
                if (!empty($this->request->data['Office'])) {
                    $data_array = array();
                    foreach ($this->request->data['Office']['DistSaleTargetMonth']['target_quantity'] as $key => $val) {
                        $exiting_data = $this->DistSaleTargetMonth->find('all', array(
                            'conditions' => array(
                                'DistSaleTargetMonth.fiscal_year_id' => $this->request->data['DistSaleTargetMonth']['fiscal_year_id'],
								'DistSaleTargetMonth.month_id' => $this->request->data['DistSaleTargetMonth']['month_id'],
                                'DistSaleTargetMonth.product_id' => $this->request->data['DistSaleTargetMonth']['product_id'],
                                //'DistSaleTargetMonth.target_category' => 2,
                                'DistSaleTargetMonth.aso_id' => $key,
                                'DistSaleTargetMonth.target_type' => 1,
                            ),
                            'fields' => array('DistSaleTargetMonth.id')
                        ));
                        if (!empty($exiting_data)) {
                            $data['DistSaleTargetMonth']['id'] = $exiting_data[0]['DistSaleTargetMonth']['id'];
                            $data['DistSaleTargetMonth']['target_amount'] = str_replace(',', '', $this->request->data['Office']['DistSaleTargetMonth']['target_amount'][$key]);
                            //$data['DistSaleTargetMonth']['target_category'] = 2;
                            $data['DistSaleTargetMonth']['fiscal_year_id'] = $this->request->data['DistSaleTargetMonth']['fiscal_year_id'];
                            $data['DistSaleTargetMonth']['target_quantity'] = str_replace(',', '', $this->request->data['Office']['DistSaleTargetMonth']['target_quantity'][$key]);
                            $this->DistSaleTargetMonth->save($data);
                            //$data_array[] = $data;
                        }
                    }
                }
            }
            $this->Session->setFlash(__('The Distributor National Sale Target Area Wise has been saved'), 'flash/success');
            $this->redirect(array('action' => 'index'));
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
		
		
        $products = $this->Product->find('list', array(
            'conditions' => array(
                'Product.product_type_id' => 1,
                'Product.is_distributor_product' => 1,
            ),
            'recursive' => -1,
            'order' => 'Product.order ASC',
        ));
        if (!empty($this->request->data['DistSaleTargetMonth']['fiscal_year_id'])) {
            $this->DistSaleTargetMonth->recursive = -1;
            $total_saletarget_list = $this->DistSaleTargetMonth->find('first', array(
                'fields' => array('DistSaleTargetMonth.target_quantity', 'DistSaleTargetMonth.target_amount'),
                'conditions' => array(
                    'AND' => array(
                        array(
                            'DistSaleTargetMonth.fiscal_year_id' => $this->request->data['DistSaleTargetMonth']['fiscal_year_id'],
							'DistSaleTargetMonth.month_id' => $this->request->data['DistSaleTargetMonth']['month_id'],
                            'DistSaleTargetMonth.product_id' => $this->request->data['DistSaleTargetMonth']['product_id'],
                            'DistSaleTargetMonth.target_type' => 0,
                        )
                    )
                )
                    )
            );
        } else {
            $total_saletarget_list['DistSaleTargetMonth']['target_quantity'] = 0;
            $total_saletarget_list['DistSaleTargetMonth']['target_amount'] = 0;
        }

        $this->Office->recursive = -1;
        $office_list = $this->Office->find('all', array(
            'conditions' => array('Office.office_type_id' => 2)
                )
        );
        /* --------- start sale target data ---------- */
        if (!empty($product_id)) {
            $targets = $this->DistSaleTargetMonth->find('all', array(
                'fields' => array('DistSaleTargetMonth.*'),
                'conditions' => array(
                    'AND' => array(
                        array(
                            'DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_id,
							'DistSaleTargetMonth.month_id' => $current_month_id,
                            'DistSaleTargetMonth.product_id' => $product_id,
                            'DistSaleTargetMonth.target_type' => 1,
                        )
            ))));
            foreach ($office_list as $office_key => $office_val) {
                $office_id = $office_val['Office']['id'];
                foreach ($targets as $target_key => $target_val) {
                    if ($office_id == $target_val['DistSaleTargetMonth']['aso_id']) {
                        $office_list[$office_key]['DistSaleTargetMonth'] = $target_val['DistSaleTargetMonth'];
                    }
                }
            }
        }
        /* --------- end sale target data ---------- */
		
		
		
        $fiscalYears = $this->FiscalYear->find('list', array('fields' => array('year_code')));
        $this->set(compact('total_saletarget_list', 'fiscalYears', 'products', 'office_list', 'fiscal_year_id','months','current_month_id'));
    }

    /**
     * get_target_area_wise_monthly_data method
     *
     * @return void
     */
    public function admin_get_target_area_wise_monthly_data() {
        $this->loadModel('DistSaleTargetMonth');
        $this->DistSaleTargetMonth->recursive = -1;
        $saletarget = $this->DistSaleTargetMonth->find('first', array('fields' => array('target_quantity', 'target_amount'),
            'conditions' => array(
                'AND' => array(
                    array(
                        'DistSaleTargetMonth.fiscal_year_id' => $this->request->data('FiscalYearId'),
						'DistSaleTargetMonth.month_id' => $this->request->data('month_id'),
                        'DistSaleTargetMonth.product_id' => $this->request->data('product_id'),
                        'DistSaleTargetMonth.target_type' => 0,
                    )
        ))));
		//pr($saletarget);
		//exit;
		
        $saletarget_list = $this->DistSaleTargetMonth->find('all', array('fields' => array('id', 'aso_id', 'product_id', 'target_quantity', 'target_amount'),
            'conditions' => array(
                'AND' => array(
                    array(
                        'DistSaleTargetMonth.fiscal_year_id' => $this->request->data('FiscalYearId'),
						'DistSaleTargetMonth.month_id' => $this->request->data('month_id'),
                        'DistSaleTargetMonth.product_id' => $this->request->data('product_id'),
                        'DistSaleTargetMonth.target_type' => 1,
                    )
        ))));
        if (empty($saletarget))
            $saletarget['DistSaleTargetMonth'] = array('');

        if (empty($saletarget_list))
            $saletarget_list['DistSaleTargetMonth'] = array('');

        $parent_array = array($saletarget, $saletarget_list);
        // echo '<pre>';
        //print_r($parent_array);
        // echo '</pre>';
        echo json_encode($parent_array);
        $this->autoRender = false;
    }

    public function admin_upload_xl() {
        $this->loadModel('Product');
        $this->loadModel('FiscalYear');
        $this->loadModel('Office');
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
                $insert_data_array = array();
                $update_data_array = array();
				
                foreach ($temp as $key => $val) 
				{
                    if ($key > 1 && !empty($val[1]) && !empty($val[2]) && !empty($val[3])) {

                        $fiscal_year_id = $this->FiscalYear->find('first', array(
                            'fields' => array('FiscalYear.id'),
                            'conditions' => array('FiscalYear.year_code LIKE' => '%' . trim($val[0] . '%')),
                            'recursive' => -1
                        ));
						
						$month_id = $this->Month->find('first', array(
                            'fields' => array('Month.id'),
                            'conditions' => array('Month.name LIKE' => '%' . trim($val[1] . '%')),
                            'recursive' => -1
                        ));
						
						$aso_id = $this->Office->find('first', array(
                            'fields' => 'Office.id',
                            'conditions' => array('lower(Office.office_name) like' => '%' . strtolower($val[2]) . '%'),
                            'recursive' => -1
                        ));

                        $product_id = $this->Product->find('first', array(
                            'fields' => 'Product.id',
                            'conditions' => array('lower(Product.name) like' => '%' . strtolower(html_entity_decode($val[3])) . '%'),
                            'recursive' => -1
                        ));


                        if (!$product_id && $fiscal_year_id && !$aso_id) {
                            $this->Session->setFlash(__('The Product Name or fiscal year or Offie Name missing or incorrect on line ' . $key), 'flash/error');
                            $this->redirect(array("controller" => "DistNatioanlSaleTargetsAreaWiseMonthly", "action" => "admin_index"));
                        }

                        $saletargets = $this->DistSaleTargetMonth->find('first', 
							array('conditions' => array(
								'DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'], 
								'DistSaleTargetMonth.month_id' => $month_id['Month']['id'], 
								'DistSaleTargetMonth.aso_id' => $aso_id['Office']['id'], 
								'DistSaleTargetMonth.product_id' => $product_id['Product']['id'], 
								'DistSaleTargetMonth.target_type' => 1, 
								//'DistSaleTargetMonth.target_category' => 2
								)
							)
						);


                        if (!isset($chk_product_sum[$aso_id['Office']['id']]['qty'][$product_id['Product']['id']])) {
                            $chk_product_sum[$aso_id['Office']['id']]['qty'][$product_id['Product']['id']] = 0;
                        }
                        if (!isset($chk_product_sum[$aso_id['Office']['id']]['target_amount'][$product_id['Product']['id']])) {
                            $chk_product_sum[$aso_id['Office']['id']]['target_amount'][$product_id['Product']['id']] = 0;
                        }

                        $chk_product_sum[$aso_id['Office']['id']]['qty'][$product_id['Product']['id']] += $val[4];

                        $string = $val[5];
                        $val[5] = str_replace(',', '', $string);
                        $chk_product_sum[$aso_id['Office']['id']]['target_amount'][$product_id['Product']['id']] += $val[5];

                        if (empty($saletargets)) {
                            $insert_data['DistSaleTargetMonth']['product_id'] = $product_id['Product']['id'];
                            $insert_data['DistSaleTargetMonth']['target_type'] = 1;
                            $insert_data['DistSaleTargetMonth']['fiscal_year_id'] = $fiscal_year_id['FiscalYear']['id'];
                            $insert_data['DistSaleTargetMonth']['target_quantity'] = $val[4];
                            $insert_data['DistSaleTargetMonth']['target_amount'] = $val[5];
                            $insert_data['DistSaleTargetMonth']['aso_id'] = $aso_id['Office']['id'];
							$insert_data['DistSaleTargetMonth']['month_id'] = $month_id['Month']['id'];
                            $insert_data_array[] = $insert_data;
                        } else {
                            $updated_data['DistSaleTargetMonth']['id'] = $saletargets['DistSaleTargetMonth']['id'];
                            $updated_data['DistSaleTargetMonth']['target_quantity'] = $val[4];
                            $updated_data['DistSaleTargetMonth']['target_amount'] = $val[5];
                            $update_data_array[] = $updated_data;
                        }
                    }
                }

                $is_error = 0;
                $error_msg = '';
                $fiscal_year_chk = $fiscal_year_id['FiscalYear']['id'];

                foreach ($chk_product_sum[$aso_id['Office']['id']]['qty'] as $key => $val) {
                    $sale_target_national = $this->DistSaleTargetMonth->find('first', array(
                        'conditions' => array(
                            'DistSaleTargetMonth.product_id' => $key,
                            'DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_chk,
							'DistSaleTargetMonth.month_id' => $month_id['Month']['id'],
                            'DistSaleTargetMonth.target_type' => 0
                        ),
                        'recursive' => -1
                    ));

                    $product_name = $this->Product->find('first', array(
                        'fields' => 'Product.name',
                        'conditions' => array('Product.id' => $key),
                        'recursive' => -1
                    ));
                    
                    /*
                    pr($fiscal_year_chk);
                    pr($key);
                    pr($sale_target_national);exit;
                    */
                    
                    if ($sale_target_national['DistSaleTargetMonth']['target_quantity'] < $val) {

                        $is_error = 1;
                        $error_msg .= "Distributor Target Quantity is gretter than national Qty for product " . $product_name['Product']['name'] . "<br>";
                    }

                    if ($sale_target_national['DistSaleTargetMonth']['target_amount'] < $chk_product_sum[$aso_id['Office']['id']]['target_amount'][$key]) {
                        /* echo $aso_id['Office']['id'].'<br>';
                          echo $key.'<br>';
                          echo $sale_target_national['SaleTarget']['target_amount'].'<br>';
                          echo $chk_product_sum[$aso_id['Office']['id']]['target_amount'][$key].'<br>';
                          exit; */

                        $is_error = 1;
                        $error_msg .= "Distributor Target Amount is gretter than National Amount for product " . $product_name['Product']['name'] . "<br>";
                    }
                }
                if ($is_error == 0) {

                    if ($insert_data_array) {
                        $this->DistSaleTargetMonth->create();
                        $this->DistSaleTargetMonth->saveAll($insert_data_array);
                    }
                    if ($update_data_array) {
                        $this->DistSaleTargetMonth->saveAll($update_data_array);
                    }


                    $this->Session->setFlash(__('The Sale Targets has been saved'), 'flash/success');
                    $this->redirect(array("controller" => "DistNatioanlSaleTargetsAreaWiseMonthly",
                        "action" => "admin_index"));
                } else {
                    $this->Session->setFlash(__($error_msg), 'flash/error');
                    $this->redirect(array("controller" => "DistNatioanlSaleTargetsAreaWiseMonthly",
                        "action" => "admin_index"));
                }
            }
        }
    }

    public function download_xl($fiscal_year_id = null, $month_id=0) {
        $this->Office->recursive = -1;
        $office_list = $this->Office->find('all', array(
            'conditions' => array('Office.office_type_id' => 2)
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
		
		$this->loadModel('Month');
		$month_info = $this->Month->find('first', array(
            'fields' => array('Month.id', 'Month.name'),
            'conditions' => array('Month.id' => $month_id),
            'recursive' => -1
        ));
		
		
        $table = '<table border="1"><tbody>
		<tr>
			<td>Fiscal Year</td>
			<td>Month</td>
			<td>Office Name</td>
			<td>Product Name</td>
			<td>Quantity</td>
			<td>Amount</td>
		</tr>
		';
        foreach ($office_list as $o_data) {
            $fiscal_year_code = $fiscal_year['FiscalYear']['year_code'];
			$month_name = $month_info['Month']['name'];
            $ofice_name = $o_data['Office']['office_name'];
            foreach ($product as $p_data) {
                $product_name = $p_data['Product']['name'];
                $sale_target = $this->DistSaleTargetMonth->find('all', array(
                    'conditions' => array(
						'DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_id,
						'DistSaleTargetMonth.month_id' => $month_id,
                        'DistSaleTargetMonth.target_type' => 1,
                        'DistSaleTargetMonth.product_id' => $p_data['Product']['id'],
                        'DistSaleTargetMonth.aso_id' => $o_data['Office']['id']
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
                    <td>' . $product_name . '</td>
					<td>' . $qty . '</td>
					<td>' . $target_amount . '</td>
                    
                </tr>
                ';
            }
        }
        $table .= '</tbody></table>';
        header('Content-Type:application/force-download');
        header('Content-Disposition: attachment; filename="sale_target_area_wise_monthly.xls"');
        header("Cache-Control: ");
        header("Pragma: ");
        echo $table;
        $this->autoRender = false;
    }

}
