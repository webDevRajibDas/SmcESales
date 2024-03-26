<?php
App::uses('AppController', 'Controller');

/**
 * Requisitions Controller
 *
 * @property Requisition $Requisition
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class RequisitionsController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Session', 'Filter.Filter');
    public $uses = array('Requisition', 'RequisitionDetail', 'Product', 'CurrentInventory');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index()
    {
        $this->set('page_title', 'DO List');
        $this->Requisition->recursive = 0;
        $conditions = '';
        if ($this->UserAuth->getOfficeParentId() != 0) {
            $conditions = array(
                'OR' => array(
                    'Requisition.sender_store_id' => $this->UserAuth->getStoreId(),
                    'Requisition.receiver_store_id' => $this->UserAuth->getStoreId()
                )
            );
        }


        $this->paginate = array('conditions' => $conditions, 'order' => array('Requisition.id' => 'DESC'));

        $this->set('requisitions', $this->paginate());
        $senderStores = $this->Requisition->SenderStore->find('list', array(
            'conditions' => array('store_type_id' => 2, 'id !=' => $this->UserAuth->getStoreId()),
            'order' => array('name' => 'asc')
        ));
        $receiverStores = $this->Requisition->ReceiverStore->find('list', array(
            'conditions' => array('store_type_id' => 2, 'id !=' => $this->UserAuth->getStoreId()),
            'order' => array('name' => 'asc')
        ));
        $store_id = $this->UserAuth->getStoreId();
        $this->set(compact('senderStores', 'receiverStores', 'store_id'));
    }


    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add()
    {

        $this->set('page_title', 'Create DO');
        //$this->dd($this->request->data);

        if ($this->request->is('post')) {
            if (empty($this->request->data['product_id'])) {
                $this->Session->setFlash(__('Requisition not created.'), 'flash/error');
                $this->redirect(array('action' => 'index'));
            } else {

                $this->Requisition->create();
                //$this->request->data['Requisition']['sender_store_id'] = $this->UserAuth->getStoreId();
                $this->request->data['Requisition']['created_at'] = $this->current_datetime();
                $this->request->data['Requisition']['created_by'] = $this->UserAuth->getUserId();
                $this->request->data['Requisition']['updated_at'] = $this->current_datetime();
                $this->request->data['Requisition']['updated_by'] = $this->UserAuth->getUserId();
                $this->request->data['Requisition']['status'] = 0;
                $this->request->data['Requisition']['is_do'] = 1;


                $sender_store_id = $this->request->data['Requisition']['sender_store_id'];

                if ($this->Requisition->save($this->request->data)) {
                    // create do number
                    $update_data['Requisition']['id'] = $this->Requisition->id;
                    $update_data['Requisition']['do_no'] = 'DO' . (1000 + $this->Requisition->id);
                    $this->Requisition->save($update_data);

                    if (!empty($this->request->data['product_id'])) {
                        $data_array = array();
                        $parent_prodct_array = array();
                        foreach ($this->request->data['product_id'] as $key => $val) {
                            // DO details data
                            $data['RequisitionDetail']['requisition_id'] = $this->Requisition->id;
                            //$data['RequisitionDetail']['product_id'] = $val;
                            $productinfo = $this->get_mother_product_info($val);
                            if ($productinfo['Product']['parent_id'] > 0) {
                                $checkparentproudt = $parent_prodct_array[$productinfo['Product']['parent_id']];
                                if (count($checkparentproudt) > 0) {
                                    $pdnamehsow = 1;
                                } else {
                                    $pdnamehsow = $this->get_product_inventroy_check($sender_store_id, $productinfo['Product']['parent_id'], $val);
                                }


                                $data['RequisitionDetail']['product_id'] = $productinfo['Product']['parent_id'];
                                $data['RequisitionDetail']['virtual_product_id'] = $val;
                                $data['RequisitionDetail']['virtual_product_name_show'] = $pdnamehsow;

                            } else {

                                $data['RequisitionDetail']['product_id'] = $val;
                                $data['RequisitionDetail']['virtual_product_id'] = 0;
                                $data['RequisitionDetail']['virtual_product_name_show'] = 0;
                                $parent_prodct_array[$val] = $val;
                            }


                            //$data['RequisitionDetail']['batch_no'] = $this->request->data['batch_no'.$val];
                            $data['RequisitionDetail']['batch_no'] = 0;
                            //$data['RequisitionDetail']['expire_date'] = ($this->request->data['expire_date'.$val] != '' ? date('Y-m-d',strtotime($this->request->data['expire_date'.$val])) : '0000-00-00');
                            $data['RequisitionDetail']['expire_date'] = date('Y-m-d');
                            $data['RequisitionDetail']['inventory_status_id'] = 1;
                            $data['RequisitionDetail']['measurement_unit_id'] = $this->request->data['measurement_unit' . $val];
                            $data['RequisitionDetail']['qty'] = $this->request->data['quantity' . $val];
                            $data['RequisitionDetail']['remaining_qty'] = $this->request->data['quantity' . $val];
                            $data_array[] = $data;
                        }

                        // insert Requisition Detail data
                        $this->RequisitionDetail->saveAll($data_array);
                    }

                    $this->Session->setFlash(__('The DO has been Drafted'), 'flash/success');
                    $this->redirect(array('action' => 'edit', $this->Requisition->id));
                }
            }
        }

        // get DO receiver store list

        $senderStores = $this->Requisition->ReceiverStore->find('list', array(
            'fields' => '',
            'conditions' => array('store_type_id' => 2),
            'order' => array('name' => 'asc')

        ));
        $this->set(compact('senderStores'));
    }

    public function get_mother_product_info($pid)
    {

        $productinfo = $this->Product->find('first', array(
            'conditions' => array(
                'Product.id' => $pid
            ),
            'fields' => array('Product.id', 'Product.name', 'Product.product_code', 'Product.is_virtual', 'Product.parent_id'),
            'recursive' => -1
        ));

        return $productinfo;

    }

    public function get_product_inventroy_check($sender_store_id, $parent_product_id, $virtual_product_id){
        $parentproductcount = $this->CurrentInventory->find('count', array(
            'fields' => array('Product.id'),
            'conditions' => array(
                'CurrentInventory.inventory_status_id' => 1,
                'CurrentInventory.store_id' => $sender_store_id,
                'CurrentInventory.product_id' => $parent_product_id,
            ),
            //'group' => array('CurrentInventory.product_id'),
            'order' => array('Product.name' => 'asc'),
            'recursive' => 0
        ));

        if (empty($parentproductcount)) {
            $parentproductcount = 0;
        }

        $chilhproductCount = $this->CurrentInventory->find('count', array(
            'fields' => array('Product.id'),
            'conditions' => array(
                'CurrentInventory.inventory_status_id' => 1,
                'CurrentInventory.store_id' => $sender_store_id,
                'Product.parent_id' => $parent_product_id
            ),
            /*'joins'=>array(
                array(
                    'alias' => 'Product',
                    'table' => 'products',
                    'type' => 'INNER',
                    'conditions' => 'Product.id = CurrentInventory.product_id'
                )
            ),*/
            //'group' => array('CurrentInventory.product_id'),
            'order' => array('Product.name' => 'asc'),
            'recursive' => 0
        ));


        if ($parentproductcount == 0 and $chilhproductCount == 1) {
            $show = 0;
        } else {
            $show = 1;
        }

        return $show;


    }


    public function admin_edit($id = null)
    {
        $this->set('page_title', 'DO Edit');
        $this->Requisition->id = $id;

        //$this->dd($this->request->data);

        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['Requisition']['updated_at'] = $this->current_datetime();
            $this->request->data['Requisition']['updated_by'] = $this->UserAuth->getUserId();
            if (array_key_exists('draft', $this->request->data)) {
                $this->request->data['Requisition']['status'] = 0;
            } else {
                $this->request->data['Requisition']['status'] = 1;
            }

            $sender_store_id = $this->request->data['Requisition']['sender_store_id'];

            if ($this->Requisition->save($this->request->data)) {
                $this->Requisition->RequisitionDetail->deleteAll(array('RequisitionDetail.requisition_id' => $id), false);
                if (!empty($this->request->data['product_id'])) {
                    $data_array = array();
                    $parent_prodct_array = array();
                    foreach ($this->request->data['product_id'] as $key => $val) {
                        // DO details data
                        $data['RequisitionDetail']['requisition_id'] = $id;
                        //$data['RequisitionDetail']['product_id'] = $val;

                        $productinfo = $this->get_mother_product_info($val);

                        if ($productinfo['Product']['parent_id'] > 0) {

                            $checkparentproudt = $parent_prodct_array[$productinfo['Product']['parent_id']];
                            if (count($checkparentproudt) > 0) {
                                $pdnamehsow = 1;
                            } else {
                                $pdnamehsow = $this->get_product_inventroy_check($sender_store_id, $productinfo['Product']['parent_id'], $val);
                            }

                            $data['RequisitionDetail']['product_id'] = $productinfo['Product']['parent_id'];
                            $data['RequisitionDetail']['virtual_product_id'] = $val;
                            $data['RequisitionDetail']['virtual_product_name_show'] = $pdnamehsow;

                        } else {

                            $data['RequisitionDetail']['product_id'] = $val;
                            $data['RequisitionDetail']['virtual_product_id'] = 0;
                            $data['RequisitionDetail']['virtual_product_name_show'] = 0;

                            $parent_prodct_array[$val] = $val;

                        }

                        //$data['RequisitionDetail']['batch_no'] = $this->request->data['batch_no'.$val];
                        $data['RequisitionDetail']['batch_no'] = 0;
                        //$data['RequisitionDetail']['expire_date'] = ($this->request->data['expire_date'.$val] != '' ? date('Y-m-d',strtotime($this->request->data['expire_date'.$val])) : '0000-00-00');
                        $data['RequisitionDetail']['expire_date'] = date('Y-m-d');
                        $data['RequisitionDetail']['inventory_status_id'] = 1;
                        $data['RequisitionDetail']['measurement_unit_id'] = $this->request->data['measurement_unit' . $val];
                        $data['RequisitionDetail']['qty'] = $this->request->data['quantity' . $val];
                        $data['RequisitionDetail']['remaining_qty'] = $this->request->data['quantity' . $val];
                        $data_array[] = $data;
                    }

                    // insert Requisition Detail data
                    $this->RequisitionDetail->saveAll($data_array);

                    if (array_key_exists('draft', $this->request->data)) {
                        $this->Session->setFlash(__('The DO has been Drafted'), 'flash/success');
                    } else {
                        $this->Session->setFlash(__('The DO has been Saved'), 'flash/success');
                    }


                    $this->redirect(array('action' => 'index'));
                }
            }
        } else {


            $options = array('conditions' => array('Requisition.' . $this->Requisition->primaryKey => $id),
                'recursive' => -1
            );
            $requisition_info = $this->Requisition->find('first', $options);
            $requisition_details = $this->Requisition->RequisitionDetail->find('all', array(
                'conditions' => array('RequisitionDetail.requisition_id' => $id),
                'fields' => array('RequisitionDetail.*', 'Product.name', 'VirtualProduct.name', 'measurement_unit.name'),
                'joins' => array(
                    array(
                        'table' => 'products',
                        'alias' => 'Product',
                        'type' => 'inner',
                        'conditions' => array(
                            'RequisitionDetail.product_id = Product.id'
                        ),
                    ),
                    array(
                        'table' => 'measurement_units',
                        'alias' => 'measurement_unit',
                        'type' => 'inner',
                        'conditions' => array(
                            'RequisitionDetail.measurement_unit_id = measurement_unit.id'
                        )
                    ),
                    array(
                        'table' => 'products',
                        'alias' => 'VirtualProduct',
                        'type' => 'left',
                        'conditions' => array(
                            'RequisitionDetail.virtual_product_id = VirtualProduct.id'
                        )
                    ),
                ),
                'recursive' => -1
            ));
            //echo '<pre>';print_r($requisition_details);die;
            $senderStores = $this->Requisition->ReceiverStore->find('list', array(
                'conditions' => array('store_type_id' => 2),
                'order' => array('name' => 'asc')
            ));
            $this->set(compact('requisition_info', 'senderStores', 'requisition_details'));
        }
    }

    /**
     * admin_edit method
     *
     * @param string $id
     * @return void
     * @throws NotFoundException
     */
    public function admin_view($id = null)
    {
        $this->set('page_title', 'DO Details');
        $this->Requisition->id = $id;

        if (!$this->Requisition->exists($id)) {
            throw new NotFoundException(__('Invalid do'));
        }

        $options = array(
            'conditions' => array('Requisition.' . $this->Requisition->primaryKey => $id),
            'recursive' => 0
        );
        $requisition = $this->Requisition->find('first', $options);

        $requisitiondetail = $this->RequisitionDetail->find('all', array(
                'conditions' => array(
                    'RequisitionDetail.requisition_id' => $requisition['Requisition']['id']
                ),
                'fields' => 'RequisitionDetail.*,Product.product_code,Product.name,VirtualProduct.product_code,VirtualProduct.name,MeasurementUnit.name',
                'recursive' => 0
            )
        );

        //echo '<pre>';print_r($requisitiondetail);exit;

        $this->set(compact('requisition', 'requisitiondetail'));
    }

    /**
     * admin_delete method
     *
     * @param string $id
     * @return void
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     */
    public function admin_delete($id = null)
    {

        $options = array('conditions' => array('Requisition.' . $this->Requisition->primaryKey => $id));
        $this->request->data = $this->Requisition->find('first', $options);
        if ($this->request->data['Requisition']['status'] > 1) {
            $this->Session->setFlash(__('You can not delete this DO.'), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }

        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Requisition->id = $id;
        if (!$this->Requisition->exists()) {
            throw new NotFoundException(__('Invalid requisition'));
        }
        if ($this->Requisition->delete()) {
            $this->Session->setFlash(__('DO has been deleted'), 'flash/success');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('DO was not deleted'), 'flash/error');
        $this->redirect(array('action' => 'index'));
    }

    public function get_receiver_store_with_sender_store_product()
    {

        $sender_store = $this->request->data['sender_store_id'];
        $receiverStores = "";
        if ($sender_store) {
            $receiverStores = $this->Requisition->ReceiverStore->find('list', array(
                'conditions' => array('store_type_id' => 2, 'id !=' => $sender_store),
                'order' => array('name' => 'asc')
            ));

        }
        $products = $this->CurrentInventory->find('list', array(
            'fields' => array('Product.id', 'Product.name'),
            'conditions' => array(
                'CurrentInventory.inventory_status_id' => 1,
                'CurrentInventory.store_id' => $sender_store
            ),
            //'group' => array('CurrentInventory.product_id'),
            'order' => array('Product.name' => 'asc'),
            'recursive' => 0
        ));


        $product_ci = array();
        foreach ($products as $key => $each_ci) {
            $product_ci[] = $key;
        }

        $product_ci_in = implode(",", $product_ci);

        $products = $this->Product->find('all', array(
            'conditions' => array(
                'Product.id' => $product_ci,
                //'Product.is_distributor_product' => 1
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

        $group_product = array();
        foreach ($products as $data) {
            if ($data[0]['p_id']) {
                $group_product[$data[0]['p_id']][] = $data[0]['id'];
            } else {
                $group_product[$data[0]['id']][] = $data[0]['id'];
            }
        }

        $product_array = array();
        foreach ($products as $data) {
            if ($data['0']['p_id'] && count($group_product[$data[0]['p_id']]) == 1) {
                $name = $data[0]['p_name'];
            } else {
                $name = $data[0]['name'];
            }
            $product_array[$data[0]['id']] = $name;

        }
        foreach ($product_array as $key_id => $product) {
            $return_product[] = array(
                'id' => $key_id,
                'name' => $product
            );
        }
        //pr($return_product);
        //echo '<pre>';print_r( $product_array );exit;

        echo json_encode(array('receiver_store' => $receiverStores, 'products' => $return_product));
        $this->autoRender = false;

    }
}
