<?php

App::uses('AppController', 'Controller');

/**
 * Sale Targets Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class OpeningBalanceCollectionsController extends AppController {
    /**
     * Components
     *
     * @var array
     */
	 
	 public $components = array('Paginator','Filter.Filter');
	 
	 public $uses = array('OpeningBalanceCollection', 'OpeningBalance', 'Office', 'Territory', 'FiscalYear');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index($get_fiscal_year_id = null) 
	{
		$this->set('page_title', 'Opening Balances Collection List');
		
		$office_parent_id 	= 	$this->UserAuth->getOfficeParentId(); 	//get office parent id
		$office_id 			= 	$this->UserAuth->getOfficeId(); 		//get office id
		$user_id 			= 	$this->UserAuth->getUserId(); 			//get user id
		
		
		if($office_parent_id){
			$conditions = array('OpeningBalanceCollection.office_id' => $office_id);
		}else{
			$conditions = array();
		}

        $this->paginate = array(
              'conditions' => $conditions,
              'order' => array('OpeningBalanceCollection.id' => 'desc'),
              'limit' => 50,
			  'recursive' => 2
            );

        $this->set('OpeningBalanceCollections', $this->paginate());
		
		//for search
		$office_parent_id 	= 	$this->UserAuth->getOfficeParentId(); 	//get office parent id
		$office_id = isset($this->request->data['OpeningBalanceCollection']['office_id'])!='' ? $this->request->data['OpeningBalanceCollection']['office_id'] : $this->UserAuth->getOfficeId();
		
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
		
		$territory_id = isset($this->request->data['OpeningBalanceCollection']['territory_id'])!='' ? $this->request->data['OpeningBalanceCollection']['territory_id'] : 0;
		$territories = $this->Territory->find('list',array(
			'conditions' => array('Territory.office_id'=>$office_id),
			'order' => array('Territory.name'=>'asc')
		));
		
		
		$this->set(compact('territories', 'offices'));
		
		
	}
	
	public function admin_add($get_fiscal_year_id = null) 
	{
		$this->set('page_title', 'Collection Add');
		
		$office_parent_id 	= 	$this->UserAuth->getOfficeParentId(); 	//get office parent id
		$user_id 			= 	$this->UserAuth->getUserId(); 			//get user id
		
		
		$fiscal_year_id = 0;
		$area_office_id = 0;
		
		if($this->request->is('post'))
		{
			//pr($this->request->data);
			
			$amount = $this->request->data['OpeningBalanceCollection']['amount'];
			$due_outstanding = $this->request->data['OpeningBalanceCollection']['due_outstanding']; 
			$area_office_id = $this->request->data['OpeningBalanceCollection']['office_id'];
			
			if($due_outstanding < $amount){
				$this->Session->setFlash(__('Collection Amount must be less than Market Outstanding!'), 'flash/error');
				$this->redirect(array('action' => 'add'));
				exit;
			}
			
			$office_id = $this->request->data['OpeningBalanceCollection']['office_id'];		
			//$fiscal_year_id = $this->request->data['OpeningBalanceCollection']['fiscal_year_id'];

			
			$data['OpeningBalanceCollection']['opening_balance_id'] = $this->request->data['OpeningBalanceCollection']['opening_balance_id'];
			//$data['OpeningBalanceCollection']['fiscal_year_id'] = $this->request->data['OpeningBalanceCollection']['fiscal_year_id'];
			$data['OpeningBalanceCollection']['office_id'] = $office_id;
			
			$data['OpeningBalanceCollection']['entry_date'] = date('Y-m-d', strtotime($this->request->data['OpeningBalanceCollection']['date_added']));
			
			$data['OpeningBalanceCollection']['amount'] = $amount;
			
			$data['OpeningBalanceCollection']['created_at'] = $this->current_datetime();
			$data['OpeningBalanceCollection']['created_by'] = $this->UserAuth->getUserId();
			$data['OpeningBalanceCollection']['updated_at'] = $this->current_datetime(); 
			$data['OpeningBalanceCollection']['updated_by'] = $this->UserAuth->getUserId();
					
			//pr($data);
			//exit;	
			
			if($this->OpeningBalanceCollection->save($data))
			{
				$this->Session->setFlash(__('Collection has been save successfully!'), 'flash/success');
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
		$this->OpeningBalanceCollection->id = $id;
		if (!$this->OpeningBalanceCollection->exists()) {
			throw new NotFoundException(__('Invalid Collection!'));
		}
		if ($this->OpeningBalanceCollection->delete()) {
			$this->Session->setFlash(__('Collection deleted!'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Collection was not deleted!'), 'flash/error');
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
	
	
	
	public function get_outstanding_by_opening_balance_id()
	{
		$this->loadModel('OpeningBalance');
		
		$opening_balance_id = $this->request->data['opening_balance_id'];
		$office_id = $this->request->data['office_id'];
		//$fiscal_year_id = $this->request->data['fiscal_year_id'];
	
		$opening_result = $this->OpeningBalance->find('first', array(
						'conditions' => array('OpeningBalance.id' => $opening_balance_id),
						//'order' => array('name' => 'asc'),
						'recursive'=> -1
					));
		
		
		$collection_result = $this->OpeningBalanceCollection->find('all', array(
						'conditions' => array('OpeningBalanceCollection.opening_balance_id' => $opening_balance_id),
						'fields' => array('SUM(amount) as total_collection'),
						//'order' => array('name' => 'asc'),
						'recursive'=> -1
					));
		
		if($collection_result[0][0]['total_collection'] > 0){			
			$total_collection = $collection_result[0][0]['total_collection'];
		}else{
			$total_collection = 0;
		}
					
		if($opening_result){	
			echo ($opening_result['OpeningBalance']['total_outstanding'] - $total_collection);		
		}else{
			echo 0;
		}
			
		$this->autoRender = false;
	}
	

    
}
