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

// Routes for standard actions

Router::connect('/admin/login', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'login'));
Router::connect('/logout', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'logout'));
Router::connect('/forgotPassword', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'forgotPassword'));
Router::connect('/activatePassword/*', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'activatePassword'));
Router::connect('/register', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'register'));
Router::connect('/changePassword', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'changePassword'));
Router::connect('/changeUserPassword/*', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'changeUserPassword'));
Router::connect('/admin/duplicate_user_check', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'duplicate_user_check'));
Router::connect('/admin/duplicate_usercode_check', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'duplicate_usercode_check'));
Router::connect('/admin/addUser', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'addUser'));
Router::connect('/admin/editUser/*', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'editUser'));
Router::connect('/admin/deleteUser/*', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'deleteUser'));
Router::connect('/admin/viewUser/*', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'viewUser'));
Router::connect('/admin/territoryTag/*', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'territory_tag'));
Router::connect('/admin/territory_deassigned/*', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'territory_deassigned'));
Router::connect('/userVerification/*', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'userVerification'));
Router::connect('/admin/allUsers/*', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'index'));
Router::connect('/admin', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'dashboard'));
//Router::connect('/admin/user_dashboard', array('controller' => 'dashboards', 'action' => 'admin_index'));
Router::connect('/admin/permissions', array('plugin' => 'usermgmt', 'controller' => 'user_group_permissions', 'action' => 'index'));
Router::connect('/update_permission', array('plugin' => 'usermgmt', 'controller' => 'user_group_permissions', 'action' => 'update'));
Router::connect('/accessDenied', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'accessDenied'));
Router::connect('/myprofile', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'myprofile'));
Router::connect('/admin/allGroups', array('plugin' => 'usermgmt', 'controller' => 'user_groups', 'action' => 'index'));
Router::connect('/admin/addGroup', array('plugin' => 'usermgmt', 'controller' => 'user_groups', 'action' => 'addGroup'));
Router::connect('/admin/editGroup/*', array('plugin' => 'usermgmt', 'controller' => 'user_groups', 'action' => 'editGroup'));
Router::connect('/admin/deleteGroup/*', array('plugin' => 'usermgmt', 'controller' => 'user_groups', 'action' => 'deleteGroup'));
Router::connect('/admin/groupPermission/*', array('plugin' => 'usermgmt', 'controller' => 'user_group_permissions', 'action' => 'groupWisePermission'));
Router::connect('/admin/mac_free/*', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'mac_free'));
Router::connect('/get_version', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'get_version'));

Router::connect('/users/download_xl', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'download_xl'));
Router::connect('/delete_permission', array('plugin' => 'usermgmt', 'controller' => 'user_group_permissions', 'action' => 'delete_permission'));

Router::connect('/admin/ae_assing_to_so/*', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'ae_assing_to_so'));
Router::connect('/admin/ae_deassing_to_so/*', array('plugin' => 'usermgmt', 'controller' => 'users', 'action' => 'ae_deassing_to_so'));

