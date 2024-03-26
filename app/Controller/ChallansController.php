<?php

App::uses('AppController', 'Controller');

/**
 * Challans Controller
 *
 * @property Challan $Challan
 * @property PaginatorComponent $Paginator
 */
class ChallansController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Filter.Filter');
    public $uses = array('Challan', 'ChallanDetail', 'Store', 'ProductType', 'CurrentInventory', 'Product');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index(){
        $this->set('page_title', 'Challan List');
        $this->Challan->recursive = 0;
        $this->paginate = array(
            'conditions' => array(
                'Challan.inventory_status_id' => 1,
                'AND' => array(
                    array(
                        'OR' => array(
                            array('Challan.sender_store_id' => $this->UserAuth->getStoreId()),
                            array('Challan.receiver_store_id' => $this->UserAuth->getStoreId())
                        )
                    ),
                    array(
                        'OR' => array(
                            array('Challan.transaction_type_id' => 1), //CWH to ASO (Challan)
                            array('Challan.transaction_type_id' => 4), //CWH to ASO (Challan Receive)
                        )
                    )
                )

            ),
            'recursive' => 0,
            'order' => array('Challan.id' => 'desc')
        );
        $this->set('challans', $this->paginate());
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
        //$this->dd($this->UserAuth->getStoreId());
        $this->set('page_title', 'Challan Details');
        if (!$this->Challan->exists($id)) {
            throw new NotFoundException(__('Invalid challan'));
        }
        $options = array(
            'conditions' => array(
                'Challan.' . $this->Challan->primaryKey => $id
            ),
            'recursive' => 0
        );
        $challan = $this->Challan->find('first', $options);
        $challandetail = $this->ChallanDetail->find(
            'all',
            array(
                'conditions' => array('ChallanDetail.challan_id' => $challan['Challan']['id']),
                'order' => array('Product.order' => 'asc'),
                'fields' => 'ChallanDetail.*,Product.product_code,Product.name,MeasurementUnit.name',
                'recursive' => 0
            )
        );

        if ($this->request->is('post')) {
            if ($this->request->data['Challan']['received_date']) {
                if ($challan['Challan']['status'] > 1) {
                    $this->Session->setFlash(__('Challan has been received.'), 'flash/success');
                    $this->redirect(array('action' => 'index'));
                }
				
				if ($challan['Challan']['sender_store_id'] <= 0) {
                    $this->Session->setFlash(__('Sender Store Missing. Please update sender store.'), 'flash/error');
                    $this->redirect(array('action' => 'index'));
                }
				
                // update challan status 
                $chalan['id'] = $id;
                $chalan['status'] = 2;
                $chalan['received_date'] = date('Y-m-d', strtotime($this->request->data['Challan']['received_date']));
                $chalan['updated_at'] = $this->current_datetime();
                $chalan['updated_by'] = $this->UserAuth->getUserId();
                $chalan['transaction_type_id'] = 4; // CWH to ASO (Receive Challan)

                $challan_update_set_sql = "
                received_date = '" . $chalan['received_date'] . "',
                transaction_type_id = '" . $chalan['transaction_type_id'] . "',
                updated_at = '" . $chalan['updated_at'] . "',
                updated_by = '" . $chalan['updated_by'] . "',
                ";

                $challan_update_conditions = "id=$id";

                $challan_update_set_sql .= " status=2";
                $challan_update_conditions .= " AND status=1";
                $datasource = $this->Challan->getDataSource();
                try {
                    $datasource->begin();
                    $prev_challan_status = $this->Challan->query("SELECT * FROM challans WHERE $challan_update_conditions");
                    $challan_update = 0;
                    if ($prev_challan_status) {
                        if (!$challan_update = $this->Challan->query("UPDATE challans set $challan_update_set_sql WHERE $challan_update_conditions")) {
                            throw new Exception();
                        }
                    }
                    // $this->request->data['Challan']['id'] = $id;
                    // if ($this->Challan->save($this->request->data)) 
                    if ($challan_update) {
                        if (!$this->Challan->save($chalan)) {
                            throw new Exception();
                        }
                        $data_array = array();
                        $insert_data_array = array();
                        $update_data_array = array();
                        foreach ($this->request->data['product_id'] as $key => $val) {
                            if ($this->request->data['receive_quantity'][$key] != '' and $this->request->data['receive_quantity'][$key] <= $this->request->data['quantity'][$key]) {
                                $receive_quantity = $this->request->data['receive_quantity'][$key];
                                //$this->dd($receive_quantity.' receive_quantity 1');
                                $quantity = $this->unit_convert($val, $this->request->data['measurement_unit_id'][$key], $receive_quantity);
                            } else {
                                $receive_quantity = $this->request->data['quantity'][$key];
                                $quantity = $this->unit_convert($val, $this->request->data['measurement_unit_id'][$key], $receive_quantity);
                            }

                            /* ------------- virtual product latest price check --------------------------- */
                            $latest_price = $this->Product->find('all', array(
                                'conditions' => array(
                                    'OR' => array(
                                        'Product.id' => $val,
                                        'Product.parent_id' => $val
                                    ),
                                    'PPS.is_so' => 1,
                                    'Product.is_active' => 1
                                ),
                                'joins' => array(
                                    array(
                                        'table' => 'product_prices_v2',
                                        'alias' => 'PP',
                                        'conditions' => 'PP.product_id=Product.id and PP.effective_date <= \'' . date('Y-m-d', strtotime($challan['Challan']['challan_date'])) . '\''
                                    ),
                                    array(
                                        'table' => 'product_price_section_v2',
                                        'alias' => 'PPS',
                                        'conditions' => 'PP.id=PPS.product_price_id'
                                    )
                                ),
                                'group' => array('Product.id', 'Product.is_virtual', 'PP.effective_date', 'Product.parent_id'),
                                'fields' => array('Product.id', 'Product.is_virtual', 'PP.effective_date', 'Product.parent_id'),
                                'order' => array('PP.effective_date desc', 'Product.is_virtual'),
                                'limit' => 1,
                                'recursive' => -1
                            ));
                            $is_virtual = 0;
                            $parent_id = 0;
                            if (count($latest_price) > 0 && $latest_price['0']['Product']['is_virtual'] == 1 && $latest_price['0']['Product']['id'] != $val) {
                                $val = $latest_price['0']['Product']['id'];
                                $is_virtual = 1;
                            } elseif (count($latest_price) > 0 && $latest_price['0']['Product']['is_virtual'] == 1 && $latest_price['0']['Product']['id'] == $val) {
                                $parent_id = $latest_price['0']['Product']['parent_id'];
                                $is_virtual = 2;
                            }
                            // ------------ stock update --------------------           
                            $inventory_info = $this->CurrentInventory->find('first', array(
                                'conditions' => array(
                                    'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
                                    'CurrentInventory.inventory_status_id' => 1,
                                    'CurrentInventory.product_id' => $val,
                                    'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
                                    'CurrentInventory.expire_date' => $this->request->data['expire_date'][$key]
                                ),
                                'recursive' => -1
                            ));

                            $this->dd($quantity);

                            if (!empty($inventory_info)) {
                                $update_data['id'] = $inventory_info['CurrentInventory']['id'];
                                $update_data['qty'] = $inventory_info['CurrentInventory']['qty'] + $quantity;
                                $update_data['updated_at'] = $this->current_datetime();
                                $update_data['transaction_type_id'] = 4; // CWH to ASO (Receive Challan)
                                $update_data['transaction_date'] = date('Y-m-d', strtotime($this->request->data['Challan']['received_date']));
                                $update_data['source'] = $this->request->data['source'][$key];
                                $this->dd($update_data);
                                // $update_data_array[] = $update_data;
                                if (!$this->CurrentInventory->saveAll($update_data)) {
                                    throw new Exception();
                                }
                                unset($update_data);
                            } else {
                                $insert_data['store_id'] = $this->UserAuth->getStoreId();
                                $insert_data['inventory_status_id'] = 1;
                                $insert_data['product_id'] = $val;
                                $insert_data['batch_number'] = $this->request->data['batch_no'][$key];
                                $insert_data['source'] = $this->request->data['source'][$key];
                                $insert_data['expire_date'] = $this->request->data['expire_date'][$key];
                                $insert_data['qty'] = $quantity;
                                $insert_data['updated_at'] = $this->current_datetime();
                                $insert_data['transaction_type_id'] = 4; // CWH to ASO
                                $insert_data['transaction_date'] = date('Y-m-d', strtotime($this->request->data['Challan']['received_date']));
                                $this->dd($insert_data);
                                // $insert_data_array[] = $insert_data;
                                if (!$this->CurrentInventory->saveAll($insert_data)) {
                                    throw new Exception();
                                }
                                unset($insert_data);
                            }
                            //----------------- End Stock update ---------------------

                            unset($data['ChallanDetail']['virtual_product_id']);
                            unset($data['ChallanDetail']['product_id']);
                            $data['ChallanDetail']['id'] = $this->request->data['id'][$key];
                            $data['ChallanDetail']['received_qty'] = $receive_quantity;
                            if ($is_virtual == 1) {
                                $data['ChallanDetail']['virtual_product_id'] = $val;
                            } elseif ($is_virtual == 2 && $parent_id) {
                                $data['ChallanDetail']['virtual_product_id'] = $val;
                                $data['ChallanDetail']['product_id'] = $parent_id;
                            }
                            $data_array[] = $data;
                        }

                        /*// insert inventory data
                        $this->CurrentInventory->saveAll($insert_data_array);
        
                        // Update inventory data
                        $this->CurrentInventory->saveAll($update_data_array);*/

                        // update received quantity
                        if (!$this->ChallanDetail->saveAll($data_array)) {
                            throw new Exception();
                        };
                        $datasource->commit();
                        $this->Session->setFlash(__('Challan has been received.'), 'flash/success');
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash(__('Challan Already has been received.'), 'flash/success');
                        $this->redirect(array('action' => 'index'));
                    }
                } catch (Exception $e) {
                    $datasource->rollback();
                    $this->Session->setFlash(__('SomeThing Went Wrong! Please Try Again.'), 'flash/error');
                    $this->redirect(array('action' => 'view/' . $id));
                }
            } else {
                $this->Session->setFlash(__('Challan Received Date is required.'), 'flash/error');
                $this->redirect(array('action' => 'view/' . $id));
            }
        }
        $office_paren_id = $this->UserAuth->getOfficeParentId();
        $this->set(compact('challan', 'challandetail', 'office_paren_id'));
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add()
    {
        $this->set('page_title', 'New Challan');

        if ($this->request->is('post')) {

            if (empty($this->request->data['product_id'])) {
                $this->Session->setFlash(__('Challan not created.'), 'flash/error');
                $this->redirect(array('action' => 'add'));
            } else {
                date_default_timezone_set('Asia/Dhaka');
                $yesterday = date('d-m-Y', strtotime('-1 day'));
                $today = date('d-m-Y');
                $challan_date = $this->request->data['Challan']['challan_date'];

                if ($challan_date != $yesterday && $challan_date != $today) {
                    $this->Session->setFlash(__('Challan Date must be yesterday or today.'), 'flash/error');
                    $this->redirect(array('action' => 'add'));
                    exit;
                }

                //pr($this->request->data['Challan']['challan_date']);
                //exit;

                $this->request->data['Challan']['transaction_type_id'] = 1; //CWH to ASO
                $this->request->data['Challan']['inventory_status_id'] = 1;
                $this->request->data['Challan']['challan_date'] = date('Y-m-d', strtotime($this->request->data['Challan']['challan_date']));
                $this->request->data['Challan']['sender_store_id'] = $this->UserAuth->getStoreId();
                $this->request->data['Challan']['created_at'] = $this->current_datetime();
                $this->request->data['Challan']['created_by'] = $this->UserAuth->getUserId();
                $this->request->data['Challan']['updated_at'] = $this->current_datetime();
                $this->request->data['Challan']['status'] = 0;
                /*if (array_key_exists('draft', $this->request->data)) {
                  $this->request->data['Challan']['status'] = 0;
                }else{
                  $this->request->data['Challan']['status'] = 1;
                }*/
                //pr($this->request->data);
                //die();
                $this->Challan->create();
                if ($this->Challan->save($this->request->data)) {
                    $udata['id'] = $this->Challan->id;
                    $udata['challan_no'] = 'CH' . (10000 + $this->Challan->id);
                    $this->Challan->save($udata);

                    if (!empty($this->request->data['product_id'])) {
                        $data_array = array();
                        foreach ($this->request->data['product_id'] as $key => $val) {
                            $data['ChallanDetail']['challan_id'] = $this->Challan->id;
                            $data['ChallanDetail']['product_id'] = $val;
                            $data['ChallanDetail']['measurement_unit_id'] = $this->request->data['measurement_unit'][$key];
                            $data['ChallanDetail']['challan_qty'] = $this->request->data['quantity'][$key];
                            $data['ChallanDetail']['batch_no'] = $this->request->data['batch_no'][$key];
                            //$data['ChallanDetail']['expire_date'] = (($this->request->data['expire_date'][$key] != '' ) ? Date('Y-m-d', strtotime($this->request->data['expire_date'][$key])) : Null);
                            $date = (($this->request->data['expire_date'][$key] != ' ' && $this->request->data['expire_date'][$key] != 'null' && $this->request->data['expire_date'][$key] != '') ? explode('-', $this->request->data['expire_date'][$key]) : '');
                            if (!empty($date[1])) {
                                $date[0] = date('m', strtotime($date[0]));
                                $a_date = date('y-m-d', mktime(0, 0, 0, $date[0], 1, $date[1]));
                                $data['ChallanDetail']['expire_date'] = date("Y-m-t", strtotime($a_date));
                            } else {
                                $data['ChallanDetail']['expire_date'] = '';
                            }
                            $data['ChallanDetail']['inventory_status_id'] = 1;  // set 1 for Sound product
                            $data['ChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
                            $data_array[] = $data;
                        }
                        $this->ChallanDetail->saveAll($data_array);
                    }

                    $this->Session->setFlash(__('Challan has been drafted.'), 'flash/success');
                    $this->redirect(array('action' => 'edit', $this->Challan->id));
                } else {
                    $this->Session->setFlash(__('Challan not created.'), 'flash/error');
                    $this->redirect(array('action' => 'index'));
                }
            }
        }

        $user_group_id = $this->UserAuth->getUserGroupId();
        $user_office_id = $this->UserAuth->getOfficeId();
        $conditions = array();
        if ($user_group_id != 1) {
            $conditions = array('store_type_id' => 2, 'office_id' => $user_office_id);
        } else {
            $conditions = array('store_type_id' => 2);
        }
        $receiver_store = $this->Store->find('list', array(
            'conditions' => $conditions,
            'order' => array('name' => 'asc')
        ));
        //pr($receiver_store);
        $productTypes = $this->ProductType->find('list', array('order' => 'id'));

        //$products = $this->Product->find('list', array('conditions' => array('is_active' => 1), 'order' => array('name' => 'asc')));
        $this->set(compact('receiver_store', 'productTypes'));
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
        $this->Challan->id = $id;
        if (!$this->Challan->exists()) {
            throw new NotFoundException(__('Invalid challan'));
        }
        if ($this->Challan->delete()) {
            $this->flash(__('Challan deleted'), array('action' => 'index'));
        }
        $this->flash(__('Challan was not deleted'), array('action' => 'index'));
        $this->redirect(array('action' => 'index'));
    }

    public function admin_edit($id = null)
    {
        $this->set('page_title', 'Challan Edit');
        if (!$this->Challan->exists($id)) {
            throw new NotFoundException(__('Invalid challan'));
        }

        if ($this->request->is('post')) {

            $challan_id = $id;
            date_default_timezone_set('Asia/Dhaka');
            $yesterday = date('d-m-Y', strtotime('-1 day'));
            $today = date('d-m-Y');
            $challan_date = $this->request->data['Challan']['challan_date'];
            if ($challan_date != $yesterday && $challan_date != $today) {
                $this->Session->setFlash(__('Challan Date must be yesterday or today.'), 'flash/error');
                $this->redirect(array('action' => 'edit', $challan_id));
                exit;
            }


            $this->ChallanDetail->deleteAll(array('ChallanDetail.challan_id' => $challan_id));

            if (empty($this->request->data['product_id'])) {
                $this->Session->setFlash(__('Challan not created.'), 'flash/error');
                $this->redirect(array('action' => 'index'));
            } else {


                $this->request->data['Challan']['transaction_type_id'] = 1; //CWH to ASO
                $this->request->data['Challan']['inventory_status_id'] = 1;
                $this->request->data['Challan']['challan_date'] = date('Y-m-d', strtotime($this->request->data['Challan']['challan_date']));
                $this->request->data['Challan']['sender_store_id'] = $this->UserAuth->getStoreId();
                $this->request->data['Challan']['created_at'] = $this->current_datetime();
                $this->request->data['Challan']['created_by'] = $this->UserAuth->getUserId();
                $this->request->data['Challan']['updated_at'] = $this->current_datetime();
                if (array_key_exists('draft', $this->request->data)) {
                    $this->request->data['Challan']['status'] = 0;
                    $message = "Challan Has Been Saved as Draft";
                } else {
                    $message = "Challan Has Been Saved";
                    $this->request->data['Challan']['status'] = 1;
                }
                $this->request->data['Challan']['id'] = $id;
                if ($this->Challan->save($this->request->data)) {

                    $udata['id'] = $this->Challan->id;
                    $udata['challan_no'] = 'CH' . (10000 + $this->Challan->id);
                    $this->Challan->save($udata);

                    if (!empty($this->request->data['product_id'])) {
                        $data_array = array();
                        foreach ($this->request->data['product_id'] as $key => $val) {
                            $data['ChallanDetail']['challan_id'] = $this->Challan->id;
                            $data['ChallanDetail']['product_id'] = $val;
                            $data['ChallanDetail']['measurement_unit_id'] = $this->request->data['measurement_unit'][$key];
                            $data['ChallanDetail']['challan_qty'] = $this->request->data['quantity'][$key];
                            $data['ChallanDetail']['batch_no'] = $this->request->data['batch_no'][$key];
                            $date = (($this->request->data['expire_date'][$key] != ' ' && $this->request->data['expire_date'][$key] != 'null' && $this->request->data['expire_date'][$key] != '') ? explode('-', $this->request->data['expire_date'][$key]) : '');
                            if (!empty($date[2])) {
                                $data['ChallanDetail']['expire_date'] = $this->request->data['expire_date'][$key];
                            } else {
                                if (!empty($date[1])) {
                                    $date[0] = date('m', strtotime($date[0]));
                                    $a_date = date('y-m-d', mktime(0, 0, 0, $date[0], 1, $date[1]));
                                    $data['ChallanDetail']['expire_date'] = date("Y-m-t", strtotime($a_date));
                                } else {
                                    $data['ChallanDetail']['expire_date'] = '';
                                }
                            }

                            $data['ChallanDetail']['inventory_status_id'] = 1;  // set 1 for Sound product
                            $data['ChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
                            $data_array[] = $data;
                        }
                        $this->ChallanDetail->saveAll($data_array);
                    }

                    $this->Session->setFlash($message, 'flash/success');
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('Challan not created.'), 'flash/error');
                    $this->redirect(array('action' => 'index'));
                }
            }
        }

        $challan_info = $this->Challan->find('all', array(
            'conditions' => array('Challan.id' => $id),
            'recursive' => 2
        ));


        $user_group_id = $this->UserAuth->getUserGroupId();
        $user_office_id = $this->UserAuth->getOfficeId();
        $conditions = array();
        if ($user_group_id != 1) {
            $conditions = array('store_type_id' => 2, 'office_id' => $user_office_id);
        } else {
            $conditions = array('store_type_id' => 2);
        }
        $receiver_store = $this->Store->find('list', array(
            'conditions' => $conditions,
            'order' => array('name' => 'asc')
        ));
        $productTypes = $this->ProductType->find('list', array('order' => 'id'));


        //$products = $this->Product->find('list', array('conditions' => array('is_active' => 1), 'order' => array('name' => 'asc')));
        //pr($challan_info);die();

        $this->set(compact('challan_info', 'receiver_store', 'productTypes'));
    }
    public function get_product()
    {
        $this->loadModel('Product');
        $rs = array(array('id' => '', 'name' => '---- Select -----'));
        $type_id = $this->request->data['product_type_id'];
        if ($type_id == '') {
            $rs = array(array('id' => '', 'name' => '---- Select -----'));
        } else {
            $product = $this->Product->find('all', array(
                'conditions' => array(
                    'Product.product_type_id' => $type_id,
                    'Product.is_virtual' => 0
                ),
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
}
