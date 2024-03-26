<?php
App::uses('AppController', 'Controller');
/**
 * Designations Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class BonusesController extends AppController {

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
		$this->set('page_title','Bonus List');
		$this->Bonus->recursive = 0;		
		$this->paginate = array(			
			'order' => array('Bonus.id' => 'DESC')
		);
		$this->set('bonuses', $this->paginate());
		
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		$office_id = $this->UserAuth->getOfficeId();
		$this->set(compact('office_parent_id','office_id'));		
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Bonus');
		if ($this->request->is('post')) {
			$this->request->data['Bonus']['effective_date'] = date('Y-m-d',strtotime($this->request->data['Bonus']['effective_date'])); 
			$this->request->data['Bonus']['end_date'] = date('Y-m-d',strtotime($this->request->data['Bonus']['end_date'])); 
			$this->request->data['Bonus']['created_at'] = $this->current_datetime(); 
			$this->request->data['Bonus']['created_by'] = $this->UserAuth->getUserId();
			$this->request->data['Bonus']['updated_at'] = $this->current_datetime(); 
			$this->request->data['Bonus']['updated_by'] = 0;
			$this->Bonus->create();
			if ($this->Bonus->save($this->request->data)) {
				$this->Session->setFlash(__('The Bonus has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		}
		$mother_products = $this->Bonus->MotherProduct->find('list',array('order'=>array('order'=>'asc')));
		$bonus_products = $this->Bonus->BonusProduct->find('list',array('order'=>array('order'=>'asc')));
		$this->set(compact('mother_products','bonus_products'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->set('page_title','Edit Bonus');
        $this->Bonus->id = $id;
		if (!$this->Bonus->exists($id)) {
			throw new NotFoundException(__('Invalid Bonus'));
		}
		
		$mother_products = $this->Bonus->MotherProduct->find('list',array('order'=>array('order'=>'asc')));
		$bonus_products = $this->Bonus->BonusProduct->find('list',array('order'=>array('order'=>'asc')));
		
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$this->request->data['Bonus']['effective_date'] = date('Y-m-d', strtotime($this->request->data['Bonus']['effective_date'])); 
			$this->request->data['Bonus']['end_date'] = date('Y-m-d', strtotime($this->request->data['Bonus']['end_date'])); 
			$this->request->data['Bonus']['updated_at'] = $this->current_datetime(); 
			$this->request->data['Bonus']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->Bonus->save($this->request->data)) {
				$this->Session->setFlash(__('The Bonus has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		} 
		else 
		{
			$options = array('conditions' => array('Bonus.' . $this->Bonus->primaryKey => $id));
			
			$this->request->data = $this->Bonus->find('first', $options);
			
			$this->request->data['Bonus']['effective_date'] = date('d-m-Y', strtotime($this->request->data['Bonus']['effective_date'])); 
			$this->request->data['Bonus']['end_date'] = date('d-m-Y', strtotime($this->request->data['Bonus']['end_date'])); 
			
		}
		
		//pr($this->request->data);
		
		$this->set(compact('mother_products','bonus_products'));
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
		$this->Bonus->id = $id;
		if (!$this->Bonus->exists()) {
			throw new NotFoundException(__('Invalid Bonus'));
		}
		if ($this->Bonus->delete()) {
			$this->Session->setFlash(__('Bonus deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Bonus was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	
	
/**
 * admin_bonus_target
 *
 */
	
	public function admin_bonus_target($id = null) {
		
		$this->set('page_title','Area Office Bonus Target Configuration');
        $this->loadModel('Office');
        $this->loadModel('BonusTarget');
		
		if ($this->request->is('post') || $this->request->is('put')) {
			
			if(!empty($this->request->data['office_id']))
			{	
				$insert_data_array = array();
				$update_data_array = array();
				foreach($this->request->data['office_id'] as $key => $val)
				{
					$BonusTarget = $this->BonusTarget->find('first',array(
						'conditions' => array('type' => 1,'bonus_id' => $id,'office_id' => $this->request->data['office_id'][$key]),
						'recursive' => -1 
					));
					
					if(!empty($BonusTarget))
					{
						$update_data['id'] = $BonusTarget['BonusTarget']['id']; 
						$update_data['target_quantity'] = $this->request->data['target_quantity'][$key]; 
						$update_data['updated_at'] = $this->current_datetime(); 
						$update_data['updated_by'] = $this->UserAuth->getUserId();
						$update_data_array[] = $update_data; 
					}else{
						$insert_data['bonus_id'] = $id; 
						$insert_data['type'] = 1; 
						$insert_data['office_id'] = $val; 
						$insert_data['target_quantity'] = $this->request->data['target_quantity'][$key]; 
						$insert_data['created_at'] = $this->current_datetime(); 
						$insert_data['created_by'] = $this->UserAuth->getUserId();
						$insert_data_array[] = $insert_data; 
					}					
				}
				
				// data insert
				$this->BonusTarget->saveAll($insert_data_array);
				// data update
				$this->BonusTarget->saveAll($update_data_array);
				
				$this->Session->setFlash(__('The Bonus target has been saved'), 'flash/success');
				$this->redirect(array('action' => 'bonus_target/'.$id));
				
			}else{
				$this->Session->setFlash(__('Bonus target not saved'), 'flash/error');
				$this->redirect(array('action' => 'bonus_target/'.$id));
			}	
			
		} else {
			
			$office_list = $this->Office->find('all',array(
				'joins' => array(
					array(
						'alias' => 'BonusTarget',
						'table' => 'bonus_targets',
						'type' => 'LEFT',
						'conditions' => array('Office.id = BonusTarget.office_id','BonusTarget.type=1','BonusTarget.bonus_id='.$id)
					)
				),
				'conditions' => array('office_type_id' => 2),
				'fields' => array('Office.*','BonusTarget.id','BonusTarget.target_quantity'),
				'order' => array('Office.office_name' => 'asc'),
				'recursive' => -1 
			));
			$this->set(compact('office_list'));
		}		
	}
	
	
	public function admin_territory_bonus_target($bonus_target_id = null,$office_id = '') {
		
		$this->set('page_title','Territory Bonus Target Configuration');
        $this->loadModel('Territory');
        $this->loadModel('BonusTarget');		
		

		if ($this->request->is('post') || $this->request->is('put')) {
			
			if(!empty($this->request->data['territory_id']))
			{	
				$insert_data_array = array();
				$update_data_array = array();
				foreach($this->request->data['territory_id'] as $key => $val)
				{
					$BonusTarget = $this->BonusTarget->find('first',array(
						'conditions' => array('type' => 2,'bonus_id' => $bonus_target_id,'office_id' => $this->request->data['office_id'][$key],'territory_id' => $val),
						'recursive' => -1 
					));
					
					if(!empty($BonusTarget))
					{
						$update_data['id'] = $BonusTarget['BonusTarget']['id']; 
						$update_data['target_quantity'] = $this->request->data['target_quantity'][$key]; 
						$update_data['updated_at'] = $this->current_datetime(); 
						$update_data['updated_by'] = $this->UserAuth->getUserId();
						$update_data_array[] = $update_data; 
					}else{
						$insert_data['bonus_id'] = $bonus_target_id; 
						$insert_data['type'] = 2; 
						$insert_data['office_id'] = $this->request->data['office_id'][$key]; 
						$insert_data['territory_id'] = $val; 
						$insert_data['target_quantity'] = $this->request->data['target_quantity'][$key]; 
						$insert_data['created_at'] = $this->current_datetime(); 
						$insert_data['created_by'] = $this->UserAuth->getUserId();
						$insert_data_array[] = $insert_data; 
					}					
				}
				
				// data insert
				$this->BonusTarget->saveAll($insert_data_array);
				// data update
				$this->BonusTarget->saveAll($update_data_array);
				
				$this->Session->setFlash(__('The Bonus target has been saved'), 'flash/success');
				$this->redirect(array('action' => 'territory_bonus_target/'.$bonus_target_id.'/'.$office_id));
				
			}else{
				$this->Session->setFlash(__('Bonus target not saved'), 'flash/error');
				$this->redirect(array('action' => 'territory_bonus_target/'.$bonus_target_id.'/'.$office_id));
			}	
			
		} else {
		
			$OfficeBonusTarget = $this->BonusTarget->find('first',array(
						'conditions' => array('BonusTarget.bonus_id' => $bonus_target_id,'BonusTarget.office_id' => $office_id,'BonusTarget.type' => 1),
						'recursive' => 0 
					));	
			if(empty($OfficeBonusTarget))
			{
				$this->Session->setFlash(__('Bonus target not configured yet.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}				
			
			$territory_list = $this->Territory->find('all',array(
				'joins' => array(
					array(
						'alias' => 'BonusTarget',
						'table' => 'bonus_targets',
						'type' => 'LEFT',
						'conditions' => array('Territory.id = BonusTarget.territory_id','BonusTarget.type = 2','BonusTarget.bonus_id = '.$bonus_target_id)
					),
					array(
						'alias' => 'SalesPerson',
						'table' => 'sales_people',
						'type' => 'LEFT',
						'conditions' => array('Territory.id = SalesPerson.territory_id')
						)
				),
				'conditions' => array('Territory.office_id' => $office_id),
				'fields' => array('Territory.*','BonusTarget.target_quantity','SalesPerson.name'),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => -1 
			));
			//$all_territory=array_column(array_column($territory_list,'Territory'),'id');
			$this->set(compact('territory_list','OfficeBonusTarget'));	
			
		}	
	}
	function get_giving_qty($bonus_id)
	{
		$territory_id =$this->request->data['territory_id'];
		//$this->loadModel('BonusTarget');
		$bonus_qty=$this->Bonus->find('all',array(
			'conditions'=>array('Bonus.id'=>$bonus_id,'MemoDetail.price'=>'0.0','MemoDetail.is_bonus'=>1,'Memo.territory_id'=>$territory_id),
			'joins'=>array(
				array(
					'table'=>'memo_details',
					'alias'=>'MemoDetail',
					'conditions'=>'MemoDetail.product_id=Bonus.bonus_product_id'
					),
				array(
					'table'=>'memos',
					'alias'=>'Memo',
					'conditions'=>'Memo.id=MemoDetail.memo_id'
					)
				),
			'fields'=>array('SUM(MemoDetail.sales_qty) as giving_qty'),
			'group'=>array('Bonus.id'),
			'recursive'=>-1
			));
		if( $bonus_qty)
		{
			echo $bonus_qty[0][0]['giving_qty'];
		}
		else
		{
			echo 0;
		}
		$this->autoRender=false;
	}

	function get_area_giving_qty($bonus_id)
	{
		$this->LoadModel('Territory');
		$office_id =$this->request->data['office_id'];
		$territory_id=array_keys($this->Territory->find('list',array('conditions'=>array('Territory.office_id'=>$office_id))));
		//$this->loadModel('BonusTarget');
		$bonus_qty=$this->Bonus->find('all',array(
			'conditions'=>array('Bonus.id'=>$bonus_id,'MemoDetail.price'=>'0.0','MemoDetail.is_bonus'=>1,'Memo.territory_id'=>$territory_id),
			'joins'=>array(
				array(
					'table'=>'memo_details',
					'alias'=>'MemoDetail',
					'conditions'=>'MemoDetail.product_id=Bonus.bonus_product_id'
					),
				array(
					'table'=>'memos',
					'alias'=>'Memo',
					'conditions'=>'Memo.id=MemoDetail.memo_id'
					)
				),
			'fields'=>array('SUM(MemoDetail.sales_qty) as giving_qty'),
			'group'=>array('Bonus.id'),
			'recursive'=>-1
			));
		// echo $this->Bonus->getLastQuery();
		if( $bonus_qty)
		{
			echo $bonus_qty[0][0]['giving_qty'];
		}
		else
		{
			echo 0;
		}
		$this->autoRender=false;
	}
	
}
