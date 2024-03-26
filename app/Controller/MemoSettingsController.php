<?php
App::uses('AppController', 'Controller');
/**
 * MemoSettings Controller
 *
 * @property MemoSetting $MemoSetting
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class MemoSettingsController extends AppController {

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
		$this->set('page_title','Memo Setting');
		$conditions = array();
		$this->paginate = array(	
			//'fields' => array('DISTINCT Combination.*'),		
			//'joins' => $joins,
			//'conditions' => $conditions,
			'order'=>   array('sort' => 'asc')   
			
		);
		//pr($this->paginate());
		//$this->set('product_id', $product_id);
		$this->set('MemoSettings', $this->paginate());
		
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			
			$MemoSettingDatas = $this->request->data['MemoSetting'];
			//pr($MemoSettingDatas);
			$total = count($MemoSettingDatas);
			//exit;
						
			foreach($MemoSettingDatas as $key => $value){
				//pr($key);
				
				$this->MemoSetting->id = $this->MemoSetting->field('id', array('name' => $key));
				if ($this->MemoSetting->id) {
					$this->MemoSetting->saveField('value', $value);
				}
			}
			
			$this->Session->setFlash(__('The setting has been update!'), 'flash/success');
			$this->redirect(array('action' => 'index'));
			
		}
		
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Memo Combination Details');
		if (!$this->MemoSetting->exists($id)) {
			throw new NotFoundException(__('Invalid product combination'));
		}
		$options = array('conditions' => array('MemoSetting.' . $this->MemoSetting->primaryKey => $id));
		$this->set('productCombination', $this->MemoSetting->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add($id = null) {
		$this->set('page_title','Add Memo Setting');
		
		$this->loadModel('Memo');
		$products = $this->Memo->find('list', array('order'=>array('Memo.order' => 'ASC')));
		
		/*$this->html = '';
		foreach($products as $key=>$val)
		{
			$this->html .= '<option value="'.$key.'">'.addslashes($val).'</option>';		
		}
		$product_list = $this->html;*/
		
		
		if ($this->request->is('post')) {
			
			
				if ($this->MemoSetting->save($this->request->data)) {
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
	   
	    $this->set('page_title','Edit Memo Setting');
        $this->MemoSetting->id = $id;
		if (!$this->MemoSetting->exists($id)) {
			throw new NotFoundException(__('Invalid Setting'));
		}

	   $this->loadModel('Memo');
	   $products = $this->Memo->find('list', array('order'=>array('Memo.order' => 'ASC')));
	   
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			
			if ($this->MemoSetting->save($this->request->data)) 
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
			$options = array('conditions' => array('MemoSetting.' . $this->MemoSetting->primaryKey => $id));
			$this->request->data = $this->MemoSetting->find('first', $options);
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
		$this->MemoSetting->id = $id;
		if (!$this->MemoSetting->exists()) {
			throw new NotFoundException(__('Invalid Setting'));
		}
		
		if ($this->MemoSetting->delete())
		{
			$this->Session->setFlash(__('Deleted successfully!'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('List was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	
	
	
}
