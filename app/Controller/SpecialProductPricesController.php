<?php
App::uses('AppController', 'Controller');
/**
 * ProductPrices Controller
 *
 * @property ProductPrice $ProductPrice
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class SpecialProductPricesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session','Filter.Filter');
	public $uses =  array('Product','SpecialProductPrice');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','Product Price List');
		$this->Product->recursive = 0;
		
		$this->paginate = array('order' => array('Product.order' => 'ASC'));
		$this->set('products', $this->paginate());
		
		// $productCategories = $this->Product->ProductCategory->generateTreeList(null, null, null, '__');
		$productCategories = $this->Product->ProductCategory->find('list');
		$this->set(compact('productCategories'));	
	}
	
	public function admin_price_list($id = null){
		$this->set('page_title','Price List');
		$this->loadModel('SpecialProductPrice');
		$this->loadModel('Product');
		$this->SpecialProductPrice->recursive = -1;
		$this->paginate = array('order' => array('SpecialProductPrice.id' => 'DESC'),'conditions'=> array('SpecialProductPrice.product_id' => $id,'SpecialProductPrice.has_combination' => 0,'SpecialProductPrice.institute_id' => 0));
		$this->set('product_prices', $this->paginate('SpecialProductPrice'));
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
		$this->loadModel('SpecialProductCombination');
		$this->set('product_id',$id);
		if ($this->request->is('post')) 
		{
			/*---------- start get immediate Prev date ---------*/
			$new_start_date = date('Y-m-d',strtotime($this->request->data['SpecialProductPrice']['effective_date']));
			//$immediate_pre_date;
			$effective_date_list = $this->SpecialProductPrice->find('all',
				array(
					'conditions' => array('SpecialProductPrice.product_id' => $id,'SpecialProductPrice.effective_date <' => $new_start_date,'SpecialProductPrice.institute_id' => 0,'SpecialProductPrice.has_combination' => 0),
					'fields' => array('SpecialProductPrice.effective_date')
				)
			);
			$short_list = array();
			if(!empty($effective_date_list)){
				foreach($effective_date_list as $date_key => $date_val){
					array_push($short_list,$date_val['SpecialProductPrice']['effective_date']);
				}
				asort($short_list);
				$immediate_pre_date = array_pop($short_list);
				/*---------- end get immediate less date ---------*/
				/*---------- start immediate start date data --------*/
				$effective_price_data = $this->SpecialProductPrice->find('first',
					array(
						'conditions' => array('SpecialProductPrice.product_id' => $id,'SpecialProductPrice.effective_date' => $immediate_pre_date,'SpecialProductPrice.institute_id' => 0,'SpecialProductPrice.has_combination' => 0),
						'fields' => array('SpecialProductPrice.effective_date')
					)
				);
			}
			/*---------- end immediate start date data --------*/
			$pre_end_date = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($this->request->data['SpecialProductPrice']['effective_date'])))));

			$this->request->data['SpecialProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['SpecialProductPrice']['effective_date']));
			$this->request->data['SpecialProductPrice']['product_id'] = $id; 
			$this->request->data['SpecialProductPrice']['created_at'] = $this->current_datetime(); 
			$this->request->data['SpecialProductPrice']['updated_at'] = $this->current_datetime(); 
			$this->request->data['SpecialProductPrice']['created_by'] = $this->UserAuth->getUserId();
			$this->SpecialProductPrice->create();
			if ($this->SpecialProductPrice->save($this->request->data)) {
				$inserted_product_price_id = $this->SpecialProductPrice->getInsertID();
				if(!empty($this->request->data['SpecialProductCombination'])){
					$data_array = array();
					$data['SpecialProductCombination']['product_id'] = $id;
					$data['SpecialProductCombination']['created_at'] = $this->current_datetime(); 
					$data['SpecialProductCombination']['updated_at'] = $this->current_datetime(); 
					$data['SpecialProductCombination']['created_by'] = $this->UserAuth->getUserId();
					$data['SpecialProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['SpecialProductPrice']['effective_date']));
					foreach($this->request->data['SpecialProductCombination']['min_qty'] as $key=>$val)
					{
						$data['SpecialProductCombination']['product_price_id'] = $this->SpecialProductPrice->id;
						$data['SpecialProductCombination']['min_qty'] = $val;
						$data['SpecialProductCombination']['price'] = $this->request->data['SpecialProductCombination']['price'][$key];
						$data_array[] = $data;
					}								
					$this->SpecialProductCombination->saveAll($data_array);
					/*----------- start update prev row by end date -----------*/
					if(!empty($effective_price_data)){
						$update_pre_row['SpecialProductPrice']['id'] = $effective_price_data['SpecialProductPrice']['id'];
						$update_pre_row['SpecialProductPrice']['end_date'] = $pre_end_date;
						if($this->SpecialProductPrice->save($update_pre_row)){
							
							foreach($effective_price_data['SpecialProductCombination'] as $prev_key=>$prev_val){
								$update_combination_prev_row['SpecialProductCombination']['id'] = $prev_val['id'];
								$update_combination_prev_row['SpecialProductCombination']['end_date'] = $pre_end_date;
								$this->SpecialProductCombination->save($update_combination_prev_row);
							}
						}
					}
					/*----------- start recent inserted data -----------*/
					$recent_inserted_data = $this->SpecialProductPrice->find('first',
						array(
							'conditions' => array('SpecialProductPrice.id' => $inserted_product_price_id),
							'fields' => array('SpecialProductPrice.effective_date')
						)
					);

					/*---------- end recent inserted data -------------*/
					/*---------- start next effective_date list ---------*/
					$effective_next_date_list = $this->SpecialProductPrice->find('all',
						array(
							'conditions' => array('SpecialProductPrice.product_id' => $id,'SpecialProductPrice.effective_date >' => $recent_inserted_data['SpecialProductPrice']['effective_date'],'SpecialProductPrice.institute_id' => 0,'SpecialProductPrice.has_combination' => 0),
							'fields' => array('SpecialProductPrice.effective_date')
						)
					);

					$next_short_list = array();
					if(!empty($effective_next_date_list)){
						foreach($effective_next_date_list as $next_date_key => $next_date_val){
							array_push($next_short_list,$next_date_val['SpecialProductPrice']['effective_date']);
						}
						rsort($next_short_list);
						$immidiat_next_date = array_pop($next_short_list);
						/*---------- end get immediate less date ---------*/
					}

					/*----------- start update recent inserted row by end date -----------*/
					if(!empty($immidiat_next_date)){
						$end_date_of_current_row = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immidiat_next_date)))));
						$update_current_row['SpecialProductPrice']['id'] = $recent_inserted_data['SpecialProductPrice']['id'];
						$update_current_row['SpecialProductPrice']['end_date'] = $end_date_of_current_row;
						if($this->SpecialProductPrice->save($update_current_row)){
							
							foreach($recent_inserted_data['SpecialProductCombination'] as $next_key=>$next_val){
								$update_combination_next_row['SpecialProductCombination']['id'] = $next_val['id'];
								$update_combination_next_row['SpecialProductCombination']['end_date'] = $end_date_of_current_row;
								$this->SpecialProductCombination->save($update_combination_next_row);
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
		         $this->loadModel('SpecialProductCombination');
				 $this->loadModel('MemoDetail');
				 
				 
				 				
		          /* check product_price_id is existed in memo_details table */
		       if($product_price_id)
			   {
				   
				$sql="select count(*) as id_num from memo_details md
					inner join special_product_combinations pc on pc.id=md.product_price_id
					inner join memos m on m.id=md.memo_id
					 where pc.product_price_id=$product_price_id and m.is_distributor=2";				    
				$data_sql = $this->MemoDetail->Query($sql);
				
				$update_allow=1;
				
				if($data_sql[0][0]['id_num']>0)
				{
					$update_allow=0;
				}
				
				$this->set('update_allow', $update_allow);
			
			   }
				
				
		
		
		
		
		$info = $this->SpecialProductPrice->find('first', array('conditions' => array('SpecialProductPrice.id' => $product_price_id)));
		$update_effective_date = $info['SpecialProductPrice']['effective_date'];
		/*------------ start update prev date ------------*/
		$update_prev_date_list = $this->SpecialProductPrice->find('all',
			array(
				'conditions' => array('SpecialProductPrice.product_id' => $id,'SpecialProductPrice.effective_date <' => $update_effective_date,'SpecialProductPrice.institute_id' => 0,'SpecialProductPrice.has_combination' => 0),
				'fields' => array('SpecialProductPrice.effective_date')
			)
		);
		if(!empty($update_prev_date_list)){
			$update_prev_short_list = array();
			foreach($update_prev_date_list as $update_prev_date_key => $update_prev_date_val){
				array_push($update_prev_short_list,$update_prev_date_val['SpecialProductPrice']['effective_date']);
			}
			asort($update_prev_short_list);
			$update_prev_date = end($update_prev_short_list);
			/* ----------- end get prev less date ----------*/
			/*----------- start get prev data -----------*/
			$update_prev_data = $this->SpecialProductPrice->find('first',
				array(
					'conditions' => array('SpecialProductPrice.product_id' => $id,'SpecialProductPrice.effective_date' => $update_prev_date,'SpecialProductPrice.institute_id' => 0,'SpecialProductPrice.has_combination' => 0),
					'fields' => array('SpecialProductPrice.effective_date')
				)
			);
			/* echo "<pre>";
			print_r($update_prev_data);
			exit; */
			/*--------- end get prev data -------------*/
		}
		/*----------- end update prev date --------------*/
		/*---------- start update next date ------------*/
		$update_next_date_list = $this->SpecialProductPrice->find('all',
			array(
				'conditions' => array('SpecialProductPrice.product_id' => $id,'SpecialProductPrice.effective_date >' => $update_effective_date,'SpecialProductPrice.institute_id' => 0,'SpecialProductPrice.has_combination' => 0),
				'fields' => array('SpecialProductPrice.effective_date')
			)
		);
		if(!empty($update_next_date_list)){
			$update_next_short_list = array();
			foreach($update_next_date_list as $update_next_date_key => $update_next_date_val){
				array_push($update_next_short_list,$update_next_date_val['SpecialProductPrice']['effective_date']);
			}
			rsort($update_next_short_list);
			$update_next_date = end($update_next_short_list);
			/* ----------- end get prev less date ----------*/
			/*----------- start get prev data -----------*/
			$update_next_data = $this->SpecialProductPrice->find('first',
				array(
					'conditions' => array('SpecialProductPrice.product_id' => $id,'SpecialProductPrice.effective_date' => $update_next_date,'SpecialProductPrice.institute_id' => 0,'SpecialProductPrice.has_combination' => 0),
					'fields' => array('SpecialProductPrice.effective_date')
				)
			);
			/*--------- end get prev data -------------*/
		}
		/*---------- end update next date ------------*/
		if(isset($info['SpecialProductPrice']['id'])!='')
		{
			$this->SpecialProductPrice->id = $info['SpecialProductPrice']['id'];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->request->data['SpecialProductPrice']['updated_by'] = $this->UserAuth->getUserId();
				$this->request->data['SpecialProductPrice']['updated_at'] = $this->current_datetime();
				$this->request->data['SpecialProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['SpecialProductPrice']['effective_date']));
				if($this->SpecialProductPrice->save($this->request->data)) {
					if(!empty($this->request->data['SpecialProductCombination'])){
						$update_data['SpecialProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['SpecialProductPrice']['effective_date']));
						if(isset($this->request->data['SpecialProductCombination']['update_min_qty'])){
							$update_data_array = array();
							$update_data['SpecialProductCombination']['updated_by'] = $this->UserAuth->getUserId();
							$update_data['SpecialProductCombination']['updated_at'] = $this->current_datetime();
							foreach($this->request->data['SpecialProductCombination']['update_min_qty'] as $ukey=>$uval)
							{
								$update_data['SpecialProductCombination']['id'] = $ukey;
								$update_data['SpecialProductCombination']['min_qty'] = $this->request->data['SpecialProductCombination']['update_min_qty'][$ukey];
								$update_data['SpecialProductCombination']['product_id'] = $info['SpecialProductPrice']['product_id'];
								$update_data['SpecialProductCombination']['price'] = $this->request->data['SpecialProductCombination']['update_price'][$ukey];
								$update_data_array[] = $update_data;
							}												
							$this->SpecialProductCombination->saveAll($update_data_array);
							
						}
						if(isset($this->request->data['SpecialProductCombination']['min_qty'])){
							$data['SpecialProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['SpecialProductPrice']['effective_date']));
							$data['SpecialProductCombination']['product_id'] = $id;
							$data_array = array();
							$data['SpecialProductCombination']['created_by'] = $this->UserAuth->getUserId();
							$data['SpecialProductCombination']['updated_at'] = $this->current_datetime();
							$data['SpecialProductCombination']['created_at'] = $this->current_datetime();
							foreach($this->request->data['SpecialProductCombination']['min_qty'] as $key=>$val)
							{
								$data['SpecialProductCombination']['product_price_id'] = $this->SpecialProductPrice->id;
								$data['SpecialProductCombination']['min_qty'] = $val;
								$data['SpecialProductCombination']['price'] = $this->request->data['SpecialProductCombination']['price'][$key];
								$data_array[] = $data;
							}														
							$this->SpecialProductCombination->saveAll($data_array);
						}
						/*-------- start update prev row by update next row --------*/
						if(!empty($update_next_date) && !empty($update_prev_date)){
							$update_prev_end_date['SpecialProductPrice']['id'] = $update_prev_data['SpecialProductPrice']['id'];
							$update_prev_end_date['SpecialProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($update_next_data['SpecialProductPrice']['effective_date'])))));
							if($this->SpecialProductPrice->save($update_prev_end_date)){
								foreach($update_prev_data['SpecialProductCombination'] as $prev_update_val){
									$update_product_combination['SpecialProductCombination']['id'] = $prev_update_val['id'];
									$update_product_combination['SpecialProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($update_next_data['SpecialProductPrice']['effective_date'])))));
									$this->SpecialProductCombination->save($update_product_combination);
								}
							}
						}
						if(empty($update_next_date) && !empty($update_prev_date)){
							$update_prev_end_date['SpecialProductPrice']['id'] = $update_prev_data['SpecialProductPrice']['id'];
							$update_prev_end_date['SpecialProductPrice']['end_date'] = NULL;
							if($this->SpecialProductPrice->save($update_prev_end_date)){
								foreach($update_prev_data['SpecialProductCombination'] as $prev_update_val){
									$update_product_combination['SpecialProductCombination']['id'] = $prev_update_val['id'];
									$update_product_combination['SpecialProductCombination']['end_date'] = NULL;
									$this->SpecialProductCombination->save($update_product_combination);
								}
							}
						}
						/*-------- end update prev row by update next row --------*/
						$info_after_update = $this->SpecialProductPrice->find('first', array('conditions' => array('SpecialProductPrice.id' => $id,'SpecialProductPrice.id' => $product_price_id)));
						$updated_effective_date = $info_after_update['SpecialProductPrice']['effective_date'];
						/*------------ start updated prev date ------------*/
						$updated_prev_date_list = $this->SpecialProductPrice->find('all',
							array(
								'conditions' => array('SpecialProductPrice.product_id' => $id,'SpecialProductPrice.effective_date <' => $updated_effective_date,'SpecialProductPrice.institute_id' => 0,'SpecialProductPrice.has_combination' => 0),
								'fields' => array('SpecialProductPrice.effective_date')
							)
						);
						if(!empty($updated_prev_date_list)){
							$updated_prev_short_list = array();
							foreach($updated_prev_date_list as $updated_prev_date_key => $updated_prev_date_val){
								array_push($updated_prev_short_list,$updated_prev_date_val['SpecialProductPrice']['effective_date']);
							}
							asort($updated_prev_short_list);
							$updated_prev_date = end($updated_prev_short_list);
							/* ----------- end get prev less date ----------*/
							/*----------- start get prev data -----------*/
							$updated_prev_data = $this->SpecialProductPrice->find('first',
								array(
									'conditions' => array('SpecialProductPrice.product_id' => $id,'SpecialProductPrice.effective_date' => $updated_prev_date,'SpecialProductPrice.institute_id' => 0,'SpecialProductPrice.has_combination' => 0),
									'fields' => array('SpecialProductPrice.effective_date')
								)
							);
							/*--------- end get prev data -------------*/
						}
						/*----------- end update prev date --------------*/
						/*---------- start update next date ------------*/
						$updated_next_date_list = $this->SpecialProductPrice->find('all',
							array(
								'conditions' => array('SpecialProductPrice.product_id' => $id,'SpecialProductPrice.effective_date >' => $updated_effective_date,'SpecialProductPrice.institute_id' => 0,'SpecialProductPrice.has_combination' => 0),
								'fields' => array('SpecialProductPrice.effective_date')
							)
						);
						if(!empty($updated_next_date_list)){
							$updated_next_short_list = array();
							foreach($updated_next_date_list as $updated_next_date_key => $updated_next_date_val){
								array_push($updated_next_short_list,$updated_next_date_val['SpecialProductPrice']['effective_date']);
							}
							rsort($updated_next_short_list);
							$updated_next_date = end($updated_next_short_list);
							/* ----------- end get prev less date ----------*/
							/*----------- start get prev data -----------*/
							$updated_next_data = $this->SpecialProductPrice->find('first',
								array(
									'conditions' => array('SpecialProductPrice.product_id' => $id,'SpecialProductPrice.effective_date' => $updated_next_date,'SpecialProductPrice.institute_id' => 0,'SpecialProductPrice.has_combination' => 0),
									'fields' => array('SpecialProductPrice.effective_date')
								)
							);
							//echo $updated_next_date;
							//exit;
							/*--------- end get prev data -------------*/
						}
						/*---------- end update next date ------------*/
						/*-------- start updated prev row by updated next row --------*/
						if(!empty($updated_prev_date)){
							$updated_prev_end_date['SpecialProductPrice']['id'] = $updated_prev_data['SpecialProductPrice']['id'];
							$updated_prev_end_date['SpecialProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_effective_date)))));
							if($this->SpecialProductPrice->save($updated_prev_end_date)){
								foreach($updated_prev_data['SpecialProductCombination'] as $prev_updated_val){
									$updated_product_combination['SpecialProductCombination']['id'] = $prev_updated_val['id'];
									$updated_product_combination['SpecialProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_effective_date)))));
									$this->SpecialProductCombination->save($updated_product_combination);
								}
							}
						}
						if(!empty($updated_next_date)){
							$update_current_row['SpecialProductPrice']['id'] = $product_price_id;
							$update_current_row['SpecialProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_next_data['SpecialProductPrice']['effective_date'])))));
							if($this->SpecialProductPrice->save($update_current_row)){
								foreach($info['SpecialProductCombination'] as $val){
									$current_product_combination['SpecialProductCombination']['id'] = $val['id'];
									$current_product_combination['SpecialProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_next_data['SpecialProductPrice']['effective_date'])))));
									$this->SpecialProductCombination->save($current_product_combination);
								}
							}
							if(!empty($updated_next_data)){
								$update_next_row['SpecialProductPrice']['id'] = $updated_next_data['SpecialProductPrice']['id'];
								$update_next_row['SpecialProductPrice']['end_date'] = NULL;
								//print_r($update_next_row);
								//exit;
								if($this->SpecialProductPrice->save($update_next_row)){
									foreach($updated_next_data['SpecialProductCombination'] as $next_val){
										$next_product_combination['SpecialProductCombination']['id'] = $next_val['id'];
										$next_product_combination['SpecialProductCombination']['end_date'] = NULL;
										$this->SpecialProductCombination->save($next_product_combination);
									}
								}
							}
						}else{
							$update_current_row['SpecialProductPrice']['id'] = $product_price_id;
							$update_current_row['SpecialProductPrice']['end_date'] = NULL;
							if($this->SpecialProductPrice->save($update_current_row)){
								foreach($info['SpecialProductCombination'] as $val){
									$current_product_combination['SpecialProductCombination']['id'] = $val['id'];
									$current_product_combination['SpecialProductCombination']['end_date'] = NULL;
									$this->SpecialProductCombination->save($current_product_combination);
								}
							}
						}
						/*-------- end updated prev row by updated next row --------*/
					}
					
					$this->Session->setFlash(__('The product price has been saved'), 'flash/success');
					$this->redirect(array('action' => 'index/'.$id));
				}
			}		
			$options = array('conditions' => array('SpecialProductPrice.' . $this->SpecialProductPrice->primaryKey => $info['SpecialProductPrice']['id']));
			$this->request->data = $this->SpecialProductPrice->find('first', $options);
			
		}else{
			if ($this->request->is('post')) {
				$this->request->data['SpecialProductPrice']['product_id'] = $id; 
				$this->request->data['SpecialProductPrice']['created_at'] = $this->current_datetime(); 
				$this->request->data['SpecialProductPrice']['updated_at'] = $this->current_datetime(); 
				$this->request->data['SpecialProductPrice']['created_by'] = $this->UserAuth->getUserId();
				$this->request->data['SpecialProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['SpecialProductPrice']['effective_date']));
				$this->SpecialProductPrice->create();
				if ($this->SpecialProductPrice->save($this->request->data)) {
					
					if(!empty($this->request->data['SpecialProductCombination'])){
						$data_array = array();
						$data['SpecialProductCombination']['product_id'] = $id;
						$data['SpecialProductCombination']['created_at'] = $this->current_datetime();
						$data['SpecialProductCombination']['updated_at'] = $this->current_datetime();
						$data['SpecialProductCombination']['created_by'] = $this->UserAuth->getUserId();
						$data['SpecialProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['SpecialProductPrice']['effective_date']));
						foreach($this->request->data['SpecialProductCombination']['min_qty'] as $key=>$val)
						{
							$data['SpecialProductCombination']['product_price_id'] = $this->SpecialProductPrice->id;
							$data['SpecialProductCombination']['min_qty'] = $val;
							$data['SpecialProductCombination']['price'] = $this->request->data['SpecialProductCombination']['price'][$key];
							$data_array[] = $data;
						}								
						$this->SpecialProductCombination->saveAll($data_array); 
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
		$this->loadModel('DistProductCombination');
		
		$info = $this->SpecialProductPrice->find('first', array('conditions' => array('SpecialProductPrice.id' => $product_price_id)));
		$update_effective_date = $info['SpecialProductPrice']['effective_date'];
		/*------------ start update prev date ------------*/
		$update_prev_date_list = $this->SpecialProductPrice->find('all',
			array(
				'conditions' => array('SpecialProductPrice.product_id' => $id,'SpecialProductPrice.effective_date <' => $update_effective_date,'SpecialProductPrice.institute_id' => 0,'SpecialProductPrice.has_combination' => 0),
				'fields' => array('SpecialProductPrice.effective_date')
				)
			);
		if(!empty($update_prev_date_list)){
			$update_prev_short_list = array();
			foreach($update_prev_date_list as $update_prev_date_key => $update_prev_date_val){
				array_push($update_prev_short_list,$update_prev_date_val['SpecialProductPrice']['effective_date']);
			}
			asort($update_prev_short_list);
			$update_prev_date = end($update_prev_short_list);
			/* ----------- end get prev less date ----------*/
			/*----------- start get prev data -----------*/
			$update_prev_data = $this->SpecialProductPrice->find('first',
				array(
					'conditions' => array('SpecialProductPrice.product_id' => $id,'SpecialProductPrice.effective_date' => $update_prev_date,'SpecialProductPrice.institute_id' => 0,'SpecialProductPrice.has_combination' => 0),
					'fields' => array('SpecialProductPrice.effective_date')
					)
				);
			/* echo "<pre>";
			print_r($update_prev_data);
			exit; */
			/*--------- end get prev data -------------*/
		}
		/*----------- end update prev date --------------*/
		/*---------- start update next date ------------*/
		$update_next_date_list = $this->SpecialProductPrice->find('all',
			array(
				'conditions' => array('SpecialProductPrice.product_id' => $id,'SpecialProductPrice.effective_date >' => $update_effective_date,'SpecialProductPrice.institute_id' => 0,'SpecialProductPrice.has_combination' => 0),
				'fields' => array('SpecialProductPrice.effective_date')
				)
			);
		if(!empty($update_next_date_list)){
			$update_next_short_list = array();
			foreach($update_next_date_list as $update_next_date_key => $update_next_date_val){
				array_push($update_next_short_list,$update_next_date_val['SpecialProductPrice']['effective_date']);
			}
			rsort($update_next_short_list);
			$update_next_date = end($update_next_short_list);
			/* ----------- end get prev less date ----------*/
			/*----------- start get prev data -----------*/
			$update_next_data = $this->SpecialProductPrice->find('first',
				array(
					'conditions' => array('SpecialProductPrice.product_id' => $id,'SpecialProductPrice.effective_date' => $update_next_date,'SpecialProductPrice.institute_id' => 0,'SpecialProductPrice.has_combination' => 0),
					'fields' => array('SpecialProductPrice.effective_date')
					)
				);
			/*--------- end get prev data -------------*/
		}
		/*---------- end update next date ------------*/
		if(isset($info['SpecialProductPrice']['id'])!='')
		{
			$this->SpecialProductPrice->id = $info['SpecialProductPrice']['id'];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->request->data['SpecialProductPrice']['updated_by'] = $this->UserAuth->getUserId();
				$this->request->data['SpecialProductPrice']['updated_at'] = $this->current_datetime();
				$this->request->data['SpecialProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['SpecialProductPrice']['effective_date']));
				if($this->SpecialProductPrice->save($this->request->data)) {
					if(!empty($this->request->data['DistProductCombination'])){
						$update_data['DistProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['SpecialProductPrice']['effective_date']));
						if(isset($this->request->data['DistProductCombination']['update_min_qty'])){
							$update_data_array = array();
							$update_data['DistProductCombination']['updated_by'] = $this->UserAuth->getUserId();
							$update_data['DistProductCombination']['updated_at'] = $this->current_datetime();
							foreach($this->request->data['DistProductCombination']['update_min_qty'] as $ukey=>$uval)
							{
								$update_data['DistProductCombination']['id'] = $ukey;
								$update_data['DistProductCombination']['min_qty'] = $this->request->data['DistProductCombination']['update_min_qty'][$ukey];
								$update_data['DistProductCombination']['product_id'] = $info['SpecialProductPrice']['product_id'];
								$update_data['DistProductCombination']['price'] = $this->request->data['DistProductCombination']['update_price'][$ukey];
								$update_data_array[] = $update_data;
							}												
							$this->DistProductCombination->saveAll($update_data_array);
							
						}
						if(isset($this->request->data['DistProductCombination']['min_qty'])){
							$data['DistProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['SpecialProductPrice']['effective_date']));
							$data['DistProductCombination']['product_id'] = $id;
							$data_array = array();
							$data['DistProductCombination']['created_by'] = $this->UserAuth->getUserId();
							$data['DistProductCombination']['updated_at'] = $this->current_datetime();
							$data['DistProductCombination']['created_at'] = $this->current_datetime();
							foreach($this->request->data['DistProductCombination']['min_qty'] as $key=>$val)
							{
								$data['DistProductCombination']['product_price_id'] = $this->SpecialProductPrice->id;
								$data['DistProductCombination']['min_qty'] = $val;
								$data['DistProductCombination']['price'] = $this->request->data['DistProductCombination']['price'][$key];
								$data_array[] = $data;
							}														
							$this->DistProductCombination->saveAll($data_array);
						}
						/*-------- start update prev row by update next row --------*/
						if(!empty($update_next_date) && !empty($update_prev_date)){
							$update_prev_end_date['SpecialProductPrice']['id'] = $update_prev_data['SpecialProductPrice']['id'];
							$update_prev_end_date['SpecialProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($update_next_data['SpecialProductPrice']['effective_date'])))));
							if($this->SpecialProductPrice->save($update_prev_end_date)){
								foreach($update_prev_data['DistProductCombination'] as $prev_update_val){
									$update_product_combination['DistProductCombination']['id'] = $prev_update_val['id'];
									$update_product_combination['DistProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($update_next_data['SpecialProductPrice']['effective_date'])))));
									$this->DistProductCombination->save($update_product_combination);
								}
							}
						}
						if(empty($update_next_date) && !empty($update_prev_date)){
							$update_prev_end_date['SpecialProductPrice']['id'] = $update_prev_data['SpecialProductPrice']['id'];
							$update_prev_end_date['SpecialProductPrice']['end_date'] = NULL;
							if($this->SpecialProductPrice->save($update_prev_end_date)){
								foreach($update_prev_data['DistProductCombination'] as $prev_update_val){
									$update_product_combination['DistProductCombination']['id'] = $prev_update_val['id'];
									$update_product_combination['DistProductCombination']['end_date'] = NULL;
									$this->DistProductCombination->save($update_product_combination);
								}
							}
						}
						/*-------- end update prev row by update next row --------*/
						$info_after_update = $this->SpecialProductPrice->find('first', array('conditions' => array('SpecialProductPrice.id' => $id,'SpecialProductPrice.id' => $product_price_id)));
						$updated_effective_date = $info_after_update['SpecialProductPrice']['effective_date'];
						/*------------ start updated prev date ------------*/
						$updated_prev_date_list = $this->SpecialProductPrice->find('all',
							array(
								'conditions' => array('SpecialProductPrice.product_id' => $id,'SpecialProductPrice.effective_date <' => $updated_effective_date,'SpecialProductPrice.institute_id' => 0,'SpecialProductPrice.has_combination' => 0),
								'fields' => array('SpecialProductPrice.effective_date')
								)
							);
						if(!empty($updated_prev_date_list)){
							$updated_prev_short_list = array();
							foreach($updated_prev_date_list as $updated_prev_date_key => $updated_prev_date_val){
								array_push($updated_prev_short_list,$updated_prev_date_val['SpecialProductPrice']['effective_date']);
							}
							asort($updated_prev_short_list);
							$updated_prev_date = end($updated_prev_short_list);
							/* ----------- end get prev less date ----------*/
							/*----------- start get prev data -----------*/
							$updated_prev_data = $this->SpecialProductPrice->find('first',
								array(
									'conditions' => array('SpecialProductPrice.product_id' => $id,'SpecialProductPrice.effective_date' => $updated_prev_date,'SpecialProductPrice.institute_id' => 0,'SpecialProductPrice.has_combination' => 0),
									'fields' => array('SpecialProductPrice.effective_date')
									)
								);
							/*--------- end get prev data -------------*/
						}
						/*----------- end update prev date --------------*/
						/*---------- start update next date ------------*/
						$updated_next_date_list = $this->SpecialProductPrice->find('all',
							array(
								'conditions' => array('SpecialProductPrice.product_id' => $id,'ProductPrice.effective_date >' => $updated_effective_date,'SpecialProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
								'fields' => array('SpecialProductPrice.effective_date')
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
							$updated_next_data = $this->SpecialProductPrice->find('first',
								array(
									'conditions' => array('SpecialProductPrice.product_id' => $id,'ProductPrice.effective_date' => $updated_next_date,'SpecialProductPrice.institute_id' => 0,'ProductPrice.has_combination' => 0),
									'fields' => array('SpecialProductPrice.effective_date')
									)
								);
							//echo $updated_next_date;
							//exit;
							/*--------- end get prev data -------------*/
						}
						/*---------- end update next date ------------*/
						/*-------- start updated prev row by updated next row --------*/
						if(!empty($updated_prev_date)){
							$updated_prev_end_date['ProductPrice']['id'] = $updated_prev_data['SpecialProductPrice']['id'];
							$updated_prev_end_date['ProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_effective_date)))));
							if($this->SpecialProductPrice->save($updated_prev_end_date)){
								foreach($updated_prev_data['DistProductCombination'] as $prev_updated_val){
									$updated_product_combination['DistProductCombination']['id'] = $prev_updated_val['id'];
									$updated_product_combination['DistProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_effective_date)))));
									$this->DistProductCombination->save($updated_product_combination);
								}
							}
						}
						if(!empty($updated_next_date)){
							$update_current_row['SpecialProductPrice']['id'] = $product_price_id;
							$update_current_row['ProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_next_data['SpecialProductPrice']['effective_date'])))));
							if($this->SpecialProductPrice->save($update_current_row)){
								foreach($info['DistProductCombination'] as $val){
									$current_product_combination['DistProductCombination']['id'] = $val['id'];
									$current_product_combination['DistProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_next_data['ProductPrice']['effective_date'])))));
									$this->DistProductCombination->save($current_product_combination);
								}
							}
							if(!empty($updated_next_data)){
								$update_next_row['SpecialProductPrice']['id'] = $updated_next_data['ProductPrice']['id'];
								$update_next_row['ProductPrice']['end_date'] = NULL;
								//print_r($update_next_row);
								//exit;
								if($this->SpecialProductPrice->save($update_next_row)){
									foreach($updated_next_data['DistProductCombination'] as $next_val){
										$next_product_combination['DistProductCombination']['id'] = $next_val['id'];
										$next_product_combination['DistProductCombination']['end_date'] = NULL;
										$this->DistProductCombination->save($next_product_combination);
									}
								}
							}
						}else{
							$update_current_row['ProductPrice']['id'] = $product_price_id;
							$update_current_row['SpecialProductPrice']['end_date'] = NULL;
							if($this->SpecialProductPrice->save($update_current_row)){
								foreach($info['DistProductCombination'] as $val){
									$current_product_combination['DistProductCombination']['id'] = $val['id'];
									$current_product_combination['DistProductCombination']['end_date'] = NULL;
									$this->DistProductCombination->save($current_product_combination);
								}
							}
						}
						/*-------- end updated prev row by updated next row --------*/
					}
					
					$this->Session->setFlash(__('The product price has been saved'), 'flash/success');
					$this->redirect(array('action' => 'index/'.$id));
				}
			}		
			$options = array('conditions' => array('ProductPrice.' . $this->SpecialProductPrice->primaryKey => $info['SpecialProductPrice']['id']));
			$this->request->data = $this->SpecialProductPrice->find('first', $options);
			
		}
	}
}