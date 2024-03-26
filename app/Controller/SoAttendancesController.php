<?php
App::uses('AppController', 'Controller');

/**
 * VisitPlanLists Controller
 *
 * @property DistMapSalesTrack $DistMapSalesTrack
 * @property PaginatorComponent $Paginator
 */
class SoAttendancesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $uses = array('Office','SalesPerson', 'SoCheckInOut');
	public $components = array('Paginator', 'Session', 'Filter.Filter');   

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index($id = null) 
	{
		ini_set('memory_limit', '2048M');
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes
				
		
		$this->set('page_title', "SO Attendance List");

					
		//for region office list
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id'=>3), 
			'order' => array('office_name' => 'asc')
		));
		
		
		
		//report type
		$present_status_array=array('absent'=>'Absent','present'=>'Present');
		$this->set(compact('present_status_array'));
				
		
		
		$region_office_id = 0;
				
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		
		$this->set(compact('office_parent_id'));
		
		$office_conditions = array('Office.office_type_id'=>2);
		
		if ($office_parent_id == 0)
		{
			$office_id = 0;
		}
		elseif($office_parent_id == 14)
		{
			$region_office_id = $this->UserAuth->getOfficeId();
			$region_offices = $this->Office->find('list', array(
				'conditions' => array('Office.office_type_id'=>3, 'Office.id'=>$region_office_id), 
				'order' => array('office_name' => 'asc')
			));
			
			$office_conditions = array('Office.parent_office_id'=>$region_office_id);
			
			$office_id = 0;
			
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id'    => 2,
					'parent_office_id'  => $region_office_id,
					
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
			
			$office_ids = array_keys($offices);
			
			if($office_ids)$conditions['Territory.office_id'] = $office_ids;

		}
		else 
		{
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id'=>2);
			$office_id = $this->UserAuth->getOfficeId();
			
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'id'    => $office_id,
				),   
				'order'=>array('office_name'=>'asc')
			));
			
		}
		
		
		$dis_con = array();
		

		if($this->request->is('post') || $this->request->is('put'))
		{
			
			$request_data = $this->request->data;
			$from_date = date('Y-m-d', strtotime($request_data['SoCheckInOut']['from_date']));
			$to_date = date('Y-m-d', strtotime($request_data['SoCheckInOut']['to_date']));
		
			$this->set(compact( 'from_date', 'to_date'));
			
			$present_status = $this->request->data['SoCheckInOut']['present_status'];
			$this->set(compact('present_status'));
			
			$region_office_id = isset($this->request->data['SoCheckInOut']['region_office_id']) != '' ? $this->request->data['SoCheckInOut']['region_office_id'] : $region_office_id;
			$this->set(compact('region_office_id'));
			$office_ids = array();
			if($region_office_id)
			{
				$offices = $this->Office->find('list', array(
					'conditions'=> array(
						'office_type_id'    => 2,
						'parent_office_id'  => $region_office_id,
						
						"NOT" => array( "id" => array(30, 31, 37))
						), 
					'order'=>array('office_name'=>'asc')
				));
				
				$office_ids = array_keys($offices);
			}
			
			$office_id = isset($this->request->data['SoCheckInOut']['office_id']) != '' ? $this->request->data['SoCheckInOut']['office_id'] : $office_id;
			$this->set(compact('office_id'));
			$db_id = isset($this->request->data['SoCheckInOut']['db_id']) != '' ? $this->request->data['SoCheckInOut']['db_id'] : 0;
			$tso_id = isset($this->request->data['SoCheckInOut']['tso_id']) != '' ? $this->request->data['SoCheckInOut']['tso_id'] : 0;
			
			$this->set(compact( 'date'));
			$present_status = $this->request->data['SoCheckInOut']['present_status'];
			$this->set(compact('present_status'));
			$this->set(compact('db_id','tso_id'));
			$conditions=array();
			
			$condition_order=array();
			if($office_id)
			{
				$conditions['Office.id']=$office_id;
			}
			elseif($office_ids)
			{
				$conditions['Office.id']=$office_ids;
			}
			

			if($present_status== 'absent')
			{
				$conditions['SoCheckInOut.id']=NULL;
			}
			elseif($present_status== 'present')
			{
				$conditions['SoCheckInOut.id <>']=NULL;
			}

			$conditions['SoCheckInOut.date >=']=$from_date;
			$conditions['SoCheckInOut.date <=']=$to_date;
			$conditions['User.active']=1;
			
			//print_r($conditions);exit;

		}else{

			$conditions = array(
				'SoCheckInOut.date'=> date('Y-m-d'),
				'User.active'=>1
			);

			if($office_id)
			{
				$conditions['Office.id']=$office_id;
			}
			
			$request_data = '';

		}

		if( $this->UserAuth->getUserGroupId() == 1028){
			$conditions['AE.id'] = $this->UserAuth->getPersonId() ;
		}

			$result=$this->SalesPerson->find('all',array(
				'conditions'=>$conditions,
				'joins'=>array(
					array(
						'table'=>'territories',
						'alias'=>'Territory',
						'conditions'=>'Territory.id=SalesPerson.territory_id'
						),
					array(
						'table'=>'so_check_in_outs',
						'alias'=>'SoCheckInOut',
						'type'=>'Left',
						'conditions'=>'SoCheckInOut.so_id=SalesPerson.id'
						),
					array(
						'table'=>'offices',
						'alias'=>'Office',
						'conditions'=>'SoCheckInOut.office_id=Office.id'
					),
					array(
						'table'=>'sales_people',
						'alias'=>'AE',
						'type'=>'left',
						'conditions'=>'AE.id=SalesPerson.ae_id'
					),
					array(
						'table'=>'users',
						'alias'=>'User',
						'conditions'=>'User.sales_person_id=SalesPerson.id'
						),
					
					),
				'fields'=>array(
					'SalesPerson.name',
					'AE.name',
					'Territory.name',
					'SoCheckInOut.check_in_time',
					'SoCheckInOut.check_out_time',
					'SoCheckInOut.date',
					'SoCheckInOut.id',
					'SoCheckInOut.status',
					'SoCheckInOut.note',
					'Office.office_name',
					),
				'order'=>array('SoCheckInOut.id'=>'DESC', 'Office.order'),
				'recursive'=>-1,
				'limit'=>50
				));

				//echo '<pre>';print_r($result);exit;

			$this->set(compact('result'));
			
			

			
		
		$this->set(compact('offices', 'region_offices', 'office_id', 'request_data'));
		
	}

	public function admin_attendance_status_update(){

		if ($this->request->is('post') || $this->request->is('put')) {

            $this->request->data['SoCheckInOut']['approve_by'] = $this->UserAuth->getUserId();
			unset($this->request->data['SoCheckInOut']['so_name']);
            if ($this->SoCheckInOut->save($this->request->data)) {
                $this->Session->setFlash(__('The So Attendance status has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                //$this->Session->setFlash(__('The market could not be saved. Please, try again.'), 'flash/error');
            }
        }

	}
	
}
