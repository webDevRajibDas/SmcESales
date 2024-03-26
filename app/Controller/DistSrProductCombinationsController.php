<?php
App::uses('AppController', 'Controller');
/**
 * DistSrProductCombinations Controller
 *
 * @property DistSrProductCombination $DistSrProductCombination
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistSrProductCombinationsController extends AppController {

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
	$this->loadModel('DistSrCombination');
	$this->set('page_title','DistSrCombination List');
	$this->DistSrCombination->recursive = 0;
	$joins= array();
	$conditions = array();
	$product_id = '';
	if(isset($id))
	{
		$joins = array(
			array(
				'table'=>'dist_sr_product_combinations',
				'type'=>'LEFT',
				'alias'=>'PC',
				'conditions'=>array('PC.combination_id = DistSrCombination.id')
				)
			);
		$conditions = array('PC.product_id' => $id);
		$product_id = $id;
	}
	else
	{
		$joins = array(
		array(
			'table'=>'dist_sr_product_combinations',
			'type'=>'LEFT',
			'alias'=>'PC',
			'conditions'=>array('PC.combination_id = DistSrCombination.id')
			)
		);
	}
	
	$this->paginate = array(	
		'joins' => $joins,
		'conditions' => $conditions,
		'fields' => array('DISTINCT DistSrCombination.id','DistSrCombination.name','PC.effective_date','PC.min_qty'),
		'order'=>'DistSrCombination.id DESC',		
			//'group' => array('DistSrCombination.id')
			
		);
		$this->set('product_id', $product_id);
		$this->set('DistSrCombinations', $this->paginate('DistSrCombination'));
		
		/* check combine_id is existed in memo_details table */
		
		$sql="select md.product_combination_id as com_id,count(md.id) as id_num from memo_details md 
				left join dist_combinations c on c.id=md.product_combination_id 
				where md.product_combination_id is not NULL 
				group by product_combination_id";
				
        $data_sql = $this->DistSrCombination->Query($sql);
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
		if (!$this->DistSrProductCombination->exists($id)) {
			throw new NotFoundException(__('Invalid product combination'));
		}
		$options = array('conditions' => array('DistSrProductCombination.' . $this->DistSrProductCombination->primaryKey => $id));
		$this->set('DistSrProductCombination', $this->DistSrProductCombination->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add($product_id = null) {
		$this->set('page_title','Add Combination');
		$this->loadModel('DistSrCombination');
		$this->loadModel('Product');
		$this->loadModel('DistSrProductPrice');
		if(!empty($product_id)){
			$this->DistSrProductCombination->virtualFields['min_qty_with_effective_date']='CONCAT(DistSrProductCombination.min_qty,\' (\',DistSrProductCombination.effective_date,\')\')';
			$slab_list = $this->DistSrProductCombination->find('list',array(
			'conditions' => array('DistSrProductCombination.combination_id' => 0,'DistSrProductCombination.product_id' => $product_id),
			'fields'=>array('DistSrProductCombination.id','min_qty_with_effective_date'),
			));
			
		}else{
			$slab_list = '';
		}
		if ($this->request->is('post')) {
			
			$new_start_date = date('Y-m-d',strtotime($this->request->data['DistSrProductCombination']['effective_date']));

			if(!empty($this->request->data['DistSrProductCombination']['product_id'])){
				$combined_product = $this->request->data['DistSrProductCombination']['product_id'];
				asort($combined_product);
				$combined_product_id = implode(',', $combined_product);
				$this->DistSrCombination->unbindModel(
					array('hasMany' => array('DistSrProductCombination'))
				);
				$prev_combination_date = $this->DistSrCombination->find('all',array(
					'conditions' => array(
						'DistSrCombination.all_products_in_combination' => $combined_product_id,
						'DistSrProductCombination.effective_date <' => $new_start_date
					),
					'joins' => array(
						array(
							'alias' => 'DistSrProductCombination',
							'table' => 'product_combinations',
							'type' => 'INNER',
							'conditions' => 'DistSrCombination.id = DistSrProductCombination.combination_id'
						)
					),
					'fields' => array(
						'DistSrCombination.*','DistSrProductCombination.*'
					)
				));
				if(!empty($prev_combination_date)){
					$prev_short_list = array();
					foreach($prev_combination_date as $prev_com_key=>$prev_com_val){
						$prev_com_val['DistSrProductCombination']['effective_date'];
						array_push($prev_short_list,$prev_com_val['DistSrProductCombination']['effective_date']);
					}
					asort($prev_short_list);
					$immediate_pre_date = array_pop($prev_short_list);
				}
				/* ------- start next start date ---------*/
				$this->DistSrCombination->unbindModel(
					array('hasMany' => array('DistSrProductCombination'))
				);
				$next_combination_date = $this->DistSrCombination->find('all',array(
					'conditions' => array(
						'DistSrCombination.all_products_in_combination' => $combined_product_id,
						'DistSrProductCombination.effective_date >' => $new_start_date
					),
					'joins' => array(
						array(
							'alias' => 'DistSrProductCombination',
							'table' => 'product_combinations',
							'type' => 'INNER',
							'conditions' => 'DistSrCombination.id = DistSrProductCombination.combination_id'
						)
					),
					'fields' => array(
						'DistSrCombination.*','DistSrProductCombination.*'
					)
				));
				if(!empty($next_combination_date)){
					$next_short_list = array();
					foreach($next_combination_date as $next_com_key=>$next_com_val){
						array_push($next_short_list,$next_com_val['DistSrProductCombination']['effective_date']);
					}
					rsort($next_short_list);
					$immediate_next_date = array_pop($next_short_list);
				}
				/* ------- end next start date ---------*/
				/*------- start previous row data --------*/
				$this->DistSrProductPrice->unbindModel(
					array('hasMany' => array('DistSrProductCombination'))
				);
				if(!empty($immediate_pre_date)){
					$prev_row_data = $this->DistSrProductPrice->find('all',array(
						'conditions' => array(
							'DistSrProductPrice.effective_date' => $immediate_pre_date,
							'DistSrProductPrice.has_combination' => 1,
							'DistSrProductPrice.institute_id' => 0,
							'DistSrProductPrice.product_id' => $combined_product
						),
						'joins' => array(
							array(
								'alias' => 'DistSrProductCombination',
								'table' => 'product_combinations',
								'type' => 'INNER',
								'conditions' => 'DistSrProductPrice.id = DistSrProductCombination.product_price_id'
							)
						),
						'fields' => array(
							'DistSrProductPrice.*','DistSrProductCombination.*'
						)
					));
				}
/* 				echo "<pre>";
				print_r($prev_row_data);
				exit; */
				/*------- end previous row data --------*/
			}

			$all_products_in_combination = array();
			$this->request->data['DistSrCombination']['created_at'] = $this->current_datetime(); 
			$this->request->data['DistSrCombination']['updated_at'] = $this->current_datetime(); 
			$this->request->data['DistSrCombination']['created_by'] = $this->UserAuth->getUserId();
			$data['DistSrProductPrice']['created_at'] = $this->current_datetime(); 
			$data['DistSrProductPrice']['updated_at'] = $this->current_datetime(); 
			$data['DistSrProductPrice']['created_by'] = $this->UserAuth->getUserId();
			
			//pr($this->request->data);pr($data);die();
			$request_product_list = $this->request->data['DistSrProductCombination']['product_id'];
			$this->loadModel('DistSrProductCombination');
			$existing_product_list = $this->DistSrProductCombination->find('all',array(
			'conditions' => array(
				'combination_id !=' => 0,
				'effective_date'=>$new_start_date
				),
			'fields' => array('id', 'product_id'),
			'recursive' => -1
			));
			$existing = 0;
			foreach($existing_product_list as $pro_list){
				$pro_id = $pro_list['DistSrProductCombination']['product_id'];
				if (in_array($pro_id, $request_product_list)){
				  $existing = 1;
				}
			}
			
			// forcely 
			$existing = 0;
			
			if($existing==0)
			{
				$this->DistSrCombination->create();
				if ($this->DistSrCombination->save($this->request->data)){
				$recent_inserted_combination_id = $this->DistSrCombination->getInsertID();
					if(!empty($this->request->data['DistSrProductCombination']))
					{
						if(!empty($this->request->data['DistSrCombination']['redirect_product_id'])){
							$redirect_product_id = $this->request->data['DistSrCombination']['redirect_product_id'];
						}
						$data['DistSrProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['DistSrProductCombination']['effective_date']));
						$data['DistSrProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['DistSrProductCombination']['effective_date']));
						foreach($this->request->data['DistSrProductCombination']['product_id'] as $key=>$val)
						{
							$combined_slab_price = $this->DistSrProductCombination->find('first',array(
								'conditions' => array('DistSrProductCombination.id'=>$this->request->data['DistSrProductCombination']['parent_slab_id'][$key])
								
							));
							$all_products_in_combination[] = $val;
							$data['DistSrProductPrice']['product_id'] = $val;
							$data['DistSrProductPrice']['has_combination'] = 1;
							$data['DistSrProductPrice']['has_price_slot'] = 1;
							$data['DistSrProductPrice']['is_active'] = 1;
							$data['DistSrProductPrice']['institute_id'] = 0;
							$data['DistSrProductPrice']['target_custommer'] = 0;
							$this->DistSrProductPrice->create();
							$this->DistSrProductPrice->save($data);
							$product_price_id = $this->DistSrProductPrice->getLastInsertID();
							
							$data['DistSrProductCombination']['product_price_id'] = $product_price_id;
							$data['DistSrProductCombination']['combination_id'] = $this->DistSrCombination->id;
							$data['DistSrProductCombination']['parent_slab_id'] = $this->request->data['DistSrProductCombination']['parent_slab_id'][$key];
							$data['DistSrProductCombination']['product_id'] = $val;
							$data['DistSrProductCombination']['price'] = $combined_slab_price['DistSrProductCombination']['price'];
							$data['DistSrProductCombination']['created_at'] = $this->current_datetime(); 
							$data['DistSrProductCombination']['updated_at'] = $this->current_datetime(); 
							$data['DistSrProductCombination']['created_by'] = $this->UserAuth->getUserId();
						   
						   
							// Multiple Quantity Slab For a Product
							foreach($this->request->data['DistSrProductCombination']['min_qty'] as $qty_key=>$qty_val)
							{
								$data['DistSrProductCombination']['min_qty'] = $qty_val;
								$this->DistSrProductCombination->create();
								$this->DistSrProductCombination->save($data); 
							}
						}


						if(!empty($immediate_pre_date) && !empty($prev_row_data))
						{ 	
							foreach($prev_row_data as $update_pre_data_key=>$update_pre_data_val)
							{
								$update_product_price['DistSrProductPrice']['id'] = $update_pre_data_val['DistSrProductPrice']['id'];
								$update_product_price['DistSrProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($new_start_date)))));

								if($this->DistSrProductPrice->save($update_product_price))
								{
									$update_product_combination['DistSrProductCombination']['id'] = $update_pre_data_val['DistSrProductCombination']['id'];
									$update_product_combination['DistSrProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($new_start_date)))));
									$this->DistSrProductCombination->save($update_product_combination);
								}
							}
						}
					}
				//sort all_products_in_combination in ascending order. This will be used for selejt querys
				asort($all_products_in_combination);
				$all_products_in_combination = implode(',', $all_products_in_combination);
				$this->data = array();
				$this->request->data['DistSrCombination']['all_products_in_combination'] = $all_products_in_combination;
				$this->request->data['DistSrCombination']['id'] = $this->DistSrCombination->id;

				$this->DistSrCombination->save($this->request->data);
				
				/*---------- start recent inserted data ----------*/
				$this->DistSrProductPrice->unbindModel(
					array('hasMany' => array('DistSrProductCombination'))
				);
				$current_row_data = $this->DistSrProductPrice->find('all',array(
					'conditions' => array(
						'DistSrProductPrice.effective_date' => date('Y-m-d',strtotime($new_start_date)),
						'DistSrProductPrice.has_combination' => 1,
						'DistSrProductPrice.institute_id' => 0,
						'DistSrProductPrice.product_id' => $combined_product
					),
					'joins' => array(
						array(
							'alias' => 'DistSrProductCombination',
							'table' => 'product_combinations',
							'type' => 'INNER',
							'conditions' => 'DistSrProductPrice.id = DistSrProductCombination.product_price_id'
						)
					),
					'fields' => array(
						'DistSrProductPrice.*','DistSrProductCombination.*'
					)
				));


				/*---------- end recent inserted data ----------*/
				/*------ start update current row data by end date -------*/
				if(!empty($immediate_next_date) && !empty($current_row_data)){
					foreach($current_row_data as $update_curr_data_key=>$update_curr_data_val){
						$update_curr_product_price['DistSrProductPrice']['id'] = $update_curr_data_val['DistSrProductPrice']['id'];
						$update_curr_product_price['DistSrProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immediate_next_date)))));
						if($this->DistSrProductPrice->save($update_curr_product_price)){
							$update_curr_product_combination['DistSrProductCombination']['id'] = $update_curr_data_val['DistSrProductCombination']['id'];
							$update_curr_product_combination['DistSrProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immediate_next_date)))));
							$this->DistSrProductCombination->save($update_curr_product_combination);
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
		$products = $this->Product->find('list',array('conditions'=>array('is_distributor_product'=> 1),'order'=>array('Product.order' => 'ASC')));
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
		$this->loadModel('DistSrCombination');
		$this->loadModel('Product');
		$this->loadModel('DistSrProductPrice');
		
		
		/* check combine_id is existed in memo_details table */
		
		$sql="select md.product_combination_id as com_id,count(md.id) as id_num from memo_details md 
				left join dist_combinations c on c.id=md.product_combination_id 
				where md.product_combination_id is not NULL 
				group by product_combination_id";
				
        $data_sql = $this->DistSrCombination->Query($sql);
		$com_ids=array();
		foreach ($data_sql as $each_data)
		{
			$ids=$each_data[0]['com_id'];
		    $com_ids[$ids]=$each_data[0]['id_num'];
		}
		$this->set('com_ids', $com_ids);
		$this->set('edit_id', $id);
		
		
		
		if(!empty($id)){
			$for_update_product = $this->DistSrProductCombination->find('all',array(
				'fields' => array('DistSrProductCombination.*'),
				'conditions' => array('DistSrProductCombination.combination_id' => $id)
			));
		}
		
		$total_slab = array();
		foreach($for_update_product as $val){
			if(!empty($val['DistSrProductCombination']['product_id'])){
				$slab_list = $this->DistSrProductCombination->find('list',array(
				'conditions' => array('DistSrProductCombination.combination_id' => 0,'DistSrProductCombination.product_id' => $val['DistSrProductCombination']['product_id'])
				));
			}else{
				$slab_list = '';
			}
			$total_slab[$val['DistSrProductCombination']['product_id']] = $slab_list;
		}
		$this->set('total_slab',$total_slab);
		
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$request_product_list = $this->request->data['DistSrProductCombination']['product_id'];
			$this->loadModel('DistSrProductCombination');
			$existing_product_list = $this->DistSrProductCombination->find('all',array(
			'conditions' => array(
				"NOT" => array( "combination_id" => array(0, $id) )
			),
			'fields' => array('id', 'product_id'),
			'recursive' => -1
			));
			$existing = 0;
			foreach($existing_product_list as $pro_list){
				$pro_id = $pro_list['DistSrProductCombination']['product_id'];
				if (in_array($pro_id, $request_product_list)){
				  $existing = 1;
				}
			}
			
			// forcely 
			$existing = 0;
			
			if($existing==0)
			{
				$combined_product = $this->request->data['DistSrProductCombination']['product_id'];
				asort($combined_product);
				$combined_product_id = implode(',', $combined_product);
				$all_products_in_combination = array();
				/*---------- start select for updated data ------------*/
				$this->DistSrCombination->unbindModel(
					array('hasMany' => array('DistSrProductCombination'))
				);
				$select_for_updated_data = $this->DistSrCombination->find('first',array(
					'conditions' => array(
						'DistSrCombination.id' => $id
					),
					'joins' => array(
						array(
							'alias' => 'DistSrProductCombination',
							'table' => 'product_combinations',
							'type' => 'INNER',
							'conditions' => 'DistSrCombination.id = DistSrProductCombination.combination_id'
						)
					),
					'fields' => array(
						'DistSrCombination.*','DistSrProductCombination.*'
					)
				));
				$effective_date_before_update = $select_for_updated_data['DistSrProductCombination']['effective_date'];
				/*---------- end select for updated data ------------*/
				
				/*------------ start update prev date ------------*/
				$this->DistSrCombination->unbindModel(
					array('hasMany' => array('DistSrProductCombination'))
				);
				$update_prev_date_list = $this->DistSrCombination->find('all',array(
					'conditions' => array(
						'DistSrCombination.all_products_in_combination' => $combined_product_id,
						'DistSrProductCombination.effective_date <' => $effective_date_before_update
					),
					'joins' => array(
						array(
							'alias' => 'DistSrProductCombination',
							'table' => 'product_combinations',
							'type' => 'INNER',
							'conditions' => 'DistSrCombination.id = DistSrProductCombination.combination_id'
						)
					),
					'fields' => array(
						'DistSrCombination.*','DistSrProductCombination.*'
					)
				));
				if(!empty($update_prev_date_list)){
					$update_prev_short_list = array();
					foreach($update_prev_date_list as $update_prev_date_key=>$update_prev_date_val){
						array_push($update_prev_short_list,$update_prev_date_val['DistSrProductCombination']['effective_date']);
					}
					asort($update_prev_short_list);
					$update_prev_date = array_pop($update_prev_short_list);
					
					/*------------ end update prev date ------------*/
					/*------- start previous row data --------*/
					if(!empty($update_prev_date)){
						$this->DistSrProductPrice->unbindModel(
							array('hasMany' => array('DistSrProductCombination'))
						);
						$update_prev_data = $this->DistSrProductPrice->find('all',array(
							'conditions' => array(
								'DistSrProductPrice.effective_date' => $update_prev_date,
								'DistSrProductPrice.has_combination' => 1,
								'DistSrProductPrice.institute_id' => 0,
								'DistSrProductPrice.product_id' => $combined_product
							),
							'joins' => array(
								array(
									'alias' => 'DistSrProductCombination',
									'table' => 'product_combinations',
									'type' => 'INNER',
									'conditions' => 'DistSrProductPrice.id = DistSrProductCombination.product_price_id'
								)
							),
							'fields' => array(
								'DistSrProductPrice.*','DistSrProductCombination.*'
							)
						));
					}
					/*------- end previous row data --------*/
				}
	
				/*------------ start update next date ------------*/
				$this->DistSrCombination->unbindModel(
					array('hasMany' => array('DistSrProductCombination'))
				);
				$update_next_date_list = $this->DistSrCombination->find('all',array(
					'conditions' => array(
						'DistSrCombination.all_products_in_combination' => $combined_product_id,
						'DistSrProductCombination.effective_date >' => $effective_date_before_update
					),
					'joins' => array(
						array(
							'alias' => 'DistSrProductCombination',
							'table' => 'product_combinations',
							'type' => 'INNER',
							'conditions' => 'DistSrCombination.id = DistSrProductCombination.combination_id'
						)
					),
					'fields' => array(
						'DistSrCombination.*','DistSrProductCombination.*'
					)
				));
				if(!empty($update_next_date_list)){
					$update_next_short_list = array();
					foreach($update_next_date_list as $update_next_date_key=>$update_next_date_val){
						array_push($update_next_short_list,$update_next_date_val['DistSrProductCombination']['effective_date']);
					}
					rsort($update_next_short_list);
					$update_next_date = end($update_next_short_list);
					
					/*------------ end update next date ------------*/
					/*------- start Next row data --------*/
					$this->DistSrProductPrice->unbindModel(
						array('hasMany' => array('DistSrProductCombination'))
					);
					$update_next_data = $this->DistSrProductPrice->find('all',array(
						'conditions' => array(
							'DistSrProductPrice.effective_date' => $update_next_date,
							'DistSrProductPrice.has_combination' => 1,
							'DistSrProductPrice.institute_id' => 0,
							'DistSrProductPrice.product_id' => $combined_product
						),
						'joins' => array(
							array(
								'alias' => 'DistSrProductCombination',
								'table' => 'product_combinations',
								'type' => 'INNER',
								'conditions' => 'DistSrProductPrice.id = DistSrProductCombination.product_price_id'
							)
						),
						'fields' => array(
							'DistSrProductPrice.*','DistSrProductCombination.*'
						)
					));
					/*------- end Next row data --------*/
				}
				/*-------- Start delete product price and product combination---------*/
				$delete_all = 0;
				foreach($for_update_product as $price_id ){
					if(!empty($price_id['DistSrProductCombination']['product_price_id']) && $price_id['DistSrProductCombination']['product_price_id'] !=0){
						
						$this->DistSrProductPrice->id = $price_id['DistSrProductCombination']['product_price_id'];
						if (!$this->DistSrProductPrice->exists()) {
							throw new NotFoundException(__('Invalid ProductPrice'));
						}
						if ($this->DistSrProductPrice->delete()) {
							$this->DistSrProductCombination->product_price_id = $price_id['DistSrProductCombination']['product_price_id'];
							$this->DistSrProductCombination->delete();
							//$this->DistSrProductCombination->deleteAll(array('DistSrProductCombination.product_price_id'=>$price_id['DistSrProductCombination']['product_price_id']));
							$delete_all = 1;
						}else{
							$delete_all = 0;
						}
					}
				}
				/*-------- End delete product price and product combination---------*/
				if($delete_all==1){
					$this->DistSrCombination->id = $id;
					$this->request->data['DistSrCombination']['updated_at'] = $this->current_datetime(); ;
					$this->request->data['DistSrCombination']['updated_by'] = $this->UserAuth->getUserId();
					if ($this->DistSrCombination->save($this->request->data)) {
							// for add product
							if(isset($this->request->data['DistSrProductCombination']['product_id'])){
								$data['DistSrProductPrice']['effective_date'] = date('Y-m-d',strtotime($this->request->data['DistSrProductCombination']['effective_date']));
								$insert_data['DistSrProductCombination']['effective_date'] = date('Y-m-d',strtotime($this->request->data['DistSrProductCombination']['effective_date']));
								$insert_data_array = array();
								foreach($this->request->data['DistSrProductCombination']['product_id'] as $ikey=>$ival)
								{	
									$combined_slab_price = $this->DistSrProductCombination->find('first',array(
										'conditions' => array('DistSrProductCombination.id'=>$this->request->data['DistSrProductCombination']['parent_slab_id'][$ikey])
										
									));
									$all_products_in_combination[] = $ival;
									// Product Price Table Entry
									$data['DistSrProductPrice']['product_id'] = $ival;
									$data['DistSrProductPrice']['has_combination'] = 1;
									$data['DistSrProductPrice']['has_price_slot'] = 1;
									$data['DistSrProductPrice']['is_active'] = 1;
									$data['DistSrProductPrice']['institute_id'] = 0;
									$data['DistSrProductPrice']['target_custommer'] = 0;
									$data['DistSrProductPrice']['created_at'] = $this->current_datetime(); ;
									$data['DistSrProductPrice']['updated_at'] = $this->current_datetime(); ;
									$data['DistSrProductPrice']['created_by'] = $this->UserAuth->getUserId();
									$data['DistSrProductPrice']['updated_by'] = $this->UserAuth->getUserId();
									$this->DistSrProductPrice->create();
									$this->DistSrProductPrice->save($data);
									$product_price_id = $this->DistSrProductPrice->getLastInsertID();
								
								
									$insert_data['DistSrProductCombination']['combination_id'] = $this->DistSrCombination->id;
									$insert_data['DistSrProductCombination']['product_price_id'] = $product_price_id;
									$insert_data['DistSrProductCombination']['product_id'] = $ival;
									$insert_data['DistSrProductCombination']['parent_slab_id'] = $this->request->data['DistSrProductCombination']['parent_slab_id'][$ikey];
									$insert_data['DistSrProductCombination']['price'] = $combined_slab_price['DistSrProductCombination']['price'];
									$insert_data['DistSrProductCombination']['min_qty'] = $this->request->data['DistSrProductCombination']['min_qty'];
									$insert_data['DistSrProductCombination']['created_at'] = $this->current_datetime();
									$insert_data['DistSrProductCombination']['updated_at'] = $this->current_datetime();
									$insert_data['DistSrProductCombination']['created_by'] = $this->UserAuth->getUserId();
									$insert_data['DistSrProductCombination']['updated_by'] = $this->UserAuth->getUserId();
									$insert_data_array[] = $insert_data;
								}				
								$this->DistSrProductCombination->saveAll($insert_data_array);
								/*-------------- start update prev data by end date -------------*/
								if(!empty($update_next_date) && !empty($update_prev_date)){
									foreach($update_prev_data as $update_prev_key=>$update_prev_val){
										$update_product_price['DistSrProductPrice']['id'] = $update_prev_val['DistSrProductPrice']['id'];
										$update_product_price['DistSrProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($update_next_date)))));
										if($this->DistSrProductPrice->save($update_product_price)){
											$update_product_combination['DistSrProductCombination']['id'] = $update_prev_val['DistSrProductCombination']['id'];
											$update_product_combination['DistSrProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($update_next_date)))));
											$this->DistSrProductCombination->save($update_product_combination);
										}
									}
								}
								if(empty($update_next_date) && !empty($update_prev_date)){
									foreach($update_prev_data as $update_prev_key=>$update_prev_val){
										$update_product_price['DistSrProductPrice']['id'] = $update_prev_val['DistSrProductPrice']['id'];
										$update_product_price['DistSrProductPrice']['end_date'] = NULL;
										if($this->DistSrProductPrice->save($update_product_price)){
											$update_product_combination['DistSrProductCombination']['id'] = $update_prev_val['DistSrProductCombination']['id'];
											$update_product_combination['DistSrProductCombination']['end_date'] = NULL;
											$this->DistSrProductCombination->save($update_product_combination);
										}
									}
								}
								/*-------------- end update prev data by end date ---------------*/
								/*-------------- start updated info -------------- */
								$this->DistSrCombination->unbindModel(
									array('hasMany' => array('DistSrProductCombination'))
								);
								$updated_row_info = $this->DistSrCombination->find('first',array(
									'conditions' => array(
										'DistSrCombination.id' => $id
									),
									'joins' => array(
										array(
											'alias' => 'DistSrProductCombination',
											'table' => 'product_combinations',
											'type' => 'INNER',
											'conditions' => 'DistSrCombination.id = DistSrProductCombination.combination_id'
										)
									),
									'fields' => array(
										'DistSrCombination.*','DistSrProductCombination.*'
									)
								));
								$updated_effective_date = $updated_row_info['DistSrProductCombination']['effective_date'];
								/*-------------- end updated info -------------- */
								
								/*---------- start updated prev date-------*/
								$this->DistSrCombination->unbindModel(
									array('hasMany' => array('DistSrProductCombination'))
								);
								$updated_prev_date_list = $this->DistSrCombination->find('all',array(
									'conditions' => array(
										'DistSrCombination.all_products_in_combination' => $combined_product_id,
										'DistSrProductCombination.effective_date <' => $updated_effective_date
									),
									'joins' => array(
										array(
											'alias' => 'DistSrProductCombination',
											'table' => 'product_combinations',
											'type' => 'INNER',
											'conditions' => 'DistSrCombination.id = DistSrProductCombination.combination_id'
										)
									),
									'fields' => array(
										'DistSrCombination.*','DistSrProductCombination.*'
									)
								));
								if(!empty($updated_prev_date_list)){
									$updated_prev_short_list = array();
									foreach($updated_prev_date_list as $updated_prev_date_key=>$updated_prev_date_val){
										array_push($updated_prev_short_list,$updated_prev_date_val['DistSrProductCombination']['effective_date']);
									}
									asort($updated_prev_short_list);
									$updated_prev_date = array_pop($updated_prev_short_list);
									/*------------ end updated prev date ------------*/
									/*------- start updated previous row data --------*/
									$this->DistSrProductPrice->unbindModel(
										array('hasMany' => array('DistSrProductCombination'))
									);
									$updated_prev_data = $this->DistSrProductPrice->find('all',array(
										'conditions' => array(
											'DistSrProductPrice.effective_date' => $updated_prev_date,
											'DistSrProductPrice.has_combination' => 1,
											'DistSrProductPrice.institute_id' => 0,
											'DistSrProductPrice.product_id' => $combined_product
										),
										'joins' => array(
											array(
												'alias' => 'DistSrProductCombination',
												'table' => 'product_combinations',
												'type' => 'INNER',
												'conditions' => 'DistSrProductPrice.id = DistSrProductCombination.product_price_id'
											)
										),
										'fields' => array(
											'DistSrProductPrice.*','DistSrProductCombination.*'
										)
									));
									/*------- end updated previous row data --------*/
								}
								
	
								/*------------ start updated next date ------------*/
								$this->DistSrCombination->unbindModel(
									array('hasMany' => array('DistSrProductCombination'))
								);
								$updated_next_date_list = $this->DistSrCombination->find('all',array(
									'conditions' => array(
										'DistSrCombination.all_products_in_combination' => $combined_product_id,
										'DistSrProductCombination.effective_date >' => $updated_effective_date
									),
									'joins' => array(
										array(
											'alias' => 'DistSrProductCombination',
											'table' => 'product_combinations',
											'type' => 'INNER',
											'conditions' => 'DistSrCombination.id = DistSrProductCombination.combination_id'
										)
									),
									'fields' => array(
										'DistSrCombination.*','DistSrProductCombination.*'
									)
								));
								if(!empty($updated_next_date_list)){
									$updated_next_short_list = array();
									foreach($updated_next_date_list as $updated_next_date_key=>$updated_next_date_val){
										array_push($updated_next_short_list,$updated_next_date_val['DistSrProductCombination']['effective_date']);
									}
									rsort($updated_next_short_list);
									$updated_next_date = end($updated_next_short_list);
								}
								/*------------ end updated next date ------------*/
								
								/*------------- start update updated prev and next row ------------*/
								if(!empty($updated_prev_date)){
									foreach($updated_prev_data as $updated_prev_key=>$updated_prev_val){
										$updated_product_price['DistSrProductPrice']['id'] = $updated_prev_val['DistSrProductPrice']['id'];
										$updated_product_price['DistSrProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_effective_date)))));
										if($this->DistSrProductPrice->save($updated_product_price)){
											$updated_product_combination['DistSrProductCombination']['id'] = $updated_prev_val['DistSrProductCombination']['id'];
											$updated_product_combination['DistSrProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_effective_date)))));
											$this->DistSrProductCombination->save($updated_product_combination);
										}
									}
								}
								/*---------- start updated effective data -----------*/
								$this->DistSrProductPrice->unbindModel(
									array('hasMany' => array('DistSrProductCombination'))
								);
								$updated_effective_data = $this->DistSrProductPrice->find('all',array(
									'conditions' => array(
										'DistSrProductPrice.effective_date' => $updated_effective_date,
										'DistSrProductPrice.has_combination' => 1,
										'DistSrProductPrice.institute_id' => 0,
										'DistSrProductPrice.product_id' => $combined_product
									),
									'joins' => array(
										array(
											'alias' => 'DistSrProductCombination',
											'table' => 'product_combinations',
											'type' => 'INNER',
											'conditions' => 'DistSrProductPrice.id = DistSrProductCombination.product_price_id'
										)
									),
									'fields' => array(
										'DistSrProductPrice.*','DistSrProductCombination.*'
									)
								));
								/*---------- end updated effective data -----------*/
								if(!empty($updated_next_date)){
									foreach($updated_effective_data as $updated_effec_key=>$updated_effec_val){
										$updated_effec_price['DistSrProductPrice']['id'] = $updated_effec_val['DistSrProductPrice']['id'];
										$updated_effec_price['DistSrProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_next_date)))));
										if($this->DistSrProductPrice->save($updated_effec_price)){
											$updated_effec_product_combination['DistSrProductCombination']['id'] = $updated_effec_val['DistSrProductCombination']['id'];
											$updated_effec_product_combination['DistSrProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($updated_next_date)))));
											$this->DistSrProductCombination->save($updated_effec_product_combination);
										}
									}
								}else{
									foreach($updated_effective_data as $updated_effec_key=>$updated_effec_val){
										$updated_effec_price['DistSrProductPrice']['id'] = $updated_effec_val['DistSrProductPrice']['id'];
										$updated_effec_price['DistSrProductPrice']['end_date'] = NULL;
										if($this->DistSrProductPrice->save($updated_effec_price)){
											$updated_effec_product_combination['DistSrProductCombination']['id'] = $updated_effec_val['DistSrProductCombination']['id'];
											$updated_effec_product_combination['DistSrProductCombination']['end_date'] = NULL;
											$this->DistSrProductCombination->save($updated_effec_product_combination);
										}
									}
								}
								/*------------- end update updated prev and next row ------------*/
								
								
								
								
							}					
						
	
						//sort all_products_in_combination in ascending order. This will be used for selejt querys
						asort($all_products_in_combination);
						$all_products_in_combination = implode(',', $all_products_in_combination);
						$this->data = array();
						$this->request->data['DistSrCombination']['all_products_in_combination'] = $all_products_in_combination;
						$this->request->data['DistSrCombination']['id'] = $this->DistSrCombination->id;
						$this->DistSrCombination->save($this->request->data);
						
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
		
		$options = array('conditions' => array('DistSrCombination.' . $this->DistSrCombination->primaryKey => $id));
		$this->request->data = $this->DistSrCombination->find('first', $options);
		
		//pr($this->request->data);	
		
		//$products_list = $this->Product->find('list');
		
		$products_list = $this->Product->find('list',array('conditions'=>array('is_distributor_product'=> 1),'order'=>array('Product.order' => 'ASC')));
		
		
		
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
		$this->loadModel('DistSrCombination');
		$this->loadModel('DistSrProductCombination');
		$this->loadModel('DistSrProductPrice');
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->DistSrCombination->id = $id;
		if (!$this->DistSrCombination->exists()) {
			throw new NotFoundException(__('Invalid Combination'));
		}
		/*-------- start deleted row info ---------*/
		if(!empty($id)){
			$deleted_data = $this->DistSrCombination->find('first',array(
				'conditions' => array('DistSrCombination.id' => $id)
			));
			$combined_product_id = $deleted_data['DistSrCombination']['all_products_in_combination'];
			$combined_product_array = explode(',',$combined_product_id);
			$deleted_effective_date = $deleted_data['DistSrProductCombination'][0]['effective_date'];
			if(!empty($deleted_data)){
			$delete_price_id_list = array();
				foreach($deleted_data['DistSrProductCombination'] as $p_price_key=>$p_price_val){
					array_push($delete_price_id_list,$p_price_val['product_price_id']);
				}
			}
		}
		/*-------- end deleted row info ---------*/
		
		/*----- start prev date and data --------*/
		$this->DistSrCombination->unbindModel(
			array('hasMany' => array('DistSrProductCombination'))
		);
		$prev_combination_date = $this->DistSrCombination->find('all',array(
			'conditions' => array(
				'DistSrCombination.all_products_in_combination' => $combined_product_id,
				'DistSrProductCombination.effective_date <' => $deleted_effective_date
			),
			'joins' => array(
				array(
					'alias' => 'DistSrProductCombination',
					'table' => 'product_combinations',
					'type' => 'INNER',
					'conditions' => 'DistSrCombination.id = DistSrProductCombination.combination_id'
				)
			),
			'fields' => array(
				'DistSrCombination.*','DistSrProductCombination.*'
			)
		));
		if(!empty($prev_combination_date)){
			$prev_short_list = array();
			foreach($prev_combination_date as $prev_com_key=>$prev_com_val){
				$prev_com_val['DistSrProductCombination']['effective_date'];
				array_push($prev_short_list,$prev_com_val['DistSrProductCombination']['effective_date']);
			}
			asort($prev_short_list);
			$immediate_pre_date = array_pop($prev_short_list);
			/*---------- start prev data ----------*/
			$this->DistSrProductPrice->unbindModel(
				array('hasMany' => array('DistSrProductCombination'))
			);
			$previous_row_data = $this->DistSrProductPrice->find('all',array(
				'conditions' => array(
					'DistSrProductPrice.effective_date' => date('Y-m-d',strtotime($immediate_pre_date)),
					'DistSrProductPrice.has_combination' => 1,
					'DistSrProductPrice.institute_id' => 0,
					'DistSrProductPrice.product_id' => $combined_product_array
				),
				'joins' => array(
					array(
						'alias' => 'DistSrProductCombination',
						'table' => 'product_combinations',
						'type' => 'INNER',
						'conditions' => 'DistSrProductPrice.id = DistSrProductCombination.product_price_id'
					)
				),
				'fields' => array(
					'DistSrProductPrice.*','DistSrProductCombination.*'
				)
			));
			/*---------- end prev data ----------*/
			
		}
		/*----- end prev date and data --------*/
		
		/*------ start next date -------*/
		$this->DistSrCombination->unbindModel(
			array('hasMany' => array('DistSrProductCombination'))
		);
		$next_combination_date = $this->DistSrCombination->find('all',array(
			'conditions' => array(
				'DistSrCombination.all_products_in_combination' => $combined_product_id,
				'DistSrProductCombination.effective_date >' => $deleted_effective_date
			),
			'joins' => array(
				array(
					'alias' => 'DistSrProductCombination',
					'table' => 'product_combinations',
					'type' => 'INNER',
					'conditions' => 'DistSrCombination.id = DistSrProductCombination.combination_id'
				)
			),
			'fields' => array(
				'DistSrCombination.*','DistSrProductCombination.*'
			)
		));
		if(!empty($next_combination_date)){
			$next_short_list = array();
			foreach($next_combination_date as $next_com_key=>$next_com_val){
				array_push($next_short_list,$next_com_val['DistSrProductCombination']['effective_date']);
			}
			rsort($next_short_list);
			$immediate_next_date = array_pop($next_short_list);
		}
		/*------ end next date -------*/

		if ($this->DistSrCombination->delete()) {
			$this->DistSrProductPrice->deleteAll(array('id' => $delete_price_id_list), false);
			
			/*-------- start update prev by next ---------*/
			if(!empty($immediate_next_date) && !empty($previous_row_data)){
				foreach($previous_row_data as $update_prev_data_key=>$update_prev_data_val){
					$update_prev_product_price['DistSrProductPrice']['id'] = $update_prev_data_val['DistSrProductPrice']['id'];
					$update_prev_product_price['DistSrProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immediate_next_date)))));
					if($this->DistSrProductPrice->save($update_prev_product_price)){
						$update_prev_product_combination['DistSrProductCombination']['id'] = $update_prev_data_val['DistSrProductCombination']['id'];
						$update_prev_product_combination['DistSrProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d',strtotime($immediate_next_date)))));
						$this->DistSrProductCombination->save($update_prev_product_combination);
					}
				}
			}else if(!empty($previous_row_data)){
				foreach($previous_row_data as $update_prev_data_key=>$update_prev_data_val){
					$update_prev_product_price['DistSrProductPrice']['id'] = $update_prev_data_val['DistSrProductPrice']['id'];
					$update_prev_product_price['DistSrProductPrice']['end_date'] = NULL;
					if($this->DistSrProductPrice->save($update_prev_product_price)){
						$update_prev_product_combination['DistSrProductCombination']['id'] = $update_prev_data_val['DistSrProductCombination']['id'];
						$update_prev_product_combination['DistSrProductCombination']['end_date'] = NULL;
						$this->DistSrProductCombination->save($update_prev_product_combination);
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
			'conditions' => array(
				'Product.product_category_id' => $product_category_id,
				'Product.is_distributor_product'=> 1
			),
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
		$this->loadModel('DistSrProductPrice');
		$current_date = $this->current_date();
		$product_id = $this->request->data['product_id'];
		/*------- start get prev effective date and next effective date ---------*/
		$effective_prev_date = $this->DistSrProductPrice->find('all',
			array(
				'conditions' => array('DistSrProductPrice.product_id' => $product_id,'DistSrProductPrice.effective_date <=' => $current_date,'DistSrProductPrice.institute_id'=>0,'DistSrProductPrice.has_combination'=>0),
				'fields' => array('DistSrProductPrice.effective_date')
			)
		);
		if(!empty($effective_prev_date)){
				$prev_short_list = array();
				foreach($effective_prev_date as $prev_date_key => $prev_date_val){
					array_push($prev_short_list,$prev_date_val['DistSrProductPrice']['effective_date']);
				}
				asort($prev_short_list);
				$prev_date = end($prev_short_list);
		}
		$effective_next_date = $this->DistSrProductPrice->find('all',
			array(
				'conditions' => array('DistSrProductPrice.product_id' => $product_id,'DistSrProductPrice.effective_date >' => $current_date,'DistSrProductPrice.institute_id'=>0,'DistSrProductPrice.has_combination'=>0),
				'fields' => array('DistSrProductPrice.effective_date')
			)
		);
		if(!empty($effective_next_date)){
				$next_short_list = array();
				foreach($effective_next_date as $next_date_key => $next_date_val){
					array_push($next_short_list,$next_date_val['DistSrProductPrice']['effective_date']);
				}
				rsort($next_short_list);
				$next_date = end($next_short_list);
		}
		if(isset($next_date)){
			$condition_value['DistSrProductCombination.effective_date <'] = $next_date;
		}
		if(isset($prev_date)){
			$condition_value['DistSrProductCombination.effective_date >='] = $prev_date;
		}
		$condition_value['DistSrProductCombination.product_id'] = $product_id;
		$condition_value['DistSrProductCombination.combination_id'] = 0;
		/*------- end get prev effective date and next effective date ---------*/
		
		
		if(!empty($product_id)){
			$slab_list = $this->DistSrProductCombination->find('all',array(
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
			if($parent_slab_id==$val['DistSrProductCombination']['id']){
				$html = $html.'<option selected value="'.$val['DistSrProductCombination']['id'].'">'.$val['DistSrProductCombination']['min_qty'].' ('.$val['DistSrProductCombination']['effective_date'].') </option>';
			}else{
				// $html = $html.'<option value="'.$key.'">'.$val.'</option>';
				$html = $html.'<option value="'.$val['DistSrProductCombination']['id'].'">'.$val['DistSrProductCombination']['min_qty'].' ('.$val['DistSrProductCombination']['effective_date'].') </option>';
			}
		}
		echo $html;
		$this->autoRender = false;
	}
}
