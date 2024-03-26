<?php

App::uses('AppController', 'Controller');

/**
 * DistChallanDetails Controller
 *
 * @property DistChallanDetail $DistChallanDetail
 * @property PaginatorComponent $Paginator
 */
class DistChallanDetailsController extends AppController {

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
        $this->ChallanDetail->recursive = 0;
        $this->set('challanDetails', $this->paginate());
    }

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_view($id = null) {
        if (!$this->ChallanDetail->exists($id)) {
            throw new NotFoundException(__('Invalid challan detail'));
        }
        $options = array('conditions' => array('ChallanDetail.' . $this->ChallanDetail->primaryKey => $id));
        $this->set('challanDetail', $this->ChallanDetail->find('first', $options));
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add() {
        if ($this->request->is('post')) {
            $this->ChallanDetail->create();
            if ($this->ChallanDetail->save($this->request->data)) {
                $this->flash(__('Challandetail saved.'), array('action' => 'index'));
            } else {
                
            }
        }
        $challans = $this->ChallanDetail->Challan->find('list');
        $products = $this->ChallanDetail->Product->find('list');
        $measurementUnits = $this->ChallanDetail->MeasurementUnit->find('list');
        $batches = $this->ChallanDetail->Batch->find('list');
        $this->set(compact('challans', 'products', 'measurementUnits', 'batches'));
    }

    /**
     * admin_edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_edit($id = null) {
        $this->ChallanDetail->id = $id;
        if (!$this->ChallanDetail->exists($id)) {
            throw new NotFoundException(__('Invalid challan detail'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->ChallanDetail->save($this->request->data)) {
                $this->flash(__('The challan detail has been saved.'), array('action' => 'index'));
            } else {
                
            }
        } else {
            $options = array('conditions' => array('ChallanDetail.' . $this->ChallanDetail->primaryKey => $id));
            $this->request->data = $this->ChallanDetail->find('first', $options);
        }
        $challans = $this->ChallanDetail->Challan->find('list');
        $products = $this->ChallanDetail->Product->find('list');
        $measurementUnits = $this->ChallanDetail->MeasurementUnit->find('list');
        $batches = $this->ChallanDetail->Batch->find('list');
        $this->set(compact('challans', 'products', 'measurementUnits', 'batches'));
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
        $this->ChallanDetail->id = $id;
        if (!$this->ChallanDetail->exists()) {
            throw new NotFoundException(__('Invalid challan detail'));
        }
        if ($this->ChallanDetail->delete()) {
            $this->flash(__('Challan detail deleted'), array('action' => 'index'));
        }
        $this->flash(__('Challan detail was not deleted'), array('action' => 'index'));
        $this->redirect(array('action' => 'index'));
    }

}
