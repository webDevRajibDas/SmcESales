<?php
App::uses('AppController', 'Controller');
/**
 * OpenCombinations Controller
 *
 * @property OpenCombination $OpenCombination
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class OpenCombinationsController extends AppController {

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
	public function admin_index($id = null) {
		
		$this->set('page_title','Open Combination List');
		
		$product_id = '';
		
		if(isset($id))
		{
			$product_id = $id;
			
			$joins = array(
				array(
					'table'=>'open_combination_products',
					'type'=>'LEFT',
					'alias'=>'OC',
					'conditions'=>array('OC.combination_id = OpenCombination.id')
				)
			);
			$conditions = array('OpenCombination.is_bonus' => 0, 'OC.product_id' => $product_id);
		}
		else
		{
			$joins = array();
			$conditions = array('OpenCombination.is_bonus' => 0);
		}
		
				
		$this->paginate = array(
			'joins' => $joins,
			'conditions' => $conditions,
			'limit' => 20,
			'order' => array('OpenCombination.id' => 'desc'),
			'recursive' => -1
		);
		$this->set('product_id', $product_id);	
		$this->set('results', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) 
	{
		$this->set('page_title','Product OpenCombination Details');
		if (!$this->OpenCombination->exists($id)) {
			throw new NotFoundException(__('Invalid product combination'));
		}
		$options = array('conditions' => array('OpenCombination.' . $this->OpenCombination->primaryKey => $id));
		$this->set('productCombination', $this->OpenCombination->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add($product_id = null) 
	{
		$this->set('page_title','Add OpenCombination');
		$this->loadModel('Product');
		$this->loadModel('OpenCombinationProduct');

		if ($this->request->is('post')) 
		{
			//pr($this->request->data);
			
			if($this->request->data['OpenCombination']['product_id'])
			{
				$all_products_in_combination = array();
				$this->request->data['OpenCombination']['start_date'] = date('Y-m-d', strtotime($this->request->data['OpenCombination']['start_date']));
				$this->request->data['OpenCombination']['end_date'] = date('Y-m-d', strtotime($this->request->data['OpenCombination']['end_date']));
				
				$this->request->data['OpenCombination']['created_at'] = $this->current_datetime(); 
				$this->request->data['OpenCombination']['updated_at'] = $this->current_datetime(); 
				$this->request->data['OpenCombination']['created_by'] = $this->UserAuth->getUserId();
				$this->request->data['OpenCombination']['updated_by'] = $this->UserAuth->getUserId();
				
				
				$request_product_list = $this->request->data['OpenCombination']['product_id'];
				
				
				$this->loadModel('OpenCombinationProduct');
				$joins = array(
					array(
						'table'=>'open_combination_products',
						'type'=>'LEFT',
						'alias'=>'OpenCombinationProduct',
						'conditions'=>array('OpenCombinationProduct.combination_id = OpenCombination.id')
					)
				);
				$conditions = array('OpenCombination.is_bonus' => 0);
				
				$existing_product_list = $this->OpenCombination->find('all',
					array(
						'joins' => $joins,
						'conditions' => $conditions,
						'fields' => array('OpenCombinationProduct.id', 'OpenCombinationProduct.product_id'),
						'recursive' => -1
					)
				);
				
				
				$existing = 0;
				foreach($existing_product_list as $pro_list)
				{
					//pr($pro_list);
					$pro_id = $pro_list['OpenCombinationProduct']['product_id'];
					if (in_array($pro_id, $request_product_list)){
					  $existing = 1;
					}
				}
				
				//echo $existing;
				//exit;
				
				if($existing==0)
				{
					$this->OpenCombination->create();
					
					if ($this->OpenCombination->save($this->request->data))
					{
						$recent_inserted_combination_id = $this->OpenCombination->getInsertID();
						
						if(!empty($this->request->data['OpenCombination']))
						{
							if(!empty($this->request->data['OpenCombination']['redirect_product_id'])){
								$redirect_product_id = $this->request->data['OpenCombination']['redirect_product_id'];
							}

							foreach($this->request->data['OpenCombination']['product_id'] as $key=>$val)
							{
								$data['OpenCombinationProduct']['product_id'] = $val;
								$data['OpenCombinationProduct']['combination_id'] = $recent_inserted_combination_id;
								$this->OpenCombinationProduct->create();
								$this->OpenCombinationProduct->save($data);
							}								
							
						}
					
					
					$this->Session->setFlash(__('The combination has been saved!'), 'flash/success');
					
					if(!empty($redirect_product_id)){
						$this->redirect(array('action' => 'index/'.$redirect_product_id));
					}else{
						$this->redirect(array('action' => 'index'));
					}
					
				  }
				}
				else
				{
					$this->Session->setFlash(__("Product doesn't add many times!"), 'flash/warning');
					$this->redirect(array('action' => 'add'));
				}
			}
			else
			{
				$this->Session->setFlash(__("Please select products!."), 'flash/error');
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
		$this->set(compact('product_list', 'products', 'product_id'));
		
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null, $product_id = '') 
	{
		$this->set('page_title','Edit OpenCombination');
		$this->loadModel('OpenCombination');
		$this->loadModel('Product');
		$this->loadModel('OpenCombinationProduct');
		
		$this->set('product_id', $product_id);	
		
		if(!empty($id)){
			$for_update_product = $this->OpenCombination->find('all',array(
				'fields' => array('OpenCombination.*'),
				'conditions' => array('OpenCombination.id' => $id)
			));
		}		
		
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->request->data['OpenCombination']['product_id'])
			{
				$all_products_in_combination = array();
				$this->request->data['OpenCombination']['start_date'] = date('Y-m-d', strtotime($this->request->data['OpenCombination']['start_date']));
				$this->request->data['OpenCombination']['end_date'] = date('Y-m-d', strtotime($this->request->data['OpenCombination']['end_date']));
				
				$this->request->data['OpenCombination']['updated_at'] = $this->current_datetime(); 
				$this->request->data['OpenCombination']['updated_by'] = $this->UserAuth->getUserId();
				
				$request_product_list = $this->request->data['OpenCombination']['product_id'];
				
				$this->loadModel('OpenCombinationProduct');
				/*$existing_product_list = $this->OpenCombinationProduct->find('all',array(
				'conditions' => array(
					"NOT" => array( "combination_id" => array(0, $id) )
				),
				'fields' => array('id', 'product_id'),
				'recursive' => -1
				));*/
				
				$joins = array(
					array(
						'table'=>'open_combination_products',
						'type'=>'LEFT',
						'alias'=>'OpenCombinationProduct',
						'conditions'=>array('OpenCombinationProduct.combination_id = OpenCombination.id')
					)
				);
				$conditions = array(
					"NOT" => array( "combination_id" => array($id)),
					'OpenCombination.is_bonus' => 0
				);
				$existing_product_list = $this->OpenCombination->find('all',
					array(
						'joins' => $joins,
						'conditions' => $conditions,
						'fields' => array('OpenCombinationProduct.id', 'OpenCombinationProduct.product_id'),
						'recursive' => -1
					)
				);
				
				$existing = 0;
				foreach($existing_product_list as $pro_list)
				{
					//pr($pro_list);
					$pro_id = $pro_list['OpenCombinationProduct']['product_id'];
					if (in_array($pro_id, $request_product_list)){
					  $existing = 1;
					}
				}
				
				//echo $existing;
				//exit;
				
				if($existing==0)
				{
					$delete_all = 0;
					$this->OpenCombinationProduct->combination_id = $id;
					if($this->OpenCombinationProduct->deleteAll(array('combination_id'=>$id))){
						$delete_all = 1;
					}
					
					if($delete_all==1)
					{
						$this->OpenCombination->id = $id;
						
						if ($this->OpenCombination->save($this->request->data))
						{							
							if(!empty($this->request->data['OpenCombination']))
							{
								if(!empty($this->request->data['OpenCombination']['redirect_product_id'])){
									$redirect_product_id = $this->request->data['OpenCombination']['redirect_product_id'];
								}
	
								foreach($this->request->data['OpenCombination']['product_id'] as $key=>$val)
								{
									$data['OpenCombinationProduct']['product_id'] = $val;
									$data['OpenCombinationProduct']['combination_id'] = $id;
									$this->OpenCombinationProduct->create();
									$this->OpenCombinationProduct->save($data);
								}								
								
							}
						
						$this->Session->setFlash(__('The combination has been saved!'), 'flash/success');
						
						if(!empty($redirect_product_id)){
							$this->redirect(array('action' => 'index/'.$redirect_product_id));
						}else{
							$this->redirect(array('action' => 'index'));
						}
						
					  }
					}
				}
				else
				{
					$this->Session->setFlash(__("Product doesn't add many times!"), 'flash/warning');
					$this->redirect(array('action' => 'edit/'.$id));
				}
			}
			else
			{
				$this->Session->setFlash(__("Please select products!."), 'flash/error');
				$this->redirect(array('action' => 'edit/'.$id));
			}
		}
		
		$options = array('conditions' => array('OpenCombination.' . $this->OpenCombination->primaryKey => $id));
		$this->request->data = $this->OpenCombination->find('first', $options);
		
		
		
		$this->request->data['OpenCombination']['start_date'] = date('d-m-Y', strtotime($this->request->data['OpenCombination']['start_date']));
		$this->request->data['OpenCombination']['end_date'] = date('d-m-Y', strtotime($this->request->data['OpenCombination']['end_date']));
		
		//pr($this->request->data);	
		//$products_list = $this->Product->find('list');
		
		$products = $this->Product->find('list', array('order'=>array('Product.order' => 'ASC')));
		$this->html = '';
		foreach($products as $key=>$val)
		{
			$this->html .= '<option value="'.$key.'">'.addslashes($val).'</option>';		
		}
		$product_list = $this->html;
		
		$this->set(compact('slab_list', 'product_list', 'products'));
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null, $product_id = null) 
	{
		$this->loadModel('OpenCombination');
		$this->loadModel('OpenCombinationProduct');

		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
				
		$this->OpenCombination->id = $id;
		if (!$this->OpenCombination->exists()) {
			throw new NotFoundException(__('Invalid OpenCombination'));
		}
					
		if ($this->OpenCombination->delete()) 
		{
			$combination_id = $id;
			$this->OpenCombinationProduct->deleteAll(array('combination_id' => $combination_id));
					
			$this->Session->setFlash(__('Combination deleted!'), 'flash/success');
			$this->redirect(array('action' => 'index/'.$product_id));
		}
		else
		{
			$this->Session->setFlash(__('Combination was not deleted!'), 'flash/error');
			$this->redirect(array('action' => 'index/'.$product_id));
		}
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
	
}
