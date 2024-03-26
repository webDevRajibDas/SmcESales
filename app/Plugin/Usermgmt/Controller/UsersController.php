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

App::uses('UserMgmtAppController', 'Usermgmt.Controller');

class UsersController extends UserMgmtAppController
{
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
	public $uses = array('Usermgmt.User', 'Usermgmt.UserGroup', 'Filter.Filter');
	/**
	 * Called before the controller action.  You can use this method to configure and customize components
	 * or perform logic that needs to happen before each controller action.
	 *
	 * @return void
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
	}
	/**
	 * Used to display all users by Admin
	 *
	 * @access public
	 * @return array
	 */
	public function index()
	{

		$this->set('page_title', 'User List');
		$this->User->unbindModel(array('hasMany' => array('LoginToken')));
		$this->User->UserGroup->unbindModel(array('hasMany' => array('UserGroupPermission')));
		$user_types = array(0 => 'E-Sales', 1 => 'DMS');
		// Custome Search  	
		$user_groups = array(1028 => 'AE', 1029 => 'TSO', 1032 => 'SR', 1034 => 'DB User');
		$conditions = array();
		$office_conditions = array(
			/*'office_type_id' => 2,*/
			"NOT" => array("id" => array(30, 31, 37))
		);
		if (($this->request->is('post') || $this->request->is('put'))) {

			if ($this->request->data['username'] != '') {
				$conditions[] = array('User.username LIKE' => '%' . $this->request->data['username'] . '%');
			}

			if ($this->request->data['name'] != '') {
				$conditions[] = array('SalesPerson.name LIKE' => '%' . $this->request->data['name'] . '%');
			}

			if ($this->request->data['user_type_id'] != '') {
				if ($this->request->data['user_type_id'] == 1) {

					$conditions[] = array('User.user_group_id' => array_keys($user_groups));
				} else {
					$conditions[] = array('NOT' => array('User.user_group_id' => array_keys($user_groups)));
				}
			}
			if ($this->request->data['user_group_id'] != '') {
				$conditions[] = array('User.user_group_id' => $this->request->data['user_group_id']);
			}
			if ($this->request->data['office_id'] != '') {
				$conditions[] = array('SalesPerson.office_id' => $this->request->data['office_id']);
			}

			if ($this->request->data['mac_id'] != '') {
				$conditions[] = array('User.mac_id' => $this->request->data['mac_id']);
			}
			if ($this->request->data['version'] != '') {
				$conditions[] = array('User.version' => $this->request->data['version']);
			}
		} else {
			$conditions[] = array('NOT' => array('User.user_group_id' => array_keys($user_groups)));
		}
		// End Search

		if ($this->UserAuth->getOfficeParentId() != 0) {
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
		$offices = $this->Office->find('list', array('fields' => array('Office.id', 'Office.office_name'), 'conditions' => $office_conditions, 'order' => array('Office.office_name' => 'ASC')));
		$userGroups = $this->UserGroup->find('list', array('order' => array('id' => 'ASC')));
		$this->set(compact('offices', 'userGroups', 'user_types'));
	}


	public function territory_tag($sales_person_id = null)
	{
		$this->loadModel('SalesPerson');
		$this->loadModel('Territory');
		$this->loadModel('TerritoryAssignHistory');
		$message = '';

		if ($this->request->isPut()) {
			unset($this->request->data['SalesPerson']['sales_person_name']);
			$count_territory = $this->SalesPerson->find('count', array(
				'conditions' => array(
					'SalesPerson.office_id' => $this->request->data['SalesPerson']['office_id'],
					'SalesPerson.territory_id' => $this->request->data['SalesPerson']['territory_id'],
					'User.user_group_id' => array(4, 1008),
					'User.active' => 1,
					'SalesPerson.id !=' => $this->request->data['SalesPerson']['id']
				),
				'recursive' => 0
			));

			if ($count_territory > 0) {
				$message = '<div class="alert alert-danger">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								This Territory already assigned.
							</div>';
			} else {
				// set territory to SO
				$SalesPerson['office_id'] = $this->request->data['SalesPerson']['office_id'];
				$SalesPerson['id'] = $this->request->data['SalesPerson']['id'];
				$SalesPerson['territory_id'] = $this->request->data['SalesPerson']['territory_id'];
				$this->SalesPerson->save($SalesPerson);

				// update territory assign status
				$Territory['id'] = $this->request->data['SalesPerson']['territory_id'];
				$Territory['is_assigned'] = 1;
				$this->Territory->save($Territory);

				// add territory assign history
				$TerritoryAssignHistory['so_id'] = $this->request->data['SalesPerson']['id'];
				$TerritoryAssignHistory['territory_id'] = $this->request->data['SalesPerson']['territory_id'];
				$TerritoryAssignHistory['assign_type'] = 1;
				$TerritoryAssignHistory['date'] = $this->current_date();
				$TerritoryAssignHistory['created_at'] = $this->current_datetime();
				$TerritoryAssignHistory['created_by'] = $this->UserAuth->getUserId();
				$this->TerritoryAssignHistory->save($TerritoryAssignHistory);

				$this->Session->setFlash(__('The user is successfully updated'), 'flash/success');
				$this->redirect('/admin/allUsers');
			}
		} else {
			$options = array('fields' => array('SalesPerson.*', 'User.*'), 'conditions' => array('SalesPerson.' . $this->SalesPerson->primaryKey => $sales_person_id), 'recursive' => 0);
			$this->request->data = $this->SalesPerson->find('first', $options);



			if ($this->request->data['User']['user_group_id'] != 4 && $this->request->data['User']['user_group_id'] != 1008) {
				$this->Session->setFlash(__('You can not assign Territory without SO or SPO group.'), 'flash/error');
				$this->redirect('/admin/allUsers');
			}
		}


		//Territory Sales Performance Monitoring
		$this->loadModel('Office');
		$offices = $this->Office->find('list', array(
			'conditions' => array(
				'office_type_id' => 2,
				"NOT" => array("id" => array(30, 31, 37))
			),
			'order' => array('office_name' => 'asc')
		));
		$this->set(compact('offices'));


		//echo $this->UserAuth->getOfficeParentId();
		//exit;

		$office_parent_id = $this->UserAuth->getOfficeParentId();

		$this->set(compact('offices', 'office_parent_id'));

		if ($office_parent_id == 0) {
			$this->Territory->unbindModel(array('belongsTo' => array('Office')));
			$territorys = $this->Territory->find('list', array(
				'fields' => array('Territory.id', 'Territory.name'),
				'conditions' => array(
					'Territory.office_id' => $this->request->data['SalesPerson']['office_id'],
					'AND' => array(
						'OR' => array(
							'Territory.is_assigned' => 0,
							'Territory.id' => $this->request->data['SalesPerson']['territory_id']
						),
						array(
							'OR' => array('AND' => array('Territory.parent_id' => 0, 'TerritoryChid.id is null'),  array('Territory.parent_id >' => 0)),

						)
					),

				),
				'joins' => array(
					array(
						'table' => 'territories',
						'alias' => 'TerritoryChid',
						'type' => 'Left',
						'conditions' => 'TerritoryChid.parent_id=Territory.id'
					)
				),
				'fields' => array('Territory.id', 'Territory.name'),
				'group' => array('Territory.id', 'Territory.name'),
				'order' => array('Territory.name' => 'ASC'),
				'recursive' => -1
			));
		} else {
			$this->Territory->unbindModel(array('belongsTo' => array('Office')));
			$territorys = $this->Territory->find('list', array(
				'fields' => array('Territory.id', 'Territory.name'),
				'conditions' => array(
					'Territory.office_id' => $this->UserAuth->getOfficeId(),
					'conditions' => array(
						'Territory.office_id' => $this->request->data['SalesPerson']['office_id'],
						'OR' => array(
							'Territory.is_assigned' => 0,
							'Territory.id' => $this->request->data['SalesPerson']['territory_id']
						),
						'OR' => array(
							'AND' => array('Territory.parent_id' => 0, 'TerritoryChid.id is null'),
							'Territory.parent_id >' => 0
						)
					),
					'joins' => array(
						array(
							'table' => 'territories',
							'alias' => 'TerritoryChid',
							'conditions' => 'TerritoryChid.parent_id=Territory.id'
						)
					),
					'fields' => array('Territory.id', 'Territory.name'),
					'group' => array('Territory.id', 'Territory.name'),
				),
				'order' => array('Territory.name' => 'ASC'),
				'recursive' => -1
			));
		}

		//pr($territorys);

		$this->set(compact('territorys', 'message'));
	}


	/**
	 * Territory deassigned by Admin
	 * 
	 */

	public function territory_deassigned($sales_person_id = null)
	{
		$this->loadModel('SalesPerson');
		$this->loadModel('Territory');
		$this->loadModel('TerritoryAssignHistory');
		$message = '';
		if ($this->request->isPut()) {
			unset($this->request->data['SalesPerson']['sales_person_name']);
			$count_territory = $this->SalesPerson->find('count', array(
				'conditions' => array(
					'SalesPerson.office_id' => $this->request->data['SalesPerson']['office_id'],
					'SalesPerson.territory_id' => $this->request->data['SalesPerson']['territory_id'],
					'User.user_group_id' => array(4, 1008),
					'User.active' => 1,
					'SalesPerson.id' => $this->request->data['SalesPerson']['id']
				),
				'recursive' => 0
			));

			if ($count_territory == 0) {
				$message = '<div class="alert alert-danger">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								Territory not available.
							</div>';
			} else {


				/*------------ Checking territory deposit balance : start -------------------------*/
				$this->LoadModel('TerritoryWiseCollectionDepositBalance');
				$deposit_data = $this->TerritoryWiseCollectionDepositBalance->find('all', array(
					'conditions' => array(
						'TerritoryWiseCollectionDepositBalance.territory_id' => $this->request->data['SalesPerson']['territory_id'],
						'TerritoryWiseCollectionDepositBalance.so_id' => $this->request->data['SalesPerson']['id'],
					),
					'order' => array('TerritoryWiseCollectionDepositBalance.instrument_type_id asc'),
					'recursive' => -1
				));
				/*------------ Checking territory deposit balance : END   -------------------------*/

				if ($deposit_data['0']['TerritoryWiseCollectionDepositBalance']['hands_of_so'] <= 0 && $deposit_data['1']['TerritoryWiseCollectionDepositBalance']['hands_of_so'] <= 0) {
					// remove Mac Id from Users
					$user['mac_id'] = NULL;
					$this->User->updateAll($user, array('User.sales_person_id' => $this->request->data['SalesPerson']['id']));
					// remove territory from SO
					$SalesPerson['id'] = $this->request->data['SalesPerson']['id'];
					$SalesPerson['territory_id'] = NULL;
					$this->SalesPerson->save($SalesPerson);

					// update territory assign status
					$Territory['id'] = $this->request->data['SalesPerson']['territory_id'];
					$Territory['is_assigned'] = 0;
					$this->Territory->save($Territory);

					// add territory assign history
					$TerritoryAssignHistory['so_id'] = $this->request->data['SalesPerson']['id'];
					$TerritoryAssignHistory['territory_id'] = $this->request->data['SalesPerson']['territory_id'];
					$TerritoryAssignHistory['assign_type'] = 2;
					$TerritoryAssignHistory['date'] = date('Y-m-d', strtotime($this->request->data['SalesPerson']['date']));
					$TerritoryAssignHistory['created_at'] = $this->current_datetime();
					$TerritoryAssignHistory['created_by'] = $this->UserAuth->getUserId();
					$this->TerritoryAssignHistory->save($TerritoryAssignHistory);

					$this->Session->setFlash(__('Territory de-assigned has been successfully completed.'), 'flash/success');
					$this->redirect('/admin/allUsers');
				} else {
					$territoryid = $this->request->data['SalesPerson']['territory_id'];
					$salespersonid = $this->request->data['SalesPerson']['id'];

					$insturment_sql = "
					select *,cl.amount-dp.amount as diff from (
						select  
							memo_no,
							sum(collectionAmount) amount
						from collections 
						where 
							territory_id=$territoryid 
							and so_id=$salespersonid 
							and memo_date >='2018-10-01' 
							and type=2
						group by
							memo_no
						) cl
						
						left join(
						select
							memo_no,
							sum(deposit_amount) amount
						from deposits  
						where 
							territory_id=$territoryid 
							and sales_person_id=$salespersonid 
							and deposit_date >='2018-10-01' 
							and type=2
						group by
							memo_no
						)dp on cl.memo_no=dp.memo_no
						
						where dp.memo_no is null or cl.amount!=dp.amount
						order by cl.memo_no
					";

					$cash_sql = "
					select *,cl.amount-dp.amount as diff from (
						select  
							memo_no,
							sum(collectionAmount) amount
						from collections 
						where 
							territory_id=$territoryid 
							and so_id=$salespersonid 
							and memo_date >='2018-10-01' 
							and type=1
						group by
							memo_no
						) cl
						
						left join(
						select
							memo_no,
							sum(deposit_amount) amount
						from deposits  
						where 
							territory_id=$territoryid 
							and sales_person_id=$salespersonid 
							and deposit_date >='2018-10-01' 
							and type=1
						group by
							memo_no
						)dp on cl.memo_no=dp.memo_no
						
						where dp.memo_no is null or cl.amount!=dp.amount
						order by cl.memo_no
					";


					$instument_memo = $this->TerritoryWiseCollectionDepositBalance->query($insturment_sql);
					$memohtml = '';
					//echo '<pre>';print_r($instument_memo);exit;
					if (!empty($instument_memo)) {

						foreach ($instument_memo as $v) {
							if (!empty($v[0]['memo_no'])) {
								$memohtml .= $v[0]['memo_no'] . ', ';
							}
						}

						$memohtml = rtrim($memohtml, ", ");;
					}

					$message = '<div class="alert alert-danger">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								Please Push Pending Deposit
								<p>Cash Deposit : ' . $deposit_data['0']['TerritoryWiseCollectionDepositBalance']['hands_of_so'] . '</p>
								<p>Instrument Deposit : ' . $deposit_data['1']['TerritoryWiseCollectionDepositBalance']['hands_of_so'] . '</p>
								<p>Instrument Memo List : ' . $memohtml . '</p>
							</div>';
				}
			}
		} else {
			$options = array('fields' => array('SalesPerson.*', 'User.*'), 'conditions' => array('SalesPerson.' . $this->SalesPerson->primaryKey => $sales_person_id), 'recursive' => 0);
			$this->request->data = $this->SalesPerson->find('first', $options);
			if ($this->request->data['User']['user_group_id'] != 4 && $this->request->data['User']['user_group_id'] != 1008) {
				$this->Session->setFlash(__('You can not de-assign Territory without SO group.'), 'flash/error');
				$this->redirect('/admin/allUsers');
			}

			if ($this->request->data['SalesPerson']['territory_id'] == 0) {
				$this->Session->setFlash(__('Territory not available.'), 'flash/error');
				$this->redirect('/admin/allUsers');
			}
		}
		$this->set(compact('message'));
	}

	/**
	 * Used to display detail of user by Admin
	 *
	 * @access public
	 * @param integer $userId user id of user
	 * @return array
	 */
	public function viewUser($userId = null)
	{

		if (!empty($userId)) {
			$this->User->unbindModel(array('hasMany' => array('LoginToken')));
			$this->User->UserGroup->unbindModel(array('hasMany' => array('UserGroupPermission')));
			$this->User->recursive = 2;
			$user = $this->User->read(null, $userId);
			$this->set('user', $user);
		} else {
			$this->redirect('/allUsers');
		}
	}


	public function duplicate_user_check()
	{
		$user_id = $this->request->data['user_id'];
		$username = $this->request->data['username'];

		if ($user_id != '') {
			$conditions = array('id !=' => $user_id, 'username' => $username);
		} else {
			$conditions = array('username' => $username);
		}

		$user = $this->User->find('first', array(
			'conditions' => $conditions,
			'recursive' => -1
		));
		if (empty($user)) {
			//if (preg_match('#[0-9]#', $username) OR preg_match('#[a-zA-Z]#', $username)) 
			if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $username)) {
				echo '<span style="color:red"> Special character not allowed.</span>';
			} else {
				echo '<span style="color:green"> Available</span>';
			}
		} else {
			echo '<span style="color:red"> Sorry Username already taken !!!</span>';
		}
		$this->autoRender = false;
	}

	public function duplicate_usercode_check()
	{
		$this->loadModel('SalesPerson');
		$id = $this->request->data['id'];
		$code = $this->request->data['code'];
		if ($id != '') {
			$conditions = array('id !=' => $id, 'code' => $code);
		} else {
			$conditions = array('code' => $code);
		}
		$user = $this->SalesPerson->find('first', array(
			'conditions' => $conditions,
			'recursive' => -1
		));

		if (empty($user)) {
			echo '<span style="color:green"> Available</span>';
		} else {
			echo '<span style="color:red"> Sorry Code already taken !!!</span>';
		}
		$this->autoRender = false;
	}


	/**
	 * Used to display detail of user by user
	 *
	 * @access public
	 * @return array
	 */
	public function myprofile()
	{
		$userId = $this->UserAuth->getUserId();
		$user = $this->User->read(null, $userId);
		$this->set('user', $user);
	}
	/**
	 * Used to logged in the site
	 *
	 * @access public
	 * @return void
	 */
	public function login()
	{
		$this->layout = 'login';
		$this->set('page_title', 'Admin Login');

		if ($this->request->isPost()) {

			$this->User->set($this->data);
			if ($this->User->LoginValidate()) {
				$userId  = $this->data['User']['username'];
				$password = $this->data['User']['password'];


				if (!empty($userId)) {
					$user = $this->User->findByUsername($userId);
					//$user = $this->User->findByEmail($email);
					if (empty($user)) {
						$this->Session->setFlash(__('<div class="alert alert-danger">Incorrect Email/Username or Password</div>'));
						return;
					}
				}
				// check for inactive account
				if ($user['User']['id'] != 1 and $user['User']['active'] == 0) {
					$this->Session->setFlash(__('Your registration has not been confirmed please verify your email or contact to Administrator'));
					return;
				}
				$hashed = md5($password);
				if ($user['User']['password'] === $hashed) {
					$this->UserAuth->login($user);
					$remember = (!empty($this->data['User']['remember']));
					if ($remember) {
						$this->UserAuth->persist('2 weeks');
					}
					$OriginAfterLogin = $this->Session->read('Usermgmt.OriginAfterLogin');
					$this->Session->delete('Usermgmt.OriginAfterLogin');
					//$redirect = (!empty($OriginAfterLogin)) ? $OriginAfterLogin : loginRedirectUrl;
					//$this->redirect($redirect);					

					$this->loadModel('Office');
					$this->loadModel('Store');

					$office_info = $this->Office->find('first', array(
						'conditions' => array('Office.id' => $this->UserAuth->getOfficeId()),
						'fields' => 'Office.id,Office.parent_office_id',
						'recursive' => -1
					));
					$store_info = $this->Store->find('first', array(
						'conditions' => array('Store.office_id' => $this->UserAuth->getOfficeId(), 'Store.territory_id' => NULL),
						'fields' => 'Store.id',
						'recursive' => -1
					));
					$sdata['id'] = $office_info['Office']['id'];
					$sdata['parent_office_id'] = $office_info['Office']['parent_office_id'];

					if (!empty($store_info)) {
						$sdata['store_id'] = $store_info['Store']['id'];
					} else {
						$sdata['store_id'] = 0;
					}
					$this->Session->write('Office', $sdata);

					$this->redirect('/admin/dashboards2');
					//$this->redirect('/admin/allUsers');
				} else {
					$this->Session->setFlash(__('<div class="alert alert-danger">Incorrect Email/Username or Password</div>'));
					return;
				}
			}
		}
	}

	/**
	 * Used to logged out from the site
	 *
	 * @access public
	 * @return void
	 */
	public function logout()
	{
		$this->UserAuth->logout();
		$this->Session->setFlash(__('<div class="alert alert-success">You are successfully signed out</div>'));
		$this->redirect('/login');
	}
	/**
	 * Used to register on the site
	 *
	 * @access public
	 * @return void
	 */
	public function register()
	{
		$userId = $this->UserAuth->getUserId();
		if ($userId) {
			$this->redirect("/user_dashboard");
		}
		if (siteRegistration) {
			$userGroups = $this->UserGroup->getGroupsForRegistration();
			$this->set('userGroups', $userGroups);
			if ($this->request->isPost()) {
				$this->User->set($this->data);
				if ($this->User->RegisterValidate()) {
					if (!isset($this->data['User']['user_group_id'])) {
						$this->request->data['User']['user_group_id'] = defaultGroupId;
					} elseif (!$this->UserGroup->isAllowedForRegistration($this->data['User']['user_group_id'])) {
						$this->Session->setFlash(__('Please select correct register as'));
						return;
					}
					if (!emailVerification) {
						$this->request->data['User']['active'] = 1;
					}
					$this->request->data['User']['password'] = $this->UserAuth->makePassword($this->request->data['User']['password']);
					$this->User->save($this->request->data, false);
					$userId = $this->User->getLastInsertID();
					$user = $this->User->findById($userId);
					if (sendRegistrationMail && !emailVerification) {
						$this->User->sendRegistrationMail($user);
					}
					if (emailVerification) {
						$this->User->sendVerificationMail($user);
					}
					if (isset($this->request->data['User']['active']) && $this->request->data['User']['active']) {
						$this->UserAuth->login($user);
						$this->redirect('/');
					} else {
						$this->Session->setFlash(__('Please check your mail and confirm your registration'), 'flash/success');
						$this->redirect('/register');
					}
				}
			}
		} else {
			$this->Session->setFlash(__('Sorry new registration is currently disabled, please try again later'));
			$this->redirect('/login');
		}
	}
	/**
	 * Used to change the password by user
	 *
	 * @access public
	 * @return void
	 */
	public function changePassword()
	{
		$userId = $this->UserAuth->getUserId();
		if ($this->request->isPost()) {
			$this->User->set($this->data);
			// if ($this->User->RegisterValidate()) {
			if ($this->User->validates($this->data)) {
				$this->User->id = $userId;
				$this->request->data['User']['password'] = $this->UserAuth->makePassword($this->request->data['User']['password']);
				$this->User->save($this->request->data, false);
				$this->Session->setFlash(__('Password changed successfully'), 'flash/success');
				$this->redirect('/logout');
			}
		}
	}
	/**
	 * Used to change the user password by Admin
	 *
	 * @access public
	 * @param integer $userId user id of user
	 * @return void
	 */
	public function changeUserPassword($userId = null)
	{
		if (!empty($userId)) {
			$name = $this->User->getNameById($userId);
			$this->set('name', $name);
			if ($this->request->isPost()) {
				$this->User->set($this->data);
				if ($this->User->RegisterValidate()) {
					$this->User->id = $userId;
					$this->request->data['User']['password'] = $this->UserAuth->makePassword($this->request->data['User']['password']);
					$this->User->save($this->request->data, false);
					$this->Session->setFlash(__('Password for %s changed successfully', $name));
					$this->redirect('/admin/allUsers');
				}
			}
		} else {
			$this->redirect('/admin/allUsers');
		}
	}
	/**
	 * Used to add user on the site by Admin
	 *
	 * @access public
	 * @return void
	 */
	public function addUser()
	{
		$this->loadModel('SalesPerson');

		if ($this->request->isPost()) {
			$this->User->create();
			$this->request->data['SalesPerson']['user_group_id'] = $this->request->data['User']['user_group_id'];
			$this->request->data['SalesPerson']['created_at'] = $this->current_datetime();
			$this->request->data['SalesPerson']['updated_at'] = $this->current_datetime();
			$this->request->data['SalesPerson']['created_by'] = $this->UserAuth->getUserId();
			if ($this->User->saveAll($this->request->data)) {

				$this->request->data['User']['id'] = $this->User->id;
				$this->request->data['User']['password'] = $this->UserAuth->makePassword($this->request->data['User']['password']);
				$this->User->save($this->request->data);

				if ($this->request->data['User']['user_group_id'] == 1034) {
					$this->loadModel('SalesPerson');
					$sales_person_id = $this->SalesPerson->id;
					$this->loadModel('DistUserMapping');
					$data['office_id'] = $this->request->data['SalesPerson']['office_id'];
					$data['dist_distributor_id'] = $this->request->data['User']['dist_distributor_id'];
					$data['sales_person_id'] = $sales_person_id;

					$this->DistUserMapping->create();
					$this->DistUserMapping->save($data);
				}
				$this->Session->setFlash(__('The user is successfully added'), 'flash/success');
				$this->redirect('/admin/allUsers');
			}
		}
		$designations = $this->SalesPerson->Designation->find('list', array('fields' => array('Designation.id', 'Designation.designation_name'), 'order' => array('Designation.designation_name' => 'ASC')));
		$offices = $this->SalesPerson->Office->find('list', array('fields' => array('Office.id', 'Office.office_name'), 'order' => array('Office.office_name' => 'ASC')));
		$userGroups = $this->UserGroup->find('list', array('order' => array('id' => 'ASC')));
		$this->set(compact('designations', 'offices', 'userGroups'));
	}
	/**
	 * Used to edit user on the site by Admin
	 *
	 * @access public
	 * @param integer $userId user id of user
	 * @return void
	 */
	public function editUser($userId = null){

		$this->loadModel('SalesPerson');
		$this->loadModel('DistUserMapping');
		$this->loadModel('DistDistributor');
		$this->loadModel('UserHistory');
		$sr_data = array();
		if ($this->request->isPut()) {
			//$this->dd($this->request->data);exit();
			$this->request->data['SalesPerson']['id'] = $this->request->data['User']['sales_person_id'];
			$this->request->data['SalesPerson']['updated_at'] = $this->current_datetime();
			$this->request->data['SalesPerson']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['SalesPerson']['user_group_id'] = $this->request->data['User']['user_group_id'];

			if (empty($this->request->data['User']['password'])) {
				unset($this->request->data['User']['password']);
			}

			if ($this->User->saveAll($this->request->data)) {

				$this->request->data['User']['id'] = $userId;
				if (empty($this->request->data['User']['password'])) {
					unset($this->request->data['User']['password']);
				} else {
					$this->request->data['User']['password'] = $this->UserAuth->makePassword($this->request->data['User']['password']);
				}
				$this->User->save($this->request->data);
				//$this->dd($this->User->id);exit();
				
				if ($this->request->data['User']['user_group_id'] == 1034) {
					$db_mapping_data = $this->DistUserMapping->find('first', array(
						'conditions' => array('DistUserMapping.sales_person_id' => $this->request->data['User']['sales_person_id']),
					));

					$dist_mapping_id = $db_mapping_data['DistUserMapping']['id'];
					$data['id'] = $dist_mapping_id;
					$data['dist_distributor_id'] = $this->request->data['User']['dist_distributor_id'];
					$data['office_id'] = $this->request->data['SalesPerson']['office_id'];
					$this->DistUserMapping->save($data);
				}

				//Office change UserHistory
				if ($this->User->id) {
					$data['user_id'] = $this->User->id;
					$data['name'] = $this->request->data['SalesPerson']['name'];
					$data['username'] = $this->request->data['User']['username'];
					$data['office_id'] = $this->request->data['SalesPerson']['office_id_old'];
					$data['designation_id'] = $this->request->data['SalesPerson']['office_id'];
					$data['created_at'] = $this->current_datetime();
					$this->UserHistory->save($data);
				}

				$this->Session->setFlash(__('The user is successfully updated'), 'flash/success');
				$this->redirect('/admin/allUsers');
			}
		} else {
			
			$user = $this->User->read(null, $userId);
			$this->request->data = null;
			if (!empty($user)) {
				$user['User']['password'] = '';
				$sales_persons = $this->SalesPerson->find('first', array('conditions' => array('SalesPerson.id' => $user['User']['sales_person_id'])));
				$office_id = $sales_persons['SalesPerson']['office_id'];
				//$this->dd($sales_persons);die();
				$user['SalesPerson'] = $sales_persons['SalesPerson'];
				$user['Designation'] = $sales_persons['Designation'];
				//$user['UserGroup'] = $sales_persons['UserGroup'];
				$e_sr_id = $sales_persons['SalesPerson']['dist_sales_representative_id'];
				//$e_sr_id[] = NULL;
				$user_group_id = $user['User']['user_group_id'];
				$user_list = array();
				if ($user_group_id == 1032) {
					$exist_users = $this->SalesPerson->find('all', array(
						'conditions' => array(
							'SalesPerson.dist_sales_representative_id != ' => array(NULL, $e_sr_id),
							'SalesPerson.office_id' => $office_id,
						)
					));

					foreach ($exist_users as $key => $value) {
						$user_list[$key] = $value['SalesPerson']['dist_sales_representative_id'];
					}
					/*pr($user_list);die();*/
					$this->loadModel('DistSalesRepresentative');
					$sr_list = $this->DistSalesRepresentative->find('all', array(
						'joins' => array(
							array(
								'table' => 'dist_distributors',
								'alias' => 'Dist',
								'conditions' => 'Dist.id=DistSalesRepresentative.dist_distributor_id',
								'type' => 'Left'
							),
						),
						'conditions' => array(
							//'DistSalesRepresentative.id'=>$sales_persons['SalesPerson']['dist_sales_representative_id'],
							//'NOT'=>array('DistSalesRepresentative.id'=>$user_list),
							'DistSalesRepresentative.id !=' => $user_list,
							'DistSalesRepresentative.office_id' => $office_id,
							'DistSalesRepresentative.is_active' => 1,
						),
					));
					//pr($sr_list);die();
					//echo $this->DistSalesRepresentative->getLastquery();
					foreach ($sr_list as $key => $value) {
						$sr_data[$value['DistSalesRepresentative']['id']] = $value['DistSalesRepresentative']['name'] . "(" . $value['DistDistributor']['name'] . ")";
					}

					//pr($sr_data);die();
					$this->set(compact('sr_data'));
				}
				if ($user_group_id == 1034) {

					$db_mapping_data = $this->DistUserMapping->find('first', array(
						'conditions' => array('DistUserMapping.sales_person_id' => $user['User']['sales_person_id']),
					));

					$distributor_id = $db_mapping_data['DistUserMapping']['dist_distributor_id'];

					$distributor_list = $this->DistDistributor->find('list', array(
						'conditions' => array('DistDistributor.office_id' => $office_id),
					));
					$this->set(compact('distributor_list', 'distributor_id', 'db_mapping_data'));
				}
				$this->request->data = $user;
			}
		}


		$designations = $this->SalesPerson->Designation->find('list', array('fields' => array('Designation.id', 'Designation.designation_name'), 'order' => array('Designation.designation_name' => 'ASC')));
		$offices = $this->SalesPerson->Office->find('list', array('fields' => array('Office.id', 'Office.office_name'), 'order' => array('Office.office_name' => 'ASC')));
		$userGroups = $this->UserGroup->find('list', array('order' => array('id' => 'ASC')));

		
		$this->set(compact('sr_data'));
		$this->set(compact('designations', 'offices', 'userGroups'));
	}
	/**
	 * Used to delete the user by Admin
	 *
	 * @access public
	 * @param integer $userId user id of user
	 * @return void
	 */
	public function deleteUser($userId = null)
	{
		if (!empty($userId)) {
			if ($this->request->isPost()) {
				if ($this->User->delete($userId)) {
					$this->Session->setFlash(__('User has been deleted successfully.'), 'flash/success');
				}
			}
			$this->redirect('/admin/allUsers');
		} else {
			$this->redirect('/admin/allUsers');
		}
	}
	/**
	 * Used to show dashboard of the user
	 *
	 * @access public
	 * @return array
	 */
	public function dashboard()
	{
		$userId = $this->UserAuth->getUserId();
		$user = $this->User->findById($userId);
		$this->set('user', $user);
	}
	/**
	 * Used to activate user by Admin
	 *
	 * @access public
	 * @param integer $userId user id of user
	 * @return void
	 */
	public function makeActive($userId = null)
	{
		if (!empty($userId)) {
			$user = array();
			$user['User']['id'] = $userId;
			$user['User']['active'] = 1;
			$this->User->save($user, false);
			$this->Session->setFlash(__('User is successfully activated'), 'flash/success');
		}
		$this->redirect('/allUsers');
	}
	/**
	 * Used to show access denied page if user want to view the page without permission
	 *
	 * @access public
	 * @return void
	 */
	public function accessDenied()
	{
	}
	/**
	 * Used to verify user's email address
	 *
	 * @access public
	 * @return void
	 */
	public function userVerification()
	{
		if (isset($_GET['ident']) && isset($_GET['activate'])) {
			$userId = $_GET['ident'];
			$activateKey = $_GET['activate'];
			$user = $this->User->read(null, $userId);
			if (!empty($user)) {
				if (!$user['User']['active']) {
					$password = $user['User']['password'];
					$theKey = $this->User->getActivationKey($password);
					if ($activateKey == $theKey) {
						$user['User']['active'] = 1;
						$this->User->save($user, false);
						if (sendRegistrationMail && emailVerification) {
							$this->User->sendRegistrationMail($user);
						}
						$this->Session->setFlash(__('Thank you, your account is activated now'));
					}
				} else {
					$this->Session->setFlash(__('Thank you, your account is already activated'));
				}
			} else {
				$this->Session->setFlash(__('Sorry something went wrong, please click on the link again'));
			}
		} else {
			$this->Session->setFlash(__('Sorry something went wrong, please click on the link again'));
		}
		$this->redirect('/login');
	}
	/**
	 * Used to send forgot password email to user
	 *
	 * @access public
	 * @return void
	 */
	public function forgotPassword()
	{
		if ($this->request->isPost()) {
			$this->User->set($this->data);
			if ($this->User->LoginValidate()) {
				$email  = $this->data['User']['email'];
				$user = $this->User->findByUsername($email);
				if (empty($user)) {
					$user = $this->User->findByEmail($email);
					if (empty($user)) {
						$this->Session->setFlash(__('Incorrect Email/Username or Password'));
						return;
					}
				}
				// check for inactive account
				if ($user['User']['id'] != 1 and $user['User']['active'] == 0) {
					$this->Session->setFlash(__('Your registration has not been confirmed yet please verify your email before reset password'));
					return;
				}
				$this->User->forgotPassword($user);
				$this->Session->setFlash(__('Please check your mail for reset your password'));
				$this->redirect('/login');
			}
		}
	}
	/**
	 *  Used to reset password when user comes on the by clicking the password reset link from their email.
	 *
	 * @access public
	 * @return void
	 */
	public function activatePassword()
	{
		if ($this->request->isPost()) {
			if (!empty($this->data['User']['ident']) && !empty($this->data['User']['activate'])) {
				$this->set('ident', $this->data['User']['ident']);
				$this->set('activate', $this->data['User']['activate']);
				$this->User->set($this->data);
				if ($this->User->RegisterValidate()) {
					$userId = $this->data['User']['ident'];
					$activateKey = $this->data['User']['activate'];
					$user = $this->User->read(null, $userId);
					if (!empty($user)) {
						$password = $user['User']['password'];
						$thekey = $this->User->getActivationKey($password);
						if ($thekey == $activateKey) {
							$user['User']['password'] = $this->data['User']['password'];
							$user['User']['password'] = $this->UserAuth->makePassword($user['User']['password']);
							$this->User->save($user, false);
							$this->Session->setFlash(__('Your password has been reset successfully'));
							$this->redirect('/login');
						} else {
							$this->Session->setFlash(__('Something went wrong, please send password reset link again'));
						}
					} else {
						$this->Session->setFlash(__('Something went wrong, please click again on the link in email'));
					}
				}
			} else {
				$this->Session->setFlash(__('Something went wrong, please click again on the link in email'));
			}
		} else {
			if (isset($_GET['ident']) && isset($_GET['activate'])) {
				$this->set('ident', $_GET['ident']);
				$this->set('activate', $_GET['activate']);
			}
		}
	}
	/*------------------- Mac id part----------------------------------*/
	//public function mac_free($user_id)
	public function mac_free()
	{
		if ($this->request->is('post') || $this->request->is('put')) {

			$data['mac_id'] = NULL;

			$this->User->id = $this->request->data['User']['id'];

			if ($this->User->save($data)) {
				$this->loadModel('MacFreeLog');
				$this->MacFreeLog->create();

				$saveNode['MacFreeLog']['user_id'] = $this->request->data['User']['id'];
				$saveNode['MacFreeLog']['before_mac'] = $this->request->data['User']['current_mac'];
				$saveNode['MacFreeLog']['mac_free_node'] = $this->request->data['User']['mac_node'];
				$saveNode['MacFreeLog']['created_at'] = $this->current_datetime();
				$saveNode['MacFreeLog']['created_by'] = $this->UserAuth->getUserId();

				$this->MacFreeLog->save($saveNode);

				$this->Session->setFlash(__('Mac free'));
			} else {
				$this->Session->setFlash(__('Sorry something went wrong, please click on the link again'));
			}
			$this->redirect('/admin/allUsers');
		}
	}
	/*------------------- get version list for user filter with version ----------------------------------*/
	public function get_version()
	{
		$target_apk = $this->request->data['target_apk'];
		$this->LoadModel('AppVersion');
		$version_list = $this->AppVersion->find('list', array(
			'conditions' => array('target_apk' => $target_apk),
			'fields' => array('AppVersion.name', 'AppVersion.name'),
			'order' => array('id desc'),
			'limit' => '5'
		));
		echo json_encode($version_list);
		exit;
	}

	public function download_xl()
	{
		ini_set('memory_limit', '2048M');
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes
		$params = $this->request->query['data'];
		$conditions = array();
		$user_groups = array(1028 => 'AE', 1029 => 'TSO', 1032 => 'SR', 1034 => 'DB User');
		if ($params) {

			if ($params['username'] != '') {
				$conditions[] = array('User.username LIKE' => '%' . $params['username'] . '%');
			}
			if ($params['user_type_id'] != '') {
				if ($params['user_type_id'] == 1) {

					$conditions[] = array('User.user_group_id' => array_keys($user_groups));
				} else {
					$conditions[] = array('NOT' => array('User.user_group_id' => array_keys($user_groups)));
				}
			}
			if ($params['user_group_id'] != '') {
				$conditions[] = array('User.user_group_id' => $params['user_group_id']);
			}
			if ($params['office_id'] != '') {
				$conditions[] = array('SalesPerson.office_id' => $params['office_id']);
			}

			if ($params['mac_id'] != '') {
				$conditions[] = array('User.mac_id' => $params['mac_id']);
			}
			if ($params['version'] != '') {
				$conditions[] = array('User.version' => $params['version']);
			}
		}
		// End Search

		if ($this->UserAuth->getOfficeParentId() != 0) {
			$conditions[] = array('SalesPerson.office_id' => $this->UserAuth->getOfficeId());
		}
		$this->User->recursive = -1;

		$users = $this->User->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'sales_people',
					'alias' => 'SalesPerson',
					'conditions' => 'SalesPerson.id=User.sales_person_id',
					'type' => 'left'
				),
				array(
					'table' => 'territories',
					'alias' => 'Territory',
					'conditions' => 'Territory.id=SalesPerson.territory_id',
					'type' => 'left'
				),
				array(
					'table' => 'offices',
					'alias' => 'Office',
					'conditions' => 'Office.id=SalesPerson.office_id',
					'type' => 'left'
				),
				array(
					'table' => 'user_groups',
					'alias' => 'UserGroup',
					'conditions' => 'UserGroup.id=User.user_group_id',
					'type' => 'left'
				),
			),
			'fields' => array(
				'User.*',
				'SalesPerson.name',
				'SalesPerson.contact',
				'Territory.name',
				'Office.office_name',
				'UserGroup.name'
			),
			'order' => array('User.id' => 'DESC')
		));

		$table = '';
		$table .= '<table border="1">';
		$table .= '<tr>';

		$table .= '<th class="text-center">id</th>
            <th class="text-center">Username</th>
            <th class="text-center">Sales Person ID</th>
            <th class="text-center">Full Name</th>
            <th class="text-center">Office</th>
            <th class="text-center">Territory</th>
            <th class="text-center">Contact</th>
            <th class="text-center">IMEI</th>
            <th class="text-center">Version</th>
            <th class="text-center">Group</th>
            <th class="text-center">Status</th>
        </tr>';
		foreach ($users as $row) :
			$table .= '<tr>';
			$table .= "<td class='text-center'>" . h($row['User']['id']) . "</td>";
			$table .= "<td>" . h($row['User']['username']) . "</td>";
			$table .= "<td class='text-center'>" . h($row['User']['sales_person_id']) . "</td>";
			$table .= "<td>" . h($row['SalesPerson']['name']) . "</td>";
			$table .= "<td class='text-center'>" . (isset($row['Office']['office_name']) != '' ? $row['Office']['office_name'] : '') . "</td>";
			$table .= "<td class='text-center'>" . (isset($row['Territory']['name']) != '' ? $row['Territory']['name'] : '') . "</td>";
			$table .= "<td class='text-center'>" . h($row['SalesPerson']['contact']) . "</td>";
			$table .= "<td class='text-center' style='mso-number-format:\@;'>" . h($row['User']['mac_id']) . "</td>";
			$table .= "<td class='text-center'>" . h($row['User']['version']) . "</td>";
			$table .= "<td class='text-center'>" . h($row['UserGroup']['name']) . "</td>";
			$table .= "<td class='text-center'>";
			if ($row['User']['active'] == 1) {
				$table .= "Active";
			} else {
				$table .= "Inactive";
			}
			$table .= "</td>";
			$table .= "</tr>";
		endforeach;
		$table .= '</table>';

		header('Content-Type:application/force-download');
		header('Content-Disposition: attachment; filename="Userlist.xls"');
		header("Cache-Control: ");
		header("Pragma: ");
		echo $table;
		$this->autoRender = false;
	}

	//--------------ae assing-----------\\

	public function ae_assing_to_so($sales_person_id, $office_id){
		$this->loadModel('SalesPerson');
		$this->loadModel('AeSoMapping');
		$message = '';

		if ($this->request->isPut()) {
			unset($this->request->data['SalesPerson']['sales_person_name']);
			//echo '<pre>';print_r($this->request->data);exit;

			$SalesPerson['id'] = $this->request->data['SalesPerson']['id'];
			$SalesPerson['ae_id'] = $this->request->data['SalesPerson']['ae_id'];
			$this->SalesPerson->save($SalesPerson);

			$aesomapping['so_id'] = $this->request->data['SalesPerson']['id'];
			$aesomapping['ae_id'] = $this->request->data['SalesPerson']['ae_id'];
			$aesomapping['is_assign'] = 1;
			$aesomapping['assign_date'] = $this->current_date();
			$aesomapping['created_at'] = $this->current_datetime();
			$aesomapping['created_by'] = $this->UserAuth->getUserId();
			$this->AeSoMapping->create();
			$this->AeSoMapping->save($aesomapping);

			$this->Session->setFlash(__('The user is successfully assinged.'), 'flash/success');
			$this->redirect('/admin/allUsers');
		} else {
			$options = array('fields' => array('SalesPerson.*'), 'conditions' => array('SalesPerson.' . $this->SalesPerson->primaryKey => $sales_person_id), 'recursive' => 0);
			$this->request->data = $this->SalesPerson->find('first', $options);
		}


		$ae_list = $this->SalesPerson->find('list', array(
			'conditions' => array(
				'SalesPerson.office_id' => $office_id,
				'User.user_group_id' => 1028,
				'User.active' => 1,
			),
			'fields' => array('SalesPerson.id', 'SalesPerson.name'),
			'recursive' => 0
		));

		$exitingInfo = $this->AeSoMapping->find('first', array(
			'conditions' => array(
				'AeSoMapping.so_id' => $sales_person_id,
			),
			'order' => array('AeSoMapping.id' => 'DESC')
		));

		//echo '<pre>';print_r($exitingInfo);exit;

		$this->set(compact('ae_list', 'exitingInfo', 'message'));
	}

	public function ae_deassing_to_so($sales_person_id, $office_id)
	{

		$this->loadModel('SalesPerson');
		$this->loadModel('AeSoMapping');

		$message = '';

		if ($this->request->isPut()) {

			unset($this->request->data['SalesPerson']['sales_person_name']);
			//echo '<pre>';print_r($this->request->data);exit;

			$SalesPerson['id'] = $this->request->data['SalesPerson']['id'];
			$SalesPerson['ae_id'] = 0;
			$this->SalesPerson->save($SalesPerson);

			// add territory assign history
			$aesomapping['id'] = $this->request->data['SalesPerson']['ae_so_mapping_id'];
			$aesomapping['is_assign'] = 0;
			$aesomapping['deassign_date'] = $this->current_date();
			$aesomapping['updated_at'] = $this->current_datetime();
			$aesomapping['updated_by'] = $this->UserAuth->getUserId();

			$this->AeSoMapping->save($aesomapping);

			$this->Session->setFlash(__('The user is successfully de-assinged.'), 'flash/success');
			$this->redirect('/admin/allUsers');
		} else {
			$options = array('fields' => array('SalesPerson.*'), 'conditions' => array('SalesPerson.' . $this->SalesPerson->primaryKey => $sales_person_id), 'recursive' => 0);
			$this->request->data = $this->SalesPerson->find('first', $options);
		}

		$exitingInfo = $this->AeSoMapping->find('first', array(
			'conditions' => array(
				'AeSoMapping.so_id' => $sales_person_id,
			),
			'order' => array('AeSoMapping.id' => 'DESC')
		));


		$ae_list = $this->SalesPerson->find('list', array(
			'conditions' => array(
				'SalesPerson.id' => $exitingInfo['AeSoMapping']['ae_id'],
			),
			'fields' => array('SalesPerson.id', 'SalesPerson.name'),
			'recursive' => 0
		));


		//echo '<pre>';print_r($exitingInfo);exit;

		$this->set(compact('ae_list', 'exitingInfo', 'message'));
	}

	


}
