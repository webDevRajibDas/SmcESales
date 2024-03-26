<?php
App::uses('AppController', 'Controller');
/**
 * Designations Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class MacFreeLogsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'Filter.Filter');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','Mac Free Log List');
		$this->MacFreeLog->recursive = 0;
		$this->paginate = array(		
			'joins' => array(
				array(
	                'table'=>'sales_people',
	                'alias'=>'SalesPeople',
	                'conditions'=>'SalesPeople.id=User.sales_person_id'
	            ),
	            array(
	                'table'=>'offices',
	                'alias'=>'Office',
	                'conditions'=>'Office.id=SalesPeople.office_id'
	            ),
	            array(
	                'table'=>'user_groups',
	                'alias'=>'UserGroup',
	                'conditions'=>'UserGroup.id=User.user_group_id'
	            ),
	        ),
			'fields' => array(
				'MacFreeLog.*', 
				'User.username',
				'User.mac_id',
				'Office.office_name',
				'UserGroup.name',
			),
			//	'conditions' => $conditions,
			'order' => array('MacFreeLog.user_id' => 'ASC'),
		);
		$this->set('maclog', $this->paginate());

		$this->loadModel('User');

		$userList = $this->User->find('list', array(
			'conditions'=>array('User.active'=>1),
			'fields'=>array('User.id','User.username')
		));

		$office_conditions = array();

		if($this->UserAuth->getOfficeParentId() != 0)
		{
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}

		$this->loadModel('Office');
		$this->loadModel('UserGroup');

		$offices = $this->Office->find('list',array('fields'=>array('Office.id','Office.office_name'),'conditions'=> $office_conditions,'order' => array('Office.office_name'=>'ASC')));	

        $userGroups = $this->UserGroup->find('list',array('order'=>array('id'=>'ASC')));
		$this->set(compact('userList', 'offices','userGroups'));

	}

}
