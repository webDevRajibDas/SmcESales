<?php
App::uses('AppModel', 'Model');
/**
 * Memo Model
 *
 */
class MacFreeLog extends AppModel {
	// data filter
	public function filter($params, $conditions) {

		$conditions = array();

		//echo "<pre>";print_r($params);exit();
		if(CakeSession::read('Office.parent_office_id') != 0)
		{
			$conditions[] = array('SalesPeople.office_id' => CakeSession::read('Office.id'));
		}

		if (!empty($params['MacFreeLog.office_id'])) {
            $conditions[] = array('SalesPeople.office_id' => $params['MacFreeLog.office_id']);
        }

		if (!empty($params['MacFreeLog.user_group_id'])) {
            $conditions[] = array('User.user_group_id' => $params['MacFreeLog.user_group_id']);
        }

		if (!empty($params['MacFreeLog.username'])) {
            $conditions[] = array('User.username' => $params['MacFreeLog.username']);
        }

        if (isset($params['MacFreeLog.date_from']) != '') {
            $conditions[] = array('MacFreeLog.created_at >=' => Date('Y-m-d', strtotime($params['MacFreeLog.date_from'])));
        }

        if (isset($params['MacFreeLog.date_to']) != '') {
            $conditions[] = array('MacFreeLog.created_at <=' => Date('Y-m-d', strtotime($params['MacFreeLog.date_to'])));
        }
        return $conditions;
    } 
	
	
	public $belongsTo = array(		
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
		),
	);
	
	
	
}
