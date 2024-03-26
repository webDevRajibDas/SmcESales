<?php
App::uses('AppController', 'Controller');
/**
 * ProductPrices Controller
 *
 * @property ProductPrice $ProductPrice
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ProductPricesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session','Filter.Filter');
	public $uses =  array('Product','ProductPrice');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','Product Price List');
		$this->Product->recursive = 0;
		
		$this->paginate = array(
			'conditions'=>array(
				'Product.product_type_id'=>1
				),
			'order' => array('Product.order' => 'ASC')
			);
		$this->set('products', $this->paginate());
		
		// $productCategories = $this->Product->ProductCategory->generateTreeList(null, null, null, '__');
		$productCategories = $this->Product->ProductCategory->find('list');
		$productTypes = $this->Product->ProductType->find('list',array('order'=>array('name'=>'asc')));
		$this->set(compact('productCategories','productTypes'));	
	}
	
	public function admin_price_list($id = null){
		$this->set('page_title','Price List');
		$this->loadModel('ProductPrice');
		$this->loadModel('Product');
		$this->ProductPrice->recursive = -1;
		$this->paginate = array('order' => array('ProductPrice.id' => 'DESC'),'conditions'=> array('ProductPrice.product_id' => $id,'ProductPrice.has_combination' => 0,'ProductPrice.institute_id' => 0));
		$this->set('product_prices', $this->paginate('ProductPrice'));
		$product=$this->Product->find('first',array('conditions'=>array('Product.id'=>$id),'recursive'=>-1));
		$this->set('product',$product);
		$this->set('id',$id);
	}
	
		public function admin_distributor_commission_list($id = null){
		$this->set('page_title','Distributor Commission');
		$this->loadModel('DistributorCommission');
		$this->loadModel('Product');
		$this->loadModel('OutletCategory');
		$this->DistributorCommission->recursive = -1;
		$this->paginate = array('order' => array('DistributorCommission.id' => 'DESC'),'conditions'=> array('DistributorCommission.product_id' => $id));
		$this->set('product_prices', $this->paginate('DistributorCommission'));
		$product=$this->Product->find('first',array('conditions'=>array('Product.id'=>$id),'recursive'=>-1));
		$outletCategories=$this->OutletCategory->find('list',
										array('conditions'=>array('OutletCategory.id'=>17),
										'recursive'=>-1));
		$this->set('product',$product);
		$this->set('outletCategories',$outletCategories);
		$this->set('id',$id);
	}
/***************************** start new function 19-11-2019 *****************************************************/	
	public function admin_distributor_wsie_commission_list($id = null){
		$this->set('page_title','Distributor Wise Commissions');
		$product_id = $id;
		$this->loadModel('DistDistributorWiseCommission');
		if($this->request->is('post')){
			if(array_key_exists('search', $this->request->data)){

				$dist_distributor_id = $this->request->data['DistDistributorWiseCommission']['dist_distributor_id'];
				$product_id = $this->request->data['DistDistributorWiseCommission']['product_id'];
				//pr($this->request->data);
				if(!empty($this->request->data['DistDistributorWiseCommission']['effective_date'])){
					$effective_date = $this->request->data['DistDistributorWiseCommission']['effective_date'];
					$effective_date = date('Y-m-d', strtotime($effective_date));
					$conditions = array(
						'DistDistributorWiseCommission.dist_distributor_id'=>$dist_distributor_id,
						'DistDistributorWiseCommission.effective_date <= '=>$effective_date,
						'DistDistributorWiseCommission.effective_date >= '=>$effective_date,
						'DistDistributorWiseCommission.product_id'=>$product_id,
					);
				}
				else{
					$conditions = array(
						'DistDistributorWiseCommission.dist_distributor_id'=>$dist_distributor_id,
						'DistDistributorWiseCommission.product_id'=>$product_id,
					);
				}
				$dist_commissions = $this->DistDistributorWiseCommission->find('all',array(
					'conditions'=>$conditions,
					'order'=>array('DistDistributorWiseCommission.id DESC'),
				));
				
				$this->set('dist_commissions', $dist_commissions);
			}
			else{
				$data = array();
				$effective_date = $this->request->data['DistDistributorWiseCommission']['effective_date'];

				$data['DistDistributorWiseCommission']['effective_date'] = date('Y-m-d', strtotime($effective_date));
				$data['DistDistributorWiseCommission']['dist_distributor_id'] = $this->request->data['DistDistributorWiseCommission']['dist_distributor_id'];
				$data['DistDistributorWiseCommission']['commission_rate'] = $this->request->data['DistDistributorWiseCommission']['commission_rate'];
				$data['DistDistributorWiseCommission']['product_id'] = $this->request->data['DistDistributorWiseCommission']['product_id'];
				$data['DistDistributorWiseCommission']['created_at'] = $this->current_datetime();
				$data['DistDistributorWiseCommission']['created_by'] = $this->UserAuth->getUserId();
				$data['DistDistributorWiseCommission']['updated_at'] = $this->current_datetime();
				$data['DistDistributorWiseCommission']['updated_by'] = $this->UserAuth->getUserId();

				$this->DistDistributorWiseCommission->create();
				if($this->DistDistributorWiseCommission->save($data)){
					$this->Session->setFlash(__('The Distributor wise Commission has been set'), 'flash/success');
					$this->redirect(array('action' => 'distributor_wsie_commission_list/'.$product_id));
				}

			}
		}else{
			$dist_commissions = $this->DistDistributorWiseCommission->find('all',array(
	        	'conditions' => array(
	        		'DistDistributorWiseCommission.product_id' => $id,
	        	),
	        	'order' =>array('DistDistributorWiseCommission.id DESC'),
	        ));
	        $this->set('dist_commissions', $dist_commissions);
		}
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

        

        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        $distributors = $this->DistDistributor->find('list',array('conditions'=>$distributor_conditions));
        $this->set(compact('offices','distributors','product_id'));
		
	}

/***************************** end new function 19-11-2019 *****************************************************/	

	public function admin_set_commission_price($product_id = null){
		$this->set('page_title','Add Commission Price');
		$this->loadModel('DistributorCommission');
		if ($this->request->is('post')) 
		{
			$this->request->data['DistributorCommission']['effective_date'] = date('Y-m-d',strtotime($this->request->data['DistributorCommission']['effective_date']));
			$effective_date=$this->request->data['DistributorCommission']['effective_date'];
			$data = $this->DistributorCommission->find('first',array(
					'conditions' => array(
						'DistributorCommission.product_id'=>$product_id,
						'DistributorCommission.effective_date >'=>$effective_date
						),
					'recursive' => -1
				));
				if (count($data)>0) {
					$this->Session->setFlash(__('New Effective Date Could not less than previous date.'), 'flash/error');
					$this->redirect(array('action' => 'distributor_commission_list/'.$product_id));
				}
			$this->request->data['DistributorCommission']['product_id'] = $product_id; 
			$this->request->data['DistributorCommission']['created_at'] = $this->current_datetime(); 
			$this->request->data['DistributorCommission']['updated_at'] = $this->current_datetime(); 
			$this->request->data['DistributorCommission']['created_by'] = $this->UserAuth->getUserId();
			$this->DistributorCommission->create();
			if ($this->DistributorCommission->save($this->request->data)) {
				$this->Session->setFlash(__('The Commission has been set'), 'flash/success');
				$this->redirect(array('action' => 'distributor_commission_list/'.$product_id));
			}
		}
	}
	public function admin_edit_commission_price($product_id = null, $id = null) {
		$this->set('page_title','Edit Commission Price');
		$this->loadModel('DistributorCommission');
		if ($this->request->is('post') || $this->request->is('put')) {
			{
				$this->request->data['DistributorCommission']['id'] = $id;
				$this->request->data['DistributorCommission']['effective_date'] = date('Y-m-d',strtotime($this->request->data['DistributorCommission']['effective_date']));
				$effective_date=$this->request->data['DistributorCommission']['effective_date'];
				$data = $this->DistributorCommission->find('first',array(
					'conditions' => array(
						"NOT" => array('DistributorCommission.id' => $id),
						'DistributorCommission.effective_date >'=>$effective_date
						),
					'recursive' => -1
				));
				if (count($data)>0) {
					$this->Session->setFlash(__('New Modification Date Could not less than previous date.'), 'flash/error');
					$this->redirect(array('action' => 'distributor_commission_list/'.$product_id));
				}
				$this->request->data['DistributorCommission']['product_id'] = $product_id; 
				$this->request->data['DistributorCommission']['created_at'] = $this->current_datetime(); 
				$this->request->data['DistributorCommission']['updated_at'] = $this->current_datetime(); 
				$this->request->data['DistributorCommission']['created_by'] = $this->UserAuth->getUserId();
				$this->DistributorCommission->create();
				if ($this->DistributorCommission->save($this->request->data)) {
					$this->Session->setFlash(__('The Commission has been updated'), 'flash/success');
					$this->redirect(array('action' => 'distributor_commission_list/'.$product_id));
				}
			}
		}
		$options = array('conditions' => array('DistributorCommission.' . $this->DistributorCommission->primaryKey => $id));
		$this->request->data = $this->DistributorCommission->find('first', $options);
	}
	/**
 * admin_set_price_slot method
 *
 * @return void
 */
	public function admin_set_unique_price($id = null,$product_price_id = null){
		$this->loadModel('ProductCombination');
		$this->set('product_id',$id);
		if ($this->request->is('post')) 
		{
			/*---------- start get immediate Prev date ---------*/
			$new_start_date = date('Y-m-d',strtotime($this->request->data['ProductPrice']['effective_date']));
			//$immediate_pre_date;
			$effective_date_list = $this->ProductPrice->find('all',
				array(
					'conditions' => array('ProductPrice.product_id' => $id,'ProductPrice.effective_date <' => $new_start_date,'ProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
					'fields' => array('ProductPrice.effective_date')
				)
			);
			$short_list = array();
			if(!empty($effective_date_list)){
				foreach($effective_date_list as $date_key => $date_val){
					array_push($short_list,$date_val['ProductPrice']['effective_date']);
				}
				asort($short_list);
				$immediate_pre_date = array_pop($short_list);
				/*---------- end get immediate less date ---------*/
				/*---------- start immediate start date data --------*/
				$effective_price_data = $this->ProductPrice->find('first',
					array(
						'conditions' => array('ProductPrice.product_id' => $id,'ProductPrice.effective_date' => $immediate_pre_date,'ProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
						'fields' => array('ProductPrice.effective_date')
					)
				);
			}
			/*---------- end immediate start date data --------*/
			$pre_end_date = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($this->request->data['ProductPrice']['effective_date'])))));

			$this->request->data['ProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['ProductPrice']['effective_date']));
			$this->request->data['ProductPrice']['product_id'] = $id; 
			$this->request->data['ProductPrice']['created_at'] = $this->current_datetime(); 
			$this->request->data['ProductPrice']['updated_at'] = $this->current_datetime(); 
			$this->request->data['ProductPrice']['created_by'] = $this->UserAuth->getUserId();
			$this->ProductPrice->create();
			if ($this->ProductPrice->save($this->request->data)) {
				$inserted_product_price_id = $this->ProductPrice->getInsertID();
				if(!empty($this->request->data['ProductCombination'])){
					$data_array = array();
					$data['ProductCombination']['product_id'] = $id;
					$data['ProductCombination']['created_at'] = $this->current_datetime(); 
					$data['ProductCombination']['updated_at'] = $this->current_datetime(); 
					$data['ProductCombination']['created_by'] = $this->UserAuth->getUserId();
					$data['ProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['ProductPrice']['effective_date']));
					foreach($this->request->data['ProductCombination']['min_qty'] as $key=>$val)
					{
						$data['ProductCombination']['product_price_id'] = $this->ProductPrice->id;
						$data['ProductCombination']['min_qty'] = $val;
						$data['ProductCombination']['price'] = $this->request->data['ProductCombination']['price'][$key];
						$data_array[] = $data;
					}								
					$this->ProductCombination->saveAll($data_array);
					$price_id=$this->ProductPrice->id;
					$sql="exec insert_dist_price_when_insert_esales_price $price_id";
					$result = $this->ProductCombination->query($sql);
					/*----------- start update prev row by end date -----------*/
					if(!empty($effective_price_data)){
						$update_pre_row['ProductPrice']['id'] = $effective_price_data['ProductPrice']['id'];
						$update_pre_row['ProductPrice']['end_date'] = $pre_end_date;
						if($this->ProductPrice->save($update_pre_row)){
							
							foreach($effective_price_data['ProductCombination'] as $prev_key=>$prev_val){
								$update_combination_prev_row['ProductCombination']['id'] = $prev_val['id'];
								$update_combination_prev_row['ProductCombination']['end_date'] = $pre_end_date;
								$this->ProductCombination->save($update_combination_prev_row);
							}
						}
					}
					/*----------- start recent inserted data -----------*/
					$recent_inserted_data = $this->ProductPrice->find('first',
						array(
							'conditions' => array('ProductPrice.id' => $inserted_product_price_id),
							'fields' => array('ProductPrice.effective_date')
						)
					);

					/*---------- end recent inserted data -------------*/
					/*---------- start next effective_date list ---------*/
					$effective_next_date_list = $this->ProductPrice->find('all',
						array(
							'conditions' => array('ProductPrice.product_id' => $id,'ProductPrice.effective_date >' => $recent_inserted_data['ProductPrice']['effective_date'],'ProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
							'fields' => array('ProductPrice.effective_date')
						)
					);

					$next_short_list = array();
					if(!empty($effective_next_date_list)){
						foreach($effective_next_date_list as $next_date_key => $next_date_val){
							array_push($next_short_list,$next_date_val['ProductPrice']['effective_date']);
						}
						rsort($next_short_list);
						$immidiat_next_date = array_pop($next_short_list);
						/*---------- end get immediate less date ---------*/
					}

					/*----------- start update recent inserted row by end date -----------*/
					if(!empty($immidiat_next_date)){
						$end_date_of_current_row = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immidiat_next_date)))));
						$update_current_row['ProductPrice']['id'] = $recent_inserted_data['ProductPrice']['id'];
						$update_current_row['ProductPrice']['end_date'] = $end_date_of_current_row;
						if($this->ProductPrice->save($update_current_row)){
							
							foreach($recent_inserted_data['ProductCombination'] as $next_key=>$next_val){
								$update_combination_next_row['ProductCombination']['id'] = $next_val['id'];
								$update_combination_next_row['ProductCombination']['end_date'] = $end_date_of_current_row;
								$this->ProductCombination->save($update_combination_next_row);
							}
						}
					}
					/*----------- start update recent inserted row by end date
					/*----------- start recent inserted data -----------*/
				}
				$this->Session->setFlash(__('The product price has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index/'.$id));
			}
		}
	}
/**
 * admin_set_price_slot method
 *
 * @return void
 */
	public function admin_set_price($id = null, $product_price_id = null) {
		
		         $this->set('page_title','Set Product Price');	
		         $this->loadModel('ProductCombination');
				 $this->loadModel('MemoDetail');
				 
				 
				 				
		          /* check product_price_id is existed in memo_details table */
		       if($product_price_id)
			   {
				   
				$sql="select count(*) as id_num from memo_details md
					inner join product_combinations pc on pc.id=md.product_price_id
					 where pc.product_price_id=$product_price_id";				    
				$data_sql = $this->MemoDetail->Query($sql);
				
				$update_allow=1;
				
				if($data_sql[0][0]['id_num']>0)
				{
					$update_allow=0;
				}
				
				$this->set('update_allow', $update_allow);
			
			   }
				
				
		
		
		
		
		$info = $this->ProductPrice->find('first', array('conditions' => array('ProductPrice.id' => $product_price_id)));
		$update_effective_date = $info['ProductPrice']['effective_date'];
		/*------------ start update prev date ------------*/
		$update_prev_date_list = $this->ProductPrice->find('all',
			array(
				'conditions' => array('ProductPrice.product_id' => $id,'ProductPrice.effective_date <' => $update_effective_date,'ProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
				'fields' => array('ProductPrice.effective_date')
			)
		);
		if(!empty($update_prev_date_list)){
			$update_prev_short_list = array();
			foreach($update_prev_date_list as $update_prev_date_key => $update_prev_date_val){
				array_push($update_prev_short_list,$update_prev_date_val['ProductPrice']['effective_date']);
			}
			asort($update_prev_short_list);
			$update_prev_date = end($update_prev_short_list);
			/* ----------- end get prev less date ----------*/
			/*----------- start get prev data -----------*/
			$update_prev_data = $this->ProductPrice->find('first',
				array(
					'conditions' => array('ProductPrice.product_id' => $id,'ProductPrice.effective_date' => $update_prev_date,'ProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
					'fields' => array('ProductPrice.effective_date')
				)
			);
			/* echo "<pre>";
			print_r($update_prev_data);
			exit; */
			/*--------- end get prev data -------------*/
		}
		/*----------- end update prev date --------------*/
		/*---------- start update next date ------------*/
		$update_next_date_list = $this->ProductPrice->find('all',
			array(
				'conditions' => array('ProductPrice.product_id' => $id,'ProductPrice.effective_date >' => $update_effective_date,'ProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
				'fields' => array('ProductPrice.effective_date')
			)
		);
		if(!empty($update_next_date_list)){
			$update_next_short_list = array();
			foreach($update_next_date_list as $update_next_date_key => $update_next_date_val){
				array_push($update_next_short_list,$update_next_date_val['ProductPrice']['effective_date']);
			}
			rsort($update_next_short_list);
			$update_next_date = end($update_next_short_list);
			/* ----------- end get prev less date ----------*/
			/*----------- start get prev data -----------*/
			$update_next_data = $this->ProductPrice->find('first',
				array(
					'conditions' => array('ProductPrice.product_id' => $id,'ProductPrice.effective_date' => $update_next_date,'ProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
					'fields' => array('ProductPrice.effective_date')
				)
			);
			/*--------- end get prev data -------------*/
		}
		/*---------- end update next date ------------*/
		if(isset($info['ProductPrice']['id'])!='')
		{
			$this->ProductPrice->id = $info['ProductPrice']['id'];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->request->data['ProductPrice']['updated_by'] = $this->UserAuth->getUserId();
				$this->request->data['ProductPrice']['updated_at'] = $this->current_datetime();
				$this->request->data['ProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['ProductPrice']['effective_date']));
				if($this->ProductPrice->save($this->request->data)) {
					if(!empty($this->request->data['ProductCombination'])){
						$update_data['ProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['ProductPrice']['effective_date']));
						if(isset($this->request->data['ProductCombination']['update_min_qty'])){
							$update_data_array = array();
							$update_data['ProductCombination']['updated_by'] = $this->UserAuth->getUserId();
							$update_data['ProductCombination']['updated_at'] = $this->current_datetime();
							foreach($this->request->data['ProductCombination']['update_min_qty'] as $ukey=>$uval)
							{
								$update_data['ProductCombination']['id'] = $ukey;
								$update_data['ProductCombination']['min_qty'] = $this->request->data['ProductCombination']['update_min_qty'][$ukey];
								$update_data['ProductCombination']['product_id'] = $info['ProductPrice']['product_id'];
								$update_data['ProductCombination']['price'] = $this->request->data['ProductCombination']['update_price'][$ukey];
								$update_data_array[] = $update_data;
							}												
							$this->ProductCombination->saveAll($update_data_array);
							
						}
						if(isset($this->request->data['ProductCombination']['min_qty'])){
							$data['ProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['ProductPrice']['effective_date']));
							$data['ProductCombination']['product_id'] = $id;
							$data_array = array();
							$data['ProductCombination']['created_by'] = $this->UserAuth->getUserId();
							$data['ProductCombination']['updated_at'] = $this->current_datetime();
							$data['ProductCombination']['created_at'] = $this->current_datetime();
							foreach($this->request->data['ProductCombination']['min_qty'] as $key=>$val)
							{
								$data['ProductCombination']['product_price_id'] = $this->ProductPrice->id;
								$data['ProductCombination']['min_qty'] = $val;
								$data['ProductCombination']['price'] = $this->request->data['ProductCombination']['price'][$key];
								$data_array[] = $data;
							}														
							$this->ProductCombination->saveAll($data_array);
						}
						/*-------- start update prev row by update next row --------*/
						if(!empty($update_next_date) && !empty($update_prev_date)){
							$update_prev_end_date['ProductPrice']['id'] = $update_prev_data['ProductPrice']['id'];
							$update_prev_end_date['ProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($update_next_data['ProductPrice']['effective_date'])))));
							if($this->ProductPrice->save($update_prev_end_date)){
								foreach($update_prev_data['ProductCombination'] as $prev_update_val){
									$update_product_combination['ProductCombination']['id'] = $prev_update_val['id'];
									$update_product_combination['ProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($update_next_data['ProductPrice']['effective_date'])))));
									$this->ProductCombination->save($update_product_combination);
								}
							}
						}
						if(empty($update_next_date) && !empty($update_prev_date)){
							$update_prev_end_date['ProductPrice']['id'] = $update_prev_data['ProductPrice']['id'];
							$update_prev_end_date['ProductPrice']['end_date'] = NULL;
							if($this->ProductPrice->save($update_prev_end_date)){
								foreach($update_prev_data['ProductCombination'] as $prev_update_val){
									$update_product_combination['ProductCombination']['id'] = $prev_update_val['id'];
									$update_product_combination['ProductCombination']['end_date'] = NULL;
									$this->ProductCombination->save($update_product_combination);
								}
							}
						}
						/*-------- end update prev row by update next row --------*/
						$info_after_update = $this->ProductPrice->find('first', array('conditions' => array('ProductPrice.id' => $id,'ProductPrice.id' => $product_price_id)));
						$updated_effective_date = $info_after_update['ProductPrice']['effective_date'];
						/*------------ start updated prev date ------------*/
						$updated_prev_date_list = $this->ProductPrice->find('all',
							array(
								'conditions' => array('ProductPrice.product_id' => $id,'ProductPrice.effective_date <' => $updated_effective_date,'ProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
								'fields' => array('ProductPrice.effective_date')
							)
						);
						if(!empty($updated_prev_date_list)){
							$updated_prev_short_list = array();
							foreach($updated_prev_date_list as $updated_prev_date_key => $updated_prev_date_val){
								array_push($updated_prev_short_list,$updated_prev_date_val['ProductPrice']['effective_date']);
							}
							asort($updated_prev_short_list);
							$updated_prev_date = end($updated_prev_short_list);
							/* ----------- end get prev less date ----------*/
							/*----------- start get prev data -----------*/
							$updated_prev_data = $this->ProductPrice->find('first',
								array(
									'conditions' => array('ProductPrice.product_id' => $id,'ProductPrice.effective_date' => $updated_prev_date,'ProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
									'fields' => array('ProductPrice.effective_date')
								)
							);
							/*--------- end get prev data -------------*/
						}
						/*----------- end update prev date --------------*/
						/*---------- start update next date ------------*/
						$updated_next_date_list = $this->ProductPrice->find('all',
							array(
								'conditions' => array('ProductPrice.product_id' => $id,'ProductPrice.effective_date >' => $updated_effective_date,'ProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
								'fields' => array('ProductPrice.effective_date')
							)
						);
						if(!empty($updated_next_date_list)){
							$updated_next_short_list = array();
							foreach($updated_next_date_list as $updated_next_date_key => $updated_next_date_val){
								array_push($updated_next_short_list,$updated_next_date_val['ProductPrice']['effective_date']);
							}
							rsort($updated_next_short_list);
							$updated_next_date = end($updated_next_short_list);
							/* ----------- end get prev less date ----------*/
							/*----------- start get prev data -----------*/
							$updated_next_data = $this->ProductPrice->find('first',
								array(
									'conditions' => array('ProductPrice.product_id' => $id,'ProductPrice.effective_date' => $updated_next_date,'ProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
									'fields' => array('ProductPrice.effective_date')
								)
							);
							//echo $updated_next_date;
							//exit;
							/*--------- end get prev data -------------*/
						}
						/*---------- end update next date ------------*/
						/*-------- start updated prev row by updated next row --------*/
						if(!empty($updated_prev_date)){
							$updated_prev_end_date['ProductPrice']['id'] = $updated_prev_data['ProductPrice']['id'];
							$updated_prev_end_date['ProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_effective_date)))));
							if($this->ProductPrice->save($updated_prev_end_date)){
								foreach($updated_prev_data['ProductCombination'] as $prev_updated_val){
									$updated_product_combination['ProductCombination']['id'] = $prev_updated_val['id'];
									$updated_product_combination['ProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_effective_date)))));
									$this->ProductCombination->save($updated_product_combination);
								}
							}
						}
						if(!empty($updated_next_date)){
							$update_current_row['ProductPrice']['id'] = $product_price_id;
							$update_current_row['ProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_next_data['ProductPrice']['effective_date'])))));
							if($this->ProductPrice->save($update_current_row)){
								foreach($info['ProductCombination'] as $val){
									$current_product_combination['ProductCombination']['id'] = $val['id'];
									$current_product_combination['ProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_next_data['ProductPrice']['effective_date'])))));
									$this->ProductCombination->save($current_product_combination);
								}
							}
							if(!empty($updated_next_data)){
								$update_next_row['ProductPrice']['id'] = $updated_next_data['ProductPrice']['id'];
								$update_next_row['ProductPrice']['end_date'] = NULL;
								//print_r($update_next_row);
								//exit;
								if($this->ProductPrice->save($update_next_row)){
									foreach($updated_next_data['ProductCombination'] as $next_val){
										$next_product_combination['ProductCombination']['id'] = $next_val['id'];
										$next_product_combination['ProductCombination']['end_date'] = NULL;
										$this->ProductCombination->save($next_product_combination);
									}
								}
							}
						}else{
							$update_current_row['ProductPrice']['id'] = $product_price_id;
							$update_current_row['ProductPrice']['end_date'] = NULL;
							if($this->ProductPrice->save($update_current_row)){
								foreach($info['ProductCombination'] as $val){
									$current_product_combination['ProductCombination']['id'] = $val['id'];
									$current_product_combination['ProductCombination']['end_date'] = NULL;
									$this->ProductCombination->save($current_product_combination);
								}
							}
						}
						/*-------- end updated prev row by updated next row --------*/
					}
					
					$this->Session->setFlash(__('The product price has been saved'), 'flash/success');
					$this->redirect(array('action' => 'index/'.$id));
				}
			}		
			$options = array('conditions' => array('ProductPrice.' . $this->ProductPrice->primaryKey => $info['ProductPrice']['id']));
			$this->request->data = $this->ProductPrice->find('first', $options);
			
		}else{
			if ($this->request->is('post')) {
				$this->request->data['ProductPrice']['product_id'] = $id; 
				$this->request->data['ProductPrice']['created_at'] = $this->current_datetime(); 
				$this->request->data['ProductPrice']['updated_at'] = $this->current_datetime(); 
				$this->request->data['ProductPrice']['created_by'] = $this->UserAuth->getUserId();
				$this->request->data['ProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['ProductPrice']['effective_date']));
				$this->ProductPrice->create();
				if ($this->ProductPrice->save($this->request->data)) {
					
					if(!empty($this->request->data['ProductCombination'])){
						$data_array = array();
						$data['ProductCombination']['product_id'] = $id;
						$data['ProductCombination']['created_at'] = $this->current_datetime();
						$data['ProductCombination']['updated_at'] = $this->current_datetime();
						$data['ProductCombination']['created_by'] = $this->UserAuth->getUserId();
						$data['ProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['ProductPrice']['effective_date']));
						foreach($this->request->data['ProductCombination']['min_qty'] as $key=>$val)
						{
							$data['ProductCombination']['product_price_id'] = $this->ProductPrice->id;
							$data['ProductCombination']['min_qty'] = $val;
							$data['ProductCombination']['price'] = $this->request->data['ProductCombination']['price'][$key];
							$data_array[] = $data;
						}								
						$this->ProductCombination->saveAll($data_array); 
					}
					
					$this->Session->setFlash(__('The product price has been saved'), 'flash/success');
					$this->redirect(array('action' => 'index/'.$id));
				}
			}
		}
	}
	/*----- Start set price for ngo -----*/
	public function admin_set_ngo_price($id = null){
		$this->set('page_title','Set NGO Price');
		$this->loadModel('Institute');
		$this->loadModel('Office');
		$this->loadModel('Outlet');
		$this->loadModel('Project');
		$this->loadModel('ProductCombination');
		$this->loadModel('OutletNgoPrice');
		$this->set('product_id',$id);
		$institute_type = array(
			1 => "NGO",
			2 => "Institue"
		);
		$institute_id = $this->Institute->find('list',array(
			'order' => array('Institute.name' => 'asc')
		));
		/*$office_id = $this->Office->find('list',array(
			'order' => array('Office.office_name' => 'asc')
		));	*/
		
		if($this->UserAuth->getOfficeParentId())
		{
			$office_id = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' => 2,
					'id' => $this->UserAuth->getOfficeId()
					), 
				'order'=>array('office_name'=>'asc')
			));
		}else{
			$office_id = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' => 2,
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
		}
		
		$this->set(compact('institute_type','institute_id','office_id'));
		
		/*---- start product ngo price insertion ----*/
		if ($this->request->is('post')) 
		{
			$this->request->data['ProductPrice']['product_id'] = $id; 
			$this->request->data['ProductPrice']['created_at'] = $this->current_datetime(); 
			$this->request->data['ProductPrice']['updated_at'] = $this->current_datetime(); 
			$this->request->data['ProductPrice']['created_by'] = $this->UserAuth->getUserId();
			$this->request->data['ProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['ProductPrice']['effective_date']));
			$this->request->data['ProductPrice']['end_date'] = date('Y-m-d',strtotime($this->request->data['ProductPrice']['end_date']));
			$this->ProductPrice->create();
			if ($this->ProductPrice->save($this->request->data)) {
				$product_price_id = $this->ProductPrice->getInsertID();
				/*--------- start add campaine project ---------*/
				$project_data['Project']['name'] = $this->request->data['ProductPrice']['name'];
				$project_data['Project']['start_date'] = $this->request->data['ProductPrice']['effective_date'];
				$project_data['Project']['end_date'] = date('Y-m-d',strtotime($this->request->data['ProductPrice']['end_date']));
				$project_data['Project']['institute_id'] = $this->request->data['ProductPrice']['institute_id'];
				$project_data['Project']['created_at'] = $this->current_datetime();
				$project_data['Project']['created_by'] = $this->UserAuth->getUserId();
				$project_data['Project']['updated_at'] = $this->current_datetime();
				if(!empty($project_data))
				{
					$this->ProductPrice->id = $product_price_id;
					$this->Project->save($project_data);
					// $update_p_price['ProductPrice']['id'] = $this->ProductPrice->id;
					$update_p_price['ProductPrice']['project_id'] = $this->Project->getInsertID();
					if(!empty($update_p_price))
					{
						$this->ProductPrice->save($update_p_price);
					}
				}
				/*--------- end add campaine project ---------*/
				if(!empty($this->request->data['ProductCombination'])){
					$data_array = array();
					$data['ProductCombination']['product_id'] = $id;
					$data['ProductCombination']['effective_date'] = $this->request->data['ProductPrice']['effective_date'];
					$data['ProductCombination']['end_date'] = date('Y-m-d',strtotime($this->request->data['ProductPrice']['end_date']));
					$data['ProductCombination']['created_at'] = $this->current_datetime(); 
					$data['ProductCombination']['updated_at'] = $this->current_datetime(); 
					$data['ProductCombination']['created_by'] = $this->UserAuth->getUserId();
					foreach($this->request->data['ProductCombination']['min_qty'] as $key=>$val)
					{
						$data['ProductCombination']['product_price_id'] = $this->ProductPrice->id;
						$data['ProductCombination']['min_qty'] = $val;
						$data['ProductCombination']['price'] = $this->request->data['ProductCombination']['price'][$key];
						$data_array[] = $data;
					}
					if($this->ProductCombination->saveAll($data_array)){
						if(!empty($this->request->data['OutletNgoPrice'])){
							$ngo_data = array();
							$data['OutletNgoPrice']['institute_id'] = $this->request->data['ProductPrice']['institute_id'];
							$data['OutletNgoPrice']['product_price_id'] = $this->ProductPrice->id;
							$data['OutletNgoPrice']['type_id'] = $this->request->data['ProductPrice']['type_id'];
							$data['OutletNgoPrice']['created_at'] = $this->current_datetime(); 
							$data['OutletNgoPrice']['updated_at'] = $this->current_datetime(); 
							$data['OutletNgoPrice']['created_by'] = $this->UserAuth->getUserId();
							foreach($this->request->data['OutletNgoPrice']['outlet_id'] as $key=>$val){
								$data['OutletNgoPrice']['outlet_id'] = $val;
								$data['OutletNgoPrice']['outlet_name'] = $this->request->data['OutletNgoPrice']['outlet_name'][$key];
								$ngo_data[] = $data;
							}
							$this->OutletNgoPrice->saveAll($ngo_data);
						}
						$this->Session->setFlash(__('The Outlet NGO price has been saved'), 'flash/success');
						$this->redirect(array('action' => 'ngo_price_list/'.$id));
					}
				}
			}
		}
		/*---- end product ngo price insertion ----*/
	}
	/*----- End set price for ngo -----*/
	
	/*----- Start ngo price list -----*/
	public function admin_ngo_price_list($id = null){
		$this->set('page_title','Price List');
		$this->loadModel('ProductPrice');
		$this->ProductPrice->recursive = -1;
		$this->paginate = array(
			'order' => array('ProductPrice.id' => 'DESC'),
			'conditions'=> array(
				'ProductPrice.product_id' => $id,
				'ProductPrice.has_combination' => 0,
				'ProductPrice.institute_id !='=>0
				),
			'joins'=>array(
				array(
					'table'=>'projects',
					'alias'=>'Project',
					'conditions'=>'Project.id=ProductPrice.project_id'
					),
				),
			'fields'=>array('Project.*','ProductPrice.*'),
			);
		$this->set('product_prices', $this->paginate('ProductPrice'));
		$this->set('id',$id);
	}
	/*----- End ngo price list -----*/
	
	/*----- Start update ngo price -----*/
	public function admin_update_ngo_price($id = null, $product_price_id = null){
		$this->set('page_title','Update NGO Price');
		$this->loadModel('Project');
		$this->loadModel('Institute');
		$this->loadModel('Outlet');
		$this->loadModel('ProductCombination');
		$this->loadModel('OutletNgoPrice');
		$this->loadModel('Office');
		$this->set('product_id',$id);
		//$this->set('outlet_list',$this->OutletNgoPrice->get_outlet_price_list());
		$institute_type = array(
			1 => "NGO",
			2 => "Institue"
		);
		$institute_id = $this->Institute->find('list',array(
			'order' => array('Institute.name' => 'asc')
		));
		$office_id = $this->Office->find('list',array(
			'order' => array('Office.office_name' => 'asc')
		));
		$this->set(compact('institute_type','institute_id','office_id'));
		$info = $this->ProductPrice->find('first', array('conditions' => array('ProductPrice.id' => $product_price_id)));

		/* check product_price_id is existed in memo_details table */
		if($product_price_id)
		{

			$sql="select count(*) as id_num from memo_details md
			inner join product_combinations pc on pc.id=md.product_price_id
			where pc.product_price_id=$product_price_id";				    
			$data_sql = $this->ProductPrice->Query($sql);

			$update_allow=1;

			if($data_sql[0][0]['id_num']>0)
			{
				$update_allow=0;
			}

			$this->set('update_allow', $update_allow);
			
		}

		if(isset($info['ProductPrice']['id'])!='')
		{
			$this->ProductPrice->id = $info['ProductPrice']['id'];
			if ($this->request->is('post') || $this->request->is('put')) 
			{	
					$this->request->data['ProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['ProductPrice']['effective_date']));
					$inputed_end_date = date('Y-m-d',strtotime($this->request->data['ProductPrice']['end_date']));
					$this->request->data['ProductPrice']['end_date'] = date('Y-m-d',strtotime($this->request->data['ProductPrice']['end_date']));
					$this->request->data['ProductPrice']['updated_at'] = $this->current_datetime(); 
					$this->request->data['ProductPrice']['updated_by'] = $this->UserAuth->getUserId();
					if($this->ProductPrice->save($this->request->data)) {
						/*--------- start add campaine project ---------*/
						$project_data['Project']['name'] = $this->request->data['ProductPrice']['name'];
						$project_data['Project']['id'] = $this->request->data['Project']['id'];
						$project_data['Project']['start_date'] = $this->request->data['ProductPrice']['effective_date'];
						$project_data['Project']['end_date'] = $inputed_end_date;
						$project_data['Project']['institute_id'] = $this->request->data['ProductPrice']['institute_id'];
						$project_data['Project']['updated_at'] = $this->current_datetime();
						if(!empty($project_data)){
							$this->Project->save($project_data);
						}
						/*--------- end add campaine project ---------*/
					if(!empty($this->request->data['ProductCombination'])){
						if(isset($this->request->data['ProductCombination']['update_min_qty'])){
							$update_data['ProductCombination']['updated_at'] = $this->current_datetime();
							$update_data['ProductCombination']['updated_by'] = $this->UserAuth->getUserId();
							$update_data_array = array();
							foreach($this->request->data['ProductCombination']['update_min_qty'] as $ukey=>$uval)
							{
								$update_data['ProductCombination']['id'] = $ukey;
								$update_data['ProductCombination']['min_qty'] = $this->request->data['ProductCombination']['update_min_qty'][$ukey];
								$update_data['ProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['ProductPrice']['effective_date']));
								$update_data['ProductCombination']['end_date'] = $inputed_end_date;;
								$update_data['ProductCombination']['product_id'] = $info['ProductPrice']['product_id'];
								$update_data['ProductCombination']['price'] = $this->request->data['ProductCombination']['update_price'][$ukey];
								$update_data_array[] = $update_data;
							}												
							$this->ProductCombination->saveAll($update_data_array);
							
						}
						if(isset($this->request->data['ProductCombination']['min_qty'])){
							$data['ProductCombination']['product_id'] = $id;
							$data['ProductCombination']['created_at'] = $this->current_datetime();
							$data['ProductCombination']['created_by'] = $this->UserAuth->getUserId();
							$data['ProductCombination']['updated_at'] = $this->current_datetime();
							$data['ProductCombination']['updated_by'] = $this->UserAuth->getUserId();
							$data['ProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['ProductPrice']['effective_date']));
							$data['ProductCombination']['end_date'] = $inputed_end_date;
							$data_array = array();
							foreach($this->request->data['ProductCombination']['min_qty'] as $key=>$val)
							{
								$data['ProductCombination']['product_price_id'] = $this->ProductPrice->id;
								$data['ProductCombination']['min_qty'] = $val;
								$data['ProductCombination']['price'] = $this->request->data['ProductCombination']['price'][$key];
								$data_array[] = $data;
							}														
							$this->ProductCombination->saveAll($data_array);
						}
						/*--- outlet ngo price update ---*/
						$this->OutletNgoPrice->product_price_id = $product_price_id;
						$delet = $this->OutletNgoPrice->deleteAll(array('OutletNgoPrice.product_price_id'=>$product_price_id));
						
						if(!empty($this->request->data['OutletNgoPrice']) && $delet){
							$ngo_data = array();
							$data['OutletNgoPrice']['institute_id'] = $this->request->data['ProductPrice']['institute_id'];
							$data['OutletNgoPrice']['product_price_id'] = $this->ProductPrice->id;
							$data['OutletNgoPrice']['type_id'] = $this->request->data['ProductPrice']['type_id'];
							$data['OutletNgoPrice']['created_at'] = $this->current_datetime(); 
							$data['OutletNgoPrice']['updated_at'] = $this->current_datetime(); 
							$data['OutletNgoPrice']['created_by'] = $this->UserAuth->getUserId();
							foreach($this->request->data['OutletNgoPrice']['outlet_id'] as $key=>$val){
								$data['OutletNgoPrice']['outlet_id'] = $val;
								$data['OutletNgoPrice']['outlet_name'] = $this->request->data['OutletNgoPrice']['outlet_name'][$key];
								$ngo_data[] = $data;
							}
							$this->OutletNgoPrice->saveAll($ngo_data);
						}
					}
					$this->Session->setFlash(__('The Ngo product price has been Updated'), 'flash/success');
					$this->redirect(array('action' => 'ngo_price_list/'.$id));
				}
			}
		$options = array(
			'conditions' => array(
				'ProductPrice.' . $this->ProductPrice->primaryKey => $info['ProductPrice']['id']
			),
			
			
			);
		$this->request->data = $this->ProductPrice->find('first', $options);
		// pr($this->request->data);exit;
		}
	}

	/*----- Start update ngo price -----*/
	public function admin_add_ngo($id = null, $product_price_id = null){
		$this->set('page_title','Update NGO Price');
		$this->loadModel('Project');
		$this->loadModel('Institute');
		$this->loadModel('Outlet');
		$this->loadModel('OutletNgoPrice');
		$this->loadModel('Office');
		$this->set('product_id',$id);
		//$this->set('outlet_list',$this->OutletNgoPrice->get_outlet_price_list());
		$institute_type = array(
			1 => "NGO",
			2 => "Institue"
		);
		$institute_id = $this->Institute->find('list',array(
			'order' => array('Institute.name' => 'asc')
		));
		$office_id = $this->Office->find('list',array(
			'order' => array('Office.office_name' => 'asc')
		));
		$this->set(compact('institute_type','institute_id','office_id'));
		$info = $this->ProductPrice->find('first', array('conditions' => array('ProductPrice.id' => $product_price_id)));

		

		if(isset($info['ProductPrice']['id'])!='')
		{
			$this->ProductPrice->id = $info['ProductPrice']['id'];
			if ($this->request->is('post') || $this->request->is('put')) 
			{	
				/*--- outlet ngo price update ---*/
				$this->OutletNgoPrice->product_price_id = $product_price_id;
				$delet = $this->OutletNgoPrice->deleteAll(array('OutletNgoPrice.product_price_id'=>$product_price_id));
				
				if(!empty($this->request->data['OutletNgoPrice']) && $delet)
				{
					$ngo_data = array();
					$data['OutletNgoPrice']['institute_id'] = $this->request->data['ProductPrice']['institute_id'];
					$data['OutletNgoPrice']['product_price_id'] = $this->ProductPrice->id;
					$data['OutletNgoPrice']['type_id'] = $this->request->data['ProductPrice']['type_id'];
					$data['OutletNgoPrice']['created_at'] = $this->current_datetime(); 
					$data['OutletNgoPrice']['updated_at'] = $this->current_datetime(); 
					$data['OutletNgoPrice']['created_by'] = $this->UserAuth->getUserId();
					foreach($this->request->data['OutletNgoPrice']['outlet_id'] as $key=>$val)
					{
						$data['OutletNgoPrice']['outlet_id'] = $val;
						$data['OutletNgoPrice']['outlet_name'] = $this->request->data['OutletNgoPrice']['outlet_name'][$key];
						$ngo_data[] = $data;
					}
					$this->OutletNgoPrice->saveAll($ngo_data);
				}
				$this->Session->setFlash(__('The Ngo Added'), 'flash/success');
				$this->redirect(array('action' => 'ngo_price_list/'.$id));	
			}
		}
		$options = array(
			'conditions' => array(
				'ProductPrice.' . $this->ProductPrice->primaryKey => $info['ProductPrice']['id']
			),
		);
		$this->request->data = $this->ProductPrice->find('first', $options);
	}
	function get_outlet_info($outlet_id)
	{
		$this->loadModel('Outlet');
		$outlet_info=$this->Outlet->find('first',array(
			'conditions'=>array('Outlet.id'=>$outlet_id),
			'joins'=>array(
				array(
					'table'=>'markets',
					'alias'=>'Market',
					'conditions'=>'Market.id=Outlet.market_id',
					),
				array(
					'table'=>'territories',
					'alias'=>'Territory',
					'conditions'=>'Territory.id=Market.territory_id',
					),
				array(
					'table'=>'offices',
					'alias'=>'Office',
					'conditions'=>'Office.id=Territory.office_id',
					),
				),
			'fields'=>array(
					'Outlet.*',
					'Market.name',
					'Territory.name',
					'Office.office_name'),
			'recursive'=>-1
			));
		return $outlet_info;
	}
	/*----- End Update ngo price -----*/
	/*----- Delete Ngo price list -----*/
	public function admin_delete_ngo_price($id = null,$product_price_id = null) {
/* 		echo $id;
		echo $product_price_id;
		exit; */
		$this->loadModel('ProductCombination');
		$this->loadModel('OutletNgoPrice');
		$this->loadModel('Project');


		/* check product_price_id is existed in memo_details table */
		if($product_price_id)
		{

			$sql="select count(*) as id_num from memo_details md
			inner join product_combinations pc on pc.id=md.product_price_id
			where pc.product_price_id=$product_price_id";				    
			$data_sql = $this->ProductPrice->Query($sql);

			$update_allow=1;

			if($data_sql[0][0]['id_num']>0)
			{
				$update_allow=0;
			}

			$this->set('update_allow', $update_allow);
			if(!$update_allow)
			{
				$this->Session->setFlash(__('Product Price Couldn\'t deleted. Already Memo created'),'flash/warning');
				$this->redirect(array('action' => 'ngo_price_list/'.$id));
			}
			
		}

		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->ProductPrice->id = $product_price_id;
		if (!$this->ProductPrice->exists()) {
			throw new NotFoundException(__('Invalid Product Price'));
		}
		
		if($id != null && $product_price_id != null){
			/* ----------- start deleted row data ----------*/
			$select_effect_date = $this->ProductPrice->find('first',
				array(
					'conditions' => array('ProductPrice.product_id' => $id,'ProductPrice.id' => $product_price_id),
					'fields' => array('ProductPrice.effective_date','ProductPrice.institute_id','Project.*')
				)
			);
			/* ----------- end deleted row data ----------*/
		}
		/*---------- start delete current click row ---------*/
		$this->ProductPrice->id = $product_price_id;
		if ($this->ProductPrice->delete()) {
			$this->Project->id = $select_effect_date['Project']['id'];
			$this->Project->delete();
			$this->OutletNgoPrice->product_price_id = $product_price_id;
			$this->OutletNgoPrice->deleteAll(array('OutletNgoPrice.product_price_id'=>$product_price_id));
			
			$this->Session->setFlash(__('The Ngo product price has been Deleted'), 'flash/success');
			$this->redirect(array('action' => 'ngo_price_list/'.$id));
		}
		/*---------- end delete current click row ---------*/
		$this->flash(__('Project Price was not deleted'), array('action' => 'ngo_price_list/'.$id));
		$this->redirect(array('action' => 'ngo_price_list/'.$id));
	}
	public function get_institute_list(){
		$this->loadModel('Institute');
		$rs = array(array('id' => '', 'name' => '---- Select Institute -----'));
		$type_id = $this->request->data['type_id'];
		$institute_list = $this->Institute->find('all', array(
			'fields' => array('Institute.id', 'Institute.name'),
			'conditions' => array('Institute.type' => $type_id),
			'order' => array('name' => 'asc'),
			'recursive' => -1
		));	
		
		$data_array = Set::extract($institute_list, '{n}.Institute');

		if(!empty($institute_list)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
		
	}
	public function get_outlet_list(){
		$this->loadModel('Territory');
		$this->loadModel('Outlet');
		$institute_id = $this->request->data['institute_id'];
		$office_id = $this->request->data['office_id'];
		$territory_id = $this->request->data['territory_id'];
		$market_id = $this->request->data['market_id'];
		
		$conditions=array();

		if($institute_id)
		{
			$conditions['Outlet.institute_id']= $institute_id;
		}
		if($office_id)
		{
			$conditions['Territory.office_id'] = $office_id;
		}
		if($territory_id)
		{
			$conditions['Territory.id'] = $territory_id;
		}
		
		
		if($market_id)
		{
			$conditions['Outlet.market_id'] = $market_id;
		}
		$conditions['Outlet.is_active'] = 1;
		$conditions['Market.is_active'] = 1;
		$conditions['Outlet.is_ngo'] = 1;
		$this->Territory->unbindModel(
			array('hasMany' => array('Market','SaleTarget','SaleTargetMonth','TerritoryPerson'),'belongsTo'=>array('Office'))
		);
		
		
		
		$outlet_list = $this->Territory->find('all',array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'Market',
					'table' => 'markets',
					'type' => 'INNER',
					'conditions' => 'Market.territory_id = Territory.id'
				),
				array(
					'alias' => 'Outlet',
					'table' => 'outlets',
					'type' => 'INNER',
					'conditions' => 'Market.id = Outlet.market_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'Office.id = Territory.office_id'
					)
			),
			'order' => array('Outlet.name' => 'asc'),
			'fields' => array('Office.office_name','Territory.name','Market.name','Outlet.id','Outlet.name'),
			'group' => array('Office.office_name','Territory.name','Market.name','Outlet.id','Outlet.name')
		));
		
		/*echo $this->Territory->getLastQuery();
		pr($outlet_list);exit;*/
		
		$data_array = Set::extract($outlet_list, '{n}.Outlet');
		$html = '';
		$outlet_other_info=array();
		if(!empty($outlet_list)){
			foreach($outlet_list as $data)
			{
				$outlet_other_info[$data['Outlet']['id']]=array(
					'office'=>$data['Office']['office_name'],
					'territory'=>$data['Territory']['name'],
					'market'=>$data['Market']['name']
					);
			}
			$html .= '<option value="all">---- All ----</option>'; 
			foreach($data_array as $key=>$val){
				$html .= '<option value="'.$val['id'].'">'.$val['name'].'</option>';
			}
		}else{
			$html .= '<option value="">---- All ----</option>'; 
		}
		$json_array['outlet_html']=$html;
		$json_array['other_info']=$outlet_other_info;
		// echo $html;
		echo json_encode($json_array);
		$this->autoRender = false;
		
	}

	public function get_market_list(){
		$this->loadModel('Territory');
		$this->loadModel('Outlet');
		$institute_id = $this->request->data['institute_id'];
		$office_id = $this->request->data['office_id'];
		$territory_id = $this->request->data['territory_id'];
		
		
		if($institute_id)
		{
			$conditions = array(
					'Territory.office_id' => $office_id,
					'Territory.id' => $territory_id,
					'Outlet.institute_id' => $institute_id,
					'Outlet.is_ngo' => 1
				   );
		}
		else
		{
			$conditions = array(
					'Territory.office_id' => $office_id,
					'Territory.id' => $territory_id,
					//'Outlet.institute_id' => $institute_id,
					'Outlet.is_ngo' => 1
				   );
		}
		
		
		$this->Territory->unbindModel(
			array('hasMany' => array('Market','SaleTarget','SaleTargetMonth','TerritoryPerson'),'belongsTo'=>array('Office'))
		);
		
		
		$conditions['Outlet.is_active'] = 1;
		$conditions['Market.is_active'] = 1;
		$market_list = $this->Territory->find('all',array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'Market',
					'table' => 'markets',
					'type' => 'INNER',
					'conditions' => 'Market.territory_id = Territory.id'
				),
				array(
					'alias' => 'Outlet',
					'table' => 'outlets',
					'type' => 'INNER',
					'conditions' => 'Market.id = Outlet.market_id'
				)
			),
			'order' => array('Market.name' => 'asc'),
			'fields' => array('Market.id','Market.name'),
			'group' => array('Market.id','Market.name')
		));
	
		//pr($outlet_list);
		
		$data_array = Set::extract($market_list, '{n}.Market');
		$html = '';
		if(!empty($market_list)){
			$html .= '<option value="">---- Select ----</option>'; 
			foreach($data_array as $key=>$val){
				$html .= '<option value="'.$val['id'].'">'.$val['name'].'</option>';
			}
		}else{
			$html .= '<option value="">---- Select ----</option>'; 
		}
		echo $html;
		$this->autoRender = false;
		
	}
	public function get_territory_list(){
		$this->loadModel('Territory');
		$rs = array(array('id' => '', 'name' => '---- Select Territory -----'));
		$office_id = $this->request->data['office_id'];
		$territory_list = $this->Territory->find('all',array(
			'conditions' => array('Territory.office_id' => $office_id,'Territory.is_active'=>1),
			'order' => array('name' => 'asc'),
			'recursive' => -1
		));
		$data_array = Set::extract($territory_list,'{n}.Territory');
		
		if(!empty($territory_list)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}
	public function admin_view_price($id = null, $product_price_id = null) 
	{
		
		$this->set('page_title','View Product Price');	
		$this->loadModel('ProductCombination');
		
		$info = $this->ProductPrice->find('first', array('conditions' => array('ProductPrice.id' => $product_price_id)));
		$update_effective_date = $info['ProductPrice']['effective_date'];
		/*------------ start update prev date ------------*/
		$update_prev_date_list = $this->ProductPrice->find('all',
			array(
				'conditions' => array('ProductPrice.product_id' => $id,'ProductPrice.effective_date <' => $update_effective_date,'ProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
				'fields' => array('ProductPrice.effective_date')
				)
			);
		if(!empty($update_prev_date_list)){
			$update_prev_short_list = array();
			foreach($update_prev_date_list as $update_prev_date_key => $update_prev_date_val){
				array_push($update_prev_short_list,$update_prev_date_val['ProductPrice']['effective_date']);
			}
			asort($update_prev_short_list);
			$update_prev_date = end($update_prev_short_list);
			/* ----------- end get prev less date ----------*/
			/*----------- start get prev data -----------*/
			$update_prev_data = $this->ProductPrice->find('first',
				array(
					'conditions' => array('ProductPrice.product_id' => $id,'ProductPrice.effective_date' => $update_prev_date,'ProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
					'fields' => array('ProductPrice.effective_date')
					)
				);
			/* echo "<pre>";
			print_r($update_prev_data);
			exit; */
			/*--------- end get prev data -------------*/
		}
		/*----------- end update prev date --------------*/
		/*---------- start update next date ------------*/
		$update_next_date_list = $this->ProductPrice->find('all',
			array(
				'conditions' => array('ProductPrice.product_id' => $id,'ProductPrice.effective_date >' => $update_effective_date,'ProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
				'fields' => array('ProductPrice.effective_date')
				)
			);
		if(!empty($update_next_date_list)){
			$update_next_short_list = array();
			foreach($update_next_date_list as $update_next_date_key => $update_next_date_val){
				array_push($update_next_short_list,$update_next_date_val['ProductPrice']['effective_date']);
			}
			rsort($update_next_short_list);
			$update_next_date = end($update_next_short_list);
			/* ----------- end get prev less date ----------*/
			/*----------- start get prev data -----------*/
			$update_next_data = $this->ProductPrice->find('first',
				array(
					'conditions' => array('ProductPrice.product_id' => $id,'ProductPrice.effective_date' => $update_next_date,'ProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
					'fields' => array('ProductPrice.effective_date')
					)
				);
			/*--------- end get prev data -------------*/
		}
		/*---------- end update next date ------------*/
		if(isset($info['ProductPrice']['id'])!='')
		{
			$this->ProductPrice->id = $info['ProductPrice']['id'];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->request->data['ProductPrice']['updated_by'] = $this->UserAuth->getUserId();
				$this->request->data['ProductPrice']['updated_at'] = $this->current_datetime();
				$this->request->data['ProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['ProductPrice']['effective_date']));
				if($this->ProductPrice->save($this->request->data)) {
					if(!empty($this->request->data['ProductCombination'])){
						$update_data['ProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['ProductPrice']['effective_date']));
						if(isset($this->request->data['ProductCombination']['update_min_qty'])){
							$update_data_array = array();
							$update_data['ProductCombination']['updated_by'] = $this->UserAuth->getUserId();
							$update_data['ProductCombination']['updated_at'] = $this->current_datetime();
							foreach($this->request->data['ProductCombination']['update_min_qty'] as $ukey=>$uval)
							{
								$update_data['ProductCombination']['id'] = $ukey;
								$update_data['ProductCombination']['min_qty'] = $this->request->data['ProductCombination']['update_min_qty'][$ukey];
								$update_data['ProductCombination']['product_id'] = $info['ProductPrice']['product_id'];
								$update_data['ProductCombination']['price'] = $this->request->data['ProductCombination']['update_price'][$ukey];
								$update_data_array[] = $update_data;
							}												
							$this->ProductCombination->saveAll($update_data_array);
							
						}
						if(isset($this->request->data['ProductCombination']['min_qty'])){
							$data['ProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['ProductPrice']['effective_date']));
							$data['ProductCombination']['product_id'] = $id;
							$data_array = array();
							$data['ProductCombination']['created_by'] = $this->UserAuth->getUserId();
							$data['ProductCombination']['updated_at'] = $this->current_datetime();
							$data['ProductCombination']['created_at'] = $this->current_datetime();
							foreach($this->request->data['ProductCombination']['min_qty'] as $key=>$val)
							{
								$data['ProductCombination']['product_price_id'] = $this->ProductPrice->id;
								$data['ProductCombination']['min_qty'] = $val;
								$data['ProductCombination']['price'] = $this->request->data['ProductCombination']['price'][$key];
								$data_array[] = $data;
							}														
							$this->ProductCombination->saveAll($data_array);
						}
						/*-------- start update prev row by update next row --------*/
						if(!empty($update_next_date) && !empty($update_prev_date)){
							$update_prev_end_date['ProductPrice']['id'] = $update_prev_data['ProductPrice']['id'];
							$update_prev_end_date['ProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($update_next_data['ProductPrice']['effective_date'])))));
							if($this->ProductPrice->save($update_prev_end_date)){
								foreach($update_prev_data['ProductCombination'] as $prev_update_val){
									$update_product_combination['ProductCombination']['id'] = $prev_update_val['id'];
									$update_product_combination['ProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($update_next_data['ProductPrice']['effective_date'])))));
									$this->ProductCombination->save($update_product_combination);
								}
							}
						}
						if(empty($update_next_date) && !empty($update_prev_date)){
							$update_prev_end_date['ProductPrice']['id'] = $update_prev_data['ProductPrice']['id'];
							$update_prev_end_date['ProductPrice']['end_date'] = NULL;
							if($this->ProductPrice->save($update_prev_end_date)){
								foreach($update_prev_data['ProductCombination'] as $prev_update_val){
									$update_product_combination['ProductCombination']['id'] = $prev_update_val['id'];
									$update_product_combination['ProductCombination']['end_date'] = NULL;
									$this->ProductCombination->save($update_product_combination);
								}
							}
						}
						/*-------- end update prev row by update next row --------*/
						$info_after_update = $this->ProductPrice->find('first', array('conditions' => array('ProductPrice.id' => $id,'ProductPrice.id' => $product_price_id)));
						$updated_effective_date = $info_after_update['ProductPrice']['effective_date'];
						/*------------ start updated prev date ------------*/
						$updated_prev_date_list = $this->ProductPrice->find('all',
							array(
								'conditions' => array('ProductPrice.product_id' => $id,'ProductPrice.effective_date <' => $updated_effective_date,'ProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
								'fields' => array('ProductPrice.effective_date')
								)
							);
						if(!empty($updated_prev_date_list)){
							$updated_prev_short_list = array();
							foreach($updated_prev_date_list as $updated_prev_date_key => $updated_prev_date_val){
								array_push($updated_prev_short_list,$updated_prev_date_val['ProductPrice']['effective_date']);
							}
							asort($updated_prev_short_list);
							$updated_prev_date = end($updated_prev_short_list);
							/* ----------- end get prev less date ----------*/
							/*----------- start get prev data -----------*/
							$updated_prev_data = $this->ProductPrice->find('first',
								array(
									'conditions' => array('ProductPrice.product_id' => $id,'ProductPrice.effective_date' => $updated_prev_date,'ProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
									'fields' => array('ProductPrice.effective_date')
									)
								);
							/*--------- end get prev data -------------*/
						}
						/*----------- end update prev date --------------*/
						/*---------- start update next date ------------*/
						$updated_next_date_list = $this->ProductPrice->find('all',
							array(
								'conditions' => array('ProductPrice.product_id' => $id,'ProductPrice.effective_date >' => $updated_effective_date,'ProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
								'fields' => array('ProductPrice.effective_date')
								)
							);
						if(!empty($updated_next_date_list)){
							$updated_next_short_list = array();
							foreach($updated_next_date_list as $updated_next_date_key => $updated_next_date_val){
								array_push($updated_next_short_list,$updated_next_date_val['ProductPrice']['effective_date']);
							}
							rsort($updated_next_short_list);
							$updated_next_date = end($updated_next_short_list);
							/* ----------- end get prev less date ----------*/
							/*----------- start get prev data -----------*/
							$updated_next_data = $this->ProductPrice->find('first',
								array(
									'conditions' => array('ProductPrice.product_id' => $id,'ProductPrice.effective_date' => $updated_next_date,'ProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
									'fields' => array('ProductPrice.effective_date')
									)
								);
							//echo $updated_next_date;
							//exit;
							/*--------- end get prev data -------------*/
						}
						/*---------- end update next date ------------*/
						/*-------- start updated prev row by updated next row --------*/
						if(!empty($updated_prev_date)){
							$updated_prev_end_date['ProductPrice']['id'] = $updated_prev_data['ProductPrice']['id'];
							$updated_prev_end_date['ProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_effective_date)))));
							if($this->ProductPrice->save($updated_prev_end_date)){
								foreach($updated_prev_data['ProductCombination'] as $prev_updated_val){
									$updated_product_combination['ProductCombination']['id'] = $prev_updated_val['id'];
									$updated_product_combination['ProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_effective_date)))));
									$this->ProductCombination->save($updated_product_combination);
								}
							}
						}
						if(!empty($updated_next_date)){
							$update_current_row['ProductPrice']['id'] = $product_price_id;
							$update_current_row['ProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_next_data['ProductPrice']['effective_date'])))));
							if($this->ProductPrice->save($update_current_row)){
								foreach($info['ProductCombination'] as $val){
									$current_product_combination['ProductCombination']['id'] = $val['id'];
									$current_product_combination['ProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_next_data['ProductPrice']['effective_date'])))));
									$this->ProductCombination->save($current_product_combination);
								}
							}
							if(!empty($updated_next_data)){
								$update_next_row['ProductPrice']['id'] = $updated_next_data['ProductPrice']['id'];
								$update_next_row['ProductPrice']['end_date'] = NULL;
								//print_r($update_next_row);
								//exit;
								if($this->ProductPrice->save($update_next_row)){
									foreach($updated_next_data['ProductCombination'] as $next_val){
										$next_product_combination['ProductCombination']['id'] = $next_val['id'];
										$next_product_combination['ProductCombination']['end_date'] = NULL;
										$this->ProductCombination->save($next_product_combination);
									}
								}
							}
						}else{
							$update_current_row['ProductPrice']['id'] = $product_price_id;
							$update_current_row['ProductPrice']['end_date'] = NULL;
							if($this->ProductPrice->save($update_current_row)){
								foreach($info['ProductCombination'] as $val){
									$current_product_combination['ProductCombination']['id'] = $val['id'];
									$current_product_combination['ProductCombination']['end_date'] = NULL;
									$this->ProductCombination->save($current_product_combination);
								}
							}
						}
						/*-------- end updated prev row by updated next row --------*/
					}
					
					$this->Session->setFlash(__('The product price has been saved'), 'flash/success');
					$this->redirect(array('action' => 'index/'.$id));
				}
			}		
			$options = array('conditions' => array('ProductPrice.' . $this->ProductPrice->primaryKey => $info['ProductPrice']['id']));
			$this->request->data = $this->ProductPrice->find('first', $options);
			
		}
	}
}