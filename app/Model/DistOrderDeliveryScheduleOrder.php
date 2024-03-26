<?php
App::uses('AppModel', 'Model');
/**
 * DistOrder Model
 *
 */
class DistOrderDeliveryScheduleOrder extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	
	

	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(		
		/*'DistSalesRepresentative' => array(
			'className' => 'DistOrderDeliverySchedule',
			'foreignKey' => 'sr_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
		),
		'Outlet' => array(
			'className' => 'DistOutlet',
			'foreignKey' => 'dist_outlet_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		),
		'Market' => array(
			'className' => 'DistMarket',
			'foreignKey' => 'dist_market_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		),
        'Distributor' => array(
			'className' => 'DistDistributor',
			'foreignKey' => 'distributor_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		)*/
	);
	
	
/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'DistOrderDeliveryScheduleOrderDetail' => array(
			'className' => 'DistOrderDeliveryScheduleOrderDetail',
			'foreignKey' => 'dist_order_no',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
	
	
	/*----- quaery Methods -----*/


}
