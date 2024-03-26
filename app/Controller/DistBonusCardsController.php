<?php

App::uses('AppController', 'Controller');

/**
 * DistBonusCards Controller
 *
 * @property DistBonusCard $DistBonusCard
 * @property PaginatorComponent $Paginator
 */
class DistBonusCardsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Filter.Filter');
    public $uses = array('DistBonusCard', 'Store','ProductType');

    /**
     * admin_index method
     *
     * @return void
     */
   
    public function admin_index() {
        $this->set('page_title', 'Incentive Party');
        $this->loadModel('DistBonusCardType');
        $this->loadModel('DistProductsBonusCard');

        $this->DistBonusCard->recursive = 1;
        $bonusCardTypes = $this->DistBonusCardType->find('list');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
		
		$conditions = array();
		 
        $this->paginate = array(
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'alias' => 'DistBonusCardType',
                    'table' => 'dist_bonus_card_types',
                    'conditions' => 'DistBonusCard.bonus_card_type_id = DistBonusCardType.id'
                    )
            ),
            
            'fields'=>array('DistBonusCard.*','DistBonusCardType.name'),
        );

        $distBonusCards = $this->paginate();
        // pr($distBonusCards);exit; 
       
        $card_ids = array();
        foreach ($distBonusCards as $key => $value) {
            $card_ids[$key] = $value['DistBonusCard']['id'];
        }
        
        $product_list = $this->DistProductsBonusCard->find('all',array('conditions'=>array('DistProductsBonusCard.dist_bonus_card_id'=>$card_ids),
            'fields'=>array('product.name','DistProductsBonusCard.dist_bonus_card_id'),
        ));

       

        $p_list = array();
        foreach ($product_list as $key => $value) {
           $p_list[$key][$value['DistProductsBonusCard']['dist_bonus_card_id']] = $value['product']['name'];
        }

        

        $this->set(compact('distBonusCards','bonusCardTypes'));
    }

    public function admin_index_so($store_id = 33) {
        /* $this->set('page_title','Current Inventories');
          pr($this->UserAuth->getStoreId());
          exit; */
        $this->DistBonusCard->recursive = 1;
        $this->loadModel('Store');
        $this->loadModel('InventoryStatuses');
        $this->loadModel('ProductCategory');
        $office_parent_id = $this->UserAuth->getOfficeParentId();

        if ($office_parent_id == 0) {
            $conditions = array('DistBonusCard.inventory_status_id' => 1);
            $storeCondition = array();
        } else {
            $conditions = array('DistBonusCard.store_id' => $store_id, 'DistBonusCard.inventory_status_id' => 1); //$this->UserAuth->getStoreId()
            $storeCondition = array('Store.office_id' => $this->UserAuth->getOfficeId());
        }

        //pr($this->UserAuth->getOfficeParentId());
        //exit;
        $this->paginate = array(
            //'conditions' => array('DistBonusCard.store_id'=>$this->UserAuth->getStoreId()),
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
            'fields' => array('DistBonusCard.product_id', 'DistBonusCard.store_id', 'SUM(DistBonusCard.qty) AS total', 'Product.name', 'Product.product_code', 'InventoryStatuses.name', 'Store.name', 'ProductCategory.name'),
            'group' => array('DistBonusCard.product_id', 'Product.name', 'Product.product_code', 'InventoryStatuses.name', 'Store.name', 'DistBonusCard.store_id', 'ProductCategory.name'),
                //'order' => array('DistBonusCard.id' => 'DESC')
        );

        /* pr($this->paginate());
          exit; */
        //$store_conditions = array('conditions'=>array('Store.office_id' => $this->UserAuth->getOfficeId()),'order' => array('Store.name' => 'ASC'));
        $this->set('DistBonusCards', $this->paginate());
        $products = $this->DistBonusCard->Product->find('list');
        $productCategories = $this->ProductCategory->find('list');
        $stores = $this->Store->find('list', array('conditions' => $storeCondition));
        $inventoryStatuses = $this->InventoryStatuses->find('list');
        $this->set(compact('stores', 'inventoryStatuses', 'products', 'productCategories'));
        //	$r=$this->DistBonusCard->find ('all',array('conditions'=>array('DistBonusCard.store_id'=>33),'fields'=>array('DistBonusCard.product_id','SUM(DistBonusCard.qty) AS total','Product.name'),'group'=>array('DistBonusCard.product_id','Product.name'))) ;
    }

    /*
     * view details method
     */

    public function admin_viewDetails($product_id = null, $store_id = null,$inventory_status_id=null) {
        $this->set('page_title', 'Current Inventories');


        $this->DistBonusCard->recursive = 1;
        $office_parent_id = $this->UserAuth->getOfficeParentId();

        //$productInfo=$this->Product->find('all',array('conditions'=>array('Product.id'=>$this->request->data['DistBonusCard']['product_id']),'recursive'=>-1,'fields'=>array('Product.id','Product.name','Product.sales_measurement_unit_id')));


        if ($office_parent_id == 0) {
            $this->paginate = array(
                'conditions' => array('DistBonusCard.store_id' => $store_id, 'DistBonusCard.product_id' => $product_id, 'DistBonusCard.inventory_status_id' => $inventory_status_id),
                'fields' => array('DistBonusCard.id', 'DistBonusCard.product_id', 'DistBonusCard.batch_number', 'DistBonusCard.expire_date', 'DistBonusCard.qty', 'InventoryStatuses.name', 'Store.name', 'Product.name', 'Product.product_code', 'Product.sales_measurement_unit_id'),
                'order' => array('DistBonusCard.id' => 'DESC')
            );
            $store_conditions = array('order' => array('Store.name' => 'ASC'));
        } else {
            $this->paginate = array(//$this->UserAuth->getStoreId()
                'conditions' => array('DistBonusCard.store_id' => $store_id, 'DistBonusCard.product_id' => $product_id,'DistBonusCard.inventory_status_id' => $inventory_status_id),
                'fields' => array('DistBonusCard.id', 'DistBonusCard.product_id', 'DistBonusCard.batch_number', 'DistBonusCard.expire_date', 'DistBonusCard.qty', 'InventoryStatuses.name', 'Store.name', 'Product.name', 'Product.product_code', 'Product.sales_measurement_unit_id'),
                'order' => array('DistBonusCard.id' => 'DESC')
            );
            $store_conditions = array('conditions' => array('Store.office_id' => $this->UserAuth->getOfficeId()), 'order' => array('Store.name' => 'ASC'));
        }
        $DistBonusCards1 = $this->paginate();
        foreach ($DistBonusCards1 as $DistBonusCard) {
            $DistBonusCard['DistBonusCard']['sale_unit_qty'] = $this->unit_convertfrombase($DistBonusCard['DistBonusCard']['product_id'], $DistBonusCard['Product']['sales_measurement_unit_id'], $DistBonusCard['DistBonusCard']['qty']);
            $DistBonusCards[] = $DistBonusCard;
        }

        //$this->set('DistBonusCards', $this->paginate());
        $stores = $this->Store->find('list', $store_conditions);
        $this->set(compact('stores', 'office_parent_id', 'DistBonusCards'));
    }

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_view($id = null) {
        if (!$this->DistBonusCard->exists($id)) {
            throw new NotFoundException(__('Invalid Incentive Party'));
        }
        $options = array(
            'conditions' => array('DistBonusCard.' . $this->DistBonusCard->primaryKey => $id),
            'joins' => array(
                array(
                    'alias' => 'DistBonusCardType',
                    'table' => 'dist_bonus_card_types',
                    'conditions' => 'DistBonusCard.bonus_card_type_id = DistBonusCardType.id'
                    )
            ),
            'fields'=>array('DistBonusCard.*','DistBonusCardType.name'),
        );
        $distBonusCard = $this->DistBonusCard->find('first', $options);


        $this->loadModel('DistProductsBonusCard');
        $product_list = $this->DistProductsBonusCard->find('all',array('conditions'=>array('DistProductsBonusCard.dist_bonus_card_id'=>$id)));
        $products = array();
        foreach($product_list as $key => $value){
            $products[$value['Product']['id']] = $value['Product']['name'];
        }
        $this->set(compact('products'));
        $this->set('distBonusCard',$distBonusCard);
    }

    public function admin_add() {
		$this->set('page_title','Add Incentive Affiliation');
        $this->loadModel('Product');
        $this->loadModel('DistBonusCardType');
        $this->loadModel('DistProductsBonusCard');
        $this->loadModel('DistPeriodsBonusCard');
        
		$products = $this->Product->find('list',array(
            'conditions'=>array(
                'is_distributor_product'=> 1,
            ),
			'order' => array('order' => 'ASC'),
		));
			
			
        $bonusCardTypes = $this->DistBonusCardType->find('list');

        if ($this->request->is('post')) { 
            //pr($this->request->data);die();
            $dist_bonus = array();
            $dist_bonus['name']=$this->request->data['DistBonusCard']['name'];
			$dist_bonus['min_qty']=$this->request->data['DistBonusCard']['min_qty'];
            $dist_bonus['bonus_card_type_id']=$this->request->data['DistBonusCard']['bonus_card_type_id'];
            $dist_bonus['date_from']=$this->request->data['DistBonusCard']['date_from'];
            $dist_bonus['date_to']=$this->request->data['DistBonusCard']['date_to'];
            $dist_bonus['status']= 1;
            $dist_bonus['created_at']=$this->current_datetime();
            $dist_bonus['created_by']=$this->UserAuth->getUserId();
            $dist_bonus['updated_at']=$this->current_datetime();
            $dist_bonus['updated_by']=$this->UserAuth->getUserId();
            $this->DistBonusCard->create();
            if($this->DistBonusCard->save($dist_bonus)){
                $dist_bonus_card_id = $this->DistBonusCard->getLastInsertId();
                $total_product_bonus = array();
                $product_ids = array();
                $products = $this->request->data['product_id'];
                if(!empty($products)){
                    foreach ($products as $key => $value) {
                        $dist_product_card['dist_bonus_card_id'] = $dist_bonus_card_id;
                        $dist_product_card['product_id'] = $value;
                        $dist_product_card['qty'] = $this->request->data['quantity'][$key];
                        $this->DistProductsBonusCard->create();
                        if($this->DistProductsBonusCard->save($dist_product_card)){
                            $dist_product_bonus_card_id = $this->DistProductsBonusCard->getLastInsertId();
                            $dist_period_bonuses = $this->request->data['period_product_id'][$value];
                            $data = array();
                            $total_period_bonus =  array();
                            foreach ($dist_period_bonuses as $key => $value) {

                                $date_from = $this->request->data['period_date_from'][$value][$key];
                                $date_from = str_replace('/', '-', $date_from);
                                $new_date_from = date("Y-m-d", strtotime($date_from));
                                $date_to = $this->request->data['period_date_to'][$value][$key];
                                $date_to = str_replace('/', '-', $date_to);
                                $new_date_to = date("Y-m-d", strtotime($date_to));

                                $data['dist_products_bonus_card_id'] =$dist_product_bonus_card_id;
                                $data['dist_bonus_card_id'] = $dist_bonus_card_id;
                                $data['product_id'] = $value;
                                $data['date_from'] = $new_date_from;
                                $data['date_to'] = $new_date_to;
                                $data['qty'] = $this->request->data['period_product_quantity'][$value][$key];

                                $total_period_bonus[] = $data;
                            }
                            $this->DistPeriodsBonusCard->create();
                            $this->DistPeriodsBonusCard->saveAll($total_period_bonus);
                        }
                    }
                
                
            }
            $this->redirect(array('action' => 'index'));
        }
        
    }
	
    $this->set(compact('products','bonusCardTypes'));
}

    /**
     * admin_edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_edit($id = null) {
		$this->set('page_title','Edit Incentive Affiliation');
        $this->DistBonusCard->id = $id;
        if (!$this->DistBonusCard->exists($id)) {
            throw new NotFoundException(__('Distributor Incentive Party'));
        }
        $this->loadModel('Product');
        $this->loadModel('DistBonusCardType');
        $this->loadModel('DistProductsBonusCard');
        $this->loadModel('DistPeriodsBonusCard');
        
		$products = $this->Product->find('list',array(
            'conditions'=>array(
                'is_distributor_product'=> 1,
            ),
			'order' => array('order' => 'ASC'),
		));
		
        $bonusCardTypes = $this->DistBonusCardType->find('list');
        if ($this->request->is('post') || $this->request->is('put')) {

                //pr($this->request->data);die();
                if(!empty($this->request->data['DistBonusCard']['id'])){
                    $dist_bonus = array();
                    $id = $this->request->data['DistBonusCard']['id'];
                    $this->admin_delete_data($id);
                    //pr($this->request->data);die();
					$dist_bonus['id']=$id;
                    $dist_bonus['name']=$this->request->data['DistBonusCard']['name'];
					$dist_bonus['min_qty']=$this->request->data['DistBonusCard']['min_qty'];
                    $dist_bonus['bonus_card_type_id']=$this->request->data['DistBonusCard']['bonus_card_type_id'];
                    $dist_bonus['date_from']=date("Y-m-d", strtotime($this->request->data['DistBonusCard']['date_from']));
                    $dist_bonus['date_to']=date("Y-m-d", strtotime($this->request->data['DistBonusCard']['date_to']));
                    $dist_bonus['status']= 1;
                    $dist_bonus['created_at']=$this->current_datetime();
                    $dist_bonus['created_by']=$this->UserAuth->getUserId();
                    $dist_bonus['updated_at']=$this->current_datetime();
                    $dist_bonus['updated_by']=$this->UserAuth->getUserId();
                    $this->DistBonusCard->create();
                    if($this->DistBonusCard->save($dist_bonus)){
                        $dist_bonus_card_id = $id;
                        $total_product_bonus = array();
                        $product_ids = array();
                        $products = $this->request->data['product_id'];
                        if(!empty($products)){
                            foreach ($products as $key => $value) {
                                $dist_product_card['dist_bonus_card_id'] = $dist_bonus_card_id;
                                $dist_product_card['product_id'] = $value;
                                $dist_product_card['qty'] = $this->request->data['quantity'][$key];
                                $this->DistProductsBonusCard->create();
                                if($this->DistProductsBonusCard->save($dist_product_card)){
                                    $dist_product_bonus_card_id = $this->DistProductsBonusCard->getLastInsertId();
                                    $dist_period_bonuses = $this->request->data['period_product_id'][$value];
                                    $data = array();
                                    $total_period_bonus =  array();
                                    foreach ($dist_period_bonuses as $key => $value) {

                                        $date_from = $this->request->data['period_date_from'][$value][$key];
                                        $date_from = str_replace('/', '-', $date_from);
                                        $new_date_from = date("Y-m-d", strtotime($date_from));
                                        $date_to = $this->request->data['period_date_to'][$value][$key];
                                        $date_to = str_replace('/', '-', $date_to);
                                        $new_date_to = date("Y-m-d", strtotime($date_to));

                                        $data['dist_products_bonus_card_id'] =$dist_product_bonus_card_id;
                                        $data['dist_bonus_card_id'] = $dist_bonus_card_id;
                                        $data['product_id'] = $value;
                                        $data['date_from'] = $new_date_from;
                                        $data['date_to'] = $new_date_to;
                                        $data['qty'] = $this->request->data['period_product_quantity'][$value][$key];

                                        $total_period_bonus[] = $data;
                                    }
                                    $this->DistPeriodsBonusCard->create();
                                    $this->DistPeriodsBonusCard->saveAll($total_period_bonus);
                                }
                            }
                        }
                    $this->redirect(array('action' => 'index'));
                }
                }
        } 
        else {
            $options = array('conditions' => array('DistBonusCard.' . $this->DistBonusCard->primaryKey => $id));
            $this->request->data = $this->DistBonusCard->find('first', $options);
			
			//pr($this->request->data);
			
            $this->loadModel('DistProductsBonusCard');
            $product_list = $this->DistProductsBonusCard->find('all',array('conditions'=>array('DistProductsBonusCard.dist_bonus_card_id'=>$id)));
            $product_data = array();
            foreach($product_list as $key => $value){
                $product_data[$value['Product']['id']] = $value['Product']['name'];
            }
            $this->set(compact('product_data'));
        }
        $this->set(compact('products','bonusCardTypes','distBonusCard'));
    }

    /**
     * admin_delete method
     *
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @param string $id
     * @return void
     */
    public function admin_delete_data($id = null){
        /*if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }*/
        $this->DistBonusCard->id = $id;
        if (!$this->DistBonusCard->exists()) {
            throw new NotFoundException(__('Invalid Incentive Party'));
        }
        //pr($id);die();
        $this->loadModel('DistProductsBonusCard');
        $this->loadModel('DistPeriodsBonusCard');
        if($this->DistPeriodsBonusCard->deleteAll(array('DistPeriodsBonusCard.dist_bonus_card_id' => $id))){
            if( $this->DistProductsBonusCard->deleteAll(array('DistProductsBonusCard.dist_bonus_card_id' => $id))){
                 if ($this->DistBonusCard->delete()) {
                    //$this->flash(__('Current inventory deleted'), array('action' => 'index'));
                }
            }
        }
       
        //$this->flash(__('Current inventory was not deleted'), array('action' => 'index'));
        //$this->redirect(array('action' => 'index'));
    }
    public function admin_delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->DistBonusCard->id = $id;
        if (!$this->DistBonusCard->exists()) {
            throw new NotFoundException(__('Invalid Incentive Party'));
        }

        if ($this->DistBonusCard->delete()) {
            $this->flash(__('Incentive Party deleted'), array('action' => 'index'));
        }
        $this->flash(__('Incentive Party not deleted'), array('action' => 'index'));
        $this->redirect(array('action' => 'index'));
    }

    /* ----------------------- Chainbox Data --------------------------- */

    public function get_batch_list() {
        $rs = array(array('id' => '', 'title' => '---- Select Batch -----'));
        $product_id = $this->request->data['product_id'];
        $inventory_status_id = $this->request->data['inventory_status_id'];
		if(isset($this->request->data['with_stock'])){
		$with_stock=$this->request->data['with_stock'];
			$conditions[] = array('DistBonusCard.qty >' => 0);
		}
		else $with_stock=false;
		 
		
		
        if (isset($this->request->data['transaction_type_id'])) {
			$conditions[] = array('DistBonusCard.inventory_status_id' => $inventory_status_id);
			$conditions[] = array('DistBonusCard.transaction_type_id' => $this->request->data['transaction_type_id']);
			$conditions[] = array('DistBonusCard.product_id' => $product_id);
			$conditions[] = array('DistBonusCard.store_id' => $this->UserAuth->getStoreId());
            
        } else {
            $conditions[] = array('DistBonusCard.inventory_status_id' => $inventory_status_id);
			$conditions[] = array('DistBonusCard.product_id' => $product_id);
			$conditions[] = array('DistBonusCard.store_id' => $this->UserAuth->getStoreId());
			
			
        }

        //$product_id = 12;
        $batch_list = $this->DistBonusCard->find('all', array(
            'fields' => array('DistBonusCard.batch_number as id', 'DistBonusCard.batch_number as title'),
            'conditions' => $conditions,
            'group' => array('DistBonusCard.batch_number'),
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
        $batch_list = $this->DistBonusCard->find('all', array(
            'fields' => array('DISTINCT DistBonusCard.batch_number as id', 'DistBonusCard.batch_number as title'),
            'conditions' => array(
                'DistBonusCard.inventory_status_id' => $inventory_status_id,
                'DistBonusCard.product_id' => $product_id,
                'DistBonusCard.store_id' => $this->UserAuth->getStoreId()
            ),
            //'group' => array('DistBonusCard.batch_number'),
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
        $batch_no = urldecode($this->request->data['batch_no']);
        // echo $batch_no;exit;
        //$batch_no = 'T242';
        $inventory_status_id = $this->request->data['inventory_status_id'];
		if(isset($this->request->data['with_stock'])){
			$with_stock=$this->request->data['with_stock'];
			$conditions[] = array('DistBonusCard.qty >' => 0);
		}
		else $with_stock=false;
		
        if (isset($this->request->data['transaction_type_id'])) {
			$conditions[] = array('DistBonusCard.inventory_status_id' => $inventory_status_id);
			$conditions[] = array('DistBonusCard.transaction_type_id' => $this->request->data['transaction_type_id']);
			$conditions[] = array('DistBonusCard.product_id' => $product_id);
			$conditions[] = array('DistBonusCard.batch_number' => $batch_no);
			$conditions[] = array('DistBonusCard.store_id' => $this->UserAuth->getStoreId());
            
        } else {
			$conditions[] = array('DistBonusCard.inventory_status_id' => $inventory_status_id);
			$conditions[] = array('DistBonusCard.product_id' => $product_id);
			$conditions[] = array('DistBonusCard.batch_number' => $batch_no);
			$conditions[] = array('DistBonusCard.store_id' => $this->UserAuth->getStoreId());
            
        }
        $exp_date_list = $this->DistBonusCard->find('all', array(
            'fields' => array('DistBonusCard.expire_date as id', 'DistBonusCard.expire_date as title'),
            'conditions' => $conditions,
            'recursive' => -1
        ));
	 $i=0;
       foreach($exp_date_list as $data){
            $data_array[]=array('id'=>$data[$i]['id'],'title'=>date("M-y",strtotime($data[$i]['title'])));
	    
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

        $conditions_options['DistBonusCard.store_id'] = $this->UserAuth->getStoreId();
        $conditions_options['DistBonusCard.product_id'] = $product_id;
	if($this->request->data['batch_no']  && $this->request->data['expire_date']){
            $conditions_options['DistBonusCard.batch_number'] = ($this->request->data['batch_no'] ? $this->request->data['batch_no'] : NULL );
       

            $conditions_options['DistBonusCard.expire_date'] = (!empty($this->request->data['expire_date']) ? $this->request->data['expire_date'] : NULL );

        }

        if (!empty($this->request->data['transaction_type_id'])) {
            $conditions_options['DistBonusCard.transaction_type_id'] = $this->request->data['transaction_type_id'];
        }
        if (!empty($this->request->data['inventory_status_id'])) {
            $conditions_options['DistBonusCard.inventory_status_id'] = $this->request->data['inventory_status_id'];
        }
        
        $batch_info = $this->DistBonusCard->find('first', array(
            'fields' => array('DistBonusCard.qty', 'Product.challan_measurement_unit_id'),
            'conditions' => array($conditions_options),
            'recursive' => 0
        ));
              
        if (!empty($batch_info)) {
            echo $this->unit_convertfrombase($product_id, $batch_info['Product']['challan_measurement_unit_id'], $batch_info['DistBonusCard']['qty']);
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
            $conditions_options['DistBonusCard.expire_date'] = $this->request->data['expire_date'];
        }
        if (!empty($this->request->data['transaction_type_id'])) {
            $conditions_options['DistBonusCard.transaction_type_id'] = $this->request->data['transaction_type_id'];
        }
        $conditions_options['DistBonusCard.product_id'] = $product_id;
        $conditions_options['DistBonusCard.batch_number'] = $batch_no;
        $conditions_options['DistBonusCard.inventory_status_id'] = $this->request->data['inventory_status_id'];
        $conditions_options['DistBonusCard.store_id'] = $this->UserAuth->getStoreId();
        $batch_info = $this->DistBonusCard->find('first', array(
            'fields' => array('DistBonusCard.qty', 'Product.return_measurement_unit_id'),
            'conditions' => array($conditions_options),
            'recursive' => 0
        ));
		//echo $batch_info['DistBonusCard']['qty'];
        if (!empty($batch_info)) {
            echo $this->unit_convertfrombase($product_id, $batch_info['Product']['return_measurement_unit_id'], $batch_info['DistBonusCard']['qty']);
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
            $conditions_options['DistBonusCard.expire_date'] = $this->request->data['expire_date'];
        }
        if (!empty($this->request->data['transaction_type_id'])) {
            $conditions_options['DistBonusCard.transaction_type_id'] = $this->request->data['transaction_type_id'];
        }
        $conditions_options['DistBonusCard.product_id'] = $product_id;
        $conditions_options['DistBonusCard.batch_number'] = $batch_no;
        $conditions_options['DistBonusCard.inventory_status_id'] = $this->request->data['inventory_status_id'];
        $conditions_options['DistBonusCard.store_id'] = $this->UserAuth->getStoreId();
        $batch_info = $this->DistBonusCard->find('first', array(
            'fields' => array('DistBonusCard.qty', 'Product.return_measurement_unit_id'),
            'conditions' => array($conditions_options),
            'recursive' => 0
        ));
        if (!empty($batch_info)) {
            echo $batch_info['DistBonusCard']['qty'];
        } else {
            echo '';
        }
        $this->autoRender = false;
    }

    public function get_inventory_status_list() {
        $rs = array(array('id' => '', 'title' => '---- Select Inventory Status -----'));
        $product_id = $this->request->data['product_id'];
        //$product_id = 12;
        $status_list = $this->DistBonusCard->find('all', array(
            'fields' => array('DISTINCT  InventoryStatuses.id as id', 'InventoryStatuses.name as title'),
            'conditions' => array(
                'DistBonusCard.product_id' => $product_id,
                'DistBonusCard.store_id' => $this->UserAuth->getStoreId()
            ),
            //'group' => array('DistBonusCard.inventory_status_id'),
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
        $cur_inv=array(2,$cur_inv_id);

        $this->LoadModel('DistBonusCard');
        $this->DistBonusCard->Behaviors->load('Containable');
        $cur_inv_product=$this->DistBonusCard->find( 'all',array(
            'conditions' => array('DistBonusCard.store_id'=>$this->UserAuth->getStoreId(),'DistBonusCard.inventory_status_id'=>$cur_inv_id,'Product.product_type_id'=>$type_id),
            'fields' => array('DistBonusCard.product_id','DistBonusCard.batch_number','DistBonusCard.expire_date','DistBonusCard.qty','DistBonusCard.inventory_status_id'),
            'contain' =>array('InventoryStatuses.name','Store.name','Product.name','Product.product_code','Product.ProductType.name'),
            'order' => array('Product.order' => 'ASC'),

        ));
        $data_array=array();
        //$fromProducts=array(''=>'--- Select Product ---');
        //$inventory_status= array(''=>'--- Select Status ---');

        if($cur_inv_product){
            
            foreach($cur_inv_product as $invProduct)
            {
                $fromProducts[$invProduct['DistBonusCard']['product_id']]=$invProduct['Product']['name'];


            }
            $data_array[1]=$fromProducts;
		    $this->LoadModel('InventoryStatus');
            $inventory_status = $this->InventoryStatus->find('list',array('conditions'=>array('NOT'=>array('InventoryStatus.id' => $cur_inv))));
            $data_array[0] = $inventory_status;
        }
         else{
            $fromProducts=array();
            $inventory_status= array();
            $data_array[1]=$fromProducts;
            $data_array[0] = $inventory_status;
        }

        echo json_encode($data_array);
        $this->autoRender = false;
    }
	
	
	
	public function get_product_Info_by_inv_id() {
        $cur_inv_id = $this->request->data['inv_id'];
        $this->DistBonusCard->Behaviors->load('Containable');


        $product_info = $this->DistBonusCard->find('all', array(
            'conditions' => array('DistBonusCard.store_id' => $this->UserAuth->getStoreId(), 'DistBonusCard.id' => $cur_inv_id),
            'fields' => array('DistBonusCard.id', 'DistBonusCard.qty'),
            'contain' => array('Product.product_type_id', 'Product.ProductType.name', 'Product.base_measurement_unit_id', 'Product.product_category_id'),
            'order' => array('DistBonusCard.id' => 'DESC'),
        ));


        //$data_array = Set::extract($status_list, '{n}.0');
        $data_array[0] = Set::combine($product_info, '{n}.Product.product_type_id', '{n}.Product.ProductType.name');
        $data_array['base_measurement_unit_id'] = $product_info[0]['Product']['base_measurement_unit_id'];
        $data_array['product_category_id'] = $product_info[0]['Product']['product_category_id'];
        $data_array['qty'] = $product_info[0]['DistBonusCard']['qty'];
        $data_array[0] = Set::combine($product_info, '{n}.Product.product_type_id', '{n}.Product.ProductType.name');


        echo json_encode($data_array);
        $this->autoRender = false;
    }

    public function get_product_Info_by_inv_id_back() {
        $cur_inv_id = $this->request->data['inv_id'];
        $this->DistBonusCard->Behaviors->load('Containable');


        $product_info = $this->DistBonusCard->find('all', array(
            'conditions' => array('DistBonusCard.store_id' => $this->UserAuth->getStoreId(), 'DistBonusCard.id' => $cur_inv_id),
            'fields' => array('DistBonusCard.id', 'DistBonusCard.qty'),
            'contain' => array('Product.product_type_id', 'Product.ProductType.name'),
            'order' => array('DistBonusCard.id' => 'DESC'),
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
        $batch_list = $this->DistBonusCard->find('all', array(
            'fields' => array('DistBonusCard.batch_number as id', 'DistBonusCard.batch_number as title'),
            'conditions' => array(
                'DistBonusCard.inventory_status_id' => $inventory_status_id,
                'DistBonusCard.product_id' => $product_id,
                'DistBonusCard.store_id' => $this->UserAuth->getStoreId()
            ),
            'group' => array('DistBonusCard.batch_number'),
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
        $batch_list = $this->DistBonusCard->find('all', array(
            'fields' => array('DistBonusCard.expire_date as id', 'DistBonusCard.expire_date as title'),
            'conditions' => array(
                'DistBonusCard.inventory_status_id' => $inventory_status_id,
                'DistBonusCard.product_id' => $product_id,
                'DistBonusCard.batch_number' => $batch_no,
                'DistBonusCard.store_id' => $this->UserAuth->getStoreId()
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
            $conditions_options['DistBonusCard.expire_date'] = $this->request->data['expire_date'];
        }
        $conditions_options['DistBonusCard.product_id'] = $product_id;
        $conditions_options['DistBonusCard.batch_number'] = $batch_no;
        $conditions_options['DistBonusCard.store_id'] = $this->UserAuth->getStoreId();
        $batch_info = $this->DistBonusCard->find('first', array(
            'fields' => array('DistBonusCard.qty', 'Product.challan_measurement_unit_id'),
            'conditions' => array($conditions_options),
            'recursive' => 0
        ));
        if (!empty($batch_info)) {
            echo $this->unit_convertfrombase($product_id, $batch_info['Product']['challan_measurement_unit_id'], $batch_info['DistBonusCard']['qty']);
        } else {
            echo '';
        }
        $this->autoRender = false;
    }
	
	
	public function get_product_list()
	{
		//$this->loadModel('Product');
              
	    $product_category_id = $this->request->data['product_category_id'];
		
		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		
		$products = $this->DistBonusCard->Product->find('all', array(
            'conditions' => array('Product.product_category_id' => $product_category_id),
			'order' => array('Product.order' => 'ASC'),
            'recursive' => -1
        ));
		
		//pr($products);
		
		$data_array = Set::extract($products, '{n}.Product');
		
		if(!empty($products))
		{
			echo json_encode(array_merge($rs, $data_array));
		}
		else
		{
			echo json_encode($rs);
		} 
		
        $this->autoRender = false;
    }
	
	
	public function admin_getCategoryQtyTotal($category_id=20)
	{
		//return $category_id.'<br>';
		
		//for category summary
		$category_summary = false;
		//$this->loadModel('DistBonusCard');
		/*$this->loadModel('Store');
		$this->loadModel('InventoryStatuses');
		$this->loadModel('ProductCategory');
		$this->loadModel('SalesPerson');*/
		
		//$category_summary = $this->request->data['DistBonusCard']['category_summary'];
		
		$conditions = array('DistBonusCard.inventory_status_id !=' => 2, 'Product.product_category_id =' => $category_id);
		
		$summary_list = $this->DistBonusCard->find('all', 
			array(
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
				'fields' => array('DistBonusCard.product_id', 'DistBonusCard.store_id', 'SUM(DistBonusCard.qty) AS total', 'Product.name', 'Product.product_category_id', 'Product.base_measurement_unit_id', 'Product.product_code', 'InventoryStatuses.name', 'Store.name', 'ProductCategory.name','DistBonusCard.inventory_status_id'),
				'group' => array('DistBonusCard.product_id', 'Product.name', 'Product.product_category_id', 'Product.order', 'Product.base_measurement_unit_id', 'Product.product_code', 'InventoryStatuses.name', 'Store.name', 'DistBonusCard.store_id', 'ProductCategory.name','DistBonusCard.inventory_status_id'),
				'order' => array('Product.order' => 'ASC'),
				'recursive' => 0
			)
		);
		
		$qty_total = 0;
		
		//pr($summary_list);
		
		foreach($summary_list as $result)
		{
			if($result['Product']['product_category_id']==$category_id){
			$qty_total += $result[0]['total'];
			}
		}
		
		//echo $qty_total;
		//exit;
		
		return $qty_total;
		
		//pr($summary_list);
		
		//exit;
		
	}
	
	
	public function getSOName($territory_id=0)
	{
		if($territory_id)
		{
			$this->loadModel('Territory');
			$territory_info = $this->Territory->find('first', 
				array(
					'conditions' => array('Territory.id' => $territory_id),
					'fields' => array('SalesPerson.name'),
					'recursive' => 0
				)
			);
			//pr($territory_info);
			//exit;
			if($territory_info['SalesPerson']['name']){
				return $territory_info['SalesPerson']['name'];
			}else{
				return 'NA';
			}
		}
		else
		{
			return 'NA';
		}
	}

    public function admin_product_details() 
    {

        $this->loadModel('ProductPrice');
        $this->loadModel('Product');
        $this->autoRender = false;

        $id = $this->request->data['product_id'];
        $date_from = $this->request->data['date_from'];
        $date_to = $this->request->data['date_to'];

        //$date_from =str_replace('/', '-', $date_from);
        //$date_to =str_replace('/', '-', $date_to);

        //$date_from = date('Y-m-d',strtotime($date_from));
        //$date_to = date('Y-m-d',strtotime($date_to));

        //pr($date_from);pr($date_to);die();
        $options = array('conditions' => array('Product.' . $this->Product->primaryKey => $id),'recursive'=>0);
        $product = $this->Product->find('first', $options);


        $from_date = new DateTime($date_from);
        $to_date = new DateTime($date_to);
        $totalDays = $from_date->diff($to_date);
        $number_of_days=$totalDays->days +1;
        if($totalDays->days < 31){
            $total_month = $totalDays->m + 1; 
        }
        else{
            $total_month = $totalDays->m; 
        }
        
        $data_array = array();
        $data_array['DateCalculation']['total_month'] = $total_month;
        $data_array['DateCalculation']['number_of_days'] = $number_of_days;
        
        echo json_encode(array_merge($product,$data_array));
    }
	

}
