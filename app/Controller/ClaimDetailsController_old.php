<?php
App::uses('AppController', 'Controller');
/**
 * ClaimDetails Controller
 *
 * @property ClaimDetail $ClaimDetail
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ClaimDetailsController extends AppController {

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
		$this->set('page_title','Claim detail List');
		$this->ClaimDetail->recursive = 0;
		$this->paginate = array('order' => array('ClaimDetail.id' => 'DESC'));
		$this->set('claimDetails', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Claim detail Details');
		if (!$this->ClaimDetail->exists($id)) {
			throw new NotFoundException(__('Invalid claim detail'));
		}
		$options = array('conditions' => array('ClaimDetail.' . $this->ClaimDetail->primaryKey => $id));
		$this->set('claimDetail', $this->ClaimDetail->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Claim detail');
		if ($this->request->is('post')) {
			$this->ClaimDetail->create();
			$this->request->data['ClaimDetail']['created_at'] = $this->current_datetime();
			$this->request->data['ClaimDetail']['created_by'] = $this->UserAuth->getUserId();			
			if ($this->ClaimDetail->save($this->request->data)) {
				$this->Session->setFlash(__('The claim detail has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The claim detail could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$claims = $this->ClaimDetail->Claim->find('list');
		$products = $this->ClaimDetail->Product->find('list');
		$measurementUnits = $this->ClaimDetail->MeasurementUnit->find('list');
		$inventoryStatuses = $this->ClaimDetail->InventoryStatus->find('list');
		$this->set(compact('claims', 'products', 'measurementUnits', 'inventoryStatuses'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
        $this->set('page_title','Edit Claim detail');
		$this->ClaimDetail->id = $id;
		if (!$this->ClaimDetail->exists($id)) {
			throw new NotFoundException(__('Invalid claim detail'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['ClaimDetail']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->ClaimDetail->save($this->request->data)) {
				$this->Session->setFlash(__('The claim detail has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The claim detail could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('ClaimDetail.' . $this->ClaimDetail->primaryKey => $id));
			$this->request->data = $this->ClaimDetail->find('first', $options);
		}
		$claims = $this->ClaimDetail->Claim->find('list');
		$products = $this->ClaimDetail->Product->find('list');
		$measurementUnits = $this->ClaimDetail->MeasurementUnit->find('list');
		$inventoryStatuses = $this->ClaimDetail->InventoryStatus->find('list');
		$this->set(compact('claims', 'products', 'measurementUnits', 'inventoryStatuses'));
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
		$this->ClaimDetail->id = $id;
		if (!$this->ClaimDetail->exists()) {
			throw new NotFoundException(__('Invalid claim detail'));
		}
		if ($this->ClaimDetail->delete()) {
			$this->Session->setFlash(__('Claim detail deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Claim detail was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
