<?php
App::uses('AppController', 'Controller');
/**
 * ProductSettings Controller
 *
 * @property GroupWiseDiscountBonusPolicie $GroupWiseDiscountBonusPolicie
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class GroupWiseDiscountBonusPoliciesController extends AppController {

/**
 * Components
 *
 * @var array
 */
 	public $uses = array('GroupWiseDiscountBonusPolicy','OutletGroup', 'Thana', 'Market', 'SalesPerson', 'Outlet', 'Product', 'GroupWiseDiscountBonusPolicyToOutlet', 'GroupWiseDiscountBonusPolicyToOutletGroup', 'GroupWiseDiscountBonusPolicyToOffice','GroupWiseDiscountBonusPolicyToOutletCategory','GroupWiseDiscountBonusPolicyOptionPriceSlab','GroupWiseDiscountBonusPolicyProduct','GroupWiseDiscountBonusPolicyOption','OutletCategory', 'GroupWiseDiscountBonusPolicyOptionBonusProduct');
	
	public $components = array('Paginator', 'Session','Filter.Filter');
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index($id = null) {
		
		
		
		$this->set('page_title', 'View List');
		$conditions = array();
		$this->paginate = array(	
			//'fields' => array('DISTINCT Combination.*'),		
			//'joins' => $joins,
			'conditions' => array(
				'GroupWiseDiscountBonusPolicy.is_distributor' => 0
			),
			'limit' => 100,
			//'order'=>   array('sort' => 'asc')
				'order' => array('GroupWiseDiscountBonusPolicy.id' => 'DESC')
		);
		//pr($this->paginate());
		//$this->set('product_id', $product_id);
		$this->set('results', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','View Details');
		if (!$this->GroupWiseDiscountBonusPolicy->exists($id)) {
			throw new NotFoundException(__('Invalid request!'));
		}
		$options = array('conditions' => array('GroupWiseDiscountBonusPolicy.' . $this->GroupWiseDiscountBonusPolicy->primaryKey => $id));
		$this->set('productCombination', $this->GroupWiseDiscountBonusPolicy->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() 
	{
		$this->set('page_title','Add New');
		
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		$this->set('office_parent_id', $office_parent_id);
        if ($office_parent_id == 0) {
            $office_conditions = array(
			'office_type_id' => 2,
			"NOT" => array( "id" => array(30, 31, 37))
			);
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }
		$this->set('office_id', $this->UserAuth->getOfficeId());
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		$this->set(compact('offices'));
		
		$outlet_categories = $this->OutletCategory->find('list',array('conditions'=>array('is_active'=>1)));
		$this->set(compact('outlet_categories'));
		
		$outlet_groups = array();
		$o_con =array('OutletGroup.is_distributor' => 0);
		$outlet_groups = $this->OutletGroup->find('list', array('conditions' => $o_con, 'order' => array('name' => 'asc')));
		//pr($outlet_groups);exit;
		
		$this->set(compact('outlet_groups'));

		$this->loadModel('Product');
		$products = $this->Product->find('list', array('order'=>array('Product.order' => 'ASC')));
		$this->html = '';
		foreach($products as $key=>$val)
		{
			$this->html .= '<option value="'.$key.'">'.addslashes($val).'</option>';		
		}
		$product_list = $this->html;
		$this->set(compact('products', 'product_list'));

		$policy_types = array(
			'0'=>'Only Discount',
			'1'=>'Only Bonus',
			'2'=>'Discount and Bonus',
			'3'=>'Discount or Bonus',
		);
		$this->set(compact('policy_types'));
		
		$disccount_types = array(
			'0'=>'%',
			'1'=>'Tk'
		);
		$this->set(compact('disccount_types'));
		
		
		if ($this->request->is('post')) {
			
			// pr($this->request->data);
			// exit;
			
			$data_policy = array();
			$data_policy['GroupWiseDiscountBonusPolicy']['start_date'] = date('Y-m-d', strtotime($this->request->data['GroupWiseDiscountBonusPolicy']['start_date']));
			$data_policy['GroupWiseDiscountBonusPolicy']['end_date'] = date('Y-m-d', strtotime($this->request->data['GroupWiseDiscountBonusPolicy']['end_date']));
			$data_policy['GroupWiseDiscountBonusPolicy']['name'] = $this->request->data['GroupWiseDiscountBonusPolicy']['name']; 
			$data_policy['GroupWiseDiscountBonusPolicy']['remarks'] = $this->request->data['GroupWiseDiscountBonusPolicy']['remarks']; 
			
			$data_policy['GroupWiseDiscountBonusPolicy']['created_at'] = $this->current_datetime(); 
			$data_policy['GroupWiseDiscountBonusPolicy']['updated_at'] = $this->current_datetime(); 
			$data_policy['GroupWiseDiscountBonusPolicy']['created_by'] = $this->UserAuth->getUserId();
			$data_policy['GroupWiseDiscountBonusPolicy']['updated_by'] = $this->UserAuth->getUserId();
			$data_policy['GroupWiseDiscountBonusPolicy']['is_distributor'] = 0;

			$this->GroupWiseDiscountBonusPolicy->create();
			if ($this->GroupWiseDiscountBonusPolicy->save($data_policy)) 
			{
				$policy_id = $this->GroupWiseDiscountBonusPolicy->getInsertID();
				
				//Offices Insert
				//pr($this->request->data['GroupWiseDiscountBonusPolicyToOffice']);
				//exit;
				
				if(!empty($this->request->data['GroupWiseDiscountBonusPolicyToOffice']))
				{
					$data_array = array();
					$data_array['GroupWiseDiscountBonusPolicyToOffice']['group_wise_discount_bonus_policy_id'] = $policy_id;
					$office_data = array();
					foreach($this->request->data['GroupWiseDiscountBonusPolicyToOffice']['office_id'] as $key => $val){
						if($val)$data_array['GroupWiseDiscountBonusPolicyToOffice']['office_id'] = $val;
						$office_data[] = $data_array;
					}
					/*pr($office_data);
					exit;*/
					$this->GroupWiseDiscountBonusPolicyToOffice->saveAll($office_data);
				}
				
				
				//Outlet Group Insert
				if(!empty($this->request->data['GroupWiseDiscountBonusPolicyToOutletGroup']))
				{
					$data_array = array();
					$data_array['GroupWiseDiscountBonusPolicyToOutletGroup']['group_wise_discount_bonus_policy_id'] = $policy_id;
					$outlet_group_data = array();
					foreach($this->request->data['GroupWiseDiscountBonusPolicyToOutletGroup']['outlet_group_id'] as $key => $val){
						if($val)$data_array['GroupWiseDiscountBonusPolicyToOutletGroup']['outlet_group_id'] = $val;
						$outlet_group_data[] = $data_array;
					}
					$this->GroupWiseDiscountBonusPolicyToOutletGroup->saveAll($outlet_group_data);
				}
				
				//Outlet Category Insert
				if(!empty($this->request->data['GroupWiseDiscountBonusPolicyToOutletCategory']))
				{
					$data_array = array();
					$data_array['GroupWiseDiscountBonusPolicyToOutletCategory']['group_wise_discount_bonus_policy_id'] = $policy_id;
					$outlet_group_data = array();
					foreach($this->request->data['GroupWiseDiscountBonusPolicyToOutletCategory']['outlet_category_id'] as $key => $val){
						if($val)$data_array['GroupWiseDiscountBonusPolicyToOutletCategory']['outlet_category_id'] = $val;
						$outlet_group_data[] = $data_array;
					}
					$this->GroupWiseDiscountBonusPolicyToOutletCategory->saveAll($outlet_group_data);
				}
				
				
				//Policy Product Insert
				if(!empty($this->request->data['GroupWiseDiscountBonusPolicyProduct']))
				{
					$data_array = array();
					$data_array['GroupWiseDiscountBonusPolicyProduct']['group_wise_discount_bonus_policy_id'] = $policy_id;
					$root_products = array();
					foreach($this->request->data['GroupWiseDiscountBonusPolicyProduct']['policy_product_id'] as $key => $val){
						if($val)$data_array['GroupWiseDiscountBonusPolicyProduct']['product_id'] = $val;
						$root_products[] = $data_array;
					}
					$this->GroupWiseDiscountBonusPolicyProduct->saveAll($root_products);
				}
				
				//Policy Option Insert
				if(!empty($this->request->data['GroupWiseDiscountBonusPolicyOption']))
				{
					$data_array = array();
					
					$option_data = array();
					foreach($this->request->data['GroupWiseDiscountBonusPolicyOption'] as $key => $val)
					{
						$data_array['GroupWiseDiscountBonusPolicyOption']['group_wise_discount_bonus_policy_id'] = $policy_id;
						$data_array['GroupWiseDiscountBonusPolicyOption']['policy_type'] = $val['policy_type'];
						$data_array['GroupWiseDiscountBonusPolicyOption']['min_qty'] = $val['min_qty'];
						//$data_array['GroupWiseDiscountBonusPolicyOption']['bonus_product_id']=$val['bonus_product_id'];
						//if(isset($val['measurement_unit_id']))$data_array['GroupWiseDiscountBonusPolicyOption']['measurement_unit_id']=$val['measurement_unit_id'];
						//$data_array['GroupWiseDiscountBonusPolicyOption']['bonus_qty'] = $val['bonus_qty'];
						$data_array['GroupWiseDiscountBonusPolicyOption']['discount_amount'] = $val['discount_amount'];
						$data_array['GroupWiseDiscountBonusPolicyOption']['disccount_type'] = $val['disccount_type'];
						//$option_data[] = $data_array;
						$this->GroupWiseDiscountBonusPolicyOption->create();
						
						//pr($data_array);
						
						if($this->GroupWiseDiscountBonusPolicyOption->save($data_array))
						{
							
							$policy_option_id = $this->GroupWiseDiscountBonusPolicyOption->getInsertID();
							
							//insert bonus products
							$b_data_array = array();
							$b_data_array2 = array();
							if($val['bonus_product_id'])
							{
								$m_unit_ids = $val['measurement_unit_id'];
								$bonus_qtys = $val['bonus_qty'];
								foreach($val['bonus_product_id'] as $b_key => $b_val)
								{
									if($b_val)
									{
										$b_data_array['GroupWiseDiscountBonusPolicyOptionBonusProduct']['group_wise_discount_bonus_policy_id'] = $policy_id;
										$b_data_array['GroupWiseDiscountBonusPolicyOptionBonusProduct']['group_wise_discount_bonus_policy_option_id'] = $policy_option_id;
										$b_data_array['GroupWiseDiscountBonusPolicyOptionBonusProduct']['bonus_product_id']=$b_val;
										$b_data_array['GroupWiseDiscountBonusPolicyOptionBonusProduct']['bonus_qty']=$bonus_qtys[$b_key];
										$b_data_array['GroupWiseDiscountBonusPolicyOptionBonusProduct']['measurement_unit_id']=$m_unit_ids[$b_key];
										$b_data_array['GroupWiseDiscountBonusPolicyOptionBonusProduct']['relation']=0;
										$b_data_array2[] = $b_data_array;
									}
									
								}
								$this->GroupWiseDiscountBonusPolicyOptionBonusProduct->saveAll($b_data_array2);
							}
							
							
							//insert price slab
							$data_array = array();
							$price_slab = array();
							//pr($key);
							
							if(@$this->request->data['GroupWiseDiscountBonusPolicyOptionPriceSlab'][$key]['slab_id'])
							{
								foreach($this->request->data['GroupWiseDiscountBonusPolicyOptionPriceSlab'][$key]['slab_id'] as $key2 => $val)
								{
									//if($val){
									$data_array['GroupWiseDiscountBonusPolicyOptionPriceSlab']['group_wise_discount_bonus_policy_id'] = $policy_id;
									$data_array['GroupWiseDiscountBonusPolicyOptionPriceSlab']['group_wise_discount_bonus_policy_option_id'] = $policy_option_id;
							
									$data_array['GroupWiseDiscountBonusPolicyOptionPriceSlab']['discount_product_id'] = $this->request->data['GroupWiseDiscountBonusPolicyOptionPriceSlab'][$key]['discount_product_id'][$key2];
									$data_array['GroupWiseDiscountBonusPolicyOptionPriceSlab']['slab_id'] = $val;
									$price_slab[] = $data_array;
									//}
								}
								$this->GroupWiseDiscountBonusPolicyOptionPriceSlab->saveAll($price_slab);
							}
							
						}
					}
				}
				
				$this->Session->setFlash(__('The data has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
				exit;
			}
			
			
		}
		
		
		
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) 
	{
	   
	    $this->set('page_title','Edit Product Setting');
        $this->GroupWiseDiscountBonusPolicy->id = $id;
		if (!$this->GroupWiseDiscountBonusPolicy->exists($id)) {
			throw new NotFoundException(__('Invalid Setting'));
		}

	   $this->loadModel('Product');
	   $products = $this->Product->find('list', array('order'=>array('Product.order' => 'ASC')));
	   $this->set(compact('products'));
	   
	   $office_parent_id = $this->UserAuth->getOfficeParentId();
		$this->set('office_parent_id', $office_parent_id);
        if ($office_parent_id == 0) {
            $office_conditions = array(
			'office_type_id' => 2,
			"NOT" => array( "id" => array(30, 31, 37))
			);
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }
		$this->set('office_id', $this->UserAuth->getOfficeId());
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		$this->set(compact('offices'));
		
		$outlet_categories = $this->OutletCategory->find('list',array('conditions'=>array('is_active'=>1)));
		$this->set(compact('outlet_categories'));
		
		$outlet_groups = array();
		$o_con =array('OutletGroup.is_distributor' => 0);
		$outlet_groups = $this->OutletGroup->find('list', array('conditions' => $o_con, 'order' => array('name' => 'asc')));		
		$this->set(compact('outlet_groups'));

		$this->loadModel('Product');
		$products = $this->Product->find('list', array('order'=>array('Product.order' => 'ASC')));
		$this->html = '';
		foreach($products as $key=>$val)
		{
			$this->html .= '<option value="'.$key.'">'.addslashes($val).'</option>';		
		}
		$product_list = $this->html;
		$this->set(compact('products', 'product_list'));

		$policy_types = array(
			'0'=>'Only Discount',
			'1'=>'Only Bonus',
			'2'=>'Discount and Bonus',
			'3'=>'Discount or Bonus',
		);
		$this->set(compact('policy_types'));
		
		$disccount_types = array(
			'0'=>'%',
			'1'=>'Tk'
		);
		$this->set(compact('disccount_types'));
		
	   
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			
			//pr($this->request->data);
			//exit;
			
			$data_policy = array();
			$data_policy['GroupWiseDiscountBonusPolicy']['id'] = $id; 
			$data_policy['GroupWiseDiscountBonusPolicy']['start_date'] = date('Y-m-d', strtotime($this->request->data['GroupWiseDiscountBonusPolicy']['start_date']));
			$data_policy['GroupWiseDiscountBonusPolicy']['end_date'] = date('Y-m-d', strtotime($this->request->data['GroupWiseDiscountBonusPolicy']['end_date']));
			$data_policy['GroupWiseDiscountBonusPolicy']['name'] = $this->request->data['GroupWiseDiscountBonusPolicy']['name']; 
			$data_policy['GroupWiseDiscountBonusPolicy']['remarks'] = $this->request->data['GroupWiseDiscountBonusPolicy']['remarks']; 
			
			$data_policy['GroupWiseDiscountBonusPolicy']['created_at'] = $this->current_datetime(); 
			$data_policy['GroupWiseDiscountBonusPolicy']['updated_at'] = $this->current_datetime(); 
			$data_policy['GroupWiseDiscountBonusPolicy']['created_by'] = $this->UserAuth->getUserId();
			$data_policy['GroupWiseDiscountBonusPolicy']['updated_by'] = $this->UserAuth->getUserId();

			//$this->GroupWiseDiscountBonusPolicy->create();
			if ($this->GroupWiseDiscountBonusPolicy->save($data_policy)) 
			{
				$policy_id = $id;
				
				//Offices Insert
				//pr($this->request->data['GroupWiseDiscountBonusPolicyToOffice']);
				//exit;
				
				if(!empty($this->request->data['GroupWiseDiscountBonusPolicyToOffice']))
				{
					$this->GroupWiseDiscountBonusPolicyToOffice->deleteAll(array('GroupWiseDiscountBonusPolicyToOffice.group_wise_discount_bonus_policy_id'=>$id));
					$data_array = array();
					$data_array['GroupWiseDiscountBonusPolicyToOffice']['group_wise_discount_bonus_policy_id'] = $policy_id;
					$office_data = array();
					foreach($this->request->data['GroupWiseDiscountBonusPolicyToOffice']['office_id'] as $key => $val){
						if($val)$data_array['GroupWiseDiscountBonusPolicyToOffice']['office_id'] = $val;
						$office_data[] = $data_array;
					}
					/*pr($office_data);
					exit;*/
					$this->GroupWiseDiscountBonusPolicyToOffice->saveAll($office_data);
				}
				
				
				//Outlet Group Insert
				if(!empty($this->request->data['GroupWiseDiscountBonusPolicyToOutletGroup']))
				{
					$this->GroupWiseDiscountBonusPolicyToOutletGroup->deleteAll(array('GroupWiseDiscountBonusPolicyToOutletGroup.group_wise_discount_bonus_policy_id'=>$id));
					$data_array = array();
					$data_array['GroupWiseDiscountBonusPolicyToOutletGroup']['group_wise_discount_bonus_policy_id'] = $policy_id;
					$outlet_group_data = array();
					foreach($this->request->data['GroupWiseDiscountBonusPolicyToOutletGroup']['outlet_group_id'] as $key => $val){
						if($val)$data_array['GroupWiseDiscountBonusPolicyToOutletGroup']['outlet_group_id'] = $val;
						$outlet_group_data[] = $data_array;
					}
					$this->GroupWiseDiscountBonusPolicyToOutletGroup->saveAll($outlet_group_data);
				}
				
				//Outlet Category Insert
				if(!empty($this->request->data['GroupWiseDiscountBonusPolicyToOutletCategory']))
				{
					$this->GroupWiseDiscountBonusPolicyToOutletCategory->deleteAll(array('GroupWiseDiscountBonusPolicyToOutletCategory.group_wise_discount_bonus_policy_id'=>$id));
					$data_array = array();
					$data_array['GroupWiseDiscountBonusPolicyToOutletCategory']['group_wise_discount_bonus_policy_id'] = $policy_id;
					$outlet_group_data = array();
					foreach($this->request->data['GroupWiseDiscountBonusPolicyToOutletCategory']['outlet_category_id'] as $key => $val){
						if($val)$data_array['GroupWiseDiscountBonusPolicyToOutletCategory']['outlet_category_id'] = $val;
						$outlet_group_data[] = $data_array;
					}
					$this->GroupWiseDiscountBonusPolicyToOutletCategory->saveAll($outlet_group_data);
				}
				
				
				//Policy Product Insert
				if(!empty($this->request->data['GroupWiseDiscountBonusPolicyProduct']))
				{
					$this->GroupWiseDiscountBonusPolicyProduct->deleteAll(array('GroupWiseDiscountBonusPolicyProduct.group_wise_discount_bonus_policy_id'=>$id));
					$data_array = array();
					$data_array['GroupWiseDiscountBonusPolicyProduct']['group_wise_discount_bonus_policy_id'] = $policy_id;
					$root_products = array();
					foreach($this->request->data['GroupWiseDiscountBonusPolicyProduct']['policy_product_id'] as $key => $val){
						if($val)$data_array['GroupWiseDiscountBonusPolicyProduct']['product_id'] = $val;
						$root_products[] = $data_array;
					}
					$this->GroupWiseDiscountBonusPolicyProduct->saveAll($root_products);
				}
				
				//Policy Option Insert
				if(!empty($this->request->data['GroupWiseDiscountBonusPolicyOption']))
				{
					$this->GroupWiseDiscountBonusPolicyOption->deleteAll(array('GroupWiseDiscountBonusPolicyOption.group_wise_discount_bonus_policy_id'=>$id));
					
					$this->GroupWiseDiscountBonusPolicyOptionPriceSlab->deleteAll(array('GroupWiseDiscountBonusPolicyOptionPriceSlab.group_wise_discount_bonus_policy_id'=>$id));
					
					$this->GroupWiseDiscountBonusPolicyOptionBonusProduct->deleteAll(array('GroupWiseDiscountBonusPolicyOptionBonusProduct.group_wise_discount_bonus_policy_id'=>$id));
					
					$data_array = array();
					$option_data = array();
					foreach($this->request->data['GroupWiseDiscountBonusPolicyOption'] as $key => $val)
					{
						$data_array['GroupWiseDiscountBonusPolicyOption']['group_wise_discount_bonus_policy_id'] = $policy_id;
						$data_array['GroupWiseDiscountBonusPolicyOption']['policy_type'] = $val['policy_type'];
						$data_array['GroupWiseDiscountBonusPolicyOption']['min_qty'] = $val['min_qty'];
						//$data_array['GroupWiseDiscountBonusPolicyOption']['bonus_product_id']=$val['bonus_product_id'];
						//if(isset($val['measurement_unit_id']))$data_array['GroupWiseDiscountBonusPolicyOption']['measurement_unit_id']=$val['measurement_unit_id'];
						//$data_array['GroupWiseDiscountBonusPolicyOption']['bonus_qty'] = $val['bonus_qty'];
						$data_array['GroupWiseDiscountBonusPolicyOption']['discount_amount'] = $val['discount_amount'];
						$data_array['GroupWiseDiscountBonusPolicyOption']['disccount_type'] = $val['disccount_type'];
						//$option_data[] = $data_array;
						$this->GroupWiseDiscountBonusPolicyOption->create();
						
						//pr($data_array);
						
						//pr($this->request->data['GroupWiseDiscountBonusPolicyOptionPriceSlab'][$key]);
						
						if($this->GroupWiseDiscountBonusPolicyOption->save($data_array))
						{
							$policy_option_id = $this->GroupWiseDiscountBonusPolicyOption->getInsertID();
							
							//insert bonus products
							$b_data_array = array();
							$b_data_array2 = array();
							if($val['bonus_product_id'])
							{
								$m_unit_ids = $val['measurement_unit_id'];
								$bonus_qtys = $val['bonus_qty'];
								foreach($val['bonus_product_id'] as $b_key => $b_val)
								{
									if($b_val)
									{	$b_data_array['GroupWiseDiscountBonusPolicyOptionBonusProduct']['group_wise_discount_bonus_policy_id'] = $policy_id;
										$b_data_array['GroupWiseDiscountBonusPolicyOptionBonusProduct']['group_wise_discount_bonus_policy_option_id'] = $policy_option_id;
										$b_data_array['GroupWiseDiscountBonusPolicyOptionBonusProduct']['bonus_product_id']=$b_val;
										$b_data_array['GroupWiseDiscountBonusPolicyOptionBonusProduct']['bonus_qty']=$bonus_qtys[$b_key];
										$b_data_array['GroupWiseDiscountBonusPolicyOptionBonusProduct']['measurement_unit_id']=$m_unit_ids[$b_key];
										$b_data_array['GroupWiseDiscountBonusPolicyOptionBonusProduct']['relation']=0;
										$b_data_array2[] = $b_data_array;
									}
								}
								$this->GroupWiseDiscountBonusPolicyOptionBonusProduct->saveAll($b_data_array2);
							}
							
							
							//insert price slab
							$p_data_array = array();
							$price_slab = array();
							
							if(@$this->request->data['GroupWiseDiscountBonusPolicyOptionPriceSlab'][$key]['slab_id'])
							{
								//pr($this->request->data['GroupWiseDiscountBonusPolicyOptionPriceSlab'][$key]['slab_id']);
								
								foreach($this->request->data['GroupWiseDiscountBonusPolicyOptionPriceSlab'][$key]['slab_id'] as $key2 => $val)
								{
									//if($val){
									$p_data_array['GroupWiseDiscountBonusPolicyOptionPriceSlab']['group_wise_discount_bonus_policy_id'] = $policy_id;
									$p_data_array['GroupWiseDiscountBonusPolicyOptionPriceSlab']['group_wise_discount_bonus_policy_option_id'] = $policy_option_id;
							
									$p_data_array['GroupWiseDiscountBonusPolicyOptionPriceSlab']['discount_product_id'] = $this->request->data['GroupWiseDiscountBonusPolicyOptionPriceSlab'][$key]['discount_product_id'][$key2];
									$p_data_array['GroupWiseDiscountBonusPolicyOptionPriceSlab']['slab_id'] = $val;
									$price_slab[] = $p_data_array;
									//}
								}
								//pr($price_slab);
								$this->GroupWiseDiscountBonusPolicyOptionPriceSlab->saveAll($price_slab);
							}
							
						}
					}
				}
				
				//exit;
				
				$this->Session->setFlash(__('The request has been update'), 'flash/success');
				$this->redirect(array('action' => 'index'));
				exit;
			}
			
			
		} 
		else 
		{
			$options = array(
				'conditions' => array(
					'GroupWiseDiscountBonusPolicy.' . $this->GroupWiseDiscountBonusPolicy->primaryKey => $id
				),
				'recursive' => 2
			);
			
			$this->request->data = $this->GroupWiseDiscountBonusPolicy->find('first', $options);
			
			$this->request->data['GroupWiseDiscountBonusPolicy']['start_date'] = date('d-m-Y', strtotime($this->request->data['GroupWiseDiscountBonusPolicy']['start_date']));
			$this->request->data['GroupWiseDiscountBonusPolicy']['end_date'] = date('d-m-Y', strtotime($this->request->data['GroupWiseDiscountBonusPolicy']['end_date']));
			
			$office_ids = array();
			foreach($this->request->data['GroupWiseDiscountBonusPolicyToOffice'] as $key=>$val){
				array_push($office_ids, $val['office_id']);
			}
			$this->set(compact('office_ids'));
			
			$outlet_group_ids = array();
			foreach($this->request->data['GroupWiseDiscountBonusPolicyToOutletGroup'] as $key=>$val){
				array_push($outlet_group_ids, $val['outlet_group_id']);
			}
			$this->set(compact('outlet_group_ids'));
			
			$outlet_category_ids = array();
			foreach($this->request->data['GroupWiseDiscountBonusPolicyToOutletCategory'] as $key=>$val){
				array_push($outlet_category_ids, $val['outlet_category_id']);
			}
			$this->set(compact('outlet_category_ids'));
			
			//pr($this->request->data);
			//pr($this->request->data['GroupWiseDiscountBonusPolicyOption']);
			//exit;
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
	public function admin_delete($id = null) 
	{
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->GroupWiseDiscountBonusPolicy->id = $id;
		if (!$this->GroupWiseDiscountBonusPolicy->exists()) {
			throw new NotFoundException(__('Invalid Data!'));
		}
		
		if ($this->GroupWiseDiscountBonusPolicy->delete())
		{
			
			$this->GroupWiseDiscountBonusPolicyToOffice->deleteAll(array('GroupWiseDiscountBonusPolicyToOffice.group_wise_discount_bonus_policy_id'=>$id));
			$this->GroupWiseDiscountBonusPolicyToOutletGroup->deleteAll(array('GroupWiseDiscountBonusPolicyToOutletGroup.group_wise_discount_bonus_policy_id'=>$id));
			$this->GroupWiseDiscountBonusPolicyProduct->deleteAll(array('GroupWiseDiscountBonusPolicyProduct.group_wise_discount_bonus_policy_id'=>$id));
			
			$this->GroupWiseDiscountBonusPolicyOption->deleteAll(array('GroupWiseDiscountBonusPolicyOption.group_wise_discount_bonus_policy_id'=>$id));
			$this->GroupWiseDiscountBonusPolicyOptionPriceSlab->deleteAll(array('GroupWiseDiscountBonusPolicyOptionPriceSlab.group_wise_discount_bonus_policy_id'=>$id));
			$this->GroupWiseDiscountBonusPolicyOptionBonusProduct->deleteAll(array('GroupWiseDiscountBonusPolicyOptionBonusProduct.group_wise_discount_bonus_policy_id'=>$id));
			
			$this->Session->setFlash(__('Deleted successfully!'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('List was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	
	
	public function admin_get_slab_list()
	{
		$this->loadModel('ProductPrice');
		$this->loadModel('ProductCombination');
		
		$current_date = $this->current_date();
		$product_id = $this->request->data['product_id'];
		/*------- start get prev effective date and next effective date ---------*/
		$effective_prev_date = $this->ProductPrice->find('all',
			array(
				'conditions' => array('ProductPrice.product_id' => $product_id,'ProductPrice.effective_date <=' => $current_date,'ProductPrice.institute_id'=>0,'ProductPrice.has_combination'=>0),
				'fields' => array('ProductPrice.effective_date')
			)
		);
		if(!empty($effective_prev_date)){
				$prev_short_list = array();
				foreach($effective_prev_date as $prev_date_key => $prev_date_val){
					array_push($prev_short_list,$prev_date_val['ProductPrice']['effective_date']);
				}
				asort($prev_short_list);
				$prev_date = end($prev_short_list);
		}
		$effective_next_date = $this->ProductPrice->find('all',
			array(
				'conditions' => array('ProductPrice.product_id' => $product_id,'ProductPrice.effective_date >' => $current_date,'ProductPrice.institute_id'=>0,'ProductPrice.has_combination'=>0),
				'fields' => array('ProductPrice.effective_date')
			)
		);
		if(!empty($effective_next_date)){
				$next_short_list = array();
				foreach($effective_next_date as $next_date_key => $next_date_val){
					array_push($next_short_list,$next_date_val['ProductPrice']['effective_date']);
				}
				rsort($next_short_list);
				$next_date = end($next_short_list);
		}
		if(isset($next_date)){
			$condition_value['ProductCombination.effective_date <'] = $next_date;
		}
		if(isset($prev_date)){
			$condition_value['ProductCombination.effective_date >='] = $prev_date;
		}
		$condition_value['ProductCombination.product_id'] = $product_id;
		$condition_value['ProductCombination.combination_id'] = 0;
		$condition_value['OR']=array('ProductPrice.project_id is null','ProductPrice.project_id'=>0);
		/*------- end get prev effective date and next effective date ---------*/
		
		
		if(!empty($product_id)){
			/*$slab_list = $this->ProductCombination->find('all',array(
			'conditions' => array($condition_value),
			'recursive'=>-1
			));*/
			$slab_list = $this->ProductCombination->find('all', array(
				'conditions' => $condition_value,
				'order' => array('ProductCombination.updated_at' => 'asc'),
				'joins'=>array(
					array(
						'table'=>'product_prices',
						'alias'=>'ProductPrice',
						'conditions'=>'ProductPrice.id=ProductCombination.product_price_id'
						),
					),
				'recursive' => -1
			));
		}else{
			$slab_list = '';
		}
		$parent_slab_id = isset($this->request->data['parent_slab_id'])?$this->request->data['parent_slab_id']:0;
		
		$html = '<option value="">---- Select Slab ----</option>';
		foreach($slab_list as $key=>$val)
		{
			if($parent_slab_id==$val['ProductCombination']['id']){
				$html = $html.'<option selected value="'.$val['ProductCombination']['id'].'">'.$val['ProductCombination']['min_qty'].' ('.$val['ProductCombination']['effective_date'].') </option>';
			}else{
				// $html = $html.'<option value="'.$key.'">'.$val.'</option>';
				$html = $html.'<option value="'.$val['ProductCombination']['id'].'">'.$val['ProductCombination']['min_qty'].' ('.$val['ProductCombination']['effective_date'].') </option>';
			}
		}
		echo $html;
		$this->autoRender = false;
	}
	
	
	public function admin_get_product_units()
	{
		$this->loadModel('ProductMeasurement');
		
		$product_id = $this->request->data['product_id'];
		
		$this->loadModel('Product');
		$product_info = $this->Product->find('first',array(
						'conditions'=>array(
							'Product.id' => $product_id,
						),
						'joins' => array(
							array(
								'alias' => 'MeasurementUnit',
								'table' => 'measurement_units',
								'type' => 'INNER',
								'conditions' => 'Product.base_measurement_unit_id = MeasurementUnit.id'
							),
						),
						'fields' => array('MeasurementUnit.id', 'MeasurementUnit.name'),
						'recursive'=>-1
					));
		
		
		$conditions = array('ProductMeasurement.product_id' => $product_id);		
		$unit_info = $this->ProductMeasurement->find('all', array(
           'fields' => array('ProductMeasurement.measurement_unit_id', 'MeasurementUnit.name'),
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
		//exit;
		
		$parent_unit_id = isset($this->request->data['parent_unit_id'])?$this->request->data['parent_unit_id']:0;
		
		//$html = '<option value="">---- Select Unit ----</option>';
		$html = '';
		
		//exit;
		foreach($product_info as $key=>$val)
		{
			//pr($val);
			if($parent_unit_id==$val['id']){
				$html = $html.'<option selected value="'.$val['id'].'">'.$val['name'].'</option>';
			}else{
				$html = $html.'<option value="'.$val['id'].'">'.$val['name'].'</option>';
			}
		}
		
		foreach($unit_info as $key=>$val)
		{
			if($parent_unit_id==$val['ProductMeasurement']['measurement_unit_id']){
				$html = $html.'<option selected value="'.$val['ProductMeasurement']['measurement_unit_id'].'">'.$val['MeasurementUnit']['name'].'</option>';
			}else{
				$html = $html.'<option value="'.$val['ProductMeasurement']['measurement_unit_id'].'">'.$val['MeasurementUnit']['name'].'</option>';
			}
		}
		echo $html;
		$this->autoRender = false;
	}
	
}
