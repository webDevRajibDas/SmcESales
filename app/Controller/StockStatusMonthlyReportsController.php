<?php

App::uses('AppController', 'Controller');

/**
* CurrentInventories Controller
*
* @property CurrentInventory $CurrentInventory
* @property PaginatorComponent $Paginator
*/
class StockStatusMonthlyReportsController extends AppController {

  /**
  * Components
  *
  * @var array
  */
  public $components = array('Paginator', 'Session', 'Filter.Filter');
  public $uses = array('CurrentInventory', 'Store','ProductType','Office','RptDailyTranBalance','Challan','ReturnChallan','Memo');

  public function admin_index() {

  ini_set('memory_limit', '-1');
  ini_set('max_execution_time', 300); //300 seconds = 5 minutes

  $this->set('page_title', 'Current Inventories');

  $this->CurrentInventory->recursive = 1;
  $this->loadModel('Store');
  $this->loadModel('InventoryStatuses');
  $this->loadModel('SalesPerson');
  $this->loadModel('Product');



  $unit_type = array(
    '1'=>'Base Unit',
    /*'2'=>'Sale Unit'*/
    );
  $this->set(compact('unit_type'));



  $product_list = $this->Product->find('list', array(
    'conditions'=>array('NOT' => array('Product.product_category_id'=>32), 'is_active' => 1),
    'order'=>  array('order'=>'asc')
    ));

  $this->set(compact('product_list'));
  $this->Session->write('product_list', $product_list);

  $office_parent_id = $this->UserAuth->getOfficeParentId();

  if ($office_parent_id == 0) {
    $conditions = array('CurrentInventory.inventory_status_id' => 1);
    $storeCondition = array('Office.office_type_id'=>2, "NOT" => array( "id" => array(30, 31, 37)));
  } else {
    $conditions = array('CurrentInventory.store_id' => $this->UserAuth->getStoreId(), 'CurrentInventory.inventory_status_id' => 1);
    $storeCondition = array('Office.id' => $this->UserAuth->getOfficeId());
  }
  $offices = $this->Office->find('list', array('conditions' => $storeCondition));

  $this->set(compact('offices', 'inventoryStatuses', 'productCategories'));

  if($this->request->is('post'))
  {
    // pr($this->request->data());exit;
    $office_id=$this->request->data['search']['office_id'];
    $so_list_r = $this->SalesPerson->find('all', array(
      'fields' => array('SalesPerson.id','SalesPerson.name', 'Territory.name'),
      'conditions' => array(
        'SalesPerson.office_id' => $office_id,
        'SalesPerson.territory_id >' => 0,
        'User.user_group_id' => 4,
        ),
      'recursive'=> 0
      )); 


    foreach($so_list_r as $key => $value)
    {
      $so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'].' ('.$value['Territory']['name'].')';
    }
    $this->set(compact('so_list'));

    $request_data = $this->request->data;
    $this->set(compact('request_data'));
    $this->Session->write('request_data', $request_data);

    if(!empty($this->request->data['search']['product_id']))
    {
      $p_condition = array('NOT'=>array('Product.product_category_id'=>32), 'Product.id' => $this->request->data['search']['product_id'],'Product.product_type_id'=>1);
    }
    else
    {
      $p_condition = array('NOT'=>array('Product.product_category_id'=>32),'Product.product_type_id'=>1);
    }

    $products = $this->CurrentInventory->Product->find('all',array(
      'fields'=>array('Product.name','Product.id','MU.name as mes_name','Product.product_category_id'),
      'joins'=>array(
        array(
          'table'=>'measurement_units',
          'alias'=>'MU',
          'type' => 'LEFT',
          'conditions'=>array('MU.id= Product.sales_measurement_unit_id')
          )
        ),
      'conditions'=> $p_condition,
      'order'=>'Product.order',
      'recursive'=>-1
      ));



    $this->set(compact('products'));
    $this->Session->write('products', $products);


    /*$inv_report_start_date =date('Y-m-d',strtotime($this->request->data['search']['date_from']));
    $inv_report_end_date =  date('Y-m-d',strtotime($this->request->data['search']['date_to'])) ;*/ 
    $inv_report_start_date =date('F - Y',strtotime($this->request->data['search']['date_from']));
    $conditions=array('Store.store_type_id' => 3, 'Store.office_id' =>$office_id);
    if($request_data['search']['so_id'])
    {
      $conditions['sp.id']=$request_data['search']['so_id'];
    }
    $Store = $this->Store->find('all', array(
      'fields'=>array('Store.id','Store.name','sp.name','sp.id', 'Territory.name'),
      'conditions' => $conditions,
      'joins'=>array(
        array(
          'table'=>'sales_people',
          'alias'=>'sp',
          'type' => 'INNER',
          'conditions'=>array(
            'sp.office_id=Store.office_id AND sp.territory_id=Store.territory_id'
            )
          ),
        array(
          'table'=>'territories',
          'alias'=>'Territory',
          'type' => 'INNER',
          'conditions'=>array(
            'Territory.id = Store.territory_id'
            )
          )
        ),

      'order' => array('Store.name' => 'asc'),
      'recursive'=>-1
      ));
    $this->Session->write('products', $products);

    $soStores=array();
    foreach($Store as $data){
      $soStores[$data['Store']['id']]=$data ['sp']['id'];
    }

    $product=$this->CurrentInventory->Product->find('list', array(
      'conditions'=> $p_condition
      ));
    $unit_type = $this->request->data['search']['unit_type'];

    $unit_type_text = $this->request->data['search']['unit_type']==1?'Base Unit':'Sale Unit';

    $this->set(compact('unit_type_text'));

    $this->LoadModel('CurrentInventoryHistory');

    $sql="
      SELECT be.*,sp.name FROM balance_error be 
              inner join stores st on st.id=be.store_id
              inner join sales_people sp on sp.territory_id=st.territory_id
              where 
                be.store_id in (".implode(',', array_keys($soStores)).") 
                and be.product_id in (".implode(',', array_keys($product)).") 
                and be.tran_date_period='".$inv_report_start_date."'
    ";

    $month_data=$this->CurrentInventory->query($sql);
    /*echo $this->CurrentInventory->getLastQuery();
    pr($month_data);
    exit;*/
    foreach($month_data as $data_history)
    {
      $so_info[$data_history[0]['store_id']]['OB'][$data_history[0]['product_id']]=$data_history[0]['opening_balance'];

      $so_info[$data_history[0]['store_id']]['CB'][$data_history[0]['product_id']]=$data_history[0]['tran_closing_balance'];

      $so_info[$data_history[0]['store_id']]['RQ'][$data_history[0]['product_id']]=$data_history[0]['tran_return_qty'];

      $so_info[$data_history[0]['store_id']]['RCV'] [$data_history[0]['product_id']]=$data_history[0]['tran_received_qty'];

      $so_info[$data_history[0]['store_id']]['SQ'][$data_history[0]['product_id']]=$data_history[0]['tran_sales_qty'];

      $so_info[$data_history[0]['store_id']]['BQ'][$data_history[0]['product_id']]=$data_history[0]['tran_bonus_qty'];
    }

    // pr($data_history_all);die;

    /* }
    }*/
    //  echo '<pre>';print_r($so_info);echo '</pre>';die();
    $this->set(compact('Store','so_info'));
  }
}

public function get_sales_unit_by_product_id($id)
{
  $this->loadModel('Product');
  $product=$this->Product->find('first',array(
    'conditions'=>array('Product.id'=>$id),
    'recursive'=>-1
    ));
  return $product['Product']['sales_measurement_unit_id'];
}


//xls download
public function admin_dwonload_xls() 
{
  $request_data = $this->Session->read('request_data');
  $products = $this->Session->read('products');

  $product_quantity = $this->Session->read('product_quantity');
  $office_id = $request_data['Memo']['office_id'];


  $header="";
  $data1="";



  foreach($this->data['e_orders']  as $e_orders){

//echo $key;

    foreach($e_orders as $key => $e_order){
      $data1 .= ucfirst($key."\t");
    }

    break;

  }

//exit;

/*$data1 .= ucfirst("Order Date,"); //for Tab Delimitated use \t
$data1 .= ucfirst("Order ID,");
$data1 .= ucfirst("Before Discount,");
$data1 .= ucfirst("Discount,");
$data1 .= ucfirst("Net Product Price,");
$data1 .= ucfirst("Shipping Cost,");
$data1 .= ucfirst("Sub Total,");
$data1 .= ucfirst("7% Tax Collected,");
$data1 .= ucfirst("3.5% Tax Collected,");
$data1 .= ucfirst("Total,");

$data1 .= ucfirst("7% Taxable Total,");
$data1 .= ucfirst("3.5% Taxable Total,");
$data1 .= ucfirst("Tax Exempt Total,");*/

$data1 .= "\n";

foreach($this->data['e_orders'] as $row1){
  $line = '';
  foreach($row1 as $value){
    if ((!isset($value)) OR ($value == "")){
$value = "\t"; //for Tab Delimitated use \t
}else{
  $value = str_replace('"', '""', $value);
$value = '"' . $value . '"' . "\t"; //for Tab Delimitated use \t
}
$line .= $value;
}
$data1 .= trim($line)."\n";
}


$data1 = str_replace("\r", "", $data1);
if ($data1 == ""){
  $data1 = "\n(0) Records Found!\n";
}

header("Content-type: application/vnd.ms-excel; name='excel'");
header("Content-Disposition: attachment; filename=\"Current-Inventory-Reports-".date("jS-F-Y-H:i:s").".xls\"");
header("Pragma: no-cache");
header("Expires: 0");

echo $data1; 
exit;

$this->autoRender = false;
}
public function get_territory_so_list()
{
  $this->loadModel('SalesPerson');
  $view = new View($this);

  $form = $view->loadHelper('Form');  

  $office_id = $this->request->data['office_id'];

//get SO list
  $so_list_r = $this->SalesPerson->find('all', array(
    'fields' => array('SalesPerson.id','SalesPerson.name', 'Territory.name'),
    'conditions' => array(
      'SalesPerson.office_id' => $office_id,
      'SalesPerson.territory_id >' => 0,
      'User.user_group_id' => array(4,1008),
      ),
    'recursive'=> 0
    )); 


  foreach($so_list_r as $key => $value)
  {
    $so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'].' ('.$value['Territory']['name'].')';
  }


  if($so_list)
  { 
    $form->create('search', array('role' => 'form', 'action'=>'index'))  ;

    echo $form->input('so_id', array('label'=>false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $so_list));
    $form->end();

  }
  else
  {
    echo '';  
  }


  $this->autoRender = false;
}

}


