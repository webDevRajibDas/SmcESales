<?php
App::uses('AppModel', 'Model');
/**
 * VisitPlanList Model
 *
 * @property Market $Market
 */
class VisitPlanList extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';

	public function filter($params, $conditions) {
		$conditions = array();
		if (!empty($params['VisitPlanList.office_id'])) {
			$conditions[] = array('Territory.office_id' => $params['VisitPlanList.office_id']);
		}
		if (!empty($params['VisitPlanList.territory_id'])) {
			$conditions[] = array('Market.territory_id' => $params['VisitPlanList.territory_id']);
		}
		if (!empty($params['VisitPlanList.market_id'])) {
			$conditions[] = array('VisitPlanList.market_id' => $params['VisitPlanList.market_id']);
		}
		if (!empty($params['VisitPlanList.date'])) {
			$conditions[] = array('VisitPlanList.visit_plan_date' => $params['VisitPlanList.date']);
		}
		if (!empty($params['VisitPlanList.visit_status'])) {
			$conditions[] = array('VisitPlanList.visit_status' => $params['VisitPlanList.visit_status']);
		}

		return $conditions;
	}
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
		'so_id' => array(
			'notMustBeEmpty' => array(
				'rule' 		=> 'notEmpty',
				'message'   => 'SO field required.'
			)
		),
		'visit_plan_date' => array(
			'notMustBeEmpty' => array(
				'rule' 		=> 'notEmpty',
				'message'   => 'Visit Plan Date field required.'
			)
		),
		'market_id' => array(
			'notMustBeEmpty' => array(
				'rule' 		=> 'notEmpty',
				'message'   => 'Market field required.'
			)
		)
	);
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Market' => array(
			'className' => 'Market',
			'foreignKey' => 'market_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Aso' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'aso_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'So' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'so_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);


}
