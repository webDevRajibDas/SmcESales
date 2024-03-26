<?php

App::uses('AppController', 'Controller');

/**
 * Challans Controller
 *
 * @property Challan $Challan
 * @property PaginatorComponent $Paginator
 */
class DistBonusProductIssuesController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Filter.Filter');
    public $uses = array('DistChallan', 'DistChallanDetail', 'DistStore', 'Product', 'Territory', 'CurrentInventory', 'SalesPerson','User','MeasurementUnit');

    /**
     * admin_index method
     *
     * @return void
     */
  public function admin_index() {

        $this->loadModel('Office');
        $this->loadModel('DistDistributor');
        
        $this->set('page_title', 'Distributor Challan List');
        
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_conditions = array('Office.office_type_id' =>2);
            $dist_challan_conditions = array(
                    'DistChallan.inventory_status_id' => 1,
                    'DistChallan.is_bonus_challan' => 1,
                    /*'OR'=>array(
                      array('DistChallan.transaction_type_id' => 45), //ASO TO SO (Product Issue)
                      array('DistChallan.transaction_type_id' => 46), //ASO TO SO (Product Issue received)
                      )*/
                  );
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(),'Office.office_type_id' =>2);
            $dist_challan_conditions = array(
              'DistChallan.office_id'=>$this->UserAuth->getOfficeId(),
              'DistChallan.inventory_status_id' => 1,
              'DistChallan.is_bonus_challan' => 1,
                    /*'OR'=>array(
                      array('DistChallan.transaction_type_id' => 45), //ASO TO SO (Product Issue)
                      array('DistChallan.transaction_type_id' => 46), //ASO TO SO (Product Issue received)
                      )*/
                  );
        }
        
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
        
        
        $office_id = isset($this->request->data['DistChallan']['office_id']) != '' ? $this->request->data['DistChallan']['office_id'] : 0;
        
        $distributors = $this->DistDistributor->find('list', array('conditions' => array('DistDistributor.office_id'=>$office_id), 'order' => array('DistDistributor.name' => 'asc')));
        $distributors_all = $this->DistDistributor->find('list', array('order' => array('DistDistributor.name' => 'asc')));
       
       $dist_distributor_id = isset($this->request->data['DistChallan']['dist_distributor_id']) != '' ? $this->request->data['DistChallan']['dist_distributor_id'] : 0;
        
       $this->loadModel('Territory'); 
       $territories = $this->Territory->find('list');
       
       
        $this->DistChallan->recursive = 0;
        $this->paginate = array(
            'conditions' => $dist_challan_conditions,
            'recursive' => 0,
            'order' => array('DistChallan.id' => 'desc')
        );
       
        
        $this->set('challans', $this->paginate());
        
        $this->set(compact('offices', 'office_id', 'distributors','dist_distributor_id','territories','distributors_all'));
    }

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_view($id = null) {

        $this->set('page_title', 'Distributor Challan Details');
        if (!$this->DistChallan->exists($id)) {
            throw new NotFoundException(__('Invalid challan'));
        }
        $options = array(
            'conditions' => array(
                'DistChallan.' . $this->DistChallan->primaryKey => $id
            ),
            'recursive' => 0
        );
        $challan = $this->DistChallan->find('first', $options);
        $challandetail = $this->DistChallanDetail->find('all', array(
            'conditions' => array('DistChallanDetail.challan_id' => $challan['DistChallan']['id']),
            'order' => array('Product.order' => 'asc'),
            'fields' => 'DistChallanDetail.*,Product.product_code,Product.name,MeasurementUnit.name',
            'recursive' => 0
                )
        );
        //pr($challandetail);die();
        $office_paren_id = $this->UserAuth->getOfficeParentId();
        
        
         $this->loadModel('DistDistributor');
         $distributors_all = $this->DistDistributor->find('list', array('order' => array('DistDistributor.name' => 'asc')));
         $this->set(compact('distributors_all'));
        
        if ($this->request->is('post')) {
            //pr($this->request->data);die();
            if($this->request->data['DistChallan']['received_date'])
            {
                //pr($this->request->data);die();
                if($challan['DistChallan']['status'] > 1)
                {
                    $this->Session->setFlash(__('Challan has already received.'), 'flash/success');
                    $this->redirect(array('action' => 'index'));
                }
                
                // update dms inventory 
                $this->loadModel('DistChallan');
                
                $chalan_received_date = date('Y-m-d', strtotime($this->request->data['DistChallan']['received_date']));
                $chalan_updated_by = $this->UserAuth->getUserId();
                $dist_store_id=$challan['DistChallan']['receiver_dist_store_id'];
                $memo_date=$challan['DistChallan']['challan_date'];        
                $sql="exec received_dist_challan_from_memo $id,$chalan_updated_by,'$chalan_received_date',$dist_store_id,'$memo_date'";
                $result = $this->DistChallan->query($sql);
            
                $this->Session->setFlash(__('Challan has been received.'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            }
            else
            {
                $this->Session->setFlash(__('Challan Received Date is required.'), 'flash/error');
                $this->redirect(array('action' => 'view/'.$id));    
            }
        }
        
       $this->loadModel('Territory'); 
       $territories = $this->Territory->find('list',array('conditions'=>array('id'=>$challan['SalesPerson']['territory_id'])));
       
        $this->set(compact('challan', 'challandetail', 'office_paren_id','territories'));
    }

    /**
     * admin_add method
     *
     * @return void
     */
public function admin_add() {
  $this->set('page_title', 'New Bonus Product Issue');
  $this->loadModel('DistStore');
  $this->loadModel('OpenCombination');
  $this->loadModel('OpenCombinationProduct');
  $this->loadModel('Product');
  $this->loadModel('DistDistributor');
  $this->loadModel('DistOutlet');
  $this->loadModel('Outlet');
  $this->loadModel('Market');
  $this->loadModel('Memo');
  $this->loadModel('MemoDetail');
  $Store = $this->DistStore->find('all', array(
      'fields'=>array('DistStore.id','DistDistributor.name'),
      'conditions' => array('DistStore.office_id' => $this->UserAuth->getOfficeId(), 'DistDistributor.is_active'=>1),
      'joins' => array(
            array(
                'table' => 'dist_distributors',
                'alias' => 'DistDistributor',
                'type' => 'INNER',
                'conditions' => array(
                    'DistDistributor.id = DistStore.dist_distributor_id'
                )
            )
        ),
      'order' => array('DistDistributor.name' => 'asc'),
      'recursive'=>-1
    ));

    $receiverStore=array();
    foreach($Store as $data){
      $receiverStore[$data ['DistStore']['id']]=$data['DistDistributor']['name'];
    }

    /*$current_date =date('Y-m-d');
    $previous_six_month = date('Y-m-d',strtotime($current_date.'-6 month'));

    $oc_data = $this->OpenCombination->find('list',array(
      'conditions'=>array(
        'start_date >='=>$previous_six_month,
        //'end_date >='=>$current_date,
        'is_bonus'=> 1,
      ),
      'fields'=>array('OpenCombination.id','OpenCombination.name')
    ));
    
    $product_from_oc = $this->OpenCombinationProduct->find('all',array(
      'conditions'=>array(
        'OpenCombinationProduct.combination_id'=>array_keys($oc_data),
      ),
      'joins'=> array(
        array(
          'alias' => 'PC',
          'table' => 'products',
          'type' => 'INNER',
          'conditions' => 'PC.id = OpenCombinationProduct.product_id'
        ),
      ),
      'fields'=>array('OpenCombinationProduct.product_id','PC.name'),
      'group'=> array('OpenCombinationProduct.product_id','PC.name')
    ));
    
    $product_ci=array();
    foreach ($product_from_oc as $each_ci) {
      $products[$each_ci['OpenCombinationProduct']['product_id']] = $each_ci['PC']['name'];
    }*/
    $this->loadModel('Product');
    $this->loadModel('CurrentInventory');
    $store_id = $this->UserAuth->getStoreId();
    $conditions=array('CurrentInventory.store_id' => $store_id,'CurrentInventory.qty > ' => 0,'inventory_status_id'=>1);
        $products_from_ci = $this->CurrentInventory->find('all', array('fields' => array('DISTINCT CurrentInventory.product_id'),
            'conditions' => $conditions,
        ));
        
        $product_ci=array();
        foreach ($products_from_ci as $each_ci) {
            $product_ci[]=$each_ci['CurrentInventory']['product_id'];
        }
        //pr($product_ci);die();
        $products = $this->Product->find('list', array(
            'conditions' => array(
               
                'id' => $product_ci,
                //'is_distributor_product'=> 1,
            ),
            'order' => array('order' => 'asc'),
            
            ));
    $this->set(compact('receiverStore', 'products'));


    if ($this->request->is('post')) {
      //pr($this->request->data);die();
      $w_store_id = $this->UserAuth->getStoreId();
      
      $dist_store_id = $this->request->data['Challan']['receiver_store_id'];
      $dist_stores= $this->DistStore->find('first',array(
        'conditions'=>array('DistStore.id'=>$dist_store_id),
        'recursive'=> -1,
      ));
      
      $dist_distributor_id =  $dist_stores['DistStore']['dist_distributor_id'];
      $office_id =  $dist_stores['DistStore']['office_id'];
      $dist_distributors = $this->DistDistributor->find('first',array(
        'conditions'=>array('DistDistributor.id'=>$dist_distributor_id),
      ));

      $outlet_id = $dist_distributors['DistOutletMap']['outlet_id'];
      $market_id = $dist_distributors['DistOutletMap']['market_id'];
      
      $territory_info=$this->Territory->find('first',array(
        'conditions'=> array(
            'Territory.office_id'=>$office_id,
            'Territory.name LIKE'=> '%Corporate Territory%',
        ),
       
      ));

      $territory_id=$territory_info['Territory']['id'];
      $sales_person_id = $territory_info['SalesPerson']['id'];
      $outlets = $this->Outlet->find('first',array(
        'conditions'=>array('Outlet.id'=>$outlet_id),
      ));
      $thana_id = $outlets['Market']['thana_id'];
      $user_id = $this->UserAuth->getUserId();
      $generate_memo_no = $user_id.date('d').date('m').date('h').date('i').date('s');
     
      //pr($outlets);die();
        $memo = array();
        $memo['sales_person_id'] = $sales_person_id;
        $memo['entry_date'] = $this->current_datetime();
        $memo['memo_date'] = $this->current_datetime();
    
        $memo['office_id'] = $office_id;
        $memo['sale_type_id'] = 1;
        $memo['territory_id'] = $territory_id;
        $memo['thana_id'] = $thana_id;
        $memo['market_id'] = $market_id;
        $memo['outlet_id'] = $outlet_id;

        $memo['memo_date'] = $this->current_datetime();
        $memo['memo_no'] = $generate_memo_no;
        $memo['gross_value'] = 0;
        $memo['is_active'] = 1;
        $memo['is_distributor'] = 1;
        $memo['w_store_id']= $w_store_id;
        $memo['status'] = 2;
        $memo['memo_time'] = $this->current_datetime();  
        $memo['from_app'] = 0;
        $memo['action'] = 1;
        $memo['is_program'] = 0;
        $memo['created_at'] = $this->current_datetime();
        $memo['created_by'] = $this->UserAuth->getUserId();
        $memo['updated_at'] = $this->current_datetime();
        $memo['updated_by'] = $this->UserAuth->getUserId();

        $this->Memo->create();
        if ($this->Memo->save($memo)) {

        $memo_id = $this->Memo->getLastInsertId();
        $memo_info_arr = $this->Memo->find('first',array(
        'conditions' => array(
            'Memo.id'=> $memo_id                    
            )
        ));

        if ($memo_id) 
        {
            $all_product_id=$this->request->data['product_id'];
            if (!empty($this->request->data['quantity'])) 
            {
                $this->loadModel('MemoDetail');
                $total_product_data = array();
                $memo_details = array();
                $memo_details['MemoDetail']['memo_id'] = $memo_id;
                
                foreach($this->request->data['product_id'] as $key => $val) 
                {   
                  $product_id = $val;

                  //-------virtual product---------\\

                  $product_details = $this->Product->find('first', array(
                      'fields' => array('id', 'is_virtual', 'parent_id'),
                      'conditions' => array('Product.id' => $product_id),
                      'recursive' => -1
                  ));

                  //$memo_details['MemoDetail']['product_id'] =$product_id ;

                  if ($product_details['Product']['is_virtual'] == 1) {
                      $memo_details['MemoDetail']['virtual_product_id'] = $product_id;
                      $memo_details['MemoDetail']['product_id'] = $product_details['Product']['parent_id'];
                  } else {
                      $memo_details['MemoDetail']['virtual_product_id'] = 0;
                      $memo_details['MemoDetail']['product_id'] = $product_details['Product']['id'];
                  }

                  $memo_details['MemoDetail']['measurement_unit_id'] = $this->request->data['measurement_unit'][$key];
                  $memo_details['MemoDetail']['price'] = 0;
                  $memo_details['MemoDetail']['sales_qty'] = $this->request->data['quantity'][$key];
                  
                  $memo_details['MemoDetail']['product_price_id'] = NULL;
                  $memo_details['MemoDetail']['bonus_qty'] = $this->request->data['quantity'][$key];
                  $memo_details['MemoDetail']['offer_id'] = 0;
                  $memo_details['MemoDetail']['bonus_product_id'] = $product_id;
                  $memo_details['MemoDetail']['bonus_id'] = 0;
                  $memo_details['MemoDetail']['bonus_scheme_id'] = 0;
                  $memo_details['MemoDetail']['price_combination_id'] = NULL;
                  $memo_details['MemoDetail']['product_combination_id'] = NULL;
                  $memo_details['MemoDetail']['is_bonus'] = 1;
                 
                  $total_product_data[] = $memo_details;
                }
              
                $this->MemoDetail->saveAll($total_product_data);
            }

            /************* create Challan *************/
            $this->loadModel('DistChallan');
            $this->loadModel('DistChallanDetail');
            $this->loadModel('CurrentInventory');
            
            $store_id=$w_store_id;
          
            $challan['office_id']=$office_id;
            $challan['memo_id']=$memo_info_arr['Memo']['id'];
            $challan['memo_no']=$memo_info_arr['Memo']['memo_no'];
            $challan['challan_no']=$memo_info_arr['Memo']['memo_no'];
            $challan['receiver_dist_store_id']= $dist_store_id;
            $challan['is_bonus_challan']=1;
            $challan['receiving_transaction_type']=2;
            $challan['received_date']='';
            $challan['challan_date']=$this->current_datetime();
            $challan['dist_distributor_id']=$dist_distributor_id;
            $challan['challan_referance_no']='';
            $challan['challan_type']="";
            $challan['remarks']=0;
            $challan['status']=0;
            $challan['so_id']=$sales_person_id;
            $challan['is_close']=0;
            $challan['inventory_status_id']=1;
            //$challan['transaction_type_id']=45;
            $challan['transaction_type_id']= 11;
            $challan['sender_store_id']=$store_id;
            $challan['created_at'] = $this->current_datetime();
            $challan['created_by'] = $this->UserAuth->getUserId();
            $challan['updated_at'] = $this->current_datetime();
            $challan['updated_by'] = $this->UserAuth->getUserId();
            //pr();die();
            $this->DistChallan->create();
           // pr($challan);
            if ($this->DistChallan->save($challan)) 
            {

                $challan_id = $this->DistChallan->getLastInsertId();
                if($challan_id){

                    $challan_no = 'Ch-'.$dist_distributor_id.'-'.date('Y').'-'.$challan_id;

                    $challan_data['id'] = $challan_id;
                    $challan_data['challan_no'] = $challan_no;
                    
                    $this->DistChallan->save($challan_data);
                }
                $product_list=$this->request->data;
                if (!empty($product_list['product_id'])) {
                    $data_array = array();

                    foreach ($product_list['product_id'] as $key => $val) {
                      if(!empty($product_list['quantity'][$key])){

                        //-------virtual product---------\\

                        $product_details = $this->Product->find('first', array(
                            'fields' => array('id', 'is_virtual', 'parent_id'),
                            'conditions' => array('Product.id' => $val),
                            'recursive' => -1
                        ));

                        //$data['DistChallanDetail']['product_id'] = $val;

                        if ($product_details['Product']['is_virtual'] == 1) {
                            $data['DistChallanDetail']['virtual_product_id'] = $val;
                            $data['DistChallanDetail']['product_id'] = $product_details['Product']['parent_id'];
                        } else {
                            $data['DistChallanDetail']['virtual_product_id'] = 0;
                            $data['DistChallanDetail']['product_id'] = $product_details['Product']['id'];
                        }

                          $data['DistChallanDetail']['challan_id'] = $this->DistChallan->id;
                          $data['DistChallanDetail']['expire_date'] = $product_list['expire_date'][$key];
                          $data['DistChallanDetail']['challan_qty'] =$product_list['quantity'][$key];
                          $data['DistChallanDetail']['received_qty'] =$product_list['quantity'][$key];
                          $data['DistChallanDetail']['batch_no'] = $product_list['batch_no'][$key];
                          $data['DistChallanDetail']['measurement_unit_id'] = $product_list['measurement_unit'][$key];
                          $data['DistChallanDetail']['price'] = 0;
                          $data['DistChallanDetail']['is_bonus'] = 1;

                          $data_array[] = $data;
                        }
                      }
                    }
                   $this->DistChallanDetail->saveAll($data_array);
                   
                }
                $this->Session->setFlash(__('Bonus Product issue has been Drafted.'), 'flash/success');
                $this->redirect(array('action' => 'edit',$this->DistChallan->id));
              }  
              /************* end Challan *************/
            }
       }
    
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
    $this->DistChallan->id = $id;
    if (!$this->DistChallan->exists()) {
      throw new NotFoundException(__('Invalid challan'));
    }
    if ($this->DistChallan->delete()) {
      $this->flash(__('Challan deleted'), array('action' => 'index'));
    }
    $this->flash(__('Challan was not deleted'), array('action' => 'index'));
    $this->redirect(array('action' => 'index'));
}

public function admin_edit($id = null) 
{
  $this->loadModel('DistStore');
  $this->loadModel('OpenCombination');
  $this->loadModel('OpenCombinationProduct');
  $this->loadModel('Product');
  $this->loadModel('DistDistributor');
  $this->loadModel('DistOutlet');
  $this->loadModel('Outlet');
  $this->loadModel('Market');
  $this->loadModel('Memo');
  $this->loadModel('MemoDetail');
  $this->set('page_title', 'Edit Bonus Product Issue');
  $stock_available=1;
  $this->DistChallan->unbindModel(
     array('belongsTo' => array('TransactionType','SenderStore','ReceiverStore','Requisition'))
    );
  $challan_info = $this->DistChallan->find('first', array(
    'conditions'=>array('DistChallan.id'=>$id),
    'recursive'=>1
  ));

  foreach($challan_info['DistChallanDetail'] as $key=>$data)
  {
    if($data['virtual_product_id'] > 0){
      $data['product_id'] = $data['virtual_product_id'];
    }

    $measurement_unit=$this->MeasurementUnit->find('first',array(
      'conditions'=>array('MeasurementUnit.id'=>$data['measurement_unit_id']),
      'recursive'=>-1
      ));
    $challan_info['DistChallanDetail'][$key]['MeasurementUnit']=$measurement_unit['MeasurementUnit'];

    $product=$this->Product->find('first',array(
      'conditions'=>array('Product.id'=>$data['product_id']),
      'recursive'=>-1
      ));
    $challan_info['DistChallanDetail'][$key]['Product']=$product['Product'];
  }
  
  /*if($challan_info['DistChallan']['status']>0)
  {
    $this->Session->setFlash(__('Product issue already has been Updated.'), 'flash/warning');
    $this->redirect(array('action' => 'index'));
  }*/
  if ($this->request->is('post')) 
  {
    
    $stock_avail=1;
    foreach ($this->request->data['product_qty'] as $k => $v)
    {
      if($this->request->data['product_qty'][$k]<$this->request->data['quantity'][$k])
      {
        $stock_avail=0;
        break; 
      }
    }

    if(!$stock_avail)
    {
      $this->Session->setFlash(__('Quantity should be less then equal Stock quantity.'), 'flash/error');
      $this->redirect(array('action' => 'edit',$id));
    }



    $store_id = $challan_info['DistChallan']['sender_store_id'];
    $no_of_product = count($challan_info['DistChallanDetail']);

    /*if (!array_key_exists('draft', $this->request->data)) {
    for ($challan_detail_count=0; $challan_detail_count < $no_of_product; $challan_detail_count++) {
    $product_id = $challan_info['ChallanDetail'][$challan_detail_count]['product_id'];
    $sales_qty = $challan_info['ChallanDetail'][$challan_detail_count]['challan_qty'];
    $measurement_unit_id = $challan_info['ChallanDetail'][$challan_detail_count]['measurement_unit_id'];
    $base_quantity = $this->unit_convert($product_id,$measurement_unit_id,$sales_qty);
    $update_type = 'add';
    $this->update_current_inventory($base_quantity,$product_id,$store_id,$update_type);
    }
    }*/

  $challan_id = $id;
  date_default_timezone_set('Asia/Dhaka');		   
  $yesterday = date('Y-m-d', strtotime('-1 day'));
  $today = date('Y-m-d');
  $challan_date = $this->request->data['DistChallan']['challan_date'];
  if($challan_date!=$yesterday && $challan_date!=$today)
  {
    $this->Session->setFlash(__('Challan Date must be yesterday or today.'), 'flash/error');
    $this->redirect(array('action' => 'edit', $challan_id));
    exit;
  }

  // $this->ChallanDetail->deleteAll(array('ChallanDetail.challan_id'=>$challan_id));

  if (empty($this->request->data['product_id'])) 
  {
    $this->Session->setFlash(__('Bonus Product issue not created.'), 'flash/error');
    $this->redirect(array('action' => 'index'));
  } 
  else 
  {

        $this->request->data['DistChallan']['transaction_type_id'] = 11; //ASO TO SO (Product Issue)
        $this->request->data['DistChallan']['inventory_status_id'] = 1;
        $this->request->data['DistChallan']['is_bonus_challan'] = 1;
        $this->request->data['DistChallan']['challan_date'] = date('Y-m-d', strtotime($this->request->data['DistChallan']['challan_date']));
        $this->request->data['DistChallan']['sender_store_id'] = $this->UserAuth->getStoreId();
        $this->request->data['DistChallan']['updated_at'] = $this->current_datetime();
        $this->request->data['DistChallan']['updated_by'] = $this->UserAuth->getUserId();
        
        /*$challan_update_set_sql="
            receiver_store_id=".$this->request->data['DistChallan']['receiver_store_id'].",
            sender_store_id=".$this->request->data['DistChallan']['sender_store_id'].",
            challan_date = '".$this->request->data['DistChallan']['challan_date']."',
            remarks = '".$this->request->data['DistChallan']['remarks']."',
            carried_by = '".$this->request->data['DistChallan']['carried_by']."',
            truck_no = '".$this->request->data['DistChallan']['truck_no']."',
            driver_name = '".$this->request->data['DistChallan']['driver_name']."',
            inventory_status_id = '".$this->request->data['DistChallan']['inventory_status_id']."',
            transaction_type_id = '".$this->request->data['DistChallan']['transaction_type_id']."',
            updated_at = '".$this->request->data['DistChallan']['updated_at']."',
            updated_by = '".$this->request->data['DistChallan']['updated_by']."',
          ";*/
        $challan_update_set_sql="
            receiver_dist_store_id=".$this->request->data['DistChallan']['receiver_store_id'].",
            sender_store_id=".$this->request->data['DistChallan']['sender_store_id'].",
            challan_date = '".$this->request->data['DistChallan']['challan_date']."',
            remarks = '".$this->request->data['DistChallan']['remarks']."',
            inventory_status_id = '".$this->request->data['DistChallan']['inventory_status_id']."',
            transaction_type_id = '".$this->request->data['DistChallan']['transaction_type_id']."',
            updated_at = '".$this->request->data['DistChallan']['updated_at']."',
            updated_by = '".$this->request->data['DistChallan']['updated_by']."',
            is_bonus_challan = 1, 
          ";
        $challan_update_conditions="id=$id";
        // $challan_update_conditions=array("id"=>$id);
        if (array_key_exists('draft', $this->request->data)) 
        {
          $this->request->data['DistChallan']['status'] = 0;
          // $challan_update_set_sql['status']=0;
          $challan_update_set_sql.="status=0";
        }
        else
        {
          $this->request->data['DistChallan']['status'] = 1;
          $challan_update_set_sql.="status=1";
          //$challan_update_set_sql['status']=1;
          $challan_update_conditions.="AND status=0";
          //$challan_update_conditions['status']=0;
        }

        $prev_challan_status=$this->DistChallan->query("SELECT * FROM dist_challans WHERE $challan_update_conditions");
        $challan_update=0;
        
        if($prev_challan_status)
        {
          $challan_update=$this->DistChallan->query("UPDATE dist_challans set $challan_update_set_sql WHERE $challan_update_conditions");
        }
        // $this->request->data['Challan']['id'] = $id;
        // if ($this->Challan->save($this->request->data)) 
        
        if ($challan_update) 
        {
          $this->DistChallanDetail->deleteAll(array('DistChallanDetail.challan_id'=>$challan_id));
          // $udata['id'] = $this->Challan->id;
          // $udata['challan_no'] = 'PI' . (10000 + $this->Challan->id);
          if (!empty($this->request->data['product_id'])) 
          {
            $data_array = array();
            $update_data_array = array();
            $so_update_data_array = array();
            $insert_data_array = array();
            foreach ($this->request->data['product_id'] as $key => $val) 
            {
              $data_array = array();
              $update_data_array = array();
              $data['DistChallanDetail']['challan_id'] = $id;

              //$data['DistChallanDetail']['product_id'] = $val;

              //-------virtual product---------\\

              $product_details = $this->Product->find('first', array(
                  'fields' => array('id', 'is_virtual', 'parent_id'),
                  'conditions' => array('Product.id' => $val),
                  'recursive' => -1
              ));

              if ($product_details['Product']['is_virtual'] == 1) {
                  $data['DistChallanDetail']['virtual_product_id'] = $val;
                  $data['DistChallanDetail']['product_id'] = $product_details['Product']['parent_id'];
              } else {
                  $data['DistChallanDetail']['virtual_product_id'] = 0;
                  $data['DistChallanDetail']['product_id'] = $product_details['Product']['id'];
              }



              $data['DistChallanDetail']['measurement_unit_id'] = $this->request->data['measurement_unit'][$key];
              $data['DistChallanDetail']['challan_qty'] = $this->request->data['quantity'][$key];
              $data['DistChallanDetail']['received_qty'] = $this->request->data['quantity'][$key];
              $data['DistChallanDetail']['batch_no'] = $this->request->data['batch_no'][$key];
              if($this->request->data['expire_date'][$key] != ' ' && $this->request->data['expire_date'][$key] != 'null' && $this->request->data['expire_date'][$key] !='')
              {
                $data['DistChallanDetail']['expire_date'] = date('Y-m-d', strtotime($this->request->data['expire_date'][$key]));
              }
              else 
              {
                $data['DistChallanDetail']['expire_date'] = '';
              }
              //echo $data['ChallanDetail']['expire_date'] = ' ';
              $data['DistChallanDetail']['is_bonus'] = 1;
              $data['DistChallanDetail']['inventory_status_id'] = 1;
              $data['DistChallanDetail']['remarks'] = $this->request->data['remarks'][$key];
              $data['DistChallanDetail']['source']=$this->request->data['source'][$key];
              $data_array[] = $data;
              if (array_key_exists('draft', $this->request->data)) 
              {
                // insert challan data
                $this->DistChallanDetail->saveAll($data_array);
              }
              else
              {
                $stock_available=1;
                // ------------ stock update --------------------     
                $inventory_info = $this->CurrentInventory->find('first', array(
                  'conditions' => array(
                    'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
                    'CurrentInventory.inventory_status_id' => 1,
                    'CurrentInventory.product_id' => $val,
                    'CurrentInventory.batch_number' => $this->request->data['batch_no'][$key],
                    'CurrentInventory.expire_date' => $data['DistChallanDetail']['expire_date']
                    ),
                  'recursive' => -1
                  ));

                $deduct_quantity = $this->unit_convert($val, $this->request->data['measurement_unit'][$key], $this->request->data['quantity'][$key]);

                $update_data['id'] = $inventory_info['CurrentInventory']['id'];
                $update_data['qty'] = $inventory_info['CurrentInventory']['qty'] - $deduct_quantity;
                if($update_data['qty'] < 0)
                {
                  $stock_available=0;
                }
                //$update_data['transaction_type_id'] = 2; // ASO to SO
                //$update_data['transaction_type_id'] = 46; // ASO to SO
                $update_data['transaction_type_id'] = 11; // ASO to SO
                $update_data['transaction_date'] = date('Y-m-d', strtotime($this->request->data['DistChallan']['challan_date']));
                $update_data_array[] = $update_data;

                if($stock_available)
                {
                  // Update inventory data
                  $this->CurrentInventory->saveAll($update_data_array);
                  $this->DistChallanDetail->saveAll($data_array);
                }
              }
            }
			
			//======memo details update==========\\
            $memo_id_con = "id=$challan_id";
            $challanInfo=$this->DistChallan->query("SELECT * FROM dist_challans WHERE $memo_id_con");
            $memoid = $challanInfo[0][0]['memo_id'];

            $this->MemoDetail->deleteAll(array('MemoDetail.memo_id'=>$memoid));

            $total_product_data = array();
            $memo_details = array();
            $memo_details['MemoDetail']['memo_id'] = $memoid;
            
            foreach($this->request->data['product_id'] as $key => $val) 
            {   
              $product_id = $val;

              //-------virtual product---------\\

              $product_details = $this->Product->find('first', array(
                  'fields' => array('id', 'is_virtual', 'parent_id'),
                  'conditions' => array('Product.id' => $product_id),
                  'recursive' => -1
              ));

              //$memo_details['MemoDetail']['product_id'] =$product_id ;

              if ($product_details['Product']['is_virtual'] == 1) {
                  $memo_details['MemoDetail']['virtual_product_id'] = $product_id;
                  $memo_details['MemoDetail']['product_id'] = $product_details['Product']['parent_id'];
              } else {
                  $memo_details['MemoDetail']['virtual_product_id'] = 0;
                  $memo_details['MemoDetail']['product_id'] = $product_details['Product']['id'];
              }

              $memo_details['MemoDetail']['measurement_unit_id'] = $this->request->data['measurement_unit'][$key];
              $memo_details['MemoDetail']['price'] = 0;
              $memo_details['MemoDetail']['sales_qty'] = $this->request->data['quantity'][$key];
              
              $memo_details['MemoDetail']['product_price_id'] = NULL;
              $memo_details['MemoDetail']['bonus_qty'] = $this->request->data['quantity'][$key];
              $memo_details['MemoDetail']['offer_id'] = 0;
              $memo_details['MemoDetail']['bonus_product_id'] = $product_id;
              $memo_details['MemoDetail']['bonus_id'] = 0;
              $memo_details['MemoDetail']['bonus_scheme_id'] = 0;
              $memo_details['MemoDetail']['price_combination_id'] = NULL;
              $memo_details['MemoDetail']['product_combination_id'] = NULL;
              $memo_details['MemoDetail']['is_bonus'] = 1;
            
              $total_product_data[] = $memo_details;
            }

            $this->MemoDetail->saveAll($total_product_data);
			
			
          }

          $this->Session->setFlash(__('Bonus Product issue has been Updated.'), 'flash/success');
          $this->redirect(array('action' => 'index'));
        }
        else
        {
            $this->Session->setFlash(__('Bonus Product issue Already Submitted.'), 'flash/warning');
            $this->redirect(array('action' => 'index'));
        }
  }
  }

  $Store = $this->DistStore->find('all', array(
      'fields'=>array('DistStore.id','DistDistributor.name'),
      'conditions' => array('DistStore.office_id' => $this->UserAuth->getOfficeId()),
      'joins' => array(
            array(
                'table' => 'dist_distributors',
                'alias' => 'DistDistributor',
                'type' => 'INNER',
                'conditions' => array(
                    'DistDistributor.id = DistStore.dist_distributor_id'
                )
            )
        ),
      'order' => array('DistDistributor.name' => 'asc'),
      'recursive'=>-1
    ));

    $receiverStore=array();
    foreach($Store as $data){
      $receiverStore[$data ['DistStore']['id']]=$data['DistDistributor']['name'];
    }

    /*$current_date =date('Y-m-d');
    $previous_six_month = date('Y-m-d',strtotime($current_date.'-6 month'));

    $oc_data = $this->OpenCombination->find('list',array(
      'conditions'=>array(
        //'end_date >='=>$previous_six_month,
        'start_date >='=>$previous_six_month,
        'is_bonus'=> 1,
      ),
      'fields'=>array('OpenCombination.id','OpenCombination.name')
    ));
    
    $product_from_oc = $this->OpenCombinationProduct->find('all',array(
      'conditions'=>array(
        'OpenCombinationProduct.combination_id'=>array_keys($oc_data),
      ),
      'joins'=> array(
        array(
          'alias' => 'PC',
          'table' => 'products',
          'type' => 'INNER',
          'conditions' => 'PC.id = OpenCombinationProduct.product_id'
        ),
      ),
      'fields'=>array('OpenCombinationProduct.product_id','PC.name'),
      'group'=> array('OpenCombinationProduct.product_id','PC.name')
    ));
    
    $product_ci=array();
    foreach ($product_from_oc as $each_ci) {
      $products[$each_ci['OpenCombinationProduct']['product_id']] = $each_ci['PC']['name'];
    }*/
  //$products = $this->Product->find('list', array('order' => array('order' => 'asc')));

   	$this->loadModel('Product');
    $this->loadModel('CurrentInventory');
    $store_id = $this->UserAuth->getStoreId();
    $conditions=array('CurrentInventory.store_id' => $store_id,'CurrentInventory.qty > ' => 0,'inventory_status_id'=>1);
        $products_from_ci = $this->CurrentInventory->find('all', array('fields' => array('DISTINCT CurrentInventory.product_id'),
            'conditions' => $conditions,
        ));
        
        $product_ci=array();
        foreach ($products_from_ci as $each_ci) {
            $product_ci[]=$each_ci['CurrentInventory']['product_id'];
        }
        //pr($product_ci);die();
        $products = $this->Product->find('list', array(
            'conditions' => array(
               
                'id' => $product_ci,
                //'is_distributor_product'=> 1,
            ),
            'order' => array('order' => 'asc'),
            
            ));

  $product_source = $this->Product->find('list', array('fields' => 'source'));


  foreach ($challan_info['DistChallanDetail'] as $key => $value) {

    if($value['virtual_product_id'] > 0){
      $value['product_id'] = $value['virtual_product_id'];
    }
   
    $current_inventory_info = $this->CurrentInventory->find('first', array(
      'conditions'=>array(
        'CurrentInventory.store_id' => $this->UserAuth->getStoreId(),
        'CurrentInventory.product_id' => $value['product_id'],
        'CurrentInventory.batch_number' => $value['batch_no'],
        'CurrentInventory.expire_date' => $value['expire_date'],
        'CurrentInventory.inventory_status_id' => 1
        ),
      'recursive' => -1
      ));
          $challan_info['DistChallanDetail'][$key]['stock_qty'] = $this->unit_convertfrombase($value['product_id'],$value['Product']['challan_measurement_unit_id'],$current_inventory_info['CurrentInventory']['qty']);
  }

// redirect to index page if previously submitted this challan to server


  $this->set(compact('receiverStore', 'products', 'challan_info', 'product_source'));
}

public function update_current_inventory($quantity,$product_id,$store_id,$update_type = 'deduct')
{
  $this->loadModel('CurrentInventory'); 

  $find_type = 'all';
  if($update_type == 'add')
    $find_type = 'first';
  $inventory_info = $this->CurrentInventory->find($find_type,array(
    'conditions' => array(
      'CurrentInventory.qty >' => 0,
      'CurrentInventory.store_id' => $store_id,
      'CurrentInventory.inventory_status_id' => 1,
      'CurrentInventory.product_id' => $product_id
      ),
    'order' => array('CurrentInventory.expire_date' => 'asc'),      
    'recursive' => -1       
    ));

  if($update_type == 'deduct')
  {
    foreach($inventory_info as $val)
    {
      if($quantity <= $val['CurrentInventory']['qty'])
      {
        $this->CurrentInventory->id = $val['CurrentInventory']['id'];
        $this->CurrentInventory->updateAll(array('CurrentInventory.qty' => 'CurrentInventory.qty - '.$quantity),array('CurrentInventory.id' => $val['CurrentInventory']['id']));
        break;
      }else{
        $quantity = $quantity - $val['CurrentInventory']['qty'];
        $this->CurrentInventory->id = $val['CurrentInventory']['id'];
        $this->CurrentInventory->updateAll(array('CurrentInventory.qty' => 'CurrentInventory.qty - '.$val['CurrentInventory']['qty']),array('CurrentInventory.id' => $val['CurrentInventory']['id']));
      }
    }
  }else
  {
    /*$this->CurrentInventory->updateAll(array('CurrentInventory.qty' => 'CurrentInventory.qty + '.$inventory_info['CurrentInventory']['qty']),array('CurrentInventory.id' => $inventory_info['CurrentInventory']['id']));*/

    $this->CurrentInventory->updateAll(array('CurrentInventory.qty' => 'CurrentInventory.qty + '.$quantity),array('CurrentInventory.id' => $inventory_info['CurrentInventory']['id']));
  }
  return true;
}



  	//for challan referance number check
public function admin_challan_validation() {

  $data_array = array();

  if ($this->request->is('post')) 
  {
   $receiver_store_id = $this->request->data['receiver_store_id'];



   $con = array('Challan.receiver_store_id'=>$receiver_store_id);


   $challan_list = $this->Challan->find('all', array(
    'conditions'=> $con,
    'fields' => array('id', 'challan_no', 'challan_date'),
    'order' => array('id' => 'desc'),
    'limit' => 1,
    'recursive' => -1
    ));

			//$challan_list = count($challan_list);


   foreach($challan_list as $list){
    $data_array['id'] = $list['Challan']['id'];
    $data_array['challan_no'] = $list['Challan']['challan_date'];
    $data_array['challan_date'] = date('d-M, Y', strtotime($list['Challan']['challan_date']));
  }

			//pr($data_array);

  echo json_encode($data_array);

}

$this->autoRender = false;
}



}
