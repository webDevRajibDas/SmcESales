<?php

App::uses('AppController', 'Controller');

/**
 * Territories Controller
 *
 * @property Territory $Territory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistDistributorClosesController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Session', 'Filter.Filter');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index() {
        $this->set('page_title', 'Distributor Close');

        $this->loadModel('Office');
        $this->loadModel('DistDistributor');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' =>2);
            $distributor_conditions = array();
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(),'Office.office_type_id' =>2);
            $distributor_conditions = array('office_id'=>$this->UserAuth->getOfficeId());
        }

        if($this->request->is('post')){


            $office_id = $this->request->data['DistDistributor']['office_id'];
            $distributor_id = $this->request->data['DistDistributor']['distributor_id'];
            $effective_date = $this->request->data['DistDistributor']['effective_date'];

            $dist_close_data['DistDistributorClose']['office_id'] = $office_id;
            $dist_close_data['DistDistributorClose']['dist_distributor_id'] = $distributor_id;
            $dist_close_data['DistDistributorClose']['effective_date'] = date('Y-m-d', strtotime($effective_date));
            $dist_close_data['DistDistributorClose']['created_by'] = $this->UserAuth->getUserId();
            $dist_close_data['DistDistributorClose']['created_at'] = $this->current_datetime();
            $dist_close_data['DistDistributorClose']['updated_by'] = $this->UserAuth->getUserId();
            $dist_close_data['DistDistributorClose']['updated_at'] = $this->current_datetime();


            $distrbutor_info = $this->DistDistributor->find('first',array('conditions'=>array('DistDistributor.id'=>$distributor_id)));
            //pr($distrbutor_info);die();

            /****************************************Start Dist Tso Mapping History*************************************************/
            $this->loadModel('DistTsoMapping');
            $this->loadModel('DistTsoMappingHistory');
            $dist_tso_mapping_histories_info = $this->DistTsoMappingHistory->find('first',array(
                'conditions'=>array('DistTsoMappingHistory.dist_distributor_id'=>$distributor_id,'DistTsoMappingHistory.active'=> 1),
                'recursive'=> -1,
            ));

            $dist_tso_mapping_histories_array = $dist_tso_mapping_histories_info;

            if(!empty($dist_tso_mapping_histories_info)){
                /*foreach ($dist_tso_mapping_histories_info as $key => $value) {
                    $dist_tso_mapping_histories_info[$key]['DistTsoMappingHistory']['active'] = 0;
                }*/
                $dist_tso_mapping_histories_info['DistTsoMappingHistory']['active'] = 0;
               
                $dist_tso_mapping_histories_info['DistTsoMappingHistory']['created_by'] = $this->UserAuth->getUserId();
                $dist_tso_mapping_histories_info['DistTsoMappingHistory']['created_at'] = $this->current_datetime();
                $dist_tso_mapping_histories_info['DistTsoMappingHistory']['updated_at'] = $this->current_datetime();
                $dist_tso_mapping_histories_info['DistTsoMappingHistory']['updated_by'] = $this->UserAuth->getUserId();
                $dist_tso_mapping_histories_info['DistTsoMappingHistory']['effective_date'] = $effective_date;
                unset($dist_tso_mapping_histories_info['DistTsoMappingHistory']['id']);
                $this->DistTsoMappingHistory->create();
                $this->DistTsoMappingHistory->save($dist_tso_mapping_histories_info);
                
                $this->DistTsoMapping->delete(array('DistTsoMapping.office_id'=>$office_id,'DistTsoMapping.dist_distributor_id'=>$distributor_id));
                
            }
            
            /****************************************end Dist Tso Mapping History*********************************************/

            /****************************************Start Dist Sr Route Mapping************************************************************/

            /*$this->loadModel('DistSrRouteMapping');
            $dist_sr_route_mapping_info = $this->DistSrRouteMapping->find('all',array(
                'conditions'=>array('DistSrRouteMapping.dist_distributor_id'=>$distributor_id),
                'recursive'=> -1,
                ));
            if(!empty($dist_sr_route_mapping_info)){
                foreach ($dist_sr_route_mapping_info as $key => $value) {
                    $dist_sr_route_mapping_info[$key]['DistSrRouteMapping']['updated_at'] = $this->current_datetime();
                    $dist_sr_route_mapping_info[$key]['DistSrRouteMapping']['updated_by'] =  $this->UserAuth->getUserId();
                }
                
                $this->DistSrRouteMapping->saveAll($dist_sr_route_mapping_info);
            }*/


            /****************************************end Dist Sr Route Mapping************************************************************/
            /****************************************Start Dist Sale Target************************************************************/

            /*$this->loadModel('DistSaleTarget');
            $dist_sale_target_info = $this->DistSaleTarget->find('all',array(
                'conditions'=>array('DistSaleTarget.dist_distributor_id'=>$distributor_id),
                'recursive'=> -1,
                ));
            if(!empty($dist_sale_target_info)){
                foreach ($dist_sale_target_info as $key => $value) {
                    $dist_sale_target_info[$key]['DistSaleTarget']['updated_at'] =  $this->current_datetime();
                    $dist_sale_target_info[$key]['DistSaleTarget']['updated_by'] = $this->UserAuth->getUserId();
                }
               
                $this->DistSaleTarget->saveAll($dist_sale_target_info);
            }*/

            /****************************************end Dist Sale Target************************************************************/
            /****************************************Start Dist Sale Target Month************************************************************/

            /*$this->loadModel('DistSaleTargetMonth');
            $dist_sale_target_month_info = $this->DistSaleTargetMonth->find('all',array(
                'conditions'=>array('DistSaleTargetMonth.dist_distributor_id'=>$distributor_id),
                'recursive'=> -1,
                ));
            if(!empty($dist_sale_target_month_info)){
                foreach ($dist_sale_target_month_info as $key => $value) {
                    $dist_sale_target_month_info[$key]['DistSaleTargetMonth']['updated_at'] = $this->current_datetime();
                    $dist_sale_target_month_info[$key]['DistSaleTargetMonth']['updated_by'] = $this->UserAuth->getUserId();
                }
               
                $this->DistSaleTargetMonth->saveAll($dist_sale_target_month_info);
            }*/
            /****************************************end Dist Sale Target Month************************************************************/
            /****************************************Start Dist Sales Representative************************************************************/

            $this->loadModel('DistSalesRepresentative');
            $dist_sale_representative_info = $this->DistSalesRepresentative->find('all',array(
                'conditions'=>array('DistSalesRepresentative.dist_distributor_id'=>$distributor_id),
                'recursive'=> -1,
                ));
            $dist_sale_representative_array = $dist_sale_representative_info;

            if(!empty($dist_sale_representative_info)){
                foreach ($dist_sale_representative_info as $key => $value) {
                    $dist_sale_representative_info[$key]['DistSalesRepresentative']['is_active'] = 0;
                }
                
                $this->DistSalesRepresentative->saveAll($dist_sale_representative_info);
                
            }
            /***************************end Dist Sales Representative**************************************************/
            /***************************Start DistDelivery Man**************** ****************************************/

            $this->loadModel('DistDeliveryMan');
            $dist_delivery_man_info = $this->DistDeliveryMan->find('all',array(
                'conditions'=>array('DistDeliveryMan.dist_distributor_id'=>$distributor_id),
                'recursive'=> -1,
                ));
            $dist_delivery_man_array = $dist_delivery_man_info;
            if(!empty($dist_delivery_man_info)){
                foreach ($dist_delivery_man_info as $key => $value) {
                    $dist_delivery_man_info[$key]['DistDeliveryMan']['is_active'] = 0;
                }
                
                $this->DistDeliveryMan->saveAll($dist_sale_representative_info);
                
            }
            /************************end DistDelivery Man*********************************************/
            /*********************Start Dist Sr Route Mapping History*********************************/

            $this->loadModel('DistSrRouteMapping');
            $this->loadModel('DistSrRouteMappingHistory');
            $dist_sr_route_mapping_history_info = $this->DistSrRouteMappingHistory->find('first',array(
                'conditions'=>array('DistSrRouteMappingHistory.dist_distributor_id'=>$distributor_id),
                'recursive'=> -1,
                ));
            $dist_sr_route_mapping_history_array = $dist_sr_route_mapping_history_info;
            
            if(!empty($dist_sr_route_mapping_history_info)){
                /*foreach ($dist_sr_route_mapping_history_info as $key => $value) {
                    $dist_sr_route_mapping_history_info[$key]['DistSrRouteMappingHistory']['active'] = 0;
                }*/
                $dist_sr_route_mapping_history_info['DistSrRouteMappingHistory']['active'] = 0;
                $dist_sr_route_mapping_history_info['DistSrRouteMappingHistory']['end_date'] = $this->current_datetime();
                $dist_sr_route_mapping_history_info['DistSrRouteMappingHistory']['created_by'] = $this->UserAuth->getUserId();
                $dist_sr_route_mapping_history_info['DistSrRouteMappingHistory']['created_at'] = $this->current_datetime();
                $dist_sr_route_mapping_history_info['DistSrRouteMappingHistory']['updated_at'] = $this->current_datetime();
                $dist_sr_route_mapping_history_info['DistSrRouteMappingHistory']['updated_by'] = $this->UserAuth->getUserId();
                $dist_sr_route_mapping_history_info['DistSrRouteMappingHistory']['effective_date'] = $effective_date;
                unset($dist_sr_route_mapping_history_info['DistSrRouteMappingHistory']['id']);
                $this->DistSrRouteMappingHistory->create();
                $this->DistSrRouteMappingHistory->save($dist_sr_route_mapping_history_info);
                
                $this->DistSrRouteMapping->delete(array('DistSrRouteMapping.office_id'=>$office_id,'DistSrRouteMapping.dist_distributor_id'=>$distributor_id));
                
            }


             /***********************end Dist Sr Route Mapping History******************************/
            /***************************Start Dist Sr Visit Plan************************************/

            /*$this->loadModel('DistSrVisitPlan');
            $dist_sr_visit_plan_info = $this->DistSrVisitPlan->find('all',array(
                'conditions'=>array('DistSrVisitPlan.distributor_id'=>$distributor_id),
                'recursive'=> -1,
                ));
            if(!empty($dist_sr_visit_plan_info)){
                foreach ($dist_sr_visit_plan_info as $key => $value) {
                   
                    $dist_sr_visit_plan_info[$key]['DistSrVisitPlan']['updated_at'] = $this->current_datetime();
                    $dist_sr_visit_plan_info[$key]['DistSrVisitPlan']['updated_by'] = $this->UserAuth->getUserId();
                    
                   
                }
                
                $this->DistSrVisitPlan->saveAll($dist_sr_visit_plan_info);
            }*/
            /*************************end Dist Sr Visit Plan******************************************/
            /****************************Start Dist Outlet Map****************************************/

            /*$this->loadModel('DistOutletMap');
            $dist_outlet_mapping_info = $this->DistOutletMap->find('all',array(
                'conditions'=>array('DistOutletMap.dist_distributor_id'=>$distributor_id),
                'recursive'=> -1,
                ));
            if(!empty($dist_outlet_mapping_info)){
                foreach ($dist_outlet_mapping_info as $key => $value) {
                    
                    $dist_outlet_mapping_info[$key]['DistOutletMap']['updated_at'] = $this->current_datetime();
                    $dist_outlet_mapping_info[$key]['DistOutletMap']['updated_by'] = $this->UserAuth->getUserId();
                }
                
                $this->DistOutletMap->saveAll($dist_outlet_mapping_info);
            }*/
            /**************************end Dist Outlet Map*******************************************/
            /*************************Start Dist Route Mapping*******************************************/

            /*$this->loadModel('DistRouteMapping');
            $dist_route_mapping_info = $this->DistRouteMapping->find('all',array(
                'conditions'=>array('DistRouteMapping.dist_distributor_id'=>$distributor_id),
                'recursive'=> -1,
                ));
            if(!empty($dist_route_mapping_info)){
                foreach ($dist_route_mapping_info as $key => $value) {
                    $dist_route_mapping_info[$key]['DistRouteMapping']['updated_at'] = $this->current_datetime();
                    $dist_route_mapping_info[$key]['DistRouteMapping']['updated_by'] = $this->UserAuth->getUserId();
                }
                $this->DistRouteMapping->saveAll($dist_route_mapping_info);
            }*/
            /***********************end Dist Route Mapping*************************************************/
            /*************************Start Dist Route Mapping History*************************************/

            $this->loadModel('DistRouteMapping');
            $this->loadModel('DistRouteMappingHistory');
            $dist_route_mapping_histories_info = $this->DistRouteMappingHistory->find('first',array(
                'conditions'=>array('DistRouteMappingHistory.dist_distributor_id'=>$distributor_id),
                'recursive'=> -1,
                ));
            $dist_route_mapping_histories_array = $dist_route_mapping_histories_info;
            if(!empty($dist_route_mapping_histories_info)){
                /*foreach ($dist_route_mapping_histories_info as $key => $value) {
                    $dist_route_mapping_histories_info[$key]['DistRouteMappingHistory']['active'] = 0;
                }
                $this->DistRouteMappingHistory->saveAll($dist_route_mapping_histories_info);*/
                $dist_route_mapping_histories_info['DistRouteMappingHistory']['active'] = 0;
               
                $dist_route_mapping_histories_info['DistRouteMappingHistory']['created_by'] = $this->UserAuth->getUserId();
                $dist_route_mapping_histories_info['DistRouteMappingHistory']['created_at'] = $this->current_datetime();
                $dist_route_mapping_histories_info['DistRouteMappingHistory']['updated_at'] = $this->current_datetime();
                $dist_route_mapping_histories_info['DistRouteMappingHistory']['updated_by'] = $this->UserAuth->getUserId();
                $dist_route_mapping_histories_info['DistRouteMappingHistory']['effective_date'] = $effective_date;
                
                unset($dist_route_mapping_histories_info['DistRouteMappingHistory']['id']);
                $this->DistRouteMappingHistory->create();
                $this->DistRouteMappingHistory->save($dist_route_mapping_histories_info);
                
                $this->DistRouteMapping->deleteAll(array('DistRouteMapping.office_id'=>$office_id,'DistRouteMapping.dist_distributor_id'=>$distributor_id));                
            }
           /****************************end Dist Route Mapping History***********************************/
            $data = array();
            if($distrbutor_info){
                $data['id']= $distrbutor_info['DistDistributor']['id'];
                $data['is_active'] = 0;
                $data['updated_at'] =$this->current_datetime();
                $data['updated_by'] = $this->UserAuth->getUserId();

                if($this->DistDistributor->save($data)){
                    $this->DistDistributorClose->create();
                    if($this->DistDistributorClose->save($dist_close_data)){
                        $this->Session->setFlash(__('The Distributor has been Closed'), 'flash/success');
                        $this->redirect(array('action' => 'index'));
                    }
                }
            }
        }

        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        $distributors = $this->DistDistributor->find('list',array('conditions'=>$distributor_conditions));
        $this->set(compact('offices','distributors'));
    }

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_view($id = null) {
        $this->set('page_title', 'Route/Beat Details');
        if (!$this->DistDistributorClose->exists($id)) {
            throw new NotFoundException(__('Invalid Route/Beat'));
        }
        $options = array('conditions' => array('DistDistributorClose.' . $this->DistDistributorClose->primaryKey => $id));
        $this->set('tso', $this->DistDistributorClose->find('first', $options));
    }

    /**
     * admin_add method
     *
     * @return void
     */
   
   


    public function admin_add() {
        $this->set('page_title', 'New Commission Rate');
        if ($this->request->is('post')) {

            //pr($this->request->data);die();
            $effective_date = $this->request->data['DistDistributorClose']['effective_date'];
            $this->request->data['DistDistributorClose']['effective_date'] = date('Y-m-d', strtotime($effective_date));
            $this->request->data['DistDistributorClose']['created_at'] = $this->current_datetime();
            $this->request->data['DistDistributorClose']['created_by'] = $this->UserAuth->getUserId();
            $this->request->data['DistDistributorClose']['updated_at'] = $this->current_datetime();
            $this->DistDistributorClose->create();
            if ($this->DistDistributorClose->save($this->request->data)) {
                $this->Session->setFlash(__('The Route/Beat has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Route/Beat could not be saved. Please, try again.'), 'flash/error');
            }
        }

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {

            $conditions = array('Office.office_type_id' =>2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(),'Office.office_type_id' =>2);
        }
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        $this->set(compact('offices'));
    }

    /**
     * admin_edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_edit($id = null) {
        $this->set('page_title', 'Edit Route/Beat');
        $this->DistDistributorClose->id = $id;
        if (!$this->DistDistributorClose->exists($id)) {
            throw new NotFoundException(__('Invalid Route/Beat'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['DistDistributorClose']['updated_by'] = $this->UserAuth->getUserId();
            if ($this->DistDistributorClose->save($this->request->data)) {
                $this->Session->setFlash(__('The Route/Beat has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $options = array('conditions' => array('DistDistributorClose.' . $this->DistDistributorClose->primaryKey => $id));
            $this->request->data = $this->DistDistributorClose->find('first', $options);

            $this->loadmodel('Territory');
            $this->loadmodel('ThanaTerritory');
            $office_id = $this->request->data['DistDistributorClose']['office_id'];
            $territory = $this->Territory->find('all', array(
                    'fields' => array('Territory.id'),
                    'conditions' => array('Territory.office_id' => $office_id),
                    'order' => array('Territory.name' => 'asc'),
                    'recursive' => 0
            ));
            $thanas = array();
            $territories = array();
            
            foreach($territory as $key => $value)
            {
                $territories[$key] = $value['Territory']['id'];
               
            }
            
            //pr($data_array);die();
           $thana_list = $this->ThanaTerritory->find('all',array('conditions'=>array(
            'ThanaTerritory.territory_id' => $territories,
           )));

           foreach($thana_list as $key => $value)
            {
                $thanas[$value['Thana']['id']] = $value['Thana']['name'];
            }

            

        }
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' =>2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(),'Office.office_type_id' =>2);
        }
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        $this->set(compact('offices','thanas'));
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
        
        $this->DistDistributorClose->id = $id;
        if (!$this->DistDistributorClose->exists()) {
            throw new NotFoundException(__('Invalid Route/Beat'));
        }
        
                /******************************* Check foreign key ****************************/
        
        if($this->DistDistributorClose->checkForeignKeys("dist_routes",$id))
        {
            $this->Session->setFlash(__('This Route/Beat has used in another'), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }
        
        
        if ($this->DistDistributorClose->delete()) {
            $this->Session->setFlash(__('Route/Beat deleted'), 'flash/success');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Route/Beat was not deleted'), 'flash/error');
        $this->redirect(array('action' => 'index'));
    }
    
    public function get_distributor_list_list(){
        
        $office_id = $this->request->data['office_id'];
        $rs = array(array('id' => '', 'name' => '---- Select -----'));
        $this->loadModel('DistDistributor');

        $distributor_info = $this->DistDistributor->find('all',array('conditions'=>array(
            'DistDistributor.office_id'=>$office_id),
       // 'order' =>array('DistDistributor.id DESC'),
        ));

        $data_array = array();

        foreach ($distributor_info as $key => $value) {
            $data_array[] = array(
                'id' => $value['DistDistributor']['id'],
                'name' =>$value['DistDistributor']['name'],
            );
        }

        if(!empty($distributor_info)){
            echo json_encode(array_merge($rs,$data_array));
        }
        else{
            echo json_encode($rs);
        }
        $this->autoRender = false;

    }

     public function admin_get_dsitributor_balance () {
        
        $office_id = $this->request->data['office_id'];
        $distributor_id = $this->request->data['distributor_id'];
        $this->loadModel('DistDistributorBalance');
        //$this->loadModel('DistDistributorLimit');
        $this->loadModel('DistCurrentInventory');
        $this->loadModel('DistStore');
        $this->loadModel('DistOutletMap');
        $this->loadModel('Order');
        $this->loadModel('Memo');

        $distributor_balance = $this->DistDistributorBalance->find('first',array('conditions'=>array(
            'DistDistributorBalance.dist_distributor_id' => $distributor_id,
            'DistDistributorBalance.office_id' => $office_id,
        )));

        /*$distributor_limit = $this->DistDistributorLimit->find('first',array('conditions'=>array(
            'DistDistributorLimit.dist_distributor_id' => $distributor_id,
            'DistDistributorLimit.office_id' => $office_id,
        )));*/

        $store_info = $this->DistStore->find('first',array('conditions'=>array(
            'DistStore.office_id'=>$office_id,
            'DistStore.dist_distributor_id'=>$distributor_id,
        )));
        $store_id =$store_info['DistStore']['id'];
        $inventory_info = $this->DistCurrentInventory->find('all',array('conditions'=>array(
            'DistCurrentInventory.store_id' => $store_id
        )));
        $stock_balance = 0;
        foreach ($inventory_info as $key => $value) {
            if($value['DistCurrentInventory']['qty'] > 0){
                $stock_balance = 1;
                break;
            }
        }
        $dist_outlet_map_info = $this->DistOutletMap->find('first',array(
            'conditions'=>array(
                'DistOutletMap.office_id'=> $office_id,
                'DistOutletMap.dist_distributor_id'=> $distributor_id,
            ),
        ));
        $outlet_id = $dist_outlet_map_info['DistOutletMap']['outlet_id'];
        $market_id = $dist_outlet_map_info['DistOutletMap']['market_id'];
        $territory_id = $dist_outlet_map_info['DistOutletMap']['territory_id'];
       
        $last_order = $this->Order->find('first',array(
            'conditions'=>array(
                'Order.office_id'=>$office_id,
                'Order.outlet_id'=>$outlet_id,
            ),
            'order'=> array('Order.id DESC')
        ));
        $all_order_info = $this->Order->find('all',array(
            'conditions'=>array(
                'Order.office_id'=>$office_id,
                'Order.outlet_id'=>$outlet_id,
                'Order.status >'=>0,
                'Order.confirm_status !='=>2,
            ),
            'order'=> array('Order.id DESC')
        ));
        $pending_order = 0;
        if($all_order_info){
            $pending_order = 1;
        }
        if($last_order){
            $last_order_date = $last_order['Order']['order_date'];
        }
        else{
            $last_order_date = $this->current_datetime();
        }
        $last_memo = $this->Memo->find('first',array(
            'conditions'=>array(
                'Memo.office_id'=>$office_id,
                'Memo.outlet_id'=>$outlet_id,
            ),
            'order'=> array('Memo.id DESC')
        ));

        if($last_order){
            $last_memo_date = $last_memo['Memo']['memo_date'];
        }
        else{
            $last_memo_date = $this->current_datetime();
        }
        
        $last_effective_date ="";

        if($last_memo_date != null || $last_order_date != null){
            if($last_memo_date > $last_order_date){
                $last_effective_date = $last_memo_date; 
            }
            elseif($last_order_date > $last_memo_date){
                $last_effective_date = $last_order_date;
            }
            else{
                $last_effective_date = $last_order_date;
            }
        }
        

        $balance = 0;
        $data_array = array();
        if(!empty($distributor_balance)){
            $balance = $distributor_balance['DistDistributorBalance']['balance'];
            $data_array = array(
                'balance' => $balance,
                'stock_balance' => $stock_balance,
                'effective_date' => $last_effective_date,
                'pending_order'=>$pending_order,
            );
        }
        else{
             $balance = 0;
             $data_array = array(
                'balance' => $balance,
                'stock_balance' =>$stock_balance,
                'effective_date' => $last_effective_date,
                'pending_order'=>$pending_order,
            );
        }
        
        echo json_encode($data_array);

        $this->autoRender = false;
    }

}
