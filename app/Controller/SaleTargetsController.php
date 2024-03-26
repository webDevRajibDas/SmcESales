<?php

App::uses('AppController', 'Controller');
App::import('Vendor', 'php-excel-reader/excel_reader2');
ini_set('max_execution_time', 999999); //300 seconds = 5 minutes
/**
 * Sale Targets Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */



class SaleTargetsController extends AppController {
    public $uses = array('SaleTarget', 'Product', 'Office', 'Territory', 'SaleTargetMonth','FiscalYear','Month');

    public function admin_index($get_fiscal_year_id = null) {
        $this->loadModel('Product');
        if ($this->request->is('post') && $this->request->data['is_submit'] == 'YES') {
            $this->SaleTarget->recursive = -1;
            // $saletargets = $this->SaleTarget->find('first', array('conditions' => array('SaleTarget.fiscal_year_id' => $this->request->data['SaleTarget']['fiscal_year_id'], 'SaleTarget.target_type_id' => 0, 'SaleTarget.target_category' => 1)));
                      //  echo "<pre>";
            //print_r($saletargets);
           // echo "<pre>";
            
           // echo "<pre>";
            //print_r($this->request->data);
            //echo "<pre>";


            if (!empty($this->request->data['SaleTarget'])) {
                $insert_data_array = array();
                $update_data_array = array();
                foreach ($this->request->data['SaleTarget']['quantity'] as $key => $val) {
                    $data['SaleTarget']['product_id'] = $key;
                    $data['SaleTarget']['target_category'] = 1;
                    $data['SaleTarget']['fiscal_year_id'] = $this->request->data['SaleTarget']['fiscal_year_id'];
                    $data['SaleTarget']['amount'] = str_replace(',', '', $this->request->data['SaleTarget']['amount'][$key]);
                    $data['SaleTarget']['quantity'] = str_replace(',', '', $val);
                    $saletargets = $this->SaleTarget->find('first', 
                        array('conditions' => array('SaleTarget.fiscal_year_id' => $this->request->data['SaleTarget']['fiscal_year_id'], 
                            'SaleTarget.target_type_id' => 0, 
                            'SaleTarget.target_category' => 1,
                            'SaleTarget.product_id'=>$key
                            )));
                    // pr($saletargets);die;
                    if (empty($saletargets))
                    {
                        $insert_data_array[] = $data;
                         unset($data);
                    }
                    else
                    {
                        $data['SaleTarget']['id']=$saletargets['SaleTarget']['id'];
                        $update_data_array[] = $data;
                        unset($data);
                    }
             }
                   /* echo '<pre>';
                    print_r($update_data_array);
                    print_r($insert_data_array);
                    exit;*/
             if($update_data_array)
             {
               $this->SaleTarget->saveAll($update_data_array);
           }
           if($insert_data_array)
           {
               $this->SaleTarget->create();
               $this->SaleTarget->saveAll($insert_data_array);
               $this->request->data['is_submit'] = 'NO';
           }
          

       }

       $this->Session->setFlash(__('The Sale Targets has been saved'), 'flash/success');
   }
   /* ----- start selected view data ------ */
   $this->set('page_title', 'Sale Targets List');
   $this->SaleTarget->recursive = 0;
   $current_year = date("Y");
   $this->loadModel('FiscalYear');
   $this->FiscalYear->recursive = -1;
   $current_year_info = $this->FiscalYear->find('first', array(
    'fields' => array('id'),
    'conditions' => array('YEAR(FiscalYear.created_at)' => $current_year)
    ));
   $this->Product->recursive = 0;
   $current_year_code = $current_year_info['FiscalYear']['id'];
   if (isset($this->request->data['SaleTarget']['fiscal_year_id'])) {
    $current_year_code = $this->request->data['SaleTarget']['fiscal_year_id'];
}
$products = $this->Product->find('all', array('conditions'=>array('Product.product_type_id'=>'1'), 'order' => array('Product.order' => 'ASC')));
/* -------- product with sale target -------- */
$this->SaleTarget->unbindModel(
    array('belongsTo' => array('FiscalYear', 'MeasurementUnit', 'Office', 'Territory', 'Product'))
    );
$product_targets = $this->SaleTarget->find('all', array(
    'conditions' => array(
        'SaleTarget.fiscal_year_id' => $current_year_code,
        'SaleTarget.target_category' => 1,
        ),
    )
);
/* ---------- products conbined with sales targets ---------- */
foreach ($products as $product_key => $product_val) {
    $product_id = $product_val['Product']['id'];
    foreach ($product_targets as $targets_key => $targets_val) {
        if ($product_id == $targets_val['SaleTarget']['product_id']) {
            $products[$product_key]['SaleTarget'] = $targets_val['SaleTarget'];
        }
    }
}

        //if($redirect)
        //$this->redirect(array('action' => 'index'));
        //echo '<pre>';
        //print_r($products);
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

        $this->SaleTarget->recursive = -1;
        $products = $this->SaleTarget->find('all', array(
            'fields' => array('id', 'product_id', 'quantity', 'amount'),
             'conditions' => array('SaleTarget.fiscal_year_id' => $this->request->data('FiscalYearId'),'SaleTarget.target_category'=>1)
             ));
        echo json_encode($products);
        $this->autoRender = false;
    }

    public function admin_set_monthly_target($product_id = null, $target_id = null, $fiscal_year_id = null) {
        $this->loadModel('Territory');
        $this->loadModel('Product');
        $this->loadModel('Month');
        $this->loadModel('SaleTargetMonth');
        $this->loadModel('SalesPeople');
        $this->loadModel('TerritoryPerson');
        $this->loadModel('SaleTarget');
        $this->loadModel('Office');
        $this->set('page_title', 'Monthly Sale Target');
        $this->SaleTarget->recursive = 0;
        $this->SaleTargetMonth->recursive = 0;

        $product_options = $this->Product->find('list', array('order' => array('Product.order' => 'ASC')));
        $this->set('product_id', $product_id);
        $this->set('fiscal_year_id', $fiscal_year_id);
        $this->set('sale_target_id', $target_id);


        $this->Office->recursive = -1;

        $fiscalYears = $this->SaleTarget->FiscalYear->find('list', array('fields' => array('year_code')));
        /* ---------- start new ---------- */
        $month_list = $this->Month->find('list', array('fields' => array('Month.id', 'Month.name')));
        $this->set(compact('fiscalYears', 'product_options','month_list'));


       //  echo '<pre>';
       //  print_r($this->request->data);
       //  echo '</pre>';
       // die();
        if ($this->request->is('post') || $this->request->is('put')) {
            $territory_quantity=$this->request->data['SaleTargetMonth']['t_quantity'];
            $territory_amount=$this->request->data['SaleTargetMonth']['t_amount'];
            $product_id=$product_id;
            $fiscal_year_id= $fiscal_year_id;
            $target_id=$this->request->data['SaleTargetMonth']['sale_target_id'];
            foreach ($this->request->data['SaleTargetMonth']['quantity'] as $key => $val) {
                $single_row = array();
                $single_row['month_id'] = $key;

                $single_row['target_quantity'] = $val;
                $single_row['target_amount'] = $this->request->data['SaleTargetMonth']['amount'][$key];
 
                $single_row['fiscal_year_id'] = $fiscal_year_id;
                $single_row['product_id'] = $product_id;
                $single_row['sale_target_id'] = $target_id;
               
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
            $this->set('sale_target_id', $target_id);


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
                'SaleTarget.id' => $target_id
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

//working



    public function admin_upload_xl(){
        ini_set('max_execution_time', 999999); //300 seconds = 5 minutes
        $this->autoRender=false;
        $this->loadModel('Product');
        $this->loadModel('FiscalYear');
        if(!empty($_FILES["file"]["name"])) {
            $target_dir = WWW_ROOT.'files/';
            $target_file = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 30);
            $uploadOk = 1;
            $imageFileType = pathinfo($_FILES["file"]["name"],PATHINFO_EXTENSION);

            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir.$target_file.'.'.$imageFileType)) {
                $data_ex = new Spreadsheet_Excel_Reader($target_dir.$target_file.'.'.$imageFileType, true);
                //$this->dd($data_ex);
                $temp = $data_ex->dumptoarray();
                $this->SaleTarget->recursive = -1;
                $insert_data_array = array();
                $update_data_array = array();

				//pr($temp);
				//exit;

                foreach ($temp as $key => $val) 
                {                   
                    if($key > 1 && !empty($val[1]) && !empty($val[2]) && !empty($val[3]) && !empty($val[4]))
                    {
                        $fiscal_year_id = $this->FiscalYear->find('first',array(
                            'fields'=>array('FiscalYear.id'),
                            'conditions'=>array('FiscalYear.year_code LIKE'=>'%'.trim($val[1].'%')),
                            'recursive'=>-1
                            ));
                        $product_id =  $this->Product->find('first',array(
                            'fields'=>'Product.id',
                            'conditions'=>array('lower(Product.name) like'=>'%'.strtolower(html_entity_decode($val[2])).'%'),
                            'recursive'=>-1
                            ));						
						
							
                        if(!$product_id || !$fiscal_year_id)
                        {
                            $this->Session->setFlash(__('The Product id or fiscal year missing or incorrect on line '.$key), 'flash/error');
                            $this->redirect(array("controller" => "SaleTargets","action" => "admin_index"));
                        }
                        $saletargets = $this->SaleTarget->find('first', array('conditions' => array('SaleTarget.fiscal_year_id'=>$fiscal_year_id['FiscalYear']['id'], 'SaleTarget.product_id' => $product_id['Product']['id'],'SaleTarget.target_type_id' => 0, 'SaleTarget.target_category' => 1)));
                        if(empty($saletargets)){
                         $insert_data['SaleTarget']['product_id'] = $product_id['Product']['id'];
                         $insert_data['SaleTarget']['target_category'] = 1;
                         $insert_data['SaleTarget']['fiscal_year_id'] =  $fiscal_year_id['FiscalYear']['id'];
                         $insert_data['SaleTarget']['amount'] = $val[3];
                         $insert_data['SaleTarget']['quantity'] = $val[4];
                         $insert_data_array[] = $insert_data;
                     }
                     else 
                     {
                         $updated_data['SaleTarget']['id'] = $saletargets['SaleTarget']['id'];
                         $updated_data['SaleTarget']['amount'] = $val[3];
                         $updated_data['SaleTarget']['quantity'] = $val[4];
                         $update_data_array[] = $updated_data;
                     }
                 }

             }
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
           $this->redirect(array("controller" => "SaleTargets", 
              "action" => "admin_index"));


       }
   }
}
public function download_xl($fiscal_year_id= null)
{
    $this->loadModel('Product');
    $this->loadModel('FiscalYear');
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
        <td>Product Name</td>
		<td>Amount</td>
        <td>Quantity</td>
        
    </tr>
    ';
    foreach($product as $pro_d)
    {
        $fiscal_year_code = $fiscal_year['FiscalYear']['year_code'];
        $product_name = $pro_d['Product']['name'];
        /*$sale_target = $this->SaleTarget->find('all',array(
        'conditions'=>array('SaleTarget.fiscal_year_id'=>$fiscal_year_id,'SaleTarget.target_category'=>1,'SaleTarget.product_id'=>$pro_d['Product']['id']),
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
            <td>'.$product_name.'</td>
    		<td>'.$amount.'</td>
            <td>'.$qty.'</td>
        
        </tr>
        ';
    }
    $table.='</tbody></table>';
    header('Content-Type:application/force-download');
    header('Content-Disposition: attachment; filename="sale_target.xls"');
    header("Cache-Control: ");
    header("Pragma: ");
    echo $table;
    $this->autoRender=false;
}

public function download_xl_month($fiscal_year_id=null)
{
    $this->loadModel('Month');
    $this->Office->recursive = -1;
    $product = $this->Product->find('all',array(
        'conditions'=>array('Product.product_type_id'=>1),
        'order'=>array('Product.order'),
        'recursive'=>-1));
    $all_month = $this->Month->find('all',array(
        /*'conditions'=>array('Month.fiscal_year_id'=>$fiscal_year_id),*/
        'order'=>array('CONVERT(int,Month.month)'),
        'recursive'=>-1
        ));
    $table='<table border="1"><tbody>
    <tr>
        <td rowspan="2">Product</td>';
    foreach($all_month as $m_data)
    {
        $table.='<td colspan="2" style="text-align:center;">'.ucfirst(substr($m_data['Month']['name'],0,3)).'</td>';
    }  

    $table.='</tr>';

    $table.='<tr>';
    foreach($all_month as $m_data)
    {
        $table.='<td>Qty</td>';
        $table.='<td>Amount</td>';
    }  

   $table.='</tr>';
    foreach($product as $p_data)
    {
        $product_name = $p_data['Product']['name'];
        $qty=0;
        $amount = 0;
        $table.='<tr>
        <td>'.$product_name.'</td>';
        foreach($all_month as $m_data)
        {
            $table.='<td>'.$qty.'</td>';
            $table.='<td>'.$amount.'</td>';
        }  
        $table.='</tr>';
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

public function admin_upload_xl_month($fiscal_year_id=0)
{

    $this->loadModel('Product');
    $this->loadModel('FiscalYear');
    /*$this->loadModel('Office');
    $this->loadModel('Territory');*/
    $this->loadModel('Month');

    if(!$fiscal_year_id)
    {
        $this->Session->setFlash(__('pleae select fiscal year first'), 'flash/error');
        $this->redirect(array("controller" => "SaleTargets","action" => "admin_index"));
    }

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
            // pr($temp);
            $this->SaleTargetMonth->recursive = -1;
            $this->SaleTarget->recursive = -1;
            $insert_data_array = array();
            $update_data_array = array();
            $month=array();
            $i=0;
            foreach ($temp[1] as $key => $val) 
            {
                if($key > 1 )
                {
                    if(!$val && !empty($month[$i-1]))
                    {
                        $month[$i-1]['amount']=ROUND($key,2);
                    }
                    if($val)
                    {
                        $month[$i]=array(
                                    'month'=>$val,
                                    'qty'=>ROUND($key,0),
                                );
                    }
                    
                }
                $i++;
                
            }
            // $xl_entry_qty_array=array();
            // pr($month);exit;
            foreach ($temp as $key => $val) 
            {
                if($key >2 && !empty($val[1]))
                {
                    //echo $val[2];exit;
                    $product_id =  $this->Product->find('first',array(
                        'fields'=>array('Product.id','Product.name'),
                        'conditions'=>array('lower(Product.name) like'=>'%'.strtolower(html_entity_decode($val[1])).'%'),
                        'recursive'=>-1
                        ));
                    if(!$product_id)
                    {
                        $this->Session->setFlash(__('The Product Name  incorrect on line '.$key), 'flash/error');
                        $this->redirect(array("controller" => "SaleTargets","action" => "admin_index"));
                    }
                    foreach($month as $month_info)
                    {
                        $month_id = $this->Month->find('first',array(
                            'fields'=>'Month.id',
                            'conditions'=>array(/*'Month.fiscal_year_id'=>$fiscal_year_id['FiscalYear']['id'],*/'lower(Month.name) like'=>'%'.strtolower($month_info['month']).'%'),
                            'recursive'=>-1
                            ));
                        $saletargets = $this->SaleTarget->find('first', array(
                            'conditions' => array(
                                'SaleTarget.fiscal_year_id' => $fiscal_year_id,
                                /*'SaleTarget.aso_id' => 0,
                                'SaleTarget.territory_id' => 0, */
                                'SaleTarget.product_id' => $product_id['Product']['id'],
                                'SaleTarget.target_type_id' => 0, 'SaleTarget.target_category' => 1)
                            ));
                        if(empty($saletargets))
                        {
                            $this->Session->setFlash(__('The Sale Target Base wise Not Set For '.$product_id['Product']['name']), 'flash/error');
                            $this->redirect(array("controller" => "SaleTargets","action" => "admin_index"));
                        }
                        $saletarget_month=$this->SaleTargetMonth->find('first',array(
                            'conditions'=>array(
                                'SaleTargetMonth.sale_target_id'=>$saletargets['SaleTarget']['id'],
                                'SaleTargetMonth.product_id' => $product_id['Product']['id'],
                                'SaleTargetMonth.month_id' => $month_id['Month']['id'],
                                'SaleTargetMonth.fiscal_year_id' => $fiscal_year_id,
                                'SaleTargetMonth.aso_id' =>0,
                                'SaleTargetMonth.territory_id' => 0,
                                )
                            ));
                        $xl_entry_qty=0;
                        
                        $xl_entry_amount=0;
                        if(isset($month_info['qty']))
                        {
                            $xl_entry_qty=$val[$month_info['qty']]?ROUND($val[$month_info['qty']],0):0;
                            // $xl_entry_qty_array[$product_id['Product']['id']][]=$xl_entry_qty;
                        }
                        if(isset($month_info['amount']))
                        {
                            $xl_entry_amount=$val[$month_info['amount']]?ROUND($val[$month_info['amount']],2):0;
                        }

                        if(!isset($chk_product_sum['qty'][$product_id['Product']['id']]))
                        {
                            $chk_product_sum['qty'][$product_id['Product']['id']]=0;
                        }
                        if(!isset($chk_product_sum['amount'][$product_id['Product']['id']]))
                        {
                            $chk_product_sum['amount'][$product_id['Product']['id']]=0;
                        }

                        $chk_product_sum['qty'][$product_id['Product']['id']]+=$xl_entry_qty;
                        $chk_product_sum['amount'][$product_id['Product']['id']]+=$xl_entry_amount;

                        if(empty($saletarget_month))
                        {

                            $insert_data['SaleTargetMonth']['sale_target_id'] = $saletargets['SaleTarget']['id'];
                            $insert_data['SaleTargetMonth']['product_id'] = $product_id['Product']['id'];
                            $insert_data['SaleTargetMonth']['month_id'] = $month_id['Month']['id'];
                            $insert_data['SaleTargetMonth']['fiscal_year_id'] = $fiscal_year_id;

                            $insert_data['SaleTargetMonth']['target_quantity'] = $xl_entry_qty;
                            $insert_data['SaleTargetMonth']['target_amount'] = $xl_entry_amount;

                            $insert_data['SaleTargetMonth']['aso_id'] =0;
                            $insert_data['SaleTargetMonth']['territory_id'] = 0;
                            $insert_data['SaleTargetMonth']['target_type'] = 0;
                            $insert_data['SaleTargetMonth']['session'] = 0;
                            $insert_data_array[]= $insert_data;
                        }
                        else 
                        {
                            $updated_data['SaleTargetMonth']['id'] = $saletarget_month['SaleTargetMonth']['id'];

                            $updated_data['SaleTargetMonth']['target_quantity'] = $xl_entry_qty;
                            $updated_data['SaleTargetMonth']['target_amount'] = $xl_entry_amount;

                            $update_data_array[] = $updated_data;
                        }
                    }
                    
                }
            }

            /*pr($insert_data_array);
            pr($update_data_array);
            pr($chk_product_sum);exit;*/
            $is_error=0;
            $error_msg='';
            $fiscal_year_chk=$fiscal_year_id;
            /*$office_id = $aso_id['Office']['id'];*/
            foreach($chk_product_sum['qty'] as $key_p=>$val_p)
            {
                /*foreach($val_p as $key_t=>$val_t)
                {*/
                    $sale_target_base = $this->SaleTarget->find('first',array(
                        'conditions'=>array(
                            'SaleTarget.product_id'=>$key_p,
                            'SaleTarget.fiscal_year_id'=>$fiscal_year_chk,
                            /*'SaleTarget.aso_id'=>0,*/
                            'SaleTarget.target_category'=>1,
                            /*'SaleTarget.territory_id' => 0*/
                            ),
                        'recursive'=>-1
                        ));
                    // pr($sale_target_base);die;
                    $product_name = $this->Product->find('first',array(
                        'fields'=>'Product.name',
                        'conditions'=>array('Product.id'=>$key_p),
                        'recursive'=>-1
                        ));
                    if($sale_target_base['SaleTarget']['quantity'] < $val_p)
                    {
                        $is_error=1;
                        $error_msg .="Target Quantity is gretter than national Qty for product ".$product_name['Product']['name']."<br>";
                          // $error_msg .="Target Quantity is gretter than national Qty for product ".$product_name['Product']['name']."-- Total Target Qty= ".$val_p." -- Full Year Set Qty= ".$sale_target_base['SaleTarget']['quantity'].join(",",$xl_entry_qty_array[$key_p])."<br>";
                    }

                    if($sale_target_base['SaleTarget']['amount'] < $chk_product_sum['amount'][$key_p])
                    {
                        $is_error=1;
                        $error_msg .="Target Amount is gretter than National Amount for product ".$product_name['Product']['name']."<br>";
                         // $error_msg .="Target Amount is gretter than National Amount for product ".$product_name['Product']['name']."-- Total Target amount= ".$chk_product_sum['amount'][$key_p]." -- Full Year Set Amount= ".$sale_target_base['SaleTarget']['amount']."<br>";
                    }
                /*}*/
            }
            if($is_error == 0)
            {
                if($insert_data_array)
                {   
                    //pr($insert_data_array);die();         
                    $this->SaleTargetMonth->create();
                    $this->SaleTargetMonth->saveAll($insert_data_array);
                }
                if($update_data_array)
                {
                //pr($update_data_array);die();
                    $this->SaleTargetMonth->saveAll($update_data_array);
                }
                $this->Session->setFlash(__('The Sale Targets monthly has been saved'), 'flash/success');
                $this->redirect(array("controller" => "SaleTargets", 
                    "action" => "admin_index"));

            }
            else
            {
                $this->Session->setFlash(__($error_msg), 'flash/error');
                $this->redirect(array("controller" => "SaleTargets", 
                    "action" => "admin_index"));
            }

        }
    }
    else
    {

            
        $this->Session->setFlash('No File Selected', 'flash/error');
        $this->redirect(array("controller" => "SaleTargets", 
            "action" => "admin_index"));
            
    }
}



    public function admin_territory_wise_upload(){


        $this->loadModel('Product');
        $this->loadModel('FiscalYear');
        $this->loadModel('Office');
        $this->loadModel('Territory');
        $this->loadModel('Month');

        $target_file = WWW_ROOT . 'files\ExcelWorksheet.xls';

        if (file_exists($target_file)) {
            $data_ex = new Spreadsheet_Excel_Reader($target_file,true);
            $tempData = $data_ex->dumptoarray();
            //$this->dd($tempData);
            $isSavingAll = 0;
            $sumByTerritori = array();
            for($i = 2; $i<count($tempData); $i++) {
                $totalQty = $tempData[$i][6];
                $totalAmount = $tempData[$i][7];

                $fiscal_year = $this->FiscalYear->find('first', array(
                    'fields' => array('FiscalYear.id'),
                    'conditions' => array('FiscalYear.year_code LIKE' => '%' . trim($tempData[$i][1] . '%')),
                    'recursive' => -1
                ));

                $fiscal_year_id = $fiscal_year['FiscalYear']['id'];
                $month = $this->Month->find('first',array(
                    'fields'=>'Month.id',
                    'conditions'=>array('lower(Month.name) like'=>'%'.strtolower($tempData[$i][2]).'%'),
                    'recursive'=>-1
                ));
                $mid = $month['Month']['id'];

                $aso = $this->Office->find('first', array(
                    'fields' => 'Office.id',
                    'conditions' => array('lower(Office.office_name) like' => '%' . strtolower($tempData[$i][3]) . '%'),
                    'recursive' => -1
                ));

                $aso_id = $aso['Office']['id'];

                $territory = $this->Territory->find('first', array(
                    'fields' => 'Territory.id',
                    'conditions' => array('lower(Territory.name) like' => '%' . strtolower(html_entity_decode($tempData[$i][4])) . '%'),
                    'recursive' => -1
                ));
                $tid = $territory['Territory']['id'];
                
                $product = $this->Product->find('first', array(
                    'fields' => array('Product.id', 'Product.name'),
                    'conditions' => array('lower(Product.name) like' => '%' . strtolower(html_entity_decode($tempData[$i][5])) . '%'),
                    'recursive' => -1
                ));
                $pid = $product['Product']['id'];

                //echo 'Fiscal year id : '.$fiscal_year_id.'--'. 'Month id: '.$mid.'--'.'Office id: '.$aso_id.'--' .'Territory id :'.$tid.'--'. 'Product id : '.$pid.'Quantity : '.$totalQty.'Value'.$totalAmount ;
                $sumByTerritori['totalQty'] += $totalQty;


            }
            
            $this->dd($sumByTerritori);

           
        }
    }

}
