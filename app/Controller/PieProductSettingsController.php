<?php
App::uses('AppController', 'Controller');
/**
 * ProductSettings Controller
 *
 * @property PieProductSetting $PieProductSetting
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class PieProductSettingsController extends AppController {

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
				$update_data['PieProductSetting']['id'] = $val;
				$update_data['PieProductSetting']['sort'] = $this->request->data['sort'][$key];
				$data_array[] = $update_data;
			}	
			
			/*pr($data_array);
			exit;*/
										
			//$this->Product->saveAll($data_array); 	
			if($this->PieProductSetting->saveMany($data_array)){			
				$this->Session->setFlash(__('The product setting has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));		
			}else{
				$this->Session->setFlash(__('Sort must be unique.'), 'flash/warning');
			}
		}
		
		$this->set('page_title', 'Pie Product Setting List');
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
		$this->set('pieProductSettings', $this->paginate());
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
		if (!$this->PieProductSetting->exists($id)) {
			throw new NotFoundException(__('Invalid product combination'));
		}
		$options = array('conditions' => array('PieProductSetting.' . $this->PieProductSetting->primaryKey => $id));
		$this->set('productCombination', $this->PieProductSetting->find('first', $options));
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
		
		
		if ($this->request->is('post')) {

			if( empty($this->request->data['PieProductSetting']['product_id']) ){
				$this->request->data['PieProductSetting']['product_id'] = 0;
			}
			
			
				if ($this->PieProductSetting->save($this->request->data)) {
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
        $this->PieProductSetting->id = $id;
		if (!$this->PieProductSetting->exists($id)) {
			throw new NotFoundException(__('Invalid Setting'));
		}

	   
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if( empty($this->request->data['PieProductSetting']['product_id']) ){
				$this->request->data['PieProductSetting']['product_id'] = 0;
			}
			
			if ($this->PieProductSetting->save($this->request->data)) 
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
			$options = array('conditions' => array('PieProductSetting.' . $this->PieProductSetting->primaryKey => $id));
			$this->request->data = $this->PieProductSetting->find('first', $options);
		}
				
		$this->loadModel('Brand');

		$brands = $this->Brand->find('list', array('order'=>array('Brand.name' => 'ASC')));

		$this->loadModel('Product');

		$con = array();

		if($this->request->data['PieProductSetting']['brand_id'] > 0){
			$con=array(
				'Product.brand_id'=>$this->request->data['PieProductSetting']['brand_id']
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
		$this->PieProductSetting->id = $id;
		if (!$this->PieProductSetting->exists()) {
			throw new NotFoundException(__('Invalid Setting'));
		}
		
		if ($this->PieProductSetting->delete())
		{
			$this->Session->setFlash(__('Deleted successfully!'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('List was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	
	
	
}
