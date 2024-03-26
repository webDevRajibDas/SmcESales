<?php

App::uses('AppController', 'Controller');

/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */

class CurrentInventoryReportsController extends AppController
{

  /**
   * Components
   *
   * @var array
   */
  public $components = array('Paginator', 'Session', 'Filter.Filter');
  public $uses = array('CurrentInventory', 'Store', 'ProductType', 'Office', 'RptDailyTranBalance', 'Challan', 'ReturnChallan', 'Memo');

  public function admin_index()
  {

    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', 300); //300 seconds = 5 minutes

    $this->set('page_title', 'Current Inventories');

    $this->CurrentInventory->recursive = 1;
    $this->loadModel('Store');
    $this->loadModel('InventoryStatuses');
    $this->loadModel('SalesPerson');
    $this->loadModel('Product');



    $unit_type = array(
      '1' => 'Base Unit',
      '2' => 'Sale Unit'
    );
    $this->set(compact('unit_type'));

    $product_type_list = $this->ProductType->find('list');
		$this->set(compact('product_type_list'));

    $product_list = $this->Product->find('list', array(
      'conditions' => array(/* 'NOT' => array('Product.product_category_id' => 32), */'is_active' => 1, 'is_virtual' => 0),
      'order' =>  array('order' => 'asc')
    ));

    $this->set(compact('product_list'));
    $this->Session->write('product_list', $product_list);

    $office_parent_id = $this->UserAuth->getOfficeParentId();

    if ($office_parent_id == 0) {
      $conditions = array('CurrentInventory.inventory_status_id' => 1);
      $storeCondition = array('Office.office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
    } else {
      $conditions = array('CurrentInventory.store_id' => $this->UserAuth->getStoreId(), 'CurrentInventory.inventory_status_id' => 1);
      $storeCondition = array('Office.id' => $this->UserAuth->getOfficeId());
    }
    $offices = $this->Office->find('list', array('conditions' => $storeCondition));

    $this->set(compact('offices', 'inventoryStatuses', 'productCategories'));

    if ($this->request->is('post')) {
      // pr($this->request->data());exit;
      $office_id = $this->request->data['search']['office_id'];
      $so_list_r = $this->SalesPerson->find('all', array(
        'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
        'conditions' => array(
          'SalesPerson.office_id' => $office_id,
          'SalesPerson.territory_id >' => 0,
          'User.user_group_id' => array(4, 1008),
        ),
        'recursive' => 0
      ));


      foreach ($so_list_r as $key => $value) {
        $so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
      }
      $this->set(compact('so_list'));

      $request_data = $this->request->data;
      $this->set(compact('request_data'));
      $this->Session->write('request_data', $request_data);

      if (!empty($this->request->data['search']['product_id'])) {
        $p_condition = array(/* 'NOT' => array('Product.product_category_id' => 32), */'Product.id' => $this->request->data['search']['product_id']);
      } else {
        $p_condition = array(/* 'NOT' => array('Product.product_category_id' => 32) */'is_virtual' => 0);
      }

      if (!empty($this->request->data['search']['product_type'])) {
        $p_condition['Product.product_type_id'] = $this->request->data['search']['product_type'];
      } 


      $products = $this->CurrentInventory->Product->find('all', array(
        'fields' => array('Product.name', 'Product.id', 'MU.name as mes_name', 'Product.product_category_id'),
        'joins' => array(
          array(
            'table' => 'measurement_units',
            'alias' => 'MU',
            'type' => 'LEFT',
            'conditions' => array('MU.id= Product.sales_measurement_unit_id')
          )
        ),
        'conditions' => $p_condition,
        'order' => 'Product.order',
        'recursive' => -1
      ));



      $this->set(compact('products'));
      $this->Session->write('products', $products);


      $inv_report_start_date = date('Y-m-d', strtotime($this->request->data['search']['date_from']));
      $inv_report_end_date =  date('Y-m-d', strtotime($this->request->data['search']['date_to']));

      $conditions = array('Store.store_type_id' => 3, 'Store.office_id' => $office_id);
      if ($request_data['search']['so_id']) {
        $conditions['sp.id'] = $request_data['search']['so_id'];
      }
      $Store = $this->Store->find('all', array(
        'fields' => array('Store.id', 'Store.name', 'sp.name', 'sp.id', 'Territory.name'),
        'conditions' => $conditions,
        'joins' => array(
          array(
            'table' => 'sales_people',
            'alias' => 'sp',
            'type' => 'INNER',
            'conditions' => array(
              'sp.office_id=Store.office_id AND sp.territory_id=Store.territory_id'
            )
          ),
          array(
            'table' => 'territories',
            'alias' => 'Territory',
            'type' => 'INNER',
            'conditions' => array(
              'Territory.id = Store.territory_id'
            )
          )
        ),

        'order' => array('Store.name' => 'asc'),
        'recursive' => -1
      ));
      $this->Session->write('products', $products);

      $soStores = array();
      foreach ($Store as $data) {
        $soStores[$data['Store']['id']] = $data['sp']['id'];
      }

      $product = $this->CurrentInventory->Product->find('list', array(
        'conditions' => $p_condition
      ));
      $unit_type = $this->request->data['search']['unit_type'];

      $unit_type_text = $this->request->data['search']['unit_type'] == 1 ? 'Base Unit' : 'Sale Unit';

      $this->set(compact('unit_type_text'));

      $this->LoadModel('CurrentInventoryHistory');


      /*$data_history_all= $this->RptDailyTranBalance->find('all',array(
    'fields'=>array('SUM(received_qty) as rcv_qty','SUM(sales_qty) as sales_qty','SUM(bonus_qty) as bonus_qty','SUM(return_qty) as return_qty','product_id as product','store_id as store','p.sales_measurement_unit_id as mes_id'),
    'conditions'=>array(
    'tran_date BETWEEN ? AND ?'=>array($inv_report_start_date,$inv_report_end_date),
    'product_id'=>array_keys($product),
    'store_id'=>array_keys($soStores)
    ),
    'joins'=>array(
    array(
    'table' => 'products',
    'alias' => 'p',
    'type' => 'INNER',
    'conditions' => array(
    'p.id = RptDailyTranBalance.product_id',
    )
    ),
    ),
    'group'=>array('store_id','product_id','p.sales_measurement_unit_id'),
    'recursive'=>-1
    ));
    pr($data_history_all);exit;*/

      /*Get All Product Received Qty by Store wise:Start*/
      $received_qty_data = $this->Challan->find('all', array(
        'fields' => array(
          'Challan.receiver_store_id',
          'ChallanDetail.product_id',
          'SUM(ChallanDetail.received_qty) AS [rcv_qty]'
        ),
        'conditions' => array(
          'Challan.received_date BETWEEN ? AND ?' => array($inv_report_start_date, $inv_report_end_date),
          'Challan.receiver_store_id' => array_keys($soStores),
          'ChallanDetail.product_id' => array_keys($product),
        ),
        'joins' => array(
          array(
            'table' => 'challan_details',
            'alias' => 'ChallanDetail',
            'type' => 'INNER',
            'conditions' => array(
              'ChallanDetail.challan_id = Challan.id',
            )
          ),
        ),
        'group' => array('Challan.receiver_store_id', 'ChallanDetail.product_id'),
        'recursive' => -1
      ));
      // pr($received_qty_data);exit;
      $received_qty = array();
      foreach ($received_qty_data as $data) {
        $received_qty[$data['Challan']['receiver_store_id']][$data['ChallanDetail']['product_id']] = $data[0]['rcv_qty'];
      }

      /*Get All Product Received Qty by Store wise:END*/

      /*Get All Product Returned Qty by Store wise:Start*/
      $received_qty_data = $this->ReturnChallan->find('all', array(
        'fields' => array(
          'ReturnChallan.sender_store_id',
          'ChallanDetail.product_id',
          'SUM(ChallanDetail.received_qty) AS [rcv_qty]'
        ),
        'conditions' => array(
          'ReturnChallan.received_date BETWEEN ? AND ?' => array($inv_report_start_date, $inv_report_end_date),
          'ReturnChallan.sender_store_id' => array_keys($soStores),
          'ChallanDetail.product_id' => array_keys($product),
        ),
        'joins' => array(
          array(
            'table' => 'return_challan_details',
            'alias' => 'ChallanDetail',
            'type' => 'INNER',
            'conditions' => array(
              'ChallanDetail.challan_id = ReturnChallan.id',
            )
          ),
        ),
        'group' => array('ReturnChallan.sender_store_id', 'ChallanDetail.product_id'),
        'recursive' => -1
      ));
      // pr($received_qty_data);exit;
      $returned_qty = array();
      foreach ($received_qty_data as $data) {
        $returned_qty[$data['ReturnChallan']['sender_store_id']][$data['ChallanDetail']['product_id']] = $data[0]['rcv_qty'];
      }
      // pr($returned_qty);exit;
      /*Get All Product Returned Qty by Store wise:END*/

      /*Get Store Wise Sales Data:Start*/
      $memo_data = $this->Memo->find('all', array(
        'fields' => array('Store.id', 'MemoDetail.product_id', 'SUM(ROUND((MemoDetail.sales_qty * CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END ),0)) as sales_qty'),
        'conditions' => array(
          'Store.id' => array_keys($soStores),
          'Memo.memo_date BETWEEN ? AND ?' => array($inv_report_start_date, $inv_report_end_date),
          'MemoDetail.product_id' => array_keys($product),
          'MemoDetail.price !=' => '0.0'
        ),
        'joins' => array(
          array(
            'table' => 'memo_details',
            'alias' => 'MemoDetail',
            'type' => 'INNER',
            'conditions' => array(
              'MemoDetail.memo_id = Memo.id',
            ),
          ),
          array(
            'table' => 'stores',
            'alias' => 'Store',
            'type' => 'INNER',
            'conditions' => array(
              'CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end = Store.territory_id',
        
            ),
          ),

          array(
            'alias' => 'Product',
            'table' => 'products',
            'type' => 'INNER',
            'conditions' => 'Product.id = MemoDetail.product_id'
          ),
          array(
            'alias' => 'ProductMeasurement',
            'table' => 'product_measurements',
            'type' => 'LEFT',
            'conditions' => 'Product.id = ProductMeasurement.product_id AND 
                CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
                ELSE 
                  MemoDetail.measurement_unit_id
                END =ProductMeasurement.measurement_unit_id'
          ),
        ),
        'group' => array('Store.id', 'MemoDetail.product_id'),
        'recursive' => -1
      ));
      $sales_qty = array();
      foreach ($memo_data as $data) {
        $sales_qty[$data['Store']['id']][$data['MemoDetail']['product_id']] = $data[0]['sales_qty'];
      }
      /*Get Store Wise Sale Data:END*/
      /*Get Store Wise Sales Data:Start*/
      $memo_data = $this->Memo->find('all', array(
        'fields' => array('Store.id', 'MemoDetail.product_id', 'SUM(ROUND((MemoDetail.sales_qty * CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END ),0)) as sales_qty'),
        'conditions' => array(
          'Store.id' => array_keys($soStores),
          'Memo.memo_date BETWEEN ? AND ?' => array($inv_report_start_date, $inv_report_end_date),
          'MemoDetail.product_id' => array_keys($product),
          'MemoDetail.price' => '0.0'
        ),
        'joins' => array(
          array(
            'table' => 'memo_details',
            'alias' => 'MemoDetail',
            'type' => 'INNER',
            'conditions' => array(
              'MemoDetail.memo_id = Memo.id',
            )
          ),
          array(
            'table' => 'stores',
            'alias' => 'Store',
            'type' => 'INNER',
            'conditions' => array(
              'CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end = Store.territory_id',
            ),
          ),
          array(
            'alias' => 'Product',
            'table' => 'products',
            'type' => 'INNER',
            'conditions' => 'Product.id = MemoDetail.product_id'
          ),
          array(
            'alias' => 'ProductMeasurement',
            'table' => 'product_measurements',
            'type' => 'LEFT',
            'conditions' => 'Product.id = ProductMeasurement.product_id AND 
                CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
                ELSE 
                  MemoDetail.measurement_unit_id
                END =ProductMeasurement.measurement_unit_id'
          ),
        ),
        'group' => array('Store.id', 'MemoDetail.product_id'),
        'recursive' => -1
      ));
      $bonus_qty = array();
      foreach ($memo_data as $data) {
        $bonus_qty[$data['Store']['id']][$data['MemoDetail']['product_id']] = $data[0]['sales_qty'];
      }
      /*Get Store Wise Sale Data:END*/

      /*$data_history_opening_all= $this->RptDailyTranBalance->find('all',array(
      'fields'=>array('opening_balance','product_id as product','store_id as store','tran_date as tran_date'),
      'conditions'=>array(
        'tran_date'=>array($inv_report_start_date),
        'product_id'=>array_keys($product),
        'store_id'=>array_keys($soStores)
        ),
        // 'group'=>array('store_id','product_id'),
      'recursive'=>-1
    ));*/


      $data_history_opening_all = $this->RptDailyTranBalance->find('all', array(
        // 'fields' => array('opening_balance', 'product_id as product', 'store_id as store', 'tran_date as tran_date'),
        'joins' => array(
          array(
            'table' => 'products',
            'alias' => 'Product',
            'conditions' => 'Product.id=RptDailyTranBalance.product_id'
          )
        ),
        'conditions' => array(
          'tran_date' => $inv_report_start_date,
          'OR' => array('Product.id' => array_keys($product), 'Product.parent_id' => array_keys($product)),
          'store_id' => array_keys($soStores)
        ),
        'fields' => array('sum(RptDailyTranBalance.opening_balance) AS opening_balance,CASE WHEN Product.parent_id is null OR Product.parent_id=0 THEN Product.id ELSE Product.parent_id END as product', 'RptDailyTranBalance.store_id as store'),
        'group' => array('CASE WHEN Product.parent_id is null OR Product.parent_id=0 THEN Product.id ELSE Product.parent_id END', 'RptDailyTranBalance.store_id'),
        'recursive' => -1
      ));
      /* pr($data_history_opening);
      echo $this->RptDailyTranBalance->getLastQuery(); */
      $data_history_closing_all = $this->RptDailyTranBalance->find('all', array(
        // 'fields' => array('opening_balance', 'product_id as product', 'store_id as store', 'tran_date as tran_date'),
        'joins' => array(
          array(
            'table' => 'products',
            'alias' => 'Product',
            'conditions' => 'Product.id=RptDailyTranBalance.product_id'
          )
        ),
        'conditions' => array(
          'tran_date' => $inv_report_end_date,
          'OR' => array('Product.id' => array_keys($product), 'Product.parent_id' => array_keys($product)),
          'store_id' => array_keys($soStores)
        ),
        'fields' => array('sum(RptDailyTranBalance.closing_balance) AS closing_balance,CASE WHEN Product.parent_id is null OR Product.parent_id=0 THEN Product.id ELSE Product.parent_id END as product', 'RptDailyTranBalance.store_id as store'),
        'group' => array('CASE WHEN Product.parent_id is null OR Product.parent_id=0 THEN Product.id ELSE Product.parent_id END', 'RptDailyTranBalance.store_id'),
        'recursive' => -1
      ));

      // echo $this->RptDailyTranBalance->getLastQuery();exit;

      /*$opening_and_closing_all = array();
    foreach($data_history_opening_all as $key => $data_history_opening)
    {
    $opening_and_closing_all[$data_history_opening[0]['store']][$data_history_opening[0]['product']]['opening'] = $data_history_opening['RptDailyTranBalance']['opening_balance'];
    // $opening_and_closing_all[$data_history_opening[0]['store']][$data_history_opening[0]['product']]['closing'] = $data_history_closing_all[$key]['RptDailyTranBalance']['closing_balance'];
    }
    foreach($data_history_closing_all as $key => $data_history_closing)
    {
    // $opening_and_closing_all[$data_history_opening[0]['store']][$data_history_opening[0]['product']]['opening'] = $data_history_opening['RptDailyTranBalance']['opening_balance'];
    $opening_and_closing_all[$data_history_closing[0]['store']][$data_history_closing[0]['product']]['closing'] = $data_history_closing['RptDailyTranBalance']['closing_balance'];
    }*/

      $opening_all = array();
      foreach ($data_history_opening_all as $key => $data_history_opening) {
        $opening_all[] = array(
          'store' => $data_history_opening[0]['store'],
          'product' => $data_history_opening[0]['product'],
          /*'opn'=>$data_history_opening['RptDailyTranBalance']['opening_balance']*/
          'opn' => ($data_history_opening['0']['opening_balance'] ? $data_history_opening['0']['opening_balance'] : 0)
        );
        // $opening_and_closing_all[$data_history_opening[0]['store']][$data_history_opening[0]['product']]['closing'] = $data_history_closing_all[$key]['RptDailyTranBalance']['closing_balance'];
      }
      // pr($opening_all);exit;
      $closing_all = array();
      foreach ($data_history_closing_all as $key => $data_history_closing) {
        $closing_all[$data_history_closing[0]['store']][$data_history_closing[0]['product']] = $data_history_closing['0']['closing_balance'];
      }
      // pr($closing_all);exit;
      /*foreach($data_history_all as $data_history)
    {
    $sales_unit=$data_history[0]['mes_id'];
    $data_history[0]['opn_blnc']= isset($opening_and_closing_all[$data_history[0]['store']][$data_history[0]['product']]['opening'])?$opening_and_closing_all[$data_history[0]['store']][$data_history[0]['product']]['opening']:0;
    $data_history[0]['closing_blnc'] = isset($opening_and_closing_all[$data_history[0]['store']][$data_history[0]['product']]['closing'])?$opening_and_closing_all[$data_history[0]['store']][$data_history[0]['product']]['closing']:0;
    //$sales_unit=$this->get_sales_unit_by_product_id($data_history[0]['product']);
    $so_info[$data_history[0]['store']]['OB'][$data_history[0]['product']]=($unit_type==1)?$data_history[0]['opn_blnc']:$this->unit_convertfrombase($data_history[0]['product'], $sales_unit, $data_history[0]['opn_blnc']);

    $so_info[$data_history[0]['store']]['CB'][$data_history[0]['product']]=($unit_type==1)?$data_history[0]['closing_blnc']:$this->unit_convertfrombase($data_history[0]['product'], $sales_unit, $data_history[0]['closing_blnc']);

    $so_info[$data_history[0]['store']]['RQ'][$data_history[0]['product']]=($unit_type==1)?$data_history[0]['return_qty']:$this->unit_convertfrombase($data_history[0]['product'], $sales_unit, $data_history[0]['return_qty']);

    $so_info[$data_history[0]['store']]['RCV'] [$data_history[0]['product']]=($unit_type==1)?$data_history[0]['rcv_qty']:$this->unit_convertfrombase($data_history[0]['product'], $sales_unit, $data_history[0]['rcv_qty']);

    $so_info[$data_history[0]['store']]['SQ'][$data_history[0]['product']]=($unit_type==1)?$data_history[0]['sales_qty']:$this->unit_convertfrombase($data_history[0]['product'], $sales_unit, $data_history[0]['sales_qty']);

    $so_info[$data_history[0]['store']]['BQ'][$data_history[0]['product']]=($unit_type==1)?$data_history[0]['bonus_qty']:$this->unit_convertfrombase($data_history[0]['product'], $sales_unit, $data_history[0]['bonus_qty']);
    }*/
      foreach ($opening_all as $data_history) {
        $sales_unit = $this->get_sales_unit_by_product_id($data_history['product']);

        $rcv_qty = 0;
        $ret_qty = 0;
        $sales_qty_set = 0;
        $bonus_qty_set = 0;
        $rcv_qty = isset($received_qty[$data_history['store']][$data_history['product']]) ? $received_qty[$data_history['store']][$data_history['product']] : 0;
        $ret_qty = isset($returned_qty[$data_history['store']][$data_history['product']]) ? $returned_qty[$data_history['store']][$data_history['product']] : 0;
        $sales_qty_set = isset($sales_qty[$data_history['store']][$data_history['product']]) ? $sales_qty[$data_history['store']][$data_history['product']] : 0;
        $bonus_qty_set = isset($bonus_qty[$data_history['store']][$data_history['product']]) ? $bonus_qty[$data_history['store']][$data_history['product']] : 0;

        $so_info[$data_history['store']]['OB'][$data_history['product']] = ($unit_type == 1) ? $data_history['opn'] : $this->unit_convertfrombase($data_history['product'], $sales_unit, $data_history['opn']);

        $so_info[$data_history['store']]['CB'][$data_history['product']] = ($unit_type == 1) ? $closing_all[$data_history['store']][$data_history['product']] : $this->unit_convertfrombase($data_history['product'], $sales_unit, $closing_all[$data_history['store']][$data_history['product']]);

        $so_info[$data_history['store']]['RQ'][$data_history['product']] = ($unit_type == 1 && $ret_qty > 0) ? $this->unit_convert($data_history['product'], $sales_unit, $ret_qty) : $ret_qty;

        $so_info[$data_history['store']]['RCV'][$data_history['product']] = ($unit_type == 1 && $rcv_qty > 0) ? $this->unit_convert($data_history['product'], $sales_unit, $rcv_qty) : $rcv_qty;

        $so_info[$data_history['store']]['SQ'][$data_history['product']] = ($unit_type == 2 && $sales_qty_set > 0) ? $this->unit_convertfrombase($data_history['product'], $sales_unit, $sales_qty_set) : $sales_qty_set;

        $so_info[$data_history['store']]['BQ'][$data_history['product']] = ($unit_type == 2 && $bonus_qty_set > 0) ? $this->unit_convertfrombase($data_history['product'], $sales_unit, $bonus_qty_set) : $bonus_qty_set;
      }

      // pr($data_history_all);die;

      /* }
    }*/
      //  echo '<pre>';print_r($so_info);echo '</pre>';die();
      $this->set(compact('Store', 'so_info'));
    }
  }

  public function get_sales_unit_by_product_id($id)
  {
    $this->loadModel('Product');
    $product = $this->Product->find('first', array(
      'conditions' => array('Product.id' => $id),
      'recursive' => -1
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


    $header = "";
    $data1 = "";



    foreach ($this->data['e_orders']  as $e_orders) {

      //echo $key;

      foreach ($e_orders as $key => $e_order) {
        $data1 .= ucfirst($key . "\t");
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

    foreach ($this->data['e_orders'] as $row1) {
      $line = '';
      foreach ($row1 as $value) {
        if ((!isset($value)) or ($value == "")) {
          $value = "\t"; //for Tab Delimitated use \t
        } else {
          $value = str_replace('"', '""', $value);
          $value = '"' . $value . '"' . "\t"; //for Tab Delimitated use \t
        }
        $line .= $value;
      }
      $data1 .= trim($line) . "\n";
    }


    $data1 = str_replace("\r", "", $data1);
    if ($data1 == "") {
      $data1 = "\n(0) Records Found!\n";
    }

    header("Content-type: application/vnd.ms-excel; name='excel'");
    header("Content-Disposition: attachment; filename=\"Current-Inventory-Reports-" . date("jS-F-Y-H:i:s") . ".xls\"");
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
      'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
      'conditions' => array(
        'SalesPerson.office_id' => $office_id,
        'SalesPerson.territory_id >' => 0,
        'User.user_group_id' => array(4, 1008),
      ),
      'recursive' => 0
    ));


    foreach ($so_list_r as $key => $value) {
      $so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
    }


    if ($so_list) {
      $form->create('search', array('role' => 'form', 'action' => 'index'));

      echo $form->input('so_id', array('label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $so_list));
      $form->end();
    } else {
      echo '';
    }


    $this->autoRender = false;
  }

  function get_product_list()
    {
      
      $view = new View($this);
      
        $form = $view->loadHelper('Form');	
      // $product_types=@array_values($this->request->data['Memo']['product_type']);
      $product_types=@$this->request->data['search']['product_type'];
      // pr($this->request->data['Memo']['product_type']);exit;

      $this->loadModel('Product');

      $conditions=array();
      if($product_types)
      {
        $conditions['product_type_id']=$product_types;
      }
      $conditions['is_virtual']=0;
      $product_list = $this->Product->find('list', array(
            'conditions'=> $conditions,
            'order'=>  array('order'=>'asc')
      ));
      if($product_list)
      {	
        $form->create('Memo', array('role' => 'form', 'action'=>'index'))	;
        
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
