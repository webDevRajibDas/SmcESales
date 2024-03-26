<?php
App::uses('AppController', 'Controller');
/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
class PrimaryMemoReportsController extends AppController {
/**
 * Components
 *
 * @var array
 */
public $uses = array('PrimaryMemo', 'SalesPerson','Product', 'MeasurementUnit', 'ProductPrice', 'PrimaryMemoDetail', 'MeasurementUnit','ProductCategory', 'Office','ProductType');
public $components = array('Paginator', 'Session', 'Filter.Filter');
/**
 * admin_index method
 *
 * @return void
 */
public function admin_index() {
    ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600); //300 seconds = 5 minutes
        $this->set('page_title', 'PrimaryMemo Report');  
        $request_data = array();
        $report_type = array();
        $product_type_list = $this->ProductType->find('list');
        $this->set(compact('product_type_list'));

    if ($this->request->is('post') || $this->request->is('put')) {
        $this->set('page_title', 'PrimaryMemo Report');

            $requested_data = $this->request->data;
            // pr($requested_data);exit;
            $date_from = $this->data['PrimaryMemoReports']['date_from'];
            $date_to = $this->data['PrimaryMemoReports']['date_to'];
            $product_id = $this->data['PrimaryMemoReports']['product_id'];
            $product_type_id = $this->data['PrimaryMemoReports']['product_type'];
            $this->set(compact('requested_data', 'date_from', 'date_to'));
            $conditions=array();
            $conditions['PrimaryMemo.challan_date BETWEEN ? and ?']=array(date('Y-m-d', strtotime($date_from)), date('Y-m-d', strtotime($date_to)));
            if($product_id){
                $conditions['PrimaryMemoDetail.product_id']=$product_id;
            }else{
                $product_id = ['47','48','339'];
                $conditions['PrimaryMemoDetail.product_id']=$product_id;
            }
            if($product_type_id)
                 $conditions['Product.product_type_id']=$product_type_id;
                
            $conditions['PrimaryMemo.status']=1;

           // echo '<pre>';print_r($conditions);exit;

            $results=$this->PrimaryMemoDetail->find('all',array(
                'conditions'=>$conditions,   
                'fields'=>array(
                    'PrimaryMemoDetail.product_id',
                    'sum(PrimaryMemoDetail.challan_qty) as challan_qty',
                    'count(PrimaryMemo.id) as total_challan_no',
                    'sum(PrimaryMemoDetail.challan_qty*product_price) as value',
                    'sum(
                        ROUND(((PrimaryMemoDetail.challan_qty * product_price) * 100) /  (PrimaryMemoDetail.vat + 100) ,2)) as base_value',
                    'sum(
                        ROUND(
                            (
                                ROUND(((PrimaryMemoDetail.challan_qty * product_price) * 100) /  (PrimaryMemoDetail.vat + 100) ,2) * PrimaryMemoDetail.vat
                            ),0) /  100
                        
                    ) as vat'
                        
                    ),
                   
                'group'=>array('PrimaryMemoDetail.product_id'),
                'recursive'=>0
                ));
                //'sum((PrimaryMemoDetail.challan_qty*product_price) * (100 + PrimaryMemoDetail.vat) / 100) as basevalue',
                $products=array();
                
                $products['Product.id']=[47,48,339];
                if($product_type_id)
                    $products['Product.product_type_id']=$product_type_id;

                $products=$this->Product->find('list',array(
                        'conditions'=>$products,
                        'order'=>array('Product.order'),
                    ));
                $this->set('products', $products);
                
                //pr($products);exit();
             //echo $this->PrimaryMemo->getLastQuery();
             //exit();

            $p_data=array();
                
          // echo '<pre>';print_r($results);exit;
            foreach ($results as  $val) {
                $p_data[$val['PrimaryMemoDetail']['product_id']]=array(
                    "challan_qty"=>$val[0]['challan_qty'],
                    "total_challan_no"=>$val[0]['total_challan_no'],
                    "value"=>$val[0]['value'],
                    "basevalue"=>$val[0]['base_value'],
                    "vat"=>$val[0]['vat'],
                    ); 
            }

            $startchallan = array();
            $endchallasn = array();

            $startchallan =$this->PrimaryMemo->find('first',array(
                'conditions'=>array(
                    'PrimaryMemo.challan_date >='=> date('Y-m-d', strtotime($date_from))
                ),   
                'fields'=>array(
                    'PrimaryMemo.challan_no'
                    ),
                    'order' => array('challan_no' => 'asc'),
                'recursive'=>-1
                ));

                $endchallasn =$this->PrimaryMemo->find('first',array(
                    'conditions'=>array(
                        'PrimaryMemo.challan_date <='=> date('Y-m-d', strtotime($date_to))
                    ),   
                    'fields'=>array(
                        'PrimaryMemo.challan_no'
                        ),
                        'order' => array('challan_no' => 'desc'),
                    'recursive'=>-1
                    ));
    
                
               
          
            $this->set(compact('startchallan', 'endchallasn'));
            $this->set('p_data', $p_data);
    }
}



    function get_product_list()
    {
        $view = new View($this);  
        $form = $view->loadHelper('Form');  
        $product_types=@$this->request->data['PrimaryMemoReports']['product_type'];
        $conditions=array();
        if($product_types)
        {
            $conditions['product_type_id']=$product_types;
        }
        $product_list = $this->Product->find('list', array(
            'conditions' => array(
                'Product.product_type_id' => $product_types,
                'Product.source'=>'SMCEL'
                 ),
            'order'=>  array('order'=>'asc')
            ));
        if($product_list)
        {   
            $form->create('PrimaryMemoReports', array('role' => 'form', 'action'=>'index'))   ;
            echo $form->input('product_id', array('id' => 'product_id', 'label'=>false, 'class' => 'product_id checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required'=> true, 'options'=> $product_list)); 
            $form->end();
        }
        else
        {
            echo '';    
        }
        $this->autoRender = false;
    }

}
