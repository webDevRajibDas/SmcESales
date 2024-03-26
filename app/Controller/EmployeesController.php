<?php
App::uses('AppController', 'Controller');

class EmployeesController extends AppController {

	public $components = array('Paginator','Session');


	public function admin_index(){
	    $this->Employee->recursive = 0;
		$this->paginate = array('order' => array('Employee.order' => 'ASC'), 'limit' => 50);
		$this->set('employees', $this->paginate());
    }

	public function admin_add() {
		$this->set('page_title','Add Employee');
		if ($this->request->is('post')) {
			$this->request->data['Employee']['created_at'] = $this->current_datetime();
			$this->request->data['Employee']['created_by'] = $this->UserAuth->getUserId();

			//$this->dd($this->request->data);
			$this->Employee->create();
			if ($this->Employee->save($this->request->data)) {
				$this->Session->setFlash(__('The Employee has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Employee could not be saved. Please, try again.'), 'flash/error');
			}
		}

	}


	function admin_edit($id=null){
		if (!$id && empty($this->data)) {  
			$this->Session->setFlash('Invalid data');  
			$this->redirect(array('action' => 'index'));  
		 }  

		if(!empty($id)){
			$get_data = $this->Employee->find('first',array('conditions'=>array('Employee.id'=>$id)));
			$this->set('edit',$get_data); 
			}
			if($this->request->data){
				//pr($this->request->data);die;
				if($this->Employee->save($this->request->data)){
					$this->Session->setFlash("Edit saved.");
					$this->redirect(array('controller'=>'employees','action'=>'index'));
				}
				else{
					$this->Session->setFlash("Wrong.");
					$this->redirect(array('controller'=>'employees','action'=>'index'));
				} 
			}
		}


	public function admin_delete($id = null){
		$this->Employee->delete($id);  
		$this->Session->setFlash('The employee with id: '.$id.' has been deleted.');  
		$this->redirect(array('action'=>'index'));
	}  

}