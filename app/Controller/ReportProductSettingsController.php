<?php
App::uses('AppController', 'Controller');
/**
 * ProductSettings Controller
 *
 * @property ReportProductSetting $ReportProductSetting
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ReportProductSettingsController extends AppController {

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
				$update_data['ReportProductSetting']['id'] = $val;
				$update_data['ReportProductSetting']['sort'] = $this->request->data['sort'][$key];
				$data_array[] = $update_data;
			}	
			
			/*pr($data_array);
			exit;*/
										
			//$this->Product->saveAll($data_array); 	
			if($this->ReportProductSetting->saveMany($data_array)){			
				$this->Session->setFlash(__('The product setting has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));		
			}else{
				$this->Session->setFlash(__('Sort must be unique.'), 'flash/warning');
			}
		}
		
		$this->set('page_title', 'Report Product Setting List');
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
		$this->set('reportProductSettings', $this->paginate());
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
		if (!$this->ReportProductSetting->exists($id)) {
			throw new NotFoundException(__('Invalid product combination'));
		}
		$options = array('conditions' => array('ReportProductSetting.' . $this->ReportProductSetting->primaryKey => $id));
		$this->set('productCombination', $this->ReportProductSetting->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add($id = null) {
		$this->set('page_title','Add Product Setting');
		
		$this->loadModel('Product');
		$products = $this->Product->find('list', array('order'=>array('Product.order' => 'ASC')));
		
		/*$this->html = '';
		foreach($products as $key=>$val)
		{
			$this->html .= '<option value="'.$key.'">'.addslashes($val).'</option>';		
		}
		$product_list = $this->html;*/
		
		
		if ($this->request->is('post')) {
			
			
				if ($this->ReportProductSetting->save($this->request->data)) {
					$this->Session->setFlash(__('The setting has been saved'), 'flash/success');
					$this->redirect(array('action' => 'index'));
					exit;
				}
			
			
		}
		
		
		$this->set(compact('products', 'id'));
		
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
        $this->ReportProductSetting->id = $id;
		if (!$this->ReportProductSetting->exists($id)) {
			throw new NotFoundException(__('Invalid Setting'));
		}

	   $this->loadModel('Product');
	   $products = $this->Product->find('list', array('order'=>array('Product.order' => 'ASC')));
	   
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			
			if ($this->ReportProductSetting->save($this->request->data)) 
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
			$options = array('conditions' => array('ReportProductSetting.' . $this->ReportProductSetting->primaryKey => $id));
			$this->request->data = $this->ReportProductSetting->find('first', $options);
		}
				
		$this->set(compact('products'));
		
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
		$this->ReportProductSetting->id = $id;
		if (!$this->ReportProductSetting->exists()) {
			throw new NotFoundException(__('Invalid Setting'));
		}
		
		if ($this->ReportProductSetting->delete())
		{
			$this->Session->setFlash(__('Deleted successfully!'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('List was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	
	
	
}
