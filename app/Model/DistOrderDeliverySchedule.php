<?php

App::uses('AppModel', 'Model');

/**
 * Outlet Model
 *
 * @property Market $Market
 * @property Category $Category
 */
class DistOrderDeliverySchedule extends AppModel {

    /**
     * Display field
     *
     * @var string
     */
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    /* ========== validate=============== */
    public $validate = array();

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
			'order' => '',
		),
		'DistSalesRepresentative' => array(
			'className' => 'DistSalesRepresentative',
			'foreignKey' => 'sr_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
		),
		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'office_id',
			'conditions' => '',
			'fields' => 'office_name',
			'order' => ''
		),
		'DistDistributor' => array(
			'className' => 'DistDistributor',
			'foreignKey' => 'distributor_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		),
	);
    public $hasOne = array();
	
	
	public $hasMany = array(
		'DistOrderDeliveryScheduleDetail' => array(
			'className' => 'DistOrderDeliveryScheduleDetail',
			'foreignKey' => 'dist_order_delivery_schedule_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	);

    // data filter
    public function filter($params, $conditions) {   
	
		$conditions = array();
       		
        if(!empty($params['DistOrderDeliverySchedule.office_id']))
        {
                $conditions[] = array('DistOrderDeliverySchedule.office_id' => $params['DistOrderDeliverySchedule.office_id']);
                
                if (!empty($params['DistOrderDeliverySchedule.order_reference_no'])) {
            $conditions[] = array('DistOrderDeliverySchedule.order_reference_no' => $params['DistOrderDeliverySchedule.order_reference_no']);
                 }
        }
		
		
        
        if (!empty($params['DistOrderDeliverySchedule.distibutor_id'])) {
            $conditions[] = array('DistOrderDeliverySchedule.distibutor_id' => $params['DistOrderDeliverySchedule.distibutor_id']);
        }
        elseif (!empty($params['DistOrderDeliverySchedule.sr_id'])) {
            $conditions[] = array('DistOrderDeliverySchedule.sr_id' => $params['DistOrderDeliverySchedule.sr_id']);
        }
	
		if (isset($params['DistOrderDeliverySchedule.date_from'])!='') {
            $conditions[] = array('DistOrderDeliverySchedule.process_date_time >=' => Date('Y-m-d H:i:s',strtotime($params['DistOrderDeliverySchedule.date_from'])));
        }
		if (isset($params['DistOrderDeliverySchedule.date_to'])!='') {
            $conditions[] = array('DistOrderDeliverySchedule.process_date_time <=' => Date('Y-m-d H:i:s',strtotime($params['DistOrderDeliverySchedule.date_to'].' 23:59:59')));
        }   
        
        return $conditions;
    }

}
