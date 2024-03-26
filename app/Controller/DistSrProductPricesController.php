<?php
App::uses('AppController', 'Controller');
/**
 * ProductPrices Controller
 *
 * @property ProductPrice $ProductPrice
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistSrProductPricesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session','Filter.Filter');
	public $uses =  array('Product','DistSrProductPrice');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','Product Price List');
		$this->Product->recursive = 0;
		
		$this->paginate = array('conditions'=>array('Product.is_active'=> 1,'Product.is_distributor_product'=> 1),'order' => array('Product.order' => 'ASC'));
		$this->set('products', $this->paginate());
		
		// $productCategories = $this->Product->ProductCategory->generateTreeList(null, null, null, '__');
		$productCategories = $this->Product->ProductCategory->find('list');
		$this->set(compact('productCategories'));	
	}
	
	public function admin_price_list($id = null){
		$this->set('page_title','Price List');
		$this->loadModel('DistSrProductPrice');
		$this->loadModel('Product');
		$this->DistSrProductPrice->recursive = -1;
		$this->paginate = array('order' => array('DistSrProductPrice.id' => 'DESC'),'conditions'=> array('DistSrProductPrice.product_id' => $id,'DistSrProductPrice.has_combination' => 0,'DistSrProductPrice.institute_id' => 0));
		$this->set('product_prices', $this->paginate('DistSrProductPrice'));
		$product=$this->Product->find('first',array('conditions'=>array('Product.id'=>$id),'recursive'=>-1));
		$this->set('product',$product);
		$this->set('id',$id);
	}
	/**
 * admin_set_price_slot method
 *
 * @return void
 */
	public function admin_set_unique_price($id = null,$product_price_id = null){
		$this->loadModel('DistSrProductCombination');
		$this->set('product_id',$id);
		if ($this->request->is('post')) 
		{
			//pr($this->request->data);die();
			/*---------- start get immediate Prev date ---------*/
			$new_start_date = date('Y-m-d',strtotime($this->request->data['DistSrProductPrice']['effective_date']));
			//$immediate_pre_date;
			$effective_date_list = $this->DistSrProductPrice->find('all',
				array(
					'conditions' => array('DistSrProductPrice.product_id' => $id,'DistSrProductPrice.effective_date <' => $new_start_date,'DistSrProductPrice.institute_id' => 0,'DistSrProductPrice.has_combination' => 0),
					'fields' => array('DistSrProductPrice.effective_date')
				)
			);
			
			$short_list = array();
			if(!empty($effective_date_list)){
				foreach($effective_date_list as $date_key => $date_val){
					array_push($short_list,$date_val['DistSrProductPrice']['effective_date']);
				}
				asort($short_list);
				$immediate_pre_date = array_pop($short_list);
				/*---------- end get immediate less date ---------*/
				/*---------- start immediate start date data --------*/
				$effective_price_data = $this->DistSrProductPrice->find('first',
					array(
						'conditions' => array('DistSrProductPrice.product_id' => $id,'DistSrProductPrice.effective_date' => $immediate_pre_date,'DistSrProductPrice.institute_id' => 0,'DistSrProductPrice.has_combination' => 0),
						'fields' => array('DistSrProductPrice.effective_date')
					)
				);
			}
			/*---------- end immediate start date data --------*/
			$pre_end_date = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($this->request->data['DistSrProductPrice']['effective_date'])))));

			$this->request->data['DistSrProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['DistSrProductPrice']['effective_date']));
			$this->request->data['DistSrProductPrice']['product_id'] = $id; 
			$this->request->data['DistSrProductPrice']['created_at'] = $this->current_datetime(); 
			$this->request->data['DistSrProductPrice']['updated_at'] = $this->current_datetime(); 
			$this->request->data['DistSrProductPrice']['created_by'] = $this->UserAuth->getUserId();
			$this->DistSrProductPrice->create();
			if ($this->DistSrProductPrice->save($this->request->data)) {
				$inserted_product_price_id = $this->DistSrProductPrice->getInsertID();
				if(!empty($this->request->data['DistSrProductCombination'])){
					$data_array = array();
					$data['DistSrProductCombination']['product_id'] = $id;
					$data['DistSrProductCombination']['created_at'] = $this->current_datetime(); 
					$data['DistSrProductCombination']['updated_at'] = $this->current_datetime(); 
					$data['DistSrProductCombination']['created_by'] = $this->UserAuth->getUserId();
					$data['DistSrProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['DistSrProductPrice']['effective_date']));
					foreach($this->request->data['DistSrProductCombination']['min_qty'] as $key=>$val)
					{
						$data['DistSrProductCombination']['product_price_id'] = $this->DistSrProductPrice->id;
						$data['DistSrProductCombination']['min_qty'] = $val;
						$data['DistSrProductCombination']['price'] = $this->request->data['DistSrProductCombination']['price'][$key];
						$data_array[] = $data;
					}								
					$this->DistSrProductCombination->saveAll($data_array);
					/*----------- start update prev row by end date -----------*/
					if(!empty($effective_price_data)){
						$update_pre_row['DistSrProductPrice']['id'] = $effective_price_data['DistSrProductPrice']['id'];
						$update_pre_row['DistSrProductPrice']['end_date'] = $pre_end_date;
						if($this->DistSrProductPrice->save($update_pre_row)){
							
							foreach($effective_price_data['DistSrProductCombination'] as $prev_key=>$prev_val){
								$update_combination_prev_row['DistSrProductCombination']['id'] = $prev_val['id'];
								$update_combination_prev_row['DistSrProductCombination']['end_date'] = $pre_end_date;
								$this->DistSrProductCombination->save($update_combination_prev_row);
							}
						}
					}
					/*----------- start recent inserted data -----------*/
					$recent_inserted_data = $this->DistSrProductPrice->find('first',
						array(
							'conditions' => array('DistSrProductPrice.id' => $inserted_product_price_id),
							'fields' => array('DistSrProductPrice.effective_date')
						)
					);

					/*---------- end recent inserted data -------------*/
					/*---------- start next effective_date list ---------*/
					$effective_next_date_list = $this->DistSrProductPrice->find('all',
						array(
							'conditions' => array('DistSrProductPrice.product_id' => $id,'DistSrProductPrice.effective_date >' => $recent_inserted_data['DistSrProductPrice']['effective_date'],'DistSrProductPrice.institute_id' => 0,'DistSrProductPrice.has_combination' => 0),
							'fields' => array('DistSrProductPrice.effective_date')
						)
					);

					$next_short_list = array();
					if(!empty($effective_next_date_list)){
						foreach($effective_next_date_list as $next_date_key => $next_date_val){
							array_push($next_short_list,$next_date_val['DistSrProductPrice']['effective_date']);
						}
						rsort($next_short_list);
						$immidiat_next_date = array_pop($next_short_list);
						/*---------- end get immediate less date ---------*/
					}

					/*----------- start update recent inserted row by end date -----------*/
					if(!empty($immidiat_next_date)){
						$end_date_of_current_row = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immidiat_next_date)))));
						$update_current_row['DistSrProductPrice']['id'] = $recent_inserted_data['DistSrProductPrice']['id'];
						$update_current_row['DistSrProductPrice']['end_date'] = $end_date_of_current_row;
						if($this->DistSrProductPrice->save($update_current_row)){
							
							foreach($recent_inserted_data['DistSrProductCombination'] as $next_key=>$next_val){
								$update_combination_next_row['DistSrProductCombination']['id'] = $next_val['id'];
								$update_combination_next_row['DistSrProductCombination']['end_date'] = $end_date_of_current_row;
								$this->DistSrProductCombination->save($update_combination_next_row);
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
		         $this->loadModel('DistSrProductCombination');
				 $this->loadModel('MemoDetail');
				 
				 
				 				
		          /* check product_price_id is existed in memo_details table */
		       if($product_price_id)
			   {
				   
				$sql="select count(*) as id_num from memo_details md
					inner join dist_product_combinations pc on pc.id=md.product_price_id
					inner join memos m on m.id=md.memo_id
					 where pc.product_price_id=$product_price_id and m.is_distributor=1";				    
				$data_sql = $this->MemoDetail->Query($sql);
				
				$update_allow=1;
				
				if($data_sql[0][0]['id_num']>0)
				{
					$update_allow=0;
				}
				
				$this->set('update_allow', $update_allow);
			
			   }
				
				
		
		
		
		
		$info = $this->DistSrProductPrice->find('first', array('conditions' => array('DistSrProductPrice.id' => $product_price_id)));
		$update_effective_date = $info['DistSrProductPrice']['effective_date'];
		/*------------ start update prev date ------------*/
		$update_prev_date_list = $this->DistSrProductPrice->find('all',
			array(
				'conditions' => array('DistSrProductPrice.product_id' => $id,'DistSrProductPrice.effective_date <' => $update_effective_date,'DistSrProductPrice.institute_id' => 0,'DistSrProductPrice.has_combination' => 0),
				'fields' => array('DistSrProductPrice.effective_date')
			)
		);
		if(!empty($update_prev_date_list)){
			$update_prev_short_list = array();
			foreach($update_prev_date_list as $update_prev_date_key => $update_prev_date_val){
				array_push($update_prev_short_list,$update_prev_date_val['DistSrProductPrice']['effective_date']);
			}
			asort($update_prev_short_list);
			$update_prev_date = end($update_prev_short_list);
			/* ----------- end get prev less date ----------*/
			/*----------- start get prev data -----------*/
			$update_prev_data = $this->DistSrProductPrice->find('first',
				array(
					'conditions' => array('DistSrProductPrice.product_id' => $id,'DistSrProductPrice.effective_date' => $update_prev_date,'DistSrProductPrice.institute_id' => 0,'DistSrProductPrice.has_combination' => 0),
					'fields' => array('DistSrProductPrice.effective_date')
				)
			);
			/* echo "<pre>";
			print_r($update_prev_data);
			exit; */
			/*--------- end get prev data -------------*/
		}
		/*----------- end update prev date --------------*/
		/*---------- start update next date ------------*/
		$update_next_date_list = $this->DistSrProductPrice->find('all',
			array(
				'conditions' => array('DistSrProductPrice.product_id' => $id,'DistSrProductPrice.effective_date >' => $update_effective_date,'DistSrProductPrice.institute_id' => 0,'DistSrProductPrice.has_combination' => 0),
				'fields' => array('DistSrProductPrice.effective_date')
			)
		);
		if(!empty($update_next_date_list)){
			$update_next_short_list = array();
			foreach($update_next_date_list as $update_next_date_key => $update_next_date_val){
				array_push($update_next_short_list,$update_next_date_val['DistSrProductPrice']['effective_date']);
			}
			rsort($update_next_short_list);
			$update_next_date = end($update_next_short_list);
			/* ----------- end get prev less date ----------*/
			/*----------- start get prev data -----------*/
			$update_next_data = $this->DistSrProductPrice->find('first',
				array(
					'conditions' => array('DistSrProductPrice.product_id' => $id,'DistSrProductPrice.effective_date' => $update_next_date,'DistSrProductPrice.institute_id' => 0,'DistSrProductPrice.has_combination' => 0),
					'fields' => array('DistSrProductPrice.effective_date')
				)
			);
			/*--------- end get prev data -------------*/
		}
		/*---------- end update next date ------------*/
		if(isset($info['DistSrProductPrice']['id'])!='')
		{
			$this->DistSrProductPrice->id = $info['DistSrProductPrice']['id'];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->request->data['DistSrProductPrice']['updated_by'] = $this->UserAuth->getUserId();
				$this->request->data['DistSrProductPrice']['updated_at'] = $this->current_datetime();
				$this->request->data['DistSrProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['DistSrProductPrice']['effective_date']));
				if($this->DistSrProductPrice->save($this->request->data)) {
					if(!empty($this->request->data['DistSrProductCombination'])){
						$update_data['DistSrProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['DistSrProductPrice']['effective_date']));
						if(isset($this->request->data['DistSrProductCombination']['update_min_qty'])){
							$update_data_array = array();
							$update_data['DistSrProductCombination']['updated_by'] = $this->UserAuth->getUserId();
							$update_data['DistSrProductCombination']['updated_at'] = $this->current_datetime();
							foreach($this->request->data['DistSrProductCombination']['update_min_qty'] as $ukey=>$uval)
							{
								$update_data['DistSrProductCombination']['id'] = $ukey;
								$update_data['DistSrProductCombination']['min_qty'] = $this->request->data['DistSrProductCombination']['update_min_qty'][$ukey];
								$update_data['DistSrProductCombination']['product_id'] = $info['DistSrProductPrice']['product_id'];
								$update_data['DistSrProductCombination']['price'] = $this->request->data['DistSrProductCombination']['update_price'][$ukey];
								$update_data_array[] = $update_data;
							}												
							$this->DistSrProductCombination->saveAll($update_data_array);
							
						}
						if(isset($this->request->data['DistSrProductCombination']['min_qty'])){
							$data['DistSrProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['DistSrProductPrice']['effective_date']));
							$data['DistSrProductCombination']['product_id'] = $id;
							$data_array = array();
							$data['DistSrProductCombination']['created_by'] = $this->UserAuth->getUserId();
							$data['DistSrProductCombination']['updated_at'] = $this->current_datetime();
							$data['DistSrProductCombination']['created_at'] = $this->current_datetime();
							foreach($this->request->data['DistSrProductCombination']['min_qty'] as $key=>$val)
							{
								$data['DistSrProductCombination']['product_price_id'] = $this->DistSrProductPrice->id;
								$data['DistSrProductCombination']['min_qty'] = $val;
								$data['DistSrProductCombination']['price'] = $this->request->data['DistSrProductCombination']['price'][$key];
								$data_array[] = $data;
							}														
							$this->DistSrProductCombination->saveAll($data_array);
						}
						/*-------- start update prev row by update next row --------*/
						if(!empty($update_next_date) && !empty($update_prev_date)){
							$update_prev_end_date['DistSrProductPrice']['id'] = $update_prev_data['DistSrProductPrice']['id'];
							$update_prev_end_date['DistSrProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($update_next_data['DistSrProductPrice']['effective_date'])))));
							if($this->DistSrProductPrice->save($update_prev_end_date)){
								foreach($update_prev_data['DistSrProductCombination'] as $prev_update_val){
									$update_product_combination['DistSrProductCombination']['id'] = $prev_update_val['id'];
									$update_product_combination['DistSrProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($update_next_data['DistSrProductPrice']['effective_date'])))));
									$this->DistSrProductCombination->save($update_product_combination);
								}
							}
						}
						if(empty($update_next_date) && !empty($update_prev_date)){
							$update_prev_end_date['DistSrProductPrice']['id'] = $update_prev_data['DistSrProductPrice']['id'];
							$update_prev_end_date['DistSrProductPrice']['end_date'] = NULL;
							if($this->DistSrProductPrice->save($update_prev_end_date)){
								foreach($update_prev_data['DistSrProductCombination'] as $prev_update_val){
									$update_product_combination['DistSrProductCombination']['id'] = $prev_update_val['id'];
									$update_product_combination['DistSrProductCombination']['end_date'] = NULL;
									$this->DistSrProductCombination->save($update_product_combination);
								}
							}
						}
						/*-------- end update prev row by update next row --------*/
						$info_after_update = $this->DistSrProductPrice->find('first', array('conditions' => array('DistSrProductPrice.id' => $id,'DistSrProductPrice.id' => $product_price_id)));
						$updated_effective_date = $info_after_update['DistSrProductPrice']['effective_date'];
						/*------------ start updated prev date ------------*/
						$updated_prev_date_list = $this->DistSrProductPrice->find('all',
							array(
								'conditions' => array('DistSrProductPrice.product_id' => $id,'DistSrProductPrice.effective_date <' => $updated_effective_date,'DistSrProductPrice.institute_id' => 0,'DistSrProductPrice.has_combination' => 0),
								'fields' => array('DistSrProductPrice.effective_date')
							)
						);
						if(!empty($updated_prev_date_list)){
							$updated_prev_short_list = array();
							foreach($updated_prev_date_list as $updated_prev_date_key => $updated_prev_date_val){
								array_push($updated_prev_short_list,$updated_prev_date_val['DistSrProductPrice']['effective_date']);
							}
							asort($updated_prev_short_list);
							$updated_prev_date = end($updated_prev_short_list);
							/* ----------- end get prev less date ----------*/
							/*----------- start get prev data -----------*/
							$updated_prev_data = $this->DistSrProductPrice->find('first',
								array(
									'conditions' => array('DistSrProductPrice.product_id' => $id,'DistSrProductPrice.effective_date' => $updated_prev_date,'DistSrProductPrice.institute_id' => 0,'DistSrProductPrice.has_combination' => 0),
									'fields' => array('DistSrProductPrice.effective_date')
								)
							);
							/*--------- end get prev data -------------*/
						}
						/*----------- end update prev date --------------*/
						/*---------- start update next date ------------*/
						$updated_next_date_list = $this->DistSrProductPrice->find('all',
							array(
								'conditions' => array('DistSrProductPrice.product_id' => $id,'DistSrProductPrice.effective_date >' => $updated_effective_date,'DistSrProductPrice.institute_id' => 0,'DistSrProductPrice.has_combination' => 0),
								'fields' => array('DistSrProductPrice.effective_date')
							)
						);
						if(!empty($updated_next_date_list)){
							$updated_next_short_list = array();
							foreach($updated_next_date_list as $updated_next_date_key => $updated_next_date_val){
								array_push($updated_next_short_list,$updated_next_date_val['DistSrProductPrice']['effective_date']);
							}
							rsort($updated_next_short_list);
							$updated_next_date = end($updated_next_short_list);
							/* ----------- end get prev less date ----------*/
							/*----------- start get prev data -----------*/
							$updated_next_data = $this->DistSrProductPrice->find('first',
								array(
									'conditions' => array('DistSrProductPrice.product_id' => $id,'DistSrProductPrice.effective_date' => $updated_next_date,'DistSrProductPrice.institute_id' => 0,'DistSrProductPrice.has_combination' => 0),
									'fields' => array('DistSrProductPrice.effective_date')
								)
							);
							//echo $updated_next_date;
							//exit;
							/*--------- end get prev data -------------*/
						}
						/*---------- end update next date ------------*/
						/*-------- start updated prev row by updated next row --------*/
						if(!empty($updated_prev_date)){
							$updated_prev_end_date['DistSrProductPrice']['id'] = $updated_prev_data['DistSrProductPrice']['id'];
							$updated_prev_end_date['DistSrProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_effective_date)))));
							if($this->DistSrProductPrice->save($updated_prev_end_date)){
								foreach($updated_prev_data['DistSrProductCombination'] as $prev_updated_val){
									$updated_product_combination['DistSrProductCombination']['id'] = $prev_updated_val['id'];
									$updated_product_combination['DistSrProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_effective_date)))));
									$this->DistSrProductCombination->save($updated_product_combination);
								}
							}
						}
						if(!empty($updated_next_date)){
							$update_current_row['DistSrProductPrice']['id'] = $product_price_id;
							$update_current_row['DistSrProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_next_data['DistSrProductPrice']['effective_date'])))));
							if($this->DistSrProductPrice->save($update_current_row)){
								foreach($info['DistSrProductCombination'] as $val){
									$current_product_combination['DistSrProductCombination']['id'] = $val['id'];
									$current_product_combination['DistSrProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_next_data['DistSrProductPrice']['effective_date'])))));
									$this->DistSrProductCombination->save($current_product_combination);
								}
							}
							if(!empty($updated_next_data)){
								$update_next_row['DistSrProductPrice']['id'] = $updated_next_data['DistSrProductPrice']['id'];
								$update_next_row['DistSrProductPrice']['end_date'] = NULL;
								//print_r($update_next_row);
								//exit;
								if($this->DistSrProductPrice->save($update_next_row)){
									foreach($updated_next_data['DistSrProductCombination'] as $next_val){
										$next_product_combination['DistSrProductCombination']['id'] = $next_val['id'];
										$next_product_combination['DistSrProductCombination']['end_date'] = NULL;
										$this->DistSrProductCombination->save($next_product_combination);
									}
								}
							}
						}else{
							$update_current_row['DistSrProductPrice']['id'] = $product_price_id;
							$update_current_row['DistSrProductPrice']['end_date'] = NULL;
							if($this->DistSrProductPrice->save($update_current_row)){
								foreach($info['DistSrProductCombination'] as $val){
									$current_product_combination['DistSrProductCombination']['id'] = $val['id'];
									$current_product_combination['DistSrProductCombination']['end_date'] = NULL;
									$this->DistSrProductCombination->save($current_product_combination);
								}
							}
						}
						/*-------- end updated prev row by updated next row --------*/
					}
					
					$this->Session->setFlash(__('The product price has been saved'), 'flash/success');
					$this->redirect(array('action' => 'index/'.$id));
				}
			}		
			$options = array('conditions' => array('DistSrProductPrice.' . $this->DistSrProductPrice->primaryKey => $info['DistSrProductPrice']['id']));
			$this->request->data = $this->DistSrProductPrice->find('first', $options);
			
		}else{
			if ($this->request->is('post')) {
				$this->request->data['DistSrProductPrice']['product_id'] = $id; 
				$this->request->data['DistSrProductPrice']['created_at'] = $this->current_datetime(); 
				$this->request->data['DistSrProductPrice']['updated_at'] = $this->current_datetime(); 
				$this->request->data['DistSrProductPrice']['created_by'] = $this->UserAuth->getUserId();
				$this->request->data['DistSrProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['DistSrProductPrice']['effective_date']));
				$this->DistSrProductPrice->create();
				if ($this->DistSrProductPrice->save($this->request->data)) {
					
					if(!empty($this->request->data['DistSrProductCombination'])){
						$data_array = array();
						$data['DistSrProductCombination']['product_id'] = $id;
						$data['DistSrProductCombination']['created_at'] = $this->current_datetime();
						$data['DistSrProductCombination']['updated_at'] = $this->current_datetime();
						$data['DistSrProductCombination']['created_by'] = $this->UserAuth->getUserId();
						$data['DistSrProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['DistSrProductPrice']['effective_date']));
						foreach($this->request->data['DistSrProductCombination']['min_qty'] as $key=>$val)
						{
							$data['DistSrProductCombination']['product_price_id'] = $this->DistSrProductPrice->id;
							$data['DistSrProductCombination']['min_qty'] = $val;
							$data['DistSrProductCombination']['price'] = $this->request->data['DistSrProductCombination']['price'][$key];
							$data_array[] = $data;
						}								
						$this->DistSrProductCombination->saveAll($data_array); 
					}
					
					$this->Session->setFlash(__('The product price has been saved'), 'flash/success');
					$this->redirect(array('action' => 'index/'.$id));
				}
			}
		}
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
				)
			),
			'order' => array('Outlet.name' => 'asc'),
			'fields' => array('Territory.*','Market.*','Outlet.id','Outlet.name')
		));
		
		//pr($outlet_list);
		
		$data_array = Set::extract($outlet_list, '{n}.Outlet');
		$html = '';
		if(!empty($outlet_list)){
			$html .= '<option value="all">---- All ----</option>'; 
			foreach($data_array as $key=>$val){
				$html .= '<option value="'.$val['id'].'">'.$val['name'].'</option>';
			}
		}else{
			$html .= '<option value="">---- All ----</option>'; 
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
		$this->loadModel('DistSrProductCombination');
		//naser 
		$info = $this->DistSrProductPrice->find('first', array('conditions' => array('DistSrProductPrice.id' => $product_price_id)));
		$update_effective_date = $info['DistSrProductPrice']['effective_date'];
		/*------------ start update prev date ------------*/
		$update_prev_date_list = $this->DistSrProductPrice->find('all',
			array(
				'conditions' => array('DistSrProductPrice.product_id' => $id,'DistSrProductPrice.effective_date <' => $update_effective_date,'DistSrProductPrice.institute_id' => 0,'DistSrProductPrice.has_combination' => 0),
				'fields' => array('DistSrProductPrice.effective_date')
				)
			);
		if(!empty($update_prev_date_list)){
			$update_prev_short_list = array();
			foreach($update_prev_date_list as $update_prev_date_key => $update_prev_date_val){
				array_push($update_prev_short_list,$update_prev_date_val['DistSrProductPrice']['effective_date']);
			}
			asort($update_prev_short_list);
			$update_prev_date = end($update_prev_short_list);
			/* ----------- end get prev less date ----------*/
			/*----------- start get prev data -----------*/
			$update_prev_data = $this->DistSrProductPrice->find('first',
				array(
					'conditions' => array('DistSrProductPrice.product_id' => $id,'DistSrProductPrice.effective_date' => $update_prev_date,'DistSrProductPrice.institute_id' => 0,'DistSrProductPrice.has_combination' => 0),
					'fields' => array('DistSrProductPrice.effective_date')
					)
				);
			/* echo "<pre>";
			print_r($update_prev_data);
			exit; */
			/*--------- end get prev data -------------*/
		}
		/*----------- end update prev date --------------*/
		/*---------- start update next date ------------*/
		$update_next_date_list = $this->DistSrProductPrice->find('all',
			array(
				'conditions' => array('DistSrProductPrice.product_id' => $id,'DistSrProductPrice.effective_date >' => $update_effective_date,'DistSrProductPrice.institute_id' => 0,'DistSrProductPrice.has_combination' => 0),
				'fields' => array('DistSrProductPrice.effective_date')
				)
			);
		if(!empty($update_next_date_list)){
			$update_next_short_list = array();
			foreach($update_next_date_list as $update_next_date_key => $update_next_date_val){
				array_push($update_next_short_list,$update_next_date_val['DistSrProductPrice']['effective_date']);
			}
			rsort($update_next_short_list);
			$update_next_date = end($update_next_short_list);
			/* ----------- end get prev less date ----------*/
			/*----------- start get prev data -----------*/
			$update_next_data = $this->DistSrProductPrice->find('first',
				array(
					'conditions' => array('DistSrProductPrice.product_id' => $id,'DistSrProductPrice.effective_date' => $update_next_date,'DistSrProductPrice.institute_id' => 0,'DistSrProductPrice.has_combination' => 0),
					'fields' => array('DistSrProductPrice.effective_date')
					)
				);
			/*--------- end get prev data -------------*/
		}
		/*---------- end update next date ------------*/
		if(isset($info['DistSrProductPrice']['id'])!='')
		{
			$this->DistSrProductPrice->id = $info['DistSrProductPrice']['id'];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->request->data['DistSrProductPrice']['updated_by'] = $this->UserAuth->getUserId();
				$this->request->data['DistSrProductPrice']['updated_at'] = $this->current_datetime();
				$this->request->data['DistSrProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['DistSrProductPrice']['effective_date']));
				if($this->ProductPrice->save($this->request->data)) {
					if(!empty($this->request->data['DistSrProductCombination'])){
						$update_data['DistSrProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['DistSrProductPrice']['effective_date']));
						if(isset($this->request->data['DistSrProductCombination']['update_min_qty'])){
							$update_data_array = array();
							$update_data['DistSrProductCombination']['updated_by'] = $this->UserAuth->getUserId();
							$update_data['DistSrProductCombination']['updated_at'] = $this->current_datetime();
							foreach($this->request->data['DistSrProductCombination']['update_min_qty'] as $ukey=>$uval)
							{
								$update_data['DistSrProductCombination']['id'] = $ukey;
								$update_data['DistSrProductCombination']['min_qty'] = $this->request->data['DistSrProductCombination']['update_min_qty'][$ukey];
								$update_data['DistSrProductCombination']['product_id'] = $info['ProductPrice']['product_id'];
								$update_data['DistSrProductCombination']['price'] = $this->request->data['DistSrProductCombination']['update_price'][$ukey];
								$update_data_array[] = $update_data;
							}												
							$this->DistSrProductCombination->saveAll($update_data_array);
							
						}
						if(isset($this->request->data['DistSrProductCombination']['min_qty'])){
							$data['DistSrProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['DistSrProductPrice']['effective_date']));
							$data['DistSrProductCombination']['product_id'] = $id;
							$data_array = array();
							$data['DistSrProductCombination']['created_by'] = $this->UserAuth->getUserId();
							$data['DistSrProductCombination']['updated_at'] = $this->current_datetime();
							$data['DistSrProductCombination']['created_at'] = $this->current_datetime();
							foreach($this->request->data['DistSrProductCombination']['min_qty'] as $key=>$val)
							{
								$data['DistSrProductCombination']['product_price_id'] = $this->DistSrProductPrice->id;
								$data['DistSrProductCombination']['min_qty'] = $val;
								$data['DistSrProductCombination']['price'] = $this->request->data['DistSrProductCombination']['price'][$key];
								$data_array[] = $data;
							}														
							$this->DistSrProductCombination->saveAll($data_array);
						}
						/*-------- start update prev row by update next row --------*/
						if(!empty($update_next_date) && !empty($update_prev_date)){
							$update_prev_end_date['DistSrProductPrice']['id'] = $update_prev_data['DistSrProductPrice']['id'];
							$update_prev_end_date['DistSrProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($update_next_data['DistSrProductPrice']['effective_date'])))));
							if($this->DistSrProductPrice->save($update_prev_end_date)){
								foreach($update_prev_data['DistSrProductCombination'] as $prev_update_val){
									$update_product_combination['DistSrProductCombination']['id'] = $prev_update_val['id'];
									$update_product_combination['DistSrProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($update_next_data['DistSrProductPrice']['effective_date'])))));
									$this->DistSrProductCombination->save($update_product_combination);
								}
							}
						}
						if(empty($update_next_date) && !empty($update_prev_date)){
							$update_prev_end_date['DistSrProductPrice']['id'] = $update_prev_data['DistSrProductPrice']['id'];
							$update_prev_end_date['DistSrProductPrice']['end_date'] = NULL;
							if($this->DistSrProductPrice->save($update_prev_end_date)){
								foreach($update_prev_data['DistSrProductCombination'] as $prev_update_val){
									$update_product_combination['DistSrProductCombination']['id'] = $prev_update_val['id'];
									$update_product_combination['DistSrProductCombination']['end_date'] = NULL;
									$this->DistSrProductCombination->save($update_product_combination);
								}
							}
						}
						/*-------- end update prev row by update next row --------*/
						$info_after_update = $this->DistSrProductPrice->find('first', array('conditions' => array('DistSrProductPrice.id' => $id,'DistSrProductPrice.id' => $product_price_id)));
						$updated_effective_date = $info_after_update['DistSrProductPrice']['effective_date'];
						/*------------ start updated prev date ------------*/
						$updated_prev_date_list = $this->DistSrProductPrice->find('all',
							array(
								'conditions' => array('DistSrProductPrice.product_id' => $id,'DistSrProductPrice.effective_date <' => $updated_effective_date,'DistSrProductPrice.institute_id' => 0,'DistSrProductPrice.has_combination' => 0),
								'fields' => array('DistSrProductPrice.effective_date')
								)
							);
						if(!empty($updated_prev_date_list)){
							$updated_prev_short_list = array();
							foreach($updated_prev_date_list as $updated_prev_date_key => $updated_prev_date_val){
								array_push($updated_prev_short_list,$updated_prev_date_val['DistSrProductPrice']['effective_date']);
							}
							asort($updated_prev_short_list);
							$updated_prev_date = end($updated_prev_short_list);
							/* ----------- end get prev less date ----------*/
							/*----------- start get prev data -----------*/
							$updated_prev_data = $this->DistSrProductPrice->find('first',
								array(
									'conditions' => array('DistSrProductPrice.product_id' => $id,'DistSrProductPrice.effective_date' => $updated_prev_date,'DistSrProductPrice.institute_id' => 0,'DistSrProductPrice.has_combination' => 0),
									'fields' => array('DistSrProductPrice.effective_date')
									)
								);
							/*--------- end get prev data -------------*/
						}
						/*----------- end update prev date --------------*/
						/*---------- start update next date ------------*/
						$updated_next_date_list = $this->DistSrProductPrice->find('all',
							array(
								'conditions' => array('DistSrProductPrice.product_id' => $id,'DistSrProductPrice.effective_date >' => $updated_effective_date,'DistSrProductPrice.institute_id' => 0,'DistSrProductPrice.has_combination' => 0),
								'fields' => array('DistSrProductPrice.effective_date')
								)
							);
						if(!empty($updated_next_date_list)){
							$updated_next_short_list = array();
							foreach($updated_next_date_list as $updated_next_date_key => $updated_next_date_val){
								array_push($updated_next_short_list,$updated_next_date_val['DistSrProductPrice']['effective_date']);
							}
							rsort($updated_next_short_list);
							$updated_next_date = end($updated_next_short_list);
							/* ----------- end get prev less date ----------*/
							/*----------- start get prev data -----------*/
							$updated_next_data = $this->DistSrProductPrice->find('first',
								array(
									'conditions' => array('DistSrProductPrice.product_id' => $id,'DistSrProductPrice.effective_date' => $updated_next_date,'DistSrProductPrice.institute_id' => 0,'DistSrProductPrice.has_combination' => 0),
									'fields' => array('DistSrProductPrice.effective_date')
									)
								);
							//echo $updated_next_date;
							//exit;
							/*--------- end get prev data -------------*/
						}
						/*---------- end update next date ------------*/
						/*-------- start updated prev row by updated next row --------*/
						if(!empty($updated_prev_date)){
							$updated_prev_end_date['DistSrProductPrice']['id'] = $updated_prev_data['DistSrProductPrice']['id'];
							$updated_prev_end_date['DistSrProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_effective_date)))));
							if($this->DistSrProductPrice->save($updated_prev_end_date)){
								foreach($updated_prev_data['DistSrProductCombination'] as $prev_updated_val){
									$updated_product_combination['DistSrProductCombination']['id'] = $prev_updated_val['id'];
									$updated_product_combination['DistSrProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_effective_date)))));
									$this->DistSrProductCombination->save($updated_product_combination);
								}
							}
						}
						if(!empty($updated_next_date)){
							$update_current_row['DistSrProductPrice']['id'] = $product_price_id;
							$update_current_row['DistSrProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_next_data['DistSrProductPrice']['effective_date'])))));
							if($this->DistSrProductPrice->save($update_current_row)){
								foreach($info['DistSrProductCombination'] as $val){
									$current_product_combination['DistSrProductCombination']['id'] = $val['id'];
									$current_product_combination['DistSrProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_next_data['DistSrProductPrice']['effective_date'])))));
									$this->DistSrProductCombination->save($current_product_combination);
								}
							}
							if(!empty($updated_next_data)){
								$update_next_row['DistSrProductPrice']['id'] = $updated_next_data['DistSrProductPrice']['id'];
								$update_next_row['DistSrProductPrice']['end_date'] = NULL;
								//print_r($update_next_row);
								//exit;
								if($this->DistSrProductPrice->save($update_next_row)){
									foreach($updated_next_data['DistSrProductCombination'] as $next_val){
										$next_product_combination['DistSrProductCombination']['id'] = $next_val['id'];
										$next_product_combination['DistSrProductCombination']['end_date'] = NULL;
										$this->DistSrProductCombination->save($next_product_combination);
									}
								}
							}
						}else{
							$update_current_row['DistSrProductPrice']['id'] = $product_price_id;
							$update_current_row['DistSrProductPrice']['end_date'] = NULL;
							if($this->DistSrProductPrice->save($update_current_row)){
								foreach($info['DistSrProductCombination'] as $val){
									$current_product_combination['DistSrProductCombination']['id'] = $val['id'];
									$current_product_combination['DistSrProductCombination']['end_date'] = NULL;
									$this->DistSrProductCombination->save($current_product_combination);
								}
							}
						}
						/*-------- end updated prev row by updated next row --------*/
					}
					
					$this->Session->setFlash(__('The product price has been saved'), 'flash/success');
					$this->redirect(array('action' => 'index/'.$id));
				}
			}		
			$options = array('conditions' => array('DistSrProductPrice.' . $this->DistSrProductPrice->primaryKey => $info['DistSrProductPrice']['id']));
			$this->request->data = $this->DistSrProductPrice->find('first', $options);
			
		}
	}
}