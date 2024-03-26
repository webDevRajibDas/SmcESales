<?php
App::uses('AppModel', 'Model');
/**
 * DayClose Model
 *
 */
class DayClose extends AppModel {

/**
 * Display field
 *
 * @var string
 */
		
	// data filter
	public function filter($params, $conditions) {   
		
		$conditions = array();	
		if(CakeSession::read('Office.parent_office_id') != 0)
		{
			$conditions[] = array('SalesPerson.office_id' => CakeSession::read('Office.id'));
		}		
		if (!empty($params['DayClose.office_id'])) {
            $conditions[] = array('Territory.office_id' => $params['DayClose.office_id']);
        }
		if (!empty($params['DayClose.territory_id'])) {
            $conditions[] = array('DayClose.territory_id' => $params['DayClose.territory_id']);
        }
		if (!empty($params['DayClose.sales_person_id'])) {
            $conditions[] = array('DayClose.sales_person_id' => $params['DayClose.sales_person_id']);
        }		
		if (isset($params['DayClose.date_from'])!='') {
            $conditions[] = array('DayClose.closing_date >=' => Date('Y-m-d',strtotime($params['DayClose.date_from'])));
        }
		if (isset($params['DayClose.date_to'])!='') {
            $conditions[] = array('DayClose.closing_date <=' => Date('Y-m-d',strtotime($params['DayClose.date_to'])));
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
		'Territory' => array(
			'className' => 'Territory',
			'foreignKey' => 'territory_id',
			'conditions' => '',
			'fields' => 'name,office_id',
			'order' => ''
		)
	);


}
