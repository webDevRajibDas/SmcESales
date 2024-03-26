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

class UserGroupPermissionsController extends UserMgmtAppController
{

	var $uses = array('Usermgmt.UserGroupPermission', 'Usermgmt.UserGroup');
	var $components = array('Usermgmt.ControllerList', 'RequestHandler', 'Paginator');
	/**
	 * Used to display all permissions of site by Admin
	 *
	 * @access public
	 * @return array
	 */
	public function index()
	{

		$c = -2;
		if (isset($_GET['c']) && $_GET['c'] != '') {
			$c = $_GET['c'];
		}
		$this->set('c', $c);
		$allControllers = $this->ControllerList->getControllers();

		$this->set('allControllers', $allControllers);
		$allControllersWithProperName = $allControllers;


		$this->set('allControllersWithProperName', $allControllersWithProperName);


		if ($c > -2) {
			$con = array();
			$conAll = $this->ControllerList->get();

			if ($c == -1) {
				$con = $conAll;
				$user_group_permissions = $this->UserGroupPermission->find('all', array('order' => array('controller', 'action')));
			} else {

				$selectedCon = $this->ControllerList->getMappingConList($allControllers[$c]);
				$user_group_permissions = $this->UserGroupPermission->find('all', array('order' => array('controller', 'action'), 'conditions' => array('controller' => $selectedCon)));

				$con[$selectedCon] = (isset($conAll[$selectedCon])) ? $conAll[$selectedCon] : array();
			}


			$optionalMethods = $this->ControllerList->getOptionalMethodList();

			if (array_key_exists($selectedCon, $optionalMethods)) {

				$optionalConMethod = $optionalMethods[$selectedCon];

				foreach ($con[$selectedCon] as $k => $v) {
					if (in_array($v, $optionalConMethod)) {
						unset($con[$selectedCon][$k]);
					}
				}
			}



			foreach ($user_group_permissions as $row) {
				$cont = $row['UserGroupPermission']['controller'];
				$act = $row['UserGroupPermission']['action'];
				$ugname = $row['UserGroup']['name'];
				$allowed = $row['UserGroupPermission']['allowed'];
				$con[$cont][$act][$ugname] = $allowed;
			}

			$this->set('controllers', $con);
			$result = $this->UserGroup->getGroupNames();
			$groups = '';
			for ($i = 0; $i < count($result); $i++) {
				$groups .= ($i == 0) ? $result[$i] : "," . $result[$i];
			}
			$this->set('user_groups', $result);
			$this->set('groups', $groups);
		}
	}
	/**
	 *  Used to update permissions of site using Ajax by Admin
	 *
	 * @access public
	 * @return integer
	 */
	/*
	public function update() {
		$this->autoRender = false;
		$controller=$this->params['data']['controller'];
		$action=$this->params['data']['action'];
		$result=$this->UserGroup->getGroupNamesAndIds();
		$success=0;
		foreach ($result as $row) {
			if (isset($this->params['data'][$row['name']])) {
				$res=$this->UserGroupPermission->find('first',array('conditions' => array('controller'=>$controller,'action'=>$action,'user_group_id'=>$row['id'])));
				if (empty($res)) {
					$data=array();
					$data['UserGroupPermission']['user_group_id']=$row['id'];
					$data['UserGroupPermission']['controller']=$controller;
					$data['UserGroupPermission']['action']=$action;
					$data['UserGroupPermission']['allowed']=$this->params['data'][$row['name']];
					$data['UserGroupPermission']['id']=null;
					$rtn=$this->UserGroupPermission->save($data);
					if ($rtn) {
						$success=1;
					}
				} else {
					if ($this->params['data'][$row['name']] !=$res['UserGroupPermission']['allowed']) {
						$data=array();
						$data['UserGroupPermission']['allowed']=$this->params['data'][$row['name']];
						$data['UserGroupPermission']['id']=$res['UserGroupPermission']['id'];
						$rtn=$this->UserGroupPermission->save($data);
						if ($rtn) {
							$success=1;
						}
					} else {
						 $success=1;
					}
				}
			}
		}
		echo $success;
		$this->__deleteCache();
	}
	*/

	public function update()
	{
		$this->autoRender = false;
		$controller = $this->params['data']['controller'];
		$action = $this->params['data']['action'];
		$result = $this->UserGroup->getGroupNamesAndIds();
		$success = 0;
		foreach ($result as $row) {
			$group_rename = str_replace(' ', '_', $row['name']);
			if (isset($this->params['data'][$group_rename])) {
				$res = $this->UserGroupPermission->find('first', array('conditions' => array('controller' => $controller, 'action' => $action, 'user_group_id' => $row['id'])));
				if (empty($res)) {
					$data = array();
					$data['UserGroupPermission']['user_group_id'] = $row['id'];
					$data['UserGroupPermission']['controller'] = $controller;
					$data['UserGroupPermission']['action'] = $action;
					$data['UserGroupPermission']['allowed'] = $this->params['data'][$group_rename];
					$data['UserGroupPermission']['id'] = null;
					$rtn = $this->UserGroupPermission->save($data);
					if ($rtn) {
						$success = 1;
					}
				} else {
					if ($this->params['data'][$group_rename] != $res['UserGroupPermission']['allowed']) {
						$data = array();
						$data['UserGroupPermission']['allowed'] = $this->params['data'][$group_rename];
						$data['UserGroupPermission']['id'] = $res['UserGroupPermission']['id'];
						$rtn = $this->UserGroupPermission->save($data);
						if ($rtn) {
							$success = 1;
						}
					} else {
						$success = 1;
					}
				}
			}
		}
		$this->__deleteCache();
		// $ch = file_get_contents(REPORT_URL . "delete_permission");

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, REPORT_URL . "delete_permission");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
		$data = curl_exec($ch);
		curl_close($ch);
		if ($data == 1) {
			$success = 1;
		}
		echo $success;
	}



	/**
	 * Used to delete cache of permissions and used when any permission gets changed by Admin
	 *
	 * @access private
	 * @return void
	 */
	private function __deleteCache()
	{
		$iterator = new RecursiveDirectoryIterator(CACHE);
		foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file) {
			$path_info = pathinfo($file);
			if ($path_info['dirname'] == ROOT . DS . "app" . DS . "tmp" . DS . "cache" && $path_info['basename'] != '.svn') {
				if (!is_dir($file->getPathname())) {
					unlink($file->getPathname());
				}
			}
		}
	}

	public function groupWisePermission($groupId = null)
	{

		$mappingConName = $this->ControllerList->getMappingConList();
		$this->set('mappingConName', $mappingConName);


		$groupPermissionData = $this->UserGroupPermission->find('all', array('conditions' => array('user_group_id' => $groupId), 'recursive' => -1));
		$allControllers = array();

		$groupPermissionDataCombine = Hash::combine(
			$groupPermissionData,
			array('%s:%s', '{n}.UserGroupPermission.controller', '{n}.UserGroupPermission.action'),
			'{n}.UserGroupPermission.id',
			'{n}.UserGroupPermission.allowed'
		);
		if (!empty($groupPermissionDataCombine))
			$allowedPermissions = $groupPermissionDataCombine[1];


		$controllerClasses = App::objects('Controller');
		$superParentActions = get_class_methods('Controller');
		$parentActions = get_class_methods('AppController');


		$i = 0;
		//$parentActionsDefined=$this->_removePrivateActions($parentActions);
		//$parentActionsDefined = array_diff($parentActionsDefined, $superParentActions);
		//$controllers= array();
		foreach ($controllerClasses as $controller) {

			if ($controller == 'ApiDataRetrivesController' || $controller == 'AppController' || $controller == 'UsersController' || $controller == 'MemosController123')
				continue;
			$controllername = str_replace('Controller', '', $controller);
			$actions = $this->__getControllerMethods($controllername, $superParentActions, $parentActions);

			if (!empty($actions)) {
				$data['controller'] = $controllername;
				$data['action'] = $actions;
				$allControllers[] = $data;
			}
		}



		/* Removing Method start */
		$optionalControllers = $this->ControllerList->getOptionalConList();
		$optionalMethods = $this->ControllerList->getOptionalMethodList();
		foreach ($allControllers as $k => $v) {
			$selectedCon = $v['controller'];

			if (in_array($selectedCon, $optionalControllers)) {
				unset($allControllers[$k]);
				continue;
			}

			if (array_key_exists($selectedCon, $optionalMethods)) {

				$optionalConMethod = $optionalMethods[$selectedCon];

				foreach ($v['action'] as $k2 => $v2) {
					if (in_array($v2, $optionalConMethod)) {
						unset($allControllers[$k]['action'][$k2]);
					}
				}
			}
		}

		/* Removing Method end */

		/* re-arranging Menu/Controller Order Start */

		$menuOrderConList = $this->ControllerList->getMenuOrderConList();
		$allControllersOrderData = array();



		foreach ($allControllers as $eachConKey => $eachConVal) {
			$MenuOrder = "";
			$conName = $eachConVal['controller'];
			$MenuOrder = array_search($conName, $menuOrderConList);
			if ($MenuOrder) {
				$allControllersOrderData[$MenuOrder] = $eachConVal;
			}
		}
		ksort($allControllersOrderData);
		$allControllers = $allControllersOrderData;

		/* re-arranging Menu/Controller Order End */



		$this->set(compact('allControllers', 'groupId', 'allowedPermissions'));
		if ($this->request->is('post')) {
			$saveData = array();
			$i = 0;
			//$groupPermissionData = $this->UserGroupPermission->find('all', array('conditions' => array('user_group_id' => $this->request->data['user_group_id']), 'recursive' => -1));
			$groupPermissionData = Hash::combine(
				$groupPermissionData,
				array('%s:%s', '{n}.UserGroupPermission.controller', '{n}.UserGroupPermission.action'),
				'{n}.UserGroupPermission.id'
			);

			foreach ($allControllers as $key => $val) {
				foreach ($val['action'] as $akey => $aval) {
					$checkboxVal[$val['controller'] . ':' . $aval] = $val['controller'] . ':' . $aval;
				}
			}



			foreach ($checkboxVal as $checkValue) {

				$dataExp = explode(':', $checkValue);

				if (isset($this->request->data['check'][$checkValue])) {

					//allowed=1
					if (isset($groupPermissionData[$checkValue])) {
						//update
						$saveData[$i]['id'] = $groupPermissionData[$checkValue];
					} else {
						//insert
						$saveData[$i]['id'] = '';
					}
					$saveData[$i]['controller'] = $dataExp[0];
					$saveData[$i]['action'] = $dataExp[1];
					$saveData[$i]['user_group_id'] = $groupId; // $this->request->data['user_group_id'];
					$saveData[$i]['allowed'] = 1;
					$i++;
				} else {
					//allowed=0
					if (isset($groupPermissionData[$checkValue])) {
						//update
						$saveData[$i]['id'] = $groupPermissionData[$checkValue];
					} else {
						//insert
						$saveData[$i]['id'] = '';
					}
					$saveData[$i]['controller'] = $dataExp[0];
					$saveData[$i]['action'] = $dataExp[1];
					$saveData[$i]['user_group_id'] = $groupId; // $this->request->data['user_group_id'];
					$saveData[$i]['allowed'] = 0;
					$i++;
				}
			}

			if ($this->UserGroupPermission->saveAll($saveData)) {
				$this->Session->setFlash(__('permission is saved'), 'flash/success');
				$this->__deleteCache();
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_URL, REPORT_URL . "delete_permission");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
				$data = curl_exec($ch);
				curl_close($ch);
				$this->redirect('/admin/groupPermission/' . $groupId . ''); //$this->request->data['user_group_id']
			} else {
				$this->Session->setFlash(__('permission could not be saved. Please, try again.'), 'flash/error');
			}
		}
	}



	private function _removePrivateActions($actions)
	{
		foreach ($actions as $k => $v) {
			if ($v{
				0} == '_') {
				unset($actions[$k]);
			}
		}
		return $actions;
	}
	private function __getControllerMethods($controllername, $superParentActions, $parentActions, $p = null)
	{
		if (empty($p)) {
			App::import('Controller', $controllername);
		} else {
			App::import('Controller', $p . '.' . $controllername);
		}
		$actions = get_class_methods($controllername . "Controller");
		if (!empty($actions)) {
			$actions = $this->_removePrivateActions($actions);
			$actions = ($controllername == 'App') ? array_diff($actions, $superParentActions) : array_diff($actions, $parentActions);
		}
		return $actions;
	}

	public function delete_permission()
	{
		$this->autoRender = false;
		$this->__deleteCache();
		return '1';
	}
}
