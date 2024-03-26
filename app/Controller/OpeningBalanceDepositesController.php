<?php

App::uses('AppController', 'Controller');

/**
 * Sale Targets Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class OpeningBalanceDepositesController extends AppController {
    /**
     * Components
     *
     * @var array
     */
	 
	  public $components = array('Paginator','Filter.Filter');
	 
	 public $uses = array('OpeningBalanceDeposite', 'OpeningBalance', 'OpeningBalanceDeposite', 'Office', 'Territory', 'FiscalYear');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index($get_fiscal_year_id = null) 
	{
		$this->set('page_title', 'Opening Balances Deposite List');
		
		$office_parent_id 	= 	$this->UserAuth->getOfficeParentId(); 	//get office parent id
		$office_id 			= 	$this->UserAuth->getOfficeId(); 		//get office id
		$user_id 			= 	$this->UserAuth->getUserId(); 			//get user id
		
		
		if($office_parent_id){
			$conditions = array('OpeningBalanceDeposite.office_id' => $office_id);
		}else{
			$conditions = array();
		}

        $this->paginate = array(
              'conditions' => $conditions,
              'order' => array('OpeningBalanceDeposite.id' => 'desc'),
              'limit' => 50,
			  'recursive' => 2
            );

        $this->set('OpeningBalanceDeposites', $this->paginate());
		
		
		//for search
		$office_parent_id 	= 	$this->UserAuth->getOfficeParentId(); 	//get office parent id
		$office_id = isset($this->request->data['OpeningBalanceDeposite']['office_id'])!='' ? $this->request->data['OpeningBalanceDeposite']['office_id'] : $this->UserAuth->getOfficeId();
		
		if(!$office_parent_id)
		{
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' => 2,
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
			$this->set(compact('offices'));
		}
		else
		{
			
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' => 2,
					'id' => $office_id
					), 
				'order'=>array('office_name'=>'asc')
			));
		}
		
		$territory_id = isset($this->request->data['OpeningBalanceDeposite']['territory_id'])!='' ? $this->request->data['OpeningBalanceDeposite']['territory_id'] : 0;
		$territories = $this->Territory->find('list',array(
			'conditions' => array('Territory.office_id'=>$office_id),
			'order' => array('Territory.name'=>'asc')
		));
		
		
		$this->set(compact('territories', 'offices'));
		
	}
	
	public function admin_add($get_fiscal_year_id = null) 
	{
		$this->set('page_title', 'Deposite Add');
		
		$office_parent_id 	= 	$this->UserAuth->getOfficeParentId(); 	//get office parent id
		$user_id 			= 	$this->UserAuth->getUserId(); 			//get user id
		
		
		$fiscal_year_id = 0;
		$area_office_id = 0;
		
		if($this->request->is('post'))
		{
			//pr($this->request->data);
			
			$amount = $this->request->data['OpeningBalanceDeposite']['amount'];
			$due_collection = $this->request->data['OpeningBalanceDeposite']['due_collection']; 
			$area_office_id = $this->request->data['OpeningBalanceDeposite']['office_id'];
			
			if($due_collection < $amount){
				$this->Session->setFlash(__('Deposite Amount must be less than Market Collection!'), 'flash/error');
				$this->redirect(array('action' => 'add'));
				exit;
			}
			
			$office_id = $this->request->data['OpeningBalanceDeposite']['office_id'];		
			//$fiscal_year_id = $this->request->data['OpeningBalanceDeposite']['fiscal_year_id'];

			
			$data['OpeningBalanceDeposite']['opening_balance_id'] = $this->request->data['OpeningBalanceDeposite']['opening_balance_id'];
			//$data['OpeningBalanceDeposite']['fiscal_year_id'] = $this->request->data['OpeningBalanceDeposite']['fiscal_year_id'];
			$data['OpeningBalanceDeposite']['office_id'] = $office_id;
			
			$data['OpeningBalanceDeposite']['entry_date'] = date('Y-m-d', strtotime($this->request->data['OpeningBalanceDeposite']['date_added']));
			
			$data['OpeningBalanceDeposite']['amount'] = $amount;
			
			$data['OpeningBalanceDeposite']['created_at'] = $this->current_datetime();
			$data['OpeningBalanceDeposite']['created_by'] = $this->UserAuth->getUserId();
			$data['OpeningBalanceDeposite']['updated_at'] = $this->current_datetime(); 
			$data['OpeningBalanceDeposite']['updated_by'] = $this->UserAuth->getUserId();
					
			//pr($data);
			//exit;	
			
			if($this->OpeningBalanceDeposite->save($data))
			{
				$this->Session->setFlash(__('Deposite has been save successfully!'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}	
			
			
			
		}
		else
		{
			$office_id = $this->UserAuth->getOfficeId(); 		//get office id
		}
		
		$this->set(compact('office_parent_id', 'office_id', 'area_office_id', 'fiscal_year_id'));
		
		
		
		
		
		//get Office list
		if(!$office_parent_id)
		{
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' => 2,
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
			$this->set(compact('offices'));
		}
		else
		{
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' => 2,
					'id' => $office_id
					), 
				'order'=>array('office_name'=>'asc')
			));
		}
		
						
        $fiscalYears = $this->FiscalYear->find('list', array('fields' => array('year_code')));
      
	    $this->set(compact('fiscalYears', 'offices'));
		
	}
	
	public function admin_edit($get_fiscal_year_id = null) 
	{
		$office_parent_id 	= 	$this->UserAuth->getOfficeParentId(); 	//get office parent id
		$office_id 			= 	$this->UserAuth->getOfficeId(); 		//get office id
		$user_id 			= 	$this->UserAuth->getUserId(); 			//get user id
		
		//get Office list
		if(!$office_parent_id)
		{
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' => 2,
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
			$this->set(compact('offices'));
		}
		else
		{
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' => 2,
					'id' => $office_id
					), 
				'order'=>array('office_name'=>'asc')
			));
		}
		
		
						
        $fiscalYears = $this->FiscalYear->find('list', array('fields' => array('year_code')));
      
	    $this->set(compact('fiscalYears', 'offices'));
		
	}
	
	
	public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->OpeningBalanceDeposite->id = $id;
		if (!$this->OpeningBalanceDeposite->exists()) {
			throw new NotFoundException(__('Invalid Deposite!'));
		}
		if ($this->OpeningBalanceDeposite->delete()) {
			$this->Session->setFlash(__('Deposite deleted!'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Deposite was not deleted!'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	
	
	public function get_balance_list()
	{
		$this->loadModel('OpeningBalance');
		
		$office_id = $this->request->data['office_id'];
		//$fiscal_year_id = $this->request->data['fiscal_year_id'];
	
		$opening_result = $this->OpeningBalance->find('all', array(
						'conditions' => array('OpeningBalance.office_id' => $office_id),
						//'order' => array('name' => 'asc'),
						'recursive'=> 0
					));
			
					
			
			
		//$data_array = Set::extract($territory, '{n}.Territory');
		
		$data_array = array();
		
		$output = '<option value="">---- Select ----</option>';
		
		foreach($opening_result as $key => $value)
		{
			$output .= '<option value="'.$value['OpeningBalance']['id'].'">'.$value['Territory']['name'].'</option>';
		}
			
		echo $output;
		 
		$this->autoRender = false;
	}
	
	
	
	public function get_collection_by_opening_balance_id()
	{
		$this->loadModel('OpeningBalanceDeposite');
		
		$opening_balance_id = $this->request->data['opening_balance_id'];
		$office_id = $this->request->data['office_id'];
		//$fiscal_year_id = $this->request->data['fiscal_year_id'];
	
		
		
		$collection_result = $this->OpeningBalanceDeposite->find('all', array(
						'conditions' => array('OpeningBalanceDeposite.opening_balance_id' => $opening_balance_id),
						'fields' => array('SUM(amount) as total_collection'),
						//'order' => array('name' => 'asc'),
						'recursive'=> -1
					));
					
		//pr($collection_result);
		
		$deposite_result = $this->OpeningBalanceDeposite->find('all', array(
						'conditions' => array('OpeningBalanceDeposite.opening_balance_id' => $opening_balance_id),
						'fields' => array('SUM(amount) as total_deposite'),
						//'order' => array('name' => 'asc'),
						'recursive'=> -1
					));
		
		//pr($deposite_result);
					
		if($collection_result){	
			echo ($collection_result[0][0]['total_collection'] - $deposite_result[0][0]['total_deposite']);		
		}else{
			echo 0;
		}
			
		$this->autoRender = false;
	}
	

    
}
