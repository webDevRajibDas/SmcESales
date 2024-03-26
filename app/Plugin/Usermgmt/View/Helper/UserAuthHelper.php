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
class UserAuthHelper extends AppHelper {

	/**
	 * This helper uses following helpers
	 *
	 * @var array
	 */
	var $helpers = array('Session');
	/**
	 * Used to check whether user is logged in or not
	 *
	 * @access public
	 * @return boolean
	 */
	public function isLogged() {
		return ($this->getUserId() !== null);
	}
	/**
	 * Used to get user from session
	 *
	 * @access public
	 * @return array
	 */
	public function getUser() {
		return $this->Session->read('UserAuth');
	}
	/**
	 * Used to get user id from session
	 *
	 * @access public
	 * @return integer
	 */
	public function getUserId() {
		return $this->Session->read('UserAuth.User.id');
	}
	/**
	 * Used to get Office id from session
	 *
	 * @access public
	 * @return integer
	 */
	public function getOfficeId() {
		return $this->Session->read('UserAuth.SalesPerson.office_id');
	}
	/**
	 * Used to get Office id from session
	 *
	 * @access public
	 * @return integer
	 */
	public function getTerritoryId() {
		return $this->Session->read('UserAuth.SalesPerson.territory_id');
	}
	/**
	 * Used to get group id from session
	 *
	 * @access public
	 * @return integer
	 */
	public function getGroupId() {
		return $this->Session->read('UserAuth.User.user_group_id');
	}
	/**
	 * Used to get group name from session
	 *
	 * @access public
	 * @return string
	 */
	public function getGroupName() {
		return $this->Session->read('UserAuth.UserGroup.alias_name');
	}	
	/**
	 * Used to get user name from session
	 *
	 * @access public
	 * @return string
	 */
	public function getUserName() {
		return $this->Session->read('UserAuth.SalesPerson.name');
	}
	public function getOnlyUserName() {
		return $this->Session->read('UserAuth.User.username');
	}
	/**
	 * Used to get user email from session
	 *
	 * @access public
	 * @return string
	 */
	public function getUserEmail() {
		return $this->Session->read('UserAuth.User.email');
	}
}