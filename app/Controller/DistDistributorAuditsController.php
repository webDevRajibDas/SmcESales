<?php

App::uses('AppController', 'Controller');

/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
class DistDistributorAuditsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Filter.Filter');
    public $uses = array('DistDistributorAudit', 'Store', 'ProductType');

    /**
     * admin_index method
     *
     * @return void
     */

    /**
     * inventory_total method
     */
    public function admin_index() {
        $this->set('page_title', 'Distributor Audit List');
        $this->loadModel('Office');
        $this->loadModel('Product');
        $this->loadModel('DistDistributorAuditDetail');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        if ($this->request->is('post')) {
          
            $query = array(
                    /* 'DistDistributorAudit.office_id' => 'DESC',
                      'DistDistributorAudit.dist_distributor_id' => 'DESC',
                      'DistDistributorAudit.product_id' => 'DESC' */
            );
        } else {
            $query = array();
        }
      
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $distributors=array();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
        
        
        
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        $products = $this->Product->find('list');
        $this->set(compact('offices', 'products', 'inventoryStatuses'));
       
        $this->loadModel('DistDistributor');
        
        if ($this->request->data) {
              $office_id=$this->request->data['DistDistributorAudit']['office_id'];
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
                $full_dis_conditions=array(
                    'conditions' => array('DistDistributor.id' => array_keys($tso_dist_list), 'DistDistributor.is_active' => 1),'order' => array('DistDistributor.name' => 'asc'),
                );
              }
               elseif($user_group_id == 1034){
                    $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                    $this->loadModel('DistUserMapping');
                    $distributor = $this->DistUserMapping->find('first',array(
                        'conditions'=>array('DistUserMapping.sales_person_id'=>$sales_people_id),
                    ));
                    $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
            
                   $full_dis_conditions=array(
                    'conditions' => array('DistDistributor.id' => $distributor_id, 'DistDistributor.is_active' => 1),'order' => array('DistDistributor.name' => 'asc'),
                );
                }
              else{
                $full_dis_conditions=array(
                    'conditions' => array('DistDistributor.office_id' => $office_id, 'DistDistributor.is_active' => 1),'order' => array('DistDistributor.name' => 'asc'),
                );
              }
                
            $distributors = $this->DistDistributor->find('list', $full_dis_conditions);
        }
        
       $this->set('distributors', $distributors);
        //$this->DistDistributorAudit->recursive = 1;
        $this->paginate = array(
            'conditions' => array(),
            /* 'joins' => array(
              array(
              'alias' => 'DistDistributorAuditDetail',
              'table' => 'dist_distributor_audit_details',
              'type' => 'INNER',
              'conditions' => 'DistDistributorAudit.id=DistDistributorAuditDetail.dist_distributor_audit_id'
              )
              ), */
            //'fields' => array('DistDistributorAudit.id','DistDistributorAuditDetail.*'),
            'order' => array('DistDistributorAudit.id' => 'desc')
        );
        $this->set('distDistributorAudits', $this->paginate());
    }

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function get_date_format($date = '1970-01-01') {
        $explode = explode('-', $date);
        return $explode[2] . '-' . $explode[1] . '-' . $explode[0];
    }

    public function admin_view($id = null) {
        if (!$this->DistDistributorAudit->exists($id)) {
            throw new NotFoundException(__('Invalid  Distributor Audit'));
        }
        $this->loadModel('Product');
        $this->loadModel('InventoryStatuses');
        $this->loadModel('MeasurementUnit');
        
        
             /****** tso and ae list start  *****/
        
        $this->loadModel('DistTso');
        $tso_list=$this->DistTso->find('list', array('order' => array('DistTso.name' => 'asc'),'recursive' => -1));
        
        $this->loadModel('DistAreaExecutive');
        $ae_list=$this->DistAreaExecutive->find('list', array('order' => array('DistAreaExecutive.name' => 'asc'),'recursive' => -1));
        $this->set(compact('tso_list','ae_list'));
        
        /****** tso and ae list end  *****/
        
        

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }


        $products = $this->Product->find('list');
        $measurementUnits = $this->MeasurementUnit->find('list');
        $this->set(compact('offices', 'products', 'measurementUnits'));

        $options = array('conditions' => array('DistDistributorAudit.' . $this->DistDistributorAudit->primaryKey => $id));
        $this->set('distDistributorAudit', $this->DistDistributorAudit->find('first', $options));
    }

    public function get_base_measurement_unit($sales_measurement_unit_id, $product_id) {
        $this->loadModel('Product');
        $products = $this->Product->find('first', array(
            'conditions' => array(
                'Product.id' => $product_id,
                'Product.sales_measurement_unit_id' => $sales_measurement_unit_id,
            ),
            'fields' => 'Product.id,Product.base_measurement_unit_id',
            'recursive' => -1
        ));
        if (count($products) > 0) {
            return $products['Product']['base_measurement_unit_id'];
        } else {
            return 0;
        }
    }

    public function get_sales_measurement_unit($base_measurement_unit_id, $product_id) {
        $this->loadModel('Product');
        $products = $this->Product->find('first', array(
            'conditions' => array(
                'Product.id' => $product_id,
                'Product.base_measurement_unit_id' => $base_measurement_unit_id,
            ),
            'fields' => 'Product.id,Product.sales_measurement_unit_id',
            'recursive' => -1
        ));
        if (count($products) > 0) {
            return $products['Product']['sales_measurement_unit_id'];
        } else {
            return 0;
        }
    }

    public function admin_add() {
        $this->set('page_title', 'Add Distributor Audit');
        
        $audit_by = array(
			'1' => 'AE',
			'2' => 'TSO',
		);
        $this->set(compact('audit_by'));
        
        if ($this->request->is('post')) {
            $this->loadModel('DistDistributorAudit');
            $this->loadModel('DistDistributorAuditDetail');
            $this->DistDistributorAudit->create();
            $audit['office_id'] = $this->request->data['DistDistributorAudit']['office_id'];
            $audit['dist_distributor_id'] = $this->request->data['DistDistributorAudit']['dist_distributor_id'];
            $audit['dist_tso_id'] = $this->request->data['DistDistributorAudit']['dist_tso_id'];
            $audit['dist_ae_id'] = $this->request->data['DistDistributorAudit']['dist_ae_id'];
            $audit['audit_by'] = $this->request->data['DistDistributorAudit']['audit_by'];
            $audit['audit_date'] = date("Y-m-d", strtotime($this->request->data['DistDistributorAudit']['audit_date']));
            $audit['created_at'] = $this->current_datetime();
            $audit['created_by'] = $this->UserAuth->getUserId();
            $audit['updated_at'] = $this->current_datetime();
            $audit['updated_by'] = $this->UserAuth->getUserId();
            $this->DistDistributorAudit->save($audit);
            $dist_distributor_audit_id = $this->DistDistributorAudit->getLastInsertID();

            /* ---------------Add in ------------------ */
            $i = 0;
            $array = array();
            foreach ($this->request->data['product_id'] as $key => $value) {
                $array[$i]['dist_distributor_audit_id'] = $dist_distributor_audit_id;
                $array[$i]['product_id'] = $value;
                $array[$i]['measurement_unit_id'] = $this->get_base_measurement_unit($this->request->data['measurement_unit'][$key], $value);
                $array[$i]['qty'] = $this->request->data['quantity'][$key];
                $array[$i]['expire_date'] = date("Y-m-d", strtotime($this->request->data['expire_date'][$key]));
                $array[$i]['batch_number'] = $this->request->data['batch_number'][$key];
                $array[$i]['qty'] = $this->request->data['quantity'][$key];
                $array[$i]['created_at'] = $this->current_datetime();
                $array[$i]['created_by'] = $this->UserAuth->getUserId();
                $array[$i]['updated_at'] = $this->current_datetime();
                $array[$i]['updated_by'] = $this->UserAuth->getUserId();
                $i++;
            }
            if ($this->DistDistributorAuditDetail->saveAll($array)) {
                $this->Session->setFlash(__('Data has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('Data could not be saved. Please, try again.'), 'flash/error');
            }
        }
        $this->loadModel('Office');
        $this->loadModel('Product');
        $this->loadModel('InventoryStatuses');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        $products = $this->Product->find('list');
        $inventoryStatuses = $this->InventoryStatuses->find('list');
        $this->set(compact('offices', 'products', 'inventoryStatuses'));
    }

    /**
     * admin_edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_edit($id = null) {
        $this->DistDistributorAudit->id = $id;
        if (!$this->DistDistributorAudit->exists($id)) {
            throw new NotFoundException(__('Invalid Distributor Audit'));
        }
        
         $audit_by = array(
			'1' => 'AE',
			'2' => 'TSO',
		);
        $this->set(compact('audit_by'));
        
          /****** tso and ae list start  *****/
        
        $this->loadModel('DistTso');
        $tso_list=$this->DistTso->find('list', array('order' => array('DistTso.name' => 'asc'),'recursive' => -1));
        
        $this->loadModel('DistAreaExecutive');
        $ae_list=$this->DistAreaExecutive->find('list', array('order' => array('DistAreaExecutive.name' => 'asc'),'recursive' => -1));
        $this->set(compact('tso_list','ae_list'));
        
        /****** tso and ae list end  *****/
        
        
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->loadModel('DistDistributorAuditDetail');
            $dist_distributor_audit_id = $this->request->data['DistDistributorAudit']['id'];
            $this->DistDistributorAuditDetail->query("delete from dist_distributor_audit_details where dist_distributor_audit_id = $dist_distributor_audit_id");
            $i = 0;
            $array = array();
            foreach ($this->request->data['product_id'] as $key => $value) {
                $array[$i]['dist_distributor_audit_id'] = $dist_distributor_audit_id;
                $array[$i]['product_id'] = $value;
                $array[$i]['measurement_unit_id'] = $this->get_base_measurement_unit($this->request->data['measurement_unit'][$key], $value);
                $array[$i]['qty'] = $this->request->data['quantity'][$key];
                $array[$i]['expire_date'] = date("Y-m-d", strtotime($this->request->data['expire_date'][$key]));
                $array[$i]['batch_number'] = $this->request->data['batch_number'][$key];
                $array[$i]['qty'] = $this->request->data['quantity'][$key];
                $array[$i]['created_at'] = $this->current_datetime();
                $array[$i]['created_by'] = $this->UserAuth->getUserId();
                $array[$i]['updated_at'] = $this->current_datetime();
                $array[$i]['updated_by'] = $this->UserAuth->getUserId();
                $i++;
            }
            if ($this->DistDistributorAuditDetail->saveAll($array)) {
                $this->Session->setFlash(__('Data has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('Data could not be saved. Please, try again.'), 'flash/error');
            }
        } else {
            $options = array('conditions' => array('DistDistributorAudit.' . $this->DistDistributorAudit->primaryKey => $id));
            $this->request->data = $this->DistDistributorAudit->find('first', $options);
            $this->request->data['DistDistributorAudit']['audit_date'] = date("d-m-Y", strtotime($this->request->data['DistDistributorAudit']['audit_date']));
            $this->loadModel('DistTso');
            $conditions = array(
                'DistTso.office_id' => $this->request->data['DistDistributorAudit']['office_id'],
                'DistTsoMapping.dist_distributor_id' => $this->request->data['DistDistributorAudit']['dist_distributor_id']
            );
            $joins = array(
                array(
                    'table' => 'dist_tso_mappings',
                    'alias' => 'DistTsoMapping',
                    'type' => 'Inner',
                    'conditions' => 'DistTsoMapping.dist_tso_id=DistTso.id'
                )
            );
            $data = $this->DistTso->find('all', array(
                'conditions' => $conditions,
                'joins' => $joins,
                'order' => array('DistTso.name' => 'ASC'),
                'recursive' => -1
            ));
            foreach ($data as $key => $value) {
             $distTsos[$value['DistTso']['id']]=$value['DistTso']['name'];
            }
            /* $this->loadModel('MeasurementUnit');
              $sales_measurement_unit_id = $this->get_sales_measurement_unit($this->request->data['MeasurementUnit']['id'], $this->request->data['Product']['id']);
              $products = $this->MeasurementUnit->find('first', array(
              'conditions' => array(
              'MeasurementUnit.id' => $sales_measurement_unit_id,
              ),
              'recursive' => -1
              ));
              $this->request->data['MeasurementUnit'] = $products['MeasurementUnit']; */
        }

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
        $distDistributors = $this->DistDistributorAudit->DistDistributor->find('list');
        $distDistributorAudits = $this->DistDistributorAudit->find('all', array('conditions' => array('DistDistributorAudit.id' => $id)));
     
        //pr($distDistributorAudits);die();
//$measurementUnits = $this->DistDistributorAudit->MeasurementUnit->find('list');
        
        $this->loadModel('Product');
        $this->loadModel('MeasurementUnit');

        $products = $this->Product->find('list');
        $measurementUnits = $this->MeasurementUnit->find('list');
        //$measurementUnits=array();
        $distTsos=array();
        $offices = $this->DistDistributorAudit->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        
        $ae_id=$distDistributorAudits[0]['DistDistributorAudit']['dist_ae_id'];
        $tso_id=$distDistributorAudits[0]['DistDistributorAudit']['dist_tso_id'];
        
        $ae_name="";
        $tso_name="";
        if(array_key_exists($ae_id, $ae_list))
        {
            $ae_name=$ae_list[$ae_id];
        }
        
        if(array_key_exists($tso_id, $tso_list))
        {
            $tso_name=$tso_list[$tso_id];
        }
        
        
        $this->set(compact('ae_name','tso_name','distTsos','measurementUnits', 'distDistributors', 'products', 'offices', 'distDistributorAudits'));
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
        $this->DistDistributorAudit->id = $id;
        if (!$this->DistDistributorAudit->exists()) {
            throw new NotFoundException(__('Invalid Distributor Audit'));
        }
        if ($this->DistDistributorAudit->delete()) {
            $this->loadModel('DistDistributorAuditDetail');
            $this->DistDistributorAuditDetail->query("delete from dist_distributor_audit_details where dist_distributor_audit_id = $id");
            $this->flash(__('Distributor Audit deleted'), array('action' => 'index'));
        }
        $this->flash(__('Distributor Audit was not deleted'), array('action' => 'index'));
        $this->redirect(array('action' => 'index'));
    }

    public function get_measurement_units_list() {
        $this->loadModel('MeasurementUnit');
        $product_id = $this->request->data('product_id');
        $data = $this->MeasurementUnit->query("select measurement_units.id,measurement_units.name from measurement_units inner join products on measurement_units.id=products.sales_measurement_unit_id where products.id=$product_id");

        foreach ($data as $key => $value) {
            $array[0]['id'] = $value[0]['id'];
            $array[0]['name'] = $value[0]['name'];
        }
        if (!empty($array)) {
            echo json_encode($array);
        }
        $this->autoRender = false;
    }
    
    	public function get_storage_product(){
		$this->loadModel('DistCurrentInventory');
		$office_id = $this->request->data('office_id');
		$dist_distributor_id = $this->request->data('dist_distributor_id');
		$product_id = $this->request->data('product_id');
                $measurement_unit_id = $this->request->data('measurement_unit_id');
                if(!$measurement_unit_id)
                {
                   $measurement_unit_id=7; 
                }
                
		$data = $this->DistCurrentInventory->query("
 select sum(qty) as sum from dist_current_inventories ci
left join dist_stores s on ci.store_id=s.id   
where s.office_id=$office_id and s.dist_distributor_id=$dist_distributor_id and ci.product_id=$product_id");

		if(!empty($data[0][0]['sum'])){
                    echo $this->unit_convertfrombase($product_id, $measurement_unit_id,$data[0][0]['sum']);
			// echo  $data[0][0]['sum'];
		}else{
			echo  0;
		}
		die();
	}
        
        
         public function get_tso_ae_list() {
         
        $this->loadModel('DistTsoMappingHistory');  
        
        $this->loadModel('DistTso');
        $tso_list=$this->DistTso->find('list', array('order' => array('DistTso.name' => 'asc'),'recursive' => -1));
        
        $this->loadModel('DistAreaExecutive');
        $ae_list=$this->DistAreaExecutive->find('list', array('order' => array('DistAreaExecutive.name' => 'asc'),'recursive' => -1));
       
         
        
        $distributor_id = $this->request->data['dist_distributor_id'];
        $office_id = $this->request->data['office_id'];
        $audit_date = $this->request->data['audit_date'];
      
         $output="";
        if ($audit_date && $distributor_id && $office_id) {
                
                $audit_date = date("Y-m-d H:i:s", strtotime($audit_date));
                $qry="select distinct dist_tso_id from dist_tso_mapping_histories
                      where office_id=$office_id and is_change=1 and dist_distributor_id=$distributor_id and 
                        '".$audit_date."' between effective_date and 
                        case 
                        when end_date is not null then 
                         end_date
                        else 
                        getdate()
                        end";

                $dist_data=$this->DistTsoMappingHistory->query($qry);
                $dist_ids=array();
               
                foreach ($dist_data as $k => $v) {
                    $dist_ids[]=$v[0]['dist_tso_id'];
                }
                $tso_id="";
                if($dist_ids)
                {
                    $tso_id=$dist_ids[0];
                }
                
                
                $qry2="select distinct dist_area_executive_id from dist_tso_histories where tso_id=$tso_id and (is_added=1 or is_transfer=1) and 
                    '".$audit_date."' between effective_date and 
                    case 
                    when effective_end_date is not null then 
                     effective_end_date
                    else 
                    getdate()
                    end";

                $ae_data=$this->DistTsoMappingHistory->query($qry2);
                $ae_ids=array();
               
                foreach ($ae_data as $k => $v) {
                    $ae_ids[]=$v[0]['dist_area_executive_id'];
                }
                $ae_id="";
                
                if($ae_ids)
                {
                    $ae_id=$ae_ids[0];
                }

                echo $ae_id. "||" .$ae_list[$ae_id]. "||" .$tso_id. "||" .$tso_list[$tso_id];
        } else {
            echo "";
        }

        $this->autoRender = false;
    }
    public function get_product_list_from_dist_id() {

        $office_id = $this->request->data['office_id'];
        $dist_distributor_id = $this->request->data['dist_distributor_id'];
        $output = "<option value=''>--- Select Product ---</option>";
        if ($dist_distributor_id) {
            $this->loadModel('DistStore');
            $this->loadModel('DistCurrentInventory');

            $store_id_arr = $this->DistStore->find('first', array(
                'conditions' => array(
                    'DistStore.dist_distributor_id' => $dist_distributor_id,
                    'DistStore.office_id' => $office_id
                )
            ));

            $store_id = $store_id_arr['DistStore']['id'];
       
           
            $products = $this->DistCurrentInventory->find('all', array(
                'fields' => array('Product.id', 'Product.name'),
                'conditions' => array(
                    'DistCurrentInventory.store_id' => $store_id
                ),
                'order' => array('Product.order' => 'asc'),
                'recursive' => 0
            ));
            
            /*$this->loadModel('Product');
            $products = $this->Product->find('list', array(
                'fields' => array('Product.id', 'Product.name'),
                'order' => array('Product.order' => 'asc'),
                'recursive' => 0
            ));*/
            $data_array = array();
            if ($products) {
                foreach ($products as $key => $val) {
                    $output .= "<option value=".$val['Product']['id'].">".$val['Product']['name']."</option>";
                    //$data_array[$val['Product']['id']] = $val['Product']['name'];
                }
            }
        }
        /*if($products){
            echo json_encode($data_array);
        }else{
            echo json_encode(array());
        }*/
        echo $output;
        $this->autoRender = false; 
    }

}
