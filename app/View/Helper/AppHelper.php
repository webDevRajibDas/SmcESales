<?php

/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class AppHelper extends Helper
{

	var $helpers = array('Session');
	public $uses = array('Usermgmt.UserGroup');

	public function menu_permission($controller = '', $action = '')
	{
		App::import("Model", "Usermgmt.UserGroup");
		$UserGroup = new UserGroup();

		$group_id = $this->Session->read('UserAuth.User.user_group_id');
		$permissions = $UserGroup->getPermissions($group_id);

		/*
		$username = $this->Session->read('UserAuth.User.username');
	
		if($username=='13003')
		{
			echo "<pre>";
			print_r($permissions); exit;
		}
		*/

		$access = str_replace(' ', '', ucwords(str_replace('_', ' ', $controller))) . '/' . $action;
		if (in_array($access, $permissions)) {
			return true;
		}
		return false;
	}


	public function dateformat($date = '')
	{
		if ($date == '0000-00-00' or $date == NULL)
			return '';
		else
			return date('d-M-Y', strtotime($date));
	}

	public function datetimeformat($datetime = '')
	{
		if ($datetime == '0000-00-00 00:00:00' or $datetime == NULL)
			return '';
		else
			return date('d-M, Y  g:ia', strtotime($datetime));
	}

	public function expire_dateformat($date = '')
	{
		if ($date == '0000-00-00' or $date == NULL)
			return '';
		else
			return date('M-y', strtotime($date));
	}
	public function unit_convertfrombase($product_id = '', $measurement_unit_id = '', $qty = '')
	{
		App::import("Model", "ProductMeasurement");
		$product_measurement = new ProductMeasurement();
		$unit_info = $product_measurement->find('first', array(
			'conditions' => array(
				'ProductMeasurement.product_id' => $product_id,
				'ProductMeasurement.measurement_unit_id' => $measurement_unit_id
			)
		));
		$number = $qty;
		if (!empty($unit_info)) {
			$number = $qty / $unit_info['ProductMeasurement']['qty_in_base'];
			//echo sprintf('%.2f', ($qty*10.0)/10.0);
			//echo ' ';
			//$number = 100;
			$decimals = 2;
			//$number = 221.12345;
			$number = $number * pow(10, $decimals);
			$number = intval($number);
			$number = $number / pow(10, $decimals);



			return sprintf('%.2f', $number);
		} else {
			return sprintf('%.2f', $number);
		}
	}

	/**
	 * Used to get understandable name of Controller action 
	 *
	 * @access public
	 * @return string
	 */
	public function getActionName($action)
	{
		$re_action = "";
		if ($action == "admin_index") {
			$re_action = "View List";
		} else {
			$re_action = ucfirst(str_replace('admin_', '', $action));
		}

		$re_action = str_replace('_', ' ', $re_action);

		return $re_action;
	}
}
