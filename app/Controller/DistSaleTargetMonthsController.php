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
class DistSaleTargetMonthsController extends AppController {
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
    public function admin_index($get_fiscal_year_id = null) 
	{
		ini_set('memory_limit', '-1');
		
        $this->loadModel('Product');
        if ($this->request->is('post') && $this->request->data['is_submit'] == 'YES') {
            $this->DistSaleTargetMonth->recursive = -1;
            if (!empty($this->request->data['DistSaleTargetMonth'])) {
                $insert_data_array = array();
                $update_data_array = array();
                foreach ($this->request->data['DistSaleTargetMonth']['target_quantity'] as $key => $val) {
                    $data['DistSaleTargetMonth']['product_id'] = $key;
                    //$data['DistSaleTargetMonth']['target_category'] = 1;
                    $data['DistSaleTargetMonth']['fiscal_year_id'] = $this->request->data['DistSaleTargetMonth']['fiscal_year_id'];
					$data['DistSaleTargetMonth']['month_id'] = $this->request->data['DistSaleTargetMonth']['month_id'];
                    $data['DistSaleTargetMonth']['target_amount'] = str_replace(',', '', $this->request->data['DistSaleTargetMonth']['target_amount'][$key]);
                    $data['DistSaleTargetMonth']['target_quantity'] = str_replace(',', '', $val);
					$data['DistSaleTargetMonth']['target_type'] = 0;
					
					$data['DistSaleTargetMonth']['created_at'] = $this->current_datetime();
					$data['DistSaleTargetMonth']['created_by'] = $this->UserAuth->getUserId();
					$data['DistSaleTargetMonth']['updated_at'] = $this->current_datetime();
					$data['DistSaleTargetMonth']['updated_by'] = $this->UserAuth->getUserId();
					
                    $saletargets = $this->DistSaleTargetMonth->find('first', 
						array(
							'conditions' => array(
							'DistSaleTargetMonth.fiscal_year_id' => $this->request->data['DistSaleTargetMonth']['fiscal_year_id'],
                            'DistSaleTargetMonth.month_id' => $this->request->data['DistSaleTargetMonth']['month_id'],
                            'DistSaleTargetMonth.target_type' => 0,
                            'DistSaleTargetMonth.product_id' => $key
                    	)
					));
					
                    if (empty($saletargets)) {
                        $insert_data_array[] = $data;
                        unset($data);
                    } else {
                        $data['DistSaleTargetMonth']['id'] = $saletargets['DistSaleTargetMonth']['id'];
                        $update_data_array[] = $data;
                        unset($data);
                    }
                }
                if ($update_data_array) {
                    if ($this->DistSaleTargetMonth->saveAll($update_data_array)) {
                        $this->Session->setFlash(__('The Distributor Sale Targets has been updated'), 'flash/success');
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash(__('The Distributor Sales could not be saved. Please, try again.'), 'flash/error');
                    }
                }
                if ($insert_data_array) {
                    $this->DistSaleTargetMonth->create();
                    if ($this->DistSaleTargetMonth->saveAll($insert_data_array)) {
                        $this->Session->setFlash(__('The Distributor Sale Targets has been saved'), 'flash/success');
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash(__('The Distributor Sales could not be saved. Please, try again.'), 'flash/error');
                    }
                    $this->request->data['is_submit'] = 'NO';
                }
            }
        }
        /* ----- start selected view data ------ */
        $this->set('page_title', 'Distributor Sale Targets List');
        $this->DistSaleTargetMonth->recursive = 0;
        
		$current_year = date("Y");
        $this->loadModel('FiscalYear');        
		$this->FiscalYear->recursive = -1;
        $current_year_info = $this->FiscalYear->find('first', array(
            'fields' => array('id'),
            'conditions' => array('YEAR(FiscalYear.created_at)' => $current_year)
        ));
		
		//get current month id
		$current_month = date("m");
        $this->loadModel('Month');
		$this->Month->recursive = -1;
        $current_month_info = $this->Month->find('first', array(
            'fields' => array('id'),
            'conditions' => array('month' => $current_month)
        ));
		$months = $this->Month->find('list', array('order' => 'month'));
		$current_month_id = $current_month_info['Month']['id'];
        if (isset($this->request->data['DistSaleTargetMonth']['month_id'])) {
            $current_month_id = $this->request->data['DistSaleTargetMonth']['month_id'];
        }
		//echo $current_month_id;
		//exit;
		
        $this->Product->recursive = 0;
        $current_year_code = $current_year_info['FiscalYear']['id'];
        if (isset($this->request->data['DistSaleTargetMonth']['fiscal_year_id'])) {
            $current_year_code = $this->request->data['DistSaleTargetMonth']['fiscal_year_id'];
        }
		
		
        $products = $this->Product->find('all', array('conditions' => array('Product.product_type_id' => '1' , 'is_distributor_product'=> 1), 'order' => array('Product.order' => 'ASC')));
        /* -------- product with sale target -------- */
        $this->DistSaleTargetMonth->unbindModel(
                array('belongsTo' => array('FiscalYear', 'MeasurementUnit', 'Office', 'Territory', 'Product'))
        );
        $product_targets = $this->DistSaleTargetMonth->find('all', array(
            'conditions' => array(
                'DistSaleTargetMonth.fiscal_year_id' => $current_year_code,
				'DistSaleTargetMonth.month_id' => $current_month_id,
                'DistSaleTargetMonth.target_type' => 0,
            ),
                )
        );
        /* ---------- products conbined with sales targets ---------- */
        foreach ($products as $product_key => $product_val) {
            $product_id = $product_val['Product']['id'];
            foreach ($product_targets as $targets_key => $targets_val) {
                if ($product_id == $targets_val['DistSaleTargetMonth']['product_id']) {
                    $products[$product_key]['DistSaleTargetMonth'] = $targets_val['DistSaleTargetMonth'];
                }
            }
        }
        $fiscalYears = $this->FiscalYear->find('list', array('fields' => array('year_code')));
		
		
		
		
        $this->set(compact('products', 'fiscalYears', 'saletargets', 'current_year_code', 'months', 'current_month_id'));
        /* ----- end selected view data ------ */
    }

    /**
     * admin_get_national_sales_data method
     *
     * @return void
     */
    public function admin_get_national_sales_data() {

        $this->DistSaleTargetMonth->recursive = -1;
        $products = $this->DistSaleTargetMonth->find('all', array(
            'fields' => array('id', 'product_id', 'target_quantity', 'target_amount'),
            'conditions' => array(
			'DistSaleTargetMonth.fiscal_year_id' => $this->request->data('FiscalYearId'),
			'DistSaleTargetMonth.month_id' => $this->request->data('month_id')
			)
        ));
        echo json_encode($products);
        $this->autoRender = false;
    }

    public function admin_upload_xl() {
        $this->autoRender = false;
        $this->loadModel('Product');
        $this->loadModel('FiscalYear');
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

                foreach ($temp as $key => $val) {
                    if ($key > 0 && !empty($val[1]) && !empty($val[2]) && !empty($val[3]) && !empty($val[4])) {
                        
						$fiscal_year_id = $this->FiscalYear->find('first', array(
                            'fields' => array('FiscalYear.id'),
                            'conditions' => array('FiscalYear.year_code LIKE' => '%' . trim($val[1] . '%')),
                            'recursive' => -1
                        ));
						
						$month_info = $this->Month->find('first', array(
                            'fields' => array('Month.id'),
                            'conditions' => array('Month.name LIKE' => '%' . trim($val[2] . '%')),
                            'recursive' => -1
                        ));
						
                        $product_id = $this->Product->find('first', array(
                            'fields' => 'Product.id',
                            'conditions' => array('lower(Product.name) like' => '%' . strtolower(html_entity_decode($val[3])) . '%'),
                            'recursive' => -1
                        ));


                        if (!$product_id || !$fiscal_year_id) {
                            $this->Session->setFlash(__('The Product id or fiscal year missing or incorrect on line ' . $key), 'flash/error');
                            $this->redirect(array("controller" => "DistSaleTargetMonths", "action" => "admin_index"));
                        }
                        $saletargets = $this->DistSaleTargetMonth->find('first', array(
						'conditions' => 
							array(
								'DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'],
								'DistSaleTargetMonth.month_id' => $month_info['Month']['id'],  
								'DistSaleTargetMonth.product_id' => $product_id['Product']['id'], 
								'DistSaleTargetMonth.target_type_id' => 0
								)
						));
						
                        if (empty($saletargets)) {
                            $insert_data['DistSaleTargetMonth']['product_id'] = $product_id['Product']['id'];
                            //$insert_data['DistSaleTargetMonth']['target_category'] = 1;
                            $insert_data['DistSaleTargetMonth']['fiscal_year_id'] = $fiscal_year_id['FiscalYear']['id'];
							$insert_data['DistSaleTargetMonth']['month'] = $month_info['Month']['id'];
                            $insert_data['DistSaleTargetMonth']['target_amount'] = $val[4];
                            $insert_data['DistSaleTargetMonth']['target_quantity'] = $val[5];
                            $insert_data_array[] = $insert_data;
                        } else {
                            $updated_data['DistSaleTargetMonth']['id'] = $saletargets['DistSaleTargetMonth']['id'];
                            $updated_data['DistSaleTargetMonth']['target_amount'] = $val[4];
                            $updated_data['DistSaleTargetMonth']['target_quantity'] = $val[5];
                            $update_data_array[] = $updated_data;
                        }
                    }
                }


                if ($insert_data_array) {
                    $this->DistSaleTargetMonth->create();
                    $this->DistSaleTargetMonth->saveAll($insert_data_array);
                }
                if ($update_data_array) {
                    $this->DistSaleTargetMonth->saveAll($update_data_array);
                }

                $this->Session->setFlash(__('The Distributor Sale Targets has been saved'), 'flash/success');
                $this->redirect(array("controller" => "DistSaleTargetMonths",
                    "action" => "admin_index"));
            }
        }
    }

    public function download_xl($fiscal_year_id = null,$month_id = null) {
        $this->loadModel('Product');
        $this->loadModel('FiscalYear');
		$this->loadModel('Month');
        $this->loadModel('DistSaleTargetMonth');
        $product = $this->Product->find('all', array(
            'conditions' => array('Product.product_type_id' => 1),
            'order' => array('Product.order'),
            'recursive' => -1));
        $fiscal_year = $this->FiscalYear->find('first', array(
            'fields' => array('FiscalYear.id', 'FiscalYear.year_code'),
            'conditions' => array('FiscalYear.id' => $fiscal_year_id),
            'recursive' => -1
        ));
		$month = $this->Month->find('first', array(
            'fields' => array('Month.id', 'Month.name'),
            'conditions' => array('Month.id' => $month_id),
            'recursive' => -1
        ));
        $table = '<table border="1"><tbody>
    <tr>
        <td>Fiscal Year</td>
		<td>Month</td>
        <td>Product Name</td>
		<td>Amount</td>
        <td>Quantity</td>
        
    </tr>
    ';
        foreach ($product as $pro_d) {
            $fiscal_year_code = $fiscal_year['FiscalYear']['year_code'];
			$month_name = $month['Month']['name'];
            $product_name = $pro_d['Product']['name'];
            $sale_target = $this->DistSaleTargetMonth->find('all', array(
                'conditions' => array(
                    'DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_id,
					'DistSaleTargetMonth.month_id' => $month_id,
                    'DistSaleTargetMonth.target_type' => 0,
                    'DistSaleTargetMonth.product_id' => $pro_d['Product']['id']),
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
        <td>' . $product_name . '</td>
		<td>' . $target_amount . '</td>
        <td>' . $qty . '</td>
        
    </tr>
    ';
        }
        $table .= '</tbody></table>';
        header('Content-Type:application/force-download');
        header('Content-Disposition: attachment; filename="sale_target.xls"');
        header("Cache-Control: ");
        header("Pragma: ");
        echo $table;
        $this->autoRender = false;
    }

}
