<?php
App::uses('AppModel', 'Model');
/**
 * MemoNotification Model
 *
 */
class MemoNotification extends AppModel {

/**
 * Display field
 *
 * @var string
 */
public function filter($params, $conditions) {   

	$conditions = array();

	if(isset($params['MemoNotification.date_to']) && isset($params['MemoNotification.date_from']))
	{
		$conditions[] = array('CONVERT(date,MemoNotification.memo_date) BETWEEN ? AND ?' => array(date("Y-m-d",strtotime($params['MemoNotification.date_from'])),date("Y-m-d",strtotime($params['MemoNotification.date_to']))));
	}
	if(isset($params['MemoNotification.thana_id']))
	{
		$conditions[] = array('Thana.id' => $params['MemoNotification.thana_id']);
	}
	elseif(isset($params['MemoNotification.territory_id']))
	{
		$conditions[] = array('Territory.id' => $params['MemoNotification.territory_id']);
	}
	elseif(isset($params['MemoNotification.office_id']))
	{
		$conditions[] = array('Office.id' => $params['MemoNotification.office_id']);
	}
	elseif(isset($params['MemoNotification.region_office_id']))
	{
		$conditions[] = array('Office.parent_office_id' => $params['MemoNotification.region_office_id']);
	}
	return $conditions;
}		
/**
 * belongsTo associations
 *
 * @var array
 */
public $belongsTo = array(		
	'SalesPerson' => array(
		'className' => 'SalesPerson',
		'foreignKey' => 'sales_person_id',
		'conditions' => '',
		'fields' => 'name,office_id',
		'order' => ''
		),
	'Outlet' => array(
		'className' => 'Outlet',
		'foreignKey' => 'outlet_id',
		'conditions' => '',
		'fields' => 'name',
		'order' => ''
		)
	);



}
