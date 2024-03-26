<?php
App::uses('AppController', 'Controller');
/**
 * ProductCombinations Controller
 *
 * @property ProductCombination $ProductCombination
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ProductCombinationsController extends AppController {

/**
 * Components
 *
 * @var array
 */
public $components = array('Paginator', 'Session');
/**
 * admin_index method
 *
 * @return void
 */
public function admin_index($id = null) 
{
	$this->loadModel('Combination');
	$this->set('page_title','Combination List');
	$this->Combination->recursive = 0;
	$joins= array();
	$conditions = array();
	$product_id = '';
	if(isset($id))
	{
		$joins = array(
			array(
				'table'=>'product_combinations',
				'type'=>'LEFT',
				'alias'=>'PC',
				'conditions'=>array('PC.combination_id = Combination.id')
				)
			);
		$conditions = array('PC.product_id' => $id);
		$product_id = $id;
	}
	else
	{
		$joins = array(
		array(
			'table'=>'product_combinations',
			'type'=>'LEFT',
			'alias'=>'PC',
			'conditions'=>array('PC.combination_id = Combination.id')
			)
		);
	}
	
	$this->paginate = array(	
		'joins' => $joins,
		'conditions' => $conditions,
		'fields' => array('DISTINCT Combination.id','Combination.name','PC.effective_date','PC.min_qty'),
		'order'=>'Combination.id DESC',		
			//'group' => array('Combination.id')
			
		);
		$this->set('product_id', $product_id);
		$this->set('combinations', $this->paginate('Combination'));
		
		/* check combine_id is existed in memo_details table */
		
		$sql="select md.product_combination_id as com_id,count(md.id) as id_num from memo_details md 
				left join combinations c on c.id=md.product_combination_id 
				where md.product_combination_id is not NULL 
				group by product_combination_id";
				
        $data_sql = $this->Combination->Query($sql);
		$com_ids=array();
		foreach ($data_sql as $each_data)
		{
			$id=$each_data[0]['com_id'];
		    $com_ids[$id]=$each_data[0]['id_num'];
		}
		$this->set('com_ids', $com_ids);
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Product Combination Details');
		if (!$this->ProductCombination->exists($id)) {
			throw new NotFoundException(__('Invalid product combination'));
		}
		$options = array('conditions' => array('ProductCombination.' . $this->ProductCombination->primaryKey => $id));
		$this->set('productCombination', $this->ProductCombination->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add($product_id = null) {
		$this->set('page_title','Add Combination');
		$this->loadModel('Combination');
		$this->loadModel('Product');
		$this->loadModel('ProductPrice');
		if(!empty($product_id)){
			$slab_list = $this->ProductCombination->find('list',array(
			'conditions' => array('ProductCombination.combination_id' => 0,'ProductCombination.product_id' => $product_id)
			));
		}else{
			$slab_list = '';
		}
		if ($this->request->is('post')) {
			$new_start_date = date('Y-m-d',strtotime($this->request->data['ProductCombination']['effective_date']));
			if(!empty($this->request->data['ProductCombination']['product_id'])){
				$combined_product = $this->request->data['ProductCombination']['product_id'];
				asort($combined_product);
				$combined_product_id = implode(',', $combined_product);
				$this->Combination->unbindModel(
					array('hasMany' => array('ProductCombination'))
				);
				$prev_combination_date = $this->Combination->find('all',array(
					'conditions' => array(
						'Combination.all_products_in_combination' => $combined_product_id,
						'ProductCombination.effective_date <' => $new_start_date
					),
					'joins' => array(
						array(
							'alias' => 'ProductCombination',
							'table' => 'product_combinations',
							'type' => 'INNER',
							'conditions' => 'Combination.id = ProductCombination.combination_id'
						)
					),
					'fields' => array(
						'Combination.*','ProductCombination.*'
					)
				));
				if(!empty($prev_combination_date)){
					$prev_short_list = array();
					foreach($prev_combination_date as $prev_com_key=>$prev_com_val){
						$prev_com_val['ProductCombination']['effective_date'];
						array_push($prev_short_list,$prev_com_val['ProductCombination']['effective_date']);
					}
					asort($prev_short_list);
					$immediate_pre_date = array_pop($prev_short_list);
				}
				/* ------- start next start date ---------*/
				$this->Combination->unbindModel(
					array('hasMany' => array('ProductCombination'))
				);
				$next_combination_date = $this->Combination->find('all',array(
					'conditions' => array(
						'Combination.all_products_in_combination' => $combined_product_id,
						'ProductCombination.effective_date >' => $new_start_date
					),
					'joins' => array(
						array(
							'alias' => 'ProductCombination',
							'table' => 'product_combinations',
							'type' => 'INNER',
							'conditions' => 'Combination.id = ProductCombination.combination_id'
						)
					),
					'fields' => array(
						'Combination.*','ProductCombination.*'
					)
				));
				if(!empty($next_combination_date)){
					$next_short_list = array();
					foreach($next_combination_date as $next_com_key=>$next_com_val){
						array_push($next_short_list,$next_com_val['ProductCombination']['effective_date']);
					}
					rsort($next_short_list);
					$immediate_next_date = array_pop($next_short_list);
				}
				/* ------- end next start date ---------*/
				/*------- start previous row data --------*/
				$this->ProductPrice->unbindModel(
					array('hasMany' => array('ProductCombination'))
				);
				if(!empty($immediate_pre_date)){
					$prev_row_data = $this->ProductPrice->find('all',array(
						'conditions' => array(
							'ProductPrice.effective_date' => $immediate_pre_date,
							'ProductPrice.has_combination' => 1,
							'ProductPrice.institute_id' => 0,
							'ProductPrice.product_id' => $combined_product
						),
						'joins' => array(
							array(
								'alias' => 'ProductCombination',
								'table' => 'product_combinations',
								'type' => 'INNER',
								'conditions' => 'ProductPrice.id = ProductCombination.product_price_id'
							)
						),
						'fields' => array(
							'ProductPrice.*','ProductCombination.*'
						)
					));
				}
/* 				echo "<pre>";
				print_r($prev_row_data);
				exit; */
				/*------- end previous row data --------*/
			}
			$all_products_in_combination = array();
			$this->request->data['Combination']['created_at'] = $this->current_datetime(); 
			$this->request->data['Combination']['updated_at'] = $this->current_datetime(); 
			$this->request->data['Combination']['created_by'] = $this->UserAuth->getUserId();
			$data['ProductPrice']['created_at'] = $this->current_datetime(); 
			$data['ProductPrice']['updated_at'] = $this->current_datetime(); 
			$data['ProductPrice']['created_by'] = $this->UserAuth->getUserId();
			
			
			$request_product_list = $this->request->data['ProductCombination']['product_id'];
			$this->loadModel('ProductCombination');
			$existing_product_list = $this->ProductCombination->find('all',array(
			'conditions' => array(
				'combination_id !=' => 0,
				'effective_date'=>$new_start_date
				),
			'fields' => array('id', 'product_id'),
			'recursive' => -1
			));
			$existing = 0;
			foreach($existing_product_list as $pro_list){
				$pro_id = $pro_list['ProductCombination']['product_id'];
				if (in_array($pro_id, $request_product_list)){
				  $existing = 1;
				}
			}
			
			// forcely 
			$existing = 0;
			
			if($existing==0)
			{
				$this->Combination->create();
				if ($this->Combination->save($this->request->data)){
				$recent_inserted_combination_id = $this->Combination->getInsertID();
					if(!empty($this->request->data['ProductCombination'])){
						if(!empty($this->request->data['Combination']['redirect_product_id'])){
							$redirect_product_id = $this->request->data['Combination']['redirect_product_id'];
						}
						$data['ProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['ProductCombination']['effective_date']));
						$data['ProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['ProductCombination']['effective_date']));
						foreach($this->request->data['ProductCombination']['product_id'] as $key=>$val)
						{
							$combined_slab_price = $this->ProductCombination->find('first',array(
								'conditions' => array('ProductCombination.id'=>$this->request->data['ProductCombination']['parent_slab_id'][$key])
								
							));
							$all_products_in_combination[] = $val;
							$data['ProductPrice']['product_id'] = $val;
							$data['ProductPrice']['has_combination'] = 1;
							$data['ProductPrice']['has_price_slot'] = 1;
							$data['ProductPrice']['is_active'] = 1;
							$data['ProductPrice']['institute_id'] = 0;
							$data['ProductPrice']['target_custommer'] = 0;
							$this->ProductPrice->create();
							$this->ProductPrice->save($data);
							$product_price_id = $this->ProductPrice->getLastInsertID();
							
							$data['ProductCombination']['product_price_id'] = $product_price_id;
							$data['ProductCombination']['combination_id'] = $this->Combination->id;
							$data['ProductCombination']['parent_slab_id'] = $this->request->data['ProductCombination']['parent_slab_id'][$key];
							$data['ProductCombination']['product_id'] = $val;
							$data['ProductCombination']['price'] = $combined_slab_price['ProductCombination']['price'];
							$data['ProductCombination']['created_at'] = $this->current_datetime(); 
							$data['ProductCombination']['updated_at'] = $this->current_datetime(); 
							$data['ProductCombination']['created_by'] = $this->UserAuth->getUserId();
						
							// Multiple Quantity Slab For a Product
							foreach($this->request->data['ProductCombination']['min_qty'] as $qty_key=>$qty_val)
							{
								$data['ProductCombination']['min_qty'] = $qty_val;
								$this->ProductCombination->create();
								$this->ProductCombination->save($data); 
							}
						}								
						if(!empty($immediate_pre_date) && !empty($prev_row_data)){
							foreach($prev_row_data as $update_pre_data_key=>$update_pre_data_val){
								$update_product_price['ProductPrice']['id'] = $update_pre_data_val['ProductPrice']['id'];
								$update_product_price['ProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($new_start_date)))));
								if($this->ProductPrice->save($update_product_price)){
									$update_product_combination['ProductCombination']['id'] = $update_pre_data_val['ProductCombination']['id'];
									$update_product_combination['ProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($new_start_date)))));
									$this->ProductCombination->save($update_product_combination);
								}
							}
						}
					}
				//sort all_products_in_combination in ascending order. This will be used for selejt querys
				asort($all_products_in_combination);
				$all_products_in_combination = implode(',', $all_products_in_combination);
				$this->data = array();
				$this->request->data['Combination']['all_products_in_combination'] = $all_products_in_combination;
				$this->request->data['Combination']['id'] = $this->Combination->id;
				$this->Combination->save($this->request->data);
				
				/*---------- start recent inserted data ----------*/
				$this->ProductPrice->unbindModel(
					array('hasMany' => array('ProductCombination'))
				);
				$current_row_data = $this->ProductPrice->find('all',array(
					'conditions' => array(
						'ProductPrice.effective_date' => date('Y-m-d',strtotime($new_start_date)),
						'ProductPrice.has_combination' => 1,
						'ProductPrice.institute_id' => 0,
						'ProductPrice.product_id' => $combined_product
					),
					'joins' => array(
						array(
							'alias' => 'ProductCombination',
							'table' => 'product_combinations',
							'type' => 'INNER',
							'conditions' => 'ProductPrice.id = ProductCombination.product_price_id'
						)
					),
					'fields' => array(
						'ProductPrice.*','ProductCombination.*'
					)
				));
				/*---------- end recent inserted data ----------*/
				/*------ start update current row data by end date -------*/
				if(!empty($immediate_next_date) && !empty($current_row_data)){
					foreach($current_row_data as $update_curr_data_key=>$update_curr_data_val){
						$update_curr_product_price['ProductPrice']['id'] = $update_curr_data_val['ProductPrice']['id'];
						$update_curr_product_price['ProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immediate_next_date)))));
						if($this->ProductPrice->save($update_curr_product_price)){
							$update_curr_product_combination['ProductCombination']['id'] = $update_curr_data_val['ProductCombination']['id'];
							$update_curr_product_combination['ProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immediate_next_date)))));
							$this->ProductCombination->save($update_curr_product_combination);
						}
					}
				}
				/*------ end update current row data by end date -------*/
				
				$this->Session->setFlash(__('The combination has been saved'), 'flash/success');
				if(!empty($redirect_product_id)){
					$this->redirect(array('action' => 'index/'.$redirect_product_id));
				}else{
					$this->redirect(array('action' => 'index'));
				}
				
			  }
			}
			else
			{
				$this->Session->setFlash(__("Product doesn't add many times."), 'flash/warning');

				$this->redirect(array('action' => 'add'));
				
			}
			
		}
		$products = $this->Product->find('list',array('order'=>array('Product.order' => 'ASC')));
		$this->html = '';
		foreach($products as $key=>$val)
		{
			$this->html .= '<option value="'.$key.'">'.addslashes($val).'</option>';		
		}
		$product_list = $this->html;
		$this->set(compact('slab_list','product_list','products','product_id'));
		
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null, $product_id = '') {
		$this->set('page_title','Edit Combination');
		$this->loadModel('Combination');
		$this->loadModel('Product');
		$this->loadModel('ProductPrice');
		
		
		/* check combine_id is existed in memo_details table */
		
		$sql="select md.product_combination_id as com_id,count(md.id) as id_num from memo_details md 
				left join combinations c on c.id=md.product_combination_id 
				where md.product_combination_id is not NULL 
				group by product_combination_id";
				
        $data_sql = $this->Combination->Query($sql);
		$com_ids=array();
		foreach ($data_sql as $each_data)
		{
			$ids=$each_data[0]['com_id'];
		    $com_ids[$ids]=$each_data[0]['id_num'];
		}
		$this->set('com_ids', $com_ids);
		$this->set('edit_id', $id);
		
		
		
		if(!empty($id)){
			$for_update_product = $this->ProductCombination->find('all',array(
				'fields' => array('ProductCombination.*'),
				'conditions' => array('ProductCombination.combination_id' => $id)
			));
		}
		
		$total_slab = array();
		foreach($for_update_product as $val){
			if(!empty($val['ProductCombination']['product_id'])){
				$slab_list = $this->ProductCombination->find('list',array(
				'conditions' => array('ProductCombination.combination_id' => 0,'ProductCombination.product_id' => $val['ProductCombination']['product_id'])
				));
			}else{
				$slab_list = '';
			}
			$total_slab[$val['ProductCombination']['product_id']] = $slab_list;
		}
		$this->set('total_slab',$total_slab);
		
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$request_product_list = $this->request->data['ProductCombination']['product_id'];
			$this->loadModel('ProductCombination');
			$existing_product_list = $this->ProductCombination->find('all',array(
			'conditions' => array(
				"NOT" => array( "combination_id" => array(0, $id) )
			),
			'fields' => array('id', 'product_id'),
			'recursive' => -1
			));
			$existing = 0;
			foreach($existing_product_list as $pro_list){
				$pro_id = $pro_list['ProductCombination']['product_id'];
				if (in_array($pro_id, $request_product_list)){
				  $existing = 1;
				}
			}
			
			// forcely 
			$existing = 0;
			
			if($existing==0)
			{
				$combined_product = $this->request->data['ProductCombination']['product_id'];
				asort($combined_product);
				$combined_product_id = implode(',', $combined_product);
				$all_products_in_combination = array();
				/*---------- start select for updated data ------------*/
				$this->Combination->unbindModel(
					array('hasMany' => array('ProductCombination'))
				);
				$select_for_updated_data = $this->Combination->find('first',array(
					'conditions' => array(
						'Combination.id' => $id
					),
					'joins' => array(
						array(
							'alias' => 'ProductCombination',
							'table' => 'product_combinations',
							'type' => 'INNER',
							'conditions' => 'Combination.id = ProductCombination.combination_id'
						)
					),
					'fields' => array(
						'Combination.*','ProductCombination.*'
					)
				));
				$effective_date_before_update = $select_for_updated_data['ProductCombination']['effective_date'];
				/*---------- end select for updated data ------------*/
				
				/*------------ start update prev date ------------*/
				$this->Combination->unbindModel(
					array('hasMany' => array('ProductCombination'))
				);
				$update_prev_date_list = $this->Combination->find('all',array(
					'conditions' => array(
						'Combination.all_products_in_combination' => $combined_product_id,
						'ProductCombination.effective_date <' => $effective_date_before_update
					),
					'joins' => array(
						array(
							'alias' => 'ProductCombination',
							'table' => 'product_combinations',
							'type' => 'INNER',
							'conditions' => 'Combination.id = ProductCombination.combination_id'
						)
					),
					'fields' => array(
						'Combination.*','ProductCombination.*'
					)
				));
				if(!empty($update_prev_date_list)){
					$update_prev_short_list = array();
					foreach($update_prev_date_list as $update_prev_date_key=>$update_prev_date_val){
						array_push($update_prev_short_list,$update_prev_date_val['ProductCombination']['effective_date']);
					}
					asort($update_prev_short_list);
					$update_prev_date = array_pop($update_prev_short_list);
					
					/*------------ end update prev date ------------*/
					/*------- start previous row data --------*/
					if(!empty($update_prev_date)){
						$this->ProductPrice->unbindModel(
							array('hasMany' => array('ProductCombination'))
						);
						$update_prev_data = $this->ProductPrice->find('all',array(
							'conditions' => array(
								'ProductPrice.effective_date' => $update_prev_date,
								'ProductPrice.has_combination' => 1,
								'ProductPrice.institute_id' => 0,
								'ProductPrice.product_id' => $combined_product
							),
							'joins' => array(
								array(
									'alias' => 'ProductCombination',
									'table' => 'product_combinations',
									'type' => 'INNER',
									'conditions' => 'ProductPrice.id = ProductCombination.product_price_id'
								)
							),
							'fields' => array(
								'ProductPrice.*','ProductCombination.*'
							)
						));
					}
					/*------- end previous row data --------*/
				}
	
				/*------------ start update next date ------------*/
				$this->Combination->unbindModel(
					array('hasMany' => array('ProductCombination'))
				);
				$update_next_date_list = $this->Combination->find('all',array(
					'conditions' => array(
						'Combination.all_products_in_combination' => $combined_product_id,
						'ProductCombination.effective_date >' => $effective_date_before_update
					),
					'joins' => array(
						array(
							'alias' => 'ProductCombination',
							'table' => 'product_combinations',
							'type' => 'INNER',
							'conditions' => 'Combination.id = ProductCombination.combination_id'
						)
					),
					'fields' => array(
						'Combination.*','ProductCombination.*'
					)
				));
				if(!empty($update_next_date_list)){
					$update_next_short_list = array();
					foreach($update_next_date_list as $update_next_date_key=>$update_next_date_val){
						array_push($update_next_short_list,$update_next_date_val['ProductCombination']['effective_date']);
					}
					rsort($update_next_short_list);
					$update_next_date = end($update_next_short_list);
					
					/*------------ end update next date ------------*/
					/*------- start Next row data --------*/
					$this->ProductPrice->unbindModel(
						array('hasMany' => array('ProductCombination'))
					);
					$update_next_data = $this->ProductPrice->find('all',array(
						'conditions' => array(
							'ProductPrice.effective_date' => $update_next_date,
							'ProductPrice.has_combination' => 1,
							'ProductPrice.institute_id' => 0,
							'ProductPrice.product_id' => $combined_product
						),
						'joins' => array(
							array(
								'alias' => 'ProductCombination',
								'table' => 'product_combinations',
								'type' => 'INNER',
								'conditions' => 'ProductPrice.id = ProductCombination.product_price_id'
							)
						),
						'fields' => array(
							'ProductPrice.*','ProductCombination.*'
						)
					));
					/*------- end Next row data --------*/
				}
				/*-------- Start delete product price and product combination---------*/
				$delete_all = 0;
				foreach($for_update_product as $price_id ){
					if(!empty($price_id['ProductCombination']['product_price_id']) && $price_id['ProductCombination']['product_price_id'] !=0){
						
						$this->ProductPrice->id = $price_id['ProductCombination']['product_price_id'];
						if (!$this->ProductPrice->exists()) {
							throw new NotFoundException(__('Invalid ProductPrice'));
						}
						if ($this->ProductPrice->delete()) {
							$this->ProductCombination->product_price_id = $price_id['ProductCombination']['product_price_id'];
							$this->ProductCombination->delete();
							//$this->ProductCombination->deleteAll(array('ProductCombination.product_price_id'=>$price_id['ProductCombination']['product_price_id']));
							$delete_all = 1;
						}else{
							$delete_all = 0;
						}
					}
				}
				/*-------- End delete product price and product combination---------*/
				if($delete_all==1){
					$this->Combination->id = $id;
					$this->request->data['Combination']['updated_at'] = $this->current_datetime(); ;
					$this->request->data['Combination']['updated_by'] = $this->UserAuth->getUserId();
					if ($this->Combination->save($this->request->data)) {
							// for add product
							if(isset($this->request->data['ProductCombination']['product_id'])){
								$data['ProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['ProductCombination']['effective_date']));
								$insert_data['ProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['ProductCombination']['effective_date']));
								$insert_data_array = array();
								foreach($this->request->data['ProductCombination']['product_id'] as $ikey=>$ival)
								{	
									$combined_slab_price = $this->ProductCombination->find('first',array(
										'conditions' => array('ProductCombination.id'=>$this->request->data['ProductCombination']['parent_slab_id'][$ikey])
										
									));
									$all_products_in_combination[] = $ival;
									// Product Price Table Entry
									$data['ProductPrice']['product_id'] = $ival;
									$data['ProductPrice']['has_combination'] = 1;
									$data['ProductPrice']['has_price_slot'] = 1;
									$data['ProductPrice']['is_active'] = 1;
									$data['ProductPrice']['institute_id'] = 0;
									$data['ProductPrice']['target_custommer'] = 0;
									$data['ProductPrice']['created_at'] = $this->current_datetime(); ;
									$data['ProductPrice']['updated_at'] = $this->current_datetime(); ;
									$data['ProductPrice']['created_by'] = $this->UserAuth->getUserId();
									$data['ProductPrice']['updated_by'] = $this->UserAuth->getUserId();
									$this->ProductPrice->create();
									$this->ProductPrice->save($data);
									$product_price_id = $this->ProductPrice->getLastInsertID();
								
								
									$insert_data['ProductCombination']['combination_id'] = $this->Combination->id;
									$insert_data['ProductCombination']['product_price_id'] = $product_price_id;
									$insert_data['ProductCombination']['product_id'] = $ival;
									$insert_data['ProductCombination']['parent_slab_id'] = $this->request->data['ProductCombination']['parent_slab_id'][$ikey];
									$insert_data['ProductCombination']['price'] = $combined_slab_price['ProductCombination']['price'];
									$insert_data['ProductCombination']['min_qty'] = $this->request->data['ProductCombination']['min_qty'];
									$insert_data['ProductCombination']['created_at'] = $this->current_datetime();
									$insert_data['ProductCombination']['updated_at'] = $this->current_datetime();
									$insert_data['ProductCombination']['created_by'] = $this->UserAuth->getUserId();
									$insert_data['ProductCombination']['updated_by'] = $this->UserAuth->getUserId();
									$insert_data_array[] = $insert_data;
								}				
								$this->ProductCombination->saveAll($insert_data_array);
								/*-------------- start update prev data by end date -------------*/
								if(!empty($update_next_date) && !empty($update_prev_date)){
									foreach($update_prev_data as $update_prev_key=>$update_prev_val){
										$update_product_price['ProductPrice']['id'] = $update_prev_val['ProductPrice']['id'];
										$update_product_price['ProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($update_next_date)))));
										if($this->ProductPrice->save($update_product_price)){
											$update_product_combination['ProductCombination']['id'] = $update_prev_val['ProductCombination']['id'];
											$update_product_combination['ProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($update_next_date)))));
											$this->ProductCombination->save($update_product_combination);
										}
									}
								}
								if(empty($update_next_date) && !empty($update_prev_date)){
									foreach($update_prev_data as $update_prev_key=>$update_prev_val){
										$update_product_price['ProductPrice']['id'] = $update_prev_val['ProductPrice']['id'];
										$update_product_price['ProductPrice']['end_date'] = NULL;
										if($this->ProductPrice->save($update_product_price)){
											$update_product_combination['ProductCombination']['id'] = $update_prev_val['ProductCombination']['id'];
											$update_product_combination['ProductCombination']['end_date'] = NULL;
											$this->ProductCombination->save($update_product_combination);
										}
									}
								}
								/*-------------- end update prev data by end date ---------------*/
								/*-------------- start updated info -------------- */
								$this->Combination->unbindModel(
									array('hasMany' => array('ProductCombination'))
								);
								$updated_row_info = $this->Combination->find('first',array(
									'conditions' => array(
										'Combination.id' => $id
									),
									'joins' => array(
										array(
											'alias' => 'ProductCombination',
											'table' => 'product_combinations',
											'type' => 'INNER',
											'conditions' => 'Combination.id = ProductCombination.combination_id'
										)
									),
									'fields' => array(
										'Combination.*','ProductCombination.*'
									)
								));
								$updated_effective_date = $updated_row_info['ProductCombination']['effective_date'];
								/*-------------- end updated info -------------- */
								
								/*---------- start updated prev date-------*/
								$this->Combination->unbindModel(
									array('hasMany' => array('ProductCombination'))
								);
								$updated_prev_date_list = $this->Combination->find('all',array(
									'conditions' => array(
										'Combination.all_products_in_combination' => $combined_product_id,
										'ProductCombination.effective_date <' => $updated_effective_date
									),
									'joins' => array(
										array(
											'alias' => 'ProductCombination',
											'table' => 'product_combinations',
											'type' => 'INNER',
											'conditions' => 'Combination.id = ProductCombination.combination_id'
										)
									),
									'fields' => array(
										'Combination.*','ProductCombination.*'
									)
								));
								if(!empty($updated_prev_date_list)){
									$updated_prev_short_list = array();
									foreach($updated_prev_date_list as $updated_prev_date_key=>$updated_prev_date_val){
										array_push($updated_prev_short_list,$updated_prev_date_val['ProductCombination']['effective_date']);
									}
									asort($updated_prev_short_list);
									$updated_prev_date = array_pop($updated_prev_short_list);
									/*------------ end updated prev date ------------*/
									/*------- start updated previous row data --------*/
									$this->ProductPrice->unbindModel(
										array('hasMany' => array('ProductCombination'))
									);
									$updated_prev_data = $this->ProductPrice->find('all',array(
										'conditions' => array(
											'ProductPrice.effective_date' => $updated_prev_date,
											'ProductPrice.has_combination' => 1,
											'ProductPrice.institute_id' => 0,
											'ProductPrice.product_id' => $combined_product
										),
										'joins' => array(
											array(
												'alias' => 'ProductCombination',
												'table' => 'product_combinations',
												'type' => 'INNER',
												'conditions' => 'ProductPrice.id = ProductCombination.product_price_id'
											)
										),
										'fields' => array(
											'ProductPrice.*','ProductCombination.*'
										)
									));
									/*------- end updated previous row data --------*/
								}
								
	
								/*------------ start updated next date ------------*/
								$this->Combination->unbindModel(
									array('hasMany' => array('ProductCombination'))
								);
								$updated_next_date_list = $this->Combination->find('all',array(
									'conditions' => array(
										'Combination.all_products_in_combination' => $combined_product_id,
										'ProductCombination.effective_date >' => $updated_effective_date
									),
									'joins' => array(
										array(
											'alias' => 'ProductCombination',
											'table' => 'product_combinations',
											'type' => 'INNER',
											'conditions' => 'Combination.id = ProductCombination.combination_id'
										)
									),
									'fields' => array(
										'Combination.*','ProductCombination.*'
									)
								));
								if(!empty($updated_next_date_list)){
									$updated_next_short_list = array();
									foreach($updated_next_date_list as $updated_next_date_key=>$updated_next_date_val){
										array_push($updated_next_short_list,$updated_next_date_val['ProductCombination']['effective_date']);
									}
									rsort($updated_next_short_list);
									$updated_next_date = end($updated_next_short_list);
								}
								/*------------ end updated next date ------------*/
								
								/*------------- start update updated prev and next row ------------*/
								if(!empty($updated_prev_date)){
									foreach($updated_prev_data as $updated_prev_key=>$updated_prev_val){
										$updated_product_price['ProductPrice']['id'] = $updated_prev_val['ProductPrice']['id'];
										$updated_product_price['ProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_effective_date)))));
										if($this->ProductPrice->save($updated_product_price)){
											$updated_product_combination['ProductCombination']['id'] = $updated_prev_val['ProductCombination']['id'];
											$updated_product_combination['ProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_effective_date)))));
											$this->ProductCombination->save($updated_product_combination);
										}
									}
								}
								/*---------- start updated effective data -----------*/
								$this->ProductPrice->unbindModel(
									array('hasMany' => array('ProductCombination'))
								);
								$updated_effective_data = $this->ProductPrice->find('all',array(
									'conditions' => array(
										'ProductPrice.effective_date' => $updated_effective_date,
										'ProductPrice.has_combination' => 1,
										'ProductPrice.institute_id' => 0,
										'ProductPrice.product_id' => $combined_product
									),
									'joins' => array(
										array(
											'alias' => 'ProductCombination',
											'table' => 'product_combinations',
											'type' => 'INNER',
											'conditions' => 'ProductPrice.id = ProductCombination.product_price_id'
										)
									),
									'fields' => array(
										'ProductPrice.*','ProductCombination.*'
									)
								));
								/*---------- end updated effective data -----------*/
								if(!empty($updated_next_date)){
									foreach($updated_effective_data as $updated_effec_key=>$updated_effec_val){
										$updated_effec_price['ProductPrice']['id'] = $updated_effec_val['ProductPrice']['id'];
										$updated_effec_price['ProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_next_date)))));
										if($this->ProductPrice->save($updated_effec_price)){
											$updated_effec_product_combination['ProductCombination']['id'] = $updated_effec_val['ProductCombination']['id'];
											$updated_effec_product_combination['ProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_next_date)))));
											$this->ProductCombination->save($updated_effec_product_combination);
										}
									}
								}else{
									foreach($updated_effective_data as $updated_effec_key=>$updated_effec_val){
										$updated_effec_price['ProductPrice']['id'] = $updated_effec_val['ProductPrice']['id'];
										$updated_effec_price['ProductPrice']['end_date'] = NULL;
										if($this->ProductPrice->save($updated_effec_price)){
											$updated_effec_product_combination['ProductCombination']['id'] = $updated_effec_val['ProductCombination']['id'];
											$updated_effec_product_combination['ProductCombination']['end_date'] = NULL;
											$this->ProductCombination->save($updated_effec_product_combination);
										}
									}
								}
								/*------------- end update updated prev and next row ------------*/
								
								
								
								
							}					
						
	
						//sort all_products_in_combination in ascending order. This will be used for selejt querys
						asort($all_products_in_combination);
						$all_products_in_combination = implode(',', $all_products_in_combination);
						$this->data = array();
						$this->request->data['Combination']['all_products_in_combination'] = $all_products_in_combination;
						$this->request->data['Combination']['id'] = $this->Combination->id;
						$this->Combination->save($this->request->data);
						
						$this->Session->setFlash(__('The combination has been saved'), 'flash/success');
						$this->redirect(array('action' => 'index'));
					}
				}
			}
			else
			{
				$this->Session->setFlash(__("Product doesn't add many times."), 'flash/warning');

				$this->redirect(array('action' => 'edit/'.$id));
			}
		}
		
		$options = array('conditions' => array('Combination.' . $this->Combination->primaryKey => $id));
		$this->request->data = $this->Combination->find('first', $options);
		
		//pr($this->request->data);	
		
		//$products_list = $this->Product->find('list');
		
		$products_list = $this->Product->find('list',array('order'=>array('Product.order' => 'ASC')));
		
		
		
		$this->html = '';
		foreach($products_list as $key=>$val)
		{
			$this->html .= '<option value="'.$key.'">'.addslashes($val).'</option>';
		}
		$products = $this->html; 
		$this->set(compact('slab_list','products_list','products'));
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null,$product_id = null) {
		$this->loadModel('Combination');
		$this->loadModel('ProductCombination');
		$this->loadModel('ProductPrice');
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Combination->id = $id;
		if (!$this->Combination->exists()) {
			throw new NotFoundException(__('Invalid Combination'));
		}
		/*-------- start deleted row info ---------*/
		if(!empty($id)){
			$deleted_data = $this->Combination->find('first',array(
				'conditions' => array('Combination.id' => $id)
			));
			$combined_product_id = $deleted_data['Combination']['all_products_in_combination'];
			$combined_product_array = explode(',',$combined_product_id);
			$deleted_effective_date = $deleted_data['ProductCombination'][0]['effective_date'];
			if(!empty($deleted_data)){
			$delete_price_id_list = array();
				foreach($deleted_data['ProductCombination'] as $p_price_key=>$p_price_val){
					array_push($delete_price_id_list,$p_price_val['product_price_id']);
				}
			}
		}
		/*-------- end deleted row info ---------*/
		
		/*----- start prev date and data --------*/
		$this->Combination->unbindModel(
			array('hasMany' => array('ProductCombination'))
		);
		$prev_combination_date = $this->Combination->find('all',array(
			'conditions' => array(
				'Combination.all_products_in_combination' => $combined_product_id,
				'ProductCombination.effective_date <' => $deleted_effective_date
			),
			'joins' => array(
				array(
					'alias' => 'ProductCombination',
					'table' => 'product_combinations',
					'type' => 'INNER',
					'conditions' => 'Combination.id = ProductCombination.combination_id'
				)
			),
			'fields' => array(
				'Combination.*','ProductCombination.*'
			)
		));
		if(!empty($prev_combination_date)){
			$prev_short_list = array();
			foreach($prev_combination_date as $prev_com_key=>$prev_com_val){
				$prev_com_val['ProductCombination']['effective_date'];
				array_push($prev_short_list,$prev_com_val['ProductCombination']['effective_date']);
			}
			asort($prev_short_list);
			$immediate_pre_date = array_pop($prev_short_list);
			/*---------- start prev data ----------*/
			$this->ProductPrice->unbindModel(
				array('hasMany' => array('ProductCombination'))
			);
			$previous_row_data = $this->ProductPrice->find('all',array(
				'conditions' => array(
					'ProductPrice.effective_date' => date('Y-m-d',strtotime($immediate_pre_date)),
					'ProductPrice.has_combination' => 1,
					'ProductPrice.institute_id' => 0,
					'ProductPrice.product_id' => $combined_product_array
				),
				'joins' => array(
					array(
						'alias' => 'ProductCombination',
						'table' => 'product_combinations',
						'type' => 'INNER',
						'conditions' => 'ProductPrice.id = ProductCombination.product_price_id'
					)
				),
				'fields' => array(
					'ProductPrice.*','ProductCombination.*'
				)
			));
			/*---------- end prev data ----------*/
			
		}
		/*----- end prev date and data --------*/
		
		/*------ start next date -------*/
		$this->Combination->unbindModel(
			array('hasMany' => array('ProductCombination'))
		);
		$next_combination_date = $this->Combination->find('all',array(
			'conditions' => array(
				'Combination.all_products_in_combination' => $combined_product_id,
				'ProductCombination.effective_date >' => $deleted_effective_date
			),
			'joins' => array(
				array(
					'alias' => 'ProductCombination',
					'table' => 'product_combinations',
					'type' => 'INNER',
					'conditions' => 'Combination.id = ProductCombination.combination_id'
				)
			),
			'fields' => array(
				'Combination.*','ProductCombination.*'
			)
		));
		if(!empty($next_combination_date)){
			$next_short_list = array();
			foreach($next_combination_date as $next_com_key=>$next_com_val){
				array_push($next_short_list,$next_com_val['ProductCombination']['effective_date']);
			}
			rsort($next_short_list);
			$immediate_next_date = array_pop($next_short_list);
		}
		/*------ end next date -------*/

		if ($this->Combination->delete()) {
			$this->ProductPrice->deleteAll(array('id' => $delete_price_id_list), false);
			
			/*-------- start update prev by next ---------*/
			if(!empty($immediate_next_date) && !empty($previous_row_data)){
				foreach($previous_row_data as $update_prev_data_key=>$update_prev_data_val){
					$update_prev_product_price['ProductPrice']['id'] = $update_prev_data_val['ProductPrice']['id'];
					$update_prev_product_price['ProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immediate_next_date)))));
					if($this->ProductPrice->save($update_prev_product_price)){
						$update_prev_product_combination['ProductCombination']['id'] = $update_prev_data_val['ProductCombination']['id'];
						$update_prev_product_combination['ProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immediate_next_date)))));
						$this->ProductCombination->save($update_prev_product_combination);
					}
				}
			}else if(!empty($previous_row_data)){
				foreach($previous_row_data as $update_prev_data_key=>$update_prev_data_val){
					$update_prev_product_price['ProductPrice']['id'] = $update_prev_data_val['ProductPrice']['id'];
					$update_prev_product_price['ProductPrice']['end_date'] = NULL;
					if($this->ProductPrice->save($update_prev_product_price)){
						$update_prev_product_combination['ProductCombination']['id'] = $update_prev_data_val['ProductCombination']['id'];
						$update_prev_product_combination['ProductCombination']['end_date'] = NULL;
						$this->ProductCombination->save($update_prev_product_combination);
					}
				}
			}
			/*-------- end update prev by next ---------*/
			
			$this->Session->setFlash(__('Combination deleted'), 'flash/success');
			$this->redirect(array('action' => 'index/'.$product_id));
		}
		$this->Session->setFlash(__('Combination was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index/'.$product_id));
	}
	
	
	public function get_product_list(){
		$rs = array(array('id' => '', 'title' => '---- Select Category -----'));
		$product_category_id = $this->request->data['product_category_id'];
        $product_list = $this->Product->find('all', array(
			'fields' => array('Product.id', 'Product.name as title'),
			'conditions' => array('Product.product_category_id' => $product_category_id),
			'recursive' => -1
		));
		$data_array = Set::extract($product_list, '{n}.Product');
		if(!empty($product_list)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
		
	}
	public function admin_get_slab_list(){
		$this->loadModel('ProductPrice');
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
		/*------- end get prev effective date and next effective date ---------*/
		
		
		if(!empty($product_id)){
			$slab_list = $this->ProductCombination->find('all',array(
			'conditions' => array($condition_value),
			'recursive'=>-1
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
}
