<?php
App::uses('AppModel', 'Model');
/**
 * VisitedOutlet Model
 *
 * @property Outlet $Outlet
 * @property So $So
 */
class VisitedOutlet extends AppModel {
 public $useTable = 'visited_outlets';

 public function filter($params, $conditions) {   
		
		$conditions = array();
		
        if(isset($params['VisitedOutlet.date_to']) && isset($params['VisitedOutlet.date_from']))
		{
			$conditions[] = array('CONVERT(date,VisitedOutlet.visited_at) BETWEEN ? AND ?' => array(date("Y-m-d",strtotime($params['VisitedOutlet.date_from'])),date("Y-m-d",strtotime($params['VisitedOutlet.date_to']))));
		}
		if(isset($params['VisitedOutlet.thana_id']))
		{
			$conditions[] = array('Thana.id' => $params['VisitedOutlet.thana_id']);
		}
		elseif(isset($params['VisitedOutlet.territory_id']))
		{
			$conditions[] = array('Territory.id' => $params['VisitedOutlet.territory_id']);
		}
		elseif(isset($params['VisitedOutlet.office_id']))
		{
			$conditions[] = array('Office.id' => $params['VisitedOutlet.office_id']);
		}
		elseif(isset($params['VisitedOutlet.region_office_id']))
		{
			$conditions[] = array('Office.parent_office_id' => $params['VisitedOutlet.region_office_id']);
		}
        return $conditions;
    }

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Outlet' => array(
			'className' => 'Outlet',
			'foreignKey' => 'outlet_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'SalesPerson' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'so_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
