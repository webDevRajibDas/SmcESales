<?php
App::uses('AppController', 'Controller');
/**
 * SoStockChecks Controller
 *
 * @property SoStockCheck $SoStockCheck
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class SoStockChecksController extends AppController {

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
	public function admin_index() 
	{
		$this->set('page_title','So stock check List');
		$this->LoadModel('Store');
		$office_parent_id = $this->UserAuth->getOfficeParentId();

        if ($office_parent_id == 0) {
            $conditions = array();
            $storeCondition = array();
        } else {
            $conditions = array('Store.office_id' => $this->UserAuth->getOfficeId());
            $storeCondition = array('Store.office_id' => $this->UserAuth->getOfficeId());
        }
        $conditions['convert(date,SoStockCheck.reported_time) >=']=date('Y-m-d');
        $conditions['convert(date,SoStockCheck.reported_time) <=']=date('Y-m-d');
        $storeCondition['Store.store_type_id']=3;
        $store_list = $this->Store->find('all', 
		array(
			'conditions' => $storeCondition, 
			'fields' => array('Store.id', 'Store.name', 'Territory.id'),
			//'order' => array('Store.name' => 'asc'),
        	'recursive' => 0)
		);
		
		$stores = array();
		foreach($store_list as $key => $value)
		{
			$stores[$value['Store']['id']] = $value['Store']['name'].' ('.$this->getSOName($value['Territory']['id']).')';
		}
		$this->SoStockCheck->recursive = 0;
		$this->paginate = array('order' => array('SoStockCheck.id' => 'DESC'),'conditions' => $conditions);
		$this->set('soStockChecks', $this->paginate());
		if (isset($this->request['data']['CurrentInventory']['store_id'])){ 
			$StoreId = $this->request['data']['CurrentInventory']['store_id'];
		}
		else
		{
			$StoreId = $this->UserAuth->getStoreId();		
		}
		$current_date = date('d-m-Y',strtotime($this->current_date()));	
		$this->set(compact('stores','StoreId','current_date'));
	}

	public function admin_view($id = null) 
	{
		$this->set('page_title','So stock check Details');
		$this->LoadModel('SoStockCheckDetail');
		if (!$this->SoStockCheck->exists($id)) {
			throw new NotFoundException(__('Invalid so stock check'));
		}
		$options = array('conditions' => array('SoStockCheck.' . $this->SoStockCheck->primaryKey => $id));
		$this->set('soStockCheck', $this->SoStockCheck->find('first', $options));
		$this->SoStockCheckDetail->unbindModel(array('belongsTo'=>array('SoStockCheck')));
		$options = array('conditions' => array('SoStockCheckDetail.so_stock_check_id' => $id));
		$this->set('soStockCheckDetail', $this->SoStockCheckDetail->find('all', $options));
	}

	/*	public function admin_add() {
		$this->set('page_title','Add So stock check');
		if ($this->request->is('post')) {
			$this->SoStockCheck->create();
			$this->request->data['SoStockCheck']['created_at'] = $this->current_datetime();
			$this->request->data['SoStockCheck']['created_by'] = $this->UserAuth->getUserId();			
			if ($this->SoStockCheck->save($this->request->data)) {
				$this->Session->setFlash(__('The so stock check has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The so stock check could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$sos = $this->SoStockCheck->So->find('list');
		$stores = $this->SoStockCheck->Store->find('list');
		$this->set(compact('sos', 'stores'));
	}
	public function admin_edit($id = null) {
        $this->set('page_title','Edit So stock check');
		$this->SoStockCheck->id = $id;
		if (!$this->SoStockCheck->exists($id)) {
			throw new NotFoundException(__('Invalid so stock check'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['SoStockCheck']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->SoStockCheck->save($this->request->data)) {
				$this->Session->setFlash(__('The so stock check has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The so stock check could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('SoStockCheck.' . $this->SoStockCheck->primaryKey => $id));
			$this->request->data = $this->SoStockCheck->find('first', $options);
		}
		$sos = $this->SoStockCheck->So->find('list');
		$stores = $this->SoStockCheck->Store->find('list');
		$this->set(compact('sos', 'stores'));
	}

	public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->SoStockCheck->id = $id;
		if (!$this->SoStockCheck->exists()) {
			throw new NotFoundException(__('Invalid so stock check'));
		}
		if ($this->SoStockCheck->delete()) {
			$this->Session->setFlash(__('So stock check deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('So stock check was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}*/
	public function getSOName($territory_id=0)
	{
		if($territory_id)
		{
			$this->loadModel('Territory');
			$territory_info = $this->Territory->find('first', 
				array(
					'conditions' => array('Territory.id' => $territory_id),
					'fields' => array('SalesPerson.name'),
					'recursive' => 0
				)
			);
			//pr($territory_info);
			//exit;
			if($territory_info['SalesPerson']['name']){
				return $territory_info['SalesPerson']['name'];
			}else{
				return 'NA';
			}
		}
		else
		{
			return 'NA';
		}
	}
}
