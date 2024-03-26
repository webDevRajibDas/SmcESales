<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

/**
 * DistNotifications Controller
 */

class DistNotificationsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    
    public $components = array('Paginator', 'Session', 'Filter.Filter');
    public $uses = array('DistNotification');


    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index() {
        
        $this->set('page_title', 'Notification Configuration for Distribution Store');        
        $this->loadModel('User');
        $this->loadModel('Office');
        $this->loadModel('DistNotificationUserMap');
        
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
        } else {
            $conditions = array('Office.office_type_id' => 2, 'Office.id' => $this->UserAuth->getOfficeId());
        }
        
        /* Load Office List */

        $offices = $this->Office->find('list',array('conditions' => $conditions));
       
        $users_data = $this->User->find('all', array(
            'fields'=>array('User.id','User.user_group_id','User.username','SalesPerson.id','SalesPerson.name','SalesPerson.office_id'),
            'conditions' => array('User.active' => 1,'User.user_group_id'=>array(1,2,3,5,7,1017,1018,1020)),
            'joins' => array(
                            array(
                                'table' => 'sales_people',
                                'alias' => 'SalesPerson',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'User.sales_person_id = SalesPerson.id',
                                ))),                           
            'order' => array('SalesPerson.name' => 'asc'),
	    'recursive' =>-1
        ));
        
        $users=array();
        foreach ($users_data as $key => $value) {
            $u_id=$value['User']['id'];
            $u_name=$value['SalesPerson']['name'];
            $users[$u_id]=$u_name;
        }
        
        $this->set(compact('offices','users'));       
    }

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_view($id = null) {
        $this->set('page_title', 'Distributors Details');
        if (!$this->DistDistributor->exists($id)) {
            throw new NotFoundException(__('Invalid territory'));
        }
        $options = array('conditions' => array('DistDistributor.' . $this->DistDistributor->primaryKey => $id));
        $this->set('territory', $this->DistDistributor->find('first', $options));
    }

   /* get notification configuration info of Products,Area Office & Usres */
    
    public function get_product_notification_list() {
        
        $this->loadModel('DistDistributor');
        $this->loadModel('Office');
        $this->loadModel('User');
        $this->loadModel('Product');
        $this->loadModel('DistNotification');
        $this->loadModel('DistNotificationUserMap');
        
        $dist_office_id=$this->request->data("office_id");
        $con_product_info=array();
        $products=array();
        $con_user_info=array();
        
        /************************ Get Existing configured Data Start *****************************/
        if($dist_office_id)
        {
                    $con_product_info = $this->DistNotification->find('all', array(
                   'conditions' => array('DistNotification.office_id' => $dist_office_id),
                   'order' => array('DistNotification.id' => 'asc'),
                               'recursive' =>0
                    ));
                    
                    
                     $con_user_info = $this->DistNotificationUserMap->find('all', array(
                   'conditions' => array('DistNotificationUserMap.office_id' => $dist_office_id),
                   'order' => array('DistNotificationUserMap.id' => 'asc'),
                               'recursive' =>0
                    ));
                    
                    
        }
        
 
        /************************ Get Existing configured Data End *****************************/
        
        if(empty($con_product_info))
        {
                $products = $this->Product->find('all', array(
                'conditions' => array('Product.is_active' => 1),
                'order' => array('Product.order' => 'asc'),
                            'recursive' =>-1
            ));
        }
                       
        $this->set(compact('products','dist_office_id','con_user_info','con_product_info'));
    }
    
    /* Mapping Distributor to Outlets */
    public function admin_mapping() 
	{
        $this->loadModel('Office');
        $this->loadModel('User');
        $this->loadModel('DistNotificationUserMap');
        
        $this->set('page_title', 'Notification Configuration for Distribution Store');
        
        $office_id="";
        $dist_distributor_id="";

        if($this->request->is('post') && ($this->request->data("office_id")!=""))
        {
            
            $mapping_data=$this->request->data("DistNotification");
            $office_id=$this->request->data("office_id");
            $user_ids=$mapping_data['user_id'];
            $max_qtys=$mapping_data['max_qty'];
            
            $this->DistNotificationUserMap->query("delete from dist_notification_user_maps where office_id=$office_id");
            $this->DistNotification->query("delete from dist_notifications where office_id=$office_id");
            
            /* Making user area notification data  */
            
            $notificationUserMap=array();
            
            foreach ($user_ids as $key => $value) {   
                $eachUserData=array();    
                $eachUserData['user_id'] = $value;
                $eachUserData['office_id'] = $office_id;
                $eachUserData['created_at'] = $this->current_datetime();
                $eachUserData['created_by'] = $this->UserAuth->getUserId(); 
                $eachUserData['updated_by'] = $this->UserAuth->getUserId();           
                $eachUserData['updated_at'] = $this->current_datetime();  
                $notificationUserMap[]=$eachUserData;
            }
            
            /* user area wise data saving */ 
            if ($this->DistNotificationUserMap->saveAll($notificationUserMap)) {
                    }
                    else 
                    {
                         $this->Session->setFlash(__('Mapped has not done successfully'), 'flash/error');
                         $this->redirect(array('action' => 'index'));
                    }
            
           /* binding data for product data configuration  */
                    
            $notifications=array();
            
            foreach ($max_qtys as $k => $v) {                  
                $eachProductData=array();    
                $eachProductData['product_id'] = $k;
                $eachProductData['max_qty'] = $v;
                $eachProductData['office_id'] = $office_id;
                $eachProductData['is_active'] = 1;
                $eachProductData['created_at'] = $this->current_datetime();
                $eachProductData['created_by'] = $this->UserAuth->getUserId(); 
                $eachProductData['updated_by'] = $this->UserAuth->getUserId();           
                $eachProductData['updated_at'] = $this->current_datetime();  
                $notifications[]=$eachProductData;
            }
            
            /* user area wise data saving */ 
            
            if ($this->DistNotification->saveAll($notifications)) {
                         $this->Session->setFlash(__('Notification Configuration for Distribution Store has been done successfully'), 'flash/success');
                         $this->redirect(array('action' => 'index'));
                    }
                    else 
                    {
                         $this->Session->setFlash(__('Notification Configuration for Distribution Store has not been done successfully'), 'flash/error');
                         $this->redirect(array('action' => 'index'));
                    }
        }

}

    public function send_dms_notifications()
    {
        $this->loadModel('Product');
        $this->loadModel('DistStore');
        $this->loadModel('DistNotificationUserMap'); 
        
        /******************** Get all product Info Start ***********************/
        
        $product_list = $this->Product->find('list', array(
                'conditions' => array('Product.is_active' => 1),
                'order' => array('Product.order' => 'asc'),
                            'recursive' =>-1
            ));
       
        /******************** Get all product Info End ***********************/
        
        
        /******************** Get all Store Info Start ***********************/
        
                $store_list = $this->DistStore->find('all',array(
                            'recursive' =>0
            ));
               // pr($store_list); exit;
                $store_info=array();
                $office_info=array();
            foreach ($store_list as $key => $value) {
                $sid=$value['DistStore']['id'];
                $sname=$value['DistStore']['name'];
                $soffice_id=$value['Office']['id'];
                $soffice_name=$value['Office']['office_name'];
                $store_info[$sid]['name']=$sname;
                $store_info[$sid]['office_id']=$soffice_id;
                $store_info[$sid]['office_name']=$soffice_name;
                $office_info[$soffice_id]=$soffice_name; 
            }
        /******************** Get all Store Info End ***********************/
        
        
        $sql="select ds.office_id,ci.store_id,ci.product_id,ci.qty,dn.max_qty 
                from dist_current_inventories ci
                left join dist_stores ds on ci.store_id=ds.id 
                inner join dist_notifications dn on dn.office_id=ds.office_id and dn.product_id=ci.product_id
                and ci.qty>dn.max_qty";
        
        $result=$this->DistNotification->query($sql);
         
        foreach ($result as $k => $v) {
           $nsid=$v[0]['store_id'];
           $npid=$v[0]['product_id'];
           $nofficeid=$v[0]['office_id'];
           
            $notice_store_name=$store_info[$nsid]['name'];
            $notice_product_name=$product_list[$npid];
            $notice_con_amount=($v[0]['max_qty']>0)?$v[0]['max_qty']:0;
            $notice_cur_amount=($v[0]['qty']>0)?$v[0]['qty']:0;
            
            $con_user_info = $this->DistNotificationUserMap->find('all', array(
                   'conditions' => array('DistNotificationUserMap.office_id' => $nofficeid),
                   'order' => array('DistNotificationUserMap.id' => 'asc'),
                               'recursive' =>0
                    ));
            
            $email_ids=array();
            
                    foreach ($con_user_info as $key => $val) {
                        //if($val['User']['email'])
                        //$email_ids[]=$val['User']['email'];
                    }
            $email_ids=array('jobaidur@arenaphonebd.net','sarwar@arenaphonebd.net');
           if($email_ids)
           {
               $this->send_mail($email_ids,"Distributor Stock Information","Distributor Store <b> $notice_store_name </b> has exceeded the max amount for <b>$notice_product_name</b> Product . Configured Max Amount is: $notice_con_amount and Current Amount is: $notice_cur_amount");
           }
           
        }
    }
    
    public function send_mail($to,$title,$msg)
    {
        /*
        $Email = new CakeEmail();
        $Email->config('smtp');
        $Email->from(array('supporttest@divergenttechbd.com' => 'DMS Support'))
            ->to($to)
           ->emailFormat('html')
            ->subject($title)
            ->send($msg);
         * 
         * 
         */
               $from = "jobaidur@arenaphonebd.net";				
		$message =  $msg;
                $destination="sarwar@arenaphonebd.net";
		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= "From: $from \r\n" .
		$headers .="Reply-To: $from \r\n";
		$headers .="X-Mailer: PHP/" . phpversion();		
		$title=html_entity_decode($title);
	
		mail($destination, $title, wordwrap($message, 75, "\n", true), wordwrap($headers, 75, "\n", true));
    }

}
