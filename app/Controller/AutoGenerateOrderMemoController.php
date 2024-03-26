<?php
App::uses('AppController', 'Controller');

/**
 * Memos Controller
 *
 * @property Memo $Memo
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */

Configure::write('debug', 2);
class AutoGenerateOrderMemoController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $uses = array('DistOrder', 'DistDistributor', 'DistSalesRepresentative', 'Thana', 'SalesPerson', 'DistMarket', 'DistSalesRepresentative', 'DistOutlet', 'Product', 'MeasurementUnit', 'DistSrProductPrice', 'DistSrProductCombination', 'DistSrCombination', 'DistOrderDetail', 'MeasurementUnit', 'DistRoute', 'DistTsoMappingHistory');
    public $components = array('Paginator', 'Session', 'Filter.Filter');


    function admin_create_sr_order()
    {
        Configure::write('debug', 2);
        date_default_timezone_set('Asia/Dhaka');
        $this->set('page_title', 'Create Memo');
        $query = "
            SELECT
                ot.id outlet_id,
                mk.id market_id,
                dr.id as route_id,
                dr.office_id as office_id,
                FLOOR(sum(qty_in_disp)) as qty
            FROM dist_ors_tornedo_offer oto 
            inner join dist_memos m on m.dist_memo_no=oto.memo_no
            inner join dist_outlets ot on ot.id=m.outlet_id
            inner join dist_markets mk on ot.dist_market_id=mk.id
            inner join dist_routes dr on dr.id=mk.dist_route_id
            where oto.is_auto_memo_created=0  --and dr.office_id=26 and ot.id=428847
            group by
                ot.id,
                mk.id,
                dr.id,
                dr.office_id
			order by 
				dr.office_id,
                dr.id,
				mk.id
		";
        $memo_data = $this->DistOrder->query($query);
        foreach ($memo_data as $data) {

            $query = "
			SELECT
				m.dist_memo_no as memo_no,
				oto.order_no as order_no
			FROM dist_ors_tornedo_offer oto 
			inner join dist_memos m on m.dist_memo_no=oto.memo_no
			inner join dist_outlets ot on ot.id=m.outlet_id
			where ot.id=" . $data[0]['outlet_id'];

            $outlet_all_memo_query = $this->DistOrder->query($query);
            $memo_no_text = '';
            $order_no_text = '';
            $memo_no_where = '';
            $is_are = count($outlet_all_memo_query) > 1 ? 'are' : 'is';
            foreach ($outlet_all_memo_query as $data_m) {
                $memo_no_text .= $data_m['0']['memo_no'] . ',';
                $order_no_text .= $data_m['0']['order_no'] . ',';
                $memo_no_where .= '\'' . $data_m['0']['memo_no'] . '\',';
            }
            $memo_no_text = rtrim($memo_no_text, ',');
            $order_no_text = rtrim($order_no_text, ',');
            $memo_no_where = rtrim($memo_no_where, ',');

            $order_date = date('Y-m-d');
            $office_id = $data[0]['office_id'];
            $market_id = $data[0]['market_id'];
            $outlet_id = $data[0]['outlet_id'];
            $route_id = $data[0]['route_id'];

            $mapping_info = $this->get_territory_thana_db_info($office_id, $market_id, $route_id, $order_date);

            $db_id = $mapping_info['db_id'];
            $sr_id = $mapping_info['sr_id'];
            $ae_id = $mapping_info['ae_id'];
            $tso_id = $mapping_info['tso_id'];
            $territory_id = $mapping_info['territory_id'];
            $thana_id = $mapping_info['thana_id'];
			
			echo 'db id:'.$db_id.' - sr id : '.$sr_id.'- AE id : '.$ae_id.'- TSO id :'.$tso_id.'- Territory id :'.$territory_id.'- $thana_id :'.$thana_id.'<br>';
			
            if (!$db_id || !$sr_id || !$ae_id || !$tso_id || !$territory_id || !$thana_id) {
                continue;
            }
            $this->loadModel('DistStore');
            $store_id_arr = $this->DistStore->find('first', array(
                'conditions' => array(
                    'DistStore.dist_distributor_id' => $db_id
                )
            ));

            $store_id = $store_id_arr['DistStore']['id'];

            $sales_person = $this->SalesPerson->find('list', array(
                'conditions' => array('territory_id' => $territory_id),
                'order' => array('name' => 'asc')
            ));

            $sp_id = $this->request->data['DistOrder']['sales_person_id'] = key($sales_person);

            $generate_order_no = 'O' . $sp_id . rand(0, 99) . date('d') . date('m') . date('h') . date('i') . date('s');
            $OrderData['office_id'] = $office_id;
            $OrderData['distributor_id'] = $db_id;
            $OrderData['sr_id'] = $sr_id;
            $OrderData['dist_route_id'] = $route_id;
            // $OrderData['sale_type_id'] = $this->request->data['DistOrder']['sale_type_id'];
            $OrderData['territory_id'] = $territory_id;
            $OrderData['market_id'] = $market_id;
            $OrderData['outlet_id'] = $outlet_id;
            // $OrderData['entry_date'] = $this->request->data['DistOrder']['entry_date'];
            $OrderData['order_date'] = $order_date;
            $OrderData['dist_order_no'] = $generate_order_no;
            $OrderData['gross_value'] = 0;
            $OrderData['cash_recieved'] = 0;
            $OrderData['credit_amount'] = 0;
            $OrderData['is_active'] = 1;
            $OrderData['status'] = 1;
            $OrderData['order_time'] = $this->current_datetime();
            $OrderData['sales_person_id'] = $sp_id;
            $OrderData['from_app'] = 0;
            $OrderData['action'] = 1;
            $OrderData['ae_id'] = $ae_id;
            $OrderData['tso_id'] = $tso_id;
            $OrderData['discount_value'] = 0;
            $OrderData['discount_percent'] = 0;
            $OrderData['total_discount'] = 0;
            $OrderData['created_at'] = $this->current_datetime();
            $OrderData['created_by'] = $this->UserAuth->getUserId();
            $OrderData['updated_at'] = $this->current_datetime();
            $OrderData['updated_by'] = $this->UserAuth->getUserId();
            $OrderData['office_id'] = $office_id ? $office_id : 0;
            $OrderData['thana_id'] = $thana_id ? $thana_id : 0;
            $OrderData['order_type'] = 1;
            $OrderData['remarks'] = "This is system generated order for ORS Tornedo Offer (22 June 2022 To 05 July 2022). Reference order number $is_are ($order_no_text)";
            $this->DistOrder->create();
            $datasource = $this->DistOrder->getDataSource();
            try {
                $datasource->begin();
                if (!$this->DistOrder->save($OrderData)) {
                    echo 'Order not saved' . $order_no_text;
                    throw new Exception();
                } else {
                    /*----- order detail  ----- */
                    $dist_order_id = $this->DistOrder->getLastInsertId();
                    $bonus_order_details = array();
                    $bonus_order_details['DistOrderDetail']['dist_order_id'] = $dist_order_id;
                    $bonus_order_details['DistOrderDetail']['product_id'] = 47;
                    $bonus_order_details['DistOrderDetail']['measurement_unit_id'] = 7;
                    $bonus_order_details['DistOrderDetail']['price'] = 0.0;
                    $bonus_order_details['DistOrderDetail']['is_bonus'] = 0;
                    $bonus_order_details['DistOrderDetail']['sales_qty'] = $data[0]['qty'];
                    if (!$this->DistOrderDetail->saveAll($bonus_order_details)) {
                        echo $order_no_text . " This order not created for order detail problem.<br>";
                        throw new Exception();
                    }
                    /*----- order detail  ----- */
                    if ($this->dist_order_delivery_schedule($sr_id, $generate_order_no)) {
                        $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
                        $product_list = Set::extract($products, '{n}.Product');
                        $generate_memo_no = 'M' . $sp_id . rand(0, 99) . date('d') . date('m') . date('h') . date('i') . date('s');
                        $total_product_data = array();
                        $MemoData = array();
                        $MemoData['office_id'] = $office_id;
                        $MemoData['distributor_id'] = $db_id;
                        $MemoData['sr_id'] = $sr_id;
                        $MemoData['dist_route_id'] = $route_id;
                        $MemoData['territory_id'] = $territory_id;

                        $MemoData['thana_id'] = $thana_id;
                        $MemoData['market_id'] = $market_id;
                        $MemoData['outlet_id'] = $outlet_id;
                        $MemoData['entry_date'] = date('Y-m-d', strtotime($order_date));
                        $MemoData['memo_date'] = date('Y-m-d', strtotime($order_date));

                        $MemoData['dist_memo_no'] = $generate_memo_no;
                        $MemoData['dist_order_no'] = $generate_order_no;

                        $MemoData['gross_value'] = 0;

                        $MemoData['total_vat'] = 0;
                        $MemoData['discount_type'] = 0;

                        $MemoData['cash_recieved'] = 0;
                        $MemoData['credit_amount'] = 0;

                        $MemoData['memo_time'] = $this->current_datetime();
                        $MemoData['sales_person_id'] = $sp_id;
                        $MemoData['ae_id'] = $ae_id;
                        $MemoData['tso_id'] = $tso_id;
                        $MemoData['discount_percent'] = 0;
                        $MemoData['discount_value'] = 0;
                        $MemoData['total_discount'] = 0;


                        $MemoData['is_active'] = 0;
                        $MemoData['from_app'] = 0;
                        $MemoData['status'] = 1;
                        $MemoData['action'] = 0;
                        $MemoData['is_program'] = 0;
                        $MemoData['memo_reference_no'] = '';

                        $MemoData['latitude'] = '';
                        $MemoData['longitude'] = '';

                        $MemoData['created_at'] = $this->current_datetime();
                        $MemoData['created_by'] = $this->UserAuth->getUserId();
                        $MemoData['updated_at'] = $this->current_datetime();
                        $MemoData['updated_by'] = $this->UserAuth->getUserId();
                        $MemoData['remarks'] =  "This is system generated memo for ORS Tornedo Offer (22 June 2022 To 05 July 2022).
                        Reference memo number $is_are ($memo_no_text)";

                        $this->loadmodel('DistMemo');
                        $this->DistMemo->create();

                        if (!$this->DistMemo->save($MemoData)) {
                            echo $memo_no_text . " This memo not created for .<br>";
                            throw new Exception();
                        } else {
                            /* ---- dist memo details prepare and save ---------- */
                            $memo_id = $this->DistMemo->getLastInsertId();
                            $memo_details_bonus['DistMemoDetail']['dist_memo_id'] = $memo_id;
                            $memo_details_bonus['DistMemoDetail']['is_bonus'] = 0;
                            $memo_details_bonus['DistMemoDetail']['vat'] = 0;
                            $memo_product_id = $memo_details_bonus['DistMemoDetail']['product_id'] = 47;
                            $memo_details_bonus['DistMemoDetail']['measurement_unit_id'] = 7;
                            $memo_details_bonus['DistMemoDetail']['price'] = 0.0;
                            $bonus_product_qty = $memo_details_bonus['DistMemoDetail']['sales_qty'] = $data[0]['qty'];

                            $invoice_bonus_qty = $bonus_product_qty;


                            $punits_pre = $this->search_array($memo_product_id, 'id', $product_list);
                            //update inventoryd
                            if ($invoice_bonus_qty < $bonus_product_qty) {
                                $n_b_sales_qty = $bonus_product_qty - $invoice_bonus_qty;
                                $update_type = 'deduct';
                                $tran_type_id = 3;
                            } else {
                                $n_b_sales_qty = $invoice_bonus_qty - $bonus_product_qty;
                                $update_type = 'add';
                                $tran_type_id = 4;
                            }

                            if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
                                $bonus_base_quantity = $n_b_sales_qty;
                                $invoice_bonus_qty = $invoice_bonus_qty;
                            } else {
                                $bonus_base_quantity = $this->unit_convert($memo_product_id, $punits_pre['sales_measurement_unit_id'], $n_b_sales_qty);
                                $invoice_bonus_qty = $this->unit_convert($memo_product_id, $punits_pre['sales_measurement_unit_id'], $invoice_bonus_qty);
                            }

                            if (!$this->dist_memo_update_current_inventory($bonus_base_quantity, $memo_product_id, $store_id, $update_type, $tran_type_id, date('Y-m-d'), $invoice_bonus_qty)) {
                                echo $memo_no_text . " This memo not created for stock update.<br>";
                                throw new Exception();
                            }
                            $this->loadmodel('DistMemoDetail');
                            if (!$this->DistMemoDetail->saveAll($memo_details_bonus)) {
                                echo $memo_no_text . " This memo not created for memo details.<br>";
                                throw new Exception();
                            }
                            if (!$this->DistOrder->updateAll(array('DistOrder.processing_status' => 2), array('DistOrder.dist_order_no' => $generate_order_no))) {
                                echo $memo_no_text . " This memo not created for oder status update.<br>";
                                throw new Exception();
                            }
                            $this->loadModel('DistOrderDeliveryScheduleOrder');
                            if (!$this->DistOrderDeliveryScheduleOrder->updateAll(array('DistOrderDeliveryScheduleOrder.processing_status' => 2), array('DistOrderDeliveryScheduleOrder.dist_order_no' => $generate_order_no))) {
                                echo $memo_no_text . " This memo not created for DistOrderDeliveryScheduleOrder update.<br>";
                                throw new Exception();
                            }
                        }
                    } else {
                        echo $order_no_text . " This order not processed<br>";
                        throw new Exception();
                    }
                }
                $update_tornedo_table_sql = "UPDATE dist_ors_tornedo_offer set is_auto_memo_created=1,refference_memo_no='$generate_memo_no' WHERE memo_no IN ($memo_no_where)";

                if (!$this->DistOrder->query($update_tornedo_table_sql)) {
                    echo 'ors tornedo table not updated for' . $memo_no_where;
                    throw new Exception();
                }

                $datasource->commit();
            } catch (Exception $e) {
                echo '<pre>';
                echo $e;
                $datasource->rollback();
                continue;
            }
        }
        $this->autoRender = false;
    }

    public function dist_order_delivery_schedule($sr_id, $order_no)
    {
        $this->loadModel('DistOrder');
        $this->loadModel('DistOrderDetail');

        $this->loadModel('DistOrderDeliverySchedule');
        $this->loadModel('DistOrderDeliveryScheduleOrder');
        $this->loadModel('DistOrderDeliveryScheduleOrderDetail');

        $this->loadModel('SalesPerson');


        $all_inserted = true;
        $relation_array = array();



        //user info
        $so_info = $this->SalesPerson->find('first', array(
            'fields' => array('SalesPerson.name', 'SalesPerson.id', 'SalesPerson.dist_sales_representative_id', 'DistSalesRepresentative.id', 'DistSalesRepresentative.name', 'DistSalesRepresentative.dist_distributor_id', 'DistDistributor.id', 'DistDistributor.name', 'DistStore.id', 'Office.id', 'Office.office_name'),
            'conditions' => array('DistSalesRepresentative.id' => $sr_id),
            'joins' => array(
                array(
                    'alias' => 'DistSalesRepresentative',
                    'table' => 'dist_sales_representatives',
                    'type' => 'INNER',
                    'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
                ),
                array(
                    'alias' => 'DistDistributor',
                    'table' => 'dist_distributors',
                    'type' => 'INNER',
                    'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
                ),
                array(
                    'alias' => 'DistStore',
                    'table' => 'dist_stores',
                    'type' => 'INNER',
                    'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
                ),
                array(
                    'alias' => 'Office',
                    'table' => 'offices',
                    'type' => 'INNER',
                    'conditions' => 'SalesPerson.office_id = Office.id'
                )
            ),
            'recursive' => -1
        ));
		echo  $this->SalesPerson->getLastQuery();
		pr($so_info);
        if ($so_info) {
            $office_id = $so_info['Office']['id'];
            $distributor_id = $so_info['DistDistributor']['id'];
            $store_id = $so_info['DistStore']['id'];
            $sales_person_id = $so_info['SalesPerson']['id'];


            $this->loadModel('Product');
            $product_measurement_units = $this->Product->find('list', array('fields' => array('id', 'sales_measurement_unit_id')));
            //pr($product_measurement_units);
            $product_category_ids = $this->Product->find('list', array('fields' => array('id', 'product_category_id')));
            //pr($product_category_ids);

            $this->loadModel('DistCurrentInventory');
            $inventory_results = $this->DistCurrentInventory->find('all', array(
                'conditions' => array(
                    'DistCurrentInventory.store_id' => $store_id,
                    'DistCurrentInventory.inventory_status_id' => 1
                ),
                'fields' => array('product_id', 'sum(qty) as qty'),
                'group' => array('product_id'),
                //'order' => array('DistCurrentInventory.product_id' => 'asc'),
                'recursive' => -1

            ));

            $inventory_qty_info = array();
            $inventory_bouns_qty_info = array();
            foreach ($inventory_results as $inventory_result) {
                $inventory_qty_info[$inventory_result['DistCurrentInventory']['product_id']] = $inventory_result[0]['qty'];
            }


            $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
            $product_list = Set::extract($products, '{n}.Product');


            $order_data_list = array();
            $process_table_status = 1;
            $process_table_status_p = 0;

            $order_results = $this->DistOrder->find('all', array(
                'conditions' => array(
                    'DistOrder.dist_order_no' => $order_no,
                    //'DistOrder.processing_status !=' => 1,
                ),
                'order' => array('DistOrder.order_time' => 'asc'),
                'recursive' => 1
            ));

            //pr($order_results);
            //exit;

            $order_products_list = array();
            foreach ($order_results as $key => $order_info) {
                $order_date = $order_info['DistOrder']['order_date'];
                $order_id = $order_info['DistOrder']['id'];
                $processing_status = $order_info['DistOrder']['processing_status'];
                $dist_order_no = $order_info['DistOrder']['dist_order_no'];
                $this->DistOrderDeliveryScheduleOrder->deleteAll(array('DistOrderDeliveryScheduleOrder.dist_order_no' => $dist_order_no));
                $this->DistOrderDeliveryScheduleOrderDetail->deleteAll(array('DistOrderDeliveryScheduleOrderDetail.dist_order_no' => $dist_order_no));


                $stock_deduction[$order_id] = 1;
                $previous_order_status = $this->DistOrder->find('first', array(
                    'conditions' => array(
                        'DistOrder.id' => $order_id,
                    ),
                    'order' => array('DistOrder.order_time' => 'asc'),
                    'recursive' => 1
                ));

                if ($previous_order_status['DistOrder']['status'] == 2 && $previous_order_status['DistOrder']['processing_status'] == 1) {
                    $previous_submitted_order['DistOrderDeliveryScheduleOrderDetail']['status'] = 1;
                    $total_order_detail_new[$dist_order_no][] = $previous_submitted_order;
                    continue;
                }

                $this->DistOrder->id = $order_id;
                if ($this->DistOrder->id) {
                    $this->DistOrder->updateAll(
                        array('DistOrder.processing_status' => 1, 'DistOrder.status' => 2, 'DistOrder.action' => 0),   //fields to update
                        array('DistOrder.id' => $order_id) //condition
                    );
                }

                foreach ($order_info['DistOrderDetail'] as $order_detail_result) {
                    $product_id = $order_detail_result['product_id'];
                    $order_details_id = $order_detail_result['id'];
                    $sales_total_qty = $order_detail_result['sales_qty'];
                    $measurement_unit_id = $order_detail_result['measurement_unit_id'];
                    $price = $order_detail_result['price'];
                    $order_id = $order_id;

                    $order_products_list[$order_details_id][$order_id] = array(
                        'order_id'              => $order_id,
                        'dist_order_no'         => $dist_order_no,
                        'product_id'            => $product_id,
                        'measurement_unit_id'   => $measurement_unit_id,
                        'processing_status'     => $processing_status,
                        'sales_qty'             => $sales_total_qty,
                        'price'                 => $price,
                        'order_date'            => $order_date,
                        'is_bonus'              => $order_detail_result['is_bonus'],
                    );
                }
            }



            //pr($order_products_list);exit;

            foreach ($order_products_list as $order_details_id => $order_info) {
                //$order_id = $order_info['DistOrder']['id'];

                //pr($order_info);exit;

                $i = 0;

                foreach ($order_info as $order_id => $order_detail_result) {
                    $product_id = $order_detail_result['product_id'];
                    $order_id = $order_detail_result['order_id'];
                    $sales_total_qty = $order_detail_result['sales_qty'];
                    $order_total_qty = $order_detail_result['sales_qty'];
                    $price = $order_detail_result['price'];

                    $product_id = $order_detail_result['product_id'];
                    $order_date = $order_detail_result['order_date'];

                    $is_bonus = $price > 0 ? 0 : 1;
                    $dist_order_no = $order_detail_result['dist_order_no'];
                    $measurement_unit_id = $order_detail_result['measurement_unit_id'];
                    //$order_id = $order_detail_result['dist_order_id'];


                    //$sales_qty=$this->unit_convert($product_id, $product_measurement_units[$product_id], $sales_total_qty);

                    $punits_pre = $this->search_array($product_id, 'id', $product_list);
                    if ($measurement_unit_id > 0) {
                        if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
                            $sales_qty = round($sales_total_qty);
                        } else {
                            $sales_qty = $this->unit_convert($product_id, $measurement_unit_id, $sales_total_qty);
                        }
                    } else {
                        if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
                            $sales_qty = round($sales_total_qty);
                        } else {
                            $sales_qty = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_total_qty);
                        }
                    }

                    $process_table_status_p = 2;
                    $order_data_list['DistOrderDeliveryScheduleOrderDetail'][$dist_order_no][$order_details_id] = array(
                        'order_id'      => $order_id,
                        'dist_order_no' => $dist_order_no,
                        'product_id'    => $product_id,
                        'measurement_unit_id' => $measurement_unit_id,
                        'order_qty'     => $order_total_qty,
                        'invoice_qty'   => $order_total_qty,
                        'price'         => $price,
                        'status'        => 1,
                        'order_date'    => $order_date,
                        'is_bonus'      => $is_bonus,
                    );
                }
            }
            //pr($order_data_list);
            //exit;

            $process_table_status = $process_table_status ? $process_table_status : $process_table_status_p;


            //insert schedule data
            $order_data_list['DistOrderDeliverySchedule'] = array(
                'office_id'             => $office_id,
                'distributor_id'        => $distributor_id,
                //'order_id'            => $order_id,
                'sr_id'                 => $sr_id,
                'sales_person_id'       => $sales_person_id,
                'process_status'        => 1,
                'status'                => $process_table_status,
                'process_date_time'     => $this->current_datetime(),
                'created_at'            => $this->current_datetime(),
                'created_by'            => $sales_person_id,
                'updated_at'            => $this->current_datetime(),
                'updated_by'            => $sales_person_id,
            );

            //pr($order_data_list);exit;

            $this->DistOrderDeliverySchedule->create();
            if ($this->DistOrderDeliverySchedule->save($order_data_list)) {
                $schedule_id = $this->DistOrderDeliverySchedule->getLastInsertID();

                foreach ($order_data_list['DistOrderDeliveryScheduleOrderDetail'] as $dist_order_no => $order_details) {
                    $schedule_order_status = 1;

                    //stock check real time
                    foreach ($order_details as $o_result);
                    $dist_stock_check = 1;

                    //insert into schedule order details table
                    foreach ($order_details as $key => $result) {
                        $product_id = $result['product_id'];
                        $dist_order_id = $result['order_id'];
                        $measurement_unit_id = $result['measurement_unit_id'];
                        $order_qty = $result['order_qty'];
                        $is_bonus = $result['is_bonus'];
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['dist_order_delivery_schedule_id'] = $schedule_id;
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['dist_order_id']  = $result['order_id'];
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['dist_order_no']  = $dist_order_no;
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['measurement_unit_id']    = $measurement_unit_id;
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['product_id']     = $product_id;
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['order_qty']      = $order_qty;
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['invoice_qty']    = $result['invoice_qty'];
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['status']         = $result['status'];
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['is_bonus']       = $result['is_bonus'];

                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['created_at']     = $this->current_datetime();
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['created_by']     = $sales_person_id;
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['updated_at']     = $this->current_datetime();
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['updated_by']     = $sales_person_id;

                        $total_order_detail[] = $order_detail;
                        $this->DistOrderDeliveryScheduleOrderDetail->saveAll($order_detail);

                        $total_order_detail_new[$dist_order_no][] = $order_detail;

                        //update dist current invetory
                        if ($dist_stock_check && $result['status'] && $stock_deduction[$result['order_id']] == 1) {
                            $punits_pre = $this->search_array($product_id, 'id', $product_list);
                            if ($measurement_unit_id > 0) {
                                if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
                                    $base_quantity = ROUND($order_qty);
                                    $invoice_qty = ROUND($result['invoice_qty']);
                                } else {
                                    $base_quantity = $this->unit_convert($product_id, $measurement_unit_id, $order_qty);
                                    $invoice_qty = $this->unit_convert($product_id, $measurement_unit_id, $result['invoice_qty']);
                                }
                            } else {
                                if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
                                    $base_quantity = ROUND($order_qty);
                                    $invoice_qty = ROUND($result['invoice_qty']);
                                } else {
                                    $base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $order_qty);
                                    $invoice_qty = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $result['invoice_qty']);
                                }
                            }

                            $update_type = 'deduct';
                            $this->dist_update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 3, $result['order_date'], $invoice_qty);


                            $this->dist_booking_update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 3, $order_date);
                        } else {
                            $schedule_order_status = 0;
                        }
                    }


                    //update dist_orders table
                    if ($schedule_order_status == 0) {
                        $this->DistOrder->id = $dist_order_id;
                        if ($this->DistOrder->id) {
                            $this->DistOrder->updateAll(
                                array(
                                    'DistOrder.processing_status' => 3,
                                    'DistOrder.status' => 1,
                                    'DistOrder.action' => 1
                                ),   //fields to update
                                array('DistOrder.id' => $dist_order_id) //condition
                            );
                        }
                    }
                    //end update dist_orders table

                    //insert into schedule order table
                    $order_into = array();
                    $order_into['DistOrderDeliveryScheduleOrder']['dist_order_delivery_schedule_id'] = $schedule_id;
                    //$order_into['DistOrderDeliveryScheduleOrder']['dist_order_id']    = $order_id;
                    $order_into['DistOrderDeliveryScheduleOrder']['dist_order_no']  = $dist_order_no;
                    $order_into['DistOrderDeliveryScheduleOrder']['processing_status'] = $schedule_order_status;
                    $this->DistOrderDeliveryScheduleOrder->saveAll($order_into);
                }

                //pr($total_order_detail);exit;



            }


            $f_order_datas = array();
            // pr($total_order_detail_new);exit;
            foreach ($total_order_detail_new as $key => $f_datas) {
                $order_status = 1;
                foreach ($f_datas as $f_data) {
                    if ($f_data['DistOrderDeliveryScheduleOrderDetail']['status'] != 1) $order_status = 0;
                }
                $f_order_datas[$key] = array(
                    //'dist_order_id' => $f_data['DistOrderDeliveryScheduleOrderDetail']['dist_order_id'],
                    'dist_order_no' => $key,
                    'status' => $order_status,
                );
            }

            $res_data = array();
            foreach ($f_order_datas as $r) {
                if ($r['status']) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }


    private function dist_stock_check($store_id, $order_id)
    {
        $this->loadModel('DistCurrentInventory');
        $this->loadModel('DistOrderDetail');
        $this->loadModel('Product');
        $this->loadModel('ProductMeasurement');

        $order_datas = $this->DistOrderDetail->find('all', array(
            'conditions' => array(
                'DistOrderDetail.dist_order_id' => $order_id
            ),
            'fields' => array('SUM(DistOrderDetail.sales_qty) as sales_qty', 'DistOrderDetail.product_id', 'DistOrderDetail.measurement_unit_id'),
            'group' => array('DistOrderDetail.product_id, DistOrderDetail.measurement_unit_id'),
            'recursive' => -1
        ));

        $order_detail = array();
        foreach ($order_datas as $data) {
            if ($data['DistOrderDetail']['measurement_unit_id']) {
                $product_info = $this->Product->find('first', array(
                    'conditions' => array(
                        'Product.id' => $data['DistOrderDetail']['product_id'],
                    ),
                    'joins' => array(
                        array(
                            'table' => 'product_measurements',
                            'alias' => 'ProductMeasurement',
                            'conditions' => 'ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id AND Product.id=ProductMeasurement.product_id'
                        )
                    ),
                    'fields' => array('ProductMeasurement.qty_in_base'),
                    'recursive' => -1
                ));
                //pr($product_info);

                $pro_measurement_info = $this->ProductMeasurement->find('first', array(
                    'conditions' => array(
                        'ProductMeasurement.product_id' => $data['DistOrderDetail']['product_id'],
                        'ProductMeasurement.measurement_unit_id' => $data['DistOrderDetail']['measurement_unit_id']
                    ),
                    'fields' => array('ProductMeasurement.qty_in_base'),
                    'recursive' => -1
                ));

                if ($pro_measurement_info && $pro_measurement_info['ProductMeasurement']['qty_in_base'] && $product_info) {
                    $prev_qty = isset($order_detail[$data['DistOrderDetail']['product_id']]) ? $order_detail[$data['DistOrderDetail']['product_id']] : 0;
                    $order_detail[$data['DistOrderDetail']['product_id']] = $prev_qty + ($data['0']['sales_qty'] * $pro_measurement_info['ProductMeasurement']['qty_in_base']) / $product_info['ProductMeasurement']['qty_in_base'];
                } elseif (empty($pro_measurement_info) && $product_info) {
                    $prev_qty = isset($order_detail[$data['DistOrderDetail']['product_id']]) ? $order_detail[$data['DistOrderDetail']['product_id']] : 0;
                    $order_detail[$data['DistOrderDetail']['product_id']] = $prev_qty + ($data['0']['sales_qty']) / $product_info['ProductMeasurement']['qty_in_base'];
                } else {
                    $prev_qty = isset($order_detail[$data['DistOrderDetail']['product_id']]) ? $order_detail[$data['DistOrderDetail']['product_id']] : 0;
                    $order_detail[$data['DistOrderDetail']['product_id']] = $prev_qty + $data['0']['sales_qty'];
                }
            } else {
                $prev_qty = isset($order_detail[$data['DistOrderDetail']['product_id']]) ? $order_detail[$data['DistOrderDetail']['product_id']] : 0;
                $order_detail[$data['DistOrderDetail']['product_id']] = $prev_qty + $data['0']['sales_qty'];
            }
        }
        foreach ($order_detail as $product_id => $qty) {
            //pr($result);
            /*$product_id = $result['DistOrderDetail']['product_id'];
            $qty = $result['0']['sales_qty'];
            */

            $current_inventory = $this->DistCurrentInventory->find('all', array(
                'conditions' => array(
                    'DistCurrentInventory.store_id' => $store_id,
                    'DistCurrentInventory.product_id' => $product_id
                ),
                'joins' => array(
                    array(
                        'table' => 'product_measurements',
                        'alias' => 'ProductMeasurement',
                        'type' => 'LEFT',
                        'conditions' => 'ProductMeasurement.product_id=Product.id AND ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id'
                    )
                ),
                'group' => array('DistCurrentInventory.store_id', 'ProductMeasurement.qty_in_base', 'DistCurrentInventory.product_id HAVING (sum(DistCurrentInventory.qty))  >= ROUND((case when ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end) *' . $qty . ',0)'),
                'fields' => array('DistCurrentInventory.store_id', 'DistCurrentInventory.product_id', 'sum(DistCurrentInventory.qty) as qty')
            ));

            if (!$current_inventory) {
                return false;
            }
        }

        return true;
    }

    public function dist_update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct', $transaction_type_id = 0, $transaction_date = '', $invoice_qty = 0)
    {

        $this->loadModel('DistCurrentInventory');

        $find_type = 'all';
        if ($update_type == 'add')
            $find_type = 'first';

        $inventory_info = $this->DistCurrentInventory->find($find_type, array(
            'conditions' => array(
                //'CurrentInventory.qty >=' => 0,
                'DistCurrentInventory.store_id' => $store_id,
                'DistCurrentInventory.inventory_status_id' => 1,
                'DistCurrentInventory.product_id' => $product_id
            ),
            'order' => array('DistCurrentInventory.qty' => 'desc'),
            'recursive' => -1
        ));

        if ($update_type == 'deduct') {
            foreach ($inventory_info as $val) {

                if ($quantity <= $val['DistCurrentInventory']['qty']) {
                    $this->DistCurrentInventory->id = $val['DistCurrentInventory']['id'];
                    $this->DistCurrentInventory->updateAll(
                        array(
                            'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' . $quantity,
                            'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty + ' . $invoice_qty,
                            'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                            'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                            'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                        ),
                        array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
                    );
                    break;
                } else {

                    if ($val['DistCurrentInventory']['id'] > 0) {

                        $this->DistCurrentInventory->id = $val['DistCurrentInventory']['id'];
                        if ($val['DistCurrentInventory']['qty'] <= 0) {
                            $this->DistCurrentInventory->updateAll(
                                array(
                                    'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' . $quantity,
                                    'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty + ' . $invoice_qty,
                                    'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                                    'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                                    'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                                ),
                                array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
                            );
                            $quantity = 0;
                            break;
                        } else {
                            $quantity = $quantity - $val['DistCurrentInventory']['qty'];
                            $this->DistCurrentInventory->updateAll(
                                array(
                                    'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' . $val['DistCurrentInventory']['qty'],
                                    'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty + ' . $invoice_qty,
                                    'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                                    'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                                    'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                                ),
                                array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
                            );
                        }
                    }
                }
            }
        } else {

            if (!empty($inventory_info)) {

                $this->DistCurrentInventory->updateAll(
                    array(
                        'DistCurrentInventory.qty' => 'DistCurrentInventory.qty + ' . $quantity,
                        //'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
                        'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                        'DistCurrentInventory.store_id' => $store_id,
                        'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
                    ),
                    array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                );

                //for invoice qty
                if ($invoice_qty <= $inventory_info['DistCurrentInventory']['invoice_qty']) {
                    $this->DistCurrentInventory->updateAll(
                        array(
                            //'DistCurrentInventory.qty' => 'DistCurrentInventory.qty + ' . $quantity,
                            'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
                            'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                            'DistCurrentInventory.store_id' => $store_id,
                            'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
                        ),
                        array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                    );
                } else {
                    $this->DistCurrentInventory->updateAll(
                        array(
                            'DistCurrentInventory.invoice_qty' => 0,
                            'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                            'DistCurrentInventory.store_id' => $store_id,
                            'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
                        ),
                        array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                    );
                }
            }
        }


        return true;
    }
    public function dist_memo_update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct', $transaction_type_id = 0, $transaction_date = '', $invoice_qty = 0)
    {

        $this->loadModel('DistCurrentInventory');

        $find_type = 'all';
        if ($update_type == 'add')
            $find_type = 'first';

        $inventory_info = $this->DistCurrentInventory->find($find_type, array(
            'conditions' => array(
                //'CurrentInventory.qty >=' => 0,
                'DistCurrentInventory.store_id' => $store_id,
                'DistCurrentInventory.inventory_status_id' => 1,
                'DistCurrentInventory.product_id' => $product_id
            ),
            'order' => array('DistCurrentInventory.qty' => 'desc'),
            'recursive' => -1
        ));



        if ($update_type == 'deduct') {
            foreach ($inventory_info as $val) {
                if ($quantity <= 0) {
                    break;
                }

                if ($quantity <= $val['DistCurrentInventory']['qty']) {
                    $this->DistCurrentInventory->id = $val['DistCurrentInventory']['id'];
                    $this->DistCurrentInventory->updateAll(
                        array(
                            'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' . $quantity,
                            'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
                            'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                            'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                            'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                        ),
                        array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
                    );
                    break;
                } else {

                    if ($val['DistCurrentInventory']['id'] > 0) {

                        $this->DistCurrentInventory->id = $val['DistCurrentInventory']['id'];
                        if ($val['DistCurrentInventory']['qty'] <= 0) {
                            $this->DistCurrentInventory->updateAll(
                                array(
                                    'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' . $quantity,
                                    'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
                                    'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                                    'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                                    'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                                ),
                                array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
                            );
                            $quantity = 0;
                            break;
                        } else {
                            $quantity = $quantity - $val['DistCurrentInventory']['qty'];
                            $this->DistCurrentInventory->updateAll(
                                array(
                                    'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' . $val['DistCurrentInventory']['qty'],
                                    'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
                                    'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                                    'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                                    'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                                ),
                                array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
                            );
                        }
                    }
                }
            }
        } else {

            if (!empty($inventory_info)) {

                $this->DistCurrentInventory->updateAll(
                    array(
                        'DistCurrentInventory.qty' => 'DistCurrentInventory.qty + ' . $quantity,
                        //'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
                        'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                        'DistCurrentInventory.store_id' => $store_id,
                        'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
                    ),
                    array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                );

                //for invoice qty
                if ($invoice_qty <= $inventory_info['DistCurrentInventory']['invoice_qty']) {
                    $this->DistCurrentInventory->updateAll(
                        array(
                            //'DistCurrentInventory.qty' => 'DistCurrentInventory.qty + ' . $quantity,
                            'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
                            'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                            'DistCurrentInventory.store_id' => $store_id,
                            'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
                        ),
                        array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                    );
                } else {
                    $this->DistCurrentInventory->updateAll(
                        array(
                            'DistCurrentInventory.invoice_qty' => 0,
                            'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                            'DistCurrentInventory.store_id' => $store_id,
                            'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
                        ),
                        array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                    );
                }
            }
        }

        //exit;
        return true;
    }
    public function dist_booking_update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct', $transaction_type_id = 0, $transaction_date = '')
    {

        $this->loadModel('DistCurrentInventory');

        $find_type = 'first';

        $inventory_info = $this->DistCurrentInventory->find($find_type, array(
            'conditions' => array(
                'DistCurrentInventory.store_id' => $store_id,
                'DistCurrentInventory.inventory_status_id' => 1,
                'DistCurrentInventory.product_id' => $product_id
            ),
            'order' => array('DistCurrentInventory.qty' => 'desc'),
            'recursive' => -1
        ));

        //pr($inventory_info);

        if ($inventory_info) {
            if ($update_type == 'deduct') {

                /*$update_array = array('DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'");
                
                if($inventory_info['DistCurrentInventory']['booking_qty'] < $quantity){
                    $update_array['DistCurrentInventory.booking_qty']=0;
                }else{
                    $update_array['DistCurrentInventory.booking_qty']='DistCurrentInventory.booking_qty - ' . $quantity;
                }
                
                if($inventory_info['DistCurrentInventory']['bonus_booking_qty'] < $booking_bonus_qty){
                    $update_array['DistCurrentInventory.bonus_booking_qty']=0;
                }else{
                    $update_array['DistCurrentInventory.bonus_booking_qty']='DistCurrentInventory.bonus_booking_qty - ' . $booking_bonus_qty;
                }
                
                $this->DistCurrentInventory->id = $inventory_info['DistCurrentInventory']['id'];
                
                $this->DistCurrentInventory->updateAll(
                        $update_array, 
                        array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                );*/


                $update_array = array();
                $update_array['DistCurrentInventory']['id'] = $inventory_info['DistCurrentInventory']['id'];
                if ($inventory_info['DistCurrentInventory']['booking_qty'] < $quantity) {
                    $update_array['DistCurrentInventory']['booking_qty'] = 0;
                } else {
                    $update_array['DistCurrentInventory']['booking_qty'] = $inventory_info['DistCurrentInventory']['booking_qty'] - $quantity;
                }

                $update_array['DistCurrentInventory']['updated_at'] = $this->current_datetime();

                //pr($update_array);


                $this->DistCurrentInventory->save($update_array);




                /*$this->DistCurrentInventory->updateAll(
                        array(
                            'DistCurrentInventory.booking_qty' => 'DistCurrentInventory.booking_qty - ' . $quantity,
                            'DistCurrentInventory.bonus_booking_qty' => 'DistCurrentInventory.bonus_booking_qty - ' . $booking_bonus_qty,
                            'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                        ), 
                        array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                );*/
            } else {


                $this->DistCurrentInventory->updateAll(
                    array(
                        'DistCurrentInventory.booking_qty' => 'DistCurrentInventory.booking_qty + ' . $quantity,

                        'DistCurrentInventory.store_id' => $store_id,
                    ),
                    array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                );
            }
        }
        return true;
    }



    public function update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct', $transaction_type_id = 0, $transaction_date = '', $booking_bonus_qty)
    {

        $this->loadModel('DistCurrentInventory');

        $find_type = 'all';
        if ($update_type == 'add')
            $find_type = 'first';

        $inventory_info = $this->DistCurrentInventory->find($find_type, array(
            'conditions' => array(
                //'CurrentInventory.qty >=' => 0,
                'DistCurrentInventory.store_id' => $store_id,
                'DistCurrentInventory.inventory_status_id' => 1,
                'DistCurrentInventory.product_id' => $product_id
            ),
            'order' => array('DistCurrentInventory.qty' => 'desc'),
            'recursive' => -1
        ));

        /* pr($inventory_info);
       pr($quantity);
       pr($product_id);
       pr($store_id);
       pr($update_type);
       pr($transaction_type_id);
       pr($transaction_date);
       pr($booking_bonus_qty);*/

        if ($update_type == 'deduct') {
            foreach ($inventory_info as $val) {
                if ($quantity <= 0) {
                    break;
                }
                if ($quantity <= $val['DistCurrentInventory']['booking_qty']) {
                    $this->DistCurrentInventory->id = $val['DistCurrentInventory']['id'];
                    $this->DistCurrentInventory->updateAll(
                        array(
                            //'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' . $quantity,
                            'DistCurrentInventory.booking_qty' => 'DistCurrentInventory.booking_qty - ' . $quantity,
                            'DistCurrentInventory.bonus_booking_qty' => 'DistCurrentInventory.bonus_booking_qty - ' . $booking_bonus_qty,
                            'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                            'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                            'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                        ),
                        array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
                    );
                    break;
                } else {


                    if ($val['DistCurrentInventory']['id'] > 0) {

                        $this->DistCurrentInventory->id = $val['DistCurrentInventory']['id'];
                        if ($val['DistCurrentInventory']['qty'] <= 0) {

                            $this->DistCurrentInventory->updateAll(
                                array(
                                    //'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' .$quantity ,
                                    'DistCurrentInventory.booking_qty' => 'DistCurrentInventory.booking_qty - ' . $quantity,
                                    'DistCurrentInventory.bonus_booking_qty' => 'DistCurrentInventory.bonus_booking_qty - ' . $booking_bonus_qty,
                                    'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                                    'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                                    'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                                ),
                                array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
                            );
                            $quantity = 0;
                            break;
                        } else {
                            //$quantity = $quantity - $val['DistCurrentInventory']['qty'];
                            $quantity = $quantity - $val['DistCurrentInventory']['booking_qty'];
                            $booking_bonus_qty = $booking_bonus_qty - $val['DistCurrentInventory']['bonus_booking_qty'];
                            $this->DistCurrentInventory->updateAll(
                                array(
                                    'DistCurrentInventory.booking_qty' => 'DistCurrentInventory.booking_qty - ' . $val['DistCurrentInventory']['booking_qty'],
                                    'DistCurrentInventory.bonus_booking_qty' => 'DistCurrentInventory.bonus_booking_qty - ' . $val['DistCurrentInventory']['bonus_booking_qty'],
                                    'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                                    'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                                    'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                                ),
                                array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
                            );
                        }
                    }
                }
            }
        } else {
            /* $this->CurrentInventory->updateAll(array('CurrentInventory.qty' => 'CurrentInventory.qty + '.$inventory_info['CurrentInventory']['qty']),array('CurrentInventory.id' => $inventory_info['CurrentInventory']['id'])); */
            if (!empty($inventory_info)) {

                $this->DistCurrentInventory->updateAll(
                    array(
                        'DistCurrentInventory.booking_qty' => 'DistCurrentInventory.booking_qty + ' . $quantity,
                        'DistCurrentInventory.bonus_booking_qty' => 'DistCurrentInventory.bonus_booking_qty + ' . $booking_bonus_qty,
                        'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                        'DistCurrentInventory.store_id' => $store_id,
                        'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
                    ),
                    array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                );
            }
        }
        /*$inventory_info = $this->DistCurrentInventory->find($find_type, array(
            'conditions' => array(
                //'CurrentInventory.qty >=' => 0,
                'DistCurrentInventory.store_id' => $store_id,
                'DistCurrentInventory.inventory_status_id' => 1,
                'DistCurrentInventory.product_id' => $product_id
            ),
            'order' => array('DistCurrentInventory.qty' => 'desc'),
            'recursive' => -1
        ));*/
        //pr($inventory_info);//die();
        return true;
    }

    private function get_territory_thana_db_info($office_id, $market_id, $route_id, $order_date)
    {
        $this->LoadModel('DistRouteMapping');
        /* ------------- get distributor id from route maping history------------- */
        $route_info = $this->DistRouteMapping->find('first', array(
            'conditions' => array(
                'DistRouteMapping.dist_route_id' => $route_id,
                'DistRouteMapping.office_id' => $office_id
            ),
            'recursive' => -1
        ));
        $distributor_id = '';
        $sr_id = '';
        if ($route_info)
            $distributor_id = $route_info['DistRouteMapping']['dist_distributor_id'];
        /* -------------- get sr id by distributor id and route id --------*/
        if ($distributor_id) {

            $this->LoadModel('DistSrRouteMapping');
            $sr_route_info = $this->DistSrRouteMapping->find('first', array(
                'conditions' => array(
                    'DistSrRouteMapping.dist_route_id' => $route_id,
                    'DistSrRouteMapping.office_id' => $office_id,
                    'DistSrRouteMapping.dist_distributor_id' => $distributor_id,
                ),
                'recursive' => -1
            ));
            if ($sr_route_info) {
                $sr_id = $sr_route_info['DistSrRouteMapping']['dist_sr_id'];
            }
        }
        $info = $this->DistMarket->find('first', array(
            'conditions' => array('DistMarket.id' => $market_id),
            'recursive' => -1
        ));

        $territory_id = $info['DistMarket']['territory_id'];
        $thana_id = $info['DistMarket']['thana_id'];

        $this->loadModel('DistTsoMappingHistory');

        $dist_ids = array();
        if ($distributor_id) {
            $order_date = date("Y-m-d H:i:s", strtotime($order_date));
            $qry = "select distinct dist_tso_id from dist_tso_mapping_histories
                where office_id=$office_id and is_change=1 and dist_distributor_id=$distributor_id and 
                '" . $order_date . "' between effective_date and 
                case 
                when end_date is not null then 
                    end_date
                else 
                getdate()
                end";

            $dist_data = $this->DistTsoMappingHistory->query($qry);
            //pr($dist_data);

            foreach ($dist_data as $k => $v) {
                $dist_ids[] = $v[0]['dist_tso_id'];
            }
        }
        $tso_id = "";
        if ($dist_ids) {
            $tso_id = $dist_ids[0];
        }

        $ae_ids = array();
        if ($tso_id) {
            $qry2 = "select distinct dist_area_executive_id from dist_tso_histories where tso_id=$tso_id and (is_added=1 or is_transfer=1) and 
            '" . $order_date . "' between effective_date and 
            case 
            when effective_end_date is not null then 
                effective_end_date
            else 
            getdate()
            end";

            $ae_data = $this->DistTsoMappingHistory->query($qry2);
            //pr($ae_data);die();

            foreach ($ae_data as $k => $v) {
                $ae_ids[] = $v[0]['dist_area_executive_id'];
            }
        }
        $ae_id = "";
        if ($ae_ids) {
            $ae_id = $ae_ids[0];
        }
        $data = array(
            'territory_id' => $territory_id,
            'thana_id' => $thana_id,
            'ae_id' => $ae_id,
            'tso_id' => $tso_id,
            'db_id' => $distributor_id,
            'sr_id' => $sr_id
        );
        return $data;
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
}
