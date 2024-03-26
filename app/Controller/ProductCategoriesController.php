<?php
App::uses('AppController', 'Controller');
/**
 * ProductCategories Controller
 *
 * @property ProductCategory $ProductCategory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ProductCategoriesController extends AppController {

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
	public function admin_index() {
		$this->set('page_title','Product category List');
		$this->ProductCategory->recursive = 0;
		$this->paginate = array('order' => array('ProductCategory.order' => 'ASC'));
		$this->set('productCategories', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Product category Details');
		if (!$this->ProductCategory->exists($id)) {
			throw new NotFoundException(__('Invalid product category'));
		}
		$options = array('conditions' => array('ProductCategory.' . $this->ProductCategory->primaryKey => $id));
		$this->set('productCategory', $this->ProductCategory->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Product category');
				
		if ($this->request->is('post')) {
			$this->ProductCategory->create();
			if(empty($this->request->data['ProductCategory']['parent_id'])){
				$this->request->data['ProductCategory']['parent_id'] = 0;
			}
			
			/*Category order*/
			$OrderIndex = $this->ProductCategory->find('all', array(
				'fields' => array(
					'max([ProductCategory]."order")+1 as LastOrder'
				),
				'recursive' => -1
			));
			$this->request->data['ProductCategory']['order'] = $OrderIndex[0][0]['LastOrder'];
			/*End Category order*/
			
			$this->request->data['ProductCategory']['created_at'] = $this->current_datetime();
			$this->request->data['ProductCategory']['updated_at'] = $this->current_datetime();
			$this->request->data['ProductCategory']['created_by'] = $this->UserAuth->getUserId();			
			if ($this->ProductCategory->save($this->request->data)) {
				$this->Session->setFlash(__('The product category has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The product category could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$parentProductCategories = $this->ProductCategory->generateTreeList(null, null, null, '__');		
		$this->set(compact('parentProductCategories'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
        $this->set('page_title','Edit Product category');
		$this->ProductCategory->id = $id;
		$this->ProductCategory->recursive = 0;
		if (!$this->ProductCategory->exists($id)) {
			throw new NotFoundException(__('Invalid product category'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			
			if(empty($this->request->data['ProductCategory']['parent_id'])){
				$this->request->data['ProductCategory']['parent_id'] = 0;				
			}
			$this->request->data['ProductCategory']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['ProductCategory']['updated_at'] = $this->current_datetime();
			if ($this->ProductCategory->save($this->request->data)) {
				$this->Session->setFlash(__('The product category has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The product category could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('ProductCategory.' . $this->ProductCategory->primaryKey => $id));
			$this->request->data = $this->ProductCategory->find('first', $options);
		}
		$parentProductCategories = $this->ProductCategory->generateTreeList(null, null, null, '__');
		$this->set(compact('parentProductCategories'));
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->ProductCategory->id = $id;
		if (!$this->ProductCategory->exists()) {
			throw new NotFoundException(__('Invalid product category'));
		}
		$product = $this->ProductCategory->Product->find('all',array(
			'conditions'=>array('Product.product_category_id'=>$id),
			'recursive'=>-1
			));
		if($product){
			$this->Session->setFlash(__('Product category can\'t be Deleted. This Category Already Assign With Product'), 'flash/error');
			$this->redirect(array('action' => 'index'));
		}
		else{
			if ($this->ProductCategory->delete()) {
				$this->Session->setFlash(__('Product category deleted'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		}
		$this->Session->setFlash(__('Product category was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	
	/**
	 * admin_getsubcategory method
	 *
	 * @throws NotFoundException
	 * @throws MethodNotAllowedException
	 * @param int $id
	 * @return list
	 */
	public function admin_getsubcategory() {
		$rs = array(array('id' => '', 'title' => '---- Select Sub Category -----'));
		$category_id = $this->request->data['category_id'];
        $subcategories = $this->ProductCategory->find('all', array(
			'fields' => array('ProductCategory.id', 'ProductCategory.name as title'),
			'conditions' => array('ProductCategory.parent_id' => $category_id),
			'recursive' => -1
		));	
		$data_array = Set::extract($subcategories, '{n}.ProductCategory');	
		if(!empty($subcategories)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}
	
	/*Order change by category tables ajax request*/
	public function change_order(){
		$this->loadModel('ProductCategory');
		$ProductCategoryInfo	= array(
			'id'		=> $this->request->data['CategoryId'],
			'order'		=> $this->request->data['OrderVal'],
			'updated_by'	=> $this->UserAuth->getUserId(),
			'updated_at'	=> $this->current_datetime()
		);
		
		if($this->ProductCategory->save($ProductCategoryInfo)){
			echo $this->request->data['OrderVal'];
		}else{
			echo $this->request->data['LastOrderVal'];
		}$this->autoRender = false;
	}
}
