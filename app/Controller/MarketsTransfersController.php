<?php
App::uses('AppController', 'Controller');

/**
 * MarketsTransfers Controller
 *
 * @property MarketsTransfers $MarketsTransfers
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class MarketsTransfersController extends AppController {

/**
 * Components
 *
 * @var array
 */
 	public $uses = array('Office', 'Thana', 'Market', 'SalesPerson', 'Outlet', 'ThanaTerritory');
	public $components = array('Paginator', 'Session','Filter.Filter');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		
		$this->set('page_title','Market and Outlet Import');
		//$office_parent_id = $this->UserAuth->getOfficeParentId();
		
		if ($this->request->is('post')) 
		{
			
			//pr($this->request->data);
			
			
			
			if($this->request->data['market_id'])
			{
				foreach($this->request->data['market_id'] as $market_id)
				{
					//echo $market_id.'<br>';
					$this->Market->id = $market_id;
					if ($this->Market->id) {
						$this->Market->saveField('territory_id', $this->request->data['Market']['to_territory_id']);
					}
				}
				$this->Session->setFlash(__('Markets Transfer Successfully!'), 'flash/success');
				
			}
			else
			{
				$this->Session->setFlash(__('Please select Market!'), 'flash/error');
			}
			
			$this->redirect(array('action' => 'index'));
						
		}
		
		
		//$thanas = $this->ThanaTerritory->find('all',array('conditions'=>array('ThanaTerritory.territory_id'=>$id)));
		
		
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if($office_parent_id == 0)
		{					
			$office_conditions = array(
				'office_type_id' => 2,
				"NOT" => array( "id" => array(30, 31, 37))
				);
		}else{				
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}
		
		$offices = $this->Office->find('list', array(
			'conditions'=> $office_conditions, 
			'order'=>array('office_name'=>'asc')
		));
		
		$this->set(compact('offices'));
		
	}
	
	
	public function get_thana_list(){

		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		$territory_id = $this->request->data['territory_id'];
        
		
		$thanas = $this->ThanaTerritory->find('all',array('conditions'=>array('ThanaTerritory.territory_id'=>$territory_id)));
		
		//pr($thanas);
		
		//$data_array = Set::extract($territory, '{n}.Territory');
		
		$data_array = array();
		
		foreach($thanas as $key => $value)
		{
			$data_array[] = array(
				'id' => $value['Thana']['id'],
				'name' => $value['Thana']['name'],
			);
		}
		
		//pr($data_array);
		
		if(!empty($thanas)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}
	
	
	public function get_market_list() 
	{		
		$view = new View($this);
        $form = $view->loadHelper('Form');	

		
		$territory_id = $this->request->data['territory_id'];
		$thana_id = $this->request->data['thana_id'];
		
		if($thana_id !='')
		{	
			$market_list = $this->Market->find('list',array(
				'conditions' => array(
					'Market.thana_id' => $thana_id,
					'Territory.id' => $territory_id
				), 	
				'order' => array('Market.name'=>'ASC'),
				'recursive' => 1
			));
				
			echo $form->input('market_id', array('label'=>false, 'multiple' => 'checkbox', 'options' => $market_list, 'required'=>true));
		}
		else
		{
			echo '';
		}
		
		$this->autoRender = false;		
	}
	
	
}
