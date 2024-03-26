<?php

App::uses('AppController', 'Controller');

/**
 * DistInventoryAdjustments Controller
 *
 * @property DistInventoryAdjustments $DistInventoryAdjustments
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistInventoryAdjustmentsController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Session', 'Filter.Filter');
    public $uses = array('DistInventoryAdjustment', 'DistInventoryAdjustmentDetail', 'DistCurrentInventory');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index()
    {
        $this->set('page_title', 'Distributor Inventory adjustment List');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $adj_conditions = array();
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $adj_conditions = $office_conditions = array('Office.office_type_id' => 2);
        } else {
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
                $adj_conditions = array('DistInventoryAdjustment.distributor_id' => array_keys($tso_dist_list));
            } else {
                $adj_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
            }
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }

        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

        $this->DistInventoryAdjustment->recursive = 0;
        $this->paginate = array(
            'fields' => array('DistInventoryAdjustment.*', 'DistTransactionType.*', 'Store.*', 'DistTso.name', 'Office.office_name', 'DistAE.name'),
            'conditions' => $adj_conditions,
            'joins' => array(


                array(
                    'table' => 'dist_tso_mappings',
                    'alias' => 'DistTsoMapping',
                    'type' => 'LEFT',
                    'conditions' => 'DistTsoMapping.dist_distributor_id = DistInventoryAdjustment.distributor_id'

                ),
                array(
                    'table' => 'dist_tsos',
                    'alias' => 'DistTso',
                    'type' => 'LEFT',
                    'conditions' => 'DistTso.id = DistTsoMapping.dist_tso_id'

                ),
                array(
                    'table' => 'dist_area_executives',
                    'alias' => 'DistAE',
                    'type' => 'LEFT',
                    'conditions' => 'DistAE.id = DistTso.dist_area_executive_id'

                ),




            ),
            'order' => array('DistInventoryAdjustment.id' => 'DESC')

        );

        // pr($this->paginate()); exit;

        $this->set('inventoryAdjustments', $this->paginate());
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
        $this->set('page_title', 'Distributor Inventory adjustment Details');
        if (!$this->DistInventoryAdjustment->exists($id)) {
            throw new NotFoundException(__('Invalid inventory adjustment'));
        }
        $options = array(
            'conditions' => array('DistInventoryAdjustment.' . $this->DistInventoryAdjustment->primaryKey => $id)
        );
        $adjustment_list = $this->DistInventoryAdjustment->find('first', $options);

        $adjustment_with_product = array();
        $total_ajustment_product = array();


        foreach ($adjustment_list['DistInventoryAdjustmentDetail'] as $key => $val) {

            $product_info = $this->DistCurrentInventory->find('all', array('conditions' => array('DistCurrentInventory.id' => $val['dist_current_inventory_id'])));
            $adjustment_with_product['CurrentInventory'] = $product_info[0]['DistCurrentInventory'];
            $adjustment_with_product['Store'] = $product_info[0]['DistStore'];
            $adjustment_with_product['Product'] = $product_info[0]['Product'];
            $adjustment_with_product['InventoryStatuses'] = $product_info[0]['InventoryStatuses'];
            $val['product_info'] = $adjustment_with_product;
            $total_ajustment_product[] = $val;
        }
        $adjustment_list['InventoryAdjustmentDetail'] = $total_ajustment_product;
        $this->set('inventoryAdjustment', $adjustment_list);
        $this->set('office_paren_id', $this->UserAuth->getOfficeParentId());

        if ($this->request->is('POST')) {
            /*
            if (!empty($this->request->data['DistCurrentInventory']['current_inventory_id'])) {
                $data['DistCurrentInventory']['transaction_type_id'] = $this->request->data['InventoryAdjustment']['transaction_type_id'];
                foreach ($this->request->data['CurrentInventory']['current_inventory_id'] as $current_inventory_key => $current_inventory_val) {
                    $current_inventory_info = $this->CurrentInventory->find('first', array(
                        'conditions' => array('CurrentInventory.id' => $current_inventory_val),
                        'fields' => array('CurrentInventory.*')
                    ));
                    $current_qty = $current_inventory_info['CurrentInventory']['qty'];
                    $adjust_qty = $this->request->data['CurrentInventory']['quantity'][$current_inventory_key];
                    if ($this->request->data['CurrentInventory']['status'] == 2) {
                        $this->CurrentInventory->id = $current_inventory_val;
                        $data['CurrentInventory']['qty'] = $current_qty + $adjust_qty;
                    } elseif ($this->request->data['CurrentInventory']['status'] == 1) {
                        $this->CurrentInventory->id = $current_inventory_val;
                        $data['CurrentInventory']['qty'] = $current_qty - $adjust_qty;
                    }
                    $data['transaction_date'] = $this->current_date();
                    if ($this->CurrentInventory->save($data)) {
                        $updated = 'yes';
                    }
                }
                if ($updated == 'yes') {
                    $this->InventoryAdjustment->id = $this->request->data['CurrentInventory']['inventory_adjustment_id'];
                    $adjust_data['InventoryAdjustment']['approval_status'] = 1;

                    if ($this->InventoryAdjustment->save($adjust_data)) {
                        $this->Session->setFlash(__('The Inventory Adjustment has been approved'), 'flash/success');
                        $this->redirect(array('action' => 'index/' . $this->request->data['CurrentInventory']['inventory_adjustment_id']));
                    }
                }
            }
            */
        }
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add()
    {
        $this->set('page_title', 'Distributor Add Inventory adjustment');

        $this->loadModel('DistTransactionType');
        $transaction_types = $this->DistTransactionType->find(
            'list',
            array(
                'conditions' => array(
                    'DistTransactionType.adjust' => 1,
                    'DistTransactionType.active' => 1,
                )
            )
        );
        $transaction_typeid_with_inout = $this->DistTransactionType->find(
            'list',
            array(
                'conditions' => array(
                    'DistTransactionType.adjust' => 1,
                    'DistTransactionType.active' => 1,
                ),
                'fields' => array('id', 'inout')
            )
        );
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_conditions = array('Office.office_type_id' => 2);
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }

        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));



        if ($this->request->is('post')) {
            $this->DistInventoryAdjustment->create();
            $distributor_id = $this->request->data['InventoryAdjustment']['distributor_id'];

            $this->loadModel('DistStore');
            $store_id_arr = $this->DistStore->find('first', array(
                'conditions' => array(
                    'DistStore.dist_distributor_id' => $distributor_id
                )
            ));

            $store_id = $store_id_arr['DistStore']['id'];

            $this->request->data['InventoryAdjustment']['dist_store_id'] = $store_id;

            $this->request->data['InventoryAdjustment']['transaction_type_id'] = $this->request->data['InventoryAdjustment']['status'];

            $transactions = $this->DistTransactionType->find(
                'first',
                array(
                    'conditions' => array('DistTransactionType.id' => $this->request->data['InventoryAdjustment']['status']),
                    'recursive' => -1
                )
            );

            $this->request->data['InventoryAdjustment']['status'] = $transactions['DistTransactionType']['inout'];

            $this->request->data['InventoryAdjustment']['remarks'] = $this->request->data['InventoryAdjustment']['remarks'];
            $this->request->data['InventoryAdjustment']['created_at'] = $this->current_datetime();
            $this->request->data['InventoryAdjustment']['created_by'] = $this->UserAuth->getUserId();
            $this->request->data['InventoryAdjustment']['updated_at'] = $this->current_datetime();
            $this->request->data['InventoryAdjustment']['updated_by'] = $this->UserAuth->getUserId();
            $this->request->data['InventoryAdjustment']['approval_status'] = 1;

            $this->request->data['DistInventoryAdjustment'] = $this->request->data['InventoryAdjustment'];

            unset($this->request->data['DistInventoryAdjustment']['product_id']);
            unset($this->request->data['DistInventoryAdjustment']['inventory_status_id']);
            unset($this->request->data['DistInventoryAdjustment']['batch_no']);
            unset($this->request->data['DistInventoryAdjustment']['expire_date']);
            unset($this->request->data['DistInventoryAdjustment']['challan_qty']);


            if ($this->DistInventoryAdjustment->save($this->request->data['DistInventoryAdjustment'])) {

                if (!empty($this->request->data['product_id'])) {
                    $stock_update_array = array();
                    $data_array = array();
                    foreach ($this->request->data['product_id'] as $key => $val) {

                        $CurrentInventory = $this->DistCurrentInventory->find('first', array(
                            'fields' => array('DistCurrentInventory.id', 'DistCurrentInventory.qty'),
                            'conditions' => array(
                                'DistCurrentInventory.store_id' => $store_id,
                                'DistCurrentInventory.product_id' => $val
                            )
                        ));

                        /**  Check Product already existed or not **/

                        if (empty($CurrentInventory)) {
                            $t_id = $transactions['DistTransactionType']['id'];
                            $cd = $this->current_date();
                            $this->DistCurrentInventory->query("insert into dist_current_inventories (store_id,inventory_status_id,product_id,qty,updated_at,transaction_date,transaction_type_id)
                                values ($store_id,1,$val,0,getdate(),'$cd',$t_id)");

                            $CurrentInventory = $this->DistCurrentInventory->find('first', array(
                                'fields' => array('DistCurrentInventory.id', 'DistCurrentInventory.qty'),
                                'conditions' => array(
                                    'DistCurrentInventory.store_id' => $store_id,
                                    'DistCurrentInventory.product_id' => $val
                                )
                            ));
                        }



                        //adjustment
                        // Inventory Adjustment details data
                        $data['DistInventoryAdjustmentDetail']['dist_inventory_adjustment_id'] = $this->DistInventoryAdjustment->id;
                        $data['DistInventoryAdjustmentDetail']['dist_current_inventory_id'] = $CurrentInventory['DistCurrentInventory']['id'];
                        $data['DistInventoryAdjustmentDetail']['quantity'] = $this->request->data['quantity'][$key];
                        $data['DistInventoryAdjustmentDetail']['bonus_quantity'] = $this->request->data['bonus_quantity'][$key];
                        $data_array[] = $data;


                        /*----------------- Stock update : Start ---------------------------*/

                        $current_qty = $CurrentInventory['DistCurrentInventory']['qty'];
                        $current_inventory_val = $CurrentInventory['DistCurrentInventory']['id'];
                        $adjust_qty = $this->request->data['quantity'][$key] + $this->request->data['bonus_quantity'][$key];
                        if ($transactions['DistTransactionType']['inout'] == 2) {
                            $inventory_data['DistCurrentInventory']['id'] = $current_inventory_val;
                            $inventory_data['DistCurrentInventory']['qty'] = $current_qty + $adjust_qty;
                        } elseif ($transactions['DistTransactionType']['inout'] == 1) {
                            /*	
                                                    if($current_qty < $adjust_qty)
							{
								$this->Session->setFlash(__('Adjustment quantity is gretter than current stock'), 'flash/warning');
								$this->redirect(array('action' => 'view/'.$this->request->data['DistCurrentInventory']['inventory_adjustment_id']));
							}
					           */
                            $inventory_data['DistCurrentInventory']['id'] = $current_inventory_val;
                            $inventory_data['DistCurrentInventory']['qty'] = $current_qty - $adjust_qty;
                        }
                        $inventory_data['DistCurrentInventory']['transaction_type_id'] = $transactions['DistTransactionType']['id'];
                        $inventory_data['DistCurrentInventory']['transaction_date'] = $this->current_date();
                        $stock_update_array[] = $inventory_data;
                        unset($inventory_data);
                        /*----------------- Stock update : Start ---------------------------*/
                    }
                    // insert Inventory Adjustment Detail data

                    $this->DistInventoryAdjustmentDetail->saveAll($data_array);
                    $this->DistCurrentInventory->saveAll($stock_update_array);
                }
                $this->Session->setFlash(__('The distributor inventory adjustment has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            }
        }




        // get store's invntory product list
        $products = array();

        $this->set(compact('products', 'transaction_types', 'office_parent_id', 'offices', 'transaction_typeid_with_inout'));
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
        $this->InventoryAdjustment->id = $id;
        if (!$this->InventoryAdjustment->exists()) {
            throw new NotFoundException(__('Invalid inventory adjustment'));
        }
        if ($this->InventoryAdjustment->delete()) {
            $this->Session->setFlash(__('Inventory adjustment deleted'), 'flash/success');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Inventory adjustment was not deleted'), 'flash/error');
        $this->redirect(array('action' => 'index'));
    }

    public function get_product_list_from_dist_id()
    {

        $distributor_id = $this->request->data['distributor_id'];
        $output = "<option value=''>--- Select Product ---</option>";
        if ($distributor_id) {
            $this->loadModel('DistStore');
            $store_id_arr = $this->DistStore->find('first', array(
                'conditions' => array(
                    'DistStore.dist_distributor_id' => $distributor_id
                )
            ));

            // $store_id = $store_id_arr['DistStore']['id'];
            $store_id = $store_id_arr['DistStore']['id'];

            $this->loadModel('Product');

            /*$products = $this->DistCurrentInventory->find('list', array(
                'fields' => array('Product.id', 'Product.name'),
                'conditions' => array(
                    'DistCurrentInventory.store_id' => $store_id
                ),
                'order' => array('Product.order' => 'asc'),
                'recursive' => 0
            ));*/

            $products = $this->Product->find('list', array(
                'fields' => array('Product.id', 'Product.name'),
                'conditions' => array('Product.is_distributor_product' => 1),
                'order' => array('Product.order' => 'asc'),
                'recursive' => 0
            ));

            if ($products) {
                foreach ($products as $key => $data) {
                    $output .= "<option value='$key'>$data</option>";
                }
            }
        }

        echo $output;
        $this->autoRender = false;
    }
    public function get_inventory_details()
    {
        $product_id = $this->request->data['product_id'];
        $db_id = $this->request->data['db_id'];

        $conditions_options['DistStore.dist_distributor_id'] = $db_id;
        $conditions_options['DistCurrentInventory.product_id'] = $product_id;
        $batch_info = $this->DistCurrentInventory->find('first', array(
            'fields' => array('DistCurrentInventory.qty'),
            'conditions' => array($conditions_options),
            'joins' => array(
                array(
                    'table' => 'dist_stores',
                    'alias' => 'DistStore',
                    'conditions' => 'DistStore.id=DistCurrentInventory.store_id'
                )
            ),
            'recursive' => -1
        ));

        if (!empty($batch_info)) {
            echo $batch_info['DistCurrentInventory']['qty'];
        } else {
            echo '';
        }
        $this->autoRender = false;
    }
}
