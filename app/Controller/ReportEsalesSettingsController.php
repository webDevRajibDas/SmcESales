<?php
App::uses('AppController', 'Controller');
/**
 * EsalesSettings Controller
 *
 * @property ReportEsalesSetting $ReportEsalesSetting
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ReportEsalesSettingsController extends AppController {

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
		
		if ($this->request->is('post') || $this->request->is('put')) {
			// for update data
			$data_array = array();
			foreach($this->request->data['product_setting_id'] as $key=>$val)
			{
				$update_data['ReportEsalesSetting']['id'] = $val;
				$update_data['ReportEsalesSetting']['sort'] = $this->request->data['sort'][$key];
				$data_array[] = $update_data;
			}	
			
			/*pr($data_array);
			exit;*/
										
			//$this->Esales->saveAll($data_array); 	
			if($this->ReportEsalesSetting->saveMany($data_array)){			
				$this->Session->setFlash(__('The product setting has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));		
			}else{
				$this->Session->setFlash(__('Sort must be unique.'), 'flash/warning');
			}
		}
		
		$this->set('page_title', 'Report Esales Setting List');
		$conditions = array();
		$this->paginate = array(	
			//'fields' => array('DISTINCT Combination.*'),		
			//'joins' => $joins,
			//'conditions' => $conditions,
			'limit' => 100,
			'order'=>   array('sort' => 'asc')   
		);
		//pr($this->paginate());
		//$this->set('product_id', $product_id);
		$this->set('reportEsalesSettings', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) 
	{
		$this->set('page_title','Esales Combination Details');
		if (!$this->ReportEsalesSetting->exists($id)) {
			throw new NotFoundException(__('Invalid product combination'));
		}
		$options = array('conditions' => array('ReportEsalesSetting.' . $this->ReportEsalesSetting->primaryKey => $id));
		$this->set('productCombination', $this->ReportEsalesSetting->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add($id = null) 
	{
		$this->set('page_title','Add Esales Setting');
		
		//pr($this->request->data);
		//exit;
		
		if ($this->request->is('post')) {
			
			//$this->request->data = $this->ReportEsalesSetting->find('first', $options);
			
			//pr($this->request->data);
						
			$validation = $this->rankNameCheck(0);
			
			if($validation['error']==0)
			{
				if ($this->ReportEsalesSetting->save($this->request->data)) {
					$this->Session->setFlash(__('The setting has been saved!'), 'flash/success');
					$this->redirect(array('action' => 'index'));
					exit;
				}
			}
			else
			{
				$this->Session->setFlash(__($validation['msg']), 'flash/error');
				//$this->redirect(array('action' => 'add'));
			}
		}
		
		$type_list = array(
			'1' => 'Monthly Sales',
			'2' => 'Total Sales'
		);
		
		$operator_list = array(
			'>' => '>',
			'>=' => '>=',
			'<' => '<',
			'<=' => '<='
		);
		$this->set(compact('type_list', 'operator_list'));
		
		//$this->set(compact('products', 'id'));
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
	   
	    $this->set('page_title','Edit Esales Setting');
        $this->ReportEsalesSetting->id = $id;
		if (!$this->ReportEsalesSetting->exists($id)) {
			throw new NotFoundException(__('Invalid Setting'));
		}
	  
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$validation = $this->rankNameCheck($id);
			
			if($validation['error']==0)
			{
				if ($this->ReportEsalesSetting->save($this->request->data)) 
				{
					$this->Session->setFlash(__('The setting has been update!'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				} 
				else 
				{
					$this->Session->setFlash(__('The setting could not be update. Please, try again.'), 'flash/error');
				}
			}
			else
			{
				$this->Session->setFlash(__($validation['msg']), 'flash/error');
				//$this->redirect(array('action' => 'add'));
			}
		} 
		else 
		{
			$options = array('conditions' => array('ReportEsalesSetting.' . $this->ReportEsalesSetting->primaryKey => $id));
			$this->request->data = $this->ReportEsalesSetting->find('first', $options);
		}
				
		$type_list = array(
			'1' => 'Monthly Sales',
			'2' => 'Total Sales'
		);
		
		$operator_list = array(
			'>' => '>',
			'>=' => '>=',
			'<' => '<',
			'<=' => '<='
		);
		$this->set(compact('type_list', 'operator_list'));
		
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
		$this->ReportEsalesSetting->id = $id;
		if (!$this->ReportEsalesSetting->exists()) {
			throw new NotFoundException(__('Invalid Setting'));
		}
		
		if ($this->ReportEsalesSetting->delete())
		{
			$this->Session->setFlash(__('Deleted successfully!'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('List was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	
	
	
	private function rankNameCheck($id=0)
	{
		$r_data['error'] = 0;
		$r_data['msg'] = '';
			
		//pr($this->request->data);
		//exit;
		
		$name = $this->request->data['ReportEsalesSetting']['name'];
		$type = $this->request->data['ReportEsalesSetting']['type'];
		
		$range_start = $this->request->data['ReportEsalesSetting']['range_start'];
		$operator_1 = $this->request->data['ReportEsalesSetting']['operator_1'];
		$operator_2 = $this->request->data['ReportEsalesSetting']['operator_2'];
		$range_end = $this->request->data['ReportEsalesSetting']['range_end'];
		
		$this->loadModel('ReportEsalesSetting');  
		
		//Rank name check
		if($id){
			$results = $this->ReportEsalesSetting->find('first', array('conditions'=> array('name' => $name, 'type' => $type, 'id !=' => $id)));
		}else{
			$results = $this->ReportEsalesSetting->find('first', array('conditions'=> array('name' => $name, 'type' => $type)));
		}
		if($results){
			$r_data['error'] = 1;
			$r_data['msg'] = 'Rank already exist!';	
			return $r_data;
		}
		
		
		//Combination check
		if($id){
			$c_results = $this->ReportEsalesSetting->find('first', array('conditions'=> array('type' => $type, 'range_start' => $range_start, 'operator_1' => $operator_1, 'operator_2' => $operator_2, 'range_end' => $range_end, 'id !=' => $id)));
		}else{
			$c_results = $this->ReportEsalesSetting->find('first', array('conditions'=> array('type' => $type, 'range_start' => $range_start, 'operator_1' => $operator_1, 'operator_2' => $operator_2, 'range_end' => $range_end)));
		}
		if($c_results){
			$r_data['error'] = 1;
			$r_data['msg'] = 'Combination already exist!';	
			return $r_data;
		}		
		
		return $r_data;
	}
	
	
}
