<?php
App::uses('AppModel', 'Model');
/**
 * DistSrVisitPlanList Model
 *
 * @property Market $Market
 */
class DistSrVisitPlanList extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';

	public function filter($params, $conditions) {
		$conditions = array();
		//pr($params);
		if (!empty($params['DistSrVisitPlanList.office_id'])) {
			$conditions[] = array('DistSrVisitPlanList.aso_id' => $params['DistSrVisitPlanList.office_id']);
		}
		if (!empty($params['DistSrVisitPlanList.distributor_id'])) {
			$conditions[] = array('DistSrVisitPlanList.dist_distributor_id' => $params['DistSrVisitPlanList.distributor_id']);
		}
		if (!empty($params['DistSrVisitPlanList.market_id'])) {
			$conditions[] = array('DistSrVisitPlanList.dist_market_id' => $params['DistSrVisitPlanList.market_id']);
		}
		if (!empty($params['DistSrVisitPlanList.dist_route_id'])) {
			$conditions[] = array('DistSrVisitPlanList.dist_route_id' => $params['DistSrVisitPlanList.dist_route_id']);
		}
		if (!empty($params['DistSrVisitPlanList.sr_id'])) {
			$conditions[] = array('DistSrVisitPlanList.sr_id' => $params['DistSrVisitPlanList.sr_id']);
		}
		if (!empty($params['DistSrVisitPlanList.date_from'])) {
			$conditions[] = array('DistSrVisitPlanList.visited_date >=' => $params['DistSrVisitPlanList.date_from']);
		}
		if (!empty($params['DistSrVisitPlanList.visit_status'])) {
			$conditions[] = array('DistSrVisitPlanList.visit_status' => $params['DistSrVisitPlanList.visit_status']);
		}
		//pr($conditions);die();
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
		'DistMarket' => array(
			'className' => 'DistMarket',
			'foreignKey' => 'dist_market_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		),
		'DistOutlet' => array(
			'className' => 'DistOutlet',
			'foreignKey' => 'dist_outlet_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		),
		'DistSalesRepresentative' => array(
			'className' => 'DistSalesRepresentative',
			'foreignKey' => 'sr_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => '',
		),
		'DistDistributor' => array(
			'className' => 'DistDistributor',
			'foreignKey' => 'dist_distributor_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		),
		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'aso_id',
			'conditions' => '',
			'fields' => 'office_name',
			'order' => ''
		),
		'DistRoute' => array(
			'className' => 'DistRoute',
			'foreignKey' => 'dist_route_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		)
	);


}
