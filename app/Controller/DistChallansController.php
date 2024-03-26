<?php

App::uses('AppController', 'Controller');

/**
 * Challans Controller
 *
 * @property DistChallan $DistChallan
 * @property PaginatorComponent $Paginator
 */
class DistChallansController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Filter.Filter');
    public $uses = array('DistChallan', 'DistChallanDetail', 'DistStore', 'ProductType', 'DistCurrentInventory');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index()
    {

        $this->loadModel('Office');
        $this->loadModel('DistDistributor');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        $this->set('page_title', 'Distributor Challan List');
        $dist_conditions = array();
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_conditions = array('Office.office_type_id' => 2);
            $dist_challan_conditions = array();
        } else {
            if ($user_group_id == 1029 || $user_group_id == 1028) {
                if ($user_group_id == 1028) {
                    $dist_ae_info = $this->DistAreaExecutive->find('first', array(
                        'conditions' => array('DistAreaExecutive.user_id' => $user_id, 'DistAreaExecutive.is_active' => 1),
                        'recursive' => -1,
                    ));
                    $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
                    $dist_tso_info = $this->DistTso->find('list', array(
                        'conditions' => array('dist_area_executive_id' => $dist_ae_id),
                        'fields' => array('DistTso.id', 'DistTso.dist_area_executive_id'),
                    ));

                    $dist_tso_id = array_keys($dist_tso_info);
                } else {
                    $dist_tso_info = $this->DistTso->find('first', array(
                        'conditions' => array('DistTso.user_id' => $user_id, 'DistTso.is_active' => 1),
                        'recursive' => -1,
                    ));
                    $dist_tso_id = $dist_tso_info['DistTso']['id'];
                }

                $tso_dist_list = $this->DistTsoMapping->find('list', array(
                    'conditions' => array(
                        'dist_tso_id' => $dist_tso_id,
                    ),
                    'fields' => array('DistTsoMapping.dist_distributor_id', 'DistTsoMapping.dist_tso_id'),
                ));
                $dist_conditions = array('DistDistributor.id' => array_keys($tso_dist_list));
                $dist_challan_conditions = array('DistChallan.dist_distributor_id' => array_keys($tso_dist_list));
            } elseif ($user_group_id == 1034) {
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first', array(
                    'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
                $dist_conditions = array('DistDistributor.id' => $distributor_id);
                $dist_challan_conditions = array('DistChallan.dist_distributor_id' => $distributor_id);
            } else {
                $dist_challan_conditions = array('DistChallan.office_id' => $this->UserAuth->getOfficeId());
            }

            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }

        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));


        $office_id = isset($this->request->data['DistChallan']['office_id']) != '' ? $this->request->data['DistChallan']['office_id'] : 0;
        if ($user_group_id == 1029 || $user_group_id == 1028) {
            $distributors = $this->DistDistributor->find('list', array('conditions' => $dist_conditions, 'order' => array('DistDistributor.name' => 'asc')));
        } elseif ($user_group_id == 1034) {
            $distributors = $this->DistDistributor->find('list', array('conditions' => $dist_conditions, 'order' => array('DistDistributor.name' => 'asc')));
        } else {
            $distributors = $this->DistDistributor->find('list', array('conditions' => array('DistDistributor.office_id' => $office_id), 'order' => array('DistDistributor.name' => 'asc')));
        }

        $distributors_all = $this->DistDistributor->find('list', array('conditions' => $dist_conditions, 'order' => array('DistDistributor.name' => 'asc')));

        $dist_distributor_id = isset($this->request->data['DistChallan']['dist_distributor_id']) != '' ? $this->request->data['DistChallan']['dist_distributor_id'] : 0;

        $this->loadModel('Territory');
        $territories = $this->Territory->find('list');


        $this->DistChallan->recursive = 0;
        $this->paginate = array(
            'conditions' => $dist_challan_conditions,
            'joins' => array(

                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'INNER',
                    'conditions' => array('Office.id=DistChallan.office_id')
                ),

                array(
                    'table' => 'dist_tsos',
                    'alias' => 'DistTso',
                    'type' => 'left',
                    'conditions' => array('DistTso.id=(
                                                        SELECT 
                                                            TOP 1 dsmh.dist_tso_id
                                                        FROM [dist_tso_mapping_histories] AS dsmh
                                                        WHERE 
                                                        (
                                                            [DistChallan].[dist_distributor_id] = dsmh.[dist_distributor_id]
                                                            AND is_change = 1
                                                            AND [DistChallan].[challan_date] BETWEEN dsmh.[effective_date] 
                                                            AND (
                                                                CASE
                                                                    WHEN dsmh.[end_date] IS NULL THEN GETDATE()
                                                                    ELSE dsmh.[end_date]
                                                                    END
                                                                )
                                                            )
                                                            order by dsmh.id asc
                                                        )
                                                ')
                ),

                array(
                    'table' => 'dist_area_executives',
                    'alias' => 'DistAE',
                    'type' => 'left',
                    'conditions' => array('DistAE.id=DistTso.dist_area_executive_id')
                ),


            ),
            'recursive' => 0,
            'fields' => array('DistChallan.*', 'DistTransactionType.id', 'DistTransactionType.name', 'SenderStore.id', 'SenderStore.name', 'SenderStore.territory_id', 'ReceiverStore.id', 'ReceiverStore.name', 'ReceiverStore.dist_distributor_id', 'SalesPerson.id', 'SalesPerson.name', 'SalesPerson.territory_id', 'Office.office_name', 'Office.order', 'DistTso.name', 'DistAE.name'),
            'order' => array('DistChallan.id' => 'desc')
            // 'group' => array('DistChallan.id','Office.order'),
            // 'order' => array('DistChallan.id desc','Office.order asc')
        );

        // echo $this->DistChallan->getLastQuery();exit;

        // $hello=$this->paginate();
        // echo '<pre>';
        // print_r($hello);

        // echo '</pre>';exit;
        $this->set('challans', $this->paginate());

        $this->set(compact('offices', 'office_id', 'distributors', 'dist_distributor_id', 'territories', 'distributors_all'));
    }
    public function admin_edit($id = null)
    {
        ini_set('max_execution_time', 1000);
        ini_set('memory_limit', '-1');
        $this->loadmodel('InstrumentType');

        if (!$this->DistChallan->exists($id)) {
            throw new NotFoundException(__('Invalid challan'));
        }
        $options = array(
            'conditions' => array(
                'DistChallan.' . $this->DistChallan->primaryKey => $id
            ),
            'recursive' => 0
        );
        $challan_info = $this->DistChallan->find('first', $options);
        $this->set(compact('challan_info'));
        $office_id = $challan_info['DistChallan']['office_id'];
        $memo_no = $challan_info['DistChallan']['memo_no'];
        $sender_store_id = $challan_info['DistChallan']['sender_store_id'];
        $dist_distributor_id = $challan_info['DistChallan']['dist_distributor_id'];
        $so_id = $challan_info['DistChallan']['so_id'];

        $challan_date_for_three_days_validation = date('Y-m-d', strtotime($challan_info['DistChallan']['challan_date'] . " +3 days"));
        if (date("Y-m-d") > $challan_date_for_three_days_validation) {
            $this->Session->setFlash(__('DB challan can be edited within 3 days!!!'), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }

        $this->loadModel('ProductCombination');
        $this->loadModel('Combination');
        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');

        $this->loadModel('Product');
        $this->loadModel('Memo');
        $this->loadModel('Order');
        $this->loadModel('OrderDetail');
        $this->loadModel('DistOutletMap');
        $this->loadModel('CurrentInventory');
        $this->loadModel('MeasurementUnit');
        $this->LoadModel('Product');
        $this->LoadModel('Store');
        $this->loadModel('ProductPrice');
        $this->loadModel('DistProductPrice');
        $this->loadModel('DistProductCombination');
        $this->loadModel('CombinationDetailsV2');
        $this->loadModel('ProductCombination');
        $this->loadModel('Outlet');
        $this->loadModel('DistDistributorBalance');
        $this->loadModel('DistDistributorBalanceHistory');
        $this->loadModel('DistDistributorLimit');
        $this->loadModel('DistDistributorLimitHistory');
        $this->loadModel('SalesPerson');
        $this->loadModel('Market');
        $this->loadModel('Office');
        $this->loadModel('Territory');
        $this->loadModel('ProductMeasurement');
        $this->loadModel('ProductBatchInfo');


        if ($this->request->is('post')) {

            $existing_detail_record = $this->OrderDetail->find('all', array(
                'fields' => array(
                    'OrderDetail.product_id',
                    'OrderDetail.virtual_product_id',
                    'OrderDetail.measurement_unit_id',
                    'ROUND((OrderDetail.sales_qty * CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END ),0) as qty',
                ),
                'joins' => array(
                    array(
                        'alias' => 'Product',
                        'table' => 'products',
                        'type' => 'INNER',
                        'conditions' => 'Product.id = OrderDetail.product_id'
                    ),
                    array(
                        'alias' => 'ProductMeasurement',
                        'table' => 'product_measurements',
                        'type' => 'LEFT',
                        'conditions' => 'Product.id = ProductMeasurement.product_id AND 
                            CASE WHEN (OrderDetail.measurement_unit_id is null or OrderDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
                            ELSE 
                            OrderDetail.measurement_unit_id
                            END =ProductMeasurement.measurement_unit_id'
                    )
                ),
                'conditions' => array('OrderDetail.order_id =(SELECT id from orders where order_no=\'' . $memo_no . '\')'),
                'recursive' => -1
            ));
            $prev_challan_product_qty = array();
            foreach ($existing_detail_record as $data) {

                if ($data['OrderDetail']['virtual_product_id']) {
                    $data['OrderDetail']['product_id'] = $data['OrderDetail']['virtual_product_id'];
                }

                $prev_challan_product_qty[$data['OrderDetail']['product_id']] = array(
                    'm_unit' => $data['OrderDetail']['measurement_unit_id'],
                    'sales_qty' => @$prev_challan_product_qty[$data['OrderDetail']['product_id']]['sales_qty'] + $data['0']['qty']
                );
            }

            // pr($this->request->data);die();
            $outlet_is_within_group = $this->outletGroupCheck($this->request->data['OrderProces']['distribut_outlet_id']);
            $product_is_injectable = $this->productInjectableCheck($this->request->data['OrderDetail']['product_id']);


            $this->loadModel('Store');
            $store_id_arr = $this->Store->find('first', array(
                'conditions' => array(
                    'Store.office_id' => $this->request->data['OrderProces']['office_id'],
                    'Store.store_type_id' => 2
                ),
                'recursive' => -1
            ));
            $store_id = $store_id_arr['Store']['id'];
            $stock_check = 0;
            $stock_available = 1;
            $m = "";
            $gross_amount = 0;

            $order_product_array_for_stock_check = array();
            $products = $this->Product->find('all', array('fields' => array('id', 'name', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
            $product_list = Set::extract($products, '{n}.Product');


            //-------------------array create for serial ------------\\
           
            $product_key_value_null = array();
            foreach ($this->request->data['OrderDetail']['product_id'] as $key => $val) {
                if(empty($val)){
                    $product_key_value_null[$key]=1;
                }
            }   

            $n=0;

            foreach ($this->request->data['OrderDetail']['product_current_inventory_id'] as $key => $val) {
                $pvalue = $product_key_value_null[$n];
                if( !empty($pvalue)){
                    $this->request->data['OrderDetail']['product_batch_current_inventory_id'][$n+1]= $val;
                    $this->request->data['OrderDetail']['product_batch_given_stock'][$n+1]= $this->request->data['OrderDetail']['product_given_stock'][$key];
                    $n = $n+1;
                }else{
                    $this->request->data['OrderDetail']['product_batch_current_inventory_id'][$n]= $val;
                    $this->request->data['OrderDetail']['product_batch_given_stock'][$n]= $this->request->data['OrderDetail']['product_given_stock'][$key];
                }

                $n++;

            }
          
            unset($this->request->data['OrderDetail']['product_current_inventory_id']);
            unset($this->request->data['OrderDetail']['product_given_stock']);  

            

            //-------------------end------------\\


            foreach ($this->request->data['OrderDetail']['product_id'] as $key => $val) {
                if ($val == NULL) {
                    continue;
                }

                /*------ Stock Checking array preparation :start  --------------*/
                if (!isset($order_product_array_for_stock_check[$val])) {
                    $order_product_array_for_stock_check[$val] = 0;
                }
                $price = $this->request->data['OrderDetail']['Price'][$key];
                $punits_pre = $this->search_array($val, 'id', $product_list);
                if ($price == 0.0) {
                    $qty = $this->request->data['OrderDetail']['sales_qty'][$key];
                } else {
                    $qty = $this->request->data['OrderDetail']['deliverd_qty'][$key];
                }
                $measurement_unit_id = isset($this->request->data['OrderDetail']['measurement_unit_id'][$key]) ? $this->request->data['OrderDetail']['measurement_unit_id'][$key] : $punits_pre['sales_measurement_unit_id'];
                $base_qty = 0;
                if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
                    $base_qty = round($qty);
                } else {
                    $base_qty = $this->unit_convert($val, $measurement_unit_id, $qty);
                }
                $bonus_base_qty = 0;
                if (isset($this->request->data['OrderDetail']['bonus_product_qty'][$key])) {
                    $bonus_qty = $this->request->data['OrderDetail']['bonus_product_qty'][$key];
                    $measurement_unit_id = isset($this->request->data['OrderDetail']['bonus_measurement_unit_id'][$key]) ? $this->request->data['OrderDetail']['measurement_unit_id'][$key] : $punits_pre['sales_measurement_unit_id'];
                    if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
                        $bonus_base_qty = round($bonus_qty);
                    } else {
                        $bonus_base_qty = $this->unit_convert($val, $measurement_unit_id, $bonus_qty);
                    }
                }
                $order_product_array_for_stock_check[$val] += ($base_qty + $bonus_base_qty);
                /*------ Stock Checking array preparation :end  --------------*/

                $total_product_price = 0;
                $total_product_price = $qty * $price;
                $gross_amount = $gross_amount + $total_product_price;
            }
            $this->request->data['Order']['gross_value'] = $gross_amount;
            $msg_for_stock_unavailable = '';
            if (array_key_exists('save', $this->request->data)) {
                /*-------------------- Stock Checking : Start -------------------*/
                foreach ($order_product_array_for_stock_check as $product_id => $qty) {
                    $qty = $qty - @$prev_challan_product_qty[$product_id]['sales_qty'];
                    if ($qty <= 0)
                        continue;
                    $punits_pre = $this->search_array($product_id, 'id', $product_list);
                    $qty = $this->unit_convertfrombase($product_id, $punits_pre['sales_measurement_unit_id'], $qty);
                    $stock_check = $this->stock_check($store_id, $product_id, $qty);
                    if ($stock_check != 1) {
                        $stock_available = 0;
                        $msg_for_stock_unavailable = "Stock Not Available For <b>" . $punits_pre['name'] . '</b>';
                        break;
                    }
                }
                /*-------------------- Stock Checking : END --------------------*/
            }
            if ($stock_available == 0) {
                $this->Session->setFlash(__($msg_for_stock_unavailable), 'flash/error');
                $this->redirect(array('action' => 'edit', $id));
            }
            /*------- 
            if($stock_available == 0){
                $this->Session->setFlash(__('Stock Is not Available'), 'flash/error');
                $this->redirect(array('action' => 'edit',$id));
            }
            -----------*/
            //$this->admin_delete($order_id, 0);
            $office_id = $this->request->data['OrderProces']['office_id'];
            $outlet_id = $this->request->data['OrderProces']['distribut_outlet_id'];

            $getOutlets = $this->Outlet->find('all', array(
                'conditions' => array('Outlet.id' => $outlet_id),
            ));
            $outlet_info = $this->DistOutletMap->find('first', array(
                'conditions' => array('DistOutletMap.outlet_id' => $this->request->data['OrderProces']['distribut_outlet_id']),

            ));

            $market_id = $getOutlets[0]['Outlet']['market_id'];
            $this->loadModel('Market');
            $market_info = $this->Market->find('first', array(
                'conditions' => array('Market.id' => $market_id),
                'fields' => 'Market.thana_id',
                'order' => array('Market.id' => 'asc'),
                'recursive' => -1,
            ));

            $thana_id = $market_info['Market']['thana_id'];
            $distributor_id = $outlet_info['DistDistributor']['id'];

            $sales_person = $this->SalesPerson->find('list', array(
                'conditions' => array('territory_id' => $this->request->data['OrderProces']['territory_id']),
                'order' => array('name' => 'asc')
            ));

            $this->request->data['OrderProces']['sales_person_id'] = key($sales_person);

            $this->request->data['OrderProces']['order_date'] = date('Y-m-d', strtotime($this->request->data['OrderProces']['order_date']));

            $order_id = $this->request->data['OrderProces']['order_id'];
            $order_no = $this->request->data['OrderProces']['order_no'];
            $order_date = $this->request->data['OrderProces']['order_date'];
            $order_time = $this->request->data['OrderProces']['order_time'];

            $prev_memo_info_for_date = $this->Memo->find('first', array(
                'conditions' => array(
                    'Memo.memo_no' => $order_no
                )
            ));
            $prev_memo_date = $prev_memo_info_for_date['Memo']['memo_date'];
            $prev_memo_time = $prev_memo_info_for_date['Memo']['memo_time'];

            $orderData['id'] = $order_id;
            $orderData['office_id'] = $this->request->data['OrderProces']['office_id'];
            $orderData['territory_id'] = $this->request->data['OrderProces']['territory_id'];
            $orderData['market_id'] = $market_id;
            $orderData['outlet_id'] = $this->request->data['OrderProces']['distribut_outlet_id'];
            $orderData['entry_date'] = $this->current_datetime();
            $orderData['order_date'] = $this->request->data['OrderProces']['order_date'];
            $order_no = $orderData['order_no'] = $order_no;
            $orderData['gross_value'] = $this->request->data['Order']['gross_value'] - $this->request->data['Order']['total_discount'];
            $orderData['w_store_id'] = $this->request->data['OrderProces']['w_store_id'];
            $orderData['is_active'] = 1;

            $orderData['order_time'] = $this->request->data['OrderProces']['order_time'];
            $orderData['order_reference_no'] = $this->request->data['OrderProces']['order_reference_no'];
            $orderData['sales_person_id'] = $this->request->data['OrderProces']['sales_person_id'];
            $orderData['from_app'] = 0;
            $orderData['action'] = 1;
            $orderData['confirmed'] = 1;
            $orderData['order_reference_no'] = $this->request->data['OrderProces']['order_reference_no'];


            $orderData['created_at'] = $this->current_datetime();
            $orderData['created_by'] = $this->UserAuth->getUserId();
            $orderData['updated_at'] = $this->current_datetime();
            $orderData['updated_by'] = $this->UserAuth->getUserId();


            $orderData['office_id'] = $office_id ? $office_id : 0;
            $orderData['thana_id'] = $thana_id ? $thana_id : 0;
            $orderData['total_discount'] = $this->request->data['Order']['total_discount'];

            $balance = 0;
            $balance = 0;
            $limit = 0;
            $dist_balance_info = array();
            $dealer_balance_info = array();
            $dist_limit_info = array();
            /*************************** Balance Check ***********************************/
            $dist_balance_info = array();
            $dist_balance_info = $this->DistDistributorBalance->find('first', array(
                'conditions' => array(
                    'DistDistributorBalance.dist_distributor_id' => $distributor_id
                ),
                'limit' => 1,
                'recursive' => -1
            ));


            $prev_gross_value = $this->request->data['OrderProces']['prev_gross_value'];
            $db_balance = array();
            $db_balance['id'] = $dist_balance_info['DistDistributorBalance']['id'];
            $db_balance['office_id'] = $dist_balance_info['DistDistributorBalance']['office_id'];
            $db_balance['dist_distributor_id'] = $dist_balance_info['DistDistributorBalance']['dist_distributor_id'];
            $db_balance['balance'] = $dist_balance_info['DistDistributorBalance']['balance'] + $prev_gross_value;
            $db_balance['created_at'] = $dist_balance_info['DistDistributorBalance']['created_at'];
            $db_balance['created_by'] = $dist_balance_info['DistDistributorBalance']['created_by'];
            $db_balance['updated_at'] = $this->current_datetime();
            $db_balance['updated_by'] = $this->UserAuth->getUserId();
            $this->DistDistributorBalance->save($db_balance);

            $dist_balance_info = array();
            $dist_balance_info = $this->DistDistributorBalance->find('first', array(
                'conditions' => array(
                    'DistDistributorBalance.dist_distributor_id' => $distributor_id
                ),
                'limit' => 1,
                'recursive' => -1
            ));
            if (empty($dist_balance_info)) {
                $this->Session->setFlash(__('Please check Balance of This Distributor!!!'), 'flash/error');
                $this->redirect(array('action' => 'index'));
            }
            $credit_amount = $this->request->data['Order']['gross_value'] - $this->request->data['Order']['total_discount'];
            $dist_balance = $dist_balance_info['DistDistributorBalance']['balance'];

            if ($dist_balance < $credit_amount) {
                $db_balance = array();
                $db_balance['id'] = $dist_balance_info['DistDistributorBalance']['id'];
                $db_balance['office_id'] = $dist_balance_info['DistDistributorBalance']['office_id'];
                $db_balance['dist_distributor_id'] = $dist_balance_info['DistDistributorBalance']['dist_distributor_id'];
                $db_balance['balance'] = $dist_balance_info['DistDistributorBalance']['balance'] - $prev_gross_value;
                $db_balance['created_at'] = $dist_balance_info['DistDistributorBalance']['created_at'];
                $db_balance['created_by'] = $dist_balance_info['DistDistributorBalance']['created_by'];
                $db_balance['updated_at'] = $this->current_datetime();
                $db_balance['updated_by'] = $this->UserAuth->getUserId();
                $this->DistDistributorBalance->save($db_balance);

                $this->Session->setFlash(__('Insufficient Balance of This Distributor!!!'), 'flash/error');
                $this->redirect(array('action' => 'index'));
            } else {
                $db_balance_history = array();
                $db_balance_history['dist_distributor_balance_id'] = $db_balance['id'];
                $db_balance_history['office_id'] = $db_balance['office_id'];
                $db_balance_history['dist_distributor_id'] = $db_balance['dist_distributor_id'];
                $db_balance_history['balance'] = $db_balance['balance'];
                $db_balance_history['transaction_amount'] = $prev_gross_value;
                $db_balance_history['balance_type'] = 1;
                $db_balance_history['balance_transaction_type_id'] = 9;
                $db_balance_history['transaction_date'] = $this->current_datetime();
                $db_balance_history['created_at'] = $this->current_datetime();
                $db_balance_history['created_by'] = $this->UserAuth->getUserId();
                $db_balance_history['updated_at'] = $this->current_datetime();
                $db_balance_history['updated_by'] = $this->UserAuth->getUserId();
                $this->DistDistributorBalanceHistory->create();
                $this->DistDistributorBalanceHistory->save($db_balance_history);
            }
            /*************************** end Balance Check ***********************************/
            $this->request->data['OrderProces']['is_active'] = 1;
            $datasource = $this->Order->getDataSource();
            try {
                $datasource->begin();
                try {
                    $this->admin_delete($order_id, 0);
                } catch (Exception $e) {
                    $datasource->rollback();
                    $this->Session->setFlash(__('SomeThing Went Wrong! Please Try Again.'), 'flash/error');
                    $this->redirect(array('action' => 'index'));
                }

                if (array_key_exists('draft', $this->request->data)) {
                    $orderData['status'] =  $this->request->data['OrderProces']['status'] = 2;
                    $orderData['confirm_status'] =  $this->request->data['OrderProces']['confirm_status'] = 1;
                    $message = "Order Has Been Saved as Draft";

                    $is_execute = 0;
                } else {
                    $message = "Order Has Been Saved";
                    $orderData['status'] =  $this->request->data['OrderProces']['status'] = 2;
                    $orderData['confirm_status'] = $this->request->data['OrderProces']['confirm_status'] = 2;
                    $is_execute = 1;

                    /*************************** Balance Check ********************************/
                    $dist_limit_info = $this->DistDistributorLimit->find('first', array(
                        'conditions' => array(
                            'DistDistributorLimit.office_id' => $office_id,
                            'DistDistributorLimit.dist_distributor_id' => $distributor_id,
                        ),
                        'limit' => 1,
                        'recursive' => -1
                    ));
                    /*if($dist_limit_info){
                    $dist_balance =$dist_balance + $dist_limit_info['DistDistributorLimit']['max_amount'];
                }*/

                    $dealer_balance_info = $this->DistDistributorBalanceHistory->find('first', array(
                        'conditions' => array(
                            'DistDistributorBalanceHistory.dist_distributor_id' => $distributor_id,

                        ),
                        'order' => 'DistDistributorBalanceHistory.id DESC',
                        'recursive' => -1
                    ));
                    /*************************** Balance Check End *********************************/
                }
                //$orderData['status'] = $this->request->data['OrderProces']['status'];
                //$orderData['confirm_status'] = $this->request->data['OrderProces']['confirm_status'];
                if (!$this->Order->save($orderData)) {
                    throw new Exception();
                } else {
                    $order_info_arr = $this->Order->find('first', array(
                        'conditions' => array(
                            'Order.id' => $order_id
                        )
                    ));

                    if ($order_id) {
                        $all_product_id = $this->request->data['OrderDetail']['product_id'];
                        if (!empty($this->request->data['OrderDetail'])) {
                            $total_product_data = array();
                            $order_details = array();

                            foreach ($this->request->data['OrderDetail']['product_id'] as $key => $val) {
                                if ($val == NULL) {
                                    continue;
                                }
                                $product_details = $this->Product->find('first', array(
                                    'fields' => array('id', 'is_virtual', 'parent_id'),
                                    'conditions' => array('Product.id' => $val),
                                    'recursive' => -1
                                ));
                                //$measurement_unit_id = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
                                $sales_price = $this->request->data['OrderDetail']['Price'][$key];
                                if ($sales_price != 0 && !empty($sales_price)) {
                                    if ($product_details['Product']['is_virtual'] == 1) {
                                        $product_id = $order_details['OrderDetail']['virtual_product_id'] = $val;
                                        $order_details['OrderDetail']['product_id'] = $product_details['Product']['parent_id'];
                                    } else {
                                        $order_details['OrderDetail']['virtual_product_id'] = 0;
                                        $product_id = $order_details['OrderDetail']['product_id'] = $product_details['Product']['id'];
                                    }
                                    $order_details['OrderDetail']['order_id'] = $order_id;
                                    $order_details['OrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
                                    $sales_price = $order_details['OrderDetail']['price'] = $this->request->data['OrderDetail']['Price'][$key];
                                    $order_qty = $order_details['OrderDetail']['sales_qty'] = $this->request->data['OrderDetail']['sales_qty'][$key];
                                    $sales_qty = $order_details['OrderDetail']['deliverd_qty'] = $this->request->data['OrderDetail']['deliverd_qty'][$key];
                                    $order_details['OrderDetail']['product_price_id'] = $this->request->data['OrderDetail']['product_price_id'][$key];
                                    $order_details['OrderDetail']['remaining_qty'] = $order_qty - $order_details['OrderDetail']['deliverd_qty'];
                                    $order_details['OrderDetail']['challan_remarks'] = $this->request->data['OrderDetail']['remarks'][$key];
                                    $product_price_slab_id = 0;
                                    $order_details['OrderDetail']['product_price_id'] = $this->request->data['OrderDetail']['product_price_id'][$key];
                                    $order_details['OrderDetail']['product_combination_id'] = $this->request->data['OrderDetail']['combination_id'][$key];
                                    $order_details['OrderDetail']['bonus_qty'] = $this->request->data['OrderDetail']['bonus_product_qty'][$key];

                                    if ($this->request->data['OrderDetail']['bonus_product_id'][$key] != 0) {
                                        $b_p_id = $order_details['OrderDetail']['bonus_product_id'] = $this->request->data['OrderDetail']['bonus_product_id'][$key];
                                        $bonus_sales_qty = $this->request->data['OrderDetail']['bonus_product_qty'][$key];
                                        if (array_key_exists('save', $this->request->data)) {
                                            $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
                                            $product_list = Set::extract($products, '{n}.Product');
                                            $punits_pre = $this->search_array($b_p_id, 'id', $product_list);
                                            if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
                                                $bonus_base_quantity = $bonus_sales_qty;
                                            } else {
                                                $bonus_base_quantity = $this->unit_convert($b_p_id, $punits_pre['sales_measurement_unit_id'], $bonus_sales_qty);
                                            }
                                            $update_type = 'deduct';
                                            try {
                                                $this->update_current_inventory($bonus_base_quantity, $b_p_id, $store_id, $update_type, 11, $prev_memo_date);
                                            } catch (Exception $e) {
                                                $datasource->rollback();
                                                $this->Session->setFlash(__('SomeThing Went Wrong! Please Try Again.'), 'flash/error');
                                                $this->redirect(array('action' => 'index'));
                                            }
                                        }
                                    } else {
                                        $order_details['OrderDetail']['bonus_product_id'] = NULL;
                                    }
                                    //Start for bonus
                                    $order_date = date('Y-m-d', strtotime($this->request->data['OrderProces']['order_date']));
                                    $bonus_product_id = $this->request->data['OrderDetail']['bonus_product_id'];
                                    $bonus_product_qty = $this->request->data['OrderDetail']['bonus_product_qty'];
                                    $order_details['OrderDetail']['bonus_id'] = 0;
                                    $order_details['OrderDetail']['bonus_scheme_id'] = 0;
                                    if ($bonus_product_qty[$key] > 0) {
                                        $b_product_id = $bonus_product_id[$key];
                                        $bonus_result = $this->bouns_and_scheme_id_set($b_product_id, $order_date);
                                        $order_details['OrderDetail']['bonus_id'] = $bonus_result['bonus_id'];
                                    }
                                    //End for bouns
                                    // Temp order Details
                                    /*$new_order_details['OrderDetail']=$OrderDetail_record[$product_id];
                                if($order_details['OrderDetail']['deliverd_qty'] == 0){
                                    $new_order_details['OrderDetail']['remaining_qty']= $sales_qty;
                                }
                                else{
                                    $new_order_details['OrderDetail']['remaining_qty']= $order_details['OrderDetail']['remaining_qty'];
                                }
                                $newtempOrderDetails['TempOrderDetail']=$new_order_details['OrderDetail'];
                                $temp_new_total_product_data[] = $newtempOrderDetails;

                                $deliverd_qty=$OrderDetail_record[$product_id]['deliverd_qty'];
                                if (array_key_exists('save', $this->request->data))
                                {
                                    $new_order_details['OrderDetail']['deliverd_qty']=$order_details['OrderDetail']['deliverd_qty'];
                                }else{
                                    $new_order_details['OrderDetail']['deliverd_qty']=$order_details['OrderDetail']['deliverd_qty'] + $deliverd_qty;
                                }
                                $new_order_details['OrderDetail']['order_id'] = $order_id;
                                $new_total_product_data[] = $new_order_details;*/
                                    $order_details['OrderDetail']['discount_type'] = $this->request->data['OrderDetail']['disccount_type'][$key];
                                    $order_details['OrderDetail']['discount_amount'] = $this->request->data['OrderDetail']['discount_amount'][$key];
                                    $order_details['OrderDetail']['policy_type'] = $this->request->data['OrderDetail']['policy_type'][$key];
                                    $order_details['OrderDetail']['policy_id'] = $this->request->data['OrderDetail']['policy_id'][$key];
                                    //$total_product_data[] = $order_details;
                                    $total_product_data = $order_details;
                                } else {
                                    $sales_qty = $this->request->data['OrderDetail']['sales_qty'][$key];
                                    if (!empty($sales_qty)) {
                                        if ($product_details['Product']['is_virtual'] == 1) {
                                            $product_id = $bouns_order_details['OrderDetail']['virtual_product_id'] = $val;
                                            $bouns_order_details['OrderDetail']['product_id'] = $product_details['Product']['parent_id'];
                                        } else {
                                            $bouns_order_details['OrderDetail']['virtual_product_id'] = 0;
                                            $product_id = $bouns_order_details['OrderDetail']['product_id'] = $product_details['Product']['id'];
                                        }
                                        $bouns_order_details['OrderDetail']['order_id'] = $order_id;
                                        $bouns_order_details['OrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
                                        $sales_price = $bouns_order_details['OrderDetail']['price'] = 0;
                                        $bouns_order_details['OrderDetail']['sales_qty'] = $sales_qty;
                                        $bouns_order_details['OrderDetail']['bonus_qty'] = $this->request->data['OrderDetail']['sales_qty'][$key];
                                        $bouns_order_details['OrderDetail']['bonus_product_id'] = $product_id;
                                        $bouns_order_details['OrderDetail']['is_bonus'] = 1;
                                        $bouns_order_details['OrderDetail']['deliverd_qty'] = $sales_qty;
                                        $bouns_order_details['OrderDetail']['discount_type'] = $this->request->data['OrderDetail']['disccount_type'][$key];
                                        $bouns_order_details['OrderDetail']['discount_amount'] = $this->request->data['OrderDetail']['discount_amount'][$key];
                                        $bouns_order_details['OrderDetail']['policy_type'] = $this->request->data['OrderDetail']['policy_type'][$key];
                                        $bouns_order_details['OrderDetail']['policy_id'] = $this->request->data['OrderDetail']['policy_id'][$key];
                                        $bouns_order_details['OrderDetail']['is_bonus'] = 1;
                                        $order_date = date('Y-m-d', strtotime($this->request->data['OrderProces']['order_date']));
                                        if ($this->request->data['OrderDetail']['is_bonus'][$key] == 3)
                                            $bouns_order_details['OrderDetail']['is_bonus'] = 3;
                                        $selected_set = '';
                                        if (isset($this->request->data['OrderDetail']['selected_set'][$bouns_order_details['OrderDetail']['policy_id']])) {
                                            $selected_set = $this->request->data['OrderDetail']['selected_set'][$bouns_order_details['OrderDetail']['policy_id']];
                                        }
                                        if ($selected_set) {
                                            $other_info = array(
                                                'selected_set' => $selected_set
                                            );
                                        }
                                        if ($other_info)
                                            $bouns_order_details['OrderDetail']['other_info'] = json_encode($other_info);
                                        // $total_product_data[] = $bouns_order_details;
                                        $total_product_data = $bouns_order_details;
                                    }
                                }
                                if (array_key_exists('save', $this->request->data)) {

                                    $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
                                    $product_list = Set::extract($products, '{n}.Product');

                                    $punits_pre = $this->search_array($product_id, 'id', $product_list);
                                    $measurement_unit_id = $this->request->data['OrderDetail']['measurement_unit_id'][$key] ? $this->request->data['OrderDetail']['measurement_unit_id'][$key] : $punits_pre['sales_measurement_unit_id'];
                                    if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
                                        $base_quantity = $sales_qty;
                                    } else {
                                        $base_quantity = $this->unit_convert($product_id, $measurement_unit_id, $sales_qty);
                                    }

                                    $update_type = 'deduct';
                                    try {
                                        $this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 11, $prev_memo_date);
                                    } catch (Exception $e) {
                                        $datasource->rollback();
                                        $this->Session->setFlash(__('SomeThing Went Wrong! Please Try Again.'), 'flash/error');
                                        $this->redirect(array('action' => 'index'));
                                    }
                                }

                                if(empty($total_product_data))
                                    continue;

                                //--------one by one insert order details--------\\
                                $this->OrderDetail->create();
                                if (!$this->OrderDetail->save($total_product_data)) {
                                    throw new Exception();
                                } else {

                                    $order_details_id = $this->OrderDetail->getLastInsertId();

                                    $given_stock = $this->request->data['OrderDetail']['product_batch_given_stock'][$key];
                                    $insertProductBatch = array();
                                    foreach ($this->request->data['OrderDetail']['product_batch_current_inventory_id'][$key] as $pbkey => $current_invenoty) {
                                        
                                        $batch_info_data['ProductBatchInfo']['current_inventory_id'] = $current_invenoty;
                                        $batch_info_data['ProductBatchInfo']['order_details_id'] = $order_details_id;
                                        $batch_info_data['ProductBatchInfo']['memo_details_id'] = 0;
                                        $batch_info_data['ProductBatchInfo']['product_id'] = $val;
                                        $batch_info_data['ProductBatchInfo']['given_stock'] = $given_stock[$pbkey];
                                        $batch_info_data['ProductBatchInfo']['created_at'] = $this->current_datetime();
                                        $batch_info_data['ProductBatchInfo']['created_by'] = $this->UserAuth->getUserId();
                                        $batch_info_data['ProductBatchInfo']['updated_at'] = $this->current_datetime();
                                        $batch_info_data['ProductBatchInfo']['updated_by'] = $this->UserAuth->getUserId();
                    
                                        $insertProductBatch[] = $batch_info_data;
                    
                                    }

                                    
                                    
                                    /* if(!empty($insertProductBatch)){
                                        $this->ProductBatchInfo->saveAll($insertProductBatch); 
                                    } */

                                    if (!$this->ProductBatchInfo->saveAll($insertProductBatch)) {
                                        throw new Exception();
                                    }
                                    
                                }




                                //--------end--------\\


                            }


                            /*  if (!$this->OrderDetail->saveAll($total_product_data)) {
                                throw new Exception();
                            } */
                        }
                    }
                    /************ Memo create date: 04-09-2019 *****************/
                    /***************Memo Create for no Confirmation Type **************/
                    $this->loadModel('Memo');
                    $this->loadModel('OrderDetail');
                    $this->loadModel('Order');
                    $this->loadModel('Collection');
                    $order_info = $this->Order->find('first', array(
                        'conditions' => array('Order.id' => $order_id),
                        'recursive' => -1
                    ));
                    $order_detail_info = $this->OrderDetail->find('all', array(
                        'conditions' => array('OrderDetail.order_id' => $order_id),
                        'recursive' => -1
                    ));


                    //pr($order_detail_info);//die();
                    $memo = array();
                    $memo['sales_person_id'] = $order_info['Order']['sales_person_id'];
                    $memo['entry_date'] = $this->current_datetime();
                    $memo['memo_date'] =  $prev_memo_date;

                    $memo['office_id'] = $order_info['Order']['office_id'];
                    $memo['sale_type_id'] = 1;
                    $memo['territory_id'] = $order_info['Order']['territory_id'];
                    $memo['thana_id'] = $order_info['Order']['thana_id'];
                    $memo['market_id'] = $order_info['Order']['market_id'];
                    $memo['outlet_id'] = $order_info['Order']['outlet_id'];

                    $memo['memo_no'] = $order_info['Order']['order_no'];
                    $memo['gross_value'] = $order_info['Order']['gross_value'];
                    $memo['cash_recieved'] = $order_info['Order']['gross_value'];
                    $memo['is_active'] = $order_info['Order']['is_active'];
                    $memo['w_store_id'] = $order_info['Order']['w_store_id'];
                    if (array_key_exists('save', $this->request->data)) {
                        $memo['status'] = 2;
                    } else {
                        $memo['status'] = 0;
                        $this->Memo->create();
                    }
                    $memo['memo_time'] = $prev_memo_time;
                    $memo['sales_person_id'] = $order_info['Order']['sales_person_id'];
                    $memo['from_app'] = 0;
                    $memo['action'] = 1;
                    $memo['is_distributor'] = 1;
                    $memo['is_program'] = 0;


                    $memo['memo_reference_no'] = $order_info['Order']['memo_reference_no'];

                    $memo['created_at'] = $this->current_datetime();
                    $memo['created_by'] = $this->UserAuth->getUserId();
                    $memo['updated_at'] = $this->current_datetime();
                    $memo['updated_by'] = $this->UserAuth->getUserId();
                    $memo['total_discount'] = $order_info['Order']['total_discount'];

                    if (array_key_exists('save', $this->request->data)) {
                        $this->loadModel('Memo');
                        //$memos=$this->Memo->find('first',array('conditions'=>array('Memo.memo_no like'=> "%".$order_no."%"),'order'=>'Memo.id DESC'));
                        //$memo_id= $memos['Memo']['id'];
                        //$this->admin_deletememo($memo_id,0);
                    }
                    //pr($memo);die();
                    if (!$this->Memo->save($memo)) {
                        throw new Exception();
                    } else {

                        $memo_id = $this->Memo->getLastInsertId();
                        $memo_info_arr = $this->Memo->find('first', array(
                            'conditions' => array(
                                'Memo.id' => $memo_id
                            )
                        ));

                        if ($memo_id) {
                            if (!empty($order_detail_info[0]['OrderDetail'])) {
                                $this->loadModel('MemoDetail');
                                $total_product_data = array();
                                $memo_details = array();
                                $bonus_memo_details = array();

                                foreach ($order_detail_info as $order_detail_result) {
                                    if ($order_detail_result['OrderDetail']['deliverd_qty'] > 0) {

                                        $orderdetailsid = $order_detail_result['OrderDetail']['id'];

                                        $product_id = $order_detail_result['OrderDetail']['product_id'];
                                        $virtual_product_id = $order_detail_result['OrderDetail']['virtual_product_id'];

                                        if ($virtual_product_id > 0) {
                                            $product_id = $virtual_product_id;
                                        }


                                        $product_details = $this->Product->find('first', array(
                                            'fields' => array('id', 'is_virtual', 'parent_id'),
                                            'conditions' => array('Product.id' => $product_id),
                                            'recursive' => -1
                                        ));
                                        if ($product_details['Product']['is_virtual'] == 1) {
                                            $memo_details['MemoDetail']['virtual_product_id'] = $product_id;
                                            $memo_details['MemoDetail']['product_id'] = $product_details['Product']['parent_id'];
                                        } else {
                                            $memo_details['MemoDetail']['virtual_product_id'] = 0;
                                            $memo_details['MemoDetail']['product_id'] = $product_details['Product']['id'];
                                        }
                                        //$memo_details['MemoDetail']['product_id'] = $product_id;

                                        $memo_details['MemoDetail']['memo_id'] = $memo_id;

                                        $memo_details['MemoDetail']['measurement_unit_id'] = $order_detail_result['OrderDetail']['measurement_unit_id'];
                                        $memo_details['MemoDetail']['actual_price'] = $order_detail_result['OrderDetail']['price'];
                                        $memo_details['MemoDetail']['price'] = $order_detail_result['OrderDetail']['price'] - $order_detail_result['OrderDetail']['discount_amount'];
                                        $memo_details['MemoDetail']['sales_qty'] = $order_detail_result['OrderDetail']['deliverd_qty'];

                                        $memo_details['MemoDetail']['product_price_id'] = $order_detail_result['OrderDetail']['product_price_id'];
                                        $memo_details['MemoDetail']['bonus_qty'] = NULL;
                                        $memo_details['MemoDetail']['offer_id'] = $order_detail_result['OrderDetail']['offer_id'];
                                        $memo_details['MemoDetail']['bonus_product_id'] = NULL;
                                        $memo_details['MemoDetail']['bonus_id'] = NULL;
                                        $memo_details['MemoDetail']['bonus_scheme_id'] = NULL;
                                        $memo_details['MemoDetail']['price_combination_id'] = $order_detail_result['OrderDetail']['price_combination_id'];
                                        $memo_details['MemoDetail']['product_combination_id'] = $order_detail_result['OrderDetail']['product_combination_id'];
                                        $memo_details['MemoDetail']['is_bonus'] = $order_detail_result['OrderDetail']['is_bonus'];
                                        $memo_details['MemoDetail']['deliverd_qty'] = $order_detail_result['OrderDetail']['deliverd_qty'];

                                        $memo_details['MemoDetail']['discount_type'] = $order_detail_result['OrderDetail']['discount_type'];
                                        $memo_details['MemoDetail']['discount_amount'] = $order_detail_result['OrderDetail']['discount_amount'];
                                        $memo_details['MemoDetail']['policy_type'] = $order_detail_result['OrderDetail']['policy_type'];
                                        $memo_details['MemoDetail']['policy_id'] = $order_detail_result['OrderDetail']['policy_id'];
                                        $memo_details['MemoDetail']['is_bonus'] = 0;
                                        if ($order_detail_result['OrderDetail']['is_bonus'] == 3)
                                            $memo_details['MemoDetail']['is_bonus'] = 3;

                                        //$total_product_data[] = $memo_details;
                                        $total_product_data = $memo_details;

                                        if ($order_detail_result['OrderDetail']['bonus_qty'] > 0 && $order_detail_result['OrderDetail']['is_bonus'] == 0) {

                                            $product_id = $order_detail_result['OrderDetail']['bonus_product_id'];
                                            $bproduct_details = $this->Product->find('first', array(
                                                'fields' => array('id', 'is_virtual', 'parent_id'),
                                                'conditions' => array('Product.id' => $product_id),
                                                'recursive' => -1
                                            ));

                                            if ($bproduct_details['Product']['is_virtual'] == 1) {
                                                $bonus_memo_details['MemoDetail']['virtual_product_id'] = $product_id;
                                                $bonus_memo_details['MemoDetail']['product_id'] = $bproduct_details['Product']['parent_id'];
                                            } else {
                                                $bonus_memo_details['MemoDetail']['virtual_product_id'] = 0;
                                                $bonus_memo_details['MemoDetail']['product_id'] = $bproduct_details['Product']['id'];
                                            }
                                            //$bonus_memo_details['MemoDetail']['product_id'] = $product_id;
                                            $bonus_memo_details['MemoDetail']['memo_id'] = $memo_id;

                                            $bonus_memo_details['MemoDetail']['measurement_unit_id'] = $order_detail_result['OrderDetail']['measurement_unit_id'];
                                            $bonus_memo_details['MemoDetail']['price'] = 0;
                                            $bonus_memo_details['MemoDetail']['sales_qty'] = $order_detail_result['OrderDetail']['bonus_qty'];

                                            $bonus_memo_details['MemoDetail']['product_price_id'] = $order_detail_result['OrderDetail']['product_price_id'];
                                            $bonus_memo_details['MemoDetail']['bonus_qty'] = $order_detail_result['OrderDetail']['bonus_qty'];
                                            $bonus_memo_details['MemoDetail']['offer_id'] = $order_detail_result['OrderDetail']['offer_id'];
                                            $bonus_memo_details['MemoDetail']['bonus_product_id'] = $order_detail_result['OrderDetail']['bonus_product_id'];
                                            $bonus_memo_details['MemoDetail']['bonus_id'] = $order_detail_result['OrderDetail']['bonus_id'];
                                            $bonus_memo_details['MemoDetail']['bonus_scheme_id'] = $order_detail_result['OrderDetail']['bonus_scheme_id'];
                                            $bonus_memo_details['MemoDetail']['price_combination_id'] = $order_detail_result['OrderDetail']['price_combination_id'];
                                            $bonus_memo_details['MemoDetail']['product_combination_id'] = $order_detail_result['OrderDetail']['product_combination_id'];
                                            $bonus_memo_details['MemoDetail']['is_bonus'] = 1;
                                            $bonus_memo_details['MemoDetail']['deliverd_qty'] = $order_detail_result['OrderDetail']['bonus_qty'];

                                            //$total_product_data[] = $bonus_memo_details;
                                            $total_product_data = $bonus_memo_details;
                                        }

                                        //--------------one by one memo details insert---------\\
                                        $this->MemoDetail->create();
                                        if (!$this->MemoDetail->save($total_product_data)) {
                                            throw new Exception();
                                        } else {
                                            //$orderdetailsid
                                            $memo_details_id = $this->MemoDetail->getLastInsertId();

                                            if (!$this->ProductBatchInfo->updateAll(
                                                array(
                                                    'ProductBatchInfo.memo_details_id' => $memo_details_id,
                                                    'ProductBatchInfo.updated_by' => "'" . $this->UserAuth->getUserId() . "'",
                                                    'ProductBatchInfo.updated_at' => "'" . $this->current_datetime() . "'"
                                                ),
                                                array('ProductBatchInfo.order_details_id' => $orderdetailsid)
                                            )) {
                                                throw new Exception();
                                            }
                                        }

                                        //-----------end----------------\\


                                    }
                                }

                                /*  if (!$this->MemoDetail->saveAll($total_product_data)) {
                                    throw new Exception();
                                } */
                                //pr($total_product_data);//die();
                            }
                            $order_outlet_id = $order_info['Order']['outlet_id'];
                            $this->loadmodel('DistOutletMap');
                            $outlet_info = $this->DistOutletMap->find('first', array(
                                'conditions' => array('DistOutletMap.outlet_id' => $order_outlet_id),
                                'fields' => array('DistOutletMap.dist_distributor_id'),
                            ));
                            $distibuter_id = $outlet_info['DistOutletMap']['dist_distributor_id'];
                            $this->loadmodel('DistStore');
                            $dist_store_id = $this->DistStore->find('first', array(
                                'conditions' => array('DistStore.dist_distributor_id' => $distibuter_id),
                                'fields' => array('DistStore.id'),
                            ));
                            /************* create Challan *************/

                            if (array_key_exists('save', $this->request->data)) {

                                /*****************Create Chalan and *****************/
                                $this->loadModel('DistChallan');
                                $this->loadModel('DistChallanDetail');
                                $this->loadModel('CurrentInventory');
                                //$company_id  =$this->Session->read('Office.company_id');
                                $office_id  = $this->request->data['OrderProces']['office_id'];
                                $store_id = $this->request->data['OrderProces']['w_store_id'];
                                //$challan['company_id']=$company_id;
                                $challan['office_id'] = $office_id;
                                $challan['memo_id'] = $memo_info_arr['Memo']['id'];
                                $challan['memo_no'] = $memo_info_arr['Memo']['memo_no'];
                                $challan['challan_no'] = $memo_info_arr['Memo']['memo_no'];
                                $challan['receiver_dist_store_id'] = $dist_store_id['DistStore']['id'];
                                $challan['receiving_transaction_type'] = 2;
                                $challan['received_date'] = '';
                                $challan['challan_date'] = $prev_memo_date;
                                $challan['dist_distributor_id'] = $distibuter_id;
                                $challan['challan_referance_no'] = '';
                                $challan['challan_type'] = "";
                                $challan['remarks'] = 0;
                                $challan['status'] = 1;
                                $challan['so_id'] = $order_info['Order']['sales_person_id'];
                                $challan['is_close'] = 0;
                                $challan['inventory_status_id'] = 2;
                                $challan['transaction_type_id'] = 2;
                                $challan['sender_store_id'] = $store_id;
                                $challan['created_at'] = $this->current_datetime();
                                $challan['created_by'] = $this->UserAuth->getUserId();
                                $challan['updated_at'] = $this->current_datetime();
                                $challan['updated_by'] = $this->UserAuth->getUserId();
                                //pr();die();
                                $this->DistChallan->create();
                                // pr($challan);
                                if (!$this->DistChallan->save($challan)) {
                                    throw new Exception();
                                } else {

                                    $challan_id = $this->DistChallan->getLastInsertId();
                                    if ($challan_id) {

                                        $challan_no = 'Ch-' . $distibuter_id . '-' . date('Y') . '-' . $challan_id;

                                        $challan_data['id'] = $challan_id;
                                        $challan_data['challan_no'] = $challan_no;

                                        $this->DistChallan->save($challan_data);
                                    }
                                    $product_list = $this->request->data['OrderDetail'];
                                    //pr($product_list);
                                    if (!empty($product_list['product_id'])) {
                                        $data_array = array();

                                        foreach ($product_list['product_id'] as $key => $val) {
                                            if ($product_list['product_id'][$key] != '') {
                                                if ($product_list['Price'][$key] != 0 && !empty($product_list['Price'][$key])) {

                                                    if ($product_list['deliverd_qty'][$key] > 0) {
                                                        if (!empty($val)) {
                                                            $inventories = $this->CurrentInventory->find('first', array(
                                                                'conditions' => array(
                                                                    'CurrentInventory.product_id' => $val,
                                                                    'CurrentInventory.store_id' => $store_id,
                                                                ),
                                                                'recursive' => -1,
                                                            ));
                                                            $batch_no = $inventories['CurrentInventory']['batch_number'];

                                                            $product_details = $this->Product->find('first', array(
                                                                'fields' => array('id', 'is_virtual', 'parent_id'),
                                                                'conditions' => array('Product.id' => $val),
                                                                'recursive' => -1
                                                            ));

                                                            if ($product_details['Product']['is_virtual'] == 1) {
                                                                $data['DistChallanDetail']['virtual_product_id'] = $val;
                                                                $data['DistChallanDetail']['product_id'] = $product_details['Product']['parent_id'];
                                                            } else {
                                                                $data['DistChallanDetail']['virtual_product_id'] = 0;
                                                                $data['DistChallanDetail']['product_id'] = $product_details['Product']['id'];
                                                            }
                                                            //$data['DistChallanDetail']['product_id'] = $val;

                                                            $data['DistChallanDetail']['challan_id'] = $this->DistChallan->id;

                                                            $data['DistChallanDetail']['measurement_unit_id'] = $product_list['measurement_unit_id'][$key];
                                                            $data['DistChallanDetail']['challan_qty'] = $product_list['deliverd_qty'][$key];
                                                            $data['DistChallanDetail']['received_qty'] = $product_list['deliverd_qty'][$key];
                                                            $data['DistChallanDetail']['batch_no'] = $batch_no;
                                                            //$data['DistChallanDetail']['remaining_qty'] =$product_list['remaining_qty'][$key];
                                                            $data['DistChallanDetail']['price'] = $product_list['Price'][$key];
                                                            /* if(!empty($product_list['bonus_product_id'][$key])){
													$data['DistChallanDetail']['is_bonus'] = 1;
													}else{*/
                                                            $data['DistChallanDetail']['is_bonus'] = 0;
                                                            //}

                                                            $data['DistChallanDetail']['source'] = "";
                                                            $data['DistChallanDetail']['remarks'] = $this->request->data['OrderDetail']['remarks'][$key];

                                                            //$data['ChallanDetail']['expire_date'] = (($this->request->data['expire_date'][$key] != '' ) ? Date('Y-m-d', strtotime($this->request->data['expire_date'][$key])) : Null);
                                                            $date = (($this->request->data['OrderProces']['order_date'][$key] != ' ' && $this->request->data['OrderProces']['order_date'][$key] != 'null' && $this->request->data['OrderProces']['order_date'][$key] != '') ? explode('-', $this->request->data['OrderProces']['order_date'][$key]) : '');
                                                            if (!empty($date[1])) {
                                                                $date[0] = date('m', strtotime($date[0]));
                                                                $a_date = date('y-m-d', mktime(0, 0, 0, $date[0], 1, $date[1]));
                                                                $data['DistChallanDetail']['expire_date'] = date("Y-m-t", strtotime($a_date));
                                                            } else {
                                                                $data['DistChallanDetail']['expire_date'] = '';
                                                            }
                                                            $data['DistChallanDetail']['inventory_status_id'] = 1;  // set 1 for Sound product
                                                            //$data['ChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
                                                        }
                                                        $data_array[] = $data;
                                                        if ($product_list['bonus_product_qty'][$key] != 0) {

                                                            $inventories = $this->CurrentInventory->find('first', array(
                                                                'conditions' => array(
                                                                    'CurrentInventory.product_id' => $product_list['bonus_product_id'][$key],
                                                                    'CurrentInventory.store_id' => $store_id,
                                                                ),
                                                                'recursive' => -1,
                                                            ));

                                                            $product_details = $this->Product->find('first', array(
                                                                'fields' => array('id', 'is_virtual', 'parent_id'),
                                                                'conditions' => array('Product.id' => $product_list['bonus_product_id'][$key]),
                                                                'recursive' => -1
                                                            ));

                                                            if ($product_details['Product']['is_virtual'] == 1) {
                                                                $bonus_data['DistChallanDetail']['virtual_product_id'] = $product_list['bonus_product_id'][$key];
                                                                $bonus_data['DistChallanDetail']['product_id'] = $product_details['Product']['parent_id'];
                                                            } else {
                                                                $bonus_data['DistChallanDetail']['virtual_product_id'] = 0;
                                                                $bonus_data['DistChallanDetail']['product_id'] = $product_details['Product']['id'];
                                                            }

                                                            //$bonus_data['DistChallanDetail']['product_id'] = $product_list['bonus_product_id'][$key];

                                                            $batch_no = $inventories['CurrentInventory']['batch_number'];
                                                            $bonus_data['DistChallanDetail']['batch_no'] = $batch_no;
                                                            $bonus_data['DistChallanDetail']['challan_id'] = $this->DistChallan->id;

                                                            $bonus_data['DistChallanDetail']['measurement_unit_id'] = $product_list['measurement_unit_id'][$key];
                                                            $bonus_data['DistChallanDetail']['challan_qty'] = $product_list['bonus_product_qty'][$key];
                                                            $bonus_data['DistChallanDetail']['received_qty'] = $product_list['bonus_product_qty'][$key];
                                                            $bonus_data['DistChallanDetail']['price'] = 0;
                                                            $bonus_data['DistChallanDetail']['is_bonus'] = 1;
                                                            //pr($bonus_data);
                                                            $data_array[] = $bonus_data;
                                                        }
                                                    }
                                                } else {
                                                    if (!empty($product_list['sales_qty'][$key])) {
                                                        if ($product_list['Price'][$key] == 0) {
                                                            $inventories = $this->CurrentInventory->find('first', array(
                                                                'conditions' => array(
                                                                    'CurrentInventory.product_id' => $val,
                                                                    'CurrentInventory.store_id' => $store_id,
                                                                ),
                                                                'recursive' => -1,
                                                            ));
                                                            $bonus_data = array();
                                                            $batch_no = $inventories['CurrentInventory']['batch_number'];
                                                            $bonus_data['DistChallanDetail']['challan_id'] = $this->DistChallan->id;

                                                            $product_details = $this->Product->find('first', array(
                                                                'fields' => array('id', 'is_virtual', 'parent_id'),
                                                                'conditions' => array('Product.id' => $val),
                                                                'recursive' => -1
                                                            ));

                                                            if ($product_details['Product']['is_virtual'] == 1) {
                                                                $bonus_data['DistChallanDetail']['virtual_product_id'] = $val;
                                                                $bonus_data['DistChallanDetail']['product_id'] = $product_details['Product']['parent_id'];
                                                            } else {
                                                                $bonus_data['DistChallanDetail']['virtual_product_id'] = 0;
                                                                $bonus_data['DistChallanDetail']['product_id'] = $product_details['Product']['id'];
                                                            }
                                                            //$bonus_data['DistChallanDetail']['product_id'] = $val;


                                                            $bonus_data['DistChallanDetail']['challan_qty'] = $product_list['sales_qty'][$key];
                                                            $bonus_data['DistChallanDetail']['received_qty'] = $product_list['sales_qty'][$key];
                                                            $bonus_data['DistChallanDetail']['batch_no'] = $batch_no;
                                                            $bonus_data['DistChallanDetail']['measurement_unit_id'] = $product_list['measurement_unit_id'][$key];
                                                            $bonus_data['DistChallanDetail']['price'] = $product_list['Price'][$key];
                                                            $bonus_data['DistChallanDetail']['is_bonus'] = 1;

                                                            $data_array[] = $bonus_data;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        if (!$this->DistChallanDetail->saveAll($data_array)) {
                                            throw new Exception();
                                        }
                                    }
                                }
                            }
                            /************* end Challan *************/

                            /***************** Balance Deduct********************/
                            if (array_key_exists('save', $this->request->data)) {
                                if ($dist_balance_info) {
                                    $balance = $dist_balance - $credit_amount;
                                } else {
                                    $balance = 0;
                                }

                                if ($balance < 1) {
                                    if ($dist_limit_info) {
                                        $dist_limit_data['DistDistributorLimit']['id'] =  $dist_limit_info['DistDistributorLimit']['id'];
                                        $dist_limit_data['DistDistributorLimit']['max_amount'] = $dist_limit_info['DistDistributorLimit']['max_amount'] - $balance;
                                        $limit_data_history = array();

                                        if ($this->DistDistributorLimit->save($dist_limit_data)) {

                                            $limit_data_history['dist_distributor_limit_id'] = $dist_limit_info['DistDistributorLimit']['id'];
                                            $limit_data_history['max_amount'] = $dist_limit_info['DistDistributorLimit']['max_amount'] - $balance;
                                            $limit_data_history['transaction_amount'] = $balance * (-1);
                                            $limit_data_history['transaction_type'] = 0;
                                            $limit_data_history['is_active'] = 1;

                                            $this->DistDistributorLimitHistory->create();
                                            $this->DistDistributorLimitHistory->save($limit_data_history);

                                            $balance = 0;
                                        }
                                    }
                                }

                                $dealer_balance_data = array();
                                $dealer_balance = array();
                                $dealer_balance['id'] = $dist_balance_info['DistDistributorBalance']['id'];
                                $dealer_balance['office_id'] = $dist_balance_info['DistDistributorBalance']['office_id'];
                                $dealer_balance['dist_distributor_id'] = $dist_balance_info['DistDistributorBalance']['dist_distributor_id'];
                                $dealer_balance['balance'] = $balance;

                                if (!$this->DistDistributorBalance->save($dealer_balance)) {
                                    throw new Exception();
                                } else {
                                    $dealer_balance_data['dist_distributor_id'] = $distributor_id;
                                    $dealer_balance_data['dist_distributor_balance_id'] = $dist_balance_info['DistDistributorBalance']['id'];
                                    $dealer_balance_data['office_id'] = $this->request->data['OrderProces']['office_id'];
                                    $dealer_balance_data['memo_value'] = $this->request->data['Order']['gross_value'];
                                    $dealer_balance_data['balance'] = $balance;
                                    $dealer_balance_data['balance_type'] = 2;
                                    $dealer_balance_data['balance_transaction_type_id'] = 2;
                                    $dealer_balance_data['transaction_amount'] = $this->request->data['Order']['gross_value'];
                                    $dealer_balance_data['transaction_date'] = date('Y-m-d');
                                    $dealer_balance_data['created_at'] = $this->current_datetime();
                                    $dealer_balance_data['created_by'] = $this->UserAuth->getUserId();
                                    $dealer_balance_data['updated_at'] = $this->current_datetime();
                                    $dealer_balance_data['updated_by'] = $this->UserAuth->getUserId();
                                    $this->DistDistributorBalanceHistory->create();
                                    if (!$this->DistDistributorBalanceHistory->save($dealer_balance_data)) {
                                        throw new Exception();
                                    }
                                }
                            }
                            /****************end  Balance Deduct*********************/
                        }
                        //start collection crate
                        $collection_data = array();
                        $collection_data['memo_id'] = $memo_info_arr['Memo']['id'];
                        $collection_data['memo_no'] = $memo_info_arr['Memo']['memo_no'];
                        $collection_data['so_id'] = $memo_info_arr['Memo']['sales_person_id'];
                        $collection_data['memo_value'] = $memo_info_arr['Memo']['gross_value'];
                        $collection_data['credit_or_due'] = $memo_info_arr['Memo']['credit_amount'];
                        $collection_data['memo_date'] = $memo_info_arr['Memo']['memo_date'];
                        $collection_data['type'] = 1;
                        $collection_data['instrument_type'] = $order_info['Order']['instrument_type'];
                        $collection_data['collectionAmount'] = $memo_info_arr['Memo']['gross_value'];
                        $collection_data['collectionDate'] = date('Y-m-d');
                        $collection_data['created_at'] = $this->current_datetime();
                        $collection_data['territory_id'] = $memo_info_arr['Memo']['territory_id'];
                        $collection_data['outlet_id'] = $memo_info_arr['Memo']['outlet_id'];
                        $collection_data['market_id'] = $memo_info_arr['Memo']['market_id'];
                        $collection_data['office_id'] = $memo_info_arr['Memo']['office_id'];
                        $this->Collection->create();
                        if (!$this->Collection->save($collection_data)) {
                            throw new Exception();
                        }
                        //end collection careate    
                    }
                }
                $datasource->commit();
                $this->Session->setFlash(__('Successfully Updated.'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } catch (Exception $e) {
                $datasource->rollback();
                $this->redirect(array('action' => 'index'));
            }
        }




        $office_parent_id = $this->UserAuth->getOfficeParentId();

        $current_date = date('d-m-Y', strtotime($this->current_date()));

        if ($office_parent_id == 0) {
            $product_list = $this->Product->find('list', array('order' => array('order' => 'asc')));
            $distributor_conditions = array();
        } else {
            $office_id = $this->UserAuth->getOfficeId();
            $product_list = $this->Product->find('list', array(
                'order' => array('order' => 'asc')
            ));
            $distributor_conditions = array('DistOutletMap.office_id' => $office_id);
        }

        $distributers_list = $this->DistOutletMap->find('all', array(
            'conditions' => $distributor_conditions,
        ));
        foreach ($distributers_list as $key => $value) {
            $distributers[$value['Outlet']['id']] = $value['DistDistributor']['name'];
        }

        $office_outlets = $distributers;
        $this->set(compact('distributers'));
        $this->set(compact('office_outlets'));
        $this->set(compact('market_list'));
        $this->set(compact('product_list'));

        /* ------- start get edit data -------- */


        $existing_record = $this->Order->find('first', array(
            'conditions' => array('Order.order_no' => $memo_no)
        ));
        //pr($existing_record);die();
        $store_id = $existing_record['Order']['w_store_id'];
        //pr($store_id);
        $conditions = array('CurrentInventory.store_id' => $store_id, 'inventory_status_id' => 1);
        $products_from_ci = $this->CurrentInventory->find('all', array(
            'fields' => array('DISTINCT CurrentInventory.product_id'),
            'conditions' => $conditions,
        ));

        $product_ci = array();
        foreach ($products_from_ci as $each_ci) {
            $product_ci[] = $each_ci['CurrentInventory']['product_id'];
        }
        //pr($product_ci);die();
        /*$this->loadModel('DistProductPrice');
        $product_list_for_distributor = $this->DistProductPrice->find('all',array(
            //'conditions'=>array('DistProductPrice.product_id'=>$product_ci),
        ));
        $product_lists=array();
        foreach ($product_list_for_distributor as $val) {
            $product_lists[]=$val['DistProductPrice']['product_id'];
        }*/

        $products = $this->Product->find('all', array(
            'conditions' => array(
                'Product.id' => $product_ci,
                'Product.is_distributor_product' => 1,
            ),
            'order' => array('Product.order' => 'asc'),
            'joins' => array(
                array(
                    'table' => 'products',
                    'alias' => 'ParentProduct',
                    'type' => 'left',
                    'conditions' => 'ParentProduct.id=Product.parent_id'
                )
            ),
            'fields' => array('Product.id as id', 'Product.name as name', 'ParentProduct.id as p_id', 'ParentProduct.name as p_name'),
            'recursive' => -1
        ));

        $product_grops = $this->Product->find('all', array(
            'conditions' => array(
                'Product.id' => $product_lists,
                'Product.is_distributor_product' => 1,
            ),
            'order' => array('Product.order' => 'asc'),
            'joins' => array(
                array(
                    'table' => 'products',
                    'alias' => 'ParentProduct',
                    'type' => 'left',
                    'conditions' => 'ParentProduct.id=Product.parent_id'
                )
            ),
            'fields' => array('Product.id as id', 'Product.name as name', 'ParentProduct.id as p_id', 'ParentProduct.name as p_name'),
            'recursive' => -1
        ));
        //echo $this->Product->getLastQuery();


        $group_product = array();
        foreach ($product_grops as $data) {
            if ($data[0]['p_id']) {
                $group_product[$data[0]['p_id']][] = $data[0]['id'];
            } else {
                $group_product[$data[0]['id']][] = $data[0]['id'];
            }
        }


        $product_array = array();

        foreach ($products as $data) {
            if ($data['0']['p_id'] && count($group_product[$data[0]['p_id']]) <= 1) {
                $name = $data[0]['p_name'];
            } else {
                $name = $data[0]['name'];
            }
            $product_array[$data[0]['id']] = $name;
        }
        $products = $product_array;

        //pr($products);die();
        $this->set(compact('products'));
        $details_data = array();
        foreach ($existing_record['OrderDetail'] as $detail_val) {
            if ($detail_val['virtual_product_id']) {
                $product = $detail_val['virtual_product_id'];
            } else {
                $product = $detail_val['product_id'];
            }
            //pr($product);
            if ($detail_val['product_combination_id']) {
                $combined_product = $this->CombinationDetailsV2->find('all', array(
                    'conditions' => array('CombinationDetailsV2.combination_id' => $detail_val['product_combination_id']),
                    'fields' => array('product_id'),
                    'recursive' => -1
                ));
                $combined_product = array_map(function ($val) {
                    return $val['CombinationDetailsV2']['product_id'];
                }, $combined_product);
                $combined_product = implode(',', $combined_product);
                $detail_val['combined_product'] = $combined_product;
            }
            $details_data[] = $detail_val;
        }

        $existing_record['OrderDetail'] = $details_data;
        for ($i = 0; $i < count($details_data); $i++) {
            $measurement_unit_id = $details_data[$i]['measurement_unit_id'];
            if ($measurement_unit_id != 0) {
                $measurement_unit_name = $this->MeasurementUnit->find('all', array(
                    'conditions' => array('MeasurementUnit.id' => $measurement_unit_id),
                    'fields' => array('name'),
                    'recursive' => -1
                ));
                $existing_record['OrderDetail'][$i]['measurement_unit_name'] = $measurement_unit_name[0]['MeasurementUnit']['name'];
            }
        }
        if (!empty($existing_record['OrderDetail'])) {
            foreach ($existing_record['OrderDetail'] as $key => $value) {

                if ($value['virtual_product_id']) {
                    $value['product_id'] = $value['virtual_product_id'];
                }

                $OrderDetail_record[$value['product_id']] = $value;
                $product_info = $this->Product->find('first', array(
                    'conditions' => array('Product.id' => $value['product_id']),
                    'recursive' => -1
                ));
                $existing_record['OrderDetail'][$key]['product_type_id'] = $product_info['Product']['product_type_id'];

                $stoksinfo = $this->CurrentInventory->find('all', array(
                    'conditions' => array(
                        'CurrentInventory.store_id' => $existing_record['Order']['w_store_id'],
                        'CurrentInventory.product_id' => $value['product_id']
                    ),
                    'fields' => array('sum(qty) as total'),
                ));
                $total_qty = $stoksinfo[0][0]['total'];
                $sales_total_qty = $this->unit_convertfrombase($value['product_id'], $value['measurement_unit_id'], $total_qty);
                $existing_record['OrderDetail'][$key]['aso_stock_qty'] = $sales_total_qty;
                $stoks = $this->CurrentInventory->find('first', array(
                    'conditions' => array(
                        'CurrentInventory.store_id' => $existing_record['Order']['w_store_id'],
                        'CurrentInventory.product_id' => $value['product_id']
                    ),
                    'fields' => array('Product.id', 'Product.name', 'CurrentInventory.qty'),
                ));
                $productName = $this->Product->find('first', array(
                    'conditions' => array('id' => $value['product_id']),
                    'fields' => array('id', 'name'),
                    'recursive' => -1
                ));
            }
        }

        $existing_record['office_id'] = $existing_record['SalesPerson']['office_id'];
        $existing_record['territory_id'] = $existing_record['Order']['territory_id'];
        $existing_record['market_id'] = $existing_record['Order']['market_id'];
        $existing_record['outlet_id'] = $existing_record['Order']['outlet_id'];
        $existing_record['order_time'] = date('d-m-Y', strtotime($existing_record['Order']['order_time']));
        $existing_record['order_date'] = date('d-m-Y', strtotime($existing_record['Order']['order_date']));
        $existing_record['order_no'] = $existing_record['Order']['order_no'];
        $existing_record['memo_reference_no'] = $existing_record['Order']['memo_reference_no'];
        $existing_record['order_time'] = $existing_record['Order']['order_time'];
        $existing_record['order_reference_no'] = $existing_record['Order']['order_reference_no'];
        $existing_record['reference_number'] = $existing_record['Order']['instrument_reference_no'];
        $existing_record['gross_value'] = $existing_record['Order']['gross_value'];

        //pr($existing_record);die();
        $store_id = $this->Store->find('first', array(
            'fields' => array('Store.id'),
            'conditions' => array('Store.id' => $existing_record['Order']['w_store_id']),
            'recursive' => -1
        ));

        $open_bonus_product = $this->Product->find('all', array(
            'fields' => array('Product.id', 'Product.name'),
            'joins' => array(
                array(
                    'table' => 'current_inventories',
                    'alias' => 'CurrentInventory',
                    'type' => 'Inner',
                    'conditions' => 'CurrentInventory.product_id=Product.id'
                ),
                array(
                    'table' => 'open_combination_products',
                    'alias' => 'OpenCombinationProduct',
                    'type' => 'Inner',
                    'conditions' => 'OpenCombinationProduct.product_id=Product.id'
                ),
                array(
                    'table' => 'open_combinations',
                    'alias' => 'OpenCombination',
                    'type' => 'Inner',
                    'conditions' => 'OpenCombinationProduct.combination_id=OpenCombination.id'
                ),

            ),
            'conditions' => array(
                'CurrentInventory.qty >' => 0,
                'CurrentInventory.store_id' => $store_id['Store']['id'],
                'OpenCombination.start_date <=' => $existing_record['Order']['order_date'],
                'OpenCombination.end_date >=' => $existing_record['Order']['order_date'],
                'OpenCombination.is_bonus' => 1
            ),
            'recursive' => -1
        ));
        $open_bonus_product_option = array();
        foreach ($open_bonus_product as $bonus_product) {
            $open_bonus_product_option[$bonus_product['Product']['id']] = $bonus_product['Product']['name'];
        }

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_conditions = array();
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }

        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

        $office_id = $existing_record['office_id'];
        $territory_id = $existing_record['territory_id'];
        $market_id = $existing_record['market_id'];
        $outlet_id = $existing_record['outlet_id'];
        $outlets = array();


        $territories_list = $this->Territory->find('all', array(
            'conditions' => array('Territory.office_id' => $office_id),
            'order' => array('Territory.name' => 'asc')
        ));
        $territories = array();
        foreach ($territories_list as $t_result) {
            $territories[$t_result['Territory']['id']] = $t_result['Territory']['name'] . ' (' . $t_result['SalesPerson']['name'] . ')';
        }


        $territory_ids = array($territory_id);
        $markets = $this->Market->find('list', array(
            'conditions' => array('territory_id' => $territory_ids),
            'order' => array('name' => 'asc')
        ));

        $outlets = $distributers;

        $store_info = $this->Store->find('first', array(
            'conditions' => array(
                'Store.id' => $existing_record['Order']['w_store_id']
            ),
            'recursive' => -1
        ));
        $store_id = $store_info['Store']['id'];
        foreach ($existing_record['OrderDetail'] as $key => $single_product) {
            $total_qty_arr = $this->CurrentInventory->find('all', array(
                'conditions' => array('store_id' => $store_id, 'product_id' => $single_product['product_id']),
                'fields' => array('sum(qty) as total'),
                'recursive' => -1
            ));

            $total_qty = $total_qty_arr[0][0]['total'];

            $sales_total_qty = $this->unit_convertfrombase($single_product['product_id'], $single_product['measurement_unit_id'], $total_qty);

            $existing_record['OrderDetail'][$key]['stock_qty'] = $sales_total_qty;
        }


        $products_from_ci = $this->CurrentInventory->find('all', array(
            'fields' => array('DISTINCT CurrentInventory.product_id'),
            'conditions' => array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.qty > ' => 0, 'inventory_status_id' => 1)
        ));

        $product_ci = array();
        foreach ($products_from_ci as $each_ci) {
            $product_ci[] = $each_ci['CurrentInventory']['product_id'];
        }
        foreach ($existing_record['OrderDetail'] as $value) {
            $product_ci[] = $value['product_id'];
        }

        $product_ci_in = implode(",", $product_ci);
        $selected_bonus = array();
        $selected_set = array();
        $selected_policy_type = array();
        foreach ($existing_record['OrderDetail'] as $key => $value) {
            if ($value['virtual_product_id']) {
                $value['product_id'] = $value['virtual_product_id'];
            }
            $existing_product_category_id_array = $this->Product->find('all', array(
                'conditions' => array('Product.id' => $value['product_id']),
                'fields' => array('product_category_id'),
                'recursive' => -1
            ));

            $existing_product_category_id = $existing_product_category_id_array[0]['Product']['product_category_id'];

            if ($value['discount_amount'] && $value['policy_type'] == 3) {
                $selected_policy_type[$value['policy_id']] = 1;
            }
            if ($value['is_bonus'] == 3) {
                if ($value['policy_type'] == 3) {
                    $selected_policy_type[$value['policy_id']] = 2;
                }
                if ($value['other_info']) {
                    $other_info = json_decode($value['other_info'], 1);
                    $selected_set[$value['policy_id']] = $other_info['selected_set'];
                    $selected_bonus[$value['policy_id']][$other_info['selected_set']][$value['product_id']] = $value['sales_qty'];
                } else {
                    $selected_bonus[$value['policy_id']][1][$value['product_id']] = $value['sales_qty'];
                }
            }
        }

        $this->set(compact('selected_bonus', 'selected_set', 'selected_policy_type'));
        $distributor_info = $this->DistOutletMap->find('first', array(
            'conditions' => array(
                'DistOutletMap.office_id' => $office_id,
                'DistOutletMap.outlet_id' => $outlet_id,
            ),
        ));
        $distributor_id = $distributor_info['DistOutletMap']['dist_distributor_id'];
        $dist_balance_info = $this->DistDistributorBalance->find('first', array(
            'conditions' => array(
                'DistDistributorBalance.dist_distributor_id' => $distributor_id
            ),
            'limit' => 1,
            'recursive' => -1
        ));

        $existing_record['current_balance'] =  $dist_balance_info['DistDistributorBalance']['balance'];

        /* -------- create individual Product data --------- */
        global $cart_data, $matched_array;
        $cart_data = array();
        $matched_array = array();
        $qty_session_data = array();

        /* ---------creating prepare data---------- */
        $prepare_cart_data = array();
        if (!empty($filter_product['Product']['id'])) {
            $prepare_cart_data[$filter_product['Product']['id']]['Product'] = $filter_product['Product'];
            $prepare_cart_data[$filter_product['Product']['id']]['ProductPrice'] = $filter_product['ProductPrice'];
            foreach ($filter_product['Individual_slab'] as $individual_slab_val) {
                $prepare_cart_data[$filter_product['Product']['id']]['Individual_slab'][$individual_slab_val['min_qty']] = $individual_slab_val['price'];
            }
            if (!empty($filter_product['Combination'])) {
                $prepare_cart_data[$filter_product['Product']['id']]['Combination'] = $filter_product['Combination'];
            }
            if (!empty($filter_product['Combination_id'])) {
                /* ---- start ------- */
                $this->Combination->recursive = 2;
                $condition_value1['Combination.id'] = $filter_product['Combination_id']; //$filter_product['Combination']['id'];
                $combination_slab_data_option = array(
                    'conditions' => array($condition_value1),
                );
                $combination_slab_data = $this->Combination->find('all', $combination_slab_data_option);
                /* ----- end -------- */

                $combination_slab = array();
                foreach ($combination_slab_data as $combine_group) {
                    foreach ($combine_group['ProductCombination'] as $combine_val) {
                        $combination_slab[$combine_val['min_qty']][$combine_val['product_id']] = $combine_val['Childrel']['price'];
                    }
                }
                if (!empty($combination_slab)) {
                    $prepare_cart_data[$filter_product['Product']['id']]['Combined_slab'] = $combination_slab;
                }
            }
        }
        /* ------ unset cart data ------- */
        $user_office_id = $this->UserAuth->getOfficeId();
        $sales_person_list = $this->SalesPerson->find('list', array(
            'conditions' => array('SalesPerson.office_id' => $user_office_id)
        ));
        $count = 0;
        $product_measurement_units = $this->ProductMeasurement->find('list', array('fields' => array('product_id', 'measurement_unit_id')));
        $product_category_id_list = $this->Product->find('list', array('fields' => array('id', 'product_category_id')));


        $this->set(compact('offices', 'territories', 'markets', 'outlets', 'current_date', 'product_list', 'sale_type_list', 'existing_record', 'office_parent_id', 'product_measurement_units', 'product_category_id_list', 'open_bonus_product_option'));
    }
    public function admin_delete($id = null, $redirect = 1)
    {

        $this->loadModel('Product');
        $this->loadModel('Order');
        $this->loadModel('TempOrderDetail');
        $this->loadModel('OrderDetail');
        $this->loadModel('Deposit');
        $this->loadModel('Collection');
        $this->loadModel('Memo');
        $this->loadModel('MemoDetail');
        $this->loadModel('DistChallan');
        $this->loadModel('DistChallanDetail');
        $this->loadModel('ProductBatchInfo');
        //$this->check_data_by_company('Order',$id);
        if ($this->request->is('post')) {

            /*
             * This condition added for data synchronization 
             * Cteated by imrul in 09, April 2017
             * Duplicate order check
             */
            $count = $this->Order->find('count', array(
                'conditions' => array(
                    'Order.id' => $id
                )
            ));

            $order_id_arr = $this->Order->find('first', array(
                'conditions' => array(
                    'Order.id' => $id
                )
            ));

            $this->loadModel('Store');
            $store_id_arr = $this->Store->find('first', array(
                'conditions' => array(
                    'Store.office_id' => $order_id_arr['Order']['office_id']
                ),
                'recursive' => -1
            ));
            $store_id = $store_id_arr['Store']['id'];



            $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
            $product_list = Set::extract($products, '{n}.Product');
        }

        $order_id = $order_id_arr['Order']['id'];
        $order_no = $order_id_arr['Order']['order_no'];
        $this->Order->id = $order_id;

        $memo_info = array();
        $memo_info = $this->Memo->find('first', array(
            'conditions' => array('Memo.memo_no ' =>  $order_no),
            'recursive' => -1
        ));
        if (!empty($memo_info)) {
            $memo_id = $memo_info['Memo']['id'];
            $challan_info = $this->DistChallan->find('first', array(
                'conditions' => array('DistChallan.memo_id' => $memo_id),
                'recursive' => -1
            ));
            $challan_id = $challan_info['DistChallan']['id'];
            $this->DistChallanDetail->deleteAll(array('DistChallanDetail.challan_id' => $challan_id));
            $this->DistChallan->id = $challan_id;
            $this->DistChallan->delete();

            $this->admin_deletememo($memo_id, 0);
        }
        //pr($order_id);die();
        // $this->TempOrderDetail->deleteAll(array('TempOrderDetail.order_id' => $order_id));

        //-------get all details id------------\\

        $order_detilas_ids = $this->OrderDetail->find('list', array(
            'conditions' => array('OrderDetail.order_id' => $order_id),
            'fields' => array('OrderDetail.id', 'OrderDetail.product_id'),
            'recursive' => -1
        ));

        if (!empty($order_detilas_ids)) {
            $this->ProductBatchInfo->deleteAll(array('ProductBatchInfo.order_details_id' => array_keys($order_detilas_ids)));
        }

        //-------------end---------------\\


        $this->OrderDetail->deleteAll(array('OrderDetail.order_id' => $order_id));



        //$this->Deposit->deleteAll(array('Deposit.order_id' => $order_no));
        //$this->Collection->deleteAll(array('Collection.order_id' => $order_no));
        $this->Order->delete();


        if ($redirect == 1) {
            $this->flash(__('Order was not deleted'), array('action' => 'index'));
            $this->redirect(array('action' => 'index'));
        } else {
        }
    }
    public function admin_deletememo($id = null, $redirect = 1)
    {

        $this->loadModel('Product');
        $this->loadModel('Memo');
        $this->loadModel('MemoDetail');
        $this->loadModel('DistChallan');
        $this->loadModel('DistChallanDetail');
        $this->loadModel('Deposit');
        $this->loadModel('Collection');
        //pr($id);die();
        //start memo setting
        $this->loadModel('MemoSetting');
        $MemoSettings = $this->MemoSetting->find(
            'all',
            array(
                //'conditions' => array('ChallanDetail.challan_id' => $challan['Challan']['id']),
                'order' => array('id' => 'asc'),
                'recursive' => 0,
                //'limit' => 100
            )
        );

        foreach ($MemoSettings as $s_result) {
            //echo $s_result['MemoSetting']['name'].'<br>';
            if ($s_result['MemoSetting']['name'] == 'stock_validation') {
                $stock_validation = $s_result['MemoSetting']['value'];
            }
            if ($s_result['MemoSetting']['name'] == 'stock_hit') {
                $stock_hit = $s_result['MemoSetting']['value'];
            }

            if ($s_result['MemoSetting']['name'] == 'ec_calculation') {
                $ec_calculation = $s_result['MemoSetting']['value'];
            }
            if ($s_result['MemoSetting']['name'] == 'oc_calculation') {
                $oc_calculation = $s_result['MemoSetting']['value'];
            }

            if ($s_result['MemoSetting']['name'] == 'sales_calculation') {
                $sales_calculation = $s_result['MemoSetting']['value'];
            }
            if ($s_result['MemoSetting']['name'] == 'stamp_calculation') {
                $stamp_calculation = $s_result['MemoSetting']['value'];
            }
            //pr($MemoSetting);
        }

        $this->set(compact('stock_validation'));
        //end memo setting


        if ($this->request->is('post')) {


            /*
             * This condition added for data synchronization 
             * Cteated by imrul in 09, April 2017
             * Duplicate memo check
             */
            $count = $this->Memo->find('count', array(
                'conditions' => array(
                    'Memo.id' => $id
                )
            ));

            $memo_id_arr = $this->Memo->find('first', array(
                'conditions' => array(
                    'Memo.id' => $id
                )
            ));

            $this->loadModel('Store');
            $store_id_arr = $this->Store->find('first', array(
                'conditions' => array(
                    //'Store.territory_id'=> $memo_id_arr['Memo']['territory_id'],
                    'Store.office_id' => $memo_id_arr['Memo']['office_id'],
                    'Store.store_type_id' => 2,
                ),
                'recursive' => -1
            ));
            $store_id = $store_id_arr['Store']['id'];



            $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
            $product_list = Set::extract($products, '{n}.Product');


            // EC Calculation 
            if ($ec_calculation) {
                $this->ec_calculation($memo_id_arr['Memo']['gross_value'], $memo_id_arr['Memo']['outlet_id'], $memo_id_arr['Memo']['territory_id'], $memo_id_arr['Memo']['memo_date'], 2);
                // OC Calculation 
            }
            if ($ec_calculation) {
                $this->oc_calculation($memo_id_arr['Memo']['territory_id'], $memo_id_arr['Memo']['gross_value'], $memo_id_arr['Memo']['outlet_id'], $memo_id_arr['Memo']['memo_date'], $memo_id_arr['Memo']['memo_time'], 2);
            }



            for ($memo_detail_count = 0; $memo_detail_count < count($memo_id_arr['MemoDetail']); $memo_detail_count++) {
                if ($memo_id_arr['MemoDetail'][$memo_detail_count]['virtual_product_id']) {
                    $product_id = $memo_id_arr['MemoDetail'][$memo_detail_count]['virtual_product_id'];
                } else {
                    $product_id = $memo_id_arr['MemoDetail'][$memo_detail_count]['product_id'];
                }

                $sales_qty = $memo_id_arr['MemoDetail'][$memo_detail_count]['sales_qty'];
                $measurement_unit_id = $memo_id_arr['MemoDetail'][$memo_detail_count]['measurement_unit_id'];
                $sales_price = $memo_id_arr['MemoDetail'][$memo_detail_count]['price'];
                $memo_territory_id = $memo_id_arr['Memo']['territory_id'];
                $memo_no = $memo_id_arr['Memo']['memo_no'];
                $memo_date = $memo_id_arr['Memo']['memo_date'];
                $outlet_id = $memo_id_arr['Memo']['outlet_id'];
                $market_id = $memo_id_arr['Memo']['market_id'];

                $punits_pre = $this->search_array($product_id, 'id', $product_list);
                if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
                    $base_quantity = $sales_qty;
                } else {
                    $base_quantity = $this->unit_convert($product_id, $measurement_unit_id, $sales_qty);
                }

                $update_type = 'add';
                $this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 12, $memo_id_arr['Memo']['memo_date']);



                // subract sales achievement and stamp achievemt 
                // sales calculation
                $t_price = $sales_qty * $sales_price;
                if ($sales_calculation) {
                    $this->sales_calculation($product_id, $memo_territory_id, $sales_qty, $t_price, $memo_date, 1);
                }

                //stamp calculation
                if ($stamp_calculation) {
                    $this->stamp_calculation($memo_no, $memo_territory_id, $product_id, $outlet_id, $sales_qty, $memo_date, 1, $t_price, $market_id);
                }
            }

            $memo_id = $memo_id_arr['Memo']['id'];
            $memo_no = $memo_id_arr['Memo']['memo_no'];

            $this->MemoDetail->deleteAll(array('MemoDetail.memo_id' => $memo_id));
            //$this->Deposit->deleteAll(array('Deposit.memo_id' => $memo_no));
            $this->Deposit->deleteAll(array('Deposit.memo_id' => $memo_id));
            $this->Collection->deleteAll(array('Collection.memo_id' => $memo_id));

            $this->Memo->id = $memo_id;
            //pr($this->Memo->id);die();
            $this->Memo->delete();



            /*$challan_info = $this->DistChallan->find('first',array(
                'conditions'=>array('DistChallan.memo_id' => $memo_id),
                'recursive'=> -1
            ));
			$challan_id = $challan_info['DistChallan']['id'];
			$this->DistChallanDetail->deleteAll(array('DistChallanDetail.challan_id' => $challan_id));
			$this->DistChallan->id = $challan_id;
            $this->DistChallan->delete();*/



            if ($redirect == 1) {
                $this->flash(__('Memo was not deleted'), array('action' => 'index'));
                $this->redirect(array('action' => 'index'));
            } else {
            }
        }
    }
    public function update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct', $transaction_type_id = 0, $transaction_date = '')
    {

        $this->loadModel('CurrentInventory');

        $find_type = 'all';
        if ($update_type == 'add')
            $find_type = 'first';

        /* ---------------------prodcut expire limit -------- */
        $this->loadModel('ProductMonth');

        $product_expire_month_info = $this->ProductMonth->find('first', array(
            'conditions' => array(
                'ProductMonth.product_id' => $product_id
            ),
            'fields' => array('ProductMonth.day_month'),
            'recursive' => -1
        ));
        if (empty($product_expire_month_info)) {
            $productExpireLimit = 0;
        } else {
            $productExpireLimit = $product_expire_month_info['ProductMonth']['day_month'];
        }

        $p_expire_date = date('Y-m-t', strtotime("+" . $productExpireLimit . " months"));

        //--------------end---------------\\

        $inventory_info = $this->CurrentInventory->find($find_type, array(
            'conditions' => array(
                //'CurrentInventory.qty >=' => 0,
                'CurrentInventory.store_id' => $store_id,
                'CurrentInventory.inventory_status_id' => 1,
                'CurrentInventory.product_id' => $product_id,
                "(CurrentInventory.expire_date is null OR CurrentInventory.expire_date > '$p_expire_date' )",
                //'CurrentInventory.expire_date >' => $p_expire_date,
            ),
            'order' => array('CurrentInventory.expire_date' => 'asc'),
            'recursive' => -1
        ));



        if ($update_type == 'deduct') {
            foreach ($inventory_info as $val) {
                if ($quantity <= $val['CurrentInventory']['qty']) {
                    $this->CurrentInventory->id = $val['CurrentInventory']['id'];
                    $this->CurrentInventory->updateAll(
                        array(
                            'CurrentInventory.qty' => 'CurrentInventory.qty - ' . $quantity,
                            'CurrentInventory.transaction_type_id' => $transaction_type_id,
                            'CurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                            'CurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                        ),
                        array('CurrentInventory.id' => $val['CurrentInventory']['id'])
                    );
                    break;
                } else {


                    if ($val['CurrentInventory']['qty'] > 0) {
                        $quantity = $quantity - $val['CurrentInventory']['qty'];
                        $this->CurrentInventory->id = $val['CurrentInventory']['id'];
                        $this->CurrentInventory->updateAll(
                            array(
                                'CurrentInventory.qty' => 'CurrentInventory.qty - ' . $val['CurrentInventory']['qty'],
                                'CurrentInventory.transaction_type_id' => $transaction_type_id,
                                'CurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                                'CurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                            ),
                            array('CurrentInventory.id' => $val['CurrentInventory']['id'])
                        );
                    }
                }
            }
        } else {
            /* $this->CurrentInventory->updateAll(array('CurrentInventory.qty' => 'CurrentInventory.qty + '.$inventory_info['CurrentInventory']['qty']),array('CurrentInventory.id' => $inventory_info['CurrentInventory']['id'])); */
            if (!empty($inventory_info)) {

                $this->CurrentInventory->updateAll(
                    array('CurrentInventory.qty' => 'CurrentInventory.qty + ' . $quantity, 'CurrentInventory.transaction_type_id' => $transaction_type_id, 'CurrentInventory.store_id' => $store_id, 'CurrentInventory.transaction_date' => "'" . $transaction_date . "'"),
                    array('CurrentInventory.id' => $inventory_info['CurrentInventory']['id'])
                );
            }
        }

        return true;
    }
    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_view($id = null)
    {

        $this->set('page_title', 'Distributor Challan Details');
        if (!$this->DistChallan->exists($id)) {
            throw new NotFoundException(__('Invalid challan'));
        }
        $options = array(
            'conditions' => array(
                'DistChallan.' . $this->DistChallan->primaryKey => $id
            ),
            'recursive' => 0
        );
        $challan = $this->DistChallan->find('first', $options);
        $challandetail = $this->DistChallanDetail->find(
            'all',
            array(
                'conditions' => array('DistChallanDetail.challan_id' => $challan['DistChallan']['id']),
                'order' => array('Product.order' => 'asc'),
                'fields' => 'DistChallanDetail.*,Product.product_code,Product.name,MeasurementUnit.name',
                'recursive' => 0
            )
        );
        $this->loadModel('User');
        $this->loadModel('SalesPerson');
        $user_name = $this->User->find('first', array(
            'conditions' => array('User.id' => $challan['DistChallan']['created_by']),
            'recursive' => -1
        ));
        //pr($challandetail);die();
        $office_paren_id = $this->UserAuth->getOfficeParentId();
        $so_info = $this->SalesPerson->find('first', array(
            'fields' => array('SalesPerson.*', 'Territory.*', 'Office.office_name', 'Office.address'),
            'conditions' => array('SalesPerson.id' => $challan['SalesPerson']['id']),
            'recursive' => 0
        ));

        $this->loadModel('DistDistributor');
        $distributors_all = $this->DistDistributor->find('list', array('order' => array('DistDistributor.name' => 'asc')));
        $this->set(compact('distributors_all'));

        if ($this->request->is('post')) {
            //pr($this->request->data);die();
            if ($this->request->data['DistChallan']['received_date']) {
                //pr($this->request->data);die();
                if ($challan['DistChallan']['status'] > 1) {
                    $this->Session->setFlash(__('Challan has already received.'), 'flash/success');
                    $this->redirect(array('action' => 'index'));
                }

                // update dms inventory 
                $this->loadModel('DistChallan');

                $chalan_received_date = date('Y-m-d', strtotime($this->request->data['DistChallan']['received_date']));
                $chalan_updated_by = $this->UserAuth->getUserId();
                $dist_store_id = $challan['DistChallan']['receiver_dist_store_id'];
                $memo_date = $challan['DistChallan']['challan_date'];
                $sql = "exec received_dist_challan_from_memo $id,$chalan_updated_by,'$chalan_received_date',$dist_store_id,'$memo_date'";
                $result = $this->DistChallan->query($sql);

                $this->Session->setFlash(__('Challan has been received.'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('Challan Received Date is required.'), 'flash/error');
                $this->redirect(array('action' => 'view/' . $id));
            }
        }

        $this->loadModel('Territory');
        $territories = $this->Territory->find('list', array('conditions' => array('id' => $challan['SalesPerson']['territory_id'])));

        $this->set(compact('challan', 'challandetail', 'office_paren_id', 'territories', 'user_name', 'so_info'));
    }



    /**
     * admin_delete method
     *
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @param string $id
     * @return void
     */
    /*public function admin_delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Challan->id = $id;
        if (!$this->Challan->exists()) {
            throw new NotFoundException(__('Invalid challan'));
        }
        if ($this->Challan->delete()) {
            $this->flash(__('Challan deleted'), array('action' => 'index'));
        }
        $this->flash(__('Challan was not deleted'), array('action' => 'index'));
        $this->redirect(array('action' => 'index'));
    }*/


    public function get_product()
    {
        $this->loadModel('Product');
        $rs = array(array('id' => '', 'name' => '---- Select -----'));
        $type_id = $this->request->data['product_type_id'];
        if ($type_id == '') {
            $rs = array(array('id' => '', 'name' => '---- Select -----'));
        } else {
            $product = $this->Product->find('all', array(
                'conditions' => array('Product.product_type_id' => $type_id),
                'order' => array('Product.order' => 'ASC'),
                'recursive' => -1
            ));
            //pr($months);
            $data_array = Set::extract($product, '{n}.Product');
            if (!empty($product)) {
                echo json_encode(array_merge($rs, $data_array));
            } else {
                echo json_encode($rs);
            }
        }
        $this->autoRender = false;
    }

    public function get_product_list()
    {
        $this->loadModel('Product');
        //$rs = array( '' => '---- Select -----');
        $type_id = $this->request->data['product_type_id'];
        if ($type_id == '') {
            $product = array('' => '---- Select -----');
        } else {
            $product = $this->Product->find('list', array(
                'conditions' => array('Product.product_type_id' => $type_id),
                'recursive' => -1
            ));
            if (empty($product)) {
                $product = array('' => '---- Select -----');
            } else {
                $product[0] = '--- Select ---';
            }
        }
        echo json_encode($product);
        $this->autoRender = false;
    }

    //for challan referance number check
    public function admin_challan_referance_validation()
    {

        if ($this->request->is('post')) {
            $challan_referance_no = $this->request->data['challan_referance_no'];
            $challan_id = $this->request->data['challan_id'];

            if ($challan_id) {
                $con = array('Challan.challan_referance_no' => $challan_referance_no, 'Challan.id !=' => $challan_id);
            } else {
                $con = array('Challan.challan_referance_no' => $challan_referance_no);
            }

            $challan_list = $this->Challan->find('list', array(
                'conditions' => $con,
                'fields' => array('challan_referance_no'),
                'recursive' => -1
            ));

            $challan_list = count($challan_list);

            echo json_encode($challan_list);
        }

        $this->autoRender = false;
    }


    function get_dist_list_by_office_id()
    {

        $this->loadModel('DistDistributor');
        $office_id = $this->request->data['office_id'];
        $output = "<option value=''>--- Select Distributor ---</option>";
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        if ($user_group_id == 1029 || $user_group_id == 1028) {
            if ($user_group_id == 1028) {
                $dist_ae_info = $this->DistAreaExecutive->find('first', array(
                    'conditions' => array('DistAreaExecutive.user_id' => $user_id),
                    'recursive' => -1,
                ));
                $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
                $dist_tso_info = $this->DistTso->find('list', array(
                    'conditions' => array('dist_area_executive_id' => $dist_ae_id),
                    'fields' => array('DistTso.id', 'DistTso.dist_area_executive_id'),
                ));

                $dist_tso_id = array_keys($dist_tso_info);
            } else {
                $dist_tso_info = $this->DistTso->find('first', array(
                    'conditions' => array('DistTso.user_id' => $user_id),
                    'recursive' => -1,
                ));
                $dist_tso_id = $dist_tso_info['DistTso']['id'];
            }

            $tso_dist_list = $this->DistTsoMapping->find('list', array(
                'conditions' => array(
                    'dist_tso_id' => $dist_tso_id,
                ),
                'fields' => array('DistTsoMapping.dist_distributor_id', 'DistTsoMapping.dist_tso_id'),
            ));

            $conditions = array(
                'conditions' => array('DistDistributor.id' => array_keys($tso_dist_list), 'DistDistributor.is_active' => 1), 'order' => array('DistDistributor.name' => 'asc'),
            );
        } elseif ($user_group_id == 1034) {
            $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
            $this->loadModel('DistUserMapping');
            $distributor = $this->DistUserMapping->find('first', array(
                'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
            ));
            $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
            $conditions = array(
                'conditions' => array('DistDistributor.id' => $distributor_id, 'DistDistributor.is_active' => 1), 'order' => array('DistDistributor.name' => 'asc'),
            );
        } else {
            $conditions = array(
                'conditions' => array('DistDistributor.office_id' => $office_id, 'DistDistributor.is_active' => 1), 'order' => array('DistDistributor.name' => 'asc'),
            );
        }

        if ($office_id) {
            $dist_list = $this->DistDistributor->find('list', $conditions);

            if ($dist_list) {
                foreach ($dist_list as $key => $data) {
                    $output .= "<option value='$key'>$data</option>";
                }
            }
        }
        echo $output;
        $this->autoRender = false;
    }

    public function getProductPrice($product_id, $challan_date)
    {
        $this->LoadModel('ProductPrice');
        $product_prices = $this->ProductPrice->find('first', array(
            'conditions' => array(
                'ProductPrice.product_id' => $product_id,
                'ProductPrice.effective_date <=' => $challan_date,
                'ProductPrice.has_combination' => 0,
                'OR' => array('ProductPrice.project_id is null', 'ProductPrice.project_id' => 0),
            ),
            'order' => array('ProductPrice.effective_date DESC'),
            'recursive' => -1

        ));
        $this->autoRender = false;
        //pr($product_prices);exit;
        return $product_prices['ProductPrice'];
    }


    public function ec_calculation($gross_value, $outlet_id, $terrority_id, $memo_date, $cal_type)
    {
        // from so_id , retrieve aso_id,terrority_id :  no need as we have territory_id
        // check gross_value >0

        if ($gross_value > 0) {
            $this->loadModel('Outlet');
            // from outlet_id, retrieve pharma or non-pharma
            $outlet_info = $this->Outlet->find('first', array(
                'conditions' => array(
                    'Outlet.id' => $outlet_id
                ),
                'recursive' => -1
            ));

            if (!empty($outlet_info)) {
                $is_pharma_type = $outlet_info['Outlet']['is_pharma_type'];
                // from memo_date , split month and get month name and compare month table with memo year
                $memoDate = strtotime($memo_date);
                $month = date("n", $memoDate);
                $year = date("Y", $memoDate);
                $this->loadModel('Month');

                // from outlet_id, retrieve pharma or non-pharma
                $fasical_info = $this->Month->find('first', array(
                    'conditions' => array(
                        'Month.month' => $month,
                        'Month.year' => $year
                    ),
                    'recursive' => -1
                ));


                if (!empty($fasical_info)) {
                    $this->loadModel('SaleTargetMonth');
                    if ($cal_type == 1) {
                        if ($is_pharma_type == 1) {
                            $update_fields_arr = array('SaleTargetMonth.effective_call_pharma_achievement' => "SaleTargetMonth.effective_call_pharma_achievement+1");
                        } else if ($is_pharma_type == 0) {
                            $update_fields_arr = array('SaleTargetMonth.effective_call_non_pharma_achievement' => "SaleTargetMonth.effective_call_non_pharma_achievement+1");
                        }
                    } else {
                        if ($is_pharma_type == 1) {
                            $update_fields_arr = array('SaleTargetMonth.effective_call_pharma_achievement' => "SaleTargetMonth.effective_call_pharma_achievement-1");
                        } else if ($is_pharma_type == 0) {
                            $update_fields_arr = array('SaleTargetMonth.effective_call_non_pharma_achievement' => "SaleTargetMonth.effective_call_non_pharma_achievement-1");
                        }
                    }

                    $conditions_arr = array('SaleTargetMonth.product_id' => 0, 'SaleTargetMonth.territory_id' => $terrority_id, 'SaleTargetMonth.fiscal_year_id' => $fasical_info['Month']['fiscal_year_id'], 'SaleTargetMonth.month_id' => $fasical_info['Month']['id']);

                    $this->SaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
                }
            }
        }
    }

    // cal_type=1 means increment and 2 means deduction 
    // it will be called from  memo_details 
    public function sales_calculation($product_id, $terrority_id, $quantity, $gross_value, $memo_date, $cal_type)
    {
        // from so_id , retrieve aso_id,terrority_id : no need as we have territory_id
        // from memo_date , split month and get month name and compare month table with memo year
        $memoDate = strtotime($memo_date);
        $month = date("n", $memoDate);
        $year = date("Y", $memoDate);
        $this->loadModel('Month');
        // from outlet_id, retrieve pharma or non-pharma
        $fasical_info = $this->Month->find('first', array(
            'conditions' => array(
                'Month.month' => $month,
                'Month.year' => $year
            ),
            'recursive' => -1
        ));

        if (!empty($fasical_info)) {
            $this->loadModel('SaleTargetMonth');
            if ($cal_type == 1) {
                $update_fields_arr = array('SaleTargetMonth.target_quantity_achievement' => "SaleTargetMonth.target_quantity_achievement+$quantity", 'SaleTargetMonth.target_amount_achievement' => "SaleTargetMonth.target_amount_achievement+$gross_value");
            } else {
                $update_fields_arr = array('SaleTargetMonth.target_quantity_achievement' => "SaleTargetMonth.target_quantity_achievement-$quantity", 'SaleTargetMonth.target_amount_achievement' => "SaleTargetMonth.target_amount_achievement-$gross_value");
            }

            $conditions_arr = array('SaleTargetMonth.product_id' => $product_id, 'SaleTargetMonth.territory_id' => $terrority_id, 'SaleTargetMonth.fiscal_year_id' => $fasical_info['Month']['fiscal_year_id'], 'SaleTargetMonth.month_id' => $fasical_info['Month']['id']);
            $this->SaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
        }
    }

    // cal_type=1 means increment and 2 means deduction 
    // it will be called from memo not from memo_details 
    public function oc_calculation($terrority_id, $gross_value, $outlet_id, $memo_date, $memo_time, $cal_type)
    {

        // from so_id , retrieve aso_id,terrority_id :  no need as we have territory_id
        // check gross_value >0
        if ($gross_value > 0) {
            $this->loadModel('Memo');
            // this will be updated monthly , if done then increment else no action
            $month_first_date = date('Y-m-01', strtotime($memo_date));
            $count = $this->Memo->find('count', array(
                'conditions' => array(
                    'Memo.outlet_id' => $outlet_id,
                    'Memo.memo_date >= ' => $month_first_date,
                    'Memo.memo_time < ' => $memo_time
                )
            ));

            if ($count == 0) {

                $this->loadModel('Outlet');
                // from outlet_id, retrieve pharma or non-pharma
                $outlet_info = $this->Outlet->find('first', array(
                    'conditions' => array(
                        'Outlet.id' => $outlet_id
                    ),
                    'recursive' => -1
                ));

                if (!empty($outlet_info)) {
                    $is_pharma_type = $outlet_info['Outlet']['is_pharma_type'];
                    // from memo_date , split month and get month name and compare month table with memo year
                    $memoDate = strtotime($memo_date);
                    $month = date("n", $memoDate);
                    $year = date("Y", $memoDate);
                    $this->loadModel('Month');
                    // from outlet_id, retrieve pharma or non-pharma
                    $fasical_info = $this->Month->find('first', array(
                        'conditions' => array(
                            'Month.month' => $month,
                            'Month.year' => $year
                        ),
                        'recursive' => -1
                    ));

                    if (!empty($fasical_info)) {
                        $this->loadModel('SaleTargetMonth');
                        if ($cal_type == 1) {
                            if ($is_pharma_type == 1) {
                                $update_fields_arr = array('SaleTargetMonth.outlet_coverage_pharma_achievement' => "SaleTargetMonth.outlet_coverage_pharma_achievement+1");
                            } else if ($is_pharma_type == 0) {
                                $update_fields_arr = array('SaleTargetMonth.outlet_coverage_non_pharma_achievement' => "SaleTargetMonth.outlet_coverage_non_pharma_achievement+1");
                            }
                        } else {
                            if ($is_pharma_type == 1) {
                                $update_fields_arr = array('SaleTargetMonth.outlet_coverage_pharma_achievement' => "SaleTargetMonth.outlet_coverage_pharma_achievement-1");
                            } else if ($is_pharma_type == 0) {
                                $update_fields_arr = array('SaleTargetMonth.outlet_coverage_non_pharma_achievement' => "SaleTargetMonth.outlet_coverage_non_pharma_achievement-1");
                            }
                        }

                        $conditions_arr = array('SaleTargetMonth.product_id' => 0, 'SaleTargetMonth.territory_id' => $terrority_id, 'SaleTargetMonth.fiscal_year_id' => $fasical_info['Month']['fiscal_year_id'], 'SaleTargetMonth.month_id' => $fasical_info['Month']['id']);
                        $this->SaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
                        //pr($conditions_arr);
                        //pr($update_fields_arr);
                        //exit;
                    }
                }
            }
        }
    }

    // it will be called from memo_details 
    public function stamp_calculation($memo_no, $terrority_id, $product_id, $outlet_id, $quantity, $memo_date, $cal_type, $gross_amount, $market_id)
    {
        // from outlet_id, get bonus_type_id and check if null then no action else action

        $this->loadModel('Outlet');
        // from outlet_id, retrieve pharma or non-pharma
        $outlet_info = $this->Outlet->find('first', array(
            'conditions' => array(
                'Outlet.id' => $outlet_id
            ),
            'recursive' => -1
        ));

        if (!empty($outlet_info) && $gross_amount > 0) {
            $bonus_type_id = $outlet_info['Outlet']['bonus_type_id'];
            if (($bonus_type_id === NULL) || (empty($bonus_type_id))) {
                // no action 
            } else {
                // from memo_date , split month and get month name and compare month table with memo year (get fascal year id)
                $memoDate = strtotime($memo_date);
                $month = date("n", $memoDate);
                $year = date("Y", $memoDate);
                $this->loadModel('Month');
                $fasical_info = $this->Month->find('first', array(
                    'conditions' => array(
                        'Month.month' => $month,
                        'Month.year' => $year
                    ),
                    'recursive' => -1
                ));

                if (!empty($fasical_info)) {
                    // check bonus card table , where is_active,and others  and get min qty per memo
                    $this->loadModel('BonusCard');
                    $bonus_card_info = $this->BonusCard->find('first', array(
                        'conditions' => array(
                            'BonusCard.fiscal_year_id' => $fasical_info['Month']['fiscal_year_id'],
                            'BonusCard.is_active' => 1,
                            'BonusCard.product_id' => $product_id,
                            'BonusCard.bonus_card_type_id' => $bonus_type_id
                        ),
                        'recursive' => -1
                    ));

                    // if exist min qty per memo , then stamp_no=mod(quantity/min qty per memo)
                    if (!empty($bonus_card_info)) {
                        $min_qty_per_memo = $bonus_card_info['BonusCard']['min_qty_per_memo'];
                        if ($min_qty_per_memo && $min_qty_per_memo <= $quantity) {
                            $stamp_no = floor($quantity / $min_qty_per_memo);
                            if ($cal_type != 1) {
                                $stamp_no = $stamp_no * (-1);
                                $quantity = $quantity * (-1);
                            }


                            $this->loadModel('StoreBonusCard');
                            $log_data = array();
                            $log_data['StoreBonusCard']['created_at'] = $this->current_datetime();
                            $log_data['StoreBonusCard']['territory_id'] = $terrority_id;
                            $log_data['StoreBonusCard']['outlet_id'] = $outlet_id;
                            $log_data['StoreBonusCard']['market_id'] = $market_id;
                            $log_data['StoreBonusCard']['product_id'] = $product_id;
                            $log_data['StoreBonusCard']['quantity'] = $quantity;
                            $log_data['StoreBonusCard']['no_of_stamp'] = $stamp_no;
                            $log_data['StoreBonusCard']['bonus_card_id'] = $bonus_card_info['BonusCard']['id'];
                            $log_data['StoreBonusCard']['bonus_card_type_id'] = $bonus_type_id;
                            $log_data['StoreBonusCard']['fiscal_year_id'] = $bonus_card_info['BonusCard']['fiscal_year_id'];
                            $log_data['StoreBonusCard']['memo_no'] = $memo_no;

                            $this->StoreBonusCard->create();
                            $this->StoreBonusCard->save($log_data);
                        }
                    }
                }
            }
        }
    }
    private function outletGroupCheck($outlet_id = 0)
    {
        if ($outlet_id) {
            $this->loadModel('Outlet');
            $result = $this->Outlet->find('first', array(
                'fields' => array('is_within_group'),
                'conditions' => array('Outlet.id' => $outlet_id),
                'recursive' => -1
            ));
            if ($result) {
                return $result['Outlet']['is_within_group'];
            } else {
                return 0;
            }
        }
    }
    private function productInjectableCheck($products_ids = array())
    {
        if ($products_ids) {
            $this->loadModel('Product');

            $result = $this->Product->find('first', array(
                'fields' => array('is_injectable'),
                'conditions' => array(
                    'Product.id' => $products_ids,
                    'Product.is_injectable' => 1
                ),
                'recursive' => -1
            ));
            if ($result) {
                return $result['Product']['is_injectable'];
            } else {
                return 0;
            }
        }
    }
    private function stock_check($store_id, $product_id, $qty)
    {
        $this->loadModel('CurrentInventory');
        $current_inventory = $this->CurrentInventory->find('all', array(
            'conditions' => array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.product_id' => $product_id),
            'joins' => array(
                array(
                    'table' => 'product_measurements',
                    'alias' => 'ProductMeasurement',
                    'type' => 'LEFT',
                    'conditions' => 'ProductMeasurement.product_id=Product.id AND ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id'
                )
            ),
            'group' => array('CurrentInventory.store_id', 'ProductMeasurement.qty_in_base', 'CurrentInventory.product_id HAVING (sum(CurrentInventory.qty))  >= ROUND((case when ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end) *' . $qty . ',0)'),
            'fields' => array('CurrentInventory.store_id', 'CurrentInventory.product_id', 'sum(CurrentInventory.qty) as qty')
        ));

        //pr($current_inventory);
        //echo $this->DistCurrentInventory->getLastQuery();exit;

        if (!$current_inventory) {
            return false;
        }

        return true;
    }
    public function search_array($value, $key, $array)
    {
        foreach ($array as $k => $val) {
            if ($val[$key] == $value) {
                return $array[$k];
            }
        }
        return null;
    }

    public function get_product_price_id($product_id, $product_prices, $all_product_id)
    {
        // echo $product_id.'--'.$product_prices.'<br>';
        $this->LoadModel('ProductCombination');
        $this->LoadModel('Combination');
        $data = array();
        $product_price = $this->ProductCombination->find('first', array(
            'conditions' => array(
                'ProductCombination.product_id' => $product_id,
                'ProductCombination.price' => $product_prices,
                'ProductCombination.effective_date <=' => $this->current_date(),
            ),
            'order' => array('ProductCombination.id' => 'DESC'),
            'recursive' => -1
        ));

        // pr($product_price);exit;
        // echo $this->ProductCombination->getLastquery().'<br>';
        if ($product_price) {
            $is_combine = 0;
            if ($product_price['ProductCombination']['combination_id'] != 0) {
                $combination = $this->Combination->find('first', array(
                    'conditions' => array('Combination.id' => $product_price['ProductCombination']['combination_id']),
                    'recursive' => -1
                ));
                $combination_product = explode(',', $combination['Combination']['all_products_in_combination']);
                foreach ($combination_product as $combination_prod) {
                    if ($product_id != $combination_prod && in_array($combination_prod, $all_product_id)) {
                        $data['combination_id'] = $product_price['ProductCombination']['combination_id'];
                        $data['product_price_id'] = $product_price['ProductCombination']['id'];
                        $is_combine = 1;
                        break;
                    }
                }
            }
            if ($is_combine == 0) {
                $product_price = $this->ProductCombination->find('first', array(
                    'conditions' => array(
                        'ProductCombination.product_id' => $product_id,
                        'ProductCombination.price' => $product_prices,
                        'ProductCombination.effective_date <=' => $this->current_date(),
                        'ProductCombination.parent_slab_id' => 0
                    ),
                    'order' => array('ProductCombination.id DESC'),
                    'recursive' => -1
                ));
                $data['combination_id'] = '';
                $data['product_price_id'] = $product_price['ProductCombination']['id'];
            }
            return $data;
        } else {
            $data['combination_id'] = '';
            $data['product_price_id'] = '';
            return $data;
        }
    }

    public function bouns_and_scheme_id_set($b_product_id = 0, $order_date = '')
    {
        $this->loadModel('Bonus');
        //$this->loadModel('OpenCombination');
        //$this->loadModel('OpenCombinationProduct');

        $bonus_result = array();

        $b_product_qty = 0;
        $bonus_id = 0;
        $bonus_scheme_id = 0;

        $bonus_info = $this->Bonus->find(
            'first',
            array(
                'conditions' => array(
                    'Bonus.effective_date <= ' => $order_date,
                    'Bonus.end_date >= ' => $order_date,
                    'Bonus.bonus_product_id' => $b_product_id
                ),
                'recursive' => -1,
            )
        );

        //pr($bonus_info);

        if ($bonus_info) {
            $bonus_table_id = $bonus_info['Bonus']['id'];
            $mother_product_id = $bonus_info['Bonus']['mother_product_id'];
            $mother_product_quantity = $bonus_info['Bonus']['mother_product_quantity'];

            $bonus_id = $bonus_table_id;

            //echo $bonus_id;
            //break;
        }


        /*echo 'Bonus = '.$bonus_id;
		echo '<br>';
		echo 'Bonus Scheme = '. $bonus_scheme_id;
		echo '<br>';
		echo '<br>';
		echo '<br>';*/

        $bonus_result['bonus_id'] = $bonus_id;
        $bonus_result['bonus_scheme_id'] = $bonus_scheme_id;

        return $bonus_result;
    }
}
