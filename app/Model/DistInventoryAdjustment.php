<?php
App::uses('AppModel', 'Model');


class DistInventoryAdjustment extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	
	public $validate = array(
		'status' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Receiver is required.'
					)
		),
		'remarks' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Sales person field is required.'
					)	
		)
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'DistTransactionType' => array(
			'className' => 'DistTransactionType',
			'foreignKey' => 'transaction_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Store' => array(
			'className' => 'DistStore',
			'foreignKey' => 'dist_store_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
              'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'office_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
            
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'DistInventoryAdjustmentDetail' => array(
			'className' => 'DistInventoryAdjustmentDetail',
			'foreignKey' => 'dist_inventory_adjustment_id',
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
	
	// data filter
	public function filter($params, $conditions) {
        $conditions = array();
		if (!empty($params['DistInventoryAdjustment.status'])) {
            $conditions[] = array('DistInventoryAdjustment.status' => $params['DistInventoryAdjustment.status']);
        }
		if (!empty($params['DistInventoryAdjustment.created_at'])) {
            $conditions[] = array('convert(varchar,DistInventoryAdjustment.created_at, 105)'=> $params['DistInventoryAdjustment.created_at']);
        }
        return $conditions;
    }

}
