<?php
App::uses('AppController', 'Controller');

/**
 * ThanaTransfers Controller
 *
 * @property ThanaTransfers $ThanaTransfers
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ThanaTransfersController extends AppController {

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
		
		$this->set('page_title','Thana and Outlet Import');
		//$office_parent_id = $this->UserAuth->getOfficeParentId();
		
		if ($this->request->is('post')) 
		{
			
			
			
			
			if($this->request->data['thana_id'])
			{
				//pr($this->request->data);
				//exit;
				
				foreach($this->request->data['thana_id'] as $thana_id)
				{
					//echo $thana_id.'<br>';
					//echo $this->request->data['Thana']['territory_id'].'<br>';
					
					//update thanas
					$ThanaTerritory_id = $this->ThanaTerritory->field('id', array('thana_id' => $thana_id, 'territory_id' => $this->request->data['Thana']['territory_id']));
										
					$this->ThanaTerritory->id = $ThanaTerritory_id;
					if ($this->ThanaTerritory->id){
						$this->ThanaTerritory->saveField('territory_id',$this->request->data['Thana']['to_territory_id']);
					}
					
					//update markets
					$this->Market->updateAll(
						array( 'Market.territory_id' => $this->request->data['Thana']['to_territory_id'] ), //fields to update
						array( 'Market.thana_id' => $thana_id, 'Market.territory_id' => $this->request->data['Thana']['territory_id'])  //condition
					);
					
				}
				$this->Session->setFlash(__('Thanas Transfer Successfully!'), 'flash/success');
				
			}
			else
			{
				$this->Session->setFlash(__('Please select Thana!'), 'flash/error');
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
	
	
	
	public function get_thana_list() 
	{		
		$view = new View($this);
        $form = $view->loadHelper('Form');	

		
		$territory_id = $this->request->data['territory_id'];
		
		if($territory_id !='')
		{	
			/*$thana_list = $this->ThanaTerritory->find('list',array(
				'conditions' => array('ThanaTerritory.territory_id' => $territory_id), 	
				//'order' => array('Thana.name'=>'ASC'),
				'recursive' => -1
			));*/
			
			
			$thanas = $this->ThanaTerritory->find('all',array('conditions'=>array('ThanaTerritory.territory_id'=>$territory_id)));
		
			//pr($thanas);
			
			//$data_array = Set::extract($territory, '{n}.Territory');
			
			$thana_list = array();
			
			foreach($thanas as $key => $value)
			{
				$thana_list[$value['Thana']['id']] = $value['Thana']['name'];
					
			}
			
			//pr($thana_list);
				
			echo $form->input('thana_id', array('label'=>false, 'multiple' => 'checkbox', 'options' => $thana_list, 'required'=>true));
		}
		else
		{
			echo '';
		}
		
		$this->autoRender = false;		
	}
	
	
}
