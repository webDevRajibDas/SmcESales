<?php

App::uses('AppController', 'Controller');

/**
 * PrimaryMemosController Controller
 *
 * @property PrimaryMemo $PrimaryMemo
 * @property PaginatorComponent $Paginator
 */
class PrimaryMemosController  extends AppController
{
    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Filter.Filter');
    public $uses = array('PrimaryMemo', 'PrimaryMemoDetail', 'PrimarySenderReceiver', 'ProductType', 'CurrentInventory', 'Product');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index()
    {

        $this->set('page_title', 'PrimaryMemo List');
        $this->PrimaryMemo->recursive = 0;
        $this->paginate = array(
            'conditions' => array(
                'PrimaryMemo.inventory_status_id' => 1,
            ),
            'recursive' => 0,
            'order' => array('PrimaryMemo.id' => 'desc')
        );
        $this->set('PrimaryMemo', $this->paginate());
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

        $this->set('page_title', 'PrimaryMemo Details');
        if (!$this->PrimaryMemo->exists($id)) {
            throw new NotFoundException(__('Invalid PrimaryMemo'));
        }
        $options = array(
            'conditions' => array(
                'PrimaryMemo.' . $this->PrimaryMemo->primaryKey => $id
            ),
            'recursive' => 0
        );
        $primarymemo = $this->PrimaryMemo->find('first', $options);
        $primarymemodetail = $this->PrimaryMemoDetail->find(
            'all',
            array(
                'conditions' => array('PrimaryMemoDetail.primary_memo_id' => $primarymemo['PrimaryMemo']['id']),
                'order' => array('Product.order' => 'asc'),
                'fields' => 'PrimaryMemoDetail.*,Product.product_code,Product.name,MeasurementUnit.name',
                'recursive' => 0
            )
        );

        if ($this->request->is('post')) {
            //pr($this->request->data);die();
            if ($this->request->data['PrimaryMemo']['received_date']) {
                if ($primarymemos['PrimaryMemo']['status'] > 1) {
                    $this->Session->setFlash(__('PrimaryMemo has been received.'), 'flash/success');
                    $this->redirect(array('action' => 'index'));
                }
                // update challan status 
                $chalan['id'] = $id;
                $chalan['status'] = 2;
                $chalan['received_date'] = date('Y-m-d', strtotime($this->request->data['PrimaryMemo']['received_date']));
                $chalan['updated_at'] = $this->current_datetime();
                $chalan['updated_by'] = $this->UserAuth->getUserId();
                $chalan['transaction_type_id'] = 4; // CWH to ASO (Receive PrimaryMemo)

                $challan_update_set_sql = "
                received_date = '" . $chalan['received_date'] . "',
                transaction_type_id = '" . $chalan['transaction_type_id'] . "',
                updated_at = '" . $chalan['updated_at'] . "',
                updated_by = '" . $chalan['updated_by'] . "',";
                $challan_update_conditions = "id=$id";
                $challan_update_set_sql .= " status=2";
                $challan_update_conditions .= " AND status=1";
                $prev_challan_status = $this->PrimaryMemo->query("SELECT * FROM primary_memos WHERE $challan_update_conditions");
                $challan_update = 0;
                if ($prev_challan_status) {
                    $challan_update = $this->PrimaryMemo->query("UPDATE primary_memos set $challan_update_set_sql WHERE $challan_update_conditions");
                }
                // $this->request->data['PrimaryMemo']['id'] = $id;
                // if ($this->PrimaryMemo->save($this->request->data)) 
                if ($challan_update) {
                    $this->PrimaryMemo->save($chalan);
                    $data_array = array();
                    $insert_data_array = array();
                    $update_data_array = array();
                    foreach ($this->request->data['product_id'] as $key => $val) {
                        if ($this->request->data['receive_quantity'][$key] != '' and $this->request->data['receive_quantity'][$key] <= $this->request->data['quantity'][$key]) {
                            $receive_quantity = $this->request->data['receive_quantity'][$key];
                            $quantity = $this->unit_convert($val, $this->request->data['measurement_unit_id'][$key], $receive_quantity);
                        } else {
                            $receive_quantity = $this->request->data['quantity'][$key];
                            $quantity = $this->unit_convert($val, $this->request->data['measurement_unit_id'][$key], $receive_quantity);
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

                        if (!empty($inventory_info)) {
                            $update_data['id'] = $inventory_info['CurrentInventory']['id'];
                            $update_data['qty'] = $inventory_info['CurrentInventory']['qty'] + $quantity;
                            $update_data['updated_at'] = $this->current_datetime();
                            $update_data['transaction_type_id'] = 4; // CWH to ASO (Receive PrimaryMemo)
                            $update_data['transaction_date'] = date('Y-m-d', strtotime($this->request->data['PrimaryMemo']['received_date']));
                            $update_data['source'] = $this->request->data['source'][$key];
                            // $update_data_array[] = $update_data;
                            $this->CurrentInventory->saveAll($update_data);
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
                            $insert_data['transaction_date'] = date('Y-m-d', strtotime($this->request->data['PrimaryMemo']['received_date']));
                            // $insert_data_array[] = $insert_data;
                            $this->CurrentInventory->saveAll($insert_data);
                            unset($insert_data);
                        }
                        //----------------- End Stock update ---------------------

                        $data['PrimaryMemoDetail']['id'] = $this->request->data['id'][$key];
                        $data['PrimaryMemoDetail']['received_qty'] = $receive_quantity;
                        $data_array[] = $data;
                    }
                    // update received quantity
                    $this->PrimaryMemoDetail->saveAll($data_array);
                    $this->Session->setFlash(__('PrimaryMemo has been received.'), 'flash/success');
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('PrimaryMemo Already has been received.'), 'flash/success');
                    $this->redirect(array('action' => 'index'));
                }
            } else {
                $this->Session->setFlash(__('PrimaryMemo Received Date is required.'), 'flash/error');
                $this->redirect(array('action' => 'view/' . $id));
            }
        }

        $office_paren_id = $this->UserAuth->getOfficeParentId();
        $this->set(compact('primarymemo', 'primarymemodetail', 'office_paren_id'));
    }

    /**
     * admin_add method
     *
     * @return void
     */

    public function admin_add()
    {
        $this->set('page_title', 'Add new primary memo');
        if ($this->request->is('post')) {
            if (empty($this->request->data['product_id'])) {
                $this->Session->setFlash(__('PrimaryMemo not created.'), 'flash/error');
                $this->redirect(array('action' => 'add'));
            } else {
                date_default_timezone_set('Asia/Dhaka');
                $yesterday = date('d-m-Y', strtotime('-1 day'));
                $today = date('d-m-Y');
                // $challan_date = $this->request->data['PrimaryMemo']['challan_date'];
                // if($challan_date!=$yesterday && $challan_date!=$today)
                //     {
                //      $this->Session->setFlash(__('PrimaryMemo Date must be yesterday or today.'), 'flash/error');
                //      $this->redirect(array('action' => 'add'));
                //      exit;
                //  }
                $this->request->data['PrimaryMemo']['transaction_type_id'] = 1; //CWH to ASO
                $this->request->data['PrimaryMemo']['inventory_status_id'] = 1;
                if (isset($this->request->data['PrimaryMemo']['challan_date']) && $this->request->data['PrimaryMemo']['challan_date'])
                    $this->request->data['PrimaryMemo']['challan_date'] = date('Y-m-d', strtotime($this->request->data['PrimaryMemo']['challan_date']));
                else
                    $this->request->data['PrimaryMemo']['challan_date'] = '';
                $this->request->data['PrimaryMemo']['sender_store_id'] = $this->request->data['PrimaryMemo']['sender_store_id'];
                $this->request->data['PrimaryMemo']['receiver_store_id'] =  $this->request->data['PrimaryMemo']['receiver_store_id'];
                $this->request->data['PrimaryMemo']['created_at'] = $this->current_datetime();
                $this->request->data['PrimaryMemo']['created_by'] = $this->UserAuth->getUserId();
                $this->request->data['PrimaryMemo']['updated_at'] = $this->current_datetime();
                $this->request->data['PrimaryMemo']['status'] = 0;

                $this->PrimaryMemo->create();
                if ($this->PrimaryMemo->save($this->request->data)) {
                    $udata['id'] = $this->PrimaryMemo->id;
                    $udata['challan_no'] = 'CH' . (10000 + $this->PrimaryMemo->id);
                    $this->PrimaryMemo->save($udata);
                    if (!empty($this->request->data['product_id'])) {
                        $data_array = array();
                        foreach ($this->request->data['product_id'] as $key => $val) {
                            $data['PrimaryMemoDetail']['primary_memo_id'] = $this->PrimaryMemo->id;
                            $data['PrimaryMemoDetail']['product_id'] = $val;
                            $data['PrimaryMemoDetail']['measurement_unit_id'] = $this->request->data['measurement_unit'][$key];
                            $data['PrimaryMemoDetail']['challan_qty'] = $this->request->data['quantity'][$key];
                            $data['PrimaryMemoDetail']['received_qty'] = $this->request->data['quantity'][$key];
                            $data['PrimaryMemoDetail']['batch_no'] = $this->request->data['batch_no'][$key];
                            $date = (($this->request->data['expire_date'][$key] != ' ' && $this->request->data['expire_date'][$key] != 'null' && $this->request->data['expire_date'][$key] != '') ? explode('-', $this->request->data['expire_date'][$key]) : '');
                            if (!empty($date[1])) {
                                $date[0] = date('m', strtotime($date[0]));
                                $a_date = date('y-m-d', mktime(0, 0, 0, $date[0], 1, $date[1]));
                                $data['PrimaryMemoDetail']['expire_date'] = date("Y-m-t", strtotime($a_date));
                            } else {
                                $data['PrimaryMemoDetail']['expire_date'] = '';
                            }
                            $data['PrimaryMemoDetail']['inventory_status_id'] = 1;  // set 1 for Sound product
                            $data['PrimaryMemoDetail']['product_price'] = $this->request->data['product_price'][$key];
                            $data['PrimaryMemoDetail']['vat'] = $this->request->data['vat'][$key];
                            $data['PrimaryMemoDetail']['remarks'] = $this->request->data['remarks'][$key];
                            $data_array[] = $data;
                        }
                        $this->PrimaryMemoDetail->saveAll($data_array);
                    }
                    $this->Session->setFlash(__('PrimaryMemo has been drafted.'), 'flash/success');
                    $this->redirect(array('action' => 'edit', $this->PrimaryMemo->id));
                } /* else {
                    $this->Session->setFlash(__('PrimaryMemo not created.'), 'flash/error');
                    $this->redirect(array('action' => 'index'));
                } */
            }
        }
        $user_group_id = $this->UserAuth->getUserGroupId();
        $user_office_id = $this->UserAuth->getOfficeId();

        $sender = $this->PrimarySenderReceiver->find('list', array(
            'conditions' => array('store_type' => 1),
            'order' => array('name' => 'asc')
        ));
        $receiver = $this->PrimarySenderReceiver->find('list', array(
            'conditions' => array('store_type' => 2),
            'order' => array('name' => 'asc')
        ));
        //pr($client_name);
        // $productTypes=$this->ProductType->find('list',array('order'=>'id'));
        $products = $this->Product->find('list', array(
            'conditions' => array('Product.id' => array(48, 339, 47, 740)),
            'recursive' => -1
        ));

        $this->set(compact('sender', 'receiver', 'products'));
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
        $this->PrimaryMemo->id = $id;
        if (!$this->PrimaryMemo->exists()) {
            throw new NotFoundException(__('Invalid challan'));
        }
        $datasource = $this->PrimaryMemo->getDataSource();
        try {
            $datasource->begin();
            if (!$this->PrimaryMemo->delete()) {
                throw new Exception();
            }
            if (!$this->PrimaryMemoDetail->deleteAll(array('PrimaryMemoDetail.primary_memo_id' => $id))) {
                throw new Exception();
            }
            $datasource->commit();
            $this->Session->setFlash(__('PrimaryMemo deleted'), array('action' => 'index'));
        } catch (Exception $e) {
            $datasource->rollback();
            $this->Session->setFlash(__('SomeThing Went Wrong! Please Try Again.'), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }

        $this->Session->setFlash(__('PrimaryMemo was not deleted'), array('action' => 'index'));
        $this->redirect(array('action' => 'index'));
    }

    public function admin_edit($id = null)
    {
        $this->set('page_title', 'PrimaryMemo Edit');
        if (!$this->PrimaryMemo->exists($id)) {
            throw new NotFoundException(__('Invalid challan'));
        }
        if ($this->request->is('post')) {
            /* echo "<pre>";
            print_r($this->request->data);
            exit(); */
            $primary_memo_id = $id;
            date_default_timezone_set('Asia/Dhaka');
            $yesterday = date('d-m-Y', strtotime('-1 day'));
            $today = date('d-m-Y');
            $challan_date = $this->request->data['PrimaryMemo']['challan_date'];
            // if($challan_date!=$yesterday && $challan_date!=$today){
            //     $this->Session->setFlash(__('PrimaryMemo Date must be yesterday or today.'), 'flash/error');
            //     $this->redirect(array('action' => 'edit', $primary_memo_id));
            //     exit;
            // }
            $this->PrimaryMemoDetail->deleteAll(array('PrimaryMemoDetail.primary_memo_id' => $primary_memo_id));

            if (empty($this->request->data['product_id'])) {
                $this->Session->setFlash(__('PrimaryMemo not created.'), 'flash/error');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->request->data['PrimaryMemo']['transaction_type_id'] = 1; //CWH to ASO
                $this->request->data['PrimaryMemo']['inventory_status_id'] = 1;
                if (isset($this->request->data['PrimaryMemo']['challan_date']) && $this->request->data['PrimaryMemo']['challan_date'])
                    $this->request->data['PrimaryMemo']['challan_date'] = date('Y-m-d', strtotime($this->request->data['PrimaryMemo']['challan_date']));
                else
                    $this->request->data['PrimaryMemo']['challan_date'] = '';
                $this->request->data['PrimaryMemo']['sender_store_id'] = $this->request->data['PrimaryMemo']['sender_store_id'];
                $this->request->data['PrimaryMemo']['receiver_store_id'] =  $this->request->data['PrimaryMemo']['receiver_store_id'];
                $this->request->data['PrimaryMemo']['created_at'] = $this->current_datetime();
                $this->request->data['PrimaryMemo']['created_by'] = $this->UserAuth->getUserId();
                $this->request->data['PrimaryMemo']['updated_at'] = $this->current_datetime();

                /*  pr($this->request->data);
                exit; */

                if (array_key_exists('draft', $this->request->data)) {
                    $this->request->data['PrimaryMemo']['status'] = 0;
                    $message = "PrimaryMemo Has Been Saved as Draft";
                } else {
                    $message = "PrimaryMemo Has Been Saved";
                    $this->request->data['PrimaryMemo']['status'] = 1;
                }
                $this->request->data['PrimaryMemo']['id'] = $id;
                if ($this->PrimaryMemo->save($this->request->data)) {
                    $udata['id'] = $this->PrimaryMemo->id;
                    $udata['challan_no'] = 'CH' . (10000 + $this->PrimaryMemo->id);
                    $this->PrimaryMemo->save($udata);
                    if (!empty($this->request->data['product_id'])) {
                        $data_array = array();
                        foreach ($this->request->data['product_id'] as $key => $val) {
                            $data['PrimaryMemoDetail']['primary_memo_id'] = $this->PrimaryMemo->id;
                            $data['PrimaryMemoDetail']['product_id'] = $val;
                            $data['PrimaryMemoDetail']['measurement_unit_id'] = $this->request->data['measurement_unit'][$key];
                            $data['PrimaryMemoDetail']['challan_qty'] = $this->request->data['quantity'][$key];
                            $data['PrimaryMemoDetail']['received_qty'] = $this->request->data['quantity'][$key];
                            $data['PrimaryMemoDetail']['batch_no'] = $this->request->data['batch_no'][$key];
                            $date = (($this->request->data['expire_date'][$key] != ' ' && $this->request->data['expire_date'][$key] != 'null' && $this->request->data['expire_date'][$key] != '') ? explode('-', $this->request->data['expire_date'][$key]) : '');

                            if (!empty($date[1])) {
                                $date[0] = date('m', strtotime($date[0]));
                                $a_date = date('y-m-d', mktime(0, 0, 0, $date[0], 1, $date[1]));
                                $data['PrimaryMemoDetail']['expire_date'] = date("Y-m-t", strtotime($a_date));
                            } else {
                                $data['PrimaryMemoDetail']['expire_date'] = '';
                            }
                            $data['PrimaryMemoDetail']['inventory_status_id'] = 1;  // set 1 for Sound product
                            $data['PrimaryMemoDetail']['product_price'] = $this->request->data['price'][$key];
                            $data['PrimaryMemoDetail']['vat'] = $this->request->data['vat'][$key];
                            $data['PrimaryMemoDetail']['remarks'] = $this->request->data['remarks'][$key];
                            $data_array[] = $data;
                        }

                        $this->PrimaryMemoDetail->saveAll($data_array);
                    }

                    $this->Session->setFlash($message, 'flash/success');
                    $this->redirect(array('action' => 'index'));
                } /* else {
                    $this->Session->setFlash(__('PrimaryMemo not created.'), 'flash/error');
                    $this->redirect(array('action' => 'index'));
                } */
            }
        }
        $challan_info = $this->PrimaryMemo->find('all', array(
            'conditions' => array('PrimaryMemo.id' => $id),
            'recursive' => 2
        ));
        $user_group_id = $this->UserAuth->getUserGroupId();
        $user_office_id = $this->UserAuth->getOfficeId();
        $sender = $this->PrimarySenderReceiver->find('list', array(
            'conditions' => array('store_type' => 1),
            'order' => array('name' => 'asc')
        ));
        $receiver = $this->PrimarySenderReceiver->find('list', array(
            'conditions' => array('store_type' => 2),
            'order' => array('name' => 'asc')
        ));
        //pr($client_name);
        // $productTypes=$this->ProductType->find('list',array('order'=>'id'));
        $products = $this->Product->find('list', array(
            'conditions' => array('Product.id' => array(48, 339, 47, 740)),
            'recursive' => -1
        ));

        $this->set(compact('sender', 'receiver', 'products'));
        $this->set(compact('challan_info'));
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
                    'Product.source' => 'SMCEL'
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
        $rs = array('' => '---- Select -----');
        $type_id = $this->request->data['product_type_id'];
        if ($type_id == '') {
            $product = array('' => '---- Select -----');
        } else {
            $product = $this->Product->find('list', array(
                'conditions' => array('Product.product_type_id' => $type_id),
                'recursive' => -1
            ));
            // pr($product);
            // exit;

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
            $primary_memo_id = $this->request->data['primary_memo_id'];
            if ($primary_memo_id) {
                $con = array('PrimaryMemo.challan_referance_no' => $challan_referance_no, 'PrimaryMemo.id !=' => $primary_memo_id);
            } else {
                $con = array('PrimaryMemo.challan_referance_no' => $challan_referance_no);
            }
            $primarymemo_list = $this->PrimaryMemo->find('list', array(
                'conditions' => $con,
                'fields' => array('challan_referance_no'),
                'recursive' => -1
            ));
            $primarymemo_list = count($primarymemo_list);
            echo json_encode($primarymemo_list);
        }
        $this->autoRender = false;
    }
}
