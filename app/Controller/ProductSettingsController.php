<?php
App::uses('AppController', 'Controller');
/**
 * ProductSettings Controller
 *
 * @property ProductSetting $ProductSetting
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ProductSettingsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session','Filter.Filter');
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index($id = null) {
		
		if ($this->request->is('post') || $this->request->is('put')) {
			// for update data
			$data_array = array();
			foreach($this->request->data['product_setting_id'] as $key=>$val)
			{
				$update_data['ProductSetting']['id'] = $val;
				$update_data['ProductSetting']['sort'] = $this->request->data['sort'][$key];
				$data_array[] = $update_data;
			}	
			
			/*pr($data_array);
			exit;*/
										
			//$this->Product->saveAll($data_array); 	
			if($this->ProductSetting->saveMany($data_array)){			
				$this->Session->setFlash(__('The product setting has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));		
			}else{
				$this->Session->setFlash(__('Sort must be unique.'), 'flash/warning');
			}
		}
		
		$this->set('page_title','Product Setting List');
		$conditions = array();
		$this->paginate = array(	
			//'fields' => array('DISTINCT Combination.*'),		
			//'joins' => $joins,
			//'conditions' => $conditions,
			'limit' => 100,
			'order'=>   array('sort' => 'asc')   
		);
		//pr($this->paginate());
		//$this->set('product_id', $product_id);
		$this->set('productSettings', $this->paginate());
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
		if (!$this->ProductSetting->exists($id)) {
			throw new NotFoundException(__('Invalid product combination'));
		}
		$options = array('conditions' => array('ProductSetting.' . $this->ProductSetting->primaryKey => $id));
		$this->set('productCombination', $this->ProductSetting->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add($id = null) {
		$this->set('page_title','Add Product Setting');
		
		$this->loadModel('Brand');

		$this->loadModel('Product');

		$brands = $this->Brand->find('list', array('order'=>array('Brand.name' => 'ASC')));

		//$products = $this->Product->find('list', array('order'=>array('Product.order' => 'ASC')));
		
		if ($this->request->is('post')) {

				if( empty($this->request->data['ProductSetting']['product_id']) ){
					$this->request->data['ProductSetting']['product_id'] = 0;
				}
			
				if ($this->ProductSetting->save($this->request->data)) {
					$this->Session->setFlash(__('The setting has been saved'), 'flash/success');
					$this->redirect(array('action' => 'index'));
					exit;
				}
			
			
		}
		
		
		$this->set(compact('id', 'brands'));
		
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
        $this->ProductSetting->id = $id;
		if (!$this->ProductSetting->exists($id)) {
			throw new NotFoundException(__('Invalid Setting'));
		}

	  
	   
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if( empty($this->request->data['ProductSetting']['product_id']) ){
				$this->request->data['ProductSetting']['product_id'] = 0;
			}
			
			if ($this->ProductSetting->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The setting has been update'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} 
			else 
			{
				$this->Session->setFlash(__('The setting could not be update. Please, try again.'), 'flash/error');
			}
			
		} 
		else 
		{
			$options = array('conditions' => array('ProductSetting.' . $this->ProductSetting->primaryKey => $id));
			$this->request->data = $this->ProductSetting->find('first', $options);
		}

		$this->loadModel('Brand');

		$brands = $this->Brand->find('list', array('order'=>array('Brand.name' => 'ASC')));

		$this->loadModel('Product');

		$con = array();

		if($this->request->data['ProductSetting']['brand_id'] > 0){
			$con=array(
				'Product.brand_id'=>$this->request->data['ProductSetting']['brand_id']
			);
		}
		
		$products = $this->Product->find('list', array(
			'conditions'=>$con,
			'order'=>array('Product.order' => 'ASC')
		));
				
		$this->set(compact('products', 'brands'));
		
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
		$this->ProductSetting->id = $id;
		if (!$this->ProductSetting->exists()) {
			throw new NotFoundException(__('Invalid Setting'));
		}
		
		if ($this->ProductSetting->delete())
		{
			$this->Session->setFlash(__('Deleted successfully!'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('List was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}


	public function get_brand_wise_product_list(){

		$brand_id = $this->request->data['brand_id'];

		$rs = array(array('id' => '', 'name' => '---- Select Product -----'));

		$this->loadModel('Product');

		$product_lists = $this->Product->find('list', array(
			'conditions'=>array(
				'Product.brand_id'=>$brand_id,
				'Product.is_active'=>1
			),
			'order'=>array('Product.order' => 'ASC')
		));

	 	foreach ($product_lists as $key => $name) {
			
			$products[] = array(
				'id' => $key,
				'name' => $name
			);
		} 

		if (!empty($products)) {
			echo json_encode(array_merge($rs, $products));
		} else {
			echo json_encode($rs);
		}

		$this->autoRender = false;

	
	}
	
	
	
}
