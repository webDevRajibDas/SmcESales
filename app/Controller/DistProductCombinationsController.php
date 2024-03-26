<?php
App::uses('AppController', 'Controller');
/**
 * DistProductCombinations Controller
 *
 * @property DistProductCombination $DistProductCombination
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistProductCombinationsController extends AppController {

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
	$this->loadModel('DistCombination');
	$this->set('page_title','DistCombination List');
	$this->DistCombination->recursive = 0;
	$joins= array();
	$conditions = array();
	$product_id = '';
	if(isset($id))
	{
		$joins = array(
			array(
				'table'=>'dist_product_combinations',
				'type'=>'LEFT',
				'alias'=>'PC',
				'conditions'=>array('PC.combination_id = DistCombination.id')
				)
			);
		$conditions = array('PC.product_id' => $id);
		$product_id = $id;
	}
	else
	{
		$joins = array(
		array(
			'table'=>'dist_product_combinations',
			'type'=>'LEFT',
			'alias'=>'PC',
			'conditions'=>array('PC.combination_id = DistCombination.id')
			)
		);
	}
	
	$this->paginate = array(	
		'joins' => $joins,
		'conditions' => $conditions,
		'fields' => array('DISTINCT DistCombination.id','DistCombination.name','PC.effective_date','PC.min_qty'),
		'order'=>'DistCombination.id DESC',		
			//'group' => array('DistCombination.id')
			
		);
		$this->set('product_id', $product_id);
		$this->set('DistCombinations', $this->paginate('DistCombination'));
		
		/* check combine_id is existed in memo_details table */
		
		$sql="select md.product_combination_id as com_id,count(md.id) as id_num from memo_details md 
				left join dist_combinations c on c.id=md.product_combination_id
				left join memos m on m.id=md.memo_id
				where md.product_combination_id is not NULL and m.is_distributor=1
				group by product_combination_id";
				
        $data_sql = $this->DistCombination->Query($sql);
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
		if (!$this->DistProductCombination->exists($id)) {
			throw new NotFoundException(__('Invalid product combination'));
		}
		$options = array('conditions' => array('DistProductCombination.' . $this->DistProductCombination->primaryKey => $id));
		$this->set('DistProductCombination', $this->DistProductCombination->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add($product_id = null) {
		$this->set('page_title','Add Combination');
		$this->loadModel('DistCombination');
		$this->loadModel('Product');
		$this->loadModel('DistProductPrice');
		if(!empty($product_id)){
			$this->DistProductCombination->virtualFields['min_qty_with_effective_date']='CONCAT(DistProductCombination.min_qty,\' (\',DistProductCombination.effective_date,\')\')';
			$slab_list = $this->DistProductCombination->find('list',array(
			'conditions' => array('DistProductCombination.combination_id' => 0,'DistProductCombination.product_id' => $product_id),
			'fields'=>array('DistProductCombination.id','min_qty_with_effective_date'),
			));
			
		}else{
			$slab_list = '';
		}
		if ($this->request->is('post')) {
			$new_start_date = date('Y-m-d',strtotime($this->request->data['DistProductCombination']['effective_date']));
			if(!empty($this->request->data['DistProductCombination']['product_id'])){
				$combined_product = $this->request->data['DistProductCombination']['product_id'];
				asort($combined_product);
				$combined_product_id = implode(',', $combined_product);
				$this->DistCombination->unbindModel(
					array('hasMany' => array('DistProductCombination'))
				);
				$prev_combination_date = $this->DistCombination->find('all',array(
					'conditions' => array(
						'DistCombination.all_products_in_combination' => $combined_product_id,
						'DistProductCombination.effective_date <' => $new_start_date
					),
					'joins' => array(
						array(
							'alias' => 'DistProductCombination',
							'table' => 'dist_product_combinations',
							'type' => 'INNER',
							'conditions' => 'DistCombination.id = DistProductCombination.combination_id'
						)
					),
					'fields' => array(
						'DistCombination.*','DistProductCombination.*'
					)
				));
				if(!empty($prev_combination_date)){
					$prev_short_list = array();
					foreach($prev_combination_date as $prev_com_key=>$prev_com_val){
						$prev_com_val['DistProductCombination']['effective_date'];
						array_push($prev_short_list,$prev_com_val['DistProductCombination']['effective_date']);
					}
					asort($prev_short_list);
					$immediate_pre_date = array_pop($prev_short_list);
				}
				/* ------- start next start date ---------*/
				$this->DistCombination->unbindModel(
					array('hasMany' => array('DistProductCombination'))
				);
				$next_combination_date = $this->DistCombination->find('all',array(
					'conditions' => array(
						'DistCombination.all_products_in_combination' => $combined_product_id,
						'DistProductCombination.effective_date >' => $new_start_date
					),
					'joins' => array(
						array(
							'alias' => 'DistProductCombination',
							'table' => 'dist_product_combinations',
							'type' => 'INNER',
							'conditions' => 'DistCombination.id = DistProductCombination.combination_id'
						)
					),
					'fields' => array(
						'DistCombination.*','DistProductCombination.*'
					)
				));
				if(!empty($next_combination_date)){
					$next_short_list = array();
					foreach($next_combination_date as $next_com_key=>$next_com_val){
						array_push($next_short_list,$next_com_val['DistProductCombination']['effective_date']);
					}
					rsort($next_short_list);
					$immediate_next_date = array_pop($next_short_list);
				}
				/* ------- end next start date ---------*/
				/*------- start previous row data --------*/
				$this->DistProductPrice->unbindModel(
					array('hasMany' => array('DistProductCombination'))
				);
				if(!empty($immediate_pre_date)){
					$prev_row_data = $this->DistProductPrice->find('all',array(
						'conditions' => array(
							'DistProductPrice.effective_date' => $immediate_pre_date,
							'DistProductPrice.has_combination' => 1,
							'DistProductPrice.institute_id' => 0,
							'DistProductPrice.product_id' => $combined_product
						),
						'joins' => array(
							array(
								'alias' => 'DistProductCombination',
								'table' => 'dist_product_combinations',
								'type' => 'INNER',
								'conditions' => 'DistProductPrice.id = DistProductCombination.product_price_id'
							)
						),
						'fields' => array(
							'DistProductPrice.*','DistProductCombination.*'
						)
					));
				}
/* 				echo "<pre>";
				print_r($prev_row_data);
				exit; */
				/*------- end previous row data --------*/
			}
			$all_products_in_combination = array();
			$this->request->data['DistCombination']['created_at'] = $this->current_datetime(); 
			$this->request->data['DistCombination']['updated_at'] = $this->current_datetime(); 
			$this->request->data['DistCombination']['created_by'] = $this->UserAuth->getUserId();
			$data['DistProductPrice']['created_at'] = $this->current_datetime(); 
			$data['DistProductPrice']['updated_at'] = $this->current_datetime(); 
			$data['DistProductPrice']['created_by'] = $this->UserAuth->getUserId();
			
			
			$request_product_list = $this->request->data['DistProductCombination']['product_id'];
			$this->loadModel('DistProductCombination');
			$existing_product_list = $this->DistProductCombination->find('all',array(
			'conditions' => array(
				'combination_id !=' => 0,
				'effective_date'=>$new_start_date
				),
			'fields' => array('id', 'product_id'),
			'recursive' => -1
			));
			$existing = 0;
			foreach($existing_product_list as $pro_list){
				$pro_id = $pro_list['DistProductCombination']['product_id'];
				if (in_array($pro_id, $request_product_list)){
				  $existing = 1;
				}
			}
			
			// forcely 
			$existing = 0;
			
			if($existing==0)
			{
				$this->DistCombination->create();
				if ($this->DistCombination->save($this->request->data)){
				$recent_inserted_combination_id = $this->DistCombination->getInsertID();
					if(!empty($this->request->data['DistProductCombination'])){
						if(!empty($this->request->data['DistCombination']['redirect_product_id'])){
							$redirect_product_id = $this->request->data['DistCombination']['redirect_product_id'];
						}
						$data['DistProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['DistProductCombination']['effective_date']));
						$data['DistProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['DistProductCombination']['effective_date']));
						foreach($this->request->data['DistProductCombination']['product_id'] as $key=>$val)
						{
							$combined_slab_price = $this->DistProductCombination->find('first',array(
								'conditions' => array('DistProductCombination.id'=>$this->request->data['DistProductCombination']['parent_slab_id'][$key])
								
							));
							$all_products_in_combination[] = $val;
							$data['DistProductPrice']['product_id'] = $val;
							$data['DistProductPrice']['has_combination'] = 1;
							$data['DistProductPrice']['has_price_slot'] = 1;
							$data['DistProductPrice']['is_active'] = 1;
							$data['DistProductPrice']['institute_id'] = 0;
							$data['DistProductPrice']['target_custommer'] = 0;
							$this->DistProductPrice->create();
							$this->DistProductPrice->save($data);
							$product_price_id = $this->DistProductPrice->getLastInsertID();
							
							$data['DistProductCombination']['product_price_id'] = $product_price_id;
							$data['DistProductCombination']['combination_id'] = $this->DistCombination->id;
							$data['DistProductCombination']['parent_slab_id'] = $this->request->data['DistProductCombination']['parent_slab_id'][$key];
							$data['DistProductCombination']['product_id'] = $val;
							$data['DistProductCombination']['price'] = $combined_slab_price['DistProductCombination']['price'];
							$data['DistProductCombination']['created_at'] = $this->current_datetime(); 
							$data['DistProductCombination']['updated_at'] = $this->current_datetime(); 
							$data['DistProductCombination']['created_by'] = $this->UserAuth->getUserId();
						
							// Multiple Quantity Slab For a Product
							foreach($this->request->data['DistProductCombination']['min_qty'] as $qty_key=>$qty_val)
							{
								$data['DistProductCombination']['min_qty'] = $qty_val;
								$this->DistProductCombination->create();
								$this->DistProductCombination->save($data); 
							}
						}								
						if(!empty($immediate_pre_date) && !empty($prev_row_data)){
							foreach($prev_row_data as $update_pre_data_key=>$update_pre_data_val){
								$update_product_price['DistProductPrice']['id'] = $update_pre_data_val['DistProductPrice']['id'];
								$update_product_price['DistProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($new_start_date)))));
								if($this->DistProductPrice->save($update_product_price)){
									$update_product_combination['DistProductCombination']['id'] = $update_pre_data_val['DistProductCombination']['id'];
									$update_product_combination['DistProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($new_start_date)))));
									$this->DistProductCombination->save($update_product_combination);
								}
							}
						}
					}
				//sort all_products_in_combination in ascending order. This will be used for selejt querys
				asort($all_products_in_combination);
				$all_products_in_combination = implode(',', $all_products_in_combination);
				$this->data = array();
				$this->request->data['DistCombination']['all_products_in_combination'] = $all_products_in_combination;
				$this->request->data['DistCombination']['id'] = $this->DistCombination->id;
				$this->DistCombination->save($this->request->data);
				
				/*---------- start recent inserted data ----------*/
				$this->DistProductPrice->unbindModel(
					array('hasMany' => array('DistProductCombination'))
				);
				$current_row_data = $this->DistProductPrice->find('all',array(
					'conditions' => array(
						'DistProductPrice.effective_date' => date('Y-m-d',strtotime($new_start_date)),
						'DistProductPrice.has_combination' => 1,
						'DistProductPrice.institute_id' => 0,
						'DistProductPrice.product_id' => $combined_product
					),
					'joins' => array(
						array(
							'alias' => 'DistProductCombination',
							'table' => 'dist_product_combinations',
							'type' => 'INNER',
							'conditions' => 'DistProductPrice.id = DistProductCombination.product_price_id'
						)
					),
					'fields' => array(
						'DistProductPrice.*','DistProductCombination.*'
					)
				));
				/*---------- end recent inserted data ----------*/
				/*------ start update current row data by end date -------*/
				if(!empty($immediate_next_date) && !empty($current_row_data)){
					foreach($current_row_data as $update_curr_data_key=>$update_curr_data_val){
						$update_curr_product_price['DistProductPrice']['id'] = $update_curr_data_val['DistProductPrice']['id'];
						$update_curr_product_price['DistProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immediate_next_date)))));
						if($this->DistProductPrice->save($update_curr_product_price)){
							$update_curr_product_combination['DistProductCombination']['id'] = $update_curr_data_val['DistProductCombination']['id'];
							$update_curr_product_combination['DistProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immediate_next_date)))));
							$this->DistProductCombination->save($update_curr_product_combination);
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
		$this->loadModel('DistCombination');
		$this->loadModel('Product');
		$this->loadModel('DistProductPrice');
		
		
		/* check combine_id is existed in memo_details table */
		
		$sql="select md.product_combination_id as com_id,count(md.id) as id_num from memo_details md 
				left join dist_combinations c on c.id=md.product_combination_id 
				where md.product_combination_id is not NULL 
				group by product_combination_id";
				
        $data_sql = $this->DistCombination->Query($sql);
		$com_ids=array();
		foreach ($data_sql as $each_data)
		{
			$ids=$each_data[0]['com_id'];
		    $com_ids[$ids]=$each_data[0]['id_num'];
		}
		$this->set('com_ids', $com_ids);
		$this->set('edit_id', $id);
		
		
		
		if(!empty($id)){
			$for_update_product = $this->DistProductCombination->find('all',array(
				'fields' => array('DistProductCombination.*'),
				'conditions' => array('DistProductCombination.combination_id' => $id)
			));
		}
		
		$total_slab = array();
		foreach($for_update_product as $val){
			if(!empty($val['DistProductCombination']['product_id'])){
				$slab_list = $this->DistProductCombination->find('list',array(
				'conditions' => array('DistProductCombination.combination_id' => 0,'DistProductCombination.product_id' => $val['DistProductCombination']['product_id'])
				));
			}else{
				$slab_list = '';
			}
			$total_slab[$val['DistProductCombination']['product_id']] = $slab_list;
		}
		$this->set('total_slab',$total_slab);
		
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$request_product_list = $this->request->data['DistProductCombination']['product_id'];
			$this->loadModel('DistProductCombination');
			$existing_product_list = $this->DistProductCombination->find('all',array(
			'conditions' => array(
				"NOT" => array( "combination_id" => array(0, $id) )
			),
			'fields' => array('id', 'product_id'),
			'recursive' => -1
			));
			$existing = 0;
			foreach($existing_product_list as $pro_list){
				$pro_id = $pro_list['DistProductCombination']['product_id'];
				if (in_array($pro_id, $request_product_list)){
				  $existing = 1;
				}
			}
			
			// forcely 
			$existing = 0;
			
			if($existing==0)
			{
				$combined_product = $this->request->data['DistProductCombination']['product_id'];
				asort($combined_product);
				$combined_product_id = implode(',', $combined_product);
				$all_products_in_combination = array();
				/*---------- start select for updated data ------------*/
				$this->DistCombination->unbindModel(
					array('hasMany' => array('DistProductCombination'))
				);
				$select_for_updated_data = $this->DistCombination->find('first',array(
					'conditions' => array(
						'DistCombination.id' => $id
					),
					'joins' => array(
						array(
							'alias' => 'DistProductCombination',
							'table' => 'dist_product_combinations',
							'type' => 'INNER',
							'conditions' => 'DistCombination.id = DistProductCombination.combination_id'
						)
					),
					'fields' => array(
						'DistCombination.*','DistProductCombination.*'
					)
				));
				$effective_date_before_update = $select_for_updated_data['DistProductCombination']['effective_date'];
				/*---------- end select for updated data ------------*/
				
				/*------------ start update prev date ------------*/
				$this->DistCombination->unbindModel(
					array('hasMany' => array('DistProductCombination'))
				);
				$update_prev_date_list = $this->DistCombination->find('all',array(
					'conditions' => array(
						'DistCombination.all_products_in_combination' => $combined_product_id,
						'DistProductCombination.effective_date <' => $effective_date_before_update
					),
					'joins' => array(
						array(
							'alias' => 'DistProductCombination',
							'table' => 'dist_product_combinations',
							'type' => 'INNER',
							'conditions' => 'DistCombination.id = DistProductCombination.combination_id'
						)
					),
					'fields' => array(
						'DistCombination.*','DistProductCombination.*'
					)
				));
				if(!empty($update_prev_date_list)){
					$update_prev_short_list = array();
					foreach($update_prev_date_list as $update_prev_date_key=>$update_prev_date_val){
						array_push($update_prev_short_list,$update_prev_date_val['DistProductCombination']['effective_date']);
					}
					asort($update_prev_short_list);
					$update_prev_date = array_pop($update_prev_short_list);
					
					/*------------ end update prev date ------------*/
					/*------- start previous row data --------*/
					if(!empty($update_prev_date)){
						$this->DistProductPrice->unbindModel(
							array('hasMany' => array('DistProductCombination'))
						);
						$update_prev_data = $this->DistProductPrice->find('all',array(
							'conditions' => array(
								'DistProductPrice.effective_date' => $update_prev_date,
								'DistProductPrice.has_combination' => 1,
								'DistProductPrice.institute_id' => 0,
								'DistProductPrice.product_id' => $combined_product
							),
							'joins' => array(
								array(
									'alias' => 'DistProductCombination',
									'table' => 'dist_product_combinations',
									'type' => 'INNER',
									'conditions' => 'DistProductPrice.id = DistProductCombination.product_price_id'
								)
							),
							'fields' => array(
								'DistProductPrice.*','DistProductCombination.*'
							)
						));
					}
					/*------- end previous row data --------*/
				}
	
				/*------------ start update next date ------------*/
				$this->DistCombination->unbindModel(
					array('hasMany' => array('DistProductCombination'))
				);
				$update_next_date_list = $this->DistCombination->find('all',array(
					'conditions' => array(
						'DistCombination.all_products_in_combination' => $combined_product_id,
						'DistProductCombination.effective_date >' => $effective_date_before_update
					),
					'joins' => array(
						array(
							'alias' => 'DistProductCombination',
							'table' => 'dist_product_combinations',
							'type' => 'INNER',
							'conditions' => 'DistCombination.id = DistProductCombination.combination_id'
						)
					),
					'fields' => array(
						'DistCombination.*','DistProductCombination.*'
					)
				));
				if(!empty($update_next_date_list)){
					$update_next_short_list = array();
					foreach($update_next_date_list as $update_next_date_key=>$update_next_date_val){
						array_push($update_next_short_list,$update_next_date_val['DistProductCombination']['effective_date']);
					}
					rsort($update_next_short_list);
					$update_next_date = end($update_next_short_list);
					
					/*------------ end update next date ------------*/
					/*------- start Next row data --------*/
					$this->DistProductPrice->unbindModel(
						array('hasMany' => array('DistProductCombination'))
					);
					$update_next_data = $this->DistProductPrice->find('all',array(
						'conditions' => array(
							'DistProductPrice.effective_date' => $update_next_date,
							'DistProductPrice.has_combination' => 1,
							'DistProductPrice.institute_id' => 0,
							'DistProductPrice.product_id' => $combined_product
						),
						'joins' => array(
							array(
								'alias' => 'DistProductCombination',
								'table' => 'dist_product_combinations',
								'type' => 'INNER',
								'conditions' => 'DistProductPrice.id = DistProductCombination.product_price_id'
							)
						),
						'fields' => array(
							'DistProductPrice.*','DistProductCombination.*'
						)
					));
					/*------- end Next row data --------*/
				}
				/*-------- Start delete product price and product combination---------*/
				$delete_all = 0;
				foreach($for_update_product as $price_id ){
					if(!empty($price_id['DistProductCombination']['product_price_id']) && $price_id['DistProductCombination']['product_price_id'] !=0){
						
						$this->DistProductPrice->id = $price_id['DistProductCombination']['product_price_id'];
						if (!$this->DistProductPrice->exists()) {
							throw new NotFoundException(__('Invalid ProductPrice'));
						}
						if ($this->DistProductPrice->delete()) {
							$this->DistProductCombination->product_price_id = $price_id['DistProductCombination']['product_price_id'];
							$this->DistProductCombination->delete();
							//$this->DistProductCombination->deleteAll(array('DistProductCombination.product_price_id'=>$price_id['DistProductCombination']['product_price_id']));
							$delete_all = 1;
						}else{
							$delete_all = 0;
						}
					}
				}
				/*-------- End delete product price and product combination---------*/
				if($delete_all==1){
					$this->DistCombination->id = $id;
					$this->request->data['DistCombination']['updated_at'] = $this->current_datetime(); ;
					$this->request->data['DistCombination']['updated_by'] = $this->UserAuth->getUserId();
					if ($this->DistCombination->save($this->request->data)) {
							// for add product
							if(isset($this->request->data['DistProductCombination']['product_id'])){
								$data['DistProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['DistProductCombination']['effective_date']));
								$insert_data['DistProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['DistProductCombination']['effective_date']));
								$insert_data_array = array();
								foreach($this->request->data['DistProductCombination']['product_id'] as $ikey=>$ival)
								{	
									$combined_slab_price = $this->DistProductCombination->find('first',array(
										'conditions' => array('DistProductCombination.id'=>$this->request->data['DistProductCombination']['parent_slab_id'][$ikey])
										
									));
									$all_products_in_combination[] = $ival;
									// Product Price Table Entry
									$data['DistProductPrice']['product_id'] = $ival;
									$data['DistProductPrice']['has_combination'] = 1;
									$data['DistProductPrice']['has_price_slot'] = 1;
									$data['DistProductPrice']['is_active'] = 1;
									$data['DistProductPrice']['institute_id'] = 0;
									$data['DistProductPrice']['target_custommer'] = 0;
									$data['DistProductPrice']['created_at'] = $this->current_datetime(); ;
									$data['DistProductPrice']['updated_at'] = $this->current_datetime(); ;
									$data['DistProductPrice']['created_by'] = $this->UserAuth->getUserId();
									$data['DistProductPrice']['updated_by'] = $this->UserAuth->getUserId();
									$this->DistProductPrice->create();
									$this->DistProductPrice->save($data);
									$product_price_id = $this->DistProductPrice->getLastInsertID();
								
								
									$insert_data['DistProductCombination']['combination_id'] = $this->DistCombination->id;
									$insert_data['DistProductCombination']['product_price_id'] = $product_price_id;
									$insert_data['DistProductCombination']['product_id'] = $ival;
									$insert_data['DistProductCombination']['parent_slab_id'] = $this->request->data['DistProductCombination']['parent_slab_id'][$ikey];
									$insert_data['DistProductCombination']['price'] = $combined_slab_price['DistProductCombination']['price'];
									$insert_data['DistProductCombination']['min_qty'] = $this->request->data['DistProductCombination']['min_qty'];
									$insert_data['DistProductCombination']['created_at'] = $this->current_datetime();
									$insert_data['DistProductCombination']['updated_at'] = $this->current_datetime();
									$insert_data['DistProductCombination']['created_by'] = $this->UserAuth->getUserId();
									$insert_data['DistProductCombination']['updated_by'] = $this->UserAuth->getUserId();
									$insert_data_array[] = $insert_data;
								}				
								$this->DistProductCombination->saveAll($insert_data_array);
								/*-------------- start update prev data by end date -------------*/
								if(!empty($update_next_date) && !empty($update_prev_date)){
									foreach($update_prev_data as $update_prev_key=>$update_prev_val){
										$update_product_price['DistProductPrice']['id'] = $update_prev_val['DistProductPrice']['id'];
										$update_product_price['DistProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($update_next_date)))));
										if($this->DistProductPrice->save($update_product_price)){
											$update_product_combination['DistProductCombination']['id'] = $update_prev_val['DistProductCombination']['id'];
											$update_product_combination['DistProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($update_next_date)))));
											$this->DistProductCombination->save($update_product_combination);
										}
									}
								}
								if(empty($update_next_date) && !empty($update_prev_date)){
									foreach($update_prev_data as $update_prev_key=>$update_prev_val){
										$update_product_price['DistProductPrice']['id'] = $update_prev_val['DistProductPrice']['id'];
										$update_product_price['DistProductPrice']['end_date'] = NULL;
										if($this->DistProductPrice->save($update_product_price)){
											$update_product_combination['DistProductCombination']['id'] = $update_prev_val['DistProductCombination']['id'];
											$update_product_combination['DistProductCombination']['end_date'] = NULL;
											$this->DistProductCombination->save($update_product_combination);
										}
									}
								}
								/*-------------- end update prev data by end date ---------------*/
								/*-------------- start updated info -------------- */
								$this->DistCombination->unbindModel(
									array('hasMany' => array('DistProductCombination'))
								);
								$updated_row_info = $this->DistCombination->find('first',array(
									'conditions' => array(
										'DistCombination.id' => $id
									),
									'joins' => array(
										array(
											'alias' => 'DistProductCombination',
											'table' => 'dist_product_combinations',
											'type' => 'INNER',
											'conditions' => 'DistCombination.id = DistProductCombination.combination_id'
										)
									),
									'fields' => array(
										'DistCombination.*','DistProductCombination.*'
									)
								));
								$updated_effective_date = $updated_row_info['DistProductCombination']['effective_date'];
								/*-------------- end updated info -------------- */
								
								/*---------- start updated prev date-------*/
								$this->DistCombination->unbindModel(
									array('hasMany' => array('DistProductCombination'))
								);
								$updated_prev_date_list = $this->DistCombination->find('all',array(
									'conditions' => array(
										'DistCombination.all_products_in_combination' => $combined_product_id,
										'DistProductCombination.effective_date <' => $updated_effective_date
									),
									'joins' => array(
										array(
											'alias' => 'DistProductCombination',
											'table' => 'dist_product_combinations',
											'type' => 'INNER',
											'conditions' => 'DistCombination.id = DistProductCombination.combination_id'
										)
									),
									'fields' => array(
										'DistCombination.*','DistProductCombination.*'
									)
								));
								if(!empty($updated_prev_date_list)){
									$updated_prev_short_list = array();
									foreach($updated_prev_date_list as $updated_prev_date_key=>$updated_prev_date_val){
										array_push($updated_prev_short_list,$updated_prev_date_val['DistProductCombination']['effective_date']);
									}
									asort($updated_prev_short_list);
									$updated_prev_date = array_pop($updated_prev_short_list);
									/*------------ end updated prev date ------------*/
									/*------- start updated previous row data --------*/
									$this->DistProductPrice->unbindModel(
										array('hasMany' => array('DistProductCombination'))
									);
									$updated_prev_data = $this->DistProductPrice->find('all',array(
										'conditions' => array(
											'DistProductPrice.effective_date' => $updated_prev_date,
											'DistProductPrice.has_combination' => 1,
											'DistProductPrice.institute_id' => 0,
											'DistProductPrice.product_id' => $combined_product
										),
										'joins' => array(
											array(
												'alias' => 'DistProductCombination',
												'table' => 'dist_product_combinations',
												'type' => 'INNER',
												'conditions' => 'DistProductPrice.id = DistProductCombination.product_price_id'
											)
										),
										'fields' => array(
											'DistProductPrice.*','DistProductCombination.*'
										)
									));
									/*------- end updated previous row data --------*/
								}
								
	
								/*------------ start updated next date ------------*/
								$this->DistCombination->unbindModel(
									array('hasMany' => array('DistProductCombination'))
								);
								$updated_next_date_list = $this->DistCombination->find('all',array(
									'conditions' => array(
										'DistCombination.all_products_in_combination' => $combined_product_id,
										'DistProductCombination.effective_date >' => $updated_effective_date
									),
									'joins' => array(
										array(
											'alias' => 'DistProductCombination',
											'table' => 'dist_product_combinations',
											'type' => 'INNER',
											'conditions' => 'DistCombination.id = DistProductCombination.combination_id'
										)
									),
									'fields' => array(
										'DistCombination.*','DistProductCombination.*'
									)
								));
								if(!empty($updated_next_date_list)){
									$updated_next_short_list = array();
									foreach($updated_next_date_list as $updated_next_date_key=>$updated_next_date_val){
										array_push($updated_next_short_list,$updated_next_date_val['DistProductCombination']['effective_date']);
									}
									rsort($updated_next_short_list);
									$updated_next_date = end($updated_next_short_list);
								}
								/*------------ end updated next date ------------*/
								
								/*------------- start update updated prev and next row ------------*/
								if(!empty($updated_prev_date)){
									foreach($updated_prev_data as $updated_prev_key=>$updated_prev_val){
										$updated_product_price['DistProductPrice']['id'] = $updated_prev_val['DistProductPrice']['id'];
										$updated_product_price['DistProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_effective_date)))));
										if($this->DistProductPrice->save($updated_product_price)){
											$updated_product_combination['DistProductCombination']['id'] = $updated_prev_val['DistProductCombination']['id'];
											$updated_product_combination['DistProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_effective_date)))));
											$this->DistProductCombination->save($updated_product_combination);
										}
									}
								}
								/*---------- start updated effective data -----------*/
								$this->DistProductPrice->unbindModel(
									array('hasMany' => array('DistProductCombination'))
								);
								$updated_effective_data = $this->DistProductPrice->find('all',array(
									'conditions' => array(
										'DistProductPrice.effective_date' => $updated_effective_date,
										'DistProductPrice.has_combination' => 1,
										'DistProductPrice.institute_id' => 0,
										'DistProductPrice.product_id' => $combined_product
									),
									'joins' => array(
										array(
											'alias' => 'DistProductCombination',
											'table' => 'dist_product_combinations',
											'type' => 'INNER',
											'conditions' => 'DistProductPrice.id = DistProductCombination.product_price_id'
										)
									),
									'fields' => array(
										'DistProductPrice.*','DistProductCombination.*'
									)
								));
								/*---------- end updated effective data -----------*/
								if(!empty($updated_next_date)){
									foreach($updated_effective_data as $updated_effec_key=>$updated_effec_val){
										$updated_effec_price['DistProductPrice']['id'] = $updated_effec_val['DistProductPrice']['id'];
										$updated_effec_price['DistProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_next_date)))));
										if($this->DistProductPrice->save($updated_effec_price)){
											$updated_effec_product_combination['DistProductCombination']['id'] = $updated_effec_val['DistProductCombination']['id'];
											$updated_effec_product_combination['DistProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_next_date)))));
											$this->DistProductCombination->save($updated_effec_product_combination);
										}
									}
								}else{
									foreach($updated_effective_data as $updated_effec_key=>$updated_effec_val){
										$updated_effec_price['DistProductPrice']['id'] = $updated_effec_val['DistProductPrice']['id'];
										$updated_effec_price['DistProductPrice']['end_date'] = NULL;
										if($this->DistProductPrice->save($updated_effec_price)){
											$updated_effec_product_combination['DistProductCombination']['id'] = $updated_effec_val['DistProductCombination']['id'];
											$updated_effec_product_combination['DistProductCombination']['end_date'] = NULL;
											$this->DistProductCombination->save($updated_effec_product_combination);
										}
									}
								}
								/*------------- end update updated prev and next row ------------*/
								
								
								
								
							}					
						
	
						//sort all_products_in_combination in ascending order. This will be used for selejt querys
						asort($all_products_in_combination);
						$all_products_in_combination = implode(',', $all_products_in_combination);
						$this->data = array();
						$this->request->data['DistCombination']['all_products_in_combination'] = $all_products_in_combination;
						$this->request->data['DistCombination']['id'] = $this->DistCombination->id;
						$this->DistCombination->save($this->request->data);
						
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
		
		$options = array('conditions' => array('DistCombination.' . $this->DistCombination->primaryKey => $id));
		$this->request->data = $this->DistCombination->find('first', $options);
		
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
		$this->loadModel('DistCombination');
		$this->loadModel('DistProductCombination');
		$this->loadModel('DistProductPrice');
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->DistCombination->id = $id;
		if (!$this->DistCombination->exists()) {
			throw new NotFoundException(__('Invalid Combination'));
		}
		/*-------- start deleted row info ---------*/
		if(!empty($id)){
			$deleted_data = $this->DistCombination->find('first',array(
				'conditions' => array('DistCombination.id' => $id)
			));
			$combined_product_id = $deleted_data['DistCombination']['all_products_in_combination'];
			$combined_product_array = explode(',',$combined_product_id);
			$deleted_effective_date = $deleted_data['DistProductCombination'][0]['effective_date'];
			if(!empty($deleted_data)){
			$delete_price_id_list = array();
				foreach($deleted_data['DistProductCombination'] as $p_price_key=>$p_price_val){
					array_push($delete_price_id_list,$p_price_val['product_price_id']);
				}
			}
		}
		/*-------- end deleted row info ---------*/
		
		/*----- start prev date and data --------*/
		$this->DistCombination->unbindModel(
			array('hasMany' => array('DistProductCombination'))
		);
		$prev_combination_date = $this->DistCombination->find('all',array(
			'conditions' => array(
				'DistCombination.all_products_in_combination' => $combined_product_id,
				'DistProductCombination.effective_date <' => $deleted_effective_date
			),
			'joins' => array(
				array(
					'alias' => 'DistProductCombination',
					'table' => 'dist_product_combinations',
					'type' => 'INNER',
					'conditions' => 'DistCombination.id = DistProductCombination.combination_id'
				)
			),
			'fields' => array(
				'DistCombination.*','DistProductCombination.*'
			)
		));
		if(!empty($prev_combination_date)){
			$prev_short_list = array();
			foreach($prev_combination_date as $prev_com_key=>$prev_com_val){
				$prev_com_val['DistProductCombination']['effective_date'];
				array_push($prev_short_list,$prev_com_val['DistProductCombination']['effective_date']);
			}
			asort($prev_short_list);
			$immediate_pre_date = array_pop($prev_short_list);
			/*---------- start prev data ----------*/
			$this->DistProductPrice->unbindModel(
				array('hasMany' => array('DistProductCombination'))
			);
			$previous_row_data = $this->DistProductPrice->find('all',array(
				'conditions' => array(
					'DistProductPrice.effective_date' => date('Y-m-d',strtotime($immediate_pre_date)),
					'DistProductPrice.has_combination' => 1,
					'DistProductPrice.institute_id' => 0,
					'DistProductPrice.product_id' => $combined_product_array
				),
				'joins' => array(
					array(
						'alias' => 'DistProductCombination',
						'table' => 'dist_product_combinations',
						'type' => 'INNER',
						'conditions' => 'DistProductPrice.id = DistProductCombination.product_price_id'
					)
				),
				'fields' => array(
					'DistProductPrice.*','DistProductCombination.*'
				)
			));
			/*---------- end prev data ----------*/
			
		}
		/*----- end prev date and data --------*/
		
		/*------ start next date -------*/
		$this->DistCombination->unbindModel(
			array('hasMany' => array('DistProductCombination'))
		);
		$next_combination_date = $this->DistCombination->find('all',array(
			'conditions' => array(
				'DistCombination.all_products_in_combination' => $combined_product_id,
				'DistProductCombination.effective_date >' => $deleted_effective_date
			),
			'joins' => array(
				array(
					'alias' => 'DistProductCombination',
					'table' => 'dist_product_combinations',
					'type' => 'INNER',
					'conditions' => 'DistCombination.id = DistProductCombination.combination_id'
				)
			),
			'fields' => array(
				'DistCombination.*','DistProductCombination.*'
			)
		));
		if(!empty($next_combination_date)){
			$next_short_list = array();
			foreach($next_combination_date as $next_com_key=>$next_com_val){
				array_push($next_short_list,$next_com_val['DistProductCombination']['effective_date']);
			}
			rsort($next_short_list);
			$immediate_next_date = array_pop($next_short_list);
		}
		/*------ end next date -------*/

		if ($this->DistCombination->delete()) {
			$this->DistProductPrice->deleteAll(array('id' => $delete_price_id_list), false);
			
			/*-------- start update prev by next ---------*/
			if(!empty($immediate_next_date) && !empty($previous_row_data)){
				foreach($previous_row_data as $update_prev_data_key=>$update_prev_data_val){
					$update_prev_product_price['DistProductPrice']['id'] = $update_prev_data_val['DistProductPrice']['id'];
					$update_prev_product_price['DistProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immediate_next_date)))));
					if($this->DistProductPrice->save($update_prev_product_price)){
						$update_prev_product_combination['DistProductCombination']['id'] = $update_prev_data_val['DistProductCombination']['id'];
						$update_prev_product_combination['DistProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immediate_next_date)))));
						$this->DistProductCombination->save($update_prev_product_combination);
					}
				}
			}else if(!empty($previous_row_data)){
				foreach($previous_row_data as $update_prev_data_key=>$update_prev_data_val){
					$update_prev_product_price['DistProductPrice']['id'] = $update_prev_data_val['DistProductPrice']['id'];
					$update_prev_product_price['DistProductPrice']['end_date'] = NULL;
					if($this->DistProductPrice->save($update_prev_product_price)){
						$update_prev_product_combination['DistProductCombination']['id'] = $update_prev_data_val['DistProductCombination']['id'];
						$update_prev_product_combination['DistProductCombination']['end_date'] = NULL;
						$this->DistProductCombination->save($update_prev_product_combination);
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
		$this->loadModel('DistProductPrice');
		$current_date = $this->current_date();
		$product_id = $this->request->data['product_id'];
		/*------- start get prev effective date and next effective date ---------*/
		$effective_prev_date = $this->DistProductPrice->find('all',
			array(
				'conditions' => array('DistProductPrice.product_id' => $product_id,'DistProductPrice.effective_date <=' => $current_date,'DistProductPrice.institute_id'=>0,'DistProductPrice.has_combination'=>0),
				'fields' => array('DistProductPrice.effective_date')
			)
		);
		if(!empty($effective_prev_date)){
				$prev_short_list = array();
				foreach($effective_prev_date as $prev_date_key => $prev_date_val){
					array_push($prev_short_list,$prev_date_val['DistProductPrice']['effective_date']);
				}
				asort($prev_short_list);
				$prev_date = end($prev_short_list);
		}
		$effective_next_date = $this->DistProductPrice->find('all',
			array(
				'conditions' => array('DistProductPrice.product_id' => $product_id,'DistProductPrice.effective_date >' => $current_date,'DistProductPrice.institute_id'=>0,'DistProductPrice.has_combination'=>0),
				'fields' => array('DistProductPrice.effective_date')
			)
		);
		if(!empty($effective_next_date)){
				$next_short_list = array();
				foreach($effective_next_date as $next_date_key => $next_date_val){
					array_push($next_short_list,$next_date_val['DistProductPrice']['effective_date']);
				}
				rsort($next_short_list);
				$next_date = end($next_short_list);
		}
		if(isset($next_date)){
			$condition_value['DistProductCombination.effective_date <'] = $next_date;
		}
		if(isset($prev_date)){
			$condition_value['DistProductCombination.effective_date >='] = $prev_date;
		}
		$condition_value['DistProductCombination.product_id'] = $product_id;
		$condition_value['DistProductCombination.combination_id'] = 0;
		/*------- end get prev effective date and next effective date ---------*/
		
		
		if(!empty($product_id)){
			$slab_list = $this->DistProductCombination->find('all',array(
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
			if($parent_slab_id==$val['DistProductCombination']['id']){
				$html = $html.'<option selected value="'.$val['DistProductCombination']['id'].'">'.$val['DistProductCombination']['min_qty'].' ('.$val['DistProductCombination']['effective_date'].') </option>';
			}else{
				// $html = $html.'<option value="'.$key.'">'.$val.'</option>';
				$html = $html.'<option value="'.$val['DistProductCombination']['id'].'">'.$val['DistProductCombination']['min_qty'].' ('.$val['DistProductCombination']['effective_date'].') </option>';
			}
		}
		echo $html;
		$this->autoRender = false;
	}
}
