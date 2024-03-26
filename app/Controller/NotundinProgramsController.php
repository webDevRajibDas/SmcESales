<?php
App::uses('AppController', 'Controller');
/**
 * BonusCards Controller
 *
 * @property BonusCard $BonusCard
 * @property PaginatorComponent $Paginator
 */

class NotundinProgramsController extends AppController {

/**
 * Components
 *
 * @var array
 */
    public $uses = array('NotundinProgram','Institute');
	public $components = array('Paginator', 'Filter.Filter');
	
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() 
	{		
		$this->set('page_title','Notundin Program List');
		
		$status = array(
			'1' => 'Assigned',
			'2' => 'De-Assigned'
		);
		$this->set('status', $status);
		
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		
		//pr($program_conditions);
		
		$this->NotundinProgram->recursive = 0;
		$this->paginate = array(
			//'conditions' => $program_conditions,
			'order'=>   array('id' => 'desc')   
		);
		//pr($this->paginate());
		//exit;
		$this->set('programs', $this->paginate());
		
	}
 


/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() 
	{
		$this->set('page_title','Add Notundin Program');
		
		$institutes = $this->Institute->find('all', array(
			'conditions' => array(
				'Institute.type' => 1,
				'Institute.is_active' => 1,
				
			),	
			'fields' => array('Institute.id','Institute.name','Institute.address','Institute.contactname','Institute.short_name'),
			'order'=>   array('Institute.name' => 'asc')   ,
			'recursive' => -1,
		));
		//pr($institutes);
		//exit;	
		$this->set('institutes', $institutes);
		
		$request_data = array();
		if ($this->request->is('post')) 
		{						
			$request_data = $this->request->data;	
		}
		
		$this->set('request_data', $request_data);
				
		$user_id = $this->UserAuth->getUserId();
				
		$this->set(compact('user_id'));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add_list() {
		
		$this->loadModel('NotundinProgram');
			
		$institutes = $this->Institute->find('all',array(
				'conditions' => array(
					'Institute.type' => 1,
					'Institute.is_active' => 1,
					
				),	
				'fields' => array('Institute.id','Institute.name','Institute.address','Institute.contactname','Institute.short_name'),
				'order'=>   array('Institute.name' => 'asc')   ,
				'recursive' => -1,
			));

		$institute_ids = $this->request->data['institute_id'];
		if(empty($institute_ids))
		{
			$institute_ids = array();
		}	

		$delete_data_array = array();
		$data_array = array();
		$update_data_array = array();
		$del = array();
		
		foreach($institutes as $outl)
		{
			$val = $outl['Institute']['id'];
			if(in_array($val, $institute_ids))
			{					
				if(!empty($this->request->data['program_id'][$val]))
				{
					$udata['id'] = $this->request->data['program_id'][$val];
					$udata['assigned_date'] =  $this->request->data['assigned_date'][$val]?date('Y-m-d', strtotime($this->request->data['assigned_date'][$val])):date('Y-m-d');
					$udata['deassigned_date'] = NULL;
					$udata['status'] = 1;
					$udata['updated_at'] = $this->current_datetime();
					$udata['updated_by'] = $this->UserAuth->getUserId();
					$update_data_array[] = $udata;
				}
				else
				{
					$data['institute_id'] = $val;
					$data['assigned_date'] =  $this->request->data['assigned_date'][$val]?date('Y-m-d', strtotime($this->request->data['assigned_date'][$val])):date('Y-m-d');
					$data['deassigned_date'] = NULL;
					$data['status'] = 1;
					$data['created_at'] = $this->current_datetime();
					$data['created_by'] = $this->UserAuth->getUserId();
					$data['updated_at'] = $this->current_datetime();
					$data['updated_by'] = $this->UserAuth->getUserId();
					$data_array[] = $data;
				}
			}
			else
			{
				if(!empty($this->request->data['program_id'][$val]))
				{
					$del[] = $this->request->data['program_id'][$val];				
				}				
			}					
		}	
		
		if(!empty($del))
		$this->NotundinProgram->deleteAll(array('id' => $del), false); 		//	delete data		
		
		$this->NotundinProgram->saveAll($update_data_array); 		//  update data	
		$this->NotundinProgram->saveAll($data_array);  				// insert data

		$this->Session->setFlash(__('The program has been saved'), 'flash/success');
		$this->redirect(array('action' => 'index'));				
				
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
        $this->set('page_title','Deassigned Notundin Institute');
		$this->NotundinProgram->id = $id;
		if (!$this->NotundinProgram->exists($id)) {
			throw new NotFoundException(__('Invalid Program!'));
		}
		
		//Start reasons
		$sql = "SELECT * FROM program_reasons";
		$query_datas = $this->NotundinProgram->query($sql);
		$reasons = array();
		foreach($query_datas as $query_data){
			$reasons[$query_data[0]['name']] = $query_data[0]['name'];
		}
		//pr($reasons);
		//exit;
		
		$this->set(compact('reasons'));
		/*$reasons = array(
			'Misconduct' 				=> 'Misconduct',
			'Poor Performance' 			=> 'Poor Performance',
			'Stealing' 					=> 'Stealing',
			'Taking Too Much Time Off' 	=> 'Taking Too Much Time Off',
			'Violating Company Policy' 	=> 'Violating Company Policy',
			'Damaging Company Property' => 'Damaging Company Property',
		);
		$this->set(compact('reasons'));*/
		//End for reasons
		
		
		if ($this->request->is('post') || $this->request->is('put')) {
			//$this->request->data['NotundinProgram']['officer_id'] = NULL;
			//$this->request->data['NotundinProgram']['member_type'] = NULL;
			//$this->request->data['NotundinProgram']['code'] = NULL;
			//$this->request->data['NotundinProgram']['assigned_date'] =  NULL;
			$this->request->data['NotundinProgram']['status'] = 2;
			$this->request->data['NotundinProgram']['deassigned_date'] = date('Y-m-d',strtotime($this->request->data['NotundinProgram']['deassigned_date']));
			$this->request->data['NotundinProgram']['updated_at'] = $this->current_datetime();
			$this->request->data['NotundinProgram']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['NotundinProgram']['deassigned_by'] = $this->UserAuth->getUserId();
			if ($this->NotundinProgram->save($this->request->data)) {
				$this->Session->setFlash(__('The Notundin program institute has been deassigned.'), 'flash/success');
				//$this->redirect(array('action' => 'notundin_programs'));
				$this->redirect(array('action' => 'index'));
			}
		}
		else 
		{
			$options = array('conditions' => array('NotundinProgram.' . $this->NotundinProgram->primaryKey => $id));
			$this->request->data = $this->NotundinProgram->find('first', $options);
		}		
	}

/**
 * bsp_program_list method
 *
 * @return void
 */
	
	public function get_program_info($institute_id=0)
	{
		//pr($request_data);
		$this->loadModel('NotundinProgram');
		$conditions = array(
		'NotundinProgram.institute_id' 		=> $institute_id,
		'NotundinProgram.status' 			=> 1
		);
		
		//pr($conditions);
		
		$program_info = $this->NotundinProgram->find('first',array(
			'conditions' => $conditions, 	
			//'order' => array('Thana.name'=>'ASC'),
			'recursive' => -1
		));
		//pr($program_info);
		//exit;
		
		return $program_info;
		
		//$this->autoRender = false;
   }
	
}
