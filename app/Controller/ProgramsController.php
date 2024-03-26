<?php
App::uses('AppController', 'Controller');
/**
 * BonusCards Controller
 *
 * @property BonusCard $BonusCard
 * @property PaginatorComponent $Paginator
 */
class ProgramsController extends AppController{
	
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
	}


	/**
	 * admin_pchp_program_list method
	 *
	 * @return void
	 */

	//PCHP Program
	public function admin_pchp_program_list(){
	
		$this->set('page_title', 'Program List');
		$status = array(
			'1' => 'Assigned',
			'2' => 'De-Assigned'
		);
		$this->set('status', $status);

		$office_parent_id = $this->UserAuth->getOfficeParentId();

		if ($office_parent_id == 0) {
			$program_conditions = array(
				'Program.program_type_id' => 1,
				/*array(
						'OR' => array(
							array('Program.officer_id' => $this->UserAuth->getUserId()),
							array('Program.deassigned_by' => $this->UserAuth->getUserId())
						)
					)*/
			);
			$office_conditions = array();
		} else {
			$program_conditions = array(
				'Program.program_type_id' => 1,
				'Territory.office_id' => $this->UserAuth->getOfficeId(),
				array(
					'OR' => array(
						array('Program.officer_id' => $this->UserAuth->getOfficeId()),
						array('Program.deassigned_by' => $this->UserAuth->getUserId())
					)
				)
			);

			if ($office_parent_id == 14) {
				$office_conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
			} else {
				$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			}

			//$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());

		}

		//pr($program_conditions);exit;

		$this->Program->recursive = 0;
		// $this->Program->unbindModel(array('belongsTo'=>array('Territory')));
		$ddd = $this->Program->virtualFields = array(
			'program_officer' => 'SalesPerson.name',
			'market_name' => 'Market.name',
			'market_thana_id' => 'Market.thana_id'
		);
		// pr($ddd );exit;
		$this->paginate = array(
			'conditions' => $program_conditions,
			'joins' => array(
				array(
					'alias' => 'Market',
					'table' => 'markets',
					'type' => 'INNER',
					'conditions' => 'Outlet.market_id=Market.id',
				),
				array(
					'alias' => 'Territory',
					'table' => 'territories',
					'type' => 'INNER',
					'conditions' => 'Territory.id=Market.territory_id',
				),
				array(
					'alias' => 'SalesPerson',
					'table' => 'sales_people',
					'type' => 'LEFT',
					'conditions' => 'SalesPerson.id=Program.program_officer_id',
				)
			),
			'fields' => array('Outlet.*', 'Program.*', 'Office.*', 'Doctor.*', 'SalesPerson.code', 'Territory.name', 'Territory.id', 'Market.id', 'Market.name', 'Market.thana_id'),
			'order' =>   array('Program.id' => 'asc')
		);
		 //pr($this->paginate());exit;
		//$this->dd($this->paginate = array());exit;
		$this->set('programs', $this->paginate());

		$this->loadModel('Office');
		$this->loadModel('Territory');
		$this->loadModel('Market');

		$office_conditions['office_type_id'] = 2;
		$office_conditions['NOT'] = array("id" => array(30, 31, 37));


		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array
		('office_name' => 'asc')));
		if ($office_parent_id) {
			$this->request->data['Program']['office_id'] = $this->UserAuth->getOfficeId();
		}
		$office_id = (isset($this->request->data['Program']['office_id']) ? $this->request->data['Program']['office_id'] : 0);
		$territories = $this->Territory->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));
		$territory_id = (isset($this->request->data['Program']['territory_id']) ? $this->request->data['Program']['territory_id'] : 0);

		//for thana list
		$thanas = array();
		if ($territory_id) {
			$this->loadModel('ThanaTerritory');
			$conditions = array('ThanaTerritory.territory_id' => $territory_id);

			$rs = array(array('id' => '', 'name' => '---- Select -----'));
			$thana_lists = $this->ThanaTerritory->find('all', array(
				'conditions' => $conditions,
				//'order' => array('Thana.name'=>'ASC'),
				'recursive' => 1
			));

			foreach ($thana_lists as $thana_info) {
				$thanas[$thana_info['Thana']['id']] = $thana_info['Thana']['name'];
			}
		}

		$thana_id = (isset($this->request->data['Program']['thana_id']) ? $this->request->data['Program']['thana_id'] : 0);
		$markets = $this->Market->find('list', array(
			'conditions' => array('Market.thana_id' => $thana_id),
			'order' => array('Market.name' => 'ASC'),
			'recursive' => 0
		));

		$this->set(compact('markets', 'offices', 'territories', 'thanas','office_id'));
	}

	/**
	 * admin_add method
	 */
	public function admin_update_officer_id(){
        if ($this->request->is('post')) {
            //$this->dd($this->request->data);exit();
            $poid = $this->request->data['program_officer_id'];
            $pid = $this->request->data['program_id'];
            $this->loadModel('Program');
            $data = array('id' => $pid, 'program_officer_id' => $poid);
            $this->Program->save($data);
            $message ='Program Officer Assign Successful';
            echo json_encode(['messege' =>$message]);
            $this->autoRender = false;
        }
        //$semiRandomArticle = $this->Program->find('first');
//        $lastCreated = $this->Program->find('first', array(
//            'order' => array('Program.created_at' => 'desc'),
//            'recursive' => -1,
//        ));
//        $specificallyThisOne = $this->Program->find('first', array(
//            'fields' => array('Program.id'),
//            'recursive' => -1,
//            'conditions' => array('Program.id' => $this->request->data['program_id'])
//        ));
        //$this->dd($specificallyThisOne);exit();



	}

	/**
	 * admin_add method
	 */
	public function admin_add_pchp()
	{
		//$this->dd('add_pchp');exit();

		$this->set('page_title', 'Add PCHP Program');
		$this->loadModel('Outlet');

		/*$outlets = array();
		if ($this->request->is('post')) 
		{
			//pr($this->request->data);
			$this->Outlet->recursive = 0;
			$outlets = $this->Outlet->find('all',array(
				'conditions' => array(
					'Outlet.market_id' => $this->request->data['Program']['market_id'],
					'Outlet.is_pharma_type' => 1,
					array(
						'OR' => array(
							array('Program.officer_id' => ''),
							array('Program.officer_id' => $this->request->data['Program']['office_id'])
						)
					),
					array(
						'OR' => array(
							array('Program.program_type_id' => ''),
							array('Program.program_type_id' => 1)
						)
					)
				),	
				'fields' => array(
				'Outlet.id','Outlet.code','Outlet.name','Outlet.in_charge','Outlet.ownar_name','Program.*'),
				'order'=>   array('Outlet.id' => 'desc')   
			));	
			
			
			//pr($outlets);
			//exit;*
					
		}
		$outlets->set('outlets', $outlets);*/

		$outlets = array();
		$request_data = array();
		if ($this->request->is('post')) {
			//$this->dd($request_data = $this->request->data);exit();
			$outlets = $this->Outlet->find('all', array(
				'conditions' => array(
					'Outlet.market_id' => $this->request->data['Program']['market_id'],
					'Outlet.is_pharma_type' => 1,
				),
				'fields' => array(
					'Outlet.id', 'Outlet.code', 'Outlet.name', 'Outlet.in_charge', 'Outlet.ownar_name'
				),
				'order' =>   array('Outlet.id' => 'desc'),
				'recursive' => -1,
			));
			//pr($outlets);
			//exit;	

			$request_data = $this->request->data;
		}
		$this->set('outlets', $outlets);
		$this->set('request_data', $request_data);



		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$conditions = array();
		} else {

			if ($office_parent_id == 14) {
				$conditions = array(
					'Office.parent_office_id' => $this->UserAuth->getOfficeId()
				);
			} else {
				$conditions = array(
					'Office.id' => $this->UserAuth->getOfficeId()
				);
			}
		}

		$this->loadModel('Office');
		$this->loadModel('Territory');


		$conditions['office_type_id'] = 2;
		$conditions['NOT'] = array("id" => array(30, 31, 37));
		$offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));

		$office_id = (isset($this->request->data['Program']['office_id']) ? $this->request->data['Program']['office_id'] : 0);

		$territories = $this->Territory->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));


		$territory_id = (isset($this->request->data['Program']['territory_id']) ? $this->request->data['Program']['territory_id'] : 0);


		//for thana list
		$thanas = array();
		if ($territory_id) {
			$this->loadModel('ThanaTerritory');
			$conditions = array('ThanaTerritory.territory_id' => $territory_id);

			$rs = array(array('id' => '', 'name' => '---- Select -----'));
			$thana_lists = $this->ThanaTerritory->find('all', array(
				'conditions' => $conditions,
				//'order' => array('Thana.name'=>'ASC'),
				'recursive' => 1
			));

			foreach ($thana_lists as $thana_info) {
				$thanas[$thana_info['Thana']['id']] = $thana_info['Thana']['name'];
			}
		}


		$thana_id = (isset($this->request->data['Program']['thana_id']) ? $this->request->data['Program']['thana_id'] : 0);

		$markets = $this->Outlet->Market->find('list', array(
			'conditions' => array('Market.thana_id' => $thana_id),
			'order' => array('Market.name' => 'ASC'),
			'recursive' => 0
		));


		$user_id = $this->UserAuth->getUserId();
		$this->set(compact('markets', 'offices', 'territories', 'user_id', 'thanas'));

		//$this->dd('add_pchp');exit();
	}

	/**
	 * admin_add method
	 */
	public function admin_add_pchp_list(){
		//$this->dd($this->request->data);exit();
		$this->loadModel('Outlet');
		$this->Outlet->recursive = 0;
		$outlets = $this->Outlet->find('all', array(
			'conditions' => array('Outlet.market_id' => $this->request->data['market_id']),
			'fields' => array('Outlet.id', 'Program.id'),
			'order' =>  array('Outlet.id' => 'desc')
		));
		
		$outlet_ids = $this->request->data['outlet_id'];
		if (empty($outlet_ids)) {
			$outlet_ids = array();
		}
		
		
		$delete_data_array = array();
		$data_array = array();
		$update_data_array = array();
		$del = array();
		
		$this->loadModel('Territory');
		$territory_info = $this->Territory->find('first', array(
			'conditions' => array('Territory.id' => $this->request->data['territory_id']),
			'fields' => array('Territory.id', 'Office.id'),
		));
		
		//pr($territory_info);
		$office_id = $territory_info['Office']['id'];
		//$this->dd($office_id);exit();
		
		//exit;
		foreach ($outlets as $outl) {
			$val = $outl['Outlet']['id'];
			if (in_array($val, $outlet_ids)) {
					
				if (!empty($this->request->data['program_id'][$val])) {
					//echo"Update data";exit();	
					$udata['id'] = $this->request->data['program_id'][$val];
					$udata['program_type_id'] = 1;
					$udata['officer_id'] = $office_id;
					$udata['member_type'] = @$this->request->data['member_type'][$val] ? $this->request->data['member_type'][$val] : NULL;
					$udata['code'] = $this->request->data['code'][$val];
					$udata['assigned_date'] =  $this->request->data['assigned_date'][$val] ? date('Y-m-d', strtotime($this->request->data['assigned_date'][$val])) : date('Y-m-d');
					$udata['deassigned_date'] = NULL;
					$udata['status'] = 1;
					$udata['updated_at'] = $this->current_datetime();
					$udata['updated_by'] = $this->UserAuth->getUserId();
					$update_data_array[] = $udata;
				} else {
					
					//echo"insert data";exit();
					$data['territory_id'] = $this->request->data['territory_id'];
					$data['market_id'] = $this->request->data['market_id'];
					$data['outlet_id'] = $val;
					$data['program_type_id'] = 1;
					$data['officer_id'] = $office_id;
					$data['member_type'] = @$this->request->data['member_type'][$val];
					$data['code'] = $this->request->data['code'][$val];
					//$data['assigned_date'] =  date('Y-m-d',strtotime($this->request->data['assigned_date'][$val]));
					$data['assigned_date'] =  $this->request->data['assigned_date'][$val] ? date('Y-m-d', strtotime($this->request->data['assigned_date'][$val])) : date('Y-m-d');
					$data['deassigned_date'] = NULL;
					$data['status'] = 1;
					$data['created_at'] = $this->current_datetime();
					$data['created_by'] = $this->UserAuth->getUserId();
					$data['updated_at'] = $this->current_datetime();
					$data['updated_by'] = $this->UserAuth->getUserId();
					$data_array[] = $data;
				}

				$this->Outlet->id = $val;
				if ($this->Outlet->id) {
					$this->Outlet->saveField('is_within_group', 1);
				}
			} else {
				/*$this->Outlet->id = $val;
				if ($this->Outlet->id) {
					$this->Outlet->saveField('is_within_group', 0);
				}*/

				if (!empty($this->request->data['program_id'][$val])) {
					$del[] = $this->request->data['program_id'][$val];
				}
			}
		}


		//$this->dd($del);exit();
		if (!empty($del))
			$this->Program->deleteAll(array('id' => $del), false); 	//delete data		

		$this->Program->saveAll($update_data_array); //  update data	
		$this->Program->saveAll($data_array);  	// insert data

		$this->Session->setFlash(__('The program has been saved'), 'flash/success');
		$this->redirect(array('action' => 'pchp_program_list'));
	}

	/**
	 * admin_edit method
	 */
	public function admin_edit_pchp($id = null){
		$this->set('page_title', 'Edit PCHP');
		$this->Program->id = $id;
		if (!$this->Program->exists($id)) {
			throw new NotFoundException(__('Invalid bonus card'));
		}


		//Start reasons
		$sql = "SELECT * FROM program_reasons";
		$query_datas = $this->Program->query($sql);
		$reasons = array();
		foreach ($query_datas as $query_data) {
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


			//$this->request->data['Program']['member_type'] = NULL;
			//$this->request->data['Program']['code'] = NULL;
			//$this->request->data['Program']['assigned_date'] =  NULL;
			$this->request->data['Program']['status'] = 2;
			$this->request->data['Program']['deassigned_date'] = date('Y-m-d', strtotime($this->request->data['Program']['deassigned_date']));
			$this->request->data['Program']['updated_at'] = $this->current_datetime();
			$this->request->data['Program']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['Program']['deassigned_by'] = $this->UserAuth->getUserId();
			if ($this->Program->save($this->request->data)) {

				//for outlet update
				$this->loadModel('Outlet');
				$outlet_id = $this->request->data['Program']['outlet_id'];
				$this->Outlet->id = $outlet_id;
				if ($this->Outlet->id) {
					$this->Outlet->saveField('is_within_group', 0);
				}

				$this->Session->setFlash(__('The PCHP program outlet has been deassigned.'), 'flash/success');
				$this->redirect(array('action' => 'pchp_program_list'));
			}
		} else {
			$options = array('conditions' => array('Program.' . $this->Program->primaryKey => $id));
			$this->request->data = $this->Program->find('first', $options);
		}
	}



	//BSP Program
	public function admin_bsp_program_list(){
		$this->set('page_title', 'Program List');

		$status = array(
			'1' => 'Assigned',
			'2' => 'De-Assigned'
		);
		$this->set('status', $status);

		$office_parent_id = $this->UserAuth->getOfficeParentId();

		if ($office_parent_id == 0) {
			$program_conditions = array(
				'Program.program_type_id' => 2,
				/*array(
						'OR' => array(
							array('Program.officer_id' => $this->UserAuth->getUserId()),
							array('Program.deassigned_by' => $this->UserAuth->getUserId())
						)
					)*/
			);
			$office_conditions = array();
		} else {
			$program_conditions = array(
				'Program.program_type_id' => 2,
				'Territory.office_id' => $this->UserAuth->getOfficeId(),
				array(
					'OR' => array(
						array('Program.officer_id' => $this->UserAuth->getUserId()),
						array('Program.deassigned_by' => $this->UserAuth->getUserId())
					)
				)
			);
			//$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			if ($office_parent_id == 14) {
				$office_conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
			} else {
				$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			}
		}

		$this->Program->recursive = 0;
		// $this->Program->unbindModel(array('belongsTo'=>array('Territory')));
		$this->Program->virtualFields = array(
			'program_officer' => 'SalesPerson.name',
			'market_name' => 'Market.name',
			'market_thana_id' => 'Market.thana_id'
		);
		$this->paginate = array(
			'conditions' => $program_conditions,
			'joins' => array(
				array(
					'alias' => 'Market',
					'table' => 'markets',
					'type' => 'INNER',
					'conditions' => 'Outlet.market_id=Market.id',
				),
				array(
					'alias' => 'Territory',
					'table' => 'territories',
					'type' => 'INNER',
					'conditions' => 'Territory.id=Market.territory_id',
				),
				array(
					'alias' => 'SalesPerson',
					'table' => 'sales_people',
					'type' => 'LEFT',
					'conditions' => 'SalesPerson.id=Program.program_officer_id',
				)
			),
			'fields' => array('Outlet.*', 'Program.*', 'Office.*', 'Doctor.*', 'Territory.name', 'Territory.id', 'Market.id', 'Market.name', 'Market.thana_id'),
			'order' =>   array('Program.id' => 'desc')
		);
		$this->set('programs', $this->paginate());


		$this->loadModel('Office');
		$this->loadModel('Territory');
		$this->loadModel('Market');

		$office_conditions['office_type_id'] = 2;
		$office_conditions['NOT'] = array("id" => array(30, 31, 37));

		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		$office_id = (isset($this->request->data['Program']['office_id']) ? $this->request->data['Program']['office_id'] : 0);
		$territories = $this->Territory->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));

		$territory_id = (isset($this->request->data['Program']['territory_id']) ? $this->request->data['Program']['territory_id'] : 0);


		//for thana list
		$thanas = array();
		if ($territory_id) {
			$this->loadModel('ThanaTerritory');
			$conditions = array('ThanaTerritory.territory_id' => $territory_id);

			$rs = array(array('id' => '', 'name' => '---- Select -----'));
			$thana_lists = $this->ThanaTerritory->find('all', array(
				'conditions' => $conditions,
				//'order' => array('Thana.name'=>'ASC'),
				'recursive' => 1
			));

			foreach ($thana_lists as $thana_info) {
				$thanas[$thana_info['Thana']['id']] = $thana_info['Thana']['name'];
			}
		}

		$thana_id = (isset($this->request->data['Program']['thana_id']) ? $this->request->data['Program']['thana_id'] : 0);
		$markets = $this->Market->find('list', array(
			'conditions' => array('Market.thana_id' => $thana_id),
			'order' => array('Market.name' => 'ASC'),
			'recursive' => 0
		));
		//Get Proggram officer
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		//$this->dd($office_parent_id);
		$program_officer_tags = array();
		if ($office_parent_id == 0) {
			$office_conditions = array(
				'office_type_id' => 2,
				"NOT" => array("id" => array(30, 31, 37))
			);
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			$program_officer_tags['SalesPerson.office_id'] = 1016;
		}
		$program_officer_tags['User.user_group_id'] = 1016; // 1016=program officer user group id
		$this->set('office_id', $this->UserAuth->getOfficeId());
		$this->loadModel('Office');
		$this->loadModel('SalesPerson');
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		$ProgramOfficers = $this->SalesPerson->find('list', array(
			'conditions' => $program_officer_tags,
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'User',
					'conditions' => 'User.sales_person_id=SalesPerson.id'
				)
			)
		));

		$this->set(compact('markets', 'offices', 'territories', 'thanas','ProgramOfficers'));
	}


	/**
	 * admin_add_bsp method
	 */
	public function admin_add_bsp()
	{
		$this->set('page_title', 'Add BSP Program');
		$this->loadModel('Doctor');
		$this->loadModel('Outlet');

		/*$doctors = array();
		if ($this->request->is('post')) 
		{			
			$this->Doctor->recursive = 0;
			$doctors = $this->Doctor->find('all',array(
				'conditions' => array(
					'Doctor.market_id' => $this->request->data['Program']['market_id'],
					'Outlet.is_pharma_type' => 1				
				),	
				//'fields' => array('Outlet.id','Outlet.code','Outlet.name','Outlet.in_charge','Outlet.ownar_name','Program.*'),
				'recursive' => 0,
				'order'=>   array('Doctor.name' => 'asc')   
			));	
			//pr($doctors);		
		}
		$this->set('doctors', $doctors);*/


		$doctors = array();
		$request_data = array();
		if ($this->request->is('post')) {
			/*$sql = "SELECT d.*, dt.title, o.name as outlet_name, o.id as outlet_id FROM doctors d 
			INNER JOIN outlets o ON(d.outlet_id=o.id)
			INNER JOIN doctor_types dt ON(d.doctor_type_id=dt.id) 
			WHERE d.market_id='".$this->request->data['Program']['market_id']."' AND o.is_pharma_type=1";*/


			$sql = "SELECT d.*, dt.title, o.name as outlet_name, o.id as outlet_id FROM outlets o 
			LEFT JOIN doctors d ON(o.id=d.outlet_id)
			LEFT JOIN doctor_types dt ON(d.doctor_type_id=dt.id) 
			WHERE o.market_id='" . $this->request->data['Program']['market_id'] . "' AND o.is_pharma_type=1";
			$doctors = $this->Doctor->query($sql);

			/*$outlets = $this->Outlet->find('all',array(
				'conditions' => array(
					'Outlet.market_id' => $this->request->data['Program']['market_id'],
					'Outlet.is_pharma_type' => 1,
				),	
				'fields' => array(
				'Outlet.id','Outlet.code','Outlet.name','Outlet.in_charge','Outlet.ownar_name'),
				'order'=>   array('Outlet.id' => 'desc')   ,
				'recursive' => -1,
			));*/
			//pr($doctors);
			//exit;	

			$request_data = $this->request->data;
		}
		$this->set('doctors', $doctors);
		$this->set('request_data', $request_data);


		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$conditions = array();
		} else {

			if ($office_parent_id == 14) {
				$conditions = array(
					'Office.parent_office_id' => $this->UserAuth->getOfficeId()
				);
			} else {
				$conditions = array(
					'Office.id' => $this->UserAuth->getOfficeId()
				);
			}
		}

		$this->loadModel('Office');
		$this->loadModel('Territory');
		$this->loadModel('Market');

		$conditions['office_type_id'] = 2;
		$conditions['NOT'] = array("id" => array(30, 31, 37));
		$offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));

		$office_id = (isset($this->request->data['Program']['office_id']) ? $this->request->data['Program']['office_id'] : 0);

		$territories = $this->Territory->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));

		$territory_id = (isset($this->request->data['Program']['territory_id']) ? $this->request->data['Program']['territory_id'] : 0);

		//for thana list
		$thanas = array();
		if ($territory_id) {
			$this->loadModel('ThanaTerritory');
			$conditions = array('ThanaTerritory.territory_id' => $territory_id);

			$rs = array(array('id' => '', 'name' => '---- Select -----'));
			$thana_lists = $this->ThanaTerritory->find('all', array(
				'conditions' => $conditions,
				//'order' => array('Thana.name'=>'ASC'),
				'recursive' => 1
			));

			foreach ($thana_lists as $thana_info) {
				$thanas[$thana_info['Thana']['id']] = $thana_info['Thana']['name'];
			}
		}

		$thana_id = (isset($this->request->data['Program']['thana_id']) ? $this->request->data['Program']['thana_id'] : 0);

		$markets = $this->Market->find('list', array(
			'conditions' => array('Market.thana_id' => $thana_id),
			'order' => array('Market.name' => 'ASC'),
			'recursive' => 0
		));

		//pr($markets);
		//exit;


		$user_id = $this->UserAuth->getUserId();
		$this->set(compact('markets', 'offices', 'territories', 'user_id', 'thanas'));
	}

	/**
	 * admin_add_bsp_list method
	 */
	public function admin_add_bsp_list()
	{
		$this->loadModel('Doctor');
		$this->loadModel('Outlet');
		/*$this->Doctor->recursive = 0;
		$doctors = $this->Doctor->find('all',array(
			'conditions' => array('Doctor.market_id' => $this->request->data['market_id']),
			'fields' => array('Doctor.id','Program.id'),
			'order'=>   array('Doctor.id' => 'desc')   
		));	*/

		/*$sql = "SELECT d.*, dt.title, o.name as outlet_name, o.id as outlet_id FROM doctors d 
			INNER JOIN outlets o ON(d.outlet_id=o.id)
			INNER JOIN doctor_types dt ON(d.doctor_type_id=dt.id) 
			WHERE d.market_id='".$this->request->data['market_id']."' AND o.is_pharma_type=1";
		$doctors = $this->Doctor->query($sql);*/

		$sql = "SELECT d.*, dt.title, o.name as outlet_name, o.id as outlet_id FROM outlets o 
			LEFT JOIN doctors d ON(o.id=d.outlet_id)
			LEFT JOIN doctor_types dt ON(d.doctor_type_id=dt.id) 
			WHERE o.market_id='" . $this->request->data['market_id'] . "' AND o.is_pharma_type=1";
		$doctors = $this->Doctor->query($sql);

		$doctor_ids = $this->request->data['outlet_id'];
		if (empty($doctor_ids)) {
			$doctor_ids = array();
		}

		$delete_data_array = array();
		$data_array = array();
		$update_data_array = array();
		$del = array();

		$this->loadModel('Territory');
		$territory_info = $this->Territory->find('first', array(
			'conditions' => array('Territory.id' => $this->request->data['territory_id']),
			'fields' => array('Territory.id', 'Office.id'),
		));
		//pr($territory_info);
		$office_id = $territory_info['Office']['id'];
		//exit;
		



		foreach ($doctors as $outl) {
			$val = $outl[0]['outlet_id'];
			$doctor_id = $outl[0]['id'];
			if (in_array($val, $doctor_ids)) {
				if (!empty($this->request->data['program_id'][$val])) {
					$udata['id'] = $this->request->data['program_id'][$val];
					$udata['outlet_id'] = $this->request->data['outlet_id'][$val];
					$udata['program_type_id'] = 2;
					$data['doctor_id'] = $doctor_id;
					//$udata['officer_id'] = $this->UserAuth->getUserId();
					//$udata['officer_id'] = $office_id;
					$udata['code'] = $this->request->data['code'][$val];
					//$udata['assigned_date'] =  date('Y-m-d',strtotime($this->request->data['assigned_date'][$val]));
					$udata['assigned_date'] =  $this->request->data['assigned_date'][$val] ? date('Y-m-d', strtotime($this->request->data['assigned_date'][$val])) : date('Y-m-d');
					$udata['deassigned_date'] = NULL;
					$udata['status'] = 1;
					$udata['updated_at'] = $this->current_datetime();
					$udata['updated_by'] = $this->UserAuth->getUserId();
					$update_data_array[] = $udata;
				} else {
					$data['territory_id'] = $this->request->data['territory_id'];
					$data['market_id'] = $this->request->data['market_id'];
					$data['outlet_id'] = $this->request->data['outlet_id'][$val];
					$data['doctor_id'] = $doctor_id;
					$data['program_type_id'] = 2;
					$data['officer_id'] = $office_id;
					$data['code'] = $this->request->data['code'][$val];
					//$data['assigned_date'] =  date('Y-m-d',strtotime($this->request->data['assigned_date'][$val]));
					$data['assigned_date'] =  $this->request->data['assigned_date'][$val] ? date('Y-m-d', strtotime($this->request->data['assigned_date'][$val])) : date('Y-m-d');
					$data['deassigned_date'] = NULL;
					$data['status'] = 1;
					$data['created_at'] = $this->current_datetime();
					$data['created_by'] = $this->UserAuth->getUserId();
					$data['updated_at'] = $this->current_datetime();
					$data['updated_by'] = $this->UserAuth->getUserId();
					$data_array[] = $data;
				}

				$this->Outlet->id = $val;
				if ($this->Outlet->id) {
					$this->Outlet->saveField('is_within_group', 1);
				}
			} else {
				/*$this->Outlet->id = $val;
				if ($this->Outlet->id) {
					$this->Outlet->saveField('is_within_group', 0);
				}*/

				if (!empty($this->request->data['program_id'][$val])) {
					$del[] = $this->request->data['program_id'][$val];
				}
			}
		}
		
		
		if (!empty($del))
			$this->Program->deleteAll(array('id' => $del), false); 		//	delete data		

		$this->Program->saveAll($update_data_array); 		//  update data	
		$this->Program->saveAll($data_array);  				// insert data

		$this->Session->setFlash(__('The BSP program has been saved'), 'flash/success');
		$this->redirect(array('action' => 'bsp_program_list'));
	}

	/**
	 * admin_edit method
	 */
	public function admin_edit_bsp($id = null)
	{
		$this->set('page_title', 'Edit BSP');
		$this->Program->id = $id;
		if (!$this->Program->exists($id)) {
			throw new NotFoundException(__('Invalid '));
		}

		//Start reasons
		$sql = "SELECT * FROM program_reasons";
		$query_datas = $this->Program->query($sql);
		$reasons = array();
		foreach ($query_datas as $query_data) {
			$reasons[$query_data[0]['name']] = $query_data[0]['name'];
		}
		//pr($reasons);
		//exit;

		$this->set(compact('reasons'));
		//End for reasons

		if ($this->request->is('post') || $this->request->is('put')) {
			//$this->request->data['Program']['officer_id'] = NULL;
			//$this->request->data['Program']['code'] = NULL;
			//$this->request->data['Program']['assigned_date'] =  NULL;
			$this->request->data['Program']['status'] = 2;
			$this->request->data['Program']['deassigned_date'] = date('Y-m-d', strtotime($this->request->data['Program']['deassigned_date']));
			$this->request->data['Program']['updated_at'] = $this->current_datetime();
			$this->request->data['Program']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['Program']['deassigned_by'] = $this->UserAuth->getUserId();

			//pr($this->request->data);
			//exit;

			if ($this->Program->save($this->request->data)) {

				//for outlet update
				$this->loadModel('Outlet');
				$outlet_id = $this->request->data['Program']['outlet_id'];
				$this->Outlet->id = $outlet_id;
				if ($this->Outlet->id) {
					$this->Outlet->saveField('is_within_group', 0);
				}

				$this->Session->setFlash(__('The BSP program outlet has been deassigned.'), 'flash/success');
				$this->redirect(array('action' => 'bsp_program_list'));
			}
		} else {
			$options = array('conditions' => array('Program.' . $this->Program->primaryKey => $id));
			$this->request->data = $this->Program->find('first', $options);
		}
	}








	//LARC Program
	public function admin_larc_program_list()
	{
		$this->set('page_title', 'LARC Program List');

		$status = array(
			'1' => 'Assigned',
			'2' => 'De-Assigned'
		);
		$this->set('status', $status);

		$office_parent_id = $this->UserAuth->getOfficeParentId();

		if ($office_parent_id == 0) {
			$program_conditions = array(
				'Program.program_type_id' => 3,
				/*array(
						'OR' => array(
							array('Program.officer_id' => $this->UserAuth->getUserId()),
							array('Program.deassigned_by' => $this->UserAuth->getUserId())
						)
					)*/
			);
			$office_conditions = array();
		} else {
			$program_conditions = array(
				'Program.program_type_id' => 3,
				'Territory.office_id' => $this->UserAuth->getOfficeId(),
				array(
					'OR' => array(
						array('Program.officer_id' => $this->UserAuth->getUserId()),
						array('Program.deassigned_by' => $this->UserAuth->getUserId())
					)
				)
			);
			if ($office_parent_id == 14) {
				$office_conditions = array(
					'Office.parent_office_id' => $this->UserAuth->getOfficeId()
				);
			} else {
				$office_conditions = array(
					'Office.id' => $this->UserAuth->getOfficeId()
				);
			}
			//$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}

		$this->Program->recursive = 0;
		$this->Program->virtualFields = array(
			'program_officer' => 'SalesPerson.name'
		);
		// $this->Program->unbindModel(array('belongsTo'=>array('Territory')));
		$this->Program->virtualFields = array(
			'program_officer' => 'SalesPerson.name',
			'market_name' => 'Market.name',
			'market_thana_id' => 'Market.thana_id'
		);
		$this->paginate = array(
			'conditions' => $program_conditions,
			'joins' => array(
				array(
					'alias' => 'Market',
					'table' => 'markets',
					'type' => 'INNER',
					'conditions' => 'Outlet.market_id=Market.id',
				),
				array(
					'alias' => 'Territory',
					'table' => 'territories',
					'type' => 'INNER',
					'conditions' => 'Territory.id=Market.territory_id',
				),
				array(
					'alias' => 'SalesPerson',
					'table' => 'sales_people',
					'type' => 'LEFT',
					'conditions' => 'SalesPerson.id=Program.program_officer_id',
				)
			),
			'fields' => array('Outlet.*', 'Program.*', 'Office.*', 'Doctor.*', 'Territory.name', 'Territory.id', 'Market.id', 'Market.name', 'Market.thana_id'),
			'order' =>   array('Program.id' => 'desc')
		);
		$this->set('programs', $this->paginate());


		$this->loadModel('Office');
		$this->loadModel('Territory');
		$this->loadModel('Market');

		$office_conditions['office_type_id'] = 2;
		$office_conditions['NOT'] = array("id" => array(30, 31, 37));

		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		$office_id = (isset($this->request->data['Program']['office_id']) ? $this->request->data['Program']['office_id'] : 0);
		$territories = $this->Territory->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));

		$territory_id = (isset($this->request->data['Program']['territory_id']) ? $this->request->data['Program']['territory_id'] : 0);
		$markets = $this->Market->find('list', array(
			'conditions' => array('Market.territory_id' => $territory_id),
			'order' => array('Market.name' => 'ASC'),
			'recursive' => 0
		));

		$this->set(compact('markets', 'offices', 'territories'));
	}


	/**
	 * admin_add_larc method
	 */
	public function admin_add_larc()
	{
		$this->set('page_title', 'Add LARC Program');
		$this->loadModel('Doctor');

		/*$doctors = array();
		if ($this->request->is('post')) {
			
			$this->Doctor->recursive = 0;
			$doctors = $this->Doctor->find('all',array(
				'conditions' => array(
					'Doctor.market_id' => $this->request->data['Program']['market_id']					
				),	
				//'fields' => array('Outlet.id','Outlet.code','Outlet.name','Outlet.in_charge','Outlet.ownar_name','Program.*'),
				'order'=>   array('Outlet.id' => 'desc')   
			));			
		}
		$this->set('doctors', $doctors);*/


		$doctors = array();
		$request_data = array();
		if ($this->request->is('post')) {
			//$this->Doctor->recursive = 0;
			/*$doctors = $this->Doctor->find('all',array(
				'conditions' => array(
					'Doctor.market_id' => $this->request->data['Program']['market_id'],
					'Outlet.is_pharma_type' => 1,
					
								
				),	
				//'fields' => array('Outlet.id','Outlet.code','Outlet.name','Outlet.in_charge','Outlet.ownar_name','Program.*'),
				'recursive' => 0,
				'order'=>   array('Doctor.name' => 'asc')   
			));*/

			/*$sql = "SELECT d.*, dt.title, o.name as outlet_name, o.id as outlet_id FROM doctors d 
			INNER JOIN outlets o ON(d.outlet_id=o.id)
			INNER JOIN doctor_types dt ON(d.doctor_type_id=dt.id) 
			WHERE d.market_id='".$this->request->data['Program']['market_id']."' AND o.is_pharma_type=1";
			$doctors = $this->Doctor->query($sql);*/

			$sql = "SELECT d.*, dt.title, o.name as outlet_name, o.id as outlet_id FROM outlets o 
			LEFT JOIN doctors d ON(o.id=d.outlet_id)
			LEFT JOIN doctor_types dt ON(d.doctor_type_id=dt.id) 
			WHERE o.market_id='" . $this->request->data['Program']['market_id'] . "' AND o.is_pharma_type=1";
			$doctors = $this->Doctor->query($sql);
			//pr($doctors);
			//exit;	

			$request_data = $this->request->data;
		}
		$this->set('doctors', $doctors);
		$this->set('request_data', $request_data);



		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$conditions = array();
		} else {

			if ($office_parent_id == 14) {
				$conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
			} else {
				$conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			}
		}

		$this->loadModel('Office');
		$this->loadModel('Territory');
		$this->loadModel('Market');

		$conditions['office_type_id'] = 2;
		$conditions['NOT'] = array("id" => array(30, 31, 37));
		$offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));

		$office_id = (isset($this->request->data['Program']['office_id']) ? $this->request->data['Program']['office_id'] : 0);
		$territories = $this->Territory->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));

		$territory_id = (isset($this->request->data['Program']['territory_id']) ? $this->request->data['Program']['territory_id'] : 0);

		//for thana list
		$thanas = array();
		if ($territory_id) {
			$this->loadModel('ThanaTerritory');
			$conditions = array('ThanaTerritory.territory_id' => $territory_id);

			$rs = array(array('id' => '', 'name' => '---- Select -----'));
			$thana_lists = $this->ThanaTerritory->find('all', array(
				'conditions' => $conditions,
				//'order' => array('Thana.name'=>'ASC'),
				'recursive' => 1
			));

			foreach ($thana_lists as $thana_info) {
				$thanas[$thana_info['Thana']['id']] = $thana_info['Thana']['name'];
			}
		}

		$thana_id = (isset($this->request->data['Program']['thana_id']) ? $this->request->data['Program']['thana_id'] : 0);

		$markets = $this->Market->find('list', array(
			'conditions' => array('Market.thana_id' => $thana_id),
			'order' => array('Market.name' => 'ASC'),
			'recursive' => 0
		));

		$user_id = $this->UserAuth->getUserId();
		$this->set(compact('markets', 'offices', 'territories', 'user_id', 'thanas'));
	}

	/**
	 * admin_add_larc_list method
	 */
	public function admin_add_larc_list()
	{
		$this->loadModel('Doctor');
		$this->loadModel('Outlet');

		/*$this->Doctor->recursive = 0;
		$doctors = $this->Doctor->find('all',array(
			'conditions' => array('Doctor.market_id' => $this->request->data['market_id']),
			'fields' => array('Doctor.id','Program.id'),
			'order'=>   array('Doctor.id' => 'desc')   
		));	*/
		/*$sql = "SELECT d.*, dt.title, o.name as outlet_name, o.id as outlet_id FROM doctors d 
			INNER JOIN outlets o ON(d.outlet_id=o.id)
			INNER JOIN doctor_types dt ON(d.doctor_type_id=dt.id) 
			WHERE d.market_id='".$this->request->data['market_id']."' AND o.is_pharma_type=1";
			$doctors = $this->Doctor->query($sql);*/
		//pr($doctors);

		$sql = "SELECT d.*, dt.title, o.name as outlet_name, o.id as outlet_id FROM outlets o 
			LEFT JOIN doctors d ON(o.id=d.outlet_id)
			LEFT JOIN doctor_types dt ON(d.doctor_type_id=dt.id) 
			WHERE o.market_id='" . $this->request->data['market_id'] . "' AND o.is_pharma_type=1";
		$doctors = $this->Doctor->query($sql);

		$doctor_ids = $this->request->data['outlet_id'];
		if (empty($doctor_ids)) {
			$doctor_ids = array();
		}

		//pr($doctor_ids);

		$delete_data_array = array();
		$data_array = array();
		$update_data_array = array();
		$del = array();


		$this->loadModel('Territory');
		$territory_info = $this->Territory->find('first', array(
			'conditions' => array('Territory.id' => $this->request->data['territory_id']),
			'fields' => array('Territory.id', 'Office.id'),
		));
		//pr($territory_info);
		$office_id = $territory_info['Office']['id'];
		//pr($this->request->data);
		//exit;

		foreach ($doctors as $outl) {
			$val = $outl[0]['outlet_id'];
			$doctor_id = $outl[0]['id'];
			if (in_array($val, $doctor_ids)) {
				if (!empty($this->request->data['program_id'][$val])) {
					$udata['id'] = $this->request->data['program_id'][$val];
					$udata['outlet_id'] = $this->request->data['outlet_id'][$val];
					$udata['program_type_id'] = 3;
					$data['doctor_id'] = $doctor_id;
					//$udata['officer_id'] = $this->UserAuth->getUserId();
					$udata['officer_id'] = $office_id;
					$udata['code'] = $this->request->data['code'][$val];
					//$udata['assigned_date'] =  date('Y-m-d',strtotime($this->request->data['assigned_date'][$val]));
					$udata['assigned_date'] =  $this->request->data['assigned_date'][$val] ? date('Y-m-d', strtotime($this->request->data['assigned_date'][$val])) : date('Y-m-d');
					$udata['deassigned_date'] = NULL;
					$udata['status'] = 1;
					$udata['updated_at'] = $this->current_datetime();
					$udata['updated_by'] = $this->UserAuth->getUserId();
					$update_data_array[] = $udata;
				} else {
					$data['territory_id'] = $this->request->data['territory_id'];
					$data['market_id'] = $this->request->data['market_id'];
					$data['outlet_id'] = $this->request->data['outlet_id'][$val];
					$data['doctor_id'] = $doctor_id;
					$data['program_type_id'] = 3;
					$data['officer_id'] = $office_id;
					$data['code'] = $this->request->data['code'][$val];
					//$data['assigned_date'] =  date('Y-m-d',strtotime($this->request->data['assigned_date'][$val]));
					$data['assigned_date'] =  $this->request->data['assigned_date'][$val] ? date('Y-m-d', strtotime($this->request->data['assigned_date'][$val])) : date('Y-m-d');
					$data['deassigned_date'] = NULL;
					$data['status'] = 1;
					$data['created_at'] = $this->current_datetime();
					$data['created_by'] = $this->UserAuth->getUserId();
					$data['updated_at'] = $this->current_datetime();
					$data['updated_by'] = $this->UserAuth->getUserId();
					$data_array[] = $data;
				}
				//echo $val.'<br>';
				$this->Outlet->id = $val;
				if ($this->Outlet->id) {
					$this->Outlet->saveField('is_within_group', 1);
				}
			} else {
				/*$this->Outlet->id = $val;
				if ($this->Outlet->id) {
					$this->Outlet->saveField('is_within_group', 0);
				}*/

				if (!empty($this->request->data['program_id'][$val])) {
					$del[] = $this->request->data['program_id'][$val];
				}
			}
		}

		//pr($del);
		//exit;	

		if (!empty($del))
			$this->Program->deleteAll(array('id' => $del), false); 		//	delete data		

		$this->Program->saveAll($update_data_array); 		//  update data	


		//pr($data_array);
		//exit;
		$this->Program->saveAll($data_array);  				// insert data

		$this->Session->setFlash(__('The LARC program has been saved'), 'flash/success');
		$this->redirect(array('action' => 'larc_program_list'));
	}

	/**
	 * admin_edit method
	 */
	public function admin_edit_larc($id = null)
	{
		$this->set('page_title', 'Edit LARC');
		$this->Program->id = $id;
		if (!$this->Program->exists($id)) {
			throw new NotFoundException(__('Invalid '));
		}

		//Start reasons
		$sql = "SELECT * FROM program_reasons";
		$query_datas = $this->Program->query($sql);
		$reasons = array();
		foreach ($query_datas as $query_data) {
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
			//$this->request->data['Program']['officer_id'] = NULL;
			//$this->request->data['Program']['code'] = NULL;
			//$this->request->data['Program']['assigned_date'] =  NULL;
			$this->request->data['Program']['status'] = 2;
			$this->request->data['Program']['deassigned_date'] = date('Y-m-d', strtotime($this->request->data['Program']['deassigned_date']));
			$this->request->data['Program']['updated_at'] = $this->current_datetime();
			$this->request->data['Program']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['Program']['deassigned_by'] = $this->UserAuth->getUserId();
			if ($this->Program->save($this->request->data)) {

				//for outlet update
				$this->loadModel('Outlet');
				$outlet_id = $this->request->data['Program']['outlet_id'];
				$this->Outlet->id = $outlet_id;
				if ($this->Outlet->id) {
					$this->Outlet->saveField('is_within_group', 0);
				}

				$this->Session->setFlash(__('The LARC program outlet has been deassigned.'), 'flash/success');
				$this->redirect(array('action' => 'larc_program_list'));
			}
		} else {
			$options = array('conditions' => array('Program.' . $this->Program->primaryKey => $id));
			$this->request->data = $this->Program->find('first', $options);
		}
	}




	//Injecttive
	public function admin_injective_program_list()
	{

		$this->set('page_title', 'Program List');

		$status = array(
			'1' => 'Assigned',
			'2' => 'De-Assigned'
		);
		$this->set('status', $status);

		$office_parent_id = $this->UserAuth->getOfficeParentId();

		if ($office_parent_id == 0) {
			$program_conditions = array(
				'Program.program_type_id' => 4,
				/*array(
						'OR' => array(
							array('Program.officer_id' => $this->UserAuth->getUserId()),
							array('Program.deassigned_by' => $this->UserAuth->getUserId())
						)
					)*/
			);
			$office_conditions = array();
		} else {
			$program_conditions = array(
				'Program.program_type_id' => 4,
				'Territory.office_id' => $this->UserAuth->getOfficeId(),
				array(
					'OR' => array(
						array('Program.officer_id' => $this->UserAuth->getOfficeId()),
						array('Program.deassigned_by' => $this->UserAuth->getUserId())
					)
				)
			);
			//$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			if ($office_parent_id == 14) {
				$office_conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
			} else {
				$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			}
		}

		//pr($program_conditions);


		$this->Program->recursive = 0;
		$this->Program->virtualFields = array(
			'program_officer' => 'SalesPerson.name',
			'market_name' => 'Market.name',
			'market_thana_id' => 'Market.thana_id'
		);
		$this->paginate = array(
			'conditions' => $program_conditions,
			'joins' => array(
				array(
					'alias' => 'Market',
					'table' => 'markets',
					'type' => 'INNER',
					'conditions' => 'Outlet.market_id=Market.id',
				),
				array(
					'alias' => 'Territory',
					'table' => 'territories',
					'type' => 'INNER',
					'conditions' => 'Territory.id=Market.territory_id',
				),
				array(
					'alias' => 'SalesPerson',
					'table' => 'sales_people',
					'type' => 'LEFT',
					'conditions' => 'SalesPerson.id=Program.program_officer_id',
				)
			),
			'fields' => array('Outlet.*', 'Program.*', 'Office.*', 'Doctor.*', 'Territory.name', 'Territory.id', 'Market.id', 'Market.name', 'Market.thana_id'),
			'order' =>   array('Program.id' => 'desc')
		);
		// pr($this->paginate());exit;
		$this->set('programs', $this->paginate());


		$this->loadModel('Office');
		$this->loadModel('Territory');
		$this->loadModel('Market');



		$office_conditions['office_type_id'] = 2;
		$office_conditions['NOT'] = array("id" => array(30, 31, 37));

		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		if ($office_parent_id) {
			$this->request->data['Program']['office_id'] = $this->UserAuth->getOfficeId();
		}
		$office_id = (isset($this->request->data['Program']['office_id']) ? $this->request->data['Program']['office_id'] : 0);
		$territories = $this->Territory->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));

		$territory_id = (isset($this->request->data['Program']['territory_id']) ? $this->request->data['Program']['territory_id'] : 0);



		//for thana list
		$thanas = array();
		if ($territory_id) {
			$this->loadModel('ThanaTerritory');
			$conditions = array('ThanaTerritory.territory_id' => $territory_id);

			$rs = array(array('id' => '', 'name' => '---- Select -----'));
			$thana_lists = $this->ThanaTerritory->find('all', array(
				'conditions' => $conditions,
				//'order' => array('Thana.name'=>'ASC'),
				'recursive' => 1
			));

			foreach ($thana_lists as $thana_info) {
				$thanas[$thana_info['Thana']['id']] = $thana_info['Thana']['name'];
			}
		}

		$thana_id = (isset($this->request->data['Program']['thana_id']) ? $this->request->data['Program']['thana_id'] : 0);
		$markets = $this->Market->find('list', array(
			'conditions' => array('Market.thana_id' => $thana_id),
			'order' => array('Market.name' => 'ASC'),
			'recursive' => 0
		));


		$this->set(compact('markets', 'offices', 'territories', 'thanas'));
	}

	/**
	 * admin_add method
	 */
	public function admin_add_injective()
	{
		$this->set('page_title', 'Add Stockist Injective');
		$this->loadModel('Outlet');

		$outlets = array();
		$request_data = array();
		if ($this->request->is('post')) {
			$outlets = $this->Outlet->find('all', array(
				'conditions' => array(
					'Outlet.market_id' => $this->request->data['Program']['market_id'],
					//'Outlet.is_pharma_type' => 1,
				),
				'fields' => array(
					'Outlet.id', 'Outlet.code', 'Outlet.name', 'Outlet.in_charge', 'Outlet.ownar_name'
				),
				'order' =>   array('Outlet.id' => 'desc'),
				'recursive' => -1,
			));
			//pr($outlets);
			//exit;	

			$request_data = $this->request->data;
		}
		$this->set('outlets', $outlets);
		$this->set('request_data', $request_data);



		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$conditions = array();
		} else {

			if ($office_parent_id == 14) {
				$conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
			} else {
				$conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			}
		}

		$this->loadModel('Office');
		$this->loadModel('Territory');


		$conditions['office_type_id'] = 2;
		$conditions['NOT'] = array("id" => array(30, 31, 37));
		$offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));

		$office_id = (isset($this->request->data['Program']['office_id']) ? $this->request->data['Program']['office_id'] : 0);

		$territories = $this->Territory->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));


		$territory_id = (isset($this->request->data['Program']['territory_id']) ? $this->request->data['Program']['territory_id'] : 0);


		//for thana list
		$thanas = array();
		if ($territory_id) {
			$this->loadModel('ThanaTerritory');
			$conditions = array('ThanaTerritory.territory_id' => $territory_id);

			$rs = array(array('id' => '', 'name' => '---- Select -----'));
			$thana_lists = $this->ThanaTerritory->find('all', array(
				'conditions' => $conditions,
				//'order' => array('Thana.name'=>'ASC'),
				'recursive' => 1
			));

			foreach ($thana_lists as $thana_info) {
				$thanas[$thana_info['Thana']['id']] = $thana_info['Thana']['name'];
			}
		}


		$thana_id = (isset($this->request->data['Program']['thana_id']) ? $this->request->data['Program']['thana_id'] : 0);

		$markets = $this->Outlet->Market->find('list', array(
			'conditions' => array('Market.thana_id' => $thana_id),
			'order' => array('Market.name' => 'ASC'),
			'recursive' => 0
		));

		$user_id = $this->UserAuth->getUserId();
		$this->set(compact('markets', 'offices', 'territories', 'user_id', 'thanas'));
	}

	/**
	 * admin_add method
	 */
	public function admin_add_injective_list()
	{
		$this->loadModel('Outlet');

		$this->Outlet->recursive = 0;
		$outlets = $this->Outlet->find('all', array(
			'conditions' => array('Outlet.market_id' => $this->request->data['market_id']),
			'fields' => array('Outlet.id', 'Program.id'),
			'order' =>   array('Outlet.id' => 'desc')
		));

		$outlet_ids = $this->request->data['outlet_id'];
		if (empty($outlet_ids)) {
			$outlet_ids = array();
		}

		$delete_data_array = array();
		$data_array = array();
		$update_data_array = array();
		$del = array();

		$this->loadModel('Territory');
		$territory_info = $this->Territory->find('first', array(
			'conditions' => array('Territory.id' => $this->request->data['territory_id']),
			'fields' => array('Territory.id', 'Office.id'),
		));
		//pr($territory_info);
		$office_id = $territory_info['Office']['id'];
		//pr($this->request->data);
		//exit;
		foreach ($outlets as $outl) {
			$val = $outl['Outlet']['id'];
			if (in_array($val, $outlet_ids)) {
				if (!empty($this->request->data['program_id'][$val])) {
					$udata['id'] = $this->request->data['program_id'][$val];
					$udata['program_type_id'] = 4;
					$udata['officer_id'] = $office_id;
					$udata['member_type'] = @$this->request->data['member_type'][$val] ? $this->request->data['member_type'][$val] : NULL;
					$udata['code'] = $this->request->data['code'][$val];
					$udata['assigned_date'] =  $this->request->data['assigned_date'][$val] ? date('Y-m-d', strtotime($this->request->data['assigned_date'][$val])) : date('Y-m-d');
					$udata['deassigned_date'] = NULL;
					$udata['status'] = 1;
					$udata['updated_at'] = $this->current_datetime();
					$udata['updated_by'] = $this->UserAuth->getUserId();
					$update_data_array[] = $udata;
				} else {
					$data['territory_id'] = $this->request->data['territory_id'];
					$data['market_id'] = $this->request->data['market_id'];
					$data['outlet_id'] = $val;
					$data['program_type_id'] = 4;
					$data['officer_id'] = $office_id;
					$data['member_type'] = @$this->request->data['member_type'][$val];
					$data['code'] = $this->request->data['code'][$val];
					//$data['assigned_date'] =  date('Y-m-d',strtotime($this->request->data['assigned_date'][$val]));
					$data['assigned_date'] =  $this->request->data['assigned_date'][$val] ? date('Y-m-d', strtotime($this->request->data['assigned_date'][$val])) : date('Y-m-d');
					$data['deassigned_date'] = NULL;
					$data['status'] = 1;
					$data['created_at'] = $this->current_datetime();
					$data['created_by'] = $this->UserAuth->getUserId();
					$data['updated_at'] = $this->current_datetime();
					$data['updated_by'] = $this->UserAuth->getUserId();
					$data_array[] = $data;
				}

				$this->Outlet->id = $val;
				if ($this->Outlet->id) {
					$this->Outlet->saveField('is_within_group', 1);
				}
			} else {
				/*$this->Outlet->id = $val;
				if ($this->Outlet->id) {
					$this->Outlet->saveField('is_within_group', 0);
				}*/

				if (!empty($this->request->data['program_id'][$val])) {
					$del[] = $this->request->data['program_id'][$val];
				}
			}
		}

		if (!empty($del))
			$this->Program->deleteAll(array('id' => $del), false); 		//	delete data		

		$this->Program->saveAll($update_data_array); 		//  update data	
		$this->Program->saveAll($data_array);  				// insert data

		$this->Session->setFlash(__('The program has been saved'), 'flash/success');
		$this->redirect(array('action' => 'injective_program_list'));
	}

	/**
	 * admin_edit method
	 */
	public function admin_edit_injective($id = null)
	{
		$this->set('page_title', 'Edit PCHP');
		$this->Program->id = $id;
		if (!$this->Program->exists($id)) {
			throw new NotFoundException(__('Invalid bonus card'));
		}


		//Start reasons
		$sql = "SELECT * FROM program_reasons";
		$query_datas = $this->Program->query($sql);
		$reasons = array();
		foreach ($query_datas as $query_data) {
			$reasons[$query_data[0]['name']] = $query_data[0]['name'];
		}
		//pr($reasons);
		//exit;

		$this->set(compact('reasons'));

		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Program']['status'] = 2;
			$this->request->data['Program']['deassigned_date'] = date('Y-m-d', strtotime($this->request->data['Program']['deassigned_date']));
			$this->request->data['Program']['updated_at'] = $this->current_datetime();
			$this->request->data['Program']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['Program']['deassigned_by'] = $this->UserAuth->getUserId();
			if ($this->Program->save($this->request->data)) {

				//for outlet update
				$this->loadModel('Outlet');
				$outlet_id = $this->request->data['Program']['outlet_id'];
				$this->Outlet->id = $outlet_id;
				if ($this->Outlet->id) {
					$this->Outlet->saveField('is_within_group', 0);
				}

				$this->Session->setFlash(__('The PCHP program outlet has been deassigned.'), 'flash/success');
				$this->redirect(array('action' => 'injective_program_list'));
			}
		} else {
			$options = array('conditions' => array('Program.' . $this->Program->primaryKey => $id));
			$this->request->data = $this->Program->find('first', $options);
		}
	}



	//NGO Injecttive
	public function admin_ngo_injective_program_list()
	{

		$this->set('page_title', 'Program List');

		$status = array(
			'1' => 'Assigned',
			'2' => 'De-Assigned'
		);
		$this->set('status', $status);

		$office_parent_id = $this->UserAuth->getOfficeParentId();

		if ($office_parent_id == 0) {
			$program_conditions = array(
				'Program.program_type_id' => 5,
				/*array(
						'OR' => array(
							array('Program.officer_id' => $this->UserAuth->getUserId()),
							array('Program.deassigned_by' => $this->UserAuth->getUserId())
						)
					)*/
			);
			$office_conditions = array();
		} else {
			$program_conditions = array(
				'Program.program_type_id' => 5,
				'Territory.office_id' => $this->UserAuth->getOfficeId(),
				array(
					'OR' => array(
						array('Program.officer_id' => $this->UserAuth->getOfficeId()),
						array('Program.deassigned_by' => $this->UserAuth->getUserId())
					)
				)
			);
			if ($office_parent_id == 14) {
				$office_conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
			} else {
				$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			}
		}

		//pr($program_conditions);

		$this->Program->recursive = 0;
		$this->Program->virtualFields = array(
			'program_officer' => 'SalesPerson.name',
			'market_name' => 'Market.name',
			'market_thana_id' => 'Market.thana_id'
		);
		$this->paginate = array(
			'conditions' => $program_conditions,
			'joins' => array(
				array(
					'alias' => 'Market',
					'table' => 'markets',
					'type' => 'INNER',
					'conditions' => 'Outlet.market_id=Market.id',
				),
				array(
					'alias' => 'Territory',
					'table' => 'territories',
					'type' => 'INNER',
					'conditions' => 'Territory.id=Market.territory_id',
				),
				array(
					'alias' => 'SalesPerson',
					'table' => 'sales_people',
					'type' => 'LEFT',
					'conditions' => 'SalesPerson.id=Program.program_officer_id',
				)
			),
			'fields' => array('Outlet.*', 'Program.*', 'Office.*', 'Doctor.*', 'Territory.name', 'Territory.id', 'Market.id', 'Market.name', 'Market.thana_id'),
			'order' =>   array('Program.id' => 'desc')
		);
		//pr($this->paginate());
		$this->set('programs', $this->paginate());


		$this->loadModel('Office');
		$this->loadModel('Territory');
		$this->loadModel('Market');



		$office_conditions['office_type_id'] = 2;
		$office_conditions['NOT'] = array("id" => array(30, 31, 37));

		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		if ($office_parent_id) {
			$this->request->data['Program']['office_id'] = $this->UserAuth->getOfficeId();
		}
		$office_id = (isset($this->request->data['Program']['office_id']) ? $this->request->data['Program']['office_id'] : 0);
		$territories = $this->Territory->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));

		$territory_id = (isset($this->request->data['Program']['territory_id']) ? $this->request->data['Program']['territory_id'] : 0);



		//for thana list
		$thanas = array();
		if ($territory_id) {
			$this->loadModel('ThanaTerritory');
			$conditions = array('ThanaTerritory.territory_id' => $territory_id);

			$rs = array(array('id' => '', 'name' => '---- Select -----'));
			$thana_lists = $this->ThanaTerritory->find('all', array(
				'conditions' => $conditions,
				//'order' => array('Thana.name'=>'ASC'),
				'recursive' => 1
			));

			foreach ($thana_lists as $thana_info) {
				$thanas[$thana_info['Thana']['id']] = $thana_info['Thana']['name'];
			}
		}

		$thana_id = (isset($this->request->data['Program']['thana_id']) ? $this->request->data['Program']['thana_id'] : 0);
		$markets = $this->Market->find('list', array(
			'conditions' => array('Market.thana_id' => $thana_id),
			'order' => array('Market.name' => 'ASC'),
			'recursive' => 0
		));


		$this->set(compact('markets', 'offices', 'territories', 'thanas'));
	}

	/**
	 * admin_add method
	 */
	public function admin_add_ngo_injective()
	{
		$this->set('page_title', 'Add Stockist Injective');
		$this->loadModel('Outlet');

		$outlets = array();
		$request_data = array();
		if ($this->request->is('post')) {
			$con = array(
				//'Outlet.market_id' => $this->request->data['Program']['market_id'],
				'Outlet.institute_id >' => 0,
			);

			//if($this->request->data['Program']['territory_id'])$con['Outlet.territory_id']=$this->request->data['Program']['territory_id'];

			if ($this->request->data['Program']['thana_id']) $con['Market.thana_id'] = $this->request->data['Program']['thana_id'];
			if ($this->request->data['Program']['market_id']) $con['Outlet.market_id'] = $this->request->data['Program']['market_id'];

			$outlets = $this->Outlet->find('all', array(
				'conditions' => $con,
				'joins' => array(
					array(
						'alias' => 'Market',
						'table' => 'markets',
						'type' => 'INNER',
						'conditions' => 'Outlet.market_id = Market.id'
					)
				),
				'fields' => array(
					'Outlet.id', 'Outlet.code', 'Outlet.name', 'Outlet.in_charge', 'Outlet.ownar_name'
				),
				'order' =>   array('Outlet.id' => 'desc'),
				'recursive' => -1,
			));
			//pr($outlets);
			//exit;	

			$request_data = $this->request->data;
		}
		$this->set('outlets', $outlets);
		$this->set('request_data', $request_data);



		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$conditions = array();
		} else {


			if ($office_parent_id == 14) {
				$conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
			} else {
				$conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			}
		}

		$this->loadModel('Office');
		$this->loadModel('Territory');


		$conditions['office_type_id'] = 2;
		$conditions['NOT'] = array("id" => array(30, 31, 37));
		$offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));

		$office_id = (isset($this->request->data['Program']['office_id']) ? $this->request->data['Program']['office_id'] : 0);

		$territories = $this->Territory->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));


		$territory_id = (isset($this->request->data['Program']['territory_id']) ? $this->request->data['Program']['territory_id'] : 0);


		//for thana list
		$thanas = array();
		if ($territory_id) {
			$this->loadModel('ThanaTerritory');
			$conditions = array('ThanaTerritory.territory_id' => $territory_id);

			$rs = array(array('id' => '', 'name' => '---- Select -----'));
			$thana_lists = $this->ThanaTerritory->find('all', array(
				'conditions' => $conditions,
				//'order' => array('Thana.name'=>'ASC'),
				'recursive' => 1
			));

			foreach ($thana_lists as $thana_info) {
				$thanas[$thana_info['Thana']['id']] = $thana_info['Thana']['name'];
			}
		}


		$thana_id = (isset($this->request->data['Program']['thana_id']) ? $this->request->data['Program']['thana_id'] : 0);

		$markets = $this->Outlet->Market->find('list', array(
			'conditions' => array('Market.thana_id' => $thana_id),
			'order' => array('Market.name' => 'ASC'),
			'recursive' => 0
		));





		$user_id = $this->UserAuth->getUserId();
		$this->set(compact('markets', 'offices', 'territories', 'user_id', 'thanas'));
	}

	/**
	 * admin_add method
	 */
	public function admin_add_ngo_injective_list()
	{
		$this->loadModel('Outlet');

		$this->Outlet->recursive = 0;
		$outlets = $this->Outlet->find('all', array(
			'conditions' => array('Outlet.market_id' => $this->request->data['market_id']),
			'fields' => array('Outlet.id', 'Program.id'),
			'order' =>   array('Outlet.id' => 'desc')
		));

		$outlet_ids = $this->request->data['outlet_id'];
		if (empty($outlet_ids)) {
			$outlet_ids = array();
		}

		$delete_data_array = array();
		$data_array = array();
		$update_data_array = array();
		$del = array();

		$this->loadModel('Territory');
		$territory_info = $this->Territory->find('first', array(
			'conditions' => array('Territory.id' => $this->request->data['territory_id']),
			'fields' => array('Territory.id', 'Office.id'),
		));
		//pr($territory_info);
		$office_id = $territory_info['Office']['id'];
		//pr($this->request->data);
		//exit;
		foreach ($outlets as $outl) {
			$val = $outl['Outlet']['id'];
			if (in_array($val, $outlet_ids)) {
				if (!empty($this->request->data['program_id'][$val])) {
					$udata['id'] = $this->request->data['program_id'][$val];
					$udata['program_type_id'] = 5;
					$udata['officer_id'] = $office_id;
					$udata['member_type'] = @$this->request->data['member_type'][$val] ? $this->request->data['member_type'][$val] : NULL;
					$udata['code'] = $this->request->data['code'][$val];
					$udata['assigned_date'] =  $this->request->data['assigned_date'][$val] ? date('Y-m-d', strtotime($this->request->data['assigned_date'][$val])) : date('Y-m-d');
					$udata['deassigned_date'] = NULL;
					$udata['status'] = 1;
					$udata['updated_at'] = $this->current_datetime();
					$udata['updated_by'] = $this->UserAuth->getUserId();
					$update_data_array[] = $udata;
				} else {
					$data['territory_id'] = $this->request->data['territory_id'];
					$data['market_id'] = $this->request->data['market_id'];
					$data['outlet_id'] = $val;
					$data['program_type_id'] = 5;
					$data['officer_id'] = $office_id;
					$data['member_type'] = @$this->request->data['member_type'][$val];
					$data['code'] = $this->request->data['code'][$val];
					//$data['assigned_date'] =  date('Y-m-d',strtotime($this->request->data['assigned_date'][$val]));
					$data['assigned_date'] =  $this->request->data['assigned_date'][$val] ? date('Y-m-d', strtotime($this->request->data['assigned_date'][$val])) : date('Y-m-d');
					$data['deassigned_date'] = NULL;
					$data['status'] = 1;
					$data['created_at'] = $this->current_datetime();
					$data['created_by'] = $this->UserAuth->getUserId();
					$data['updated_at'] = $this->current_datetime();
					$data['updated_by'] = $this->UserAuth->getUserId();
					$data_array[] = $data;
				}

				$this->Outlet->id = $val;
				if ($this->Outlet->id) {
					$this->Outlet->saveField('is_within_group', 1);
				}
			} else {
				/*$this->Outlet->id = $val;
				if ($this->Outlet->id) {
					$this->Outlet->saveField('is_within_group', 0);
				}*/

				if (!empty($this->request->data['program_id'][$val])) {
					$del[] = $this->request->data['program_id'][$val];
				}
			}
		}

		if (!empty($del))
			$this->Program->deleteAll(array('id' => $del), false); 		//	delete data		

		$this->Program->saveAll($update_data_array); 		//  update data	
		$this->Program->saveAll($data_array);  				// insert data

		$this->Session->setFlash(__('The program has been saved'), 'flash/success');
		$this->redirect(array('action' => 'ngo_injective_program_list'));
	}

	/**
	 * admin_edit method
	 */
	public function admin_edit_ngo_injective($id = null)
	{
		$this->set('page_title', 'Edit PCHP');
		$this->Program->id = $id;
		if (!$this->Program->exists($id)) {
			throw new NotFoundException(__('Invalid bonus card'));
		}


		//Start reasons
		$sql = "SELECT * FROM program_reasons";
		$query_datas = $this->Program->query($sql);
		$reasons = array();
		foreach ($query_datas as $query_data) {
			$reasons[$query_data[0]['name']] = $query_data[0]['name'];
		}
		//pr($reasons);
		//exit;

		$this->set(compact('reasons'));

		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Program']['status'] = 2;
			$this->request->data['Program']['deassigned_date'] = date('Y-m-d', strtotime($this->request->data['Program']['deassigned_date']));
			$this->request->data['Program']['updated_at'] = $this->current_datetime();
			$this->request->data['Program']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['Program']['deassigned_by'] = $this->UserAuth->getUserId();
			if ($this->Program->save($this->request->data)) {

				//for outlet update
				$this->loadModel('Outlet');
				$outlet_id = $this->request->data['Program']['outlet_id'];
				$this->Outlet->id = $outlet_id;
				if ($this->Outlet->id) {
					$this->Outlet->saveField('is_within_group', 0);
				}

				$this->Session->setFlash(__('The PCHP program outlet has been deassigned.'), 'flash/success');
				$this->redirect(array('action' => 'ngo_injective_program_list'));
			}
		} else {
			$options = array('conditions' => array('Program.' . $this->Program->primaryKey => $id));
			$this->request->data = $this->Program->find('first', $options);
		}
	}








	public function get_market_list()
	{
		$this->loadModel('Market');
		$thana_id = $this->request->data['thana_id'];
		$location_type_id = $this->request->data['location_type_id'];

		if ($thana_id > 0 and $location_type_id > 0) {
			$conditions = array('thana_id' => $thana_id, 'location_type_id' => $location_type_id);
		} else {
			$conditions = array('thana_id' => $thana_id);
		}

		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		$market_list = $this->Market->find('all', array(
			'conditions' => $conditions,
			'order' => array('Market.name' => 'ASC'),
			'recursive' => -1
		));
		$data_array = Set::extract($market_list, '{n}.Market');
		if (!empty($market_list)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}


	public function get_thana_list()
	{
		$this->loadModel('ThanaTerritory');
		$territory_id = $this->request->data['territory_id'];

		$conditions = array('ThanaTerritory.territory_id' => $territory_id);

		$rs = array(array('id' => '', 'name' => '---- Select -----'));
		$thana_list = $this->ThanaTerritory->find('all', array(
			'conditions' => $conditions,
			//'order' => array('Thana.name'=>'ASC'),
			'recursive' => 1
		));

		//pr($thana_list);

		$data_array = Set::extract($thana_list, '{n}.Thana');
		//pr($data_array);
		//exit;
		if (!empty($thana_list)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}


	public function get_thana_info($id = 0)
	{
		$this->loadModel('Thana');
		$conditions = array('Thana.id' => $id);
		$thana_info = $this->Thana->find('first', array(
			'conditions' => $conditions,
			//'order' => array('Thana.name'=>'ASC'),
			'recursive' => -1
		));
		//pr($thana_info);
		//exit;

		return $thana_info;

		//$this->autoRender = false;
	}


	public function get_program_info($doctor_id = 0, $request_data = array(), $outlet_id = 0, $program_type_id = 0)
	{
		//pr($request_data);exit();
		$this->loadModel('Program');
		$conditions = array(
			//'Program.doctor_id' 		=> $doctor_id,
			//'Program.territory_id' 	=> $request_data['Program']['territory_id'],
			'Program.market_id' 		=> $request_data['Program']['market_id'],
			'Program.outlet_id' 		=> $outlet_id,
			'Program.program_type_id' 	=> $program_type_id,
			'Program.status' 			=> 1
		);
		//pr($conditions);
		if ($doctor_id) $conditions['Program.doctor_id'] = $doctor_id;
		//pr($conditions);
		$program_info = $this->Program->find('first', array(
			'conditions' => $conditions,
			//'order' => array('Thana.name'=>'ASC'),
			'recursive' => -1
		));
		//pr($program_info);exit;
		return $program_info;
		//$this->autoRender = false;
	}

	function gsp_download_xl()
	{
		// pr($this->request->data);exit;
		$params = $this->request->data;

		$program_type_id = $params['program_type_id'];

		$conditions[] = array('Program.program_type_id' => $program_type_id);

		if (!empty($params['office_id'])) {
			$conditions[] = array('Program.officer_id' => $params['office_id']);
		}
		if (!empty($params['territory_id'])) {
			$conditions[] = array('Territory.id' => $params['territory_id']);
		}
		if (!empty($params['market_id'])) {
			$conditions[] = array('Market.id' => $params['market_id']);
		}
		if (!empty($params['thana_id'])) {
			$conditions[] = array('Market.thana_id' => $params['thana_id']);
		}
		if (!empty($params['status'])) {
			$conditions[] = array('Program.status' => $params['status']);
		}

		$this->Program->recursive = 0;

		$programs = $this->Program->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'Market',
					'table' => 'markets',
					'type' => 'INNER',
					'conditions' => 'Outlet.market_id=Market.id',
				),
				array(
					'alias' => 'Territory',
					'table' => 'territories',
					'type' => 'INNER',
					'conditions' => 'Territory.id=Market.territory_id',
				),
				array(
					'alias' => 'SalesPerson',
					'table' => 'sales_people',
					'type' => 'LEFT',
					'conditions' => 'SalesPerson.id=Program.program_officer_id',
				)
			),
			'fields' => array('Outlet.*', 'Program.*', 'Office.*', 'Doctor.*', 'Market.id', 'Market.name', 'SalesPerson.name', 'Market.thana_id', 'Territory.name', 'Territory.id'),
			'order' =>   array('Program.id' => 'desc')
		));
		// pr($programs);
		// exit;
		$this->autoRender = false;
		$View = new View($this, false);
		$View->set(compact('programs', 'program_type_id'));
		$html = $View->render('gsp_download_xl');
		echo $html;
	}

	function bsp_download_xl()
	{
		// pr($this->request->data);exit;
		$params = $this->request->data;

		$program_type_id = $params['program_type_id'];



		$conditions[] = array('Program.program_type_id' => $program_type_id);

		if (!empty($params['office_id'])) {
			$conditions[] = array('Program.officer_id' => $params['office_id']);
		}
		if (!empty($params['territory_id'])) {
			$conditions[] = array('Territory.id' => $params['territory_id']);
		}
		if (!empty($params['market_id'])) {
			$conditions[] = array('Market.id' => $params['market_id']);
		}
		if (!empty($params['thana_id'])) {
			$conditions[] = array('Market.thana_id' => $params['thana_id']);
		}
		if (!empty($params['status'])) {
			$conditions[] = array('Program.status' => $params['status']);
		}

		$this->Program->recursive = 0;

		$programs = $this->Program->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'Market',
					'table' => 'markets',
					'type' => 'INNER',
					'conditions' => 'Outlet.market_id=Market.id',
				),
				array(
					'alias' => 'Territory',
					'table' => 'territories',
					'type' => 'INNER',
					'conditions' => 'Territory.id=Market.territory_id',
				),
				array(
					'alias' => 'SalesPerson',
					'table' => 'sales_people',
					'type' => 'LEFT',
					'conditions' => 'SalesPerson.id=Program.program_officer_id',
				)
			),
			'fields' => array('Outlet.*', 'Program.*', 'Office.*', 'Doctor.*', 'Market.id', 'Market.name', 'SalesPerson.name', 'Market.thana_id', 'Territory.name', 'Territory.id'),
			'order' =>   array('Program.id' => 'desc')
		));
		$this->autoRender = false;
		$View = new View($this, false);
		$View->set(compact('programs', 'program_type_id'));
		$html = $View->render('bsp_download_xl');
		echo $html;
	}

	function program_officer_list(){
		$office_id = $this->request->data['office_id'];
		$this->loadModel('User');
		$this->loadModel('SalesPerson');
		$programoffice_con = array(
			'User.user_group_id'	=>1016,
			'User.active'			=>1,
		);
		if ($office_id >  0) {
			$programoffice_con['SalesPerson.office_id'] = $office_id;
		}

		$program_office_lsit = $this->SalesPerson->find('list', array(
			'conditions'=>$programoffice_con,
			'joins'=>array(
				array(
					'alias'=>'User',
					'table'=>'users',
					'type'=>'left',
					'conditions'=>'SalesPerson.id=User.sales_person_id'
				)
			),
			'fields'=>array('SalesPerson.id', 'SalesPerson.name')
		));

		//$this->dd($program_office_lsit);exit();
		echo json_encode($program_office_lsit);
		$this->autoRender = false;
		
	}
	
}
