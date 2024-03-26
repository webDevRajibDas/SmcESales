<?php

App::uses('AppController', 'Controller');

/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
class DistCurrentInventoriesController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Filter.Filter');
    public $uses = array('DistCurrentInventory', 'DistStore', 'ProductType','DistDistributor');

    /**
     * admin_index method
     *
     * @return void
     */

    /**
     * inventory_total method
     */
    public function admin_index() {
        $this->set('page_title', 'Current Inventories');
        $this->DistCurrentInventory->recursive = 1;
        $this->loadModel('DistStore');
        $this->loadModel('InventoryStatuses');
        $this->loadModel('ProductCategory');
        
        $this->loadModel('Office');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        
        
         $office_id = isset($this->request->data['DistCurrentInventory']['office_id']) != '' ? $this->request->data['DistCurrentInventory']['office_id'] : 0;

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $this->set('office_parent_id', $office_parent_id);
        if ($office_parent_id == 0) {
            //$conditions = array('DistCurrentInventory.inventory_status_id !=' => 2);
            $storeCondition = array();
            $office_conditions = array(
                'office_type_id' => 2
            );
        } 
        else {
            if($user_group_id == 1029 || $user_group_id == 1028){
                if($user_group_id == 1028){
                    $dist_ae_info = $this->DistAreaExecutive->find('first',array(
                        'conditions'=>array('DistAreaExecutive.user_id'=>$user_id),
                        'recursive'=> -1,
                    ));
                    $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
                    $dist_tso_info = $this->DistTso->find('list',array(
                        'conditions'=>array('dist_area_executive_id'=>$dist_ae_id),
                        'fields'=> array('DistTso.id','DistTso.dist_area_executive_id'),
                    ));
                    
                    $dist_tso_id = array_keys($dist_tso_info);
                }
                else{
                    $dist_tso_info = $this->DistTso->find('first',array(
                        'conditions'=>array('DistTso.user_id'=>$user_id),
                        'recursive'=> -1,
                    ));
                    $dist_tso_id = $dist_tso_info['DistTso']['id'];
                }
               
                $tso_dist_list = $this->DistTsoMapping->find('list',array(
                    'conditions'=> array(
                        'dist_tso_id' => $dist_tso_id,
                    ),
                    'fields'=>array('DistTsoMapping.dist_distributor_id','DistTsoMapping.dist_tso_id'),
                ));
               //$dist_conditions = array('DistDistributor.id'=>array_keys($tso_dist_list));
               $storeCondition = array('DistStore.dist_distributor_id'=>array_keys($tso_dist_list));
               //$conditions = array('DistCurrentInventory.inventory_status_id !=' => 2);
            }
            elseif($user_group_id == 1034){
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first',array(
                    'conditions'=>array('DistUserMapping.sales_person_id'=>$sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
        
                $storeCondition = array('DistStore.dist_distributor_id'=>$distributor_id);
            }
            else{
                //$conditions = array('DistCurrentInventory.inventory_status_id !=' => 2);
                $storeCondition = array('DistStore.office_id'=>$this->UserAuth->getOfficeId());
            }
            
            //$conditions = array('DistCurrentInventory.Diststore_id' => $this->UserAuth->getStoreId(), 'DistCurrentInventory.inventory_status_id !=' => 2);
            //$storeCondition = array('DistStore.office_id' => $this->UserAuth->getOfficeId());
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }
        
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
        
        $this->DistStore->virtualFields = array(
            "name" => "Distributor.name"
        );
		
		$storeCondition['Distributor.is_active'] = 1;
		 
        $distStores = $this->DistStore->find('list', array(
            'conditions' => $storeCondition,
            //'fields' => array('DistStore.id', 'DistStore.name', 'DistDistributor.id'),
            //'order' => array('Store.name' => 'asc'),
            'joins'=>array(
                array(
                    'table'=>'dist_distributors',
                    'alias'=>'Distributor',
                    'conditions'=>'Distributor.id=DistStore.dist_distributor_id'
                    ),
                
                ),
            'recursive' => 0)
        );
        unset($this->DistStore->virtualFields['name']);
        if(!empty($distStores)){
           $conditions = array('DistCurrentInventory.inventory_status_id !=' => 2,'DistCurrentInventory.store_id'=>array_keys($distStores));
        }  
        if($office_id)    
        {
           $storeCondition = array('DistStore.office_id' => $office_id);  
        }
        else 
        {
            $storeCondition=array();
        }
        $this->DistCurrentInventory->virtualFields = array(
			
			'office_order' => 'Office.order',
			
		);
        $this->paginate = array(
            'conditions' => $conditions,
            'order' => array( 'office_order' => 'ASC','Product.order' => 'ASC'),
            
            'joins' => array(
                array(
                    'table' => 'product_categories',
                    'alias' => 'ProductCategory',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Product.product_category_id = ProductCategory.id'
                    )
                ),
                
             array(
                'table'=>'dist_distributors',
                'alias'=>'Distributor',
                'conditions'=>'Distributor.id=DistStore.dist_distributor_id'
                ),
                
                array(
                    'table'=>'dist_tso_mappings',
                    'alias'=>'DistTsoMapping',
                    'type' => 'LEFT',
                    'conditions' => 'DistTsoMapping.dist_distributor_id = DistStore.dist_distributor_id'
                    
                ),
                array(
                    'table'=>'dist_tsos',
                    'alias'=>'DistTso',
                    'type' => 'LEFT',
                    'conditions' => 'DistTso.id = DistTsoMapping.dist_tso_id'
                    
                ),
                array(
                    'table'=>'dist_area_executives',
                    'alias'=>'DistAE',
                    'type' => 'LEFT',
                    'conditions' => 'DistAE.id = DistTso.dist_area_executive_id'
                    
                ),
                array(
                    'table'=>'offices',
                    'alias'=>'Office',
                    'type' => 'LEFT',
                    'conditions'=>array('Office.id=DistStore.office_id')
                ),
            ),
            'fields' => array('DistCurrentInventory.id', 'DistCurrentInventory.product_id', 'DistCurrentInventory.store_id', 'SUM(DistCurrentInventory.qty) AS total','SUM(DistCurrentInventory.bonus_qty) as total_bonus' ,'Product.name', 'Product.base_measurement_unit_id', 'Product.sales_measurement_unit_id', 'Product.product_code', 'InventoryStatuses.name', 'Distributor.name', 'ProductCategory.name', 'DistCurrentInventory.inventory_status_id','Office.office_name','DistTso.name','DistAE.name'),
            'group' => array('DistCurrentInventory.id', 'DistCurrentInventory.product_id', 'Product.name', 'Product.order', 'Product.base_measurement_unit_id', 'Product.sales_measurement_unit_id', 'Product.product_code', 'InventoryStatuses.name', 'Distributor.name', 'DistCurrentInventory.store_id', 'ProductCategory.name', 'DistCurrentInventory.inventory_status_id','Office.office_name','DistTso.name','DistAE.name','Office.order'),
            
            // 'recursive'=>1
            
            
            
            // 'order' => array('Office.order asc','Product.order asc')
            // 'recursive'=>1
        );
        
        $currentInventories = $this->paginate();
        // echo $this->DistCurrentInventory->getLastQuery();
        // exit;
        
        // pr($currentInventories);die();
        // echo '<pre>';
        // print_r($currentInventories);
        // echo '</pre>';exit;
        $this->loadModel('MeasurementUnit');
        $measurement_unit_list = $this->MeasurementUnit->find('list', array('fields' => 'name'));

        $this->set(compact('measurement_unit_list', 'currentInventories'));

        $market_id = isset($this->request->data['UserDoctorVisitPlanList']['market_id']) != '' ? $this->request->data['UserDoctorVisitPlanList']['market_id'] : 0;


        if (isset($this->request->data['DistCurrentInventory']['product_categories_id']) != '') {
            $products = $this->DistCurrentInventory->Product->find('list', array(
                'conditions' => array('Product.product_category_id' => $this->request->data['DistCurrentInventory']['product_categories_id']),
                'order' => array('Product.order' => 'ASC'),
                'recursive' => -1
            ));
        } else {
            $products = array();
        }

        if (isset($this->request->data['DistCurrentInventory']['category_summary'])) {
            $summaryCategoryList = $this->ProductCategory->find('all', array(
                //'order' => array('Product.order'=>'asc'),
                'recursive' => -1
                    )
            );

            $category_summary = $this->request->data['DistCurrentInventory']['category_summary'];
        } else {
            $category_summary = false;
            $summaryCategoryList = '';
        }
        $this->set(compact('category_summary', 'summaryCategoryList'));

        $productCategories = $this->ProductCategory->find('list');
        
        $distStore_data = $this->DistStore->find('all', array(
            'conditions' => $storeCondition,
            'fields' => array('DistStore.id', 'DistStore.name', 'DistDistributor.id'),
            //'order' => array('Store.name' => 'asc'),
            'recursive' => 0)
        );

        $inventoryStatuses = $this->InventoryStatuses->find('list', array('conditions' => array('InventoryStatuses.id !=' => 2)));
        if (isset($this->request['data']['DistCurrentInventory']['store_id'])) {
            $StoreId = $this->request['data']['DistCurrentInventory']['store_id'];
        } else {
            //$StoreId = $this->UserAuth->getStoreId();
            $StoreId = 0;
        }
        
        if(!$office_id)    
        {
            $distStores=array();
        }
       
      
        $this->set(compact('distStores', 'inventoryStatuses', 'products', 'productCategories', 'StoreId','distStore_data','office_parent_id','offices','office_id'));
    }

    public function admin_index_so($store_id = 33) {
        /* $this->set('page_title','Current Inventories');
          pr($this->UserAuth->getStoreId());
          exit; */
        $this->CurrentInventory->recursive = 1;
        $this->loadModel('Store');
        $this->loadModel('InventoryStatuses');
        $this->loadModel('ProductCategory');
        $office_parent_id = $this->UserAuth->getOfficeParentId();

        if ($office_parent_id == 0) {
            $conditions = array('CurrentInventory.inventory_status_id' => 1);
            $storeCondition = array();
        } else {
            $conditions = array('CurrentInventory.store_id' => $store_id, 'CurrentInventory.inventory_status_id' => 1); //$this->UserAuth->getStoreId()
            $storeCondition = array('Store.office_id' => $this->UserAuth->getOfficeId());
        }

        //pr($this->UserAuth->getOfficeParentId());
        //exit;
        $this->paginate = array(
            //'conditions' => array('CurrentInventory.store_id'=>$this->UserAuth->getStoreId()),
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'product_categories',
                    'alias' => 'ProductCategory',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Product.product_category_id = ProductCategory.id'
                    )
                )
            ),
            'fields' => array('CurrentInventory.product_id', 'CurrentInventory.store_id', 'SUM(CurrentInventory.qty) AS total', 'Product.name', 'Product.product_code', 'InventoryStatuses.name', 'Store.name', 'ProductCategory.name'),
            'group' => array('CurrentInventory.product_id', 'Product.name', 'Product.product_code', 'InventoryStatuses.name', 'Store.name', 'CurrentInventory.store_id', 'ProductCategory.name'),
                //'order' => array('CurrentInventory.id' => 'DESC')
        );

        /* pr($this->paginate());
          exit; */
        //$store_conditions = array('conditions'=>array('Store.office_id' => $this->UserAuth->getOfficeId()),'order' => array('Store.name' => 'ASC'));
        $this->set('currentInventories', $this->paginate());
        $products = $this->CurrentInventory->Product->find('list');
        $productCategories = $this->ProductCategory->find('list');
        $stores = $this->Store->find('list', array('conditions' => $storeCondition));
        $inventoryStatuses = $this->InventoryStatuses->find('list');
        $this->set(compact('stores', 'inventoryStatuses', 'products', 'productCategories'));
        //	$r=$this->CurrentInventory->find ('all',array('conditions'=>array('CurrentInventory.store_id'=>33),'fields'=>array('CurrentInventory.product_id','SUM(CurrentInventory.qty) AS total','Product.name'),'group'=>array('CurrentInventory.product_id','Product.name'))) ;
    }

    /*
     * view details method
     */

    public function admin_viewDetails($product_id = null, $store_id = null, $inventory_status_id = null) {
        $this->set('page_title', 'Distributor Current Inventories');


        $this->DistCurrentInventory->recursive = 1;
        $office_parent_id = $this->UserAuth->getOfficeParentId();

        //$productInfo=$this->Product->find('all',array('conditions'=>array('Product.id'=>$this->request->data['CurrentInventory']['product_id']),'recursive'=>-1,'fields'=>array('Product.id','Product.name','Product.sales_measurement_unit_id')));


        if ($office_parent_id == 0) {
            $this->paginate = array(
                'conditions' => array('DistCurrentInventory.store_id' => $store_id, 'DistCurrentInventory.product_id' => $product_id, 'DistCurrentInventory.inventory_status_id' => $inventory_status_id),
                'fields' => array('DistCurrentInventory.id', 'DistCurrentInventory.product_id', 'DistCurrentInventory.batch_number', 'DistCurrentInventory.expire_date', 'DistCurrentInventory.qty', 'InventoryStatuses.name', 'DistStore.name', 'Product.name', 'Product.product_code', 'Product.sales_measurement_unit_id'),
                'order' => array('DistCurrentInventory.id' => 'DESC')
            );
            $store_conditions = array('order' => array('DistStore.name' => 'ASC'));
        } else {
            $this->paginate = array(//$this->UserAuth->getStoreId()
                'conditions' => array('DistCurrentInventory.store_id' => $store_id, 'DistCurrentInventory.product_id' => $product_id, 'DistCurrentInventory.inventory_status_id' => $inventory_status_id),
                'fields' => array('DistCurrentInventory.id', 'DistCurrentInventory.product_id', 'DistCurrentInventory.batch_number', 'DistCurrentInventory.expire_date', 'DistCurrentInventory.qty', 'InventoryStatuses.name', 'DistStore.name', 'Product.name', 'Product.product_code', 'Product.sales_measurement_unit_id'),
                'order' => array('DistCurrentInventory.id' => 'DESC')
            );
            $store_conditions = array('conditions' => array('DistStore.office_id' => $this->UserAuth->getOfficeId()), 'order' => array('DistStore.name' => 'ASC'));
        }
        $currentInventories1 = $this->paginate();
        foreach ($currentInventories1 as $currentInventory) {
            $currentInventory['DistCurrentInventory']['sale_unit_qty'] = $this->unit_convertfrombase($currentInventory['DistCurrentInventory']['product_id'], $currentInventory['Product']['sales_measurement_unit_id'], $currentInventory['DistCurrentInventory']['qty']);
            $currentInventories[] = $currentInventory;
        }

        //$this->set('currentInventories', $this->paginate());
        $stores = $this->DistStore->find('list', $store_conditions);
        $this->set(compact('stores', 'office_parent_id', 'currentInventories'));
    }

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_view($id = null) {
        if (!$this->DistCurrentInventory->exists($id)) {
            throw new NotFoundException(__('Invalid Distributor current inventory'));
        }
        $options = array('conditions' => array('DistCurrentInventory.' . $this->DistCurrentInventory->primaryKey => $id));
        $this->set('DistCurrentInventory', $this->DistCurrentInventory->find('first', $options));
    }

    public function admin_add() {
        $this->loadModel('DistStore');
        $this->loadModel('InventoryStatuses');
        $office_parent_id = $this->UserAuth->getOfficeParentId();

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_conditions = array('Office.office_type_id' => 2);
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }

        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

        if ($this->request->is('post')) {
            //pr($this->request->data);die();
            if (empty($this->request->data['product_id'])) {
                $this->Session->setFlash(__('CurrentInventory not created.'), 'flash/error');
                $this->redirect(array('action' => 'index'));
            } else {
            	// pr($this->request->data);exit;
                $data = array();
                $distributor_id=$this->request->data['DistCurrentInventory']['distributor_id'];
                $this->loadModel('DistStore');
                $store_id_arr = $this->DistStore->find('first', array(
                	'conditions' => array(
                		'DistStore.dist_distributor_id' => $distributor_id
                		),
                	'recursive'=>-1
                	));
                $store_id = $store_id_arr['DistStore']['id'];

                $data['DistCurrentInventory']['store_id'] = $store_id;
                $data['DistCurrentInventory']['updated_at'] = $this->current_datetime();
                $data['DistCurrentInventory']['inventory_status_id'] = $this->request->data['DistCurrentInventory']['inventory_status_id'];
                $data['DistCurrentInventory']['transaction_type_id'] = 12; //  Opening Balance	
                if (!empty($this->request->data['product_id'])) {
                    $data_array = array();
                    
                    $inventory_status_id = $this->request->data['DistCurrentInventory']['inventory_status_id'];
                    foreach ($this->request->data['product_id'] as $key => $val) {

                        $product_id = $val;
                       /* $batch_number = $this->request->data['batch_no'][$key];
                        $expire_date = $this->request->data['expire_date'][$key];*/
                        $conditions = array();

                        $conditions[] = array('DistCurrentInventory.product_id' => $product_id);
                        // $conditions[] = array('CurrentInventory.batch_number' => $batch_number);

                        $conditions[] = array('DistCurrentInventory.inventory_status_id' => $inventory_status_id);
                        $conditions[] = array('DistCurrentInventory.store_id' => $store_id);

                        $this->request->data['quantity_log'][$key] = $this->request->data['quantity'][$key];
                        // $this->request->data['bonus_quantity_log'][$key] = $this->request->data['bonus_quantity'][$key];
                        $this->request->data['bonus_quantity_log'][$key] = 0;
                        
                        $inventory_info = $this->DistCurrentInventory->find('first', array('conditions' => $conditions));
                        if (!empty($inventory_info)) 
                        {
                            $data['DistCurrentInventory']['id'] = $inventory_info['DistCurrentInventory']['id'];
                            $this->request->data['quantity'][$key] = $this->request->data['quantity'][$key] + $inventory_info['DistCurrentInventory']['qty'];
                            // $this->request->data['bonus_quantity'][$key] = $this->request->data['bonus_quantity'][$key] + $inventory_info['DistCurrentInventory']['bonus_qty'];
                            $this->request->data['bonus_quantity'][$key] = 0;
                        }
                        //pr($inventory_info);
                        $data['DistCurrentInventory']['product_id'] = $val;
                        $data['DistCurrentInventory']['qty'] = $this->request->data['quantity'][$key];
                        // $data['DistCurrentInventory']['bonus_qty'] = $this->request->data['bonus_quantity'][$key];
                        $data['DistCurrentInventory']['bonus_qty'] = 0;
                       
                        
                        $data['DistCurrentInventory']['transaction_date'] = $this->current_date();
                        $data['DistCurrentInventory']['transaction_type_id'] = 12;

                        $data_array[] = $data;
                    }
                    //pr($data_array);die();
                    if ($this->DistCurrentInventory->saveAll($data_array)) {

                        $this->loadModel('DistcurrentInventoryBalanceLog');
                        $data = array();
                        $data['DistcurrentInventoryBalanceLog']['store_id'] = $store_id;
                        $data['DistcurrentInventoryBalanceLog']['created_by'] = $this->UserAuth->getUserId();
                        $data['DistcurrentInventoryBalanceLog']['created_at'] = $this->current_datetime();
                        $data['DistcurrentInventoryBalanceLog']['inventory_status_id'] =$inventory_status_id;

                        $data['DistcurrentInventoryBalanceLog']['transaction_date'] = $this->current_date();
                        $data['DistcurrentInventoryBalanceLog']['transaction_type_id'] = 12;

                        $data_array = array();
                        foreach ($this->request->data['product_id'] as $key => $val) {
                            $data['DistcurrentInventoryBalanceLog']['product_id'] = $val;
                            $data['DistcurrentInventoryBalanceLog']['qty'] = $this->request->data['quantity_log'][$key];
                            $data['DistcurrentInventoryBalanceLog']['bonus_qty'] = $this->request->data['bonus_quantity_log'][$key];
                           /* $data['currentInventoryBalanceLog']['batch_no'] = $this->request->data['batch_no'][$key];
                            $data['currentInventoryBalanceLog']['exp_date'] = (trim($this->request->data['expire_date'][$key]) != '') ? (date('Y-m-d', strtotime($this->request->data['expire_date'][$key]))) : "";
                            */
                           $data_array[] = $data;
                        }
                        //pr($data_array);die();
                        $this->DistcurrentInventoryBalanceLog->saveAll($data_array);
                    }


                    $this->Session->setFlash(__('Current Inventory has been created.'), 'flash/success');
                    $this->redirect(array('action' => 'index'));
                }
            }
        }

        $inventoryStatuses = $this->InventoryStatuses->find('list',array('conditions'=>array('InventoryStatuses.id'=>1)));
        $productTypes = $this->ProductType->find('list', array('order' => 'id'));
        $this->set(compact('offices', 'inventoryStatuses','office_parent_id','productTypes'));
    }

    /**
     * admin_edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_edit($id = null) {
        $this->CurrentInventory->id = $id;
        if (!$this->CurrentInventory->exists($id)) {
            throw new NotFoundException(__('Invalid current inventory'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->CurrentInventory->save($this->request->data)) {
                $this->flash(__('The current inventory has been saved.'), array('action' => 'index'));
            } else {
                
            }
        } else {
            $options = array('conditions' => array('CurrentInventory.' . $this->CurrentInventory->primaryKey => $id));
            $this->request->data = $this->CurrentInventory->find('first', $options);
        }
        $inventoryStores = $this->CurrentInventory->InventoryStore->find('list');
        $inventoryStatuses = $this->CurrentInventory->InventoryStatus->find('list');
        $products = $this->CurrentInventory->Product->find('list');
        $batches = $this->CurrentInventory->Batch->find('list');
        $this->set(compact('inventoryStores', 'inventoryStatuses', 'products', 'batches'));
    }

    /**
     * admin_delete method
     *
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @param string $id
     * @return void
     */
    public function admin_delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->CurrentInventory->id = $id;
        if (!$this->CurrentInventory->exists()) {
            throw new NotFoundException(__('Invalid current inventory'));
        }
        if ($this->CurrentInventory->delete()) {
            $this->flash(__('Current inventory deleted'), array('action' => 'index'));
        }
        $this->flash(__('Current inventory was not deleted'), array('action' => 'index'));
        $this->redirect(array('action' => 'index'));
    }

    /* ----------------------- Chainbox Data --------------------------- */

    public function get_batch_list() {
        $rs = array(array('id' => '', 'title' => '---- Select Batch -----'));
        $product_id = $this->request->data['product_id'];
        $inventory_status_id = $this->request->data['inventory_status_id'];
        if (isset($this->request->data['with_stock'])) {
            $with_stock = $this->request->data['with_stock'];
            $conditions[] = array('CurrentInventory.qty >' => 0);
        } else
            $with_stock = false;



        if (isset($this->request->data['transaction_type_id'])) {
            $conditions[] = array('CurrentInventory.inventory_status_id' => $inventory_status_id);
            $conditions[] = array('CurrentInventory.transaction_type_id' => $this->request->data['transaction_type_id']);
            $conditions[] = array('CurrentInventory.product_id' => $product_id);
            $conditions[] = array('CurrentInventory.store_id' => $this->UserAuth->getStoreId());
        } else {
            $conditions[] = array('CurrentInventory.inventory_status_id' => $inventory_status_id);
            $conditions[] = array('CurrentInventory.product_id' => $product_id);
            $conditions[] = array('CurrentInventory.store_id' => $this->UserAuth->getStoreId());
        }

        //$product_id = 12;
        $batch_list = $this->CurrentInventory->find('all', array(
            'fields' => array('CurrentInventory.batch_number as id', 'CurrentInventory.batch_number as title'),
            'conditions' => $conditions,
            'group' => array('CurrentInventory.batch_number'),
            'recursive' => -1
        ));
        /* foreach($batch_list as ){

          } */

        $data_array = Set::extract($batch_list, '{n}.0');
        /* echo "<pre>";
          print_r($batch_list);
          print_r($data_array);
          exit; */
        if (!empty($batch_list)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    // batch list by status
    public function get_batch_list_status() {
        $rs = array(array('id' => '', 'title' => '---- Select Batch -----'));
        $product_id = $this->request->data['product_id'];
        $inventory_status_id = $this->request->data['inventory_status_id'];
        $batch_list = $this->CurrentInventory->find('all', array(
            'fields' => array('DISTINCT CurrentInventory.batch_number as id', 'CurrentInventory.batch_number as title'),
            'conditions' => array(
                'CurrentInventory.inventory_status_id' => $inventory_status_id,
                'CurrentInventory.product_id' => $product_id,
                'CurrentInventory.store_id' => $this->UserAuth->getStoreId()
            ),
            //'group' => array('CurrentInventory.batch_number'),
            'recursive' => -1
        ));
        $data_array = Set::extract($batch_list, '{n}.0');
        if (!empty($batch_list)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function get_expire_date_list() {
        $rs = array(array('id' => '', 'title' => '---- Select Expired Date -----'));
        $product_id = $this->request->data['product_id'];
        //$product_id = 11;
        $batch_no = $this->request->data['batch_no'];
        //$batch_no = 'T242';
        $inventory_status_id = $this->request->data['inventory_status_id'];
        if (isset($this->request->data['with_stock'])) {
            $with_stock = $this->request->data['with_stock'];
            $conditions[] = array('CurrentInventory.qty >' => 0);
        } else
            $with_stock = false;

        if (isset($this->request->data['transaction_type_id'])) {
            $conditions[] = array('CurrentInventory.inventory_status_id' => $inventory_status_id);
            $conditions[] = array('CurrentInventory.transaction_type_id' => $this->request->data['transaction_type_id']);
            $conditions[] = array('CurrentInventory.product_id' => $product_id);
            $conditions[] = array('CurrentInventory.batch_number' => $batch_no);
            $conditions[] = array('CurrentInventory.store_id' => $this->UserAuth->getStoreId());
        } else {
            $conditions[] = array('CurrentInventory.inventory_status_id' => $inventory_status_id);
            $conditions[] = array('CurrentInventory.product_id' => $product_id);
            $conditions[] = array('CurrentInventory.batch_number' => $batch_no);
            $conditions[] = array('CurrentInventory.store_id' => $this->UserAuth->getStoreId());
        }
        $exp_date_list = $this->CurrentInventory->find('all', array(
            'fields' => array('CurrentInventory.expire_date as id', 'CurrentInventory.expire_date as title'),
            'conditions' => $conditions,
            'recursive' => -1
        ));
        $i = 0;
        foreach ($exp_date_list as $data) {
            $data_array[] = array('id' => $data[$i]['id'], 'title' => date("M-y", strtotime($data[$i]['title'])));
        }
        //$data_array = Set::extract($exp_date_list, '{n}.0');
        if (!empty($exp_date_list)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function get_inventory_details() {
        $product_id = $this->request->data['product_id'];

        $conditions_options['CurrentInventory.store_id'] = $this->UserAuth->getStoreId();
        $conditions_options['CurrentInventory.product_id'] = $product_id;
        if ($this->request->data['batch_no'] && $this->request->data['expire_date']) {
            $conditions_options['CurrentInventory.batch_number'] = ($this->request->data['batch_no'] ? $this->request->data['batch_no'] : NULL );


            $conditions_options['CurrentInventory.expire_date'] = (!empty($this->request->data['expire_date']) ? $this->request->data['expire_date'] : NULL );
        }

        if (!empty($this->request->data['transaction_type_id'])) {
            $conditions_options['CurrentInventory.transaction_type_id'] = $this->request->data['transaction_type_id'];
        }
        if (!empty($this->request->data['inventory_status_id'])) {
            $conditions_options['CurrentInventory.inventory_status_id'] = $this->request->data['inventory_status_id'];
        }

        $batch_info = $this->CurrentInventory->find('first', array(
            'fields' => array('CurrentInventory.qty', 'Product.challan_measurement_unit_id'),
            'conditions' => array($conditions_options),
            'recursive' => 0
        ));

        if (!empty($batch_info)) {
            echo $this->unit_convertfrombase($product_id, $batch_info['Product']['challan_measurement_unit_id'], $batch_info['CurrentInventory']['qty']);
        } else {
            echo '';
        }
        $this->autoRender = false;
    }

    public function get_inventory_details_in_Return_challan() {
        $product_id = $this->request->data['product_id'];
        //$product_id = 12;
        $batch_no = $this->request->data['batch_no'];
        //$batch_no = 12000;
        //$expire_date = ($this->request->data['expire_date'] !='' ? $this->request->data['expire_date'] : '0000-00-00');
        if (!empty($this->request->data['expire_date'])) {
            $conditions_options['CurrentInventory.expire_date'] = $this->request->data['expire_date'];
        }
        if (!empty($this->request->data['transaction_type_id'])) {
            $conditions_options['CurrentInventory.transaction_type_id'] = $this->request->data['transaction_type_id'];
        }
        $conditions_options['CurrentInventory.product_id'] = $product_id;
        $conditions_options['CurrentInventory.batch_number'] = $batch_no;
        $conditions_options['CurrentInventory.inventory_status_id'] = $this->request->data['inventory_status_id'];
        $conditions_options['CurrentInventory.store_id'] = $this->UserAuth->getStoreId();
        $batch_info = $this->CurrentInventory->find('first', array(
            'fields' => array('CurrentInventory.qty', 'Product.return_measurement_unit_id'),
            'conditions' => array($conditions_options),
            'recursive' => 0
        ));
        //echo $batch_info['CurrentInventory']['qty'];
        if (!empty($batch_info)) {
            echo $this->unit_convertfrombase($product_id, $batch_info['Product']['return_measurement_unit_id'], $batch_info['CurrentInventory']['qty']);
        } else {
            echo '';
        }
        $this->autoRender = false;
    }

    public function get_inventory_details_in_NCP_Return_challan() {
        $product_id = $this->request->data['product_id'];
        //$product_id = 12;
        $batch_no = $this->request->data['batch_no'];
        //$batch_no = 12000;
        //$expire_date = ($this->request->data['expire_date'] !='' ? $this->request->data['expire_date'] : '0000-00-00');
        if (!empty($this->request->data['expire_date'])) {
            $conditions_options['CurrentInventory.expire_date'] = $this->request->data['expire_date'];
        }
        if (!empty($this->request->data['transaction_type_id'])) {
            $conditions_options['CurrentInventory.transaction_type_id'] = $this->request->data['transaction_type_id'];
        }
        $conditions_options['CurrentInventory.product_id'] = $product_id;
        $conditions_options['CurrentInventory.batch_number'] = $batch_no;
        $conditions_options['CurrentInventory.inventory_status_id'] = $this->request->data['inventory_status_id'];
        $conditions_options['CurrentInventory.store_id'] = $this->UserAuth->getStoreId();
        $batch_info = $this->CurrentInventory->find('first', array(
            'fields' => array('CurrentInventory.qty', 'Product.return_measurement_unit_id'),
            'conditions' => array($conditions_options),
            'recursive' => 0
        ));
        if (!empty($batch_info)) {
            echo $batch_info['CurrentInventory']['qty'];
        } else {
            echo '';
        }
        $this->autoRender = false;
    }

    public function get_inventory_status_list() {
        $rs = array(array('id' => '', 'title' => '---- Select Inventory Status -----'));
        $product_id = $this->request->data['product_id'];
        //$product_id = 12;
        $status_list = $this->CurrentInventory->find('all', array(
            'fields' => array('DISTINCT  InventoryStatuses.id as id', 'InventoryStatuses.name as title'),
            'conditions' => array(
                'CurrentInventory.product_id' => $product_id,
                'CurrentInventory.store_id' => $this->UserAuth->getStoreId()
            ),
            //'group' => array('CurrentInventory.inventory_status_id'),
            'recursive' => 0
        ));


        $data_array = Set::extract($status_list, '{n}.0');

        /* echo "<pre>";
          print_r($data_array);
          exit; */
        if (!empty($status_list)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function get_inventory_status_by_inv_id() {
        $cur_inv_id = $this->request->data['inv_id'];
        $type_id = $this->request->data['types'];
        $cur_inv = array(2, $cur_inv_id);

        $this->LoadModel('CurrentInventory');
        $this->CurrentInventory->Behaviors->load('Containable');
        $cur_inv_product = $this->CurrentInventory->find('all', array(
            'conditions' => array('CurrentInventory.store_id' => $this->UserAuth->getStoreId(), 'CurrentInventory.inventory_status_id' => $cur_inv_id, 'Product.product_type_id' => $type_id),
            'fields' => array('CurrentInventory.product_id', 'CurrentInventory.batch_number', 'CurrentInventory.expire_date', 'CurrentInventory.qty', 'CurrentInventory.inventory_status_id'),
            'contain' => array('InventoryStatuses.name', 'Store.name', 'Product.name', 'Product.product_code', 'Product.ProductType.name'),
            'order' => array('Product.order' => 'ASC'),
        ));
        $data_array = array();
        //$fromProducts=array(''=>'--- Select Product ---');
        //$inventory_status= array(''=>'--- Select Status ---');

        if ($cur_inv_product) {

            foreach ($cur_inv_product as $invProduct) {
                $fromProducts[$invProduct['CurrentInventory']['product_id']] = $invProduct['Product']['name'];
            }
            $data_array[1] = $fromProducts;
            $this->LoadModel('InventoryStatus');
            $inventory_status = $this->InventoryStatus->find('list', array('conditions' => array('NOT' => array('InventoryStatus.id' => $cur_inv))));
            $data_array[0] = $inventory_status;
        } else {
            $fromProducts = array();
            $inventory_status = array();
            $data_array[1] = $fromProducts;
            $data_array[0] = $inventory_status;
        }

        echo json_encode($data_array);
        $this->autoRender = false;
    }

    public function get_product_Info_by_inv_id() {
        $cur_inv_id = $this->request->data['inv_id'];
        $this->CurrentInventory->Behaviors->load('Containable');


        $product_info = $this->CurrentInventory->find('all', array(
            'conditions' => array('CurrentInventory.store_id' => $this->UserAuth->getStoreId(), 'CurrentInventory.id' => $cur_inv_id),
            'fields' => array('CurrentInventory.id', 'CurrentInventory.qty'),
            'contain' => array('Product.product_type_id', 'Product.ProductType.name', 'Product.base_measurement_unit_id', 'Product.product_category_id'),
            'order' => array('CurrentInventory.id' => 'DESC'),
        ));


        //$data_array = Set::extract($status_list, '{n}.0');
        $data_array[0] = Set::combine($product_info, '{n}.Product.product_type_id', '{n}.Product.ProductType.name');
        $data_array['base_measurement_unit_id'] = $product_info[0]['Product']['base_measurement_unit_id'];
        $data_array['product_category_id'] = $product_info[0]['Product']['product_category_id'];
        $data_array['qty'] = $product_info[0]['CurrentInventory']['qty'];
        $data_array[0] = Set::combine($product_info, '{n}.Product.product_type_id', '{n}.Product.ProductType.name');


        echo json_encode($data_array);
        $this->autoRender = false;
    }

    public function get_product_Info_by_inv_id_back() {
        $cur_inv_id = $this->request->data['inv_id'];
        $this->CurrentInventory->Behaviors->load('Containable');


        $product_info = $this->CurrentInventory->find('all', array(
            'conditions' => array('CurrentInventory.store_id' => $this->UserAuth->getStoreId(), 'CurrentInventory.id' => $cur_inv_id),
            'fields' => array('CurrentInventory.id', 'CurrentInventory.qty'),
            'contain' => array('Product.product_type_id', 'Product.ProductType.name'),
            'order' => array('CurrentInventory.id' => 'DESC'),
        ));


        //$data_array = Set::extract($status_list, '{n}.0');
        $data_array = Set::combine($product_info, '{n}.Product.product_type_id', '{n}.Product.ProductType.name');

        echo json_encode($data_array);
        $this->autoRender = false;
    }

    //only temporary for so

    public function get_batch_list_so() {
        $rs = array(array('id' => '', 'title' => '---- Select Batch -----'));
        $product_id = $this->request->data['product_id'];
        $inventory_status_id = $this->request->data['inventory_status_id'];
        //$product_id = 12;
        $batch_list = $this->CurrentInventory->find('all', array(
            'fields' => array('CurrentInventory.batch_number as id', 'CurrentInventory.batch_number as title'),
            'conditions' => array(
                'CurrentInventory.inventory_status_id' => $inventory_status_id,
                'CurrentInventory.product_id' => $product_id,
                'CurrentInventory.store_id' => $this->UserAuth->getStoreId()
            ),
            'group' => array('CurrentInventory.batch_number'),
            'recursive' => -1
        ));
        /* foreach($batch_list as ){

          } */

        $data_array = Set::extract($batch_list, '{n}.0');
        /* echo "<pre>";
          print_r($batch_list);
          print_r($data_array);
          exit; */
        if (!empty($batch_list)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function get_expire_date_list_so() {
        $rs = array(array('id' => '', 'title' => '---- Select Batch -----'));
        $product_id = $this->request->data['product_id'];
        //$product_id = 11;
        $batch_no = $this->request->data['batch_no'];
        //$batch_no = 'T242';
        $inventory_status_id = $this->request->data['inventory_status_id'];
        $batch_list = $this->CurrentInventory->find('all', array(
            'fields' => array('CurrentInventory.expire_date as id', 'CurrentInventory.expire_date as title'),
            'conditions' => array(
                'CurrentInventory.inventory_status_id' => $inventory_status_id,
                'CurrentInventory.product_id' => $product_id,
                'CurrentInventory.batch_number' => $batch_no,
                'CurrentInventory.store_id' => $this->UserAuth->getStoreId()
            ),
            'recursive' => -1
        ));
        $data_array = Set::extract($batch_list, '{n}.0');
        if (!empty($batch_list)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function get_inventory_details_so() {
        $product_id = $this->request->data['product_id'];
        //$product_id = 12;
        $batch_no = $this->request->data['batch_no'];
        //$batch_no = 12000;
        //$expire_date = ($this->request->data['expire_date'] !='' ? $this->request->data['expire_date'] : '0000-00-00');
        if (!empty($this->request->data['expire_date'])) {
            $conditions_options['CurrentInventory.expire_date'] = $this->request->data['expire_date'];
        }
        $conditions_options['CurrentInventory.product_id'] = $product_id;
        $conditions_options['CurrentInventory.batch_number'] = $batch_no;
        $conditions_options['CurrentInventory.store_id'] = $this->UserAuth->getStoreId();
        $batch_info = $this->CurrentInventory->find('first', array(
            'fields' => array('CurrentInventory.qty', 'Product.challan_measurement_unit_id'),
            'conditions' => array($conditions_options),
            'recursive' => 0
        ));
        if (!empty($batch_info)) {
            echo $this->unit_convertfrombase($product_id, $batch_info['Product']['challan_measurement_unit_id'], $batch_info['CurrentInventory']['qty']);
        } else {
            echo '';
        }
        $this->autoRender = false;
    }

    public function get_product_list() {
        //$this->loadModel('Product');

        $product_category_id = $this->request->data['product_category_id'];

        $rs = array(array('id' => '', 'name' => '---- Select -----'));

        $products = $this->CurrentInventory->Product->find('all', array(
            'conditions' => array('Product.product_category_id' => $product_category_id),
            'order' => array('Product.order' => 'ASC'),
            'recursive' => -1
        ));

        //pr($products);

        $data_array = Set::extract($products, '{n}.Product');

        if (!empty($products)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }

        $this->autoRender = false;
    }

    public function admin_getCategoryQtyTotal($category_id = 20) {
        $category_summary = false;
        $conditions = array('DistCurrentInventory.inventory_status_id !=' => 2, 'Product.product_category_id =' => $category_id);

        $summary_list = $this->DistCurrentInventory->find('all', array(
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'product_categories',
                    'alias' => 'ProductCategory',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Product.product_category_id = ProductCategory.id'
                    )
                )
            ),
            'fields' => array('DistCurrentInventory.product_id', 'DistCurrentInventory.store_id', 'SUM(DistCurrentInventory.qty) AS total', 'Product.name', 'Product.product_category_id', 'Product.base_measurement_unit_id', 'Product.product_code', 'InventoryStatuses.name', 'DistStore.name', 'ProductCategory.name', 'DistCurrentInventory.inventory_status_id'),
            'group' => array('DistCurrentInventory.product_id', 'Product.name', 'Product.product_category_id', 'Product.order', 'Product.base_measurement_unit_id', 'Product.product_code', 'InventoryStatuses.name', 'DistStore.name', 'DistCurrentInventory.store_id', 'ProductCategory.name', 'DistCurrentInventory.inventory_status_id'),
            'order' => array('Product.order' => 'ASC'),
            'recursive' => 0
                )
        );

        $qty_total = 0;
        foreach ($summary_list as $result) {
            if ($result['Product']['product_category_id'] == $category_id) {
                $qty_total += $result[0]['total'];
            }
        }
        return $qty_total;
    }

    public function getSOName($territory_id = 0) {
        if ($territory_id) {
            $this->loadModel('Territory');
            $territory_info = $this->Territory->find('first', array(
                'conditions' => array('Territory.id' => $territory_id),
                'fields' => array('SalesPerson.name'),
                'recursive' => 0
                    )
            );
            //pr($territory_info);
            //exit;
            if ($territory_info['SalesPerson']['name']) {
                return $territory_info['SalesPerson']['name'];
            } else {
                return 'NA';
            }
        } else {
            return 'NA';
        }
    }
    
    
    public function get_diststore_list() {
        $office_id = $this->request->data['office_id'];
        
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistAreaExecutive');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        $rs = array(array('id' => '', 'name' => '---- Select Office -----'));

        if($user_group_id == 1029 || $user_group_id == 1028){
            if($user_group_id == 1028){
                $dist_ae_info = $this->DistAreaExecutive->find('first',array(
                    'conditions'=>array('DistAreaExecutive.user_id'=>$user_id),
                    'recursive'=> -1,
                ));
                $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
                $dist_tso_info = $this->DistTso->find('list',array(
                    'conditions'=>array('dist_area_executive_id'=>$dist_ae_id),
                    'fields'=> array('DistTso.id','DistTso.dist_area_executive_id'),
                ));
                
                $dist_tso_id = array_keys($dist_tso_info);
            }
            else{
                $dist_tso_info = $this->DistTso->find('first',array(
                    'conditions'=>array('DistTso.user_id'=>$user_id),
                    'recursive'=> -1,
                ));
                $dist_tso_id = $dist_tso_info['DistTso']['id'];
            }
           
            $tso_dist_list = $this->DistTsoMapping->find('list',array(
                'conditions'=> array(
                    'dist_tso_id' => $dist_tso_id,
                ),
                'fields'=>array('DistTsoMapping.dist_distributor_id','DistTsoMapping.dist_tso_id'),
            ));
           //$dist_conditions = array('DistDistributor.id'=>array_keys($tso_dist_list));
           $storeCondition = array('DistStore.dist_distributor_id'=>array_keys($tso_dist_list));
           //$conditions = array('DistCurrentInventory.inventory_status_id !=' => 2);
        }
        elseif($user_group_id == 1034){
            $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
            $this->loadModel('DistUserMapping');
            $distributor = $this->DistUserMapping->find('first',array(
                'conditions'=>array('DistUserMapping.sales_person_id'=>$sales_people_id),
            ));
            $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
    
            $storeCondition = array('DistStore.dist_distributor_id'=>$distributor_id);
        }else{
            $storeCondition = array('DistStore.office_id'=>$office_id);
        }

        $this->DistCurrentInventory->DistStore->virtualFields = array(
            "name" => "Distributor.name"
        );
		
		$storeCondition['Distributor.is_active'] = 1;
		
        $distStores = $this->DistCurrentInventory->DistStore->find('all', array(
            'conditions' => $storeCondition,
            'joins'=>array(
                array(
                    'table'=>'dist_distributors',
                    'alias'=>'Distributor',
                    'conditions'=>'Distributor.id=DistStore.dist_distributor_id'
                    ),
                ),
            'order' => array('Distributor.name' => 'ASC'),
            'recursive' => -1
        ));

        $data_array = Set::extract($distStores, '{n}.DistStore');

        if (!empty($distStores)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
       
        $this->autoRender = false;
    }


    function get_dist_list_by_office_id_for_adjustment() {
        
        $office_id = $this->request->data['office_id'];
        $output = "<option value=''>--- Select Distributor ---</option>";
        
        
        $conditions=array(
                'conditions' => array('DistDistributor.office_id' => $office_id, 'DistDistributor.is_active' => 1),'order' => array('DistDistributor.name' => 'asc'),
            );

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

    public function get_product(){
        $this->loadModel('Product');
        $rs = array(array('id' => '', 'name' => '---- Select -----'));
        $type_id = $this->request->data['product_type_id'];
        if($type_id==''){
            $rs = array(array('id' => '', 'name' => '---- Select -----'));
        }
        else{
        $product = $this->Product->find('all', array(
            'conditions' => array('Product.product_type_id' => $type_id,'Product.is_distributor_product'=>1),
			'order' => array('Product.order' => 'ASC'),
            'recursive' => -1
        ));
        //pr($months);
        $data_array = Set::extract($product, '{n}.Product');
        if(!empty($product)){
            echo json_encode(array_merge($rs,$data_array));
        }else{
            echo json_encode($rs);
        } }
        $this->autoRender = false;
    }

    function get_db_latest_buying_price_from_challan($store_id=0,$product_id=0)
    {
		$this->loadModel('Product');

		$product_details = $this->Product->find('first', array(
			'fields' => array('id', 'is_virtual', 'parent_id'),
			'conditions' => array('Product.id' => $product_id),
			'recursive' => -1
		));

		if($product_details['Product']['is_virtual'] == 1){
			$con = array(
				'DistChallan.receiver_dist_store_id'=>$store_id,
                'DistChallanDetail.virtual_product_id'=>$product_id,
                'DistChallanDetail.price >'=>0
			);
		}else{
			$con = array(
				'DistChallan.receiver_dist_store_id'=>$store_id,
                'DistChallanDetail.product_id'=>$product_id,
                'DistChallanDetail.price >'=>0
			);
		}


        $this->loadModel('DistChallan');
        $price=$this->DistChallan->find('first',array(
            'conditions'=>$con,
            'joins'=>array(
                array(
                    'alias'=>'DistChallanDetail',
                    'table'=>'dist_challan_details',
                    'conditions'=>'DistChallan.id=DistChallanDetail.challan_id'
                    ),
                ),
            'fields'=>array('DistChallanDetail.price'),
            'order'=>array('DistChallan.id DESC'),
            'recursive'=>-1
            ));
        /*echo $this->DistChallan->getLastQuery().'<br>';
        pr($price);*/
        return $price ? $price['DistChallanDetail']['price']:0;
    }

    public function search_array($value, $key, $array) {
        foreach ($array as $k => $val) {
            if ($val[$key] == $value) {
                return $array[$k];
            }
        }
        return null;
    }
	public function download_xl() {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600); //300 seconds = 5 minutes
        $params=$this->request->query['data'];
        
        $this->loadModel('InventoryStatuses');
        $this->loadModel('ProductCategory');
        
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if(CakeSession::read('Office.parent_office_id') != 0)
        {   
            $conditions[] = array('DistStore.office_id' => CakeSession::read('Office.id'));
        }
        if (!empty($params['DistCurrentInventory']['product_code'])) {
            $conditions[] = array('Product.product_code' => $params['DistCurrentInventory']['product_code']);
        }
        if (!empty($params['DistCurrentInventory']['inventory_status_id'])) {
            $conditions[] = array('DistCurrentInventory.inventory_status_id' => $params['DistCurrentInventory']['inventory_status_id']);
        }else
        {
            $conditions[] = array('DistCurrentInventory.inventory_status_id !=' => 2);
        }   
        if (!empty($params['DistCurrentInventory']['product_id'])) {
            $conditions[] = array('DistCurrentInventory.product_id' => $params['DistCurrentInventory']['product_id']);
        }
        if (!empty($params['DistCurrentInventory']['store_id'])) {
            $conditions[] = array('DistCurrentInventory.store_id' => $params['DistCurrentInventory']['store_id']);
        }
        if (!empty($params['DistCurrentInventory']['product_categories_id'])) {
            $conditions[] = array('ProductCategory.id' => $params['DistCurrentInventory']['product_categories_id']);
        }
        if (!empty($params['DistCurrentInventory']['product_type_id'])) {
            $conditions[] = array('Product.product_type_id' => $params['DistCurrentInventory']['product_type_id']);
        }
		//pr($conditions);die();
        $currentInventories = $this->DistCurrentInventory->find('all',array(
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'product_categories',
                    'alias' => 'ProductCategory',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Product.product_category_id = ProductCategory.id'
                        )
                    ),
                array(
                    'table'=>'dist_distributors',
                    'alias'=>'Distributor',
                    'conditions'=>'Distributor.id=DistStore.dist_distributor_id'
                    ),
                    array(
                        'table'=>'offices',
                        'alias'=>'Office',
                        'type' => 'INNER',
                        'conditions'=>array('Office.id=DistStore.office_id')
                    ),
                    array(
                        'table'=>'dist_tsos',
                        'alias'=>'DistTso',
                        'type' => 'INNER',
                        'conditions'=>array('DistTso.id=(
                                                          SELECT 
                                                            TOP 1 dist_tso_mappings.dist_tso_id
                                                          FROM dist_tso_mappings
                                                          WHERE dist_tso_mappings.dist_distributor_id = DistStore.dist_distributor_id order by dist_tso_mappings.id asc
                                                            )
                                                    ')
                    ),
                    array(
                        'table'=>'dist_area_executives',
                        'alias'=>'DistAE',
                        'type' => 'INNER',
                        'conditions'=>array('DistAE.id=(
                                                          SELECT 
                                                            TOP 1 dist_area_executives.id
                                                          FROM dist_area_executives
                                                          WHERE dist_area_executives.id = DistTso.dist_area_executive_id order by dist_area_executives.id asc
                                                            )
                                                    ')
                    ),
                ),
            'fields' => array('DistCurrentInventory.product_id', 'DistCurrentInventory.store_id', 'SUM(DistCurrentInventory.qty) AS total','SUM(DistCurrentInventory.bonus_qty) AS bonus_total', 'Product.name','Product.base_measurement_unit_id','Product.sales_measurement_unit_id', 'Product.product_code', 'InventoryStatuses.name', 'Distributor.name', 'ProductCategory.name','DistCurrentInventory.inventory_status_id','Office.office_name','DistTso.name','DistAE.name'),
            'group' => array('DistCurrentInventory.product_id', 'Product.name', 'Product.order', 'Product.base_measurement_unit_id','Product.sales_measurement_unit_id', 'Product.product_code', 'InventoryStatuses.name', 'Distributor.name', 'DistCurrentInventory.store_id', 'ProductCategory.name','DistCurrentInventory.inventory_status_id','Office.office_name','DistTso.name','DistAE.name','Office.order'),
            // 'order' => array('Product.order' => 'ASC'),
            'order' => array('Office.order asc','Product.order asc'),
            'recursive'=>1
            ));
        //     echo $this->DistCurrentInventory->getLastQuery();
        // exit;
        // echo '<pre>';
        // print_r($currentInventories);
        // echo '</pre>';exit;
        
        if (isset($params['DistCurrentInventory']['category_summary']) && $params['DistCurrentInventory']['category_summary']==1)
        {           

            $summaryCategoryList = $this->ProductCategory->find('all', array(
                'recursive' => -1
                )
            );
            
            $category_summary = $params['DistCurrentInventory']['category_summary'];
        }
        else
        {
            $category_summary = false;
            $summaryCategoryList = '';
        }
        $this->loadModel('MeasurementUnit');
        $measurement_unit_list = $this->MeasurementUnit->find('list', array('fields'=>'name'));
        $productCategories = $this->ProductCategory->find('list');
        $inventoryStatuses=$this->InventoryStatuses->find('list',array('conditions'=>array('InventoryStatuses.id !='=>2))); 

        // echo $StoreId;exit; 
        /*----------------- Report Xl table creation ------------------*/
        $table='';
        if($summaryCategoryList)
        {                    

            $table.='<table border="1">
            <tr>
                <th class="text-center">Category Name</th>
                <th class="text-center">Quantity</th>
            </tr>';
            foreach ($summaryCategoryList as $result): 

                if($this->admin_getCategoryQtyTotal($result['ProductCategory']['id'])>0)
                {
                    $table.='<tr>';
                    $table.='<td class="text-center">'.h($result['ProductCategory']['name']).'</td>';
                    $table.='<td class="text-center">'.$this->admin_getCategoryQtyTotal($result['ProductCategory']['id']).'</td>';
                    $table.='</tr>';
                }
                endforeach;
                $table.='</table>';
        }
        else
        {

            $table.='<table border="1">';
            $table.='<tr>';
            
            $table.='<th class="text-center">Area Office</th>
            <th class="text-center">Area Executive</th>
            <th class="text-center">TSO</th>';
            $table.='<th class="text-center">Store</th>';
            
            $table.='<th class="text-center">Product</th>
                <th class="text-center">Product Unit</th>
                <th class="text-center">Product Code</th>
                <th class="text-center">Inventory Status</th>
                <th class="text-center">Product Category</th>
                <th class="text-center">Quantity</th>
				<th class="text-center">Bonus Quantity</th>
                <th class="text-center">Quantity(Sale Unit)</th>
				<th class="text-center">Bonus Quantity(Sale Unit)</th>
            </tr>';
            foreach ($currentInventories as $currentInventory):
                $table.='<tr>';
                $table.='<td>'.h($currentInventory['Office']['office_name']).'</td>';
                $table.='<td>'.h($currentInventory['DistAE']['name']).'</td>';
                $table.='<td>'.h($currentInventory['DistTso']['name']).'</td>';
                $table.='<td>'.trim($currentInventory['Distributor']['name']).'</td>';
                $table.='<td>'.h($currentInventory['Product']['name']).'</td>';
                $table.='<td>'.h($measurement_unit_list[$currentInventory['Product']['base_measurement_unit_id']]).'</td>';
                $table.='<td class="text-center">'.h($currentInventory['Product']['product_code']).'</td>';
                $table.='<td class="text-center">'.h($currentInventory['InventoryStatuses']['name']).'</td>';
                $table.='<td class="text-center">'.h($currentInventory['ProductCategory']['name']).'</td>';
                $table.='<td class="text-center">'.h($currentInventory[0]['total']).'</td>';
				$table.='<td class="text-center">'.h($currentInventory[0]['bonus_total']).'</td>';
                $table.='<td class="text-center">'.$this->unit_convertfrombase($currentInventory['DistCurrentInventory']['product_id'],$currentInventory['Product']['sales_measurement_unit_id'],($currentInventory[0]['total'])).'</td>';
				$table.='<td class="text-center">'.$this->unit_convertfrombase($currentInventory['DistCurrentInventory']['product_id'],$currentInventory['Product']['sales_measurement_unit_id'],($currentInventory[0]['bonus_total'])).'</td>';
                $table.='</tr>';
            endforeach;
            $table.='</table>';
        }
        header('Content-Type:application/force-download');
        header('Content-Disposition: attachment; filename="inventory.xls"');
        header("Cache-Control: ");
        header("Pragma: ");
        echo $table;
        $this->autoRender=false;
    }
}
