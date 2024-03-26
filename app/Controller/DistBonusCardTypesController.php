<?php
App::uses('AppController', 'Controller');
/**
 * DistBonusCardTypes Controller
 *
 * @property DistBonusCardType $DistBonusCardType
 * @property PaginatorComponent $Paginator
 */
class DistBonusCardTypesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','Incentive Affiliation Type List');
		$this->DistBonusCardType->recursive = 0;
		$this->paginate = array('order' => array('DistBonusCardType.id' => 'DESC'));
		$this->set('DistBonusCardTypes', $this->paginate());
	}


/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Incentive Affiliation Type');
		if ($this->request->is('post')) {
			$this->DistBonusCardType->create();
			$this->request->data['DistBonusCardType']['created_at'] = $this->current_datetime();
			$this->request->data['DistBonusCardType']['created_by'] = $this->UserAuth->getUserId();			
			if ($this->DistBonusCardType->save($this->request->data)) {
				$this->Session->setFlash(__('The bonus card type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		}
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
        $this->set('page_title','Edit Incentive Affiliation Type');
		$this->DistBonusCardType->id = $id;
		if (!$this->DistBonusCardType->exists($id)) {
			throw new NotFoundException(__('Invalid card type'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['DistBonusCardType']['updated_at'] = $this->current_datetime();
			$this->request->data['DistBonusCardType']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->DistBonusCardType->save($this->request->data)) {
				$this->Session->setFlash(__('The type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$options = array('conditions' => array('DistBonusCardType.' . $this->DistBonusCardType->primaryKey => $id));
			$this->request->data = $this->DistBonusCardType->find('first', $options);
		}
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
		$this->DistBonusCardType->id = $id;
		if (!$this->DistBonusCardType->exists()) {
			throw new NotFoundException(__('Invalid bonus card type'));
		}
		if ($this->DistBonusCardType->delete()) {
			$this->Session->setFlash(__('Incentive Affiliation type deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Incentive Affiliation type was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
