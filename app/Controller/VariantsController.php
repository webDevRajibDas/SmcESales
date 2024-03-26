<?php

App::uses('AppController', 'Controller');

/**
 * Variants Controller
 *
 * @property Variant $Variant
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class VariantsController extends AppController {

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
        $this->set('page_title', 'Variant List');
        $this->Variant->recursive = 0;
        $this->paginate = array(
            'order' => array('Variant.id' => 'DESC')
        );
        $this->set('variants', $this->paginate());
    }

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_view($id = null) {
        $this->set('page_title', 'Variant Details');
        if (!$this->Variant->exists($id)) {
            throw new NotFoundException(__('Invalid variant'));
        }
        $options = array('conditions' => array('Variant.' . $this->Variant->primaryKey => $id));
        $this->set('variant', $this->Variant->find('first', $options));
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add() {
        $this->set('page_title', 'Add Variant');
        if ($this->request->is('post')) {
            $this->Variant->create();
            $this->request->data['Variant']['created_at'] = $this->current_datetime();
            $this->request->data['Variant']['updated_at'] = $this->current_datetime();
            $this->request->data['Variant']['created_by'] = $this->UserAuth->getUserId();
            if ($this->Variant->save($this->request->data)) {
                $this->Session->setFlash(__('The variant has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The variant could not be saved. Please, try again.'), 'flash/error');
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
        $this->set('page_title', 'Edit Variant');
        $this->Variant->id = $id;
        if (!$this->Variant->exists($id)) {
            throw new NotFoundException(__('Invalid variant'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['Variant']['updated_at'] = $this->current_datetime();
            $this->request->data['Variant']['updated_by'] = $this->UserAuth->getUserId();
            if ($this->Variant->save($this->request->data)) {
                $this->Session->setFlash(__('The variant has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The variant could not be saved. Please, try again.'), 'flash/error');
            }
        } else {
            $this->Variant->recursive = 0;
            $options = array('conditions' => array('Variant.' . $this->Variant->primaryKey => $id));
            $this->request->data = $this->Variant->find('first', $options);
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
        $this->Variant->id = $id;
        if (!$this->Variant->exists()) {
            throw new NotFoundException(__('Invalid variant'));
        }
        if ($this->Variant->delete()) {
            $this->Session->setFlash(__('Variant deleted'), 'flash/success');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Variant was not deleted'), 'flash/error');
        $this->redirect(array('action' => 'index'));
    }

}
