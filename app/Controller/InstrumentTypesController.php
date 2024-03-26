<?php
App::uses('AppController', 'Controller');
/**
 * InstrumentTypes Controller
 *
 * @property InstrumentType $InstrumentType
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class InstrumentTypesController extends AppController {

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
		$this->set('page_title','Instrument type List');
		$this->InstrumentType->recursive = 0;
		$this->paginate = array('order' => array('InstrumentType.id' => 'DESC'));
		$this->set('instrumentTypes', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Instrument type Details');
		if (!$this->InstrumentType->exists($id)) {
			throw new NotFoundException(__('Invalid instrument type'));
		}
		$options = array('conditions' => array('InstrumentType.' . $this->InstrumentType->primaryKey => $id));
		$this->set('instrumentType', $this->InstrumentType->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Instrument type');
		if ($this->request->is('post')) {
			$this->InstrumentType->create();
			$this->request->data['InstrumentType']['created_at'] = $this->current_datetime();
			$this->request->data['InstrumentType']['created_by'] = $this->UserAuth->getUserId();			
			if ($this->InstrumentType->save($this->request->data)) {
				$this->Session->setFlash(__('The instrument type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The instrument type could not be saved. Please, try again.'), 'flash/error');
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
        $this->set('page_title','Edit Instrument type');
        if($id==1 || $id==2)
        {
        	$this->Session->setFlash(__('Cannot edit this'), 'flash/error');
        	$this->redirect(array('action' => 'index'));
        }
		$this->InstrumentType->id = $id;
		if (!$this->InstrumentType->exists($id)) {
			throw new NotFoundException(__('Invalid instrument type'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['InstrumentType']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->InstrumentType->save($this->request->data)) {
				$this->Session->setFlash(__('The instrument type has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The instrument type could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('InstrumentType.' . $this->InstrumentType->primaryKey => $id));
			$this->request->data = $this->InstrumentType->find('first', $options);
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
	/*public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->InstrumentType->id = $id;
		if (!$this->InstrumentType->exists()) {
			throw new NotFoundException(__('Invalid instrument type'));
		}
		if ($this->InstrumentType->delete()) {
			$this->Session->setFlash(__('Instrument type deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Instrument type was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}*/
}
