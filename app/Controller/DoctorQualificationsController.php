<?php
App::uses('AppController', 'Controller');
/**
 * DoctorQualifications Controller
 *
 * @property DoctorQualification $DoctorQualification
 * @property PaginatorComponent $Paginator
 */
class DoctorQualificationsController extends AppController {

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
		$this->set('page_title','Doctor qualification List');
		$this->DoctorQualification->recursive = 0;
		$this->paginate = array('order' => array('DoctorQualification.id' => 'DESC'));
		$this->set('doctorQualifications', $this->paginate());
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Doctor qualification');
		if ($this->request->is('post')) {
			$this->DoctorQualification->create();
			$this->request->data['DoctorQualification']['created_at'] = $this->current_datetime();
			$this->request->data['DoctorQualification']['created_by'] = $this->UserAuth->getUserId();			
			if ($this->DoctorQualification->save($this->request->data)) {
				$this->Session->setFlash(__('The doctor qualification has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The doctor qualification could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$DoctorType = $this->DoctorQualification->DoctorType->find('list');
		$this->set(compact('DoctorType'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
        $this->set('page_title','Edit Doctor qualification');
		$this->DoctorQualification->id = $id;
		if (!$this->DoctorQualification->exists($id)) {
			throw new NotFoundException(__('Invalid doctor qualification'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['DoctorQualification']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->DoctorQualification->save($this->request->data)) {
				$this->Session->setFlash(__('The doctor qualification has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The doctor qualification could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('DoctorQualification.' . $this->DoctorQualification->primaryKey => $id));
			$this->request->data = $this->DoctorQualification->find('first', $options);
		}
		$DoctorType = $this->DoctorQualification->DoctorType->find('list');
		$this->set(compact('DoctorType'));
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
		$this->DoctorQualification->id = $id;
		if (!$this->DoctorQualification->exists()) {
			throw new NotFoundException(__('Invalid doctor qualification'));
		}
		if ($this->DoctorQualification->delete()) {
			$this->Session->setFlash(__('Doctor qualification deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Doctor qualification was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
