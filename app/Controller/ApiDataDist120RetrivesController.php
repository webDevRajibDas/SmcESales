<?php
App::uses('AppController', 'Controller');
App::import('Vendor', 'dompdf', array('file' => 'dompdf/dompdf_config.inc.php'));
//App::import('Vendor', 'dompdf/dompdf_config.inc.php');

/**
 * Controller
 *
 * @property ApiDataRetrives $ApiDataRetrives
 * @property PaginatorComponent $Paginator
 */
class ApiDataDist120RetrivesController extends AppController 
{
    
	public $components = array('RequestHandler', 'Usermgmt.UserAuth');
	
	
	public function mac_check($mac_id,$so_id)
    {
        $this->LoadModel('User');
        $this->LoadModel('CommonMac');
        $users=$this->User->find('first',array(
                'conditions'=>array('User.mac_id'=>$mac_id,'User.sales_person_id'=>$so_id),
                'recursive'=>-1
                ));
        if(empty($users))
        {
            $common_mac_check=$this->CommonMac->find('first',array('conditions'=>array('CommonMac.mac_id'=>$mac_id)));
            
            if($common_mac_check)
                return true;
            else
                return false;
        }
        else
        {
            	return true;
        }
    }
	 
	/* ------------------- Dist User login ------------------------ */
    public function dist_user_login() {
        $this->loadModel('Usermgmt.User');
        $this->loadModel('Office');
        $this->loadModel('DistStore');
        $this->loadModel('SalesPerson');
		
		$json_data = $this->request->input('json_decode', true);
        $username = $json_data['username'];
        $password = md5($json_data['password']);


        $path = APP . 'logs/';
        $myfile = fopen($path . "dist_login.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        //$username = 'antora';
        //$password = md5('123456');
        //add new for version
        if ($json_data['version']) {
            $version = $json_data['version'];
        } else {
            $version = '';
        }
        $version_info = array();
        $c_date = date('Y-m-d');
        if ($version) {
            $sql = "SELECT TOP 1 * FROM app_versions WHERE name='$version' AND status=1 AND start_date<='$c_date' AND target_apk=2 ORDER BY ID DESC";
            $version_info = $this->User->query($sql);
        } else {
            $sql = "SELECT TOP 1 * FROM app_versions WHERE status=1 AND start_date>'$c_date' AND target_apk=2 ORDER BY ID DESC";
            $version_info = $this->User->query($sql);
        }
        //end for version

        if ($version_info) {
            $user_info = $this->User->find('first', array(
                'fields' => array('User.id', 'User.sales_person_id', 'User.username', 'SalesPerson.id', 'SalesPerson.name', 'SalesPerson.office_id', 'SalesPerson.territory_id', 'UserGroup.id', 'UserGroup.name', 'User.mac_id', 'SalesPerson.dist_sales_representative_id'),
                'conditions' => array('User.username' => $username, 'User.password' => $password, 'User.active' => 1),
                'recursive' => 0
            ));

            //pr($user_info);

            if (!empty($user_info)) {
                
				//user info
								
				/*------------------------------mac check: Start ---------------*/
                if(empty($user_info['User']['mac_id']))
                {
                    $mac_exist = $this->User->find('first', array(
                        'fields' => array('User.id', 'User.sales_person_id', 'User.username','User.mac_id', 'SalesPerson.id', 'SalesPerson.name', 'SalesPerson.office_id', 'SalesPerson.territory_id', 'UserGroup.id', 'UserGroup.name'),
                        'conditions' => array('User.mac_id' =>$json_data['mac']),
                        'recursive' => 0
                        ));
                    if($mac_exist)
                    {
                        $login['status'] = 0;
                        $login['message'] = 'Mac Already Configured';
                        $res = $login;
                        $this->set(array(
                            'response' => array($res),
                            '_serialize' => array('response')
                            ));
                        return 0;
                    }
                    else
                    {
                        $mac_data['mac_id']=$json_data['mac'];
                        $mac_data['version']=$version;
                        $mac_data['id']=$user_info['User']['id'];
                        $this->User->saveAll($mac_data);
                        
                        $so_id = $user_info['SalesPerson']['id'];
						$so_info = $this->SalesPerson->find('first', array(
							'fields' => array(
									'SalesPerson.name',
									'SalesPerson.id',
									'SalesPerson.dist_sales_representative_id',
									'DistSalesRepresentative.id',
									'DistSalesRepresentative.name',
									'DistSalesRepresentative.dist_distributor_id',
									'DistDistributor.id',
									'DistDistributor.name',
									'DistDistributor.address',
									'DistDistributor.mobile_number',
									'DistStore.id',
									'Office.id',
									'Office.office_name',
									'Office.phone',
									'Office.address',
									'SalesPerson.territory_id'
									),
							'conditions' => array('SalesPerson.id' => $so_id),
							'joins' => array(
								array(
									'alias' => 'DistSalesRepresentative',
									'table' => 'dist_sales_representatives',
									'type' => 'INNER',
									'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
								),
								array(
									'alias' => 'DistDistributor',
									'table' => 'dist_distributors',
									'type' => 'INNER',
									'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
								),
								array(
									'alias' => 'DistStore',
									'table' => 'dist_stores',
									'type' => 'INNER',
									'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
								),
								array(
									'alias' => 'Office',
									'table' => 'offices',
									'type' => 'INNER',
									'conditions' => 'SalesPerson.office_id = Office.id'
								)
							),
							
							'recursive' => -1
						));
						
						//pr($so_info);
						
						//$company_id = $so_info['Office']['company_id'];
						$office_id = $so_info['Office']['id'];
						//$territory_id = $so_info['DistDistributor']['territory_id'];
						$store_id = $so_info['DistStore']['id'];
						
						$this->loadModel('Territory');
						$territory_info=$this->Territory->find('first',array(
							'conditions'=> array(
								'Territory.office_id'=>$office_id,
								'Territory.name LIKE'=> '%Corporate Territory%',
							)));
						$territory_id=$territory_info['Territory']['id'];
						
						$user_info = array();
						$user_info['user_info'] = array(
							//'company_id' 		=> $company_id,
							'office_id' 		=> $office_id,
							'sr_name' 			=> $so_info['SalesPerson']['name'],
							'office_name' 		=> $so_info['Office']['office_name'],
							'office_address' 	=> $so_info['Office']['address'],
							'office_phone' 		=> $so_info['Office']['phone'],
							'db_name' 			=> $so_info['DistDistributor']['name'],
							'db_address' 		=> $so_info['DistDistributor']['address'],
							'db_mobile' 		=> $so_info['DistDistributor']['mobile_number'],
							'office_id' 		=> $office_id,
							'territory_id' 		=> $territory_id,
							'store_id' 			=> $store_id,
							'sales_person_id' 	=> $so_id
						);
						
						
						if (!empty($so_info)) {
							$login['status'] = 1;
							$login['message'] = 'Success';
							$res = array_merge($login,$user_info);
						} else {
							$login['status'] = 0;
							$login['message'] = 'Store not configured yet for this user.';
							$res = $login;
						}
                    }
                }
                else
                {
                    $this->LoadModel('CommonMac');
                    $common_mac_check=$this->CommonMac->find('first',array('conditions'=>array('CommonMac.mac_id'=>$json_data['mac'])));
                    if($json_data['mac']==$user_info['User']['mac_id'] || $common_mac_check)
                    {
                    	/*---- version update in user table : start -------------*/
                        $version_data['version']=$version;
                        $version_data['id']=$user_info['User']['id'];
                        $this->User->saveAll($version_data);
                        /*---- version update in user table : END -------------*/
                        $so_id = $user_info['SalesPerson']['id'];
						$so_info = $this->SalesPerson->find('first', array(
							'fields' => array(
									'SalesPerson.name',
									'SalesPerson.id',
									'SalesPerson.dist_sales_representative_id',
									'DistSalesRepresentative.id',
									'DistSalesRepresentative.name',
									'DistSalesRepresentative.dist_distributor_id',
									'DistDistributor.id',
									'DistDistributor.name',
									'DistDistributor.address',
									'DistDistributor.mobile_number',
									'DistStore.id',
									'Office.id',
									'Office.office_name',
									'Office.phone',
									'Office.address',
									'SalesPerson.territory_id'
									),
							'conditions' => array('SalesPerson.id' => $so_id),
							'joins' => array(
								array(
									'alias' => 'DistSalesRepresentative',
									'table' => 'dist_sales_representatives',
									'type' => 'INNER',
									'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
								),
								array(
									'alias' => 'DistDistributor',
									'table' => 'dist_distributors',
									'type' => 'INNER',
									'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
								),
								array(
									'alias' => 'DistStore',
									'table' => 'dist_stores',
									'type' => 'INNER',
									'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
								),
								array(
									'alias' => 'Office',
									'table' => 'offices',
									'type' => 'INNER',
									'conditions' => 'SalesPerson.office_id = Office.id'
								)
							),
							
							'recursive' => -1
						));
						
						//pr($so_info);
						
						$office_id = $so_info['Office']['id'];
						//$territory_id = $so_info['DistDistributor']['territory_id'];
						$store_id = $so_info['DistStore']['id'];
						
						
						$this->loadModel('Territory');
						$territory_info=$this->Territory->find('first',array(
							'conditions'=> array(
								'Territory.office_id'=>$office_id,
								'Territory.name LIKE'=> '%Corporate Territory%',
							)));
						$territory_id=$territory_info['Territory']['id'];
						
						$user_info = array();
						$user_info['user_info'] = array(
							//'company_id' 		=> $company_id,
							'office_id' 		=> $office_id,
							'sr_name' 			=> $so_info['SalesPerson']['name'],
							'office_name' 		=> $so_info['Office']['office_name'],
							'office_address' 	=> $so_info['Office']['address'],
							'office_phone' 		=> $so_info['Office']['phone'],
							'db_name' 			=> $so_info['DistDistributor']['name'],
							'db_address' 		=> $so_info['DistDistributor']['address'],
							'db_mobile' 		=> $so_info['DistDistributor']['mobile_number'],
							'office_id' 		=> $office_id,
							'territory_id' 		=> $territory_id,
							'store_id' 			=> $store_id,
							'sales_person_id' 	=> $so_id
						);
						
						
						if (!empty($so_info)) {
							$login['status'] = 1;
							$login['message'] = 'Success';
							$res = array_merge($login,$user_info);
						} else {
							$login['status'] = 0;
							$login['message'] = 'Store not configured yet for this user.';
							$res = $login;
						}
                    }
                    else
                    {
                        $login['status'] = 0;
                        $login['message'] = 'Mac Id Not Match';
                        $res = $login;
                    }
                }
				
				
            } 
			else 
			{
                $login['status'] = 0;
                $login['message'] = 'Username or Password does not match';
                $res = $login;
            }
        } 
		else 
		{
            $login['status'] = 0;
            $login['message'] = 'Version does not match!';
            $res = $login;
        }

        $this->set(array(
            'response' => array($res),
            '_serialize' => array('response')
        ));
    }
    /* ------------------- End Dist User login -------------------- */
	
	
	/* ------------------- Dist Data Pull ------------------------- */
	public function dist_data_pull(){
		
		ini_set('memory_limit', '-1');
		
        $this->loadModel('SalesPerson');
        $this->loadModel('Usermgmt.User');
        $this->loadModel('Office');
        $this->loadModel('DistStore');
        $this->loadModel('DistSalesRepresentative');
		
        $json_data = $this->request->input('json_decode', true);
		
		/*pr($json_data);
		exit;*/
		
		$so_id = $json_data['sales_person_id'];
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_data_pull.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
		/*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'], $so_id);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }

		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistSalesRepresentative.code','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $so_id),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		/*pr($so_info);
		exit;*/
		$office_id = $so_info['Office']['id'];
		
		$this->loadModel('Territory');
		$territory_info=$this->Territory->find('first',array(
			'conditions'=> array(
				'Territory.office_id'=>$office_id,
				'Territory.name LIKE'=> '%Corporate Territory%',
			)));
		$territory_id=$territory_info['Territory']['id'];
		
		//$territory_id = 44;
		
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		$sr_code = $so_info['DistSalesRepresentative']['code'];
		
		$user_info['user_info'] = array(
			'sales_person_id' 	=> $so_id,
			'office_id' 		=> $office_id,
			'territory_id' 		=> $territory_id,
			'distributor_id' 	=> $distributor_id,
			'store_id' 			=> $store_id,
			'sr_id' 			=> $sr_id
		);
        //end user info
		
		
		//start current_promotion_products / get_promotion_message
		$this->loadModel('MessageList');
        $this->loadModel('MessageProduct');
		$last_update_date = ($json_data['current_promotion_products']['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['current_promotion_products']['last_update']));
		
		$conditions = array(
			'MessageList.is_promotional' => 1,
		);
		if($last_update_date!='all')$conditions['MessageList.created_at >']=$last_update_date;
        $promotion_message = $this->MessageList->find('all', array(
            'fields' => array('MessageList.id', 'MessageList.message', 'MessageList.created_at'),
            'conditions' => $conditions,
            'order' => array('MessageList.created_at' => 'asc'),
            'recursive' => -1
        ));
		
		$current_promotion_products['message'] = array();
		$current_promotion_products['current_promotion_products'] = array();
		
        if (!empty($promotion_message)) {
			
            foreach ($promotion_message as $val) {
				
				$current_promotion_products['message'][] = array(
					'current_promotion_id' 	=> $val['MessageList']['id'],
					'message' 				=> $val['MessageList']['message'],
				);
				
                $MessageProductresults = $this->MessageProduct->find('all', array(
                    'fields' => array('MessageProduct.id', 'MessageProduct.message_id', 'MessageProduct.product_id', 'Product.name'),
                    'conditions' => $conditions,
                    'order' => array('MessageList.created_at' => 'asc'),
                    'recursive' => 0
                ));
				
				foreach($MessageProductresults as $result){
					$current_promotion_products['current_promotion_products'][] = array(
						'current_promotion_id' 	=> $result['MessageProduct']['message_id'],
						'product_id' 				=> $result['MessageProduct']['product_id'],
					);
				}
				
            }
        }
		//end current_promotion_products / get_promotion_message
				
		
		//start product / get_product_list	
        $this->loadModel('Product');
        
		$last_update_date = ($json_data['product']['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['product']['last_update']));
		//$last_update_date = 'all';
        $conditions = array(); 
        //$conditions = array('Product.company_id' => $company_id);
		$conditions['Product.is_distributor_product'] = 1;
        if($last_update_date!='all')$conditions['Product.updated_at >'] = $last_update_date;
		
		
        $products = $this->Product->find('all', array(
            'fields' => array(
            	'Product.id', 
            	'Product.name', 
            	'Product.product_code', 
            	'Product.product_category_id', 
            	'Product.sales_measurement_unit_id', 
            	'Product.product_type_id', 
            	'Product.updated_at', 
            	'Product.is_pharma', 
            	'Product.order', 
            	'Product.is_injectable',
            	'Product.product_image'
            	),
            'conditions' => $conditions,
            'order' => array('Product.order' => 'asc'),
            'recursive' => -1
        ));

        $this->loadModel('OpenCombinationProduct');
        $price_open = array();
        $bonus_open = array();

        foreach ($products as $key => $val) {
            //for price opne
            $price_open = $this->OpenCombinationProduct->find('first', array(
                'conditions' => array(
                    'OpenCombinationProduct.product_id' => $val['Product']['id'],
                    'OpenCombination.is_bonus' => 0,
					'OpenCombination.start_date <=' => $this->current_date(),
					'OpenCombination.end_date >=' =>  $this->current_date()	
                ),
            ));
            //pr($price_open);			

            if ($price_open) {
                $products[$key]['Product']['price_open_start'] = $price_open['OpenCombination']['start_date'];
                $products[$key]['Product']['price_open_end'] = $price_open['OpenCombination']['end_date'];
            } else {
                $products[$key]['Product']['price_open_start'] = '';
                $products[$key]['Product']['price_open_end'] = '';
            }


            //for bonus opne
            $bonus_open = $this->OpenCombinationProduct->find('first', array(
                'conditions' => array(
                    'OpenCombinationProduct.product_id' => $val['Product']['id'],
                    'OpenCombination.is_bonus' => 1,
					'OpenCombination.start_date <=' => $this->current_date(),
					'OpenCombination.end_date >=' =>  $this->current_date()	
                ),
            ));
            //pr($bonus_open);			

            if ($bonus_open) {
                $products[$key]['Product']['price_bonus_start'] = $bonus_open['OpenCombination']['start_date'];
                $products[$key]['Product']['price_bonus_end'] = $bonus_open['OpenCombination']['end_date'];
            } else {
                $products[$key]['Product']['price_bonus_start'] = '';
                $products[$key]['Product']['price_bonus_end'] = '';
            }

            $products[$key]['Product']['action'] = 1;
        }
		
		
		$product['product'] = array();
		foreach ($products as $val) {
			$product['product'][] = array(
				'product_id' 					=> $val['Product']['id'],
				'product_name' 					=> $val['Product']['name'],
				'product_category_id' 			=> $val['Product']['product_category_id'],
				'product_type_id' 				=> $val['Product']['product_type_id'],
				'product_order' 				=> $val['Product']['order'],
				'is_injectable' 				=> $val['Product']['is_injectable'],
				'image_name' 					=> $val['Product']['product_image'],
				'image_url' 					=> BASE_URL.'app/webroot/img/product_img/'.$val['Product']['product_image'],
			);
		}
		//end product / get_product_list
		
		//start Distributor Bonus Product

        $distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
       // comment by Naser. Because as fer SMC policy there is no seperate bonus stock. Bonus stock will be maintain from main stock.
        /*$bonus_product_list = $this->Product->find('all', array(
            'fields' => array(
            	'Product.id', 
            	'Product.name',
            	'SUM(DistCurrentInventory.bonus_qty) as bonus_qty'
            ),
            'joins' => array(
                array(
                    'table' => 'dist_current_inventories',
                    'alias' => 'DistCurrentInventory',
                    'type' => 'Inner',
                    'conditions' => 'DistCurrentInventory.product_id=Product.id'
                ),
                
            ),

            'conditions' => array(
                'DistCurrentInventory.store_id' => $store_id,
                'DistCurrentInventory.bonus_qty >' => 0,
            ),
            'group' => array('Product.id', 'Product.name'),
            'recursive' => -1
        ));
		
		
      	$dist_bonus_product = array();
      	foreach ($bonus_product_list as $key => $value) 
      	{
      		$dist_bonus_product['dist_bonus_product'][] = array(
				'product_id' 					=> $value['Product']['id'],
				'product_name' 					=> $value['Product']['name'],
				'bonus_qty'						=> $value[0]['bonus_qty']
			);
      	}*/

		//end Distributor Bonus Product
		

		//start product_categories / get_product_categories	
		$this->loadModel('ProductCategory');
		$last_update_date = ($json_data['product_categories']['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['product_categories']['last_update']));
        
		$conditions = array(); 
        if($last_update_date!='all')$conditions['ProductCategory.updated_at >'] = $last_update_date;
		//$conditions['ProductCategory.is_dms_category'] = 1;
        
		
		$results = $this->ProductCategory->find('all', array(
            'fields' => array('ProductCategory.id', 'ProductCategory.name', 'ProductCategory.parent_id', 'ProductCategory.is_pharma_product', 'ProductCategory.updated_at'),
            'conditions' => $conditions,
            'order' => array('ProductCategory.updated_at' => 'asc'),
            'recursive' => -1
        ));
				
		$product_categories['product_categories'] = array();
        foreach($results as $val){
			$product_categories['product_categories'][] = array(
				'category_id' 				=> $val['ProductCategory']['id'],
				'category_name' 			=> $val['ProductCategory']['name'],
				'is_active' 				=> 1,
			);
		}
		//end product_categories / get_product_categories
	    
		
		//start product_price / get_product_price_list	
		$this->loadModel('DistSrProductPrice');
		
		$last_update_date = ($json_data['product_price']['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['product_price']['last_update']));
        
		$conditions = array(); 
		//$conditions['DistSrProductPrice.effective_date >='] = date('Y-m-d');
        if($last_update_date!='all')$conditions['DistSrProductPrice.updated_at >'] = $last_update_date;

        $results = $this->DistSrProductPrice->find('all', array(
            'conditions' => $conditions,
            //'order' => array('DistSrProductPrice.effective_date' => 'desc'),
			'order' => array('DistSrProductPrice.updated_at' => 'asc'),
			//'order' => array('DistSrProductPrice.product_id' => 'asc'),
            'recursive' => -1
        ));
		//pr($results);exit;
		$product_price['product_price'] = array();
        foreach($results as $val){
			$product_price['product_price'][] = array(
				'price_id' 					=> $val['DistSrProductPrice']['id'],
				'product_id' 				=> $val['DistSrProductPrice']['product_id'],
				'target_custommer' 			=> $val['DistSrProductPrice']['target_custommer'],
				'institute_id' 				=> $val['DistSrProductPrice']['institute_id'],
				'price' 					=> $val['DistSrProductPrice']['general_price'],
				'effective_date' 			=> $val['DistSrProductPrice']['effective_date'],
				'vat' 						=> $val['DistSrProductPrice']['vat'],
			);
		}
		//end product_price / get_product_price_list
				
		
		//start product_type / get_product_types	
		$this->loadModel('ProductType');
		
		$last_update_date = ($json_data['product_type']['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['product_type']['last_update']));
        
		$conditions = array(); 
        if($last_update_date!='all')$conditions['ProductType.updated_at >'] = $last_update_date;

        $results = $this->ProductType->find('all', array(
            'fields' => array('ProductType.id', 'ProductType.name', 'ProductType.updated_at'),
            'conditions' => $conditions,
            'order' => array('ProductType.id' => 'asc'),
            'recursive' => 0
        ));
		
		$product_type['product_type'] = array();
        foreach($results as $val){
			$product_type['product_type'][] = array(
				'product_type_id' 				=> $val['ProductType']['id'],
				'product_type_name' 			=> $val['ProductType']['name'],
			);
		}
		//end product_price / get_product_types
		
		
		//start product_combinations / get_product_combination_list 	
		$this->loadModel('DistSrProductCombination');
		$last_update_date = ($json_data['product_combinations']['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['product_combinations']['last_update']));
		//$last_update_date = 'all';
        $conditions = array(); 
		//$conditions = array('Product.company_id' => $company_id);
        if($last_update_date!='all')$conditions['DistSrProductCombination.updated_at >'] = $last_update_date;

        $results = $this->DistSrProductCombination->find('all', array(
            'conditions' => $conditions,
            'order' => array('DistSrProductCombination.updated_at' => 'asc'),
            'recursive' => -1
        ));		

		$product_combinations['product_combinations'] = array();
        foreach($results as $val){
			$product_combinations['product_combinations'][] = array(
				'slab_id' 						=> $val['DistSrProductCombination']['id'],
				'product_id' 				=> $val['DistSrProductCombination']['product_id'],
				'combination_id' 			=> $val['DistSrProductCombination']['combination_id'],
				'min_quantity' 				=> $val['DistSrProductCombination']['min_qty'],
				'price' 					=> $val['DistSrProductCombination']['price'],
				'effective_date' 			=> $val['DistSrProductCombination']['effective_date'],
			);
		}
		//end product_combinations / get_product_combination_list
		
		//territory / new implement
		$this->loadModel('Territory');
		$last_update_date = ($json_data['territories']['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['territories']['last_update']));
		//$last_update_date = 'all';
        $conditions = array(); 
		$conditions = array('Territory.office_id' => $office_id);
        if($last_update_date!='all')$conditions['Territory.updated_at >'] = $last_update_date;
		
		$territory_list = $this->Territory->find('all', array(
            'conditions' => $conditions,
            'order' => array('Territory.id' => 'asc'),
            'recursive' => -1
        ));
		$territories['territories'] = array();
        foreach($territory_list as $val){
			$territories['territories'][] = array(
				'territory_id' 					=> $val['Territory']['id'],
				'territory_name' 				=> $val['Territory']['name'],
				
			);
		}
		//edn territory / new implement
		
		//start thaha / get_thana 	
		$this->loadModel('Thana');
		$last_update_date = ($json_data['thana']['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['thana']['last_update']));
        $conditions = array(); 
		//$conditions = array('ThanaTerritory.territory_id' => $territory_id);
		//$conditions = array('Territory.office_id' => $office_id);
		$conditions = array('DistSrRouteMapping.dist_distributor_id' => $distributor_id);
		$conditions = array('DistSrRouteMapping.dist_sr_id' => $sr_id);
        //if($last_update_date!='all')$conditions['Thana.updated_at >'] = $last_update_date;
		/*pr($conditions);
		exit;*/

        $thana_list = $this->Thana->find('all', array(
            'fields' => array('Thana.id', 'Thana.name'),
            'conditions' => $conditions,
			'joins' => array(
				/*array(
					'alias' => 'DistMarket',
					'table' => 'dist_markets',
					'type' => 'INNER',
					'conditions' => 'Thana.id = DistMarket.thana_id'
				),*/
				array(
					'alias' => 'DistRoute',
					'table' => 'dist_routes',
					'type' => 'INNER',
					'conditions' => 'Thana.id = DistRoute.thana_id'
				),
				array(
					'alias' => 'DistSrRouteMapping',
					'table' => 'dist_sr_route_mappings',
					'type' => 'INNER',
					'conditions' => 'DistRoute.id = DistSrRouteMapping.dist_route_id'
				)
			),
            'order' => array('Thana.id' => 'asc'),
			'group' => array('Thana.id','Thana.name'),
            'recursive' => -1
        ));
		
		//pr($thana_list);exit;
		
		$thanas['thana'] = array();
        foreach($thana_list as $val){
			$thanas['thana'][] = array(
				'thana_id' 					=> $val['Thana']['id'],
				'thana_name' 				=> $val['Thana']['name'],
				//'dist_route_id' 			=> $val['DistRoute']['id'],
				//'territory_id' 				=> $val['ThanaTerritory']['territory_id'],
			);
		}
		//end thaha / get_thana													
		
		
		//start route / new implemented
		$this->loadModel('DistSrRouteMapping');
        $last_update_date = ($json_data['route']['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['route']['last_update']));
		
		$conditions = array();

		$conditions = array('DistSrRouteMapping.dist_sr_id' => $sr_id,'DistSrRouteMapping.dist_distributor_id' => $distributor_id);
        if($last_update_date!='all')$conditions['DistSrRouteMapping.updated_at >'] = $last_update_date;
		

        $results = $this->DistSrRouteMapping->find('all', array(
            'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'DistRoute',
					'table' => 'dist_routes',
					'type' => 'INNER',
					'conditions' => 'DistSrRouteMapping.dist_route_id = DistRoute.id'
				)
			),
			'fields' => 'DistRoute.id, DistRoute.name, DistRoute.thana_id',
            'order' => array('DistRoute.name' => 'asc'),
			'recursive' => -1
        ));
		

		$route['route'] = array();
        foreach ($results as $key => $val) {
            $route['route'][] = array(
				'route_id' 			=> $val['DistRoute']['id'],
				'route_name' 		=> $val['DistRoute']['name'],
				'thana_id' 			=> $val['DistRoute']['thana_id']
			);
        }
		//end route / new implemented
		
		
		
		//start markets / get_market_list 	
		$this->loadModel('DistMarket');		
		$last_update_date = ($json_data['markets']['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['markets']['last_update']));
		$conditions = array();
        $conditions = array('DistSrRouteMapping.dist_distributor_id' => $distributor_id); 
		$conditions = array('DistSrRouteMapping.dist_sr_id' => $sr_id, 'DistMarket.is_active' => 1);
        if($last_update_date!='all')$conditions['DistMarket.updated_at >'] = $last_update_date;
		
		//pr($conditions);
		
        $market_list = $this->DistMarket->find('all', array(
            'conditions' => $conditions,
			'joins' => array(
				/*array(
					'alias' => 'DistRoute',
					'table' => 'dist_routes',
					'type' => 'INNER',
					'conditions' => 'DistRoute.id = DistMarket.dist_route_id'
				),*/
				array(
					'alias' => 'DistSrRouteMapping',
					'table' => 'dist_sr_route_mappings',
					'type' => 'INNER',
					'conditions' => 'DistMarket.dist_route_id = DistSrRouteMapping.dist_route_id'
				)
			),
            'order' => array('DistMarket.name' => 'asc'),
			'recursive' => -1
        ));
		
		//pr($market_list);

        $markets['markets'] = array();
        foreach($market_list as $val){
			$markets['markets'][] = array(
				'market_id' 				=> $val['DistMarket']['id'],
				'market_name' 				=> $val['DistMarket']['name'],
				'thana_id' 					=> $val['DistMarket']['thana_id'],
				'territory_id' 				=> $val['DistMarket']['territory_id'],
				'location_type_id' 			=> $val['DistMarket']['location_type_id'],
				'is_active' 				=> $val['DistMarket']['is_active'],
				/*'market_code' 				=> $val['DistMarket']['code'],*/
				'address' 					=> $val['DistMarket']['address'],
				'route_id' 					=> $val['DistMarket']['dist_route_id'],
			);
		}
		//end markets / get_market_list
		
		
		//start outlets / get_outlet_list 	
		$this->loadModel('DistOutlet');		
		$last_update_date = ($json_data['outlets']['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['outlets']['last_update']));
		//echo $last_update_date;exit;
		//$last_update_date = 'all';
        $conditions = array();
        $conditions = array('DistSrRouteMapping.dist_distributor_id' => $distributor_id);  
		$conditions = array('DistSrRouteMapping.dist_sr_id' => $sr_id);
        if($last_update_date!='all')$conditions['DistOutlet.updated_at >'] = $last_update_date;
		
		//pr($conditions );

        $outlet_list = $this->DistOutlet->find('all', array(
            'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'DistMarket',
					'table' => 'dist_markets',
					'type' => 'INNER',
					'conditions' => 'DistOutlet.dist_market_id = DistMarket.id'
				),
				/*array(
					'alias' => 'DistRoute',
					'table' => 'dist_routes',
					'type' => 'INNER',
					'conditions' => 'DistRoute.id = DistMarket.dist_route_id'
				),*/
				array(
					'alias' => 'DistSrRouteMapping',
					'table' => 'dist_sr_route_mappings',
					'type' => 'INNER',
					'conditions' => 'DistMarket.dist_route_id = DistSrRouteMapping.dist_route_id'
				)
			),
			'fields' => array('DistOutlet.*', 'DistMarket.thana_id',),
            'order' => array('DistOutlet.name' => 'asc'),
			'recursive' => -1
        ));

        //echo $this->DistOutlet->getLastquery();
        //pr($outlet_list);exit;

        $outlets['outlets'] = array();
        foreach($outlet_list as $val){
			$outlets['outlets'][] = array(
				'outlet_id' 				=> $val['DistOutlet']['id'],
				'outlet_name' 				=> $val['DistOutlet']['name'],
				'address' 					=> $val['DistOutlet']['address'],
				'thana_id' 					=> $val['DistMarket']['thana_id'],
				'market_id' 				=> $val['DistOutlet']['dist_market_id'],
				'outlet_category_id' 		=> $val['DistOutlet']['category_id'],
				'ngo_institute_id' 			=> $val['DistOutlet']['institute_id'],
				'owner_name' 				=> $val['DistOutlet']['ownar_name'],
				'incharge_name' 			=> $val['DistOutlet']['in_charge'],
				'mobile' 					=> $val['DistOutlet']['mobile'],
				'pharma_type' 				=> $val['DistOutlet']['is_pharma_type'],
				'isActivated' 				=> $val['DistOutlet']['is_active'],
				'bonus_party_type' 			=> $val['DistOutlet']['bonus_type_id'],
				'route_id' 					=> $val['DistOutlet']['dist_route_id'],
				'latitude' 					=> $val['DistOutlet']['latitude'],
				'longitude' 				=> $val['DistOutlet']['longitude'],
			);
		}
		//end outlets / get_outlet_list
        
				
		
		//start bank_branch_list / get_bank_branch
		$last_update_date = ($json_data['bank_branch']['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['bank_branch']['last_update']));
		$this->loadModel('BankBranch');
        if ($last_update_date == 'all') {
            $conditions = array(
                'OR' => array(
                    array(
                        'AND' => array(
                            array('BankBranch.territory_id' => $territory_id),
                        )
                    ),
                    array(
                        'AND' => array(
                            array('BankBranch.office_id' => $office_id),
                            array('BankBranch.is_common' => 1),
                        )
                    ),
                    array(
                        'AND' => array(
                            array('BankBranch.office_id' => NULL),
                            array('BankBranch.territory_id' => NULL),
                            array('BankBranch.is_common' => 1),
                        )
                    ),
                )
            );
        } 
		else 
		{
            $conditions = array(
                'BankBranch.updated_at >' => $last_update_date,
                'OR' => array(
                    array(
                        'AND' => array(
                            array('BankBranch.territory_id' => $territory_id),
                        )
                    ),
                    array(
                        'AND' => array(
                            array('BankBranch.office_id' => $office_id),
                            array('BankBranch.is_common' => 1),
                        )
                    ),
                    array(
                        'AND' => array(
                            array('BankBranch.office_id' => NULL),
                            array('BankBranch.territory_id' => NULL),
                            array('BankBranch.is_common' => 1),
                        )
                    ),
                )
            );
        }
		//$conditions['Bank.office_id'] = $office_id;
		//pr($conditions);
		//exit;
        $BankBranch = $this->BankBranch->find('all', array(
            'fields' => array('BankBranch.id', 'BankBranch.name', 'BankBranch.updated_at'),
            'conditions' => $conditions,
            'order' => array('BankBranch.id' => 'asc'),
            'recursive' => 0
        ));
        
        $bank_branch['bank_branch'] = array();
        foreach ($BankBranch as $key => $val) {
            $bank_branch['bank_branch'][] = array(
				'bank_branch_id' 		=> $val['BankBranch']['id'],
				'bank_branch_name' 		=> $val['BankBranch']['name'],
			);
        }
		//end bank_branch_list / get_bank_branch
		
		
		//start product_history / get_product_wise_open_bonus_and_price_combination
		$this->loadModel('DistOpenCombinationProduct');
		$conditions = array('Product.is_distributor_product' => 1);
        $open_combination=$this->DistOpenCombinationProduct->find('all',array(
            'conditions' => $conditions,
            'order'=>array('DistOpenCombinationProduct.product_id'),
			'recursive' => 0
            ));
        //pr($open_combination);exit;
        $product_history['product_history'] = array();
        $bonus_product_id=array();
        foreach ($open_combination as $key => $val) {
        	$bonus_product_id[$val['DistOpenCombinationProduct']['product_id']]=$val['DistOpenCombinationProduct']['product_id'];
            $product_history['product_history'][] = array(
				'is_bonus' 			=> $val['DistOpenCombination']['is_bonus'],
				'start_date' 		=> $val['DistOpenCombination']['start_date'],
				'end_date' 			=> $val['DistOpenCombination']['end_date'],
				'product_id' 		=> $val['DistOpenCombinationProduct']['product_id'],
				'combination_id' 	=> $val['DistOpenCombinationProduct']['combination_id'],
			);
        }
		//end product_history / get_product_wise_open_bonus_and_price_combination
		
		
		//start instrument_number / get_installment_no
		$this->loadModel('InstallmentNo');
        //$so_id = 20381;
        $installment = $this->InstallmentNo->find('all', array(
            'conditions' => array(
                'so_id' => $so_id
            ),
            'recursive' => -1
        ));
        $instrument_number['instrument_number'] = array();
        foreach ($installment as $key => $val) {
            $instrument_number['instrument_number'][] = array(
				'instrument_number_id' 			=> $val['InstallmentNo']['installment_no_id'],
				'instrument_number_name' 		=> $val['InstallmentNo']['installment_no_name'],
				'memo_number' 					=> $val['InstallmentNo']['memo_no'],
				'memo_value' 					=> $val['InstallmentNo']['memo_value'],
				'is_used' 						=> $val['InstallmentNo']['is_used'],
				'payment' 						=> $val['InstallmentNo']['payment'],
				'payment_id' 					=> $val['InstallmentNo']['payment_id'],
			);
        }
		//end instrument_number / get_installment_no
		
		
		//start instrument_type / get_instrument_type
		$this->LoadModel('InstrumentType');		
        $results = $this->InstrumentType->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'name')
        ));
        $instrument_type['instrument_type'] = array();
        foreach ($results as $key => $val) {
            $instrument_type['instrument_type'][] = array(
				'instrument_type_id' 			=> $val['InstrumentType']['id'],
				'instrument_type_name' 			=> $val['InstrumentType']['name']
			);
        }
		//end instrument_type / get_instrument_type
		
		
		//start location / new implemented 
		$this->LoadModel('LocationType');
        $results = $this->LocationType->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'name')
        ));
        $location['location'] = array();
        foreach ($results as $key => $val) {
            $location['location'][] = array(
				'location_id' 			=> $val['LocationType']['id'],
				'location_name' 		=> $val['LocationType']['name']
			);
        }
		//end location / new implemented 
		
		
		//start materials / get_inventory_status
        $this->loadModel('InventoryStatus');
        $results = $this->InventoryStatus->find('all', array(
            'fields' => array('InventoryStatus.id', 'InventoryStatus.name'),
            'order' => array('InventoryStatus.id' => 'asc'),
            'recursive' => -1
        ));
        $materials['materials'] = array();
        foreach ($results as $key => $val) {
            $materials['materials'][] = array(
				'material_id' 			=> $val['InventoryStatus']['id'],
				'material_name' 		=> $val['InventoryStatus']['name']
			);
        }
		//end materials / get_inventory_status
		
		
		//start sales_week / get_weeks
        $this->loadModel('Week');
		
		$last_update_date = ($json_data['sales_week']['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['sales_week']['last_update']));
       
		$conditions = array(
		'Week.start_date >='=>date('Y-m-d',strtotime(date('Y-m-01').' -2 months')),
		'Week.start_date <='=>date('Y-m-t'),
		//'Week.company_id' => $company_id
		);
        
		if ($last_update_date != 'all') $conditions['Week.updated_at >'] = $last_update_date;
		
        $weeks = $this->Week->find('all', array(
            'fields' => array('Week.id', 'Week.week_name', 'Week.updated_at', 'Week.start_date', 'Week.end_date'),
            'conditions' => $conditions,
            'order' => array('Week.updated_at' => 'asc'),
            'recursive' => -1
        ));

        $sales_week['sales_week'] = array();
        foreach ($weeks as $key => $val) {
            $sales_week['sales_week'][] = array(
				'sales_week_id' 			=> $val['Week']['id'],
				'sales_week_name' 			=> $val['Week']['week_name']
			);
        }
		//end sales_week / get_weeks
		
		
		//start unit / get_measurement_units
		$this->loadModel('MeasurementUnit');
        $last_update_date = ($json_data['unit']['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['unit']['last_update']));
		
		$conditions = array();
		//$conditions = array('MeasurementUnit.company_id' => $company_id);
        if ($last_update_date != 'all')$conditions['MeasurementUnit.updated_at >'] = $last_update_date;

        $measurement_units = $this->MeasurementUnit->find('all', array(
            'fields' => array('MeasurementUnit.id', 'MeasurementUnit.name', 'MeasurementUnit.updated_at'),
            'conditions' => $conditions,
            'order' => array('MeasurementUnit.updated_at' => 'asc'),
            'recursive' => -1
        ));

		$unit['unit'] = array();
        foreach ($measurement_units as $key => $val) {
            $unit['unit'][] = array(
				'unit_id' 			=> $val['MeasurementUnit']['id'],
				'unit_name' 		=> $val['MeasurementUnit']['name']
			);
        }
		//end unit / get_measurement_units
		
		//start orders / new implemented
        $this->loadModel('DistOrder');
        $this->loadModel('DistOrderDetail');
        $this->loadModel('Product');

        $res_status = 1;
        $three_days_pre = date('Y-m-d', strtotime('-3 days'));

		$conditions = array();

        $conditions = array(
		// 'DistOrder.sr_id' => $sr_id, 
		'DistSrRouteMapping.dist_sr_id' => $sr_id,
		'DistSrRouteMapping.dist_distributor_id' => $distributor_id,
		'DistOrder.status' => array(1,2), 
		/*'DistOrder.status' => 1,*/
		//'DistOrder.status !=' => 3,
		//'DistOrder.processing_status' => 1, 
		'DistOrder.order_date >' => $three_days_pre
		);

        $order_list = $this->DistOrder->find('all', array(
            'fields' => array('DistOrder.*'),
            'conditions' => $conditions,
            'joins'=>array(
            	array(
					'alias' => 'DistSrRouteMapping',
					'table' => 'dist_sr_route_mappings',
					'type' => 'INNER',
					'conditions' => 'DistOrder.dist_route_id = DistSrRouteMapping.dist_route_id'
				)
            	),
            'recursive' => -1
        ));

        $order_array = array();
		$mdata = array();
		/*echo $this->DistOrder->getLastquery();
		pr($order_list);exit;*/
        foreach ($order_list as $m) 
		{
            $mdata['id'] = $m['DistOrder']['id'];
            $mdata['order_number'] = $m['DistOrder']['dist_order_no'];
            $mdata['order_date'] = $m['DistOrder']['order_date'];
			$mdata['order_date_time'] = $m['DistOrder']['order_time'];
            $mdata['sales_person_id'] = $m['DistOrder']['sr_id'];
            $mdata['outlet_id'] = $m['DistOrder']['outlet_id'];
            $mdata['market_id'] = $m['DistOrder']['market_id'];
            $mdata['territory_id'] = $m['DistOrder']['territory_id'];
            $mdata['gross_value'] = $m['DistOrder']['gross_value'];
			$mdata['total_vat'] = $m['DistOrder']['total_vat'];
			
			$mdata['discount_value'] = $m['DistOrder']['discount_value'];
			
			$mdata['discount_percent'] = $m['DistOrder']['discount_percent'];
			$mdata['discount_type'] = $m['DistOrder']['discount_type'];
			
            /*$mdata['adjustment_amount'] = $m['DistOrder']['adjustment_amount'];
            $mdata['adjustment_note'] = $m['DistOrder']['adjustment_note'];
            $mdata['cash_recieved'] = $m['DistOrder']['cash_recieved'];
            $mdata['credit_amount'] = $m['DistOrder']['credit_amount'];
            $mdata['latitude'] = $m['DistOrder']['latitude'];
            $mdata['longitude'] = $m['DistOrder']['longitude'];*/
            $mdata['from_app'] = $m['DistOrder']['from_app'];
			$mdata['status'] = $m['DistOrder']['processing_status'];
			$mdata['total_discount'] = $m['DistOrder']['total_discount'];
            if($m['DistOrder']['from_app']== 0 && $m['DistOrder']['action']==1)
            {
                $mdata['updated_at'] = date('Y-m-d H:i:s',strtotime($m['DistOrder']['order_date']));
            }
            else
            {
                $mdata['updated_at'] = $m['DistOrder']['order_time'];
            }
            $mdata['action'] = ($res_status == 1 ? 1 : $m['DistOrder']['action']);
            $mdata['order_details'] = $m['DistOrderDetail'];
            $mm = 0;

            foreach ($m['DistOrderDetail'] as $each_order_details) 
			{
                $product_id = $each_order_details['product_id'];
                $product_info = $this->Product->find('first', array(
                    'fields' => array('product_type_id'),
                    'conditions' => array('id' => $product_id),
                    'recursive' => -1
                ));

                $type = "";
                if ($product_info['Product']['product_type_id'] == 1 && $each_order_details['price'] > 0) {
                    $type = 0;
                } else if ($product_info['Product']['product_type_id'] == 3) {
                    $type = 1;
                } else if ($product_info['Product']['product_type_id'] == 1 && $each_order_details['price'] < 1) {
                    $type = 2;
                }
                $mdata['order_details'][$mm]['product_type'] = $type;
				$mdata['order_details'][$mm]['quantity'] = $each_order_details['sales_qty'];
				$mdata['order_details'][$mm]['price'] = $each_order_details['actual_price'];
				$mdata['order_details'][$mm]['order_number'] = $m['DistOrder']['dist_order_no'];
				
				
                $mm++;
            }


            $order_array[] = $mdata;

        }
		
		$dis_order_array['orders'] = $order_array;
		//end orders / new implemented
		
		
		//start dist memos / new implemented
        $this->loadModel('DistMemo');
        $this->loadModel('DistMemoDetail');
        $this->loadModel('Product');

        $res_status = 1;
        $seven_days_pre = date('Y-m-d 00:00:00', strtotime('-3 days'));

		$conditions = array();

        $conditions = array(
        	// 'DistMemo.sr_id' => $sr_id, 
        	'DistSrRouteMapping.dist_sr_id' => $sr_id,
        	'DistSrRouteMapping.dist_distributor_id' => $distributor_id, 
        	'DistMemo.action' => 0, 
        	'DistMemo.memo_time >' => $seven_days_pre);
        
        $memo_list = $this->DistMemo->find('all', array(
            'fields' => array('DistMemo.*'),
            'conditions' => $conditions,
            'joins'=>array(
            	array(
					'alias' => 'DistSrRouteMapping',
					'table' => 'dist_sr_route_mappings',
					'type' => 'INNER',
					'conditions' => 'DistMemo.dist_route_id = DistSrRouteMapping.dist_route_id'
				)
            	),
            'recursive' => 1
        ));
		/*pr($memo_list);
		exit;*/
		
        $memo_array = array();
		$mdata = array();
        foreach ($memo_list as $m) {
            $mdata['id'] = $m['DistMemo']['id'];
            $mdata['memo_number'] = $m['DistMemo']['dist_memo_no'];
            $mdata['memo_date'] = $m['DistMemo']['memo_date'];
			$mdata['memo_date_time'] = $m['DistMemo']['memo_time'];
            $mdata['sales_person_id'] = $m['DistMemo']['sr_id'];
            $mdata['outlet_id'] = $m['DistMemo']['outlet_id'];
            $mdata['market_id'] = $m['DistMemo']['market_id'];
            $mdata['territory_id'] = $m['DistMemo']['territory_id'];
            $mdata['gross_value'] = $m['DistMemo']['gross_value'];
			$mdata['total_vat'] = $m['DistMemo']['total_vat'];
			
			$mdata['discount_value'] = $m['DistMemo']['discount_value'];
			$mdata['discount_percent'] = $m['DistMemo']['discount_percent'];
			$mdata['discount_type'] = $m['DistMemo']['discount_type'];
			
            $mdata['adjustment_amount'] = $m['DistMemo']['adjustment_amount'];
            $mdata['adjustment_note'] = $m['DistMemo']['adjustment_note'];
            $mdata['cash_recieved'] = $m['DistMemo']['cash_recieved'];
            $mdata['credit_amount'] = $m['DistMemo']['credit_amount'];
            $mdata['latitude'] = $m['DistMemo']['latitude'];
            $mdata['longitude'] = $m['DistMemo']['longitude'];
            $mdata['from_app'] = $m['DistMemo']['from_app'];
			$mdata['total_discount'] = $m['DistMemo']['total_discount'];
            if($m['DistMemo']['from_app']== 0 && $m['DistMemo']['action']==1)
            {
                $mdata['updated_at'] = date('Y-m-d H:i:s',strtotime($m['DistMemo']['memo_date']));
            }
            else
            {
                $mdata['updated_at'] = $m['DistMemo']['memo_time'];
            }
            $mdata['action'] = ($res_status == 1 ? 1 : $m['DistMemo']['action']);
            $mdata['memo_details'] = $m['DistMemoDetail'];
            $mm = 0;

            foreach ($m['DistMemoDetail'] as $each_memo_details) {

                $product_id = $each_memo_details['product_id'];
                $product_info = $this->Product->find('first', array(
                    'fields' => array('product_type_id'),
                    'conditions' => array('id' => $product_id),
                    'recursive' => -1
                ));

                $type = "";
                if ($product_info['Product']['product_type_id'] == 1 && $each_memo_details['price'] > 0) {
                    $type = 0;
                } else if ($product_info['Product']['product_type_id'] == 3) {
                    $type = 1;
                } else if ($product_info['Product']['product_type_id'] == 1 && $each_memo_details['price'] < 1) {
                    $type = 2;
                }

                $mdata['memo_details'][$mm]['product_type_id'] = $type;
                $mdata['memo_details'][$mm]['price'] = $each_memo_details['actual_price'];
                $mm++;
            }


            $memo_array[] = $mdata;

        }
		
		$dis_memo_array['memos'] = $memo_array;
		//end memos / new implemented
		
		
		
		
		
		//start general_notice / get_inbox_message
        $this->loadModel('MessageReceiver');
        $last_update_date = ($json_data['sales_week']['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['sales_week']['last_update']));
		//$so_id = 20223;
		$conditions = array(
			'MessageReceiver.receiver_id' => $so_id,
			'OR' => array(
				'MessageList.message_type' => 0,
				'MessageList.message_type' => 2
			)
		);
		
		if($last_update_date!='all')$conditions['MessageList.created_at >'] = $last_update_date;


        $inbox_message = $this->MessageReceiver->find('all', array(
            'fields' => array('MessageList.id', 'MessageList.message', 'MessageList.created_at'),
            'conditions' => $conditions,
            'order' => array('MessageReceiver.id' => 'asc'),
            'recursive' => 0
        ));
		
		/*pr($inbox_message);
		exit;*/

        $general_notice['general_notice'] = array();
        foreach ($inbox_message as $key => $val) {
            $general_notice['general_notice'][] = array(
				'notice_id' 		=> $val['MessageList']['id'],
				'notice_title' 		=> $val['MessageList']['message']
			);
        }
		//end general_notice / get_inbox_message
		
		
		//start outlet_categories / get_outlet_category
        $this->loadModel('DistOutletCategory');
		$last_update_date = ($json_data['outlet_categories']['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['outlet_categories']['last_update']));
		$conditions = array();
		$conditions = array('DistOutletCategory.is_active' => 1);
        if ($last_update_date != 'all')$conditions['DistOutletCategory.updated_at >'] = $last_update_date;
		
        $outlet_category_list = $this->DistOutletCategory->find('all', array(
            'fields' => array('DistOutletCategory.id', 'DistOutletCategory.category_name', 'DistOutletCategory.updated_at', 'DistOutletCategory.is_active'),
            'conditions' => $conditions,
            'order' => array('DistOutletCategory.updated_at' => 'asc'),
            'recursive' => -1
        ));


		$outlet_categories['outlet_categories'] = array();
		
        foreach ($outlet_category_list as $key => $val) {
            $outlet_categories['outlet_categories'][] = array(
				'outlet_category_id' 		=> $val['DistOutletCategory']['id'],
				'outlet_category_name' 		=> $val['DistOutletCategory']['category_name'],
				'is_active' 				=> $val['DistOutletCategory']['is_active']
			);
        }
    	//start outlet_categories / get_outlet_category
		
		
		//start for bounus (Maybe will change later)
		$this->loadModel('Bonus');
		$last_update_date = ($json_data['bonuses']['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['bonuses']['last_update']));
		
		$conditions = array();
        if ($last_update_date != 'all')$conditions['Bonus.updated_at >'] = $last_update_date;
		
        $bonus_product = $this->Bonus->find('all', array(
            'conditions' => $conditions,
            'order' => array('Bonus.updated_at' => 'asc'),
            'recursive' => -1
        ));
		
		$bonuses['bonuses'] = array();
        foreach ($bonus_product as $key => $val) {
			//pr($val);
             $bonuses['bonuses'][]	= array(
			 	'mother_product_id'		=> $val['Bonus']['mother_product_id'],
			 	'mother_product_qty'	=> $val['Bonus']['mother_product_quantity'],
				'bonus_product_id'		=> $val['Bonus']['bonus_product_id'],
				'bonus_product_qty'		=> $val['Bonus']['bonus_product_quantity'],
				'start_date'			=> $val['Bonus']['effective_date'],
				'end_date'				=> $val['Bonus']['end_date'],
				'updated_at'			=> $val['Bonus']['updated_at'],
			 );
        }
		//end for bounus
		
		
		//start discount percent for memo value	
		$this->loadModel('DistDiscount');
        $conditions = array(); 
		$conditions = array(
			'DistDiscount.is_active' => 1,
			'OR' => array(
				array('DistDiscount.office_id' => $office_id),
				array('DistDiscount.office_id' => NULL),
			)
		);
        $results = $this->DistDiscount->find('all', array(
            'conditions' => $conditions,
            'order' => array('DistDiscount.id' => 'desc'),
            //'recursive' => -1
        ));
        /*echo $this->DistDiscount->getLastquery();	
		pr($results);exit;*/
		
		$discounts['discounts'] = array();
        foreach($results as $val)
		{
			if($val['DistDiscount']['distributor_id']==NULL || $val['DistDiscount']['distributor_id']==$distributor_id)
			{
				foreach($val['DistDiscountDetail'] as $valD)
				{
					$discounts['discounts'][] = array(
						'discount_id' 			=> $valD['id'],
						'office_id' 			=> $val['DistDiscount']['office_id'],
						'distributor_id' 		=> $val['DistDiscount']['distributor_id'],
						'date_from' 			=> $valD['date_from'],
						'date_to' 				=> $valD['date_to'],
						'memo_value' 			=> $valD['memo_value'],
						'discount_percent' 		=> $valD['discount_percent'],
						'discount_type' 		=> $valD['discount_type'],
					);
				}
			}
		}
		//pr($discounts);exit;
		//end discount percent for memo value
		
		
		//start visite plan
		$this->loadModel('DistSrVisitPlan');
		$this->loadModel('DistSrVisitPlanDetail');
		$conditions = array(); 
		$conditions = array(
			'DistSrVisitPlan.code' => $sr_code, 
			'DistSrVisitPlan.distributor_id' => $distributor_id, 
			'DistSrVisitPlan.effective_date <=' => date('Y-m-d')
		);
		
		//pr($conditions);

        $plan_info = $this->DistSrVisitPlan->find('first', array(
            'conditions' => $conditions,
			'order' => array('id' => 'desc'),
 			'recursive' => -1
        ));
		$dist_sr_visit_plan_id = @$plan_info['DistSrVisitPlan']['id'];
		
		$conditions = array(); 
		$conditions = array('DistSrVisitPlanDetail.dist_sr_visit_plan_id' => $dist_sr_visit_plan_id);
		$week_id = @$json_data['week_id'];
		if($week_id)$conditions['DistSrVisitPlanDetail.week_id'] = $week_id;
	
		$visit_results = $this->DistSrVisitPlanDetail->find('all', array(
            'conditions' => $conditions,
 			'recursive' => -1
        ));
		
		//pr($visit_results);exit;
		
        $visit_list['visit_list'] = array();
        foreach($visit_results as $val){
			
			$visit_list['visit_list'][] = array(
				//'plan_id' 			=> $dist_sr_visit_plan_id,
				'plan_id' 			=> $val['DistSrVisitPlanDetail']['id'],
				'sat' 				=> $val['DistSrVisitPlanDetail']['sat'],
				'sun' 				=> $val['DistSrVisitPlanDetail']['sun'],
				'mon' 				=> $val['DistSrVisitPlanDetail']['mon'],
				'tue' 				=> $val['DistSrVisitPlanDetail']['tue'],
				'wed' 				=> $val['DistSrVisitPlanDetail']['wed'],
				'thu' 				=> $val['DistSrVisitPlanDetail']['thu'],
				'fri' 				=> $val['DistSrVisitPlanDetail']['fri'],
				'route_id' 			=> $val['DistSrVisitPlanDetail']['route_id'],
				'week_id' 			=> $val['DistSrVisitPlanDetail']['week_id'],
			);
		}
		//end visite plan
		
		
		//start stock info
		$this->loadModel('DistCurrentInventory');
		$conditions = array();

        $conditions = array(
			'DistCurrentInventory.store_id' => $store_id, 
			'Product.is_distributor_product' => 1, 
		);
		//if($product_type_id)$conditions['Product.product_type_id'] = $product_type_id;
		//if($product_category_id)$conditions['Product.product_category_id'] = $product_category_id;
				
        $stock_info = $this->DistCurrentInventory->find('all', array(
            'fields' => array('DistCurrentInventory.*','Product.*'),
            'conditions' => $conditions,
            'recursive' => 0
        ));

        $stock_info_array['stock_info'] = array();
		$s_data = array();
		//pr($stock_info);exit;
		
        foreach ($stock_info as $val) 
		{
            $s_data['product_name'] = $val['Product']['name'];
			$s_data['product_id'] = $val['Product']['id'];
			$s_data['unit'] = $val['Product']['sales_measurement_unit_id'];
			if($val['Product']['base_measurement_unit_id']==$val['Product']['sales_measurement_unit_id']){
				$s_data['quantity'] = $val['DistCurrentInventory']['qty'];
				$s_data['booking_quantity'] = $val['DistCurrentInventory']['booking_qty'];
				$s_data['invoice_qty'] = $val['DistCurrentInventory']['invoice_qty'];
				$s_data['bonus_quantity'] = $val['DistCurrentInventory']['bonus_qty'];
				$s_data['bonus_booking_quantity'] = $val['DistCurrentInventory']['bonus_booking_qty'];
			}else{
            	$s_data['quantity'] = $this->unit_convertfrombase($val['DistCurrentInventory']['product_id'], $val['Product']['sales_measurement_unit_id'], $val['DistCurrentInventory']['qty']);
				$s_data['booking_quantity'] = $this->unit_convertfrombase($val['DistCurrentInventory']['product_id'], $val['Product']['sales_measurement_unit_id'], $val['DistCurrentInventory']['booking_qty']);
				$s_data['invoice_qty'] = $this->unit_convertfrombase($val['DistCurrentInventory']['product_id'], $val['Product']['sales_measurement_unit_id'], $val['DistCurrentInventory']['invoice_qty']);
				$s_data['bonus_quantity'] = $this->unit_convertfrombase($val['DistCurrentInventory']['product_id'], $val['Product']['sales_measurement_unit_id'], $val['DistCurrentInventory']['bonus_qty']);
				$s_data['bonus_booking_quantity'] = $this->unit_convertfrombase($val['DistCurrentInventory']['product_id'], $val['Product']['sales_measurement_unit_id'], $val['DistCurrentInventory']['bonus_booking_qty']);
			}
            $stock_info_array['stock_info'][] = $s_data;
        }
		//end stock info
		
		/*-------- Fiscal year data in apps : start -----------*/
		$this->LoadModel('FiscalYear');
		$fiscal_years=$this->FiscalYear->find('all',array(
			'conditions'=>array(
				'FiscalYear.id >= (SELECT fy_v.id
                    FROM app_versions app_v
                    inner join fiscal_years fy_v on fy_v.id=app_v.fiscal_year_id_for_bonus_report
                    WHERE app_v.id = 2)'
                    ),
			'recursive'=>-1
			));
		$fiscal_year_info['fiscal_year']=array();
		foreach($fiscal_years as $data)
		{
			$fiscal_year_info['fiscal_year'][]=$data['FiscalYear'];
		}
		/*-------- Fiscal year data in apps : END   -----------*/
		/*-------- MOnth data in apps : start -----------*/
		$this->LoadModel('Month');
		$month=$this->Month->find('all',array(
			'recursive'=>-1
			));
		$month_info['month']=array();
		foreach($month as $data)
		{
			$month_info['month'][]=array(
				'id'=>$data['Month']['id'],
				'name'=>$data['Month']['name'],
				);
		}
		/*-------- MOnth data in apps : END   -----------*/

		$res = array_merge(
		$user_info, 
		//$bonus_party, 
		//$bonuses, 
		//$credit_collection, 
		$current_promotion_products, 
		//$deposit_balance, 
		//$deposits,
		//$designation
		$product,
		$product_categories,
		$product_price,
		$product_type,
		$product_combinations,
		$territories,
		$thanas,
		$route,
		$markets,
		$outlets,
		$bank_branch,
		$product_history,
		//$installment,
		$instrument_type,
		$location,
		$materials,
		$sales_week,
		$unit,
		$dis_order_array,
		$dis_memo_array,
		$general_notice,
		$outlet_categories,
		$bonuses,
		$discounts,
		$visit_list,
		$stock_info_array,
		/*$dist_bonus_product,*/
		$fiscal_year_info,
		$month_info
		);

        $this->set(array(
            'response' => $res,
            '_serialize' => array('response')
        ));
    }
	/* ------------------- End Dist Data Push --------------------- */
	
	
	/* ------------------- Dist Market Create --------------------- */
	public function dist_create_market() {
        $this->loadModel('DistMarket');

        $json_data = $this->request->input('json_decode', true);
        /*---------------------------- Mac check --------------------------------*/
				
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
        $all_inserted = true;
        $relation_array = array();

        $path = APP . 'logs/';
        $myfile = fopen($path . "dist_create_market.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);

        //pr($json_data['market_list']);
		
		$all_inserted = true;

        if (!empty($json_data['market_list'])) 
		{
            $market_array = array();
			$data = array();
			$res = array();
			
            foreach ($json_data['market_list'] as $val) 
			{
                $data['temp_id'] = $val['temp_id'];
                $data['code'] = $val['code'];
                $data['name'] = $val['market_name'];
                $data['address'] = $val['address'];
                $data['location_type_id'] = $val['location_type_id'];
                $data['thana_id'] = $val['thana_id'];
                $data['territory_id'] = $val['territory_id'];
				$data['dist_route_id'] = $val['route_id'];
				
                /* $data['is_active'] = 1; */
                $data['is_active'] = ($val['is_active'] != '' ? $val['is_active'] : 1);
                $data['created_at'] = isset($val['updated_at']) && $val['updated_at'] ? $val['updated_at'] : $this->current_datetime();
                $data['created_by'] = $json_data['sales_person_id'];
                $data['updated_at'] = isset($val['updated_at']) && $val['updated_at'] ? $val['updated_at'] : $this->current_datetime();


                $market_array = $data;

                if (is_numeric($val['temp_id'])) 
				{
                    $data['id'] = $val['temp_id'];
                    if ($this->DistMarket->save($data)) {
                        unset($data['id']);
                        $relation_array['new_id'] = $val['temp_id'];
                        $relation_array['previous_id'] = $val['temp_id'];
                        $res['replaced_relation'][] = $relation_array;
                    } else {
                        $all_inserted = false;
                    }
					$res['status'] = 1;
					if ($all_inserted) {
						$res['message'] = 'Market has been updated successfuly.';
					}else{
						$res['message'] = 'Market failed to update.';
					}
                } 
				else 
				{
                    $this->DistMarket->create();
                    if ($this->DistMarket->save($data)) {
                        unset($data['id']);
                        $relation_array['new_id'] = $this->DistMarket->getLastInsertID();
                        $relation_array['previous_id'] = $val['temp_id'];
                        $res['replaced_relation'][] = $relation_array;
                    } else {
                        $all_inserted = false;
                    }
					$res['status'] = 1;
					if ($all_inserted) {
						$res['message'] = 'Market has been created successfuly completed.';
					}else{
						$res['message'] = 'Market Failed to create.';
					}
                }
            }
        } 
		else 
		{
            $res['status'] = 0;
            $res['message'] = 'No Data Provided.';
        }

        $this->set(array(
            'market' => $res,
            '_serialize' => array('market')
        ));
    }
	/* ------------------- End Dist Market Create ----------------- */
	
	
	/* ---------------- Dist Market List Limit Wise ---------------- */
	public function dist_market_list() { 	
		$this->loadModel('DistMarket');	
		$this->loadModel('SalesPerson');	
		
		$json_data = $this->request->input('json_decode', true);
       
	    $path = APP . 'logs/';
        $myfile = fopen($path . "dist_market_list.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
	   
	    /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		/*pr($so_info);
		exit;*/
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];


        $conditions = array(); 
		$conditions = array('DistSrRouteMapping.dist_distributor_id' => $distributor_id,'DistSrRouteMapping.dist_sr_id' => $sr_id, 'DistMarket.is_active' => 1);
		
		$thana_id = isset($json_data['thana_id'])?$json_data['thana_id']:0;
		if($thana_id)$conditions['DistMarket.thana_id'] = $thana_id;
		
		$route_id = isset($json_data['route_id'])?$json_data['route_id']:0;
		if($route_id)$conditions['DistMarket.dist_route_id'] = $route_id;
		
		//pr($conditions);

        $market_list = $this->DistMarket->find('all', array(
            'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'DistRoute',
					'table' => 'dist_routes',
					'type' => 'INNER',
					'conditions' => 'DistRoute.id = DistMarket.dist_route_id'
				)
				,
				array(
					'alias' => 'DistSrRouteMapping',
					'table' => 'dist_sr_route_mappings',
					'type' => 'INNER',
					'conditions' => 'DistMarket.dist_route_id = DistSrRouteMapping.dist_route_id'
				)
			),
            'order' => array('DistMarket.name' => 'asc'),
			'limit' => 50,
			'page' => $json_data['page']?$json_data['page']:1,
			'recursive' => -1
        ));

        $markets = array();
        foreach($market_list as $val){
			$markets[] = array(
				'market_id' 				=> $val['DistMarket']['id'],
				'market_name' 				=> $val['DistMarket']['name'],
				'thana_id' 					=> $val['DistMarket']['thana_id'],
				'territory_id' 				=> $val['DistMarket']['territory_id'],
				'location_type_id' 			=> $val['DistMarket']['location_type_id'],
				'is_active' 				=> $val['DistMarket']['is_active'],
				//'market_code' 				=> $val['DistMarket']['code'],
				'address' 					=> $val['DistMarket']['address'],
				'route_id' 					=> $val['DistMarket']['dist_route_id'],
			);
		}
		
		$this->set(array(
            'markets' => $markets,
            '_serialize' => array('markets')
        ));
		
	}
	/* -------------- End Dist Market List Limit Wise -------------- */
	public function dist_market_details() { 	
		$this->loadModel('DistMarket');	
		$this->loadModel('SalesPerson');	
		
		$json_data = $this->request->input('json_decode', true);
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_market_details.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		/*pr($so_info);
		exit;*/
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];


        $conditions = array(); 
		//$conditions = array('DistRouteMapping.dist_distributor_id' => $distributor_id,'DistMarket.is_active' => 1);
		$market_id = @$json_data['market_id'];
		if($market_id)$conditions['DistMarket.id']=$market_id;
		

        $market_info = $this->DistMarket->find('first', array(
            'conditions' => $conditions,
			/*'joins' => array(
				array(
					'alias' => 'DistRoute',
					'table' => 'dist_routes',
					'type' => 'INNER',
					'conditions' => 'DistRoute.id = DistMarket.dist_route_id'
				),
				array(
					'alias' => 'DistRouteMapping',
					'table' => 'dist_route_mappings',
					'type' => 'INNER',
					'conditions' => 'DistMarket.dist_route_id = DistRouteMapping.dist_route_id'
				)
			),*/
            'order' => array('DistMarket.name' => 'asc'),
			//'limit' => 50,
			//'page' => $json_data['page']?$json_data['page']:1,
			'recursive' => -1
        ));

        $markets = array();
		$markets[] = array(
			'market_id' 				=> $market_info['DistMarket']['id'],
			'market_name' 				=> $market_info['DistMarket']['name'],
			'thana_id' 					=> $market_info['DistMarket']['thana_id'],
			'territory_id' 				=> $market_info['DistMarket']['territory_id'],
			'location_type_id' 			=> $market_info['DistMarket']['location_type_id'],
			'is_active' 				=> $market_info['DistMarket']['is_active'],
			//'market_code' 			=> $market_info['DistMarket']['code'],
			'address' 					=> $market_info['DistMarket']['address'],
			'route_id' 					=> $market_info['DistMarket']['dist_route_id'],
		);
		
		
		$this->set(array(
            'markets' => $markets,
            '_serialize' => array('markets')
        ));
		
	}
		
	/* ------------------- Dist Outlet Create ---------------------- */
	public function dist_create_outlet() {
        $this->loadModel('DistOutlet');
		$this->loadModel('DistOutletImage');
		
        $json_data = $this->request->input('json_decode', true);

        
        $json = $this->request->input();
        $all_inserted = true;
        $relation_array = array();

        /* removing unicode characters */
        $raw_json_data = str_replace("蹢", '"', $json);
        $json_data = json_decode($raw_json_data, TRUE);
		
		/*pr($json_data);
		exit;*/
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {

            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }

        $path = APP . 'logs/';
        $myfile = fopen($path . "dist_create_outlet.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);


        if (!empty($json_data['outlet_list'])) {
            // data array							
            $outlet_array = array();
			$data = array();
			$res = array();
            foreach ($json_data['outlet_list'] as $val) 
			{
				//pr($val);
				
                $data['temp_id'] = $val['temp_id'];
                $data['name'] = $val['outlet_name'];
                $data['in_charge'] = '';
                $data['ownar_name'] = $val['owner_name'];
                $data['address'] = $val['address'];
                $data['mobile'] = $val['mobile'];
                $data['dist_market_id'] = (is_numeric($val['market_id']) ? $val['market_id'] : 0);
				$data['dist_route_id'] = (is_numeric($val['route_id']) ? $val['route_id'] : 0);
                //$data['market_id'] = $val['market_id'];
                $data['category_id'] = $val['outlet_category_id'];
                $data['is_pharma_type'] = 0;
                $data['is_ngo'] = 0;
                $data['institute_id'] = 0;
                $data['is_active'] = isset($val['isActivated']) && $val['isActivated']?$val['isActivated']:0;
				$data['from_app'] = 1;
				$data['created_by'] = $json_data['sales_person_id'];
				
                //$data['created_at'] = date("Y-m-d H:i:s", strtotime($val['created_at']));
                //$data['updated_at'] = date("Y-m-d H:i:s", strtotime($val['created_at']));
				
				$data['created_at'] = $this->current_datetime();  //request from tanvir
                $data['updated_at'] = $this->current_datetime();
				
                $data['updated_by'] = $json_data['sales_person_id'];
                $data['latitude'] = (isset($val['latitude']) != '' ? $val['latitude'] : 0);
                $data['longitude'] = (isset($val['longitude']) != '' ? $val['longitude'] : 0);
                $data['bonus_type_id'] = (isset($val['bonus_party_type']) != '' && $val['bonus_party_type'] != 'null' ? $val['bonus_party_type'] : 0);               				
				
                if (is_numeric($val['temp_id'])) 
				{
                    $data['id'] = $val['temp_id'];
                    if ($this->DistOutlet->save($data)) {
                        unset($data['id']);
                        $relation_array['new_id'] = $val['temp_id'];
                        $relation_array['previous_id'] = $val['temp_id'];
                        $res['replaced_relation'][] = $relation_array;
						
						//create outlet images
						$outlet_id = $val['temp_id'];
						$this->DistOutletImage->deleteAll(array('DistOutletImage.dist_outlet_id' => $outlet_id));
						
						if($val['images']){
							$image_details = array();
							$total_image_data = array();
							
							//pr($val['images']);exit;
							
							foreach($val['images'] as $key => $image){
								
								if(isset($image['status']) && $image['status']==1){
									// Obtain the original content (usually binary data)
									$bin = base64_decode($image['image_url']);
									
									// Gather information about the image using the GD library
									$size = getImageSizeFromString($bin);
									
									// Mime types are represented as image/gif, image/png, image/jpeg, and so on
									// Therefore, to extract the image extension, we subtract everything after the “image/” prefix
									$ext = substr($size['mime'], 6);
									
									// Specify the location where you want to save the image
									$file_url = "dist_outlet_images/".$outlet_id."_".$key."_".time().".{$ext}";
									$img_file = WWW_ROOT.$file_url;
									$image_url = BASE_URL.'app/webroot/'.$file_url;
									// Save binary data as raw data (that is, it will not remove metadata or invalid contents)
									// In this case, the PHP backdoor will be stored on the server
									if(file_put_contents($img_file, $bin)){
										$image_details['DistOutletImage']['dist_outlet_id'] = $outlet_id;
										$image_details['DistOutletImage']['image_url'] = $image_url;
										$bin = '';
									}
								}else{
									
									if(isset($image['image_url']) && $image['image_url']){
										$image_details['DistOutletImage']['dist_outlet_id'] = $outlet_id;
										$image_details['DistOutletImage']['image_url'] = $image['image_url'];
									}
								
								}
								$total_image_data[] = $image_details;
							}
							
							/*pr($total_image_data);
							exit;*/
							
							if($total_image_data)$this->DistOutletImage->saveAll($total_image_data);
						}
						
                    } 
					else 
					{
                        $all_inserted = false;
                    }
					$res['status'] = 1;
					if ($all_inserted) {
						$res['message'] = 'Outlet has been updated successfuly.';
					} else {
						$res['message'] = 'Outlet failed to update.';
					}
                } 
				else 
				{
                    $this->DistOutlet->create();
                    if ($this->DistOutlet->save($data)) 
					{
                        unset($data['id']);
                        $relation_array['new_id'] = $this->DistOutlet->getLastInsertID();
                        $relation_array['previous_id'] = $val['temp_id'];
                        $res['replaced_relation'][] = $relation_array;
						
						
						//create outlet images
						$outlet_id = $this->DistOutlet->getLastInsertID();
						
						if($val['images']){
							
							$image_details = array();
							$total_image_data = array();
							foreach($val['images'] as $key => $image){
								
								// Obtain the original content (usually binary data)
								$bin = base64_decode($image);
								
								// Gather information about the image using the GD library
								$size = getImageSizeFromString($bin);
								
								// Mime types are represented as image/gif, image/png, image/jpeg, and so on
								// Therefore, to extract the image extension, we subtract everything after the “image/” prefix
								$ext = substr($size['mime'], 6);
								
								// Specify the location where you want to save the image
								$file_url = "dist_outlet_images/".$outlet_id."_".$key."_".time().".{$ext}";
								$img_file = WWW_ROOT.$file_url;
								$image_url = BASE_URL.'app/webroot/'.$file_url;
								// Save binary data as raw data (that is, it will not remove metadata or invalid contents)
								// In this case, the PHP backdoor will be stored on the server
								if(file_put_contents($img_file, $bin)){
									$image_details['DistOutletImage']['dist_outlet_id'] = $outlet_id;
									$image_details['DistOutletImage']['image_url'] = $image_url;
									$bin = '';
								}
								$total_image_data[] = $image_details;
								
							}
							//pr($total_image_data);
							if($total_image_data)$this->DistOutletImage->saveAll($total_image_data);
						}
						
                    } 
					else 
					{
                        $all_inserted = false;
                    }
					$res['status'] = 1;
					if ($all_inserted) {
						$res['message'] = 'Outlet has been created successfuly.';
					} else {
						$res['message'] = 'Outlet Failed to create.';
					}
                }
            }

            
        } 
		else 
		{
            $res['status'] = 0;
            $res['message'] = 'No Data Provided.';
        }

        $path = APP . 'logs/';
        $myfile = fopen($path . "dist_create_outlet_response.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . serialize($res));
        fclose($myfile);

        $this->set(array(
            'outlet' => $res,
            '_serialize' => array('outlet')
        ));
    }
	/* ------------------- End Dist Outlet Create ------------------ */
	

	/* ------------------- Dist Outlet Update ---------------------- */
	public function dist_update_outlet() {
        $this->loadModel('DistOutlet');
		$this->loadModel('DistOutletImage');
		
        $json_data = $this->request->input('json_decode', true);

        
        $json = $this->request->input();
        $all_inserted = true;
        $relation_array = array();

        /* removing unicode characters */
        $raw_json_data = str_replace("蹢", '"', $json);
        $json_data = json_decode($raw_json_data, TRUE);
		
		/*pr($json_data);
		exit;*/
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {

            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }

        $path = APP . 'logs/';
        $myfile = fopen($path . "dist_update_outlet.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);

		$data = array();
        
		if(!empty($json_data['outlet_id'])) 
		{
			$data['id'] = $json_data['outlet_id'];
			$data['updated_at'] = $this->current_datetime();
			$data['updated_by'] = $json_data['sales_person_id'];
			$data['latitude'] = (isset($json_data['latitude']) != '' ? $json_data['latitude'] : 0);
			$data['longitude'] = (isset($json_data['longitude']) != '' ? $json_data['longitude'] : 0);
			
			/*echo '<pre>';
			print_r($data);
			exit;*/
			
			if ($this->DistOutlet->save($data)) 
			{
				$all_inserted = true;
			} 
			else 
			{
				$all_inserted = false;
			}
			
			if ($all_inserted) {
				$res['message'] = 'Outlet has been updated successfuly.';
			} else {
				$res['message'] = 'Outlet failed to update.';
			}
        } 
		else 
		{
            $res['status'] = 0;
            $res['message'] = 'No Data Provided.';
        }

        $path = APP . 'logs/';
        $myfile = fopen($path . "dist_create_outlet_response.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . serialize($res));
        fclose($myfile);

        $this->set(array(
            'outlet' => $res,
            '_serialize' => array('outlet')
        ));
    }
	/* ------------------- End Dist Outlet Update ------------------ */
	
	
	/* ---------------- Dist Outlet List Limit Wise ---------------- */
	public function dist_outlet_list() { 	
		$this->loadModel('DistOutlet');	
		
		
		$json_data = $this->request->input('json_decode', true);
        /*---------------------------- Mac check --------------------------------*/
			
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_outlet_list.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$this->loadModel('SalesPerson');	
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		
		//$last_update_date = 'all';
        $conditions = array(); 
		//$conditions = array('DistRouteMapping.dist_distributor_id' => $distributor_id);
		
		$thana_id = @$json_data['thana_id'];
		$route_id = @$json_data['route_id'];
		$market_id = @$json_data['market_id'];
		
		$conditions['DistSrRouteMapping.dist_sr_id'] = $sr_id;
		$conditions['DistSrRouteMapping.dist_distributor_id'] = $distributor_id;
		if($thana_id)$conditions['DistMarket.thana_id'] = $thana_id;
		if($route_id)$conditions['DistRoute.id'] = $route_id;
		if($market_id)$conditions['DistOutlet.dist_market_id'] = $market_id;
		
		//pr($conditions );

        $outlet_list = $this->DistOutlet->find('all', array(
            'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'DistMarket',
					'table' => 'dist_markets',
					'type' => 'INNER',
					'conditions' => 'DistOutlet.dist_market_id = DistMarket.id'
				),
				array(
					'alias' => 'DistRoute',
					'table' => 'dist_routes',
					'type' => 'INNER',
					'conditions' => 'DistRoute.id = DistMarket.dist_route_id'
				),
				array(
					'alias' => 'DistSrRouteMapping',
					'table' => 'dist_sr_route_mappings',
					'type' => 'INNER',
					'conditions' => 'DistMarket.dist_route_id = DistSrRouteMapping.dist_route_id'
				)
			),
			'fields' => array('DistOutlet.*', 'DistMarket.thana_id',),
            'order' => array('DistOutlet.name' => 'asc'),
			'limit' => 50,
			'page' => $json_data['page']?$json_data['page']:1,
			'recursive' => -1
        ));

        $outlets = array();
        foreach($outlet_list as $val){
			$outlets[] = array(
				'outlet_id' 				=> $val['DistOutlet']['id'],
				'outlet_name' 				=> $val['DistOutlet']['name'],
				'address' 					=> $val['DistOutlet']['address'],
				'incharge_name' 			=> $val['DistOutlet']['in_charge'],
				'thana_id' 					=> $val['DistMarket']['thana_id'],
				'market_id' 				=> $val['DistOutlet']['dist_market_id'],
				'outlet_category_id' 		=> $val['DistOutlet']['category_id'],
				'mobile' 					=> $val['DistOutlet']['mobile'],
				'bonus_type_id' 			=> $val['DistOutlet']['bonus_type_id'],
				'route_id' 					=> $val['DistOutlet']['dist_route_id'],
				'isActivated' 				=> $val['DistOutlet']['is_active'],
				'latitude' 					=> $val['DistOutlet']['latitude'],
				'longitude' 				=> $val['DistOutlet']['longitude'],
				//'ngo_institute_id' 			=> $val['DistOutlet']['institute_id'],
				//'project_id' 				=> $val['DistOutlet']['project_id'],
				
				/*'owner_name' 				=> $val['DistOutlet']['ownar_name'],
				'address' 					=> $val['DistOutlet']['address'],
				'outlet_code' 				=> $val['DistOutlet']['code'],
				'pharma_type' 				=> $val['DistOutlet']['is_pharma_type'],
				'latitude' 					=> $val['DistOutlet']['latitude'],
				'longitude' 				=> $val['DistOutlet']['longitude'],
				'isNgo' 					=> $val['DistOutlet']['is_ngo'],
				
				'isUpdated' 				=> '',
				'is_within_group' 			=> $val['DistOutlet']['is_within_group'],
				
				'updated_at' 				=> $val['DistOutlet']['updated_at'],*/
			);
		}
		
		$this->set(array(
            'outlets' => $outlets,
            '_serialize' => array('outlets')
        ));
		
	}
	/* -------------- End Dist Outlet List Limit Wise -------------- */
	public function dist_outlet_details() { 	
		$this->loadModel('DistOutlet');	
		$this->loadModel('DistOutletImage');	
		$this->loadModel('SalesPerson');	
		
		$json_data = $this->request->input('json_decode', true);
        /*---------------------------- Mac check --------------------------------*/
				
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		
		//$last_update_date = 'all';
        $conditions = array(); 
		$conditions = array('DistOutlet.id' => $json_data['outlet_id']);
		
		//pr($conditions );

        $val = $this->DistOutlet->find('first', array(
            'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'DistMarket',
					'table' => 'dist_markets',
					'type' => 'INNER',
					'conditions' => 'DistOutlet.dist_market_id = DistMarket.id'
				),
				array(
					'alias' => 'Thana',
					'table' => 'thanas',
					'type' => 'INNER',
					'conditions' => 'Thana.id = DistMarket.thana_id'
				),
				array(
					'alias' => 'DistRoute',
					'table' => 'dist_routes',
					'type' => 'INNER',
					'conditions' => 'DistRoute.id = DistMarket.dist_route_id'
				)
			),
			'fields' => array('DistOutlet.*', 'DistMarket.thana_id','DistMarket.name','Thana.name','DistRoute.id','DistRoute.name'),
			'recursive' => -1
        ));
		
		$outlets = array();
					
		$outlets[] = array(
			'outlet_id' 				=> $val['DistOutlet']['id'],
			'outlet_name' 				=> $val['DistOutlet']['name'],
			'address' 					=> $val['DistOutlet']['address'],
			'incharge_name' 			=> $val['DistOutlet']['in_charge']?$val['DistOutlet']['in_charge']:'',
			'mobile' 					=> $val['DistOutlet']['mobile'],
			'thana_id' 					=> $val['DistMarket']['thana_id'],
			'thana_name' 				=> $val['Thana']['name'],
			'market_id' 				=> $val['DistOutlet']['dist_market_id'],
			'market_name' 				=> $val['DistMarket']['name'],
			'outlet_category_id' 		=> $val['DistOutlet']['category_id'],
			'owner_name' 				=> $val['DistOutlet']['ownar_name']?$val['DistOutlet']['ownar_name']:'',
			'pharma_type' 				=> $val['DistOutlet']['is_pharma_type'],
			'isActivated' 				=> $val['DistOutlet']['is_active'],
			'bonus_party_type' 			=> $val['DistOutlet']['bonus_type_id']?$val['DistOutlet']['bonus_type_id']:'',
			'route_id' 					=> $val['DistOutlet']['dist_route_id'],
			'route_name' 				=> $val['DistRoute']['name']?$val['DistRoute']['name']:'',
		);
		
		$outlet_images = $this->DistOutletImage->find('all', array(
            'conditions' => array(
                'DistOutletImage.dist_outlet_id' => $json_data['outlet_id'],
            ),
            'recursive' => -1
        ));
		//pr($outlet_images);exit;
		$images = array();
		foreach($outlet_images as $outlet_image){
			//$images[$outlet_image['DistOutletImage']['id']]['image_url'] = $outlet_image['DistOutletImage']['image_url'];
			$images[] = $outlet_image['DistOutletImage']['image_url'];
		}
		
		$outlets['images']=$images;
		
		//pr($outlets);
		
		//$j = json_encode($outlets, JSON_UNESCAPED_SLASHES);
		
		$this->set(array(
            'outlets' => $outlets,
            '_serialize' => array('outlets')
        ));
		
	}
	
	
	
	
	/* ------------------- Dist Order Create ----------------------- */
	public function dist_create_order() {
		$this->loadModel('DistOrder');
		$this->loadModel('DistOrderDetail');
		
		//$this->loadModel('DistOrder');
		//$this->loadModel('DistOrderDetail');
		
		$this->loadModel('SalesPerson');
		$this->loadModel('DistStore');
		$this->loadModel('Product');
		$this->loadModel('DistMarket');
		 

        $json_data = $this->request->input('json_decode', true);
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_create_order.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
        $all_inserted = true;
        $relation_array = array();

        //pr($json_data);exit;
		
		
		$all_inserted = true;
		
		$so_id = $json_data['sales_person_id'];
		
		if (!empty($json_data['order_list'])) 
		{
			//user info
			$so_info = $this->SalesPerson->find('first', array(
				'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistSalesRepresentative.code','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
				'conditions' => array('SalesPerson.id' => $so_id),
				'joins' => array(
					array(
						'alias' => 'DistSalesRepresentative',
						'table' => 'dist_sales_representatives',
						'type' => 'INNER',
						'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
					),
					array(
						'alias' => 'DistDistributor',
						'table' => 'dist_distributors',
						'type' => 'INNER',
						'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
					),
					array(
						'alias' => 'DistStore',
						'table' => 'dist_stores',
						'type' => 'INNER',
						'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
					),
					array(
						'alias' => 'Office',
						'table' => 'offices',
						'type' => 'INNER',
						'conditions' => 'SalesPerson.office_id = Office.id'
					)
				),
				
				'recursive' => -1
			));
			
			/*pr($so_info);
			exit;*/
			
			$office_id = $so_info['Office']['id'];
			$distributor_id = $so_info['DistDistributor']['id'];
			$store_id = $so_info['DistStore']['id'];
			$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
			$sr_code = $so_info['DistSalesRepresentative']['code'];
			$sale_type_id = 10;

			$path = APP . 'logs/DBwiseLog/'.$distributor_id.'/'.date('Y-m-d').'/';
			if(!file_exists($path))
			{
				 mkdir($path, 0777, true);
			}
	        $myfile = fopen($path . "dist_create_order.txt", "a") or die("Unable to open file!");
	        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
	        fclose($myfile);

			//pr($json_data['order_list']);exit;
			
			foreach($json_data['order_list'] as $result)
			{				
				//get territory_id and thana_id
				$market_info = $this->DistMarket->find('first', array(
					'conditions' => array('DistMarket.id' => $result['market_id']),
					'recursive' => -1
				));
	
				$territory_id = $market_info['DistMarket']['territory_id'];
				$thana_id = $market_info['DistMarket']['thana_id'];
				
				$order_date = date('Y-m-d', strtotime($result['order_date']));
				//end get territory_id and thana_id
				
				//get ae and tso id
				$this->loadModel('DistTsoMappingHistory');            
				if($order_date && $distributor_id)
				{					
					$qry="select distinct dist_tso_id from dist_tso_mapping_histories
						  where office_id=$office_id and is_change=1 and dist_distributor_id=$distributor_id and 
							'".$order_date."' between effective_date and 
							case 
							when end_date is not null then 
							 end_date
							else 
							getdate()
							end";
	
					$dist_data=$this->DistTsoMappingHistory->query($qry);
					//pr($qry);exit;
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
						'".$order_date."' between effective_date and 
						case 
						when effective_end_date is not null then 
						 effective_end_date
						else 
						getdate()
						end";
	
					$ae_data=$this->DistTsoMappingHistory->query($qry2);
					//pr($ae_data);die();
					$ae_ids=array();
				   
					foreach ($ae_data as $k => $v) {
						$ae_ids[]=$v[0]['dist_area_executive_id'];
					}
					$ae_id="";
					
					if($ae_ids)
					{
						$ae_id=$ae_ids[0];
					}
				}
				//end get ae and tso id
				
				
				$count = $this->DistOrder->find('count', array(
					'conditions' => array(
						'DistOrder.dist_order_no' => $result['order_number']
					)
				));
				
				$total_product_data = array();
				
				
				
				if($count==0)
				{
					$this->dist_create_visit_plan($result, $office_id, $distributor_id, $sr_id, $sr_code);
					
					$OrderData = array();
					$OrderData['office_id'] = $office_id;
					$OrderData['distributor_id'] = $distributor_id;
					$OrderData['sr_id'] = $sr_id;
					$OrderData['dist_route_id'] = $result['route_id'];
					$OrderData['sale_type_id'] = $sale_type_id;
					$OrderData['territory_id'] = $territory_id;
					$OrderData['thana_id'] = $thana_id;
					$OrderData['market_id'] = $result['market_id'];
					$OrderData['outlet_id'] = $result['outlet_id'];
					$OrderData['entry_date'] = date('Y-m-d', strtotime($result['order_date']));
					$OrderData['order_date'] = date('Y-m-d', strtotime($result['order_date']));
					$OrderData['dist_order_no'] = $result['order_number'];
					$OrderData['gross_value'] = $result['gross_value'];
					$OrderData['total_vat'] = $result['total_vat'];
					$OrderData['discount_value'] = $result['discount_value'];
					$OrderData['discount_percent'] = $result['discount_percent'];
					$OrderData['discount_type'] = $result['discount_type'];
					$OrderData['cash_recieved'] = 0;
					$OrderData['credit_amount'] = 0;
					$OrderData['is_active'] = 1;
					$OrderData['status'] = 1;
					$OrderData['order_time'] = $result['order_date_time']&&$result['order_date_time']!='null'?$result['order_date_time']:$result['order_date'];
					$OrderData['sales_person_id'] = $so_id;
					$OrderData['ae_id'] = $ae_id;
					$OrderData['tso_id'] = $tso_id;
					$OrderData['from_app'] = 1;
					$OrderData['action'] = 0;
					$OrderData['is_program'] = 0;
					$OrderData['order_reference_no'] = '';
					
					$OrderData['latitude'] = $result['latitude'];
					$OrderData['longitude'] = $result['longitude'];
					
					$OrderData['created_at'] = isset($result['updated_at']) && $result['updated_at'] ? $result['updated_at'] : $this->current_datetime();
					$OrderData['created_by'] = $so_id;
					$OrderData['updated_at'] = isset($result['updated_at']) && $result['updated_at'] ? $result['updated_at'] : $this->current_datetime();
					$OrderData['updated_by'] = $so_id;
					
					//pr($OrderData);exit;
					
					$this->DistOrder->create();
	
					if ($this->DistOrder->save($OrderData)) 
					{
						// ec calculation 
						$this->dist_ec_calculation($result['gross_value'], $result['outlet_id'], $sr_code, $office_id, $OrderData['order_date'], 1);
						// oc calculation 
						$this->dist_oc_calculation($sr_code, $office_id, $result['gross_value'], $result['outlet_id'], $OrderData['order_date'], $OrderData['order_time'], 1);
						
						$order_id = $this->DistOrder->getLastInsertId();
	
						if ($order_id) 
						{
							
							$all_product_id = array_map(function($element) {
								return $element['product_id'];
							}, $result['order_details']);
							
							foreach ($result['order_details'] as $val) 
							{
								if ($val == NULL) {
									continue;
								}
								
								$product_id = $order_details['DistOrderDetail']['product_id'] = $val['product_id'];
								
								$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
								$product_list = Set::extract($products, '{n}.Product');
	
								$punits_pre = $this->search_array($product_id, 'id', $product_list);
								//pr($punits_pre);//die();
								
								$order_details['DistOrderDetail']['dist_order_id'] = $order_id;
								$order_details['DistOrderDetail']['measurement_unit_id'] = $val['measurement_unit_id']>0?$val['measurement_unit_id']:$punits_pre['sales_measurement_unit_id'];
								$sales_price = $order_details['DistOrderDetail']['price'] = $val['price']?$val['price']:0;
								$sales_qty = $order_details['DistOrderDetail']['sales_qty'] = $val['quantity'];
								
								$order_details['DistOrderDetail']['vat'] = $val['vat']?$val['vat']:0;
								
								$product_price_slab_id = 0;
								if ($sales_price > 0) {
									$product_price_slab_id = $this->dist_get_product_price_id($val['product_id'], $sales_price, $all_product_id, $OrderData['order_date'], 1);
									//pr($product_price_slab_id);exit;
								}
								$order_details['DistOrderDetail']['product_price_id'] = $product_price_slab_id['product_price_id'];
								//$order_details['DistOrderDetail']['product_combination_id'] = $product_price_slab_id['combination_id'];
								
								$order_details['DistOrderDetail']['price'] =$sales_price?($sales_price-($val['discount_amount']?$val['discount_amount']:0)):0;
								$order_details['DistOrderDetail']['actual_price'] = $sales_price;
								$order_details['DistOrderDetail']['bonus_qty']= 0;
								$order_details['DistOrderDetail']['bonus_product_id'] = 0;
	
								//Start for bonus
								$order_details['DistOrderDetail']['is_bonus'] = $val['is_bonus'];
								$order_details['DistOrderDetail']['bonus_id'] = $val['bonus_id'];
								$order_details['DistOrderDetail']['bonus_scheme_id'] = 0;
								//End for bouns
								
								$order_details['DistOrderDetail']['discount_amount'] = $val['discount_amount'];
								$order_details['DistOrderDetail']['discount_type'] = $val['discount_type'];
								$order_details['DistOrderDetail']['policy_type'] = $val['policy_type'];
								$order_details['DistOrderDetail']['policy_id'] = $val['policy_id'];
								
	
								$total_product_data[] = $order_details;
							   
								//update inventory
								$stock_hit=1;
								if ($stock_hit) {
									if($val['measurement_unit_id']>0)
									{
										if ($val['measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) 
										{
											
											$base_quantity = ROUND($sales_qty);
											
										} 
										else 
										{
											$base_quantity = $this->unit_convert($product_id, $val['measurement_unit_id'], $sales_qty);
										}
									}
									else
									{
										if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) 
										{
											
											$base_quantity = ROUND($sales_qty);
											
										} 
										else 
										{
											$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
										}
									}
	
									$update_type = 'add';
									
									$this->dist_booking_update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 3, $OrderData['order_date']);
								}
	
								// sales calculation (target qty and amount achievement)
								$tt_price = $sales_qty * $sales_price;
								if ($sales_price > 0){
									$this->dist_sales_calculation($product_id, $sr_code, $office_id, $sales_qty, $tt_price, $OrderData['order_date'], 1);
								}
								
							}
							
							//pr($total_product_data);exit;
							//die();
							$this->DistOrderDetail->saveAll($total_product_data);
							
							$res['status'] = 1;
							//$res['memo_no'] = $result['order_number'];
							$res['message'] = 'Orders has been created successfuly.';
						}
					}
				}
				else
				{
					$order_id_arr = $this->DistOrder->find('first', array(
                        'conditions' => array(
                            'DistOrder.dist_order_no' => $result['order_number']
                        )
                    ));
					//pr($order_id_arr);
                   
				    $old_order_id = $order_id_arr['DistOrder']['id'];	
					
					$this->dist_order_delete($old_order_id, $sr_code, $office_id);
					
					$OrderData = array();
					$OrderData['id'] = $old_order_id;
					$OrderData['office_id'] = $office_id;
					$OrderData['distributor_id'] = $distributor_id;
					$OrderData['sr_id'] = $sr_id;
					$OrderData['dist_route_id'] = $result['route_id'];
					$OrderData['sale_type_id'] = $sale_type_id;
					$OrderData['territory_id'] = $territory_id;
					$OrderData['thana_id'] = $thana_id;
					$OrderData['market_id'] = $result['market_id'];
					$OrderData['outlet_id'] = $result['outlet_id'];
					$OrderData['entry_date'] = date('Y-m-d', strtotime($result['order_date']));
					$OrderData['order_date'] = date('Y-m-d', strtotime($result['order_date']));
					$OrderData['dist_order_no'] = $result['order_number'];
					//$OrderData['dist_order_no'] = 1030611112339;
					
					$OrderData['gross_value'] = $result['gross_value'];
					$OrderData['total_vat'] = $result['total_vat'];
					
					$OrderData['discount_value'] = $result['discount_value'];
					$OrderData['discount_percent'] = $result['discount_percent'];
					$OrderData['discount_type'] = $result['discount_type'];
					
					$OrderData['cash_recieved'] = 0;
					$OrderData['credit_amount'] = 0;
					$OrderData['is_active'] = 1;
					$OrderData['status'] = 1;
					$OrderData['order_time'] = $result['order_date_time']&&$result['order_date_time']!='null'?$result['order_date_time']:$result['order_date'];
					$OrderData['sales_person_id'] = $so_id;
					$OrderData['ae_id'] = $ae_id;
					$OrderData['tso_id'] = $tso_id;
					$OrderData['from_app'] = 1;
					$OrderData['action'] = 0;
					$OrderData['is_program'] = 0;
					$OrderData['order_reference_no'] = '';
					
					$OrderData['latitude'] = $result['latitude'];
					$OrderData['longitude'] = $result['longitude'];
					
					$OrderData['created_at'] = isset($result['updated_at']) && $result['updated_at'] ? $result['updated_at'] : $this->current_datetime();
					$OrderData['created_by'] = $so_id;
					$OrderData['updated_at'] = isset($result['updated_at']) && $result['updated_at'] ? $result['updated_at'] : $this->current_datetime();
					$OrderData['updated_by'] = $so_id;
					
					
					
					$this->DistOrder->create();
					
					
	
					if ($this->DistOrder->save($OrderData)) 
					{
						// ec calculation 
						$this->dist_ec_calculation($result['gross_value'], $result['outlet_id'], $sr_code, $office_id, $OrderData['order_date'], 1);
						
						// oc calculation 
						$this->dist_oc_calculation($sr_code, $office_id, $result['gross_value'], $result['outlet_id'], $OrderData['order_date'], $OrderData['order_time'], 1);
						
						$order_id = $old_order_id;
	
						if ($order_id) 
						{
							
							$all_product_id = array_map(function($element) {
								return $element['product_id'];
							}, $result['order_details']);
							
							//pr($result['order_details']);exit;
							
							foreach ($result['order_details'] as $val) 
							{
								if ($val == NULL) {
									continue;
								}
								
								$product_id = $order_details['DistOrderDetail']['product_id'] = $val['product_id'];
								
								$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
								$product_list = Set::extract($products, '{n}.Product');
	
								$punits_pre = $this->search_array($product_id, 'id', $product_list);
								//pr($punits_pre);//die();
								
								$order_details['DistOrderDetail']['dist_order_id'] = $order_id;
								$order_details['DistOrderDetail']['measurement_unit_id'] = $val['measurement_unit_id']>0?$val['measurement_unit_id']:$punits_pre['sales_measurement_unit_id'];
								$sales_price = $order_details['DistOrderDetail']['price'] = $val['price']?$val['price']:0;
								$sales_qty = $order_details['DistOrderDetail']['sales_qty'] = $val['quantity'];
								
								$order_details['DistOrderDetail']['vat'] = $val['vat']?$val['vat']:0;
	
								$product_price_slab_id = 0;
								if ($sales_price > 0) {
									$product_price_slab_id = $this->dist_get_product_price_id($val['product_id'], $sales_price, $all_product_id, $OrderData['order_date'], 1);
									//pr($product_price_slab_id);exit;
								}
								$order_details['DistOrderDetail']['product_price_id'] = $product_price_slab_id['product_price_id']?$product_price_slab_id['product_price_id']:0;
								//$order_details['DistOrderDetail']['product_combination_id'] = $product_price_slab_id['combination_id'];
								$order_details['DistOrderDetail']['price'] =$sales_price?($sales_price-($val['discount_amount']?$val['discount_amount']:0)):0;
								$order_details['DistOrderDetail']['actual_price'] = $sales_price;
								$order_details['DistOrderDetail']['bonus_qty']= 0;
								$order_details['DistOrderDetail']['bonus_product_id'] = 0;
	
								//Start for bonus
								$order_details['DistOrderDetail']['is_bonus'] = $val['is_bonus'];
								$order_details['DistOrderDetail']['bonus_id'] = 0;
								$order_details['DistOrderDetail']['bonus_scheme_id'] = 0;
								//End for bouns
								
								$order_details['DistOrderDetail']['discount_amount'] = $val['discount_amount'];
								$order_details['DistOrderDetail']['discount_type'] = $val['discount_type'];
								$order_details['DistOrderDetail']['policy_type'] = $val['policy_type'];
								$order_details['DistOrderDetail']['policy_id'] = $val['policy_id'];
	
								$total_product_data[] = $order_details;
							   
								//update inventory
								$stock_hit=1;
								if ($stock_hit) {
									if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) 
									{
										$base_quantity = ROUND($sales_qty);
									} 
									else 
									{
										$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
									}
	
									$update_type = 'add';
									
									$this->dist_booking_update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 3, $OrderData['order_date']);
								}
	

								// sales calculation (target qty and amount achievement)
								$tt_price = $sales_qty * $sales_price;
								if ($sales_price > 0){
									$this->dist_sales_calculation($product_id, $sr_code, $office_id, $sales_qty, $tt_price, $OrderData['order_date'], 1);
								}
							}
							
							
							//pr($total_product_data);
														
							$this->DistOrderDetail->saveAll($total_product_data);
							
							$res['status'] = 1;
							//$res['memo_no'] = $result['order_number'];
							$res['message'] = 'Orders has been updated successfuly.';
						}
					}
				}
				
			}
		} 
		else 
		{
            $res['status'] = 0;
            $res['message'] = 'No Data Provided.';
        }

        $this->set(array(
            'order' => $res,
            '_serialize' => array('order')
        ));
		
	}
	
	public function dist_order_delete($id = 0, $sr_code = 0, $office_id = 0) {

        $this->loadModel('Product');
        $this->loadModel('DistOrder');
        $this->loadModel('DistOrderDetail');
        
		$count = $this->DistOrder->find('count', array(
			'conditions' => array(
				'DistOrder.id' => $id
			)
		));

		$order_id_arr = $this->DistOrder->find('first', array(
			'conditions' => array(
				'DistOrder.id' => $id
			)
		));

		$this->loadModel('DistStore');
		$store_id_arr = $this->DistStore->find('first', array(
			'conditions' => array(
				'DistStore.dist_distributor_id' => $order_id_arr['DistOrder']['distributor_id']
			),
			'recursive' => -1
		));
		
		$store_id = $store_id_arr['DistStore']['id'];

		
		$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
		$product_list = Set::extract($products, '{n}.Product');
		
		// ec calculation 
		$this->dist_ec_calculation($order_id_arr['DistOrder']['gross_value'], $order_id_arr['DistOrder']['outlet_id'], $sr_code, $office_id, $order_id_arr['DistOrder']['order_date'], 2);
		
		// oc calculation 
		$this->dist_oc_calculation($sr_code, $office_id, $order_id_arr['DistOrder']['gross_value'], $order_id_arr['DistOrder']['outlet_id'], $order_id_arr['DistOrder']['order_date'], $order_id_arr['DistOrder']['order_time'], 2);

		for ($order_detail_count = 0; $order_detail_count < count($order_id_arr['DistOrderDetail']); $order_detail_count++) 
		{
			$product_id = $order_id_arr['DistOrderDetail'][$order_detail_count]['product_id'];
			$sales_qty = $order_id_arr['DistOrderDetail'][$order_detail_count]['sales_qty'];
			$sales_price = $order_id_arr['DistOrderDetail'][$order_detail_count]['price'];
			
			/*$order_territory_id = $order_id_arr['DistOrder']['territory_id'];
			$order_no = $order_id_arr['DistOrder']['dist_order_no'];
			$order_date = $order_id_arr['DistOrder']['order_date'];
			$outlet_id = $order_id_arr['DistOrder']['outlet_id'];
			$market_id = $order_id_arr['DistOrder']['market_id'];*/
			
			$punits_pre = $this->search_array($product_id, 'id', $product_list);

			if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) 
			{
				$base_quantity = ROUND($sales_qty);
			} 
			else 
			{
				$base_quantity =  $base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
			}

			$update_type = 'deduct';
			$this->dist_booking_update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 3, $order_id_arr['DistOrder']['order_date']);

			// sales calculation (target qty and amount achievement)
			$tt_price = $sales_qty * $sales_price;
			if ($sales_price > 0){
				$this->dist_sales_calculation($product_id, $sr_code, $office_id, $sales_qty, $tt_price,  $order_id_arr['DistOrder']['order_date'], 2);
			}
		}

		$order_id = $order_id_arr['DistOrder']['id'];
		$dist_order_no = $order_id_arr['DistOrder']['dist_order_no'];
		$this->DistOrderDetail->deleteAll(array('DistOrderDetail.dist_order_id' => $order_id));
		//$this->DistOrder->id = $order_id;
		$this->DistOrder->deleteAll(array('DistOrder.dist_order_no' => $dist_order_no));
		//$this->DistOrder->delete();
    }
	
	public function dist_order_cancel($id = 0) {

        $this->loadModel('Product');
        $this->loadModel('DistOrder');
        $this->loadModel('DistOrderDetail');
        $this->loadModel('SalesPerson');
		
		$json_data = $this->request->input('json_decode', true);	
        
        $json = $this->request->input();
        $all_inserted = true;
        $relation_array = array();
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }

        $path = APP . 'logs/';
        $myfile = fopen($path . "dist_order_cancel.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistSalesRepresentative.code','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			'recursive' => -1
		));
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		$sr_code = $so_info['DistSalesRepresentative']['code'];
		
		$path = APP . 'logs/DBwiseLog/'.$distributor_id.'/'.date('Y-m-d').'/';
		if(!file_exists($path))
		{
			 mkdir($path, 0777, true);
		}
        $myfile = fopen($path . "dist_order_cancel.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);

		$dist_order_no = $json_data['order_number'];
		
		$order_id_arr = $this->DistOrder->find('first', array(
			'conditions' => array(
				'DistOrder.dist_order_no' => $dist_order_no,
				'DistOrder.processing_status' => array(0,3),
				//'DistOrder.action' => 1,
				'DistOrder.status' => array(1,2,3),
				//'DistOrder.status  !=' => 3,
				'DistOrder.sr_id' => $sr_id
			)
		));
		
		//pr($order_id_arr);die;
		
		if($order_id_arr)
		{
			if($order_id_arr['DistOrder']['status']==3)
			{
				$res = array();
				$res['message'] = 'Order already cancelled.';
				$res['status'] = 0;
			}
			else
			{
				// ec calculation 
				$this->dist_ec_calculation($order_id_arr['DistOrder']['gross_value'], $order_id_arr['DistOrder']['outlet_id'], $sr_code, $office_id, $order_id_arr['DistOrder']['order_date'], 2);
				
				// oc calculation 
				$this->dist_oc_calculation($sr_code, $office_id, $order_id_arr['DistOrder']['gross_value'], $order_id_arr['DistOrder']['outlet_id'], $order_id_arr['DistOrder']['order_date'], $order_id_arr['DistOrder']['order_time'], 2);
				
				$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
				$product_list = Set::extract($products, '{n}.Product');
		
				for ($order_detail_count = 0; $order_detail_count < count($order_id_arr['DistOrderDetail']); $order_detail_count++) 
				{
					$product_id = $order_id_arr['DistOrderDetail'][$order_detail_count]['product_id'];
					$sales_qty = $order_id_arr['DistOrderDetail'][$order_detail_count]['sales_qty'];
					$sales_price = $order_id_arr['DistOrderDetail'][$order_detail_count]['price'];
					
					$punits_pre = $this->search_array($product_id, 'id', $product_list);
					if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) 
					{
						$base_quantity = ROUND($sales_qty);
					} 
					else 
					{
						$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
					}
		
					$update_type = 'deduct';
					$this->dist_booking_update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 3, $order_id_arr['DistOrder']['order_date']);
					
					// sales calculation (target qty and amount achievement)
					$tt_price = $sales_qty * $sales_price;
					if ($sales_price > 0){
						$this->dist_sales_calculation($product_id, $sr_code, $office_id, $sales_qty, $tt_price,  $order_id_arr['DistOrder']['order_date'], 2);
					}
		
				}
				
				$this->DistOrder->dist_order_no = $dist_order_no;
				if ($this->DistOrder->dist_order_no) {
					$this->DistOrder->updateAll(
						array(
						'DistOrder.action' => 0, 
						'DistOrder.status' => 3,
						'DistOrder.processing_status' => 0
						),   //fields to update
						array('DistOrder.dist_order_no' => $dist_order_no) //condition
					);
				}
			
				$res = array();
				$res['message'] = 'Cancel Successfully Completed.';
				$res['status'] = 1;
			}
		}
		else
		{
			$res = array();
			$res['message'] = 'Cancel Failed.';
			$res['status'] = 0;
		}
		
		$this->set(array(
            'schedule' => $res,
            '_serialize' => array('schedule')
        ));

    }
	
	public function dist_order_cancel_list($id = 0) {

        $this->loadModel('Product');
        $this->loadModel('DistOrder');
        $this->loadModel('DistOrderDetail');
        $this->loadModel('SalesPerson');
		
		$json_data = $this->request->input('json_decode', true);	
        
        $json = $this->request->input();
        $all_inserted = true;
        $relation_array = array();
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }

        $path = APP . 'logs/';
        $myfile = fopen($path . "dist_order_cancel_list.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistSalesRepresentative.code','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			'recursive' => -1
		));
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		$sr_code = $so_info['DistSalesRepresentative']['code'];
		
		$order_numbers = $json_data['order_numbers'];

		$path = APP . 'logs/DBwiseLog/'.$distributor_id.'/'.date('Y-m-d').'/';
		if(!file_exists($path))
		{
			 mkdir($path, 0777, true);
		}
        $myfile = fopen($path . "dist_order_cancel_list.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
		//pr($order_numbers);exit;
		
		foreach($order_numbers as $order_number)
		{
			$order_id_arr = $this->DistOrder->find('first', array(
				'conditions' => array(
					'DistOrder.dist_order_no' => $order_number,
					'DistOrder.processing_status' => array(0,3),
					'DistOrder.status' => array(1,2),
					'DistOrder.sr_id' => $sr_id
				)
			));
			
			//pr($order_id_arr);die;
			
			if($order_id_arr)
			{
				
				// ec calculation 
				$this->dist_ec_calculation($order_id_arr['DistOrder']['gross_value'], $order_id_arr['DistOrder']['outlet_id'], $sr_code, $office_id, $order_id_arr['DistOrder']['order_date'], 2);
				
				// oc calculation 
				$this->dist_oc_calculation($sr_code, $office_id, $order_id_arr['DistOrder']['gross_value'], $order_id_arr['DistOrder']['outlet_id'], $order_id_arr['DistOrder']['order_date'], $order_id_arr['DistOrder']['order_time'], 2);
				
				$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
				$product_list = Set::extract($products, '{n}.Product');
		
				for ($order_detail_count = 0; $order_detail_count < count($order_id_arr['DistOrderDetail']); $order_detail_count++) 
				{
					$product_id = $order_id_arr['DistOrderDetail'][$order_detail_count]['product_id'];
					$sales_qty = $order_id_arr['DistOrderDetail'][$order_detail_count]['sales_qty'];
					$sales_price = $order_id_arr['DistOrderDetail'][$order_detail_count]['price'];
					
					$punits_pre = $this->search_array($product_id, 'id', $product_list);
					if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) 
					{
						$base_quantity = ROUND($sales_qty);
					} 
					else
					{
						$base_quantity =  $base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
					}
		
					$update_type = 'deduct';
					$this->dist_booking_update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 3, $order_id_arr['DistOrder']['order_date']);
					
					// sales calculation (target qty and amount achievement)
					$tt_price = $sales_qty * $sales_price;
					if ($sales_price > 0){
						$this->dist_sales_calculation($product_id, $sr_code, $office_id, $sales_qty, $tt_price,  $order_id_arr['DistOrder']['order_date'], 2);
					}
		
				}
				
				$this->DistOrder->dist_order_no = $order_number;
				if ($this->DistOrder->dist_order_no) {
					$this->DistOrder->updateAll(
						array(
						'DistOrder.action' => 0, 
						'DistOrder.status' => 3,
						'DistOrder.processing_status' => 0
						),   //fields to update
						array('DistOrder.dist_order_no' => $order_number) //condition
					);
				}
			
				$res = array();
				$res['message'] = 'Cancel Successfully Completed.';
				$res['status'] = 1;
			}
			else
			{
				$res = array();
				$res['message'] = 'Cancel Failed.';
				$res['status'] = 0;
			}
		}
		
		$this->set(array(
            'schedule' => $res,
            '_serialize' => array('schedule')
        ));

    }
	
	
	public function dist_booking_update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct', $transaction_type_id = 0, $transaction_date = '') {

        $this->loadModel('DistCurrentInventory');

        $find_type = 'first';

        $inventory_info = $this->DistCurrentInventory->find($find_type, array(
            'conditions' => array(
                'DistCurrentInventory.store_id' => $store_id,
                'DistCurrentInventory.inventory_status_id' => 1,
                'DistCurrentInventory.product_id' => $product_id
            ),
            'order' => array('DistCurrentInventory.qty' => 'desc'),
            'recursive' => -1
        ));

        //pr($inventory_info);
		
		if($inventory_info)
		{
			if ($update_type == 'deduct') 
			{
				
				/*$update_array = array('DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'");
				
				if($inventory_info['DistCurrentInventory']['booking_qty'] < $quantity){
					$update_array['DistCurrentInventory.booking_qty']=0;
				}else{
					$update_array['DistCurrentInventory.booking_qty']='DistCurrentInventory.booking_qty - ' . $quantity;
				}
				
				if($inventory_info['DistCurrentInventory']['bonus_booking_qty'] < $booking_bonus_qty){
					$update_array['DistCurrentInventory.bonus_booking_qty']=0;
				}else{
					$update_array['DistCurrentInventory.bonus_booking_qty']='DistCurrentInventory.bonus_booking_qty - ' . $booking_bonus_qty;
				}
				
				$this->DistCurrentInventory->id = $inventory_info['DistCurrentInventory']['id'];
				
				$this->DistCurrentInventory->updateAll(
						$update_array, 
						array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
				);*/
				
				
				$update_array = array();
				$update_array['DistCurrentInventory']['id'] = $inventory_info['DistCurrentInventory']['id']; 
				if($inventory_info['DistCurrentInventory']['booking_qty'] < $quantity){
					$update_array['DistCurrentInventory']['booking_qty'] = 0;
				}else{
					$update_array['DistCurrentInventory']['booking_qty'] = $inventory_info['DistCurrentInventory']['booking_qty'] - $quantity;
				}
				
				$update_array['DistCurrentInventory']['updated_at'] = $this->current_datetime(); 

				//pr($update_array);
												
				
				$this->DistCurrentInventory->save($update_array);
					
				
				

				/*$this->DistCurrentInventory->updateAll(
						array(
							'DistCurrentInventory.booking_qty' => 'DistCurrentInventory.booking_qty - ' . $quantity,
							'DistCurrentInventory.bonus_booking_qty' => 'DistCurrentInventory.bonus_booking_qty - ' . $booking_bonus_qty,
							'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
						), 
						array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
				);*/
					
			} 
			else 
			{
				
	
					$this->DistCurrentInventory->updateAll(
							array(
							'DistCurrentInventory.booking_qty' => 'DistCurrentInventory.booking_qty + ' . $quantity,
							
							'DistCurrentInventory.store_id' => $store_id,
							), 
							 array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
					);
				
			}
		}
        return true;
    }
	
	
	
	public function dist_ec_calculation($gross_value, $outlet_id, $sr_code, $office_id, $order_date, $cal_type) {
        // from so_id , retrieve aso_id,terrority_id :  no need as we have territory_id
        // check gross_value >0

        if ($gross_value > 0) {
            $this->loadModel('DistOutlet');
            // from outlet_id, retrieve pharma or non-pharma
            $outlet_info = $this->DistOutlet->find('first', array(
                'conditions' => array(
                    'DistOutlet.id' => $outlet_id
                ),
                'recursive' => -1
            ));

            if (!empty($outlet_info)) {
                $is_pharma_type = $outlet_info['DistOutlet']['is_pharma_type'];
                // from order_date , split month and get month name and compare month table with order year
                $orderDate = strtotime($order_date);
                $month = date("n", $orderDate);
                $year = date("Y", $orderDate);
                $order_date = date('Y-m-d', $orderDate);
                $this->loadModel('Month');
                $this->loadModel('FiscalYear');

                // from outlet_id, retrieve pharma or non-pharma
                $fasical_month = $this->Month->find('first', array(
                    'conditions' => array(
                        'Month.month' => $month,
                    /* 'Month.year' => $year */
                    ),
                    'recursive' => -1
                ));
                $fasical_info = $this->FiscalYear->find('first', array(
                    'conditions' => array(
                        'FiscalYear.start_date <=' => $order_date,
                        'FiscalYear.end_date >=' => $order_date,
                    ),
                    'recursive' => -1
                ));

                if (!empty($fasical_info)) {
                    $this->loadModel('DistSaleTargetMonth');
                    if ($cal_type == 1) {
                        if ($is_pharma_type == 1) {
                            $update_fields_arr = array('DistSaleTargetMonth.effective_call_pharma_achievement' => "DistSaleTargetMonth.effective_call_pharma_achievement+1");
                        } else if ($is_pharma_type == 0) {
                            $update_fields_arr = array('DistSaleTargetMonth.effective_call_non_pharma_achievement' => "DistSaleTargetMonth.effective_call_non_pharma_achievement+1");
                        }
                    } else {
                        if ($is_pharma_type == 1) {
                            $update_fields_arr = array('DistSaleTargetMonth.effective_call_pharma_achievement' => "DistSaleTargetMonth.effective_call_pharma_achievement-1");
                        } else if ($is_pharma_type == 0) {
                            $update_fields_arr = array('DistSaleTargetMonth.effective_call_non_pharma_achievement' => "DistSaleTargetMonth.effective_call_non_pharma_achievement-1");
                        }
                    }

                    $conditions_arr = array('DistSaleTargetMonth.product_id' => 0, 'DistSaleTargetMonth.dist_sales_representative_code' => $sr_code, 'DistSaleTargetMonth.aso_id' => $office_id, 'DistSaleTargetMonth.target_type' => 5, 'DistSaleTargetMonth.fiscal_year_id' => $fasical_info['FiscalYear']['id'], 'DistSaleTargetMonth.month_id' => $fasical_month['Month']['id']);

                    $this->DistSaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
                }
            }
        }
    }
	
	// cal_type=1 means increment and 2 means deduction 
    // it will be called from order not from order_details 
    public function dist_oc_calculation($sr_code, $office_id, $gross_value, $outlet_id, $order_date, $order_time, $cal_type){

        // from so_id , retrieve aso_id,terrority_id :  no need as we have territory_id
        // check gross_value >0
        if ($gross_value > 0) {
            $this->loadModel('DistOrder');
            // this will be updated monthly , if done then increment else no action
            $month_first_date = date('Y-m-01', strtotime($order_date));
            $count = $this->DistOrder->find('count', array(
                'conditions' => array(
                    'DistOrder.outlet_id' => $outlet_id,
                    'DistOrder.order_date >= ' => $month_first_date,
                    'DistOrder.order_time < ' => $order_time
                ),
                'recursive'=>-1
            ));
            if ($count == 0) {

                $this->loadModel('DistOutlet');
                // from outlet_id, retrieve pharma or non-pharma
                $outlet_info = $this->DistOutlet->find('first', array(
                    'conditions' => array(
                        'DistOutlet.id' => $outlet_id
                    ),
                    'recursive' => -1
                ));

                if (!empty($outlet_info)) {
                    $is_pharma_type = $outlet_info['DistOutlet']['is_pharma_type'];
                    // from order_date , split month and get month name and compare month table with order year
                    $orderDate = strtotime($order_date);
                    $month = date("n", $orderDate);
                    $year = date("Y", $orderDate);
                    $order_date = date('Y-m-d', $orderDate);
                    $this->loadModel('Month');
                    $this->loadModel('FiscalYear');
                    // from outlet_id, retrieve pharma or non-pharma
                    $fasical_month = $this->Month->find('first', array(
                        'conditions' => array(
                            'Month.month' => $month,
                        /* 'Month.year' => $year */
                        ),
                        'recursive' => -1
                    ));
                    $fasical_info = $this->FiscalYear->find('first', array(
                        'conditions' => array(
                            'FiscalYear.start_date <=' => $order_date,
                            'FiscalYear.end_date >=' => $order_date,
                        ),
                        'recursive' => -1
                    ));

                    if (!empty($fasical_info)) 
					{
                        $this->loadModel('DistSaleTargetMonth');
                        if ($cal_type == 1) {
                            if ($is_pharma_type == 1) {
                                $update_fields_arr = array('DistSaleTargetMonth.outlet_coverage_pharma_achievement' => "DistSaleTargetMonth.outlet_coverage_pharma_achievement+1");
                            } else if ($is_pharma_type == 0) {
                                $update_fields_arr = array('DistSaleTargetMonth.outlet_coverage_non_pharma_achievement' => "DistSaleTargetMonth.outlet_coverage_non_pharma_achievement+1");
                            }
                        } else {
                            if ($is_pharma_type == 1) {
                                $update_fields_arr = array('DistSaleTargetMonth.outlet_coverage_pharma_achievement' => "DistSaleTargetMonth.outlet_coverage_pharma_achievement-1");
                            } else if ($is_pharma_type == 0) {
                                $update_fields_arr = array('DistSaleTargetMonth.outlet_coverage_non_pharma_achievement' => "DistSaleTargetMonth.outlet_coverage_non_pharma_achievement-1");
                            }
                        }
						
						$conditions_arr = array('DistSaleTargetMonth.product_id' => 0, 'DistSaleTargetMonth.dist_sales_representative_code' => $sr_code, 'DistSaleTargetMonth.aso_id' => $office_id, 'DistSaleTargetMonth.target_type' => 5, 'DistSaleTargetMonth.fiscal_year_id' => $fasical_info['FiscalYear']['id'], 'DistSaleTargetMonth.month_id' => $fasical_month['Month']['id']);
                        $this->DistSaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);

                    }
                }
            }
        }
    }
	
    // cal_type=1 means increment and 2 means deduction 
    // it will be called from  order_details 
    public function dist_sales_calculation($product_id, $sr_code, $office_id, $quantity, $gross_value, $order_date, $cal_type){
        // from so_id , retrieve aso_id,terrority_id : no need as we have territory_id
        // from order_date , split month and get month name and compare month table with order year
        $orderDate = strtotime($order_date);
        $month = date("n", $orderDate);
        $year = date("Y", $orderDate);
        $order_date = date('Y-m-d', $orderDate);
        $this->loadModel('Month');
        $this->loadModel('FiscalYear');
        // from outlet_id, retrieve pharma or non-pharma
        $fasical_month = $this->Month->find('first', array(
            'conditions' => array(
                'Month.month' => $month,
            /* 'Month.year' => $year */
            ),
            'recursive' => -1
        ));
        $fasical_info = $this->FiscalYear->find('first', array(
            'conditions' => array(
                'FiscalYear.start_date <=' => $order_date,
                'FiscalYear.end_date >=' => $order_date,
            ),
            'recursive' => -1
        ));

        if (!empty($fasical_info)) {
            $this->loadModel('DistSaleTargetMonth');
            if ($cal_type == 1) {
                $update_fields_arr = array('DistSaleTargetMonth.target_quantity_achievement' => "DistSaleTargetMonth.target_quantity_achievement+$quantity", 'DistSaleTargetMonth.target_amount_achievement' => "DistSaleTargetMonth.target_amount_achievement+$gross_value");
            } else {
                $update_fields_arr = array('DistSaleTargetMonth.target_quantity_achievement' => "DistSaleTargetMonth.target_quantity_achievement-$quantity", 'DistSaleTargetMonth.target_amount_achievement' => "DistSaleTargetMonth.target_amount_achievement-$gross_value");
            }

            $conditions_arr = array('DistSaleTargetMonth.product_id' => $product_id, 'DistSaleTargetMonth.dist_sales_representative_code' => $sr_code, 'DistSaleTargetMonth.aso_id' => $office_id, 'DistSaleTargetMonth.target_type' => 2, 'DistSaleTargetMonth.fiscal_year_id' => $fasical_info['FiscalYear']['id'], 'DistSaleTargetMonth.month_id' => $fasical_month['Month']['id']);
            $this->DistSaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
        }
    }

    

    // it will be called from order_details 
    public function dist_stamp_calculation($memo_no, $terrority_id, $product_id, $outlet_id, $quantity, $memo_date, $cal_type, $gross_amount, $market_id) {
        // from outlet_id, get bonus_type_id and check if null then no action else action

        $this->loadModel('DistOutlet');
        // from outlet_id, retrieve pharma or non-pharma
        $outlet_info = $this->DistOutlet->find('first', array(
            'conditions' => array(
                'DistOutlet.id' => $outlet_id
            ),
            'recursive' => -1
        ));

        if (!empty($outlet_info) && $gross_amount > 0) {
            $bonus_type_id = $outlet_info['DistOutlet']['bonus_type_id'];
            if (($bonus_type_id === NULL) || (empty($bonus_type_id))) {
                // no action 
            } else {
                // from memo_date , split month and get month name and compare month table with memo year (get fascal year id)
                $memoDate = strtotime($memo_date);
                $month = date("n", $memoDate);
                $year = date("Y", $memoDate);
                /* $this->loadModel('Month');
                  $fasical_info = $this->Month->find('first', array(
                  'conditions' => array(
                  'Month.month' => $month,
                  'Month.year' => $year
                  ),
                  'recursive' => -1
                  )); */
                $memo_date = date('Y-m-d', $memoDate);
                $this->LoadModel('FiscalYear');
                $fasical_info = $this->FiscalYear->find('first', array(
                    'conditions' => array(
                        'FiscalYear.start_date <=' => $memo_date,
                        'FiscalYear.end_date >=' => $memo_date,
                    ),
                    'recursive' => -1
                ));
                if (!empty($fasical_info)) {
                    // check bonus card table , where is_active,and others  and get min qty per memo
                    $this->loadModel('BonusCard');
                    $bonus_card_info = $this->BonusCard->find('first', array(
                        'conditions' => array(
                            'BonusCard.fiscal_year_id' => $fasical_info['FiscalYear']['id'],
                            'BonusCard.is_active' => 1,
                            'BonusCard.product_id' => $product_id,
                            'BonusCard.bonus_card_type_id' => $bonus_type_id
                        ),
                        'recursive' => -1
                    ));
                    /* echo $this->BonusCard->getLastquery();
                      pr($bonus_card_info); */

                    // if exist min qty per memo , then stamp_no=mod(quantity/min qty per memo)
                    if (!empty($bonus_card_info)) {
                        $min_qty_per_memo = $bonus_card_info['BonusCard']['min_qty_per_memo'];
                        if ($min_qty_per_memo && $min_qty_per_memo <= $quantity) {
                            $stamp_no = floor($quantity / $min_qty_per_memo);
                            /* if ($cal_type != 1) {
                              $stamp_no = $stamp_no * (-1);
                              $quantity = $quantity * (-1);
                              } */


                            $this->loadModel('StoreBonusCard');
                            $log_data = array();
                            $log_data['StoreBonusCard']['created_at'] = $this->current_datetime();
                            $log_data['StoreBonusCard']['territory_id'] = $terrority_id;
                            $log_data['StoreBonusCard']['outlet_id'] = $outlet_id;
                            $log_data['StoreBonusCard']['market_id'] = $market_id;
                            $log_data['StoreBonusCard']['product_id'] = $product_id;
                            $log_data['StoreBonusCard']['quantity'] = $quantity;
                            $log_data['StoreBonusCard']['no_of_stamp'] = $stamp_no;
                            $log_data['StoreBonusCard']['bonus_card_id'] = $bonus_card_info['BonusCard']['id'];
                            $log_data['StoreBonusCard']['bonus_card_type_id'] = $bonus_type_id;
                            $log_data['StoreBonusCard']['fiscal_year_id'] = $bonus_card_info['BonusCard']['fiscal_year_id'];
                            $log_data['StoreBonusCard']['memo_no'] = $memo_no;

                            $this->StoreBonusCard->create();
                            $this->StoreBonusCard->save($log_data);
                        }
                    }
                }
            }
        }
    }
	
	
	
	
	
	public function dist_get_product_price_id($product_id, $product_prices, $all_product_id, $order_date, $distributor) {
        // echo $product_id.'--'.$product_prices.'<br>';
        $this->LoadModel('ProductCombination');
        $this->LoadModel('Combination');
        $data = array();
        $product_price = $this->ProductCombination->find('first', array(
            'conditions' => array(
                'ProductCombination.product_id' => $product_id,
                'ProductCombination.price' => $product_prices,
                'ProductCombination.effective_date <=' => $order_date,
            ),
            'order' => array('ProductCombination.id' => 'DESC'),
            'recursive' => -1
        ));

        // pr($product_price);exit;
        // echo $this->ProductCombination->getLastquery().'<br>';
        if ($product_price) {
            $is_combine = 0;
            if ($product_price['ProductCombination']['combination_id'] != 0) {
                $combination = $this->Combination->find('first', array(
                    'conditions' => array('Combination.id' => $product_price['ProductCombination']['combination_id']),
                    'recursive' => -1
                ));
                $combination_product = explode(',', $combination['Combination']['all_products_in_combination']);
                foreach ($combination_product as $combination_prod) {
                    if ($product_id != $combination_prod && in_array($combination_prod, $all_product_id)) {
                        $data['combination_id'] = $product_price['ProductCombination']['combination_id'];
                        $data['product_price_id'] = $product_price['ProductCombination']['id'];
                        $is_combine = 1;
                        break;
                    }
                }
            }
            if ($is_combine == 0) {
                $product_price = $this->ProductCombination->find('first', array(
                    'conditions' => array(
                        'ProductCombination.product_id' => $product_id,
                        'ProductCombination.price' => $product_prices,
                        'ProductCombination.effective_date <=' => $order_date,
                        'ProductCombination.parent_slab_id' => 0
                    ),
                    'order' => array('ProductCombination.id DESC'),
                    'recursive' => -1
                ));
                $data['combination_id'] = '';
                $data['product_price_id'] = $product_price['ProductCombination']['id'];
            }
            //pr($data);die();
            return $data;
        } else {
            $data['combination_id'] = '';
            $data['product_price_id'] = '';
            //pr($data);die();
            return $data;
        }
    }
	
	
	
	
	public function dist_last_orders(){
		$this->loadModel('DistOrder');
		$this->loadModel('DistOrderDetail');
        $this->loadModel('SalesPerson');
		$this->loadModel('Product');
		
		$json_data = $this->request->input('json_decode', true);
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_last_orders.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];

		
		$res_status = 1;
        //$seven_days_pre = date('Y-m-d 00:00:00', strtotime('-3 days'));
		
		$outlet_id = isset($json_data['outlet_id'])?$json_data['outlet_id']:0;
		$outlet_category_id = isset($json_data['outlet_category_id'])?$json_data['outlet_category_id']:0;
		$start_date = isset($json_data['start_date'])?$json_data['start_date']:0;
		$end_date = isset($json_data['end_date'])?$json_data['end_date']:0;

		$conditions = array();

        $conditions = array(
			'DistOrder.sr_id' => $sr_id, 
			//'DistOrder.action' => 1,
			'DistOrder.status' => array(1,2),
			//'DistOrder.status !=' => 3			
		);
		
        if($outlet_id)$conditions['DistOrder.outlet_id'] = $outlet_id;
		if($outlet_category_id)$conditions['Outlet.category_id'] = $outlet_category_id;
		if($start_date)$conditions['DistOrder.order_date >='] = $start_date;
		if($end_date)$conditions['DistOrder.order_date <='] = $end_date;
 		
		//pr($conditions);
		
        $order_list = $this->DistOrder->find('all', array(
            //'fields' => array('DistOrder.*'),
            'conditions' => $conditions,
            'recursive' => 1
        ));

        $order_array = array();
		$mdata = array();
		//pr($order_list);exit;
        foreach ($order_list as $m) 
		{
            $mdata['id'] = $m['DistOrder']['id'];
            $mdata['order_number'] = $m['DistOrder']['dist_order_no'];
            $mdata['order_date'] = $m['DistOrder']['order_date'];
			$mdata['order_date_time'] = $m['DistOrder']['order_time'];
            $mdata['sales_person_id'] = $m['DistOrder']['sr_id'];
            $mdata['outlet_id'] = $m['DistOrder']['outlet_id'];
            $mdata['market_id'] = $m['DistOrder']['market_id'];
            $mdata['territory_id'] = $m['DistOrder']['territory_id'];
            $mdata['gross_value'] = $m['DistOrder']['gross_value'];
			$mdata['total_vat'] = $m['DistOrder']['total_vat'];
			
			$mdata['discount_value'] = $m['DistOrder']['discount_value'];
			$mdata['discount_percent'] = $m['DistOrder']['discount_percent'];
			$mdata['discount_type'] = $m['DistOrder']['discount_type'];
			
            $mdata['adjustment_amount'] = $m['DistOrder']['adjustment_amount'];
            $mdata['adjustment_note'] = $m['DistOrder']['adjustment_note'];
            $mdata['cash_recieved'] = $m['DistOrder']['cash_recieved'];
            $mdata['credit_amount'] = $m['DistOrder']['credit_amount'];
            $mdata['latitude'] = $m['DistOrder']['latitude'];
            $mdata['longitude'] = $m['DistOrder']['longitude'];
            $mdata['from_app'] = $m['DistOrder']['from_app'];
			$mdata['status'] = $m['DistOrder']['processing_status'];
			$mdata['editable'] = $m['DistOrder']['order_editable'];
			$mdata['total_discount'] = $m['DistOrder']['total_discount'];
			
            if($m['DistOrder']['from_app']== 0 && $m['DistOrder']['action']==1)
            {
                $mdata['updated_at'] = date('Y-m-d H:i:s',strtotime($m['DistOrder']['order_date']));
            }
            else
            {
                $mdata['updated_at'] = $m['DistOrder']['order_time'];
            }
            $mdata['action'] = ($res_status == 1 ? 1 : $m['DistOrder']['action']);
            $mdata['order_details'] = $m['DistOrderDetail'];
            $mm = 0;

            foreach ($m['DistOrderDetail'] as $each_order_details) {

                $product_id = $each_order_details['product_id'];
                $product_info = $this->Product->find('first', array(
                    'fields' => array('product_type_id'),
                    'conditions' => array('id' => $product_id),
                    'recursive' => -1
                ));

                $type = "";
                if ($product_info['Product']['product_type_id'] == 1 && $each_order_details['price'] > 0) {
                    $type = 0;
                } else if ($product_info['Product']['product_type_id'] == 3) {
                    $type = 1;
                } else if ($product_info['Product']['product_type_id'] == 1 && $each_order_details['price'] < 1) {
                    $type = 2;
                }

                $mdata['order_details'][$mm]['product_type'] = $type;
				$mdata['order_details'][$mm]['quantity'] = $each_order_details['sales_qty'];
				$mdata['order_details'][$mm]['price'] = $each_order_details['actual_price'];
				$mdata['order_details'][$mm]['order_number'] = $m['DistOrder']['dist_order_no'];
                $mm++;
            }


            $order_array[] = $mdata;

        }
		
		//pr($order_array);
		
		$this->set(array(
            'orders' => $order_array,
            '_serialize' => array('orders')
        ));
	}	
	
	public function dist_order_details(){
		$this->loadModel('DistOrder');
		$this->loadModel('DistOrderDetail');
        $this->loadModel('SalesPerson');
		$this->loadModel('Product');
		
		$json_data = $this->request->input('json_decode', true);
        /*---------------------------- Mac check --------------------------------*/
				
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];

		
		$res_status = 1;
        //$seven_days_pre = date('Y-m-d 00:00:00', strtotime('-3 days'));
		
		$order_no = $json_data['order_number'];

		$conditions = array();

        $conditions = array(
			'DistOrder.sr_id' => $sr_id, 
			//'DistOrder.action' => 1, 
			'DistOrder.status >' => 0,
			'DistOrder.status  !=' => 3
		);
        if($order_no)$conditions['DistOrder.order_no'] = $order_no;
 		
        $m = $this->DistOrder->find('first', array(
            'fields' => array('DistOrder.*'),
            'conditions' => $conditions,
            'recursive' => 1
        ));

        $order_array = array();
		$mdata = array();
		//pr($order_list);
        //foreach ($order_list as $m) {
            $mdata['id'] = $m['DistOrder']['id'];
            $mdata['order_number'] = $m['DistOrder']['dist_order_no'];
            $mdata['order_date'] = $m['DistOrder']['order_date'];
            $mdata['sales_person_id'] = $m['DistOrder']['sr_id'];
            $mdata['outlet_id'] = $m['DistOrder']['outlet_id'];
            $mdata['market_id'] = $m['DistOrder']['market_id'];
            $mdata['territory_id'] = $m['DistOrder']['territory_id'];
            $mdata['gross_value'] = $m['DistOrder']['gross_value'];
			$mdata['total_vat'] = $m['DistOrder']['total_vat'];
			
			$mdata['discount_value'] = $m['DistOrder']['discount_value'];
			$mdata['discount_percent'] = $m['DistOrder']['discount_percent'];
			$mdata['discount_type'] = $m['DistOrder']['discount_type'];
			
            $mdata['adjustment_amount'] = $m['DistOrder']['adjustment_amount'];
            $mdata['adjustment_note'] = $m['DistOrder']['adjustment_note'];
            $mdata['cash_recieved'] = $m['DistOrder']['cash_recieved'];
            $mdata['credit_amount'] = $m['DistOrder']['credit_amount'];
            $mdata['latitude'] = $m['DistOrder']['latitude'];
            $mdata['longitude'] = $m['DistOrder']['longitude'];
            $mdata['from_app'] = $m['DistOrder']['from_app'];
			$mdata['total_discount'] = $m['DistOrder']['total_discount'];
            if($m['DistOrder']['from_app']== 0 && $m['DistOrder']['action']==1)
            {
                $mdata['updated_at'] = date('Y-m-d H:i:s',strtotime($m['DistOrder']['order_date']));
            }
            else
            {
                $mdata['updated_at'] = $m['DistOrder']['order_time'];
            }
            $mdata['action'] = ($res_status == 1 ? 1 : $m['DistOrder']['action']);
            $mdata['order_details'] = $m['DistOrderDetail'];
            $mm = 0;

            foreach ($m['DistOrderDetail'] as $each_order_details) {

                $product_id = $each_order_details['product_id'];
                $product_info = $this->Product->find('first', array(
                    'fields' => array('product_type_id'),
                    'conditions' => array('id' => $product_id),
                    'recursive' => -1
                ));

                $type = "";
                if ($product_info['Product']['product_type_id'] == 1 && $each_order_details['price'] > 0) {
                    $type = 0;
                } else if ($product_info['Product']['product_type_id'] == 3) {
                    $type = 1;
                } else if ($product_info['Product']['product_type_id'] == 1 && $each_order_details['price'] < 1) {
                    $type = 2;
                }

                $mdata['order_details'][$mm]['product_type'] = $type;
				$mdata['order_details'][$mm]['qty'] = $each_order_details['sales_qty'];
				$mdata['order_details'][$mm]['price'] = $each_order_details['actual_price'];
                $mm++;
            }


            $order_array[] = $mdata;

        //}
		
		//pr($order_array);
		
		$this->set(array(
            'orders' => $order_array,
            '_serialize' => array('orders')
        ));
	}
		
	
	/* ------------------- End Order Create ------------------ */
	
	
	
	/* ------------------- Dist Memo Create ----------------------- */
	public function dist_create_memo() {
		$this->loadModel('DistOrder');
		$this->loadModel('DistOrderDetail');
		$this->loadModel('DistMemo');
		$this->loadModel('DistMemoDetail');
		
		$this->loadModel('SalesPerson');
		$this->loadModel('DistStore');
		$this->loadModel('Product');
		$this->loadModel('DistMarket');
		
		$this->loadModel('DistOrderDeliveryScheduleOrder');
		$this->loadModel('DistOrderDeliveryScheduleOrderDetail');
		

        $json_data = $this->request->input('json_decode', true);
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_create_memo.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
        $all_inserted = true;
        $relation_array = array();
		
		
        
		
		$all_inserted = true;
		
		$so_id = $json_data['sales_person_id'];
		
		
		//pr($json_data['memo_list']);exit;
		
		
		if (!empty($json_data['memo_list'])) 
		{
			//user info
			$so_info = $this->SalesPerson->find('first', array(
				'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistSalesRepresentative.code','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
				'conditions' => array('SalesPerson.id' => $so_id),
				'joins' => array(
					array(
						'alias' => 'DistSalesRepresentative',
						'table' => 'dist_sales_representatives',
						'type' => 'INNER',
						'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
					),
					array(
						'alias' => 'DistDistributor',
						'table' => 'dist_distributors',
						'type' => 'INNER',
						'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
					),
					array(
						'alias' => 'DistStore',
						'table' => 'dist_stores',
						'type' => 'INNER',
						'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
					),
					array(
						'alias' => 'Office',
						'table' => 'offices',
						'type' => 'INNER',
						'conditions' => 'SalesPerson.office_id = Office.id'
					)
				),
				
				'recursive' => -1
			));
			
			/*pr($so_info);
			exit;*/
			
			$office_id = $so_info['Office']['id'];
			$distributor_id = $so_info['DistDistributor']['id'];
			$store_id = $so_info['DistStore']['id'];
			$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
			$sr_code = $so_info['DistSalesRepresentative']['code'];
			$sale_type_id = 10;
			
			$path = APP . 'logs/DBwiseLog/'.$distributor_id.'/'.date('Y-m-d').'/';
			if(!file_exists($path))
			{
				 mkdir($path, 0777, true);
			}
	        $myfile = fopen($path . "dist_create_memo.txt", "a") or die("Unable to open file!");
	        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
	        fclose($myfile);
			
			$this->loadModel('DistCurrentInventory');
			$inventory_results = $this->DistCurrentInventory->find('all', array(
				'conditions' => array(
					'DistCurrentInventory.store_id' => $store_id,
					'DistCurrentInventory.inventory_status_id' => 1
				),
				'fields' => array('product_id', 'sum(qty) as qty','product_id'),
				'group'=>array('product_id'),
				//'order' => array('DistCurrentInventory.product_id' => 'asc'),
				'recursive' => -1
			));
			
			$inventory_qty_info = array();
			$inventory_bonus_qty_info = array();
			foreach($inventory_results as $inventory_result)
			{
				$inventory_qty_info[$inventory_result['DistCurrentInventory']['product_id']]=$inventory_result[0]['qty'];
			}
			//pr($inventory_qty_info);exit;
			
			//pr($json_data['memo_list']);exit;
			
			foreach($json_data['memo_list'] as $result)
			{	
				$count = $this->DistMemo->find('count', array(
							'conditions' => array(
								'DistMemo.dist_order_no' => $result['order_number']
							)
						));
						
				$dist_order_no = $result['order_number'];
				$order_detials = $this->DistOrderDetail->find('all', array(
					'conditions' => array(
						'DistOrder.dist_order_no' => $result['order_number'],
						//'DistOrderDeliveryScheduleOrderDetail.dist_order_no' => $result['order_number']
					),
					'joins' => array(
						array(
							'alias' => 'DistOrder',
							'table' => 'dist_orders',
							'type' => 'INNER',
							'conditions' => 'DistOrderDetail.dist_order_id = DistOrder.id'
						),
						
					),
					'fields' => array('DistOrderDetail.dist_order_id as dist_order_id','DistOrderDetail.product_id as product_id','DistOrderDetail.measurement_unit_id as measurement_unit_id','DistOrderDetail.sales_qty as sales_qty','DistOrderDetail.price as price','DistOrderDetail.is_bonus as is_bonus','DistOrderDetail.product_price_id as product_price_id'),
					'recursive' => -1
				));
				//pr($order_detials);exit;
				$dist_order_id=0;
				$order_products = array();
				$order_products2 = array();
				foreach($order_detials as $order_detial)
				{					
					//echo $order_detial[0]['product_id'].'--'.$order_detial[0]['invoice_qty'];
					//echo '<br>';
					$dist_order_id=$order_detial[0]['dist_order_id'];
					$b_track = $order_detial[0]['price']>0?0:1;
					$s_order_detials = $this->DistOrderDeliveryScheduleOrderDetail->find('first', array(
							'conditions' => array(
								'DistOrderDeliveryScheduleOrderDetail.dist_order_no' => $result['order_number'],
								'DistOrderDeliveryScheduleOrderDetail.product_id' => $order_detial[0]['product_id'],
								'DistOrderDeliveryScheduleOrderDetail.is_bonus' => $b_track,
								'DistOrderDeliveryScheduleOrderDetail.status' => 1
							),							
						
						'fields' => array('invoice_qty as invoice_qty'),
						'recursive' => -1
					));
					//pr($s_order_detials);
					if(!isset($s_order_detials[0]['invoice_qty']))
					{
						
						$res['status'] = 0;
			            $res['message'] = 'Invoice not perfectly created.Please Cancel Invoice First Then Invoice Again';
						$this->set(array(
						            'memo' => $res,
						            '_serialize' => array('memo')
						        ));
						return 0;
					}
					
					$order_products[$order_detial[0]['product_id']] = array(
						'product_id'			=>	$order_detial[0]['product_id'],
						'measurement_unit_id' 	=>	$order_detial[0]['measurement_unit_id'],
						'sales_qty' 			=>	$order_detial[0]['sales_qty'],
						'price' 				=>	$order_detial[0]['price'],
						'is_bonus' 				=>	$order_detial[0]['is_bonus'],
						//'product_price_id' 		=>	$order_detial[0']['product_price_id'],
						'invoice_qty' 			=>	$s_order_detials[0]['invoice_qty'],
					);

					
					$order_products2[$order_detial[0]['product_id']][$b_track] = array(
						'product_id'			=>	$order_detial[0]['product_id'],
						'measurement_unit_id' 	=>	$order_detial[0]['measurement_unit_id'],
						'sales_qty' 			=>	$order_detial[0]['sales_qty'],
						'price' 				=>	$order_detial[0]['price'],
						'is_bonus' 				=>	$order_detial[0]['is_bonus'],
						'invoice_qty' 			=>	$s_order_detials[0]['invoice_qty'],
						//'product_price_id' 		=>	$s_order_detials[0]['product_price_id']
						
					);
					//pr($order_products2);
					//if($order_detial[0]['product_id']==28)break;
				}
				//echo $store_id;
				//pr($order_products2);
				//exit;
				
				$memo_products = array();
				$memo_products2 = array();
				foreach($result['memo_details'] as $val)
				{
					$val['price'] = $val['price']?$val['price']:0;
					$b_track = $val['price']>0?0:1;
					
					//echo $val['product_id'].'---'. $order_products2[$val['product_id']][$b_track]['invoice_qty'];
					//echo '<br>';
					
					$memo_products[$val['product_id']][$val['price']][$val['is_bonus']] = array(
						'product_id'			=>	$val['product_id'],
						'measurement_unit_id' 	=>	isset($val['measurement_unit_id'])?$val['measurement_unit_id']:0,
						'sales_qty' 			=>	$val['quantity'],
						'price' 				=>	$val['price'],
						'is_bonus' 				=>	$val['is_bonus'],
						//'product_price_id' 		=>	0,
						'order_sales_qty' 		=>	isset($order_products2[$val['product_id']][$b_track]['sales_qty'])?$order_products2[$val['product_id']][$b_track]['sales_qty']:0,
						'invoice_qty' 			=>	isset($order_products2[$val['product_id']][$b_track]['invoice_qty'])?$order_products2[$val['product_id']][$b_track]['invoice_qty']:0,
					);
					
					$memo_products2[$val['product_id']][$b_track] = array(
						'product_id'			=>	$val['product_id'],
						'measurement_unit_id' 	=>	isset($val['measurement_unit_id'])?$val['measurement_unit_id']:0,
						'sales_qty' 			=>	$val['quantity'],
						'price' 				=>	$val['price'],
						'is_bonus' 				=>	$val['is_bonus'],
						//'product_price_id' 		=>	0,
						//'order_sales_qty' 		=>	isset($order_products[$val['product_id']]['sales_qty'])?$order_products[$val['product_id']]['sales_qty']:0,
						//'invoice_qty' 			=>	isset($order_products[$val['product_id']]['invoice_qty'])?$order_products[$val['product_id']]['invoice_qty']:0,
						'order_sales_qty' 		=>	isset($order_products2[$val['product_id']][$b_track]['sales_qty'])?$order_products2[$val['product_id']][$b_track]['sales_qty']:0,
						'invoice_qty' 			=>	isset($order_products2[$val['product_id']][$b_track]['invoice_qty'])?$order_products2[$val['product_id']][$b_track]['invoice_qty']:0,
					);					
				}
																
				
				$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
				$product_list = Set::extract($products, '{n}.Product');
				//pr($product_list);
				
				//pr($order_products);
				//pr($order_products2);
				//pr($memo_products2);
				//exit;
				
				
				
				
				//inventory check
				$inventory_check = 1;	
				foreach($memo_products as $memo_product1)
				{
					foreach($memo_product1 as $p_price => $memo_product2){
						foreach($memo_product2 as $is_bonus_c => $memo_product){
							//echo $p_price;
							//pr($memo_product);
							if($p_price>0){
								$product_id = $memo_product['product_id'];
								$is_bonus = $memo_product['is_bonus'];
							}else{
								$product_id = $memo_product['product_id'];
								$is_bonus = $memo_product['is_bonus'];
							}
							
							//echo $is_bonus_c.'<br>';
							
							/*$punits_pre = $this->search_array($product_id, 'id', $product_list);
							if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
								$base_qty = $memo_product['sales_qty'];
							} else {
								$base_qty = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $memo_product['sales_qty']);
							}*/
							
							
							$punits_pre = $this->search_array($product_id, 'id', $product_list);
							if($memo_product['measurement_unit_id']>0)
							{
								if ($memo_product['measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
									$base_qty = round($memo_product['sales_qty']);
								} else {
									$base_qty = $this->unit_convert($product_id, $memo_product['measurement_unit_id'], $memo_product['sales_qty']);
								}
							}
							else
							{
								if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
									$base_qty = round($memo_product['sales_qty']);
								} else {
									$base_qty = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $memo_product['sales_qty']);
								}
							}
							
							
							if($memo_product['invoice_qty'] < $memo_product['sales_qty'])
							{
								$memo_sales_qty = $memo_product['sales_qty'] - $memo_product['invoice_qty'];
								//$memo_base_qty = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $memo_sales_qty);
								
								if($memo_product['measurement_unit_id']>0){
									$memo_base_qty = $this->unit_convert($product_id, $memo_product['measurement_unit_id'], $memo_sales_qty);
								}else{
									$memo_base_qty = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $memo_sales_qty);	
								}
								
								
								$product_stock_qty = @$inventory_qty_info[$product_id];	
								
								if($product_stock_qty < $memo_base_qty)
								{
									$inventory_check = 0;
								}
							}
						}
					}
					
				}
				//end inventory check	
				
				//pr($memo_products);
				//pr($result['memo_details']);exit;
				
				if($inventory_check)
				{

					//inventory update for which are not available on memo 				
					if($count==0)
					{
						foreach($order_products2 as $o_product_id => $or_data)
						{						
							foreach($or_data as $b_track => $o_val)
							{
								if(!isset($memo_products2[$o_product_id][$b_track]))
								{
									//pr($o_val);
									$punits_pre = $this->search_array($o_product_id, 'id', $product_list);
									
									//$base_quantity = $this->unit_convert($o_product_id, $punits_pre['sales_measurement_unit_id'], $o_val['sales_qty']);
									//$invoice_qty = $this->unit_convert($o_product_id, $punits_pre['sales_measurement_unit_id'], $o_val['invoice_qty']);
									
									if($o_val['measurement_unit_id']>0)
									{
										if ($o_val['measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
											$base_quantity = ROUND($o_val['sales_qty']);
											$invoice_qty = ROUND($o_val['invoice_qty']);
										} else {
											$base_quantity = $this->unit_convert($o_product_id, $o_val['measurement_unit_id'], $o_val['sales_qty']);
											$invoice_qty = $this->unit_convert($o_product_id, $o_val['measurement_unit_id'], $o_val['invoice_qty']);
										}
									}
									else
									{
										if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
											$base_quantity = round($o_val['sales_qty']);
											$invoice_qty = round($o_val['invoice_qty']);
										} else {
											$base_quantity = $this->unit_convert($o_product_id, $punits_pre['sales_measurement_unit_id'], $o_val['sales_qty']);
											$invoice_qty = $this->unit_convert($o_product_id, $punits_pre['sales_measurement_unit_id'], $o_val['invoice_qty']);
										}
									}
									
									
									
									$o_is_bonus = $o_val['price']<1?1:0;
									
									//echo $o_product_id;
									//echo '<br>';
							
									$this->dist_memo_update_current_inventory($base_quantity, $o_product_id, $store_id, 'add', 4, $result['memo_date'] , $invoice_qty);
								}
							}
						}
					}
					//exit;			
					//get territory_id and thana_id
					$market_info = $this->DistMarket->find('first', array(
						'conditions' => array('DistMarket.id' => $result['market_id']),
						'recursive' => -1
					));
					
					if($market_info)
					{
						$territory_id = $market_info['DistMarket']['territory_id'];
						$thana_id = $market_info['DistMarket']['thana_id'];
						
						$memo_date = date('Y-m-d', strtotime($result['memo_date']));
						//end get territory_id and thana_id
						
						//get ae and tso id
						$this->loadModel('DistTsoMappingHistory');            
						if($memo_date && $distributor_id)
						{					
							$qry="select distinct dist_tso_id from dist_tso_mapping_histories
								  where office_id=$office_id and is_change=1 and dist_distributor_id=$distributor_id and 
									'".$memo_date."' between effective_date and 
									case 
									when end_date is not null then 
									 end_date
									else 
									getdate()
									end";
			
							$dist_data=$this->DistTsoMappingHistory->query($qry);
							//pr($dist_data);
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
								'".$memo_date."' between effective_date and 
								case 
								when effective_end_date is not null then 
								 effective_end_date
								else 
								getdate()
								end";
			
							$ae_data=$this->DistTsoMappingHistory->query($qry2);
							//pr($ae_data);die();
							$ae_ids=array();
						   
							foreach ($ae_data as $k => $v) {
								$ae_ids[]=$v[0]['dist_area_executive_id'];
							}
							$ae_id="";
							
							if($ae_ids)
							{
								$ae_id=$ae_ids[0];
							}
						}
						//end get ae and tso id
						
						/*$count = $this->DistMemo->find('count', array(
							'conditions' => array(
								'DistMemo.dist_memo_no' => $result['memo_number']
							)
						));*/
						
						
						$total_product_data = array();
						
						if($count==0)
						{
							$MemoData = array();
							$MemoData['office_id'] = $office_id;
							$MemoData['distributor_id'] = $distributor_id;
							$MemoData['sr_id'] = $sr_id;
							$MemoData['dist_route_id'] = $result['route_id'];
							$MemoData['sale_type_id'] = $sale_type_id;
							$MemoData['territory_id'] = $territory_id;
							$MemoData['thana_id'] = $thana_id;
							$MemoData['market_id'] = $result['market_id'];
							$MemoData['outlet_id'] = $result['outlet_id'];
							$MemoData['entry_date'] = date('Y-m-d', strtotime($result['memo_date']));
							$MemoData['memo_date'] = date('Y-m-d', strtotime($result['memo_date']));
							
							$MemoData['dist_memo_no'] = $result['memo_number'];
							$MemoData['dist_order_no'] = $dist_order_no;
							
							$MemoData['gross_value'] = $result['gross_value'];
							
							$MemoData['total_vat'] = ($result['total_vat']&&$result['total_vat']!='null')?$result['total_vat']:0;
							$MemoData['discount_value'] = ($result['discount_value']&&$result['discount_value']!='null')?$result['discount_value']:0;
							$MemoData['discount_percent'] = ($result['discount_percent']&&$result['discount_percent']!='null')?$result['discount_percent']:0;
							$MemoData['discount_type'] = ($result['discount_type']&&$result['discount_type']!='null')?$result['discount_type']:0;
							
							$MemoData['cash_recieved'] = 0;
							$MemoData['credit_amount'] = 0;
							$MemoData['memo_time'] = $result['memo_date_time']&&$result['memo_date_time']!='null'?$result['memo_date_time']:$result['memo_date'];
							
							
							$MemoData['memo_time'] = $result['memo_date_time']&&$result['memo_date_time']!='null'?$result['memo_date_time']:$result['memo_date'];
							
							$MemoData['sales_person_id'] = $so_id;
							$MemoData['ae_id'] = $ae_id;
							$MemoData['tso_id'] = $tso_id;
							$MemoData['is_active'] = 0;
							$MemoData['from_app'] = 1;
							$MemoData['status'] = 1;
							$MemoData['action'] = 0;
							$MemoData['is_program'] = 0;
							$MemoData['memo_reference_no'] = '';
							
							$MemoData['latitude'] = $result['latitude'];
							$MemoData['longitude'] = $result['longitude'];
							
							$MemoData['created_at'] = isset($result['updated_at']) && $result['updated_at'] ? $result['updated_at'] : $this->current_datetime();
							$MemoData['created_by'] = $so_id;
							$MemoData['updated_at'] = isset($result['updated_at']) && $result['updated_at'] ? $result['updated_at'] : $this->current_datetime();
							$MemoData['updated_by'] = $so_id;
							
							$MemoData['total_discount'] = ($result['total_discount']=='null')?0:$result['total_discount'];
							
							//pr($MemoData);
							//exit;
							
							$this->DistMemo->create();
			
							if ($this->DistMemo->save($MemoData)) 
							{																
								$memo_id = $this->DistMemo->getLastInsertId();
			
								if ($memo_id) 
								{
									$all_product_id = array_map(function($element) {
										return $element['product_id'];
									}, $result['memo_details']);
									
									foreach ($result['memo_details'] as $val) 
									{
										if ($val == NULL) {
											continue;
										}
										
										$val['price'] = $val['price']?$val['price']:0;
										
										$product_id = $memo_details['DistMemoDetail']['product_id'] = $val['product_id'];
			
										$punits_pre = $this->search_array($product_id, 'id', $product_list);
										//pr($punits_pre);//die();
										
										$memo_details['DistMemoDetail']['dist_memo_id'] = $memo_id;
										$memo_details['DistMemoDetail']['measurement_unit_id'] = $val['measurement_unit_id']>0?$val['measurement_unit_id']:$punits_pre['sales_measurement_unit_id'];
										$sales_price = $memo_details['DistMemoDetail']['price'] = $memo_products[$product_id][$val['price']][$val['is_bonus']]['price'];
										$sales_qty = $memo_details['DistMemoDetail']['sales_qty'] = $memo_products[$product_id][$val['price']][$val['is_bonus']]['sales_qty'];
										$is_bonus = $memo_details['DistMemoDetail']['is_bonus'] = $memo_products[$product_id][$val['price']][$val['is_bonus']]['is_bonus'];
										
										$memo_details['DistMemoDetail']['vat'] = $val['vat']?$val['vat']:0;
										
										$order_sales_qty = $memo_products[$product_id][$val['price']][$val['is_bonus']]['order_sales_qty'];
										$invoice_qty = $memo_products[$product_id][$val['price']][$val['is_bonus']]['invoice_qty'];
										
												
										$product_price_slab_id = 0;
										if ($sales_price > 0) {
											$product_price_slab_id = $this->dist_get_product_price_id($val['product_id'], $sales_price, $all_product_id, $MemoData['memo_date'], 1);
											//pr($product_price_slab_id);exit;
										}
										$memo_details['DistMemoDetail']['product_price_id'] = $product_price_slab_id['product_price_id'];		
										$memo_details['DistMemoDetail']['bonus_qty']= $val['is_bonus']?$sales_qty:0;
										$memo_details['DistMemoDetail']['bonus_product_id'] = 0;
										$memo_details['DistMemoDetail']['price'] = $sales_price?($sales_price-($val['discount_amount']?$val['discount_amount']:0)):0;
										$memo_details['DistMemoDetail']['actual_price'] = $sales_price;
										//Start for bonus
										$memo_details['DistMemoDetail']['is_bonus'] = $val['is_bonus'];
										$memo_details['DistMemoDetail']['bonus_id'] = 0;
										$memo_details['DistMemoDetail']['bonus_scheme_id'] = 0;
										//End for bouns
										
										$memo_details['DistMemoDetail']['discount_type'] = ($val['discount_type'] =='null')?0:$val['discount_type'];
										$memo_details['DistMemoDetail']['discount_amount'] = ($val['discount_amount'] =='null')?0:$val['discount_amount'];
										$memo_details['DistMemoDetail']['policy_type'] = ($val['policy_type'] =='null')?0:$val['policy_type'];
										$memo_details['DistMemoDetail']['policy_id'] = ($val['policy_id'] =='null')?0:$val['policy_id'];
			
										$total_product_data[] = $memo_details;
									   
										//update inventory
										if($invoice_qty < $sales_qty)
										{
											$n_sales_qty = $sales_qty - $invoice_qty;
											$update_type = 'deduct';
											$tran_type_id = 3;
										}
										else
										{
											$n_sales_qty = $invoice_qty - $sales_qty;
											$update_type = 'add';
											$tran_type_id = 4;
										}
										
										/*if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
											$base_quantity = $n_sales_qty;
											$invoice_qty = $invoice_qty;
										} else {
											$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $n_sales_qty);
											$invoice_qty = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $invoice_qty);
										}*/
										
										
										if($val['measurement_unit_id']>0)
										{
											if ($val['measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
												$base_quantity = round($n_sales_qty);
												$invoice_qty = round($invoice_qty);
											} else {
												$base_quantity = $this->unit_convert($product_id, $val['measurement_unit_id'], $n_sales_qty);
												$invoice_qty = $this->unit_convert($product_id, $val['measurement_unit_id'], $invoice_qty);
											}
										}
										else
										{
											if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
												$base_quantity = round($n_sales_qty);
												$invoice_qty = round($invoice_qty);
											} else {
												$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $n_sales_qty);
												$invoice_qty = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $invoice_qty);
											}
										}
										$this->dist_memo_update_current_inventory($base_quantity, $product_id, $store_id, $update_type, $tran_type_id, $result['memo_date'], $invoice_qty);
				
									}
			
										
									
									//pr($total_product_data);
									//die();
									$this->DistMemoDetail->saveAll($total_product_data);
									
								}
								
								
								$res['status'] = 1;
								//$res['memo_no'] = $result['memo_number'];
								$res['message'] = 'Memos has been created successfully.';
								
								//update dist order table
								$this->DistOrder->updateAll(array('DistOrder.processing_status' => 2), array('DistOrder.id' => $dist_order_id));
								$this->DistOrderDeliveryScheduleOrder->updateAll(array('DistOrderDeliveryScheduleOrder.processing_status' => 2), array('DistOrderDeliveryScheduleOrder.dist_order_no' => $dist_order_no));
								
							}
							
						}
						else
						{
							/*$memo_id_arr = $this->DistMemo->find('first', array(
								'conditions' => array(
									'DistMemo.dist_memo_no' => $result['memo_number']
								)
							));*/
							
							$memo_id_arr = $this->DistMemo->find('first', array(
								'conditions' => array(
									'DistMemo.dist_order_no' => $result['order_number']
								)
							));
							
							//pr($memo_id_arr);
							
							
						   
							$old_memo_id = $memo_id_arr['DistMemo']['id'];	
							//$old_memo_id = 52;
							
							$this->dist_memo_delete($old_memo_id, $sr_code, $office_id);
							//exit;
							
							$MemoData = array();
							$MemoData['id'] = $old_memo_id;
							$MemoData['office_id'] = $office_id;
							$MemoData['distributor_id'] = $distributor_id;
							$MemoData['sr_id'] = $sr_id;
							$MemoData['dist_route_id'] = $result['route_id'];
							$MemoData['sale_type_id'] = $sale_type_id;
							$MemoData['territory_id'] = $territory_id;
							$MemoData['thana_id'] = $thana_id;
							$MemoData['market_id'] = $result['market_id'];
							$MemoData['outlet_id'] = $result['outlet_id'];
							$MemoData['entry_date'] = date('Y-m-d', strtotime($result['memo_date']));
							$MemoData['memo_date'] = date('Y-m-d', strtotime($result['memo_date']));
							
							$MemoData['dist_memo_no'] = $result['memo_number'];
							$MemoData['dist_order_no'] = $dist_order_no;
							
							$MemoData['gross_value'] = $result['gross_value'];
							
							/*$MemoData['total_vat'] = $result['total_vat'];
							$MemoData['discount_value'] = $result['discount_value']?$result['discount_value']:0;
							$MemoData['discount_percent'] = $result['discount_percent']?$result['discount_percent']:0;
							$MemoData['discount_type'] = $result['discount_type']?$result['discount_type']:0;*/
							
							$MemoData['total_vat'] = ($result['total_vat']&&$result['total_vat']!='null')?$result['total_vat']:0;
							$MemoData['discount_value'] = ($result['discount_value']&&$result['discount_value']!='null')?$result['discount_value']:0;
							$MemoData['discount_percent'] = ($result['discount_percent']&&$result['discount_percent']!='null')?$result['discount_percent']:0;
							$MemoData['discount_type'] = ($result['discount_type']&&$result['discount_type']!='null')?$result['discount_type']:0;
							
							$MemoData['cash_recieved'] = 0;
							$MemoData['credit_amount'] = 0;
							$MemoData['is_active'] = 1;
							$MemoData['status'] = 1;
							$MemoData['memo_time'] = $result['memo_date_time']&&$result['memo_date_time']!='null'?$result['memo_date_time']:$result['memo_date'];
							$MemoData['sales_person_id'] = $so_id;
							$MemoData['ae_id'] = $ae_id;
							$MemoData['tso_id'] = $tso_id;
							$MemoData['from_app'] = 1;
							$MemoData['action'] = 0;
							$MemoData['is_program'] = 0;
							$MemoData['memo_reference_no'] = '';
							
							$MemoData['latitude'] = $result['latitude'];
							$MemoData['longitude'] = $result['longitude'];
							
							$MemoData['created_at'] = isset($result['updated_at']) && $result['updated_at'] ? $result['updated_at'] : $this->current_datetime();
							$MemoData['created_by'] = $so_id;
							$MemoData['updated_at'] = isset($result['updated_at']) && $result['updated_at'] ? $result['updated_at'] : $this->current_datetime();
							$MemoData['updated_by'] = $so_id;
							
							$MemoData['total_discount'] = ($result['total_discount']=='null')?0:$result['total_discount'];
							
							//pr($MemoData);exit;
							
							$this->DistMemo->create();
			
							if ($this->DistMemo->save($MemoData)) 
							{
								$memo_id = $old_memo_id;
			
								if ($memo_id) 
								{
									$all_product_id = array_map(function($element) {
										return $element['product_id'];
									}, $result['memo_details']);
									
									//pr($result['memo_details']);exit;
									
									foreach ($result['memo_details'] as $val) 
									{
										if ($val == NULL) {
											continue;
										}
										
										$product_id = $memo_details['DistMemoDetail']['product_id'] = $val['product_id'];
										
										$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
										$product_list = Set::extract($products, '{n}.Product');
			
										$punits_pre = $this->search_array($product_id, 'id', $product_list);
										//pr($punits_pre);//die();
										
										$memo_details['DistMemoDetail']['dist_memo_id'] = $memo_id;
										//$memo_details['DistMemoDetail']['measurement_unit_id'] = $punits_pre['sales_measurement_unit_id'];
										$memo_details['DistMemoDetail']['measurement_unit_id'] = $val['measurement_unit_id']>0?$val['measurement_unit_id']:$punits_pre['sales_measurement_unit_id'];
										$sales_price = $memo_details['DistMemoDetail']['price'] = $val['price']?$val['price']:0;
										$sales_qty = $memo_details['DistMemoDetail']['sales_qty'] = $val['quantity'];
										
										$memo_details['DistMemoDetail']['vat'] = $val['vat']?$val['vat']:0;
			
										$product_price_slab_id = 0;
										if ($sales_price > 0) {
											$product_price_slab_id = $this->dist_get_product_price_id($val['product_id'], $sales_price, $all_product_id, $MemoData['memo_date'], 1);
											//pr($product_price_slab_id);exit;
										}
										$memo_details['DistMemoDetail']['product_price_id'] = $product_price_slab_id['product_price_id']?$product_price_slab_id['product_price_id']:0;
			
										$memo_details['DistMemoDetail']['bonus_qty']= $val['is_bonus']?$sales_qty:0;
										$memo_details['DistMemoDetail']['bonus_product_id'] = 0;
										$memo_details['DistMemoDetail']['price'] = $sales_price ?($sales_price-($val['discount_amount']?$val['discount_amount']:0)):0;
										$memo_details['DistMemoDetail']['actual_price'] = $sales_price;
										
										//Start for bonus
										$memo_details['DistMemoDetail']['is_bonus'] = $val['is_bonus'];
										$memo_details['DistMemoDetail']['bonus_id'] = 0;
										$memo_details['DistMemoDetail']['bonus_scheme_id'] = 0;
										//End for bouns
										
										$memo_details['DistMemoDetail']['discount_type'] = ($val['discount_type'] == 'null')?0:$val['discount_type'];
										$memo_details['DistMemoDetail']['discount_amount'] = ($val['discount_amount'] == 'null')?0:$val['discount_amount'];
										$memo_details['DistMemoDetail']['policy_type'] = ($val['policy_type'] == 'null')?0:$val['policy_type'];
										$memo_details['DistMemoDetail']['policy_id'] = ($val['policy_id'] == 'null')?0:$val['policy_id'];;
			
										$total_product_data[] = $memo_details;
									   
										
										//update inventory
										
										if($val['measurement_unit_id']>0)
										{
											if ($val['measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) 
											{
												$base_quantity = round($sales_qty);
											} else {
												$base_quantity = $this->unit_convert($product_id, $val['measurement_unit_id'], $sales_qty);
											}
										}
										else
										{
											if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) 
											{
												$base_quantity = round($sales_qty);
											} else {
												$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
											}
										}
										
										$invoice_qty = 0;
										$update_type = 'deduct';									
										$this->dist_memo_update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 3, $result['memo_date'], $invoice_qty);
										
									}
									
									
									//pr($total_product_data);
																
									$this->DistMemoDetail->saveAll($total_product_data);
									$res['status'] = 1;
									$res['message'] = 'Memos has been updated successfully.';
								}
							}
						}
					}
					else
					{
						$res['status'] = 0;
            			$res['message'] = 'Market not found.';
					}
				}
				else
				{
				 	$res['status'] = 0;
            		$res['message'] = 'Memo created failed. Stock not available.';
				}
				
			}
			$path = APP . 'logs/DBwiseLog/'.$distributor_id.'/'.date('Y-m-d').'/';
			if(!file_exists($path))
			{
				 mkdir($path, 0777, true);
			}
	        $myfile = fopen($path . "dist_create_memo_response.txt", "a") or die("Unable to open file!");
	        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input().' respose : '.json_encode($res));
	        fclose($myfile);
		} 
		else 
		{
            $res['status'] = 0;
            $res['message'] = 'No Data Provided.';
        }
        $path = APP . 'logs/';
        $myfile = fopen($path . "dist_create_memo_response.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input().' response :'.json_encode($res));
        fclose($myfile);

        $this->set(array(
            'memo' => $res,
            '_serialize' => array('memo')
        ));
		
	}
	
	public function dist_memo_delete($id = 0, $sr_code = 0, $office_id = 0) {

        $this->loadModel('Product');
        $this->loadModel('DistMemo');
        $this->loadModel('DistMemoDetail');		
        
		$count = $this->DistMemo->find('count', array(
			'conditions' => array(
				'DistMemo.id' => $id
			)
		));

		$memo_id_arr = $this->DistMemo->find('first', array(
			'conditions' => array(
				'DistMemo.id' => $id
			)
		));
		
		
		

		$this->loadModel('DistStore');
		$store_id_arr = $this->DistStore->find('first', array(
			'conditions' => array(
				'DistStore.dist_distributor_id' => $memo_id_arr['DistMemo']['distributor_id']
			),
			'recursive' => -1
		));
		
		$store_id = $store_id_arr['DistStore']['id'];

		
		$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
		$product_list = Set::extract($products, '{n}.Product');

		for ($memo_detail_count = 0; $memo_detail_count < count($memo_id_arr['DistMemoDetail']); $memo_detail_count++) 
		{
			$product_id = $memo_id_arr['DistMemoDetail'][$memo_detail_count]['product_id'];
			$sales_qty = $memo_id_arr['DistMemoDetail'][$memo_detail_count]['sales_qty'];
			$sales_price = $memo_id_arr['DistMemoDetail'][$memo_detail_count]['price'];
			$is_bonus = $memo_id_arr['DistMemoDetail'][$memo_detail_count]['is_bonus'];
			$measurement_unit_id = $memo_id_arr['DistMemoDetail'][$memo_detail_count]['measurement_unit_id'];

			//$punits_pre = $this->search_array($product_id, 'id', $product_list);
			
			$punits_pre = $this->search_array($product_id, 'id', $product_list);
			
			if($measurement_unit_id>0)
			{
				if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) 
				{
					$base_quantity = round($sales_qty);
				} else {
					$base_quantity = $this->unit_convert($product_id, $measurement_unit_id, $sales_qty);
				}
			}
			else
			{
				if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
					$base_quantity = round($sales_qty);
				} else {
					$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
				}
			}
			
			$invoice_qty = 0;

			$update_type = 'add';
			$this->dist_memo_update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 4, $memo_id_arr['DistMemo']['memo_date'], $invoice_qty);
			
			// subract sales achievement and stamp achievemt 
			// sales calculation
			$t_price = $sales_qty * $sales_price;
		}

		$memo_id = $memo_id_arr['DistMemo']['id'];
		$dist_memo_no = $memo_id_arr['DistMemo']['dist_memo_no'];
		$this->DistMemoDetail->deleteAll(array('DistMemoDetail.dist_memo_id' => $memo_id));
		$this->DistMemo->deleteAll(array('DistMemo.dist_memo_no' => $dist_memo_no));
    }
	
		
	
	
	
	public function dist_memo_update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct', $transaction_type_id = 0, $transaction_date = '', $invoice_qty=0) {

        $this->loadModel('DistCurrentInventory');

        $find_type = 'all';
        if ($update_type == 'add')
            $find_type = 'first';

        $inventory_info = $this->DistCurrentInventory->find($find_type, array(
            'conditions' => array(
                //'CurrentInventory.qty >=' => 0,
                'DistCurrentInventory.store_id' => $store_id,
                'DistCurrentInventory.inventory_status_id' => 1,
                'DistCurrentInventory.product_id' => $product_id
            ),
            'order' => array('DistCurrentInventory.qty' => 'desc'),
            'recursive' => -1
        ));

		
		
		if ($update_type == 'deduct') 
		{
			foreach ($inventory_info as $val) 
			{
				if($quantity <= 0)
				{
					break;
				}
				
				if ($quantity <= $val['DistCurrentInventory']['qty']) 
				{
					$this->DistCurrentInventory->id = $val['DistCurrentInventory']['id'];
					$this->DistCurrentInventory->updateAll(
							array(
							'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' . $quantity,
							'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
							'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
							'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
							'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
							), 
							array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
					);
					break;
				} 
				else 
				{

					if ($val['DistCurrentInventory']['id'] > 0) {

						$this->DistCurrentInventory->id = $val['DistCurrentInventory']['id'];
						if($val['DistCurrentInventory']['qty'] <= 0)
						{
							$this->DistCurrentInventory->updateAll(
									array(
									'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' .$quantity ,
									'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
									'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
									'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
									'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
									), 
									array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
							);
							$quantity = 0;
							break;
						}
						else
						{
							$quantity = $quantity - $val['DistCurrentInventory']['qty'];
							$this->DistCurrentInventory->updateAll(
								array(
								'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' . $val['DistCurrentInventory']['qty'],
								'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
								'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
								'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
								'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
								), 
								array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
							);
						}
					}
				}
			}
		} 
		else 
		{
			
			if (!empty($inventory_info)) 
			{

				$this->DistCurrentInventory->updateAll(
						array(
							'DistCurrentInventory.qty' => 'DistCurrentInventory.qty + ' . $quantity,
							//'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
						 	'DistCurrentInventory.transaction_type_id' => $transaction_type_id, 
						 	'DistCurrentInventory.store_id' => $store_id, 
							'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
						), 
						array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
				);
				
				//for invoice qty
				if ($invoice_qty <= $inventory_info['DistCurrentInventory']['invoice_qty']) {
					$this->DistCurrentInventory->updateAll(
						array(
							//'DistCurrentInventory.qty' => 'DistCurrentInventory.qty + ' . $quantity,
							'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
						 	'DistCurrentInventory.transaction_type_id' => $transaction_type_id, 
						 	'DistCurrentInventory.store_id' => $store_id, 
							'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
						), 
						array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
					);
					
				}
				else
				{
					$this->DistCurrentInventory->updateAll(
						array(
							'DistCurrentInventory.invoice_qty' => 0,
						 	'DistCurrentInventory.transaction_type_id' => $transaction_type_id, 
						 	'DistCurrentInventory.store_id' => $store_id, 
							'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
						), 
						array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
					);
				}
			}
			
		}
		
		//exit;
        return true;
    }
	
	public function dist_last_memos(){
		$this->loadModel('DistMemo');
		$this->loadModel('DistMemoDetail');
        $this->loadModel('SalesPerson');
		$this->loadModel('Product');
		
		$json_data = $this->request->input('json_decode', true);
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_last_memos.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];

		
		$res_status = 1;
        //$seven_days_pre = date('Y-m-d 00:00:00', strtotime('-3 days'));
		
		$outlet_id = isset($json_data['outlet_id'])?$json_data['outlet_id']:0;
		$outlet_category_id = isset($json_data['outlet_category_id'])?$json_data['outlet_category_id']:0;
		$start_date = isset($json_data['start_date'])?$json_data['start_date']:0;
		$end_date = isset($json_data['end_date'])?$json_data['end_date']:0;

		$conditions = array();

        $conditions = array(
			// 'DistMemo.sr_id' => $sr_id, 
			'DistSrRouteMapping.dist_sr_id' => $sr_id, 
			'DistSrRouteMapping.dist_distributor_id' => $distributor_id, 
			'DistMemo.status' => 1,
		);
		
        if($outlet_id)$conditions['DistMemo.outlet_id'] = $outlet_id;
		if($outlet_category_id)$conditions['Outlet.category_id'] = $outlet_category_id;
		if($start_date)$conditions['DistMemo.memo_date >='] = $start_date;
		if($end_date)$conditions['DistMemo.memo_date <='] = $end_date;
 		
		//pr($conditions);
		
        $memo_list = $this->DistMemo->find('all', array(
            'fields' => array('DistMemo.*'),
            'conditions' => $conditions,
            'joins'=>array(
            	array(
					'alias' => 'DistSrRouteMapping',
					'table' => 'dist_sr_route_mappings',
					'type' => 'INNER',
					'conditions' => 'DistMemo.dist_route_id = DistSrRouteMapping.dist_route_id'
				)
            	),
            'recursive' => 1
        ));

        $memo_array = array();
		$mdata = array();
		//pr($memo_list);
        foreach ($memo_list as $m) {
            $mdata['id'] = $m['DistMemo']['id'];
            $mdata['memo_number'] = $m['DistMemo']['dist_memo_no'];
			$mdata['order_number'] = $m['DistMemo']['dist_order_no'];
            $mdata['memo_date'] = $m['DistMemo']['memo_date'];
			$mdata['memo_date_time'] = $m['DistMemo']['memo_time'];
            $mdata['sales_person_id'] = $m['DistMemo']['sr_id'];
            $mdata['outlet_id'] = $m['DistMemo']['outlet_id'];
            $mdata['market_id'] = $m['DistMemo']['market_id'];
            $mdata['territory_id'] = $m['DistMemo']['territory_id'];
            $mdata['gross_value'] = $m['DistMemo']['gross_value'];
			$mdata['total_vat'] = $m['DistMemo']['total_vat'];
			
			$mdata['discount_value'] = $m['DistMemo']['discount_value'];
			$mdata['discount_percent'] = $m['DistMemo']['discount_percent'];
			$mdata['discount_type'] = $m['DistMemo']['discount_type'];
			
            $mdata['adjustment_amount'] = $m['DistMemo']['adjustment_amount'];
            $mdata['adjustment_note'] = $m['DistMemo']['adjustment_note'];
            $mdata['cash_recieved'] = $m['DistMemo']['cash_recieved'];
            $mdata['credit_amount'] = $m['DistMemo']['credit_amount'];
            $mdata['latitude'] = $m['DistMemo']['latitude'];
            $mdata['longitude'] = $m['DistMemo']['longitude'];
            $mdata['from_app'] = $m['DistMemo']['from_app'];
			$mdata['status'] = $m['DistMemo']['status'];
			
			$mdata['editable'] = $m['DistMemo']['memo_editable'];
			$mdata['total_discount'] = $m['DistMemo']['total_discount'];
			
            if($m['DistMemo']['from_app']== 0 && $m['DistMemo']['action']==1)
            {
                $mdata['updated_at'] = date('Y-m-d H:i:s',strtotime($m['DistMemo']['memo_date']));
            }
            else
            {
                $mdata['updated_at'] = $m['DistMemo']['memo_time'];
            }
            $mdata['action'] = ($res_status == 1 ? 1 : $m['DistMemo']['action']);
            $mdata['memo_details'] = $m['DistMemoDetail'];
            $mm = 0;

            foreach ($m['DistMemoDetail'] as $each_memo_details) {

                $product_id = $each_memo_details['product_id'];
                $product_info = $this->Product->find('first', array(
                    'fields' => array('product_type_id'),
                    'conditions' => array('id' => $product_id),
                    'recursive' => -1
                ));

                $type = "";
                if ($product_info['Product']['product_type_id'] == 1 && $each_memo_details['price'] > 0) {
                    $type = 0;
                } else if ($product_info['Product']['product_type_id'] == 3) {
                    $type = 1;
                } 
                else if ($product_info['Product']['product_type_id'] == 1 && $each_memo_details['price'] < 1) {
                    $type = 2;
                }

                $mdata['memo_details'][$mm]['product_type'] = $type;
				$mdata['memo_details'][$mm]['quantity'] = $each_memo_details['sales_qty'];
				$mdata['memo_details'][$mm]['price'] = $each_memo_details['actual_price'];
				$mdata['memo_details'][$mm]['memo_number'] = $m['DistMemo']['dist_memo_no'];
                $mm++;
            }


            $memo_array[] = $mdata;

        }
		
		//pr($memo_array);
		
		$this->set(array(
            'memos' => $memo_array,
            '_serialize' => array('memos')
        ));
	}
	
	public function dist_last_memo_details(){
		$this->loadModel('DistMemo');
		$this->loadModel('DistMemoDetail');
        $this->loadModel('SalesPerson');
		$this->loadModel('Product');
		
		$json_data = $this->request->input('json_decode', true);
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_last_memos.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];

		
		$res_status = 1;
        //$seven_days_pre = date('Y-m-d 00:00:00', strtotime('-3 days'));
		
		$outlet_id = isset($json_data['outlet_id'])?$json_data['outlet_id']:0;

		$conditions = array();

        $conditions = array(
			// 'DistMemo.sr_id' => $sr_id, 
			'DistSrRouteMapping.dist_sr_id' => $sr_id, 
			'DistSrRouteMapping.dist_distributor_id' => $distributor_id, 
			'DistMemo.status' => 1,
		);
		
        if($outlet_id)$conditions['DistMemo.outlet_id'] = $outlet_id;
 		
		//pr($conditions);
		
        $m = $this->DistMemo->find('first', array(
            'fields' => array('DistMemo.*'),
            'conditions' => $conditions,
            'joins'=>array(
            	array(
					'alias' => 'DistSrRouteMapping',
					'table' => 'dist_sr_route_mappings',
					'type' => 'INNER',
					'conditions' => 'DistMemo.dist_route_id = DistSrRouteMapping.dist_route_id'
				)
            	),
			'order' => array('DistMemo.id DESC'),
            'recursive' => 1
        ));
		
		//pr($m);exit;

        $memo_array = array();
		$mdata = array();
		//pr($memo_list);
        if($m) 
		{
            $mdata['id'] = $m['DistMemo']['id'];
            $mdata['memo_number'] = $m['DistMemo']['dist_memo_no'];
			$mdata['order_number'] = $m['DistMemo']['dist_order_no'];
            $mdata['memo_date'] = $m['DistMemo']['memo_date'];
			$mdata['memo_date_time'] = $m['DistMemo']['memo_time'];
            $mdata['sales_person_id'] = $m['DistMemo']['sr_id'];
            $mdata['outlet_id'] = $m['DistMemo']['outlet_id'];
            $mdata['market_id'] = $m['DistMemo']['market_id'];
            $mdata['territory_id'] = $m['DistMemo']['territory_id'];
            $mdata['gross_value'] = $m['DistMemo']['gross_value'];
			$mdata['total_vat'] = $m['DistMemo']['total_vat'];
			
			$mdata['discount_value'] = $m['DistMemo']['discount_value'];
			$mdata['discount_percent'] = $m['DistMemo']['discount_percent'];
			$mdata['discount_type'] = $m['DistMemo']['discount_type'];
			
            $mdata['adjustment_amount'] = $m['DistMemo']['adjustment_amount'];
            $mdata['adjustment_note'] = $m['DistMemo']['adjustment_note'];

            $mdata['cash_recieved'] = $m['DistMemo']['cash_recieved'];
            $mdata['credit_amount'] = $m['DistMemo']['credit_amount'];
            $mdata['latitude'] = $m['DistMemo']['latitude'];
            $mdata['longitude'] = $m['DistMemo']['longitude'];
            $mdata['from_app'] = $m['DistMemo']['from_app'];
			$mdata['status'] = $m['DistMemo']['status'];
			$mdata['total_discount'] = $m['DistMemo']['total_discount'];
            if($m['DistMemo']['from_app']== 0 && $m['DistMemo']['action']==1)
            {
                $mdata['updated_at'] = date('Y-m-d H:i:s',strtotime($m['DistMemo']['memo_date']));
            }
            else
            {
                $mdata['updated_at'] = $m['DistMemo']['memo_time'];
            }
            $mdata['action'] = ($res_status == 1 ? 1 : $m['DistMemo']['action']);
            $mdata['memo_details'] = $m['DistMemoDetail'];
            $mm = 0;

            foreach ($m['DistMemoDetail'] as $each_memo_details) {

                $product_id = $each_memo_details['product_id'];
                $product_info = $this->Product->find('first', array(
                    'fields' => array('product_type_id'),
                    'conditions' => array('id' => $product_id),
                    'recursive' => -1
                ));

                $type = "";
                if ($product_info['Product']['product_type_id'] == 1 && $each_memo_details['price'] > 0) {
                    $type = 0;
                } else if ($product_info['Product']['product_type_id'] == 3) {
                    $type = 1;
                } else if ($product_info['Product']['product_type_id'] == 1 && $each_memo_details['price'] < 1) {
                    $type = 2;
                }

                $mdata['memo_details'][$mm]['product_type'] = $type;
				$mdata['memo_details'][$mm]['quantity'] = $each_memo_details['sales_qty'];
				$mdata['memo_details'][$mm]['price'] = $each_memo_details['actual_price'];
				$mdata['memo_details'][$mm]['memo_number'] = $m['DistMemo']['dist_memo_no'];
                $mm++;
            }


            $memo_array[] = $mdata;

        }
		
		//pr($memo_array);
		
		$this->set(array(
            'last_memo_details' => $memo_array,
            '_serialize' => array('last_memo_details')
        ));
	}
	/* ------------------- End Dist Memo Create -------------------- */
	
	
	/* ------------------- Start order_ready_for_process_list (who are waiting for process) ------------------ */
	public function dist_order_ready_for_process_list(){
		$this->loadModel('DistOrder');
		$this->loadModel('DistOrderDetail');
        $this->loadModel('SalesPerson');
		$this->loadModel('Product');
		
		$json_data = $this->request->input('json_decode', true);
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_order_ready_for_process_list.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        /*---------------------------- Mac check --------------------------------*/
				
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];

		
		$res_status = 1;
        //$seven_days_pre = date('Y-m-d 00:00:00', strtotime('-3 days'));
		
		$order_date = isset($json_data['order_date'])?$json_data['order_date']:0;
		//$start_date = $json_data['start_date'];
		//$end_date = $json_data['end_date'];

		$conditions = array();

        $conditions = array(
		// 'DistOrder.sr_id' => $sr_id, 
		'DistSrRouteMapping.dist_sr_id' => $sr_id, 
		'DistSrRouteMapping.dist_distributor_id' => $distributor_id, 
		//'DistOrder.action' => 1, 
		'DistOrder.status' => 1,
		//'DistOrder.status  !=' => 3,
		'DistOrder.processing_status' => array(0,3)
		);
		
		if($order_date)$conditions['DistOrder.order_date'] = $order_date;
		
        $order_list = $this->DistOrder->find('all', array(
            'fields' => array('DistOrder.*', 'DistOutlet.name'),
            'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'DistOutlet',
					'table' => 'dist_outlets',
					'type' => 'INNER',
					'conditions' => 'DistOrder.outlet_id = DistOutlet.id'
				),
				array(
					'alias' => 'DistSrRouteMapping',
					'table' => 'dist_sr_route_mappings',
					'type' => 'INNER',
					'conditions' => 'DistOrder.dist_route_id = DistSrRouteMapping.dist_route_id'
				)
			),
            'recursive' => -1
        ));
		//pr($order_list); exit;

        $order_array = array();
		$mdata = array();
		
        foreach ($order_list as $m) 
		{
            //
            $mdata['order_number'] 	= $m['DistOrder']['dist_order_no'];
			$mdata['outlet_id'] 	= $m['DistOrder']['outlet_id'];
			$mdata['outlet_name'] 	= $m['DistOutlet']['name'];
			$mdata['gross_value'] 	= $m['DistOrder']['gross_value'];
            $mdata['order_date'] 	= $m['DistOrder']['order_date'];
			$mdata['total_discount'] = $m['DistOrder']['total_discount'];
			
            $order_array[] = $mdata;
        }
		
		//pr($order_array);
		
		$this->set(array(
            'orders' => $order_array,
            '_serialize' => array('orders')
        ));
	}
	/* ------------------- End order_ready_for_process_list (who are waiting for process) -------------------- */
	
	
	/* ------------------- Start Order Delivery Schedule (Working as partail success process) ------------------ */
	public function dist_order_delivery_schedule(){
		$this->loadModel('DistOrder');
		$this->loadModel('DistOrderDetail');
		
		$this->loadModel('DistOrderDeliverySchedule');
		$this->loadModel('DistOrderDeliveryScheduleOrder');
		$this->loadModel('DistOrderDeliveryScheduleOrderDetail');
		
		$this->loadModel('SalesPerson');
		
        $json_data = $this->request->input('json_decode', true);	
		//pr($json_data);exit;
		
        
        $json = $this->request->input();
        $all_inserted = true;
        $relation_array = array();
		
		//pr($json_data);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }

        $path = APP . 'logs/';
        $myfile = fopen($path . "dist_order_delivery_schedule.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			'recursive' => -1
		));
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		
		$path = APP . 'logs/DBwiseLog/'.$distributor_id.'/'.date('Y-m-d').'/';
		if(!file_exists($path))
		{
			 mkdir($path, 0777, true);
		}
        $myfile = fopen($path . "dist_order_delivery_schedule.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
		$this->loadModel('Product');
		$product_measurement_units = $this->Product->find('list', array('fields' => array('id', 'sales_measurement_unit_id')));
		//pr($product_measurement_units);
        $product_category_ids = $this->Product->find('list', array('fields' => array('id', 'product_category_id')));
		//pr($product_category_ids);
		
		$this->loadModel('DistCurrentInventory');
        $inventory_results = $this->DistCurrentInventory->find('all', array(
            'conditions' => array(
                'DistCurrentInventory.store_id' => $store_id,
                'DistCurrentInventory.inventory_status_id' => 1
            ),
			'fields' => array('product_id', 'sum(qty) as qty'),
			'group'=>array('product_id'),
            //'order' => array('DistCurrentInventory.product_id' => 'asc'),
            'recursive' => -1

        ));
		
		$inventory_qty_info = array();
		$inventory_bouns_qty_info = array();
		foreach($inventory_results as $inventory_result)
		{
			$inventory_qty_info[$inventory_result['DistCurrentInventory']['product_id']]=$inventory_result[0]['qty'];
		}
		
		
		$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
		$product_list = Set::extract($products, '{n}.Product');
		
		
		$order_data_list = array();
		$process_table_status = 1;
		$process_table_status_p = 0;
		
		$order_results = $this->DistOrder->find('all', array(
			'conditions' => array(
				'DistOrder.dist_order_no' => $json_data['order_numbers'],
				//'DistOrder.processing_status !=' => 1,
			),
			'order' => array('DistOrder.order_time' => 'asc'),
			'recursive' => 1
		));

		//pr($order_results);
		//exit;
		
		$order_products_list = array();
		foreach($order_results as $key => $order_info)
		{
			$order_date = $order_info['DistOrder']['order_date'];
			$order_id = $order_info['DistOrder']['id'];
			$processing_status = $order_info['DistOrder']['processing_status'];
			$dist_order_no = $order_info['DistOrder']['dist_order_no'];
			$this->DistOrderDeliveryScheduleOrder->deleteAll(array('DistOrderDeliveryScheduleOrder.dist_order_no' => $dist_order_no));
			$this->DistOrderDeliveryScheduleOrderDetail->deleteAll(array('DistOrderDeliveryScheduleOrderDetail.dist_order_no' => $dist_order_no));
			
			
			$stock_deduction[$order_id]=1;
			$previous_order_status = $this->DistOrder->find('first', array(
				'conditions' => array(
					'DistOrder.id' => $order_id,
				),
				'order' => array('DistOrder.order_time' => 'asc'),
				'recursive' => 1
			));

			if($previous_order_status['DistOrder']['status']==2 && $previous_order_status['DistOrder']['processing_status']==1)
			{
				$previous_submitted_order['DistOrderDeliveryScheduleOrderDetail']['status']=1;
				$total_order_detail_new[$dist_order_no][] = $previous_submitted_order;
				continue;
			}
			
			$this->DistOrder->id = $order_id;
			if ($this->DistOrder->id) 
			{
				$this->DistOrder->updateAll(
					array('DistOrder.processing_status' => 1, 'DistOrder.status' => 2, 'DistOrder.action' => 0),   //fields to update
					array('DistOrder.id' => $order_id) //condition
				);
			}

			foreach($order_info['DistOrderDetail'] as $order_detail_result)
			{
				$product_id = $order_detail_result['product_id'];
				$order_details_id = $order_detail_result['id'];
				$sales_total_qty = $order_detail_result['sales_qty'];
				$measurement_unit_id = $order_detail_result['measurement_unit_id'];
				$price = $order_detail_result['price'];
				$order_id = $order_id;
				
				$order_products_list[$order_details_id][$order_id] = array(
					'order_id'				=> $order_id,
					'dist_order_no'			=> $dist_order_no,
					'product_id'			=> $product_id,
					'measurement_unit_id'	=> $measurement_unit_id,
					'processing_status'		=> $processing_status,
					'sales_qty'				=> $sales_total_qty,
					'price'					=> $price,
					'order_date'			=> $order_date,
					'is_bonus'				=> $order_detail_result['is_bonus'],
				);
				
			}
		}	
									
								
		
		//pr($order_products_list);exit;
		
		foreach($order_products_list as $order_details_id => $order_info)
		{
			//$order_id = $order_info['DistOrder']['id'];
			
			//pr($order_info);exit;
			
			$i=0;
			
			foreach($order_info as $order_id => $order_detail_result)
			{
				$product_id = $order_detail_result['product_id'];
				$order_id = $order_detail_result['order_id'];
				$sales_total_qty = $order_detail_result['sales_qty'];
				$order_total_qty = $order_detail_result['sales_qty'];
				$price = $order_detail_result['price'];
				
				$product_id = $order_detail_result['product_id'];
				$order_date = $order_detail_result['order_date'];
				
				$is_bonus = $price>0?0:1;
				$dist_order_no = $order_detail_result['dist_order_no'];
				$measurement_unit_id = $order_detail_result['measurement_unit_id'];
				//$order_id = $order_detail_result['dist_order_id'];
				
				
				//$sales_qty=$this->unit_convert($product_id, $product_measurement_units[$product_id], $sales_total_qty);
				
				$punits_pre = $this->search_array($product_id, 'id', $product_list);
				if($measurement_unit_id>0)
				{
					if ($measurement_unit_id == $punits_pre['base_measurement_unit_id'])
					{
						$sales_qty = round($sales_total_qty);
					}
					else
					{
						$sales_qty = $this->unit_convert($product_id, $measurement_unit_id, $sales_total_qty);
					}
				}
				else
				{
					if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
						$sales_qty = round($sales_total_qty);
					} else {
						$sales_qty = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_total_qty);
					}
				}
				$product_stock_qty = @$inventory_qty_info[$product_id];

				
				
				if($product_stock_qty >= $sales_qty)
				{
					$process_table_status_p = 2;
					
					$n_product_stock_qty = $product_stock_qty-$sales_qty;
					$inventory_qty_info[$product_id] = $n_product_stock_qty;

					$order_data_list['DistOrderDeliveryScheduleOrderDetail'][$dist_order_no][$order_details_id] = array(
						'order_id'		=> $order_id,
						'dist_order_no'	=> $dist_order_no,
						'product_id'	=> $product_id,
						'measurement_unit_id' => $measurement_unit_id,
						'order_qty'		=> $order_total_qty,
						'invoice_qty'	=> $order_total_qty,
						'price'			=> $price,
						'status'		=> 1,
						'order_date'	=> $order_date,
						'is_bonus'		=> $is_bonus,
					);	
				}
				else
				{
					$stock_deduction[$order_id]=0;
					$process_table_status = 0;
					$invoice_qty=$this->unit_convertfrombase($product_id, $product_measurement_units[$product_id], $product_stock_qty);
					
					$punits_pre = $this->search_array($product_id, 'id', $product_list);
					if($measurement_unit_id>0)
					{
						$invoice_qty=$this->unit_convertfrombase($product_id, $measurement_unit_id, $product_stock_qty);
					}else{
						$invoice_qty=$this->unit_convertfrombase($product_id, $punits_pre['sales_measurement_unit_id'], $product_stock_qty);	
						
					}
					
					$invoice_qty = $i==0?$invoice_qty:0;
					$order_data_list['DistOrderDeliveryScheduleOrderDetail'][$dist_order_no][$order_details_id] = array(
						'order_id'		=> $order_id,
						'dist_order_no'	=> $dist_order_no,
						'product_id'	=> $product_id,
						'order_qty'		=> $order_total_qty,
						'invoice_qty'	=> $invoice_qty>0?$invoice_qty:0,
						//'invoice_qty'	=> 0,
						'price'			=> $price,
						'status'		=> 0,
						'order_date'	=> $order_date,
						'is_bonus'		=> $is_bonus,
						'measurement_unit_id' => $measurement_unit_id,
					);	
					$i++;
				}
			}
		}
		//pr($order_data_list);
		//exit;
		
		$process_table_status = $process_table_status?$process_table_status:$process_table_status_p;
		
		
		//insert schedule data
		$order_data_list['DistOrderDeliverySchedule'] = array(
			'office_id' 			=> $office_id,
			'distributor_id' 		=> $distributor_id,
			//'order_id' 			=> $order_id,
			'sr_id' 				=> $sr_id,
			'sales_person_id' 		=> $json_data['sales_person_id'],
			'process_status' 		=> 1,
			'status' 				=> $process_table_status,
			'process_date_time' 	=> $this->current_datetime(),
			'created_at' 			=> $this->current_datetime(),
			'created_by' 			=> $json_data['sales_person_id'],
			'updated_at' 			=> $this->current_datetime(),
			'updated_by' 			=> $json_data['sales_person_id'],
		);
		
		//pr($order_data_list);exit;
		
		$this->DistOrderDeliverySchedule->create();
        if($this->DistOrderDeliverySchedule->save($order_data_list))
		{
			$schedule_id = $this->DistOrderDeliverySchedule->getLastInsertID();
						
			foreach($order_data_list['DistOrderDeliveryScheduleOrderDetail'] as $dist_order_no => $order_details)
			{
				$schedule_order_status = 1;
				
				//stock check real time
				foreach($order_details as $o_result);
				$dist_stock_check = $this->dist_stock_check($store_id, $o_result['order_id']);
				
				//insert into schedule order details table
				foreach($order_details as $key => $result)
				{
					$product_id = $result['product_id'];
					$dist_order_id=$result['order_id'];
					$measurement_unit_id = $result['measurement_unit_id'];
					$order_qty = $result['order_qty'];
					$is_bonus = $result['is_bonus'];
					$order_detail['DistOrderDeliveryScheduleOrderDetail']['dist_order_delivery_schedule_id'] = $schedule_id;	
					$order_detail['DistOrderDeliveryScheduleOrderDetail']['dist_order_id'] 	= $result['order_id'];
					$order_detail['DistOrderDeliveryScheduleOrderDetail']['dist_order_no'] 	= $dist_order_no;						
					$order_detail['DistOrderDeliveryScheduleOrderDetail']['measurement_unit_id'] 	= $measurement_unit_id;
					$order_detail['DistOrderDeliveryScheduleOrderDetail']['product_id'] 	= $product_id;
					$order_detail['DistOrderDeliveryScheduleOrderDetail']['order_qty'] 		= $order_qty;
					$order_detail['DistOrderDeliveryScheduleOrderDetail']['invoice_qty'] 	= $result['invoice_qty'];
					$order_detail['DistOrderDeliveryScheduleOrderDetail']['status'] 		= $result['status'];
					$order_detail['DistOrderDeliveryScheduleOrderDetail']['is_bonus'] 		= $result['is_bonus'];
					
					$order_detail['DistOrderDeliveryScheduleOrderDetail']['created_at'] 	= $this->current_datetime();
					$order_detail['DistOrderDeliveryScheduleOrderDetail']['created_by'] 	= $json_data['sales_person_id'];
					$order_detail['DistOrderDeliveryScheduleOrderDetail']['updated_at'] 	= $this->current_datetime();
					$order_detail['DistOrderDeliveryScheduleOrderDetail']['updated_by'] 	= $json_data['sales_person_id'];
					
					$total_order_detail[] = $order_detail;
					$this->DistOrderDeliveryScheduleOrderDetail->saveAll($order_detail);

					$total_order_detail_new[$dist_order_no][] = $order_detail;
					
					//update dist current invetory
					if($dist_stock_check && $result['status'] && $stock_deduction[$result['order_id']]==1)
					{
						$punits_pre = $this->search_array($product_id, 'id', $product_list);
						if($measurement_unit_id>0)
						{
							if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
								$base_quantity = ROUND($order_qty);
								$invoice_qty = ROUND($result['invoice_qty']);
							} else {
								$base_quantity = $this->unit_convert($product_id, $measurement_unit_id, $order_qty);
								$invoice_qty = $this->unit_convert($product_id, $measurement_unit_id, $result['invoice_qty']);
							}
						}
						else{
							if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
								$base_quantity = ROUND($order_qty);
								$invoice_qty = ROUND($result['invoice_qty']);
							} else {
								$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $order_qty);
								$invoice_qty = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $result['invoice_qty']);
							}
							
						}
						
						$update_type = 'deduct';
						$this->dist_update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 3, $result['order_date'], $invoice_qty);
						
						
						$this->dist_booking_update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 3, $order_date);
					}
					else
					{
						$schedule_order_status = 0;	
					}
				}
				
				
				//update dist_orders table
				if($schedule_order_status==0)
				{
					$this->DistOrder->id = $dist_order_id;
					if ($this->DistOrder->id) {
						$this->DistOrder->updateAll(
							array(
							'DistOrder.processing_status' => 3, 
							'DistOrder.status' => 1, 
							//'DistOrder.action' => 1
							),   //fields to update
							array('DistOrder.id' => $dist_order_id) //condition
						);
					}
				}
				//end update dist_orders table
				
				//insert into schedule order table
				$order_into = array();
				$order_into['DistOrderDeliveryScheduleOrder']['dist_order_delivery_schedule_id'] = $schedule_id;
				//$order_into['DistOrderDeliveryScheduleOrder']['dist_order_id'] 	= $order_id;
				$order_into['DistOrderDeliveryScheduleOrder']['dist_order_no'] 	= $dist_order_no;
				$order_into['DistOrderDeliveryScheduleOrder']['processing_status'] = $schedule_order_status;
				$this->DistOrderDeliveryScheduleOrder->saveAll($order_into);
			}
			
			//pr($total_order_detail);exit;
			
			
		
		}
		
		
		$f_order_datas = array();
		//pr($total_order_detail_new);
		foreach($total_order_detail_new as $key => $f_datas)
		{
			$order_status=1;
			foreach($f_datas as $f_data)
			{
				if($f_data['DistOrderDeliveryScheduleOrderDetail']['status']!=1)$order_status=0;
			
			}
			$f_order_datas[$key] = array(
				//'dist_order_id' => $f_data['DistOrderDeliveryScheduleOrderDetail']['dist_order_id'],
				'dist_order_no' => $key,
				'status' => $order_status,
			);
		}
		
		$res_data = array();
		foreach($f_order_datas as $r){
			$res_data[] = array(
				'dist_order_no' => $r['dist_order_no'],
				'status' =>$r['status'],
			);
		}
		
		$res = $res_data;
		
		$this->set(array(
            'schedule' => $res,
            '_serialize' => array('schedule')
        ));
	}
	
	
	private function dist_stock_check($store_id, $order_id)
    {
        $this->loadModel('DistCurrentInventory');
		$this->loadModel('DistOrderDetail');
		$this->loadModel('Product');
		$this->loadModel('ProductMeasurement');
		
		$order_datas=$this->DistOrderDetail->find('all',array(
                'conditions'=>array(
                'DistOrderDetail.dist_order_id'=>$order_id
                ),
                'fields'=>array('SUM(DistOrderDetail.sales_qty) as sales_qty', 'DistOrderDetail.product_id','DistOrderDetail.measurement_unit_id'),
                'group'=>array('DistOrderDetail.product_id, DistOrderDetail.measurement_unit_id'),
                'recursive'=>-1
            ));

		$order_detail=array();
        foreach($order_datas as $data)
        {               
            if($data['DistOrderDetail']['measurement_unit_id'])
            {
                $product_info = $this->Product->find('first',array(
                    'conditions'=>array(
                        'Product.id' => $data['DistOrderDetail']['product_id'],
                    ),
                    'joins'=>array(
                        array(
                            'table'=>'product_measurements',
                            'alias'=>'ProductMeasurement',
                            'conditions'=>'ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id AND Product.id=ProductMeasurement.product_id'
                            )
                        ),
                    'fields'=>array('ProductMeasurement.qty_in_base'),
                    'recursive'=>-1
                ));
                //pr($product_info);
                
                $pro_measurement_info = $this->ProductMeasurement->find('first',array(
                    'conditions'=>array(
                        'ProductMeasurement.product_id' => $data['DistOrderDetail']['product_id'],
                        'ProductMeasurement.measurement_unit_id' => $data['DistOrderDetail']['measurement_unit_id']
                    ),
                    'fields'=>array('ProductMeasurement.qty_in_base'),
                    'recursive'=>-1
                ));
                
                if($pro_measurement_info && $pro_measurement_info['ProductMeasurement']['qty_in_base'] && $product_info)
                {
                    $prev_qty=isset($order_detail[$data['DistOrderDetail']['product_id']])?$order_detail[$data['DistOrderDetail']['product_id']]:0;
                    $order_detail[$data['DistOrderDetail']['product_id']] = $prev_qty+($data['0']['sales_qty']*$pro_measurement_info['ProductMeasurement']['qty_in_base'])/$product_info['ProductMeasurement']['qty_in_base'];
                }
                elseif(empty($pro_measurement_info) && $product_info)
                {
                    $prev_qty=isset($order_detail[$data['DistOrderDetail']['product_id']])?$order_detail[$data['DistOrderDetail']['product_id']]:0;
                    $order_detail[$data['DistOrderDetail']['product_id']] = $prev_qty+($data['0']['sales_qty'])/$product_info['ProductMeasurement']['qty_in_base'];
                }
                else{
                    $prev_qty=isset($order_detail[$data['DistOrderDetail']['product_id']])?$order_detail[$data['DistOrderDetail']['product_id']]:0;
                    $order_detail[$data['DistOrderDetail']['product_id']] = $prev_qty+$data['0']['sales_qty'];
                }
            }
            else
            {
                $prev_qty=isset($order_detail[$data['DistOrderDetail']['product_id']])?$order_detail[$data['DistOrderDetail']['product_id']]:0;
                $order_detail[$data['DistOrderDetail']['product_id']] = $prev_qty+$data['0']['sales_qty'];
            }
        }
		foreach($order_detail as $product_id=>$qty)
		{
			//pr($result);
			/*$product_id = $result['DistOrderDetail']['product_id'];
			$qty = $result['0']['sales_qty'];
			*/
			
			$current_inventory=$this->DistCurrentInventory->find('all',array(
				'conditions'=>array(
					'DistCurrentInventory.store_id'=>$store_id,
					'DistCurrentInventory.product_id'=>$product_id),
				'joins'=>array(
					array(
						'table'=>'product_measurements',
						'alias'=>'ProductMeasurement',
						'type'=>'LEFT',
						'conditions'=>'ProductMeasurement.product_id=Product.id AND ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id'
						)
					),
				'group'=>array('DistCurrentInventory.store_id','ProductMeasurement.qty_in_base','DistCurrentInventory.product_id HAVING (sum(DistCurrentInventory.qty))  >= ROUND((case when ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end) *'.$qty.',0)'),
				'fields'=>array('DistCurrentInventory.store_id','DistCurrentInventory.product_id','sum(DistCurrentInventory.qty) as qty')
			));
			
			if(!$current_inventory)
			{
				return false;
			}
		}	
		
        return true;

    }
	
	public function dist_update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct', $transaction_type_id = 0, $transaction_date = '',$invoice_qty=0) {

        $this->loadModel('DistCurrentInventory');

        $find_type = 'all';
        if ($update_type == 'add')
            $find_type = 'first';

        $inventory_info = $this->DistCurrentInventory->find($find_type, array(
            'conditions' => array(
                //'CurrentInventory.qty >=' => 0,
                'DistCurrentInventory.store_id' => $store_id,
                'DistCurrentInventory.inventory_status_id' => 1,
                'DistCurrentInventory.product_id' => $product_id
            ),
            'order' => array('DistCurrentInventory.qty' => 'desc'),
            'recursive' => -1
        ));

		if ($update_type == 'deduct') {
			foreach ($inventory_info as $val) 
			{
				if($quantity <= 0)
				{
					break;
				}
				if ($quantity <= $val['DistCurrentInventory']['qty']) {
					$this->DistCurrentInventory->id = $val['DistCurrentInventory']['id'];
					$this->DistCurrentInventory->updateAll(
							array(
							'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' . $quantity,
							'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty + ' . $invoice_qty,
							'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
							'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
							'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
							), 
							array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
					);
					break;
				} else {

					if ($val['DistCurrentInventory']['id'] > 0) {

						$this->DistCurrentInventory->id = $val['DistCurrentInventory']['id'];
						if($val['DistCurrentInventory']['qty'] <= 0)
						{
							$this->DistCurrentInventory->updateAll(
									array(
									'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' .$quantity ,
									'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty + ' . $invoice_qty,
									'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
									'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
									'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
									), 
									array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
							);
							$quantity = 0;
							break;
						}
						else
						{
							$quantity = $quantity - $val['DistCurrentInventory']['qty'];
							$this->DistCurrentInventory->updateAll(
								array(
								'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' . $val['DistCurrentInventory']['qty'],
								'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty + ' . $invoice_qty,
								'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
								'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
								'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
								), 
								array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
							);
						}
					}
				}
			}
		} 
		else 
		{
			
			if (!empty($inventory_info)) {

				$this->DistCurrentInventory->updateAll(
						array(
							'DistCurrentInventory.qty' => 'DistCurrentInventory.qty + ' . $quantity,
							//'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
						 	'DistCurrentInventory.transaction_type_id' => $transaction_type_id, 
						 	'DistCurrentInventory.store_id' => $store_id, 
							'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
						), 
						array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
				);
				
				//for invoice qty
				if ($invoice_qty <= $inventory_info['DistCurrentInventory']['invoice_qty']) {
					$this->DistCurrentInventory->updateAll(
						array(
							//'DistCurrentInventory.qty' => 'DistCurrentInventory.qty + ' . $quantity,
							'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
						 	'DistCurrentInventory.transaction_type_id' => $transaction_type_id, 
						 	'DistCurrentInventory.store_id' => $store_id, 
							'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
						), 
						array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
					);
					
				}
				else
				{
					$this->DistCurrentInventory->updateAll(
						array(
							'DistCurrentInventory.invoice_qty' => 0,
						 	'DistCurrentInventory.transaction_type_id' => $transaction_type_id, 
						 	'DistCurrentInventory.store_id' => $store_id, 
							'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
						), 
						array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
					);
				}
				
				
			}
			
		}
		

        return true;
    }
	
	public function dist_order_delivery_unprocess(){
		$this->loadModel('DistOrder');
		$this->loadModel('DistOrderDetail');
		
		$this->loadModel('DistOrderDeliverySchedule');
		$this->loadModel('DistOrderDeliveryScheduleOrder');
		$this->loadModel('DistOrderDeliveryScheduleOrderDetail');
		
		$this->loadModel('SalesPerson');
		
        $json_data = $this->request->input('json_decode', true);	
		//pr($json_data);
		
        
        $json = $this->request->input();
        $all_inserted = true;
        $relation_array = array();
		
		//pr($json_data);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }

        $path = APP . 'logs/';
        $myfile = fopen($path . "dist_order_delivery_unprocess.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			'recursive' => -1
		));
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		
		$path = APP . 'logs/DBwiseLog/'.$distributor_id.'/'.date('Y-m-d').'/';
		if(!file_exists($path))
		{
			 mkdir($path, 0777, true);
		}
        $myfile = fopen($path . "dist_order_delivery_unprocess.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
		$this->loadModel('Product');
		$product_measurement_units = $this->Product->find('list', array('fields' => array('id', 'sales_measurement_unit_id')));
        $product_category_ids = $this->Product->find('list', array('fields' => array('id', 'product_category_id')));
		$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
		$product_list = Set::extract($products, '{n}.Product');
		
		$cancelled_order_list = $this->DistOrder->find('all', array(
			'conditions' => array(
				'DistOrder.dist_order_no' => $json_data['order_numbers'],
			),
			'order' => array('DistOrder.order_time' => 'asc'),
			'recursive' => 1
		));
		foreach($cancelled_order_list as $cancelled_orders)
		{
			$dist_order_no = $cancelled_orders['DistOrder']['dist_order_no'];
			$dist_order_id = $cancelled_orders['DistOrder']['id'];
			
			$previous_order_status = $this->DistOrder->find('first', array(
				'conditions' => array(
					'DistOrder.id' => $dist_order_id,
				),
				'order' => array('DistOrder.order_time' => 'asc'),
				'recursive' => 1
			));
			if($previous_order_status['DistOrder']['status']==1 && $previous_order_status['DistOrder']['processing_status']==0)
			{
				continue;
			}
				
			//update order table
			$this->DistOrder->id = $dist_order_id;
			if ($this->DistOrder->id) {
				$this->DistOrder->updateAll(
					array(
					'DistOrder.processing_status' => 0,
					'DistOrder.status' => 1, 
					//'DistOrder.action' => 1
					),   //fields to update
					array('DistOrder.id' => $dist_order_id) //condition
				);
			}

			$order_results = $this->DistOrderDeliveryScheduleOrderDetail->find('all', array(
				'conditions' => array(
					'DistOrderDeliveryScheduleOrderDetail.dist_order_no' => $dist_order_no,
				),
				'joins' => array(
					array(
						'alias' => 'DistOrder',
						'table' => 'dist_orders',
						'type' => 'INNER',
						'conditions' => 'DistOrderDeliveryScheduleOrderDetail.dist_order_id = DistOrder.id'
					)
				),
				'fields' => array('DistOrderDeliveryScheduleOrderDetail.*', 'DistOrder.order_date'),
				//'order' => array('DistOrder.order_time' => 'asc'),
				'recursive' => -1
			));
			
			$prev_DistOrderDeliveryScheduleOrder=$this->DistOrderDeliveryScheduleOrder->find('first',array(
				'conditions'=>array('DistOrderDeliveryScheduleOrder.dist_order_no' => $dist_order_no),
				'recursive'=>-1
				));
			
			$prev_processing_status=$prev_DistOrderDeliveryScheduleOrder['DistOrderDeliveryScheduleOrder']['processing_status']?$prev_DistOrderDeliveryScheduleOrder['DistOrderDeliveryScheduleOrder']['processing_status']:0;
				
			//update process order table
			$this->DistOrderDeliveryScheduleOrder->dist_order_no = $dist_order_no;
			if ($this->DistOrderDeliveryScheduleOrder->dist_order_no) {
				$this->DistOrderDeliveryScheduleOrder->updateAll(
					array('DistOrderDeliveryScheduleOrder.processing_status' => 0), 
					array('DistOrderDeliveryScheduleOrder.dist_order_no' => $dist_order_no) //condition
				);
			}
			//pr($order_results);
			
			$order_products_list = array();
			foreach($order_results as $key => $order_info)
			{
				
				$product_id = $order_info['DistOrderDeliveryScheduleOrderDetail']['product_id'];
				$order_qty = $order_info['DistOrderDeliveryScheduleOrderDetail']['order_qty'];
				$invoice_qty = $order_info['DistOrderDeliveryScheduleOrderDetail']['invoice_qty'];
				$order_date = $order_info['DistOrder']['order_date'];
				$is_bonus = $order_info['DistOrderDeliveryScheduleOrderDetail']['is_bonus'];
				$measurement_unit_id = $order_info['DistOrderDeliveryScheduleOrderDetail']['measurement_unit_id'];
				
				$punits_pre = $this->search_array($product_id, 'id', $product_list);
				if($measurement_unit_id>0)
				{
					if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
						$base_quantity = round($order_qty);
						$invoice_qty = round($invoice_qty);
					} else {
						$base_quantity = $this->unit_convert($product_id, $measurement_unit_id, $order_qty);
						$invoice_qty = $this->unit_convert($product_id, $measurement_unit_id, $invoice_qty);
					}
				}
				else
				{

					if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
						$base_quantity = round($order_qty);
						$invoice_qty = round($invoice_qty);
					} else {
						$base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $order_qty);
						$invoice_qty = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $invoice_qty);
					}
				}
				
				//echo $invoice_qty;

				$update_type = 'add';
				if($prev_processing_status== 1 || $prev_processing_status== 2)
				{
					$this->dist_update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 4, $order_date, $invoice_qty);
					$this->dist_booking_update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 4, $order_date, $booking_bonus_qty);
				}
				
			}
		}
		//exit;

				
		$res = array();
		$res['message'] = 'Unprocessed Successfully Complete.';
		$res['status'] = 1;
		
		
		$this->set(array(
            'schedule' => $res,
            '_serialize' => array('schedule')
        ));
	}
	/* ------------------- End Order Delivery Schedule ------------------ */
	
	public function dist_schedule_order_details(){

        $this->loadModel('SalesPerson');
		$this->loadModel('DistOrderDeliverySchedule');
		$this->loadModel('DistOrderDeliveryScheduleOrderDetail');
		
		$json_data = $this->request->input('json_decode', true);
        /*---------------------------- Mac check --------------------------------*/
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_schedule_order_details.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];

		
		$res_status = 1;
        //$seven_days_pre = date('Y-m-d 00:00:00', strtotime('-3 days'));
		
		$order_no = $json_data['order_number'];

		$conditions = array();

        $conditions = array('DistOrderDeliveryScheduleOrderDetail.dist_order_no' => $order_no);
 		
        $results = $this->DistOrderDeliveryScheduleOrderDetail->find('all', array(
            'fields' => array('DistOrderDeliveryScheduleOrderDetail.*', 'Product.name'),
            'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'Product',
					'table' => 'products',
					'type' => 'INNER',
					'conditions' => 'DistOrderDeliveryScheduleOrderDetail.product_id = Product.id'
				)
			),
			'order' => array('DistOrderDeliveryScheduleOrderDetail.dist_order_delivery_schedule_id' => 'desc'),
            'recursive' => -1
        ));
		
		//pr($results);exit;
		
        $schedule_order_details = array();
            

		foreach ($results as $result) {
		   $schedule_order_details[] = array(
		   	'product_name' 	=> $result['Product']['name'],
			'product_id' 	=> $result['DistOrderDeliveryScheduleOrderDetail']['product_id'],
			'order_qty' 	=> $result['DistOrderDeliveryScheduleOrderDetail']['order_qty'],
			'invoice_qty' 	=> $result['DistOrderDeliveryScheduleOrderDetail']['invoice_qty'],
			'status' 		=> $result['DistOrderDeliveryScheduleOrderDetail']['status']
		   );
		}

		
		$this->set(array(
            'schedule_order_details' => $schedule_order_details,
            '_serialize' => array('schedule_order_details')
        ));
	}
	
	
	/* ------------------- Start process_order_list (who are successfully processed) ------------------ */
	public function dist_process_order_list(){
		$this->loadModel('DistOrder');
		$this->loadModel('DistOrderDetail');
        $this->loadModel('SalesPerson');
		$this->loadModel('Product');
		
		$json_data = $this->request->input('json_decode', true);
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_process_order_list.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        /*---------------------------- Mac check --------------------------------*/
				
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];

		
		$res_status = 1;
        //$seven_days_pre = date('Y-m-d 00:00:00', strtotime('-3 days'));
		
		
		//$start_date = $json_data['start_date'];
		//$end_date = $json_data['end_date'];

		$conditions = array();

        $conditions = array(
		// 'DistOrder.sr_id' => $sr_id,
		'DistSrRouteMapping.dist_sr_id' => $sr_id,
		'DistSrRouteMapping.dist_distributor_id' => $distributor_id,
		'DistOrder.action' => 0,  
		'DistOrder.status' => 2,
		'DistOrder.processing_status' => 1
		);
		
		$order_date = isset($json_data['order_date'])?$json_data['order_date']:0;
		if($order_date)$conditions['DistOrder.order_date'] = $order_date;
		
		$outlet_id = isset($json_data['outlet_id'])?$json_data['outlet_id']:0;
		if($outlet_id)$conditions['DistOrder.outlet_id'] = $outlet_id;
		
		$market_id = isset($json_data['market_id'])?$json_data['market_id']:0;
		if($market_id)$conditions['DistOrder.market_id'] = $market_id;
		
		$route_id = isset($json_data['route_id'])?$json_data['route_id']:0;
		if($route_id)$conditions['DistOrder.dist_route_id'] = $route_id;
		
        $order_list = $this->DistOrder->find('all', array(
            'fields' => array('DistOrder.*', 'DistOutlet.name'),
            'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'DistOutlet',
					'table' => 'dist_outlets',
					'type' => 'INNER',
					'conditions' => 'DistOrder.outlet_id = DistOutlet.id'
				),
				array(
					'alias' => 'DistSrRouteMapping',
					'table' => 'dist_sr_route_mappings',
					'type' => 'INNER',
					'conditions' => 'DistOrder.dist_route_id = DistSrRouteMapping.dist_route_id'
				)
			),
            'recursive' => -1
        ));
		//pr($order_list); exit;

        $order_array = array();
		$mdata = array();
		
        foreach ($order_list as $m) 
		{
            $mdata['order_number'] 	= $m['DistOrder']['dist_order_no'];
			$mdata['outlet_id'] 	= $m['DistOrder']['outlet_id'];
			$mdata['outlet_name'] 	= $m['DistOutlet']['name'];
			$mdata['gross_value'] 	= $m['DistOrder']['gross_value'];
            $mdata['order_date'] 	= $m['DistOrder']['order_date'];
			
            $order_array[] = $mdata;
        }
		
		//pr($order_array);
		
		$this->set(array(
            'orders' => $order_array,
            '_serialize' => array('orders')
        ));
	}
	/* ------------------- End process_order_list (who are successfully processed) -------------------- */
	
	/* ------------------- Start process_order_list (who are successfully processed and waiting for delivery) ---------- */
	public function dist_process_order_list_for_delivery(){
		$this->loadModel('DistOrder');
		$this->loadModel('DistOrderDetail');
        $this->loadModel('SalesPerson');
		$this->loadModel('Product');
		$this->loadModel('DistOrderDeliveryScheduleOrder');
		$this->loadModel('DistOrderDeliveryScheduleOrderDetail');
		
		$json_data = $this->request->input('json_decode', true);
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_process_order_list_for_delivery.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];

        $conditions = array(
		// 'DistOrder.sr_id' => $sr_id,
		'DistSrRouteMapping.dist_sr_id' => $sr_id,
		'DistSrRouteMapping.dist_distributor_id' => $distributor_id,
		'DistOrder.action' => 0,
		'DistOrder.status' => 2,
		'DistOrder.processing_status' => 1
		);
		
		$route_id = isset($json_data['route_id'])?$json_data['route_id']:0;
		if($route_id)$conditions['DistOrder.dist_route_id'] = $route_id;
		
		$start_date = isset($json_data['start_date'])?$json_data['start_date']:0;
		if($start_date)$conditions['DistOrder.order_date <='] = $start_date;
		
		$end_date = isset($json_data['end_date'])?$json_data['end_date']:0;
		if($end_date)$conditions['DistOrder.order_date >='] = $end_date;
		
		$order_list = $this->DistOrder->find('all', array(
            'fields' => array('DistOrder.*'),
            'joins'=>array(
            	array(
					'alias' => 'DistSrRouteMapping',
					'table' => 'dist_sr_route_mappings',
					'type' => 'INNER',
					'conditions' => 'DistOrder.dist_route_id = DistSrRouteMapping.dist_route_id'
				)
            	),
            'conditions' => $conditions,
            'recursive' => 1
        ));
		//pr($order_list);exit;

        $order_array = array();
		$mdata = array();
        foreach ($order_list as $m) {
            $mdata['id'] = $m['DistOrder']['id'];
            $mdata['order_number'] = $m['DistOrder']['dist_order_no'];
            $mdata['order_date'] = $m['DistOrder']['order_date'];
			$mdata['order_date_time'] = $m['DistOrder']['order_time'];
            $mdata['sales_person_id'] = $m['DistOrder']['sr_id'];
            $mdata['outlet_id'] = $m['DistOrder']['outlet_id'];
            $mdata['market_id'] = $m['DistOrder']['market_id'];
            $mdata['territory_id'] = $m['DistOrder']['territory_id'];
            $mdata['gross_value'] = $m['DistOrder']['gross_value'];
			$mdata['total_vat'] = $m['DistOrder']['total_vat'];
			
            $mdata['adjustment_amount'] = $m['DistOrder']['adjustment_amount'];
            $mdata['adjustment_note'] = $m['DistOrder']['adjustment_note'];
            $mdata['cash_recieved'] = $m['DistOrder']['cash_recieved'];
            $mdata['credit_amount'] = $m['DistOrder']['credit_amount'];
            $mdata['latitude'] = $m['DistOrder']['latitude'];
            $mdata['longitude'] = $m['DistOrder']['longitude'];
            $mdata['from_app'] = $m['DistOrder']['from_app'];
			$mdata['status'] = $m['DistOrder']['processing_status'];
			$mdata['total_discount'] = $m['DistOrder']['total_discount'];
            if($m['DistOrder']['from_app']== 0 && $m['DistOrder']['action']==1)
            {
                $mdata['updated_at'] = date('Y-m-d H:i:s',strtotime($m['DistOrder']['order_date']));
            }
            else
            {
                $mdata['updated_at'] = $m['DistOrder']['order_time'];
            }
            $mdata['action'] = $m['DistOrder']['action'];
            $mdata['order_details'] = $m['DistOrderDetail'];
            $mm = 0;

            foreach ($m['DistOrderDetail'] as $each_order_details) {

                $product_id = $each_order_details['product_id'];
                $product_info = $this->Product->find('first', array(
                    'fields' => array('product_type_id'),
                    'conditions' => array('id' => $product_id),
                    'recursive' => -1
                ));

                $type = "";
                if ($product_info['Product']['product_type_id'] == 1 && $each_order_details['price'] > 0) {
                    $type = 0;
                } else if ($product_info['Product']['product_type_id'] == 3) {
                    $type = 1;
                } else if ($product_info['Product']['product_type_id'] == 1 && $each_order_details['price'] < 1) {
                    $type = 2;
                }

                $mdata['order_details'][$mm]['product_type'] = $type;
				$mdata['order_details'][$mm]['quantity'] = $each_order_details['sales_qty'];
				$mdata['order_details'][$mm]['price'] = $each_order_details['actual_price'];
				$mdata['order_details'][$mm]['order_number'] = $m['DistOrder']['dist_order_no'];

				
				
				//for invoice qty
				$product_id = $each_order_details['product_id'];
				$order_id = $m['DistOrder']['id'];
                $OrderDetail_info = $this->DistOrderDeliveryScheduleOrderDetail->find('first', array(
                    'fields' => array('invoice_qty'),
                    'conditions' => array('product_id' => $product_id,'dist_order_id' => $order_id),
                    'recursive' => -1
                ));
				//pr($OrderDetail_info);exit;
				$mdata['order_details'][$mm]['invoice_qty'] = $OrderDetail_info['DistOrderDeliveryScheduleOrderDetail']['invoice_qty'];
				
                $mm++;
            }


            $order_array[] = $mdata;

        }
				
		$this->set(array(
            'orders' => $order_array,
            '_serialize' => array('orders')
        ));
	}
	/* ------------------- End process_order_list (who are successfully processed and waiting for delivery) ------------ */
	
	
	//do later
	public function dist_order_schedule_list(){
				
		$this->loadModel('DistOrderDeliverySchedule');
		$this->loadModel('DistOrderDeliveryScheduleOrderDetail');
		
		$this->loadModel('SalesPerson');
		
		$json_data = $this->request->input('json_decode', true);
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_order_schedule_list.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        /*---------------------------- Mac check --------------------------------*/
				
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];

		
		$res_status = 1;
        //$seven_days_pre = date('Y-m-d 00:00:00', strtotime('-3 days'));
		
		//$order_date = isset($json_data['order_date'])?$json_data['order_date']:date('Y-m-d');
		$start_date = (isset($json_data['start_date']))?$json_data['start_date']:0;
		$end_date 	= (isset($json_data['end_date']))?$json_data['end_date']:0;

		$conditions = array();

        $conditions = array(
			'sr_id' => $sr_id, 
		);
		if($start_date)$conditions['Convert(Date, process_date_time) >='] = $start_date;
		if($end_date)$conditions['Convert(Date, process_date_time) <='] = $end_date;
        $schedule_list = $this->DistOrderDeliverySchedule->find('all', array(
            'fields' => array('DistOrderDeliverySchedule.*'),
            'conditions' => $conditions,
            'recursive' => -1
        ));

        $schedule_array = array();
		$s_data = array();
        foreach ($schedule_list as $sl) 
		{
            //$s_data['id'] = $m['DistOrder']['id'];
            $s_data['order_number'] = $sl['DistOrder']['dist_order_no'];
            $s_data['order_date'] = $sl['DistOrder']['order_date'];
			//$s_data['order_time'] = $m['DistOrder']['order_time'];
            $s_data['sales_person_id'] = $sl['DistOrder']['sr_id'];
            $s_data['outlet_id'] = $sl['DistOrder']['outlet_id'];			
            $schedule_array[] = $s_data;
        }
		
		//pr($order_array);
		
		$this->set(array(
            'schedule_list' => $schedule_array,
            '_serialize' => array('schedule_list')
        ));
	
	}
	
		
	/* ---------------- Dist SR Vist Plan ----------------- */
	public function dist_visit_plan_list() { 	
		$this->loadModel('DistSrVisitPlan');	
		$this->loadModel('DistSrVisitPlanDetail');	
		$this->loadModel('SalesPerson');	
		
		$json_data = $this->request->input('json_decode', true);
       
	    $path = APP . 'logs/';
        $myfile = fopen($path . "dist_visit_plan_list.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
	   
	    /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistSalesRepresentative.code','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		/*pr($so_info);
		exit;*/
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		$sr_code = $so_info['DistSalesRepresentative']['code'];


        $conditions = array(); 
		$conditions = array(
			'DistSrVisitPlan.code' => $sr_code, 
			'DistSrVisitPlan.distributor_id' => $distributor_id, 
			'DistSrVisitPlan.effective_date <=' => date('Y-m-d')
		);
		
		//pr($conditions);

        $plan_info = $this->DistSrVisitPlan->find('first', array(
            'conditions' => $conditions,
			'order' => array('id' => 'desc'),
 			'recursive' => -1
        ));
		$dist_sr_visit_plan_id = $plan_info['DistSrVisitPlan']['id'];
		
		$conditions = array(); 
		$conditions = array('DistSrVisitPlanDetail.dist_sr_visit_plan_id' => $dist_sr_visit_plan_id);
		$week_id = @$json_data['week_id'];
		if($week_id)$conditions['DistSrVisitPlanDetail.week_id'] = $week_id;
	
		$visit_results = $this->DistSrVisitPlanDetail->find('all', array(
            'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'DistRoute',
					'table' => 'dist_routes',
					'type' => 'INNER',
					'conditions' => 'DistSrVisitPlanDetail.route_id = DistRoute.id'
				),
			),
			'fields' => array('DistSrVisitPlanDetail.*', 'DistRoute.name'),
 			'recursive' => -1
        ));
		
		//pr($visit_results);exit;
		
        $visit_list = array();
        foreach($visit_results as $val){
			
			$visit_list[] = array(
				'plan_id' 			=> $val['DistSrVisitPlanDetail']['id'],
				'sat' 				=> $val['DistSrVisitPlanDetail']['sat'],
				'sun' 				=> $val['DistSrVisitPlanDetail']['sun'],
				'mon' 				=> $val['DistSrVisitPlanDetail']['mon'],
				'tue' 				=> $val['DistSrVisitPlanDetail']['tue'],
				'wed' 				=> $val['DistSrVisitPlanDetail']['wed'],
				'thu' 				=> $val['DistSrVisitPlanDetail']['thu'],
				'fri' 				=> $val['DistSrVisitPlanDetail']['fri'],
				'week_id' 			=> $val['DistSrVisitPlanDetail']['week_id'],
				'route_id' 			=> $val['DistSrVisitPlanDetail']['route_id'],
				'route_name' 		=> $val['DistRoute']['name'],
			);
		}
		//pr($visit_list);exit;
		
		$this->set(array(
            'visit_list' => $visit_list,
            '_serialize' => array('visit_list')
        ));
		
	}
	/* -------------- End Dist SR Vist Plan  -------------- */
	
	
	/* ---------------- Dist SR Create Plan Vist ----------------- */
    public function dist_create_visit_plan($OrderData=array(), $office_id=0, $distributor_id=0, $sr_id=0, $sr_code=0) 
	{
        $this->loadModel('DistSrVisitPlanList');
        
		//pr($OrderData);
		
		$plan_info = $this->DistSrVisitPlanList->find('first', array(
            'conditions' => array(
                'DistSrVisitPlanList.sr_code' => $sr_code,
				'DistSrVisitPlanList.dist_distributor_id' => $distributor_id,
                'DistSrVisitPlanList.dist_route_id' => $OrderData['route_id'],
                'DistSrVisitPlanList.visit_plan_date' => $OrderData['order_date'],
            ),
            'recursive' => -1
        ));
        
		$udata = array();
		$udata['aso_id'] 						= $office_id;
		$udata['sr_id'] 						= $sr_id;
		$udata['sr_code'] 						= $sr_code;
		$udata['dist_distributor_id'] 			= $distributor_id;
		$udata['dist_route_id'] 				= $OrderData['route_id'];
		
		$udata['dist_sr_visit_plan_detail_id'] 	= $OrderData['plan_id'];
		$udata['dist_outlet_id'] 				= $OrderData['outlet_id'];
		$udata['dist_market_id'] 				= $OrderData['market_id'];
		
		$udata['visit_plan_date'] 				= date('Y-m-d', strtotime($OrderData['order_date']));
		$udata['visited_date'] 					= date('Y-m-d', strtotime($OrderData['order_date']));
		$udata['is_out_of_plan'] 				= $OrderData['is_out_of_plan'];
		$udata['visit_status'] 					= 1;
		$udata['updated_at'] 					= $this->current_datetime();
		$udata['updated_by'] 					= $OrderData['sales_person_id'];
		
		if (empty($plan_info)) 
		{
			$udata['created_at'] = $this->current_datetime();
			$udata['created_by'] = $OrderData['sales_person_id'];
			$this->DistSrVisitPlanList->create();
		}
		else
		{
			$udata['id'] = $plan_info['DistSrVisitPlanList']['id'];
		}
		
		$this->DistSrVisitPlanList->save($udata);
        
    }
	/* -------------- End Dist SR Create Plan Vist -------------- */
	
	/* ---------------- Dist SR Out of Plan Vist ----------------- */
	public function dist_create_out_of_plan_visit() {
        $this->loadModel('DistSrVisitPlanList');	
		$this->loadModel('SalesPerson');	
		
		$json_data = $this->request->input('json_decode', true);
       
	    $path = APP . 'logs/';
        $myfile = fopen($path . "dist_create_out_of_plan_visit.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
	   
	    /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistSalesRepresentative.code','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		//pr($json_data);
		//exit;
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		$sr_code = $so_info['DistSalesRepresentative']['code'];
		
		
		$this->loadModel('DistSrVisitPlanList');
		
		if($json_data['route_id'])
		{
			$plan_info = $this->DistSrVisitPlanList->find('first', array(
				'conditions' => array(
					'DistSrVisitPlanList.sr_code' => $sr_code,
					'DistSrVisitPlanList.dist_distributor_id' => $distributor_id,
					'DistSrVisitPlanList.dist_route_id' => $json_data['route_id'],
					'DistSrVisitPlanList.visit_plan_date' => date('Y-m-d'),
				),
				'recursive' => -1
			));
			
			
			$udata = array();
			$udata['aso_id'] 						= $office_id;
			$udata['sr_id'] 						= $sr_id;
			$udata['sr_code'] 						= $sr_code;
			$udata['dist_distributor_id'] 			= $distributor_id;
			
			$udata['dist_route_id'] 				= $json_data['route_id'];
			$udata['dist_sr_visit_plan_detail_id'] 	= 0;
			
			$udata['dist_outlet_id'] 				= isset($json_data['outlet_id'])?$json_data['outlet_id']:0;
			$udata['dist_market_id'] 				= $json_data['market_id'];
			
			$udata['visit_plan_date'] 				= date('Y-m-d');
			$udata['visited_date'] 					= date('Y-m-d');
			$udata['is_out_of_plan'] 				= 1;
			$udata['visit_status'] 					= 1;
			
			$udata['remarks'] 						= $json_data['remarks'];
			$udata['updated_at'] 					= $this->current_datetime();
			$udata['updated_by'] 					= $json_data['sales_person_id'];
			
			
			//pr($udata);
			//exit;
			
			if (empty($plan_info)) 
			{
				
				$udata['created_at'] 					= $this->current_datetime();
				$udata['created_by'] 					= $json_data['sales_person_id'];
				$this->DistSrVisitPlanList->create();
				$res['message'] = 'Plan created successfully.';
				$res['status'] = 1;
			}
			else
			{
				
				$udata['id'] = $plan_info['DistSrVisitPlanList']['id'];
				$res['status'] = 1;
				$res['message'] = 'Plan updated successfully.';
			}
			
			$this->DistSrVisitPlanList->save($udata);
		}
		else
		{
			$res['status'] = 0;
			$res['message'] = 'No Data Provided.';
		}
		
		$this->set(array(
            'visit_plan' => $res,
            '_serialize' => array('visit_plan')
        ));
		
    }
	/* -------------- End Dist SR Out of Plan Vist -------------- */
	
	
	/* ---------------- Dist Stock Info ------------------- */
	public function dist_order_stock_info(){
				
		$this->loadModel('DistCurrentInventory');		
		
		
		$json_data = $this->request->input('json_decode', true);
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_order_stock_info.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$this->loadModel('SalesPerson');
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];

		
		$res_status = 1;
        //$seven_days_pre = date('Y-m-d 00:00:00', strtotime('-3 days'));
		
		$product_type_id = isset($json_data['product_type_id'])?$json_data['product_type_id']:0;
		$product_category_id = isset($json_data['product_category_id'])?$json_data['product_category_id']:0;

		$conditions = array();

        $conditions = array(
			'DistCurrentInventory.store_id' => $store_id, 
			'Product.is_distributor_product' => 1, 
		);
		if($product_type_id)$conditions['Product.product_type_id'] = $product_type_id;
		if($product_category_id)$conditions['Product.product_category_id'] = $product_category_id;
				
        $stock_info = $this->DistCurrentInventory->find('all', array(
            'fields' => array('DistCurrentInventory.*','Product.*'),
            'conditions' => $conditions,
            'order'=>array('Product.order'),
            'recursive' => 0
        ));

        $stock_info_array = array();
		$s_data = array();

		//pr($stock_info);exit;
		$this->LoadModel('DistOrder');
		$order_conditions=array(
			'DistOrder.distributor_id'=>$distributor_id,
			'DistOrder.processing_status'=>array(0,1),
			'DistOrder.status'=>array(1,2)
			);
        foreach ($stock_info as $val) 
		{
			$order_conditions['DistOrderDetail.product_id']=$val['Product']['id'];
			$order_info=$this->DistOrder->find('all',array(
				'conditions'=>$order_conditions,
				'joins'=>array(
					array(
						'table'=>'dist_order_details',
						'alias'=>'DistOrderDetail',
						'conditions'=> 'DistOrderDetail.dist_order_id=DistOrder.id'
						),
					array(
						'alias' => 'Product',
						'table' => 'products',
						'type' => 'INNER',
						'conditions' => 'DistOrderDetail.product_id = Product.id'
						),
					array(
						'alias' => 'ProductMeasurement',
						'table' => 'product_measurements',
						'type' => 'LEFT',
						'conditions' => 'Product.id = ProductMeasurement.product_id AND 
							CASE WHEN (DistOrderDetail.measurement_unit_id is null or DistOrderDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
							ELSE 
								DistOrderDetail.measurement_unit_id
							END =ProductMeasurement.measurement_unit_id'
						),
					),
				'fields'=>array(
					'SUM(
						(ROUND(
								(CASE WHEN DistOrder.processing_status=0 THEN DistOrderDetail.sales_qty END) * 
								(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
							,0))
					) AS booking_qty',
					'SUM(
						(ROUND(
								(CASE WHEN DistOrder.processing_status=1 THEN DistOrderDetail.sales_qty END) * 
								(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
							,0))
					) AS invoice_qty'
					),
				'recursive'=>-1
				));
            $s_data['product_name'] = $val['Product']['name'];
			$s_data['product_id'] = $val['Product']['id'];
			$s_data['unit'] = $val['Product']['sales_measurement_unit_id'];
			$s_data['inventory_id'] = $val['DistCurrentInventory']['id'];
			if($val['Product']['base_measurement_unit_id']==$val['Product']['sales_measurement_unit_id']){
				$s_data['quantity'] = $val['DistCurrentInventory']['qty'];
				$s_data['booking_quantity'] = $val['DistCurrentInventory']['booking_qty'];
				$s_data['invoice_qty'] = $val['DistCurrentInventory']['invoice_qty'];
				$s_data['actual_booking_quantity'] = isset($order_info['0']['0']['booking_qty'])?$order_info['0']['0']['booking_qty']:0;
				$s_data['actual_invoice_qty'] = isset($order_info['0']['0']['invoice_qty'])?$order_info['0']['0']['invoice_qty']:0;
				$s_data['bonus_quantity'] = $val['DistCurrentInventory']['bonus_qty'];
				$s_data['bonus_booking_quantity'] = $val['DistCurrentInventory']['bonus_booking_qty'];
			}else{
            	$s_data['quantity'] = $this->unit_convertfrombase($val['DistCurrentInventory']['product_id'], $val['Product']['sales_measurement_unit_id'], $val['DistCurrentInventory']['qty']);
				$s_data['booking_quantity'] = $this->unit_convertfrombase($val['DistCurrentInventory']['product_id'], $val['Product']['sales_measurement_unit_id'], $val['DistCurrentInventory']['booking_qty']);
				$s_data['invoice_qty'] = $this->unit_convertfrombase($val['DistCurrentInventory']['product_id'], $val['Product']['sales_measurement_unit_id'], $val['DistCurrentInventory']['invoice_qty']);
				$s_data['actual_booking_quantity'] = $this->unit_convertfrombase($val['DistCurrentInventory']['product_id'], $val['Product']['sales_measurement_unit_id'], (isset($order_info['0']['0']['booking_qty'])?$order_info['0']['0']['booking_qty']:0));
				$s_data['actual_invoice_qty'] = $this->unit_convertfrombase($val['DistCurrentInventory']['product_id'], $val['Product']['sales_measurement_unit_id'], (isset($order_info['0']['0']['invoice_qty'])?$order_info['0']['0']['invoice_qty']:0));
				$s_data['bonus_quantity'] = $this->unit_convertfrombase($val['DistCurrentInventory']['product_id'], $val['Product']['sales_measurement_unit_id'], $val['DistCurrentInventory']['bonus_qty']);
				$s_data['bonus_booking_quantity'] = $this->unit_convertfrombase($val['DistCurrentInventory']['product_id'], $val['Product']['sales_measurement_unit_id'], $val['DistCurrentInventory']['bonus_booking_qty']);
			}
            $stock_info_array[] = $s_data;
        }
		
		//pr($order_array);
		
		$this->set(array(
            'stock_info' => $stock_info_array,
            '_serialize' => array('stock_info')
        ));
	
	}
	/* ---------------- End Dist Stock Info --------------- */
	
	/* ---------------- Dist price list ------------------- */
	public function dist_price_list(){
		$this->loadModel('DistSrProductPrice');
		
		$json_data = $this->request->input('json_decode', true);
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_price_list.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$this->loadModel('SalesPerson');
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		
		$data_list = array();
		
		//start product / get_product_list	
        $this->loadModel('Product');
        
		//$last_update_date = ($json_data['product']['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['product']['last_update']));
        $conditions = array(); 
		$conditions['Product.is_distributor_product'] = 1;
        //if($last_update_date!='all')$conditions['Product.updated_at >'] = $last_update_date;
        $products = $this->Product->find('all', array(
            'fields' => array('Product.id', 'Product.name', 'Product.product_code', 'Product.product_category_id', 'Product.sales_measurement_unit_id', 'Product.product_type_id', 'Product.updated_at', 'Product.is_pharma', 'Product.order', 'Product.is_injectable'),
            'conditions' => $conditions,
            'order' => array('Product.order' => 'asc'),
            'recursive' => -1
        ));

        $this->loadModel('OpenCombinationProduct');
        $price_open = array();
        $bonus_open = array();

        foreach ($products as $key => $val) {
            //for price opne
            $price_open = $this->OpenCombinationProduct->find('first', array(
                'conditions' => array(
                    'OpenCombinationProduct.product_id' => $val['Product']['id'],
                    'OpenCombination.is_bonus' => 0,
					'OpenCombination.start_date <=' => $this->current_date(),
					'OpenCombination.end_date >=' =>  $this->current_date()	
                ),
            ));
            //pr($price_open);			

            if ($price_open) {
                $products[$key]['Product']['price_open_start'] = $price_open['OpenCombination']['start_date'];
                $products[$key]['Product']['price_open_end'] = $price_open['OpenCombination']['end_date'];
            } else {
                $products[$key]['Product']['price_open_start'] = '';
                $products[$key]['Product']['price_open_end'] = '';
            }


            //for bonus opne
            $bonus_open = $this->OpenCombinationProduct->find('first', array(
                'conditions' => array(
                    'OpenCombinationProduct.product_id' => $val['Product']['id'],
                    'OpenCombination.is_bonus' => 1,
					'OpenCombination.start_date <=' => $this->current_date(),
					'OpenCombination.end_date >=' =>  $this->current_date()	
                ),
            ));
            //pr($bonus_open);			

            if ($bonus_open) {
                $products[$key]['Product']['price_bonus_start'] = $bonus_open['OpenCombination']['start_date'];
                $products[$key]['Product']['price_bonus_end'] = $bonus_open['OpenCombination']['end_date'];
            } else {
                $products[$key]['Product']['price_bonus_start'] = '';
                $products[$key]['Product']['price_bonus_end'] = '';
            }

            $products[$key]['Product']['action'] = 1;
        }
		
		$data_list['product'] = array();
		foreach ($products as $val) {
			$data_list['product'][] = array(
				'product_id' 					=> $val['Product']['id'],
				'product_name' 					=> $val['Product']['name'],
				'product_category_id' 			=> $val['Product']['product_category_id'],
				'product_type_id' 				=> $val['Product']['product_type_id'],
				'product_order' 				=> $val['Product']['order'],
				'is_injectable' 				=> $val['Product']['is_injectable'],
			);
		}
		//end product / get_product_list
		
		
		//start product_history / get_product_wise_open_bonus_and_price_combination
		$this->loadModel('DistOpenCombinationProduct');
		$conditions = array('Product.is_distributor_product' => 1);
        $open_combination=$this->DistOpenCombinationProduct->find('all',array(
			'conditions' => $conditions,
			'order'=>array('DistOpenCombinationProduct.product_id'),
			'recursive' => 0
		));
        $data_list['product_history'] = array();
        foreach ($open_combination as $key => $val) {
            $data_list['product_history'][] = array(
				'is_bonus' 			=> $val['DistOpenCombination']['is_bonus'],
				'start_date' 		=> $val['DistOpenCombination']['start_date'],
				'end_date' 			=> $val['DistOpenCombination']['end_date'],
				'product_id' 		=> $val['DistOpenCombinationProduct']['product_id'],
				'combination_id' 	=> $val['DistOpenCombinationProduct']['combination_id'],
			);
        }
		//end product_history / get_product_wise_open_bonus_and_price_combination
		
		
		//start product_combinations / get_product_combination_list 	
		$this->loadModel('DistSrProductCombination');
		//$last_update_date = ($json_data['product_combinations']['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['product_combinations']['last_update']));
        $conditions = array(); 
        //if($last_update_date!='all')$conditions['DistSrProductCombination.updated_at >'] = $last_update_date;

        $results = $this->DistSrProductCombination->find('all', array(
            'conditions' => $conditions,
            'order' => array('DistSrProductCombination.updated_at' => 'asc'),
            'recursive' => -1
        ));		

		$data_list['product_combinations'] = array();
        foreach($results as $val){
			$data_list['product_combinations'][] = array(
				'product_id' 				=> $val['DistSrProductCombination']['product_id'],
				'combination_id'  			=> $val['DistSrProductCombination']['combination_id'],
				'min_quantity' 				=> $val['DistSrProductCombination']['min_qty'],
				'price' 					=> $val['DistSrProductCombination']['price'],
				'effective_date' 			=> $val['DistSrProductCombination']['effective_date'],
			);
		}
		//end product_combinations / get_product_combination_list
		

		
		
		//product price list
		$conditions = array(); 
		//$last_update_date = ($json_data['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['last_update']));
        //if($last_update_date!='all')$conditions['DistSrProductPrice.updated_at >'] = $last_update_date;
        $results = $this->DistSrProductPrice->find('all', array(
            'conditions' => $conditions,
			'order' => array('DistSrProductPrice.updated_at' => 'asc'),
            'recursive' => -1
        ));
		
        foreach($results as $val){
			$data_list['product_price'][] = array(
				'price_id' 					=> $val['DistSrProductPrice']['id'],
				'product_id' 				=> $val['DistSrProductPrice']['product_id'],
				'target_custommer' 			=> $val['DistSrProductPrice']['target_custommer'],
				'institute_id' 				=> $val['DistSrProductPrice']['institute_id'],
				'price' 					=> $val['DistSrProductPrice']['general_price'],
				'effective_date' 			=> $val['DistSrProductPrice']['effective_date'],
			);
		}
		
		
		/*$product_price = $this->DistSrProductPrice->find('all', array(
            'conditions' => $conditions,
            'order' => array('DistSrProductPrice.updated_at' => 'asc'),
            'recursive' => -1
        ));
		
		//pr($product_price);exit;
		
		
        foreach ($product_price as $key => $val) {
            $data_list['product_price'][$key]['DistSrProductPrice']['action'] = 1;
        }*/
		
		
		//bonus product list
		$this->loadModel('Bonus');
		//$last_update_date = ($json_data['bonuses']['last_update']=='all')?'all':date('Y-m-d H:i:s', strtotime($json_data['bonuses']['last_update']));
		$conditions = array();
        //if ($last_update_date != 'all')$conditions['Bonus.updated_at >'] = $last_update_date;
		
        $bonus_product = $this->Bonus->find('all', array(
            'conditions' => $conditions,
            'order' => array('Bonus.updated_at' => 'asc'),
            'recursive' => -1
        ));
		
		$data_list['bonuses'] = array();
        foreach ($bonus_product as $key => $val) {
             $data_list['bonuses'][]	= array(
			 	'mother_product_id'		=> $val['Bonus']['mother_product_id'],
			 	'mother_product_qty'	=> $val['Bonus']['mother_product_quantity'],
				'bonus_product_id'		=> $val['Bonus']['bonus_product_id'],
				'bonus_product_qty'		=> $val['Bonus']['bonus_product_quantity'],
				'effective_date'		=> $val['Bonus']['effective_date'],
				'end_date'				=> $val['Bonus']['end_date'],
				'updated_at'			=> $val['Bonus']['updated_at'],
			 );
        }
		//end bonus list
		
		//start discount percent for memo value	
		$this->loadModel('DistDiscount');
        $conditions = array(); 
		$conditions = array(
			'DistDiscount.is_active' => 1,
			'OR' => array(
				array('DistDiscount.office_id' => $office_id),
				array('DistDiscount.office_id' => NULL),
			)
		);
        $results = $this->DistDiscount->find('all', array(
            'conditions' => $conditions,
            'order' => array('DistDiscount.id' => 'desc'),
            //'recursive' => -1
        ));		
		
		
		$discounts['discounts'] = array();
        foreach($results as $val)
		{
			if($val['DistDiscount']['distributor_id']==NULL || $val['DistDiscount']['distributor_id']==$distributor_id)
			{
				foreach($val['DistDiscountDetail'] as $valD)
				{
					$data_list['discounts'][] = array(
						'discount_id' 			=> $valD['id'],
						'office_id' 			=> $val['DistDiscount']['office_id'],
						'distributor_id' 		=> $val['DistDiscount']['distributor_id'],
						'date_from' 			=> $valD['date_from'],
						'date_to' 				=> $valD['date_to'],
						'memo_value' 			=> $valD['memo_value'],
						'discount_percent' 		=> $valD['discount_percent'],
						'discount_type' 		=> $valD['discount_type'],
					);
				}
			}
		}
		//pr($discounts);exit;
		//end discount percent for memo value

        $this->set(array(
            'dist_product_price' => $data_list,
            '_serialize' => array('dist_product_price')
        ));
	}
	/* ---------------- End Dist price list ------------------ */
	
	
	/* ---------------- Start dist bonus party affiliation ------------------- */
	public function dist_bonus_party_affiliation()
	{
		$this->loadModel('DistOutlet');
		$this->loadModel('SalesPerson');
		
		$json_data = $this->request->input('json_decode', true);
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_bonus_party_affiliation.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//pr($json_data);exit;
        
		//selected_outlets
		$bonus_type_id = $json_data['details']['bonus_type_id'];
		$selected_outlets = $json_data['details']['selected_outlets'];
		$this->DistOutlet->updateAll(
			array(
			'DistOutlet.bonus_type_id' => $bonus_type_id, 
			),   //fields to update
			array('DistOutlet.id' => $selected_outlets) //condition
		);

		
		//uselected_outlets
		$bonus_type_id = 0;
		$unselected_outlets = $json_data['details']['unselected_outlets'];
		$this->DistOutlet->updateAll(
			array(
			'DistOutlet.bonus_type_id' => $bonus_type_id, 
			),   //fields to update
			array('DistOutlet.id' => $unselected_outlets) //condition
		);
	
		$res = array();
		$res['message'] = 'Add Successfully.';
		$res['status'] = 1;
		
		$this->set(array(
            'res' => $res,
            '_serialize' => array('res')
        ));
	}
	/* ---------------- End dist bonus party affiliation ------------------ */
	
	
	
		
	/* ---------------- Start dist product sales report ------------------- */
	public function dist_product_sales_report()
	{
		$this->loadModel('DistMemo');
		$this->loadModel('SalesPerson');
		
		$json_data = $this->request->input('json_decode', true);
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_product_sales_report.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		
		//pr($json_data);exit;
		
		$start_date = ($json_data['start_date'])?$json_data['start_date']:0;
		$end_date = ($json_data['end_date'])?$json_data['end_date']:0;
        
        
		$product_sale_results = array();
		
		$this->loadModel('Product');
		$products = $this->Product->find('list', array(
            'conditions' => array('Product.is_distributor_product' => 1),
            'order' => array('Product.order' => 'asc'),
        ));		
		foreach($products as $product_id => $product_name)
		{
			$conditions = array(
							'DistMemoDetail.product_id' => $product_id,
							'DistMemo.sr_id' => $sr_id,
							/*'DistMemoDetail.price >' => 0,*/
							'DistMemo.status' => 1
						);
						
			if($start_date)$conditions['DistMemo.memo_date >='] = $start_date;
			if($end_date)$conditions['DistMemo.memo_date <='] = $end_date;
			
			$product_total_sales = $this->DistMemo->find('all', array(
				'conditions'=> $conditions,
				'joins' => array(
					array(
						'alias' => 'DistMemoDetail',
						'table' => 'dist_memo_details',
						'type' => 'INNER',
						'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
					),
					array(
							'alias' => 'Product',
							'table' => 'products',
							'type' => 'INNER',
							'conditions' => 'DistMemoDetail.product_id = Product.id'
						),
					array(
						'alias' => 'ProductMeasurement',
						'table' => 'product_measurements',
						'type' => 'LEFT',
						'conditions' => 'Product.id = ProductMeasurement.product_id AND 
							CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
							ELSE 
								DistMemoDetail.measurement_unit_id
							END =ProductMeasurement.measurement_unit_id'
					),
					array(
						'alias' => 'ProductMeasurementSales',
						'table' => 'product_measurements',
						'type' => 'LEFT',
						'conditions' => '
							Product.id = ProductMeasurementSales.product_id 
							AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
					),
				),
				'fields'=> array(
					'sum(DistMemoDetail.price*DistMemoDetail.sales_qty) AS total_price',
					'sum(CASE WHEN DistMemoDetail.price > 0 THEN DistMemoDetail.sales_qty END) AS total_qty', 
					'SUM(
						ROUND((ROUND(
								(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
								(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
							,0)) / 
							(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
							,2,1)
					) AS bonus_qty', 
					'COUNT(distinct DistMemo.dist_order_no) AS total_ec'),
				//'group' => array('DistMemoDetail.product_id'),
				'recursive' => -1
				)
			);

			/*echo $this->DistMemo->getLastquery();
			pr($product_total_sales); exit;*/
			
			$product_total_oc = $this->DistMemo->find('all', array(
				'conditions'=> $conditions,
				'joins' => array(
						array(
							'alias' => 'DistMemoDetail',
							'table' => 'dist_memo_details',
							'type' => 'INNER',
							'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
						)
					),
				'fields' => 'COUNT(DISTINCT outlet_id) as total_oc',
				'recursive' => -1
				)
			);
			
			$product_sale_results[] = array(
				'product_name'		=>	$product_name,
				'total_qty'			=>	$product_total_sales[0][0]['total_qty']?$product_total_sales[0][0]['total_qty']:0,
				'bonus_qty'			=>	sprintf('%0.2f',$product_total_sales[0][0]['bonus_qty']?$product_total_sales[0][0]['bonus_qty']:0),
				'total_price'		=>	$product_total_sales[0][0]['total_price']?$product_total_sales[0][0]['total_price']:0,
				'total_ec'			=>	$product_total_sales[0][0]['total_ec']?$product_total_sales[0][0]['total_ec']:0,
				'total_oc'			=>	$product_total_oc[0][0]['total_oc']?$product_total_oc[0][0]['total_oc']:0
			);	
		}
		
		//pr($product_sale_results);
		//exit;
		
		$this->set(array(
            'product_sales_report' => $product_sale_results,
            '_serialize' => array('product_sales_report')
        ));
	}
	/* ---------------- End dist product sales report ------------------ */
	
	
	/* ---------------- Start dist product bonus report ------------------- */
	public function dist_product_bonus_report()
	{
		$this->loadModel('DistMemo');
		$this->loadModel('SalesPerson');
		
		$json_data = $this->request->input('json_decode', true);
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_product_bonus_report.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		
		//pr($json_data);exit;
		
		$start_date = ($json_data['start_date'])?$json_data['start_date']:0;
		$end_date = ($json_data['end_date'])?$json_data['end_date']:0;
        
        
		$product_bonus_results = array();
		
		$this->loadModel('Product');
		$products = $this->Product->find('list', array(
            'conditions' => array('Product.is_distributor_product' => 1),
            'order' => array('Product.order' => 'asc'),
        ));		
		foreach($products as $product_id => $product_name)
		{
			$conditions = array(
							'DistMemoDetail.product_id' => $product_id,
							'DistMemo.sr_id' => $sr_id,
							'DistMemoDetail.price <' => 1,
							'DistMemo.status' => 1
						);
						
			if($start_date)$conditions['DistMemo.memo_date >='] = $start_date;
			if($end_date)$conditions['DistMemo.memo_date <='] = $end_date;
			
			$product_total_sales = $this->DistMemo->find('all', array(
				'conditions'=> $conditions,
				'joins' => array(
					array(
						'alias' => 'DistMemoDetail',
						'table' => 'dist_memo_details',
						'type' => 'INNER',
						'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
					),
					array(
						'alias' => 'Product',
						'table' => 'products',
						'type' => 'INNER',
						'conditions' => 'DistMemoDetail.product_id = Product.id'
						),
					array(
						'alias' => 'ProductMeasurement',
						'table' => 'product_measurements',
						'type' => 'LEFT',
						'conditions' => 'Product.id = ProductMeasurement.product_id AND 
							CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
							ELSE 
								DistMemoDetail.measurement_unit_id
							END =ProductMeasurement.measurement_unit_id'
					),
					array(
						'alias' => 'ProductMeasurementSales',
						'table' => 'product_measurements',
						'type' => 'LEFT',
						'conditions' => '
							Product.id = ProductMeasurementSales.product_id 
							AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
					),
				),
				'fields'=> array('SUM(
										ROUND((ROUND(
												(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
												(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
											,0)) / 
											(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
											,2,1)
									) AS total_qty',
								'COUNT(DistMemo.dist_order_no) AS total_ec'),
				//'group' => array('DistMemoDetail.product_id'),
				'recursive' => -1
				)
			);
			//pr($product_total_sales); 
			
			$product_total_oc = $this->DistMemo->find('all', array(
				'conditions'=> $conditions,
				'joins' => array(
						array(
							'alias' => 'DistMemoDetail',
							'table' => 'dist_memo_details',
							'type' => 'INNER',
							'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
						)
					),
				'fields' => 'COUNT(DISTINCT outlet_id) as total_oc',
				'recursive' => -1
				)
			);
			
			$product_bonus_results[] = array(
				'product_name'		=>	$product_name,
				'total_qty'			=>	$product_total_sales[0][0]['total_qty']?sprintf("%0.2f",$product_total_sales[0][0]['total_qty']):0,
				'total_ec'			=>	$product_total_sales[0][0]['total_ec']?$product_total_sales[0][0]['total_ec']:0,
				'total_oc'			=>	$product_total_oc[0][0]['total_oc']?$product_total_oc[0][0]['total_oc']:0,

			);	
		}
		
		//pr($product_sale_results);
		//exit;
		
		$this->set(array(
            'product_bonus_report' => $product_bonus_results,
            '_serialize' => array('product_bonus_report')
        ));
	}
	/* ---------------- End dist product bonus report ------------------ */
	
	
	
	
	
	
	/* ---------------- Start dist product order report ------------------- */
	public function dist_product_order_report()
	{
		$this->loadModel('DistOrder');
		$this->loadModel('SalesPerson');
		
		$json_data = $this->request->input('json_decode', true);
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_product_order_report.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array(
				'DistSalesRepresentative.id',
				'DistSalesRepresentative.dist_distributor_id',
				),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				)
			),
			
			'recursive' => -1
		));
		
		$distributor_id = $so_info['DistSalesRepresentative']['dist_distributor_id'];
		$sr_id = $so_info['DistSalesRepresentative']['id'];
		
		//pr($json_data);exit;
		
		$start_date = ($json_data['start_date'])?$json_data['start_date']:0;
		$end_date = ($json_data['end_date'])?$json_data['end_date']:0;
        
        
		$product_results = array();
		
		
		$this->loadModel('Product');
		$product_id = ($json_data['product_id'])?$json_data['product_id']:0;
		$pro_con = array('Product.is_distributor_product' => 1);
		
		if($product_id)$pro_con['Product.id'] = $product_id;
		
		$products = $this->Product->find('list', array(
            'conditions' => $pro_con,
            'order' => array('Product.order' => 'asc'),
        ));
			
		foreach($products as $product_id => $product_name)
		{
			$conditions = array(
				'DistOrderDetail.product_id' => $product_id,
				'DistOrder.sr_id' => $sr_id,
				'DistOrderDetail.price >' => 0,
				'DistOrder.status' => array(1,2)
			);
						
			if($start_date)$conditions['DistOrder.order_date >='] = $start_date;
			if($end_date)$conditions['DistOrder.order_date <='] = $end_date;
			
			$product_total_sales = $this->DistOrder->find('all', array(
				'conditions'=> $conditions,
				'joins' => array(
					array(
						'alias' => 'DistOrderDetail',
						'table' => 'dist_order_details',
						'type' => 'INNER',
						'conditions' => 'DistOrder.id = DistOrderDetail.dist_order_id'
					)
				),
				'fields'=> array('sum(DistOrderDetail.price*DistOrderDetail.sales_qty) AS total_price','sum(DistOrderDetail.sales_qty) AS total_qty, count(DistOrder.dist_order_no) AS total_ec'),
				//'group' => array('DistOrderDetail.product_id'),
				'recursive' => -1
				)
			);
			
			$product_total_oc = $this->DistOrder->find('all', array(
				'conditions'=> $conditions,
				'joins' => array(
						array(
							'alias' => 'DistOrderDetail',
							'table' => 'dist_order_details',
							'type' => 'INNER',
							'conditions' => 'DistOrder.id = DistOrderDetail.dist_order_id'
						)
					),
				'fields' => 'COUNT(DISTINCT outlet_id) as total_oc',
				'recursive' => -1
				)
			);
			
			//for order number wise report
			//pr($conditions);
			$order_results = $this->DistOrder->find('all', array(
				'conditions'=> $conditions,
				'joins' => array(
						array(
							'alias' => 'DistOrderDetail',
							'table' => 'dist_order_details',
							'type' => 'INNER',
							'conditions' => 'DistOrder.id = DistOrderDetail.dist_order_id'
						),
						array(
							'alias' => 'DistOutlet',
							'table' => 'dist_outlets',
							'type' => 'INNER',
							'conditions' => 'DistOrder.outlet_id = DistOutlet.id'
						)
					),
				'fields' => 'DistOrder.dist_order_no, DistOrder.order_date, DistOrder.outlet_id, DistOutlet.name, DistOrderDetail.sales_qty',
				'group' => array('DistOrder.dist_order_no, DistOrder.order_date, DistOrder.outlet_id, DistOutlet.name,  DistOrderDetail.sales_qty'),
				'recursive' => -1
				)
			);
			//pr($order_results);
			
			$report_details = array();
			foreach($order_results as $order_result){
				$report_details[] = array(
					'dist_order_no' 	=> $order_result['DistOrder']['dist_order_no'],
					'order_date' 		=> $order_result['DistOrder']['order_date'],
					'outlet_name' 		=> $order_result['DistOutlet']['name'],
					'sales_qty' 		=> $order_result['DistOrderDetail']['sales_qty'],
				);
			}
			
			$product_results[] = array(
				'product_name'		=>	$product_name,
				'total_qty'			=>	$product_total_sales[0][0]['total_qty']?$product_total_sales[0][0]['total_qty']:0,
				'total_price'		=>	$product_total_sales[0][0]['total_price']?$product_total_sales[0][0]['total_price']:0,
				'total_ec'			=>	$product_total_sales[0][0]['total_ec']?$product_total_sales[0][0]['total_ec']:0,
				'total_oc'			=>	$product_total_oc[0][0]['total_oc']?$product_total_oc[0][0]['total_oc']:0,
				'report_details'	=>  $report_details
			);	
			
		}
		
		//pr($product_results);
		//exit;
		
		$this->set(array(
            'product_order_report' => $product_results,
            '_serialize' => array('product_order_report')
        ));
	}
	/* ---------------- End dist product order report --------------------- */
	
	
	
	
	/* ---------------- Start dist product order report ------------------- */
	public function dist_product_order_bonus_report()
	{
		$this->loadModel('DistOrder');
		$this->loadModel('SalesPerson');
		
		$json_data = $this->request->input('json_decode', true);
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_product_order_bonus_report.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		
		//pr($json_data);exit;
		
		$start_date = ($json_data['start_date'])?$json_data['start_date']:0;
		$end_date = ($json_data['end_date'])?$json_data['end_date']:0;
        
        
		$product_results = array();
		
		
		$this->loadModel('Product');
		$product_id = ($json_data['product_id'])?$json_data['product_id']:0;
		$pro_con = array('Product.is_distributor_product' => 1);
		
		if($product_id)$pro_con['Product.id'] = $product_id;
		
		$products = $this->Product->find('list', array(
            'conditions' => $pro_con,
            'order' => array('Product.order' => 'asc'),
        ));	
			
		foreach($products as $product_id => $product_name)
		{
			$conditions = array(
				'DistOrderDetail.product_id' => $product_id,
				'DistOrder.sr_id' => $sr_id,
				'DistOrderDetail.price <=' => 0,
				'DistOrder.status' => array(1,2)
			);
						
			if($start_date)$conditions['DistOrder.order_date >='] = $start_date;
			if($end_date)$conditions['DistOrder.order_date <='] = $end_date;
			
			$product_total_sales = $this->DistOrder->find('all', array(
				'conditions'=> $conditions,
				'joins' => array(
					array(
						'alias' => 'DistOrderDetail',
						'table' => 'dist_order_details',
						'type' => 'INNER',
						'conditions' => 'DistOrder.id = DistOrderDetail.dist_order_id'
					)
				),
				'fields'=> array('sum(DistOrderDetail.price*DistOrderDetail.sales_qty) AS total_price','sum(DistOrderDetail.sales_qty) AS total_qty, count(DistOrder.dist_order_no) AS total_ec'),
				//'group' => array('DistOrderDetail.product_id'),
				'recursive' => -1
				)
			);
			
			$product_total_oc = $this->DistOrder->find('all', array(
				'conditions'=> $conditions,
				'joins' => array(
						array(
							'alias' => 'DistOrderDetail',
							'table' => 'dist_order_details',
							'type' => 'INNER',
							'conditions' => 'DistOrder.id = DistOrderDetail.dist_order_id'
						)
					),
				'fields' => 'COUNT(DISTINCT outlet_id) as total_oc',
				'recursive' => -1
				)
			);
			
			//for order number wise report
			$order_results = $this->DistOrder->find('all', array(
				'conditions'=> $conditions,
				'joins' => array(
						array(
							'alias' => 'DistOrderDetail',
							'table' => 'dist_order_details',
							'type' => 'INNER',
							'conditions' => 'DistOrder.id = DistOrderDetail.dist_order_id'
						),
						array(
							'alias' => 'DistOutlet',
							'table' => 'dist_outlets',
							'type' => 'INNER',
							'conditions' => 'DistOrder.outlet_id = DistOutlet.id'
						)
					),
				'fields' => 'DistOrder.dist_order_no, DistOrder.order_date, DistOrder.outlet_id, DistOutlet.name, DistOrderDetail.sales_qty',
				//'group' => array('DistOrder.dist_order_no, DistOrder.order_date, DistOrder.outlet_id,DistOrderDetail.sales_qty'),
				'recursive' => -1
				)
			);
			//pr($order_results);
			
			$report_details = array();
			foreach($order_results as $order_result){
				$report_details[] = array(
					'dist_order_no' 	=> $order_result['DistOrder']['dist_order_no'],
					'order_date' 		=> $order_result['DistOrder']['order_date'],
					'outlet_name' 		=> $order_result['DistOutlet']['name'],
					'sales_qty' 		=> $order_result['DistOrderDetail']['sales_qty'],
				);
			}
			
			$product_results[] = array(
				'product_name'		=>	$product_name,
				'total_qty'			=>	$product_total_sales[0][0]['total_qty']?$product_total_sales[0][0]['total_qty']:0,
				'total_price'		=>	$product_total_sales[0][0]['total_price']?$product_total_sales[0][0]['total_price']:0,
				'total_ec'			=>	$product_total_sales[0][0]['total_ec']?$product_total_sales[0][0]['total_ec']:0,
				'total_oc'			=>	$product_total_oc[0][0]['total_oc']?$product_total_oc[0][0]['total_oc']:0,
				'report_details'	=>  $report_details
			);	
			
		}
		
		//pr($product_results);
		//exit;
		
		$this->set(array(
            'product_order_report' => $product_results,
            '_serialize' => array('product_order_report')
        ));
	}
	/* ---------------- End dist product order report --------------------- */
	
	
	/* ---------------- Start dist processed orders report ------------------- */
	public function dist_product_processed_order_report()
	{
		$this->loadModel('DistOrder');
		$this->loadModel('SalesPerson');
		
		$json_data = $this->request->input('json_decode', true);
		
		/*$path = APP . 'logs/';
        $myfile = fopen($path . "dist_product_order_summery_report.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);*/
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array(
				'DistSalesRepresentative.id',
				'DistSalesRepresentative.dist_distributor_id'
				),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				)
				
			),
			
			'recursive' => -1
		));
		
		
		$distributor_id = $so_info['DistSalesRepresentative']['dist_distributor_id'];
		$sr_id = $so_info['DistSalesRepresentative']['id'];
		
		//pr($json_data);exit;
		
		$start_date = ($json_data['start_date'])?$json_data['start_date']:0;
		$end_date = ($json_data['end_date'])?$json_data['end_date']:0;
        
        
		$product_results = array();
		
		
		$this->loadModel('Product');
		$product_id = isset($json_data['product_id'])?$json_data['product_id']:0;
		$pro_con = array('Product.is_distributor_product' => 1);
		
		if($product_id)@$pro_con['Product.id'] = $product_id;
		$pro_con['Product.product_type_id'] = 1;
		$products = $this->Product->find('list', array(
            'conditions' => $pro_con,
            'order' => array('Product.order' => 'asc'),
        ));	
		
			
		
		$conditions = array(
			/*'DistOrderDetail.product_id' => $product_id,*/
			'DistOrder.sr_id' => $sr_id,
			/*'DistOrderDetail.price >' => 0,*/
			'DistOrder.status' => array(1,2),
			'DistOrder.processing_status' => array(1)
		);
						
		if($start_date)$conditions['DistOrder.order_date >='] = $start_date;
		if($end_date)$conditions['DistOrder.order_date <='] = $end_date;
		
		$product_total_sales = $this->DistOrder->find('all', array(
			'conditions'=> $conditions,
			'joins' => array(
				array(
					'alias' => 'DistOrderDetail',
					'table' => 'dist_order_details',
					'type' => 'INNER',
					'conditions' => 'DistOrder.id = DistOrderDetail.dist_order_id'
				),
				array(
						'alias' => 'Product',
						'table' => 'products',
						'type' => 'INNER',
						'conditions' => 'DistOrderDetail.product_id = Product.id'
					),
				array(
					'alias' => 'ProductMeasurement',
					'table' => 'product_measurements',
					'type' => 'LEFT',
					'conditions' => 'Product.id = ProductMeasurement.product_id AND 
						CASE WHEN (DistOrderDetail.measurement_unit_id is null or DistOrderDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
						ELSE 
							DistOrderDetail.measurement_unit_id
						END =ProductMeasurement.measurement_unit_id'
				),
				array(
					'alias' => 'ProductMeasurementSales',
					'table' => 'product_measurements',
					'type' => 'LEFT',
					'conditions' => '
						Product.id = ProductMeasurementSales.product_id 
						AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
				),
			),
			'fields'=> array(
				'DistOrderDetail.product_id',
				'Product.name',
				'sum(DistOrderDetail.price*DistOrderDetail.sales_qty) AS total_price',
				'sum(CASE WHEN DistOrderDetail.price >0 THEN DistOrderDetail.sales_qty END) AS total_qty,
				SUM(
					ROUND((ROUND(
							(CASE WHEN DistOrderDetail.price=0 THEN DistOrderDetail.sales_qty END) * 
							(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
						,0)) / 
						(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
						,2,1)
				) AS bonus_qty,
				count(distinct DistOrder.dist_order_no) AS total_ec,COUNT(DISTINCT DistOrder.outlet_id) as total_oc'),
			'group' => array('DistOrderDetail.product_id','Product.name'),
			'recursive' => -1
			)
		);

		foreach($product_total_sales as $data)
		{
			$product_results[$data['DistOrderDetail']['product_id']] = array(
				'product_name'		=>	$data['Product']['name'],
				'total_qty'			=>	$data[0]['total_qty'],
				'bonus_qty'			=>	sprintf('%0.2f',$data[0]['bonus_qty']?$data[0]['bonus_qty']:0),
				'total_price'		=>	sprintf('%0.2f',$data[0]['total_price']?$data[0]['total_price']:0),
				'total_ec'			=>	$data[0]['total_ec']?$data[0]['total_ec']:0,
				'total_oc'			=>	$data[0]['total_oc']?$data[0]['total_oc']:0,
				/*'report_details'	=>	$report_details[$data['DistOrderDetail']['product_id']]*/
			);
		}
		$report_data=array();
		foreach($products as $product_id => $product_name)
		{
			$report_data[]=array(
				'product_name'		=>	$product_name,
				'total_qty'			=>	$product_results[$product_id]['total_qty']?$product_results[$product_id]['total_qty']:0,
				'bonus_qty'			=>	sprintf('%0.2f',$product_results[$product_id]['bonus_qty']?$product_results[$product_id]['bonus_qty']:0),
				'total_price'		=>	sprintf('%0.2f',$product_results[$product_id]['total_price']?$product_results[$product_id]['total_price']:0),
				'total_ec'			=>	$product_results[$product_id]['total_ec']?$product_results[$product_id]['total_ec']:0,
				'total_oc'			=>	$product_results[$product_id]['total_oc']?$product_results[$product_id]['total_oc']:0,
				
			);

		}
		$this->set(array(
            'product_sales_report' => $report_data,
            '_serialize' => array('product_sales_report')
        ));
	}
	/* ---------------- End dist processed orders report --------------------- */
	

	/* ---------------- Start dist pending orders report ------------------- */
	public function dist_product_order_summery_report()
	{
		$this->loadModel('DistOrder');
		$this->loadModel('SalesPerson');
		
		$json_data = $this->request->input('json_decode', true);
		
		/*$path = APP . 'logs/';
        $myfile = fopen($path . "dist_product_order_summery_report.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);*/
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array(
				'DistSalesRepresentative.id',
				'DistSalesRepresentative.dist_distributor_id'
				),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				)
				
			),
			
			'recursive' => -1
		));
		
		
		$distributor_id = $so_info['DistSalesRepresentative']['dist_distributor_id'];
		$sr_id = $so_info['DistSalesRepresentative']['id'];
		
		//pr($json_data);exit;
		
		$start_date = ($json_data['start_date'])?$json_data['start_date']:0;
		$end_date = ($json_data['end_date'])?$json_data['end_date']:0;
        
        
		$product_results = array();
		
		
		$this->loadModel('Product');
		$product_id = isset($json_data['product_id'])?$json_data['product_id']:0;
		$pro_con = array('Product.is_distributor_product' => 1);
		
		if($product_id)@$pro_con['Product.id'] = $product_id;
		$pro_con['Product.product_type_id'] = 1;
		$products = $this->Product->find('list', array(
            'conditions' => $pro_con,
            'order' => array('Product.order' => 'asc'),
        ));	
		
			
		
		$conditions = array(
			/*'DistOrderDetail.product_id' => $product_id,*/
			'DistOrder.sr_id' => $sr_id,
			/*'DistOrderDetail.price >' => 0,*/
			'DistOrder.status' => array(1),
			'DistOrder.processing_status' => array(0)
		);
						
		if($start_date)$conditions['DistOrder.order_date >='] = $start_date;
		if($end_date)$conditions['DistOrder.order_date <='] = $end_date;
		
		$product_total_sales = $this->DistOrder->find('all', array(
			'conditions'=> $conditions,
			'joins' => array(
				array(
					'alias' => 'DistOrderDetail',
					'table' => 'dist_order_details',
					'type' => 'INNER',
					'conditions' => 'DistOrder.id = DistOrderDetail.dist_order_id'
				),
				array(
						'alias' => 'Product',
						'table' => 'products',
						'type' => 'INNER',
						'conditions' => 'DistOrderDetail.product_id = Product.id'
					),
				array(
					'alias' => 'ProductMeasurement',
					'table' => 'product_measurements',
					'type' => 'LEFT',
					'conditions' => 'Product.id = ProductMeasurement.product_id AND 
						CASE WHEN (DistOrderDetail.measurement_unit_id is null or DistOrderDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
						ELSE 
							DistOrderDetail.measurement_unit_id
						END =ProductMeasurement.measurement_unit_id'
				),
				array(
					'alias' => 'ProductMeasurementSales',
					'table' => 'product_measurements',
					'type' => 'LEFT',
					'conditions' => '
						Product.id = ProductMeasurementSales.product_id 
						AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
				),
			),
			'fields'=> array(
				'DistOrderDetail.product_id',
				'Product.name',
				'sum(DistOrderDetail.price*DistOrderDetail.sales_qty) AS total_price',
				'sum(CASE WHEN DistOrderDetail.price >0 THEN DistOrderDetail.sales_qty END) AS total_qty,
				SUM(
					ROUND((ROUND(
							(CASE WHEN DistOrderDetail.price=0 THEN DistOrderDetail.sales_qty END) * 
							(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
						,0)) / 
						(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
						,2,1)
				) AS bonus_qty,
				count(distinct DistOrder.dist_order_no) AS total_ec,COUNT(DISTINCT DistOrder.outlet_id) as total_oc'),
			'group' => array('DistOrderDetail.product_id','Product.name'),
			'recursive' => -1
			)
		);

		foreach($product_total_sales as $data)
		{
			$product_results[$data['DistOrderDetail']['product_id']] = array(
				'product_name'		=>	$data['Product']['name'],
				'total_qty'			=>	$data[0]['total_qty'],
				'bonus_qty'			=>	sprintf('%0.2f',$data[0]['bonus_qty']?$data[0]['bonus_qty']:0),
				'total_price'		=>	sprintf('%0.2f',$data[0]['total_price']?$data[0]['total_price']:0),
				'total_ec'			=>	$data[0]['total_ec']?$data[0]['total_ec']:0,
				'total_oc'			=>	$data[0]['total_oc']?$data[0]['total_oc']:0,
				/*'report_details'	=>	$report_details[$data['DistOrderDetail']['product_id']]*/
			);
		}
		$report_data=array();
		foreach($products as $product_id => $product_name)
		{
			$report_data[]=array(
				'product_name'		=>	$product_name,
				'total_qty'			=>	$product_results[$product_id]['total_qty']?$product_results[$product_id]['total_qty']:0,
				'bonus_qty'			=>	sprintf('%0.2f',$product_results[$product_id]['bonus_qty']?$product_results[$product_id]['bonus_qty']:0),
				'total_price'		=>	sprintf('%0.2f',$product_results[$product_id]['total_price']?$product_results[$product_id]['total_price']:0),
				'total_ec'			=>	$product_results[$product_id]['total_ec']?$product_results[$product_id]['total_ec']:0,
				'total_oc'			=>	$product_results[$product_id]['total_oc']?$product_results[$product_id]['total_oc']:0,
				
			);

		}
		$this->set(array(
            'product_sales_report' => $report_data,
            '_serialize' => array('product_sales_report')
        ));
	}
	/* ---------------- End dist pending orders report --------------------- */
	
	/* ---------------- Start Dist SR Slaes Targets ------------------- */
	public function dist_sales_targets(){
				
		$this->loadModel('Product');		
		$this->loadModel('SalesPerson');
		
		$json_data = $this->request->input('json_decode', true);
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_order_stock_info.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistSalesRepresentative.code','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		$sr_code = $so_info['DistSalesRepresentative']['code'];

		
        $this->loadModel('Month');
		$this->Month->recursive = -1;
        $current_month_info = $this->Month->find('first', array(
            'fields' => array('id'),
            'conditions' => array('month' => $json_data['month'])
        ));
		$month_id = $current_month_info['Month']['id'];
		
		$fiscal_year_id = $json_data['fiscal_year_id'];
		$this->LoadModel('FiscalYear');
		$fiscal_year_info = $this->FiscalYear->find('first', array(
            'conditions' => array('FiscalYear.id' => $fiscal_year_id),
            'recursive'=>-1
        ));
		$fiscal_year_start_date=$fiscal_year_info['FiscalYear']['start_date'];
		$fiscal_year_end_date=$fiscal_year_info['FiscalYear']['end_date'];
		if($json_data['month'] >=date('m',strtotime($fiscal_year_start_date)) && $json_data['month'] <=12)
		{
			$month_start_date=date("Y-".$json_data['month']."-01",strtotime($fiscal_year_start_date));
		}
		else
		{
			$month_start_date=date("Y-".$json_data['month']."-01",strtotime($fiscal_year_end_date));
		}
		$month_end_date=date('Y-m-t',strtotime($month_start_date));
		$this->loadModel('DistSaleTargetMonth');  
		$condition = array(
                'DistSaleTargetMonth.fiscal_year_id' => $fiscal_year_id,
				'DistSaleTargetMonth.month_id' => $month_id,
                'DistSaleTargetMonth.target_type' => 2,
				'DistSaleTargetMonth.dist_sales_representative_code' => $sr_code,
				'DistSaleTargetMonth.aso_id' => $office_id,
            );
		//$this->DistSaleTargetMonth->recursive = -1;
		$sr_product_targets = $this->DistSaleTargetMonth->find('all', array(
            'conditions' => $condition,
          )
        );
		$this->loadModel('DistMemo');
		// pr($sr_product_targets);exit;
		
        $info_array = array();
		$s_data = array();
		$condition_memo = array(
				'DistSalesRepresentative.code' => $sr_code,
				'DistMemoDetail.price > '=>0,
				'DistMemo.memo_date BETWEEN ? AND ?'=>array($month_start_date,$month_end_date),
				'DistMemo.office_id'=>$office_id
            );
        foreach ($sr_product_targets as $val) 
		{
			$condition_memo['DistMemoDetail.product_id']=$val['Product']['id'];
			$memo_data=$this->DistMemo->find('all',array(
				'conditions'=>$condition_memo,
				'joins'=>array(
					array(
						'table'=>'dist_sales_representatives',
						'alias'=>'DistSalesRepresentative',
						'conditions'=>'DistSalesRepresentative.id=DistMemo.sr_id'
						),
					array(
						'table'=>'dist_memo_details',
						'alias'=>'DistMemoDetail',
						'conditions'=>'DistMemoDetail.dist_memo_id=DistMemo.id'
						),
					array(
						'alias' => 'Product',
						'table' => 'products',
						'type' => 'INNER',
						'conditions' => 'DistMemoDetail.product_id = Product.id'
						),
					array(
						'alias' => 'ProductMeasurement',
						'table' => 'product_measurements',
						'type' => 'LEFT',
						'conditions' => 'Product.id = ProductMeasurement.product_id AND 
							CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
							ELSE 
								DistMemoDetail.measurement_unit_id
							END =ProductMeasurement.measurement_unit_id'
						),
					),
				'fields'=>array(
					'SUM(
						(ROUND(
								DistMemoDetail.sales_qty * 
								(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
							,0))
					) AS achievement_qty',
					'sum(DistMemoDetail.sales_qty*DistMemoDetail.price) AS achievement_amount'
					),
				'recursive'=>-1
				));
            $s_data['product_name'] 	= $val['Product']['name'];
			$s_data['target_qty'] 		= $val['DistSaleTargetMonth']['target_quantity'];
			$s_data['achieve_qty'] 		= $memo_data['0']['0']['achievement_qty'];
			$s_data['target_amount'] 	= $val['DistSaleTargetMonth']['target_amount'];
			$s_data['achieve_amount'] 	= $memo_data['0']['0']['achievement_amount'];
            $info_array[] 		= $s_data;
        }
		
		//pr($order_array);
		
		$this->set(array(
            'product_targets' => $info_array,
            '_serialize' => array('product_targets')
        ));
	
	}
	/* ---------------- End Dist SR Slaes Target ---------------------- */
	
	
	
	/** Password changing for so:Start */
    public function dist_change_password() {
        $this->loadModel('Usermgmt.User');
		$this->loadModel('SalesPerson');
		
        $json_data = $this->request->input('json_decode', true);
		
		$path = APP . 'logs/';
        $myfile = fopen($path . "dist_change_password.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
		
		//user info
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistSalesRepresentative.code','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name','User.username'),
			'conditions' => array('SalesPerson.id' => $json_data['sales_person_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				),
				array(
					'alias' => 'User',
					'table' => 'users',
					'type' => 'INNER',
					'conditions' => 'User.sales_person_id = SalesPerson.id'
				)
			),
			
			'recursive' => -1
		));
		
		//pr($so_info);
		//exit;
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		$username = $so_info['User']['username'];

        $username = $username;
        $new_password = md5($json_data['new_password']);
        $old_password = md5($json_data['old_password']);
        
		$user_info = $this->User->find('first', array(
            'fields' => array('User.id', 'User.sales_person_id', 'User.username', 'SalesPerson.id', 'SalesPerson.name', 'SalesPerson.office_id', 'SalesPerson.territory_id', 'UserGroup.id', 'UserGroup.name'),
            'conditions' => array('User.username' => $username, 'User.password' => $old_password, 'User.active' => 1),
            'recursive' => 0
        ));

        if (!$user_info) {
            $res['status'] = 0;
            $res['message'] = 'User Or Old Password Not match';
        } else {
            $data['User']['id'] = $user_info['User']['id'];
            $data['User']['password'] = $new_password;
            if ($this->User->save($data)) {
                $res['status'] = 1;
                $res['message'] = 'Password Change successfuly';
            } else {
                $res['status'] = 0;
                $res['message'] = 'Please Try Again Later';
            }
        }
        $this->set(array(
            'response' => $res,
            '_serialize' => array('response')
        ));
    }

    /** Password changing for so:END */
	
	
	function search_array($value, $key, $array) {
        foreach ($array as $k => $val) {
            if ($val[$key] == $value) {
                return $array[$k];
            }
        }
        return null;
    }
	
	
	
	/* ------------------- Dist Memo PDF ---------------------- */
	public function dist_memo_create_pdf() {
        $this->loadModel('DistOrder');
		$this->loadModel('DistOrderDetail');
		
        $json_data = $this->request->input('json_decode', true);

        
        $json = $this->request->input();
        $all_inserted = true;
        $relation_array = array();

        /* removing unicode characters */
        $raw_json_data = str_replace("蹢", '"', $json);
        $json_data = json_decode($raw_json_data, TRUE);
		
		/*pr($json_data);
		exit;*/
		
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {

            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }

        $path = APP . 'logs/';
        $myfile = fopen($path . "dist_memo_create_pdf.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
        fclose($myfile);

		$data = array();
		
		
		//require_once(APP . 'Vendor' . DS . 'dompdf' . DS . 'dompdf_config.inc.php'); 
		//spl_autoload_register('DOMPDF_autoload'); 
		
		/*$dompdf = new DOMPDF(); 
		$dompdf->set_paper = 'A4';
		$dompdf->load_html(utf8_decode($content_for_layout), Configure::read('App.encoding'));
		$dompdf->render();*/
		
		$path =  getcwd().'/pdfs/';
		$name = time();
		$pdf = new DOMPDF;
		
		
		$conditions = array();
        
		$sr_id = $json_data['sales_person_id'];
 		
		//pr($conditions);
		if($json_data['order_numbers'])
		{
			$output = '';
				
			foreach($json_data['order_numbers'] as $val)
			{
				//echo $val.'<br>';
				$conditions = array(
					//'DistOrder.sr_id' => $sr_id, 
					'DistOrder.dist_order_no' => $val,
					//'DistOrder.status' => array(1,2),
				);
				
				$order_info = $this->DistOrder->find('first', array(
					'conditions' => $conditions,
					'recursive' => 0
				));
				//pr($order_info);
				
				$output.= "<table style='width:100%; font-size:11px; font-family:Gotham, Arial, sans-serif'; text-align:center;><tr>
						<td align='center'>
						Government of the People's Republic of Bangladesh. <br>           
						National Board of Revenue <br>
						Vat Challan Patra <br>           
						</td>
					</tr>
				</table>";
				
				$output.= "<p style='width:100%; font-size:11px; font-family:Gotham, Arial, sans-serif';>           
						SMC Enterprise Ltd. <br>
						33, Banani C/A, Dhaka-1213 <br>
						Central BIN:000049992-0101 <br>
						".$order_info['Office']['office_name'].",<br> 
						".$order_info['Office']['address']."<br>
						<b>".$order_info['Outlet']['name']."</b>
						".$order_info['Outlet']['address']."<br>";
						
				$output.= "Challan# : ".$order_info['DistOrder']['dist_order_no'].", ".date('d-M-Y', strtotime($order_info['DistOrder']['order_date']))."
						</p>";
					
				
						
				$output.= '<table border="1" cellspacing="0" cellspacing="0" style="width:100%; margin:10px 0 20px; font-size:10px; font-family:Gotham, Arial, sans-serif">
							<tbody>
								<tr>
									<th align="left">Product Name</th>
									<th align="right" class="text-center">Vat</th>
									<th align="right" class="text-center">Price</th>
									<th align="right">Sales Qty</th>
									<th align="right">Total Price</th>
								</tr>';
							
							$order_details = $this->DistOrderDetail->find('all', array(
								'conditions' => array('DistOrderDetail.dist_order_id' => $order_info['DistOrder']['id']),
								'order' => array('Product.order'=>'asc'),
								'recursive' => 0
								)
							);
							
							$bonus = '';
							$gift = '';
							$total_discount=0;
							$total_amonut=0;
							foreach($order_details as $val)
							{	
								if($val['DistOrderDetail']['price']>0)
								{
									$total = $val['DistOrderDetail']['sales_qty']*$val['DistOrderDetail']['actual_price'];
									$total_discount += $val['DistOrderDetail']['sales_qty']*($val['DistOrderDetail']['discount_amount']?$val['DistOrderDetail']['discount_amount']:0);
									$total_amonut+=$total;
									$output.= '<tr>
											<td align="left">'.$val['Product']['name'].'</td>
											<td align="right">'.sprintf('%.2f',$val['DistOrderDetail']['vat']).'</td>
											<td align="right">'.$val['DistOrderDetail']['actual_price'].'</td>
											<td align="right">'.$val['DistOrderDetail']['sales_qty'].'</td>
											<td align="right">'.sprintf('%.2f', $total).'</td>
										</tr>';
								}
								elseif($val['DistOrderDetail']['is_bonus'])
								{
									$bonus.= $val['Product']['name'].'('.$val['DistOrderDetail']['sales_qty'].$val['MeasurementUnit']['name'] .'), ';
								}
								elseif(!$val['DistOrderDetail']['is_bonus'] && !$val['DistOrderDetail']['actual_price']>0)
								{
									$gift.= $val['Product']['name'].'('.$val['DistOrderDetail']['sales_qty'].'), ';
								}
							}
							
						$output.= '<tr>
									<td colspan="4" align="right"><strong>Total Amount :</strong></td>
									<td align="right"><strong>'.sprintf('%.2f', $total_amonut).'</strong></td>
								</tr>';
						$output.= '<tr>
									<td colspan="4" align="right"><strong>Total Discount :</strong></td>
									<td align="right"><strong>'.sprintf('%.2f', $total_discount).'</strong></td>
								</tr>';
						$output.= '<tr>
									<td colspan="4" align="right"><strong>Total Vat :</strong></td>
									<td align="right"><strong>'.sprintf('%.2f', $order_info['DistOrder']['total_vat']).'</strong></td>
								</tr>';
						$output.= '</tbody>
						</table>';
				
				//$bonus = $bonus?$bonus:'Nill';
				
				//$gift = $gift?$gift:'Nill';
						
				if($bonus)$output.= '<p style="font-size:10px; margin:0px 0 2px; font-family:Gotham, Arial, sans-serif">Bonus: '.$bonus.'</p>';
				if($bonus)$output.= '<p style="font-size:10px; margin:0px 0 40px; font-family:Gotham, Arial, sans-serif">Gift: '.$gift.'</p>';
				/*$output.= '<p style="font-size:10px; margin:0px 0 40px; font-family:Gotham, Arial, sans-serif">
				Md. Mahafuzur Rahaman (Tangail)<br>
				Sales Officer,<br>
				* Product(s) price are  SD & including VAT
				</p>';*/
					
			}
			
			$name = 'pdf_'.time();
					$pdf->load_html($output);
					$pdf->render();
					$file = $pdf->output();
					$file_name = $name . '.pdf';
					file_put_contents($path.$file_name, $file);
					
			$res['status'] = 1;
			$res['link'] = BASE_URL.'/app/webroot/pdfs/'.$file_name;
            $res['message'] = 'PDF generate successfully.';
		}
		else 
		{
            $res['status'] = 0;
            $res['message'] = 'No Data Provided.';
        }
		//exit;
		
					

		
		//echo $dompdf->output();
		//exit;


        /*$path = APP . 'logs/';
        $myfile = fopen($path . "dist_create_outlet_response.txt", "a") or die("Unable to open file!");
        fwrite($myfile, "\n" . $this->current_datetime() . ':' . serialize($res));
        fclose($myfile);*/

        $this->set(array(
            'outlet' => $res,
            '_serialize' => array('outlet')
        ));
    }
	/* ------------------- End Dist Memo PDF ------------------ */



	/*---------------------------- update data push time -----------------------------*/
	/*
		This code added by Naser. For updating SR sync Time
	 */
	public function update_data_push_time() {
        $this->loadModel('SalesPerson');
        $this->loadModel('Usermgmt.User');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['sales_person_id']);
        if(!$mac_check)
        {

            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }

        $so_id = $json_data['sales_person_id'];

        //add new for version
        if ($json_data['version']) {
            $version = $json_data['version'];
        } else {
            $version = '';
        }
        $version_info = array();
        $c_date = date('Y-m-d');
        if ($version) {
            $sql = "SELECT TOP 1 * FROM app_versions WHERE name='$version' AND status=1 AND start_date<='$c_date' ORDER BY ID DESC";
            $version_info = $this->SalesPerson->query($sql);
        } else {
            $sql = "SELECT TOP 1 * FROM app_versions WHERE status=1 AND start_date>'$c_date' ORDER BY ID DESC";
            $version_info = $this->SalesPerson->query($sql);
        }
        //end for version

        if ($version_info) {
            //check user status
            $user_info = $this->User->find('first', array(
                'fields' => array('User.id', 'User.sales_person_id', 'User.username', 'SalesPerson.id', 'SalesPerson.name', 'SalesPerson.office_id', 'SalesPerson.territory_id', 'UserGroup.id', 'UserGroup.name'),
                'conditions' => array('SalesPerson.id' => $so_id, 'User.active' => 1),
                'recursive' => 0
            ));

            if ($user_info) {
                $data['last_data_push_time'] = $this->current_datetime();
                $this->SalesPerson->id = $json_data['sales_person_id'];
                $this->SalesPerson->save($data);

                $res['status'] = 1;
                $res['message'] = 'Success';
            } else {
                $res['status'] = 100;
                $res['message'] = 'User is inactive!';
            }
        } else {
            $res['status'] = 100;
            $res['message'] = 'Version does not match!';
        }


        $this->set(array(
            'response' => $res,
            '_serialize' => array('response')
        ));
    }
	/*---------------------------- update data push time -----------------------------*/
	
	
	//add new (25/07/2020)
	public function get_outlet_group() {
        $this->loadModel('OutletGroupToOutlet');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['so_id']);
        /*if(!$mac_check)
        {

            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }*/
        
		$conditions = array();
		$conditions =array('OutletGroupToOutlet.is_distributor' => 1);

        $outlet_group_list = $this->OutletGroupToOutlet->find('all', array(
            //'fields' => array('OutletCategory.id', 'OutletCategory.category_name', 'OutletCategory.updated_at'),
            'conditions' => $conditions,
            //'order' => array('OutletCategory.updated_at' => 'asc'),
            'recursive' => -1
        ));
		
		//pr($outlet_group_list);
		//exit;

        $data_array = array();
       /* foreach ($outlet_group_list as $key => $val) {
            $outlet_group_list[$key]['OutletGroupToOutlet']['action'] = 1;
        }*/

        $this->set(array(
            'outlet_group_list' => $outlet_group_list,
            '_serialize' => array('outlet_group_list')
        ));
    }
		
	public function get_policy_list() {
        $this->loadModel('GroupWiseDiscountBonusPolicy');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'], $json_data['so_id']);
        /*if(!$mac_check)
        {

            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }*/
        
			
		//user info
		$this->loadModel('SalesPerson');
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['so_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',

					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];


		$conditions =array('GroupWiseDiscountBonusPolicy.is_distributor' => 1,'Grp.office_id'=>$office_id);

        $policy_list = $this->GroupWiseDiscountBonusPolicy->find('all', array(
            //'fields' => array('OutletCategory.id', 'OutletCategory.category_name', 'OutletCategory.updated_at'),
            'conditions' => $conditions,
            'joins'=>array(
				array(
					'table'=>'group_wise_discount_bonus_policy_to_offices',
					'alias'=>'Grp',
					'conditions'=>'Grp.group_wise_discount_bonus_policy_id=GroupWiseDiscountBonusPolicy.id'
				)
			),
			
            'recursive' => 2
        ));
		
		//pr($outlet_group_list);
		//exit;

        $data_array = array();
       /* foreach ($outlet_group_list as $key => $val) {
            $outlet_group_list[$key]['OutletGroupToOutlet']['action'] = 1;
        }*/

        $this->set(array(
            'policy_list' => $policy_list,
            '_serialize' => array('policy_list')
        ));
    }
	
	public function get_product_units() {
        $this->loadModel('ProductMeasurement');
        $json_data = $this->request->input('json_decode', true);

        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['so_id']);
        if(!$mac_check)
        {

            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
        
        $last_update_date = $json_data['last_update_date'];
        $res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
        if ($res_status == 1) {
            $conditions = array();
        } else {
            $conditions = array('ProductMeasurement.updated_at >' => $last_update_date);
        }
		
        $unit_info = $this->ProductMeasurement->find('all', array(
           'fields' => array('ProductMeasurement.id', 'ProductMeasurement.measurement_unit_id', 'MeasurementUnit.name', 'ProductMeasurement.product_id', 'ProductMeasurement.qty_in_base', 'ProductMeasurement.updated_at'),
            'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'MeasurementUnit',
					'table' => 'measurement_units',
					'type' => 'INNER',
					'conditions' => 'ProductMeasurement.measurement_unit_id = MeasurementUnit.id'
				),
			),
            //'order' => array('ProductMeasurement.updated_at' => 'asc'),
            'recursive' => -1
        ));
		
		//pr($unit_info);
		
        foreach ($unit_info as $key => $val) {
            $unit_info[$key]['ProductMeasurement']['action'] = 1;
        }
        $this->set(array(
            'unit_info' => $unit_info,
            '_serialize' => array('unit_info')
        ));
    }
    /*
    	Route wise outlet visit report.
    	Api perameter : so_id,date from,date to,so_id {"so_id":"20550","date_from":"2020-08-01","date_to":"2020-09-01","mac":"860070044189068"}
    	Api Response : Route : total outlet,total visited outlet,total EC
     */
    public function get_route_wise_outlet_visit_report()
    {
    	$json_data = $this->request->input('json_decode', true);
				
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['so_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
        $this->loadModel('SalesPerson');
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','DistStore.id','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['so_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',

					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$store_id = $so_info['DistStore']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		$date_from = $json_data['date_from'];
		$date_to = $json_data['date_to'];

		$this->loadModel('DistSrRouteMapping');
		$sr_route=$this->DistSrRouteMapping->find('list',array(
			'conditions'=>array(
				'DistSrRouteMapping.dist_sr_id'=>$sr_id,
				'DistSrRouteMapping.dist_distributor_id'=>$distributor_id
				),
			'joins'=>array(
				array(
					'table'=>'dist_routes',
					'alias'=>'DistRoute',
					'conditions'=>'DistRoute.id=DistSrRouteMapping.dist_route_id'
					)
				),
			'fields'=>array('DistRoute.id','DistRoute.name'),
			'recursive'=>-1
			));
		$this->loadModel('DistOutlet');
		$total_outlet=$this->DistOutlet->find('all',array(
			'conditions'=>array(
				'DistOutlet.dist_route_id'=>array_keys($sr_route)
				),
			'fields'=>array('DistOutlet.dist_route_id','count(Distinct DistOutlet.id) as total_outlet'),
			'group'=>array('DistOutlet.dist_route_id'),
			'recursive'=>-1
			));
		$route_wise_outlet=array();
		foreach($total_outlet as $data)
		{
			$route_wise_outlet[$data['DistOutlet']['dist_route_id']]=$data['0']['total_outlet'];
		}
		$this->loadModel('DistOrder');
		$total_visited=$this->DistOrder->find('all',array(
			'conditions'=>array(
				'DistOutlet.dist_route_id'=>array_keys($sr_route),
				'DistOrder.order_date BETWEEN ? AND ?'=>array($date_from,$date_to),
				'DistOrder.status '=>array(1,2)
				),
			'joins'=>array(
				array(
					'table'=>'dist_outlets',
					'alias'=>'DistOutlet',
					'conditions'=>'DistOutlet.id=DistOrder.outlet_id'
					),
				),
			'fields'=>array('DistOutlet.dist_route_id','Count(Distinct DistOutlet.id) as total_visited'),
			'group'=>array('DistOutlet.dist_route_id'),
			'recursive'=>-1
			));
		$route_wise_visited_outlet=array();
		foreach($total_visited as $data)
		{
			$route_wise_visited_outlet[$data['DistOutlet']['dist_route_id']]=$data['0']['total_visited'];
		}

		$this->loadModel('DistMemo');
		$total_ec=$this->DistMemo->find('all',array(
			'conditions'=>array(
				'DistOutlet.dist_route_id'=>array_keys($sr_route),
				'DistMemo.memo_date BETWEEN ? AND ?'=>array($date_from,$date_to),
				'DistMemo.status >'=>0
				),
			'joins'=>array(
				array(
					'table'=>'dist_outlets',
					'alias'=>'DistOutlet',
					'conditions'=>'DistOutlet.id=DistMemo.outlet_id'
					),
				),
			'fields'=>array('DistOutlet.dist_route_id','Count(Distinct DistMemo.id) as total_ec'),
			'group'=>array('DistOutlet.dist_route_id'),
			'recursive'=>-1
			));
		

		$route_wise_ec=array();
		foreach($total_ec as $data)
		{
			$route_wise_ec[$data['DistOutlet']['dist_route_id']]=$data['0']['total_ec'];
		}

		$report_data=array();
		foreach($sr_route as $id=>$name)
		{
			$report_data[]=array(
				'route'=>$name,
				'total_outlet'=>isset($route_wise_outlet[$id])?$route_wise_outlet[$id]:0,
				'total_visited'=>isset($route_wise_visited_outlet[$id])?$route_wise_visited_outlet[$id]:0,
				'total_ec'=>isset($route_wise_ec[$id])?$route_wise_ec[$id]:0
				);
		}
		$this->set(array(
            'report_data' => $report_data,
            '_serialize' => array('report_data')
        ));

    }
     /*
    	SR check in out by this method. time will take server time.
    	Api perameter : so_id,login_type,macid
    	{"so_id":"20550","type":"0","mac":"860070044189068"}
     */
    function set_sr_check_in_out()
    {
    	$json_data = $this->request->input('json_decode', true);
				
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['so_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
        $this->loadModel('SalesPerson');
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['so_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',

					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];

		$this->loadModel('DistSrCheckInOut');
		$data['sr_id']=$sr_id;
		$data['db_id']=$distributor_id;
		$data['office_id']=$office_id;
		$type=$json_data['type'];
		$prev_data=$this->DistSrCheckInOut->find('first',array(
			'conditions'=>array(
				'DistSrCheckInOut.sr_id'=>$sr_id,
				'DistSrCheckInOut.date'=>date('Y-m-d')
				),
			'recursive'=>-1
			));
		if($type==0)
		{
			if($prev_data && $prev_data['DistSrCheckInOut']['check_in_time'])
			{
				$res['status']=0;
				$res['msg']='Already Checked in';
				$res['date']=date('d M Y',strtotime($prev_data['DistSrCheckInOut']['date']));
				$res['check_in_time']=date('h:i A',strtotime($prev_data['DistSrCheckInOut']['check_in_time']));
			}
			else
			{
				$this->DistSrCheckInOut->create();
				$data['check_in_time']=$this->current_datetime();
				$data['date']=$this->current_date();
				$this->DistSrCheckInOut->save($data);
				$res['status']=1;
				$res['msg']='Successfully Checked in';
				$res['date']=date('d M Y',strtotime($data['date']));
				$res['check_in_time']=date('h:i A',strtotime($data['check_in_time']));
			}
		}
		elseif($type==1)
		{
			/*if($prev_data && $prev_data['DistSrCheckInOut']['check_out_time'])
			{
				$res['status']=0;
				$res['msg']='Already Checked out';
				$res['date']=date('d M Y',strtotime($prev_data['DistSrCheckInOut']['date']));
				$res['check_in_time']=date('h:i A',strtotime($prev_data['DistSrCheckInOut']['check_in_time']));
				$res['check_out_time']=date('h:i A',strtotime($prev_data['DistSrCheckInOut']['check_out_time']));
			}
			else*/if(empty($prev_data)) 
			{
				$res['status']=0;
				$res['msg']='Please check in first';
			}
			else
			{
				$data['id']=$prev_data['DistSrCheckInOut']['id'];
				$data['check_out_time']=$this->current_datetime();
				$this->DistSrCheckInOut->save($data);
				$res['status']=1;
				$res['msg']='Successfully Checked out';
				$res['date']=date('d M Y',strtotime($prev_data['DistSrCheckInOut']['date']));
				$res['check_in_time']=date('h:i A',strtotime($prev_data['DistSrCheckInOut']['check_in_time']));
				$res['check_out_time']=date('h:i A',strtotime($data['check_out_time']));
			}
		}
		$this->set(array(
            'res' => $res,
            '_serialize' => array('res')
        ));

    }
     /*
    	SR check in out by this method. time will take server time.
    	Api perameter : so_id,macid
    	{"so_id":"20550","mac":"860070044189068"}
     */
    function get_sr_check_in_out()
    {
    	$json_data = $this->request->input('json_decode', true);
				
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['so_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
        $this->loadModel('SalesPerson');
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['so_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',

					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];

		$this->loadModel('DistSrCheckInOut');

		$prev_data=$this->DistSrCheckInOut->find('first',array(
			'conditions'=>array(
				'DistSrCheckInOut.sr_id'=>$sr_id,
				'DistSrCheckInOut.date'=>date('Y-m-d')
				),
			'recursive'=>-1
			));
		if($prev_data)
		{
			$res['status']=1;
			$res['date']=date('d M Y',strtotime($prev_data['DistSrCheckInOut']['date']));
			$res['check_in']=date('h:i A',strtotime($prev_data['DistSrCheckInOut']['check_in_time']));
			$res['check_out']=$prev_data['DistSrCheckInOut']['check_out_time']?date('h:i A',strtotime($prev_data['DistSrCheckInOut']['check_out_time'])):0;
		}
		else
		{
			$res['status']=0;
			$res['msg']='No Checked In/Out data';
			$res['date']=date('d M Y');
		}
		$this->set(array(
            'res' => $res,
            '_serialize' => array('res')
        ));

    }


     /*
    	SR check in out by this method. time will take server time.
    	Api perameter : so_id,macid
    	{"so_id":"20574","start_date":"2020-Oct-21","end_date":"2020-Oct-17","outlet_id":"332424","mac":"null"}
     */
    function dist_outlet_wise_sales_report()
    {
    	$json_data = $this->request->input('json_decode', true);
				
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['so_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
        $this->loadModel('SalesPerson');
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['so_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',

					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		$from_date=$json_data['start_date'];
		$to_date=$json_data['end_date'];
		$outlet_id=$json_data['outlet_id'];

		$this->LoadModel('DistMemo');

		$conditions=array(
			'DistMemo.outlet_id'=>$outlet_id,
			'DistMemo.memo_date BETWEEN ? AND ?'=>array(date('Y-m-d',strtotime($from_date)),date('Y-m-d',strtotime($to_date))),
			'DistMemo.status >'=>0
			);
		$memo_data=$this->DistMemo->find('all',array(
			'conditions'=>$conditions,
			'joins'=>array(
				array(
					'table'=>'dist_memo_details',
					'alias'=>'DistMemoDetail',
					'conditions'=>'DistMemoDetail.dist_memo_id=DistMemo.id'
					),
				array(
					'alias' => 'Product',
					'table' => 'products',
					'type' => 'INNER',
					'conditions' => 'DistMemoDetail.product_id = Product.id'
					),
				array(
					'alias' => 'ProductMeasurement',
					'table' => 'product_measurements',
					'type' => 'LEFT',
					'conditions' => 'Product.id = ProductMeasurement.product_id AND 
						CASE WHEN (DistMemoDetail.measurement_unit_id is null or DistMemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
						ELSE 
							DistMemoDetail.measurement_unit_id
						END =ProductMeasurement.measurement_unit_id'
					),
				array(
					'alias' => 'ProductMeasurementSales',
					'table' => 'product_measurements',
					'type' => 'LEFT',
					'conditions' => '
						Product.id = ProductMeasurementSales.product_id 
						AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
					),
				),
			'fields'=>array(
				'DistMemo.dist_memo_no',
				'DistMemo.id',
				'DistMemo.memo_date',
				'DistMemoDetail.product_id',
				'Product.name',
				'sum(CASE WHEN DistMemoDetail.price > 0 THEN sales_qty END) as sales_qty',
				'SUM(
						ROUND((ROUND(
								(CASE WHEN DistMemoDetail.price=0 THEN DistMemoDetail.sales_qty END) * 
								(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
							,0)) / 
							(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
							,2,1)
					) AS bonus_qty',
				'sum(DistMemoDetail.price*DistMemoDetail.sales_qty) AS total_value',
				),
			'group'=>array(
				'DistMemo.id',
				'DistMemo.dist_memo_no',
				'DistMemo.memo_date',
				'DistMemoDetail.product_id',
				'Product.name'
				),
			'recursive'=>'-1'
			));
		$memo_datas=array();
		foreach($memo_data as $data)
		{
			$memo_datas[$data['DistMemo']['id']]['DistMemo']=array(
				'dist_memo_no'=>$data['DistMemo']['dist_memo_no'],
				'memo_date'=>$data['DistMemo']['memo_date']
				);
			$memo_datas[$data['DistMemo']['id']]['DistMemoDetail'][]=array(
				'product_id'=>$data['DistMemoDetail']['product_id'],
				'sales_qty'=>isset($data['0']['sales_qty'])?sprintf("%0.2f",$data['0']['sales_qty']):0,
				'bonus_qty'=>isset($data['0']['bonus_qty']) ? sprintf("%0.2f",$data['0']['bonus_qty']):0,
				'total_value'=>isset($data['0']['total_value'])?sprintf("%0.2f",$data['0']['total_value']):0,
				'product_name'=>$data['Product']['name']
				);
			
		}
		$report_data=array();
		foreach($memo_datas as $data)
		{
			$memo['memo_no']=$data['DistMemo']['dist_memo_no'];
			$memo['memo_date']=$data['DistMemo']['memo_date'];
			$memo['details']=$data['DistMemoDetail'];
			
			$report_data[]=$memo;
		}
		$this->set(array(
            'res' => $report_data,
            '_serialize' => array('res')
        ));

    }
	public function giftitem_received() 
	{
    	$this->loadModel('Product');
    	$this->loadModel('DistGiftItem');
    	$this->loadModel('DistGiftItemDetail');
    	$this->loadModel('DistSalesRepresentative');

    	$json_data = $this->request->input('json_decode', true);

    	$path = APP . 'logs/';
    	$myfile = fopen($path . "dist_gift_item_received.txt", "a") or die("Unable to open file!");
    	fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
    	fclose($myfile);

    	/*---------------------------- Mac check --------------------------------*/
    	$mac_check=$this->mac_check($json_data['gift_issue']['mac'],$json_data['gift_issue']['so_id']);
    	if(!$mac_check)
    	{

    		$mac['status']=0;
    		$mac['message']='Mac Id Not Match';
    		$res=$mac;
    		$this->set(array(
    			'mac' => $res,
    			'_serialize' => array('mac')
    			));
    		return 0;
    	}
    	 $this->loadModel('SalesPerson');
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','Office.id','Office.office_name','DistStore.id'),
			'conditions' => array('SalesPerson.id' => $json_data['gift_issue']['so_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',

					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'DistStore',
					'table' => 'dist_stores',
					'type' => 'INNER',
					'conditions' => 'DistStore.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		$dist_store_id = $so_info['DistStore']['id'];
	

    	if (!empty($json_data)) {
    		$val=$json_data['gift_issue'];
        	unset($dist_gift_item);
            $dist_gift_item['distributor_id'] = $distributor_id;
            $dist_gift_item['sr_id'] = $sr_id;
            $dist_gift_item['gift_for'] = @$val['gift_for'];
            $dist_gift_item['outlet_id'] = $val['outlet_id'];
            $dist_gift_item['doctor_visit_id'] = @$val['doctor_visit_id'];
            $dist_gift_item['session_id'] = @$val['session_id'];
            $dist_gift_item['date'] = $val['gift_issue_date'];
            $dist_gift_item['remarks'] = $val['remarks'];
            $dist_gift_item['created_at'] = $this->current_datetime();
            $dist_gift_item['created_by'] = $val['so_id'];
            $dist_gift_item['updated_at'] = $this->current_datetime();
            $dist_gift_item['updated_by'] = $val['so_id'];

            if ($json_data['gift_issue_details']) {

            	$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
            	$product_list = Set::extract($products, '{n}.Product');
            	if(!is_numeric($val['temp_id']))
            	{
            		$prev_gift_item=$this->DistGiftItem->find('first',array(
            			'conditions'=>array(
            				'DistGiftItem.temp_id'=>$val['temp_id']
            				),
            			'recursive'=>1
            			));
            		if($prev_gift_item)
            		{

            			$prev_data = array();
            			foreach ( $prev_gift_item['DistGiftItemDetail'] as $element) 
            			{
            				$prev_data[$element['product_id']] = (isset($prev_data[$element['product_id']])?$prev_data[$element['product_id']]:0)+$element['quantity'];
            			}
            			$stock_checking_array = array();
            			foreach ($json_data['gift_issue_details']as $element) 
            			{
            				$stock_checking_array[$element['product_id']] = (isset($stock_checking_array[$element['product_id']])?$stock_checking_array[$element['product_id']]:0)+$element['quantity'];
            			}

            			$stock_okay=$this->stock_check_for_validation_for_other($dist_store_id,$stock_checking_array,$prev_data);

            			if(!$stock_okay)
            			{
            				$relation_array['previous_id'] = $val['temp_id'];
            				$relation_array['messege'] = "Stock Not Available";
            				$res['replaced_relation'][] = $relation_array;
							$res['status'] = 2; //status 2 for stock not avaible 
            				unset($relation_array);
            			}
            			else
            			{
            				/*-------------- previous stock update : start -------*/
            				foreach($prev_data as $product_id=>$qty)
            				{
            					$punits = $this->search_array($product_id, 'id', $product_list);
            					if ($punits['sales_measurement_unit_id'] == $punits['base_measurement_unit_id']) {
            						$sale_quantity = $qty;
            					} 
            					else {
            						$sale_quantity = $this->unit_convert($product_id, $punits['sales_measurement_unit_id'], $qty);
            					}
            					$this->dist_update_current_inventory($sale_quantity, $product_id,$dist_store_id, 'add', 4,$val['gift_issue_date'],0);
            				
            				}
            				$this->DistGiftItemDetail->deleteAll(array('DistGiftItemDetail.gift_item_id' => $prev_gift_item['DistGiftItem']['id']));
            				/*-------------- previous stock update : end -------*/
	            			$dist_gift_item['id'] =  $prev_gift_item['DistGiftItem']['id'];
	            			if ($this->DistGiftItem->save($dist_gift_item)) {
	            				unset($dist_gift_item);
	            				$data_array = array();
	            				foreach ($json_data['gift_issue_details'] as $dval) {
	            					$gift_item_details['gift_item_id'] = $prev_gift_item['DistGiftItem']['id'];
	            					$gift_item_details['product_id'] = $dval['product_id'];
	            					$gift_item_details['quantity'] = $dval['quantity'];
	            					$data_array[] = $gift_item_details;

	            					/*-------------- For Stock Update:Start --------------------------*/
	            					$punits = $this->search_array($dval['product_id'], 'id', $product_list);
	            					if ($punits['sales_measurement_unit_id'] == $punits['base_measurement_unit_id']) {
	            						$sale_quantity = $dval['quantity'];
	            					} 
	            					else {
	            						$sale_quantity = $this->unit_convert($dval['product_id'], $punits['sales_measurement_unit_id'], $dval['quantity']);
	            					}
	            					$this->dist_update_current_inventory($sale_quantity, $dval['product_id'],$dist_store_id, 'deduct', 3,$val['gift_issue_date'],0);
	            					/*-------------- For Stock Update:End ----------------------------*/
	            				}
	            				$this->DistGiftItemDetail->saveAll($data_array);
		            			$relation_array['new_id'] = $prev_gift_item['DistGiftItem']['id'];
		            			$relation_array['previous_id'] = $val['temp_id'];
		            			$res['replaced_relation'][] = $relation_array;
	            			}
	            			unset($dist_gift_item);
							$res['status'] = 1;
							$res['message'] = 'Gift item has been received successfuly.';
	            		}

            		}
            		else
            		{
            			$stock_checking_array = array();
            			foreach ($json_data['gift_issue_details']as $element) 
            			{
            				$stock_checking_array[$element['product_id']] = (isset($stock_checking_array[$element['product_id']])?$stock_checking_array[$element['product_id']]:0)+$element['quantity'];
            			}

            			$stock_okay=$this->stock_check_for_validation_for_other($dist_store_id,$stock_checking_array);

            			if(!$stock_okay)
            			{
            				$relation_array['previous_id'] = $val['temp_id'];
            				$relation_array['messege'] = "Stock Not Available";
            				$res['replaced_relation'][] = $relation_array;
							$res['status'] = 2; //status 2 for stock not avaible 
            				unset($relation_array);
            			}
            			else
            			{
	            			$dist_gift_item['temp_id'] = $val['temp_id'];
	            			$this->DistGiftItem->create();
	            			if ($this->DistGiftItem->save($dist_gift_item)) {
	            				unset($dist_gift_item);
	            				$data_array = array();
	            				foreach ($json_data['gift_issue_details'] as $dval) {
	            					$gift_item_details['gift_item_id'] = $this->DistGiftItem->id;
	            					$gift_item_details['product_id'] = $dval['product_id'];
	            					$gift_item_details['quantity'] = $dval['quantity'];
	            					$data_array[] = $gift_item_details;

	            					/*-------------- For Stock Update:Start --------------------------*/
	            					$punits = $this->search_array($dval['product_id'], 'id', $product_list);
	            					if ($punits['sales_measurement_unit_id'] == $punits['base_measurement_unit_id']) {
	            						$sale_quantity = $dval['quantity'];
	            					} 
	            					else {
	            						$sale_quantity = $this->unit_convert($dval['product_id'], $punits['sales_measurement_unit_id'], $dval['quantity']);
	            					}
	            					$this->dist_update_current_inventory($sale_quantity, $dval['product_id'],$dist_store_id, 'deduct', 3,$val['gift_issue_date'],0);
	            					/*-------------- For Stock Update:End ----------------------------*/
	            				}
	            				$this->DistGiftItemDetail->saveAll($data_array);
	            				$relation_array['new_id'] = $this->DistGiftItem->getLastInsertID();
	            				$relation_array['previous_id'] = $val['temp_id'];
	            				$res['replaced_relation'][] = $relation_array;
	            			}
							unset($dist_gift_item);
							$res['status'] = 1;
							$res['message'] = 'Gift item has been received successfuly.';
            			}
						 
            		}
					

            	}
            	else
            	{
            		$prev_gift_item=$this->DistGiftItem->find('first',array(
            			'conditions'=>array(
            				'DistGiftItem.id'=>$val['temp_id']
            				),
            			'recursive'=>1
            			));
            		if($prev_gift_item)
            		{

            			$prev_data = array();
            			foreach ( $prev_gift_item['DistGiftItemDetail'] as $element) 
            			{
            				$prev_data[$element['product_id']] = (isset($prev_data[$element['product_id']])?$prev_data[$element['product_id']]:0)+$element['quantity'];
            			}
            			$stock_checking_array = array();
            			foreach ($json_data['gift_issue_details']as $element) 
            			{
            				$stock_checking_array[$element['product_id']] = (isset($stock_checking_array[$element['product_id']])?$stock_checking_array[$element['product_id']]:0)+$element['quantity'];
            			}

            			$stock_okay=$this->stock_check_for_validation_for_other($dist_store_id,$stock_checking_array,$prev_data);

            			if(!$stock_okay)
            			{
            				$relation_array['previous_id'] = $val['temp_id'];
            				$relation_array['messege'] = "Stock Not Available";
            				$res['replaced_relation'][] = $relation_array;
							$res['status'] = 2; //status 2 for stock not avaible 
            				unset($relation_array);
            			}
            			else
            			{
            				/*-------------- previous stock update : start -------*/
            				foreach($prev_data as $product_id=>$qty)
            				{
            					$punits = $this->search_array($product_id, 'id', $product_list);
            					if ($punits['sales_measurement_unit_id'] == $punits['base_measurement_unit_id']) {
            						$sale_quantity = $qty;
            					} 
            					else {
            						$sale_quantity = $this->unit_convert($product_id, $punits['sales_measurement_unit_id'], $qty);
            					}
            					$this->dist_update_current_inventory($sale_quantity, $product_id,$dist_store_id, 'add', 4,$val['gift_issue_date'],0);
            				
            				}
            				$this->DistGiftItemDetail->deleteAll(array('DistGiftItemDetail.gift_item_id' => $prev_gift_item['DistGiftItem']['id']));
            				/*-------------- previous stock update : end -------*/
	            			$dist_gift_item['id'] =  $prev_gift_item['DistGiftItem']['id'];
	            			if ($this->DistGiftItem->save($dist_gift_item)) {
	            				unset($dist_gift_item);
	            				$data_array = array();
	            				foreach ($json_data['gift_issue_details'] as $dval) {
	            					$gift_item_details['gift_item_id'] = $prev_gift_item['DistGiftItem']['id'];
	            					$gift_item_details['product_id'] = $dval['product_id'];
	            					$gift_item_details['quantity'] = $dval['quantity'];
	            					$data_array[] = $gift_item_details;

	            					/*-------------- For Stock Update:Start --------------------------*/
	            					$punits = $this->search_array($dval['product_id'], 'id', $product_list);
	            					if ($punits['sales_measurement_unit_id'] == $punits['base_measurement_unit_id']) {
	            						$sale_quantity = $dval['quantity'];
	            					} 
	            					else {
	            						$sale_quantity = $this->unit_convert($dval['product_id'], $punits['sales_measurement_unit_id'], $dval['quantity']);
	            					}
	            					$this->dist_update_current_inventory($sale_quantity, $dval['product_id'],$dist_store_id, 'deduct', 3,$val['gift_issue_date'],0);
	            					/*-------------- For Stock Update:End ----------------------------*/
	            				}
	            				$this->DistGiftItemDetail->saveAll($data_array);
		            			$relation_array['new_id'] = $prev_gift_item['DistGiftItem']['id'];
		            			$relation_array['previous_id'] = $val['temp_id'];
		            			$res['replaced_relation'][] = $relation_array;
	            			}
	            			unset($dist_gift_item);
							$res['status'] = 1;
							$res['message'] = 'Gift item has been received successfuly.';
	            		}

            		}
            	}
            }
            
            
			} else {
        	$res['status'] = 0;
        	$res['message'] = 'No data found.';
        }

        $this->set(array(
        	'giftitem_received' => $res,
        	'_serialize' => array('giftitem_received')
        	));
    }
    /*
    	json = {"so_id":"20344","mac":"NULL","start_date":"2020-10-01","end_date":"2020-10-01","route_id":"","market_id":""}
     */
    function get_gift_item_list()
    {
    	$json_data = $this->request->input('json_decode', true);
				
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['so_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
        $this->loadModel('SalesPerson');
        $this->loadModel('DistGiftItem');
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['so_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',

					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		$from_date=date('Y-m-d',strtotime($json_data['start_date']));
		$to_date=date('Y-m-d',strtotime($json_data['end_date']));
		$market_id=$json_data['market_id'];
		$route_id=$json_data['route_id'];
		$conditions=array();
		$conditions['date BETWEEN ? AND ?']=array($from_date,$to_date);
		$conditions['DistGiftItem.distributor_id'] = $distributor_id;
		if($route_id)$conditions['DistMarket.dist_route_id'] = $route_id;
		if($market_id)$conditions['DistMarket.id'] = $market_id;
		$item_list=$this->DistGiftItem->find('all',array(
			'conditions'=>$conditions,
			'joins'=>array(
				array(
					'table'=>'dist_outlets',
					'alias'=>'DistOutlet',
					'conditions'=>'DistOutlet.id=DistGiftItem.outlet_id'
					),
				array(
					'table'=>'dist_markets',
					'alias'=>'DistMarket',
					'conditions'=>'DistMarket.id=DistOutlet.dist_market_id'
					),
				),
			'fields'=>array('DistGiftItem.id','DistGiftItem.date','DistOutlet.name','DistMarket.name'),
			'order'=>array('DistGiftItem.id desc'),
			'recursive'=>-1
		));
		$list_array=array();
		foreach($item_list as $data)
		{
			$list_array[]=array(
				'id'=>$data['DistGiftItem']['id'],
				'date'=>$data['DistGiftItem']['date'],
				'outlet'=>$data['DistOutlet']['name'],
				'market'=>$data['DistMarket']['name'],
				'is_editable'=> date('Y-m-d') <= date('Y-m-d',strtotime($data['DistGiftItem']['date'].'+5 day'))?1:0
				);
		}

        $this->set(array(
        	'giftitem_list' => $list_array,
        	'_serialize' => array('giftitem_list','max_update_day')
        	));
    }
    /*
    	json = {"so_id":"20344","mac":"NULL","item_id":"10"}
    */
    function get_gift_item_details()
    {
    	$json_data = $this->request->input('json_decode', true);
				
        /*---------------------------- Mac check --------------------------------*/
        $mac_check=$this->mac_check($json_data['mac'],$json_data['so_id']);
        if(!$mac_check)
        {
            $mac['status']=0;
            $mac['message']='Mac Id Not Match';
            $res=$mac;
            $this->set(array(
                'mac' => $res,
                '_serialize' => array('mac')
                ));
            return 0;
        }
        $this->loadModel('SalesPerson');
        $this->loadModel('DistGiftItem');
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['so_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',

					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		$item_id=$json_data['item_id'];
		$conditions=array();
		$conditions['DistGiftItem.id']=$item_id;
		$item=$this->DistGiftItem->find('first',array(
			'conditions'=>$conditions,
			
			'fields'=>array(
				'*',
				'(SELECT id FROM sales_people WHERE dist_sales_representative_id=DistGiftItem.sr_id) as so_id'
			)
		));
		$gift_items=array(
			'gift_issue'=>array(
				"so_id" 			=> $item['0']['so_id'],
			    "temp_id"			=> $item['DistGiftItem']['id'],
			    "outlet_id"			=> $item['DistGiftItem']['outlet_id'],
			    "gift_issue_id"		=> $item['DistGiftItem']['id'],
			    "gift_issued_by"	=> $item['0']['so_id'],
			    "remarks"			=> $item['DistGiftItem']['remarks'],
			    "gift_issue_date"	=> $item['DistGiftItem']['date'],
				),
				'gift_issue_details'=>$item['DistGiftItemDetail']
			);
		$this->set(array(
        	'details' => $gift_items,
        	'_serialize' => array('details')
        	));
    }
    function stock_check_for_validation_for_other($store_id,$details,$prev_data=array())
    {
        //pr($details);
		//pr($prev_data);exit;
        $this->loadModel('DistCurrentInventory');
        if($prev_data)
        {
            foreach($details as $product_id=>$qty)
            {
                if(isset($prev_data[$product_id]))
                {
                    $current_inventory=$this->DistCurrentInventory->find('all',array(
                        'conditions'=>array('DistCurrentInventory.store_id'=>$store_id,'DistCurrentInventory.product_id'=>$product_id),
                        'joins'=>array(
                            array(
                                'table'=>'product_measurements',
                                'alias'=>'ProductMeasurement',
                                'type'=>'LEFT',
                                'conditions'=>'ProductMeasurement.product_id=Product.id AND ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id'
                                )
                            ),
                        'group'=>array('DistCurrentInventory.store_id','ProductMeasurement.qty_in_base','DistCurrentInventory.product_id HAVING (sum(DistCurrentInventory.qty) + ROUND((case when ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end) *'.$prev_data[$product_id].',0))  >= ROUND((case when ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end) *'.$qty.',0)'),
                        'fields'=>array('DistCurrentInventory.store_id','DistCurrentInventory.product_id','sum(DistCurrentInventory.qty) as qty')
                        ));
                    if(!$current_inventory)
                    {
                        return false;
                    }
                }
                else
                {
                    $current_inventory=$this->DistCurrentInventory->find('all',array(
                        'conditions'=>array('DistCurrentInventory.store_id'=>$store_id,'DistCurrentInventory.product_id'=>$product_id),
                        'joins'=>array(
                            array(
                                'table'=>'product_measurements',
                                'alias'=>'ProductMeasurement',
                                'type'=>'LEFT',
                                'conditions'=>'ProductMeasurement.product_id=Product.id AND ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id'
                                )
                            ),
                        'group'=>array('DistCurrentInventory.store_id','ProductMeasurement.qty_in_base','DistCurrentInventory.product_id HAVING (sum(DistCurrentInventory.qty))  >= ROUND((case when ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end) *'.$qty.',0)'),
                        'fields'=>array('DistCurrentInventory.store_id','DistCurrentInventory.product_id','sum(DistCurrentInventory.qty) as qty')
                        ));
                    if(!$current_inventory)
                    {
                        return false;
                    }
                }
                
            }

        }
        else
        {
            foreach($details as $product_id=>$qty)
            {
                $current_inventory=$this->DistCurrentInventory->find('all',array(
                    'conditions'=>array('DistCurrentInventory.store_id'=>$store_id,'DistCurrentInventory.product_id'=>$product_id),
                    'joins'=>array(
                        array(
                            'table'=>'product_measurements',
                            'alias'=>'ProductMeasurement',
                            'type' =>'left',
                            'conditions'=>'ProductMeasurement.product_id=Product.id AND ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id'
                            )
                        ),
                    'group'=>array(
                    	'DistCurrentInventory.store_id',
                    	'ProductMeasurement.qty_in_base',
                    	'DistCurrentInventory.product_id 
                    		HAVING sum(DistCurrentInventory.qty)  >= ROUND((case when ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end) *'.$qty.',0)'),
                    'fields'=>array('DistCurrentInventory.store_id','DistCurrentInventory.product_id','sum(DistCurrentInventory.qty) as qty')
                    ));

                if(!$current_inventory)
                {
                    return false;
                }
            }
        }
        return true;
    }

    function get_bonus_cards_report()
    {
    	$json_data = $this->request->input('json_decode', true);

    	/*---------------------------- Mac check --------------------------------*/
    	$mac_check=$this->mac_check($json_data['mac'],$json_data['so_id']);
    	if(!$mac_check)
    	{

    		$mac['status']=0;
    		$mac['message']='Mac Id Not Match';
    		$res=$mac;
    		$this->set(array(
    			'mac' => $res,
    			'_serialize' => array('mac')
    			));
    		return 0;
    	}
    	

		$this->LoadModel('DistStoreBonusCard');
		$fiscal_year_id=$json_data['fiscal_year_id'];
		$route_id=isset($json_data['route_id'])?$json_data['route_id']:0;
		$market_id=isset($json_data['market_id'])?$json_data['market_id']:0;
		$bonus_type_id=isset($json_data['bonus_type_id'])?$json_data['bonus_type_id']:0;
		if($fiscal_year_id && $route_id)
		{
			$conditions=array();
			if($fiscal_year_id)
			{
				$conditions['DistStoreBonusCard.fiscal_year_id']=$fiscal_year_id;
			}
			if($route_id)
			{
				$conditions['DistMarket.dist_route_id']=$route_id;
			}

			if($bonus_type_id)
			{
				$conditions['DistStoreBonusCard.bonus_card_type_id']=$bonus_type_id;
			}
			if($market_id)
			{
				$conditions['DistMarket.id']=$market_id;
			}


			$bonus_data=$this->DistStoreBonusCard->find('all',array(
				'conditions'=>$conditions,
				'joins'=>array(
					array(
						'alias'=>'DistOutlet',
						'table'=>'dist_outlets',
						'conditions'=>'DistOutlet.id=DistStoreBonusCard.outlet_id',
						),
					array(
						'alias'=>'DistMarket',
						'table'=>'dist_markets',
						'conditions'=>'DistOutlet.dist_market_id=DistMarket.id',
						),
					array(
						'alias'=>'BonusCard',
						'table'=>'bonus_cards',
						'conditions'=>'BonusCard.id=DistStoreBonusCard.bonus_card_id',
						),
					array(
						'alias'=>'Product',
						'table'=>'products',
						'conditions'=>'Product.id=DistStoreBonusCard.product_id',
						),
					),
				'group'=>array(
					'DistOutlet.name',
					'Product.name',
					'BonusCard.min_qty_per_year',
					'DistStoreBonusCard.bonus_card_type_id'
					),
				'fields'=>array(
					'DistOutlet.name as outlet_name',
					'Product.name as product_name',
					'BonusCard.min_qty_per_year as target',
					'DistStoreBonusCard.bonus_card_type_id as bonus_type_id',
					'SUM(DistStoreBonusCard.quantity) AS achievement',
					'SUM(DistStoreBonusCard.no_of_stamp) AS stamp'
					),
				'recursive'=>-1
				));
			$report_data=array();
			foreach($bonus_data as $data)
			{
				$report_data[]=$data[0];
			}
			$res['status']=1;
			$res['report_data']=$report_data;
		}
		else
		{
			$res['status']=0;
			$res['msg']="Please Select Fiscal Year & Route First";
		}
	    $this->set(array(
	        	'report' => $res,
	        	'_serialize' => array('report')
	        	));
    }
     /*
    	SR Wise login report .
    	Api perameter : so_id,macid
    	{"so_id":"20550","date_from":"2020-10-01","date_to":"2020-10-17","mac":"null"}
     */
    function sr_login_report()
    {
    	$json_data = $this->request->input('json_decode', true);
    	/*---------------------------- Mac check --------------------------------*/
    	$mac_check=$this->mac_check($json_data['mac'],$json_data['so_id']);
    	if(!$mac_check)
    	{

    		$mac['status']=0;
    		$mac['message']='Mac Id Not Match';
    		$res=$mac;
    		$this->set(array(
    			'mac' => $res,
    			'_serialize' => array('mac')
    			));
    		return 0;
    	}
    	 $this->loadModel('SalesPerson');
		$so_info = $this->SalesPerson->find('first', array(
			'fields' => array('SalesPerson.name','SalesPerson.id','SalesPerson.dist_sales_representative_id','DistSalesRepresentative.id','DistSalesRepresentative.name','DistSalesRepresentative.dist_distributor_id','DistDistributor.id','DistDistributor.name','Office.id','Office.office_name'),
			'conditions' => array('SalesPerson.id' => $json_data['so_id']),
			'joins' => array(
				array(
					'alias' => 'DistSalesRepresentative',
					'table' => 'dist_sales_representatives',
					'type' => 'INNER',

					'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
				),
				array(
					'alias' => 'DistDistributor',
					'table' => 'dist_distributors',
					'type' => 'INNER',
					'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.office_id = Office.id'
				)
			),
			
			'recursive' => -1
		));
		$office_id = $so_info['Office']['id'];
		$distributor_id = $so_info['DistDistributor']['id'];
		$sr_id = $so_info['SalesPerson']['dist_sales_representative_id'];
		$date_from=date('Y-m-d',strtotime($json_data['date_from']));
		$date_to=date('Y-m-d',strtotime($json_data['date_to']));

		$this->LoadModel('DistSrCheckInOut');
		$login_report=$this->DistSrCheckInOut->find('all',array(
			'conditions'=>array(
				'DistSrCheckInOut.sr_id'=>$sr_id,
				'DistSrCheckInOut.date BETWEEN ? AND ?'=>array($date_from,$date_to)
				),
			'recursive'=>-1
			));
		$report_data=array();
		foreach($login_report as $data)
		{
			$report_data[]=array(
				'date'=>date('d M y',strtotime($data['DistSrCheckInOut']['date'])),
				'check_in_time'=>date('h:i a',strtotime($data['DistSrCheckInOut']['check_in_time'])),
				'check_out_time'=>$data['DistSrCheckInOut']['check_out_time']?date('h:i a',strtotime($data['DistSrCheckInOut']['check_out_time'])):"No Checkout Time",
				);
		}
		$this->set(array(
	        	'report' => $report_data,
	        	'_serialize' => array('report')
	        	));
    }

}
