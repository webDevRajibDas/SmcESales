<?php
App::uses('AppController', 'Controller');
/**
 * SpecialProductCombinations Controller
 *
 * @property SpecialProductCombination $SpecialProductCombination
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class SpecialProductCombinationsController extends AppController {

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
	$this->loadModel('SpecialCombination');
	$this->set('page_title','SpecialCombination List');
	$this->SpecialCombination->recursive = 0;
	$joins= array();
	$conditions = array();
	$product_id = '';
	if(isset($id))
	{
		$joins = array(
			array(
				'table'=>'special_product_combinations',
				'type'=>'LEFT',
				'alias'=>'PC',
				'conditions'=>array('PC.combination_id = SpecialCombination.id')
				)
			);
		$conditions = array('PC.product_id' => $id);
		$product_id = $id;
	}
	else
	{
		$joins = array(
		array(
			'table'=>'special_product_combinations',
			'type'=>'LEFT',
			'alias'=>'PC',
			'conditions'=>array('PC.combination_id = SpecialCombination.id')
			)
		);
	}
	
	$this->paginate = array(	
		'joins' => $joins,
		'conditions' => $conditions,
		'fields' => array('DISTINCT SpecialCombination.id','SpecialCombination.name','PC.effective_date','PC.min_qty'),
		'order'=>'SpecialCombination.id DESC',		
			//'group' => array('SpecialCombination.id')
			
		);
		$this->set('product_id', $product_id);
		$this->set('SpecialCombinations', $this->paginate('SpecialCombination'));
		
		/* check combine_id is existed in memo_details table */
		
		$sql="select md.product_combination_id as com_id,count(md.id) as id_num from memo_details md 
				left join special_combinations c on c.id=md.product_combination_id 
				where md.product_combination_id is not NULL 
				group by product_combination_id";
				
        $data_sql = $this->SpecialCombination->Query($sql);
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
		if (!$this->SpecialProductCombination->exists($id)) {
			throw new NotFoundException(__('Invalid product combination'));
		}
		$options = array('conditions' => array('SpecialProductCombination.' . $this->SpecialProductCombination->primaryKey => $id));
		$this->set('SpecialProductCombination', $this->SpecialProductCombination->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add($product_id = null) {
		$this->set('page_title','Add Combination');
		$this->loadModel('SpecialCombination');
		$this->loadModel('Product');
		$this->loadModel('SpecialProductPrice');
		if(!empty($product_id)){
			$this->SpecialProductCombination->virtualFields['min_qty_with_effective_date']='CONCAT(SpecialProductCombination.min_qty,\' (\',SpecialProductCombination.effective_date,\')\')';
			$slab_list = $this->SpecialProductCombination->find('list',array(
			'conditions' => array('SpecialProductCombination.combination_id' => 0,'SpecialProductCombination.product_id' => $product_id),
			'fields'=>array('SpecialProductCombination.id','min_qty_with_effective_date'),
			));
			
		}else{
			$slab_list = '';
		}
		if ($this->request->is('post')) {
			$new_start_date = date('Y-m-d',strtotime($this->request->data['SpecialProductCombination']['effective_date']));
			if(!empty($this->request->data['SpecialProductCombination']['product_id'])){
				$combined_product = $this->request->data['SpecialProductCombination']['product_id'];
				asort($combined_product);
				$combined_product_id = implode(',', $combined_product);
				$this->SpecialCombination->unbindModel(
					array('hasMany' => array('SpecialProductCombination'))
				);
				$prev_combination_date = $this->SpecialCombination->find('all',array(
					'conditions' => array(
						'SpecialCombination.all_products_in_combination' => $combined_product_id,
						'SpecialProductCombination.effective_date <' => $new_start_date
					),
					'joins' => array(
						array(
							'alias' => 'SpecialProductCombination',
							'table' => 'special_product_combinations',
							'type' => 'INNER',
							'conditions' => 'SpecialCombination.id = SpecialProductCombination.combination_id'
						)
					),
					'fields' => array(
						'SpecialCombination.*','SpecialProductCombination.*'
					)
				));
				if(!empty($prev_combination_date)){
					$prev_short_list = array();
					foreach($prev_combination_date as $prev_com_key=>$prev_com_val){
						$prev_com_val['SpecialProductCombination']['effective_date'];
						array_push($prev_short_list,$prev_com_val['SpecialProductCombination']['effective_date']);
					}
					asort($prev_short_list);
					$immediate_pre_date = array_pop($prev_short_list);
				}
				/* ------- start next start date ---------*/
				$this->SpecialCombination->unbindModel(
					array('hasMany' => array('SpecialProductCombination'))
				);
				$next_combination_date = $this->SpecialCombination->find('all',array(
					'conditions' => array(
						'SpecialCombination.all_products_in_combination' => $combined_product_id,
						'SpecialProductCombination.effective_date >' => $new_start_date
					),
					'joins' => array(
						array(
							'alias' => 'SpecialProductCombination',
							'table' => 'special_product_combinations',
							'type' => 'INNER',
							'conditions' => 'SpecialCombination.id = SpecialProductCombination.combination_id'
						)
					),
					'fields' => array(
						'SpecialCombination.*','SpecialProductCombination.*'
					)
				));
				if(!empty($next_combination_date)){
					$next_short_list = array();
					foreach($next_combination_date as $next_com_key=>$next_com_val){
						array_push($next_short_list,$next_com_val['SpecialProductCombination']['effective_date']);
					}
					rsort($next_short_list);
					$immediate_next_date = array_pop($next_short_list);
				}
				/* ------- end next start date ---------*/
				/*------- start previous row data --------*/
				$this->SpecialProductPrice->unbindModel(
					array('hasMany' => array('SpecialProductCombination'))
				);
				if(!empty($immediate_pre_date)){
					$prev_row_data = $this->SpecialProductPrice->find('all',array(
						'conditions' => array(
							'SpecialProductPrice.effective_date' => $immediate_pre_date,
							'SpecialProductPrice.has_combination' => 1,
							'SpecialProductPrice.institute_id' => 0,
							'SpecialProductPrice.product_id' => $combined_product
						),
						'joins' => array(
							array(
								'alias' => 'SpecialProductCombination',
								'table' => 'special_product_combinations',
								'type' => 'INNER',
								'conditions' => 'SpecialProductPrice.id = SpecialProductCombination.product_price_id'
							)
						),
						'fields' => array(
							'SpecialProductPrice.*','SpecialProductCombination.*'
						)
					));
				}
/* 				echo "<pre>";
				print_r($prev_row_data);
				exit; */
				/*------- end previous row data --------*/
			}
			$all_products_in_combination = array();
			$this->request->data['SpecialCombination']['created_at'] = $this->current_datetime(); 
			$this->request->data['SpecialCombination']['updated_at'] = $this->current_datetime(); 
			$this->request->data['SpecialCombination']['created_by'] = $this->UserAuth->getUserId();
			$data['SpecialProductPrice']['created_at'] = $this->current_datetime(); 
			$data['SpecialProductPrice']['updated_at'] = $this->current_datetime(); 
			$data['SpecialProductPrice']['created_by'] = $this->UserAuth->getUserId();
			
			
			$request_product_list = $this->request->data['SpecialProductCombination']['product_id'];
			$this->loadModel('SpecialProductCombination');
			$existing_product_list = $this->SpecialProductCombination->find('all',array(
			'conditions' => array(
				'combination_id !=' => 0,
				'effective_date'=>$new_start_date
				),
			'fields' => array('id', 'product_id'),
			'recursive' => -1
			));
			$existing = 0;
			foreach($existing_product_list as $pro_list){
				$pro_id = $pro_list['SpecialProductCombination']['product_id'];
				if (in_array($pro_id, $request_product_list)){
				  $existing = 1;
				}
			}
			
			// forcely 
			$existing = 0;
			
			if($existing==0)
			{
				$this->SpecialCombination->create();
				if ($this->SpecialCombination->save($this->request->data)){
				$recent_inserted_combination_id = $this->SpecialCombination->getInsertID();
					if(!empty($this->request->data['SpecialProductCombination'])){
						if(!empty($this->request->data['SpecialCombination']['redirect_product_id'])){
							$redirect_product_id = $this->request->data['SpecialCombination']['redirect_product_id'];
						}
						$data['SpecialProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['SpecialProductCombination']['effective_date']));
						$data['SpecialProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['SpecialProductCombination']['effective_date']));
						foreach($this->request->data['SpecialProductCombination']['product_id'] as $key=>$val)
						{
							$combined_slab_price = $this->SpecialProductCombination->find('first',array(
								'conditions' => array('SpecialProductCombination.id'=>$this->request->data['SpecialProductCombination']['parent_slab_id'][$key])
								
							));
							$all_products_in_combination[] = $val;
							$data['SpecialProductPrice']['product_id'] = $val;
							$data['SpecialProductPrice']['has_combination'] = 1;
							$data['SpecialProductPrice']['has_price_slot'] = 1;
							$data['SpecialProductPrice']['is_active'] = 1;
							$data['SpecialProductPrice']['institute_id'] = 0;
							$data['SpecialProductPrice']['target_custommer'] = 0;
							$this->SpecialProductPrice->create();
							$this->SpecialProductPrice->save($data);
							$product_price_id = $this->SpecialProductPrice->getLastInsertID();
							
							$data['SpecialProductCombination']['product_price_id'] = $product_price_id;
							$data['SpecialProductCombination']['combination_id'] = $this->SpecialCombination->id;
							$data['SpecialProductCombination']['parent_slab_id'] = $this->request->data['SpecialProductCombination']['parent_slab_id'][$key];
							$data['SpecialProductCombination']['product_id'] = $val;
							$data['SpecialProductCombination']['price'] = $combined_slab_price['SpecialProductCombination']['price'];
							$data['SpecialProductCombination']['created_at'] = $this->current_datetime(); 
							$data['SpecialProductCombination']['updated_at'] = $this->current_datetime(); 
							$data['SpecialProductCombination']['created_by'] = $this->UserAuth->getUserId();
						
							// Multiple Quantity Slab For a Product
							foreach($this->request->data['SpecialProductCombination']['min_qty'] as $qty_key=>$qty_val)
							{
								$data['SpecialProductCombination']['min_qty'] = $qty_val;
								$this->SpecialProductCombination->create();
								$this->SpecialProductCombination->save($data); 
							}
						}								
						if(!empty($immediate_pre_date) && !empty($prev_row_data)){
							foreach($prev_row_data as $update_pre_data_key=>$update_pre_data_val){
								$update_product_price['SpecialProductPrice']['id'] = $update_pre_data_val['SpecialProductPrice']['id'];
								$update_product_price['SpecialProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($new_start_date)))));
								if($this->SpecialProductPrice->save($update_product_price)){
									$update_product_combination['SpecialProductCombination']['id'] = $update_pre_data_val['SpecialProductCombination']['id'];
									$update_product_combination['SpecialProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($new_start_date)))));
									$this->SpecialProductCombination->save($update_product_combination);
								}
							}
						}
					}
				//sort all_products_in_combination in ascending order. This will be used for selejt querys
				asort($all_products_in_combination);
				$all_products_in_combination = implode(',', $all_products_in_combination);
				$this->data = array();
				$this->request->data['SpecialCombination']['all_products_in_combination'] = $all_products_in_combination;
				$this->request->data['SpecialCombination']['id'] = $this->SpecialCombination->id;
				$this->SpecialCombination->save($this->request->data);
				
				/*---------- start recent inserted data ----------*/
				$this->SpecialProductPrice->unbindModel(
					array('hasMany' => array('SpecialProductCombination'))
				);
				$current_row_data = $this->SpecialProductPrice->find('all',array(
					'conditions' => array(
						'SpecialProductPrice.effective_date' => date('Y-m-d',strtotime($new_start_date)),
						'SpecialProductPrice.has_combination' => 1,
						'SpecialProductPrice.institute_id' => 0,
						'SpecialProductPrice.product_id' => $combined_product
					),
					'joins' => array(
						array(
							'alias' => 'SpecialProductCombination',
							'table' => 'special_product_combinations',
							'type' => 'INNER',
							'conditions' => 'SpecialProductPrice.id = SpecialProductCombination.product_price_id'
						)
					),
					'fields' => array(
						'SpecialProductPrice.*','SpecialProductCombination.*'
					)
				));
				/*---------- end recent inserted data ----------*/
				/*------ start update current row data by end date -------*/
				if(!empty($immediate_next_date) && !empty($current_row_data)){
					foreach($current_row_data as $update_curr_data_key=>$update_curr_data_val){
						$update_curr_product_price['SpecialProductPrice']['id'] = $update_curr_data_val['SpecialProductPrice']['id'];
						$update_curr_product_price['SpecialProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immediate_next_date)))));
						if($this->SpecialProductPrice->save($update_curr_product_price)){
							$update_curr_product_combination['SpecialProductCombination']['id'] = $update_curr_data_val['SpecialProductCombination']['id'];
							$update_curr_product_combination['SpecialProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immediate_next_date)))));
							$this->SpecialProductCombination->save($update_curr_product_combination);
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
		$this->loadModel('SpecialCombination');
		$this->loadModel('Product');
		$this->loadModel('SpecialProductPrice');
		
		
		/* check combine_id is existed in memo_details table */
		
		$sql="select md.product_combination_id as com_id,count(md.id) as id_num from memo_details md 
				left join special_combinations c on c.id=md.product_combination_id 
				where md.product_combination_id is not NULL 
				group by product_combination_id";
				
        $data_sql = $this->SpecialCombination->Query($sql);
		$com_ids=array();
		foreach ($data_sql as $each_data)
		{
			$ids=$each_data[0]['com_id'];
		    $com_ids[$ids]=$each_data[0]['id_num'];
		}
		$this->set('com_ids', $com_ids);
		$this->set('edit_id', $id);
		
		
		
		if(!empty($id)){
			$for_update_product = $this->SpecialProductCombination->find('all',array(
				'fields' => array('SpecialProductCombination.*'),
				'conditions' => array('SpecialProductCombination.combination_id' => $id)
			));
		}
		
		$total_slab = array();
		foreach($for_update_product as $val){
			if(!empty($val['SpecialProductCombination']['product_id'])){
				$slab_list = $this->SpecialProductCombination->find('list',array(
				'conditions' => array('SpecialProductCombination.combination_id' => 0,'SpecialProductCombination.product_id' => $val['SpecialProductCombination']['product_id'])
				));
			}else{
				$slab_list = '';
			}
			$total_slab[$val['SpecialProductCombination']['product_id']] = $slab_list;
		}
		$this->set('total_slab',$total_slab);
		
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$request_product_list = $this->request->data['SpecialProductCombination']['product_id'];
			$this->loadModel('SpecialProductCombination');
			$existing_product_list = $this->SpecialProductCombination->find('all',array(
			'conditions' => array(
				"NOT" => array( "combination_id" => array(0, $id) )
			),
			'fields' => array('id', 'product_id'),
			'recursive' => -1
			));
			$existing = 0;
			foreach($existing_product_list as $pro_list){
				$pro_id = $pro_list['SpecialProductCombination']['product_id'];
				if (in_array($pro_id, $request_product_list)){
				  $existing = 1;
				}
			}
			
			// forcely 
			$existing = 0;
			
			if($existing==0)
			{
				$combined_product = $this->request->data['SpecialProductCombination']['product_id'];
				asort($combined_product);
				$combined_product_id = implode(',', $combined_product);
				$all_products_in_combination = array();
				/*---------- start select for updated data ------------*/
				$this->SpecialCombination->unbindModel(
					array('hasMany' => array('SpecialProductCombination'))
				);
				$select_for_updated_data = $this->SpecialCombination->find('first',array(
					'conditions' => array(
						'SpecialCombination.id' => $id
					),
					'joins' => array(
						array(
							'alias' => 'SpecialProductCombination',
							'table' => 'special_product_combinations',
							'type' => 'INNER',
							'conditions' => 'SpecialCombination.id = SpecialProductCombination.combination_id'
						)
					),
					'fields' => array(
						'SpecialCombination.*','SpecialProductCombination.*'
					)
				));
				$effective_date_before_update = $select_for_updated_data['SpecialProductCombination']['effective_date'];
				/*---------- end select for updated data ------------*/
				
				/*------------ start update prev date ------------*/
				$this->SpecialCombination->unbindModel(
					array('hasMany' => array('SpecialProductCombination'))
				);
				$update_prev_date_list = $this->SpecialCombination->find('all',array(
					'conditions' => array(
						'SpecialCombination.all_products_in_combination' => $combined_product_id,
						'SpecialProductCombination.effective_date <' => $effective_date_before_update
					),
					'joins' => array(
						array(
							'alias' => 'SpecialProductCombination',
							'table' => 'special_product_combinations',
							'type' => 'INNER',
							'conditions' => 'SpecialCombination.id = SpecialProductCombination.combination_id'
						)
					),
					'fields' => array(
						'SpecialCombination.*','SpecialProductCombination.*'
					)
				));
				if(!empty($update_prev_date_list)){
					$update_prev_short_list = array();
					foreach($update_prev_date_list as $update_prev_date_key=>$update_prev_date_val){
						array_push($update_prev_short_list,$update_prev_date_val['SpecialProductCombination']['effective_date']);
					}
					asort($update_prev_short_list);
					$update_prev_date = array_pop($update_prev_short_list);
					
					/*------------ end update prev date ------------*/
					/*------- start previous row data --------*/
					if(!empty($update_prev_date)){
						$this->SpecialProductPrice->unbindModel(
							array('hasMany' => array('SpecialProductCombination'))
						);
						$update_prev_data = $this->SpecialProductPrice->find('all',array(
							'conditions' => array(
								'SpecialProductPrice.effective_date' => $update_prev_date,
								'SpecialProductPrice.has_combination' => 1,
								'SpecialProductPrice.institute_id' => 0,
								'SpecialProductPrice.product_id' => $combined_product
							),
							'joins' => array(
								array(
									'alias' => 'SpecialProductCombination',
									'table' => 'special_product_combinations',
									'type' => 'INNER',
									'conditions' => 'SpecialProductPrice.id = SpecialProductCombination.product_price_id'
								)
							),
							'fields' => array(
								'SpecialProductPrice.*','SpecialProductCombination.*'
							)
						));
					}
					/*------- end previous row data --------*/
				}
	
				/*------------ start update next date ------------*/
				$this->SpecialCombination->unbindModel(
					array('hasMany' => array('SpecialProductCombination'))
				);
				$update_next_date_list = $this->SpecialCombination->find('all',array(
					'conditions' => array(
						'SpecialCombination.all_products_in_combination' => $combined_product_id,
						'SpecialProductCombination.effective_date >' => $effective_date_before_update
					),
					'joins' => array(
						array(
							'alias' => 'SpecialProductCombination',
							'table' => 'special_product_combinations',
							'type' => 'INNER',
							'conditions' => 'SpecialCombination.id = SpecialProductCombination.combination_id'
						)
					),
					'fields' => array(
						'SpecialCombination.*','SpecialProductCombination.*'
					)
				));
				if(!empty($update_next_date_list)){
					$update_next_short_list = array();
					foreach($update_next_date_list as $update_next_date_key=>$update_next_date_val){
						array_push($update_next_short_list,$update_next_date_val['SpecialProductCombination']['effective_date']);
					}
					rsort($update_next_short_list);
					$update_next_date = end($update_next_short_list);
					
					/*------------ end update next date ------------*/
					/*------- start Next row data --------*/
					$this->SpecialProductPrice->unbindModel(
						array('hasMany' => array('SpecialProductCombination'))
					);
					$update_next_data = $this->SpecialProductPrice->find('all',array(
						'conditions' => array(
							'SpecialProductPrice.effective_date' => $update_next_date,
							'SpecialProductPrice.has_combination' => 1,
							'SpecialProductPrice.institute_id' => 0,
							'SpecialProductPrice.product_id' => $combined_product
						),
						'joins' => array(
							array(
								'alias' => 'SpecialProductCombination',
								'table' => 'special_product_combinations',
								'type' => 'INNER',
								'conditions' => 'SpecialProductPrice.id = SpecialProductCombination.product_price_id'
							)
						),
						'fields' => array(
							'SpecialProductPrice.*','SpecialProductCombination.*'
						)
					));
					/*------- end Next row data --------*/
				}
				/*-------- Start delete product price and product combination---------*/
				
				$delete_all = 0;
				foreach($for_update_product as $price_id ){
					if(!empty($price_id['SpecialProductCombination']['product_price_id']) && $price_id['SpecialProductCombination']['product_price_id'] !=0){
						
						$this->SpecialProductPrice->id = $price_id['SpecialProductCombination']['product_price_id'];
						if (!$this->SpecialProductPrice->exists()) {
							throw new NotFoundException(__('Invalid ProductPrice'));
						}
						if ($this->SpecialProductPrice->delete()) {
							// echo 'delete';exit;
							$this->SpecialProductCombination->product_price_id = $price_id['SpecialProductCombination']['product_price_id'];
							$this->SpecialProductCombination->delete();
							//$this->SpecialProductCombination->deleteAll(array('SpecialProductCombination.product_price_id'=>$price_id['SpecialProductCombination']['product_price_id']));
							$delete_all = 1;
						}else{
							$delete_all = 0;
						}
					}
				}
				// echo 'after_delete';exit;
				/*-------- End delete product price and product combination---------*/
				if($delete_all==1){
					$this->SpecialCombination->id = $id;
					$this->request->data['SpecialCombination']['updated_at'] = $this->current_datetime(); ;
					$this->request->data['SpecialCombination']['updated_by'] = $this->UserAuth->getUserId();
					if ($this->SpecialCombination->save($this->request->data)) {
							// for add product
							if(isset($this->request->data['SpecialProductCombination']['product_id'])){
								$data['SpecialProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['SpecialProductCombination']['effective_date']));
								$insert_data['SpecialProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['SpecialProductCombination']['effective_date']));
								$insert_data_array = array();
								foreach($this->request->data['SpecialProductCombination']['product_id'] as $ikey=>$ival)
								{	
									$combined_slab_price = $this->SpecialProductCombination->find('first',array(
										'conditions' => array('SpecialProductCombination.id'=>$this->request->data['SpecialProductCombination']['parent_slab_id'][$ikey])
										
									));
									$all_products_in_combination[] = $ival;
									// Product Price Table Entry
									$data['SpecialProductPrice']['product_id'] = $ival;
									$data['SpecialProductPrice']['has_combination'] = 1;
									$data['SpecialProductPrice']['has_price_slot'] = 1;
									$data['SpecialProductPrice']['is_active'] = 1;
									$data['SpecialProductPrice']['institute_id'] = 0;
									$data['SpecialProductPrice']['target_custommer'] = 0;
									$data['SpecialProductPrice']['created_at'] = $this->current_datetime(); ;
									$data['SpecialProductPrice']['updated_at'] = $this->current_datetime(); ;
									$data['SpecialProductPrice']['created_by'] = $this->UserAuth->getUserId();
									$data['SpecialProductPrice']['updated_by'] = $this->UserAuth->getUserId();
									$this->SpecialProductPrice->create();
									$this->SpecialProductPrice->save($data);
									$product_price_id = $this->SpecialProductPrice->getLastInsertID();
								
								
									$insert_data['SpecialProductCombination']['combination_id'] = $this->SpecialCombination->id;
									$insert_data['SpecialProductCombination']['product_price_id'] = $product_price_id;
									$insert_data['SpecialProductCombination']['product_id'] = $ival;
									$insert_data['SpecialProductCombination']['parent_slab_id'] = $this->request->data['SpecialProductCombination']['parent_slab_id'][$ikey];
									$insert_data['SpecialProductCombination']['price'] = $combined_slab_price['SpecialProductCombination']['price'];
									$insert_data['SpecialProductCombination']['min_qty'] = $this->request->data['SpecialProductCombination']['min_qty'];
									$insert_data['SpecialProductCombination']['created_at'] = $this->current_datetime();
									$insert_data['SpecialProductCombination']['updated_at'] = $this->current_datetime();
									$insert_data['SpecialProductCombination']['created_by'] = $this->UserAuth->getUserId();
									$insert_data['SpecialProductCombination']['updated_by'] = $this->UserAuth->getUserId();
									$insert_data_array[] = $insert_data;
								}				
								$this->SpecialProductCombination->saveAll($insert_data_array);
								/*-------------- start update prev data by end date -------------*/
								if(!empty($update_next_date) && !empty($update_prev_date)){
									foreach($update_prev_data as $update_prev_key=>$update_prev_val){
										$update_product_price['SpecialProductPrice']['id'] = $update_prev_val['SpecialProductPrice']['id'];
										$update_product_price['SpecialProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($update_next_date)))));
										if($this->SpecialProductPrice->save($update_product_price)){
											$update_product_combination['SpecialProductCombination']['id'] = $update_prev_val['SpecialProductCombination']['id'];
											$update_product_combination['SpecialProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($update_next_date)))));
											$this->SpecialProductCombination->save($update_product_combination);
										}
									}
								}
								if(empty($update_next_date) && !empty($update_prev_date)){
									foreach($update_prev_data as $update_prev_key=>$update_prev_val){
										$update_product_price['SpecialProductPrice']['id'] = $update_prev_val['SpecialProductPrice']['id'];
										$update_product_price['SpecialProductPrice']['end_date'] = NULL;
										if($this->SpecialProductPrice->save($update_product_price)){
											$update_product_combination['SpecialProductCombination']['id'] = $update_prev_val['SpecialProductCombination']['id'];
											$update_product_combination['SpecialProductCombination']['end_date'] = NULL;
											$this->SpecialProductCombination->save($update_product_combination);
										}
									}
								}
								/*-------------- end update prev data by end date ---------------*/
								/*-------------- start updated info -------------- */
								$this->SpecialCombination->unbindModel(
									array('hasMany' => array('SpecialProductCombination'))
								);
								$updated_row_info = $this->SpecialCombination->find('first',array(
									'conditions' => array(
										'SpecialCombination.id' => $id
									),
									'joins' => array(
										array(
											'alias' => 'SpecialProductCombination',
											'table' => 'special_product_combinations',
											'type' => 'INNER',
											'conditions' => 'SpecialCombination.id = SpecialProductCombination.combination_id'
										)
									),
									'fields' => array(
										'SpecialCombination.*','SpecialProductCombination.*'
									)
								));
								$updated_effective_date = $updated_row_info['SpecialProductCombination']['effective_date'];
								/*-------------- end updated info -------------- */
								
								/*---------- start updated prev date-------*/
								$this->SpecialCombination->unbindModel(
									array('hasMany' => array('SpecialProductCombination'))
								);
								$updated_prev_date_list = $this->SpecialCombination->find('all',array(
									'conditions' => array(
										'SpecialCombination.all_products_in_combination' => $combined_product_id,
										'SpecialProductCombination.effective_date <' => $updated_effective_date
									),
									'joins' => array(
										array(
											'alias' => 'SpecialProductCombination',
											'table' => 'special_product_combinations',
											'type' => 'INNER',
											'conditions' => 'SpecialCombination.id = SpecialProductCombination.combination_id'
										)
									),
									'fields' => array(
										'SpecialCombination.*','SpecialProductCombination.*'
									)
								));
								if(!empty($updated_prev_date_list)){
									$updated_prev_short_list = array();
									foreach($updated_prev_date_list as $updated_prev_date_key=>$updated_prev_date_val){
										array_push($updated_prev_short_list,$updated_prev_date_val['SpecialProductCombination']['effective_date']);
									}
									asort($updated_prev_short_list);
									$updated_prev_date = array_pop($updated_prev_short_list);
									/*------------ end updated prev date ------------*/
									/*------- start updated previous row data --------*/
									$this->SpecialProductPrice->unbindModel(
										array('hasMany' => array('SpecialProductCombination'))
									);
									$updated_prev_data = $this->SpecialProductPrice->find('all',array(
										'conditions' => array(
											'SpecialProductPrice.effective_date' => $updated_prev_date,
											'SpecialProductPrice.has_combination' => 1,
											'SpecialProductPrice.institute_id' => 0,
											'SpecialProductPrice.product_id' => $combined_product
										),
										'joins' => array(
											array(
												'alias' => 'SpecialProductCombination',
												'table' => 'special_product_combinations',
												'type' => 'INNER',
												'conditions' => 'SpecialProductPrice.id = SpecialProductCombination.product_price_id'
											)
										),
										'fields' => array(
											'SpecialProductPrice.*','SpecialProductCombination.*'
										)
									));
									/*------- end updated previous row data --------*/
								}
								
	
								/*------------ start updated next date ------------*/
								$this->SpecialCombination->unbindModel(
									array('hasMany' => array('SpecialProductCombination'))
								);
								$updated_next_date_list = $this->SpecialCombination->find('all',array(
									'conditions' => array(
										'SpecialCombination.all_products_in_combination' => $combined_product_id,
										'SpecialProductCombination.effective_date >' => $updated_effective_date
									),
									'joins' => array(
										array(
											'alias' => 'SpecialProductCombination',
											'table' => 'special_product_combinations',
											'type' => 'INNER',
											'conditions' => 'SpecialCombination.id = SpecialProductCombination.combination_id'
										)
									),
									'fields' => array(
										'SpecialCombination.*','SpecialProductCombination.*'
									)
								));
								if(!empty($updated_next_date_list)){
									$updated_next_short_list = array();
									foreach($updated_next_date_list as $updated_next_date_key=>$updated_next_date_val){
										array_push($updated_next_short_list,$updated_next_date_val['SpecialProductCombination']['effective_date']);
									}
									rsort($updated_next_short_list);
									$updated_next_date = end($updated_next_short_list);
								}
								/*------------ end updated next date ------------*/
								
								/*------------- start update updated prev and next row ------------*/
								if(!empty($updated_prev_date)){
									foreach($updated_prev_data as $updated_prev_key=>$updated_prev_val){
										$updated_product_price['SpecialProductPrice']['id'] = $updated_prev_val['SpecialProductPrice']['id'];
										$updated_product_price['SpecialProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_effective_date)))));
										if($this->SpecialProductPrice->save($updated_product_price)){
											$updated_product_combination['SpecialProductCombination']['id'] = $updated_prev_val['SpecialProductCombination']['id'];
											$updated_product_combination['SpecialProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_effective_date)))));
											$this->SpecialProductCombination->save($updated_product_combination);
										}
									}
								}
								/*---------- start updated effective data -----------*/
								$this->SpecialProductPrice->unbindModel(
									array('hasMany' => array('SpecialProductCombination'))
								);
								$updated_effective_data = $this->SpecialProductPrice->find('all',array(
									'conditions' => array(
										'SpecialProductPrice.effective_date' => $updated_effective_date,
										'SpecialProductPrice.has_combination' => 1,
										'SpecialProductPrice.institute_id' => 0,
										'SpecialProductPrice.product_id' => $combined_product
									),
									'joins' => array(
										array(
											'alias' => 'SpecialProductCombination',
											'table' => 'special_product_combinations',
											'type' => 'INNER',
											'conditions' => 'SpecialProductPrice.id = SpecialProductCombination.product_price_id'
										)
									),
									'fields' => array(
										'SpecialProductPrice.*','SpecialProductCombination.*'
									)
								));
								/*---------- end updated effective data -----------*/
								if(!empty($updated_next_date)){
									foreach($updated_effective_data as $updated_effec_key=>$updated_effec_val){
										$updated_effec_price['SpecialProductPrice']['id'] = $updated_effec_val['SpecialProductPrice']['id'];
										$updated_effec_price['SpecialProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_next_date)))));
										if($this->SpecialProductPrice->save($updated_effec_price)){
											$updated_effec_product_combination['SpecialProductCombination']['id'] = $updated_effec_val['SpecialProductCombination']['id'];
											$updated_effec_product_combination['SpecialProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_next_date)))));
											$this->SpecialProductCombination->save($updated_effec_product_combination);
										}
									}
								}else{
									foreach($updated_effective_data as $updated_effec_key=>$updated_effec_val){
										$updated_effec_price['SpecialProductPrice']['id'] = $updated_effec_val['SpecialProductPrice']['id'];
										$updated_effec_price['SpecialProductPrice']['end_date'] = NULL;
										if($this->SpecialProductPrice->save($updated_effec_price)){
											$updated_effec_product_combination['SpecialProductCombination']['id'] = $updated_effec_val['SpecialProductCombination']['id'];
											$updated_effec_product_combination['SpecialProductCombination']['end_date'] = NULL;
											$this->SpecialProductCombination->save($updated_effec_product_combination);
										}
									}
								}
								/*------------- end update updated prev and next row ------------*/
								
								
								
								
							}					
						
	
						//sort all_products_in_combination in ascending order. This will be used for selejt querys
						asort($all_products_in_combination);
						$all_products_in_combination = implode(',', $all_products_in_combination);
						$this->data = array();
						$this->request->data['SpecialCombination']['all_products_in_combination'] = $all_products_in_combination;
						$this->request->data['SpecialCombination']['id'] = $this->SpecialCombination->id;
						$this->SpecialCombination->save($this->request->data);
						
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
		
		$options = array('conditions' => array('SpecialCombination.' . $this->SpecialCombination->primaryKey => $id));
		$this->request->data = $this->SpecialCombination->find('first', $options);
		
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
		$this->loadModel('SpecialCombination');
		$this->loadModel('SpecialProductCombination');
		$this->loadModel('SpecialProductPrice');
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->SpecialCombination->id = $id;
		if (!$this->SpecialCombination->exists()) {
			throw new NotFoundException(__('Invalid Combination'));
		}
		/*-------- start deleted row info ---------*/
		if(!empty($id)){
			$deleted_data = $this->SpecialCombination->find('first',array(
				'conditions' => array('SpecialCombination.id' => $id)
			));
			$combined_product_id = $deleted_data['SpecialCombination']['all_products_in_combination'];
			$combined_product_array = explode(',',$combined_product_id);
			$deleted_effective_date = $deleted_data['SpecialProductCombination'][0]['effective_date'];
			if(!empty($deleted_data)){
			$delete_price_id_list = array();
				foreach($deleted_data['SpecialProductCombination'] as $p_price_key=>$p_price_val){
					array_push($delete_price_id_list,$p_price_val['product_price_id']);
				}
			}
		}
		/*-------- end deleted row info ---------*/
		
		/*----- start prev date and data --------*/
		$this->SpecialCombination->unbindModel(
			array('hasMany' => array('SpecialProductCombination'))
		);
		$prev_combination_date = $this->SpecialCombination->find('all',array(
			'conditions' => array(
				'SpecialCombination.all_products_in_combination' => $combined_product_id,
				'SpecialProductCombination.effective_date <' => $deleted_effective_date
			),
			'joins' => array(
				array(
					'alias' => 'SpecialProductCombination',
					'table' => 'special_product_combinations',
					'type' => 'INNER',
					'conditions' => 'SpecialCombination.id = SpecialProductCombination.combination_id'
				)
			),
			'fields' => array(
				'SpecialCombination.*','SpecialProductCombination.*'
			)
		));
		if(!empty($prev_combination_date)){
			$prev_short_list = array();
			foreach($prev_combination_date as $prev_com_key=>$prev_com_val){
				$prev_com_val['SpecialProductCombination']['effective_date'];
				array_push($prev_short_list,$prev_com_val['SpecialProductCombination']['effective_date']);
			}
			asort($prev_short_list);
			$immediate_pre_date = array_pop($prev_short_list);
			/*---------- start prev data ----------*/
			$this->SpecialProductPrice->unbindModel(
				array('hasMany' => array('SpecialProductCombination'))
			);
			$previous_row_data = $this->SpecialProductPrice->find('all',array(
				'conditions' => array(
					'SpecialProductPrice.effective_date' => date('Y-m-d',strtotime($immediate_pre_date)),
					'SpecialProductPrice.has_combination' => 1,
					'SpecialProductPrice.institute_id' => 0,
					'SpecialProductPrice.product_id' => $combined_product_array
				),
				'joins' => array(
					array(
						'alias' => 'SpecialProductCombination',
						'table' => 'special_product_combinations',
						'type' => 'INNER',
						'conditions' => 'SpecialProductPrice.id = SpecialProductCombination.product_price_id'
					)
				),
				'fields' => array(
					'SpecialProductPrice.*','SpecialProductCombination.*'
				)
			));
			/*---------- end prev data ----------*/
			
		}
		/*----- end prev date and data --------*/
		
		/*------ start next date -------*/
		$this->SpecialCombination->unbindModel(
			array('hasMany' => array('SpecialProductCombination'))
		);
		$next_combination_date = $this->SpecialCombination->find('all',array(
			'conditions' => array(
				'SpecialCombination.all_products_in_combination' => $combined_product_id,
				'SpecialProductCombination.effective_date >' => $deleted_effective_date
			),
			'joins' => array(
				array(
					'alias' => 'SpecialProductCombination',
					'table' => 'special_product_combinations',
					'type' => 'INNER',
					'conditions' => 'SpecialCombination.id = SpecialProductCombination.combination_id'
				)
			),
			'fields' => array(
				'SpecialCombination.*','SpecialProductCombination.*'
			)
		));
		if(!empty($next_combination_date)){
			$next_short_list = array();
			foreach($next_combination_date as $next_com_key=>$next_com_val){
				array_push($next_short_list,$next_com_val['SpecialProductCombination']['effective_date']);
			}
			rsort($next_short_list);
			$immediate_next_date = array_pop($next_short_list);
		}
		/*------ end next date -------*/

		if ($this->SpecialCombination->delete()) {
			$this->SpecialProductPrice->deleteAll(array('id' => $delete_price_id_list), false);
			
			/*-------- start update prev by next ---------*/
			if(!empty($immediate_next_date) && !empty($previous_row_data)){
				foreach($previous_row_data as $update_prev_data_key=>$update_prev_data_val){
					$update_prev_product_price['SpecialProductPrice']['id'] = $update_prev_data_val['SpecialProductPrice']['id'];
					$update_prev_product_price['SpecialProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immediate_next_date)))));
					if($this->SpecialProductPrice->save($update_prev_product_price)){
						$update_prev_product_combination['SpecialProductCombination']['id'] = $update_prev_data_val['SpecialProductCombination']['id'];
						$update_prev_product_combination['SpecialProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immediate_next_date)))));
						$this->SpecialProductCombination->save($update_prev_product_combination);
					}
				}
			}else if(!empty($previous_row_data)){
				foreach($previous_row_data as $update_prev_data_key=>$update_prev_data_val){
					$update_prev_product_price['SpecialProductPrice']['id'] = $update_prev_data_val['SpecialProductPrice']['id'];
					$update_prev_product_price['SpecialProductPrice']['end_date'] = NULL;
					if($this->SpecialProductPrice->save($update_prev_product_price)){
						$update_prev_product_combination['SpecialProductCombination']['id'] = $update_prev_data_val['SpecialProductCombination']['id'];
						$update_prev_product_combination['SpecialProductCombination']['end_date'] = NULL;
						$this->SpecialProductCombination->save($update_prev_product_combination);
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
		$this->loadModel('SpecialProductPrice');
		$current_date = $this->current_date();
		$product_id = $this->request->data['product_id'];
		/*------- start get prev effective date and next effective date ---------*/
		$effective_prev_date = $this->SpecialProductPrice->find('all',
			array(
				'conditions' => array('SpecialProductPrice.product_id' => $product_id,'SpecialProductPrice.effective_date <=' => $current_date,'SpecialProductPrice.institute_id'=>0,'SpecialProductPrice.has_combination'=>0),
				'fields' => array('SpecialProductPrice.effective_date')
			)
		);
		if(!empty($effective_prev_date)){
				$prev_short_list = array();
				foreach($effective_prev_date as $prev_date_key => $prev_date_val){
					array_push($prev_short_list,$prev_date_val['SpecialProductPrice']['effective_date']);
				}
				asort($prev_short_list);
				$prev_date = end($prev_short_list);
		}
		$effective_next_date = $this->SpecialProductPrice->find('all',
			array(
				'conditions' => array('SpecialProductPrice.product_id' => $product_id,'SpecialProductPrice.effective_date >' => $current_date,'SpecialProductPrice.institute_id'=>0,'SpecialProductPrice.has_combination'=>0),
				'fields' => array('SpecialProductPrice.effective_date')
			)
		);
		if(!empty($effective_next_date)){
				$next_short_list = array();
				foreach($effective_next_date as $next_date_key => $next_date_val){
					array_push($next_short_list,$next_date_val['SpecialProductPrice']['effective_date']);
				}
				rsort($next_short_list);
				$next_date = end($next_short_list);
		}
		if(isset($next_date)){
			$condition_value['SpecialProductCombination.effective_date <'] = $next_date;
		}
		if(isset($prev_date)){
			$condition_value['SpecialProductCombination.effective_date >='] = $prev_date;
		}
		$condition_value['SpecialProductCombination.product_id'] = $product_id;
		$condition_value['SpecialProductCombination.combination_id'] = 0;
		/*------- end get prev effective date and next effective date ---------*/
		
		
		if(!empty($product_id)){
			$slab_list = $this->SpecialProductCombination->find('all',array(
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
			if($parent_slab_id==$val['SpecialProductCombination']['id']){
				$html = $html.'<option selected value="'.$val['SpecialProductCombination']['id'].'">'.$val['SpecialProductCombination']['min_qty'].' ('.$val['SpecialProductCombination']['effective_date'].') </option>';
			}else{
				// $html = $html.'<option value="'.$key.'">'.$val.'</option>';
				$html = $html.'<option value="'.$val['SpecialProductCombination']['id'].'">'.$val['SpecialProductCombination']['min_qty'].' ('.$val['SpecialProductCombination']['effective_date'].') </option>';
			}
		}
		echo $html;
		$this->autoRender = false;
	}
}
