<?php

App::uses('AppController', 'Controller');

/**
 * Challans Controller
 *
 * @property Challan $ReturnChallan
 * @property PaginatorComponent $Paginator
 */

class ReturnChallansToAsoController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Filter.Filter');
    public $uses = array('ReturnChallan', 'Office', 'Store', 'Product', 'CurrentInventory', 'ReturnChallanDetail');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index()
    {

        $this->set('page_title', 'Return Challan List');



        $offices = array();

        $sote_id = 0;

        $region_office_id = 0;

        $office_parent_id = $this->UserAuth->getOfficeParentId();

        $this->set(compact('office_parent_id'));

        $office_conditions = array('Office.office_type_id' => 2);

        if ($office_parent_id == 0) {
            $office_conditions = array('office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
            $offices = $this->Office->find('list', array(
                'conditions' => $office_conditions,
                'order' => array('office_name' => 'asc')
            ));

            $office_id = 0;

            $store_con = array(
                'ReturnChallan.inventory_status_id' => 1,
                'AND' => array(
                    array(
                        'OR' => array(
                            array('ReturnChallan.transaction_type_id' => 9), //SO TO ASO (RETURN)
                            array('ReturnChallan.transaction_type_id' => 19), //SO TO ASO (RETURN Received)
                        )
                    )
                )
            );
        } elseif ($office_parent_id == 14) {
            $region_office_id = $this->UserAuth->getOfficeId();
            $region_offices = $this->Office->find('list', array(
                'conditions' => array('Office.office_type_id' => 3, 'Office.id' => $region_office_id),
                'order' => array('office_name' => 'asc')
            ));

            $office_conditions = array('Office.parent_office_id' => $region_office_id);

            $office_id = 0;

            $offices = $this->Office->find('list', array(
                'conditions' => array(
                    'office_type_id'     => 2,
                    'parent_office_id'     => $region_office_id,

                    "NOT" => array("id" => array(30, 31, 37))
                ),
                'order' => array('office_name' => 'asc')
            ));

            $office_ids = array_keys($offices);

            $office_id = $office_ids;

            $store_con = array(
                'ReturnChallan.inventory_status_id' => 1,
                'AND' => array(
                    array(
                        'OR' => array(
                            array('ReturnChallan.transaction_type_id' => 9), //SO TO ASO (RETURN)
                            array('ReturnChallan.transaction_type_id' => 19), //SO TO ASO (RETURN Received)
                        )
                    ),
                    array(
                        'OR' => array(
                            array('SenderStore.office_id' => $office_id),
                            array('ReceiverStore.office_id' => $office_id)
                        )
                    )
                )
            );
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
            $office_id = $this->UserAuth->getOfficeId();

            $offices = $this->Office->find('list', array(
                'conditions' => array(
                    'id'     => $office_id,
                ),
                'order' => array('office_name' => 'asc')
            ));


            $sote_id = $this->UserAuth->getStoreId();

            $store_con = array(
                'ReturnChallan.inventory_status_id' => 1,
                'AND' => array(
                    array(
                        'OR' => array(
                            array('ReturnChallan.transaction_type_id' => 9), //SO TO ASO (RETURN)
                            array('ReturnChallan.transaction_type_id' => 19), //SO TO ASO (RETURN Received)
                        )
                    ),
                    array(
                        'OR' => array(
                            array('ReturnChallan.sender_store_id' => $sote_id),
                            array('ReturnChallan.receiver_store_id' => $sote_id)
                        )
                    )
                )
            );
        }


        $this->set('offices', $offices);



        $this->paginate = array(
            'conditions' => $store_con,
            'recursive' => 0,
            'order' => array('ReturnChallan.id' => 'desc')
        );

        $this->set('returnChallans', $this->paginate());
        $this->loadModel('InventoryStatus');
        $inventoryStatus = $this->InventoryStatus->find('list', array(
            'conditions' => array('id !=' => 2)
        ));
        $this->set('inventoryStatus', $inventoryStatus);
        $this->loadModel('SalesPerson');
        $sales_person_array = $this->SalesPerson->query('select stores.id,sales_people.name from stores INNER JOIN sales_people on stores.territory_id = sales_people.territory_id');

        $sales_person_extract_array = Set::extract($sales_person_array, '{n}.0');
        foreach ($sales_person_extract_array as $value) {
            $sales_person[$value['id']] = $value['name'];
        }

        $this->set('sales_person', $sales_person);
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
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        $this->set('page_title', 'Return Challan Details');
        if (!$this->ReturnChallan->exists($id)) {
            throw new NotFoundException(__('Invalid challan'));
        }

        $options = array(
            'conditions' => array(
                'ReturnChallan.' . $this->ReturnChallan->primaryKey => $id
            ),
            'recursive' => 0
        );
        $returnChallan = $this->ReturnChallan->find('first', $options);
        $returnChallanDetail = $this->ReturnChallanDetail->find(
            'all',
            array(
                'conditions' => array('ReturnChallanDetail.challan_id' => $returnChallan['ReturnChallan']['id']),
                'fields' => 'ReturnChallanDetail.*,Product.product_code,Product.name,MeasurementUnit.name',
                'order' => array('Product.order' => 'asc'),
                'recursive' => 0
            )
        );

        if ($this->request->is('post')) {

            /*------------ stock checking for challan received : start --------------------------- */
            $return_stock_check = $this->ReturnChallanDetail->find(
                'all',
                array(
                    'conditions' => array(
                        'ReturnChallanDetail.challan_id' => $returnChallan['ReturnChallan']['id'],
                    ),
                    'joins' => array(
                        array(
                            'table' => 'product_measurements',
                            'alias' => 'ProductMeasurement',
                            'type' => 'LEFT',
                            'conditions' => 'ProductMeasurement.product_id=(case when ReturnChallanDetail.virtual_product_id is null or ReturnChallanDetail.virtual_product_id=0 then ReturnChallanDetail.product_id else ReturnChallanDetail.virtual_product_id end) AND ProductMeasurement.measurement_unit_id=ReturnChallanDetail.measurement_unit_id'
                        ),
                        array(
                            'table' => '(select ci.store_id,ci.product_id,sum(ci.qty) as qty from current_inventories ci group by ci.store_id,ci.product_id)',
                            'alias' => 'CurrentInventory',
                            'conditions' => 'CurrentInventory.product_id=(case when ReturnChallanDetail.virtual_product_id is null or ReturnChallanDetail.virtual_product_id=0 then ReturnChallanDetail.product_id else ReturnChallanDetail.virtual_product_id end) and ReturnChallan.sender_store_id=CurrentInventory.store_id'
                        ),
                    ),
                    'fields' => array(
                        'CurrentInventory.product_id',
                        'CurrentInventory.store_id',
                        'Product.name',
                        'SUM(ROUND(ReturnChallanDetail.challan_qty * (CASE WHEN ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end),0)) as base_challan_qty',
                        'SUM(CurrentInventory.qty) as stock_qty'
                    ),
                    'group' => array(
                        'CurrentInventory.product_id',
                        'Product.name',
                        'CurrentInventory.store_id Having (SUM(CurrentInventory.qty)) <  (SUM(ReturnChallanDetail.challan_qty * (CASE WHEN ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end)))'
                    ),

                    'recursive' => 0
                )
            );
            // echo $this->ReturnChallanDetail->getLastQuery();exit;
            if ($return_stock_check) {
                $this->Session->setFlash(__('Stock Not Available For Product'), 'flash/error');
                $this->redirect(array('action' => 'view/' . $id));
            }
            /*------------ stock checking for challan received : END ----------------------------- */

            if (!$this->request->data['RetureturnChallanDetailrnChallan']['received_date']) {
                $this->Session->setFlash(__('Return Challan Received Date is required.'), 'flash/error');
                $this->redirect(array('action' => 'view/' . $id));
            }
            if ($returnChallan['ReturnChallan']['status'] > 1) {
                $this->Session->setFlash(__('Return challan has already been received.'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            }
            //echo date('Y-m-d', strtotime($this->request->data['RetureturnChallanDetailrnChallan']['received_date']));
            //pr($this->request->data);die();


            // update challan status 
            $returnChalan['id'] = $id;
            $returnChalan['status'] = 2;
            //$returnChalan['received_date'] = date('Y-m-d',strtotime($this->request->data['ReturnChallan']['received_date']));
            $returnChalan['received_date'] = date('Y-m-d', strtotime($this->request->data['RetureturnChallanDetailrnChallan']['received_date']));
            $returnChalan['updated_at'] = $this->current_datetime();
            $returnChalan['updated_by'] = $this->UserAuth->getUserId();
            $returnChalan['transaction_type_id'] = 19; // SO TO ASO (Return Received)


            $challan_update_set_sql = "
            received_date = '" . $returnChalan['received_date'] . "',
            transaction_type_id = '" . $returnChalan['transaction_type_id'] . "',
            updated_at = '" . $returnChalan['updated_at'] . "',
            updated_by = '" . $returnChalan['updated_by'] . "',
            ";

            $challan_update_conditions = "id=$id";

            $challan_update_set_sql .= " status=2";
            $challan_update_conditions .= " AND status=1";

            $prev_challan_status = $this->ReturnChallan->query("SELECT * FROM return_challans WHERE $challan_update_conditions");
            $challan_update = 0;
            $datasource = $this->ReturnChallan->getDataSource();
            try {
                $datasource->begin();
                if ($prev_challan_status) {
                    if (!$challan_update = $this->ReturnChallan->query("UPDATE return_challans set $challan_update_set_sql WHERE $challan_update_conditions")) {
                        throw new Exception();
                    }
                }
                if ($challan_update) {
                    if (!$this->ReturnChallan->save($returnChalan)) {
                        throw new Exception();
                    }
                    $data_array = array();
                    $insert_data_array = array();
                    $update_data_array = array();
                    $update_second_data_array = array();
                    foreach ($this->request->data['product_id'] as $key => $val) {

                        $virtual_product_id = $this->request->data['virtual_product_id'][$key];

                        if ($virtual_product_id > 0) {
                            $val = $virtual_product_id;
                        }

                        $receive_quantity = $this->request->data['quantity'][$key];
                        $quantity = $this->unit_convert($val, $this->request->data['measurement_unit_id'][$key], $receive_quantity);

                        // ------------ stock update --------------------			
                        $inventory_info = $this->CurrentInventory->find('first', array(
                            'conditions' => array(
                                'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
                                'CurrentInventory.inventory_status_id' => $this->request->data['inventory_status_id'][$key],
                                'CurrentInventory.product_id' => $val,
                                'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
                                'CurrentInventory.expire_date' => $this->request->data['expire_date'][$key]
                            ),
                            'recursive' => -1
                        ));

                        if (!empty($inventory_info)) {
                            $update_data['id'] = $inventory_info['CurrentInventory']['id'];
                            $update_data['qty'] = $inventory_info['CurrentInventory']['qty'] + $quantity;
                            $update_data['transaction_type_id'] = 19; //SO TO ASO (Return Received)
                            $update_data['updated_at'] = $this->current_datetime();
                            $update_data['transaction_date'] = date('Y-m-d', strtotime($this->request->data['RetureturnChallanDetailrnChallan']['received_date']));
                            $update_data_array[] = $update_data;
                            // Update inventory data
                            if (!$this->CurrentInventory->saveAll($update_data)) {
                                throw new Exception();
                            }
                            unset($update_data);
                        } else {

                            /* Again update without batch and expire date */
                            $inventory_info_row = $this->CurrentInventory->find('first', array(
                                'conditions' => array(
                                    'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
                                    'CurrentInventory.inventory_status_id' => $this->request->data['inventory_status_id'][$key],
                                    'CurrentInventory.product_id' => $val
                                ),
                                'recursive' => -1
                            ));

                            if (!empty($inventory_info_row)) {

                                $update_data['id'] = $inventory_info_row['CurrentInventory']['id'];
                                $update_data['qty'] = $inventory_info_row['CurrentInventory']['qty'] + $quantity;
                                $update_data['updated_at'] = $this->current_datetime();
                                $update_data['transaction_type_id'] = 19; //SO TO ASO (Return Received)
                                $update_data['transaction_date'] = date('Y-m-d', strtotime($this->request->data['RetureturnChallanDetailrnChallan']['received_date']));
                                $update_second_data_array[] = $update_data;

                                // Update inventory data
                                if (!$this->CurrentInventory->saveAll($update_data)) {
                                    throw new Exception();
                                }
                                unset($update_data);
                            } else {
                                $insert_data['store_id'] = $this->UserAuth->getStoreId();
                                $insert_data['inventory_status_id'] = $this->request->data['inventory_status_id'][$key];
                                $insert_data['product_id'] = $val;
                                $insert_data['batch_number'] = $this->request->data['batch_no'][$key];
                                $insert_data['expire_date'] = $this->request->data['expire_date'][$key];
                                $insert_data['qty'] = $quantity;
                                $insert_data['updated_at'] = $this->current_datetime();
                                $insert_data['transaction_type_id'] = 19; //SO TO ASO (Return Received)
                                $insert_data['transaction_date'] = date('Y-m-d', strtotime($this->request->data['RetureturnChallanDetailrnChallan']['received_date']));
                                $insert_data_array[] = $insert_data;
                            }
                        }

                        /* update so inventory start */

                        $inventory_info_rows = $this->CurrentInventory->find('all', array(
                            'conditions' => array(
                                'CurrentInventory.store_id' => $this->request->data['SenderStore_id'][$key],
                                'CurrentInventory.inventory_status_id' => 1, //All time product deduct from sound product
                                'CurrentInventory.product_id' => $val
                            ),
                            'recursive' => -1
                        ));


                        if (!empty($inventory_info_rows)) {


                            $total_base_unit = $quantity;
                            for ($batch_count = 0; $batch_count < count($inventory_info_rows); $batch_count++) {

                                if ($inventory_info_rows[$batch_count]['CurrentInventory']['qty'] > $total_base_unit) {
                                    $updated_qty = [];
                                    $updated_current_inventory['id'] = $inventory_info_rows[$batch_count]['CurrentInventory']['id'];
                                    $updated_current_inventory['qty'] = $inventory_info_rows[$batch_count]['CurrentInventory']['qty'] - $total_base_unit;
                                    $updated_current_inventory['updated_at'] = $this->current_datetime();

                                    $updated_current_inventory['transaction_type_id'] = 19; //SO TO ASO (Return Received)
                                    $updated_current_inventory['transaction_date'] = date('Y-m-d', strtotime($this->request->data['RetureturnChallanDetailrnChallan']['received_date']));
                                    if (!$this->CurrentInventory->save($updated_current_inventory)) {
                                        throw new Exception();
                                    }
                                    break;
                                } else {
                                    $updated_qty[$batch_count] = $inventory_info_rows[$batch_count]['CurrentInventory']['qty'] - $inventory_info_rows[$batch_count]['CurrentInventory']['qty'];

                                    $updated_current_inventory['id'] = $inventory_info_rows[$batch_count]['CurrentInventory']['id'];
                                    $updated_current_inventory['qty'] = $updated_qty[$batch_count];
                                    $updated_current_inventory['updated_at'] = $this->current_datetime();
                                    $updated_current_inventory['transaction_type_id'] = 19; //SO TO ASO (Return Received)
                                    $updated_current_inventory['transaction_date'] = date('Y-m-d', strtotime($this->request->data['RetureturnChallanDetailrnChallan']['received_date']));
                                    if (!$this->CurrentInventory->save($updated_current_inventory)) {
                                        throw new Exception();
                                    }
                                    // remaining amount 
                                    $total_base_unit = $total_base_unit - $inventory_info_rows[$batch_count]['CurrentInventory']['qty'];
                                }
                            }
                        }

                        /* update so inventory end */
                        //----------------- End Stock update ---------------------


                        $data['ReturnChallanDetail']['id'] = $this->request->data['id'][$key];
                        $data['ReturnChallanDetail']['received_qty'] = $receive_quantity;
                        $data_array[] = $data;
                    }
                    // insert inventory data
                    if (!empty($insert_data_array) && !$this->CurrentInventory->saveAll($insert_data_array)) {
                        throw new Exception();
                    }
                    // update received quantity
                    if (!$this->ReturnChallanDetail->saveAll($data_array)) {
                        throw new Exception();
                    }
                    $datasource->commit();
                    $this->Session->setFlash(__('Return Challan has been received.'), 'flash/success');
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('Return Challan Already has been received.'), 'flash/success');
                    $this->redirect(array('action' => 'index'));
                }
            } catch (Exception $e) {
                $datasource->rollback();
                $this->Session->setFlash(__('SomeThing Went Wrong! Please Try Again.'), 'flash/error');
                $this->redirect(array('action' => 'view/' . $id));
            }
        }


        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $this->set(compact('returnChallan', 'returnChallanDetail', 'office_parent_id'));
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add()
    {
        $this->set('page_title', 'Return Challan Add');

        if ($this->request->is('post')) {

            if (empty($this->request->data['product_id'])) {
                $this->Session->setFlash(__('Return Challan to ASO not created.'), 'flash/error');
                $this->redirect(array('action' => 'index'));
            } else {

                $this->request->data['ReturnChallan']['transaction_type_id'] = 9; //SO TO ASO (return)
                $this->request->data['ReturnChallan']['inventory_status_id'] = 1;

                $this->request->data['ReturnChallan']['challan_date'] = $this->current_date();
                $this->request->data['ReturnChallan']['sender_store_id'] = $this->UserAuth->getStoreId();
                //$this->request->data['ReturnChallan']['sender_store_id'] =23; //mohakhali store
                $this->request->data['ReturnChallan']['status'] = 1;
                $this->request->data['ReturnChallan']['created_at'] = $this->current_datetime();
                $this->request->data['ReturnChallan']['created_by'] = $this->UserAuth->getUserId();
                $this->request->data['ReturnChallan']['updated_at'] = $this->current_datetime();
                $this->request->data['ReturnChallan']['updated_by'] = 0;
                $this->ReturnChallan->create();
                if ($this->ReturnChallan->save($this->request->data)) {

                    $udata['id'] = $this->ReturnChallan->id;
                    $udata['challan_no'] = 'RCHASO' . (10000 + $this->ReturnChallan->id);
                    $this->ReturnChallan->save($udata);
                    if (!empty($this->request->data['product_id'])) {
                        $data_array = array();
                        $update_data_array = array();
                        $so_update_data_array = array();
                        $insert_data_array = array();
                        foreach ($this->request->data['product_id'] as $key => $val) {
                            $data['ReturnChallanDetail']['challan_id'] = $this->ReturnChallan->id;
                            $data['ReturnChallanDetail']['product_id'] = $val;
                            $data['ReturnChallanDetail']['measurement_unit_id'] = $this->request->data['measurement_unit'][$key];
                            $data['ReturnChallanDetail']['challan_qty'] = $this->request->data['quantity'][$key];
                            $data['ReturnChallanDetail']['batch_no'] = $this->request->data['batch_no'][$key];
                            $data['ReturnChallanDetail']['expire_date'] = ($this->request->data['expire_date'][$key] != '' ? date('Y-m-d', strtotime($this->request->data['expire_date'][$key])) : Null);
                            $data['ReturnChallanDetail']['inventory_status_id'] = 1;
                            $data['ReturnChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
                            $data_array[] = $data;

                            // ------------ stock update --------------------
                            $inventory_info = $this->CurrentInventory->find('first', array(
                                'conditions' => array(
                                    'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
                                    //'CurrentInventory.store_id' =>23, //mohakhali store
                                    'CurrentInventory.inventory_status_id' => 1,
                                    'CurrentInventory.product_id' => $val,
                                    'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
                                    'CurrentInventory.expire_date' => ($this->request->data['expire_date'][$key] != '' ? date('Y-m-d', strtotime($this->request->data['expire_date'][$key])) : Null)
                                ),
                                'recursive' => -1
                            ));


                            $deduct_quantity = $this->unit_convert($val, $this->request->data['measurement_unit'][$key], $this->request->data['quantity'][$key]);

                            $update_data['id'] = $inventory_info['CurrentInventory']['id'];
                            $update_data['qty'] = $inventory_info['CurrentInventory']['qty'] - $deduct_quantity;
                            $update_data['transaction_type_id'] = 9; // SO to ASO (Return)	
                            $insert_data['transaction_date'] = $this->current_date();
                            $update_data_array[] = $update_data;
                        }
                        // insert challan data
                        $this->ReturnChallanDetail->saveAll($data_array);
                        // Update inventory data
                        $this->CurrentInventory->saveAll($update_data_array);
                    }

                    $this->Session->setFlash(__('Product Returned has been created.'), 'flash/success');
                    $this->redirect(array('action' => 'index'));
                }
            }
        }

        $receiverStore = $this->Store->find('list', array(
            'conditions' => array('store_type_id' => 2, 'office_id' => 15), //$this->UserAuth->getOfficeParentId()
            'order' => array('name' => 'asc')
        ));

        $products = $this->Product->find('list', array('order' => array('name' => 'asc')));
        $this->set(compact('receiverStore', 'products'));
    }

    /**
     * admin_delete method
     *
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @param string $id
     * @return void
     */
    public function admin_delete($id = null)
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->ReturnChallan->id = $id;
        if (!$this->ReturnChallan->exists()) {
            throw new NotFoundException(__('Invalid challan'));
        }
        if ($this->ReturnChallan->delete()) {
            $this->flash(__('Challan deleted'), array('action' => 'index'));
        }
        $this->flash(__('Challan was not deleted'), array('action' => 'index'));
        $this->redirect(array('action' => 'index'));
    }
}
