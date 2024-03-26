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
class NatioanlSaleTargetsAreaWiseController extends AppController
{
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
    public function admin_index()
    {
        $this->set('page_title', 'National Sale Target Area Wise List');
        $current_year = date("Y");
        $this->FiscalYear->recursive = -1;
        $current_year_info = $this->FiscalYear->find('first', array(
            'fields' => array('id'),
            'conditions' => array('YEAR(FiscalYear.created_at)' => $current_year)
        ));
        $fiscal_year_id = $current_year_info['FiscalYear']['id'];
        
        if ($this->request->is('post')) {
            	/*echo "<pre>";
                print_r($this->request->data);
                exit; */
            $fiscal_year_id = $this->request->data['SaleTarget']['fiscal_year_id'];
            $product_id = $this->request->data['SaleTarget']['product_id'];
            $saletarget_list = $this->SaleTarget->find('all', array(
                'fields' => array('SaleTarget.*'),
                'conditions' => array(
                    'AND' => array(
                        array(
                            'SaleTarget.fiscal_year_id' => $fiscal_year_id,
                            'SaleTarget.product_id' => $product_id,
                            'SaleTarget.target_category' => 2,
                            'SaleTarget.target_type_id' => 0,
                        )
                    ))));
            if (empty($saletarget_list)) {
                if (!empty($this->request->data['Office'])) {
                    $this->SaleTarget->create();
                    $data_array = array();
                    foreach ($this->request->data['Office']['SaleTarget']['quantity'] as $key => $val) {
                        $data['SaleTarget']['product_id'] = $this->request->data['SaleTarget']['product_id'];
                        $data['SaleTarget']['aso_id'] = $this->request->data['SaleTarget']['aso_id'][$key];
                        $data['SaleTarget']['target_category'] = 2;
                        $data['SaleTarget']['fiscal_year_id'] = $this->request->data['SaleTarget']['fiscal_year_id'];
                        $data['SaleTarget']['amount'] = str_replace(',', '', $this->request->data['Office']['SaleTarget']['amount'][$key]);
                        $data['SaleTarget']['quantity'] = str_replace(',', '', $val);
                        $data_array[] = $data;
                    }
                    
                    $this->SaleTarget->saveAll($data_array);
                }
            } else {
                if (!empty($this->request->data['Office'])) {
                    $data_array = array();
                    foreach ($this->request->data['Office']['SaleTarget']['quantity'] as $key => $val) {
                        $exiting_data = $this->SaleTarget->find('all', array(
                            'conditions' => array(
                                'SaleTarget.fiscal_year_id' => $this->request->data['SaleTarget']['fiscal_year_id'],
                                'SaleTarget.product_id' => $this->request->data['SaleTarget']['product_id'],
                                'SaleTarget.target_category' => 2,
                                'SaleTarget.aso_id' => $key,
                                'SaleTarget.target_type_id' => 0,
                            ),
                            'fields' => array('saletarget.id')
                        ));
                        if (!empty($exiting_data)) {
                            $data['SaleTarget']['id'] = $exiting_data[0]['saletarget']['id'];
                            $data['SaleTarget']['amount'] = str_replace(',', '', $this->request->data['Office']['SaleTarget']['amount'][$key]);
                            $data['SaleTarget']['target_category'] = 2;
                            $data['SaleTarget']['fiscal_year_id'] = $this->request->data['SaleTarget']['fiscal_year_id'];
                            $data['SaleTarget']['quantity'] = str_replace(',', '', $this->request->data['Office']['SaleTarget']['quantity'][$key]);
                            $this->SaleTarget->save($data);
                            //$data_array[] = $data;
                        }
                    }
                }
            }
            $this->Session->setFlash(__('The Sale Targets has been saved'), 'flash/success');
        }
        
        $this->SaleTarget->recursive = 0;
        $product_options = $this->Product->find('list', array('conditions' => array('Product.product_type_id' => '1'), 'order' => array('Product.order' => 'ASC')));
        if (!empty($this->request->data['SaleTarget']['fiscal_year_id'])) {
            $this->SaleTarget->recursive = -1;
            $total_saletarget_list = $this->SaleTarget->find('first',
                array(
                    'fields' => array('SaleTarget.quantity', 'SaleTarget.amount'),
                    'conditions' => array(
                        'AND' => array(
                            array(
                                'SaleTarget.fiscal_year_id' => $this->request->data['SaleTarget']['fiscal_year_id'],
                                'SaleTarget.product_id' => $this->request->data['SaleTarget']['product_id'],
                                'SaleTarget.target_category' => 1,
                            )
                        )
                    )
                )
            );
        } else {
            $total_saletarget_list['SaleTarget']['quantity'] = 0;
            $total_saletarget_list['SaleTarget']['amount'] = 0;
        }
        
        $this->Office->recursive = -1;
        $office_list = $this->Office->find('all',
            array(
                'conditions' => array('Office.office_type_id' => 2)
            )
        );
        /*--------- start sale target data ----------*/
        if (!empty($product_id)) {
            $targets = $this->SaleTarget->find('all', array(
                'fields' => array('SaleTarget.*'),
                'conditions' => array(
                    'AND' => array(
                        array(
                            'SaleTarget.fiscal_year_id' => $fiscal_year_id,
                            'SaleTarget.product_id' => $product_id,
                            'SaleTarget.target_category' => 2,
                        )
                    ))));
            foreach ($office_list as $office_key => $office_val) {
                $office_id = $office_val['Office']['id'];
                foreach ($targets as $target_key => $target_val) {
                    if ($office_id == $target_val['SaleTarget']['aso_id']) {
                        $office_list[$office_key]['SaleTarget'] = $target_val['SaleTarget'];
                    }
                }
            }
        }
        //$saletarget_list
        /*--------- end sale target data ----------*/
        //echo "<pre>";
        //print_r($saletargets_list);
        //exit;
        $fiscalYears = $this->SaleTarget->FiscalYear->find('list', array('fields' => array('year_code')));
        $this->set(compact('total_saletarget_list', 'fiscalYears', 'product_options', 'office_list', 'fiscal_year_id'));
        //echo "<pre>";
        //print_r($saletargets_list);
        //exit;
    }
    
    /**
     * get_national_target_area_wise_data method
     *
     * @return void
     */
    
    public function admin_get_national_target_area_wise_data()
    {
        //print_r($this->request->data);
        $this->SaleTarget->recursive = -1;
        $saletarget = $this->SaleTarget->find('first', array('fields' => array('quantity', 'amount'),
            'conditions' => array(
                'AND' => array(
                    array(
                        'SaleTarget.fiscal_year_id' => $this->request->data('FiscalYearId'),
                        'SaleTarget.product_id' => $this->request->data('ProductId'),
                        'SaleTarget.target_category' => 1,
                    
                    )
                ))));
        $saletarget_list = $this->SaleTarget->find('all', array('fields' => array('id', 'aso_id', 'product_id', 'quantity', 'amount'),
            'conditions' => array(
                'AND' => array(
                    array(
                        'SaleTarget.fiscal_year_id' => $this->request->data('FiscalYearId'),
                        'SaleTarget.product_id' => $this->request->data('ProductId'),
                        'SaleTarget.target_category' => 2,
                    )
                ))));
        if (empty($saletarget))
            $saletarget['SaleTarget'] = array('');
        
        if (empty($saletarget_list))
            $saletarget_list['SaleTarget'] = array('');
        
        $parent_array = array($saletarget, $saletarget_list);
        // echo '<pre>';
        //print_r($parent_array);
        // echo '</pre>';
        echo json_encode($parent_array);
        $this->autoRender = false;
        
        
    }
    
    public function admin_upload_xl()
    {
        
        $this->loadModel('Product');
        $this->loadModel('FiscalYear');
        $this->loadModel('Office');
        if (!empty($_FILES["file"]["name"])) {
            $target_dir = WWW_ROOT . 'files/';;
            $target_file = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 30);
            $uploadOk = 1;
            $imageFileType = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir . $target_file . '.' . $imageFileType)) {
                $data_ex = new Spreadsheet_Excel_Reader($target_dir . $target_file . '.' . $imageFileType, true);
                $temp = $data_ex->dumptoarray();
                $this->SaleTarget->recursive = -1;
                $insert_data_array = array();
                $update_data_array = array();
                
                // pr($temp);
                // exit;
                
                foreach ($temp as $key => $val) {
                    
                    if ($key > 1 && !empty($val[1]) && !empty($val[2]) && !empty($val[3])) {
                        
                        
                        $fiscal_year_id = $this->FiscalYear->find('first', array(
                            'fields' => array('FiscalYear.id'),
                            'conditions' => array('FiscalYear.year_code LIKE' => '%' . trim($val[1] . '%')),
                            'recursive' => -1
                        ));
                        
                        $product_id = $this->Product->find('first', array(
                            'fields' => 'Product.id',
                            'conditions' => array('lower(Product.name) like' => '%' . strtolower(html_entity_decode($val[3])) . '%'),
                            'recursive' => -1
                        ));
                        
                        //echo $product_id['Product']['id'].$val[5].'<br>';
                        
                        $aso_id = $this->Office->find('first', array(
                            'fields' => 'Office.id',
                            'conditions' => array('lower(Office.office_name) like' => '%' . strtolower($val[2]) . '%'),
                            'recursive' => -1
                        ));
                        
                        //echo $aso_id['Office']['id'].$val[3].'<br>';
                        
                        if (!$product_id && $fiscal_year_id && !$aso_id) {
                            $this->Session->setFlash(__('The Product Name or fiscal year or Offie Name missing or incorrect on line ' . $key), 'flash/error');
                            $this->redirect(array("controller" => "NatioanlSaleTargetsAreaWise", "action" => "admin_index"));
                        }
                        
                        $saletargets = $this->SaleTarget->find('first', array('conditions' => array('SaleTarget.fiscal_year_id' => $fiscal_year_id['FiscalYear']['id'], 'SaleTarget.aso_id' => $aso_id['Office']['id'], 'SaleTarget.product_id' => $product_id['Product']['id'], 'SaleTarget.target_type_id' => 0, 'SaleTarget.target_category' => 2)));
                        //pr($saletargets);
                        
                        if (!isset($chk_product_sum[$aso_id['Office']['id']]['qty'][$product_id['Product']['id']])) {
                            $chk_product_sum[$aso_id['Office']['id']]['qty'][$product_id['Product']['id']] = 0;
                        }
                        if (!isset($chk_product_sum[$aso_id['Office']['id']]['amount'][$product_id['Product']['id']])) {
                            $chk_product_sum[$aso_id['Office']['id']]['amount'][$product_id['Product']['id']] = 0;
                        }
                        
                        $chk_product_sum[$aso_id['Office']['id']]['qty'][$product_id['Product']['id']] += $val[4];
                        
                        $string = $val[5];
                        $val[5] = str_replace(',', '', $string);
                        
                        
                        $chk_product_sum[$aso_id['Office']['id']]['amount'][$product_id['Product']['id']] += $val[5];
                        
                        if (empty($saletargets)) {
                            $insert_data['SaleTarget']['product_id'] = $product_id['Product']['id'];
                            $insert_data['SaleTarget']['target_category'] = 2;
                            $insert_data['SaleTarget']['fiscal_year_id'] = $fiscal_year_id['FiscalYear']['id'];
                            $insert_data['SaleTarget']['quantity'] = $val[4];
                            $insert_data['SaleTarget']['amount'] = $val[5];
                            $insert_data['SaleTarget']['aso_id'] = $aso_id['Office']['id'];
                            $insert_data_array[] = $insert_data;
                        } else {
                            $updated_data['SaleTarget']['id'] = $saletargets['SaleTarget']['id'];
                            $updated_data['SaleTarget']['quantity'] = $val[4];
                            $updated_data['SaleTarget']['amount'] = $val[5];
                            $update_data_array[] = $updated_data;
                        }
                    }
                    
                }
                
                // pr($chk_product_sum[$aso_id['Office']['id']]);
                // exit;
                $is_error = 0;
                $error_msg = '';
                $fiscal_year_chk = $fiscal_year_id['FiscalYear']['id'];
                foreach ($chk_product_sum[$aso_id['Office']['id']]['qty'] as $key => $val) {
                    $sale_target_national = $this->SaleTarget->find('first', array(
                        'conditions' => array(
                            'SaleTarget.product_id' => $key,
                            'SaleTarget.fiscal_year_id' => $fiscal_year_chk,
                            'SaleTarget.target_category' => 1
                        ),
                        'recursive' => -1
                    ));
                    
                    //pr($sale_target_national);
                    
                    $product_name = $this->Product->find('first', array(
                        'fields' => 'Product.name',
                        'conditions' => array('Product.id' => $key),
                        'recursive' => -1
                    ));
                    if ($sale_target_national['SaleTarget']['quantity'] < $val) {
                        
                        $is_error = 1;
                        $error_msg .= "Target Quantity is gretter than national Qty for product " . $product_name['Product']['name'] . "<br>";
                    }
                    
                    if ($sale_target_national['SaleTarget']['amount'] < $chk_product_sum[$aso_id['Office']['id']]['amount'][$key]) {
                        /*echo $aso_id['Office']['id'].'<br>';
                        echo $key.'<br>';
                        echo $sale_target_national['SaleTarget']['amount'].'<br>';
                        echo $chk_product_sum[$aso_id['Office']['id']]['amount'][$key].'<br>';
                        exit;*/
                        
                        $is_error = 1;
                        $error_msg .= "Target Amount is gretter than National Amount for product " . $product_name['Product']['name'] . "<br>";
                    }
                }
                if ($is_error == 0) {
                    
                    if ($insert_data_array) {
                        $this->SaleTarget->create();
                        $this->SaleTarget->saveAll($insert_data_array);
                    }
                    if ($update_data_array) {
                        $this->SaleTarget->saveAll($update_data_array);
                    }
                    
                    
                    $this->Session->setFlash(__('The Sale Targets has been saved'), 'flash/success');
                    $this->redirect(array("controller" => "NatioanlSaleTargetsAreaWise",
                        "action" => "admin_index"));
                } else {
                    $this->Session->setFlash(__($error_msg), 'flash/error');
                    $this->redirect(array("controller" => "NatioanlSaleTargetsAreaWise",
                        "action" => "admin_index"));
                }
                
                
            }
        }
    }
    
    public function download_xl($fiscal_year_id = null)
    {
        $this->Office->recursive = -1;
        $office_list = $this->Office->find('all',
            array(
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
        $table = '<table border="1"><tbody>
		<tr>
			<td>Fiscal Year</td>
			<td>Office Name</td>
			<td>Product Name</td>
			<td>Quantity</td>
			<td>Amount</td>
		</tr>
		';
        foreach ($office_list as $o_data) {
            $fiscal_year_code = $fiscal_year['FiscalYear']['year_code'];
            $ofice_name = $o_data['Office']['office_name'];
            foreach ($product as $p_data) {
                $product_name = $p_data['Product']['name'];
                /*$sale_target = $this->SaleTarget->find('all',array(
                    'conditions'=>array('SaleTarget.fiscal_year_id'=>$fiscal_year_id,
                        'SaleTarget.target_category'=>2,
                        'SaleTarget.product_id'=>$p_data['Product']['id'],
                        'SaleTarget.aso_id'=>$o_data['Office']['id']
                        ),
                    'recursive' => -1
                    ));*/
                $qty = 0;
                $amount = 0;
                /*if($sale_target)
                {
                    $qty=$sale_target[0]['SaleTarget']['quantity'];
                    $amount = $sale_target[0]['SaleTarget']['amount'];
                }*/
                $table .= '<tr>
                    <td>' . $fiscal_year_code . '</td>
                    <td>' . $ofice_name . '</td>
                    <td>' . $product_name . '</td>
					<td>' . $qty . '</td>
					<td>' . $amount . '</td>
                    
                </tr>
                ';
            }
            
        }
        $table .= '</tbody></table>';
        header('Content-Type:application/force-download');
        header('Content-Disposition: attachment; filename="sale_target_area_wise.xls"');
        header("Cache-Control: ");
        header("Pragma: ");
        echo $table;
        $this->autoRender = false;
    }
}	