<?php
App::uses('AppController', 'Controller');
/**
 * VisitPlanLists Controller
 *
 * @property LiveSalesTrack $LiveSalesTrack
 * @property PaginatorComponent $Paginator
 */
class LiveSalesTracksController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Filter.Filter');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() 
	{
		$this->set('page_title', 'Live Sale Track List');
		
		$this->paginate = array('order' => array('id' => 'DESC'));
						
		$this->set('LiveSalesTracks', $this->paginate());

	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Visit plan list Details');
		if (!$this->LiveSalesTrack->exists($id)) {
			throw new NotFoundException(__('Invalid visit plan list'));
		}
		$options = array('conditions' => array('LiveSalesTrack.' . $this->LiveSalesTrack->primaryKey => $id));
		$this->set('LiveSalesTrack', $this->LiveSalesTrack->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() 
	{
		$this->set('page_title', 'Add Sales Track');
		
		$interval = array();
		
		for($i=1; $i<=60; $i++){
			$interval[$i]=$i.' Min';
		}
		
		if ($this->request->is('post')) 
		{
			$this->request->data['LiveSalesTrack']['active'] = 0;
			
			$this->LiveSalesTrack->create();
			if ($this->LiveSalesTrack->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The sale traking has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
			
		}
				
		$this->set(compact('interval'));
	}
	
	
	
	

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
        $this->set('page_title','Edit Visit plan list');
		$this->LiveSalesTrack->id = $id;
						
		if (!$this->LiveSalesTrack->exists($id)) {
			throw new NotFoundException(__('Invalid visit plan list'));
		}
		
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['LiveSalesTrack']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->LiveSalesTrack->save($this->request->data)) {
				$this->Session->setFlash(__('The visit plan list has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The visit plan list could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('LiveSalesTrack.' . $this->LiveSalesTrack->primaryKey => $id));
			$this->request->data = $this->LiveSalesTrack->find('first', $options);
		}
		
		$interval = array();
		
		for($i=1; $i<=60; $i++){
			$interval[$i]=$i.' Min';
		}
		
		$results = $this->LiveSalesTrack->find('first', array('conditions'=> array('id' => $id)));
		$start_time = date('h:i a', strtotime($results['LiveSalesTrack']['start_time']));
		$end_time = date('h:i a', strtotime($results['LiveSalesTrack']['end_time']));
		
		$this->set(compact('interval', 'start_time', 'end_time'));
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
		$this->LiveSalesTrack->id = $id;
		if (!$this->LiveSalesTrack->exists()) {
			throw new NotFoundException(__('Invalid traking list'));
		}
		if ($this->LiveSalesTrack->delete($id)) {
			$this->Session->setFlash(__('Traking list deleted!'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Traking was not deleted!'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	
}
