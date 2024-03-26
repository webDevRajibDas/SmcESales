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
class DistSaleTargetsController extends AppController {
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
    public function admin_index($get_fiscal_year_id = null) {
        $this->loadModel('Product');
        if ($this->request->is('post') && $this->request->data['is_submit'] == 'YES') {
            $this->DistSaleTarget->recursive = -1;
            if (!empty($this->request->data['DistSaleTarget'])) {
                $insert_data_array = array();
                $update_data_array = array();
                foreach ($this->request->data['DistSaleTarget']['quantity'] as $key => $val) {
                    $data['DistSaleTarget']['product_id'] = $key;
                    $data['DistSaleTarget']['target_category'] = 1;
                    $data['DistSaleTarget']['fiscal_year_id'] = $this->request->data['DistSaleTarget']['fiscal_year_id'];
                    $data['DistSaleTarget']['amount'] = str_replace(',', '', $this->request->data['DistSaleTarget']['amount'][$key]);
                    $data['DistSaleTarget']['quantity'] = str_replace(',', '', $val);
                    $saletargets = $this->DistSaleTarget->find('first', array('conditions' => array('DistSaleTarget.fiscal_year_id' => $this->request->data['DistSaleTarget']['fiscal_year_id'],
                            'DistSaleTarget.target_type_id' => 0,
                            'DistSaleTarget.target_category' => 1,
                            'DistSaleTarget.product_id' => $key
                    )));
                    if (empty($saletargets)) {
                        $insert_data_array[] = $data;
                        unset($data);
                    } else {
                        $data['DistSaleTarget']['id'] = $saletargets['DistSaleTarget']['id'];
                        $update_data_array[] = $data;
                        unset($data);
                    }
                }
                if ($update_data_array) {
                    if ($this->DistSaleTarget->saveAll($update_data_array)) {
                        $this->Session->setFlash(__('The Distributor Sale Targets has been saved'), 'flash/success');
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash(__('The Distributor Sales could not be saved. Please, try again.'), 'flash/error');
                    }
                }
                if ($insert_data_array) {
                    $this->DistSaleTarget->create();
                    if ($this->DistSaleTarget->saveAll($insert_data_array)) {
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
        $this->DistSaleTarget->recursive = 0;
        $current_year = date("Y");
        $this->loadModel('FiscalYear');
        $this->FiscalYear->recursive = -1;
        $current_year_info = $this->FiscalYear->find('first', array(
            'fields' => array('id'),
            'conditions' => array('YEAR(FiscalYear.created_at)' => $current_year)
        ));
        $this->Product->recursive = 0;
        $current_year_code = $current_year_info['FiscalYear']['id'];
        if (isset($this->request->data['DistSaleTarget']['fiscal_year_id'])) {
            $current_year_code = $this->request->data['DistSaleTarget']['fiscal_year_id'];
        }
        $products = $this->Product->find('all', array('conditions' => array('Product.product_type_id' => '1'), 'order' => array('Product.order' => 'ASC')));
        /* -------- product with sale target -------- */
        $this->DistSaleTarget->unbindModel(
                array('belongsTo' => array('FiscalYear', 'MeasurementUnit', 'Office', 'Territory', 'Product'))
        );
        $product_targets = $this->DistSaleTarget->find('all', array(
            'conditions' => array(
                'DistSaleTarget.fiscal_year_id' => $current_year_code,
                'DistSaleTarget.target_category' => 1,
            ),
                )
        );
        /* ---------- products conbined with sales targets ---------- */
        foreach ($products as $product_key => $product_val) {
            $product_id = $product_val['Product']['id'];
            foreach ($product_targets as $targets_key => $targets_val) {
                if ($product_id == $targets_val['DistSaleTarget']['product_id']) {
                    $products[$product_key]['DistSaleTarget'] = $targets_val['DistSaleTarget'];
                }
            }
        }
        $fiscalYears = $this->FiscalYear->find('list', array('fields' => array('year_code')));
        $this->set(compact('products', 'fiscalYears', 'saletargets', 'current_year_code'));
        /* ----- end selected view data ------ */
    }

    /**
     * admin_get_national_sales_data method
     *
     * @return void
     */
    public function admin_get_national_sales_data() {

        $this->DistSaleTarget->recursive = -1;
        $products = $this->DistSaleTarget->find('all', array(
            'fields' => array('id', 'product_id', 'quantity', 'amount'),
            'conditions' => array('DistSaleTarget.fiscal_year_id' => $this->request->data('FiscalYearId'), 'DistSaleTarget.target_category' => 1)
        ));
        echo json_encode($products);
        $this->autoRender = false;
    }

    public function admin_upload_xl() {
        $this->autoRender = false;
        $this->loadModel('Product');
        $this->loadModel('FiscalYear');
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

                foreach ($temp as $key => $val) {
                    if ($key > 0 && !empty($val[1]) && !empty($val[2]) && !empty($val[3]) && !empty($val[4])) {
                        $fiscal_year_id = $this->FiscalYear->find('first', array(
                            'fields' => array('FiscalYear.id'),
                            'conditions' => array('FiscalYear.year_code LIKE' => '%' . trim($val[1] . '%')),
                            'recursive' => -1
                        ));
                        $product_id = $this->Product->find('first', array(
                            'fields' => 'Product.id',
                            'conditions' => array('lower(Product.name) like' => '%' . strtolower(html_entity_decode($val[2])) . '%'),
                            'recursive' => -1
                        ));


                        if (!$product_id || !$fiscal_year_id) {
                            $this->Session->setFlash(__('The Product id or fiscal year missing or incorrect on line ' . $key), 'flash/error');
                            $this->redirect(array("controller" => "DistSaleTargets", "action" => "admin_index"));
                        }
                        $saletargets = $this->DistSaleTarget->find('first', array('conditions' => array('DistSaleTarget.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'], 'DistSaleTarget.product_id' => $product_id['Product']['id'], 'DistSaleTarget.target_type_id' => 0, 'DistSaleTarget.target_category' => 1)));
                        if (empty($saletargets)) {
                            $insert_data['DistSaleTarget']['product_id'] = $product_id['Product']['id'];
                            $insert_data['DistSaleTarget']['target_category'] = 1;
                            $insert_data['DistSaleTarget']['fiscal_year_id'] = $fiscal_year_id['FiscalYear']['id'];
                            $insert_data['DistSaleTarget']['amount'] = $val[3];
                            $insert_data['DistSaleTarget']['quantity'] = $val[4];
                            $insert_data_array[] = $insert_data;
                        } else {
                            $updated_data['DistSaleTarget']['id'] = $saletargets['DistSaleTarget']['id'];
                            $updated_data['DistSaleTarget']['amount'] = $val[3];
                            $updated_data['DistSaleTarget']['quantity'] = $val[4];
                            $update_data_array[] = $updated_data;
                        }
                    }
                }


                if ($insert_data_array) {
                    $this->DistSaleTarget->create();
                    $this->DistSaleTarget->saveAll($insert_data_array);
                }
                if ($update_data_array) {
                    $this->DistSaleTarget->saveAll($update_data_array);
                }

                $this->Session->setFlash(__('The Distributor Sale Targets has been saved'), 'flash/success');
                $this->redirect(array("controller" => "DistSaleTargets",
                    "action" => "admin_index"));
            }
        }
    }

    public function download_xl($fiscal_year_id = null) {
        $this->loadModel('Product');
        $this->loadModel('FiscalYear');
        $this->loadModel('DistSaleTarget');
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
        <td>Product Name</td>
		<td>Amount</td>
        <td>Quantity</td>
        
    </tr>
    ';
        foreach ($product as $pro_d) {
            $fiscal_year_code = $fiscal_year['FiscalYear']['year_code'];
            $product_name = $pro_d['Product']['name'];
            $sale_target = $this->DistSaleTarget->find('all', array(
                'conditions' => array(
                    'DistSaleTarget.fiscal_year_id' => $fiscal_year_id,
                    'DistSaleTarget.target_category' => 1,
                    'DistSaleTarget.product_id' => $pro_d['Product']['id']),
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
        <td>' . $product_name . '</td>
		<td>' . $amount . '</td>
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
