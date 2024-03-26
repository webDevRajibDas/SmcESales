<?php
/*
	This file is part of UserMgmt.

	Author: Chetan Varshney (http://ektasoftwares.com)

	UserMgmt is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	UserMgmt is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
*/

//App::uses('UserMgmtAppController', 'Usermgmt.Controller');
App::uses('AppController', 'Controller');
//class UsersController extends UserMgmtAppController {
class DmsUsersController extends AppController {
	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator');
	var $is_menu;
	/**
	 * This controller uses following models
	 *
	 * @var array
	 */
	public $uses = array('Usermgmt.User', 'Usermgmt.UserGroup','Filter.Filter');
	/**
	 * Called before the controller action.  You can use this method to configure and customize components
	 * or perform logic that needs to happen before each controller action.
	 *
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
	}
	/**
	 * Used to display all users by Admin
	 *
	 * @access public
	 * @return array
	 */
	public function admin_index() {
		$this->set('page_title', 'DB User List');
		$this->User->unbindModel(array('hasMany' => array('LoginToken')));
		$this->User->UserGroup->unbindModel(array('hasMany' => array('UserGroupPermission')));
		$user_types = array(0 => 'E-Sales', 1 => 'DMS');
		// Custome Search  	
		$user_groups = array(1028=>'AE',1029=>'TSO',1032=>'SR',1034=>'DB User'); 	
		$conditions = array('User.user_group_id' => array_keys($user_groups));
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
            $office_conditions = array(
			/*'office_type_id' => 2,*/
				"NOT" => array( "id" => array(30, 31, 37))
			);	
            
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }		
			
		if(($this->request->is('post') || $this->request->is('put'))){
			
			if($this->request->data['username'] != '')
			{
				$conditions[] = array('User.username LIKE' => '%'.$this->request->data['username'].'%'); 
			}
			
			if($this->request->data['user_group_id'] != '')
			{
				$conditions[] = array('User.user_group_id' => $this->request->data['user_group_id']); 
			}
			if($this->request->data['office_id'] != '')
			{
				$conditions[] = array('SalesPerson.office_id' => $this->request->data['office_id']); 
			}
		}
		// End Search
		
		if($this->UserAuth->getOfficeParentId() != 0)
		{
			$conditions[] = array('SalesPerson.office_id' => $this->UserAuth->getOfficeId());
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}
		$this->User->recursive = 2;
		$this->paginate = array(			
			'conditions' => $conditions,
			'order' => array('User.id' => 'DESC'),
			'limit' => 50
		);
		$this->set('users', $this->paginate());	
		$this->loadModel('SalesPerson');
		/*$offices = $this->SalesPerson->Office->find('list',array('fields'=>array('Office.id','Office.office_name'),'conditions'=> $office_conditions,'order' => array('Office.office_name'=>'ASC')));*/		
		$this->loadModel('Office');
		$offices = $this->Office->find('list',array('fields'=>array('Office.id','Office.office_name'),'conditions'=> $office_conditions,'order' => array('Office.office_name'=>'ASC')));		
         $userGroups = $this->UserGroup->find('list',array(
         	'conditions'=>array('id'=> array_keys($user_groups)),
         	'order'=>array('id'=>'ASC')
         ));
		$this->set(compact('offices','userGroups','user_types'));	
	}

	public function admin_view($userId=null) {
		
		if (!empty($userId)) {
			$this->User->unbindModel(array('hasMany' => array('LoginToken')));
			$this->User->UserGroup->unbindModel(array('hasMany' => array('UserGroupPermission')));
			$this->User->recursive = 2;			
			$user = $this->User->read(null, $userId);
			$this->set('user', $user);
		} else {
			$this->redirect('/admin/dms_users/index');
		}
	}

	public function duplicate_user_check()
	{
		$user_id = $this->request->data['user_id'];
		$username = $this->request->data['username'];
		
		if($user_id!='')
		{
			$conditions = array('id !=' => $user_id,'username'=>$username);
		}else{
			$conditions = array('username' => $username);
		}
		
		$user = $this->User->find('first',array(
			'conditions' => $conditions,
			'recursive' => -1
		));
		if(empty($user))
		{
			//if (preg_match('#[0-9]#', $username) OR preg_match('#[a-zA-Z]#', $username)) 
			if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $username))
			{
				echo '<span style="color:red"> Special character not allowed.</span>';
			}else{
				echo '<span style="color:green"> Available</span>';				
			}			
		}else{
			echo '<span style="color:red"> Sorry Username already taken !!!</span>';
		}
		$this->autoRender = false;
	}

	public function admin_add() {
		$this->loadModel('SalesPerson');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
            $office_conditions = array("NOT" => array( "id" => array(30, 31, 37)));
            
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }

		if ($this->request -> isPost()) {
			
			$this->User->create();
			
			$dist_distributor_id = $this->request->data['DmsUser']['dist_distributor_id'];

			$this->request->data['User']['user_group_id'] = $this->request->data['DmsUser']['user_group_id'];
			$this->request->data['User'] = $this->request->data['DmsUser'];
			
			unset($this->request->data['DmsUser']);
			
			$this->request->data['SalesPerson']['user_group_id'] = $this->request->data['User']['user_group_id'];
			$this->request->data['SalesPerson']['created_at'] = $this->current_datetime();
			$this->request->data['SalesPerson']['updated_at'] = $this->current_datetime();
			$this->request->data['SalesPerson']['created_by'] = $this->UserAuth->getUserId();
			if($this->User->saveAll($this->request->data)) {
				
				$this->request->data['DmsUser']['id'] = $this->User->id;
				$this->request->data['User']['password'] = $this->UserAuth->makePassword($this->request->data['User']['password']);
				$this->User->save($this->request->data);
				
				$this->Session->setFlash(__('The user is successfully added'), 'flash/success');
				$this->redirect('/admin/dms_users/index');							
			}
		}


		$designations = $this->SalesPerson->Designation->find('list',array('fields'=>array('Designation.id','Designation.designation_name'),'order' => array('Designation.designation_name'=>'ASC')));
		$offices = $this->SalesPerson->Office->find('list',array(
			'conditions'=>$office_conditions,
			'fields'=>array('Office.id','Office.office_name'),
			'order' => array('Office.office_name'=>'ASC'),
		));
		$user_groups = array(1028=>'AE',1029=>'TSO',1032=>'SR',1034=>'DB User'); 		
		$userGroups = $this->UserGroup->find('list',array(
			'conditions'=>array('id'=> array_keys($user_groups)),
			'order'=>array('id'=>'ASC')
		));
		$this->set(compact('designations','offices','userGroups'));
	}
	/**
	 * Used to edit user on the site by Admin
	 *
	 * @access public
	 * @param integer $userId user id of user
	 * @return void
	 */
	public function admin_edit($userId=null) {
		$this->loadModel('SalesPerson');
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
            $office_conditions = array("NOT" => array( "id" => array(30, 31, 37)));
            
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }
		$sr_data = array();
		if ($this->request -> isPost()) {
			
			$this->request->data['User'] = $this->request->data['DmsUser'];
			unset($this->request->data['DmsUser']);
			
			$this->request->data['SalesPerson']['id'] = $this->request->data['User']['sales_person_id'];
			$this->request->data['SalesPerson']['updated_at'] = $this->current_datetime();
			$this->request->data['SalesPerson']['updated_by'] = $this->UserAuth->getUserId();				
			$this->request->data['SalesPerson']['user_group_id'] = $this->request->data['User']['user_group_id'];		
			
			if (empty($this->request->data['User']['password'])) {
					unset($this->request->data['User']['password']);
				} 	
				
			if($this->User->saveAll($this->request->data)) {				
				
				$this->request->data['User']['id'] = $userId;
				if (empty($this->request->data['User']['password'])) {
					unset($this->request->data['User']['password']);
				} else {
					$this->request->data['User']['password'] = $this->UserAuth->makePassword($this->request->data['User']['password']);
				}
				$this->User->save($this->request->data);				
				$this->Session->setFlash(__('The user is successfully updated'), 'flash/success');
				$this->redirect('/admin/dms_users/index');
			}
		} else {
			$user = $this->User->read(null, $userId);
			//pr($user);die();
			$this->request->data=null;
			if (!empty($user)) {
				$user['User']['password']='';
				$sales_persons=$this->SalesPerson->find('first',array('conditions'=>array('SalesPerson.id'=>$user['User']['sales_person_id'])));
				$office_id = $sales_persons['SalesPerson']['office_id'];
				//pr($sales_persons);die();
				$user['SalesPerson'] = $sales_persons['SalesPerson'];
				$user['Designation'] = $sales_persons['Designation'];
				//$user['UserGroup'] = $sales_persons['UserGroup'];
				$e_sr_id = $sales_persons['SalesPerson']['dist_sales_representative_id'];
				//$e_sr_id[] = NULL;

				$user_list = array();
				if($user['User']['user_group_id']==1032){
					$exist_users = $this->SalesPerson->find('all',array(
			          	'conditions'=>array(
			          		'SalesPerson.dist_sales_representative_id != ' => array(NULL,$e_sr_id),
			          		'SalesPerson.office_id' => $office_id,
			          )));
					
	          		foreach ($exist_users as $key => $value) {
			           	$user_list[$key]=$value['SalesPerson']['dist_sales_representative_id'];
			         }
			         /*pr($user_list);die();*/
					$this->loadModel('DistSalesRepresentative');
					$sr_list=$this->DistSalesRepresentative->find('all',array(
						'joins'=>array(
			            	array(
			            		'table'=>'dist_distributors',
			            		'alias'=>'Dist',
			            		'conditions'=>'Dist.id=DistSalesRepresentative.dist_distributor_id',
			            		'type'=>'Left'
			            		),
			            	),
			          	'conditions'=>array(
			          		//'DistSalesRepresentative.id'=>$sales_persons['SalesPerson']['dist_sales_representative_id'],
			          		//'NOT'=>array('DistSalesRepresentative.id'=>$user_list),
			          		'DistSalesRepresentative.id !='=>$user_list,
			          		'DistSalesRepresentative.office_id'=> $office_id,
			          		'DistSalesRepresentative.is_active'=>1,
			          	),
			          ));
					//pr($sr_list);die();
					//echo $this->DistSalesRepresentative->getLastquery();
					foreach ($sr_list as $key => $value) {
						$sr_data[$value['DistSalesRepresentative']['id']] = $value['DistSalesRepresentative']['name']."(".$value['DistDistributor']['name'].")" ;
					}

					//pr($sr_data);die();
					$this->set(compact('sr_data'));
				}
				$this->request->data = $user;
				$this->request->data['DmsUser'] = $this->request->data['User'];
				unset($this->request->data['User']);
			}
		}
		$designations = $this->SalesPerson->Designation->find('list',array('fields'=>array('Designation.id','Designation.designation_name'),'order' => array('Designation.designation_name'=>'ASC')));
		$offices = $this->SalesPerson->Office->find('list',array(
			'conditions'=>$office_conditions,
			'fields'=>array('Office.id','Office.office_name'),
			'order' => array('Office.office_name'=>'ASC'),
		));
		$user_groups = array(1028=>'AE',1029=>'TSO',1032=>'SR',1034=>'DB User'); 		
		$userGroups = $this->UserGroup->find('list',array(
			'conditions'=>array('id'=> array_keys($user_groups)),
			'order'=>array('id'=>'ASC')
		));
		$this->set(compact('sr_data'));
		$this->set(compact('designations','offices','userGroups'));
	}
	/**
	 * Used to delete the user by Admin
	 *
	 * @access public
	 * @param integer $userId user id of user
	 * @return void
	 */
	public function admin_delete($userId = null) {
		if (!empty($userId)) {
			if ($this->request -> isPost()) {
				if ($this->User->delete($userId)) {
					$this->Session->setFlash(__('User has been deleted successfully.'), 'flash/success');					
				}
			}
			$this->redirect('/admin/dms_users/index');
		} else {
			$this->redirect('/admin/dms_users/index');
		}
	}

	public function get_distributor_list(){
		$office_id = $this->request->data['office_id'];
		$this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        if($user_group_id == 1029 || $user_group_id == 1028){
                if($user_group_id == 1028){
                    $dist_ae_info = $this->DistAreaExecutive->find('first',array(
                        'conditions'=>array('DistAreaExecutive.user_id'=>$user_id),
                        'recursive'=> -1,
                    ));
                    $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
                    $dist_tso_info = $this->DistTso->find('list',array(
                        'conditions'=>array('dist_area_executive_id'=>$dist_ae_id),
                        'fields'=> array('DistTso.id','DistTso.dist_area_executive_id'),
                    ));
                    
                    $dist_tso_id = array_keys($dist_tso_info);
                }
                else{
                    $dist_tso_info = $this->DistTso->find('first',array(
                        'conditions'=>array('DistTso.user_id'=>$user_id),
                        'recursive'=> -1,
                    ));
                    $dist_tso_id = $dist_tso_info['DistTso']['id'];
                }
               
                $tso_dist_list = $this->DistTsoMapping->find('list',array(
                    'conditions'=> array(
                        'dist_tso_id' => $dist_tso_id,
                    ),
                    'fields'=>array('DistTsoMapping.dist_distributor_id','DistTsoMapping.dist_tso_id'),
                ));
               $dist_conditions = array('DistDistributor.id'=>array_keys($tso_dist_list),'DistDistributor.is_active'=> 1);
            }
            else{
                
                $dist_conditions = array('DistDistributor.office_id'=>$office_id,'DistDistributor.is_active'=> 1);
            }

		$rs = array(array('id' => '', 'name' => '---- Select ----'));
		$this->loadModel('DistDistributor');
		$dist_distributors = $this->DistDistributor->find('all',array(
			'conditions'=> $dist_conditions,
			'order' => array('DistDistributor.name' => 'asc'),
			'recursive' => 0
		));

		$data_array = array();
		if(!empty($dist_distributors)){
			foreach ($dist_distributors as $key => $value) {
				$data_array[] = array(
					'id'=>$value['DistDistributor']['id'],
					'name'=>$value['DistDistributor']['name']
				);
			}

		}
	
		if(!empty($dist_distributors)){
			echo json_encode(array_merge($rs,$data_array));
		}
		else{
			echo json_encode($rs);
		}

		$this->autoRender = false;
	}
	public function get_distributor_list_for_db_user(){
		$office_id = $this->request->data['office_id'];
		$this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $this->loadModel('DistUserMapping');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        $exist_db_id = $this->DistUserMapping->find('list',array('conditions'=>array('office_id'=>$office_id),'fields'=>array('DistUserMapping.dist_distributor_id','DistUserMapping.office_id')));

        if($user_group_id == 1029 || $user_group_id == 1028){
                if($user_group_id == 1028){
                    $dist_ae_info = $this->DistAreaExecutive->find('first',array(
                        'conditions'=>array('DistAreaExecutive.user_id'=>$user_id),
                        'recursive'=> -1,
                    ));
                    $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
                    $dist_tso_info = $this->DistTso->find('list',array(
                        'conditions'=>array('dist_area_executive_id'=>$dist_ae_id),
                        'fields'=> array('DistTso.id','DistTso.dist_area_executive_id'),
                    ));
                    
                    $dist_tso_id = array_keys($dist_tso_info);
                }
                else{
                    $dist_tso_info = $this->DistTso->find('first',array(
                        'conditions'=>array('DistTso.user_id'=>$user_id),
                        'recursive'=> -1,
                    ));
                    $dist_tso_id = $dist_tso_info['DistTso']['id'];
                }
               
                $tso_dist_list = $this->DistTsoMapping->find('list',array(
                    'conditions'=> array(
                        'dist_tso_id' => $dist_tso_id,
                    ),
                    'fields'=>array('DistTsoMapping.dist_distributor_id','DistTsoMapping.dist_tso_id'),
                ));
               $dist_conditions = array(
               	'DistDistributor.id'=>array_keys($tso_dist_list),
               	'DistDistributor.is_active'=> 1,
               	'NOT'=> array('DistDistributor.id' =>array_keys($exist_db_id)),
               );
        }
        else{
            
            $dist_conditions = array(
            	'DistDistributor.office_id'=>$office_id,
            	'DistDistributor.is_active'=> 1,
            	'NOT'=> array('DistDistributor.id' =>array_keys($exist_db_id)),
            );
        }
        //pr($dist_conditions);die();
		$rs = array(array('id' => '', 'name' => '---- Select ----'));
		$this->loadModel('DistDistributor');
		$dist_distributors = $this->DistDistributor->find('all',array(
			'conditions'=> $dist_conditions,
			'order' => array('DistDistributor.name' => 'asc'),
			'recursive' => 0
		));

		$data_array = array();
		if(!empty($dist_distributors)){
			foreach ($dist_distributors as $key => $value) {
				$data_array[] = array(
					'id'=>$value['DistDistributor']['id'],
					'name'=>$value['DistDistributor']['name']
				);
			}

		}
	
		if(!empty($dist_distributors)){
			echo json_encode(array_merge($rs,$data_array));
		}
		else{
			echo json_encode($rs);
		}

		$this->autoRender = false;
	}
}
